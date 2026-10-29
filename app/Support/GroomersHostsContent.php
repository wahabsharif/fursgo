<?php

namespace App\Support;

use App\Models\GroomersHostsItem;
use App\Models\GroomersHostsSetting;
use Illuminate\Support\Facades\Storage;

class GroomersHostsContent
{
    /**
     * Full For Groomers & Hosts payload for the public page.
     *
     * @return array<string, mixed>|null
     */
    public static function page(): ?array
    {
        $settings = GroomersHostsSetting::current();

        if (! $settings) {
            return null;
        }

        $items = GroomersHostsItem::query()
            ->published()
            ->orderBy('sort_order')
            ->get()
            ->groupBy('section');

        return [
            'hero' => [
                'eyebrow' => $settings->hero_eyebrow,
                'headline' => $settings->hero_headline,
                'description' => $settings->hero_description,
                'primary_cta' => $settings->hero_primary_cta,
                'primary_cta_url' => $settings->hero_primary_cta_url,
                'secondary_cta' => $settings->hero_secondary_cta,
                'secondary_cta_url' => $settings->hero_secondary_cta_url,
                'photo' => self::imageUrl($settings->hero_photo),
                'photo_path' => $settings->hero_photo,
            ],
            'flexibility' => [
                'title' => $settings->flexibility_title,
                'items' => self::mapItems($items->get(GroomersHostsItem::SECTION_FLEXIBILITY, collect())),
            ],
            'safety' => [
                'title' => $settings->safety_title,
                'photo' => self::imageUrl($settings->safety_photo),
                'photo_path' => $settings->safety_photo,
                'items' => self::mapItems($items->get(GroomersHostsItem::SECTION_SAFETY, collect())),
            ],
            'tools' => [
                'title' => $settings->tools_title,
                'items' => self::mapItems($items->get(GroomersHostsItem::SECTION_TOOLS, collect())),
            ],
            'how_it_works' => [
                'title' => $settings->how_it_works_title,
                'intro' => $settings->how_it_works_intro,
                'steps' => self::mapItems($items->get(GroomersHostsItem::SECTION_HOW_IT_WORKS, collect())),
            ],
            'testimonials' => [
                'title' => $settings->testimonials_title,
                'intro' => $settings->testimonials_intro,
                'items' => self::mapItems($items->get(GroomersHostsItem::SECTION_TESTIMONIAL, collect())),
            ],
            'services' => [
                'title' => $settings->services_title,
                'intro' => $settings->services_intro,
                'items' => self::mapItems($items->get(GroomersHostsItem::SECTION_SERVICE, collect())),
            ],
        ];
    }

    /**
     * Alpine-friendly bootstrap for the admin Website Content tab.
     *
     * @return array<string, mixed>
     */
    public static function adminBootstrap(): array
    {
        $content = self::page();

        if (! $content) {
            return [
                'heroEyebrow' => '→ Grow your grooming business',
                'heroHeadline' => 'Set your own schedule, pricing & preferences.',
                'heroDescription' => 'Join thousands of professionals earning more with FursGo. You control when you work, what you charge, and who you serve.',
                'heroPrimaryCta' => 'Start your Profile',
                'heroPrimaryCtaUrl' => '/business-landing-page',
                'heroSecondaryCta' => 'Learn more',
                'heroSecondaryCtaUrl' => '',
                'heroPhotoUrl' => asset('images/business-homepage-groomer-space-owner-hero-section.png'),
                'heroPhotoPath' => 'images/business-homepage-groomer-space-owner-hero-section.png',
            ];
        }

        return [
            'heroEyebrow' => $content['hero']['eyebrow'] ?? '',
            'heroHeadline' => $content['hero']['headline'] ?? '',
            'heroDescription' => $content['hero']['description'] ?? '',
            'heroPrimaryCta' => $content['hero']['primary_cta'] ?? '',
            'heroPrimaryCtaUrl' => $content['hero']['primary_cta_url'] ?? '',
            'heroSecondaryCta' => $content['hero']['secondary_cta'] ?? '',
            'heroSecondaryCtaUrl' => $content['hero']['secondary_cta_url'] ?? '',
            'heroPhotoUrl' => $content['hero']['photo'] ?? asset('images/business-homepage-groomer-space-owner-hero-section.png'),
            'heroPhotoPath' => $content['hero']['photo_path'],
        ];
    }

    /**
     * @param  \Illuminate\Support\Collection<int, GroomersHostsItem>  $items
     * @return list<array<string, mixed>>
     */
    private static function mapItems($items): array
    {
        return $items->values()->map(function (GroomersHostsItem $item) {
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
     * Seeded paths use public/images/... ; uploads use storage/app/public/groomers-hosts/...
     */
    public static function imageUrl(?string $path): ?string
    {
        if (! filled($path)) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        if (str_starts_with($path, 'groomers-hosts/')) {
            return Storage::disk('public')->url($path);
        }

        return asset($path);
    }
}
