<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Controllers\Admin;

use App\Application\Event\Commands\CreateEventCommand;
use App\Domain\Event\Models\Event;
use App\Domain\Event\ValueObjects\EventStatus;
use App\Interfaces\Http\Requests\StoreEventRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rules\Enum;
use Illuminate\View\View;

/**
 * Admin CRUD for events.
 * Controller is intentionally thin — delegates to Application layer.
 */
final class EventController
{
    public function __construct(private readonly CreateEventCommand $createEvent)
    {
    }

    public function index(): View
    {
        $events = Event::latest()->paginate(20);

        return view('admin.events.index', compact('events'));
    }

    public function create(): View
    {
        return view('admin.events.create');
    }

    public function store(StoreEventRequest $request): RedirectResponse
    {
        $event = $this->createEvent->execute([
            ...$request->validated(),
            'created_by' => $request->user()->id,
        ]);

        return redirect()
            ->route('admin.events.show', $event)
            ->with('success', 'Evento creado correctamente.');
    }

    public function show(Event $event): View
    {
        $event->load('modules');

        return view('admin.events.show', compact('event'));
    }

    public function edit(Event $event): View
    {
        return view('admin.events.edit', compact('event'));
    }

    public function update(Request $request, Event $event): RedirectResponse
    {
        $event->update($request->validate([
            'name'           => ['required', 'string', 'max:255'],
            'description'    => ['nullable', 'string', 'max:2000'],
            'primary_color'  => ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'secondary_color'=> ['required', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'accent_color'   => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'bg_color'       => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'surface_color'  => ['nullable', 'string', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'status'         => ['required', new Enum(EventStatus::class)],
        ]));

        Cache::forget("tenant_event:{$event->subdomain}");

        return back()->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Event $event): RedirectResponse
    {
        $event->delete();

        Cache::forget("tenant_event:{$event->subdomain}");

        return redirect()
            ->route('admin.events.index')
            ->with('success', 'Evento eliminado.');
    }
}
