<?php

namespace Database\Seeders;

use App\Models\HomepageItem;
use App\Models\HomepageSetting;
use Illuminate\Database\Seeder;

class HomepageContentSeeder extends Seeder
{
    public function run(): void
    {
        if (HomepageSetting::query()->exists()) {
            $this->command?->info('Homepage content already seeded — skipped.');

            return;
        }

        $catalog = $this->catalog();

        HomepageSetting::create($catalog['settings']);

        foreach ($catalog['items'] as $index => $item) {
            HomepageItem::create([
                ...$item,
                'sort_order' => $item['sort_order'] ?? ($index + 1),
                'is_published' => $item['is_published'] ?? true,
            ]);
        }
    }

    /**
     * Starter copy from the public homepage (home.blade.php).
     *
     * @return array{settings: array<string, mixed>, items: list<array<string, mixed>>}
     */
    private function catalog(): array
    {
        return [
            'settings' => [
                'hero_headline' => 'Book trusted pet grooming in minutes!',
                'hero_rating_value' => 4.9,
                'hero_rating_headline' => '4.9 average rating from 5,000 + pet owners.',
                'hero_rating_copy' => 'All groomers are ID-verified and insured through FursGo Protect.',
                'hero_photo' => 'images/hero_section.png',

                'feature_headline' => 'Groom from anywhere. Book from anywhere. Earn from anywhere.',
                'feature_photo' => 'images/dog.png',

                'how_it_works_title' => 'How it works',
                'how_it_works_intro' => "Finding the right groomer shouldn't be overwhelming — and with FursGo, it isn't. We give you clear profiles and real availability, so you can confidently choose the groomer who's the right fit for your pet's needs.",

                'popular_services_title' => 'Popular Services',
                'popular_services_intro' => 'Our most trusted services — chosen by owners who want comfort, reliability, and peace of mind.',

                'testimonials_intro' => 'Loved by Pet Parents',
                'testimonials_desc' => "Your pet's safety comes first. Every groomer is background-checked, insured, and trained in animal handling.",
                'testimonials_trust' => 'All payments and messages are protected through Fursgo.',

                'mission_title' => 'Our Mission',
                'mission_desc' => 'To create a harmonious grooming community—where pets receive gentle, timely care; owners enjoy convenience and peace of mind; groomers find flexible, rewarding opportunities; space owners foster meaningful connections; and Fetchers enable smooth, stress-free journeys.',
                'mission_cta' => 'About Us',
                'mission_cta_url' => '/profile_pets_preferences/about_us',
                'mission_photo' => 'images/our_mission.png',

                'service_map_title' => 'FursGo across the UK',
                'service_map_desc' => 'Now serving pet owners across the UK.',
                'service_map_image' => 'images/Intersect.png',
            ],
            'items' => [
                // Feature highlight points
                [
                    'section' => HomepageItem::SECTION_FEATURE,
                    'title' => 'Booked in Minutes',
                    'description' => 'No calls, no quotes.',
                    'sort_order' => 1,
                ],
                [
                    'section' => HomepageItem::SECTION_FEATURE,
                    'title' => 'Trusted Professionals',
                    'description' => 'Verified & insured.',
                    'sort_order' => 2,
                ],
                [
                    'section' => HomepageItem::SECTION_FEATURE,
                    'title' => 'Happy Pets',
                    'description' => 'Gentle grooming, stress-free service.',
                    'sort_order' => 3,
                ],

                // How it works steps
                [
                    'section' => HomepageItem::SECTION_HOW_IT_WORKS,
                    'title' => 'Search',
                    'description' => 'No more guessing or endless calling around. We help you find the right groomer with total confidence.',
                    'image' => 'images/search.png',
                    'sort_order' => 1,
                ],
                [
                    'section' => HomepageItem::SECTION_HOW_IT_WORKS,
                    'title' => 'Book & Pay',
                    'description' => 'Simple, secure, and built to save you time. Book in seconds, pay safely, and manage everything in one place.',
                    'image' => 'images/book_and_pay.png',
                    'sort_order' => 2,
                ],
                [
                    'section' => HomepageItem::SECTION_HOW_IT_WORKS,
                    'title' => 'Relax',
                    'description' => 'Calm, clear, and stress-free appointments so your pet feels comfortable and you stay informed.',
                    'image' => 'images/relax.png',
                    'sort_order' => 3,
                ],

                // Popular services
                [
                    'section' => HomepageItem::SECTION_POPULAR_SERVICE,
                    'title' => 'Full Groom',
                    'description' => 'Bath, trim, blow-dry, and essential hygiene.',
                    'image' => 'images/popular-services-1.png',
                    'meta' => ['shape_class' => 'oval'],
                    'sort_order' => 1,
                ],
                [
                    'section' => HomepageItem::SECTION_POPULAR_SERVICE,
                    'title' => 'Bath & Tidy',
                    'description' => 'A quick refresh to keep your pet clean between full grooms.',
                    'image' => 'images/popular-services-2.png',
                    'meta' => ['shape_class' => 'circle'],
                    'sort_order' => 2,
                ],
                [
                    'section' => HomepageItem::SECTION_POPULAR_SERVICE,
                    'title' => 'Nail Trim',
                    'description' => 'Quality and careful nail care focused on comfort and safety.',
                    'image' => 'images/popular-services-3.png',
                    'meta' => ['shape_class' => 'rectangle'],
                    'sort_order' => 3,
                ],
                [
                    'section' => HomepageItem::SECTION_POPULAR_SERVICE,
                    'title' => 'Pet Spa',
                    'description' => 'Soothing coat and skin treatments to nourish & refresh.',
                    'image' => 'images/popular-services-4.png',
                    'meta' => ['shape_class' => 'oval'],
                    'sort_order' => 4,
                ],
                [
                    'section' => HomepageItem::SECTION_POPULAR_SERVICE,
                    'title' => 'Mobile Grooming',
                    'description' => 'Grooming at home, for a stress-free experience.',
                    'image' => 'images/popular-services-5.png',
                    'meta' => ['shape_class' => 'circle'],
                    'sort_order' => 5,
                ],

                // Testimonials
                [
                    'section' => HomepageItem::SECTION_TESTIMONIAL,
                    'title' => 'Sarah M.',
                    'description' => "The easiest booking I've ever made! My dog looked amazing and the groomer was so kind.",
                    'image' => 'images/testimonial-1.png',
                    'meta' => ['rating' => 5, 'date_label' => '1 day ago'],
                    'sort_order' => 1,
                ],
                [
                    'section' => HomepageItem::SECTION_TESTIMONIAL,
                    'title' => 'David R.',
                    'description' => 'Five stars every time. Reliable and professional.',
                    'image' => null,
                    'meta' => ['rating' => 5, 'date_label' => '1 week ago'],
                    'sort_order' => 2,
                ],
                [
                    'section' => HomepageItem::SECTION_TESTIMONIAL,
                    'title' => 'Sarah M.',
                    'description' => 'Such a smooth experience — my pup loved it!',
                    'image' => null,
                    'meta' => ['rating' => 5, 'date_label' => '1 day ago'],
                    'sort_order' => 3,
                ],

                // Service map cities (Leeds kept separate — home.blade currently merges Birmingham + Leeds)
                [
                    'section' => HomepageItem::SECTION_CITY,
                    'title' => 'London',
                    'sort_order' => 1,
                ],
                [
                    'section' => HomepageItem::SECTION_CITY,
                    'title' => 'Manchester',
                    'sort_order' => 2,
                ],
                [
                    'section' => HomepageItem::SECTION_CITY,
                    'title' => 'Birmingham',
                    'sort_order' => 3,
                ],
                [
                    'section' => HomepageItem::SECTION_CITY,
                    'title' => 'Leeds',
                    'sort_order' => 4,
                ],
                [
                    'section' => HomepageItem::SECTION_CITY,
                    'title' => 'Bristol',
                    'sort_order' => 5,
                ],
                [
                    'section' => HomepageItem::SECTION_CITY,
                    'title' => 'Edinburgh',
                    'sort_order' => 6,
                ],
            ],
        ];
    }
}
