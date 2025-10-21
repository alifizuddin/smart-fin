<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Grant all permissions to Admins.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('Admin')) {
            return true;
        }
        
        return null; // Let other methods decide for non-admins
    }

    /**
     * Determine whether the user can view any models.
     * (e.g., the user index page)
     */
    public function viewAny(User $user): bool
    {
        // Only Admins (handled by 'before')
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // Only Admins (handled by 'before')
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Only Admins (handled by 'before')
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Only Admins (handled by 'before')
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * We add the self-delete check here!
     */
    public function delete(User $user, User $model): bool
    {
        // The 'before' method gives Admins access, but we ALSO
        // want to prevent them from deleting themselves.
        if ($user->id === $model->id) {
            return false;
        }
        
        // This check will now apply *even to admins*.
        return $user->hasRole('Admin');
    }
}