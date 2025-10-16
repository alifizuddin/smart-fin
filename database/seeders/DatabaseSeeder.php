<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     * $this->call(RolesAndPermissionsSeeder::class);
     */
    public function run(): void
    {
        // User::factory(10)->create();
        $this->call([
            DepartmentSeeder::class,
            RolesAndPermissionsSeeder::class,
            UserSeeder::class,// <-- Add this line
        ]);
    }
}
