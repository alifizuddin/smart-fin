<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\User;
use App\Services\BudgetService;
use Illuminate\Support\Facades\DB;

#[Layout('layouts.app')]
class Reporting extends Component
{
    public $stats = [];
    public $expenseTrends = [];
    public $expensesByCategory = [];
    public $recentActivities = [];

    public function mount()
    {
        $this->loadData();
    }

    public function exportReport($type = 'data')
    {
        if ($type === 'budget') {
            $this->authorize('viewAny', Budget::class);
            
            // Get the budget service
            $budgetService = app(\App\Services\BudgetService::class);
            
            // Get all budgets (filtered by current request parameters if needed)
            $request = request();
            $allBudgets = $budgetService->getAllFilteredBudgets($request);
            
            // Get summary and charts data
            $summary = $budgetService->getBudgetSummary($allBudgets);
            $charts = $budgetService->getBudgetChartData($allBudgets);
            
            // Prepare data for the view
            $viewData = [
                'budgets' => $allBudgets,
                'summary' => $summary,
                'charts' => $charts,
                'filters' => $request->only(['status', 'start_date', 'end_date']),
                'export' => true
            ];
            
            // Generate PDF using the report view
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('budgets.report', $viewData);
            
            // Return the PDF as a download
            return response()->streamDownload(function() use ($pdf) {
                echo $pdf->output();
            }, 'budget-report-' . now()->format('Y-m-d') . '.pdf');
        } else {
            // Existing export functionality
            $budgetService = app(BudgetService::class);
            $allBudgets = $budgetService->getAllFilteredBudgets(request());
            $summary = $budgetService->getBudgetSummary($allBudgets);
            $charts = $budgetService->getBudgetChartData($allBudgets);
            
            return view('budgets.report', [
                'budgets' => $allBudgets,
                'summary' => $summary,
                'charts' => $charts,
                'filters' => request()->only(['status', 'start_date', 'end_date']),
                'export' => true
            ]);
        }
    }

    public function loadData()
    {
        // Get basic statistics
        $this->stats = [
            'totalExpenses' => Expense::where('status', 'approved')->sum('amount'),
            'totalBudgets' => Budget::sum('total_amount'),
            'totalUsers' => User::count(),
            'pendingApprovals' => Expense::where('status', 'pending')->count(),
        ];

        // Get expense trends (last 6 months)
        $this->expenseTrends = Expense::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('SUM(amount) as total')
        )
        ->where('created_at', '>=', now()->subMonths(6))
        ->where('status', 'approved')
        ->groupBy('month')
        ->orderBy('month')
        ->get()
        ->toArray();

        // Get expenses by category
        $this->expensesByCategory = Expense::select(
            'category',
            DB::raw('SUM(amount) as total')
        )
        ->where('status', 'approved')
        ->groupBy('category')
        ->orderBy('total', 'desc')
        ->get()
        ->toArray();

        // Get recent activities
        $this->recentActivities = Expense::with('user', 'budget.department')
            ->where('status', 'approved')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.reporting', [
            'stats' => $this->stats,
            'expenseTrends' => $this->expenseTrends,
            'expensesByCategory' => $this->expensesByCategory,
            'recentActivities' => $this->recentActivities
        ]);
    }
}
