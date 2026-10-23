<?php

namespace App\Support;

use App\Models\HomepageItem;
use App\Models\HomepageSetting;
use Illuminate\Support\Facades\Storage;

class HomepageContent
{
    /**
     * Full customer homepage payload for admin + front end.
     *
     * @return array<string, mixed>|null
     */
    public static function customer(): ?array
    {
        $settings = HomepageSetting::current();

        if (! $settings) {
            return null;
        }

        $items = HomepageItem::query()
            ->published()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');

        return [
            'hero' => [
                'headline' => $settings->hero_headline,
                'rating_value' => (float) $settings->hero_rating_value,
                'rating_headline' => $settings->hero_rating_headline,
                'rating_copy' => $settings->hero_rating_copy,
                'photo' => self::imageUrl($settings->hero_photo),
                'photo_path' => $settings->hero_photo,
            ],
            'feature' => [
                'headline' => $settings->feature_headline,
                'photo' => self::imageUrl($settings->feature_photo),
                'photo_path' => $settings->feature_photo,
                'items' => self::mapItems($items->get(HomepageItem::SECTION_FEATURE, collect())),
            ],
            'how_it_works' => [
                'title' => $settings->how_it_works_title,
                'intro' => $settings->how_it_works_intro,
                'steps' => self::mapItems($items->get(HomepageItem::SECTION_HOW_IT_WORKS, collect())),
            ],
            'popular_services' => [
                'title' => $settings->popular_services_title,
                'intro' => $settings->popular_services_intro,
                'items' => self::mapItems($items->get(HomepageItem::SECTION_POPULAR_SERVICE, collect())),
            ],
            'testimonials' => [
                'intro' => $settings->testimonials_intro,
                'desc' => $settings->testimonials_desc,
                'trust' => $settings->testimonials_trust,
                'items' => self::mapItems($items->get(HomepageItem::SECTION_TESTIMONIAL, collect())),
            ],
            'mission' => [
                'title' => $settings->mission_title,
                'desc' => $settings->mission_desc,
                'cta' => $settings->mission_cta,
                'cta_url' => $settings->mission_cta_url,
                'photo' => self::imageUrl($settings->mission_photo),
                'photo_path' => $settings->mission_photo,
            ],
            'service_map' => [
                'title' => $settings->service_map_title,
                'desc' => $settings->service_map_desc,
                'image' => self::imageUrl($settings->service_map_image),
                'image_path' => $settings->service_map_image,
                'cities' => self::mapItems($items->get(HomepageItem::SECTION_CITY, collect())),
            ],
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, HomepageItem>  $items
     * @return list<array<string, mixed>>
     */
    private static function mapItems($items): array
    {
        return $items->values()->map(function (HomepageItem $item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'image' => self::imageUrl($item->image),
                'image_path' => $item->image,
                'meta' => $item->meta ?? [],
                'sort_order' => $item->sort_order,
            ];
        })->all();
    }

    /**
     * Resolve a stored path to a public URL.
     * Seeded paths use public/images/... ; uploads use storage/app/public/homepage/...
     */
    public static function imageUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'homepage/')) {
            return Storage::disk('public')->url($path);
        }

        return asset($path);
    }
}
