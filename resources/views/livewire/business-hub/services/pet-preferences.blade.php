<?php

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
        $id = auth('groomer_spacer')->id();

        return $id ? (int) $id : null;
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
        if ($otherPets === []) {
            $otherPets = array_values(array_filter(array_map('trim', explode(',', $this->otherPets))));
        }

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
            init() {
                this.syncListFromText();
                this.$watch('otherPetsText', () => {
                    if (this.editTypes) {
                        return;
                    }
                    this.syncListFromText();
                });
            },
            syncListFromText(value = this.otherPetsText) {
                this.otherList = String(value || '')
                    .split(',')
                    .map((item) => item.trim())
                    .filter(Boolean);
            },
            includesLabel() {
                const items = this.otherList.map((item) => String(item).trim()).filter(Boolean);
                return items.length ? `${items.join(', ')}.` : '-';
            },
            toggleType(type) {
                if (!this.editTypes) return;
                if (this.petTypes.includes(type)) {
                    this.petTypes = this.petTypes.filter((item) => item !== type);
                } else {
                    this.petTypes.push(type);
                }
            },
            openTypesEdit() {
                this.syncListFromText();
                this.originalPetTypes = [...this.petTypes];
                this.originalOtherPetsText = this.otherPetsText;
                this.editTypes = true;
            },
            cancelTypesEdit() {
                this.petTypes = [...this.originalPetTypes];
                this.otherPetsText = this.originalOtherPetsText;
                this.otherInput = '';
                this.syncListFromText(this.originalOtherPetsText);
                this.removingKeys = [];
                this.editTypes = false;
            },
            saveTypes() {
                const items = this.otherList.map((item) => String(item).trim()).filter(Boolean);
                this.otherList = items;
                this.otherPetsText = items.join(', ');
                this.otherInput = '';
                this.editTypes = false;
                this.$wire.saveTypePreferences(items);
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
                <button type="button" class="pet-pref-edit-btn" x-show="!editTypes" x-cloak @click="openTypesEdit()"
                    aria-label="Edit pet types">
                    <img src="{{ asset('images/business-hub/icon-pet-pref-edit.svg') }}" width="36" height="36"
                        alt="" />
                </button>
            </div>

            <div class="pet-pref-chips">
                @foreach (['cat' => 'Cat', 'other' => 'Other', 'dog' => 'Dog'] as $key => $label)
                    <button type="button" class="pet-pref-chip pet-pref-chip--type"
                        :class="{ 'is-active': petTypes.includes('{{ $key }}') }"
                        @click="toggleType('{{ $key }}')">
                        <svg class="pet-pref-chip-tick" xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                            viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C560" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        @if ($key === 'cat')
                            <svg class="pet-pref-chip-icon" xmlns="http://www.w3.org/2000/svg" width="14" height="20"
                                viewBox="0 0 14 20" fill="none" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.54981 3.32031C7.25466 3.2334 8.23074 3.25555 9.16407 3.57129C10.1961 3.92045 11.1929 4.63819 11.6807 5.96875L13.6914 6.93262L13.7354 7.08984C14.0021 8.05153 14.1602 9.54554 13.7471 10.9053C13.5389 11.5904 13.1836 12.2495 12.6162 12.7832C12.0474 13.3182 11.2824 13.7115 10.2832 13.8896C6.68798 14.5308 4.64839 17.6058 4.08595 19.0576C3.85458 19.7189 2.48197 19.8865 2.15919 19.2646C-2.66183 9.97639 1.67491 2.64266 4.66798 0L6.54981 3.32031ZM8.77735 6.68164C8.24975 6.68164 7.68946 6.94374 7.68946 7.99121C7.68948 8.7143 8.61152 7.99121 9.21192 7.99121C9.81222 7.9913 9.86427 8.71425 9.86427 7.99121C9.86427 7.26825 9.37758 6.6819 8.77735 6.68164Z"
                                    fill="currentColor" />
                            </svg>
                        @elseif ($key === 'other')
                            <svg class="pet-pref-chip-icon" xmlns="http://www.w3.org/2000/svg" width="21" height="17"
                                viewBox="0 0 21 17" fill="none" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M6.75 0C6.0075 0 5.4255 0.465182 5.0655 1.02309C4.701 1.58564 4.5 2.31973 4.5 3.09091C4.5 3.86209 4.701 4.59618 5.0655 5.15873C5.4255 5.71509 6.0075 6.18182 6.75 6.18182C7.4925 6.18182 8.0745 5.71664 8.4345 5.15873C8.799 4.59618 9 3.86209 9 3.09091C9 2.31973 8.799 1.58564 8.4345 1.02309C8.0745 0.466727 7.4925 0 6.75 0ZM14.25 0C13.5075 0 12.9255 0.465182 12.5655 1.02309C12.201 1.58564 12 2.31973 12 3.09091C12 3.86209 12.201 4.59618 12.5655 5.15873C12.9255 5.71509 13.5075 6.18182 14.25 6.18182C14.9925 6.18182 15.5745 5.71664 15.9345 5.15873C16.299 4.59618 16.5 3.86209 16.5 3.09091C16.5 2.31973 16.299 1.58564 15.9345 1.02309C15.5745 0.466727 14.9925 0 14.25 0ZM2.25 6.95455C1.5075 6.95455 0.9255 7.41973 0.5655 7.97764C0.201 8.54018 0 9.27427 0 10.0455C0 10.8166 0.201 11.5507 0.5655 12.1133C0.9255 12.6696 1.5075 13.1364 2.25 13.1364C2.9925 13.1364 3.5745 12.6712 3.9345 12.1133C4.299 11.5507 4.5 10.8166 4.5 10.0455C4.5 9.27427 4.299 8.54018 3.9345 7.97764C3.5745 7.42127 2.9925 6.95455 2.25 6.95455ZM10.5 6.95455C8.7 6.95455 7.3665 7.94982 6.5145 9.18464C5.673 10.4009 5.25 11.9108 5.25 13.1364C5.25 14.5644 6.0825 15.5581 7.104 16.1531C8.109 16.7404 9.369 17 10.5 17C11.631 17 12.891 16.7419 13.896 16.1531C14.916 15.5565 15.75 14.5644 15.75 13.1364C15.75 11.9108 15.327 10.4009 14.4855 9.18464C13.635 7.94827 12.3015 6.95455 10.5 6.95455ZM18.75 6.95455C18.0075 6.95455 17.4255 7.41973 17.0655 7.97764C16.701 8.54018 16.5 9.27427 16.5 10.0455C16.5 10.8166 16.701 11.5507 17.0655 12.1133C17.4255 12.6696 18.0075 13.1364 18.75 13.1364C19.4925 13.1364 20.0745 12.6712 20.4345 12.1133C20.799 11.5507 21 10.8166 21 10.0455C21 9.27427 20.799 8.54018 20.4345 7.97764C20.0745 7.42127 19.4925 6.95455 18.75 6.95455Z"
                                    fill="currentColor" />
                            </svg>
                        @else
                            <svg class="pet-pref-chip-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="19"
                                viewBox="0 0 20 19" fill="none" aria-hidden="true">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M10.0941 8.70252e-10C10.7113 -1.81926e-05 11.2943 0.285229 11.6732 0.772461L14.4515 4.34473C14.6106 4.54926 14.8434 4.68405 15.0999 4.7207L17.7464 5.09863C18.1045 5.14979 18.4075 5.39029 18.5189 5.73438C18.8051 6.61821 19.2752 8.35348 18.9417 9.37109C18.5358 10.6085 17.8843 11.2276 16.7103 11.5303C14.6994 12.0487 13.0373 11.4562 11.0726 12.9541C10.53 13.3678 10.1419 13.8958 9.87143 14.4863C8.80526 16.8134 5.84433 19.2963 3.63803 17.999C2.21377 17.1611 1.5216 15.4772 1.94468 13.8799L2.54135 11.624C2.68005 11.6145 2.81557 11.6042 2.94468 11.5879C3.30448 11.5425 3.65539 11.4642 3.88706 11.3174C4.07261 11.1997 4.26051 10.9971 4.43686 10.7715C4.6171 10.5409 4.80106 10.2654 4.97495 9.98145C5.32278 9.41333 5.64148 8.79555 5.82944 8.39844C5.8885 8.27365 5.83506 8.12451 5.7103 8.06543C5.58561 8.00644 5.43645 8.05903 5.37729 8.18359C5.19516 8.56842 4.88485 9.17086 4.54819 9.7207C4.3799 9.99556 4.20757 10.2537 4.04331 10.4639C3.87527 10.6788 3.72991 10.8244 3.61948 10.8945C3.48843 10.9776 3.23457 11.0473 2.88218 11.0918C2.54069 11.1349 2.14164 11.1512 1.74839 11.1504C1.36053 11.1496 0.983727 11.129 0.681003 11.1074C0.194785 10.8871 -0.103935 10.3229 0.0335424 9.79883C1.33099 4.85932 2.07831 2.59895 3.33335 1.34375C4.67684 0.000287295 7.36362 8.70252e-10 7.36362 8.70252e-10H10.0941ZM10.5492 4.47949C9.98084 4.47949 9.37838 4.76142 9.37827 5.88965C9.37827 6.66872 10.3711 5.88965 11.0179 5.88965C11.6644 5.88994 11.7201 6.66857 11.7201 5.88965C11.7199 5.11076 11.1959 4.47955 10.5492 4.47949Z"
                                    fill="currentColor" />
                            </svg>
                        @endif
                        <span>{{ $label }}</span>
                    </button>
                @endforeach
            </div>

            <div class="pet-pref-other-summary" x-show="!editTypes">
                <span>Other includes:</span>
                <p x-text="includesLabel()"></p>
            </div>

            <div class="pet-pref-edit-panel" x-show="editTypes" x-cloak>
                <div class="pet-pref-other-field">
                    <span>Other Pet types</span>
                    <div class="pet-pref-other-input-row">
                        <input type="text" x-model="otherInput" @keydown.enter.prevent="addByEnter()"
                            placeholder="e.g Rabbit, Ferret, Hamster" />
                        <button type="button" class="pet-pref-add-btn" aria-label="Add pet type" @click="addByEnter()">
                            <img src="{{ asset('images/business-hub/icon-other-pet-add.svg') }}" width="64" height="64"
                                alt="" />
                        </button>
                    </div>
                </div>

                <div class="pet-pref-pills" x-show="otherList.length > 0" x-cloak>
                    <template x-for="(item, index) in otherList" :key="`${item}-${index}`">
                        <div class="pet-pref-pill" :class="{ 'is-removing': removingKeys.includes(`${item}-${index}`) }">
                            <span x-text="item"></span>
                            <button type="button" aria-label="Remove pet type" @click="removeAt(index)">
                                <img src="{{ asset('images/business-hub/icon-other-pet-close.svg') }}" width="11.6148"
                                    height="11.6066" alt="" />
                            </button>
                        </div>
                    </template>
                </div>

                <div class="pet-pref-actions">
                    <button type="button" class="pet-pref-cancel-btn" @click="cancelTypesEdit()">Cancel</button>
                    <button type="button" class="pet-pref-save-btn" @click="saveTypes()"
                        wire:loading.attr="disabled" wire:target="saveTypePreferences">
                        <span wire:loading.class="hidden" wire:target="saveTypePreferences">Save Changes</span>
                        <span class="pet-pref-save-loading hidden" wire:loading.class.remove="hidden"
                            wire:target="saveTypePreferences">
                            <span class="pet-pref-save-spinner"></span>
                        </span>
                    </button>
                </div>
            </div>
        </article>

        <article class="pet-preferences-card" x-data="{
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
                <h4>Pet Size Preferences</h4>
                <button type="button" class="pet-pref-edit-btn" x-show="!editSizes" x-cloak @click="openSizesEdit()"
                    aria-label="Edit pet sizes">
                    <img src="{{ asset('images/business-hub/icon-pet-pref-edit.svg') }}" width="36" height="36"
                        alt="" />
                </button>
            </div>

            <div class="pet-pref-chips">
                @foreach (['small' => 'Small 0-7 kg', 'medium' => 'Medium 8-18 kg', 'large' => 'Large 19+ kg'] as $key => $label)
                    <button type="button" class="pet-pref-chip pet-pref-chip--size"
                        :class="{ 'is-active': petSizes.includes('{{ $key }}') }"
                        @click="toggleSize('{{ $key }}')">
                        <svg class="pet-pref-chip-tick" xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                            viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C560" stroke-width="1.5"
                                stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>{{ $label }}</span>
                    </button>
                @endforeach
            </div>

            <div class="pet-pref-actions" x-show="editSizes" x-cloak>
                <button type="button" class="pet-pref-cancel-btn" @click="cancelSizesEdit()">Cancel</button>
                <button type="button" class="pet-pref-save-btn" wire:click="saveSizePreferences"
                    wire:loading.attr="disabled" wire:target="saveSizePreferences">
                    <span wire:loading.class="hidden" wire:target="saveSizePreferences">Save Changes</span>
                    <span class="pet-pref-save-loading hidden" wire:loading.class.remove="hidden"
                        wire:target="saveSizePreferences">
                        <span class="pet-pref-save-spinner"></span>
                    </span>
                </button>
            </div>
        </article>
    </div>
</section>

<style>
    .pet-preferences-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
        align-items: stretch;
    }

    .pet-preferences-card {
        position: relative;
        display: flex;
        flex-direction: column;
        min-height: 195px;
        background: #fdfdfd;
        border-radius: 10px;
        box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
        padding: 20px;
    }

    .pet-preferences-card.is-editing {
        min-height: 355px;
    }

    .pet-preferences-card-head {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 12px;
        min-height: 36px;
    }

    .pet-preferences-card-head h4 {
        margin: 0;
        padding-top: 8px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .pet-pref-edit-btn {
        width: 36px;
        height: 36px;
        margin-top: -10px;
        margin-right: -10px;
        padding: 0;
        border: 0;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        line-height: 0;
    }

    .pet-pref-edit-btn img {
        display: block;
        width: 36px;
        height: 36px;
    }

    .pet-pref-chips {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 20px;
        margin-top: 22px;
    }

    .pet-pref-chip {
        height: 42px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 0;
        border-radius: 22px;
        background: #F7F7F7;
        color: #D4D4D4;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        padding: 0 16px;
        cursor: default;
    }

    .pet-preferences-card.is-editing .pet-pref-chip {
        cursor: pointer;
    }

    .pet-pref-chip--size {
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .pet-pref-chip.is-active {
        background: rgba(216, 232, 183, 0.2);
        color: #A4C560;
    }

    .pet-pref-chip-icon {
        color: currentColor;
        flex-shrink: 0;
    }

    .pet-pref-chip-tick {
        flex-shrink: 0;
        width: 0;
        height: 9px;
        overflow: hidden;
        opacity: 0;
    }

    .pet-preferences-card.is-editing .pet-pref-chip.is-active .pet-pref-chip-tick {
        width: 12px;
        opacity: 1;
    }

    .pet-pref-other-summary {
        margin-top: 20px;
    }

    .pet-pref-other-summary span {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .pet-pref-other-summary p {
        margin: 8px 0 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
    }

    .pet-pref-edit-panel {
        display: flex;
        flex-direction: column;
        flex: 1;
    }

    .pet-pref-other-field {
        display: flex;
        flex-direction: column;
        gap: 13px;
        margin-top: 20px;
    }

    .pet-pref-other-field>span {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .pet-pref-other-input-row {
        display: flex;
        align-items: flex-start;
        gap: 20px;
    }

    .pet-pref-other-input-row input {
        width: 332px;
        max-width: 100%;
        height: 48px;
        border: 1px solid #D4D4D4;
        border-radius: 10px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        padding: 0 20px;
    }

    .pet-pref-other-input-row input::placeholder {
        color: #9D9B98;
    }

    .pet-pref-add-btn {
        position: relative;
        width: 48px;
        height: 48px;
        border: 0;
        background: transparent;
        padding: 0;
        cursor: pointer;
        flex-shrink: 0;
        overflow: visible;
        line-height: 0;
    }

    .pet-pref-add-btn img {
        position: absolute;
        left: -8px;
        top: -3px;
        pointer-events: none;
    }

    .pet-pref-pills {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 20px;
        margin-top: 20px;
    }

    .pet-pref-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        min-width: 112px;
        height: 42px;
        padding: 0 16px 0 20px;
        border-radius: 22px;
        background: #ededed;
        color: #615a51;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: 25px;
        text-transform: capitalize;
    }

    .pet-pref-pill.is-removing {
        opacity: 0;
        transform: scale(0.94);
        pointer-events: none;
    }

    .pet-pref-pill button {
        border: 0;
        background: transparent;
        padding: 0;
        width: 12px;
        height: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        line-height: 0;
        flex-shrink: 0;
    }

    .pet-pref-pill button img {
        display: block;
    }

    .pet-pref-actions {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        margin-top: auto;
        padding-top: 40px;
    }

    .pet-pref-cancel-btn,
    .pet-pref-save-btn {
        width: 138px;
        height: 42px;
        border-radius: 100px;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-align: center;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pet-pref-cancel-btn {
        background: #fff;
        border: 1px solid #D9D9D9;
        color: #9D9B98;
    }

    .pet-pref-save-btn {
        border: 0;
        background: #BACF8E;
        box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.1);
        color: #fff;
    }

    .pet-pref-save-loading {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .pet-pref-save-spinner {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        animation: pet-pref-save-spin 0.8s linear infinite;
    }

    .hidden {
        display: none !important;
    }

    @keyframes pet-pref-save-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 900px) {
        .pet-preferences-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
