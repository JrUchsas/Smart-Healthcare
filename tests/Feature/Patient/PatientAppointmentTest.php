<?php

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->patientUser = User::factory()->create(['user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
    $this->patient = Patient::create(['user_id' => $this->patientUser->id]);

    $this->dept = Department::create(['name' => 'Neurology', 'slug' => 'neurology', 'is_active' => true]);
    $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'department_id' => $this->dept->id,
        'consultation_fee' => 75.00,
        'experience_years' => 6,
        'is_available' => true,
    ]);

    $this->schedule = DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'schedule_date' => now()->addDay()->toDateString(),
        'start_time' => '10:00:00',
        'end_time' => '11:00:00',
        'slot_duration_minutes' => 30,
    ]);

    $this->slot = AppointmentSlot::create([
        'doctor_schedule_id' => $this->schedule->id,
        'doctor_id' => $this->doctor->id,
        'start_time' => '10:00:00',
        'end_time' => '10:30:00',
        'is_booked' => true,
        'status' => 'booked',
    ]);

    $this->appointment = Appointment::create([
        'appointment_number' => 'APT-20260804-001',
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_slot_id' => $this->slot->id,
        'appointment_date' => $this->schedule->schedule_date,
        'consultation_fee' => 75.00,
        'status' => 'pending',
        'patient_notes' => 'Headache and dizziness',
    ]);
});

test('patient can view their booked appointments index page', function () {
    $response = $this->actingAs($this->patientUser)->get(route('patient.appointments.index'));

    $response->assertStatus(200);
});

test('patient can edit appointment notes', function () {
    $response = $this->actingAs($this->patientUser)->put(route('patient.appointments.update', $this->appointment), [
        'patient_notes' => 'Updated notes: Severe migraine',
    ]);

    $response->assertRedirect(route('patient.appointments.index'));

    $this->assertDatabaseHas('appointments', [
        'id' => $this->appointment->id,
        'patient_notes' => 'Updated notes: Severe migraine',
    ]);
});

test('patient can cancel appointment and release slot back to available status', function () {
    $response = $this->actingAs($this->patientUser)->delete(route('patient.appointments.destroy', $this->appointment));

    $response->assertRedirect(route('patient.appointments.index'));

    // Appointment deleted
    $this->assertDatabaseMissing('appointments', [
        'id' => $this->appointment->id,
    ]);

    // Slot reverted to available
    $this->assertDatabaseHas('appointment_slots', [
        'id' => $this->slot->id,
        'is_booked' => 0,
        'status' => 'available',
    ]);
});

test('deleting paid confirmed appointment releases slot and cleans up payment records', function () {
    $this->appointment->update([
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    $payment = Payment::create([
        'payment_number' => 'PAY-DEL-01',
        'appointment_id' => $this->appointment->id,
        'patient_id' => $this->patient->id,
        'amount' => 75.00,
        'payment_method' => 'credit_card',
        'payment_status' => 'completed',
        'paid_at' => now(),
    ]);

    $response = $this->actingAs($this->patientUser)->delete(route('patient.appointments.destroy', $this->appointment));

    $response->assertRedirect(route('patient.appointments.index'));

    $this->assertDatabaseMissing('appointments', ['id' => $this->appointment->id]);
    $this->assertDatabaseMissing('payments', ['id' => $payment->id]);

    $this->assertDatabaseHas('appointment_slots', [
        'id' => $this->slot->id,
        'is_booked' => 0,
        'status' => 'available',
    ]);
});
