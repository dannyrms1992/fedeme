<?php

declare(strict_types=1);

namespace App\Application\Access\Commands;

use App\Infrastructure\Logging\AccessAttemptLogger;
use App\Infrastructure\Tenant\TenantContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

/**
 * Validates the submitted access code against the current tenant event.
 * Handles rate limiting and structured logging of failed attempts.
 */
final class ValidateAccessCodeCommand
{
    private const MAX_ATTEMPTS = 5;
    private const DECAY_SECONDS = 60;

    public function __construct(
        private readonly TenantContext    $context,
        private readonly AccessAttemptLogger $logger,
    ) {
    }

    /**
     * @return bool True → code is correct, session was set.
     * @throws \Illuminate\Http\Exceptions\ThrottleRequestsException
     */
    public function execute(Request $request, string $plainCode): bool
    {
        $event     = $this->context->resolved();
        $throttleKey = 'event_access:' . $request->ip() . ':' . $event->id;

        if (RateLimiter::tooManyAttempts($throttleKey, self::MAX_ATTEMPTS)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            abort(429, "Demasiados intentos. Intente nuevamente en {$seconds} segundos.");
        }

        $valid = $event->verifyAccessCode($plainCode);

        if (!$valid) {
            RateLimiter::hit($throttleKey, self::DECAY_SECONDS);
            $this->logger->logFailedAttempt($event->id, $request->ip());
            return false;
        }

        RateLimiter::clear($throttleKey);
        $request->session()->put("event_access_{$event->id}", true);

        return true;
    }
}
