<?php

namespace App\Http\Requests;

use App\Models\DoctorSchedule;
use Carbon\Carbon;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreDoctorScheduleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user?->hasRole('Admin') || $user?->user_type === 'admin' || $user?->hasRole('Doctor') || $user?->user_type === 'doctor';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'doctor_id' => ['nullable', 'exists:doctors,id'],
            'schedule_date' => ['required', 'date', 'after_or_equal:today'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'slot_duration_minutes' => ['required', 'integer', 'in:15,20,30,45,60'],
        ];
    }

    /**
     * Configure the validator instance with custom overlap checks.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            $user = $this->user();
            $isAdmin = $user?->hasRole('Admin') || $user?->user_type === 'admin';

            if ($isAdmin && $this->filled('doctor_id')) {
                $doctorId = $this->input('doctor_id');
            } else {
                $doctorId = $user?->doctor?->id;
            }

            if (! $doctorId) {
                $validator->errors()->add('doctor', 'Doctor profile not found.');

                return;
            }

            if ($this->filled(['schedule_date', 'start_time', 'end_time'])) {
                $date = Carbon::parse($this->input('schedule_date'))->format('Y-m-d');
                $start = Carbon::parse($this->input('start_time'))->format('H:i:s');
                $end = Carbon::parse($this->input('end_time'))->format('H:i:s');

                // Check for overlapping schedule blocks for the specified doctor on the specified date
                $hasOverlap = DoctorSchedule::where('doctor_id', $doctorId)
                    ->whereDate('schedule_date', $date)
                    ->where('status', 'active')
                    ->where(function ($query) use ($start, $end) {
                        $query->where('start_time', '<', $end)
                            ->where('end_time', '>', $start);
                    })
                    ->exists();

                if ($hasOverlap) {
                    $validator->errors()->add(
                        'start_time',
                        'You already have an active schedule block overlapping with this timeframe on '.$date.'.'
                    );
                }
            }
        });
    }
}
