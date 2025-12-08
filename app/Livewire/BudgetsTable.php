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
    public $loadingBudget = null;
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

    // If user is a manager or staff, filter by their department
    if (auth()->user()->hasRole('Manager') || auth()->user()->hasRole('Staff')) {
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

    // In the viewBudget method
public function viewBudget($id)
{
    // Set the loading state for this budget
    $this->loadingBudget = $id;
    $this->budgetDetails = null;
    
    // Force a re-render to show the loading spinner
    $this->dispatch('$refresh');
    
    // Small delay to ensure the loading state is shown
    usleep(500000); // 100ms delay
    
    try {
        $budget = Budget::with(['department', 'expenses' => function($q) {
            $q->where('status', 'approved');
        }])->findOrFail($id);

        $usedAmount = $budget->expenses->sum('amount');
        $remaining = $budget->total_amount - $usedAmount;
        $percentage = $budget->total_amount > 0 ? round(($usedAmount / $budget->total_amount) * 100, 1) : 0;

        $this->budgetDetails = [
            'id' => $budget->id,
            'name' => $budget->name,
            'department' => $budget->department->name,
            'total_amount' => number_format($budget->total_amount, 2),
            'used_amount' => number_format($usedAmount, 2),
            'remaining' => number_format(abs($remaining), 2),
            'is_over_budget' => $remaining < 0,
            'percentage' => $percentage,
            'status' => $budget->status,
            'notes' => $budget->notes,
            'created_at' => $budget->created_at->format('M d, Y'),
            'updated_at' => $budget->updated_at->format('M d, Y'),
            'creator' => $budget->creator->name ?? 'N/A',
        ];
        $this->viewingBudget = $budget;
    } finally {
        // Clear loading state when done
        $this->loadingBudget = null;
    }

    $this->dispatch('open-budget-modal');
}

    public function editBudget($budgetId)
    {
        usleep(200000);
        $this->editingBudget = Budget::findOrFail($budgetId);
        $this->form = [
            'name' => $this->editingBudget->name,
            'department_id' => $this->editingBudget->department_id,
            'total_amount' => $this->editingBudget->total_amount,
            'notes' => $this->editingBudget->notes,
            'status' => $this->editingBudget->status
        ];
        $this->dispatch('open-edit-budget-modal');
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