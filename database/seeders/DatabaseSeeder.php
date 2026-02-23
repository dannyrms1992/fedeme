<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // Roles and permissions first
        $this->call(RolesAndPermissionsSeeder::class);

        // SuperAdmin user
        $superAdmin = User::factory()->create([
            'name'     => 'Super Admin FEDEME',
            'email'    => 'admin@fedeme.ec',
            'password' => Hash::make('fedeme2026!'),
        ]);

        $superAdmin->assignRole('super-admin');
    }
}

