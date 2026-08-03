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
        body {
            font-family: 'Inter', sans-serif;
            color: #1e293b;
            background-color: #f8fafc;
        }
        .hero-section {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f766e 100%);
            color: #ffffff;
            padding: 6rem 0 7rem 0;
        }
        .navbar-brand-logo {
            font-weight: 800;
            letter-spacing: -0.5px;
        }
        .feature-card {
            border: none;
            border-radius: 1rem;
            transition: all 0.3s ease;
        }
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
        }
        .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
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
    </style>
</head>
<body>

    <!-- Header Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark py-3 sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center me-4" href="{{ url('/') }}">
                <i class="bi bi-heart-pulse-fill text-danger fs-2 me-2"></i>
                <span class="navbar-brand-logo fs-4">Smart Healthcare</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link active" href="{{ url('/') }}">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="#departments">Departments</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About System</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-outline-light px-4 rounded-pill">
                                <i class="bi bi-speedometer2 me-1"></i> Dashboard
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-outline-light px-4 rounded-pill">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Log in
                            </a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="btn btn-gradient px-4 rounded-pill">
                                    <i class="bi bi-person-plus me-1"></i> Register Patient
                                </a>
                            @endif
                        @endauth
                    @endif
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center text-lg-start">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <span class="badge bg-teal-500 bg-opacity-20 text-info border border-info mb-3 px-3 py-2 rounded-pill">
                        <i class="bi bi-shield-check me-1"></i> Advanced Healthcare Management
                    </span>
                    <h1 class="display-4 fw-extrabold mb-3">Smart Healthcare & Online Doctor Appointment System</h1>
                    <p class="lead text-light opacity-75 mb-4">
                        Connect with top medical specialists, manage digital health records, schedule online appointments, and receive instant digital prescriptions anytime, anywhere.
                    </p>
                    <div class="d-flex flex-column flex-sm-row gap-3">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-gradient btn-lg px-4 rounded-pill">
                                <i class="bi bi-speedometer2 me-2"></i> Go to Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="btn btn-gradient btn-lg px-4 rounded-pill">
                                <i class="bi bi-calendar-check me-2"></i> Book Appointment Now
                            </a>
                            <a href="{{ route('login') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                                <i class="bi bi-person-lock me-2"></i> Doctor / Admin Login
                            </a>
                        @endauth
                    </div>
                </div>
                <div class="col-lg-5 text-center mt-5 mt-lg-0">
                    <div class="bg-white bg-opacity-10 p-4 rounded-4 backdrop-blur shadow-lg border border-white border-opacity-10">
                        <i class="bi bi-hospital text-info display-1 d-block mb-3"></i>
                        <h4 class="text-white fw-bold mb-2">24/7 Digital Health Portal</h4>
                        <p class="text-light opacity-75 small mb-0">Seamless experience for Patients, Doctors, and Hospital Administrators.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Key Services Section -->
    <section id="features" class="py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-primary fw-bold text-uppercase small">Core System Features</span>
                <h2 class="fw-bold mt-1">Healthcare Solutions Tailored For You</h2>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 bg-white shadow-sm">
                        <div class="icon-box bg-primary bg-opacity-10 text-primary mb-3">
                            <i class="bi bi-calendar-event fs-3"></i>
                        </div>
                        <h5 class="fw-bold">Online Doctor Appointments</h5>
                        <p class="text-muted mb-0">Browse specialist doctors, check real-time availability, and book appointments effortlessly.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 bg-white shadow-sm">
                        <div class="icon-box bg-success bg-opacity-10 text-success mb-3">
                            <i class="bi bi-file-earmark-medical fs-3"></i>
                        </div>
                        <h5 class="fw-bold">Digital Prescriptions & Reports</h5>
                        <p class="text-muted mb-0">Access your medical history, diagnosis logs, and download PDF prescriptions securely.</p>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card h-100 p-4 bg-white shadow-sm">
                        <div class="icon-box bg-warning bg-opacity-10 text-warning mb-3">
                            <i class="bi bi-shield-lock fs-3"></i>
                        </div>
                        <h5 class="fw-bold">Role-Based Secure Access</h5>
                        <p class="text-muted mb-0">Dedicated portal dashboards for Admin, Doctors, and Patients backed by Spatie RBAC security.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Departments Section -->
    <section id="departments" class="bg-white py-5">
        <div class="container py-4">
            <div class="text-center mb-5">
                <span class="text-primary fw-bold text-uppercase small">Specialized Care</span>
                <h2 class="fw-bold mt-1">Medical Departments</h2>
            </div>

            <div class="row g-3">
                <div class="col-md-2 col-6">
                    <div class="p-3 text-center border rounded-3 bg-light">
                        <i class="bi bi-heart-pulse text-danger fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold mb-0">Cardiology</h6>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="p-3 text-center border rounded-3 bg-light">
                        <i class="bi bi-cpu text-info fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold mb-0">Neurology</h6>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="p-3 text-center border rounded-3 bg-light">
                        <i class="bi bi-emoji-smile text-warning fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold mb-0">Pediatrics</h6>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="p-3 text-center border rounded-3 bg-light">
                        <i class="bi bi-bandaid text-primary fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold mb-0">Orthopedics</h6>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="p-3 text-center border rounded-3 bg-light">
                        <i class="bi bi-eye text-success fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold mb-0">Ophthalmology</h6>
                    </div>
                </div>
                <div class="col-md-2 col-6">
                    <div class="p-3 text-center border rounded-3 bg-light">
                        <i class="bi bi-hospital text-secondary fs-2 d-block mb-2"></i>
                        <h6 class="fw-bold mb-0">General Care</h6>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5 border-top border-secondary">
        <div class="container text-center">
            <p class="mb-0 text-muted small">&copy; {{ date('Y') }} Smart Healthcare Management System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
