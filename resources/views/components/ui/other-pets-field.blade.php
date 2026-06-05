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
    <label class="service-field service-other-pets-label">
        <span>Other</span>
        <div class="service-input-with-icon">
            <div class="service-other-pet-type-wrap">
                <x-ui.pet-type :id="$inputId" name="other_pet_input" label="" variant="service"
                    placeholder="Specify pet type" breeds-select-id="" x-model="otherPetInput"
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
                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"
                    fill="none">
                    <g filter="url(#filter0_d_{{ $inputId }})">
                        <rect x="8" y="3" width="48" height="48" rx="24" fill="#FFC97A" />
                    </g>
                    <path d="M32 19V27.495M32 27.495V35.99M32 27.495H40.495M32 27.495H23.505" stroke="white"
                        stroke-width="2" stroke-linecap="round" />
                    <defs>
                        <filter id="filter0_d_{{ $inputId }}" x="0" y="0" width="64" height="64"
                            filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix"
                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="5" />
                            <feGaussianBlur stdDeviation="4" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_3_541" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_3_541" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
        </div>

        <div class="service-other-pets-card" x-show="otherPets.length > 0" x-cloak
            x-transition:enter="service-other-pets-card-enter"
            x-transition:enter-start="service-other-pets-card-enter-start"
            x-transition:enter-end="service-other-pets-card-enter-end"
            x-transition:leave="service-other-pets-card-leave"
            x-transition:leave-start="service-other-pets-card-leave-start"
            x-transition:leave-end="service-other-pets-card-leave-end">
            <div class="service-other-pets-head">
                <span>Other</span>
                <span class="service-other-pets-edit-col">Edit</span>
            </div>
            <template x-for="(item, index) in otherPets" :key="`${item}-${index}`">
                <div class="service-other-pets-row"
                    :class="{
                        'is-adding': index === lastAddedIndex,
                        'is-removing': removingKeys.includes(`${item}-${index}`),
                    }"
                    x-transition:enter="service-other-pets-enter"
                    x-transition:enter-start="service-other-pets-enter-start"
                    x-transition:enter-end="service-other-pets-enter-end" x-transition:leave="service-other-pets-leave"
                    x-transition:leave-start="service-other-pets-leave-start"
                    x-transition:leave-end="service-other-pets-leave-end">
                    <div class="service-other-pets-name">
                        <svg class="service-other-pets-paw" xmlns="http://www.w3.org/2000/svg" width="21"
                            height="17" viewBox="0 0 21 17" fill="none" aria-hidden="true">
                            <path fill-rule="evenodd" clip-rule="evenodd"
                                d="M6.75 0C6.0075 0 5.4255 0.465182 5.0655 1.02309C4.701 1.58564 4.5 2.31973 4.5 3.09091C4.5 3.86209 4.701 4.59618 5.0655 5.15873C5.4255 5.71509 6.0075 6.18182 6.75 6.18182C7.4925 6.18182 8.0745 5.71664 8.4345 5.15873C8.799 4.59618 9 3.86209 9 3.09091C9 2.31973 8.799 1.58564 8.4345 1.02309C8.0745 0.466727 7.4925 0 6.75 0ZM14.25 0C13.5075 0 12.9255 0.465182 12.5655 1.02309C12.201 1.58564 12 2.31973 12 3.09091C12 3.86209 12.201 4.59618 12.5655 5.15873C12.9255 5.71509 13.5075 6.18182 14.25 6.18182C14.9925 6.18182 15.5745 5.71664 15.9345 5.15873C16.299 4.59618 16.5 3.86209 16.5 3.09091C16.5 2.31973 16.299 1.58564 15.9345 1.02309C15.5745 0.466727 14.9925 0 14.25 0ZM2.25 6.95455C1.5075 6.95455 0.9255 7.41973 0.5655 7.97764C0.201 8.54018 0 9.27427 0 10.0455C0 10.8166 0.201 11.5507 0.5655 12.1133C0.9255 12.6696 1.5075 13.1364 2.25 13.1364C2.9925 13.1364 3.5745 12.6712 3.9345 12.1133C4.299 11.5507 4.5 10.8166 4.5 10.0455C4.5 9.27427 4.299 8.54018 3.9345 7.97764C3.5745 7.42127 2.9925 6.95455 2.25 6.95455ZM10.5 6.95455C8.7 6.95455 7.3665 7.94982 6.5145 9.18464C5.673 10.4009 5.25 11.9108 5.25 13.1364C5.25 14.5644 6.0825 15.5581 7.104 16.1531C8.109 16.7404 9.369 17 10.5 17C11.631 17 12.891 16.7419 13.896 16.1531C14.916 15.5565 15.75 14.5644 15.75 13.1364C15.75 11.9108 15.327 10.4009 14.4855 9.18464C13.635 7.94827 12.3015 6.95455 10.5 6.95455ZM18.75 6.95455C18.0075 6.95455 17.4255 7.41973 17.0655 7.97764C16.701 8.54018 16.5 9.27427 16.5 10.0455C16.5 10.8166 16.701 11.5507 17.0655 12.1133C17.4255 12.6696 18.0075 13.1364 18.75 13.1364C19.4925 13.1364 20.0745 12.6712 20.4345 12.1133C20.799 11.5507 21 10.8166 21 10.0455C21 9.27427 20.799 8.54018 20.4345 7.97764C20.0745 7.42127 19.4925 6.95455 18.75 6.95455Z"
                                fill="currentColor" />
                        </svg>
                        <span x-text="item"></span>
                    </div>
                    <div class="service-other-pets-edit-col">
                        <button type="button" class="service-other-pets-remove" aria-label="Remove pet type"
                            @click="removeAt(index)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="15"
                                viewBox="0 0 13 15" fill="none">
                                <path
                                    d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                    fill="#3B3731" />
                            </svg>
                        </button>
                    </div>
                </div>
            </template>
        </div>
    </label>
</div>

<style>
    .service-other-pets-label {
        width: 100%;
    }

    .service-input-with-icon {
        display: flex;
        align-items: center;
        gap: 1rem;
        width: calc(332px + 1rem + 64px);
    }

    .service-other-pet-type-wrap {
        position: relative;
        width: 332px;
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
        border: 1px solid #d9d9d9;
        border-radius: 10px;
        background: #fff;
        color: #3B3731;
        font-family: Lato, sans-serif;
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
        font-family: Lato, sans-serif;
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
        margin-top: 10px;
        border: 0;
        background: transparent;
        padding: 0;
        cursor: pointer;
        flex-shrink: 0;
        line-height: 0;
        transition: transform 180ms cubic-bezier(0.22, 1, 0.36, 1);
    }

    .service-other-pet-add-btn:active {
        transform: scale(0.94);
    }

    .service-other-pet-add-btn svg {
        display: block;
    }

    .service-other-pets-card {
        margin-top: 1rem;
        border-radius: 10px;
        background: #F7F7F7;
        padding: 15px 20px;
        overflow: hidden;
        width: calc(332px + 1rem + 64px);
        transform-origin: top center;
    }

    .service-other-pets-card-enter {
        transition: opacity 320ms cubic-bezier(0.22, 1, 0.36, 1),
            transform 320ms cubic-bezier(0.22, 1, 0.36, 1);
    }

    .service-other-pets-card-enter-start {
        opacity: 0;
        transform: translateY(-10px) scale(0.97);
    }

    .service-other-pets-card-enter-end {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .service-other-pets-card-leave {
        transition: opacity 240ms ease, transform 240ms ease;
    }

    .service-other-pets-card-leave-start {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .service-other-pets-card-leave-end {
        opacity: 0;
        transform: translateY(-6px) scale(0.98);
    }

    .service-other-pets-head {
        display: grid;
        grid-template-columns: 1fr 72px;
        border-bottom: 1px solid #D4D4D4;
        min-height: 48px;
        align-items: center;
    }

    .service-other-pets-head>span {
        padding: 0.75rem 1.25rem;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .service-other-pets-head span:first-child {
        padding-left: 0;
    }

    .service-other-pets-edit-col {
        display: flex;
        align-items: center;
        justify-content: center;
        border-left: 1px solid #D4D4D4;
        min-height: 100%;
    }

    .service-other-pets-row {
        display: grid;
        grid-template-columns: 1fr 72px;
        border-bottom: 1px solid #D4D4D4;
        min-height: 56px;
        align-items: center;
        transform-origin: center left;
        background: transparent;
    }

    .service-other-pets-row:last-child {
        border-bottom: none;
    }

    .service-other-pets-row.is-adding {
        animation: service-other-pet-row-in 480ms cubic-bezier(0.22, 1, 0.36, 1) forwards;
    }

    .service-other-pets-row.is-adding .service-other-pets-paw {
        animation: service-other-pet-paw-in 380ms cubic-bezier(0.22, 1, 0.36, 1) 0.06s both;
    }

    .service-other-pets-row.is-adding .service-other-pets-name span {
        animation: service-other-pet-label-in 380ms cubic-bezier(0.22, 1, 0.36, 1) 0.1s both;
    }

    .service-other-pets-row.is-removing {
        animation: service-other-pet-row-out 220ms ease forwards;
        pointer-events: none;
    }

    @keyframes service-other-pet-row-in {
        0% {
            opacity: 0;
            transform: translateY(10px) scale(0.98);
            background: rgba(216, 232, 183, 0.45);
        }

        45% {
            background: rgba(216, 232, 183, 0.22);
        }

        100% {
            opacity: 1;
            transform: translateY(0) scale(1);
            background: transparent;
        }
    }

    @keyframes service-other-pet-paw-in {
        from {
            opacity: 0;
            transform: scale(0.65);
        }

        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    @keyframes service-other-pet-label-in {
        from {
            opacity: 0;
            transform: translateX(-6px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes service-other-pet-row-out {
        to {
            opacity: 0;
            transform: translateX(8px) scale(0.98);
        }
    }

    .service-other-pets-name {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        padding: 0.75rem 1.25rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-transform: capitalize;
        padding-left: 0;
    }

    .service-other-pets-remove {
        border: 0;
        background: transparent;
        color: #9D9B98;
        cursor: pointer;
        padding: 0.35rem;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: color 150ms ease;
    }

    .service-other-pets-remove:hover {
        color: #3B3731;
    }

    .service-other-pets-enter {
        transition: opacity 300ms cubic-bezier(0.22, 1, 0.36, 1),
            transform 300ms cubic-bezier(0.22, 1, 0.36, 1);
    }

    .service-other-pets-enter-start {
        opacity: 0;
        transform: translateY(10px) scale(0.98);
    }

    .service-other-pets-enter-end {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .service-other-pets-leave {
        transition: opacity 220ms ease, transform 220ms ease;
    }

    .service-other-pets-leave-start {
        opacity: 1;
        transform: translateY(0) scale(1);
    }

    .service-other-pets-leave-end {
        opacity: 0;
        transform: translateY(-6px) scale(0.98);
    }
</style>
