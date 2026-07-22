<?php

namespace App\Support;

use App\Models\AccountSetting;
use App\Models\GroomerSpacerProfile;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPdfWrapper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Carbon;

class AccountDataExport
{
    private const BOOLEAN_SETTINGS = [
        'push_notifications',
        'two_factor_enabled',
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

    public static function pdfResponse(Model $owner): Response
    {
        $settings = AccountSetting::forOwner($owner)->only([
            'language',
            'timezone',
            'currency',
            'theme',
            ...self::BOOLEAN_SETTINGS,
        ]);

        $profile = $owner->only(self::profileFields($owner));
        $ownerName = self::ownerDisplayName($owner, $profile);

        $pdf = Pdf::loadView('pdf.account-data', [
            'logoDataUri' => self::logoDataUri(),
            'exportedAt' => now()->format('d M Y, H:i'),
            'ownerName' => $ownerName,
            'ownerTypeLabel' => $owner instanceof GroomerSpacerProfile ? 'Business account' : 'Personal account',
            'profileRows' => self::profileRows($profile, $owner),
            'generalSettings' => self::settingsRows(array_intersect_key($settings, array_flip([
                'language',
                'timezone',
                'currency',
                'theme',
            ]))),
            'notificationSettings' => self::settingsRows(array_intersect_key($settings, array_flip([
                'push_notifications',
                'notify_booking_updates',
                'notify_groomer_messages',
                'notify_promotions',
                'notify_reminder_alerts',
                'email_marketing',
                'sms_notifications',
                'partner_offers',
            ]))),
            'privacySettings' => self::settingsRows(array_intersect_key($settings, array_flip([
                'two_factor_enabled',
                'profile_visibility',
                'data_sharing_consent',
                'analytics_tracking',
            ]))),
        ])->setPaper('a4', 'portrait');

        self::registerFonts($pdf);

        $filename = self::downloadFilename($ownerName);
        $output = $pdf->output();

        return response($output, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Content-Length' => (string) strlen($output),
            'Cache-Control' => 'private, must-revalidate, max-age=0',
        ]);
    }

    private static function downloadFilename(string $ownerName): string
    {
        $safeName = trim(preg_replace('/[\\\\\\/:*?"<>|]+/', '', $ownerName) ?? '');
        $safeName = preg_replace('/\s+/', ' ', $safeName) ?: 'Account holder';

        return 'Fursgo - ' . $safeName . ' Account data.pdf';
    }

    private static function registerFonts(DomPdfWrapper $pdf): void
    {
        $options = $pdf->getDomPDF()->getOptions();
        $fontDir = (string) $options->getFontDir();
        if ($fontDir !== '' && !is_dir($fontDir)) {
            File::makeDirectory($fontDir, 0755, true);
        }
        $fontCache = (string) $options->getFontCache();
        if ($fontCache !== '' && $fontCache !== $fontDir && !is_dir($fontCache)) {
            File::makeDirectory($fontCache, 0755, true);
        }

        $pairs = [
            [public_path('fonts/Lato'), ['family' => 'Lato', 'style' => 'normal', 'weight' => 'normal'], 'Lato-Regular.ttf'],
            [public_path('fonts/Lato'), ['family' => 'Lato', 'style' => 'normal', 'weight' => 'bold'], 'Lato-Bold.ttf'],
            [public_path('fonts/Lato'), ['family' => 'Lato', 'style' => 'italic', 'weight' => 'normal'], 'Lato-Italic.ttf'],
            [public_path('fonts/Lato'), ['family' => 'Lato', 'style' => 'italic', 'weight' => 'bold'], 'Lato-BoldItalic.ttf'],
            [public_path('fonts/Playfair_Display/static'), ['family' => 'Playfair Display', 'style' => 'normal', 'weight' => 'normal'], 'PlayfairDisplay-Regular.ttf'],
            [public_path('fonts/Playfair_Display/static'), ['family' => 'Playfair Display', 'style' => 'normal', 'weight' => 'bold'], 'PlayfairDisplay-SemiBold.ttf'],
            [public_path('fonts/Playfair_Display/static'), ['family' => 'Playfair Display', 'style' => 'italic', 'weight' => 'normal'], 'PlayfairDisplay-Italic.ttf'],
        ];

        $fontMetrics = $pdf->getDomPDF()->getFontMetrics();

        foreach ($pairs as [$dir, $style, $filename]) {
            $path = realpath($dir . DIRECTORY_SEPARATOR . $filename);
            if ($path !== false && is_readable($path)) {
                $fontMetrics->registerFont($style, $path);
            }
        }
    }

    private static function logoDataUri(): string
    {
        foreach ([
            public_path('images/logo/fursgo-invoice-wordmark.svg'),
            public_path('images/logo/logo.svg'),
        ] as $logoPath) {
            if (is_readable($logoPath)) {
                return 'data:image/svg+xml;base64,' . base64_encode((string) file_get_contents($logoPath));
            }
        }

        return '';
    }

    /**
     * @param  array<string, mixed>  $profile
     */
    private static function ownerDisplayName(Model $owner, array $profile): string
    {
        if ($owner instanceof User) {
            return (string) ($profile['name'] ?? 'Account holder');
        }

        if ($owner instanceof GroomerSpacerProfile) {
            return (string) ($profile['full_name'] ?? 'Account holder');
        }

        return 'Account holder';
    }

    /**
     * @return list<string>
     */
    private static function profileFields(Model $owner): array
    {
        if ($owner instanceof User) {
            return ['id', 'name', 'email', 'user_type', 'user_status', 'created_at', 'updated_at'];
        }

        if ($owner instanceof GroomerSpacerProfile) {
            return ['id', 'full_name', 'email', 'user_type', 'account_type', 'user_status', 'created_at', 'updated_at'];
        }

        return ['id', 'created_at', 'updated_at'];
    }

    /**
     * @param  array<string, mixed>  $profile
     * @return list<array{label: string, value: string}>
     */
    private static function profileRows(array $profile, Model $owner): array
    {
        $labels = [
            'id' => 'Account ID',
            'name' => 'Name',
            'full_name' => 'Name',
            'email' => 'Email',
            'user_type' => 'User type',
            'account_type' => 'Account type',
            'user_status' => 'Status',
            'created_at' => 'Created at',
            'updated_at' => 'Updated at',
        ];

        $rows = [];

        foreach (self::profileFields($owner) as $field) {
            $rows[] = [
                'label' => $labels[$field] ?? str($field)->headline()->toString(),
                'value' => self::formatValue($profile[$field] ?? null),
            ];
        }

        return $rows;
    }

    /**
     * @param  array<string, mixed>  $settings
     * @return list<array{label: string, value: string}>
     */
    private static function settingsRows(array $settings): array
    {
        $labels = [
            'language' => 'Language',
            'timezone' => 'Timezone',
            'currency' => 'Currency',
            'theme' => 'Theme',
            'push_notifications' => 'Push notifications',
            'two_factor_enabled' => 'Two-factor authentication',
            'notify_booking_updates' => 'Booking updates',
            'notify_groomer_messages' => 'Client messages',
            'notify_space_owner_messages' => 'Space owner messages',
            'notify_promotions' => 'Promotions',
            'notify_reminder_alerts' => 'Reminder alerts',
            'profile_visibility' => 'Profile visibility',
            'data_sharing_consent' => 'Data sharing consent',
            'email_marketing' => 'Email marketing',
            'sms_notifications' => 'SMS notifications',
            'partner_offers' => 'Partner offers',
            'analytics_tracking' => 'Analytics tracking',
        ];

        $rows = [];

        foreach ($settings as $key => $value) {
            $rows[] = [
                'label' => $labels[$key] ?? str($key)->headline()->toString(),
                'value' => self::formatSettingValue($key, $value),
            ];
        }

        return $rows;
    }

    private static function formatSettingValue(string $key, mixed $value): string
    {
        if ($key === 'language') {
            return AccountLanguages::labelFor((string) $value);
        }

        if ($key === 'theme') {
            return str((string) $value)->headline()->toString();
        }

        if (is_bool($value)) {
            return $value ? 'Enabled' : 'Disabled';
        }

        return self::formatValue($value);
    }

    private static function formatValue(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '—';
        }

        if ($value instanceof \DateTimeInterface) {
            return Carbon::instance($value)->format('d M Y, H:i');
        }

        return (string) $value;
    }
}
