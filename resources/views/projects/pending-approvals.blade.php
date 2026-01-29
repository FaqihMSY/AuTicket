@extends('layouts.app')

@section('title', 'Pending Approvals')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Pending Approvals</h2>
                <p class="text-muted">Draft projects submitted by others awaiting your approval</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to All Projects
                </a>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('projects.pendingApprovals') }}" class="row g-3">
                            @if(auth()->user()->isAdmin())
                                <div class="col-md-3">
                                    <label class="form-label small">Department</label>
                                    <select name="department" class="form-select">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-{{ auth()->user()->isAdmin() ? '6' : '9' }}">
                                <label class="form-label small">Search</label>
                                <input type="text" name="search" class="form-control" placeholder="Search by title or submitter name..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Filter
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approvals Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if($projects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 200px;">Title</th>
                                            <th style="min-width: 150px;">Submitted By</th>
                                            <th style="min-width: 120px;">Department</th>
                                            <th style="min-width: 150px;">Assignment Type</th>
                                            <th style="min-width: 100px;">Priority</th>
                                            <th style="min-width: 120px;">Deadline</th>
                                            <th style="min-width: 150px;">Assigned Auditors</th>
                                            <th style="min-width: 120px;">Submitted</th>
                                            <th style="min-width: 150px;">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($projects as $project)
                                            <tr>
                                                <td>
                                                    <strong>{{ $project->title }}</strong>
                                                </td>
                                                <td>
                                                    <div class="fw-medium">{{ $project->creator->name }}</div>
                                                    <small class="text-muted">{{ $project->creator->email }}</small>
                                                </td>
                                                <td>
                                                    <span class="badge bg-secondary">{{ $project->department->name }}</span>
                                                </td>
                                                <td>{{ $project->assignmentType->name }}</td>
                                                <td>
                                                    @if($project->priority === 'HIGH')
                                                        <span class="badge bg-danger">High</span>
                                                    @elseif($project->priority === 'MEDIUM')
                                                        <span class="badge bg-warning">Medium</span>
                                                    @else
                                                        <span class="badge bg-info">Low</span>
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
                                                    @if($project->auditors->count() > 0)
                                                        <div class="small">
                                                            @foreach($project->auditors->take(2) as $auditor)
                                                                <div>• {{ $auditor->user->name }}</div>
                                                            @endforeach
                                                            @if($project->auditors->count() > 2)
                                                                <div class="text-muted">+{{ $project->auditors->count() - 2 }} more</div>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No auditors assigned</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        {{ $project->created_at->diffForHumans() }}
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <a href="{{ route('projects.show', $project) }}" 
                                                           class="btn btn-sm btn-outline-primary"
                                                           title="View Details">
                                                            <i class="bi bi-eye"></i> View
                                                        </a>
                                                        @can('publish', $project)
                                                            <form action="{{ route('projects.publish', $project) }}" 
                                                                  method="POST" 
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('Approve and publish this project?')">
                                                                @csrf
                                                                <button type="submit" 
                                                                        class="btn btn-sm btn-success"
                                                                        title="Approve & Publish">
                                                                    <i class="bi bi-check-circle"></i> Approve
                                                                </button>
                                                            </form>
                                                        @endcan
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }} of
                                    {{ $projects->total() }} pending approvals
                                </div>
                                <div>
                                    {{ $projects->links() }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <h5><i class="bi bi-check-circle"></i> All Clear!</h5>
                                <p class="mb-0">There are no pending approvals at the moment. All drafts have been reviewed.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
