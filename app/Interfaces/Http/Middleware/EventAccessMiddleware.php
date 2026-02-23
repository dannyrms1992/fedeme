<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Middleware;

use App\Infrastructure\Tenant\TenantContext;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Guards event routes behind an optional access code.
 *
 * If the event does not require an access code → passes through.
 * If the event requires a code and session is valid → passes through.
 * Otherwise → redirects to the access code form.
 */
final class EventAccessMiddleware
{
    public function __construct(private readonly TenantContext $context)
    {
    }

    public function handle(Request $request, Closure $next): Response
    {
        // Only acts if tenant is resolved
        if (!$this->context->isResolved()) {
            return $next($request);
        }

        $event = $this->context->resolved();

        // No access code required → open access
        if (!$event->requiresAccessCode()) {
            return $next($request);
        }

        // Valid session token → open access
        $sessionKey = "event_access_{$event->id}";
        if ($request->session()->get($sessionKey) === true) {
            return $next($request);
        }

        // Redirect to access code form (skip if already on that route to avoid loops)
        if ($request->routeIs('event.access.form')) {
            return $next($request);
        }

        return redirect()->route('event.access.form');
    }
}
