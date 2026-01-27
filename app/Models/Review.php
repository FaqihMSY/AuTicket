<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'project_id',
        'reviewer_id',
        'reviewee_id',
        'overall_rating',
        'timeliness_rating',
        'completeness_rating',
        'quality_rating',
        'communication_rating',
        'feedback',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function reviewee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewee_id');
    }

    protected static function booted(): void
    {
        static::created(function (Review $review) {
            $auditor = Auditor::where('user_id', $review->reviewee_id)->first();
            
            if ($auditor) {
                $auditor->updatePerformanceScore();
            }
        });
    }
}
