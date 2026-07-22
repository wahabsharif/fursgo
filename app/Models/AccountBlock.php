<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class AccountBlock extends Model
{
    protected $fillable = [
        'blocker_type',
        'blocker_id',
        'blocked_type',
        'blocked_id',
    ];

    public function blocker(): MorphTo
    {
        return $this->morphTo();
    }

    public function blocked(): MorphTo
    {
        return $this->morphTo();
    }
}
