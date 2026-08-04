<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Smart Healthcare') }}</title>

    <!-- Google Fonts & Bootstrap 5 CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f4f6f9;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #1e293b;
        }
        .sidebar .nav-link {
            color: #94a3b8;
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #ffffff;
            background-color: #334155;
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
        }
        .navbar-brand-title {
            font-weight: 700;
            letter-spacing: -0.5px;
        }
        .stat-card {
            border: none;
            border-radius: 0.75rem;
            transition: transform 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-3px);
        }
        .card-header {
            min-height: 56px;
        }
        .card {
            border-radius: 0.75rem;
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-4">
                <div class="d-flex align-items-center mb-4 px-2 text-white">
                    <i class="bi bi-heart-pulse-fill text-danger fs-3 me-2"></i>
                    <span class="fs-5 navbar-brand-title">Smart Health</span>
                </div>
                <hr class="text-secondary mb-4">
                <ul class="nav flex-column">
                    @include('layouts.navigation-sidebar')
                </ul>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 p-4">
                <!-- Top Header Navbar -->
                <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <div>
                        <h4 class="mb-0 fw-bold">@yield('page_title', 'Dashboard')</h4>
                        <small class="text-muted">Welcome back, {{ auth()->user()->name }}</small>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle d-flex align-items-center shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 me-2 text-primary"></i>
                            <span>{{ auth()->user()->name }}</span>
                            <span class="badge bg-secondary ms-2 text-capitalize">{{ auth()->user()->user_type }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><a class="dropdown-item" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2"></i>Profile</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </header>

                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Main Section -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts: jQuery, Bootstrap 5, Chart.js, DataTables, SweetAlert2 -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        // Attach Flatpickr globally to every date input so the calendar always
        // shows DD/MM/YYYY — the browser's native date picker ignores locale
        // on many systems and defaults to MM/DD/YYYY which confuses non-US users.
        document.addEventListener('DOMContentLoaded', function () {
            flatpickr('input[type="date"]', {
                dateFormat: 'Y-m-d',        // Standard YYYY-MM-DD date format
                allowInput: true,           // Allow typing directly or selecting from calendar
                disableMobile: false,       // Mobile native datepicker support
            });
        });
    </script>

    @stack('scripts')
</body>
</html>
