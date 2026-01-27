@extends('layouts.app')

@section('title', 'Projects')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Projects</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-info me-2 text-white" data-bs-toggle="modal"
                    data-bs-target="#importModal">
                    <i class="bi bi-upload"></i> Import CSV
                </button>
                <a href="{{ route('projects.export', request()->query()) }}" class="btn btn-success me-2">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                </a>
                @if(auth()->user()->canManageProjects())
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create New Project
                    </a>
                @endif
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('projects.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <select name="status" class="form-select">
                                    <option value="">All Status</option>
                                    <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draft</option>
                                    <option value="PUBLISHED" {{ request('status') == 'PUBLISHED' ? 'selected' : '' }}>
                                        Published</option>
                                    <option value="ON_PROGRESS" {{ request('status') == 'ON_PROGRESS' ? 'selected' : '' }}>On
                                        Progress</option>
                                    <option value="WAITING" {{ request('status') == 'WAITING' ? 'selected' : '' }}>Waiting
                                        Approval</option>
                                    <option value="CLOSED" {{ request('status') == 'CLOSED' ? 'selected' : '' }}>Closed
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="sort" class="form-select">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Newest Created
                                    </option>
                                    <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>Closest
                                        Deadline</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Search by title..."
                                    value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if($projects->count() > 0)
                            <div class="table-responsive" style="overflow-x: auto;">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 200px;">Title</th>
                                            <th style="min-width: 150px;">Assignment Type</th>
                                            <th style="min-width: 100px;">Status</th>
                                            <th style="min-width: 100px;">Priority</th>
                                            <th style="min-width: 120px;">Deadline</th>
                                            <th style="min-width: 150px;">Auditors</th>
                                            <th style="min-width: 100px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($projects as $project)
                                            <tr>
                                                <td>{{ $project->title }}</td>
                                                <td>{{ $project->assignmentType->name }}</td>
                                                <td>
                                                    @if($project->status === 'DRAFT')
                                                        <span class="badge bg-secondary">Draft</span>
                                                    @elseif($project->status === 'PUBLISHED')
                                                        <span class="badge bg-info">Published</span>
                                                    @elseif($project->status === 'ON_PROGRESS')
                                                        <span class="badge bg-primary">On Progress</span>
                                                    @elseif($project->status === 'WAITING')
                                                        <span class="badge bg-warning">Waiting</span>
                                                    @else
                                                        <span class="badge bg-success">Closed</span>
                                                    @endif
                                                </td>
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
                                                <td>{{ $project->auditors->pluck('user.name')->take(2)->join(', ') }}{{ $project->auditors->count() > 2 ? '...' : '' }}
                                                </td>
                                                <td>
                                                    <a href="{{ route('projects.show', $project) }}"
                                                        class="btn btn-sm btn-primary">View</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    Showing {{ $projects->firstItem() ?? 0 }} to {{ $projects->lastItem() ?? 0 }} of
                                    {{ $projects->total() }} projects
                                </div>
                                <div>
                                    {{ $projects->links() }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h5>No Projects Found</h5>
                                <p class="mb-0">There are no projects to display.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Import Modal -->
    <div class="modal fade" id="importModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Import Projects</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('projects.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p>Upload a CSV file to import projects. The format must match the Export format.</p>
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">CSV File</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        </div>
                        <div class="alert alert-warning small">
                            <i class="bi bi-exclamation-triangle"></i> Ensure "Auditor Emails" are correct. Invalid emails
                            will be ignored.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection