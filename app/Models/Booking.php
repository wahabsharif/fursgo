<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'pet_owner_id',
        'goormer_spacer_id',
        'time',
        'date',
        'service',
        'amount',
        'refund_amount',
        'discount',
        'extra_add_ons',
        'staff',
        'rating',
        'visit_type',
        'acquisition_source',
        'booking_status',
        'cancelled_by',
        'refund_status',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
        'refund_amount' => 'decimal:2',
        'discount' => 'decimal:2',
        'extra_add_ons' => 'array',
        'rating' => 'decimal:1',
    ];

    public function petOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pet_owner_id');
    }

    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(PetDetail::class, 'booking_pet', 'booking_id', 'pet_detail_id')->withTimestamps();
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function promoCodeUsage(): HasOne
    {
        return $this->hasOne(PromoCodeUsage::class);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopePending($query)
    {
        return $query->where('booking_status', 'pending');
    }

    public function scopeConfirmed($query)
    {
        return $query->where('booking_status', 'confirmed');
    }

    public function scopeUpcoming($query)
    {
        return $query
            ->whereDate('date', '>=', today())
            ->whereIn('booking_status', ['pending', 'confirmed']);
    }
}
