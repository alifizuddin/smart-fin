<?php

namespace App\Livewire\Forecasting;

use Livewire\Component;
use App\Models\Forecast;
use App\Models\Expense;
use App\Services\N8NService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Index extends Component
{
    public $loading = false;
    public $lastUpdated;
    public $latestForecast;
    public $upcomingForecasts = [];
    public $category = null;
    public $categories = []; // List of available categories
    public $projectionMonths = 3;
    
    public function mount()
    {
        // Load available categories from Expenses
        $this->categories = Expense::distinct()
            ->whereNotNull('category')
            ->orderBy('category')
            ->pluck('category')
            ->toArray();

        $this->loadLatestForecast();
    }

    public function updatedCategory()
    {
        $this->loadLatestForecast();
        // Dispatch event to update the chart component
        $this->dispatch('forecast-updated', category: $this->category);
    }

    public function loadLatestForecast()
    {
        $query = Forecast::query();

        if ($this->category) {
            $query->where('category', $this->category);
        } else {
            $query->whereNull('category');
        }

        // Get all future forecasts
        $forecasts = $query
            ->where('forecast_date', '>=', Carbon::now()->startOfMonth())
            ->orderBy('forecast_date', 'asc')
            ->limit(3) // Get up to 3 months of forecasts
            ->get();

        $this->upcomingForecasts = $forecasts->all();
        $this->latestForecast = $forecasts->first();

        // Fallback: if no future forecasts, get the most recent one
        if ($forecasts->isEmpty()) {
            $this->latestForecast = $query->orderBy('created_at', 'desc')->first();
        }

        $this->lastUpdated = $this->latestForecast ? $this->latestForecast->updated_at->diffForHumans() : 'Never';
    }

    public function generateForecast()
    {
        $this->loading = true;
        
        $service = new N8NService();
        $success = $service->generateForecast($this->category); // Pass selected category

        if ($success) {
            $this->loadLatestForecast();
            $this->dispatch('forecast-updated', category: $this->category); 
        } else {
            // Flash error
            session()->flash('error', 'Failed to generate forecast. Check logs for details.');
        }

        $this->loading = false;
    }

    public function render()
    {
        return view('livewire.forecasting.index');
    }
}
