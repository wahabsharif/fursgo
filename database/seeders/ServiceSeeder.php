<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedForEmail('dev@dev.com', $this->groomerCatalog());
        $this->seedForEmail('space@dev.com', $this->spaceCatalog());
    }

    /**
     * @param  list<array{name: string, description: string, base_duration: int, buffer_time: int, base_price: float, prices: array{small: float, medium: float, large: float}, durations?: array{small: int, medium: int, large: int}}>  $catalog
     */
    private function seedForEmail(string $email, array $catalog): void
    {
        $profile = GroomerSpacerProfile::where('email', $email)->first();

        if (!$profile) {
            $this->command?->warn("ServiceSeeder skipped: {$email} not found in goormer_spacer_profiles.");

            return;
        }

        foreach ($catalog as $row) {
            $durations = $row['durations'] ?? [
                'small' => $row['base_duration'],
                'medium' => (int) round($row['base_duration'] * 1.5),
                'large' => $row['base_duration'] * 2,
            ];

            Service::updateOrCreate(
                [
                    'groomer_spacer_id' => $profile->id,
                    'service_name' => $row['name'],
                ],
                [
                    'description' => $row['description'],
                    'pet_compatibility' => [
                        'pet_types' => ['cat', 'dog', 'other'],
                        'other_pets' => ['rabbit', 'guinea pig'],
                        'pet_sizes' => ['small', 'medium', 'large'],
                    ],
                    'duration' => [
                        'base_duration' => $row['base_duration'],
                        'buffer_time' => $row['buffer_time'],
                        'duration_by_size' => $durations,
                    ],
                    'pricing' => [
                        'base_price' => $row['base_price'],
                        'overtime_charge' => ['price' => 10, 'per' => '15 min'],
                        'pricing_by_size' => $row['prices'],
                    ],
                    'add_ons_compatibility' => true,
                    'visibility_controls' => true,
                ]
            );
        }
    }

    /**
     * @return list<array{name: string, description: string, base_duration: int, buffer_time: int, base_price: float, prices: array{small: float, medium: float, large: float}, durations?: array{small: int, medium: int, large: int}}>
     */
    private function groomerCatalog(): array
    {
        return [
            [
                'name' => 'Standard Grooming',
                'description' => 'Basic grooming service for compatible pets.',
                'base_duration' => 60,
                'buffer_time' => 15,
                'base_price' => 25,
                'prices' => ['small' => 25, 'medium' => 35, 'large' => 45],
            ],
            [
                'name' => 'Full Groom',
                'description' => 'Wash, cut, styling, and nail trim.',
                'base_duration' => 90,
                'buffer_time' => 15,
                'base_price' => 45,
                'prices' => ['small' => 45, 'medium' => 60, 'large' => 80],
                'durations' => ['small' => 75, 'medium' => 90, 'large' => 120],
            ],
            [
                'name' => 'Bath & Brush',
                'description' => 'Gentle bath with brush-out and dry.',
                'base_duration' => 45,
                'buffer_time' => 10,
                'base_price' => 30,
                'prices' => ['small' => 30, 'medium' => 40, 'large' => 55],
            ],
            [
                'name' => 'Nail Trim',
                'description' => 'Quick nail trim appointment.',
                'base_duration' => 20,
                'buffer_time' => 5,
                'base_price' => 15,
                'prices' => ['small' => 15, 'medium' => 18, 'large' => 22],
            ],
            [
                'name' => 'Face Trim',
                'description' => 'Face tidy and eye-area trim.',
                'base_duration' => 30,
                'buffer_time' => 10,
                'base_price' => 20,
                'prices' => ['small' => 20, 'medium' => 25, 'large' => 30],
            ],
            [
                'name' => 'Ear Cleaning',
                'description' => 'Ear clean and check.',
                'base_duration' => 15,
                'buffer_time' => 5,
                'base_price' => 12,
                'prices' => ['small' => 12, 'medium' => 14, 'large' => 16],
            ],
        ];
    }

    /**
     * @return list<array{name: string, description: string, base_duration: int, buffer_time: int, base_price: float, prices: array{small: float, medium: float, large: float}, durations?: array{small: int, medium: int, large: int}}>
     */
    private function spaceCatalog(): array
    {
        return [
            [
                'name' => 'Hourly',
                'description' => 'Hourly space hire for grooming use.',
                'base_duration' => 60,
                'buffer_time' => 15,
                'base_price' => 25,
                'prices' => ['small' => 25, 'medium' => 25, 'large' => 25],
                'durations' => ['small' => 60, 'medium' => 60, 'large' => 60],
            ],
            [
                'name' => 'Half-Day',
                'description' => 'Half-day space booking.',
                'base_duration' => 240,
                'buffer_time' => 15,
                'base_price' => 80,
                'prices' => ['small' => 80, 'medium' => 80, 'large' => 80],
                'durations' => ['small' => 240, 'medium' => 240, 'large' => 240],
            ],
            [
                'name' => 'Full-Day',
                'description' => 'Full-day space booking.',
                'base_duration' => 480,
                'buffer_time' => 15,
                'base_price' => 120,
                'prices' => ['small' => 120, 'medium' => 120, 'large' => 120],
                'durations' => ['small' => 480, 'medium' => 480, 'large' => 480],
            ],
        ];
    }
}
