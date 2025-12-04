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
use App\Notifications\NewAiAlert;

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
            // Get user's budgets first
            $userBudgets = Budget::where('department_id', $user->department_id)
                ->where('status', 'active')
                ->where('period_end', '>=', now())
                ->orderBy('period_end', 'asc')
                ->get();

            // Check for a specific budget from the request
            $requestedBudgetId = $request->query('budget_id');
            $activeBudget = null;

            if ($requestedBudgetId) {
                $activeBudget = $userBudgets->firstWhere('id', $requestedBudgetId);
            }

            // If no budget was requested, or the requested one wasn't found, default to the first one.
            if (!$activeBudget && $userBudgets->isNotEmpty()) {
                $activeBudget = $userBudgets->first();
            }

            // Get expenses for the selected budget
            $userExpensesQuery = Expense::where('user_id', $user->id)
                ->with('budget')
                ->latest();

            if ($activeBudget) {
                $userExpensesQuery->where('budget_id', $activeBudget->id);
            }

            $userExpenses = $userExpensesQuery->get();

            $totalDepartmentBudget = $userBudgets->sum('total_amount');

        } 
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