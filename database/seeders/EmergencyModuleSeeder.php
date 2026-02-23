<?php

namespace Database\Seeders;

use App\Domain\Event\Models\Event;
use Illuminate\Database\Seeder;

class EmergencyModuleSeeder extends Seeder
{
    public function run(): void
    {
        foreach (Event::all() as $event) {
            if ($event->modules()->where('type', 'emergency')->exists()) {
                continue;
            }
            $maxOrder = $event->modules()->max('order') ?? 0;
            $event->modules()->create([
                'type'      => 'emergency',
                'is_active' => false,
                'order'     => $maxOrder + 1,
                'settings'  => [
                    'title'       => 'Numeros de Emergencia',
                    'description' => '',
                    'entries'     => [],
                ],
            ]);
            $this->command->info("Seeded emergency module for: {$event->name}");
        }
    }
}
