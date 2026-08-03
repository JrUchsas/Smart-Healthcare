<?php

use App\Models\Doctor;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->doctorUser = User::factory()->create([
        'user_type' => 'doctor',
    ]);
    $this->doctorUser->assignRole('Doctor');

    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'consultation_fee' => 50.00,
        'experience_years' => 5,
        'is_available' => true,
    ]);
});

test('doctor can view schedule index', function () {
    $response = $this->actingAs($this->doctorUser)->get(route('doctor.schedules.index'));

    $response->assertStatus(200);
});

test('admin can view schedule index without 403 error', function () {
    $admin = User::factory()->create(['user_type' => 'admin']);
    $admin->assignRole('Admin');

    $response = $this->actingAs($admin)->get(route('doctor.schedules.index'));

    $response->assertStatus(200);
});

test('patient can view schedule index without 403 error', function () {
    $patient = User::factory()->create(['user_type' => 'patient']);
    $patient->assignRole('Patient');

    $response = $this->actingAs($patient)->get(route('doctor.schedules.index'));

    $response->assertStatus(200);
});

test('doctor can create schedule block and generate slots via SlotGenerationService', function () {
    $tomorrow = now()->addDay()->format('Y-m-d');

    $response = $this->actingAs($this->doctorUser)->post(route('doctor.schedules.store'), [
        'schedule_date' => $tomorrow,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'slot_duration_minutes' => 30,
    ]);

    $response->assertRedirect(route('doctor.schedules.index'));

    $this->assertDatabaseHas('doctor_schedules', [
        'doctor_id' => $this->doctor->id,
        'slot_duration_minutes' => 30,
    ]);

    // 09:00 to 11:00 with 30 minute duration generates 4 slots: 09:00-09:30, 09:30-10:00, 10:00-10:30, 10:30-11:00
    $this->assertDatabaseCount('appointment_slots', 4);
});

test('doctor cannot create overlapping schedule blocks', function () {
    $tomorrow = now()->addDay()->format('Y-m-d');

    // First schedule: 09:00 - 11:00
    $this->actingAs($this->doctorUser)->post(route('doctor.schedules.store'), [
        'schedule_date' => $tomorrow,
        'start_time' => '09:00',
        'end_time' => '11:00',
        'slot_duration_minutes' => 30,
    ]);

    // Overlapping schedule attempt: 10:00 - 12:00
    $response = $this->actingAs($this->doctorUser)->post(route('doctor.schedules.store'), [
        'schedule_date' => $tomorrow,
        'start_time' => '10:00',
        'end_time' => '12:00',
        'slot_duration_minutes' => 30,
    ]);

    $response->assertSessionHasErrors('start_time');
});
