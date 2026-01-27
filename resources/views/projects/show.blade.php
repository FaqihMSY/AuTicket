@extends('layouts.app')

@section('title', 'Project Detail')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>{{ $project->title }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                        <li class="breadcrumb-item active">{{ $project->title }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-end">
                @if($project->status === 'DRAFT')
                    <span class="badge bg-secondary fs-5">DRAFT</span>
                @elseif($project->status === 'PUBLISHED')
                    <span class="badge bg-info fs-5">PUBLISHED</span>
                @elseif($project->status === 'ON_PROGRESS')
                    <span class="badge bg-primary fs-5">ON PROGRESS</span>
                @elseif($project->status === 'WAITING')
                    <span class="badge bg-warning fs-5">WAITING APPROVAL</span>
                @else
                    <span class="badge bg-success fs-5">CLOSED</span>
                @endif
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Project Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Project Information</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Assignment Type:</th>
                                <td>{{ $project->assignmentType->name }}</td>
                            </tr>
                            <tr>
                                <th>Priority:</th>
                                <td>
                                    @if($project->priority === 'HIGH')
                                        <span class="badge bg-danger">High</span>
                                    @elseif($project->priority === 'MEDIUM')
                                        <span class="badge bg-warning">Medium</span>
                                    @else
                                        <span class="badge bg-info">Low</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Start Date:</th>
                                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>End Date:</th>
                                <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Created By:</th>
                                <td>
                                    <strong>{{ $project->creator->name }}</strong>
                                    ({{ $project->creator->department->name }})
                                    @if(auth()->id() === $project->created_by)
                                        <span class="badge bg-info">You</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Created At:</th>
                                <td>{{ $project->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>

                        @if($project->description)
                            <hr>
                            <h6>Description:</h6>
                            <p>{{ $project->description }}</p>
                        @endif
                    </div>
                </div>

                <!-- Assigned Auditors -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Assigned Auditors</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Specialization</th>
                                        @if(auth()->user()->canManageProjects())
                                            <th>Performance Score</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->auditors as $auditor)
                                        <tr>
                                            <td>{{ $auditor->user->name }}</td>
                                            <td>{{ $auditor->specialization ?? '-' }}</td>
                                            @if(auth()->user()->canManageProjects())
                                                <td>
                                                    <span class="badge bg-primary">{{ $auditor->performance_score }}/100</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- File Attachments -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">File Attachments</h5>
                    </div>
                    <div class="card-body">
                        @if($project->attachments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>File Name</th>
                                            <th>Type</th>
                                            <th>Uploaded By</th>
                                            <th>Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->attachments as $attachment)
                                            <tr>
                                                <td>{{ $attachment->original_filename }}</td>
                                                <td>
                                                    @if($attachment->category === 'INSTRUCTION')
                                                        <span class="badge bg-info">Instruction</span>
                                                    @else
                                                        <span class="badge bg-success">Result</span>
                                                    @endif
                                                </td>
                                                <td>{{ $attachment->uploader->name }}</td>
                                                <td>{{ $attachment->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <a href="{{ route('attachments.download', $attachment) }}"
                                                        class="btn btn-sm btn-primary me-1">
                                                        <i class="bi bi-download"></i> Download
                                                    </a>
                                                    @can('delete', $attachment)
                                                        <form action="{{ route('attachments.destroy', $attachment) }}" method="POST"
                                                            onsubmit="return confirm('Are you sure you want to delete this file?');"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i> Delete
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No files attached yet.</p>
                        @endif
                    </div>
                </div>

                <!-- Upload Result (For Auditors in ON_PROGRESS) -->
                <!-- Upload Work Papers / Attachments (For Auditors in ON_PROGRESS) -->
                @if($project->status === 'ON_PROGRESS' && auth()->user()->isAuditor() && $project->auditors->contains('user_id', auth()->id()))
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Upload Work Papers / Attachments</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <i class="bi bi-info-circle"></i> Files uploaded here are immediately visible to Managers.
                                Uploading files <strong>does not</strong> automatically submit the project.
                            </p>
                            <form method="POST" action="{{ route('projects.uploadResult', $project) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="result_files" class="form-label">Select Files (PDF/Excel/Word, max 10MB
                                        each)</label>
                                    <input type="file" class="form-control @error('result_files.*') is-invalid @enderror"
                                        id="result_files" name="result_files[]" accept=".pdf,.xlsx,.xls,.doc,.docx" multiple
                                        required>
                                    @error('result_files.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Upload Files
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="col-lg-4">
                <!-- Workflow Actions -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Actions</h6>
                    </div>
                    <div class="card-body">
                        @if($project->status === 'DRAFT' && auth()->user()->canManageProjects())
                            <!-- Publish Project -->
                            <form method="POST" action="{{ route('projects.publish', $project) }}"
                                onsubmit="return confirm('Are you sure you want to publish this project? Auditors will be notified.');">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-send"></i> Publish Project
                                </button>
                            </form>
                            <p class="small text-muted mb-0">Publishing will change status to PUBLISHED and notify assigned
                                auditors.</p>

                        @elseif($project->status === 'PUBLISHED')
                            @can('start', $project)
                                <!-- Start Project -->
                                <form method="POST" action="{{ route('projects.start', $project) }}"
                                    onsubmit="return confirm('Are you ready to start this project?');">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 mb-2">
                                        <i class="bi bi-play-circle"></i> Start Project
                                    </button>
                                </form>
                                <p class="small text-muted mb-0">Starting the project will set status to ON PROGRESS.</p>
                            @endcan

                        @elseif($project->status === 'ON_PROGRESS' && auth()->user()->isAuditor() && $project->auditors->contains('user_id', auth()->id()))
                            <!-- Mark as Done -->
                            <form method="POST" action="{{ route('projects.markAsDone', $project) }}"
                                onsubmit="return confirm('Mark this project as done? It will be sent for review.');">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 mb-2">
                                    <i class="bi bi-check-circle"></i> Mark as Done
                                </button>
                            </form>
                            <p class="small text-muted mb-0">This will change status to WAITING and notify the admin for review.
                            </p>

                        @elseif($project->status === 'WAITING')
                            @can('review', $project)
                                <!-- Review & Close -->
                                <a href="{{ route('reviews.create', $project) }}" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-star"></i> Review & Close
                                </a>
                                <p class="small text-muted mb-2">Review auditor performance and close this project.</p>
                            @endcan

                            @can('cancelSubmission', $project)
                                <!-- Cancel Submission -->
                                <form method="POST" action="{{ route('projects.cancelSubmission', $project) }}"
                                    onsubmit="return confirm('Return this project to Draft?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100 mb-2">
                                        <i class="bi bi-x-circle"></i> Cancel Submission
                                    </button>
                                </form>
                                <p class="small text-muted mb-0">Return project to Draft status.</p>
                            @endcan

                            @can('cancelReviewSubmission', $project)
                                <!-- Cancel Review Submission -->
                                <form method="POST" action="{{ route('projects.cancelReviewSubmission', $project) }}"
                                    onsubmit="return confirm('Cancel your review submission? Project will return to On Progress.');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning w-100 mb-2">
                                        <i class="bi bi-arrow-counterclockwise"></i> Cancel Review Submission
                                    </button>
                                </form>
                                <p class="small text-muted mb-0">Return project to On Progress to continue working.</p>
                            @endcan

                        @elseif($project->status === 'CLOSED')
                            <div class="alert alert-success mb-2">
                                <i class="bi bi-check-circle-fill"></i> This project is closed.
                            </div>

                            @can('review', $project)
                                <!-- Edit Review -->
                                <a href="{{ route('reviews.edit', $project) }}" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-pencil"></i> Edit Review
                                </a>
                            @endcan
                            <p class="small text-muted mb-0">Modify review scores and feedback.</p>

                        @else
                            <p class="text-muted mb-0">No actions available.</p>
                        @endif

                        <hr>

                        <a href="{{ route('projects.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-left"></i> Back to Projects
                        </a>
                    </div>
                </div>

                <!-- Project Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Timeline</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Created:</strong><br>
                                <small>{{ $project->created_at->format('d M Y H:i') }}</small>
                            </li>
                            @if($project->published_at)
                                <li class="mb-2">
                                    <strong>Published:</strong><br>
                                    <small>{{ $project->published_at->format('d M Y H:i') }}</small>
                                </li>
                            @endif
                            @if($project->submitted_at)
                                <li class="mb-2">
                                    <strong>Submitted:</strong><br>
                                    <small>{{ $project->submitted_at->format('d M Y H:i') }}</small>
                                </li>
                            @endif
                            @if($project->closed_at)
                                <li class="mb-2">
                                    <strong>Closed:</strong><br>
                                    <small>{{ $project->closed_at->format('d M Y H:i') }}</small>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection