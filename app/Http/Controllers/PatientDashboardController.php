<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PatientDashboardController extends Controller
{
    /**
     * Display the Patient Dashboard with appointments & medical history summary.
     */
    public function index(Request $request): View
    {
        $patient = $request->user()->patient;

        $stats = [
            'upcoming_appointments_count' => 0,
            'prescriptions_count' => 0,
            'invoices_count' => 0,
            'total_spent' => 0.00,
        ];

        return view('patient.dashboard', compact('patient', 'stats'));
    }
}
