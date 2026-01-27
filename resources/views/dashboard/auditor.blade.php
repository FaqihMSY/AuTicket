@extends('layouts.app')

@section('title', 'My Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>My Dashboard</h2>
                <p class="text-muted">Welcome, {{ auth()->user()->name }}</p>
            </div>
        </div>

        <!-- Personal Statistics -->
        <div class="row g-3 mb-4 justify-content-center">
            <!-- Draft (My Drafts) -->
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('projects.index', ['status' => 'DRAFT']) }}" class="text-decoration-none">
                    <div class="card bg-secondary text-white h-100 shadow-sm hover-scale">
                        <div class="card-body text-center px-2">
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">My Drafts</h6>
                            <h2 class="card-title mb-0">{{ $stats['draft'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Click to view <i
                                    class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Published (Open Projects) -->
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('projects.index', ['status' => 'PUBLISHED']) }}" class="text-decoration-none">
                    <div class="card bg-info text-white h-100 shadow-sm hover-scale">
                        <div class="card-body text-center px-2">
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Open Projects</h6>
                            <h2 class="card-title mb-0">{{ $stats['published'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Click to view <i
                                    class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- On Progress -->
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('projects.index', ['status' => 'ON_PROGRESS']) }}" class="text-decoration-none">
                    <div class="card bg-primary text-white h-100 shadow-sm hover-scale">
                        <div class="card-body text-center px-2">
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">On Progress</h6>
                            <h2 class="card-title mb-0">{{ $stats['active'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Click to view <i
                                    class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Waiting -->
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('projects.index', ['status' => 'WAITING']) }}" class="text-decoration-none">
                    <div class="card bg-warning text-white h-100 shadow-sm hover-scale">
                        <div class="card-body text-center px-2">
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Waiting</h6>
                            <h2 class="card-title mb-0">{{ $stats['waiting'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-black-50" style="font-size: 0.7rem;">Click to view <i
                                    class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Closed -->
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('projects.index', ['status' => 'CLOSED']) }}" class="text-decoration-none">
                    <div class="card bg-success text-white h-100 shadow-sm hover-scale">
                        <div class="card-body text-center px-2">
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Closed</h6>
                            <h2 class="card-title mb-0">{{ $stats['closed'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Click to view <i
                                    class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Overdue -->
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('projects.index', ['filter' => 'overdue']) }}" class="text-decoration-none">
                    <div class="card bg-danger text-white h-100 shadow-sm hover-scale">
                        <div class="card-body text-center px-2">
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Overdue</h6>
                            <h2 class="card-title mb-0">{{ $stats['overdue'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Click to view <i
                                    class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Performance Score (No Link) -->
            <div class="col-6 col-md-4 col-lg">
                <div class="card bg-info text-white h-100 shadow-sm">
                    <div class="card-body text-center px-2">
                        <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Score</h6>
                        <h2 class="card-title mb-0">{{ auth()->user()->auditor->performance_score ?? 0 }}</h2>
                        <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">(Your Rating)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- My Active Projects -->
        @if(isset($activeProjects) && $activeProjects->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">My Active Projects</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Assignment Type</th>
                                            <th>Status</th>
                                            <th>Deadline</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($activeProjects as $project)
                                            <tr>
                                                <td>{{ $project->title }}</td>
                                                <td>{{ $project->assignmentType->name }}</td>
                                                <td>
                                                    @if($project->status === 'ON_PROGRESS')
                                                        <span class="badge bg-primary">On Progress</span>
                                                    @elseif($project->status === 'WAITING')
                                                        <span class="badge bg-warning">Waiting Approval</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($project->isDueSoon())
                                                        <span class="bg-danger text-white px-2 py-1 rounded fw-bold">
                                                            {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                                                        </span>
                                                    @else
                                                        {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary">
                                                        View Details
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                <div class="col-12">
                    <div class="alert alert-info">
                        <h5>No Active Projects</h5>
                        <p class="mb-0">You don't have any active projects at the moment.</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection