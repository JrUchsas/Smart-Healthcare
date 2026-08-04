@extends('layouts.dashboard-layout')

@section('title', 'Book Appointment')
@section('page_title', 'Select Slot & Confirm Appointment')

@section('content')
    <div class="row g-4">
        <!-- Left Column: Doctor Overview -->
        <div class="col-md-5 col-lg-4">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4 text-center">
                    <div class="avatar-xl bg-primary-subtle text-primary rounded-circle mx-auto p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                        <i class="bi bi-person-badge fs-1"></i>
                    </div>
                    <h5 class="fw-bold mb-1">{{ \Illuminate\Support\Str::startsWith($doctor->user?->name, 'Dr.') ? $doctor->user?->name : 'Dr. '.$doctor->user?->name }}</h5>
                    <span class="badge bg-primary mb-3">{{ $doctor->department?->name ?? 'Medical Department' }}</span>

                    <hr class="text-muted">

                    <div class="text-start">
                        <p class="small mb-2"><i class="bi bi-patch-check text-info me-2"></i><strong>Specialty:</strong> {{ $doctor->specialization?->name ?? 'General Practice' }}</p>
                        <p class="small mb-2"><i class="bi bi-mortarboard me-2"></i><strong>Qualification:</strong> {{ $doctor->qualification ?? 'MBBS, MD' }}</p>
                        <p class="small mb-2"><i class="bi bi-briefcase me-2"></i><strong>Experience:</strong> {{ $doctor->experience_years }} Years</p>
                        <p class="small mb-3"><i class="bi bi-telephone me-2"></i><strong>Contact:</strong> {{ $doctor->phone ?? 'Hospital Desk' }}</p>

                        @if($doctor->bio)
                            <div class="bg-light p-3 rounded small text-muted">
                                <strong>About Doctor:</strong><br>
                                {{ $doctor->bio }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-4 p-3 bg-success-subtle border border-success-subtle rounded">
                        <small class="text-success fw-semibold d-block">Consultation Fee</small>
                        <span class="fs-4 fw-bold text-success">${{ number_format($doctor->consultation_fee, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Slot Selection & Booking Form -->
        <div class="col-md-7 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history text-primary me-2"></i>Available Appointment Slots</h5>
                    <a href="{{ route('patient.doctors.search') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Doctor Search
                    </a>
                </div>
                <div class="card-body p-4">
                    @if($availableSlots->isEmpty())
                        <div class="text-center py-5">
                            <i class="bi bi-calendar-x text-muted display-4"></i>
                            <h5 class="mt-3 text-secondary fw-bold">No Open Slots Available</h5>
                            <p class="text-muted">{{ \Illuminate\Support\Str::startsWith($doctor->user?->name, 'Dr.') ? $doctor->user?->name : 'Dr. '.$doctor->user?->name }} currently has no active open appointment slots.</p>
                            <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary btn-sm">Search Other Doctors</a>
                        </div>
                    @else
                        <form id="booking-form" action="{{ route('patient.appointments.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="doctor_id" value="{{ $doctor->id }}">

                            <div class="mb-4">
                                <label class="form-label fw-bold"><i class="bi bi-calendar3 text-primary me-1"></i> Choose Date & Time Slot <span class="text-danger">*</span></label>
                                
                                @foreach($availableSlots as $date => $slots)
                                    <div class="mb-3 border rounded p-3 bg-light">
                                        <h6 class="fw-bold mb-2 text-dark">
                                            <i class="bi bi-calendar-check text-primary me-1"></i>
                                            {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                                        </h6>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($slots as $slot)
                                                <div class="form-check form-check-inline slot-pill m-0">
                                                    <input class="btn-check slot-radio" 
                                                           type="radio" 
                                                           name="appointment_slot_id" 
                                                           id="slot-{{ $slot->id }}" 
                                                           value="{{ $slot->id }}" 
                                                           data-date="{{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}"
                                                           data-time="{{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}"
                                                           required>
                                                    <label class="btn btn-outline-primary btn-sm px-3 py-2 fw-semibold" for="slot-{{ $slot->id }}">
                                                        <i class="bi bi-clock me-1"></i>
                                                        {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                    </label>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="mb-4">
                                <label for="patient_notes" class="form-label fw-semibold">Symptoms / Patient Notes (Optional)</label>
                                <textarea name="patient_notes" id="patient_notes" rows="3" class="form-control" placeholder="Briefly describe your symptoms or reason for visit..."></textarea>
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('patient.doctors.search') }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-success px-4" id="submit-booking-btn">
                                    <i class="bi bi-check2-circle me-1"></i> Confirm Booking (${{ number_format($doctor->consultation_fee, 2) }})
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Setup CSRF header
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle SweetAlert2 Confirmation Dialog before submitting booking
            $('#booking-form').on('submit', function(e) {
                e.preventDefault();

                var selectedRadio = $('.slot-radio:checked');
                if (!selectedRadio.length) {
                    Swal.fire('Please Select a Slot', 'Choose an available time slot before proceeding.', 'warning');
                    return;
                }

                var slotDate = selectedRadio.data('date');
                var slotTime = selectedRadio.data('time');
                var doctorName = "{{ \Illuminate\Support\Str::startsWith($doctor->user?->name, 'Dr.') ? $doctor->user?->name : 'Dr. '.$doctor->user?->name }}";
                var fee = "${{ number_format($doctor->consultation_fee, 2) }}";

                Swal.fire({
                    title: 'Confirm Appointment Booking?',
                    html: `
                        <div class="text-start fs-6 p-3 bg-light rounded border">
                            <p class="mb-1"><strong>Doctor:</strong> ${doctorName}</p>
                            <p class="mb-1"><strong>Date:</strong> ${slotDate}</p>
                            <p class="mb-1"><strong>Time:</strong> ${slotTime}</p>
                            <p class="mb-0 text-success"><strong>Fee:</strong> ${fee}</p>
                        </div>
                    `,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#198754',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, Confirm Booking!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        var formData = $('#booking-form').serialize();

                        $.ajax({
                            url: "{{ route('patient.appointments.store') }}",
                            type: 'POST',
                            data: formData,
                            beforeSend: function() {
                                $('#submit-booking-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span> Booking...');
                            },
                            success: function(response) {
                                Swal.fire(
                                    'Appointment Booked!',
                                    response.message || 'Your appointment has been successfully scheduled.',
                                    'success'
                                ).then(() => {
                                    window.location.href = response.redirect_url || "{{ route('patient.dashboard') }}";
                                });
                            },
                            error: function(xhr) {
                                $('#submit-booking-btn').prop('disabled', false).html('<i class="bi bi-check2-circle me-1"></i> Confirm Booking');
                                Swal.fire(
                                    'Booking Failed',
                                    xhr.responseJSON?.message || 'Selected slot is no longer available.',
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
