<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingController extends Controller
{
    public function index()
    {
        // Get basic statistics
        $stats = [
            'totalExpenses' => Expense::where('status', 'approved')->sum('amount'),
            'totalBudgets' => Budget::sum('total_amount'),
            'totalUsers' => User::count(),
            'pendingApprovals' => Expense::where('status', 'pending')->count(),
        ];

        // Get expense trends (last 6 months)
        $expenseTrends = Expense::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(amount) as total')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->where('status', 'approved')
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        // Get expenses by category
        $expensesByCategory = Expense::select(
            'category',
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'approved')
        ->groupBy('category')
        ->orderBy('total', 'desc')
        ->get();

        // Get recent activities
        $recentActivities = Expense::with('user', 'budget.department')
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        return view('reporting.index', [
            'stats' => $stats,
            'expenseTrends' => $expenseTrends,
            'expensesByCategory' => $expensesByCategory,
            'recentActivities' => $recentActivities
        ]);
    }
}
