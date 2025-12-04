<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Expense;
use Livewire\Attributes\On;

class SpendingChart extends Component
{
    public $labels = [];
    public $data = [];

    public function mount()
    {
        $this->updateChartData();
    }

    #[On('expense-approved')]
    #[On('expense-created')] 
    public function updateChartData()
    {
        $user = Auth::user();
        
        // Logic adapted from DashboardController (Manager section)
        // You can easily adapt this for Staff by changing the query to use 'user_id'
        $query = Expense::where('department_id', $user->department_id)
            ->where('status', 'approved');

        $results = $query->groupBy('category')
            ->select('category', DB::raw('SUM(amount) as total_amount'))
            ->orderBy('total_amount', 'desc')
            ->get();

        $this->labels = $results->pluck('category')->toArray();
        $this->data = $results->pluck('total_amount')->toArray();
        
        // Dispatch event for Alpine.js to pick up
        $this->dispatch('chart-data-updated', labels: $this->labels, data: $this->data);
    }

    public function render()
    {
        return view('livewire.spending-chart');
    }
}