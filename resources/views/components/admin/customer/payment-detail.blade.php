@props(['profile'])

@php
$customerName = $profile['name'] ?? 'Jane Doe';
$customerId = $profile['id'] ?? 'USR-01452';
@endphp

<div class="admin-co-pay-detail" x-show="selectedPayment" x-cloak>
    <div class="admin-co-overview-head">
        <div class="admin-co-bk-detail-title-row">
            <button type="button" class="admin-co-bk-detail-back" @click="closePayment()" aria-label="Back to payments">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                    <g filter="url(#filter0_d_pay_detail_back)">
                        <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
                    </g>
                    <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_pay_detail_back" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_pay_detail_back" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_pay_detail_back" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
            <h2 class="admin-page-title mb-0">
                Payment <span x-text="selectedPayment?.invoice_id"></span>
            </h2>
        </div>
        <div class="admin-co-pay-detail-head-actions">
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

    <template x-if="selectedPayment">
        <div class="admin-co-pay-detail-body" :class="'is-' + (selectedPayment.detail?.variant || selectedPayment.status)">
            <span
                class="admin-po-status admin-co-pay-detail-badge"
                :class="'is-' + (selectedPayment.status || 'processed')"
                x-text="selectedPayment.status_label"></span>

            <section class="admin-co-pay-detail-summary">
                <div class="admin-co-pay-detail-summary-main">
                    <div class="admin-co-pay-detail-summary-item">
                        <span class="admin-co-pay-detail-summary-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M1.5 3.5H10.5V9.5C10.5 10.0523 10.0523 10.5 9.5 10.5H2.5C1.94772 10.5 1.5 10.0523 1.5 9.5V3.5Z" stroke="#3B3731" stroke-width="0.9" />
                                <path d="M1.5 5H10.5M4 1.5V3.5M8 1.5V3.5" stroke="#3B3731" stroke-width="0.9" stroke-linecap="round" />
                            </svg>
                        </span>
                        <p class="admin-co-pay-detail-summary-text" x-text="selectedPayment.detail?.charge_label"></p>
                    </div>
                    <div class="admin-co-pay-detail-summary-item">
                        <span class="admin-co-pay-detail-summary-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <rect x="1" y="2" width="10" height="9" rx="1.5" stroke="#3B3731" stroke-width="0.9" />
                                <path d="M1 5H11M4 1V3M8 1V3" stroke="#3B3731" stroke-width="0.9" stroke-linecap="round" />
                            </svg>
                        </span>
                        <p class="admin-co-pay-detail-summary-text" x-text="selectedPayment.detail?.summary_date || selectedPayment.date"></p>
                    </div>
                    <div class="admin-co-pay-detail-summary-item">
                        <span class="admin-co-pay-detail-summary-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="14" viewBox="0 0 12 14" fill="none">
                                <circle cx="6" cy="3.5" r="2.75" stroke="#3B3731" stroke-width="0.9" />
                                <path d="M1 12.5C1 9.6 3.1 7.5 6 7.5C8.9 7.5 11 9.6 11 12.5" stroke="#3B3731" stroke-width="0.9" stroke-linecap="round" />
                            </svg>
                        </span>
                        <p class="admin-co-pay-detail-summary-text">
                            <span>{{ $customerName }}</span>
                            <span class="admin-co-pay-detail-summary-sep">·</span>
                            <span>{{ $customerId }}</span>
                        </p>
                    </div>
                    <div class="admin-co-pay-detail-summary-item">
                        <span class="admin-co-pay-detail-summary-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                <path d="M2 3.5H10V9.5H2V3.5Z" stroke="#3B3731" stroke-width="0.9" />
                                <path d="M4 1.5V3.5M8 1.5V3.5M2 5.5H10" stroke="#3B3731" stroke-width="0.9" stroke-linecap="round" />
                            </svg>
                        </span>
                        <p class="admin-co-pay-detail-summary-text">
                            Booking ID <span x-text="selectedPayment.booking_id"></span>
                        </p>
                    </div>
                </div>

                <div class="admin-co-pay-detail-summary-side">
                    <div class="admin-co-pay-detail-summary-stat">
                        <p class="admin-co-pay-detail-summary-label" x-text="selectedPayment.detail?.amount_label || 'Amount charged'"></p>
                        <p
                            class="admin-co-pay-detail-summary-amount"
                            :class="'is-' + (selectedPayment.detail?.amount_tone || selectedPayment.amount_tone || 'default')"
                            x-text="selectedPayment.detail?.hero_amount || selectedPayment.amount"></p>
                        <p class="admin-co-pay-detail-summary-note" x-text="selectedPayment.detail?.amount_note || ''"></p>
                    </div>
                    <div class="admin-co-pay-detail-summary-stat">
                        <p class="admin-co-pay-detail-summary-label" x-text="selectedPayment.detail?.status_side_label || 'Payment status'"></p>
                        <p
                            class="admin-co-pay-detail-summary-status"
                            :class="'is-' + (selectedPayment.detail?.status_key || selectedPayment.status)"
                            x-text="selectedPayment.detail?.status_side_value || selectedPayment.status_label"></p>
                        <p class="admin-co-pay-detail-summary-note" x-text="selectedPayment.detail?.status_side_note || ''"></p>
                    </div>
                    <div class="admin-co-pay-detail-summary-stat" x-show="selectedPayment.detail?.payout_label" x-cloak>
                        <p class="admin-co-pay-detail-summary-label" x-text="selectedPayment.detail?.payout_label"></p>
                        <p
                            class="admin-co-pay-detail-summary-status"
                            :class="'is-' + (selectedPayment.detail?.payout_key || selectedPayment.status)"
                            x-text="selectedPayment.detail?.payout_value"></p>
                        <p class="admin-co-pay-detail-summary-note" x-text="selectedPayment.detail?.payout_note || ''"></p>
                    </div>
                    <div class="admin-co-pay-detail-summary-stat" x-show="selectedPayment.detail?.failure_reason" x-cloak>
                        <p class="admin-co-pay-detail-summary-label">Failure reason</p>
                        <p class="admin-co-pay-detail-summary-status is-failed" x-text="selectedPayment.detail?.failure_reason"></p>
                    </div>
                </div>
            </section>

            <div class="admin-co-pay-detail-sep" aria-hidden="true"></div>

            <div class="admin-co-pay-detail-grid">
                <section class="admin-card admin-co-panel admin-co-pay-detail-card">
                    <div class="admin-co-pay-detail-hero">
                        <span class="admin-co-pay-detail-hero-icon" :class="'is-' + (selectedPayment.detail?.variant || selectedPayment.status)" aria-hidden="true">
                            <template x-if="(selectedPayment.detail?.variant || selectedPayment.status) === 'processed'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                    <rect width="48" height="48" rx="24" fill="#F4F8EC" />
                                    <rect x="12.5" y="12.5" width="23" height="23" rx="11.5" fill="#F4F8EC" stroke="#A7C569" />
                                    <path d="M28.9044 21L22.77 27.7789C22.6363 27.9263 22.4803 28 22.302 28C22.1237 28 21.9677 27.9263 21.8339 27.7789L18.959 24.6105" stroke="#A7C569" stroke-linecap="round" />
                                </svg>
                            </template>
                            <template x-if="(selectedPayment.detail?.variant || selectedPayment.status) === 'open_refund'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                    <rect width="48" height="48" rx="24" fill="#FEF0DC" />
                                    <rect x="12.5" y="12.5" width="23" height="23" rx="11.5" fill="#FEF0DC" stroke="#FFAF3B" />
                                    <path d="M23.0917 29.0041H26.3647C28.1723 29.0041 29.6377 27.5387 29.6377 25.7311C29.6377 23.9234 28.1723 22.4581 26.3647 22.4581H19.0005" stroke="#FFAF3B" stroke-linecap="round" />
                                    <path d="M21.4551 24.9128L19.0002 22.4579L21.4582 20" stroke="#FFAF3B" stroke-linecap="round" />
                                </svg>
                            </template>
                            <template x-if="(selectedPayment.detail?.variant || selectedPayment.status) === 'refunded'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                    <rect width="48" height="48" rx="24" fill="#FEF0DC" />
                                    <rect x="12.5" y="12.5" width="23" height="23" rx="11.5" fill="#FEF0DC" stroke="#FFAF3B" />
                                    <path d="M23.0917 29.0041H26.3647C28.1723 29.0041 29.6377 27.5387 29.6377 25.7311C29.6377 23.9234 28.1723 22.4581 26.3647 22.4581H19.0005" stroke="#FFAF3B" stroke-linecap="round" />
                                    <path d="M21.4551 24.9128L19.0002 22.4579L21.4582 20" stroke="#FFAF3B" stroke-linecap="round" />
                                </svg>
                            </template>
                            <template x-if="(selectedPayment.detail?.variant || selectedPayment.status) === 'failed'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                    <rect width="48" height="48" rx="24" fill="#FEE8E4" />
                                    <rect x="12.5" y="12.5" width="23" height="23" rx="11.5" fill="#FEE8E4" stroke="#FE6F56" />
                                    <path d="M20 20L28 28M28 20L20 28" stroke="#FE6F56" stroke-linecap="round" />
                                </svg>
                            </template>
                            <template x-if="!['processed','open_refund','refunded','failed'].includes(selectedPayment.detail?.variant || selectedPayment.status)">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                    <circle cx="24" cy="24" r="24" fill="#F4F8EC" />
                                    <circle cx="24" cy="24" r="14" fill="#A1C25D" />
                                    <path d="M17.5 24.5L21.5 28.5L30.5 19.5" stroke="#fff" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </template>
                        </span>
                        <p
                            class="admin-co-pay-detail-hero-amount"
                            :class="'is-' + (selectedPayment.detail?.amount_tone || selectedPayment.amount_tone || 'default')"
                            x-text="selectedPayment.detail?.hero_amount || selectedPayment.amount"></p>
                        <span
                            class="admin-po-status is-inline"
                            :class="'is-' + (selectedPayment.status || 'processed')"
                            x-text="selectedPayment.status_label"></span>
                        <p class="admin-co-pay-detail-hero-sub" x-text="selectedPayment.detail?.hero_sub"></p>
                    </div>

                    <div class="admin-co-pay-detail-sep" aria-hidden="true"></div>

                    <dl class="admin-co-details admin-co-pay-detail-rows">
                        <template x-for="(row, idx) in (selectedPayment.detail?.rows || [])" :key="idx">
                            <div class="admin-co-details-row">
                                <dt x-text="row.label"></dt>
                                <dd
                                    :class="{
                                        'is-status': row.tone,
                                        ['is-' + row.tone]: !!row.tone,
                                    }"
                                    x-text="row.value"></dd>
                            </div>
                        </template>
                    </dl>
                </section>

                <div class="admin-co-pay-detail-aside">
                    <section class="admin-card admin-co-panel admin-co-pay-detail-breakdown">
                        <x-admin.customer.section-header :title="'Price breakdown'" />
                        <dl class="admin-co-details admin-co-pay-detail-rows">
                            <template x-for="(line, idx) in (selectedPayment.detail?.price_lines || [])" :key="idx">
                                <div class="admin-co-details-row" :class="{ 'is-discount': line.tone === 'discount', 'is-refund': line.tone === 'refund' || line.tone === 'open_refund', 'is-failed': line.tone === 'failed' }">
                                    <dt x-text="line.label"></dt>
                                    <dd x-text="line.value"></dd>
                                </div>
                            </template>
                            <div class="admin-co-details-row admin-co-pay-detail-total-row">
                                <dt x-text="selectedPayment.detail?.total_label || 'Total charged to customer'"></dt>
                                <dd x-text="selectedPayment.detail?.total || selectedPayment.amount"></dd>
                            </div>
                            <div class="admin-co-details-row" x-show="selectedPayment.detail?.aside_extra_label" x-cloak>
                                <dt x-text="selectedPayment.detail?.aside_extra_label"></dt>
                                <dd
                                    :class="'is-' + (selectedPayment.detail?.aside_extra_tone || 'default')"
                                    x-text="selectedPayment.detail?.aside_extra_value"></dd>
                            </div>
                            <div class="admin-co-details-row" x-show="selectedPayment.detail?.aside_payout_label" x-cloak>
                                <dt x-text="selectedPayment.detail?.aside_payout_label"></dt>
                                <dd x-text="selectedPayment.detail?.aside_payout_value"></dd>
                            </div>
                        </dl>
                    </section>

                    <section class="admin-card admin-co-panel admin-co-pay-detail-status-panel" x-show="selectedPayment.detail?.callout" x-cloak>
                        <div class="admin-co-pay-detail-status-head" x-show="selectedPayment.detail?.callout_title" x-cloak>
                            <h3 class="admin-co-section-title" x-text="selectedPayment.detail?.callout_title"></h3>
                            <button type="button" class="admin-co-link-btn" x-show="selectedPayment.detail?.callout_action" x-cloak x-text="selectedPayment.detail?.callout_action"></button>
                        </div>

                        <div
                            class="admin-co-pay-detail-callout"
                            :class="'is-' + (selectedPayment.detail?.variant || selectedPayment.status)">
                            <span class="admin-co-pay-detail-callout-icon" aria-hidden="true">
                                <template x-if="(selectedPayment.detail?.variant || selectedPayment.status) === 'processed'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect width="24" height="24" rx="5" fill="#A7C569" />
                                        <path d="M16.9044 9L10.77 15.7789C10.6363 15.9263 10.4803 16 10.302 16C10.1237 16 9.96767 15.9263 9.83395 15.7789L6.95898 12.6105" stroke="white" stroke-width="1.25" stroke-linecap="round" />
                                    </svg>
                                </template>
                                <template x-if="(selectedPayment.detail?.variant || selectedPayment.status) === 'open_refund'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect width="24" height="24" rx="5" fill="#FFAF3B" />
                                        <path d="M7.3806 13.9134C7.12933 13.3068 7 12.6566 7 12C7 10.6739 7.52678 9.40215 8.46447 8.46447C9.40215 7.52678 10.6739 7 12 7C13.3261 7 14.5979 7.52678 15.5355 8.46447C16.4732 9.40215 17 10.6739 17 12C17 12.6566 16.8707 13.3068 16.6194 13.9134C16.3681 14.52 15.9998 15.0712 15.5355 15.5355C15.0712 15.9998 14.52 16.3681 13.9134 16.6194C13.3068 16.8707 12.6566 17 12 17C11.3434 17 10.6932 16.8707 10.0866 16.6194C9.47995 16.3681 8.92876 15.9998 8.46447 15.5355C8.00017 15.0712 7.63188 14.52 7.3806 13.9134Z" stroke="#FDFDFD" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 9.22223V12L13.6667 13.6667" stroke="#FDFDFD" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </template>
                                <template x-if="(selectedPayment.detail?.variant || selectedPayment.status) === 'refunded'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect width="24" height="24" rx="5" fill="#649FC9" />
                                        <path d="M7.3806 13.9134C7.12933 13.3068 7 12.6566 7 12C7 10.6739 7.52678 9.40215 8.46447 8.46447C9.40215 7.52678 10.6739 7 12 7C13.3261 7 14.5979 7.52678 15.5355 8.46447C16.4732 9.40215 17 10.6739 17 12C17 12.6566 16.8707 13.3068 16.6194 13.9134C16.3681 14.52 15.9998 15.0712 15.5355 15.5355C15.0712 15.9998 14.52 16.3681 13.9134 16.6194C13.3068 16.8707 12.6566 17 12 17C11.3434 17 10.6932 16.8707 10.0866 16.6194C9.47995 16.3681 8.92876 15.9998 8.46447 15.5355C8.00017 15.0712 7.63188 14.52 7.3806 13.9134Z" stroke="#FDFDFD" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M12 9.22223V12L13.6667 13.6667" stroke="#FDFDFD" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </template>
                                <template x-if="(selectedPayment.detail?.variant || selectedPayment.status) === 'failed'">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                        <rect width="24" height="24" rx="5" fill="#FE6F56" />
                                        <path d="M0.5 0.5L8.5 8.5M8.5 0.5L0.5 8.5" stroke="#FDFDFD" stroke-linecap="round" transform="translate(7.5 7.5)" />
                                    </svg>
                                </template>
                            </span>
                            <div>
                                <p class="admin-co-pay-detail-callout-title" x-text="selectedPayment.detail?.callout?.title"></p>
                                <p class="admin-co-pay-detail-callout-body" x-text="selectedPayment.detail?.callout?.body"></p>
                            </div>
                        </div>

                        <dl class="admin-co-details admin-co-pay-detail-rows" x-show="(selectedPayment.detail?.callout_rows || []).length" x-cloak>
                            <template x-for="(row, idx) in (selectedPayment.detail?.callout_rows || [])" :key="idx">
                                <div class="admin-co-details-row">
                                    <dt x-text="row.label"></dt>
                                    <dd x-text="row.value"></dd>
                                </div>
                            </template>
                        </dl>
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
                    notes: [...(selectedPayment?.detail?.notes || [])],
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
                        <label class="admin-co-note-compose-label" for="co-pay-note-{{ $profile['id'] ?? 'detail' }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                        <textarea
                            id="co-pay-note-{{ $profile['id'] ?? 'detail' }}"
                            class="admin-co-note-textarea"
                            rows="4"
                            placeholder="Write an internal note about this payment - only visible to the admin team members."
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

            <section class="admin-card admin-co-panel admin-co-activity-panel admin-co-pay-detail-timeline">
                <x-admin.customer.section-header title="Payment activity timeline" />
                <ul class="admin-co-activity admin-co-pay-activity">
                    <template x-for="(item, idx) in (selectedPayment.detail?.timeline || [])" :key="idx">
                        <li class="admin-co-activity-item">
                            <span class="admin-co-activity-dot" :class="'is-' + (item.tone || 'gray')" aria-hidden="true"></span>
                            <div>
                                <p class="admin-co-activity-title" x-text="item.title"></p>
                                <span class="admin-co-activity-time" x-text="item.time"></span>
                            </div>
                        </li>
                    </template>
                </ul>
            </section>
        </div>
    </template>
</div>