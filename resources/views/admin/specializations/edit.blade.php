@extends('layouts.dashboard-layout')

@section('title', 'Edit Specialization')
@section('page_title', 'Edit Medical Specialization')

@section('content')
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Edit Specialization: {{ $specialization->name }}</h5>
            <a href="{{ route('admin.specializations.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Back to List
            </a>
        </div>
        <div class="card-body p-4">
            <form action="{{ route('admin.specializations.update', $specialization) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="department_id" class="form-label fw-semibold">Select Parent Department <span class="text-danger">*</span></label>
                    <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                        <option value="">-- Choose Department --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $specialization->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Specialization Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $specialization->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label fw-semibold">Description</label>
                    <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $specialization->description) }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $specialization->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold" for="is_active">Active Specialization</label>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('admin.specializations.index') }}" class="btn btn-light">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-save me-1"></i> Update Specialization</button>
                </div>
            </form>
        </div>
    </div>
@endsection
