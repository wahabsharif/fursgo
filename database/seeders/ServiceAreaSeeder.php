<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\ServiceArea;
use Illuminate\Database\Seeder;

class ServiceAreaSeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'groomer@dev.com')->first();

        if (!$groomerSpacer) {
            $this->command?->warn('ServiceAreaSeeder skipped: groomer@dev.com not found in groomer_spacer_profiles.');

            return;
        }

        $mapColors = ['#8EBFDF', '#F0A36A', '#B7D48A'];

        $areas = [
            [
                'name' => 'Waterloo South Bank',
                'radius' => 0.5,
                'latitude' => 51.50406,
                'longitude' => -0.11664,
                'address' => 'Waterloo Station, London SE1',
                'is_paused' => false,
            ],
            [
                'name' => 'Southwark',
                'radius' => 1.5,
                'latitude' => 51.50135,
                'longitude' => -0.09399,
                'address' => 'Borough High St, London SE1',
                'is_paused' => false,
            ],
            [
                'name' => 'Cannon Street',
                'radius' => 1.0,
                'latitude' => 51.5113,
                'longitude' => -0.0904,
                'address' => 'Cannon St, London EC4',
                'is_paused' => true,
            ],
        ];

        foreach ($areas as $index => $area) {
            ServiceArea::updateOrCreate(
                [
                    'groomer_spacer_id' => $groomerSpacer->id,
                    'name' => $area['name'],
                ],
                [
                    'radius' => $area['radius'],
                    'latitude' => $area['latitude'],
                    'longitude' => $area['longitude'],
                    'address' => $area['address'],
                    'map_color' => $mapColors[$index % count($mapColors)],
                    'is_paused' => (bool) ($area['is_paused'] ?? false),
                ]
            );
        }
    }
}
