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

    // Doctor setup
    $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $dept = Department::create(['name' => 'General', 'slug' => 'general', 'is_active' => true]);
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'department_id' => $dept->id,
        'consultation_fee' => 50.00,
        'is_available' => true,
    ]);

    // Patient setup
    $patientUser = User::factory()->create(['user_type' => 'patient']);
    $patientUser->assignRole('Patient');
    $this->patient = Patient::create(['user_id' => $patientUser->id]);

    // Slot & appointment setup — paid and confirmed so the doctor can prescribe
    $schedule = DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'schedule_date' => now()->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '17:00:00',
        'slot_duration_minutes' => 30,
    ]);

    $slot = AppointmentSlot::create([
        'doctor_id' => $this->doctor->id,
        'doctor_schedule_id' => $schedule->id,
        'start_time' => '09:00:00',
        'end_time' => '09:30:00',
        'is_booked' => true,
        'status' => 'booked',
    ]);

    $this->appointment = Appointment::create([
        'appointment_number' => 'APT-RX-001',
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_slot_id' => $slot->id,
        'appointment_date' => now()->toDateString(),
        'consultation_fee' => 50.00,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);
});

test('doctor can view the prescription builder for a confirmed appointment', function () {
    $response = $this->actingAs($this->doctorUser)
        ->get(route('doctor.prescriptions.create', $this->appointment));

    $response->assertStatus(200);
    $response->assertSee('Prescription Builder');
});

test('doctor can issue a prescription with medicine items', function () {
    $response = $this->actingAs($this->doctorUser)
        ->post(route('doctor.prescriptions.store'), [
            'appointment_id' => $this->appointment->id,
            'symptoms' => 'Fever and body ache',
            'diagnosis' => 'Viral fever',
            'advice' => 'Rest and hydration',
            'follow_up_date' => now()->addDays(7)->toDateString(),
            'doctor_signature' => 'Dr. Test Doctor',
            'medicines' => [
                [
                    'medicine_name' => 'Paracetamol 500mg',
                    'dosage' => '1 tablet',
                    'duration' => '5 days',
                    'instructions' => 'After meals',
                ],
                [
                    'medicine_name' => 'Vitamin C',
                    'dosage' => '1 tablet',
                    'duration' => '7 days',
                    'instructions' => null,
                ],
            ],
        ]);

    $response->assertRedirect();

    $prescription = Prescription::where('appointment_id', $this->appointment->id)->first();

    expect($prescription)->not->toBeNull()
        ->and($prescription->diagnosis)->toBe('Viral fever')
        ->and($prescription->items)->toHaveCount(2);
});

test('prescription store fails when medicine rows are missing', function () {
    $response = $this->actingAs($this->doctorUser)
        ->post(route('doctor.prescriptions.store'), [
            'appointment_id' => $this->appointment->id,
            'symptoms' => 'Cough',
            'medicines' => [],
        ]);

    $response->assertSessionHasErrors('medicines');
});

test('doctor can view a prescription they issued', function () {
    $prescription = Prescription::create([
        'prescription_number' => 'RX-TEST001',
        'appointment_id' => $this->appointment->id,
        'doctor_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'diagnosis' => 'Test diagnosis',
    ]);

    $response = $this->actingAs($this->doctorUser)
        ->get(route('doctor.prescriptions.show', $prescription));

    $response->assertStatus(200);
    $response->assertSee('RX-TEST001');
});

test('doctor can stream prescription as PDF', function () {
    $prescription = Prescription::create([
        'prescription_number' => 'RX-PDF001',
        'appointment_id' => $this->appointment->id,
        'doctor_id' => $this->doctor->id,
        'patient_id' => $this->patient->id,
        'diagnosis' => 'PDF test',
    ]);

    $response = $this->actingAs($this->doctorUser)
        ->get(route('doctor.prescriptions.pdf', $prescription));

    $response->assertStatus(200);
    expect($response->headers->get('content-type'))->toContain('application/pdf');
});
