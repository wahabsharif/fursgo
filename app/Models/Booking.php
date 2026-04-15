<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Booking extends Model
{
    protected $fillable = [
        'pet_owner_id',
        'time',
        'date',
        'service',
        'amount',
        'visit_type',
        'booking_status',
    ];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2',
    ];

    public function petOwner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pet_owner_id');
    }

    public function pets(): BelongsToMany
    {
        return $this->belongsToMany(PetDetail::class, 'booking_pet', 'booking_id', 'pet_detail_id')->withTimestamps();
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
        return $query->whereDate('date', '>=', today())
            ->whereIn('booking_status', ['pending', 'confirmed']);
    }
}
