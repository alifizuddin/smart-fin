<?php

namespace App\Services;

use App\Models\Budget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection as SupportCollection;

class BudgetService
{
    /**
     * Get a paginated list of budgets based on filters and user roles.
     * This is for the main index page table.
     */
    public function getPaginatedBudgets(Request $request): LengthAwarePaginator
    {
        $query = $this->buildFilteredQuery($request);

        // Apply role-based visibility
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('budgets.department_id', Auth::user()->department_id);
        }

        // Apply sorting from the request
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'department') {
            $query->join('departments', 'budgets.department_id', '=', 'departments.id')
                  ->orderBy('departments.name', $sortOrder)
                  ->select('budgets.*'); // Avoid column conflicts
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        return $query->paginate(10)->appends($request->except('page'));
    }

    /**
     * Get ALL budgets (unpaginated) that match the filters and roles.
     * This is for calculating summaries and chart data.
     */
    public function getAllFilteredBudgets(Request $request): Collection
    {
        $query = $this->buildFilteredQuery($request);

        // Apply role-based visibility
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('budgets.department_id', Auth::user()->department_id);
        }

        return $query->get();
    }

    /**
     * Prepares the summary statistics block from a given collection of budgets.
     */
    public function getBudgetSummary(SupportCollection $budgets): array
    {
        $departmentGroups = $this->groupBudgetsByDepartment($budgets);
        $totalBudget = $departmentGroups->sum('total');
        $totalSpent  = $departmentGroups->sum('used');

        return [
            'totalBudget'      => $totalBudget,
            'totalSpent'       => $totalSpent,
            'remainingFunds'   => $totalBudget - $totalSpent,
            'overBudgetCount'  => $departmentGroups->where(fn($dept) => $dept['used'] > $dept['total'])->count(),
        ];
    }

    /**
     * Prepares the chart data from a given collection of budgets.
     */
    public function getBudgetChartData(SupportCollection $budgets): array
    {
        $departmentGroups = $this->groupBudgetsByDepartment($budgets);
        
        return [
            'pie' => $departmentGroups->map->total,
            'bar' => $departmentGroups,
        ];
    }

    /**
     * Helper to group a budget collection by department and sum amounts.
     */
    /**
     * Helper to group a budget collection by department and sum amounts.
     */
    private function groupBudgetsByDepartment(SupportCollection $budgets): SupportCollection
    {
        // ADD THIS LINE: Filter out pending budgets before calculating
        $approvedBudgets = $budgets->whereIn('status', ['active', 'closed']);

        // We group by 'department.name' which was eager-loaded by the query
        return $approvedBudgets->groupBy('department.name') // <-- Change this variable
            ->map(fn($group) => [
                'total' => $group->sum('total_amount'),
                'used'  => $group->sum('used_amount'),
            ]);
    }

    /**
     * Private helper to build the base query with filters.
     * This is reused by both paginated and non-paginated methods.
     */
    private function buildFilteredQuery(Request $request): Builder
    {
        $query = Budget::with(['department', 'creator']);

        // Apply filters from the request
        $query->when($request->filled('department_id'), function ($q) use ($request) {
            return $q->where('department_id', $request->department_id);
        });

        $query->when($request->filled('status'), function ($q) use ($request) {
            return $q->where('status', $request->status);
        });

        $query->when($request->filled('start_date'), function ($q) use ($request) {
            return $q->where('period_start', '>=', $request->start_date);
        });

        $query->when($request->filled('end_date'), function ($q) use ($request) {
            return $q->where('period_end', '<=', $request->end_date);
        });

        return $query;
    }
}