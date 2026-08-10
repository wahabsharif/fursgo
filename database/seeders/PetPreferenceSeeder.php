<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\PetPreference;
use Illuminate\Database\Seeder;

class PetPreferenceSeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'groomer@dev.com')->first();

        if (! $groomerSpacer) {
            $this->command?->warn('PetPreferenceSeeder skipped: groomer@dev.com not found in goormer_spacer_profiles.');
            return;
        }

        PetPreference::updateOrCreate(
            [
                'groomer_spacer_id' => $groomerSpacer->id,
            ],
            [
                'pet_compatibility' => [
                    'pet_types' => ['cat', 'dog', 'other'],
                    'other_pets' => ['turtle', 'rabbit', 'fish', 'horse', 'reptile'],
                    'pet_sizes' => ['small', 'medium', 'large'],
                ],
            ]
        );
    }
}
