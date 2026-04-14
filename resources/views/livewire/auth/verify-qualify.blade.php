<?php

use App\Models\GroomerSpacerProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.app')] class extends Component {
    use WithFileUploads;

    // Form display control
    public bool $showVerificationCard = true;
    public bool $showAccountPayoutsForm = false;
    public bool $showRegisteredBusiness = false;
    public bool $showFreelance = false;
    public bool $showVerificationStatus = false;
    public bool $showBusinessBasicsForm = false;
    public bool $showGroomerBusinessProfileForm = false;
    public bool $showSpacerBusinessProfileForm = false;

    /** Step 3 — Legal & Policy (after groomer/spacer profile). */
    public bool $showLegalPolicyForm = false;

    /** Step 4 — Success after legal agreements (“Start Grooming & Earning!”). */
    public bool $showStartGroomingEarningComplete = false;

    public bool $legal_terms_accepted = false;

    public bool $legal_privacy_accepted = false;

    /** Legal agreements panel: collapsed (scroll) vs expanded (full height). */
    public bool $legal_agreements_expanded = false;

    /** Step 2 — Business Basics (customer-facing profile). */
    public string $business_display_name = '';

    public string $business_tagline = '';

    public string $business_bio = '';

    public string $business_avatar_path = '';

    public $business_avatar_upload = null;

    /** Stored public-disk paths (max 3). */
    public array $business_gallery_paths = [];

    /** New gallery files before submit (max 3 total with paths). */
    public array $business_gallery_pending = [];

    public $business_gallery_pick = null;

    /** Step 2A — Groomer business profile. */
    public string $groomer_experience = '';
    public string $groomer_specialties = '';
    public array $groomer_pet_specialties = [];
    public string $groomer_specialty_other = '';
    public array $groomer_pet_sizes = [];
    public string $groomer_addon_input = '';
    public array $groomer_custom_addons = [];
    public array $groomer_selected_addons = [];

    /**
     * Fixed grooming service rows (matches verify-qualify-groomer-business-profile UI). Stored in groomer_business_profile.services.
     *
     * @var array<string, array{price: string, description?: string}>
     */
    public array $groomer_services_pricing = [
        'full_groom' => ['price' => '', 'description' => ''],
        'face_trim' => ['price' => ''],
    ];

    /**
     * Sample add-on price rows in the pricing table. Stored in groomer_business_profile.addon_pricing.
     *
     * @var array<string, array{price: string, description?: string}>
     */
    public array $groomer_addon_pricing = [
        'flea_tick' => ['price' => '', 'description' => ''],
        'fast_dry' => ['price' => ''],
    ];

    /** Step 2B — Spacer business profile (stored in spacer_business_profile JSON). */
    public string $spacer_bio = '';

    public array $spacer_services_pricing = [
        'hourly' => ['selected' => false, 'price' => ''],
        'half_day' => ['selected' => false, 'price' => ''],
        'full_day' => ['selected' => false, 'price' => ''],
    ];

    public array $spacer_addons_service = [
        'storage_locker' => ['selected' => false, 'price' => ''],
        'deep_clean' => ['selected' => false, 'price' => ''],
        'after_hours_access' => ['selected' => false, 'price' => ''],
        'early_hours_access' => ['selected' => false, 'price' => ''],
    ];

    public string $spacer_addon_input = '';

    /** @var array<int, array{name: string, selected: bool, price: string}> */
    public array $spacer_addon_custom_rows = [];

    public array $spacer_suitable_for = [];

    public string $spacer_rule_input = '';

    public array $spacer_rules_custom = [];

    public array $spacer_rules_preset_selected = [];

    public string $spacer_amenity_input = '';

    public array $spacer_amenities_custom = [];

    public array $spacer_amenities_preset_selected = [];

    // First form: Account verification
    public string $fursgo_usage = '';
    public string $account_type = '';
    public array $location_types = [];

    // Second form: Personal & Business information
    public string $full_name = '';
    public string $business_email = '';
    public string $business_name = '';
    public string $business_registration_number = '';
    public string $business_phone = '';
    public $id_documents = [];
    /** Saved storage paths (strings) from DB — never use as wire:model for file inputs. */
    public array $insurance_certificate_paths = [];
    /** New file picks only — bound to the insurance file input. */
    public $insurance_certificate_upload = [];
    public $business_owner_id_images = [];

    // Third form: Payout details
    public string $account_holder_name = '';
    public string $account_number = '';
    public string $sort_code = '';
    public string $iban = '';

    /** Freelance-only: stored in freelance_details JSON (service / home address). */
    public string $freelance_service_home_address_line1 = '';

    public string $freelance_service_home_address_line2 = '';

    /** User must check before Submit (stored in information_accuracy_confirmed). */
    public bool $information_accuracy_confirmed = false;

    /**
     * Initialize component with user data
     */
    public function mount(): void
    {
        $this->loadExistingData();
    }

    /**
     * Preserve uploaded files during Livewire updates
     */
    public function updated($propertyName)
    {
        // When any property is updated, preserve the uploaded files
        if (str_starts_with((string) $propertyName, 'spacer_')) {
            return;
        }

        if (in_array($propertyName, ['full_name', 'business_email', 'business_name', 'business_registration_number', 'business_phone', 'freelance_service_home_address_line1', 'freelance_service_home_address_line2', 'business_owner_id_images', 'insurance_certificate_upload', 'id_documents', 'account_holder_name', 'account_number', 'sort_code', 'iban', 'business_display_name', 'business_tagline', 'business_bio', 'groomer_experience', 'groomer_specialties', 'groomer_pet_specialties', 'groomer_specialty_other', 'groomer_pet_sizes', 'groomer_addon_input', 'groomer_custom_addons', 'groomer_selected_addons', 'groomer_services_pricing', 'groomer_addon_pricing'])) {
            // Don't reset file arrays when other properties change
            return;
        }

        if (in_array($propertyName, ['business_avatar_upload', 'business_gallery_pick', 'business_gallery_pending'], true)) {
            return;
        }

        // Log the property that's being updated for debugging
        \Log::info('Property updated: ' . $propertyName);
    }

    protected function validationAttributes(): array
    {
        return [
            'business_gallery_pick' => 'Gallery photo',
        ];
    }

    /**
     * Map DB / UI values to groomer | space for wizard branching.
     */
    private function normalizeFursgoUsage(?string $raw): string
    {
        $s = strtolower(trim((string) $raw));
        if ($s === 'spacer') {
            return 'space';
        }

        return $s;
    }

    /**
     * Align build-profile session substep with persisted user_type (session can be stale after DB edits).
     */
    private function coerceBuildProfileSubstepToUserType(GroomerSpacerProfile $user, string $substep): string
    {
        $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
        $bb = $user->business_basics ?? [];
        if (!is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }
        $hasDisplayName = trim((string) ($bb['display_name'] ?? '')) !== '';

        if ($usage === 'space') {
            if ($substep === 'groomer_profile') {
                return $hasDisplayName ? 'spacer_profile' : 'business_basics';
            }
        } elseif ($usage === 'groomer') {
            if ($substep === 'spacer_profile') {
                return $hasDisplayName ? 'groomer_profile' : 'business_basics';
            }
        }

        return $substep;
    }

    /**
     * Infer which "Build your profile" substep matches saved profile data (session-independent resume).
     *
     * @return 'business_basics'|'groomer_profile'|'spacer_profile'|'legal_policy'|'start_grooming'
     */
    private function inferVerificationBuildProfileSubstep(GroomerSpacerProfile $user): string
    {
        $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
        $bb = $user->business_basics ?? [];
        if (!is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }
        $hasDisplayName = trim((string) ($bb['display_name'] ?? '')) !== '';

        if (!$hasDisplayName) {
            return 'business_basics';
        }

        if ($usage === 'groomer') {
            $gp = $user->groomer_business_profile ?? [];
            if (!is_array($gp)) {
                $gp = is_string($gp) ? (json_decode($gp, true) ?: []) : [];
            }
            if ($gp === [] && isset($bb['groomer_profile'])) {
                $legacy = $bb['groomer_profile'];
                $gp = is_array($legacy) ? $legacy : (is_string($legacy) ? (json_decode($legacy, true) ?: []) : []);
            }
            $experience = trim((string) ($gp['experience'] ?? ''));
            $petSpecs = $gp['pet_specialties'] ?? [];
            $petSizes = $gp['pet_sizes'] ?? [];
            $profileDone = $experience !== '' && is_array($petSpecs) && count($petSpecs) > 0 && is_array($petSizes) && count($petSizes) > 0;

            if (!$profileDone) {
                return 'groomer_profile';
            }

            return $user->legal_policy_agreements ? 'start_grooming' : 'legal_policy';
        }

        if ($usage === 'space') {
            $sp = $user->spacer_business_profile ?? [];
            if (!is_array($sp)) {
                $sp = is_string($sp) ? (json_decode($sp, true) ?: []) : [];
            }
            $bio = trim((string) ($sp['bio'] ?? ''));
            $pricing = $sp['services_pricing'] ?? [];
            $anyService = false;
            if (is_array($pricing)) {
                foreach ($pricing as $row) {
                    if (is_array($row) && !empty($row['selected'])) {
                        $anyService = true;
                        break;
                    }
                }
            }
            $profileDone = $bio !== '' && $anyService;

            if (!$profileDone) {
                return 'spacer_profile';
            }

            return $user->legal_policy_agreements ? 'start_grooming' : 'legal_policy';
        }

        return 'business_basics';
    }

    /**
     * Load existing data from user profile
     */
    public function loadExistingData(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if ($user) {
            $this->showVerificationStatus = false;
            $this->showBusinessBasicsForm = false;

            // Get current step from session
            $currentStep = session('verification_current_step', 'account_payouts');

            // Load existing verification data (normalize so "Space", "spacer", etc. match wizard branches)
            $this->fursgo_usage = $this->normalizeFursgoUsage($user->user_type ?? '');
            $this->account_type = $user->account_type ?? '';
            $this->location_types = $user->select_location_type ?? [];

            // Load existing personal information data
            $this->full_name = $user->full_name ?? '';
            $this->information_accuracy_confirmed = (bool) ($user->information_accuracy_confirmed ?? false);

            // Load existing files from JSON fields (model may cast to array)
            $businessDetails = $user->business_details ?? [];
            if (!is_array($businessDetails)) {
                $businessDetails = is_string($businessDetails) ? (json_decode($businessDetails, true) ?: []) : [];
            }
            $freelanceDetails = $user->freelance_details ?? [];
            if (!is_array($freelanceDetails)) {
                $freelanceDetails = is_string($freelanceDetails) ? (json_decode($freelanceDetails, true) ?: []) : [];
            }
            $payoutDetails = $user->payout_details ?? [];
            if (!is_array($payoutDetails)) {
                $payoutDetails = is_string($payoutDetails) ? (json_decode($payoutDetails, true) ?: []) : [];
            }
            $insuranceDetails = $user->insurance_details ?? [];
            if (!is_array($insuranceDetails)) {
                $insuranceDetails = is_string($insuranceDetails) ? (json_decode($insuranceDetails, true) ?: []) : [];
            }

            // Registered business: business_details JSON. Freelance: freelance_details JSON (different keys).
            if (($user->account_type ?? '') === 'freelance') {
                $this->freelance_service_home_address_line1 = trim((string) ($freelanceDetails['service_home_address_line1'] ?? ''));
                $this->freelance_service_home_address_line2 = trim((string) ($freelanceDetails['service_home_address_line2'] ?? ''));
                if ($this->freelance_service_home_address_line1 === '' && $this->freelance_service_home_address_line2 === '') {
                    $legacyTrading = trim((string) ($freelanceDetails['trading_name'] ?? ''));
                    if ($legacyTrading !== '') {
                        $this->freelance_service_home_address_line1 = $legacyTrading;
                    }
                }
                $this->business_email = trim((string) ($freelanceDetails['contact_email'] ?? ''));
                $this->business_name = '';
                $this->business_registration_number = '';
                $this->business_phone = $freelanceDetails['contact_phone'] ?? '';
            } else {
                $this->business_email = trim((string) ($businessDetails['business_email'] ?? ''));
                $this->business_name = $businessDetails['business_name'] ?? '';
                $this->business_registration_number = $businessDetails['business_registration_number'] ?? '';
                $this->business_phone = $businessDetails['business_phone'] ?? '';
            }

            // Load payout details
            $this->account_holder_name = $payoutDetails['account_holder_name'] ?? '';
            $this->account_number = $payoutDetails['account_number'] ?? '';
            $this->sort_code = $payoutDetails['sort_code'] ?? '';
            $this->iban = $payoutDetails['iban'] ?? '';

            // Load existing file paths (these are stored separately from actual files)
            $idPaths = $user->id_document_paths ?? null;
            if (is_array($idPaths)) {
                $this->id_documents = $idPaths;
            } elseif (is_string($idPaths) && $idPaths !== '') {
                $this->id_documents = json_decode($idPaths, true) ?: [];
            } else {
                $this->id_documents = [];
            }
            $rawInsPaths = $insuranceDetails['insurance_certificate_paths'] ?? [];
            $this->insurance_certificate_paths = is_array($rawInsPaths) ? array_values(array_filter($rawInsPaths, fn($p) => is_string($p) && $p !== '')) : [];
            $this->insurance_certificate_upload = [];
            $this->business_owner_id_images = ($user->account_type ?? '') === 'freelance' ? $freelanceDetails['id_verification_images'] ?? [] : $businessDetails['business_owner_id_images'] ?? [];

            // Which wizard screen to show (session), or last step if profile data shows onboarding is done.
            $personalInfoDone = $user->hasCompletedVerifyQualifyPersonalStep();

            if (session('verification_build_profile_step', false) && $personalInfoDone && $user instanceof GroomerSpacerProfile) {
                $bpSub = (string) session('verification_build_profile_substep', 'business_basics');
                if ($bpSub === 'complete') {
                    session()->forget(['verification_build_profile_step', 'verification_build_profile_substep']);
                    session()->save();
                }
            }

            if (session('verification_build_profile_step', false) && $personalInfoDone && $user instanceof GroomerSpacerProfile) {
                $buildProfileSubstep = $this->inferVerificationBuildProfileSubstep($user);
                session([
                    'verification_build_profile_step' => true,
                    'verification_build_profile_substep' => $buildProfileSubstep,
                ]);
                session()->save();
                $buildProfileSubstep = $this->coerceBuildProfileSubstepToUserType($user, $buildProfileSubstep);
                if ($buildProfileSubstep !== (string) session('verification_build_profile_substep', '')) {
                    session(['verification_build_profile_substep' => $buildProfileSubstep]);
                    session()->save();
                }
                $this->enterBusinessBasicsStep($user, false);
                if ($buildProfileSubstep === 'groomer_profile') {
                    $this->showBusinessBasicsForm = false;
                    $this->showGroomerBusinessProfileForm = true;
                    $this->showSpacerBusinessProfileForm = false;
                    $this->showLegalPolicyForm = false;
                    $this->showStartGroomingEarningComplete = false;
                } elseif ($buildProfileSubstep === 'spacer_profile') {
                    $this->showBusinessBasicsForm = false;
                    $this->showGroomerBusinessProfileForm = false;
                    $this->showSpacerBusinessProfileForm = true;
                    $this->showLegalPolicyForm = false;
                    $this->showStartGroomingEarningComplete = false;
                } elseif ($buildProfileSubstep === 'legal_policy') {
                    $this->showBusinessBasicsForm = false;
                    $this->showGroomerBusinessProfileForm = false;
                    $this->showSpacerBusinessProfileForm = false;
                    $this->showLegalPolicyForm = true;
                    $this->showStartGroomingEarningComplete = false;
                    if ($user->legal_policy_agreements) {
                        $this->legal_terms_accepted = true;
                        $this->legal_privacy_accepted = true;
                    }
                } elseif ($buildProfileSubstep === 'start_grooming') {
                    $this->showBusinessBasicsForm = false;
                    $this->showGroomerBusinessProfileForm = false;
                    $this->showSpacerBusinessProfileForm = false;
                    $this->showLegalPolicyForm = false;
                    $this->showStartGroomingEarningComplete = true;
                }
            } elseif (session('verify_qualify_show_approved', false) && $personalInfoDone) {
                $this->showVerificationStatus = true;
                $this->showVerificationCard = false;
                $this->showAccountPayoutsForm = false;
                $this->showRegisteredBusiness = false;
                $this->showFreelance = false;
            } elseif ($personalInfoDone) {
                $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
                if (($usage === 'space' || $usage === 'groomer') && $user instanceof GroomerSpacerProfile) {
                    $buildProfileSubstep = $this->inferVerificationBuildProfileSubstep($user);
                    session([
                        'verification_build_profile_step' => true,
                        'verification_build_profile_substep' => $buildProfileSubstep,
                    ]);
                    session()->save();
                    $buildProfileSubstep = $this->coerceBuildProfileSubstepToUserType($user, $buildProfileSubstep);
                    session(['verification_build_profile_substep' => $buildProfileSubstep]);
                    session()->save();
                    $this->enterBusinessBasicsStep($user, false);
                    if ($buildProfileSubstep === 'groomer_profile') {
                        $this->showBusinessBasicsForm = false;
                        $this->showGroomerBusinessProfileForm = true;
                        $this->showSpacerBusinessProfileForm = false;
                        $this->showLegalPolicyForm = false;
                        $this->showStartGroomingEarningComplete = false;
                    } elseif ($buildProfileSubstep === 'spacer_profile') {
                        $this->showBusinessBasicsForm = false;
                        $this->showGroomerBusinessProfileForm = false;
                        $this->showSpacerBusinessProfileForm = true;
                        $this->showLegalPolicyForm = false;
                        $this->showStartGroomingEarningComplete = false;
                    } elseif ($buildProfileSubstep === 'legal_policy') {
                        $this->showBusinessBasicsForm = false;
                        $this->showGroomerBusinessProfileForm = false;
                        $this->showSpacerBusinessProfileForm = false;
                        $this->showLegalPolicyForm = true;
                        $this->showStartGroomingEarningComplete = false;
                        if ($user->legal_policy_agreements) {
                            $this->legal_terms_accepted = true;
                            $this->legal_privacy_accepted = true;
                        }
                    } elseif ($buildProfileSubstep === 'start_grooming') {
                        $this->showBusinessBasicsForm = false;
                        $this->showGroomerBusinessProfileForm = false;
                        $this->showSpacerBusinessProfileForm = false;
                        $this->showLegalPolicyForm = false;
                        $this->showStartGroomingEarningComplete = true;
                    }
                } else {
                    $this->showVerificationCard = false;
                    $this->showAccountPayoutsForm = false;
                    $this->showRegisteredBusiness = $user->account_type === 'registered_business';
                    $this->showFreelance = $user->account_type === 'freelance';
                }
            } else {
                switch ($currentStep) {
                    case 'registered_business':
                        $this->showVerificationCard = false;
                        $this->showAccountPayoutsForm = false;
                        $this->showRegisteredBusiness = true;
                        $this->showFreelance = false;
                        break;
                    case 'freelance_groomer':
                        $this->showVerificationCard = false;
                        $this->showAccountPayoutsForm = false;
                        $this->showRegisteredBusiness = false;
                        $this->showFreelance = true;
                        break;
                    case 'account_payouts':
                        $this->showVerificationCard = false;
                        $this->showAccountPayoutsForm = true;
                        $this->showRegisteredBusiness = false;
                        $this->showFreelance = false;
                        break;
                    default:
                        $this->showVerificationCard = true;
                        $this->showAccountPayoutsForm = false;
                        $this->showRegisteredBusiness = false;
                        $this->showFreelance = false;
                        break;
                }
            }

            // Never show the wrong build-profile partial if session substep was stale vs user_type
            if ($user instanceof GroomerSpacerProfile) {
                $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
                $bb = $user->business_basics ?? [];
                if (!is_array($bb)) {
                    $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
                }
                $hasDisplayName = trim((string) ($bb['display_name'] ?? '')) !== '';
                if ($usage === 'space' && $this->showGroomerBusinessProfileForm) {
                    $this->showGroomerBusinessProfileForm = false;
                    if ($hasDisplayName && !$this->showBusinessBasicsForm) {
                        $this->showSpacerBusinessProfileForm = true;
                    }
                } elseif ($usage === 'groomer' && $this->showSpacerBusinessProfileForm) {
                    $this->showSpacerBusinessProfileForm = false;
                    if ($hasDisplayName && !$this->showBusinessBasicsForm) {
                        $this->showGroomerBusinessProfileForm = true;
                    }
                }
            }
        }
    }

    /**
     * Handle ID document uploads
     */
    public function updatedIdDocuments($files)
    {
        $this->id_documents = $files;
    }

    /**
     * Handle business owner ID images uploads — keep existing stored paths when new files are picked.
     */
    public function updatedBusinessOwnerIdImages($value): void
    {
        $prev = $this->business_owner_id_images ?? [];
        $keptPaths = [];
        foreach (is_array($prev) ? $prev : [] as $item) {
            if (is_string($item) && $item !== '') {
                $keptPaths[] = $item;
            }
        }
        $incoming = is_array($value) ? $value : ($value ? [$value] : []);
        $combined = $keptPaths;
        foreach ($incoming as $item) {
            if ($item instanceof TemporaryUploadedFile || $item instanceof UploadedFile) {
                $combined[] = $item;
            } elseif (is_string($item) && $item !== '' && !in_array($item, $combined, true)) {
                $combined[] = $item;
            }
        }
        $this->business_owner_id_images = array_values($combined);
    }

    /**
     * Remove a stored business-owner ID file from profile JSON and disk.
     */
    public function removeStoredBusinessOwnerImage(string $path): void
    {
        if ($path === '' || str_contains($path, '..')) {
            return;
        }

        $user = Auth::guard('groomer_spacer')->user();
        if (!$user) {
            return;
        }

        $isFreelance = ($user->account_type ?? '') === 'freelance';

        if ($isFreelance) {
            $freelanceDetails = $user->freelance_details ?? [];
            if (!is_array($freelanceDetails)) {
                $freelanceDetails = is_string($freelanceDetails) ? (json_decode($freelanceDetails, true) ?: []) : [];
            }
            $images = $freelanceDetails['id_verification_images'] ?? [];
            if (!is_array($images) || !in_array($path, $images, true)) {
                return;
            }
            $freelanceDetails['id_verification_images'] = array_values(array_filter($images, fn($p) => $p !== $path));
        } else {
            $businessDetails = $user->business_details ?? [];
            if (!is_array($businessDetails)) {
                $businessDetails = is_string($businessDetails) ? (json_decode($businessDetails, true) ?: []) : [];
            }

            $images = $businessDetails['business_owner_id_images'] ?? [];
            if (!is_array($images) || !in_array($path, $images, true)) {
                return;
            }

            $businessDetails['business_owner_id_images'] = array_values(array_filter($images, fn($p) => $p !== $path));
        }

        $this->business_owner_id_images = array_values(
            array_filter($this->business_owner_id_images ?? [], function ($item) use ($path) {
                if ($item instanceof TemporaryUploadedFile || $item instanceof UploadedFile) {
                    return true;
                }

                return !(is_string($item) && $item === $path);
            }),
        );

        $idPaths = [];
        $rawId = $user->id_document_paths ?? null;
        if (is_array($rawId)) {
            $idPaths = $rawId;
        } elseif (is_string($rawId) && $rawId !== '') {
            $idPaths = json_decode($rawId, true) ?: [];
        }
        $idPaths = array_values(array_filter($idPaths, fn($p) => is_string($p) && $p !== $path));

        $this->id_documents = array_values(array_filter($this->id_documents ?? [], fn($item) => !is_string($item) || $item !== $path));

        $payload = [];
        if ($isFreelance) {
            $payload['freelance_details'] = $freelanceDetails;
        } else {
            $payload['business_details'] = $businessDetails;
        }
        if (Schema::hasColumn($user->getTable(), 'id_document_paths')) {
            $payload['id_document_paths'] = $idPaths;
        }

        $user->update($payload);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Remove a stored insurance certificate path from profile JSON and disk.
     */
    public function removeStoredInsuranceCertificate(string $path): void
    {
        if ($path === '' || str_contains($path, '..')) {
            return;
        }

        $user = Auth::guard('groomer_spacer')->user();
        if (!$user) {
            return;
        }

        $insuranceDetails = $user->insurance_details ?? [];
        if (!is_array($insuranceDetails)) {
            $insuranceDetails = is_string($insuranceDetails) ? (json_decode($insuranceDetails, true) ?: []) : [];
        }

        $paths = $insuranceDetails['insurance_certificate_paths'] ?? [];
        if (!is_array($paths) || !in_array($path, $paths, true)) {
            return;
        }

        $insuranceDetails['insurance_certificate_paths'] = array_values(array_filter($paths, fn($p) => $p !== $path));

        $this->insurance_certificate_paths = array_values(array_filter($this->insurance_certificate_paths ?? [], fn($p) => $p !== $path));

        $user->update(['insurance_details' => $insuranceDetails]);

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    /**
     * Handle business verification submission
     */
    public function submit(): void
    {
        $validated = $this->validate([
            'fursgo_usage' => ['required', 'string', 'in:groomer,space'],
            'account_type' => ['required', 'string', 'in:registered_business,freelance'],
            'location_types' => ['required', 'array', 'min:1'],
            'location_types.*' => ['string', 'in:space_visits,commercial_salon,home_studio,house_visit,mobile_van'],
        ]);

        $user = Auth::guard('groomer_spacer')->user();

        if ($user) {
            // Update user profile with verification data
            $user->update([
                'user_type' => $this->normalizeFursgoUsage($this->fursgo_usage),
                'account_type' => $this->account_type,
                'select_location_type' => $this->location_types,
            ]);

            // Save the next step to session then trigger a full client-side navigation
            if ($this->account_type === 'freelance') {
                session(['verification_current_step' => 'freelance_groomer']);
            } else {
                session(['verification_current_step' => 'registered_business']);
            }

            session()->forget('verify_qualify_show_approved');
            session()->save();
            $this->js('window.location.href = ' . json_encode(route('verify-qualify')));
        }
    }

    /**
     * Check if form is valid for enabling submit button
     */
    public function isFormValid(): bool
    {
        return $this->fursgo_usage && $this->account_type && count($this->location_types) > 0;
    }

    public function verifyBusiness()
    {
        $this->showVerificationStatus = false;
        $this->showVerificationCard = false;
        $this->showAccountPayoutsForm = true;
        $this->showRegisteredBusiness = false;
        $this->showFreelance = false;
        session(['verification_current_step' => 'account_payouts']);
        session()->save();
    }

    /**
     * Handle back button click - go to previous step
     */
    public function goBack(): void
    {
        $this->showVerificationStatus = false;
        $this->showVerificationCard = false;
        $this->showAccountPayoutsForm = true;
        $this->showRegisteredBusiness = false;
        $this->showFreelance = false;
        $this->showBusinessBasicsForm = false;
        $this->showGroomerBusinessProfileForm = false;
        $this->showSpacerBusinessProfileForm = false;
        $this->showLegalPolicyForm = false;
        $this->showStartGroomingEarningComplete = false;
        $this->legal_agreements_expanded = false;
        session(['verification_current_step' => 'account_payouts']);
        session()->forget('verification_build_profile_step');
        session()->forget('verification_build_profile_substep');
        session()->save();
    }

    /**
     * Leave the post-submit approval screen and open step 2 (Business Basics).
     */
    public function continueToBuildProfile(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile || !$user->hasCompletedVerifyQualifyPersonalStep()) {
            return;
        }

        session()->forget('verify_qualify_show_approved');
        session([
            'verification_build_profile_step' => true,
            'verification_build_profile_substep' => 'business_basics',
        ]);
        session()->save();

        $this->enterBusinessBasicsStep($user, true);
    }

    /**
     * Show Business Basics and optionally re-hydrate fields from the database.
     */
    private function enterBusinessBasicsStep(GroomerSpacerProfile $user, bool $refreshFromDb): void
    {
        $this->showBusinessBasicsForm = true;
        $this->showVerificationStatus = false;
        $this->showVerificationCard = false;
        $this->showAccountPayoutsForm = false;
        $this->showRegisteredBusiness = false;
        $this->showFreelance = false;
        $this->showGroomerBusinessProfileForm = false;
        $this->showSpacerBusinessProfileForm = false;
        $this->showLegalPolicyForm = false;
        $this->showStartGroomingEarningComplete = false;
        $this->legal_agreements_expanded = false;

        if ($refreshFromDb) {
            $user->refresh();
        }

        $this->hydrateBusinessBasicsFields($user);
    }

    private function hydrateBusinessBasicsFields(GroomerSpacerProfile $user): void
    {
        $bb = $user->business_basics ?? [];
        if (!is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }

        $this->business_display_name = trim((string) ($bb['display_name'] ?? ''));
        if ($this->business_display_name === '' && ($user->account_type ?? '') === 'registered_business') {
            $bd = $user->business_details ?? [];
            if (!is_array($bd)) {
                $bd = is_string($bd) ? (json_decode($bd, true) ?: []) : [];
            }
            $this->business_display_name = trim((string) ($bd['business_name'] ?? ''));
        }

        $this->business_tagline = trim((string) ($bb['tagline'] ?? ''));
        $this->business_bio = trim((string) ($bb['bio'] ?? ''));
        $this->business_avatar_path = $this->normalizeStoredPublicPath((string) ($bb['profile_photo_path'] ?? ''));

        $paths = $bb['gallery_paths'] ?? [];
        if (is_string($paths) && $paths !== '') {
            $paths = json_decode($paths, true) ?: [];
        }
        $this->business_gallery_paths = [];
        if (is_array($paths)) {
            foreach ($paths as $p) {
                if (!is_string($p) || $p === '') {
                    continue;
                }
                $n = $this->normalizeStoredPublicPath($p);
                if ($n !== '') {
                    $this->business_gallery_paths[] = $n;
                }
                if (count($this->business_gallery_paths) >= 3) {
                    break;
                }
            }
        }

        $this->business_avatar_upload = null;
        $this->business_gallery_pending = [];
        $this->business_gallery_pick = null;

        $groomerProfile = $user->groomer_business_profile ?? [];
        if (!is_array($groomerProfile)) {
            $groomerProfile = is_string($groomerProfile) ? (json_decode($groomerProfile, true) ?: []) : [];
        }
        if ($groomerProfile === [] && isset($bb['groomer_profile'])) {
            $legacy = $bb['groomer_profile'];
            $groomerProfile = is_array($legacy) ? $legacy : (is_string($legacy) ? (json_decode($legacy, true) ?: []) : []);
        }
        $this->groomer_experience = trim((string) ($groomerProfile['experience'] ?? ''));
        $this->groomer_specialties = trim((string) ($groomerProfile['specialties'] ?? ''));
        $petSpecialties = $groomerProfile['pet_specialties'] ?? [];
        if (!is_array($petSpecialties)) {
            $petSpecialties = [];
        }
        $this->groomer_pet_specialties = array_values(array_filter($petSpecialties, fn($v) => in_array($v, ['dog', 'cat', 'other'], true)));
        $this->groomer_specialty_other = trim((string) ($groomerProfile['specialty_other'] ?? ''));
        $petSizes = $groomerProfile['pet_sizes'] ?? [];
        if (!is_array($petSizes)) {
            $petSizes = [];
        }
        $this->groomer_pet_sizes = array_values(array_filter($petSizes, fn($v) => in_array($v, ['small', 'medium', 'large'], true)));
        $customAddons = $groomerProfile['custom_addons'] ?? [];
        if (!is_array($customAddons)) {
            $customAddons = [];
        }
        $this->groomer_custom_addons = array_values(array_filter($customAddons, fn($v) => is_string($v) && trim($v) !== ''));
        $selectedAddons = $groomerProfile['selected_addons'] ?? [];
        if (!is_array($selectedAddons)) {
            $selectedAddons = [];
        }
        $this->groomer_selected_addons = array_values(array_filter($selectedAddons, fn($v) => is_string($v) && trim($v) !== ''));
        $this->groomer_addon_input = '';

        $this->hydrateGroomerServiceAndAddonPricing($groomerProfile);

        $this->hydrateSpacerBusinessProfile($user);
    }

    /**
     * Load groomer_services_pricing / groomer_addon_pricing from saved JSON (fixed keys for the template rows).
     */
    private function hydrateGroomerServiceAndAddonPricing(array $groomerProfile): void
    {
        $defServices = [
            'full_groom' => ['price' => '', 'description' => ''],
            'face_trim' => ['price' => ''],
        ];
        $svc = $groomerProfile['services'] ?? [];
        if (!is_array($svc)) {
            $svc = [];
        }
        $outS = [];
        foreach ($defServices as $k => $shape) {
            $row = isset($svc[$k]) && is_array($svc[$k]) ? $svc[$k] : [];
            $merged = $shape;
            foreach (array_keys($shape) as $f) {
                if (array_key_exists($f, $row) && (is_string($row[$f]) || is_numeric($row[$f]))) {
                    $merged[$f] = trim((string) $row[$f]);
                }
            }
            $outS[$k] = $merged;
        }
        $this->groomer_services_pricing = $outS;

        $defAddons = [
            'flea_tick' => ['price' => '', 'description' => ''],
            'fast_dry' => ['price' => ''],
        ];
        $addon = $groomerProfile['addon_pricing'] ?? [];
        if (!is_array($addon)) {
            $addon = [];
        }
        $outA = [];
        foreach ($defAddons as $k => $shape) {
            $row = isset($addon[$k]) && is_array($addon[$k]) ? $addon[$k] : [];
            $merged = $shape;
            foreach (array_keys($shape) as $f) {
                if (array_key_exists($f, $row) && (is_string($row[$f]) || is_numeric($row[$f]))) {
                    $merged[$f] = trim((string) $row[$f]);
                }
            }
            $outA[$k] = $merged;
        }
        $this->groomer_addon_pricing = $outA;
    }

    private function mergeSpacerProfileKeyedRows(array $defaults, array $loaded): array
    {
        $out = [];
        foreach ($defaults as $k => $shape) {
            $row = isset($loaded[$k]) && is_array($loaded[$k]) ? $loaded[$k] : [];
            $merged = $shape;
            foreach ($shape as $f => $_defaultVal) {
                if (!array_key_exists($f, $row)) {
                    continue;
                }
                if ($f === 'selected') {
                    $merged['selected'] = (bool) $row[$f];
                } else {
                    $merged[$f] = trim((string) $row[$f]);
                }
            }
            $out[$k] = $merged;
        }

        return $out;
    }

    private function hydrateSpacerBusinessProfile(GroomerSpacerProfile $user): void
    {
        $data = $user->spacer_business_profile ?? [];
        if (!is_array($data)) {
            $data = is_string($data) ? (json_decode($data, true) ?: []) : [];
        }

        $bb = $user->business_basics ?? [];
        if (!is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }

        if ($data === [] && isset($bb['spacer_profile']) && is_array($bb['spacer_profile']) && $bb['spacer_profile'] !== []) {
            $leg = $bb['spacer_profile'];
            $data = [
                'legacy' => [
                    'location' => (string) ($leg['location'] ?? ''),
                    'capacity' => (string) ($leg['capacity'] ?? ''),
                    'amenities' => (string) ($leg['amenities'] ?? ''),
                ],
            ];
        }

        $this->spacer_bio = trim((string) ($data['bio'] ?? ''));

        if ($this->spacer_bio === '' && isset($data['legacy']) && is_array($data['legacy'])) {
            $leg = $data['legacy'];
            $parts = array_values(array_filter([trim((string) ($leg['location'] ?? '')) !== '' ? 'Location: ' . trim($leg['location']) : null, trim((string) ($leg['capacity'] ?? '')) !== '' ? 'Capacity: ' . trim($leg['capacity']) : null, trim((string) ($leg['amenities'] ?? '')) !== '' ? trim($leg['amenities']) : null], fn($x) => $x !== null));
            $this->spacer_bio = implode("\n\n", $parts);
        }

        $defSvc = [
            'hourly' => ['selected' => false, 'price' => ''],
            'half_day' => ['selected' => false, 'price' => ''],
            'full_day' => ['selected' => false, 'price' => ''],
        ];
        $svcIn = $data['services_pricing'] ?? [];
        $this->spacer_services_pricing = $this->mergeSpacerProfileKeyedRows($defSvc, is_array($svcIn) ? $svcIn : []);

        $defF = [];
        foreach ($this->spacerFursgoAddonCatalog() as $slug => $_label) {
            $defF[$slug] = ['selected' => false, 'price' => ''];
        }
        $fuIn = $data['addons_service'] ?? ($data['addons_fursgo'] ?? []);
        $this->spacer_addons_service = $this->mergeSpacerProfileKeyedRows($defF, is_array($fuIn) ? $fuIn : []);

        $this->spacer_addon_custom_rows = [];
        $customIn = $data['addons_custom'] ?? [];
        if (is_array($customIn)) {
            foreach ($customIn as $row) {
                if (is_array($row) && isset($row['name']) && trim((string) $row['name']) !== '') {
                    $this->spacer_addon_custom_rows[] = [
                        'name' => trim((string) $row['name']),
                        'selected' => (bool) ($row['selected'] ?? true),
                        'price' => trim((string) ($row['price'] ?? '')),
                    ];
                } elseif (is_string($row) && trim($row) !== '') {
                    $this->spacer_addon_custom_rows[] = ['name' => trim($row), 'selected' => true, 'price' => ''];
                }
            }
        }

        $sf = $data['suitable_for'] ?? [];
        $this->spacer_suitable_for = is_array($sf) ? array_values(array_filter($sf, fn($v) => is_string($v) && $v !== '')) : [];

        $this->spacer_rules_preset_selected = [];
        $this->spacer_rules_custom = [];
        $rulesFlat = $data['rules'] ?? [];
        if (is_array($rulesFlat) && $rulesFlat !== [] && array_is_list($rulesFlat)) {
            foreach ($rulesFlat as $r) {
                if (!is_string($r) || trim($r) === '') {
                    continue;
                }
                if (in_array($r, $this->spacerRulesPresetCatalog(), true)) {
                    $this->spacer_rules_preset_selected[] = $r;
                } else {
                    $this->spacer_rules_custom[] = $r;
                }
            }
        } else {
            $rp = $data['rules_presets'] ?? [];
            if (is_array($rp)) {
                $this->spacer_rules_preset_selected = array_values(array_intersect($this->spacerRulesPresetCatalog(), $rp));
            }
            $rc = $data['rules_custom'] ?? [];
            if (is_array($rc)) {
                $this->spacer_rules_custom = array_values(array_filter($rc, fn($v) => is_string($v) && trim($v) !== ''));
            }
        }
        $this->spacer_rules_preset_selected = array_values(array_unique($this->spacer_rules_preset_selected));

        $this->spacer_amenities_preset_selected = [];
        $this->spacer_amenities_custom = [];
        $amenitiesFlat = $data['amenities'] ?? [];
        if (is_array($amenitiesFlat) && $amenitiesFlat !== [] && array_is_list($amenitiesFlat)) {
            foreach ($amenitiesFlat as $a) {
                if (!is_string($a) || trim($a) === '') {
                    continue;
                }
                if (in_array($a, $this->spacerAmenitiesPresetCatalog(), true)) {
                    $this->spacer_amenities_preset_selected[] = $a;
                } else {
                    $this->spacer_amenities_custom[] = $a;
                }
            }
        } else {
            $ap = $data['amenities_presets'] ?? [];
            if (is_array($ap)) {
                $this->spacer_amenities_preset_selected = array_values(array_intersect($this->spacerAmenitiesPresetCatalog(), $ap));
            }
            $ac = $data['amenities_custom'] ?? [];
            if (is_array($ac)) {
                $this->spacer_amenities_custom = array_values(array_filter($ac, fn($v) => is_string($v) && trim($v) !== ''));
            }
        }
        $this->spacer_amenities_preset_selected = array_values(array_unique($this->spacer_amenities_preset_selected));

        $this->spacer_addon_input = '';
        $this->spacer_rule_input = '';
        $this->spacer_amenity_input = '';
    }

    public function updatedBusinessGalleryPick($value): void
    {
        if (!$value instanceof TemporaryUploadedFile) {
            $this->business_gallery_pick = null;

            return;
        }

        $this->resetValidation('business_gallery_pick');

        $this->validate([
            'business_gallery_pick' => ['file'],
        ]);

        $room = 3 - count($this->business_gallery_paths) - count($this->business_gallery_pending);
        if ($room > 0) {
            $this->business_gallery_pending[] = $value;
        }

        $this->business_gallery_pick = null;
    }

    public function removeBusinessAvatar(): void
    {
        if ($this->business_avatar_upload instanceof TemporaryUploadedFile) {
            $this->business_avatar_upload = null;

            return;
        }

        if ($this->business_avatar_upload !== null && $this->business_avatar_upload !== '') {
            $this->business_avatar_upload = null;

            return;
        }

        $path = $this->business_avatar_path;
        $this->business_avatar_path = '';

        if ($path !== '' && !str_contains($path, '..') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $user = Auth::guard('groomer_spacer')->user();
        if ($user) {
            $bb = $user->business_basics ?? [];
            if (!is_array($bb)) {
                $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
            }
            unset($bb['profile_photo_path']);
            $user->update(['business_basics' => $bb]);
        }
    }

    public function removeBusinessGalleryPath(int $index): void
    {
        if (!isset($this->business_gallery_paths[$index])) {
            return;
        }

        $path = $this->business_gallery_paths[$index];
        unset($this->business_gallery_paths[$index]);
        $this->business_gallery_paths = array_values($this->business_gallery_paths);

        if (is_string($path) && $path !== '' && !str_contains($path, '..') && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }

        $user = Auth::guard('groomer_spacer')->user();
        if ($user) {
            $bb = $user->business_basics ?? [];
            if (!is_array($bb)) {
                $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
            }
            $bb['gallery_paths'] = $this->business_gallery_paths;
            $user->update(['business_basics' => $bb]);
        }
    }

    public function removeBusinessGalleryPending(int $index): void
    {
        if (!isset($this->business_gallery_pending[$index])) {
            return;
        }

        $file = $this->business_gallery_pending[$index];
        unset($this->business_gallery_pending[$index]);
        $this->business_gallery_pending = array_values($this->business_gallery_pending);
    }

    /**
     * Human-readable file size without the PHP intl extension (Laravel Number::fileSize requires intl).
     */
    public function formatBytesForDisplay(int $bytes): string
    {
        if ($bytes <= 0) {
            return '0 B';
        }

        $k = 1024;
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = (int) floor(log($bytes, $k));
        $i = min(max($i, 0), count($units) - 1);
        $value = $bytes / $k ** $i;
        $decimals = $i === 0 ? 0 : ($value >= 10 ? 1 : 2);

        return number_format($value, $decimals, '.', '') . ' ' . $units[$i];
    }

    public function isBusinessBasicsContinueEnabled(): bool
    {
        return trim($this->business_display_name) !== '';
    }

    public function isGroomerBusinessProfileContinueEnabled(): bool
    {
        $hasSpecialty = count($this->groomer_pet_specialties) > 0;
        $otherOk = !in_array('other', $this->groomer_pet_specialties, true) || trim($this->groomer_specialty_other) !== '';

        return trim($this->groomer_experience) !== '' && $hasSpecialty && count($this->groomer_pet_sizes) > 0 && $otherOk;
    }

    public function isSpacerBusinessProfileContinueEnabled(): bool
    {
        if (trim($this->spacer_bio) === '') {
            return false;
        }
        foreach ($this->spacer_services_pricing as $row) {
            if (!empty($row['selected'])) {
                return true;
            }
        }

        return false;
    }

    public function isLegalPolicyContinueEnabled(): bool
    {
        return $this->legal_terms_accepted;
    }

    public function toggleLegalAgreementsExpanded(): void
    {
        $this->legal_agreements_expanded = !$this->legal_agreements_expanded;
    }

    public function submitLegalPolicy(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        $this->validate([
            'legal_terms_accepted' => ['accepted'],
        ]);

        $user->update(['legal_policy_agreements' => true]);

        $this->legal_agreements_expanded = false;
        $this->showLegalPolicyForm = false;
        $this->showStartGroomingEarningComplete = true;

        session(['verification_build_profile_step' => true]);
        $this->setBuildProfileSubstep('start_grooming');
        session()->save();
    }

    /** @return array<string, string> slug => label */
    public function spacerFursgoAddonCatalog(): array
    {
        return [
            'storage_locker' => 'Storage Locker',
            'deep_clean' => 'Deep Clean',
            'after_hours_access' => 'After Hours access',
            'early_hours_access' => 'Early Hours Access',
        ];
    }

    /** @return array<string, array{name: string, meta: string|null}> */
    public function spacerServicesPricingRowLabels(): array
    {
        return [
            'hourly' => ['name' => 'Hourly', 'meta' => null],
            'half_day' => ['name' => 'Half-Day', 'meta' => '(4 hours)'],
            'full_day' => ['name' => 'Full-Day', 'meta' => '(8 hours)'],
        ];
    }

    /** @return list<string> */
    public function spacerSuitableForCatalog(): array
    {
        return ['Full Groom', 'Bath & Brush', 'Nail Trim', 'Ear Cleaning', 'Medicated Bath', 'Teeth Brushing', 'Deshedding', 'Sanitary Trim', 'Dematting', 'Paw Pad Trim', 'Anal Gland Expression'];
    }

    /** @return list<string> */
    public function spacerRulesPresetCatalog(): array
    {
        return ['Suitable for all breeds and coat types', 'Leave space tidy', 'Not suitable for aggressive animals in heat', 'No overnight stays', 'Pets must remain supervised', 'No smoking'];
    }

    /** @return list<string> */
    public function spacerAmenitiesPresetCatalog(): array
    {
        return ['Grooming Table', 'Bath', 'Dryer', 'Towels', 'Waiting Area', 'Parking', 'Wi-fi'];
    }

    public function addSpacerCustomAddonRow(): void
    {
        $name = trim($this->spacer_addon_input);
        if ($name === '') {
            return;
        }
        $this->spacer_addon_custom_rows[] = [
            'name' => $name,
            'selected' => true,
            'price' => '',
        ];
        $this->spacer_addon_input = '';
    }

    public function addSpacerRuleCustom(): void
    {
        $t = trim($this->spacer_rule_input);
        if ($t === '') {
            return;
        }
        if (!in_array($t, $this->spacer_rules_custom, true)) {
            $this->spacer_rules_custom[] = $t;
        }
        $this->spacer_rule_input = '';
    }

    public function addSpacerAmenityCustom(): void
    {
        $t = trim($this->spacer_amenity_input);
        if ($t === '') {
            return;
        }
        if (!in_array($t, $this->spacer_amenities_custom, true)) {
            $this->spacer_amenities_custom[] = $t;
        }
        $this->spacer_amenity_input = '';
    }

    public function addGroomerCustomAddon(): void
    {
        $name = trim($this->groomer_addon_input);
        if ($name === '') {
            return;
        }

        if (in_array($name, $this->groomerAddonCatalog(), true)) {
            if (!in_array($name, $this->groomer_selected_addons, true)) {
                $this->groomer_selected_addons[] = $name;
            }
            $this->groomer_addon_input = '';

            return;
        }

        if (!in_array($name, $this->groomer_custom_addons, true)) {
            $this->groomer_custom_addons[] = $name;
        }
        if (!in_array($name, $this->groomer_selected_addons, true)) {
            $this->groomer_selected_addons[] = $name;
        }

        $this->groomer_addon_input = '';
    }

    private function groomerAddonCatalog(): array
    {
        return ['Flea & Tick Treatment', 'Hypoallergenic Shampoo Upgrade', 'Tear-Stain Treatment', 'Coat Shine Spray', 'Nail Grinding', 'Coat Colour Enhancing Shampoo', 'Fast-Dry Service (express grooming)', 'Breath Freshner Gel', 'Deep Conditioning Mask', 'Shed-Control Shampoo', 'Deodorising Treatment', 'Anti-Itch Treatment', 'Soft-Claws / Nail Caps Application', 'Premium Fragrance Upgrade', 'Paw Fur Shaping'];
    }

    private function setBuildProfileSubstep(string $substep): void
    {
        session(['verification_build_profile_substep' => $substep]);
        session()->save();
    }

    public function submitBusinessBasics(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user) {
            return;
        }

        $this->validate([
            'business_display_name' => ['required', 'string', 'max:255'],
            'business_tagline' => ['nullable', 'string', 'max:500'],
            'business_bio' => ['nullable', 'string', 'max:5000'],
            'business_avatar_upload' => ['nullable', 'file'],
        ]);

        foreach ($this->business_gallery_pending as $i => $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $this->validate([
                    "business_gallery_pending.$i" => ['file'],
                ]);
            }
        }

        $avatarPath = $this->business_avatar_path;
        if ($this->business_avatar_upload instanceof TemporaryUploadedFile) {
            if ($avatarPath !== '' && !str_contains($avatarPath, '..') && Storage::disk('public')->exists($avatarPath)) {
                Storage::disk('public')->delete($avatarPath);
            }
            $dir = $this->storageDirectoryForUpload($this->business_avatar_upload, 'groomer_spacer_profile');
            $avatarPath = $this->business_avatar_upload->store($dir, 'public');
        }

        $gallery = $this->business_gallery_paths;
        foreach ($this->business_gallery_pending as $file) {
            if (count($gallery) >= 3) {
                break;
            }
            if ($file instanceof TemporaryUploadedFile) {
                $dir = $this->storageDirectoryForUpload($file, 'groomer_spacer_profile_gallery');
                $gallery[] = $file->store($dir, 'public');
            }
        }
        $gallery = array_slice($gallery, 0, 3);

        $existing = $user->business_basics ?? [];
        if (!is_array($existing)) {
            $existing = is_string($existing) ? (json_decode($existing, true) ?: []) : [];
        }

        $user->update([
            'business_basics' => array_merge($existing, [
                'display_name' => trim($this->business_display_name),
                'tagline' => trim($this->business_tagline),
                'bio' => trim($this->business_bio),
                'profile_photo_path' => $avatarPath,
                'gallery_paths' => $gallery,
            ]),
        ]);

        $this->business_gallery_pending = [];
        $this->business_avatar_upload = null;
        $this->business_gallery_paths = $gallery;
        $this->business_avatar_path = $avatarPath;
        $this->showBusinessBasicsForm = false;
        $usage = $this->normalizeFursgoUsage($this->fursgo_usage);
        $this->showGroomerBusinessProfileForm = $usage === 'groomer';
        $this->showSpacerBusinessProfileForm = $usage === 'space';
        if ($this->showGroomerBusinessProfileForm || $this->showSpacerBusinessProfileForm) {
            session(['verification_build_profile_step' => true]);
            session()->save();
        }
        if ($this->showGroomerBusinessProfileForm) {
            $this->setBuildProfileSubstep('groomer_profile');
        } elseif ($this->showSpacerBusinessProfileForm) {
            $this->setBuildProfileSubstep('spacer_profile');
        }

        if (!$this->showGroomerBusinessProfileForm && !$this->showSpacerBusinessProfileForm) {
            $this->showBusinessBasicsForm = true;
            $this->setBuildProfileSubstep('business_basics');
            $this->js('alert(' . json_encode('Please select how you use FursGo (groomer or space).') . ')');
        }
    }

    public function submitGroomerBusinessProfile(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user) {
            return;
        }

        $this->validate([
            'groomer_experience' => ['required', 'string', 'max:1000'],
            'groomer_pet_specialties' => ['required', 'array', 'min:1'],
            'groomer_pet_specialties.*' => ['in:dog,cat,other'],
            'groomer_specialty_other' => ['nullable', 'string', 'max:255'],
            'groomer_pet_sizes' => ['required', 'array', 'min:1'],
            'groomer_pet_sizes.*' => ['in:small,medium,large'],
            'groomer_custom_addons' => ['nullable', 'array'],
            'groomer_custom_addons.*' => ['string', 'max:255'],
            'groomer_selected_addons' => ['nullable', 'array'],
            'groomer_selected_addons.*' => ['string', 'max:255'],
        ]);

        if (in_array('other', $this->groomer_pet_specialties, true) && trim($this->groomer_specialty_other) === '') {
            $this->addError('groomer_specialty_other', 'Please specify your other specialty.');

            return;
        }

        $specialtyLabels = [
            'dog' => 'Dog',
            'cat' => 'Cat',
            'other' => 'Other',
        ];
        $selectedSpecialties = [];
        foreach ($this->groomer_pet_specialties as $key) {
            $selectedSpecialties[] = $specialtyLabels[$key] ?? $key;
        }
        if (in_array('other', $this->groomer_pet_specialties, true) && trim($this->groomer_specialty_other) !== '') {
            $selectedSpecialties[] = trim($this->groomer_specialty_other);
        }

        $user->update([
            'groomer_business_profile' => [
                'experience' => trim($this->groomer_experience),
                'specialties' => implode(', ', $selectedSpecialties),
                'pet_specialties' => array_values($this->groomer_pet_specialties),
                'specialty_other' => trim($this->groomer_specialty_other),
                'pet_sizes' => array_values($this->groomer_pet_sizes),
                'custom_addons' => array_values($this->groomer_custom_addons),
                'selected_addons' => array_values($this->groomer_selected_addons),
                'services' => $this->groomer_services_pricing,
                'addon_pricing' => $this->groomer_addon_pricing,
            ],
        ]);
        $this->showGroomerBusinessProfileForm = false;
        $this->showLegalPolicyForm = true;
        $this->setBuildProfileSubstep('legal_policy');
    }

    public function submitSpacerBusinessProfile(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        $this->validate([
            'spacer_bio' => ['required', 'string', 'max:5000'],
            'spacer_services_pricing' => ['required', 'array'],
            'spacer_addon_custom_rows' => ['nullable', 'array'],
            'spacer_suitable_for' => ['nullable', 'array'],
            'spacer_suitable_for.*' => ['string', 'max:255'],
            'spacer_rules_custom' => ['nullable', 'array'],
            'spacer_rules_custom.*' => ['string', 'max:500'],
            'spacer_amenities_custom' => ['nullable', 'array'],
            'spacer_amenities_custom.*' => ['string', 'max:500'],
        ]);

        $anyService = false;
        foreach ($this->spacer_services_pricing as $row) {
            if (!empty($row['selected'])) {
                $anyService = true;
                break;
            }
        }
        if (!$anyService) {
            $this->addError('spacer_services_pricing', 'Select at least one pricing option (Hourly, Half-Day, or Full-Day).');

            return;
        }

        $rulesMerged = array_values(array_unique(array_merge($this->spacer_rules_preset_selected, $this->spacer_rules_custom)));
        $amenitiesMerged = array_values(array_unique(array_merge($this->spacer_amenities_preset_selected, $this->spacer_amenities_custom)));

        $payload = [
            'bio' => trim($this->spacer_bio),
            'services_pricing' => $this->spacer_services_pricing,
            'addons_service' => $this->spacer_addons_service,
            'addons_custom' => $this->spacer_addon_custom_rows,
            'suitable_for' => array_values($this->spacer_suitable_for),
            'rules' => $rulesMerged,
            'amenities' => $amenitiesMerged,
        ];

        $updates = ['spacer_business_profile' => $payload];

        $bb = $user->business_basics ?? [];
        if (!is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }
        if (isset($bb['spacer_profile'])) {
            unset($bb['spacer_profile']);
            $updates['business_basics'] = $bb;
        }

        $user->update($updates);

        $this->showSpacerBusinessProfileForm = false;
        $this->showStartGroomingEarningComplete = false;
        $this->showLegalPolicyForm = true;
        $this->setBuildProfileSubstep('legal_policy');
    }

    /**
     * Paths saved in business_basics must match the public disk root (storage/app/public), not a URL or prefixed path.
     */
    private function normalizeStoredPublicPath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));
        if ($path === '') {
            return '';
        }
        $path = ltrim($path, '/');
        foreach (['public/', 'storage/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }

        return $path;
    }

    /**
     * URL for persisted business-basics files: served via an auth route so Apache/IIS cannot 403 direct /storage access.
     */
    public function publicDiskUrl(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));
        if ($path === '') {
            return '';
        }
        if (preg_match('#^https?://#i', $path)) {
            return $path;
        }
        $path = $this->normalizeStoredPublicPath($path);
        if ($path === '') {
            return '';
        }

        return route('groomer-spacer.business-basics-file', [
            't' => Crypt::encryptString($path),
        ]);
    }

    /**
     * Public-disk subdirectory for an upload: {baseFolder}/images|pdfs|files (folders are created by store()).
     */
    private function storageDirectoryForUpload(UploadedFile $file, string $baseFolder): string
    {
        $mime = (string) ($file->getMimeType() ?? '');

        if (str_starts_with($mime, 'image/')) {
            return $baseFolder . '/images';
        }

        if ($mime === 'application/pdf' || $mime === 'application/x-pdf') {
            return $baseFolder . '/pdfs';
        }

        $ext = strtolower($file->getClientOriginalExtension() ?? '');

        return match ($ext) {
            'jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'heic' => $baseFolder . '/images',
            'pdf' => $baseFolder . '/pdfs',
            default => $baseFolder . '/files',
        };
    }

    /**
     * Freelance vs registered rules: prefer DB `account_type` so we stay correct when the Livewire property is empty/stale
     * (freelance users would otherwise match registered rules and fail on business_name / min ID files).
     */
    private function isFreelanceAccount(?\Illuminate\Contracts\Auth\Authenticatable $user = null): bool
    {
        $user = $user ?? Auth::guard('groomer_spacer')->user();
        $persisted = $user ? (string) ($user->account_type ?? '') : '';

        if ($persisted === 'freelance') {
            return true;
        }
        if ($persisted === 'registered_business') {
            return false;
        }

        return $this->account_type === 'freelance' || $this->showFreelance;
    }

    /**
     * Handle personal information form submission
     */
    public function submitPersonalInfo(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user) {
            return;
        }

        $persistedType = (string) ($user->account_type ?? '');
        if ($persistedType !== '') {
            $this->account_type = $persistedType;
        }

        $isFreelance = $this->isFreelanceAccount($user);

        if ($isFreelance) {
            $this->validate([
                'full_name' => ['required', 'string', 'max:255'],
                'business_email' => ['required', 'email'],
                'freelance_service_home_address_line1' => ['nullable', 'string', 'max:500'],
                'freelance_service_home_address_line2' => ['nullable', 'string', 'max:500'],
                'business_phone' => ['required', 'string', 'max:20'],
                'business_owner_id_images' => ['nullable', 'array'],
                'id_documents' => ['nullable', 'array'],
                'insurance_certificate_upload' => ['nullable', 'array'],
                'account_holder_name' => ['required', 'string', 'max:255'],
                'account_number' => ['required', 'string', 'max:50'],
                'sort_code' => ['required', 'string', 'max:20'],
                'iban' => ['required', 'string', 'max:50'],
                'information_accuracy_confirmed' => ['accepted'],
            ]);
            $hasBo = is_array($this->business_owner_id_images) && count($this->business_owner_id_images) > 0;
            $hasId = is_array($this->id_documents) && count($this->id_documents) > 0;
            if (!$hasBo && !$hasId) {
                $this->addError('business_owner_id_images', 'Please upload at least one valid ID document.');

                return;
            }
        } else {
            $this->validate([
                'full_name' => ['required', 'string', 'max:255'],
                'business_email' => ['required', 'email'],
                'business_name' => ['required', 'string', 'max:255'],
                'business_registration_number' => ['required', 'string', 'max:255'],
                'business_phone' => ['required', 'string', 'max:20'],
                'business_owner_id_images' => ['required', 'array', 'min:1'],
                'id_documents' => ['nullable', 'array'],
                'insurance_certificate_upload' => ['nullable', 'array'],
                'account_holder_name' => ['required', 'string', 'max:255'],
                'account_number' => ['required', 'string', 'max:50'],
                'sort_code' => ['required', 'string', 'max:20'],
                'iban' => ['required', 'string', 'max:50'],
                'information_accuracy_confirmed' => ['accepted'],
            ]);
        }

        foreach ($this->business_owner_id_images ?? [] as $index => $image) {
            if ($image instanceof UploadedFile) {
                $this->validate([
                    "business_owner_id_images.$index" => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                ]);
            } elseif (!is_string($image) || trim($image) === '') {
                $this->addError('business_owner_id_images', 'Each business owner ID entry must be a valid file or saved path.');
                return;
            }
        }

        if ($this->id_documents && is_array($this->id_documents)) {
            foreach ($this->id_documents as $index => $document) {
                if ($document instanceof UploadedFile) {
                    $this->validate([
                        "id_documents.$index" => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                    ]);
                } elseif ($document !== null && $document !== '' && !is_string($document)) {
                    $this->addError('id_documents', 'Invalid ID document entry.');
                    return;
                }
            }
        }

        $insuranceUploadFiles = [];
        if (is_array($this->insurance_certificate_upload)) {
            $insuranceUploadFiles = $this->insurance_certificate_upload;
        } elseif ($this->insurance_certificate_upload instanceof UploadedFile || $this->insurance_certificate_upload instanceof TemporaryUploadedFile) {
            $insuranceUploadFiles = [$this->insurance_certificate_upload];
        }

        foreach ($insuranceUploadFiles as $index => $certificate) {
            if ($certificate instanceof UploadedFile || $certificate instanceof TemporaryUploadedFile) {
                $this->validate([
                    "insurance_certificate_upload.$index" => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:51200'],
                ]);
            } else {
                $this->addError('insurance_certificate_upload', 'Invalid insurance certificate upload.');

                return;
            }
        }

        if ($user) {
            // ID proof uploads (same files as business owner ID; id_document_paths mirrors for legacy column)
            $documentPaths = [];

            // Insurance: keep existing stored paths + persist new uploads from insurance_certificate_upload only
            $insuranceCertificatePaths = array_values(array_filter($this->insurance_certificate_paths ?? [], fn($p) => is_string($p) && $p !== ''));
            foreach ($insuranceUploadFiles as $certificate) {
                if ($certificate instanceof UploadedFile || $certificate instanceof TemporaryUploadedFile) {
                    $dir = $this->storageDirectoryForUpload($certificate, 'insurance_certificates');
                    $insuranceCertificatePaths[] = $certificate->store($dir, 'public');
                }
            }

            // Store the business owner ID images if uploaded
            $businessOwnerIdImagePaths = [];
            if ($this->business_owner_id_images && is_array($this->business_owner_id_images)) {
                foreach ($this->business_owner_id_images as $image) {
                    if ($image instanceof \Illuminate\Http\UploadedFile) {
                        $dir = $this->storageDirectoryForUpload($image, 'business_owner_id_images');
                        $path = $image->store($dir, 'public');
                        $businessOwnerIdImagePaths[] = $path;
                        $documentPaths[] = $path;
                    } elseif (is_string($image)) {
                        $businessOwnerIdImagePaths[] = $image;
                        $documentPaths[] = $image;
                    }
                }
            }

            if ($this->id_documents && is_array($this->id_documents)) {
                foreach ($this->id_documents as $document) {
                    if ($document instanceof \Illuminate\Http\UploadedFile) {
                        $dir = $this->storageDirectoryForUpload($document, 'id_documents');
                        $path = $document->store($dir, 'public');
                        $documentPaths[] = $path;
                    } elseif (is_string($document)) {
                        $documentPaths[] = $document;
                    }
                }
            }

            $table = $user->getTable();

            if ($isFreelance) {
                $existingFreelance = $user->freelance_details ?? [];
                if (!is_array($existingFreelance)) {
                    $existingFreelance = is_string($existingFreelance) ? (json_decode($existingFreelance, true) ?: []) : [];
                }

                $freelanceDetailsPayload = [
                    'service_home_address_line1' => trim((string) ($this->freelance_service_home_address_line1 ?? '')),
                    'service_home_address_line2' => trim((string) ($this->freelance_service_home_address_line2 ?? '')),
                    'contact_email' => trim((string) $this->business_email),
                    'contact_phone' => trim((string) $this->business_phone),
                    'id_verification_images' => $businessOwnerIdImagePaths,
                    'information_accuracy_confirmed_at' => now()->toIso8601String(),
                    'last_submitted_at' => now()->toIso8601String(),
                ];

                $mergedFreelance = array_merge($existingFreelance, $freelanceDetailsPayload);
                foreach (['trading_name', 'personal_info_completed', 'personal_info_completed_at'] as $legacyKey) {
                    unset($mergedFreelance[$legacyKey]);
                }

                $payload = [
                    'full_name' => $this->full_name,
                    'freelance_details' => $mergedFreelance,
                    'payout_details' => [
                        'account_holder_name' => $this->account_holder_name,
                        'account_number' => $this->account_number,
                        'sort_code' => $this->sort_code,
                        'iban' => $this->iban,
                    ],
                    'insurance_details' => [
                        'insurance_certificate_paths' => $insuranceCertificatePaths,
                    ],
                ];
            } else {
                $existingBusiness = $user->business_details ?? [];
                if (!is_array($existingBusiness)) {
                    $existingBusiness = is_string($existingBusiness) ? (json_decode($existingBusiness, true) ?: []) : [];
                }

                $businessDetailsPayload = [
                    'business_name' => $this->business_name,
                    'business_registration_number' => $this->business_registration_number,
                    'business_phone' => $this->business_phone,
                    'business_email' => $this->business_email,
                    'business_owner_id_images' => $businessOwnerIdImagePaths,
                ];

                $payload = [
                    'full_name' => $this->full_name,
                    'business_details' => array_merge($existingBusiness, $businessDetailsPayload),
                    'payout_details' => [
                        'account_holder_name' => $this->account_holder_name,
                        'account_number' => $this->account_number,
                        'sort_code' => $this->sort_code,
                        'iban' => $this->iban,
                    ],
                    'insurance_details' => [
                        'insurance_certificate_paths' => $insuranceCertificatePaths,
                    ],
                ];
            }

            $payload['information_accuracy_confirmed'] = true;

            if (Schema::hasColumn($table, 'id_document_paths')) {
                $payload['id_document_paths'] = $documentPaths;
            }
            $user->update($payload);

            $this->insurance_certificate_paths = $insuranceCertificatePaths;
            $this->insurance_certificate_upload = [];

            session(['verify_qualify_show_approved' => true]);
            session()->save();

            $this->showVerificationStatus = true;
            $this->showRegisteredBusiness = false;
            $this->showFreelance = false;
            $this->showVerificationCard = false;
            $this->showAccountPayoutsForm = false;
        }
    }

    /**
     * Check if personal information form is valid for enabling submit button
     */
    public function isPersonalInfoFormValid(): bool
    {
        $bo = is_array($this->business_owner_id_images) ? $this->business_owner_id_images : [];
        $idDocs = is_array($this->id_documents) ? $this->id_documents : [];
        $hasIdProof = count($bo) > 0 || count($idDocs) > 0;
        $emailOk = (bool) filter_var($this->business_email, FILTER_VALIDATE_EMAIL);

        if ($this->isFreelanceAccount()) {
            return $this->information_accuracy_confirmed && $this->full_name && $this->business_email && $this->business_phone && $this->account_holder_name && $this->account_number && $this->sort_code && $this->iban && $hasIdProof && $emailOk;
        }

        return $this->information_accuracy_confirmed && $this->full_name && $this->business_email && $this->business_name && $this->business_registration_number && $this->business_phone && $this->account_holder_name && $this->account_number && $this->sort_code && $this->iban && $hasIdProof && $emailOk;
    }

    /**
     * Debug payload for browser console (why Submit / Continue is disabled).
     */
    public function getSubmitButtonDebug(): array
    {
        $bo = is_array($this->business_owner_id_images) ? $this->business_owner_id_images : [];
        $idDocs = is_array($this->id_documents) ? $this->id_documents : [];
        $hasIdProof = count($bo) > 0 || count($idDocs) > 0;

        return [
            'step' => [
                'showVerificationStatus' => $this->showVerificationStatus,
                'showBusinessBasicsForm' => $this->showBusinessBasicsForm,
                'showGroomerBusinessProfileForm' => $this->showGroomerBusinessProfileForm,
                'showSpacerBusinessProfileForm' => $this->showSpacerBusinessProfileForm,
                'showLegalPolicyForm' => $this->showLegalPolicyForm,
                'showStartGroomingEarningComplete' => $this->showStartGroomingEarningComplete,
                'showVerificationCard' => $this->showVerificationCard,
                'showAccountPayoutsForm' => $this->showAccountPayoutsForm,
                'showRegisteredBusiness' => $this->showRegisteredBusiness,
                'showFreelance' => $this->showFreelance,
            ],
            'business_basics' => [
                'continue_enabled' => $this->isBusinessBasicsContinueEnabled(),
            ],
            'continue_step' => [
                'fursgo_usage' => $this->fursgo_usage,
                'account_type' => $this->account_type,
                'location_types' => $this->location_types,
                'location_types_count' => count($this->location_types),
                'continue_would_enable' => (bool) ($this->fursgo_usage && $this->account_type && count($this->location_types) > 0),
            ],
            'personal_step' => [
                'full_name' => $this->full_name !== '',
                'business_email' => $this->business_email !== '',
                'business_name' => $this->business_name !== '',
                'business_registration_number' => $this->business_registration_number !== '',
                'business_phone' => $this->business_phone !== '',
                'account_holder_name' => $this->account_holder_name !== '',
                'account_number' => $this->account_number !== '',
                'sort_code' => $this->sort_code !== '',
                'iban' => $this->iban !== '',
                'email_format_ok' => (bool) filter_var($this->business_email, FILTER_VALIDATE_EMAIL),
                'business_owner_id_images_count' => count($bo),
                'id_documents_count' => count($idDocs),
                'has_id_proof' => $hasIdProof,
                'information_accuracy_confirmed' => $this->information_accuracy_confirmed,
                'submit_would_enable' => $this->isPersonalInfoFormValid(),
            ],
        ];
    }

    public function activeSidebarStepLabel(): string
    {
        if ($this->showStartGroomingEarningComplete) {
            return 'Start Grooming & Earning!';
        }
        if ($this->showLegalPolicyForm) {
            return 'Legal & Policy Agreement';
        }
        if ($this->showBusinessBasicsForm || $this->showGroomerBusinessProfileForm || $this->showSpacerBusinessProfileForm) {
            return 'Build Your Profile';
        }

        return 'Verify & Qualify';
    }
};
?>

<section class="container mt-5 mb-5">
    <div class="verification-wrapper">
        <!-- Floating Sidebar (step tracker) -->
        <div class="floating-sidebar">
            <div class="sidebar-header">
                <h1>{{ $this->activeSidebarStepLabel() }}</h1>
            </div>
            <div class="steps-list">
                <div
                    class="step-item {{ ($showVerificationCard || $showAccountPayoutsForm || $showRegisteredBusiness || $showFreelance || $showVerificationStatus) && !$showBusinessBasicsForm && !$showGroomerBusinessProfileForm && !$showSpacerBusinessProfileForm && !$showLegalPolicyForm && !$showStartGroomingEarningComplete ? 'active' : '' }}">
                    <div class="step-content">
                        <div class="step-title"><span>1.</span>
                            <p>Verify & Qualify</p>
                        </div>
                    </div>
                </div>
                <div
                    class="step-item {{ ($showBusinessBasicsForm || $showGroomerBusinessProfileForm || $showSpacerBusinessProfileForm) && !$showLegalPolicyForm && !$showStartGroomingEarningComplete ? 'active' : '' }}">
                    <div class="step-content">
                        <div class="step-title"><span>2.</span>
                            <p>Build Your Profile</p>
                        </div>
                    </div>
                </div>
                <div class="step-item {{ $showLegalPolicyForm && !$showStartGroomingEarningComplete ? 'active' : '' }}">
                    <div class="step-content">
                        <div class="step-title"><span>3.</span>
                            <p>Legal & Policy Agreement</p>
                        </div>
                    </div>
                </div>
                <div class="step-item {{ $showStartGroomingEarningComplete ? 'active' : '' }}">
                    <div class="step-content">
                        <div class="step-title"><span>4.</span>
                            <p>Start Grooming & Earning!</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            @if ($showVerificationStatus)
                @include('livewire.auth.verify-qualify-verification-status')
            @elseif ($showStartGroomingEarningComplete)
                @include('livewire.auth.verify-qualify-start-grooming-complete')
            @elseif ($showBusinessBasicsForm)
                <div class="business-basics-wrap" wire:key="verify-qualify-business-basics">
                    <h1 class="business-basics-title">Business Basics</h1>

                    <form wire:submit="submitBusinessBasics" class="business-basics-form">
                        <div class="basics-card">
                            <div class="basics-field">
                                <label class="form-label" for="business-display-name">Business Name</label>
                                <div class="input-field-wrap">
                                    <textarea id="business-display-name" wire:model.live="business_display_name" class="form-input"
                                        placeholder="Enter your business display name (what customers see) if different from legal name (e.g. Companies House)."
                                        style="width: 100%; height: 70px; resize: none; overflow: hidden;"></textarea>
                                    <span class="input-valid-icon" aria-hidden="true"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                            viewBox="0 0 19 19" fill="none">
                                            <path
                                                d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                fill="#C9DDA0" />
                                        </svg></span>
                                </div>
                                @error('business_display_name')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="basics-field">
                                <label class="form-label" for="business-tagline">Tagline</label>
                                <input id="business-tagline" type="text" wire:model.live="business_tagline"
                                    class="form-input" placeholder="e.g., Luxury grooming with a gentle touch.">
                                @error('business_tagline')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="basics-field">
                                <label class="form-label" for="business-bio">Bio</label>
                                <textarea id="business-bio" wire:model.live="business_bio" class="form-input basics-textarea"
                                    style="resize: none; overflow: hidden; height: 90px; width: 100%;"
                                    placeholder="Tell customers a bit about yourself (and your space), it would be good to provide information for them to learn a bit more about your experience and training."></textarea>
                                @error('business_bio')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="basics-card">
                            <div class="basics-section-intro">
                                <h2 class="basics-card-heading">Profile Photo</h2>
                                <p class="basics-section-muted">Upload your profile photo or logo.</p>
                            </div>
                            <div class="profile-photo-upload-tracker" wire:key="profile-avatar-widget">
                                <div class="profile-photo-drop">
                                    <div class="profile-photo-placeholder-static" aria-hidden="true">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="85" height="85"
                                            viewBox="0 0 85 85" fill="none">
                                            <circle cx="42.5" cy="42.5" r="42" fill="#E3E3E3"
                                                stroke="#F6F6F6" />
                                            <path
                                                d="M16.4332 75.7445C18.0849 43.507 66.8864 43.507 68.5381 75.7446C68.5381 75.7446 59.0091 84.8113 42.5757 84.8113C26.1424 84.8113 16.4332 75.7445 16.4332 75.7445Z"
                                                fill="white" />
                                            <path
                                                d="M55.519 33.4267C55.519 36.8815 54.1466 40.1948 51.7037 42.6377C49.2608 45.0806 45.9475 46.453 42.4928 46.453C39.038 46.453 35.7247 45.0806 33.2818 42.6377C30.839 40.1948 29.4666 36.8815 29.4666 33.4267C29.4666 29.9719 30.839 26.6586 33.2818 24.2157C35.7247 21.7728 39.038 20.4004 42.4928 20.4004C45.9475 20.4004 49.2608 21.7728 51.7037 24.2157C54.1466 26.6586 55.519 29.9719 55.519 33.4267Z"
                                                fill="white" />
                                            <path
                                                d="M73.9928 20.1803C68.9347 20.1803 64.8201 16.0657 64.8201 11.0076C64.8201 5.94955 68.9347 1.83496 73.9928 1.83496C79.0509 1.83496 83.1655 5.94955 83.1655 11.0076C83.1655 16.0657 79.0509 20.1803 73.9928 20.1803Z"
                                                fill="#9D9B98" />
                                            <path
                                                d="M73.993 15.5936C73.6261 15.5936 73.3378 15.3053 73.3378 14.9384V7.07609C73.3378 6.70918 73.6261 6.4209 73.993 6.4209C74.3599 6.4209 74.6481 6.70918 74.6481 7.07609V14.9384C74.6481 15.3053 74.3599 15.5936 73.993 15.5936Z"
                                                fill="white" />
                                            <path
                                                d="M77.924 11.6619H70.0617C69.6948 11.6619 69.4066 11.3737 69.4066 11.0068C69.4066 10.6398 69.6948 10.3516 70.0617 10.3516H77.924C78.2909 10.3516 78.5792 10.6398 78.5792 11.0068C78.5792 11.3737 78.2909 11.6619 77.924 11.6619Z"
                                                fill="white" />
                                        </svg>
                                    </div>
                                    <div class="profile-photo-copy">
                                        <p class="profile-photo-label">Upload Image</p>
                                        <label class="profile-photo-btn">
                                            <input type="file" wire:model="business_avatar_upload"
                                                class="hidden-input">
                                            <span class="profile-photo-btn-inner">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 14 14" fill="none">
                                                    <path
                                                        d="M7 10.3163C6.72386 10.3163 6.5 10.0924 6.5 9.81626V1.66626L4.52903 3.63722C4.33115 3.8351 4.00998 3.83398 3.81349 3.63471C3.61896 3.43744 3.62005 3.12016 3.81593 2.92422L6.55492 0.184464C6.80072 -0.0614062 7.19931 -0.0614344 7.44514 0.184402L10.185 2.92425C10.3809 3.1202 10.3822 3.43753 10.1877 3.63499C9.99116 3.83461 9.66959 3.83585 9.47149 3.63775L7.5 1.66626V9.81626C7.5 10.0924 7.27614 10.3163 7 10.3163ZM1.616 13.7393C1.15533 13.7393 0.771 13.5853 0.463 13.2773C0.155 12.9693 0.000666667 12.5846 0 12.1233V10.2003C0 9.92412 0.223858 9.70026 0.5 9.70026C0.776142 9.70026 1 9.92412 1 10.2003V12.1233C1 12.2773 1.064 12.4186 1.192 12.5473C1.32 12.6759 1.461 12.7399 1.615 12.7393H12.385C12.5383 12.7393 12.6793 12.6753 12.808 12.5473C12.9367 12.4193 13.0007 12.2779 13 12.1233V10.2003C13 9.92412 13.2239 9.70026 13.5 9.70026C13.7761 9.70026 14 9.92412 14 10.2003V12.1233C14 12.5839 13.846 12.9683 13.538 13.2763C13.23 13.5843 12.8453 13.7386 12.384 13.7393H1.616Z"
                                                        fill="white" />
                                                </svg>
                                                Upload Photo
                                            </span>
                                        </label>
                                    </div>
                                    @error('business_avatar_upload')
                                        <span class="error-text">{{ $message }}</span>
                                    @enderror
                                </div>

                                @if ($business_avatar_upload || $business_avatar_path !== '')
                                    <div class="file-list profile-basics-avatar-attachments">
                                        @if ($business_avatar_upload)
                                            <div class="file-item-1" wire:key="profile-avatar-temp">
                                                <div class="file-info">
                                                    <img src="{{ $business_avatar_upload->temporaryUrl() }}"
                                                        class="file-thumbnail" alt="">
                                                    <div class="file-details">
                                                        <div class="file-name">
                                                            {{ $business_avatar_upload->getClientOriginalName() }}
                                                        </div>
                                                        <div class="file-progress-text" style="color:#10b981">
                                                            {{ $this->formatBytesForDisplay((int) $business_avatar_upload->getSize()) }}
                                                            • Uploaded
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="file-remove"
                                                    wire:click="removeBusinessAvatar" title="Remove"
                                                    aria-label="Remove">
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none">
                                                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @elseif ($business_avatar_path !== '')
                                            <div class="file-item file-item--saved" wire:key="profile-avatar-saved">
                                                <div class="file-info">
                                                    <img class="file-thumbnail"
                                                        src="{{ $this->publicDiskUrl($business_avatar_path) }}"
                                                        alt="" loading="lazy">
                                                    <div class="file-details">
                                                        <div class="file-name">{{ basename($business_avatar_path) }}
                                                        </div>
                                                        <div class="file-progress-text" style="color:#10b981">Saved
                                                        </div>
                                                    </div>
                                                </div>
                                                <button type="button" class="file-remove"
                                                    wire:click="removeBusinessAvatar" wire:loading.attr="disabled"
                                                    wire:target="removeBusinessAvatar" title="Remove"
                                                    aria-label="Remove">
                                                    <svg width="16" height="16" viewBox="0 0 16 16"
                                                        fill="none">
                                                        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor"
                                                            stroke-width="1.5" stroke-linecap="round" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                <div class="profile-avatar-upload-progress" hidden>
                                    <div class="profile-avatar-upload-progress-inner">
                                        <div class="profile-avatar-upload-progress-thumb-wrap" aria-hidden="true">
                                            <img class="profile-avatar-upload-progress-thumb" src=""
                                                alt="">
                                        </div>
                                        <div class="profile-avatar-upload-progress-text">
                                            <span class="profile-avatar-upload-progress-name"></span>
                                            <span class="profile-avatar-upload-progress-status"></span>
                                        </div>
                                        <button type="button" class="profile-avatar-upload-progress-cancel"
                                            wire:click="$cancelUpload('business_avatar_upload')"
                                            wire:loading.attr="disabled" wire:target="business_avatar_upload"
                                            title="Cancel upload" aria-label="Cancel upload">×</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="basics-card">
                            <div class="basics-section-intro">
                                <h2 class="basics-card-heading">Photo Gallery</h2>
                                <p class="basics-section-muted">Add more photos of your setup or services.</p>
                            </div>
                            <div class="gallery-slots">
                                @php
                                    $gallery_items = [];
                                    foreach ($business_gallery_paths as $pi => $p) {
                                        $gallery_items[] = ['kind' => 'path', 'path' => $p, 'pathIndex' => $pi];
                                    }
                                    foreach ($business_gallery_pending as $i => $f) {
                                        $gallery_items[] = ['kind' => 'pending', 'idx' => $i, 'file' => $f];
                                    }
                                    $gallery_items = array_slice($gallery_items, 0, 3);
                                    $gallery_used = count($gallery_items);
                                    $gallery_room = max(
                                        0,
                                        3 - count($business_gallery_paths) - count($business_gallery_pending),
                                    );
                                @endphp
                                @foreach (range(0, 2) as $slot)
                                    @php $item = $gallery_items[$slot] ?? null; @endphp
                                    <div class="gallery-slot">
                                        @if ($item && $item['kind'] === 'path')
                                            <img class="gallery-slot-img"
                                                src="{{ $this->publicDiskUrl($item['path']) }}" alt="">
                                            <button type="button" class="gallery-slot-remove"
                                                wire:click="removeBusinessGalleryPath({{ (int) $item['pathIndex'] }})"
                                                aria-label="Remove photo">&times;</button>
                                        @elseif (
                                            $item &&
                                                $item['kind'] === 'pending' &&
                                                $item['file'] instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile)
                                            <img class="gallery-slot-img" src="{{ $item['file']->temporaryUrl() }}"
                                                alt="">
                                            <button type="button" class="gallery-slot-remove"
                                                wire:click="removeBusinessGalleryPending({{ (int) $item['idx'] }})"
                                                aria-label="Remove photo">&times;</button>
                                        @else
                                            @if ($slot === $gallery_used && $gallery_room > 0)
                                                <label class="gallery-slot-empty">
                                                    <input type="file" wire:model="business_gallery_pick"
                                                        class="hidden-input">
                                                    <span class="gallery-paw" aria-hidden="true">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="160"
                                                            height="158" viewBox="0 0 160 158" fill="none">
                                                            <rect width="159.422" height="157.441" rx="10"
                                                                fill="white" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M69.9503 40.5986C67.9196 40.5986 66.328 41.8179 65.3434 43.2803C64.3466 44.7548 63.7969 46.6789 63.7969 48.7002C63.7969 50.7216 64.3466 52.6457 65.3434 54.1202C66.328 55.5785 67.9196 56.8018 69.9503 56.8018C71.9809 56.8018 73.5725 55.5825 74.5571 54.1202C75.5539 52.6457 76.1036 50.7216 76.1036 48.7002C76.1036 46.6789 75.5539 44.7548 74.5571 43.2803C73.5725 41.822 71.9809 40.5986 69.9503 40.5986ZM90.4615 40.5986C88.4309 40.5986 86.8392 41.8179 85.8546 43.2803C84.8578 44.7548 84.3081 46.6789 84.3081 48.7002C84.3081 50.7216 84.8578 52.6457 85.8546 54.1202C86.8392 55.5785 88.4309 56.8018 90.4615 56.8018C92.4921 56.8018 94.0837 55.5825 95.0683 54.1202C96.0651 52.6457 96.6148 50.7216 96.6148 48.7002C96.6148 46.6789 96.0651 44.7548 95.0683 43.2803C94.0837 41.822 92.4921 40.5986 90.4615 40.5986ZM57.6435 58.8272C55.6129 58.8272 54.0213 60.0465 53.0367 61.5089C52.0399 62.9834 51.4902 64.9075 51.4902 66.9288C51.4902 68.9502 52.0399 70.8743 53.0367 72.3488C54.0213 73.8071 55.6129 75.0305 57.6435 75.0305C59.6741 75.0305 61.2658 73.8112 62.2504 72.3488C63.2472 70.8743 63.7969 68.9502 63.7969 66.9288C63.7969 64.9075 63.2472 62.9834 62.2504 61.5089C61.2658 60.0506 59.6741 58.8272 57.6435 58.8272ZM80.2059 58.8272C75.2832 58.8272 71.6363 61.436 69.3062 64.6726C67.0048 67.8605 65.848 71.8182 65.848 75.0305C65.848 78.7734 68.1248 81.3781 70.9184 82.9376C73.6669 84.4769 77.1128 85.1575 80.2059 85.1575C83.299 85.1575 86.7448 84.481 89.4933 82.9376C92.2829 81.374 94.5637 78.7734 94.5637 75.0305C94.5637 71.8182 93.4069 67.8605 91.1055 64.6726C88.7795 61.4319 85.1327 58.8272 80.2059 58.8272ZM102.768 58.8272C100.738 58.8272 99.1459 60.0465 98.1614 61.5089C97.1645 62.9834 96.6148 64.9075 96.6148 66.9288C96.6148 68.9502 97.1645 70.8743 98.1614 72.3488C99.1459 73.8071 100.738 75.0305 102.768 75.0305C104.799 75.0305 106.39 73.8112 107.375 72.3488C108.372 70.8743 108.922 68.9502 108.922 66.9288C108.922 64.9075 108.372 62.9834 107.375 61.5089C106.39 60.0506 104.799 58.8272 102.768 58.8272Z"
                                                                fill="#E5E5E5" />
                                                            <path
                                                                d="M80.1727 134.566C75.1146 134.566 71 130.451 71 125.393C71 120.335 75.1146 116.221 80.1727 116.221C85.2307 116.221 89.3453 120.335 89.3453 125.393C89.3453 130.451 85.2307 134.566 80.1727 134.566Z"
                                                                fill="#9D9B98" />
                                                            <path
                                                                d="M80.1728 129.981C79.8059 129.981 79.5176 129.693 79.5176 129.326V121.464C79.5176 121.097 79.8059 120.809 80.1728 120.809C80.5397 120.809 80.828 121.097 80.828 121.464V129.326C80.828 129.693 80.5397 129.981 80.1728 129.981Z"
                                                                fill="white" />
                                                            <path
                                                                d="M84.1039 126.049H76.2416C75.8747 126.049 75.5864 125.76 75.5864 125.393C75.5864 125.027 75.8747 124.738 76.2416 124.738H84.1039C84.4708 124.738 84.7591 125.027 84.7591 125.393C84.7591 125.76 84.4708 126.049 84.1039 126.049Z"
                                                                fill="white" />
                                                        </svg>
                                                    </span>
                                                </label>
                                            @else
                                                <div class="gallery-slot-empty gallery-slot-placeholder"
                                                    aria-hidden="true">
                                                    <span class="gallery-paw">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="160"
                                                            height="158" viewBox="0 0 160 158" fill="none">
                                                            <rect width="159.422" height="157.441" rx="10"
                                                                fill="white" />
                                                            <path fill-rule="evenodd" clip-rule="evenodd"
                                                                d="M69.9503 40.5986C67.9196 40.5986 66.328 41.8179 65.3434 43.2803C64.3466 44.7548 63.7969 46.6789 63.7969 48.7002C63.7969 50.7216 64.3466 52.6457 65.3434 54.1202C66.328 55.5785 67.9196 56.8018 69.9503 56.8018C71.9809 56.8018 73.5725 55.5825 74.5571 54.1202C75.5539 52.6457 76.1036 50.7216 76.1036 48.7002C76.1036 46.6789 75.5539 44.7548 74.5571 43.2803C73.5725 41.822 71.9809 40.5986 69.9503 40.5986ZM90.4615 40.5986C88.4309 40.5986 86.8392 41.8179 85.8546 43.2803C84.8578 44.7548 84.3081 46.6789 84.3081 48.7002C84.3081 50.7216 84.8578 52.6457 85.8546 54.1202C86.8392 55.5785 88.4309 56.8018 90.4615 56.8018C92.4921 56.8018 94.0837 55.5825 95.0683 54.1202C96.0651 52.6457 96.6148 50.7216 96.6148 48.7002C96.6148 46.6789 96.0651 44.7548 95.0683 43.2803C94.0837 41.822 92.4921 40.5986 90.4615 40.5986ZM57.6435 58.8272C55.6129 58.8272 54.0213 60.0465 53.0367 61.5089C52.0399 62.9834 51.4902 64.9075 51.4902 66.9288C51.4902 68.9502 52.0399 70.8743 53.0367 72.3488C54.0213 73.8071 55.6129 75.0305 57.6435 75.0305C59.6741 75.0305 61.2658 73.8112 62.2504 72.3488C63.2472 70.8743 63.7969 68.9502 63.7969 66.9288C63.7969 64.9075 63.2472 62.9834 62.2504 61.5089C61.2658 60.0506 59.6741 58.8272 57.6435 58.8272ZM80.2059 58.8272C75.2832 58.8272 71.6363 61.436 69.3062 64.6726C67.0048 67.8605 65.848 71.8182 65.848 75.0305C65.848 78.7734 68.1248 81.3781 70.9184 82.9376C73.6669 84.4769 77.1128 85.1575 80.2059 85.1575C83.299 85.1575 86.7448 84.481 89.4933 82.9376C92.2829 81.374 94.5637 78.7734 94.5637 75.0305C94.5637 71.8182 93.4069 67.8605 91.1055 64.6726C88.7795 61.4319 85.1327 58.8272 80.2059 58.8272ZM102.768 58.8272C100.738 58.8272 99.1459 60.0465 98.1614 61.5089C97.1645 62.9834 96.6148 64.9075 96.6148 66.9288C96.6148 68.9502 97.1645 70.8743 98.1614 72.3488C99.1459 73.8071 100.738 75.0305 102.768 75.0305C104.799 75.0305 106.39 73.8112 107.375 72.3488C108.372 70.8743 108.922 68.9502 108.922 66.9288C108.922 64.9075 108.372 62.9834 107.375 61.5089C106.39 60.0506 104.799 58.8272 102.768 58.8272Z"
                                                                fill="#E5E5E5" />
                                                            <path
                                                                d="M80.1727 134.566C75.1146 134.566 71 130.451 71 125.393C71 120.335 75.1146 116.221 80.1727 116.221C85.2307 116.221 89.3453 120.335 89.3453 125.393C89.3453 130.451 85.2307 134.566 80.1727 134.566Z"
                                                                fill="#9D9B98" />
                                                            <path
                                                                d="M80.1728 129.981C79.8059 129.981 79.5176 129.693 79.5176 129.326V121.464C79.5176 121.097 79.8059 120.809 80.1728 120.809C80.5397 120.809 80.828 121.097 80.828 121.464V129.326C80.828 129.693 80.5397 129.981 80.1728 129.981Z"
                                                                fill="white" />
                                                            <path
                                                                d="M84.1039 126.049H76.2416C75.8747 126.049 75.5864 125.76 75.5864 125.393C75.5864 125.027 75.8747 124.738 76.2416 124.738H84.1039C84.4708 124.738 84.7591 125.027 84.7591 125.393C84.7591 125.76 84.4708 126.049 84.1039 126.049Z"
                                                                fill="white" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @error('business_gallery_pick')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="basics-actions">
                            <button type="submit"
                                class="submit-btn {{ $this->isBusinessBasicsContinueEnabled() ? 'btn-active' : 'btn-disabled' }}"
                                wire:loading.attr="disabled" wire:target="submitBusinessBasics"
                                @if (!$this->isBusinessBasicsContinueEnabled()) disabled @endif>
                                <span wire:loading.remove wire:target="submitBusinessBasics">Continue</span>
                                <span wire:loading wire:target="submitBusinessBasics">Saving…</span>
                            </button>
                        </div>
                    </form>
                </div>
            @elseif ($showLegalPolicyForm)
                @include('livewire.auth.verify-qualify-legal-policy')
            @elseif (
                $fursgo_usage === 'space' &&
                    !$showBusinessBasicsForm &&
                    ($showSpacerBusinessProfileForm || $showGroomerBusinessProfileForm))
                @include('livewire.auth.verify-qualify-spacer-business-profile')
            @elseif ($fursgo_usage === 'groomer' && !$showBusinessBasicsForm && $showGroomerBusinessProfileForm)
                @include('livewire.auth.verify-qualify-groomer-business-profile')
            @elseif ($showSpacerBusinessProfileForm)
                @include('livewire.auth.verify-qualify-spacer-business-profile')
            @elseif ($showGroomerBusinessProfileForm)
                @include('livewire.auth.verify-qualify-groomer-business-profile')
            @elseif ($showVerificationCard)
                <div class="verification-card">
                    <div class="verification-header">
                        <div class="icon-wrapper">
                            <svg xmlns="http://www.w3.org/2000/svg" width="138" height="135"
                                viewBox="0 0 138 135" fill="none">
                                <g filter="url(#filter0_d_14_635)">
                                    <path
                                        d="M72.0689 18.8712C72.2854 18.7938 72.522 18.7934 72.7388 18.87L109.21 31.766C109.609 31.9072 109.876 32.285 109.876 32.7088V70.8439C109.876 91.5939 77.1721 108.667 73.0304 110.746C72.7583 110.883 72.4436 110.883 72.1715 110.746C68.0298 108.667 35.3257 91.5939 35.3257 70.8439V32.7063C35.3257 32.2837 35.5913 31.9068 35.9892 31.7646L72.0689 18.8712Z"
                                        fill="#FFF8EE" />
                                    <path
                                        d="M72.4527 11.1946L115.737 26.5003C115.837 26.5357 115.904 26.6298 115.904 26.7357V71.9329C115.904 77.902 113.536 83.6508 109.848 88.9808C106.161 94.3099 101.184 99.1785 96.0406 103.37C85.7542 111.755 74.9016 117.355 72.6812 118.465C72.62 118.496 72.5822 118.496 72.5211 118.465C70.3012 117.355 59.4484 111.755 49.1617 103.37C44.0186 99.1785 39.0411 94.3099 35.3541 88.9808C31.6666 83.6508 29.2984 77.902 29.2984 71.9329V26.7337C29.2984 26.6281 29.365 26.5339 29.4644 26.4984L72.2857 11.1956C72.3397 11.1764 72.3986 11.1756 72.4527 11.1946Z"
                                        stroke="#3B3731" stroke-width="1.5" />
                                    <path
                                        d="M57.5518 67.7464L69.0816 79.2762L91.3519 57.006C92.3984 55.9596 92.4014 54.2639 91.3586 53.2138C90.3107 52.1584 88.6046 52.1554 87.553 53.207L69.0816 71.6783L61.3373 63.9556C60.2908 62.912 58.5969 62.9132 57.5518 63.9583C56.5057 65.0043 56.5057 66.7004 57.5518 67.7464Z"
                                        fill="#FFC97A" />
                                    <rect x="16.9937" y="12.9932" width="20.9817" height="23.313" rx="3"
                                        fill="white" />
                                    <path
                                        d="M28.461 6.21127C28.155 6.07285 27.8272 6 27.4848 6C27.1424 6 26.8146 6.07285 26.5086 6.21127L12.7903 12.0322C11.1875 12.7098 9.99275 14.2907 10 16.1995C10.0365 23.4265 13.0089 36.6494 25.5615 42.6598C26.7781 43.2426 28.1915 43.2426 29.4081 42.6598C41.9607 36.6494 44.9331 23.4265 44.9696 16.1995C44.9769 14.2907 43.7821 12.7098 42.1793 12.0322L28.461 6.21127ZM20.5565 26.8506C20.9062 26.938 21.2777 26.9817 21.6565 26.9817C24.2283 26.9817 26.3191 24.8908 26.3191 22.3191V17.6565H29.5393C30.4208 17.6565 31.2295 18.1519 31.6229 18.946L32.1474 19.9878H36.81C37.4511 19.9878 37.9757 20.5124 37.9757 21.1535V23.4848C37.9757 26.7049 35.3675 29.313 32.1474 29.313H28.6504V33.0067C28.6504 33.5385 28.2206 33.9756 27.6815 33.9756C27.5504 33.9756 27.4192 33.9465 27.3027 33.8955L20.1121 30.8138C19.6312 30.6098 19.3252 30.1363 19.3252 29.619C19.3252 29.415 19.369 29.2183 19.4637 29.0362L20.5565 26.8506ZM20.4909 17.6565H23.9878V22.3191C23.9878 23.6086 22.946 24.6504 21.6565 24.6504C20.367 24.6504 19.3252 23.6086 19.3252 22.3191V18.8222C19.3252 18.1811 19.8498 17.6565 20.4909 17.6565ZM29.8161 21.1535C29.8161 20.8443 29.6933 20.5478 29.4747 20.3292C29.2561 20.1106 28.9596 19.9878 28.6504 19.9878C28.3413 19.9878 28.0448 20.1106 27.8262 20.3292C27.6076 20.5478 27.4848 20.8443 27.4848 21.1535C27.4848 21.4626 27.6076 21.7591 27.8262 21.9777C28.0448 22.1963 28.3413 22.3191 28.6504 22.3191C28.9596 22.3191 29.2561 22.1963 29.4747 21.9777C29.6933 21.7591 29.8161 21.4626 29.8161 21.1535Z"
                                        fill="#C9DDA0" />
                                    <path
                                        d="M111.002 84.1144C110.696 83.976 110.368 83.9031 110.025 83.9031C109.683 83.9031 109.355 83.976 109.049 84.1144L95.3308 89.9354C93.7281 90.6129 92.5333 92.1938 92.5406 94.1026C92.577 101.33 95.5494 114.553 108.102 120.563C109.319 121.146 110.732 121.146 111.949 120.563C124.501 114.553 127.474 101.33 127.51 94.1026C127.517 92.1938 126.323 90.6129 124.72 89.9354L111.002 84.1144Z"
                                        fill="#CBDCE8" />
                                    <path
                                        d="M119.52 94.0211L112.026 101.984L119.52 94.0211ZM108.696 101.595C106.373 102.487 104.515 102.334 102.658 101.598C103.126 107.634 105.94 109.954 109.692 110.883C109.692 110.883 112.518 108.884 112.926 104.145C112.97 103.632 112.992 103.376 112.886 103.086C112.779 102.797 112.569 102.59 112.15 102.175C111.461 101.493 111.117 101.152 110.708 101.066C110.298 100.981 109.764 101.186 108.696 101.595Z"
                                        fill="#CBDCE8" />
                                    <path
                                        d="M119.52 94.0211L112.026 101.984M108.696 101.595C106.373 102.487 104.515 102.334 102.658 101.598C103.126 107.634 105.94 109.954 109.692 110.883C109.692 110.883 112.518 108.884 112.926 104.145C112.97 103.632 112.992 103.376 112.886 103.086C112.779 102.797 112.569 102.59 112.15 102.175C111.461 101.493 111.117 101.152 110.708 101.066C110.298 100.981 109.764 101.186 108.696 101.595Z"
                                        stroke="white" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M104.063 106.618C104.063 106.618 106.405 107.071 108.747 105.263L104.063 106.618Z"
                                        fill="#CBDCE8" />
                                    <path d="M104.063 106.618C104.063 106.618 106.405 107.071 108.747 105.263"
                                        stroke="white" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <path
                                        d="M107.81 98.0027C107.81 98.3133 107.687 98.6111 107.467 98.8307C107.248 99.0503 106.95 99.1737 106.639 99.1737C106.329 99.1737 106.031 99.0503 105.811 98.8307C105.592 98.6111 105.468 98.3133 105.468 98.0027C105.468 97.6921 105.592 97.3943 105.811 97.1747C106.031 96.9551 106.329 96.8317 106.639 96.8317C106.95 96.8317 107.248 96.9551 107.467 97.1747C107.687 97.3943 107.81 97.6921 107.81 98.0027Z"
                                        fill="#CBDCE8" stroke="white" />
                                    <path d="M110.152 94.9579V95.0512V94.9579Z" fill="#CBDCE8" />
                                    <path d="M110.152 94.9579V95.0512" stroke="white" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </g>
                                <defs>
                                    <filter id="filter0_d_14_635" x="0" y="0" width="137.51" height="135"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="4" />
                                        <feGaussianBlur stdDeviation="5" />
                                        <feComposite in2="hardAlpha" operator="out" />
                                        <feColorMatrix type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.28 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_14_635" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_14_635"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                        <h2>Background Checks</h2>
                        <div class="verification-subtitle">
                            <p>Please complete your background checks</p>
                            <span>Complete verification to start receiving payouts.</span>
                        </div>

                    </div>

                    <div class="submit-section">
                        <button type="button" class="submit-btn" wire:click="verifyBusiness">
                            Verify Business
                        </button>
                    </div>
                </div>
            @endif

            @if ($showAccountPayoutsForm)
                <div class="verification-card">
                    <div class="step-heading">
                        <h2>Verify Your Account for Payouts</h2>
                    </div>

                    <form wire:submit="submit" class="verification-form">
                        <div>
                            <div class="form-section">
                                <div class="section-title">
                                    <h3>Choose how you use FursGo</h3>
                                    <p>This helps us set up the right tools and dashboard for your business.</p>
                                </div>
                                <div class="radio-group">
                                    <div class="radio-item {{ $fursgo_usage == 'groomer' ? 'checked' : '' }}">
                                        <input type="radio" wire:model.live="fursgo_usage" value="groomer"
                                            id="groomer" name="fursgo_usage">
                                        <label for="groomer" class="radio-label">
                                            <div class="radio-content">
                                                <p class="radio-title">Pet Groomer</p>
                                                <p class="radio-description">I provide grooming services for pets and
                                                    accept
                                                    bookings from pet owners.</p>
                                            </div>
                                            <span class="radio-custom"></span>
                                        </label>
                                    </div>
                                    <div class="radio-item {{ $fursgo_usage == 'space' ? 'checked' : '' }}">
                                        <input type="radio" wire:model.live="fursgo_usage" value="space"
                                            id="space" name="fursgo_usage">
                                        <label for="space" class="radio-label">
                                            <div class="radio-content">
                                                <p class="radio-title">Space Host</p>
                                                <p class="radio-description">I rent out a grooming space for
                                                    professional
                                                    groomers to use.</p>
                                            </div>
                                            <span class="radio-custom"></span>
                                        </label>
                                    </div>
                                </div>
                                @error('fursgo_usage')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Select Account Type -->
                            <div class="form-section">
                                <h3 class="section-title">Select Account Type</h3>
                                <div class="radio-group">
                                    <div
                                        class="radio-item {{ $account_type == 'registered_business' ? 'checked' : '' }}">
                                        <input type="radio" wire:model.live="account_type"
                                            value="registered_business" id="registered_business" name="account_type">
                                        <label for="registered_business" class="radio-label">
                                            <div class="radio-content">
                                                <p class="radio-title">Registered Business</p>
                                                <p class="radio-description">I operate as a registered business.</p>
                                            </div>
                                            <span class="radio-custom"></span>
                                        </label>
                                    </div>
                                    <div class="radio-item {{ $account_type == 'freelance' ? 'checked' : '' }}">
                                        <input type="radio" wire:model.live="account_type" value="freelance"
                                            id="freelance" name="account_type">
                                        <label for="freelance" class="radio-label">
                                            <div class="radio-content">
                                                <p class="radio-title">Freelance</p>
                                                <p class="radio-description">I operate independently.</p>
                                            </div>
                                            <span class="radio-custom"></span>
                                        </label>
                                    </div>
                                </div>
                                @error('account_type')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Select Location Type -->
                            <div class="form-section">
                                <h3 class="section-title">Select location type</h3>
                                <div class="checkbox-group">
                                    <div
                                        class="checkbox-item {{ in_array('space_visits', $location_types) ? 'checked' : '' }}">
                                        <input type="checkbox" wire:model.live="location_types" value="space_visits"
                                            id="space_visits" name="location_types">
                                        <label for="space_visits" class="checkbox-label">
                                            <div class="checkbox-content">
                                                <p class="checkbox-title">Space Visits</p>
                                            </div>
                                            <span class="checkbox-custom"></span>
                                        </label>
                                    </div>
                                    <div
                                        class="checkbox-item {{ in_array('commercial_salon', $location_types) ? 'checked' : '' }}">
                                        <input type="checkbox" wire:model.live="location_types"
                                            value="commercial_salon" id="commercial_salon" name="location_types">
                                        <label for="commercial_salon" class="checkbox-label">
                                            <div class="checkbox-content">
                                                <p class="checkbox-title">Commercial Salon</p>
                                            </div>
                                            <span class="checkbox-custom"></span>
                                        </label>
                                    </div>
                                    <div
                                        class="checkbox-item {{ in_array('home_studio', $location_types) ? 'checked' : '' }}">
                                        <input type="checkbox" wire:model.live="location_types" value="home_studio"
                                            id="home_studio" name="location_types">
                                        <label for="home_studio" class="checkbox-label">
                                            <div class="checkbox-content">
                                                <p class="checkbox-title">Home Studio</p>
                                            </div>
                                            <span class="checkbox-custom"></span>
                                        </label>
                                    </div>
                                    <div
                                        class="checkbox-item {{ in_array('house_visit', $location_types) ? 'checked' : '' }}">
                                        <input type="checkbox" wire:model.live="location_types" value="house_visit"
                                            id="house_visit" name="location_types">
                                        <label for="house_visit" class="checkbox-label">
                                            <div class="checkbox-content">
                                                <p class="checkbox-title">House visit</p>
                                            </div>
                                            <span class="checkbox-custom"></span>
                                        </label>
                                    </div>
                                    <div
                                        class="checkbox-item {{ in_array('mobile_van', $location_types) ? 'checked' : '' }}">
                                        <input type="checkbox" wire:model.live="location_types" value="mobile_van"
                                            id="mobile_van" name="location_types">
                                        <label for="mobile_van" class="checkbox-label">
                                            <div class="checkbox-content">
                                                <p class="checkbox-title">Mobile Van</p>
                                            </div>
                                            <span class="checkbox-custom"></span>
                                        </label>
                                    </div>
                                </div>
                                @error('location_types')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="submit-section">
                            <button type="submit"
                                class="submit-btn {{ $fursgo_usage && $account_type && count($location_types) > 0 ? 'btn-active' : 'btn-disabled' }}"
                                wire:loading.attr="disabled" wire:target="submit"
                                @if ($fursgo_usage && $account_type && count($location_types) > 0) @else disabled @endif>
                                <span wire:loading.remove wire:target="submit">Continue</span>
                                <span wire:loading wire:target="submit">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @if ($showRegisteredBusiness)
                <div class="verification-card" wire:key="verify-qualify-registered">
                    <div class="step-heading">
                        <h2>Registered Business</h2>
                    </div>

                    <form wire:submit="submitPersonalInfo" novalidate>
                        <div class="form-grid">
                            <!-- Full Name -->
                            <div class="form-group full-width">
                                <div>
                                    <label class="form-label">Full Name <span>(must match ID)</span>
                                    </label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="text" wire:model.live="full_name" class="form-input"
                                                placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                                @error('full_name')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- business_details col data in json -->
                            <div class="form-group full-width">
                                <h3>Business Details</h3>
                                <div>
                                    <label class="form-label">Business Name</label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="text" wire:model.live="business_name" class="form-input"
                                                placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Business Registration Number</label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="text" wire:model.live="business_registration_number"
                                                class="form-input" placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Phone Number</label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="tel" wire:model.live="business_phone" class="form-input"
                                                placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Email Address</label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="email" wire:model.live="business_email" class="form-input"
                                                placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Business Owner ID</label>
                                    <p>Please upload a clear photo or scan of a valid government-issued ID (e.g.
                                        passport or
                                        driving licence) and a recent UK utility bill, bank statement, or official
                                        letter
                                        showing your current address. Both documents must be in English and dated within
                                        the
                                        last 3 months.</p>

                                    @php
                                        $__boSavedFileEntries = [];
                                        foreach ($business_owner_id_images ?? [] as $__p) {
                                            if (is_string($__p) && $__p !== '') {
                                                $__boSavedFileEntries[] = [
                                                    'path' => $__p,
                                                    'url' => route('groomer-spacer.business-owner-id-file', [
                                                        't' => \Illuminate\Support\Facades\Crypt::encryptString($__p),
                                                    ]),
                                                ];
                                            }
                                        }
                                    @endphp
                                    <input type="hidden" id="business-owner-saved-urls-json"
                                        value="{{ htmlspecialchars(json_encode($__boSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                                        wire:key="business-owner-saved-urls-json">
                                    <script>
                                        window.__boSavedFileEntries = @json($__boSavedFileEntries);
                                    </script>

                                    <!-- Custom File Upload Interface: wire:ignore entire widget so Livewire morphs do not reset Attach/Upload tab state (was causing flicker on every wire:model.live keystroke). Hidden saved-url inputs stay above this block. -->
                                    <div class="custom-file-upload" wire:ignore>
                                        <!-- Tabs -->
                                        <div class="upload-tabs">
                                            <div>
                                                <button type="button" class="tab-btn active" data-tab="attach"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="11"
                                                        height="12" viewBox="0 0 11 12" fill="none">
                                                        <path
                                                            d="M10.5 6.04469L6.17551 10.5107C5.54818 11.1481 4.70239 11.5037 3.82235 11.5C2.94232 11.4963 2.09936 11.1336 1.47707 10.4909C0.854792 9.8483 0.503611 8.97775 0.500028 8.06891C0.496444 7.16008 0.840748 6.2866 1.45794 5.63874L5.78243 1.17272C5.98895 0.95944 6.23412 0.790259 6.50395 0.674834C6.77378 0.559409 7.06298 0.5 7.35504 0.5C7.64711 0.5 7.93631 0.559409 8.20614 0.674834C8.47597 0.790259 8.72114 0.95944 8.92766 1.17272C9.13418 1.386 9.298 1.63919 9.40977 1.91785C9.52153 2.19652 9.57906 2.49518 9.57906 2.7968C9.57906 3.09842 9.52153 3.39709 9.40977 3.67575C9.298 3.95441 9.13418 4.20761 8.92766 4.42089L4.60317 8.88691C4.3946 9.10231 4.1117 9.22333 3.81673 9.22333C3.52175 9.22333 3.23886 9.10231 3.03028 8.88691C2.8217 8.6715 2.70452 8.37935 2.70452 8.07472C2.70452 7.77009 2.8217 7.47794 3.03028 7.26254L6.96168 3.20304"
                                                            stroke="#3B3731" stroke-linecap="round" />
                                                    </svg>Attach</button>
                                                <button type="button" class="tab-btn" data-tab="upload"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="12"
                                                        height="12" viewBox="0 0 12 12" fill="none">
                                                        <path
                                                            d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z"
                                                            stroke="#3B3731" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M4.16662 5.38878C4.84163 5.38878 5.38884 4.84157 5.38884 4.16656C5.38884 3.49154 4.84163 2.94434 4.16662 2.94434C3.4916 2.94434 2.9444 3.49154 2.9444 4.16656C2.9444 4.84157 3.4916 5.38878 4.16662 5.38878Z"
                                                            stroke="#3B3731" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M11.5001 7.83358L9.61421 5.94769C9.38501 5.71856 9.07419 5.58984 8.7501 5.58984C8.42601 5.58984 8.11519 5.71856 7.88599 5.94769L2.33344 11.5002"
                                                            stroke="#3B3731" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>Upload</button>

                                            </div>
                                        </div>

                                        <!-- Tab Content -->
                                        <div class="tab-content">
                                            <!-- Attach Tab -->
                                            <div class="tab-pane active" id="attach-tab">
                                                <div class="file-list" id="business-owner-id-file-list" wire:ignore>
                                                    <!-- Files will be dynamically added here -->
                                                </div>
                                                <p class="file-list-empty-msg" data-role="file-list-empty">No file
                                                    attached.
                                                </p>
                                            </div>

                                            <!-- Upload Tab -->
                                            <div class="tab-pane" id="upload-tab">
                                                <div class="upload-area" id="business-owner-id-upload-area">
                                                    <div>
                                                        <p>Choose a file or drag & drop it here.</p>
                                                    </div>
                                                    <div class="upload-icon">
                                                        Browse File
                                                    </div>
                                                    <input type="file" wire:model="business_owner_id_images"
                                                        id="business-owner-id-file-input" class="hidden-input"
                                                        accept=".pdf,.jpg,.jpeg,.png" multiple>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- payout_details col data in json -->
                            <div class="form-group full-width">
                                <h3>Payout Details</h3>
                                <div>
                                    <label class="form-label">Account Holder Name</label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="text" wire:model.live="account_holder_name"
                                                class="form-input" placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Account Number</label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="text" wire:model.live="account_number" class="form-input"
                                                placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">Sort Code</label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="text" wire:model.live="sort_code" class="form-input"
                                                placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label class="form-label">IBAN</label>
                                    <div class="input-container">
                                        <div class="input-field-wrap">
                                            <input type="text" wire:model.live="iban" class="form-input"
                                                placeholder=" " required>
                                            <span class="input-valid-icon" aria-hidden="true"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg></span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- insurance_details col data in json -->
                            <div class="form-group full-width">
                                <h3>Insurance Details</h3>
                                <div>
                                    <label class="form-label">Insurance Certificate <span>(Optional)</span></label>
                                    @php
                                        $__insSavedFileEntries = [];
                                        foreach ($insurance_certificate_paths ?? [] as $__p) {
                                            if (is_string($__p) && $__p !== '') {
                                                $__insSavedFileEntries[] = [
                                                    'path' => $__p,
                                                    'url' => route('groomer-spacer.insurance-certificate-file', [
                                                        't' => \Illuminate\Support\Facades\Crypt::encryptString($__p),
                                                    ]),
                                                ];
                                            }
                                        }
                                    @endphp
                                    <input type="hidden" id="insurance-saved-urls-json"
                                        value="{{ htmlspecialchars(json_encode($__insSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                                        wire:key="insurance-saved-urls-json">
                                    <script>
                                        window.__insSavedFileEntries = @json($__insSavedFileEntries);
                                    </script>
                                    <!-- Custom File Upload Interface (wire:ignore — same tab-flicker fix as Business Owner ID) -->
                                    <div class="custom-file-upload" wire:ignore>
                                        <!-- Tabs -->
                                        <div class="upload-tabs">
                                            <div>
                                                <button type="button" class="tab-btn"
                                                    data-tab="insurance-attach"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="11"
                                                        height="12" viewBox="0 0 11 12" fill="none">
                                                        <path
                                                            d="M10.5 6.04469L6.17551 10.5107C5.54818 11.1481 4.70239 11.5037 3.82235 11.5C2.94232 11.4963 2.09936 11.1336 1.47707 10.4909C0.854792 9.8483 0.503611 8.97775 0.500028 8.06891C0.496444 7.16008 0.840748 6.2866 1.45794 5.63874L5.78243 1.17272C5.98895 0.95944 6.23412 0.790259 6.50395 0.674834C6.77378 0.559409 7.06298 0.5 7.35504 0.5C7.64711 0.5 7.93631 0.559409 8.20614 0.674834C8.47597 0.790259 8.72114 0.95944 8.92766 1.17272C9.13418 1.386 9.298 1.63919 9.40977 1.91785C9.52153 2.19652 9.57906 2.49518 9.57906 2.7968C9.57906 3.09842 9.52153 3.39709 9.40977 3.67575C9.298 3.95441 9.13418 4.20761 8.92766 4.42089L4.60317 8.88691C4.3946 9.10231 4.1117 9.22333 3.81673 9.22333C3.52175 9.22333 3.23886 9.10231 3.03028 8.88691C2.8217 8.6715 2.70452 8.37935 2.70452 8.07472C2.70452 7.77009 2.8217 7.47794 3.03028 7.26254L6.96168 3.20304"
                                                            stroke="#3B3731" stroke-linecap="round" />
                                                    </svg>Attach</button>
                                                <button type="button" class="tab-btn active"
                                                    data-tab="insurance-upload"><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="12"
                                                        height="12" viewBox="0 0 12 12" fill="none">
                                                        <path
                                                            d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z"
                                                            stroke="#3B3731" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M4.16662 5.38878C4.84163 5.38878 5.38884 4.84157 5.38884 4.16656C5.38884 3.49154 4.84163 2.94434 4.16662 2.94434C3.4916 2.94434 2.9444 3.49154 2.9444 4.16656C2.9444 4.84157 3.4916 5.38878 4.16662 5.38878Z"
                                                            stroke="#3B3731" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M11.5001 7.83358L9.61421 5.94769C9.38501 5.71856 9.07419 5.58984 8.7501 5.58984C8.42601 5.58984 8.11519 5.71856 7.88599 5.94769L2.33344 11.5002"
                                                            stroke="#3B3731" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>Upload</button>

                                            </div>
                                        </div>

                                        <!-- Tab Content -->
                                        <div class="tab-content">
                                            <!-- Attach Tab -->
                                            <div class="tab-pane" id="insurance-attach-tab">
                                                <div class="file-list" id="insurance-file-list" wire:ignore>
                                                    <!-- Files will be dynamically added here -->
                                                </div>
                                                <p class="file-list-empty-msg" data-role="file-list-empty">No file
                                                    attached.
                                                </p>
                                            </div>

                                            <!-- Upload Tab -->
                                            <div class="tab-pane active" id="insurance-upload-tab">
                                                <div class="upload-area" id="insurance-upload-area">
                                                    <div>
                                                        <p>Choose a file or drag & drop it here.</p>
                                                        <span>JPEG, PNG, and PDF formats, up to 50 MB.</span>
                                                    </div>
                                                    <div class="upload-icon">
                                                        Browse File
                                                    </div>
                                                    <input type="file" wire:model="insurance_certificate_upload"
                                                        id="insurance-file-input" class="hidden-input"
                                                        accept=".pdf,.jpg,.jpeg,.png" multiple>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    @error('insurance_certificate_upload')
                                        <span class="error-text">{{ $message }}</span>
                                    @enderror
                                    @if ($errors->has('insurance_certificate_upload.*'))
                                        <span
                                            class="error-text">{{ $errors->first('insurance_certificate_upload.*') }}</span>
                                    @endif
                                </div>

                            </div>
                        </div>

                        @include('livewire.auth.verify-qualify-accuracy-confirm')

                        <!-- Buttons -->
                        <div class="form-buttons">
                            <button type="button" class="back-btn" wire:click="goBack">
                                <span>Back</span>
                            </button>
                            <button type="submit"
                                class="submit-btn {{ $this->isPersonalInfoFormValid() ? 'btn-active' : 'btn-disabled' }}"
                                wire:loading.attr="disabled" wire:target="submitPersonalInfo">
                                <span wire:loading.remove wire:target="submitPersonalInfo">Submit</span>
                                <span wire:loading wire:target="submitPersonalInfo">Processing...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @endif

            @include('livewire.auth.verify-qualify-freelance-step')

        </div>
    </div>
</section>

@script
    <script>
        (function() {
            if (window.__verifyQualifyDebugInstalled) {
                return;
            }
            window.__verifyQualifyDebugInstalled = true;

            let t = null;
            const debounceMs = 250;

            function wireRootFromNode(node) {
                if (!node || !node.closest) {
                    return null;
                }
                return node.closest('[wire\\:id]');
            }

            function resolveComponent(component, el) {
                if (component && typeof component.call === 'function') {
                    return component;
                }
                const root = wireRootFromNode(el);
                const id = root && root.getAttribute('wire:id');
                if (!id || typeof Livewire === 'undefined' || typeof Livewire.find !== 'function') {
                    return null;
                }
                return Livewire.find(id);
            }

            function logVerifyQualifyState(component, el) {
                const cmp = resolveComponent(component, el);
                if (!cmp || typeof cmp.call !== 'function') {
                    return;
                }
                clearTimeout(t);
                t = setTimeout(() => {
                    cmp.call('getSubmitButtonDebug').then((data) => {
                        const p = data.personal_step;
                        const c = data.continue_step;

                        if (data.step.showBusinessBasicsForm) {
                            console.log(
                                '%c[verify-qualify]%c Business basics (Build Your Profile)',
                                'color:#ca8a04;font-weight:bold',
                                'color:inherit',
                                data.business_basics?.continue_enabled ? 'Continue ENABLED' :
                                'Continue DISABLED — Business name required',
                                data
                            );
                            return;
                        }

                        if (data.step.showVerificationStatus) {
                            console.log(
                                '%c[verify-qualify]%c Verification approved screen',
                                'color:#16a34a;font-weight:bold',
                                'color:inherit',
                                data
                            );
                            return;
                        }

                        if (data.step.showAccountPayoutsForm) {
                            const continueReasons = [];
                            if (!c.continue_would_enable) {
                                if (!data.continue_step.fursgo_usage) {
                                    continueReasons.push(
                                        'Choose how you use FursGo (groomer vs space)');
                                }
                                if (!data.continue_step.account_type) {
                                    continueReasons.push('Select account type');
                                }
                                if (data.continue_step.location_types_count < 1) {
                                    continueReasons.push('Select at least one location type');
                                }
                            }
                            console.log(
                                '%c[verify-qualify]%c Continue',
                                'color:#2563eb;font-weight:bold',
                                'color:inherit',
                                c.continue_would_enable ? 'ENABLED' : 'DISABLED — ' +
                                continueReasons.join('; '),
                                data
                            );
                        }

                        if (data.step.showRegisteredBusiness || data.step.showFreelance) {
                            const submitReasons = [];
                            if (!p.submit_would_enable) {
                                const skipKeys = new Set([
                                    'email_format_ok',
                                    'business_owner_id_images_count',
                                    'id_documents_count',
                                    'has_id_proof',
                                    'submit_would_enable',
                                ]);
                                Object.entries(p).forEach(([key, ok]) => {
                                    if (skipKeys.has(key) || key.endsWith('_count')) {
                                        return;
                                    }
                                    if (ok === false) {
                                        submitReasons.push('missing or empty: ' + key);
                                    }
                                });
                                if (!p.email_format_ok && p.business_email) {
                                    submitReasons.push('business_email fails email validation');
                                }
                                if (!p.has_id_proof) {
                                    submitReasons.push(
                                        'no ID files on server (upload Business Owner ID; drag-drop must fire change on the Livewire input)'
                                    );
                                }
                            }
                            console.log(
                                '%c[verify-qualify]%c Submit',
                                'color:#059669;font-weight:bold',
                                'color:inherit',
                                p.submit_would_enable ? 'ENABLED' : 'DISABLED — ' + submitReasons
                                .join('; '),
                                data
                            );
                        }
                    }).catch((e) => console.warn('[verify-qualify] getSubmitButtonDebug failed', e));
                }, debounceMs);
            }

            document.addEventListener('livewire:init', () => {
                Livewire.hook('morph.updated', ({
                    el,
                    component
                }) => {
                    const root = wireRootFromNode(el);
                    if (!root || !root.querySelector('.verification-wrapper')) {
                        return;
                    }
                    logVerifyQualifyState(component, el);
                });

                queueMicrotask(() => {
                    const wrap = document.querySelector('.verification-wrapper');
                    if (wrap) {
                        logVerifyQualifyState(null, wrap);
                    }
                });
            });
        })();
    </script>
@endscript

<style>
    .verification-wrapper {
        display: flex;
        gap: 10rem;
        position: relative;
    }

    /* Floating Sidebar */
    .floating-sidebar {
        max-width: 18rem;
        position: sticky;
        top: 2rem;
        height: fit-content;
    }

    .sidebar-header h1 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 50px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    .steps-list {
        margin-top: 3rem;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .step-item {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        padding-left: 1.5rem;
        border-radius: 12px;
        cursor: default;
    }

    .step-item.active {
        border-radius: 96px;
        background: #FFC97A;
    }

    .step-content {
        flex: 1;
    }

    .step-title {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .step-item:not(.active) .step-title {
        color: #3B3731;
    }

    .step-item.active .step-title {
        color: #FFF;
    }

    /* Main Content */
    .main-content {
        flex: 1;
        min-width: 0;
    }

    .verification-card {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        max-width: 35rem;
        margin: 0 auto;
    }

    .verification-header {
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-bottom: 3rem;
    }

    .step-heading {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 3rem;
    }

    .icon-wrapper {
        width: 117.51px;
        height: 115px;
        aspect-ratio: 47/46;
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .verification-header h2 {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 36px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
    }

    .verification-subtitle>p {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .verification-subtitle>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .verification-approved-card {
        text-align: center;
        padding-top: 1rem;
        padding-bottom: 2rem;
    }

    .verification-approved-visual {
        display: flex;
        justify-content: center;
        margin-bottom: 2rem;
    }

    .verification-approved-heading {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 36px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .verification-approved-status {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 36px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
    }


    .verification-approved-copy {
        margin: 1rem 0;
    }

    .verification-approved-copy p {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .verification-approved-copy-muted {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .verification-approved-actions {
        display: flex;
        justify-content: center;
    }

    .verification-approved-cta {
        width: auto;
        min-width: 200px;
        padding: 0 1.75rem;
        box-shadow: 0 4px 14px rgba(59, 55, 49, 0.14);
        background: #FFC97A;
    }

    .submit-section {
        display: flex;
        justify-content: center;
        align-items: center;
        width: 179px;
        height: 48px;
        border-radius: 96px;
        background: #FFC97A;
        cursor: pointer;
    }

    .verification-form .submit-section {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        width: 100%;
        height: 48px;
        background: transparent;
        cursor: pointer;
    }

    .submit-btn {
        border: none;
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        background: #FFC97A !important;
        cursor: pointer;
        width: 179px;
        height: 48px;
        border-radius: 96px;
    }

    .submit-btn>span {
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        background: transparent !important;
    }

    .verification-form {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .verification-form>div:nth-child(1) {
        border-radius: 10px;
        background: #FBFBFB;
        padding: 4rem;
        width: 715px;
        height: auto;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        border-radius: 10px;
        background: #FBFBFB;
        padding: 1.5rem;
        height: auto;
        gap: 1rem;
    }

    .form-group>div {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }

    .form-group>div>p {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .form-group>h3 {
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 1rem;
    }

    .form-group.full-width {
        grid-column: 1 / -1;
    }

    .form-label {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 0.5rem;
    }

    .form-label>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .input-container {
        position: relative;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .form-input {
        width: 100%;
        padding: 1rem 1.25rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        background: #ffffff;
        color: #374151;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .form-input:focus {
        outline: none;
        border-color: var(--active-bg);
        box-shadow: 0 0 0 4px rgba(255, 201, 122, 0.1);
        transform: translateY(-1px);
    }

    .form-input::placeholder {
        color: #9ca3af;
    }

    .input-field-wrap {
        position: relative;
        display: flex;
        align-items: center;
    }

    .input-field-wrap .form-input {
        padding-right: 2.75rem;
    }

    .input-valid-icon {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .input-field-wrap .form-input:not(:placeholder-shown)~.input-valid-icon {
        opacity: 1;
    }

    .input-icon {
        position: absolute;
        right: 1.25rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .success-icon {
        color: #10b981;
    }

    .error-icon {
        color: #ef4444;
    }

    .error-text {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }

    .input-hint {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 0.5rem;
        display: block;
    }

    .terms-section {
        padding: 1.5rem;
        background: #f9fafb;
        border-radius: 12px;
        border: 1px solid #e5e7eb;
    }

    .checkbox-container {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
    }

    .checkbox-input {
        width: 1.25rem;
        height: 1.25rem;
        margin-top: 0.25rem;
        cursor: pointer;
    }

    .checkbox-label {
        color: #374151;
        font-size: 0.95rem;
        line-height: 1.5;
        cursor: pointer;
        margin: 0;
    }

    .link-primary {
        color: #3b82f6;
        text-decoration: none;
        font-weight: 500;
    }

    .link-primary:hover {
        text-decoration: underline;
    }

    .btn-active {
        width: 105px;
        height: 48px;
        border-radius: 96px;
        background: #FFC97A;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .btn-disabled {
        background: #e5e7eb !important;
        color: #9ca3af;
        cursor: not-allowed;
        width: 105px;
        height: 48px;
        border-radius: 96px;
    }

    .btn-disabled:hover {
        transform: none;
        box-shadow: none;
    }

    .form-section {
        margin-bottom: 2rem;
    }

    .section-title {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 1rem;
    }

    .section-title>h3 {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: 20px;
    }

    .section-title>p {
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
        margin-bottom: 1rem;

    }

    .radio-group {
        display: flex;
        gap: 1rem;
    }

    .radio-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        cursor: pointer;
        width: 50%;
    }

    .radio-item:hover {
        background: #FFF4E4;
    }

    .radio-item.checked {
        background: #FFF4E4;
        border: none;
    }

    .radio-item input[type="radio"] {
        display: none;
    }

    .radio-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        font-weight: 500;
        color: #374151;
        margin: 0;
        flex: 1;
        justify-content: space-between;
    }

    .radio-content {
        flex: 1;
    }

    .radio-title {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    .radio-description {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .radio-custom {
        width: 24px;
        height: 24px;
        border: none;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        flex-shrink: 0;
    }

    .radio-custom::after {
        content: '';
        width: 19px;
        height: 19px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='19' height='19' viewBox='0 0 19 19' fill='none'%3E%3Cpath d='M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z' fill='%23C9DDA0'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        opacity: 0;
    }

    .radio-item input[type="radio"]:checked+.radio-label .radio-custom {
        border-radius: 10px;
        background: #FFF4E4;
    }

    .radio-item input[type="radio"]:checked+.radio-label .radio-custom::after,
    .radio-item.checked .radio-custom::after {
        opacity: 1;
    }

    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 1rem;
        border-radius: 12px;
        border: 2px solid #e5e7eb;
        cursor: pointer;
        width: fit-content;
        /* ← was causing the issue */
    }

    .checkbox-item:hover {
        background: transparent;
    }

    .checkbox-item.checked,
    .checkbox-item input[type="checkbox"]:checked+.checkbox-label {
        background: #FFF4E4;
        border: none;
    }

    .checkbox-item input[type="checkbox"] {
        display: none;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        font-weight: 500;
        color: #374151;
        margin: 0;
        flex: 1;
        justify-content: space-between;
        background: transparent !important;
    }

    .checkbox-content {
        flex: 1;
    }

    .checkbox-title {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .checkbox-custom {
        width: 24px;
        height: 24px;
        border: 2px solid #e5e7eb;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        border: none;
        flex-shrink: 0;
    }

    .checkbox-custom::after {
        content: '';
        width: 19px;
        height: 19px;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='19' height='19' viewBox='0 0 19 19' fill='none'%3E%3Cpath d='M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z' fill='%23C9DDA0'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        opacity: 0;
    }

    .checkbox-item input[type="checkbox"]:checked+.checkbox-label .checkbox-custom {
        border-radius: 10px;
        background: #FFF4E4;
    }

    .checkbox-item input[type="checkbox"]:checked+.checkbox-label .checkbox-custom::after,
    .checkbox-item.checked .checkbox-custom::after {
        opacity: 1;
    }

    /* Accuracy confirmation (custom circular checkbox) */
    .accuracy-confirm-wrap {
        width: 100%;
        margin-top: 5rem;
        margin-bottom: 1rem;
    }

    .accuracy-confirm-row {
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        cursor: pointer;
        margin: 0;
    }

    .accuracy-confirm-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
        margin: 0;
    }

    .accuracy-confirm-input:focus-visible+.accuracy-confirm-ui {
        outline: 2px solid #3B3731;
        outline-offset: 3px;
    }

    .accuracy-confirm-ui {
        flex-shrink: 0;
        width: 1.25rem;
        height: 1.25rem;
        border-radius: 50%;
        border: 2px solid #E8D5A8;
        box-sizing: border-box;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-top: 0.15rem;
        transition: border-color 0.15s ease;
    }

    .accuracy-confirm-input:checked+.accuracy-confirm-ui {
        border-color: #FFD88C;
    }

    .accuracy-confirm-input:checked+.accuracy-confirm-ui::after {
        content: '';
        width: 14px;
        height: 13px;
        border-radius: 50%;
        background: #FFD88C;
    }

    .accuracy-confirm-text {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    /* Form Buttons */
    .form-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 2rem;
    }

    .back-btn {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-decoration-line: underline;
        text-decoration-style: solid;
        text-decoration-skip-ink: auto;
        text-decoration-thickness: auto;
        text-underline-offset: auto;
        text-underline-position: from-font;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    /* Custom File Upload Styles */
    .custom-file-upload {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        margin-top: 1rem;
    }

    .upload-tabs {
        border-bottom: 1px solid #e5e7eb;
        background: #f9fafb;
    }

    .upload-tabs>div {
        display: flex;
        width: 50%;
    }

    .tab-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex: 1;
        padding: 12px 16px;
        border: none;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        background: transparent;
        cursor: pointer;
        transition: all 0.2s ease;
        border-bottom: 2px solid transparent;
    }

    .tab-btn:hover {
        color: #3b3731;
        background: #f3f4f6;
    }

    .tab-btn.active {
        color: #3b3731;
        background: white;
        border-bottom-color: #3b3731;
    }

    .tab-content {
        position: relative;
    }

    .tab-pane {
        display: none;
        background: white;
    }

    .tab-pane>p {
        text-align: center;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin-bottom: 1rem;
        background: #FBFBFB;
    }

    .tab-pane.active {
        display: block;
    }

    .file-list {
        margin-bottom: 16px;
    }

    .file-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border: 1px solid #E2E2E2;
    }

    .file-item-1 {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        border: 1px solid #E2E2E2;
        border-radius: 0 0 10px 10px;
        background: #F8F8F8;
        margin-bottom: 8px;
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .file-icon {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-family: Lato;
        font-size: 10px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-transform: uppercase;
    }

    .file-icon.file-icon--pdf svg {
        display: block;
        width: 21px;
        height: 25px;
    }

    .file-thumbnail {
        width: 32px;
        height: 32px;
        border-radius: 4px;
        object-fit: cover;
        border: 1px solid #e5e7eb;
    }

    .file-details {
        flex: 1;
    }

    .file-name {
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        color: #3b3731;
        margin-bottom: 2px;
    }

    .file-size {
        font-family: Lato;
        font-size: 12px;
        color: #6b7280;
    }

    .file-progress-text {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin-right: 12px;
        min-width: 150px;
    }

    .file-remove {
        width: 24px;
        height: 24px;
        border: none;
        background: transparent;
        color: #6b7280;
        cursor: pointer;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s ease;
    }

    .file-remove:hover {
        background: #f3f4f6;
        color: #374151;
    }

    .upload-area {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 10px;
        border-radius: 6px;
        padding: 32px;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s ease;
        position: relative;
    }

    .upload-area>div>p {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .upload-area>div>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }


    .upload-area.dragover {
        border-color: #ff6b35;
        background: #fff4e4;
    }

    .upload-icon {
        display: flex;
        justify-content: center;
        align-items: center;
        border-radius: 96px;
        border: 1px solid #D8D8D8;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        width: 113px;
        height: 36px;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .upload-area p {
        margin: 0;
        font-family: Lato;
        font-size: 14px;
        color: #6b7280;
    }

    .browse-link {
        color: #ff6b35;
        text-decoration: underline;
        cursor: pointer;
        font-weight: 500;
    }

    .browse-link:hover {
        color: #e55a2b;
    }

    .hidden-input {
        display: none;
    }

    /* Upload Tab Styles */
    .upload-methods {
        display: flex;
        flex-direction: column;
        gap: 24px;
    }

    .upload-method h4 {
        font-family: Lato;
        font-size: 14px;
        font-weight: 600;
        color: #3b3731;
        margin: 0 0 12px 0;
    }

    .upload-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: white;
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .upload-btn:hover {
        border-color: #ff6b35;
        background: #fff4e4;
    }

    .source-buttons {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
    }

    .source-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 4px;
        background: white;
        color: #6b7280;
        font-family: Lato;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .source-btn:hover {
        border-color: #9ca3af;
        background: #f9fafb;
        color: #374151;
    }

    /* Business Basics (step 2) */
    .business-basics-wrap {
        max-width: 35rem;
        margin: 0 auto;
    }

    .business-basics-title {
        color: #3B3731;
        font-family: "Playfair Display", serif;
        font-size: 36px;
        font-weight: 700;
        margin: 0 0 2rem;
        text-align: center;
    }

    .business-basics-form {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }

    .basics-card {
        border-radius: 10px;
        background: #FBFBFB;
        padding: 1.75rem 2rem;
        border: none;
    }

    .basics-card-heading {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 0 0 1.25rem;
    }

    .basics-section-intro .basics-card-heading {
        margin-bottom: 0.35rem;
    }

    .basics-section-muted {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin: 0 0 1rem;
    }

    .basics-field {
        margin-bottom: 1.25rem;
        display: flex;
        flex-direction: column;
        align-items: stretch;
    }

    .business-basics-form .basics-field>.form-label {
        display: block;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 0.7rem;
    }

    .basics-field:last-child {
        margin-bottom: 0;
    }

    .groomer-focus-wrap {
        display: flex;
        flex-direction: column;
        gap: 0.9rem;
    }

    .groomer-focus-heading {
        margin: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 22px;
        font-weight: 500;
        line-height: 1.2;
    }

    .groomer-pill-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.7rem;
    }

    .groomer-pill-option {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 auto;
        width: fit-content;
        max-width: max-content;
        border-radius: 96px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        color: #FDFCF8;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        min-height: 58px;
        padding: 0.5rem 1.65rem;
        cursor: pointer;
        user-select: none;
        transition: all 0.2s ease;
    }

    .groomer-pill-option>span {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        white-space: nowrap;
        color: #D4D4D4;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .groomer-pill-option input {
        display: none;
    }

    .groomer-pill-option.is-active {
        background: #FFC97A;
        border: none;
        color: #FFF !important;
    }

    .groomer-pill-option.is-active>span {
        color: #FDFCF8 !important;
    }

    .groomer-pill-icon {
        color: #D4D4D4;
        flex-shrink: 0;
    }

    .groomer-pill-option.is-active .groomer-pill-icon {
        color: #FDFCF8;
    }

    .groomer-pill-option.groomer-pill-size span::before {
        content: none;
    }

    .groomer-pill-option.groomer-pill-size.is-active span::before {
        content: "";
        display: inline-block;
        width: 14px;
        height: 10px;
        background-color: #FDFCF8;
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10' fill='none'%3E%3Cpath d='M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z' fill='black'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10' fill='none'%3E%3Cpath d='M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z' fill='black'/%3E%3C/svg%3E");
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-size: 14px 10px;
        mask-size: 14px 10px;
        -webkit-mask-position: center;
        mask-position: center;
    }

    .groomer-pill-option.groomer-pill-specialty.is-active span::before {
        content: "";
        display: inline-block;
        width: 14px;
        height: 10px;
        background-color: #FDFCF8;
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10' fill='none'%3E%3Cpath d='M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z' fill='black'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10' fill='none'%3E%3Cpath d='M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z' fill='black'/%3E%3C/svg%3E");
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-size: 14px 10px;
        mask-size: 14px 10px;
        -webkit-mask-position: center;
        mask-position: center;
    }

    .profile-photo-upload-tracker {
        display: flex;
        flex-direction: column;
    }

    .profile-photo-drop {
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        gap: 1.5rem;
        padding: 1.25rem;
        border: 1px solid #E2E2E2;
        border-radius: 10px 10px 0 0;
        background: #FFF;
    }

    .profile-photo-placeholder-static {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .profile-photo-copy {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .profile-photo-copy .profile-photo-label,
    .profile-photo-label {
        color: #3B3731;
        text-align: center;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        line-height: normal;
        margin: 0;
    }

    .profile-photo-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        margin-top: 0.75rem;
        cursor: pointer;
        width: 162px;
        height: 48px;
        border-radius: 96px;
        background: #FFC97A;
    }

    .profile-photo-btn-inner {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        color: #FFF;
        text-align: center;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .profile-basics-avatar-attachments {
        margin-bottom: 0;
    }

    .profile-avatar-upload-progress {
        border-radius: 0 0 10px 10px;
        border: 1px solid #E2E2E2;
        background: #F8F8F8;
        padding: 0.85rem 1rem;
    }

    .profile-avatar-upload-progress-inner {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .profile-avatar-upload-progress-thumb-wrap {
        width: 48px;
        height: 48px;
        border-radius: 8px;
        overflow: hidden;
        background: #f3f4f6;
        flex-shrink: 0;
        border: 1px solid #e8e8e8;
    }

    .profile-avatar-upload-progress-thumb {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .profile-avatar-upload-progress-text {
        flex: 1;
        min-width: 0;
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .profile-avatar-upload-progress-name {
        font-family: Lato, sans-serif;
        font-size: 15px;
        font-weight: 500;
        color: #3B3731;
        word-break: break-word;
    }

    .profile-avatar-upload-progress-status {
        font-family: Lato, sans-serif;
        font-size: 14px;
        color: #9D9B98;
    }

    .profile-avatar-upload-progress-cancel {
        border: none;
        background: transparent;
        color: #6b7280;
        font-size: 1.35rem;
        line-height: 1;
        padding: 0.2rem 0.45rem;
        cursor: pointer;
        flex-shrink: 0;
        border-radius: 6px;
    }

    .profile-avatar-upload-progress-cancel:hover {
        color: #3B3731;
        background: #f3f4f6;
    }

    .basics-upload-hint {
        width: 100%;
        font-size: 14px;
        color: #9D9B98;
        margin-top: 0.5rem;
    }

    .gallery-slots {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
    }

    .gallery-slot {
        position: relative;
        aspect-ratio: 1;
        border-radius: 12px;
        border: none;
        background: #fff;
        overflow: hidden;
    }

    .gallery-slot-img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .gallery-slot-remove {
        position: absolute;
        top: 6px;
        right: 6px;
        width: 28px;
        height: 28px;
        border: none;
        border-radius: 5px;
        background: rgba(0, 0, 0, 0.45);
        color: #fff;
        font-size: 18px;
        line-height: 1;
        cursor: pointer;
    }

    .gallery-slot-empty,
    .gallery-slot-placeholder {
        border: none;
        border-radius: 10px;
        background: #FFF;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        min-height: 120px;
        cursor: pointer;
    }

    .gallery-paw {
        opacity: 0.85;
    }


    .gallery-slot-empty {
        position: relative;
    }

    .basics-actions {
        display: flex;
        justify-content: flex-end;
        padding-top: 0.5rem;
    }

    .basics-actions .submit-section {
        justify-content: flex-end;
    }
</style>

<script>
    // ── Helpers ──────────────────────────────────────────────────────
    window.uploadedFiles = [];
    window.insuranceUploadedFiles = [];
    window.businessOwnerIdUploadedFiles = [];
    /** @type {File[]} Cumulative picks across multiple open-file dialogs */
    window.businessOwnerIdRawFiles = window.businessOwnerIdRawFiles || [];
    window.__boIdIgnoreNextChange = false;
    /** Fingerprint added only when the fake progress bar finishes — morphs after that skip re-animating. */
    window.__uploadSimCompletedFps = window.__uploadSimCompletedFps || new Set();
    /** One active interval per file fingerprint so a quick morph can replace the row without leaving ghost timers. */
    window.__uploadSimIntervalByFp = window.__uploadSimIntervalByFp || new Map();

    /**
     * Business Basics profile photo: Livewire upload progress row (thumbnail + KB + cancel).
     * See https://livewire.laravel.com/docs/uploads#progress-indicators
     */
    (function bindProfileAvatarLwUploadUi() {
        if (window.__verifyQualifyAvatarLwUiBound) {
            return;
        }
        window.__verifyQualifyAvatarLwUiBound = true;

        var avatarThumbObjectUrl = null;

        function revokeAvatarThumb() {
            if (avatarThumbObjectUrl) {
                try {
                    URL.revokeObjectURL(avatarThumbObjectUrl);
                } catch (err) {
                    /* ignore */
                }
                avatarThumbObjectUrl = null;
            }
        }

        function trackerFromEventTarget(t) {
            return t && t.closest && t.closest('.profile-photo-upload-tracker');
        }

        function formatKb(bytes) {
            var n = Number(bytes) || 0;
            return Math.max(0, Math.round(n / 1024)) + ' KB';
        }

        function els(tracker) {
            return {
                row: tracker.querySelector('.profile-avatar-upload-progress'),
                thumb: tracker.querySelector('.profile-avatar-upload-progress-thumb'),
                nameEl: tracker.querySelector('.profile-avatar-upload-progress-name'),
                statusEl: tracker.querySelector('.profile-avatar-upload-progress-status'),
            };
        }

        function hide(tracker) {
            revokeAvatarThumb();
            var o = els(tracker);
            if (o.row) {
                o.row.hidden = true;
            }
            if (o.thumb) {
                o.thumb.removeAttribute('src');
            }
            if (o.nameEl) {
                o.nameEl.textContent = '';
            }
            if (o.statusEl) {
                o.statusEl.textContent = '';
            }
        }

        document.addEventListener('livewire-upload-start', function(e) {
            var tracker = trackerFromEventTarget(e.target);
            if (!tracker) {
                return;
            }
            var input = e.target;
            if (!input || input.tagName !== 'INPUT' || input.type !== 'file') {
                return;
            }

            var o = els(tracker);
            revokeAvatarThumb();
            var file = input.files && input.files[0];
            if (file) {
                if (o.nameEl) {
                    o.nameEl.textContent = String(file.name || '').replace(/[<>]/g, '');
                }
                avatarThumbObjectUrl = URL.createObjectURL(file);
                if (o.thumb) {
                    o.thumb.src = avatarThumbObjectUrl;
                }
                if (o.statusEl) {
                    o.statusEl.textContent = formatKb(0) + ' of ' + formatKb(file.size) + ' • Uploading...';
                }
            }
            if (o.row) {
                o.row.hidden = false;
            }
        });

        document.addEventListener('livewire-upload-progress', function(e) {
            var tracker = trackerFromEventTarget(e.target);
            if (!tracker) {
                return;
            }
            var input = e.target;
            var file = input.files && input.files[0];
            var o = els(tracker);
            var p = (e.detail && typeof e.detail.progress === 'number') ? e.detail.progress : 0;
            if (o.statusEl && file) {
                var loaded = Math.round((file.size * p) / 100);
                o.statusEl.textContent = formatKb(loaded) + ' of ' + formatKb(file.size) +
                    ' • Uploading...';
            }
        });

        function end(tracker) {
            hide(tracker);
        }

        document.addEventListener('livewire-upload-finish', function(e) {
            var t = trackerFromEventTarget(e.target);
            if (t) {
                end(t);
            }
        });
        document.addEventListener('livewire-upload-cancel', function(e) {
            var t = trackerFromEventTarget(e.target);
            if (t) {
                end(t);
            }
        });
        document.addEventListener('livewire-upload-error', function(e) {
            var t = trackerFromEventTarget(e.target);
            if (t) {
                end(t);
            }
        });
    })();

    function businessOwnerFileFingerprint(file) {
        return file.name + '\0' + file.size + '\0' + file.lastModified;
    }

    function mergeBusinessOwnerPicks(input, newlyPickedFiles) {
        if (!window.businessOwnerIdRawFiles) {
            window.businessOwnerIdRawFiles = [];
        }
        const seen = new Set(window.businessOwnerIdRawFiles.map(businessOwnerFileFingerprint));
        newlyPickedFiles.forEach((f) => {
            const fp = businessOwnerFileFingerprint(f);
            if (!seen.has(fp)) {
                seen.add(fp);
                window.businessOwnerIdRawFiles.push(f);
            }
        });
        const dt = new DataTransfer();
        window.businessOwnerIdRawFiles.forEach((f) => dt.items.add(f));
        window.__boIdIgnoreNextChange = true;
        input.files = dt.files;
    }

    function storagePathFromPublicUrl(url) {
        try {
            const u = new URL(url, window.location.origin);
            const i = u.pathname.indexOf('/storage/');
            if (i === -1) return '';
            return decodeURIComponent(u.pathname.slice(i + '/storage/'.length));
        } catch (e) {
            return '';
        }
    }

    function readSavedBusinessOwnerEntries() {
        const hid = document.getElementById('business-owner-saved-urls-json');
        let raw = [];
        if (hid) {
            try {
                raw = JSON.parse(hid.value || '[]');
            } catch (e) {
                raw = [];
            }
        }
        if (!Array.isArray(raw) || !raw.length) {
            raw = Array.isArray(window.__boSavedFileEntries) ? window.__boSavedFileEntries : [];
        }
        if (!Array.isArray(raw)) return [];
        return raw
            .map((item) => {
                if (typeof item === 'string') {
                    const path = storagePathFromPublicUrl(item);
                    return {
                        path: path || '',
                        url: item
                    };
                }
                if (item && typeof item.url === 'string') {
                    return {
                        path: item.path || storagePathFromPublicUrl(item.url) || '',
                        url: item.url,
                    };
                }
                return null;
            })
            .filter(Boolean);
    }

    if (!window.removeBusinessOwnerStoredFile) {
        window.removeBusinessOwnerStoredFile = function(storagePath) {
            if (!storagePath || !window.Livewire) return;
            const root = document.querySelector('[wire\\:id]');
            if (!root) return;
            const wid = root.getAttribute('wire:id');
            if (!wid) return;
            const c = Livewire.find(wid);
            if (c && typeof c.$call === 'function') {
                c.$call('removeStoredBusinessOwnerImage', storagePath);
            }
        };
    }

    function appendSavedBusinessOwnerRows(listEl) {
        if (!listEl) return;
        const entries = readSavedBusinessOwnerEntries();
        if (!entries.length) return;
        entries.forEach((entry) => {
            const url = entry.url;
            const storagePath = entry.path;
            // Signed URLs end with .../business-owner-id-file?t=... — no extension; use storage path for name/type
            const seg =
                (storagePath && storagePath.split('/').pop()) ||
                (url.split('/').pop() || 'file').split('?')[0];
            const name = decodeURIComponent(seg);
            const div = document.createElement('div');
            div.className = 'file-item file-item--saved';
            const isImg = /\.(jpe?g|png|gif|webp|bmp|heic)$/i.test(name);
            const isPdf = /\.pdf$/i.test(name) || getFileExtension(name) === 'PDF';
            const thumb = isImg ?
                `<img src="${url}" class="file-thumbnail" alt="" loading="lazy">` :
                isPdf ?
                `<div class="file-icon file-icon--pdf">${pdfIconSvgHtml()}</div>` :
                `<div class="file-icon">${getFileExtension(name)}</div>`;
            div.innerHTML = `
            <div class="file-info">
                ${thumb}
                <div class="file-details">
                    <div class="file-name"><a href="${url}" target="_blank" rel="noopener noreferrer">${name}</a></div>
                    <div class="file-progress-text" style="color:#10b981">Saved</div>
                </div>
            </div>
            ${storagePath
                    ? `<button type="button" class="file-remove" title="Remove" aria-label="Remove">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>`
                    : ''
                }`;
            const btn = div.querySelector('.file-remove');
            if (btn && storagePath) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    window.removeBusinessOwnerStoredFile(storagePath);
                });
            }
            listEl.appendChild(div);
        });
    }

    function readSavedInsuranceEntries() {
        const hid = document.getElementById('insurance-saved-urls-json');
        let raw = [];
        if (hid) {
            try {
                raw = JSON.parse(hid.value || '[]');
            } catch (e) {
                raw = [];
            }
        }
        if (!Array.isArray(raw) || !raw.length) {
            raw = Array.isArray(window.__insSavedFileEntries) ? window.__insSavedFileEntries : [];
        }
        if (!Array.isArray(raw)) {
            return [];
        }
        return raw
            .map((item) => {
                if (typeof item === 'string') {
                    const path = storagePathFromPublicUrl(item);
                    return {
                        path: path || '',
                        url: item
                    };
                }
                if (item && typeof item.url === 'string') {
                    return {
                        path: item.path || storagePathFromPublicUrl(item.url) || '',
                        url: item.url,
                    };
                }
                return null;
            })
            .filter(Boolean);
    }

    if (!window.removeInsuranceStoredFile) {
        window.removeInsuranceStoredFile = function(storagePath) {
            if (!storagePath || !window.Livewire) {
                return;
            }
            const root = document.querySelector('[wire\\:id]');
            if (!root) {
                return;
            }
            const wid = root.getAttribute('wire:id');
            if (!wid) {
                return;
            }
            const c = Livewire.find(wid);
            if (c && typeof c.$call === 'function') {
                c.$call('removeStoredInsuranceCertificate', storagePath);
            }
        };
    }

    function appendSavedInsuranceRows(listEl) {
        if (!listEl) {
            return;
        }
        const entries = readSavedInsuranceEntries();
        if (!entries.length) {
            return;
        }
        entries.forEach((entry) => {
            const url = entry.url;
            const storagePath = entry.path;
            const seg =
                (storagePath && storagePath.split('/').pop()) ||
                (url.split('/').pop() || 'file').split('?')[0];
            const name = decodeURIComponent(seg);
            const div = document.createElement('div');
            div.className = 'file-item file-item--saved';
            const isImg = /\.(jpe?g|png|gif|webp|bmp|heic)$/i.test(name);
            const isPdf = /\.pdf$/i.test(name) || getFileExtension(name) === 'PDF';
            const thumb = isImg ?
                `<img src="${url}" class="file-thumbnail" alt="" loading="lazy">` :
                isPdf ?
                `<div class="file-icon file-icon--pdf">${pdfIconSvgHtml()}</div>` :
                `<div class="file-icon">${getFileExtension(name)}</div>`;
            div.innerHTML = `
            <div class="file-info">
                ${thumb}
                <div class="file-details">
                    <div class="file-name"><a href="${url}" target="_blank" rel="noopener noreferrer">${name}</a></div>
                    <div class="file-progress-text" style="color:#10b981">Saved</div>
                </div>
            </div>
            ${storagePath
                    ? `<button type="button" class="file-remove" title="Remove" aria-label="Remove">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>`
                    : ''
                }`;
            const btn = div.querySelector('.file-remove');
            if (btn && storagePath) {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    window.removeInsuranceStoredFile(storagePath);
                });
            }
            listEl.appendChild(div);
        });
    }

    function renderInsuranceAttachListIfNeeded() {
        const listEl = document.getElementById('insurance-file-list');
        if (!listEl) {
            return;
        }
        const pending = window.insuranceUploadedFiles || [];
        if (pending.length > 0) {
            listEl.replaceChildren();
            appendSavedInsuranceRows(listEl);
            pending.forEach((item) => {
                if (item && item.element) {
                    listEl.appendChild(item.element);
                }
            });
            updateNoFileMessage(listEl);
            return;
        }
        const entries = readSavedInsuranceEntries();
        if (!entries.length) {
            return;
        }
        listEl.replaceChildren();
        appendSavedInsuranceRows(listEl);
        updateNoFileMessage(listEl);
        showInsuranceAttachTab();
    }

    function renderBusinessOwnerAttachListIfNeeded() {
        const listEl = document.getElementById('business-owner-id-file-list');
        if (!listEl) return;
        const raw = window.businessOwnerIdRawFiles || [];
        if (raw.length > 0) {
            rebuildBusinessOwnerIdListUI();
            return;
        }
        const entries = readSavedBusinessOwnerEntries();
        if (!entries.length) return;
        listEl.replaceChildren();
        appendSavedBusinessOwnerRows(listEl);
        updateNoFileMessage(listEl);
    }

    function rebuildBusinessOwnerIdListUI() {
        const listEl = document.getElementById('business-owner-id-file-list');
        if (!listEl) return;
        listEl.replaceChildren();
        window.businessOwnerIdUploadedFiles = [];
        appendSavedBusinessOwnerRows(listEl);
        const raw = window.businessOwnerIdRawFiles || [];
        raw.forEach((file) => {
            const item = createFileItem(file, (removedId) => {
                const removed = window.businessOwnerIdUploadedFiles.find((x) => x.id === removedId);
                if (!removed || !removed.file) return;
                clearUploadSimForFile(removed.file);
                window.businessOwnerIdRawFiles = (window.businessOwnerIdRawFiles || []).filter(
                    (f) => businessOwnerFileFingerprint(f) !== businessOwnerFileFingerprint(removed
                        .file)
                );
                const input = document.getElementById('business-owner-id-file-input');
                if (input) {
                    const dt = new DataTransfer();
                    (window.businessOwnerIdRawFiles || []).forEach((f) => dt.items.add(f));
                    window.__boIdIgnoreNextChange = true;
                    input.files = dt.files;
                    input.dispatchEvent(new Event('input', {
                        bubbles: true
                    }));
                }
                rebuildBusinessOwnerIdListUI();
            });
            listEl.appendChild(item.element);
            window.businessOwnerIdUploadedFiles.push(item);
            simulateUpload(item);
        });
        updateNoFileMessage(listEl);
    }

    if (!window.__boOwnerUploadClickDelegated) {
        window.__boOwnerUploadClickDelegated = true;
        document.addEventListener(
            'click',
            function(e) {
                const area = e.target.closest && e.target.closest('#business-owner-id-upload-area');
                if (!area) return;
                if (e.target.closest('#business-owner-id-file-input')) return;
                e.preventDefault();
                const inp = document.getElementById('business-owner-id-file-input');
                if (!inp || window.__boIdFileDialogOpening) return;
                window.__boIdFileDialogOpening = true;
                inp.click();
                setTimeout(function() {
                    window.__boIdFileDialogOpening = false;
                }, 500);
            },
            true
        );
    }

    /** One delegated handler + debounce — avoids duplicate open when setupUploaders re-binds after Livewire morph */
    if (!window.__insuranceUploadClickDelegated) {
        window.__insuranceUploadClickDelegated = true;
        document.addEventListener(
            'click',
            function(e) {
                const area = e.target.closest && e.target.closest('#insurance-upload-area');
                if (!area) return;
                if (e.target.closest('#insurance-file-input')) return;
                e.preventDefault();
                const inp = document.getElementById('insurance-file-input');
                if (!inp || window.__insIdFileDialogOpening) return;
                window.__insIdFileDialogOpening = true;
                inp.click();
                setTimeout(function() {
                    window.__insIdFileDialogOpening = false;
                }, 500);
            },
            true
        );
    }

    function formatFileSize(bytes) {
        if (!bytes) return '0 Bytes';
        const k = 1024,
            sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function getFileExtension(name) {
        return name.split('.').pop().toUpperCase();
    }

    function pdfIconSvgHtml() {
        return `<svg xmlns="http://www.w3.org/2000/svg" width="21" height="25" viewBox="0 0 21 25" fill="none" aria-hidden="true">
  <path d="M5.04074 24.501H15.9593C17.1635 24.501 18.3185 24.0226 19.1701 23.1711C20.0216 22.3195 20.5 21.1646 20.5 19.9603V12.7859C20.5004 11.5818 20.0226 10.4268 19.1715 9.57499L11.4276 1.82979C11.0059 1.40815 10.5053 1.0737 9.95439 0.845536C9.40346 0.61737 8.81297 0.499957 8.21666 0.5H5.04074C3.83646 0.5 2.6815 0.978398 1.82995 1.82995C0.978398 2.6815 0.5 3.83646 0.5 5.04074V19.9603C0.5 21.1646 0.978398 22.3195 1.82995 23.1711C2.6815 24.0226 3.83646 24.501 5.04074 24.501Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M10.0952 0.966797V8.30982C10.0952 8.99798 10.3686 9.65795 10.8552 10.1446C11.3418 10.6312 12.0018 10.9045 12.6899 10.9045H20.0355" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M4.33759 18.3383V17.041M4.33759 17.041V14.4463H5.63494C5.97902 14.4463 6.30901 14.583 6.55231 14.8263C6.79561 15.0696 6.93229 15.3996 6.93229 15.7436C6.93229 16.0877 6.79561 16.4177 6.55231 16.661C6.30901 16.9043 5.97902 17.041 5.63494 17.041H4.33759ZM14.7164 18.3383V16.7167M14.7164 16.7167V14.4463H16.6624M14.7164 16.7167H16.6624M9.527 18.3383V14.4463H10.1757C10.6918 14.4463 11.1868 14.6513 11.5517 15.0163C11.9167 15.3812 12.1217 15.8762 12.1217 16.3923C12.1217 16.9084 11.9167 17.4034 11.5517 17.7684C11.1868 18.1333 10.6918 18.3383 10.1757 18.3383H9.527Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round"/>
</svg>`;
    }

    function getFileIcon(file) {
        if (file.type.startsWith('image/'))
            return `<img src="${URL.createObjectURL(file)}" class="file-thumbnail" alt="${file.name}">`;
        if (file.type === 'application/pdf' || getFileExtension(file.name) === 'PDF')
            return `<div class="file-icon file-icon--pdf">${pdfIconSvgHtml()}</div>`;
        return `<div class="file-icon">${getFileExtension(file.name)}</div>`;
    }

    function clearUploadSimForFile(file) {
        if (!file) return;
        const fp = businessOwnerFileFingerprint(file);
        const iv = window.__uploadSimIntervalByFp?.get(fp);
        if (iv != null) {
            clearInterval(iv);
            window.__uploadSimIntervalByFp.delete(fp);
        }
        window.__uploadSimCompletedFps?.delete(fp);
    }

    function simulateUpload(fileItemData) {
        const file = fileItemData.file;
        if (!file) return;
        const fp = businessOwnerFileFingerprint(file);
        const total = file.size;
        const progressText = fileItemData.element.querySelector('.file-progress-text');

        if (window.__uploadSimCompletedFps.has(fp)) {
            if (progressText) {
                progressText.textContent = `${formatFileSize(total)} • Uploaded`;
                progressText.style.color = '#10b981';
            }
            return;
        }

        const prevIv = window.__uploadSimIntervalByFp.get(fp);
        if (prevIv != null) {
            clearInterval(prevIv);
        }

        let uploaded = 0;
        const iv = setInterval(() => {
            const pt = fileItemData.element.querySelector('.file-progress-text');
            if (!pt || !pt.isConnected) {
                clearInterval(iv);
                window.__uploadSimIntervalByFp.delete(fp);
                return;
            }
            uploaded += Math.random() * total * 0.1;
            if (uploaded >= total) {
                clearInterval(iv);
                window.__uploadSimIntervalByFp.delete(fp);
                window.__uploadSimCompletedFps.add(fp);
                pt.textContent = `${formatFileSize(total)} • Uploaded`;
                pt.style.color = '#10b981';
            } else {
                pt.textContent = `${formatFileSize(uploaded)} of ${formatFileSize(total)} • Uploading...`;
            }
        }, 200);
        window.__uploadSimIntervalByFp.set(fp, iv);
    }

    function createFileItem(file, onRemove) {
        const id = Date.now() + '_' + Math.random().toString(36).substr(2, 9);
        const div = document.createElement('div');
        div.className = 'file-item';
        div.dataset.fileId = id;
        div.innerHTML = `
            <div class="file-info">
                ${getFileIcon(file)}
                <div class="file-details">
                    <div class="file-name">${file.name}</div>
                    <div class="file-progress-text">0 KB of ${formatFileSize(file.size)} • Uploading...</div>
                </div>
            </div>
            <button class="file-remove" type="button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                    <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"/>
                </svg>
            </button>`;
        div.querySelector('.file-remove').addEventListener('click', () => {
            div.remove();
            onRemove(id);
        });
        return {
            element: div,
            id,
            file
        };
    }

    function updateNoFileMessage(listEl) {
        if (!listEl) return;
        const pane = listEl.closest('.tab-pane');
        if (!pane) return;
        const msg = pane.querySelector('.file-list-empty-msg');
        if (msg) msg.style.display = listEl.children.length === 0 ? 'block' : 'none';
    }

    /** Show Business Owner ID "Attach" tab so the file list (which lives there) is visible */
    function showBusinessOwnerAttachTab() {
        const wrap = document.getElementById('business-owner-id-file-input')?.closest('.custom-file-upload');
        if (!wrap) return;
        const attachBtn = wrap.querySelector('[data-tab="attach"]');
        const uploadBtn = wrap.querySelector('[data-tab="upload"]');
        if (attachBtn) attachBtn.classList.add('active');
        if (uploadBtn) uploadBtn.classList.remove('active');
        const attachPane = document.getElementById('attach-tab');
        const uploadPane = document.getElementById('upload-tab');
        if (attachPane) attachPane.classList.add('active');
        if (uploadPane) uploadPane.classList.remove('active');
    }

    /** Insurance certificate list lives on Attach tab; show it after picking files on Upload tab */
    function showInsuranceAttachTab() {
        const wrap = document.getElementById('insurance-file-input')?.closest('.custom-file-upload');
        if (!wrap) return;
        const attachBtn = wrap.querySelector('[data-tab="insurance-attach"]');
        const uploadBtn = wrap.querySelector('[data-tab="insurance-upload"]');
        if (attachBtn) attachBtn.classList.add('active');
        if (uploadBtn) uploadBtn.classList.remove('active');
        const attachPane = document.getElementById('insurance-attach-tab');
        const uploadPane = document.getElementById('insurance-upload-tab');
        if (attachPane) attachPane.classList.add('active');
        if (uploadPane) uploadPane.classList.remove('active');
    }

    // ── Restore file items after Livewire morphs the DOM ─────────────
    function restoreFileItems() {
        const fileList = document.getElementById('business-file-list');
        const insFileList = document.getElementById('insurance-file-list');
        const businessOwnerIdFileList = document.getElementById('business-owner-id-file-list');

        if (fileList) {
            if (fileList.children.length === 0 && window.uploadedFiles.length > 0) {
                window.uploadedFiles.forEach(item => {
                    if (item.element && !fileList.contains(item.element)) {
                        fileList.appendChild(item.element);
                    }
                });
                updateNoFileMessage(fileList);
            }
        }

        if (insFileList) {
            if (insFileList.children.length === 0 && window.insuranceUploadedFiles.length > 0) {
                window.insuranceUploadedFiles.forEach(item => {
                    if (item.element && !insFileList.contains(item.element)) {
                        insFileList.appendChild(item.element);
                    }
                });
                updateNoFileMessage(insFileList);
            }
        }

        if (businessOwnerIdFileList && window.businessOwnerIdUploadedFiles.length > 0) {
            if (businessOwnerIdFileList.children.length === 0) {
                window.businessOwnerIdUploadedFiles.forEach(item => {
                    if (item.element && !businessOwnerIdFileList.contains(item.element)) {
                        businessOwnerIdFileList.appendChild(item.element);
                    }
                });
            }
            updateNoFileMessage(businessOwnerIdFileList);
        }
    }

    // ── Tab switching — delegated once on document so it works for dynamically-injected steps ───
    function setupTabs() {
        if (window.__tabDelegationBound) return;
        window.__tabDelegationBound = true;

        document.addEventListener('click', function(e) {
            const btn = e.target.closest('[data-tab]');
            if (!btn) return;

            const target = btn.dataset.tab;
            const wrap = btn.closest('.custom-file-upload');
            if (!wrap) return;

            e.preventDefault();

            // Deactivate all sibling tab buttons in this upload widget
            wrap.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
            // Activate clicked button
            btn.classList.add('active');

            // Deactivate all panes in this widget, activate the target one
            wrap.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
            const pane = wrap.querySelector('[id="' + target + '-tab"]');
            if (pane) pane.classList.add('active');
        });
    }

    // ── File upload — only bind once per input using a flag ───────────
    function setupUploaders() {
        // ── Business Owner ID ─────────────────────────────────────────
        const fileInput = document.getElementById('file-input');
        const uploadArea = document.getElementById('upload-area');
        const fileList = document.getElementById('business-file-list');

        if (fileInput && !fileInput.dataset.bound) {
            fileInput.dataset.bound = '1';

            fileInput.addEventListener('change', function() {
                const listEl = document.getElementById('business-file-list') || fileList;
                if (!listEl) return;
                (window.uploadedFiles || []).forEach((u) => {
                    if (u && u.file) clearUploadSimForFile(u.file);
                });
                listEl.replaceChildren();
                window.uploadedFiles = [];

                Array.from(this.files).forEach(file => {
                    const item = createFileItem(file, removedId => {
                        const removed = window.uploadedFiles.find((f) => f.id === removedId);
                        if (removed && removed.file) {
                            clearUploadSimForFile(removed.file);
                        }
                        window.uploadedFiles = window.uploadedFiles.filter(f => f.id !==
                            removedId);
                        updateNoFileMessage(listEl);
                    });
                    listEl.appendChild(item.element);
                    window.uploadedFiles.push(item);
                    simulateUpload(item);
                });
                updateNoFileMessage(listEl);
            });
        }

        if (uploadArea && !uploadArea.dataset.bound) {
            uploadArea.dataset.bound = '1';

            // Use stopPropagation on the hidden input so click doesn't bubble back up
            uploadArea.addEventListener('click', function(e) {
                // If the click came FROM the hidden input itself, ignore it
                if (e.target === fileInput || e.target === document.getElementById('file-input')) return;
                document.getElementById('file-input').click();
            });

            uploadArea.addEventListener('dragover', e => {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            uploadArea.addEventListener('dragleave', e => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });
            uploadArea.addEventListener('drop', e => {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                if (!fileInput || !fileList) return;
                fileInput.files = e.dataTransfer.files;
                fileInput.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            });
        }

        // ── Insurance Certificate ─────────────────────────────────────
        const insFileInput = document.getElementById('insurance-file-input');
        const insUploadArea = document.getElementById('insurance-upload-area');
        const insFileList = document.getElementById('insurance-file-list');

        if (insFileInput && !insFileInput.dataset.bound) {
            insFileInput.dataset.bound = '1';

            insFileInput.addEventListener('change', function() {
                const listEl = document.getElementById('insurance-file-list') || insFileList;
                if (!listEl) return;
                (window.insuranceUploadedFiles || []).forEach((u) => {
                    if (u && u.file) clearUploadSimForFile(u.file);
                });
                listEl.replaceChildren();
                window.insuranceUploadedFiles = [];
                appendSavedInsuranceRows(listEl);

                Array.from(this.files).forEach(file => {
                    const item = createFileItem(file, removedId => {
                        const removed = window.insuranceUploadedFiles.find((f) => f.id ===
                            removedId);
                        if (removed && removed.file) {
                            clearUploadSimForFile(removed.file);
                        }
                        window.insuranceUploadedFiles = window.insuranceUploadedFiles.filter(
                            f => f.id !== removedId);
                        updateNoFileMessage(listEl);
                    });
                    listEl.appendChild(item.element);
                    window.insuranceUploadedFiles.push(item);
                    simulateUpload(item);
                });
                updateNoFileMessage(listEl);
                this.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                showInsuranceAttachTab();
            });
        }

        if (insUploadArea && !insUploadArea.dataset.bound) {
            insUploadArea.dataset.bound = '1';

            insUploadArea.addEventListener('dragover', e => {
                e.preventDefault();
                insUploadArea.classList.add('dragover');
            });
            insUploadArea.addEventListener('dragleave', e => {
                e.preventDefault();
                insUploadArea.classList.remove('dragover');
            });
            insUploadArea.addEventListener('drop', e => {
                e.preventDefault();
                insUploadArea.classList.remove('dragover');
                if (!insFileInput) return;
                insFileInput.files = e.dataTransfer.files;
                insFileInput.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            });
        }

        // Business Owner ID Images
        const businessOwnerIdFileInput = document.getElementById('business-owner-id-file-input');
        const businessOwnerIdUploadArea = document.getElementById('business-owner-id-upload-area');
        const businessOwnerIdFileList = document.getElementById('business-owner-id-file-list');

        if (businessOwnerIdFileInput && !businessOwnerIdFileInput.dataset.bound) {
            businessOwnerIdFileInput.dataset.bound = '1';

            businessOwnerIdFileInput.addEventListener('change', function() {
                if (window.__boIdIgnoreNextChange) {
                    window.__boIdIgnoreNextChange = false;
                    return;
                }
                mergeBusinessOwnerPicks(this, Array.from(this.files));
                rebuildBusinessOwnerIdListUI();
                this.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                showBusinessOwnerAttachTab();
            });
        }

        if (businessOwnerIdUploadArea && !businessOwnerIdUploadArea.dataset.bound) {
            businessOwnerIdUploadArea.dataset.bound = '1';

            businessOwnerIdUploadArea.addEventListener('dragover', (e) => {
                e.preventDefault();
                businessOwnerIdUploadArea.classList.add('dragover');
            });
            businessOwnerIdUploadArea.addEventListener('dragleave', (e) => {
                e.preventDefault();
                businessOwnerIdUploadArea.classList.remove('dragover');
            });
            businessOwnerIdUploadArea.addEventListener('drop', (e) => {
                e.preventDefault();
                businessOwnerIdUploadArea.classList.remove('dragover');
                const inp = document.getElementById('business-owner-id-file-input');
                if (!inp) return;
                mergeBusinessOwnerPicks(inp, Array.from(e.dataTransfer.files));
                rebuildBusinessOwnerIdListUI();
                inp.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                showBusinessOwnerAttachTab();
            });
        }

    }

    /** Stable fingerprint for hidden JSON so harmless re-renders (key order, whitespace) do not rebuild lists. */
    function normalizeSavedUrlsFingerprint(jsonStr) {
        if (jsonStr == null || jsonStr === '') {
            return '';
        }
        try {
            const a = JSON.parse(jsonStr);
            if (!Array.isArray(a)) {
                return jsonStr;
            }
            const parts = a
                .map((item) => {
                    if (typeof item === 'string') {
                        return 's:' + item;
                    }
                    if (item && typeof item === 'object') {
                        return 'o:' + (item.path || '') + '\t' + (item.url || '');
                    }
                    return '';
                })
                .filter(Boolean);
            parts.sort();
            return parts.join('\n');
        } catch (e) {
            return jsonStr;
        }
    }

    function syncMorphSavedFingerprints() {
        const boHid = document.getElementById('business-owner-saved-urls-json');
        window.__vqLastBoSavedSnap = normalizeSavedUrlsFingerprint(boHid ? boHid.value : '');
        const insHid = document.getElementById('insurance-saved-urls-json');
        window.__vqLastInsSavedSnap = normalizeSavedUrlsFingerprint(insHid ? insHid.value : '');
    }

    /**
     * After Livewire morph (e.g. wire:model.live): do not re-run full init — that re-bound uploaders and
     * replaceChildren() on file lists on every keystroke and caused flicker. Only rebuild BO/insurance lists
     * when the hidden saved-URL JSON actually changed (remove file, new session data, etc.).
     */
    function afterLivewireMorphLight() {
        restoreFileItems();

        const boHid = document.getElementById('business-owner-saved-urls-json');
        const boFp = normalizeSavedUrlsFingerprint(boHid ? boHid.value : '');
        if (boFp !== window.__vqLastBoSavedSnap) {
            window.__vqLastBoSavedSnap = boFp;
            renderBusinessOwnerAttachListIfNeeded();
        } else {
            const boList = document.getElementById('business-owner-id-file-list');
            if (boList) updateNoFileMessage(boList);
        }

        const insHid = document.getElementById('insurance-saved-urls-json');
        const insFp = normalizeSavedUrlsFingerprint(insHid ? insHid.value : '');
        if (insFp !== window.__vqLastInsSavedSnap) {
            window.__vqLastInsSavedSnap = insFp;
            renderInsuranceAttachListIfNeeded();
        } else {
            const insList = document.getElementById('insurance-file-list');
            if (insList) updateNoFileMessage(insList);
        }
    }

    // ── Boot ──────────────────────────────────────────────────────────
    function initVerificationPage() {
        setupTabs();
        setupUploaders();
        restoreFileItems();
        renderInsuranceAttachListIfNeeded();
        renderBusinessOwnerAttachListIfNeeded();
        syncMorphSavedFingerprints();
    }

    document.addEventListener('DOMContentLoaded', initVerificationPage);
    document.addEventListener('livewire:navigated', initVerificationPage);

    let __vqMorphLightTimer = null;
    document.addEventListener('livewire:init', function() {
        Livewire.hook('morph.updated', function() {
            clearTimeout(__vqMorphLightTimer);
            __vqMorphLightTimer = setTimeout(function() {
                afterLivewireMorphLight();
            }, 100);
        });
    });


    // Global drag prevention (do not cancel drops on our upload zones — those handlers set input.files)
    function __vqIsFileUploadZoneTarget(el) {
        if (!el || !el.closest) {
            return false;
        }
        return Boolean(
            el.closest('.upload-area') ||
            el.closest('#insurance-upload-area') ||
            el.closest('#business-owner-id-upload-area') ||
            el.closest('#upload-area')
        );
    }
    document.addEventListener('dragover', function(e) {
        if (__vqIsFileUploadZoneTarget(e.target)) {
            return;
        }
        e.preventDefault();
    });
    document.addEventListener('drop', function(e) {
        if (__vqIsFileUploadZoneTarget(e.target)) {
            return;
        }
        e.preventDefault();
    });
</script>
