<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Middleware;

use App\Domain\Event\Models\Event;
use App\Domain\Event\ValueObjects\EventStatus;
use App\Domain\Tenant\Exceptions\TenantNotFoundException;
use App\Infrastructure\Tenant\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\URL;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the tenant (event) from the request subdomain.
 *
 * Flow:
 *   1. Extract subdomain from Host header.
 *   2. Validate it is not the root domain or www.
 *   3. Look up the active event (cached).
 *   4. Inject into TenantContext singleton.
 *   5. If not found → throw 404 (TenantNotFoundException).
 */
final class TenantResolver
{
    private const ROOT_SUBDOMAINS = ['www', ''];
    private const CACHE_TTL_SECONDS = 300; // 5 minutes

    public function __construct(private readonly TenantContext $context)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $this->extractSubdomain($request);

        if ($subdomain === null || in_array($subdomain, self::ROOT_SUBDOMAINS, true)) {
            // This middleware is only for subdomains; continue without resolving tenant.
            return $next($request);
        }

        $event = $this->resolveEvent($subdomain);

        if ($event === null) {
            throw TenantNotFoundException::forSubdomain($subdomain);
        }

        $this->context->set($event);

        // Set URL default so that route('event.*') calls don't need to
        // explicitly pass {subdomain} when using Route::domain('{subdomain}.*')
        URL::defaults(['subdomain' => $subdomain]);

        return $next($request);
    }

    private function extractSubdomain(Request $request): ?string
    {
        $host       = $request->getHost();
        $appDomain  = config('app.domain', 'fedeme.app');
        $appDomain  = ltrim($appDomain, '.');

        // Host must end with the app domain
        if (!str_ends_with($host, $appDomain)) {
            return null;
        }

        $subdomain = str_replace('.' . $appDomain, '', $host);

        // Security: allow only alphanumeric + hyphens, max 63 chars
        if (!preg_match('/^[a-z0-9\-]{1,63}$/', $subdomain)) {
            return null;
        }

        return $subdomain;
    }

    private function resolveEvent(string $subdomain): ?Event
    {
        $cacheKey = "tenant_event:{$subdomain}";

        return Cache::remember($cacheKey, self::CACHE_TTL_SECONDS, function () use ($subdomain) {
            return Event::where('subdomain', $subdomain)
                ->where('status', EventStatus::Active)
                ->first();
        });
    }
}
