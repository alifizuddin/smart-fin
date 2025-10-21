<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Department;
use App\Models\Projects;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Services\BudgetService;

class BudgetController extends Controller
{
    protected $budgetService; // 2. CREATE A PROPERTY FOR IT

    // 3. INJECT THE SERVICE IN THE CONSTRUCTOR
    public function __construct(BudgetService $budgetService)
    {
        $this->budgetService = $budgetService;
    }

    /**
     * Display a listing of the budgets.
     */
    public function index(Request $request)
    {
        // ============================================================
        // 1️⃣ Get Data from the Service
        // ============================================================
        
        // Get the paginated list for the table
        $budgets = $this->budgetService->getPaginatedBudgets($request);
        
        // Get ALL filtered budgets for the summary and charts
        $allFilteredBudgets = $this->budgetService->getAllFilteredBudgets($request);
        
        // ============================================================
        // 2️⃣ Get Summary & Chart Data from the Service
        // ============================================================
        $summary = $this->budgetService->getBudgetSummary($allFilteredBudgets);
        $charts = $this->budgetService->getBudgetChartData($allFilteredBudgets);

        // ============================================================
        // 3️⃣ Fetch Filter Options
        // ============================================================
        $departments = Department::orderBy('name')->get();

        // ============================================================
        // 4️⃣ Prepare View Data
        // ============================================================
        $viewData = [
            'budgets'      => $budgets, // The paginated list
            'departments'  => $departments,
            'summary'      => $summary,
            'charts'       => $charts,
            'filters'      => $request->only(['status', 'start_date', 'end_date', 'sort_by', 'sort_order', 'department_id']),
        ];

        // ============================================================
        // 5️⃣ Return View Page
        // ============================================================
        return view('budgets.index', $viewData);
    }
    
    public function report(Request $request)
    {
        $this->authorize('viewAny', Budget::class);
        // ============================================================
        // 1️⃣ Get ALL Data from the Service
        // ============================================================
        $allBudgets = $this->budgetService->getAllFilteredBudgets($request);

        // ============================================================
        // 2️⃣ Get Summary & Chart Data from the Service
        // ============================================================
        $summary = $this->budgetService->getBudgetSummary($allBudgets);
        $charts = $this->budgetService->getBudgetChartData($allBudgets);
        
        // ============================================================
        // 3️⃣ Prepare Data for the View
        // ============================================================
        $viewData = [
            'budgets' => $allBudgets, // The full (unpaginated) list
            'summary' => $summary,
            'charts'  => $charts,
            'filters' => $request->only(['status', 'start_date', 'end_date']),
        ];

        // ============================================================
        // 4️⃣ Return Report View
        // ============================================================
        return view('budgets.report', $viewData);
    }

    public function bulkAction(Request $request)
{
    // ============================================================
    // 1️⃣ Authorization
    // ============================================================
    $this->authorize('deleteAny', Budget::class); 
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
    $this->authorize('create', Budget::class); 
    // Only admins can access the create budget form.

    // ============================================================
    // 2️⃣ Fetch Required Data
    // ============================================================
    $departments = Department::all();
    $projects = Projects::orderBy('name')->get();
    // Retrieve all departments for the department dropdown list.

    // ============================================================
    // 3️⃣ Return View
    // ============================================================
    return view('budgets.create', compact('departments', 'projects'));
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
    $this->authorize('create', Budget::class); 
    // Only admins can create new budgets.

    // ============================================================
    // 2️⃣ Validation
    // ============================================================
    $validated = $request->validate([
        'department_id' => 'required|exists:departments,id',
        'project_id'    => 'nullable|exists:projects,id',
        'total_amount'  => 'required|numeric|min:0',
        'period_start'  => 'required|date',
        'period_end'    => 'required|date|after_or_equal:period_start',
    ]);

    // ============================================================
    // 3️⃣ Create Budget Record
    // ============================================================
    Budget::create($validated + ['created_by' => Auth::id()]);
    
    $message = 'Budget submitted for approval.';
    if (Auth::user()->hasRole('Admin')) {
        $data['status'] = 'active'; // Admins can auto-approve
        $message = 'Budget created and activated successfully.';
    }

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
    $this->authorize('view', $budget);
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
    $this->authorize('update', $budget);
    // ============================================================
    // 2️⃣ Fetch Required Data
    // ============================================================
    $departments = Department::all();

    // ============================================================
    // 3️⃣ Return View
    // ============================================================
    return view('budgets.edit', compact('budget', 'departments'));
}
public function approve(Budget $budget)
{
    // ============================================================
    // 1️⃣ Authorization
    // ============================================================
    $this->authorize('approve', $budget);
    // ============================================================
    // 2️⃣ Update Budget Status
    // ============================================================
    $budget->update(['status' => 'active']);
    // ============================================================
    // 3️⃣ Redirect with Success Message
    // ============================================================
    return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget approved successfully.');
}
public function reject(Budget $budget)
{
    // You could also use a Policy check here
    // $this->authorize('reject', $budget);

    // We'll just delete the rejected budget.
    // You could also set its status to 'rejected' if you prefer.
    $budget->delete();

    // Optional: Notify the manager who created it

    return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget rejected and removed.');
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
    $this->authorize('update', $budget);

    // ============================================================
    // 2️⃣ Validation
    // ============================================================
    $validated = $request->validate([
        'department_id' => 'required|exists:departments,id',
        'total_amount' => 'required|numeric|min:0',
        'period_start' => 'required|date',
        'period_end'   => 'required|date|after_or_equal:period_start',
        'status'       => 'required|string|in:active,closed,pending',
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

    // ... ALL OTHER METHODS (bulkAction, create, store, show, etc.)
    // ... remain exactly the same as they were.
    // ...
    // ...
    
}