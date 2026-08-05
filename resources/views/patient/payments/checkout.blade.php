@extends('layouts.dashboard-layout')

@section('title', 'Checkout - Pay Consultation Fee')
@section('page_title', 'Checkout & Payment Processing')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-credit-card text-primary me-2"></i>Pay Consultation Fee</h5>
                    <span class="badge bg-warning text-dark"><i class="bi bi-shield-lock me-1"></i>Secure Checkout</span>
                </div>
                <div class="card-body p-4">
                    <!-- Order Summary Card -->
                    <div class="bg-light p-3 rounded mb-4 border">
                        <h6 class="fw-bold text-dark mb-3"><i class="bi bi-receipt-cutoff me-1 text-primary"></i>Appointment Order Summary</h6>
                        <div class="row g-2 text-dark">
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Doctor</small>
                                <strong>{{ \Illuminate\Support\Str::startsWith($appointment->doctor?->user?->name, 'Dr.') ? $appointment->doctor?->user?->name : 'Dr. '.$appointment->doctor?->user?->name }}</strong>
                                <small class="text-muted d-block">{{ $appointment->doctor?->department?->name ?? 'General Medical' }}</small>
                            </div>
                            <div class="col-sm-6">
                                <small class="text-muted d-block">Date & Time</small>
                                <strong>{{ $appointment->appointment_date ? $appointment->appointment_date->format('M j, Y') : 'N/A' }}</strong>
                                @if($appointment->slot)
                                    <small class="text-muted d-block">{{ \Carbon\Carbon::parse($appointment->slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($appointment->slot->end_time)->format('h:i A') }}</small>
                                @endif
                            </div>
                        </div>
                        <hr class="my-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">Total Consultation Fee Due:</span>
                            <span class="fs-4 fw-bold text-success">৳{{ number_format($appointment->consultation_fee, 2) }}</span>
                        </div>
                    </div>

                    <!-- Payment Form -->
                    <form action="{{ route('patient.payments.process') }}" method="POST" id="payment-form">
                        @csrf
                        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Select Payment Method</label>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_card" value="credit_card" checked>
                                    <label class="btn btn-outline-primary w-100 p-3 text-start" for="pay_card">
                                        <i class="bi bi-credit-card-2-front fs-4 d-block mb-1"></i>
                                        <strong>Credit / Debit Card</strong>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <input type="radio" class="btn-check" name="payment_method" id="pay_paypal" value="paypal">
                                    <label class="btn btn-outline-primary w-100 p-3 text-start" for="pay_paypal">
                                        <i class="bi bi-paypal fs-4 d-block mb-1 text-info"></i>
                                        <strong>PayPal Express</strong>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Credit Card Input Section -->
                        <div id="credit-card-fields">
                            <div class="mb-3">
                                <label for="card_holder_name" class="form-label fw-semibold">Cardholder Name</label>
                                <input type="text" name="card_holder_name" id="card_holder_name" class="form-control @error('card_holder_name') is-invalid @enderror" value="{{ old('card_holder_name', auth()->user()->name) }}" placeholder="e.g. John Doe">
                                @error('card_holder_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="mb-3">
                                <label for="card_number" class="form-label fw-semibold">Card Number</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-credit-card"></i></span>
                                    <input type="text" name="card_number" id="card_number" class="form-control @error('card_number') is-invalid @enderror" value="{{ old('card_number', '4242 4242 4242 4242') }}" placeholder="4242 4242 4242 4242" maxlength="19">
                                </div>
                                @error('card_number') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-6">
                                    <label for="card_expiry" class="form-label fw-semibold">Expiry Date (MM/YY)</label>
                                    <input type="text" name="card_expiry" id="card_expiry" class="form-control @error('card_expiry') is-invalid @enderror" value="{{ old('card_expiry', '12/28') }}" placeholder="MM/YY" maxlength="7">
                                    @error('card_expiry') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label for="card_cvv" class="form-label fw-semibold">CVV / CVC Code</label>
                                    <input type="password" name="card_cvv" id="card_cvv" class="form-control @error('card_cvv') is-invalid @enderror" value="{{ old('card_cvv', '123') }}" placeholder="123" maxlength="4">
                                    @error('card_cvv') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('patient.appointments.index') }}" class="btn btn-light">
                                <i class="bi bi-arrow-left me-1"></i> Back to Appointments
                            </a>
                            <button type="submit" class="btn btn-success btn-lg px-4" id="submit-pay-btn">
                                <i class="bi bi-lock-fill me-1"></i> Pay ৳{{ number_format($appointment->consultation_fee, 2) }} Now
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
