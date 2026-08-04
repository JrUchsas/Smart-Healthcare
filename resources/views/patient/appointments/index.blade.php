@extends('layouts.dashboard-layout')

@section('title', 'My Booked Appointments')
@section('page_title', 'My Appointments & Consultations')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>My Booked Appointments</h5>
            <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Book New Appointment
            </a>
        </div>
        <div class="card-body p-4">
            @if($appointments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted display-4"></i>
                    <h5 class="mt-3 text-secondary fw-bold">No Appointments Scheduled</h5>
                    <p class="text-muted">You have not booked any doctor appointments yet.</p>
                    <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary btn-sm mt-2">
                        <i class="bi bi-search me-1"></i> Find a Doctor & Book Slot
                    </a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Reference #</th>
                                <th>Doctor Details</th>
                                <th>Date & Time</th>
                                <th>Fee</th>
                                <th>Status</th>
                                <th>Symptoms / Notes</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($appointments as $apt)
                                <tr>
                                    <td>
                                        <span class="badge bg-dark-subtle text-dark border font-monospace">
                                            {{ $apt->appointment_number }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary-subtle text-primary rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="bi bi-person-badge fs-5"></i>
                                            </div>
                                            <div>
                                                <strong class="text-dark d-block">
                                                    {{ \Illuminate\Support\Str::startsWith($apt->doctor?->user?->name, 'Dr.') ? $apt->doctor?->user?->name : 'Dr. '.$apt->doctor?->user?->name }}
                                                </strong>
                                                <small class="text-muted">
                                                    {{ $apt->doctor?->department?->name ?? 'General Medical' }}
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>
                                            <strong class="text-dark d-block">
                                                <i class="bi bi-calendar3 text-primary me-1"></i>
                                                {{ $apt->appointment_date ? $apt->appointment_date->format('M j, Y') : 'N/A' }}
                                            </strong>
                                            @if($apt->slot)
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($apt->slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($apt->slot->end_time)->format('h:i A') }}
                                                </small>
                                            @endif
                                        </div>
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success">${{ number_format($apt->consultation_fee, 2) }}</span>
                                    </td>
                                    <td>
                                        @if($apt->status === 'confirmed')
                                            <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> Confirmed</span>
                                        @elseif($apt->status === 'completed')
                                            <span class="badge bg-info"><i class="bi bi-check-all me-1"></i> Completed</span>
                                        @elseif($apt->status === 'cancelled')
                                            <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> Cancelled</span>
                                        @else
                                            <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> Pending</span>
                                        @endif
                                    </td>
                                    <td>
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 180px;" title="{{ $apt->patient_notes }}">
                                            {{ $apt->patient_notes ?: 'No notes provided' }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            @if($apt->payment_status === 'unpaid' && $apt->status !== 'cancelled')
                                                <a href="{{ route('patient.payments.checkout', $apt) }}" class="btn btn-success" title="Pay Consultation Fee">
                                                    <i class="bi bi-credit-card me-1"></i> Pay Now
                                                </a>
                                            @elseif($apt->invoice)
                                                <a href="{{ route('patient.invoices.show', $apt->invoice) }}" class="btn btn-outline-info" title="View Invoice">
                                                    <i class="bi bi-receipt me-1"></i> Invoice
                                                </a>
                                            @endif
                                            <a href="{{ route('patient.appointments.edit', $apt) }}" class="btn btn-outline-primary" title="Edit Notes">
                                                <i class="bi bi-pencil-square me-1"></i> Edit
                                            </a>
                                            <button type="button" 
                                                    data-url="{{ route('patient.appointments.destroy', $apt) }}" 
                                                    data-number="{{ $apt->appointment_number }}"
                                                    class="btn btn-outline-danger cancel-appointment-btn" 
                                                    title="Cancel Appointment">
                                                <i class="bi bi-trash me-1"></i> Cancel
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $appointments->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Setup CSRF token
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle SweetAlert2 Appointment Cancellation
            $(document).on('click', '.cancel-appointment-btn', function() {
                var deleteUrl = $(this).data('url');
                var aptNumber = $(this).data('number');

                Swal.fire({
                    title: 'Cancel Appointment?',
                    text: 'Are you sure you want to cancel appointment #' + aptNumber + '? The reserved time slot will be released for other patients.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Cancel Appointment!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire(
                                    'Cancelled!',
                                    response.message || 'Your appointment has been cancelled.',
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message || 'Failed to cancel appointment.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
