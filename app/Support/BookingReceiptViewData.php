<?php

namespace App\Support;

use App\Models\Booking;
use DateTimeImmutable;
use Throwable;

class BookingReceiptViewData
{
    /**
     * @return array{
     *     booking_id: int,
     *     invoice_url: string,
     *     is_space_user: bool,
     *     booking_id_label: string,
     *     date_label: string,
     *     owner_name: string,
     *     owner_initial: string,
     *     owner_photo_url: string|null,
     *     pet_name: string,
     *     pet_type: string,
     *     service: string,
     *     service_line_label: string,
     *     space_label: string,
     *     service_time_label_for_space: string,
     *     service_amount: float,
     *     service_amount_formatted: string,
     *     extras_amount: float,
     *     extras_amount_formatted: string,
     *     promo_discount: float,
     *     promo_discount_formatted: string,
     *     promo_discount_label: string,
     *     total_amount: float,
     *     total_amount_formatted: string,
     *     addons: list<array{label: string, amount: float, amount_formatted: string}>
     * }
     */
    public static function fromBooking(
        Booking $booking,
        ?bool $isSpaceUser = null,
        ?string $ownerName = null,
        ?string $petName = null,
        ?string $petType = null,
    ): array {
        $isSpaceUser ??= strtolower((string) (auth('groomer_spacer')->user()?->user_type ?? auth()->user()?->user_type ?? '')) === 'space';

        if ($ownerName === null && $booking->relationLoaded('petOwner')) {
            $ownerName = $booking->petOwner->name ?? 'N/A';
        }
        $ownerName ??= 'N/A';

        $pet = $booking->relationLoaded('pets') ? $booking->pets->first() : null;
        if ($petName === null || $petType === null) {
            $petName ??= $pet->name ?? 'N/A';
            $petType ??= $pet->pet_type ?? '';
        }
        $service = $booking->service ?: 'N/A';

        $visitType = trim((string) ($booking->visit_type ?? ''));
        if ($visitType === '') {
            $spaceLabel = 'Garden / Shed';
        } elseif (str_contains($visitType, '/') || str_contains($visitType, ' ')) {
            $spaceLabel = str_replace('Garden/Shed', 'Garden / Shed', $visitType);
        } else {
            $spaceLabel = match (str_replace('_', ' ', strtolower($visitType))) {
                'garden shed', 'garden/shed' => 'Garden / Shed',
                'home visit', 'home' => 'Home Visit',
                'salon', 'salon visit' => 'Salon',
                'mobile station', 'mobile_station' => 'Mobile Station',
                default => ucwords(str_replace('_', ' ', $visitType)),
            };
        }

        $timeRaw = (string) ($booking->time ?? '');
        $timeLabelForSpace = trim($timeRaw) !== '' ? trim($timeRaw) : 'N/A';
        $spaceTimeRange = '';
        if (str_contains($timeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $timeRaw, 2);
            $start = $parts[0] ?? '';
            $end = $parts[1] ?? '';
            preg_match('/(\d{1,2}:\d{2})/', $start, $startMatch);
            preg_match('/(\d{1,2}:\d{2})/', $end, $endMatch);
            if (!empty($startMatch[1]) && !empty($endMatch[1])) {
                try {
                    $startDt = new DateTimeImmutable($startMatch[1]);
                    $endDt = new DateTimeImmutable($endMatch[1]);
                    $timeLabelForSpace = $startDt->format('H:i') . ' - ' . $endDt->format('H:i');
                } catch (Throwable) {
                    $timeLabelForSpace = trim((string) $startMatch[1]) . ' - ' . trim((string) $endMatch[1]);
                }
                $spaceTimeRange = $startMatch[1] . '-' . $endMatch[1];
            }
        } elseif (trim($timeRaw) !== '') {
            $spaceTimeRange = trim($timeRaw);
        }

        $serviceAmount = (float) $booking->amount;
        $addons = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])
            ->map(function ($item) {
                $amount = (float) data_get($item, 'amount', 0);

                return [
                    'label' => trim((string) data_get($item, 'label', '')),
                    'amount' => $amount,
                    'amount_formatted' => number_format($amount, 2),
                ];
            })
            ->filter(fn(array $item) => $item['label'] !== '')
            ->values()
            ->all();

        $extrasAmount = (float) collect($addons)->sum(fn(array $item) => $item['amount']);
        $promoDiscount = (float) ($booking->discount ?? 0);
        $totalAmount = $serviceAmount + $extrasAmount - $promoDiscount;

        $serviceLineLabel = trim((string) ($booking->service ?: 'Service'));
        if (!$isSpaceUser && $petName !== '' && $petName !== 'N/A') {
            $serviceLineLabel .= ' (' . $petName . ')';
        }

        $serviceLower = strtolower(trim((string) ($booking->service ?? '')));
        $durationLabel = match (true) {
            (bool) preg_match('/full[\s_-]*day|fullday/', $serviceLower) => 'Full-day',
            (bool) preg_match('/half[\s_-]*day/', $serviceLower) => 'Half-day',
            str_contains($serviceLower, 'hour') => 'Hourly',
            default => 'Half-day',
        };
        $spaceDetail = $durationLabel;
        if ($spaceTimeRange !== '') {
            $spaceDetail .= ' - ' . $spaceTimeRange;
        }
        $spaceLineLabel = str_replace(' / ', '/', $spaceLabel) . ' (' . $spaceDetail . ')';

        $ownerPhotoRaw = '';
        if ($booking->relationLoaded('petOwner')) {
            $ownerPhotoRaw = trim((string) ($booking->petOwner?->profile_image ?? ''));
        }
        $ownerPhotoUrl = self::resolvePhotoUrl($ownerPhotoRaw);

        return [
            'booking_id' => (int) $booking->id,
            'invoice_url' => route('business-hub.bookings.invoice-pdf', $booking),
            'is_space_user' => $isSpaceUser,
            'booking_id_label' => 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT),
            'date_label' => optional($booking->date)->format('d/m/Y') ?? 'N/A',
            'owner_name' => $ownerName,
            'owner_initial' => \App\Support\BusinessHubAvatar::initials($ownerName),
            'owner_photo_url' => $ownerPhotoUrl,
            'pet_name' => $petName,
            'pet_type' => $petType,
            'service' => $service,
            'service_line_label' => $isSpaceUser ? $spaceLineLabel : $serviceLineLabel,
            'space_label' => $spaceLabel,
            'service_time_label_for_space' => $service . ' (' . $timeLabelForSpace . ')',
            'service_amount' => $serviceAmount,
            'service_amount_formatted' => number_format($serviceAmount, 2),
            'extras_amount' => $extrasAmount,
            'extras_amount_formatted' => number_format($extrasAmount, 2),
            'promo_discount' => $promoDiscount,
            'promo_discount_formatted' => number_format($promoDiscount, 2),
            'promo_discount_label' => $promoDiscount > 0
                ? '- £' . number_format($promoDiscount, 2)
                : '£' . number_format($promoDiscount, 2),
            'total_amount' => $totalAmount,
            'total_amount_formatted' => number_format($totalAmount, 2),
            'addons' => $addons,
        ];
    }

    private static function resolvePhotoUrl(?string $raw): ?string
    {
        $raw = trim((string) $raw);
        if ($raw === '') {
            return null;
        }

        $isAbsolute = str_starts_with($raw, 'http://') ||
            str_starts_with($raw, 'https://') ||
            str_starts_with($raw, 'data:') ||
            str_starts_with($raw, '/');

        return $isAbsolute ? $raw : asset('storage/' . ltrim($raw, '/'));
    }
}
