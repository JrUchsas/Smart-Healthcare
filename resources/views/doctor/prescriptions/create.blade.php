@extends('layouts.dashboard-layout')

@section('title', 'Prescription Builder')

@section('content')
<div class="container-fluid px-4 py-4">

    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h4 fw-bold mb-1"><i class="bi bi-file-medical me-2 text-primary"></i>Prescription Builder</h1>
            <small class="text-muted">
                Patient: <strong>{{ $appointment->patient?->user?->name ?? 'N/A' }}</strong>
                &mdash; Appointment: <strong>{{ $appointment->appointment_number }}</strong>
                &mdash; Date: <strong>{{ $appointment->appointment_date?->format('M j, Y') }}</strong>
            </small>
        </div>
        <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Dashboard
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('doctor.prescriptions.store') }}" method="POST" id="prescriptionForm">
        @csrf
        <input type="hidden" name="appointment_id" value="{{ $appointment->id }}">

        <div class="row g-4">

            {{-- Left Column: Clinical Details --}}
            <div class="col-lg-5">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>Clinical Details</h6>
                    </div>
                    <div class="card-body">

                        <div class="mb-3">
                            <label for="symptoms" class="form-label fw-semibold">Symptoms</label>
                            <textarea id="symptoms" name="symptoms" rows="3" class="form-control @error('symptoms') is-invalid @enderror"
                                placeholder="e.g. Fever, headache, body ache for 3 days">{{ old('symptoms') }}</textarea>
                            @error('symptoms') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="diagnosis" class="form-label fw-semibold">Diagnosis</label>
                            <textarea id="diagnosis" name="diagnosis" rows="3" class="form-control @error('diagnosis') is-invalid @enderror"
                                placeholder="e.g. Viral fever with upper respiratory tract infection">{{ old('diagnosis') }}</textarea>
                            @error('diagnosis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="advice" class="form-label fw-semibold">Advice / Instructions</label>
                            <textarea id="advice" name="advice" rows="3" class="form-control @error('advice') is-invalid @enderror"
                                placeholder="e.g. Rest, drink plenty of fluids, avoid cold foods">{{ old('advice') }}</textarea>
                            @error('advice') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="follow_up_date" class="form-label fw-semibold">Follow-Up Date</label>
                            <input type="date" id="follow_up_date" name="follow_up_date"
                                class="form-control @error('follow_up_date') is-invalid @enderror"
                                value="{{ old('follow_up_date') }}" min="{{ date('Y-m-d', strtotime('+1 day')) }}">
                            @error('follow_up_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label for="doctor_signature" class="form-label fw-semibold">Doctor Signature (Name)</label>
                            <input type="text" id="doctor_signature" name="doctor_signature"
                                class="form-control @error('doctor_signature') is-invalid @enderror"
                                value="{{ old('doctor_signature', auth()->user()->name) }}"
                                placeholder="Dr. Full Name">
                            @error('doctor_signature') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                    </div>
                </div>
            </div>

            {{-- Right Column: Medicines Table --}}
            <div class="col-lg-7">
                <div class="card shadow-sm border-0">
                    <div class="card-header d-flex align-items-center justify-content-between bg-success text-white">
                        <h6 class="mb-0"><i class="bi bi-capsule me-2"></i>Medicines</h6>
                        <button type="button" id="addMedicineRow" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Add Medicine
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" id="medicinesTable">
                                <thead class="table-light">
                                    <tr>
                                        <th style="min-width:180px">Medicine Name <span class="text-danger">*</span></th>
                                        <th style="min-width:140px">Dosage <span class="text-danger">*</span></th>
                                        <th style="min-width:130px">Duration <span class="text-danger">*</span></th>
                                        <th style="min-width:150px">Instructions</th>
                                        <th style="width:44px"></th>
                                    </tr>
                                </thead>
                                <tbody id="medicineRows">
                                    {{-- First row is always present --}}
                                    <tr class="medicine-row">
                                        <td><input type="text" name="medicines[0][medicine_name]" class="form-control form-control-sm" placeholder="e.g. Paracetamol 500mg" required></td>
                                        <td><input type="text" name="medicines[0][dosage]" class="form-control form-control-sm" placeholder="e.g. 1 tablet" required></td>
                                        <td><input type="text" name="medicines[0][duration]" class="form-control form-control-sm" placeholder="e.g. 5 days" required></td>
                                        <td><input type="text" name="medicines[0][instructions]" class="form-control form-control-sm" placeholder="e.g. After meals"></td>
                                        <td class="text-center align-middle">
                                            <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove" style="display:none">
                                                <i class="bi bi-trash3"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Submit --}}
                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('doctor.dashboard') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-send me-1"></i> Issue Prescription
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Track row index so names stay unique and PHP parses them as a proper array
    let rowIndex = 1;

    document.getElementById('addMedicineRow').addEventListener('click', function () {
        const tbody = document.getElementById('medicineRows');

        const row = document.createElement('tr');
        row.className = 'medicine-row';
        row.innerHTML = `
            <td><input type="text" name="medicines[${rowIndex}][medicine_name]" class="form-control form-control-sm" placeholder="e.g. Amoxicillin 250mg" required></td>
            <td><input type="text" name="medicines[${rowIndex}][dosage]" class="form-control form-control-sm" placeholder="e.g. 2 tablets" required></td>
            <td><input type="text" name="medicines[${rowIndex}][duration]" class="form-control form-control-sm" placeholder="e.g. 7 days" required></td>
            <td><input type="text" name="medicines[${rowIndex}][instructions]" class="form-control form-control-sm" placeholder="e.g. Before meals"></td>
            <td class="text-center align-middle">
                <button type="button" class="btn btn-outline-danger btn-sm remove-row" title="Remove">
                    <i class="bi bi-trash3"></i>
                </button>
            </td>`;

        tbody.appendChild(row);
        rowIndex++;

        // Show all remove buttons once there's more than one row
        syncRemoveButtons();
    });

    // Event delegation — handles remove clicks on dynamically added rows too
    document.getElementById('medicineRows').addEventListener('click', function (e) {
        const btn = e.target.closest('.remove-row');
        if (!btn) return;

        btn.closest('tr').remove();
        syncRemoveButtons();
    });

    function syncRemoveButtons() {
        const rows = document.querySelectorAll('.medicine-row');
        rows.forEach(function (row) {
            const btn = row.querySelector('.remove-row');
            // Only show the remove button when there are 2+ rows
            btn.style.display = rows.length > 1 ? 'inline-flex' : 'none';
        });
    }
</script>
@endpush
