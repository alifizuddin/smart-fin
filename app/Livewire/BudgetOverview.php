<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class BudgetOverview extends Component
{
    public $activeBudget = null;

    #[On('budget-changed')]
    public function updateBudget($budgetId)
    {
        $this->activeBudget = Budget::find($budgetId);
    }

    public function render()
    {
        $stats = [
            'total_spent' => 0,
            'total_budget' => 0,
            'remaining' => 0,
            'percent' => 0,
        ];

        if ($this->activeBudget) {
            $user = Auth::user();
            $expenses = Expense::where('user_id', $user->id)
                ->where('budget_id', $this->activeBudget->id)
                ->get();

            $approvedExpenses = $expenses->where('status', 'approved');
            $stats['total_spent'] = $approvedExpenses->sum('amount');
            $stats['total_budget'] = $this->activeBudget->total_amount ?? 0;
            $stats['remaining'] = max($stats['total_budget'] - $stats['total_spent'], 0);
            $stats['percent'] = $stats['total_budget'] > 0 
                ? round(($stats['total_spent'] / $stats['total_budget']) * 100, 2) 
                : 0;
        }

        return view('livewire.budget-overview', [
            'stats' => $stats,
            'budget' => $this->activeBudget,
        ]);
    }
}
