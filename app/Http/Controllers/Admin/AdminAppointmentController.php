<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminAppointmentController extends Controller
{
    /**
     * Display a listing of all appointments for admin review with status & search filtering.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status');
        $search = $request->query('search');

        $query = Appointment::with([
            'patient.user',
            'doctor.user',
            'doctor.department',
            'slot',
            'payment',
            'prescription',
        ]);

        if (! empty($status) && in_array($status, ['pending', 'confirmed', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('appointment_number', 'like', "%{$search}%")
                    ->orWhereHas('patient.user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"))
                    ->orWhereHas('doctor.user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $appointments = $query->orderBy('appointment_date', 'asc')
            ->orderBy('id', 'asc')
            ->paginate(15)
            ->withQueryString();

        $stats = [
            'total' => Appointment::count(),
            'pending' => Appointment::where('status', 'pending')->count(),
            'confirmed' => Appointment::where('status', 'confirmed')->count(),
            'completed' => Appointment::where('status', 'completed')->count(),
            'cancelled' => Appointment::where('status', 'cancelled')->count(),
        ];

        return view('admin.appointments.index', compact('appointments', 'stats', 'status', 'search'));
    }

    /**
     * Display full detailed overview of a single appointment.
     */
    public function show(Appointment $appointment): View
    {
        $appointment->load([
            'patient.user',
            'doctor.user',
            'doctor.department',
            'doctor.specialization',
            'slot.schedule',
            'payment',
            'invoice',
            'prescription.items',
        ]);

        return view('admin.appointments.show', compact('appointment'));
    }
}
