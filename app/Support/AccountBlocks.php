<?php

namespace App\Support;

use App\Models\AccountBlock;
use App\Models\GroomerSpacerProfile;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class AccountBlocks
{
    /**
     * @return list<array{
     *     id: int,
     *     name: string,
     *     subtitle: string,
     *     avatar_url: string|null
     * }>
     */
    public static function listFor(Model $owner): array
    {
        return AccountBlock::query()
            ->where('blocker_type', $owner->getMorphClass())
            ->where('blocker_id', $owner->getKey())
            ->with('blocked')
            ->orderByDesc('created_at')
            ->get()
            ->map(function (AccountBlock $block) {
                $blocked = $block->blocked;

                return [
                    'id' => $block->id,
                    'name' => self::displayName($blocked),
                    'subtitle' => 'Includes other accounts they may have or create.',
                    'avatar_url' => self::avatarUrl($blocked),
                ];
            })
            ->values()
            ->all();
    }

    public static function block(Model $blocker, Model $blocked): AccountBlock
    {
        abort_if(
            $blocker->is($blocked),
            422,
            'You cannot block your own account.'
        );

        return AccountBlock::query()->firstOrCreate([
            'blocker_type' => $blocker->getMorphClass(),
            'blocker_id' => $blocker->getKey(),
            'blocked_type' => $blocked->getMorphClass(),
            'blocked_id' => $blocked->getKey(),
        ]);
    }

    public static function unblock(Model $blocker, int $blockId): bool
    {
        $deleted = AccountBlock::query()
            ->where('blocker_type', $blocker->getMorphClass())
            ->where('blocker_id', $blocker->getKey())
            ->whereKey($blockId)
            ->delete();

        return $deleted > 0;
    }

    public static function isBlocked(Model $blocker, Model $blocked): bool
    {
        return AccountBlock::query()
            ->where('blocker_type', $blocker->getMorphClass())
            ->where('blocker_id', $blocker->getKey())
            ->where('blocked_type', $blocked->getMorphClass())
            ->where('blocked_id', $blocked->getKey())
            ->exists();
    }

    public static function unblockBlocked(Model $blocker, Model $blocked): bool
    {
        $deleted = AccountBlock::query()
            ->where('blocker_type', $blocker->getMorphClass())
            ->where('blocker_id', $blocker->getKey())
            ->where('blocked_type', $blocked->getMorphClass())
            ->where('blocked_id', $blocked->getKey())
            ->delete();

        return $deleted > 0;
    }

    public static function clearFor(Model $owner): void
    {
        AccountBlock::query()
            ->where(function ($query) use ($owner) {
                $query
                    ->where(function ($inner) use ($owner) {
                        $inner
                            ->where('blocker_type', $owner->getMorphClass())
                            ->where('blocker_id', $owner->getKey());
                    })
                    ->orWhere(function ($inner) use ($owner) {
                        $inner
                            ->where('blocked_type', $owner->getMorphClass())
                            ->where('blocked_id', $owner->getKey());
                    });
            })
            ->delete();
    }

    private static function displayName(?Model $blocked): string
    {
        if ($blocked instanceof User) {
            $name = trim((string) $blocked->name);

            return $name !== '' ? $name : 'Unknown user';
        }

        if ($blocked instanceof GroomerSpacerProfile) {
            $businessDetails = $blocked->business_details ?? [];
            if (!is_array($businessDetails)) {
                $businessDetails = [];
            }

            $businessName = trim((string) ($businessDetails['business_name'] ?? ''));
            if ($businessName !== '') {
                return $businessName;
            }

            $fullName = trim((string) ($blocked->full_name ?? ''));

            return $fullName !== '' ? $fullName : 'Unknown user';
        }

        return 'Unknown user';
    }

    private static function avatarUrl(?Model $blocked): ?string
    {
        if ($blocked instanceof User) {
            $path = trim((string) ($blocked->profile_image ?? ''));
            if ($path === '') {
                return null;
            }

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                return $path;
            }

            return asset('storage/' . ltrim($path, '/'));
        }

        return null;
    }
}
