<?php

namespace App\Services;

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;

class ChatService
{
    /**
     * Get an existing chat room or create a new one between a Patient and Doctor.
     */
    public function getOrCreateRoom(int $patientId, int $doctorId, ?int $appointmentId = null): ChatRoom
    {
        return ChatRoom::firstOrCreate(
            ['patient_id' => $patientId, 'doctor_id' => $doctorId],
            ['appointment_id' => $appointmentId, 'last_message_at' => now()]
        );
    }

    /**
     * Save and process an incoming chat message with optional image/document attachment.
     */
    public function sendMessage(ChatRoom $room, User $sender, array $data): ChatMessage
    {
        $attachmentPath = null;
        $attachmentType = null;
        $attachmentName = null;

        if (isset($data['attachment']) && $data['attachment'] instanceof UploadedFile) {
            $file = $data['attachment'];
            $attachmentName = $file->getClientOriginalName();
            $mime = strtolower($file->getMimeType());

            if (str_contains($mime, 'image')) {
                $attachmentType = 'image';
                $filename = 'img_'.now()->format('Ymd_His').'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $path = 'chat_attachments/images/'.$filename;

                // Process image with Intervention Image if available, or fallback to storage
                try {
                    $img = Image::read($file)->scale(width: 1200);
                    Storage::disk('public')->put($path, (string) $img->encode());
                } catch (\Throwable $e) {
                    $path = $file->storeAs('chat_attachments/images', $filename, 'public');
                }

                $attachmentPath = $path;
            } else {
                $attachmentType = 'document';
                $filename = 'doc_'.now()->format('Ymd_His').'_'.uniqid().'.'.$file->getClientOriginalExtension();
                $attachmentPath = $file->storeAs('chat_attachments/documents', $filename, 'public');
            }
        }

        $message = ChatMessage::create([
            'chat_room_id' => $room->id,
            'sender_id' => $sender->id,
            'message' => $data['message'] ?? null,
            'attachment_path' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'attachment_name' => $attachmentName,
            'is_seen' => false,
        ]);

        $room->update(['last_message_at' => now()]);

        // Send notification to recipient user
        $recipientUserId = null;
        $senderDisplayName = $sender->name;

        if ((int) $sender->id === (int) $room->patient?->user_id) {
            $recipientUserId = $room->doctor?->user_id;
        } else {
            $recipientUserId = $room->patient?->user_id;
            if ($sender->hasRole('Doctor') || $sender->user_type === 'doctor') {
                $senderDisplayName = 'Dr. '.$sender->name;
            }
        }

        if ($recipientUserId) {
            $snippet = ! empty($data['message'])
                ? Str::limit($data['message'], 60)
                : 'Sent a file attachment: '.$attachmentName;

            Notification::create([
                'user_id' => $recipientUserId,
                'title' => 'New Message from '.$senderDisplayName,
                'message' => $snippet,
                'type' => 'chat',
                'is_read' => false,
            ]);
        }

        return $message;
    }

    /**
     * Mark unread messages sent by the recipient as seen.
     */
    public function markAsSeen(ChatRoom $room, User $currentUser): int
    {
        return ChatMessage::where('chat_room_id', $room->id)
            ->where('sender_id', '!=', $currentUser->id)
            ->where('is_seen', false)
            ->update([
                'is_seen' => true,
                'seen_at' => now(),
            ]);
    }

    /**
     * Update user typing status timestamp in chat room.
     */
    public function updateTypingStatus(ChatRoom $room, User $user): void
    {
        if ($user->hasRole('Patient') || $user->user_type === 'patient') {
            $room->update(['patient_typing_at' => now()]);
        } else {
            $room->update(['doctor_typing_at' => now()]);
        }
    }

    /**
     * Retrieve all chat rooms for the logged-in user with latest message & unread badge count.
     */
    public function getRoomsForUser(User $user): Collection
    {
        if ($user->patient) {
            $rooms = ChatRoom::with(['doctor.user', 'doctor.department', 'latestMessage'])
                ->where('patient_id', $user->patient->id);
        } elseif ($user->doctor) {
            $rooms = ChatRoom::with(['patient.user', 'latestMessage'])
                ->where('doctor_id', $user->doctor->id);
        } else {
            // Admin sees all active chat rooms
            $rooms = ChatRoom::with(['patient.user', 'doctor.user', 'latestMessage']);
        }

        return $rooms->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($room) use ($user) {
                $room->unread_count = ChatMessage::where('chat_room_id', $room->id)
                    ->where('sender_id', '!=', $user->id)
                    ->where('is_seen', false)
                    ->count();

                return $room;
            });
    }
}
