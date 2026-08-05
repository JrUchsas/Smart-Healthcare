@extends('layouts.dashboard-layout')

@section('title', 'My Notifications')
@section('page_title', 'System Notifications & Alerts')

@section('content')
<div class="container-fluid px-0">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body py-3 px-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-bell-fill text-primary me-2"></i>Notifications & System Alerts</h5>
                <small class="text-muted">Important updates regarding your appointments, refunds, and prescriptions</small>
            </div>

            @if($notifications->isNotEmpty())
                <form method="POST" action="{{ route('notifications.clear') }}" onsubmit="return confirm('Clear all notifications?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-trash3 me-1"></i> Clear All Notifications
                    </button>
                </form>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if($notifications->isEmpty())
                <div class="text-center py-5">
                    <i class="bi bi-bell-slash text-muted display-4"></i>
                    <h5 class="mt-3 fw-semibold text-secondary">No Notifications Found</h5>
                    <p class="text-muted small">You will receive alerts here when appointments are booked, updated, or refunded.</p>
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach($notifications as $item)
                        @php
                            $icon = 'bi-info-circle text-info';
                            $bg = 'bg-light';
                            if ($item->type === 'refund') {
                                $icon = 'bi-currency-exchange text-success';
                            } elseif ($item->type === 'chat') {
                                $icon = 'bi-chat-dots-fill text-primary';
                            } elseif ($item->type === 'appointment_expired' || $item->type === 'warning') {
                                $icon = 'bi-exclamation-triangle text-warning';
                            }
                        @endphp
                        <div class="list-group-item p-4 {{ $item->is_read ? 'bg-white' : 'bg-light border-start border-primary border-4' }}">
                            <div class="d-flex align-items-start justify-content-between gap-3">
                                <div class="d-flex align-items-start gap-3">
                                    <div class="p-2 rounded-circle bg-white shadow-sm border">
                                        <i class="bi {{ $icon }} fs-4"></i>
                                    </div>
                                    <div>
                                        <h6 class="fw-bold mb-1 text-dark">{{ $item->title }}</h6>
                                        <p class="mb-2 text-secondary small">{{ $item->message }}</p>
                                        <small class="text-muted extra-small" style="font-size: 0.75rem;">
                                            <i class="bi bi-clock me-1"></i>{{ $item->created_at?->format('d M Y, h:i A') }} ({{ $item->created_at?->diffForHumans() }})
                                        </small>
                                    </div>
                                </div>
                                @if($item->type === 'chat')
                                    <a href="{{ route('chat.index') }}" class="btn btn-sm btn-outline-primary text-nowrap mt-1">
                                        <i class="bi bi-chat-dots me-1"></i> Open Chat
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($notifications->hasPages())
                    <div class="px-4 py-3 border-top">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
