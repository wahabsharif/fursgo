<?php

namespace App\Support;

use Illuminate\Support\Carbon;

class HelpCentre
{
    public const AUDIENCE_PET_OWNER = 'pet_owner';

    public const AUDIENCE_BUSINESS = 'business';

    public const TICKET_CATEGORIES = [
        'bookings' => 'Bookings',
        'payments' => 'Payments',
        'account' => 'Account',
        'app_technical' => 'App/Technical',
        'other' => 'Other',
    ];

    public static function audience(): string
    {
        return BusinessPageShell::prefersBusinessChrome()
            ? self::AUDIENCE_BUSINESS
            : self::AUDIENCE_PET_OWNER;
    }

    public static function isChatOnline(?Carbon $now = null): bool
    {
        $now = ($now ?? now())->timezone('Europe/London');

        return $now->isWeekday()
            && $now->gte($now->copy()->setTime(9, 0))
            && $now->lt($now->copy()->setTime(17, 0));
    }
}
