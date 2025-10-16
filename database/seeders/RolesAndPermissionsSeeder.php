<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        Permission::create(['name' => 'manage users']);
        Permission::create(['name' => 'manage departments']);
        Permission::create(['name' => 'manage budgets']);
        Permission::create(['name' => 'manage system settings']);
        Permission::create(['name' => 'view reports']);
        Permission::create(['name' => 'approve expenses']);
        Permission::create(['name' => 'submit expenses']);
        Permission::create(['name' => 'view own expenses']);

        // create roles and assign created permissions

        // Admin
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo(Permission::all());

        // Manager
        $managerRole = Role::create(['name' => 'Manager']);
        $managerRole->givePermissionTo([
            'manage budgets',
            'view reports',
            'approve expenses',
        ]);

        // Staff
        $staffRole = Role::create(['name' => 'Staff']);
        $staffRole->givePermissionTo([
            'submit expenses',
            'view own expenses',
        ]);
    }
}