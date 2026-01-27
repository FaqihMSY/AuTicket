<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auditor extends Model
{
    protected $fillable = [
        'user_id',
        'specialization',
        'certification',
        'is_active',
        'performance_score',
        'total_completed_projects',
        'average_completion_days',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'performance_score' => 'decimal:2',
        'average_completion_days' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_assignees');
    }

    public function activeProjects(): BelongsToMany
    {
        return $this->projects()->whereIn('status', ['ON_PROGRESS', 'WAITING']);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'reviewee_id', 'user_id');
    }

    public function completedProjects(): BelongsToMany
    {
        return $this->projects()->where('status', 'CLOSED');
    }

    public function getWorkloadStatus(): string
    {
        $activeCount = $this->activeProjects()->count();

        if ($activeCount <= 3) {
            return 'AVAILABLE';
        } elseif ($activeCount <= 5) {
            return 'MODERATE';
        }

        return 'BUSY';
    }

    public function getWorkloadColor(): string
    {
        return match ($this->getWorkloadStatus()) {
            'AVAILABLE' => 'success',
            'MODERATE' => 'warning',
            'BUSY' => 'danger',
        };
    }

    public function updatePerformanceScore(): void
    {
        $averageRating = $this->reviews()->avg('overall_rating');

        if ($averageRating) {
            $this->performance_score = round($averageRating, 2);
            $this->save();
        }
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrderByPerformance($query)
    {
        return $query->orderBy('performance_score', 'desc');
    }

    public function scopeOrderByAvailability($query)
    {
        return $query->withCount([
            'projects as active_projects_count' => function ($q) {
                $q->whereIn('status', ['ON_PROGRESS', 'WAITING']);
            }
        ])->orderBy('active_projects_count', 'asc');
    }

    public function getCompletionRate(): float
    {
        $total = $this->projects()->count();

        if ($total === 0) {
            return 0;
        }

        $completed = $this->completedProjects()->count();
        return round(($completed / $total) * 100, 2);
    }

    public function getOnTimeRate(): float
    {
        $completed = $this->completedProjects()->get();

        if ($completed->isEmpty()) {
            return 0;
        }

        $onTime = $completed->filter(function ($project) {
            return $project->closed_at <= $project->end_date;
        })->count();

        return round(($onTime / $completed->count()) * 100, 2);
    }

    public function getAverageCompletionDays(): float
    {
        $completed = $this->completedProjects()->get();

        if ($completed->isEmpty()) {
            return 0;
        }

        $totalDays = $completed->sum(function ($project) {
            if (!$project->published_at || !$project->closed_at) {
                return 0;
            }
            return $project->published_at->diffInDays($project->closed_at);
        });

        return round($totalDays / $completed->count(), 2);
    }

    public function getPerformanceTrend(int $months = 6): array
    {
        $trend = [];
        $startDate = now()->subMonths($months);

        for ($i = $months - 1; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();

            $avgRating = $this->reviews()
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->avg('overall_rating');

            $trend[] = [
                'month' => $monthStart->format('M Y'),
                'score' => $avgRating ? round($avgRating, 2) : null,
            ];
        }

        return $trend;
    }

    public function getRatingBreakdown(): array
    {
        return [
            'timeliness' => round($this->reviews()->avg('timeliness_rating') ?? 0, 2),
            'completeness' => round($this->reviews()->avg('completeness_rating') ?? 0, 2),
            'quality' => round($this->reviews()->avg('quality_rating') ?? 0, 2),
            'communication' => round($this->reviews()->avg('communication_rating') ?? 0, 2),
        ];
    }

    public function getProjectTypeBreakdown(): array
    {
        return $this->completedProjects()
            ->with('assignmentType')
            ->get()
            ->filter(function ($project) {
                return $project->assignmentType !== null;
            })
            ->groupBy('assignment_type_id')
            ->map(function ($projects) {
                return [
                    'type' => $projects->first()->assignmentType->name,
                    'count' => $projects->count(),
                ];
            })
            ->values()
            ->toArray();
    }
}
