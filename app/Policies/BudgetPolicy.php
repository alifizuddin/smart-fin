<?php

namespace App\Policies;

use App\Models\Budget;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BudgetPolicy
{
    use HandlesAuthorization;

    /**
     * Perform pre-authorization checks.
     *
     * This method runs before all other methods.
     * If it returns true, the user is authorized.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return null; // Return null to let other methods decide
    }

    /**
     * Determine whether the user can view any models.
     * (e.g., the main budgets.index page)
     *
     * Only Admins can see *all* budgets. Managers/Staff are
     * limited by the controller query, so we can allow them here.
     */
    public function viewAny(User $user): bool
    {
        // All roles can view the index page, but the
        // controller/service will limit *which* budgets they see.
        return true;
    }

    /**
     * Determine whether the user can view the model.
     * (This replaces your 'view-budget' Gate)
     */
    public function view(User $user, Budget $budget): bool
    {
        // Admin is already handled by the 'before' method.
        // This is the logic from your 'view-budget' Gate.
        return $user->hasRole('Manager') && $user->department_id === $budget->department_id; //
    }

    /**
     * Determine whether the user can create models.
     * (Only Admins)
     */
    public function create(User $user): bool
    {
        // 'before' method handles Admin, so non-admins will
        // get a 'false' and be denied.
        return false;
    }

    /**
     * Determine whether the user can update the model.
     * (Only Admins)
     */
    public function update(User $user, Budget $budget): bool
    {
        // 'before' method handles Admin
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * (Only Admins)
     */
    public function delete(User $user, Budget $budget): bool
    {
        // 'before' method handles Admin
        return false;
    }

    /**
     * Determine whether the user can perform bulk actions (like delete).
     */
    public function deleteAny(User $user): bool
    {
        // 'before' method handles Admin
        return false;
    }
}