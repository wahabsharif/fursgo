<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BusinessHubAvatar
{
    public const GROOMER_BG = '#FFC97A';

    public const SPACE_BG = '#FFA899';

    public static function isSpaceAccount(): bool
    {
        $type = strtolower((string) (
            Auth::guard('groomer_spacer')->user()?->user_type
            ?? Auth::user()?->user_type
            ?? ''
        ));

        return $type === 'space';
    }

    public static function background(): string
    {
        return self::isSpaceAccount() ? self::SPACE_BG : self::GROOMER_BG;
    }

    /**
     * Two-letter initials from a display name (e.g. "Jamie Dalton" → "JD").
     */
    public static function initials(?string $name): string
    {
        $name = trim((string) $name);
        if ($name === '') {
            return '??';
        }

        $parts = preg_split('/\s+/u', $name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        if ($parts === []) {
            return '??';
        }

        $letters = collect($parts)
            ->map(fn (string $part) => Str::upper(Str::substr($part, 0, 1)))
            ->filter()
            ->values();

        if ($letters->isEmpty()) {
            return '??';
        }

        if ($letters->count() === 1) {
            return Str::upper(Str::substr($parts[0], 0, 2));
        }

        return $letters->take(2)->implode('');
    }

    public static function mediaUrl(?string $path): ?string
    {
        $raw = trim((string) $path);
        if ($raw === '') {
            return null;
        }

        if (
            str_starts_with($raw, 'http://')
            || str_starts_with($raw, 'https://')
            || str_starts_with($raw, 'data:')
            || str_starts_with($raw, '/')
        ) {
            return $raw;
        }

        return asset('storage/'.ltrim($raw, '/'));
    }
}
