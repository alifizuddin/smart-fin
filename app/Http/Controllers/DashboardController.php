<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Expense; 
use App\Models\Budget;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        $pendingExpenses = collect(); // Default to empty collection
        $activeBudget = null;
        $userExpenses = collect();
        $aiSuggestions = [];

        // 2. Fetch data based on user role
        if ($user->hasRole('Manager')) {
            // Managers see pending expenses for their department
            $pendingExpenses = Expense::where('department_id', $user->department_id)
                ->where('status', 'pending')
                ->with('user', 'budget') // Load relations
                ->latest()
                ->get();
        }

        if ($user->hasRole('Staff')) {
            // Staff see their own active budget
            $activeBudget = Budget::where('department_id', $user->department_id)
                ->where('status', 'active')
                ->where('period_start', '<=', now())
                ->where('period_end', '>=', now())
                ->first();
                
            $userExpenses = Expense::where('user_id', $user->id)
                ->with('budget')
                ->latest()
                ->get();
            
            // --- AI Logic should be INSIDE the Staff block ---
            $aiServiceUrl = config('services.ai_suggester.url'); 

            if ($aiServiceUrl) {
                try {
                    // 2. Prepare data for the AI
                    $budgetData = [
                        'total_budget' => $activeBudget->amount ?? 0,
                        'spent' => $userExpenses->where('status', 'approved')->sum('amount'),
                    ];

                    $expenseData = $userExpenses->map(fn($exp) => [
                        'category' => $exp->category,
                        'amount' => $exp->amount,
                        'status' => $exp->status,
                        'date' => $exp->created_at->toIso8601String(),
                    ]);

                    // 3. Call the AI service
                    $response = Http::timeout(15)->post($aiServiceUrl, [
                        'user_id' => $user->id,
                        'budget' => $budgetData,
                        'expenses' => $expenseData,
                    ]);

                    // 4. Get the suggestion and add it to the array
                    if ($response->successful()) {
                    $suggestionText = trim($response->body());

                    if (!empty($suggestionText) && strcasecmp($suggestionText, 'All clear.') !== 0) {
                        $aiSuggestions[] = $suggestionText;
                    }
                }

                } catch (\Exception $e) {
                    // 5. If AI fails, log the error and do nothing
                    Log::error('AI Suggestion Service failed: ' . $e->getMessage());
                }
            }
            // --- End of AI Logic ---

        } // <-- The 'if ($user->hasRole('Staff'))' block ends HERE
        
        // 3. Pass data to the view
        return view('dashboard', [
            'pendingExpenses' => $pendingExpenses,
            'activeBudget' => $activeBudget,
            'userExpenses' => $userExpenses, // Changed from 'userExpenses ?? collect()'
            'aiSuggestions' => $aiSuggestions
        ]);
    }
}