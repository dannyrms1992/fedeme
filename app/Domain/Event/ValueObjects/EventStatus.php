<?php

declare(strict_types=1);

namespace App\Domain\Event\ValueObjects;

enum EventStatus: string
{
    case Draft   = 'draft';
    case Active  = 'active';
    case Inactive = 'inactive';

    public function label(): string
    {
        return match($this) {
            self::Draft    => 'Borrador',
            self::Active   => 'Activo',
            self::Inactive => 'Inactivo',
        };
    }

    public function isPubliclyAccessible(): bool
    {
        return $this === self::Active;
    }
}
