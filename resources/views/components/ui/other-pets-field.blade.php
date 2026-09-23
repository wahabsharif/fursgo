@props([
    'inputId' => 'service-other-pet',
])

<div class="service-other-pets-block" x-data="{
    otherPets: $wire.entangle('otherPets').live,
    otherPetInput: $wire.entangle('otherPetInput').live,
    showSuggestions: false,
    suggestions: [],
    removingKeys: [],
    lastAddedIndex: -1,
    inputId: @js($inputId),
    excludeTypes: ['Dog', 'Cat'],
    async init() {
        if (typeof loadPetBreedsData === 'function') {
            await loadPetBreedsData();
        }
    },
    filterSuggestions() {
        const query = this.otherPetInput.trim().toLowerCase();
        if (query.length === 0) {
            this.suggestions = [];
            this.showSuggestions = false;
            return;
        }
        const types = window.petBreedsData?.petTypes ?? [];
        this.suggestions = types.filter((petType) =>
            !this.excludeTypes.includes(petType.name) &&
            petType.name.toLowerCase().includes(query)
        );
        this.showSuggestions = this.suggestions.length > 0;
    },
    addPet(name = null) {
        const candidate = (name ?? this.otherPetInput).trim();
        if (candidate === '') {
            return;
        }
        const exists = this.otherPets.some((item) => item.toLowerCase() === candidate.toLowerCase());
        if (!exists) {
            this.otherPets = [...this.otherPets, candidate];
            this.lastAddedIndex = this.otherPets.length - 1;
            setTimeout(() => {
                if (this.lastAddedIndex === this.otherPets.length - 1) {
                    this.lastAddedIndex = -1;
                }
            }, 480);
        }
        this.otherPetInput = '';
        this.suggestions = [];
        this.showSuggestions = false;
    },
    selectSuggestion(name) {
        this.addPet(name);
    },
    removeAt(index) {
        if (!this.otherPets[index]) {
            return;
        }
        const candidate = this.otherPets[index];
        const removeKey = `${candidate}-${index}`;
        this.removingKeys.push(removeKey);
        setTimeout(() => {
            const targetIndex = this.otherPets.findIndex((item) => item.toLowerCase() === candidate.toLowerCase());
            if (targetIndex !== -1) {
                this.otherPets = this.otherPets.filter((_, i) => i !== targetIndex);
            }
            this.removingKeys = this.removingKeys.filter((key) => key !== removeKey);
        }, 190);
    },
}" x-init="init()"
    @click.outside="showSuggestions = false">
    <div class="service-other-pets-label">
        <span>Other</span>
        <div class="service-input-with-icon">
            <div class="service-other-pet-type-wrap">
                <x-ui.pet-type :id="$inputId" name="other_pet_input" label="" variant="service"
                    placeholder="Specify Pet Type" breeds-select-id="" x-model="otherPetInput"
                    @input="filterSuggestions()" @focus="if (otherPetInput.trim()) { filterSuggestions(); }"
                    @keydown.enter.prevent="addPet()" />
                <div id="{{ $inputId }}-suggestions" class="pet-type-suggestions pet-type-suggestions--service"
                    x-show="showSuggestions && suggestions.length > 0" x-cloak>
                    <template x-for="pet in suggestions" :key="pet.id">
                        <button type="button" class="pet-type-suggestion-item" @click="selectSuggestion(pet.name)"
                            x-text="pet.name"></button>
                    </template>
                </div>
            </div>
            <button type="button" class="service-other-pet-add-btn" aria-label="Add pet type" @click="addPet()">
                <img src="{{ asset('images/business-hub/icon-other-pet-add.svg') }}" width="64" height="64" alt="" />
            </button>
        </div>

        <div class="service-other-pets-chips" x-show="otherPets.length > 0" x-cloak>
            <template x-for="(item, index) in otherPets" :key="`${item}-${index}`">
                <div class="service-other-pet-chip"
                    :class="{
                        'is-adding': index === lastAddedIndex,
                        'is-removing': removingKeys.includes(`${item}-${index}`),
                    }">
                    <span x-text="item"></span>
                    <button type="button" class="service-other-pet-chip-remove" aria-label="Remove pet type"
                        @click="removeAt(index)">
                        <img src="{{ asset('images/business-hub/icon-other-pet-close.svg') }}" width="11.6148"
                            height="11.6066" alt="" />
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>

@once
    <style>
        .service-other-pets-label {
            display: flex;
            flex-direction: column;
            gap: 13px;
            width: 100%;
        }

        .service-other-pets-label>span {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-input-with-icon {
            display: flex;
            align-items: flex-start;
            gap: 20px;
            width: 100%;
        }

        .service-other-pet-type-wrap {
            position: relative;
            width: 417px;
            max-width: 100%;
            flex-shrink: 0;
        }

        .pet-type-field--service {
            width: 100%;
        }

        .pet-type-field--service .pet-type-input-wrap {
            position: relative;
            display: block;
            width: 100%;
        }

        .pet-type-input--service {
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
            padding: 0.65rem 0.9rem;
        }

        .pet-type-input--service::placeholder {
            color: #9D9B98;
        }

        .pet-type-suggestions--service {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            z-index: 30;
            border: 1px solid #D4D4D4;
            border-top: none;
            border-radius: 0 0 10px 10px;
            background: #FFF;
            max-height: 200px;
            overflow-y: auto;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
        }

        .pet-type-suggestion-item {
            width: 100%;
            border: 0;
            border-bottom: 1px solid #EEE;
            background: #FFF;
            padding: 0.65rem 0.9rem;
            text-align: left;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            cursor: pointer;
        }

        .pet-type-suggestion-item:last-child {
            border-bottom: none;
        }

        .pet-type-suggestion-item:hover {
            background: #F2F2F2;
        }

        .service-other-pet-add-btn {
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
            transition: transform 180ms cubic-bezier(0.22, 1, 0.36, 1);
        }

        .service-other-pet-add-btn:active {
            transform: scale(0.94);
        }

        .service-other-pet-add-btn img {
            position: absolute;
            left: -8px;
            top: -3px;
            pointer-events: none;
        }

        .service-other-pets-chips {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 20px;
            margin-top: 7px;
        }

        .service-other-pet-chip {
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

        .service-other-pet-chip.is-adding {
            animation: service-other-pet-chip-in 380ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
        }

        .service-other-pet-chip.is-removing {
            animation: service-other-pet-chip-out 190ms ease forwards;
            pointer-events: none;
        }

        .service-other-pet-chip-remove {
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

        .service-other-pet-chip-remove img {
            display: block;
        }

        @keyframes service-other-pet-chip-in {
            from {
                opacity: 0;
                transform: scale(0.94);
            }

            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes service-other-pet-chip-out {
            to {
                opacity: 0;
                transform: scale(0.94);
            }
        }

        @media (max-width: 720px) {
            .service-other-pet-type-wrap {
                width: 100%;
            }
        }
    </style>
@endonce
