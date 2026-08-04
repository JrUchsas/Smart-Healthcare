<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePrescriptionRequest;
use App\Models\ActivityLog;
use App\Models\Appointment;
use App\Models\Prescription;
use App\Services\PrescriptionPdfService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PrescriptionController extends Controller
{
    public function __construct(private readonly PrescriptionPdfService $pdfService) {}

    /**
     * Show the prescription builder for a specific confirmed appointment.
     */
    public function create(Appointment $appointment): View
    {
        // Only the doctor assigned to this appointment can open the builder.
        $doctor = Auth::user()->doctor;
        abort_if($appointment->doctor_id !== $doctor->id, 403);

        // If a prescription already exists, redirect to its view instead of recreating.
        if ($appointment->prescription) {
            return view('doctor.prescriptions.show', [
                'prescription' => $appointment->prescription->load('items', 'patient.user', 'doctor.user'),
            ]);
        }

        return view('doctor.prescriptions.create', compact('appointment'));
    }

    /**
     * Store a freshly issued prescription and all its medicine items.
     *
     * We save the Prescription parent first to get an ID, then loop the
     * medicine rows. This is cleaner than a bulk insert because Eloquent
     * fires model events for each item (useful for future audit hooks).
     */
    public function store(StorePrescriptionRequest $request): RedirectResponse
    {
        $doctor = Auth::user()->doctor;
        $appointment = Appointment::findOrFail($request->appointment_id);

        abort_if($appointment->doctor_id !== $doctor->id, 403);

        $prescription = Prescription::create([
            'prescription_number' => 'RX-'.strtoupper(uniqid()),
            'appointment_id' => $appointment->id,
            'doctor_id' => $doctor->id,
            'patient_id' => $appointment->patient_id,
            'symptoms' => $request->symptoms,
            'diagnosis' => $request->diagnosis,
            'advice' => $request->advice,
            'follow_up_date' => $request->follow_up_date,
            'doctor_signature' => $request->doctor_signature,
        ]);

        foreach ($request->medicines as $item) {
            $prescription->items()->create([
                'medicine_name' => $item['medicine_name'],
                'dosage' => $item['dosage'],
                'duration' => $item['duration'],
                'instructions' => $item['instructions'] ?? null,
            ]);
        }

        // Issuing a prescription is the final step of a consultation.
        // Mark the appointment completed so it moves off the doctor's active queue
        // and shows up correctly in both the doctor and patient completed counts.
        $appointment->update(['status' => 'completed']);

        ActivityLog::log(
            'Prescription Issued',
            "Prescription #{$prescription->prescription_number} issued for appointment #{$appointment->appointment_number}.",
            Auth::user(),
            'Prescriptions'
        );

        return redirect()->route('doctor.prescriptions.show', $prescription)
            ->with('success', 'Prescription created and appointment completed successfully.');
    }

    /**
     * Show a completed prescription with all medicine items.
     */
    public function show(Prescription $prescription): View
    {
        $doctor = Auth::user()->doctor;
        abort_if($prescription->doctor_id !== $doctor->id, 403);

        $prescription->load(['items', 'patient.user', 'doctor.user', 'appointment']);

        return view('doctor.prescriptions.show', compact('prescription'));
    }

    /**
     * Stream the prescription as an inline PDF in the browser.
     */
    public function pdf(Prescription $prescription): Response
    {
        $doctor = Auth::user()->doctor;
        abort_if($prescription->doctor_id !== $doctor->id, 403);

        return $this->pdfService->stream($prescription);
    }

    /**
     * Force-download the prescription PDF.
     */
    public function download(Prescription $prescription): Response
    {
        $doctor = Auth::user()->doctor;
        abort_if($prescription->doctor_id !== $doctor->id, 403);

        return $this->pdfService->download($prescription);
    }
}
