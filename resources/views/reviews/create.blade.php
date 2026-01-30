@extends('layouts.app')

@section('title', 'Review & Tutup Proyek')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Review & Tutup Proyek</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Proyek</a></li>
                        <li class="breadcrumb-item"><a
                                href="{{ route('projects.show', $project) }}">{{ $project->title }}</a></li>
                        <li class="breadcrumb-item active">Review</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Project Summary -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Ringkasan Proyek</h5>
                    </div>
                    <div class="card-body">
                        <h6>{{ $project->title }}</h6>
                        <p class="mb-2"><strong>Jenis Penugasan:</strong> {{ $project->assignmentType->name }}</p>
                        <p class="mb-2"><strong>Durasi:</strong>
                            {{ \Carbon\Carbon::parse($project->start_date)->format('d M Y') }} -
                            {{ \Carbon\Carbon::parse($project->end_date)->format('d M Y') }}</p>
                        <p class="mb-0"><strong>Status:</strong> <span class="badge bg-warning">MENUNGGU REVIEW</span></p>
                    </div>
                </div>

                <!-- Review Form -->
                <form method="POST" action="{{ route('reviews.store', $project) }}" id="reviewForm">
                    @csrf

                    @foreach($project->auditors as $index => $auditor)
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">Review untuk {{ $auditor->user->name }}</h5>
                                <small class="text-muted">{{ $auditor->specialization ?? 'Auditor Umum' }}</small>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <label for="overall_rating_{{ $auditor->id }}" class="form-label">
                                        Skor Keseluruhan <span class="text-danger">*</span>
                                    </label>
                                    <input type="number"
                                        class="form-control @error('reviews.' . $index . '.overall_rating') is-invalid @enderror"
                                        id="overall_rating_{{ $auditor->id }}" name="reviews[{{ $index }}][overall_rating]"
                                        min="0" max="100"
                                        value="{{ old('reviews.' . $index . '.overall_rating', $suggestedScores[$auditor->id]['overall_rating'] ?? 85) }}"
                                        placeholder="Dihitung otomatis dari 4 skor di bawah" readonly required>
                                    @error('reviews.' . $index . '.overall_rating')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <input type="hidden" name="reviews[{{ $index }}][reviewee_id]"
                                        value="{{ $auditor->user->id }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="timeliness_rating_{{ $auditor->id }}" class="form-label">Ketepatan
                                            Waktu</label>
                                        <input type="number"
                                            class="form-control @error('reviews.' . $index . '.timeliness_rating') is-invalid @enderror"
                                            id="timeliness_rating_{{ $auditor->id }}"
                                            name="reviews[{{ $index }}][timeliness_rating]" min="0" max="100"
                                            value="{{ old('reviews.' . $index . '.timeliness_rating', $suggestedScores[$auditor->id]['timeliness_rating'] ?? '') }}">
                                        @error('reviews.' . $index . '.timeliness_rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="completeness_rating_{{ $auditor->id }}"
                                            class="form-label">Kelengkapan</label>
                                        <input type="number"
                                            class="form-control @error('reviews.' . $index . '.completeness_rating') is-invalid @enderror"
                                            id="completeness_rating_{{ $auditor->id }}"
                                            name="reviews[{{ $index }}][completeness_rating]" min="1" max="100"
                                            value="{{ old('reviews.' . $index . '.completeness_rating', $suggestedScores[$auditor->id]['completeness_rating'] ?? '') }}">
                                        @error('reviews.' . $index . '.completeness_rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="quality_rating_{{ $auditor->id }}" class="form-label">Kualitas</label>
                                        <input type="number"
                                            class="form-control @error('reviews.' . $index . '.quality_rating') is-invalid @enderror"
                                            id="quality_rating_{{ $auditor->id }}" name="reviews[{{ $index }}][quality_rating]"
                                            min="1" max="100"
                                            value="{{ old('reviews.' . $index . '.quality_rating', $suggestedScores[$auditor->id]['quality_rating'] ?? '') }}">
                                        @error('reviews.' . $index . '.quality_rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="col-md-6 mb-3">
                                        <label for="communication_rating_{{ $auditor->id }}"
                                            class="form-label">Komunikasi</label>
                                        <input type="number"
                                            class="form-control @error('reviews.' . $index . '.communication_rating') is-invalid @enderror"
                                            id="communication_rating_{{ $auditor->id }}"
                                            name="reviews[{{ $index }}][communication_rating]" min="1" max="100"
                                            value="{{ old('reviews.' . $index . '.communication_rating', $suggestedScores[$auditor->id]['communication_rating'] ?? '') }}">
                                        @error('reviews.' . $index . '.communication_rating')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="feedback_{{ $auditor->id }}" class="form-label">Umpan Balik</label>
                                    <textarea class="form-control @error('reviews.' . $index . '.feedback') is-invalid @enderror"
                                        id="feedback_{{ $auditor->id }}" name="reviews[{{ $index }}][feedback]" rows="3"
                                        maxlength="500">{{ old('reviews.' . $index . '.feedback') }}</textarea>
                                    @error('reviews.' . $index . '.feedback')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <!-- Submit Buttons -->
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary btn-lg"
                                    onclick="return confirm('Kirim review dan tutup proyek ini? Tindakan ini tidak dapat dibatalkan.');">
                                    <i class="bi bi-check-circle"></i> Simpan Review & Tutup Proyek
                                </button>
                                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-lg">Batal</a>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Panduan Nilai</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled small mb-0">
                            <li><strong>90-100:</strong> Sangat Baik</li>
                            <li><strong>80-89:</strong> Baik Sekali</li>
                            <li><strong>70-79:</strong> Baik</li>
                            <li><strong>60-69:</strong> Cukup</li>
                            <li><strong>&lt;60:</strong> Perlu Perbaikan</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function calculateOverallScore(auditorId) {
                const timeliness = parseInt(document.getElementById(`timeliness_rating_${auditorId}`).value) || 0;
                const completeness = parseInt(document.getElementById(`completeness_rating_${auditorId}`).value) || 0;
                const quality = parseInt(document.getElementById(`quality_rating_${auditorId}`).value) || 0;
                const communication = parseInt(document.getElementById(`communication_rating_${auditorId}`).value) || 0;

                if (timeliness && completeness && quality && communication) {
                    const average = Math.round((timeliness + completeness + quality + communication) / 4);
                    document.getElementById(`overall_rating_${auditorId}`).value = average;
                }
            }

            const detailedScoreInputs = document.querySelectorAll('[id^="timeliness_rating_"], [id^="completeness_rating_"], [id^="quality_rating_"], [id^="communication_rating_"]');
            detailedScoreInputs.forEach(input => {
                input.addEventListener('input', function () {
                    const auditorId = this.id.split('_').pop();
                    calculateOverallScore(auditorId);
                });

                const auditorId = input.id.split('_').pop();
                calculateOverallScore(auditorId);
            });

            const form = document.getElementById('reviewForm');
            form.addEventListener('submit', function (e) {
                let valid = true;
                const overallScores = document.querySelectorAll('[name*="[overall_rating]"]');

                overallScores.forEach(input => {
                    const value = parseInt(input.value);
                    if (!value || value < 0 || value > 100) {
                        valid = false;
                        input.classList.add('is-invalid');
                    } else {
                        input.classList.remove('is-invalid');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('Silakan isi semua nilai detail (0-100) untuk semua auditor.');
                    return false;
                }
            });

            const numberInputs = document.querySelectorAll('input[type="number"]:not([readonly])');
            numberInputs.forEach(input => {
                input.addEventListener('input', function () {
                    let value = parseInt(this.value);

                    if (value < 0) {
                        this.value = 0;
                        value = 0;
                    } else if (value > 100) {
                        this.value = 100;
                        value = 100;
                    }

                    if (value !== '' && (value < 0 || value > 100)) {
                        this.setCustomValidity('Nilai harus antara 0 dan 100');
                    } else {
                        this.setCustomValidity('');
                    }
                });

                input.addEventListener('blur', function () {
                    let value = parseInt(this.value);
                    if (value < 0) this.value = 0;
                    if (value > 100) this.value = 100;
                });
            });
        });
    </script>
@endpush