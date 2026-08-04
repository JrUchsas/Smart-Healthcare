@php
    $user = auth()->user();
    $isAdmin = $user?->hasRole('Admin') || $user?->user_type === 'admin';
    $isDoctor = $user?->hasRole('Doctor') || $user?->user_type === 'doctor';
    $isPatient = $user?->hasRole('Patient') || $user?->user_type === 'patient';
@endphp

{{-- Admin Sidebar Menu --}}
@if($isAdmin)
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>

    {{-- Departments & Specializations Dropdown --}}
    <li class="nav-item">
        <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.specializations.*') ? 'active' : '' }}" 
           href="#departmentsSubmenu" 
           data-bs-toggle="collapse" 
           role="button"
           aria-expanded="{{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.specializations.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-hospital me-2"></i> Departments</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.departments.*') || request()->routeIs('admin.specializations.*') ? 'show' : '' }}" id="departmentsSubmenu">
            <ul class="nav flex-column ms-3 mt-1">
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}" href="{{ route('admin.departments.index') }}">
                        <i class="bi bi-list-task me-2"></i> All Departments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ request()->routeIs('admin.specializations.*') ? 'active' : '' }}" href="{{ route('admin.specializations.index') }}">
                        <i class="bi bi-patch-check me-2"></i> Specializations
                    </a>
                </li>
            </ul>
        </div>
    </li>

    {{-- Doctors Dropdown --}}
    <li class="nav-item">
        <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('admin.doctors.*') || request()->routeIs('doctor.schedules.*') ? 'active' : '' }}" 
           href="#doctorsSubmenu" 
           data-bs-toggle="collapse" 
           role="button"
           aria-expanded="{{ request()->routeIs('admin.doctors.*') || request()->routeIs('doctor.schedules.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-person-badge me-2"></i> Doctors</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ request()->routeIs('admin.doctors.*') || request()->routeIs('doctor.schedules.*') ? 'show' : '' }}" id="doctorsSubmenu">
            <ul class="nav flex-column ms-3 mt-1">
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ request()->routeIs('admin.doctors.*') ? 'active' : '' }}" href="{{ route('admin.doctors.index') }}">
                        <i class="bi bi-people me-2"></i> All Doctors
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ request()->routeIs('doctor.schedules.*') ? 'active' : '' }}" href="{{ route('doctor.schedules.index') }}">
                        <i class="bi bi-calendar-range me-2"></i> Doctor Schedules
                    </a>
                </li>
            </ul>
        </div>
    </li>

    {{-- Appointment History --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.appointments.*') ? 'active' : '' }}" href="{{ route('admin.appointments.index') }}">
            <i class="bi bi-clock-history me-2"></i> Appointment History
        </a>
    </li>

    {{-- Revenue Reports --}}
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.reports.revenue') ? 'active' : '' }}" href="{{ route('admin.reports.revenue') }}">
            <i class="bi bi-graph-up-arrow me-2"></i> Revenue Reports
        </a>
    </li>
@endif

{{-- Doctor Sidebar Menu --}}
@if($isDoctor)
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.dashboard') ? 'active' : '' }}" href="{{ route('doctor.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.schedules.*') ? 'active' : '' }}" href="{{ route('doctor.schedules.index') }}">
            <i class="bi bi-calendar-range"></i> Schedules & Availability
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.consultations.completed') ? 'active' : '' }}" href="{{ route('doctor.consultations.completed') }}">
            <i class="bi bi-check-all"></i> Completed Consultations
        </a>
    </li>
@endif

{{-- Patient Sidebar Menu --}}
@if($isPatient)
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patient.dashboard') ? 'active' : '' }}" href="{{ route('patient.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patient.doctors.*') || request()->routeIs('patient.appointments.create') ? 'active' : '' }}" href="{{ route('patient.doctors.search') }}">
            <i class="bi bi-search me-1"></i> Find Doctor Appointment
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link d-flex justify-content-between align-items-center {{ request()->routeIs('patient.appointments.*') ? 'active' : '' }}"
           href="#appointmentsSubmenu"
           data-bs-toggle="collapse"
           role="button"
           aria-expanded="{{ request()->routeIs('patient.appointments.*') ? 'true' : 'false' }}">
            <span><i class="bi bi-calendar2-check me-2"></i> My Appointments</span>
            <i class="bi bi-chevron-down small"></i>
        </a>
        <div class="collapse {{ request()->routeIs('patient.appointments.*') ? 'show' : '' }}" id="appointmentsSubmenu">
            <ul class="nav flex-column ms-3 mt-1">
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ request()->routeIs('patient.appointments.index') && request('type', 'booked') === 'booked' ? 'active' : '' }}"
                       href="{{ route('patient.appointments.index', ['type' => 'booked']) }}">
                        <i class="bi bi-hourglass-split me-2"></i> Booked Appointments
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link py-1.5 px-3 {{ request()->routeIs('patient.appointments.index') && request('type') === 'completed' ? 'active' : '' }}"
                       href="{{ route('patient.appointments.index', ['type' => 'completed']) }}">
                        <i class="bi bi-check-all me-2"></i> Completed Appointments
                    </a>
                </li>
            </ul>
        </div>
    </li>
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('patient.prescriptions.*') ? 'active' : '' }}" href="{{ route('patient.prescriptions.index') }}">
            <i class="bi bi-file-medical me-2"></i> My Prescriptions
        </a>
    </li>
@endif

@hasSection('sidebar_menu')
    @yield('sidebar_menu')
@endif
