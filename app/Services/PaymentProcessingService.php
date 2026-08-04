<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Transaction;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentProcessingService
{
    /**
     * Process patient payment and record all financial audit records atomically.
     *
     * We execute all writes inside DB::transaction so that if invoice generation,
     * payment insertion, or appointment status update fails mid-flight, MySQL
     * rolls back all partial writes immediately, preventing orphaned records.
     */
    public function processPayment(Appointment $appointment, array $paymentData): Invoice
    {
        // Prevent double payment for already confirmed or paid appointments
        if ($appointment->payment_status === 'paid' && $appointment->status === 'confirmed') {
            throw new Exception('This appointment consultation fee has already been paid.');
        }

        return DB::transaction(function () use ($appointment, $paymentData) {
            // 1. Mock 3rd Party Payment Gateway Processing
            $gatewayResponse = $this->mockGatewayCharge((float) $appointment->consultation_fee, $paymentData);

            if (! $gatewayResponse['success']) {
                throw new Exception('Payment Gateway Error: '.$gatewayResponse['message']);
            }

            // 2. Create Payment Audit Entry
            $paymentNumber = 'PAY-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
            $payment = Payment::create([
                'payment_number' => $paymentNumber,
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'amount' => $appointment->consultation_fee,
                'payment_method' => $paymentData['payment_method'] ?? 'credit_card',
                'payment_status' => 'completed',
                'paid_at' => now(),
            ]);

            // 3. Create Gateway Transaction Ledger
            $txnReference = 'TXN-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
            Transaction::create([
                'transaction_reference' => $txnReference,
                'payment_id' => $payment->id,
                'appointment_id' => $appointment->id,
                'amount' => $appointment->consultation_fee,
                'gateway' => ($paymentData['payment_method'] ?? '') === 'paypal' ? 'paypal_mock' : 'stripe_mock',
                'gateway_transaction_id' => $gatewayResponse['transaction_id'],
                'status' => 'success',
                'payload' => [
                    'last_four' => isset($paymentData['card_number']) ? substr($paymentData['card_number'], -4) : '4242',
                    'card_type' => 'Visa',
                    'charge_id' => $gatewayResponse['transaction_id'],
                ],
            ]);

            // 4. Issue Official Patient Invoice
            $invoiceNumber = 'INV-'.now()->format('Ymd').'-'.strtoupper(Str::random(5));
            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'appointment_id' => $appointment->id,
                'patient_id' => $appointment->patient_id,
                'payment_id' => $payment->id,
                'subtotal_amount' => $appointment->consultation_fee,
                'tax_amount' => 0.00,
                'total_amount' => $appointment->consultation_fee,
                'status' => 'paid',
                'issued_at' => now(),
            ]);

            // 5. Update Appointment Status to Confirmed & Paid
            $appointment->update([
                'status' => 'confirmed',
                'payment_status' => 'paid',
            ]);

            return $invoice;
        });
    }

    /**
     * Simulate response from external payment gateway provider.
     */
    protected function mockGatewayCharge(float $amount, array $paymentData): array
    {
        return [
            'success' => true,
            'transaction_id' => 'ch_mock_'.Str::random(16),
            'message' => 'Consultation fee charged successfully.',
        ];
    }
}
