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
    public string $baseDuration = '90 Minutes';
    public string $bufferTime = '15 Minutes';
    public string $durationSmall = '90 Minutes';
    public string $durationMedium = '90 Minutes';
    public string $durationLarge = '90 Minutes';
    public float $basePrice = 35;
    public float $priceSmall = 35;
    public float $priceMedium = 45;
    public ?float $priceLarge = null;
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
            return ((int) $matches[0]) . ' Minutes';
        }

        $minutes = (int) $value;

        return $minutes > 0 ? $minutes . ' Minutes' : $fallback;
    }

    private function numericPrice(mixed $value, float $fallback): float
    {
        if ($value === null || $value === '') {
            return $fallback;
        }

        return (float) $value;
    }

    private function hasSize(string $size): bool
    {
        return in_array($size, $this->selectedSizes, true);
    }

    private function durationForSize(string $size, string $duration): int|string
    {
        return $this->hasSize($size) ? $this->parseMinutes($duration) : '';
    }

    private function priceForSize(string $size, float $price): float|string
    {
        return $this->hasSize($size) ? (float) $price : '';
    }

    private function priceForLarge(): float|string
    {
        if (!$this->hasSize('large') || $this->priceLarge === null) {
            return '';
        }

        return (float) $this->priceLarge;
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
                    'small' => $this->durationForSize('small', $this->durationSmall),
                    'medium' => $this->durationForSize('medium', $this->durationMedium),
                    'large' => $this->durationForSize('large', $this->durationLarge),
                ],
            ],
            'pricing' => [
                'base_price' => (float) $this->basePrice,
                'overtime_charge' => ['price' => (float) $this->overtimeCharge, 'per' => $this->overtimePer],
                'pricing_by_size' => [
                    'small' => $this->priceForSize('small', $this->priceSmall),
                    'medium' => $this->priceForSize('medium', $this->priceMedium),
                    'large' => $this->priceForLarge(),
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
        $this->baseDuration = $this->minutesLabel(data_get($duration, 'base_duration'), '90 Minutes');
        $this->bufferTime = $this->minutesLabel(data_get($duration, 'buffer_time'), '15 Minutes');
        $this->durationSmall = $this->minutesLabel(data_get($duration, 'duration_by_size.small'), '90 Minutes');
        $this->durationMedium = $this->minutesLabel(data_get($duration, 'duration_by_size.medium'), '90 Minutes');
        $this->durationLarge = $this->minutesLabel(data_get($duration, 'duration_by_size.large'), '90 Minutes');

        $pricing = (array) $service->pricing;
        $this->basePrice = $this->numericPrice(data_get($pricing, 'base_price'), 35);
        $this->priceSmall = $this->numericPrice(data_get($pricing, 'pricing_by_size.small'), $this->basePrice);
        $this->priceMedium = $this->numericPrice(data_get($pricing, 'pricing_by_size.medium'), $this->basePrice);
        $largePrice = data_get($pricing, 'pricing_by_size.large');
        $this->priceLarge = $largePrice === '' || $largePrice === null ? null : (float) $largePrice;
        $this->overtimeCharge = $this->numericPrice(data_get($pricing, 'overtime_charge.price'), 10);
        $this->overtimePer = $this->minutesLabel(data_get($pricing, 'overtime_charge.per'), '15 Minutes');
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
    <form class="service-form" wire:submit.prevent="save">
        <div class="service-form-card">
            <div class="service-form-grid">
                <label class="service-field">
                    <span>Service Name</span>
                    <input type="text" placeholder="Full Groom" wire:model="serviceName"
                        @input="window.dispatchEvent(new CustomEvent('service-form-title-changed', { detail: { title: $event.target.value } }))" />
                </label>

                <label class="service-field">
                    <span>Description</span>
                    <input type="text" wire:model="description"
                        placeholder="Full grooming service including wash, cut, styling, and nail trim." />
                </label>
            </div>
        </div>

        <h4 class="service-section-title">Pet Compatibility</h4>
        <div class="service-form-card">
            <x-business-hub.services.pet-compatibility title="" other-pets-input-id="service-other-pet-groomer" />
        </div>

        <h4 class="service-section-title">Duration & Price</h4>
        <div class="service-form-card">
            <x-business-hub.services.duration-price />
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
    .service-form-wrapper {
        margin-top: 0.5rem;
    }

    .service-form {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .service-form-card {
        background: #fdfdfd;
        border: 1px solid #f6f5f5;
        border-radius: 10px;
        box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
        padding: 1.5rem 1.25rem;
    }

    .service-settings-card h4 {
        margin: 0 0 1rem;
        padding: 0;
        border: 0;
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
    }

    .service-setting-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        min-height: 84px;
        padding: 1.15rem 1.25rem;
        border-radius: 10px;
        border: 1px solid #ededed;
        background: #f5f5f5;
        margin-bottom: 0.65rem;
    }

    .service-setting-row:last-child {
        margin-bottom: 0;
    }

    .service-setting-row.is-active {
        background: #f1f5e9;
    }

    .service-setting-row strong {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .service-setting-row p {
        margin: 0.2rem 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
    }

    .service-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        align-items: end;
    }

    .service-fieldset h4 {
        padding-bottom: 1rem;
        margin-bottom: 1rem;
        margin-top: 1.5rem;
        border-bottom: 1px solid #D4D4D4;
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 32px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .service-field {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .service-field>span {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-field input,
    .service-field select {
        width: 100%;
        height: 48px;
        border: 1px solid #d9d9d9;
        border-radius: 10px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        padding: 0.65rem 0.9rem;
    }

    .service-custom-select {
        position: relative;
        width: 190px;
    }

    .service-custom-trigger {
        width: 190px;
        height: 48px;
        border-radius: 10px;
        border: 1px solid #DDD;
        background: #fff;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 1rem;
    }

    .service-custom-select.is-open .service-custom-trigger {
        border-bottom-left-radius: 0;
        border-bottom-right-radius: 0;
        border-bottom-color: #DDD;
    }

    .service-custom-menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        background: #F8F8F8;
        border: 1px solid #DDD;
        border-top: none;
        border-radius: 0 0 10px 10px;
        z-index: 20;
        overflow: hidden;
    }

    .service-custom-menu-enter {
        transition: opacity 180ms ease, transform 180ms ease;
        transform-origin: top;
    }

    .service-custom-menu-enter-start {
        opacity: 0;
        transform: scaleY(0.95);
    }

    .service-custom-menu-enter-end {
        opacity: 1;
        transform: scaleY(1);
    }

    .service-custom-menu-leave {
        transition: opacity 140ms ease, transform 140ms ease;
        transform-origin: top;
    }

    .service-custom-menu-leave-start {
        opacity: 1;
        transform: scaleY(1);
    }

    .service-custom-menu-leave-end {
        opacity: 0;
        transform: scaleY(0.95);
    }

    .service-custom-option {
        width: 100%;
        border: 0;
        border-bottom: 2px solid #e6e6e5;
        background: #FFF;
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

    .service-custom-option:last-child {
        border-bottom: none;
    }

    .service-custom-option:hover {
        background: #F2F2F2;
    }

    .service-custom-option.is-active {
        background: rgba(216, 232, 183, 0.20);
        color: #A4C560;
    }

    .service-input-with-icon {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .service-input-with-icon>svg {
        margin-top: 10px;
    }

    .service-field input::placeholder,
    .service-field textarea::placeholder {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .service-custom-select-duration,
    .service-custom-select-duration .service-custom-trigger {
        width: 165px;
    }

    .service-toggle-wrap {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 360px;
        border-bottom: 1px solid #E0E0E0;
        padding-bottom: 1.5rem;
        margin-top: 1rem;
    }

    .service-toggle-wrap p {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .service-form-wrapper .service-toggle {
        width: 56px;
        height: 30px;
        border-radius: 999px;
        border: 0;
        background: #cfcfcf;
        position: relative;
        display: inline-block;
        cursor: pointer;
        transition: background-color 0.24s ease;
    }

    .service-form-wrapper .service-toggle::after {
        content: "";
        position: absolute;
        top: 3px;
        left: 4px;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #fff;
        z-index: 1;
        transition: left 0.24s ease, background 0.24s ease;
    }

    .service-form-wrapper .service-toggle::before {
        content: none;
    }

    .service-form-wrapper .service-toggle.is-on {
        background: #c7d59f;
    }

    .service-form-wrapper .service-toggle.is-on::after {
        left: 28px;
        background: transparent url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='20' height='20' viewBox='0 0 20 20' fill='none'%3E%3Cpath d='M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z' fill='white'/%3E%3C/svg%3E") center / 24px 24px no-repeat;
    }

    .service-form-wrapper .service-toggle.is-on::before {
        content: none;
    }

    .service-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 0;
    }

    .service-form-btn {
        width: 138px;
        height: 42px;
        border-radius: 75px;
        border: 1px solid transparent;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        transition: opacity 0.15s ease;
    }

    .service-form-btn:hover {
        opacity: 0.92;
    }

    .service-form-btn-cancel {
        border-color: #D9D9D9;
        background: transparent;
        color: #9D9B98;
    }

    .service-form-btn-save {
        background: #bacf8e;
        color: #fff;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
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
