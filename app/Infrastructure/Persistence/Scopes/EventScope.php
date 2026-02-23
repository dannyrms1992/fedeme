<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Global scope that automatically filters queries by the current tenant (event).
 * Applied to all models that depend on an event via event_id.
 */
final class EventScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $tenantId = app(\App\Infrastructure\Tenant\TenantContext::class)->getId();

        if ($tenantId !== null) {
            $builder->where($model->getTable() . '.event_id', $tenantId);
        }
    }
}
