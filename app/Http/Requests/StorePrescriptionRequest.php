<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->hasRole('Doctor');
    }

    public function rules(): array
    {
        return [
            'appointment_id' => ['required', 'integer', 'exists:appointments,id'],
            'symptoms' => ['nullable', 'string', 'max:2000'],
            'diagnosis' => ['nullable', 'string', 'max:2000'],
            'advice' => ['nullable', 'string', 'max:2000'],
            'follow_up_date' => ['nullable', 'date', 'after_or_equal:today'],
            'doctor_signature' => ['nullable', 'string', 'max:255'],

            // Each medicine row must supply a name, dosage, and duration.
            // We validate the full array so a partial row never slips through.
            'medicines' => ['required', 'array', 'min:1'],
            'medicines.*.medicine_name' => ['required', 'string', 'max:255'],
            'medicines.*.dosage' => ['required', 'string', 'max:255'],
            'medicines.*.duration' => ['required', 'string', 'max:255'],
            'medicines.*.instructions' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'medicines.required' => 'Please add at least one medicine.',
            'medicines.*.medicine_name.required' => 'Medicine name is required for every row.',
            'medicines.*.dosage.required' => 'Dosage is required for every medicine.',
            'medicines.*.duration.required' => 'Duration is required for every medicine.',
        ];
    }
}
