<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class AccountLoginSession extends Model
{
    protected $fillable = [
        'owner_type',
        'owner_id',
        'session_id',
        'guard',
        'device_type',
        'device_label',
        'ip_address',
        'user_agent',
        'last_active_at',
    ];

    protected function casts(): array
    {
        return [
            'last_active_at' => 'datetime',
        ];
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }
}
