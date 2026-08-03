@extends('layouts.dashboard-layout')

@section('title', 'Patient Dashboard')
@section('page_title', 'Patient Portal & Health Overview')

@section('content')
    <!-- Patient Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Upcoming Appointments</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['upcoming_appointments_count'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="bi bi-calendar2-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Prescriptions</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['prescriptions_count'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="bi bi-capsule fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Invoices</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['invoices_count'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Total Spent</span>
                        <h3 class="fw-bold mb-0 mt-1">${{ number_format($stats['total_spent'] ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="bi bi-credit-card-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules Grid Section -->
    <div class="row g-4 mb-4">
        <!-- Upcoming Appointments Module Placeholder -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2 text-primary"></i>Upcoming Appointments</h6>
                    <span class="badge bg-primary">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Doctor</th>
                                    <th>Department</th>
                                    <th>Date/Time</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-plus fs-3 d-block mb-1"></i>
                                        You have no upcoming appointments.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Prescriptions Module Placeholder -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-file-earmark-medical me-2 text-success"></i>My Prescriptions</h6>
                    <span class="badge bg-success">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center py-4 mb-0">Issued medical prescriptions and PDF downloads will appear here.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Payment History & Invoices Module Placeholder -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-credit-card me-2 text-info"></i>Payment History & Invoices</h6>
                    <span class="badge bg-info">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center py-4 mb-0">Appointment payment history and billing receipts will display here.</p>
                </div>
            </div>
        </div>

        <!-- Profile Management Quick Access Placeholder -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-secondary"></i>Profile Management Overview</h6>
                    <span class="badge bg-secondary">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-badge fs-1 text-secondary me-3"></i>
                        <div>
                            <h6 class="mb-1 fw-bold">{{ auth()->user()->name }}</h6>
                            <p class="text-muted small mb-0">{{ auth()->user()->email }} | Patient Profile</p>
                        </div>
                    </div>
                    <hr>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">Edit Medical Profile Details</a>
                </div>
            </div>
        </div>
    </div>
@endsection
