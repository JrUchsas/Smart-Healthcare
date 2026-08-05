<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\DoctorSchedule;
use App\Models\Notification;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DoctorScheduleService
{
    /**
     * Purge outdated doctor schedules (schedule_date < TODAY).
     *
     * If an appointment was booked on an outdated slot and uncompleted (pending/confirmed):
     * 1. Cancel appointment with cancellation note explaining slot expiration and refund.
     * 2. If paid, update payment_status to 'refunded' and create refund ledger entry.
     * 3. Send Notification to the patient informing them that the slot expired and money was refunded.
     * 4. Log ActivityLog entry in audit trail.
     * 5. Dissociate appointment_slot_id and delete the outdated schedule and slots.
     */
    public static function purgeOutdatedSchedules(): int
    {
        $today = Carbon::today();
        $outdatedSchedules = DoctorSchedule::whereDate('schedule_date', '<', $today)->get();
        $purgedCount = 0;

        foreach ($outdatedSchedules as $schedule) {
            DB::transaction(function () use ($schedule, &$purgedCount) {
                // Find all appointments linked to slots in this schedule
                $slotIds = $schedule->slots()->pluck('id')->toArray();

                $appointments = ! empty($slotIds)
                    ? Appointment::whereIn('appointment_slot_id', $slotIds)->get()
                    : collect();

                foreach ($appointments as $apt) {
                    if (in_array($apt->status, ['pending', 'confirmed'])) {
                        $refundAmount = $apt->doctor?->consultation_fee ?? $apt->consultation_fee;
                        $fee = number_format($refundAmount, 2);

                        // 1. Cancel appointment & nullify slot reference before slot deletion
                        $apt->status = 'cancelled';
                        $apt->appointment_slot_id = null;

                        $isPaid = ($apt->payment_status === 'paid');

                        // 2. Refund payment if paid
                        if ($isPaid) {
                            $apt->payment_status = 'refunded';

                            Payment::create([
                                'payment_number' => 'REF-'.strtoupper(uniqid()),
                                'appointment_id' => $apt->id,
                                'patient_id' => $apt->patient_id,
                                'amount' => $refundAmount,
                                'payment_method' => 'refund',
                                'payment_status' => 'refunded',
                                'paid_at' => now(),
                            ]);
                        }

                        $apt->save();

                        // 3. Send Patient Notification
                        if ($apt->patient?->user_id) {
                            $title = $isPaid ? 'Appointment Slot Expired & Money Refunded' : 'Appointment Slot Expired';
                            $message = "Your appointment #{$apt->appointment_number} scheduled for {$schedule->schedule_date->format('d M Y')} has expired as the consultation date passed.";

                            if ($isPaid) {
                                $message .= " A full refund of ৳{$fee} has been credited back to your account.";
                            }

                            Notification::create([
                                'user_id' => $apt->patient->user_id,
                                'title' => $title,
                                'message' => $message,
                                'type' => $isPaid ? 'refund' : 'warning',
                                'is_read' => false,
                            ]);
                        }

                        // 4. Log Audit Activity
                        ActivityLog::log(
                            'Slot Expired'.($isPaid ? ' & Refunded' : ''),
                            "Appointment #{$apt->appointment_number} expired.".($isPaid ? " Full refund of ৳{$fee} issued to patient {$apt->patient?->user?->name}." : ''),
                            null,
                            'Appointments'
                        );
                    } else {
                        // Dissociate slot reference for completed/cancelled appointments so foreign keys don't fail
                        $apt->update(['appointment_slot_id' => null]);
                    }
                }

                // Delete associated slots and schedule
                $schedule->slots()->delete();
                $schedule->delete();
                $purgedCount++;
            });
        }

        return $purgedCount;
    }
}
