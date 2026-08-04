@extends('layouts.dashboard-layout')

@section('title', 'System Settings')
@section('page_title', 'Global System Configuration')

@section('content')
<div class="container-fluid px-0">

    <form method="POST" action="{{ route('admin.settings.update') }}">
        @csrf
        @method('PUT')

        {{-- 1. Clinic & Hospital Identity --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-hospital me-2 text-primary"></i>Clinic & Hospital Identity</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label for="hospital_name" class="form-label fw-semibold">Healthcare Center Name</label>
                        <input type="text" id="hospital_name" name="hospital_name" value="{{ old('hospital_name', $settings['hospital_name']) }}" class="form-control @error('hospital_name') is-invalid @enderror" required>
                        @error('hospital_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-6">
                        <label for="hospital_tagline" class="form-label fw-semibold">Clinic Tagline / Subtitle</label>
                        <input type="text" id="hospital_tagline" name="hospital_tagline" value="{{ old('hospital_tagline', $settings['hospital_tagline']) }}" class="form-control @error('hospital_tagline') is-invalid @enderror">
                        @error('hospital_tagline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="contact_email" class="form-label fw-semibold">Clinic Support Email</label>
                        <input type="email" id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}" class="form-control @error('contact_email') is-invalid @enderror" required>
                        @error('contact_email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="contact_phone" class="form-label fw-semibold">Contact Phone Number</label>
                        <input type="text" id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}" class="form-control @error('contact_phone') is-invalid @enderror" required>
                        @error('contact_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="emergency_hotline" class="form-label fw-semibold text-danger">Emergency Hotline</label>
                        <input type="text" id="emergency_hotline" name="emergency_hotline" value="{{ old('emergency_hotline', $settings['emergency_hotline']) }}" class="form-control @error('emergency_hotline') is-invalid @enderror">
                        @error('emergency_hotline')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="hospital_address" class="form-label fw-semibold">Physical Clinic Address</label>
                        <textarea id="hospital_address" name="hospital_address" rows="2" class="form-control @error('hospital_address') is-invalid @enderror">{{ old('hospital_address', $settings['hospital_address']) }}</textarea>
                        @error('hospital_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. Financial & Billing Parameters --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-currency-dollar me-2 text-success"></i>Financial & Billing Parameters</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-3">
                        <label for="currency_symbol" class="form-label fw-semibold">Billing Currency Symbol</label>
                        <input type="text" id="currency_symbol" name="currency_symbol" value="{{ old('currency_symbol', $settings['currency_symbol']) }}" class="form-control @error('currency_symbol') is-invalid @enderror" required>
                        @error('currency_symbol')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="tax_percentage" class="form-label fw-semibold">Medical Tax Rate (%)</label>
                        <input type="number" step="0.01" id="tax_percentage" name="tax_percentage" value="{{ old('tax_percentage', $settings['tax_percentage']) }}" class="form-control @error('tax_percentage') is-invalid @enderror" required>
                        @error('tax_percentage')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="default_consultation_fee" class="form-label fw-semibold">Default Consultation Fee ($)</label>
                        <input type="number" step="0.01" id="default_consultation_fee" name="default_consultation_fee" value="{{ old('default_consultation_fee', $settings['default_consultation_fee']) }}" class="form-control @error('default_consultation_fee') is-invalid @enderror" required>
                        @error('default_consultation_fee')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="payment_mode" class="form-label fw-semibold">Payment Gateway Mode</label>
                        <select id="payment_mode" name="payment_mode" class="form-select @error('payment_mode') is-invalid @enderror">
                            <option value="sandbox" {{ $settings['payment_mode'] === 'sandbox' ? 'selected' : '' }}>Sandbox / Test Mode</option>
                            <option value="live" {{ $settings['payment_mode'] === 'live' ? 'selected' : '' }}>Live Production Mode</option>
                        </select>
                        @error('payment_mode')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Appointment Scheduling Rules --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-calendar-range me-2 text-info"></i>Appointment Scheduling Rules</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-3">
                        <label for="appointment_cancellation_hours" class="form-label fw-semibold">Free Cancellation Window (Hours)</label>
                        <input type="number" id="appointment_cancellation_hours" name="appointment_cancellation_hours" value="{{ old('appointment_cancellation_hours', $settings['appointment_cancellation_hours']) }}" class="form-control @error('appointment_cancellation_hours') is-invalid @enderror" required>
                        @error('appointment_cancellation_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="default_slot_duration" class="form-label fw-semibold">Default Slot Duration (Minutes)</label>
                        <select id="default_slot_duration" name="default_slot_duration" class="form-select @error('default_slot_duration') is-invalid @enderror">
                            <option value="15" {{ $settings['default_slot_duration'] == '15' ? 'selected' : '' }}>15 Minutes</option>
                            <option value="30" {{ $settings['default_slot_duration'] == '30' ? 'selected' : '' }}>30 Minutes</option>
                            <option value="45" {{ $settings['default_slot_duration'] == '45' ? 'selected' : '' }}>45 Minutes</option>
                            <option value="60" {{ $settings['default_slot_duration'] == '60' ? 'selected' : '' }}>60 Minutes</option>
                        </select>
                        @error('default_slot_duration')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="max_booking_advance_days" class="form-label fw-semibold">Max Booking Advance (Days)</label>
                        <input type="number" id="max_booking_advance_days" name="max_booking_advance_days" value="{{ old('max_booking_advance_days', $settings['max_booking_advance_days']) }}" class="form-control @error('max_booking_advance_days') is-invalid @enderror" required>
                        @error('max_booking_advance_days')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-3">
                        <label for="auto_confirm_appointments" class="form-label fw-semibold">Auto-Confirm Paid Appointments</label>
                        <select id="auto_confirm_appointments" name="auto_confirm_appointments" class="form-select @error('auto_confirm_appointments') is-invalid @enderror">
                            <option value="1" {{ $settings['auto_confirm_appointments'] == '1' ? 'selected' : '' }}>Yes - Enabled</option>
                            <option value="0" {{ $settings['auto_confirm_appointments'] == '0' ? 'selected' : '' }}>No - Require Manual Doctor Approval</option>
                        </select>
                        @error('auto_confirm_appointments')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Notifications & Feature Toggles --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-bell me-2 text-warning"></i>Notifications & Feature Toggles</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-4">
                        <label for="enable_email_notifications" class="form-label fw-semibold">Email Notifications & Reminders</label>
                        <select id="enable_email_notifications" name="enable_email_notifications" class="form-select @error('enable_email_notifications') is-invalid @enderror">
                            <option value="1" {{ $settings['enable_email_notifications'] == '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ $settings['enable_email_notifications'] == '0' ? 'selected' : '' }}>Disabled</option>
                        </select>
                        @error('enable_email_notifications')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="enable_sms_alerts" class="form-label fw-semibold">SMS Alert Notifications</label>
                        <select id="enable_sms_alerts" name="enable_sms_alerts" class="form-select @error('enable_sms_alerts') is-invalid @enderror">
                            <option value="1" {{ $settings['enable_sms_alerts'] == '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ $settings['enable_sms_alerts'] == '0' ? 'selected' : '' }}>Disabled</option>
                        </select>
                        @error('enable_sms_alerts')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-md-4">
                        <label for="enable_patient_chat" class="form-label fw-semibold">Real-Time Patient & Doctor Chat</label>
                        <select id="enable_patient_chat" name="enable_patient_chat" class="form-select @error('enable_patient_chat') is-invalid @enderror">
                            <option value="1" {{ $settings['enable_patient_chat'] == '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ $settings['enable_patient_chat'] == '0' ? 'selected' : '' }}>Disabled</option>
                        </select>
                        @error('enable_patient_chat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="col-12">
                        <label for="prescription_header_notice" class="form-label fw-semibold">Prescription Header Legal Notice</label>
                        <input type="text" id="prescription_header_notice" name="prescription_header_notice" value="{{ old('prescription_header_notice', $settings['prescription_header_notice']) }}" class="form-control @error('prescription_header_notice') is-invalid @enderror">
                        @error('prescription_header_notice')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end mb-4">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-semibold">
                <i class="bi bi-check-circle-fill me-2"></i> Save All System Settings
            </button>
        </div>
    </form>

</div>
@endsection
