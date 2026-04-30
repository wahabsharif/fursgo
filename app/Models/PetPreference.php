<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PetPreference extends Model
{
    protected $fillable = [
        'pet_compatibility',
    ];

    protected $casts = [
        'pet_compatibility' => 'array',
    ];
}
