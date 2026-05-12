<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\PetDetail;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $spacerIds = DB::table('goormer_spacer_profiles')->pluck('id')->filter()->values()->all();

        if (empty($spacerIds)) {
            $this->command?->warn('BookingSeeder skipped: no goormer_spacer_profiles found.');
            return;
        }

        // Ensure we have a pet owner user
        $owner = User::firstOrCreate(
            ['email' => 'petowner@example.com'],
            [
                'name'      => 'Jane Smith',
                'password'  => bcrypt('password'),
                'user_type' => 'pet_owner',
            ]
        );

        // users.address is not mass-assignable on User; set for invoice / Bill To demos
        $owner->forceFill([
            'address' => "12 Oak Street\nManchester M1 4DP\nUnited Kingdom",
        ])->save();

        // Create pets for this owner if none exist
        $pets = PetDetail::where('user_id', $owner->id)->get();

        if ($pets->isEmpty()) {
            $pets = collect([
                PetDetail::create([
                    'user_id'  => $owner->id,
                    'name'     => 'Buddy',
                    'pet_type' => 'Dog',
                    'breed'    => 'Labrador Retriever',
                    'sex'      => 'male',
                    'birthday' => '2020-03-15',
                    'weight'   => 28.5,
                    'photo'    => 'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=600&q=80',
                    'notes'    => 'Friendly and energetic.',
                ]),
                PetDetail::create([
                    'user_id'  => $owner->id,
                    'name'     => 'Luna',
                    'pet_type' => 'Rabbit',
                    'breed'    => 'Holland Lop',
                    'sex'      => 'female',
                    'birthday' => '2022-06-10',
                    'weight'   => 1.8,
                    'photo'    => 'https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?auto=format&fit=crop&w=600&q=80',
                    'notes'    => 'Very calm, loves cuddles.',
                ]),
                PetDetail::create([
                    'user_id'  => $owner->id,
                    'name'     => 'Shelly',
                    'pet_type' => 'Turtle',
                    'breed'    => 'Red-Eared Slider',
                    'sex'      => 'female',
                    'birthday' => '2018-01-20',
                    'weight'   => 0.9,
                    'photo'    => 'https://images.unsplash.com/photo-1496196614460-48988a57fccf?auto=format&fit=crop&w=600&q=80',
                    'notes'    => 'Needs UV lamp daily.',
                ]),
            ]);
        }

        // Normalize keys so index-based booking mappings are reliable.
        $pets = $pets->values();
        $serviceAddOns = [
            'Full Groom' => [
                ['label' => 'Fast-Dry Service', 'amount' => 8.00],
                ['label' => 'Hypoallergenic Shampoo Upgrade', 'amount' => 20.00],
            ],
            'Bath & Brush' => [
                ['label' => 'De-shedding Booster', 'amount' => 12.00],
            ],
            'Nail Trim' => [
                ['label' => 'Paw Balm Treatment', 'amount' => 6.00],
            ],
            'Deshedding Treatment' => [
                ['label' => 'Coat Conditioning Mask', 'amount' => 15.00],
            ],
            'Puppy Intro Groom' => [
                ['label' => 'Calming Aromatherapy', 'amount' => 7.50],
            ],
            'Teeth Cleaning' => [
                ['label' => 'Breath Freshener Gel', 'amount' => 5.50],
            ],
            'De-matting' => [
                ['label' => 'Coat Recovery Serum', 'amount' => 14.00],
            ],
        ];
        $staffRoster = ['Emma Wilson', 'Oliver Brown', 'Sophia Ahmed', 'Liam Carter'];
        $completedRatings = [4.0, 4.2, 4.3, 4.5, 4.7, 4.8, 5.0];
        $refundStatuses = ['Rejected', 'In Progress', 'Processed'];
        $discountSamples = [0.0, 5.0, 10.0, 15.0, 20.0, 25.0];

        $bookings = [
            // Today — confirmed
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '14:30 - 15:30',
                'date'           => today()->toDateString(),
                'service'        => 'Full Groom',
                'amount'         => 65.00,
                'visit_type'     => 'Garden / Shed',
                'booking_status' => 'confirmed',
                'pet_indices'    => [0, 1, 2],
            ],
            // Today — confirmed (second slot)
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '16:00 - 17:00',
                'date'           => today()->toDateString(),
                'service'        => 'Bath & Brush',
                'amount'         => 45.00,
                'visit_type'     => 'Garden / Shed',
                'booking_status' => 'confirmed',
                'pet_indices'    => [2],
            ],
            // Pending — future
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '10:00 - 11:00',
                'date'           => today()->addDays(3)->toDateString(),
                'service'        => 'Full Groom',
                'amount'         => 65.00,
                'visit_type'     => 'Garden / Shed',
                'booking_status' => 'pending',
                'pet_indices'    => [1, 2],
            ],
            // Pending — future
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '14:30 - 15:30',
                'date'           => today()->addDays(5)->toDateString(),
                'service'        => 'Nail Trim',
                'amount'         => 25.00,
                'visit_type'     => 'salon',
                'booking_status' => 'pending',
                'pet_indices'    => [0],
            ],
            // Upcoming — confirmed
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '09:00 - 10:00',
                'date'           => today()->addDays(7)->toDateString(),
                'service'        => 'Full Groom',
                'amount'         => 80.00,
                'visit_type'     => 'Garden / Shed',
                'booking_status' => 'confirmed',
                'pet_indices'    => [0, 1],
            ],
            // Upcoming — confirmed (turtle)
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '11:30 - 12:30',
                'date'           => today()->addDays(10)->toDateString(),
                'service'        => 'Full Groom',
                'amount'         => 40.00,
                'visit_type'     => 'Garden / Shed',
                'booking_status' => 'confirmed',
                'pet_indices'    => [2],
            ],
            // Past — completed
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '13:00 - 14:00',
                'date'           => today()->subDays(7)->toDateString(),
                'service'        => 'Bath & Brush',
                'amount'         => 45.00,
                'visit_type'     => 'salon',
                'booking_status' => 'completed',
                'pet_indices'    => [0],
            ],
            // Today — pending
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '18:00 - 19:00',
                'date'           => today()->toDateString(),
                'service'        => 'Nail Trim',
                'amount'         => 28.00,
                'visit_type'     => 'home_visit',
                'booking_status' => 'pending',
                'pet_indices'    => [1],
            ],
            // Near future — confirmed
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '08:30 - 09:30',
                'date'           => today()->addDays(1)->toDateString(),
                'service'        => 'Deshedding Treatment',
                'amount'         => 72.00,
                'visit_type'     => 'Garden / Shed',
                'booking_status' => 'confirmed',
                'pet_indices'    => [0, 2],
            ],
            // Future — pending
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '12:00 - 13:00',
                'date'           => today()->addDays(2)->toDateString(),
                'service'        => 'Puppy Intro Groom',
                'amount'         => 38.00,
                'visit_type'     => 'salon',
                'booking_status' => 'pending',
                'pet_indices'    => [0],
            ],
            // Future — cancelled
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '15:00 - 16:00',
                'date'           => today()->addDays(4)->toDateString(),
                'service'        => 'Full Groom',
                'amount'         => 78.00,
                'visit_type'     => 'mobile_station',
                'booking_status' => 'cancelled',
                'pet_indices'    => [1, 2],
            ],
            // Future — confirmed
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '10:30 - 11:30',
                'date'           => today()->addDays(6)->toDateString(),
                'service'        => 'Teeth Cleaning',
                'amount'         => 32.00,
                'visit_type'     => 'Garden / Shed',
                'booking_status' => 'confirmed',
                'pet_indices'    => [2],
            ],
            // Future — completed-like history seed
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '17:00 - 18:00',
                'date'           => today()->subDays(3)->toDateString(),
                'service'        => 'Bath & Brush',
                'amount'         => 52.00,
                'visit_type'     => 'salon',
                'booking_status' => 'completed',
                'pet_indices'    => [0, 1],
            ],
            // Older past — cancelled
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '11:00 - 12:00',
                'date'           => today()->subDays(12)->toDateString(),
                'service'        => 'Nail Trim',
                'amount'         => 22.00,
                'visit_type'     => 'home_visit',
                'booking_status' => 'cancelled',
                'pet_indices'    => [1],
            ],
            // Future — pending
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '09:30 - 10:30',
                'date'           => today()->addDays(9)->toDateString(),
                'service'        => 'Full Groom',
                'amount'         => 82.00,
                'visit_type'     => 'mobile_station',
                'booking_status' => 'pending',
                'pet_indices'    => [0, 1, 2],
            ],
            // Far future — confirmed
            [
                'pet_owner_id'   => $owner->id,
                'time'           => '13:30 - 14:30',
                'date'           => today()->addDays(14)->toDateString(),
                'service'        => 'De-matting',
                'amount'         => 58.00,
                'visit_type'     => 'Garden / Shed',
                'booking_status' => 'confirmed',
                'pet_indices'    => [0],
            ],
        ];

        foreach ($bookings as $data) {
            $petIndices = $data['pet_indices'];
            unset($data['pet_indices']);
            $data['goormer_spacer_id'] = $spacerIds[array_rand($spacerIds)];
            $data['extra_add_ons'] = $serviceAddOns[$data['service']] ?? [];
            $data['staff'] = in_array($data['booking_status'], ['confirmed', 'completed'], true)
                ? $staffRoster[array_rand($staffRoster)]
                : null;
            $data['rating'] = $data['booking_status'] === 'completed'
                ? $completedRatings[array_rand($completedRatings)]
                : null;
            $data['cancelled_by'] = $data['booking_status'] === 'cancelled' ? 'Pet Owner' : null;
            $data['refund_amount'] = $data['booking_status'] === 'cancelled' ? (float) $data['amount'] : null;
            $data['refund_status'] = $data['booking_status'] === 'cancelled'
                ? $refundStatuses[array_rand($refundStatuses)]
                : null;
            $data['discount'] = in_array($data['booking_status'], ['completed', 'cancelled'], true)
                ? $discountSamples[array_rand($discountSamples)]
                : 0.0;

            // Idempotent seeding: update existing seeded bookings and always enforce
            // correct pivot mapping instead of creating duplicates.
            $booking = Booking::updateOrCreate(
                [
                    'pet_owner_id' => $data['pet_owner_id'],
                    'goormer_spacer_id' => $data['goormer_spacer_id'],
                    'date' => $data['date'],
                    'time' => $data['time'],
                    'service' => $data['service'],
                ],
                [
                    'amount' => $data['amount'],
                    'refund_amount' => $data['refund_amount'],
                    'discount' => $data['discount'],
                    'extra_add_ons' => $data['extra_add_ons'],
                    'staff' => $data['staff'],
                    'rating' => $data['rating'],
                    'visit_type' => $data['visit_type'],
                    'booking_status' => $data['booking_status'],
                    'cancelled_by' => $data['cancelled_by'],
                    'refund_status' => $data['refund_status'],
                ]
            );

            $petIds = collect($petIndices)
                ->map(fn (int $index) => optional($pets->get($index))->id)
                ->filter()
                ->values()
                ->toArray();

            // Ensure each seeded booking has at least one linked pet.
            if (empty($petIds) && $pets->isNotEmpty()) {
                $petIds = [$pets->first()->id];
            }

            $booking->pets()->sync($petIds);
        }
    }
}
