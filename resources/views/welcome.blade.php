<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Smart Healthcare Management & Online Doctor Appointment System</title>

    <!-- Google Fonts & Bootstrap 5 CSS -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
            color: #ffffff;
        }
        .full-screen-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .navbar-brand-logo {
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            border-radius: 1.25rem;
            transition: all 0.3s ease;
        }
        .glass-card:hover {
            background: rgba(255, 255, 255, 0.12);
            transform: translateY(-4px);
        }
        .btn-gradient {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: #ffffff;
            border: none;
            font-weight: 600;
        }
        .btn-gradient:hover {
            background: linear-gradient(135deg, #0f766e 0%, #115e59 100%);
            color: #ffffff;
        }
        footer {
            background: rgba(15, 23, 42, 0.85);
            backdrop-filter: blur(8px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
    </style>
</head>
<body>

    <div class="full-screen-wrapper">
        {{-- Navbar Header --}}
        <nav class="navbar navbar-expand-lg navbar-dark py-3 px-3 px-md-5 border-bottom border-white border-opacity-10">
            <div class="container-fluid">
                <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                    <i class="bi bi-heart-pulse-fill text-danger fs-2 me-2"></i>
                    <span class="navbar-brand-logo fs-4">Smart Healthcare</span>
                </a>
                <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <div class="ms-auto d-flex align-items-center gap-2 mt-3 mt-lg-0">
                        @if (Route::has('login'))
                            @auth
                                <a href="{{ url('/dashboard') }}" class="btn btn-outline-light px-4 rounded-pill">
                                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-outline-light px-4 rounded-pill">
                                    <i class="bi bi-box-arrow-in-right me-1"></i> Sign In
                                </a>
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="btn btn-gradient px-4 rounded-pill">
                                        <i class="bi bi-person-plus me-1"></i> Patient Register
                                    </a>
                                @endif
                            @endauth
                        @endif
                    </div>
                </div>
            </div>
        </nav>

        {{-- Hero Body Content --}}
        <div class="container my-auto py-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-7 text-center text-lg-start">
                    <span class="badge bg-info bg-opacity-20 text-info border border-info mb-3 px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> Full-Service Digital Hospital & Telehealth Portal
                    </span>
                    <h1 class="display-4 fw-extrabold mb-3 text-white">Smart Healthcare & Online Doctor Appointment System</h1>
                    <p class="lead text-light opacity-75 mb-4">
                        Connect with top medical specialists, manage digital health profiles, schedule atomic online appointments, receive digital PDF prescriptions, and chat live with healthcare professionals.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3 justify-content-center justify-content-lg-start">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-gradient btn-lg px-4 rounded-pill shadow">
                                <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-gradient btn-lg px-4 rounded-pill shadow">
                                <i class="bi bi-calendar-check me-2"></i> Book Appointment Now
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                                <i class="bi bi-box-arrow-in-right me-2"></i> User Sign In
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="glass-card p-4 p-md-5 text-center shadow-lg">
                        <div class="mb-3">
                            <i class="bi bi-hospital text-info display-1"></i>
                        </div>
                        <h3 class="text-white fw-bold mb-2">24/7 Medical Access</h3>
                        <p class="text-light opacity-75 small mb-4">Complete digital healthcare workflow for Patients, Doctors, and Hospital Administrators.</p>

                        <div class="d-grid gap-2">
                            <a href="{{ route('login') }}" class="btn btn-light btn-sm fw-semibold rounded-pill py-2">
                                <i class="bi bi-person-lock me-1"></i> Sign In to Account
                            </a>
                            <a href="{{ route('register.doctor') }}" class="btn btn-outline-info btn-sm rounded-pill py-2 text-white">
                                <i class="bi bi-person-badge me-1"></i> Register as Doctor
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer class="py-3 text-center text-light">
            <div class="container">
                <small class="opacity-75">&copy; {{ date('Y') }} Smart Healthcare Management System. All rights reserved.</small>
            </div>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
