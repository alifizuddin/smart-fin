<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Services\ExpenseCategorizationService;
use App\Models\Expense;
use App\Models\User;
use App\Notifications\ExpenseApproved;
use App\Notifications\NewExpenseSubmission;
use App\Events\ExpenseCreated;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Carbon\Carbon;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the authenticated user's expenses.
     */
    public function myExpenses(Request $request)
    {
        $user = Auth::user();
        
        $query = $user->expenses()
            ->with(['budget', 'department'])
            ->latest('created_at');
            
        // Add search functionality
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function($q) use ($searchTerm) {
                $q->where('category', 'like', "%{$searchTerm}%")
                ->orWhere('description', 'like', "%{$searchTerm}%")
                ->orWhere('status', 'like', "%{$searchTerm}%");
            });
        }
        
        // Add status filter
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }
        
        $expenses = $query->paginate(10);
        
        // Calculate totals
        $totalExpenses = $user->expenses()->sum('amount');
        $pendingCount = $user->expenses()->where('status', 'pending')->count();
        $approvedCount = $user->expenses()->where('status', 'approved')->count();
        $rejectedCount = $user->expenses()->where('status', 'rejected')->count();
        
        return view('expenses.my-expenses', [
            'expenses' => $expenses,
            'totalExpenses' => $totalExpenses,
            'totalPending' => $pendingCount,
            'totalApproved' => $approvedCount,
            'totalRejected' => $rejectedCount,
            'statuses' => ['pending', 'approved', 'rejected']
        ]);
    }
    public function analyzeReceipt(Request $request)
    {
        $request->validate([
            'receipt' => 'required|file|mimes:jpg,png,pdf|max:10240',
        ]);

        // Send to N8N
        $n8nWebhookUrl = 'http://n8n:5678/webhook/process-receipt';

        try {
            $response = Http::timeout(30)
                ->attach(
                    'receipt',
                    file_get_contents($request->file('receipt')),
                    $request->file('receipt')->getClientOriginalName()
                )->post($n8nWebhookUrl);

            if ($response->successful()) {
                $data = $response->json();
                
                // Return structured JSON for the frontend
                return response()->json([
                    'success' => true,
                    'amount' => $data['data']['amount'] ?? null,
                    'date' => isset($data['data']['date']) ? Carbon::parse($data['data']['date'])->format('Y-m-d') : now()->format('Y-m-d'),
                    'category' => $data['category'] ?? 'Uncategorized',
                    'description' => $data['description'] ?? '',
                ]);
            }

            return response()->json(['success' => false, 'message' => 'N8N processing failed'], 500);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
    public function index()
    {
        //
    }
    public function quickStore(Request $request)
    {
        // 1. Get the authenticated user and their department
        $user = Auth::user();
        if (!$user->department_id) {
            return response()->json([
                'message' => 'Your user account is not associated with a department. Please contact admin.'
            ], 422);
        }

        // 2. Validate the input
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'receipt' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120', // 5MB max
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // 3. Find all active budgets for the user's department
        $activeBudgets = Budget::where('department_id', $user->department_id)
            ->where('status', 'active')
            ->where('period_start', '<=', Carbon::now())
            ->where('period_end', '>=', Carbon::now())
            ->get();

        if ($activeBudgets->isEmpty()) {
            return response()->json([
                'message' => 'No active budget found for your department at this time. Please contact your manager.'
            ], 422);
        }

        // 4. Find a budget with sufficient remaining balance
        $selectedBudget = null;
        $expenseAmount = $validated['amount'];

        foreach ($activeBudgets as $budget) {
            // Calculate total spent (approved + pending)
            $totalSpent = Expense::where('budget_id', $budget->id)
                ->where('status', 'approved')
                ->sum('amount');
            
            $remainingBalance = $budget->amount - $totalSpent;

            // Check if this budget has enough remaining balance
            if ($remainingBalance >= $expenseAmount) {
                $selectedBudget = $budget;
                break;
            }
        }

        if (!$selectedBudget) {
            return response()->json([
                'message' => 'No budget with sufficient remaining balance found. Please contact your manager or reduce the expense amount.'
            ], 422);
        }

        // 5. Handle file upload
        try {
            $path = $request->file('receipt')->store('receipts', 'public');

            // 6. Create the expense
            $expense = Expense::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'budget_id' => $selectedBudget->id,
                'amount' => $validated['amount'],
                'category' => 'Uncategorized',
                'description' => $validated['description'],
                'receipt_path' => $path,
                'transaction_date' => Carbon::now()->format('Y-m-d'),
                'payment_method' => 'Not specified',
                'status' => 'pending',
            ]);

            // Notify managers in the same department
            $managers = User::role('Manager')
                ->where('department_id', $user->department_id)
                ->get();

            \Log::info('Found managers to notify', [
                'count' => $managers->count(),
                'department_id' => $user->department_id,
                'managers' => $managers->pluck('id')->toArray()
            ]);

            if ($managers->isNotEmpty()) {
                try {
                    Notification::send($managers, new NewExpenseSubmission($expense));
                    \Log::info('Notification sent successfully');
                } catch (\Exception $e) {
                    \Log::error('Failed to send notification', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }

            return response()->json([
                'message' => 'Expense submitted successfully! Automatically assigned to Budget #' . $selectedBudget->id
            ], 201);
        } catch (\Exception $e) {
            // Log the error
            logger()->error('Expense Quick Store Failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
    }
    public function approvalsIndex(Request $request)
    {
        $user = Auth::user();
        if ($user->hasRole('Manager')) {
            $approvedExpenses = Expense::where('department_id', $user->department_id)
                ->where('status', 'approved')
                ->with('user', 'budget')
                ->latest()
                ->paginate(10, ['*'], 'approved_page');
        }

        if (!$user->department_id) {
            return redirect()->route('dashboard')
                ->with('error', 'You must be associated with a department to view expense approvals.');
        }

        $query = Expense::where('department_id', $user->department_id)
            ->where('status', 'pending')
            ->with(['user', 'budget']);

        // Handle search
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('category', 'like', "%{$searchTerm}%")
                ->orWhereHas('user', function ($userQuery) use ($searchTerm) {
                    $userQuery->where('name', 'like', "%{$searchTerm}%");
                });
            });
        }

        $pendingExpenses = $query->orderBy('created_at', 'asc')->paginate(10, ['*'], 'pending_page');

        // Calculate stats for the department
        $totalExpenses = Expense::where('department_id', $user->department_id)->sum('amount');
        $totalPending = Expense::where('department_id', $user->department_id)
            ->where('status', 'pending')
            ->count();
        $totalApproved = Expense::where('department_id', $user->department_id)
            ->where('status', 'approved')
            ->count();
        $totalRejected = Expense::where('department_id', $user->department_id)
            ->where('status', 'rejected')
            ->count();

        return view('expenses.approval-index', compact('pendingExpenses','approvedExpenses', 'totalExpenses', 'totalPending', 'totalApproved', 'totalRejected'));
    }

    public function approve(Expense $expense)
    {
        // 1. Authorize: Ensure manager is in the same department
        if (Auth::user()->department_id !== $expense->department_id) {
            abort(403, 'You can only manage expenses for your own department.');
        }

        // 2. Check if already approved
        if ($expense->status === 'approved') {
            return redirect()->back()->with('info', 'Expense is already approved.');
        }

        $budget = $expense->budget;

        if (!$budget) {
            return redirect()->back()->with('error', 'Associated budget could not be found.');
        }

        try {
            // 3. Use a transaction to ensure data integrity
            DB::transaction(function () use ($expense, $budget) {
                // 4. Update expense status
                $expense->update(['status' => 'approved']);

                // 5. Deduct amount from the budget
                $budget->increment('used_amount', $expense->amount);
            });
            
            try {
                $expense->user->notify(new ExpenseApproved($expense));
            } catch (\Exception $e) {
                // Log notification failure but don't fail the approval transaction
                \Log::error('Failed to send approval notification', ['error' => $e->getMessage()]);
            }

            return redirect()->back()->with('success', 'Expense approved and budget updated.');
        } catch (\Exception $e) {
            logger()->error('Expense approval failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while approving the expense.');
        }
    }
    public function reject(Expense $expense)
    {
        // 1. Authorize: Ensure manager is in the same department
        if (Auth::user()->department_id !== $expense->department_id) {
            abort(403, 'You can only manage expenses for your own department.');
        }

        $originalStatus = $expense->status;
        $budget = $expense->budget;

        try {
            // 2. Use a transaction
            DB::transaction(function () use ($expense, $budget, $originalStatus) {
                // 3. Update expense status
                $expense->update(['status' => 'rejected']);

                // 4. If it was *previously* approved, refund the budget
                if ($originalStatus === 'approved' && $budget) {
                    $budget->decrement('used_amount', $expense->amount);
                }
            });

            return redirect()->back()->with('success', 'Expense rejected.');
        } catch (\Exception $e) {
            logger()->error('Expense rejection failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'An error occurred while rejecting the expense.');
        }
    }

    public function create()
{
    $user = Auth::user();

    // 1. Attempt to find a currently active budget (for default selection or context)
    // We keep this query, but we WON'T redirect if it returns null.
    $activeBudget = Budget::where('department_id', $user->department_id)
        ->where('status', 'active')
        ->where('period_start', '<=', now())
        ->where('period_end', '>=', now())
        ->first();

    // [DELETED] The redirect block that was here is removed.

    // 2. Get list of all valid budgets for the dropdown
    $allActiveBudgets = Budget::where('department_id', $user->department_id)    
        ->where('status', 'active')
        ->where('period_end', '>=', now()) // Show budgets that haven't expired yet
        ->with(['expenses' => function ($query) {
            $query->whereIn('status', ['approved', 'pending']);
        }])
        ->orderBy('period_end', 'asc')
        ->get();

    return view('expenses.create', compact('activeBudget', 'allActiveBudgets'));
}

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // 1. Validate inputs (including the ones confirmed in the modal)
        $validated = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'receipt' => 'required|file|mimes:jpg,png,pdf|max:10240',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'required|date',
            'category' => 'required|string',
            'description' => 'nullable|string',
        ]);

        // 2. Upload File
        $path = $request->file('receipt')->store('receipts', 'public');

        // 3. Create Expense (No AI call here, we use the request data)
        try {
            $expense = Expense::create([
                'budget_id'        => $validated['budget_id'],
                'user_id'          => $user->id,
                'department_id'    => $user->department_id, // Ensure user has department
                'receipt_path'     => $path,
                'status'           => 'pending',
                'amount'           => $validated['amount'],
                'transaction_date' => $validated['transaction_date'],
                'category'         => $validated['category'],
                'description'      => $validated['description'],
                'payment_method'   => 'Not specified',
            ]);

            // Notify Managers
            $managers = User::role('Manager')
                ->where('department_id', $user->department_id)
                ->get();

            if ($managers->isNotEmpty()) {
                Notification::send($managers, new NewExpenseSubmission($expense));
            }

            // Redirect back to create page with success
            return redirect()->route('expenses.create')->with(
                'success', 
                'Expense submitted successfully! Amount: RM' . number_format($expense->amount, 2)
            );

        } catch (\Exception $e) {
            logger()->error('Expense creation failed', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Failed to save expense.')->withInput();
        }
    }


    public function show(string $id)
    {
        //
    }


    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $expense = Expense::findOrFail($id);
        $expense->delete();

        return redirect()->back()->with('success', 'Expense deleted successfully.');
    }
    private function getCategoryFromImage(string $imageAbsolutePath): string
    {

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
}