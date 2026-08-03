<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Doctor extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'user_id',
        'department_id',
        'specialization_id',
        'qualification',
        'experience_years',
        'consultation_fee',
        'phone',
        'bio',
        'is_available',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'experience_years' => 'integer',
            'consultation_fee' => 'decimal:2',
            'is_available' => 'boolean',
        ];
    }

    /**
     * Get the core user record for this doctor.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the assigned department.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the assigned specialization.
     */
    public function specialization(): BelongsTo
    {
        return $this->belongsTo(Specialization::class);
    }

    /**
     * Get all schedule blocks for this doctor.
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(DoctorSchedule::class);
    }

    /**
     * Get all individual appointment slots for this doctor.
     */
    public function appointmentSlots(): HasMany
    {
        return $this->hasMany(AppointmentSlot::class);
    }

    /**
     * Get all appointments for this doctor.
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
