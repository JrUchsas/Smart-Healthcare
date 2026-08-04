<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard with real summary metrics.
     */
    public function index(Request $request): View
    {
        $stats = [
            'total_departments' => Department::count(),
            'total_doctors' => Doctor::count(),
            'pending_approvals' => Doctor::where('is_available', false)->count(),
            'total_patients' => Patient::count(),
            'total_appointments' => Appointment::count(),
            'total_revenue' => Appointment::where('status', '!=', 'cancelled')->sum('consultation_fee'),
        ];

        $pendingDoctors = Doctor::with('user')
            ->where('is_available', false)
            ->latest()
            ->take(5)
            ->get();

        $recentAppointments = Appointment::with(['patient.user', 'doctor.user', 'slot'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'pendingDoctors', 'recentAppointments'));
    }
}
