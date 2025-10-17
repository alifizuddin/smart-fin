<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        Gate::define('is-admin', function ($user) {
            return $user->hasRole('Admin');
        });

        Gate::define('is-manager', function (User $user) {
            return $user->role === 'manager';
        });

        Gate::define('is-staff', function (User $user) {
            return $user->role === 'staff';
        });
        Gate::define('view-budget', function (User $user, Budget $budget) {
            if ($user->role === 'admin') {
                return true;
            }
            return $user->role === 'manager' && $user->department_id === $budget->department_id;
        });
    }
}
