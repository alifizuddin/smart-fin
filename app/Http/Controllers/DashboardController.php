<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense; // <-- Import the Expense model
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the user's dashboard.
     */
    public function index(): View
    {
        $user = Auth::user();
        $data = []; // Initialize an empty array for data

        // Check if the user has the 'Staff' role
        if ($user->hasRole('Staff')) {
            
            // Fetch expenses for the current user
            // We'll get the 10 most recent submissions
            $data['expenseSubmissions'] = Expense::where('user_id', $user->id)
                                                ->orderBy('created_at', 'desc')
                                                ->take(10) 
                                                ->get();
            
            // You can also pass data for the other cards here if needed
            // $data['aiSuggestions'] = [];
            // $data['budgetOverview'] = [];
            // $data['approvalFollowUps'] = [];
        }

        // Pass all collected data to the view
        return view('dashboard', $data);
    }
}