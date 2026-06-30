<?php

namespace App\Support;

use Illuminate\Http\Request;

class BusinessHubNav
{
    public const SESSION_KEY = 'business_hub_nav';

    /**
     * @var list<string>
     */
    public const SECTIONS = [
        'business-hub',
        'bookings',
        'availability',
        'manage-availability',
        'services',
        'clients',
        'earnings',
        'settings',
    ];

    /**
     * @var list<string>
     */
    public const BOOKING_STATUSES = ['', 'pending', 'confirmed', 'completed', 'cancelled'];

    /**
     * @var list<string>
     */
    public const SERVICE_MENUS = ['services', 'add-ons', 'pet-preferences', 'service-area'];

    /**
     * @var list<string>
     */
    public const EARNINGS_MENUS = ['overview', 'transactions', 'pay-outs', 'invoices'];

    /**
     * @var list<string>
     */
    public const SETTINGS_MENUS = ['general', 'business-details', 'service-policies'];

    /**
     * @return array{active_section: string, active_booking_status: string, active_service_menu: string, active_earnings_menu: string, active_settings_menu: string}
     */
    public static function fromSession(): array
    {
        $nav = session(self::SESSION_KEY, []);
        $section = (string) ($nav['active_section'] ?? 'business-hub');

        if (!in_array($section, self::SECTIONS, true)) {
            $section = 'business-hub';
        }

        $bookingStatus = (string) ($nav['active_booking_status'] ?? '');
        if (!in_array($bookingStatus, self::BOOKING_STATUSES, true)) {
            $bookingStatus = '';
        }

        $serviceMenu = (string) ($nav['active_service_menu'] ?? 'services');
        if (!in_array($serviceMenu, self::SERVICE_MENUS, true)) {
            $serviceMenu = 'services';
        }

        $earningsMenu = (string) ($nav['active_earnings_menu'] ?? 'overview');
        if (!in_array($earningsMenu, self::EARNINGS_MENUS, true)) {
            $earningsMenu = 'overview';
        }

        $settingsMenu = (string) ($nav['active_settings_menu'] ?? 'general');
        if (!in_array($settingsMenu, self::SETTINGS_MENUS, true)) {
            $settingsMenu = 'general';
        }

        return [
            'active_section' => $section,
            'active_booking_status' => $bookingStatus,
            'active_service_menu' => $serviceMenu,
            'active_earnings_menu' => $earningsMenu,
            'active_settings_menu' => $settingsMenu,
        ];
    }

    public static function activeSection(): string
    {
        return self::fromSession()['active_section'];
    }

    /**
     * @return array{active_section: string, active_booking_status: string, active_service_menu: string, active_earnings_menu: string, active_settings_menu: string}
     */
    public static function mergeFromRequest(Request $request): array
    {
        $nav = self::fromSession();

        if ($request->has('section')) {
            $section = (string) $request->input('section');
            if (!in_array($section, self::SECTIONS, true)) {
                abort(422);
            }

            $nav['active_section'] = $section;
        }

        if ($request->has('active_booking_status')) {
            $bookingStatus = (string) $request->input('active_booking_status');
            if (!in_array($bookingStatus, self::BOOKING_STATUSES, true)) {
                abort(422);
            }

            $nav['active_booking_status'] = $bookingStatus;
        }

        if ($request->has('active_service_menu')) {
            $serviceMenu = (string) $request->input('active_service_menu');
            if (!in_array($serviceMenu, self::SERVICE_MENUS, true)) {
                abort(422);
            }

            $nav['active_service_menu'] = $serviceMenu;
        }

        if ($request->has('active_earnings_menu')) {
            $earningsMenu = (string) $request->input('active_earnings_menu');
            if (!in_array($earningsMenu, self::EARNINGS_MENUS, true)) {
                abort(422);
            }

            $nav['active_earnings_menu'] = $earningsMenu;
        }

        if ($request->has('active_settings_menu')) {
            $settingsMenu = (string) $request->input('active_settings_menu');
            if (!in_array($settingsMenu, self::SETTINGS_MENUS, true)) {
                abort(422);
            }

            $nav['active_settings_menu'] = $settingsMenu;
        }

        return $nav;
    }

    public static function persist(array $nav): void
    {
        session([self::SESSION_KEY => $nav]);
    }
}
