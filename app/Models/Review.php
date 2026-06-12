<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'booking_id',
        'pet_owner_id',
        'review',
        'reply',
        'rating',
        'reply_from',
    ];

    protected $casts = [
        'rating' => 'decimal:1',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function petOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pet_owner_id');
    }

    public function replyFrom(): BelongsTo
    {
        return $this->belongsTo(GroomerSpacerProfile::class, 'reply_from');
    }
}
