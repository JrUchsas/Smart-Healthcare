<?php

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Prescription;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $doctorUser->assignRole('Doctor');
    $dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology', 'is_active' => true]);
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'department_id' => $dept->id,
        'consultation_fee' => 100.00,
        'is_available' => true,
    ]);

    $this->patientUser = User::factory()->create(['user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
    $this->patient = Patient::create(['user_id' => $this->patientUser->id]);

    $schedule = DoctorSchedule::create([
        'doctor_id' => $doctor->id,
        'schedule_date' => now()->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '12:00:00',
        'slot_duration_minutes' => 30,
    ]);

    $slot = AppointmentSlot::create([
        'doctor_id' => $doctor->id,
        'doctor_schedule_id' => $schedule->id,
        'start_time' => '10:00:00',
        'end_time' => '10:30:00',
        'is_booked' => true,
        'status' => 'booked',
    ]);

    $appointment = Appointment::create([
        'appointment_number' => 'APT-PRXP-001',
        'patient_id' => $this->patient->id,
        'doctor_id' => $doctor->id,
        'appointment_slot_id' => $slot->id,
        'appointment_date' => now()->toDateString(),
        'consultation_fee' => 100.00,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $this->prescription = Prescription::create([
        'prescription_number' => 'RX-PATIENT001',
        'appointment_id' => $appointment->id,
        'doctor_id' => $doctor->id,
        'patient_id' => $this->patient->id,
        'symptoms' => 'Chest pain',
        'diagnosis' => 'Angina',
        'advice' => 'Avoid stress',
    ]);

    $this->prescription->items()->create([
        'medicine_name' => 'Aspirin 75mg',
        'dosage' => '1 tablet',
        'duration' => '30 days',
        'instructions' => 'After breakfast',
    ]);
});

test('patient can view their prescriptions index page', function () {
    $response = $this->actingAs($this->patientUser)
        ->get(route('patient.prescriptions.index'));

    $response->assertStatus(200);
    $response->assertSee('My Prescriptions');
    $response->assertSee('RX-PATIENT001');
});

test('patient can view a single prescription detail', function () {
    $response = $this->actingAs($this->patientUser)
        ->get(route('patient.prescriptions.show', $this->prescription));

    $response->assertStatus(200);
    $response->assertSee('Angina');
    $response->assertSee('Aspirin 75mg');
});

test('patient cannot view another patients prescription', function () {
    $otherUser = User::factory()->create(['user_type' => 'patient']);
    $otherUser->assignRole('Patient');
    // Must have a Patient record so the controller can do Auth::user()->patient->id
    Patient::create(['user_id' => $otherUser->id]);

    $response = $this->actingAs($otherUser)
        ->get(route('patient.prescriptions.show', $this->prescription));

    $response->assertStatus(403);
});

test('patient can stream prescription as PDF', function () {
    $response = $this->actingAs($this->patientUser)
        ->get(route('patient.prescriptions.pdf', $this->prescription));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});

test('patient can download prescription as PDF', function () {
    $response = $this->actingAs($this->patientUser)
        ->get(route('patient.prescriptions.download', $this->prescription));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});
