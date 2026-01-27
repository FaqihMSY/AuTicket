@extends('layouts.app')

@section('title', 'Manage Assignment Types')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Assignment Types</h2>
            <a href="{{ route('assignment-types.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Add Assignment Type
            </a>
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($assignmentTypes as $type)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $type->code }}</span></td>
                                    <td>{{ $type->name }}</td>
                                    <td><span class="badge bg-info text-dark">{{ $type->department->name }}</span></td>
                                    <td>{{ $type->description ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('assignment-types.edit', $type) }}"
                                                class="btn btn-sm btn-outline-primary">Edit</a>
                                            <form action="{{ route('assignment-types.destroy', $type) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Are you sure?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No assignment types found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $assignmentTypes->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection