<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Budget;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;

class EditBudget extends Component
{
    public Budget $budget;
    public $name;
    public $department_id;
    public $total_amount;
    public $notes;
    public $status;
    public $isLoading = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'department_id' => 'required|exists:departments,id',
        'total_amount' => 'required|numeric|min:0',
        'status' => 'required|in:active,pending,closed,draft',
        'notes' => 'nullable|string'
    ];

    public function mount($budgetId)
    {
        $this->budget = Budget::findOrFail($budgetId);
        
        // Check authorization
        if (Auth::user()->cannot('update', $this->budget)) {
            abort(403);
        }

        $this->fill([
            'name' => $this->budget->name,
            'department_id' => $this->budget->department_id,
            'total_amount' => $this->budget->total_amount,
            'notes' => $this->budget->notes,
            'status' => $this->budget->status
        ]);

        $this->isLoading = false;
    }

    public function updateBudget()
    {
        $this->validate();

        $this->budget->update([
            'name' => $this->name,
            'department_id' => $this->department_id,
            'total_amount' => $this->total_amount,
            'notes' => $this->notes,
            'status' => $this->status
        ]);

        session()->flash('message', 'Budget updated successfully!');
        return $this->redirect(route('budgets.index'), navigate: true);
    }

    public function getDepartmentsProperty()
    {
        return Department::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.edit-budget', [
            'departments' => $this->departments
        ]);
    }
}
