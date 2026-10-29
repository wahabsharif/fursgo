<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroomersHostsSetting extends Model
{
    protected $fillable = [
        'hero_eyebrow',
        'hero_headline',
        'hero_description',
        'hero_primary_cta',
        'hero_primary_cta_url',
        'hero_secondary_cta',
        'hero_secondary_cta_url',
        'hero_photo',
        'flexibility_title',
        'safety_title',
        'safety_photo',
        'tools_title',
        'how_it_works_title',
        'how_it_works_intro',
        'testimonials_title',
        'testimonials_intro',
        'services_title',
        'services_intro',
    ];

    /**
     * This page uses a single settings row (same idea as HomepageSetting).
     */
    public static function current(): ?self
    {
        return static::query()->first();
    }
}
