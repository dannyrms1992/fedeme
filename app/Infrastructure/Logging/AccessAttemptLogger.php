<?php

declare(strict_types=1);

namespace App\Infrastructure\Logging;

use Illuminate\Support\Facades\Log;

/**
 * Records structured logs for access code attempts.
 * Stored in a dedicated channel to facilitate monitoring.
 */
final class AccessAttemptLogger
{
    public function logFailedAttempt(int $eventId, string $ip): void
    {
        Log::channel('access_attempts')->warning('Access code attempt failed', [
            'event_id' => $eventId,
            'ip'       => $ip,
            'ts'       => now()->toIso8601String(),
        ]);
    }

    public function logSuccessfulAttempt(int $eventId, string $ip): void
    {
        Log::channel('access_attempts')->info('Access code attempt succeeded', [
            'event_id' => $eventId,
            'ip'       => $ip,
            'ts'       => now()->toIso8601String(),
        ]);
    }
}
