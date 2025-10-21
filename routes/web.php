<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\BudgetController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {

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
        Route::get('budgets/report', [BudgetController::class, 'report'])->name('budgets.report');
        Route::get('/budgets/{budget}/json', [BudgetController::class, 'showJson'])->name('budgets.showJson');
    });

    // --- ROUTES FOR ALL LOGGED-IN ROLES ---
    // (Permissions are handled inside the BudgetController for each action)
    Route::resource('budgets', BudgetController::class);

    // --- OTHER ROLE GROUPS ---
    Route::middleware('role:Manager')->group(function () {
        // Add any routes here that are ONLY for Managers (and not Admins)
    });

    Route::middleware('role:Staff')->group(function () {
        // Staff-specific routes
    });
});

require __DIR__.'/auth.php';