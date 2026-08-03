<?php

use App\Models\AppointmentSlot;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\User;
use App\Services\AppointmentBookingService;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    // Create Patient
    $this->patientUser = User::factory()->create(['user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
    $this->patient = Patient::create(['user_id' => $this->patientUser->id]);

    // Create Doctor & Department
    $this->dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology', 'is_active' => true]);
    $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'department_id' => $this->dept->id,
        'consultation_fee' => 100.00,
        'experience_years' => 10,
        'is_available' => true,
    ]);

    // Create Schedule & Slot
    $this->schedule = DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'schedule_date' => now()->addDay()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'slot_duration_minutes' => 30,
    ]);

    $this->slot = AppointmentSlot::create([
        'doctor_schedule_id' => $this->schedule->id,
        'doctor_id' => $this->doctor->id,
        'start_time' => '09:00:00',
        'end_time' => '09:30:00',
        'is_booked' => false,
        'status' => 'available',
    ]);
});

test('patient can search for doctors via DoctorSearchController', function () {
    $response = $this->actingAs($this->patientUser)->get(route('patient.doctors.search'));

    $response->assertStatus(200);
});

test('patient can book available appointment slot using AppointmentBookingService', function () {
    $service = app(AppointmentBookingService::class);

    $appointment = $service->bookAppointment($this->patient, [
        'appointment_slot_id' => $this->slot->id,
        'patient_notes' => 'Routine cardiology checkup',
    ]);

    expect($appointment)->not->toBeNull();
    expect($appointment->status)->toBe('pending');
    expect($appointment->consultation_fee)->toBe('100.00');

    $this->assertDatabaseHas('appointment_slots', [
        'id' => $this->slot->id,
        'is_booked' => 1,
        'status' => 'booked',
    ]);
});

test('double booking throws an exception inside database transaction', function () {
    $service = app(AppointmentBookingService::class);

    // Book once
    $service->bookAppointment($this->patient, [
        'appointment_slot_id' => $this->slot->id,
    ]);

    // Attempt second booking on same slot
    expect(function () use ($service) {
        $service->bookAppointment($this->patient, [
            'appointment_slot_id' => $this->slot->id,
        ]);
    })->toThrow(Exception::class);
});
