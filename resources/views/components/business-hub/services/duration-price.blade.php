@props([
    'showBySize' => true,
    'showBuffer' => true,
    'showBaseDuration' => true,
    'largeMode' => 'dropdown',
    'money' => false,
])

@php
    $durationOptions = ['90 Minutes', '60 Minutes', '45 Minutes', '30 Minutes', '15 Minutes'];
    $bufferTimeOptions = ['15 Minutes', '10 Minutes', '5 Minutes'];
    $overtimeOptions = ['90 Minutes', '45 Minutes', '30 Minutes', '15 Minutes'];
@endphp

<div class="service-dp service-duration-fieldset service-price-fieldset" x-data="{
    @if ($showBaseDuration) baseDuration: @entangle('baseDuration').live, @endif
    @if ($showBuffer) bufferTime: @entangle('bufferTime').live, @endif
    basePrice: $wire.entangle('basePrice').live,
        overtimeCharge: $wire.entangle('overtimeCharge').live,
        overtimePer: $wire.entangle('overtimePer').live,
        showAdvancedPrice: true,
        @if ($showBySize) selectedSizes: $wire.entangle('selectedSizes').live,
        durationSmall: @entangle('durationSmall').live,
        durationMedium: @entangle('durationMedium').live,
        durationLarge: @entangle('durationLarge').live,
        priceSmall: $wire.entangle('priceSmall').live,
        priceMedium: $wire.entangle('priceMedium').live,
        priceLarge: $wire.entangle('priceLarge').live,
        priceLargeDirty: false,
        openDurationSmall: false,
        openDurationMedium: false,
        openDurationLarge: false, @endif
    @if ($showBaseDuration) openBaseDuration: false, @endif
    @if ($showBuffer) openBufferTime: false, @endif
    openOvertimePer: false,
        @if ($showBySize) isDurationEmpty(value) {
            return value === null || value === '';
        },
        isSizeSelected(size) {
            return this.selectedSizes.includes(size);
        },
        init() {
            const syncBySize = (sizes) => {
                if (sizes.includes('large')) {
                    if (this.isDurationEmpty(this.durationLarge)) {
                        this.durationLarge = @js($durationOptions[0]);
                    }
                    if (this.priceLarge === null) {
                        this.priceLarge = this.basePrice;
                        this.priceLargeDirty = false;
                    }
                } else {
                    this.openDurationLarge = false;
                    this.priceLarge = null;
                    this.priceLargeDirty = false;
                }
                if (!sizes.includes('small')) {
                    this.openDurationSmall = false;
                }
                if (!sizes.includes('medium')) {
                    this.openDurationMedium = false;
                }
            };
            syncBySize(this.selectedSizes);
            this.$watch('selectedSizes', syncBySize);
        }, @endif
}" {{ $attributes }}>
    <div @class([
        'service-dp-top',
        'is-pair' => $showBaseDuration && !$showBuffer,
        'is-price-only' => !$showBaseDuration,
    ])>
        <label class="service-field">
            <span>Base Price</span>
            <x-business-hub.services.price-number-input model="basePrice" width="100%"
                increase-label="Increase base price" decrease-label="Decrease base price"
                :decimals="$money ? 2 : 0" />
        </label>

        @if ($showBaseDuration)
            <label class="service-field">
                <span>Base Duration</span>
                <x-business-hub.services.duration-select model="baseDuration" open-key="openBaseDuration"
                    :options="$durationOptions" />
            </label>
        @endif

        @if ($showBuffer)
            <label class="service-field">
                <span>Buffer Time <em>(after service)</em></span>
                <x-business-hub.services.duration-select model="bufferTime" open-key="openBufferTime"
                    :options="$bufferTimeOptions" />
            </label>
        @endif
    </div>

    @if ($showBySize)
        <div class="service-dp-table">
            <div class="service-dp-table-head">
                <p>By Pet Size</p>
                <p>Duration</p>
                <p>Price</p>
            </div>
            <div class="service-dp-table-body">
                <div class="service-dp-table-row" :class="{ 'is-inactive': !isSizeSelected('small') }">
                    <p>Small 0 - 7kg</p>
                    <div class="service-dp-table-value">
                        <div x-show="isSizeSelected('small')" x-cloak>
                            <x-business-hub.services.duration-select model="durationSmall" open-key="openDurationSmall"
                                :options="$durationOptions" />
                        </div>
                        <span x-show="!isSizeSelected('small')" x-cloak class="service-dp-incompatible">Not
                            compatible</span>
                    </div>
                    <div class="service-dp-table-value">
                        <div x-show="isSizeSelected('small')" x-cloak>
                            <x-business-hub.services.price-number-input model="priceSmall" width="100%"
                                increase-label="Increase small pet price"
                                decrease-label="Decrease small pet price" />
                        </div>
                        <span x-show="!isSizeSelected('small')" x-cloak class="service-dp-incompatible">—</span>
                    </div>
                </div>

                <div class="service-dp-table-row" :class="{ 'is-inactive': !isSizeSelected('medium') }">
                    <p>Medium 8-18 kg</p>
                    <div class="service-dp-table-value">
                        <div x-show="isSizeSelected('medium')" x-cloak>
                            <x-business-hub.services.duration-select model="durationMedium"
                                open-key="openDurationMedium" :options="$durationOptions" />
                        </div>
                        <span x-show="!isSizeSelected('medium')" x-cloak class="service-dp-incompatible">Not
                            compatible</span>
                    </div>
                    <div class="service-dp-table-value">
                        <div x-show="isSizeSelected('medium')" x-cloak>
                            <x-business-hub.services.price-number-input model="priceMedium" width="100%"
                                increase-label="Increase medium pet price"
                                decrease-label="Decrease medium pet price" />
                        </div>
                        <span x-show="!isSizeSelected('medium')" x-cloak class="service-dp-incompatible">—</span>
                    </div>
                </div>

                <div class="service-dp-table-row" :class="{ 'is-inactive': !isSizeSelected('large') }">
                    <p>Large 19+ kg</p>
                    @if ($largeMode === 'dropdown')
                        <div class="service-dp-table-value">
                            <div x-show="isSizeSelected('large')" x-cloak>
                                <x-business-hub.services.duration-select model="durationLarge"
                                    open-key="openDurationLarge" :options="$durationOptions" />
                            </div>
                            <span x-show="!isSizeSelected('large')" x-cloak class="service-dp-incompatible">Not
                                compatible</span>
                        </div>
                        <div class="service-dp-table-value">
                            <div x-show="isSizeSelected('large')" x-cloak>
                                <x-business-hub.services.price-number-input model="priceLarge" width="100%"
                                    increase-label="Increase large pet price"
                                    decrease-label="Decrease large pet price" dirty-on-step />
                            </div>
                            <span x-show="!isSizeSelected('large')" x-cloak class="service-dp-incompatible">—</span>
                        </div>
                    @else
                        <span class="service-dp-incompatible">Not compatible</span>
                        <span class="service-dp-incompatible">—</span>
                    @endif
                </div>
            </div>
        </div>
    @endif

    <button type="button" class="service-dp-advanced-btn" @click="showAdvancedPrice = !showAdvancedPrice"
        :aria-expanded="showAdvancedPrice.toString()">
        @if ($money)
            <span>+</span>
        @else
            <span x-text="showAdvancedPrice ? '−' : '+'"></span>
        @endif
        <span>Advanced Price Settings</span>
    </button>

    <div @class(['service-dp-overtime', 'is-fixed' => $money]) x-cloak x-show="showAdvancedPrice"
        x-transition:enter="service-dp-overtime-enter" x-transition:enter-start="service-dp-overtime-enter-start"
        x-transition:enter-end="service-dp-overtime-enter-end" x-transition:leave="service-dp-overtime-leave"
        x-transition:leave-start="service-dp-overtime-leave-start"
        x-transition:leave-end="service-dp-overtime-leave-end">
        <label class="service-field">
            <span>Overtime charges</span>
            <x-business-hub.services.price-number-input model="overtimeCharge" width="100%"
                increase-label="Increase overtime charge" decrease-label="Decrease overtime charge"
                :decimals="$money ? 2 : 0" />
        </label>
        <span class="service-dp-overtime-per">per</span>
        <label class="service-field">
            <span>Overtime Duration</span>
            <div class="service-custom-select" :class="{ 'is-open': openOvertimePer }"
                @keydown.escape.window="openOvertimePer = false" @click.outside="openOvertimePer = false">
                <button type="button" class="service-custom-trigger" @click="openOvertimePer = !openOvertimePer"
                    :aria-expanded="openOvertimePer.toString()">
                    <span x-text="overtimePer"></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6" viewBox="0 0 11 6"
                        fill="none" aria-hidden="true">
                        <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <div class="service-custom-menu" x-cloak x-show="openOvertimePer"
                    x-transition:enter="service-custom-menu-enter"
                    x-transition:enter-start="service-custom-menu-enter-start"
                    x-transition:enter-end="service-custom-menu-enter-end"
                    x-transition:leave="service-custom-menu-leave"
                    x-transition:leave-start="service-custom-menu-leave-start"
                    x-transition:leave-end="service-custom-menu-leave-end">
                    @foreach ($overtimeOptions as $option)
                        <button type="button" class="service-custom-option"
                            :class="{ 'is-active': overtimePer === @js($option) }"
                            @click="overtimePer = @js($option); openOvertimePer = false">
                            <span>{{ $option }}</span>
                        </button>
                    @endforeach
                </div>
            </div>
        </label>
    </div>
</div>

@once
    <style>
        .service-dp {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .service-dp-top {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 1.25rem;
            align-items: end;
        }

        .service-dp-top.is-pair {
            grid-template-columns: 1fr 1fr;
            gap: 2.5rem;
        }

        .service-dp-top.is-price-only {
            display: block;
        }

        .service-dp-top.is-price-only>.service-field {
            width: 100%;
            max-width: 317px;
        }

        .service-dp .service-field>span em {
            font-style: normal;
            font-weight: 400;
            color: #948f88;
        }

        .service-dp .service-custom-select,
        .service-dp .service-custom-trigger,
        .service-dp .service-number-input-wrap {
            width: 100% !important;
        }

        .service-dp .service-custom-select {
            position: relative;
        }

        .service-dp .service-custom-select.is-open {
            z-index: 40;
        }

        .service-dp .service-custom-trigger {
            height: 48px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: #fff;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            line-height: 25px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
            text-align: left;
        }

        .service-dp .service-custom-select.is-open .service-custom-trigger {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .service-dp .service-custom-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #F8F8F8;
            border: 1px solid #DDD;
            border-top: none;
            border-radius: 0 0 10px 10px;
            z-index: 50;
            overflow: hidden;
        }

        .service-dp .service-custom-option {
            width: 100%;
            border: 0;
            border-bottom: 2px solid #e6e6e5;
            background: #fff;
            padding: 0.9rem 1rem;
            text-align: left;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .service-dp .service-custom-option:last-child {
            border-bottom: none;
        }

        .service-dp .service-custom-option:hover {
            background: #F2F2F2;
        }

        .service-dp .service-custom-option.is-active {
            background: rgba(216, 232, 183, 0.20);
            color: #A4C560;
        }

        .service-dp .service-number-input-wrap {
            position: relative;
        }

        .service-dp .service-number-input-wrap input[type="number"],
        .service-dp .service-number-input-wrap input[type="text"] {
            width: 100%;
            height: 48px;
            border-radius: 10px;
            border: 1px solid #ddd;
            background: #fff;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            padding: 0 1.5rem 0 1.45rem;
            -moz-appearance: textfield;
            box-sizing: border-box;
        }

        .service-dp .service-number-input-wrap-currency::before {
            content: "£";
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            pointer-events: none;
        }

        .service-dp .service-number-input-wrap input[type="number"]::-webkit-outer-spin-button,
        .service-dp .service-number-input-wrap input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .service-dp .service-number-input-controls {
            position: absolute;
            top: 50%;
            right: 0.7rem;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .service-dp .service-number-step-btn {
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

        .service-dp-table {
            overflow: visible;
        }

        .service-dp-table-head {
            display: grid;
            grid-template-columns: 1.15fr 1fr 1fr;
            align-items: center;
            height: 50px;
            padding: 0 1.25rem;
            background: #f6f5f5;
            border-radius: 10px 10px 0 0;
        }

        .service-dp-table-head p {
            margin: 0;
            color: #948f88;
            font-family: Lato;
            font-size: 16px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .service-dp-table-body {
            background: #fff;
            border: 1px solid #f6f5f5;
            border-top: 0;
            border-radius: 0 0 10px 10px;
            overflow: visible;
        }

        .service-dp-table-row {
            display: grid;
            grid-template-columns: 1.15fr 1fr 1fr;
            align-items: center;
            gap: 1rem;
            min-height: 68px;
            padding: 0.6rem 1.25rem;
            position: relative;
        }

        .service-dp-table-row:has(.service-custom-select.is-open) {
            z-index: 40;
        }

        .service-dp-table-row p {
            margin: 0;
            color: #3B3731 !important;
            font-family: Lato;
            font-size: 16px;
            font-weight: 600;
        }

        .service-dp-table-row.is-inactive p {
            color: #9d9b98 !important;
            font-weight: 600;
        }

        .service-dp-table-value {
            position: relative;
            min-height: 48px;
        }

        .service-dp-incompatible {
            margin-left: 1rem;
            display: flex;
            align-items: center;
            min-height: 48px;
            color: #948f88;
            font-family: Lato;
            font-size: 16px;
            font-style: italic;
            font-weight: 400;
            line-height: 25px;
        }

        .service-dp-advanced-btn {
            border: 0;
            background: transparent;
            color: #fdb752;
            font-family: Lato;
            font-size: 16px;
            font-weight: 700;
            line-height: normal;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            cursor: pointer;
            padding: 0;
            width: fit-content;
        }

        .service-dp-overtime {
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            gap: 1.25rem;
            align-items: end;
            padding: 1.25rem;
            background: #fff;
            border: 1px solid #f6f5f5;
            border-radius: 10px;
        }

        .service-dp-overtime.is-fixed {
            display: flex;
            align-items: flex-end;
            justify-content: flex-start;
            gap: 20px;
            width: 100%;
            box-sizing: border-box;
        }

        .service-dp-overtime.is-fixed>.service-field {
            flex: 0 1 317px;
            width: 317px;
            min-width: 0;
            max-width: 317px;
        }

        .service-dp-overtime.is-fixed .service-dp-overtime-per {
            flex: 0 0 auto;
        }

        .service-dp-overtime-per {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            line-height: 25px;
            padding-bottom: 0.75rem;
        }

        .service-dp-overtime-enter {
            transition: opacity 260ms cubic-bezier(0.4, 0, 0.2, 1), transform 260ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .service-dp-overtime-enter-start {
            opacity: 0;
            transform: translateY(-8px);
        }

        .service-dp-overtime-enter-end {
            opacity: 1;
            transform: translateY(0);
        }

        .service-dp-overtime-leave {
            transition: opacity 200ms cubic-bezier(0.4, 0, 0.2, 1), transform 200ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        .service-dp-overtime-leave-start {
            opacity: 1;
            transform: translateY(0);
        }

        .service-dp-overtime-leave-end {
            opacity: 0;
            transform: translateY(-8px);
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

        .service-dp [x-cloak] {
            display: none !important;
        }

        @media (max-width: 900px) {

            .service-dp-top,
            .service-dp-table-head,
            .service-dp-table-row,
            .service-dp-overtime {
                grid-template-columns: 1fr;
            }

            .service-dp-overtime.is-fixed {
                flex-direction: column;
                align-items: stretch;
            }

            .service-dp-overtime.is-fixed>.service-field {
                width: 100%;
                max-width: none;
                flex-basis: auto;
            }

            .service-dp-top.is-price-only>.service-field {
                max-width: none;
            }

            .service-dp-overtime-per {
                padding-bottom: 0;
            }
        }
    </style>
@endonce
