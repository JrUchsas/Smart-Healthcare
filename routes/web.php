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

use App\Http\Controllers\Admin\AdminAppointmentController;
use App\Http\Controllers\Admin\AdminRevenueReportController;
use App\Http\Controllers\Admin\DoctorManagementController;

// Admin Routes Group
Route::middleware(['auth', 'role:Admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::resource('departments', DepartmentController::class);
    Route::resource('specializations', SpecializationController::class);
    Route::resource('doctors', DoctorManagementController::class)->only(['index', 'edit', 'update']);
    Route::get('/departments/{department}/specializations', [DoctorManagementController::class, 'getSpecializations'])->name('departments.specializations');

    // Appointment History Routes
    Route::get('/appointments', [AdminAppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AdminAppointmentController::class, 'show'])->name('appointments.show');

    // Revenue & Financial Reports Route
    Route::get('/reports/revenue', [AdminRevenueReportController::class, 'index'])->name('reports.revenue');
});

use App\Http\Controllers\Doctor\DoctorScheduleController;

// Doctor Schedules Routes (Accessible to Admin, Doctor, and Patient)
Route::middleware(['auth', 'role:Admin|Doctor|Patient'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::resource('schedules', DoctorScheduleController::class);
});

use App\Http\Controllers\Doctor\DoctorCompletedConsultationsController;
use App\Http\Controllers\PrescriptionController;

// Doctor Routes Group
Route::middleware(['auth', 'role:Doctor'])->prefix('doctor')->name('doctor.')->group(function () {
    Route::get('/dashboard', [DoctorDashboardController::class, 'index'])->name('dashboard');

    // Prescription builder
    Route::get('/appointments/{appointment}/prescribe', [PrescriptionController::class, 'create'])->name('prescriptions.create');
    Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
    Route::get('/prescriptions/{prescription}', [PrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::get('/prescriptions/{prescription}/pdf', [PrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::get('/prescriptions/{prescription}/download', [PrescriptionController::class, 'download'])->name('prescriptions.download');

    // Completed consultations history
    Route::get('/completed-consultations', [DoctorCompletedConsultationsController::class, 'index'])->name('consultations.completed');
});

use App\Http\Controllers\Patient\AppointmentBookingController;
use App\Http\Controllers\Patient\DoctorSearchController;
use App\Http\Controllers\Patient\PatientAppointmentController;
use App\Http\Controllers\Patient\PatientPrescriptionController;
use App\Http\Controllers\Patient\PaymentController;

// Patient Routes Group
Route::middleware(['auth', 'role:Patient'])->prefix('patient')->name('patient.')->group(function () {
    Route::get('/dashboard', [PatientDashboardController::class, 'index'])->name('dashboard');
    Route::get('/doctors/search', [DoctorSearchController::class, 'index'])->name('doctors.search');
    Route::get('/doctors/{doctor}/slots', [DoctorSearchController::class, 'getSlots'])->name('doctors.slots');
    Route::get('/doctors/{doctor}/book', [AppointmentBookingController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [AppointmentBookingController::class, 'store'])->name('appointments.store');
    Route::resource('my-appointments', PatientAppointmentController::class)->except(['create', 'store'])->parameters(['my-appointments' => 'appointment'])->names([
        'index' => 'appointments.index',
        'edit' => 'appointments.edit',
        'update' => 'appointments.update',
        'destroy' => 'appointments.destroy',
    ]);
    Route::get('/appointments/{appointment}/checkout', [PaymentController::class, 'checkout'])->name('payments.checkout');
    Route::post('/payments/process', [PaymentController::class, 'process'])->name('payments.process');
    Route::get('/invoices/{invoice}', [PaymentController::class, 'showInvoice'])->name('invoices.show');

    // Patient prescriptions — view list, view details, stream/download PDF
    Route::get('/prescriptions', [PatientPrescriptionController::class, 'index'])->name('prescriptions.index');
    Route::get('/prescriptions/{prescription}', [PatientPrescriptionController::class, 'show'])->name('prescriptions.show');
    Route::get('/prescriptions/{prescription}/pdf', [PatientPrescriptionController::class, 'pdf'])->name('prescriptions.pdf');
    Route::get('/prescriptions/{prescription}/download', [PatientPrescriptionController::class, 'download'])->name('prescriptions.download');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
