@extends('layouts.dashboard-layout')

@section('title', 'Revenue & Financial Reports')
@section('page_title', 'Revenue Analytics & Financial Ledger')

@section('content')
<div class="container-fluid px-0">

    {{-- Financial Metrics Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm border-0 text-white" style="background: linear-gradient(135deg, #059669, #10b981);">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">${{ number_format($stats['total_revenue'], 2) }}</div>
                        <small class="opacity-75">Total Gross Revenue</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm border-0 text-white" style="background: linear-gradient(135deg, #4f46e5, #7c3aed);">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex">
                        <i class="bi bi-calendar-check fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">${{ number_format($stats['monthly_revenue'], 2) }}</div>
                        <small class="opacity-75">Monthly Revenue ({{ \Carbon\Carbon::parse($month.'-01')->format('M Y') }})</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm border-0 text-white" style="background: linear-gradient(135deg, #0ea5e9, #0284c7);">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex">
                        <i class="bi bi-clock-history fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">${{ number_format($stats['today_revenue'], 2) }}</div>
                        <small class="opacity-75">Today's Revenue</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3">
            <div class="card stat-card shadow-sm border-0 text-white" style="background: linear-gradient(135deg, #d97706, #f59e0b);">
                <div class="card-body d-flex align-items-center gap-3 p-3">
                    <div class="rounded-circle bg-white bg-opacity-25 p-3 d-flex">
                        <i class="bi bi-receipt-cutoff fs-4"></i>
                    </div>
                    <div>
                        <div class="fs-3 fw-bold lh-1">{{ $stats['paid_invoices_count'] }}</div>
                        <small class="opacity-75">Total Paid Invoices</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter Form --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3 px-4">
            <form method="GET" action="{{ route('admin.reports.revenue') }}" class="row g-3 align-items-end">
                <div class="col-md-4 col-sm-6">
                    <label for="month" class="form-label small fw-bold text-muted">Select Report Month</label>
                    <input type="month" id="month" name="month" value="{{ $month }}" class="form-control form-control-sm">
                </div>
                <div class="col-md-4 col-sm-6">
                    <label for="department_id" class="form-label small fw-bold text-muted">Filter by Department</label>
                    <select id="department_id" name="department_id" class="form-select form-select-sm">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (string)$departmentId === (string)$dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm px-4">
                        <i class="bi bi-filter me-1"></i> Apply Filters
                    </button>
                    <a href="{{ route('admin.reports.revenue') }}" class="btn btn-outline-secondary btn-sm">
                        Reset
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Department Revenue Breakdown --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold"><i class="bi bi-hospital me-2 text-primary"></i>Revenue Breakdown by Department</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4">Department Name</th>
                            <th>Active Doctors</th>
                            <th>Paid Consultations</th>
                            <th>Monthly Revenue ({{ \Carbon\Carbon::parse($month.'-01')->format('M Y') }})</th>
                            <th class="text-end pe-4">Total All-Time Revenue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($departments as $dept)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">
                                    <i class="bi bi-building me-2 text-secondary"></i>{{ $dept->name }}
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $dept->doctors_count }} Doctors</span></td>
                                <td><span class="badge bg-primary-subtle text-primary border">{{ $dept->paid_appointments_count }} Consultations</span></td>
                                <td class="fw-bold text-primary">${{ number_format($dept->monthly_revenue, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-success">${{ number_format($dept->total_revenue, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No department revenue records available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Paid Invoices & Financial Ledger --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold"><i class="bi bi-receipt me-2 text-success"></i>Paid Invoices & Payment Ledger</h5>
            <small class="text-muted">Showing {{ $invoices->firstItem() ?? 0 }} - {{ $invoices->lastItem() ?? 0 }} of {{ $invoices->total() }} invoices</small>
        </div>
        <div class="card-body p-0">
            @if($invoices->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-receipt-cutoff text-muted display-4"></i>
                    <h5 class="mt-3 fw-semibold text-secondary">No Invoices Found</h5>
                    <p class="text-muted mb-0">No paid invoices match the selected month and department filters.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="ps-4">Invoice #</th>
                                <th>Patient</th>
                                <th>Doctor & Dept</th>
                                <th>Payment Method</th>
                                <th>Issued Date</th>
                                <th class="text-end pe-4">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($invoices as $inv)
                                <tr>
                                    <td class="ps-4">
                                        <a href="{{ route('patient.invoices.show', $inv) }}" class="font-monospace fw-bold text-primary text-decoration-none">
                                            {{ $inv->invoice_number }}
                                        </a>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $inv->patient?->user?->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $inv->patient?->user?->email }}</small>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            {{ \Illuminate\Support\Str::startsWith($inv->appointment?->doctor?->user?->name, 'Dr.') ? $inv->appointment?->doctor?->user?->name : 'Dr. '.$inv->appointment?->doctor?->user?->name }}
                                        </div>
                                        <small class="text-muted">{{ $inv->appointment?->doctor?->department?->name ?? 'General Medical' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border text-capitalize">
                                            <i class="bi bi-credit-card me-1"></i>
                                            {{ str_replace('_', ' ', $inv->payment?->payment_method ?? 'Card Payment') }}
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-dark fw-semibold">
                                            <i class="bi bi-calendar3 text-muted me-1"></i>
                                            {{ $inv->issued_at?->format('d M Y, h:i A') ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td class="text-end pe-4">
                                        <span class="fw-bold text-success fs-6">${{ number_format($inv->total_amount, 2) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($invoices->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $invoices->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>
@endsection
