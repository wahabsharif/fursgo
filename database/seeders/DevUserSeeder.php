<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DevUserSeeder extends Seeder
{
    /**
     * Seed the development user account.
     */
    public function run(): void
    {
        $plainPassword = '@7415369Dev';

        User::updateOrCreate(
            ['email' => 'dev@dev.com'],
            [
                'name' => 'Dev',
                'password' => Hash::make($plainPassword),
                'user_type' => 'dev',
                'user_status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'groomer@dev.com'],
            [
                'name' => 'Dev Groomer',
                'password' => Hash::make($plainPassword),
                'user_type' => 'groomer',
                'user_status' => 'active',
            ]
        );

        User::updateOrCreate(
            ['email' => 'space@dev.com'],
            [
                'name' => 'Dev Space',
                'password' => Hash::make($plainPassword),
                'user_type' => 'space',
                'user_status' => 'active',
            ]
        );

        $devEmail = 'dev@dev.com';
        $groomerEmail = 'groomer@dev.com';
        $spaceEmail = 'space@dev.com';

        $groomerProfileData = [
            'full_name' => 'Dev User',
            'information_accuracy_confirmed' => true,
            'password' => Hash::make($plainPassword),
            'user_type' => 'groomer',
            'account_type' => 'registered_business',
            'select_location_type' => ['home_studio', 'house_visit', 'mobile_van'],
            'id_document_paths' => [],
            'business_details' => [
                'business_name' => 'Dev Grooming Ltd',
                'business_registration_number' => '12345678',
                'business_phone' => '+441632960000',
                'business_email' => $devEmail,
                'business_owner_id_images' => [],
            ],
            'payout_details' => [
                'bank' => 'Barclays',
                'account_holder_name' => 'Dev User',
                'account_number' => '12345678',
                'sort_code' => '12-34-56',
                'iban' => 'GB82WEST12345698765432',
                'payout_frequency' => 'Weekly',
            ],
            'insurance_details' => [
                'insurance_certificate_paths' => [],
                'insurance_certificate_expiry_date' => '2026-01-12',
            ],
            'freelance_details' => null,
            'business_basics' => [
                'display_name' => 'Dev Grooming',
                'tagline' => 'Calm, kind grooms for every coat.',
                'bio' => 'Development seed profile with representative business basics data.',
                'profile_photo_path' => '',
                'gallery_paths' => [],
            ],
            'groomer_business_profile' => [
                'experience' => '6+ years breed-specific trims and hand-stripping.',
                'specialties' => 'Dog, Cat',
                'pet_specialties' => ['dog', 'cat'],
                'specialty_other' => '',
                'pet_sizes' => ['small', 'medium', 'large'],
                'custom_addons' => [],
                'selected_addons' => [
                    'Anti-Itch Treatment',
                    'Fast-Dry Service (express grooming)',
                ],
                'services' => [
                    'full_groom' => ['price' => '45', 'description' => 'Bath, dry, clip, nails'],
                    'face_trim' => ['price' => '20'],
                ],
                'addon_pricing' => [
                    'anti_itch_treatment' => [
                        'name' => 'Anti-Itch Treatment',
                        'price' => '10',
                        'description' => 'Soothing anti-itch wash',
                    ],
                    'fast_dry' => [
                        'name' => 'Fast-Dry Service (express grooming)',
                        'price' => '8',
                    ],
                ],
            ],
            'spacer_business_profile' => null,
            'profile_visit' => 1240,
            'legal_policy_agreements' => true,
            'auto_accept_booking' => true,
        ];

        GroomerSpacerProfile::updateOrCreate(
            ['email' => $devEmail],
            $groomerProfileData
        );

        GroomerSpacerProfile::updateOrCreate(
            ['email' => $groomerEmail],
            array_merge($groomerProfileData, [
                'full_name' => 'Dev Groomer',
                'password' => Hash::make($plainPassword),
                'business_details' => array_merge($groomerProfileData['business_details'], [
                    'business_email' => $groomerEmail,
                ]),
                'payout_details' => array_merge($groomerProfileData['payout_details'], [
                    'account_holder_name' => 'Dev Groomer',
                ]),
                'business_basics' => array_merge($groomerProfileData['business_basics'], [
                    'display_name' => 'Dev Groomer Studio',
                ]),
            ])
        );

        GroomerSpacerProfile::updateOrCreate(
            ['email' => $spaceEmail],
            [
                'full_name' => 'Dev Space',
                'information_accuracy_confirmed' => true,
                'password' => Hash::make($plainPassword),
                'user_type' => 'space',
                'account_type' => 'registered_business',
                'select_location_type' => ['garden_shed', 'garage'],
                'id_document_paths' => [],
                'business_details' => [
                    'business_name' => 'Dev Space Hire Ltd',
                    'business_registration_number' => '87654321',
                    'business_phone' => '+441632960001',
                    'business_email' => $spaceEmail,
                    'business_owner_id_images' => [],
                ],
                'payout_details' => [
                    'bank' => 'Barclays',
                    'account_holder_name' => 'Dev Space',
                    'account_number' => '87654321',
                    'sort_code' => '65-43-21',
                    'iban' => 'GB82WEST12345698765433',
                    'payout_frequency' => 'Weekly',
                ],
                'insurance_details' => [
                    'insurance_certificate_paths' => [],
                    'insurance_certificate_expiry_date' => '2026-01-12',
                ],
                'freelance_details' => null,
                'business_basics' => [
                    'display_name' => 'Dev Space Studio',
                    'tagline' => 'Flexible spaces for calm, professional pet care.',
                    'bio' => 'Development seed space profile with representative rental options.',
                    'profile_photo_path' => '',
                    'gallery_paths' => [],
                ],
                'groomer_business_profile' => null,
                'spacer_business_profile' => [
                    'experience' => 'Purpose-built hire spaces for groomers and pet pros.',
                    'space_types' => ['Garden / Shed', 'Garage'],
                    'services' => [
                        'hourly' => ['name' => 'Hourly', 'price' => '25', 'description' => '1 hour access'],
                        'half_day' => ['name' => 'Half-Day', 'price' => '80', 'meta' => '(4 hours)'],
                        'full_day' => ['name' => 'Full-Day', 'price' => '120', 'meta' => '(8 hours)'],
                    ],
                    'addon_pricing' => [
                        'storage_locker' => [
                            'name' => 'Storage Locker',
                            'price' => '8',
                        ],
                        'deep_clean' => [
                            'name' => 'Deep Clean',
                            'price' => '20',
                        ],
                        'after_hours' => [
                            'name' => 'After-Hours Access',
                            'price' => '10',
                        ],
                    ],
                ],
                'profile_visit' => 980,
                'legal_policy_agreements' => true,
                'auto_accept_booking' => true,
            ]
        );
    }
}
