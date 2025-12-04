<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class BudgetStatus extends Component
{
    public $activeBudget = null;

    #[On('budget-changed')]
    public function updateBudget($budgetId)
    {
        $this->activeBudget = Budget::find($budgetId);
    }

    public function render()
    {
        $percent = 0;

        if ($this->activeBudget) {
            $user = Auth::user();
            $expenses = Expense::where('user_id', $user->id)
                ->where('budget_id', $this->activeBudget->id)
                ->get();

            $approvedExpenses = $expenses->where('status', 'approved');
            $totalSpent = $approvedExpenses->sum('amount');
            $totalBudget = $this->activeBudget->total_amount ?? 0;
            $percent = $totalBudget > 0 
                ? round(($totalSpent / $totalBudget) * 100, 2) 
                : 0;
        }

        return view('livewire.budget-status', ['percent' => $percent]);
    }
}
