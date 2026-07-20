<?php

use App\Models\GroomerSpacerProfile;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new #[Layout('layouts.dashboard')] class extends Component {
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
    public bool $showStartEarningComplete = false;

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

    private bool $mergingBusinessOwnerIdUpload = false;
    private bool $mergingBusinessGalleryPending = false;

    private bool $mergingGovernmentIdUpload = false;

    private bool $mergingInsuranceCertificateUpload = false;

    /** @var array<string, list<UploadedFile|TemporaryUploadedFile>> */
    private array $pendingDocUploadSnapshots = [];

    /** Stored public-disk paths. */
    public array $business_gallery_paths = [];

    /** New gallery files before submit. */
    public array $business_gallery_pending = [];

    public $business_gallery_pick = null;

    private const GALLERY_MIN_VISIBLE_SLOTS = 3;

    /** Step 2A — Groomer business profile. */
    public string $groomer_experience = '';
    public string $groomer_specialties = '';
    public array $groomer_pet_specialties = [];
    public string $groomer_specialty_other = '';
    public array $groomer_pet_sizes = [];
    public string $groomer_service_input = '';
    public array $groomer_custom_services = [];
    public array $groomer_selected_services = [];
    public string $groomer_addon_input = '';
    public array $groomer_custom_addons = [];
    public array $groomer_selected_addons = [];

    /**
     * Service rows keyed by slug. Stored in groomer_business_profile.services.
     *
     * @var array<string, array{name: string, price: string, description: string}>
     */
    public array $groomer_services_pricing = [];

    /**
     * Add-on rows keyed by slug. Stored in groomer_business_profile.addon_pricing.
     *
     * @var array<string, array{name: string, price: string, description: string}>
     */
    public array $groomer_addon_pricing = [];

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
    /** Saved business-owner ID paths from DB (registered business). */
    public array $business_owner_id_paths = [];
    /** New business-owner ID file picks only — bound to the file input. */
    public $business_owner_id_images = [];

    /** Saved government ID paths from DB (freelance). */
    public array $government_id_paths = [];
    /** New government ID file picks only — bound to the file input. */
    public $government_id = [];

    /** Original upload filenames keyed by storage path (registered business ID). */
    public array $business_owner_id_file_names = [];

    /** Original upload filenames keyed by storage path (freelance government ID). */
    public array $government_id_file_names = [];

    /** Original upload filenames keyed by storage path (insurance). */
    public array $insurance_certificate_file_names = [];

    // Third form: Payout details
    public string $bank = '';
    public string $account_holder_name = '';
    public string $account_number = '';
    public string $sort_code = '';
    public string $iban = '';

    /** Freelance-only: stored in freelance_details JSON (service / home address). */
    public string $freelance_service_home_address_line1 = '';

    public string $freelance_service_home_address_line2 = '';

    /** User must check before Submit (stored in information_accuracy_confirmed). */
    public bool $information_accuracy_confirmed = false;

    /** When true, returning from Verification Notices highlights missing personal-step fields. */
    public bool $verification_review_mode = false;

    /**
     * Initialize component with user data
     */
    public function mount(): void
    {
        $this->loadExistingData();
        $this->scrollVerifyQualifyStepToTop();
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

        if (in_array($propertyName, ['fursgo_usage', 'account_type', 'location_types', 'full_name', 'business_email', 'business_name', 'business_registration_number', 'business_phone', 'freelance_service_home_address_line1', 'freelance_service_home_address_line2', 'business_owner_id_paths', 'business_owner_id_images', 'business_owner_id_file_names', 'government_id_paths', 'government_id', 'government_id_file_names', 'insurance_certificate_upload', 'insurance_certificate_paths', 'insurance_certificate_file_names', 'id_documents', 'account_holder_name', 'account_number', 'sort_code', 'iban', 'business_display_name', 'business_tagline', 'business_bio', 'groomer_experience', 'groomer_specialties', 'groomer_pet_specialties', 'groomer_specialty_other', 'groomer_pet_sizes', 'groomer_service_input', 'groomer_custom_services', 'groomer_selected_services', 'groomer_addon_input', 'groomer_custom_addons', 'groomer_selected_addons', 'groomer_services_pricing', 'groomer_addon_pricing'])) {
            // Don't reset file arrays when other properties change
            return;
        }

        if (in_array($propertyName, ['business_avatar_upload', 'business_gallery_pick', 'business_gallery_pending'], true)) {
            return;
        }

        if (in_array($propertyName, ['legal_terms_accepted', 'legal_privacy_accepted', 'legal_agreements_expanded'], true)) {
            return;
        }

        // Log the property that's being updated for debugging
        \Log::info('Property updated: ' . $propertyName);
    }

    public function updatedFursgoUsage($value): void
    {
        $this->fursgo_usage = $this->normalizeFursgoUsage((string) $value);
    }

    public function updatedAccountType($value): void
    {
        $this->account_type = (string) $value;
    }

    public function updatedLocationTypes($value): void
    {
        if (!is_array($this->location_types)) {
            $this->location_types = [];
        }
    }

    private function shouldBypassDevValidation(?\Illuminate\Contracts\Auth\Authenticatable $user = null): bool
    {
        $user = $user ?? Auth::guard('groomer_spacer')->user();
        $email = strtolower(trim((string) ($user->email ?? '')));

        return $email === 'dev@dev.com';
    }

    /**
     * Dev preview mode is enabled only when dev has DB-driven type selections.
     */
    private function shouldUseDevDbPreview(?\Illuminate\Contracts\Auth\Authenticatable $user = null): bool
    {
        $user = $user ?? Auth::guard('groomer_spacer')->user();
        if (!$user || !$this->shouldBypassDevValidation($user)) {
            return false;
        }

        $usage = $this->normalizeFursgoUsage((string) ($user->user_type ?? ''));
        $accountType = (string) ($user->account_type ?? '');

        return in_array($usage, ['groomer', 'space'], true) && in_array($accountType, ['registered_business', 'freelance'], true);
    }

    private function persistRealtimeVerificationTypeSelections(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        $table = $user->getTable();
        $payload = [];

        if (Schema::hasColumn($table, 'user_type') && in_array($this->fursgo_usage, ['groomer', 'space'], true)) {
            $payload['user_type'] = $this->normalizeFursgoUsage($this->fursgo_usage);
        }

        if (Schema::hasColumn($table, 'account_type') && in_array($this->account_type, ['registered_business', 'freelance'], true)) {
            $payload['account_type'] = $this->account_type;
        }

        if (Schema::hasColumn($table, 'select_location_type') && is_array($this->location_types)) {
            $payload['select_location_type'] = array_values(array_unique(array_filter($this->location_types, fn($value) => is_string($value) && in_array($value, ['space_visits', 'commercial_salon', 'home_studio', 'house_visit', 'mobile_van'], true))));
        }

        if ($payload !== []) {
            $user->update($payload);
        }
    }

    private function syncVerificationTypeSelectionsFromDb(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        $user->refresh();
        $table = $user->getTable();

        if (Schema::hasColumn($table, 'user_type')) {
            $dbUsage = $this->normalizeFursgoUsage((string) ($user->user_type ?? ''));
            if (in_array($dbUsage, ['groomer', 'space'], true)) {
                $this->fursgo_usage = $dbUsage;
            }
        }

        if (Schema::hasColumn($table, 'account_type')) {
            $dbAccountType = (string) ($user->account_type ?? '');
            if (in_array($dbAccountType, ['registered_business', 'freelance'], true)) {
                $this->account_type = $dbAccountType;
            }
        }
    }

    private function syncRealtimeVerificationComponentVisibility(): void
    {
        if (!$this->showAccountPayoutsForm) {
            return;
        }

        $hasValidUsage = in_array($this->fursgo_usage, ['groomer', 'space'], true);
        $hasValidAccount = in_array($this->account_type, ['registered_business', 'freelance'], true);
        $hasValidLocations = is_array($this->location_types) && count($this->location_types) > 0;
        $allowWithoutLocations = $this->shouldUseDevDbPreview();
        $canContinue = $hasValidUsage && $hasValidAccount && $hasValidLocations;
        if (!$canContinue && $allowWithoutLocations) {
            $canContinue = $hasValidUsage && $hasValidAccount;
        }

        if (!$canContinue) {
            $this->showRegisteredBusiness = false;
            $this->showFreelance = false;
            return;
        }
        // Keep the user on "Verify Your Account for Payouts" until they click Continue.
        // This prevents auto-advancing while they are still making multi-select changes.
        $this->showRegisteredBusiness = false;
        $this->showFreelance = false;
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
     * Resolve Verify & Qualify sub-step (Figma 2.3–2.7) from DB + session.
     *
     * @return 'background_checks'|'account_payouts'|'registered_business'|'freelance_groomer'|'verification_notices'|''
     */
    private function resolveVerificationCurrentStepFromDb(?\Illuminate\Contracts\Auth\Authenticatable $user, ?string $sessionStep): string
    {
        if (!$user instanceof GroomerSpacerProfile) {
            return 'background_checks';
        }

        if ($user->hasCompletedVerifyQualifyPersonalStep()) {
            return 'verification_notices';
        }

        $validSessionSteps = ['background_checks', 'account_payouts', 'registered_business', 'freelance_groomer', 'verification_notices'];
        if (in_array($sessionStep, $validSessionSteps, true)) {
            if (in_array($sessionStep, ['registered_business', 'freelance_groomer'], true)) {
                return $this->reconcilePersonalInfoSubstepWithDb($user, $sessionStep);
            }

            return $sessionStep;
        }

        // Fresh visit: always begin at Background Checks (do not skip ahead from saved DB fields).
        return 'background_checks';
    }

    private function resolveVerificationStatus(?GroomerSpacerProfile $user = null): string
    {
        $user = $user ?? Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return '';
        }

        if ($this->isFreelanceAccount($user)) {
            $details = $this->decodeProfileJson($user->freelance_details ?? null);
        } else {
            $details = $this->decodeProfileJson($user->business_details ?? null);
        }

        $status = strtolower(trim((string) ($details['verification_status'] ?? '')));

        if (in_array($status, ['approved', 'pending', 'rejected'], true)) {
            return $status;
        }

        if ($user->hasCompletedVerifyQualifyPersonalStep()) {
            return 'approved';
        }

        return '';
    }

    private function persistVerificationStatus(GroomerSpacerProfile $user, string $status): void
    {
        if (!in_array($status, ['approved', 'pending', 'rejected'], true)) {
            return;
        }

        if ($this->isFreelanceAccount($user)) {
            $existing = $user->freelance_details ?? [];
            if (!is_array($existing)) {
                $existing = is_string($existing) ? (json_decode($existing, true) ?: []) : [];
            }
            $user->update([
                'freelance_details' => array_merge($existing, ['verification_status' => $status]),
            ]);

            return;
        }

        $existing = $user->business_details ?? [];
        if (!is_array($existing)) {
            $existing = is_string($existing) ? (json_decode($existing, true) ?: []) : [];
        }
        $user->update([
            'business_details' => array_merge($existing, ['verification_status' => $status]),
        ]);
    }

    public function verificationIsApproved(): bool
    {
        if ((bool) session('verify_qualify_show_approved', false)) {
            return true;
        }

        return $this->resolveVerificationStatus() === 'approved';
    }

    /**
     * Map persisted account_type to the personal-info wizard sub-step.
     */
    private function personalInfoSubstepForAccountType(string $accountType): string
    {
        return $accountType === 'freelance' ? 'freelance_groomer' : 'registered_business';
    }

    /**
     * Always show the personal-info screen that matches DB account_type (not a stale session key).
     */
    private function reconcilePersonalInfoSubstepWithDb(GroomerSpacerProfile $user, string $step): string
    {
        $accountType = (string) ($user->account_type ?? '');
        if (!in_array($accountType, ['registered_business', 'freelance'], true)) {
            return $step;
        }

        return $this->personalInfoSubstepForAccountType($accountType);
    }

    /**
     * Show a single Verify & Qualify screen (2.3–2.7).
     */
    private function applyVerifyQualifySubstep(string $step): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if ($user instanceof GroomerSpacerProfile && in_array($step, ['registered_business', 'freelance_groomer'], true)) {
            $step = $this->reconcilePersonalInfoSubstepWithDb($user, $step);
            $this->syncVerificationTypeSelectionsFromDb();
            $this->hydratePersonalInfoFieldsFromDb($user);
        }

        $this->showVerificationStatus = false;
        $this->showBusinessBasicsForm = false;
        $this->showGroomerBusinessProfileForm = false;
        $this->showSpacerBusinessProfileForm = false;
        $this->showLegalPolicyForm = false;
        $this->showStartEarningComplete = false;
        $this->legal_agreements_expanded = false;
        $this->showVerificationCard = false;
        $this->showAccountPayoutsForm = false;
        $this->showRegisteredBusiness = false;
        $this->showFreelance = false;

        switch ($step) {
            case 'background_checks':
                $this->showVerificationCard = true;
                break;
            case 'account_payouts':
                $this->showAccountPayoutsForm = true;
                break;
            case 'registered_business':
                $this->showRegisteredBusiness = true;
                break;
            case 'freelance_groomer':
                $this->showFreelance = true;
                break;
            case 'verification_notices':
                $this->showVerificationStatus = true;
                break;
        }

        session(['verification_current_step' => $step]);
        session()->save();
        $this->scrollVerifyQualifyStepToTop();
    }

    private function scrollVerifyQualifyStepToTop(): void
    {
        $this->js('window.__vqRequestStepScrollToTop?.()');
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
        if (!$user instanceof GroomerSpacerProfile) {
            $this->applyVerifyQualifySubstep('background_checks');

            return;
        }

        // Load existing verification data (normalize so "Space", "spacer", etc. match wizard branches)
        $this->fursgo_usage = $this->normalizeFursgoUsage($user->user_type ?? '');
        $this->account_type = $user->account_type ?? '';
        $this->location_types = $user->select_location_type ?? [];

        $this->restoreVerificationViewState($user);
    }

    /**
     * Restore wizard screen from session (and DB fallbacks) after a full page reload.
     */
    private function restoreVerificationViewState(GroomerSpacerProfile $user): void
    {
        $this->verification_review_mode = (bool) session('verification_review_mode', false);

        if ((bool) session('verification_build_profile_step', false)) {
            $validBuildSubsteps = ['business_basics', 'groomer_profile', 'spacer_profile', 'legal_policy', 'start_grooming'];
            $sessionSub = (string) session('verification_build_profile_substep', '');

            if (in_array($sessionSub, $validBuildSubsteps, true)) {
                $substep = $this->coerceBuildProfileSubstepToUserType($user, $sessionSub);
            } else {
                $substep = $this->inferVerificationBuildProfileSubstep($user);
            }

            if ($substep === 'start_grooming' && !$user->legal_policy_agreements && !$this->shouldUseDevDbPreview($user)) {
                $substep = 'legal_policy';
            }

            session(['verification_build_profile_step' => true]);
            $this->setBuildProfileSubstep($substep);
            $this->applyBuildProfileSubstepUi($user, $substep);

            return;
        }

        $sessionStep = session('verification_current_step');
        $sessionStep = is_string($sessionStep) && $sessionStep !== '' ? $sessionStep : null;
        $step = $this->resolveVerificationCurrentStepFromDb($user, $sessionStep);
        $this->applyVerifyQualifySubstep($step);

        if ($this->verification_review_mode && in_array($step, ['registered_business', 'freelance_groomer'], true)) {
            $this->highlightPersonalStepValidationErrors($user);
        }
    }

    /**
     * Decode a profile JSON column to an array.
     */
    private function decodeProfileJson(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (!is_string($value) || trim($value) === '') {
            return [];
        }

        $decoded = json_decode($value, true);

        return is_array($decoded) ? $decoded : [];
    }

    /**
     * @param  array<string, mixed>|null  $value
     * @return array<string, string>
     */
    private function fileNamesMapFromJson(?array $value): array
    {
        if (!is_array($value)) {
            return [];
        }

        $map = [];
        foreach ($value as $path => $name) {
            if (!is_string($path) || $path === '' || !is_string($name) || trim($name) === '') {
                continue;
            }
            $map[$path] = trim($name);
        }

        return $map;
    }

    private function normalizeStoragePathForLookup(string $path): string
    {
        return ltrim(str_replace('\\', '/', trim($path)), '/');
    }

    private function storagePathMatches(string $left, string $right): bool
    {
        return $this->normalizeStoragePathForLookup($left) === $this->normalizeStoragePathForLookup($right);
    }

    /**
     * @param  list<string>  $paths
     */
    private function pathExistsInStoredList(string $path, array $paths): bool
    {
        foreach ($paths as $stored) {
            if (is_string($stored) && $this->storagePathMatches($path, $stored)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<string>  $paths
     * @return list<string>
     */
    private function removeStoredPathFromList(array $paths, string $path): array
    {
        return array_values(array_filter($paths, fn($stored) => !is_string($stored) || !$this->storagePathMatches($path, $stored)));
    }

    /**
     * @param  array<string, string>  $fileNames
     * @return array<string, string>
     */
    private function removeFileNameForStoredPath(array $fileNames, string $path): array
    {
        $filtered = [];
        foreach ($fileNames as $storedPath => $name) {
            if ($this->storagePathMatches((string) $storedPath, $path)) {
                continue;
            }
            $filtered[$storedPath] = $name;
        }

        return $filtered;
    }

    /**
     * @param  list<string>  $paths
     */
    private function resolveStoredPublicDiskPath(string $path, array $paths): string
    {
        foreach ($paths as $stored) {
            if (is_string($stored) && $this->storagePathMatches($path, $stored)) {
                return $stored;
            }
        }

        return $path;
    }

    private function pendingUploadMatchesRemoval(UploadedFile|TemporaryUploadedFile $item, string $filename, int $size): bool
    {
        if (trim($item->getClientOriginalName()) !== $filename) {
            return false;
        }

        if ($size > 0 && (int) $item->getSize() !== $size) {
            return false;
        }

        return true;
    }

    /**
     * @param  list<string>  $paths
     * @return list<string>
     */
    private function uniqueStoredPaths(array $paths): array
    {
        $out = [];
        $seen = [];
        foreach ($paths as $path) {
            if (!is_string($path) || $path === '') {
                continue;
            }
            $key = $this->normalizeStoragePathForLookup($path);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $path;
        }

        return array_values($out);
    }

    /**
     * Keep one stored path per original display filename (latest path wins).
     *
     * @param  list<string>  $paths
     * @param  array<string, string>  $fileNames
     * @return list<string>
     */
    private function uniquePathsByOriginalName(array $paths, array $fileNames): array
    {
        $paths = $this->uniqueStoredPaths($paths);
        $out = [];
        $seenNames = [];

        foreach (array_reverse($paths) as $path) {
            if (!is_string($path) || $path === '') {
                continue;
            }

            $displayName = strtolower(trim($this->displayFileNameForPath($path, $fileNames)));
            if ($displayName !== '') {
                if (isset($seenNames[$displayName])) {
                    continue;
                }
                $seenNames[$displayName] = true;
            }

            array_unshift($out, $path);
        }

        return array_values($out);
    }

    private function uploadFingerprint(UploadedFile|TemporaryUploadedFile $file): string
    {
        return strtolower(trim($file->getClientOriginalName())) . '|' . $file->getSize();
    }

    private function originalNameAlreadyStored(string $originalName, array $fileNames): bool
    {
        $needle = strtolower(trim($originalName));
        if ($needle === '') {
            return false;
        }

        foreach ($fileNames as $name) {
            if (strtolower(trim((string) $name)) === $needle) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param  list<string>  $paths
     * @param  array<string, string>  $fileNames
     * @return array{paths: list<string>, fileNames: array<string, string>, storedPath: ?string}
     */
    private function storeDocUploadIfNew(array $paths, array $fileNames, UploadedFile|TemporaryUploadedFile $file, string $storageKey): array
    {
        $originalName = trim($file->getClientOriginalName());
        if ($this->originalNameAlreadyStored($originalName, $fileNames)) {
            return [
                'paths' => $paths,
                'fileNames' => $fileNames,
                'storedPath' => null,
            ];
        }

        $dir = $this->storageDirectoryForUpload($file, $storageKey);
        $path = $file->store($dir, 'public');
        $paths[] = $path;
        if ($originalName !== '') {
            $fileNames[$path] = $originalName;
        }

        return [
            'paths' => $paths,
            'fileNames' => $fileNames,
            'storedPath' => $path,
        ];
    }

    /**
     * @return list<UploadedFile|TemporaryUploadedFile>
     */
    private function pendingInsuranceCertificateUploads(): array
    {
        $items = is_array($this->insurance_certificate_upload ?? null) ? $this->insurance_certificate_upload : [];

        return array_values(array_filter($items, fn($item) => $item instanceof UploadedFile || $item instanceof TemporaryUploadedFile));
    }

    /**
     * @param  list<UploadedFile|TemporaryUploadedFile>  $uploads
     * @return list<UploadedFile|TemporaryUploadedFile>
     */
    private function uniquePendingUploads(array $uploads): array
    {
        $out = [];
        $seen = [];

        foreach ($uploads as $file) {
            $key = $this->uploadFingerprint($file);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $out[] = $file;
        }

        return $out;
    }

    /**
     * @param  list<UploadedFile|TemporaryUploadedFile>  $uploads
     * @param  array<string, string>  $storedFileNames
     * @return list<UploadedFile|TemporaryUploadedFile>
     */
    private function filterPendingUploadsNotStored(array $uploads, array $storedFileNames): array
    {
        return array_values(
            array_filter($uploads, function ($file) use ($storedFileNames) {
                if (!($file instanceof UploadedFile || $file instanceof TemporaryUploadedFile)) {
                    return false;
                }

                return !$this->originalNameAlreadyStored((string) $file->getClientOriginalName(), $storedFileNames);
            }),
        );
    }

    /**
     * @param  list<UploadedFile|TemporaryUploadedFile>  $prev
     * @param  array<string, string>  $storedFileNames
     * @return list<UploadedFile|TemporaryUploadedFile>
     */
    private function reconcilePendingDocUploads(array $prev, mixed $incoming, array $storedFileNames): array
    {
        $byFingerprint = [];

        foreach ($prev as $file) {
            if ($file instanceof UploadedFile || $file instanceof TemporaryUploadedFile) {
                $byFingerprint[$this->uploadFingerprint($file)] = $file;
            }
        }

        $incomingList = is_array($incoming) ? $incoming : ($incoming instanceof UploadedFile || $incoming instanceof TemporaryUploadedFile ? [$incoming] : []);

        foreach ($incomingList as $file) {
            if ($file instanceof UploadedFile || $file instanceof TemporaryUploadedFile) {
                $byFingerprint[$this->uploadFingerprint($file)] = $file;
            }
        }

        return $this->filterPendingUploadsNotStored(array_values($byFingerprint), $storedFileNames);
    }

    /**
     * @param  array<string, string>  $fileNames
     */
    private function displayFileNameForPath(string $path, array $fileNames): string
    {
        $direct = trim((string) ($fileNames[$path] ?? ''));
        if ($direct !== '') {
            return $direct;
        }

        $normalized = $this->normalizeStoragePathForLookup($path);
        foreach ($fileNames as $storedPath => $name) {
            if ($this->normalizeStoragePathForLookup((string) $storedPath) === $normalized) {
                $match = trim((string) $name);

                return $match !== '' ? $match : '';
            }
        }

        return '';
    }

    /**
     * @param  array<string, string>  $fileNames
     * @return array{path: string, url: string, name?: string}
     */
    private function savedDocUploadEntry(string $path, string $routeName, array $fileNames = []): array
    {
        $entry = [
            'path' => $path,
            'url' => route($routeName, ['t' => Crypt::encryptString($path)]),
        ];

        $displayName = $this->displayFileNameForPath($path, $fileNames);
        if ($displayName !== '') {
            $entry['name'] = $displayName;
        }

        return $entry;
    }

    /**
     * @param  list<string>  $paths
     * @param  array<string, string>  $fileNames
     * @return list<array{path: string, url: string, name?: string}>
     */
    private function savedDocUploadEntriesForPaths(array $paths, array $fileNames, string $routeName): array
    {
        $entries = [];
        $seen = [];
        foreach ($paths as $path) {
            if (!is_string($path) || $path === '') {
                continue;
            }
            $key = $this->normalizeStoragePathForLookup($path);
            if (isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $entries[] = $this->savedDocUploadEntry($path, $routeName, $fileNames);
        }

        return $entries;
    }

    /**
     * @param  array<string, string>  $fileNames
     * @param  list<string>  $paths
     * @return array<string, string>
     */
    private function intersectFileNamesWithPaths(array $fileNames, array $paths): array
    {
        if ($fileNames === [] || $paths === []) {
            return [];
        }

        $allowed = [];
        foreach ($paths as $path) {
            if (!is_string($path) || $path === '') {
                continue;
            }
            $allowed[$this->normalizeStoragePathForLookup($path)] = true;
        }

        $filtered = [];
        foreach ($fileNames as $storedPath => $name) {
            if (!is_string($storedPath) || $storedPath === '' || !is_string($name) || trim($name) === '') {
                continue;
            }
            if (isset($allowed[$this->normalizeStoragePathForLookup($storedPath)])) {
                $filtered[$storedPath] = trim($name);
            }
        }

        return $filtered;
    }

    /**
     * Load personal-info form fields from the JSON column that matches account_type.
     */
    private function hydratePersonalInfoFieldsFromDb(GroomerSpacerProfile $user): void
    {
        $this->full_name = $user->full_name ?? '';
        $this->information_accuracy_confirmed = (bool) ($user->information_accuracy_confirmed ?? false);

        $businessDetails = $this->decodeProfileJson($user->business_details ?? null);
        $freelanceDetails = $this->decodeProfileJson($user->freelance_details ?? null);
        $payoutDetails = $this->decodeProfileJson($user->payout_details ?? null);
        $insuranceDetails = $this->decodeProfileJson($user->insurance_details ?? null);

        if ($this->isFreelanceAccount($user)) {
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
            $this->business_phone = (string) ($freelanceDetails['contact_phone'] ?? '');
            $this->government_id_paths = GroomerSpacerProfile::governmentIdPathsFromFreelanceDetails($freelanceDetails);
            $this->government_id = [];
            $this->government_id_file_names = $this->intersectFileNamesWithPaths($this->fileNamesMapFromJson(is_array($freelanceDetails['government_id_file_names'] ?? null) ? $freelanceDetails['government_id_file_names'] : []), $this->government_id_paths);
            $this->government_id_paths = $this->uniquePathsByOriginalName($this->government_id_paths, $this->government_id_file_names);
            $this->business_owner_id_paths = [];
            $this->business_owner_id_images = [];
            $this->business_owner_id_file_names = [];
        } else {
            $this->freelance_service_home_address_line1 = '';
            $this->freelance_service_home_address_line2 = '';
            $this->government_id_paths = [];
            $this->government_id = [];
            $this->government_id_file_names = [];
            $this->business_email = trim((string) ($businessDetails['business_email'] ?? ''));
            $this->business_name = (string) ($businessDetails['business_name'] ?? '');
            $this->business_registration_number = (string) ($businessDetails['business_registration_number'] ?? '');
            $this->business_phone = (string) ($businessDetails['business_phone'] ?? '');
            $this->business_owner_id_paths = $this->uniqueStoredPaths(GroomerSpacerProfile::businessOwnerIdPathsFromBusinessDetails($businessDetails));
            if ($this->business_owner_id_paths === []) {
                $legacyIdPaths = is_array($user->id_document_paths ?? null) ? $user->id_document_paths : [];
                $this->business_owner_id_paths = $this->uniqueStoredPaths(array_values(array_filter($legacyIdPaths, fn($path) => is_string($path) && $path !== '')));
            }
            $this->business_owner_id_images = [];
            $this->business_owner_id_file_names = $this->intersectFileNamesWithPaths($this->fileNamesMapFromJson(is_array($businessDetails['business_owner_id_file_names'] ?? null) ? $businessDetails['business_owner_id_file_names'] : []), $this->business_owner_id_paths);
            $this->business_owner_id_paths = $this->uniquePathsByOriginalName($this->business_owner_id_paths, $this->business_owner_id_file_names);
        }

        $this->bank = (string) ($payoutDetails['bank'] ?? '');
        $this->account_holder_name = (string) ($payoutDetails['account_holder_name'] ?? '');
        $this->account_number = (string) ($payoutDetails['account_number'] ?? '');
        $this->sort_code = (string) ($payoutDetails['sort_code'] ?? '');
        $this->iban = (string) ($payoutDetails['iban'] ?? '');

        $this->id_documents = [];
        $rawInsPaths = $insuranceDetails['insurance_certificate_paths'] ?? [];
        $this->insurance_certificate_paths = is_array($rawInsPaths) ? $this->uniqueStoredPaths(array_filter($rawInsPaths, fn($p) => is_string($p) && $p !== '')) : [];
        $this->insurance_certificate_file_names = $this->intersectFileNamesWithPaths($this->fileNamesMapFromJson(is_array($insuranceDetails['insurance_certificate_file_names'] ?? null) ? $insuranceDetails['insurance_certificate_file_names'] : []), $this->insurance_certificate_paths);
        $this->insurance_certificate_paths = $this->uniquePathsByOriginalName($this->insurance_certificate_paths, $this->insurance_certificate_file_names);
        $this->insurance_certificate_upload = [];
    }

    /**
     * Handle ID document uploads
     */
    public function updatedIdDocuments($files)
    {
        $this->id_documents = $files;
    }

    /**
     * @return list<string>
     */
    private function storedBusinessOwnerIdPathsForDisplay(): array
    {
        return array_values(array_filter(is_array($this->business_owner_id_paths) ? $this->business_owner_id_paths : [], fn($path) => is_string($path) && $path !== ''));
    }

    /**
     * @return list<string>
     */
    private function storedGovernmentIdPathsForDisplay(): array
    {
        return array_values(array_filter(is_array($this->government_id_paths) ? $this->government_id_paths : [], fn($path) => is_string($path) && $path !== ''));
    }

    /**
     * @return list<\Illuminate\Http\UploadedFile|Livewire\Features\SupportFileUploads\TemporaryUploadedFile>
     */
    private function pendingBusinessOwnerIdUploads(): array
    {
        return array_values(array_filter(is_array($this->business_owner_id_images) ? $this->business_owner_id_images : [], fn($item) => $item instanceof UploadedFile || $item instanceof TemporaryUploadedFile));
    }

    /**
     * @return list<\Illuminate\Http\UploadedFile|Livewire\Features\SupportFileUploads\TemporaryUploadedFile>
     */
    private function pendingGovernmentIdUploads(): array
    {
        return array_values(array_filter(is_array($this->government_id) ? $this->government_id : [], fn($item) => $item instanceof UploadedFile || $item instanceof TemporaryUploadedFile));
    }

    /**
     * @return list<UploadedFile|TemporaryUploadedFile>
     */
    private function pendingUploadItemsFromProperty(string $property): array
    {
        $items = $this->{$property} ?? [];
        if (!is_array($items)) {
            $items = $items instanceof UploadedFile || $items instanceof TemporaryUploadedFile ? [$items] : [];
        }

        return array_values(array_filter($items, fn($item) => $item instanceof UploadedFile || $item instanceof TemporaryUploadedFile));
    }

    private function snapshotPendingDocUploadProperty(string $property): void
    {
        $this->pendingDocUploadSnapshots[$property] = $this->pendingUploadItemsFromProperty($property);
    }

    /**
     * @return list<UploadedFile|TemporaryUploadedFile>
     */
    private function consumePendingDocUploadSnapshot(string $property): array
    {
        $prev = $this->pendingDocUploadSnapshots[$property] ?? [];
        unset($this->pendingDocUploadSnapshots[$property]);

        return is_array($prev) ? $prev : [];
    }

    public function updatingBusinessOwnerIdImages($value): void
    {
        if (!$this->mergingBusinessOwnerIdUpload) {
            $this->snapshotPendingDocUploadProperty('business_owner_id_images');
        }
    }

    /**
     * Handle business owner ID images uploads — keep existing stored paths when new files are picked.
     */
    public function updatedBusinessOwnerIdImages($value): void
    {
        if ($this->mergingBusinessOwnerIdUpload) {
            return;
        }

        $this->mergingBusinessOwnerIdUpload = true;

        try {
            $prev = $this->consumePendingDocUploadSnapshot('business_owner_id_images');
            $this->business_owner_id_images = $this->reconcilePendingDocUploads($prev, $value, $this->business_owner_id_file_names);
        } finally {
            $this->mergingBusinessOwnerIdUpload = false;
        }
    }

    public function updatingGovernmentId($value): void
    {
        if (!$this->mergingGovernmentIdUpload) {
            $this->snapshotPendingDocUploadProperty('government_id');
        }
    }

    /**
     * Handle freelance government ID uploads — keep existing stored paths when new files are picked.
     */
    public function updatedGovernmentId($value): void
    {
        if ($this->mergingGovernmentIdUpload) {
            return;
        }

        $this->mergingGovernmentIdUpload = true;

        try {
            $prev = $this->consumePendingDocUploadSnapshot('government_id');
            $this->government_id = $this->reconcilePendingDocUploads($prev, $value, $this->government_id_file_names);
        } finally {
            $this->mergingGovernmentIdUpload = false;
        }
    }

    public function updatingInsuranceCertificateUpload($value): void
    {
        if (!$this->mergingInsuranceCertificateUpload) {
            $this->snapshotPendingDocUploadProperty('insurance_certificate_upload');
        }
    }

    /**
     * Handle insurance certificate uploads — keep existing stored paths and prior temp uploads.
     */
    public function updatedInsuranceCertificateUpload($value): void
    {
        if ($this->mergingInsuranceCertificateUpload) {
            return;
        }

        $this->mergingInsuranceCertificateUpload = true;

        try {
            $prev = $this->consumePendingDocUploadSnapshot('insurance_certificate_upload');
            $this->insurance_certificate_upload = $this->reconcilePendingDocUploads($prev, $value, $this->insurance_certificate_file_names);
        } finally {
            $this->mergingInsuranceCertificateUpload = false;
        }
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

        $idPaths = [];
        $rawId = $user->id_document_paths ?? null;
        if (is_array($rawId)) {
            $idPaths = $rawId;
        } elseif (is_string($rawId) && $rawId !== '') {
            $idPaths = json_decode($rawId, true) ?: [];
        }
        $pathInIdDocuments = $this->pathExistsInStoredList($path, $idPaths);

        if ($isFreelance) {
            $freelanceDetails = $user->freelance_details ?? [];
            if (!is_array($freelanceDetails)) {
                $freelanceDetails = is_string($freelanceDetails) ? (json_decode($freelanceDetails, true) ?: []) : [];
            }
            $images = GroomerSpacerProfile::governmentIdPathsFromFreelanceDetails($freelanceDetails);
            $pathInFreelance = $this->pathExistsInStoredList($path, $images);
            if (!$pathInFreelance && !$pathInIdDocuments) {
                return;
            }
            if ($pathInFreelance) {
                $freelanceDetails['government_id'] = $this->removeStoredPathFromList($images, $path);
                unset($freelanceDetails['id_verification_images']);
                $fileNames = $this->fileNamesMapFromJson(is_array($freelanceDetails['government_id_file_names'] ?? null) ? $freelanceDetails['government_id_file_names'] : []);
                $freelanceDetails['government_id_file_names'] = $this->intersectFileNamesWithPaths($this->removeFileNameForStoredPath($fileNames, $path), $freelanceDetails['government_id']);
            }
        } else {
            $businessDetails = $user->business_details ?? [];
            if (!is_array($businessDetails)) {
                $businessDetails = is_string($businessDetails) ? (json_decode($businessDetails, true) ?: []) : [];
            }

            $images = $businessDetails['business_owner_id_images'] ?? [];
            if (!is_array($images)) {
                $images = [];
            }
            $pathInBusiness = $this->pathExistsInStoredList($path, $images);
            if (!$pathInBusiness && !$pathInIdDocuments) {
                return;
            }
            if ($pathInBusiness) {
                $businessDetails['business_owner_id_images'] = $this->removeStoredPathFromList($images, $path);
                $fileNames = $this->fileNamesMapFromJson(is_array($businessDetails['business_owner_id_file_names'] ?? null) ? $businessDetails['business_owner_id_file_names'] : []);
                $businessDetails['business_owner_id_file_names'] = $this->intersectFileNamesWithPaths($this->removeFileNameForStoredPath($fileNames, $path), $businessDetails['business_owner_id_images']);
            }
        }

        $diskCandidates = $idPaths;
        if (isset($images) && is_array($images)) {
            $diskCandidates = array_merge($diskCandidates, $images);
        }
        $diskCandidates = array_merge($diskCandidates, is_array($this->business_owner_id_paths ?? null) ? $this->business_owner_id_paths : [], is_array($this->government_id_paths ?? null) ? $this->government_id_paths : []);
        $diskPath = $this->resolveStoredPublicDiskPath($path, $diskCandidates);

        $this->business_owner_id_paths = $this->removeStoredPathFromList($this->business_owner_id_paths ?? [], $path);

        $this->government_id_paths = $this->removeStoredPathFromList($this->government_id_paths ?? [], $path);

        $this->business_owner_id_file_names = $this->intersectFileNamesWithPaths($this->removeFileNameForStoredPath($this->business_owner_id_file_names, $path), $this->business_owner_id_paths);
        $this->government_id_file_names = $this->intersectFileNamesWithPaths($this->removeFileNameForStoredPath($this->government_id_file_names, $path), $this->government_id_paths);

        $this->business_owner_id_images = array_values(
            array_filter($this->business_owner_id_images ?? [], function ($item) use ($path) {
                if ($item instanceof TemporaryUploadedFile || $item instanceof UploadedFile) {
                    return true;
                }

                return !(is_string($item) && $this->storagePathMatches($item, $path));
            }),
        );

        $this->government_id = array_values(
            array_filter($this->government_id ?? [], function ($item) use ($path) {
                if ($item instanceof TemporaryUploadedFile || $item instanceof UploadedFile) {
                    return true;
                }

                return !(is_string($item) && $this->storagePathMatches($item, $path));
            }),
        );

        $idPaths = $this->removeStoredPathFromList($idPaths, $path);

        $this->id_documents = array_values(array_filter($this->id_documents ?? [], fn($item) => !(is_string($item) && $this->storagePathMatches($item, $path))));

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

        if (Storage::disk('public')->exists($diskPath)) {
            Storage::disk('public')->delete($diskPath);
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
        if (!is_array($paths) || !$this->pathExistsInStoredList($path, $paths)) {
            return;
        }

        $diskPath = $this->resolveStoredPublicDiskPath($path, $paths);

        $insuranceDetails['insurance_certificate_paths'] = $this->removeStoredPathFromList($paths, $path);
        $fileNames = $this->fileNamesMapFromJson(is_array($insuranceDetails['insurance_certificate_file_names'] ?? null) ? $insuranceDetails['insurance_certificate_file_names'] : []);
        $insuranceDetails['insurance_certificate_file_names'] = $this->intersectFileNamesWithPaths($this->removeFileNameForStoredPath($fileNames, $path), $insuranceDetails['insurance_certificate_paths']);

        $this->insurance_certificate_paths = $this->removeStoredPathFromList($this->insurance_certificate_paths ?? [], $path);
        $this->insurance_certificate_file_names = $this->intersectFileNamesWithPaths($this->removeFileNameForStoredPath($this->insurance_certificate_file_names, $path), $this->insurance_certificate_paths);
        $this->insurance_certificate_upload = array_values(
            array_filter($this->insurance_certificate_upload ?? [], function ($item) use ($path) {
                if ($item instanceof TemporaryUploadedFile || $item instanceof UploadedFile) {
                    return true;
                }

                return !(is_string($item) && $this->storagePathMatches($item, $path));
            }),
        );

        $user->update(['insurance_details' => $insuranceDetails]);

        if (Storage::disk('public')->exists($diskPath)) {
            Storage::disk('public')->delete($diskPath);
        }
    }

    /**
     * Handle business verification submission
     */
    public function submit(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user) {
            return;
        }

        $isDevBypass = $this->shouldUseDevDbPreview($user);
        if (!$isDevBypass) {
            $this->validate([
                'fursgo_usage' => ['required', 'string', 'in:groomer,space'],
                'account_type' => ['required', 'string', 'in:registered_business,freelance'],
                'location_types' => ['required', 'array', 'min:1'],
                'location_types.*' => ['string', 'in:space_visits,commercial_salon,home_studio,house_visit,mobile_van'],
            ]);
        }

        $usageForSave = in_array($this->fursgo_usage, ['groomer', 'space'], true) ? $this->normalizeFursgoUsage($this->fursgo_usage) : $this->normalizeFursgoUsage((string) ($user->user_type ?? 'groomer'));
        if (!in_array($usageForSave, ['groomer', 'space'], true)) {
            $usageForSave = 'groomer';
        }

        $accountTypeForSave = in_array($this->account_type, ['registered_business', 'freelance'], true) ? $this->account_type : (string) ($user->account_type ?? 'registered_business');
        if (!in_array($accountTypeForSave, ['registered_business', 'freelance'], true)) {
            $accountTypeForSave = 'registered_business';
        }

        $locationTypesForSave = is_array($this->location_types) ? array_values(array_filter($this->location_types, fn($value) => is_string($value) && in_array($value, ['space_visits', 'commercial_salon', 'home_studio', 'house_visit', 'mobile_van'], true))) : [];
        if ($locationTypesForSave === [] && is_array($user->select_location_type ?? null)) {
            $locationTypesForSave = array_values($user->select_location_type);
        }

        // Update user profile with verification data
        $user->update([
            'user_type' => $usageForSave,
            'account_type' => $accountTypeForSave,
            'select_location_type' => $locationTypesForSave,
        ]);

        session()->forget(['verify_qualify_show_approved', 'verification_review_mode']);
        $this->verification_review_mode = false;

        $this->applyVerifyQualifySubstep($this->personalInfoSubstepForAccountType($accountTypeForSave));
    }

    /**
     * Check if form is valid for enabling submit button
     */
    public function isFormValid(): bool
    {
        if ($this->shouldUseDevDbPreview()) {
            return true;
        }

        return $this->fursgo_usage && $this->account_type && count($this->location_types) > 0;
    }

    public function verifyBusiness(): void
    {
        $this->verification_review_mode = false;
        session()->forget('verification_review_mode');
        $this->applyVerifyQualifySubstep('account_payouts');
    }

    /**
     * Handle back button — previous screen in Figma order (2.6/2.5 → 2.4 → 2.3).
     */
    public function goBack(): void
    {
        if ($this->showStartEarningComplete) {
            $this->goBackFromStartGroomingComplete();

            return;
        }

        if ($this->showVerificationStatus) {
            $this->goBackFromVerificationNotices();

            return;
        }

        if ($this->showLegalPolicyForm || $this->showGroomerBusinessProfileForm || $this->showSpacerBusinessProfileForm || $this->showBusinessBasicsForm) {
            $this->goBackFromBuildProfile();

            return;
        }

        if ($this->showRegisteredBusiness || $this->showFreelance) {
            $this->verification_review_mode = false;
            session()->forget('verification_review_mode');
            $this->applyVerifyQualifySubstep('account_payouts');

            return;
        }

        if ($this->showAccountPayoutsForm) {
            $this->applyVerifyQualifySubstep('background_checks');
        }
    }

    /**
     * Back within Build Your Profile (business basics → groomer/spacer → legal).
     */
    public function goBackFromBuildProfile(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        if ($this->showLegalPolicyForm) {
            $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
            $sub = $usage === 'space' ? 'spacer_profile' : 'groomer_profile';
            session(['verification_build_profile_step' => true]);
            $this->setBuildProfileSubstep($sub);
            $this->applyBuildProfileSubstepUi($user, $sub);

            return;
        }

        if ($this->showGroomerBusinessProfileForm || $this->showSpacerBusinessProfileForm) {
            session(['verification_build_profile_step' => true]);
            $this->setBuildProfileSubstep('business_basics');
            $this->enterBusinessBasicsStep($user, true);

            return;
        }

        if ($this->showBusinessBasicsForm) {
            if ($user->hasCompletedVerifyQualifyPersonalStep()) {
                session()->forget(['verification_build_profile_step', 'verification_build_profile_substep']);
                $this->applyVerifyQualifySubstep('verification_notices');
            } else {
                $this->applyVerifyQualifySubstep('account_payouts');
            }

            return;
        }
    }

    /**
     * Back from step 4 completion screen → Legal & Policy.
     */
    public function goBackFromStartGroomingComplete(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        session(['verification_build_profile_step' => true]);
        $this->setBuildProfileSubstep('legal_policy');
        $this->applyBuildProfileSubstepUi($user, 'legal_policy');
    }

    /**
     * Back from Verification Notices → last personal-info screen (DB account_type).
     */
    public function goBackFromVerificationNotices(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            $this->applyVerifyQualifySubstep('background_checks');

            return;
        }

        $accountType = (string) ($user->account_type ?? '');
        if ($accountType === 'freelance') {
            $this->applyVerifyQualifySubstep('freelance_groomer');
        } elseif ($accountType === 'registered_business') {
            $this->applyVerifyQualifySubstep('registered_business');
        } else {
            $this->applyVerifyQualifySubstep('account_payouts');
        }
    }

    /**
     * From Verification Notices (not approved) — return to 2.5/2.6 with validation hints.
     */
    public function reviewSubmission(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        $this->verification_review_mode = true;
        session(['verification_review_mode' => true]);
        session()->forget('verify_qualify_show_approved');

        $accountType = (string) ($user->account_type ?? '');
        $step = in_array($accountType, ['registered_business', 'freelance'], true) ? $this->personalInfoSubstepForAccountType($accountType) : ($this->isFreelanceAccount($user) ? 'freelance_groomer' : 'registered_business');
        $this->applyVerifyQualifySubstep($step);
        $this->highlightPersonalStepValidationErrors($user);
    }

    private function highlightPersonalStepValidationErrors(GroomerSpacerProfile $user): void
    {
        if ($this->shouldUseDevDbPreview($user)) {
            return;
        }

        $rules = $this->isFreelanceAccount($user)
            ? [
                'full_name' => ['required', 'string', 'max:255'],
                'business_email' => ['required', 'email', 'max:255'],
                'business_phone' => ['required', 'string', 'max:50'],
                'account_holder_name' => ['required', 'string', 'max:255'],
                'account_number' => ['required', 'string', 'max:50'],
                'sort_code' => ['required', 'string', 'max:20'],
                'iban' => ['required', 'string', 'max:50'],
                'information_accuracy_confirmed' => ['accepted'],
            ]
            : [
                'full_name' => ['required', 'string', 'max:255'],
                'business_email' => ['required', 'email', 'max:255'],
                'business_name' => ['required', 'string', 'max:255'],
                'business_registration_number' => ['required', 'string', 'max:100'],
                'business_phone' => ['required', 'string', 'max:50'],
                'account_holder_name' => ['required', 'string', 'max:255'],
                'account_number' => ['required', 'string', 'max:50'],
                'sort_code' => ['required', 'string', 'max:20'],
                'iban' => ['required', 'string', 'max:50'],
                'information_accuracy_confirmed' => ['accepted'],
            ];

        try {
            $this->validate($rules);
        } catch (\Illuminate\Validation\ValidationException) {
            // Surface field errors on the review screen.
        }

        if (!$this->isPersonalInfoFormValid()) {
            $idUploads = $this->isFreelanceAccount($user) ? $this->storedGovernmentIdPathsForDisplay() : $this->storedBusinessOwnerIdPathsForDisplay();
            $pendingUploads = $this->isFreelanceAccount($user) ? $this->pendingGovernmentIdUploads() : $this->pendingBusinessOwnerIdUploads();
            if (count($idUploads) === 0 && count($pendingUploads) === 0) {
                $errorKey = $this->isFreelanceAccount($user) ? 'government_id' : 'business_owner_id_images';
                $this->addError($errorKey, 'Please upload at least one ID document.');
            }
        }
    }

    /**
     * Leave the post-submit approval screen and open step 2 (Business Basics).
     */
    public function continueToBuildProfile(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        if (!$this->verificationIsApproved()) {
            return;
        }

        session()->forget('verify_qualify_show_approved');
        session([
            'verification_current_step' => '',
            'verification_build_profile_step' => true,
            'verification_build_profile_substep' => 'business_basics',
        ]);
        session()->save();

        $this->enterBusinessBasicsStep($user, true);
    }

    /**
     * Show Business Basics and optionally re-hydrate fields from the database.
     *
     * @param  bool  $hydrateBasicsFields  When false, skips heavy JSON hydration (faster step switches to Legal / success).
     */
    private function enterBusinessBasicsStep(GroomerSpacerProfile $user, bool $refreshFromDb, bool $hydrateBasicsFields = true, bool $scrollToTop = true): void
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
        $this->showStartEarningComplete = false;
        $this->legal_agreements_expanded = false;

        if ($refreshFromDb || $hydrateBasicsFields) {
            $user->refresh();
        }

        if ($hydrateBasicsFields) {
            $this->hydrateBusinessBasicsFields($user);
        }

        if ($scrollToTop) {
            $this->scrollVerifyQualifyStepToTop();
        }
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
        $this->business_gallery_paths = $this->galleryPathsFromBusinessBasics($bb);

        $this->business_avatar_upload = null;
        $this->business_gallery_pick = null;
        $this->business_gallery_pending = [];

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
        $customServices = $groomerProfile['custom_services'] ?? [];
        if (!is_array($customServices)) {
            $customServices = [];
        }
        $this->groomer_custom_services = array_values(array_filter($customServices, fn($v) => is_string($v) && trim($v) !== ''));
        $selectedServices = $groomerProfile['selected_services'] ?? [];
        if (!is_array($selectedServices)) {
            $selectedServices = [];
        }
        $this->groomer_selected_services = array_values(array_filter($selectedServices, fn($v) => is_string($v) && trim($v) !== ''));
        $this->groomer_service_input = '';
        $this->groomer_addon_input = '';

        $this->hydrateGroomerServiceAndAddonPricing($groomerProfile);

        $this->hydrateSpacerBusinessProfile($user);
    }

    /**
     * @param  array<string, mixed>  $bb
     * @return list<string>
     */
    private function galleryPathsFromBusinessBasics(array $bb): array
    {
        $paths = $bb['gallery_paths'] ?? [];
        if (is_string($paths) && $paths !== '') {
            $paths = json_decode($paths, true) ?: [];
        }

        $out = [];
        if (!is_array($paths)) {
            return $out;
        }

        foreach ($paths as $p) {
            if (!is_string($p) || $p === '') {
                continue;
            }
            $n = $this->normalizeStoredPublicPath($p);
            if ($n !== '') {
                $out[] = $n;
            }
            if (count($out) >= 100) {
                break;
            }
        }

        return $out;
    }

    public function initVerifyQualifyDocUploads(): void
    {
        $this->js(
            <<<'JS'
                if (window.VqDocUpload) {
                    if (typeof window.VqDocUpload.init === 'function') window.VqDocUpload.init();
                    if (typeof window.VqDocUpload.afterMorph === 'function') window.VqDocUpload.afterMorph();
                }
            JS
            ,
        );
    }

    /** @deprecated Use initVerifyQualifyDocUploads() */
    public function initBusinessBasicsDocUploads(): void
    {
        $this->initVerifyQualifyDocUploads();
    }

    private function syncBusinessBasicsMediaFromDb(): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        $user->refresh();
        $bb = $user->business_basics ?? [];
        if (!is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }

        $this->business_avatar_path = $this->normalizeStoredPublicPath((string) ($bb['profile_photo_path'] ?? ''));
        $this->business_gallery_paths = $this->galleryPathsFromBusinessBasics($bb);
    }

    /**
     * @return list<UploadedFile|TemporaryUploadedFile>
     */
    private function pendingBusinessGalleryUploads(): array
    {
        return array_values(array_filter(is_array($this->business_gallery_pending) ? $this->business_gallery_pending : [], fn($item) => $item instanceof UploadedFile || $item instanceof TemporaryUploadedFile));
    }

    public function galleryVisibleSlotCount(): int
    {
        $used = count($this->business_gallery_paths) + count($this->pendingBusinessGalleryUploads());

        return max(self::GALLERY_MIN_VISIBLE_SLOTS, $used + 1);
    }

    /**
     * Load groomer_services_pricing / groomer_addon_pricing from saved JSON.
     */
    private function hydrateGroomerServiceAndAddonPricing(array $groomerProfile): void
    {
        $svc = $groomerProfile['services'] ?? [];
        if (!is_array($svc)) {
            $svc = [];
        }

        if ($this->groomer_selected_services === []) {
            $legacyServiceMap = [
                'full_groom' => 'Full Groom (bath, dry, haircut)',
                'face_trim' => 'Face Trim Only',
            ];
            foreach ($legacyServiceMap as $key => $label) {
                if (array_key_exists($key, $svc)) {
                    $this->groomer_selected_services[] = $label;
                }
            }
            foreach ($svc as $key => $row) {
                if (!is_array($row)) {
                    continue;
                }
                $name = trim((string) ($row['name'] ?? ''));
                if ($name === '' && isset($legacyServiceMap[$key])) {
                    $name = $legacyServiceMap[$key];
                }
                if ($name !== '' && !in_array($name, $this->groomer_selected_services, true)) {
                    $this->groomer_selected_services[] = $name;
                }
            }
        }

        $addon = $groomerProfile['addon_pricing'] ?? [];
        if (!is_array($addon)) {
            $addon = [];
        }

        if ($this->groomer_selected_addons === []) {
            $legacyAddonMap = [
                'flea_tick' => 'Flea & Tick Treatment',
                'fast_dry' => 'Fast-Dry Service (express grooming)',
            ];
            foreach ($legacyAddonMap as $key => $label) {
                if (array_key_exists($key, $addon)) {
                    $this->groomer_selected_addons[] = $label;
                }
            }
        }

        $this->groomer_selected_addons = $this->normalizeGroomerSelectedAddons($this->groomer_selected_addons, $addon);

        if ($this->groomer_selected_addons === []) {
            $legacyAddonMap = [
                'flea_tick' => 'Flea & Tick Treatment',
                'fast_dry' => 'Fast-Dry Service (express grooming)',
            ];
            foreach ($legacyAddonMap as $key => $label) {
                if (array_key_exists($key, $addon)) {
                    $this->groomer_selected_addons[] = $label;
                }
            }
        }

        $this->syncGroomerServicesPricing($svc);
        $this->syncGroomerAddonPricing($addon);
    }

    /**
     * @param  array<int, string>  $selected
     * @param  array<string, array<string, mixed>>  $addonPricing
     * @return array<int, string>
     */
    private function normalizeGroomerSelectedAddons(array $selected, array $addonPricing = []): array
    {
        $legacyKeyToLabel = [
            'flea_tick' => 'Flea & Tick Treatment',
            'fast_dry' => 'Fast-Dry Service (express grooming)',
        ];

        $normalized = [];
        foreach ($selected as $value) {
            if (!is_string($value) || trim($value) === '') {
                continue;
            }
            $value = trim($value);

            if (in_array($value, $this->groomerAddonCatalog(), true)) {
                $normalized[] = $value;

                continue;
            }

            if (in_array($value, $this->groomer_custom_addons, true)) {
                $normalized[] = $value;

                continue;
            }

            if (isset($legacyKeyToLabel[$value])) {
                $normalized[] = $legacyKeyToLabel[$value];

                continue;
            }

            if (isset($addonPricing[$value]) && is_array($addonPricing[$value])) {
                $name = trim((string) ($addonPricing[$value]['name'] ?? ''));
                if ($name !== '') {
                    $normalized[] = $name;

                    continue;
                }
            }

            $resolved = null;
            foreach ($this->groomerAddonCatalog() as $label) {
                if ($this->groomerAddonKey($label) === $value) {
                    $resolved = $label;
                    break;
                }
            }
            if ($resolved === null) {
                foreach ($this->groomer_custom_addons as $label) {
                    if ($this->groomerAddonKey($label) === $value) {
                        $resolved = $label;
                        break;
                    }
                }
            }
            if ($resolved !== null) {
                $normalized[] = $resolved;

                continue;
            }

            if (!preg_match('/^[a-z0-9_]+$/', $value)) {
                $normalized[] = $value;
            }
        }

        return array_values(array_unique($normalized));
    }

    public function groomerServiceCatalog(): array
    {
        return ['Full Groom (bath, dry, haircut)', 'Face Trim Only', 'Nail Trim', 'Ear Cleaning', 'Tail Trim Only', 'Bath & Brush', 'Luxury Spa'];
    }

    public function groomerAddonCatalog(): array
    {
        return ['Flea & Tick Treatment', 'Hypoallergenic Shampoo Upgrade', 'Tear-Stain Treatment', 'Coat Shine Spray', 'Nail Grinding', 'Coat Colour Enhancing Shampoo', 'Fast-Dry Service (express grooming)', 'Breath Freshner Gel', 'Deep Conditioning Mask', 'Shed-Control Shampoo', 'Deodorising Treatment', 'Anti-Itch Treatment', 'Soft-Claws / Nail Caps Application', 'Premium Fragrance Upgrade', 'Paw Fur Shaping'];
    }

    public function groomerServiceDefaultDescription(string $name): string
    {
        return match ($name) {
            'Face Trim Only' => 'Targeted trimming of facial hair to maintain cleanliness, comfort, and visibility without a full groom.',
            default => '',
        };
    }

    public function groomerServiceKeyFor(string $name): string
    {
        return $this->groomerServiceKey($name);
    }

    public function groomerAddonKeyFor(string $name): string
    {
        return $this->groomerAddonKey($name);
    }

    private function groomerServiceKey(string $name): string
    {
        $legacy = [
            'Full Groom (bath, dry, haircut)' => 'full_groom',
            'Face Trim Only' => 'face_trim',
            'Nail Trim' => 'nail_trim',
            'Ear Cleaning' => 'ear_cleaning',
            'Tail Trim Only' => 'tail_trim_only',
            'Bath & Brush' => 'bath_brush',
            'Luxury Spa' => 'luxury_spa',
        ];

        if (isset($legacy[$name])) {
            return $legacy[$name];
        }

        $base = Str::slug($name, '_');

        return $base !== '' ? $base : 'service_' . substr(md5($name), 0, 8);
    }

    private function groomerAddonKey(string $name): string
    {
        $legacy = [
            'Flea & Tick Treatment' => 'flea_tick',
            'Fast-Dry Service (express grooming)' => 'fast_dry',
        ];

        if (isset($legacy[$name])) {
            return $legacy[$name];
        }

        $base = Str::slug($name, '_');

        return $base !== '' ? $base : 'addon_' . substr(md5($name), 0, 8);
    }

    public function updatedGroomerSelectedServices(): void
    {
        $this->syncGroomerServicesPricing();
    }

    public function updatedGroomerSelectedAddons(): void
    {
        $this->syncGroomerAddonPricing();
    }

    private function syncGroomerServicesPricing(?array $loaded = null): void
    {
        $loaded = $loaded ?? $this->groomer_services_pricing;
        $next = [];
        foreach ($this->groomer_selected_services as $name) {
            if (!is_string($name) || trim($name) === '') {
                continue;
            }
            $name = trim($name);
            $key = $this->groomerServiceKey($name);
            $row = isset($loaded[$key]) && is_array($loaded[$key]) ? $loaded[$key] : [];
            $existing = $this->groomer_services_pricing[$key] ?? [];
            $next[$key] = [
                'name' => $name,
                'price' => trim((string) ($existing['price'] ?? ($row['price'] ?? ''))),
                'description' => trim((string) ($existing['description'] ?? ($row['description'] ?? ''))),
            ];
        }
        $this->groomer_services_pricing = $next;
    }

    private function syncGroomerAddonPricing(?array $loaded = null): void
    {
        $loaded = $loaded ?? $this->groomer_addon_pricing;
        $next = [];
        foreach ($this->groomer_selected_addons as $name) {
            if (!is_string($name) || trim($name) === '') {
                continue;
            }
            $name = trim($name);
            $key = $this->groomerAddonKey($name);
            $row = isset($loaded[$key]) && is_array($loaded[$key]) ? $loaded[$key] : [];
            $existing = $this->groomer_addon_pricing[$key] ?? [];
            $next[$key] = [
                'name' => $name,
                'price' => trim((string) ($existing['price'] ?? ($row['price'] ?? ''))),
                'description' => trim((string) ($existing['description'] ?? ($row['description'] ?? ''))),
            ];
        }
        $this->groomer_addon_pricing = $next;
    }

    public function addGroomerCustomService(): void
    {
        $name = trim($this->groomer_service_input);
        if ($name === '') {
            return;
        }

        if (in_array($name, $this->groomerServiceCatalog(), true)) {
            if (!in_array($name, $this->groomer_selected_services, true)) {
                $this->groomer_selected_services[] = $name;
            }
            $this->groomer_service_input = '';
            $this->syncGroomerServicesPricing();

            return;
        }

        if (!in_array($name, $this->groomer_custom_services, true)) {
            $this->groomer_custom_services[] = $name;
        }
        if (!in_array($name, $this->groomer_selected_services, true)) {
            $this->groomer_selected_services[] = $name;
        }

        $this->groomer_service_input = '';
        $this->syncGroomerServicesPricing();
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
                    $this->spacer_rules_custom[] = ['text' => $r, 'selected' => true];
                }
            }
        } else {
            $rp = $data['rules_presets'] ?? [];
            if (is_array($rp)) {
                $this->spacer_rules_preset_selected = array_values(array_intersect($this->spacerRulesPresetCatalog(), $rp));
            }
            $rc = $data['rules_custom'] ?? [];
            if (is_array($rc)) {
                $this->spacer_rules_custom = $this->normalizeSpacerCustomEntries($rc);
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
                    $this->spacer_amenities_custom[] = ['text' => $a, 'selected' => true];
                }
            }
        } else {
            $ap = $data['amenities_presets'] ?? [];
            if (is_array($ap)) {
                $this->spacer_amenities_preset_selected = array_values(array_intersect($this->spacerAmenitiesPresetCatalog(), $ap));
            }
            $ac = $data['amenities_custom'] ?? [];
            if (is_array($ac)) {
                $this->spacer_amenities_custom = $this->normalizeSpacerCustomEntries($ac);
            }
        }
        $this->spacer_amenities_preset_selected = array_values(array_unique($this->spacer_amenities_preset_selected));

        $this->spacer_addon_input = '';
        $this->spacer_rule_input = '';
        $this->spacer_amenity_input = '';
    }

    public function updatingBusinessGalleryPending($value): void
    {
        if (!$this->mergingBusinessGalleryPending) {
            $this->snapshotPendingDocUploadProperty('business_gallery_pending');
        }
    }

    public function updatedBusinessGalleryPending($value): void
    {
        if ($this->mergingBusinessGalleryPending) {
            return;
        }

        $this->mergingBusinessGalleryPending = true;

        try {
            $prev = $this->consumePendingDocUploadSnapshot('business_gallery_pending');
            $this->business_gallery_pending = $this->reconcilePendingDocUploads($prev, $value, []);
        } finally {
            $this->mergingBusinessGalleryPending = false;
        }
    }

    public function updatedBusinessGalleryPick($value): void
    {
        if (!$value instanceof TemporaryUploadedFile) {
            return;
        }

        $this->resetValidation('business_gallery_pick');

        $this->validate([
            'business_gallery_pick' => ['file', 'mimes:jpg,jpeg,png,gif,webp', 'max:51200'],
        ]);

        $room = 3 - count($this->business_gallery_paths) - count($this->business_gallery_pending);
        if ($room > 0) {
            $this->business_gallery_pending = [...$this->business_gallery_pending, $value];
        }

        $this->business_gallery_pick = null;
    }

    public function removeBusinessAvatar(bool $includeStored = true): void
    {
        if ($this->business_avatar_upload instanceof TemporaryUploadedFile) {
            $this->business_avatar_upload = null;

            return;
        }

        if ($this->business_avatar_upload !== null && $this->business_avatar_upload !== '') {
            $this->business_avatar_upload = null;

            return;
        }

        if (!$includeStored) {
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

    /**
     * Remove a not-yet-submitted temporary upload from a doc-upload wire model.
     */
    public function removePendingDocUpload(string $property, string $filename, int $size = 0): void
    {
        $allowed = ['business_owner_id_images', 'government_id', 'insurance_certificate_upload', 'business_gallery_pending'];
        if (!in_array($property, $allowed, true)) {
            return;
        }

        unset($this->pendingDocUploadSnapshots[$property]);

        $items = $this->{$property} ?? [];
        if (!is_array($items)) {
            $items = $items instanceof TemporaryUploadedFile ? [$items] : [];
        }

        $remaining = [];
        foreach ($items as $item) {
            if ($item instanceof TemporaryUploadedFile || $item instanceof UploadedFile) {
                if ($this->pendingUploadMatchesRemoval($item, $filename, $size)) {
                    if ($item instanceof TemporaryUploadedFile) {
                        $item->delete();
                    }

                    continue;
                }
            }

            $remaining[] = $item;
        }

        $this->{$property} = array_values($remaining);
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

    public function removeBusinessGalleryStoredFile(string $path): void
    {
        if ($path === '' || str_contains($path, '..')) {
            return;
        }

        $needle = $this->normalizeStoragePathForLookup($path);
        foreach ($this->business_gallery_paths as $index => $stored) {
            if (!is_string($stored) || $stored === '') {
                continue;
            }
            if ($this->normalizeStoragePathForLookup($stored) === $needle) {
                $this->removeBusinessGalleryPath((int) $index);

                return;
            }
        }
    }

    public function removeBusinessGalleryPending(int $index): void
    {
        if (!isset($this->business_gallery_pending[$index])) {
            return;
        }

        $file = $this->business_gallery_pending[$index];
        if ($file instanceof TemporaryUploadedFile) {
            $file->delete();
        }
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
        if ($this->shouldUseDevDbPreview()) {
            return true;
        }

        return trim($this->business_display_name) !== '';
    }

    public function isGroomerBusinessProfileContinueEnabled(): bool
    {
        if ($this->shouldUseDevDbPreview()) {
            return true;
        }

        $hasSpecialty = count($this->groomer_pet_specialties) > 0;
        $otherOk = !in_array('other', $this->groomer_pet_specialties, true) || trim($this->groomer_specialty_other) !== '';

        return trim($this->groomer_experience) !== '' && $hasSpecialty && count($this->groomer_pet_sizes) > 0 && $otherOk;
    }

    public function isSpacerBusinessProfileContinueEnabled(): bool
    {
        if ($this->shouldUseDevDbPreview()) {
            return true;
        }

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
        if ($this->shouldUseDevDbPreview()) {
            return true;
        }

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

        if (!$this->shouldUseDevDbPreview($user)) {
            $this->validate([
                'legal_terms_accepted' => ['accepted'],
            ]);
        }

        $user->update(['legal_policy_agreements' => true]);

        $this->legal_agreements_expanded = false;
        $this->showLegalPolicyForm = false;
        $this->showStartEarningComplete = true;

        session(['verification_build_profile_step' => true]);
        $this->setBuildProfileSubstep('start_grooming');
        session()->save();
        $this->scrollVerifyQualifyStepToTop();
    }

    public function currentSidebarStep(): int
    {
        if ($this->showStartEarningComplete) {
            return 4;
        }
        if ($this->showLegalPolicyForm) {
            return 3;
        }
        if ($this->showBusinessBasicsForm || $this->showGroomerBusinessProfileForm || $this->showSpacerBusinessProfileForm) {
            return 2;
        }

        return 1;
    }

    public function currentVerifyQualifySubstepKey(): string
    {
        if ($this->showVerificationStatus) {
            return 'verification_notices';
        }
        if ($this->showFreelance) {
            return 'freelance_groomer';
        }
        if ($this->showRegisteredBusiness) {
            return 'registered_business';
        }
        if ($this->showAccountPayoutsForm) {
            return 'account_payouts';
        }

        return 'background_checks';
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    public function verifyQualifySubsteps(?GroomerSpacerProfile $user = null): array
    {
        $user = $user ?? Auth::guard('groomer_spacer')->user();
        $isFreelance = $this->isFreelanceAccount($user);

        return [
            ['key' => 'background_checks', 'label' => 'Background Checks'],
            ['key' => 'account_payouts', 'label' => 'Verify Your Account for Payouts'],
            [
                'key' => $isFreelance ? 'freelance_groomer' : 'registered_business',
                'label' => $isFreelance ? 'Freelance Groomer' : 'Registered Business',
            ],
            ['key' => 'verification_notices', 'label' => 'Verification Status'],
        ];
    }

    /**
     * @return list<array{key: string, label: string}>
     */
    public function buildProfileSubsteps(): array
    {
        return [['key' => 'business_basics', 'label' => 'Business Basics'], ['key' => 'about_business', 'label' => 'About Your Business']];
    }

    public function currentBuildProfileSubstepKey(): string
    {
        if ($this->showGroomerBusinessProfileForm || $this->showSpacerBusinessProfileForm) {
            return 'about_business';
        }

        return 'business_basics';
    }

    private function hasEnteredBuildProfilePhase(GroomerSpacerProfile $user): bool
    {
        if ((bool) session('verification_build_profile_step', false)) {
            return true;
        }

        $bb = $user->business_basics ?? [];
        if (!is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }

        return trim((string) ($bb['display_name'] ?? '')) !== '';
    }

    private function maxUnlockedVerifyQualifySubstepIndex(GroomerSpacerProfile $user): int
    {
        if ($user->hasCompletedVerifyQualifyPersonalStep()) {
            return 3;
        }

        $sessionStep = (string) session('verification_current_step', '');
        if (in_array($sessionStep, ['registered_business', 'freelance_groomer'], true)) {
            return 2;
        }
        if ($sessionStep === 'account_payouts') {
            return 1;
        }

        $usage = $this->normalizeFursgoUsage((string) ($user->user_type ?? ''));
        $accountType = (string) ($user->account_type ?? '');
        $locations = $user->select_location_type ?? [];
        $hasLocations = is_array($locations) && count($locations) > 0;

        if (in_array($usage, ['groomer', 'space'], true) && in_array($accountType, ['registered_business', 'freelance'], true) && $hasLocations) {
            return 2;
        }

        if (in_array($usage, ['groomer', 'space'], true) && in_array($accountType, ['registered_business', 'freelance'], true)) {
            return 1;
        }

        return 0;
    }

    private function maxUnlockedBuildProfileSubstepIndex(GroomerSpacerProfile $user): int
    {
        $bb = $user->business_basics ?? [];
        if (!is_array($bb)) {
            $bb = is_string($bb) ? (json_decode($bb, true) ?: []) : [];
        }
        $hasDisplayName = trim((string) ($bb['display_name'] ?? '')) !== '';
        if (!$hasDisplayName) {
            return 0;
        }

        $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
        if ($usage === 'groomer') {
            $gp = $user->groomer_business_profile ?? [];
            if (!is_array($gp)) {
                $gp = is_string($gp) ? (json_decode($gp, true) ?: []) : [];
            }
            $experience = trim((string) ($gp['experience'] ?? ''));
            $petSpecs = $gp['pet_specialties'] ?? [];
            $petSizes = $gp['pet_sizes'] ?? [];
            $profileDone = $experience !== '' && is_array($petSpecs) && count($petSpecs) > 0 && is_array($petSizes) && count($petSizes) > 0;

            return $profileDone ? 1 : 0;
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

            return $bio !== '' && $anyService ? 1 : 0;
        }

        return 0;
    }

    public function verifyQualifySubstepIsNavigable(string $key, ?GroomerSpacerProfile $user = null): bool
    {
        $user = $user ?? Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return $key === 'background_checks';
        }
        if ($this->shouldUseDevDbPreview($user)) {
            return true;
        }

        $maxIdx = $this->maxUnlockedVerifyQualifySubstepIndex($user);
        foreach ($this->verifyQualifySubsteps($user) as $i => $step) {
            if ($step['key'] === $key) {
                return $i <= $maxIdx;
            }
        }

        return false;
    }

    public function buildProfileSubstepIsNavigable(string $key, ?GroomerSpacerProfile $user = null): bool
    {
        $user = $user ?? Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return false;
        }
        if ($this->shouldUseDevDbPreview($user)) {
            return true;
        }
        if (!$this->hasEnteredBuildProfilePhase($user)) {
            return false;
        }

        $maxIdx = $this->maxUnlockedBuildProfileSubstepIndex($user);

        return match ($key) {
            'business_basics' => true,
            'about_business' => $maxIdx >= 1,
            default => false,
        };
    }

    public function goToVerifyQualifySubstep(string $key): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }
        if (!$this->verifyQualifySubstepIsNavigable($key, $user)) {
            return;
        }

        session()->forget(['verification_build_profile_step', 'verification_build_profile_substep']);
        if (in_array($key, ['registered_business', 'freelance_groomer'], true)) {
            $key = $this->reconcilePersonalInfoSubstepWithDb($user, $key);
        }
        $this->applyVerifyQualifySubstep($key);
    }

    public function goToBuildProfileSubstep(string $key): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }
        if (!$this->buildProfileSubstepIsNavigable($key, $user)) {
            return;
        }

        session(['verification_build_profile_step' => true]);
        session()->save();

        if ($key === 'business_basics') {
            $this->setBuildProfileSubstep('business_basics');
            $this->enterBusinessBasicsStep($user, true);

            return;
        }

        $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
        $sub = $usage === 'space' ? 'spacer_profile' : 'groomer_profile';
        $this->setBuildProfileSubstep($sub);
        $this->applyBuildProfileSubstepUi($user, $sub);
    }

    public function sidebarStepIsAvailable(int $step): bool
    {
        if ($step < 1 || $step > 4) {
            return false;
        }
        if ($this->shouldUseDevDbPreview()) {
            return true;
        }
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return $step === 1;
        }

        $dbMax = $this->resolveMaximumSidebarStep($user);
        $forwardCap = $this->resolveForwardNavigationCapFromCurrentUi($user);

        return $step <= min($dbMax, $forwardCap);
    }

    /**
     * Caps how far forward the user may jump while the current screen has unsatisfied required fields.
     */
    private function resolveForwardNavigationCapFromCurrentUi(GroomerSpacerProfile $user): int
    {
        if ($this->shouldUseDevDbPreview($user)) {
            return 4;
        }

        $dbMax = $this->resolveMaximumSidebarStep($user);

        if (!$user->hasCompletedVerifyQualifyPersonalStep()) {
            return 1;
        }

        $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
        if ($usage !== 'groomer' && $usage !== 'space') {
            return 1;
        }

        if ($this->showStartEarningComplete) {
            return $dbMax;
        }

        if ($this->showLegalPolicyForm && !$this->isLegalPolicyContinueEnabled()) {
            return min(3, $dbMax);
        }

        if ($this->showBusinessBasicsForm && !$this->isBusinessBasicsContinueEnabled()) {
            return min(2, $dbMax);
        }

        if ($this->showGroomerBusinessProfileForm && !$this->isGroomerBusinessProfileContinueEnabled()) {
            return min(2, $dbMax);
        }

        if ($this->showSpacerBusinessProfileForm && !$this->isSpacerBusinessProfileContinueEnabled()) {
            return min(2, $dbMax);
        }

        return $dbMax;
    }

    private function resolveMaximumSidebarStep(GroomerSpacerProfile $user): int
    {
        if ($this->shouldUseDevDbPreview($user)) {
            return 4;
        }

        if (!$user->hasCompletedVerifyQualifyPersonalStep()) {
            return 1;
        }

        if (!$this->verificationIsApproved() || !$this->hasEnteredBuildProfilePhase($user)) {
            return 1;
        }

        $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
        if ($usage !== 'groomer' && $usage !== 'space') {
            return 1;
        }

        if ($user->legal_policy_agreements) {
            return 4;
        }

        $sub = $this->coerceBuildProfileSubstepToUserType($user, $this->inferVerificationBuildProfileSubstep($user));

        return match ($sub) {
            'business_basics', 'groomer_profile', 'spacer_profile' => 2,
            'legal_policy' => 3,
            'start_grooming' => 4,
            default => 2,
        };
    }

    public function goToSidebarStep(int $step): void
    {
        if ($step < 1 || $step > 4) {
            return;
        }
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }
        if (!$this->sidebarStepIsAvailable($step)) {
            return;
        }

        if ($step === 1) {
            $this->applySidebarStepOneForGroomerSpaceUser($user);

            return;
        }

        if ($this->currentSidebarStep() === $step) {
            return;
        }

        if ($this->shouldUseDevDbPreview($user)) {
            match ($step) {
                2 => $this->applySidebarStepTwoForGroomerSpaceUser($user),
                3 => $this->applySidebarStepThreeForGroomerSpaceUser($user),
                4 => $this->applySidebarStepFourForGroomerSpaceUser($user),
                default => null,
            };

            return;
        }

        if (!$this->verificationIsApproved() || !$this->hasEnteredBuildProfilePhase($user)) {
            return;
        }

        match ($step) {
            2 => $this->applySidebarStepTwoForGroomerSpaceUser($user),
            3 => $this->applySidebarStepThreeForGroomerSpaceUser($user),
            4 => $this->applySidebarStepFourForGroomerSpaceUser($user),
            default => null,
        };
    }

    private function applyBuildProfileSubstepUi(GroomerSpacerProfile $user, string $buildProfileSubstep): void
    {
        $buildProfileSubstep = $this->coerceBuildProfileSubstepToUserType($user, $buildProfileSubstep);
        $hydrateBasics = !in_array($buildProfileSubstep, ['legal_policy', 'start_grooming'], true);
        $this->enterBusinessBasicsStep($user, false, $hydrateBasics, false);
        if ($buildProfileSubstep === 'groomer_profile') {
            $this->showBusinessBasicsForm = false;
            $this->showGroomerBusinessProfileForm = true;
            $this->showSpacerBusinessProfileForm = false;
            $this->showLegalPolicyForm = false;
            $this->showStartEarningComplete = false;
        } elseif ($buildProfileSubstep === 'spacer_profile') {
            $this->showBusinessBasicsForm = false;
            $this->showGroomerBusinessProfileForm = false;
            $this->showSpacerBusinessProfileForm = true;
            $this->showLegalPolicyForm = false;
            $this->showStartEarningComplete = false;
        } elseif ($buildProfileSubstep === 'legal_policy') {
            $this->showBusinessBasicsForm = false;
            $this->showGroomerBusinessProfileForm = false;
            $this->showSpacerBusinessProfileForm = false;
            $this->showLegalPolicyForm = true;
            $this->showStartEarningComplete = false;
            if ($user->legal_policy_agreements) {
                $this->legal_terms_accepted = true;
                $this->legal_privacy_accepted = true;
            }
        } elseif ($buildProfileSubstep === 'start_grooming') {
            $this->showBusinessBasicsForm = false;
            $this->showGroomerBusinessProfileForm = false;
            $this->showSpacerBusinessProfileForm = false;
            $this->showLegalPolicyForm = false;
            $this->showStartEarningComplete = true;
        } else {
            $this->showBusinessBasicsForm = true;
            $this->showGroomerBusinessProfileForm = false;
            $this->showSpacerBusinessProfileForm = false;
            $this->showLegalPolicyForm = false;
            $this->showStartEarningComplete = false;
        }
        $this->scrollVerifyQualifyStepToTop();
    }

    private function applySidebarStepOneForGroomerSpaceUser(GroomerSpacerProfile $user): void
    {
        session()->forget(['verification_build_profile_step', 'verification_build_profile_substep', 'verification_review_mode', 'verify_qualify_show_approved']);
        $this->verification_review_mode = false;
        $this->applyVerifyQualifySubstep('background_checks');
    }

    private function applySidebarStepTwoForGroomerSpaceUser(GroomerSpacerProfile $user): void
    {
        session(['verification_build_profile_step' => true]);
        session()->save();

        $key = $this->currentBuildProfileSubstepKey();
        if (!$this->buildProfileSubstepIsNavigable($key, $user)) {
            $key = 'business_basics';
        }

        if ($key === 'about_business') {
            $usage = $this->normalizeFursgoUsage($user->user_type ?? '');
            $sub = $usage === 'space' ? 'spacer_profile' : 'groomer_profile';
            session(['verification_build_profile_substep' => $sub]);
            session()->save();
            $this->applyBuildProfileSubstepUi($user, $sub);

            return;
        }

        session(['verification_build_profile_substep' => 'business_basics']);
        session()->save();
        $this->enterBusinessBasicsStep($user, true);
    }

    private function applySidebarStepThreeForGroomerSpaceUser(GroomerSpacerProfile $user): void
    {
        session(['verification_build_profile_step' => true]);
        session(['verification_build_profile_substep' => 'legal_policy']);
        session()->save();
        $this->applyBuildProfileSubstepUi($user, 'legal_policy');
    }

    private function applySidebarStepFourForGroomerSpaceUser(GroomerSpacerProfile $user): void
    {
        if (!$user->legal_policy_agreements) {
            return;
        }
        session(['verification_build_profile_step' => true]);
        session(['verification_build_profile_substep' => 'start_grooming']);
        session()->save();
        $this->applyBuildProfileSubstepUi($user, 'start_grooming');
    }

    public function goToDashboard(): void
    {
        $this->redirect(route('business-hub', absolute: false), navigate: true);
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
        if (!in_array($t, array_column($this->spacer_rules_custom, 'text'), true)) {
            $this->spacer_rules_custom[] = ['text' => $t, 'selected' => true];
        }
        $this->spacer_rule_input = '';
    }

    public function addSpacerAmenityCustom(): void
    {
        $t = trim($this->spacer_amenity_input);
        if ($t === '') {
            return;
        }
        if (!in_array($t, array_column($this->spacer_amenities_custom, 'text'), true)) {
            $this->spacer_amenities_custom[] = ['text' => $t, 'selected' => true];
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
            $this->syncGroomerAddonPricing();

            return;
        }

        if (!in_array($name, $this->groomer_custom_addons, true)) {
            $this->groomer_custom_addons[] = $name;
        }
        if (!in_array($name, $this->groomer_selected_addons, true)) {
            $this->groomer_selected_addons[] = $name;
        }

        $this->groomer_addon_input = '';
        $this->syncGroomerAddonPricing();
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

        $isDevPreview = $this->shouldUseDevDbPreview($user);
        if (!$isDevPreview) {
            $this->validate([
                'business_display_name' => ['required', 'string', 'max:255'],
                'business_tagline' => ['nullable', 'string', 'max:500'],
                'business_bio' => ['nullable', 'string', 'max:5000'],
                'business_avatar_upload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:51200'],
            ]);
        }

        if (!$isDevPreview) {
            foreach ($this->pendingBusinessGalleryUploads() as $i => $file) {
                $this->validate([
                    "business_gallery_pending.$i" => ['file', 'mimes:jpg,jpeg,png,gif,webp', 'max:51200'],
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
        foreach ($this->pendingBusinessGalleryUploads() as $file) {
            if ($file instanceof TemporaryUploadedFile) {
                $dir = $this->storageDirectoryForUpload($file, 'groomer_spacer_profile_gallery');
                $gallery[] = $file->store($dir, 'public');
            }
        }

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
        if (!in_array($usage, ['groomer', 'space'], true)) {
            $usage = $this->normalizeFursgoUsage((string) ($user->user_type ?? ''));
        }
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

        $this->scrollVerifyQualifyStepToTop();
    }

    public function spacerBusinessProfileClientState(): array
    {
        return [
            'suitableFor' => $this->spacer_suitable_for,
            'selectedRules' => $this->spacer_rules_preset_selected,
            'selectedAmenities' => $this->spacer_amenities_preset_selected,
            'customAddonRows' => $this->spacer_addon_custom_rows,
            'rulesCustom' => $this->spacer_rules_custom,
            'amenitiesCustom' => $this->spacer_amenities_custom,
        ];
    }

    /**
     * @param  array<int, mixed>  $items
     * @return list<array{text: string, selected: bool}>
     */
    private function normalizeSpacerCustomEntries(array $items): array
    {
        $normalized = [];
        $seen = [];

        foreach ($items as $item) {
            $text = '';
            $selected = true;

            if (is_string($item)) {
                $text = trim($item);
            } elseif (is_array($item)) {
                $text = trim((string) ($item['text'] ?? ''));
                $selected = !empty($item['selected']);
            }

            if ($text === '' || isset($seen[$text])) {
                continue;
            }

            $seen[$text] = true;
            $normalized[] = ['text' => $text, 'selected' => $selected];
        }

        return $normalized;
    }

    /**
     * @param  array<int, mixed>  $entries
     * @return list<string>
     */
    private function selectedSpacerCustomEntryTexts(array $entries): array
    {
        $texts = [];

        foreach ($entries as $entry) {
            if (is_string($entry)) {
                $text = trim($entry);
                if ($text !== '') {
                    $texts[] = $text;
                }

                continue;
            }

            if (!is_array($entry) || empty($entry['selected'])) {
                continue;
            }

            $text = trim((string) ($entry['text'] ?? ''));
            if ($text !== '') {
                $texts[] = $text;
            }
        }

        return array_values(array_unique($texts));
    }

    /**
     * @param  array<string, mixed>  $form
     */
    private function assignSpacerBusinessProfileFromClient(array $form): void
    {
        $suitableFor = $form['suitableFor'] ?? [];
        $this->spacer_suitable_for = is_array($suitableFor) ? array_values(array_filter($suitableFor, fn($v) => is_string($v) && trim($v) !== '')) : [];

        $selectedRules = $form['selectedRules'] ?? [];
        $this->spacer_rules_preset_selected = is_array($selectedRules) ? array_values(array_filter($selectedRules, fn($v) => is_string($v) && trim($v) !== '')) : [];

        $selectedAmenities = $form['selectedAmenities'] ?? [];
        $this->spacer_amenities_preset_selected = is_array($selectedAmenities) ? array_values(array_filter($selectedAmenities, fn($v) => is_string($v) && trim($v) !== '')) : [];

        $customAddonRows = $form['customAddonRows'] ?? [];
        $normalizedRows = [];
        if (is_array($customAddonRows)) {
            foreach ($customAddonRows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $name = trim((string) ($row['name'] ?? ''));
                if ($name === '') {
                    continue;
                }
                $price = $row['price'] ?? '';
                $normalizedRows[] = [
                    'name' => $name,
                    'selected' => !empty($row['selected']),
                    'price' => is_scalar($price) ? trim((string) $price) : '',
                ];
            }
        }
        $this->spacer_addon_custom_rows = $normalizedRows;

        $rulesCustom = $form['rulesCustom'] ?? [];
        $this->spacer_rules_custom = is_array($rulesCustom) ? $this->normalizeSpacerCustomEntries($rulesCustom) : [];

        $amenitiesCustom = $form['amenitiesCustom'] ?? [];
        $this->spacer_amenities_custom = is_array($amenitiesCustom) ? $this->normalizeSpacerCustomEntries($amenitiesCustom) : [];

        $this->spacer_addon_input = '';
        $this->spacer_rule_input = '';
        $this->spacer_amenity_input = '';
    }

    public function groomerBusinessProfileClientState(): array
    {
        $defaultDescriptions = [];
        foreach ($this->groomerServiceCatalog() as $serviceName) {
            $desc = $this->groomerServiceDefaultDescription($serviceName);
            if ($desc !== '') {
                $defaultDescriptions[$serviceName] = $desc;
            }
        }

        return [
            'experience' => $this->groomer_experience,
            'petSpecialties' => $this->groomer_pet_specialties,
            'specialtyOther' => $this->groomer_specialty_other,
            'petSizes' => $this->groomer_pet_sizes,
            'customServices' => $this->groomer_custom_services,
            'selectedServices' => $this->groomer_selected_services,
            'servicesPricing' => $this->groomer_services_pricing,
            'customAddons' => $this->groomer_custom_addons,
            'selectedAddons' => $this->groomer_selected_addons,
            'addonPricing' => $this->groomer_addon_pricing,
            'serviceCatalog' => $this->groomerServiceCatalog(),
            'addonCatalog' => $this->groomerAddonCatalog(),
            'serviceDefaultDescriptions' => $defaultDescriptions,
            'devPreview' => $this->shouldUseDevDbPreview(),
        ];
    }

    /**
     * @param  array<string, mixed>  $form
     */
    private function assignGroomerBusinessProfileFromClient(array $form): void
    {
        $this->groomer_experience = trim((string) ($form['experience'] ?? ''));
        $petSpecialties = $form['petSpecialties'] ?? [];
        $this->groomer_pet_specialties = is_array($petSpecialties) ? array_values(array_filter($petSpecialties, fn($v) => in_array($v, ['dog', 'cat', 'other'], true))) : [];
        $this->groomer_specialty_other = trim((string) ($form['specialtyOther'] ?? ''));
        $petSizes = $form['petSizes'] ?? [];
        $this->groomer_pet_sizes = is_array($petSizes) ? array_values(array_filter($petSizes, fn($v) => in_array($v, ['small', 'medium', 'large'], true))) : [];
        $customServices = $form['customServices'] ?? [];
        $this->groomer_custom_services = is_array($customServices) ? array_values(array_filter($customServices, fn($v) => is_string($v) && trim($v) !== '')) : [];
        $selectedServices = $form['selectedServices'] ?? [];
        $this->groomer_selected_services = is_array($selectedServices) ? array_values(array_filter($selectedServices, fn($v) => is_string($v) && trim($v) !== '')) : [];
        $customAddons = $form['customAddons'] ?? [];
        $this->groomer_custom_addons = is_array($customAddons) ? array_values(array_filter($customAddons, fn($v) => is_string($v) && trim($v) !== '')) : [];
        $selectedAddons = $form['selectedAddons'] ?? [];
        $this->groomer_selected_addons = is_array($selectedAddons) ? array_values(array_filter($selectedAddons, fn($v) => is_string($v) && trim($v) !== '')) : [];
        $servicesPricing = $form['servicesPricing'] ?? [];
        $this->groomer_services_pricing = is_array($servicesPricing) ? $servicesPricing : [];
        $addonPricing = $form['addonPricing'] ?? [];
        $this->groomer_addon_pricing = is_array($addonPricing) ? $addonPricing : [];
        $this->groomer_selected_addons = $this->normalizeGroomerSelectedAddons($this->groomer_selected_addons, $this->groomer_addon_pricing);
        $this->syncGroomerAddonPricing();
        $this->groomer_service_input = '';
        $this->groomer_addon_input = '';
    }

    public function submitGroomerBusinessProfile(array $form = []): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user) {
            return;
        }

        if ($form !== []) {
            $this->assignGroomerBusinessProfileFromClient($form);
        }

        $isDevPreview = $this->shouldUseDevDbPreview($user);
        if (!$isDevPreview) {
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
                'groomer_custom_services' => ['nullable', 'array'],
                'groomer_custom_services.*' => ['string', 'max:255'],
                'groomer_selected_services' => ['nullable', 'array'],
                'groomer_selected_services.*' => ['string', 'max:255'],
            ]);
        }

        if (!$isDevPreview && in_array('other', $this->groomer_pet_specialties, true) && trim($this->groomer_specialty_other) === '') {
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
                'custom_services' => array_values($this->groomer_custom_services),
                'selected_services' => array_values($this->groomer_selected_services),
                'services' => $this->groomer_services_pricing,
                'addon_pricing' => $this->groomer_addon_pricing,
            ],
        ]);
        $this->showGroomerBusinessProfileForm = false;
        $this->showLegalPolicyForm = true;
        $this->setBuildProfileSubstep('legal_policy');
        $this->scrollVerifyQualifyStepToTop();
    }

    public function submitSpacerBusinessProfile(array $form = []): void
    {
        $user = Auth::guard('groomer_spacer')->user();
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        if ($form !== []) {
            $this->assignSpacerBusinessProfileFromClient($form);
        }

        $isDevPreview = $this->shouldUseDevDbPreview($user);
        if (!$isDevPreview) {
            $this->validate([
                'spacer_bio' => ['required', 'string', 'max:5000'],
                'spacer_services_pricing' => ['required', 'array'],
                'spacer_addon_custom_rows' => ['nullable', 'array'],
                'spacer_suitable_for' => ['nullable', 'array'],
                'spacer_suitable_for.*' => ['string', 'max:255'],
                'spacer_rules_custom' => ['nullable', 'array'],
                'spacer_amenities_custom' => ['nullable', 'array'],
            ]);
        }

        $anyService = false;
        foreach ($this->spacer_services_pricing as $row) {
            if (!empty($row['selected'])) {
                $anyService = true;
                break;
            }
        }
        if (!$isDevPreview && !$anyService) {
            $this->addError('spacer_services_pricing', 'Select at least one pricing option (Hourly, Half-Day, or Full-Day).');

            return;
        }

        $rulesMerged = array_values(array_unique(array_merge($this->spacer_rules_preset_selected, $this->selectedSpacerCustomEntryTexts($this->spacer_rules_custom))));
        $amenitiesMerged = array_values(array_unique(array_merge($this->spacer_amenities_preset_selected, $this->selectedSpacerCustomEntryTexts($this->spacer_amenities_custom))));

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
        $this->showStartEarningComplete = false;
        $this->showLegalPolicyForm = true;
        $this->setBuildProfileSubstep('legal_policy');
        $this->scrollVerifyQualifyStepToTop();
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
        foreach (['storage/app/public/', 'public/', 'storage/'] as $prefix) {
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
        if (!$user instanceof GroomerSpacerProfile) {
            return;
        }

        $persistedType = (string) ($user->account_type ?? '');
        if ($persistedType !== '') {
            $this->account_type = $persistedType;
        }

        $isDevBypass = $this->shouldUseDevDbPreview($user);
        $isFreelance = $this->isFreelanceAccount($user);

        if (!$isDevBypass && $isFreelance) {
            $this->validate([
                'full_name' => ['required', 'string', 'max:255'],
                'business_email' => ['required', 'email'],
                'freelance_service_home_address_line1' => ['nullable', 'string', 'max:500'],
                'freelance_service_home_address_line2' => ['nullable', 'string', 'max:500'],
                'business_phone' => ['required', 'string', 'max:20'],
                'government_id' => ['nullable', 'array'],
                'id_documents' => ['nullable', 'array'],
                'insurance_certificate_upload' => ['nullable', 'array'],
                'account_holder_name' => ['required', 'string', 'max:255'],
                'account_number' => ['required', 'string', 'max:50'],
                'sort_code' => ['required', 'string', 'max:20'],
                'iban' => ['required', 'string', 'max:50'],
                'information_accuracy_confirmed' => ['accepted'],
            ]);
            $hasGovernmentId = count($this->storedGovernmentIdPathsForDisplay()) > 0 || count($this->pendingGovernmentIdUploads()) > 0;
            if (!$hasGovernmentId) {
                $this->addError('government_id', 'Please upload at least one valid government ID document.');

                return;
            }
        } elseif (!$isDevBypass) {
            $this->validate([
                'full_name' => ['required', 'string', 'max:255'],
                'business_email' => ['required', 'email'],
                'business_name' => ['required', 'string', 'max:255'],
                'business_registration_number' => ['required', 'string', 'max:255'],
                'business_phone' => ['required', 'string', 'max:20'],
                'business_owner_id_images' => ['nullable', 'array'],
                'business_owner_id_paths' => ['nullable', 'array'],
                'id_documents' => ['nullable', 'array'],
                'insurance_certificate_upload' => ['nullable', 'array'],
                'account_holder_name' => ['required', 'string', 'max:255'],
                'account_number' => ['required', 'string', 'max:50'],
                'sort_code' => ['required', 'string', 'max:20'],
                'iban' => ['required', 'string', 'max:50'],
                'information_accuracy_confirmed' => ['accepted'],
            ]);
            $hasBusinessOwnerId = count($this->storedBusinessOwnerIdPathsForDisplay()) > 0 || count($this->pendingBusinessOwnerIdUploads()) > 0;
            if (!$hasBusinessOwnerId) {
                $this->addError('business_owner_id_images', 'Please upload at least one valid ID document.');

                return;
            }
        }

        $idUploadProperty = $isFreelance ? 'government_id' : 'business_owner_id_images';
        $idUploadLabel = $isFreelance ? 'government ID' : 'business owner ID';
        $pendingUploads = $isFreelance ? $this->pendingGovernmentIdUploads() : $this->pendingBusinessOwnerIdUploads();

        foreach ($pendingUploads as $index => $image) {
            if ($image instanceof UploadedFile) {
                if (!$isDevBypass) {
                    $this->validate([
                        "{$idUploadProperty}.{$index}" => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:51200'],
                    ]);
                }
            } elseif (!is_string($image) || trim($image) === '') {
                $this->addError($idUploadProperty, "Each {$idUploadLabel} entry must be a valid file or saved path.");
                return;
            }
        }

        if ($this->id_documents && is_array($this->id_documents)) {
            foreach ($this->id_documents as $index => $document) {
                if ($document instanceof UploadedFile) {
                    if (!$isDevBypass) {
                        $this->validate([
                            "id_documents.$index" => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
                        ]);
                    }
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
                if (!$isDevBypass) {
                    $this->validate([
                        "insurance_certificate_upload.$index" => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:51200'],
                    ]);
                }
            } elseif (is_string($certificate) && $certificate !== '') {
                continue;
            } else {
                $this->addError('insurance_certificate_upload', 'Invalid insurance certificate upload.');

                return;
            }
        }

        if ($user) {
            // ID proof uploads (same files as business owner ID; id_document_paths mirrors for legacy column)
            $documentPaths = [];

            $existingInsurance = $this->decodeProfileJson($user->insurance_details ?? null);

            // Insurance: keep stored paths and persist only new pending uploads.
            $insuranceCertificatePaths = $this->uniquePathsByOriginalName($this->insurance_certificate_paths, $this->insurance_certificate_file_names);
            $insuranceFileNames = $this->intersectFileNamesWithPaths($this->insurance_certificate_file_names, $insuranceCertificatePaths);

            foreach ($this->uniquePendingUploads($this->filterPendingUploadsNotStored($this->pendingInsuranceCertificateUploads(), $insuranceFileNames)) as $certificate) {
                $stored = $this->storeDocUploadIfNew($insuranceCertificatePaths, $insuranceFileNames, $certificate, 'insurance_certificates');
                $insuranceCertificatePaths = $stored['paths'];
                $insuranceFileNames = $stored['fileNames'];
            }

            $insuranceCertificatePaths = $this->uniquePathsByOriginalName($insuranceCertificatePaths, $insuranceFileNames);
            $insuranceFileNames = $this->intersectFileNamesWithPaths($insuranceFileNames, $insuranceCertificatePaths);

            // Store government ID / business owner ID images if uploaded
            $governmentIdPaths = $isFreelance ? $this->uniquePathsByOriginalName($this->storedGovernmentIdPathsForDisplay(), $this->government_id_file_names) : [];
            $businessOwnerIdImagePaths = $isFreelance ? [] : $this->uniquePathsByOriginalName($this->storedBusinessOwnerIdPathsForDisplay(), $this->business_owner_id_file_names);
            $governmentIdFileNames = $isFreelance ? $this->intersectFileNamesWithPaths($this->government_id_file_names, $governmentIdPaths) : [];
            $businessOwnerIdFileNames = $isFreelance ? [] : $this->intersectFileNamesWithPaths($this->business_owner_id_file_names, $businessOwnerIdImagePaths);
            $pendingIdUploads = $this->uniquePendingUploads($this->filterPendingUploadsNotStored($isFreelance ? $this->pendingGovernmentIdUploads() : $this->pendingBusinessOwnerIdUploads(), $isFreelance ? $governmentIdFileNames : $businessOwnerIdFileNames));
            $documentPaths = [];

            if ($isFreelance) {
                foreach ($pendingIdUploads as $image) {
                    $stored = $this->storeDocUploadIfNew($governmentIdPaths, $governmentIdFileNames, $image, 'government_id');
                    $governmentIdPaths = $stored['paths'];
                    $governmentIdFileNames = $stored['fileNames'];
                    if ($stored['storedPath'] !== null) {
                        $documentPaths[] = $stored['storedPath'];
                    }
                }
            } else {
                foreach ($pendingIdUploads as $image) {
                    $stored = $this->storeDocUploadIfNew($businessOwnerIdImagePaths, $businessOwnerIdFileNames, $image, 'business_owner_id_images');
                    $businessOwnerIdImagePaths = $stored['paths'];
                    $businessOwnerIdFileNames = $stored['fileNames'];
                    if ($stored['storedPath'] !== null) {
                        $documentPaths[] = $stored['storedPath'];
                    }
                }
            }

            $governmentIdPaths = $this->uniquePathsByOriginalName($governmentIdPaths, $governmentIdFileNames);
            $businessOwnerIdImagePaths = $this->uniquePathsByOriginalName($businessOwnerIdImagePaths, $businessOwnerIdFileNames);
            $governmentIdFileNames = $this->intersectFileNamesWithPaths($governmentIdFileNames, $governmentIdPaths);
            $businessOwnerIdFileNames = $this->intersectFileNamesWithPaths($businessOwnerIdFileNames, $businessOwnerIdImagePaths);
            $documentPaths = $isFreelance ? $governmentIdPaths : $businessOwnerIdImagePaths;

            $table = $user->getTable();
            $existingPayoutDetails = $user->payout_details ?? [];
            if (!is_array($existingPayoutDetails)) {
                $existingPayoutDetails = is_string($existingPayoutDetails) ? (json_decode($existingPayoutDetails, true) ?: []) : [];
            }
            $payoutFrequency = $existingPayoutDetails['payout_frequency'] ?? 'Weekly';
            $payoutBank = trim($this->bank) !== '' ? $this->bank : (string) ($existingPayoutDetails['bank'] ?? '');

            $insuranceDetailsPayload = array_merge($existingInsurance, [
                'insurance_certificate_paths' => $insuranceCertificatePaths,
                'insurance_certificate_file_names' => $insuranceFileNames,
            ]);

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
                    'government_id' => $governmentIdPaths,
                    'government_id_file_names' => $governmentIdFileNames,
                    'information_accuracy_confirmed_at' => now()->toIso8601String(),
                    'last_submitted_at' => now()->toIso8601String(),
                ];

                $mergedFreelance = array_merge($existingFreelance, $freelanceDetailsPayload);
                foreach (['trading_name', 'personal_info_completed', 'personal_info_completed_at', 'id_verification_images'] as $legacyKey) {
                    unset($mergedFreelance[$legacyKey]);
                }

                $payload = [
                    'full_name' => $this->full_name,
                    'freelance_details' => $mergedFreelance,
                    'payout_details' => [
                        'bank' => $payoutBank,
                        'account_holder_name' => $this->account_holder_name,
                        'account_number' => $this->account_number,
                        'sort_code' => $this->sort_code,
                        'iban' => $this->iban,
                        'payout_frequency' => $payoutFrequency,
                    ],
                    'insurance_details' => $insuranceDetailsPayload,
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
                    'business_owner_id_file_names' => $businessOwnerIdFileNames,
                ];

                $payload = [
                    'full_name' => $this->full_name,
                    'business_details' => array_merge($existingBusiness, $businessDetailsPayload),
                    'payout_details' => [
                        'bank' => $payoutBank,
                        'account_holder_name' => $this->account_holder_name,
                        'account_number' => $this->account_number,
                        'sort_code' => $this->sort_code,
                        'iban' => $this->iban,
                        'payout_frequency' => $payoutFrequency,
                    ],
                    'insurance_details' => $insuranceDetailsPayload,
                ];
            }

            $payload['information_accuracy_confirmed'] = true;

            if (Schema::hasColumn($table, 'id_document_paths')) {
                $payload['id_document_paths'] = $documentPaths;
            }
            $user->update($payload);

            $this->insurance_certificate_paths = $insuranceCertificatePaths;
            $this->insurance_certificate_file_names = $insuranceFileNames;
            $this->insurance_certificate_upload = [];
            $this->business_owner_id_paths = $businessOwnerIdImagePaths;
            $this->business_owner_id_file_names = $businessOwnerIdFileNames;
            $this->business_owner_id_images = [];
            $this->government_id_paths = $governmentIdPaths;
            $this->government_id_file_names = $governmentIdFileNames;
            $this->government_id = [];

            $status = $this->isPersonalInfoFormValid() ? 'approved' : 'pending';
            $this->persistVerificationStatus($user, $status);

            session()->forget(['verification_build_profile_step', 'verification_build_profile_substep', 'verification_review_mode']);
            $this->verification_review_mode = false;

            if ($status === 'approved') {
                session(['verify_qualify_show_approved' => true]);
            } else {
                session()->forget('verify_qualify_show_approved');
            }
            session()->save();

            $this->applyVerifyQualifySubstep('verification_notices');
        }
    }

    /**
     * Check if personal information form is valid for enabling submit button
     */
    public function isPersonalInfoFormValid(): bool
    {
        if ($this->shouldUseDevDbPreview()) {
            return true;
        }

        $hasIdProof = $this->isFreelanceAccount() ? count($this->storedGovernmentIdPathsForDisplay()) > 0 || count($this->pendingGovernmentIdUploads()) > 0 : count($this->storedBusinessOwnerIdPathsForDisplay()) > 0 || count($this->pendingBusinessOwnerIdUploads()) > 0;
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
        $boPaths = $this->storedBusinessOwnerIdPathsForDisplay();
        $govPaths = $this->storedGovernmentIdPathsForDisplay();
        $hasIdProof = $this->isFreelanceAccount() ? count($govPaths) > 0 || count($this->pendingGovernmentIdUploads()) > 0 : count($boPaths) > 0 || count($this->pendingBusinessOwnerIdUploads()) > 0;

        return [
            'step' => [
                'showVerificationStatus' => $this->showVerificationStatus,
                'showBusinessBasicsForm' => $this->showBusinessBasicsForm,
                'showGroomerBusinessProfileForm' => $this->showGroomerBusinessProfileForm,
                'showSpacerBusinessProfileForm' => $this->showSpacerBusinessProfileForm,
                'showLegalPolicyForm' => $this->showLegalPolicyForm,
                'showStartEarningComplete' => $this->showStartEarningComplete,
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
                'continue_would_enable' => $this->isFormValid(),
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
                'business_owner_id_paths_count' => count($boPaths),
                'government_id_paths_count' => count($govPaths),
                'business_owner_id_images_count' => count($this->pendingBusinessOwnerIdUploads()),
                'government_id_count' => count($this->pendingGovernmentIdUploads()),
                'id_documents_count' => 0,
                'has_id_proof' => $hasIdProof,
                'information_accuracy_confirmed' => $this->information_accuracy_confirmed,
                'submit_would_enable' => $this->isPersonalInfoFormValid(),
            ],
        ];
    }

    public function activeSidebarStepLabel(): string
    {
        return match ($this->currentSidebarStep()) {
            4 => 'Start Grooming & Earning!',
            3 => 'Legal & Policy Agreement',
            2 => 'Build Your Profile',
            default => 'Verify & Qualify',
        };
    }

    public function activeContentScreenLabel(): string
    {
        if ($this->showStartEarningComplete) {
            return 'Your account is all set!';
        }
        if ($this->showLegalPolicyForm) {
            return 'Legal & Policy Agreements';
        }
        if ($this->showGroomerBusinessProfileForm || $this->showSpacerBusinessProfileForm) {
            return 'About Your Business';
        }
        if ($this->showBusinessBasicsForm) {
            return 'Business Basics';
        }
        if ($this->showVerificationStatus) {
            return 'Verification Status';
        }
        if ($this->showFreelance) {
            return 'Freelance Groomer';
        }
        if ($this->showRegisteredBusiness) {
            return 'Registered Business';
        }
        if ($this->showAccountPayoutsForm) {
            return 'Verify Your Account for Payouts';
        }

        return 'Background Checks';
    }
};
?>

@pushOnce('styles')
    <link rel="stylesheet" href="{{ asset('css/vq-doc-upload.css') }}">
@endPushOnce

@pushOnce('script')
    <script src="{{ asset('js/vq-doc-upload.js') }}"></script>
    <script src="{{ asset('js/vq-groomer-business-profile.js') }}"></script>
    <script src="{{ asset('js/vq-spacer-business-profile.js') }}"></script>
@endPushOnce

<section class="container verify-qualify-page mt-5 mb-5">
    <div class="verification-wrapper{{ $showVerificationCard || $showVerificationStatus || $showStartEarningComplete ? ' verification-wrapper--no-sidebar' : '' }}"
        wire:loading.class="verification-wrapper--navigating"
        wire:target="goToSidebarStep,goToVerifyQualifySubstep,goToBuildProfileSubstep,goBack,submitBusinessBasics,submit,submitPersonalInfo,submitGroomerBusinessProfile,submitSpacerBusinessProfile,submitLegalPolicy">
        <div class="verification-step-loading-bar" wire:loading
            wire:target="goToSidebarStep,goToVerifyQualifySubstep,goToBuildProfileSubstep,goBack,submitBusinessBasics,submit,submitPersonalInfo,submitGroomerBusinessProfile,submitSpacerBusinessProfile,submitLegalPolicy"
            aria-hidden="true">
            <span class="verification-step-loading-bar__sweep"></span>
        </div>
        <!-- Floating Sidebar (step tracker) -->
        @unless ($showVerificationCard || $showVerificationStatus || $showStartEarningComplete)
            <div class="floating-sidebar">
                <div class="sidebar-header">
                    <h1>{{ $this->activeSidebarStepLabel() }}</h1>
                </div>
                <div class="steps-list" role="list">
                    <div @if ($this->sidebarStepIsAvailable(1)) wire:click="goToSidebarStep(1)" role="button"
                        tabindex="0" @else aria-disabled="true" tabindex="-1" @endif
                        class="step-item {{ $this->currentSidebarStep() === 1 ? 'active' : '' }} {{ $this->sidebarStepIsAvailable(1) ? 'step-item--clickable' : 'step-item--disabled' }}">
                        <div class="step-content">
                            <div class="step-title"><span>1.</span>
                                <p>Verify & Qualify</p>
                            </div>
                        </div>
                    </div>
                    <div @if ($this->sidebarStepIsAvailable(2)) wire:click="goToSidebarStep(2)" role="button"
                        tabindex="0" @else aria-disabled="true" tabindex="-1" @endif
                        class="step-item {{ $this->currentSidebarStep() === 2 ? 'active' : '' }} {{ $this->sidebarStepIsAvailable(2) ? 'step-item--clickable' : 'step-item--disabled' }}">
                        <div class="step-content">
                            <div class="step-title"><span>2.</span>
                                <p>Build Your Profile</p>
                            </div>
                        </div>
                    </div>
                    <div @if ($this->sidebarStepIsAvailable(3)) wire:click="goToSidebarStep(3)" role="button"
                        tabindex="0" @else aria-disabled="true" tabindex="-1" @endif
                        class="step-item {{ $this->currentSidebarStep() === 3 ? 'active' : '' }} {{ $this->sidebarStepIsAvailable(3) ? 'step-item--clickable' : 'step-item--disabled' }}">
                        <div class="step-content">
                            <div class="step-title"><span>3.</span>
                                <p>Legal & Policy Agreement</p>
                            </div>
                        </div>
                    </div>
                    <div @if ($this->sidebarStepIsAvailable(4)) wire:click="goToSidebarStep(4)" role="button"
                        tabindex="0" @else aria-disabled="true" tabindex="-1" @endif
                        class="step-item {{ $this->currentSidebarStep() === 4 ? 'active' : '' }} {{ $this->sidebarStepIsAvailable(4) ? 'step-item--clickable' : 'step-item--disabled' }}">
                        <div class="step-content">
                            <div class="step-title"><span>4.</span>
                                <p>Start Grooming & Earning!</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endunless

        <!-- Main Content -->
        <div class="main-content">
            @if ($showVerificationStatus)
                @if ($this->verificationIsApproved())
                    @include('livewire.auth.verify-qualify-verification-status')
                @else
                    @include('livewire.auth.verify-qualify-verification-status-pending')
                @endif
            @elseif ($showStartEarningComplete)
                @include('livewire.auth.verify-qualify-start-grooming-complete')
            @elseif ($showBusinessBasicsForm)
                <div class="business-basics-wrap" wire:key="verify-qualify-business-basics"
                    wire:init="initVerifyQualifyDocUploads">
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
                            @php
                                $__avatarSavedFileEntries =
                                    ($business_avatar_path ?? '') !== ''
                                        ? [
                                            $this->savedDocUploadEntry(
                                                (string) $business_avatar_path,
                                                'groomer-spacer.business-basics-file',
                                            ),
                                        ]
                                        : [];
                                $__avatarSavedKey = md5(implode("\0", array_column($__avatarSavedFileEntries, 'path')));
                            @endphp
                            <input type="hidden" id="profile-photo-saved-urls-json"
                                value="{{ htmlspecialchars(json_encode($__avatarSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                                wire:key="profile-photo-saved-urls-json-{{ $__avatarSavedKey }}">
                            <script>
                                window.__avatarSavedFileEntries = @json($__avatarSavedFileEntries);
                            </script>
                            <x-common.doc-upload upload-id="profile-photo" wire-model="business_avatar_upload"
                                :multiple="false" accept=".jpg,.jpeg,.png,.gif,.webp"
                                hint="JPEG, PNG, GIF, and WebP formats, up to 50 MB." header-label="Upload Image"
                                browse-label="Browse File" empty-title="Choose an image or drag & drop it here."
                                :saved-entries="$__avatarSavedFileEntries" :saved-entries-key="$__avatarSavedKey" saved-json-id="profile-photo-saved-urls-json"
                                saved-window-key="__avatarSavedFileEntries"
                                remove-stored-fn="removeBusinessAvatarStoredFile"
                                pending-clear-call="removeBusinessAvatar" />
                            @error('business_avatar_upload')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
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
                                    $gallery_used = count($gallery_items);
                                    $gallery_total_slots = $this->galleryVisibleSlotCount();
                                    $gallery_slots_key = md5(
                                        implode("\0", $business_gallery_paths) .
                                            '|' .
                                            count($business_gallery_pending) .
                                            '|' .
                                            $gallery_total_slots,
                                    );
                                @endphp
                                @foreach (range(0, $gallery_total_slots - 1) as $slot)
                                    @php $item = $gallery_items[$slot] ?? null; @endphp
                                    <div class="gallery-slot"
                                        wire:key="gallery-slot-{{ $slot }}-{{ $gallery_slots_key }}">
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
                                        @elseif ($slot === $gallery_used)
                                            <label class="gallery-slot-empty">
                                                <input type="file" id="business-gallery-pick-input"
                                                    class="hidden-input" accept=".jpg,.jpeg,.png,.gif,.webp" multiple>
                                                @include('livewire.auth.partials.verify-qualify-gallery-paw')
                                            </label>
                                        @else
                                            <div class="gallery-slot-empty gallery-slot-placeholder"
                                                aria-hidden="true">
                                                @include('livewire.auth.partials.verify-qualify-gallery-paw')
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            @error('business_gallery_pending')
                                <span class="error-text">{{ $message }}</span>
                            @enderror
                            @if ($errors->has('business_gallery_pending.*'))
                                <span class="error-text">{{ $errors->first('business_gallery_pending.*') }}</span>
                            @endif
                        </div>

                        <div class="form-buttons basics-actions">
                            <x-common.button type="button" label="Back" width="105px" bg-color="#FFFFFF"
                                text-color="#9D9B98" border="1px solid rgba(59, 55, 49, 0.10)" :shadow="false"
                                wire:click="goBack" />
                            <x-common.button type="submit" label="Continue" width="105px"
                                bg-color="{{ $this->isBusinessBasicsContinueEnabled() ? '#FFC97A' : '#e5e7eb' }}"
                                text-color="{{ $this->isBusinessBasicsContinueEnabled() ? '#FFFFFF' : '#9ca3af' }}"
                                loading-target="submitBusinessBasics,business_avatar_upload,business_gallery_pending"
                                :disabled="!$this->isBusinessBasicsContinueEnabled()" />
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

                    <div class="verification-card-actions">
                        <x-common.button label="Verify Business" wire:click="verifyBusiness"
                            loading-target="verifyBusiness" />
                    </div>
                </div>
            @elseif ($showAccountPayoutsForm)
                <div class="verification-card">
                    <div class="step-heading">
                        <h2>Verify Your Account for Payouts</h2>
                    </div>

                    <form wire:submit="submit" class="verification-form" x-data="{
                        fursgoUsage: @js($fursgo_usage),
                        accountType: @js($account_type),
                        locationTypes: @js($location_types),
                        devPreview: @js($this->shouldUseDevDbPreview()),
                        get canContinue() {
                            if (this.devPreview) {
                                return true;
                            }
                    
                            return Boolean(this.fursgoUsage) &&
                                Boolean(this.accountType) &&
                                this.locationTypes.length > 0;
                        },
                        isLocationChecked(value) {
                            return this.locationTypes.includes(value);
                        },
                        toggleLocation(value, checked) {
                            if (checked) {
                                if (!this.locationTypes.includes(value)) {
                                    this.locationTypes.push(value);
                                }
                    
                                return;
                            }
                    
                            this.locationTypes = this.locationTypes.filter((item) => item !== value);
                        },
                    }">
                        <div>
                            <div class="form-section">
                                <div class="section-title">
                                    <h3>Choose how you use FursGo</h3>
                                    <p>This helps us set up the right tools and dashboard for your business.</p>
                                </div>
                                <div class="radio-group">
                                    <label class="radio-item" :class="{ 'checked': fursgoUsage === 'groomer' }">
                                        <input type="radio" wire:model="fursgo_usage" value="groomer"
                                            id="groomer" name="fursgo_usage" @change="fursgoUsage = 'groomer'">
                                        <div class="radio-content">
                                            <p class="radio-title">Pet Groomer</p>
                                            <p class="radio-description">I provide grooming services for pets and
                                                accept
                                                bookings from pet owners.</p>
                                        </div>
                                        <span class="radio-custom"></span>
                                    </label>
                                    <label class="radio-item" :class="{ 'checked': fursgoUsage === 'space' }">
                                        <input type="radio" wire:model="fursgo_usage" value="space"
                                            id="space" name="fursgo_usage" @change="fursgoUsage = 'space'">
                                        <div class="radio-content">
                                            <p class="radio-title">Space Host</p>
                                            <p class="radio-description">I rent out a grooming space for
                                                professional
                                                groomers to use.</p>
                                        </div>
                                        <span class="radio-custom"></span>
                                    </label>
                                </div>
                                @error('fursgo_usage')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Select Account Type -->
                            <div class="form-section">
                                <h3 class="section-title">Select Account Type</h3>
                                <div class="radio-group">
                                    <label class="radio-item"
                                        :class="{ 'checked': accountType === 'registered_business' }">
                                        <input type="radio" wire:model="account_type" value="registered_business"
                                            id="registered_business" name="account_type"
                                            @change="accountType = 'registered_business'">
                                        <div class="radio-content">
                                            <p class="radio-title">Registered Business</p>
                                            <p class="radio-description">I operate as a registered business.</p>
                                        </div>
                                        <span class="radio-custom"></span>
                                    </label>
                                    <label class="radio-item" :class="{ 'checked': accountType === 'freelance' }">
                                        <input type="radio" wire:model="account_type" value="freelance"
                                            id="freelance" name="account_type" @change="accountType = 'freelance'">
                                        <div class="radio-content">
                                            <p class="radio-title">Freelance</p>
                                            <p class="radio-description">I operate independently.</p>
                                        </div>
                                        <span class="radio-custom"></span>
                                    </label>
                                </div>
                                @error('account_type')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Select Location Type -->
                            <div class="form-section">
                                <h3 class="section-title">Select location type</h3>
                                <div class="checkbox-group">
                                    <label class="checkbox-item"
                                        :class="{ 'checked': isLocationChecked('space_visits') }">
                                        <input type="checkbox" wire:model="location_types" value="space_visits"
                                            id="space_visits" name="location_types"
                                            @change="toggleLocation('space_visits', $event.target.checked)">
                                        <div class="checkbox-content">
                                            <p class="checkbox-title">Space Visits</p>
                                        </div>
                                        <span class="checkbox-custom"></span>
                                    </label>
                                    <label class="checkbox-item"
                                        :class="{ 'checked': isLocationChecked('commercial_salon') }">
                                        <input type="checkbox" wire:model="location_types" value="commercial_salon"
                                            id="commercial_salon" name="location_types"
                                            @change="toggleLocation('commercial_salon', $event.target.checked)">
                                        <div class="checkbox-content">
                                            <p class="checkbox-title">Commercial Salon</p>
                                        </div>
                                        <span class="checkbox-custom"></span>
                                    </label>
                                    <label class="checkbox-item"
                                        :class="{ 'checked': isLocationChecked('home_studio') }">
                                        <input type="checkbox" wire:model="location_types" value="home_studio"
                                            id="home_studio" name="location_types"
                                            @change="toggleLocation('home_studio', $event.target.checked)">
                                        <div class="checkbox-content">
                                            <p class="checkbox-title">Home Studio</p>
                                        </div>
                                        <span class="checkbox-custom"></span>
                                    </label>
                                    <label class="checkbox-item"
                                        :class="{ 'checked': isLocationChecked('house_visit') }">
                                        <input type="checkbox" wire:model="location_types" value="house_visit"
                                            id="house_visit" name="location_types"
                                            @change="toggleLocation('house_visit', $event.target.checked)">
                                        <div class="checkbox-content">
                                            <p class="checkbox-title">House visit</p>
                                        </div>
                                        <span class="checkbox-custom"></span>
                                    </label>
                                    <label class="checkbox-item"
                                        :class="{ 'checked': isLocationChecked('mobile_van') }">
                                        <input type="checkbox" wire:model="location_types" value="mobile_van"
                                            id="mobile_van" name="location_types"
                                            @change="toggleLocation('mobile_van', $event.target.checked)">
                                        <div class="checkbox-content">
                                            <p class="checkbox-title">Mobile Van</p>
                                        </div>
                                        <span class="checkbox-custom"></span>
                                    </label>
                                </div>
                                @error('location_types')
                                    <span class="error-text">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-buttons verification-form-actions">
                            <x-common.button type="button" label="Back" width="105px" bg-color="#FFFFFF"
                                text-color="#9D9B98" border="1px solid rgba(59, 55, 49, 0.10)" :shadow="false"
                                wire:click="goBack" />
                            <x-common.button type="submit" label="Continue" width="105px" loading-target="submit"
                                x-bind:disabled="!canContinue" x-bind:class="{ 'common-btn--disabled': !canContinue }"
                                x-bind:style="{
                                    backgroundColor: canContinue ? '#FFC97A' : '#e5e7eb',
                                    color: canContinue ? '#FFFFFF' : '#9ca3af',
                                    boxShadow: canContinue ? '0 5px 8px 0 rgba(0, 0, 0, 0.10)' : 'none',
                                }" />
                        </div>
                    </form>
                </div>
            @elseif ($showRegisteredBusiness)
                <div class="verification-card" wire:key="verify-qualify-registered">
                    <div class="step-heading">
                        <h2>Registered Business</h2>
                    </div>

                    @if ($verification_review_mode)
                        <div class="verification-review-banner" role="status">
                            Please review your submission. Fields that still need attention are highlighted below.
                        </div>
                    @endif

                    <form wire:submit="submitPersonalInfo" novalidate wire:init="initVerifyQualifyDocUploads">
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
                                    <p class="business-owner-id-help">Please upload a clear photo or scan of a valid
                                        government-issued ID (e.g. passport or driving licence) and a recent UK utility
                                        bill, bank statement, or official letter showing your current address. Both
                                        documents must be in English and dated within the last 3 months.</p>

                                    @php
                                        $__boSavedFileEntries = $this->savedDocUploadEntriesForPaths(
                                            is_array($business_owner_id_paths ?? null) ? $business_owner_id_paths : [],
                                            is_array($business_owner_id_file_names ?? null)
                                                ? $business_owner_id_file_names
                                                : [],
                                            'groomer-spacer.business-owner-id-file',
                                        );
                                        $__boSavedKey = md5(implode("\0", array_column($__boSavedFileEntries, 'path')));
                                    @endphp
                                    <input type="hidden" id="business-owner-saved-urls-json-registered"
                                        value="{{ htmlspecialchars(json_encode($__boSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                                        wire:key="business-owner-saved-urls-json-registered">
                                    <script>
                                        window.__boSavedFileEntriesRegistered = @json($__boSavedFileEntries);
                                    </script>

                                    <!-- Business Owner ID upload -->
                                    <x-common.doc-upload upload-id="business-owner-id"
                                        wire-model="business_owner_id_images" :saved-entries="$__boSavedFileEntries" :saved-entries-key="$__boSavedKey"
                                        saved-json-id="business-owner-saved-urls-json-registered"
                                        saved-window-key="__boSavedFileEntriesRegistered"
                                        remove-stored-fn="removeBusinessOwnerStoredFile"
                                        empty-title="Choose files or drag & drop them here."
                                        browse-label="Browse Files" />
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
                                        $__insSavedFileEntries = $this->savedDocUploadEntriesForPaths(
                                            is_array($insurance_certificate_paths ?? null)
                                                ? $insurance_certificate_paths
                                                : [],
                                            is_array($insurance_certificate_file_names ?? null)
                                                ? $insurance_certificate_file_names
                                                : [],
                                            'groomer-spacer.insurance-certificate-file',
                                        );
                                        $__insSavedKey = md5(
                                            implode("\0", array_column($__insSavedFileEntries, 'path')),
                                        );
                                    @endphp
                                    <input type="hidden" id="insurance-saved-urls-json"
                                        value="{{ htmlspecialchars(json_encode($__insSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                                        wire:key="insurance-saved-urls-json">
                                    <script>
                                        window.__insSavedFileEntries = @json($__insSavedFileEntries);
                                    </script>
                                    <!-- Insurance certificate upload -->
                                    <x-common.doc-upload upload-id="insurance"
                                        wire-model="insurance_certificate_upload" :saved-entries="$__insSavedFileEntries" :saved-entries-key="$__insSavedKey"
                                        saved-json-id="insurance-saved-urls-json"
                                        saved-window-key="__insSavedFileEntries"
                                        remove-stored-fn="removeInsuranceStoredFile"
                                        empty-title="Choose files or drag & drop them here."
                                        browse-label="Browse Files" />
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
                            <x-common.button type="button" label="Back" width="105px" bg-color="#FFFFFF"
                                text-color="#9D9B98" border="1px solid rgba(59, 55, 49, 0.10)" :shadow="false"
                                wire:click="goBack" />
                            <x-common.button type="submit" label="Submit" width="105px"
                                bg-color="{{ $this->isPersonalInfoFormValid() ? '#FFC97A' : '#e5e7eb' }}"
                                text-color="{{ $this->isPersonalInfoFormValid() ? '#FFFFFF' : '#9ca3af' }}"
                                loading-target="submitPersonalInfo,business_owner_id_images,insurance_certificate_upload"
                                :disabled="!$this->isPersonalInfoFormValid()" />
                        </div>
                    </form>
                </div>
            @elseif ($showFreelance)
                @include('livewire.auth.verify-qualify-freelance-step')
            @endif

        </div>
    </div>
</section>

@script
    <script>
        (function() {
            function easeInOutCubic(t) {
                return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
            }

            function revealMainContent() {
                const main = document.querySelector(
                    ".verification-wrapper .main-content",
                );
                if (!main) {
                    return;
                }
                main.classList.remove("vq-step-enter");
                void main.offsetWidth;
                main.classList.add("vq-step-enter");
                main.addEventListener(
                    "animationend",
                    () => main.classList.remove("vq-step-enter"), {
                        once: true
                    },
                );
            }

            if (!window.__vqScrollStepToTop) {
                window.__vqScrollStepToTop = function __vqScrollStepToTop() {
                    const reduceMotion = window.matchMedia(
                        "(prefers-reduced-motion: reduce)",
                    ).matches;
                    const root =
                        document.scrollingElement || document.documentElement;
                    const startTop = root.scrollTop || window.scrollY || 0;
                    const targetTop = 0;
                    const distance = targetTop - startTop;

                    const snapToTop = () => {
                        window.scrollTo({
                            top: 0,
                            left: 0,
                            behavior: "auto",
                        });
                        root.scrollTop = 0;
                        document.body.scrollTop = 0;
                        revealMainContent();
                    };

                    if (reduceMotion || Math.abs(distance) < 6) {
                        snapToTop();
                        return;
                    }

                    const duration = Math.min(
                        780,
                        Math.max(420, Math.abs(distance) * 0.5),
                    );
                    const startTime = performance.now();

                    const tick = (now) => {
                        const progress = Math.min((now - startTime) / duration, 1);
                        const nextTop =
                            startTop + distance * easeInOutCubic(progress);
                        window.scrollTo(0, nextTop);
                        root.scrollTop = nextTop;
                        if (progress < 1) {
                            requestAnimationFrame(tick);
                        } else {
                            snapToTop();
                        }
                    };

                    requestAnimationFrame(() => requestAnimationFrame(tick));
                };
            }

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

    .verification-wrapper--no-sidebar {
        gap: 0;
    }

    .verification-wrapper--no-sidebar .main-content {
        width: 100%;
    }

    .verification-wrapper .main-content {
        will-change: transform, opacity;
    }

    @keyframes vq-step-content-enter {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .verification-wrapper .main-content.vq-step-enter {
        animation: vq-step-content-enter 0.5s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .verification-wrapper--navigating {
        cursor: wait;
    }

    .verification-wrapper--navigating .main-content {
        opacity: 0.45;
        transform: translateY(6px);
        transition: opacity 0.22s ease, transform 0.22s ease;
        pointer-events: none;
    }

    @media (prefers-reduced-motion: reduce) {
        .verification-wrapper .main-content.vq-step-enter {
            animation: none;
        }

        .verification-wrapper--navigating .main-content {
            transform: none;
            transition: opacity 0.12s ease;
        }
    }

    .verification-wrapper--navigating .floating-sidebar {
        pointer-events: none;
    }

    .verification-step-loading-bar {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        overflow: hidden;
        background: rgba(232, 228, 222, 0.85);
        z-index: 5;
        border-radius: 2px;
        pointer-events: none;
    }

    .verification-step-loading-bar__sweep {
        position: absolute;
        top: 0;
        left: -42%;
        height: 100%;
        width: 42%;
        border-radius: 2px;
        background: linear-gradient(90deg, #FFC97A 0%, #f6a623 45%, #FFC97A 100%);
        box-shadow: 0 0 12px rgba(246, 166, 35, 0.45);
        will-change: left;
        animation: vq-step-load-sweep 1.1s linear infinite;
    }

    @keyframes vq-step-load-sweep {
        0% {
            left: -42%;
        }

        100% {
            left: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .verification-step-loading-bar__sweep {
            animation: none;
            left: 0;
            width: 100%;
            opacity: 0.85;
        }
    }

    /* Floating Sidebar */
    .floating-sidebar {
        max-width: 18rem;
        position: sticky;
        top: calc(var(--dashboard-sticky-header-offset, 9.5rem) + 0.75rem);
        height: fit-content;
        align-self: flex-start;
        z-index: 1010;
        background: #fff;
    }

    .sidebar-header h1 {
        width: 23rem;
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
        border-radius: 96px;
        cursor: default;
    }

    .step-item.active {
        border-radius: 96px;
        background: #FFC97A;
    }

    .step-item--clickable {
        cursor: pointer;
        outline: none;
        transition: box-shadow 0.15s ease, background 0.15s ease;
    }

    .step-item--clickable:focus-visible {
        box-shadow: 0 0 0 2px #fff, 0 0 0 4px #FFC97A;
    }

    .step-item--clickable:hover:not(.active) {
        background: #F0EFEB;
        border-radius: 96px;
    }

    .step-item--disabled {
        opacity: 0.48;
        cursor: not-allowed !important;
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
        justify-content: space-between;
        align-items: center;
        width: 100%;
        max-width: 32rem;
        margin: 2rem auto 0;
        gap: 1rem;
    }

    .verification-form-actions {
        margin-top: 2rem;
    }

    .verification-pending-card .verification-pending-status {
        color: #B45309;
        font-family: "Playfair Display";
        font-size: 36px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
    }

    .verification-review-banner {
        margin: 0 0 1.25rem;
        padding: 0.875rem 1rem;
        border-radius: 8px;
        background: #FFF4E4;
        border: 1px solid #FFC97A;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 15px;
        line-height: 1.45;
    }

    .verification-card-actions {
        display: flex;
        justify-content: center;
        align-items: center;
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
        justify-content: space-between;
        margin: 0;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }


    .radio-item:hover {
        background: #FFF4E4;
    }

    .radio-item.checked {
        background: #FFF4E4;
        border-color: transparent;
    }

    .radio-item input[type="radio"] {
        display: none;
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

    .radio-item input[type="radio"]:checked~.radio-custom {
        border-radius: 10px;
        background: #FFF4E4;
    }

    .radio-item input[type="radio"]:checked~.radio-custom::after,
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
        justify-content: space-between;
        margin: 0;
        user-select: none;
        -webkit-user-select: none;
    }

    .checkbox-item:hover {
        background: transparent;
    }

    .checkbox-item.checked {
        background: #FFF4E4;
        border-color: transparent;
    }

    .checkbox-item input[type="checkbox"] {
        display: none;
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

    .checkbox-item input[type="checkbox"]:checked~.checkbox-custom {
        border-radius: 10px;
        background: #FFF4E4;
    }

    .checkbox-item input[type="checkbox"]:checked~.checkbox-custom::after,
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

    .business-owner-id-help {
        margin: 0 0 1rem;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: 1.45;
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

    .groomer-pill-group--sizes {
        flex-wrap: nowrap;
        gap: 0.5rem;
    }

    .groomer-pill-option.groomer-pill-size {
        min-height: 42px;
        padding: 0.35rem 0.75rem;
        font-size: 14px;
    }

    .groomer-pill-option.groomer-pill-size>span {
        font-size: 14px;
        gap: 6px;
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
        min-height: 48px;
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
        width: 12px;
        height: 8px;
        background-color: #FDFCF8;
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10' fill='none'%3E%3Cpath d='M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z' fill='black'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10' fill='none'%3E%3Cpath d='M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z' fill='black'/%3E%3C/svg%3E");
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-size: 12px 8px;
        mask-size: 12px 8px;
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
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        z-index: 0;
    }

    .gallery-slot-preview {
        z-index: 1;
    }

    .gallery-slot-img:not(.gallery-slot-preview) {
        transition: opacity 0.2s ease;
    }

    .gallery-slot-remove {
        z-index: 4;
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
        position: absolute;
        inset: 0;
        z-index: 1;
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
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
        pointer-events: none;
    }

    .gallery-paw svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .gallery-slot-placeholder {
        cursor: default;
        pointer-events: none;
    }

    .gallery-slot--uploading .gallery-slot-empty .gallery-paw {
        opacity: 0;
    }

    .gallery-slot--uploading .gallery-slot-empty {
        pointer-events: none;
        background: transparent;
    }

    .gallery-slot--uploading .gallery-slot-preview {
        opacity: 1;
    }

    .gallery-slot--batch-pending .gallery-slot-preview {
        opacity: 1;
    }

    .gallery-upload-ring {
        position: absolute;
        inset: 0;
        z-index: 3;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 12px;
        background: rgba(0, 0, 0, 0.35);
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .gallery-upload-ring.gallery-upload-ring--visible {
        opacity: 1;
    }

    .gallery-upload-ring[hidden] {
        display: none !important;
    }

    .gallery-upload-ring__inner {
        position: relative;
        width: 72px;
        height: 72px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .gallery-upload-ring__svg {
        width: 72px;
        height: 72px;
        display: block;
    }

    .gallery-upload-ring__bg {
        stroke: #e8e8e8;
    }

    .gallery-upload-ring__progress {
        stroke: #ffc97a;
        stroke-linecap: round;
    }

    .gallery-upload-ring__label {
        position: absolute;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        line-height: 1;
        pointer-events: none;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.45);
    }

    .basics-actions {
        margin-top: 2rem;
        padding-top: 0;
    }
</style>

<script>
    (function registerVqStepScroll() {
        if (window.__vqStepScrollRegistered) {
            return;
        }
        window.__vqStepScrollRegistered = true;

        if ("scrollRestoration" in history) {
            history.scrollRestoration = "manual";
        }

        function easeInOutCubic(t) {
            return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
        }

        function revealMainContent() {
            const main = document.querySelector(
                ".verification-wrapper .main-content",
            );
            if (!main) {
                return;
            }
            main.classList.remove("vq-step-enter");
            void main.offsetWidth;
            main.classList.add("vq-step-enter");
            main.addEventListener(
                "animationend",
                () => main.classList.remove("vq-step-enter"), {
                    once: true
                },
            );
        }

        window.__vqScrollStepToTop = function __vqScrollStepToTop() {
            const reduceMotion = window.matchMedia(
                "(prefers-reduced-motion: reduce)",
            ).matches;
            const root =
                document.scrollingElement || document.documentElement;
            const startTop = root.scrollTop || window.scrollY || 0;
            const targetTop = 0;
            const distance = targetTop - startTop;

            const snapToTop = () => {
                window.scrollTo({
                    top: 0,
                    left: 0,
                    behavior: "auto"
                });
                root.scrollTop = 0;
                document.body.scrollTop = 0;
                revealMainContent();
            };

            if (reduceMotion || Math.abs(distance) < 6) {
                snapToTop();
                return;
            }

            const duration = Math.min(
                780,
                Math.max(420, Math.abs(distance) * 0.5),
            );
            const startTime = performance.now();

            const tick = (now) => {
                const progress = Math.min((now - startTime) / duration, 1);
                const nextTop = startTop + distance * easeInOutCubic(progress);
                window.scrollTo(0, nextTop);
                root.scrollTop = nextTop;
                if (progress < 1) {
                    requestAnimationFrame(tick);
                } else {
                    snapToTop();
                }
            };

            requestAnimationFrame(() => requestAnimationFrame(tick));
        };

        window.__vqRequestStepScrollToTop = function __vqRequestStepScrollToTop() {
            window.__vqPendingStepScroll = true;
            window.setTimeout(() => {
                window.__vqFlushPendingStepScroll?.();
            }, 300);
        };

        window.__vqFlushPendingStepScroll = function __vqFlushPendingStepScroll() {
            if (!window.__vqPendingStepScroll) {
                return;
            }
            window.__vqPendingStepScroll = false;
            requestAnimationFrame(() => {
                requestAnimationFrame(() => {
                    window.__vqScrollStepToTop?.();
                });
            });
        };

        window.addEventListener("pageshow", (event) => {
            if (!document.querySelector(".verify-qualify-page")) {
                return;
            }
            if (event.persisted) {
                window.__vqRequestStepScrollToTop?.();
            }
        });
    })();

    function vqResolveLivewireWire() {
        if (!window.Livewire) {
            return null;
        }
        const root = document.querySelector('[wire\\:id]');
        if (!root) {
            return null;
        }
        const wid = root.getAttribute('wire:id');
        return wid ? Livewire.find(wid) : null;
    }

    function vqCallLivewire(method, ...args) {
        const wire = vqResolveLivewireWire();
        if (!wire) {
            return null;
        }
        const callFn = wire.$call || wire.call;
        if (typeof callFn !== 'function') {
            return null;
        }
        return callFn.call(wire, method, ...args);
    }

    function vqAfterDocUploadMorph() {
        if (window.VqDocUpload && typeof window.VqDocUpload.afterMorph === 'function') {
            window.VqDocUpload.afterMorph();
        }
    }

    if (!window.removeBusinessOwnerStoredFile) {
        window.removeBusinessOwnerStoredFile = function(storagePath) {
            if (!storagePath) {
                return Promise.reject(new Error("Missing storage path"));
            }
            const result = vqCallLivewire('removeStoredBusinessOwnerImage', storagePath);
            if (result && typeof result.then === 'function') {
                return result.then(function() {
                    vqAfterDocUploadMorph();
                });
            }
            vqAfterDocUploadMorph();
            return Promise.resolve();
        };
    }

    if (!window.removeInsuranceStoredFile) {
        window.removeInsuranceStoredFile = function(storagePath) {
            if (!storagePath) {
                return Promise.reject(new Error("Missing storage path"));
            }
            const result = vqCallLivewire('removeStoredInsuranceCertificate', storagePath);
            if (result && typeof result.then === 'function') {
                return result.then(function() {
                    vqAfterDocUploadMorph();
                });
            }
            vqAfterDocUploadMorph();
            return Promise.resolve();
        };
    }

    if (!window.removeBusinessAvatarStoredFile) {
        window.removeBusinessAvatarStoredFile = function() {
            const result = vqCallLivewire('removeBusinessAvatar');
            if (result && typeof result.then === 'function') {
                return result.then(function() {
                    vqAfterDocUploadMorph();
                });
            }
            vqAfterDocUploadMorph();
            return Promise.resolve();
        };
    }

    if (!window.removeBusinessGalleryStoredFile) {
        window.removeBusinessGalleryStoredFile = function(storagePath) {
            if (!storagePath) {
                return Promise.reject(new Error("Missing storage path"));
            }
            const result = vqCallLivewire('removeBusinessGalleryStoredFile', storagePath);
            if (result && typeof result.then === 'function') {
                return result.then(function() {
                    vqAfterDocUploadMorph();
                });
            }
            vqAfterDocUploadMorph();
            return Promise.resolve();
        };
    }

    function syncDashboardStickyHeaderOffset() {
        const header = document.querySelector('.dashboard-header');
        const root =
            document.querySelector('.dashboard-shell--verify-qualify') ||
            document.body;
        if (!header || !root) {
            return;
        }

        const height = Math.ceil(header.getBoundingClientRect().height);
        const effectiveHeight = Math.max(height, 120);

        root.style.setProperty(
            '--dashboard-sticky-header-offset',
            effectiveHeight + 'px',
        );
    }

    function scheduleDashboardStickyHeaderOffsetSync() {
        window.requestAnimationFrame(() => {
            syncDashboardStickyHeaderOffset();
        });
    }

    function bindDashboardStickyHeaderOffsetSync() {
        if (window.__vqDashboardHeaderOffsetBound) {
            scheduleDashboardStickyHeaderOffsetSync();
            return;
        }

        window.__vqDashboardHeaderOffsetBound = true;
        window.addEventListener('resize', scheduleDashboardStickyHeaderOffsetSync);
        window.addEventListener('load', scheduleDashboardStickyHeaderOffsetSync);

        const header = document.querySelector('.dashboard-header');
        if (header && typeof ResizeObserver !== 'undefined') {
            const observer = new ResizeObserver(
                scheduleDashboardStickyHeaderOffsetSync,
            );
            observer.observe(header);
        }
    }

    function initVerificationPage() {
        bindDashboardStickyHeaderOffsetSync();
        scheduleDashboardStickyHeaderOffsetSync();
        if (window.VqDocUpload && typeof window.VqDocUpload.init === 'function') {
            window.VqDocUpload.init();
        }
        if (window.VqDocUpload && typeof window.VqDocUpload.afterMorph === 'function') {
            window.VqDocUpload.afterMorph();
        }
        bindGalleryUploadProgress();

        if (document.querySelector('.verify-qualify-page')) {
            window.__vqPendingStepScroll = true;
            window.__vqFlushPendingStepScroll?.();
        }
    }

    function revokeGalleryPreviewUrl(preview) {
        if (preview && preview.dataset.objectUrl) {
            URL.revokeObjectURL(preview.dataset.objectUrl);
            delete preview.dataset.objectUrl;
        }
    }

    function bindGalleryUploadProgress() {
        if (window.__vqGalleryUploadProgressBound) return;
        window.__vqGalleryUploadProgressBound = true;

        const GALLERY_INPUT_ID = 'business-gallery-pick-input';
        const RING_RADIUS = 15.5;
        const CIRCUMFERENCE = 2 * Math.PI * RING_RADIUS;

        function isGalleryInput(input) {
            return input && input.id === GALLERY_INPUT_ID;
        }

        function getGallerySlot(input) {
            return input ? input.closest('.gallery-slot') : null;
        }

        function revokeGalleryPreview(preview) {
            revokeGalleryPreviewUrl(preview);
        }

        function markGallerySlotUploading(slot) {
            if (!slot) return;
            slot.classList.add('gallery-slot--uploading');
            window.__vqGalleryActiveSlot = slot;
        }

        function galleryFileFingerprint(file) {
            return [file.name, file.size, file.lastModified].join('::');
        }

        function getGallerySlotsContainer() {
            return document.querySelector('.gallery-slots');
        }

        function getGallerySlotElements() {
            const container = getGallerySlotsContainer();
            return container ?
                Array.from(container.querySelectorAll('.gallery-slot')) : [];
        }

        function getGalleryUploadSlotIndex(input) {
            const slots = getGallerySlotElements();
            const uploadSlot = getGallerySlot(input);
            return uploadSlot ? slots.indexOf(uploadSlot) : -1;
        }

        function ensureGallerySlotAtIndex(index) {
            const container = getGallerySlotsContainer();
            if (!container) {
                return null;
            }

            let slots = getGallerySlotElements();
            while (slots.length <= index) {
                const div = document.createElement('div');
                div.className = 'gallery-slot gallery-slot--client';
                div.setAttribute('data-vq-gallery-client-slot', '1');
                container.appendChild(div);
                slots = getGallerySlotElements();
            }

            return slots[index] || null;
        }

        function clearGallerySlotEmptyUi(slot) {
            if (!slot) {
                return;
            }
            const empty = slot.querySelector('.gallery-slot-empty');
            if (empty) {
                empty.style.display = 'none';
            }
        }

        function getGalleryBatchSlotForFile(file) {
            const batch = window.__vqGalleryBatch;
            if (!batch || !file) {
                return null;
            }
            if (!batch.fileSlots) {
                batch.fileSlots = new Map();
            }
            const fp = galleryFileFingerprint(file);
            if (batch.fileSlots.has(fp)) {
                return batch.fileSlots.get(fp);
            }
            const idx = batch.files.findIndex(function(item) {
                return galleryFileFingerprint(item) === fp;
            });
            if (idx < 0) {
                return null;
            }
            const slot = ensureGallerySlotAtIndex(batch.startIndex + idx);
            if (slot) {
                batch.fileSlots.set(fp, slot);
            }
            return slot;
        }

        function resolveGalleryEventSlot(e) {
            const file = e.target && e.target.files && e.target.files[0];
            if (file) {
                const slot = getGalleryBatchSlotForFile(file);
                if (slot) {
                    return slot;
                }
            }

            const batch = window.__vqGalleryBatch;
            if (batch && batch.files.length > 1) {
                if (typeof batch.currentUploadIndex === 'number') {
                    const current = batch.files[batch.currentUploadIndex];
                    if (current) {
                        return getGalleryBatchSlotForFile(current);
                    }
                }
                return null;
            }

            return isGalleryInput(e.target) ? getGallerySlot(e.target) : null;
        }

        function isGalleryBatchUploadEvent(e) {
            const batch = window.__vqGalleryBatch;
            if (!batch || batch.files.length <= 1) {
                return false;
            }

            const prop =
                e.detail && (e.detail.property || e.detail.name) ?
                e.detail.property || e.detail.name :
                '';
            if (prop && prop !== 'business_gallery_pending') {
                return false;
            }

            const file = e.target && e.target.files && e.target.files[0];
            if (!file) {
                return true;
            }

            const fp = galleryFileFingerprint(file);
            return batch.files.some(function(item) {
                return galleryFileFingerprint(item) === fp;
            });
        }

        function isActiveGalleryUploadEvent(e) {
            if (isGalleryInput(e.target)) {
                return true;
            }
            return isGalleryBatchUploadEvent(e);
        }

        function rememberGalleryBatchSlots(files, startIndex) {
            const batch = window.__vqGalleryBatch;
            if (!batch) {
                return;
            }
            batch.fileSlots = new Map();
            files.forEach(function(file, i) {
                const slot = ensureGallerySlotAtIndex(startIndex + i);
                if (slot) {
                    batch.fileSlots.set(galleryFileFingerprint(file), slot);
                }
            });
        }

        function showGalleryFilePreviewInSlot(slot, file, options) {
            if (!slot || !file || !String(file.type || '').startsWith('image/')) {
                return;
            }

            const withProgress = !!(options && options.withProgress);
            const batchPending = !!(options && options.batchPending);

            if (batchPending) {
                slot.classList.add('gallery-slot--batch-pending');
            }

            markGallerySlotUploading(slot);
            clearGallerySlotEmptyUi(slot);

            let preview = slot.querySelector('img.gallery-slot-preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.className = 'gallery-slot-img gallery-slot-preview';
                preview.alt = '';
                slot.insertBefore(preview, slot.firstChild);
            }

            revokeGalleryPreview(preview);
            const url = URL.createObjectURL(file);
            preview.dataset.objectUrl = url;
            preview.dataset.vqGalleryFileFp = galleryFileFingerprint(file);
            preview.src = url;
            slot.dataset.vqGalleryPreviewUrl = url;

            if (withProgress) {
                const ring = ensureGalleryUploadRing(slot);
                if (ring) {
                    startGalleryUploadProgress(ring);
                }
            }
        }

        function showAllGallerySlotPreviews(input, files, startIndex) {
            rememberGalleryBatchSlots(files, startIndex);
            const isBatch = files.length > 1;
            files.forEach(function(file, i) {
                const slot = ensureGallerySlotAtIndex(startIndex + i);
                showGalleryFilePreviewInSlot(slot, file, {
                    withProgress: true,
                    batchPending: isBatch,
                });
            });
        }

        function uploadSingleGalleryFile(wire, file) {
            const slot = getGalleryBatchSlotForFile(file);
            if (slot) {
                const ring = ensureGalleryUploadRing(slot);
                if (ring) {
                    startGalleryUploadProgress(ring);
                }
            }

            const uploadFn = wire.$upload || wire.upload;
            if (typeof uploadFn !== 'function') {
                return;
            }

            const onFinish = function() {
                if (slot) {
                    const ring = slot.querySelector('.gallery-upload-ring');
                    if (ring) {
                        finishGalleryUploadProgress(ring);
                    }
                }
            };
            const onError = function() {
                if (slot) {
                    clearGallerySlotClientState(slot);
                }
            };
            const onProgress = function(event) {
                const percent =
                    event && event.detail && event.detail.progress != null ?
                    event.detail.progress :
                    event && event.progress != null ?
                    event.progress :
                    null;
                if (slot && percent != null) {
                    const ring = slot.querySelector('.gallery-upload-ring');
                    if (ring) {
                        setGalleryUploadProgress(ring, percent);
                    }
                }
            };

            try {
                uploadFn.call(
                    wire,
                    'business_gallery_pending',
                    file,
                    onFinish,
                    onError,
                    onProgress,
                );
            } catch (error) {
                uploadFn.call(wire, 'business_gallery_pending', file);
            }
        }

        function pushGalleryFilesToLivewire(files) {
            const wire = vqResolveLivewireWire();
            if (!wire || !files.length) {
                return false;
            }

            rememberGalleryBatchSlots(
                files,
                window.__vqGalleryBatch ?
                window.__vqGalleryBatch.startIndex :
                0,
            );

            const uploadMultipleFn = wire.$uploadMultiple || wire.uploadMultiple;

            if (files.length > 1 && typeof uploadMultipleFn === 'function') {
                uploadMultipleFn.call(wire, 'business_gallery_pending', files);
                return true;
            }

            files.forEach(function(file) {
                uploadSingleGalleryFile(wire, file);
            });

            return true;
        }

        function countGalleryServerImages() {
            return document.querySelectorAll(
                '.gallery-slots .gallery-slot-img:not(.gallery-slot-preview)',
            ).length;
        }

        function restoreGalleryBatchPreviewsAfterMorph() {
            const batch = window.__vqGalleryBatch;
            if (!batch || !batch.files || !batch.files.length) {
                return;
            }

            const serverCount = countGalleryServerImages();
            const targetCount = batch.startIndex + batch.files.length;
            if (serverCount >= targetCount) {
                finishGalleryBatchIfComplete();
                return;
            }

            const remainingStart = Math.max(batch.startIndex, serverCount);
            const remaining = batch.files.slice(
                Math.max(0, serverCount - batch.startIndex),
            );
            if (!remaining.length) {
                return;
            }

            remaining.forEach(function(file, i) {
                const slot = ensureGallerySlotAtIndex(remainingStart + i);
                if (!slot) {
                    return;
                }
                const hasServer = slot.querySelector(
                    '.gallery-slot-img:not(.gallery-slot-preview)',
                );
                if (hasServer) {
                    return;
                }
                showGalleryFilePreviewInSlot(slot, file, {
                    batchPending: true,
                    withProgress: true,
                });
            });
        }

        function finishGalleryBatchIfComplete() {
            const batch = window.__vqGalleryBatch;
            if (!batch) {
                return;
            }

            const serverCount = countGalleryServerImages();
            const targetCount = batch.startIndex + batch.files.length;
            if (serverCount < targetCount) {
                return;
            }

            getGallerySlotElements().forEach(function(slot) {
                if (slot.querySelector('.gallery-slot-img:not(.gallery-slot-preview)')) {
                    clearGallerySlotClientState(slot);
                }
                slot.classList.remove('gallery-slot--batch-pending');
            });

            document
                .querySelectorAll('.gallery-slot[data-vq-gallery-client-slot="1"]')
                .forEach(function(slot) {
                    if (!slot.querySelector('.gallery-slot-img:not(.gallery-slot-preview)')) {
                        clearGallerySlotClientState(slot);
                        slot.remove();
                    } else {
                        slot.removeAttribute('data-vq-gallery-client-slot');
                        slot.classList.remove('gallery-slot--client');
                    }
                });

            window.__vqGalleryBatch = null;
            window.__vqGalleryActiveSlot = null;
        }

        function handleGalleryInputChange(input) {
            const files = Array.from(input.files || []).filter(function(file) {
                return String(file.type || '').startsWith('image/');
            });
            if (!files.length) {
                return;
            }

            const startIndex = getGalleryUploadSlotIndex(input);
            if (startIndex < 0) {
                return;
            }

            window.__vqGalleryBatch = {
                files: files,
                startIndex: startIndex,
                fileSlots: new Map(),
                currentUploadIndex: 0,
            };

            showAllGallerySlotPreviews(input, files, startIndex);
            pushGalleryFilesToLivewire(files);
            input.value = '';
        }

        function showGallerySlotPreview(input, file) {
            const slot = getGallerySlot(input);
            showGalleryFilePreviewInSlot(slot, file, {
                withProgress: true
            });
            window.__vqGalleryActiveSlot = slot;
        }

        function getGalleryProgressState(ring) {
            if (!ring.__vqGalleryProgressState) {
                ring.__vqGalleryProgressState = {
                    uploading: false,
                    display: 0,
                    target: 0,
                    raf: null,
                    creepTimer: null,
                };
            }
            return ring.__vqGalleryProgressState;
        }

        function cancelGalleryProgressAnimation(state) {
            if (state.raf) {
                cancelAnimationFrame(state.raf);
                state.raf = null;
            }
        }

        function clearGalleryProgressCreep(state) {
            if (state.creepTimer) {
                clearInterval(state.creepTimer);
                state.creepTimer = null;
            }
        }

        function paintGalleryProgress(ring, value) {
            if (!ring) return;
            const p = Math.max(0, Math.min(100, Math.round(value)));
            const circle = ring.querySelector('.gallery-upload-ring__progress');
            const label = ring.querySelector('.gallery-upload-ring__label');
            if (circle) {
                circle.style.strokeDasharray = String(CIRCUMFERENCE);
                circle.style.strokeDashoffset = String(
                    CIRCUMFERENCE * (1 - Math.max(p, 1) / 100),
                );
            }
            if (label) {
                label.textContent = p + '%';
            }
            ring.hidden = false;
            ring.classList.add('gallery-upload-ring--visible');
            const slot = ring.closest('.gallery-slot');
            if (slot) {
                slot.classList.add('gallery-slot--uploading');
            }
            const state = getGalleryProgressState(ring);
            state.display = p;
        }

        function animateGalleryProgressTo(ring, nextTarget) {
            if (!ring) return;
            const state = getGalleryProgressState(ring);
            const goal = Math.max(
                state.display,
                Math.min(100, Number(nextTarget) || 0),
            );
            state.target = Math.max(state.target, goal);

            if (goal <= state.display + 0.4) {
                paintGalleryProgress(ring, goal);
                return;
            }

            cancelGalleryProgressAnimation(state);
            const startValue = state.display;
            const delta = goal - startValue;
            const startedAt = performance.now();
            const duration = Math.min(900, Math.max(300, delta * 16));
            const easeInOut = function(t) {
                return t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
            };

            const step = function(now) {
                const elapsed = Math.min(1, (now - startedAt) / duration);
                paintGalleryProgress(ring, startValue + delta * easeInOut(elapsed));
                if (elapsed < 1) {
                    state.raf = requestAnimationFrame(step);
                } else {
                    state.raf = null;
                    state.display = goal;
                }
            };

            state.raf = requestAnimationFrame(step);
        }

        function startGalleryUploadProgress(ring) {
            if (!ring) return;
            const state = getGalleryProgressState(ring);
            if (state.uploading) return;

            clearGalleryProgressCreep(state);
            cancelGalleryProgressAnimation(state);
            state.uploading = true;
            state.display = 0;
            state.target = 1;
            paintGalleryProgress(ring, 1);

            state.creepTimer = setInterval(function() {
                if (!state.uploading) {
                    clearGalleryProgressCreep(state);
                    return;
                }
                const ceiling = 88;
                if (state.display >= ceiling) return;
                const bump = Math.max(1, Math.round((ceiling - state.display) * 0.12));
                animateGalleryProgressTo(ring, Math.min(ceiling, state.display + bump));
            }, 110);
        }

        function setGalleryUploadProgress(ring, percent) {
            if (!ring) return;
            const state = getGalleryProgressState(ring);
            const next = Math.min(99, Math.max(1, Number(percent) || 0));
            state.target = Math.max(state.target, next);
            animateGalleryProgressTo(ring, next);
        }

        function finishGalleryUploadProgress(ring) {
            if (!ring) return;
            const state = getGalleryProgressState(ring);
            clearGalleryProgressCreep(state);
            cancelGalleryProgressAnimation(state);
            state.uploading = false;
            state.target = 100;
            if (state.display >= 99) {
                paintGalleryProgress(ring, 100);
                return;
            }
            animateGalleryProgressTo(ring, 100);
        }

        function resetGalleryUploadProgress(ring) {
            if (!ring) return;
            const state = getGalleryProgressState(ring);
            state.uploading = false;
            clearGalleryProgressCreep(state);
            cancelGalleryProgressAnimation(state);
            state.display = 0;
            state.target = 0;
        }

        function showProgressRing(ringEl, percent) {
            animateGalleryProgressTo(ringEl, percent);
            return Math.round(percent);
        }

        function setProgressRing(ringEl, percent) {
            return showProgressRing(ringEl, percent);
        }

        function ensureGalleryUploadRing(slot) {
            if (!slot) return null;
            let ring = slot.querySelector('.gallery-upload-ring');
            if (ring) return ring;
            ring = document.createElement('div');
            ring.className = 'gallery-upload-ring';
            ring.hidden = true;
            ring.innerHTML =
                '<div class="gallery-upload-ring__inner">' +
                '<svg class="gallery-upload-ring__svg" viewBox="0 0 36 36" aria-hidden="true">' +
                '<circle class="gallery-upload-ring__bg" cx="18" cy="18" r="' + RING_RADIUS +
                '" fill="none" stroke-width="3" />' +
                '<circle class="gallery-upload-ring__progress" cx="18" cy="18" r="' + RING_RADIUS +
                '" fill="none" stroke-width="3" transform="rotate(-90 18 18)" />' +
                '</svg>' +
                '<span class="gallery-upload-ring__label">0%</span>' +
                '</div>';
            slot.appendChild(ring);
            return ring;
        }

        function resetGallerySlotUpload(input) {
            const slot = getGallerySlot(input);
            if (!slot) return;
            const ring = slot.querySelector('.gallery-upload-ring');
            if (ring) {
                resetGalleryUploadProgress(ring);
                ring.hidden = true;
                ring.classList.remove('gallery-upload-ring--visible');
            }
            slot.classList.remove('gallery-slot--uploading');
            const preview = slot.querySelector('.gallery-slot-preview');
            if (preview) {
                revokeGalleryPreview(preview);
                preview.remove();
            }
            if (window.__vqGalleryActiveSlot === slot) {
                window.__vqGalleryActiveSlot = null;
            }
        }

        document.addEventListener('change', function(e) {
            const input = e.target;
            if (!isGalleryInput(input) || !input.files || !input.files.length) return;
            handleGalleryInputChange(input);
        });

        document.addEventListener('livewire-upload-start', function(e) {
            if (!isActiveGalleryUploadEvent(e)) return;
            const slot = resolveGalleryEventSlot(e);
            const ring = ensureGalleryUploadRing(slot);
            if (ring) startGalleryUploadProgress(ring);
            const file = e.target && e.target.files && e.target.files[0];
            if (file) {
                showGalleryFilePreviewInSlot(slot, file, {
                    withProgress: true
                });
            }
        });

        document.addEventListener('livewire-upload-progress', function(e) {
            if (!isActiveGalleryUploadEvent(e)) return;
            const percent = e.detail && e.detail.progress != null ? e.detail.progress : 1;
            const slot = resolveGalleryEventSlot(e);
            if (slot) {
                setGalleryUploadProgress(slot.querySelector('.gallery-upload-ring'), percent);
            }
        });

        document.addEventListener('livewire-upload-finish', function(e) {
            if (!isActiveGalleryUploadEvent(e)) return;
            const slot = resolveGalleryEventSlot(e);
            const ring = slot ? slot.querySelector('.gallery-upload-ring') : null;
            if (ring) finishGalleryUploadProgress(ring);
        });

        document.addEventListener('livewire-upload-error', function(e) {
            if (!isActiveGalleryUploadEvent(e)) return;
            const slot = resolveGalleryEventSlot(e);
            if (slot) {
                clearGallerySlotClientState(slot);
                return;
            }
            if (isGalleryInput(e.target)) {
                resetGallerySlotUpload(e.target);
            }
        });

        document.addEventListener('livewire-upload-cancel', function(e) {
            if (!isActiveGalleryUploadEvent(e)) return;
            const slot = resolveGalleryEventSlot(e);
            if (slot) {
                clearGallerySlotClientState(slot);
                return;
            }
            if (isGalleryInput(e.target)) {
                resetGallerySlotUpload(e.target);
            }
        });

        function restoreGalleryPreviewIfNeeded(slot) {
            if (!slot) return null;
            const existing = slot.querySelector('.gallery-slot-preview');
            if (existing) return existing;

            const url = slot.dataset.vqGalleryPreviewUrl;
            if (!url) return null;

            const preview = document.createElement('img');
            preview.className = 'gallery-slot-img gallery-slot-preview';
            preview.alt = '';
            preview.dataset.objectUrl = url;
            preview.src = url;
            slot.insertBefore(preview, slot.firstChild);
            return preview;
        }

        function clearGallerySlotClientState(slot) {
            if (!slot) return;
            const preview = slot.querySelector('.gallery-slot-preview');
            if (preview) {
                revokeGalleryPreview(preview);
                preview.remove();
            }
            delete slot.dataset.vqGalleryPreviewUrl;
            const ring = slot.querySelector('.gallery-upload-ring');
            if (ring) {
                resetGalleryUploadProgress(ring);
                ring.hidden = true;
                ring.classList.remove('gallery-upload-ring--visible');
            }
            slot.classList.remove('gallery-slot--uploading');
            slot.classList.remove('gallery-slot--batch-pending');
            const empty = slot.querySelector('.gallery-slot-empty');
            if (empty) {
                empty.style.display = '';
            }
        }

        function finalizeGallerySlotAfterMorph() {
            restoreGalleryBatchPreviewsAfterMorph();
            finishGalleryBatchIfComplete();

            const slots = [];
            if (window.__vqGalleryActiveSlot) {
                slots.push(window.__vqGalleryActiveSlot);
            }
            document.querySelectorAll('.gallery-slot--uploading').forEach(function(slot) {
                if (slots.indexOf(slot) === -1) slots.push(slot);
            });

            if (!slots.length) {
                return;
            }

            slots.forEach(function(slot) {
                const serverImg = slot.querySelector('.gallery-slot-img:not(.gallery-slot-preview)');

                if (slot.classList.contains('gallery-slot--batch-pending')) {
                    if (serverImg) {
                        const ring = slot.querySelector('.gallery-upload-ring');
                        if (ring) {
                            finishGalleryUploadProgress(ring);
                        }
                        const revealServerImage = function() {
                            serverImg.style.opacity = '1';
                            requestAnimationFrame(function() {
                                clearGallerySlotClientState(slot);
                                slot.classList.remove('gallery-slot--batch-pending');
                            });
                        };
                        const preview = slot.querySelector('.gallery-slot-preview');
                        if (preview) {
                            serverImg.style.opacity = '0';
                        }
                        if (serverImg.complete && serverImg.naturalWidth > 0) {
                            revealServerImage();
                        } else {
                            serverImg.addEventListener('load', revealServerImage, {
                                once: true,
                            });
                            serverImg.addEventListener('error', revealServerImage, {
                                once: true,
                            });
                        }
                    }
                    return;
                }

                const preview = restoreGalleryPreviewIfNeeded(slot);
                const ring = ensureGalleryUploadRing(slot);

                if (ring) {
                    paintGalleryProgress(ring, 100);
                }

                if (!serverImg) {
                    return;
                }

                const revealServerImage = function() {
                    serverImg.style.opacity = '1';
                    requestAnimationFrame(function() {
                        clearGallerySlotClientState(slot);
                        if (window.__vqGalleryActiveSlot === slot) {
                            window.__vqGalleryActiveSlot = null;
                        }
                    });
                };

                if (preview) {
                    serverImg.style.opacity = '0';
                }

                if (serverImg.complete && serverImg.naturalWidth > 0) {
                    revealServerImage();
                    return;
                }

                serverImg.addEventListener('load', revealServerImage, {
                    once: true
                });
                serverImg.addEventListener('error', revealServerImage, {
                    once: true
                });
            });
        }

        function registerGalleryMorphCleanup() {
            if (window.__vqGalleryMorphCleanupHooked) return;
            window.__vqGalleryMorphCleanupHooked = true;
            const register = function() {
                Livewire.hook('commit', function(payload) {
                    if (typeof payload.succeed !== 'function') return;
                    payload.succeed(function() {
                        finalizeGallerySlotAfterMorph();
                    });
                });
            };
            if (window.Livewire) {
                register();
            } else {
                document.addEventListener('livewire:init', register, {
                    once: true
                });
            }
        }

        registerGalleryMorphCleanup();
    }

    bindGalleryUploadProgress();

    document.addEventListener('DOMContentLoaded', initVerificationPage);
    document.addEventListener('livewire:navigated', initVerificationPage);
    document.addEventListener('livewire:init', () => {
        bindDashboardStickyHeaderOffsetSync();
        if (window.Livewire) {
            Livewire.hook('commit', ({
                succeed
            }) => {
                if (typeof succeed === 'function') {
                    succeed(() => {
                        scheduleDashboardStickyHeaderOffsetSync();
                        if (window.VqDocUpload && typeof window.VqDocUpload.afterMorph ===
                            'function') {
                            window.VqDocUpload.afterMorph();
                        }
                        window.__vqFlushPendingStepScroll?.();
                    });
                }
            });
        }
    });
</script>
