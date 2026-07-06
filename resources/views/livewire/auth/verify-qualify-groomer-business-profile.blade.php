<div class="business-basics-wrap" wire:key="verify-qualify-groomer-business-profile" x-data="groomerBusinessProfile(@js($this->groomerBusinessProfileClientState()))">
    <svg aria-hidden="true" focusable="false" width="0" height="0"
        style="position: absolute; width: 0; height: 0; overflow: hidden;">
        <defs>
            <filter id="filter0_d_58_696" x="0" y="0" width="64" height="64" filterUnits="userSpaceOnUse"
                color-interpolation-filters="sRGB">
                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                    result="hardAlpha" />
                <feOffset dy="5" />
                <feGaussianBlur stdDeviation="4" />
                <feComposite in2="hardAlpha" operator="out" />
                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0" />
                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_58_696" />
                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_58_696" result="shape" />
            </filter>
        </defs>
    </svg>
    <h1 class="business-basics-title">About Your Business</h1>

    <form @submit.prevent="submitForm()" class="business-basics-form">
        <div class="basics-card">
            <div class="basics-field">
                <label class="form-label" for="groomer-experience">Bio</label>
                <textarea id="groomer-experience" x-model="experience" class="form-input basics-textarea"
                    placeholder="Describe your services, experience, and philosophy."
                    style="resize: none; overflow: hidden; height: 150px; width: 100%;"></textarea>
                @error('groomer_experience')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="basics-card">
            <div class="basics-field">
                <label class="form-label" style="margin-bottom: 2rem;">Pet Preferences</label>
                <div class="groomer-focus-wrap">
                    <label class="form-label">Select Pet Specialty:</label>
                    <div class="groomer-pill-group">
                        <label class="groomer-pill-option groomer-pill-specialty"
                            :class="{ 'is-active': petSpecialties.includes('dog') }">
                            <input type="checkbox" value="dog" :checked="petSpecialties.includes('dog')"
                                @change="togglePetSpecialty('dog')">
                            <span>Dog
                                <svg class="groomer-pill-icon" xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="15" viewBox="0 0 16 15" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.02515 0C8.64209 0.000213345 9.22451 0.285449 9.60328 0.772461L11.7185 3.49219C11.8776 3.69667 12.1105 3.83152 12.3669 3.86816L14.3513 4.15137C14.7092 4.20264 15.0125 4.44317 15.1209 4.78809C15.3556 5.53533 15.6937 6.86423 15.4334 7.6582C15.1017 8.66932 14.5694 9.17545 13.6101 9.42285C11.9669 9.84646 10.6081 9.36208 9.00269 10.5859C8.61476 10.8817 8.32298 11.2488 8.10816 11.6592L7.93433 12.0166C6.99837 13.8635 4.67318 15.7352 2.92652 14.708C1.76272 14.0232 1.19689 12.6471 1.54273 11.3418L2.01832 9.5459C2.13774 9.53792 2.2548 9.52867 2.36597 9.51465C2.6604 9.47748 2.95561 9.41272 3.15406 9.28711C3.31249 9.18662 3.47116 9.01627 3.61597 8.83105C3.76469 8.64081 3.91577 8.41352 4.05836 8.18066C4.34353 7.71488 4.60442 7.20842 4.75855 6.88281C4.81749 6.75812 4.76399 6.60893 4.63941 6.5498C4.5149 6.49107 4.36662 6.54376 4.30738 6.66797C4.15908 6.98129 3.90564 7.47235 3.6316 7.91992C3.49464 8.14359 3.35417 8.35266 3.22144 8.52246C3.08483 8.69719 2.96988 8.81132 2.88648 8.86426C2.78857 8.92626 2.59053 8.98231 2.30347 9.01855C2.0271 9.05342 1.70289 9.06706 1.38257 9.06641C1.06811 9.06575 0.762227 9.04878 0.516364 9.03125C0.129202 8.76968 -0.0880056 8.27103 0.0339417 7.80859C1.06233 3.9097 1.6697 2.10636 2.67847 1.09766C3.77006 0.00665437 5.94579 3.69894e-05 5.97144 0H8.02515ZM8.57496 3.66016C8.11053 3.66016 7.61807 3.89064 7.61793 4.8125C7.61793 5.44915 8.42917 4.8125 8.95777 4.8125C9.48622 4.81262 9.53199 5.44909 9.53199 4.8125C9.5318 4.17611 9.10336 3.66027 8.57496 3.66016Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </label>
                        <label class="groomer-pill-option groomer-pill-specialty"
                            :class="{ 'is-active': petSpecialties.includes('cat') }">
                            <input type="checkbox" value="cat" :checked="petSpecialties.includes('cat')"
                                @change="togglePetSpecialty('cat')">
                            <span>Cat
                                <svg class="groomer-pill-icon" xmlns="http://www.w3.org/2000/svg" width="11"
                                    height="15" viewBox="0 0 11 15" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.99889 2.53516C5.53684 2.4688 6.2816 2.4856 6.994 2.72656C7.78167 2.99305 8.5426 3.54017 8.9149 4.55566L10.4501 5.29199L10.4833 5.41211C10.6868 6.146 10.8081 7.28565 10.493 8.32324C10.3341 8.84623 10.0619 9.34947 9.62877 9.75684C9.19459 10.1652 8.61113 10.4656 7.8485 10.6016C5.10446 11.0909 3.54731 13.4378 3.11803 14.5459C2.94136 15.0506 1.89466 15.1787 1.6483 14.7041C-2.03157 7.61478 1.27786 2.01708 3.56236 0L4.99889 2.53516ZM6.69908 5.09961C6.29638 5.09961 5.869 5.30013 5.869 6.09961C5.86942 6.65073 6.57298 6.09961 7.03111 6.09961C7.48905 6.09978 7.52914 6.65064 7.52916 6.09961C7.52916 5.54772 7.15731 5.09966 6.69908 5.09961Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </label>
                        <label class="groomer-pill-option groomer-pill-specialty"
                            :class="{ 'is-active': petSpecialties.includes('other') }">
                            <input type="checkbox" value="other" :checked="petSpecialties.includes('other')"
                                @change="togglePetSpecialty('other')">
                            <span>Other
                                <svg class="groomer-pill-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                    height="15" viewBox="0 0 19 15" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M6.10714 0C5.43536 0 4.90879 0.410454 4.58307 0.902727C4.25329 1.39909 4.07143 2.04682 4.07143 2.72727C4.07143 3.40773 4.25329 4.05545 4.58307 4.55182C4.90879 5.04273 5.43536 5.45455 6.10714 5.45455C6.77893 5.45455 7.3055 5.04409 7.63121 4.55182C7.961 4.05545 8.14286 3.40773 8.14286 2.72727C8.14286 2.04682 7.961 1.39909 7.63121 0.902727C7.3055 0.411818 6.77893 0 6.10714 0ZM12.8929 0C12.2211 0 11.6945 0.410454 11.3688 0.902727C11.039 1.39909 10.8571 2.04682 10.8571 2.72727C10.8571 3.40773 11.039 4.05545 11.3688 4.55182C11.6945 5.04273 12.2211 5.45455 12.8929 5.45455C13.5646 5.45455 14.0912 5.04409 14.4169 4.55182C14.7467 4.05545 14.9286 3.40773 14.9286 2.72727C14.9286 2.04682 14.7467 1.39909 14.4169 0.902727C14.0912 0.411818 13.5646 0 12.8929 0ZM2.03571 6.13636C1.36393 6.13636 0.837357 6.54682 0.511643 7.03909C0.181857 7.53545 0 8.18318 0 8.86364C0 9.54409 0.181857 10.1918 0.511643 10.6882C0.837357 11.1791 1.36393 11.5909 2.03571 11.5909C2.7075 11.5909 3.23407 11.1805 3.55979 10.6882C3.88957 10.1918 4.07143 9.54409 4.07143 8.86364C4.07143 8.18318 3.88957 7.53545 3.55979 7.03909C3.23407 6.54818 2.7075 6.13636 2.03571 6.13636ZM9.5 6.13636C7.87143 6.13636 6.66493 7.01455 5.89407 8.10409C5.13271 9.17727 4.75 10.5095 4.75 11.5909C4.75 12.8509 5.50321 13.7277 6.42743 14.2527C7.33671 14.7709 8.47671 15 9.5 15C10.5233 15 11.6633 14.7723 12.5726 14.2527C13.4954 13.7264 14.25 12.8509 14.25 11.5909C14.25 10.5095 13.8673 9.17727 13.1059 8.10409C12.3364 7.01318 11.1299 6.13636 9.5 6.13636ZM16.9643 6.13636C16.2925 6.13636 15.7659 6.54682 15.4402 7.03909C15.1104 7.53545 14.9286 8.18318 14.9286 8.86364C14.9286 9.54409 15.1104 10.1918 15.4402 10.6882C15.7659 11.1791 16.2925 11.5909 16.9643 11.5909C17.6361 11.5909 18.1626 11.1805 18.4884 10.6882C18.8181 10.1918 19 9.54409 19 8.86364C19 8.18318 18.8181 7.53545 18.4884 7.03909C18.1626 6.54818 17.6361 6.13636 16.9643 6.13636Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </label>
                    </div>
                    @error('groomer_pet_specialties')
                        <span class="error-text">{{ $message }}</span>
                    @enderror

                    <label class="form-label" for="groomer-specialty-other">Other <span>(Please specify)</span></label>
                    <input id="groomer-specialty-other" type="text" x-model="specialtyOther" class="form-input"
                        placeholder="e.g., Luxury grooming with a gentle touch.">
                    @error('groomer_specialty_other')
                        <span class="error-text">{{ $message }}</span>
                    @enderror

                    <label class="form-label">Select Pet Size:</label>
                    <div class="groomer-pill-group groomer-pill-group--sizes">
                        <label class="groomer-pill-option groomer-pill-size"
                            :class="{ 'is-active': petSizes.includes('small') }">
                            <input type="checkbox" value="small" :checked="petSizes.includes('small')"
                                @change="togglePetSize('small')">
                            <span>Small 0-7 kg</span>
                        </label>
                        <label class="groomer-pill-option groomer-pill-size"
                            :class="{ 'is-active': petSizes.includes('medium') }">
                            <input type="checkbox" value="medium" :checked="petSizes.includes('medium')"
                                @change="togglePetSize('medium')">
                            <span>Medium 8-18 kg</span>
                        </label>
                        <label class="groomer-pill-option groomer-pill-size"
                            :class="{ 'is-active': petSizes.includes('large') }">
                            <input type="checkbox" value="large" :checked="petSizes.includes('large')"
                                @change="togglePetSize('large')">
                            <span>Large 19+ kg</span>
                        </label>
                    </div>
                    @error('groomer_pet_sizes')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="basics-card addon-picker-card">
            <div class="basics-field">
                <label class="form-label">Services offered <span>(Choose core services you want to
                        offer)</span></label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="Paw fur trim" x-model="serviceInput"
                        @keydown.enter.stop.prevent="addCustomService()">
                    <button type="button" class="addon-picker-plus" aria-label="Add service"
                        :disabled="serviceAddPending" @click="addCustomService()">
                        <span class="addon-picker-plus-icon" x-show="!serviceAddPending" x-cloak>
                            @include('livewire.auth.partials.groomer-plus-icon')
                        </span>
                        <span class="addon-picker-plus-spinner" x-show="serviceAddPending" x-cloak
                            aria-hidden="true">
                            <span class="groomer-plus-spinner"></span>
                        </span>
                    </button>
                </div>
                <template x-if="customServices.length > 0">
                    <div class="groomer-chip-list groomer-chip-list-custom">
                        <template x-for="service in customServices" :key="'custom-service-' + service">
                            <label class="groomer-service-chip"
                                :class="{ 'is-selected': isServiceSelected(service) }">
                                <input type="checkbox" :value="service" :checked="isServiceSelected(service)"
                                    @change="toggleService(service)">
                                <span x-text="service"></span>
                            </label>
                        </template>
                    </div>
                </template>
                <p class="addon-picker-label">Or choose from FursGo services:</p>
                <div class="groomer-chip-list groomer-chip-list-fursgo">
                    <template x-for="service in serviceCatalog" :key="'catalog-service-' + service">
                        <label class="groomer-service-chip" :class="{ 'is-selected': isServiceSelected(service) }">
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
                    <p>List of services</p>
                    <span>Price <span>(£)</span></span>
                </div>
                <div class="services-list services-list--single">
                    <template x-for="(serviceName, index) in selectedServices"
                        :key="'service-row-' + serviceKey(serviceName)">
                        <div class="service-item">
                            <div>
                                <p x-text="(index + 1) + '. ' + serviceName"></p>
                                <div class="service-price-control">
                                    <span class="service-price-currency">£</span>
                                    <input type="number" class="service-price-input" min="0" step="1"
                                        x-model="servicesPricing[serviceKey(serviceName)].price">
                                    <div class="service-price-steppers">
                                        <button type="button" class="service-stepper-btn"
                                            aria-label="Increase price"
                                            @click="stepPrice(serviceKey(serviceName), 1, 'service')"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                viewBox="0 0 11 6" fill="none">
                                                <path d="M10.374 5.47852L5.3952 0.499696L0.499963 5.39494"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg></button>
                                        <button type="button" class="service-stepper-btn"
                                            aria-label="Decrease price"
                                            @click="stepPrice(serviceKey(serviceName), -1, 'service')"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                viewBox="0 0 11 6" fill="none">
                                                <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></button>
                                    </div>
                                </div>
                            </div>
                            <template x-if="showServiceDescriptionText(serviceName)">
                                <div class="service-item-default-description">
                                    <p class="service-item-description-text"
                                        x-text="serviceDescriptionText(serviceName)"></p>
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
                                            @include('livewire.auth.partials.groomer-plus-icon')
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
            <div class="basics-field">
                <label class="form-label">Extra's &amp; Add-ons <span>(List out your add-ons (inc. short
                        description))</span></label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="Flea & Tick Treatment"
                        x-model="addonInput" @keydown.enter.stop.prevent="addCustomAddon()">
                    <button type="button" class="addon-picker-plus" aria-label="Add add-on"
                        :disabled="addonAddPending" @click="addCustomAddon()">
                        <span class="addon-picker-plus-icon" x-show="!addonAddPending" x-cloak>
                            @include('livewire.auth.partials.groomer-plus-icon')
                        </span>
                        <span class="addon-picker-plus-spinner" x-show="addonAddPending" x-cloak aria-hidden="true">
                            <span class="groomer-plus-spinner"></span>
                        </span>
                    </button>
                </div>
                <template x-if="customAddons.length > 0">
                    <div class="groomer-chip-list groomer-chip-list-custom">
                        <template x-for="addon in customAddons" :key="'custom-addon-' + addon">
                            <label class="groomer-service-chip" :class="{ 'is-selected': isAddonSelected(addon) }">
                                <input type="checkbox" :value="addon" :checked="isAddonSelected(addon)"
                                    @change="toggleAddon(addon)">
                                <span x-text="addon"></span>
                            </label>
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
            </div>
        </div>

        <template x-if="selectedAddons.length > 0">
            <div class="services-card">
                <div class="services-header">
                    <p>List of Extra's &amp; Add-ons</p>
                    <span>Price <span>(£)</span></span>
                </div>
                <div class="services-list services-list--single">
                    <template x-for="(addonName, index) in selectedAddons" :key="'addon-row-' + addonKey(addonName)">
                        <div class="service-item service-item--addon">
                            <p class="service-item-title" x-text="(index + 1) + '. ' + addonName"></p>
                            <div class="service-item-side"
                                :class="{
                                    'service-item-side--price-only': !showAddonDescriptionEditor(addonName) && !
                                        showAddonDescriptionText(addonName)
                                }">
                                <div class="service-price-control">
                                    <span class="service-price-currency">£</span>
                                    <input type="number" class="service-price-input" min="0" step="1"
                                        x-model="addonPricing[addonKey(addonName)].price">
                                    <div class="service-price-steppers">
                                        <button type="button" class="service-stepper-btn"
                                            aria-label="Increase price"
                                            @click="stepPrice(addonKey(addonName), 1, 'addon')"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                viewBox="0 0 11 6" fill="none">
                                                <path d="M10.374 5.47852L5.3952 0.499696L0.499963 5.39494"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg></button>
                                        <button type="button" class="service-stepper-btn"
                                            aria-label="Decrease price"
                                            @click="stepPrice(addonKey(addonName), -1, 'addon')"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                                viewBox="0 0 11 6" fill="none">
                                                <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                                    stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></button>
                                    </div>
                                </div>
                                <template x-if="showAddonDescriptionEditor(addonName)">
                                    <button type="button"
                                        class="service-description-plus service-description-plus--side"
                                        aria-label="Save description" @click="commitAddonDescription(addonName)">
                                        @include('livewire.auth.partials.groomer-plus-icon')
                                    </button>
                                </template>
                            </div>
                            <template x-if="showAddonDescriptionText(addonName)">
                                <div class="service-item-default-description service-item-default-description--addon">
                                    <p class="service-item-description-text" x-text="addonDescriptionText(addonName)">
                                    </p>
                                </div>
                            </template>
                            <template x-if="showAddonDescriptionEditor(addonName)">
                                <div class="service-item-description service-item-description--addon">
                                    <p>Description (optional)</p>
                                    <input type="text" class="service-item-description-input"
                                        placeholder="Please write a short description of add-on provided."
                                        x-model="addonPricing[addonKey(addonName)].description"
                                        @keydown.enter.prevent="commitAddonDescription(addonName)">
                                </div>
                            </template>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        <div class="form-buttons basics-actions">
            <x-common.button type="button" label="Back" width="105px" bg-color="#FFFFFF" text-color="#9D9B98"
                border="1px solid rgba(59, 55, 49, 0.10)" :shadow="false" wire:click="goBack" />
            <x-common.button type="button" label="Continue" width="105px" bg-color="#FFC97A" text-color="#FFFFFF"
                loading-target="submitGroomerBusinessProfile" x-bind:disabled="!canContinue || submitting"
                x-bind:class="{ 'common-btn--disabled': !canContinue || submitting }"
                x-bind:style="{
                    width: '105px',
                    height: '48px',
                    border: 'none',
                    borderRadius: '96px',
                    backgroundColor: (canContinue && !submitting) ? '#FFC97A' : '#e5e7eb',
                    color: (canContinue && !submitting) ? '#FFFFFF' : '#9ca3af',
                    boxShadow: (canContinue && !submitting) ? '0 5px 8px 0 rgba(0, 0, 0, 0.10)' : 'none',
                }"
                @click="submitForm()" />
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
        justify-content: space-between;
        align-items: center;
    }

    .service-item>div:first-child {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
    }

    .service-item>div:first-child>p {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
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
    }

    .service-price-currency {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .service-price-input {
        width: 40px;
        border: none;
        outline: none;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        position: absolute;
        left: 24px;
        top: 50%;
        transform: translateY(-50%);
        padding: 0;
        background: transparent;
    }

    .service-price-input::-webkit-outer-spin-button,
    .service-price-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .service-price-steppers {
        display: flex;
        flex-direction: column;
        gap: 8px;
        color: #3B3731;
        font-size: 14px;
        line-height: 1;
        align-items: center;
        justify-content: center;
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .service-stepper-btn {
        border: none;
        background: transparent;
        padding: 0;
        margin: 0;
        width: 12px;
        height: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
    }

    .service-item-description {
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: start;
        width: 100%;

    }

    .service-item-description>p {
        margin-top: 15px;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-item-description-input-wrap {
        display: flex;
        align-items: center;
        gap: 0.9rem;
        width: 100%;
    }

    .service-item-description>div>input,
    .service-item-description-input-wrap>input {
        padding: 15px;
        flex: 1;
        max-width: 390px;
        height: 48px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        transition: border-color 0.15s ease;
    }

    .business-basics-wrap .service-item-description-input-wrap>input:focus {
        outline: none;
        border-color: var(--active-bg, #FFC97A);
    }

    .business-basics-wrap .service-price-control:focus-within {
        border-color: var(--active-bg, #FFC97A);
    }

    .service-description-plus {
        margin-top: 0.8rem;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: transparent;
        flex-shrink: 0;
        width: 64px;
        height: 64px;
        padding: 0;
    }

    .service-description-plus .groomer-plus-icon-svg,
    .service-description-plus svg {
        width: 64px;
        height: 64px;
        display: block;
    }

    .service-item-description-text {
        color: #9D9B98 !important;
        font-family: Lato !important;
        font-size: 14px !important;
        font-style: normal !important;
        font-weight: 400 !important;
        line-height: normal !important;
        width: 390px !important;
        text-align: left !important;
        margin: 0 !important;
        align-self: flex-start !important;
    }

    .service-item>div:last-child {
        width: 100%;
        display: flex;
        justify-content: flex-start;
        margin-top: -13px;
        margin-left: 31px;
    }

    .service-item>div:last-child .service-item-description-text {
        width: 100% !important;
        max-width: 390px;
    }

    .addon-picker-card {
        padding-top: 1.25rem;
        padding-bottom: 1.25rem;
    }

    .addon-picker-card>.basics-field>.form-label>span {
        line-height: 20px;
    }

    .addon-picker-input-wrap {
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }

    .addon-picker-plus {
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        background: transparent;
        flex-shrink: 0;
        width: 64px;
        height: 64px;
        padding: 0;
        position: relative;
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
        width: 64px;
        height: 64px;
    }

    .addon-picker-plus-icon .groomer-plus-icon-svg,
    .addon-picker-plus-icon svg {
        width: 64px;
        height: 64px;
        display: block;
    }

    .addon-picker-plus-spinner {
        border-radius: 50%;
    }

    .groomer-plus-spinner {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: #FFC97A;
        box-shadow: 0 5px 8px rgba(0, 0, 0, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .groomer-plus-spinner::after {
        content: "";
        width: 20px;
        height: 20px;
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
        border-color: #F6C676;
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
        background: #F6C676;
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
        padding-bottom: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .services-list--single .service-item:not(:last-child)::after {
        content: "";
        position: absolute;
        left: -25px;
        right: -25px;
        bottom: 0;
        border-bottom: 1px solid #E2E2E2;
    }

    .service-item-default-description {
        width: 100%;
        margin-top: -13px;
        margin-left: 31px;
    }

    .service-item--addon {
        display: grid;
        grid-template-columns: 1fr auto;
        column-gap: 1rem;
        align-items: start;
    }

    .service-item--addon .service-item-title {
        grid-column: 1;
        grid-row: 1;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 0;
        padding-top: 14px;
    }

    .service-item--addon .service-item-side {
        grid-column: 2;
        grid-row: 1 / span 2;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
        flex-shrink: 0;
    }

    .service-item--addon .service-item-side--price-only {
        grid-row: 1;
    }

    .service-item--addon:has(.service-item-side--price-only) {
        align-items: center;
    }

    .service-item--addon:has(.service-item-side--price-only) .service-item-title {
        padding-top: 0;
    }

    .service-item--addon .service-item-description--addon {
        grid-column: 1;
        grid-row: 2;
        width: 100%;
        margin-top: 0;
    }

    .service-item--addon .service-item-description--addon>p {
        margin-top: 15px;
        margin-bottom: 10px;
    }

    .service-item--addon .service-item-description-input {
        width: 100%;
        max-width: 390px;
        padding: 15px;
        height: 48px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        box-sizing: border-box;
        transition: border-color 0.15s ease;
    }

    .business-basics-wrap .service-item--addon .service-item-description-input:focus {
        outline: none;
        border-color: var(--active-bg, #FFC97A);
    }

    .service-item--addon .service-item-default-description--addon {
        grid-column: 1 / -1;
        margin-top: 0;
        margin-left: 0;
    }

    .service-description-plus--side {
        margin-top: 1.5rem;
    }
</style>
