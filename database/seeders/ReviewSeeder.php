<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\GroomerSpacerProfile;
use App\Models\Review;
use Illuminate\Database\Seeder;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'groomer@dev.com')->first();

        if (!$groomerSpacer) {
            $this->command?->warn('ReviewSeeder skipped: groomer@dev.com not found in goormer_spacer_profiles.');

            return;
        }

        $reviewSamples = [
            'Buddy was amazing—friendly, patient, and super gentle with my dog. The grooming was done carefully and my pup looks and smells fantastic. Highly recommend!',
            'Really happy with the service. Luna was handled with care and the team kept me updated throughout. Will book again.',
            'Professional, punctual, and thorough. Max came home calm and looking great.',
            'Whiskers is usually nervous around groomers but the visit went smoothly. Thank you for the gentle approach.',
            'Rocky and Daisy both looked wonderful after their groom. Great attention to detail on sensitive areas.',
            'Excellent bath and brush. Coat feels soft and shedding is much better already.',
            'Nail trim was quick and stress-free. Appreciated the friendly staff.',
        ];

        $replySamples = [
            'Thank you so much for the kind words—we loved caring for Buddy!',
            'We are glad Luna had a good experience. See you next time!',
            'Thanks for trusting us with Max. We look forward to your next visit.',
            null,
            'Rocky and Daisy were both stars today. Thanks for booking with us!',
            null,
            'Happy to help—see you again soon!',
        ];

        $clientRatings = [4.0, 4.5, 5.0, 4.0, 4.5, 5.0, 4.0];

        $completedBookings = Booking::query()
            ->where('booking_status', 'completed')
            ->whereNotNull('rating')
            ->orderBy('date')
            ->get();

        foreach ($completedBookings as $index => $booking) {
            $reviewText = $reviewSamples[$index % count($reviewSamples)];
            $replyText = $replySamples[$index % count($replySamples)];

            Review::updateOrCreate(
                ['booking_id' => $booking->id],
                [
                    'pet_owner_id' => $booking->pet_owner_id,
                    'review' => $reviewText,
                    'reply' => $replyText,
                    'rating' => filled($replyText) ? $clientRatings[$index % count($clientRatings)] : null,
                    'reply_from' => filled($replyText) ? $groomerSpacer->id : null,
                ],
            );
        }
    }
}
