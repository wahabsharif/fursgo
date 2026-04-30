<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'groomer_spacer_id',
        'service_name',
        'description',
        'pet_compatibility',
        'duration',
        'pricing',
        'add_ons_compatibility',
        'visibility_controls',
    ];

    protected $casts = [
        'pet_compatibility' => 'array',
        'duration' => 'array',
        'pricing' => 'array',
        'add_ons_compatibility' => 'boolean',
        'visibility_controls' => 'boolean',
    ];
}
