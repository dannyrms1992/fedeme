<?php

declare(strict_types=1);

namespace App\Domain\Tenant\Contracts;

interface TenantInterface
{
    public function getTenantId(): int;
    public function getTenantSubdomain(): string;
}
