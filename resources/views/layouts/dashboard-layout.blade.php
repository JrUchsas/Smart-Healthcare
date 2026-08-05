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
        :root {
            --sidebar-bg: #1e293b;
            --sidebar-hover: #334155;
            --sidebar-color: #94a3b8;
            --body-bg: #f4f6f9;
        }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--body-bg);
            overflow-x: hidden;
        }

        /* Sidebar Styling */
        .sidebar {
            background-color: var(--sidebar-bg);
            transition: all 0.3s ease;
        }
        @media (min-width: 768px) {
            .sidebar {
                min-height: 100vh;
                position: sticky;
                top: 0;
                height: 100vh;
                overflow-y: auto;
            }
        }
        .sidebar .nav-link {
            color: var(--sidebar-color);
            padding: 0.75rem 1.25rem;
            font-weight: 500;
            border-radius: 0.375rem;
            margin-bottom: 0.25rem;
            display: flex;
            align-items: center;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #ffffff;
            background-color: var(--sidebar-hover);
        }
        .sidebar .nav-link i {
            margin-right: 0.75rem;
            font-size: 1.1rem;
        }
        .navbar-brand-title {
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        /* Responsive Stat Cards & Layout */
        .stat-card {
            border: none;
            border-radius: 0.75rem;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.08) !important;
        }
        .card {
            border-radius: 0.75rem;
        }
        .card-header {
            min-height: 56px;
        }

        /* Mobile Adjustments */
        @media (max-width: 767.98px) {
            .sidebar {
                position: fixed;
                top: 0;
                left: 0;
                bottom: 0;
                z-index: 1045;
                width: 280px;
                transform: translateX(-100%);
            }
            .sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0.5rem 2rem rgba(0, 0, 0, 0.3);
            }
            .mobile-overlay {
                position: fixed;
                inset: 0;
                background: rgba(15, 23, 42, 0.5);
                z-index: 1040;
                backdrop-filter: blur(2px);
                display: none;
            }
            .mobile-overlay.show {
                display: block;
            }
            .main-content {
                padding: 1rem !important;
            }
        }
    </style>

    @stack('styles')
</head>
<body>
    <div class="mobile-overlay" id="mobileOverlay"></div>

    <div class="container-fluid px-0">
        <div class="row g-0">
            <!-- Sidebar Navigation -->
            <nav class="col-md-3 col-lg-2 sidebar p-4" id="sidebarMenu">
                <div class="d-flex align-items-center justify-content-between mb-4 px-2 text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-heart-pulse-fill text-danger fs-3 me-2"></i>
                        <span class="fs-5 navbar-brand-title">Smart Health</span>
                    </div>
                    <button class="btn btn-sm btn-outline-light d-md-none border-0" id="closeSidebarBtn">
                        <i class="bi bi-x-lg fs-5"></i>
                    </button>
                </div>
                <hr class="text-secondary mb-4">
                <ul class="nav flex-column">
                    @include('layouts.navigation-sidebar')
                </ul>
            </nav>

            <!-- Main Content Area -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content p-3 p-md-4">
                <!-- Mobile Navigation Bar (Visible only on mobile <768px) -->
                <div class="d-flex d-md-none align-items-center justify-content-between mb-3 bg-slate p-3 rounded-3 shadow-sm" style="background-color: #1e293b;">
                    <div class="d-flex align-items-center text-white">
                        <i class="bi bi-heart-pulse-fill text-danger fs-4 me-2"></i>
                        <span class="fw-bold fs-6">Smart Health</span>
                    </div>
                    <button class="btn btn-primary btn-sm px-3 py-1 fw-semibold d-flex align-items-center" id="toggleSidebarBtn">
                        <i class="bi bi-list fs-5 me-1"></i> Menu
                    </button>
                </div>

                <!-- Top Header Navbar -->
                <header class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                    <div>
                        <h4 class="mb-0 fw-bold text-dark">@yield('page_title', 'Dashboard')</h4>
                        <small class="text-muted d-none d-sm-inline">Welcome back, {{ auth()->user()->name }}</small>
                    </div>

                    <div class="d-flex align-items-center gap-2">
                        @if(!auth()->user()->hasRole('Admin') && auth()->user()->user_type !== 'admin')
                            @php
                                $unreadNotificationsCount = \App\Models\Notification::where('user_id', auth()->id())->where('is_read', false)->count();
                            @endphp
                            <a href="{{ route('notifications.index') }}" class="btn btn-white bg-white border position-relative shadow-sm py-2 px-3" title="View Notifications">
                                <i class="bi bi-bell fs-5 text-secondary"></i>
                                @if($unreadNotificationsCount > 0)
                                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem;">
                                        {{ $unreadNotificationsCount }}
                                    </span>
                                @endif
                            </a>
                        @endif

                        <div class="dropdown">
                        <button class="btn btn-white bg-white border dropdown-toggle d-flex align-items-center shadow-sm py-2 px-3" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle fs-5 me-2 text-primary"></i>
                            <span class="fw-semibold text-dark me-2">{{ auth()->user()->name }}</span>
                            <span class="badge bg-secondary text-capitalize d-none d-sm-inline-block">{{ auth()->user()->user_type }}</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2">
                            <li><a class="dropdown-item py-2" href="{{ route('profile.edit') }}"><i class="bi bi-person me-2 text-primary"></i>Profile Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger py-2"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </header>

                <!-- Flash Alerts -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <!-- Page Main Section -->
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Scripts: jQuery, Bootstrap 5, Chart.js, DataTables, SweetAlert2, Flatpickr -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Flatpickr Initialization
            flatpickr('input[type="date"]', {
                dateFormat: 'Y-m-d',
                allowInput: true,
                disableMobile: false,
            });

            // Mobile Sidebar Toggle Logic
            const sidebar = document.getElementById('sidebarMenu');
            const overlay = document.getElementById('mobileOverlay');
            const toggleBtn = document.getElementById('toggleSidebarBtn');
            const closeBtn = document.getElementById('closeSidebarBtn');

            function openSidebar() {
                if (sidebar) sidebar.classList.add('show');
                if (overlay) overlay.classList.add('show');
            }

            function closeSidebar() {
                if (sidebar) sidebar.classList.remove('show');
                if (overlay) overlay.classList.remove('show');
            }

            if (toggleBtn) toggleBtn.addEventListener('click', openSidebar);
            if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
            if (overlay) overlay.addEventListener('click', closeSidebar);
        });
    </script>

    @stack('scripts')
</body>
</html>
