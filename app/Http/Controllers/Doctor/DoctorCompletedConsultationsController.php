<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Doctor;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DoctorCompletedConsultationsController extends Controller
{
    /**
     * List all completed consultations for the logged-in doctor,
     * paginated and ordered newest-first.
     */
    public function index(Request $request): View
    {
        $doctor = Doctor::firstOrCreate(['user_id' => $request->user()->id]);

        $consultations = Appointment::with(['patient.user', 'prescription'])
            ->where('doctor_id', $doctor->id)
            ->where('status', 'completed')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('doctor.consultations.completed', compact('doctor', 'consultations'));
    }
}
