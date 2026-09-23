<?php

use App\Models\Service;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public ?int $editingId = null;
    public string $serviceName = '';
    public string $description = '';
    public array $otherPets = [];
    public string $otherPetInput = '';
    public array $selectedPets = [];
    public array $selectedSizes = [];
    public bool $addOnsCompatibility = false;
    public bool $visibilityControls = false;
    public string $baseDuration = '60 Minutes';
    public string $bufferTime = '15 min';
    public float $basePrice = 25;
    public float $overtimeCharge = 10;
    public string $overtimePer = '15 Minutes';

    public function mount(?int $editingId = null): void
    {
        $this->editingId = $editingId;

        if ($this->editingId) {
            $this->loadService($this->editingId);
        }
    }

    private function profileId(): ?int
    {
        $id = auth('groomer_spacer')->id() ?? auth()->id();

        return $id ? (int) $id : null;
    }

    private function parseMinutes(?string $value): int|string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        preg_match('/\d+/', $value, $matches);
        return isset($matches[0]) ? (int) $matches[0] : '';
    }

    private function minutesLabel(mixed $value, string $fallback): string
    {
        if (is_string($value) && preg_match('/\d+/', $value, $matches)) {
            $minutes = (int) $matches[0];
            return str_contains((string) $value, 'min') && !str_contains((string) $value, 'Minutes') ? $minutes . ' min' : $minutes . ' Minutes';
        }

        $minutes = (int) $value;

        return $minutes > 0 ? $minutes . ' Minutes' : $fallback;
    }

    private function minutesOption(mixed $value, string $fallback): string
    {
        if (is_string($value) && preg_match('/\d+/', $value, $matches)) {
            return ((int) $matches[0]) . ' Minutes';
        }

        $minutes = (int) $value;

        return $minutes > 0 ? $minutes . ' Minutes' : $fallback;
    }

    private function payload(): array
    {
        return [
            'service_name' => $this->serviceName,
            'description' => $this->description !== '' ? $this->description : '',
            'pet_compatibility' => [
                'pet_types' => array_values($this->selectedPets),
                'other_pets' => array_values($this->otherPets),
                'pet_sizes' => array_values($this->selectedSizes),
            ],
            'duration' => [
                'base_duration' => $this->parseMinutes($this->baseDuration),
                'buffer_time' => $this->parseMinutes($this->bufferTime),
                'duration_by_size' => [
                    'small' => $this->parseMinutes($this->baseDuration),
                    'medium' => $this->parseMinutes($this->baseDuration),
                    'large' => $this->parseMinutes($this->baseDuration),
                ],
            ],
            'pricing' => [
                'base_price' => (float) $this->basePrice,
                'overtime_charge' => ['price' => (float) $this->overtimeCharge, 'per' => $this->overtimePer],
                'pricing_by_size' => [
                    'small' => (float) $this->basePrice,
                    'medium' => (float) $this->basePrice,
                    'large' => (float) $this->basePrice,
                ],
            ],
            'add_ons_compatibility' => $this->addOnsCompatibility,
            'visibility_controls' => $this->visibilityControls,
        ];
    }

    #[On('reset-service-form')]
    public function resetForm(): void
    {
        $this->reset();
        $this->editingId = null;
        $this->js('window.dispatchEvent(new CustomEvent("service-form-baseline"))');
    }

    public function loadService(int $serviceId, bool $silent = false): void
    {
        $profileIds = array_values(array_unique(array_filter([auth('groomer_spacer')->id(), auth()->id(), $this->profileId()])));

        $service = Service::query()
            ->when($profileIds !== [], fn($query) => $query->whereIn('groomer_spacer_id', $profileIds))
            ->find($serviceId);

        if (!$service) {
            return;
        }

        $this->editingId = (int) $service->id;
        $this->serviceName = (string) $service->service_name;
        $this->description = (string) ($service->description ?? '');
        $compat = (array) $service->pet_compatibility;
        $this->selectedPets = array_values((array) data_get($compat, 'pet_types', []));
        $this->otherPets = array_values((array) data_get($compat, 'other_pets', []));
        $this->selectedSizes = array_values((array) data_get($compat, 'pet_sizes', []));
        $duration = (array) $service->duration;
        $this->baseDuration = $this->minutesLabel(data_get($duration, 'base_duration'), '60 Minutes');
        $this->bufferTime = $this->minutesLabel(data_get($duration, 'buffer_time'), '15 min');
        $pricing = (array) $service->pricing;
        $this->basePrice = (float) data_get($pricing, 'base_price', 25);
        $this->overtimeCharge = (float) data_get($pricing, 'overtime_charge.price', 10);
        $this->overtimePer = $this->minutesOption(data_get($pricing, 'overtime_charge.per'), '15 Minutes');
        $this->addOnsCompatibility = (bool) $service->add_ons_compatibility;
        $this->visibilityControls = (bool) $service->visibility_controls;
        if ($silent) {
            $this->skipRender();
        }
        $this->js('window.dispatchEvent(new CustomEvent("service-form-baseline"))');
    }

    public function save(): void
    {
        $this->validate([
            'serviceName' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $profileId = $this->profileId();

        if (!$profileId) {
            $this->addError('serviceName', 'Groomer/Spacer profile not found for current user.');
            return;
        }

        if ($this->editingId) {
            $service = Service::query()->where('groomer_spacer_id', $profileId)->find($this->editingId);

            if (!$service) {
                $this->addError('serviceName', 'Service not found.');
                return;
            }

            $service->update($this->payload());
        } else {
            $service = Service::create(
                array_merge($this->payload(), [
                    'groomer_spacer_id' => $profileId,
                ]),
            );
        }

        $this->dispatch('service-created', itemId: $service->id);
        $this->reset();
    }

    public function deleteCurrent(): void
    {
        $profileId = $this->profileId();

        if (!$profileId || !$this->editingId) {
            return;
        }

        $service = Service::query()->where('groomer_spacer_id', $profileId)->find($this->editingId);

        if ($service) {
            $service->delete();
        }

        $this->dispatch('service-deleted');
        $this->js('window.dispatchEvent(new CustomEvent("nav-list-loading-end")); window.dispatchEvent(new CustomEvent("service-form-cancel"))');
    }
}; ?>

<section class="service-form-wrapper" aria-label="Add service form"
    x-data="{ addOnsCompatibility: $wire.entangle('addOnsCompatibility').live, visibilityControls: $wire.entangle('visibilityControls').live }"
    x-on:service-form-delete.window="$wire.deleteCurrent()"
    x-on:service-edit-requested.window="
        const d = $event.detail || {};
        const id = Number(d.serviceId || d.service_id || d.id || 0);
        if (d.state) { window.applyServiceFormState($wire, d.state); }
        if (id) { $wire.loadService(id, !!d.state); }
    ">
    <form class="service-form service-form-space" wire:submit.prevent="save">
        <div class="service-form-card">
            <div class="service-form-grid">
                <label class="service-field">
                    <span>Service Name</span>
                    <input type="text" placeholder="Hourly" wire:model="serviceName"
                        @input="window.dispatchEvent(new CustomEvent('service-form-title-changed', { detail: { title: $event.target.value } }))" />
                </label>

                <label class="service-field">
                    <span>Description</span>
                    <input type="text" placeholder="Book our space per hour" wire:model="description" />
                </label>
            </div>
        </div>

        <div class="service-form-block">
            <h4 class="service-section-title">Pet Compatibility</h4>
            <div class="service-form-card">
                <x-business-hub.services.pet-compatibility title="" other-pets-input-id="service-other-pet-space" />
            </div>
        </div>

        <div class="service-form-block">
            <h4 class="service-section-title">Duration & Price</h4>
            <div class="service-form-card">
                <x-business-hub.services.duration-price :show-by-size="false" :show-buffer="false" money />
            </div>
        </div>

        <div class="service-form-card service-settings-card">
            <h4>Setting Options</h4>
            <div class="service-setting-row">
                <div>
                    <strong>Add-ons Compatibility</strong>
                    <p>Allow add-ons with this service</p>
                </div>
                <button type="button" class="service-toggle" :class="{ 'is-on': addOnsCompatibility }"
                    @click="addOnsCompatibility = !addOnsCompatibility"></button>
            </div>
            <div class="service-setting-row" :class="{ 'is-active': visibilityControls }">
                <div>
                    <strong>Visibility Controls</strong>
                    <p>This service is active on your profile — clients can book it now</p>
                </div>
                <button type="button" class="service-toggle" :class="{ 'is-on': visibilityControls }"
                    @click="visibilityControls = !visibilityControls"></button>
            </div>
        </div>

        <x-business-hub.services.form-footer />
    </form>
</section>

<style>
    .service-form-space {
        gap: 40px;
    }

    .service-form-space .service-form-block {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .service-form-space .service-form-card {
        overflow: visible;
    }

    .service-form-space .service-setting-row strong {
        font-size: 18px;
        font-weight: 600;
    }

    .service-form-space .service-setting-row p {
        color: #9c9790;
        font-size: 18px;
        font-weight: 400;
        line-height: normal;
    }

    .service-form-space .service-toggle {
        width: 44px;
        height: 24px;
        background: #d4d4d4;
    }

    .service-form-space .service-toggle::after {
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        background: #fff;
    }

    .service-form-space .service-toggle.is-on {
        background: #d8e8b7;
    }

    .service-form-space .service-toggle.is-on::after {
        left: 22px;
        background: transparent url('/images/business-hub/icon-toggle-tick.svg') center / 20px 20px no-repeat;
    }

    .service-form-space .service-toggle.is-on::before {
        content: none;
    }

    .service-form-space .service-field {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .service-form-space .service-field>span {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .service-form-space .service-form-grid .service-field input {
        width: 100%;
        height: 48px;
        border: 1px solid #d4d4d4;
        border-radius: 10px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        padding: 0 20px;
    }

    .service-form-space .service-field input::placeholder {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .save-btn-loading {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
    }

    .hidden {
        display: none !important;
    }

    .save-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 999px;
        animation: save-spin 0.8s linear infinite;
    }

    @keyframes save-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
