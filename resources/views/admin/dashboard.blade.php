@extends('layouts.dashboard-layout')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Overview & Control Center')

@section('sidebar_menu')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.specializations.*') ? 'active' : '' }}" 
           href="#departmentsSubmenu" 
           data-bs-toggle="collapse" 
           role="button"
           aria-expanded="{{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.specializations.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-hospital me-2"></i> Departments</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.specializations.*') ? 'show' : '' }}" id="departmentsSubmenu">
            <ul class="nav flex-column ms-3 mt-1">
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                        <i class="bi bi-list-task me-2"></i> All Departments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ request()->routeIs('admin.specializations.*') ? 'active' : '' }}" href="{{ route('admin.specializations.index') }}">
                        <i class="bi bi-patch-check me-2"></i> Specializations
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-person-badge"></i> Doctor Approvals
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-people"></i> Patient Management
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-calendar-check"></i> Appointments
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-bar-chart-line"></i> Revenue Reports
        </a>
    </li>
@endsection

@section('content')
    <!-- Statistics Section -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Total Doctors</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['total_doctors'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Doctor Approvals</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['pending_approvals'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="bi bi-hourglass-split fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Total Patients</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['total_patients'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Total Revenue</span>
                        <h3 class="fw-bold mb-0 mt-1">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules Section -->
    <div class="row g-4 mb-4">
        <!-- Doctor Approval Module Placeholder -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-check me-2 text-warning"></i>Doctor Approval Queue</h6>
                    <span class="badge bg-warning text-dark">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Doctor Name</th>
                                    <th>Department</th>
                                    <th>Qualification</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                        No pending doctor approvals.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Appointment Monitoring Module Placeholder -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2 text-primary"></i>Recent Appointments Monitor</h6>
                    <span class="badge bg-primary">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Date/Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-x fs-3 d-block mb-1"></i>
                                        No recent appointments logged yet.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Reports Module Placeholder -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-graph-up-arrow me-2 text-success"></i>Revenue & Financial Analytics</h6>
                    <span class="badge bg-success">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <div class="text-center py-5">
                        <i class="bi bi-bar-chart-steps text-secondary display-4 d-block mb-2"></i>
                        <p class="text-muted mb-0">Financial chart analytics will render here upon transaction initialization.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
