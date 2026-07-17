@props([
    'promoServiceOptions' => [],
    'promoPetTypeOptions' => ['Cat', 'Dog', 'Other'],
    'promoPetSizeOptions' => ['Small 0 - 7 kg', 'Medium 8 - 18 kg', 'Large 19+ kg'],
    'promoStartDate' => '',
    'promoEndDate' => '',
    'promoEditingId' => null,
])

@php
    use App\Models\PromoCode;

    $promoServiceOptions = array_values(
        array_filter(
            array_map(
                static fn($option) => trim((string) $option),
                is_array($promoServiceOptions) ? $promoServiceOptions : [],
            ),
        ),
    );
    $promoPetTypeOptions = is_array($promoPetTypeOptions) ? $promoPetTypeOptions : ['Cat', 'Dog', 'Other'];
    $promoPetSizeOptions = is_array($promoPetSizeOptions)
        ? $promoPetSizeOptions
        : ['Small 0 - 7 kg', 'Medium 8 - 18 kg', 'Large 19+ kg'];
    $promoStartDate = $promoStartDate ?: '';
    $promoEndDate = $promoEndDate ?: '';
    $promoEditingId = $promoEditingId ?? null;
    $promoCalendarId = 'promo-validity-range';
    $promoSingleCalendarId = 'promo-validity-single';
@endphp

<section class="mh-promo-form" aria-label="Promo creation form" x-data="{
    openDiscountType: false,
    openService: false,
    openPetType: false,
    openPetSize: false,
    calendarId: @js($promoCalendarId),
    singleCalendarId: @js($promoSingleCalendarId),
    datePickerOpen: false,
    datePickerTarget: 'start',
    localStart: @js($promoStartDate ?: ''),
    localEnd: @js($promoEndDate ?: ''),
    closeSelects() {
        this.openDiscountType = false;
        this.openService = false;
        this.openPetType = false;
        this.openPetSize = false;
    },
    toggleServiceSelect() {
        if (@js($promoServiceOptions === [])) {
            return;
        }
        const willOpen = !this.openService;
        this.closeSelects();
        if (willOpen && $wire.allServices) {
            $wire.set('allServices', false, false);
            const first = @js($promoServiceOptions[0] ?? '');
            if (first && !$wire.selectedService) {
                $wire.set('selectedService', first, false);
            }
        }
        this.openService = willOpen;
    },
    togglePetTypeSelect() {
        const willOpen = !this.openPetType;
        this.closeSelects();
        if (willOpen && $wire.allPetTypes) {
            $wire.set('allPetTypes', false, false);
            if (!$wire.selectedPetType) {
                $wire.set('selectedPetType', @js($promoPetTypeOptions[0] ?? 'Cat'), false);
            }
        }
        this.openPetType = willOpen;
    },
    togglePetSizeSelect() {
        const willOpen = !this.openPetSize;
        this.closeSelects();
        if (willOpen && $wire.allPetSizes) {
            $wire.set('allPetSizes', false, false);
            if (!$wire.selectedPetSize) {
                $wire.set('selectedPetSize', @js($promoPetSizeOptions[0] ?? 'Small 0 - 7 kg'), false);
            }
        }
        this.openPetSize = willOpen;
    },
    activeCalendarId() {
        return $wire.noEndDate ? this.singleCalendarId : this.calendarId;
    },
    discountTypeLabel(type) {
        return type === @js(PromoCode::DISCOUNT_TYPE_POUND) ? '£ Off' : '% Off';
    },
    isPoundDiscount(type) {
        return type === @js(PromoCode::DISCOUNT_TYPE_POUND);
    },
    amountInputWidth() {
        return `width: ${Math.max(String($wire.discountAmount ?? '0').length, 1)}ch`;
    },
    formatDisplay(iso) {
        if (!iso) return 'Select date';
        const parts = String(iso).split('-').map(Number);
        if (parts.length !== 3 || !parts[0] || !parts[1] || !parts[2]) return 'Select date';
        const dt = new Date(parts[0], parts[1] - 1, parts[2]);
        if (Number.isNaN(dt.getTime())) return 'Select date';
        return dt.toLocaleDateString('en-GB', { day: '2-digit', month: 'short', year: 'numeric' });
    },
    syncToWire() {
        $wire.set('startDate', this.localStart || '', false);
        $wire.set('endDate', $wire.noEndDate ? '' : (this.localEnd || ''), false);
    },
    syncCalendar(componentId = null) {
        const id = componentId || this.activeCalendarId();
        window.dispatchEvent(new CustomEvent('range-calendar-set', {
            detail: {
                componentId: id,
                start: this.localStart || '',
                end: $wire.noEndDate ? '' : (this.localEnd || ''),
                pickMode: $wire.noEndDate ? 'start' : (this.datePickerTarget === 'end' ? 'end' : 'range'),
            },
        }));
    },
    onCalendarChanged(event) {
        const detail = event?.detail || {};
        if (detail.componentId !== this.activeCalendarId()) return;
        this.localStart = detail.start || '';
        this.localEnd = $wire.noEndDate ? '' : (detail.end || '');
        this.syncToWire();
    },
    setNoEndDate(checked) {
        $wire.set('noEndDate', checked, false);
        if (checked) {
            this.localEnd = '';
            $wire.set('endDate', '', false);
            this.datePickerTarget = 'start';
        }
        this.$nextTick(() => {
            this.syncCalendar(checked ? this.singleCalendarId : this.calendarId);
        });
    },
    openDatePicker(target = 'start') {
        if ($wire.noEndDate && target === 'end') return;
        this.datePickerTarget = target === 'end' ? 'end' : 'start';
        this.datePickerOpen = true;
        this.$nextTick(() => this.syncCalendar());
    },
    closeDatePicker() {
        this.datePickerOpen = false;
        this.datePickerTarget = 'start';
    },
    toggleDatePicker(target = 'start') {
        if (this.datePickerOpen && this.datePickerTarget === target) {
            this.closeDatePicker();
            return;
        }
        this.openDatePicker(target);
    },
}"
    x-on:range-calendar-changed.window="onCalendarChanged($event)" @keydown.escape.window="closeDatePicker()">
    <form class="mh-promo-form__body" wire:submit.prevent="savePromo">
        <div class="mh-promo-form__section">
            <div class="mh-promo-form__section-head">
                <h3 class="mh-promo-form__heading">Promo Code</h3>
                <button type="button" class="mh-promo-publish-btn"
                    @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } }))"
                    wire:click="savePromo" wire:loading.attr="disabled" wire:target="savePromo">
                    <span wire:loading.remove wire:target="savePromo">Save &amp; Publish Promo</span>
                    <span class="mh-promo-publish-btn__loading" wire:loading.flex wire:target="savePromo">
                        <span class="mh-promo-publish-spinner" aria-hidden="true"></span>
                        Saving
                    </span>
                </button>
            </div>
            <div class="mh-promo-form__grid mh-promo-form__grid--2">
                <label class="service-field mh-promo-form__code-field">
                    <span>Discount Code</span>
                    <input type="text" placeholder="NY20OFF" wire:model="discountCode" maxlength="80"
                        autocomplete="off" class="mh-promo-form__code-input" inputmode="text" spellcheck="false"
                        @keydown.space.prevent />
                    @error('discountCode')
                        <small class="mh-promo-form__error">{{ $message }}</small>
                    @enderror
                </label>
                <label class="service-field mh-promo-form__desc-field">
                    <span class="mh-promo-form__label--muted">Description (Optional)</span>
                    <input type="text" placeholder="New Years Discount" wire:model="description" maxlength="255"
                        style="max-width:55rem;" />
                    @error('description')
                        <small class="mh-promo-form__error">{{ $message }}</small>
                    @enderror
                </label>
            </div>
        </div>

        <div class="mh-promo-form__section">
            <h3 class="mh-promo-form__heading">Validity</h3>
            <div class="mh-promo-form__validity" @click.outside="closeDatePicker()">
                <div class="mh-promo-form__validity-dates">
                    <div class="service-field mh-promo-form__date-field">
                        <span>Date From</span>
                        <button type="button" class="mh-promo-form__date-display"
                            :class="{ 'is-open': datePickerOpen && datePickerTarget === 'start' }"
                            @click.stop="toggleDatePicker('start')"
                            :aria-expanded="(datePickerOpen && datePickerTarget === 'start').toString()"
                            aria-haspopup="dialog">
                            <span class="mh-promo-form__date-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14"
                                    viewBox="0 0 15 14" fill="none">
                                    <path
                                        d="M0.5 6.83383C0.5 4.32006 0.5 3.06284 1.3162 2.28224C2.13239 1.50164 3.44513 1.50098 6.0713 1.50098H8.85695C11.4831 1.50098 12.7966 1.50098 13.612 2.28224C14.4275 3.0635 14.4282 4.32006 14.4282 6.83383V8.16705C14.4282 10.6808 14.4282 11.938 13.612 12.7186C12.7959 13.4992 11.4831 13.4999 8.85695 13.4999H6.0713C3.44513 13.4999 2.13169 13.4999 1.3162 12.7186C0.500696 11.9374 0.5 10.6808 0.5 8.16705V6.83383Z"
                                        stroke="#3B3731" />
                                    <path d="M3.98212 1.49991V0.5M10.9462 1.49991V0.5M0.848267 4.83295H14.0801"
                                        stroke="#3B3731" stroke-linecap="round" />
                                </svg>
                            </span>
                            <span x-text="formatDisplay(localStart)" :class="{ 'is-placeholder': !localStart }"></span>
                        </button>
                        @error('startDate')
                            <small class="mh-promo-form__error">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="service-field mh-promo-form__date-field" :class="{ 'is-disabled': $wire.noEndDate }">
                        <span>Date To</span>
                        <button type="button" class="mh-promo-form__date-display"
                            :class="{ 'is-open': datePickerOpen && datePickerTarget === 'end' && !$wire.noEndDate }"
                            :style="$wire.noEndDate ? 'background: #F7F7F7;' : null"
                            @click.stop="!$wire.noEndDate && toggleDatePicker('end')" :disabled="$wire.noEndDate"
                            :aria-expanded="(datePickerOpen && datePickerTarget === 'end').toString()"
                            aria-haspopup="dialog">
                            <span class="mh-promo-form__date-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14"
                                    viewBox="0 0 15 14" fill="none">
                                    <path
                                        d="M0.5 6.83383C0.5 4.32006 0.5 3.06284 1.3162 2.28224C2.13239 1.50164 3.44513 1.50098 6.0713 1.50098H8.85695C11.4831 1.50098 12.7966 1.50098 13.612 2.28224C14.4275 3.0635 14.4282 4.32006 14.4282 6.83383V8.16705C14.4282 10.6808 14.4282 11.938 13.612 12.7186C12.7959 13.4992 11.4831 13.4999 8.85695 13.4999H6.0713C3.44513 13.4999 2.13169 13.4999 1.3162 12.7186C0.500696 11.9374 0.5 10.6808 0.5 8.16705V6.83383Z"
                                        stroke="#3B3731" />
                                    <path d="M3.98212 1.49991V0.5M10.9462 1.49991V0.5M0.848267 4.83295H14.0801"
                                        stroke="#3B3731" stroke-linecap="round" />
                                </svg>
                            </span>
                            <span x-text="$wire.noEndDate ? 'Ongoing' : formatDisplay(localEnd)"
                                :class="{ 'is-placeholder': !$wire.noEndDate && !localEnd }"></span>
                        </button>
                        @error('endDate')
                            <small class="mh-promo-form__error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <label class="mh-promo-form__dot-label mh-promo-form__dot-label--validity">
                    <span class="service-policies-fee-dot">
                        <input type="checkbox" :checked="$wire.noEndDate" @change="setNoEndDate($event.target.checked)"
                            aria-label="No end date">
                        <span aria-hidden="true"></span>
                    </span>
                    <span>No End Date</span>
                </label>

                <div class="mh-promo-date-picker" data-mh-promo-date-picker x-cloak x-show="datePickerOpen"
                    :class="{ 'mh-promo-date-picker--single': $wire.noEndDate }"
                    x-transition:enter="mh-promo-date-picker-anim"
                    x-transition:enter-start="mh-promo-date-picker-anim-start"
                    x-transition:enter-end="mh-promo-date-picker-anim-end"
                    x-transition:leave="mh-promo-date-picker-anim"
                    x-transition:leave-start="mh-promo-date-picker-anim-end"
                    x-transition:leave-end="mh-promo-date-picker-anim-start" @click.stop role="dialog"
                    :aria-label="$wire.noEndDate ? 'Promo start date' : 'Promo validity date range'">
                    <div x-show="!$wire.noEndDate" wire:ignore
                        wire:key="promo-cal-range-{{ $promoEditingId ?? 'new' }}">
                        <x-ui.range-date-calendar :id="$promoCalendarId" start-name="promo_start_date"
                            end-name="promo_end_date" :start-value="$promoStartDate ?: null" :end-value="$promoEndDate ?: null" calendar-width="100%" />
                    </div>
                    <div x-show="$wire.noEndDate" wire:ignore
                        wire:key="promo-cal-single-{{ $promoEditingId ?? 'new' }}">
                        <x-ui.range-date-calendar :id="$promoSingleCalendarId" start-name="promo_start_date_single"
                            end-name="promo_end_date_single" :start-value="$promoStartDate ?: null" :end-value="null" calendar-width="100%"
                            :single="true" />
                    </div>
                    <div class="mh-promo-date-picker__actions">
                        <button type="button" @click="closeDatePicker()">Done</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="mh-promo-form__section">
            <h3 class="mh-promo-form__heading">Discount Amount</h3>
            <div class="mh-promo-form__grid mh-promo-form__grid--discount">
                <label class="service-field">
                    <span>Discount Type</span>
                    <div class="service-custom-select mh-promo-form__discount-type"
                        :class="{ 'is-open': openDiscountType }" @keydown.escape.window="openDiscountType = false"
                        @click.outside="openDiscountType = false">
                        <button type="button" class="service-custom-trigger" style="background: #F7F7F7;"
                            @click="openDiscountType = !openDiscountType"
                            :aria-expanded="openDiscountType.toString()">
                            <span x-text="discountTypeLabel($wire.discountType)"></span>
                            <svg class="service-custom-chevron" xmlns="http://www.w3.org/2000/svg" width="11"
                                height="6" viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="service-custom-menu" x-show="openDiscountType" x-cloak @click.stop>
                            <button type="button" class="service-custom-option"
                                :class="{ 'is-active': $wire.discountType === @js(PromoCode::DISCOUNT_TYPE_PERCENT) }"
                                @click="$wire.set('discountType', @js(PromoCode::DISCOUNT_TYPE_PERCENT)); openDiscountType = false">
                                <span>% Off</span>
                            </button>
                            <button type="button" class="service-custom-option"
                                :class="{ 'is-active': $wire.discountType === @js(PromoCode::DISCOUNT_TYPE_POUND) }"
                                @click="$wire.set('discountType', @js(PromoCode::DISCOUNT_TYPE_POUND)); openDiscountType = false">
                                <span>£ Off</span>
                            </button>
                        </div>
                    </div>
                    @error('discountType')
                        <small class="mh-promo-form__error">{{ $message }}</small>
                    @enderror
                </label>

                <div class="service-field mh-promo-form__amount-field">
                    <span>Amount</span>
                    <div class="mh-promo-form__amount"
                        :class="isPoundDiscount($wire.discountType) ? 'mh-promo-form__amount--pound' :
                            'mh-promo-form__amount--percent'">
                        <div class="mh-promo-form__amount-value">
                            <span class="mh-promo-form__amount-affix" x-show="isPoundDiscount($wire.discountType)"
                                x-cloak aria-hidden="true">£</span>
                            <input type="number" min="0" step="1" inputmode="decimal"
                                wire:model.live="discountAmount" x-ref="discountAmountInput"
                                :style="amountInputWidth()" aria-label="Discount amount" />
                            <span class="mh-promo-form__amount-affix" x-show="!isPoundDiscount($wire.discountType)"
                                x-cloak aria-hidden="true">%</span>
                        </div>
                        <div class="mh-promo-form__steppers">
                            <button type="button" class="mh-promo-form__stepper-btn"
                                aria-label="Increase discount amount"
                                @click.stop="
                                    const el = $refs.discountAmountInput;
                                    if (!el) return;
                                    el.stepUp();
                                    el.dispatchEvent(new Event('input', { bubbles: true }));
                                ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6"
                                    viewBox="0 0 10 6" fill="none" aria-hidden="true">
                                    <path d="M1 5L5 1L9 5" stroke="#3B3731" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                            <button type="button" class="mh-promo-form__stepper-btn"
                                aria-label="Decrease discount amount"
                                @click.stop="
                                    const el = $refs.discountAmountInput;
                                    if (!el) return;
                                    el.stepDown();
                                    el.dispatchEvent(new Event('input', { bubbles: true }));
                                ">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6"
                                    viewBox="0 0 10 6" fill="none" aria-hidden="true">
                                    <path d="M1 1L5 5L9 1" stroke="#3B3731" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    @error('discountAmount')
                        <small class="mh-promo-form__error">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mh-promo-form__section">
            <h3 class="mh-promo-form__heading">Service / Add-ons Discount</h3>
            <div class="mh-promo-form__either-or">
                <div class="mh-promo-form__all-option">
                    <span class="mh-promo-form__all-label">All Services</span>
                    <div class="mh-promo-form__all-controls">
                        <label class="mh-promo-form__dot-label">
                            <span class="service-policies-fee-dot">
                                <input type="checkbox" wire:model.live="allServices" aria-label="All services">
                                <span aria-hidden="true"></span>
                            </span>
                        </label>
                        <label class="ma-switch" style="height: 24px;">
                            <input type="checkbox" wire:model.live="allServices" aria-label="Toggle all services">
                            <span class="ma-switch-slider"></span>
                            <span class="ma-switch-check-icon" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none">
                                    <path
                                        d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                        fill="white" />
                                </svg>
                            </span>
                        </label>
                    </div>
                </div>
                <span class="mh-promo-form__or">or</span>
                <div class="service-field mh-promo-form__select-field" :class="{ 'is-disabled': $wire.allServices }">
                    <span :class="{ 'mh-promo-form__label--muted': $wire.allServices }">Select Service Type</span>
                    <div class="service-custom-select" :class="{ 'is-open': openService }"
                        @keydown.escape.window="openService = false" @click.outside="openService = false">
                        <button type="button" class="service-custom-trigger" @click.stop="toggleServiceSelect()"
                            :aria-expanded="openService.toString()" :disabled="@js($promoServiceOptions === [])">
                            <span x-text="$wire.selectedService || @js($promoServiceOptions === [] ? 'No services found' : 'Select service')"
                                :class="{ 'is-placeholder': !$wire.selectedService }"></span>
                            <svg class="service-custom-chevron" xmlns="http://www.w3.org/2000/svg" width="11"
                                height="6" viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="service-custom-menu" x-show="openService" x-cloak @click.stop>
                            @forelse ($promoServiceOptions as $option)
                                <button type="button" class="service-custom-option"
                                    :class="{ 'is-active': $wire.selectedService === @js($option) }"
                                    @click="$wire.set('selectedService', @js($option)); $wire.set('allServices', false); openService = false">
                                    <span>{{ $option }}</span>
                                </button>
                            @empty
                                <div class="service-custom-option" aria-disabled="true">No services found</div>
                            @endforelse
                        </div>
                    </div>
                    @error('selectedService')
                        <small class="mh-promo-form__error">{{ $message }}</small>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mh-promo-form__section">
            <h3 class="mh-promo-form__heading">Pet Types Discount</h3>
            <div class="mh-promo-form__either-or-stack">
                <div class="mh-promo-form__either-or">
                    <div class="mh-promo-form__all-option">
                        <span class="mh-promo-form__all-label">All Pet Types</span>
                        <div class="mh-promo-form__all-controls">
                            <label class="mh-promo-form__dot-label">
                                <span class="service-policies-fee-dot">
                                    <input type="checkbox" wire:model.live="allPetTypes" aria-label="All pet types">
                                    <span aria-hidden="true"></span>
                                </span>
                            </label>
                            <label class="ma-switch" style="height: 24px;">
                                <input type="checkbox" wire:model.live="allPetTypes"
                                    aria-label="Toggle all pet types">
                                <span class="ma-switch-slider"></span>
                                <span class="ma-switch-check-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none">
                                        <path
                                            d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                            fill="white" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                    <span class="mh-promo-form__or">or</span>
                    <div class="service-field mh-promo-form__select-field"
                        :class="{ 'is-disabled': $wire.allPetTypes }">
                        <span :class="{ 'mh-promo-form__label--muted': $wire.allPetTypes }">Select Pet Type</span>
                        <div class="service-custom-select" :class="{ 'is-open': openPetType }"
                            @keydown.escape.window="openPetType = false" @click.outside="openPetType = false">
                            <button type="button" class="service-custom-trigger" @click.stop="togglePetTypeSelect()"
                                :aria-expanded="openPetType.toString()">
                                <span x-text="$wire.selectedPetType || 'Cat'"></span>
                                <svg class="service-custom-chevron" xmlns="http://www.w3.org/2000/svg" width="11"
                                    height="6" viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                    <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="service-custom-menu" x-show="openPetType" x-cloak @click.stop>
                                @foreach ($promoPetTypeOptions as $option)
                                    <button type="button" class="service-custom-option"
                                        :class="{ 'is-active': $wire.selectedPetType === @js($option) }"
                                        @click="$wire.set('selectedPetType', @js($option)); $wire.set('allPetTypes', false); openPetType = false">
                                        <span>{{ $option }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('selectedPetType')
                            <small class="mh-promo-form__error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <div class="mh-promo-form__either-or">
                    <div class="mh-promo-form__all-option">
                        <span class="mh-promo-form__all-label">All Pet Sizes</span>
                        <div class="mh-promo-form__all-controls">
                            <label class="mh-promo-form__dot-label">
                                <span class="service-policies-fee-dot">
                                    <input type="checkbox" wire:model.live="allPetSizes" aria-label="All pet sizes">
                                    <span aria-hidden="true"></span>
                                </span>
                            </label>
                            <label class="ma-switch" style="height: 24px;">
                                <input type="checkbox" wire:model.live="allPetSizes"
                                    aria-label="Toggle all pet sizes">
                                <span class="ma-switch-slider"></span>
                                <span class="ma-switch-check-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                        viewBox="0 0 20 20" fill="none">
                                        <path
                                            d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                            fill="white" />
                                    </svg>
                                </span>
                            </label>
                        </div>
                    </div>
                    <span class="mh-promo-form__or">or</span>
                    <div class="service-field mh-promo-form__select-field"
                        :class="{ 'is-disabled': $wire.allPetSizes }">
                        <span :class="{ 'mh-promo-form__label--muted': $wire.allPetSizes }">Select Pet Size</span>
                        <div class="service-custom-select" :class="{ 'is-open': openPetSize }"
                            @keydown.escape.window="openPetSize = false" @click.outside="openPetSize = false">
                            <button type="button" class="service-custom-trigger" @click.stop="togglePetSizeSelect()"
                                :aria-expanded="openPetSize.toString()">
                                <span x-text="$wire.selectedPetSize || 'Small 0 - 7 kg'"></span>
                                <svg class="service-custom-chevron" xmlns="http://www.w3.org/2000/svg" width="11"
                                    height="6" viewBox="0 0 11 6" fill="none" aria-hidden="true">
                                    <path d="M10.3741 0.5L5.39527 5.47882L0.500024 0.583578" stroke="#3B3731"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="service-custom-menu" x-show="openPetSize" x-cloak @click.stop>
                                @foreach ($promoPetSizeOptions as $option)
                                    <button type="button" class="service-custom-option"
                                        :class="{ 'is-active': $wire.selectedPetSize === @js($option) }"
                                        @click="$wire.set('selectedPetSize', @js($option)); $wire.set('allPetSizes', false); openPetSize = false">
                                        <span>{{ $option }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                        @error('selectedPetSize')
                            <small class="mh-promo-form__error">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="mh-promo-form__section mh-promo-form__section--last">
            <h3 class="mh-promo-form__heading">Visibility Controls</h3>
            <div class="service-toggle-wrap mh-promo-form__visibility">
                <p>Active Promo</p>
                <label class="ma-switch" style="height: 24px;">
                    <input type="checkbox" wire:model.live="visibility" aria-label="Active promo">
                    <span class="ma-switch-slider"></span>
                    <span class="ma-switch-check-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20"
                            fill="none">
                            <path
                                d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                fill="white" />
                        </svg>
                    </span>
                </label>
            </div>
        </div>
    </form>
</section>
