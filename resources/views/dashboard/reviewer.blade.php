@extends('layouts.app')

@section('title', 'Reviewer Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Reviewer Dashboard</h2>
                <p class="text-muted">Welcome, {{ auth()->user()->name }} | Review and close assigned projects</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <!-- Waiting for Review -->
            <div class="col-md-4">
                <div class="card bg-warning text-white h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 text-white-50">Waiting for Review</h6>
                        <h2 class="card-title mb-0">{{ $stats['waiting'] ?? 0 }}</h2>
                        <small class="d-block mt-1 text-black-50">Projects assigned to you</small>
                    </div>
                </div>
            </div>

            <!-- Closed -->
            <div class="col-md-4">
                <div class="card bg-success text-white h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 text-white-50">Closed</h6>
                        <h2 class="card-title mb-0">{{ $stats['closed'] ?? 0 }}</h2>
                        <small class="d-block mt-1 text-white-50">Projects you reviewed</small>
                    </div>
                </div>
            </div>

            <!-- Total Assigned -->
            <div class="col-md-4">
                <div class="card bg-info text-white h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 text-white-50">Total Assigned</h6>
                        <h2 class="card-title mb-0">{{ $stats['total_assigned'] ?? 0 }}</h2>
                        <small class="d-block mt-1 text-white-50">All time</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Projects -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Projects Assigned to You</h5>
                    </div>
                    <div class="card-body">
                        @if($assignedProjects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Department</th>
                                            <th>Assignment Type</th>
                                            <th>Status</th>
                                            <th>Submitted Date</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($assignedProjects as $project)
                                            <tr>
                                                <td>{{ $project->title }}</td>
                                                <td>{{ $project->department->name ?? '-' }}</td>
                                                <td>{{ $project->assignmentType->name ?? '-' }}</td>
                                                <td>
                                                    @if($project->status === 'WAITING')
                                                        <span class="badge bg-warning">Waiting</span>
                                                    @elseif($project->status === 'CLOSED')
                                                        <span class="badge bg-success">Closed</span>
                                                    @endif
                                                </td>
                                                <td>{{ $project->submitted_at?->format('d M Y') ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary">
                                                        View
                                                    </a>
                                                    @if($project->status === 'WAITING')
                                                        @can('review', $project)
                                                            <a href="{{ route('reviews.create', $project) }}"
                                                                class="btn btn-sm btn-success">
                                                                <i class="bi bi-star"></i> Review & Close
                                                            </a>
                                                        @endcan
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h5>No Projects Assigned</h5>
                                <p class="mb-0">You don't have any projects assigned to you for review yet.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection