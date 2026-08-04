<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProcessPaymentRequest;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use App\Services\PaymentProcessingService;
use Exception;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function __construct(
        protected PaymentProcessingService $paymentProcessingService
    ) {}

    /**
     * Show Checkout / Pay Consultation Fee page.
     */
    public function checkout(Appointment $appointment, Request $request): View|RedirectResponse
    {
        $user = $request->user();
        $patient = Patient::firstOrCreate(['user_id' => $user->id]);

        if ((int) $appointment->patient_id !== (int) $patient->id && ! $user->hasRole('Admin')) {
            abort(403, 'Unauthorized payment access.');
        }

        if ($appointment->payment_status === 'paid' && $appointment->invoice) {
            return redirect()->route('patient.invoices.show', $appointment->invoice)
                ->with('info', 'This appointment has already been paid for.');
        }

        $appointment->load(['doctor.user', 'doctor.department', 'slot']);

        return view('patient.payments.checkout', compact('appointment'));
    }

    /**
     * Process checkout submission via PaymentProcessingService.
     */
    public function process(ProcessPaymentRequest $request): RedirectResponse
    {
        $appointment = Appointment::findOrFail($request->input('appointment_id'));

        $patient = Patient::firstOrCreate(['user_id' => $request->user()->id]);
        if ((int) $appointment->patient_id !== (int) $patient->id && ! $request->user()->hasRole('Admin')) {
            abort(403, 'Unauthorized payment processing attempt.');
        }

        try {
            $invoice = $this->paymentProcessingService->processPayment($appointment, $request->validated());

            return redirect()->route('patient.invoices.show', $invoice)
                ->with('payment_success', 'Payment completed successfully! Your appointment is now confirmed.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage())->withInput();
        }
    }

    /**
     * Display Invoice details page.
     */
    public function showInvoice(Invoice $invoice, Request $request): View
    {
        $user = $request->user();
        $patient = Patient::firstOrCreate(['user_id' => $user->id]);

        if ((int) $invoice->patient_id !== (int) $patient->id && ! $user->hasRole('Admin')) {
            abort(403, 'Unauthorized access to invoice.');
        }

        $invoice->load(['appointment.doctor.user', 'appointment.doctor.department', 'appointment.slot', 'patient.user', 'payment']);

        return view('patient.invoices.show', compact('invoice'));
    }
}
