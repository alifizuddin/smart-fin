<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\On;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardStats extends Component
{
    public $activeBudget = null;

    #[On('expense-created')] 
    #[On('expense-deleted')]
    public function refreshStats()
    {
        // This empty method allows the component to re-render 
        // automatically when these events are dispatched.
    }

    #[On('budget-changed')]
    public function updateBudget($budgetId)
    {
        // Find the new budget and update the property
        $this->activeBudget = Budget::find($budgetId);
    }

    public function render()
    {
        $user = Auth::user();
        
        // Default values
        $stats = [
            'total_count' => 0,
            'avg_daily' => 0,
            'percent' => 0,
            'remaining' => 0,
            'total_spent' => 0,
            'total_budget' => 0,
            'is_critical' => false,
        ];

        if ($this->activeBudget) {
            // Fetch fresh expenses for accurate calculation
            $expenses = Expense::where('user_id', $user->id)
                ->where('budget_id', $this->activeBudget->id)
                ->get();

            $approvedExpenses = $expenses->where('status', 'approved');
            $stats['total_spent'] = $approvedExpenses->sum('amount');
            
            // Use total_amount based on your DashboardController logic
            $stats['total_budget'] = $this->activeBudget->total_amount ?? 0;

            // 1. Total Expenses (Count of all submissions)
            $stats['total_count'] = $expenses->count();

            // 2. Avg Daily Spend
            $daysElapsed = max(1, now()->diffInDays(Carbon::parse($this->activeBudget->period_start)));
            $stats['avg_daily'] = $stats['total_spent'] / $daysElapsed;

            // 3. Percent Used
            $stats['percent'] = $stats['total_budget'] > 0 
                ? round(($stats['total_spent'] / $stats['total_budget']) * 100, 2) 
                : 0;
            
            $stats['is_critical'] = $stats['percent'] > 90;

            // 4. Remaining
            $stats['remaining'] = max($stats['total_budget'] - $stats['total_spent'], 0);
        }

        return view('livewire.dashboard-stats', ['stats' => $stats]);
    }
}