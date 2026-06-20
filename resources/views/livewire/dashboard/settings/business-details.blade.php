<?php

use App\Models\GroomerSpacerProfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Volt\Component;
use Livewire\WithFileUploads;

new class extends Component {
    use WithFileUploads;

    public ?string $editingSection = null;

    public string $fullName = '';
    public string $email = '';
    public string $businessPhone = '';

    public string $businessName = '';
    public string $businessRegistrationNumber = '';
    public string $tagline = '';
    public string $bio = '';
    public string $profilePhotoPath = '';
    public $profilePhotoUpload = null;

    public string $galleryPathsText = '';
    public array $galleryPaths = [];
    public $galleryUpload = null;
    public ?int $galleryReplaceIndex = null;

    public string $accountHolderName = '';
    public string $accountNumber = '';
    public string $sortCode = '';
    public string $iban = '';

    public string $businessIdPathsText = '';
    public string $insurancePathsText = '';
    public string $insuranceExpiryDate = '';

    public function mount(): void
    {
        $this->hydrateEditableFields();
    }

    public function with(): array
    {
        $profile = $this->profile();
        $businessDetails = $this->arrayValue($profile?->business_details);
        $businessBasics = $this->arrayValue($profile?->business_basics);
        $payoutDetails = $this->arrayValue($profile?->payout_details);
        $insuranceDetails = $this->arrayValue($profile?->insurance_details);

        $profileImage = $this->fileCard($businessBasics['profile_photo_path'] ?? null);
        $gallery = $this->fileCards($businessBasics['gallery_paths'] ?? [], PHP_INT_MAX);
        $businessIdFiles = $this->fileCards($businessDetails['business_owner_id_images'] ?? ($profile?->id_document_paths ?? []), 4);
        $insuranceFiles = $this->fileCards($insuranceDetails['insurance_certificate_paths'] ?? [], 4, [
            'expires_at' => $this->firstString($insuranceDetails, ['expires_at', 'expiry_date', 'expiration_date', 'insurance_expiry_date', 'insurance_certificate_expiry_date', 'insurance_certificate_expires_at']),
        ]);
        $businessIdHasIssue = $this->filesHaveIssue($businessIdFiles);
        $insuranceHasIssue = $this->filesHaveIssue($insuranceFiles);

        return [
            'profile' => $profile,
            'businessDetails' => $businessDetails,
            'businessBasics' => $businessBasics,
            'payoutDetails' => $payoutDetails,
            'profileImage' => $profileImage,
            'gallery' => $gallery,
            'businessIdFiles' => $businessIdFiles,
            'insuranceFiles' => $insuranceFiles,
            'businessIdHasIssue' => $businessIdHasIssue,
            'insuranceHasIssue' => $insuranceHasIssue,
        ];
    }

    public function editSection(string $section): void
    {
        if (!in_array($section, ['personal', 'business', 'gallery', 'payout', 'business-id', 'insurance'], true)) {
            return;
        }

        $this->hydrateEditableFields();
        $this->editingSection = $section;
    }

    public function savePersonalDetails(): void
    {
        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $validated = $this->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'businessPhone' => ['nullable', 'string', 'max:50'],
        ]);

        $businessDetails = $this->arrayValue($profile->business_details);
        $businessDetails['business_phone'] = $validated['businessPhone'];

        $profile->update([
            'full_name' => $validated['fullName'],
            'email' => $validated['email'],
            'business_details' => $businessDetails,
        ]);

        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    public function saveBusinessDetails(): void
    {
        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $validated = $this->validate([
            'businessName' => ['nullable', 'string', 'max:255'],
            'businessRegistrationNumber' => ['nullable', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profilePhotoPath' => ['nullable', 'string', 'max:2048'],
            'profilePhotoUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,avif', 'max:1024'],
        ]);

        $businessDetails = $this->arrayValue($profile->business_details);
        $businessBasics = $this->arrayValue($profile->business_basics);

        $businessDetails['business_name'] = $validated['businessName'];
        $businessDetails['business_registration_number'] = $validated['businessRegistrationNumber'];
        $businessBasics['display_name'] = $validated['businessName'];
        $businessBasics['tagline'] = $validated['tagline'];
        $businessBasics['bio'] = $validated['bio'];
        $businessBasics['profile_photo_path'] = $this->profilePhotoUpload ? $this->profilePhotoUpload->store($this->profileImageUploadDirectory($profile), 'public') : $validated['profilePhotoPath'];

        $profile->update([
            'business_details' => $businessDetails,
            'business_basics' => $businessBasics,
        ]);

        $this->profilePhotoUpload = null;
        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    private function profileImageUploadDirectory(GroomerSpacerProfile $profile): string
    {
        return strtolower((string) $profile->user_type) === 'space' ? 'spacer-assets/profile-image' : 'groomer-assets/profile-image';
    }

    private function galleryImageUploadDirectory(GroomerSpacerProfile $profile): string
    {
        return strtolower((string) $profile->user_type) === 'space' ? 'spacer-assets/pets-images' : 'groomer-assets/pets-images';
    }

    public function saveGalleryDetails(): void
    {
        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $this->validate([
            'galleryUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,avif', 'max:2048'],
        ]);

        $this->persistGalleryUpload($profile);
        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    public function updatedGalleryUpload(): void
    {
        $this->validateOnly('galleryUpload', [
            'galleryUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,avif', 'max:2048'],
        ]);

        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $this->persistGalleryUpload($profile);
        $this->editingSection = 'gallery';
        $this->hydrateEditableFields();
    }

    private function persistGalleryUpload(GroomerSpacerProfile $profile): void
    {
        if (!$this->galleryUpload) {
            return;
        }

        $profile->refresh();
        $businessBasics = $this->arrayValue($profile->business_basics);
        $galleryPaths = $this->arrayStrings($businessBasics['gallery_paths'] ?? []);
        $storedPath = $this->galleryUpload->store($this->galleryImageUploadDirectory($profile), 'public');
        $replaceIndex = $this->galleryReplaceIndex;

        if ($replaceIndex !== null && $replaceIndex >= 0) {
            $galleryPaths[$replaceIndex] = $storedPath;
            ksort($galleryPaths);
            $galleryPaths = array_values($galleryPaths);
        } else {
            $galleryPaths[] = $storedPath;
        }

        $businessBasics['gallery_paths'] = $galleryPaths;
        $profile->update(['business_basics' => $businessBasics]);

        $this->galleryPaths = $businessBasics['gallery_paths'];
        $this->galleryPathsText = implode("\n", $this->galleryPaths);
        $this->galleryUpload = null;
        $this->galleryReplaceIndex = null;
    }

    public function removeGalleryImage(int $index): void
    {
        $profile = $this->profile();
        if (!$profile || $index < 0) {
            return;
        }

        $profile->refresh();
        $businessBasics = $this->arrayValue($profile->business_basics);
        $galleryPaths = $this->arrayStrings($businessBasics['gallery_paths'] ?? []);

        if (!array_key_exists($index, $galleryPaths)) {
            return;
        }

        unset($galleryPaths[$index]);
        $businessBasics['gallery_paths'] = array_values($galleryPaths);
        $profile->update(['business_basics' => $businessBasics]);

        $this->galleryPaths = $businessBasics['gallery_paths'];
        $this->galleryPathsText = implode("\n", $this->galleryPaths);
        $this->galleryUpload = null;
        $this->galleryReplaceIndex = null;
        $this->editingSection = 'gallery';
        $this->hydrateEditableFields();
    }

    public function savePayoutDetails(): void
    {
        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $validated = $this->validate([
            'accountHolderName' => ['nullable', 'string', 'max:255'],
            'accountNumber' => ['nullable', 'string', 'max:50'],
            'sortCode' => ['nullable', 'string', 'max:50'],
            'iban' => ['nullable', 'string', 'max:100'],
        ]);

        $payoutDetails = $this->arrayValue($profile->payout_details);
        $payoutDetails['account_holder_name'] = $validated['accountHolderName'];
        $payoutDetails['account_number'] = $validated['accountNumber'];
        $payoutDetails['sort_code'] = $validated['sortCode'];
        $payoutDetails['iban'] = $validated['iban'];

        $profile->update(['payout_details' => $payoutDetails]);

        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    public function saveBusinessIdDetails(): void
    {
        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $this->validate([
            'businessIdPathsText' => ['nullable', 'string', 'max:5000'],
        ]);

        $paths = $this->linesToArray($this->businessIdPathsText);
        $businessDetails = $this->arrayValue($profile->business_details);
        $businessDetails['business_owner_id_images'] = $paths;

        $profile->update([
            'business_details' => $businessDetails,
            'id_document_paths' => $paths,
        ]);

        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    public function saveInsuranceDetails(): void
    {
        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $validated = $this->validate([
            'insurancePathsText' => ['nullable', 'string', 'max:5000'],
            'insuranceExpiryDate' => ['nullable', 'date'],
        ]);

        $insuranceDetails = $this->arrayValue($profile->insurance_details);
        $insuranceDetails['insurance_certificate_paths'] = $this->linesToArray($validated['insurancePathsText']);
        $insuranceDetails['insurance_certificate_expiry_date'] = $validated['insuranceExpiryDate'];

        $profile->update(['insurance_details' => $insuranceDetails]);

        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    private function profile(): ?GroomerSpacerProfile
    {
        $profile = auth('groomer_spacer')->user();

        if ($profile instanceof GroomerSpacerProfile) {
            return $profile;
        }

        $user = auth()->user();
        $email = (string) ($user->email ?? '');

        return $email !== '' ? GroomerSpacerProfile::whereEmail($email)->first() : null;
    }

    private function hydrateEditableFields(): void
    {
        $profile = $this->profile();
        $businessDetails = $this->arrayValue($profile?->business_details);
        $businessBasics = $this->arrayValue($profile?->business_basics);
        $payoutDetails = $this->arrayValue($profile?->payout_details);
        $insuranceDetails = $this->arrayValue($profile?->insurance_details);

        $this->fullName = (string) ($profile?->full_name ?? '');
        $this->email = (string) ($profile?->email ?? '');
        $this->businessPhone = (string) ($businessDetails['business_phone'] ?? '');

        $this->businessName = (string) ($businessDetails['business_name'] ?? ($businessBasics['display_name'] ?? ''));
        $this->businessRegistrationNumber = (string) ($businessDetails['business_registration_number'] ?? '');
        $this->tagline = (string) ($businessBasics['tagline'] ?? '');
        $this->bio = (string) ($businessBasics['bio'] ?? '');
        $this->profilePhotoPath = (string) ($businessBasics['profile_photo_path'] ?? '');

        $this->galleryPaths = $this->arrayStrings($businessBasics['gallery_paths'] ?? []);
        $this->galleryPathsText = implode("\n", $this->galleryPaths);

        $this->accountHolderName = (string) ($payoutDetails['account_holder_name'] ?? '');
        $this->accountNumber = (string) ($payoutDetails['account_number'] ?? '');
        $this->sortCode = (string) ($payoutDetails['sort_code'] ?? '');
        $this->iban = (string) ($payoutDetails['iban'] ?? '');

        $this->businessIdPathsText = implode("\n", $this->arrayStrings($businessDetails['business_owner_id_images'] ?? ($profile?->id_document_paths ?? [])));
        $this->insurancePathsText = implode("\n", $this->arrayStrings($insuranceDetails['insurance_certificate_paths'] ?? []));
        $this->insuranceExpiryDate = (string) ($this->dateString($this->firstString($insuranceDetails, ['expires_at', 'expiry_date', 'expiration_date', 'insurance_expiry_date', 'insurance_certificate_expiry_date', 'insurance_certificate_expires_at'])) ?? '');
    }

    private function arrayValue(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value) && $value !== '') {
            return json_decode($value, true) ?: [];
        }

        return [];
    }

    private function arrayStrings(mixed $value): array
    {
        if (is_string($value) && $value !== '') {
            $decoded = json_decode($value, true);
            $value = is_array($decoded) ? $decoded : [$value];
        }

        if (!is_array($value)) {
            return [];
        }

        return array_values(array_filter($value, fn($item) => is_string($item) && trim($item) !== ''));
    }

    private function linesToArray(string $value): array
    {
        return array_values(array_filter(array_map('trim', preg_split('/\R/', $value) ?: []), fn($line) => $line !== ''));
    }

    private function fileCards(mixed $paths, int $limit, array $metadata = []): array
    {
        if (is_string($paths) && $paths !== '') {
            $paths = json_decode($paths, true) ?: [$paths];
        }

        if (!is_array($paths)) {
            return [];
        }

        return collect($paths)->filter(fn($path) => is_string($path) && trim($path) !== '')->take($limit)->map(fn($path) => $this->fileCard($path, $metadata))->values()->all();
    }

    private function fileCard(mixed $path, array $metadata = []): ?array
    {
        if (!is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        $expiresAt = $this->dateString($metadata['expires_at'] ?? null);
        $expired = $this->isExpired($expiresAt);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $urlPath = (string) (parse_url($path, PHP_URL_PATH) ?: '');
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));

            return [
                'path' => $path,
                'name' => basename($urlPath) ?: 'profile-picture',
                'url' => $path,
                'size' => null,
                'uploaded' => null,
                'extension' => $extension,
                'is_image' => true,
                'available' => true,
                'expires_at' => $expiresAt,
                'expired' => $expired,
            ];
        }

        $publicPath = $this->normalizePublicAssetPath($path);
        $publicExists = $publicPath !== '' && file_exists(public_path($publicPath));
        $publicExtension = strtolower(pathinfo($publicPath, PATHINFO_EXTENSION));
        if ($publicExists && in_array($publicExtension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'], true)) {
            return [
                'path' => $path,
                'name' => basename($publicPath),
                'url' => asset($publicPath),
                'size' => filesize(public_path($publicPath)) ?: null,
                'uploaded' => date('d M Y', filemtime(public_path($publicPath))),
                'extension' => $publicExtension,
                'is_image' => true,
                'available' => true,
                'expires_at' => $expiresAt,
                'expired' => $expired,
            ];
        }

        $normalizedPath = $this->normalizePublicDiskPath($path);
        $exists = $normalizedPath !== '' && Storage::disk('public')->exists($normalizedPath);
        $extension = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?: $normalizedPath ?: $path, PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'], true);

        return [
            'path' => $path,
            'name' => basename($normalizedPath ?: $path),
            'url' => $isImage ? Storage::url($normalizedPath) : ($exists ? Storage::url($normalizedPath) : null),
            'size' => $exists ? $this->formatFileSize(Storage::disk('public')->size($normalizedPath)) : null,
            'uploaded' => $exists ? date('d M Y', Storage::disk('public')->lastModified($normalizedPath)) : null,
            'extension' => $extension,
            'is_image' => $isImage,
            'available' => $exists,
            'expires_at' => $expiresAt,
            'expired' => $expired,
        ];
    }

    private function filesHaveIssue(array $files): bool
    {
        if ($files === []) {
            return true;
        }

        return collect($files)->contains(fn(array $file) => !($file['available'] ?? false) || ($file['expired'] ?? false));
    }

    private function firstString(array $source, array $keys): ?string
    {
        foreach ($keys as $key) {
            $value = $source[$key] ?? null;
            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return null;
    }

    private function dateString(mixed $value): ?string
    {
        if (!is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    private function isExpired(?string $date): bool
    {
        if ($date === null) {
            return false;
        }

        return Carbon::parse($date)->isBefore(today());
    }

    private function normalizePublicDiskPath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));
        $path = ltrim($path, '/');

        foreach (['public/', 'storage/'] as $prefix) {
            if (str_starts_with($path, $prefix)) {
                $path = substr($path, strlen($prefix));
            }
        }

        return $path;
    }

    private function normalizePublicAssetPath(string $path): string
    {
        $path = trim(str_replace('\\', '/', $path));
        $path = ltrim($path, '/');

        if (str_starts_with($path, 'public/')) {
            $path = substr($path, strlen('public/'));
        }

        return $path;
    }

    private function formatFileSize(int|float $bytes): string
    {
        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 1) . ' MB';
        }

        return max(1, (int) ceil($bytes / 1024)) . ' KB';
    }
}; ?>

<div class="business-details-settings" x-data="{ showBusinessDetailsAlert: true, editingSection: @js($editingSection) }">
    <div class="business-details-heading">
        <div>
            <h2>Business Details</h2>
            <p>Keep your business information and compliance details up to date.</p>
        </div>
    </div>

    <div class="business-details-alert" x-show="showBusinessDetailsAlert" x-cloak
        x-transition:enter="business-details-alert-enter" x-transition:enter-start="business-details-alert-enter-start"
        x-transition:enter-end="business-details-alert-enter-end" x-transition:leave="business-details-alert-leave"
        x-transition:leave-start="business-details-alert-leave-start"
        x-transition:leave-end="business-details-alert-leave-end" role="status">
        <span class="business-details-alert__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="3" height="14" viewBox="0 0 3 14" fill="none">
                <path
                    d="M2.196 0V5.148C2.196 5.688 2.172 6.219 2.124 6.741C2.076 7.257 2.013 7.815 1.935 8.415H0.63C0.546 7.815 0.48 7.257 0.432 6.741C0.39 6.219 0.369 5.688 0.369 5.148V0H2.196ZM0 11.844C0 11.67 0.03 11.508 0.09 11.358C0.156 11.202 0.246 11.067 0.36 10.953C0.474 10.839 0.606 10.749 0.756 10.683C0.906 10.617 1.071 10.584 1.251 10.584C1.425 10.584 1.587 10.617 1.737 10.683C1.893 10.749 2.025 10.839 2.133 10.953C2.247 11.067 2.337 11.202 2.403 11.358C2.469 11.508 2.502 11.67 2.502 11.844C2.502 12.024 2.469 12.189 2.403 12.339C2.337 12.489 2.247 12.621 2.133 12.735C2.025 12.849 1.893 12.936 1.737 12.996C1.587 13.062 1.425 13.095 1.251 13.095C1.071 13.095 0.906 13.062 0.756 12.996C0.606 12.936 0.474 12.849 0.36 12.735C0.246 12.621 0.156 12.489 0.09 12.339C0.03 12.189 0 12.024 0 11.844Z"
                    fill="white" />
            </svg>
        </span>
        <div>
            <strong>Keeping your compliance information current ensures trust and platform safety.</strong>
            <span>Please review and update your details regularly.</span>
        </div>
        <button type="button" class="business-details-alert__close" @click="showBusinessDetailsAlert = false"
            aria-label="Dismiss compliance information alert"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                height="14" viewBox="0 0 24 14" fill="none" aria-hidden="true">
                <path
                    d="M6.65625 13.7071C6.26572 14.0976 5.63256 14.0976 5.24204 13.7071C4.85151 13.3166 4.85151 12.6834 5.24204 12.2929L5.94914 13L6.65625 13.7071ZM11.9999 6.94921L12.707 6.24211C13.0975 6.63263 13.0975 7.2658 12.707 7.65632L11.9999 6.94921ZM5.34361 1.70711C4.95309 1.31658 4.95309 0.683417 5.34361 0.292892C5.73413 -0.0976337 6.3673 -0.0976336 6.75782 0.292892L6.05072 1L5.34361 1.70711ZM5.94914 13L5.24204 12.2929L11.2928 6.24211L11.9999 6.94921L12.707 7.65632L6.65625 13.7071L5.94914 13ZM11.9999 6.94921L11.2928 7.65632L5.34361 1.70711L6.05072 1L6.75782 0.292892L12.707 6.24211L11.9999 6.94921Z"
                    fill="#B4CCDD" />
                <path
                    d="M17.3025 13.7071C17.693 14.0976 18.3262 14.0976 18.7167 13.7071C19.1072 13.3166 19.1072 12.6834 18.7167 12.2929L18.0096 13L17.3025 13.7071ZM11.9588 6.94921L11.2517 6.24211C10.8612 6.63263 10.8612 7.2658 11.2517 7.65632L11.9588 6.94921ZM18.6151 1.70711C19.0056 1.31658 19.0056 0.683417 18.6151 0.292892C18.2246 -0.0976337 17.5914 -0.0976336 17.2009 0.292892L17.908 1L18.6151 1.70711ZM18.0096 13L18.7167 12.2929L12.6659 6.24211L11.9588 6.94921L11.2517 7.65632L17.3025 13.7071L18.0096 13ZM11.9588 6.94921L12.6659 7.65632L18.6151 1.70711L17.908 1L17.2009 0.292892L11.2517 6.24211L11.9588 6.94921Z"
                    fill="#B4CCDD" />
            </svg></button>
    </div>

    <section class="business-details-block">
        <x-dashboard.settings.business-details.section-title title="Personal Details" section="personal"
            :is-editing="$editingSection === 'personal'" save-action="savePersonalDetails" />
        <div @class([
            'business-details-card',
            'business-details-grid',
            'business-details-grid--three',
            'business-details-card--editing' => $editingSection === 'personal',
        ]) :class="{ 'business-details-card--editing': editingSection === 'personal' }">
            <div class="business-details-toggle-panel" x-cloak x-show="editingSection === 'personal'">
                <div class="business-details-edit-grid business-details-edit-grid--three">
                    <label class="business-details-input-field">
                        <span>Full Name (must match ID)</span>
                        <input type="text" wire:model.defer="fullName">
                    </label>
                    <label class="business-details-input-field">
                        <span>Email Address</span>
                        <input type="email" wire:model.defer="email">
                    </label>
                    <label class="business-details-input-field">
                        <span>Phone Number</span>
                        <input type="text" wire:model.defer="businessPhone">
                    </label>
                </div>
            </div>
            <div class="business-details-toggle-panel" x-show="editingSection !== 'personal'">
                <div class="business-details-edit-grid business-details-edit-grid--three">
                    <x-dashboard.settings.business-details.field label="Full Name (must match ID)" :value="$profile?->full_name"
                        placeholder="Not provided" />
                    <x-dashboard.settings.business-details.field label="Email Address" :value="$profile?->email"
                        placeholder="Not provided" />
                    <x-dashboard.settings.business-details.field label="Phone Number" :value="$businessDetails['business_phone'] ?? null"
                        placeholder="Not provided" />
                </div>
            </div>
        </div>
    </section>

    <section class="business-details-block">
        <x-dashboard.settings.business-details.section-title title="Business Details" section="business"
            :is-editing="$editingSection === 'business'" save-action="saveBusinessDetails" />
        <div @class([
            'business-details-card',
            'business-details-profile',
            'business-details-card--editing' => $editingSection === 'business',
        ]) :class="{ 'business-details-card--editing': editingSection === 'business' }">
            <div>
                <span class="business-details-label">Business Profile Image</span>
                <div class="business-details-avatar" x-show="editingSection !== 'business'">
                    @if ($profileImage && $profileImage['is_image'])
                        <img src="{{ $profileImage['url'] }}" alt="Business profile image">
                    @else
                        <span class="business-details-paw" aria-hidden="true">paw</span>
                    @endif
                </div>
                <div class="business-details-avatar-upload" wire:key="business-profile-image-uploader"
                    x-data="{
                        uploading: false,
                        progress: 0,
                        targetProgress: 0,
                        progressFrame: null,
                        previewUrl: null,
                        startProgress() {
                            this.cancelProgressFrame();
                            this.uploading = true;
                            this.progress = 0;
                            this.targetProgress = 1;
                            this.setProgress(1);
                        },
                        setProgress(value) {
                            const nextTarget = Math.max(this.targetProgress, Math.min(100, Number(value || 0)));
                            const startValue = this.progress;
                            const delta = nextTarget - startValue;
                    
                            if (delta <= 0) {
                                return;
                            }
                    
                            this.targetProgress = nextTarget;
                            this.cancelProgressFrame();
                    
                            const startedAt = performance.now();
                            const duration = Math.min(900, Math.max(260, delta * 12));
                            const easeInOut = (t) => t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
                    
                            const step = (now) => {
                                const elapsed = Math.min(1, (now - startedAt) / duration);
                                this.progress = startValue + delta * easeInOut(elapsed);
                    
                                if (elapsed < 1) {
                                    this.progressFrame = requestAnimationFrame(step);
                                    return;
                                }
                    
                                this.progress = nextTarget;
                                this.progressFrame = null;
                    
                                if (this.progress >= 100 && this.targetProgress >= 100) {
                                    setTimeout(() => {
                                        this.uploading = false;
                                        this.progress = 0;
                                        this.targetProgress = 0;
                                    }, 180);
                                }
                            };
                    
                            this.progressFrame = requestAnimationFrame(step);
                        },
                        cancelProgressFrame() {
                            if (this.progressFrame) {
                                cancelAnimationFrame(this.progressFrame);
                                this.progressFrame = null;
                            }
                        },
                        resetProgress() {
                            this.cancelProgressFrame();
                            this.uploading = false;
                            this.progress = 0;
                            this.targetProgress = 0;
                        },
                    }" x-cloak x-show="editingSection === 'business'"
                    x-on:livewire-upload-start="startProgress()" x-on:livewire-upload-finish="setProgress(100)"
                    x-on:livewire-upload-error="resetProgress()"
                    x-on:livewire-upload-progress="setProgress($event.detail.progress)">
                    <div class="business-details-avatar-upload__icon" aria-hidden="true">
                        <img x-cloak x-show="previewUrl" :src="previewUrl" alt="Selected business profile image">
                        @if ($profileImage && $profileImage['is_image'])
                            <img x-show="!previewUrl" src="{{ $profileImage['url'] }}" alt="Business profile image">
                        @else
                            <svg x-show="!previewUrl" xmlns="http://www.w3.org/2000/svg" width="88" height="88"
                                viewBox="0 0 88 88" fill="none">
                                <circle cx="44" cy="44" r="44" fill="#DFDFDF" />
                                <circle cx="44" cy="32" r="14" fill="#FFFFFF" />
                                <path d="M17.5 88C19.5 68.5 30.2 57.5 44 57.5C57.8 57.5 68.5 68.5 70.5 88H17.5Z"
                                    fill="#FFFFFF" />
                            </svg>
                        @endif
                        @unless ($profileImage && $profileImage['is_image'])
                            <span class="business-details-avatar-upload__plus" x-show="!previewUrl">+</span>
                        @endunless
                    </div>
                    <div class="business-details-avatar-upload__copy">
                        <span>Upload Image</span>
                        <span>Max file size: 1 MB</span>
                    </div>
                    <input x-ref="profilePhotoInput" type="file" class="business-details-avatar-upload__input"
                        wire:model="profilePhotoUpload" accept="image/*"
                        @change="previewUrl && URL.revokeObjectURL(previewUrl); previewUrl = $event.target.files[0] ? URL.createObjectURL($event.target.files[0]) : null">
                    <button type="button" class="business-details-avatar-upload__button"
                        @click="$refs.profilePhotoInput.click()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"
                            stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 16V4" />
                            <path d="m7 9 5-5 5 5" />
                            <path d="M20 16v4H4v-4" />
                        </svg>
                        <span wire:loading.remove wire:target="profilePhotoUpload" x-show="!uploading">Upload
                            Photo</span>
                        <span class="business-details-avatar-upload__button-progress" x-cloak x-show="uploading">
                            <span class="business-details-avatar-upload__progress-bar">
                                <span :style="`width: ${progress}%`"></span>
                            </span>
                            <span x-text="`${Math.round(progress)}%`"></span>
                        </span>
                    </button>
                    @error('profilePhotoUpload')
                        <span class="business-details-avatar-upload__error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="business-details-profile__copy">
                <div class="business-details-toggle-panel" x-cloak x-show="editingSection === 'business'">
                    <div class="business-details-edit-stack">
                        <label class="business-details-input-field">
                            <span>Business Name</span>
                            <input type="text" wire:model.defer="businessName">
                        </label>
                        <label class="business-details-input-field">
                            <span>Business Registration Number</span>
                            <input type="text" wire:model.defer="businessRegistrationNumber">
                        </label>
                        <label class="business-details-input-field">
                            <span>Tagline</span>
                            <input type="text" wire:model.defer="tagline">
                        </label>
                        <label class="business-details-input-field">
                            <span>Bio</span>
                            <textarea rows="3" wire:model.defer="bio"></textarea>
                        </label>
                    </div>
                </div>
                <div class="business-details-toggle-panel" x-show="editingSection !== 'business'">
                    <div class="business-details-edit-stack">
                        <x-dashboard.settings.business-details.field label="Business Name" :value="$businessDetails['business_name'] ?? ($businessBasics['display_name'] ?? null)"
                            placeholder="Not provided" />
                        <x-dashboard.settings.business-details.field label="Business Registration Number"
                            :value="$businessDetails['business_registration_number'] ?? null" placeholder="Not provided" />
                        <x-dashboard.settings.business-details.field label="Tagline" :value="$businessBasics['tagline'] ?? null"
                            placeholder="Not provided" />
                        <x-dashboard.settings.business-details.field label="Bio" :value="$businessBasics['bio'] ?? null"
                            placeholder="Not provided" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="business-details-block">
        @php
            $isSpaceGallery = strtolower((string) ($profile?->user_type ?? '')) === 'space';
        @endphp
        <x-dashboard.settings.business-details.section-title title="Photo Gallery" section="gallery" :is-editing="$editingSection === 'gallery'"
            save-action="saveGalleryDetails" />
        <div class="business-details-fade-panel" x-cloak x-show="editingSection === 'gallery'">
            <div class="business-details-gallery business-details-gallery--editable">
                @php
                    $galleryCount = count($gallery);
                    $nextAddSlot = $galleryCount;
                @endphp
                @for ($i = 0; $i <= $galleryCount; $i++)
                    @php
                        $image = $gallery[$i] ?? null;
                        $hasUsableImage = $image && ($image['is_image'] ?? false) && ($image['available'] ?? true) && !empty($image['url']);
                        $hasExistingPath = $image && !empty($image['path']);
                        $shouldShowSlot = $i < $galleryCount || $i === $nextAddSlot;
                    @endphp
                    @if ($shouldShowSlot)
                    <div class="business-details-gallery__slot"
                        wire:key="business-gallery-edit-slot-{{ $i }}-{{ md5((string) ($image['path'] ?? 'add-slot')) }}"
                        x-data="{
                        previewUrl: null,
                        uploading: false,
                        removing: false,
                        progress: 0,
                        targetProgress: 0,
                        progressFrame: null,
                        pick() {
                            this.progress = 0;
                            this.targetProgress = 0;
                            $wire.set('galleryReplaceIndex', {{ $hasExistingPath ? $i : 'null' }}).then(() => {
                                this.$refs.galleryUploadInput.click();
                            });
                        },
                        preview(event) {
                            if (this.previewUrl) {
                                URL.revokeObjectURL(this.previewUrl);
                            }
                            const file = event.target.files[0] || null;
                            this.previewUrl = file ? URL.createObjectURL(file) : null;
                        },
                        startProgress() {
                            this.cancelProgressFrame();
                            this.uploading = true;
                            this.progress = 1;
                            this.targetProgress = 1;
                        },
                        setProgress(value) {
                            const nextTarget = Math.max(this.targetProgress, Math.min(100, Number(value || 0)));
                            const startValue = this.progress;
                            const delta = nextTarget - startValue;
                    
                            if (delta <= 0) {
                                return;
                            }
                    
                            this.targetProgress = nextTarget;
                            this.cancelProgressFrame();
                    
                            const startedAt = performance.now();
                            const duration = Math.min(900, Math.max(260, delta * 14));
                            const easeInOut = (t) => t < 0.5 ? 2 * t * t : 1 - Math.pow(-2 * t + 2, 2) / 2;
                    
                            const step = (now) => {
                                const elapsed = Math.min(1, (now - startedAt) / duration);
                                this.progress = startValue + delta * easeInOut(elapsed);
                    
                                if (elapsed < 1) {
                                    this.progressFrame = requestAnimationFrame(step);
                                    return;
                                }
                    
                                this.progress = nextTarget;
                                this.progressFrame = null;
                    
                                if (this.progress >= 100 && this.targetProgress >= 100) {
                                    setTimeout(() => {
                                        this.uploading = false;
                                        this.progress = 0;
                                        this.targetProgress = 0;
                                    }, 250);
                                }
                            };
                    
                            this.progressFrame = requestAnimationFrame(step);
                        },
                        cancelProgressFrame() {
                            if (this.progressFrame) {
                                cancelAnimationFrame(this.progressFrame);
                                this.progressFrame = null;
                            }
                        },
                        reset() {
                            this.cancelProgressFrame();
                            this.uploading = false;
                            this.progress = 0;
                            this.targetProgress = 0;
                        },
                        remove(index) {
                            if (this.removing) {
                                return;
                            }
                            this.removing = true;
                            setTimeout(() => $wire.removeGalleryImage(index), 240);
                        },
                    }" x-on:livewire-upload-start="startProgress()"
                        x-on:livewire-upload-progress="setProgress($event.detail.progress)"
                        x-on:livewire-upload-finish="setProgress(100)"
                        x-on:livewire-upload-error="reset()" :class="{ 'business-details-gallery__slot--removing': removing }">
                    <input x-ref="galleryUploadInput" type="file" class="business-details-gallery__input"
                        wire:model="galleryUpload" accept="image/*" @change="preview($event)">
                    <button type="button" class="business-details-gallery__item business-details-gallery__upload-tile"
                        @click="pick()"
                        aria-label="{{ $hasUsableImage ? 'Replace gallery image ' . ($i + 1) : 'Upload gallery image ' . ($i + 1) }}">
                        <template x-if="previewUrl">
                            <span class="business-details-gallery__preview">
                                <img :src="previewUrl" alt="Selected gallery image">
                                <span class="business-details-gallery-progress" x-cloak x-show="uploading"
                                    :style="`--progress: ${progress}`">
                                    <span x-text="`${Math.round(progress)}%`"></span>
                                </span>
                            </span>
                        </template>
                        <span x-show="!previewUrl" class="business-details-gallery__current">
                            @if ($hasUsableImage)
                                <img src="{{ $image['url'] }}" alt="Business gallery image {{ $i + 1 }}"
                                    onerror="this.hidden = true; this.nextElementSibling.hidden = false;">
                                <span class="business-details-gallery-add" hidden aria-hidden="true">
                                    @if ($isSpaceGallery)
                                        <x-dashboard.settings.business-details.space-gallery-placeholder />
                                    @else
                                        <svg class="business-details-gallery-paw" xmlns="http://www.w3.org/2000/svg"
                                        width="61" height="48" viewBox="0 0 61 48" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M19.5692 0C17.4166 0 15.7293 1.29255 14.6856 2.84275C13.6289 4.40583 13.0461 6.44557 13.0461 8.58837C13.0461 10.7312 13.6289 12.7709 14.6856 14.334C15.7293 15.8799 17.4166 17.1767 19.5692 17.1767C21.7218 17.1767 23.4091 15.8842 24.4528 14.334C25.5096 12.7709 26.0923 10.7312 26.0923 8.58837C26.0923 6.44557 25.5096 4.40583 24.4528 2.84275C23.4091 1.29684 21.7218 0 19.5692 0ZM41.3128 0C39.1602 0 37.4729 1.29255 36.4292 2.84275C35.3724 4.40583 34.7897 6.44557 34.7897 8.58837C34.7897 10.7312 35.3724 12.7709 36.4292 14.334C37.4729 15.8799 39.1602 17.1767 41.3128 17.1767C43.4654 17.1767 45.1527 15.8842 46.1964 14.334C47.2531 12.7709 47.8359 10.7312 47.8359 8.58837C47.8359 6.44557 47.2531 4.40583 46.1964 2.84275C45.1527 1.29684 43.4654 0 41.3128 0ZM6.52307 19.3238C4.37046 19.3238 2.68316 20.6164 1.63947 22.1666C0.582728 23.7297 0 25.7694 0 27.9122C0 30.055 0.582728 32.0947 1.63947 33.6578C2.68316 35.2037 4.37046 36.5006 6.52307 36.5006C8.67568 36.5006 10.363 35.208 11.4067 33.6578C12.4634 32.0947 13.0461 30.055 13.0461 27.9122C13.0461 25.7694 12.4634 23.7297 11.4067 22.1666C10.363 20.6207 8.67568 19.3238 6.52307 19.3238ZM30.441 19.3238C25.2225 19.3238 21.3565 22.0893 18.8865 25.5203C16.4468 28.8999 15.2205 33.0953 15.2205 36.5006C15.2205 40.4684 17.634 43.2296 20.5955 44.8828C23.5091 46.5146 27.1621 47.236 30.441 47.236C33.7199 47.236 37.3728 46.5189 40.2865 44.8828C43.2436 43.2253 45.6615 40.4684 45.6615 36.5006C45.6615 33.0953 44.4352 28.8999 41.9955 25.5203C39.5298 22.085 35.6638 19.3238 30.441 19.3238ZM54.3589 19.3238C52.2063 19.3238 50.519 20.6164 49.4753 22.1666C48.4186 23.7297 47.8359 25.7694 47.8359 27.9122C47.8359 30.055 48.4186 32.0947 49.4753 33.6578C50.519 35.2037 52.2063 36.5006 54.3589 36.5006C56.5115 36.5006 58.1988 35.208 59.2425 33.6578C60.2993 32.0947 60.882 30.055 60.882 27.9122C60.882 25.7694 60.2993 23.7297 59.2425 22.1666C58.1988 20.6207 56.5115 19.3238 54.3589 19.3238Z"
                                            fill="#E5E5E5" />
                                        </svg>
                                    @endif
                                    <span class="business-details-gallery-add__plus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                            <path
                                                d="M9.72378 19.4476C4.36181 19.4476 0 15.0857 0 9.72378C0 4.36181 4.36181 0 9.72378 0C15.0857 0 19.4476 4.36181 19.4476 9.72378C19.4476 15.0857 15.0857 19.4476 9.72378 19.4476Z"
                                                fill="#9D9B98" />
                                        </svg>
                                    </span>
                                </span>
                            @else
                                <span class="business-details-gallery-add" aria-hidden="true">
                                    @if ($isSpaceGallery)
                                        <x-dashboard.settings.business-details.space-gallery-placeholder />
                                    @else
                                        <svg class="business-details-gallery-paw" xmlns="http://www.w3.org/2000/svg"
                                        width="61" height="48" viewBox="0 0 61 48" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M19.5692 0C17.4166 0 15.7293 1.29255 14.6856 2.84275C13.6289 4.40583 13.0461 6.44557 13.0461 8.58837C13.0461 10.7312 13.6289 12.7709 14.6856 14.334C15.7293 15.8799 17.4166 17.1767 19.5692 17.1767C21.7218 17.1767 23.4091 15.8842 24.4528 14.334C25.5096 12.7709 26.0923 10.7312 26.0923 8.58837C26.0923 6.44557 25.5096 4.40583 24.4528 2.84275C23.4091 1.29684 21.7218 0 19.5692 0ZM41.3128 0C39.1602 0 37.4729 1.29255 36.4292 2.84275C35.3724 4.40583 34.7897 6.44557 34.7897 8.58837C34.7897 10.7312 35.3724 12.7709 36.4292 14.334C37.4729 15.8799 39.1602 17.1767 41.3128 17.1767C43.4654 17.1767 45.1527 15.8842 46.1964 14.334C47.2531 12.7709 47.8359 10.7312 47.8359 8.58837C47.8359 6.44557 47.2531 4.40583 46.1964 2.84275C45.1527 1.29684 43.4654 0 41.3128 0ZM6.52307 19.3238C4.37046 19.3238 2.68316 20.6164 1.63947 22.1666C0.582728 23.7297 0 25.7694 0 27.9122C0 30.055 0.582728 32.0947 1.63947 33.6578C2.68316 35.2037 4.37046 36.5006 6.52307 36.5006C8.67568 36.5006 10.363 35.208 11.4067 33.6578C12.4634 32.0947 13.0461 30.055 13.0461 27.9122C13.0461 25.7694 12.4634 23.7297 11.4067 22.1666C10.363 20.6207 8.67568 19.3238 6.52307 19.3238ZM30.441 19.3238C25.2225 19.3238 21.3565 22.0893 18.8865 25.5203C16.4468 28.8999 15.2205 33.0953 15.2205 36.5006C15.2205 40.4684 17.634 43.2296 20.5955 44.8828C23.5091 46.5146 27.1621 47.236 30.441 47.236C33.7199 47.236 37.3728 46.5189 40.2865 44.8828C43.2436 43.2253 45.6615 40.4684 45.6615 36.5006C45.6615 33.0953 44.4352 28.8999 41.9955 25.5203C39.5298 22.085 35.6638 19.3238 30.441 19.3238ZM54.3589 19.3238C52.2063 19.3238 50.519 20.6164 49.4753 22.1666C48.4186 23.7297 47.8359 25.7694 47.8359 27.9122C47.8359 30.055 48.4186 32.0947 49.4753 33.6578C50.519 35.2037 52.2063 36.5006 54.3589 36.5006C56.5115 36.5006 58.1988 35.208 59.2425 33.6578C60.2993 32.0947 60.882 30.055 60.882 27.9122C60.882 25.7694 60.2993 23.7297 59.2425 22.1666C58.1988 20.6207 56.5115 19.3238 54.3589 19.3238Z"
                                            fill="#E5E5E5" />
                                        </svg>
                                    @endif
                                    <span class="business-details-gallery-add__plus">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                            viewBox="0 0 20 20" fill="none" aria-hidden="true">
                                            <path
                                                d="M9.72378 19.4476C4.36181 19.4476 0 15.0857 0 9.72378C0 4.36181 4.36181 0 9.72378 0C15.0857 0 19.4476 4.36181 19.4476 9.72378C19.4476 15.0857 15.0857 19.4476 9.72378 19.4476Z"
                                                fill="#9D9B98" />
                                        </svg>
                                    </span>
                                </span>
                            @endif
                        </span>
                    </button>
                    @if ($hasExistingPath)
                        <span class="business-details-gallery-remove-spinner" x-cloak x-show="removing"
                            aria-hidden="true"></span>
                        <button type="button" class="business-details-gallery-remove"
                            @click.stop="remove({{ $i }})" :disabled="removing"
                            aria-label="Remove gallery image {{ $i + 1 }}">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <path d="M1.2 1.2L10.8 10.8M10.8 1.2L1.2 10.8" stroke="currentColor"
                                    stroke-width="1.8" stroke-linecap="round" />
                            </svg>
                        </button>
                    @endif
                    </div>
                    @endif
                @endfor
            </div>
            @error('galleryUpload')
                <span class="business-details-avatar-upload__error">{{ $message }}</span>
            @enderror
        </div>
        <div class="business-details-gallery business-details-fade-panel" x-show="editingSection !== 'gallery'">
            @foreach ($gallery as $i => $image)
                @php
                    $hasUsableImage = $image && ($image['is_image'] ?? false) && ($image['available'] ?? true) && !empty($image['url']);
                @endphp
                <div class="business-details-gallery__item">
                    @if ($hasUsableImage)
                        <img src="{{ $image['url'] }}" alt="Business gallery image {{ $i + 1 }}"
                            onerror="this.hidden = true; this.nextElementSibling.hidden = false;">
                        <span class="business-details-gallery-add" hidden aria-hidden="true">
                            @if ($isSpaceGallery)
                                <x-dashboard.settings.business-details.space-gallery-placeholder />
                            @else
                                <svg class="business-details-gallery-paw" xmlns="http://www.w3.org/2000/svg"
                                width="61" height="48" viewBox="0 0 61 48" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M19.5692 0C17.4166 0 15.7293 1.29255 14.6856 2.84275C13.6289 4.40583 13.0461 6.44557 13.0461 8.58837C13.0461 10.7312 13.6289 12.7709 14.6856 14.334C15.7293 15.8799 17.4166 17.1767 19.5692 17.1767C21.7218 17.1767 23.4091 15.8842 24.4528 14.334C25.5096 12.7709 26.0923 10.7312 26.0923 8.58837C26.0923 6.44557 25.5096 4.40583 24.4528 2.84275C23.4091 1.29684 21.7218 0 19.5692 0ZM41.3128 0C39.1602 0 37.4729 1.29255 36.4292 2.84275C35.3724 4.40583 34.7897 6.44557 34.7897 8.58837C34.7897 10.7312 35.3724 12.7709 36.4292 14.334C37.4729 15.8799 39.1602 17.1767 41.3128 17.1767C43.4654 17.1767 45.1527 15.8842 46.1964 14.334C47.2531 12.7709 47.8359 10.7312 47.8359 8.58837C47.8359 6.44557 47.2531 4.40583 46.1964 2.84275C45.1527 1.29684 43.4654 0 41.3128 0ZM6.52307 19.3238C4.37046 19.3238 2.68316 20.6164 1.63947 22.1666C0.582728 23.7297 0 25.7694 0 27.9122C0 30.055 0.582728 32.0947 1.63947 33.6578C2.68316 35.2037 4.37046 36.5006 6.52307 36.5006C8.67568 36.5006 10.363 35.208 11.4067 33.6578C12.4634 32.0947 13.0461 30.055 13.0461 27.9122C13.0461 25.7694 12.4634 23.7297 11.4067 22.1666C10.363 20.6207 8.67568 19.3238 6.52307 19.3238ZM30.441 19.3238C25.2225 19.3238 21.3565 22.0893 18.8865 25.5203C16.4468 28.8999 15.2205 33.0953 15.2205 36.5006C15.2205 40.4684 17.634 43.2296 20.5955 44.8828C23.5091 46.5146 27.1621 47.236 30.441 47.236C33.7199 47.236 37.3728 46.5189 40.2865 44.8828C43.2436 43.2253 45.6615 40.4684 45.6615 36.5006C45.6615 33.0953 44.4352 28.8999 41.9955 25.5203C39.5298 22.085 35.6638 19.3238 30.441 19.3238ZM54.3589 19.3238C52.2063 19.3238 50.519 20.6164 49.4753 22.1666C48.4186 23.7297 47.8359 25.7694 47.8359 27.9122C47.8359 30.055 48.4186 32.0947 49.4753 33.6578C50.519 35.2037 52.2063 36.5006 54.3589 36.5006C56.5115 36.5006 58.1988 35.208 59.2425 33.6578C60.2993 32.0947 60.882 30.055 60.882 27.9122C60.882 25.7694 60.2993 23.7297 59.2425 22.1666C58.1988 20.6207 56.5115 19.3238 54.3589 19.3238Z"
                                    fill="#E5E5E5" />
                                </svg>
                            @endif
                        </span>
                    @else
                        <span class="business-details-gallery-add" aria-hidden="true">
                            @if ($isSpaceGallery)
                                <x-dashboard.settings.business-details.space-gallery-placeholder />
                            @else
                                <svg class="business-details-gallery-paw" xmlns="http://www.w3.org/2000/svg"
                                width="61" height="48" viewBox="0 0 61 48" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M19.5692 0C17.4166 0 15.7293 1.29255 14.6856 2.84275C13.6289 4.40583 13.0461 6.44557 13.0461 8.58837C13.0461 10.7312 13.6289 12.7709 14.6856 14.334C15.7293 15.8799 17.4166 17.1767 19.5692 17.1767C21.7218 17.1767 23.4091 15.8842 24.4528 14.334C25.5096 12.7709 26.0923 10.7312 26.0923 8.58837C26.0923 6.44557 25.5096 4.40583 24.4528 2.84275C23.4091 1.29684 21.7218 0 19.5692 0ZM41.3128 0C39.1602 0 37.4729 1.29255 36.4292 2.84275C35.3724 4.40583 34.7897 6.44557 34.7897 8.58837C34.7897 10.7312 35.3724 12.7709 36.4292 14.334C37.4729 15.8799 39.1602 17.1767 41.3128 17.1767C43.4654 17.1767 45.1527 15.8842 46.1964 14.334C47.2531 12.7709 47.8359 10.7312 47.8359 8.58837C47.8359 6.44557 47.2531 4.40583 46.1964 2.84275C45.1527 1.29684 43.4654 0 41.3128 0ZM6.52307 19.3238C4.37046 19.3238 2.68316 20.6164 1.63947 22.1666C0.582728 23.7297 0 25.7694 0 27.9122C0 30.055 0.582728 32.0947 1.63947 33.6578C2.68316 35.2037 4.37046 36.5006 6.52307 36.5006C8.67568 36.5006 10.363 35.208 11.4067 33.6578C12.4634 32.0947 13.0461 30.055 13.0461 27.9122C13.0461 25.7694 12.4634 23.7297 11.4067 22.1666C10.363 20.6207 8.67568 19.3238 6.52307 19.3238ZM30.441 19.3238C25.2225 19.3238 21.3565 22.0893 18.8865 25.5203C16.4468 28.8999 15.2205 33.0953 15.2205 36.5006C15.2205 40.4684 17.634 43.2296 20.5955 44.8828C23.5091 46.5146 27.1621 47.236 30.441 47.236C33.7199 47.236 37.3728 46.5189 40.2865 44.8828C43.2436 43.2253 45.6615 40.4684 45.6615 36.5006C45.6615 33.0953 44.4352 28.8999 41.9955 25.5203C39.5298 22.085 35.6638 19.3238 30.441 19.3238ZM54.3589 19.3238C52.2063 19.3238 50.519 20.6164 49.4753 22.1666C48.4186 23.7297 47.8359 25.7694 47.8359 27.9122C47.8359 30.055 48.4186 32.0947 49.4753 33.6578C50.519 35.2037 52.2063 36.5006 54.3589 36.5006C56.5115 36.5006 58.1988 35.208 59.2425 33.6578C60.2993 32.0947 60.882 30.055 60.882 27.9122C60.882 25.7694 60.2993 23.7297 59.2425 22.1666C58.1988 20.6207 56.5115 19.3238 54.3589 19.3238Z"
                                    fill="#E5E5E5" />
                                </svg>
                            @endif
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
        <div class="business-details-gallery business-details-fade-panel" x-show="false" hidden>
            @forelse ($gallery as $i => $image)
                <div class="business-details-gallery__item">
                    @if ($image && $image['is_image'])
                        <img src="{{ $image['url'] }}" alt="Business gallery image {{ $i + 1 }}"
                            onerror="this.hidden = true; this.nextElementSibling.hidden = false;">
                        <span class="business-details-gallery-placeholder" hidden aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="61" height="48"
                                viewBox="0 0 61 48" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M19.5733 0C17.4202 0 15.7326 1.29282 14.6886 2.84334C13.6317 4.40674 13.0488 6.4469 13.0488 8.59015C13.0488 10.7334 13.6317 12.7735 14.6886 14.337C15.7326 15.8832 17.4202 17.1803 19.5733 17.1803C21.7263 17.1803 23.414 15.8875 24.4579 14.337C25.5148 12.7735 26.0977 10.7334 26.0977 8.59015C26.0977 6.4469 25.5148 4.40674 24.4579 2.84334C23.414 1.29711 21.7263 0 19.5733 0ZM41.3213 0C39.1683 0 37.4806 1.29282 36.4367 2.84334C35.3798 4.40674 34.7969 6.4469 34.7969 8.59015C34.7969 10.7334 35.3798 12.7735 36.4367 14.337C37.4806 15.8832 39.1683 17.1803 41.3213 17.1803C43.4744 17.1803 45.162 15.8875 46.2059 14.337C47.2629 12.7735 47.8457 10.7334 47.8457 8.59015C47.8457 6.4469 47.2629 4.40674 46.2059 2.84334C45.162 1.29711 43.4744 0 41.3213 0ZM6.52442 19.3278C4.37136 19.3278 2.68371 20.6206 1.6398 22.1712C0.582848 23.7346 0 25.7747 0 27.918C0 30.0612 0.582848 32.1014 1.6398 33.6648C2.68371 35.211 4.37136 36.5081 6.52442 36.5081C8.67748 36.5081 10.3651 35.2153 11.409 33.6648C12.466 32.1014 13.0488 30.0612 13.0488 27.918C13.0488 25.7747 12.466 23.7346 11.409 22.1712C10.3651 20.6249 8.67748 19.3278 6.52442 19.3278ZM30.4473 19.3278C25.2278 19.3278 21.361 22.0939 18.8904 25.5256C16.4502 28.9058 15.2236 33.1021 15.2236 36.5081C15.2236 40.4768 17.6377 43.2385 20.5998 44.8921C23.514 46.5242 27.1677 47.2458 30.4473 47.2458C33.7269 47.2458 37.3806 46.5285 40.2948 44.8921C43.2526 43.2342 45.6709 40.4768 45.6709 36.5081C45.6709 33.1021 44.4443 28.9058 42.0042 25.5256C39.538 22.0896 35.6712 19.3278 30.4473 19.3278ZM54.3702 19.3278C52.2171 19.3278 50.5295 20.6206 49.4855 22.1712C48.4286 23.7346 47.8457 25.7747 47.8457 27.918C47.8457 30.0612 48.4286 32.1014 49.4855 33.6648C50.5295 35.211 52.2171 36.5081 54.3702 36.5081C56.5232 36.5081 58.2109 35.2153 59.2548 33.6648C60.3117 32.1014 60.8946 30.0612 60.8946 27.918C60.8946 25.7747 60.3117 23.7346 59.2548 22.1712C58.2109 20.6249 56.5232 19.3278 54.3702 19.3278Z"
                                    fill="#E5E5E5" />
                            </svg>
                        </span>
                    @else
                        <span class="business-details-gallery-placeholder" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="61" height="48"
                                viewBox="0 0 61 48" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M19.5733 0C17.4202 0 15.7326 1.29282 14.6886 2.84334C13.6317 4.40674 13.0488 6.4469 13.0488 8.59015C13.0488 10.7334 13.6317 12.7735 14.6886 14.337C15.7326 15.8832 17.4202 17.1803 19.5733 17.1803C21.7263 17.1803 23.414 15.8875 24.4579 14.337C25.5148 12.7735 26.0977 10.7334 26.0977 8.59015C26.0977 6.4469 25.5148 4.40674 24.4579 2.84334C23.414 1.29711 21.7263 0 19.5733 0ZM41.3213 0C39.1683 0 37.4806 1.29282 36.4367 2.84334C35.3798 4.40674 34.7969 6.4469 34.7969 8.59015C34.7969 10.7334 35.3798 12.7735 36.4367 14.337C37.4806 15.8832 39.1683 17.1803 41.3213 17.1803C43.4744 17.1803 45.162 15.8875 46.2059 14.337C47.2629 12.7735 47.8457 10.7334 47.8457 8.59015C47.8457 6.4469 47.2629 4.40674 46.2059 2.84334C45.162 1.29711 43.4744 0 41.3213 0ZM6.52442 19.3278C4.37136 19.3278 2.68371 20.6206 1.6398 22.1712C0.582848 23.7346 0 25.7747 0 27.918C0 30.0612 0.582848 32.1014 1.6398 33.6648C2.68371 35.211 4.37136 36.5081 6.52442 36.5081C8.67748 36.5081 10.3651 35.2153 11.409 33.6648C12.466 32.1014 13.0488 30.0612 13.0488 27.918C13.0488 25.7747 12.466 23.7346 11.409 22.1712C10.3651 20.6249 8.67748 19.3278 6.52442 19.3278ZM30.4473 19.3278C25.2278 19.3278 21.361 22.0939 18.8904 25.5256C16.4502 28.9058 15.2236 33.1021 15.2236 36.5081C15.2236 40.4768 17.6377 43.2385 20.5998 44.8921C23.514 46.5242 27.1677 47.2458 30.4473 47.2458C33.7269 47.2458 37.3806 46.5285 40.2948 44.8921C43.2526 43.2342 45.6709 40.4768 45.6709 36.5081C45.6709 33.1021 44.4443 28.9058 42.0042 25.5256C39.538 22.0896 35.6712 19.3278 30.4473 19.3278ZM54.3702 19.3278C52.2171 19.3278 50.5295 20.6206 49.4855 22.1712C48.4286 23.7346 47.8457 25.7747 47.8457 27.918C47.8457 30.0612 48.4286 32.1014 49.4855 33.6648C50.5295 35.211 52.2171 36.5081 54.3702 36.5081C56.5232 36.5081 58.2109 35.2153 59.2548 33.6648C60.3117 32.1014 60.8946 30.0612 60.8946 27.918C60.8946 25.7747 60.3117 23.7346 59.2548 22.1712C58.2109 20.6249 56.5232 19.3278 54.3702 19.3278Z"
                                    fill="#E5E5E5" />
                            </svg>
                        </span>
                    @endif
                </div>
            @empty
                @foreach ([] as $i)
                    <div class="business-details-gallery__item">
                        <span class="business-details-gallery-placeholder" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="61" height="48"
                                viewBox="0 0 61 48" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M19.5733 0C17.4202 0 15.7326 1.29282 14.6886 2.84334C13.6317 4.40674 13.0488 6.4469 13.0488 8.59015C13.0488 10.7334 13.6317 12.7735 14.6886 14.337C15.7326 15.8832 17.4202 17.1803 19.5733 17.1803C21.7263 17.1803 23.414 15.8875 24.4579 14.337C25.5148 12.7735 26.0977 10.7334 26.0977 8.59015C26.0977 6.4469 25.5148 4.40674 24.4579 2.84334C23.414 1.29711 21.7263 0 19.5733 0ZM41.3213 0C39.1683 0 37.4806 1.29282 36.4367 2.84334C35.3798 4.40674 34.7969 6.4469 34.7969 8.59015C34.7969 10.7334 35.3798 12.7735 36.4367 14.337C37.4806 15.8832 39.1683 17.1803 41.3213 17.1803C43.4744 17.1803 45.162 15.8875 46.2059 14.337C47.2629 12.7735 47.8457 10.7334 47.8457 8.59015C47.8457 6.4469 47.2629 4.40674 46.2059 2.84334C45.162 1.29711 43.4744 0 41.3213 0ZM6.52442 19.3278C4.37136 19.3278 2.68371 20.6206 1.6398 22.1712C0.582848 23.7346 0 25.7747 0 27.918C0 30.0612 0.582848 32.1014 1.6398 33.6648C2.68371 35.211 4.37136 36.5081 6.52442 36.5081C8.67748 36.5081 10.3651 35.2153 11.409 33.6648C12.466 32.1014 13.0488 30.0612 13.0488 27.918C13.0488 25.7747 12.466 23.7346 11.409 22.1712C10.3651 20.6249 8.67748 19.3278 6.52442 19.3278ZM30.4473 19.3278C25.2278 19.3278 21.361 22.0939 18.8904 25.5256C16.4502 28.9058 15.2236 33.1021 15.2236 36.5081C15.2236 40.4768 17.6377 43.2385 20.5998 44.8921C23.514 46.5242 27.1677 47.2458 30.4473 47.2458C33.7269 47.2458 37.3806 46.5285 40.2948 44.8921C43.2526 43.2342 45.6709 40.4768 45.6709 36.5081C45.6709 33.1021 44.4443 28.9058 42.0042 25.5256C39.538 22.0896 35.6712 19.3278 30.4473 19.3278ZM54.3702 19.3278C52.2171 19.3278 50.5295 20.6206 49.4855 22.1712C48.4286 23.7346 47.8457 25.7747 47.8457 27.918C47.8457 30.0612 48.4286 32.1014 49.4855 33.6648C50.5295 35.211 52.2171 36.5081 54.3702 36.5081C56.5232 36.5081 58.2109 35.2153 59.2548 33.6648C60.3117 32.1014 60.8946 30.0612 60.8946 27.918C60.8946 25.7747 60.3117 23.7346 59.2548 22.1712C58.2109 20.6249 56.5232 19.3278 54.3702 19.3278Z"
                                    fill="#E5E5E5" />
                            </svg>
                        </span>
                    </div>
                @endforeach
            @endforelse
        </div>
    </section>

    <section class="business-details-block">
        <x-dashboard.settings.business-details.section-title title="Payout Details" section="payout"
            :is-editing="$editingSection === 'payout'" save-action="savePayoutDetails" />
        <div @class([
            'business-details-card',
            'business-details-grid',
            'business-details-grid--four',
            'business-details-card--editing' => $editingSection === 'payout',
        ]) :class="{ 'business-details-card--editing': editingSection === 'payout' }">
            <div class="business-details-toggle-panel" x-cloak x-show="editingSection === 'payout'">
                <div class="business-details-edit-grid business-details-edit-grid--four">
                    <label class="business-details-input-field">
                        <span>Account Holder Name</span>
                        <input type="text" wire:model.defer="accountHolderName">
                    </label>
                    <label class="business-details-input-field">
                        <span>Account Number</span>
                        <input type="text" wire:model.defer="accountNumber">
                    </label>
                    <label class="business-details-input-field">
                        <span>Sort Code</span>
                        <input type="text" wire:model.defer="sortCode">
                    </label>
                    <label class="business-details-input-field">
                        <span>IBAN</span>
                        <input type="text" wire:model.defer="iban">
                    </label>
                </div>
            </div>
            <div class="business-details-toggle-panel" x-show="editingSection !== 'payout'">
                <div class="business-details-edit-grid business-details-edit-grid--four">
                    <x-dashboard.settings.business-details.field label="Account Holder Name" :value="$payoutDetails['account_holder_name'] ?? null"
                        placeholder="Not provided" />
                    <x-dashboard.settings.business-details.field label="Account Number" :value="$payoutDetails['account_number'] ?? null"
                        placeholder="Not provided" />
                    <x-dashboard.settings.business-details.field label="Sort Code" :value="$payoutDetails['sort_code'] ?? null"
                        placeholder="Not provided" />
                    <x-dashboard.settings.business-details.field label="IBAN" :value="$payoutDetails['iban'] ?? null"
                        placeholder="Not provided" />
                </div>
            </div>
        </div>
    </section>

    <section class="business-details-block">
        <x-dashboard.settings.business-details.section-title title="Business ID" :tone="$businessIdHasIssue ? 'warning' : 'success'"
            section="business-id" :is-editing="$editingSection === 'business-id'" save-action="saveBusinessIdDetails" />
        <div @class([
            'business-details-card',
            'business-details-files',
            'business-details-card--editing' => $editingSection === 'business-id',
            'business-details-card--warning' => $businessIdHasIssue,
        ])
            :class="{ 'business-details-card--editing': editingSection === 'business-id' }">
            <span class="business-details-files__label">Business Owner ID</span>
            <div class="business-details-toggle-panel" x-cloak x-show="editingSection === 'business-id'">
                <label class="business-details-input-field business-details-input-field--full">
                    <span>Business Owner ID Paths</span>
                    <textarea rows="4" wire:model.defer="businessIdPathsText" placeholder="One file path per line"></textarea>
                </label>
            </div>
            <div class="business-details-toggle-panel" x-show="editingSection !== 'business-id'">
                <div class="business-details-edit-stack">
                    @forelse ($businessIdFiles as $file)
                        <x-dashboard.settings.business-details.file-card :file="$file" status="Verified"
                            :tone="$businessIdHasIssue ? 'warning' : 'success'" />
                    @empty
                        <x-dashboard.settings.business-details.empty-file text="No business ID uploaded yet." />
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="business-details-block">
        <x-dashboard.settings.business-details.section-title title="Insurance Details" :tone="$insuranceHasIssue ? 'warning' : 'success'"
            section="insurance" :is-editing="$editingSection === 'insurance'" save-action="saveInsuranceDetails" />
        <div @class([
            'business-details-card',
            'business-details-files',
            'business-details-card--editing' => $editingSection === 'insurance',
            'business-details-card--warning' => $insuranceHasIssue,
        ])
            :class="{ 'business-details-card--editing': editingSection === 'insurance' }">
            <span class="business-details-files__label">Insurance Certificate <span>(Optional)</span></span>
            <div class="business-details-toggle-panel" x-cloak x-show="editingSection === 'insurance'">
                <div class="business-details-edit-stack">
                    <label class="business-details-input-field business-details-input-field--full">
                        <span>Insurance Certificate Paths</span>
                        <textarea rows="4" wire:model.defer="insurancePathsText" placeholder="One file path per line"></textarea>
                    </label>
                    <label class="business-details-input-field">
                        <span>Expiry Date</span>
                        <input type="date" wire:model.defer="insuranceExpiryDate">
                    </label>
                </div>
            </div>
            <div class="business-details-toggle-panel" x-show="editingSection !== 'insurance'">
                <div class="business-details-edit-stack">
                    @forelse ($insuranceFiles as $file)
                        <x-dashboard.settings.business-details.file-card :file="$file" status="Uploaded"
                            :tone="$insuranceHasIssue ? 'warning' : 'success'" />
                    @empty
                        <x-dashboard.settings.business-details.empty-file
                            text="No insurance certificate uploaded yet." />
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <style>
        .business-details-settings {
            width: 100%;
            padding-top: 1.5rem;
            color: #3B3731;
            font-family: Lato, sans-serif;
        }

        .business-details-heading {
            display: flex;
            justify-content: flex-start;
            border-bottom: 1px solid #D8D4CF;
            padding-bottom: 1.15rem;
            margin-bottom: 1.5rem;
        }

        .business-details-heading h2 {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 28px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-heading p {
            margin: 0.25rem 0 0;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: 20px;
        }

        .business-details-alert {
            display: flex;
            align-items: center;
            gap: 1rem;
            max-width: 50rem;
            margin: 0 auto 2.2rem;
            padding: 0.8rem 1rem;
            border: 1px solid #CBDCE8;
            border-radius: 10px;
            background: rgba(203, 220, 232, 0.20);
            color: #8BAFC8;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            line-height: normal;
        }

        .business-details-alert-enter,
        .business-details-alert-leave {
            overflow: hidden;
            transition: opacity 0.22s ease, transform 0.22s ease, max-height 0.22s ease, margin 0.22s ease,
                padding 0.22s ease;
        }

        .business-details-alert-enter-start,
        .business-details-alert-leave-end {
            opacity: 0;
            max-height: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            transform: translateY(-8px);
        }

        .business-details-alert-enter-end,
        .business-details-alert-leave-start {
            opacity: 1;
            max-height: 6rem;
            transform: translateY(0);
        }

        .business-details-alert strong {
            display: block;
            font-weight: 600;
        }

        .business-details-alert span {
            display: block;
            font-weight: 400;
        }

        .business-details-alert__icon {
            width: 24px;
            height: 24px;
            transform: rotate(45deg);
            aspect-ratio: 1/1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 2px;
            background: #B4CCDD;
            flex: 0 0 auto;
            padding: 4px;
        }

        .business-details-alert__icon svg {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-45deg);
        }

        .business-details-alert__close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .business-details-block {
            margin-bottom: 2.1rem;
        }

        .business-details-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin-bottom: 1.5rem;

        }

        .business-details-section-title h3 {
            padding-bottom: 2rem;
            border-bottom: 1px solid #D4D4D4;
            width: 85%;
            margin: 0;
            min-width: 10rem;
            color: #3B3731;
            font-size: 16px;
            font-weight: 700;
        }

        .business-details-title-actions {
            position: relative;
            width: 143px;
            height: 48px;
            flex: 0 0 143px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .business-details-title-actions .business-details-edit,
        .business-details-title-actions .business-details-save {
            position: absolute;
            inset: 0;
        }

        .business-details-edit {
            width: 143px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            background: transparent;
            border: 1px solid #D8D4CF;
            border-radius: 100px;
            border: 1px solid #E2E2E2;
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-decoration: none;
            cursor: pointer;
        }

        .business-details-edit:disabled {
            cursor: wait;
            opacity: 0.75;
        }

        .business-details-save {
            width: 143px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            border: 0;
            border-radius: 100px;
            background: #C9DDA0;
            color: #FFFFFF;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
            transition: background-color 0.18s ease;
        }

        .business-details-save:disabled {
            cursor: wait;
            opacity: 0.75;
        }

        .business-details-btn-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.45);
            border-top-color: #FFFFFF;
            border-radius: 999px;
            display: inline-block;
            flex: 0 0 auto;
            animation: business-details-spin 0.75s linear infinite;
        }

        .business-details-btn-spinner--dark {
            border-color: rgba(59, 55, 49, 0.22);
            border-top-color: #3B3731;
        }

        @keyframes business-details-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .business-details-input-field {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            width: 100%;
        }

        .business-details-input-field--avatar {
            margin-top: 1rem;
        }

        .business-details-input-field--full {
            flex: 1 0 100%;
        }

        .business-details-input-field span {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-input-field input,
        .business-details-input-field textarea {
            width: 100%;
            border: 1px solid #E4E1DD;
            border-radius: 8px;
            background: #FFFFFF;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
            padding: 0.8rem 0.9rem;
            outline: none;
            box-sizing: border-box;
            transition: border-color 0.18s ease, box-shadow 0.18s ease;
        }

        .business-details-input-field textarea {
            min-height: 6rem;
            resize: vertical;
        }

        .business-details-input-field input:focus,
        .business-details-input-field textarea:focus {
            border-color: #C9DDA0;
        }


        .business-details-card {
            border-radius: 8px;
            background: #FAFAFA;
            padding: 1.2rem;
        }

        .business-details-card--editing {
            border-radius: 10px;
            border: 1px solid #E2E2E2;
            background: rgba(255, 255, 255, 0.20);
        }

        .business-details-card--warning {
            border: 1px solid #FFCA7D;
        }

        .business-details-grid {
            display: grid;
            gap: 1rem;
        }

        .business-details-grid--three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .business-details-grid--four {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .business-details-toggle-panel {
            width: 100%;
        }

        .business-details-grid>.business-details-toggle-panel {
            grid-column: 1 / -1;
        }

        .business-details-files>.business-details-toggle-panel {
            flex: 0 0 100%;
        }

        .business-details-edit-grid {
            display: grid;
            gap: 1rem;
        }

        .business-details-edit-grid--three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .business-details-edit-grid--four {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .business-details-edit-stack {
            display: grid;
            gap: 2.5rem;
        }

        .business-details-files .business-details-edit-stack {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .business-details-action-enter,
        .business-details-action-leave {
            transition: opacity 0.22s ease-in-out;
            will-change: opacity;
        }

        .business-details-action-enter-start,
        .business-details-action-leave-end {
            opacity: 0;
        }

        .business-details-action-enter-end,
        .business-details-action-leave-start {
            opacity: 1;
        }

        .business-details-label {
            display: block;
            margin-bottom: 0.8rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-card--editing .business-details-label {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-value {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .business-details-profile {
            display: grid;
            grid-template-columns: 16rem 1fr;
            gap: 8rem;
        }

        .business-details-avatar {
            width: 300px;
            height: 300px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 5px solid #FFC97A;
            border-radius: 999px;
            margin-top: 0.75rem;
            margin-left: 2.5rem;
            padding: 13px;
            background: #FFFFFF;
            box-sizing: border-box;
        }

        .business-details-avatar img {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 999px;
            object-fit: cover;
        }

        .business-details-avatar-upload {
            width: 300px;
            height: 300px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            border: 1px solid #E2E2E2;
            border-radius: 999px;
            margin-top: 2.5rem;
            margin-left: 2.5rem;
            background: #FFFFFF;
            box-sizing: border-box;
        }

        .business-details-avatar-upload__icon {
            position: relative;
            width: 88px;
            height: 88px;
            border-radius: 999px;
        }

        .business-details-avatar-upload__icon svg,
        .business-details-avatar-upload__icon img {
            width: 100%;
            height: 100%;
            display: block;
            border-radius: 999px;
        }

        .business-details-avatar-upload__icon img {
            object-fit: cover;
        }

        .business-details-avatar-upload__plus {
            position: absolute;
            top: 1px;
            right: -2px;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: #9D9B98;
            color: #FFFFFF;
            font-family: Lato;
            font-size: 24px;
            font-weight: 700;
            line-height: 1;
        }

        .business-details-avatar-upload__copy {
            display: grid;
            gap: 0.15rem;
            text-align: center;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }


        .business-details-avatar-upload__button {
            min-width: 178px;
            height: 52px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.65rem;
            border: 0;
            border-radius: 999px;
            background: #FFC97A;
            color: #FFFFFF;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
        }

        .business-details-avatar-upload__input {
            display: none;
        }

        .business-details-avatar-upload__button-progress {
            width: 92px;
            display: inline-grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 0.45rem;
            font-size: 13px;
            line-height: 1;
        }

        .business-details-avatar-upload__progress-bar {
            width: 100%;
            height: 6px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.45);
        }

        .business-details-avatar-upload__progress-bar span {
            height: 100%;
            display: block;
            border-radius: inherit;
            background: #FFFFFF;
        }

        .business-details-avatar-upload__error {
            max-width: 210px;
            color: #B42318;
            text-align: center;
            font-family: Lato;
            font-size: 12px;
            font-style: normal;
            font-weight: 600;
            line-height: 1.3;
        }

        .business-details-gallery__item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .business-details-profile__copy {
            display: grid;
            gap: 2.5rem;
            align-content: start;
        }

        .business-details-gallery {
            display: flex;
            flex-wrap: wrap;
            gap: 2.5rem;
        }
        
        .business-details-gallery__input {
            display: none;
        }

        .business-details-gallery__slot {
            position: relative;
            width: 170px;
            height: 170px;
            animation: business-details-gallery-tile-enter 0.28s ease-out both;
            transition: opacity 0.22s ease, transform 0.22s ease;
            will-change: opacity, transform;
        }

        .business-details-gallery__slot--removing {
            pointer-events: none;
        }

        .business-details-gallery__slot--removing .business-details-gallery__item::after {
            content: "";
            position: absolute;
            inset: 0;
            z-index: 3;
            background: rgba(59, 55, 49, 0.24);
            backdrop-filter: blur(3px);
            animation: business-details-gallery-remove-overlay 0.18s ease-out both;
        }

        .business-details-gallery__slot--removing .business-details-gallery__item img {
            filter: blur(1px);
        }

        @keyframes business-details-gallery-remove-overlay {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes business-details-gallery-tile-enter {
            from {
                opacity: 0;
                transform: translateY(8px) scale(0.97);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .business-details-gallery__item {
            position: relative;
            width: 170px;
            height: 170px;
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            overflow: hidden;
            background: #FFFFFF;
            box-sizing: border-box;
        }

        .business-details-gallery:not(.business-details-gallery--editable) .business-details-gallery__item {
            background: #FBFBFB;
        }

        .business-details-gallery__upload-tile {
            padding: 0;
            color: inherit;
            cursor: pointer;
            transition: border-color 0.18s ease, box-shadow 0.18s ease, transform 0.18s ease;
        }

        .business-details-gallery__upload-tile:hover {
            border-color: #FFC97A;
            box-shadow: 0 10px 24px rgba(59, 55, 49, 0.08);
            transform: translateY(-2px);
        }

        .business-details-gallery__upload-tile:focus-visible {
            outline: 3px solid rgba(201, 221, 160, 0.5);
            outline-offset: 3px;
        }

        .business-details-gallery-remove {
            position: absolute;
            top: -10px;
            right: -10px;
            z-index: 3;
            width: 28px;
            height: 28px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 999px;
            background: #9D9B98;
            color: #FFFFFF;
            cursor: pointer;
            transition: background-color 0.18s ease, transform 0.18s ease;
        }

        .business-details-gallery-remove:hover {
            background: #9D9B98;
            transform: scale(1.05);
        }

        .business-details-gallery-remove:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .business-details-gallery-remove-spinner {
            position: absolute;
            top: 50%;
            left: 50%;
            z-index: 4;
            width: 34px;
            height: 34px;
            border: 3px solid rgba(255, 255, 255, 0.65);
            border-top-color: #FFC97A;
            border-radius: 999px;
            box-shadow: 0 8px 20px rgba(59, 55, 49, 0.18);
            transform: translate(-50%, -50%);
            animation: business-details-gallery-spinner 0.75s linear infinite;
        }

        @keyframes business-details-gallery-spinner {
            to {
                transform: translate(-50%, -50%) rotate(360deg);
            }
        }

        .business-details-gallery__current,
        .business-details-gallery__preview {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .business-details-gallery-add {
            position: relative;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .business-details-gallery-add[hidden] {
            display: none;
        }

        .business-details-gallery-paw {
            width: 61px;
            height: 48px;
            display: block;
            flex: 0 0 auto;
        }

        .business-details-gallery-space-placeholder {
            width: 100%;
            height: 100%;
            display: block;
            flex: 0 0 auto;
        }

        .business-details-gallery-add__plus {
            position: absolute;
            left: 50%;
            bottom: 26px;
            width: 20px;
            height: 20px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            color: #FFFFFF;
            font-family: Lato;
            font-size: 18px;
            font-weight: 700;
            line-height: 1;
            transform: translateX(-50%);
        }

        .business-details-gallery:not(.business-details-gallery--editable) .business-details-gallery-add__plus {
            display: none;
        }

        .business-details-gallery-add__plus svg {
            position: absolute;
            inset: 0;
            width: 20px;
            height: 20px;
            display: block;
        }

        .business-details-gallery-add__plus::after {
            content: "+";
            position: relative;
            z-index: 1;
            margin-top: -1px;
        }

        .business-details-gallery-progress {
            position: absolute;
            top: 50%;
            left: 50%;
            width: 64px;
            height: 64px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            background: conic-gradient(#FFC97A calc(var(--progress, 0) * 1%), rgba(255, 255, 255, 0.72) 0);
            box-shadow: 0 8px 24px rgba(59, 55, 49, 0.18);
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 700;
            transform: translate(-50%, -50%);
            z-index: 2;
        }

        .business-details-gallery-progress::before {
            content: "";
            position: absolute;
            inset: 6px;
            border-radius: inherit;
            background: rgba(255, 255, 255, 0.92);
        }

        .business-details-gallery-progress span {
            position: relative;
            z-index: 1;
        }

        .business-details-gallery-placeholder {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .business-details-gallery-placeholder[hidden] {
            display: none;
        }

        .business-details-paw {
            color: transparent;
            width: 42px;
            height: 36px;
            position: relative;
        }

        .business-details-paw::before {
            content: "";
            position: absolute;
            inset: 12px 9px 0;
            border-radius: 50% 50% 45% 45%;
            background: #DFDFDF;
        }

        .business-details-paw::after {
            content: "";
            position: absolute;
            width: 9px;
            height: 9px;
            left: 4px;
            top: 8px;
            border-radius: 999px;
            background: #DFDFDF;
            box-shadow: 10px -5px 0 #DFDFDF, 22px -5px 0 #DFDFDF, 32px 8px 0 #DFDFDF;
        }

        .business-details-files {
            display: flex;
            flex-wrap: wrap;
            gap: 2rem;
        }

        .business-details-files__label {
            flex: 0 0 100%;
            margin-bottom: -1.8rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-files__label span {
            font-weight: 600;
        }

        .business-details-file {
            min-width: 13rem;
        }

        .business-details-file__top {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            margin-bottom: 0.7rem;
        }

        .business-details-file__icon,
        .business-details-file__download {
            color: #B8B4AE;
            flex: 0 0 auto;
        }

        .business-details-file__name {
            display: block;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            word-break: break-word;
        }

        .business-details-file__meta,
        .business-details-file__status {
            display: block;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .business-details-file__download {
            margin-left: auto;
        }

        .business-details-file__status-group {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .business-details-file__status-group svg {
            width: 19px;
            height: 19px;
            min-width: 19px;
            display: block;
            flex: 0 0 19px;
            margin-top: 0.05rem;
            overflow: visible;
        }

        .business-details-empty {
            margin: 0;
            min-height: 5rem;
            flex: 1 0 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            text-align: center;
        }

        @media (max-width: 991px) {

            .business-details-grid--three,
            .business-details-grid--four,
            .business-details-profile,
            .business-details-gallery {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 640px) {

            .business-details-grid--three,
            .business-details-grid--four,
            .business-details-profile,
            .business-details-gallery {
                grid-template-columns: 1fr;
            }

            .business-details-avatar {
                width: 12rem;
                height: 12rem;
                padding: 10px;
            }
        }
    </style>
</div>
