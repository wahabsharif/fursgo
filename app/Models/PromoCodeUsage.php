<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class PromoCodeUsage extends Model
{
    protected $fillable = [
        'promo_code_id',
        'goormer_spacer_id',
        'pet_owner_id',
        'booking_id',
        'discount_code',
        'discount_applied',
        'used_at',
    ];

    protected $casts = [
        'discount_applied' => 'decimal:2',
        'used_at' => 'datetime',
    ];

    public function promoCode(): BelongsTo
    {
        return $this->belongsTo(PromoCode::class);
    }

    public function spacer(): BelongsTo
    {
        return $this->belongsTo(GroomerSpacerProfile::class, 'goormer_spacer_id');
    }

    public function petOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pet_owner_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}
