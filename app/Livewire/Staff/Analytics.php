<?php

namespace App\Livewire\Staff;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use Carbon\Carbon;
use Livewire\Attributes\Layout;

#[Layout('layouts.app')]
class Analytics extends Component
{
    public function render()
    {
        $user = Auth::user();

        // 1. Total Spending
        $totalSpent = Expense::where('user_id', $user->id)
            ->where('status', 'approved')
            ->sum('amount');

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

        // 4. Recent Transactions
        $recentExpenses = Expense::where('user_id', $user->id)
            ->latest()
            ->take(5)
            ->get();

        return view('livewire.staff.analytics', [
            'totalSpent' => $totalSpent,
            'pendingAmount' => $pendingAmount,
            'thisMonth' => $thisMonth,
            'trend' => $trend,
            'recentExpenses' => $recentExpenses
        ]);
    }
}
