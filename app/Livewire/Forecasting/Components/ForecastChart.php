<?php

namespace App\Livewire\Forecasting\Components;

use Livewire\Component;
use App\Models\Expense;
use App\Models\Forecast;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class ForecastChart extends Component
{
    public $chartData = [];

    #[On('forecast-updated')] 
    public function updateChart()
    {
        $this->loadData();
        $this->dispatch('chart-data-updated', data: $this->chartData);
    }

    public function mount()
    {
        $this->loadData();
    }

    public function loadData()
    {
        // 1. Historical Data (Last 12 Months)
        $history = Expense::where('status', 'approved')
            ->where('created_at', '>=', Carbon::now()->subMonths(11)->startOfMonth())
            ->selectRaw('DATE_FORMAT(created_at, "%Y-%m-01") as month, SUM(amount) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 2. Forecast Data (Future)
        $forecasts = Forecast::whereNull('category')
            ->where('forecast_date', '>=', Carbon::now()->startOfMonth())
            ->orderBy('forecast_date')
            ->get();

        // 3. Merge Labels
        $historicalLabels = $history->pluck('month')->map(fn($d) => Carbon::parse($d)->format('M Y'));
        $forecastLabels = $forecasts->pluck('forecast_date')->map(fn($d) => $d->format('M Y'));
        
        $allLabels = $historicalLabels->merge($forecastLabels)->unique()->values();

        // 4. Map Data to Labels
        // Historical data array (matched to allLabels)
        $historicalDataPoints = $allLabels->map(function($label) use ($history) {
            $item = $history->first(fn($h) => Carbon::parse($h->month)->format('M Y') === $label);
            return $item ? $item->total : null;
        });

        // Forecast data array
        $forecastDataPoints = $allLabels->map(function($label) use ($forecasts) {
            $item = $forecasts->first(fn($f) => $f->forecast_date->format('M Y') === $label);
            return $item ? $item->amount : null;
        });

        $this->chartData = [
            'labels' => $allLabels->toArray(),
            'datasets' => [
                [
                    'label' => 'Actual Spending',
                    'data' => $historicalDataPoints->toArray(),
                    'borderColor' => 'rgba(79, 70, 229, 1)', // Indigo-600
                    'backgroundColor' => 'rgba(79, 70, 229, 0.1)',
                    'tension' => 0.4,
                    'fill' => true
                ],
                [
                    'label' => 'Forecast',
                    'data' => $forecastDataPoints->toArray(),
                    'borderColor' => 'rgba(147, 51, 234, 1)', // Purple-600
                    'backgroundColor' => 'rgba(147, 51, 234, 0.1)',
                    'borderDash' => [5, 5],
                    'tension' => 0.4,
                    'fill' => false
                ]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.forecasting.components.forecast-chart');
    }
}
