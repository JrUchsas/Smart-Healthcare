<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Prescription;
use App\Services\PrescriptionPdfService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class PatientPrescriptionController extends Controller
{
    public function __construct(private readonly PrescriptionPdfService $pdfService) {}

    /**
     * List all prescriptions issued to the logged-in patient.
     */
    public function index(): View
    {
        $patient = Auth::user()->patient;

        $prescriptions = Prescription::with(['doctor.user', 'appointment', 'items'])
            ->where('patient_id', $patient->id)
            ->latest()
            ->get();

        return view('patient.prescriptions.index', compact('prescriptions'));
    }

    /**
     * Show a single prescription in detail.
     */
    public function show(Prescription $prescription): View
    {
        $patient = Auth::user()->patient;

        // Patients may only view their own prescriptions.
        abort_if($prescription->patient_id !== $patient->id, 403);

        $prescription->load(['items', 'doctor.user', 'appointment']);

        return view('patient.prescriptions.show', compact('prescription'));
    }

    /**
     * Stream the prescription PDF inline so the patient can read it in-browser
     * and choose to save it. Identical PDF template to the doctor's view.
     */
    public function pdf(Prescription $prescription): Response
    {
        $patient = Auth::user()->patient;

        abort_if($prescription->patient_id !== $patient->id, 403);

        return $this->pdfService->stream($prescription);
    }

    /**
     * Force-download the prescription as a PDF file.
     */
    public function download(Prescription $prescription): Response
    {
        $patient = Auth::user()->patient;

        abort_if($prescription->patient_id !== $patient->id, 403);

        return $this->pdfService->download($prescription);
    }
}
