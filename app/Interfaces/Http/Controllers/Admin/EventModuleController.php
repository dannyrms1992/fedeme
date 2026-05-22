<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domain\Event\Models\Event;
use App\Domain\Event\Models\EventModule;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

final class EventModuleController extends Controller
{
    public function edit(Event $event): View
    {
        // Garantizar que todos los módulos por defecto existan (inactivos si son nuevos)
        $allTypes      = ['hero', 'info', 'contact', 'pdf', 'map', 'emergency', 'video_intro'];
        $existingTypes = $event->modules()->pluck('type')->toArray();
        $lastOrder     = $event->modules()->max('order') ?? 0;

        foreach ($allTypes as $type) {
            if (!in_array($type, $existingTypes)) {
                $event->modules()->create([
                    'type'      => $type,
                    'is_active' => false,
                    'order'     => ++$lastOrder,
                    'settings'  => [],
                ]);
            }
        }

        $modules = $event->modules()->orderBy('order')->get();

        return view('admin.events.modules', compact('event', 'modules'));
    }

    public function reorder(Request $request, Event $event, EventModule $module): RedirectResponse
    {
        abort_if($module->event_id !== $event->id, 403);

        $direction = $request->input('direction'); // 'up' | 'down'
        $modules   = $event->modules()->orderBy('order')->get();
        $index     = $modules->search(fn ($m) => $m->id === $module->id);

        if ($direction === 'up' && $index > 0) {
            $swap = $modules[$index - 1];
        } elseif ($direction === 'down' && $index < $modules->count() - 1) {
            $swap = $modules[$index + 1];
        } else {
            return back();
        }

        // Intercambiar valores de order
        $currentOrder = $module->order;
        $module->update(['order' => $swap->order]);
        $swap->update(['order' => $currentOrder]);

        return back()->with('success', 'Orden actualizado.');
    }

    public function update(Request $request, Event $event, EventModule $module): RedirectResponse
    {
        // Make sure the module belongs to the event
        abort_if($module->event_id !== $event->id, 403);

        $request->validate([
            'settings'  => ['nullable'],
            'is_active' => ['nullable'],
            'logo'      => ['nullable', 'image', 'max:2048'],
            'video'     => ['nullable', 'file', 'mimetypes:video/mp4,video/webm', 'max:102400'],
        ]);

        // Parse settings — may arrive as array (field-per-field) or as JSON string
        $settings = null;
        if ($request->has('settings')) {
            $raw = $request->input('settings');
            if (is_array($raw)) {
                // Decode any JSON string sub-fields (e.g. contacts from Alpine serialization)
                foreach (['contacts'] as $jsonField) {
                    if (isset($raw[$jsonField]) && is_string($raw[$jsonField]) && $raw[$jsonField] !== '') {
                        $decoded = json_decode($raw[$jsonField], true);
                        $raw[$jsonField] = is_array($decoded) ? $decoded : [];
                    }
                }
                $settings = $raw;
            } else {
                $decoded = json_decode($raw, true);
                $settings = is_array($decoded) ? $decoded : $module->settings;
            }
        }

        $settings = $settings ?? $module->settings ?? [];

        // Handle logo upload for emergency module
        if ($module->type === 'emergency' && $request->hasFile('logo')) {
            // Delete old logo if exists
            if (!empty($module->settings['logo_path'])) {
                Storage::disk('public')->delete($module->settings['logo_path']);
            }
            $path = $request->file('logo')->store('modules/emergency', 'public');
            $settings['logo_path'] = $path;
        } elseif ($module->type === 'emergency') {
            // Preserve existing logo_path if no new file uploaded
            $settings['logo_path'] = $module->settings['logo_path'] ?? '';
        }

        // Handle video upload for video_intro module
        if ($module->type === 'video_intro' && $request->hasFile('video')) {
            if (!empty($module->settings['video_path'])) {
                Storage::disk('public')->delete($module->settings['video_path']);
            }
            $path = $request->file('video')->store('events/' . $event->id . '/video', 'public');
            $settings['video_path'] = $path;
        } elseif ($module->type === 'video_intro') {
            $settings['video_path'] = $module->settings['video_path'] ?? '';
        }

        $module->update([
            'settings'  => $settings,
            'is_active' => $request->boolean('is_active', $module->is_active),
        ]);

        return back()->with('success', 'Módulo "' . $module->type . '" actualizado.');
    }

    public function store(Request $request, Event $event): RedirectResponse
    {
        $allowed = ['hero', 'info', 'contact', 'pdf', 'map', 'emergency', 'video_intro'];

        $request->validate([
            'type' => ['required', 'string', 'in:' . implode(',', $allowed)],
        ]);

        $type = $request->input('type');

        // Prevent duplicates
        if ($event->modules()->where('type', $type)->exists()) {
            return back()->with('error', 'El módulo "' . $type . '" ya existe en este evento.');
        }

        $lastOrder = $event->modules()->max('order') ?? 0;

        $event->modules()->create([
            'type'      => $type,
            'is_active' => false,
            'order'     => $lastOrder + 1,
            'settings'  => [],
        ]);

        return back()->with('success', 'Módulo añadido correctamente. Configúralo y actívalo cuando esté listo.');
    }
}
