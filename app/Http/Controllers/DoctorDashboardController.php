<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DoctorDashboardController extends Controller
{
    /**
     * Display the Doctor Dashboard with daily schedule & earnings summary.
     */
    public function index(Request $request): View
    {
        $doctor = $request->user()->doctor;

        $stats = [
            'today_appointments_count' => 0,
            'completed_appointments_count' => 0,
            'total_patients_treated' => 0,
            'monthly_earnings' => 0.00,
        ];

        return view('doctor.dashboard', compact('doctor', 'stats'));
    }
}
