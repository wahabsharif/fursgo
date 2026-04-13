<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Groomer extends Model
{
    protected $fillable = [
        'user_id',
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
        'is_top_rated',
    ];

    protected $casts = [
        'tags' => 'array',
        'slots' => 'array',
        'is_top_rated' => 'boolean',
    ];

    /**
     * Get the user that owns the groomer
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
