<?php

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->patientUser = User::factory()->create(['user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
    $this->patient = Patient::create(['user_id' => $this->patientUser->id]);

    $this->dept = Department::create(['name' => 'Cardiology', 'slug' => 'cardiology', 'is_active' => true]);
    $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'department_id' => $this->dept->id,
        'consultation_fee' => 150.00,
        'is_available' => true,
    ]);

    $this->schedule = DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'schedule_date' => now()->addDays(2)->toDateString(),
        'start_time' => '09:00:00',
        'end_time' => '10:00:00',
        'slot_duration_minutes' => 30,
    ]);

    $this->slot = AppointmentSlot::create([
        'doctor_schedule_id' => $this->schedule->id,
        'doctor_id' => $this->doctor->id,
        'start_time' => '09:00:00',
        'end_time' => '09:30:00',
        'is_booked' => true,
        'status' => 'booked',
    ]);

    $this->appointment = Appointment::create([
        'appointment_number' => 'APT-20260804-999',
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'appointment_slot_id' => $this->slot->id,
        'appointment_date' => $this->schedule->schedule_date,
        'consultation_fee' => 150.00,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);
});

test('patient can view checkout page for unpaid appointment', function () {
    $response = $this->actingAs($this->patientUser)->get(route('patient.payments.checkout', $this->appointment));

    $response->assertStatus(200);
    $response->assertSee('150.00');
});

test('payment processing creates payment, transaction, invoice, and confirms appointment atomically', function () {
    $response = $this->actingAs($this->patientUser)->post(route('patient.payments.process'), [
        'appointment_id' => $this->appointment->id,
        'payment_method' => 'credit_card',
        'card_holder_name' => 'Jane Doe',
        'card_number' => '4242 4242 4242 4242',
        'card_expiry' => '12/28',
        'card_cvv' => '123',
    ]);

    $invoice = Invoice::where('appointment_id', $this->appointment->id)->first();
    expect($invoice)->not->toBeNull();

    $response->assertRedirect(route('patient.invoices.show', $invoice));

    // Verify appointment status updated
    $this->assertDatabaseHas('appointments', [
        'id' => $this->appointment->id,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    // Verify Payment entry created
    $this->assertDatabaseHas('payments', [
        'appointment_id' => $this->appointment->id,
        'patient_id' => $this->patient->id,
        'amount' => 150.00,
        'payment_status' => 'completed',
    ]);

    // Verify Transaction entry created
    $this->assertDatabaseHas('transactions', [
        'appointment_id' => $this->appointment->id,
        'amount' => 150.00,
        'status' => 'success',
    ]);

    // Verify Invoice entry created
    $this->assertDatabaseHas('invoices', [
        'appointment_id' => $this->appointment->id,
        'patient_id' => $this->patient->id,
        'total_amount' => 150.00,
        'status' => 'paid',
    ]);
});

test('patient can view official invoice after successful payment', function () {
    $payment = Payment::create([
        'payment_number' => 'PAY-TEST-001',
        'appointment_id' => $this->appointment->id,
        'patient_id' => $this->patient->id,
        'amount' => 150.00,
        'payment_method' => 'credit_card',
        'payment_status' => 'completed',
        'paid_at' => now(),
    ]);

    $invoice = Invoice::create([
        'invoice_number' => 'INV-TEST-001',
        'appointment_id' => $this->appointment->id,
        'patient_id' => $this->patient->id,
        'payment_id' => $payment->id,
        'subtotal_amount' => 150.00,
        'tax_amount' => 0.00,
        'total_amount' => 150.00,
        'status' => 'paid',
        'issued_at' => now(),
    ]);

    $response = $this->actingAs($this->patientUser)->get(route('patient.invoices.show', $invoice));

    $response->assertStatus(200);
    $response->assertSee('INV-TEST-001');
    $response->assertSee('150.00');
});
