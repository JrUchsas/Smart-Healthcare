@extends('layouts.dashboard-layout')

@section('title', 'Edit & Reschedule Appointment')
@section('page_title', 'Edit / Reschedule Appointment Details')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Appointment #{{ $appointment->appointment_number }}</h5>
                    <a href="{{ route('patient.appointments.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Appointments
                    </a>
                </div>
                <div class="card-body p-4">
                    <!-- Current Booking Summary -->
                    <div class="bg-light p-3 rounded mb-4 border">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-primary">Ref #: {{ $appointment->appointment_number }}</span>
                            <span class="fw-bold text-success">${{ number_format($appointment->consultation_fee, 2) }}</span>
                        </div>
                        <p class="mb-1"><strong>Doctor:</strong> {{ \Illuminate\Support\Str::startsWith($appointment->doctor?->user?->name, 'Dr.') ? $appointment->doctor?->user?->name : 'Dr. '.$appointment->doctor?->user?->name }} ({{ $appointment->doctor?->department?->name ?? 'General Medical' }})</p>
                        <p class="mb-0 small text-muted">
                            <strong>Current Slot:</strong> 
                            <i class="bi bi-calendar3 ms-1 me-1"></i> {{ $appointment->appointment_date ? $appointment->appointment_date->format('l, d M Y') : 'N/A' }}
                            @if($appointment->slot)
                                | <i class="bi bi-clock me-1"></i> {{ \Carbon\Carbon::parse($appointment->slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->slot->end_time)->format('h:i A') }}
                            @endif
                        </p>
                    </div>

                    <form action="{{ route('patient.appointments.update', $appointment) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Optional Reschedule Slot Selection -->
                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="bi bi-calendar-event text-primary me-1"></i> Reschedule Time Slot (Optional)</label>
                            <p class="small text-muted mb-2">Selecting a new slot will automatically release your previous slot back to empty on the doctor's schedule.</p>
                            
                            @if($availableSlots->isEmpty())
                                <div class="alert alert-secondary py-2 px-3 small">
                                    No other open slots available for Dr. {{ $appointment->doctor?->user?->name }} at this time.
                                </div>
                            @else
                                <div class="border rounded p-3 bg-light max-h-60 overflow-y-auto">
                                    @foreach($availableSlots as $date => $slots)
                                        <div class="mb-3">
                                            <h6 class="fw-bold small text-dark mb-2">
                                                <i class="bi bi-calendar-check text-primary me-1"></i>
                                                {{ \Carbon\Carbon::parse($date)->format('l, d M Y') }}
                                            </h6>
                                            <div class="d-flex flex-wrap gap-2">
                                                @foreach($slots as $slot)
                                                    <div class="form-check form-check-inline m-0">
                                                        <input class="btn-check" 
                                                               type="radio" 
                                                               name="appointment_slot_id" 
                                                               id="edit-slot-{{ $slot->id }}" 
                                                               value="{{ $slot->id }}" 
                                                               {{ (int) old('appointment_slot_id', $appointment->appointment_slot_id) === (int) $slot->id ? 'checked' : '' }}>
                                                        <label class="btn btn-outline-primary btn-sm px-3 py-1.5 fw-semibold" for="edit-slot-{{ $slot->id }}">
                                                            <i class="bi bi-clock me-1"></i>
                                                            {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <!-- Symptoms / Patient Notes -->
                        <div class="mb-4">
                            <label for="patient_notes" class="form-label fw-semibold">Symptoms / Patient Notes</label>
                            <textarea name="patient_notes" id="patient_notes" rows="3" class="form-control @error('patient_notes') is-invalid @enderror" placeholder="Describe your symptoms or reason for visit...">{{ old('patient_notes', $appointment->patient_notes) }}</textarea>
                            @error('patient_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('patient.appointments.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
