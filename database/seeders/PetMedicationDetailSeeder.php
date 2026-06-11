<?php

namespace Database\Seeders;

use App\Models\PetDetail;
use App\Models\PetMedicationDetail;
use Illuminate\Database\Seeder;

class PetMedicationDetailSeeder extends Seeder
{
    public function run(): void
    {
        $samples = $this->sampleDefinitions();

        PetDetail::query()
            ->select(['id', 'user_id', 'name', 'pet_type'])
            ->each(function (PetDetail $pet) use ($samples) {
                $sample = $samples[$pet->name] ?? $this->defaultSample($pet);

                PetMedicationDetail::updateOrCreate(
                    ['pet_detail_id' => $pet->id],
                    array_merge($sample, [
                        'pet_owner_id' => $pet->user_id,
                    ])
                );
            });
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    private function sampleDefinitions(): array
    {
        return [
            'Buddy' => [
                'last_verified' => now()->subDays(12),
                'veterinary_clinic' => 'Manchester Paws Veterinary Clinic',
                'vaccinations' => [
                    ['name' => 'Rabies', 'date' => now()->subYear()->format('Y-m-d'), 'next_due' => now()->subDays(7)->format('Y-m-d')],
                    ['name' => 'Distemper', 'date' => now()->subMonths(3)->format('Y-m-d'), 'next_due' => now()->addYear()->subDay()->format('Y-m-d')],
                    ['name' => 'Parvovirus', 'date' => now()->subMonths(3)->format('Y-m-d'), 'next_due' => now()->addYear()->subDay()->format('Y-m-d')],
                ],
                'health_conditions' => [
                    'Mild hip dysplasia',
                    'Managed with joint supplements',
                ],
                'current_medication' => [
                    'Glucosamine chew — 1 daily, morning',
                ],
                'allergies' => [
                    'Chicken',
                ],
                'emergency_contact' => [
                    'veterinary_clinic' => 'Manchester Paws Veterinary Clinic',
                    'phone' => '+44 161 555 0142',
                ],
                'groomer_guidance_notes' => 'Very social but can get overexcited around other dogs. Use a non-slip mat during bathing.',
                'preferred_grooming_style' => [
                    'Natural retriever trim',
                    'Medium coat length',
                    'Tidy face and feet only',
                ],
                'grooming_behaviour' => [
                    'Friendly temperament',
                    'Loud dryer noise',
                    'Treat rewards',
                    'Short breaks',
                ],
                'tolerance_levels' => [
                    'bathing' => 'Comfortable',
                    'dryer' => 'Slightly nervous',
                    'nail_trim' => 'Sensitive paws',
                ],
                'product_preferences' => 'Oatmeal shampoo, hypoallergenic conditioner.',
                'handling_notes' => 'Approach from the side. Loves peanut butter during nail trims.',
                'photo_gallery' => [
                    'https://images.unsplash.com/photo-1518717758536-85ae29035b6d?auto=format&fit=crop&w=600&q=80',
                    'https://images.unsplash.com/photo-1552053831-71594a27632d?auto=format&fit=crop&w=600&q=80',
                ],
                'groomer_notes' => [
                    [
                        'date' => '2026-04-15',
                        'title' => "Sarah's Grooming Studio",
                        'note' => 'Bella behaved well during bath and brushing. Nail trimming required extra care due to paw sensitivity.',
                    ],
                ],
                'owner_notes' => [
                    [
                        'date' => '2026-04-12',
                        'title' => '',
                        'note' => 'Bella recently had a small skin irritation on her back leg but it has healed.',
                    ],
                ],
            ],
            'Luna' => [
                'last_verified' => now()->subDays(45),
                'veterinary_clinic' => 'Small Pets Vet Leeds',
                'vaccinations' => [
                    ['name' => 'RHDV2', 'date' => now()->subMonths(2)->format('Y-m-d'), 'next_due' => now()->addMonths(10)->format('Y-m-d')],
                ],
                'health_conditions' => [],
                'current_medication' => [],
                'allergies' => [],
                'emergency_contact' => [
                    'veterinary_clinic' => 'Small Pets Vet Leeds',
                    'phone' => '+44 113 555 0198',
                ],
                'groomer_guidance_notes' => 'Very calm. Keep sessions short and quiet.',
                'preferred_grooming_style' => [
                    'Light brush-out only',
                    'Natural coat length',
                ],
                'grooming_behaviour' => [
                    'Calm temperament',
                    'Sudden handling of hind legs',
                ],
                'tolerance_levels' => [
                    'bathing' => 'Slightly nervous',
                    'dryer' => 'Slightly nervous',
                    'nail_trim' => 'Sensitive paws',
                ],
                'product_preferences' => 'Fragrance-free products only.',
                'handling_notes' => 'Support hindquarters when lifting.',
                'photo_gallery' => [
                    'https://images.unsplash.com/photo-1585110396000-c9ffd4e4b308?auto=format&fit=crop&w=600&q=80',
                ],
                'groomer_notes' => [],
                'owner_notes' => [
                    [
                        'date' => now()->subDays(14)->toDateString(),
                        'title' => 'First groom',
                        'note' => 'First professional groom — go slowly.',
                    ],
                ],
            ],
            'Rocky' => [
                'last_verified' => now()->subDays(8),
                'veterinary_clinic' => 'London Bridge Animal Hospital',
                'vaccinations' => [
                    ['name' => 'Rabies', 'date' => now()->subYear()->format('Y-m-d'), 'next_due' => now()->subMonth()->format('Y-m-d')],
                    ['name' => 'DHPP', 'date' => now()->subMonths(4)->format('Y-m-d'), 'next_due' => now()->addMonths(8)->format('Y-m-d')],
                ],
                'health_conditions' => [
                    'Sensitive skin',
                    'Prone to irritation after harsh shampoos',
                ],
                'current_medication' => [],
                'allergies' => [
                    'Wheat',
                    'Perfumed products',
                ],
                'emergency_contact' => [
                    'veterinary_clinic' => 'London Bridge Animal Hospital',
                    'phone' => '+44 20 7946 0958',
                ],
                'groomer_guidance_notes' => 'Use hypoallergenic products. Monitor skin after bath.',
                'preferred_grooming_style' => [
                    'Clean face fold trim',
                    'Short coat',
                ],
                'grooming_behaviour' => [
                    'Anxious temperament',
                    'Nail grinder',
                    'Face handling',
                    'Lick mat',
                    'Low dryer setting',
                ],
                'tolerance_levels' => [
                    'bathing' => 'Slightly nervous',
                    'dryer' => 'Slightly nervous',
                    'nail_trim' => 'Sensitive paws',
                ],
                'product_preferences' => 'Hypoallergenic shampoo, unscented conditioner.',
                'handling_notes' => 'Needs frequent breaks. Wipe face folds gently.',
                'photo_gallery' => [
                    'https://images.unsplash.com/photo-1583337130417-3346a1be7dee?auto=format&fit=crop&w=600&q=80',
                ],
                'groomer_notes' => [
                    [
                        'date' => now()->subDays(10)->toDateString(),
                        'title' => 'Post-bath skin reaction',
                        'note' => 'Mild redness after last bath — switched products.',
                    ],
                ],
                'owner_notes' => [
                    [
                        'date' => now()->subDays(20)->toDateString(),
                        'title' => 'Skin monitoring',
                        'note' => 'Please call if skin looks irritated post-groom.',
                    ],
                ],
            ],
            'Bailey' => [
                'last_verified' => now()->subDays(3),
                'veterinary_clinic' => 'Sheffield Senior Pet Care',
                'vaccinations' => [
                    ['name' => 'Rabies', 'date' => now()->subMonths(6)->format('Y-m-d'), 'next_due' => now()->addMonths(6)->format('Y-m-d')],
                ],
                'health_conditions' => [
                    'Arthritis',
                    'Stiff in mornings — avoid long standing sessions',
                    'Hearing loss — approach from front',
                ],
                'current_medication' => [
                    'Carprofen 75mg — twice daily with food',
                ],
                'allergies' => [],
                'emergency_contact' => [
                    'veterinary_clinic' => 'Sheffield Senior Pet Care',
                    'phone' => '+44 114 555 0177',
                ],
                'groomer_guidance_notes' => 'Senior dog — use supportive harness and padded table.',
                'preferred_grooming_style' => [
                    'Comfort trim',
                    'Medium coat length',
                ],
                'grooming_behaviour' => [
                    'Gentle temperament',
                    'Extended standing',
                ],
                'tolerance_levels' => [
                    'bathing' => 'Comfortable',
                    'dryer' => 'Slightly nervous',
                    'nail_trim' => 'Comfortable',
                ],
                'product_preferences' => 'Moisturising shampoo for dry coat.',
                'handling_notes' => 'Allow rest breaks every 10 minutes.',
                'photo_gallery' => [
                    'https://images.unsplash.com/photo-1633722712433-6d54ae3421d6?auto=format&fit=crop&w=600&q=80',
                ],
                'groomer_notes' => [
                    [
                        'date' => now()->subDays(120)->toDateString(),
                        'title' => 'Arthritis handling',
                        'note' => 'Handled well despite arthritis.',
                    ],
                ],
                'owner_notes' => [
                    [
                        'date' => now()->subDays(90)->toDateString(),
                        'title' => 'Medication schedule',
                        'note' => 'Medication given at 7am and 7pm.',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private function defaultSample(PetDetail $pet): array
    {
        return [
            'last_verified' => null,
            'veterinary_clinic' => null,
            'vaccinations' => [],
            'health_conditions' => [],
            'current_medication' => [],
            'allergies' => [],
            'emergency_contact' => null,
            'groomer_guidance_notes' => "Standard handling for {$pet->name} ({$pet->pet_type}).",
            'preferred_grooming_style' => [
                'Owner preference not specified',
            ],
            'grooming_behaviour' => [
                'Temperament unknown',
            ],
            'tolerance_levels' => [
                'bathing' => 'Comfortable',
                'dryer' => 'Comfortable',
                'nail_trim' => 'Comfortable',
            ],
            'product_preferences' => null,
            'handling_notes' => null,
            'photo_gallery' => [],
            'groomer_notes' => [],
            'owner_notes' => [],
        ];
    }
}
