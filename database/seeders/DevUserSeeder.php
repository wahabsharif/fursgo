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
            ]
        );

        $devEmail = 'dev@dev.com';
        $idProofPath = 'dev-seed/id-documents/owner-id-sample.jpg';

        GroomerSpacerProfile::updateOrCreate(
            ['email' => $devEmail],
            [
                'full_name' => 'Dev User',
                'information_accuracy_confirmed' => true,
                'password' => Hash::make($plainPassword),
                'user_type' => 'groomer',
                'account_type' => 'registered_business',
                'select_location_type' => ['home_studio', 'house_visit', 'mobile_van'],
                'id_document_paths' => [$idProofPath],
                'business_details' => [
                    'business_name' => 'Dev Grooming Ltd',
                    'business_registration_number' => '12345678',
                    'business_phone' => '+441632960000',
                    'business_email' => $devEmail,
                    'business_owner_id_images' => [$idProofPath],
                ],
                'payout_details' => [
                    'account_holder_name' => 'Dev User',
                    'account_number' => '12345678',
                    'sort_code' => '12-34-56',
                    'iban' => 'GB82WEST12345698765432',
                ],
                'insurance_details' => [
                    'insurance_certificate_paths' => ['dev-seed/insurance/certificate-sample.pdf'],
                ],
                'freelance_details' => null,
                'business_basics' => [
                    'display_name' => 'Dev Grooming',
                    'tagline' => 'Calm, kind grooms for every coat.',
                    'bio' => 'Development seed profile with representative business basics data.',
                    'profile_photo_path' => 'dev-seed/avatars/profile-sample.jpg',
                    'gallery_paths' => [
                        'dev-seed/gallery/sample-1.jpg',
                        'dev-seed/gallery/sample-2.jpg',
                    ],
                ],
                'groomer_business_profile' => [
                    'experience' => '6+ years breed-specific trims and hand-stripping.',
                    'specialties' => 'Dog, Cat',
                    'pet_specialties' => ['dog', 'cat'],
                    'specialty_other' => '',
                    'pet_sizes' => ['small', 'medium', 'large'],
                    'custom_addons' => ['Teeth brushing'],
                    'selected_addons' => ['nail_clipping', 'ear_cleaning'],
                    'services' => [
                        'full_groom' => ['price' => '45', 'description' => 'Bath, dry, clip, nails'],
                        'face_trim' => ['price' => '20'],
                    ],
                    'addon_pricing' => [
                        'flea_tick' => ['price' => '12', 'description' => 'Flea & tick shampoo'],
                        'fast_dry' => ['price' => '8'],
                    ],
                ],
                'spacer_business_profile' => null,
                'profile_visit' => 125,
                'legal_policy_agreements' => true,
            ]
        );
    }
}
