<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreSpecializationRequest;
use App\Http\Requests\UpdateSpecializationRequest;
use App\Models\Department;
use App\Models\Specialization;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class SpecializationController extends Controller
{
    /**
     * Display a listing of specializations via Yajra DataTables with eager loaded department.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            // Eager load parent department to eliminate N+1 queries
            $specializations = Specialization::query()
                ->with(['department:id,name'])
                ->select(['id', 'department_id', 'name', 'slug', 'description', 'is_active', 'created_at']);

            return DataTables::of($specializations)
                ->addColumn('department_name', function (Specialization $specialization) {
                    return $specialization->department ? e($specialization->department->name) : '<span class="text-muted">Unassigned</span>';
                })
                ->addColumn('status', function (Specialization $specialization) {
                    return $specialization->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('actions', function (Specialization $specialization) {
                    $editUrl = route('admin.specializations.edit', $specialization);
                    $deleteUrl = route('admin.specializations.destroy', $specialization);

                    return '
                        <a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i> Edit</a>
                        <button type="button" data-url="'.$deleteUrl.'" class="btn btn-sm btn-outline-danger delete-btn"><i class="bi bi-trash"></i> Delete</button>
                    ';
                })
                ->rawColumns(['department_name', 'status', 'actions'])
                ->make(true);
        }

        return view('admin.specializations.index');
    }

    /**
     * Show the form for creating a new specialization.
     */
    public function create(): View
    {
        $departments = Department::where('is_active', true)->orderBy('name')->get();

        return view('admin.specializations.create', compact('departments'));
    }

    /**
     * Store a newly created specialization in storage.
     */
    public function store(StoreSpecializationRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Specialization::create($validated);

        return redirect()->route('admin.specializations.index')
            ->with('success', 'Medical specialization created successfully.');
    }

    /**
     * Show the form for editing the specified specialization.
     */
    public function edit(Specialization $specialization): View
    {
        $departments = Department::orderBy('name')->get();

        return view('admin.specializations.edit', compact('specialization', 'departments'));
    }

    /**
     * Update the specified specialization in storage.
     */
    public function update(UpdateSpecializationRequest $request, Specialization $specialization): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $specialization->update($validated);

        return redirect()->route('admin.specializations.index')
            ->with('success', 'Medical specialization updated successfully.');
    }

    /**
     * Remove the specified specialization from storage.
     */
    public function destroy(Specialization $specialization, Request $request): JsonResponse|RedirectResponse
    {
        $specialization->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Specialization deleted successfully.']);
        }

        return redirect()->route('admin.specializations.index')
            ->with('success', 'Specialization deleted successfully.');
    }
}
