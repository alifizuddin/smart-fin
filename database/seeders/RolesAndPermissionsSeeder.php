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
        Permission::firstOrCreate(['name' => 'manage users']);
        Permission::firstOrCreate(['name' => 'manage departments']);
        Permission::firstOrCreate(['name' => 'manage budgets']);
        Permission::firstOrCreate(['name' => 'manage system settings']);
        Permission::firstOrCreate(['name' => 'view reports']);
        Permission::firstOrCreate(['name' => 'approve expenses']);
        Permission::firstOrCreate(['name' => 'submit expenses']);
        Permission::firstOrCreate(['name' => 'view own expenses']);
        
        //finance admin additional permissions
        Permission::firstOrCreate(['name' => 'manage annual budget']);
        Permission::firstOrCreate(['name' => 'approve final budgets']);
        Permission::firstOrCreate(['name' => 'adjust department funds']);
        Permission::firstOrCreate(['name' => 'view budget alerts']);

        // create roles and assign created permissions

        // Admin
        $adminRole = Role::firstOrCreate(['name' => 'Admin']);
        $adminRole->syncPermissions(Permission::all());

        // Manager
        $managerRole = Role::firstOrCreate(['name' => 'Manager']);
        $managerRole->syncPermissions([
            'manage budgets',
            'view reports',
            'approve expenses',
        ]);

        // Staff
        $staffRole = Role::firstOrCreate(['name' => 'Staff']);
        $staffRole->syncPermissions([
            'submit expenses',
            'view own expenses',
        ]);

        // Finance Admin
        $financeAdmin = Role::firstOrCreate(['name' => 'Finance Admin']);
        $financeAdmin->syncPermissions([
            'manage annual budget',
            'approve final budgets',
            'adjust department funds',
            'view reports',
            'view budget alerts',
            'approve expenses',
            'manage budgets',
        ]);
    }
}