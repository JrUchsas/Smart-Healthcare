@extends('layouts.dashboard-layout')

@section('title', 'Official Invoice - '.$invoice->invoice_number)
@section('page_title', 'Payment Receipt & Official Invoice')

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-9 col-xl-8">
            <div class="card shadow-sm border-0 mb-4" id="printable-invoice">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-heart-pulse-fill text-danger fs-3 me-2"></i>
                        <h5 class="mb-0 fw-bold">Smart Healthcare Clinic</h5>
                    </div>
                    <div>
                        <span class="badge bg-success fs-6 px-3 py-2"><i class="bi bi-check-circle-fill me-1"></i> PAID</span>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">
                    <div class="row mb-4">
                        <div class="col-sm-6">
                            <h6 class="text-uppercase text-muted fw-bold small">Billed To:</h6>
                            <h5 class="fw-bold mb-1 text-dark">{{ $invoice->patient?->user?->name ?? 'Patient' }}</h5>
                            <p class="text-muted small mb-0">{{ $invoice->patient?->user?->email }}</p>
                        </div>
                        <div class="col-sm-6 text-sm-end mt-3 mt-sm-0">
                            <h6 class="text-uppercase text-muted fw-bold small">Invoice Details:</h6>
                            <p class="mb-1"><strong>Invoice #:</strong> <span class="font-monospace text-primary fw-bold">{{ $invoice->invoice_number }}</span></p>
                            <p class="mb-1"><strong>Payment Date:</strong> {{ $invoice->issued_at ? $invoice->issued_at->format('F j, Y h:i A') : now()->format('F j, Y') }}</p>
                            <p class="mb-0"><strong>Payment Method:</strong> <span class="text-capitalize">{{ str_replace('_', ' ', $invoice->payment?->payment_method ?? 'credit_card') }}</span></p>
                        </div>
                    </div>

                    <!-- Appointment Summary Box -->
                    <div class="bg-light p-3 rounded mb-4 border">
                        <h6 class="fw-bold text-dark mb-2"><i class="bi bi-person-badge me-1 text-primary"></i>Consultation Service Details</h6>
                        <div class="row text-muted small">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Attending Doctor:</strong> {{ \Illuminate\Support\Str::startsWith($invoice->appointment?->doctor?->user?->name, 'Dr.') ? $invoice->appointment?->doctor?->user?->name : 'Dr. '.$invoice->appointment?->doctor?->user?->name }}</p>
                                <p class="mb-0"><strong>Department:</strong> {{ $invoice->appointment?->doctor?->department?->name ?? 'General Medical' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Appointment Reference:</strong> #{{ $invoice->appointment?->appointment_number }}</p>
                                <p class="mb-0"><strong>Scheduled Date:</strong> {{ $invoice->appointment?->appointment_date ? $invoice->appointment?->appointment_date->format('M j, Y') : 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Itemized Invoice Table -->
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Description</th>
                                    <th class="text-end">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <strong>Medical Consultation Fee</strong>
                                        <div class="small text-muted">Specialist doctor consultation and time slot reservation</div>
                                    </td>
                                    <td class="text-end fw-bold">${{ number_format($invoice->subtotal_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-bold">Subtotal:</td>
                                    <td class="text-end fw-bold">${{ number_format($invoice->subtotal_amount, 2) }}</td>
                                </tr>
                                <tr>
                                    <td class="text-end fw-bold">Tax & Service Charges:</td>
                                    <td class="text-end fw-bold">${{ number_format($invoice->tax_amount, 2) }}</td>
                                </tr>
                                <tr class="table-success">
                                    <td class="text-end fs-5 fw-bold text-success">Total Amount Paid:</td>
                                    <td class="text-end fs-5 fw-bold text-success">${{ number_format($invoice->total_amount, 2) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="d-flex justify-content-between align-items-center d-print-none mt-4">
                        <a href="{{ route('patient.appointments.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-left me-1"></i> Back to Appointments
                        </a>
                        <div class="d-flex gap-2">
                            <button type="button" onclick="window.print()" class="btn btn-primary">
                                <i class="bi bi-printer me-1"></i> Print Invoice
                            </button>
                            <a href="{{ route('patient.dashboard') }}" class="btn btn-success">
                                <i class="bi bi-house-door-fill me-1"></i> Go to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    @if(session('payment_success'))
        <script>
            $(document).ready(function() {
                Swal.fire({
                    title: 'Payment Successful!',
                    text: "{{ session('payment_success') }}",
                    icon: 'success',
                    confirmButtonText: 'View Invoice & Receipt',
                    confirmButtonColor: '#198754'
                });
            });
        </script>
    @endif
@endpush
