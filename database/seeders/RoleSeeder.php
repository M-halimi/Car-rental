<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            'super_admin' => 'Super Admin',
            'agency' => 'Agency Admin',
            'customer' => 'Customer',
        ];

        foreach ($roles as $name => $label) {
            Role::firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        $permissions = [
            'manage-agencies',
            'manage-subscriptions',
            'manage-platform-config',
            'manage-global-bookings',
            'manage-moderation',
            'view-platform-analytics',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        $superAdmin = Role::findByName('super_admin', 'web');
        $superAdmin->givePermissionTo($permissions);
    }
}
