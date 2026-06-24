<?php

use App\Models\GroomerSpacerProfile;
use App\Models\ServicePolicy;
use Livewire\Volt\Component;

new class extends Component {
    public ?string $editingSection = null;

    public string $cancellationWindow = '';
    public string $cancellationFee = '';
    public string $noShowFee = '';

    public string $gracePeriod = '';
    public string $lateArrivalFee = '';

    public bool $refundPolicy = true;
    public string $serviceLimitationsText = '';
    public bool $animalWelfareStatement = true;
    public string $hygieneSafetyStandardsText = '';
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
            'animalWelfareEnabled' => (bool) ($data['animal_welfare_statement'] ?? false),
            'animalWelfareUploadedDate' => $policy?->created_at?->format('d M Y') ?? 'Not uploaded',
            'hygieneSafetyStandards' => $this->nonEmptyStrings($data['hygiene_safety_standards'] ?? []),
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
            'cancellationFee' => ['nullable', 'string', 'max:255'],
            'noShowFee' => ['nullable', 'string', 'max:255'],
        ]);

        $this->savePolicyAttributes([
            'cancellation_policy' => [
                [
                    'Cancellation Window' => $validated['cancellationWindow'] ?? '',
                    'Cancellation Fee' => $validated['cancellationFee'] ?? '',
                    'No Show Fee' => $validated['noShowFee'] ?? '',
                ],
            ],
        ]);
    }

    public function saveLateArrivalPolicy(): void
    {
        $validated = $this->validate([
            'gracePeriod' => ['nullable', 'string', 'max:255'],
            'lateArrivalFee' => ['nullable', 'string', 'max:255'],
        ]);

        $this->savePolicyAttributes([
            'late_arrival_policy' => [
                [
                    'Grace Period' => $validated['gracePeriod'] ?? '',
                    'Late Arrival Fee (Optional)' => $validated['lateArrivalFee'] ?? '',
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

    public function saveServiceLimitations(): void
    {
        $this->validate([
            'serviceLimitationsText' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->savePolicyAttributes([
            'service_limitations' => $this->linesToArray($this->serviceLimitationsText),
        ]);
    }

    public function saveAnimalWelfareStatement(): void
    {
        $this->savePolicyAttributes([
            'animal_welfare_statement' => $this->animalWelfareStatement,
        ]);
    }

    public function saveHygieneSafetyStandards(): void
    {
        $this->validate([
            'hygieneSafetyStandardsText' => ['nullable', 'string', 'max:2000'],
        ]);

        $this->savePolicyAttributes([
            'hygiene_safety_standards' => $this->linesToArray($this->hygieneSafetyStandardsText),
        ]);
    }

    public function saveComplianceDeclaration(): void
    {
        $this->savePolicyAttributes([
            'compliance_declaration' => $this->complianceDeclaration,
        ]);
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
        $this->cancellationFee = (string) ($cancellation['Cancellation Fee'] ?? '');
        $this->noShowFee = (string) ($cancellation['No Show Fee'] ?? '');
        $this->gracePeriod = (string) ($lateArrival['Grace Period'] ?? '');
        $this->lateArrivalFee = (string) ($lateArrival['Late Arrival Fee (Optional)'] ?? '');
        $this->refundPolicy = (bool) ($data['refund_policy'] ?? true);
        $this->serviceLimitationsText = implode(PHP_EOL, $this->nonEmptyStrings($data['service_limitations'] ?? []));
        $this->animalWelfareStatement = (bool) ($data['animal_welfare_statement'] ?? true);
        $this->hygieneSafetyStandardsText = implode(PHP_EOL, $this->nonEmptyStrings($data['hygiene_safety_standards'] ?? []));
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
        return ['cancellation', 'late-arrival', 'refund', 'limitations', 'animal-welfare', 'hygiene-safety', 'compliance', 'timeline'];
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
}; ?>

<div class="service-policies-settings" x-data="{
    showServicePoliciesAlert: true,
    editingSection: @js($editingSection),
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
                    wire:target="saveCancellationPolicy">Save Details</button>
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
        <div class="service-policies-card">
            <div class="service-policies-edit-grid service-policies-edit-grid--three" x-cloak
                x-show="editingSection === 'cancellation'">
                <label>
                    <span>Cancellation Window</span>
                    <input type="text" wire:model.defer="cancellationWindow">
                </label>
                <label>
                    <span>Cancellation Fee</span>
                    <input type="text" wire:model.defer="cancellationFee">
                </label>
                <label>
                    <span>No Show Fee</span>
                    <input type="text" wire:model.defer="noShowFee">
                </label>
            </div>
            <div class="service-policies-value-grid service-policies-value-grid--three"
                x-show="editingSection !== 'cancellation'">
                <div>
                    <span>Cancellation Window</span>
                    <p>{{ $cancellation['Cancellation Window'] ?? 'Not provided' }}</p>
                </div>
                <div>
                    <span>Cancellation Fee</span>
                    <p>{{ $cancellation['Cancellation Fee'] ?? 'Not provided' }}</p>
                </div>
                <div>
                    <span>No Show Fee</span>
                    <p>{{ $cancellation['No Show Fee'] ?? 'Not provided' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Late Arrival Policy</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak x-show="editingSection === 'late-arrival'"
                    @click="saveSection('saveLateArrivalPolicy')" wire:loading.attr="disabled"
                    wire:target="saveLateArrivalPolicy">Save Details</button>
                <button type="button" class="service-policies-edit" x-cloak x-show="editingSection !== 'late-arrival'"
                    @click="editSection('late-arrival')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card">
            <div class="service-policies-edit-grid service-policies-edit-grid--two" x-cloak
                x-show="editingSection === 'late-arrival'">
                <label>
                    <span>Grace Period</span>
                    <input type="text" wire:model.defer="gracePeriod">
                </label>
                <label>
                    <span>Late Arrival Fee (Optional)</span>
                    <input type="text" wire:model.defer="lateArrivalFee">
                </label>
            </div>
            <div class="service-policies-value-grid service-policies-value-grid--four"
                x-show="editingSection !== 'late-arrival'">
                <div>
                    <span>Grace Period</span>
                    <p>{{ $lateArrival['Grace Period'] ?? 'Not provided' }}</p>
                </div>
                <div>
                    <span>Late Arrival Fee (Optional)</span>
                    <p>{{ $lateArrival['Late Arrival Fee (Optional)'] ?? 'Not provided' }}</p>
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
                    wire:target="saveRefundPolicy">Save Details</button>
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
        <div class="service-policies-card service-policies-document">
            <div x-cloak x-show="editingSection === 'refund'">
                <label class="service-policies-check">
                    <input type="checkbox" wire:model.defer="refundPolicy">
                    <span>Refund Policy is acknowledged and active</span>
                </label>
            </div>
            <div x-show="editingSection !== 'refund'">
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
                    wire:loading.attr="disabled" wire:target="saveServiceLimitations">Save Details</button>
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
        <div class="service-policies-card">
            <label class="service-policies-textarea" x-cloak x-show="editingSection === 'limitations'">
                <span>Service Limitations</span>
                <textarea rows="5" wire:model.defer="serviceLimitationsText"></textarea>
            </label>
            <div class="service-policies-list" x-show="editingSection !== 'limitations'">
                <span>Service Limitations</span>
                @forelse ($serviceLimitations as $limitation)
                    <p>{{ $limitation }}</p>
                @empty
                    <p>Not provided</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Animal Welfare Statement</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak
                    x-show="editingSection === 'animal-welfare'" @click="saveSection('saveAnimalWelfareStatement')"
                    wire:loading.attr="disabled" wire:target="saveAnimalWelfareStatement">Save Details</button>
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
        <div class="service-policies-card service-policies-document">
            <div x-cloak x-show="editingSection === 'animal-welfare'">
                <label class="service-policies-check">
                    <input type="checkbox" wire:model.defer="animalWelfareStatement">
                    <span>Animal welfare statement is acknowledged and active</span>
                </label>
            </div>
            <div x-show="editingSection !== 'animal-welfare'">
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
                    wire:loading.attr="disabled" wire:target="saveHygieneSafetyStandards">Save Details</button>
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
        <div class="service-policies-card">
            <label class="service-policies-textarea" x-cloak x-show="editingSection === 'hygiene-safety'">
                <span>Hygiene &amp; Safety Standards</span>
                <textarea rows="5" wire:model.defer="hygieneSafetyStandardsText"></textarea>
            </label>
            <div class="service-policies-list" x-show="editingSection !== 'hygiene-safety'">
                <span>Hygiene &amp; Safety Standards</span>
                @forelse ($hygieneSafetyStandards as $standard)
                    <p>{{ $standard }}</p>
                @empty
                    <p>Not provided</p>
                @endforelse
            </div>
        </div>
    </section>

    <section class="service-policies-block">
        <div class="service-policies-section-title">
            <h3>Compliance Declaration</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak x-show="editingSection === 'compliance'"
                    @click="saveSection('saveComplianceDeclaration')" wire:loading.attr="disabled"
                    wire:target="saveComplianceDeclaration">Save Details</button>
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
        <div class="service-policies-card service-policies-document">
            <div x-cloak x-show="editingSection === 'compliance'">
                <label class="service-policies-check">
                    <input type="checkbox" wire:model.defer="complianceDeclaration">
                    <span>Compliance declaration is acknowledged and active</span>
                </label>
            </div>
            <div x-show="editingSection !== 'compliance'">
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

    <section class="service-policies-block">
        <div class="service-policies-section-title service-policies-section-title--compact">
            <h3>Compliance Timeline</h3>
            <div class="service-policies-title-actions">
                <button type="button" class="service-policies-save" x-cloak x-show="editingSection === 'timeline'"
                    @click="saveSection('saveComplianceTimeline')" wire:loading.attr="disabled"
                    wire:target="saveComplianceTimeline">Save Details</button>
                <button type="button" class="service-policies-edit" x-cloak x-show="editingSection !== 'timeline'"
                    @click="editSection('timeline')">
                    <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                            viewBox="0 0 16 15" fill="none">
                            <path
                                d="M10.2059 2.37997L12.853 4.97712M8.44119 14.5H15.5M1.38236 11.0371L0.500011 14.5L4.02942 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.853 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38236 11.0371Z"
                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg></span> Edit details
                </button>
            </div>
        </div>
        <div class="service-policies-card service-policies-timeline">
            <label class="service-policies-textarea" x-cloak x-show="editingSection === 'timeline'">
                <span>Verify Dates</span>
                <textarea rows="5" wire:model.defer="complianceVerifyDatesText"></textarea>
            </label>
            <div x-show="editingSection !== 'timeline'">
                @forelse ($verifyDates as $date)
                    <p><span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg" width="24"
                                height="24" viewBox="0 0 24 24" fill="none">
                                <path
                                    d="M12 0C5.4 0 0 5.4 0 12C0 18.6 5.4 24 12 24C18.6 24 24 18.6 24 12C24 5.4 18.6 0 12 0ZM9.6 18L3.6 12L5.292 10.308L9.6 14.604L18.708 5.496L20.4 7.2L9.6 18Z"
                                    fill="#C9DDA0" />
                            </svg></span>{{ $date }} - Business verified</p>
                @empty
                    <p>No timeline entries added.</p>
                @endforelse
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
            padding-bottom: 1.5rem;
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

        .service-policies-card {
            border-radius: 8px;
            background: rgba(232, 232, 232, 0.20);
            padding: 1.2rem;
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
        }
    </style>
</div>
