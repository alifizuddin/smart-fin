<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    // Staff routes
    Route::middleware('role:Staff')->group(function () {
        Route::get('my-expenses', [ExpenseController::class, 'myExpenses'])
            ->name('expenses.my-expenses');
            
        Route::post('expenses/quick-store', [ExpenseController::class, 'quickStore'])
            ->name('expenses.quickStore');
            
        Route::get('analytics', \App\Livewire\Staff\Analytics::class)
            ->name('staff.analytics');
            
        Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy'])
            ->name('expenses.delete');
        Route::post('expenses/analyze', [ExpenseController::class, 'analyzeReceipt'])
        ->name('expenses.analyze');
    });

    // --- ADMIN-ONLY ROUTES ---
    Route::middleware('role:Finance Admin|Admin')->group(function () {
        // Route::resource already creates index, edit, update, etc.
        // The individual routes before it were redundant.
        Route::post('budgets/{budget}/approve', [BudgetController::class, 'approve'])
        ->name('budgets.approve')
        ->middleware('role:Finance Admin'); // Only Admins can access

        Route::post('budgets/{budget}/reject', [BudgetController::class, 'reject'])
        ->name('budgets.reject')
        ->middleware('role:Finance Admin'); // Only Admins can access
        
        Route::resource('users', UserController::class);
        
        // This route is Admin-only, so it must be in this group
        Route::post('budgets/bulk-action', [BudgetController::class, 'bulkAction'])->name('budgets.bulkAction');
    });

    // --- ADMIN & MANAGER ROUTES ---
    Route::middleware('role:Finance Admin|Manager')->group(function () {
        // This single definition now works for both roles
        Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])
        ->name('expenses.approve')
        ->middleware('role:Manager');

        Route::post('expenses/{expense}/reject', [ExpenseController::class, 'reject'])
        ->name('expenses.reject')
        ->middleware('role:Manager');
        
        // Forecasting
        Route::get('/forecasting', \App\Livewire\Forecasting\Index::class)
            ->name('forecasting.index');
        
        Route::get('budgets/report', [BudgetController::class, 'report'])->name('budgets.report');
    });

    Route::middleware('role:Manager')->group(function () {
        // Add any routes here that are ONLY for Managers (and not Admins)
        Route::get('expenses/approvals', [ExpenseController::class, 'approvalsIndex'])
            ->name('expenses.approvals.index');
        
        Route::patch('notifications/read/{id}', [DashboardController::class, 'markNotificationRead'])
            ->name('notifications.read');
            
    });
    Route::middleware('role:Staff')->group(function () {
        // Staff-specific routes
    });

    Route::resource('budgets', BudgetController::class);
    Route::resource('expenses', ExpenseController::class);
    Route::get('/budgets/{budget}/json', [BudgetController::class, 'showJson'])->name('budgets.showJson');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    
    // Reporting routes (handled by Livewire)
    Route::middleware('role:Finance Admin')->group(function () {
        Route::get('/reporting', \App\Livewire\Reporting::class)->name('reporting.index');
        Route::get('/reporting/export', [\App\Livewire\Reporting::class, 'exportReport'])->name('reporting.export');
    });

});

require __DIR__.'/auth.php';