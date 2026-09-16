<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Seed the default admin account for local / team development.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@fursgo.com'],
            [
                'name' => 'FursGo Admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
                'user_type' => 'admin',
                'user_status' => 'active',
            ]
        );
    }
}
