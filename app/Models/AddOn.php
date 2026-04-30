<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    protected $table = 'add_ons';

    protected $fillable = [
        'groomer_spacer_id',
        'add_ons_name',
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
