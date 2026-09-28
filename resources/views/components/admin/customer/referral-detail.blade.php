@props(['profile'])

@php
$firstName = explode(' ', trim($profile['name'] ?? ''))[0] ?: 'Customer';
$balanceTitle = $firstName . "'s credits balance";
@endphp

<div class="admin-co-ref-detail" x-show="selectedReferral" x-cloak>
    <div class="admin-co-overview-head">
        <div class="admin-co-bk-detail-title-row">
            <button type="button" class="admin-co-bk-detail-back" @click="closeReferral()" aria-label="Back to referrals">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                    <g filter="url(#filter0_d_ref_detail_back)">
                        <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
                    </g>
                    <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_ref_detail_back" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_ref_detail_back" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_ref_detail_back" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
            <h2 class="admin-page-title mb-0">
                Referral <span x-text="selectedReferral?.detail?.invoice_id"></span>
            </h2>
        </div>
        <div class="admin-co-ref-detail-head-actions">
            <button type="button" class="admin-co-ref-download-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none" aria-hidden="true">
                    <path d="M2.35714 7H7M2.35714 8.85714H8.85714M2.35714 10.7143H5.14286M11.6429 11.6429V5.14286L7 0.5H2.35714C1.8646 0.5 1.39223 0.695663 1.04394 1.04394C0.695663 1.39223 0.5 1.8646 0.5 2.35714V11.6429C0.5 12.1354 0.695663 12.6078 1.04394 12.9561C1.39223 13.3043 1.8646 13.5 2.35714 13.5H9.78571C10.2783 13.5 10.7506 13.3043 11.0989 12.9561C11.4472 12.6078 11.6429 12.1354 11.6429 11.6429Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7 0.5V3.28571C7 3.77826 7.19566 4.25063 7.54394 4.59891C7.89223 4.94719 8.3646 5.14286 8.85714 5.14286H11.6429" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Download Receipt
            </button>
            <button type="button" class="admin-btn-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                    <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                </svg>
                Export Data
            </button>
        </div>
    </div>

    <template x-if="selectedReferral">
        <div class="admin-co-ref-detail-body">
            <span class="admin-co-ref-detail-badge" x-text="selectedReferral.detail?.badge || 'Credits'"></span>

            <section class="admin-co-ref-detail-summary">
                <div class="admin-co-ref-detail-summary-main">
                    <div class="admin-co-ref-detail-summary-item">
                        <span class="admin-co-ref-detail-summary-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M4.79297 0.912109C5.01301 0.196056 5.98699 0.196053 6.20703 0.912109L6.72168 2.58887C6.91507 3.21819 7.48798 3.65619 8.14551 3.65625H9.88086C10.5745 3.65625 10.9151 4.59361 10.3213 5.0498L8.8623 6.1709C8.35579 6.56018 8.14811 7.23028 8.33594 7.8418L8.87988 9.6123C9.11028 10.362 8.2828 10.8907 7.7334 10.4688L6.39746 9.44238C5.86617 9.03434 5.13383 9.03434 4.60254 9.44238L3.2666 10.4688C2.7172 10.8907 1.88972 10.362 2.12012 9.6123L2.66406 7.8418C2.85189 7.23028 2.64421 6.56018 2.1377 6.1709L0.678711 5.0498C0.0848595 4.59361 0.425508 3.65625 1.11914 3.65625H2.85449C3.51202 3.65619 4.08493 3.21819 4.27832 2.58887L4.79297 0.912109Z" stroke="#3B3731" stroke-width="0.75" />
                            </svg>
                        </span>
                        <p class="admin-co-ref-detail-summary-text">Referral credit</p>
                    </div>
                    <div class="admin-co-ref-detail-summary-item">
                        <span class="admin-co-ref-detail-summary-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M0.375 5.60001C0.375 3.52596 0.375 2.48866 1.0196 1.84461C1.6642 1.20056 2.70095 1.20001 4.775 1.20001H6.975C9.04905 1.20001 10.0863 1.20001 10.7304 1.84461C11.3744 2.48921 11.375 3.52596 11.375 5.60001V6.70001C11.375 8.77406 11.375 9.81136 10.7304 10.4554C10.0858 11.0995 9.04905 11.1 6.975 11.1H4.775C2.70095 11.1 1.66365 11.1 1.0196 10.4554C0.37555 9.81081 0.375 8.77406 0.375 6.70001V5.60001Z" stroke="#3B3731" stroke-width="0.75" />
                                <path d="M3.12539 1.2V0.375M8.62539 1.2V0.375M0.650391 3.95H11.1004" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" />
                                <path d="M9.17495 8.35001C9.17495 8.49588 9.11701 8.63577 9.01386 8.73892C8.91072 8.84206 8.77082 8.90001 8.62495 8.90001C8.47908 8.90001 8.33919 8.84206 8.23604 8.73892C8.1329 8.63577 8.07495 8.49588 8.07495 8.35001C8.07495 8.20414 8.1329 8.06424 8.23604 7.9611C8.33919 7.85795 8.47908 7.80001 8.62495 7.80001C8.77082 7.80001 8.91072 7.85795 9.01386 7.9611C9.11701 8.06424 9.17495 8.20414 9.17495 8.35001ZM9.17495 6.15001C9.17495 6.29588 9.11701 6.43577 9.01386 6.53891C8.91072 6.64206 8.77082 6.70001 8.62495 6.70001C8.47908 6.70001 8.33919 6.64206 8.23604 6.53891C8.1329 6.43577 8.07495 6.29588 8.07495 6.15001C8.07495 6.00414 8.1329 5.86424 8.23604 5.7611C8.33919 5.65795 8.47908 5.60001 8.62495 5.60001C8.77082 5.60001 8.91072 5.65795 9.01386 5.7611C9.11701 5.86424 9.17495 6.00414 9.17495 6.15001ZM6.42495 8.35001C6.42495 8.49588 6.367 8.63577 6.26386 8.73892C6.16071 8.84206 6.02082 8.90001 5.87495 8.90001C5.72908 8.90001 5.58919 8.84206 5.48604 8.73892C5.3829 8.63577 5.32495 8.49588 5.32495 8.35001C5.32495 8.20414 5.3829 8.06424 5.48604 7.9611C5.58919 7.85795 5.72908 7.80001 5.87495 7.80001C6.02082 7.80001 6.16071 7.85795 6.26386 7.9611C6.367 8.06424 6.42495 8.20414 6.42495 8.35001ZM6.42495 6.15001C6.42495 6.29588 6.367 6.43577 6.26386 6.53891C6.16071 6.64206 6.02082 6.70001 5.87495 6.70001C5.72908 6.70001 5.58919 6.64206 5.48604 6.53891C5.3829 6.43577 5.32495 6.29588 5.32495 6.15001C5.32495 6.00414 5.3829 5.86424 5.48604 5.7611C5.58919 5.65795 5.72908 5.60001 5.87495 5.60001C6.02082 5.60001 6.16071 5.65795 6.26386 5.7611C6.367 5.86424 6.42495 6.00414 6.42495 6.15001ZM3.67495 8.35001C3.67495 8.49588 3.617 8.63577 3.51386 8.73892C3.41071 8.84206 3.27082 8.90001 3.12495 8.90001C2.97908 8.90001 2.83919 8.84206 2.73604 8.73892C2.6329 8.63577 2.57495 8.49588 2.57495 8.35001C2.57495 8.20414 2.6329 8.06424 2.73604 7.9611C2.83919 7.85795 2.97908 7.80001 3.12495 7.80001C3.27082 7.80001 3.41071 7.85795 3.51386 7.9611C3.617 8.06424 3.67495 8.20414 3.67495 8.35001ZM3.67495 6.15001C3.67495 6.29588 3.617 6.43577 3.51386 6.53891C3.41071 6.64206 3.27082 6.70001 3.12495 6.70001C2.97908 6.70001 2.83919 6.64206 2.73604 6.53891C2.6329 6.43577 2.57495 6.29588 2.57495 6.15001C2.57495 6.00414 2.6329 5.86424 2.73604 5.7611C2.83919 5.65795 2.97908 5.60001 3.12495 5.60001C3.27082 5.60001 3.41071 5.65795 3.51386 5.7611C3.617 5.86424 3.67495 6.00414 3.67495 6.15001Z" fill="#3B3731" />
                            </svg>
                        </span>
                        <p class="admin-co-ref-detail-summary-text" x-text="selectedReferral.detail?.summary_date"></p>
                    </div>
                    <div class="admin-co-ref-detail-summary-item">
                        <span class="admin-co-ref-detail-summary-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none">
                                <path d="M5.88574 0.375C7.64534 0.375151 9.07129 1.80197 9.07129 3.56152C9.07108 5.32091 7.64521 6.74692 5.88574 6.74707C4.12614 6.74707 2.69942 5.321 2.69922 3.56152C2.69922 1.80187 4.12602 0.375 5.88574 0.375Z" stroke="#3B3731" stroke-width="0.75" />
                                <path d="M0.382936 12.9496C0.27502 10.7913 1.22469 6.79849 5.88668 6.79849C6.92629 6.79849 8.55504 6.89064 9.77132 8.19379C10.3059 8.74835 11.375 10.4759 11.375 12.9496" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" />
                            </svg>
                        </span>
                        <p class="admin-co-ref-detail-summary-text">
                            <span x-text="selectedReferral.detail?.new_user?.short_name || selectedReferral.name"></span>
                            <span class="admin-co-ref-detail-summary-sep">·</span>
                            <span x-text="selectedReferral.detail?.new_user?.id || selectedReferral.id"></span>
                        </p>
                    </div>
                    <div class="admin-co-ref-detail-summary-item">
                        <span class="admin-co-ref-detail-summary-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="11" viewBox="0 0 12 11" fill="none">
                                <path d="M3.54331 5.46279C2.53243 5.0992 1.80957 4.13195 1.80957 2.99588C1.80957 1.54841 2.98301 0.375 4.43052 0.375C4.68322 0.375 4.92757 0.410762 5.15878 0.477502" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" />
                                <path d="M0.380841 9.9054C0.30881 8.46481 0.876991 5.91369 3.54288 5.45132" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" />
                                <path d="M7.37415 1.48596C8.61449 1.48604 9.62024 2.49175 9.62024 3.73206C9.62012 4.97226 8.61442 5.97807 7.37415 5.97815C6.1338 5.97815 5.12817 4.97231 5.12805 3.73206C5.12805 2.4917 6.13373 1.48596 7.37415 1.48596Z" stroke="#3B3731" stroke-width="0.75" />
                                <path d="M3.33911 9.94253C3.48074 8.3201 4.40256 6.11438 7.37516 6.11438C8.14027 6.11438 9.33898 6.1822 10.2341 7.14127C10.575 7.49494 11.2114 8.49682 11.3751 9.94253" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" />
                            </svg>
                        </span>
                        <p class="admin-co-ref-detail-summary-text">
                            Code <span x-text="selectedReferral.detail?.referral_code"></span> used
                        </p>
                    </div>
                </div>
                <div class="admin-co-ref-detail-summary-side">
                    <div class="admin-co-ref-detail-summary-stat">
                        <p class="admin-co-ref-detail-summary-label">Credit amount</p>
                        <p class="admin-co-ref-detail-summary-amount" x-text="selectedReferral.detail?.credit_amount"></p>
                        <p class="admin-co-ref-detail-summary-note" x-text="selectedReferral.detail?.credit_type"></p>
                    </div>
                    <div class="admin-co-ref-detail-summary-stat">
                        <p class="admin-co-ref-detail-summary-label">Expires</p>
                        <p class="admin-co-ref-detail-summary-value is-strong" x-text="selectedReferral.detail?.expires"></p>
                        <p class="admin-co-ref-detail-summary-note" x-text="selectedReferral.detail?.expires_note"></p>
                    </div>
                    <div class="admin-co-ref-detail-summary-stat">
                        <p class="admin-co-ref-detail-summary-label">Credit status</p>
                        <p
                            class="admin-co-ref-detail-summary-status"
                            :class="'is-' + (selectedReferral.detail?.status || selectedReferral.status)"
                            x-text="selectedReferral.detail?.status_label || selectedReferral.status_label"></p>
                        <p class="admin-co-ref-detail-summary-note" x-text="selectedReferral.detail?.status_note"></p>
                    </div>
                </div>
            </section>

            <div class="admin-co-ref-detail-sep" aria-hidden="true"></div>

            <div class="admin-co-ref-detail-grid">
                <section class="admin-card admin-co-panel admin-co-ref-detail-card">
                    <div class="admin-co-ref-detail-hero">
                        <span class="admin-co-ref-detail-hero-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                <rect width="48" height="48" rx="24" fill="#F1F1F1" />
                                <rect x="12.5" y="12.5" width="23" height="23" rx="11.5" fill="#F1F1F1" stroke="#9C9A97" />
                                <path d="M23.3164 19.0215C23.5303 18.3263 24.4697 18.3263 24.6836 19.0215L25.2461 20.8516C25.4684 21.5748 26.1273 22.0789 26.8857 22.0791H28.7793C29.4437 22.0792 29.7874 22.9885 29.2041 23.4365L27.6123 24.6592C27.0291 25.1074 26.7912 25.8784 27.0068 26.5811L27.6006 28.5127C27.8286 29.2548 27.0131 29.7495 26.4912 29.3486L25.0342 28.2295C24.4219 27.7591 23.5781 27.7591 22.9658 28.2295L21.5088 29.3486C20.9869 29.7495 20.1714 29.2548 20.3994 28.5127L20.9932 26.5811C21.2089 25.8784 20.9709 25.1074 20.3877 24.6592L18.7959 23.4365C18.2126 22.9885 18.5563 22.0792 19.2207 22.0791H21.1143C21.8727 22.0789 22.5316 21.5748 22.7539 20.8516L23.3164 19.0215Z" stroke="#9C9A97" />
                            </svg>
                        </span>
                        <p class="admin-co-ref-detail-hero-amount" x-text="selectedReferral.detail?.credit_amount_display || selectedReferral.credit"></p>
                        <span class="admin-co-ref-detail-badge is-inline" x-text="selectedReferral.detail?.badge || 'Credits'"></span>
                        <p class="admin-co-ref-detail-hero-sub" x-text="selectedReferral.detail?.subtitle"></p>
                    </div>

                    <div class="admin-co-ref-detail-sep" aria-hidden="true"></div>

                    <dl class="admin-co-details admin-co-ref-detail-rows">
                        <div class="admin-co-details-row">
                            <dt>Invoice ID</dt>
                            <dd x-text="selectedReferral.detail?.invoice_id"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Transaction type</dt>
                            <dd x-text="selectedReferral.detail?.transaction_type"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Credit type</dt>
                            <dd x-text="selectedReferral.detail?.credit_type"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Referral code used</dt>
                            <dd x-text="selectedReferral.detail?.referral_code"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Referred by</dt>
                            <dd>
                                <button type="button" class="admin-co-ref-detail-link">
                                    <span x-text="selectedReferral.detail?.referred_by?.name"></span>
                                    <span class="admin-co-ref-detail-summary-sep">·</span>
                                    <span x-text="selectedReferral.detail?.referred_by?.id"></span>
                                    <span aria-hidden="true">→</span>
                                </button>
                            </dd>
                        </div>
                        <div class="admin-co-details-row admin-co-ref-detail-user-row">
                            <dt>New user sign up</dt>
                            <dd>
                                <button type="button" class="admin-co-ref-detail-user-chip">
                                    <span class="admin-co-ref-detail-user-avatar" x-text="selectedReferral.detail?.new_user?.initials"></span>
                                    <span class="admin-co-ref-detail-user-meta">
                                        <span class="admin-co-ref-detail-user-name" x-text="selectedReferral.detail?.new_user?.name"></span>
                                        <span class="admin-co-ref-detail-user-email" x-text="selectedReferral.detail?.new_user?.email"></span>
                                    </span>
                                    <span class="admin-co-ref-detail-user-arrow" aria-hidden="true">→</span>
                                </button>
                            </dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Date signed up</dt>
                            <dd x-text="selectedReferral.detail?.signed_up"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>First booking</dt>
                            <dd x-text="selectedReferral.detail?.first_booking"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Credit amount</dt>
                            <dd class="admin-co-ref-credit" x-text="selectedReferral.detail?.credit_amount"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Date applied</dt>
                            <dd x-text="selectedReferral.detail?.date_applied"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Expires</dt>
                            <dd x-text="selectedReferral.detail?.expires"></dd>
                        </div>
                        <div class="admin-co-details-row">
                            <dt>Credit status</dt>
                            <dd>
                                <span
                                    class="admin-po-status"
                                    :class="'is-' + (selectedReferral.detail?.status || selectedReferral.status)"
                                    x-text="selectedReferral.detail?.status_label || selectedReferral.status_label"></span>
                            </dd>
                        </div>
                    </dl>
                </section>

                <div class="admin-co-ref-detail-aside">
                    <section class="admin-card admin-co-panel admin-co-ref-detail-balance">
                        <x-admin.customer.section-header :title="$balanceTitle" />
                        <div class="admin-co-ref-metrics">
                            <template x-for="(card, idx) in (selectedReferral.detail?.balance_cards || [])" :key="idx">
                                <div class="admin-co-ref-metric" :class="{ 'is-available': card.highlight }">
                                    <p class="admin-co-ref-metric-value" x-text="card.value"></p>
                                    <p class="admin-co-ref-metric-label" x-text="card.label"></p>
                                </div>
                            </template>
                        </div>
                        <div class="admin-co-ref-meta">
                            <div class="admin-co-details-row">
                                <dt>Pending credits</dt>
                                <dd x-text="selectedReferral.detail?.pending"></dd>
                            </div>
                            <div class="admin-co-details-row">
                                <dt>Expired credits</dt>
                                <dd x-text="selectedReferral.detail?.expired"></dd>
                            </div>
                            <div class="admin-co-details-row">
                                <dt>Next expiry</dt>
                                <dd x-text="selectedReferral.detail?.next_expiry"></dd>
                            </div>
                        </div>
                    </section>

                    <section class="admin-card admin-co-panel admin-co-activity-panel admin-co-ref-detail-timeline">
                        <x-admin.customer.section-header title="Credit timeline" />
                        <ul class="admin-co-activity">
                            <template x-for="(item, idx) in (selectedReferral.detail?.timeline || [])" :key="idx">
                                <li class="admin-co-activity-item">
                                    <span class="admin-co-activity-dot" :class="'is-' + (item.tone || 'password')" aria-hidden="true"></span>
                                    <div>
                                        <p class="admin-co-activity-title" x-text="item.title"></p>
                                        <span class="admin-co-activity-time" x-text="item.time"></span>
                                    </div>
                                </li>
                            </template>
                        </ul>
                    </section>
                </div>
            </div>

            <section
                class="admin-card admin-co-panel"
                :class="{ 'is-editing': editingNotes }"
                x-data="{
                    editingNotes: false,
                    newNote: '',
                    editIndex: -1,
                    notes: [...(selectedReferral?.detail?.notes || [])],
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
                        <label class="admin-co-note-compose-label" for="co-ref-note-{{ $profile['id'] ?? 'detail' }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                        <textarea
                            id="co-ref-note-{{ $profile['id'] ?? 'detail' }}"
                            class="admin-co-note-textarea"
                            rows="4"
                            placeholder="Write an internal note about this referral credit - only visible to the admin team members."
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
        </div>
    </template>
</div>