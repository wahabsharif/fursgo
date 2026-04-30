<?php

use App\Models\GroomerSpacerProfile;
use App\Models\AddOn;
use Livewire\Volt\Component;

new class extends Component {
    public string $addOnsName = '';
    public string $description = '';
    public string $otherPet = '';
    public array $selectedPets = ['cat', 'dog', 'other'];
    public array $selectedSizes = ['small', 'medium', 'large'];
    public bool $visibilityControls = true;
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
            'addOnsName' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ]);

        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();

        if (!$profile) {
            $this->addError('addOnsName', 'Groomer/Spacer profile not found for current user.');
            return;
        }

        AddOn::create([
            'groomer_spacer_id' => $profile->id,
            'add_ons_name' => $this->addOnsName,
            'description' => $this->description !== '' ? $this->description : '',
            'pet_compatibility' => [
                'pet_type' => array_values($this->selectedPets),
                'other_pets' => $this->otherPet !== '' ? [$this->otherPet] : '',
                'pet_size' => array_values($this->selectedSizes),
            ],
            'duration' => '',
            'pricing' => [
                'base_price' => (float) $this->basePrice,
                'overtime_charge' => ['price' => (float) $this->overtimeCharge, 'per' => $this->overtimePer],
                'pricing_by_size' => [
                    'small' => (float) $this->basePrice,
                    'medium' => (float) $this->basePrice,
                    'large' => (float) $this->basePrice,
                ],
            ],
            'add_ons_compatibility' => false,
            'visibility_controls' => $this->visibilityControls,
        ]);

        $this->dispatch('add-on-created');
        $this->reset(['addOnsName', 'description', 'otherPet']);
        $this->dispatch('service-form-cancel');
    }
}; ?>

<section class="service-form-wrapper" aria-label="Add service form" x-data="{ selectedPets: $wire.entangle('selectedPets').live, selectedSizes: $wire.entangle('selectedSizes').live, visibilityControls: $wire.entangle('visibilityControls').live, basePrice: $wire.entangle('basePrice').live, overtimeCharge: $wire.entangle('overtimeCharge').live, overtimePer: $wire.entangle('overtimePer').live }">
    <form class="service-form" wire:submit.prevent="save"
        x-on:submit="window.dispatchEvent(new CustomEvent('nav-list-loading-start'))">
        <div class="service-form-grid">
            <label class="service-field" style="width: 400px;">
                <span>Add-on Name</span>
                <input type="text" placeholder="Storage Locker" wire:model="addOnsName" />
            </label>

            <label class="service-field" style="width: 505px;">
                <span style="color: #9D9B98;">Description</span>
                <input type="text" placeholder="Keep your belongings in one of our on-site lockers."
                    wire:model="description" />
            </label>
        </div>

        <div class="service-fieldset">
            <h4>Pet Compatibility</h4>
            <div class="service-chip-row">
                <div>
                    <span>Pet Types</span>
                    <div>
                        <button type="button" class="service-chip"
                            :class="{ 'is-active': selectedPets.includes('cat') }"
                            @click="selectedPets.includes('cat') ? selectedPets = selectedPets.filter((pet) => pet !== 'cat') : selectedPets.push('cat')">
                            <svg class="service-chip-tick" xmlns="http://www.w3.org/2000/svg" width="12"
                                height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C560" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <svg class="service-chip-icon" xmlns="http://www.w3.org/2000/svg" width="14"
                                height="20" viewBox="0 0 14 20" fill="none" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.54981 3.32031C7.25466 3.2334 8.23074 3.25555 9.16407 3.57129C10.1961 3.92045 11.1929 4.63819 11.6807 5.96875L13.6914 6.93262L13.7354 7.08984C14.0021 8.05153 14.1602 9.54554 13.7471 10.9053C13.5389 11.5904 13.1836 12.2495 12.6162 12.7832C12.0474 13.3182 11.2824 13.7115 10.2832 13.8896C6.68798 14.5308 4.64839 17.6058 4.08595 19.0576C3.85458 19.7189 2.48197 19.8865 2.15919 19.2646C-2.66183 9.97639 1.67491 2.64266 4.66798 0L6.54981 3.32031ZM8.77735 6.68164C8.24975 6.68164 7.68946 6.94374 7.68946 7.99121C7.68948 8.7143 8.61152 7.99121 9.21192 7.99121C9.81222 7.9913 9.86427 8.71425 9.86427 7.99121C9.86427 7.26825 9.37758 6.6819 8.77735 6.68164Z"
                                    fill="currentColor" />
                            </svg>
                            <span>Cat</span>
                        </button>
                        <button type="button" class="service-chip"
                            :class="{ 'is-active': selectedPets.includes('other') }"
                            @click="selectedPets.includes('other') ? selectedPets = selectedPets.filter((pet) => pet !== 'other') : selectedPets.push('other')">
                            <svg class="service-chip-tick" xmlns="http://www.w3.org/2000/svg" width="12"
                                height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C560" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <svg class="service-chip-icon" xmlns="http://www.w3.org/2000/svg" width="21"
                                height="17" viewBox="0 0 21 17" fill="none" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.75 0C6.0075 0 5.4255 0.465182 5.0655 1.02309C4.701 1.58564 4.5 2.31973 4.5 3.09091C4.5 3.86209 4.701 4.59618 5.0655 5.15873C5.4255 5.71509 6.0075 6.18182 6.75 6.18182C7.4925 6.18182 8.0745 5.71664 8.4345 5.15873C8.799 4.59618 9 3.86209 9 3.09091C9 2.31973 8.799 1.58564 8.4345 1.02309C8.0745 0.466727 7.4925 0 6.75 0ZM14.25 0C13.5075 0 12.9255 0.465182 12.5655 1.02309C12.201 1.58564 12 2.31973 12 3.09091C12 3.86209 12.201 4.59618 12.5655 5.15873C12.9255 5.71509 13.5075 6.18182 14.25 6.18182C14.9925 6.18182 15.5745 5.71664 15.9345 5.15873C16.299 4.59618 16.5 3.86209 16.5 3.09091C16.5 2.31973 16.299 1.58564 15.9345 1.02309C15.5745 0.466727 14.9925 0 14.25 0ZM2.25 6.95455C1.5075 6.95455 0.9255 7.41973 0.5655 7.97764C0.201 8.54018 0 9.27427 0 10.0455C0 10.8166 0.201 11.5507 0.5655 12.1133C0.9255 12.6696 1.5075 13.1364 2.25 13.1364C2.9925 13.1364 3.5745 12.6712 3.9345 12.1133C4.299 11.5507 4.5 10.8166 4.5 10.0455C4.5 9.27427 4.299 8.54018 3.9345 7.97764C3.5745 7.42127 2.9925 6.95455 2.25 6.95455ZM10.5 6.95455C8.7 6.95455 7.3665 7.94982 6.5145 9.18464C5.673 10.4009 5.25 11.9108 5.25 13.1364C5.25 14.5644 6.0825 15.5581 7.104 16.1531C8.109 16.7404 9.369 17 10.5 17C11.631 17 12.891 16.7419 13.896 16.1531C14.916 15.5565 15.75 14.5644 15.75 13.1364C15.75 11.9108 15.327 10.4009 14.4855 9.18464C13.635 7.94827 12.3015 6.95455 10.5 6.95455ZM18.75 6.95455C18.0075 6.95455 17.4255 7.41973 17.0655 7.97764C16.701 8.54018 16.5 9.27427 16.5 10.0455C16.5 10.8166 16.701 11.5507 17.0655 12.1133C17.4255 12.6696 18.0075 13.1364 18.75 13.1364C19.4925 13.1364 20.0745 12.6712 20.4345 12.1133C20.799 11.5507 21 10.8166 21 10.0455C21 9.27427 20.799 8.54018 20.4345 7.97764C20.0745 7.42127 19.4925 6.95455 18.75 6.95455Z"
                                    fill="currentColor" />
                            </svg>
                            <span>Other</span>
                        </button>
                        <button type="button" class="service-chip"
                            :class="{ 'is-active': selectedPets.includes('dog') }"
                            @click="selectedPets.includes('dog') ? selectedPets = selectedPets.filter((pet) => pet !== 'dog') : selectedPets.push('dog')">
                            <svg class="service-chip-tick" xmlns="http://www.w3.org/2000/svg" width="12"
                                height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C560" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <svg class="service-chip-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                height="19" viewBox="0 0 20 19" fill="none" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.0941 8.70252e-10C10.7113 -1.81926e-05 11.2943 0.285229 11.6732 0.772461L14.4515 4.34473C14.6106 4.54926 14.8434 4.68405 15.0999 4.7207L17.7464 5.09863C18.1045 5.14979 18.4075 5.39029 18.5189 5.73438C18.8051 6.61821 19.2752 8.35348 18.9417 9.37109C18.5358 10.6085 17.8843 11.2276 16.7103 11.5303C14.6994 12.0487 13.0373 11.4562 11.0726 12.9541C10.53 13.3678 10.1419 13.8958 9.87143 14.4863C8.80526 16.8134 5.84433 19.2963 3.63803 17.999C2.21377 17.1611 1.5216 15.4772 1.94468 13.8799L2.54135 11.624C2.68005 11.6145 2.81557 11.6042 2.94468 11.5879C3.30448 11.5425 3.65539 11.4642 3.88706 11.3174C4.07261 11.1997 4.26051 10.9971 4.43686 10.7715C4.6171 10.5409 4.80106 10.2654 4.97495 9.98145C5.32278 9.41333 5.64148 8.79555 5.82944 8.39844C5.8885 8.27365 5.83506 8.12451 5.7103 8.06543C5.58561 8.00644 5.43645 8.05903 5.37729 8.18359C5.19516 8.56842 4.88485 9.17086 4.54819 9.7207C4.3799 9.99556 4.20757 10.2537 4.04331 10.4639C3.87527 10.6788 3.72991 10.8244 3.61948 10.8945C3.48843 10.9776 3.23457 11.0473 2.88218 11.0918C2.54069 11.1349 2.14164 11.1512 1.74839 11.1504C1.36053 11.1496 0.983727 11.129 0.681003 11.1074C0.194785 10.8871 -0.103935 10.3229 0.0335424 9.79883C1.33099 4.85932 2.07831 2.59895 3.33335 1.34375C4.67684 0.000287295 7.36362 8.70252e-10 7.36362 8.70252e-10H10.0941ZM10.5492 4.47949C9.98084 4.47949 9.37838 4.76142 9.37827 5.88965C9.37827 6.66872 10.3711 5.88965 11.0179 5.88965C11.6644 5.88994 11.7201 6.66857 11.7201 5.88965C11.7199 5.11076 11.1959 4.47955 10.5492 4.47949Z"
                                    fill="currentColor" />
                            </svg>
                            <span>Dog</span>
                        </button>
                    </div>
                </div>
                <div>
                    <span>Pet Size</span>
                    <div>
                        <button type="button" class="service-chip"
                            :class="{ 'is-active': selectedSizes.includes('small') }"
                            @click="selectedSizes.includes('small') ? selectedSizes = selectedSizes.filter((size) => size !== 'small') : selectedSizes.push('small')">
                            <svg class="service-chip-tick" xmlns="http://www.w3.org/2000/svg" width="12"
                                height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C560" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Small 0-7 kg</span>
                        </button>
                        <button type="button" class="service-chip"
                            :class="{ 'is-active': selectedSizes.includes('medium') }"
                            @click="selectedSizes.includes('medium') ? selectedSizes = selectedSizes.filter((size) => size !== 'medium') : selectedSizes.push('medium')">
                            <svg class="service-chip-tick" xmlns="http://www.w3.org/2000/svg" width="12"
                                height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C560" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Medium 8-18 kg</span>
                        </button>
                        <button type="button" class="service-chip"
                            :class="{ 'is-active': selectedSizes.includes('large') }"
                            @click="selectedSizes.includes('large') ? selectedSizes = selectedSizes.filter((size) => size !== 'large') : selectedSizes.push('large')">
                            <svg class="service-chip-tick" xmlns="http://www.w3.org/2000/svg" width="12"
                                height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C560" stroke-width="1.5"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <span>Large 19+ kg</span>
                        </button>
                    </div>
                </div>
            </div>
            <label class="service-field">
                <span>Other</span>
                <div class="service-input-with-icon">
                    <input type="text" placeholder="Specify pet type" style="width: 332px;"
                        wire:model="otherPet" />
                    <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"
                        fill="none">
                        <g filter="url(#filter0_d_3_541)">
                            <rect x="8" y="3" width="48" height="48" rx="24" fill="#FFC97A" />
                        </g>
                        <path d="M32 19V27.495M32 27.495V35.99M32 27.495H40.495M32 27.495H23.505" stroke="white"
                            stroke-width="2" stroke-linecap="round" />
                        <defs>
                            <filter id="filter0_d_3_541" x="0" y="0" width="64" height="64"
                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                <feOffset dy="5" />
                                <feGaussianBlur stdDeviation="4" />
                                <feComposite in2="hardAlpha" operator="out" />
                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0" />
                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_3_541" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_3_541"
                                    result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </div>
            </label>
        </div>

        <div class="service-fieldset">
            <h4>Price</h4>
            <div class="service-price-top-row">
                <label class="service-field">
                    <span>Base Price</span>
                    <div class="service-number-input-wrap service-number-input-wrap-currency">
                        <input type="number" min="0" step="0.01" x-model="basePrice"
                            style="width: 190px;" />
                        <div class="service-number-input-controls">
                            <button type="button" class="service-number-step-btn" aria-label="Increase base price"
                                @click="$event.currentTarget.closest('.service-number-input-wrap').querySelector('input').stepUp(); $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').dispatchEvent(new Event('input', { bubbles: true }))">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                    viewBox="0 0 11 6" fill="none">
                                    <path d="M10.3741 5.47876L5.39527 0.499941L0.500024 5.39518" stroke="#3B3731"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <button type="button" class="service-number-step-btn" aria-label="Decrease base price"
                                @click="$event.currentTarget.closest('.service-number-input-wrap').querySelector('input').stepDown(); $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').dispatchEvent(new Event('input', { bubbles: true }))">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                    viewBox="0 0 11 6" fill="none">
                                    <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </label>
                <div class="service-price-advanced-wrap">
                    <button type="button" class="service-price-advanced-btn">
                        <span>+</span>
                        <span>Advanced Price Settings</span>
                    </button>
                </div>
            </div>

            <div class="service-overtime-wrap">
                <label class="service-field">
                    <span style="font-weight: 400;">Overtime charges</span>
                    <div class="service-overtime-inline">
                        <div class="service-number-input-wrap service-number-input-wrap-currency service-number-input-wrap-compact"
                            style="width: 85px;">
                            <input type="number" min="0" step="0.01" x-model="overtimeCharge"
                                style="width: 100%;" />
                            <div class="service-number-input-controls">
                                <button type="button" class="service-number-step-btn"
                                    aria-label="Increase overtime charge"
                                    @click="$event.currentTarget.closest('.service-number-input-wrap').querySelector('input').stepUp(); $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').dispatchEvent(new Event('input', { bubbles: true }))">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.3741 5.47876L5.39527 0.499941L0.500024 5.39518" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                                <button type="button" class="service-number-step-btn"
                                    aria-label="Decrease overtime charge"
                                    @click="$event.currentTarget.closest('.service-number-input-wrap').querySelector('input').stepDown(); $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').dispatchEvent(new Event('input', { bubbles: true }))">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <span class="service-overtime-per-text">per</span>
                        <div class="service-custom-select service-custom-select-overtime"
                            style="width: 190px;background: #F7F7F7;" :class="{ 'is-open': open }"
                            x-data="{ open: false }" @keydown.escape.window="open = false">
                            <button type="button" class="service-custom-trigger"
                                style="background:transparent;width: 100%;" @click="open = !open"
                                :aria-expanded="open.toString()">
                                <span x-text="overtimePer"></span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                    viewBox="0 0 11 6" fill="none">
                                    <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="service-custom-menu" x-cloak x-show="open" @click.outside="open = false"
                                x-transition:enter="service-custom-menu-enter"
                                x-transition:enter-start="service-custom-menu-enter-start"
                                x-transition:enter-end="service-custom-menu-enter-end"
                                x-transition:leave="service-custom-menu-leave"
                                x-transition:leave-start="service-custom-menu-leave-start"
                                x-transition:leave-end="service-custom-menu-leave-end">
                                <button type="button" class="service-custom-option"
                                    :class="{ 'is-active': overtimePer === '15 min' }"
                                    @click="overtimePer = '15 min'; open = false">
                                    <span>15 min</span>
                                </button>
                                <button type="button" class="service-custom-option"
                                    :class="{ 'is-active': overtimePer === '30 min' }"
                                    @click="overtimePer = '30 min'; open = false">
                                    <span>30 min</span>
                                </button>
                                <button type="button" class="service-custom-option"
                                    :class="{ 'is-active': overtimePer === '45 min' }"
                                    @click="overtimePer = '45 min'; open = false">
                                    <span>45 min</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </label>
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

    .service-chip-row {
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 5rem;
    }

    .service-chip-row>div>div {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 1rem 0;
        flex-wrap: wrap;
    }

    .service-chip-row>div>span {
        display: inline-block;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 1rem 0 0;
    }

    .service-chip {
        min-width: 103px;
        max-width: fit-content;
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        border: none;
        border-radius: 22px;
        background: #F7F7F7;
        color: #D4D4D4;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        padding: 0.5rem 1.5rem;
        cursor: pointer;

    }

    .service-chip.is-active {
        background: rgba(216, 232, 183, 0.20);
        color: #A4C560;
    }

    .service-chip-icon {
        color: #D4D4D4;
        flex-shrink: 0;
    }

    .service-chip.is-active .service-chip-icon {
        color: #A4C560;
    }

    .service-chip-tick {
        display: none;
        flex-shrink: 0;
    }

    .service-chip.is-active .service-chip-tick {
        display: inline-block;
    }

    .service-price-top-row {
        display: flex;
        align-items: flex-end;
        gap: 1.5rem;
        margin: 1rem 0;
    }

    .service-price-advanced-wrap {
        padding-bottom: 0.45rem;
    }

    .service-price-advanced-btn {
        border: 0;
        background: transparent;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        cursor: pointer;
        padding: 0;
    }

    .service-price-advanced-btn span:last-child {
        text-decoration-line: underline;
        text-underline-offset: 4px;
    }

    .service-overtime-wrap {
        width: fit-content;
        padding: 1rem;
        border-radius: 10px;
        background: #FAFAFA;
    }

    .service-overtime-inline {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        width: fit-content;
    }

    .service-overtime-inline input {
        width: 64px;
        height: 42px;
        border-radius: 10px;
        border: 1px solid #DDD;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        padding: 0 0.7rem;
    }

    .service-overtime-per-text {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
    }

    .service-custom-select-overtime,
    .service-custom-select-overtime .service-custom-trigger {
        width: 145px;
    }

    .service-number-input-wrap {
        position: relative;
        width: 100%;
    }

    .service-number-input-wrap input[type="number"] {
        width: 100%;
        padding-right: 1.5rem;
        -moz-appearance: textfield;
    }

    .service-number-input-wrap-currency::before {
        content: "£";
        position: absolute;
        left: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        pointer-events: none;
    }

    .service-number-input-wrap-currency input[type="number"] {
        padding-left: 1.45rem;
    }

    .service-number-input-wrap input[type="number"]::-webkit-outer-spin-button,
    .service-number-input-wrap input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .service-number-input-controls {
        position: absolute;
        top: 50%;
        right: 0.7rem;
        transform: translateY(-50%);
        display: flex;
        flex-direction: column;
        gap: 0.7rem;
    }

    .service-number-step-btn {
        border: 0;
        background: transparent;
        cursor: pointer;
        width: 12px;
        height: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .service-number-input-wrap-compact {
        width: 64px;
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
        transition: left 0.24s ease;
    }

    .service-toggle.is-on {
        background: #c7d59f;
    }

    .service-toggle.is-on::after {
        left: 28px;
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
