<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            'dashboard.show',
            'users.show',
            'users.create',
            'users.edit',
            'users.active',
            'roles.show',
            'roles.create',
            'roles.edit',
            'roles.active',
            'permissions.show',
            'permissions.create',
            'permissions.edit',
            'permissions.active',
            'categories.show',
            'categories.create',
            'categories.edit',
            'categories.active',
            'products.show',
            'products.create',
            'products.edit',
            'products.active',
            'products.delete',
            'orders.show',
            'orders.cancel',
            'reports.show',
            'kardex.show',
            'exchange-rates.show',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        Role::firstOrCreate(['name' => 'SuperAdministrador'])
            ->syncPermissions(Permission::all());

        Role::firstOrCreate(['name' => 'Administrativo'])
            ->syncPermissions([
                'dashboard.show',
                'products.show',
                'orders.show',
                'reports.show',
            ]);
    }
}
