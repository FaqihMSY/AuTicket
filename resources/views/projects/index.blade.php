@extends('layouts.app')

@section('title', 'Proyek')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Proyek</h2>
            </div>
            <div class="col-md-6 text-end">
                <button type="button" class="btn btn-info me-2 text-white" data-bs-toggle="modal"
                    data-bs-target="#importModal">
                    <i class="bi bi-upload"></i> Impor CSV
                </button>
                <a href="{{ route('projects.export', request()->query()) }}" class="btn btn-success me-2">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor CSV
                </a>
                @if(auth()->user()->canManageProjects() || auth()->user()->isAuditor())
                    <a href="{{ route('projects.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Buat Proyek Baru
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
                                    <option value="">Semua Status</option>
                                    <option value="DRAFT" {{ request('status') == 'DRAFT' ? 'selected' : '' }}>Draf</option>
                                    <option value="PUBLISHED" {{ request('status') == 'PUBLISHED' ? 'selected' : '' }}>Dipublikasikan</option>
                                    <option value="ON_PROGRESS" {{ request('status') == 'ON_PROGRESS' ? 'selected' : '' }}>Sedang Berjalan</option>
                                    <option value="WAITING" {{ request('status') == 'WAITING' ? 'selected' : '' }}>Menunggu Review</option>
                                    <option value="CLOSED" {{ request('status') == 'CLOSED' ? 'selected' : '' }}>Selesai</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="sort" class="form-select">
                                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Terbaru</option>
                                    <option value="deadline" {{ request('sort') == 'deadline' ? 'selected' : '' }}>Tenggat Terdekat</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Cari judul proyek..."
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
                                            <th style="min-width: 200px;">Judul</th>
                                            <th style="min-width: 150px;">Jenis Penugasan</th>
                                            <th style="min-width: 100px;">Status</th>
                                            <th style="min-width: 100px;">Prioritas</th>
                                            <th style="min-width: 120px;">Tenggat</th>
                                            <th style="min-width: 150px;">Auditor</th>
                                            <th style="min-width: 100px;">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($projects as $project)
                                            <tr>
                                                <td>{{ $project->title }}</td>
                                                <td>{{ $project->assignmentType->name }}</td>
                                                <td>
                                                    <span class="badge {{ project_status_badge_class($project->status) }}">
                                                        {{ project_status_label($project->status) }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <span class="badge {{ project_priority_badge_class($project->priority) }}">
                                                        {{ project_priority_label($project->priority) }}
                                                    </span>
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
                                                        class="btn btn-sm btn-primary">Lihat</a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3 d-flex justify-content-between align-items-center">
                                <div>
                                    Menampilkan {{ $projects->firstItem() ?? 0 }} sampai {{ $projects->lastItem() ?? 0 }} dari
                                    {{ $projects->total() }} proyek
                                </div>
                                <div>
                                    {{ $projects->links() }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info">
                                <h5>Proyek Tidak Ditemukan</h5>
                                <p class="mb-0">Tidak ada proyek untuk ditampilkan.</p>
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
                    <h5 class="modal-title">Impor Proyek</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form action="{{ route('projects.import') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <p>Unggah file CSV untuk mengimpor proyek. Format harus sesuai dengan format Ekspor.</p>
                        <div class="mb-3">
                            <label for="csv_file" class="form-label">File CSV</label>
                            <input type="file" class="form-control" id="csv_file" name="csv_file" accept=".csv" required>
                        </div>
                        <div class="alert alert-warning small">
                            <i class="bi bi-exclamation-triangle"></i> Pastikan "Auditor Emails" benar. Email yang tidak valid akan diabaikan.
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection