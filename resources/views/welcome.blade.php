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
        {{-- Top Navbar Header --}}
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
                                        <i class="bi bi-person-plus me-1"></i> Register
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
            <div class="row justify-content-center text-center">
                <div class="col-lg-10 col-xl-9">
                    {{-- High-visibility pill badge --}}
                    <span class="badge bg-info text-dark fw-bold mb-3 px-3 py-2 rounded-pill shadow-sm" style="font-size: 0.9rem;">
                        <i class="bi bi-shield-check me-1"></i> Full-Service Digital Hospital & Telehealth Portal
                    </span>

                    <h1 class="display-4 fw-extrabold mb-3 text-white">Smart Healthcare & Online Doctor Appointment System</h1>

                    <p class="lead text-light opacity-75 mb-4 mx-auto" style="max-width: 800px;">
                        Connect with top medical specialists, manage digital health profiles, schedule atomic online appointments, receive digital PDF prescriptions, and chat live with healthcare professionals.
                    </p>

                    <div class="d-flex justify-content-center gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-gradient btn-lg px-5 rounded-pill shadow">
                                <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-gradient btn-lg px-5 rounded-pill shadow">
                                <i class="bi bi-calendar-check me-2"></i> Book Appointment Now
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
