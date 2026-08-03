<?php

use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\SpecializationController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\DoctorDashboardController;
use App\Http\Controllers\PatientDashboardController;
use App\Http\Controllers\ProfileController;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Central Smart Redirect Based on User Role
Route::get('/dashboard', function (Request $request) {
    $user = $request->user();

    if ($user->hasRole('Admin') || $user->user_type === 'admin') {
        if (! $user->hasRole('Admin')) {
            $user->assignRole('Admin');
        }

        return redirect()->route('admin.dashboard');
    }

    if ($user->hasRole('Doctor') || $user->user_type === 'doctor') {
        if (! $user->hasRole('Doctor')) {
            $user->assignRole('Doctor');
        }

        return redirect()->route('doctor.dashboard');
    }

    // Default to Patient role
    if (! $user->hasRole('Patient')) {
        $user->assignRole('Patient');
    }

    if (! $user->patient) {
        Patient::firstOrCreate(['user_id' => $user->id]);
    }

    return redirect()->route('patient.dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Admin Routes Group
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('departments', DepartmentController::class);
    Route::resource('specializations', SpecializationController::class);
});

// Doctor Routes Group
Route::middleware(['auth', 'role:Doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');
});

// Patient Routes Group
Route::middleware(['auth', 'role:Patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
