<?php

namespace App\Support;

use Illuminate\Http\Request;

class DashboardNav
{
    public const SESSION_KEY = 'dashboard_nav';

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
     * @return array{active_section: string, active_booking_status: string, active_service_menu: string}
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

        return [
            'active_section' => $section,
            'active_booking_status' => $bookingStatus,
            'active_service_menu' => $serviceMenu,
        ];
    }

    public static function activeSection(): string
    {
        return self::fromSession()['active_section'];
    }

    /**
     * @return array{active_section: string, active_booking_status: string, active_service_menu: string}
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

        return $nav;
    }

    public static function persist(array $nav): void
    {
        session([self::SESSION_KEY => $nav]);
    }
}
