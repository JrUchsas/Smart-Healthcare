@extends('layouts.dashboard-layout')

@section('title', 'Doctor Schedules')
@section('page_title', 'My Availability & Appointment Slots')

@section('content')
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-event text-primary me-2"></i>My Active Schedule Blocks</h5>
            @if((isset($isAdmin) && $isAdmin) || (isset($isDoctor) && $isDoctor))
                <a href="{{ route('doctor.schedules.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Add Schedule Block
                </a>
            @endif
        </div>
        <div class="card-body">
            @if($schedules->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-calendar-x text-muted display-4"></i>
                    <h5 class="mt-3 fw-semibold text-secondary">No Schedule Blocks Available</h5>
                    <p class="text-muted">No doctor schedules have been set up yet.</p>
                    @if((isset($isAdmin) && $isAdmin) || (isset($isDoctor) && $isDoctor))
                        <a href="{{ route('doctor.schedules.create') }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-plus-lg me-1"></i> Create First Schedule Block
                        </a>
                    @endif
                </div>
            @else
                <div class="accordion" id="schedulesAccordion">
                    @foreach($schedules as $schedule)
                        <div class="accordion-item border rounded mb-3 shadow-sm">
                            <h2 class="accordion-header" id="heading-{{ $schedule->id }}">
                                <button class="accordion-button collapsed py-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $schedule->id }}" aria-expanded="false" aria-controls="collapse-{{ $schedule->id }}">
                                    <div class="d-flex flex-wrap align-items-center justify-content-between w-100 me-3">
                                        <div class="d-flex align-items-center me-3">
                                            <i class="bi bi-calendar3 text-primary fs-4 me-3"></i>
                                            <div>
                                                <h6 class="mb-0 fw-bold text-dark">
                                                    @if(isset($schedule->doctor->user))
                                                        <span class="badge bg-primary me-2"><i class="bi bi-person-badge me-1"></i>{{ \Illuminate\Support\Str::startsWith($schedule->doctor->user->name, 'Dr.') ? $schedule->doctor->user->name : 'Dr. '.$schedule->doctor->user->name }}</span>
                                                    @endif
                                                    {{ $schedule->schedule_date->format('l, F j, Y') }}
                                                </h6>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    {{ \Carbon\Carbon::parse($schedule->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($schedule->end_time)->format('h:i A') }}
                                                    ({{ $schedule->slot_duration_minutes }} mins / slot)
                                                </small>
                                            </div>
                                        </div>
                                        <div class="d-flex align-items-center gap-2 mt-2 mt-sm-0">
                                            <span class="badge bg-info text-dark">
                                                <i class="bi bi-grid me-1"></i> {{ $schedule->slots->count() }} Total Slots
                                            </span>
                                            <span class="badge bg-success">
                                                <i class="bi bi-check-circle me-1"></i> {{ $schedule->slots->where('is_booked', true)->count() }} Booked
                                            </span>
                                        </div>
                                    </div>
                                </button>
                            </h2>
                            <div id="collapse-{{ $schedule->id }}" class="accordion-collapse collapse" aria-labelledby="heading-{{ $schedule->id }}" data-bs-parent="#schedulesAccordion">
                                <div class="accordion-body bg-light border-top">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h6 class="fw-bold mb-0 text-secondary"><i class="bi bi-clock-history me-1"></i> Available Slots</h6>
                                        @if((isset($isAdmin) && $isAdmin) || (isset($isDoctor) && $isDoctor))
                                            <button type="button" 
                                                    data-url="{{ route('doctor.schedules.destroy', $schedule) }}" 
                                                    class="btn btn-outline-danger btn-sm delete-schedule-btn">
                                                <i class="bi bi-trash me-1"></i> Delete Schedule Block
                                            </button>
                                        @endif
                                    </div>

                                    <div class="d-flex flex-wrap gap-2">
                                        @foreach($schedule->slots as $slot)
                                            <div class="p-2 border rounded bg-white shadow-sm d-flex align-items-center gap-2">
                                                <i class="bi bi-clock text-primary"></i>
                                                <span class="fw-semibold small">
                                                    {{ \Carbon\Carbon::parse($slot->start_time)->format('h:i A') }} - {{ \Carbon\Carbon::parse($slot->end_time)->format('h:i A') }}
                                                </span>
                                                @if($slot->is_booked)
                                                    <span class="badge bg-primary">Booked</span>
                                                @else
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle">Available</span>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-4">
                    {{ $schedules->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            // Setup CSRF token for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            // Handle SweetAlert2 Delete Confirmation for Doctor Schedule Block
            $(document).on('click', '.delete-schedule-btn', function() {
                var deleteUrl = $(this).data('url');

                Swal.fire({
                    title: 'Delete Schedule Block?',
                    text: 'This will delete this date block and all unbooked slots generated for it!',
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
                                    response.message || 'Schedule block deleted.',
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr) {
                                Swal.fire(
                                    'Error!',
                                    xhr.responseJSON?.message || 'Failed to delete schedule block.',
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
