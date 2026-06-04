<div class="business-basics-wrap" wire:key="verify-qualify-spacer-business-profile">
    <h1 class="business-basics-title">About Your Business</h1>

    <form wire:submit="submitSpacerBusinessProfile" class="business-basics-form">
        {{-- Bio --}}
        <div class="basics-card">
            <div class="basics-field">
                <label class="form-label" for="spacer-bio">Bio</label>
                <textarea id="spacer-bio" wire:model.live="spacer_bio" class="form-input basics-textarea"
                    placeholder="Describe your services, experience, and philosophy."
                    style="resize: none; overflow: hidden; min-height: 150px; width: 100%;"></textarea>
                @error('spacer_bio')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        {{-- Services & Pricing --}}
        <div class="services-card">
            <div class="services-header spacer-services-header">
                <div>
                    <p>Services & Pricing</p>
                    <span>Choose the pricing structure for your space</span>
                </div>
                <span>Price <span>(£)</span></span>
            </div>
            <div class="spacer-services-list-body">
                @foreach ($this->spacerServicesPricingRowLabels() as $slug => $rowLabel)
                    <label
                        class="spacer-service-row {{ !empty($spacer_services_pricing[$slug]['selected']) ? 'spacer-service-row--selected' : '' }}">
                        <input type="checkbox" wire:model.live="spacer_services_pricing.{{ $slug }}.selected"
                            class="spacer-select-input">
                        <div class="spacer-service-row-inner">
                            <div
                                class="spacer-select-row {{ !empty($spacer_services_pricing[$slug]['selected']) ? 'is-selected' : '' }}">
                                <span class="spacer-select-dot" aria-hidden="true"></span>
                                <span class="spacer-service-label spacer-service-label--with-icon">
                                    <span class="spacer-service-label__icon" aria-hidden="true">
                                        @if ($slug === 'hourly')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="22"
                                                viewBox="0 0 26 22" fill="none">
                                                <path
                                                    d="M10.9401 0C15.9387 0 20.1532 3.35263 21.4601 7.93179C21.5815 8.35738 21.2287 8.75207 20.7861 8.75207C20.6431 8.75207 20.5181 8.65675 20.4831 8.5181C19.4037 4.25167 15.5419 1.09401 10.9401 1.09401C5.50224 1.09401 1.09401 5.50225 1.09401 10.9401C1.09401 15.8782 4.72947 19.9655 9.46984 20.6752C9.71705 20.7122 9.912 20.9111 9.94481 21.1589C9.99054 21.5043 9.71174 21.8142 9.36694 21.7645C4.07035 21.0019 0 16.4479 0 10.9401C0 4.89805 4.89804 0 10.9401 0Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M22.3843 17.0285C22.6177 16.7915 22.8748 16.6 23.1556 16.4541C23.44 16.3083 23.7664 16.2353 24.1347 16.2353C24.441 16.2353 24.7091 16.2864 24.9388 16.3885C25.1722 16.4906 25.3673 16.6347 25.5241 16.8206C25.6809 17.003 25.7994 17.2236 25.8796 17.4825C25.9599 17.7414 26 18.0277 26 18.3413V21.8804H24.9279V18.3413C24.9279 17.9438 24.8367 17.6357 24.6544 17.4169C24.472 17.1944 24.193 17.0832 23.8174 17.0832C23.5439 17.0832 23.2868 17.1488 23.0462 17.2801C22.8091 17.4078 22.5885 17.5828 22.3843 17.8052V21.8804H21.3176V13.8066H22.3843V17.0285Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M19.8125 21.0661V21.8812H15.5677V21.0661H17.2306V15.9188C17.2306 15.7547 17.2361 15.5851 17.247 15.4101L15.9124 16.5424C15.8613 16.5825 15.8102 16.608 15.7592 16.619C15.7081 16.6299 15.6589 16.6317 15.6115 16.6244C15.5677 16.6172 15.5276 16.6026 15.4912 16.5807C15.4583 16.5552 15.431 16.5296 15.4091 16.5041L15.0754 16.0392L17.4276 14.0098H18.2973V21.0661H19.8125Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M11.4871 7.65833C11.4871 7.35623 11.732 7.11133 12.0341 7.11133C12.3362 7.11133 12.5811 7.35623 12.5811 7.65833V12.5814H8.75208C8.44998 12.5814 8.20508 12.3365 8.20508 12.0344C8.20508 11.7323 8.44998 11.4874 8.75208 11.4874H11.4871V7.65833Z"
                                                    fill="#3B3731" />
                                            </svg>
                                        @elseif ($slug === 'half_day')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="22"
                                                viewBox="0 0 26 22" fill="none">
                                                <path
                                                    d="M10.94 0C15.9386 0 20.1531 3.35262 21.4599 7.93175C21.5813 8.35734 21.2285 8.75203 20.786 8.75203C20.643 8.75203 20.518 8.65671 20.4829 8.51806C19.4036 4.25165 15.5418 1.094 10.94 1.094C5.5022 1.094 1.094 5.50222 1.094 10.94C1.094 15.8781 4.72943 19.9654 9.46977 20.6751C9.71697 20.7121 9.91192 20.911 9.94473 21.1588C9.99046 21.5042 9.71166 21.8141 9.36686 21.7644C4.07032 21.0018 0 16.4478 0 10.94C0 4.89802 4.898 0 10.94 0Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M22.384 17.0285C22.6174 16.7914 22.8745 16.6 23.1553 16.4541C23.4397 16.3083 23.7661 16.2353 24.1344 16.2353C24.4407 16.2353 24.7088 16.2864 24.9385 16.3885C25.1719 16.4906 25.367 16.6346 25.5238 16.8206C25.6806 17.003 25.7991 17.2236 25.8794 17.4825C25.9596 17.7414 25.9997 18.0277 25.9997 18.3413V21.8804H24.9276V18.3413C24.9276 17.9438 24.8364 17.6357 24.6541 17.4169C24.4717 17.1944 24.1928 17.0832 23.8172 17.0832C23.5437 17.0832 23.2866 17.1488 23.0459 17.2801C22.8089 17.4077 22.5882 17.5828 22.384 17.8052V21.8804H21.3174V13.8066H22.384V17.0285Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M18.1932 19.0137V15.9504C18.1932 15.8447 18.195 15.7335 18.1987 15.6168C18.206 15.4964 18.2187 15.3724 18.237 15.2448L15.4856 19.0137H18.1932ZM20.2663 19.0137V19.6318C20.2663 19.6938 20.2463 19.7466 20.2062 19.7904C20.1661 19.8342 20.1095 19.856 20.0366 19.856H19.1286V21.88H18.1932V19.856H14.7471C14.6669 19.856 14.5994 19.8342 14.5447 19.7904C14.49 19.743 14.4536 19.6846 14.4353 19.6154L14.3259 19.0684L18.1221 14.0195H19.1286V19.0137H20.2663Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M11.4868 7.65833C11.4868 7.35623 11.7317 7.11133 12.0338 7.11133C12.3359 7.11133 12.5808 7.35623 12.5808 7.65833V12.5813H8.75183C8.44973 12.5813 8.20483 12.3364 8.20483 12.0343C8.20483 11.7322 8.44973 11.4873 8.75183 11.4873H11.4868V7.65833Z"
                                                    fill="#3B3731" />
                                            </svg>
                                        @elseif ($slug === 'full_day')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="22"
                                                viewBox="0 0 26 22" fill="none">
                                                <path
                                                    d="M10.9401 0C15.9387 0 20.1532 3.35263 21.46 7.93179C21.5815 8.35737 21.2287 8.75207 20.7861 8.75207C20.6431 8.75207 20.5181 8.65675 20.4831 8.51809C19.4037 4.25167 15.5419 1.09401 10.9401 1.09401C5.50224 1.09401 1.09401 5.50225 1.09401 10.9401C1.09401 15.8782 4.72946 19.9655 9.46984 20.6752C9.71704 20.7122 9.91199 20.9111 9.9448 21.1589C9.99053 21.5042 9.71173 21.8142 9.36693 21.7645C4.07035 21.0019 0 16.4479 0 10.9401C0 4.89804 4.89803 0 10.9401 0Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M22.3843 17.0285C22.6177 16.7915 22.8748 16.6 23.1556 16.4541C23.44 16.3083 23.7664 16.2353 24.1347 16.2353C24.441 16.2353 24.709 16.2864 24.9388 16.3885C25.1722 16.4906 25.3673 16.6347 25.5241 16.8206C25.6809 17.003 25.7994 17.2236 25.8796 17.4825C25.9599 17.7414 26 18.0277 26 18.3413V21.8804H24.9279V18.3413C24.9279 17.9438 24.8367 17.6357 24.6543 17.4169C24.472 17.1944 24.193 17.0832 23.8174 17.0832C23.5439 17.0832 23.2868 17.1488 23.0462 17.2801C22.8091 17.4078 22.5885 17.5828 22.3843 17.8052V21.8804H21.3176V13.8066H22.3843V17.0285Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M17.3947 21.1197C17.6427 21.1197 17.8633 21.085 18.0566 21.0157C18.2499 20.9464 18.414 20.8498 18.5489 20.7258C18.6838 20.5982 18.7859 20.4469 18.8552 20.2718C18.9245 20.0968 18.9591 19.9053 18.9591 19.6975C18.9591 19.4422 18.9172 19.2234 18.8333 19.0411C18.7495 18.8551 18.6364 18.7037 18.4942 18.587C18.352 18.4667 18.1861 18.3792 17.9964 18.3245C17.8068 18.2661 17.6062 18.237 17.3947 18.237C17.1796 18.237 16.9772 18.2661 16.7875 18.3245C16.5979 18.3792 16.432 18.4667 16.2898 18.587C16.1476 18.7037 16.0345 18.8551 15.9506 19.0411C15.8704 19.2234 15.8303 19.4422 15.8303 19.6975C15.8303 19.9053 15.8649 20.0968 15.9342 20.2718C16.0035 20.4469 16.1056 20.5982 16.2405 20.7258C16.3755 20.8498 16.5396 20.9464 16.7328 21.0157C16.9261 21.085 17.1467 21.1197 17.3947 21.1197ZM17.3947 14.758C17.1686 14.758 16.9699 14.7927 16.7985 14.8619C16.6307 14.9276 16.4903 15.0187 16.3773 15.1354C16.2642 15.2521 16.1785 15.3871 16.1202 15.5402C16.0655 15.6934 16.0382 15.8575 16.0382 16.0325C16.0382 16.2039 16.0619 16.3717 16.1093 16.5358C16.1603 16.6962 16.2387 16.8403 16.3445 16.9679C16.4539 17.0919 16.5943 17.1922 16.7657 17.2688C16.9371 17.3453 17.1467 17.3836 17.3947 17.3836C17.639 17.3836 17.8469 17.3453 18.0183 17.2688C18.1933 17.1922 18.3337 17.0919 18.4395 16.9679C18.5489 16.8403 18.6273 16.6962 18.6747 16.5358C18.7258 16.3717 18.7513 16.2039 18.7513 16.0325C18.7513 15.8575 18.7221 15.6934 18.6638 15.5402C18.6091 15.3871 18.5252 15.2521 18.4121 15.1354C18.2991 15.0187 18.1569 14.9276 17.9855 14.8619C17.8177 14.7927 17.6208 14.758 17.3947 14.758ZM18.5708 17.7829C19.0594 17.9288 19.4296 18.164 19.6812 18.4886C19.9365 18.8131 20.0641 19.2234 20.0641 19.7193C20.0641 20.0621 19.9985 20.3721 19.8672 20.6492C19.7395 20.9264 19.5572 21.1634 19.3202 21.3603C19.0868 21.5536 18.806 21.7031 18.4778 21.8089C18.1496 21.9146 17.7886 21.9675 17.3947 21.9675C17.0009 21.9675 16.6399 21.9146 16.3117 21.8089C15.9834 21.7031 15.7008 21.5536 15.4638 21.3603C15.2304 21.1634 15.0481 20.9264 14.9168 20.6492C14.7892 20.3721 14.7253 20.0621 14.7253 19.7193C14.7253 19.2234 14.8512 18.8131 15.1028 18.4886C15.358 18.164 15.73 17.9288 16.2187 17.7829C15.8139 17.6298 15.5094 17.4019 15.3052 17.0992C15.101 16.7929 14.9988 16.43 14.9988 16.0106C14.9988 15.7189 15.0554 15.4472 15.1684 15.1956C15.2851 14.9403 15.4474 14.7197 15.6552 14.5337C15.8668 14.3478 16.1202 14.2019 16.4156 14.0961C16.711 13.9904 17.0373 13.9375 17.3947 13.9375C17.7521 13.9375 18.0785 13.9904 18.3739 14.0961C18.6692 14.2019 18.9209 14.3478 19.1287 14.5337C19.3402 14.7197 19.5025 14.9403 19.6156 15.1956C19.7322 15.4472 19.7906 15.7189 19.7906 16.0106C19.7906 16.43 19.6885 16.7929 19.4843 17.0992C19.2801 17.4019 18.9756 17.6298 18.5708 17.7829Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M11.4871 7.65833C11.4871 7.35623 11.732 7.11133 12.0341 7.11133C12.3362 7.11133 12.5811 7.35623 12.5811 7.65833V12.5814H8.75208C8.44998 12.5814 8.20508 12.3365 8.20508 12.0344C8.20508 11.7323 8.44998 11.4874 8.75208 11.4874H11.4871V7.65833Z"
                                                    fill="#3B3731" />
                                            </svg>
                                        @endif
                                    </span>
                                    <span class="spacer-service-label__text">
                                        <span class="spacer-service-label__name">{{ $rowLabel['name'] }}</span>
                                        @if (!empty($rowLabel['meta']))
                                            <span class="spacer-service-label__meta"> {{ $rowLabel['meta'] }}</span>
                                        @endif
                                    </span>
                                </span>
                            </div>
                            <div class="service-price-control">
                                <span class="service-price-currency">£</span>
                                <input type="number" class="service-price-input" min="0" step="1"
                                    wire:model.live="spacer_services_pricing.{{ $slug }}.price">
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
                    </label>
                @endforeach
            </div>
        </div>
        @error('spacer_services_pricing')
            <span class="error-text" style="display:block;margin-top:0.5rem;">{{ $message }}</span>
        @enderror

        {{-- Add-on Services --}}
        <div class="basics-card addon-picker-card" style="margin-top: 1.5rem;">
            <div class="basics-field">
                <div class="spacer-services-header">
                    <div style="margin-bottom: 1rem;">
                        <p>Add-on Services</p>
                        <span>Choose the add-ons and pricing structure for your space</span>
                    </div>
                </div>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="Early Hours Access"
                        wire:model.live="spacer_addon_input">
                    <button type="button" class="addon-picker-plus" aria-label="Add add-on"
                        wire:click="addSpacerCustomAddonRow">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"
                            fill="none">
                            <g filter="url(#spacer_plus)">
                                <rect x="8" y="3" width="48" height="48" rx="24" fill="#FFC97A" />
                            </g>
                            <path d="M32 19V27.495M32 27.495V35.99M32 27.495H40.495M32 27.495H23.505" stroke="white"
                                stroke-width="2" stroke-linecap="round" />
                            <defs>
                                <filter id="spacer_plus" x="0" y="0" width="64" height="64"
                                    filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="5" />
                                    <feGaussianBlur stdDeviation="4" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix"
                                        values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow"
                                        result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </div>
                @foreach ($spacer_addon_custom_rows as $idx => $row)
                    <div class="spacer-addon-custom-row" wire:key="spacer-addon-custom-{{ $idx }}">
                        <label class="spacer-select-row sm {{ !empty($row['selected']) ? 'is-selected' : '' }}">
                            <input type="checkbox" class="spacer-select-input"
                                wire:model.live="spacer_addon_custom_rows.{{ $idx }}.selected">
                            <span class="spacer-select-dot" aria-hidden="true"></span>
                            <span class="spacer-service-label">{{ $row['name'] }}</span>
                        </label>
                        <div class="service-price-control">
                            <span class="service-price-currency">£</span>
                            <input type="number" class="service-price-input" min="0" step="1"
                                wire:model.live="spacer_addon_custom_rows.{{ $idx }}.price">
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
                @endforeach
                <p class="addon-picker-label">Or choose from FursGo add-ons:</p>
                @foreach ($this->spacerFursgoAddonCatalog() as $slug => $addonLabel)
                    <div class="spacer-addon-fursgo-row" wire:key="spacer-fursgo-{{ $slug }}">
                        <label
                            class="spacer-select-row sm {{ !empty($spacer_addons_service[$slug]['selected']) ? 'is-selected' : '' }}">
                            <input type="checkbox" class="spacer-select-input"
                                wire:model.live="spacer_addons_service.{{ $slug }}.selected">
                            <span class="spacer-select-dot" aria-hidden="true"></span>
                            <span class="spacer-service-label">{{ $addonLabel }}</span>
                        </label>
                        <div class="service-price-control">
                            <span class="service-price-currency">£</span>
                            <input type="number" class="service-price-input" min="0" step="1"
                                wire:model.live="spacer_addons_service.{{ $slug }}.price">
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
                @endforeach
            </div>
        </div>

        {{-- Suitable For --}}
        <div class="basics-card" style="margin-top: 1.5rem;">
            <div class="spacer-services-header">
                <div style="margin-bottom: 1rem;">
                    <p>Suitable For</p>
                    <span>Select the grooming services your space supports</span>
                </div>
            </div>
            <div class="groomer-pill-group spacer-suitable-grid">
                @foreach ($this->spacerSuitableForCatalog() as $option)
                    <label
                        class="groomer-pill-option groomer-pill-suitable {{ in_array($option, $spacer_suitable_for, true) ? 'is-active' : '' }}">
                        <input type="checkbox" wire:model.live="spacer_suitable_for" value="{{ $option }}">
                        <span class="groomer-pill-suitable__tick" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="10"
                                viewBox="0 0 14 10" fill="none">
                                <path
                                    d="M13.6793 0.289386C13.5867 0.197689 13.4765 0.124907 13.3551 0.0752394C13.2337 0.0255714 13.1035 0 12.972 0C12.8405 0 12.7103 0.0255714 12.5889 0.0752394C12.4675 0.124907 12.3574 0.197689 12.2647 0.289386L4.84329 7.58766L1.72529 4.51573C1.62913 4.42451 1.51563 4.35279 1.39125 4.30465C1.26688 4.25652 1.13406 4.23291 1.0004 4.23518C0.86673 4.23745 0.734828 4.26555 0.612221 4.31789C0.489614 4.37022 0.378704 4.44576 0.285823 4.54019C0.192942 4.63462 0.119909 4.74609 0.0708932 4.86824C0.0218778 4.99039 -0.00216024 5.12082 0.000152332 5.25209C0.0024649 5.38336 0.0310826 5.5129 0.0843711 5.63331C0.13766 5.75372 0.214575 5.86265 0.310727 5.95386L4.13601 9.71062C4.22862 9.80231 4.3388 9.87509 4.46019 9.92476C4.58158 9.97443 4.71179 10 4.84329 10C4.9748 10 5.105 9.97443 5.22639 9.92476C5.34779 9.87509 5.45796 9.80231 5.55057 9.71062L13.6793 1.72752C13.7804 1.63591 13.8611 1.52472 13.9163 1.40096C13.9715 1.2772 14 1.14356 14 1.00845C14 0.873344 13.9715 0.7397 13.9163 0.615943C13.8611 0.492186 13.7804 0.380998 13.6793 0.289386Z"
                                    fill="#FDFCF8" />
                            </svg>
                        </span>
                        <span class="groomer-pill-suitable__text">{{ $option }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        {{-- Rules & Restrictions --}}
        <div class="basics-card addon-picker-card" style="margin-top: 1.5rem;">
            <div class="basics-field">
                <div class="spacer-services-header">
                    <div style="margin-bottom: 1rem;">
                        <p>Rules & Restrictions</p>
                        <span>Set expectations for groomers using your space</span>
                    </div>
                </div>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="No Food or Drink"
                        wire:model.live="spacer_rule_input">
                    <button type="button" class="addon-picker-plus" aria-label="Add rule"
                        wire:click="addSpacerRuleCustom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"
                            fill="none">
                            <g>
                                <rect x="8" y="3" width="48" height="48" rx="24" fill="#FFC97A" />
                            </g>
                            <path d="M32 19V27.495M32 27.495V35.99M32 27.495H40.495M32 27.495H23.505" stroke="white"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                @if (count($spacer_rules_custom) > 0)
                    <ul class="spacer-custom-chips">
                        @foreach ($spacer_rules_custom as $r)
                            <li class="spacer-custom-chip">
                                <span class="spacer-custom-chip__marker" aria-hidden="true"></span>
                                <span>{{ $r }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <p class="addon-picker-label">Or choose from FursGo rules &amp; restrictions:</p>
                <div class="addon-checkbox-list">
                    @foreach ($this->spacerRulesPresetCatalog() as $ruleLabel)
                        <label
                            class="addon-checkbox-item {{ in_array($ruleLabel, $spacer_rules_preset_selected, true) ? 'is-selected' : '' }}">
                            <input type="checkbox" wire:model.live="spacer_rules_preset_selected"
                                value="{{ $ruleLabel }}">
                            <span>{{ $ruleLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Amenities --}}
        <div class="basics-card addon-picker-card" style="margin-top: 1.5rem;">
            <div class="basics-field">
                <label class="form-label">Custom amenity</label>
                <div class="addon-picker-input-wrap">
                    <input type="text" class="form-input" placeholder="Premium shampoos provided"
                        wire:model.live="spacer_amenity_input">
                    <button type="button" class="addon-picker-plus" aria-label="Add amenity"
                        wire:click="addSpacerAmenityCustom">
                        <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 64 64"
                            fill="none">
                            <g>
                                <rect x="8" y="3" width="48" height="48" rx="24" fill="#FFC97A" />
                            </g>
                            <path d="M32 19V27.495M32 27.495V35.99M32 27.495H40.495M32 27.495H23.505" stroke="white"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                @if (count($spacer_amenities_custom) > 0)
                    <ul class="spacer-custom-chips">
                        @foreach ($spacer_amenities_custom as $a)
                            <li class="spacer-custom-chip">
                                <span class="spacer-custom-chip__marker" aria-hidden="true"></span>
                                <span>{{ $a }}</span>
                            </li>
                        @endforeach
                    </ul>
                @endif
                <p class="addon-picker-label">Or choose from FursGo amenities:</p>
                <div class="addon-checkbox-list">
                    @foreach ($this->spacerAmenitiesPresetCatalog() as $amLabel)
                        <label
                            class="addon-checkbox-item {{ in_array($amLabel, $spacer_amenities_preset_selected, true) ? 'is-selected' : '' }}">
                            <input type="checkbox" wire:model.live="spacer_amenities_preset_selected"
                                value="{{ $amLabel }}">
                            <span>{{ $amLabel }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="form-buttons basics-actions">
            <button type="button" class="back-btn" wire:click="goBack">
                <span>Back</span>
            </button>
            <button type="submit"
                class="submit-btn {{ $this->isSpacerBusinessProfileContinueEnabled() ? 'btn-active' : 'btn-disabled' }}"
                wire:loading.attr="disabled" wire:target="submitSpacerBusinessProfile"
                @if (!$this->isSpacerBusinessProfileContinueEnabled()) disabled @endif>
                <span wire:loading.remove wire:target="submitSpacerBusinessProfile">Continue</span>
                <span wire:loading wire:target="submitSpacerBusinessProfile">Saving…</span>
            </button>
        </div>
    </form>
</div>

<style>
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
    }

    .spacer-services-header>div>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .spacer-services-header>span {
        margin-right: 1.7rem;
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
        background: #FFF;
    }

    .spacer-service-row {
        display: block;
        position: relative;
        margin: 0;
        padding: 18px 25px;
        border-bottom: 1px solid #E2E2E2;
        cursor: pointer;
    }

    .spacer-service-row:last-child {
        border-bottom: none;
    }

    .spacer-service-row-inner {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
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

    .spacer-select-row.sm {
        flex: 1;
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
        border-color: #F6C676;
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
        background: #F6C676;
    }

    .spacer-service-label {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .spacer-service-label:not(.spacer-service-label--with-icon) {
        color: #3B3731;
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

    .spacer-service-row.spacer-service-row--selected .spacer-service-label__icon {
        opacity: 1;
    }

    .spacer-service-row:not(.spacer-service-row--selected) .spacer-service-label__icon {
        opacity: 0.55;
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

    .spacer-service-label__icon svg {
        width: 26px;
        height: 22px;
        display: block;
    }

    .spacer-addon-custom-row,
    .spacer-addon-fursgo-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 0.85rem;
    }

    .spacer-addon-fursgo-row .spacer-select-row.sm:not(.is-selected) .spacer-service-label {
        color: #9D9B98;
    }

    .spacer-suitable-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 0.65rem;
    }

    .groomer-pill-suitable {
        position: relative;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        border-radius: 999px;
        border: 1px solid #E2E2E2;
        padding: 0.5rem 0.9rem;
    }

    .groomer-pill-suitable.is-active {
        background: #FFC97A;
        border-color: #FFC97A;
    }

    .groomer-pill-suitable input {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
    }

    /* Beat verify-qualify .groomer-pill-option > span { display: flex } so hidden tick takes no space */
    .groomer-pill-option.groomer-pill-suitable .groomer-pill-suitable__tick {
        display: none;
        flex-shrink: 0;
        line-height: 0;
    }

    .groomer-pill-option.groomer-pill-suitable .groomer-pill-suitable__tick svg {
        display: block;
    }

    .groomer-pill-option.groomer-pill-suitable.is-active .groomer-pill-suitable__tick {
        display: inline-flex;
    }

    /* Parent wizard sets .groomer-pill-option { transition: all 0.2s } — remove for snappier toggles */
    .business-basics-wrap .groomer-pill-option.groomer-pill-suitable {
        transition: none;
    }

    .business-basics-wrap .addon-checkbox-item {
        transition: none;
    }

    .spacer-custom-chips {
        list-style: none;
        margin: 0.5rem 0 0 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .spacer-custom-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.8rem;
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .spacer-custom-chip__marker {
        flex-shrink: 0;
        width: 1rem;
        height: 1rem;
        border-radius: 999px;
        border: 1px solid #F6C676;
        background: #FFF;
        position: relative;
    }

    .spacer-custom-chip__marker::after {
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
    }

    .services-header>span>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .service-price-control {
        width: 85px;
        height: 48px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        position: relative;
        flex-shrink: 0;
    }

    .service-price-currency {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
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
        position: absolute;
        left: 24px;
        top: 50%;
        transform: translateY(-50%);
        padding: 0;
        background: transparent;
    }

    .spacer-service-row:not(.spacer-service-row--selected) .service-price-currency {
        display: none;
    }

    .spacer-service-row:not(.spacer-service-row--selected) .service-price-input {
        left: 10px;
    }

    .spacer-addon-custom-row .spacer-select-row:not(.is-selected)~.service-price-control .service-price-currency,
    .spacer-addon-fursgo-row .spacer-select-row:not(.is-selected)~.service-price-control .service-price-currency {
        display: none;
    }

    .spacer-addon-custom-row .spacer-select-row:not(.is-selected)~.service-price-control .service-price-input,
    .spacer-addon-fursgo-row .spacer-select-row:not(.is-selected)~.service-price-control .service-price-input {
        left: 10px;
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
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
    }

    .service-stepper-btn {
        border: none;
        background: transparent;
        padding: 0;
        cursor: pointer;
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
        margin: 1rem 0 2rem;
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
        width: 1rem;
        height: 1rem;
        border-radius: 999px;
        transform: translate(-50%, -50%);
        background: #F6C676;
    }

    .addon-checkbox-item.is-selected>span {
        color: #000;
    }
</style>
