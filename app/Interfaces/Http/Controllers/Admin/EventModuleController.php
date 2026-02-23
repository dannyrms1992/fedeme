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

        $module->update([
            'settings'  => $settings,
            'is_active' => $request->boolean('is_active', $module->is_active),
        ]);

        return back()->with('success', 'Módulo "' . $module->type . '" actualizado.');
    }
}
