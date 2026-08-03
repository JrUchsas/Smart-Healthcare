@extends('layouts.dashboard-layout')

@section('title', 'Find a Doctor')
@section('page_title', 'Doctor Directory & Search')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body p-4">
            <form id="doctor-search-form" class="row g-3">
                <div class="col-md-7">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" 
                               name="q" 
                               id="search-query" 
                               class="form-control border-start-0 ps-0" 
                               placeholder="Search doctor by name, qualification, or medical specialty..."
                               value="{{ request('q') }}">
                    </div>
                </div>

                <div class="col-md-3">
                    <select name="department_id" id="department-filter" class="form-select">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2 d-grid">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filter</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Doctor Cards Grid -->
    <div class="row g-4" id="doctors-grid">
        @forelse($doctors as $doctor)
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm border-0 stat-card">
                    <div class="card-body p-4 d-flex flex-column justify-content-between">
                        <div>
                            <div class="d-flex align-items-center mb-3">
                                <div class="avatar-lg bg-primary-subtle text-primary rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" style="width: 54px; height: 54px;">
                                    <i class="bi bi-person-badge fs-3"></i>
                                </div>
                                <div>
                                    <h5 class="card-title fw-bold mb-1 text-dark">{{ \Illuminate\Support\Str::startsWith($doctor->user?->name, 'Dr.') ? $doctor->user?->name : 'Dr. '.$doctor->user?->name }}</h5>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle me-1">
                                        {{ $doctor->department?->name ?? 'General Medical' }}
                                    </span>
                                </div>
                            </div>

                            <p class="text-muted small mb-2">
                                <i class="bi bi-patch-check-fill text-info me-1"></i>
                                <strong>Specialty:</strong> {{ $doctor->specialization?->name ?? 'Specialist' }}
                            </p>

                            <p class="text-muted small mb-2">
                                <i class="bi bi-mortarboard me-1"></i>
                                <strong>Qualification:</strong> {{ $doctor->qualification ?? 'MBBS, MD' }}
                            </p>

                            <p class="text-muted small mb-3">
                                <i class="bi bi-briefcase me-1"></i>
                                <strong>Experience:</strong> {{ $doctor->experience_years }} Years Practice
                            </p>
                        </div>

                        <div class="pt-3 border-top d-flex align-items-center justify-content-between">
                            <div>
                                <small class="text-muted d-block">Consultation Fee</small>
                                <span class="fw-bold text-success fs-5">${{ number_format($doctor->consultation_fee, 2) }}</span>
                            </div>
                            <a href="{{ route('patient.appointments.create', $doctor) }}" class="btn btn-outline-primary btn-sm px-3">
                                <i class="bi bi-calendar-plus me-1"></i> Book Slot
                            </a>
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

    <div class="mt-4" id="pagination-container">
        {{ $doctors->withQueryString()->links() }}
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Live AJAX filter on search input or department change
            $('#doctor-search-form').on('submit', function(e) {
                e.preventDefault();
                fetchDoctors();
            });

            $('#department-filter').on('change', function() {
                fetchDoctors();
            });

            function fetchDoctors() {
                var query = $('#search-query').val();
                var departmentId = $('#department-filter').val();

                $.ajax({
                    url: "{{ route('patient.doctors.search') }}",
                    type: "GET",
                    data: {
                        q: query,
                        department_id: departmentId
                    },
                    beforeSend: function() {
                        $('#doctors-grid').css('opacity', '0.5');
                    },
                    success: function(response) {
                        $('#doctors-grid').css('opacity', '1');
                        window.location.href = "{{ route('patient.doctors.search') }}?q=" + encodeURIComponent(query) + "&department_id=" + encodeURIComponent(departmentId);
                    },
                    error: function() {
                        $('#doctors-grid').css('opacity', '1');
                    }
                });
            }
        });
    </script>
@endpush
