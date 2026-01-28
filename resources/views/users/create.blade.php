@extends('layouts.app')

@section('title', 'Create User')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Create New User</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                        <li class="breadcrumb-item active">Create</li>
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
                                <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
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
                                <label for="password_confirmation" class="form-label">Confirm Password <span
                                        class="text-danger">*</span></label>
                                <input type="password" class="form-control" id="password_confirmation"
                                    name="password_confirmation" required>
                            </div>

                            <div class="mb-3">
                                <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                                <select class="form-select @error('role') is-invalid @enderror" id="role" name="role"
                                    required>
                                    <option value="">Select Role</option>
                                    <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Admin (Level 1)
                                    </option>
                                    <option value="pengawas" {{ old('role') === 'pengawas' ? 'selected' : '' }}>Pengawas
                                        (Level 2)</option>
                                    <option value="staff" {{ old('role') === 'staff' ? 'selected' : '' }}>Staff (Level 3)
                                    </option>
                                </select>
                                @error('role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3" id="departmentField">
                                <label for="department_id" class="form-label">Department</label>
                                <select class="form-select @error('department_id') is-invalid @enderror" id="department_id"
                                    name="department_id">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
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
                                <label for="specialization" class="form-label">Specialization</label>
                                <input type="text" class="form-control @error('specialization') is-invalid @enderror"
                                    id="specialization" name="specialization" value="{{ old('specialization') }}"
                                    placeholder="e.g. IT Audit, Financial">
                                <small class="text-muted">Required for Staff role</small>
                                @error('specialization')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <hr>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Create User
                            </button>
                            <a href="{{ route('users.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancel
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