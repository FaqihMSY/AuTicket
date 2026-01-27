@extends('layouts.app')

@section('title', 'Create Project')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-12">
                <h2>Create New Project</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item"><a href="{{ route('projects.index') }}">Projects</a></li>
                        <li class="breadcrumb-item active">Create</li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Project Information</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('projects.store') }}" enctype="multipart/form-data"
                            id="createProjectForm">
                            @csrf

                            <!-- Title -->
                            <div class="mb-3">
                                <label for="title" class="form-label">Project Title <span
                                        class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('title') is-invalid @enderror" id="title"
                                    name="title" value="{{ old('title') }}" required>
                                @error('title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Assignment Type -->
                            <div class="mb-3">
                                <label for="assignment_type_id" class="form-label">Assignment Type <span
                                        class="text-danger">*</span></label>
                                <select class="form-select @error('assignment_type_id') is-invalid @enderror"
                                    id="assignment_type_id" name="assignment_type_id" required>
                                    <option value="">Select Assignment Type</option>
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

                            <!-- Dates -->
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="start_date" class="form-label">Start Date <span
                                            class="text-danger">*</span></label>
                                    <input type="date" class="form-control @error('start_date') is-invalid @enderror"
                                        id="start_date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}"
                                        required>
                                    @error('start_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="end_date" class="form-label">End Date <span
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
                                <label for="priority" class="form-label">Priority <span class="text-danger">*</span></label>
                                <select class="form-select @error('priority') is-invalid @enderror" id="priority"
                                    name="priority" required>
                                    <option value="LOW" {{ old('priority') == 'LOW' ? 'selected' : '' }}>Low</option>
                                    <option value="MEDIUM" {{ old('priority', 'MEDIUM') == 'MEDIUM' ? 'selected' : '' }}>
                                        Medium</option>
                                    <option value="HIGH" {{ old('priority') == 'HIGH' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Description -->
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" id="description"
                                    name="description" rows="4">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Instruction Files -->
                            <div class="mb-3">
                                <label for="instruction_files" class="form-label">Instruction Files (PDF/Excel/Word, max
                                    10MB each)</label>
                                <input type="file" class="form-control @error('instruction_files.*') is-invalid @enderror"
                                    id="instruction_files" name="instruction_files[]" accept=".pdf,.xlsx,.xls,.doc,.docx"
                                    multiple>
                                <small class="text-muted">Optional: Upload instruction documents for auditors (multiple
                                    files allowed)</small>
                                @error('instruction_files.*')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Auditor Selection -->
                            <div class="mb-4">
                                <label class="form-label">Assign Auditors <span class="text-danger">*</span></label>

                                <!-- Sort Options -->
                                <div class="mb-3">
                                    <label for="sortBy" class="form-label">Sort by:</label>
                                    <select class="form-select form-select-sm" id="sortBy" style="width: 200px;">
                                        <option value="performance">Performance</option>
                                        <option value="availability">Availability</option>
                                        <option value="balanced">Balanced</option>
                                    </select>
                                </div>

                                <!-- Auditors List -->
                                <div id="auditorsList" class="border rounded p-3">
                                    <div class="text-center">
                                        <div class="spinner-border spinner-border-sm" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mb-0 mt-2">Loading auditors...</p>
                                    </div>
                                </div>

                                @error('auditor_ids')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                                @error('auditor_ids.*')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Submit Buttons -->
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-save"></i> Create Project
                                </button>
                                <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Help Sidebar -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Help</h6>
                    </div>
                    <div class="card-body">
                        <h6>Workload Indicators:</h6>
                        <ul class="list-unstyled">
                            <li><span class="badge bg-success">AVAILABLE</span> 0-3 active projects</li>
                            <li><span class="badge bg-warning">MODERATE</span> 4-5 active projects</li>
                            <li><span class="badge bg-danger">BUSY</span> 6+ active projects</li>
                        </ul>

                        <hr>

                        <h6>Tips:</h6>
                        <ul class="small">
                            <li>Select at least one auditor</li>
                            <li>Consider workload when assigning</li>
                            <li>Higher performance scores indicate better past performance</li>
                            <li>Project will be saved as DRAFT until published</li>
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
            // Initialize auditor selection
            loadAuditors();

            // Sort change handler
            document.getElementById('sortBy').addEventListener('change', function () {
                loadAuditors();
            });

            // Date validation
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');

            startDate.addEventListener('change', function () {
                endDate.min = this.value;
            });

            endDate.addEventListener('change', function () {
                if (this.value < startDate.value) {
                    alert('End date cannot be before start date');
                    this.value = '';
                }
            });
        });

        function loadAuditors() {
            const sortBy = document.getElementById('sortBy').value;
            const auditorsList = document.getElementById('auditorsList');

            // Show loading
            auditorsList.innerHTML = `
            <div class="text-center">
                <div class="spinner-border spinner-border-sm" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mb-0 mt-2">Loading auditors...</p>
            </div>
        `;

            // Fetch auditors
            fetch(`/api/auditors?sort=${sortBy}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length === 0) {
                        auditorsList.innerHTML = '<p class="text-muted mb-0">No auditors available</p>';
                        return;
                    }

                    let html = '';
                    data.forEach(auditor => {
                        const workloadClass = auditor.workload_status === 'AVAILABLE' ? 'success' :
                            auditor.workload_status === 'MODERATE' ? 'warning' : 'danger';

                        html += `
                        <div class="form-check mb-3 p-3 border rounded ${auditor.workload_status === 'BUSY' ? 'bg-light' : ''}">
                            <input class="form-check-input" type="checkbox" name="auditor_ids[]" 
                                   value="${auditor.id}" id="auditor${auditor.id}">
                            <label class="form-check-label w-100" for="auditor${auditor.id}">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <strong>${auditor.user.name}</strong>
                                        <br>
                                        <small class="text-muted">${auditor.specialization || 'General'}</small>
                                        ${auditor.certification ? `<br><small class="text-info">${auditor.certification}</small>` : ''}
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-${workloadClass}">${auditor.workload_status}</span>
                                        <br>
                                        <small class="text-muted">Load: ${auditor.current_load} projects</small>
                                    </div>
                                </div>
                            </label>
                        </div>
                    `;
                    });

                    auditorsList.innerHTML = html;
                })
                .catch(error => {
                    console.error('Error loading auditors:', error);
                    auditorsList.innerHTML = '<p class="text-danger mb-0">Error loading auditors. Please refresh the page.</p>';
                });
        }
    </script>
@endpush