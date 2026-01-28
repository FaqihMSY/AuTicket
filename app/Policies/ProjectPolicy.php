<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    public function before(User $user, $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->canManageProjects()) {
            return true;
        }

        if ($user->id === $project->created_by) {
            return true;
        }

        if ($user->isAuditor()) {
            return $project->auditors()->where('auditor_id', $user->auditor->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->canManageProjects() || $user->isAuditor();
    }

    public function update(User $user, Project $project): bool
    {
        if ($user->canManageProjects() && ($project->isDraft() || $project->isOnProgress())) {
            return true;
        }

        return $user->isAuditor()
            && $project->isDraft()
            && $user->id === $project->created_by;
    }

    public function delete(User $user, Project $project): bool
    {
        if ($user->canManageProjects() && $project->isDraft()) {
            return true;
        }

        return $user->isAuditor()
            && $project->isDraft()
            && $user->id === $project->created_by;
    }

    public function publish(User $user, Project $project): bool
    {
        return $user->canManageProjects() && $project->isDraft();
    }

    public function start(User $user, Project $project): bool
    {
        return $user->isAuditor()
            && $project->isPublished()
            && $project->auditors->contains($user->auditor->id);
    }

    public function markAsDone(User $user, Project $project): bool
    {
        if (!$user->isAuditor()) {
            return false;
        }

        return $project->auditors()->where('auditor_id', $user->auditor->id)->exists()
            && $project->isOnProgress();
    }

    public function uploadResult(User $user, Project $project): bool
    {
        if (!$user->isAuditor()) {
            return false;
        }

        return $project->auditors()->where('auditor_id', $user->auditor->id)->exists()
            && $project->isOnProgress();
    }

    public function review(User $user, Project $project): bool
    {
        return $user->canManageProjects()
            && $user->id === $project->published_by
            && ($project->isWaiting() || $project->status === 'CLOSED');
    }

    public function cancelSubmission(User $user, Project $project): bool
    {
        return $user->canManageProjects()
            && $project->isWaiting()
            && ($user->isAdmin()
                || $user->id === $project->created_by
                || $user->id === $project->published_by);
    }

    public function cancelReviewSubmission(User $user, Project $project): bool
    {
        if (!$user->isAuditor() && !$user->canManageProjects()) {
            return false;
        }

        return $project->isWaiting()
            && $project->auditors()->where('auditor_id', $user->auditor->id ?? 0)->exists();
    }
}
