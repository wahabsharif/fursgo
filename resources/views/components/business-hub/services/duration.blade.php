@props([
    'title' => 'Duration',
    'showBySize' => false,
    'showAdvanced' => false,
    'largeMode' => null,
])

@php
    $baseDurationOptions = ['90 Minutes', '60 Minutes', '30 Minutes'];
    $bufferTimeOptions = ['15 min', '10 min', '5 min'];
    $smallDurationOptions = ['15 min', '20 min', '30 min'];
    $mediumDurationOptions = ['20 min', '30 min', '45 min'];
    $largeDurationOptions = ['45 min', '60 min', '90 min'];
@endphp

<div class="service-fieldset service-duration-fieldset" x-data="{
    baseDuration: @entangle('baseDuration').live,
    bufferTime: @entangle('bufferTime').live,
    showAdvancedDuration: @js(!$showAdvanced),
    @if ($showBySize) durationSmall: @entangle('durationSmall').live,
            durationMedium: @entangle('durationMedium').live,
            @if ($largeMode === 'dropdown')
                durationLarge: @entangle('durationLarge').live, @endif
    @endif
    openBaseDuration: false,
    openBufferTime: false,
    @if ($showBySize) openDurationSmall: false,
            openDurationMedium: false,
            @if ($largeMode === 'dropdown')
                openDurationLarge: false, @endif
    @endif
    isDurationEmpty(value) {
        return value === null || value === '';
    },
}" {{ $attributes }}>
    <h4>{{ $title }}</h4>
    <div class="service-form-grid">
        <label class="service-field">
            <span>Base Duration</span>
            <x-business-hub.services.duration-select model="baseDuration" open-key="openBaseDuration" :options="$baseDurationOptions" />
        </label>

        <label class="service-field">
            <span style="color: #9D9B98;">Buffer Time (after service)</span>
            <x-business-hub.services.duration-select model="bufferTime" open-key="openBufferTime" :options="$bufferTimeOptions"
                trigger-style="background: #F7F7F7;" />
        </label>

        @if ($showAdvanced)
            <div class="service-duration-advanced-wrap">
                <button type="button" class="service-duration-advanced-btn"
                    @click="showAdvancedDuration = !showAdvancedDuration"
                    :aria-expanded="showAdvancedDuration.toString()">
                    <span x-text="showAdvancedDuration ? '−' : '+'"></span>
                    <span>Advanced Duration Settings</span>
                </button>
            </div>
        @endif
    </div>

    @if ($showBySize)
        @if ($showAdvanced)
            <div class="service-duration-by-size-reveal" x-cloak x-show="showAdvancedDuration"
                x-transition:enter="service-duration-by-size-enter"
                x-transition:enter-start="service-duration-by-size-enter-start"
                x-transition:enter-end="service-duration-by-size-enter-end"
                x-transition:leave="service-duration-by-size-leave"
                x-transition:leave-start="service-duration-by-size-leave-start"
                x-transition:leave-end="service-duration-by-size-leave-end">
        @endif
        <div class="service-duration-by-size">
            <div class="service-duration-by-size-head">
                <p>Duration by Size</p>
                <p>Time</p>
            </div>

            <div class="service-duration-by-size-card">
                <div class="service-duration-by-size-row">
                    <p>Small 0-7 kg</p>
                    <x-business-hub.services.duration-select model="durationSmall" open-key="openDurationSmall"
                        :options="$smallDurationOptions" :by-size="true" />
                </div>

                <div class="service-duration-by-size-row">
                    <p>Medium 8-18 kg</p>
                    <x-business-hub.services.duration-select model="durationMedium" open-key="openDurationMedium"
                        :options="$mediumDurationOptions" :by-size="true" />
                </div>

                <div class="service-duration-by-size-row">
                    <p>Large 19+ kg</p>
                    @if ($largeMode === 'dropdown')
                        <x-business-hub.services.duration-select model="durationLarge" open-key="openDurationLarge"
                            :options="$largeDurationOptions" :by-size="true" :allow-empty="true" />
                    @else
                        <span class="service-duration-none">—</span>
                    @endif
                </div>
            </div>
        </div>
        @if ($showAdvanced)
            </div>
        @endif
    @endif
</div>

@once
    <style>
        .service-duration-fieldset .service-custom-select {
            position: relative;
            width: 190px;
        }

        .service-duration-fieldset .service-custom-select.is-open {
            z-index: 30;
        }

        .service-duration-fieldset .service-custom-select-duration.is-open {
            z-index: 50;
        }

        .service-duration-fieldset .service-custom-select-duration,
        .service-duration-fieldset .service-custom-select-duration .service-custom-trigger {
            width: 165px;
        }

        .service-duration-fieldset .service-custom-trigger {
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

        .service-duration-fieldset .service-custom-select.is-open .service-custom-trigger {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
            border-bottom-color: #DDD;
        }

        .service-duration-fieldset .service-custom-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #F8F8F8;
            border: 1px solid #DDD;
            border-top: none;
            border-radius: 0 0 10px 10px;
            z-index: 40;
            overflow: hidden;
        }

        .service-duration-fieldset .service-custom-menu-enter {
            transition: opacity 180ms ease, transform 180ms ease;
            transform-origin: top;
        }

        .service-duration-fieldset .service-custom-menu-enter-start {
            opacity: 0;
            transform: scaleY(0.95);
        }

        .service-duration-fieldset .service-custom-menu-enter-end {
            opacity: 1;
            transform: scaleY(1);
        }

        .service-duration-fieldset .service-custom-menu-leave {
            transition: opacity 140ms ease, transform 140ms ease;
            transform-origin: top;
        }

        .service-duration-fieldset .service-custom-menu-leave-start {
            opacity: 1;
            transform: scaleY(1);
        }

        .service-duration-fieldset .service-custom-menu-leave-end {
            opacity: 0;
            transform: scaleY(0.95);
        }

        .service-duration-fieldset .service-custom-option {
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

        .service-duration-fieldset .service-custom-option:last-child {
            border-bottom: none;
        }

        .service-duration-fieldset .service-custom-option:hover {
            background: #F2F2F2;
        }

        .service-duration-fieldset .service-custom-option.is-active {
            background: rgba(216, 232, 183, 0.20);
            color: #A4C560;
        }

        .service-duration-fieldset .service-duration-advanced-wrap {
            display: flex;
            align-items: flex-end;
            padding-bottom: 0.4rem;
        }

        .service-duration-fieldset .service-duration-advanced-btn {
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

        .service-duration-fieldset .service-duration-advanced-btn span:last-child {
            text-decoration-line: underline;
            text-underline-offset: 4px;
        }

        .service-duration-fieldset .service-duration-by-size {
            margin-top: 2rem;
            width: 28%;
            overflow: visible;
        }

        .service-duration-fieldset .service-duration-by-size-head {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
            margin-bottom: 0.8rem;
        }

        .service-duration-fieldset .service-duration-by-size-head p {
            margin: 0;
            color: #1F1F1F;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-duration-fieldset .service-duration-by-size-card {
            border-radius: 12px;
            background: #FAFAFA;
            padding: 1.2rem 1.25rem;
            display: flex;
            flex-direction: column;
            gap: 1.2rem;
            overflow: visible;
        }

        .service-duration-fieldset .service-duration-by-size-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .service-duration-fieldset .service-duration-by-size-row p {
            margin: 0;
            color: #000;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-duration-fieldset .service-duration-none {
            width: 165px;
            height: 48px;
            display: flex;
            align-items: center;
            padding-left: 2rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-duration-fieldset .service-custom-select-duration .service-custom-trigger.is-empty {
            width: 165px;
            min-height: 48px;
            border: none;
            background: transparent;
            justify-content: flex-start;
            align-items: center;
            padding-left: 2rem;
            padding-right: 1rem;
            font-size: 24px;
            line-height: normal;
            cursor: pointer;
        }

        .service-duration-fieldset .service-custom-select-duration .service-custom-trigger.is-empty span {
            color: #3B3731;
        }

        .service-duration-fieldset .service-custom-select--allow-empty .service-custom-trigger.is-empty .service-custom-chevron {
            display: none;
        }

        .service-duration-fieldset .service-custom-select-duration.is-open .service-custom-menu {
            z-index: 60;
        }

        .service-duration-fieldset .service-duration-by-size-row {
            position: relative;
            z-index: 1;
        }

        .service-duration-fieldset .service-duration-by-size-row:has(.service-custom-select.is-open) {
            z-index: 50;
        }

        .service-duration-fieldset [x-cloak] {
            display: none !important;
        }

        .service-duration-fieldset .service-duration-by-size-reveal {
            overflow: hidden;
        }

        .service-duration-fieldset .service-duration-by-size-enter {
            transition: opacity 260ms cubic-bezier(0.4, 0, 0.2, 1),
                max-height 300ms cubic-bezier(0.4, 0, 0.2, 1),
                transform 260ms cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .service-duration-fieldset .service-duration-by-size-enter-start {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }

        .service-duration-fieldset .service-duration-by-size-enter-end {
            opacity: 1;
            max-height: 320px;
            transform: translateY(0);
        }

        .service-duration-fieldset .service-duration-by-size-leave {
            transition: opacity 200ms cubic-bezier(0.4, 0, 0.2, 1),
                max-height 240ms cubic-bezier(0.4, 0, 0.2, 1),
                transform 200ms cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
        }

        .service-duration-fieldset .service-duration-by-size-leave-start {
            opacity: 1;
            max-height: 320px;
            transform: translateY(0);
        }

        .service-duration-fieldset .service-duration-by-size-leave-end {
            opacity: 0;
            max-height: 0;
            transform: translateY(-10px);
        }
    </style>
@endonce
