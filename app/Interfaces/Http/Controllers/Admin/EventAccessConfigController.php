<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Controllers\Admin;

use App\Application\Event\Commands\UpdateAccessCodeCommand;
use App\Domain\Event\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Manages the access code configuration for a specific event.
 */
final class EventAccessConfigController
{
    public function __construct(private readonly UpdateAccessCodeCommand $updateAccessCode)
    {
    }

    public function edit(Event $event): View
    {
        return view('admin.events.access', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $data = $request->validate([
            'access_enabled' => ['required', 'boolean'],
            'access_code'    => ['nullable', 'string', 'min:4', 'max:128'],
            'access_expires_at' => ['nullable', 'date', 'after:now'],
        ]);

        $this->updateAccessCode->execute(
            event: $event,
            plainCode: $data['access_code'] ?? null,
            expiresAt: $data['access_expires_at'] ?? null,
            enabled: (bool) $data['access_enabled'],
        );

        return back()->with('success', 'Configuración de acceso guardada.');
    }
}
