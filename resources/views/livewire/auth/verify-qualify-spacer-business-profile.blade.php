<div class="business-basics-wrap" wire:key="verify-qualify-spacer-business-profile"
    x-data="spacerBusinessProfile(@js($this->spacerBusinessProfileClientState()))">
    <h1 class="business-basics-title">About Your Business</h1>

    <form @submit.prevent="submitForm()" class="business-basics-form">
        {{-- Services & Pricing --}}
        <div class="basics-card">
            <h2 class="basics-section-title">Services</h2>
            <div class="services-card">
                <div class="services-header spacer-services-header">
                    <div>
                        <p>Services &amp; Pricing</p>
                        <span>Choose the pricing structure for your space</span>
                    </div>
                    <span>Price <span>(£)</span></span>
                </div>
                <div class="spacer-services-list-body">
                    @foreach ($this->spacerServicesPricingRowLabels() as $slug => $rowLabel)
                        <div class="spacer-service-row"
                            :class="{ 'spacer-service-row--selected': servicesPricing[{{ json_encode($slug) }}].selected }">
                            <div class="spacer-service-row-inner">
                                <label class="spacer-select-row"
                                    :class="{ 'is-selected': servicesPricing[{{ json_encode($slug) }}].selected }">
                                    <input type="checkbox" class="spacer-select-input"
                                        x-model="servicesPricing[{{ json_encode($slug) }}].selected">
                                    <span class="spacer-select-dot" aria-hidden="true"></span>
                                    <span class="spacer-service-label spacer-service-label--with-icon">
                                        <span class="spacer-service-label__icon" aria-hidden="true">
                                            @if ($slug === 'hourly')
                                                <img src="{{ asset('images/verify-qualify/icon-clock-hourly.svg') }}" alt=""
                                                    width="26" height="22">
                                            @elseif ($slug === 'half_day')
                                                <img src="{{ asset('images/verify-qualify/icon-clock-half-day.svg') }}" alt=""
                                                    width="26" height="22">
                                            @elseif ($slug === 'full_day')
                                                <img src="{{ asset('images/verify-qualify/icon-clock-full-day.svg') }}" alt=""
                                                    width="26" height="22">
                                            @endif
                                        </span>
                                        <span class="spacer-service-label__text">
                                            <span class="spacer-service-label__name">{{ $rowLabel['name'] }}</span>
                                            @if (!empty($rowLabel['meta']))
                                                <span class="spacer-service-label__meta"> {{ $rowLabel['meta'] }}</span>
                                            @endif
                                        </span>
                                    </span>
                                </label>
                                <div class="service-price-control spacer-service-price" @click.stop
                                    :aria-hidden="!servicesPricing[{{ json_encode($slug) }}].selected">
                                    <span class="service-price-currency">£</span>
                                    <input type="number" class="service-price-input" min="0" step="1"
                                        x-model="servicesPricing[{{ json_encode($slug) }}].price">
                                    <x-verify-qualify.price-steppers step-input />
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @error('spacer_services_pricing')
            <span class="error-text" style="display:block;margin-top:0.5rem;">{{ $message }}</span>
        @enderror

        {{-- Add-on Services --}}
        <div class="basics-card addon-picker-card" style="margin-top: 1.5rem;">
            <h2 class="basics-section-title">Add-ons</h2>
            <div class="basics-field">
                <label class="form-label">Add-ons <span class="form-label-muted">Choose add-ons and pricing
                        structure</span></label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="e.g. Early Hours Access" x-model="addonInput"
                        @keydown.enter.stop.prevent="addCustomAddon()">
                    <button type="button" class="addon-picker-plus" aria-label="Add add-on" :disabled="addonAddPending"
                        @click="addCustomAddon()">
                        <span class="addon-picker-plus-icon" x-show="!addonAddPending" x-cloak>
                            <x-verify-qualify.plus-icon />
                        </span>
                        <span class="addon-picker-plus-spinner" x-show="addonAddPending" x-cloak aria-hidden="true">
                            <span class="groomer-plus-spinner"></span>
                        </span>
                    </button>
                </div>
                <template x-if="customAddonRows.length > 0">
                    <div class="basics-pet-tags" aria-label="Custom add-ons">
                        <template x-for="(row, index) in customAddonRows" :key="'spacer-addon-tag-' + index">
                            <span class="basics-pet-tag">
                                <span class="basics-pet-tag__label" x-text="row.name"></span>
                                <button type="button" class="basics-pet-tag__remove" :aria-label="'Remove ' + row.name"
                                    @click="removeCustomAddon(index)">
                                    <x-verify-qualify.tag-remove-icon />
                                </button>
                            </span>
                        </template>
                    </div>
                </template>
                <p class="addon-picker-label">Or choose from FursGo add-ons:</p>
                <div class="addon-checkbox-list">
                    <template x-for="addon in addonCatalog" :key="'catalog-addon-' + addon.slug">
                        <label class="addon-checkbox-item"
                            :class="{ 'is-selected': fursgoAddons[addon.slug]?.selected }">
                            <input type="checkbox" x-model="fursgoAddons[addon.slug].selected">
                            <span x-text="addon.label"></span>
                        </label>
                    </template>
                </div>

                <template x-if="selectedAddonList.length > 0">
                    <div class="services-card spacer-addon-pricing-card">
                        <div class="services-header">
                            <p>List of Add-ons &amp; Pricing</p>
                            <span>Price <span>(£)</span></span>
                        </div>
                        <div class="services-list services-list--single">
                            <template x-for="(entry, index) in selectedAddonList" :key="'addon-row-' + entry.key">
                                <div class="service-item">
                                    <div class="service-item-main">
                                        <p class="service-item-name" x-text="index + 1 + '. ' + entry.name"></p>
                                        <div class="service-price-control">
                                            <span class="service-price-currency">£</span>
                                            <input type="number" class="service-price-input" min="0" step="1"
                                                :value="addonEntryRow(entry)?.price"
                                                @input="addonEntryRow(entry).price = $event.target.value">
                                            <x-verify-qualify.price-steppers step-input />
                                        </div>
                                    </div>
                                    <template x-if="showAddonDescriptionText(entry)">
                                        <div class="service-item-saved">
                                            <p class="service-item-description-text"
                                                :class="{ 'is-empty': addonDescriptionIsEmpty(entry) }"
                                                x-text="addonDescriptionText(entry)"></p>
                                            <button type="button" class="service-item-edit-btn"
                                                @click="editAddonDescription(entry)">
                                                Edit
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!showAddonDescriptionText(entry)">
                                        <div class="service-item-description">
                                            <p>Description (optional)</p>
                                            <div class="service-item-description-input-wrap">
                                                <input type="text"
                                                    placeholder="Please write a short description of add-on provided."
                                                    :value="addonEntryRow(entry)?.description"
                                                    @input="addonEntryRow(entry).description = $event.target.value"
                                                    @keydown.enter.prevent="commitAddonDescription(entry)">
                                                <button type="button" class="service-description-plus"
                                                    aria-label="Save description"
                                                    @click="commitAddonDescription(entry)">
                                                    <x-verify-qualify.plus-icon />
                                                </button>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </template>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Amenities --}}
        <div class="basics-card addon-picker-card" style="margin-top: 1.5rem;">
            <h2 class="basics-section-title">Amenities</h2>
            <div class="basics-field">
                <label class="form-label">Amenities Included <span class="form-label-muted">Select what your space
                        provides</span></label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="e.g. Premium shampoos provided"
                        x-model="amenityInput" @keydown.enter.stop.prevent="addCustomAmenity()">
                    <button type="button" class="addon-picker-plus" aria-label="Add amenity"
                        :disabled="amenityAddPending" @click="addCustomAmenity()">
                        <span class="addon-picker-plus-icon" x-show="!amenityAddPending" x-cloak>
                            <x-verify-qualify.plus-icon />
                        </span>
                        <span class="addon-picker-plus-spinner" x-show="amenityAddPending" x-cloak aria-hidden="true">
                            <span class="groomer-plus-spinner"></span>
                        </span>
                    </button>
                </div>
                <template x-if="amenitiesCustom.length > 0">
                    <div class="basics-pet-tags" aria-label="Custom amenities">
                        <template x-for="(amenity, index) in amenitiesCustom" :key="'spacer-amenity-custom-' + index">
                            <span class="basics-pet-tag">
                                <span class="basics-pet-tag__label" x-text="amenity.text"></span>
                                <button type="button" class="basics-pet-tag__remove"
                                    :aria-label="'Remove ' + amenity.text" @click="removeCustomAmenity(index)">
                                    <x-verify-qualify.tag-remove-icon />
                                </button>
                            </span>
                        </template>
                    </div>
                </template>
                <p class="addon-picker-label">Or choose from FursGo amenities:</p>
                <div class="addon-checkbox-list">
                    @foreach ($this->spacerAmenitiesPresetCatalog() as $amLabel)
                        <label class="addon-checkbox-item"
                            :class="{ 'is-selected': selectedAmenities.includes(@js($amLabel)) }">
                            <input type="checkbox" x-model="selectedAmenities" value="{{ $amLabel }}">
                            <span>{{ $amLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Service Suitability --}}
        <div class="basics-card" style="margin-top: 1.5rem;">
            <h2 class="basics-section-title">Service Suitability</h2>
            <div class="basics-field">
                <label class="form-label spacer-suitable-label">Suitable For <span class="form-label-muted">Select the
                        grooming services your space supports</span></label>
                <div class="basics-pet-chip-group spacer-suitable-grid">
                    @foreach ($this->spacerSuitableForCatalog() as $option)
                        <label class="basics-pet-chip spacer-suitable-chip"
                            :class="{ 'is-active': suitableFor.includes(@js($option)) }">
                            <input type="checkbox" x-model="suitableFor" value="{{ $option }}">
                            <span>{{ $option }}</span>
                            <span class="spacer-suitable-chip__check" aria-hidden="true">
                                <img src="{{ asset('images/verify-qualify/icon-suitability-check.svg') }}" alt="" width="19"
                                    height="19">
                            </span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Rules & Restrictions --}}
        <div class="basics-card addon-picker-card" style="margin-top: 1.5rem;">
            <h2 class="basics-section-title">Rules &amp; Restrictions</h2>
            <div class="basics-field">
                <label class="form-label">Rules &amp; Restrictions <span class="form-label-muted">Set expectations for
                        groomers using your space</span></label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="e.g. No Food or Drink" x-model="ruleInput"
                        @keydown.enter.stop.prevent="addCustomRule()">
                    <button type="button" class="addon-picker-plus" aria-label="Add rule" :disabled="ruleAddPending"
                        @click="addCustomRule()">
                        <span class="addon-picker-plus-icon" x-show="!ruleAddPending" x-cloak>
                            <x-verify-qualify.plus-icon />
                        </span>
                        <span class="addon-picker-plus-spinner" x-show="ruleAddPending" x-cloak aria-hidden="true">
                            <span class="groomer-plus-spinner"></span>
                        </span>
                    </button>
                </div>
                <template x-if="rulesCustom.length > 0">
                    <div class="basics-pet-tags" aria-label="Custom rules">
                        <template x-for="(rule, index) in rulesCustom" :key="'spacer-rule-custom-' + index">
                            <span class="basics-pet-tag">
                                <span class="basics-pet-tag__label" x-text="rule.text"></span>
                                <button type="button" class="basics-pet-tag__remove" :aria-label="'Remove ' + rule.text"
                                    @click="removeCustomRule(index)">
                                    <x-verify-qualify.tag-remove-icon />
                                </button>
                            </span>
                        </template>
                    </div>
                </template>
                <p class="addon-picker-label">Or choose from FursGo rules &amp; restrictions:</p>
                <div class="addon-checkbox-list">
                    @foreach ($this->spacerRulesPresetCatalog() as $ruleLabel)
                        <label class="addon-checkbox-item"
                            :class="{ 'is-selected': selectedRules.includes(@js($ruleLabel)) }">
                            <input type="checkbox" x-model="selectedRules" value="{{ $ruleLabel }}">
                            <span>{{ $ruleLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-buttons basics-actions">
            <x-common.button type="button" label="Back" width="105px" bg-color="#FFFFFF" text-color="#9D9B98"
                border="1px solid rgba(59, 55, 49, 0.10)" :shadow="false" wire:click="goBack" />
            <x-common.button type="button" label="Continue" width="105px" bg-color="#FFC97A" text-color="#FFFFFF"
                loading-target="submitSpacerBusinessProfile" x-bind:disabled="!canContinue || submitting"
                x-bind:class="{ 'common-btn--disabled': !canContinue || submitting }" x-bind:style="{
                    width: '105px',
                    height: '48px',
                    border: 'none',
                    borderRadius: '96px',
                    backgroundColor: canContinue && !submitting ? '#FFC97A' : '#e5e7eb',
                    color: canContinue && !submitting ? '#FFFFFF' : '#9ca3af',
                    boxShadow: canContinue && !submitting ? '0 5px 8px 0 rgba(0, 0, 0, 0.10)' : 'none',
                }" @click="submitForm()" />
        </div>
    </form>
</div>

<style>
    .basics-section-title {
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-weight: 600;
        line-height: normal;
        margin: 0 0 1.5rem;
    }

    .spacer-services-header {
        border-radius: 10px 10px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .spacer-services-header>div>p {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 0;
    }

    .spacer-services-header>div>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .spacer-services-header>span {
        margin-right: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .spacer-services-header>span>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .spacer-services-list-body {
        border: 1px solid #E2E2E2;
        border-top: none;
        border-radius: 0 0 10px 10px;
        overflow: hidden;
        background: #F8F8F8;
    }

    .spacer-service-row {
        display: block;
        position: relative;
        margin: 0;
        padding: 18px 25px;
        border-bottom: 1px solid #E2E2E2;
        background: #F8F8F8;
        user-select: none;
        -webkit-user-select: none;
    }

    .spacer-service-row:last-child {
        border-bottom: none;
    }

    .spacer-service-row--selected {
        background: #FFF;
    }

    .spacer-service-row-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        min-height: 48px;
        gap: 1rem;
    }

    .spacer-select-row {
        display: inline-flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        flex: 1;
        min-width: 0;
    }

    .spacer-select-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .spacer-select-dot {
        width: 22px;
        height: 22px;
        border-radius: 999px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        flex-shrink: 0;
        position: relative;
    }

    .spacer-select-row.is-selected .spacer-select-dot {
        border-color: #FFD88C;
    }

    .spacer-select-row.is-selected .spacer-select-dot::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 15px;
        height: 15px;
        border-radius: 999px;
        transform: translate(-50%, -50%);
        background: #FFD88C;
    }

    .spacer-service-label {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .spacer-service-label__name {
        color: #9C9790;
    }

    .spacer-service-label__meta {
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .spacer-service-row.spacer-service-row--selected .spacer-service-label__name {
        color: #3B3731;
    }

    .spacer-service-row:not(.spacer-service-row--selected) .spacer-service-label--with-icon {
        opacity: 0.5;
    }

    .spacer-service-label--with-icon {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
    }

    .spacer-service-label__icon {
        display: inline-flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
    }

    .spacer-service-label__icon svg,
    .spacer-service-label__icon img {
        width: 26px;
        height: 22px;
        display: block;
    }

    .spacer-suitable-label .form-label-muted {
        display: block;
        margin-top: 0.15rem;
    }

    .spacer-suitable-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .business-basics-wrap .spacer-suitable-chip {
        position: relative;
        gap: 0;
        min-height: 48px;
        height: 48px;
        padding: 0 1rem;
        border: none;
        border-radius: 10px;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        user-select: none;
        -webkit-user-select: none;
        -webkit-touch-callout: none;
        transition: background-color 0.22s ease, color 0.22s ease;
    }

    .business-basics-wrap .spacer-suitable-chip.is-active {
        background: #FFF4E4;
        color: #3B3731;
    }

    .business-basics-wrap .spacer-suitable-chip input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        pointer-events: none;
    }

    .business-basics-wrap .spacer-suitable-chip__check {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 0;
        height: 19px;
        margin-left: 0;
        opacity: 0;
        overflow: hidden;
        transform: scale(0.6);
        transform-origin: center left;
        flex-shrink: 0;
        transition: width 0.22s ease, margin-left 0.22s ease, opacity 0.18s ease, transform 0.22s ease;
    }

    .business-basics-wrap .spacer-suitable-chip.is-active .spacer-suitable-chip__check {
        width: 19px;
        margin-left: 0.5rem;
        opacity: 1;
        transform: scale(1);
    }

    .business-basics-wrap .spacer-suitable-chip__check img {
        width: 19px;
        height: 19px;
        display: block;
        flex-shrink: 0;
    }

    @media (prefers-reduced-motion: reduce) {

        .business-basics-wrap .spacer-suitable-chip,
        .business-basics-wrap .spacer-suitable-chip__check {
            transition: none;
        }
    }

    .business-basics-wrap .addon-checkbox-item {
        transition: none;
    }

    .business-basics-wrap .addon-picker-card .basics-pet-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin: 0.75rem 0 1rem;
    }

    .basics-pet-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        min-height: 48px;
        padding: 0 1rem;
        border: 1px solid #F0F0F0;
        border-radius: 10px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .basics-pet-tag__remove {
        border: none;
        background: transparent;
        color: #9D9B98;
        cursor: pointer;
        line-height: 0;
        padding: 0;
    }

    .addon-picker-plus-spinner {
        border-radius: 50%;
    }

    .groomer-plus-spinner {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        background: #C9DDA0;
        box-shadow: 0 5px 8px rgba(0, 0, 0, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .groomer-plus-spinner::after {
        content: "";
        width: 12px;
        height: 12px;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 50%;
        animation: groomer-plus-spin 0.8s linear infinite;
    }

    .addon-picker-plus-icon[x-cloak],
    .addon-picker-plus-spinner[x-cloak] {
        display: none !important;
    }

    @keyframes groomer-plus-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .services-card>.services-header {
        border-radius: 10px 10px 0 0;
        border: 1px solid #E2E2E2;
        background: #F8F8F8;
        padding: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .services-header>p {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        margin: 0;
    }

    .services-header>span {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .services-header>span>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .spacer-addon-pricing-card {
        margin-top: 1.25rem;
    }

    .services-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 25px;
        border: 1px solid #E2E2E2;
    }

    .services-list--single {
        gap: 0;
        border-radius: 0 0 10px 10px;
        background: #FFF;
    }

    .service-item {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        position: relative;
        padding: 20px 0;
        gap: 0;
    }

    .services-list--single .service-item:not(:last-child) {
        padding-bottom: 20px;
    }

    .services-list--single .service-item:not(:last-child)::after {
        content: "";
        position: absolute;
        left: -25px;
        right: -25px;
        bottom: 0;
        border-bottom: 1px solid #E2E2E2;
    }

    .service-item-main {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 1rem;
    }

    .service-item-name {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 0;
    }

    .service-price-control {
        width: 85px;
        height: 48px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        position: relative;
        padding: 0;
        box-sizing: border-box;
        flex-shrink: 0;
    }

    .spacer-service-price {
        width: 125px;
    }

    .spacer-service-row:not(.spacer-service-row--selected) .spacer-service-price {
        visibility: hidden;
        pointer-events: none;
    }

    .service-price-currency {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
    }

    .service-price-input {
        width: 2.25rem;
        border: none;
        outline: none;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        position: absolute;
        left: 28px;
        top: 50%;
        transform: translateY(-50%);
        padding: 0;
        background: transparent;
        appearance: textfield;
        -moz-appearance: textfield;
    }

    .spacer-service-price .service-price-input {
        width: 4.5rem;
    }

    .service-price-input::-webkit-outer-spin-button,
    .service-price-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .service-price-steppers {
        width: 11px;
        height: 27px;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .service-price-steppers__icon {
        display: block;
        width: 11px;
        height: 27px;
        pointer-events: none;
    }

    .service-stepper-btn {
        position: absolute;
        left: 0;
        width: 11px;
        height: 13px;
        border: none;
        background: transparent;
        padding: 0;
        margin: 0;
        cursor: pointer;
    }

    .service-stepper-btn--up {
        top: 0;
    }

    .service-stepper-btn--down {
        bottom: 0;
    }

    .service-item-description {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        width: 100%;
    }

    .service-item-description>p {
        margin: 10px 0 10px;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-item-description-input-wrap {
        position: relative;
        display: block;
        width: 100%;
        max-width: none;
    }

    .service-item-description-input-wrap>input {
        padding: 0 3.25rem 0 10px;
        width: 100%;
        max-width: none;
        height: 48px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        box-sizing: border-box;
    }

    .service-item-description-input-wrap>input::placeholder {
        color: #9D9B98;
    }

    .business-basics-wrap .service-item-description-input-wrap>input:focus,
    .business-basics-wrap .service-price-control:focus-within {
        outline: none;
        border-color: #D4D4D4;
    }

    .service-description-plus {
        position: absolute;
        top: 60%;
        right: 2px;
        transform: translateY(-50%);
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: transparent;
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        padding: 0;
        z-index: 1;
    }

    .service-description-plus .groomer-plus-icon-svg,
    .service-description-plus img {
        width: 44px;
        height: 44px;
        display: block;
    }

    .service-item-description-text {
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        max-width: 360px;
        text-align: left;
        margin: 0;
        flex: 1;
        min-width: 0;
    }

    .service-item-description-text.is-empty {
        font-style: italic;
    }

    .service-item-saved {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        gap: 1rem;
        margin-top: 4px;
    }

    .service-item-edit-btn {
        border: none;
        background: transparent;
        padding: 0;
        margin: 0;
        cursor: pointer;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-decoration-line: underline;
        text-underline-position: from-font;
    }

    .addon-picker-card {
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
    }

    .addon-picker-input-wrap {
        position: relative;
        display: block;
    }

    .addon-picker-input-wrap .form-input,
    .business-basics-wrap .addon-picker-input-wrap .form-input {
        width: 100%;
        padding-right: 3.25rem;
    }

    .addon-picker-plus {
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: transparent;
        flex-shrink: 0;
        width: 44px;
        height: 44px;
        padding: 0;
        position: absolute;
        top: 60%;
        right: 0.15rem;
        transform: translateY(-50%);
        z-index: 1;
    }

    .addon-picker-plus-icon,
    .addon-picker-plus-spinner {
        position: absolute;
        inset: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
    }

    .addon-picker-plus-icon .groomer-plus-icon-svg,
    .addon-picker-plus-icon img {
        width: 44px;
        height: 44px;
        display: block;
    }

    .addon-picker-label {
        margin: 1rem 0 0.8rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .addon-checkbox-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .addon-checkbox-item {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
        user-select: none;
        -webkit-user-select: none;
    }

    .addon-checkbox-item input[type="checkbox"] {
        appearance: none;
        width: 26px;
        height: 26px;
        margin: 0;
        border-radius: 999px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        position: relative;
    }

    .addon-checkbox-item.is-selected input[type="checkbox"] {
        border-color: #FFD88C;
    }

    .addon-checkbox-item.is-selected input[type="checkbox"]::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 50%;
        width: 1rem;
        height: 1rem;
        border-radius: 999px;
        transform: translate(-50%, -50%);
        background: #FFD88C;
    }

    .addon-checkbox-item.is-selected>span {
        color: #000;
    }
</style>