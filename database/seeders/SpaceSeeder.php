<?php

namespace Database\Seeders;

use App\Models\Space;
use Illuminate\Database\Seeder;

class SpaceSeeder extends Seeder
{
    public function run(): void
    {
        Space::create([
            'name' => 'Cozy Indoor Space',
            'description' => 'A warm and safe indoor grooming space.',
            'distance' => 1.2,
            'price' => 30,
            'image_url' => 'images/space1.png',
            'venue_type' => 'private_rooms',
            'amenities' => json_encode(['Heating', 'Towels']),
        ]);
    }
}
