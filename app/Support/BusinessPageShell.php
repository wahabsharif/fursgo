<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class BusinessPageShell
{
    public const SESSION_KEY = 'business_page_shell';

    public const SHELL_WEB = 'web';

    public const SHELL_BUSINESS_HUB = 'business-hub';

    public static function useWeb(): void
    {
        session([self::SESSION_KEY => self::SHELL_WEB]);
    }

    public static function useBusinessHub(): void
    {
        session([self::SESSION_KEY => self::SHELL_BUSINESS_HUB]);
    }

    public static function applyFromRequest(): void
    {
        $shell = request()->query('shell');

        if ($shell === self::SHELL_BUSINESS_HUB && Auth::guard('groomer_spacer')->check()) {
            self::useBusinessHub();

            return;
        }

        if ($shell === self::SHELL_WEB) {
            self::useWeb();
        }
    }

    public static function prefersBusinessHub(): bool
    {
        return session(self::SESSION_KEY) === self::SHELL_BUSINESS_HUB &&
            Auth::guard('groomer_spacer')->check();
    }

    public static function resolveComponent(string $businessHubComponent, string $webComponent): string
    {
        return self::prefersBusinessHub() ? $businessHubComponent : $webComponent;
    }
}
