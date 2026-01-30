@extends('layouts.app')

@section('title', 'Riwayat Proyek')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Riwayat Proyek</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('projects.export', array_merge(request()->query(), ['status' => 'CLOSED'])) }}"
                    class="btn btn-success me-2">
                    <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor CSV
                </a>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-body">
                        <form method="GET" action="{{ route('projects.history') }}" class="row g-3">
                            <div class="col-md-10">
                                <input type="text" name="search" class="form-control"
                                    placeholder="Cari judul atau jenis penugasan..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Cari</button>
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
                                            <th style="min-width: 120px;">Tanggal Selesai</th>
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
                                                    {{ $project->closed_at ? $project->closed_at->format('d M Y') : '-' }}
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
                                <h5>Riwayat Tidak Ditemukan</h5>
                                <p class="mb-0">Tidak ada proyek selesai untuk ditampilkan.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection