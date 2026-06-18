<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\GroomerSpacerProfile;
use App\Models\Payment;
use App\Models\PetDetail;
use App\Models\User;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    public function run(): void
    {
        $devEmail = 'dev@dev.com';
        $groomerSpacer = GroomerSpacerProfile::query()
            ->where(['email' => $devEmail])
            ->first();

        if (!$groomerSpacer) {
            $this->command?->warn("PaymentSeeder skipped: {$devEmail} not found in goormer_spacer_profiles.");

            return;
        }

        $owner = User::firstOrCreate(
            ['email' => 'petowner@example.com'],
            [
                'name' => 'Jane Smith',
                'password' => bcrypt('password'),
                'user_type' => 'pet_owner',
                'user_status' => 'active',
                'profile_image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80',
            ],
        );
        $owner->forceFill([
            'profile_image' => 'https://images.unsplash.com/photo-1494790108377-be9c29b29330?auto=format&fit=crop&w=600&q=80',
        ])->save();

        $petDefinitions = [
            ['name' => 'Bella', 'pet_type' => 'Rabbit', 'breed' => 'Mini Lop', 'sex' => 'female', 'birthday' => '2021-04-12', 'weight' => 1.6],
            ['name' => 'Louis', 'pet_type' => 'Dog', 'breed' => 'Beagle', 'sex' => 'male', 'birthday' => '2019-09-03', 'weight' => 12.4],
            ['name' => 'Surf', 'pet_type' => 'Turtle', 'breed' => 'Red-Eared Slider', 'sex' => 'male', 'birthday' => '2017-02-18', 'weight' => 0.8],
        ];

        $pets = collect($petDefinitions)->map(function (array $pet) use ($owner) {
            return PetDetail::firstOrCreate(
                ['user_id' => $owner->id, 'name' => $pet['name']],
                $pet,
            );
        });

        $booking = Booking::updateOrCreate(
            [
                'pet_owner_id' => $owner->id,
                'goormer_spacer_id' => $groomerSpacer->id,
                'date' => '2025-02-05',
                'time' => '10:00 - 11:00',
                'service' => 'Full Groom',
            ],
            [
                'amount' => 50.0,
                'visit_type' => 'salon',
                'booking_status' => 'completed',
                'staff' => 'Emma Wilson',
                'rating' => 4.8,
                'discount' => 0.0,
                'extra_add_ons' => [],
            ],
        );

        $booking->pets()->sync($pets->map(fn(PetDetail $pet) => $pet->id)->all());

        $paymentSamples = [
            ['date' => '2025-02-05', 'pet_name' => 'Bella', 'amount' => 50.0, 'status' => 'paid', 'payment_method' => 'Debit/Credit Card'],
            ['date' => '2025-02-06', 'pet_name' => 'Louis', 'amount' => 50.0, 'status' => 'failed', 'payment_method' => 'PayPal'],
            ['date' => '2025-02-07', 'pet_name' => 'Surf', 'amount' => 25.0, 'status' => 'refunded', 'payment_method' => 'Debit/Credit Card'],
            ['date' => '2025-02-07', 'pet_name' => 'Bella', 'amount' => 25.0, 'status' => 'paid', 'payment_method' => 'PayPal'],
        ];

        foreach ($paymentSamples as $sample) {
            $pet = $pets->first(fn(PetDetail $pet) => $pet->name === $sample['pet_name']);

            Payment::updateOrCreate(
                [
                    'booking_id' => $booking->id,
                    'pet_owner_id' => $owner->id,
                    'pet_detail_id' => $pet?->id,
                    'date' => $sample['date'],
                    'status' => $sample['status'],
                ],
                [
                    'service_type' => 'Full Groom',
                    'amount' => $sample['amount'],
                    'payment_method' => $sample['payment_method'],
                ],
            );
        }

        $petOwnerIdColumn = 'pet_owner_id';
        $completedBookings = Booking::query()
            ->where(['booking_status' => 'completed'])
            ->whereNot($petOwnerIdColumn, $owner->id)
            ->with('pets')
            ->get();

        $paymentMethods = ['Debit/Credit Card', 'PayPal'];

        foreach ($completedBookings as $index => $completedBooking) {
            $pet = $completedBooking->pets->first();

            Payment::updateOrCreate(
                [
                    'booking_id' => $completedBooking->id,
                    'pet_owner_id' => $completedBooking->pet_owner_id,
                    'pet_detail_id' => $pet?->id,
                    'date' => $completedBooking->date?->toDateString(),
                ],
                [
                    'service_type' => $completedBooking->service,
                    'amount' => $completedBooking->amount,
                    'status' => 'paid',
                    'payment_method' => $paymentMethods[$index % count($paymentMethods)],
                ],
            );
        }

        $this->seedSpacePayments();
    }

    private function seedSpacePayments(): void
    {
        $spaceSpacer = GroomerSpacerProfile::firstOrCreate(
            ['email' => 'space@dev.com'],
            [
                'full_name' => 'Dev Space',
                'password' => bcrypt('@7415369Dev'),
                'user_type' => 'space',
                'account_type' => 'registered_business',
                'information_accuracy_confirmed' => true,
                'legal_policy_agreements' => true,
            ],
        );

        $spaceClient = User::firstOrCreate(
            ['email' => 'space.client@example.com'],
            [
                'name' => 'Claire Smith',
                'password' => bcrypt('password'),
                'user_type' => 'groomer',
                'user_status' => 'active',
                'profile_image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=600&q=80',
            ],
        );
        $spaceClient->forceFill([
            'profile_image' => 'https://images.unsplash.com/photo-1438761681033-6461ffad8d80?auto=format&fit=crop&w=600&q=80',
        ])->save();

        $spaceBooking = Booking::updateOrCreate(
            [
                'pet_owner_id' => $spaceClient->id,
                'goormer_spacer_id' => $spaceSpacer->id,
                'date' => '2025-02-05',
                'time' => '14:30 - 18:30',
                'service' => 'Half-Day',
            ],
            [
                'amount' => 80.0,
                'visit_type' => 'Garden/Shed',
                'booking_status' => 'completed',
                'staff' => 'Emma Wilson',
                'rating' => 4.9,
                'discount' => 25.0,
                'extra_add_ons' => [
                    ['label' => 'Storage Locker', 'amount' => 8],
                    ['label' => 'Deep Clean', 'amount' => 20],
                    ['label' => 'After-Hours Access', 'amount' => 10],
                ],
            ],
        );

        $spacePaymentSamples = [
            ['date' => '2025-02-05', 'service_type' => 'Half-Day', 'amount' => 80.0, 'status' => 'paid', 'payment_method' => 'Debit/Credit Card'],
            ['date' => '2025-02-06', 'service_type' => 'Full-Day', 'amount' => 120.0, 'status' => 'failed', 'payment_method' => 'PayPal'],
            ['date' => '2025-02-07', 'service_type' => 'Hourly', 'amount' => 20.0, 'status' => 'refunded', 'payment_method' => 'Debit/Credit Card'],
            ['date' => '2025-02-08', 'service_type' => 'Half-Day', 'amount' => 80.0, 'status' => 'paid', 'payment_method' => 'PayPal'],
        ];

        foreach ($spacePaymentSamples as $sample) {
            Payment::updateOrCreate(
                [
                    'booking_id' => $spaceBooking->id,
                    'pet_owner_id' => $spaceClient->id,
                    'pet_detail_id' => null,
                    'date' => $sample['date'],
                    'status' => $sample['status'],
                ],
                [
                    'service_type' => $sample['service_type'],
                    'amount' => $sample['amount'],
                    'payment_method' => $sample['payment_method'],
                ],
            );
        }
    }
}
