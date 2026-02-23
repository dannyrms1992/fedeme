<?php

declare(strict_types=1);

namespace App\Application\Event\Commands;

use App\Domain\Event\Models\Event;
use App\Domain\Event\ValueObjects\EventStatus;
use Illuminate\Support\Facades\Cache;

/**
 * Creates a new event and seeds its default modules.
 */
final class CreateEventCommand
{
    public function execute(array $data): Event
    {
        $event = Event::create([
            'name'            => $data['name'],
            'slug'            => $data['slug'],
            'subdomain'       => $data['subdomain'],
            'description'     => $data['description'] ?? null,
            'logo_path'       => $data['logo_path'] ?? null,
            'primary_color'   => $data['primary_color'] ?? '#1a4f8a',
            'secondary_color' => $data['secondary_color'] ?? '#c0392b',
            'accent_color'    => $data['accent_color'] ?? '#F59E0B',
            'bg_color'        => $data['bg_color'] ?? '#F8FAFC',
            'surface_color'   => $data['surface_color'] ?? '#FFFFFF',
            'status'          => EventStatus::Draft,
            'access_enabled'  => false,
            'created_by'      => $data['created_by'],
        ]);

        // Seed default landing modules
        $defaultModules = ['hero', 'info', 'contact', 'emergency'];
        foreach ($defaultModules as $index => $type) {
            $event->modules()->create([
                'type'      => $type,
                'is_active' => true,
                'order'     => $index + 1,
            ]);
        }

        return $event;
    }
}
