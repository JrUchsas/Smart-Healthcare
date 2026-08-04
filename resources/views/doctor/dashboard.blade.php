@extends('layouts.dashboard-layout')

@section('title', 'Doctor Dashboard')
@section('page_title', 'Doctor Portal & Practice Overview')

@section('content')
    @if(isset($doctor) && (! $doctor->is_available || ! auth()->user()->is_active))
        <div class="alert alert-warning d-flex align-items-center mb-4 p-3 shadow-sm border-warning" role="alert">
            <i class="bi bi-exclamation-triangle-fill fs-3 me-3 text-warning"></i>
            <div>
                <h6 class="fw-bold mb-1">Account Pending Administrator Verification</h6>
                <p class="mb-0 small">Your doctor account registration is currently pending verification and department assignment by the hospital administrator. You will be able to manage patient consultation slots once your credentials have been verified.</p>
            </div>
        </div>
    @endif

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
                        <i class="bi bi-wallet2 fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Doctor Modules Grid -->
    <div class="row g-4 mb-4">
        <!-- Today's Appointments -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>Today's Consultation Schedule</h6>
                    <span class="badge bg-primary-subtle text-primary border">{{ now()->format('l, M j, Y') }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref #</th>
                                    <th>Patient Name</th>
                                    <th>Time Slot</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($todayAppointments as $apt)
                                    <tr>
                                        <td><small class="font-monospace fw-bold">{{ $apt->appointment_number }}</small></td>
                                        <td><strong>{{ $apt->patient?->user?->name ?? 'Patient' }}</strong></td>
                                        <td>
                                            @if($apt->slot)
                                                <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ \Carbon\Carbon::parse($apt->slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($apt->slot->end_time)->format('h:i A') }}</small>
                                            @else
                                                <small class="text-muted">Scheduled</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ ucfirst($apt->status) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="bi bi-calendar-check fs-3 d-block mb-1"></i>
                                            No patient consultations scheduled for today.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Practice Schedule Management Link -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar3 me-2 text-info"></i>Practice Schedule Controls</h6>
                </div>
                <div class="card-body text-center p-4">
                    <i class="bi bi-calendar-range text-info display-4 d-block mb-3"></i>
                    <h5 class="fw-bold mb-2">Shift & Availability Manager</h5>
                    <p class="text-muted small mb-4">Set up consultation dates, start/end shift hours, and auto-generate 15/30/45/60 min appointment slots.</p>
                    <a href="{{ route('doctor.schedules.index') }}" class="btn btn-info text-white px-4">
                        <i class="bi bi-gear-fill me-1"></i> Manage Schedules & Slots
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
