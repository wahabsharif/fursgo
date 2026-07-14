<?php

namespace App\Support;

use Illuminate\Http\Request;

class MarketingHubNav
{
    public const SESSION_KEY = 'marketing_hub_nav';

    /**
     * @var list<string>
     */
    public const SECTIONS = [
        'marketing-hub',
        'promo-creation',
        'settings',
    ];

    /**
     * @return array{active_section: string}
     */
    public static function fromSession(): array
    {
        $nav = session(self::SESSION_KEY, []);
        $section = (string) ($nav['active_section'] ?? 'marketing-hub');

        if (!in_array($section, self::SECTIONS, true)) {
            $section = 'marketing-hub';
        }

        return [
            'active_section' => $section,
        ];
    }

    /**
     * @param  array{active_section?: string}  $nav
     */
    public static function persist(array $nav): void
    {
        $section = (string) ($nav['active_section'] ?? 'marketing-hub');

        if (!in_array($section, self::SECTIONS, true)) {
            $section = 'marketing-hub';
        }

        session([
            self::SESSION_KEY => [
                'active_section' => $section,
            ],
        ]);
    }

    /**
     * @return array{active_section: string}
     */
    public static function mergeFromRequest(Request $request): array
    {
        $current = self::fromSession();
        $section = (string) $request->input('section', $current['active_section']);

        if (!in_array($section, self::SECTIONS, true)) {
            $section = $current['active_section'];
        }

        return [
            'active_section' => $section,
        ];
    }
}
