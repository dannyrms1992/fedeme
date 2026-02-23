<?php

declare(strict_types=1);

namespace App\Application\Event\Commands;

use App\Domain\Event\Models\Event;
use Illuminate\Support\Facades\Cache;

/**
 * Updates the access code for a given event.
 * Handles hashing, expiration and cache invalidation.
 */
final class UpdateAccessCodeCommand
{
    public function execute(Event $event, ?string $plainCode, ?string $expiresAt, bool $enabled): void
    {
        $event->access_enabled = $enabled;

        if ($plainCode !== null && $plainCode !== '') {
            $event->setAccessCode($plainCode);
        }

        $event->access_expires_at = $expiresAt ? \Carbon\Carbon::parse($expiresAt) : null;
        $event->save();

        // Invalidate the tenant cache so the resolver picks up changes immediately
        Cache::forget("tenant_event:{$event->subdomain}");
    }
}
