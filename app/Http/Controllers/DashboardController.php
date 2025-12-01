<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\Expense; 
use App\Models\Budget;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use App\Notifications\NewAiAlert; // Ensure this is imported if you use the class name

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $pendingExpenses = collect(); // Default to empty collection
        $activeBudget = null;
        $userExpenses = collect();
        $aiSuggestions = [];
        $userBudgets = collect();
        $totalDepartmentBudget = 0;
        $spendingByCategory = collect();
        $anomalyAlerts = collect();

        // 2. Fetch data based on user role
        if ($user->hasRole('Manager')) {
        // ... existing expenses/spending logic ...
        $pendingExpenses = Expense::where('department_id', $user->department_id)
            ->where('status', 'pending')
            ->with('user', 'budget') 
            ->latest()
            ->get();

        $spendingByCategory = Expense::where('department_id', $user->department_id)
            ->where('status', 'approved')
            ->groupBy('category')
            ->select('category', DB::raw('SUM(amount) as total_amount'))
            ->orderBy('total_amount', 'desc')
            ->get();

        $anomalyAlerts = $user->unreadNotifications()
            ->where('type', 'App\Notifications\NewAiAlert')
            ->latest()
            ->take(5) 
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id ,
                    'message' => $notification->data['message'] ?? 'No detail provided',
                    'severity' => $notification->data['severity'] ?? 'info',
                    'date' => $notification->created_at->diffForHumans(),
                ];
            });
        // -------------------------------------------------------
    }

        if ($user->hasRole('Staff')) {
            $userExpenses = Expense::where('user_id', $user->id)
                ->with('budget')
                ->latest()
                ->take(5)
                ->get();

            $userBudgets = Budget::where('department_id', $user->department_id)
                ->where('status', 'active')
                ->where('period_end', '>=', now())
                ->orderBy('period_end', 'asc')
                ->get();

            // Check for a specific budget from the request
            $requestedBudgetId = $request->query('budget_id');
            $activeBudget = null;

            if ($requestedBudgetId) {
                // Find the requested budget within the user's accessible budgets
                $activeBudget = $userBudgets->firstWhere('id', $requestedBudgetId);
            }

            // If no budget was requested, or the requested one wasn't found, default to the first one.
            if (!$activeBudget && $userBudgets->isNotEmpty()) {
                $activeBudget = $userBudgets->first();
            }

            $totalDepartmentBudget = $userBudgets->sum('total_amount');
            
            // --- AI Logic should be INSIDE the Staff block ---
            $aiServiceUrl = config('services.ai_suggester.url'); 

            if ($aiServiceUrl) {
                try {
                    // 2. Prepare data for the AI
                    $budgetData = [
                        'total_budget' => $activeBudget->total_amount ?? 0,
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
            'userExpenses' => $userExpenses,
            'aiSuggestions' => $aiSuggestions,
            'userBudgets' => $userBudgets,
            'totalDepartmentBudget' => $totalDepartmentBudget,
            'spendingByCategory' => $spendingByCategory,
            'anomalyAlerts' => $anomalyAlerts,
        ]);
    }
    public function markNotificationRead($id)
    {
        $notification = auth()->user()->unreadNotifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
        }

        return back()->with('success', 'Alert dismissed.');
    }
}