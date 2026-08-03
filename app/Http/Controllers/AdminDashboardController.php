<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Display the Admin Dashboard with summary metrics.
     */
    public function index(Request $request): View
    {
        // Placeholder stats for the Admin Dashboard overview
        $stats = [
            'total_doctors' => Doctor::count(),
            'pending_approvals' => Doctor::where('is_available', false)->count(),
            'total_patients' => Patient::count(),
            'total_appointments' => 0,
            'total_revenue' => 0.00,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
