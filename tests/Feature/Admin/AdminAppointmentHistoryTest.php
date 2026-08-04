<?php

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->adminUser = User::factory()->create(['user_type' => 'admin']);
    $this->adminUser->assignRole('Admin');

    $patientUser = User::factory()->create(['user_type' => 'patient']);
    $patientUser->assignRole('Patient');
    $this->patient = Patient::create(['user_id' => $patientUser->id]);

    $dept = Department::create(['name' => 'Pediatrics', 'slug' => 'pediatrics', 'is_active' => true]);
    $doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'department_id' => $dept->id,
        'consultation_fee' => 120.00,
        'is_available' => true,
    ]);

    $schedule = DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'schedule_date' => now()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'slot_duration_minutes' => 30,
    ]);

    $slot = AppointmentSlot::create([
        'doctor_schedule_id' => $schedule->id,
        'doctor_id' => $this->doctor->id,
        'start_time' => '09:00:00',
        'end_time' => '09:30:00',
        'is_booked' => true,
        'status' => 'booked',
    ]);

    $this->appointment = Appointment::create([
        'appointment_number' => 'APT-ADMIN-001',
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_slot_id' => $slot->id,
        'appointment_date' => now()->toDateString(),
        'consultation_fee' => 120.00,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);
});

test('admin can view appointment history list page', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.appointments.index'));

    $response->assertStatus(200);
    $response->assertSee('Appointment History');
    $response->assertSee('APT-ADMIN-001');
});

test('admin can filter appointment history by status', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.appointments.index', ['status' => 'confirmed']));

    $response->assertStatus(200);
    $response->assertSee('APT-ADMIN-001');
});

test('admin can view single appointment detail page', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.appointments.show', $this->appointment));

    $response->assertStatus(200);
    $response->assertSee('APT-ADMIN-001');
    $response->assertSee('Pediatrics');
});

test('non admin cannot access admin appointment history', function () {
    $nonAdmin = User::factory()->create(['user_type' => 'patient']);
    $nonAdmin->assignRole('Patient');

    $response = $this->actingAs($nonAdmin)
        ->get(route('admin.appointments.index'));

    $response->assertStatus(403);
});
