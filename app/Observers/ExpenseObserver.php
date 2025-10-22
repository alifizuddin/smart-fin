<?php

namespace App\Observers;

use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExpenseObserver
{
    /**
     * Handle the Expense "created" event.
     */
    public function created(Expense $expense): void
    {
        try {
            if ($expense->budget_id && $expense->amount > 0) {
                // Use a database transaction to ensure this update is atomic
                DB::transaction(function () use ($expense) {
                    $budget = $expense->budget()->lockForUpdate()->first();
                    if ($budget) {
                        // Increment the used_amount by the expense amount
                        $budget->increment('used_amount', $expense->amount);
                    }
                });
            }
        } catch (\Exception $e) {
            // Log any errors to prevent failures
            Log::error("Failed to update budget amount for expense ID: {$expense->id}", [
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the Expense "updated" event.
     */
    public function updated(Expense $expense): void
    {
        // Check if the amount has changed
        if ($expense->isDirty('amount')) {
            try {
                // Get the original amount before the update
                $originalAmount = $expense->getOriginal('amount', 0);
                $newAmount = $expense->amount;
                $difference = $newAmount - $originalAmount;

                if ($expense->budget_id && $difference != 0) {
                    DB::transaction(function () use ($expense, $difference) {
                        $budget = $expense->budget()->lockForUpdate()->first();
                        if ($budget) {
                            // Update the budget by the difference
                            $budget->increment('used_amount', $difference);
                        }
                    });
                }
            } catch (\Exception $e) {
                Log::error("Failed to update budget (on update) for expense ID: {$expense->id}", [
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Handle the Expense "deleted" event.
     */
    public function deleted(Expense $expense): void
    {
         try {
            if ($expense->budget_id && $expense->amount > 0) {
                DB::transaction(function () use ($expense) {
                    $budget = $expense->budget()->lockForUpdate()->first();
                    if ($budget) {
                        // Decrement the used_amount (refund)
                        $budget->decrement('used_amount', $expense->amount);
                    }
                });
            }
        } catch (\Exception $e) {
            Log::error("Failed to refund budget for deleted expense ID: {$expense->id}", [
                'error' => $e->getMessage()
            ]);
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