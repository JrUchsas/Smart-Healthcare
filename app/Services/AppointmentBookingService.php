<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Patient;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AppointmentBookingService
{
    /**
     * Book an appointment safely inside a database transaction.
     *
     * We issue a lockForUpdate() query on the target appointment slot row
     * to prevent race conditions (double-booking) when multiple patients
     * attempt to select the same time slot concurrently.
     *
     * @param  array{appointment_slot_id: int, patient_notes?: string|null}  $data
     *
     * @throws Exception
     */
    public function bookAppointment(Patient $patient, array $data): Appointment
    {
        return DB::transaction(function () use ($patient, $data) {
            // 1. Lock the appointment slot row for update to prevent concurrent double-booking
            $slot = AppointmentSlot::where('id', $data['appointment_slot_id'])
                ->lockForUpdate()
                ->firstOrFail();

            // 2. Validate slot availability
            if ($slot->is_booked || $slot->status !== 'available') {
                throw new Exception('The selected appointment time slot has already been booked by another patient.');
            }

            // 3. Mark the slot as booked
            $slot->update([
                'is_booked' => true,
                'status' => 'booked',
            ]);

            $schedule = $slot->schedule;
            $doctor = $slot->doctor;

            // 4. Generate a unique appointment reference number
            $appointmentNumber = 'APT-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));

            // 5. Create the pending appointment record
            return Appointment::create([
                'appointment_number' => $appointmentNumber,
                'patient_id' => $patient->id,
                'doctor_id' => $doctor->id,
                'appointment_slot_id' => $slot->id,
                'appointment_date' => $schedule ? $schedule->schedule_date : now()->toDateString(),
                'consultation_fee' => $doctor->consultation_fee ?? 50.00,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'patient_notes' => $data['patient_notes'] ?? null,
            ]);
        });
    }
}
