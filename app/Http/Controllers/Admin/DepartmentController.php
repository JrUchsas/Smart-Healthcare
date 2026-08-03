<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDepartmentRequest;
use App\Http\Requests\UpdateDepartmentRequest;
use App\Models\Department;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class DepartmentController extends Controller
{
    /**
     * Display a listing of departments via Yajra DataTables.
     */
    public function index(Request $request): View|JsonResponse
    {
        if ($request->ajax()) {
            $departments = Department::query()->select(['id', 'name', 'slug', 'description', 'is_active', 'created_at']);

            return DataTables::of($departments)
                ->addColumn('status', function (Department $department) {
                    return $department->is_active
                        ? '<span class="badge bg-success">Active</span>'
                        : '<span class="badge bg-danger">Inactive</span>';
                })
                ->addColumn('actions', function (Department $department) {
                    $editUrl = route('admin.departments.edit', $department);
                    $deleteUrl = route('admin.departments.destroy', $department);

                    return '
                        <a href="'.$editUrl.'" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-pencil"></i> Edit</a>
                        <button type="button" data-url="'.$deleteUrl.'" class="btn btn-sm btn-outline-danger delete-btn"><i class="bi bi-trash"></i> Delete</button>
                    ';
                })
                ->rawColumns(['status', 'actions'])
                ->make(true);
        }

        return view('admin.departments.index');
    }

    /**
     * Show the form for creating a new department.
     */
    public function create(): View
    {
        return view('admin.departments.create');
    }

    /**
     * Store a newly created department in storage.
     */
    public function store(StoreDepartmentRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active', true);

        Department::create($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Medical department created successfully.');
    }

    /**
     * Show the form for editing the specified department.
     */
    public function edit(Department $department): View
    {
        return view('admin.departments.edit', compact('department'));
    }

    /**
     * Update the specified department in storage.
     */
    public function update(UpdateDepartmentRequest $request, Department $department): RedirectResponse
    {
        $validated = $request->validated();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['is_active'] = $request->boolean('is_active');

        $department->update($validated);

        return redirect()->route('admin.departments.index')
            ->with('success', 'Medical department updated successfully.');
    }

    /**
     * Remove the specified department from storage.
     */
    public function destroy(Department $department, Request $request): JsonResponse|RedirectResponse
    {
        $department->delete();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Department deleted successfully.']);
        }

        return redirect()->route('admin.departments.index')
            ->with('success', 'Department deleted successfully.');
    }
}
