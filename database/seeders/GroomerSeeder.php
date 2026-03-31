<?php

namespace Database\Seeders;

use App\Models\Groomer;
use Illuminate\Database\Seeder;

class GroomerSeeder extends Seeder
{
    public function run(): void
    {
        Groomer::create([
            'name' => 'Sarah W.',
            'studio_name' => 'Sarah’s Grooming Studio',
            'distance' => 2.5,
            'rating' => 4.3,
            'reviews_count' => 20,
            'experience_text' => 'Gentle, breed-specific trims · 6+ years experience.',
            'price' => 38,
            'image_url' => 'assets/images/card1.png',
            'tags' => json_encode(['Home Visit', 'Mobile Station']),
            'slots' => json_encode(['Mon 1, 08:30 AM', 'Tue 15, 12:30 PM', 'Wed 27, 09:15 AM']),
            'is_top_rated' => true,
        ]);

        Groomer::create([
            'name' => 'Ken T.',
            'studio_name' => 'Ken’s Grooming Studio',
            'distance' => 1.8,
            'rating' => 4.8,
            'reviews_count' => 45,
            'experience_text' => 'Precision cuts & show grooming · 10+ years.',
            'price' => 55,
            'image_url' => 'assets/images/card2.png',
            'tags' => json_encode(['Salon Visit', 'Spa Package']),
            'slots' => json_encode(['Wed 5, 10:00 AM', 'Fri 7, 02:00 PM']),
            'is_top_rated' => false,
        ]);

        Groomer::create([
            'name' => 'Cathy P.',
            'studio_name' => 'Cathy’s Mobile Grooming',
            'distance' => 3.2,
            'rating' => 4.9,
            'reviews_count' => 102,
            'experience_text' => 'Fear-free grooming · 8+ years experience.',
            'price' => 45,
            'image_url' => 'assets/images/card3.png',
            'tags' => json_encode(['Home Visit', 'Mobile Station', 'Senior Dog Specialist']),
            'slots' => json_encode(['Mon 8, 09:00 AM', 'Tue 9, 11:30 AM', 'Thu 11, 01:00 PM']),
            'is_top_rated' => true,
        ]);
    }
}
