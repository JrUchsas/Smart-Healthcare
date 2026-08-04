@extends('layouts.dashboard-layout')

@section('title', $pageTitle)
@section('page_title', $pageTitle)

@section('content')
<div class="container-fluid px-0">
    @if($type === 'booked')
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-hourglass-split text-primary me-2"></i>Booked Appointments</h5>
                <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Book New Appointment
                </a>
            </div>
            <div class="card-body p-0">
                @if($appointments->isEmpty())
                    <div class="text-center py-5 px-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-primary-subtle" style="width:72px;height:72px">
                                <i class="bi bi-calendar-plus fs-2 text-primary"></i>
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark">No Booked Appointments</h5>
                        <p class="text-muted mb-4">You have no pending or confirmed appointments. Find a doctor and book a slot to get started.</p>
                        <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary px-4">
                            <i class="bi bi-search me-1"></i> Find a Doctor
                        </a>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="ps-4">Reference #</th>
                                    <th>Doctor</th>
                                    <th>Date & Time</th>
                                    <th>Fee</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $apt)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-dark-subtle text-dark border font-monospace fw-semibold">
                                                {{ $apt->appointment_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width:38px;height:38px">
                                                    <i class="bi bi-person-badge"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark lh-sm">
                                                        {{ \Illuminate\Support\Str::startsWith($apt->doctor?->user?->name, 'Dr.') ? $apt->doctor?->user?->name : 'Dr. '.$apt->doctor?->user?->name }}
                                                    </div>
                                                    <small class="text-muted">{{ $apt->doctor?->department?->name ?? 'General Medical' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">
                                                <i class="bi bi-calendar3 text-primary me-1"></i>
                                                {{ $apt->appointment_date?->format('d M Y') ?? 'N/A' }}
                                            </div>
                                            @if($apt->slot)
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($apt->slot->start_time)->format('h:i A') }}
                                                    &ndash;
                                                    {{ \Carbon\Carbon::parse($apt->slot->end_time)->format('h:i A') }}
                                                </small>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="fw-bold text-success">${{ number_format($apt->consultation_fee, 2) }}</div>
                                            @if($apt->payment_status === 'paid')
                                                <span class="badge bg-success-subtle text-success border mt-1">
                                                    <i class="bi bi-check-circle me-1"></i> Paid
                                                </span>
                                            @else
                                                <span class="badge bg-warning-subtle text-dark border mt-1">
                                                    <i class="bi bi-hourglass-split me-1"></i> Unpaid
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($apt->status === 'confirmed')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i> Confirmed
                                                </span>
                                            @else
                                                <span class="badge bg-warning text-dark">
                                                    <i class="bi bi-hourglass-split me-1"></i> Pending
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted" style="max-width:150px;display:block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap"
                                                title="{{ $apt->patient_notes }}">
                                                {{ $apt->patient_notes ?: '—' }}
                                            </small>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="d-flex gap-1 justify-content-end flex-wrap">
                                                @if($apt->payment_status === 'unpaid' && $apt->status !== 'cancelled')
                                                    <a href="{{ route('patient.payments.checkout', $apt) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="bi bi-credit-card me-1"></i> Pay Now
                                                    </a>
                                                @elseif($apt->invoice)
                                                    <a href="{{ route('patient.invoices.show', $apt->invoice) }}"
                                                        class="btn btn-outline-info btn-sm">
                                                        <i class="bi bi-receipt me-1"></i> Invoice
                                                    </a>
                                                @endif
                                                <a href="{{ route('patient.appointments.edit', $apt) }}"
                                                    class="btn btn-outline-primary btn-sm">
                                                    <i class="bi bi-pencil-square me-1"></i> Edit
                                                </a>
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-sm cancel-appointment-btn"
                                                    data-url="{{ route('patient.appointments.destroy', $apt) }}"
                                                    data-number="{{ $apt->appointment_number }}">
                                                    <i class="bi bi-x-circle me-1"></i> Cancel
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($appointments->hasPages())
                        <div class="px-4 py-3 border-top">
                            {{ $appointments->appends(['type' => $type])->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="bi bi-check-all text-success me-2"></i>Completed Appointments</h5>
                <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Book New Appointment
                </a>
            </div>
            <div class="card-body p-0">
                @if($appointments->isEmpty())
                    <div class="text-center py-5 px-4">
                        <div class="mb-3">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle" style="width:72px;height:72px">
                                <i class="bi bi-clipboard2-pulse fs-2 text-success"></i>
                            </span>
                        </div>
                        <h5 class="fw-bold text-dark">No Completed Visits Yet</h5>
                        <p class="text-muted">Appointments appear here after your doctor has issued a prescription following your consultation.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="ps-4">Reference #</th>
                                    <th>Doctor</th>
                                    <th>Visit Date</th>
                                    <th>Fee Paid</th>
                                    <th>Prescription</th>
                                    <th class="text-end pe-4">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($appointments as $apt)
                                    <tr>
                                        <td class="ps-4">
                                            <span class="badge bg-dark-subtle text-dark border font-monospace fw-semibold">
                                                {{ $apt->appointment_number }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0"
                                                    style="width:38px;height:38px">
                                                    <i class="bi bi-person-badge"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark lh-sm">
                                                        {{ \Illuminate\Support\Str::startsWith($apt->doctor?->user?->name, 'Dr.') ? $apt->doctor?->user?->name : 'Dr. '.$apt->doctor?->user?->name }}
                                                    </div>
                                                    <small class="text-muted">{{ $apt->doctor?->department?->name ?? 'General Medical' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-semibold text-dark">
                                                <i class="bi bi-calendar3 text-success me-1"></i>
                                                {{ $apt->appointment_date?->format('d M Y') ?? 'N/A' }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fw-bold text-success">${{ number_format($apt->consultation_fee, 2) }}</span>
                                            <span class="badge bg-success-subtle text-success border d-block mt-1">
                                                <i class="bi bi-check-circle me-1"></i> Paid
                                            </span>
                                        </td>
                                        <td>
                                            @if($apt->prescription)
                                                <span class="badge bg-success-subtle text-success border">
                                                    <i class="bi bi-file-medical me-1"></i> Issued
                                                </span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-muted border">—</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            @if($apt->prescription)
                                                <div class="d-flex gap-1 justify-content-end">
                                                    <a href="{{ route('patient.prescriptions.show', $apt->prescription) }}"
                                                        class="btn btn-outline-success btn-sm">
                                                        <i class="bi bi-file-medical me-1"></i> View Rx
                                                    </a>
                                                    <a href="{{ route('patient.prescriptions.download', $apt->prescription) }}"
                                                        class="btn btn-success btn-sm">
                                                        <i class="bi bi-download me-1"></i> PDF
                                                    </a>
                                                </div>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($appointments->hasPages())
                        <div class="px-4 py-3 border-top">
                            {{ $appointments->appends(['type' => $type])->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });

        $(document).on('click', '.cancel-appointment-btn', function () {
            var deleteUrl = $(this).data('url');
            var aptNumber = $(this).data('number');

            Swal.fire({
                title: 'Cancel Appointment?',
                text: 'Cancel appointment #' + aptNumber + '? The reserved time slot will be released for other patients.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, Cancel It',
            }).then(function (result) {
                if (result.isConfirmed) {
                    $.ajax({
                        url: deleteUrl,
                        type: 'DELETE',
                        success: function (response) {
                            Swal.fire('Cancelled!', response.message || 'Your appointment has been cancelled.', 'success')
                                .then(function () { window.location.reload(); });
                        },
                        error: function (xhr) {
                            Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to cancel appointment.', 'error');
                        },
                    });
                }
            });
        });
    });
</script>
@endpush
