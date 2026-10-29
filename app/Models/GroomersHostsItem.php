<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GroomersHostsItem extends Model
{
    public const SECTION_FLEXIBILITY = 'flexibility';

    public const SECTION_SAFETY = 'safety';

    public const SECTION_TOOLS = 'tools';

    public const SECTION_HOW_IT_WORKS = 'how_it_works';

    public const SECTION_TESTIMONIAL = 'testimonial';

    public const SECTION_SERVICE = 'service';

    protected $fillable = [
        'section',
        'title',
        'description',
        'image',
        'meta',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'meta' => 'array',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForSection(Builder $query, string $section): Builder
    {
        return $query->where('section', $section)->orderBy('sort_order');
    }

    public function metaValue(string $key, mixed $default = null): mixed
    {
        return data_get($this->meta, $key, $default);
    }
}
