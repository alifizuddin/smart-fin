<?php

namespace App\Livewire\Staff\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use Carbon\Carbon;

class SpendingChart extends Component
{
    public $chartData = [];

    public function mount()
    {
        $user = Auth::user();
        
        // Get last 6 months
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i));
        }

        $labels = $months->map(function ($date) {
            return $date->format('M Y');
        });

        $data = $months->map(function ($date) use ($user) {
            return Expense::where('user_id', $user->id)
                ->where('status', 'approved')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount');
        });

        $this->chartData = [
            'labels' => $labels->toArray(),
            'datasets' => [
                [
                    'label' => 'Monthly Spending (RM)',
                    'data' => $data->toArray(),
                    'backgroundColor' => 'rgba(99, 102, 241, 0.5)', // Indigo-500
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                    'borderWidth' => 1,
                    'borderRadius' => 4,
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.staff.components.spending-chart');
    }
}
