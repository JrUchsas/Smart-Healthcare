<?php

namespace App\Services;

use App\Models\AppointmentSlot;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SlotGenerationService
{
    /**
     * Create a schedule block and automatically generate individual time slots.
     *
     * We calculate the time ranges by iterating from start_time to end_time
     * in step increments matching slot_duration_minutes. Each step produces
     * a discrete AppointmentSlot record ready for patient booking.
     *
     * @param  array{schedule_date: string, start_time: string, end_time: string, slot_duration_minutes: int}  $data
     */
    public function createScheduleWithSlots(Doctor $doctor, array $data): DoctorSchedule
    {
        return DB::transaction(function () use ($doctor, $data) {
            // 1. Create the parent schedule block
            $schedule = DoctorSchedule::create([
                'doctor_id' => $doctor->id,
                'schedule_date' => $data['schedule_date'],
                'start_time' => $data['start_time'],
                'end_time' => $data['end_time'],
                'slot_duration_minutes' => (int) $data['slot_duration_minutes'],
                'status' => 'active',
            ]);

            // 2. Parse times using Carbon for precise time arithmetic
            $date = $data['schedule_date'];
            $currentStart = Carbon::parse($date.' '.$data['start_time']);
            $scheduleEnd = Carbon::parse($date.' '.$data['end_time']);
            $duration = (int) $data['slot_duration_minutes'];

            $slotsToInsert = [];

            // 3. Incrementally generate non-overlapping time slots
            while ($currentStart->copy()->addMinutes($duration)->lte($scheduleEnd)) {
                $currentEnd = $currentStart->copy()->addMinutes($duration);

                $slotsToInsert[] = [
                    'doctor_schedule_id' => $schedule->id,
                    'doctor_id' => $doctor->id,
                    'start_time' => $currentStart->format('H:i:s'),
                    'end_time' => $currentEnd->format('H:i:s'),
                    'is_booked' => false,
                    'status' => 'available',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Advance start time to the end of the current slot
                $currentStart = $currentEnd;
            }

            if (! empty($slotsToInsert)) {
                AppointmentSlot::insert($slotsToInsert);
            }

            return $schedule;
        });
    }
}
