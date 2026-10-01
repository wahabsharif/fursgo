@props(['profile'])

@php
$providerName = $profile['name'] ?? 'Pawfect Grooming';
$providerEmail = $profile['email'] ?? 'sarah.w@pawfect.co.uk';
$providerAvatar = $profile['avatar'] ?? asset('images/profile_image.png');
$firstName = explode(' ', trim($providerName))[0] ?: $providerName;
@endphp

<div class="admin-co-support-detail" x-show="selectedTicket" x-cloak>
    <div class="admin-co-overview-head">
        <div class="admin-co-bk-detail-title-row">
            <button type="button" class="admin-co-bk-detail-back" @click="closeTicket()" aria-label="Back to support tickets">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                    <g filter="url(#filter0_d_provider_support_detail_back)">
                        <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
                    </g>
                    <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_provider_support_detail_back" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_provider_support_detail_back" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_provider_support_detail_back" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
            <h2 class="admin-page-title mb-0">
                Support <span x-text="selectedTicket?.ticket_id"></span>
            </h2>
        </div>
        <div class="admin-co-support-detail-head-actions">
            <button type="button" class="admin-co-ref-download-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="12" viewBox="0 0 13 12" fill="none">
                    <path d="M0.52228 3.70283C0.507241 4.10778 0.5 4.52721 0.5 4.9561C0.500081 5.37396 0.507509 5.79179 0.52228 6.20938C0.568512 7.53117 0.591907 8.19235 1.12942 8.73376C1.66721 9.27544 2.34633 9.30467 3.70398 9.36309L3.70616 9.36319L3.84207 9.36876V10.6766C3.842 10.7544 3.86425 10.8306 3.90617 10.8961C3.94809 10.9617 4.00793 11.0138 4.07859 11.0463C4.14924 11.0789 4.22775 11.0905 4.3048 11.0797C4.38185 11.069 4.4542 11.0364 4.51327 10.9858L5.727 9.94415C6.03225 9.68235 6.18487 9.55257 6.36645 9.48294C6.5465 9.41391 6.75119 9.41004 7.15645 9.40237L7.16688 9.40218C7.60246 9.39364 8.02486 9.38064 8.43408 9.36319L8.43646 9.36308C9.79399 9.30467 10.4736 9.27542 11.0108 8.73432C11.5483 8.19235 11.5717 7.53117 11.618 6.20938C11.6473 5.37412 11.6473 4.53809 11.618 3.70283C11.5717 2.38104 11.5483 1.71986 11.0108 1.17845C10.473 0.636766 9.79392 0.607541 8.43627 0.549118L8.43408 0.549024C7.68824 0.516717 6.8945 0.500007 6.07012 0.500007C5.28188 0.499617 4.49371 0.51596 3.70616 0.549024L3.70379 0.549126C2.34626 0.607544 1.66663 0.636791 1.12942 1.17789C0.591907 1.71986 0.568512 2.38104 0.52228 3.70283Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M6.14012 4.95609H6.06994M3.91096 4.95609H3.84189M8.36761 4.95609H8.29799M6.20919 4.95609C6.20919 4.99302 6.19452 5.02844 6.16841 5.05455C6.14229 5.08067 6.10687 5.09534 6.06994 5.09534C6.03301 5.09534 5.99759 5.08067 5.97147 5.05455C5.94536 5.02844 5.93069 4.99302 5.93069 4.95609C5.93069 4.91915 5.94536 4.88373 5.97147 4.85762C5.99759 4.8315 6.03301 4.81683 6.06994 4.81683C6.10687 4.81683 6.14229 4.8315 6.16841 4.85762C6.19452 4.88373 6.20919 4.91915 6.20919 4.95609ZM3.98114 4.95609C3.98114 4.99302 3.96647 5.02844 3.94036 5.05455C3.91424 5.08067 3.87882 5.09534 3.84189 5.09534C3.80496 5.09534 3.76954 5.08067 3.74342 5.05455C3.71731 5.02844 3.70264 4.99302 3.70264 4.95609C3.70264 4.91915 3.71731 4.88373 3.74342 4.85762C3.76954 4.8315 3.80496 4.81683 3.84189 4.81683C3.87882 4.81683 3.91424 4.8315 3.94036 4.85762C3.96647 4.88373 3.98114 4.91915 3.98114 4.95609ZM8.43724 4.95609C8.43724 4.99302 8.42257 5.02844 8.39645 5.05455C8.37034 5.08067 8.33492 5.09534 8.29799 5.09534C8.26106 5.09534 8.22564 5.08067 8.19952 5.05455C8.17341 5.02844 8.15873 4.99302 8.15873 4.95609C8.15873 4.91915 8.17341 4.88373 8.19952 4.85762C8.22564 4.8315 8.26106 4.81683 8.29799 4.81683C8.33492 4.81683 8.37034 4.8315 8.39645 4.85762C8.42257 4.88373 8.43724 4.91915 8.43724 4.95609Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Open conversation
            </button>
            <button type="button" class="admin-btn-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                    <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                </svg>
                Export Log
            </button>
        </div>
    </div>

    <template x-if="selectedTicket">
        <div class="admin-co-support-detail-body">
            <div class="admin-co-support-meta-row">
                <div class="admin-co-support-meta-main">
                    <div class="admin-co-support-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                            <path d="M6.425 0.375V1.51786M6.425 7.23214V8.375M6.425 3.80357V4.94643M0.375 2.66071C0.812607 2.66071 1.23229 2.84133 1.54173 3.16282C1.85116 3.48431 2.025 3.92034 2.025 4.375C2.025 4.82966 1.85116 5.26569 1.54173 5.58718C1.23229 5.90867 0.812607 6.08929 0.375 6.08929V7.23214C0.375 7.53525 0.490892 7.82594 0.697182 8.04027C0.903472 8.25459 1.18326 8.375 1.475 8.375H10.275C10.5667 8.375 10.8465 8.25459 11.0528 8.04027C11.2591 7.82594 11.375 7.53525 11.375 7.23214V6.08929C10.9374 6.08929 10.5177 5.90867 10.2083 5.58718C9.89884 5.26569 9.725 4.82966 9.725 4.375C9.725 3.92034 9.89884 3.48431 10.2083 3.16282C10.5177 2.84133 10.9374 2.66071 11.375 2.66071V1.51786C11.375 1.21475 11.2591 0.924062 11.0528 0.709735C10.8465 0.495408 10.5667 0.375 10.275 0.375H1.475C1.18326 0.375 0.903472 0.495408 0.697182 0.709735C0.490892 0.924062 0.375 1.21475 0.375 1.51786V2.66071Z" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span x-text="selectedTicket.ticket_id"></span>
                    </div>
                    <div class="admin-co-support-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M0.375 5.60001C0.375 3.52596 0.375 2.48866 1.0196 1.84461C1.6642 1.20056 2.70095 1.20001 4.775 1.20001H6.975C9.04905 1.20001 10.0863 1.20001 10.7304 1.84461C11.3744 2.48921 11.375 3.52596 11.375 5.60001V6.70001C11.375 8.77406 11.375 9.81136 10.7304 10.4554C10.0858 11.0995 9.04905 11.1 6.975 11.1H4.775C2.70095 11.1 1.66365 11.1 1.0196 10.4554C0.37555 9.81081 0.375 8.77406 0.375 6.70001V5.60001Z" stroke="#3B3731" stroke-width="0.75" />
                            <path d="M3.1249 1.2V0.375M8.6249 1.2V0.375M0.649902 3.95H11.0999" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" />
                            <path d="M9.17495 8.35001C9.17495 8.49588 9.11701 8.63577 9.01386 8.73892C8.91072 8.84206 8.77082 8.90001 8.62495 8.90001C8.47908 8.90001 8.33919 8.84206 8.23604 8.73892C8.1329 8.63577 8.07495 8.49588 8.07495 8.35001C8.07495 8.20414 8.1329 8.06424 8.23604 7.9611C8.33919 7.85795 8.47908 7.80001 8.62495 7.80001C8.77082 7.80001 8.91072 7.85795 9.01386 7.9611C9.11701 8.06424 9.17495 8.20414 9.17495 8.35001ZM9.17495 6.15001C9.17495 6.29588 9.11701 6.43577 9.01386 6.53891C8.91072 6.64206 8.77082 6.70001 8.62495 6.70001C8.47908 6.70001 8.33919 6.64206 8.23604 6.53891C8.1329 6.43577 8.07495 6.29588 8.07495 6.15001C8.07495 6.00414 8.1329 5.86424 8.23604 5.7611C8.33919 5.65795 8.47908 5.60001 8.62495 5.60001C8.77082 5.60001 8.91072 5.65795 9.01386 5.7611C9.11701 5.86424 9.17495 6.00414 9.17495 6.15001ZM6.42495 8.35001C6.42495 8.49588 6.367 8.63577 6.26386 8.73892C6.16071 8.84206 6.02082 8.90001 5.87495 8.90001C5.72908 8.90001 5.58919 8.84206 5.48604 8.73892C5.3829 8.63577 5.32495 8.49588 5.32495 8.35001C5.32495 8.20414 5.3829 8.06424 5.48604 7.9611C5.58919 7.85795 5.72908 7.80001 5.87495 7.80001C6.02082 7.80001 6.16071 7.85795 6.26386 7.9611C6.367 8.06424 6.42495 8.20414 6.42495 8.35001ZM6.42495 6.15001C6.42495 6.29588 6.367 6.43577 6.26386 6.53891C6.16071 6.64206 6.02082 6.70001 5.87495 6.70001C5.72908 6.70001 5.58919 6.64206 5.48604 6.53891C5.3829 6.43577 5.32495 6.29588 5.32495 6.15001C5.32495 6.00414 5.3829 5.86424 5.48604 5.7611C5.58919 5.65795 5.72908 5.60001 5.87495 5.60001C6.02082 5.60001 6.16071 5.65795 6.26386 5.7611C6.367 5.86424 6.42495 6.00414 6.42495 6.15001ZM3.67495 8.35001C3.67495 8.49588 3.617 8.63577 3.51386 8.73892C3.41071 8.84206 3.27082 8.90001 3.12495 8.90001C2.97908 8.90001 2.83919 8.84206 2.73604 8.73892C2.6329 8.63577 2.57495 8.49588 2.57495 8.35001C2.57495 8.20414 2.6329 8.06424 2.73604 7.9611C2.83919 7.85795 2.97908 7.80001 3.12495 7.80001C3.27082 7.80001 3.41071 7.85795 3.51386 7.9611C3.617 8.06424 3.67495 8.20414 3.67495 8.35001ZM3.67495 6.15001C3.67495 6.29588 3.617 6.43577 3.51386 6.53891C3.41071 6.64206 3.27082 6.70001 3.12495 6.70001C2.97908 6.70001 2.83919 6.64206 2.73604 6.53891C2.6329 6.43577 2.57495 6.29588 2.57495 6.15001C2.57495 6.00414 2.6329 5.86424 2.73604 5.7611C2.83919 5.65795 2.97908 5.60001 3.12495 5.60001C3.27082 5.60001 3.41071 5.65795 3.51386 5.7611C3.617 5.86424 3.67495 6.00414 3.67495 6.15001Z" fill="#3B3731" />
                        </svg>
                        <span>Opened <span x-text="selectedTicket.opened"></span></span>
                    </div>
                    <div class="admin-co-support-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none">
                            <path d="M5.53735 0.375C7.17271 0.375 8.49823 1.70065 8.49829 3.33594C8.49829 4.97127 7.17275 6.29688 5.53735 6.29688C3.90205 6.29677 2.57642 4.97121 2.57642 3.33594C2.57648 1.70072 3.90208 0.375105 5.53735 0.375Z" stroke="#3B3731" stroke-width="0.75" />
                            <path d="M9.17736 7.67597C8.03797 6.45519 6.51217 6.36887 5.53827 6.36887C1.17097 6.36887 0.281336 10.1093 0.382431 12.1311" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" />
                            <path d="M11.3751 9.44604L8.82755 12.2613C8.77201 12.3225 8.70723 12.3531 8.63318 12.3531C8.55914 12.3531 8.49435 12.3225 8.43882 12.2613L7.24487 10.9455" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" />
                        </svg>
                        <span>Assigned: <span x-text="selectedTicket.assigned"></span></span>
                    </div>
                    <div class="admin-co-support-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                            <path d="M6.60982 0.8329L10.6421 4.86517C10.8744 5.09752 11.0588 5.37274 11.1845 5.67632C11.3103 5.97991 11.375 6.3053 11.375 6.6339C11.375 6.9625 11.3103 7.28789 11.1845 7.59147C11.0588 7.89506 10.8744 8.1709 10.6421 8.40326L9.52236 9.52298L8.40263 10.6427C8.17028 10.8751 7.89443 11.0594 7.59085 11.1851C7.28726 11.3109 6.96188 11.3756 6.63327 11.3756C6.30467 11.3756 5.97929 11.3109 5.6757 11.1851C5.37211 11.0594 5.09627 10.8751 4.86392 10.6427L0.832274 6.61044C0.53924 6.317 0.374757 5.91918 0.375 5.50448V2.25164C0.375 1.75392 0.572717 1.27659 0.924655 0.924654C1.27659 0.572717 1.75392 0.375 2.25164 0.375H5.50448C5.91905 0.375089 6.31663 0.539789 6.60982 0.8329Z" stroke="black" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M3.43528 3.43576C3.74724 3.1238 3.74724 2.61802 3.43528 2.30606C3.12332 1.9941 2.61753 1.9941 2.30558 2.30606C1.99362 2.61802 1.99362 3.1238 2.30558 3.43576C2.61753 3.74772 3.12332 3.74772 3.43528 3.43576Z" fill="black" />
                        </svg>
                        <span>Category: <span x-text="selectedTicket.category"></span></span>
                    </div>
                </div>
                <span
                    class="admin-po-status"
                    :class="'is-' + (selectedTicket.status || 'open')"
                    x-text="selectedTicket.status_label"></span>
            </div>

            <div class="admin-co-support-detail-sep" aria-hidden="true"></div>

            {{-- Original submission --}}
            <section class="admin-card admin-co-panel admin-co-support-submission">
                <div class="admin-co-section-head admin-co-support-submission-head">
                    <h3 class="admin-co-section-title">Original submission request</h3>
                    <p class="admin-co-support-desc-meta" x-text="selectedTicket.detail?.submitted_meta"></p>
                </div>
                <div class="admin-co-support-submission-grid">
                    <div class="admin-co-support-submission-fields">
                        <div class="admin-co-support-field">
                            <p class="admin-co-support-field-label">Subject</p>
                            <p class="admin-co-support-field-value is-subject" x-text="selectedTicket.detail?.subject"></p>
                        </div>
                        <div class="admin-co-support-field">
                            <p class="admin-co-support-field-label">Category</p>
                            <p class="admin-co-support-field-value" x-text="selectedTicket.detail?.category"></p>
                        </div>
                        <div class="admin-co-support-field">
                            <p class="admin-co-support-field-label">Booking reference (optional)</p>
                            <p class="admin-co-support-field-value" x-text="selectedTicket.detail?.booking_ref"></p>
                        </div>
                        <div class="admin-co-support-field">
                            <p class="admin-co-support-field-label">Attachments</p>
                            <div class="admin-co-support-attachments" x-show="(selectedTicket.detail?.attachments || []).length" x-cloak>
                                <template x-for="(file, idx) in (selectedTicket.detail?.attachments || [])" :key="idx">
                                    <div class="admin-co-support-attachment">
                                        <div class="admin-co-support-attachment-main">
                                            <span class="admin-co-support-attachment-icon" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                                    <path d="M8.09088 10.3738C8.58429 10.3595 9.06068 10.1903 9.45265 9.89031C9.84462 9.59029 10.1323 9.17518 10.275 8.70265C10.3397 8.48735 10.375 8.25853 10.375 8.02206V2.72794C10.375 2.1039 10.1271 1.50542 9.68584 1.06416C9.24458 0.622898 8.6461 0.375 8.02206 0.375H2.72794C2.1039 0.375 1.50542 0.622898 1.06416 1.06416C0.622898 1.50542 0.375 2.1039 0.375 2.72794V8.06324C0.385797 8.68011 0.638454 9.26807 1.07855 9.70046C1.51865 10.1329 2.11097 10.3751 2.72794 10.375H8.02206L8.09088 10.3738ZM10.275 8.70265L10.2232 8.64147L8.77265 6.89088C8.66258 6.75809 8.52467 6.65112 8.36867 6.57756C8.21267 6.50399 8.0424 6.46562 7.86993 6.46517C7.69745 6.46473 7.52699 6.5022 7.37061 6.57496C7.21423 6.64771 7.07575 6.75396 6.965 6.88618L6.19324 7.80735L6.06735 7.96088M0.375588 8.06382L0.479706 7.94559L2.36559 5.69441C2.47601 5.56263 2.61398 5.45664 2.76978 5.38393C2.92558 5.31121 3.09542 5.27352 3.26735 5.27352C3.43929 5.27352 3.60913 5.31121 3.76493 5.38393C3.92073 5.45664 4.05869 5.56263 4.16912 5.69441L6.06735 7.96088L8.03618 10.3115L8.09088 10.3738" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                                    <path d="M7.1936 3.04938C7.4736 3.04963 7.70044 3.27714 7.70044 3.55719C7.70019 3.83703 7.47344 4.06378 7.1936 4.06403C6.91355 4.06403 6.68604 3.83718 6.68579 3.55719C6.68579 3.27699 6.9134 3.04938 7.1936 3.04938Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.75" />
                                                </svg>
                                            </span>
                                            <p class="admin-co-support-attachment-name" x-text="file.name"></p>
                                        </div>
                                        <div class="admin-co-support-attachment-meta">
                                            <span class="admin-co-support-attachment-size" x-text="file.size"></span>
                                            <button type="button" class="admin-co-support-attachment-dl" aria-label="Download attachment">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                                    <path d="M5.26586 8.01036C5.19462 7.9826 5.12469 7.93441 5.05607 7.86579L2.9975 5.808C2.92102 5.73153 2.88121 5.64143 2.87807 5.53772C2.87493 5.434 2.91474 5.33893 2.9975 5.2525C3.08445 5.16555 3.17795 5.12129 3.278 5.11972C3.37857 5.11815 3.47207 5.16084 3.5585 5.24779L5.10714 6.79643V0.392862C5.10714 0.281291 5.14459 0.187791 5.2195 0.112363C5.2944 0.0369341 5.3879 -0.000518391 5.5 5.4187e-06C5.6121 0.000529228 5.7056 0.0379817 5.7805 0.112363C5.8554 0.186744 5.89286 0.280243 5.89286 0.392862V6.79643L7.4415 5.24779C7.51798 5.17131 7.60886 5.13124 7.71414 5.12757C7.81943 5.12391 7.91529 5.16555 8.00171 5.2525C8.08552 5.33893 8.12821 5.43164 8.12979 5.53064C8.13136 5.62964 8.08893 5.7221 8.0025 5.808L5.94393 7.86657C5.87583 7.93467 5.8059 7.9826 5.73414 8.01036C5.6629 8.03812 5.58486 8.052 5.5 8.052C5.41514 8.052 5.33709 8.03812 5.26586 8.01036ZM1.26971 11C0.907762 11 0.605786 10.879 0.363786 10.637C0.121786 10.395 0.00052381 10.0928 0 9.73029V8.21936C0 8.10779 0.0374525 8.01455 0.112357 7.93964C0.187262 7.86474 0.280762 7.82703 0.392857 7.8265C0.504952 7.82598 0.598452 7.86369 0.673357 7.93964C0.748262 8.0156 0.785714 8.10884 0.785714 8.21936V9.73029C0.785714 9.85129 0.836 9.96233 0.936571 10.0634C1.03714 10.1645 1.14793 10.2148 1.26893 10.2143H9.73107C9.85155 10.2143 9.96233 10.164 10.0634 10.0634C10.1645 9.96286 10.2148 9.85181 10.2143 9.73029V8.21936C10.2143 8.10779 10.2517 8.01455 10.3266 7.93964C10.4015 7.86474 10.495 7.82703 10.6071 7.8265C10.7192 7.82598 10.8127 7.86369 10.8876 7.93964C10.9625 8.0156 11 8.10884 11 8.21936V9.73029C11 10.0922 10.879 10.3942 10.637 10.6362C10.395 10.8782 10.0928 10.9995 9.73029 11H1.26971Z" fill="#9C9A97" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </div>
                            <div
                                class="admin-co-support-attachments-empty"
                                x-show="!(selectedTicket.detail?.attachments || []).length"
                                x-cloak>
                                <div class="attachment-svg-background">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                        <path d="M8.09088 10.3738C8.58429 10.3595 9.06068 10.1903 9.45265 9.89031C9.84462 9.59029 10.1323 9.17518 10.275 8.70265C10.3397 8.48735 10.375 8.25853 10.375 8.02206V2.72794C10.375 2.1039 10.1271 1.50542 9.68584 1.06416C9.24458 0.622898 8.6461 0.375 8.02206 0.375H2.72794C2.1039 0.375 1.50542 0.622898 1.06416 1.06416C0.622898 1.50542 0.375 2.1039 0.375 2.72794V8.06324C0.385797 8.68011 0.638454 9.26807 1.07855 9.70046C1.51865 10.1329 2.11097 10.3751 2.72794 10.375H8.02206L8.09088 10.3738ZM10.275 8.70265L10.2232 8.64147L8.77265 6.89088C8.66258 6.75809 8.52467 6.65112 8.36867 6.57756C8.21267 6.50399 8.0424 6.46562 7.86993 6.46517C7.69745 6.46473 7.52699 6.5022 7.37061 6.57496C7.21423 6.64771 7.07575 6.75396 6.965 6.88618L6.19324 7.80735L6.06735 7.96088M0.375588 8.06382L0.479706 7.94559L2.36559 5.69441C2.47601 5.56263 2.61398 5.45664 2.76978 5.38393C2.92558 5.31121 3.09542 5.27352 3.26735 5.27352C3.43929 5.27352 3.60913 5.31121 3.76493 5.38393C3.92073 5.45664 4.05869 5.56263 4.16912 5.69441L6.06735 7.96088L8.03618 10.3115L8.09088 10.3738" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7.1936 3.04938C7.4736 3.04963 7.70044 3.27714 7.70044 3.55719C7.70019 3.83703 7.47344 4.06378 7.1936 4.06403C6.91355 4.06403 6.68604 3.83718 6.68579 3.55719C6.68579 3.27699 6.9134 3.04938 7.1936 3.04938Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.75" />
                                    </svg>
                                </div>
                                <span>— No attachments provided.</span>
                            </div>
                        </div>
                    </div>
                    <div class="admin-co-support-submission-desc">
                        <p class="admin-co-support-field-label">Description</p>
                        <div class="admin-co-support-desc-box">
                            <p class="admin-co-support-desc-text" x-text="selectedTicket.detail?.description"></p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Conversation thread --}}
            <section class="admin-card admin-co-panel admin-co-support-thread is-preview">
                <div class="admin-co-support-thread-head">
                    <p class="admin-co-support-thread-title">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="11" viewBox="0 0 12 11" fill="none" aria-hidden="true">
                            <path d="M0.521052 3.52621C0.506842 3.90883 0.5 4.30513 0.5 4.71038C0.500077 5.1052 0.507095 5.49998 0.521052 5.89455C0.564734 7.14345 0.586839 7.76816 1.09472 8.27972C1.60285 8.79154 2.24451 8.81915 3.52731 8.87435L3.52936 8.87444L3.65778 8.8797V10.1154C3.65771 10.1889 3.67873 10.2609 3.71834 10.3228C3.75795 10.3848 3.81449 10.434 3.88125 10.4648C3.94801 10.4955 4.02219 10.5065 4.09499 10.4963C4.16779 10.4862 4.23615 10.4554 4.29197 10.4075L5.43877 9.42337C5.72718 9.17601 5.87138 9.05338 6.04296 8.98759C6.21307 8.92236 6.40648 8.91871 6.78938 8.91147L6.79925 8.91128C7.21081 8.90321 7.60992 8.89093 7.99657 8.87444L7.99881 8.87434C9.28148 8.81915 9.92364 8.79151 10.4312 8.28025C10.9391 7.76817 10.9612 7.14345 11.0049 5.89455C11.0326 5.10535 11.0326 4.31541 11.0049 3.52621C10.9612 2.27731 10.9391 1.6526 10.4312 1.14104C9.92309 0.629224 9.28142 0.601611 7.99864 0.54641L7.99657 0.546321C7.29186 0.515795 6.54189 0.500006 5.76297 0.500006C5.01819 0.499639 4.27349 0.51508 3.52936 0.546321L3.52712 0.546417C2.24445 0.601614 1.6023 0.629247 1.09472 1.14051C0.586839 1.6526 0.564734 2.27731 0.521052 3.52621Z" stroke="#FFAF3B" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M5.8292 4.71043H5.76288M3.72296 4.71043H3.6577M7.93386 4.71043H7.86807M5.89446 4.71043C5.89446 4.74533 5.8806 4.77879 5.85592 4.80347C5.83125 4.82814 5.79778 4.84201 5.76288 4.84201C5.72799 4.84201 5.69452 4.82814 5.66985 4.80347C5.64517 4.77879 5.63131 4.74533 5.63131 4.71043C5.63131 4.67554 5.64517 4.64207 5.66985 4.61739C5.69452 4.59272 5.72799 4.57886 5.76288 4.57886C5.79778 4.57886 5.83125 4.59272 5.85592 4.61739C5.8806 4.64207 5.89446 4.67554 5.89446 4.71043ZM3.78927 4.71043C3.78927 4.74533 3.77541 4.77879 3.75073 4.80347C3.72606 4.82814 3.69259 4.84201 3.6577 4.84201C3.6228 4.84201 3.58934 4.82814 3.56466 4.80347C3.53999 4.77879 3.52612 4.74533 3.52612 4.71043C3.52612 4.67554 3.53999 4.64207 3.56466 4.61739C3.58934 4.59272 3.6228 4.57886 3.6577 4.57886C3.69259 4.57886 3.72606 4.59272 3.75073 4.61739C3.77541 4.64207 3.78927 4.67554 3.78927 4.71043ZM7.99965 4.71043C7.99965 4.74533 7.98578 4.77879 7.96111 4.80347C7.93643 4.82814 7.90297 4.84201 7.86807 4.84201C7.83318 4.84201 7.79971 4.82814 7.77503 4.80347C7.75036 4.77879 7.7365 4.74533 7.7365 4.71043C7.7365 4.67554 7.75036 4.64207 7.77503 4.61739C7.79971 4.59272 7.83318 4.57886 7.86807 4.57886C7.90297 4.57886 7.93643 4.59272 7.96111 4.61739C7.98578 4.64207 7.99965 4.67554 7.99965 4.71043Z" stroke="#FFAF3B" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Support conversation thread · <span x-text="selectedTicket.detail?.thread?.count || 0"></span> messages
                    </p>
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                            <path d="M10 1L4 7M10 1H6M10 1V5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Open full chat
                    </button>
                </div>

                <div class="admin-co-support-thread-feed">
                    <template x-for="(msg, mIdx) in (selectedTicket.detail?.thread?.messages || [])" :key="mIdx">
                        <div class="admin-co-support-msg" :class="'is-' + msg.side">
                            <template x-if="msg.side === 'customer'">
                                <img class="admin-co-support-msg-avatar" :src="msg.avatar || '{{ $providerAvatar }}'" alt="" width="32" height="32">
                            </template>
                            <div class="admin-co-support-msg-body">
                                <p class="admin-co-support-msg-meta" x-text="msg.author + ' · ' + msg.meta"></p>
                                <div class="admin-co-support-msg-bubble">
                                    <p x-text="msg.text"></p>
                                </div>
                                <p class="admin-co-support-msg-time" x-text="msg.time"></p>
                            </div>
                            <template x-if="msg.side === 'admin'">
                                <span class="admin-co-support-msg-initials" x-text="msg.initials || 'AD'"></span>
                            </template>
                        </div>
                    </template>
                </div>

                <div class="admin-co-support-thread-status">
                    <span x-text="selectedTicket.detail?.thread?.status_line"></span>
                </div>

                <div class="admin-co-support-reply">
                    <div class="admin-co-support-reply-toolbar" aria-hidden="true">
                        <button type="button"><strong>B</strong></button>
                        <button type="button"><em>I</em></button>
                        <button type="button" aria-label="Insert link">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M3.62566 7.37611L7.37571 3.62561M4.87567 1.75036L5.16505 1.41531C5.75119 0.829189 6.54613 0.499941 7.37499 0.5C8.20385 0.500059 8.99874 0.829419 9.5848 1.41563C10.1709 2.00183 10.5001 2.79687 10.5 3.62583C10.4999 4.45479 10.1706 5.24978 9.58448 5.8359L9.25073 6.12594M6.12569 9.25136L5.87756 9.58515C5.28443 10.1713 4.48419 10.5 3.65035 10.5C2.8165 10.5 2.01626 10.1713 1.42313 9.58515C1.1307 9.2962 0.89852 8.95207 0.740058 8.57271C0.581596 8.19335 0.5 7.7863 0.5 7.37517C0.5 6.96403 0.581596 6.55699 0.740058 6.17763C0.89852 5.79827 1.1307 5.45414 1.42313 5.16519L1.75063 4.87577" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <button type="button" aria-label="Attach file">
                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none">
                                <path d="M8.5473 6.6507L6.19027 9.00772C5.56515 9.63284 4.71731 9.98403 3.83325 9.98403C2.9492 9.98403 2.10135 9.63284 1.47623 9.00772C0.851108 8.3826 0.499918 7.53476 0.499918 6.6507C0.499918 5.76665 0.851108 4.9188 1.47623 4.29368L4.61893 1.15098C5.03567 0.734234 5.6009 0.500108 6.19027 0.500108C6.77964 0.500108 7.34488 0.734234 7.76162 1.15098C8.17837 1.56773 8.4125 2.13296 8.4125 2.72233C8.4125 3.3117 8.17837 3.87693 7.76162 4.29368L4.61893 7.43637C4.41055 7.64475 4.12794 7.76181 3.83325 7.76181C3.53857 7.76181 3.25595 7.64475 3.04758 7.43637C2.8392 7.228 2.72214 6.94539 2.72214 6.6507C2.72214 6.35602 2.8392 6.0734 3.04758 5.86503L6.19027 2.72233" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    <div class="admin-co-support-reply-body">
                        <textarea
                            class="admin-co-support-reply-input"
                            rows="4"
                            x-model="replyText"
                            :placeholder="'Write your reply to {{ $providerName }} - they will receive this by email at ' + (selectedTicket.detail?.reply_to || '{{ $providerEmail }}') + ' ...'"></textarea>
                    </div>
                    <div class="admin-co-support-reply-foot">
                        <p class="admin-co-support-reply-hint">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                                <rect x="1" y="2.5" width="10" height="7" rx="1.2" stroke="#9C9A97" stroke-width="1" />
                                <path d="M1.5 3.5L6 6.5L10.5 3.5" stroke="#9C9A97" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Sends to <span x-text="selectedTicket.detail?.reply_to"></span>
                        </p>
                        <div class="admin-co-support-reply-actions">
                            <button type="button" class="admin-co-form-btn is-cancel" @click="replyText = ''">Cancel</button>
                            <button type="button" class="admin-co-support-send-btn">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                                    <path d="M4.95617 6.04323C4.86063 5.94785 4.74676 5.87281 4.62143 5.82264L0.656884 4.23282C0.609548 4.21383 0.569155 4.18081 0.541127 4.13819C0.513098 4.09558 0.498776 4.04541 0.500082 3.99443C0.501388 3.94344 0.51826 3.89407 0.548433 3.85295C0.578606 3.81183 0.620637 3.78092 0.668883 3.76437L10.1678 0.514747C10.2121 0.498747 10.26 0.495693 10.306 0.505944C10.352 0.516195 10.3941 0.539325 10.4274 0.572629C10.4607 0.605933 10.4838 0.648034 10.4941 0.694004C10.5043 0.739974 10.5013 0.787912 10.4853 0.832211L7.23563 10.3311C7.21908 10.3794 7.18817 10.4214 7.14705 10.4516C7.10593 10.4817 7.05656 10.4986 7.00557 10.4999C6.95459 10.5012 6.90442 10.4869 6.86181 10.4589C6.81919 10.4308 6.78617 10.3905 6.76718 10.3431L5.17736 6.37757C5.12696 6.25233 5.05172 6.1386 4.95617 6.04323ZM4.95617 6.04323L10.4258 0.57474" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Send reply
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Linked records --}}
            {{-- Linked records --}}
            <section class="admin-card admin-co-panel admin-co-support-linked">
                <div class="admin-co-section-head">
                    <h3 class="admin-co-section-title">
                        <span x-show="(selectedTicket.detail?.linked_state || 'na') === 'populated'" x-cloak>
                            Linked records · <span x-text="selectedTicket.detail?.linked_count || 3"></span> linked
                        </span>
                        <span x-show="(selectedTicket.detail?.linked_state || 'na') !== 'populated'" x-cloak>
                            Linked records
                        </span>
                    </h3>
                    <button
                        type="button"
                        class="admin-co-link-btn"
                        x-show="(selectedTicket.detail?.linked_state || 'na') !== 'na'"
                        x-cloak>
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                            <path d="M3.62566 7.37611L7.37571 3.62561M4.87567 1.75036L5.16505 1.41531C5.75119 0.829189 6.54613 0.499941 7.37499 0.5C8.20385 0.500059 8.99874 0.829419 9.5848 1.41563C10.1709 2.00183 10.5001 2.79687 10.5 3.62583C10.4999 4.45479 10.1706 5.24978 9.58448 5.8359L9.25073 6.12594M6.12569 9.25136L5.87756 9.58515C5.28443 10.1713 4.48419 10.5 3.65035 10.5C2.8165 10.5 2.01626 10.1713 1.42313 9.58515C1.1307 9.2962 0.89852 8.95207 0.740058 8.57271C0.581596 8.19335 0.5 7.7863 0.5 7.37517C0.5 6.96403 0.581596 6.55699 0.740058 6.17763C0.89852 5.79827 1.1307 5.45414 1.42313 5.16519L1.75063 4.87577" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Add link
                    </button>
                </div>

                {{-- Populated --}}
                <div x-show="(selectedTicket.detail?.linked_state || 'na') === 'populated'" x-cloak>
                    <div class="admin-co-support-linked-block">
                        <p class="admin-co-support-field-label">Booking</p>
                        <div class="admin-co-support-linked-card is-booking">
                            <span class="admin-co-support-linked-icon is-booking" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="20" fill="#FFC97A" />
                                    <path d="M17.6368 22.3634C18.8417 23.5683 21.7721 22.5917 24.1819 20.1816C26.5921 17.7718 27.5686 14.8413 26.3637 13.6365M21.182 12.818L21.7274 13.3638M19.2733 14.7272L19.8186 15.2725M17.6364 16.909L18.1818 17.4544M17.0911 19.6362L17.6364 20.1816M24.1819 12L24.7273 12.5454M23.6365 15.2729L24.7273 16.3637M21.7278 17.1821L22.8185 18.2728M19.546 18.8182L20.6367 19.9089" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.6363 24.0004C18.0881 23.5485 18.0881 22.8159 17.6363 22.3641C17.1844 21.9122 16.4518 21.9122 16 22.3641L13.8182 24.5458C13.3663 24.9977 13.3663 25.7303 13.8182 26.1822C14.27 26.634 15.0026 26.634 15.4545 26.1822L17.6363 24.0004Z" fill="#FFC97A" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div class="admin-co-support-linked-copy">
                                <p class="admin-co-support-linked-title" x-text="selectedTicket.detail?.linked?.booking?.title"></p>
                                <p class="admin-co-support-linked-meta" x-text="selectedTicket.detail?.linked?.booking?.meta"></p>
                            </div>
                            <button type="button" class="admin-co-support-linked-action is-booking">View booking →</button>
                        </div>
                    </div>

                    <div class="admin-co-support-linked-block">
                        <p class="admin-co-support-field-label">Payment</p>
                        <div class="admin-co-support-linked-card is-payment">
                            <span class="admin-co-support-linked-icon is-payment" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="20" fill="#F3F2F2" />
                                    <path d="M26.3333 27H16.3333C15.4493 27 14.6014 26.6488 13.9763 26.0237C13.3512 25.3986 13 24.5507 13 23.6667V13.6667C13 13.2246 13.1756 12.8007 13.4882 12.4882C13.8007 12.1756 14.2246 12 14.6667 12H23C23.442 12 23.866 12.1756 24.1785 12.4882C24.4911 12.8007 24.6667 13.2246 24.6667 13.6667V24.5C24.6667 25.8808 24.9525 27 26.3333 27Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M28.0001 17.8334C28.0001 17.3914 27.8245 16.9675 27.5119 16.6549C27.1994 16.3423 26.7754 16.1667 26.3334 16.1667H24.6667V24.9167C24.6667 26.0667 25.1834 27.0001 26.3334 27.0001C27.4834 27.0001 28.0001 26.0667 28.0001 24.9167V17.8334Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M21.3333 18.6666H16.3333M21.3333 15.3333H16.3333M18.8333 21.9999H16.3333" stroke="#3B3731" stroke-linecap="round" />
                                </svg>
                            </span>
                            <div class="admin-co-support-linked-copy">
                                <p class="admin-co-support-linked-title" x-text="selectedTicket.detail?.linked?.payment?.title"></p>
                                <p class="admin-co-support-linked-meta" x-text="selectedTicket.detail?.linked?.payment?.meta"></p>
                            </div>
                            <button type="button" class="admin-co-support-linked-action">View payment →</button>
                        </div>
                    </div>

                    <div class="admin-co-support-linked-block">
                        <p class="admin-co-support-field-label">Related chat thread</p>
                        <div class="admin-co-support-linked-card is-chat">
                            <div class="admin-co-support-chat-top">
                                <img class="admin-co-support-chat-avatar" :src="selectedTicket.detail?.linked?.chat?.avatar || '{{ $providerAvatar }}'" alt="" width="36" height="36">
                                <div class="admin-co-support-chat-identity">
                                    <p class="admin-co-support-linked-title" x-text="selectedTicket.detail?.linked?.chat?.business"></p>
                                    <p class="admin-co-support-linked-meta" x-text="selectedTicket.detail?.linked?.chat?.person"></p>
                                </div>
                                <div class="admin-co-support-chat-tags">
                                    <template x-for="(tag, tIdx) in (selectedTicket.detail?.linked?.chat?.tags || [])" :key="tIdx">
                                        <span class="admin-co-support-chat-tag" x-text="tag"></span>
                                    </template>
                                </div>
                                <button type="button" class="admin-co-support-linked-action">View full chat →</button>
                            </div>
                            <div class="admin-co-support-chat-preview">
                                <p class="admin-co-support-chat-preview-text">
                                    <span class="admin-co-support-chat-preview-name" x-text="selectedTicket.detail?.linked?.chat?.preview_name || ''"></span><span x-show="selectedTicket.detail?.linked?.chat?.preview_name" x-cloak>: </span><span class="admin-co-support-chat-preview-body" x-text="selectedTicket.detail?.linked?.chat?.preview_message || selectedTicket.detail?.linked?.chat?.preview || ''"></span>
                                </p>
                                <span class="admin-co-support-chat-preview-time" x-text="selectedTicket.detail?.linked?.chat?.preview_time"></span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Empty --}}
                <div
                    class="admin-co-support-linked-empty-state"
                    x-show="(selectedTicket.detail?.linked_state || 'na') === 'empty'"
                    x-cloak>
                    <span class="admin-co-support-linked-empty-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 11 11" fill="none">
                            <path d="M3.62566 7.37611L7.37571 3.62561M4.87567 1.75036L5.16505 1.41531C5.75119 0.829189 6.54613 0.499941 7.37499 0.5C8.20385 0.500059 8.99874 0.829419 9.5848 1.41563C10.1709 2.00183 10.5001 2.79687 10.5 3.62583C10.4999 4.45479 10.1706 5.24978 9.58448 5.8359L9.25073 6.12594M6.12569 9.25136L5.87756 9.58515C5.28443 10.1713 4.48419 10.5 3.65035 10.5C2.8165 10.5 2.01626 10.1713 1.42313 9.58515C1.1307 9.2962 0.89852 8.95207 0.740058 8.57271C0.581596 8.19335 0.5 7.7863 0.5 7.37517C0.5 6.96403 0.581596 6.55699 0.740058 6.17763C0.89852 5.79827 1.1307 5.45414 1.42313 5.16519L1.75063 4.87577" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <p class="admin-co-support-linked-empty-title">No linked records</p>
                    <p class="admin-co-support-linked-empty-copy">This ticket hasn't been linked to a booking, payment or chat thread. Add a link if you find a related record.</p>
                </div>

                {{-- Not applicable --}}
                <p
                    class="admin-co-support-linked-empty"
                    x-show="(selectedTicket.detail?.linked_state || 'na') === 'na'"
                    x-cloak>— Not applicable for this ticket type — no booking or payment to link.</p>

                {{-- Suggested --}}
                <div x-show="(selectedTicket.detail?.linked_state || 'na') === 'suggested'" x-cloak>
                    <div class="admin-co-support-linked-suggest-banner">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                            <path d="M6.58794 7.02685L6.11543 8.67956C5.93838 9.2982 5.06162 9.2982 4.88457 8.67956L4.41259 7.02685C4.38271 6.9223 4.32668 6.82709 4.24979 6.75021C4.17291 6.67332 4.0777 6.61729 3.97315 6.58741L2.32044 6.11543C1.7018 5.93838 1.7018 5.06162 2.32044 4.88457L3.97315 4.41259C4.0777 4.38271 4.17291 4.32668 4.24979 4.24979C4.32668 4.17291 4.38271 4.0777 4.41259 3.97315L4.88457 2.32044C5.06162 1.7018 5.93838 1.7018 6.11543 2.32044L6.58741 3.97315C6.61729 4.0777 6.67332 4.17291 6.75021 4.24979C6.82709 4.32668 6.9223 4.38271 7.02685 4.41259L8.67956 4.88457C9.2982 5.06162 9.2982 5.93838 8.67956 6.11543L7.02685 6.58741C6.9223 6.61729 6.82709 6.67332 6.75021 6.75021C6.67332 6.82709 6.61729 6.9223 6.58741 7.02685M9.53712 9.61498L9.3366 10.4192C9.30993 10.5269 9.15687 10.5269 9.12967 10.4192L8.92862 9.61498C8.92391 9.59631 8.91423 9.57927 8.90062 9.56565C8.887 9.55204 8.86996 9.54236 8.85129 9.53765L8.04706 9.3366C7.93934 9.30993 7.93934 9.15687 8.04706 9.12967L8.85129 8.92862C8.86996 8.92391 8.887 8.91423 8.90062 8.90062C8.91423 8.887 8.92391 8.86996 8.92862 8.85129L9.12967 8.04706C9.15634 7.93934 9.3094 7.93934 9.3366 8.04706L9.53765 8.85129C9.54236 8.86996 9.55204 8.887 9.56565 8.90062C9.57927 8.91423 9.59631 8.92391 9.61498 8.92862L10.4192 9.12967C10.5269 9.15634 10.5269 9.3094 10.4192 9.3366L9.61498 9.53765C9.59631 9.54236 9.57927 9.55204 9.56565 9.56565C9.55204 9.57927 9.54183 9.59631 9.53712 9.61498ZM2.07085 2.14871L1.87033 2.95294C1.84366 3.06066 1.69007 3.06066 1.6634 2.95294L1.46235 2.14871C1.45764 2.13004 1.44796 2.113 1.43435 2.09938C1.42073 2.08577 1.40369 2.07609 1.38502 2.07138L0.580796 1.87033C0.473068 1.84366 0.473068 1.69007 0.580796 1.6634L1.38502 1.46235C1.40369 1.45764 1.42073 1.44796 1.43435 1.43435C1.44796 1.42073 1.45764 1.40369 1.46235 1.38502L1.6634 0.580796C1.69007 0.473068 1.84366 0.473068 1.87033 0.580796L2.07138 1.38502C2.07609 1.40369 2.08577 1.42073 2.09938 1.43435C2.113 1.44796 2.13004 1.45764 2.14871 1.46235L2.95294 1.6634C3.06066 1.69007 3.06066 1.84366 2.95294 1.87033L2.14871 2.07138C2.13004 2.07609 2.113 2.08577 2.09938 2.09938C2.08577 2.113 2.07556 2.13004 2.07085 2.14871Z" stroke="#FFB13F" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <span>A possible related record was detected from the ticket content. Confirm or dismiss below.</span>
                    </div>
                    <div class="admin-co-support-linked-block">
                        <p class="admin-co-support-field-label">Booking</p>
                        <div class="admin-co-support-linked-card is-suggested">
                            <span class="admin-co-support-linked-icon is-booking" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="20" fill="#FFC97A" />
                                    <path d="M17.6368 22.3634C18.8417 23.5683 21.7721 22.5917 24.1819 20.1816C26.5921 17.7718 27.5686 14.8413 26.3637 13.6365M21.182 12.818L21.7274 13.3638M19.2733 14.7272L19.8186 15.2725M17.6364 16.909L18.1818 17.4544M17.0911 19.6362L17.6364 20.1816M24.1819 12L24.7273 12.5454M23.6365 15.2729L24.7273 16.3637M21.7278 17.1821L22.8185 18.2728M19.546 18.8182L20.6367 19.9089" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M17.6363 24.0004C18.0881 23.5485 18.0881 22.8159 17.6363 22.3641C17.1844 21.9122 16.4518 21.9122 16 22.3641L13.8182 24.5458C13.3663 24.9977 13.3663 25.7303 13.8182 26.1822C14.27 26.634 15.0026 26.634 15.4545 26.1822L17.6363 24.0004Z" fill="#FFC97A" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div class="admin-co-support-linked-copy">
                                <p class="admin-co-support-linked-title" x-text="selectedTicket.detail?.linked?.booking?.title"></p>
                                <p class="admin-co-support-linked-meta">
                                    <span x-text="selectedTicket.detail?.linked?.booking?.meta"></span>
                                    <span class="admin-co-support-linked-suggest-label"> · Suggested match</span>
                                </p>
                            </div>
                            <div class="admin-co-support-linked-suggest-actions">
                                <button
                                    type="button"
                                    class="admin-co-support-linked-suggest-btn is-confirm"
                                    @click="selectedTicket.detail.linked_state = 'populated'; selectedTicket.detail.linked_count = 3">
                                    Confirm
                                </button>
                                <button
                                    type="button"
                                    class="admin-co-support-linked-suggest-btn is-dismiss"
                                    @click="selectedTicket.detail.linked_state = 'empty'">
                                    Dismiss
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Admin notes --}}
            <section
                class="admin-card admin-co-panel"
                :class="{ 'is-editing': editingNotes }"
                x-data="{
                    editingNotes: false,
                    newNote: '',
                    editIndex: -1,
                    notes: [...(selectedTicket?.detail?.notes || [])],
                }">
                <div x-show="!editingNotes">
                    <x-admin.customer.section-header title="Admin notes">
                        <button type="button" class="admin-co-link-btn" @click="editingNotes = true; newNote = ''; editIndex = -1">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                                <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Add Notes
                        </button>
                    </x-admin.customer.section-header>
                    <div class="admin-co-notes">
                        <template x-for="(note, index) in notes" :key="index">
                            <article class="admin-co-note">
                                <p class="admin-co-note-text" x-text="note.text"></p>
                                <div class="admin-co-note-foot">
                                    <span x-text="note.author"></span>
                                    <span class="admin-co-note-dot">·</span>
                                    <span x-text="note.time"></span>
                                    <span class="admin-co-note-dot" x-show="note.tag" x-cloak>·</span>
                                    <span class="admin-co-note-tag" x-show="note.tag" x-cloak x-text="note.tag"></span>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>

                <div x-show="editingNotes" x-cloak>
                    <div class="admin-co-section-head admin-co-notes-edit-head">
                        <h3 class="admin-co-section-title">Admin notes</h3>
                        <div class="admin-co-section-action-wrap">
                            <button type="button" class="admin-co-form-btn is-cancel" @click="editingNotes = false; newNote = ''; editIndex = -1">Cancel</button>
                            <button type="button" class="admin-co-form-btn is-save" @click="editingNotes = false; newNote = ''; editIndex = -1">Save changes</button>
                        </div>
                    </div>

                    <div class="admin-co-note-compose">
                        <label class="admin-co-note-compose-label" for="bp-support-note-{{ $profile['id'] ?? 'detail' }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                        <textarea
                            id="bp-support-note-{{ $profile['id'] ?? 'detail' }}"
                            class="admin-co-note-textarea"
                            rows="4"
                            placeholder="Write an internal note about this ticket - only visible to the admin team members."
                            x-model="newNote"
                            x-ref="noteInput"></textarea>
                        <div class="admin-co-note-compose-actions">
                            <button
                                type="button"
                                class="admin-co-form-btn is-add-note"
                                @click="
                                    newNote.trim() && (
                                        editIndex >= 0
                                            ? (notes[editIndex].text = newNote.trim(), editIndex = -1, newNote = '')
                                            : (notes.unshift({ text: newNote.trim(), author: 'Admin', time: 'Just now', tag: 'Internal only' }), newNote = '')
                                    )
                                "
                                x-text="editIndex >= 0 ? 'Update note' : 'Add note'"></button>
                        </div>
                    </div>

                    <div class="admin-co-notes admin-co-notes-edit">
                        <template x-for="(note, index) in notes" :key="index">
                            <article class="admin-co-note" :class="{ 'is-editing-note': editIndex === index }">
                                <p class="admin-co-note-text" x-text="note.text"></p>
                                <div class="admin-co-note-foot">
                                    <span x-text="note.author"></span>
                                    <span class="admin-co-note-dot">·</span>
                                    <span x-text="note.time"></span>
                                    <span class="admin-co-note-dot" x-show="note.tag" x-cloak>·</span>
                                    <span class="admin-co-note-tag" x-show="note.tag" x-cloak x-text="note.tag"></span>
                                </div>
                                <div class="admin-co-note-actions">
                                    <button type="button" class="admin-co-note-action" @click="newNote = note.text; editIndex = index; $refs.noteInput.focus()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        Edit
                                    </button>
                                    <button
                                        type="button"
                                        class="admin-co-note-action"
                                        @click="
                                            notes.splice(index, 1);
                                            editIndex === index && (editIndex = -1, newNote = '');
                                            editIndex > index && (editIndex = editIndex - 1);
                                        ">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                            <path d="M1.75 3.5H12.25" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                            <path d="M5.25 3.5V2.45C5.25 1.89772 5.69772 1.45 6.25 1.45H7.75C8.30228 1.45 8.75 1.89772 8.75 2.45V3.5" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                            <path d="M11.0833 3.5V11.55C11.0833 12.1023 10.6356 12.55 10.0833 12.55H3.91667C3.36438 12.55 2.91667 12.1023 2.91667 11.55V3.5" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                            <path d="M5.83301 6.125V9.625" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                            <path d="M8.16699 6.125V9.625" stroke="#3B3731" stroke-width="1.3" stroke-linecap="round" />
                                        </svg>
                                        Delete
                                    </button>
                                </div>
                            </article>
                        </template>
                    </div>
                </div>
            </section>

            {{-- Details + activity --}}
            <div class="admin-co-support-bottom-split">
                <section class="admin-card admin-co-panel">
                    <h3 class="admin-co-section-title">Support details</h3>
                    <dl class="admin-co-details">
                        <template x-for="(row, dIdx) in (selectedTicket.detail?.details || [])" :key="dIdx">
                            <div class="admin-co-details-row">
                                <dt x-text="row.label"></dt>
                                <dd :class="{ 'is-muted': row.muted }" x-text="row.value"></dd>
                            </div>
                        </template>
                    </dl>
                </section>

                <section class="admin-card admin-co-panel admin-co-activity-panel">
                    <div class="admin-co-section-head">
                        <h3 class="admin-co-section-title">Recent Activity</h3>
                        <button type="button" class="admin-co-link-btn">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                                <path d="M10 1L4 7M10 1H6M10 1V5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Full Log
                        </button>
                    </div>
                    <ul class="admin-co-activity">
                        <template x-for="(event, aIdx) in (selectedTicket.detail?.activity || [])" :key="aIdx">
                            <li class="admin-co-activity-item">
                                <span class="admin-co-activity-dot" :class="'is-' + (event.type || 'password')" aria-hidden="true"></span>
                                <div class="admin-co-activity-body">
                                    <p class="admin-co-activity-title" x-text="event.title"></p>
                                    <time class="admin-co-activity-time" x-text="event.time"></time>
                                </div>
                            </li>
                        </template>
                    </ul>
                </section>
            </div>
        </div>
    </template>
</div>