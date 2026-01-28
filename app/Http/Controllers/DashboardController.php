<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user->canManageProjects()) {
            return $this->adminDashboard();
        }

        return $this->auditorDashboard();
    }

    private function adminDashboard()
    {
        $user = auth()->user();
        $departmentId = $user->department_id;

        // Base query for stats
        $projectQuery = Project::query();

        // Only filter by department if NOT admin
        if (!$user->isAdmin()) {
            $projectQuery->where('department_id', $departmentId);
        }

        $stats = [
            'draft' => (clone $projectQuery)->where('status', 'DRAFT')->count(),
            'published' => (clone $projectQuery)->where('status', 'PUBLISHED')->count(),
            'active' => (clone $projectQuery)->where('status', 'ON_PROGRESS')->count(),
            'waiting' => (clone $projectQuery)->where('status', 'WAITING')->count(),
            'closed' => (clone $projectQuery)->where('status', 'CLOSED')->count(), // Total Completed
            'overdue' => (clone $projectQuery)->overdue()->count(),
        ];

        // Waiting projects query
        $waitingProjectsQuery = Project::where('status', 'WAITING')
            ->with(['auditors.user', 'assignmentType'])
            ->latest()
            ->take(5);

        if (!$user->isAdmin()) {
            $waitingProjectsQuery->where('department_id', $departmentId);
        }

        $waitingProjects = $waitingProjectsQuery->get();

        return view('dashboard.admin', compact('stats', 'waitingProjects'));
    }

    private function auditorDashboard()
    {
        $user = auth()->user();
        $auditor = $user->auditor;

        $stats = [
            'draft' => Project::where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                    ->orWhereHas('auditors', function ($subQ) use ($user) {
                        $subQ->where('auditors.id', $user->auditor->id);
                    });
            })->where('status', 'DRAFT')->count(),
            'published' => Project::where('department_id', $user->department_id)->where('status', 'PUBLISHED')->count(),
            'active' => $auditor->projects()->where('status', 'ON_PROGRESS')->count(),
            'waiting' => $auditor->projects()->where('status', 'WAITING')->count(),
            'closed' => $auditor->projects()->where('status', 'CLOSED')->count(),
            'overdue' => $auditor->projects()->overdue()->count(),
            'performance_score' => $auditor->performance_score,
        ];

        $activeProjects = $auditor->projects()
            ->with(['assignmentType', 'creator'])
            ->whereIn('status', ['ON_PROGRESS', 'WAITING'])
            ->orderBy('end_date', 'asc')
            ->get();

        return view('dashboard.auditor', compact('stats', 'activeProjects'));
    }
}
