<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use Illuminate\Support\Facades\Auth;

class BudgetSelector extends Component
{
    public $selectedBudgetId;
    public $budgets;

    public function mount($activeBudgetId = null)
    {
        // Fetch budgets for the dropdown (same logic as your Controller)
        $this->budgets = Budget::where('department_id', Auth::user()->department_id)
            ->where('status', 'active')
            ->where('period_end', '>=', now())
            ->orderBy('period_end', 'asc')
            ->get();

        // Set the selected budget ID (use first budget if none provided)
        $this->selectedBudgetId = $activeBudgetId ?? $this->budgets->first()?->id;
    }

    // Lifecycle Hook: Runs automatically when $selectedBudgetId changes
    public function updatedSelectedBudgetId($value)
    {
        // Dispatch event so other components (Stats, List) can update
        $this->dispatch('budget-changed', budgetId: $value);
    }

    public function render()
    {
        return view('livewire.budget-selector');
    }
}