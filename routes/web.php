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

    Route::middleware('role:Admin')->group(function () { // Use role middleware
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::resource('users', UserController::class);
        Route::get('/budgets/{budget}/json', [BudgetController::class, 'showJson'])->name('budgets.showJson');
        Route::get('budgets/report', [BudgetController::class, 'report'])->name('budgets.report');
    });
    Route::resource('budgets', BudgetController::class);
    Route::post('budgets-bulk-action', [BudgetController::class, 'bulkAction'])->name('budgets.bulkAction');

    // You can add more role-specific routes here
    Route::middleware('role:Manager')->group(function () {
        // Manager-specific routes
    });

    Route::middleware('role:Staff')->group(function () {
        // Staff-specific routes
    });
});
require __DIR__.'/auth.php';
