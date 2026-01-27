<?php

namespace App\Policies;

use App\Models\Auditor;
use App\Models\User;

class AuditorPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canManageProjects();
    }

    public function view(User $user, Auditor $auditor): bool
    {
        if (!$auditor->user) {
            return false;
        }

        if ($user->canManageProjects()) {
            return true;
        }

        return $user->id === $auditor->user_id;
    }
}
