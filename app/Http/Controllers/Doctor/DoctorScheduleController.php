<?php

namespace App\Http\Controllers\Doctor;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDoctorScheduleRequest;
use App\Models\Doctor;
use App\Models\DoctorSchedule;
use App\Services\SlotGenerationService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DoctorScheduleController extends Controller
{
    public function __construct(
        protected SlotGenerationService $slotGenerationService
    ) {}

    /**
     * Display a listing of doctor schedule blocks and slots.
     */
    public function index(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('Admin') || $user->user_type === 'admin';
        $isDoctor = $user->hasRole('Doctor') || $user->user_type === 'doctor';

        if ($isAdmin || ! $isDoctor) {
            $schedules = DoctorSchedule::with(['doctor.user', 'slots' => function ($query) {
                $query->orderBy('start_time', 'asc');
            }])
                ->orderBy('schedule_date', 'desc')
                ->paginate(10);
        } else {
            $doctor = Doctor::firstOrCreate(['user_id' => $user->id]);

            $schedules = $doctor->schedules()
                ->with(['slots' => function ($query) {
                    $query->orderBy('start_time', 'asc');
                }])
                ->orderBy('schedule_date', 'desc')
                ->paginate(10);
        }

        return view('doctor.schedules.index', compact('schedules', 'isAdmin', 'isDoctor'));
    }

    /**
     * Show the form for creating a new schedule block.
     */
    public function create(Request $request): View
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('Admin') || $user->user_type === 'admin';
        $isDoctor = $user->hasRole('Doctor') || $user->user_type === 'doctor';

        if (! $isAdmin && ! $isDoctor) {
            abort(403, 'Patients are not authorized to create doctor schedule blocks.');
        }

        $doctors = $isAdmin ? Doctor::with('user')->get() : collect();

        return view('doctor.schedules.create', compact('isAdmin', 'doctors'));
    }

    /**
     * Store a new schedule block and auto-generate appointment slots using SlotGenerationService.
     */
    public function store(StoreDoctorScheduleRequest $request): RedirectResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('Admin') || $user->user_type === 'admin';

        if ($isAdmin && $request->filled('doctor_id')) {
            $doctor = Doctor::findOrFail($request->input('doctor_id'));
        } else {
            $doctor = Doctor::firstOrCreate(['user_id' => $user->id]);
        }

        $this->slotGenerationService->createScheduleWithSlots($doctor, $request->validated());

        return redirect()->route('doctor.schedules.index')
            ->with('success', 'Doctor schedule block and time slots generated successfully.');
    }

    /**
     * Remove the specified schedule block and its associated slots.
     */
    public function destroy(DoctorSchedule $schedule, Request $request): JsonResponse|RedirectResponse
    {
        $user = $request->user();
        $isAdmin = $user->hasRole('Admin') || $user->user_type === 'admin';
        $isDoctor = $user->hasRole('Doctor') || $user->user_type === 'doctor';

        if (! $isAdmin && ! $isDoctor) {
            abort(403, 'Patients are not authorized to delete doctor schedule blocks.');
        }

        if ($isDoctor && ! $isAdmin) {
            $doctor = Doctor::firstOrCreate(['user_id' => $user->id]);

            if ($schedule->doctor_id !== $doctor->id) {
                abort(403, 'Unauthorized access to doctor schedule.');
            }
        }

        $schedule->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Schedule block and generated time slots deleted successfully.',
            ]);
        }

        return redirect()->route('doctor.schedules.index')
            ->with('success', 'Schedule block deleted successfully.');
    }
}
