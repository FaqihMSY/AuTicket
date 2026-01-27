<?php

namespace App\Policies;

use App\Models\ProjectAttachment;
use App\Models\User;

class ProjectAttachmentPolicy
{
    public function download(User $user, ProjectAttachment $attachment): bool
    {
        $project = $attachment->project;

        if ($user->canManageProjects()) {
            return true;
        }

        if ($user->isAuditor()) {
            return $project->auditors()->where('auditor_id', $user->auditor->id)->exists();
        }

        return false;
    }

    public function delete(User $user, ProjectAttachment $attachment): bool
    {
        $project = $attachment->project;

        // Managers can delete if project is not CLOSED (or strictly DRAFT? Let's say NOT CLOSED for flexibility)
        if ($user->canManageProjects()) {
            return !$project->isClosed();
        }

        // Auditors can delete their OWN uploads ONLY if project is ON_PROGRESS
        if ($user->isAuditor() && $attachment->uploaded_by === $user->id) {
            return $project->isOnProgress();
        }

        return false;
    }
}
