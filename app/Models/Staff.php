<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Staff extends Model
{
    protected $table = 'staff';

    protected $fillable = [
        'goormer_spacer_profile_id',
        'name',
        'phone',
        'email',
        'job_title',
        'image',
        'working_hours',
        'holiday_time_off',
        'pause_booking',
    ];

    protected $casts = [
        'working_hours' => 'array',
        'holiday_time_off' => 'array',
        'pause_booking' => 'boolean',
    ];

    public function groomerSpacerProfile(): BelongsTo
    {
        return $this->belongsTo(GroomerSpacerProfile::class, 'goormer_spacer_profile_id');
    }

    public static function defaultWorkingHours(): array
    {
        $days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];

        return collect($days)
            ->mapWithKeys(
                fn($day) => [
                    $day => [
                        'status' => !in_array($day, ['saturday', 'sunday'], true),
                        'start' => '10:00',
                        'end' => '18:00',
                    ],
                ],
            )
            ->all();
    }

    /**
     * Staff list for Manage Availability: the logged-in account is always first.
     */
    public static function listedForProfile(GroomerSpacerProfile $profile): Collection
    {
        $email = trim((string) ($profile->email ?? ''));
        $name = trim((string) ($profile->full_name ?? '')) ?: 'You';
        $jobTitle = strtolower((string) ($profile->user_type ?? '')) === 'space'
            ? 'Space Owner'
            : 'Groomer';
        $basics = is_array($profile->business_basics) ? $profile->business_basics : [];
        $image = trim((string) ($basics['profile_photo_path'] ?? '')) ?: null;

        $ownerQuery = static::query()->where('goormer_spacer_profile_id', $profile->id);
        $found = $email !== ''
            ? (clone $ownerQuery)->whereRaw('LOWER(email) = ?', [mb_strtolower($email)])->first()
            : (clone $ownerQuery)->orderBy('id')->first();

        if (!$found instanceof self) {
            $owner = static::create([
                'goormer_spacer_profile_id' => $profile->id,
                'name' => $name,
                'email' => $email !== '' ? $email : null,
                'job_title' => $jobTitle,
                'image' => $image,
                'working_hours' => self::defaultWorkingHours(),
                'holiday_time_off' => [],
                'pause_booking' => false,
            ]);
        } else {
            $owner = $found;
            $updates = [];
            if ($name !== 'You' && $owner->name !== $name) {
                $updates['name'] = $name;
            }
            if (!$owner->job_title) {
                $updates['job_title'] = $jobTitle;
            }
            if ($image && !$owner->image) {
                $updates['image'] = $image;
            }
            if ($updates !== []) {
                $owner->fill($updates);
                $owner->save();
            }
        }

        $others = static::query()
            ->where('goormer_spacer_profile_id', $profile->id)
            ->where('id', '!=', $owner->id)
            ->orderBy('id')
            ->get();

        return collect([$owner])->concat($others)->values();
    }
}
