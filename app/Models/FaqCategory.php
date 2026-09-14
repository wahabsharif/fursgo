<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FaqCategory extends Model
{
    protected $fillable = [
        'slug',
        'name',
        'chip_label',
        'subtitle',
        'icon',
        'audience',
        'sort_order',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(FaqArticle::class);
    }

    public function scopeForAudience(Builder $query, string $audience): Builder
    {
        return $query->whereIn('audience', [$audience, 'both']);
    }
}
