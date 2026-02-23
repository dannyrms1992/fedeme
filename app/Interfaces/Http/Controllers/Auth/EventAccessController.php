<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Controllers\Auth;

use App\Application\Access\Commands\ValidateAccessCodeCommand;
use App\Infrastructure\Tenant\TenantContext;
use App\Interfaces\Http\Requests\AccessCodeRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

/**
 * Handles the lightweight event access code gate.
 * Intentionally uses neutral messages to avoid revealing code status.
 */
final class EventAccessController
{
    public function __construct(
        private readonly TenantContext             $context,
        private readonly ValidateAccessCodeCommand $command,
    ) {
    }

    public function show(): View
    {
        if (! $this->context->isResolved()) {
            abort(404);
        }

        $event = $this->context->resolved();

        return view('public.access', compact('event'));
    }

    public function store(AccessCodeRequest $request): RedirectResponse
    {
        $valid = $this->command->execute($request, $request->validated('code'));

        if (!$valid) {
            return back()->withErrors(['code' => __('El código ingresado no es válido o ha expirado.')]);
        }

        return redirect()->route('event.landing');
    }
}
