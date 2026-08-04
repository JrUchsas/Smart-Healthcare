@extends('layouts.dashboard-layout')

@section('title', 'Prescription – ' . $prescription->prescription_number)
@section('page_title', 'Prescription Details')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1"><i class="bi bi-file-medical-fill me-2 text-success"></i>Prescription Issued</h1>
            <small class="text-muted">Ref: <strong>{{ $prescription->prescription_number }}</strong></small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('doctor.prescriptions.pdf', $prescription) }}" target="_blank" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-eye me-1"></i> View PDF
            </a>
            <a href="{{ route('doctor.prescriptions.download', $prescription) }}" class="btn btn-success btn-sm">
                <i class="bi bi-download me-1"></i> Download PDF
            </a>
            <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-house-door me-1"></i> Dashboard
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-person-vcard me-2"></i>Patient & Clinical Details</h6>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-5 text-muted">Patient</dt>
                        <dd class="col-sm-7 fw-semibold">{{ $prescription->patient?->user?->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5 text-muted">Appointment</dt>
                        <dd class="col-sm-7">{{ $prescription->appointment?->appointment_number ?? 'N/A' }}</dd>

                        <dt class="col-sm-5 text-muted">Symptoms</dt>
                        <dd class="col-sm-7">{{ $prescription->symptoms ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted">Diagnosis</dt>
                        <dd class="col-sm-7">{{ $prescription->diagnosis ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted">Advice</dt>
                        <dd class="col-sm-7">{{ $prescription->advice ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted">Follow-Up Date</dt>
                        <dd class="col-sm-7">{{ $prescription->follow_up_date?->format('d M Y') ?? '—' }}</dd>

                        <dt class="col-sm-5 text-muted">Issued By</dt>
                        <dd class="col-sm-7 fw-semibold">Dr. {{ $prescription->doctor?->user?->name ?? 'N/A' }}</dd>

                        <dt class="col-sm-5 text-muted">Issued On</dt>
                        <dd class="col-sm-7">{{ $prescription->created_at->format('d M Y h:i A') }}</dd>
                    </dl>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="bi bi-capsule me-2"></i>Medicines Prescribed</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Medicine</th>
                                    <th>Dosage</th>
                                    <th>Duration</th>
                                    <th>Instructions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($prescription->items as $i => $item)
                                    <tr>
                                        <td class="text-muted">{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $item->medicine_name }}</td>
                                        <td>{{ $item->dosage }}</td>
                                        <td>{{ $item->duration }}</td>
                                        <td class="text-muted">{{ $item->instructions ?? '—' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">No medicines recorded.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
