<div class="business-basics-wrap" wire:key="verify-qualify-groomer-business-profile"
    x-data="groomerBusinessProfile(@js($this->groomerBusinessProfileClientState()))">
    <h1 class="business-basics-title">About Your Business</h1>

    <form @submit.prevent="submitForm()" class="business-basics-form">
        <div class="basics-card addon-picker-card">
            <h2 class="basics-section-title">Services</h2>
            <div class="basics-field">
                <label class="form-label">Services offered</label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="e.g. Paw fur trim ..." x-model="serviceInput"
                        @keydown.enter.stop.prevent="addCustomService()">
                    <button type="button" class="addon-picker-plus" aria-label="Add service"
                        :disabled="serviceAddPending" @click="addCustomService()">
                        <span class="addon-picker-plus-icon" x-show="!serviceAddPending" x-cloak>
                            <x-verify-qualify.plus-icon />
                        </span>
                        <span class="addon-picker-plus-spinner" x-show="serviceAddPending" x-cloak aria-hidden="true">
                            <span class="groomer-plus-spinner"></span>
                        </span>
                    </button>
                </div>
                <template x-if="customServices.length > 0">
                    <div class="basics-pet-tags" aria-label="Custom services">
                        <template x-for="service in customServices" :key="'custom-service-' + service">
                            <span class="basics-pet-tag">
                                <span class="basics-pet-tag__label" x-text="service"></span>
                                <button type="button" class="basics-pet-tag__remove" :aria-label="'Remove ' + service"
                                    @click="removeCustomService(service)">
                                    <x-verify-qualify.tag-remove-icon />
                                </button>
                            </span>
                        </template>
                    </div>
                </template>
                <p class="addon-picker-label">Or choose from FursGo services:</p>
                <div class="addon-checkbox-list">
                    <template x-for="service in serviceCatalog" :key="'catalog-service-' + service">
                        <label class="addon-checkbox-item" :class="{ 'is-selected': isServiceSelected(service) }">
                            <input type="checkbox" :value="service" :checked="isServiceSelected(service)"
                                @change="toggleService(service)">
                            <span x-text="service"></span>
                        </label>
                    </template>
                </div>
            </div>
        </div>

        <template x-if="selectedServices.length > 0">
            <div class="services-card">
                <div class="services-header">
                    <p>List of Services &amp; Pricing</p>
                    <span>Price <span>(£)</span></span>
                </div>
                <div class="services-list services-list--single">
                    <template x-for="(serviceName, index) in selectedServices"
                        :key="'service-row-' + serviceKey(serviceName)">
                        <div class="service-item">
                            <div class="service-item-main">
                                <p class="service-item-name" x-text="(index + 1) + '. ' + serviceName"></p>
                                <div class="service-price-control">
                                    <span class="service-price-currency">£</span>
                                    <input type="number" class="service-price-input" min="0" step="1"
                                        x-model="servicesPricing[serviceKey(serviceName)].price">
                                    <x-verify-qualify.price-steppers
                                        increase="stepPrice(serviceKey(serviceName), 1, 'service')"
                                        decrease="stepPrice(serviceKey(serviceName), -1, 'service')" />
                                </div>
                            </div>
                            <template x-if="showServiceDescriptionText(serviceName)">
                                <div class="service-item-saved">
                                    <p class="service-item-description-text"
                                        x-text="serviceDescriptionText(serviceName)"></p>
                                    <button type="button" class="service-item-edit-btn"
                                        @click="editServiceDescription(serviceName)">
                                        Edit
                                    </button>
                                </div>
                            </template>
                            <template x-if="!showServiceDescriptionText(serviceName)">
                                <div class="service-item-description">
                                    <p>Description (optional)</p>
                                    <div class="service-item-description-input-wrap">
                                        <input type="text"
                                            placeholder="Please write a short description of service provided."
                                            x-model="servicesPricing[serviceKey(serviceName)].description"
                                            @keydown.enter.prevent="commitServiceDescription(serviceName)">
                                        <button type="button" class="service-description-plus"
                                            aria-label="Save description"
                                            @click="commitServiceDescription(serviceName)">
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

        <div class="basics-card addon-picker-card">
            <h2 class="basics-section-title">Add-ons</h2>
            <div class="basics-field">
                <label class="form-label">Add-ons <span class="form-label-muted">Choose add-ons and pricing
                        structure</span></label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="e.g. Paw fur trim ..." x-model="addonInput"
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
                <template x-if="customAddons.length > 0">
                    <div class="basics-pet-tags" aria-label="Custom add-ons">
                        <template x-for="addon in customAddons" :key="'custom-addon-' + addon">
                            <span class="basics-pet-tag">
                                <span class="basics-pet-tag__label" x-text="addon"></span>
                                <button type="button" class="basics-pet-tag__remove" :aria-label="'Remove ' + addon"
                                    @click="removeCustomAddon(addon)">
                                    <x-verify-qualify.tag-remove-icon />
                                </button>
                            </span>
                        </template>
                    </div>
                </template>
                <p class="addon-picker-label">Or choose from FursGo add-ons:</p>
                <div class="addon-checkbox-list">
                    <template x-for="addon in addonCatalog" :key="'catalog-addon-' + addon">
                        <label class="addon-checkbox-item" :class="{ 'is-selected': isAddonSelected(addon) }">
                            <input type="checkbox" :value="addon" :checked="isAddonSelected(addon)"
                                @change="toggleAddon(addon)">
                            <span x-text="addon"></span>
                        </label>
                    </template>
                </div>

                <template x-if="selectedAddons.length > 0">
                    <div class="services-card addon-pricing-card">
                        <div class="services-header">
                            <p>List of Add-ons &amp; Pricing</p>
                            <span>Price <span>(£)</span></span>
                        </div>
                        <div class="services-list services-list--single">
                            <template x-for="(addonName, index) in selectedAddons"
                                :key="'addon-row-' + addonKey(addonName)">
                                <div class="service-item">
                                    <div class="service-item-main">
                                        <p class="service-item-name" x-text="(index + 1) + '. ' + addonName"></p>
                                        <div class="service-price-control">
                                            <span class="service-price-currency">£</span>
                                            <input type="number" class="service-price-input" min="0" step="1"
                                                x-model="addonPricing[addonKey(addonName)].price">
                                            <x-verify-qualify.price-steppers
                                                increase="stepPrice(addonKey(addonName), 1, 'addon')"
                                                decrease="stepPrice(addonKey(addonName), -1, 'addon')" />
                                        </div>
                                    </div>
                                    <template x-if="showAddonDescriptionText(addonName)">
                                        <div class="service-item-saved">
                                            <p class="service-item-description-text"
                                                x-text="addonDescriptionText(addonName)">
                                            </p>
                                            <button type="button" class="service-item-edit-btn"
                                                @click="editAddonDescription(addonName)">
                                                Edit
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="!showAddonDescriptionText(addonName)">
                                        <div class="service-item-description">
                                            <p>Description (optional)</p>
                                            <div class="service-item-description-input-wrap">
                                                <input type="text"
                                                    placeholder="Please write a short description of add-on provided."
                                                    x-model="addonPricing[addonKey(addonName)].description"
                                                    @keydown.enter.prevent="commitAddonDescription(addonName)">
                                                <button type="button" class="service-description-plus"
                                                    aria-label="Save description"
                                                    @click="commitAddonDescription(addonName)">
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

        <div class="basics-card addon-picker-card">
            <h2 class="basics-section-title">Rules &amp; Restrictions</h2>
            <div class="basics-field">
                <label class="form-label">Rules &amp; Restrictions <span class="form-label-muted">Set expectations for
                        clients booking with you</span></label>
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
                <template x-if="customRules.length > 0">
                    <div class="basics-pet-tags" aria-label="Custom rules">
                        <template x-for="rule in customRules" :key="'custom-rule-' + rule">
                            <span class="basics-pet-tag">
                                <span class="basics-pet-tag__label" x-text="rule"></span>
                                <button type="button" class="basics-pet-tag__remove" :aria-label="'Remove ' + rule"
                                    @click="removeCustomRule(rule)">
                                    <x-verify-qualify.tag-remove-icon />
                                </button>
                            </span>
                        </template>
                    </div>
                </template>
                <p class="addon-picker-label">Or choose from FursGo rules &amp; restrictions:</p>
                <div class="addon-checkbox-list">
                    <template x-for="rule in ruleCatalog" :key="'catalog-rule-' + rule">
                        <label class="addon-checkbox-item" :class="{ 'is-selected': selectedRules.includes(rule) }">
                            <input type="checkbox" :value="rule" :checked="selectedRules.includes(rule)"
                                @change="toggleRule(rule)">
                            <span x-text="rule"></span>
                        </label>
                    </template>
                </div>
            </div>
        </div>

        <div class="form-buttons basics-actions">
            <x-common.button type="button" label="Back" width="105px" bg-color="#FFFFFF" text-color="#9D9B98"
                border="1px solid rgba(59, 55, 49, 0.10)" :shadow="false" wire:click="goBack" />
            <x-common.button type="button" label="Continue" width="105px" bg-color="#FFC97A" text-color="#FFFFFF"
                loading-target="submitGroomerBusinessProfile" x-bind:disabled="!canContinue || submitting"
                x-bind:class="{ 'common-btn--disabled': !canContinue || submitting }" x-bind:style="{
                    width: '105px',
                    height: '48px',
                    border: 'none',
                    borderRadius: '96px',
                    backgroundColor: (canContinue && !submitting) ? '#FFC97A' : '#e5e7eb',
                    color: (canContinue && !submitting) ? '#FFFFFF' : '#9ca3af',
                    boxShadow: (canContinue && !submitting) ? '0 5px 8px 0 rgba(0, 0, 0, 0.10)' : 'none',
                }" @click="submitForm()" />
        </div>
    </form>
</div>
<style>
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
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .services-header>span {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .services-header>span>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .services-list {
        display: flex;
        flex-direction: column;
        gap: 10px;
        padding: 25px;
        border: 1px solid #E2E2E2;
    }

    .services-list:last-child {
        border-radius: 0 0 10px 10px;
        border: 1px solid #E2E2E2;
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
        transition: border-color 0.15s ease;
        flex-shrink: 0;
    }

    .service-price-currency {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
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
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        position: absolute;
        left: 28px;
        top: 50%;
        transform: translateY(-50%);
        padding: 0;
        background: transparent;
        appearance: textfield;
        -moz-appearance: textfield;
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
        transition: border-color 0.15s ease;
        box-sizing: border-box;
    }

    .service-item-description-input-wrap>input::placeholder {
        color: #9D9B98;
    }

    .business-basics-wrap .service-item-description-input-wrap>input:focus {
        outline: none;
        border-color: #D4D4D4;
    }

    .business-basics-wrap .service-price-control:focus-within {
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

    .service-item-saved {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        width: 100%;
        gap: 1rem;
        margin-top: 4px;
    }

    .service-item-saved .service-item-edit-btn {
        flex-shrink: 0;
        width: 85px;
        text-align: center;
        margin-top: 10px;
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

    .addon-picker-card {
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
    }

    .addon-picker-card .addon-pricing-card {
        margin-top: 1.25rem;
    }

    .addon-picker-card>.basics-field>.form-label>span {
        line-height: 20px;
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

    .groomer-other-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 0.75rem 0 0.25rem;
    }

    .groomer-other-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .groomer-other-tag__label {
        max-width: 12rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .groomer-other-tag__remove {
        border: none;
        background: transparent;
        color: #9D9B98;
        cursor: pointer;
        font-size: 16px;
        line-height: 1;
        padding: 0;
    }

    .groomer-other-tag__remove:hover {
        color: #3B3731;
    }

    .groomer-other-input-block {
        margin-top: 1rem;
        margin-bottom: 0.5rem;
        overflow: hidden;
    }

    .groomer-other-input-block[x-cloak] {
        display: none !important;
    }

    .groomer-other-anim {
        transition:
            opacity 0.3s ease,
            transform 0.3s ease,
            max-height 0.3s ease,
            margin 0.3s ease;
        will-change: opacity, transform, max-height;
    }

    .groomer-other-anim-start {
        opacity: 0;
        transform: translateY(-0.4rem);
        max-height: 0;
        margin-top: 0;
        margin-bottom: 0;
    }

    .groomer-other-anim-end {
        opacity: 1;
        transform: translateY(0);
        max-height: 10rem;
        margin-top: 1rem;
        margin-bottom: 0.5rem;
    }

    .groomer-other-input-wrap {
        position: relative;
        display: block;
        gap: 0;
    }

    .groomer-other-input-wrap .form-input {
        width: 100%;
        padding-right: 3.25rem;
        margin-top: 0.9rem;
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

    .addon-picker-plus.groomer-other-plus {
        position: absolute;
        top: 50%;
        right: 0.35rem;
        transform: translateY(-50%);
        width: 44px;
        height: 44px;
        margin: 0;
        z-index: 1;
        margin-top: 10px
    }

    .addon-picker-plus.groomer-other-plus svg,
    .addon-picker-plus.groomer-other-plus img {
        width: 44px;
        height: 44px;
        display: block;
    }

    .addon-picker-plus:disabled {
        cursor: wait;
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
    .addon-picker-plus-icon svg,
    .addon-picker-plus-icon img {
        width: 44px;
        height: 44px;
        display: block;
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

    .addon-picker-plus .hidden {
        display: none !important;
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

    .addon-picker-label {
        margin: 1rem 0 0.8rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .addon-checkbox-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .addon-checkbox-list-custom {
        margin-top: 0.9rem;
    }

    .addon-checkbox-item {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
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
        width: 16px;
        height: 16px;
        border-radius: 999px;
        transform: translate(-50%, -50%);
        background: #FFD88C;
    }

    .addon-checkbox-item span {
        color: inherit;
        font-size: 16px;
        line-height: normal;
    }

    .groomer-chip-list {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
        margin-top: 0.9rem;
    }

    .groomer-chip-list-fursgo {
        margin-top: 0;
    }

    .groomer-service-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 96px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        min-height: 48px;
        padding: 0.45rem 1.1rem;
        cursor: pointer;
        user-select: none;
    }

    .groomer-service-chip input {
        display: none;
    }

    .groomer-service-chip span {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        color: #D4D4D4;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        white-space: nowrap;
    }

    .groomer-service-chip.is-selected {
        background: #FFC97A;
        border-color: #FFC97A;
    }

    .groomer-service-chip.is-selected span {
        color: #FDFCF8;
    }

    .groomer-service-chip.is-selected span::before {
        content: "";
        display: inline-block;
        width: 14px;
        height: 10px;
        background-color: #FDFCF8;
        -webkit-mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10' fill='none'%3E%3Cpath d='M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z' fill='black'/%3E%3C/svg%3E");
        mask-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='10' viewBox='0 0 14 10' fill='none'%3E%3Cpath d='M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z' fill='black'/%3E%3C/svg%3E");
        -webkit-mask-repeat: no-repeat;
        mask-repeat: no-repeat;
        -webkit-mask-size: 14px 10px;
        mask-size: 14px 10px;
        -webkit-mask-position: center;
        mask-position: center;
    }

    .services-list--single {
        gap: 0;
        border-radius: 0 0 10px 10px;
        background: #FFF;
    }

    .services-list--single .service-item {
        width: 100%;
        align-items: stretch;
        position: relative;
    }

    .services-list--single .service-item:not(:last-child) {
        padding-bottom: 20px;
        margin-bottom: 0;
    }

    .services-list--single .service-item:not(:last-child)::after {
        content: "";
        position: absolute;
        left: -25px;
        right: -25px;
        bottom: 0;
        border-bottom: 1px solid #E2E2E2;
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
        text-decoration-style: solid;
        text-decoration-skip-ink: auto;
        text-decoration-thickness: auto;
        text-underline-offset: auto;
        text-underline-position: from-font;
    }
</style>