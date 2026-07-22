<?php

namespace App\Support;

use App\Models\AccountLoginSession;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;

class AccountLoginSessions
{
    /**
     * Upsert the current browser session for the authenticated owner.
     */
    public static function touch(Model $owner, Request $request, string $guard): void
    {
        $sessionId = Session::getId();
        if ($sessionId === '') {
            return;
        }

        $userAgent = (string) $request->userAgent();
        [$deviceType, $deviceLabel] = self::deviceFromUserAgent($userAgent);

        $existing = AccountLoginSession::query()
            ->where('session_id', $sessionId)
            ->first();

        if ($existing) {
            $shouldRefresh = $existing->last_active_at === null ||
                $existing->last_active_at->lt(now()->subMinutes(2));

            if ($shouldRefresh) {
                $existing->forceFill([
                    'ip_address' => $request->ip(),
                    'user_agent' => $userAgent !== '' ? $userAgent : $existing->user_agent,
                    'device_type' => $deviceType,
                    'device_label' => $deviceLabel,
                    'last_active_at' => now(),
                ])->save();
            }

            return;
        }

        AccountLoginSession::query()->updateOrCreate(
            ['session_id' => $sessionId],
            [
                'owner_type' => $owner->getMorphClass(),
                'owner_id' => $owner->getKey(),
                'guard' => $guard,
                'device_type' => $deviceType,
                'device_label' => $deviceLabel,
                'ip_address' => $request->ip(),
                'user_agent' => $userAgent !== '' ? $userAgent : null,
                'last_active_at' => now(),
            ]
        );
    }

    /**
     * @return list<array{
     *     id: int,
     *     session_id: string,
     *     device_type: string,
     *     device_label: string,
     *     last_active_label: string,
     *     is_current: bool
     * }>
     */
    public static function listFor(Model $owner): array
    {
        self::pruneExpired($owner);

        $currentId = Session::getId();

        return AccountLoginSession::query()
            ->where('owner_type', $owner->getMorphClass())
            ->where('owner_id', $owner->getKey())
            ->orderByDesc('last_active_at')
            ->get()
            ->map(function (AccountLoginSession $session) use ($currentId) {
                return [
                    'id' => $session->id,
                    'session_id' => $session->session_id,
                    'device_type' => $session->device_type,
                    'device_label' => $session->device_label,
                    'last_active_label' => self::formatLastActive($session->last_active_at),
                    'is_current' => $session->session_id === $currentId,
                ];
            })
            ->values()
            ->all();
    }

    public static function revoke(Model $owner, int $loginSessionId): bool
    {
        $loginSession = AccountLoginSession::query()
            ->where('owner_type', $owner->getMorphClass())
            ->where('owner_id', $owner->getKey())
            ->whereKey($loginSessionId)
            ->first();

        if (!$loginSession) {
            return false;
        }

        $sessionId = $loginSession->session_id;
        $isCurrent = $sessionId === Session::getId();

        $loginSession->delete();

        if (config('session.driver') === 'database') {
            DB::table(config('session.table', 'sessions'))
                ->where('id', $sessionId)
                ->delete();
        }

        return $isCurrent;
    }

    public static function pruneExpired(Model $owner): void
    {
        $lifetimeMinutes = (int) config('session.lifetime', 120);
        $cutoff = now()->subMinutes($lifetimeMinutes);

        AccountLoginSession::query()
            ->where('owner_type', $owner->getMorphClass())
            ->where('owner_id', $owner->getKey())
            ->where(function ($query) use ($cutoff) {
                $query
                    ->whereNull('last_active_at')
                    ->orWhere('last_active_at', '<', $cutoff);
            })
            ->delete();
    }

    /**
     * @return array{0: string, 1: string}
     */
    private static function deviceFromUserAgent(string $userAgent): array
    {
        $ua = strtolower($userAgent);

        if ($ua === '') {
            return ['web', 'Web'];
        }

        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipod')) {
            return ['iphone', 'iPhone'];
        }

        if (str_contains($ua, 'ipad')) {
            return ['ipad', 'iPad'];
        }

        if (str_contains($ua, 'android')) {
            return ['android', 'Android'];
        }

        if (str_contains($ua, 'windows phone')) {
            return ['mobile', 'Windows Phone'];
        }

        if (str_contains($ua, 'mobile')) {
            return ['mobile', 'Mobile'];
        }

        return ['web', 'Web'];
    }

    private static function formatLastActive(?Carbon $lastActiveAt): string
    {
        if ($lastActiveAt === null) {
            return 'Unknown';
        }

        return $lastActiveAt->timezone(config('app.timezone'))->format('d/m/Y, H:i T');
    }
}
