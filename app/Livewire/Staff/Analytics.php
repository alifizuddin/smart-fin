<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class Analytics extends Component
{
    use WithPagination;
    
    public $filter = 'all';
    public $dateRange = 'all';
    public $perPage = 5;

    public function render()
    {
        $user = Auth::user();

        // 1. Total Spending
        $totalSpent = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');
            
        // Handle pagination with filters
        $query = Expense::where('user_id', $user->id)
            ->when($this->filter !== 'all', function($q) {
                return $q->where('status', $this->filter);
            })
            ->latest();
            
        $recentExpenses = $query->paginate($this->perPage);

        // 2. Pending Amount
        $pendingAmount = Expense::where('user_id', $user->id)
            ->where('status', 'pending')
            ->sum('amount');

        // 3. This Month vs Last Month
        $thisMonth = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->month)
            ->sum('amount');

        $lastMonth = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('created_at', Carbon::now()->subMonth()->month)
            ->sum('amount');

        $trend = 0;
        if ($lastMonth > 0) {
            $trend = (($thisMonth - $lastMonth) / $lastMonth) * 100;
        }

        // Apply date range filter if needed
        if ($this->dateRange !== 'all') {
            $now = Carbon::now();
            switch ($this->dateRange) {
                case 'this_month':
                    $query->whereYear('created_at', $now->year)
                          ->whereMonth('created_at', $now->month);
                    break;
                case 'last_month':
                    $lastMonth = $now->copy()->subMonth();
                    $query->whereYear('created_at', $lastMonth->year)
                          ->whereMonth('created_at', $lastMonth->month);
                    break;
            }
        }

        return view('livewire.staff.analytics', [
            'totalSpent' => $totalSpent,
            'pendingAmount' => $pendingAmount,
            'thisMonth' => $thisMonth,
            'trend' => $trend,
            'recentExpenses' => $recentExpenses
        ]);
    }

    #[On('filter-by-status')]
    public function filterByStatus($status)
    {
        $this->filter = $status;
        $this->resetPage(); // Reset to first page when filtering
    }

    #[On('filter-by-date')]
    public function filterByDate($dateRange)
    {
        $this->dateRange = $dateRange;
        $this->resetPage(); // Reset to first page when filtering
    }

    public function resetFilters()
    {
        $this->filter = 'all';
        $this->dateRange = 'all';
        $this->resetPage(); // Reset to first page when resetting filters
    }
    
    public function updatedFilter()
    {
        $this->resetPage();
    }
    
    public function updatedDateRange()
    {
        $this->resetPage();
    }

    /**
     * Check if a filter is currently being applied
     */
    public function isFiltering(): bool
    {
        return $this->getErrorBag()->has('filter') || 
               $this->getErrorBag()->has('dateRange') ||
               $this->getErrorBag()->has('perPage');
    }
}
