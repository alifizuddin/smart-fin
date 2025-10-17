<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Spatie\Permission\Models\Role;

class BudgetController extends Controller
{

public function index()
{
    if (Auth::user()->hasRole('Admin')) {
        $budgets = Budget::with('department')->latest()->paginate(10);
        // For admin, calculate stats across all budgets
        $allBudgets = Budget::with('department')->get();
    } else {
        // For others, scope everything to their department
        $departmentId = Auth::user()->department_id;
        $budgets = Budget::where('department_id', $departmentId)
            ->with('department')
            ->latest()
            ->paginate(10);
        $allBudgets = Budget::where('department_id', $departmentId)->get();
    }

    // --- Tier 2: Summary Card and Chart Data ---

    // 1. Summary Card Calculations
    $totalBudget = $allBudgets->sum('total_amount');
    $totalSpent = $allBudgets->sum('used_amount');
    $remainingFunds = $totalBudget - $totalSpent;
    $overBudgetCount = $allBudgets->where('used_amount', '>', 'total_amount')->count();

    // 2. Chart Data Preparation
    $departmentBudgets = $allBudgets->groupBy('department.name');

    $pieChartData = $departmentBudgets->map(function ($group) {
        return $group->sum('total_amount');
    });

    $barChartData = $departmentBudgets->map(function ($group) {
        return [
            'total' => $group->sum('total_amount'),
            'used' => $group->sum('used_amount'),
        ];
    });

    // Consolidate all data for the view
    $viewData = [
        'budgets' => $budgets,
        'summary' => [
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remainingFunds' => $remainingFunds,
            'overBudgetCount' => $overBudgetCount,
        ],
        'charts' => [
            'pie' => $pieChartData,
            'bar' => $barChartData,
        ],
    ];

    return view('budgets.index', $viewData);
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