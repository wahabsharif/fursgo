<?php

namespace App\Support;

class BookingDeclineReasons
{
    /**
     * @return list<string>
     */
    public static function options(): array
    {
        return [
            'Changes in schedule',
            'Can’t accommodate this request',
            'Customer did not show up',
            'Other',
        ];
    }

    public static function default(): string
    {
        return self::options()[0];
    }

    public static function normalize(?string $reason): string
    {
        $reason = trim((string) $reason);
        $canonical = self::straightenApostrophes($reason);

        foreach (self::options() as $option) {
            if (self::straightenApostrophes($option) === $canonical) {
                return $option;
            }
        }

        return self::default();
    }

    private static function straightenApostrophes(string $value): string
    {
        return str_replace(['’', '‘'], "'", $value);
    }
}
