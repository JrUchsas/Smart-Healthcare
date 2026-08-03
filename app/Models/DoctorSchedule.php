<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DoctorSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'schedule_date',
        'start_time',
        'end_time',
        'slot_duration_minutes',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'schedule_date' => 'date',
            'slot_duration_minutes' => 'integer',
        ];
    }

    /**
     * Get the doctor that owns the schedule.
     */
    public function doctor(): BelongsTo
    {
        return $this->belongsTo(Doctor::class);
    }

    /**
     * Get all generated appointment slots for this schedule.
     */
    public function slots(): HasMany
    {
        return $this->hasMany(AppointmentSlot::class, 'doctor_schedule_id');
    }
}
