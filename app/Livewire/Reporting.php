<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Expense;
use App\Models\Budget;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

#[Layout('layouts.app')]
class Reporting extends Component
{
    public $stats = [];
    public $expenseTrends = [];
    public $expensesByCategory = [];
    public $recentActivities = [];

    // Define the available reports for the UI
    public $availableReports = [
        [
            'id' => 'budget_variance',
            'title' => 'Budget Variance Report',
            'description' => 'Compare actual spending against allocated budget amounts by department.',
            'icon' => 'chart-pie'
        ],
        [
            'id' => 'project_financials',
            'title' => 'Project Cost Report',
            'description' => 'Detailed breakdown of expenses and budget usage per project.',
            'icon' => 'briefcase'
        ],
        [
            'id' => 'expense_register',
            'title' => 'Detailed Expense Register',
            'description' => 'Full list of all approved transactions for auditing purposes.',
            'icon' => 'table'
        ],
        [
            'id' => 'department_spending',
            'title' => 'Department Spending Analysis',
            'description' => 'Aggregate view of expenses grouped by department.',
            'icon' => 'building-office'
        ]
    ];

    public function mount()
    {
        $this->loadData();
    }

    public function generateReport($reportId)
    {
        switch ($reportId) {
            case 'budget_variance':
                return $this->exportBudgetVariance();
            case 'project_financials':
                return $this->exportProjectFinancials();
            case 'expense_register':
                return $this->exportExpenseRegister();
            case 'department_spending':
                return $this->exportDepartmentSpending();
            default:
                // Handle unknown report types if necessary
                return;
        }
    }

    public function exportBudgetVariance()
    {
        // Fetch budgets with related department and project data
        $budgets = Budget::with(['department', 'project'])->get();

        $pdf = Pdf::loadView('reports.budget-variance', [
            'budgets' => $budgets,
            'start_date' => null, // You can add date filters later
            'end_date' => now()->format('Y-m-d'),
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Budget_Variance_Report_' . now()->format('Ymd') . '.pdf');
    }

    public function exportProjectFinancials()
    {
        // Fetch projects with their budgets
        $projects = Projects::with(['budgets', 'department'])->get();

        $pdf = Pdf::loadView('reports.project-financials', [
            'projects' => $projects
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Project_Financials_' . now()->format('Ymd') . '.pdf');
    }

    public function exportExpenseRegister()
    {
        // Fetch all approved expenses with details
        $expenses = Expense::with(['user', 'department', 'budget'])
            ->where('status', 'approved')
            ->orderBy('transaction_date', 'desc')
            ->get();

        $pdf = Pdf::loadView('reports.expense-register', [
            'expenses' => $expenses
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Expense_Register_' . now()->format('Ymd') . '.pdf');
    }

    public function exportDepartmentSpending()
    {
        // Fetch departments with their budgets and expenses
        $departments = Department::with(['budgets', 'expenses'])->get();

        $pdf = Pdf::loadView('reports.department-spending', [
            'departments' => $departments
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'Department_Spending_' . now()->format('Ymd') . '.pdf');
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
    
    // Existing exportReport function can be kept if needed for the top button
    // or you can refactor it to use the new system.
    public function exportReport($type = 'data') 
    {
         // ... (Keep your existing logic here if you want to support the "old" buttons)
         return;
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