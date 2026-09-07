<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FaqArticle extends Model
{
    protected $fillable = [
        'faq_category_id',
        'question',
        'answer',
        'excerpt',
        'is_published',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForAudience(Builder $query, string $audience): Builder
    {
        return $query->whereHas('category', fn (Builder $category) => $category->forAudience($audience));
    }

    public function scopeSearch(Builder $query, string $term): Builder
    {
        $like = '%'.addcslashes(trim($term), '%_\\').'%';

        return $query->where(function (Builder $inner) use ($like) {
            $inner->where('question', 'like', $like)
                ->orWhere('answer', 'like', $like)
                ->orWhere('excerpt', 'like', $like);
        });
    }

    public function excerptText(): string
    {
        if (filled($this->excerpt)) {
            return (string) $this->excerpt;
        }

        return str($this->answer)->replace("\n", ' ')->limit(140)->toString();
    }
}
