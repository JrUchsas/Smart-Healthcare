@extends('layouts.dashboard-layout')

@section('title', 'Doctor Dashboard')
@section('page_title', 'Doctor Portal & Practice Overview')

@section('content')
    <!-- Statistics Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Today's Appointments</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['today_appointments_count'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="bi bi-calendar-event-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Completed Today</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['completed_appointments_count'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Total Patients</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['total_patients_treated'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                        <i class="bi bi-person-lines-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Monthly Earnings</span>
                        <h3 class="fw-bold mb-0 mt-1">${{ number_format($stats['monthly_earnings'] ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="bi bi-cash-stack fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modules Section -->
    <div class="row g-4 mb-4">
        <!-- Today's Appointments Module Placeholder -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Today's Appointment Schedule</h6>
                    <span class="badge bg-primary">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Time</th>
                                    <th>Patient Name</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-check fs-3 d-block mb-1"></i>
                                        No appointments scheduled for today.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Schedule & Availability Calendar Placeholder -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar3 me-2 text-info"></i>Practice Schedule & Calendar</h6>
                    <span class="badge bg-info">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <div class="text-center py-4">
                        <i class="bi bi-calendar-week text-secondary display-4 d-block mb-2"></i>
                        <p class="text-muted mb-0">Doctor appointment calendar slot controls will render here.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Patient History & Earnings Row -->
    <div class="row g-4">
        <!-- Patient History Module Placeholder -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-journal-check me-2 text-success"></i>Recent Patient Consultations</h6>
                    <span class="badge bg-success">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center py-4 mb-0">Patient consultation logs and prescription records will display here.</p>
                </div>
            </div>
        </div>

        <!-- Earnings Summary Module Placeholder -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-piggy-bank me-2 text-warning"></i>Earnings & Payout Summary</h6>
                    <span class="badge bg-warning text-dark">Module Placeholder</span>
                </div>
                <div class="card-body">
                    <p class="text-muted text-center py-4 mb-0">Monthly consultation fee payouts and financial breakdown will display here.</p>
                </div>
            </div>
        </div>
    </div>
@endsection
