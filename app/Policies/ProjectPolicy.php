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

        // Assigned reviewer can view
        if ($user->isReviewer() && $project->reviewer_id === $user->id) {
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

        if ($user->isAdmin()) {
            return true;
        }

        $isEditableStatus = $project->isDraft() || $project->isPublished() || $project->isOnProgress();

        if (!$isEditableStatus) {
            return false;
        }

        if ($user->canManageProjects()) {
            return $user->id === $project->created_by || $user->id === $project->published_by;
        }

        if ($user->role === 'staff') {
            return $project->isDraft() && $user->id === $project->created_by;
        }

        return false;
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
        // Any manager can review
        if ($user->canManageProjects()) {
            return ($project->isWaiting() || $project->status === 'CLOSED');
        }

        // Assigned reviewer can review
        if ($project->reviewer_id === $user->id) {
            return ($project->isWaiting() || $project->status === 'CLOSED');
        }

        return false;
    }

    public function close(User $user, Project $project): bool
    {
        // Manager can always close
        if ($user->canManageProjects()) {
            return true;
        }

        // Assigned reviewer can close
        if ($project->reviewer_id === $user->id) {
            return true;
        }

        return false;
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

    public function assignReviewer(User $user, Project $project): bool
    {
        // Only managers can assign reviewers
        if (!$user->canManageProjects()) {
            return false;
        }

        // Can assign reviewer to WAITING projects
        if ($project->status === 'WAITING') {
            return true;
        }

        // Can also assign during creation/editing (DRAFT, PUBLISHED, ON_PROGRESS)
        return $this->update($user, $project);
    }
}
