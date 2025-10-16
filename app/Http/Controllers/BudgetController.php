<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BudgetController extends Controller
{
    /**
     * Display a listing of the budgets.
     */
    public function index()
    {
        if (Auth::user()->role === 'admin') {
            $budgets = Budget::with('department')->latest()->paginate(10);
        } else {
            // Managers and Staff see budgets for their department only
            $budgets = Budget::where('department_id', Auth::user()->department_id)
                ->with('department')
                ->latest()
                ->paginate(10);
        }
        return view('budgets.index', compact('budgets'));
    }

    /**
     * Show the form for creating a new budget.
     */
    public function create()
    {
        Gate::authorize('is-admin'); // Only admins can create budgets
        $departments = Department::all();
        return view('budgets.create', compact('departments'));
    }

    /**
     * Store a newly created budget in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('is-admin');

        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'total_amount' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
        ]);

        Budget::create($request->all() + ['created_by' => Auth::id()]);

        return redirect()->route('budgets.index')->with('success', 'Budget created successfully.');
    }

    /**
     * Display the specified budget.
     */
    public function show(Budget $budget)
    {
        Gate::authorize('view-budget', $budget);
        return view('budgets.show', compact('budget'));
    }

    /**
     * Show the form for editing the specified budget.
     */
    public function edit(Budget $budget)
    {
        Gate::authorize('is-admin');
        $departments = Department::all();
        return view('budgets.edit', compact('budget', 'departments'));
    }

    /**
     * Update the specified budget in storage.
     */
    public function update(Request $request, Budget $budget)
    {
        Gate::authorize('is-admin');

        $request->validate([
            'total_amount' => 'required|numeric|min:0',
            'period_start' => 'required|date',
            'period_end' => 'required|date|after_or_equal:period_start',
            'status' => 'required|string|in:active,closed',
        ]);

        $budget->update($request->all());

        return redirect()->route('budgets.index')->with('success', 'Budget updated successfully.');
    }
}