<?php

namespace Database\Seeders;

use App\Models\GroomerSpacerProfile;
use App\Models\Staff;
use Illuminate\Database\Seeder;

class StaffSeeder extends Seeder
{
    public function run(): void
    {
        $profiles = GroomerSpacerProfile::query()
            ->whereIn('email', ['groomer@dev.com', 'space@dev.com'])
            ->get();

        if ($profiles->isEmpty()) {
            $this->command?->warn('StaffSeeder skipped: no groomer@dev.com / space@dev.com profiles.');

            return;
        }

        // 8h day matches Availability Figma ("Xh free" against a full working day).
        $defaultWorkingHours = [
            'monday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'tuesday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'wednesday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'thursday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'friday' => ['status' => true, 'start' => '10:00', 'end' => '18:00'],
            'saturday' => ['status' => false, 'start' => '10:00', 'end' => '18:00'],
            'sunday' => ['status' => false, 'start' => '10:00', 'end' => '18:00'],
        ];

        $defaultHolidayTimeOff = $this->availabilityHolidayRanges();

        foreach ($profiles as $groomerSpacer) {
            $isSpace = strtolower((string) $groomerSpacer->user_type) === 'space';
            $emailPrefix = $isSpace ? 'space' : 'groomer';

            $rows = [
                [
                    'name' => $groomerSpacer->full_name ?: ($isSpace ? 'Dev Space' : 'Dev Groomer'),
                    'phone' => $groomerSpacer->business_details['business_phone'] ?? null,
                    'email' => $groomerSpacer->email,
                    'job_title' => $isSpace ? 'Space Owner' : 'Groomer',
                    'image' => $groomerSpacer->business_basics['profile_photo_path'] ?? null,
                    'working_hours' => $defaultWorkingHours,
                    'holiday_time_off' => $defaultHolidayTimeOff,
                    'pause_booking' => false,
                ],
                [
                    'name' => $isSpace ? 'Harper Lane' : 'Liam Anderson',
                    'phone' => '+16175550124',
                    'email' => "liam.anderson.{$emailPrefix}@dev.com",
                    'job_title' => $isSpace ? 'Space Host' : 'Pet Stylist',
                    'image' => 'https://i.pravatar.cc/150?u=liam.anderson.' . $emailPrefix,
                    'working_hours' => $defaultWorkingHours,
                    'holiday_time_off' => $defaultHolidayTimeOff,
                    'pause_booking' => true,
                ],
                [
                    'name' => $isSpace ? 'Riley Quinn' : 'Aisha Khan',
                    'phone' => '+923001234567',
                    'email' => "aisha.khan.{$emailPrefix}@dev.com",
                    'job_title' => $isSpace ? 'Front Desk' : 'Junior Groomer',
                    'image' => 'https://i.pravatar.cc/150?u=aisha.khan.' . $emailPrefix,
                    'working_hours' => $defaultWorkingHours,
                    'holiday_time_off' => $defaultHolidayTimeOff,
                    'pause_booking' => false,
                ],
                [
                    'name' => $isSpace ? 'Jordan Lee' : 'Noah Wilson',
                    'phone' => '+447700900123',
                    'email' => "noah.wilson.{$emailPrefix}@dev.com",
                    'job_title' => $isSpace ? 'Facilities' : 'Bath Specialist',
                    'image' => 'https://i.pravatar.cc/150?u=noah.wilson.' . $emailPrefix,
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

    /**
     * Time-off ranges for Availability month cells (current month + fixed future holidays).
     *
     * @return list<array{from: string, to: string, reason: string}>
     */
    private function availabilityHolidayRanges(): array
    {
        $month = today()->copy()->startOfMonth();
        // Two weekday closures in the current month so "Time off" cells show in the calendar.
        $firstOff = $month->copy()->addDays(6);  // typically around day 7
        while ($firstOff->isWeekend()) {
            $firstOff->addDay();
        }
        $secondOff = $month->copy()->addDays(13);
        while ($secondOff->isWeekend() || $secondOff->equalTo($firstOff)) {
            $secondOff->addDay();
        }

        return [
            [
                'from' => $firstOff->toDateString(),
                'to' => $firstOff->toDateString(),
                'reason' => 'Personal time off',
            ],
            [
                'from' => $secondOff->toDateString(),
                'to' => $secondOff->toDateString(),
                'reason' => 'Studio maintenance',
            ],
            [
                'from' => today()->copy()->addMonthsNoOverflow(1)->startOfMonth()->addDays(19)->toDateString(),
                'to' => today()->copy()->addMonthsNoOverflow(1)->startOfMonth()->addDays(21)->toDateString(),
                'reason' => 'Bank holiday weekend',
            ],
            [
                'from' => '2026-12-24',
                'to' => '2026-12-26',
                'reason' => 'Christmas closure',
            ],
        ];
    }
}
