<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    public const DISCOUNT_TYPE_PERCENT = '%';

    public const DISCOUNT_TYPE_POUND = '£';

    /**
     * @var list<string>
     */
    public const DISCOUNT_TYPES = [
        self::DISCOUNT_TYPE_PERCENT,
        self::DISCOUNT_TYPE_POUND,
    ];

    protected $fillable = [
        'goormer_spacer_id',
        'discount_code',
        'description',
        'start_date',
        'end_date',
        'no_end_date',
        'discount_type',
        'discount_amount',
        'services',
        'pet_types',
        'pet_sizes',
        'visibility',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'no_end_date' => 'boolean',
        'discount_amount' => 'decimal:2',
        'services' => 'array',
        'pet_types' => 'array',
        'pet_sizes' => 'array',
        'visibility' => 'boolean',
    ];

    public function spacer(): BelongsTo
    {
        return $this->belongsTo(GroomerSpacerProfile::class, 'goormer_spacer_id');
    }

    public function usages(): HasMany
    {
        return $this->hasMany(PromoCodeUsage::class);
    }
}
