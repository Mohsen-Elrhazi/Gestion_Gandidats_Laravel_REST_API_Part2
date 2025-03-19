<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine if the authenticated user can update their own profile.
     */
    public function update(User $authUser, User $user): bool
    {
        return $authUser->id === $user->id;
    }
}