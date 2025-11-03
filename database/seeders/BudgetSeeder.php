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
        $departments = Department::all();
        $users = User::all();

        if ($departments->isEmpty() || $users->isEmpty()) {
            $this->command->info('Please seed departments and users first.');
            return;
        }

        // Possible random words for naming
        $projectNames = ['Apollo', 'Zenith', 'Nova', 'Aurora', 'Pioneer', 'Vertex', 'Odyssey', 'Nimbus', 'Horizon', 'Atlas'];
        $budgetTypes = ['Budget', 'Project', 'Initiative', 'Campaign', 'Fund'];
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        for ($i = 0; $i < 10; $i++) {
            $department = $departments->random();
            $creator = $users->random();

            // Randomly choose between a "Project" or "Budget" style name
            $type = $budgetTypes[array_rand($budgetTypes)];
            $word = $projectNames[array_rand($projectNames)];
            $year = rand(2023, 2026);
            $month = $months[array_rand($months)];

            // Generate name like "Aurora Project 2025" or "Zenith Budget September 2024"
            $name = rand(0, 1)
                ? "{$word} {$type} {$year}"
                : "{$word} {$type} {$month} {$year}";

            Budget::create([
                'name' => $name,
                'department_id' => $department->id,
                'total_amount' => rand(10000, 100000),
                'period_start' => Carbon::now()->startOfMonth(),
                'period_end' => Carbon::now()->endOfMonth(),
                'status' => 'active',
                'created_by' => $creator->id,
            ]);
        }
    }
}
