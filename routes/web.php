<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {

    Route::post('expenses/quick-store', [ExpenseController::class, 'quickStore'])
        ->name('expenses.quickStore')
        ->middleware('role:Staff');
        
    Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])->name('expenses.delete');

    // --- ADMIN-ONLY ROUTES ---
    Route::middleware('role:Admin')->group(function () {
        // Route::resource already creates index, edit, update, etc.
        // The individual routes before it were redundant.
        Route::patch('budgets/{budget}/approve', [BudgetController::class, 'approve'])
        ->name('budgets.approve')
        ->middleware('role:Admin'); // Only Admins can access

        Route::patch('budgets/{budget}/reject', [BudgetController::class, 'reject'])
        ->name('budgets.reject')
        ->middleware('role:Admin'); // Only Admins can access
        
        Route::resource('users', UserController::class);

        // This route is Admin-only, so it must be in this group
        Route::post('budgets/bulk-action', [BudgetController::class, 'bulkAction'])->name('budgets.bulkAction');
    });

    // --- ADMIN & MANAGER ROUTES ---
    Route::middleware('role:Admin|Manager')->group(function () {
        // This single definition now works for both roles
        Route::patch('expenses/{expense}/approve', [ExpenseController::class, 'approve'])
        ->name('expenses.approve')
        ->middleware('role:Manager');

        Route::patch('expenses/{expense}/reject', [ExpenseController::class, 'reject'])
        ->name('expenses.reject')
        ->middleware('role:Manager');
        
        Route::get('budgets/report', [BudgetController::class, 'report'])->name('budgets.report');
    });

    Route::middleware('role:Manager')->group(function () {
        // Add any routes here that are ONLY for Managers (and not Admins)
        Route::get('expenses/approvals', [ExpenseController::class, 'approvalsIndex'])
            ->name('expenses.approvals.index');
    });
    Route::middleware('role:Staff')->group(function () {
        // Staff-specific routes
    });

    // --- ROUTES FOR ALL LOGGED-IN ROLES ---
    // (Permissions are handled inside the BudgetController for each action)
    Route::resource('budgets', BudgetController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::get('/budgets/{budget}/json', [BudgetController::class, 'showJson'])->name('budgets.showJson');

});

require __DIR__.'/auth.php';