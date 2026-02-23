<?php

declare(strict_types=1);

namespace App\Providers;

use App\Application\Access\Commands\ValidateAccessCodeCommand;
use App\Application\Event\Commands\CreateEventCommand;
use App\Application\Event\Commands\UpdateAccessCodeCommand;
use App\Infrastructure\Logging\AccessAttemptLogger;
use App\Infrastructure\Tenant\TenantContext;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

/**
 * Binds Clean Architecture layers into the IoC container.
 * All cross-cutting infrastructure singletons are registered here.
 */
class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // TenantContext lives as a singleton for the request lifecycle
        $this->app->singleton(TenantContext::class);

        // Infrastructure
        $this->app->singleton(AccessAttemptLogger::class);

        // Application commands (stateless — new instance per request is fine)
        $this->app->bind(ValidateAccessCodeCommand::class);
        $this->app->bind(CreateEventCommand::class);
        $this->app->bind(UpdateAccessCodeCommand::class);
    }

    public function boot(): void
    {
        // super-admin bypasses all permission checks
        Gate::before(function ($user, string $ability) {
            if ($user->hasRole('super-admin')) {
                return true;
            }
        });
    }
}
