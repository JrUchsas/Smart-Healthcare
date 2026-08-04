<?php

namespace App\Services;

use App\Models\Prescription;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PrescriptionPdfService
{
    /**
     * Generate and stream the prescription as an inline PDF.
     *
     * We stream inline (rather than force-download) so the patient can
     * view it in the browser's built-in PDF viewer and choose to save it
     * themselves. The filename is still set so "Save As" gives a clean name.
     */
    public function stream(Prescription $prescription): Response
    {
        $prescription->load([
            'items',
            'doctor.user',
            'doctor.specialization',
            'patient.user',
            'appointment',
        ]);

        // DomPDF works best with A4 paper for medical documents.
        // setPaper dimensions ensure the clinical layout renders consistently
        // across every OS without relying on browser print settings.
        $pdf = Pdf::loadView('doctor.prescriptions.pdf', compact('prescription'))
            ->setPaper('a4', 'portrait');

        $filename = 'prescription-'.$prescription->prescription_number.'.pdf';

        return $pdf->stream($filename);
    }

    /**
     * Force a download instead of inline view.
     *
     * Kept separate from stream() because some entry points (e.g. a "Download"
     * button on the patient portal) need the browser to trigger a Save dialogue
     * rather than opening the PDF in a new tab.
     */
    public function download(Prescription $prescription): Response
    {
        $prescription->load([
            'items',
            'doctor.user',
            'doctor.specialization',
            'patient.user',
            'appointment',
        ]);

        $pdf = Pdf::loadView('doctor.prescriptions.pdf', compact('prescription'))
            ->setPaper('a4', 'portrait');

        $filename = 'prescription-'.$prescription->prescription_number.'.pdf';

        return $pdf->download($filename);
    }
}
