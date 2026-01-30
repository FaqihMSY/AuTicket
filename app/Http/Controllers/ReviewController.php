<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Auditor;
use App\Models\Project;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ReviewController extends Controller
{
    use AuthorizesRequests;
    public function create(Project $project)
    {
        $this->authorize('review', $project);

        $project->load('auditors.user');

        // Calculate suggested scores for each auditor
        $suggestedScores = [];
        foreach ($project->auditors as $auditor) {
            $suggestedScores[$auditor->id] = $this->calculateSuggestedScores($project, $auditor);
        }

        return view('reviews.create', compact('project', 'suggestedScores'));
    }

    /**
     * Calculate suggested review scores based on project metrics
     */
    private function calculateSuggestedScores(Project $project, $auditor): array
    {
        // Timeliness: Based on submission vs deadline
        $timelinessScore = 100; // Default excellent
        if ($project->submitted_at && $project->end_date) {
            $deadline = \Carbon\Carbon::parse($project->end_date);
            $submitted = \Carbon\Carbon::parse($project->submitted_at);
            
            if ($submitted->gt($deadline)) {
                // Late submission
                $daysLate = $submitted->diffInDays($deadline);
                $timelinessScore = max(60, 100 - ($daysLate * 5)); // -5 points per day late, min 60
            } else {
                // Early or on-time
                $daysEarly = $deadline->diffInDays($submitted);
                $timelinessScore = min(100, 95 + $daysEarly); // Bonus for early, max 100
            }
        }

        // Completeness: Based on result files uploaded
        $completenessScore = 85; // Default good
        $resultFiles = $project->attachments()->where('category', 'RESULT')->count();
        if ($resultFiles > 0) {
            $completenessScore = min(100, 85 + ($resultFiles * 5)); // +5 per file, max 100
        }

        // Quality & Communication: Default to good (can be adjusted manually)
        $qualityScore = 85;
        $communicationScore = 85;

        // Overall: Average of all aspects
        $overallScore = round(($timelinessScore + $completenessScore + $qualityScore + $communicationScore) / 4);

        return [
            'overall_rating' => $overallScore,
            'timeliness_rating' => round($timelinessScore),
            'completeness_rating' => round($completenessScore),
            'quality_rating' => $qualityScore,
            'communication_rating' => $communicationScore,
        ];
    }

    public function store(Request $request, Project $project)
    {
        $this->authorize('review', $project);

        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*.reviewee_id' => 'required|exists:users,id',
            'reviews.*.overall_rating' => 'required|integer|min:1|max:100',
            'reviews.*.timeliness_rating' => 'nullable|integer|min:1|max:100',
            'reviews.*.completeness_rating' => 'nullable|integer|min:1|max:100',
            'reviews.*.quality_rating' => 'nullable|integer|min:1|max:100',
            'reviews.*.communication_rating' => 'nullable|integer|min:1|max:100',
            'reviews.*.feedback' => 'nullable|string',
        ]);

        foreach ($validated['reviews'] as $reviewData) {
            Review::create([
                'project_id' => $project->id,
                'reviewer_id' => auth()->id(),
                'reviewee_id' => $reviewData['reviewee_id'],
                'overall_rating' => $reviewData['overall_rating'],
                'timeliness_rating' => $reviewData['timeliness_rating'] ?? null,
                'completeness_rating' => $reviewData['completeness_rating'] ?? null,
                'quality_rating' => $reviewData['quality_rating'] ?? null,
                'communication_rating' => $reviewData['communication_rating'] ?? null,
                'feedback' => $reviewData['feedback'] ?? null,
            ]);
        }

        if (auth()->user()->can('close', $project)) {
            $project->close();
            ActivityLog::log('project_closed', $project->id, "Closed project with reviews: {$project->title}");
            $message = 'Reviews submitted and project closed successfully';
        } else {
            ActivityLog::log('review_submitted', $project->id, "Submitted review for project: {$project->title}");
            $message = 'Review submitted successfully. Only the manager or assigned reviewer can close this project.';
        }

        return redirect()->route('projects.show', $project)
            ->with('success', $message);
    }

    public function edit(Project $project)
    {
        $this->authorize('review', $project);

        if ($project->status !== 'CLOSED') {
            return redirect()->route('projects.show', $project)
                ->with('error', 'Can only edit reviews for closed projects');
        }

        $project->load('auditors.user', 'reviews');

        // Get existing reviews indexed by reviewee_id
        $existingReviews = [];
        foreach ($project->reviews as $review) {
            $existingReviews[$review->reviewee_id] = $review;
        }

        return view('reviews.edit', compact('project', 'existingReviews'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('review', $project);

        $validated = $request->validate([
            'reviews' => 'required|array',
            'reviews.*.review_id' => 'required|exists:reviews,id',
            'reviews.*.overall_rating' => 'required|integer|min:1|max:100',
            'reviews.*.timeliness_rating' => 'nullable|integer|min:1|max:100',
            'reviews.*.completeness_rating' => 'nullable|integer|min:1|max:100',
            'reviews.*.quality_rating' => 'nullable|integer|min:1|max:100',
            'reviews.*.communication_rating' => 'nullable|integer|min:1|max:100',
            'reviews.*.feedback' => 'nullable|string',
        ]);

        foreach ($validated['reviews'] as $reviewData) {
            $review = Review::findOrFail($reviewData['review_id']);

            $review->update([
                'overall_rating' => $reviewData['overall_rating'],
                'timeliness_rating' => $reviewData['timeliness_rating'] ?? null,
                'completeness_rating' => $reviewData['completeness_rating'] ?? null,
                'quality_rating' => $reviewData['quality_rating'] ?? null,
                'communication_rating' => $reviewData['communication_rating'] ?? null,
                'feedback' => $reviewData['feedback'] ?? null,
            ]);

            // Update auditor performance score
            $auditor = Auditor::where('user_id', $review->reviewee_id)->first();
            if ($auditor) {
                $auditor->updatePerformanceScore();
            }
        }

        ActivityLog::log('reviews_updated', $project->id, "Updated reviews for project: {$project->title}");

        return redirect()->route('projects.show', $project)
            ->with('success', 'Reviews updated successfully');
    }
}
