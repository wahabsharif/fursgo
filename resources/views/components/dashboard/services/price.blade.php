@props([
    'title' => 'Price',
    'showBySize' => false,
    'showAdvanced' => false,
    'largeMode' => null,
    'mutedOvertimeLabel' => true,
])

@php
    $overtimeOptions = ['15 min', '30 min', '45 min'];
@endphp

<div class="service-fieldset service-price-fieldset" x-data="{
    basePrice: $wire.entangle('basePrice').live,
    overtimeCharge: $wire.entangle('overtimeCharge').live,
    @if ($showBySize) priceSmall: $wire.entangle('priceSmall').live,
            priceMedium: $wire.entangle('priceMedium').live,
            @if ($largeMode === 'editable')
                priceLarge: $wire.entangle('priceLarge').live,
                priceLargeDirty: false, @endif
    @endif
}" {{ $attributes }}>
    <h4>{{ $title }}</h4>
    <div class="service-price-top-row">
        <label class="service-field">
            <span>Base Price</span>
            <x-dashboard.services.price-number-input model="basePrice" increase-label="Increase base price"
                decrease-label="Decrease base price" />
        </label>

        @if ($showAdvanced)
            <div class="service-price-advanced-wrap">
                <button type="button" class="service-price-advanced-btn">
                    <span>+</span>
                    <span>Advanced Price Settings</span>
                </button>
            </div>
        @endif
    </div>

    @if ($showBySize)
        <div class="service-price-layout">
            <div class="service-price-by-size">
                <div class="service-price-by-size-head">
                    <p>Pricing by Size</p>
                    <p>Price</p>
                </div>
                <div class="service-price-by-size-card">
                    <div class="service-price-by-size-row">
                        <p>Small 0-7 kg</p>
                        <x-dashboard.services.price-number-input model="priceSmall" width="85px"
                            increase-label="Increase small pet price" decrease-label="Decrease small pet price" />
                    </div>
                    <div class="service-price-by-size-row">
                        <p>Medium 8-18 kg</p>
                        <x-dashboard.services.price-number-input model="priceMedium" width="85px"
                            increase-label="Increase medium pet price" decrease-label="Decrease medium pet price" />
                    </div>
                    <div class="service-price-by-size-row">
                        <p>Large 19+ kg</p>
                        @if ($largeMode === 'editable')
                            <span x-show="priceLarge === null"
                                class="service-duration-none service-price-large-placeholder" role="button"
                                tabindex="0"
                                @click="priceLargeDirty = false; priceLarge = basePrice; $nextTick(() => $refs.priceLargeInput?.focus())"
                                @keydown.enter.prevent="priceLargeDirty = false; priceLarge = basePrice; $nextTick(() => $refs.priceLargeInput?.focus())">—</span>
                            <div x-show="priceLarge !== null" x-cloak
                                class="service-number-input-wrap service-number-input-wrap-currency"
                                style="width: 85px;"
                                @click.outside="if (!priceLargeDirty) { priceLarge = null; priceLargeDirty = false; }">
                                <input type="number" min="0" step="0.01" x-ref="priceLargeInput"
                                    x-model.number="priceLarge" style="width: 100%;" @input="priceLargeDirty = true" />
                                <div class="service-number-input-controls">
                                    <button type="button" class="service-number-step-btn"
                                        aria-label="Increase large pet price"
                                        @click="priceLargeDirty = true; $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').stepUp(); $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').dispatchEvent(new Event('input', { bubbles: true }))">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                            viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                            <path d="M10.3741 5.47876L5.39527 0.499941L0.500024 5.39518"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <button type="button" class="service-number-step-btn"
                                        aria-label="Decrease large pet price"
                                        @click="priceLargeDirty = true; $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').stepDown(); $event.currentTarget.closest('.service-number-input-wrap').querySelector('input').dispatchEvent(new Event('input', { bubbles: true }))">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                            viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                            <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        @else
                            <span class="service-duration-none">—</span>
                        @endif
                    </div>
                </div>
            </div>

            <x-dashboard.services.price-overtime :muted-label="$mutedOvertimeLabel" :options="$overtimeOptions" />
        </div>
    @else
        <x-dashboard.services.price-overtime :muted-label="$mutedOvertimeLabel" :options="$overtimeOptions" />
    @endif
</div>

@once
    <style>
        .service-price-fieldset .service-custom-select {
            position: relative;
            width: 190px;
        }

        .service-price-fieldset .service-custom-trigger {
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

        .service-price-fieldset .service-custom-select.is-open .service-custom-trigger {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-color: #DDD;
        }

        .service-price-fieldset .service-custom-menu {
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

        .service-price-fieldset .service-custom-menu-enter {
            transition: opacity 180ms ease, transform 180ms ease;
            transform-origin: top;
        }

        .service-price-fieldset .service-custom-menu-enter-start {
            opacity: 0;
            transform: scaleY(0.95);
        }

        .service-price-fieldset .service-custom-menu-enter-end {
            opacity: 1;
            transform: scaleY(1);
        }

        .service-price-fieldset .service-custom-menu-leave {
            transition: opacity 140ms ease, transform 140ms ease;
            transform-origin: top;
        }

        .service-price-fieldset .service-custom-menu-leave-start {
            opacity: 1;
            transform: scaleY(1);
        }

        .service-price-fieldset .service-custom-menu-leave-end {
            opacity: 0;
            transform: scaleY(0.95);
        }

        .service-price-fieldset .service-custom-option {
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

        .service-price-fieldset .service-custom-option:last-child {
            border-bottom: none;
        }

        .service-price-fieldset .service-custom-option:hover {
            background: #F2F2F2;
        }

        .service-price-fieldset .service-custom-option.is-active {
            background: rgba(216, 232, 183, 0.20);
            color: #A4C560;
        }

        .service-price-fieldset .service-custom-select-overtime,
        .service-price-fieldset .service-custom-select-overtime .service-custom-trigger {
            width: 145px;
        }

        .service-price-fieldset .service-price-large-placeholder {
            cursor: text;
        }

        .service-price-fieldset .service-price-large-placeholder:hover {
            color: #9D9B98;
        }

        .service-price-fieldset .service-price-top-row {
            display: flex;
            align-items: flex-end;
            gap: 1.5rem;
            margin: 1rem 0;
        }

        .service-price-fieldset .service-price-advanced-wrap {
            padding-bottom: 0.45rem;
        }

        .service-price-fieldset .service-price-advanced-btn {
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

        .service-price-fieldset .service-price-advanced-btn span:last-child {
            text-decoration-line: underline;
            text-underline-offset: 4px;
        }

        .service-price-fieldset .service-price-layout {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            margin: 1rem 0;
        }

        .service-price-fieldset .service-price-by-size {
            width: 422px;
        }

        .service-price-fieldset .service-price-by-size-head {
            display: grid;
            grid-template-columns: 1fr 205px;
            gap: 0.8rem;
            margin-bottom: 0.6rem;
        }

        .service-price-fieldset .service-price-by-size-head p {
            margin: 0;
            color: #000;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-price-fieldset .service-price-by-size-card {
            width: 100%;
            border-radius: 10px;
            background: #FAFAFA;
            padding: 1rem 0.9rem;
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        .service-price-fieldset .service-price-by-size-row {
            display: grid;
            grid-template-columns: 1fr 190px;
            align-items: center;
            gap: 0.8rem;
        }

        .service-price-fieldset .service-price-by-size-row p {
            margin: 0;
            color: #000;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-price-fieldset .service-price-by-size-row input {
            width: 100%;
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
            padding: 0 0.75rem;
        }

        .service-price-fieldset .service-overtime-wrap {
            width: 280px;
            padding-top: 0.2rem;
        }

        .service-price-fieldset .service-overtime-inline {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            width: fit-content;
        }

        .service-price-fieldset .service-overtime-inline input {
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

        .service-price-fieldset .service-overtime-per-text {
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
        }

        .service-price-fieldset .service-number-input-wrap {
            position: relative;
            width: 100%;
        }

        .service-price-fieldset .service-number-input-wrap input[type="number"] {
            width: 100%;
            padding-right: 1.5rem;
            -moz-appearance: textfield;
            height: 48px;
            border-radius: 10px;
            border: 1px solid #d9d9d9;
            background: #fff;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-price-fieldset .service-number-input-wrap-currency::before {
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

        .service-price-fieldset .service-number-input-wrap-currency input[type="number"] {
            padding-left: 1.45rem;
        }

        .service-price-fieldset .service-number-input-wrap input[type="number"]::-webkit-outer-spin-button,
        .service-price-fieldset .service-number-input-wrap input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .service-price-fieldset .service-number-input-controls {
            position: absolute;
            top: 50%;
            right: 0.7rem;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .service-price-fieldset .service-number-step-btn {
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

        .service-price-fieldset .service-number-input-wrap-compact {
            width: 64px;
        }

        .service-price-fieldset .service-number-input-wrap-compact input[type="number"] {
            height: 42px;
        }

        .service-price-fieldset .service-duration-none {
            width: 165px;
            height: 48px;
            display: flex;
            align-items: end;
            padding-left: 2rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }
    </style>
@endonce
