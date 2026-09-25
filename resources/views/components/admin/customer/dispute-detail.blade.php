{{-- Full dispute view — rendered inside bookings Alpine scope --}}
<div class="admin-co-dispute" x-show="selectedBooking && disputeOpen" x-cloak>
    <div class="admin-co-overview-head">
        <div class="admin-co-bk-detail-title-row">
            <button type="button" class="admin-co-bk-detail-back" @click="closeDispute()" aria-label="Back to booking">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                    <g filter="url(#filter0_d_dispute_back)">
                        <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
                    </g>
                    <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_dispute_back" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_dispute_back" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_dispute_back" result="shape" />
                        </filter>
                    </defs>
                </svg>
            </button>
            <h2 class="admin-page-title mb-0">
                Dispute <span x-text="selectedBooking?.dispute_id || '—'"></span>
            </h2>
        </div>
        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    {{-- Summary card (reuse booking card chrome) --}}
    <template x-if="selectedBooking">
        <article
            class="admin-co-bk-card admin-co-bk-detail-summary"
            :class="{ 'is-disputed': selectedBooking.status === 'disputed' }">
            <div class="admin-co-bk-card-head">
                <p class="admin-co-bk-meta">
                    <span class="admin-co-bk-warn" x-show="selectedBooking.status === 'disputed'" aria-label="Disputed">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none">
                            <path d="M9.53101 0.535238C9.11998 -0.178413 8.01236 -0.178413 7.60133 0.535238L0.129134 13.5183L0.0672303 13.6453C-0.173102 14.2451 0.256901 14.9057 0.943487 14.9911L1.09504 15H16.0373C16.8614 15 17.3895 14.1901 17.0032 13.5183L9.53101 0.535238Z" fill="#FFC97A" />
                            <path d="M8.40443 5.47978L8.60442 9.73045L8.80406 5.48152C8.8053 5.45436 8.80098 5.42723 8.79137 5.40179C8.78176 5.37635 8.76707 5.35314 8.74819 5.33358C8.72931 5.31401 8.70664 5.2985 8.68156 5.28799C8.65648 5.27749 8.62952 5.27221 8.60233 5.27247C8.57562 5.27273 8.54923 5.27834 8.52472 5.28897C8.50021 5.2996 8.47808 5.31503 8.45963 5.33436C8.44118 5.35368 8.42679 5.3765 8.41731 5.40148C8.40783 5.42646 8.40345 5.45308 8.40443 5.47978Z" fill="#3B3731" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M8.4707 11.1465C8.60269 11.1202 8.7399 11.1341 8.86426 11.1855C8.98873 11.2371 9.09605 11.3245 9.1709 11.4365C9.24567 11.5485 9.28516 11.6808 9.28516 11.8154C9.28504 11.9959 9.21359 12.1692 9.08594 12.2969C8.95829 12.4245 8.785 12.496 8.60449 12.4961C8.46984 12.4961 8.33757 12.4566 8.22559 12.3818C8.11356 12.307 8.02617 12.1997 7.97461 12.0752C7.9232 11.9508 7.90929 11.8136 7.93555 11.6816C7.96188 11.5496 8.02689 11.4282 8.12207 11.333C8.21725 11.2378 8.3387 11.1728 8.4707 11.1465Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.03125" />
                        </svg>
                    </span>
                    <span class="admin-co-bk-meta-label">Booking ID</span>
                    <span class="admin-co-bk-meta-value" x-text="selectedBooking.id"></span>
                </p>
                <div class="admin-co-bk-card-head-right">
                    <p class="admin-co-bk-meta">
                        <span class="admin-co-bk-meta-label">Date</span>
                        <span class="admin-co-bk-meta-value" x-text="selectedBooking.date"></span>
                    </p>
                </div>
            </div>
            <div class="admin-co-bk-card-body">
                <div class="admin-co-bk-col admin-co-bk-col--pet">
                    <div class="admin-co-bk-pet">
                        <div class="admin-co-bk-pet-stack" aria-hidden="true">
                            <template x-for="(pet, idx) in (selectedBooking.pets || [])" :key="idx">
                                <img :src="pet.image" alt="" class="admin-co-bk-pet-avatar" width="36" height="36">
                            </template>
                        </div>
                        <div class="admin-co-bk-field">
                            <span class="admin-co-bk-field-label">Pet</span>
                            <span class="admin-co-bk-field-value" x-text="petLabel(selectedBooking)"></span>
                        </div>
                    </div>
                </div>
                <div class="admin-co-bk-col">
                    <div class="admin-co-bk-field">
                        <span class="admin-co-bk-field-label">Service</span>
                        <span class="admin-co-bk-field-value" x-text="selectedBooking.service"></span>
                    </div>
                </div>
                <div class="admin-co-bk-col admin-co-bk-col--provider">
                    <div class="admin-co-bk-field">
                        <span class="admin-co-bk-field-label" x-text="selectedBooking.provider_role"></span>
                        <span class="admin-co-bk-field-value" x-text="selectedBooking.provider"></span>
                    </div>
                </div>
                <div class="admin-co-bk-col admin-co-bk-col--rating">
                    <div class="admin-co-bk-field">
                        <span class="admin-co-bk-field-label">Rating</span>
                        <span class="admin-co-bk-field-value is-muted" x-show="selectedBooking.rating === null">—</span>
                        <span class="admin-co-pet-stars" x-show="selectedBooking.rating !== null">
                            <template x-for="i in 5" :key="i">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                    <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" :fill="i <= (selectedBooking.rating || 0) ? '#FFC97A' : '#D4D2CF'" />
                                </svg>
                            </template>
                        </span>
                    </div>
                </div>
                <div class="admin-co-bk-col admin-co-bk-col--total">
                    <div class="admin-co-bk-field">
                        <span class="admin-co-bk-field-label">Total</span>
                        <span class="admin-co-bk-total" x-text="selectedBooking.amount"></span>
                    </div>
                    <span class="admin-co-booking-status is-pill" :class="'is-' + selectedBooking.status" x-text="selectedBooking.status_label"></span>
                </div>
            </div>
        </article>
    </template>

    <div class="admin-co-bk-sep" aria-hidden="true"></div>

    {{-- Booking details + Recent activity --}}
    <div class="admin-co-split admin-co-dispute-split">
        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Booking details">
                <button type="button" class="admin-co-link-btn" @click="closeDispute()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                    </svg>
                    View Full Booking
                </button>
            </x-admin.customer.section-header>
            <dl class="admin-co-details">
                <div class="admin-co-details-row">
                    <dt>Booking ID</dt>
                    <dd x-text="selectedBooking?.id"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Service</dt>
                    <dd x-text="selectedBooking?.detail?.service"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Add-ons</dt>
                    <dd x-text="selectedBooking?.detail?.addons"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Pet</dt>
                    <dd x-text="selectedBooking?.detail?.pet"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Groomer</dt>
                    <dd class="admin-co-dispute-link" x-text="selectedBooking?.detail?.groomer"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Date &amp; Time</dt>
                    <dd x-text="selectedBooking?.detail?.datetime"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Total charged</dt>
                    <dd x-text="selectedBooking?.detail?.total || selectedBooking?.amount"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Payment status</dt>
                    <dd class="is-muted" x-text="selectedBooking?.detail?.payment_status"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Groomer payout</dt>
                    <dd class="is-muted" x-text="selectedBooking?.detail?.groomer_payout"></dd>
                </div>
            </dl>
        </section>

        <section class="admin-card admin-co-panel admin-co-activity-panel">
            <x-admin.customer.section-header title="Recent Activity">
                <button type="button" class="admin-co-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                    </svg>
                    Full Log
                </button>
            </x-admin.customer.section-header>
            <ul class="admin-co-activity">
                <template x-for="(item, idx) in (selectedBooking?.detail?.dispute?.timeline || selectedBooking?.detail?.timeline || []).slice(0, 6)" :key="idx">
                    <li class="admin-co-activity-item">
                        <span class="admin-co-activity-dot" :class="'is-' + (item.tone || 'flag')" aria-hidden="true"></span>
                        <div>
                            <p class="admin-co-activity-title" x-text="item.title"></p>
                            <span class="admin-co-activity-time" x-text="item.time"></span>
                        </div>
                    </li>
                </template>
            </ul>
        </section>
    </div>

    {{-- Dispute statements --}}
    <section class="admin-co-dispute-statements">
        <h3 class="admin-co-section-title">Dispute statements</h3>

        <div class="admin-co-dispute-statement-group">
            <p class="admin-co-dispute-eyebrow">Customer statement</p>
            <div class="admin-co-dispute-statement">
                <div class="admin-co-dispute-statement-head">
                    <div class="admin-co-dispute-statement-who">
                        <img
                            class="admin-co-dispute-avatar"
                            :src="selectedBooking?.detail?.dispute?.customer_statement?.avatar || '{{ asset('images/profile_image.png') }}'"
                            alt=""
                            width="36"
                            height="36">
                        <div>
                            <p class="admin-co-dispute-name" x-text="selectedBooking?.detail?.dispute?.customer_statement?.name"></p>
                            <p class="admin-co-dispute-meta" x-text="selectedBooking?.detail?.dispute?.customer_statement?.email"></p>
                        </div>
                    </div>
                    <span class="admin-co-dispute-time" x-text="selectedBooking?.detail?.dispute?.customer_statement?.time"></span>
                </div>
                <p class="admin-co-dispute-text" x-text="selectedBooking?.detail?.dispute?.customer_statement?.text"></p>
            </div>
        </div>

        <div class="admin-co-dispute-statement-group">
            <p class="admin-co-dispute-eyebrow">Groomer statement</p>
            <div class="admin-co-dispute-statement">
                <div class="admin-co-dispute-statement-head">
                    <div class="admin-co-dispute-statement-who">
                        <img
                            class="admin-co-dispute-avatar"
                            :src="selectedBooking?.detail?.dispute?.groomer_statement?.avatar || '{{ asset('images/profile_image.png') }}'"
                            alt=""
                            width="36"
                            height="36">
                        <div>
                            <p class="admin-co-dispute-name" x-text="selectedBooking?.detail?.dispute?.groomer_statement?.name"></p>
                            <p class="admin-co-dispute-meta" x-text="selectedBooking?.detail?.dispute?.groomer_statement?.subtitle"></p>
                        </div>
                    </div>
                    <span class="admin-co-dispute-time" x-text="selectedBooking?.detail?.dispute?.groomer_statement?.time"></span>
                </div>
                <p class="admin-co-dispute-text" x-text="selectedBooking?.detail?.dispute?.groomer_statement?.text"></p>
            </div>
        </div>
    </section>

    {{-- Evidence --}}
    <section class="admin-co-dispute-evidence">
        <h3 class="admin-co-section-title">Evidence submitted</h3>
        <div class="admin-co-split admin-co-dispute-evidence-grid">
            <div class="admin-co-dispute-evidence-col">
                <p class="admin-co-dispute-eyebrow">
                    From customer (<span x-text="(selectedBooking?.detail?.dispute?.evidence_customer || []).length"></span> files)
                </p>
                <template x-for="(file, idx) in (selectedBooking?.detail?.dispute?.evidence_customer || [])" :key="'c'+idx">
                    <div class="admin-co-dispute-file">
                        <span class="admin-co-dispute-file-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M8.09088 10.3738C8.58429 10.3595 9.06068 10.1903 9.45265 9.89031C9.84462 9.59029 10.1323 9.17518 10.275 8.70265C10.3397 8.48735 10.375 8.25853 10.375 8.02206V2.72794C10.375 2.1039 10.1271 1.50542 9.68584 1.06416C9.24458 0.622898 8.6461 0.375 8.02206 0.375H2.72794C2.1039 0.375 1.50542 0.622898 1.06416 1.06416C0.622898 1.50542 0.375 2.1039 0.375 2.72794V8.06324C0.385797 8.68011 0.638454 9.26807 1.07855 9.70046C1.51865 10.1329 2.11097 10.3751 2.72794 10.375H8.02206L8.09088 10.3738ZM10.275 8.70265L10.2232 8.64147L8.77265 6.89088C8.66258 6.75809 8.52467 6.65112 8.36867 6.57756C8.21267 6.50399 8.0424 6.46562 7.86993 6.46517C7.69745 6.46473 7.52699 6.5022 7.37061 6.57496C7.21423 6.64771 7.07575 6.75396 6.965 6.88618L6.19324 7.80735L6.06735 7.96088M0.375588 8.06382L0.479706 7.94559L2.36559 5.69441C2.47601 5.56263 2.61398 5.45664 2.76978 5.38393C2.92558 5.31121 3.09542 5.27352 3.26735 5.27352C3.43929 5.27352 3.60913 5.31121 3.76493 5.38393C3.92073 5.45664 4.05869 5.56263 4.16912 5.69441L6.06735 7.96088L8.03618 10.3115L8.09088 10.3738" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M7.19336 3.04932C7.47335 3.04956 7.7002 3.27708 7.7002 3.55713C7.69995 3.83697 7.4732 4.06372 7.19336 4.06396C6.91331 4.06396 6.68579 3.83712 6.68555 3.55713C6.68555 3.27693 6.91316 3.04932 7.19336 3.04932Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.75" />
                            </svg>
                        </span>
                        <span class="admin-co-dispute-file-name" x-text="file.name"></span>
                        <span class="admin-co-dispute-file-size" x-text="file.size"></span>
                        <button type="button" class="admin-co-dispute-file-dl" aria-label="Download">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M5.26586 8.01036C5.19462 7.9826 5.12469 7.93441 5.05607 7.86579L2.9975 5.808C2.92102 5.73153 2.88121 5.64143 2.87807 5.53772C2.87493 5.434 2.91474 5.33893 2.9975 5.2525C3.08445 5.16555 3.17795 5.12129 3.278 5.11972C3.37857 5.11815 3.47207 5.16084 3.5585 5.24779L5.10714 6.79643V0.392862C5.10714 0.281291 5.14459 0.187791 5.2195 0.112363C5.2944 0.0369341 5.3879 -0.000518391 5.5 5.4187e-06C5.6121 0.000529228 5.7056 0.0379817 5.7805 0.112363C5.8554 0.186744 5.89286 0.280243 5.89286 0.392862V6.79643L7.4415 5.24779C7.51798 5.17131 7.60886 5.13124 7.71414 5.12757C7.81943 5.12391 7.91529 5.16555 8.00171 5.2525C8.08552 5.33893 8.12821 5.43164 8.12979 5.53064C8.13136 5.62964 8.08893 5.7221 8.0025 5.808L5.94393 7.86657C5.87583 7.93467 5.8059 7.9826 5.73414 8.01036C5.6629 8.03812 5.58486 8.052 5.5 8.052C5.41514 8.052 5.33709 8.03812 5.26586 8.01036ZM1.26971 11C0.907762 11 0.605786 10.879 0.363786 10.637C0.121786 10.395 0.00052381 10.0928 0 9.73029V8.21936C0 8.10779 0.0374525 8.01455 0.112357 7.93964C0.187262 7.86474 0.280762 7.82703 0.392857 7.8265C0.504952 7.82598 0.598452 7.86369 0.673357 7.93964C0.748262 8.0156 0.785714 8.10884 0.785714 8.21936V9.73029C0.785714 9.85129 0.836 9.96233 0.936571 10.0634C1.03714 10.1645 1.14793 10.2148 1.26893 10.2143H9.73107C9.85155 10.2143 9.96233 10.164 10.0634 10.0634C10.1645 9.96286 10.2148 9.85181 10.2143 9.73029V8.21936C10.2143 8.10779 10.2517 8.01455 10.3266 7.93964C10.4015 7.86474 10.495 7.82703 10.6071 7.8265C10.7192 7.82598 10.8127 7.86369 10.8876 7.93964C10.9625 8.0156 11 8.10884 11 8.21936V9.73029C11 10.0922 10.879 10.3942 10.637 10.6362C10.395 10.8782 10.0928 10.9995 9.73029 11H1.26971Z" fill="#9C9A97" />
                            </svg>
                        </button>
                    </div>
                </template>
            </div>
            <div class="admin-co-dispute-evidence-col">
                <p class="admin-co-dispute-eyebrow">
                    From groomer (<span x-text="(selectedBooking?.detail?.dispute?.evidence_groomer || []).length"></span> file)
                </p>
                <template x-for="(file, idx) in (selectedBooking?.detail?.dispute?.evidence_groomer || [])" :key="'g'+idx">
                    <div class="admin-co-dispute-file">
                        <span class="admin-co-dispute-file-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M8.09088 10.3738C8.58429 10.3595 9.06068 10.1903 9.45265 9.89031C9.84462 9.59029 10.1323 9.17518 10.275 8.70265C10.3397 8.48735 10.375 8.25853 10.375 8.02206V2.72794C10.375 2.1039 10.1271 1.50542 9.68584 1.06416C9.24458 0.622898 8.6461 0.375 8.02206 0.375H2.72794C2.1039 0.375 1.50542 0.622898 1.06416 1.06416C0.622898 1.50542 0.375 2.1039 0.375 2.72794V8.06324C0.385797 8.68011 0.638454 9.26807 1.07855 9.70046C1.51865 10.1329 2.11097 10.3751 2.72794 10.375H8.02206L8.09088 10.3738ZM10.275 8.70265L10.2232 8.64147L8.77265 6.89088C8.66258 6.75809 8.52467 6.65112 8.36867 6.57756C8.21267 6.50399 8.0424 6.46562 7.86993 6.46517C7.69745 6.46473 7.52699 6.5022 7.37061 6.57496C7.21423 6.64771 7.07575 6.75396 6.965 6.88618L6.19324 7.80735L6.06735 7.96088M0.375588 8.06382L0.479706 7.94559L2.36559 5.69441C2.47601 5.56263 2.61398 5.45664 2.76978 5.38393C2.92558 5.31121 3.09542 5.27352 3.26735 5.27352C3.43929 5.27352 3.60913 5.31121 3.76493 5.38393C3.92073 5.45664 4.05869 5.56263 4.16912 5.69441L6.06735 7.96088L8.03618 10.3115L8.09088 10.3738" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M7.19336 3.04932C7.47335 3.04956 7.7002 3.27708 7.7002 3.55713C7.69995 3.83697 7.4732 4.06372 7.19336 4.06396C6.91331 4.06396 6.68579 3.83712 6.68555 3.55713C6.68555 3.27693 6.91316 3.04932 7.19336 3.04932Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.75" />
                            </svg>
                        </span>
                        <span class="admin-co-dispute-file-name" x-text="file.name"></span>
                        <span class="admin-co-dispute-file-size" x-text="file.size"></span>
                        <button type="button" class="admin-co-dispute-file-dl" aria-label="Download">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M5.26586 8.01036C5.19462 7.9826 5.12469 7.93441 5.05607 7.86579L2.9975 5.808C2.92102 5.73153 2.88121 5.64143 2.87807 5.53772C2.87493 5.434 2.91474 5.33893 2.9975 5.2525C3.08445 5.16555 3.17795 5.12129 3.278 5.11972C3.37857 5.11815 3.47207 5.16084 3.5585 5.24779L5.10714 6.79643V0.392862C5.10714 0.281291 5.14459 0.187791 5.2195 0.112363C5.2944 0.0369341 5.3879 -0.000518391 5.5 5.4187e-06C5.6121 0.000529228 5.7056 0.0379817 5.7805 0.112363C5.8554 0.186744 5.89286 0.280243 5.89286 0.392862V6.79643L7.4415 5.24779C7.51798 5.17131 7.60886 5.13124 7.71414 5.12757C7.81943 5.12391 7.91529 5.16555 8.00171 5.2525C8.08552 5.33893 8.12821 5.43164 8.12979 5.53064C8.13136 5.62964 8.08893 5.7221 8.0025 5.808L5.94393 7.86657C5.87583 7.93467 5.8059 7.9826 5.73414 8.01036C5.6629 8.03812 5.58486 8.052 5.5 8.052C5.41514 8.052 5.33709 8.03812 5.26586 8.01036ZM1.26971 11C0.907762 11 0.605786 10.879 0.363786 10.637C0.121786 10.395 0.00052381 10.0928 0 9.73029V8.21936C0 8.10779 0.0374525 8.01455 0.112357 7.93964C0.187262 7.86474 0.280762 7.82703 0.392857 7.8265C0.504952 7.82598 0.598452 7.86369 0.673357 7.93964C0.748262 8.0156 0.785714 8.10884 0.785714 8.21936V9.73029C0.785714 9.85129 0.836 9.96233 0.936571 10.0634C1.03714 10.1645 1.14793 10.2148 1.26893 10.2143H9.73107C9.85155 10.2143 9.96233 10.164 10.0634 10.0634C10.1645 9.96286 10.2148 9.85181 10.2143 9.73029V8.21936C10.2143 8.10779 10.2517 8.01455 10.3266 7.93964C10.4015 7.86474 10.495 7.82703 10.6071 7.8265C10.7192 7.82598 10.8127 7.86369 10.8876 7.93964C10.9625 8.0156 11 8.10884 11 8.21936V9.73029C11 10.0922 10.879 10.3942 10.637 10.6362C10.395 10.8782 10.0928 10.9995 9.73029 11H1.26971Z" fill="#9C9A97" />
                            </svg>
                        </button>
                    </div>
                </template>
                <button type="button" class="admin-co-dispute-request">+ Request more evidence</button>
            </div>
        </div>
    </section>

    {{-- Resolve dispute --}}
    <section class="admin-co-dispute-resolve">
        <div class="admin-co-dispute-resolve-head">
            <div class="admin-co-dispute-resolve-title-row">
                <span class="admin-co-dispute-resolve-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 15 15" fill="none">
                        <path d="M12.3843 6.65039C12.5579 6.47681 12.8405 6.47689 13.0142 6.65039L13.4927 7.12891C13.6663 7.30252 13.6663 7.58517 13.4927 7.75879L10.5562 10.6982C10.3825 10.8719 10.0999 10.8719 9.92627 10.6982L9.55811 10.3301V10.3271L9.44775 10.2168C9.27426 10.0432 9.27418 9.76049 9.44775 9.58691L12.3843 6.65039ZM6.23975 0.504883C6.39167 0.353231 6.6267 0.334371 6.79932 0.448242L6.86865 0.504883L7.34717 0.986328H7.34814C7.52173 1.15991 7.52166 1.44258 7.34814 1.61621L4.41064 4.55273C4.23703 4.72635 3.95536 4.72635 3.78174 4.55273L3.30225 4.07422C3.12882 3.90058 3.12869 3.61789 3.30225 3.44434L6.23975 0.504883Z" stroke="#FFAF3B" stroke-width="0.75" />
                        <path d="M7.15918 7.76001L1.48438 13.4348C1.23185 13.6873 0.820673 13.6894 0.564453 13.4348C0.311996 13.1839 0.310993 12.7742 0.56543 12.5168L6.24023 6.84106L7.15918 7.76001Z" stroke="#FFAF3B" stroke-width="0.75" />
                        <rect x="8.42676" y="2.26544" width="4.71068" height="4.83869" rx="0.625" transform="rotate(45 8.42676 2.26544)" stroke="#FFAF3B" stroke-width="0.75" />
                        <path d="M13.9294 13.929H7.37451" stroke="#FFAF3B" stroke-width="0.75" stroke-linecap="round" />
                    </svg>
                </span>
                <h3 class="admin-co-section-title mb-0">Resolve Dispute</h3>
            </div>
            <p class="admin-co-dispute-resolve-note">Only available on this page · action is permanent</p>
        </div>
        <div class="admin-co-dispute-resolve-body">
        <p class="admin-co-dispute-resolve-intro">Review all statements and evidence above before resolving. Select an outcome and confirm your decision.</p>

        <p class="admin-co-dispute-step">Step 1 — Select outcome</p>
        <div class="admin-co-dispute-outcomes">
            <button type="button" class="admin-co-dispute-outcome" :class="{ 'is-selected': resolveOutcome === 'customer' }" @click="selectResolveOutcome('customer')">
                <span class="admin-co-dispute-outcome-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                        <path d="M8 8C9.65685 8 11 6.65685 11 5C11 3.34315 9.65685 2 8 2C6.34315 2 5 3.34315 5 5C5 6.65685 6.34315 8 8 8Z" stroke="currentColor" stroke-width="1.25" />
                        <path d="M2.5 13.5C2.5 11.2909 4.79086 9.5 8 9.5C11.2091 9.5 13.5 11.2909 13.5 13.5" stroke="currentColor" stroke-width="1.25" stroke-linecap="round" />
                    </svg>
                </span>
                <span class="admin-co-dispute-outcome-label">Rule for customer</span>
                <span class="admin-co-dispute-outcome-desc">Issue a refund to the customer / withhold groomer payout accordingly.</span>
            </button>
            <button type="button" class="admin-co-dispute-outcome" :class="{ 'is-selected': resolveOutcome === 'groomer' }" @click="selectResolveOutcome('groomer')">
                <span class="admin-co-dispute-outcome-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
                        <path d="M4.65756 10.8634C5.86245 12.0683 8.79289 11.0917 11.2027 8.68156C13.6128 6.27179 14.5894 3.34135 13.3845 2.13647M8.20279 1.31804L8.74815 1.86379M6.29403 3.22719L6.83939 3.77255M4.65718 5.40901L5.20254 5.95437M4.11182 8.1362L4.65718 8.68156M11.2027 0.5L11.748 1.04536M10.6573 3.77293L11.748 4.86365M8.74854 5.68208L9.83926 6.7728M6.56671 7.31816L7.65743 8.40888" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.65703 12.5004C5.10889 12.0485 5.10889 11.3159 4.65703 10.8641C4.20517 10.4122 3.47256 10.4122 3.0207 10.8641L0.838933 13.0458C0.387073 13.4977 0.387073 14.2303 0.838933 14.6822C1.29079 15.134 2.0234 15.134 2.47526 14.6822L4.65703 12.5004Z" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="admin-co-dispute-outcome-label">Rule for groomer</span>
                <span class="admin-co-dispute-outcome-desc">No refund issued / release full groomer payout.</span>
            </button>
            <button type="button" class="admin-co-dispute-outcome" :class="{ 'is-selected': resolveOutcome === 'split' }" @click="selectResolveOutcome('split')">
                <span class="admin-co-dispute-outcome-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 22 22" fill="none">
                        <path d="M20.75 6.86111V4.75C20.75 2.86444 20.75 1.92111 20.1644 1.33556C19.5789 0.75 18.6356 0.75 16.75 0.75H14.6389M19.6389 1.86111L13.5278 7.97222M0.75 6.86111V4.75C0.75 2.86444 0.75 1.92111 1.33556 1.33556C1.92111 0.75 2.86444 0.75 4.75 0.75H6.86111M1.86111 1.86111L8.14667 8.14667C9.43111 9.43111 10.0733 10.0733 10.4111 10.8911C10.75 11.7067 10.75 12.6156 10.75 14.4322V20.75" stroke="#787775" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </span>
                <span class="admin-co-dispute-outcome-label">Split decision</span>
                <span class="admin-co-dispute-outcome-desc">Partial refund to customer / partial payout to groomer.</span>
            </button>
        </div>

        <div class="admin-co-dispute-resolve-sep" aria-hidden="true"></div>

        <p class="admin-co-dispute-step">Step 2 — Confirm financial outcome</p>
        <div class="admin-co-split admin-co-dispute-finance-fields">
            <div class="admin-co-suspend-field">
                <label class="admin-co-suspend-label">Refund to customer <span class="admin-co-suspend-required">*</span></label>
                <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openResolveRefund = false">
                    <button type="button" class="admin-co-dd-trigger" @click="openResolveRefund = !openResolveRefund; openResolvePayout = false; openResolveNotify = false">
                        <span class="admin-co-dd-value" :class="{ 'is-placeholder': !resolveRefund }" x-text="resolveRefund || 'Select refund amount ...'"></span>
                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="admin-co-dd-menu" x-show="openResolveRefund" x-cloak>
                        <button type="button" class="admin-co-dd-option" @click="resolveRefund = 'Full refund - ' + (selectedBooking?.amount || ''); openResolveRefund = false">Full refund - <span x-text="selectedBooking?.amount"></span></button>
                        <button type="button" class="admin-co-dd-option" @click="resolveRefund = 'No refund'; openResolveRefund = false">No refund</button>
                        <button type="button" class="admin-co-dd-option" @click="resolveRefund = 'Partial refund - enter amount'; openResolveRefund = false">Partial refund - enter amount</button>
                    </div>
                </div>
            </div>
            <div class="admin-co-suspend-field">
                <label class="admin-co-suspend-label">Groomer payout <span class="admin-co-suspend-required">*</span></label>
                <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openResolvePayout = false">
                    <button type="button" class="admin-co-dd-trigger" @click="openResolvePayout = !openResolvePayout; openResolveRefund = false; openResolveNotify = false">
                        <span class="admin-co-dd-value" :class="{ 'is-placeholder': !resolvePayout }" x-text="resolvePayout || 'Select payout action ...'"></span>
                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="admin-co-dd-menu" x-show="openResolvePayout" x-cloak>
                        <button type="button" class="admin-co-dd-option" @click="resolvePayout = 'Release full payout - ' + (selectedBooking?.amount || ''); openResolvePayout = false">Release full payout - <span x-text="selectedBooking?.amount"></span></button>
                        <button type="button" class="admin-co-dd-option" @click="resolvePayout = 'Withhold full payout'; openResolvePayout = false">Withhold full payout</button>
                        <button type="button" class="admin-co-dd-option" @click="resolvePayout = 'Release partial payout - enter amount'; openResolvePayout = false">Release partial payout - enter amount</button>
                    </div>
                </div>
            </div>
        </div>

        <p class="admin-co-dispute-step">Financial breakdown — live preview</p>
        <div class="admin-co-dispute-preview-box">
            <dl class="admin-co-details admin-co-dispute-preview">
                <template x-for="(line, idx) in (selectedBooking?.detail?.price_lines || [])" :key="idx">
                    <div class="admin-co-details-row">
                        <dt x-text="line.label"></dt>
                        <dd :class="{ 'admin-co-bk-detail-warn': String(line.value || '').startsWith('-') }" x-text="line.value"></dd>
                    </div>
                </template>
                <div class="admin-co-details-row admin-co-bk-detail-total-row">
                    <dt>Total originally charged</dt>
                    <dd x-text="selectedBooking?.detail?.total || selectedBooking?.amount"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Refund to customer</dt>
                    <dd class="admin-co-bk-detail-warn" x-text="resolveRefundPreview()"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Groomer payout</dt>
                    <dd class="admin-co-dispute-payout-ok" x-text="resolvePayoutPreview()"></dd>
                </div>
            </dl>
            <div class="admin-co-suspend-alert is-danger admin-co-dispute-alloc" x-show="resolveRefund && resolvePayout" x-cloak>
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                    <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p>Full amount of <span x-text="selectedBooking?.amount"></span> has been allocated between refund and payout.</p>
            </div>
        </div>

        <div class="admin-co-dispute-resolve-sep" aria-hidden="true"></div>

        <p class="admin-co-dispute-step">Step 3 — Resolution notes</p>
        <div class="admin-co-suspend-field admin-co-dispute-notes-field">
            <label class="admin-co-suspend-label">Resolution notes — sent to both parties <span class="admin-co-suspend-required">*</span></label>
            <textarea
                class="admin-co-suspend-notes"
                rows="4"
                placeholder="Explain your decision. Both parties will receive this message alongside the financial outcome. Be clear about what evidence you reviewed and why you made this decision ..."
                x-model="resolveNotes"></textarea>
        </div>
        <div class="admin-co-split admin-co-dispute-finance-fields">
            <div class="admin-co-suspend-field">
                <label class="admin-co-suspend-label">Internal admin notes (not sent to parties)</label>
                <input type="text" class="admin-co-dd-trigger admin-co-dispute-input" placeholder="Any context for audit log ..." x-model="resolveInternal">
            </div>
            <div class="admin-co-suspend-field">
                <label class="admin-co-suspend-label">Notification preference</label>
                <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openResolveNotify = false">
                    <button type="button" class="admin-co-dd-trigger" @click="openResolveNotify = !openResolveNotify; openResolveRefund = false; openResolvePayout = false">
                        <span class="admin-co-dd-value" x-text="resolveNotify === 'both' ? 'Notify both parties by email' : (resolveNotify === 'customer' ? 'Notify customer only' : (resolveNotify === 'groomer' ? 'Notify groomer only' : 'Do not notify'))"></span>
                        <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                            <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <div class="admin-co-dd-menu" x-show="openResolveNotify" x-cloak>
                        <button type="button" class="admin-co-dd-option" @click="resolveNotify = 'both'; openResolveNotify = false">Notify both parties by email</button>
                        <button type="button" class="admin-co-dd-option" @click="resolveNotify = 'customer'; openResolveNotify = false">Notify customer only</button>
                        <button type="button" class="admin-co-dd-option" @click="resolveNotify = 'groomer'; openResolveNotify = false">Notify groomer only</button>
                        <button type="button" class="admin-co-dd-option" @click="resolveNotify = 'none'; openResolveNotify = false">Do not notify</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="admin-co-blocked-modal-foot is-confirm admin-co-dispute-resolve-foot">
            <button type="button" class="admin-co-form-btn is-cancel" @click="closeDispute()">Cancel</button>
            <button type="button" class="admin-co-form-btn is-send-email" :disabled="!resolveReady()" @click="resolveReady() && closeDispute()">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                    <path d="M12.75 1.25L6.25 7.75M12.75 1.25L8.5 12.75L6.25 7.75M12.75 1.25L1.25 5.5L6.25 7.75" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Confirm resolution
            </button>
        </div>
        </div>
    </section>
</div>