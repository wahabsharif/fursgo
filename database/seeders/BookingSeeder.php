<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\GroomerSpacerProfile;
use App\Models\PetDetail;
use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'groomer@dev.com')->first();

        if (!$groomerSpacer) {
            $this->command?->warn('BookingSeeder skipped: groomer@dev.com not found in goormer_spacer_profiles.');
            return;
        }

        $spacerId = $groomerSpacer->id;

        $serviceAddOns = [
            'Full Groom' => [
                ['label' => 'Fast-Dry Service', 'amount' => 8.0],
                ['label' => 'Hypoallergenic Shampoo Upgrade', 'amount' => 20.0],
            ],
            'Bath & Brush' => [
                ['label' => 'De-shedding Booster', 'amount' => 12.0],
            ],
            'Nail Trim' => [
                ['label' => 'Paw Balm Treatment', 'amount' => 6.0],
            ],
            'Deshedding Treatment' => [
                ['label' => 'Coat Conditioning Mask', 'amount' => 15.0],
            ],
            'Puppy Intro Groom' => [
                ['label' => 'Calming Aromatherapy', 'amount' => 7.5],
            ],
            'Teeth Cleaning' => [
                ['label' => 'Breath Freshener Gel', 'amount' => 5.5],
            ],
            'De-matting' => [
                ['label' => 'Coat Recovery Serum', 'amount' => 14.0],
            ],
        ];

        $staffRoster = ['Emma Wilson', 'Oliver Brown', 'Sophia Ahmed', 'Liam Carter'];
        $completedRatings = [4.0, 4.2, 4.3, 4.5, 4.7, 4.8, 5.0];
        $refundStatuses = ['Rejected', 'In Progress', 'Processed'];
        $discountSamples = [0.0, 5.0, 10.0, 15.0, 20.0, 25.0];

        $clientSeeds = [
            [
                'owner' => [
                    'email' => 'petowner@example.com',
                    'name' => 'Jane Smith',
                    'address' => "12 Oak Street\nManchester M1 4DP\nUnited Kingdom",
                ],
                'pets' => [
                    ['name' => 'Buddy', 'pet_type' => 'Dog', 'breed' => 'Labrador Retriever', 'sex' => 'male', 'birthday' => '2020-03-15', 'weight' => 28.5, 'photo' => 'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=600&q=80', 'notes' => 'Friendly and energetic.'],
                    ['name' => 'Luna', 'pet_type' => 'Rabbit', 'breed' => 'Holland Lop', 'sex' => 'female', 'birthday' => '2022-06-10', 'weight' => 1.8, 'photo' => 'https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?auto=format&fit=crop&w=600&q=80', 'notes' => 'Very calm, loves cuddles.'],
                    ['name' => 'Shelly', 'pet_type' => 'Turtle', 'breed' => 'Red-Eared Slider', 'sex' => 'female', 'birthday' => '2018-01-20', 'weight' => 0.9, 'photo' => 'https://images.unsplash.com/photo-1496196614460-48988a57fccf?auto=format&fit=crop&w=600&q=80', 'notes' => 'Needs UV lamp daily.'],
                ],
                'bookings' => [
                    ['time' => '14:30 - 15:30', 'date' => today()->toDateString(), 'service' => 'Full Groom', 'amount' => 65.0, 'visit_type' => 'Garden / Shed', 'booking_status' => 'confirmed', 'pet_indices' => [0, 1, 2]],
                    ['time' => '16:00 - 17:00', 'date' => today()->toDateString(), 'service' => 'Bath & Brush', 'amount' => 45.0, 'visit_type' => 'Garden / Shed', 'booking_status' => 'confirmed', 'pet_indices' => [2]],
                    ['time' => '10:00 - 11:00', 'date' => today()->addDays(3)->toDateString(), 'service' => 'Full Groom', 'amount' => 65.0, 'visit_type' => 'Garden / Shed', 'booking_status' => 'pending', 'pet_indices' => [1, 2]],
                    ['time' => '14:30 - 15:30', 'date' => today()->addDays(5)->toDateString(), 'service' => 'Nail Trim', 'amount' => 25.0, 'visit_type' => 'salon', 'booking_status' => 'pending', 'pet_indices' => [0]],
                    ['time' => '09:00 - 10:00', 'date' => today()->addDays(7)->toDateString(), 'service' => 'Full Groom', 'amount' => 80.0, 'visit_type' => 'Garden / Shed', 'booking_status' => 'confirmed', 'pet_indices' => [0, 1]],
                    ['time' => '13:00 - 14:00', 'date' => today()->subDays(7)->toDateString(), 'service' => 'Bath & Brush', 'amount' => 45.0, 'visit_type' => 'salon', 'booking_status' => 'completed', 'pet_indices' => [0]],
                    ['time' => '17:00 - 18:00', 'date' => today()->subDays(3)->toDateString(), 'service' => 'Bath & Brush', 'amount' => 52.0, 'visit_type' => 'salon', 'booking_status' => 'completed', 'pet_indices' => [0, 1]],
                ],
            ],
            [
                'owner' => [
                    'email' => 'claire.thompson@example.com',
                    'name' => 'Claire Thompson',
                    'address' => "8 Willow Lane\nLeeds LS1 2AB\nUnited Kingdom",
                ],
                'pets' => [
                    ['name' => 'Max', 'pet_type' => 'Dog', 'breed' => 'Cocker Spaniel', 'sex' => 'male', 'birthday' => '2019-08-22', 'weight' => 14.2, 'photo' => 'https://images.unsplash.com/photo-1583511655857-d19b40a7a54e?auto=format&fit=crop&w=600&q=80', 'notes' => 'Loves water.'],
                ],
                'bookings' => [
                    ['time' => '11:00 - 12:00', 'date' => today()->addDays(2)->toDateString(), 'service' => 'Full Groom', 'amount' => 70.0, 'visit_type' => 'salon', 'booking_status' => 'confirmed', 'pet_indices' => [0]],
                    ['time' => '15:00 - 16:00', 'date' => today()->subDays(14)->toDateString(), 'service' => 'Bath & Brush', 'amount' => 42.0, 'visit_type' => 'salon', 'booking_status' => 'completed', 'pet_indices' => [0]],
                    ['time' => '10:30 - 11:30', 'date' => today()->subDays(45)->toDateString(), 'service' => 'Nail Trim', 'amount' => 24.0, 'visit_type' => 'home_visit', 'booking_status' => 'completed', 'pet_indices' => [0]],
                ],
            ],
            [
                'owner' => [
                    'email' => 'michael.obrien@example.com',
                    'name' => "Michael O'Brien",
                    'address' => "44 Birch Road\nBirmingham B2 4QA\nUnited Kingdom",
                ],
                'pets' => [
                    ['name' => 'Whiskers', 'pet_type' => 'Cat', 'breed' => 'British Shorthair', 'sex' => 'male', 'birthday' => '2021-11-05', 'weight' => 5.1, 'photo' => 'https://images.unsplash.com/photo-1514888286974-6c03e2ca1dba?auto=format&fit=crop&w=600&q=80', 'notes' => 'Indoor only.'],
                ],
                'bookings' => [
                    ['time' => '13:30 - 14:30', 'date' => today()->subDays(5)->toDateString(), 'service' => 'Full Groom', 'amount' => 55.0, 'visit_type' => 'salon', 'booking_status' => 'completed', 'pet_indices' => [0]],
                ],
            ],
            [
                'owner' => [
                    'email' => 'sarah.patel@example.com',
                    'name' => 'Sarah Patel',
                    'address' => "19 Maple Court\nLondon SW1A 1AA\nUnited Kingdom",
                ],
                'pets' => [
                    ['name' => 'Rocky', 'pet_type' => 'Dog', 'breed' => 'French Bulldog', 'sex' => 'male', 'birthday' => '2020-01-18', 'weight' => 11.5, 'photo' => 'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=600&q=80', 'notes' => 'Sensitive skin.'],
                    ['name' => 'Daisy', 'pet_type' => 'Dog', 'breed' => 'Poodle', 'sex' => 'female', 'birthday' => '2018-07-30', 'weight' => 9.8, 'photo' => 'https://images.unsplash.com/photo-1548199973-03cce0bbc87b?auto=format&fit=crop&w=600&q=80', 'notes' => 'Needs regular trims.'],
                ],
                'bookings' => [
                    ['time' => '09:30 - 10:30', 'date' => today()->addDays(4)->toDateString(), 'service' => 'Full Groom', 'amount' => 88.0, 'visit_type' => 'Garden / Shed', 'booking_status' => 'confirmed', 'pet_indices' => [0, 1]],
                    ['time' => '16:30 - 17:30', 'date' => today()->subDays(10)->toDateString(), 'service' => 'Deshedding Treatment', 'amount' => 68.0, 'visit_type' => 'salon', 'booking_status' => 'completed', 'pet_indices' => [1]],
                    ['time' => '12:00 - 13:00', 'date' => today()->subDays(28)->toDateString(), 'service' => 'Bath & Brush', 'amount' => 48.0, 'visit_type' => 'salon', 'booking_status' => 'completed', 'pet_indices' => [0]],
                    ['time' => '14:00 - 15:00', 'date' => today()->subDays(55)->toDateString(), 'service' => 'Nail Trim', 'amount' => 26.0, 'visit_type' => 'home_visit', 'booking_status' => 'completed', 'pet_indices' => [0, 1]],
                ],
            ],
            [
                'owner' => [
                    'email' => 'james.wilson@example.com',
                    'name' => 'James Wilson',
                    'address' => "3 Cedar Close\nBristol BS1 5TR\nUnited Kingdom",
                ],
                'pets' => [
                    ['name' => 'Pepper', 'pet_type' => 'Dog', 'breed' => 'Border Collie', 'sex' => 'female', 'birthday' => '2023-02-14', 'weight' => 18.0, 'photo' => 'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=80', 'notes' => 'First groom appointment.'],
                ],
                'bookings' => [
                    ['time' => '08:00 - 09:00', 'date' => today()->addDays(6)->toDateString(), 'service' => 'Puppy Intro Groom', 'amount' => 38.0, 'visit_type' => 'salon', 'booking_status' => 'pending', 'pet_indices' => [0]],
                    ['time' => '11:00 - 12:00', 'date' => today()->addDays(11)->toDateString(), 'service' => 'Full Groom', 'amount' => 62.0, 'visit_type' => 'salon', 'booking_status' => 'confirmed', 'pet_indices' => [0]],
                ],
            ],
            [
                'owner' => [
                    'email' => 'emma.davies@example.com',
                    'name' => 'Emma Davies',
                    'address' => "27 Fern Avenue\nCardiff CF10 1EP\nUnited Kingdom",
                ],
                'pets' => [
                    ['name' => 'Milo', 'pet_type' => 'Cat', 'breed' => 'Maine Coon', 'sex' => 'male', 'birthday' => '2017-04-09', 'weight' => 7.4, 'photo' => 'https://images.unsplash.com/photo-1574158622682-e40e69881006?auto=format&fit=crop&w=600&q=80', 'notes' => 'Long coat.'],
                ],
                'bookings' => [
                    ['time' => '10:00 - 11:00', 'date' => today()->subDays(2)->toDateString(), 'service' => 'De-matting', 'amount' => 58.0, 'visit_type' => 'salon', 'booking_status' => 'completed', 'pet_indices' => [0]],
                ],
            ],
            [
                'owner' => [
                    'email' => 'oliver.brown.client@example.com',
                    'name' => 'Oliver Brown',
                    'address' => "91 Ash Grove\nSheffield S1 2GU\nUnited Kingdom",
                ],
                'pets' => [
                    ['name' => 'Bailey', 'pet_type' => 'Dog', 'breed' => 'Golden Retriever', 'sex' => 'male', 'birthday' => '2016-09-12', 'weight' => 32.0, 'photo' => 'https://images.unsplash.com/photo-1633722712433-6d54ae3421d6?auto=format&fit=crop&w=600&q=80', 'notes' => 'Senior dog, gentle handling.'],
                ],
                'bookings' => [
                    ['time' => '14:00 - 15:00', 'date' => today()->subDays(120)->toDateString(), 'service' => 'Full Groom', 'amount' => 72.0, 'visit_type' => 'Garden / Shed', 'booking_status' => 'completed', 'pet_indices' => [0], 'created_days_ago' => 120],
                    ['time' => '16:00 - 17:00', 'date' => today()->subDays(95)->toDateString(), 'service' => 'Bath & Brush', 'amount' => 46.0, 'visit_type' => 'salon', 'booking_status' => 'completed', 'pet_indices' => [0], 'created_days_ago' => 95],
                ],
            ],
        ];

        foreach ($clientSeeds as $clientSeed) {
            $owner = User::firstOrCreate(
                ['email' => $clientSeed['owner']['email']],
                [
                    'name' => $clientSeed['owner']['name'],
                    'password' => bcrypt('password'),
                    'user_type' => 'pet_owner',
                    'user_status' => 'active',
                    'profile_image' => $clientSeed['owner']['profile_image'] ?? ($clientSeed['pets'][0]['photo'] ?? null),
                ]
            );

            if (!empty($clientSeed['owner']['address'])) {
                $owner->forceFill(['address' => $clientSeed['owner']['address']])->save();
            }

            if (filled($clientSeed['owner']['profile_image'] ?? null) || filled($clientSeed['pets'][0]['photo'] ?? null)) {
                $owner->forceFill([
                    'profile_image' => $clientSeed['owner']['profile_image'] ?? ($clientSeed['pets'][0]['photo'] ?? null),
                ])->save();
            }

            $pets = $this->ensurePets($owner, $clientSeed['pets']);

            foreach ($clientSeed['bookings'] as $bookingData) {
                $this->seedBooking(
                    ownerId: $owner->id,
                    spacerId: $spacerId,
                    pets: $pets,
                    bookingData: $bookingData,
                    serviceAddOns: $serviceAddOns,
                    staffRoster: $staffRoster,
                    completedRatings: $completedRatings,
                    refundStatuses: $refundStatuses,
                    discountSamples: $discountSamples,
                );
            }
        }

        $this->seedMarketingVolume(
            spacerId: $spacerId,
            serviceAddOns: $serviceAddOns,
            staffRoster: $staffRoster,
            completedRatings: $completedRatings,
            refundStatuses: $refundStatuses,
            discountSamples: $discountSamples,
        );

        $this->seedSpaceMarketingVolume(
            staffRoster: $staffRoster,
            completedRatings: $completedRatings,
            refundStatuses: $refundStatuses,
            discountSamples: $discountSamples,
        );
    }

    /**
     * Demo promo codes for the Promo Creation table.
     *
     * Date ranges intentionally cover all Valid Dates formats:
     * - Same month: 12-25 Nov
     * - Different months: 15 Jun – 15 Jul
     * - Ongoing: 12 Nov – Ongoing
     *
     * @param  list<array{
     *     code: string,
     *     type: string,
     *     amount: float|int,
     *     visibility: bool,
     *     dates?: 'same_month'|'cross_month'|'ongoing'
     * }>  $catalog
     */
    private function seedPromoCatalog(int $spacerId, array $catalog = []): void
    {
        if ($catalog === []) {
            $catalog = [
                ['code' => 'NY367', 'type' => PromoCode::DISCOUNT_TYPE_PERCENT, 'amount' => 20, 'visibility' => true, 'dates' => 'same_month'],
                ['code' => 'NEW10', 'type' => PromoCode::DISCOUNT_TYPE_POUND, 'amount' => 20, 'visibility' => true, 'dates' => 'cross_month'],
                ['code' => 'PETS19', 'type' => PromoCode::DISCOUNT_TYPE_PERCENT, 'amount' => 20, 'visibility' => true, 'dates' => 'ongoing'],
                ['code' => 'PAWS20', 'type' => PromoCode::DISCOUNT_TYPE_POUND, 'amount' => 20, 'visibility' => false, 'dates' => 'same_month'],
                ['code' => 'NAILS10', 'type' => PromoCode::DISCOUNT_TYPE_PERCENT, 'amount' => 20, 'visibility' => true, 'dates' => 'cross_month'],
                ['code' => 'BATH10', 'type' => PromoCode::DISCOUNT_TYPE_POUND, 'amount' => 5, 'visibility' => false, 'dates' => 'ongoing'],
                ['code' => 'FUR298', 'type' => PromoCode::DISCOUNT_TYPE_PERCENT, 'amount' => 20, 'visibility' => true, 'dates' => 'same_month'],
                ['code' => 'FIRST50', 'type' => PromoCode::DISCOUNT_TYPE_PERCENT, 'amount' => 20, 'visibility' => true, 'dates' => 'cross_month'],
                ['code' => 'NWYEAR26', 'type' => PromoCode::DISCOUNT_TYPE_PERCENT, 'amount' => 15, 'visibility' => true, 'dates' => 'ongoing'],
                ['code' => 'SPRING15', 'type' => PromoCode::DISCOUNT_TYPE_POUND, 'amount' => 15, 'visibility' => true, 'dates' => 'same_month'],
                ['code' => 'WELCOME10', 'type' => PromoCode::DISCOUNT_TYPE_PERCENT, 'amount' => 10, 'visibility' => true, 'dates' => 'cross_month'],
            ];
        }

        $year = (int) now()->year;
        $datePresets = [
            // 12-25 Nov
            'same_month' => [
                'start_date' => "{$year}-11-12",
                'end_date' => "{$year}-11-25",
                'no_end_date' => false,
            ],
            // 15 Jun – 15 Jul
            'cross_month' => [
                'start_date' => "{$year}-06-15",
                'end_date' => "{$year}-07-15",
                'no_end_date' => false,
            ],
            // 12 Nov – Ongoing
            'ongoing' => [
                'start_date' => "{$year}-11-12",
                'end_date' => null,
                'no_end_date' => true,
            ],
        ];

        foreach ($catalog as $row) {
            $dates = $datePresets[$row['dates'] ?? 'same_month'] ?? $datePresets['same_month'];

            PromoCode::updateOrCreate(
                [
                    'goormer_spacer_id' => $spacerId,
                    'discount_code' => $row['code'],
                ],
                [
                    'description' => '',
                    'start_date' => $dates['start_date'],
                    'end_date' => $dates['end_date'],
                    'no_end_date' => $dates['no_end_date'],
                    'discount_type' => $row['type'],
                    'discount_amount' => $row['amount'],
                    'services' => ['allow_all' => true, 'selected' => []],
                    'pet_types' => ['allow_all' => true, 'selected' => []],
                    'pet_sizes' => ['allow_all' => true, 'selected' => []],
                    'visibility' => $row['visibility'],
                ]
            );
        }
    }

    /**
     * Extra historical bookings so Marketing Hub charts/KPIs have enough coverage.
     *
     * @param  array<string, array<int, array<string, mixed>>>  $serviceAddOns
     * @param  array<int, string>  $staffRoster
     * @param  array<int, float>  $completedRatings
     * @param  array<int, string>  $refundStatuses
     * @param  array<int, float>  $discountSamples
     */
    private function seedMarketingVolume(
        int $spacerId,
        array $serviceAddOns,
        array $staffRoster,
        array $completedRatings,
        array $refundStatuses,
        array $discountSamples,
    ): void {
        $this->seedPromoCatalog($spacerId);
        $services = array_keys($serviceAddOns);
        $sources = ['direct_profile', 'direct_profile', 'direct_profile', 'platform_search', 'platform_search', 'promotion_link'];
        $promos = ['NWYEAR26', 'SPRING15', 'WELCOME10', null, null];
        $timeSlots = [
            '08:00 - 09:00',
            '10:00 - 11:00',
            '12:00 - 13:00',
            '14:00 - 15:00',
            '16:00 - 17:00',
            '18:00 - 19:00',
            '20:00 - 21:00',
        ];

        $syntheticOwners = [
            [
                'email' => 'mh.owner1@example.com',
                'name' => 'Ava Marketing',
                'pets' => [
                    ['name' => 'Coco', 'pet_type' => 'Cat', 'breed' => 'Siamese', 'sex' => 'female', 'birthday' => '2021-05-01', 'weight' => 4.2, 'photo' => null, 'notes' => null],
                ],
            ],
            [
                'email' => 'mh.owner2@example.com',
                'name' => 'Noah Marketing',
                'pets' => [
                    ['name' => 'Rex', 'pet_type' => 'Dog', 'breed' => 'Beagle', 'sex' => 'male', 'birthday' => '2019-09-12', 'weight' => 12.0, 'photo' => null, 'notes' => null],
                ],
            ],
            [
                'email' => 'mh.owner3@example.com',
                'name' => 'Isla Marketing',
                'pets' => [
                    ['name' => 'Pip', 'pet_type' => 'Rabbit', 'breed' => 'Netherland Dwarf', 'sex' => 'female', 'birthday' => '2022-01-08', 'weight' => 1.2, 'photo' => null, 'notes' => null],
                ],
            ],
        ];

        $owners = [];
        foreach ($syntheticOwners as $def) {
            $owner = User::firstOrCreate(
                ['email' => $def['email']],
                [
                    'name' => $def['name'],
                    'password' => bcrypt('password'),
                    'user_type' => 'pet_owner',
                    'user_status' => 'active',
                ]
            );
            $pets = $this->ensurePets($owner, $def['pets']);
            $owners[] = ['owner' => $owner, 'pets' => $pets];
        }

        // Spread completed bookings across weekdays + time slots for the last ~10 weeks
        for ($week = 0; $week < 10; $week++) {
            foreach ([1, 2, 3, 4, 5, 6, 0] as $weekday) {
                foreach ($timeSlots as $slotIndex => $time) {
                    // Sparse coverage: skip some combinations
                    if (($week + $weekday + $slotIndex) % 3 === 0) {
                        continue;
                    }

                    $ownerBag = $owners[($week + $weekday + $slotIndex) % count($owners)];
                    $date = today()->subWeeks($week)->startOfWeek()->addDays($weekday === 0 ? 6 : $weekday - 1);
                    if ($date->greaterThan(today())) {
                        continue;
                    }

                    $service = $services[($week + $slotIndex) % count($services)];
                    $source = $sources[($week + $weekday + $slotIndex) % count($sources)];
                    $promo = $source === 'promotion_link'
                        ? ($promos[($week + $slotIndex) % count($promos)] ?? 'NWYEAR26')
                        : ($promos[($week + $slotIndex) % count($promos)]);

                    $this->seedBooking(
                        ownerId: $ownerBag['owner']->id,
                        spacerId: $spacerId,
                        pets: $ownerBag['pets'],
                        bookingData: [
                            'time' => $time,
                            'date' => $date->toDateString(),
                            'service' => $service,
                            'amount' => 40 + (($slotIndex + $week) % 5) * 8,
                            'visit_type' => 'salon',
                            'booking_status' => 'completed',
                            'pet_indices' => [0],
                            'acquisition_source' => $source,
                            'promo_code' => $promo,
                            'discount' => $promo ? 10.0 : 0.0,
                            'created_days_ago' => max(1, today()->diffInDays($date)),
                        ],
                        serviceAddOns: $serviceAddOns,
                        staffRoster: $staffRoster,
                        completedRatings: $completedRatings,
                        refundStatuses: $refundStatuses,
                        discountSamples: $discountSamples,
                    );
                }
            }
        }
    }

    /**
     * Marketing Hub volume for the space@dev.com account (Hourly / Half-Day / Full-Day).
     *
     * @param  array<int, string>  $staffRoster
     * @param  array<int, float>  $completedRatings
     * @param  array<int, string>  $refundStatuses
     * @param  array<int, float>  $discountSamples
     */
    private function seedSpaceMarketingVolume(
        array $staffRoster,
        array $completedRatings,
        array $refundStatuses,
        array $discountSamples,
    ): void {
        $spaceSpacer = GroomerSpacerProfile::where('email', 'space@dev.com')->first();

        if (!$spaceSpacer) {
            $this->command?->warn('Space marketing seed skipped: space@dev.com not found in goormer_spacer_profiles.');

            return;
        }

        $this->seedPromoCatalog($spaceSpacer->id);

        $serviceAddOns = [
            'Hourly' => [
                ['label' => 'Storage Locker', 'amount' => 8.0],
            ],
            'Half-Day' => [
                ['label' => 'Storage Locker', 'amount' => 8.0],
                ['label' => 'Deep Clean', 'amount' => 20.0],
            ],
            'Full-Day' => [
                ['label' => 'Deep Clean', 'amount' => 20.0],
                ['label' => 'After-Hours Access', 'amount' => 10.0],
            ],
        ];

        // Weighted toward Hourly (~60%), Half-Day (~25%), Full-Day (~15%)
        $serviceSlots = [
            ['service' => 'Hourly', 'time' => '08:00 - 09:00', 'amount' => 25.0],
            ['service' => 'Hourly', 'time' => '10:00 - 11:00', 'amount' => 25.0],
            ['service' => 'Hourly', 'time' => '12:00 - 13:00', 'amount' => 28.0],
            ['service' => 'Hourly', 'time' => '14:00 - 15:00', 'amount' => 25.0],
            ['service' => 'Hourly', 'time' => '16:00 - 17:00', 'amount' => 30.0],
            ['service' => 'Hourly', 'time' => '18:00 - 19:00', 'amount' => 32.0],
            ['service' => 'Half-Day', 'time' => '09:00 - 13:00', 'amount' => 80.0],
            ['service' => 'Half-Day', 'time' => '14:00 - 18:00', 'amount' => 85.0],
            ['service' => 'Full-Day', 'time' => '09:00 - 17:00', 'amount' => 120.0],
        ];

        $sources = ['direct_profile', 'direct_profile', 'direct_profile', 'platform_search', 'platform_search', 'promotion_link'];
        $promos = ['NWYEAR26', 'SPRING15', 'WELCOME10', null, null];

        $syntheticOwners = [
            [
                'email' => 'mh.space.owner1@example.com',
                'name' => 'Mia Space',
                'pets' => [
                    ['name' => 'Pepper', 'pet_type' => 'Dog', 'breed' => 'Poodle', 'sex' => 'female', 'birthday' => '2020-04-12', 'weight' => 8.5, 'photo' => null, 'notes' => null],
                ],
            ],
            [
                'email' => 'mh.space.owner2@example.com',
                'name' => 'Leo Space',
                'pets' => [
                    ['name' => 'Mochi', 'pet_type' => 'Cat', 'breed' => 'Ragdoll', 'sex' => 'male', 'birthday' => '2021-07-19', 'weight' => 5.4, 'photo' => null, 'notes' => null],
                ],
            ],
            [
                'email' => 'mh.space.owner3@example.com',
                'name' => 'Zoe Space',
                'pets' => [
                    ['name' => 'Bean', 'pet_type' => 'Dog', 'breed' => 'Cockapoo', 'sex' => 'male', 'birthday' => '2022-02-03', 'weight' => 9.1, 'photo' => null, 'notes' => null],
                ],
            ],
        ];

        $owners = [];
        foreach ($syntheticOwners as $def) {
            $owner = User::firstOrCreate(
                ['email' => $def['email']],
                [
                    'name' => $def['name'],
                    'password' => bcrypt('password'),
                    'user_type' => 'pet_owner',
                    'user_status' => 'active',
                ]
            );
            $pets = $this->ensurePets($owner, $def['pets']);
            $owners[] = ['owner' => $owner, 'pets' => $pets];
        }

        for ($week = 0; $week < 10; $week++) {
            foreach ([1, 2, 3, 4, 5, 6, 0] as $weekday) {
                foreach ($serviceSlots as $slotIndex => $slot) {
                    if (($week + $weekday + $slotIndex) % 3 === 0) {
                        continue;
                    }

                    $ownerBag = $owners[($week + $weekday + $slotIndex) % count($owners)];
                    $date = today()->subWeeks($week)->startOfWeek()->addDays($weekday === 0 ? 6 : $weekday - 1);
                    if ($date->greaterThan(today())) {
                        continue;
                    }

                    $source = $sources[($week + $weekday + $slotIndex) % count($sources)];
                    $promo = $source === 'promotion_link'
                        ? ($promos[($week + $slotIndex) % count($promos)] ?? 'NWYEAR26')
                        : ($promos[($week + $slotIndex) % count($promos)]);

                    $this->seedBooking(
                        ownerId: $ownerBag['owner']->id,
                        spacerId: $spaceSpacer->id,
                        pets: $ownerBag['pets'],
                        bookingData: [
                            'time' => $slot['time'],
                            'date' => $date->toDateString(),
                            'service' => $slot['service'],
                            'amount' => $slot['amount'] + (($week % 3) * 2),
                            'visit_type' => 'Garden / Shed',
                            'booking_status' => 'completed',
                            'pet_indices' => [0],
                            'acquisition_source' => $source,
                            'promo_code' => $promo,
                            'discount' => $promo ? 10.0 : 0.0,
                            'created_days_ago' => max(1, today()->diffInDays($date)),
                        ],
                        serviceAddOns: $serviceAddOns,
                        staffRoster: $staffRoster,
                        completedRatings: $completedRatings,
                        refundStatuses: $refundStatuses,
                        discountSamples: $discountSamples,
                    );
                }
            }
        }
    }

    /**
     * @param  array<int, array<string, mixed>>  $petDefinitions
     */
    private function ensurePets(User $owner, array $petDefinitions): Collection
    {
        $existing = PetDetail::where('user_id', $owner->id)->get();

        if ($existing->isNotEmpty()) {
            return $existing->values();
        }

        return collect($petDefinitions)->map(function (array $pet) use ($owner) {
            return PetDetail::create([
                'user_id' => $owner->id,
                ...$pet,
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $bookingData
     * @param  array<string, array<int, array<string, mixed>>>  $serviceAddOns
     * @param  array<int, string>  $staffRoster
     * @param  array<int, float>  $completedRatings
     * @param  array<int, string>  $refundStatuses
     * @param  array<int, float>  $discountSamples
     */
    private function seedBooking(
        int $ownerId,
        int $spacerId,
        Collection $pets,
        array $bookingData,
        array $serviceAddOns,
        array $staffRoster,
        array $completedRatings,
        array $refundStatuses,
        array $discountSamples,
    ): void {
        $petIndices = $bookingData['pet_indices'];
        $createdDaysAgo = $bookingData['created_days_ago'] ?? null;
        $acquisitionSource = $bookingData['acquisition_source'] ?? null;
        $promoCode = $bookingData['promo_code'] ?? null;
        $explicitDiscount = array_key_exists('discount', $bookingData) ? $bookingData['discount'] : null;
        unset(
            $bookingData['pet_indices'],
            $bookingData['created_days_ago'],
            $bookingData['acquisition_source'],
            $bookingData['promo_code'],
            $bookingData['discount'],
        );

        $sources = ['direct_profile', 'platform_search', 'promotion_link'];
        if ($acquisitionSource === null) {
            $acquisitionSource = $sources[array_rand($sources)];
        }

        $bookingData['pet_owner_id'] = $ownerId;
        $bookingData['goormer_spacer_id'] = $spacerId;
        $bookingData['extra_add_ons'] = $serviceAddOns[$bookingData['service']] ?? [];
        $bookingData['staff'] = in_array($bookingData['booking_status'], ['confirmed', 'completed'], true)
            ? $staffRoster[array_rand($staffRoster)]
            : null;
        $bookingData['rating'] = $bookingData['booking_status'] === 'completed'
            ? $completedRatings[array_rand($completedRatings)]
            : null;
        $bookingData['cancelled_by'] = $bookingData['booking_status'] === 'cancelled' ? 'Pet Owner' : null;
        $bookingData['refund_amount'] = $bookingData['booking_status'] === 'cancelled' ? (float) $bookingData['amount'] : null;
        $bookingData['refund_status'] = $bookingData['booking_status'] === 'cancelled'
            ? $refundStatuses[array_rand($refundStatuses)]
            : null;
        $bookingData['discount'] = $explicitDiscount !== null
            ? (float) $explicitDiscount
            : (in_array($bookingData['booking_status'], ['completed', 'cancelled'], true)
                ? $discountSamples[array_rand($discountSamples)]
                : 0.0);
        $bookingData['acquisition_source'] = $acquisitionSource;

        if ($promoCode === null && $bookingData['discount'] > 0) {
            $promoCode = ['NWYEAR26', 'SPRING15', 'WELCOME10'][array_rand(['NWYEAR26', 'SPRING15', 'WELCOME10'])];
        }

        $booking = Booking::updateOrCreate(
            [
                'pet_owner_id' => $bookingData['pet_owner_id'],
                'goormer_spacer_id' => $bookingData['goormer_spacer_id'],
                'date' => $bookingData['date'],
                'time' => $bookingData['time'],
                'service' => $bookingData['service'],
            ],
            [
                'amount' => $bookingData['amount'],
                'refund_amount' => $bookingData['refund_amount'],
                'discount' => $bookingData['discount'],
                'extra_add_ons' => $bookingData['extra_add_ons'],
                'staff' => $bookingData['staff'],
                'rating' => $bookingData['rating'],
                'visit_type' => $bookingData['visit_type'],
                'acquisition_source' => $bookingData['acquisition_source'],
                'booking_status' => $bookingData['booking_status'],
                'cancelled_by' => $bookingData['cancelled_by'],
                'refund_status' => $bookingData['refund_status'],
            ]
        );

        if ($createdDaysAgo !== null) {
            $timestamp = now()->subDays($createdDaysAgo);
            $booking->forceFill([
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ])->save();
        }

        if (filled($promoCode) && $bookingData['discount'] > 0) {
            $promo = PromoCode::firstOrCreate(
                [
                    'goormer_spacer_id' => $spacerId,
                    'discount_code' => $promoCode,
                ],
                [
                    'description' => '',
                    'start_date' => now()->subYear()->toDateString(),
                    'end_date' => now()->addYear()->toDateString(),
                    'no_end_date' => false,
                    'discount_type' => PromoCode::DISCOUNT_TYPES[array_rand(PromoCode::DISCOUNT_TYPES)],
                    'discount_amount' => 10,
                    'services' => ['allow_all' => true, 'selected' => []],
                    'pet_types' => ['allow_all' => true, 'selected' => []],
                    'pet_sizes' => ['allow_all' => true, 'selected' => []],
                    'visibility' => true,
                ]
            );

            PromoCodeUsage::updateOrCreate(
                [
                    'promo_code_id' => $promo->id,
                    'booking_id' => $booking->id,
                ],
                [
                    'goormer_spacer_id' => $spacerId,
                    'pet_owner_id' => $ownerId,
                    'discount_code' => $promoCode,
                    'discount_applied' => $bookingData['discount'],
                    'used_at' => $booking->created_at ?? now(),
                ]
            );
        }

        $petIds = collect($petIndices)
            ->map(fn(int $index) => optional($pets->get($index))->id)
            ->filter()
            ->values()
            ->toArray();

        if (empty($petIds) && $pets->isNotEmpty()) {
            $petIds = [$pets->first()->id];
        }

        $booking->pets()->sync($petIds);
    }
}
