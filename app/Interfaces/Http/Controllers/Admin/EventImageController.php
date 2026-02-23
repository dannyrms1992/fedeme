<?php

declare(strict_types=1);

namespace App\Interfaces\Http\Controllers\Admin;

use App\Domain\Event\Models\Event;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

final class EventImageController extends Controller
{
    /**
     * Sube o reemplaza el logo del evento.
     */
    public function uploadLogo(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
        ], [
            'logo.required' => 'Debes seleccionar una imagen.',
            'logo.image'    => 'El archivo debe ser una imagen.',
            'logo.mimes'    => 'El logo debe ser JPG, PNG, WebP o SVG.',
            'logo.max'      => 'El logo no puede superar los 2 MB.',
        ]);

        // Eliminar logo anterior
        if ($event->logo_path && Storage::disk('public')->exists($event->logo_path)) {
            Storage::disk('public')->delete($event->logo_path);
        }

        $path = $request->file('logo')->store("events/{$event->id}/logo", 'public');

        $event->update(['logo_path' => $path]);

        return redirect()->route('admin.events.edit', $event)
            ->with('success', 'Logo actualizado correctamente.');
    }

    /**
     * Agrega imágenes al carrusel del evento.
     */
    public function uploadCarousel(Request $request, Event $event): RedirectResponse
    {
        $request->validate([
            'carousel'   => ['required', 'array', 'min:1', 'max:10'],
            'carousel.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'carousel.required'   => 'Debes seleccionar al menos una imagen.',
            'carousel.max'        => 'No puedes subir más de 10 imágenes a la vez.',
            'carousel.*.image'    => 'Cada archivo debe ser una imagen.',
            'carousel.*.mimes'    => 'Las imágenes deben ser JPG, PNG o WebP.',
            'carousel.*.max'      => 'Cada imagen no puede superar los 4 MB.',
        ]);

        $existing = $event->carousel_images ?? [];

        foreach ($request->file('carousel') as $file) {
            $path = $file->store("events/{$event->id}/carousel", 'public');
            $existing[] = $path;
        }

        $event->update(['carousel_images' => $existing]);

        return redirect()->route('admin.events.edit', $event)
            ->with('success', 'Imágenes del carrusel actualizadas.');
    }

    /**
     * Elimina una imagen individual del carrusel por índice.
     */
    public function deleteCarouselImage(Request $request, Event $event, int $index): RedirectResponse
    {
        $images = $event->carousel_images ?? [];

        if (! isset($images[$index])) {
            return redirect()->route('admin.events.edit', $event)
                ->with('error', 'Imagen no encontrada.');
        }

        $path = $images[$index];

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        array_splice($images, $index, 1);

        $event->update(['carousel_images' => array_values($images)]);

        return redirect()->route('admin.events.edit', $event)
            ->with('success', 'Imagen eliminada del carrusel.');
    }
}
