<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use App\Services\DoctorScheduleService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    /**
     * Display the Doctor Dashboard with real daily schedule & metrics.
     */
    public function index(Request $request): View
    {
        // Auto-purge outdated schedules (< TODAY), refund money, and notify patients
        DoctorScheduleService::purgeOutdatedSchedules();

        $user = $request->user();
        $doctor = Doctor::firstOrCreate(['user_id' => $user->id]);

        $todayDate = now()->toDateString();

        // Completed appointments must not appear in the active schedule —
        // the doctor has already prescribed for them; the slot is done.
        $todayAppointments = Appointment::with(['patient.user', 'slot', 'prescription'])
            ->where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $todayDate)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('id', 'asc')
            ->get()
            // Re-sort in PHP by slot start_time so the schedule reads chronologically
            ->sortBy(fn ($apt) => optional($apt->slot)->start_time ?? '99:99:99')
            ->values();

        $stats = [
            'today_appointments_count' => Appointment::where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $todayDate)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'completed_appointments_count' => Appointment::where('doctor_id', $doctor->id)
                ->whereDate('appointment_date', $todayDate)
                ->where('status', 'completed')
                ->count(),
            'total_patients_treated' => Appointment::where('doctor_id', $doctor->id)
                ->distinct('patient_id')
                ->count('patient_id'),
            'monthly_earnings' => Appointment::where('doctor_id', $doctor->id)
                ->whereMonth('appointment_date', now()->month)
                ->whereYear('appointment_date', now()->year)
                ->where('status', '!=', 'cancelled')
                ->sum('consultation_fee'),
        ];

        $upcomingSchedules = $doctor->schedules()
            ->withCount([
                'slots as total_slots',
                'slots as booked_slots' => fn ($q) => $q->where('is_booked', true),
            ])
            ->where('schedule_date', '>=', $todayDate)
            ->orderBy('schedule_date', 'asc')
            ->take(4)
            ->get();

        $calendarEvents = $doctor->schedules()
            ->with('slots')
            ->where('schedule_date', '>=', now()->subMonths(1)->startOfMonth()->toDateString())
            ->get()
            ->map(function ($sched) {
                $totalSlots = $sched->slots->count();
                $bookedSlots = $sched->slots->where('is_booked', true)->count();
                $startTime = Carbon::parse($sched->start_time)->format('h:i A');
                $endTime = Carbon::parse($sched->end_time)->format('h:i A');

                return [
                    'id' => $sched->id,
                    'title' => "{$startTime}-{$endTime} ({$bookedSlots}/{$totalSlots} Booked)",
                    'start' => $sched->schedule_date->format('Y-m-d'),
                    'allDay' => true,
                    'backgroundColor' => $bookedSlots > 0 ? '#059669' : '#4f46e5',
                    'borderColor' => $bookedSlots > 0 ? '#047857' : '#4338ca',
                    'textColor' => '#ffffff',
                    'extendedProps' => [
                        'startTime' => $startTime,
                        'endTime' => $endTime,
                        'totalSlots' => $totalSlots,
                        'bookedSlots' => $bookedSlots,
                    ],
                ];
            });

        return view('doctor.dashboard', compact('doctor', 'stats', 'todayAppointments', 'upcomingSchedules', 'calendarEvents'));
    }
}
