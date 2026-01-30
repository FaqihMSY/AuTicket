<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'AuTicket') }} - @yield('title', 'Dashboard')</title>
    <link rel="icon" href="{{ asset('img/logo.png') }}" type="image/png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css"
        rel="stylesheet">
    <!-- <link rel="stylesheet" href="{{ asset('css/custom.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/theme.css') }}">
    <link rel="stylesheet" href="{{ asset('css/theme-dark-text-fix.css') }}">
    <link rel="stylesheet" href="{{ asset('css/professional-ui.css') }}">

    <!-- Prevent flash of unstyled content -->
    <script>
        (function () {
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.documentElement.setAttribute('data-theme', savedTheme);
            } else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    @stack('styles')
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}">
                <img src="{{ asset('img/logo.png') }}" alt="Logo" height="30"
                    class="d-inline-block align-text-top rounded">
                AuTicket
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('dashboard') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('projects.index') }}">Proyek</a>
                    </li>
                    @if(auth()->user()->canManageProjects())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('projects.create') }}">Tambah Proyek</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('projects.pendingApprovals') }}">
                                Persetujuan
                                @php
                                    $pendingCount = \App\Models\Project::where('status', 'DRAFT')
                                        ->when(!auth()->user()->isAdmin(), function ($q) {
                                            $q->where('assigned_manager_id', auth()->id());
                                        })
                                        ->count();
                                @endphp
                                @if($pendingCount > 0)
                                    <span class="badge bg-danger ms-1">{{ $pendingCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('auditors.index') }}">Kinerja Auditor</a>
                        </li>
                    @endif
                    @if(auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('users.index') }}">Manajemen Pengguna</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="masterDataDropdown" role="button"
                                data-bs-toggle="dropdown">
                                Master Data
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('departments.index') }}">Departemen</a></li>
                                <li><a class="dropdown-item" href="{{ route('assignment-types.index') }}">Jenis
                                        Penugasan</a></li>
                            </ul>
                        </li>
                    @endif
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                            data-bs-toggle="dropdown">
                            {{ auth()->user()->name }}
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><span class="dropdown-item-text">{{ user_role_label(auth()->user()->role) }}</span>
                            </li>
                            <li>
                                <div class="theme-toggle-container">
                                    <div class="theme-toggle-wrapper">
                                        <span class="theme-label">Terang</span>
                                        <label class="theme-switch">
                                            <input type="checkbox">
                                            <span class="slider"></span>
                                        </label>
                                        <span class="theme-label">Gelap</span>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item">Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

    @stack('scripts')

    <!-- Theme Toggle -->
    <script src="{{ asset('js/theme-toggle.js') }}"></script>

    <!-- Failsafe for stuck layouts/modals -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Remove stuck modal backdrops
            const backdrops = document.querySelectorAll('.modal-backdrop');
            if (backdrops.length > 0 && !document.querySelector('.modal.show')) {
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
            }
        });
    </script>
</body>

</html>