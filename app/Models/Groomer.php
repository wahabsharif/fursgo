<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groomer extends Model
{
    protected $fillable = [
        'name',
        'studio_name',
        'distance',
        'rating',
        'reviews_count',
        'experience_text',
        'price',
        'image_url',
        'tags',
        'slots',
        'is_top_rated'
    ];

    protected $casts = [
        'tags' => 'array',
        'slots' => 'array',
        'is_top_rated' => 'boolean',
    ];
}
