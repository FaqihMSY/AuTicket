<?php

namespace App\Http\Controllers;

use App\Models\Auditor;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuditorController extends Controller
{
    use AuthorizesRequests;
    public function getAvailableAuditors(Request $request)
    {
        $sortBy = $request->get('sort_by', 'performance');

        $query = Auditor::with('user')
            ->active()
            ->withCount([
                'projects as active_projects_count' => function ($q) {
                    $q->whereIn('status', ['ON_PROGRESS', 'WAITING']);
                }
            ]);

        if ($sortBy === 'performance') {
            $query->orderByPerformance();
        } elseif ($sortBy === 'availability') {
            $query->orderBy('active_projects_count', 'asc');
        } elseif ($sortBy === 'balanced') {
            $query->get()->sortByDesc(function ($auditor) {
                return ($auditor->performance_score * 0.6) +
                    ((10 - $auditor->active_projects_count) * 10 * 0.4);
            });
        }

        $auditors = $query->get()->map(function ($auditor) {
            return [
                'id' => $auditor->id,
                'user' => [
                    'name' => $auditor->user->name,
                    'email' => $auditor->user->email,
                ],
                'specialization' => $auditor->specialization,
                'certification' => $auditor->certification,
                'performance_score' => $auditor->performance_score,
                'workload_status' => $auditor->getWorkloadLabel(),
                'workload_score' => $auditor->getWorkloadScore(),
                'workload_color_class' => $auditor->getWorkloadColorClass(),
            ];
        });

        return response()->json($auditors);
    }

    public function index()
    {
        $this->authorize('viewAny', Auditor::class);

        $auditors = Auditor::with(['user', 'user.department'])
            ->withCount([
                'projects as active_projects_count' => function ($q) {
                    $q->whereIn('status', ['ON_PROGRESS', 'WAITING']);
                },
                'projects as completed_projects_count' => function ($q) {
                    $q->where('status', 'CLOSED');
                }
            ])
            ->orderByPerformance()
            ->get();

        return view('auditors.index', compact('auditors'));
    }
    public function export()
    {
        $this->authorize('viewAny', Auditor::class);

        $auditors = Auditor::with(['user', 'projects.assignmentType'])
            ->withCount([
                'projects as active_projects_count' => function ($q) {
                    $q->whereIn('status', ['ON_PROGRESS', 'WAITING']);
                },
                'projects as completed_projects_count' => function ($q) {
                    $q->where('status', 'CLOSED');
                }
            ])
            ->orderByPerformance()
            ->get();

        $assignmentTypes = \App\Models\AssignmentType::pluck('name', 'id');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="auditor_performance_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($auditors, $assignmentTypes) {
            $file = fopen('php://output', 'w');

            // Build Headers
            $csvHeaders = ['Name', 'Specialization', 'Performance Score', 'Total Completed', 'Active Projects', 'Workload Status'];
            foreach ($assignmentTypes as $typeName) {
                $csvHeaders[] = $typeName; // Dynamic column for each type
            }
            fputcsv($file, $csvHeaders);

            foreach ($auditors as $auditor) {
                $row = [
                    $auditor->user->name,
                    $auditor->specialization ?? '-',
                    number_format((float) $auditor->performance_score, 2),
                    $auditor->completed_projects_count,
                    $auditor->active_projects_count,
                    $auditor->getWorkloadStatus()
                ];

                // Calculate counts for each assignment type
                $completedProjects = $auditor->projects->where('status', 'CLOSED');

                foreach ($assignmentTypes as $typeId => $typeName) {
                    $count = $completedProjects->where('assignment_type_id', $typeId)->count();
                    $row[] = $count > 0 ? $count : '0';
                }

                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function show(Auditor $auditor)
    {
        $auditor->load('user');

        $this->authorize('view', $auditor);

        $auditor->load(['reviews.project', 'reviews.reviewer']);

        $stats = [
            'total_projects' => $auditor->projects()->count(),
            'completed_projects' => $auditor->completedProjects()->count(),
            'active_projects' => $auditor->activeProjects()->count(),
            'completion_rate' => $auditor->getCompletionRate(),
            'on_time_rate' => $auditor->getOnTimeRate(),
            'average_completion_days' => $auditor->getAverageCompletionDays(),
            'project_type_breakdown' => $auditor->getProjectTypeBreakdown(),
        ];

        return view('auditors.show', compact('auditor', 'stats'));
    }

    public function chartData(Auditor $auditor)
    {
        $auditor->load('user');

        $this->authorize('view', $auditor);

        return response()->json([
            'performance_trend' => $auditor->getPerformanceTrend(),
            'rating_breakdown' => $auditor->getRatingBreakdown(),
        ]);
    }
}
