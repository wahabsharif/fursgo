<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $groomerSpacer = GroomerSpacerProfile::where('email', 'groomer@dev.com')->first();

        if (!$groomerSpacer) {
            $this->command?->warn('StaffSeeder skipped: groomer@dev.com not found in goormer_spacer_profiles.');
            return;
        }

        $defaultWorkingHours = [
            'monday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'tuesday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'wednesday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'thursday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'friday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'saturday' => ['status' => false, 'start' => '10:00', 'end' => '18:00'],
            'sunday' => ['status' => false, 'start' => '10:00', 'end' => '18:00'],
        ];

        $defaultHolidayTimeOff = [
            [
                'from' => '2026-05-20',
                'to' => '2026-05-22',
                'reason' => 'Bank holiday weekend',
            ],
            [
                'from' => '2026-06-15',
                'to' => '2026-06-22',
                'reason' => 'Summer break',
            ],
            [
                'from' => '2026-12-24',
                'to' => '2026-12-26',
                'reason' => 'Christmas closure',
            ],
        ];

        $rows = [
            [
                'name' => 'Liam Anderson',
                'phone' => '+16175550124',
                'email' => 'liam.anderson@dev.com',
                'job_title' => 'Pet Stylist',
                'image' => 'https://i.pravatar.cc/150?u=liam.anderson@dev.com',
                'working_hours' => $defaultWorkingHours,
                'holiday_time_off' => $defaultHolidayTimeOff,
                'pause_booking' => true,
            ],
            [
                'name' => 'Aisha Khan',
                'phone' => '+923001234567',
                'email' => 'aisha.khan@dev.com',
                'job_title' => 'Junior Groomer',
                'image' => 'https://i.pravatar.cc/150?u=aisha.khan@dev.com',
                'working_hours' => $defaultWorkingHours,
                'holiday_time_off' => $defaultHolidayTimeOff,
                'pause_booking' => false,
            ],
            [
                'name' => 'Noah Wilson',
                'phone' => '+447700900123',
                'email' => 'noah.wilson@dev.com',
                'job_title' => 'Bath Specialist',
                'image' => 'https://i.pravatar.cc/150?u=noah.wilson@dev.com',
                'working_hours' => $defaultWorkingHours,
                'holiday_time_off' => $defaultHolidayTimeOff,
                'pause_booking' => false,
            ],
        ];

        foreach ($rows as $row) {
            Staff::updateOrCreate(
                [
                    'goormer_spacer_profile_id' => $groomerSpacer->id,
                    'email' => $row['email'],
                ],
                [
                    'name' => $row['name'],
                    'phone' => $row['phone'],
                    'job_title' => $row['job_title'],
                    'image' => $row['image'],
                    'working_hours' => $row['working_hours'],
                    'holiday_time_off' => $row['holiday_time_off'],
                    'pause_booking' => $row['pause_booking'],
                ]
            );
        }
    }
}
