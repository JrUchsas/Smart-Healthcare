<?php

use App\Models\ChatRoom;
use App\Models\Doctor;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\User;
use App\Services\ChatService;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->doctorUser = User::factory()->create(['name' => 'Doctor John', 'user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'consultation_fee' => 150.00,
        'experience_years' => 5,
        'qualification' => 'MBBS',
        'is_available' => true,
    ]);

    $this->patientUser = User::factory()->create(['name' => 'Patient Sarah', 'user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
    $this->patient = Patient::firstOrCreate(['user_id' => $this->patientUser->id]);

    $this->room = ChatRoom::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'last_message_at' => now(),
    ]);
});

test('sending chat message generates notification for recipient', function () {
    $chatService = new ChatService;

    // Doctor sends message to patient
    $chatService->sendMessage($this->room, $this->doctorUser, ['message' => 'Hello Sarah, please share your test report.']);

    $notification = Notification::where('user_id', $this->patientUser->id)->first();

    expect($notification)->not->toBeNull();
    expect($notification->title)->toContain('New Message from Dr. Doctor John');
    expect($notification->message)->toContain('Hello Sarah, please share your test report');
    expect($notification->type)->toBe('chat');
});
