<?php

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);
    Storage::fake('public');

    $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'consultation_fee' => 150.00,
        'experience_years' => 5,
        'qualification' => 'MBBS',
        'is_available' => true,
    ]);

    $this->patientUser = User::factory()->create(['user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
    $this->patient = Patient::firstOrCreate(['user_id' => $this->patientUser->id]);

    $this->room = ChatRoom::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'last_message_at' => now(),
    ]);
});

test('participant can download or view chat pdf attachment without 403 error', function () {
    $file = UploadedFile::fake()->create('medical_report.pdf', 100, 'application/pdf');
    $path = $file->storeAs('chat_attachments/documents', 'doc_test.pdf', 'public');

    $message = ChatMessage::create([
        'chat_room_id' => $this->room->id,
        'sender_id' => $this->patientUser->id,
        'message' => 'Here is my report PDF',
        'attachment_path' => $path,
        'attachment_type' => 'document',
        'attachment_name' => 'medical_report.pdf',
        'is_seen' => false,
    ]);

    $response = $this->actingAs($this->patientUser)
        ->get(route('chat.attachment', $message->id));

    $response->assertOk();

    // Doctor can also view it
    $doctorResponse = $this->actingAs($this->doctorUser)
        ->get(route('chat.attachment', $message->id));

    $doctorResponse->assertOk();
});
