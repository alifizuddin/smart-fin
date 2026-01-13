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
    
    public function mount()
    {
        $this->latestForecast = Forecast::whereNull('category')
            ->orderBy('created_at', 'desc')
            ->first();

        $this->lastUpdated = $this->latestForecast ? $this->latestForecast->updated_at->diffForHumans() : 'Never';
    }

    public function generateForecast()
    {
        $this->loading = true;
        
        $service = new N8NService();
        $success = $service->generateForecast(); // Global forecast

        if ($success) {
            $this->latestForecast = Forecast::whereNull('category')
                ->orderBy('created_at', 'desc')
                ->first();
            $this->lastUpdated = Carbon::now()->diffForHumans();
            $this->dispatch('forecast-updated'); // Event to refresh chart
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
