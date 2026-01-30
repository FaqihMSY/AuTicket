@extends('layouts.app')

@section('title', 'Dashboard Reviewer')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Dashboard Reviewer</h2>
                <p class="text-muted">Selamat datang, {{ auth()->user()->name }} | Review dan tutup proyek yang ditugaskan
                </p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-3 mb-4">
            <!-- Waiting for Review -->
            <div class="col-md-4">
                <div class="card bg-warning text-white h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 text-white-50">Menunggu Review</h6>
                        <h2 class="card-title mb-0">{{ $stats['waiting'] ?? 0 }}</h2>
                        <small class="d-block mt-1 text-black-50">Proyek yang ditugaskan kepada Anda</small>
                    </div>
                </div>
            </div>

            <!-- Closed -->
            <div class="col-md-4">
                <div class="card bg-success text-white h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 text-white-50">Selesai</h6>
                        <h2 class="card-title mb-0">{{ $stats['closed'] ?? 0 }}</h2>
                        <small class="d-block mt-1 text-white-50">Proyek yang Anda review</small>
                    </div>
                </div>
            </div>

            <!-- Total Assigned -->
            <div class="col-md-4">
                <div class="card bg-info text-white h-100 shadow-sm">
                    <div class="card-body text-center">
                        <h6 class="card-subtitle mb-2 text-white-50">Total Ditugaskan</h6>
                        <h2 class="card-title mb-0">{{ $stats['total_assigned'] ?? 0 }}</h2>
                        <small class="d-block mt-1 text-white-50">Sepanjang waktu</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assigned Projects -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Proyek yang Ditugaskan kepada Anda</h5>
                    </div>
                    <div class="card-body">
                        @if($assignedProjects->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Judul</th>
                                            <th>Departemen</th>
                                            <th>Jenis Penugasan</th>
                                            <th>Status</th>
                                            <th>Tanggal Submit</th>
                                            <th>Aksi</th>
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
                                                        <span class="badge bg-warning">Menunggu Review</span>
                                                    @elseif($project->status === 'CLOSED')
                                                        <span class="badge bg-success">Selesai</span>
                                                    @endif
                                                </td>
                                                <td>{{ $project->submitted_at?->format('d M Y') ?? '-' }}</td>
                                                <td>
                                                    <a href="{{ route('projects.show', $project) }}" class="btn btn-sm btn-primary">
                                                        Lihat
                                                    </a>
                                                    @if($project->status === 'WAITING')
                                                        @can('review', $project)
                                                            <a href="{{ route('reviews.create', $project) }}"
                                                                class="btn btn-sm btn-success">
                                                                <i class="bi bi-star"></i> Review & Tutup
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
                                <h5>Tidak Ada Proyek yang Ditugaskan</h5>
                                <p class="mb-0">Anda belum memiliki proyek yang ditugaskan untuk di-review.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection