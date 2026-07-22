<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Model;

class AccountSetting extends Model
{
    protected $fillable = [
        'owner_type',
        'owner_id',
        'language',
        'timezone',
        'currency',
        'theme',
        'push_notifications',
        'two_factor_enabled',
        'password_updated_at',
        'notify_booking_updates',
        'notify_groomer_messages',
        'notify_space_owner_messages',
        'notify_promotions',
        'notify_reminder_alerts',
        'profile_visibility',
        'data_sharing_consent',
        'email_marketing',
        'sms_notifications',
        'partner_offers',
        'analytics_tracking',
    ];

    protected function casts(): array
    {
        return [
            'push_notifications' => 'boolean',
            'two_factor_enabled' => 'boolean',
            'password_updated_at' => 'datetime',
            'notify_booking_updates' => 'boolean',
            'notify_groomer_messages' => 'boolean',
            'notify_space_owner_messages' => 'boolean',
            'notify_promotions' => 'boolean',
            'notify_reminder_alerts' => 'boolean',
            'profile_visibility' => 'boolean',
            'data_sharing_consent' => 'boolean',
            'email_marketing' => 'boolean',
            'sms_notifications' => 'boolean',
            'partner_offers' => 'boolean',
            'analytics_tracking' => 'boolean',
        ];
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get or create account settings for the given owner with defaults.
     */
    public static function forOwner(Model $owner): self
    {
        return static::query()->firstOrCreate(
            [
                'owner_type' => $owner->getMorphClass(),
                'owner_id' => $owner->getKey(),
            ],
            [
                'language' => 'en_GB',
                'timezone' => 'Europe/London',
                'currency' => 'GBP',
                'theme' => 'light',
            ]
        );
    }
}
