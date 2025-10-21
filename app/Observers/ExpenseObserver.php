<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Expense;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        if ($expense->budget) {
            $this->recalculateBudget($expense->budget);
        }
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // Get the budget ID before the update
        $originalBudgetId = $expense->getOriginal('budget_id');
        
        // Get the new budget ID
        $newBudgetId = $expense->budget_id;

        // If the budget was changed (or amount changed), update the new budget
        if ($expense->wasChanged('budget_id') || $expense->wasChanged('amount')) {
            if ($expense->budget) {
                $this->recalculateBudget($expense->budget);
            }
        }

        // If the budget was changed, we must ALSO update the *old* budget
        if ($expense->wasChanged('budget_id') && $originalBudgetId) {
            $oldBudget = Budget::find($originalBudgetId);
            if ($oldBudget) {
                $this->recalculateBudget($oldBudget);
            }
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
        if ($expense->budget) {
            $this->recalculateBudget($expense->budget);
        }
    }
    
    /**
     * Handle the Expense "restored" event.
     */
    public function restored(Expense $expense): void
    {
         if ($expense->budget) {
            $this->recalculateBudget($expense->budget);
        }
    }

    /**
     * Handle the Expense "force deleted" event.
     */
    public function forceDeleted(Expense $expense): void
    {
         if ($expense->budget) {
            $this->recalculateBudget($expense->budget);
        }
    }

    /**
     * Recalculates the total 'used_amount' for a given budget.
     */
    protected function recalculateBudget(Budget $budget): void
    {
        // Sum all expenses for this budget
        $totalUsed = $budget->expenses()->sum('amount');
        
        // Update the budget's 'used_amount'
        $budget->used_amount = $totalUsed;
        $budget->saveQuietly(); // saveQuietly prevents triggering other events
    }
}