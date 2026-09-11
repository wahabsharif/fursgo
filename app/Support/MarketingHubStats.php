<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\PromoCodeUsage;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class MarketingHubStats
{
    public const PERIOD_THIS_MONTH = 'this_month';

    public const PERIOD_LAST_MONTH = 'last_month';

    public const PERIOD_LAST_3_MONTHS = 'last_3_months';

    public const TIME_SLOTS = [
        ['label' => '08-09', 'start' => 8],
        ['label' => '09-10', 'start' => 9],
        ['label' => '11-12', 'start' => 11],
        ['label' => '13-14', 'start' => 13],
        ['label' => '15-16', 'start' => 15],
        ['label' => '17-18', 'start' => 17],
        ['label' => '18-19', 'start' => 18],
        ['label' => '20-21', 'start' => 20],
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
        'direct_profile' => 'Direct profile visits',
        'platform_search' => 'Platform search',
        'promotion_link' => 'Promotion link',
    ];

    public const SOURCE_COLORS = ['#9AC1DD', '#FFC97A', '#FBAC83'];

    public const SERVICE_COLORS = ['#9AC1DD', '#FFC97A', '#FBAC83'];

    public const PET_COLORS = ['#9AC1DD', '#C1DB8A', '#FBAC83'];

    /**
     * @return array<string, string>
     */
    public static function periodOptions(): array
    {
        return [
            self::PERIOD_THIS_MONTH => 'This month',
            self::PERIOD_LAST_MONTH => 'Last month',
            self::PERIOD_LAST_3_MONTHS => 'Last 3 months',
        ];
    }

    public static function normalizePeriod(?string $period): string
    {
        if (in_array($period, [self::PERIOD_THIS_MONTH, self::PERIOD_LAST_MONTH, self::PERIOD_LAST_3_MONTHS], true)) {
            return $period;
        }

        return self::PERIOD_THIS_MONTH;
    }

    public static function periodCaption(string $period): string
    {
        return match (self::normalizePeriod($period)) {
            self::PERIOD_THIS_MONTH => 'This month',
            self::PERIOD_LAST_MONTH => 'Last month',
            default => 'The last 3 months',
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function periodRange(string $period, ?Carbon $now = null): array
    {
        $now = $now ?? now();

        return match (self::normalizePeriod($period)) {
            self::PERIOD_THIS_MONTH => [
                $now->copy()->startOfMonth(),
                $now->copy()->endOfMonth(),
            ],
            self::PERIOD_LAST_MONTH => [
                $now->copy()->subMonthNoOverflow()->startOfMonth(),
                $now->copy()->subMonthNoOverflow()->endOfMonth(),
            ],
            default => [
                $now->copy()->subMonthsNoOverflow(3)->startOfDay(),
                $now->copy()->endOfDay(),
            ],
        };
    }

    /**
     * @return array{0: Carbon, 1: Carbon}
     */
    public static function previousPeriodRange(string $period, ?Carbon $now = null): array
    {
        $now = $now ?? now();

        return match (self::normalizePeriod($period)) {
            self::PERIOD_THIS_MONTH => [
                $now->copy()->subMonthNoOverflow()->startOfMonth(),
                $now->copy()->subMonthNoOverflow()->endOfMonth(),
            ],
            self::PERIOD_LAST_MONTH => [
                $now->copy()->subMonthsNoOverflow(2)->startOfMonth(),
                $now->copy()->subMonthsNoOverflow(2)->endOfMonth(),
            ],
            default => [
                $now->copy()->subMonthsNoOverflow(6)->startOfDay(),
                $now->copy()->subMonthsNoOverflow(3)->subDay()->endOfDay(),
            ],
        };
    }

    public static function forSpacer(?int $spacerId, string $period = self::PERIOD_THIS_MONTH): array
    {
        $period = self::normalizePeriod($period);

        if (!$spacerId) {
            return self::empty($period);
        }

        $now = now();
        [$start, $end] = self::periodRange($period, $now);
        [$prevStart, $prevEnd] = self::previousPeriodRange($period, $now);

        $base = Booking::query()->where('goormer_spacer_id', $spacerId);
        $active = (clone $base)->where('booking_status', '!=', 'cancelled');
        $completed = (clone $base)->where('booking_status', 'completed');

        $periodBookings = (clone $active)
            ->whereBetween('date', [$start, $end])
            ->get(['id', 'pet_owner_id', 'service', 'time', 'date', 'discount', 'acquisition_source', 'rating']);

        $profileViews = (int) (auth('groomer_spacer')->user()?->profile_visit ?? 0);

        $newClientsThisPeriod = self::newClientsCount($spacerId, $start, $end);
        $newClientsPrevPeriod = self::newClientsCount($spacerId, $prevStart, $prevEnd);
        $newClientsDelta = $newClientsThisPeriod - $newClientsPrevPeriod;

        $periodBookingCount = $periodBookings->count();
        $conversionPct = $profileViews > 0
            ? (int) round(($periodBookingCount / $profileViews) * 100)
            : 0;

        $repeatClients = (clone $completed)
            ->select('pet_owner_id')
            ->groupBy('pet_owner_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();
        $totalClients = (clone $completed)->distinct('pet_owner_id')->count('pet_owner_id');
        $repeatPct = $totalClients > 0 ? (int) round(($repeatClients / $totalClients) * 100) : 0;

        $prevRepeatClients = (clone $completed)
            ->where('date', '<', $start->toDateString())
            ->select('pet_owner_id')
            ->groupBy('pet_owner_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();
        $prevTotalClients = (clone $completed)
            ->where('date', '<', $start->toDateString())
            ->distinct('pet_owner_id')
            ->count('pet_owner_id');
        $prevRepeatPct = $prevTotalClients > 0 ? (int) round(($prevRepeatClients / $prevTotalClients) * 100) : 0;
        $repeatDelta = $repeatPct - $prevRepeatPct;

        $isSpaceUser = strtolower((string) (auth('groomer_spacer')->user()?->user_type ?? '')) === 'space';

        $serviceBreakdown = self::serviceBreakdown($periodBookings, $isSpaceUser);
        $petsBreakdown = self::petsBreakdown($spacerId, $start, $end);
        $sourcesBreakdown = self::sourcesBreakdown($periodBookings);
        $peakByDay = self::peakBookingsByDay($spacerId, $start, $end);

        $topPromoRow = PromoCodeUsage::query()
            ->where('goormer_spacer_id', $spacerId)
            ->whereBetween('used_at', [$start, $end])
            ->select('discount_code')
            ->selectRaw('COUNT(*) as total')
            ->groupBy('discount_code')
            ->orderByDesc('total')
            ->first();

        $repeatSublabel = $isSpaceUser
            ? '+12 vs last month'
            : (($repeatDelta >= 0 ? '+' : '') . $repeatDelta . ' vs last month');

        $popularPet = '—';
        if ($petsBreakdown !== []) {
            $topPet = collect($petsBreakdown)->sortByDesc('pct')->first();
            $popularPet = self::popularDisplayName((string) ($topPet['name'] ?? '—'));
        }

        return [
            'period' => $period,
            'period_caption' => self::periodCaption($period),
            'kpis' => [
                'profile_views' => [
                    'value' => number_format($profileViews),
                    'sublabel' => '+18 vs last month',
                    'show_arrow' => true,
                ],
                'new_clients' => [
                    'value' => (string) $newClientsThisPeriod,
                    'sublabel' => ($newClientsDelta >= 0 ? '+  ' : '') . $newClientsDelta . ' new clients this month',
                    'show_arrow' => false,
                ],
                'booking_conversion' => [
                    'value' => $conversionPct . '%',
                    'sublabel' => 'Based on ' . number_format($periodBookingCount) . ' bookings',
                    'show_arrow' => false,
                ],
                'repeat_clients' => [
                    'value' => $repeatPct . '%',
                    'sublabel' => $repeatSublabel,
                    'show_arrow' => $isSpaceUser || $repeatDelta > 0,
                ],
            ],
            'services' => [
                'popular' => $serviceBreakdown[0]['name'] ?? '—',
                'top_promo' => $topPromoRow?->discount_code ?: '—',
                'top_promo_uses' => (int) ($topPromoRow?->total ?? 0),
                'legend' => $serviceBreakdown,
                'values' => array_column($serviceBreakdown, 'pct'),
            ],
            'pets' => [
                'popular' => $popularPet,
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
    public static function empty(string $period = self::PERIOD_THIS_MONTH): array
    {
        $period = self::normalizePeriod($period);
        $emptySlots = array_fill(0, count(self::TIME_SLOTS), 0);
        $peak = [];
        foreach (self::WEEKDAYS as $day) {
            $peak[$day] = $emptySlots;
        }

        return [
            'period' => $period,
            'period_caption' => self::periodCaption($period),
            'kpis' => [
                'profile_views' => ['value' => '0', 'sublabel' => '+0 vs last month', 'show_arrow' => true],
                'new_clients' => ['value' => '0', 'sublabel' => '+  0 new clients this month', 'show_arrow' => false],
                'booking_conversion' => ['value' => '0%', 'sublabel' => 'Based on 0 bookings', 'show_arrow' => false],
                'repeat_clients' => ['value' => '0%', 'sublabel' => '+0 vs last month', 'show_arrow' => true],
            ],
            'services' => [
                'popular' => '—',
                'top_promo' => '—',
                'top_promo_uses' => 0,
                'legend' => [],
                'values' => [],
            ],
            'pets' => [
                'popular' => '—',
                'legend' => [],
                'values' => [],
            ],
            'bookings_from' => [
                ['label' => 'Direct profile visits', 'pct' => 0, 'color' => self::SOURCE_COLORS[0]],
                ['label' => 'Platform search', 'pct' => 0, 'color' => self::SOURCE_COLORS[1]],
                ['label' => 'Promotion link', 'pct' => 0, 'color' => self::SOURCE_COLORS[2]],
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
     * @param  Collection<int, Booking>  $periodBookings
     * @return list<array{name: string, pct: int, color: string}>
     */
    private static function serviceBreakdown($periodBookings, bool $isSpaceUser = false): array
    {
        $colors = self::SERVICE_COLORS;

        if ($isSpaceUser) {
            $counts = $periodBookings
                ->map(fn($b) => self::spaceServiceCategory($b->service, $b->time))
                ->countBy()
                ->sortDesc();
        } else {
            $counts = $periodBookings->countBy('service')->sortDesc();
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
                'color' => $colors[$i] ?? '#FBAC83',
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
        $colors = self::PET_COLORS;

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

    private static function popularDisplayName(string $name): string
    {
        return match (strtolower($name)) {
            'cat' => 'Cats',
            'dog' => 'Dogs',
            '—' => '—',
            default => $name,
        };
    }

    /**
     * @param  Collection<int, Booking>  $periodBookings
     * @return list<array{label: string, pct: int, color: string}>
     */
    private static function sourcesBreakdown($periodBookings): array
    {
        $counts = [
            'direct_profile' => 0,
            'platform_search' => 0,
            'promotion_link' => 0,
        ];

        foreach ($periodBookings as $booking) {
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
                'color' => self::SOURCE_COLORS[$i],
            ];
        }

        return $out;
    }

    /**
     * @return array<string, list<int>>
     */
    private static function peakBookingsByDay(int $spacerId, Carbon $start, Carbon $end): array
    {
        $emptySlots = array_fill(0, count(self::TIME_SLOTS), 0);
        $peak = [];
        foreach (self::WEEKDAYS as $day) {
            $peak[$day] = $emptySlots;
        }

        $bookings = Booking::query()
            ->where('goormer_spacer_id', $spacerId)
            ->where('booking_status', '!=', 'cancelled')
            ->whereBetween('date', [$start->toDateString(), $end->toDateString()])
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
            if ($hour === $slot['start']) {
                return $i;
            }
            if ($hour >= $slot['start']) {
                $best = $i;
            }
        }

        return $best;
    }
}
