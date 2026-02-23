<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeds the four canonical FEDEME roles and their base permissions.
 *
 * Roles:
 *  - super-admin   → all permissions (bypassed via Spatie gate)
 *  - admin-fedeme  → manage all events
 *  - organizador   → manage own events only
 *  - publico       → no admin permissions (landing access only)
 */
final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view events',
            'create events',
            'edit events',
            'delete events',
            'manage access codes',
            'manage modules',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Roles
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin',   'guard_name' => 'web']);
        $adminFedeme = Role::firstOrCreate(['name' => 'admin-fedeme', 'guard_name' => 'web']);
        $organizador = Role::firstOrCreate(['name' => 'organizador',  'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'publico', 'guard_name' => 'web']);

        // super-admin gets everything via gate override (Spatie default)
        // admin-fedeme gets all permissions
        $adminFedeme->syncPermissions($permissions);

        // organizador gets limited permissions
        $organizador->syncPermissions([
            'view events',
            'edit events',
            'manage access codes',
            'manage modules',
        ]);
    }
}
