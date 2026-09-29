<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class GroomerSpacerProfile extends Authenticatable
{
    use Notifiable;

    protected $table = 'goormer_spacer_profiles';

    protected $fillable = [
        'full_name',
        'information_accuracy_confirmed',
        'email',
        'password',
        'user_type',
        'account_type',
        'select_location_type',
        'id_document_paths',
        'business_details',
        'payout_details',
        'insurance_details',
        'freelance_details',
        'business_basics',
        'groomer_business_profile',
        'spacer_business_profile',
        'profile_visit',
        'legal_policy_agreements',
        'auto_accept_booking',
    ];

    protected $casts = [
        'information_accuracy_confirmed' => 'boolean',
        'user_type' => 'string',
        'account_type' => 'string',
        'select_location_type' => 'array',
        'id_document_paths' => 'array',
        'business_details' => 'array',
        'payout_details' => 'array',
        'insurance_details' => 'array',
        'freelance_details' => 'array',
        'business_basics' => 'array',
        'groomer_business_profile' => 'array',
        'spacer_business_profile' => 'array',
        'profile_visit' => 'integer',
        'legal_policy_agreements' => 'boolean',
        'auto_accept_booking' => 'boolean',
    ];

    /**
     * Whether the business-verification personal / payout step is satisfied (no DB flag; inferred from stored JSON).
     */
    public function hasCompletedBusinessVerificationPersonalStep(): bool
    {
        $freelance = $this->freelance_details ?? [];
        if (!is_array($freelance)) {
            $freelance = is_string($freelance) ? (json_decode($freelance, true) ?: []) : [];
        }
        $business = $this->business_details ?? [];
        if (!is_array($business)) {
            $business = is_string($business) ? (json_decode($business, true) ?: []) : [];
        }

        // Once a verification status is recorded (including empty QA submissions), the personal step is done.
        foreach ([$freelance, $business] as $details) {
            $status = strtolower(trim((string) ($details['verification_status'] ?? '')));
            if (in_array($status, ['approved', 'pending', 'rejected'], true)) {
                return true;
            }
        }

        if (trim((string) ($this->full_name ?? '')) === '') {
            return false;
        }

        $payout = $this->payout_details ?? [];
        if (!is_array($payout)) {
            $payout = is_string($payout) ? (json_decode($payout, true) ?: []) : [];
        }
        foreach (['bank', 'account_holder_name', 'account_number', 'sort_code', 'iban'] as $key) {
            if (trim((string) ($payout[$key] ?? '')) === '') {
                return false;
            }
        }

        if (($this->account_type ?? '') === 'freelance') {
            if (trim((string) ($freelance['contact_email'] ?? '')) === '') {
                return false;
            }
            if (trim((string) ($freelance['contact_phone'] ?? '')) === '') {
                return false;
            }

            return count(self::governmentIdPathsFromFreelanceDetails($freelance)) > 0;
        }

        if (($this->account_type ?? '') !== 'registered_business') {
            return false;
        }

        foreach (['business_email', 'business_name', 'business_registration_number', 'business_phone'] as $key) {
            if (trim((string) ($business[$key] ?? '')) === '') {
                return false;
            }
        }

        return count(self::businessOwnerIdPathsFromBusinessDetails($business)) > 0;
    }

    /**
     * @param  array<string, mixed>|null  $businessDetails
     * @return list<string>
     */
    public static function businessOwnerIdPathsFromBusinessDetails(?array $businessDetails): array
    {
        if (!is_array($businessDetails)) {
            return [];
        }

        $paths = $businessDetails['business_owner_id_images'] ?? null;
        if (!is_array($paths)) {
            return [];
        }

        return array_values(array_filter($paths, fn($path) => is_string($path) && $path !== ''));
    }

    /**
     * @param  array<string, mixed>|null  $freelanceDetails
     * @return list<string>
     */
    public static function governmentIdPathsFromFreelanceDetails(?array $freelanceDetails): array
    {
        if (!is_array($freelanceDetails)) {
            return [];
        }

        $paths = $freelanceDetails['government_id'] ?? null;
        if (is_array($paths) && $paths !== []) {
            return array_values(array_filter($paths, fn($path) => is_string($path) && $path !== ''));
        }

        $legacy = $freelanceDetails['id_verification_images'] ?? null;
        if (!is_array($legacy)) {
            return [];
        }

        return array_values(array_filter($legacy, fn($path) => is_string($path) && $path !== ''));
    }

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the account settings for the profile.
     */
    public function accountSetting()
    {
        return $this->morphOne(AccountSetting::class, 'owner');
    }
}
