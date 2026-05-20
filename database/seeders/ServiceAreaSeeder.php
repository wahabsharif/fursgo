<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\ServiceArea;
use Illuminate\Database\Seeder;

class ServiceAreaSeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'dev@dev.com')->first();

        if (!$groomerSpacer) {
            $this->command?->warn('ServiceAreaSeeder skipped: dev@dev.com not found in groomer_spacer_profiles.');

            return;
        }

        $mapColors = ['#B8D4E8', '#FFD4B8', '#C8E8B8'];

        $areas = [
            [
                'name' => 'Waterloo South Bank',
                'radius' => 0.5,
                'latitude' => 51.50198,
                'longitude' => -0.11714,
                'address' => 'SE1 8SW',
            ],
            [
                'name' => 'Southwark',
                'radius' => 1.0,
                'latitude' => 51.5045,
                'longitude' => -0.086,
                'address' => 'SE1 1AA',
            ],
            [
                'name' => 'Cannon St',
                'radius' => 0.6,
                'latitude' => 51.5113,
                'longitude' => -0.0904,
                'address' => 'EC4N 6AP',
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
                ]
            );
        }
    }
}
