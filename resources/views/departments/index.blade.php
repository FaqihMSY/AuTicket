@extends('layouts.app')

@section('title', 'Kelola Departemen')

@section('content')
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Departemen</h2>
            <a href="{{ route('departments.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i> Tambah Departemen
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
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Deskripsi</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($departments as $department)
                                <tr>
                                    <td><span class="badge bg-secondary">{{ $department->code }}</span></td>
                                    <td>{{ $department->name }}</td>
                                    <td>{{ $department->description ?? '-' }}</td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('departments.edit', $department) }}"
                                                class="btn btn-sm btn-outline-primary">Ubah</a>
                                            <form action="{{ route('departments.destroy', $department) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Apakah Anda yakin?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted">Tidak ada departemen ditemukan.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="d-flex justify-content-end mt-3">
                    {{ $departments->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection