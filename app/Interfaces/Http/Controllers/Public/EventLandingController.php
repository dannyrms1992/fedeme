<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Controllers\Public;

use App\Infrastructure\Tenant\TenantContext;
use Illuminate\View\View;

/**
 * Renders the public landing page of the event.
 * No authentication required (access code is enforced by middleware).
 */
final class EventLandingController
{
    public function __construct(private readonly TenantContext $context)
    {
    }

    public function show(): View
    {
        if (! $this->context->isResolved()) {
            abort(404);
        }

        $event   = $this->context->resolved();
        $modules = $event->modules()->where('is_active', true)->get();

        return view('public.landing', compact('event', 'modules'));
    }
}
