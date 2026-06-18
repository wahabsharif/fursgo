<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'booking_id',
        'pet_owner_id',
        'pet_detail_id',
        'date',
        'service_type',
        'amount',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function petOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pet_owner_id');
    }

    public function pet(): BelongsTo
    {
        return $this->belongsTo(PetDetail::class, 'pet_detail_id');
    }
}
