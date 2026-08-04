<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProcessPaymentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'appointment_id' => ['required', 'exists:appointments,id'],
            'payment_method' => ['required', 'string', 'in:credit_card,paypal'],
            'card_holder_name' => ['required_if:payment_method,credit_card', 'nullable', 'string', 'max:255'],
            'card_number' => ['required_if:payment_method,credit_card', 'nullable', 'string', 'max:19'],
            'card_expiry' => ['required_if:payment_method,credit_card', 'nullable', 'string', 'max:7'],
            'card_cvv' => ['required_if:payment_method,credit_card', 'nullable', 'string', 'max:4'],
        ];
    }
}
