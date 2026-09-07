<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class BusinessPageShell
{
    public const SESSION_KEY = 'business_page_shell';

    public const PUBLIC_CHROME_KEY = 'business_public_chrome';

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

    public static function usePublicBusinessChrome(): void
    {
        session([self::PUBLIC_CHROME_KEY => true]);
    }

    public static function clearPublicBusinessChrome(): void
    {
        session()->forget(self::PUBLIC_CHROME_KEY);
    }

    public static function applyFromRequest(): void
    {
        $shell = request()->query('shell');

        if ($shell === self::SHELL_BUSINESS_HUB && Auth::guard('groomer_spacer')->check()) {
            self::useBusinessHub();
            self::usePublicBusinessChrome();

            return;
        }

        if ($shell === self::SHELL_WEB) {
            self::useWeb();
            self::clearPublicBusinessChrome();
        }

        if (request()->query('chrome') === 'business' || request()->routeIs([
            'business-landing-page',
            'business-homepage-groomer-space-owner',
            'login-groomer-space',
            'signup-groomer-space',
        ])) {
            self::usePublicBusinessChrome();
        }
    }

    public static function prefersBusinessHub(): bool
    {
        return session(self::SESSION_KEY) === self::SHELL_BUSINESS_HUB &&
            Auth::guard('groomer_spacer')->check();
    }

    public static function prefersBusinessChrome(): bool
    {
        return self::prefersBusinessHub() || session(self::PUBLIC_CHROME_KEY) === true;
    }

    public static function resolveComponent(string $businessHubComponent, string $webComponent): string
    {
        return self::prefersBusinessHub() ? $businessHubComponent : $webComponent;
    }
}
