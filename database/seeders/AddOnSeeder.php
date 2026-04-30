<?php

namespace Database\Seeders;

use App\Models\AddOn;
use App\Models\GroomerSpacerProfile;
use Illuminate\Database\Seeder;

class AddOnSeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'dev@dev.com')->first();

        if (! $groomerSpacer) {
            $this->command?->warn('AddOnSeeder skipped: dev@dev.com not found in goormer_spacer_profiles.');
            return;
        }

        AddOn::updateOrCreate(
            [
                'groomer_spacer_id' => $groomerSpacer->id,
                'add_ons_name' => 'Nail Trimming Add-on',
            ],
            [
                'description' => 'Quick nail trim as an add-on service.',
                'pet_compatibility' => [
                    'pet_type' => ['cat', 'dog'],
                    'pet_size' => ['small', 'medium', 'large'],
                ],
                'duration' => [
                    'base_duration' => 15,
                    'buffer_time' => 5,
                ],
                'pricing' => [
                    'base_price' => 10,
                ],
                'add_ons_compatibility' => true,
                'visibility_controls' => true,
            ]
        );
    }
}
