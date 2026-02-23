<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Exceptions;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class TenantNotFoundException extends NotFoundHttpException
{
    public static function forSubdomain(string $subdomain): self
    {
        return new self("No active event found for subdomain: [{$subdomain}].");
    }
}
