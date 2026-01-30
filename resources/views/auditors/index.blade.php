@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Kinerja Auditor</h2>
            <a href="{{ route('auditors.export') }}" class="btn btn-success">
                <i class="bi bi-file-earmark-spreadsheet"></i> Ekspor CSV
            </a>
        </div>

        <div class="card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Spesialisasi</th>
                                <th>Skor Kinerja</th>
                                <th>Proyek Selesai</th>
                                <th>Proyek Aktif</th>
                                <th>Beban Kerja</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($auditors as $auditor)
                                <tr>
                                    <td>
                                        <strong>{{ $auditor->user->name }}</strong>
                                    </td>
                                    <td>{{ $auditor->specialization ?? '-' }}</td>
                                    <td>{{ number_format($auditor->performance_score, 0) }}/100</td>
                                    <td>{{ $auditor->completed_projects_count ?? 0 }}</td>
                                    <td>{{ $auditor->active_projects_count ?? 0 }}</td>
                                    <td>
                                        <span class="badge {{ $auditor->getWorkloadColorClass() }}">
                                            {{ $auditor->getWorkloadLabel() }} ({{ $auditor->getWorkloadScore() }})
                                        </span>
                                    </td>
                                    <td>
                                        <a href="{{ route('auditors.show', $auditor) }}" class="btn btn-sm btn-primary">
                                            Lihat Profil
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted">Tidak ada auditor ditemukan</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection