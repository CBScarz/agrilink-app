<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the user can approve/reject farmers.
     */
    public function approveFarmer(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view all farmers (for admin).
     */
    public function viewAnyFarmers(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can view a specific farmer's details.
     */
    public function viewFarmer(User $user, User $farmer): bool
    {
        return $user->isAdmin() || $user->id === $farmer->id;
    }
}
