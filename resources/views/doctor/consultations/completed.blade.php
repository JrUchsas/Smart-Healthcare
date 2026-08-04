@extends('layouts.dashboard-layout')

@section('title', 'Completed Consultations')
@section('page_title', 'Completed Consultations')

@section('content')
<div class="container-fluid px-0">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <span class="text-muted">All consultations where a prescription has been issued.</span>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clipboard2-pulse text-success me-2"></i>Consultation History</h5>
            <span class="badge bg-success-subtle text-success border px-3 py-2">
                {{ $consultations->total() }} Total Completed
            </span>
        </div>
        <div class="card-body p-0">
            @if($consultations->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-clipboard2-check fs-1 text-muted d-block mb-3"></i>
                    <h5 class="text-muted">No Completed Consultations Yet</h5>
                    <p class="text-muted">Consultations appear here after you issue a prescription to a patient.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Ref #</th>
                                <th>Patient</th>
                                <th>Appointment Date</th>
                                <th>Fee</th>
                                <th>Prescription</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($consultations as $apt)
                                <tr>
                                    <td>
                                        <span class="badge bg-dark-subtle text-dark border font-monospace">
                                            {{ $apt->appointment_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="bg-success-subtle text-success rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width:38px;height:38px">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div>
                                                <strong class="d-block">{{ $apt->patient?->user?->name ?? 'N/A' }}</strong>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-calendar3 text-success me-1"></i>
                                        <strong>{{ $apt->appointment_date?->format('d M Y') ?? 'N/A' }}</strong>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">${{ number_format($apt->consultation_fee, 2) }}</span>
                                        @if($apt->payment_status === 'paid')
                                            <span class="badge bg-success-subtle text-success border d-block mt-1">
                                                <i class="bi bi-check-circle me-1"></i> Paid
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($apt->prescription)
                                            <span class="badge bg-success-subtle text-success border">
                                                <i class="bi bi-file-medical me-1"></i> {{ $apt->prescription->prescription_number }}
                                            </span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-muted border">No Prescription</span>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        @if($apt->prescription)
                                            <a href="{{ route('doctor.prescriptions.show', $apt->prescription) }}" class="btn btn-outline-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i> View Rx
                                            </a>
                                            <a href="{{ route('doctor.prescriptions.pdf', $apt->prescription) }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                                                <i class="bi bi-file-pdf me-1"></i> PDF
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-3">
                    {{ $consultations->links() }}
                </div>
            @endif
        </div>
    </div>

</div>
@endsection
