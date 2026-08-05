@extends('layouts.dashboard-layout')

@section('title', 'Assign Department & Edit Doctor')
@section('page_title', 'Edit Doctor Profile & Department Assignment')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square text-primary me-2"></i>Doctor Profile: {{ $doctor->user?->name }}</h5>
                    <a href="{{ route('admin.doctors.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-arrow-left me-1"></i> Back to Doctors
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.doctors.update', $doctor) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="department_id" class="form-label fw-semibold">Assign Department <span class="text-danger">*</span></label>
                                <select name="department_id" id="department_id" class="form-select @error('department_id') is-invalid @enderror" required>
                                    <option value="">-- Select Medical Department --</option>
                                    @foreach($departments as $dept)
                                        <option value="{{ $dept->id }}" {{ old('department_id', $doctor->department_id) == $dept->id ? 'selected' : '' }}>
                                            {{ $dept->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('department_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="specialization_id" class="form-label fw-semibold">Assign Specialization</label>
                                <select name="specialization_id" id="specialization_id" class="form-select @error('specialization_id') is-invalid @enderror">
                                    <option value="">-- Select Specialization --</option>
                                    @foreach($specializations as $spec)
                                        <option value="{{ $spec->id }}" {{ old('specialization_id', $doctor->specialization_id) == $spec->id ? 'selected' : '' }}>
                                            {{ $spec->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('specialization_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="qualification" class="form-label fw-semibold">Medical Qualification <span class="text-danger">*</span></label>
                                <input type="text" name="qualification" id="qualification" class="form-control @error('qualification') is-invalid @enderror" value="{{ old('qualification', $doctor->qualification) }}" placeholder="e.g. MBBS, MD, FACC" required>
                                @error('qualification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="experience_years" class="form-label fw-semibold">Years of Experience <span class="text-danger">*</span></label>
                                <input type="number" name="experience_years" id="experience_years" class="form-control @error('experience_years') is-invalid @enderror" value="{{ old('experience_years', $doctor->experience_years) }}" min="0" max="60" required>
                                @error('experience_years')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="consultation_fee" class="form-label fw-semibold">Consultation Fee (৳) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">৳</span>
                                    <input type="number" step="0.01" name="consultation_fee" id="consultation_fee" class="form-control @error('consultation_fee') is-invalid @enderror" value="{{ old('consultation_fee', $doctor->consultation_fee) }}" required>
                                </div>
                                @error('consultation_fee')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label fw-semibold">Phone Number</label>
                                <input type="text" name="phone" id="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $doctor->phone) }}" placeholder="+880 1712-345678">
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="bio" class="form-label fw-semibold">Professional Biography</label>
                            <textarea name="bio" id="bio" rows="3" class="form-control @error('bio') is-invalid @enderror" placeholder="Brief summary of doctor's clinical background...">{{ old('bio', $doctor->bio) }}</textarea>
                            @error('bio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" name="is_available" id="is_available" value="1" {{ old('is_available', $doctor->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label fw-semibold" for="is_available">Doctor Active & Available for Consultations</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('admin.doctors.index') }}" class="btn btn-light">Cancel</a>
                            <button type="submit" class="btn btn-primary px-4">
                                <i class="bi bi-check-circle me-1"></i> Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Dynamic Cascading Dropdown: Load specializations when Department changes
            $('#department_id').on('change', function() {
                var departmentId = $(this).val();
                var $specSelect = $('#specialization_id');

                $specSelect.empty().append('<option value="">-- Loading Specializations... --</option>');

                if (departmentId) {
                    $.ajax({
                        url: "/admin/departments/" + departmentId + "/specializations",
                        type: 'GET',
                        success: function(response) {
                            $specSelect.empty().append('<option value="">-- Select Specialization --</option>');
                            if (response.success && response.specializations.length > 0) {
                                $.each(response.specializations, function(index, spec) {
                                    $specSelect.append('<option value="' + spec.id + '">' + spec.name + '</option>');
                                });
                            } else {
                                $specSelect.append('<option value="">-- No Specializations Found --</option>');
                            }
                        },
                        error: function() {
                            $specSelect.empty().append('<option value="">-- Error Loading Specializations --</option>');
                        }
                    });
                } else {
                    $specSelect.empty().append('<option value="">-- Select Specialization --</option>');
                }
            });
        });
    </script>
@endpush
