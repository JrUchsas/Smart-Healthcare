<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\AppointmentSlot;
use App\Models\Patient;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientAppointmentController extends Controller
{
    /**
     * Display listing of booked appointments for the logged-in patient.
     */
    public function index(Request $request): View
    {
        $patient = Patient::firstOrCreate(['user_id' => $request->user()->id]);
        $type = $request->query('type', 'booked');

        if ($type === 'completed') {
            $pageTitle = 'Completed Appointments';
            $appointments = $patient->appointments()
                ->with(['doctor.user', 'doctor.department', 'prescription'])
                ->where('status', 'completed')
                ->orderBy('appointment_date', 'desc')
                ->orderBy('id', 'desc')
                ->paginate(10);
        } else {
            $type = 'booked';
            $pageTitle = 'Booked Appointments';
            $appointments = $patient->appointments()
                ->with(['doctor.user', 'doctor.department', 'doctor.specialization', 'slot', 'invoice', 'prescription'])
                ->whereIn('status', ['pending', 'confirmed'])
                ->orderBy('appointment_date', 'asc')
                ->orderBy('id', 'asc')
                ->paginate(10);
        }

        return view('patient.appointments.index', compact('appointments', 'pageTitle', 'type'));
    }

    /**
     * Show form for editing appointment notes or rescheduling time slot.
     */
    public function edit(Appointment $appointment, Request $request): View
    {
        $this->authorizeAppointmentOwnership($appointment, $request->user());

        $appointment->load(['doctor.user', 'doctor.department', 'slot']);

        // Fetch open slots for doctor if patient wants to reschedule
        $availableSlots = $appointment->doctor->appointmentSlots()
            ->with('schedule')
            ->where('is_booked', false)
            ->where('status', 'available')
            ->get()
            ->sortBy([
                fn ($a, $b) => optional($a->schedule)->schedule_date <=> optional($b->schedule)->schedule_date,
                fn ($a, $b) => $a->start_time <=> $b->start_time,
            ])
            ->groupBy(function ($slot) {
                return $slot->schedule ? $slot->schedule->schedule_date->format('Y-m-d') : 'Other';
            })
            ->sortKeys();

        return view('patient.appointments.edit', compact('appointment', 'availableSlots'));
    }

    /**
     * Update appointment notes or reschedule to a new time slot.
     */
    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $this->authorizeAppointmentOwnership($appointment, $request->user());

        $request->validate([
            'patient_notes' => ['nullable', 'string', 'max:1000'],
            'appointment_slot_id' => ['nullable', 'exists:appointment_slots,id'],
        ]);

        try {
            DB::transaction(function () use ($request, $appointment) {
                // If a new slot is chosen (rescheduling)
                if ($request->filled('appointment_slot_id') && (int) $request->input('appointment_slot_id') !== (int) $appointment->appointment_slot_id) {
                    $newSlotId = (int) $request->input('appointment_slot_id');

                    // 1. Release previous slot back to available
                    if ($appointment->slot) {
                        $appointment->slot->update([
                            'is_booked' => false,
                            'status' => 'available',
                        ]);
                    }

                    // 2. Lock & book new slot
                    $newSlot = AppointmentSlot::where('id', $newSlotId)->lockForUpdate()->firstOrFail();

                    if ($newSlot->is_booked || $newSlot->status !== 'available') {
                        throw new Exception('The selected new time slot is no longer available.');
                    }

                    $newSlot->update([
                        'is_booked' => true,
                        'status' => 'booked',
                    ]);

                    $appointment->appointment_slot_id = $newSlot->id;
                    $appointment->appointment_date = $newSlot->schedule ? $newSlot->schedule->schedule_date : $appointment->appointment_date;
                }

                $appointment->patient_notes = $request->input('patient_notes');
                $appointment->save();
            });

            return redirect()->route('patient.appointments.index')
                ->with('success', 'Appointment updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Cancel/delete appointment and release the time slot back to available on doctor's schedule.
     */
    public function destroy(Appointment $appointment, Request $request): JsonResponse|RedirectResponse
    {
        $this->authorizeAppointmentOwnership($appointment, $request->user());

        DB::transaction(function () use ($appointment) {
            // Revert previous slot back to available on doctor's schedule
            if ($appointment->slot) {
                $appointment->slot->update([
                    'is_booked' => false,
                    'status' => 'available',
                ]);
            }

            // Delete the appointment record
            $appointment->delete();
        });

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Appointment cancelled and time slot released successfully.',
            ]);
        }

        return redirect()->route('patient.appointments.index')
            ->with('success', 'Appointment cancelled successfully.');
    }

    /**
     * Verify that the appointment belongs to the logged-in patient or Admin.
     */
    protected function authorizeAppointmentOwnership(Appointment $appointment, $user): void
    {
        // Admin role can manage any appointment
        if ($user->hasRole('Admin') || $user->user_type === 'admin') {
            return;
        }

        $patient = Patient::firstOrCreate(['user_id' => $user->id]);

        if ((int) $appointment->patient_id !== (int) $patient->id) {
            abort(403, 'Unauthorized access to appointment details.');
        }
    }
}
