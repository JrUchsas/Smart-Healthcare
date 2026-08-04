@extends('layouts.dashboard-layout')

@section('title', 'Appointment History')
@section('page_title', 'Appointment History & System Records')

@section('content')
<div class="container-fluid px-0">

    {{-- Filter Pills & Quick Stats --}}
    <div class="row g-3 mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('admin.appointments.index') }}"
                           class="btn btn-sm {{ empty($status) ? 'btn-primary' : 'btn-outline-secondary' }}">
                            All <span class="badge bg-white text-dark ms-1">{{ $stats['total'] }}</span>
                        </a>
                        <a href="{{ route('admin.appointments.index', ['status' => 'pending']) }}"
                           class="btn btn-sm {{ $status === 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-dark' }}">
                            Pending <span class="badge bg-warning-subtle text-dark ms-1">{{ $stats['pending'] }}</span>
                        </a>
                        <a href="{{ route('admin.appointments.index', ['status' => 'confirmed']) }}"
                           class="btn btn-sm {{ $status === 'confirmed' ? 'btn-success' : 'btn-outline-success' }}">
                            Confirmed <span class="badge bg-success-subtle text-success ms-1">{{ $stats['confirmed'] }}</span>
                        </a>
                        <a href="{{ route('admin.appointments.index', ['status' => 'completed']) }}"
                           class="btn btn-sm {{ $status === 'completed' ? 'btn-info text-white' : 'btn-outline-info' }}">
                            Completed <span class="badge bg-info-subtle text-info ms-1">{{ $stats['completed'] }}</span>
                        </a>
                    </div>

                    {{-- Search Form --}}
                    <form method="GET" action="{{ route('admin.appointments.index') }}" class="d-flex gap-2 ms-auto">
                        @if(!empty($status))
                            <input type="hidden" name="status" value="{{ $status }}">
                        @endif
                        <div class="input-group input-group-sm" style="max-width: 280px;">
                            <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search ref, patient, doctor...">
                            <button class="btn btn-outline-primary" type="submit"><i class="bi bi-search"></i></button>
                        </div>
                        @if(!empty($search))
                            <a href="{{ route('admin.appointments.index', array_filter(['status' => $status])) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Table Card --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i>All System Appointments</h5>
            <small class="text-muted">Showing {{ $appointments->firstItem() ?? 0 }} - {{ $appointments->lastItem() ?? 0 }} of {{ $appointments->total() }} appointments</small>
        </div>
        <div class="card-body p-0">
            @if($appointments->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted display-4"></i>
                    <h5 class="mt-3 fw-semibold text-secondary">No Appointments Found</h5>
                    <p class="text-muted mb-0">No appointment records match the selected criteria.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light border-bottom">
                            <tr>
                                <th class="ps-4">Reference #</th>
                                <th>Patient</th>
                                <th>Doctor</th>
                                <th>Date & Time</th>
                                <th>Fee & Payment</th>
                                <th>Status</th>
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
                                            <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width:36px;height:36px">
                                                <i class="bi bi-person-fill"></i>
                                            </div>
                                            <div>
                                                <div class="fw-semibold text-dark">{{ $apt->patient?->user?->name ?? 'N/A' }}</div>
                                                <small class="text-muted">{{ $apt->patient?->user?->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">
                                            {{ \Illuminate\Support\Str::startsWith($apt->doctor?->user?->name, 'Dr.') ? $apt->doctor?->user?->name : 'Dr. '.$apt->doctor?->user?->name }}
                                        </div>
                                        <small class="text-muted">{{ $apt->doctor?->department?->name ?? 'General Medical' }}</small>
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
                                        <div class="fw-bold text-dark">${{ number_format($apt->consultation_fee, 2) }}</div>
                                        @if($apt->payment_status === 'paid')
                                            <span class="badge bg-success-subtle text-success border">
                                                <i class="bi bi-check-circle me-1"></i> Paid
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-dark border">
                                                <i class="bi bi-hourglass-split me-1"></i> Unpaid
                                            </span>
                                        @endif
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
                                    <td class="text-end pe-4">
                                        <a href="{{ route('admin.appointments.show', $apt) }}" class="btn btn-outline-primary btn-sm px-3">
                                            <i class="bi bi-eye me-1"></i> View Details
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if($appointments->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $appointments->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>

</div>
@endsection
