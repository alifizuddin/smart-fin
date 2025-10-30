<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Models\Department;
use App\Models\Projects;
use App\Models\Expense;
use Illuminate\Http\Request;
use App\Notifications\BudgetStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;
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
    
    public function showJson(Budget $budget)
    {
    
        return response()->json($budget->load('department'));
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

public function storeExpense(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
            'receipt_image' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Validate the image
            'description' => 'nullable|string',
        ]);

        // 1. Store the uploaded image temporarily
        // This saves it in 'storage/app/temp_receipts/some_random_name.jpg'
        $path = $request->file('receipt_image')->store('temp_receipts', 'local');
        $absolutePath = storage_path('app/' . $path);

        $description = $request->input('description');
        $predictedCategory = 'Uncategorized';

        try {
            // 2. Call the Python script to get the category
            $predictedCategory = $this->getCategoryFromImage($absolutePath);
            
            // If the user didn't provide a description,
            // you could try to get the first line of the receipt as a description
            if (empty($description)) {
                 // This is a simple (and optional) improvement
                 $description = "Receipt " . $request->file('receipt_image')->getClientOriginalName();
            }

        } catch (\Exception $e) {
            // Log the error
            \Log::error('Receipt prediction failed: ' . $e->getMessage());
        }

        // 3. Clean up the temporary file
        Storage::disk('local')->delete($path);
        
        // 4. Create the expense in the database
        Expense::create([
            'description' => $description,
            'amount' => $request->input('amount'),
            'category' => $predictedCategory, // Use the predicted category!
            'user_id' => auth()->id(),
            // ... other fields ...
        ]);

        return redirect()->back()->with('success', 'Expense added with category: ' . $predictedCategory);
    }
    /**
     * Calls the Python script to predict a category from an image file.
     *
     * @param string $imageAbsolutePath
     * @return string
     */
    private function getCategoryFromImage(string $imageAbsolutePath): string
    {
        // IMPORTANT: Use absolute paths for your Python script and model
        // e.g., base_path('ml/predict_from_image.py')
        $pythonScriptPath = base_path('ml/predict_from_image.py');

        // Set the working directory to where the model is, so the script can find it
        $process = new Process(['python', $pythonScriptPath, $imageAbsolutePath]);
        $process->setWorkingDirectory(base_path('ml')); // Run from the 'ml' folder
        
        try {
            $process->run();

            if (!$process->isSuccessful()) {
                // Get error output from stderr
                throw new \Exception($process->getErrorOutput());
            }

            $predictedCategory = trim($process->getOutput());
            
            return $predictedCategory ?: 'Uncategorized (Script Error)';

        } catch (ProcessFailedException $exception) {
            \Log::error('NLP/OCR Prediction Failed: ' . $exception->getMessage());
            return 'Uncategorized (Process Failed)';
        }
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
    // Only admins/managers can create new budgets

    // ============================================================
    // 2️⃣ Validation
    // ============================================================
    $validated = $request->validate([
        'name' => 'required|string',
        'department_id' => 'required|exists:departments,id',
        'project_id'    => 'nullable|exists:projects,id',
        'total_amount'  => 'required|numeric|min:0',
        'period_start'  => 'required|date',
        'period_end'    => 'required|date|after_or_equal:period_start',
    ]);

    // ============================================================
    // 3️⃣ Prepare Data (FIXED)
    // ============================================================
    
    // Start with validated data and the creator
    $data = $validated + ['created_by' => Auth::id()];

    // Set default message for non-admins (who submit as 'pending')
    $message = 'Budget submitted for approval.';
    
    // Admins can auto-approve their own created budgets
    if (Auth::user()->hasRole('Admin')) {
        $data['status'] = 'active'; // Add 'active' status to the data array
        $message = 'Budget created and activated successfully.';
    }

    // ============================================================
    // 4️⃣ Create Budget Record (FIXED)
    // ============================================================
    Budget::create($data); // Create record with all prepared data

    // ============================================================
    // 5️⃣ Redirect with Success Message (FIXED)
    // ============================================================
    return redirect()
        ->route('budgets.index')
        ->with('success', $message); // Use the dynamic message
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
    
    $this->authorize('approve', $budget); // <-- COMMENT OUT THIS LINE
    
    // ============================================================
    // 2️⃣ Update Budget Status
    // ============================================================
    $budget->update(['status' => 'active']);
    // ============================================================
    // 3️⃣ Redirect with Success Message
    // ============================================================
    $creator = $budget->creator; // Get the user who made the budget
    if ($creator) {
        $message = "Your budget submission for {$budget->department->name} has been approved.";
        $creator->notify(new BudgetStatusUpdated($budget, $message, 'View Budgets'));
    }
    return redirect()
        ->route('budgets.index')
        ->with('success', 'Budget approved successfully.');
}
public function reject(Budget $budget)
{
    // You could also use a Policy check here
    $this->authorize('reject', $budget);

    // We'll just delete the rejected budget.
    // You could also set its status to 'rejected' if you prefer.
    $budget->update(['status' => 'rejected']);

    $creator = $budget->creator;
    if ($creator) {
        $message = "Your budget submission for {$budget->department->name} has been rejected.";
        // You could add a reason here later
        $creator->notify(new BudgetStatusUpdated($budget, $message, 'View Budgets'));
    }

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