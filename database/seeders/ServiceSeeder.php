<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'dev@dev.com')->first();

        if (! $groomerSpacer) {
            $this->command?->warn('ServiceSeeder skipped: dev@dev.com not found in goormer_spacer_profiles.');
            return;
        }

        Service::updateOrCreate(
            [
                'groomer_spacer_id' => $groomerSpacer->id,
                'service_name' => 'Standard Grooming',
            ],
            [
                'description' => 'Basic grooming service for compatible pets.',
                'pet_compatibility' => [
                    'pet_types' => ['cat', 'dog', 'other'],
                    'other_pets' => ['turtle', 'rabbit', 'fish', 'horse', 'reptile'],
                    'pet_sizes' => ['small', 'medium', 'large'],
                ],
                'duration' => [
                    'base_duration' => 60,
                    'buffer_time' => 15,
                    'duration_by_size' => ['small' => 60, 'medium' => 90, 'large' => 120],
                ],
                'pricing' => [
                    'base_price' => 25,
                    'overtime_charge' => ['price' => 10, 'per' => '15 min'],
                    'pricing_by_size' => ['small' => 25, 'medium' => 35, 'large' => 45],
                ],
                'add_ons_compatibility' => true,
                'visibility_controls' => true,
            ]
        );
    }
}
