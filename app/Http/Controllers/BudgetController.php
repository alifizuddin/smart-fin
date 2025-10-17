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
    public function index(Request $request)
    {
        // Base query
        $query = Budget::with(['department', 'creator']);

        // --- Tier 1: Filtering and Sorting ---
        $query->when($request->filled('status'), function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        $query->when($request->filled('start_date'), function ($q) use ($request) {
            return $q->where('period_start', '>=', $request->start_date);
        });

        $query->when($request->filled('end_date'), function ($q) use ($request) {
            return $q->where('period_end', '<=', $request->end_date);
        });
        
        // Default sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy == 'department') {
            $query->join('departments', 'budgets.department_id', '=', 'departments.id')
                  ->orderBy('departments.name', $sortOrder)
                  ->select('budgets.*'); // Avoid column name conflicts
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Apply role-based visibility
        if (Auth::user()->hasRole('Admin')) {
            $budgets = $query->paginate(10);
            $allBudgets = Budget::with('department')->get(); // For stats
        } else {
            $departmentId = Auth::user()->department_id;
            $budgets = $query->where('budgets.department_id', $departmentId)->paginate(10);
            $allBudgets = Budget::where('department_id', $departmentId)->get(); // For stats
        }

        // --- Tier 2 Data (from previous step) ---
        $totalBudget = $allBudgets->sum('total_amount');
        $totalSpent = $allBudgets->sum('used_amount');

        $viewData = [
            'budgets' => $budgets->appends($request->except('page')), // Persist filters on pagination
            'summary' => [
                'totalBudget' => $totalBudget,
                'totalSpent' => $totalSpent,
                'remainingFunds' => $totalBudget - $totalSpent,
                'overBudgetCount' => $allBudgets->where('used_amount', '>', 'total_amount')->count(),
            ],
            'charts' => [
                'pie' => $allBudgets->groupBy('department.name')->map->sum('total_amount'),
                'bar' => $allBudgets->groupBy('department.name')->map(fn($g) => ['total' => $g->sum('total_amount'), 'used' => $g->sum('used_amount')]),
            ],
            // Pass current filters to the view for the form fields
            'filters' => $request->only(['status', 'start_date', 'end_date', 'sort_by', 'sort_order']),
        ];

        return view('budgets.index', $viewData);
    }
    /**
 * Display a printable budget report.
 */
public function report(Request $request)
{
    // Base query
    $query = Budget::with(['department', 'creator']);

    // --- Filtering from the index page can be applied here as well ---
    $query->when($request->filled('status'), function ($q) use ($request) {
        return $q->where('status', $request->status);
    });

    $query->when($request->filled('start_date'), function ($q) use ($request) {
        return $q->where('period_start', '>=', $request->start_date);
    });

    $query->when($request->filled('end_date'), function ($q) use ($request) {
        return $q->where('period_end', '<=', $request->end_date);
    });
    
    // Apply role-based visibility
    if (Auth::user()->hasRole('Admin')) {
        $allBudgets = $query->get();
    } else {
        $departmentId = Auth::user()->department_id;
        $allBudgets = $query->where('budgets.department_id', $departmentId)->get();
    }

    $totalBudget = $allBudgets->sum('total_amount');
    $totalSpent = $allBudgets->sum('used_amount');

    $viewData = [
        'budgets' => $allBudgets,
        'summary' => [
            'totalBudget' => $totalBudget,
            'totalSpent' => $totalSpent,
            'remainingFunds' => $totalBudget - $totalSpent,
            'overBudgetCount' => $allBudgets->where('used_amount', '>', 'total_amount')->count(),
        ],
        'charts' => [
            'pie' => $allBudgets->groupBy('department.name')->map->sum('total_amount'),
            'bar' => $allBudgets->groupBy('department.name')->map(fn($g) => ['total' => $g->sum('total_amount'), 'used' => $g->sum('used_amount')]),
        ],
        // Pass current filters to the view for context
        'filters' => $request->only(['status', 'start_date', 'end_date']),
    ];

    return view('budgets.report', $viewData);
}

    // --- Add the new bulkAction method below ---

    /**
     * Handle bulk actions on budgets.
     */
    public function bulkAction(Request $request)
    {
        Gate::authorize('is-admin');

        $request->validate([
            'action' => 'required|string|in:delete',
            'budget_ids' => 'required|array',
            'budget_ids.*' => 'exists:budgets,id',
        ]);

        if ($request->action === 'delete') {
            Budget::destroy($request->budget_ids);
            return redirect()->route('budgets.index')->with('success', 'Selected budgets have been deleted successfully.');
        }

        return redirect()->route('budgets.index')->with('error', 'Invalid action selected.');
    }

    // --- Keep the rest of the controller methods (create, store, show, edit, update) as they are ---
    
    public function create()
    {
        Gate::authorize('is-admin');
        $departments = Department::all();
        return view('budgets.create', compact('departments'));
    }
    public function showJson(Budget $budget)
{
    // Eager load the department relationship to avoid N+1 issues
    $budget->load('department'); 
    return response()->json($budget);
}

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

    public function show(Request $request, Budget $budget)
    {
        Gate::authorize('view-budget', $budget);

    // Eager load relationships for both JSON and view responses
        $budget->load(['department', 'creator', 'expenses.user']);

    // --- Add this block to handle AJAX requests for the modal ---
        if ($request->wantsJson()) {
            return response()->json($budget);
        }

    // This remains for users who might access the URL directly
        return view('budgets.show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        Gate::authorize('is-admin');
        $departments = Department::all();
        return view('budgets.edit', compact('budget', 'departments'));
    }

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