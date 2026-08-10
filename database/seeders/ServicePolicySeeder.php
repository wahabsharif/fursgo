<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\ServicePolicy;
use Illuminate\Database\Seeder;

class ServicePolicySeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'groomer@dev.com')->first();

        if (!$groomerSpacer) {
            $this->command?->warn('ServicePolicySeeder skipped: groomer@dev.com not found in goormer_spacer_profiles.');

            return;
        }

        ServicePolicy::updateOrCreate(
            [
                'goormer_spacer_profiles_id' => $groomerSpacer->id,
            ],
            [
                'cancellation_policy' => [
                    [
                        'Cancellation Window' => '24 hours before appointment',
                        'Cancellation Fee' => 'Late Cancellation Fee 50% of booking price',
                        'No Show Fee' => '100% of booking price',
                    ],
                ],
                'late_arrival_policy' => [
                    [
                        'Grace Period' => '10 minutes',
                        'Late Arrival Fee (Optional)' => '£10 after 15 mins',
                    ],
                ],
                'refund_policy' => true,
                'service_limitations' => ['No sedated pets', 'No aggressive pets without consultation', 'No severe matting without assessment', 'Weight or size restrictions apply'],
                'animal_welfare_statement' => true,
                'hygiene_safety_standards' => ['Tools sanitised between pets', 'Clean grooming table after each appointment', 'Fresh towels used per pet', 'Equipment safety checked'],
                'compliance_declaration' => true,
                'compliance_timeline' => [
                    'verify Dates' => ['12 Jan 2026', '12 Jan 2025', '12 Jan 2024', '12 Jan 2023'],
                ],
            ]
        );
    }
}
