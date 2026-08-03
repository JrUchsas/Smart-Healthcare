@extends('layouts.dashboard-layout')

@section('title', 'Edit Department')
@section('page_title', 'Edit Medical Department')

@section('sidebar_menu')
    <li class="nav-item">
        <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
    </li>
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
@endsection

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Department: {{ $department->name }}</h5>
            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.departments.update', $department) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Department Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $department->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $department->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $department->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Active Department</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.departments.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Update Department</button>
                </div>
            </form>
        </div>
    </div>
@endsection
