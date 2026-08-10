<?php

namespace Database\Seeders;

use App\Models\AddOn;
use App\Models\GroomerSpacerProfile;
use Illuminate\Database\Seeder;

class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedForEmail('groomer@dev.com', $this->groomerCatalog());
        $this->seedForEmail('space@dev.com', $this->spaceCatalog());
    }

    /**
     * @param  list<array{name: string, description: string, base_duration: int, buffer_time: int, base_price: float}>  $catalog
     */
    private function seedForEmail(string $email, array $catalog): void
    {
        $profile = GroomerSpacerProfile::where('email', $email)->first();

        if (!$profile) {
            $this->command?->warn("AddOnSeeder skipped: {$email} not found in goormer_spacer_profiles.");

            return;
        }

        foreach ($catalog as $row) {
            AddOn::updateOrCreate(
                [
                    'groomer_spacer_id' => $profile->id,
                    'add_ons_name' => $row['name'],
                ],
                [
                    'description' => $row['description'],
                    'pet_compatibility' => [
                        'pet_type' => ['cat', 'dog'],
                        'pet_size' => ['small', 'medium', 'large'],
                    ],
                    'duration' => [
                        'base_duration' => $row['base_duration'],
                        'buffer_time' => $row['buffer_time'],
                    ],
                    'pricing' => [
                        'base_price' => $row['base_price'],
                    ],
                    'add_ons_compatibility' => true,
                    'visibility_controls' => true,
                ]
            );
        }
    }

    /**
     * @return list<array{name: string, description: string, base_duration: int, buffer_time: int, base_price: float}>
     */
    private function groomerCatalog(): array
    {
        return [
            [
                'name' => 'Nail Trimming Add-on',
                'description' => 'Quick nail trim as an add-on service.',
                'base_duration' => 15,
                'buffer_time' => 5,
                'base_price' => 10,
            ],
            [
                'name' => 'Teeth Brushing',
                'description' => 'Gentle teeth clean with pet-safe paste.',
                'base_duration' => 10,
                'buffer_time' => 5,
                'base_price' => 8,
            ],
            [
                'name' => 'Paw Balm',
                'description' => 'Moisturising balm for paw pads.',
                'base_duration' => 5,
                'buffer_time' => 0,
                'base_price' => 6,
            ],
            [
                'name' => 'Cologne Finish',
                'description' => 'Light finishing spray after grooming.',
                'base_duration' => 5,
                'buffer_time' => 0,
                'base_price' => 5,
            ],
            [
                'name' => 'Deshedding Treatment',
                'description' => 'Extra de-shed brush-out for heavy coats.',
                'base_duration' => 20,
                'buffer_time' => 5,
                'base_price' => 15,
            ],
        ];
    }

    /**
     * @return list<array{name: string, description: string, base_duration: int, buffer_time: int, base_price: float}>
     */
    private function spaceCatalog(): array
    {
        return [
            [
                'name' => 'Storage Locker',
                'description' => 'Secure locker for the booking duration.',
                'base_duration' => 0,
                'buffer_time' => 0,
                'base_price' => 8,
            ],
            [
                'name' => 'Deep Clean',
                'description' => 'Extra deep clean after your session.',
                'base_duration' => 30,
                'buffer_time' => 0,
                'base_price' => 20,
            ],
            [
                'name' => 'After-Hours Access',
                'description' => 'Extended access outside standard hours.',
                'base_duration' => 60,
                'buffer_time' => 0,
                'base_price' => 10,
            ],
        ];
    }
}
