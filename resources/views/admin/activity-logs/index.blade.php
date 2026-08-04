@extends('layouts.dashboard-layout')

@section('title', 'System Activity Logs')
@section('page_title', 'Audit Trail & Activity Logs')

@section('content')
<div class="container-fluid px-0">

    {{-- Header & Search Form --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-shield-check me-2 text-primary"></i>System Audit Trail & Activity Logs</h5>
            </div>

            <div class="d-flex gap-2 ms-auto flex-wrap align-items-center">
                {{-- Search Form --}}
                <form method="GET" action="{{ route('admin.activity-logs.index') }}" class="d-flex gap-2">
                    @if(!empty($action))
                        <input type="hidden" name="action" value="{{ $action }}">
                    @endif
                    <div class="input-group input-group-sm" style="max-width: 280px;">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control" placeholder="Search event, user, description...">
                        <button class="btn btn-primary" type="submit">Search</button>
                    </div>
                </form>

                {{-- Clear All Logs Form --}}
                <form method="POST" action="{{ route('admin.activity-logs.clear') }}" onsubmit="return confirm('Are you sure you want to clear ALL activity logs? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm" title="Clear All Activity Logs">
                        <i class="bi bi-trash3 me-1"></i> Clear All Logs
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- Form for Bulk Deleting Selected Logs --}}
    <form method="POST" action="{{ route('admin.activity-logs.destroy-selected') }}" id="bulk-delete-form" onsubmit="return confirm('Are you sure you want to delete the selected log records?');">
        @csrf
        @method('DELETE')

        <div class="card shadow-sm border-0 mb-3 d-none" id="bulk-actions-bar">
            <div class="card-body py-2 px-4 bg-light d-flex align-items-center justify-content-between">
                <span class="fw-semibold text-dark small" id="selected-count-label">0 logs selected</span>
                <button type="submit" class="btn btn-danger btn-sm px-3">
                    <i class="bi bi-trash me-1"></i> Delete Selected Logs
                </button>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @if($logs->isEmpty())
                    <div class="text-center py-5">
                        <i class="bi bi-journal-x text-muted display-4"></i>
                        <h5 class="mt-3 fw-semibold text-secondary">No Activity Logs Found</h5>
                        <p class="text-muted small">System audit events will automatically record as users book appointments, issue prescriptions, and manage settings.</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light border-bottom">
                                <tr>
                                    <th class="ps-3" style="width: 40px;">
                                        <input type="checkbox" id="select-all-checkbox" class="form-check-input">
                                    </th>
                                    <th style="width: 80px;">Log ID</th>
                                    <th>User & Role</th>
                                    <th>Module</th>
                                    <th>Action Event</th>
                                    <th>Detailed Description</th>
                                    <th class="text-end pe-4">Date & Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($logs as $log)
                                    @php
                                        $userRole = 'System';
                                        $badgeColor = 'bg-secondary';
                                        if ($log->user) {
                                            if ($log->user->hasRole('Admin') || $log->user->user_type === 'admin') {
                                                $userRole = 'Admin';
                                                $badgeColor = 'bg-danger-subtle text-danger border-danger';
                                            } elseif ($log->user->hasRole('Doctor') || $log->user->user_type === 'doctor') {
                                                $userRole = 'Doctor';
                                                $badgeColor = 'bg-primary-subtle text-primary border-primary';
                                            } elseif ($log->user->hasRole('Patient') || $log->user->user_type === 'patient') {
                                                $userRole = 'Patient';
                                                $badgeColor = 'bg-info-subtle text-info border-info';
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-3">
                                            <input type="checkbox" name="ids[]" value="{{ $log->id }}" class="form-check-input log-item-checkbox">
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark border font-monospace fw-bold">#{{ $logs->firstItem() + $loop->index }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm rounded-circle bg-light text-dark fw-bold d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 0.85rem;">
                                                    {{ strtoupper(substr($log->user?->name ?? 'S', 0, 1)) }}
                                                </div>
                                                <div>
                                                    <div class="fw-semibold text-dark mb-0">{{ $log->user?->name ?? 'System Process' }}</div>
                                                    <span class="badge {{ $badgeColor }} border extra-small" style="font-size: 0.7rem;">
                                                        {{ $userRole }}
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-dark-subtle text-dark border font-monospace">
                                                <i class="bi bi-folder2 me-1"></i>{{ $log->module ?? 'System' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-primary-subtle text-primary border fw-semibold">
                                                {{ $log->action }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="text-dark small lh-sm" style="max-width: 380px;">
                                                {{ $log->description }}
                                            </div>
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="fw-semibold text-dark small">
                                                <i class="bi bi-clock me-1 text-muted"></i>{{ $log->created_at?->format('d M Y, h:i A') }}
                                            </div>
                                            <small class="text-muted extra-small" style="font-size: 0.72rem;">
                                                {{ $log->created_at?->diffForHumans() }}
                                            </small>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @if($logs->hasPages())
                        <div class="px-4 py-3 border-top">
                            {{ $logs->links() }}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAll = document.getElementById('select-all-checkbox');
        const checkboxes = document.querySelectorAll('.log-item-checkbox');
        const bulkBar = document.getElementById('bulk-actions-bar');
        const countLabel = document.getElementById('selected-count-label');

        function updateBulkState() {
            const checkedCount = document.querySelectorAll('.log-item-checkbox:checked').length;
            if (checkedCount > 0) {
                bulkBar.classList.remove('d-none');
                countLabel.textContent = checkedCount + ' log(s) selected';
            } else {
                bulkBar.classList.add('d-none');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function () {
                checkboxes.forEach(cb => cb.checked = selectAll.checked);
                updateBulkState();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateBulkState);
        });
    });
</script>
@endpush
