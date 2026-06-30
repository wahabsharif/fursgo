<?php

use App\Models\GroomerSpacerProfile;
use App\Models\Service;
use Livewire\Volt\Component;

new class extends Component {
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
    public string $overtimePer = '15 min';

    private function parseMinutes(?string $value): int|string
    {
        if ($value === null || trim($value) === '') {
            return '';
        }

        preg_match('/\d+/', $value, $matches);
        return isset($matches[0]) ? (int) $matches[0] : '';
    }

    public function save(): void
    {
        $this->validate([
            'serviceName' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();

        if (!$profile) {
            $this->addError('serviceName', 'Groomer/Spacer profile not found for current user.');
            return;
        }

        $service = Service::create([
            'groomer_spacer_id' => $profile->id,
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
        ]);

        $this->dispatch('service-created', itemId: $service->id);
        $this->dispatch('service-form-cancel');
        $this->reset();
    }
}; ?>

<section class="service-form-wrapper" aria-label="Add service form" x-data="{ addOnsCompatibility: $wire.entangle('addOnsCompatibility').live, visibilityControls: $wire.entangle('visibilityControls').live }">
    <form class="service-form" wire:submit.prevent="save"
        x-on:submit="window.dispatchEvent(new CustomEvent('nav-list-loading-start'))">
        <div class="service-form-grid">
            <label class="service-field" style="width: 400px;">
                <span>Service Name</span>
                <input type="text" placeholder="Hourly" wire:model="serviceName" />
            </label>

            <label class="service-field" style="width: 505px;">
                <span style="color: #9D9B98;">Description</span>
                <input type="text" placeholder="Book our space per hour" wire:model="description" />
            </label>
        </div>

        <x-business-hub.services.pet-compatibility other-pets-input-id="service-other-pet-space" />

        <x-business-hub.services.duration />

        <x-business-hub.services.price show-advanced :muted-overtime-label="false" />

        <div class="service-fieldset">
            <h4>Add-ons Compatibility</h4>
            <div class="service-toggle-wrap">
                <p>Allow add-ons with this service</p>
                <button type="button" class="service-toggle" :class="{ 'is-on': addOnsCompatibility }"
                    @click="addOnsCompatibility = !addOnsCompatibility"></button>
            </div>
        </div>
        <div class="service-fieldset">
            <h4>Visibility Controls</h4>
            <div class="service-toggle-wrap">
                <p>Active Service</p>
                <button type="button" class="service-toggle" :class="{ 'is-on': visibilityControls }"
                    @click="visibilityControls = !visibilityControls"></button>
            </div>
        </div>

        <div class="service-form-actions">
            <button type="button" class="service-form-btn service-form-btn-cancel"
                @click="$dispatch('service-form-cancel')">Cancel</button>
            <button type="submit" class="service-form-btn service-form-btn-save" wire:loading.attr="disabled"
                wire:target="save">
                <span class="save-btn-text" wire:loading.class="hidden" wire:target="save">Save Changes</span>
                <span class="save-btn-loading hidden" wire:loading.class.remove="hidden" wire:target="save">
                    <span class="save-spinner"></span>
                </span>
            </button>
        </div>
    </form>
</section>

<style>
    .service-form-wrapper {
        margin-top: 0.5rem;
    }

    .service-form {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }

    .service-form-grid {
        display: flex;
        justify-content: start;
        align-items: end;
        gap: 1.5rem;
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

    .service-toggle {
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

    .service-toggle::after {
        content: "";
        position: absolute;
        top: 3px;
        left: 4px;
        width: 24px;
        height: 24px;
        border-radius: 999px;
        background: #fff;
        z-index: 1;
        transition: left 0.24s ease;
    }

    .service-toggle::before {
        content: none;
        opacity: 0;
        transform: scale(0.88);
    }

    .service-toggle.is-on {
        background: #c7d59f;
    }

    .service-toggle.is-on::after {
        left: 28px;
    }

    .service-toggle.is-on::before {
        content: "";
        position: absolute;
        right: 9px;
        top: 9px;
        width: 13px;
        height: 13px;
        background-repeat: no-repeat;
        background-position: center;
        background-size: contain;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='13' height='11' viewBox='0 0 13 11' fill='none'%3E%3Cpath d='M1.25 5.8L4.4 8.95L11.75 1.6' stroke='%23C7D59F' stroke-width='2.1' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        z-index: 2;
        opacity: 1;
        transform: scale(1);
        animation: toggle-icon-in 0.16s ease-in 0.24s both;
    }

    @keyframes toggle-icon-in {
        from {
            opacity: 0;
            transform: scale(0.9);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    .service-form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
        margin-top: 0.5rem;
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
        background: #c9dda0;
        color: #fff;
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
