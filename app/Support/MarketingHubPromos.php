<?php

namespace App\Support;

use App\Models\Booking;
use App\Models\PromoCode;
use App\Models\PromoCodeUsage;
use Carbon\Carbon;

class MarketingHubPromos
{
    /**
     * @return array{promos: list<array<string, mixed>>, performance: array<string, array{value: string, sublabel: string}>}
     */
    public static function forSpacer(?int $spacerId): array
    {
        if (!$spacerId) {
            return self::empty();
        }

        $promos = PromoCode::query()
            ->where('goormer_spacer_id', $spacerId)
            ->orderByDesc('updated_at')
            ->orderByDesc('created_at')
            ->get()
            ->map(fn(PromoCode $promo) => self::presentPromo($promo))
            ->values()
            ->all();

        return [
            'promos' => $promos,
            'performance' => self::campaignPerformance($spacerId),
        ];
    }

    /**
     * @return array{promos: list<array<string, mixed>>, performance: array<string, array{value: string, sublabel: string}>}
     */
    public static function empty(): array
    {
        return [
            'promos' => [],
            'performance' => [
                'views' => ['value' => '0', 'sublabel' => '+0 vs last month'],
                'bookings' => ['value' => '0', 'sublabel' => '+0 new clients this month'],
                'revenue' => ['value' => '£0', 'sublabel' => 'Generated from promos'],
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    private static function presentPromo(PromoCode $promo): array
    {
        $isPercent = $promo->discount_type === PromoCode::DISCOUNT_TYPE_PERCENT ||
            $promo->discount_type === '%';
        $amount = rtrim(rtrim(number_format((float) $promo->discount_amount, 2), '0'), '.');

        return [
            'id' => $promo->id,
            'discount_type_label' => $isPercent ? '% Off' : '£ Off',
            'discount_amount_label' => $isPercent ? "{$amount}% off" : "£{$amount} off",
            'code' => $promo->discount_code,
            'valid_dates_label' => self::formatValidDates($promo->start_date, $promo->end_date, (bool) $promo->no_end_date),
            'visibility' => (bool) $promo->visibility,
        ];
    }

    private static function formatValidDates(mixed $start, mixed $end, bool $noEndDate): string
    {
        if (!$start) {
            return $noEndDate ? 'Ongoing' : '—';
        }

        $startDate = $start instanceof Carbon ? $start : Carbon::parse($start);

        if ($noEndDate || !$end) {
            return $startDate->format('j M') . ' – Ongoing';
        }

        $endDate = $end instanceof Carbon ? $end : Carbon::parse($end);

        // Same month + year: 12-25 Nov
        if ($startDate->isSameMonth($endDate) && $startDate->isSameYear($endDate)) {
            if ($startDate->isSameDay($endDate)) {
                return $startDate->format('j M');
            }

            return $startDate->format('j') . '-' . $endDate->format('j M');
        }

        // Different months: 15 Jun – 15 Jul
        if ($startDate->isSameYear($endDate)) {
            return $startDate->format('j M') . ' – ' . $endDate->format('j M');
        }

        return $startDate->format('j M Y') . ' – ' . $endDate->format('j M Y');
    }

    /**
     * @return array<string, array{value: string, sublabel: string}>
     */
    private static function campaignPerformance(int $spacerId): array
    {
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $lastMonthStart = now()->subMonthNoOverflow()->startOfMonth();
        $lastMonthEnd = now()->subMonthNoOverflow()->endOfMonth();

        $usagesThisWeek = PromoCodeUsage::query()
            ->where('goormer_spacer_id', $spacerId)
            ->whereBetween('used_at', [$weekStart, $weekEnd]);

        $bookingIds = (clone $usagesThisWeek)->whereNotNull('booking_id')->pluck('booking_id');
        $bookingsThisWeek = $bookingIds->unique()->count();

        $revenue = (float) Booking::query()
            ->whereIn('id', $bookingIds)
            ->where('booking_status', '!=', 'cancelled')
            ->sum('amount');

        $viewsThisMonth = PromoCodeUsage::query()
            ->where('goormer_spacer_id', $spacerId)
            ->whereBetween('used_at', [$monthStart, $monthEnd])
            ->count();
        $viewsLastMonth = PromoCodeUsage::query()
            ->where('goormer_spacer_id', $spacerId)
            ->whereBetween('used_at', [$lastMonthStart, $lastMonthEnd])
            ->count();
        $viewsDelta = $viewsThisMonth - $viewsLastMonth;

        $newClientsThisMonth = self::newPromoClientsCount($spacerId, $monthStart, $monthEnd);

        // Views proxy until dedicated promo-view tracking exists
        $viewsThisWeek = max($bookingsThisWeek * 4, $viewsThisMonth > 0 ? (int) round($viewsThisMonth / max(1, now()->day) * 7) : 0);

        return [
            'views' => [
                'value' => (string) $viewsThisWeek,
                'sublabel' => ($viewsDelta >= 0 ? '+' : '') . $viewsDelta . ' vs last month',
            ],
            'bookings' => [
                'value' => (string) $bookingsThisWeek,
                'sublabel' => ($newClientsThisMonth >= 0 ? '+' : '') . $newClientsThisMonth . ' new clients this month',
            ],
            'revenue' => [
                'value' => '£' . number_format($revenue, 0),
                'sublabel' => 'Generated from promos',
            ],
        ];
    }

    private static function newPromoClientsCount(int $spacerId, Carbon $start, Carbon $end): int
    {
        $ownerIds = PromoCodeUsage::query()
            ->where('goormer_spacer_id', $spacerId)
            ->whereBetween('used_at', [$start, $end])
            ->whereNotNull('pet_owner_id')
            ->pluck('pet_owner_id')
            ->unique();

        if ($ownerIds->isEmpty()) {
            return 0;
        }

        $returning = PromoCodeUsage::query()
            ->where('goormer_spacer_id', $spacerId)
            ->where('used_at', '<', $start)
            ->whereIn('pet_owner_id', $ownerIds)
            ->distinct('pet_owner_id')
            ->count('pet_owner_id');

        return max(0, $ownerIds->count() - $returning);
    }
}
