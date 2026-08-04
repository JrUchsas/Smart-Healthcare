@extends('layouts.dashboard-layout')

@section('title', 'My Prescriptions')

@section('content')
<div class="container-fluid px-4 py-4">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1"><i class="bi bi-file-medical me-2 text-primary"></i>My Prescriptions</h1>
            <small class="text-muted">All prescriptions issued to you by your doctors.</small>
        </div>
    </div>

    @if($prescriptions->isEmpty())
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <i class="bi bi-file-medical fs-1 text-muted d-block mb-3"></i>
                <h5 class="text-muted">No Prescriptions Yet</h5>
                <p class="text-muted mb-4">Prescriptions will appear here after your doctor issues them following a confirmed consultation.</p>
                <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary">
                    <i class="bi bi-search me-1"></i> Find a Doctor
                </a>
            </div>
        </div>
    @else
        <div class="row g-4">
            @foreach($prescriptions as $prescription)
                <div class="col-md-6 col-xl-4">
                    <div class="card shadow-sm border-0 h-100">
                        <div class="card-header d-flex align-items-center justify-content-between">
                            <span class="fw-bold font-monospace text-primary">{{ $prescription->prescription_number }}</span>
                            <span class="badge bg-success-subtle text-success border">
                                <i class="bi bi-check-circle me-1"></i> Issued
                            </span>
                        </div>
                        <div class="card-body">
                            <dl class="row mb-0 small">
                                <dt class="col-5 text-muted">Doctor</dt>
                                <dd class="col-7 fw-semibold">Dr. {{ $prescription->doctor?->user?->name ?? 'N/A' }}</dd>

                                <dt class="col-5 text-muted">Appointment</dt>
                                <dd class="col-7">{{ $prescription->appointment?->appointment_number ?? 'N/A' }}</dd>

                                <dt class="col-5 text-muted">Diagnosis</dt>
                                <dd class="col-7">{{ Str::limit($prescription->diagnosis, 50) ?? '—' }}</dd>

                                <dt class="col-5 text-muted">Medicines</dt>
                                <dd class="col-7">
                                    <span class="badge bg-primary-subtle text-primary border">
                                        {{ $prescription->items->count() }} item(s)
                                    </span>
                                </dd>

                                <dt class="col-5 text-muted">Follow-Up</dt>
                                <dd class="col-7">{{ $prescription->follow_up_date?->format('d M Y') ?? '—' }}</dd>

                                <dt class="col-5 text-muted">Issued On</dt>
                                <dd class="col-7 text-muted">{{ $prescription->created_at->format('d M Y') }}</dd>
                            </dl>
                        </div>
                        <div class="card-footer bg-transparent border-top-0 pt-0 pb-3 px-3 d-flex gap-2">
                            <a href="{{ route('patient.prescriptions.show', $prescription) }}" class="btn btn-outline-primary btn-sm flex-fill">
                                <i class="bi bi-eye me-1"></i> View
                            </a>
                            <a href="{{ route('patient.prescriptions.pdf', $prescription) }}" target="_blank" class="btn btn-outline-secondary btn-sm flex-fill">
                                <i class="bi bi-file-pdf me-1"></i> Open PDF
                            </a>
                            <a href="{{ route('patient.prescriptions.download', $prescription) }}" class="btn btn-success btn-sm flex-fill">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

</div>
@endsection
