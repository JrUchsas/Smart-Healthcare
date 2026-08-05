<?php

namespace App\Http\Controllers;

use App\Http\Requests\SendChatMessageRequest;
use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use App\Services\ChatService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function __construct(
        protected ChatService $chatService
    ) {}

    /**
     * Display real-time Chat & Communication hub.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $rooms = $this->chatService->getRoomsForUser($user);

        $activeRoomId = $request->query('room');
        $activeRoom = null;

        if ($activeRoomId) {
            $activeRoom = $rooms->firstWhere('id', (int) $activeRoomId);
        }

        if (! $activeRoom && $rooms->isNotEmpty()) {
            $activeRoom = $rooms->first();
        }

        if ($activeRoom) {
            $this->chatService->markAsSeen($activeRoom, $user);
        }

        return view('chat.index', compact('rooms', 'activeRoom'));
    }

    /**
     * Start or jump into a chat room with a Doctor or Patient from anywhere in the portal.
     */
    public function start(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($request->has('doctor_id')) {
            $patient = Patient::firstOrCreate(['user_id' => $user->id]);
            $doctor = Doctor::findOrFail($request->input('doctor_id'));
            $appointmentId = $request->input('appointment_id');

            $room = $this->chatService->getOrCreateRoom($patient->id, $doctor->id, $appointmentId);

            return redirect()->route('chat.index', ['room' => $room->id]);
        }

        if ($request->has('patient_id')) {
            $doctor = Doctor::firstOrCreate(['user_id' => $user->id]);
            $patient = Patient::findOrFail($request->input('patient_id'));
            $appointmentId = $request->input('appointment_id');

            $room = $this->chatService->getOrCreateRoom($patient->id, $doctor->id, $appointmentId);

            return redirect()->route('chat.index', ['room' => $room->id]);
        }

        return redirect()->route('chat.index');
    }

    /**
     * Fetch JSON messages payload for polling loop (supports incremental after_id).
     */
    public function getMessages(ChatRoom $room, Request $request): JsonResponse
    {
        $user = $request->user();
        $afterId = $request->query('after_id', 0);

        // Security authorization check
        $this->authorizeRoomAccess($room, $user);

        // Mark incoming messages as seen & update current user heartbeat timestamp
        $this->chatService->markAsSeen($room, $user);
        Cache::put('user-last-seen-'.$user->id, now(), now()->addMinutes(10));

        $query = ChatMessage::with('sender')
            ->where('chat_room_id', $room->id);

        if ($afterId > 0) {
            $query->where('id', '>', $afterId);
        }

        $messages = $query->orderBy('id', 'asc')->get()->map(function ($msg) use ($user) {
            return [
                'id' => $msg->id,
                'sender_id' => $msg->sender_id,
                'is_me' => (int) $msg->sender_id === (int) $user->id,
                'sender_name' => $msg->sender?->name ?? 'User',
                'message' => $msg->message,
                'attachment_url' => $msg->attachment_url,
                'attachment_type' => $msg->attachment_type,
                'attachment_name' => $msg->attachment_name,
                'is_seen' => $msg->is_seen,
                'time' => $msg->created_at?->format('h:i A'),
                'date' => $msg->created_at?->format('d M Y'),
            ];
        });

        // Determine if partner is typing (typing status updated within last 6 seconds)
        $isPartnerTyping = false;
        $partnerUser = null;

        if ($user->patient && (int) $room->patient_id === (int) $user->patient->id) {
            $isPartnerTyping = $room->doctor_typing_at && $room->doctor_typing_at->diffInSeconds(now()) < 6;
            $partnerUser = $room->doctor?->user;
        } elseif ($user->doctor && (int) $room->doctor_id === (int) $user->doctor->id) {
            $isPartnerTyping = $room->patient_typing_at && $room->patient_typing_at->diffInSeconds(now()) < 6;
            $partnerUser = $room->patient?->user;
        }

        $isPartnerOnline = false;
        $partnerStatusText = 'Offline';

        if ($partnerUser) {
            $lastSeen = Cache::get('user-last-seen-'.$partnerUser->id);
            if ($lastSeen) {
                $secondsAgo = now()->diffInSeconds($lastSeen);
                if ($secondsAgo < 90) {
                    $isPartnerOnline = true;
                    $partnerStatusText = 'Active Now';
                } else {
                    $partnerStatusText = 'Active '.$lastSeen->diffForHumans();
                }
            }
        }

        return response()->json([
            'success' => true,
            'messages' => $messages,
            'is_partner_typing' => $isPartnerTyping,
            'is_partner_online' => $isPartnerOnline,
            'partner_status_text' => $partnerStatusText,
        ]);
    }

    /**
     * Send message via ChatService and return JSON response.
     */
    public function sendMessage(ChatRoom $room, SendChatMessageRequest $request): JsonResponse
    {
        $user = $request->user();
        $this->authorizeRoomAccess($room, $user);

        $message = $this->chatService->sendMessage($room, $user, $request->validated());

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'sender_id' => $message->sender_id,
                'is_me' => true,
                'sender_name' => $user->name,
                'message' => $message->message,
                'attachment_url' => $message->attachment_url,
                'attachment_type' => $message->attachment_type,
                'attachment_name' => $message->attachment_name,
                'is_seen' => false,
                'time' => $message->created_at?->format('h:i A'),
                'date' => $message->created_at?->format('d M Y'),
            ],
        ]);
    }

    /**
     * Update typing indicator for current user.
     */
    public function typing(ChatRoom $room, Request $request): JsonResponse
    {
        $user = $request->user();
        $this->authorizeRoomAccess($room, $user);

        $this->chatService->updateTypingStatus($room, $user);

        return response()->json(['success' => true]);
    }

    /**
     * Download or view inline chat attachment (PDF, Images, Documents) securely.
     */
    public function downloadAttachment(ChatMessage $message, Request $request): mixed
    {
        $user = $request->user();
        $room = $message->chatRoom;

        $this->authorizeRoomAccess($room, $user);

        if (! $message->attachment_path || ! Storage::disk('public')->exists($message->attachment_path)) {
            abort(404, 'Attachment file not found.');
        }

        $filePath = Storage::disk('public')->path($message->attachment_path);
        $mimeType = Storage::disk('public')->mimeType($message->attachment_path) ?? 'application/octet-stream';

        if ($message->attachment_type === 'image' || str_contains($mimeType, 'pdf')) {
            return response()->file($filePath, [
                'Content-Type' => $mimeType,
                'Content-Disposition' => 'inline; filename="'.($message->attachment_name ?? basename($filePath)).'"',
            ]);
        }

        return Storage::disk('public')->download($message->attachment_path, $message->attachment_name);
    }

    /**
     * Authorize user access to specific chat room.
     */
    protected function authorizeRoomAccess(ChatRoom $room, User $user): void
    {
        if ($user->hasRole('Admin') || $user->user_type === 'admin') {
            return;
        }

        if ($user->patient && (int) $room->patient_id === (int) $user->patient->id) {
            return;
        }

        if ($user->doctor && (int) $room->doctor_id === (int) $user->doctor->id) {
            return;
        }

        abort(403, 'Unauthorized chat room access.');
    }
}
