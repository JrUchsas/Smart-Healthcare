<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Doctor;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DoctorSearchController extends Controller
{
    /**
     * Display dynamic doctor search page and handle AJAX filtering.
     */
    public function index(Request $request): View|JsonResponse
    {
        $query = Doctor::query()
            ->with(['user', 'department', 'specialization'])
            ->where('is_available', true);

        // Filter by text query (doctor name, qualification, or bio)
        if ($request->filled('q')) {
            $search = $request->input('q');
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', function ($u) use ($search) {
                    $u->where('name', 'like', "%{$search}%");
                })
                    ->orWhere('qualification', 'like', "%{$search}%")
                    ->orWhere('bio', 'like', "%{$search}%");
            });
        }

        // Filter by Department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->input('department_id'));
        }

        $doctors = $query->paginate(9);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'doctors' => $doctors,
            ]);
        }

        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('patient.doctors.search', compact('doctors', 'departments'));
    }

    /**
     * Fetch available appointment slots for a specific doctor via AJAX.
     */
    public function getSlots(Doctor $doctor, Request $request): JsonResponse
    {
        $slotsQuery = $doctor->appointmentSlots()
            ->with('schedule')
            ->where('is_booked', false)
            ->where('status', 'available');

        // Optional date filter
        if ($request->filled('date')) {
            $date = $request->input('date');
            $slotsQuery->whereHas('schedule', function ($q) use ($date) {
                $q->whereDate('schedule_date', $date);
            });
        }

        $slots = $slotsQuery->get()->map(function ($slot) {
            return [
                'id' => $slot->id,
                'schedule_date' => $slot->schedule ? $slot->schedule->schedule_date->format('Y-m-d') : null,
                'schedule_date_formatted' => $slot->schedule ? $slot->schedule->schedule_date->format('l, M j, Y') : 'N/A',
                'start_time_formatted' => Carbon::parse($slot->start_time)->format('h:i A'),
                'end_time_formatted' => Carbon::parse($slot->end_time)->format('h:i A'),
            ];
        });

        return response()->json([
            'success' => true,
            'doctor_name' => $doctor->user?->name,
            'slots' => $slots,
        ]);
    }
}
