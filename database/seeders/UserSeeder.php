<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Admin User
        $admin = User::firstOrCreate(
            [ 'name' => 'Admin User' ],
            [
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole('Admin');

        // Create Manager User
        $manager = User::firstOrCreate(
            [ 'name' => 'Manager User' ],
            [
                'email' => 'manager@example.com',
                'password' => Hash::make('password'),
            ]
        );
        $manager->assignRole('Manager');

        // Create Staff User
        $staff = User::firstOrCreate(
            [ 'name' => 'Staff User' ],
            [
                'email' => 'staff@example.com',
                'password' => Hash::make('password'),
            ]
        );
        $staff->assignRole('Staff');

        // Create Finance Admin User
        $financeAdmin = User::firstOrCreate(
            [ 'name' => 'Finance Admin User' ],
            [
                'email' => 'financeadmin@example.com',
                'password' => Hash::make('password'),
            ]
        );
        $financeAdmin->assignRole('Finance Admin');
    }
}