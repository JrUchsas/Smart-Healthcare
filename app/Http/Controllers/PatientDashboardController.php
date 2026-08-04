<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    /**
     * Display the Patient Dashboard with real upcoming appointments and metrics.
     */
    public function index(Request $request): View
    {
        $patient = Patient::firstOrCreate(['user_id' => $request->user()->id]);

        $upcomingAppointments = Appointment::with(['doctor.user', 'doctor.department', 'slot'])
            ->where('patient_id', $patient->id)
            ->whereIn('status', ['pending', 'confirmed'])
            ->orderBy('appointment_date', 'asc')
            ->take(5)
            ->get();

        $stats = [
            'upcoming_appointments_count' => Appointment::where('patient_id', $patient->id)
                ->whereIn('status', ['pending', 'confirmed'])
                ->count(),
            'completed_appointments_count' => Appointment::where('patient_id', $patient->id)
                ->where('status', 'completed')
                ->count(),
            'total_doctors_visited' => Appointment::where('patient_id', $patient->id)
                ->distinct('doctor_id')
                ->count('doctor_id'),
            'total_spent' => Appointment::where('patient_id', $patient->id)
                ->where('status', '!=', 'cancelled')
                ->sum('consultation_fee'),
        ];

        return view('patient.dashboard', compact('patient', 'stats', 'upcomingAppointments'));
    }
}
