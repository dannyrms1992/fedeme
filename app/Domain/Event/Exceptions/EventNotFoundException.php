<?php

declare(strict_types=1);

namespace App\Domain\Event\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class EventNotFoundException extends NotFoundHttpException
{
    public static function forId(int $id): self
    {
        return new self("Event with ID [{$id}] not found.");
    }
}
