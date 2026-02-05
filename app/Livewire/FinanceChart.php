<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\FinanceService;

class FinanceChart extends Component
{
    public $chartData = [];
    public $labels = [];
    public $datasets = [];

    protected $financeService;

    public function mount()
    {
        $this->loadChartData();
    }

    public function loadChartData()
    {
        // Get data from your service or repository
        $this->chartData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'datasets' => [
                [
                    'label' => 'Marketing',
                    'data' => [45000, 52000, 48000, 61000, 58000, 67000],
                    'borderColor' => 'rgba(99, 102, 241, 1)',
                    'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                    'tension' => 0.4
                ],
                // ... other datasets
            ]
        ];

        $this->labels = $this->chartData['labels'];
        $this->datasets = $this->chartData['datasets'];
    }

    public function render()
    {
        return view('livewire.finance-chart');
    }
}