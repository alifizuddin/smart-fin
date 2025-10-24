<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        }

        // 3. Pass data to the view
        return view('dashboard', [
            'pendingExpenses' => $pendingExpenses,
            'activeBudget' => $activeBudget,
            'userExpenses' => $userExpenses ?? collect()
        ]);
    }
}