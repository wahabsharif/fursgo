<?php

use App\Models\GroomerSpacerProfile;
use App\Models\ServicePolicy;
use Livewire\Volt\Component;

new class extends Component {
    public ?string $editingSection = null;

    public string $cancellationWindow = '';
    public string $cancellationFee = '';
    public string $noShowFee = '';
    public bool $lateCancellationFeeEnabled = true;
    public bool $noShowFeeEnabled = true;

    public string $gracePeriod = '';
    public string $lateArrivalFee = '';
    public bool $lateArrivalFeeEnabled = true;
    public string $lateArrivalFeeAmount = '';
    public string $lateArrivalFeeMinutes = '';

    public bool $refundPolicy = true;
    public string $serviceLimitationsText = '';
    public array $selectedServiceLimitations = [];
    public array $customServiceLimitations = [];
    public string $customServiceLimitation = '';
    public bool $animalWelfareStatement = true;
    public string $hygieneSafetyStandardsText = '';
    public array $selectedHygieneSafetyStandards = [];
    public array $customHygieneSafetyStandards = [];
    public string $customHygieneSafetyStandard = '';
    public bool $complianceDeclaration = true;
    public string $complianceVerifyDatesText = '';

    public function mount(): void
    {
        $this->hydrateEditableFields();
    }

    public function with(): array
    {
        $data = $this->policyData();
        $policy = $this->policy();
        $cancellation = $data['cancellation_policy'][0] ?? [];
        $lateArrival = $data['late_arrival_policy'][0] ?? [];
        $timeline = $data['compliance_timeline']['verify Dates'] ?? [];

        return [
            'cancellation' => $cancellation,
            'lateArrival' => $lateArrival,
            'refundPolicyEnabled' => (bool) ($data['refund_policy'] ?? false),
            'refundPolicyUploadedDate' => $policy?->created_at?->format('d M Y') ?? 'Not uploaded',
            'serviceLimitations' => $this->nonEmptyStrings($data['service_limitations'] ?? []),
            'serviceLimitationPresets' => $this->serviceLimitationPresets(),
            'animalWelfareEnabled' => (bool) ($data['animal_welfare_statement'] ?? false),
            'animalWelfareUploadedDate' => $policy?->created_at?->format('d M Y') ?? 'Not uploaded',
            'hygieneSafetyStandards' => $this->nonEmptyStrings($data['hygiene_safety_standards'] ?? []),
            'hygieneSafetyPresets' => $this->hygieneSafetyPresets(),
            'complianceDeclarationEnabled' => (bool) ($data['compliance_declaration'] ?? false),
            'complianceDeclarationUploadedDate' => $policy?->created_at?->format('d M Y') ?? 'Not uploaded',
            'verifyDates' => $this->nonEmptyStrings($timeline),
        ];
    }

    public function editSection(string $section): void
    {
        if (!in_array($section, $this->editableSections(), true)) {
            return;
        }

        $this->hydrateEditableFields();
        $this->editingSection = $section;
    }

    public function cancelEdit(): void
    {
        $this->resetValidation();
        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    public function saveCancellationPolicy(): void
    {
        $validated = $this->validate([
            'cancellationWindow' => ['nullable', 'string', 'max:255'],
            'lateCancellationFeeEnabled' => ['boolean'],
            'noShowFeeEnabled' => ['boolean'],
            'cancellationFee' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'noShowFee' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $this->savePolicyAttributes([
            'cancellation_policy' => [
                [
                    'Cancellation Window' => $validated['cancellationWindow'] ?? '',
                    'Cancellation Fee' => $this->lateCancellationFeeEnabled ? $this->feeText('Late Cancellation Fee', $validated['cancellationFee'] ?? '') : '',
                    'No Show Fee' => $this->noShowFeeEnabled ? $this->feeText('', $validated['noShowFee'] ?? '') : '',
                ],
            ],
        ]);
    }

    public function saveLateArrivalPolicy(): void
    {
        $validated = $this->validate([
            'gracePeriod' => ['nullable', 'string', 'max:255'],
            'lateArrivalFeeEnabled' => ['boolean'],
            'lateArrivalFeeAmount' => ['nullable', 'numeric', 'min:0', 'max:999'],
            'lateArrivalFeeMinutes' => ['nullable', 'numeric', 'min:0', 'max:999'],
        ]);

        $this->savePolicyAttributes([
            'late_arrival_policy' => [
                [
                    'Grace Period' => $validated['gracePeriod'] ?? '',
                    'Late Arrival Fee (Optional)' => $this->lateArrivalFeeEnabled ? $this->lateArrivalFeeText($validated['lateArrivalFeeAmount'] ?? '', $validated['lateArrivalFeeMinutes'] ?? '') : '',
                ],
            ],
        ]);
    }

    public function saveRefundPolicy(): void
    {
        $this->savePolicyAttributes([
            'refund_policy' => $this->refundPolicy,
        ]);
    }

    public function declineRefundPolicy(): void
    {
        $this->refundPolicy = false;
        $this->saveRefundPolicy();
    }

    public function acceptRefundPolicy(): void
    {
        $this->refundPolicy = true;
        $this->saveRefundPolicy();
    }

    public function saveServiceLimitations(): void
    {
        $this->validate([
            'selectedServiceLimitations' => ['array'],
            'selectedServiceLimitations.*' => ['string', 'max:255'],
            'customServiceLimitations' => ['array'],
            'customServiceLimitations.*' => ['string', 'max:255'],
            'customServiceLimitation' => ['nullable', 'string', 'max:255'],
        ]);

        $this->addCustomServiceLimitation();

        $this->savePolicyAttributes([
            'service_limitations' => $this->serviceLimitationsForSave(),
        ]);
    }

    public function addCustomServiceLimitation(): void
    {
        $limitation = trim($this->customServiceLimitation);

        if ($limitation === '') {
            return;
        }

        $existing = array_merge($this->selectedServiceLimitations, $this->customServiceLimitations);

        if (!in_array($limitation, $existing, true)) {
            $this->customServiceLimitations[] = $limitation;
        }

        $this->customServiceLimitation = '';
    }

    public function removeCustomServiceLimitation(int $index): void
    {
        unset($this->customServiceLimitations[$index]);
        $this->customServiceLimitations = array_values($this->customServiceLimitations);
    }

    public function saveAnimalWelfareStatement(): void
    {
        $this->savePolicyAttributes([
            'animal_welfare_statement' => $this->animalWelfareStatement,
        ]);
    }

    public function declineAnimalWelfareStatement(): void
    {
        $this->animalWelfareStatement = false;
        $this->saveAnimalWelfareStatement();
    }

    public function acceptAnimalWelfareStatement(): void
    {
        $this->animalWelfareStatement = true;
        $this->saveAnimalWelfareStatement();
    }

    public function saveHygieneSafetyStandards(): void
    {
        $this->validate([
            'selectedHygieneSafetyStandards' => ['array'],
            'selectedHygieneSafetyStandards.*' => ['string', 'max:255'],
            'customHygieneSafetyStandards' => ['array'],
            'customHygieneSafetyStandards.*' => ['string', 'max:255'],
            'customHygieneSafetyStandard' => ['nullable', 'string', 'max:255'],
        ]);

        $this->addCustomHygieneSafetyStandard();

        $this->savePolicyAttributes([
            'hygiene_safety_standards' => $this->hygieneSafetyStandardsForSave(),
        ]);
    }

    public function addCustomHygieneSafetyStandard(): void
    {
        $standard = trim($this->customHygieneSafetyStandard);

        if ($standard === '') {
            return;
        }

        $existing = array_merge($this->selectedHygieneSafetyStandards, $this->customHygieneSafetyStandards);

        if (!in_array($standard, $existing, true)) {
            $this->customHygieneSafetyStandards[] = $standard;
        }

        $this->customHygieneSafetyStandard = '';
    }

    public function removeCustomHygieneSafetyStandard(int $index): void
    {
        unset($this->customHygieneSafetyStandards[$index]);
        $this->customHygieneSafetyStandards = array_values($this->customHygieneSafetyStandards);
    }

    public function saveComplianceDeclaration(): void
    {
        $this->savePolicyAttributes([
            'compliance_declaration' => $this->complianceDeclaration,
        ]);
    }

    public function declineComplianceDeclaration(): void
    {
        $this->complianceDeclaration = false;
        $this->saveComplianceDeclaration();
    }

    public function acceptComplianceDeclaration(): void
    {
        $this->complianceDeclaration = true;
        $this->saveComplianceDeclaration();
    }

    public function saveComplianceTimeline(): void
    {
        $this->validate([
            'complianceVerifyDatesText' => ['nullable', 'string', 'max:1000'],
        ]);

        $this->savePolicyAttributes([
            'compliance_timeline' => [
                'verify Dates' => $this->linesToArray($this->complianceVerifyDatesText),
            ],
        ]);
    }

    private function hydrateEditableFields(): void
    {
        $data = $this->policyData();
        $cancellation = $data['cancellation_policy'][0] ?? [];
        $lateArrival = $data['late_arrival_policy'][0] ?? [];
        $timeline = $data['compliance_timeline']['verify Dates'] ?? [];

        $this->cancellationWindow = (string) ($cancellation['Cancellation Window'] ?? '');
        $this->cancellationFee = $this->percentValue($cancellation['Cancellation Fee'] ?? '');
        $this->noShowFee = $this->percentValue($cancellation['No Show Fee'] ?? '');
        $this->lateCancellationFeeEnabled = trim((string) ($cancellation['Cancellation Fee'] ?? '')) !== '';
        $this->noShowFeeEnabled = trim((string) ($cancellation['No Show Fee'] ?? '')) !== '';
        $this->gracePeriod = (string) ($lateArrival['Grace Period'] ?? '');
        $this->lateArrivalFee = (string) ($lateArrival['Late Arrival Fee (Optional)'] ?? '');
        $this->lateArrivalFeeEnabled = trim($this->lateArrivalFee) !== '';
        $this->lateArrivalFeeAmount = $this->moneyValue($this->lateArrivalFee);
        $this->lateArrivalFeeMinutes = $this->minuteValue($this->lateArrivalFee);
        $this->refundPolicy = (bool) ($data['refund_policy'] ?? true);
        $this->serviceLimitationsText = implode(PHP_EOL, $this->nonEmptyStrings($data['service_limitations'] ?? []));
        $this->hydrateServiceLimitations($data['service_limitations'] ?? []);
        $this->animalWelfareStatement = (bool) ($data['animal_welfare_statement'] ?? true);
        $this->hygieneSafetyStandardsText = implode(PHP_EOL, $this->nonEmptyStrings($data['hygiene_safety_standards'] ?? []));
        $this->hydrateHygieneSafetyStandards($data['hygiene_safety_standards'] ?? []);
        $this->complianceDeclaration = (bool) ($data['compliance_declaration'] ?? true);
        $this->complianceVerifyDatesText = implode(PHP_EOL, $this->nonEmptyStrings($timeline));
    }

    private function policyData(): array
    {
        $policy = $this->policy();

        if (!$policy) {
            return $this->defaultPolicyAttributes();
        }

        return array_merge($this->defaultPolicyAttributes(), [
            'cancellation_policy' => $this->arrayValue($policy->cancellation_policy),
            'late_arrival_policy' => $this->arrayValue($policy->late_arrival_policy),
            'refund_policy' => (bool) $policy->refund_policy,
            'service_limitations' => $this->arrayValue($policy->service_limitations),
            'animal_welfare_statement' => (bool) $policy->animal_welfare_statement,
            'hygiene_safety_standards' => $this->arrayValue($policy->hygiene_safety_standards),
            'compliance_declaration' => (bool) $policy->compliance_declaration,
            'compliance_timeline' => $this->arrayValue($policy->compliance_timeline),
        ]);
    }

    private function savePolicyAttributes(array $attributes): void
    {
        $profile = $this->profile();

        if (!$profile) {
            return;
        }

        $policy = ServicePolicy::firstOrNew([
            'goormer_spacer_profiles_id' => $profile->id,
        ]);

        if (!$policy->exists) {
            $policy->fill($this->defaultPolicyAttributes());
        }

        $policy->fill($attributes);
        $policy->save();

        $this->editingSection = null;
        $this->hydrateEditableFields();
    }

    private function policy(): ?ServicePolicy
    {
        $profile = $this->profile();

        if (!$profile) {
            return null;
        }

        return ServicePolicy::where('goormer_spacer_profiles_id', $profile->id)->first();
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

    private function defaultPolicyAttributes(): array
    {
        return [
            'cancellation_policy' => [
                [
                    'Cancellation Window' => '24 hours before appointment',
                    'Cancellation Fee' => 'Late Cancellation Fee 50% of booking price',
                    'No Show Fee' => '100% of booking price',
                ],
            ],
            'late_arrival_policy' => [
                [
                    'Grace Period' => '10 minutes',
                    'Late Arrival Fee (Optional)' => '£10 after 15 mins',
                ],
            ],
            'refund_policy' => true,
            'service_limitations' => ['No sedated pets', 'No aggressive pets without consultation', 'No severe matting without assessment', 'Weight or size restrictions apply'],
            'animal_welfare_statement' => true,
            'hygiene_safety_standards' => ['Tools sanitised between pets', 'Clean grooming table after each appointment', 'Fresh towels used per pet', 'Equipment safety checked'],
            'compliance_declaration' => true,
            'compliance_timeline' => [
                'verify Dates' => ['12 Jan 2026', '12 Jan 2025', '12 Jan 2024', '12 Jan 2023'],
            ],
        ];
    }

    private function editableSections(): array
    {
        return ['cancellation', 'late-arrival', 'refund', 'limitations', 'animal-welfare', 'hygiene-safety', 'compliance'];
    }

    private function serviceLimitationPresets(): array
    {
        return ['No sedated pets', 'No aggressive pets without consultation', 'No severe matting without assessment', 'Weight or size restrictions apply'];
    }

    private function hygieneSafetyPresets(): array
    {
        return ['Tools sanitised between pets', 'Clean grooming table after each appointment', 'Fresh towels used per pet', 'Equipment safety checked'];
    }

    private function hydrateServiceLimitations(mixed $limitations): void
    {
        $limitations = $this->nonEmptyStrings($limitations);
        $presets = $this->serviceLimitationPresets();

        $this->selectedServiceLimitations = array_values(array_filter($presets, static fn($preset) => in_array($preset, $limitations, true)));
        $this->customServiceLimitations = array_values(array_filter($limitations, static fn($limitation) => !in_array($limitation, $presets, true)));
        $this->customServiceLimitation = '';
    }

    private function serviceLimitationsForSave(): array
    {
        $presets = $this->serviceLimitationPresets();
        $selectedPresets = array_values(array_filter($presets, fn($preset) => in_array($preset, $this->selectedServiceLimitations, true)));
        $customLimitations = $this->nonEmptyStrings($this->customServiceLimitations);

        $limitations = array_merge($selectedPresets, $customLimitations);
        $this->serviceLimitationsText = implode(PHP_EOL, $limitations);

        return $limitations;
    }

    private function hydrateHygieneSafetyStandards(mixed $standards): void
    {
        $standards = $this->nonEmptyStrings($standards);
        $presets = $this->hygieneSafetyPresets();

        $this->selectedHygieneSafetyStandards = array_values(array_filter($presets, static fn($preset) => in_array($preset, $standards, true)));
        $this->customHygieneSafetyStandards = array_values(array_filter($standards, static fn($standard) => !in_array($standard, $presets, true)));
        $this->customHygieneSafetyStandard = '';
    }

    private function hygieneSafetyStandardsForSave(): array
    {
        $presets = $this->hygieneSafetyPresets();
        $selectedPresets = array_values(array_filter($presets, fn($preset) => in_array($preset, $this->selectedHygieneSafetyStandards, true)));
        $customStandards = $this->nonEmptyStrings($this->customHygieneSafetyStandards);

        $standards = array_merge($selectedPresets, $customStandards);
        $this->hygieneSafetyStandardsText = implode(PHP_EOL, $standards);

        return $standards;
    }

    private function linesToArray(string $value): array
    {
        $lines = preg_split('/\r\n|\r|\n/', $value) ?: [];

        return array_values(array_filter(array_map(static fn($line) => trim((string) $line), $lines), static fn($line) => $line !== ''));
    }

    private function nonEmptyStrings(mixed $value): array
    {
        return array_values(array_filter(array_map(static fn($item) => trim((string) $item), is_array($value) ? $value : []), static fn($item) => $item !== ''));
    }

    private function arrayValue(mixed $value): array
    {
        if (is_array($value)) {
            return $value;
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }

    private function percentValue(mixed $value): string
    {
        if (preg_match('/(\d+(?:\.\d+)?)\s*%/', (string) $value, $matches)) {
            return $matches[1];
        }

        if (is_numeric($value)) {
            return (string) $value;
        }

        return '';
    }

    private function feeText(string $label, mixed $value): string
    {
        if ($value === '' || $value === null) {
            return '';
        }

        $percentage = (string) $value;
        if (str_contains($percentage, '.')) {
            $percentage = rtrim(rtrim($percentage, '0'), '.');
        }

        $prefix = $label !== '' ? "{$label} " : '';

        return "{$prefix}{$percentage}% of booking price";
    }

    private function lateArrivalFeeText(mixed $amount, mixed $minutes): string
    {
        if ($amount === '' || $amount === null || $minutes === '' || $minutes === null) {
            return '';
        }

        return '£' . $this->trimNumber($amount) . ' after ' . $this->trimNumber($minutes) . ' mins';
    }

    private function moneyValue(mixed $value): string
    {
        if (preg_match('/£\s*(\d+(?:\.\d+)?)/', (string) $value, $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function minuteValue(mixed $value): string
    {
        if (preg_match('/after\s*(\d+(?:\.\d+)?)\s*mins?/i', (string) $value, $matches)) {
            return $matches[1];
        }

        return '';
    }

    private function trimNumber(mixed $value): string
    {
        $number = (string) $value;

        return str_contains($number, '.') ? rtrim(rtrim($number, '0'), '.') : $number;
    }
}; ?>

<div class="service-policies-settings" x-data="{
    showServicePoliciesAlert: true,
    editingSection: @entangle('editingSection').live,
    cancellationWindowValue: @entangle('cancellationWindow').live,
    gracePeriodValue: @entangle('gracePeriod').live,
    openCancellationWindow: false,
    openGracePeriod: false,
    isEditing(section) {
        return this.editingSection === section;
    },
    editSection(section) {
        this.editingSection = section;
        this.$wire.call('editSection', section);
    },
    cancelEdit() {
        if (!this.editingSection) {
            return;
        }

        this.editingSection = null;
        this.$wire.call('cancelEdit');
    },
    saveSection(action) {
        return Promise.resolve(this.$wire.call(action)).then(() => {
            this.editingSection = null;
        });
    },
}" x-on:keydown.escape.window="cancelEdit()">
    <div class="service-policies-heading">
        <div>
            <h2>Service Policies</h2>
        </div>
        <div class="service-policies-status">
            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 24 24" fill="none">
                <path
                    d="M12 0C5.4 0 0 5.4 0 12C0 18.6 5.4 24 12 24C18.6 24 24 18.6 24 12C24 5.4 18.6 0 12 0ZM9.6 18L3.6 12L5.292 10.308L9.6 14.604L18.708 5.496L20.4 7.2L9.6 18Z"
                    fill="#C9DDA0" />
            </svg> Verified &amp; Active
        </div>
    </div>

    <div class="service-policies-alert" x-show="showServicePoliciesAlert" x-cloak role="status"
        x-transition:enter="service-policies-alert-enter" x-transition:enter-start="service-policies-alert-enter-start"
        x-transition:enter-end="service-policies-alert-enter-end" x-transition:leave="service-policies-alert-leave"
        x-transition:leave-start="service-policies-alert-leave-start"
        x-transition:leave-end="service-policies-alert-leave-end">
        <span class="service-policies-alert__icon" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="3" height="14" viewBox="0 0 3 14" fill="none">
                <path
                    d="M2.196 0V5.148C2.196 5.688 2.172 6.219 2.124 6.741C2.076 7.257 2.013 7.815 1.935 8.415H0.63C0.546 7.815 0.48 7.257 0.432 6.741C0.39 6.219 0.369 5.688 0.369 5.148V0H2.196ZM0 11.844C0 11.67 0.03 11.508 0.09 11.358C0.156 11.202 0.246 11.067 0.36 10.953C0.474 10.839 0.606 10.749 0.756 10.683C0.906 10.617 1.071 10.584 1.251 10.584C1.425 10.584 1.587 10.617 1.737 10.683C1.893 10.749 2.025 10.839 2.133 10.953C2.247 11.067 2.337 11.202 2.403 11.358C2.469 11.508 2.502 11.67 2.502 11.844C2.502 12.024 2.469 12.189 2.403 12.339C2.337 12.489 2.247 12.621 2.133 12.735C2.025 12.849 1.893 12.936 1.737 12.996C1.587 13.062 1.425 13.095 1.251 13.095C1.071 13.095 0.906 13.062 0.756 12.996C0.606 12.936 0.474 12.849 0.36 12.735C0.246 12.621 0.156 12.489 0.09 12.339C0.03 12.189 0 12.024 0 11.844Z"
                    fill="white" />
            </svg>
        </span>
        <div>
            <strong>These policies are displayed to customers before booking.</strong>
            <span>Clear policies reduce disputes and improve trust.</span>
        </div>
        <button type="button" class="service-policies-alert__close" @click="showServicePoliciesAlert = false"
            aria-label="Dismiss service policies alert"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                height="14" viewBox="0 0 24 14" fill="none">
                <path
                    d="M6.65625 13.7071C6.26572 14.0976 5.63256 14.0976 5.24204 13.7071C4.85151 13.3166 4.85151 12.6834 5.24204 12.2929L5.94914 13L6.65625 13.7071ZM11.9999 6.94921L12.707 6.24211C13.0975 6.63263 13.0975 7.2658 12.707 7.65632L11.9999 6.94921ZM5.34361 1.70711C4.95309 1.31658 4.95309 0.683417 5.34361 0.292892C5.73413 -0.0976337 6.3673 -0.0976336 6.75782 0.292892L6.05072 1L5.34361 1.70711ZM5.94914 13L5.24204 12.2929L11.2928 6.24211L11.9999 6.94921L12.707 7.65632L6.65625 13.7071L5.94914 13ZM11.9999 6.94921L11.2928 7.65632L5.34361 1.70711L6.05072 1L6.75782 0.292892L12.707 6.24211L11.9999 6.94921Z"
                    fill="#B4CCDD" />
                <path
                    d="M17.3025 13.7071C17.693 14.0976 18.3262 14.0976 18.7167 13.7071C19.1072 13.3166 19.1072 12.6834 18.7167 12.2929L18.0096 13L17.3025 13.7071ZM11.9588 6.94921L11.2517 6.24211C10.8612 6.63263 10.8612 7.2658 11.2517 7.65632L11.9588 6.94921ZM18.6151 1.70711C19.0056 1.31658 19.0056 0.683417 18.6151 0.292892C18.2246 -0.0976337 17.5914 -0.0976336 17.2009 0.292892L17.908 1L18.6151 1.70711ZM18.0096 13L18.7167 12.2929L12.6659 6.24211L11.9588 6.94921L11.2517 7.65632L17.3025 13.7071L18.0096 13ZM11.9588 6.94921L12.6659 7.65632L18.6151 1.70711L17.908 1L17.2009 0.292892L11.2517 6.24211L11.9588 6.94921Z"
                    fill="#B4CCDD" />
            </svg></button>
    </div>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Cancellation Policy</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak x-show="editingSection === 'cancellation'"
                    @click="saveSection('saveCancellationPolicy')" wire:loading.attr="disabled"
                    wire:target="saveCancellationPolicy">
                    <span wire:loading.remove wire:target="saveCancellationPolicy">Save Details</span>
                    <span class="service-policies-saving" wire:loading.flex wire:target="saveCancellationPolicy">
                        <span class="service-policies-spinner" aria-hidden="true"></span>
                        Saving
                    </span>
                </button>
                <button type="button" class="service-policies-edit" x-cloak x-show="editingSection !== 'cancellation'"
                    @click="editSection('cancellation')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card" :class="{ 'service-policies-card--editing': isEditing('cancellation') }">
            @php
                $cancellationWindowOptions = [
                    '24 hours before appointment',
                    '48 hours before appointment',
                    '72 hours before appointment',
                    '7 days before appointment',
                ];
            @endphp
            <div class="service-policies-cancellation-editor" x-cloak x-show="isEditing('cancellation')">
                <label class="service-policies-window-field">
                    <span>Cancellation Window</span>
                    <x-dashboard.services.duration-select model="cancellationWindowValue"
                        open-key="openCancellationWindow" :options="$cancellationWindowOptions" />
                    <small>How long before the appointment customers can cancel without penalty.</small>
                </label>

                <div class="service-policies-fee-group">
                    <span>Cancellation Fee</span>
                    <div class="service-policies-fee-row">
                        <label class="service-policies-fee-dot">
                            <input type="checkbox" wire:model.live="lateCancellationFeeEnabled"
                                aria-label="Enable late cancellation fee">
                            <span aria-hidden="true"></span>
                        </label>
                        <strong>Late Cancellation Fee</strong>
                        <span class="service-policies-percent-input" x-data="{
                            shakePercent: false,
                            syncWidth() {
                                const input = this.$refs.input;
                                input.style.width = `${Math.min(Math.max((input.value || '0').length + 0.35, 1.35), 4)}ch`;
                            },
                            triggerPercentShake() {
                                this.shakePercent = false;
                                this.$nextTick(() => {
                                    this.shakePercent = true;
                                    setTimeout(() => this.shakePercent = false, 360);
                                });
                            },
                            clampPercent() {
                                const input = this.$refs.input;
                                if (Number(input.value) > 100) {
                                    input.value = 100;
                                    this.triggerPercentShake();
                                }
                            }
                        }" x-init="$nextTick(() => syncWidth())"
                            :class="{ 'service-policies-percent-input--shake': shakePercent }">
                            <input type="number" min="0" max="100" step="1"
                                wire:model.defer="cancellationFee" aria-label="Late cancellation fee percentage"
                                x-ref="input" @input="clampPercent(); syncWidth()"
                                @change="clampPercent(); syncWidth()"
                                @wheel.prevent="
                                    if (!$el.disabled) {
                                        if ($event.deltaY < 0 && Number($el.value || 0) >= Number($el.max || 100)) {
                                            triggerPercentShake();
                                        } else {
                                            $event.deltaY < 0 ? $el.stepUp() : $el.stepDown();
                                            $el.dispatchEvent(new Event('input', { bubbles: true }));
                                            $el.dispatchEvent(new Event('change', { bubbles: true }));
                                        }
                                    }
                                "
                                @disabled(!$lateCancellationFeeEnabled)>
                            <span class="service-policies-percent-symbol" aria-hidden="true">%</span>
                            <span class="service-policies-stepper">
                                <button type="button" aria-label="Increase late cancellation fee"
                                    @click.prevent="
                                        const input = $el.closest('.service-policies-percent-input').querySelector('input');
                                        if (input.disabled) return;
                                        input.stepUp();
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                        <path d="M10.374 5.479L5.3952 0.500185L0.499963 5.39543" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <button type="button" aria-label="Decrease late cancellation fee"
                                    @click.prevent="
                                        const input = $el.closest('.service-policies-percent-input').querySelector('input');
                                        if (input.disabled) return;
                                        input.stepDown();
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                        <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </span>
                        </span>
                        <em>of booking price</em>
                    </div>
                    <small>Fee charged if a customer cancels after the allowed window.</small>
                </div>

                <div class="service-policies-fee-group">
                    <div class="service-policies-fee-row">
                        <label class="service-policies-fee-dot">
                            <input type="checkbox" wire:model.live="noShowFeeEnabled"
                                aria-label="Enable no show fee">
                            <span aria-hidden="true"></span>
                        </label>
                        <strong>No Show Fee</strong>
                        <span class="service-policies-percent-input" x-data="{
                            shakePercent: false,
                            syncWidth() {
                                const input = this.$refs.input;
                                input.style.width = `${Math.min(Math.max((input.value || '0').length + 0.35, 1.35), 4)}ch`;
                            },
                            triggerPercentShake() {
                                this.shakePercent = false;
                                this.$nextTick(() => {
                                    this.shakePercent = true;
                                    setTimeout(() => this.shakePercent = false, 360);
                                });
                            },
                            clampPercent() {
                                const input = this.$refs.input;
                                if (Number(input.value) > 100) {
                                    input.value = 100;
                                    this.triggerPercentShake();
                                }
                            }
                        }" x-init="$nextTick(() => syncWidth())"
                            :class="{ 'service-policies-percent-input--shake': shakePercent }">
                            <input type="number" min="0" max="100" step="1"
                                wire:model.defer="noShowFee" aria-label="No show fee percentage" x-ref="input"
                                @input="clampPercent(); syncWidth()" @change="clampPercent(); syncWidth()"
                                @wheel.prevent="
                                    if (!$el.disabled) {
                                        if ($event.deltaY < 0 && Number($el.value || 0) >= Number($el.max || 100)) {
                                            triggerPercentShake();
                                        } else {
                                            $event.deltaY < 0 ? $el.stepUp() : $el.stepDown();
                                            $el.dispatchEvent(new Event('input', { bubbles: true }));
                                            $el.dispatchEvent(new Event('change', { bubbles: true }));
                                        }
                                    }
                                "
                                @disabled(!$noShowFeeEnabled)>
                            <span class="service-policies-percent-symbol" aria-hidden="true">%</span>
                            <span class="service-policies-stepper">
                                <button type="button" aria-label="Increase no show fee"
                                    @click.prevent="
                                        const input = $el.closest('.service-policies-percent-input').querySelector('input');
                                        if (input.disabled) return;
                                        input.stepUp();
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                        <path d="M10.374 5.479L5.3952 0.500185L0.499963 5.39543" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <button type="button" aria-label="Decrease no show fee"
                                    @click.prevent="
                                        const input = $el.closest('.service-policies-percent-input').querySelector('input');
                                        if (input.disabled) return;
                                        input.stepDown();
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                        <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </span>
                        </span>
                        <em>of booking price</em>
                    </div>
                    <small>Charged if a customer does not attend their appointment.</small>
                </div>
            </div>
            <div class="service-policies-value-grid service-policies-value-grid--three"
                x-show="!isEditing('cancellation')">
                <div>
                    <span>Cancellation Window</span>
                    <p>{{ blank($cancellation['Cancellation Window'] ?? null) ? 'No data found' : $cancellation['Cancellation Window'] }}
                    </p>
                </div>
                <div>
                    <span>Cancellation Fee</span>
                    <p>{{ blank($cancellation['Cancellation Fee'] ?? null) ? 'No data found' : $cancellation['Cancellation Fee'] }}
                    </p>
                </div>
                <div>
                    <span>No Show Fee</span>
                    <p>{{ blank($cancellation['No Show Fee'] ?? null) ? 'No data found' : $cancellation['No Show Fee'] }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Late Arrival Policy</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak
                    x-show="editingSection === 'late-arrival'" @click="saveSection('saveLateArrivalPolicy')"
                    wire:loading.attr="disabled" wire:target="saveLateArrivalPolicy">
                    <span wire:loading.remove wire:target="saveLateArrivalPolicy">Save Details</span>
                    <span class="service-policies-saving" wire:loading.flex wire:target="saveLateArrivalPolicy">
                        <span class="service-policies-spinner" aria-hidden="true"></span>
                        Saving
                    </span>
                </button>
                <button type="button" class="service-policies-edit" x-cloak
                    x-show="editingSection !== 'late-arrival'" @click="editSection('late-arrival')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card" :class="{ 'service-policies-card--editing': isEditing('late-arrival') }">
            <div class="service-policies-late-editor" x-cloak x-show="isEditing('late-arrival')">
                <label class="service-policies-window-field">
                    <span>Grace Period</span>
                    <x-dashboard.services.duration-select model="gracePeriodValue" open-key="openGracePeriod"
                        :options="['5 minutes', '10 minutes', '15 minutes', '30 minutes']" />
                    <small>How long a customer can arrive late before the booking may be cancelled.</small>
                </label>

                <div class="service-policies-fee-group">
                    <span>Late Arrival Fee <em>(Optional)</em></span>
                    <div class="service-policies-fee-row service-policies-fee-row--late">
                        <label class="service-policies-switch-check">
                            <input type="checkbox" wire:model.live="lateArrivalFeeEnabled"
                                aria-label="Enable late arrival fee">
                            <span aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="11"
                                    viewBox="0 0 14 11" fill="none">
                                    <path d="M1 5.5L5.07143 9.5L13 1" stroke="white" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                        </label>

                        <span class="service-policies-number-input service-policies-money-input"
                            x-data="{
                                syncWidth() {
                                    const input = this.$refs.input;
                                    input.style.width = '4ch';
                                }
                            }" x-init="$nextTick(() => syncWidth())">
                            <span aria-hidden="true">£</span>
                            <input type="number" min="0" max="999" step="1"
                                wire:model.defer="lateArrivalFeeAmount" aria-label="Late arrival fee amount"
                                x-ref="input" @input="syncWidth()" @change="syncWidth()"
                                @wheel.prevent="
                                    if (!$el.disabled) {
                                        $event.deltaY < 0 ? $el.stepUp() : $el.stepDown();
                                        $el.dispatchEvent(new Event('input', { bubbles: true }));
                                        $el.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                "
                                @disabled(!$lateArrivalFeeEnabled)>
                            <span class="service-policies-stepper">
                                <button type="button" aria-label="Increase late arrival fee"
                                    @click.prevent="
                                        const input = $el.closest('.service-policies-number-input').querySelector('input');
                                        if (input.disabled) return;
                                        input.stepUp();
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                        <path d="M10.374 5.479L5.3952 0.500185L0.499963 5.39543" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <button type="button" aria-label="Decrease late arrival fee"
                                    @click.prevent="
                                        const input = $el.closest('.service-policies-number-input').querySelector('input');
                                        if (input.disabled) return;
                                        input.stepDown();
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                        <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </span>
                        </span>

                        <em>after</em>

                        <span class="service-policies-number-input service-policies-minutes-input"
                            x-data="{
                                syncWidth() {
                                    const input = this.$refs.input;
                                    input.style.width = `${Math.min(Math.max((input.value || '0').length + 0.35, 1.35), 4)}ch`;
                                }
                            }" x-init="$nextTick(() => syncWidth())">
                            <input type="number" min="0" max="999" step="1"
                                wire:model.defer="lateArrivalFeeMinutes" aria-label="Late arrival fee minutes"
                                x-ref="input" @input="syncWidth()" @change="syncWidth()"
                                @wheel.prevent="
                                    if (!$el.disabled) {
                                        $event.deltaY < 0 ? $el.stepUp() : $el.stepDown();
                                        $el.dispatchEvent(new Event('input', { bubbles: true }));
                                        $el.dispatchEvent(new Event('change', { bubbles: true }));
                                    }
                                "
                                @disabled(!$lateArrivalFeeEnabled)>
                            <span aria-hidden="true">mins</span>
                            <span class="service-policies-stepper">
                                <button type="button" aria-label="Increase late arrival fee minutes"
                                    @click.prevent="
                                        const input = $el.closest('.service-policies-number-input').querySelector('input');
                                        if (input.disabled) return;
                                        input.stepUp();
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                        <path d="M10.374 5.479L5.3952 0.500185L0.499963 5.39543" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <button type="button" aria-label="Decrease late arrival fee minutes"
                                    @click.prevent="
                                        const input = $el.closest('.service-policies-number-input').querySelector('input');
                                        if (input.disabled) return;
                                        input.stepDown();
                                        input.dispatchEvent(new Event('input', { bubbles: true }));
                                        input.dispatchEvent(new Event('change', { bubbles: true }));
                                    ">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                        <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="service-policies-value-grid service-policies-value-grid--four"
                x-show="!isEditing('late-arrival')">
                <div>
                    <span>Grace Period</span>
                    <p>{{ blank($lateArrival['Grace Period'] ?? null) ? 'No data found' : $lateArrival['Grace Period'] }}
                    </p>
                </div>
                <div>
                    <span>Late Arrival Fee (Optional)</span>
                    <p>{{ blank($lateArrival['Late Arrival Fee (Optional)'] ?? null) ? 'No data found' : $lateArrival['Late Arrival Fee (Optional)'] }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Refund Policy</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak x-show="editingSection === 'refund'"
                    @click="saveSection('saveRefundPolicy')" wire:loading.attr="disabled"
                    wire:target="saveRefundPolicy">
                    <span wire:loading.remove wire:target="saveRefundPolicy">Save Details</span>
                    <span class="service-policies-saving" wire:loading.flex wire:target="saveRefundPolicy">
                        <span class="service-policies-spinner" aria-hidden="true"></span>
                        Saving
                    </span>
                </button>
                <button type="button" class="service-policies-edit" x-cloak x-show="editingSection !== 'refund'"
                    @click="editSection('refund')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card service-policies-document service-policies-refund-card"
            :class="{ 'service-policies-card--editing': isEditing('refund') }">
            <div x-cloak x-show="isEditing('refund')">
                <div class="service-policies-refund-editor">
                    <article class="service-policies-refund-document">
                        <h4>FursGo Refund Policy (24-Hour Window)</h4>
                        <h5>FursGo Refund Policy</h5>
                        <p>FursGo offers customers a refund window of up to 24 hours after a booking is made, provided
                            the service has not yet taken place.</p>
                        <p>If a customer cancels their booking within 24 hours of confirming the booking, they may be
                            eligible for a full refund.</p>
                        <p>After this 24-hour period, refunds may be subject to the service provider's cancellation
                            policy.</p>
                        <p>Businesses agree to honour this refund window when accepting bookings through the FursGo
                            platform.</p>
                    </article>

                    <label class="service-policies-refund-ack">
                        <input type="checkbox" wire:model.live="refundPolicy">
                        <span aria-hidden="true"></span>
                        <p>I acknowledge and agree to follow the FursGo 24-hour refund policy for bookings made through
                            the platform.</p>
                    </label>

                    <p class="service-policies-refund-note">Refunds requested within 24 hours of booking may be
                        processed automatically through FursGo.</p>

                    <div class="service-policies-refund-actions">
                        <button type="button" class="service-policies-refund-download">
                            <span style="color: #3B3731;font-weight: 400;">Download Documents</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21"
                                viewBox="0 0 18 21" fill="none" aria-hidden="true">
                                <path
                                    d="M0.75 16.583V18.1663C0.75 18.5863 0.90165 18.989 1.17159 19.2859C1.44153 19.5829 1.80764 19.7497 2.18939 19.7497H15.1439C15.5257 19.7497 15.8918 19.5829 16.1617 19.2859C16.4317 18.989 16.5833 18.5863 16.5833 18.1663V16.583"
                                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M8.66663 0.749674V13.8122M12.9848 9.45801L8.66663 14.208L4.34845 9.45801"
                                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div>
                            <button type="button" class="service-policies-refund-decline"
                                wire:click="declineRefundPolicy" wire:loading.attr="disabled"
                                wire:target="declineRefundPolicy,acceptRefundPolicy,saveRefundPolicy">Decline</button>
                            <button type="button" class="service-policies-refund-agree"
                                wire:click="acceptRefundPolicy" wire:loading.attr="disabled"
                                wire:target="declineRefundPolicy,acceptRefundPolicy,saveRefundPolicy">Agree &amp;
                                Continue</button>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="!isEditing('refund')">
                <span>FursGo Refund Policy (24-Hour Window)</span>
                <p>I acknowledge and agree to follow the FursGo 24-hour refund policy for bookings made<br>through the
                    platform.</p>
                <div class="service-policies-document-meta">
                    <span aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <path
                                d="M12 0C5.4 0 0 5.4 0 12C0 18.6 5.4 24 12 24C18.6 24 24 18.6 24 12C24 5.4 18.6 0 12 0ZM9.6 18L3.6 12L5.292 10.308L9.6 14.604L18.708 5.496L20.4 7.2L9.6 18Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                    <div>
                        <p class="service-policies-uploaded-date">Uploaded: {{ $refundPolicyUploadedDate }}</p>
                        <small>{{ $refundPolicyEnabled ? 'Status: Verified' : 'Status: Not verified' }}</small>
                    </div>
                </div>
                <button type="button" class="service-policies-download" aria-label="Download refund policy">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21"
                        fill="none">
                        <path
                            d="M0.75 16.583V18.1663C0.75 18.5863 0.90165 18.989 1.17159 19.2859C1.44153 19.5829 1.80764 19.7497 2.18939 19.7497H15.1439C15.5257 19.7497 15.8918 19.5829 16.1617 19.2859C16.4317 18.989 16.5833 18.5863 16.5833 18.1663V16.583"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.66663 0.749674V13.8122M12.9848 9.45801L8.66663 14.208L4.34845 9.45801"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Service Limitations</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak
                    x-show="editingSection === 'limitations'" @click="saveSection('saveServiceLimitations')"
                    wire:loading.attr="disabled" wire:target="saveServiceLimitations">
                    <span wire:loading.remove wire:target="saveServiceLimitations">Save Details</span>
                    <span class="service-policies-saving" wire:loading.flex wire:target="saveServiceLimitations">
                        <span class="service-policies-spinner" aria-hidden="true"></span>
                        Saving
                    </span>
                </button>
                <button type="button" class="service-policies-edit" x-cloak
                    x-show="editingSection !== 'limitations'" @click="editSection('limitations')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card" :class="{ 'service-policies-card--editing': isEditing('limitations') }">
            <div class="service-policies-limitations-editor" x-cloak x-show="isEditing('limitations')">
                <div class="service-policies-limitations-presets">
                    <span>Service Limitations</span>
                    @foreach ($serviceLimitationPresets as $limitation)
                        <label class="service-policies-limitation-option">
                            <input type="checkbox" wire:model.defer="selectedServiceLimitations"
                                value="{{ $limitation }}">
                            <span aria-hidden="true"></span>
                            <p>{{ $limitation }}</p>
                        </label>
                    @endforeach
                </div>

                <div class="service-policies-limitations-custom">
                    <span>Add custom limitations</span>
                    <div class="service-policies-limitations-add">
                        <input type="text" wire:model.defer="customServiceLimitation" placeholder="Optional"
                            maxlength="255" wire:keydown.enter.prevent="addCustomServiceLimitation">
                        <button type="button" wire:click="addCustomServiceLimitation"
                            aria-label="Add custom limitation">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5V19M5 12H19" stroke="white" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    @if ($customServiceLimitations !== [])
                        <div class="service-policies-limitations-custom-list">
                            @foreach ($customServiceLimitations as $index => $limitation)
                                <button type="button"
                                    wire:click="removeCustomServiceLimitation({{ $index }})">
                                    <span>{{ $limitation }}</span>
                                    <strong aria-hidden="true">&times;</strong>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>

                <small>This tells customers what the business does not accept.</small>
            </div>
            <div class="service-policies-list" x-show="!isEditing('limitations')">
                <span>Service Limitations</span>
                @forelse ($serviceLimitations as $limitation)
                    <p>{{ $limitation }}</p>
                @empty
                    <p>No data found</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Animal Welfare Statement</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-edit" x-cloak
                    x-show="editingSection !== 'animal-welfare'" @click="editSection('animal-welfare')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card service-policies-document service-policies-animal-welfare-card"
            :class="{ 'service-policies-card--editing': isEditing('animal-welfare') }">
            <div x-cloak x-show="isEditing('animal-welfare')">
                <div class="service-policies-refund-editor">
                    <article class="service-policies-refund-document service-policies-animal-welfare-document">
                        <h4>Animal Welfare Statement</h4>
                        <h5>Animal Welfare Commitment</h5>
                        <p>As a service provider on FursGo, I confirm that I will handle all animals with care,
                            patience,
                            and respect for their wellbeing.</p>
                        <p>I agree to:</p>
                        <p>&bull; Treat all pets humanely and avoid causing unnecessary stress, injury, or
                            discomfort.<br>
                            &bull; Follow safe and gentle grooming or handling practices appropriate to each animal's
                            breed, age, and condition.<br>
                            &bull; Ensure all equipment and grooming environments are clean, safe, and suitable for
                            animal
                            care.<br>
                            &bull; Monitor animals for signs of distress, illness, or injury and stop services if a
                            pet's
                            welfare may be at risk.<br>
                            &bull; Inform the pet owner immediately if any health or welfare concerns arise.</p>
                    </article>

                    <label class="service-policies-refund-ack service-policies-animal-welfare-ack">
                        <input type="checkbox" wire:model.live="animalWelfareStatement">
                        <span aria-hidden="true"></span>
                        <p>I confirm that I will follow these animal welfare standards when providing services through
                            FursGo.</p>
                    </label>

                    <div class="service-policies-refund-actions">
                        <button type="button" class="service-policies-refund-download">
                            <span style="color: #3B3731;font-weight: 400;">Download Documents</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21"
                                viewBox="0 0 18 21" fill="none" aria-hidden="true">
                                <path
                                    d="M0.75 16.583V18.1663C0.75 18.5863 0.90165 18.989 1.17159 19.2859C1.44153 19.5829 1.80764 19.7497 2.18939 19.7497H15.1439C15.5257 19.7497 15.8918 19.5829 16.1617 19.2859C16.4317 18.989 16.5833 18.5863 16.5833 18.1663V16.583"
                                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M8.66663 0.749674V13.8122M12.9848 9.45801L8.66663 14.208L4.34845 9.45801"
                                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div>
                            <button type="button" class="service-policies-refund-decline"
                                wire:click="declineAnimalWelfareStatement" wire:loading.attr="disabled"
                                wire:target="declineAnimalWelfareStatement,acceptAnimalWelfareStatement,saveAnimalWelfareStatement">Decline</button>
                            <button type="button" class="service-policies-refund-agree"
                                wire:click="acceptAnimalWelfareStatement" wire:loading.attr="disabled"
                                wire:target="declineAnimalWelfareStatement,acceptAnimalWelfareStatement,saveAnimalWelfareStatement">Agree
                                &amp; Continue</button>
                        </div>
                    </div>
                </div>
            </div>
            <div x-show="!isEditing('animal-welfare')">
                <span>Animal Welfare Commitment</span>
                <p>I confirm that I will follow these animal welfare standards when providing services through FursGo.
                </p>
                <div class="service-policies-document-meta">
                    <span aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <path
                                d="M12 0C5.4 0 0 5.4 0 12C0 18.6 5.4 24 12 24C18.6 24 24 18.6 24 12C24 5.4 18.6 0 12 0ZM9.6 18L3.6 12L5.292 10.308L9.6 14.604L18.708 5.496L20.4 7.2L9.6 18Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                    <div>
                        <p class="service-policies-uploaded-date">Uploaded: {{ $animalWelfareUploadedDate }}</p>
                        <small>{{ $animalWelfareEnabled ? 'Status: Verified' : 'Status: Not verified' }}</small>
                    </div>
                </div>
                <button type="button" class="service-policies-download"
                    aria-label="Download animal welfare statement">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21"
                        fill="none">
                        <path
                            d="M0.75 16.583V18.1663C0.75 18.5863 0.90165 18.989 1.17159 19.2859C1.44153 19.5829 1.80764 19.7497 2.18939 19.7497H15.1439C15.5257 19.7497 15.8918 19.5829 16.1617 19.2859C16.4317 18.989 16.5833 18.5863 16.5833 18.1663V16.583"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.66663 0.749674V13.8122M12.9848 9.45801L8.66663 14.208L4.34845 9.45801"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Hygiene &amp; Safety Standards</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak
                    x-show="editingSection === 'hygiene-safety'" @click="saveSection('saveHygieneSafetyStandards')"
                    wire:loading.attr="disabled" wire:target="saveHygieneSafetyStandards">
                    <span wire:loading.remove wire:target="saveHygieneSafetyStandards">Save Details</span>
                    <span class="service-policies-saving" wire:loading.flex wire:target="saveHygieneSafetyStandards">
                        <span class="service-policies-spinner" aria-hidden="true"></span>
                        Saving
                    </span>
                </button>
                <button type="button" class="service-policies-edit" x-cloak
                    x-show="editingSection !== 'hygiene-safety'" @click="editSection('hygiene-safety')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card" :class="{ 'service-policies-card--editing': isEditing('hygiene-safety') }">
            <div class="service-policies-limitations-editor" x-cloak x-show="isEditing('hygiene-safety')">
                <div class="service-policies-limitations-presets">
                    <span>Hygiene &amp; Safety Standards</span>
                    @foreach ($hygieneSafetyPresets as $standard)
                        <label class="service-policies-limitation-option">
                            <input type="checkbox" wire:model.defer="selectedHygieneSafetyStandards"
                                value="{{ $standard }}">
                            <span aria-hidden="true"></span>
                            <p>{{ $standard }}</p>
                        </label>
                    @endforeach
                </div>

                <div class="service-policies-limitations-custom">
                    <span>Add custom Hygiene &amp; Safety Standards</span>
                    <div class="service-policies-limitations-add">
                        <input type="text" wire:model.defer="customHygieneSafetyStandard" placeholder="Optional"
                            maxlength="255" wire:keydown.enter.prevent="addCustomHygieneSafetyStandard">
                        <button type="button" wire:click="addCustomHygieneSafetyStandard"
                            aria-label="Add custom hygiene and safety standard">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                viewBox="0 0 24 24" fill="none" aria-hidden="true">
                                <path d="M12 5V19M5 12H19" stroke="white" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                        </button>
                    </div>
                    @if ($customHygieneSafetyStandards !== [])
                        <div class="service-policies-limitations-custom-list">
                            @foreach ($customHygieneSafetyStandards as $index => $standard)
                                <button type="button"
                                    wire:click="removeCustomHygieneSafetyStandard({{ $index }})">
                                    <span>{{ $standard }}</span>
                                    <strong aria-hidden="true">&times;</strong>
                                </button>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
            <div class="service-policies-list" x-show="!isEditing('hygiene-safety')">
                <span>Hygiene &amp; Safety Standards</span>
                @forelse ($hygieneSafetyStandards as $standard)
                    <p>{{ $standard }}</p>
                @empty
                    <p>No data found</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Compliance Declaration</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-edit" x-cloak x-show="editingSection !== 'compliance'"
                    @click="editSection('compliance')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card service-policies-document service-policies-compliance-card"
            :class="{ 'service-policies-card--editing': isEditing('compliance') }">
            <div x-cloak x-show="isEditing('compliance')">
                <div class="service-policies-refund-editor">
                    <article class="service-policies-refund-document service-policies-compliance-document">
                        <h4>FursGo Compliance Declaration</h4>
                        <h5>Compliance Declaration</h5>
                        <p>By operating on the FursGo platform, I confirm that my business complies with all relevant
                            local laws, regulations, and professional standards applicable to pet care services.</p>
                        <p>This includes ensuring that:</p>
                        <p>&bull; My services are provided in a safe and responsible environment.<br>
                            &bull; I follow appropriate animal welfare standards.<br>
                            &bull; Any required licences, permits, or insurance relevant to my services are obtained and
                            maintained where applicable.<br>
                            &bull; My business information and services listed on FursGo are accurate and up to date.
                        </p>
                    </article>

                    <label class="service-policies-refund-ack service-policies-compliance-ack">
                        <input type="checkbox" wire:model.live="complianceDeclaration">
                        <span aria-hidden="true"></span>
                        <p>I confirm that my business complies with applicable laws and regulations and will operate
                            responsibly on the FursGo platform.</p>
                    </label>

                    <div class="service-policies-refund-actions">
                        <button type="button" class="service-policies-refund-download">
                            <span style="color: #3B3731;font-weight: 400;">Download Documents</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21"
                                viewBox="0 0 18 21" fill="none" aria-hidden="true">
                                <path
                                    d="M0.75 16.583V18.1663C0.75 18.5863 0.90165 18.989 1.17159 19.2859C1.44153 19.5829 1.80764 19.7497 2.18939 19.7497H15.1439C15.5257 19.7497 15.8918 19.5829 16.1617 19.2859C16.4317 18.989 16.5833 18.5863 16.5833 18.1663V16.583"
                                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                                <path d="M8.66663 0.749674V13.8122M12.9848 9.45801L8.66663 14.208L4.34845 9.45801"
                                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                    stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div>
                            <button type="button" class="service-policies-refund-decline"
                                wire:click="declineComplianceDeclaration" wire:loading.attr="disabled"
                                wire:target="declineComplianceDeclaration,acceptComplianceDeclaration,saveComplianceDeclaration">Decline</button>
                            <button type="button" class="service-policies-refund-agree"
                                wire:click="acceptComplianceDeclaration" wire:loading.attr="disabled"
                                wire:target="declineComplianceDeclaration,acceptComplianceDeclaration,saveComplianceDeclaration">Agree
                                &amp; Continue</button>
                        </div>
                    </div>

                    <div class="service-policies-compliance-publish">
                        <label class="service-policies-refund-ack service-policies-compliance-publish-ack">
                            <input type="checkbox" wire:model.live="complianceDeclaration">
                            <span aria-hidden="true"></span>
                            <p>I confirm these policies comply with local regulations and will be applied to all
                                bookings.</p>
                        </label>

                        <button type="button" class="service-policies-save service-policies-save--publish"
                            @click="saveSection('saveComplianceDeclaration')" wire:loading.attr="disabled"
                            wire:target="saveComplianceDeclaration">
                            <span wire:loading.remove wire:target="saveComplianceDeclaration">Save &amp; Publish</span>
                            <span class="service-policies-saving" wire:loading.flex
                                wire:target="saveComplianceDeclaration">
                                <span class="service-policies-spinner" aria-hidden="true"></span>
                                Saving
                            </span>
                        </button>
                    </div>
                </div>
            </div>
            <div x-show="!isEditing('compliance')">
                <span>FursGo Compliance Declaration</span>
                <p>I confirm that my business complies with applicable laws and regulations and will operate responsibly
                    on the FursGo platform.</p>
                <div class="service-policies-document-meta">
                    <span aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none">
                            <path
                                d="M12 0C5.4 0 0 5.4 0 12C0 18.6 5.4 24 12 24C18.6 24 24 18.6 24 12C24 5.4 18.6 0 12 0ZM9.6 18L3.6 12L5.292 10.308L9.6 14.604L18.708 5.496L20.4 7.2L9.6 18Z"
                                fill="#C9DDA0" />
                        </svg>
                    </span>
                    <div>
                        <p class="service-policies-uploaded-date">Uploaded: {{ $complianceDeclarationUploadedDate }}
                        </p>
                        <small>{{ $complianceDeclarationEnabled ? 'Status: Verified' : 'Status: Not verified' }}</small>
                    </div>
                </div>
                <button type="button" class="service-policies-download"
                    aria-label="Download compliance declaration">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="21" viewBox="0 0 18 21"
                        fill="none">
                        <path
                            d="M0.75 16.583V18.1663C0.75 18.5863 0.90165 18.989 1.17159 19.2859C1.44153 19.5829 1.80764 19.7497 2.18939 19.7497H15.1439C15.5257 19.7497 15.8918 19.5829 16.1617 19.2859C16.4317 18.989 16.5833 18.5863 16.5833 18.1663V16.583"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.66663 0.749674V13.8122M12.9848 9.45801L8.66663 14.208L4.34845 9.45801"
                            stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </section>

    <section class="service-policies-block service-policies-timeline-section" x-data="{ timelineOpen: true }">
        <button type="button"
            class="service-policies-section-title service-policies-section-title--compact service-policies-timeline-title"
            @click="timelineOpen = !timelineOpen" :aria-expanded="timelineOpen.toString()"
            aria-controls="service-policies-compliance-timeline">
            <h3>Compliance Timeline</h3>
            <span class="service-policies-timeline-toggle" :class="{ 'is-collapsed': !timelineOpen }">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8"
                    fill="none" aria-hidden="true">
                    <path d="M13.8737 7.24316L7.13022 0.499723L0.499976 7.12996" stroke="#3B3731"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </span>
        </button>
        <div id="service-policies-compliance-timeline" class="service-policies-timeline-collapse"
            :class="{ 'is-open': timelineOpen }">
            <div class="service-policies-timeline-collapse__inner">
                <div class="service-policies-card service-policies-timeline">
                    @forelse ($verifyDates as $date)
                        <p><span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                    height="24" viewBox="0 0 24 24" fill="none">
                                    <path
                                        d="M12 0C5.4 0 0 5.4 0 12C0 18.6 5.4 24 12 24C18.6 24 24 18.6 24 12C24 5.4 18.6 0 12 0ZM9.6 18L3.6 12L5.292 10.308L9.6 14.604L18.708 5.496L20.4 7.2L9.6 18Z"
                                        fill="#C9DDA0" />
                                </svg></span>{{ $date }} - Business verified</p>
                    @empty
                        <p>No data found</p>
                    @endforelse
                </div>
            </div>
        </div>
    </section>

    <style>
        .service-policies-settings {
            width: 100%;
            padding-top: 1.5rem;
            color: #3B3731;
            font-family: Lato, sans-serif;
        }

        .service-policies-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1.5rem;
            border-bottom: 1px solid #D4D4D4;
            padding-bottom: 1rem;
        }

        .service-policies-heading h2 {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 28px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-status {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: #9D9B98;
            font-size: 14px;
            font-weight: 600;
            white-space: nowrap;
        }

        .service-policies-status svg {
            display: block;
            width: 19px;
            height: 19px;
            flex: 0 0 19px;
            overflow: visible;
        }


        .service-policies-alert {
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
            font-size: 16px;
            line-height: normal;
        }

        .service-policies-alert-enter,
        .service-policies-alert-leave {
            overflow: hidden;
            transition: opacity 0.22s ease, transform 0.22s ease, max-height 0.22s ease, margin 0.22s ease,
                padding 0.22s ease;
        }

        .service-policies-alert-enter-start,
        .service-policies-alert-leave-end {
            opacity: 0;
            max-height: 0;
            margin-bottom: 0;
            padding-top: 0;
            padding-bottom: 0;
            transform: translateY(-8px);
        }

        .service-policies-alert-enter-end,
        .service-policies-alert-leave-start {
            opacity: 1;
            max-height: 6rem;
            transform: translateY(0);
        }

        .service-policies-alert .service-policies-alert__icon {
            width: 24px;
            height: 24px;
            aspect-ratio: 1/1;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 2px;
            background: #B4CCDD;
            transform: rotate(45deg);
            flex: 0 0 auto;
            padding: 4px;
        }

        .service-policies-alert__icon svg {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            transform: rotate(-45deg);
        }

        .service-policies-alert strong,
        .service-policies-alert span {
            display: block;
            color: #8BAFC8;
            font-family: Lato;
            font-style: normal;
            line-height: normal;
        }

        .service-policies-alert strong {
            font-size: 18px;
            font-weight: 600;
        }

        .service-policies-alert span {
            font-size: 16px;
            font-weight: 400;
        }

        .service-policies-alert__close {
            margin-left: auto;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .service-policies-block {
            margin-bottom: 2.1rem;
        }

        .service-policies-section-title {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 0.75rem;
            margin: 5rem 0 1rem;
        }

        .service-policies-section-title h3 {
            width: 85%;
            min-width: 10rem;
            margin: 0;
            padding-bottom: 1.5rem;
            border-bottom: 1px solid #D4D4D4;
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-title-actions {
            position: relative;
            width: 143px;
            height: 48px;
            flex: 0 0 143px;
        }

        .service-policies-edit,
        .service-policies-save {
            position: absolute;
            inset: 0;
            width: 143px;
            height: 48px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            border-radius: 100px;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
        }

        .service-policies-edit {
            border: 1px solid #E2E2E2;
            background: transparent;
            color: #3B3731;
        }

        .service-policies-save {
            border: 0;
            background: #BACF8E;
            color: #FFFFFF;
        }

        .service-policies-save:disabled {
            cursor: wait;
            opacity: 0.75;
        }

        .service-policies-saving {
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
        }

        .service-policies-spinner {
            width: 16px;
            height: 16px;
            border: 2px solid rgba(255, 255, 255, 0.45);
            border-top-color: #FFFFFF;
            border-radius: 50%;
            animation: service-policies-spin 800ms linear infinite;
        }

        @keyframes service-policies-spin {
            to {
                transform: rotate(360deg);
            }
        }

        .service-policies-card {
            border-radius: 8px;
            background: rgba(232, 232, 232, 0.20);
            padding: 1.2rem;
        }

        .service-policies-card.service-policies-card--editing {
            border: 1px solid #ECECEC;
            border-radius: 10px;
            background: #FFFFFF;
            padding: 1.55rem 1.35rem 1.45rem;
        }

        .service-policies-value-grid,
        .service-policies-edit-grid {
            display: grid;
            gap: 1rem;
        }

        .service-policies-value-grid--two,
        .service-policies-edit-grid--two {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .service-policies-value-grid--three,
        .service-policies-edit-grid--three {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .service-policies-value-grid--four,
        .service-policies-edit-grid--four {
            grid-template-columns: repeat(4, minmax(0, 1fr));
        }

        .service-policies-value-grid span,
        .service-policies-list span,
        .service-policies-document span,
        .service-policies-textarea span,
        .service-policies-edit-grid label span {
            display: block;
            margin-bottom: 1rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-value-grid p,
        .service-policies-list p,
        .service-policies-document p,
        .service-policies-timeline p {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-policies-list p+p {
            margin-top: 0.15rem;
        }

        .service-policies-limitations-editor {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 0.6fr);
            column-gap: 4.5rem;
            row-gap: 1.35rem;
            align-items: start;
        }

        .service-policies-limitations-presets>span,
        .service-policies-limitations-custom>span {
            display: block;
            margin-bottom: 1rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-limitation-option {
            display: flex;
            align-items: center;
            gap: 1.05rem;
            width: fit-content;
            margin-bottom: 1.05rem;
            cursor: pointer;
        }

        .service-policies-limitation-option input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .service-policies-limitation-option>span {
            width: 17px;
            height: 17px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 17px;
            margin: 0;
            border: 1px solid #FFC56D;
            border-radius: 50%;
            background: #FFFFFF;
            box-sizing: border-box;
        }

        .service-policies-limitation-option input:checked+span::after {
            content: '';
            width: 11px;
            height: 11px;
            border-radius: 50%;
            background: #FFD485;
        }

        .service-policies-limitation-option p {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-policies-limitations-add {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .service-policies-limitations-add input {
            width: min(100%, 290px);
            height: 43px;
            border: 1px solid #E0E0E0;
            border-radius: 10px;
            background: #FFFFFF;
            padding: 0 1rem;
            box-sizing: border-box;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            outline: none;
        }

        .service-policies-limitations-add input::placeholder {
            color: #9D9B98;
        }

        .service-policies-limitations-add button {
            width: 42px;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 42px;
            border: 0;
            border-radius: 50%;
            background: #FFC56D;
            box-shadow: 0 6px 14px rgba(59, 55, 49, 0.16);
            cursor: pointer;
        }

        .service-policies-limitations-custom-list {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-top: 0.8rem;
        }

        .service-policies-limitations-custom-list button {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            border: 1px solid #E8E8E8;
            border-radius: 999px;
            background: #FAFAFA;
            padding: 0.35rem 0.65rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 13px;
            cursor: pointer;
        }

        .service-policies-limitations-custom-list strong {
            color: #9D9B98;
            font-size: 16px;
            font-weight: 400;
            line-height: 1;
        }

        .service-policies-limitations-editor>small {
            grid-column: 1 / -1;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-policies-edit-grid label,
        .service-policies-textarea {
            display: flex;
            flex-direction: column;
            gap: 0.55rem;
            width: 100%;
        }

        .service-policies-edit-grid input,
        .service-policies-textarea textarea {
            width: 100%;
            border: 1px solid #E4E1DD;
            border-radius: 8px;
            background: #FFFFFF;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            line-height: 25px;
            padding: 0.8rem 0.9rem;
            outline: none;
            box-sizing: border-box;
        }

        .service-policies-cancellation-editor {
            display: flex;
            flex-direction: column;
            gap: 2rem;
            max-width: 920px;
        }

        .service-policies-window-field,
        .service-policies-fee-group {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 0.65rem;
        }

        .service-policies-window-field>span,
        .service-policies-fee-group>span {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-window-field select {
            width: 290px;
            height: 54px;
            appearance: none;
            border: 1px solid #E9E9E9;
            border-radius: 10px;
            background-color: #FFFFFF;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='11' height='6' viewBox='0 0 11 6' fill='none'%3E%3Cpath d='M10.374 0.5L5.3952 5.47882L0.499963 0.583578' stroke='%233B3731' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
            background-position: right 1.2rem center;
            background-repeat: no-repeat;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
            padding: 0 2.8rem 0 1.3rem;
            outline: none;
        }

        .service-policies-settings .service-custom-select {
            position: relative;
            width: 290px;
        }

        .service-policies-settings .service-custom-select.is-open {
            z-index: 50;
        }

        .service-policies-settings .service-custom-trigger {
            width: 290px;
            height: 54px;
            border-radius: 10px;
            border: 1px solid #E9E9E9;
            background: #FFFFFF;
            color: #3B3731;
            text-align: left;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.2rem 0 1.3rem;
        }

        .service-policies-settings .service-custom-select.is-open .service-custom-trigger {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-color: #DDD;
        }

        .service-policies-settings .service-custom-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #F8F8F8;
            border: 1px solid #DDD;
            border-top: none;
            border-radius: 0 0 10px 10px;
            z-index: 60;
            overflow: hidden;
        }

        .service-policies-settings .service-custom-menu-enter {
            transition: opacity 180ms ease, transform 180ms ease;
            transform-origin: top;
        }

        .service-policies-settings .service-custom-menu-enter-start {
            opacity: 0;
            transform: scaleY(0.95);
        }

        .service-policies-settings .service-custom-menu-enter-end {
            opacity: 1;
            transform: scaleY(1);
        }

        .service-policies-settings .service-custom-menu-leave {
            transition: opacity 140ms ease, transform 140ms ease;
            transform-origin: top;
        }

        .service-policies-settings .service-custom-menu-leave-start {
            opacity: 1;
            transform: scaleY(1);
        }

        .service-policies-settings .service-custom-menu-leave-end {
            opacity: 0;
            transform: scaleY(0.95);
        }

        .service-policies-settings .service-custom-option {
            width: 100%;
            border: 0;
            border-bottom: 2px solid #E6E6E5;
            background: #FFFFFF;
            padding: 0.9rem 1rem;
            text-align: left;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .service-policies-settings .service-custom-option:last-child {
            border-bottom: none;
        }

        .service-policies-settings .service-custom-option:hover {
            background: #F2F2F2;
        }

        .service-policies-settings .service-custom-option.is-active {
            background: rgba(216, 232, 183, 0.20);
            color: #A4C560;
        }

        .service-policies-window-field small,
        .service-policies-fee-group small {
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-policies-late-editor {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
            max-width: 920px;
        }

        .service-policies-fee-group>span em {
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-fee-row {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .service-policies-fee-row--late {
            gap: 0.9rem;
        }

        .service-policies-switch-check {
            position: relative;
            width: 46px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            flex: 0 0 46px;
            border-radius: 999px;
            background: #DCEABF;
            cursor: pointer;
            transition: background-color 160ms ease;
        }

        .service-policies-switch-check:has(input:not(:checked)) {
            background: #E2E2E2;
        }

        .service-policies-switch-check input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            opacity: 0;
            cursor: pointer;
        }

        .service-policies-switch-check span {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: 25px;
            border-radius: 50%;
            background: #FFFFFF;
            transition: margin 160ms ease, background-color 160ms ease;
        }

        .service-policies-switch-check:has(input:not(:checked)) span {
            background: #F7F7F7;
        }

        .service-policies-switch-check span svg {
            display: block;
            width: 11px;
            height: 9px;
        }

        .service-policies-switch-check span path {
            stroke: #B8D58B;
        }

        .service-policies-switch-check input:not(:checked)+span {
            margin-left: 3px;
        }

        .service-policies-switch-check input:not(:checked)+span svg {
            opacity: 0;
        }

        .service-policies-fee-dot {
            position: relative;
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 22px;
            cursor: pointer;
        }

        .service-policies-fee-dot input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            margin: 0;
            opacity: 0;
            cursor: pointer;
        }

        .service-policies-fee-dot span {
            width: 22px;
            height: 22px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #FFD485;
            border-radius: 50%;
            background: #FFFFFF;
            box-sizing: border-box;
            transition: border-color 160ms ease, background-color 160ms ease;
        }

        .service-policies-fee-dot span::after {
            content: '';
            width: 14px;
            height: 14px;
            border-radius: 50%;
            background: #FFD485;
            opacity: 0;
            transition: opacity 160ms ease;
        }

        .service-policies-fee-dot input:checked+span::after {
            opacity: 1;
        }

        .service-policies-fee-dot input:focus-visible+span {
            outline: 2px solid rgba(255, 212, 133, 0.45);
            outline-offset: 2px;
        }

        .service-policies-fee-row strong {
            min-width: 150px;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-percent-input {
            position: relative;
            display: inline-flex;
            align-items: center;
            width: 95px;
            height: 48px;
            border: 1px solid #D4D4D4;
            border-radius: 12px;
            background: #FFFFFF;
            box-sizing: border-box;
            padding: 0 2rem 0 0.65rem;
        }

        .service-policies-percent-input--shake {
            animation: service-policies-input-shake 360ms ease-in-out;
        }

        @keyframes service-policies-input-shake {

            0%,
            100% {
                transform: translateX(0);
            }

            20% {
                transform: translateX(-4px);
            }

            40% {
                transform: translateX(4px);
            }

            60% {
                transform: translateX(-3px);
            }

            80% {
                transform: translateX(3px);
            }
        }

        .service-policies-percent-input input {
            width: 2ch;
            min-width: 1.35ch;
            max-width: 4ch;
            height: 100%;
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato;
            font-size: 17px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
            padding: 0;
            outline: none;
            box-sizing: border-box;
        }

        .service-policies-percent-symbol {
            color: #3B3731;
            font-family: Lato;
            font-size: 17px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            pointer-events: none;
        }

        .service-policies-percent-input input::-webkit-outer-spin-button,
        .service-policies-percent-input input::-webkit-inner-spin-button {
            appearance: none;
            margin: 0;
        }

        .service-policies-percent-input input[type='number'] {
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .service-policies-percent-input:has(input:disabled) {
            cursor: not-allowed;
            opacity: 0.65;
        }

        .service-policies-percent-input:has(input:disabled) input,
        .service-policies-percent-input:has(input:disabled) .service-policies-percent-symbol,
        .service-policies-percent-input:has(input:disabled) .service-policies-stepper button {
            cursor: not-allowed;
        }

        .service-policies-stepper {
            position: absolute;
            top: 50%;
            right: 0.75rem;
            display: inline-flex;
            flex-direction: column;
            gap: 0.55rem;
            transform: translateY(-50%);
        }

        .service-policies-stepper button {
            width: 12px;
            height: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border: 0;
            background: transparent;
            color: #3B3731;
            cursor: pointer;
        }

        .service-policies-stepper svg {
            display: block;
            width: 11px;
            height: 6px;
        }

        .service-policies-fee-row em {
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-policies-number-input {
            position: relative;
            height: 44px;
            display: inline-flex;
            align-items: center;
            border: 1px solid #D8D8D8;
            border-radius: 8px;
            background: #FFFFFF;
            box-sizing: border-box;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
        }

        .service-policies-money-input {
            width: 96px;
            padding: 0 2rem 0 0.85rem;
        }

        .service-policies-minutes-input {
            width: 116px;
            padding: 0 2rem 0 0.85rem;
            gap: 0;
        }

        .service-policies-number-input>span:not(.service-policies-stepper) {
            display: inline;
            margin: 0;
            color: #3B3731;
            font: inherit;
        }

        .service-policies-number-input input {
            width: 4ch;
            min-width: 4ch;
            max-width: 4ch;
            height: 100%;
            border: 0;
            background: transparent;
            color: #3B3731;
            font: inherit;
            padding: 0;
            outline: none;
            box-sizing: border-box;
        }

        .service-policies-minutes-input input {
            width: 2ch;
            min-width: 1.35ch;
            max-width: 4ch;
        }

        .service-policies-number-input input::-webkit-outer-spin-button,
        .service-policies-number-input input::-webkit-inner-spin-button {
            appearance: none;
            margin: 0;
        }

        .service-policies-number-input input[type='number'] {
            appearance: textfield;
            -moz-appearance: textfield;
        }

        .service-policies-number-input:has(input:disabled) {
            cursor: not-allowed;
            border-color: #E2E2E2;
            background: #F5F5F5;
            color: #9D9B98;
            opacity: 1;
        }

        .service-policies-number-input:has(input:disabled) input,
        .service-policies-number-input:has(input:disabled)>span:not(.service-policies-stepper),
        .service-policies-number-input:has(input:disabled) .service-policies-stepper button {
            color: #9D9B98;
            cursor: not-allowed;
        }

        .service-policies-textarea textarea {
            min-height: 7rem;
            resize: vertical;
        }

        .service-policies-check {
            display: inline-flex;
            align-items: center;
            gap: 0.7rem;
            color: #3B3731;
            font-size: 15px;
            font-weight: 600;
        }

        .service-policies-check input {
            width: 18px;
            height: 18px;
            accent-color: #C9DDA0;
        }

        .service-policies-document {
            position: relative;
            padding-right: 4.5rem;
        }

        .service-policies-document.service-policies-card--editing {
            padding-right: 1.35rem;
        }

        .service-policies-refund-card.service-policies-card--editing {
            border: 0;
        }

        .service-policies-animal-welfare-card.service-policies-card--editing {
            border: 0;
            background: transparent;
            padding: 1.2rem 0;
        }

        .service-policies-compliance-card.service-policies-card--editing {
            border: 0;
            background: transparent;
            padding: 1.2rem 0;
        }

        .service-policies-refund-editor {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 1.75rem;
        }

        .service-policies-refund-editor span {
            display: inline-flex;
            margin: 0;
        }

        .service-policies-refund-document {
            width: min(100%, 640px);
            max-height: 380px;
            overflow-y: scroll;
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            background: #FAFAFA;
            padding: 3rem 2.2rem;
            box-sizing: border-box;
            color: #3B3731;
            scrollbar-color: #E3E3E3 transparent;
            scrollbar-width: thin;
        }

        .service-policies-refund-document::-webkit-scrollbar {
            width: 8px;
        }

        .service-policies-refund-document::-webkit-scrollbar-button {
            display: none;
            width: 0;
            height: 0;
        }

        .service-policies-refund-document::-webkit-scrollbar-button:single-button,
        .service-policies-refund-document::-webkit-scrollbar-button:decrement,
        .service-policies-refund-document::-webkit-scrollbar-button:increment {
            display: none;
            width: 0;
            height: 0;
        }

        .service-policies-refund-document::-webkit-scrollbar-track {
            background: transparent;
        }

        .service-policies-refund-document::-webkit-scrollbar-thumb {
            min-height: 79.959px;
            border-radius: 96px;
            background: #E3E3E3;
        }

        .service-policies-refund-document h4 {
            margin: 0 0 2.2rem;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-refund-document h5 {
            margin: 0 0 1.3rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #D9D9D9;
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-refund-document p {
            margin: 0 0 1.25rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 1.28;
        }

        .service-policies-refund-document p:last-child {
            margin-bottom: 0;
        }

        .service-policies-animal-welfare-document {
            width: min(100%, 624px);
            max-height: 445px;
        }

        .service-policies-compliance-document {
            width: min(100%, 624px);
            max-height: 408px;
        }

        .service-policies-refund-ack {
            width: min(100%, 600px);
            display: grid;
            grid-template-columns: 22px 1fr;
            gap: 1.1rem;
            align-items: flex-start;
            color: #3B3731;
            cursor: pointer;
        }

        .service-policies-refund-ack input {
            position: absolute;
            opacity: 0;
            pointer-events: none;
        }

        .service-policies-refund-ack>span {
            width: 18px;
            height: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-top: 0.2rem;
            border: 2px solid #FFD485;
            border-radius: 50%;
            background: #FFFFFF;
            box-sizing: border-box;
        }

        .service-policies-refund-ack>span::after {
            content: '';
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #FFD485;
            opacity: 0;
        }

        .service-policies-refund-ack input:checked+span::after {
            opacity: 1;
        }

        .service-policies-refund-ack p {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: 1.35;
        }

        .service-policies-refund-note {
            width: min(100%, 640px);
            margin: -0.65rem 0 0 !important;
            color: #9D9B98 !important;
            font-family: Lato;
            font-size: 14px !important;
            font-style: normal;
            font-weight: 400 !important;
            line-height: normal !important;
        }

        .service-policies-refund-actions {
            width: min(100%, 640px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .service-policies-refund-actions>div {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .service-policies-refund-download,
        .service-policies-refund-decline,
        .service-policies-refund-agree {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 96px;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);

            cursor: pointer;
        }

        .service-policies-refund-download {
            width: 281px;
            height: 48px;
            gap: 1.25rem;
            border: 1px solid #9D9B98;
            background: #FFFFFF;
            color: #3B3731;
        }

        .service-policies-refund-decline {
            width: 94px;
            height: 48px;
            border: 1px solid #9D9B98;
            background: transparent;
            color: #3B3731;
        }

        .service-policies-refund-agree {
            width: 167px;
            height: 48px;
            border: 0;
            background: #FFC97A;
            color: #FFFFFF;
        }

        .service-policies-refund-decline:disabled,
        .service-policies-refund-agree:disabled {
            cursor: wait;
            opacity: 0.75;
        }

        .service-policies-compliance-publish {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            margin-top: 3.25rem;
        }

        .service-policies-compliance-publish-ack {
            width: auto;
            flex: 1 1 auto;
            align-items: center;
        }

        .service-policies-compliance-publish-ack p {
            font-weight: 600 !important;
        }

        .service-policies-save--publish {
            position: static;
            inset: auto;
            width: 160px;
            height: 42px;
            flex: 0 0 140px;
            margin-left: auto;
        }

        .service-policies-save--publish>span {
            color: #FFF;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-policies-download {
            position: absolute;
            top: 50%;
            right: 1.9rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
            padding: 0;
            border: 0;
            background: transparent;
            color: #3B3731;
            cursor: pointer;
            transform: translateY(-50%);
        }

        .service-policies-download svg {
            display: block;
            width: 21px;
            height: 21px;
        }

        .service-policies-document-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .service-policies-uploaded-date {
            margin: 0 !important;
            color: #9D9B98 !important;
            font-family: Lato;
            font-size: 16px !important;
            font-style: normal;
            font-weight: 400 !important;
            line-height: normal !important;
        }

        .service-policies-document small {
            display: block;
            margin-top: 0;
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .service-policies-timeline {
            background: transparent;
            padding: 0;
        }

        .service-policies-timeline-section .service-policies-section-title {
            width: 100%;
            border-bottom: 1px solid #D4D4D4;
            background: transparent;
            padding-bottom: 1.5rem;
            border-top: 0;
            border-right: 0;
            border-left: 0;
            text-align: left;
            cursor: pointer;
        }

        .service-policies-timeline-section .service-policies-section-title h3 {
            width: auto;
            flex: 1 1 auto;
            padding-bottom: 0;
            border-bottom: 0;
        }

        .service-policies-timeline-toggle {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-left: auto;
            padding: 0;
            flex: 0 0 32px;
        }

        .service-policies-timeline-toggle svg {
            transition: transform 220ms ease;
        }

        .service-policies-timeline-toggle.is-collapsed svg {
            transform: rotate(180deg);
        }

        .service-policies-timeline-collapse {
            display: grid;
            grid-template-rows: 0fr;
            opacity: 0;
            transition: grid-template-rows 240ms ease, opacity 180ms ease;
        }

        .service-policies-timeline-collapse.is-open {
            grid-template-rows: 1fr;
            opacity: 1;
        }

        .service-policies-timeline-collapse__inner {
            overflow: hidden;
        }

        .service-policies-timeline p {
            display: flex;
            align-items: center;
            gap: 0.7rem;
            margin-bottom: 1.5rem;
        }

        .service-policies-document-meta>span,
        .service-policies-timeline p span {
            width: 24px;
            height: 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex: 0 0 24px;
            margin: 0;
        }

        .service-policies-document-meta>span svg,
        .service-policies-timeline p span svg {
            display: block;
            width: 24px;
            height: 24px;
        }

        @media (max-width: 900px) {

            .service-policies-value-grid--two,
            .service-policies-value-grid--three,
            .service-policies-value-grid--four,
            .service-policies-edit-grid--two,
            .service-policies-edit-grid--three,
            .service-policies-edit-grid--four {
                grid-template-columns: 1fr;
            }

            .service-policies-limitations-editor {
                grid-template-columns: 1fr;
                column-gap: 0;
            }
        }

        @media (max-width: 640px) {

            .service-policies-heading,
            .service-policies-section-title {
                align-items: flex-start;
                flex-direction: column;
            }

            .service-policies-section-title h3 {
                width: 100%;
            }

            .service-policies-window-field select {
                width: 100%;
            }

            .service-policies-settings .service-custom-select,
            .service-policies-settings .service-custom-trigger {
                width: 100%;
            }

            .service-policies-fee-row {
                align-items: flex-start;
                flex-wrap: wrap;
                gap: 0.7rem;
            }

            .service-policies-fee-row strong {
                min-width: calc(100% - 2rem);
            }

            .service-policies-fee-row--late {
                align-items: center;
            }

            .service-policies-refund-document {
                padding: 2rem 1.5rem;
            }

            .service-policies-limitations-add {
                align-items: flex-start;
            }

            .service-policies-limitations-add input {
                width: 100%;
            }

            .service-policies-refund-actions,
            .service-policies-refund-actions>div {
                align-items: stretch;
                flex-direction: column;
                width: 100%;
            }

            .service-policies-compliance-publish {
                align-items: stretch;
                flex-direction: column;
                margin-top: 1.5rem;
            }

            .service-policies-refund-download,
            .service-policies-refund-decline,
            .service-policies-refund-agree,
            .service-policies-save--publish {
                width: 100%;
                flex-basis: auto;
            }
        }
    </style>
</div>
