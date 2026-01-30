<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    protected $fillable = [
        'department_id',
        'assignment_type_id',
        'created_by',
        'assigned_manager_id',
        'published_by',
        'reviewer_id',
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'priority',
        'published_at',
        'submitted_at',
        'closed_at',
        'started_at',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'published_at' => 'datetime',
        'submitted_at' => 'datetime',
        'closed_at' => 'datetime',
        'started_at' => 'datetime',
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function assignmentType(): BelongsTo
    {
        return $this->belongsTo(AssignmentType::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'published_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function assignedManager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_manager_id');
    }

    public function auditors(): BelongsToMany
    {
        return $this->belongsToMany(Auditor::class, 'project_assignees');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ProjectAttachment::class);
    }

    public function instructionAttachments(): HasMany
    {
        return $this->attachments()->where('category', 'INSTRUCTION');
    }

    public function resultAttachments(): HasMany
    {
        return $this->attachments()->where('category', 'RESULT');
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function publish(): void
    {
        $this->status = 'ON_PROGRESS';
        $this->published_at = now();
        $this->save();
    }

    public function markAsDone(): void
    {
        $this->status = 'WAITING';
        $this->submitted_at = now();
        $this->save();
    }

    public function close(): void
    {
        $this->status = 'CLOSED';
        $this->closed_at = now();
        $this->save();
    }

    public function isDraft(): bool
    {
        return $this->status === 'DRAFT';
    }

    public function isOnProgress(): bool
    {
        return $this->status === 'ON_PROGRESS';
    }

    public function isWaiting(): bool
    {
        return $this->status === 'WAITING';
    }

    public function isPublished(): bool
    {
        return $this->status === 'PUBLISHED';
    }

    public function isClosed(): bool
    {
        return $this->status === 'CLOSED';
    }

    public function isOverdue(): bool
    {
        return !$this->isClosed() && $this->end_date->isPast();
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['PUBLISHED', 'ON_PROGRESS', 'WAITING']);
    }

    public function scopeOverdue($query)
    {
        return $query->whereNotIn('status', ['CLOSED'])
            ->where('end_date', '<', now());
    }

    public function isDueSoon(): bool
    {
        // Warning if not Closed and deadline is coming in <= 3 days (or already passed)
        return !$this->isClosed() && $this->end_date <= now()->addDays(3);
    }
}
