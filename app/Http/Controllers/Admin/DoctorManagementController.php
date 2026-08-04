<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateDoctorProfileRequest;
use App\Models\ActivityLog;
use App\Models\Department;
use App\Models\Doctor;
use App\Models\Specialization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DoctorManagementController extends Controller
{
    /**
     * Display a listing of doctors for Admin approval and department assignment.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $doctors = Doctor::query()
                ->with(['user:id,name,email,is_active', 'department:id,name', 'specialization:id,name'])
                ->select([
                    'doctors.id',
                    'doctors.user_id',
                    'doctors.department_id',
                    'doctors.specialization_id',
                    'doctors.qualification',
                    'doctors.experience_years',
                    'doctors.consultation_fee',
                    'doctors.is_available',
                    'doctors.created_at',
                ]);

            return DataTables::of($doctors)
                ->addColumn('doctor_name', function (Doctor $doctor) {
                    $name = e($doctor->user?->name ?? 'N/A');
                    $email = e($doctor->user?->email ?? '');

                    return '<div><strong class="text-dark">'.$name.'</strong><br><small class="text-muted">'.$email.'</small></div>';
                })
                ->addColumn('department_name', function (Doctor $doctor) {
                    return $doctor->department ? e($doctor->department->name) : '<span class="badge bg-warning text-dark">Unassigned</span>';
                })
                ->addColumn('specialization_name', function (Doctor $doctor) {
                    return $doctor->specialization ? e($doctor->specialization->name) : '<span class="text-muted">N/A</span>';
                })
                ->addColumn('fee', function (Doctor $doctor) {
                    return '$'.number_format($doctor->consultation_fee, 2);
                })
                ->addColumn('status', function (Doctor $doctor) {
                    return $doctor->is_available
                        ? '<span class="badge bg-success">Available</span>'
                        : '<span class="badge bg-danger">Unavailable</span>';
                })
                ->addColumn('actions', function (Doctor $doctor) {
                    $editUrl = route('admin.doctors.edit', $doctor);

                    return '<a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil-square me-1"></i> Edit / Assign</a>';
                })
                ->rawColumns(['doctor_name', 'department_name', 'specialization_name', 'status', 'actions'])
                ->make(true);
        }

        return view('admin.doctors.index');
    }

    /**
     * Show form for assigning department, specialization, and details to a doctor.
     */
    public function edit(Doctor $doctor): View
    {
        $doctor->load(['user', 'department', 'specialization']);

        $departments = Department::where('is_active', true)->orderBy('name')->get();
        $specializations = Specialization::where('is_active', true)
            ->when($doctor->department_id, function ($q) use ($doctor) {
                $q->where('department_id', $doctor->department_id);
            })
            ->orderBy('name')
            ->get();

        return view('admin.doctors.edit', compact('doctor', 'departments', 'specializations'));
    }

    /**
     * Update doctor's department, specialization, and professional profile.
     */
    public function update(UpdateDoctorProfileRequest $request, Doctor $doctor): RedirectResponse
    {
        $validated = $request->validated();
        $validated['is_available'] = $request->boolean('is_available', true);

        $doctor->update($validated);
        $doctor->user?->update(['is_active' => $validated['is_available']]);

        ActivityLog::log('Doctor Profile Updated', "Admin updated profile and availability status for Dr. {$doctor->user?->name}.", null, 'Doctor Management');

        return redirect()->route('admin.doctors.index')
            ->with('success', 'Doctor profile, department, and specialization updated successfully.');
    }

    /**
     * Fetch specializations dynamically by department ID for AJAX cascading dropdowns.
     */
    public function getSpecializations(Department $department): JsonResponse
    {
        $specializations = $department->specializations()
            ->where('is_active', true)
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();

        return response()->json([
            'success' => true,
            'specializations' => $specializations,
        ]);
    }
}
