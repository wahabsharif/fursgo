{{-- <x-ui.range-date-calendar id="dashboard-availability-range" start-name="availability_start_date"
    end-name="availability_end_date" calendar-width="50rem" /> --}}

@props([
    'id' => 'range-calendar-' . uniqid(),
    'startName' => 'start_date',
    'endName' => 'end_date',
    'startValue' => null,
    'endValue' => null,
    'calendarWidth' => '100%',
])

@php
    $componentId = preg_replace('/[^A-Za-z0-9_-]/', '-', (string) $id);
@endphp

<div class="rdc" id="{{ $componentId }}" style="--rdc-width: {{ $calendarWidth }};" x-data="rangeDateCalendar({
    start: @js($startValue),
    end: @js($endValue),
    startName: @js($startName),
    endName: @js($endName),
})"
    x-init="init()">
    <input type="hidden" :name="startName" :value="startDate">
    <input type="hidden" :name="endName" :value="endDate">

    <div class="rdc-top" @click.outside="closeMonthDropdown()">
        <button type="button" class="rdc-nav-circle" @click="prevMonth()" aria-label="Previous month"><svg
                xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34" fill="none">
                <g filter="url(#filter0_d_3_2002)">
                    <circle cx="17" cy="13" r="13" fill="white" />
                    <circle cx="17" cy="13" r="12.5" stroke="#F5F5F5" />
                </g>
                <path d="M18.625 17.0625L14.5347 12.9722L18.5563 8.9505" stroke="#3B3731" stroke-linecap="round"
                    stroke-linejoin="round" />
                <defs>
                    <filter id="filter0_d_3_2002" x="0" y="0" width="34" height="34" filterUnits="userSpaceOnUse"
                        color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                        <feColorMatrix in="SourceAlpha" type="matrix"
                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                        <feOffset dy="4" />
                        <feGaussianBlur stdDeviation="2" />
                        <feComposite in2="hardAlpha" operator="out" />
                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_3_2002" />
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_3_2002" result="shape" />
                    </filter>
                </defs>
            </svg></button>
        <div class="rdc-title-wrap">
            <button type="button" class="rdc-range-title" @click="toggleMonthDropdown()"
                :aria-expanded="monthDropdownOpen ? 'true' : 'false'">
                <span x-text="rangeTitle"></span>
            </button>
            <div class="rdc-month-dropdown" x-cloak x-show="monthDropdownOpen"
                x-transition:enter="transition ease-out duration-180"
                x-transition:enter-start="opacity-0 -translate-y-2 scale-y-95"
                x-transition:enter-end="opacity-100 translate-y-0 scale-y-100"
                x-transition:leave="transition ease-in duration-140"
                x-transition:leave-start="opacity-100 translate-y-0 scale-y-100"
                x-transition:leave-end="opacity-0 -translate-y-2 scale-y-95">
                <div class="rdc-month-range-grid">
                    <div class="rdc-month-range-block">
                        <label class="rdc-month-range-label">From</label>
                        <div class="rdc-month-range-controls">
                            <div class="rdc-custom-select-wrap" @click.outside="closeFromMonthSelect()">
                                <button type="button" class="rdc-custom-select" @click="toggleFromMonthSelect()"
                                    :aria-expanded="fromMonthSelectOpen ? 'true' : 'false'">
                                    <span x-text="months[draftFromMonth]"></span>
                                </button>
                                <div class="rdc-custom-select-menu" x-cloak x-show="fromMonthSelectOpen">
                                    <template x-for="(month, index) in months" :key="'from-month-' + index">
                                        <button type="button" class="rdc-custom-select-option"
                                            :class="{ 'is-active': draftFromMonth === index }"
                                            @click="selectFromMonth(index)" x-text="month"></button>
                                    </template>
                                </div>
                            </div>
                            <div class="rdc-custom-select-wrap" @click.outside="closeFromYearSelect()">
                                <button type="button" class="rdc-custom-select" @click="toggleFromYearSelect()"
                                    :aria-expanded="fromYearSelectOpen ? 'true' : 'false'">
                                    <span x-text="draftFromYear"></span>
                                </button>
                                <div class="rdc-custom-select-menu" x-cloak x-show="fromYearSelectOpen">
                                    <template x-for="year in yearOptions" :key="'from-year-' + year">
                                        <button type="button" class="rdc-custom-select-option"
                                            :class="{ 'is-active': draftFromYear === year }"
                                            @click="selectFromYear(year)" x-text="year"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="rdc-month-range-block">
                        <label class="rdc-month-range-label">To</label>
                        <div class="rdc-month-range-controls">
                            <div class="rdc-custom-select-wrap" @click.outside="closeToMonthSelect()">
                                <button type="button" class="rdc-custom-select" @click="toggleToMonthSelect()"
                                    :aria-expanded="toMonthSelectOpen ? 'true' : 'false'">
                                    <span x-text="months[draftToMonth]"></span>
                                </button>
                                <div class="rdc-custom-select-menu" x-cloak x-show="toMonthSelectOpen">
                                    <template x-for="(month, index) in months" :key="'to-month-' + index">
                                        <button type="button" class="rdc-custom-select-option"
                                            :class="{ 'is-active': draftToMonth === index }"
                                            @click="selectToMonth(index)" x-text="month"></button>
                                    </template>
                                </div>
                            </div>
                            <div class="rdc-custom-select-wrap" @click.outside="closeToYearSelect()">
                                <button type="button" class="rdc-custom-select" @click="toggleToYearSelect()"
                                    :aria-expanded="toYearSelectOpen ? 'true' : 'false'">
                                    <span x-text="draftToYear"></span>
                                </button>
                                <div class="rdc-custom-select-menu" x-cloak x-show="toYearSelectOpen">
                                    <template x-for="year in yearOptions" :key="'to-year-' + year">
                                        <button type="button" class="rdc-custom-select-option"
                                            :class="{ 'is-active': draftToYear === year }" @click="selectToYear(year)"
                                            x-text="year"></button>
                                    </template>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rdc-month-dropdown-actions">
                    <button type="button" class="rdc-month-action-btn is-cancel"
                        @click="closeMonthDropdown()">Cancel</button>
                    <button type="button" class="rdc-month-action-btn is-apply"
                        @click="applyFromToMonths()">Apply</button>
                </div>
            </div>
        </div>
        <button type="button" class="rdc-nav-circle" @click="nextMonth()" aria-label="Next month">
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34"
                fill="none">
                <g filter="url(#filter0_d_3_2005)">
                    <circle cx="13" cy="13" r="13" transform="matrix(-1 0 0 1 30 0)" fill="white" />
                    <circle cx="13" cy="13" r="12.5" transform="matrix(-1 0 0 1 30 0)"
                        stroke="#F5F5F5" />
                </g>
                <path d="M15.375 17.0625L19.4653 12.9722L15.4437 8.9505" stroke="#3B3731" stroke-linecap="round"
                    stroke-linejoin="round" />
                <defs>
                    <filter id="filter0_d_3_2005" x="0" y="0" width="34" height="34"
                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                        <feColorMatrix in="SourceAlpha" type="matrix"
                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                        <feOffset dy="4" />
                        <feGaussianBlur stdDeviation="2" />
                        <feComposite in2="hardAlpha" operator="out" />
                        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_3_2005" />
                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_3_2005" result="shape" />
                    </filter>
                </defs>
            </svg>
        </button>
    </div>

    <div class="rdc-panel">
        <div class="rdc-month">
            <div class="rdc-month-header">
                <button type="button" class="rdc-nav-inline" @click="prevMonth()" aria-label="Previous month">
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34"
                        fill="none">
                        <g filter="url(#filter0_d_3_1905)">
                            <circle cx="17" cy="13" r="13" fill="white" />
                        </g>
                        <path d="M18.625 17.0625L14.5347 12.9722L18.5563 8.9505" stroke="#3B3731"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <defs>
                            <filter id="filter0_d_3_1905" x="0" y="0" width="34" height="34"
                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                <feOffset dy="4" />
                                <feGaussianBlur stdDeviation="2" />
                                <feComposite in2="hardAlpha" operator="out" />
                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                <feBlend mode="normal" in2="BackgroundImageFix"
                                    result="effect1_dropShadow_3_1905" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_3_1905"
                                    result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </button>
                <strong x-text="monthLabel(leftDate)"></strong>
                <div></div>
            </div>
            <div class="rdc-weekdays">
                <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
            </div>
            <div class="rdc-grid">
                <template x-for="day in monthGrid(leftDate)" :key="'l-' + day.key">
                    <button type="button" class="rdc-day" :class="dayClasses(day)" :disabled="!day.date"
                        @mouseenter="setHoverDate(day.date)" @mouseleave="clearHoverDate()"
                        @click="pickDate(day.date)">
                        <span class="rdc-day-label" x-text="day.label"></span>
                    </button>
                </template>
            </div>
        </div>

        <div class="rdc-month">
            <div class="rdc-month-header">
                <div></div>
                <strong x-text="monthLabel(rightDate)"></strong>
                <button type="button" class="rdc-nav-inline" @click="nextMonth()" aria-label="Next month">
                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34" viewBox="0 0 34 34"
                        fill="none">
                        <g filter="url(#filter0_d_3_1955)">
                            <circle cx="13" cy="13" r="13" transform="matrix(-1 0 0 1 30 0)"
                                fill="white" />
                        </g>
                        <path d="M15.375 17.0625L19.4653 12.9722L15.4437 8.9505" stroke="#3B3731"
                            stroke-linecap="round" stroke-linejoin="round" />
                        <defs>
                            <filter id="filter0_d_3_1955" x="0" y="0" width="34" height="34"
                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                <feColorMatrix in="SourceAlpha" type="matrix"
                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                <feOffset dy="4" />
                                <feGaussianBlur stdDeviation="2" />
                                <feComposite in2="hardAlpha" operator="out" />
                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                <feBlend mode="normal" in2="BackgroundImageFix"
                                    result="effect1_dropShadow_3_1955" />
                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_3_1955"
                                    result="shape" />
                            </filter>
                        </defs>
                    </svg>
                </button>
            </div>
            <div class="rdc-weekdays">
                <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
            </div>
            <div class="rdc-grid">
                <template x-for="day in monthGrid(rightDate)" :key="'r-' + day.key">
                    <button type="button" class="rdc-day" :class="dayClasses(day)" :disabled="!day.date"
                        @mouseenter="setHoverDate(day.date)" @mouseleave="clearHoverDate()"
                        @click="pickDate(day.date)">
                        <span class="rdc-day-label" x-text="day.label"></span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</div>

<style>
    .rdc {
        width: var(--rdc-width, 100%);
        max-width: 100%;
        color: #3b3731;
        font-family: Lato, sans-serif;
    }

    .rdc-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 14px;
        position: relative;
    }

    .rdc-title-wrap {
        position: relative;
    }

    .rdc-range-title {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .rdc-month-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%);
        width: 280px;
        border: 1px solid #e9dfd1;
        border-radius: 10px;
        background: #fff;
        box-shadow: 0 12px 24px rgba(0, 0, 0, 0.08);
        padding: 10px;
        z-index: 20;
    }

    .rdc-month-range-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 10px;
    }

    .rdc-month-range-block {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .rdc-month-range-label {
        color: #6f685e;
        font-family: Lato;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .rdc-month-range-controls {
        display: grid;
        grid-template-columns: 1fr;
        gap: 6px;
    }

    .rdc-month-select,
    .rdc-year-select {
        width: 100%;
        border: 1px solid #e4ddd2;
        border-radius: 8px;
        background-color: #fff;
        background-image: linear-gradient(45deg, transparent 50%, #b5aa99 50%),
            linear-gradient(135deg, #b5aa99 50%, transparent 50%);
        background-position: calc(100% - 14px) calc(50% - 2px), calc(100% - 9px) calc(50% - 2px);
        background-size: 6px 6px, 6px 6px;
        background-repeat: no-repeat;
        color: #3B3731;
        font-family: Lato;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.3;
        padding: 9px 30px 9px 10px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        cursor: pointer;
        transition: border-color 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
    }

    .rdc-month-select:hover,
    .rdc-year-select:hover {
        border-color: #d3c7b7;
        background-color: #fffcf8;
    }

    .rdc-month-select:focus,
    .rdc-year-select:focus {
        outline: none;
        border-color: #FFC97A;
        box-shadow: 0 0 0 3px rgba(255, 201, 122, 0.22);
    }

    .rdc-month-select option,
    .rdc-year-select option {
        color: #3B3731;
        background: #fff;
        font-family: Lato, sans-serif;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 10px;
    }

    .rdc-custom-select-wrap {
        position: relative;
    }

    .rdc-custom-select {
        width: 100%;
        border: 1px solid #e4ddd2;
        border-radius: 8px;
        background-color: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 13px;
        font-weight: 600;
        line-height: 1.3;
        padding: 9px 30px 9px 10px;
        text-align: left;
        cursor: pointer;
        position: relative;
        transition: border-color 160ms ease, box-shadow 160ms ease, background-color 160ms ease;
    }

    .rdc-custom-select::before,
    .rdc-custom-select::after {
        content: '';
        position: absolute;
        right: 12px;
        width: 6px;
        height: 6px;
        border-bottom: 1.5px solid #b5aa99;
        border-right: 1.5px solid #b5aa99;
        transform: rotate(45deg);
        top: calc(50% - 4px);
        pointer-events: none;
    }

    .rdc-custom-select:hover {
        border-color: #d3c7b7;
        background-color: #fffcf8;
    }

    .rdc-custom-select:focus {
        outline: none;
        border-color: #FFC97A;
        box-shadow: 0 0 0 3px rgba(255, 201, 122, 0.22);
    }

    .rdc-custom-select-menu {
        position: absolute;
        left: 0;
        right: 0;
        top: calc(100% + 4px);
        max-height: 180px;
        overflow-y: auto;
        border: 1px solid #e4ddd2;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
        z-index: 30;
        padding: 4px;
        scrollbar-width: thin;
        scrollbar-color: #FFC97A transparent;
    }

    .rdc-custom-select-menu::-webkit-scrollbar {
        width: 6px;
    }

    .rdc-custom-select-menu::-webkit-scrollbar-track {
        background: transparent;
    }

    .rdc-custom-select-menu::-webkit-scrollbar-thumb {
        background: #FFC97A;
        border-radius: 9999px;
    }

    .rdc-custom-select-option {
        width: 100%;
        border: none;
        background: transparent;
        text-align: left;
        color: #3B3731;
        font-family: Lato;
        font-size: 13px;
        font-weight: 600;
        padding: 7px 8px;
        border-radius: 6px;
        cursor: pointer;
    }

    .rdc-custom-select-option:hover,
    .rdc-custom-select-option.is-active {
        background: #fff4e3;
    }

    .rdc-month-dropdown-actions {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .rdc-month-action-btn {
        border: 1px solid transparent;
        border-radius: 8px;
        padding: 7px 12px;
        font-family: Lato;
        font-size: 13px;
        font-weight: 700;
        cursor: pointer;
    }

    .rdc-month-action-btn.is-cancel {
        background: #f7f4ef;
        color: #6c655b;
        border-color: #e5ddcf;
    }

    .rdc-month-action-btn.is-apply {
        background: #FFC97A;
        color: #fff;
    }

    .rdc-nav-circle {
        border: none;
        background: transparent;
        padding: 0;
        cursor: pointer;
    }

    .rdc-nav-circle>svg {
        margin-top: 10px;
    }

    .rdc-panel {
        border: 1px solid #ccc3b7;
        border-radius: 12px;
        background: #fff;
        padding: 14px 16px;
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 20px;
    }

    .rdc-month-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 8px;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .rdc-nav-inline {
        border: none;
        background: transparent;
        padding: 0;
        margin: 0;
        cursor: pointer;
    }

    .rdc-nav-inline>svg {
        margin-top: 10px;
    }

    .rdc-weekdays,
    .rdc-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .rdc-weekdays {
        margin-bottom: 6px;
    }

    .rdc-weekdays span {
        text-align: center;
        color: #9C9790;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .rdc-day {
        border: none;
        background: transparent;
        width: 100%;
        height: 34px;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        border-radius: 0;
        transition: background-color 180ms ease, color 180ms ease;
    }

    .rdc-day:disabled {
        cursor: default;
        color: transparent;
    }

    .rdc-day.is-selected {
        background: transparent;
    }

    .rdc-day.is-in-range {
        background: rgba(255, 201, 122, 0.20);
    }

    .rdc-day.is-range-start {
        background: linear-gradient(to right, transparent 50%, rgba(255, 201, 122, 0.20) 50%);
    }

    .rdc-day.is-range-end {
        background: linear-gradient(to left, transparent 50%, rgba(255, 201, 122, 0.20) 50%);
    }

    .rdc-day-label {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: background-color 180ms ease, color 180ms ease, transform 180ms ease, box-shadow 180ms ease;
    }

    .rdc-day.is-selected .rdc-day-label {
        background: #FFC97A;
        color: #fff;
        transform: scale(1.02);
        box-shadow: 0 2px 8px rgba(255, 201, 122, 0.35);
    }

    .rdc-day:not(:disabled):hover .rdc-day-label {
        transform: scale(1.05);
    }
</style>

<script>
    (function() {
        if (window.rangeDateCalendar) return;

        const MONTHS = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September',
            'October', 'November', 'December'
        ];

        window.rangeDateCalendar = function(config) {
            return {
                months: MONTHS,
                yearOptions: [],
                startName: config.startName || 'start_date',
                endName: config.endName || 'end_date',
                startDate: config.start || null,
                endDate: config.end || null,
                hoverDate: null,
                monthDropdownOpen: false,
                fromMonthSelectOpen: false,
                toMonthSelectOpen: false,
                fromYearSelectOpen: false,
                toYearSelectOpen: false,
                draftFromMonth: 0,
                draftFromYear: new Date().getFullYear(),
                draftToMonth: 1,
                draftToYear: new Date().getFullYear(),
                leftDate: new Date(),
                rightDate: new Date(),
                init() {
                    const seed = this.startDate ? this.isoToDate(this.startDate) : new Date();
                    this.leftDate = new Date(seed.getFullYear(), seed.getMonth(), 1);
                    this.rightDate = new Date(seed.getFullYear(), seed.getMonth() + 1, 1);
                    const currentYear = new Date().getFullYear();
                    this.yearOptions = Array.from({
                        length: 16
                    }, (_, idx) => currentYear + idx);
                    this.syncDraftMonthsFromState();
                },
                get rangeTitle() {
                    return `${MONTHS[this.leftDate.getMonth()]} ${this.leftDate.getFullYear()} - ${MONTHS[this.rightDate.getMonth()]} ${this.rightDate.getFullYear()}`;
                },
                monthLabel(baseDate) {
                    return `${MONTHS[baseDate.getMonth()]} ${baseDate.getFullYear()}`;
                },
                prevMonth() {
                    this.leftDate = new Date(this.leftDate.getFullYear(), this.leftDate.getMonth() - 1, 1);
                    this.rightDate = new Date(this.rightDate.getFullYear(), this.rightDate.getMonth() - 1, 1);
                    this.syncDraftMonthsFromState();
                },
                nextMonth() {
                    this.leftDate = new Date(this.leftDate.getFullYear(), this.leftDate.getMonth() + 1, 1);
                    this.rightDate = new Date(this.rightDate.getFullYear(), this.rightDate.getMonth() + 1, 1);
                    this.syncDraftMonthsFromState();
                },
                toggleMonthDropdown() {
                    this.monthDropdownOpen = !this.monthDropdownOpen;
                    if (this.monthDropdownOpen) {
                        this.syncDraftMonthsFromState();
                    } else {
                        this.closeMonthSelects();
                    }
                },
                closeMonthDropdown() {
                    this.monthDropdownOpen = false;
                    this.closeMonthSelects();
                },
                closeMonthSelects() {
                    this.fromMonthSelectOpen = false;
                    this.toMonthSelectOpen = false;
                    this.fromYearSelectOpen = false;
                    this.toYearSelectOpen = false;
                },
                toggleFromMonthSelect() {
                    this.fromMonthSelectOpen = !this.fromMonthSelectOpen;
                    if (this.fromMonthSelectOpen) {
                        this.toMonthSelectOpen = false;
                        this.fromYearSelectOpen = false;
                        this.toYearSelectOpen = false;
                    }
                },
                closeFromMonthSelect() {
                    this.fromMonthSelectOpen = false;
                },
                toggleToMonthSelect() {
                    this.toMonthSelectOpen = !this.toMonthSelectOpen;
                    if (this.toMonthSelectOpen) {
                        this.fromMonthSelectOpen = false;
                        this.fromYearSelectOpen = false;
                        this.toYearSelectOpen = false;
                    }
                },
                closeToMonthSelect() {
                    this.toMonthSelectOpen = false;
                },
                toggleFromYearSelect() {
                    this.fromYearSelectOpen = !this.fromYearSelectOpen;
                    if (this.fromYearSelectOpen) {
                        this.fromMonthSelectOpen = false;
                        this.toMonthSelectOpen = false;
                        this.toYearSelectOpen = false;
                    }
                },
                closeFromYearSelect() {
                    this.fromYearSelectOpen = false;
                },
                toggleToYearSelect() {
                    this.toYearSelectOpen = !this.toYearSelectOpen;
                    if (this.toYearSelectOpen) {
                        this.fromMonthSelectOpen = false;
                        this.toMonthSelectOpen = false;
                        this.fromYearSelectOpen = false;
                    }
                },
                closeToYearSelect() {
                    this.toYearSelectOpen = false;
                },
                selectFromMonth(index) {
                    this.draftFromMonth = Number(index);
                    this.fromMonthSelectOpen = false;
                },
                selectToMonth(index) {
                    this.draftToMonth = Number(index);
                    this.toMonthSelectOpen = false;
                },
                selectFromYear(year) {
                    this.draftFromYear = Number(year);
                    this.fromYearSelectOpen = false;
                },
                selectToYear(year) {
                    this.draftToYear = Number(year);
                    this.toYearSelectOpen = false;
                },
                syncDraftMonthsFromState() {
                    this.draftFromMonth = this.leftDate.getMonth();
                    this.draftFromYear = this.leftDate.getFullYear();
                    this.draftToMonth = this.rightDate.getMonth();
                    this.draftToYear = this.rightDate.getFullYear();
                },
                applyFromToMonths() {
                    const fromDate = new Date(this.draftFromYear, this.draftFromMonth, 1);
                    const toDate = new Date(this.draftToYear, this.draftToMonth, 1);
                    if (toDate < fromDate) {
                        this.rightDate = new Date(this.draftFromYear, this.draftFromMonth + 1, 1);
                    } else {
                        this.rightDate = toDate;
                    }
                    this.leftDate = fromDate;
                    this.closeMonthDropdown();
                },
                monthGrid(baseDate) {
                    const year = baseDate.getFullYear();
                    const month = baseDate.getMonth();
                    const first = new Date(year, month, 1);
                    const total = new Date(year, month + 1, 0).getDate();
                    const mondayIndex = (first.getDay() + 6) % 7;
                    const cells = [];
                    for (let i = 0; i < mondayIndex; i++) {
                        cells.push({
                            key: `e-${year}-${month}-${i}`,
                            label: '',
                            date: null
                        });
                    }
                    for (let day = 1; day <= total; day++) {
                        const date = new Date(year, month, day);
                        cells.push({
                            key: this.dateToIso(date),
                            label: day,
                            date: this.dateToIso(date)
                        });
                    }
                    while (cells.length % 7 !== 0) {
                        cells.push({
                            key: `t-${year}-${month}-${cells.length}`,
                            label: '',
                            date: null
                        });
                    }
                    return cells;
                },
                pickDate(dateIso) {
                    if (!this.startDate || (this.startDate && this.endDate)) {
                        this.startDate = dateIso;
                        this.endDate = null;
                        this.hoverDate = null;
                        return;
                    }
                    if (dateIso < this.startDate) {
                        this.endDate = this.startDate;
                        this.startDate = dateIso;
                    } else {
                        this.endDate = dateIso;
                    }
                    this.hoverDate = null;
                    window.dispatchEvent(new CustomEvent('range-calendar-changed', {
                        detail: {
                            start: this.startDate,
                            end: this.endDate
                        },
                    }));
                },
                setHoverDate(dateIso) {
                    if (!this.startDate || this.endDate || !dateIso) return;
                    this.hoverDate = dateIso;
                },
                clearHoverDate() {
                    if (this.endDate) return;
                    this.hoverDate = null;
                },
                dayClasses(day) {
                    if (!day.date) return '';
                    const previewEnd = this.endDate ?? this.hoverDate;
                    const rangeStart = this.startDate && previewEnd && previewEnd < this.startDate ?
                        previewEnd : this.startDate;
                    const rangeEnd = this.startDate && previewEnd && previewEnd < this.startDate ? this
                        .startDate : previewEnd;
                    const inRange = rangeStart && rangeEnd && day.date > rangeStart && day.date < rangeEnd;
                    const isStart = this.startDate === day.date;
                    const isEnd = this.endDate ? this.endDate === day.date : this.hoverDate === day.date;
                    const hasRange = !!rangeStart && !!rangeEnd && rangeStart !== rangeEnd;
                    return {
                        'is-selected': isStart || isEnd,
                        'is-in-range': inRange,
                        'is-range-start': isStart && hasRange,
                        'is-range-end': isEnd && hasRange,
                    };
                },
                dateToIso(date) {
                    const y = date.getFullYear();
                    const m = String(date.getMonth() + 1).padStart(2, '0');
                    const d = String(date.getDate()).padStart(2, '0');
                    return `${y}-${m}-${d}`;
                },
                isoToDate(iso) {
                    const [y, m, d] = String(iso).split('-').map(Number);
                    return new Date(y, (m || 1) - 1, d || 1);
                },
            };
        };
    })();
</script>
