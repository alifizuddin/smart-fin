<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB; // <-- Make sure to import this

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('departments')->insert([
            ['name' => 'Finance', 'description' => 'Handles all financial matters.'],
            ['name' => 'Human Resources', 'description' => 'Manages employee relations and recruitment.'],
            ['name' => 'Information Technology', 'description' => 'Manages IT infrastructure and support.'],
            ['name' => 'Marketing', 'description' => 'Responsible for marketing and advertising.'],
            ['name' => 'Sales', 'description' => 'Drives business and sales operations.'],
            ['name' => 'Operations', 'description' => 'Oversees the production of goods and services.'],
        ]);
    }
}