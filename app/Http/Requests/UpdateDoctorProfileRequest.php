<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateDoctorProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->hasRole('Admin') || $user?->user_type === 'admin' || $user?->hasRole('Doctor') || $user?->user_type === 'doctor';
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'department_id' => ['required', 'exists:departments,id'],
            'specialization_id' => ['nullable', 'exists:specializations,id'],
            'qualification' => ['required', 'string', 'max:255'],
            'experience_years' => ['required', 'integer', 'min:0', 'max:60'],
            'consultation_fee' => ['required', 'numeric', 'min:0', 'max:10000'],
            'phone' => ['nullable', 'string', 'max:30'],
            'bio' => ['nullable', 'string', 'max:2000'],
            'is_available' => ['nullable', 'boolean'],
        ];
    }
}
