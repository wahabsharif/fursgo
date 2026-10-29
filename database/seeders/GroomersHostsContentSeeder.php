<?php

namespace Database\Seeders;

use App\Models\GroomersHostsItem;
use App\Models\GroomersHostsSetting;
use Illuminate\Database\Seeder;

class GroomersHostsContentSeeder extends Seeder
{
    public function run(): void
    {
        if (GroomersHostsSetting::query()->exists()) {
            $this->command?->info('Groomers & Hosts content already seeded — skipped.');

            return;
        }

        $catalog = $this->catalog();

        GroomersHostsSetting::create($catalog['settings']);

        foreach ($catalog['items'] as $index => $item) {
            GroomersHostsItem::create([
                ...$item,
                'sort_order' => $item['sort_order'] ?? ($index + 1),
                'is_published' => $item['is_published'] ?? true,
            ]);
        }
    }

    /**
     * Starter copy from the public business homepage
     * (business-homepage-content.blade.php + admin Website Content).
     *
     * @return array{settings: array<string, mixed>, items: list<array<string, mixed>>}
     */
    private function catalog(): array
    {
        return [
            'settings' => [
                'hero_eyebrow' => '→ Grow your grooming business',
                'hero_headline' => 'Set your own schedule, pricing & preferences.',
                'hero_description' => "Join thousands of professionals earning more with FursGo.\nYou control when you work, what you charge, and who you serve.",
                'hero_primary_cta' => 'Start your Profile',
                'hero_primary_cta_url' => '/business-landing-page',
                'hero_secondary_cta' => 'Learn more',
                'hero_secondary_cta_url' => null,
                'hero_photo' => 'images/business-homepage-groomer-space-owner-hero-section.png',

                'flexibility_title' => 'Control & flexibility',

                'safety_title' => 'Guaranteed safety & support',
                'safety_photo' => 'images/admin/website-content/safety-support-shield.svg',

                'tools_title' => 'Professional tools & CRM',

                'how_it_works_title' => 'How it works',
                'how_it_works_intro' => 'A simple 3-step process to start earning.',

                'testimonials_title' => 'Testimonials & trust',
                'testimonials_intro' => '→ Hear how providers are earning more with FursGo.',

                'services_title' => 'Service categories',
                'services_intro' => 'Earning potential varies by service and location. Top groomers earn £2,000–£4,000/month',
            ],
            'items' => [
                // Banner 1 — Control & flexibility cards
                [
                    'section' => GroomersHostsItem::SECTION_FLEXIBILITY,
                    'title' => 'Set your own schedule & prices.',
                    'description' => 'Work on your terms, choose your availability and rates.',
                    'meta' => ['card_class' => 'card-orange'],
                    'sort_order' => 1,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_FLEXIBILITY,
                    'title' => 'Choose the services you offer.',
                    'description' => 'Offer full grooms, bath & brush, add-ons & more.',
                    'meta' => ['card_class' => 'card-dark-orange'],
                    'sort_order' => 2,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_FLEXIBILITY,
                    'title' => 'Select size, age & pet preferences.',
                    'description' => 'Only accept pets you are comfortable handling.',
                    'meta' => ['card_class' => 'card-blue'],
                    'sort_order' => 3,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_FLEXIBILITY,
                    'title' => 'All-in-one platform',
                    'description' => 'Everything you need to run your business in one place.',
                    'meta' => ['card_class' => 'card-green'],
                    'sort_order' => 4,
                ],

                // Banner 2 — Safety & support benefits
                [
                    'section' => GroomersHostsItem::SECTION_SAFETY,
                    'title' => 'Manage bookings, CRM, marketing & payments',
                    'description' => 'Automated scheduling, reminders, and secure payouts.',
                    'sort_order' => 1,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_SAFETY,
                    'title' => 'Protected by FursGo Insurance',
                    'description' => 'Every booking is protected with industry-leading insurance.',
                    'sort_order' => 2,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_SAFETY,
                    'title' => 'Secure payments & ID verification',
                    'description' => 'No cash handling. Payments are guaranteed and on time.',
                    'sort_order' => 3,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_SAFETY,
                    'title' => 'Dedicated support team',
                    'description' => 'Real support from humans — whenever you need it.',
                    'sort_order' => 4,
                ],

                // Banner 3 — Professional tools & CRM
                [
                    'section' => GroomersHostsItem::SECTION_TOOLS,
                    'title' => 'Professional CRM tools',
                    'description' => 'Send follow-ups, manage clients, and recover abandoned bookings.',
                    'sort_order' => 1,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_TOOLS,
                    'title' => 'Real-time analytics dashboard',
                    'description' => 'Track earnings, returning customers, and growth instantly.',
                    'sort_order' => 2,
                ],

                // Banner 4 — How it works steps
                [
                    'section' => GroomersHostsItem::SECTION_HOW_IT_WORKS,
                    'title' => 'Create your profile',
                    'description' => 'Build a professional profile in minutes. Add photos, services, prices, and availability.',
                    'meta' => [
                        'summary' => "Build your profile with FursGo's onboarding flow.",
                        'step' => '01',
                    ],
                    'sort_order' => 1,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_HOW_IT_WORKS,
                    'title' => 'Accept Bookings',
                    'description' => 'Fill your calendar with bookings from real customers. Decide when you want to work and who you accept.',
                    'meta' => [
                        'summary' => 'Set your availability and choose clients.',
                        'step' => '02',
                    ],
                    'sort_order' => 2,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_HOW_IT_WORKS,
                    'title' => 'Get paid',
                    'description' => 'Fast, secure payouts directly to your bank. Paid automatically after each completed booking.',
                    'meta' => [
                        'summary' => 'Secure payouts straight to your bank.',
                        'step' => '03',
                    ],
                    'sort_order' => 3,
                ],

                // Testimonials
                [
                    'section' => GroomersHostsItem::SECTION_TESTIMONIAL,
                    'title' => 'Sarah T.',
                    'description' => 'FursGo helped me grow my business by 40%.',
                    'image' => 'images/ellipse-68.svg',
                    'meta' => [
                        'role' => 'Pet Salon Owner',
                        'date_label' => '1 week ago',
                        'rating' => 5,
                    ],
                    'sort_order' => 1,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_TESTIMONIAL,
                    'title' => 'Zain Riaz',
                    'description' => 'The interface is clean and easy for my clients to book from.',
                    'image' => 'images/ellipse-70.svg',
                    'meta' => [
                        'role' => 'Dog Walker',
                        'date_label' => '2 days ago',
                        'rating' => 5,
                    ],
                    'sort_order' => 2,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_TESTIMONIAL,
                    'title' => 'Emma L.',
                    'description' => 'I get more repeat bookings now and my calendar stays full.',
                    'image' => 'images/ellipse-69.svg',
                    'meta' => [
                        'role' => 'Pet Groomer',
                        'date_label' => '3 days ago',
                        'rating' => 5,
                    ],
                    'sort_order' => 3,
                ],

                // Banner 5 — Service categories
                [
                    'section' => GroomersHostsItem::SECTION_SERVICE,
                    'title' => 'Grooming',
                    'description' => 'Full grooming services including bathing, trimming, styling, and coat care.',
                    'image' => 'images/servecies-rectangle-325.png',
                    'meta' => ['layout' => 'text_first'],
                    'sort_order' => 1,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_SERVICE,
                    'title' => 'Boarding',
                    'description' => 'Overnight or short-term care for pets in a safe, home-style environment.',
                    'image' => 'images/services-rectangle-331.png',
                    'meta' => ['layout' => 'image_first'],
                    'sort_order' => 2,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_SERVICE,
                    'title' => 'Daycare',
                    'description' => 'Daytime care for dogs while owners are at work or away.',
                    'image' => 'images/services-rectangle 330.png',
                    'meta' => ['layout' => 'text_first'],
                    'sort_order' => 3,
                ],
                [
                    'section' => GroomersHostsItem::SECTION_SERVICE,
                    'title' => 'Training',
                    'description' => 'Basic obedience or behaviour-focused training sessions.',
                    'image' => 'images/servcies-rectangle-332.png',
                    'meta' => ['layout' => 'image_first'],
                    'sort_order' => 4,
                ],
            ],
        ];
    }
}
