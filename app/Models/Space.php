<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Space extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'distance',
        'price',
        'image_url',
        'venue_type',
        'amenities',
    ];

    protected $casts = [
        'amenities' => 'array',
    ];

    /**
     * Get the user that owns the space
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
