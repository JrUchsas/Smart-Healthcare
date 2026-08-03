<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppointmentSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_schedule_id',
        'doctor_id',
        'start_time',
        'end_time',
        'is_booked',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'is_booked' => 'boolean',
        ];
    }

    /**
     * Get the schedule block that this slot belongs to.
     */
    public function schedule(): BelongsTo
    {
        return $this->belongsTo(DoctorSchedule::class, 'doctor_schedule_id');
    }

    /**
     * Get the doctor for this slot.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }
}
