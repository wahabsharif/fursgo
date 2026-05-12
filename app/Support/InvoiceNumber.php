<?php

namespace App\Support;

use Carbon\CarbonInterface;

/**
 * Fursgo invoice reference formats:
 *
 * - Customer (one per booking, suffix = booking id): CI-YYYY-XXXX
 * - Groomer payout bundles (sequential per year): BG-YYYY-XXXX
 * - Space owner payout bundles (sequential per year): BS-YYYY-XXXX
 */
final class InvoiceNumber
{
    /**
     * Customer invoice for a grooming booking (suffix is the booking id, minimum four digits).
     */
    public static function customerBooking(int $bookingId, ?CarbonInterface $appointmentDate = null): string
    {
        $year = $appointmentDate?->format('Y') ?? now()->format('Y');
        $suffix = str_pad((string) $bookingId, 4, '0', STR_PAD_LEFT);

        return sprintf('CI-%s-%s', $year, $suffix);
    }

    /**
     * Groomer payout covering a period (sequential number assigned per calendar year).
     */
    public static function groomerPayout(int $calendarYear, int $sequence): string
    {
        return self::sequential('BG', $calendarYear, $sequence);
    }

    /**
     * Space owner payout covering a period (sequential number assigned per calendar year).
     */
    public static function spaceOwnerPayout(int $calendarYear, int $sequence): string
    {
        return self::sequential('BS', $calendarYear, $sequence);
    }

    private static function sequential(string $prefix, int $calendarYear, int $sequence): string
    {
        $seq = str_pad((string) max(0, $sequence), 4, '0', STR_PAD_LEFT);

        return sprintf('%s-%d-%s', $prefix, $calendarYear, $seq);
    }
}
