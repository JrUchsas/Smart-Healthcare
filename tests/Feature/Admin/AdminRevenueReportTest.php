<?php

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->adminUser = User::factory()->create(['user_type' => 'admin']);
    $this->adminUser->assignRole('Admin');

    $patientUser = User::factory()->create(['user_type' => 'patient']);
    $patientUser->assignRole('Patient');
    $patient = Patient::create(['user_id' => $patientUser->id]);

    $dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology', 'is_active' => true]);
    $doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $doctorUser->assignRole('Doctor');
    $doctor = Doctor::create([
        'user_id' => $doctorUser->id,
        'department_id' => $dept->id,
        'consultation_fee' => 150.00,
        'is_available' => true,
    ]);

    $schedule = DoctorSchedule::create([
        'doctor_id' => $doctor->id,
        'schedule_date' => now()->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
        'slot_duration_minutes' => 30,
    ]);

    $slot = AppointmentSlot::create([
        'doctor_schedule_id' => $schedule->id,
        'doctor_id' => $doctor->id,
        'start_time' => '10:00:00',
        'end_time' => '10:30:00',
        'is_booked' => true,
        'status' => 'booked',
    ]);

    $appointment = Appointment::create([
        'appointment_number' => 'APT-REV-001',
        'patient_id' => $patient->id,
        'doctor_id' => $doctor->id,
        'appointment_slot_id' => $slot->id,
        'appointment_date' => now()->toDateString(),
        'consultation_fee' => 150.00,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $payment = Payment::create([
        'payment_number' => 'PAY-REV-001',
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'amount' => 150.00,
        'payment_method' => 'credit_card',
        'payment_status' => 'completed',
        'paid_at' => now(),
    ]);

    Invoice::create([
        'invoice_number' => 'INV-REV-001',
        'appointment_id' => $appointment->id,
        'patient_id' => $patient->id,
        'payment_id' => $payment->id,
        'subtotal_amount' => 150.00,
        'tax_amount' => 0.00,
        'total_amount' => 150.00,
        'status' => 'paid',
        'issued_at' => now(),
    ]);
});

test('admin can access revenue reports page and view financial analytics', function () {
    $response = $this->actingAs($this->adminUser)
        ->get(route('admin.reports.revenue'));

    $response->assertStatus(200);
    $response->assertSee('Revenue Analytics &amp; Financial Ledger', false);
    $response->assertSee('Cardiology');
    $response->assertSee('INV-REV-001');
});

test('non admin cannot access revenue reports page', function () {
    $patientUser = User::factory()->create(['user_type' => 'patient']);
    $patientUser->assignRole('Patient');

    $response = $this->actingAs($patientUser)
        ->get(route('admin.reports.revenue'));

    $response->assertStatus(403);
});
