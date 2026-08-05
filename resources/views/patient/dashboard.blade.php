@extends('layouts.dashboard-layout')

@section('title', 'Patient Dashboard')
@section('page_title', 'Patient Health Portal & Overview')

@section('content')
    <!-- Statistics Overview -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-primary border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Upcoming Appointments</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['upcoming_appointments_count'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded-circle text-primary">
                        <i class="bi bi-calendar-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-success border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Completed Visits</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['completed_appointments_count'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded-circle text-success">
                        <i class="bi bi-patch-check-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-info border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Doctors Visited</span>
                        <h3 class="fw-bold mb-0 mt-1">{{ $stats['total_doctors_visited'] ?? 0 }}</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded-circle text-info">
                        <i class="bi bi-person-badge-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card stat-card bg-white shadow-sm p-3 border-start border-warning border-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted small text-uppercase">Total Spent</span>
                        <h3 class="fw-bold mb-0 mt-1">৳{{ number_format($stats['total_spent'] ?? 0, 2) }}</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded-circle text-warning">
                        <i class="bi bi-credit-card-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Patient Modules Grid -->
    <div class="row g-4 mb-4">
        <!-- Upcoming Appointments -->
        <div class="col-lg-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-calendar-event me-2 text-primary"></i>My Upcoming Appointments</h6>
                    <a href="{{ route('patient.appointments.index') }}" class="btn btn-outline-primary btn-sm">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Ref #</th>
                                    <th>Doctor</th>
                                    <th>Date & Time</th>
                                    <th>Status</th>
                                    <th class="text-end">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($upcomingAppointments as $apt)
                                    <tr>
                                        <td><small class="font-monospace fw-bold">{{ $apt->appointment_number }}</small></td>
                                        <td>
                                            <strong>{{ \Illuminate\Support\Str::startsWith($apt->doctor?->user?->name, 'Dr.') ? $apt->doctor?->user?->name : 'Dr. '.$apt->doctor?->user?->name }}</strong><br>
                                            <small class="text-muted">{{ $apt->doctor?->department?->name ?? 'General Medical' }}</small>
                                        </td>
                                        <td>
                                            <small class="text-dark d-block fw-semibold">{{ $apt->appointment_date ? $apt->appointment_date->format('d M Y') : 'N/A' }}</small>
                                            @if($apt->slot)
                                                <small class="text-muted">{{ \Carbon\Carbon::parse($apt->slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($apt->slot->end_time)->format('h:i A') }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            @if($apt->status === 'confirmed')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Confirmed</span>
                                            @elseif($apt->status === 'completed')
                                                <span class="badge bg-info"><i class="bi bi-check-all me-1"></i> Completed</span>
                                            @else
                                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            @if($apt->payment_status === 'unpaid' && $apt->status !== 'cancelled')
                                                <a href="{{ route('patient.payments.checkout', $apt) }}" class="btn btn-success btn-sm px-3" title="Pay Consultation Fee">
                                                    <i class="bi bi-credit-card me-1"></i> Pay Now
                                                </a>
                                            @elseif($apt->invoice)
                                                <a href="{{ route('patient.invoices.show', $apt->invoice) }}" class="btn btn-outline-info btn-sm px-3" title="View Invoice">
                                                    <i class="bi bi-receipt me-1"></i> Invoice
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="bi bi-calendar-plus fs-3 d-block mb-1"></i>
                                            You have no upcoming appointments.
                                            <div class="mt-2">
                                                <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary btn-sm">Book Appointment Now</a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Profile -->
        <div class="col-lg-5">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-circle me-2 text-secondary"></i>Account & Profile Settings</h6>
                </div>
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-lg bg-primary-subtle text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                            <i class="bi bi-person-badge fs-3"></i>
                        </div>
                        <div>
                            <h6 class="mb-1 fw-bold text-dark">{{ auth()->user()->name }}</h6>
                            <p class="text-muted small mb-0">{{ auth()->user()->email }}</p>
                        </div>
                    </div>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary">
                            <i class="bi bi-gear me-1"></i> Edit Account Profile
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
