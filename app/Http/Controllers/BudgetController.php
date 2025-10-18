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
    // ============================================================
    // 1️⃣ Base Query Setup
    // ============================================================
    $query = Budget::with(['department', 'creator']);

    // ============================================================
    // 2️⃣ Filtering Logic
    // ============================================================
    $query->when($request->filled('department_id'), function ($q) use ($request) {
        return $q->where('department_id', $request->department_id);
    });

    $query->when($request->filled('status'), function ($q) use ($request) {
        return $q->where('status', $request->status);
    });

    $query->when($request->filled('start_date'), function ($q) use ($request) {
        return $q->where('period_start', '>=', $request->start_date);
    });

    $query->when($request->filled('end_date'), function ($q) use ($request) {
        return $q->where('period_end', '<=', $request->end_date);
    });

    // ============================================================
    // 3️⃣ Sorting Logic
    // ============================================================
    $sortBy = $request->get('sort_by', 'created_at');
    $sortOrder = $request->get('sort_order', 'desc');

    // Sort by department name (requires join to avoid ambiguity)
    if ($sortBy === 'department') {
        $query->join('departments', 'budgets.department_id', '=', 'departments.id')
              ->orderBy('departments.name', $sortOrder)
              ->select('budgets.*'); // Prevent column name conflicts
    } else {
        $query->orderBy($sortBy, $sortOrder);
    }

    // ============================================================
    // 4️⃣ Role-Based Data Visibility
    // ============================================================
    if (Auth::user()->hasRole('Admin')) {
        // Admins see all budgets
        $budgets = $query->paginate(10);
        $allBudgets = Budget::with('department')->get(); // For summary stats
    } else {
        // Non-admins only see their own department's budgets
        $departmentId = Auth::user()->department_id;

        $budgets = $query->where('budgets.department_id', $departmentId)
                         ->paginate(10);

        $allBudgets = Budget::where('department_id', $departmentId)
                            ->with('department')
                            ->get(); // For summary stats
    }

    // ============================================================
    // 5️⃣ Summary Statistics
    // ============================================================
    $totalBudget = $allBudgets->sum('total_amount');
    $totalSpent  = $allBudgets->sum('used_amount');

    $summary = [
        'totalBudget'      => $totalBudget,
        'totalSpent'       => $totalSpent,
        'remainingFunds'   => $totalBudget - $totalSpent,
        'overBudgetCount'  => $allBudgets->where('used_amount', '>', 'total_amount')->count(),
    ];

    // ============================================================
    // 6️⃣ Chart Data Preparation
    // ============================================================
    $charts = [
        'pie' => $allBudgets
            ->groupBy('department.name')
            ->map->sum('total_amount'),

        'bar' => $allBudgets
            ->groupBy('department.name')
            ->map(fn($group) => [
                'total' => $group->sum('total_amount'),
                'used'  => $group->sum('used_amount'),
            ]),
    ];

    // ============================================================
    // 7️⃣ Fetch Filter Options
    // ============================================================
    $departments = Department::orderBy('name')->get();

    // ============================================================
    // 8️⃣ Prepare View Data
    // ============================================================
    $viewData = [
        'budgets'      => $budgets->appends($request->except('page')), // Persist filters in pagination
        'departments'  => $departments, // For filter dropdown
        'summary'      => $summary,
        'charts'       => $charts,
        'filters'      => $request->only(['status', 'start_date', 'end_date', 'sort_by', 'sort_order', 'department_id']),
    ];

    // ============================================================
    // 9️⃣ Return View Page
    // ============================================================
    return view('budgets.index', $viewData);
}
public function report(Request $request)
{
    // ============================================================
    // 1️⃣ Base Query Setup
    // ============================================================
    $query = Budget::with(['department', 'creator']);

    // ============================================================
    // 2️⃣ Apply Filters (reused from index page)
    // ============================================================
    $query->when($request->filled('status'), function ($q) use ($request) {
        return $q->where('status', $request->status);
    });

    $query->when($request->filled('start_date'), function ($q) use ($request) {
        return $q->where('period_start', '>=', $request->start_date);
    });

    $query->when($request->filled('end_date'), function ($q) use ($request) {
        return $q->where('period_end', '<=', $request->end_date);
    });

    // ============================================================
    // 3️⃣ Role-Based Data Visibility
    // ============================================================
    if (Auth::user()->hasRole('Admin')) {
        // Admins can view all budget records
        $allBudgets = $query->get();
    } else {
        // Non-admins only see budgets from their department
        $departmentId = Auth::user()->department_id;
        $allBudgets = $query->where('budgets.department_id', $departmentId)->get();
    }

    // ============================================================
    // 4️⃣ Summary Statistics
    // ============================================================
    $totalBudget = $allBudgets->sum('total_amount');
    $totalSpent  = $allBudgets->sum('used_amount');

    $summary = [
        'totalBudget'      => $totalBudget,
        'totalSpent'       => $totalSpent,
        'remainingFunds'   => $totalBudget - $totalSpent,
        'overBudgetCount'  => $allBudgets->where('used_amount', '>', 'total_amount')->count(),
    ];

    // ============================================================
    // 5️⃣ Chart Data Preparation
    // ============================================================
    $charts = [
        'pie' => $allBudgets
            ->groupBy('department.name')
            ->map
            ->sum('total_amount'),

        'bar' => $allBudgets
            ->groupBy('department.name')
            ->map(fn($group) => [
                'total' => $group->sum('total_amount'),
                'used'  => $group->sum('used_amount'),
            ]),
    ];

    // ============================================================
    // 6️⃣ Prepare Data for the View
    // ============================================================
    $viewData = [
        'budgets' => $allBudgets,
        'summary' => $summary,
        'charts'  => $charts,
        'filters' => $request->only(['status', 'start_date', 'end_date']), // For maintaining filter context
    ];

    // ============================================================
    // 7️⃣ Return Report View
    // ============================================================
    return view('budgets.report', $viewData);
}

public function bulkAction(Request $request)
{
    // ============================================================
    // 1️⃣ Authorization
    // ============================================================
    Gate::authorize('is-admin'); 
    // Only Admins are allowed to perform bulk actions.

    // ============================================================
    // 2️⃣ Validate Request Inputs
    // ============================================================
    $request->validate([
        'action'       => 'required|string|in:delete', // Only 'delete' supported for now
        'budget_ids'   => 'required|array',             // Must be an array of IDs
        'budget_ids.*' => 'exists:budgets,id',          // Ensure all IDs exist in DB
    ]);

    // ============================================================
    // 3️⃣ Handle Requested Bulk Action
    // ============================================================
    switch ($request->action) {
        case 'delete':
            // Perform bulk deletion
            Budget::destroy($request->budget_ids);

            return redirect()
                ->route('budgets.index')
                ->with('success', 'Selected budgets have been deleted successfully.');

        default:
            // Invalid or unsupported action
            return redirect()
                ->route('budgets.index')
                ->with('error', 'Invalid action selected.');
    }
}
    
    public function create()
{
    // ============================================================
    // 1️⃣ Authorization
    // ============================================================
    Gate::authorize('is-admin');
    // Only admins can access the create budget form.

    // ============================================================
    // 2️⃣ Fetch Required Data
    // ============================================================
    $departments = Department::all();
    // Retrieve all departments for the department dropdown list.

    // ============================================================
    // 3️⃣ Return View
    // ============================================================
    return view('budgets.create', compact('departments'));
}
/**
 * Return budget details as JSON (for AJAX or API usage).
 *
 * @param  \App\Models\Budget  $budget
 * @return \Illuminate\Http\JsonResponse
 */
public function showJson(Budget $budget)
{
    // ============================================================
    // 1️⃣ Eager Load Relationships
    // ============================================================
    $budget->load('department');
    // Prevent N+1 query issues by preloading the related department.

    // ============================================================
    // 2️⃣ Return JSON Response
    // ============================================================
    return response()->json($budget);
}

    /**
 * Store a newly created budget in the database.
 *
 * @param  \Illuminate\Http\Request  $request
 * @return \Illuminate\Http\RedirectResponse
 */
public function store(Request $request)
{
    // ============================================================
    // 1️⃣ Authorization
    // ============================================================
    Gate::authorize('is-admin');
    // Only admins can create new budgets.

    // ============================================================
    // 2️⃣ Validation
    // ============================================================
    $validated = $request->validate([
        'department_id' => 'required|exists:departments,id',
        'total_amount'  => 'required|numeric|min:0',
        'period_start'  => 'required|date',
        'period_end'    => 'required|date|after_or_equal:period_start',
    ]);

    // ============================================================
    // 3️⃣ Create Budget Record
    // ============================================================
    Budget::create($validated + ['created_by' => Auth::id()]);

    // ============================================================
    // 4️⃣ Redirect with Success Message
    // ============================================================
    return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget created successfully.');
}


/**
 * Display the specified budget (supports both web and AJAX/modal requests).
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \App\Models\Budget        $budget
 * @return \Illuminate\Http\JsonResponse|\Illuminate\View\View
 */
public function show(Request $request, Budget $budget)
{
    // ============================================================
    // 1️⃣ Authorization
    // ============================================================
    Gate::authorize('view-budget', $budget);
    if ($request->wantsJson()) {
        Gate::authorize('is-admin');
        // For JSON requests, return budget data as JSON
        return response()->json($budget);
    } else { 
        Gate::authorize('view-budget', $budget);
    }

    // ============================================================
    // 2️⃣ Eager Load Relationships
    // ============================================================
    $budget->load(['department', 'creator', 'expenses.user']);

    // ============================================================
    // 3️⃣ Handle AJAX Requests (e.g., modal)
    // ============================================================
    if ($request->wantsJson()) {
        return response()->json($budget);
    }

    // ============================================================
    // 4️⃣ Handle Direct Page View
    // ============================================================
    return view('budgets.show', compact('budget'));
}


/**
 * Show the form for editing an existing budget.
 *
 * @param  \App\Models\Budget  $budget
 * @return \Illuminate\View\View
 */
public function edit(Budget $budget)
{
    // ============================================================
    // 1️⃣ Authorization
    // ============================================================
    Gate::authorize('is-admin');

    // ============================================================
    // 2️⃣ Fetch Required Data
    // ============================================================
    $departments = Department::all();

    // ============================================================
    // 3️⃣ Return View
    // ============================================================
    return view('budgets.edit', compact('budget', 'departments'));
}


/**
 * Update the specified budget in the database.
 *
 * @param  \Illuminate\Http\Request  $request
 * @param  \App\Models\Budget        $budget
 * @return \Illuminate\Http\RedirectResponse
 */
public function update(Request $request, Budget $budget)
{
    // ============================================================
    // 1️⃣ Authorization
    // ============================================================
    Gate::authorize('is-admin');

    // ============================================================
    // 2️⃣ Validation
    // ============================================================
    $validated = $request->validate([
        'department_id' => 'required|exists:departments,id',
        'total_amount' => 'required|numeric|min:0',
        'period_start' => 'required|date',
        'period_end'   => 'required|date|after_or_equal:period_start',
        'status'       => 'required|string|in:active,closed',
    ]);

    // ============================================================
    // 3️⃣ Update Budget Record
    // ============================================================
    $budget->update($validated);

    // ============================================================
    // 4️⃣ Redirect with Success Message
    // ============================================================
    return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget updated successfully.');
}

}