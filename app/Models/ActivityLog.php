<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'action',
        'module',
        'description',
        'ip_address',
        'user_agent',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Helper to log an activity event with smart module classification.
     */
    public static function log(string $action, string $description, ?User $user = null, ?string $module = null): self
    {
        if (empty($module)) {
            $actionLower = strtolower($action);
            if (str_contains($actionLower, 'appointment')) {
                $module = 'Appointments';
            } elseif (str_contains($actionLower, 'prescription') || str_contains($actionLower, 'rx')) {
                $module = 'Prescriptions';
            } elseif (str_contains($actionLower, 'doctor')) {
                $module = 'Doctor Management';
            } elseif (str_contains($actionLower, 'setting')) {
                $module = 'Settings';
            } elseif (str_contains($actionLower, 'profile')) {
                $module = 'Profile Management';
            } elseif (str_contains($actionLower, 'payment') || str_contains($actionLower, 'invoice') || str_contains($actionLower, 'revenue')) {
                $module = 'Billing & Revenue';
            } elseif (str_contains($actionLower, 'chat') || str_contains($actionLower, 'message')) {
                $module = 'Real-Time Chat';
            } else {
                $module = 'General Operations';
            }
        }

        return self::create([
            'user_id' => $user?->id ?? auth()->id(),
            'action' => $action,
            'module' => $module,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
