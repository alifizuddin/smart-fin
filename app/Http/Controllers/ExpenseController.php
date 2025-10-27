<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use App\Services\ExpenseCategorizationService;
use App\Models\Expense;
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
            'receipt'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'description' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $validated = $validator->validated();

        // 3. Find an active budget for the user's department
        $activeBudget = Budget::where('department_id', $user->department_id)
            ->where('status', 'active')
            ->where('period_start', '<=', Carbon::now())
            ->where('period_end', '>=', Carbon::now())
            ->first();

        if (!$activeBudget) {
            return response()->json([
                'message' => 'No active budget found for your department at this time. Please contact your manager.'
            ], 422);
        }

        // 4. Create the expense
        try {
            // **FIXED RUNTIME BUG**: Added a default 'Uncategorized' value.
            // You were calling $validated['category'] but 'category' was not in your validator.
            Expense::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'budget_id' => $activeBudget->id,
                'amount' => $validated['amount'],
                'category' => 'Uncategorized', // <-- FIX
                'description' => $validated['description'],
                'status' => 'pending', // Default status
            ]);

            return response()->json([
                'message' => 'Expense submitted successfully!'
            ], 201);
        } catch (\Exception $e) {
            // Log the error
            logger()->error('Expense Quick Store Failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'An unexpected error occurred. Please try again later.'
            ], 500);
        }
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
        // Get the single active budget for the current period from the user's department
        $user = Auth::user();
        $activeBudget = Budget::where('department_id', $user->department_id)
            ->where('status', 'active')
            ->where('period_start', '<=', now())
            ->where('period_end', '>=', now())
            ->first();

        if (!$activeBudget) {
            return redirect()->route('dashboard')
                ->with('error', 'No active budget found for your department. Please contact your manager.');
        }

        return view('expenses.create', compact('activeBudget'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request)
    {
        // 1. Validate only the required fields
        $validated = $request->validate([
            'budget_id' => 'required|exists:budgets,id',
            'receipt'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // 2. Handle the file upload
        $path = $request->file('receipt')->store('receipts', 'public');

        try {
            // 3. Process receipt with Python API
            $response = Http::timeout(30) // Increased timeout for OCR processing
                ->attach(
                    'receipt',
                    file_get_contents($request->file('receipt')),
                    $request->file('receipt')->getClientOriginalName()
                )->post('http://127.0.0.1:5010/process-receipt');

            if (!$response->successful()) {
                logger()->error('Receipt processing failed', ['response' => $response->body()]);
                return redirect()->back()
                    ->with('error', 'Could not process receipt. Please try again or contact support.')
                    ->withInput();
            }

            $ai_data = $response->json();

            // **FIXED SYNTAX ERROR**: Removed a broken, duplicated block of code that was here.

            // 4. Prepare data array for creation using AI-extracted data
            $user = Auth::user();

            // Convert amount to proper decimal
            $amount = $ai_data['data']['amount'] ?? null;
            if (!is_numeric($amount) || $amount <= 0) {
                return redirect()->back()
                    ->with('error', 'Could not detect a valid amount from the receipt. Please make sure the amount is clearly visible.')
                    ->withInput();
            }

            // Parse and validate date
            $transactionDate = $ai_data['data']['date'] ?? null;
            try {
                $parsedDate = $transactionDate ? Carbon::parse($transactionDate) : now();
            } catch (\Exception $e) {
                $parsedDate = now();
            }

            $data = [
                'budget_id'        => $validated['budget_id'],
                'user_id'          => $user->id,
                'department_id'    => $user->department_id,
                'receipt_path'     => $path,
                'status'           => 'pending',

                // AI-extracted data with validation
                'amount'           => $amount,
                'transaction_date' => $parsedDate->format('Y-m-d'),
                'payment_method'   => $ai_data['data']['payment_method'] ?? 'Not specified',
                'category'         => $ai_data['category'] ?? 'Uncategorized',
                'description'      => $ai_data['description'] ?? 'Receipt processed automatically',
            ];

            // 5. Create the expense
            try {
                $expense = Expense::create($data);

                return redirect()->route('dashboard')->with(
                    'success',
                    'Expense submitted successfully! ' .
                        'Amount: $' . number_format($expense->amount, 2) . ', ' .
                        'Category: ' . $expense->category
                );
            } catch (\Exception $e) {
                logger()->error('Expense creation failed', ['error' => $e->getMessage(), 'data' => $data]);
                return redirect()->back()
                    ->with('error', 'Failed to submit expense. Please try again or contact support.')
                    ->withInput();
            }

            // **FIXED SYNTAX ERROR**: Removed the redundant Expense::create and redirect calls that were here.
            // The logic is already handled inside the try/catch block above.

        } catch (\Exception $e) { // This catch block is for the Http::post call
            logger()->error('Receipt processing HTTP call failed', ['error' => $e->getMessage()]);
            return redirect()->back()
                ->with('error', 'Could not connect to the receipt processing service. Please try again later.')
                ->withInput();
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
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
}