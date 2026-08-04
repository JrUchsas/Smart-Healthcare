<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    /**
     * Display the Doctor Dashboard with real daily schedule & metrics.
     */
    public function index(Request $request): View
    {
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

        return view('doctor.dashboard', compact('doctor', 'stats', 'todayAppointments'));
    }
}
