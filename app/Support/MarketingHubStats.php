<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\PromoCodeUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MarketingHubStats
{
    public const TIME_SLOTS = [
        ['label' => '08 - 09', 'start' => 8],
        ['label' => '10 - 11', 'start' => 10],
        ['label' => '12 - 13', 'start' => 12],
        ['label' => '14 - 15', 'start' => 14],
        ['label' => '16 - 17', 'start' => 16],
        ['label' => '18 - 19', 'start' => 18],
        ['label' => '20 - 21', 'start' => 20],
    ];

    public const WEEKDAYS = [
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
        'Sunday',
    ];

    public const ACQUISITION_SOURCES = [
        'direct_profile' => 'Direct Profile Visits',
        'platform_search' => 'Platform Search',
        'promotion_link' => 'Promotion Link',
    ];

    public static function forSpacer(?int $spacerId): array
    {
        if (!$spacerId) {
            return self::empty();
        }

        $now = now();
        $monthStart = $now->copy()->startOfMonth();
        $monthEnd = $now->copy()->endOfMonth();
        $lastMonthStart = $now->copy()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonthNoOverflow()->endOfMonth();

        $base = Booking::query()->where('goormer_spacer_id', $spacerId);
        $active = (clone $base)->where('booking_status', '!=', 'cancelled');
        $completed = (clone $base)->where('booking_status', 'completed');

        $monthBookings = (clone $active)
            ->whereBetween('date', [$monthStart, $monthEnd])
            ->get(['id', 'pet_owner_id', 'service', 'time', 'date', 'discount', 'acquisition_source', 'rating']);

        $profileViews = (int) (auth('groomer_spacer')->user()?->profile_visit ?? 0);

        $newClientsThisMonth = self::newClientsCount($spacerId, $monthStart, $monthEnd);
        $newClientsLastMonth = self::newClientsCount($spacerId, $lastMonthStart, $lastMonthEnd);
        $newClientsDelta = $newClientsThisMonth - $newClientsLastMonth;

        $totalBookingsAllTime = (clone $active)->count();
        $conversionPct = $profileViews > 0
            ? (int) round(($totalBookingsAllTime / $profileViews) * 100)
            : 0;

        $repeatClients = (clone $completed)
            ->select('pet_owner_id')
            ->groupBy('pet_owner_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();
        $totalClients = (clone $completed)->distinct('pet_owner_id')->count('pet_owner_id');
        $repeatPct = $totalClients > 0 ? (int) round(($repeatClients / $totalClients) * 100) : 0;

        $rated = (clone $completed)->whereNotNull('rating');
        $avgRating = (float) ($rated->avg('rating') ?? 0);
        $ratedCount = (clone $completed)->whereNotNull('rating')->count();

        $isSpaceUser = strtolower((string) (auth('groomer_spacer')->user()?->user_type ?? '')) === 'space';

        $serviceBreakdown = self::serviceBreakdown($monthBookings, $isSpaceUser);
        $petsBreakdown = self::petsBreakdown($spacerId, $monthStart, $monthEnd);
        $sourcesBreakdown = self::sourcesBreakdown($monthBookings);
        $peakByDay = self::peakBookingsByDay($spacerId);

        $topPromo = PromoCodeUsage::query()
            ->where('goormer_spacer_id', $spacerId)
            ->whereBetween('used_at', [$monthStart, $monthEnd])
            ->select('discount_code')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('discount_code')
            ->orderByDesc('total')
            ->value('discount_code');

        return [
            'kpis' => [
                'profile_views' => [
                    'value' => number_format($profileViews),
                    'sublabel' => $totalBookingsAllTime . ' bookings from views',
                ],
                'new_clients' => [
                    'value' => (string) $newClientsThisMonth,
                    'sublabel' => ($newClientsDelta >= 0 ? '+' : '') . $newClientsDelta . ' new clients this month',
                ],
                'booking_conversion' => [
                    'value' => $conversionPct . '%',
                    'sublabel' => 'Based on ' . number_format($totalBookingsAllTime) . ' bookings',
                ],
                'repeat_clients' => [
                    'value' => $repeatPct . '%',
                    'sublabel' => number_format($repeatClients) . ' returning clients',
                ],
                'average_rating' => [
                    'value' => $avgRating > 0 ? rtrim(rtrim(number_format($avgRating, 1), '0'), '.') : '—',
                    'sublabel' => 'Based on ' . number_format($ratedCount) . ' bookings',
                ],
            ],
            'services' => [
                'popular' => $serviceBreakdown[0]['name'] ?? '—',
                'top_promo' => $topPromo ?: '—',
                'legend' => $serviceBreakdown,
                'values' => array_column($serviceBreakdown, 'pct'),
            ],
            'pets' => [
                'legend' => $petsBreakdown,
                'values' => array_column($petsBreakdown, 'pct'),
            ],
            'bookings_from' => $sourcesBreakdown,
            'peak_bookings' => $peakByDay,
            'time_labels' => array_column(self::TIME_SLOTS, 'label'),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public static function empty(): array
    {
        $emptySlots = array_fill(0, count(self::TIME_SLOTS), 0);
        $peak = [];
        foreach (self::WEEKDAYS as $day) {
            $peak[$day] = $emptySlots;
        }

        return [
            'kpis' => [
                'profile_views' => ['value' => '0', 'sublabel' => '0 bookings from views'],
                'new_clients' => ['value' => '0', 'sublabel' => '+0 new clients this month'],
                'booking_conversion' => ['value' => '0%', 'sublabel' => 'Based on 0 bookings'],
                'repeat_clients' => ['value' => '0%', 'sublabel' => '0 returning clients'],
                'average_rating' => ['value' => '—', 'sublabel' => 'Based on 0 bookings'],
            ],
            'services' => [
                'popular' => '—',
                'top_promo' => '—',
                'legend' => [],
                'values' => [],
            ],
            'pets' => [
                'legend' => [],
                'values' => [],
            ],
            'bookings_from' => [
                ['label' => 'Direct Profile Visits', 'pct' => 0],
                ['label' => 'Platform Search', 'pct' => 0],
                ['label' => 'Promotion Link', 'pct' => 0],
            ],
            'peak_bookings' => $peak,
            'time_labels' => array_column(self::TIME_SLOTS, 'label'),
        ];
    }

    private static function newClientsCount(int $spacerId, Carbon $start, Carbon $end): int
    {
        $firstBookings = Booking::query()
            ->where('goormer_spacer_id', $spacerId)
            ->where('booking_status', '!=', 'cancelled')
            ->select('pet_owner_id', DB::raw('MIN(date) as first_date'))
            ->groupBy('pet_owner_id')
            ->havingRaw('MIN(date) BETWEEN ? AND ?', [$start->toDateString(), $end->toDateString()])
            ->get();

        return $firstBookings->count();
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Booking>  $monthBookings
     * @return list<array{name: string, pct: int, color: string}>
     */
    private static function serviceBreakdown($monthBookings, bool $isSpaceUser = false): array
    {
        $colors = ['#FBAC83', '#FDD0B3', '#FFF4E4'];

        if ($isSpaceUser) {
            $counts = $monthBookings
                ->map(fn($b) => self::spaceServiceCategory($b->service, $b->time))
                ->countBy()
                ->sortDesc();
        } else {
            $counts = $monthBookings->countBy('service')->sortDesc();
        }

        $top = $counts->take(3);

        if ($top->isEmpty()) {
            return [];
        }

        $topTotal = max($top->sum(), 1);
        $rows = [];
        $i = 0;
        $assigned = 0;
        foreach ($top as $name => $count) {
            $pct = $i === $top->count() - 1
                ? max(0, 100 - $assigned)
                : (int) round(($count / $topTotal) * 100);
            $assigned += $pct;
            $rows[] = [
                'name' => (string) $name,
                'pct' => $pct,
                'color' => $colors[$i] ?? '#FFF4E4',
            ];
            $i++;
        }

        return $rows;
    }

    private static function spaceServiceCategory(?string $service, mixed $time): string
    {
        $serviceLower = strtolower(trim((string) $service));

        if (preg_match('/full[\s_-]*day|fullday/', $serviceLower)) {
            return 'Full-Day';
        }

        if (preg_match('/half[\s_-]*day/', $serviceLower)) {
            return 'Half-Day';
        }

        if (str_contains($serviceLower, 'hour')) {
            return 'Hourly';
        }

        $timeRange = trim((string) $time);
        if ($timeRange !== '' && preg_match('/^(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})$/', $timeRange, $range)) {
            $startMinutes = ((int) $range[1]) * 60 + (int) $range[2];
            $endMinutes = ((int) $range[3]) * 60 + (int) $range[4];
            $diff = $endMinutes - $startMinutes;

            if ($diff < 0) {
                $diff += 24 * 60;
            }

            if ($diff >= 7 * 60) {
                return 'Full-Day';
            }

            if ($diff >= 3 * 60) {
                return 'Half-Day';
            }
        }

        return 'Hourly';
    }

    /**
     * @return list<array{name: string, pct: int, color: string}>
     */
    private static function petsBreakdown(int $spacerId, Carbon $start, Carbon $end): array
    {
        $colors = [
            '#D8E8B7',
            'rgba(216, 232, 183, 0.60)',
            'rgba(216, 232, 183, 0.20)',
        ];

        $rows = DB::table('booking_pet')
            ->join('bookings', 'bookings.id', '=', 'booking_pet.booking_id')
            ->join('pet_details', 'pet_details.id', '=', 'booking_pet.pet_detail_id')
            ->where('bookings.goormer_spacer_id', $spacerId)
            ->where('bookings.booking_status', '!=', 'cancelled')
            ->whereBetween('bookings.date', [$start->toDateString(), $end->toDateString()])
            ->selectRaw("CASE
                WHEN LOWER(pet_details.pet_type) LIKE '%dog%' THEN 'Dog'
                WHEN LOWER(pet_details.pet_type) LIKE '%cat%' THEN 'Cat'
                ELSE 'Other'
            END as pet_group, COUNT(*) as total")
            ->groupBy('pet_group')
            ->pluck('total', 'pet_group');

        $ordered = ['Cat', 'Dog', 'Other'];
        $total = max((int) $rows->sum(), 1);
        $out = [];
        $assigned = 0;

        foreach ($ordered as $i => $name) {
            $count = (int) ($rows[$name] ?? 0);
            $pct = $i === count($ordered) - 1
                ? max(0, 100 - $assigned)
                : (int) round(($count / $total) * 100);
            if ($rows->sum() === 0) {
                $pct = 0;
            }
            $assigned += $pct;
            $out[] = [
                'name' => $name,
                'pct' => $pct,
                'color' => $colors[$i],
            ];
        }

        if ($rows->sum() === 0) {
            return [];
        }

        return $out;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Booking>  $monthBookings
     * @return list<array{label: string, pct: int}>
     */
    private static function sourcesBreakdown($monthBookings): array
    {
        $counts = [
            'direct_profile' => 0,
            'platform_search' => 0,
            'promotion_link' => 0,
        ];

        foreach ($monthBookings as $booking) {
            $key = $booking->acquisition_source;
            if (!isset($counts[$key])) {
                $key = 'direct_profile';
            }
            $counts[$key]++;
        }

        $total = max(array_sum($counts), 1);
        $out = [];
        $keys = array_keys($counts);
        $assigned = 0;

        foreach ($keys as $i => $key) {
            $pct = $i === count($keys) - 1
                ? max(0, 100 - $assigned)
                : (int) round(($counts[$key] / $total) * 100);
            if (array_sum($counts) === 0) {
                $pct = 0;
            }
            $assigned += $pct;
            $out[] = [
                'label' => self::ACQUISITION_SOURCES[$key],
                'pct' => $pct,
            ];
        }

        return $out;
    }

    /**
     * @return array<string, list<int>>
     */
    private static function peakBookingsByDay(int $spacerId): array
    {
        $emptySlots = array_fill(0, count(self::TIME_SLOTS), 0);
        $peak = [];
        foreach (self::WEEKDAYS as $day) {
            $peak[$day] = $emptySlots;
        }

        $bookings = Booking::query()
            ->where('goormer_spacer_id', $spacerId)
            ->where('booking_status', '!=', 'cancelled')
            ->where('date', '>=', now()->subDays(90)->toDateString())
            ->get(['date', 'time']);

        foreach ($bookings as $booking) {
            if (!$booking->date || !$booking->time) {
                continue;
            }

            $weekday = $booking->date->format('l');
            if (!isset($peak[$weekday])) {
                continue;
            }

            $slot = self::slotIndexFromTime((string) $booking->time);
            if ($slot === null) {
                continue;
            }

            $peak[$weekday][$slot]++;
        }

        return $peak;
    }

    private static function slotIndexFromTime(string $time): ?int
    {
        if (!preg_match('/(\d{1,2})\s*:/', $time, $m)) {
            return null;
        }

        $hour = (int) $m[1];
        $best = null;

        foreach (self::TIME_SLOTS as $i => $slot) {
            if ($hour >= $slot['start'] && $hour < $slot['start'] + 2) {
                return $i;
            }
            if ($hour >= $slot['start']) {
                $best = $i;
            }
        }

        return $best;
    }
}
