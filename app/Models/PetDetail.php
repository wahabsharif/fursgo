<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PetDetail extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'photo',
        'birthday',
        'pet_type',
        'breed',
        'sex',
        'weight',
        'notes',
        'address',
        'home_address_toggled',
    ];

    protected $casts = [
        'birthday' => 'date',
        'weight' => 'decimal:2',
        'home_address_toggled' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
