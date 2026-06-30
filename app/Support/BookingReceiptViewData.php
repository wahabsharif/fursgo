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
     *     pet_name: string,
     *     pet_type: string,
     *     service: string,
     *     space_label: string,
     *     service_time_label_for_space: string,
     *     service_amount: float,
     *     service_amount_formatted: string,
     *     extras_amount: float,
     *     extras_amount_formatted: string,
     *     promo_discount: float,
     *     promo_discount_formatted: string,
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

        if ($petName === null || $petType === null) {
            $pet = $booking->relationLoaded('pets') ? $booking->pets->first() : null;
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
            }
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

        return [
            'booking_id' => (int) $booking->id,
            'invoice_url' => route('business-hub.bookings.invoice-pdf', $booking),
            'is_space_user' => $isSpaceUser,
            'booking_id_label' => 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT),
            'date_label' => optional($booking->date)->format('d/m/Y') ?? 'N/A',
            'owner_name' => $ownerName,
            'pet_name' => $petName,
            'pet_type' => $petType,
            'service' => $service,
            'space_label' => $spaceLabel,
            'service_time_label_for_space' => $service . ' (' . $timeLabelForSpace . ')',
            'service_amount' => $serviceAmount,
            'service_amount_formatted' => number_format($serviceAmount, 2),
            'extras_amount' => $extrasAmount,
            'extras_amount_formatted' => number_format($extrasAmount, 2),
            'promo_discount' => $promoDiscount,
            'promo_discount_formatted' => number_format($promoDiscount, 2),
            'total_amount' => $totalAmount,
            'total_amount_formatted' => number_format($totalAmount, 2),
            'addons' => $addons,
        ];
    }
}
