<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'appointment_number',
        'patient_id',
        'doctor_id',
        'appointment_slot_id',
        'appointment_date',
        'consultation_fee',
        'status',
        'payment_status',
        'patient_notes',
    ];

    protected function casts(): array
    {
        return [
            'appointment_date' => 'date',
            'consultation_fee' => 'decimal:2',
        ];
    }

    /**
     * Get the patient who booked the appointment.
     */
    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    /**
     * Get the doctor for this appointment.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get the specific appointment slot.
     */
    public function slot(): BelongsTo
    {
        return $this->belongsTo(AppointmentSlot::class, 'appointment_slot_id');
    }
}
