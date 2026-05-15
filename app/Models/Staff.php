<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'goormer_spacer_profile_id',
        'name',
        'phone',
        'email',
        'job_title',
        'image',
        'working_hours',
        'holiday_time_off',
        'pause_booking',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'holiday_time_off' => 'array',
        'pause_booking' => 'boolean',
    ];

    public function groomerSpacerProfile(): BelongsTo
    {
        return $this->belongsTo(GroomerSpacerProfile::class, 'goormer_spacer_profile_id');
    }
}
