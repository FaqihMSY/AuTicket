<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $targetUser): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }

        if ($user->id === $targetUser->id) {
            return false;
        }

        return true;
    }

    public function delete(User $user, User $targetUser): bool
    {
        if (!$user->isAdmin()) {
            return false;
        }

        
        // Prevent deleting self
        if ($user->id === $targetUser->id) {
            return false;
        }

        return true;
    }
}
