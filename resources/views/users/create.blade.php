@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Tambah Pengguna Baru</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active">Tambah</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.store') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                    required>
                                    <option value="">Pilih Peran</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Level 1)
                                    </option>
                                    <option value="pengawas" {{ old('role') === 'pengawas' ? 'selected' : '' }}>Pengawas
                                        (Level 2)</option>
                                    <option value="reviewer" {{ old('role') === 'reviewer' ? 'selected' : '' }}>Reviewer
                                    </option>
                                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff (Level 3)
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>


                            <!-- Auditor Details (Visible only for Staff) -->
                            <div class="mb-3" id="auditorFields" style="display: none;">
                                <label for="specialization" class="form-label">Spesialisasi</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                                    id="specialization" name="specialization" value="{{ old('specialization') }}"
                                    placeholder="misal: Audit TI, Keuangan">
                                <small class="text-muted">Wajib untuk peran Staff</small>
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Tambah Pengguna
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const roleSelect = document.getElementById('role');
            const auditorFields = document.getElementById('auditorFields');

            function toggleFields() {
                if (roleSelect.value === 'staff') {
                    auditorFields.style.display = 'block';
                } else {
                    auditorFields.style.display = 'none';
                }
            }

            roleSelect.addEventListener('change', toggleFields);
            toggleFields(); // Run on load
        });
    </script>
@endpush