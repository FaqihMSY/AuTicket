@extends('layouts.app')

@section('title', 'Menunggu Persetujuan')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>Menunggu Persetujuan</h2>
                <p class="text-muted">Proyek draf yang diajukan oleh orang lain yang menunggu persetujuan Anda</p>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke Semua Proyek
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
                                    <label class="form-label small">Departemen</label>
                                    <select name="department" class="form-select">
                                        <option value="">Semua Departemen</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>
                                                {{ $dept->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            @endif
                            <div class="col-md-{{ auth()->user()->isAdmin() ? '6' : '9' }}">
                                <label class="form-label small">Cari</label>
                                <input type="text" name="search" class="form-control" placeholder="Cari judul atau nama pengaju..."
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
                                            <th style="min-width: 200px;">Judul</th>
                                            <th style="min-width: 150px;">Diajukan Oleh</th>
                                            <th style="min-width: 120px;">Departemen</th>
                                            <th style="min-width: 150px;">Jenis Penugasan</th>
                                            <th style="min-width: 100px;">Prioritas</th>
                                            <th style="min-width: 120px;">Tenggat</th>
                                            <th style="min-width: 150px;">Auditor yang Ditugaskan</th>
                                            <th style="min-width: 120px;">Diajukan</th>
                                            <th style="min-width: 150px;">Aksi</th>
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
                                                           title="Lihat Detail">
                                                            <i class="bi bi-eye"></i> Lihat
                                                        </a>
                                                        @can('publish', $project)
                                                            <form action="{{ route('projects.publish', $project) }}" 
                                                                  method="POST" 
                                                                  class="d-inline"
                                                                  onsubmit="return confirm('Setujui dan publikasikan proyek ini?')">
                                                                @csrf
                                                                <button type="submit" 
                                                                        class="btn btn-sm btn-success"
                                                                        title="Setujui & Publikasikan">
                                                                    <i class="bi bi-check-circle"></i> Setujui
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
                                    Menampilkan {{ $projects->firstItem() ?? 0 }} sampai {{ $projects->lastItem() ?? 0 }} dari
                                    {{ $projects->total() }} proyek menunggu persetujuan
                                </div>
                                <div>
                                    {{ $projects->links() }}
                                </div>
                            </div>
                        @else
                            <div class="alert alert-success">
                                <h5><i class="bi bi-check-circle"></i> Semua Beres!</h5>
                                <p class="mb-0">Tidak ada proyek yang menunggu persetujuan saat ini. Semua draf telah direview.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
