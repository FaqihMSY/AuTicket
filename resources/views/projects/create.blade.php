@extends('layouts.app')

@section('title', 'Buat Proyek')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Buat Proyek Baru</h2>
                <p class="text-muted">Buat proyek audit baru dan tugaskan auditor.</p>
                @if(auth()->user()->isAuditor() && !auth()->user()->canManageProjects())
                    <div class="alert alert-warning d-inline-block py-2 px-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Mode Draf:</strong> Proyek Anda akan disimpan sebagai Draf dan memerlukan persetujuan Manajer.
                    </div>
                @endif
            </div>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informasi Proyek</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data"
                            id="createProjectForm">
                            @csrf

                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Judul Proyek <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Assignment Type -->
                            <div class="mb-3">
                                <label for="assignment_type_id" class="form-label">Jenis Penugasan <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('assignment_type_id') is-invalid @enderror"
                                    id="assignment_type_id" name="assignment_type_id" required>
                                    <option value="">Pilih Jenis Penugasan</option>
                                    @foreach($assignmentTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('assignment_type_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }} - {{ $type->description }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('assignment_type_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="department_id" class="form-label">Departemen <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('department_id') is-invalid @enderror"
                                    id="department_id" name="department_id" required>
                                    <option value="">Pilih Departemen</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Dates -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Tanggal Mulai <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                                        required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">Tanggal Selesai <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('end_date') is-invalid @enderror"
                                        id="end_date" name="end_date" value="{{ old('end_date') }}" required>
                                    @error('end_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Priority -->
                            <div class="mb-3">
                                <label for="priority" class="form-label">Prioritas <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority"
                                    name="priority" required>
                                    <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>Rendah</option>
                                    <option value="MEDIUM" {{ old('priority', 'MEDIUM') == 'MEDIUM' ? 'selected' : '' }}>
                                        Sedang</option>
                                    <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>Tinggi</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Deskripsi</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                    name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Instruction Files -->
                            <div class="mb-3">
                                <label for="instruction_files" class="form-label">File Instruksi (PDF/Excel/Word, maks 10MB per file)</label>
                                <input type="file" class="form-control @error('instruction_files.*') is-invalid @enderror"
                                    id="instruction_files" name="instruction_files[]" accept=".pdf,.xlsx,.xls,.doc,.docx"
                                    multiple>
                                <small class="text-muted">Opsional: Unggah dokumen instruksi untuk auditor (bisa banyak file)</small>
                                @error('instruction_files.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Auditor Selection -->
                            <div class="mb-4">
                                <label class="form-label">Tugaskan Auditor <span class="text-danger">*</span></label>

                                <!-- Sort Options -->
                                <div class="mb-3">
                                    <label for="sortBy" class="form-label">Urutkan berdasarkan:</label>
                                    <select class="form-select form-select-sm" id="sortBy" style="width: 200px;">
                                        <option value="performance">Kinerja</option>
                                        <option value="availability">Ketersediaan</option>
                                        <option value="balanced">Seimbang</option>
                                    </select>
                                </div>

                                <!-- Auditors List -->
                                <div id="auditorsList" class="border rounded p-3" 
                                     data-current-auditor-id="{{ auth()->user()->isAuditor() ? auth()->user()->auditor->id : '' }}">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Memuat...</span>
                                        </div>
                                        <p class="mb-0 mt-2">Memuat auditor...</p>
                                    </div>
                                </div>

                                @error('auditor_ids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('auditor_ids.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Assign to Manager (Required for Staff) -->
                            @if(auth()->user()->isAuditor())
                                <div class="mb-4">
                                    <label for="assigned_manager_id" class="form-label">
                                        Tugaskan ke Manajer <span class="text-danger">*</span>
                                        <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip" 
                                           title="Pilih manajer yang akan menyetujui dan mempublikasikan proyek ini"></i>
                                    </label>
                                    <select class="form-select @error('assigned_manager_id') is-invalid @enderror" 
                                            id="assigned_manager_id" name="assigned_manager_id" required>
                                        <option value="">Pilih manajer...</option>
                                        @foreach($managers as $manager)
                                            <option value="{{ $manager->id }}" {{ old('assigned_manager_id') == $manager->id ? 'selected' : '' }}>
                                                {{ $manager->name }} ({{ ucfirst($manager->role) }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        Manajer ini akan mereview dan menyetujui draf proyek Anda
                                    </small>
                                    @error('assigned_manager_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <!-- Reviewer Selection -->
                            @if(auth()->user()->canManageProjects())
                                <div class="mb-4">
                                    <label for="reviewer_id" class="form-label">
                                        Tunjuk Reviewer (Opsional)
                                        <i class="bi bi-info-circle text-muted" data-bs-toggle="tooltip" 
                                           title="Jika ditunjuk, reviewer ini dapat menutup proyek setelah direview"></i>
                                    </label>
                                    <select class="form-select @error('reviewer_id') is-invalid @enderror" 
                                            id="reviewer_id" name="reviewer_id">
                                        <option value="">Tidak butuh reviewer</option>
                                        @foreach($reviewers as $reviewer)
                                            <option value="{{ $reviewer->id }}" {{ old('reviewer_id') == $reviewer->id ? 'selected' : '' }}>
                                                {{ $reviewer->name }} ({{ $reviewer->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">
                                        Reviewer dapat ditunjuk sekarang atau nanti saat status proyek MENUNGGU REVIEW
                                    </small>
                                    @error('reviewer_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            @endif

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Tambah Proyek
                                </button>
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary">Batal</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Bantuan</h6>
                    </div>
                    <div class="card-body">
                        <h6>Indikator Beban Kerja:</h6>
                        <ul class="list-unstyled">
                            <li><span class="badge workload-0">0</span> Tersedia</li>
                            <li><span class="badge workload-1">1</span> Sangat Ringan</li>
                            <li><span class="badge workload-2">2</span> Ringan</li>
                            <li><span class="badge workload-3">3</span> Sedang</li>
                            <li><span class="badge workload-4">4</span> Berat</li>
                            <li><span class="badge workload-5">5+</span> Penuh/Sibuk</li>
                        </ul>

                        <hr>

                        <h6>Tips:</h6>
                        <ul class="small">
                            <li>Pilih minimal satu auditor</li>
                            <li>Pertimbangkan beban kerja saat menugaskan</li>
                            <li>Skor kinerja yang lebih tinggi menunjukkan kinerja masa lalu yang lebih baik</li>
                            <li>Proyek akan disimpan sebagai DRAF sampai dipublikasikan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('js/auditor-selection.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Date validation
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            startDate.addEventListener('change', function () {
                endDate.min = this.value;
            });

            endDate.addEventListener('change', function () {
                if (this.value < startDate.value) {
                    alert('Tanggal selesai tidak boleh sebelum tanggal mulai');
                    this.value = '';
                }
            });
        });
    </script>
@endpush