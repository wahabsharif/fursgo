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

        GroomerSpacerProfile::updateOrCreate(
            ['email' => 'dev@dev.com'],
            [
                'full_name' => 'Dev',
                'password' => Hash::make($plainPassword),
                'user_type' => 'dev',
                'profile_visit' => 125,
            ]
        );

    }
}
