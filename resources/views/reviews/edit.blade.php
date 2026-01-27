@extends('layouts.app')

@section('title', 'Edit Review')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2>Edit Review</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></li>
                    <li class="breadcrumb-item active">Edit Review</li>
                </ol>
            </nav>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Project Summary -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Project Summary</h5>
                </div>
                <div class="card-body">
                    <h6>{{ $project->title }}</h6>
                    <p class="mb-2"><strong>Assignment Type:</strong> {{ $project->assignmentType->name }}</p>
                    <p class="mb-2"><strong>Duration:</strong> {{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</p>
                    <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning">WAITING APPROVAL</span></p>
                </div>
            </div>

            <!-- Review Form -->
            <form method="POST" action="{{ route('reviews.update', $project) }}" id="reviewForm">
                @method('PUT')
                @csrf

                @foreach($project->auditors as $index => $auditor)
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Review for {{ $auditor->user->name }}</h5>
                        <small class="text-muted">{{ $auditor->specialization ?? 'General Auditor' }}</small>
                    </div>
                    <div class="card-body">
                        <!-- Overall Score (Required) -->
                        <div class="mb-3">
                            <label for="overall_rating_{{ $auditor->id }}" class="form-label">
                                Overall Score <span class="text-danger">*</span>
                            </label>
                            <input type="number" 
                                   class="form-control @error('reviews.'.$index.'.overall_rating') is-invalid @enderror" 
                                   id="overall_rating_{{ $auditor->id }}" 
                                   name="reviews[{{ $index }}][overall_rating]" 
                                   min="1" max="100" 
                                   value="{{ old('reviews.'.$index.'.overall_rating', $existingReviews[$auditor->user->id]->overall_rating ?? 85) }}" 
                                   required>
                            <input type="hidden" name="reviews[{{ $index }}][review_id]" value="{{ $existingReviews[$auditor->user->id]->id ?? '' }}">
                            <small class="text-muted">Score from 1 to 100</small>
                            @error('reviews.'.$index.'.overall_rating')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                        </div>

                        <!-- Detailed Scores (Optional) -->
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="timeliness_rating_{{ $auditor->id }}" class="form-label">
                                    Timeliness Score
                                </label>
                                <input type="number" 
                                       class="form-control @error('reviews.'.$index.'.timeliness_rating') is-invalid @enderror" 
                                       id="timeliness_rating_{{ $auditor->id }}" 
                                       name="reviews[{{ $index }}][timeliness_rating]" 
                                       min="1" max="100" 
                                       value="{{ old('reviews.'.$index.'.timeliness_rating', $existingReviews[$auditor->user->id]->timeliness_rating ?? '') }}">
                                <small class="text-muted">Optional</small>
                                @error('reviews.'.$index.'.timeliness_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="completeness_rating_{{ $auditor->id }}" class="form-label">
                                    Completeness Score
                                </label>
                                <input type="number" 
                                       class="form-control @error('reviews.'.$index.'.completeness_rating') is-invalid @enderror" 
                                       id="completeness_rating_{{ $auditor->id }}" 
                                       name="reviews[{{ $index }}][completeness_rating]" 
                                       min="1" max="100" 
                                       value="{{ old('reviews.'.$index.'.completeness_rating', $existingReviews[$auditor->user->id]->completeness_rating ?? '') }}">
                                <small class="text-muted">Optional</small>
                                @error('reviews.'.$index.'.completeness_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="quality_rating_{{ $auditor->id }}" class="form-label">
                                    Quality Score
                                </label>
                                <input type="number" 
                                       class="form-control @error('reviews.'.$index.'.quality_rating') is-invalid @enderror" 
                                       id="quality_rating_{{ $auditor->id }}" 
                                       name="reviews[{{ $index }}][quality_rating]" 
                                       min="1" max="100" 
                                       value="{{ old('reviews.'.$index.'.quality_rating', $existingReviews[$auditor->user->id]->quality_rating ?? '') }}">
                                <small class="text-muted">Optional</small>
                                @error('reviews.'.$index.'.quality_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="communication_rating_{{ $auditor->id }}" class="form-label">
                                    Communication Score
                                </label>
                                <input type="number" 
                                       class="form-control @error('reviews.'.$index.'.communication_rating') is-invalid @enderror" 
                                       id="communication_rating_{{ $auditor->id }}" 
                                       name="reviews[{{ $index }}][communication_rating]" 
                                       min="1" max="100" 
                                       value="{{ old('reviews.'.$index.'.communication_rating', $existingReviews[$auditor->user->id]->communication_rating ?? '') }}">
                                <small class="text-muted">Optional</small>
                                @error('reviews.'.$index.'.communication_rating')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Feedback -->
                        <div class="mb-3">
                            <label for="feedback_{{ $auditor->id }}" class="form-label">Feedback</label>
                            <textarea class="form-control @error('reviews.'.$index.'.feedback') is-invalid @enderror" 
                                      id="feedback_{{ $auditor->id }}" 
                                      name="reviews[{{ $index }}][feedback]" 
                                      rows="3" 
                                      maxlength="500">{{ old('reviews.'.$index.'.feedback', $existingReviews[$auditor->user->id]->feedback ?? '') }}</textarea>
                            <small class="text-muted">Optional, max 500 characters</small>
                            @error('reviews.'.$index.'.feedback')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Stats -->
                        <div class="alert alert-info">
                            <small>
                                <strong>Completed Projects:</strong> {{ $auditor->total_completed_projects }}
                            </small>
                        </div>
                    </div>
                </div>
                @endforeach

                <!-- Submit Buttons -->
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" onclick="return confirm('Update reviews? Performance scores will be recalculated.');">
                                <i class="bi bi-save"></i> Update Reviews
                            </button>
                            <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-lg">Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Help Sidebar -->
        <div class="col-lg-4">
            <div class="card mb-3">
                <div class="card-header">
                    <h6 class="mb-0">Scoring Guide</h6>
                </div>
                <div class="card-body">
                    <h6>Score Ranges:</h6>
                    <ul class="list-unstyled small">
                        <li><strong>90-100:</strong> Excellent</li>
                        <li><strong>80-89:</strong> Very Good</li>
                        <li><strong>70-79:</strong> Good</li>
                        <li><strong>60-69:</strong> Satisfactory</li>
                        <li><strong>Below 60:</strong> Needs Improvement</li>
                    </ul>

                    <hr>

                    <h6>Scoring Aspects:</h6>
                    <ul class="small">
                        <li><strong>Timeliness:</strong> Met deadlines, punctuality</li>
                        <li><strong>Completeness:</strong> All requirements fulfilled</li>
                        <li><strong>Quality:</strong> Accuracy and thoroughness</li>
                        <li><strong>Communication:</strong> Responsiveness and clarity</li>
                    </ul>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">What Happens Next?</h6>
                </div>
                <div class="card-body">
                    <ol class="small">
                        <li>Reviews will be saved for each auditor</li>
                        <li>Performance scores will be automatically updated</li>
                        <li>Project status will change to CLOSED</li>
                        <li>Auditors will be notified of their reviews</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Form validation
    const form = document.getElementById('reviewForm');
    
    form.addEventListener('submit', function(e) {
        let valid = true;
        const overallScores = document.querySelectorAll('[name*="[overall_rating]"]');
        
        overallScores.forEach(input => {
            const value = parseInt(input.value);
            if (!value || value < 1 || value > 100) {
                valid = false;
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });
        
        if (!valid) {
            e.preventDefault();
            alert('Please provide overall scores (1-100) for all auditors.');
            return false;
        }
    });
    
    // Number input validation
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(input => {
        input.addEventListener('input', function() {
            const value = parseInt(this.value);
            if (value && (value < 1 || value > 100)) {
                this.setCustomValidity('Score must be between 1 and 100');
            } else {
                this.setCustomValidity('');
            }
        });
    });
});
</script>
@endpush
