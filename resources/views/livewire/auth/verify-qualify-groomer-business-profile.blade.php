<div class="business-basics-wrap" wire:key="verify-qualify-groomer-business-profile">
    <h1 class="business-basics-title">About Your Business</h1>

    <form wire:submit="submitGroomerBusinessProfile" class="business-basics-form">
        <div class="basics-card">
            <div class="basics-field">
                <label class="form-label" for="groomer-experience">Bio</label>
                <textarea id="groomer-experience" wire:model.live="groomer_experience" class="form-input basics-textarea"
                    placeholder="Describe your services, experience, and philosophy."
                    style="resize: none; overflow: hidden; height: 150px; width: 100%;"></textarea>
                @error('groomer_experience')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="basics-card">
            <div class="basics-field">
                <label class="form-label" style="margin-bottom: 2rem;">Specialties, Focus & Base Rate</label>
                <div class="groomer-focus-wrap">
                    <label class="form-label">Select Pet Specialty:</label>
                    <div class="groomer-pill-group">
                        <label
                            class="groomer-pill-option groomer-pill-specialty {{ in_array('dog', $groomer_pet_specialties, true) ? 'is-active' : '' }}">
                            <input type="checkbox" wire:model.live="groomer_pet_specialties" value="dog">
                            <span>Dog
                                <svg class="groomer-pill-icon" xmlns="http://www.w3.org/2000/svg" width="16"
                                    height="15" viewBox="0 0 16 15" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M8.02515 0C8.64209 0.000213345 9.22451 0.285449 9.60328 0.772461L11.7185 3.49219C11.8776 3.69667 12.1105 3.83152 12.3669 3.86816L14.3513 4.15137C14.7092 4.20264 15.0125 4.44317 15.1209 4.78809C15.3556 5.53533 15.6937 6.86423 15.4334 7.6582C15.1017 8.66932 14.5694 9.17545 13.6101 9.42285C11.9669 9.84646 10.6081 9.36208 9.00269 10.5859C8.61476 10.8817 8.32298 11.2488 8.10816 11.6592L7.93433 12.0166C6.99837 13.8635 4.67318 15.7352 2.92652 14.708C1.76272 14.0232 1.19689 12.6471 1.54273 11.3418L2.01832 9.5459C2.13774 9.53792 2.2548 9.52867 2.36597 9.51465C2.6604 9.47748 2.95561 9.41272 3.15406 9.28711C3.31249 9.18662 3.47116 9.01627 3.61597 8.83105C3.76469 8.64081 3.91577 8.41352 4.05836 8.18066C4.34353 7.71488 4.60442 7.20842 4.75855 6.88281C4.81749 6.75812 4.76399 6.60893 4.63941 6.5498C4.5149 6.49107 4.36662 6.54376 4.30738 6.66797C4.15908 6.98129 3.90564 7.47235 3.6316 7.91992C3.49464 8.14359 3.35417 8.35266 3.22144 8.52246C3.08483 8.69719 2.96988 8.81132 2.88648 8.86426C2.78857 8.92626 2.59053 8.98231 2.30347 9.01855C2.0271 9.05342 1.70289 9.06706 1.38257 9.06641C1.06811 9.06575 0.762227 9.04878 0.516364 9.03125C0.129202 8.76968 -0.0880056 8.27103 0.0339417 7.80859C1.06233 3.9097 1.6697 2.10636 2.67847 1.09766C3.77006 0.00665437 5.94579 3.69894e-05 5.97144 0H8.02515ZM8.57496 3.66016C8.11053 3.66016 7.61807 3.89064 7.61793 4.8125C7.61793 5.44915 8.42917 4.8125 8.95777 4.8125C9.48622 4.81262 9.53199 5.44909 9.53199 4.8125C9.5318 4.17611 9.10336 3.66027 8.57496 3.66016Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </label>
                        <label
                            class="groomer-pill-option groomer-pill-specialty {{ in_array('cat', $groomer_pet_specialties, true) ? 'is-active' : '' }}">
                            <input type="checkbox" wire:model.live="groomer_pet_specialties" value="cat">
                            <span>Cat
                                <svg class="groomer-pill-icon" xmlns="http://www.w3.org/2000/svg" width="11"
                                    height="15" viewBox="0 0 11 15" fill="none">
                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                        d="M4.99889 2.53516C5.53684 2.4688 6.2816 2.4856 6.994 2.72656C7.78167 2.99305 8.5426 3.54017 8.9149 4.55566L10.4501 5.29199L10.4833 5.41211C10.6868 6.146 10.8081 7.28565 10.493 8.32324C10.3341 8.84623 10.0619 9.34947 9.62877 9.75684C9.19459 10.1652 8.61113 10.4656 7.8485 10.6016C5.10446 11.0909 3.54731 13.4378 3.11803 14.5459C2.94136 15.0506 1.89466 15.1787 1.6483 14.7041C-2.03157 7.61478 1.27786 2.01708 3.56236 0L4.99889 2.53516ZM6.69908 5.09961C6.29638 5.09961 5.869 5.30013 5.869 6.09961C5.86942 6.65073 6.57298 6.09961 7.03111 6.09961C7.48905 6.09978 7.52914 6.65064 7.52916 6.09961C7.52916 5.54772 7.15731 5.09966 6.69908 5.09961Z"
                                        fill="currentColor" />
                                </svg>
                            </span>
                        </label>
                        <label
                            class="groomer-pill-option groomer-pill-specialty {{ in_array('other', $groomer_pet_specialties, true) ? 'is-active' : '' }}">
                            <input type="checkbox" wire:model.live="groomer_pet_specialties" value="other">
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
                    <input id="groomer-specialty-other" type="text" wire:model.live="groomer_specialty_other"
                        class="form-input" placeholder="e.g., Luxury grooming with a gentle touch.">
                    @error('groomer_specialty_other')
                        <span class="error-text">{{ $message }}</span>
                    @enderror

                    <label class="form-label">Select Pet Size:</label>
                    <div class="groomer-pill-group">
                        <label
                            class="groomer-pill-option groomer-pill-size {{ in_array('small', $groomer_pet_sizes, true) ? 'is-active' : '' }}">
                            <input type="checkbox" wire:model.live="groomer_pet_sizes" value="small">
                            <span>Small 0-7 kg</span>
                        </label>
                        <label
                            class="groomer-pill-option groomer-pill-size {{ in_array('medium', $groomer_pet_sizes, true) ? 'is-active' : '' }}">
                            <input type="checkbox" wire:model.live="groomer_pet_sizes" value="medium">
                            <span>Medium 8-18 kg</span>
                        </label>
                        <label
                            class="groomer-pill-option groomer-pill-size {{ in_array('large', $groomer_pet_sizes, true) ? 'is-active' : '' }}">
                            <input type="checkbox" wire:model.live="groomer_pet_sizes" value="large">
                            <span>Large 19+ kg</span>
                        </label>
                    </div>
                    @error('groomer_pet_sizes')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="services-card">
            <div class="services-header">
                <p>Services</p>
                <span>Price <span>(£)</span></span>
            </div>
            <div class="services-list">
                <div class="service-item">
                    <div>
                        <p>1. Full Groom (bath, dry, haircut)</p>
                        <div class="service-price-control">
                            <span class="service-price-currency">£</span>
                            <input type="number" class="service-price-input" min="0" step="1"
                                wire:model.live="groomer_services_pricing.full_groom.price">
                            <div class="service-price-steppers">
                                <button type="button" class="service-stepper-btn" aria-label="Increase price"
                                    onclick="const i=this.closest('.service-price-control').querySelector('.service-price-input'); i.stepUp(); i.dispatchEvent(new Event('input',{bubbles:true}));"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.374 5.47852L5.3952 0.499696L0.499963 5.39494" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                                <button type="button" class="service-stepper-btn" aria-label="Decrease price"
                                    onclick="const i=this.closest('.service-price-control').querySelector('.service-price-input'); i.stepDown(); i.dispatchEvent(new Event('input',{bubbles:true}));"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                            </div>
                        </div>
                    </div>
                    <div class="service-item-description" style="margin-left: 2rem; margin-top: 0.8rem;">
                        <p>Description (optional)</p>
                        <div>
                            <input type="text" placeholder="Please write a short description of service provided."
                                wire:model.live="groomer_services_pricing.full_groom.description">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                viewBox="0 0 64 64" fill="none">
                                <g filter="url(#filter0_d_58_696)">
                                    <rect x="8" y="3" width="48" height="48" rx="24" fill="#FFC97A" />
                                </g>
                                <path d="M32 19V27.495M32 27.495V35.99M32 27.495H40.495M32 27.495H23.505"
                                    stroke="white" stroke-width="2" stroke-linecap="round" />
                                <defs>
                                    <filter id="filter0_d_58_696" x="0" y="0" width="64" height="64"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="5" />
                                        <feGaussianBlur stdDeviation="4" />
                                        <feComposite in2="hardAlpha" operator="out" />
                                        <feColorMatrix type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_58_696" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_58_696"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>
            <div class="services-list">
                <div class="service-item">
                    <div>
                        <p>2. Face Trim Only</p>
                        <div class="service-price-control">
                            <span class="service-price-currency">£</span>
                            <input type="number" class="service-price-input" min="0" step="1"
                                wire:model.live="groomer_services_pricing.face_trim.price">
                            <div class="service-price-steppers">
                                <button type="button" class="service-stepper-btn" aria-label="Increase price"
                                    onclick="const i=this.closest('.service-price-control').querySelector('.service-price-input'); i.stepUp(); i.dispatchEvent(new Event('input',{bubbles:true}));"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.374 5.47852L5.3952 0.499696L0.499963 5.39494" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                                <button type="button" class="service-stepper-btn" aria-label="Decrease price"
                                    onclick="const i=this.closest('.service-price-control').querySelector('.service-price-input'); i.stepDown(); i.dispatchEvent(new Event('input',{bubbles:true}));"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                            </div>
                        </div>
                    </div>
                    <div>
                        <p class="service-item-description-text">Targeted trimming of facial hair to maintain
                            cleanliness, comfort, and visibility without a
                            full groom.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="basics-card addon-picker-card">
            <div class="basics-field">
                <label class="form-label">Add-on <span>(List out your add-ons (inc. short description)</span></label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="Flea & Tick Treatment"
                        wire:model.live="groomer_addon_input">
                    <button type="button" class="addon-picker-plus" aria-label="Add add-on"
                        wire:click="addGroomerCustomAddon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"
                            fill="none">
                            <g filter="url(#filter0_d_58_696)">
                                <rect x="8" y="3" width="48" height="48" rx="24" fill="#FFC97A" />
                            </g>
                            <path d="M32 19V27.495M32 27.495V35.99M32 27.495H40.495M32 27.495H23.505" stroke="white"
                                stroke-width="2" stroke-linecap="round" />
                            <defs>
                                <filter id="filter0_d_58_696" x="0" y="0" width="64" height="64"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="5" />
                                    <feGaussianBlur stdDeviation="4" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix"
                                        result="effect1_dropShadow_58_696" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_58_696"
                                        result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </div>
                @if (count($groomer_custom_addons) > 0)
                    <div class="addon-checkbox-list addon-checkbox-list-custom">
                        @foreach ($groomer_custom_addons as $addon)
                            <label
                                class="addon-checkbox-item {{ in_array($addon, $groomer_selected_addons, true) ? 'is-selected' : '' }}">
                                <input type="checkbox" wire:model.live="groomer_selected_addons"
                                    value="{{ $addon }}">
                                <span>{{ $addon }}</span>
                            </label>
                        @endforeach
                    </div>
                @endif
                <p class="addon-picker-label">Or choose from FursGo add-ons:</p>
                @php
                    $fursgoAddons = [
                        'Flea & Tick Treatment',
                        'Hypoallergenic Shampoo Upgrade',
                        'Tear-Stain Treatment',
                        'Coat Shine Spray',
                        'Nail Grinding',
                        'Coat Colour Enhancing Shampoo',
                        'Fast-Dry Service (express grooming)',
                        'Breath Freshner Gel',
                        'Deep Conditioning Mask',
                        'Shed-Control Shampoo',
                        'Deodorising Treatment',
                        'Anti-Itch Treatment',
                        'Soft-Claws / Nail Caps Application',
                        'Premium Fragrance Upgrade',
                        'Paw Fur Shaping',
                    ];
                @endphp
                <div class="addon-checkbox-list">
                    @foreach ($fursgoAddons as $addon)
                        <label
                            class="addon-checkbox-item {{ in_array($addon, $groomer_selected_addons, true) ? 'is-selected' : '' }}">
                            <input type="checkbox" wire:model.live="groomer_selected_addons"
                                value="{{ $addon }}">
                            <span>{{ $addon }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="services-card">
            <div class="services-header">
                <p>Add-ons</p>
                <span>Price <span>(£)</span></span>
            </div>
            <div class="services-list">
                <div class="service-item">
                    <div>
                        <p>1. Flea & Tick Treatment</p>
                        <div class="service-price-control">
                            <span class="service-price-currency">£</span>
                            <input type="number" class="service-price-input" min="0" step="1"
                                wire:model.live="groomer_addon_pricing.flea_tick.price">
                            <div class="service-price-steppers">
                                <button type="button" class="service-stepper-btn" aria-label="Increase price"
                                    onclick="const i=this.closest('.service-price-control').querySelector('.service-price-input'); i.stepUp(); i.dispatchEvent(new Event('input',{bubbles:true}));"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.374 5.47852L5.3952 0.499696L0.499963 5.39494" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                                <button type="button" class="service-stepper-btn" aria-label="Decrease price"
                                    onclick="const i=this.closest('.service-price-control').querySelector('.service-price-input'); i.stepDown(); i.dispatchEvent(new Event('input',{bubbles:true}));"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                            </div>
                        </div>
                    </div>
                    <div class="service-item-description" style="margin-left: 2rem; margin-top: 0.8rem;">
                        <p>Description (optional)</p>
                        <div>
                            <input type="text" placeholder="Please write a short description of add-on provided."
                                wire:model.live="groomer_addon_pricing.flea_tick.description">
                            <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64"
                                viewBox="0 0 64 64" fill="none">
                                <g filter="url(#filter0_d_58_696)">
                                    <rect x="8" y="3" width="48" height="48" rx="24" fill="#FFC97A" />
                                </g>
                                <path d="M32 19V27.495M32 27.495V35.99M32 27.495H40.495M32 27.495H23.505"
                                    stroke="white" stroke-width="2" stroke-linecap="round" />
                                <defs>
                                    <filter id="filter0_d_58_696" x="0" y="0" width="64" height="64"
                                        filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                        <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                        <feColorMatrix in="SourceAlpha" type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                        <feOffset dy="5" />
                                        <feGaussianBlur stdDeviation="4" />
                                        <feComposite in2="hardAlpha" operator="out" />
                                        <feColorMatrix type="matrix"
                                            values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0" />
                                        <feBlend mode="normal" in2="BackgroundImageFix"
                                            result="effect1_dropShadow_58_696" />
                                        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_58_696"
                                            result="shape" />
                                    </filter>
                                </defs>
                            </svg>
                        </div>
                    </div>
                </div>

            </div>
            <div class="services-list">
                <div class="service-item">
                    <div
                        style="width: 100%;margin-left: 0;display: flex;justify-content: space-between;align-items: center;">
                        <p>2. Fast-Dry Service (express grooming)</p>
                        <div class="service-price-control">
                            <span class="service-price-currency">£</span>
                            <input type="number" class="service-price-input" min="0" step="1"
                                wire:model.live="groomer_addon_pricing.fast_dry.price">
                            <div class="service-price-steppers">
                                <button type="button" class="service-stepper-btn" aria-label="Increase price"
                                    onclick="const i=this.closest('.service-price-control').querySelector('.service-price-input'); i.stepUp(); i.dispatchEvent(new Event('input',{bubbles:true}));"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.374 5.47852L5.3952 0.499696L0.499963 5.39494" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                                <button type="button" class="service-stepper-btn" aria-label="Decrease price"
                                    onclick="const i=this.closest('.service-price-control').querySelector('.service-price-input'); i.stepDown(); i.dispatchEvent(new Event('input',{bubbles:true}));"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="11" height="6"
                                        viewBox="0 0 11 6" fill="none">
                                        <path d="M10.374 0.5L5.3952 5.47882L0.499963 0.583578" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="form-buttons basics-actions">
            <button type="button" class="back-btn" wire:click="goBack">
                <span>Back</span>
            </button>
            <button type="submit"
                class="submit-btn {{ $this->isGroomerBusinessProfileContinueEnabled() ? 'btn-active' : 'btn-disabled' }}"
                wire:loading.attr="disabled" wire:target="submitGroomerBusinessProfile"
                @if (!$this->isGroomerBusinessProfileContinueEnabled()) disabled @endif>
                <span wire:loading.remove wire:target="submitGroomerBusinessProfile">Continue</span>
                <span wire:loading wire:target="submitGroomerBusinessProfile">Saving…</span>
            </button>
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
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-item-description>div {
        display: flex;
        align-items: center;
        width: 100%;
        gap: 10px;
    }

    .service-item-description>div>input {
        padding: 15px;
        width: 390px;
        height: 48px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
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
</style>
