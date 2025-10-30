<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Budget;
use App\Models\Department;
use App\Models\User;
use Carbon\Carbon;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get some departments and users to associate with the budgets
        $departments = Department::all();
        $users = User::all();

        // Check if there are departments and users
        if ($departments->isEmpty() || $users->isEmpty()) {
            $this->command->info('Please seed departments and users first.');
            return;
        }

        // Create a few budgets
        for ($i = 0; $i < 10; $i++) {
            Budget::create([
                'department_id' => $departments->random()->id,
                'total_amount' => rand(10000, 100000),
                'used_amount' => rand(1000, 10000),
                'period_start' => Carbon::now()->startOfMonth(),
                'period_end' => Carbon::now()->endOfMonth(),
                'status' => 'active',
                'created_by' => $users->random()->id,
            ]);
        }
    }
}