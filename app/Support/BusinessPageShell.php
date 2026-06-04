<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class BusinessPageShell
{
    public const SESSION_KEY = 'business_page_shell';

    public const SHELL_WEB = 'web';

    public const SHELL_DASHBOARD = 'dashboard';

    public static function useWeb(): void
    {
        session([self::SESSION_KEY => self::SHELL_WEB]);
    }

    public static function useDashboard(): void
    {
        session([self::SESSION_KEY => self::SHELL_DASHBOARD]);
    }

    public static function applyFromRequest(): void
    {
        $shell = request()->query('shell');

        if ($shell === self::SHELL_DASHBOARD && Auth::guard('groomer_spacer')->check()) {
            self::useDashboard();

            return;
        }

        if ($shell === self::SHELL_WEB) {
            self::useWeb();
        }
    }

    public static function prefersDashboard(): bool
    {
        return session(self::SESSION_KEY) === self::SHELL_DASHBOARD &&
            Auth::guard('groomer_spacer')->check();
    }

    public static function resolveComponent(string $dashboardComponent, string $webComponent): string
    {
        return self::prefersDashboard() ? $dashboardComponent : $webComponent;
    }
}
