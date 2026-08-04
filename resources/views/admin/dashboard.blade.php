@extends('layouts.dashboard-layout')

@section('title', 'Admin Dashboard')
@section('page_title', 'Admin Overview & Control Center')

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
                        <span class="text-muted small text-uppercase">Pending Approvals</span>
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
            <a href="{{ route('admin.reports.revenue') }}" class="text-decoration-none">
                <div class="card stat-card bg-white shadow-sm p-3 border-start border-info border-4 hover-shadow">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small text-uppercase">Total Revenue</span>
                            <h3 class="fw-bold mb-0 mt-1 text-dark">${{ number_format($stats['total_revenue'] ?? 0, 2) }}</h3>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                            <i class="bi bi-currency-dollar fs-4"></i>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- Active Management Modules Grid -->
    <div class="row g-4 mb-4">
        <!-- Recent Appointments Monitor (LEFT) -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2 text-primary"></i>Recent Appointments Monitor</h6>
                    <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-clock-history me-1"></i> View History
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Ref #</th>
                                    <th>Patient</th>
                                    <th>Doctor</th>
                                    <th>Status</th>
                                    <th class="text-end pe-3">Fee & Payment</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentAppointments as $apt)
                                    <tr>
                                        <td class="ps-3">
                                            <a href="{{ route('admin.appointments.show', $apt) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                                {{ $apt->appointment_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ $apt->patient?->user?->name ?? 'Patient' }}</div>
                                            <small class="text-muted">{{ $apt->patient?->user?->email }}</small>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">{{ \Illuminate\Support\Str::startsWith($apt->doctor?->user?->name, 'Dr.') ? $apt->doctor?->user?->name : 'Dr. '.$apt->doctor?->user?->name }}</div>
                                            <small class="text-muted">{{ $apt->doctor?->department?->name ?? 'Specialist' }}</small>
                                        </td>
                                        <td>
                                            @if($apt->status === 'completed')
                                                <span class="badge bg-info-subtle text-info border">Completed</span>
                                            @elseif($apt->status === 'confirmed')
                                                <span class="badge bg-success-subtle text-success border">Confirmed</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-dark border">Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-3">
                                            <span class="fw-bold text-dark">${{ number_format($apt->consultation_fee, 2) }}</span>
                                            @if($apt->payment_status === 'paid')
                                                <span class="badge bg-success-subtle text-success border ms-1"><i class="bi bi-check-circle me-1"></i> Paid</span>
                                            @else
                                                <span class="badge bg-warning-subtle text-dark border ms-1"><i class="bi bi-hourglass-split me-1"></i> Unpaid</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-calendar-x fs-3 d-block mb-1"></i>
                                            No recent appointments logged yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Doctor Approvals (RIGHT) -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-check me-2 text-warning"></i>Doctor Approval Queue</h6>
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary btn-sm">Manage All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-3">Doctor Name</th>
                                    <th>Qualification</th>
                                    <th class="text-end pe-3">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($pendingDoctors as $doc)
                                    <tr>
                                        <td class="ps-3">
                                            <strong class="text-dark">{{ $doc->user?->name }}</strong><br>
                                            <small class="text-muted">{{ $doc->user?->email }}</small>
                                        </td>
                                        <td><span class="badge bg-light text-dark border">{{ $doc->qualification ?? 'MBBS' }}</span></td>
                                        <td class="text-end pe-3">
                                            <a href="{{ route('admin.doctors.edit', $doc) }}" class="btn btn-primary btn-sm px-3">
                                                Review & Assign
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            <i class="bi bi-check-circle text-success fs-3 d-block mb-1"></i>
                                            All doctor profiles reviewed & active.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
