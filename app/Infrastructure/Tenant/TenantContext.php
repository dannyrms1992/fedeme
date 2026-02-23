<?php

declare(strict_types=1);

namespace App\Infrastructure\Tenant;

use App\Domain\Event\Models\Event;

/**
 * Singleton bound in the container that holds the resolved tenant (event)
 * for the entire lifecycle of a single request.
 */
final class TenantContext
{
    private ?Event $event = null;

    public function set(Event $event): void
    {
        $this->event = $event;
    }

    public function get(): ?Event
    {
        return $this->event;
    }

    public function getId(): ?int
    {
        return $this->event?->id;
    }

    public function isResolved(): bool
    {
        return $this->event !== null;
    }

    public function resolved(): Event
    {
        if ($this->event === null) {
            throw new \RuntimeException(
                'TenantContext has not been resolved yet. Ensure TenantResolver middleware ran.'
            );
        }

        return $this->event;
    }
}
