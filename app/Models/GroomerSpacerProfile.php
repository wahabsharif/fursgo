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
        'business_profile',
        'legal_policy_agreements',
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
        'business_profile' => 'array',
        'legal_policy_agreements' => 'boolean',
    ];

    /**
     * Whether the verify-qualify personal / payout step is satisfied (no DB flag; inferred from stored JSON).
     */
    public function hasCompletedVerifyQualifyPersonalStep(): bool
    {
        if (trim((string) ($this->full_name ?? '')) === '') {
            return false;
        }

        $payout = $this->payout_details ?? [];
        if (! is_array($payout)) {
            $payout = is_string($payout) ? (json_decode($payout, true) ?: []) : [];
        }
        foreach (['account_holder_name', 'account_number', 'sort_code', 'iban'] as $key) {
            if (trim((string) ($payout[$key] ?? '')) === '') {
                return false;
            }
        }

        if (($this->account_type ?? '') === 'freelance') {
            $fd = $this->freelance_details ?? [];
            if (! is_array($fd)) {
                $fd = is_string($fd) ? (json_decode($fd, true) ?: []) : [];
            }
            if (trim((string) ($fd['contact_email'] ?? '')) === '') {
                return false;
            }
            if (trim((string) ($fd['contact_phone'] ?? '')) === '') {
                return false;
            }
            $ids = $fd['id_verification_images'] ?? [];

            return is_array($ids) && count($ids) > 0;
        }

        if (($this->account_type ?? '') !== 'registered_business') {
            return false;
        }

        $bd = $this->business_details ?? [];
        if (! is_array($bd)) {
            $bd = is_string($bd) ? (json_decode($bd, true) ?: []) : [];
        }
        foreach (['business_email', 'business_name', 'business_registration_number', 'business_phone'] as $key) {
            if (trim((string) ($bd[$key] ?? '')) === '') {
                return false;
            }
        }
        $bo = $bd['business_owner_id_images'] ?? [];

        return is_array($bo) && count($bo) > 0;
    }

    /**
     * Get the user that owns the profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
