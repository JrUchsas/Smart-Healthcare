@extends('layouts.dashboard-layout')

@section('title', 'Find Doctor')
@section('page_title', 'Find Doctor & Specialists')

@section('content')
<div class="container-fluid px-0">

    {{-- Search & Filter Bar --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('patient.doctors.search') }}" class="row g-3 align-items-center">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text"
                               name="q"
                               id="search-query"
                               class="form-control border-start-0 ps-0"
                               placeholder="Search doctor by name, qualification, or specialty..."
                               value="{{ request('q') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="department_id" id="department-filter" class="form-select" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ (string)request('department_id') === (string)$dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-filter me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Doctor Cards Grid List --}}
    <div class="row g-4" id="doctors-grid">
        @forelse($doctors as $doctor)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 stat-card">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-lg bg-primary-subtle text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 56px; height: 56px;">
                                    <i class="bi bi-person-badge fs-2"></i>
                                </div>
                                <div class="min-width-0">
                                    <h5 class="card-title fw-bold mb-1 text-dark text-truncate">
                                        {{ \Illuminate\Support\Str::startsWith($doctor->user?->name, 'Dr.') ? $doctor->user?->name : 'Dr. '.$doctor->user?->name }}
                                    </h5>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $doctor->department?->name ?? 'General Practice' }}
                                    </span>
                                </div>
                            </div>

                            <div class="bg-light p-3 rounded mb-3">
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-patch-check-fill text-info me-1"></i>
                                    <strong>Specialization:</strong> {{ $doctor->specialization?->name ?? 'Medical Specialist' }}
                                </p>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-mortarboard me-1 text-secondary"></i>
                                    <strong>Qualification:</strong> {{ $doctor->qualification ?? 'MBBS, MD' }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-briefcase me-1 text-secondary"></i>
                                    <strong>Experience:</strong> {{ $doctor->experience_years }} Years Practice
                                </p>
                            </div>
                        </div>

                        <div>
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <small class="text-muted d-block">Consultation Fee</small>
                                    <span class="fw-bold text-success fs-5">৳{{ number_format($doctor->consultation_fee, 2) }}</span>
                                </div>
                                <span class="badge bg-success-subtle text-success border">
                                    <i class="bi bi-circle-fill small me-1"></i> Available
                                </span>
                            </div>

                            <div class="d-flex gap-2">
                                <a href="{{ route('patient.appointments.create', $doctor) }}" class="btn btn-primary btn-sm flex-fill">
                                    <i class="bi bi-calendar-plus me-1"></i> Book Slot
                                </a>
                                <a href="{{ route('chat.start', ['doctor_id' => $doctor->id]) }}" class="btn btn-outline-secondary btn-sm" title="Chat with Doctor">
                                    <i class="bi bi-chat-dots"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded shadow-sm">
                    <i class="bi bi-search text-muted display-4"></i>
                    <h5 class="mt-3 text-secondary fw-bold">No Doctors Found</h5>
                    <p class="text-muted">Try adjusting your search terms or clearing department filters.</p>
                </div>
            </div>
        @endforelse
    </div>

    @if($doctors->hasPages())
        <div class="mt-4" id="pagination-container">
            {{ $doctors->withQueryString()->links() }}
        </div>
    @endif

</div>
@endsection
