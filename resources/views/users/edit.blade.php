@extends('layouts.app')

@section('title', 'Ubah Pengguna')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Ubah Pengguna</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Beranda</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Pengguna</a></li>
                        <li class="breadcrumb-item active">Ubah</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-body">
                        <form method="POST" action="{{ route('users.update', $user) }}">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="name" class="form-label">Nama <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name', $user->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email', $user->email) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Kata Sandi</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                    id="password" name="password">
                                <small class="text-muted">Biarkan kosong jika tidak ingin mengubah kata sandi</small>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Konfirmasi Kata Sandi</label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation">
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Peran <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                    required>
                                    <option value="">Pilih Peran</option>
                                    <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Admin
                                        (Level 1)</option>
                                    <option value="pengawas" {{ old('role', $user->role) === 'pengawas' ? 'selected' : '' }}>
                                        Pengawas (Level 2)</option>
                                    <option value="reviewer" {{ old('role', $user->role) === 'reviewer' ? 'selected' : '' }}>
                                        Reviewer</option>
                                    <option value="staff" {{ old('role', $user->role) === 'staff' ? 'selected' : '' }}>Staff
                                        (Level 3)</option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="departmentField">
                                <label for="department_id" class="form-label">Departemen</label>
                                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id"
                                    name="department_id">
                                    <option value="">Pilih Departemen</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>
                                            {{ $department->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Required for Admin and Pengawas roles</small>
                            </div>

                            <!-- Auditor Details (Visible only for Staff) -->
                            <div class="mb-3" id="auditorFields" style="display: none;">
                                <label for="specialization" class="form-label">Spesialisasi</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                                    id="specialization" name="specialization"
                                    value="{{ old('specialization', $user->auditor->specialization ?? '') }}"
                                    placeholder="misal: Audit TI, Keuangan">
                                <small class="text-muted">Wajib untuk peran Staff</small>
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Perbarui Pengguna
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
            const departmentField = document.getElementById('departmentField');

            function toggleFields() {
                if (roleSelect.value === 'staff') {
                    auditorFields.style.display = 'block';
                    departmentField.style.display = 'none';
                } else {
                    auditorFields.style.display = 'none';
                    departmentField.style.display = 'block';
                }
            }

            roleSelect.addEventListener('change', toggleFields);
            toggleFields(); // Run on load
        });
    </script>
@endpush