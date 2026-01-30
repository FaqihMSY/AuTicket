@extends('layouts.app')

@section('title', 'User Management')

@section('content')
    <div class="container-fluid">
        <div class="row mb-4">
            <div class="col-md-6">
                <h2>User Management</h2>
            </div>
            <div class="col-md-6 text-end">
                <a href="{{ route('users.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Create New User
                </a>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        @if($users->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Department</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>
                                                    @if($user->role === 'admin')
                                                        <span class="badge bg-danger">Admin (L1)</span>
                                                    @elseif($user->role === 'pengawas')
                                                        <span class="badge bg-primary">Pengawas (L2)</span>
                                                    @elseif($user->role === 'reviewer')
                                                        <span class="badge bg-info">Reviewer</span>
                                                    @else
                                                        <span class="badge bg-secondary">Staff (L3)</span>
                                                    @endif
                                                </td>
                                                <td>{{ $user->department?->name ?? 'All Departments' }}</td>
                                                <td>
                                                    @can('update', $user)
                                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i> Edit
                                                        </a>
                                                    @endcan

                                                    @can('delete', $user)
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST"
                                                            class="d-inline" onsubmit="return confirm('Delete this user?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i> Delete
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
                            <div class="alert alert-info">
                                <h5>No Users Found</h5>
                                <p class="mb-0">There are no users to display.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection