<?php

use App\Models\GroomerSpacerProfile;
use App\Models\PetPreference;
use Livewire\Volt\Component;

new class extends Component {
    public array $petTypes = ['cat', 'other'];
    public array $petSizes = ['small', 'medium'];
    public string $otherPets = '';
    public string $otherPetInput = '';
    public bool $editTypes = false;
    public bool $editSizes = false;

    public function mount(): void
    {
        $this->loadPreference();
    }

    private function getProfileId(): ?int
    {
        $email = (string) data_get(auth()->user(), 'email', '');
        $profile = GroomerSpacerProfile::where('email', $email)->first();

        return $profile?->id;
    }

    private function loadPreference(): void
    {
        $profileId = $this->getProfileId();
        if (!$profileId) {
            return;
        }

        $preference = PetPreference::where('groomer_spacer_id', $profileId)->first();
        $compatibility = (array) data_get($preference, 'pet_compatibility', []);

        $types = data_get($compatibility, 'pet_types', []);
        $sizes = data_get($compatibility, 'pet_sizes', []);
        $others = data_get($compatibility, 'other_pets', []);

        $this->petTypes = is_array($types) && !empty($types) ? array_values($types) : ['cat', 'other'];
        $this->petSizes = is_array($sizes) && !empty($sizes) ? array_values($sizes) : ['small', 'medium'];
        $this->otherPets = is_array($others) ? implode(', ', $others) : (string) $others;
    }

    private function upsertPreference(array $payload): void
    {
        $profileId = $this->getProfileId();
        if (!$profileId) {
            return;
        }

        $existing = PetPreference::where('groomer_spacer_id', $profileId)->first();
        $compatibility = (array) data_get($existing, 'pet_compatibility', []);

        PetPreference::updateOrCreate(['groomer_spacer_id' => $profileId], ['pet_compatibility' => array_merge($compatibility, $payload)]);
    }

    public function toggleType(string $type): void
    {
        if (!$this->editTypes) {
            return;
        }

        if (in_array($type, $this->petTypes, true)) {
            $this->petTypes = array_values(array_filter($this->petTypes, fn($item) => $item !== $type));
            return;
        }

        $this->petTypes[] = $type;
    }

    public function toggleSize(string $size): void
    {
        if (!$this->editSizes) {
            return;
        }

        if (in_array($size, $this->petSizes, true)) {
            $this->petSizes = array_values(array_filter($this->petSizes, fn($item) => $item !== $size));
            return;
        }

        $this->petSizes[] = $size;
    }

    public function saveTypePreferences(array $otherPets = []): void
    {
        $normalizedOtherPets = array_values(array_unique(array_filter(array_map(fn($item) => trim((string) $item), $otherPets))));
        $this->otherPets = implode(', ', $normalizedOtherPets);
        $this->otherPetInput = '';

        $this->upsertPreference([
            'pet_types' => array_values($this->petTypes),
            'other_pets' => $normalizedOtherPets,
        ]);

        $this->editTypes = false;
    }

    public function cancelTypePreferences(): void
    {
        $this->loadPreference();
        $this->otherPetInput = '';
        $this->editTypes = false;
    }

    public function saveSizePreferences(): void
    {
        $this->upsertPreference([
            'pet_sizes' => array_values($this->petSizes),
        ]);

        $this->editSizes = false;
    }

    public function cancelSizePreferences(): void
    {
        $this->loadPreference();
        $this->editSizes = false;
    }

    public function getOtherPetsListProperty(): array
    {
        return array_values(array_filter(array_map('trim', explode(',', $this->otherPets))));
    }

    public function addOtherPet(?string $value = null): void
    {
        $candidate = trim($value ?? $this->otherPetInput);
        if ($candidate === '') {
            return;
        }

        $list = $this->otherPetsList;
        $list = array_values(array_filter($list, fn($item) => strcasecmp($item, $candidate) !== 0));
        $list[] = $candidate;
        $this->otherPets = implode(', ', $list);
        $this->otherPetInput = '';
    }

    public function removeOtherPet(int $index): void
    {
        $list = $this->otherPetsList;
        if (!isset($list[$index])) {
            return;
        }

        unset($list[$index]);
        $this->otherPets = implode(', ', array_values($list));
    }
}; ?>

<section class="pet-preferences-wrapper">
    <div class="pet-preferences-grid">
        <article class="pet-preferences-card" x-data="{
            editTypes: @entangle('editTypes').live,
            petTypes: @entangle('petTypes').live,
            otherPetsText: @entangle('otherPets').live,
            otherInput: @entangle('otherPetInput').live,
            otherList: [],
            removingKeys: [],
            originalPetTypes: [],
            originalOtherPetsText: '',
            toggleType(type) {
                if (!this.editTypes) return;
                if (this.petTypes.includes(type)) {
                    this.petTypes = this.petTypes.filter((item) => item !== type);
                } else {
                    this.petTypes.push(type);
                }
            },
            openTypesEdit() {
                this.originalPetTypes = [...this.petTypes];
                this.originalOtherPetsText = this.otherPetsText;
                this.otherList = this.otherPetsText
                    .split(',')
                    .map((item) => item.trim())
                    .filter(Boolean);
                this.editTypes = true;
            },
            cancelTypesEdit() {
                this.petTypes = [...this.originalPetTypes];
                this.otherPetsText = this.originalOtherPetsText;
                this.otherInput = '';
                this.otherList = this.otherPetsText
                    .split(',')
                    .map((item) => item.trim())
                    .filter(Boolean);
                this.removingKeys = [];
                this.editTypes = false;
            },
            addByEnter() {
                const candidate = this.otherInput.trim();
                if (!candidate) return;
                this.otherList = this.otherList.filter((item) => item.toLowerCase() !== candidate.toLowerCase());
                this.otherList.push(candidate);
                this.otherInput = '';
            },
            removeAt(index) {
                if (!this.otherList[index]) return;
                const candidate = this.otherList[index];
                const removeKey = `${candidate}-${index}`;
                this.removingKeys.push(removeKey);
                setTimeout(() => {
                    const targetIndex = this.otherList.findIndex((item) => item.toLowerCase() === candidate.toLowerCase());
                    if (targetIndex !== -1) {
                        this.otherList.splice(targetIndex, 1);
                    }
                    this.removingKeys = this.removingKeys.filter((key) => key !== removeKey);
                }, 190);
            },
        }" :class="{ 'is-editing': editTypes }"
            @keydown.escape.window="if (editTypes) { cancelTypesEdit() }">
            <div class="pet-preferences-card-head">
                <h4>Pet Types Accepted</h4>
                <button type="button" class="pet-pref-edit-btn" x-show="!editTypes"
                    x-transition:enter="pet-pref-fade-enter" x-transition:enter-start="pet-pref-fade-enter-start"
                    x-transition:enter-end="pet-pref-fade-enter-end" x-transition:leave="pet-pref-fade-leave"
                    x-transition:leave-start="pet-pref-fade-leave-start"
                    x-transition:leave-end="pet-pref-fade-leave-end" @click="openTypesEdit()"
                    aria-label="Edit pet types">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16"
                        fill="none">
                        <path
                            d="M10.8529 2.51425L13.6765 5.29691M8.97059 15.5H16.5M1.44118 11.7898L0.5 15.5L4.26471 14.5724L15.1692 3.82581C15.5221 3.47793 15.7203 3.00616 15.7203 2.51425C15.7203 2.02234 15.5221 1.55057 15.1692 1.20269L15.0073 1.04315C14.6543 0.695371 14.1756 0.5 13.6765 0.5C13.1773 0.5 12.6986 0.695371 12.3456 1.04315L1.44118 11.7898Z"
                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <p class="pet-pref-sub-label" x-show="editTypes" x-cloak x-transition:enter="pet-pref-slide-enter"
                x-transition:enter-start="pet-pref-slide-enter-start" x-transition:enter-end="pet-pref-slide-enter-end"
                x-transition:leave="pet-pref-slide-leave" x-transition:leave-start="pet-pref-slide-leave-start"
                x-transition:leave-end="pet-pref-slide-leave-end">
                Select Pet Specialty:
            </p>
            <div class="service-chip-row">
                @foreach (['cat' => 'Cat', 'other' => 'Other', 'dog' => 'Dog'] as $key => $label)
                    <button type="button" class="service-chip has-pet-icon"
                        :class="{ 'is-active': petTypes.includes('{{ $key }}') }"
                        @click="toggleType('{{ $key }}')">
                        <span class="pref-chip-check-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="10" viewBox="0 0 14 10"
                                fill="none">
                                <path
                                    d="M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z"
                                    fill="#FDFCF8" />
                            </svg>
                        </span>
                        <span class="pref-chip-label">{{ $label }}</span>
                        <span class="pref-chip-end-icon">
                            @if ($key === 'cat')
                                <svg class="service-chip-icon" xmlns="http://www.w3.org/2000/svg" width="14"
                                    height="20" viewBox="0 0 14 20" fill="none" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.54981 3.32031C7.25466 3.2334 8.23074 3.25555 9.16407 3.57129C10.1961 3.92045 11.1929 4.63819 11.6807 5.96875L13.6914 6.93262L13.7354 7.08984C14.0021 8.05153 14.1602 9.54554 13.7471 10.9053C13.5389 11.5904 13.1836 12.2495 12.6162 12.7832C12.0474 13.3182 11.2824 13.7115 10.2832 13.8896C6.68798 14.5308 4.64839 17.6058 4.08595 19.0576C3.85458 19.7189 2.48197 19.8865 2.15919 19.2646C-2.66183 9.97639 1.67491 2.64266 4.66798 0L6.54981 3.32031ZM8.77735 6.68164C8.24975 6.68164 7.68946 6.94374 7.68946 7.99121C7.68948 8.7143 8.61152 7.99121 9.21192 7.99121C9.81222 7.9913 9.86427 8.71425 9.86427 7.99121C9.86427 7.26825 9.37758 6.6819 8.77735 6.68164Z"
                                        fill="currentColor" />
                                </svg>
                            @elseif($key === 'other')
                                <svg class="service-chip-icon" xmlns="http://www.w3.org/2000/svg" width="21"
                                    height="17" viewBox="0 0 21 17" fill="none" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.75 0C6.0075 0 5.4255 0.465182 5.0655 1.02309C4.701 1.58564 4.5 2.31973 4.5 3.09091C4.5 3.86209 4.701 4.59618 5.0655 5.15873C5.4255 5.71509 6.0075 6.18182 6.75 6.18182C7.4925 6.18182 8.0745 5.71664 8.4345 5.15873C8.799 4.59618 9 3.86209 9 3.09091C9 2.31973 8.799 1.58564 8.4345 1.02309C8.0745 0.466727 7.4925 0 6.75 0ZM14.25 0C13.5075 0 12.9255 0.465182 12.5655 1.02309C12.201 1.58564 12 2.31973 12 3.09091C12 3.86209 12.201 4.59618 12.5655 5.15873C12.9255 5.71509 13.5075 6.18182 14.25 6.18182C14.9925 6.18182 15.5745 5.71664 15.9345 5.15873C16.299 4.59618 16.5 3.86209 16.5 3.09091C16.5 2.31973 16.299 1.58564 15.9345 1.02309C15.5745 0.466727 14.9925 0 14.25 0ZM2.25 6.95455C1.5075 6.95455 0.9255 7.41973 0.5655 7.97764C0.201 8.54018 0 9.27427 0 10.0455C0 10.8166 0.201 11.5507 0.5655 12.1133C0.9255 12.6696 1.5075 13.1364 2.25 13.1364C2.9925 13.1364 3.5745 12.6712 3.9345 12.1133C4.299 11.5507 4.5 10.8166 4.5 10.0455C4.5 9.27427 4.299 8.54018 3.9345 7.97764C3.5745 7.42127 2.9925 6.95455 2.25 6.95455ZM10.5 6.95455C8.7 6.95455 7.3665 7.94982 6.5145 9.18464C5.673 10.4009 5.25 11.9108 5.25 13.1364C5.25 14.5644 6.0825 15.5581 7.104 16.1531C8.109 16.7404 9.369 17 10.5 17C11.631 17 12.891 16.7419 13.896 16.1531C14.916 15.5565 15.75 14.5644 15.75 13.1364C15.75 11.9108 15.327 10.4009 14.4855 9.18464C13.635 7.94827 12.3015 6.95455 10.5 6.95455ZM18.75 6.95455C18.0075 6.95455 17.4255 7.41973 17.0655 7.97764C16.701 8.54018 16.5 9.27427 16.5 10.0455C16.5 10.8166 16.701 11.5507 17.0655 12.1133C17.4255 12.6696 18.0075 13.1364 18.75 13.1364C19.4925 13.1364 20.0745 12.6712 20.4345 12.1133C20.799 11.5507 21 10.8166 21 10.0455C21 9.27427 20.799 8.54018 20.4345 7.97764C20.0745 7.42127 19.4925 6.95455 18.75 6.95455Z"
                                        fill="currentColor" />
                                </svg>
                            @elseif($key === 'dog')
                                <svg class="service-chip-icon" xmlns="http://www.w3.org/2000/svg" width="20"
                                    height="19" viewBox="0 0 20 19" fill="none" aria-hidden="true">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M10.0941 8.70252e-10C10.7113 -1.81926e-05 11.2943 0.285229 11.6732 0.772461L14.4515 4.34473C14.6106 4.54926 14.8434 4.68405 15.0999 4.7207L17.7464 5.09863C18.1045 5.14979 18.4075 5.39029 18.5189 5.73438C18.8051 6.61821 19.2752 8.35348 18.9417 9.37109C18.5358 10.6085 17.8843 11.2276 16.7103 11.5303C14.6994 12.0487 13.0373 11.4562 11.0726 12.9541C10.53 13.3678 10.1419 13.8958 9.87143 14.4863C8.80526 16.8134 5.84433 19.2963 3.63803 17.999C2.21377 17.1611 1.5216 15.4772 1.94468 13.8799L2.54135 11.624C2.68005 11.6145 2.81557 11.6042 2.94468 11.5879C3.30448 11.5425 3.65539 11.4642 3.88706 11.3174C4.07261 11.1997 4.26051 10.9971 4.43686 10.7715C4.6171 10.5409 4.80106 10.2654 4.97495 9.98145C5.32278 9.41333 5.64148 8.79555 5.82944 8.39844C5.8885 8.27365 5.83506 8.12451 5.7103 8.06543C5.58561 8.00644 5.43645 8.05903 5.37729 8.18359C5.19516 8.56842 4.88485 9.17086 4.54819 9.7207C4.3799 9.99556 4.20757 10.2537 4.04331 10.4639C3.87527 10.6788 3.72991 10.8244 3.61948 10.8945C3.48843 10.9776 3.23457 11.0473 2.88218 11.0918C2.54069 11.1349 2.14164 11.1512 1.74839 11.1504C1.36053 11.1496 0.983727 11.129 0.681003 11.1074C0.194785 10.8871 -0.103935 10.3229 0.0335424 9.79883C1.33099 4.85932 2.07831 2.59895 3.33335 1.34375C4.67684 0.000287295 7.36362 8.70252e-10 7.36362 8.70252e-10H10.0941ZM10.5492 4.47949C9.98084 4.47949 9.37838 4.76142 9.37827 5.88965C9.37827 6.66872 10.3711 5.88965 11.0179 5.88965C11.6644 5.88994 11.7201 6.66857 11.7201 5.88965C11.7199 5.11076 11.1959 4.47955 10.5492 4.47949Z"
                                        fill="currentColor" />
                                </svg>
                            @else
                                <span class="pref-chip-end-placeholder"></span>
                            @endif
                        </span>
                    </button>
                @endforeach
            </div>

            <label class="pet-pref-other-wrap">
                <span>
                    <template x-if="editTypes">
                        <span>Other <span class="pet-pref-muted-note">(Please specify)</span></span>
                    </template>
                    <template x-if="!editTypes">
                        <span>Other includes:</span>
                    </template>
                </span>
                <div x-show="editTypes" x-cloak x-transition:enter="pet-pref-slide-enter"
                    x-transition:enter-start="pet-pref-slide-enter-start"
                    x-transition:enter-end="pet-pref-slide-enter-end" x-transition:leave="pet-pref-slide-leave"
                    x-transition:leave-start="pet-pref-slide-leave-start"
                    x-transition:leave-end="pet-pref-slide-leave-end">
                    <div class="pet-pref-other-input-row">
                        <input type="text" x-model="otherInput" @keydown.enter.prevent="addByEnter()"
                            placeholder="e.g., Luxury grooming with a gentle touch." />
                    </div>
                </div>
                <p x-show="!editTypes" x-cloak x-text="otherPetsText !== '' ? `${otherPetsText}.` : '-'"></p>
            </label>

            <div x-show="editTypes" x-cloak x-transition:enter="pet-pref-slide-enter"
                x-transition:enter-start="pet-pref-slide-enter-start" x-transition:enter-end="pet-pref-slide-enter-end"
                x-transition:leave="pet-pref-slide-leave" x-transition:leave-start="pet-pref-slide-leave-start"
                x-transition:leave-end="pet-pref-slide-leave-end">
                <div class="pet-pref-list-wrap">
                    <template x-if="otherList.length === 0">
                        <p class="pet-pref-empty">No custom pets added yet.</p>
                    </template>
                    <template x-for="(item, index) in otherList" :key="`${item}-${index}`">
                        <div class="pet-pref-list-row"
                            :class="{ 'is-removing': removingKeys.includes(`${item}-${index}`) }"
                            x-transition:enter="pet-pref-transition-enter"
                            x-transition:enter-start="pet-pref-transition-enter-start"
                            x-transition:enter-end="pet-pref-transition-enter-end"
                            x-transition:leave="pet-pref-transition-leave"
                            x-transition:leave-start="pet-pref-transition-leave-start"
                            x-transition:leave-end="pet-pref-transition-leave-end">
                            <span x-text="`${index + 1}. ${item}`"></span>
                            <button type="button" @click="removeAt(index)"><svg xmlns="http://www.w3.org/2000/svg"
                                    width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M0.75 10.75L10.75 0.75M0.75 0.75L10.75 10.75" stroke="#9D9B98"
                                        stroke-width="1.5" stroke-linecap="round" />
                                </svg></button>
                        </div>
                    </template>
                </div>
                <button type="button" class="pet-pref-save-btn" @click="$wire.saveTypePreferences(otherList)"
                    wire:loading.attr="disabled" wire:target="saveTypePreferences">
                    <span wire:loading.class="hidden" wire:target="saveTypePreferences">Save Changes</span>
                    <span class="pet-pref-save-loading hidden" wire:loading.class.remove="hidden"
                        wire:target="saveTypePreferences">
                        <span class="pet-pref-save-spinner"></span>
                    </span>
                </button>
            </div>
        </article>

        <article class="pet-preferences-card" style="max-height: fit-content;min-height: 200px;"
            x-data="{
                editSizes: @entangle('editSizes').live,
                petSizes: @entangle('petSizes').live,
                originalPetSizes: [],
                openSizesEdit() {
                    this.originalPetSizes = [...this.petSizes];
                    this.editSizes = true;
                },
                cancelSizesEdit() {
                    this.petSizes = [...this.originalPetSizes];
                    this.editSizes = false;
                },
                toggleSize(size) {
                    if (!this.editSizes) return;
                    if (this.petSizes.includes(size)) {
                        this.petSizes = this.petSizes.filter((item) => item !== size);
                    } else {
                        this.petSizes.push(size);
                    }
                },
            }" :class="{ 'is-editing': editSizes }"
            @keydown.escape.window="if (editSizes) { cancelSizesEdit() }">
            <div class="pet-preferences-card-head">
                <h4>Pet Size Accepted</h4>
                <button type="button" class="pet-pref-edit-btn" x-show="!editSizes"
                    x-transition:enter="pet-pref-fade-enter" x-transition:enter-start="pet-pref-fade-enter-start"
                    x-transition:enter-end="pet-pref-fade-enter-end" x-transition:leave="pet-pref-fade-leave"
                    x-transition:leave-start="pet-pref-fade-leave-start"
                    x-transition:leave-end="pet-pref-fade-leave-end" @click="openSizesEdit()"
                    aria-label="Edit pet sizes">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="16" viewBox="0 0 17 16"
                        fill="none">
                        <path
                            d="M10.8529 2.51425L13.6765 5.29691M8.97059 15.5H16.5M1.44118 11.7898L0.5 15.5L4.26471 14.5724L15.1692 3.82581C15.5221 3.47793 15.7203 3.00616 15.7203 2.51425C15.7203 2.02234 15.5221 1.55057 15.1692 1.20269L15.0073 1.04315C14.6543 0.695371 14.1756 0.5 13.6765 0.5C13.1773 0.5 12.6986 0.695371 12.3456 1.04315L1.44118 11.7898Z"
                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <p class="pet-pref-sub-label" x-show="editSizes" x-cloak x-transition:enter="pet-pref-slide-enter"
                x-transition:enter-start="pet-pref-slide-enter-start"
                x-transition:enter-end="pet-pref-slide-enter-end" x-transition:leave="pet-pref-slide-leave"
                x-transition:leave-start="pet-pref-slide-leave-start"
                x-transition:leave-end="pet-pref-slide-leave-end">
                Select Pet Size:
            </p>
            <div class="service-chip-row">
                @foreach (['small' => 'Small 0-7 kg', 'medium' => 'Medium 8-18 kg', 'large' => 'Large 19+ kg'] as $key => $label)
                    <button type="button" class="service-chip size-chip"
                        :class="{ 'is-active': petSizes.includes('{{ $key }}') }"
                        @click="toggleSize('{{ $key }}')">
                        <span class="pref-chip-check-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="10"
                                viewBox="0 0 14 10" fill="none">
                                <path
                                    d="M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z"
                                    fill="#FDFCF8" />
                            </svg>
                        </span>
                        <span class="pref-chip-label">{{ $label }}</span>
                        <span class="pref-chip-end-placeholder"></span>
                    </button>
                @endforeach
            </div>

            <button type="button" class="pet-pref-save-btn" x-show="editSizes" x-cloak
                x-transition:enter="pet-pref-slide-enter" x-transition:enter-start="pet-pref-slide-enter-start"
                x-transition:enter-end="pet-pref-slide-enter-end" x-transition:leave="pet-pref-slide-leave"
                x-transition:leave-start="pet-pref-slide-leave-start"
                x-transition:leave-end="pet-pref-slide-leave-end" wire:click="saveSizePreferences"
                wire:loading.attr="disabled" wire:target="saveSizePreferences">
                <span wire:loading.class="hidden" wire:target="saveSizePreferences">Save Changes</span>
                <span class="pet-pref-save-loading hidden" wire:loading.class.remove="hidden"
                    wire:target="saveSizePreferences">
                    <span class="pet-pref-save-spinner"></span>
                </span>
            </button>
        </article>
    </div>
</section>

<style>
    .pet-preferences-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1rem;
    }

    .pet-preferences-card {
        background: #FAFAFA;
        border-radius: 12px;
        padding: 1.15rem;
        transition: box-shadow 240ms ease;
    }

    .pet-preferences-card.is-editing {
        box-shadow: 0 8px 24px rgba(59, 55, 49, 0.08);
    }

    .pet-pref-fade-enter {
        transition: opacity 180ms ease, transform 180ms ease;
    }

    .pet-pref-fade-enter-start {
        opacity: 0;
        transform: scale(0.92);
    }

    .pet-pref-fade-enter-end {
        opacity: 1;
        transform: scale(1);
    }

    .pet-pref-fade-leave {
        transition: opacity 140ms ease, transform 140ms ease;
    }

    .pet-pref-fade-leave-start {
        opacity: 1;
        transform: scale(1);
    }

    .pet-pref-fade-leave-end {
        opacity: 0;
        transform: scale(0.92);
    }

    .pet-pref-slide-enter {
        transition: opacity 220ms ease, transform 220ms ease;
    }

    .pet-pref-slide-enter-start {
        opacity: 0;
        transform: translateY(8px);
    }

    .pet-pref-slide-enter-end {
        opacity: 1;
        transform: translateY(0);
    }

    .pet-pref-slide-leave {
        transition: opacity 180ms ease, transform 180ms ease;
    }

    .pet-pref-slide-leave-start {
        opacity: 1;
        transform: translateY(0);
    }

    .pet-pref-slide-leave-end {
        opacity: 0;
        transform: translateY(-6px);
    }

    .pet-preferences-card-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        margin-bottom: 0.9rem;
    }

    .pet-preferences-card-head h4 {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .pet-pref-sub-label {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .pet-pref-edit-btn {
        border: 0;
        background: transparent;
        color: #7f7b76;
        font-family: Lato;
        font-size: 14px;
        cursor: pointer;
    }

    .service-chip-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 1.5rem 0;
    }

    .service-chip {
        min-width: 103px;
        max-width: fit-content;
        height: 48px;
        display: grid;
        grid-template-columns: 16px 1fr 20px;
        align-items: center;
        justify-items: center;
        gap: 0.35rem;
        border-radius: 96px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        color: #D4D4D4;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 1;
        padding: 0 1.5rem;
        cursor: pointer;
        transition: background-color 220ms ease, color 220ms ease, border-color 220ms ease, transform 180ms ease,
            box-shadow 220ms ease;
        will-change: transform;
    }

    .service-chip.is-active {
        background: rgba(216, 232, 183, 0.20);
        color: #A4C560;
        border: none;
        transform: translateY(-1px) scale(1.02);
    }

    .service-chip:active {
        transform: scale(0.98);
    }

    .pref-chip-label {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 100%;
        text-align: center;
        color: inherit;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
    }

    .service-chip.is-active .pref-chip-label {
        color: #A4C560;
    }

    .pet-preferences-card.is-editing .service-chip.is-active .pref-chip-label {
        color: #FDFCF8;
    }

    .pref-chip-check-icon {
        width: 14px;
        height: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        opacity: 0;
        transform: scale(0.6);
        transition: opacity 180ms ease, transform 180ms ease, width 180ms ease;
    }

    .pref-chip-check-icon svg,
    .service-chip-icon {
        display: block;
    }

    .service-chip.is-active .pref-chip-check-icon {
        opacity: 1;
        transform: scale(1);
    }

    .pref-chip-end-icon,
    .pref-chip-end-placeholder {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pet-preferences-card:not(.is-editing) .service-chip {
        grid-template-columns: 1fr;
        gap: 0;
        padding-right: 1.5rem;
        padding-left: 1.5rem;
    }

    .pet-preferences-card:not(.is-editing) .service-chip.has-pet-icon {
        grid-template-columns: 20px 1fr;
        gap: 0.45rem;
        padding-right: 1.2rem;
        padding-left: 0.95rem;
    }

    .pet-preferences-card:not(.is-editing) .pref-chip-end-icon {
        display: inline-flex;
        grid-column: 1;
        grid-row: 1;
    }

    .pet-preferences-card:not(.is-editing) .pref-chip-end-placeholder {
        display: none;
    }

    .pet-preferences-card:not(.is-editing) .pref-chip-label {
        grid-column: 1;
        grid-row: 1;
        text-align: center;
    }

    .pet-preferences-card:not(.is-editing) .service-chip.has-pet-icon .pref-chip-label {
        grid-column: 2;
    }

    .service-chip-icon {
        color: #D4D4D4;
        flex-shrink: 0;
        transition: color 220ms ease, transform 220ms ease;
    }

    .service-chip.is-active .service-chip-icon {
        color: #A4C560;
        transform: scale(1.06);
    }

    .pet-preferences-card:not(.is-editing) .pref-chip-check-icon {
        display: none;
    }

    .pet-preferences-card.is-editing .service-chip:not(.is-active) {
        background: #FFF;
        color: #D4D4D4;
        border: 1px solid #E2E2E2;
    }

    .pet-preferences-card.is-editing .service-chip:not(.is-active) .service-chip-icon {
        color: #D4D4D4;
        transform: none;
    }

    .pet-preferences-card.is-editing .service-chip.is-active {
        border-radius: 96px;
        background: #FFC97A;
        color: #FDFCF8;
        border: none;
        transform: translateY(-1px) scale(1.02);
    }

    .pet-preferences-card.is-editing .service-chip.is-active .service-chip-icon {
        color: #FDFCF8;
        transform: scale(1.06);
    }

    .pet-pref-other-wrap {
        display: block;
        margin-top: 1rem;
    }

    .pet-pref-other-wrap span {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .pet-pref-other-wrap p {
        margin: 0.3rem 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize
    }

    .pet-pref-muted-note {
        color: #9D9B98;
    }

    .pet-pref-other-wrap input {
        width: 100%;
        height: 48px;
        margin-top: 1rem;
        border: 1px solid #D4D4D4;
        border-radius: 10px;
        padding: 0 0.75rem;
    }

    .pet-pref-other-input-row {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .pet-pref-other-wrap input::placeholder {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .pet-pref-save-btn {
        justify-content: center;
        align-items: center;
        border: none;
        border-radius: 100px;
        background: #BACF8E;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        width: 154px;
        height: 37px;
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-top: 1rem;
        cursor: pointer;
        display: block;
        margin-left: auto;
    }

    .pet-pref-save-loading {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 90px;
    }

    .pet-pref-save-spinner {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        animation: pet-pref-save-spin 0.8s linear infinite;
    }

    @keyframes pet-pref-save-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .hidden {
        display: none !important;
    }

    .pet-pref-list-wrap {
        margin-top: 0.65rem;
    }

    .pet-pref-list-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 75px;
        border-bottom: 1px solid #D4D4D4;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
        padding: 0 2rem;
    }

    .pet-pref-edit-reveal {
        animation: pet-pref-edit-reveal 240ms ease-out;
    }

    .pet-pref-row-enter {
        animation: pet-pref-row-pop 240ms ease-out;
    }

    .pet-pref-transition-enter {
        transition: all 220ms ease;
    }

    .pet-pref-transition-enter-start {
        opacity: 0;
        transform: translateY(8px) scale(0.98);
    }

    .pet-pref-transition-enter-end {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .pet-pref-transition-leave {
        transition: all 180ms ease;
    }

    .pet-pref-transition-leave-start {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .pet-pref-transition-leave-end {
        opacity: 0;
        transform: translateY(-4px) scale(0.98);
    }

    @keyframes pet-pref-edit-reveal {
        from {
            opacity: 0;
            transform: translateY(6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pet-pref-row-pop {
        0% {
            opacity: 0;
            transform: translateY(8px) scale(0.98);
        }

        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .pet-pref-list-row:last-child {
        border: none;
    }

    .pet-pref-list-row.is-removing {
        opacity: 0;
        transform: translateY(-4px) scale(0.98);
        transition: all 180ms ease;
        pointer-events: none;
    }

    .pet-pref-list-row button {
        border: 0;
        background: transparent;
        color: #9d9b98;
        font-size: 28px;
        line-height: 1;
        cursor: pointer;
    }

    .pet-pref-empty {
        margin: 0.6rem 0;
        color: #9d9b98;
        font-family: Lato;
    }
</style>
