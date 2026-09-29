<?php

use App\Models\GroomerSpacerProfile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Crypt;
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
    public $businessIdUpload = [];
    public string $insurancePathsText = '';
    public $insuranceUpload = [];
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

        $profileImage = $this->fileCard($businessBasics['profile_photo_path'] ?? null, [], true);
        $gallery = $this->fileCards($businessBasics['gallery_paths'] ?? [], PHP_INT_MAX, [], true);
        $businessIdFiles = $this->fileCards($businessDetails['business_owner_id_images'] ?? ($profile?->id_document_paths ?? []), 4, [
            'names' => $this->arrayValue($businessDetails['business_owner_id_file_names'] ?? []),
        ]);
        $insuranceFiles = $this->fileCards($insuranceDetails['insurance_certificate_paths'] ?? [], 4, [
            'expires_at' => $this->firstString($insuranceDetails, ['expires_at', 'expiry_date', 'expiration_date', 'insurance_expiry_date', 'insurance_certificate_expiry_date', 'insurance_certificate_expires_at']),
            'names' => $this->arrayValue($insuranceDetails['insurance_certificate_file_names'] ?? []),
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

    public function editAll(): void
    {
        $this->hydrateEditableFields();
        $this->editingSection = 'all';
    }

    public function saveAllDetails(): void
    {
        $profile = $this->profile();

        if (!$profile) {
            return;
        }

        $validated = $this->validate([
            'fullName' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'businessPhone' => ['nullable', 'string', 'max:50'],
            'businessName' => ['nullable', 'string', 'max:255'],
            'businessRegistrationNumber' => ['nullable', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
            'profilePhotoPath' => ['nullable', 'string', 'max:2048'],
            'profilePhotoUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:51200'],
            'galleryUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp,avif', 'max:2048'],
        ]);

        $businessDetails = $this->arrayValue($profile->business_details);
        $businessBasics = $this->arrayValue($profile->business_basics);

        $businessDetails['business_phone'] = $validated['businessPhone'];
        $businessDetails['business_name'] = $validated['businessName'];
        $businessDetails['business_registration_number'] = $validated['businessRegistrationNumber'];
        $businessBasics['display_name'] = $validated['businessName'];
        $businessBasics['tagline'] = $validated['tagline'];
        $businessBasics['bio'] = $validated['bio'];
        $businessBasics['profile_photo_path'] = $this->profilePhotoUpload ? $this->profilePhotoUpload->store($this->profileImageUploadDirectory($profile), 'public') : $validated['profilePhotoPath'];

        $profile->update([
            'full_name' => $validated['fullName'],
            'email' => $validated['email'],
            'business_details' => $businessDetails,
            'business_basics' => $businessBasics,
        ]);

        $this->profilePhotoUpload = null;
        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    public function cancelEdit(): void
    {
        $this->resetValidation();
        $this->profilePhotoUpload = null;
        $this->galleryUpload = null;
        $this->galleryReplaceIndex = null;
        $this->businessIdUpload = [];
        $this->insuranceUpload = [];
        $this->editingSection = null;
        $this->hydrateEditableFields();
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
            'profilePhotoUpload' => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif,webp', 'max:51200'],
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
        $this->editingSection = $this->editingSection === 'all' ? 'all' : 'gallery';
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
        $this->editingSection = $this->editingSection === 'all' ? 'all' : 'gallery';
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
        $businessDetails['business_owner_id_file_names'] = array_intersect_key($this->arrayValue($businessDetails['business_owner_id_file_names'] ?? []), array_flip($paths));

        $profile->update([
            'business_details' => $businessDetails,
            'id_document_paths' => $paths,
        ]);

        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    public function updatedBusinessIdUpload(): void
    {
        $this->validate([
            'businessIdUpload' => ['nullable', 'array'],
            'businessIdUpload.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:51200'],
        ]);

        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $profile->refresh();
        $businessDetails = $this->arrayValue($profile->business_details);
        $paths = $this->arrayStrings($businessDetails['business_owner_id_images'] ?? ($profile->id_document_paths ?? []));
        $fileNames = $this->arrayValue($businessDetails['business_owner_id_file_names'] ?? []);

        foreach ((array) $this->businessIdUpload as $file) {
            if (!$file) {
                continue;
            }

            $storedPath = $file->store($this->businessIdUploadDirectory($file), 'public');
            $paths[] = $storedPath;
            $fileNames[$storedPath] = $file->getClientOriginalName();
        }

        $paths = array_values(array_unique($paths));
        $businessDetails['business_owner_id_images'] = $paths;
        $businessDetails['business_owner_id_file_names'] = array_intersect_key($fileNames, array_flip($paths));

        $profile->update([
            'business_details' => $businessDetails,
            'id_document_paths' => $paths,
        ]);

        $this->businessIdPathsText = implode("\n", $paths);
        $this->businessIdUpload = [];
        $this->editingSection = 'business-id';
    }

    public function removeBusinessIdFile(int $index): void
    {
        $profile = $this->profile();
        if (!$profile || $index < 0) {
            return;
        }

        $profile->refresh();
        $businessDetails = $this->arrayValue($profile->business_details);
        $paths = $this->arrayStrings($businessDetails['business_owner_id_images'] ?? ($profile->id_document_paths ?? []));

        if (!array_key_exists($index, $paths)) {
            return;
        }

        $removedPath = $paths[$index];
        unset($paths[$index]);
        $paths = array_values($paths);
        $businessDetails['business_owner_id_images'] = $paths;
        $fileNames = $this->arrayValue($businessDetails['business_owner_id_file_names'] ?? []);
        unset($fileNames[$removedPath]);
        $businessDetails['business_owner_id_file_names'] = array_intersect_key($fileNames, array_flip($paths));

        if ($removedPath !== '' && Storage::disk('public')->exists($removedPath)) {
            Storage::disk('public')->delete($removedPath);
        }

        $profile->update([
            'business_details' => $businessDetails,
            'id_document_paths' => $paths,
        ]);

        $this->businessIdPathsText = implode("\n", $paths);
        $this->businessIdUpload = [];
        $this->editingSection = 'business-id';
    }

    private function businessIdUploadDirectory(mixed $file): string
    {
        $mime = (string) ($file?->getMimeType() ?? '');

        return match (true) {
            str_contains($mime, 'pdf') => 'business_owner_id_images/pdfs',
            str_starts_with($mime, 'image/') => 'business_owner_id_images/images',
            default => 'business_owner_id_images/files',
        };
    }

    public function saveInsuranceDetails(): void
    {
        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $validated = $this->validate([
            'insurancePathsText' => ['nullable', 'string', 'max:5000'],
        ]);

        $insuranceDetails = $this->arrayValue($profile->insurance_details);
        $insuranceDetails['insurance_certificate_paths'] = $this->linesToArray($validated['insurancePathsText']);
        $insuranceDetails['insurance_certificate_file_names'] = array_intersect_key($this->arrayValue($insuranceDetails['insurance_certificate_file_names'] ?? []), array_flip($insuranceDetails['insurance_certificate_paths']));
        $insuranceDetails = $this->withInsuranceExpiryFields($insuranceDetails);

        $profile->update(['insurance_details' => $insuranceDetails]);

        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    public function updatedInsuranceUpload(): void
    {
        $this->validate([
            'insuranceUpload' => ['nullable', 'array'],
            'insuranceUpload.*' => ['file', 'mimes:pdf,jpg,jpeg,png', 'max:51200'],
        ]);

        $profile = $this->profile();
        if (!$profile) {
            return;
        }

        $profile->refresh();
        $insuranceDetails = $this->arrayValue($profile->insurance_details);
        $paths = $this->arrayStrings($insuranceDetails['insurance_certificate_paths'] ?? []);
        $fileNames = $this->arrayValue($insuranceDetails['insurance_certificate_file_names'] ?? []);

        foreach ((array) $this->insuranceUpload as $file) {
            if (!$file) {
                continue;
            }

            $storedPath = $file->store($this->insuranceUploadDirectory($file), 'public');
            $paths[] = $storedPath;
            $fileNames[$storedPath] = $file->getClientOriginalName();
        }

        $paths = array_values(array_unique($paths));
        $insuranceDetails['insurance_certificate_paths'] = $paths;
        $insuranceDetails['insurance_certificate_file_names'] = array_intersect_key($fileNames, array_flip($paths));
        $insuranceDetails = $this->withInsuranceExpiryFields($insuranceDetails);

        $profile->update(['insurance_details' => $insuranceDetails]);

        $this->insurancePathsText = implode("\n", $paths);
        $this->insuranceUpload = [];
        $this->editingSection = 'insurance';
    }

    public function removeInsuranceFile(int $index): void
    {
        $profile = $this->profile();
        if (!$profile || $index < 0) {
            return;
        }

        $profile->refresh();
        $insuranceDetails = $this->arrayValue($profile->insurance_details);
        $paths = $this->arrayStrings($insuranceDetails['insurance_certificate_paths'] ?? []);

        if (!array_key_exists($index, $paths)) {
            return;
        }

        $removedPath = $paths[$index];
        unset($paths[$index]);
        $paths = array_values($paths);
        $insuranceDetails['insurance_certificate_paths'] = $paths;
        $fileNames = $this->arrayValue($insuranceDetails['insurance_certificate_file_names'] ?? []);
        unset($fileNames[$removedPath]);
        $insuranceDetails['insurance_certificate_file_names'] = array_intersect_key($fileNames, array_flip($paths));
        $insuranceDetails = $paths === [] ? $this->withoutInsuranceExpiryFields($insuranceDetails) : $this->withInsuranceExpiryFields($insuranceDetails, false);

        if ($removedPath !== '' && Storage::disk('public')->exists($removedPath)) {
            Storage::disk('public')->delete($removedPath);
        }

        $profile->update(['insurance_details' => $insuranceDetails]);

        $this->insurancePathsText = implode("\n", $paths);
        $this->insuranceUpload = [];
        $this->editingSection = 'insurance';
    }

    private function insuranceUploadDirectory(mixed $file): string
    {
        $mime = (string) ($file?->getMimeType() ?? '');

        return match (true) {
            str_contains($mime, 'pdf') => 'insurance_certificates/pdfs',
            str_starts_with($mime, 'image/') => 'insurance_certificates/images',
            default => 'insurance_certificates/files',
        };
    }

    private function withoutInsuranceExpiryFields(array $insuranceDetails): array
    {
        foreach (['expires_at', 'expiry_date', 'expiration_date', 'insurance_expiry_date', 'insurance_certificate_expiry_date', 'insurance_certificate_expires_at'] as $key) {
            unset($insuranceDetails[$key]);
        }

        return $insuranceDetails;
    }

    private function withInsuranceExpiryFields(array $insuranceDetails, bool $resetExpiry = true): array
    {
        $existingExpiry = $this->firstString($insuranceDetails, ['expires_at', 'expiry_date', 'expiration_date', 'insurance_expiry_date', 'insurance_certificate_expiry_date', 'insurance_certificate_expires_at']);
        $expiryDate = $resetExpiry || !$existingExpiry ? now()->addMonthsNoOverflow(3)->toDateString() : $this->dateString($existingExpiry);

        $insuranceDetails = $this->withoutInsuranceExpiryFields($insuranceDetails);
        $insuranceDetails['insurance_certificate_expiry_date'] = $expiryDate;

        return $insuranceDetails;
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

    private function fileCards(mixed $paths, int $limit, array $metadata = [], bool $useBusinessBasicsFileRoute = false): array
    {
        if (is_string($paths) && $paths !== '') {
            $paths = json_decode($paths, true) ?: [$paths];
        }

        if (!is_array($paths)) {
            return [];
        }

        return collect($paths)->filter(fn($path) => is_string($path) && trim($path) !== '')->take($limit)->map(fn($path) => $this->fileCard($path, $metadata, $useBusinessBasicsFileRoute))->values()->all();
    }

    private function fileCard(mixed $path, array $metadata = [], bool $useBusinessBasicsFileRoute = false): ?array
    {
        if (!is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        $displayName = $this->displayFileName($path, $metadata);
        $expiresAt = $this->dateString($metadata['expires_at'] ?? null);
        $expired = $this->isExpired($expiresAt);

        if (filter_var($path, FILTER_VALIDATE_URL)) {
            $urlPath = (string) (parse_url($path, PHP_URL_PATH) ?: '');
            $extension = strtolower(pathinfo($urlPath, PATHINFO_EXTENSION));

            return [
                'path' => $path,
                'name' => $displayName ?: (basename($urlPath) ?: 'profile-picture'),
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

        $normalizedPath = $this->normalizePublicDiskPath($path);
        $exists = $normalizedPath !== '' && Storage::disk('public')->exists($normalizedPath);
        $extension = strtolower(pathinfo(parse_url($path, PHP_URL_PATH) ?: $normalizedPath ?: $path, PATHINFO_EXTENSION));
        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'], true);

        if ($useBusinessBasicsFileRoute && $exists && $isImage) {
            return [
                'path' => $path,
                'name' => $displayName ?: basename($normalizedPath),
                'url' => Storage::url($normalizedPath),
                'fallback_url' => $this->fileUrl($normalizedPath, true),
                'size' => null,
                'uploaded' => date('d M Y', Storage::disk('public')->lastModified($normalizedPath)),
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
                'name' => $displayName ?: basename($publicPath),
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

        return [
            'path' => $path,
            'name' => $displayName ?: basename($normalizedPath ?: $path),
            'url' => $isImage && $useBusinessBasicsFileRoute ? Storage::url($normalizedPath) : ($isImage ? $this->fileUrl($normalizedPath, false) : ($exists ? Storage::url($normalizedPath) : null)),
            'fallback_url' => $isImage && $useBusinessBasicsFileRoute && $normalizedPath !== '' ? $this->fileUrl($normalizedPath, true) : null,
            'size' => $exists ? $this->formatFileSize(Storage::disk('public')->size($normalizedPath)) : null,
            'uploaded' => $exists ? date('d M Y', Storage::disk('public')->lastModified($normalizedPath)) : null,
            'extension' => $extension,
            'is_image' => $isImage,
            'available' => $exists,
            'expires_at' => $expiresAt,
            'expired' => $expired,
        ];
    }

    private function displayFileName(string $path, array $metadata): ?string
    {
        $names = $this->arrayValue($metadata['names'] ?? []);
        $name = $names[$path] ?? ($names[$this->normalizePublicDiskPath($path)] ?? null);

        return is_string($name) && trim($name) !== '' ? trim($name) : null;
    }

    private function fileUrl(string $path, bool $useBusinessBasicsFileRoute): string
    {
        if (!$useBusinessBasicsFileRoute) {
            return Storage::url($path);
        }

        return route('groomer-spacer.business-basics-file', [
            't' => Crypt::encryptString($path),
        ]);
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

        foreach (['storage/app/public/', 'public/', 'storage/'] as $prefix) {
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

<div class="business-details-settings" :class="{ 'business-details-settings--editing': editingSection === 'all' }" x-data="{
    showBusinessDetailsAlert: true,
    editingSection: @js($editingSection),
    isEditing(section) {
        return this.editingSection === section || this.editingSection === 'all';
    },
    editAll() {
        this.editingSection = 'all';
        this.$wire.call('editAll');
    },
    cancelEdit() {
        if (!this.editingSection) {
            return;
        }

        this.editingSection = null;
        this.$wire.call('cancelEdit');
    },
}" x-on:keydown.escape.window="cancelEdit()">
    <div class="business-details-alert" x-show="showBusinessDetailsAlert" x-cloak
        x-transition:enter="business-details-alert-enter" x-transition:enter-start="business-details-alert-enter-start"
        x-transition:enter-end="business-details-alert-enter-end" x-transition:leave="business-details-alert-leave"
        x-transition:leave-start="business-details-alert-leave-start"
        x-transition:leave-end="business-details-alert-leave-end" role="status">
        <span class="business-details-alert__icon" aria-hidden="true">
            <img src="{{ asset('images/business-hub/icon-settings-alert-info.svg') }}" width="18" height="18" alt="">
        </span>
        <div>
            <strong>Keeping your compliance details current keeps your account verified and visible to clients. Review these regularly.</strong>
        </div>
        <button type="button" class="business-details-alert__close" @click="showBusinessDetailsAlert = false"
            aria-label="Dismiss compliance information alert">
            <img src="{{ asset('images/business-hub/icon-settings-alert-close.svg') }}" width="11" height="11" alt=""
                aria-hidden="true">
        </button>
    </div>

    <section class="business-details-block">
        <x-business-hub.settings.business-details.section-title title="Personal details" section="personal"
            :is-editing="$editingSection === 'personal'" save-action="savePersonalDetails" />
        <div @class([
            'business-details-card',
            'business-details-grid',
            'business-details-grid--three',
            'business-details-card--editing' => $editingSection === 'personal',
        ])
            :class="{ 'business-details-card--editing': isEditing('personal') }">
            <div class="business-details-toggle-panel" x-cloak x-show="isEditing('personal')">
                <div class="business-details-edit-grid business-details-edit-grid--three">
                    <label class="business-details-input-field">
                        <span>Full Name <em>(must match ID)</em></span>
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
            <div class="business-details-toggle-panel" x-show="!isEditing('personal')">
                <div class="business-details-edit-grid business-details-edit-grid--three">
                    <x-business-hub.settings.business-details.field label="Full Name (must match ID)"
                        :value="$profile?->full_name" placeholder="Not provided" />
                    <x-business-hub.settings.business-details.field label="Email Address" :value="$profile?->email"
                        placeholder="Not provided" />
                    <x-business-hub.settings.business-details.field label="Phone Number"
                        :value="$businessDetails['business_phone'] ?? null" placeholder="Not provided" />
                </div>
            </div>
        </div>
    </section>

    <section class="business-details-block">
        <x-business-hub.settings.business-details.section-title title="Business details" section="business"
            :is-editing="$editingSection === 'business'" save-action="saveBusinessDetails" />
        <div @class([
            'business-details-card',
            'business-details-profile',
            'business-details-card--editing' => $editingSection === 'business',
        ])
            :class="{ 'business-details-card--editing': isEditing('business') }">
            <div>
                <div class="business-details-avatar" x-show="!isEditing('business')">
                    @if ($profileImage && $profileImage['is_image'])
                        <img src="{{ $profileImage['url'] }}" data-fallback-src="{{ $profileImage['fallback_url'] ?? '' }}"
                            alt="Business profile image"
                            onerror="if (this.dataset.fallbackSrc && this.src !== this.dataset.fallbackSrc) { this.src = this.dataset.fallbackSrc; this.removeAttribute('data-fallback-src'); }">
                    @else
                        <img class="business-details-paw" src="{{ asset('images/business-hub/icon-gallery-paw.svg') }}"
                            width="28" height="22" alt="" aria-hidden="true">
                    @endif
                </div>
                <div class="business-details-avatar-upload" wire:key="business-profile-image-uploader" x-data="{
                    uploading: false,
                    progress: 0,
                    targetProgress: 0,
                    progressFrame: null,
                    previewUrl: @js($profileImage && ($profileImage['is_image'] ?? false) ? $profileImage['fallback_url'] ?? ($profileImage['url'] ?? null) : null),
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
                    previewFile(file) {
                        if (this.previewUrl?.startsWith('blob:')) {
                            URL.revokeObjectURL(this.previewUrl);
                        }
                
                        this.previewUrl = file ? URL.createObjectURL(file) : null;
                    },
                }" x-cloak x-show="isEditing('business')" x-on:livewire-upload-start="startProgress()"
                    x-on:livewire-upload-finish="setProgress(100)" x-on:livewire-upload-error="resetProgress()"
                    x-on:livewire-upload-progress="setProgress($event.detail.progress)">
                    <div class="business-details-avatar-upload__preview" x-cloak x-show="previewUrl || uploading"
                        aria-hidden="true">
                        <img x-cloak x-show="previewUrl" :src="previewUrl" alt="Selected business profile image">
                        <span class="business-details-avatar-upload__button-progress" x-cloak x-show="uploading">
                            <span class="business-details-avatar-upload__progress-bar">
                                <span :style="`width: ${progress}%`"></span>
                            </span>
                            <span x-text="`${Math.round(progress)}%`"></span>
                        </span>
                    </div>
                    <div class="business-details-avatar-upload__empty" x-show="!previewUrl && !uploading">
                        <span class="business-details-avatar-upload__cloud" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="37" height="34" viewBox="0 0 37 34" fill="none">
                                <path
                                    d="M9.00404 24.1362C-2.99132 25.4739 -1.65851 10.7594 9.00404 12.0971C5.00558 -2.61738 27.6635 -2.61738 26.3307 8.08405C39.6588 4.07101 39.6588 25.4739 27.6635 24.1362M24.9979 18.7855L18.3338 13.4348L11.6697 18.7855M18.3338 13.4348V33.5"
                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </span>
                        <div class="business-details-avatar-upload__copy">
                            <span>Drag &amp; Drop to upload</span>
                            <span>
                                <em>or</em>
                                <button type="button" class="business-details-avatar-upload__browse"
                                    @click.stop="$refs.profilePhotoInput.click()">browse files</button>
                            </span>
                        </div>
                    </div>
                    <input x-ref="profilePhotoInput" type="file" class="business-details-avatar-upload__input"
                        wire:model="profilePhotoUpload" accept="image/*"
                        @change="previewFile($event.target.files[0])">
                    <button type="button" class="business-details-avatar-upload__hit"
                        @click="$refs.profilePhotoInput.click()"
                        @dragover.prevent
                        @drop.prevent="
                            const file = $event.dataTransfer.files?.[0];
                            if (!file) return;
                            const transfer = new DataTransfer();
                            transfer.items.add(file);
                            $refs.profilePhotoInput.files = transfer.files;
                            $refs.profilePhotoInput.dispatchEvent(new Event('change', { bubbles: true }));
                        "
                        aria-label="Upload business profile image"></button>
                    @error('profilePhotoUpload')
                        <span class="business-details-avatar-upload__error">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="business-details-profile__copy">
                <div class="business-details-toggle-panel" x-cloak x-show="isEditing('business')">
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
                <div class="business-details-toggle-panel" x-show="!isEditing('business')">
                    <div class="business-details-edit-stack">
                        <x-business-hub.settings.business-details.field label="Business Name"
                            :value="$businessDetails['business_name'] ?? ($businessBasics['display_name'] ?? null)"
                            placeholder="Not provided" />
                        <x-business-hub.settings.business-details.field label="Business Registration Number"
                            :value="$businessDetails['business_registration_number'] ?? null"
                            placeholder="Not provided" />
                        <x-business-hub.settings.business-details.field label="Tagline"
                            :value="$businessBasics['tagline'] ?? null" placeholder="Not provided" />
                        <x-business-hub.settings.business-details.field label="Bio" :value="$businessBasics['bio'] ?? null" placeholder="Not provided" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="business-details-block">
        @php
            $isSpaceGallery = strtolower((string) ($profile?->user_type ?? '')) === 'space';
        @endphp
        <x-business-hub.settings.business-details.section-title title="Photo gallery" section="gallery"
            :is-editing="$editingSection === 'gallery'" save-action="saveGalleryDetails" />
        <div class="business-details-fade-panel" x-cloak x-show="isEditing('gallery')">
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
                        $isAddSlot = $i === $nextAddSlot;
                    @endphp
                    @if ($shouldShowSlot)
                        @if ($isAddSlot)
                            <div class="business-details-gallery__slot"
                                wire:key="business-gallery-edit-slot-{{ $i }}-add"
                                x-data="{
                                    previewUrl: null,
                                    uploading: false,
                                    progress: 0,
                                    targetProgress: 0,
                                    progressFrame: null,
                                    pick() {
                                        this.progress = 0;
                                        this.targetProgress = 0;
                                        $wire.set('galleryReplaceIndex', null, false);
                                        this.$refs.galleryUploadInput.value = null;
                                        this.$refs.galleryUploadInput.click();
                                    },
                                    preview(event) {
                                        if (this.previewUrl) {
                                            URL.revokeObjectURL(this.previewUrl);
                                        }
                                        const file = event.target.files[0] || null;
                                        this.previewUrl = file ? URL.createObjectURL(file) : null;
                                    },
                                    clearPreview() {
                                        if (this.uploading) {
                                            return;
                                        }
                                        if (this.previewUrl) {
                                            URL.revokeObjectURL(this.previewUrl);
                                        }
                                        this.previewUrl = null;
                                        this.$refs.galleryUploadInput.value = null;
                                        $wire.set('galleryUpload', null, false);
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
                                    finishProgress() {
                                        this.cancelProgressFrame();
                                        this.progress = 100;
                                        this.targetProgress = 100;
                                        setTimeout(() => {
                                            this.uploading = false;
                                            this.progress = 0;
                                            this.targetProgress = 0;
                                        }, 80);
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
                                }" x-on:livewire-upload-start="startProgress()"
                                x-on:livewire-upload-progress="setProgress($event.detail.progress)"
                                x-on:livewire-upload-finish="finishProgress()" x-on:livewire-upload-error="reset()">
                                <input x-ref="galleryUploadInput" type="file" class="business-details-gallery__input"
                                    wire:model="galleryUpload" accept="image/*" @change="preview($event)">
                                <button type="button"
                                    class="business-details-gallery__item business-details-gallery__upload-tile"
                                    @click="pick()" aria-label="Upload gallery image">
                                    <template x-if="previewUrl">
                                        <span class="business-details-gallery__preview">
                                            <img :src="previewUrl" alt="Selected gallery image">
                                            <span class="business-details-gallery-progress" x-cloak x-show="uploading"
                                                :style="`--progress: ${progress}`">
                                                <span x-text="`${Math.round(progress)}%`"></span>
                                            </span>
                                        </span>
                                    </template>
                                    <span x-show="!previewUrl" class="business-details-gallery__dropzone" aria-hidden="true">
                                        <img src="{{ asset('images/business-hub/icon-gallery-upload-cloud.svg') }}"
                                            width="37" height="34" alt="" aria-hidden="true">
                                        <span>
                                            Drag &amp; Drop to upload
                                            <small><em>or</em> <u>browse files</u></small>
                                        </span>
                                    </span>
                                </button>
                                <button type="button" class="business-details-gallery-remove" x-cloak x-show="previewUrl"
                                    @click.stop="clearPreview()" :disabled="uploading" wire:loading.attr="disabled"
                                    wire:target="galleryUpload" aria-label="Clear selected gallery image">
                                    <img src="{{ asset('images/business-hub/icon-gallery-remove.svg') }}" width="36"
                                        height="36" alt="" aria-hidden="true">
                                </button>
                            </div>
                        @else
                            <div class="business-details-gallery__slot"
                                wire:key="business-gallery-edit-slot-{{ $i }}-{{ md5((string) ($image['path'] ?? 'slot')) }}"
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
                                        $wire.set('galleryReplaceIndex', {{ $hasExistingPath ? $i : 'null' }}, false);
                                        this.$refs.galleryUploadInput.value = null;
                                        this.$refs.galleryUploadInput.click();
                                    },
                                    preview(event) {
                                        if (this.previewUrl) {
                                            URL.revokeObjectURL(this.previewUrl);
                                        }
                                        const file = event.target.files[0] || null;
                                        this.previewUrl = file ? URL.createObjectURL(file) : null;
                                    },
                                    clearPreview() {
                                        if (this.uploading) {
                                            return;
                                        }
                                        if (this.previewUrl) {
                                            URL.revokeObjectURL(this.previewUrl);
                                        }
                                        this.previewUrl = null;
                                        this.$refs.galleryUploadInput.value = null;
                                        $wire.set('galleryUpload', null, false);
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
                                    finishProgress() {
                                        this.cancelProgressFrame();
                                        this.progress = 100;
                                        this.targetProgress = 100;
                                        setTimeout(() => {
                                            this.uploading = false;
                                            this.progress = 0;
                                            this.targetProgress = 0;
                                        }, 80);
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
                                x-on:livewire-upload-finish="finishProgress()" x-on:livewire-upload-error="reset()"
                                :class="{ 'business-details-gallery__slot--removing': removing }">
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
                                            <img src="{{ $image['url'] }}" data-fallback-src="{{ $image['fallback_url'] ?? '' }}"
                                                decoding="async" alt="Business gallery image {{ $i + 1 }}"
                                                onerror="if (this.dataset.fallbackSrc && this.src !== this.dataset.fallbackSrc) { this.src = this.dataset.fallbackSrc; this.removeAttribute('data-fallback-src'); return; } this.hidden = true; this.nextElementSibling.hidden = false;">
                                            <span class="business-details-gallery-add" hidden aria-hidden="true">
                                                @if ($isSpaceGallery)
                                                    <x-business-hub.settings.business-details.space-gallery-placeholder />
                                                @else
                                                    <img class="business-details-gallery-paw"
                                                        src="{{ asset('images/business-hub/icon-gallery-paw.svg') }}"
                                                        width="40" height="32" alt="" aria-hidden="true">
                                                @endif
                                            </span>
                                        @endif
                                    </span>
                                </button>
                                @if ($hasExistingPath)
                                    <span class="business-details-gallery-remove-spinner" x-cloak x-show="removing"
                                        aria-hidden="true"></span>
                                    <button type="button" class="business-details-gallery-remove" @click.stop="remove({{ $i }})"
                                        :disabled="removing || uploading" aria-label="Remove gallery image {{ $i + 1 }}">
                                        <img src="{{ asset('images/business-hub/icon-gallery-remove.svg') }}" width="36" height="36"
                                            alt="" aria-hidden="true">
                                    </button>
                                    @if (!empty($image['uploaded']))
                                        <span class="business-details-gallery-caption">
                                            Uploaded · {{ $image['uploaded'] }}
                                        </span>
                                    @elseif (!empty($image['path']))
                                        <span class="business-details-gallery-caption">Uploaded</span>
                                    @endif
                                @else
                                    <button type="button" class="business-details-gallery-remove" x-cloak x-show="previewUrl"
                                        @click.stop="clearPreview()" :disabled="uploading" wire:loading.attr="disabled"
                                        wire:target="galleryUpload" aria-label="Clear selected gallery image">
                                        <img src="{{ asset('images/business-hub/icon-gallery-remove.svg') }}" width="36" height="36"
                                            alt="" aria-hidden="true">
                                    </button>
                                @endif
                            </div>
                        @endif
                    @endif
                @endfor
            </div>
            @error('galleryUpload')
                <span class="business-details-avatar-upload__error">{{ $message }}</span>
            @enderror
        </div>
        <div class="business-details-gallery business-details-fade-panel" x-show="!isEditing('gallery')">
            @foreach ($gallery as $i => $image)
                @php
                    $hasUsableImage = $image && ($image['is_image'] ?? false) && ($image['available'] ?? true) && !empty($image['url']);
                @endphp
                <div class="business-details-gallery__item">
                    @if ($hasUsableImage)
                        <img src="{{ $image['url'] }}" data-fallback-src="{{ $image['fallback_url'] ?? '' }}" decoding="async"
                            alt="Business gallery image {{ $i + 1 }}"
                            onerror="if (this.dataset.fallbackSrc && this.src !== this.dataset.fallbackSrc) { this.src = this.dataset.fallbackSrc; this.removeAttribute('data-fallback-src'); return; } this.hidden = true; this.nextElementSibling.hidden = false;">
                        <span class="business-details-gallery-add" hidden aria-hidden="true">
                            @if ($isSpaceGallery)
                                <x-business-hub.settings.business-details.space-gallery-placeholder />
                            @else
                                <img class="business-details-gallery-paw"
                                    src="{{ asset('images/business-hub/icon-gallery-paw.svg') }}" width="40" height="32"
                                    alt="" aria-hidden="true">
                            @endif
                        </span>
                    @else
                        <span class="business-details-gallery-add" aria-hidden="true">
                            @if ($isSpaceGallery)
                                <x-business-hub.settings.business-details.space-gallery-placeholder />
                            @else
                                <img class="business-details-gallery-paw"
                                    src="{{ asset('images/business-hub/icon-gallery-paw.svg') }}" width="40" height="32"
                                    alt="" aria-hidden="true">
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
                        <img src="{{ $image['url'] }}" data-fallback-src="{{ $image['fallback_url'] ?? '' }}" decoding="async"
                            alt="Business gallery image {{ $i + 1 }}"
                            onerror="if (this.dataset.fallbackSrc && this.src !== this.dataset.fallbackSrc) { this.src = this.dataset.fallbackSrc; this.removeAttribute('data-fallback-src'); return; } this.hidden = true; this.nextElementSibling.hidden = false;">
                        <span class="business-details-gallery-placeholder" hidden aria-hidden="true">
                            <img class="business-details-gallery-paw"
                                src="{{ asset('images/business-hub/icon-gallery-paw.svg') }}" width="40" height="32" alt=""
                                aria-hidden="true">
                        </span>
                    @else
                        <span class="business-details-gallery-placeholder" aria-hidden="true">
                            <img class="business-details-gallery-paw"
                                src="{{ asset('images/business-hub/icon-gallery-paw.svg') }}" width="40" height="32" alt=""
                                aria-hidden="true">
                        </span>
                    @endif
                </div>
            @empty
                @for ($i = 0; $i < 4; $i++)
                    <div class="business-details-gallery__item">
                        <span class="business-details-gallery-placeholder" aria-hidden="true">
                            <img class="business-details-gallery-paw"
                                src="{{ asset('images/business-hub/icon-gallery-paw.svg') }}" width="40" height="32" alt=""
                                aria-hidden="true">
                        </span>
                    </div>
                @endfor
            @endforelse
        </div>
    </section>

    <div class="business-details-save-bar" x-cloak x-show="editingSection === 'all'">
        <strong>1 unsaved changes</strong>
        <div class="business-details-save-bar__actions" x-data="{ saving: false }">
            <button type="button" class="business-details-save-bar__cancel" @click="cancelEdit()">Cancel</button>
            <button type="button" class="business-details-save-bar__submit"
                @click="saving = true; Promise.resolve($wire.call('saveAllDetails')).then(() => editingSection = $wire.editingSection).finally(() => saving = false)"
                :disabled="saving">
                <span class="business-details-btn-spinner" x-cloak x-show="saving" aria-hidden="true"></span>
                <span>Save Changes</span>
            </button>
        </div>
    </div>

    <section class="business-details-block" x-cloak x-show="editingSection !== 'all'">
        <x-business-hub.settings.business-details.section-title title="Payout details" section="payout"
            :is-editing="$editingSection === 'payout'" save-action="savePayoutDetails" />
        <div @class([
            'business-details-card',
            'business-details-grid',
            'business-details-grid--four',
            'business-details-card--editing' => $editingSection === 'payout',
        ])
            :class="{ 'business-details-card--editing': editingSection === 'payout' }">
            <div class="business-details-toggle-panel" x-cloak x-show="editingSection === 'payout'">
                <div class="business-details-edit-grid business-details-edit-grid--two">
                    <label class="business-details-input-field">
                        <span>Account Holder Name</span>
                        <input type="text" wire:model.defer="accountHolderName">
                    </label>
                    <label class="business-details-input-field">
                        <span>Sort Code</span>
                        <input type="text" wire:model.defer="sortCode">
                    </label>
                    <label class="business-details-input-field">
                        <span>Account Number</span>
                        <input type="text" wire:model.defer="accountNumber">
                    </label>
                    <label class="business-details-input-field">
                        <span>IBAN</span>
                        <input type="text" wire:model.defer="iban">
                    </label>
                </div>
            </div>
            <div class="business-details-toggle-panel" x-show="editingSection !== 'payout'">
                <div class="business-details-edit-grid business-details-edit-grid--four">
                    <x-business-hub.settings.business-details.field label="Account holder"
                        :value="$payoutDetails['account_holder_name'] ?? null" placeholder="Not provided" />
                    <x-business-hub.settings.business-details.field label="Account number"
                        :value="!empty($payoutDetails['account_number']) ? '•••• ' . substr((string) $payoutDetails['account_number'], -4) : null" placeholder="Not provided" />
                    <x-business-hub.settings.business-details.field label="Sort code"
                        :value="$payoutDetails['sort_code'] ?? null" placeholder="Not provided" />
                    <div class="business-details-field">
                        <span>Status</span>
                        <p class="business-details-payout-status">
                            <img src="{{ asset('images/business-hub/icon-verified-check.svg') }}" width="12" height="12"
                                alt="" aria-hidden="true">
                            Verified
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="business-details-verification" x-cloak x-show="editingSection !== 'all'">
        <div class="business-details-verification__heading">
            <h3>Verification status</h3>
        </div>
        <div class="business-details-verification__summary">
            <div>
                <span>Identity verified</span>
                <p>Provider passed third-party verification at onboarding</p>
            </div>
            <div class="business-details-verification__status">
                <span>Status</span>
                <strong>
                    <img src="{{ asset('images/business-hub/icon-verified-check.svg') }}" width="12" height="12" alt=""
                        aria-hidden="true">
                    Verified
                </strong>
            </div>
        </div>
        <div class="business-details-verification__provider">
            <div class="business-details-verification__provider-intro">
                <strong>Third-party verification system</strong>
                <span>Documents and identity files are held externally.</span>
            </div>
            <div class="business-details-verification__meta">
                <p>
                    <span>Verified</span>
                    {{ $profile?->created_at?->format('d M Y') ?? '12 Jan 2023' }}
                </p>
                <p>
                    <span>Type</span>
                    {{ strtolower((string) ($profile?->user_type ?? 'groomer')) === 'space' ? 'Space Provider' : 'Freelance Groomer' }}
                </p>
                <p>
                    <span>Result</span>
                    <em class="business-details-verification__pass">Pass</em>
                </p>
                <p>
                    <span>Reference</span>
                    VRF-{{ $profile?->created_at?->format('Y') ?? '2023' }}-{{ str_pad((string) ($profile?->id ?? 142), 5, '0', STR_PAD_LEFT) }}
                </p>
            </div>
        </div>
    </section>

    <section class="business-details-block business-details-block--legacy-verification" hidden>
        <x-business-hub.settings.business-details.section-title title="Business ID" :tone="$businessIdHasIssue ? 'warning' : 'success'" section="business-id" :is-editing="$editingSection === 'business-id'"
            save-action="saveBusinessIdDetails" />
        <div @class([
            'business-details-card',
            'business-details-files',
            'business-details-card--editing' => $editingSection === 'business-id',
            'business-details-card--warning' => $businessIdHasIssue,
        ])
            :class="{ 'business-details-card--editing': editingSection === 'business-id' }">
            <span class="business-details-files__label">Business Owner ID</span>
            <div class="business-details-toggle-panel" x-cloak x-show="editingSection === 'business-id'">
                <div class="business-details-upload-layout" x-data="{
                    uploading: false,
                    progress: 0,
                    targetProgress: 0,
                    progressTimer: null,
                    startUpload() {
                        this.clearProgressTimer();
                        this.uploading = true;
                        this.progress = 1;
                        this.targetProgress = 12;
                        this.progressTimer = setInterval(() => {
                            if (!this.uploading) {
                                this.clearProgressTimer();
                                return;
                            }
                
                            const ceiling = Math.max(this.targetProgress, 90);
                            if (this.progress < ceiling) {
                                this.progress += Math.max(1, Math.round((ceiling - this.progress) * 0.08));
                            }
                        }, 120);
                    },
                    setUploadProgress(value) {
                        const nextProgress = Math.min(95, Math.max(this.targetProgress, Number(value || 0)));
                        this.targetProgress = nextProgress;
                        this.progress = Math.max(this.progress, Math.min(nextProgress, this.progress + 8));
                    },
                    finishUpload() {
                        this.clearProgressTimer();
                        this.progress = 100;
                        this.targetProgress = 100;
                        setTimeout(() => {
                            this.uploading = false;
                            this.progress = 0;
                            this.targetProgress = 0;
                        }, 250);
                    },
                    resetUpload() {
                        this.clearProgressTimer();
                        this.uploading = false;
                        this.progress = 0;
                        this.targetProgress = 0;
                    },
                    clearProgressTimer() {
                        if (this.progressTimer) {
                            clearInterval(this.progressTimer);
                            this.progressTimer = null;
                        }
                    },
                }" x-on:livewire-upload-start="startUpload()" x-on:livewire-upload-finish="finishUpload()"
                    x-on:livewire-upload-error="resetUpload()"
                    x-on:livewire-upload-progress="setUploadProgress($event.detail.progress)">
                    <textarea class="business-details-sr-field" rows="4" wire:model.defer="businessIdPathsText"
                        aria-label="Business Owner ID Paths"></textarea>
                    <input x-ref="businessIdInput" type="file" class="business-details-sr-field"
                        wire:model="businessIdUpload" accept=".pdf,image/jpeg,image/png" multiple>
                    <div class="business-details-upload-card" @click="$refs.businessIdInput.click()" @dragover.prevent
                        @drop.prevent="$refs.businessIdInput.files = $event.dataTransfer.files; $refs.businessIdInput.dispatchEvent(new Event('change', { bubbles: true }))">
                        <div class="business-details-upload-tabs">
                            <button type="button" class="business-details-upload-tab"
                                @click.stop="$refs.businessIdInput.click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path
                                        d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                </svg>
                                Attach
                            </button>
                            <button type="button" class="business-details-upload-tab"
                                @click.stop="$refs.businessIdInput.click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                    <circle cx="9" cy="9" r="2" />
                                    <path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21" />
                                </svg>
                                Upload
                            </button>
                        </div>
                        <div class="business-details-upload-dropzone">
                            <p>Choose a file or drag &amp; drop it here.</p>
                            <span>JPEG, PNG, and PDF formats, up to 50 MB.</span>
                            <button type="button" @click.stop="$refs.businessIdInput.click()">
                                <span x-show="!uploading">Browse File</span>
                                <span x-cloak x-show="uploading" x-text="`Uploading ${Math.round(progress)}%`"></span>
                            </button>
                        </div>
                    </div>
                    <div class="business-details-upload-list">
                        @forelse ($businessIdFiles as $index => $file)
                            @php
                                $fileExtension = strtolower((string) ($file['extension'] ?? ''));
                                $isPdfFile = $fileExtension === 'pdf';
                            @endphp
                            <div class="business-details-upload-list__item">
                                <span class="business-details-upload-list__icon" aria-hidden="true">
                                    @if ($isPdfFile)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="27" viewBox="0 0 21 25"
                                            fill="none">
                                            <path
                                                d="M5.04074 24.501H15.9593C17.1635 24.501 18.3185 24.0226 19.1701 23.1711C20.0216 22.3195 20.5 21.1646 20.5 19.9603V12.7859C20.5004 11.5818 20.0226 10.4268 19.1715 9.57499L11.4276 1.82979C11.0059 1.40815 10.5053 1.0737 9.95439 0.845536C9.40346 0.61737 8.81297 0.499957 8.21666 0.5H5.04074C3.83646 0.5 2.6815 0.978398 1.82995 1.82995C0.978398 2.6815 0.5 3.83646 0.5 5.04074V19.9603C0.5 21.1646 0.978398 22.3195 1.82995 23.1711C2.6815 24.0226 3.83646 24.501 5.04074 24.501Z"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M10.0952 0.966797V8.30982C10.0952 8.99798 10.3686 9.65795 10.8552 10.1446C11.3418 10.6312 12.0018 10.9045 12.6899 10.9045H20.0355"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M4.33759 18.3383V17.041M4.33759 17.041V14.4463H5.63494C5.97902 14.4463 6.30901 14.583 6.55231 14.8263C6.79561 15.0696 6.93229 15.3996 6.93229 15.7436C6.93229 16.0877 6.79561 16.4177 6.55231 16.661C6.30901 16.9043 5.97902 17.041 5.63494 17.041H4.33759ZM14.7164 18.3383V16.7167M14.7164 16.7167V14.4463H16.6624M14.7164 16.7167H16.6624M9.527 18.3383V14.4463H10.1757C10.6918 14.4463 11.1868 14.6513 11.5517 15.0163C11.9167 15.3812 12.1217 15.8762 12.1217 16.3923C12.1217 16.9084 11.9167 17.4034 11.5517 17.7684C11.1868 18.1333 10.6918 18.3383 10.1757 18.3383H9.527Z"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="16" rx="2" />
                                            <circle cx="8.5" cy="9.5" r="1.5" />
                                            <path d="M21 16l-5-5L5 20" />
                                        </svg>
                                    @endif
                                </span>
                                <span class="business-details-upload-list__copy">
                                    <span>{{ $file['name'] ?? 'business_id.pdf' }}</span>
                                    <small>{{ $file['size'] ?? 'Size unavailable' }}</small>
                                </span>
                                <button type="button" class="business-details-upload-list__remove"
                                    wire:click="removeBusinessIdFile({{ $index }})" wire:loading.attr="disabled"
                                    wire:target="removeBusinessIdFile({{ $index }})"
                                    aria-label="Remove {{ $file['name'] ?? 'business ID file' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"
                                        fill="none" aria-hidden="true">
                                        <path d="M1.4 1.4L12.6 12.6M12.6 1.4L1.4 12.6" stroke="currentColor"
                                            stroke-width="1.8" stroke-linecap="round" />
                                    </svg>
                                </button>
                                <div class="business-details-upload-list__delete-progress" x-cloak wire:loading.flex
                                    wire:target="removeBusinessIdFile({{ $index }})" aria-hidden="true">
                                    <span></span>
                                </div>
                            </div>
                        @empty
                            <x-business-hub.settings.business-details.empty-file text="No business ID uploaded yet." />
                        @endforelse
                        @error('businessIdUpload.*')
                            <span class="business-details-avatar-upload__error">{{ $message }}</span>
                        @enderror
                        @error('businessIdUpload')
                            <span class="business-details-avatar-upload__error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="business-details-toggle-panel" x-show="editingSection !== 'business-id'">
                <div class="business-details-edit-stack">
                    @forelse ($businessIdFiles as $file)
                        <x-business-hub.settings.business-details.file-card :file="$file" status="Verified"
                            :tone="$businessIdHasIssue ? 'warning' : 'success'" />
                    @empty
                        <x-business-hub.settings.business-details.empty-file text="No business ID uploaded yet." />
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <section class="business-details-block business-details-block--legacy-verification" hidden>
        <x-business-hub.settings.business-details.section-title title="Insurance Details" :tone="$insuranceHasIssue ? 'warning' : 'success'" section="insurance" :is-editing="$editingSection === 'insurance'"
            save-action="saveInsuranceDetails" />
        <div @class([
            'business-details-card',
            'business-details-files',
            'business-details-card--editing' => $editingSection === 'insurance',
            'business-details-card--warning' => $insuranceHasIssue,
        ])
            :class="{ 'business-details-card--editing': editingSection === 'insurance' }">
            @if ($insuranceFiles !== [])
                <span class="business-details-files__label">Insurance Certificate <span>(Optional)</span></span>
            @endif
            <div class="business-details-toggle-panel" x-cloak x-show="editingSection === 'insurance'">
                <div class="business-details-upload-layout" x-data="{
                    uploading: false,
                    progress: 0,
                    targetProgress: 0,
                    progressTimer: null,
                    startUpload() {
                        this.clearProgressTimer();
                        this.uploading = true;
                        this.progress = 1;
                        this.targetProgress = 12;
                        this.progressTimer = setInterval(() => {
                            if (!this.uploading) {
                                this.clearProgressTimer();
                                return;
                            }
                            const ceiling = Math.max(this.targetProgress, 90);
                            if (this.progress < ceiling) {
                                this.progress += Math.max(1, Math.round((ceiling - this.progress) * 0.08));
                            }
                        }, 120);
                    },
                    setUploadProgress(value) {
                        const nextProgress = Math.min(95, Math.max(this.targetProgress, Number(value || 0)));
                        this.targetProgress = nextProgress;
                        this.progress = Math.max(this.progress, Math.min(nextProgress, this.progress + 8));
                    },
                    finishUpload() {
                        this.clearProgressTimer();
                        this.progress = 100;
                        this.targetProgress = 100;
                        setTimeout(() => {
                            this.uploading = false;
                            this.progress = 0;
                            this.targetProgress = 0;
                        }, 250);
                    },
                    resetUpload() {
                        this.clearProgressTimer();
                        this.uploading = false;
                        this.progress = 0;
                        this.targetProgress = 0;
                    },
                    clearProgressTimer() {
                        if (this.progressTimer) {
                            clearInterval(this.progressTimer);
                            this.progressTimer = null;
                        }
                    },
                }" x-on:livewire-upload-start="startUpload()" x-on:livewire-upload-finish="finishUpload()"
                    x-on:livewire-upload-error="resetUpload()"
                    x-on:livewire-upload-progress="setUploadProgress($event.detail.progress)">
                    <textarea class="business-details-sr-field" rows="4" wire:model.defer="insurancePathsText"
                        aria-label="Insurance Certificate Paths"></textarea>
                    <input x-ref="insuranceInput" type="file" class="business-details-sr-field"
                        wire:model="insuranceUpload" accept=".pdf,image/jpeg,image/png" multiple>
                    <div class="business-details-upload-card" @click="$refs.insuranceInput.click()" @dragover.prevent
                        @drop.prevent="$refs.insuranceInput.files = $event.dataTransfer.files; $refs.insuranceInput.dispatchEvent(new Event('change', { bubbles: true }))">
                        <div class="business-details-upload-tabs">
                            <button type="button" class="business-details-upload-tab"
                                @click.stop="$refs.insuranceInput.click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <path
                                        d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48" />
                                </svg>
                                Attach
                            </button>
                            <button type="button" class="business-details-upload-tab"
                                @click.stop="$refs.insuranceInput.click()">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round"
                                    stroke-linejoin="round" aria-hidden="true">
                                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2" />
                                    <circle cx="9" cy="9" r="2" />
                                    <path d="m21 15-3.1-3.1a2 2 0 0 0-2.8 0L6 21" />
                                </svg>
                                Upload
                            </button>
                        </div>
                        <div class="business-details-upload-dropzone">
                            <p>Choose a file or drag &amp; drop it here.</p>
                            <span>JPEG, PNG, and PDF formats, up to 50 MB.</span>
                            <button type="button" @click.stop="$refs.insuranceInput.click()">
                                <span x-show="!uploading">Browse File</span>
                                <span x-cloak x-show="uploading" x-text="`Uploading ${Math.round(progress)}%`"></span>
                            </button>
                        </div>
                    </div>
                    <div class="business-details-upload-list">
                        @forelse ($insuranceFiles as $index => $file)
                            @php
                                $fileExtension = strtolower((string) ($file['extension'] ?? ''));
                                $isPdfFile = $fileExtension === 'pdf';
                            @endphp
                            <div class="business-details-upload-list__item">
                                <span class="business-details-upload-list__icon" aria-hidden="true">
                                    @if ($isPdfFile)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="23" height="27" viewBox="0 0 21 25"
                                            fill="none">
                                            <path
                                                d="M5.04074 24.501H15.9593C17.1635 24.501 18.3185 24.0226 19.1701 23.1711C20.0216 22.3195 20.5 21.1646 20.5 19.9603V12.7859C20.5004 11.5818 20.0226 10.4268 19.1715 9.57499L11.4276 1.82979C11.0059 1.40815 10.5053 1.0737 9.95439 0.845536C9.40346 0.61737 8.81297 0.499957 8.21666 0.5H5.04074C3.83646 0.5 2.6815 0.978398 1.82995 1.82995C0.978398 2.6815 0.5 3.83646 0.5 5.04074V19.9603C0.5 21.1646 0.978398 22.3195 1.82995 23.1711C2.6815 24.0226 3.83646 24.501 5.04074 24.501Z"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M10.0952 0.966797V8.30982C10.0952 8.99798 10.3686 9.65795 10.8552 10.1446C11.3418 10.6312 12.0018 10.9045 12.6899 10.9045H20.0355"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M4.33759 18.3383V17.041M4.33759 17.041V14.4463H5.63494C5.97902 14.4463 6.30901 14.583 6.55231 14.8263C6.79561 15.0696 6.93229 15.3996 6.93229 15.7436C6.93229 16.0877 6.79561 16.4177 6.55231 16.661C6.30901 16.9043 5.97902 17.041 5.63494 17.041H4.33759ZM14.7164 18.3383V16.7167M14.7164 16.7167V14.4463H16.6624M14.7164 16.7167H16.6624M9.527 18.3383V14.4463H10.1757C10.6918 14.4463 11.1868 14.6513 11.5517 15.0163C11.9167 15.3812 12.1217 15.8762 12.1217 16.3923C12.1217 16.9084 11.9167 17.4034 11.5517 17.7684C11.1868 18.1333 10.6918 18.3383 10.1757 18.3383H9.527Z"
                                                stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                            fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                            stroke-linejoin="round">
                                            <rect x="3" y="4" width="18" height="16" rx="2" />
                                            <circle cx="8.5" cy="9.5" r="1.5" />
                                            <path d="M21 16l-5-5L5 20" />
                                        </svg>
                                    @endif
                                </span>
                                <span class="business-details-upload-list__copy">
                                    <span>{{ $file['name'] ?? 'insurance_certificate.pdf' }}</span>
                                    <small>{{ $file['size'] ?? 'Size unavailable' }}</small>
                                </span>
                                <button type="button" class="business-details-upload-list__remove"
                                    wire:click="removeInsuranceFile({{ $index }})" wire:loading.attr="disabled"
                                    wire:target="removeInsuranceFile({{ $index }})"
                                    aria-label="Remove {{ $file['name'] ?? 'insurance certificate' }}">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"
                                        fill="none" aria-hidden="true">
                                        <path d="M1.4 1.4L12.6 12.6M12.6 1.4L1.4 12.6" stroke="currentColor"
                                            stroke-width="1.8" stroke-linecap="round" />
                                    </svg>
                                </button>
                                <div class="business-details-upload-list__delete-progress" x-cloak wire:loading.flex
                                    wire:target="removeInsuranceFile({{ $index }})" aria-hidden="true">
                                    <span></span>
                                </div>
                            </div>
                        @empty
                            <x-business-hub.settings.business-details.empty-file
                                text="No insurance certificate uploaded yet." />
                        @endforelse
                        @error('insuranceUpload.*')
                            <span class="business-details-avatar-upload__error">{{ $message }}</span>
                        @enderror
                        @error('insuranceUpload')
                            <span class="business-details-avatar-upload__error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="business-details-toggle-panel" x-show="editingSection !== 'insurance'">
                <div class="business-details-edit-stack">
                    @forelse ($insuranceFiles as $file)
                        <x-business-hub.settings.business-details.file-card :file="$file" status="Uploaded"
                            :tone="$insuranceHasIssue ? 'warning' : 'success'" />
                    @empty
                        <x-business-hub.settings.business-details.empty-file
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
            font-family: Lato;
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
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 auto;
            padding: 0;
            border-radius: 0;
            background: transparent;
            transform: none;
        }

        .business-details-alert__icon img,
        .business-details-alert__icon svg {
            width: 18px;
            height: 18px;
            display: block;
            transform: none;
        }

        .business-details-alert__close {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 18px;
            height: 18px;
            margin-left: auto;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .business-details-alert__close img,
        .business-details-alert__close svg {
            width: 11px;
            height: 11px;
            display: block;
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

        .business-details-edit-grid--two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
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
            gap: 4rem;
        }

        .business-details-sr-field {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .business-details-upload-layout {
            display: grid;
            grid-template-columns: minmax(280px, 47%) 1fr;
            align-items: end;
            gap: 2.75rem;
            width: 100%;
        }

        .business-details-upload-card {
            overflow: hidden;
            min-height: 184px;
            border: 1px solid #E2E2E2;
            border-radius: 8px;
            background: #FFFFFF;
            cursor: pointer;
        }

        .business-details-upload-tabs {
            height: 46px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #E2E2E2;
            background: #F8F8F8;
        }

        .business-details-upload-tab {
            height: 100%;
            display: inline-flex;
            align-items: center;
            gap: 0.55rem;
            padding: 0 1.1rem;
            border: 0;
            border-right: 1px solid #E2E2E2;
            background: transparent;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        .business-details-upload-tab svg {
            color: #6E6B67;
        }

        .business-details-upload-dropzone {
            min-height: 136px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 1.25rem;
            text-align: center;
        }

        .business-details-upload-dropzone p {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 500;
        }

        .business-details-upload-dropzone span {
            margin-top: 0.1rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
        }

        .business-details-upload-dropzone button {
            min-width: 113px;
            height: 36px;
            margin-top: 1.2rem;
            padding: 0 1.1rem;
            border: 1px solid #E2E2E2;
            border-radius: 999px;
            background: #FFFFFF;
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            box-shadow: 0 3px 8px rgba(59, 55, 49, 0.06);
            cursor: pointer;
        }

        .business-details-upload-list {
            display: grid;
            gap: 1.05rem;
        }

        .business-details-upload-list__item {
            position: relative;
            display: grid;
            grid-template-columns: 28px minmax(0, 1fr) 24px;
            align-items: center;
            gap: 0.9rem;
            padding-bottom: 0.35rem;
        }

        .business-details-upload-list__icon {
            color: #B8B4AE;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .business-details-upload-list__copy {
            min-width: 0;
            display: grid;
            gap: 0.1rem;
        }

        .business-details-upload-list__copy span {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 500;
            line-height: 1.2;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .business-details-upload-list__copy small {
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.2;
        }

        .business-details-upload-list__remove {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            background: transparent;
            color: #3B3731;
            cursor: pointer;
        }

        .business-details-upload-list__remove:disabled {
            cursor: wait;
            opacity: 0.55;
        }

        .business-details-upload-list__delete-progress {
            position: absolute;
            left: 38px;
            right: 34px;
            bottom: 0;
            height: 3px;
            overflow: hidden;
            border-radius: 999px;
            background: #ECECEC;
        }

        .business-details-upload-list__delete-progress span {
            width: 42%;
            height: 100%;
            display: block;
            border-radius: inherit;
            background: #C9DDA0;
            animation: business-details-delete-progress 0.8s ease-in-out infinite;
        }

        @keyframes business-details-delete-progress {
            0% {
                transform: translateX(-110%);
            }

            100% {
                transform: translateX(260%);
            }
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

        .business-details-avatar img.business-details-paw {
            width: 28px;
            height: 22px;
            border-radius: 0;
            object-fit: contain;
        }

        .business-details-avatar-upload {
            position: relative;
            width: 250px;
            height: 250px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            border: 2px dashed #E2E2E2;
            border-radius: 999px;
            margin-top: 2.5rem;
            margin-left: 2.5rem;
            background: #FFFFFF;
            box-sizing: border-box;
            overflow: hidden;
        }

        .business-details-avatar-upload__hit {
            position: absolute;
            inset: 0;
            z-index: 1;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .business-details-avatar-upload__preview,
        .business-details-avatar-upload__empty {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            pointer-events: none;
        }

        .business-details-avatar-upload__preview {
            width: 100%;
            height: 100%;
        }

        .business-details-avatar-upload__preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 999px;
        }

        .business-details-avatar-upload__cloud {
            display: inline-flex;
            width: 37px;
            height: 34px;
        }

        .business-details-avatar-upload__cloud svg {
            width: 37px;
            height: 34px;
            display: block;
        }

        .business-details-avatar-upload__copy {
            display: grid;
            gap: 0.15rem;
            text-align: center;
            color: #3B3731;
            font-family: Lato;
            font-size: 12px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-avatar-upload__copy em {
            font-style: normal;
            color: #FFC97A;
        }

        .business-details-avatar-upload__browse {
            position: relative;
            z-index: 3;
            pointer-events: auto;
            border: 0;
            background: transparent;
            padding: 0;
            color: #FFC97A;
            font: inherit;
            text-decoration: underline;
            cursor: pointer;
        }

        .business-details-avatar-upload__input {
            display: none;
        }

        .business-details-avatar-upload__button-progress {
            position: absolute;
            left: 50%;
            bottom: 28px;
            transform: translateX(-50%);
            width: 110px;
            display: inline-grid;
            grid-template-columns: 1fr auto;
            align-items: center;
            gap: 0.45rem;
            color: #3B3731;
            font-size: 13px;
            line-height: 1;
            pointer-events: none;
        }

        .business-details-avatar-upload__progress-bar {
            width: 100%;
            height: 6px;
            overflow: hidden;
            border-radius: 999px;
            background: rgba(59, 55, 49, 0.12);
        }

        .business-details-avatar-upload__progress-bar span {
            height: 100%;
            display: block;
            border-radius: inherit;
            background: #FFC97A;
        }

        .business-details-avatar-upload__error {
            position: absolute;
            left: 50%;
            bottom: 16px;
            z-index: 3;
            transform: translateX(-50%);
            max-width: 210px;
            color: #B42318;
            text-align: center;
            font-family: Lato;
            font-size: 12px;
            font-style: normal;
            font-weight: 600;
            line-height: 1.3;
            pointer-events: none;
        }

        .business-details-gallery__item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .business-details-gallery__item img.business-details-gallery-paw {
            width: 40px;
            height: 32px;
            object-fit: contain;
        }

        .business-details-profile__copy {
            display: grid;
            gap: 2.5rem;
            align-content: start;
        }

        .business-details-gallery {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 20px;
        }

        .business-details-gallery__input {
            display: none;
        }

        .business-details-gallery__slot {
            position: relative;
            width: 100%;
            aspect-ratio: 1;
            height: auto;
            animation: business-details-gallery-tile-enter 0.12s ease-out both;
            transition: opacity 0.12s ease, transform 0.12s ease;
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
            width: 100%;
            height: 100%;
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
            background: #FAFAFA;
            border-style: dashed;
        }

        .business-details-gallery__upload-tile:has(.business-details-gallery__preview),
        .business-details-gallery__upload-tile:has(.business-details-gallery__current > img) {
            background: #FFFFFF;
            border-style: solid;
        }

        .business-details-gallery__dropzone {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px;
            box-sizing: border-box;
            text-align: center;
            color: #3B3731;
            font-family: Lato;
            font-size: 12px;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-gallery__dropzone>img {
            width: 37px;
            height: 34px;
            display: block;
            flex: 0 0 auto;
            object-fit: contain;
        }

        .business-details-gallery__dropzone span {
            display: grid;
            gap: 0;
        }

        .business-details-gallery__dropzone small {
            font-size: 12px;
            font-weight: 600;
            color: #9D9B98;
        }

        .business-details-gallery__dropzone em {
            font-style: normal;
            color: #FFC97A;
        }

        .business-details-gallery__dropzone u {
            color: #FFC97A;
            text-decoration: underline;
            text-underline-offset: 2px;
            text-decoration-thickness: from-font;
        }

        .business-details-gallery-caption {
            position: absolute;
            left: 1px;
            right: 1px;
            bottom: 1px;
            z-index: 2;
            height: 40px;
            display: flex;
            align-items: flex-end;
            padding: 0 10px 8px;
            border-radius: 0 0 8px 8px;
            background: linear-gradient(180deg, rgba(59, 55, 49, 0) 0%, #79756E 100%);
            color: #FFF;
            font-family: Lato;
            font-size: 12px;
            font-weight: 600;
            line-height: normal;
            pointer-events: none;
            box-sizing: border-box;
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
            top: 10px;
            right: 10px;
            z-index: 3;
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 0;
            border-radius: 999px;
            background: transparent;
            color: #3B3731;
            padding: 0;
            cursor: pointer;
            transition: transform 0.18s ease;
        }

        .business-details-gallery-remove:hover {
            background: transparent;
            transform: scale(1.05);
        }

        .business-details-gallery-remove:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .business-details-gallery-remove svg,
        .business-details-gallery-remove img {
            width: 36px;
            height: 36px;
            display: block;
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
            width: 40px;
            height: 32px;
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

        .business-details-gallery-add__plus--preview {
            z-index: 2;
            bottom: 12px;
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
            width: 28px;
            height: 22px;
            display: block;
            flex: 0 0 auto;
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
            .business-details-profile {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .business-details-gallery {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }

            .business-details-upload-layout {
                grid-template-columns: 1fr;
                align-items: stretch;
            }
        }

        @media (max-width: 640px) {

            .business-details-grid--three,
            .business-details-grid--four,
            .business-details-profile {
                grid-template-columns: 1fr;
            }

            .business-details-gallery {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .business-details-avatar {
                width: 12rem;
                height: 12rem;
                padding: 10px;
            }

            .business-details-upload-tabs {
                height: 42px;
            }

            .business-details-upload-tab {
                padding: 0 0.85rem;
            }
        }

        /* Settings design system (Figma 09 Business Profile - Settings). */
        .business-details-settings {
            padding-top: 0;
        }

        .business-details-alert {
            width: 100%;
            max-width: none;
            min-height: 36px;
            box-sizing: border-box;
            gap: 7px;
            margin: -20px 0 20px;
            padding: 8px;
            font-size: 14px;
            line-height: 18px;
        }

        .business-details-alert strong {
            font-size: 14px;
            font-weight: 400;
        }

        .business-details-alert__icon {
            width: 18px;
            height: 18px;
            padding: 0;
            background: transparent;
            transform: none;
        }

        .business-details-alert__icon img,
        .business-details-alert__icon svg {
            width: 18px;
            height: 18px;
            transform: none;
        }

        .business-details-alert__close {
            width: 18px;
            height: 18px;
        }

        .business-details-alert__close img,
        .business-details-alert__close svg {
            width: 11px;
            height: 11px;
        }

        .business-details-block {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            background: #FFF;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, .10);
            overflow: visible;
        }

        .business-details-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin: 0 0 20px;
        }

        .business-details-section-title h3 {
            width: auto;
            min-width: 0;
            padding: 0;
            border: 0;
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 20px;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-title-actions {
            position: relative;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .business-details-title-actions .business-details-edit,
        .business-details-title-actions .business-details-save {
            position: absolute;
            inset: 0;
        }

        .business-details-title-actions .business-details-edit {
            width: 36px;
            height: 36px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 0;
            border-radius: 999px;
            background: transparent;
            box-shadow: none;
            font-size: 0;
            cursor: pointer;
        }

        .business-details-title-actions .business-details-edit svg,
        .business-details-title-actions .business-details-edit img {
            width: 36px;
            height: 36px;
            display: block;
        }

        .business-details-title-actions .business-details-save {
            inset: 0 auto auto auto;
            right: 0;
            width: 126px;
            height: 36px;
            font-size: 14px;
        }

        .business-details-section-title:has(.business-details-edit--manage) .business-details-title-actions {
            width: auto;
            flex-basis: auto;
        }

        .business-details-title-actions .business-details-edit--manage {
            position: static;
            width: auto;
            height: auto;
            padding: 0;
            border: 0;
            border-radius: 0;
            background: transparent;
            box-shadow: none;
            color: #F4A340;
            font-family: Lato;
            font-size: 14px;
            font-weight: 700;
            line-height: normal;
        }

        .business-details-card {
            padding: 20px;
            border: 1px solid #F3F3F3;
            border-radius: 10px;
            background: #FCFCFC;
        }

        .business-details-card--editing {
            border-color: #F3F3F3;
            background: #FCFCFC;
        }

        .business-details-card--editing.business-details-grid {
            padding: 0;
            border: 0;
            background: transparent;
        }

        .business-details-label,
        .business-details-field span,
        .business-details-files__label {
            display: block;
            margin-bottom: 4px;
            color: #9C9790;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            text-transform: uppercase;
        }

        .business-details-field p,
        .business-details-value {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
            text-transform: none;
        }

        .business-details-card--editing .business-details-label {
            color: #9C9790;
            font-size: 14px;
            font-weight: 400;
            text-transform: uppercase;
        }

        .business-details-edit-grid {
            gap: 20px;
        }

        .business-details-card--editing .business-details-edit-grid {
            padding: 0 1.5rem;
        }

        .business-details-edit-stack {
            gap: 20px;
        }

        .business-details-profile__copy {
            gap: 20px;
        }

        .business-details-input-field {
            gap: 10px;
        }

        .business-details-input-field span {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
            text-transform: none;
        }

        .business-details-input-field span em {
            font-style: normal;
            font-weight: 600;
            color: #9D9B98;
        }

        .business-details-input-field input,
        .business-details-input-field textarea {
            width: 100%;
            min-height: 48px;
            padding: 11px 12px;
            border: 1px solid #DDD;
            border-radius: 10px;
            background: #FFF;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            line-height: 25px;
            box-sizing: border-box;
        }

        .business-details-input-field textarea {
            min-height: 96px;
            resize: vertical;
        }

        .business-details-avatar {
            width: 250px;
            height: 250px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #FFC97A;
            border-radius: 999px;
            margin-top: 0;
            margin-left: 0;
            padding: 3px;
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

        .business-details-avatar img.business-details-paw {
            width: 28px;
            height: 22px;
            border-radius: 0;
            object-fit: contain;
        }

        .business-details-avatar-upload {
            margin-top: 0;
            margin-left: 0;
            border: 2px dashed #E2E2E2;
            background: #FFF;
        }

        .business-details-avatar-upload__browse {
            color: #FFC97A;
            text-decoration: underline;
            text-underline-offset: 2px;
        }

        .business-details-profile {
            display: grid;
            grid-template-columns: 250px 1fr;
            gap: 48px;
            align-items: center;
        }

        .business-details-gallery {
            display: grid;
            grid-template-columns: repeat(5, minmax(0, 1fr));
            gap: 20px;
        }

        .business-details-gallery__item {
            width: 100%;
            height: 100%;
            border-radius: 9px;
        }

        .business-details-gallery__slot {
            width: 100%;
            height: auto;
            aspect-ratio: 1;
        }

        .business-details-verification {
            margin-bottom: 20px;
            padding: 20px;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            background: #EEEEEE;
            color: #3B3731;
        }

        .business-details-verification__heading h3 {
            margin: 0 0 24px;
            font-family: "Playfair Display";
            font-size: 20px;
            font-weight: 600;
            line-height: normal;
            color: #3B3731;
        }

        .business-details-verification__summary {
            display: flex;
            align-items: flex-start;
            justify-content: start;
            gap: 22.5rem;
            margin-bottom: 24px;
        }

        .business-details-verification__summary span,
        .business-details-verification__status span {
            display: block;
            color: #9C9790;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            text-transform: uppercase;
        }

        .business-details-verification__summary p {
            margin: 0;
            color: #3B3731;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-verification__status {
            flex: 0 0 auto;
            text-align: left;
        }

        .business-details-verification__status strong {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            margin-top: 2px;
            color: #AFCD6F;
            font-size: 14px;
            font-weight: 500;
            line-height: normal;
        }

        .business-details-verification__status img {
            width: 12px;
            height: 12px;
            display: block;
            flex: 0 0 auto;
        }

        .business-details-verification__provider {
            overflow: hidden;
            border: 1px solid #F3F3F3;
            border-radius: 10px;
            background: #FCFCFC;
        }

        .business-details-verification__provider-intro {
            padding: 20px;
        }

        .business-details-verification__provider-intro strong {
            display: block;
            margin-bottom: 0;
            color: #3B3731;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
        }

        .business-details-verification__provider-intro span {
            display: block;
            color: #9C9790;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            text-transform: none;
        }

        .business-details-verification__meta {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            align-items: center;
            gap: 16px;
            min-height: 50px;
            padding: 15px 20px;
            border-top: 1px solid #F3F3F3;
            background: #FFFFFF;
        }

        .business-details-verification__meta p {
            margin: 0;
            color: #3B3731;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
        }

        .business-details-verification__meta span {
            color: #9C9A97;
            font-size: 14px;
            font-weight: 400;
            text-transform: none;
        }

        .business-details-verification__pass {
            color: #9FC356;
            font-style: normal;
            font-weight: 400;
        }

        .business-details-payout-status {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            color: #BACF8E !important;
            font-weight: 600 !important;
        }

        .business-details-save-bar {
            min-height: 80px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            margin: 20px 0 0;
            padding: 19px 20px;
            border: 0;
            border-top: 1px solid #FFC56D;
            border-radius: 0;
            background: #FFFCF6;
            box-sizing: border-box;
        }

        .business-details-save-bar>strong {
            color: #FFAE37;
            font-family: Lato;
            font-size: 18px;
            font-weight: 600;
            line-height: normal;
        }

        .business-details-save-bar__actions {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .business-details-save-bar__cancel,
        .business-details-save-bar__submit {
            width: 138px;
            height: 42px;
            padding: 0;
            border-radius: 100px;
            font-family: Lato;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
        }

        .business-details-save-bar__cancel {
            border: 1px solid #D9D9D9;
            background: #FFF;
            color: #9D9B98;
        }

        .business-details-save-bar__submit {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            border: 0;
            background: #BACF8E;
            color: #FFF;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .business-details-save-bar__submit:disabled {
            cursor: wait;
            opacity: .7;
        }

        .business-details-settings--editing .business-details-avatar-upload {
            border-style: dashed;
            border-width: 2px;
            border-color: #E2E2E2;
        }

        @media (max-width: 991px) {
            .business-details-profile {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .business-details-avatar,
            .business-details-avatar-upload {
                margin-left: auto;
                margin-right: auto;
            }
        }

        @media (max-width: 640px) {
            .business-details-alert {
                margin-top: -8px;
            }

            .business-details-avatar,
            .business-details-avatar-upload {
                width: 200px;
                height: 200px;
            }

            .business-details-verification__summary {
                flex-direction: column;
                align-items: flex-start;
            }

            .business-details-verification__meta {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .business-details-verification__meta p {
                white-space: normal;
            }

            .business-details-save-bar {
                flex-direction: column;
                align-items: stretch;
                gap: 16px;
            }

            .business-details-save-bar__actions {
                justify-content: stretch;
            }

            .business-details-save-bar__cancel,
            .business-details-save-bar__submit {
                flex: 1;
            }
        }
    </style>
</div>
