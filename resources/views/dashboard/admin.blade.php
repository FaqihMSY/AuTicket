@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Dashboard</h2>
                <p class="text-muted">Selamat datang, {{ auth()->user()->name }} |
                    @if(auth()->user()->isAdmin())
                        Menampilkan semua proyek
                    @else
                        Menampilkan proyek yang Anda publikasikan
                    @endif
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <!-- Draft -->
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('projects.index', ['status' => 'DRAFT']) }}" class="text-decoration-none">
                    <div class="card bg-secondary text-white h-100 shadow-sm hover-scale">
                        <div class="card-body text-center px-2">
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Draf</h6>
                            <h2 class="card-title mb-0">{{ $stats['draft'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Klik untuk lihat <i
                                    class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>

            <!-- Published (Ready) -->
            <div class="col-6 col-md-4 col-lg">
                <a href="{{ route('projects.index', ['status' => 'PUBLISHED']) }}" class="text-decoration-none">
                    <div class="card bg-info text-white h-100 shadow-sm hover-scale">
                        <div class="card-body text-center px-2">
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Dipublikasikan</h6>
                            <h2 class="card-title mb-0">{{ $stats['published'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Klik untuk lihat <i
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
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Sedang Berjalan</h6>
                            <h2 class="card-title mb-0">{{ $stats['active'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Klik untuk lihat <i
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
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Menunggu Review</h6>
                            <h2 class="card-title mb-0">{{ $stats['waiting'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-black-50" style="font-size: 0.7rem;">Klik untuk lihat <i
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
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Selesai</h6>
                            <h2 class="card-title mb-0">{{ $stats['closed'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Klik untuk lihat <i
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
                            <h6 class="card-subtitle mb-2 text-white-50" style="font-size: 0.8rem;">Terlambat</h6>
                            <h2 class="card-title mb-0">{{ $stats['overdue'] ?? 0 }}</h2>
                            <small class="d-block mt-1 text-white-50" style="font-size: 0.7rem;">Klik untuk lihat <i
                                    class="bi bi-arrow-right"></i></small>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Quick Actions</h5>
                    </div>
                    <div class="card-body">
                        <a href="{{ route('projects.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create New Project
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Team Performance</h5>
                    </div>
                    <div class="card-body">
                        {{-- <p class="text-muted mb-3">Monitor auditor performance and review history</p> --}}
                        <a href="{{ route('auditors.index') }}" class="btn btn-primary">
                            View Auditor Performance
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Waiting Approval Projects -->
        @if(isset($waitingProjects) && $waitingProjects->count() > 0)
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Projects Waiting Approval</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Assignment Type</th>
                                            <th>Submitted Date</th>
                                            <th>Deadline</th>
                                            <th>Auditors</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($waitingProjects as $project)
                                            <tr>
                                                <td>{{ $project->title }}</td>
                                                <td>{{ $project->assignmentType->name }}</td>
                                                <td>{{ $project->submitted_at?->format('d M Y') ?? '-' }}</td>
                                                <td>
                                                    @if($project->isDueSoon())
                                                        <span class="bg-danger text-white px-2 py-1 rounded fw-bold">
                                                            {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                                                        </span>
                                                    @else
                                                        {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}
                                                    @endif
                                                </td>
                                                <td>{{ $project->auditors->pluck('user.name')->join(', ') }}</td>
                                                <td>
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary">
                                                        View
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
        @endif
    </div>
@endsection