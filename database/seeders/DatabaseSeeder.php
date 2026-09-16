<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::firstOrCreate(
            ['email' => 'test@example.com'],
            ['name' => 'Test User', 'password' => bcrypt('password'), 'user_status' => 'active']
        );

        $this->call([
            DevUserSeeder::class,
            AdminUserSeeder::class,
            BookingSeeder::class,
            ReviewSeeder::class,
            PaymentSeeder::class,
            PetMedicationDetailSeeder::class,
            ServiceSeeder::class,
            ServiceAreaSeeder::class,
            ServicePolicySeeder::class,
            AddOnSeeder::class,
            PetPreferenceSeeder::class,
            StaffSeeder::class,
            FaqSeeder::class,
        ]);
    }
}
