@extends('layouts.dashboard-layout')

@section('title', 'Appointment Details - ' . $appointment->appointment_number)
@section('page_title', 'Appointment Record #' . $appointment->appointment_number)

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1">
                <i class="bi bi-file-earmark-medical me-2 text-primary"></i>Appointment Details
            </h1>
            <small class="text-muted">Reference: <strong class="font-monospace text-primary">{{ $appointment->appointment_number }}</strong></small>
        </div>
        <a href="{{ route('admin.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to History
        </a>
    </div>

    <div class="row g-4">

        {{-- Column 1: Appointment & Payment Status --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i>Appointment Status & Schedule</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Reference Number</dt>
                        <dd class="col-sm-7"><span class="badge bg-dark-subtle text-dark border font-monospace">{{ $appointment->appointment_number }}</span></dd>

                        <dt class="col-sm-5 text-muted">Appointment Date</dt>
                        <dd class="col-sm-7 fw-semibold"><i class="bi bi-calendar3 text-primary me-1"></i>{{ $appointment->appointment_date?->format('d M Y') ?? 'N/A' }}</dd>

                        <dt class="col-sm-5 text-muted">Time Slot</dt>
                        <dd class="col-sm-7">
                            @if($appointment->slot)
                                <i class="bi bi-clock me-1"></i>
                                {{ \Carbon\Carbon::parse($appointment->slot->start_time)->format('h:i A') }}
                                &ndash;
                                {{ \Carbon\Carbon::parse($appointment->slot->end_time)->format('h:i A') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5 text-muted">Appointment Status</dt>
                        <dd class="col-sm-7">
                            @if($appointment->status === 'confirmed')
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Confirmed</span>
                            @elseif($appointment->status === 'completed')
                                <span class="badge bg-info"><i class="bi bi-check-all me-1"></i> Completed</span>
                            @elseif($appointment->status === 'cancelled')
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Cancelled</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                            @endif
                        </dd>

                        <dt class="col-sm-5 text-muted">Consultation Fee</dt>
                        <dd class="col-sm-7 fw-bold text-success">৳{{ number_format($appointment->consultation_fee, 2) }}</dd>

                        <dt class="col-sm-5 text-muted">Payment Status</dt>
                        <dd class="col-sm-7">
                            @if($appointment->payment_status === 'paid')
                                <span class="badge bg-success-subtle text-success border"><i class="bi bi-check-circle me-1"></i> Paid</span>
                            @else
                                <span class="badge bg-warning-subtle text-dark border"><i class="bi bi-hourglass-split me-1"></i> Unpaid</span>
                            @endif
                        </dd>

                        @if($appointment->invoice)
                            <dt class="col-sm-5 text-muted">Invoice Ref</dt>
                            <dd class="col-sm-7">
                                <span class="font-monospace text-primary fw-semibold">{{ $appointment->invoice->invoice_number }}</span>
                            </dd>
                        @endif

                        <dt class="col-sm-5 text-muted">Patient Notes</dt>
                        <dd class="col-sm-7 text-muted">{{ $appointment->patient_notes ?: 'No notes provided' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        {{-- Column 2: Doctor Information --}}
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-header bg-white py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-person-badge me-2 text-primary"></i>Doctor Information</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="avatar-md bg-info-subtle text-info rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width:48px;height:48px">
                            <i class="bi bi-person-badge-fill fs-4"></i>
                        </div>
                        <div>
                            <h6 class="mb-0 fw-bold">
                                {{ \Illuminate\Support\Str::startsWith($appointment->doctor?->user?->name, 'Dr.') ? $appointment->doctor?->user?->name : 'Dr. '.$appointment->doctor?->user?->name }}
                            </h6>
                            <small class="text-muted">{{ $appointment->doctor?->department?->name ?? 'General Medical' }}</small>
                        </div>
                    </div>
                    <dl class="row mb-0 small">
                        <dt class="col-sm-4 text-muted">Email</dt>
                        <dd class="col-sm-8">{{ $appointment->doctor?->user?->email }}</dd>

                        <dt class="col-sm-4 text-muted">Specialization</dt>
                        <dd class="col-sm-8">{{ $appointment->doctor?->specialization?->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-4 text-muted">Experience</dt>
                        <dd class="col-sm-8">{{ $appointment->doctor?->experience_years ? $appointment->doctor->experience_years.' Years' : 'N/A' }}</dd>
                    </dl>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
