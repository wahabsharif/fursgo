<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomepageSetting extends Model
{
    protected $fillable = [
        'hero_headline',
        'hero_rating_value',
        'hero_rating_headline',
        'hero_rating_copy',
        'hero_photo',
        'feature_headline',
        'feature_photo',
        'how_it_works_title',
        'how_it_works_intro',
        'popular_services_title',
        'popular_services_intro',
        'testimonials_intro',
        'testimonials_desc',
        'testimonials_trust',
        'mission_title',
        'mission_desc',
        'mission_cta',
        'mission_cta_url',
        'mission_photo',
        'service_map_title',
        'service_map_desc',
        'service_map_image',
    ];

    protected function casts(): array
    {
        return [
            'hero_rating_value' => 'float',
        ];
    }

    /**
     * Customer homepage uses a single settings row.
     */
    public static function current(): ?self
    {
        return static::query()->first();
    }
}
