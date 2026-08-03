@extends('layouts.dashboard-layout')

@section('title', 'Doctor Profiles & Assignments')
@section('page_title', 'Doctor Directory & Department Assignments')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>Hospital Doctors Directory</h5>
        </div>
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="doctors-table">
                    <thead class="table-light">
                        <tr>
                            <th>Doctor Details</th>
                            <th>Department</th>
                            <th>Specialization</th>
                            <th>Fee</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#doctors-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.doctors.index') }}",
                columns: [
                    { data: 'doctor_name', name: 'user.name' },
                    { data: 'department_name', name: 'department.name' },
                    { data: 'specialization_name', name: 'specialization.name' },
                    { data: 'fee', name: 'consultation_fee' },
                    { data: 'status', name: 'is_available' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false, className: 'text-end' }
                ],
                order: [[0, 'asc']],
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search doctors..."
                }
            });
        });
    </script>
@endpush
