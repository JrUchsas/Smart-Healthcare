<?php

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Models\Notification;
use App\Models\Patient;
use App\Models\User;
use App\Services\DoctorScheduleService;
use Carbon\Carbon;
use Database\Seeders\RoleAndPermissionSeeder;

beforeEach(function () {
    $this->seed(RoleAndPermissionSeeder::class);

    $this->doctorUser = User::factory()->create(['user_type' => 'doctor']);
    $this->doctorUser->assignRole('Doctor');
    $this->doctor = Doctor::create([
        'user_id' => $this->doctorUser->id,
        'consultation_fee' => 150.00,
        'experience_years' => 5,
        'qualification' => 'MBBS',
        'is_available' => true,
    ]);

    $this->patientUser = User::factory()->create(['user_type' => 'patient']);
    $this->patientUser->assignRole('Patient');
    $this->patient = Patient::firstOrCreate(['user_id' => $this->patientUser->id]);
});

test('outdated schedules are automatically deleted and booked slots refunded with notification', function () {
    $yesterday = Carbon::yesterday();

    // Create an outdated schedule block for yesterday
    $schedule = DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'schedule_date' => $yesterday,
        'start_time' => '09:00:00',
        'end_time' => '12:00:00',
        'slot_duration_minutes' => 30,
        'status' => 'active',
    ]);

    $slot = AppointmentSlot::create([
        'doctor_id' => $this->doctor->id,
        'doctor_schedule_id' => $schedule->id,
        'start_time' => '09:00:00',
        'end_time' => '09:30:00',
        'is_booked' => true,
    ]);

    $appointment = Appointment::create([
        'appointment_number' => 'APT-EXP-001',
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'doctor_schedule_id' => $schedule->id,
        'appointment_slot_id' => $slot->id,
        'appointment_date' => $yesterday,
        'consultation_fee' => 150.00,
        'status' => 'confirmed',
        'payment_status' => 'paid',
    ]);

    // Run purge
    $purged = DoctorScheduleService::purgeOutdatedSchedules();

    expect($purged)->toBeGreaterThan(0);

    // Verify schedule is deleted
    $this->assertDatabaseMissing('doctor_schedules', ['id' => $schedule->id]);

    // Verify appointment status updated to cancelled & refunded
    $appointment->refresh();
    expect($appointment->status)->toBe('cancelled');
    expect($appointment->payment_status)->toBe('refunded');

    // Verify Notification created for patient
    $notification = Notification::where('user_id', $this->patientUser->id)->first();
    expect($notification)->not->toBeNull();
    expect($notification->title)->toContain('Expired & Money Refunded');
    expect($notification->message)->toContain('A full refund of');
});

test('outdated unpaid schedule expiration sends notification without refund text', function () {
    $yesterday = Carbon::yesterday();

    $schedule = DoctorSchedule::create([
        'doctor_id' => $this->doctor->id,
        'schedule_date' => $yesterday,
        'start_time' => '14:00:00',
        'end_time' => '17:00:00',
        'slot_duration_minutes' => 30,
        'status' => 'active',
    ]);

    $slot = AppointmentSlot::create([
        'doctor_id' => $this->doctor->id,
        'doctor_schedule_id' => $schedule->id,
        'start_time' => '14:00:00',
        'end_time' => '14:30:00',
        'is_booked' => true,
    ]);

    $appointment = Appointment::create([
        'appointment_number' => 'APT-UNPAID-001',
        'patient_id' => $this->patient->id,
        'doctor_id' => $this->doctor->id,
        'doctor_schedule_id' => $schedule->id,
        'appointment_slot_id' => $slot->id,
        'appointment_date' => $yesterday,
        'consultation_fee' => 150.00,
        'status' => 'pending',
        'payment_status' => 'unpaid',
    ]);

    DoctorScheduleService::purgeOutdatedSchedules();

    $notification = Notification::where('user_id', $this->patientUser->id)
        ->where('title', 'Appointment Slot Expired')
        ->first();

    expect($notification)->not->toBeNull();
    expect($notification->message)->not->toContain('refund');
});
