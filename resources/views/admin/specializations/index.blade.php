@extends('layouts.dashboard-layout')

@section('title', 'Specializations')
@section('page_title', 'Specialization Management')

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
            <h5 class="mb-0 fw-bold"><i class="bi bi-patch-check text-primary me-2"></i>Medical Specializations List</h5>
            <a href="{{ route('admin.specializations.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Add New Specialization
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle w-100" id="specializations-table">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Department</th>
                            <th>Specialization Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX requests
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Initialize Yajra DataTables with server-side processing
            var table = $('#specializations-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.specializations.index') }}",
                columns: [
                    { data: 'id', name: 'id' },
                    { data: 'department_name', name: 'department.name', className: 'fw-semibold text-primary' },
                    { data: 'name', name: 'name', className: 'fw-semibold' },
                    { data: 'slug', name: 'slug', className: 'text-muted' },
                    { data: 'description', name: 'description', defaultContent: '<span class="text-muted">N/A</span>' },
                    { data: 'status', name: 'status', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ]
            });

            // Handle SweetAlert2 Delete Confirmation
            $(document).on('click', '.delete-btn', function() {
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this specialization deletion!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: deleteUrl,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire(
                                    'Deleted!',
                                    response.message || 'Specialization has been deleted.',
                                    'success'
                                );
                                table.ajax.reload(null, false);
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message || 'Failed to delete specialization.',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>
@endpush
