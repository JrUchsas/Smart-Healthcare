@extends('layouts.dashboard-layout')

@section('title', 'Real-Time Chat & Communication')
@section('page_title', 'Messages & Consultations Chat')

@section('content')
<div class="container-fluid px-0">
    <div class="card shadow-sm border-0 overflow-hidden" style="min-height: 680px; height: calc(100vh - 170px);">
        <div class="row g-0 h-100">

            {{-- ─────────────────────── LEFT PANEL: ROOMS LIST ─────────────────────── --}}
            <div class="col-md-4 col-lg-3 border-end bg-light d-flex flex-column h-100">
                <div class="p-3 bg-white border-bottom">
                    <h6 class="fw-bold mb-2 text-dark"><i class="bi bi-chat-dots text-primary me-2"></i>Active Conversations</h6>
                    <div class="input-group input-group-sm">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="chat-search-input" class="form-control bg-light border-start-0" placeholder="Search contact or doctor...">
                    </div>
                </div>

                <div class="overflow-auto flex-grow-1" id="chat-rooms-container">
                    @if($rooms->isEmpty())
                        <div class="text-center py-5 px-3">
                            <i class="bi bi-chat-square-dots text-muted fs-1 d-block mb-2"></i>
                            <h6 class="fw-semibold text-secondary">No Conversations Yet</h6>
                            <p class="text-muted small">Start a conversation from your appointments or doctor directory.</p>
                            @if(auth()->user()->hasRole('Patient'))
                                <a href="{{ route('patient.doctors.search') }}" class="btn btn-primary btn-sm">Find Doctor</a>
                            @endif
                        </div>
                    @else
                        <div class="list-group list-group-flush" id="chat-rooms-list">
                            @foreach($rooms as $room)
                                @php
                                    $isPatient = auth()->user()->hasRole('Patient') || auth()->user()->user_type === 'patient';
                                    $partnerUser = $isPatient ? $room->doctor?->user : $room->patient?->user;
                                    $partnerSubtext = $isPatient ? ($room->doctor?->department?->name ?? 'Doctor') : 'Patient';
                                    $isActive = $activeRoom && $activeRoom->id === $room->id;
                                @endphp
                                <a href="{{ route('chat.index', ['room' => $room->id]) }}"
                                   class="list-group-item list-group-item-action py-3 px-3 border-bottom chat-room-item {{ $isActive ? 'active bg-primary-subtle text-dark border-start border-primary border-4' : 'bg-white' }}"
                                   data-name="{{ strtolower($partnerUser?->name ?? '') }}">
                                    <div class="d-flex align-items-center">
                                        <div class="position-relative me-3 flex-shrink-0">
                                            <div class="avatar-md rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center"
                                                 style="width: 44px; height: 44px; font-size: 1.1rem;">
                                                {{ strtoupper(substr($partnerUser?->name ?? 'C', 0, 1)) }}
                                            </div>
                                            <span class="position-absolute bottom-0 end-0 p-1 bg-success border border-white rounded-circle"></span>
                                        </div>
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="d-flex justify-content-between align-items-baseline mb-1">
                                                <h6 class="mb-0 fw-semibold text-truncate me-2" style="max-width: 140px;">
                                                    {{ $partnerUser?->name ?? 'Contact' }}
                                                </h6>
                                                @if($room->latestMessage)
                                                    <small class="text-muted extra-small" style="font-size: 0.72rem;">
                                                        {{ $room->latestMessage->created_at?->format('h:i A') }}
                                                    </small>
                                                @endif
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center">
                                                <small class="text-muted text-truncate d-block me-1" style="max-width: 140px; font-size: 0.8rem;">
                                                    @if($room->latestMessage)
                                                        {{ $room->latestMessage->message ?: 'Sent an attachment' }}
                                                    @else
                                                        {{ $partnerSubtext }}
                                                    @endif
                                                </small>
                                                @if($room->unread_count > 0)
                                                    <span class="badge bg-danger rounded-pill px-2 py-1 small">{{ $room->unread_count }}</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            {{-- ─────────────────────── RIGHT PANEL: MAIN CHAT WINDOW ─────────────────────── --}}
            <div class="col-md-8 col-lg-9 d-flex flex-column h-100 bg-white">
                @if($activeRoom)
                    @php
                        $isPatient = auth()->user()->hasRole('Patient') || auth()->user()->user_type === 'patient';
                        $partnerUser = $isPatient ? $activeRoom->doctor?->user : $activeRoom->patient?->user;
                        $partnerSubtitle = $isPatient ? ($activeRoom->doctor?->department?->name ?? 'Specialist Doctor') : 'Patient Consultation';
                    @endphp

                    {{-- Chat Top Header --}}
                    <div class="p-3 border-bottom bg-white d-flex align-items-center justify-content-between flex-shrink-0">
                        <div class="d-flex align-items-center">
                            <div class="avatar-md rounded-circle bg-primary-subtle text-primary fw-bold d-flex align-items-center justify-content-center me-3"
                                 style="width: 44px; height: 44px; font-size: 1.1rem;">
                                {{ strtoupper(substr($partnerUser?->name ?? 'C', 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $partnerUser?->name ?? 'User' }}</h6>
                                <small class="text-muted" id="partner-status-text">
                                    <i class="bi bi-circle-fill text-success me-1 small"></i> {{ $partnerSubtitle }} &bull; Active Now
                                </small>
                                <span class="badge bg-warning-subtle text-dark ms-2 d-none" id="typing-indicator">
                                    <i class="bi bi-three-dots me-1"></i> Typing...
                                </span>
                            </div>
                        </div>

                        @if($activeRoom->appointment)
                            <div class="text-end">
                                <small class="text-muted d-block">Appointment Ref</small>
                                <span class="badge bg-dark-subtle text-dark border font-monospace fw-semibold">
                                    #{{ $activeRoom->appointment->appointment_number }}
                                </span>
                            </div>
                        @endif
                    </div>

                    {{-- Scrollable Messages Thread Body --}}
                    <div class="p-4 flex-grow-1 overflow-auto bg-light bg-opacity-50" id="messages-container" style="scroll-behavior: smooth;">
                        <div class="text-center my-3 text-muted small">
                            <span class="bg-white px-3 py-1 rounded-pill border shadow-sm">
                                <i class="bi bi-lock me-1"></i> End-to-end encrypted medical consultation chat
                            </span>
                        </div>

                        <div id="messages-list">
                            {{-- Messages dynamically populated via JavaScript polling loop --}}
                        </div>
                    </div>

                    {{-- Bottom Input Area with Attachments --}}
                    <div class="p-3 border-top bg-white flex-shrink-0">
                        {{-- Attachment Preview Pill --}}
                        <div id="attachment-preview-container" class="mb-2 d-none">
                            <span class="badge bg-primary-subtle text-primary border p-2 d-inline-flex align-items-center gap-2">
                                <i class="bi bi-paperclip fs-6"></i>
                                <span id="attachment-preview-name" class="fw-semibold">filename.jpg</span>
                                <button type="button" id="remove-attachment-btn" class="btn-close btn-close-xs ms-1" aria-label="Remove"></button>
                            </span>
                        </div>

                        <form id="chat-send-form" enctype="multipart/form-data" class="d-flex align-items-center gap-2">
                            @csrf
                            <input type="file" id="chat-attachment-input" name="attachment" class="d-none" accept="image/*,.pdf,.doc,.docx,.txt">

                            <button type="button" id="trigger-attachment-btn" class="btn btn-outline-secondary btn-icon rounded-circle flex-shrink-0" title="Attach Image or Document" style="width:42px;height:42px">
                                <i class="bi bi-paperclip fs-5"></i>
                            </button>

                            <input type="text" id="chat-message-input" name="message" class="form-control py-2 px-3 rounded-pill" placeholder="Type your message here..." autocomplete="off">

                            <button type="submit" id="chat-send-btn" class="btn btn-primary rounded-circle flex-shrink-0 d-flex align-items-center justify-content-center" style="width:42px;height:42px">
                                <i class="bi bi-send-fill fs-6"></i>
                            </button>
                        </form>
                    </div>

                @else
                    <div class="d-flex align-items-center justify-content-center h-100 text-center p-5">
                        <div>
                            <div class="rounded-circle bg-primary-subtle text-primary p-4 d-inline-flex mb-3">
                                <i class="bi bi-chat-quote fs-1"></i>
                            </div>
                            <h5 class="fw-bold text-dark">Select a Conversation</h5>
                            <p class="text-muted">Choose a contact from the left list to start messaging in real-time.</p>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        @if($activeRoom)
            const activeRoomId = {{ $activeRoom->id }};
            const currentUserId = {{ auth()->id() }};
            let lastMessageId = 0;
            let isPolling = false;
            let typingTimer = null;

            $.ajaxSetup({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
            });

            // 1. Initial Fetch & Start Polling Loop
            fetchMessages();

            /*
             * AJAX Polling Loop:
             * We poll every 3.5 seconds to fetch new messages and partner typing indicators.
             * 3.5 seconds is pragmatically structured to provide near-instant real-time responsiveness
             * for patients & doctors while remaining light on server CPU & database query load.
             */
            setInterval(function () {
                if (!isPolling) {
                    fetchMessages();
                }
            }, 3500);

            function fetchMessages() {
                isPolling = true;
                $.ajax({
                    url: '/chat/room/' + activeRoomId + '/messages?after_id=' + lastMessageId,
                    type: 'GET',
                    success: function (response) {
                        if (response.success) {
                            if (response.messages && response.messages.length > 0) {
                                response.messages.forEach(function (msg) {
                                    renderMessage(msg);
                                    if (msg.id > lastMessageId) {
                                        lastMessageId = msg.id;
                                    }
                                });
                                scrollToBottom();
                            }

                            // Update typing status banner
                            if (response.is_partner_typing) {
                                $('#typing-indicator').removeClass('d-none');
                            } else {
                                $('#typing-indicator').addClass('d-none');
                            }
                        }
                    },
                    complete: function () {
                        isPolling = false;
                    }
                });
            }

            // 2. Render Single Bubble Message into DOM
            function renderMessage(msg) {
                if ($('#msg-' + msg.id).length > 0) return;

                const isMe = msg.is_me;
                const bubbleClass = isMe ? 'bg-primary text-white ms-auto' : 'bg-white text-dark border me-auto';
                const wrapperClass = isMe ? 'justify-content-end' : 'justify-content-start';

                let attachmentHtml = '';
                if (msg.attachment_url) {
                    if (msg.attachment_type === 'image') {
                        attachmentHtml = `
                            <div class="mt-2">
                                <a href="${msg.attachment_url}" target="_blank">
                                    <img src="${msg.attachment_url}" class="img-fluid rounded border shadow-sm" style="max-max-width: 260px; max-height: 200px; object-fit: cover;">
                                </a>
                            </div>`;
                    } else {
                        attachmentHtml = `
                            <div class="mt-2 p-2 rounded ${isMe ? 'bg-white bg-opacity-25 text-white' : 'bg-light border text-dark'}">
                                <a href="${msg.attachment_url}" target="_blank" class="${isMe ? 'text-white' : 'text-primary'} text-decoration-none fw-semibold small">
                                    <i class="bi bi-file-earmark-arrow-down me-1"></i> ${msg.attachment_name || 'Download Attachment'}
                                </a>
                            </div>`;
                    }
                }

                let seenHtml = '';
                if (isMe) {
                    seenHtml = msg.is_seen ? '<i class="bi bi-check2-all text-white-50 ms-1" title="Seen"></i>' : '<i class="bi bi-check2 text-white-50 ms-1" title="Sent"></i>';
                }

                const html = `
                    <div class="d-flex ${wrapperClass} mb-3" id="msg-${msg.id}">
                        <div class="p-3 rounded-4 shadow-sm" style="max-width: 70%; ${isMe ? 'background: linear-gradient(135deg, #4f46e5, #6366f1);' : ''}">
                            <div class="small fw-semibold mb-1 ${isMe ? 'text-white-50' : 'text-muted'}">${msg.sender_name}</div>
                            ${msg.message ? `<div class="mb-0 text-break">${escapeHtml(msg.message)}</div>` : ''}
                            ${attachmentHtml}
                            <div class="text-end mt-1 extra-small ${isMe ? 'text-white-50' : 'text-muted'}" style="font-size: 0.7rem;">
                                ${msg.time} ${seenHtml}
                            </div>
                        </div>
                    </div>`;

                $('#messages-list').append(html);
            }

            function scrollToBottom() {
                var container = document.getElementById('messages-container');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }

            function escapeHtml(text) {
                return $('<div>').text(text).html();
            }

            // 3. Handle File Attachment Selection
            $('#trigger-attachment-btn').on('click', function () {
                $('#chat-attachment-input').click();
            });

            $('#chat-attachment-input').on('change', function () {
                var file = this.files[0];
                if (file) {
                    $('#attachment-preview-name').text(file.name);
                    $('#attachment-preview-container').removeClass('d-none');
                } else {
                    $('#attachment-preview-container').addClass('d-none');
                }
            });

            $('#remove-attachment-btn').on('click', function () {
                $('#chat-attachment-input').val('');
                $('#attachment-preview-container').addClass('d-none');
            });

            // 4. Handle Typing Indicator Event (Debounced)
            $('#chat-message-input').on('input', function () {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function () {
                    $.post('/chat/room/' + activeRoomId + '/typing');
                }, 300);
            });

            // 5. Submit Message & Attachments via AJAX Form Data
            $('#chat-send-form').on('submit', function (e) {
                e.preventDefault();

                var formData = new FormData(this);
                var messageText = $('#chat-message-input').val().trim();
                var hasFile = $('#chat-attachment-input')[0].files.length > 0;

                if (!messageText && !hasFile) return;

                $('#chat-send-btn').prop('disabled', true);

                $.ajax({
                    url: '/chat/room/' + activeRoomId + '/send',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        if (response.success && response.message) {
                            renderMessage(response.message);
                            if (response.message.id > lastMessageId) {
                                lastMessageId = response.message.id;
                            }
                            scrollToBottom();
                            $('#chat-message-input').val('');
                            $('#chat-attachment-input').val('');
                            $('#attachment-preview-container').addClass('d-none');
                        }
                    },
                    error: function (xhr) {
                        Swal.fire('Error!', xhr.responseJSON?.message || 'Failed to send message.', 'error');
                    },
                    complete: function () {
                        $('#chat-send-btn').prop('disabled', false);
                    }
                });
            });

            // 6. Local Search Filter for Contact List
            $('#chat-search-input').on('keyup', function () {
                var value = $(this).val().toLowerCase();
                $('.chat-room-item').filter(function () {
                    $(this).toggle($(this).data('name').indexOf(value) > -1);
                });
            });
        @endif
    });
</script>
@endpush
