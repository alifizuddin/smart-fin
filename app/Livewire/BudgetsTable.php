<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Budget;
use App\Models\Department;
use App\Models\Expense;
use App\Services\BudgetService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Illuminate\Support\Facades\Auth;

class BudgetsTable extends Component
{
    use WithPagination;
    public $editingBudget = null;
    public $form = [
        'name' => '',
        'department_id' => '',
        'total_amount' => '',
        'notes' => '',
        'status' => 'active'
    ];

    // Filter properties
    #[Url]
    public $status = '';
    
    #[Url]
    public $department_id = '';
    
    #[Url]
    public $start_date = '';
    
    #[Url]
    public $end_date = '';
    
    #[Url]
    public $sort_by = 'created_at';
    
    #[Url]
    public $sort_order = 'desc';

    protected $budgetService;

    public function __construct()
    {
        $this->budgetService = app(BudgetService::class);
    }

    #[Computed]
    public function departments()
    {
        return Department::orderBy('name')->get();
    }

    #[Computed]
    public function budgets()
    {
        $query = Budget::query()
        ->with(['department', 'expenses' => function($q) {
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

    // Apply sorting
    $query->orderBy($this->sort_by, $this->sort_order);

    return $query->paginate(10);
    }

    #[Computed]
    public function summary()
    {
        $budgets = $this->budgets->items();
        
        $totalBudget = collect($budgets)->sum('total_amount');
        $totalSpent = collect($budgets)->sum(function($budget) {
            return $budget->expenses->sum('amount');
        });
        
        return [
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remainingFunds' => $totalBudget - $totalSpent,
            'overBudgetCount' => collect($budgets)->filter(function($budget) {
                return $budget->expenses->sum('amount') > $budget->total_amount;
            })->count(),
        ];
    }

    public function sortBy($field)
    {
        if ($this->sort_by === $field) {
            $this->sort_order = $this->sort_order === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort_by = $field;
            $this->sort_order = 'asc';
        }
    }

    public function resetFilters()
    {
        $this->reset(['status', 'department_id', 'start_date', 'end_date']);
    }

    public $viewingBudget = null;
    public $budgetDetails = null;

    public function viewBudget($budgetId)
    {
        $this->viewingBudget = Budget::with(['department', 'expenses' => function($q) {
            $q->where('status', 'approved');
        }])->findOrFail($budgetId);

        $usedAmount = $this->viewingBudget->expenses->sum('amount');
        $remaining = $this->viewingBudget->total_amount - $usedAmount;
        $isOverBudget = $remaining < 0;
        $percentage = $this->viewingBudget->total_amount > 0 ? 
            min(100, round(($usedAmount / $this->viewingBudget->total_amount) * 100 * 10) / 10) : 0;

        $this->budgetDetails = [
            'id' => $this->viewingBudget->id,
            'name' => $this->viewingBudget->name,
            'status' => $this->viewingBudget->status,
            'department' => $this->viewingBudget->department->name ?? 'N/A',
            'total_amount' => number_format($this->viewingBudget->total_amount, 2),
            'used_amount' => number_format($usedAmount, 2),
            'remaining' => number_format(abs($remaining), 2),
            'is_over_budget' => $isOverBudget,
            'percentage' => $percentage,
            'notes' => $this->viewingBudget->notes,
            'created_at' => $this->viewingBudget->created_at->format('M d, Y'),
            'updated_at' => $this->viewingBudget->updated_at->format('M d, Y'),
            'creator' => $this->viewingBudget->creator->name ?? 'N/A',
        ];

        $this->dispatch('open-budget-modal');
    }
    public function editBudget($budgetId)
{
    $this->editingBudget = Budget::findOrFail($budgetId);
    $this->form = [
        'name' => $this->editingBudget->name,
        'department_id' => $this->editingBudget->department_id,
        'total_amount' => $this->editingBudget->total_amount,
        'notes' => $this->editingBudget->notes,
        'status' => $this->editingBudget->status
    ];
}

public function updateBudget()
{
    $this->validate([
        'form.name' => 'required|string|max:255',
        'form.department_id' => 'required|exists:departments,id',
        'form.total_amount' => 'required|numeric|min:0',
        'form.status' => 'required|in:active,pending,closed,draft',
        'form.notes' => 'nullable|string'
    ]);

    $this->editingBudget->update($this->form);
    $this->editingBudget = null;
    $this->reset('form');
    
    session()->flash('message', 'Budget updated successfully!');
}

public function closeEditModal()
{
    $this->editingBudget = null;
    $this->reset('form');
}

    public function closeBudgetModal()
    {
        $this->viewingBudget = null;
        $this->budgetDetails = null;
    }

    public function render()
    {
        return view('livewire.budgets-table', [
            'budgets' => $this->budgets,
            'summary' => $this->summary,
            'departments' => Department::orderBy('name')->get()
        ]);
    }
}
