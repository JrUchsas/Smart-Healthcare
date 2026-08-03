@extends('layouts.dashboard-layout')

@section('title', 'Create Schedule Block')
@section('page_title', 'Set Availability & Auto-Generate Slots')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-plus-circle text-primary me-2"></i>New Availability Block</h5>
            <a href="{{ route('doctor.schedules.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to Schedule List
            </a>
        </div>
        <div class="card-body p-4">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center mb-4">
                <i class="bi bi-info-circle-fill fs-4 me-3 text-info"></i>
                <div>
                    <strong>Automated Slot Generation:</strong>
                    When you specify a date and timeframe (e.g. 09:00 AM to 01:00 PM with 30-minute intervals), the system will automatically create individual, non-overlapping patient appointment slots for you.
                </div>
            </div>

            <form action="{{ route('doctor.schedules.store') }}" method="POST">
                @csrf

                @if(isset($isAdmin) && $isAdmin)
                    <div class="mb-3">
                        <label for="doctor_id" class="form-label fw-semibold">Select Doctor <span class="text-danger">*</span></label>
                        <select name="doctor_id" id="doctor_id" class="form-select @error('doctor_id') is-invalid @enderror" required>
                            <option value="">-- Choose Doctor --</option>
                            @foreach($doctors as $doc)
                                <option value="{{ $doc->id }}" {{ old('doctor_id') == $doc->id ? 'selected' : '' }}>
                                    Dr. {{ $doc->user?->name ?? 'Doctor #'.$doc->id }}
                                </option>
                            @endforeach
                        </select>
                        @error('doctor_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="schedule_date" class="form-label fw-semibold">Schedule Date <span class="text-danger">*</span></label>
                        <input type="date" 
                               name="schedule_date" 
                               id="schedule_date" 
                               min="{{ date('Y-m-d') }}" 
                               class="form-control @error('schedule_date') is-invalid @enderror" 
                               value="{{ old('schedule_date', date('Y-m-d')) }}" 
                               required>
                        @error('schedule_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="slot_duration_minutes" class="form-label fw-semibold">Consultation Duration / Slot <span class="text-danger">*</span></label>
                        <select name="slot_duration_minutes" id="slot_duration_minutes" class="form-select @error('slot_duration_minutes') is-invalid @enderror" required>
                            <option value="15" {{ old('slot_duration_minutes') == '15' ? 'selected' : '' }}>15 Minutes</option>
                            <option value="20" {{ old('slot_duration_minutes') == '20' ? 'selected' : '' }}>20 Minutes</option>
                            <option value="30" {{ old('slot_duration_minutes', '30') == '30' ? 'selected' : '' }}>30 Minutes (Recommended)</option>
                            <option value="45" {{ old('slot_duration_minutes') == '45' ? 'selected' : '' }}>45 Minutes</option>
                            <option value="60" {{ old('slot_duration_minutes') == '60' ? 'selected' : '' }}>60 Minutes (1 Hour)</option>
                        </select>
                        @error('slot_duration_minutes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="start_time" class="form-label fw-semibold">Shift Start Time <span class="text-danger">*</span></label>
                        <input type="time" 
                               name="start_time" 
                               id="start_time" 
                               class="form-control @error('start_time') is-invalid @enderror" 
                               value="{{ old('start_time', '09:00') }}" 
                               required>
                        @error('start_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="end_time" class="form-label fw-semibold">Shift End Time <span class="text-danger">*</span></label>
                        <input type="time" 
                               name="end_time" 
                               id="end_time" 
                               class="form-control @error('end_time') is-invalid @enderror" 
                               value="{{ old('end_time', '13:00') }}" 
                               required>
                        @error('end_time')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('doctor.schedules.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-gear-wide-connected me-1"></i> Generate Time Slots</button>
                </div>
            </form>
        </div>
    </div>
@endsection
