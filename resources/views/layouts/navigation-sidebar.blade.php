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

    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('doctor.schedules.*') ? 'active' : '' }}" href="{{ route('doctor.schedules.index') }}">
            <i class="bi bi-calendar-range"></i> Doctor Schedules
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-person-badge"></i> Doctor Approvals
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-people"></i> Patient Management
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-calendar-check"></i> Appointments
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-graph-up"></i> Revenue Reports
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
        <a class="nav-link" href="#">
            <i class="bi bi-journal-medical"></i> Patient History
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-wallet2"></i> Earnings Summary
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
        <a class="nav-link {{ request()->routeIs('doctor.schedules.*') ? 'active' : '' }}" href="{{ route('doctor.schedules.index') }}">
            <i class="bi bi-calendar-event"></i> Doctor Schedules & Booking
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-clock-history"></i> Appointment History
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-file-earmark-medical"></i> Prescriptions
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link" href="#">
            <i class="bi bi-receipt"></i> Invoices & Payments
        </a>
    </li>
@endif

@hasSection('sidebar_menu')
    @yield('sidebar_menu')
@endif
