<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use App\Models\Department;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class BudgetStats extends Component
{
    #[Url]
    public $department_id = '';

    #[Computed]
    public function departments()
    {
        return Department::orderBy('name')->get();
    }

    #[Computed]
    public function summary()
    {
        $query = Budget::query()
        ->with(['expenses' => function($q) {
            $q->where('status', 'approved');
        }]);

    // If user is a manager, filter by their department
    if (auth()->user()->hasRole('Manager')) {
        $query->where('department_id', auth()->user()->department_id);
    } 
    // If user is admin and has selected a department
    elseif (auth()->user()->hasRole('Admin') && $this->department_id) {
        $query->where('department_id', $this->department_id);
    }

        $budgets = $query->get();
        
        $totalBudget = $budgets->sum('total_amount');
        $totalSpent = $budgets->sum(function($budget) {
            return $budget->expenses->sum('amount');
        });
        
        return [
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remainingFunds' => $totalBudget - $totalSpent,
            'overBudgetCount' => $budgets->filter(function($budget) {
                return $budget->expenses->sum('amount') > $budget->total_amount;
            })->count(),
        ];
    }

    public function updatedDepartmentId()
    {
        $this->dispatch('department-updated', departmentId: $this->department_id);
    }

    public function render()
    {
        return view('livewire.budget-stats', [
            'summary' => $this->summary,
        ]);
    }
}