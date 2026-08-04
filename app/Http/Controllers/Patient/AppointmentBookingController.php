<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Models\Doctor;
use App\Models\Patient;
use App\Services\AppointmentBookingService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class AppointmentBookingController extends Controller
{
    public function __construct(
        protected AppointmentBookingService $bookingService
    ) {}

    /**
     * Show appointment booking page for a selected doctor.
     */
    public function create(Doctor $doctor): View
    {
        $doctor->load(['user', 'department', 'specialization']);

        $availableSlots = $doctor->appointmentSlots()
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

        return view('patient.appointments.create', compact('doctor', 'availableSlots'));
    }

    /**
     * Store a newly booked appointment inside a database transaction via AppointmentBookingService.
     */
    public function store(StoreAppointmentRequest $request): JsonResponse|RedirectResponse
    {
        $patient = Patient::firstOrCreate(['user_id' => $request->user()->id]);

        try {
            $appointment = $this->bookingService->bookAppointment($patient, $request->validated());

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Appointment booked successfully! Reference #: '.$appointment->appointment_number,
                    'appointment_number' => $appointment->appointment_number,
                    'redirect_url' => route('patient.dashboard'),
                ]);
            }

            return redirect()->route('patient.dashboard')
                ->with('success', 'Appointment booked successfully! Reference #: '.$appointment->appointment_number);
        } catch (Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                ], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }
}
