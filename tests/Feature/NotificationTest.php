<?php

use App\Models\Notification;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->patientUser = User::factory()->create(['user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
});

test('patient can view their notifications', function () {
    Notification::create([
        'user_id' => $this->patientUser->id,
        'title' => 'Appointment Slot Expired & Money Refunded',
        'message' => 'Your appointment #APT-100 has expired. Full refund of ৳150.00 processed.',
        'type' => 'refund',
        'is_read' => false,
    ]);

    $response = $this->actingAs($this->patientUser)
        ->get(route('notifications.index'));

    $response->assertOk()
        ->assertSee('Appointment Slot Expired &amp; Money Refunded', false)
        ->assertSee('Full refund of ৳150.00 processed');
});

test('patient can clear all notifications', function () {
    Notification::create([
        'user_id' => $this->patientUser->id,
        'title' => 'Test Notification',
        'message' => 'Test message',
        'type' => 'info',
        'is_read' => false,
    ]);

    $response = $this->actingAs($this->patientUser)
        ->delete(route('notifications.clear'));

    $response->assertRedirect(route('notifications.index'));

    $this->assertDatabaseMissing('notifications', [
        'user_id' => $this->patientUser->id,
    ]);
});
