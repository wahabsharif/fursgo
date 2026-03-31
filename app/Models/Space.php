<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $fillable = [
        'name',
        'description',
        'distance',
        'price',
        'image_url',
        'venue_type',
        'amenities'
    ];

    protected $casts = [
        'amenities' => 'array',
    ];
}
