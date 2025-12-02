<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\User;
use App\Notifications\NewAiAlert; // We will create this next
use Illuminate\Support\Facades\Log;

class N8nController extends Controller
{
    /**
     * Endpoint for n8n to fetch data for analysis.
     */
    public function getData(Request $request)
    {
        // 1. Fetch recent expenses (last 30 days)
        $recentExpenses = Expense::with('user', 'budget')
            ->where('created_at', '>=', now()->subDays(30))
            ->get()
            ->map(function ($expense) {
                return [
                    'id' => $expense->id,
                    'date' => $expense->created_at->toDateString(),
                    'amount' => $expense->amount,
                    'category' => $expense->category,
                    'user' => $expense->user->name,
                    'user_id' => $expense->user_id,
                    'budget' => $expense->budget->name ?? 'N/A',
                ];
            });

        // 2. Fetch active budgets and their usage
        $budgets = Budget::where('status', 'active')->with('expenses')->get()->map(function ($budget) {
            $spent = $budget->expenses->where('status', 'approved')->sum('amount');
            return [
                'name' => $budget->name,
                'total' => $budget->total_amount,
                'spent' => $spent,
                'remaining' => $budget->total_amount - $spent,
                'percentage' => $budget->total_amount > 0 ? round(($spent / $budget->total_amount) * 100, 1) : 0
            ];
        });

        return response()->json([
            'expenses' => $recentExpenses,
            'budgets' => $budgets,
            'context' => 'Analyze this data for spending anomalies, budget overruns, or unusual frequency of expenses.'
        ]);
    }

    /**
     * Endpoint for n8n to push an AI alert.
     */
    public function storeAlert(Request $request)
    {
        $validated = $request->validate([
            'message' => 'required|string',
            'severity' => 'required|in:info,warning,critical',
            'details' => 'nullable|string'
        ]);

        // Find the Manager (or specific user) to notify
        // For now, we'll notify the first user with the Manager role
        $manager = User::role('Manager')->first();

        if ($manager) {
            // Save to database notifications
            $manager->notify(new NewAiAlert($validated));
            return response()->json(['status' => 'Alert received and stored']);
        }

        return response()->json(['status' => 'No manager found'], 404);
    }
}