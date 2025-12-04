<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On; // [1] Required for event listening

class ExpenseList extends Component
{
    public $budgetId;
    public $limit = 5; // Default to showing 5 items

    // [2] Listener: When an expense is created, refresh this component automatically
    #[On('expense-created')] 
    public function refreshList()
    {
        // This method doesn't need to do anything; the attribute triggers a re-render.
    }

    #[On('budget-changed')]
    public function updateBudget($budgetId)
    {
        $this->budgetId = $budgetId;
    }

    public function delete($id)
    {
        $expense = Expense::find($id);

        if ($expense && $expense->user_id === Auth::id()) {
            $expense->delete();
        
            // ADD THIS LINE:
            $this->dispatch('expense-deleted'); 
        }
    }

    public function render()
    {
        // Build the query exactly like your Controller does
        $query = Expense::where('user_id', Auth::id())
            ->with(['budget']) // Eager load budget for performance
            ->latest();

        if ($this->budgetId) {
            $query->where('budget_id', $this->budgetId);
        }

        return view('livewire.expense-list', [
            'expenses' => $query->take($this->limit)->get()
        ]);
    }
}