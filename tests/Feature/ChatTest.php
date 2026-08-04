<?php

use App\Models\ChatMessage;
use App\Models\ChatRoom;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->patientUser = User::factory()->create(['user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
    $this->patient = Patient::create(['user_id' => $this->patientUser->id]);

    $dept = Department::create(['name' => 'Dermatology', 'slug' => 'dermatology', 'is_active' => true]);
    $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'department_id' => $dept->id,
        'consultation_fee' => 100.00,
        'is_available' => true,
    ]);

    $this->room = ChatRoom::create([
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'last_message_at' => now(),
    ]);
});

test('patient can access chat interface', function () {
    $response = $this->actingAs($this->patientUser)
        ->get(route('chat.index'));

    $response->assertStatus(200);
    $response->assertSee('Active Conversations');
});

test('doctor can start chat with patient', function () {
    $response = $this->actingAs($this->doctorUser)
        ->get(route('chat.start', ['patient_id' => $this->patient->id]));

    $response->assertRedirect(route('chat.index', ['room' => $this->room->id]));
});

test('patient can send text message in chat room', function () {
    $response = $this->actingAs($this->patientUser)
        ->postJson(route('chat.send', $this->room), [
            'message' => 'Hello Doctor, I have a question about my skin prescription.',
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->assertDatabaseHas('chat_messages', [
        'chat_room_id' => $this->room->id,
        'sender_id' => $this->patientUser->id,
        'message' => 'Hello Doctor, I have a question about my skin prescription.',
    ]);
});

test('patient can upload image attachment in chat room', function () {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('skin_rash.jpg', 600, 600);

    $response = $this->actingAs($this->patientUser)
        ->postJson(route('chat.send', $this->room), [
            'message' => 'Here is a photo of the rash.',
            'attachment' => $file,
        ]);

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $message = ChatMessage::where('chat_room_id', $this->room->id)->first();
    expect($message->attachment_type)->toBe('image');
    expect($message->attachment_path)->not->toBeNull();
});

test('polling messages endpoint returns messages and updates seen status', function () {
    ChatMessage::create([
        'chat_room_id' => $this->room->id,
        'sender_id' => $this->doctorUser->id,
        'message' => 'Hello, please describe your symptoms.',
        'is_seen' => false,
    ]);

    $response = $this->actingAs($this->patientUser)
        ->getJson(route('chat.messages', $this->room));

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);
    expect(count($response->json('messages')))->toBeGreaterThan(0);

    $this->assertDatabaseHas('chat_messages', [
        'chat_room_id' => $this->room->id,
        'is_seen' => true,
    ]);
});

test('updating typing status registers timestamp', function () {
    $response = $this->actingAs($this->patientUser)
        ->postJson(route('chat.typing', $this->room));

    $response->assertStatus(200);
    $response->assertJsonPath('success', true);

    $this->room->refresh();
    expect($this->room->patient_typing_at)->not->toBeNull();
});

test('unauthorized user cannot access another users chat room', function () {
    $otherUser = User::factory()->create(['user_type' => 'patient']);
    $otherUser->assignRole('Patient');

    $response = $this->actingAs($otherUser)
        ->getJson(route('chat.messages', $this->room));

    $response->assertStatus(403);
});
