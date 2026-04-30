<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\BookingSeeder;
use Database\Seeders\DevUserSeeder;
use Database\Seeders\ServiceSeeder;
use Database\Seeders\AddOnSeeder;
use Database\Seeders\PetPreferenceSeeder;

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
            ['name' => 'Test User', 'password' => bcrypt('password')]
        );

        $this->call([
            DevUserSeeder::class,
            BookingSeeder::class,
            ServiceSeeder::class,
            AddOnSeeder::class,
            PetPreferenceSeeder::class,
        ]);
    }
}
