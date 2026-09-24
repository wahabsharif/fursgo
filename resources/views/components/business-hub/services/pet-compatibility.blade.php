@props([
    'otherPetsInputId' => 'service-other-pet',
    'title' => 'Pet Compatibility',
    'showOtherPets' => true,
])

<div class="service-fieldset pet-compatibility-fieldset" x-data="{
    selectedPets: $wire.entangle('selectedPets').live,
    selectedSizes: $wire.entangle('selectedSizes').live,
    togglePet(pet) {
        if (this.selectedPets.includes(pet)) {
            this.selectedPets = this.selectedPets.filter((item) => item !== pet);
        } else {
            this.selectedPets.push(pet);
        }
    },
    toggleSize(size) {
        if (this.selectedSizes.includes(size)) {
            this.selectedSizes = this.selectedSizes.filter((item) => item !== size);
        } else {
            this.selectedSizes.push(size);
        }
    },
}" {{ $attributes }}>
    @if (filled($title))
        <h4>{{ $title }}</h4>
    @endif
    <div class="service-chip-row">
        <div class="service-chip-group">
            <span>Pet Types</span>
            <div class="service-chip-group-chips">
                <x-business-hub.services.pet-compatibility-chip value="cat" label="Cat" icon="cat" type="pet" />
                <x-business-hub.services.pet-compatibility-chip value="other" label="Other" icon="other" type="pet" />
                <x-business-hub.services.pet-compatibility-chip value="dog" label="Dog" icon="dog" type="pet" />
            </div>
        </div>
        <div class="service-chip-group">
            <span>Pet Size</span>
            <div class="service-chip-group-chips">
                <x-business-hub.services.pet-compatibility-chip value="small" label="Small 0-7 kg" type="size" />
                <x-business-hub.services.pet-compatibility-chip value="medium" label="Medium 8-18 kg" type="size" />
                <x-business-hub.services.pet-compatibility-chip value="large" label="Large 19+ kg" type="size" />
            </div>
        </div>
    </div>

    @if ($showOtherPets)
        <x-ui.other-pets-field :input-id="$otherPetsInputId" />
    @endif
</div>

@once
    <style>
        .pet-compatibility-fieldset {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .pet-compatibility-fieldset .service-chip-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 2rem;
            width: 100%;
            flex-wrap: wrap;
        }

        .pet-compatibility-fieldset .service-chip-group {
            display: flex;
            flex-direction: column;
            gap: 13px;
        }

        .pet-compatibility-fieldset .service-chip-group-chips {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pet-compatibility-fieldset .service-chip-group>span {
            display: inline-block;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .pet-compatibility-fieldset .service-chip {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            border-radius: 22px;
            background: #F7F7F7;
            color: #D4D4D4;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
            padding: 0 16px 0 15px;
            cursor: pointer;
            transition: background-color 220ms ease, color 220ms ease;
        }

        .pet-compatibility-fieldset .service-chip--size {
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
            padding: 0 16px;
        }

        .pet-compatibility-fieldset .service-chip.is-active {
            background: rgba(216, 232, 183, 0.20);
            color: #A4C560;
        }

        .pet-compatibility-fieldset .service-chip:active {
            transform: scale(0.98);
        }

        .pet-compatibility-fieldset .service-chip-icon {
            color: #D4D4D4;
            flex-shrink: 0;
            transition: color 220ms ease;
        }

        .pet-compatibility-fieldset .service-chip.is-active .service-chip-icon {
            color: #A4C560;
        }

        .pet-compatibility-fieldset .service-chip-tick {
            flex-shrink: 0;
            width: 0;
            height: 9px;
            overflow: hidden;
            opacity: 0;
            transform: scale(0.6);
            transition: opacity 180ms ease, transform 180ms ease, width 180ms ease;
        }

        .pet-compatibility-fieldset .service-chip.is-active .service-chip-tick {
            width: 12px;
            opacity: 1;
            transform: scale(1);
        }
    </style>
@endonce
