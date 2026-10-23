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
     * Alpine-friendly bootstrap for the admin Website Content tab.
     *
     * @return array<string, mixed>
     */
    public static function adminBootstrap(): array
    {
        $content = self::customer();

        if (! $content) {
            return [
                'heroHeadline' => 'Book trusted pet grooming in minutes!',
                'heroRatingValue' => 4.9,
                'heroRatingHeadline' => '4.9 average rating from 5,000 + pet owners.',
                'heroRatingCopy' => 'All groomers are ID-verified and insured through FursGo Protect.',
                'heroPhotoUrl' => asset('images/hero_section.png'),
                'heroPhotoPath' => 'images/hero_section.png',
                'featureHeadline' => 'Groom from anywhere. Book from anywhere. Earn from anywhere.',
                'featurePhotoUrl' => asset('images/dog.png'),
                'featurePhotoPath' => 'images/dog.png',
                'featureItems' => [],
                'howItWorksTitle' => 'How it works',
                'howItWorksIntro' => '',
                'howItWorksSteps' => [],
                'popularServicesTitle' => 'Popular Services',
                'popularServicesIntro' => '',
                'popularServicesItems' => [],
                'testimonialIntro' => 'Loved by Pet Parents',
                'testimonialDesc' => '',
                'testimonialTrust' => '',
                'testimonials' => [],
                'banner3Title' => 'Our Mission',
                'banner3Desc' => '',
                'banner3Cta' => 'About Us',
                'banner3CtaUrl' => '/profile_pets_preferences/about_us',
                'banner3PhotoUrl' => asset('images/our_mission.png'),
                'banner3PhotoPath' => 'images/our_mission.png',
                'serviceMapTitle' => 'FursGo across the UK',
                'serviceMapDesc' => 'Now serving pet owners across the UK.',
                'cities' => [],
            ];
        }

        return [
            'heroHeadline' => $content['hero']['headline'],
            'heroRatingValue' => $content['hero']['rating_value'],
            'heroRatingHeadline' => $content['hero']['rating_headline'],
            'heroRatingCopy' => $content['hero']['rating_copy'],
            'heroPhotoUrl' => $content['hero']['photo'] ?? asset('images/hero_section.png'),
            'heroPhotoPath' => $content['hero']['photo_path'],
            'featureHeadline' => $content['feature']['headline'],
            'featurePhotoUrl' => $content['feature']['photo'] ?? asset('images/dog.png'),
            'featurePhotoPath' => $content['feature']['photo_path'],
            'featureItems' => collect($content['feature']['items'])->map(fn (array $item) => [
                'id' => $item['id'],
                'title' => $item['title'],
                'desc' => $item['description'],
            ])->values()->all(),
            'howItWorksTitle' => $content['how_it_works']['title'],
            'howItWorksIntro' => $content['how_it_works']['intro'],
            'howItWorksSteps' => collect($content['how_it_works']['steps'])->map(fn (array $item) => [
                'id' => $item['id'],
                'title' => $item['title'],
                'desc' => $item['description'],
                'image' => $item['image'],
                'image_path' => $item['image_path'],
            ])->values()->all(),
            'popularServicesTitle' => $content['popular_services']['title'],
            'popularServicesIntro' => $content['popular_services']['intro'],
            'popularServicesItems' => collect($content['popular_services']['items'])->map(fn (array $item) => [
                'id' => $item['id'],
                'title' => $item['title'],
                'desc' => $item['description'],
                'image' => $item['image'],
                'image_path' => $item['image_path'],
                'shape_class' => $item['meta']['shape_class'] ?? 'oval',
            ])->values()->all(),
            'testimonialIntro' => $content['testimonials']['intro'],
            'testimonialDesc' => $content['testimonials']['desc'],
            'testimonialTrust' => $content['testimonials']['trust'],
            'testimonials' => collect($content['testimonials']['items'])->map(fn (array $item) => [
                'id' => $item['id'],
                'name' => $item['title'],
                'quote' => $item['description'],
                'when' => $item['meta']['date_label'] ?? '',
                'dateSubmitted' => $item['meta']['date_label'] ?? '',
                'rating' => (int) ($item['meta']['rating'] ?? 5),
                'avatar' => $item['image'],
                'image_path' => $item['image_path'],
            ])->values()->all(),
            'banner3Title' => $content['mission']['title'],
            'banner3Desc' => $content['mission']['desc'],
            'banner3Cta' => $content['mission']['cta'],
            'banner3CtaUrl' => $content['mission']['cta_url'],
            'banner3PhotoUrl' => $content['mission']['photo'] ?? asset('images/our_mission.png'),
            'banner3PhotoPath' => $content['mission']['photo_path'],
            'serviceMapTitle' => $content['service_map']['title'],
            'serviceMapDesc' => $content['service_map']['desc'],
            'cities' => collect($content['service_map']['cities'])->pluck('title')->values()->all(),
        ];
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
