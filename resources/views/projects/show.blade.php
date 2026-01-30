@extends('layouts.app')

@section('title', 'Detail Proyek')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-8">
                <h2>{{ $project->title }}</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyek</a></li>
                        <li class="breadcrumb-item active">{{ $project->title }}</li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-4 text-end">
                <span class="badge {{ project_status_badge_class($project->status) }} fs-5">
                    {{ strtoupper(project_status_label($project->status)) }}
                </span>
            </div>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Project Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Proyek</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless">
                            <tr>
                                <th width="200">Jenis Penugasan:</th>
                                <td>{{ $project->assignmentType->name }}</td>
                            </tr>
                            <tr>
                                <th>Prioritas:</th>
                                <td>
                                    <span class="badge {{ project_priority_badge_class($project->priority) }}">
                                        {{ project_priority_label($project->priority) }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <th>Tanggal Mulai:</th>
                                <td>{{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Tanggal Selesai:</th>
                                <td>{{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <th>Dibuat Oleh:</th>
                                <td>
                                    <strong>{{ $project->creator->name }}</strong>
                                    @if(auth()->id() === $project->created_by)
                                        <span class="badge bg-info">Anda</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Dibuat Pada:</th>
                                <td>{{ $project->created_at->format('d M Y H:i') }}</td>
                            </tr>
                            @if($project->status === 'DRAFT' && $project->assigned_manager_id)
                                <tr>
                                    <th>Ditugaskan ke Manajer:</th>
                                    <td>
                                        <strong>{{ $project->assignedManager->name }}</strong>
                                        ({{ user_role_label($project->assignedManager->role) }})
                                        @if(auth()->id() === $project->assigned_manager_id)
                                            <span class="badge bg-warning">Menunggu persetujuan Anda</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">Akan menyetujui dan mempublikasikan proyek ini</small>
                                    </td>
                                </tr>
                            @endif
                            @if($project->published_by)
                                <tr>
                                    <th>Dipublikasikan Oleh:</th>
                                    <td>
                                        <strong>{{ $project->publisher->name }}</strong>
                                        @if(auth()->id() === $project->published_by)
                                            <span class="badge bg-success">Anda</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">pada {{ $project->published_at->format('d M Y H:i') }}</small>
                                    </td>
                                </tr>
                            @endif
                            @if($project->reviewer_id)
                                <tr>
                                    <th>Reviewer yang Ditunjuk:</th>
                                    <td>
                                        <strong>{{ $project->reviewer->name }}</strong>
                                        ({{ $project->reviewer->email }})
                                        @if(auth()->id() === $project->reviewer_id)
                                            <span class="badge bg-info">Anda</span>
                                        @endif
                                        <br>
                                        <small class="text-muted">Dapat mereview dan menutup proyek ini</small>
                                    </td>
                                </tr>
                            @endif
                        </table>

                        @if($project->description)
                            <hr>
                            <h6>Deskripsi::</h6>
                            <p>{{ $project->description }}</p>
                        @endif
                    </div>
                </div>

                <!-- Assigned Auditors -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Auditor yang Ditugaskan</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nama</th>
                                        <th>Spesialisasi</th>
                                        @if(auth()->user()->canManageProjects())
                                            <th>Skor Kinerja</th>
                                        @endif
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->auditors as $auditor)
                                        <tr>
                                            <td>{{ $auditor->user->name }}</td>
                                            <td>{{ $auditor->specialization ?? '-' }}</td>
                                            @if(auth()->user()->canManageProjects())
                                                <td>
                                                    <span class="badge bg-primary">{{ $auditor->performance_score }}/100</span>
                                                </td>
                                            @endif
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- File Attachments -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">Lampiran File</h5>
                    </div>
                    <div class="card-body">
                        @if($project->attachments->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Nama File</th>
                                            <th>Tipe</th>
                                            <th>Diunggah Oleh</th>
                                            <th>Tanggal</th>
                                            <th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($project->attachments as $attachment)
                                            <tr>
                                                <td>{{ $attachment->original_filename }}</td>
                                                <td>
                                                    @if($attachment->category === 'INSTRUCTION')
                                                        <span class="badge bg-info">Instruksi</span>
                                                    @else
                                                        <span class="badge bg-success">Hasil</span>
                                                    @endif
                                                </td>
                                                <td>{{ $attachment->uploader->name }}</td>
                                                <td>{{ $attachment->created_at->format('d M Y') }}</td>
                                                <td>
                                                    <a href="{{ route('attachments.download', $attachment) }}"
                                                        class="btn btn-sm btn-primary me-1">
                                                        <i class="bi bi-download"></i> Unduh
                                                    </a>
                                                    @can('delete', $attachment)
                                                        <form action="{{ route('attachments.destroy', $attachment) }}" method="POST"
                                                            onsubmit="return confirm('Apakah Anda yakin ingin menghapus file ini?');"
                                                            class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i> Hapus
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">Belum ada file yang dilampirkan.</p>
                        @endif
                    </div>
                </div>

                <!-- Upload Result (For Auditors in ON_PROGRESS) -->
                <!-- Upload Work Papers / Attachments (For Auditors in ON_PROGRESS) -->
                @if($project->status === 'ON_PROGRESS' && auth()->user()->isAuditor() && $project->auditors->contains('user_id', auth()->id()))
                    <div class="card mb-3 border-primary">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Unggah Kertas Kerja / Lampiran</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted small mb-3">
                                <i class="bi bi-info-circle"></i> File yang diunggah di sini akan langsung dapat dilihat oleh
                                Manajer.
                                Mengunggah file <strong>tidak</strong> secara otomatis mengirimkan (submit) proyek.
                            </p>
                            <form method="POST" action="{{ route('projects.uploadResult', $project) }}"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="mb-3">
                                    <label for="result_files" class="form-label">Pilih File (PDF/Excel/Word, maks 10MB per
                                        file)</label>
                                    <input type="file" class="form-control @error('result_files.*') is-invalid @enderror"
                                        id="result_files" name="result_files[]" accept=".pdf,.xlsx,.xls,.doc,.docx" multiple
                                        required>
                                    @error('result_files.*')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-upload"></i> Unggah File
                                </button>
                            </form>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Sidebar Actions -->
            <div class="col-lg-4">
                <!-- Workflow Actions -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Aksi</h6>
                    </div>
                    <div class="card-body">
                        @can('update', $project)
                            <a href="{{ route('projects.edit', $project) }}" class="btn btn-outline-primary w-100 mb-3">
                                <i class="bi bi-pencil"></i> Ubah Proyek
                            </a>
                            <hr>
                        @endcan

                        @if($project->status === 'DRAFT' && auth()->user()->canManageProjects())
                            <!-- Publish Project -->
                            <form method="POST" action="{{ route('projects.publish', $project) }}"
                                onsubmit="return confirm('Apakah Anda yakin ingin mempublikasikan proyek ini? Auditor akan diberitahu.');">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2">
                                    <i class="bi bi-send"></i> Publikasikan Proyek
                                </button>
                            </form>
                            <p class="small text-muted mb-0">Mempublikasikan akan mengubah status menjadi DIPUBLIKASIKAN dan
                                memberitahu auditor yang ditugaskan.</p>

                        @elseif($project->status === 'PUBLISHED')
                            @can('start', $project)
                                <!-- Start Project -->
                                <form method="POST" action="{{ route('projects.start', $project) }}"
                                    onsubmit="return confirm('Apakah Anda siap untuk memulai proyek ini?');">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 mb-2">
                                        <i class="bi bi-play-circle"></i> Mulai Proyek
                                    </button>
                                </form>
                                <p class="small text-muted mb-0">Memulai proyek akan mengubah status menjadi SEDANG BERJALAN.</p>
                            @endcan

                        @elseif($project->status === 'ON_PROGRESS' && auth()->user()->isAuditor() && $project->auditors->contains('user_id', auth()->id()))
                            <!-- Mark as Done -->
                            <form method="POST" action="{{ route('projects.markAsDone', $project) }}"
                                onsubmit="return confirm('Tandai proyek ini sebagai selesai? Proyek akan dikirim untuk direview.');">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 mb-2">
                                    <i class="bi bi-check-circle"></i> Tandai Selesai
                                </button>
                            </form>
                            <p class="small text-muted mb-0">Ini akan mengubah status menjadi MENUNGGU REVIEW dan memberitahu
                                manajer untuk direview.</p>

                        @elseif($project->status === 'WAITING')
                            @can('review', $project)
                                <!-- Review & Close -->
                                <a href="{{ route('reviews.create', $project) }}" class="btn btn-primary w-100 mb-2">
                                    <i class="bi bi-star"></i> Review & Tutup
                                </a>
                                <p class="small text-muted mb-2">Review kinerja auditor dan tutup proyek ini.</p>
                            @endcan

                            @if(auth()->user()->canManageProjects() && !$project->reviewer_id)
                                <!-- Delegate to Supervisor (Optional) -->
                                <button type="button" class="btn btn-outline-info w-100 mb-2" data-bs-toggle="modal"
                                    data-bs-target="#requestReviewModal">
                                    <i class="bi bi-person-check"></i> Delegasikan ke Reviewer (Opsional)
                                </button>
                                <p class="small text-muted mb-2">Opsional: Tugaskan seorang reviewer untuk mereview dan menutup
                                    proyek ini atas nama Anda.</p>
                            @endif

                            @can('cancelSubmission', $project)
                                <!-- Cancel Submission -->
                                <form method="POST" action="{{ route('projects.cancelSubmission', $project) }}"
                                    onsubmit="return confirm('Kembalikan proyek ini ke Draf?');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger w-100 mb-2">
                                        <i class="bi bi-x-circle"></i> Batalkan Submit
                                    </button>
                                </form>
                                <p class="small text-muted mb-0">Kembalikan proyek ke status Draf.</p>
                            @endcan

                            @can('cancelReviewSubmission', $project)
                                <!-- Cancel Review Submission -->
                                <form method="POST" action="{{ route('projects.cancelReviewSubmission', $project) }}"
                                    onsubmit="return confirm('Batalkan pengiriman review Anda? Proyek akan kembali ke status Sedang Berjalan.');">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-warning w-100 mb-2">
                                        <i class="bi bi-arrow-counterclockwise"></i> Batalkan Submit Review
                                    </button>
                                </form>
                                <p class="small text-muted mb-0">Kembalikan proyek ke status Sedang Berjalan untuk melanjutkan
                                    pengerjaan.</p>
                            @endcan

                        @elseif($project->status === 'CLOSED')
                            <div class="alert alert-success mb-2">
                                <i class="bi bi-check-circle-fill"></i> Proyek ini telah selesai.
                            </div>

                            @can('review', $project)
                                <!-- Edit Review -->
                                <a href="{{ route('reviews.edit', $project) }}" class="btn btn-outline-primary w-100 mb-2">
                                    <i class="bi bi-pencil"></i> Ubah Review
                                </a>
                            @endcan
                            <p class="small text-muted mb-0">Ubah nilai review dan umpan balik.</p>

                        @else
                            <p class="text-muted mb-0">Tidak ada aksi tersedia.</p>
                        @endif

                        <hr>

                        <a href="{{ route('projects.index') }}" class="btn btn-secondary w-100">
                            <i class="bi bi-arrow-left"></i> Kembali ke Proyek
                        </a>
                    </div>
                </div>

                <!-- Project Timeline -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Linimasa</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Dibuat:</strong><br>
                                <small>{{ $project->created_at->format('d M Y H:i') }}</small>
                            </li>
                            @if($project->published_at)
                                <li class="mb-2">
                                    <strong>Dipublikasikan:</strong><br>
                                    <small>{{ $project->published_at->format('d M Y H:i') }}</small>
                                </li>
                            @endif
                            @if($project->submitted_at)
                                <li class="mb-2">
                                    <strong>Diserahkan:</strong><br>
                                    <small>{{ $project->submitted_at->format('d M Y H:i') }}</small>
                                </li>
                            @endif
                            @if($project->closed_at)
                                <li class="mb-2">
                                    <strong>Selesai:</strong><br>
                                    <small>{{ $project->closed_at->format('d M Y H:i') }}</small>
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Request Review Modal -->
    @if(auth()->user()->canManageProjects() && $project->status === 'WAITING' && !$project->reviewer_id)
        <div class="modal fade" id="requestReviewModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Minta Review dari Reviewer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form method="POST" action="{{ route('projects.assignReviewer', $project) }}">
                        @csrf
                        @method('PATCH')
                        <div class="modal-body">
                            <p>Tunjuk seorang reviewer yang akan memiliki izin untuk menutup proyek ini setelah direview.</p>
                            <div class="mb-3">
                                <label for="reviewer_id" class="form-label">Pilih Reviewer</label>
                                <select class="form-select" id="reviewer_id" name="reviewer_id" required>
                                    <option value="">Pilih reviewer...</option>
                                    @foreach(\App\Models\User::where('role', 'reviewer')->get() as $reviewer)
                                        <option value="{{ $reviewer->id }}">
                                            {{ $reviewer->name }} ({{ $reviewer->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">Tunjuk Reviewer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection