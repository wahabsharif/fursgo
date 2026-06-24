<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\AddOnSeeder;
use Database\Seeders\BookingSeeder;
use Database\Seeders\DevUserSeeder;
use Database\Seeders\PaymentSeeder;
use Database\Seeders\PetMedicationDetailSeeder;
use Database\Seeders\PetPreferenceSeeder;
use Database\Seeders\ReviewSeeder;
use Database\Seeders\ServiceAreaSeeder;
use Database\Seeders\ServicePolicySeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\StaffSeeder;
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
        ]);
    }
}
