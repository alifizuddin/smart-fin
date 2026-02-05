<?php

namespace App\Livewire\Staff\Components;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;

class CategoryChart extends Component
{
    public $chartData = [];

    public function mount()
    {
        $this->updateChartData();
    }

    public function updateChartData()
    {
        $user = Auth::user();

        // Group by category, sum amount
        $expenses = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->select('category', DB::raw('sum(amount) as total'))
            ->groupBy('category')
            ->get();

        $labels = $expenses->pluck('category');
        $data = $expenses->pluck('total');
        
        // Generate random colors or use a preset palette
        $colors = [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
            'rgba(255, 159, 64, 0.7)',
        ];

        $bgColors = collect($labels)->map(function($label, $index) use ($colors) {
            return $colors[$index % count($colors)];
        })->toArray();


        $this->chartData = [
            'labels' => $labels->toArray(),
            'datasets' => [
                [
                    'data' => $data->toArray(),
                    'backgroundColor' => $bgColors,
                    'borderWidth' => 0,
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.staff.components.category-chart');
    }
}
