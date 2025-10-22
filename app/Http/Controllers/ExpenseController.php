<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Budget;
use App\Models\Expense;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Services\ExpenseCategorizationService;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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
            'category' => 'required|string|max:255',
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
            Expense::create([
                'user_id' => $user->id,
                'department_id' => $user->department_id,
                'budget_id' => $activeBudget->id,
                'amount' => $validated['amount'],
                'category' => $validated['category'],
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

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Get active budgets from the user's department to show in a dropdown
        $user = Auth::user();
        $budgets = Budget::where('department_id', $user->department_id)
                         ->where('status', 'active')
                         ->orderBy('period_start', 'desc')
                         ->get();

        return view('expenses.create', compact('budgets'));
    }

    /**
     * Store a newly created expense in storage.
     */
    public function store(Request $request, ExpenseCategorizationService $categorizationService)
    {
        // 1. Validate the incoming request
        $validated = $request->validate([
            // ... (all your validation rules)
            'budget_id'        => 'required|exists:budgets,id',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'required|string|max:1000',
            'transaction_date' => 'required|date',
            'payment_method'   => 'required|string|max:100',
            'receipt'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        // 2. Handle the file upload
        $path = $request->file('receipt')->store('receipts', 'public');
        
        // 3. --- NEW AI CATEGORIZATION STEP ---
        $category = $categorizationService->categorizeWithKeywords($validated['description']);
        
        // -------------------------------------

        // 4. Prepare data array for creation
        $user = Auth::user();
        $data = $validated + [
            'user_id'       => $user->id,
            'department_id' => $user->department_id,
            'receipt_path'  => $path,
            'status'        => 'pending',
            'category'      => $category, // --- USE THE AI-GENERATED CATEGORY ---
        ];

        // 5. Create the expense
        Expense::create($data);

        // 6. Redirect back with a success message
        return redirect()->route('dashboard')->with('success', 'Expense submitted for approval.');
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
    public function destroy(string $id)
    {
        //
    }
}
