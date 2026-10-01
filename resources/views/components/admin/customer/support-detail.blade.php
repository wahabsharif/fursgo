@props(['profile'])

@php
$customerName = $profile['name'] ?? 'Jane Doe';
$customerAvatar = $profile['avatar'] ?? asset('images/profile_image.png');
$firstName = explode(' ', trim($customerName))[0] ?: $customerName;
@endphp

<div class="admin-co-support-detail" x-show="selectedTicket" x-cloak>
    <div class="admin-co-overview-head">
        <div class="admin-co-bk-detail-title-row">
            <button type="button" class="admin-co-bk-detail-back" @click="closeTicket()" aria-label="Back to support tickets">
                <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                    <g filter="url(#filter0_d_support_detail_back)">
                        <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
                    </g>
                    <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <defs>
                        <filter id="filter0_d_support_detail_back" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                            <feOffset dy="4" />
                            <feGaussianBlur stdDeviation="2" />
                            <feComposite in2="hardAlpha" operator="out" />
                            <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_support_detail_back" />
                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_support_detail_back" result="shape" />
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
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2 3.25H10V9.5C10 9.77614 9.77614 10 9.5 10H2.5C2.22386 10 2 9.77614 2 9.5V3.25Z" stroke="#9C9A97" stroke-width="0.9" />
                            <path d="M4 2V4M8 2V4M2 5.5H10" stroke="#9C9A97" stroke-width="0.9" stroke-linecap="round" />
                        </svg>
                        <span x-text="selectedTicket.ticket_id"></span>
                    </div>
                    <div class="admin-co-support-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <rect x="1.25" y="2" width="9.5" height="8.5" rx="1.5" stroke="#9C9A97" stroke-width="0.9" />
                            <path d="M1.25 5H10.75M4 1.25V3M8 1.25V3" stroke="#9C9A97" stroke-width="0.9" stroke-linecap="round" />
                        </svg>
                        <span>Opened <span x-text="selectedTicket.opened"></span></span>
                    </div>
                    <div class="admin-co-support-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <circle cx="6" cy="3.5" r="2.25" stroke="#9C9A97" stroke-width="0.9" />
                            <path d="M1.5 10.5C1.5 8.3 3.3 6.75 6 6.75C8.7 6.75 10.5 8.3 10.5 10.5" stroke="#9C9A97" stroke-width="0.9" stroke-linecap="round" />
                        </svg>
                        <span>Assigned: <span x-text="selectedTicket.assigned"></span></span>
                    </div>
                    <div class="admin-co-support-meta-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none" aria-hidden="true">
                            <path d="M2 3.5H7.5L10 6V9.5H2V3.5Z" stroke="#9C9A97" stroke-width="0.9" stroke-linejoin="round" />
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
                <h3 class="admin-co-section-title">Original submission request</h3>
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
                            <p class="admin-co-support-field-value is-link" x-text="selectedTicket.detail?.booking_ref"></p>
                        </div>
                        <div class="admin-co-support-field">
                            <p class="admin-co-support-field-label">Attachments</p>
                            <div class="admin-co-support-attachments">
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
                        </div>
                    </div>
                    <div class="admin-co-support-submission-desc">
                        <div class="admin-co-support-desc-head">
                            <p class="admin-co-support-field-label">Description</p>
                            <p class="admin-co-support-desc-meta" x-text="selectedTicket.detail?.submitted_meta"></p>
                        </div>
                        <div class="admin-co-support-desc-box">
                            <p class="admin-co-support-desc-text" x-text="selectedTicket.detail?.description"></p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Linked records --}}
            <section class="admin-card admin-co-panel admin-co-support-linked">
                <div class="admin-co-section-head">
                    <h3 class="admin-co-section-title">Linked records · 3 linked</h3>
                    <button type="button" class="admin-co-link-btn" @click="openLinkModal()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                            <path d="M3.62566 7.37611L7.37571 3.62561M4.87567 1.75036L5.16505 1.41531C5.75119 0.829189 6.54613 0.499941 7.37499 0.5C8.20385 0.500059 8.99874 0.829419 9.5848 1.41563C10.1709 2.00183 10.5001 2.79687 10.5 3.62583C10.4999 4.45479 10.1706 5.24978 9.58448 5.8359L9.25073 6.12594M6.12569 9.25136L5.87756 9.58515C5.28443 10.1713 4.48419 10.5 3.65035 10.5C2.8165 10.5 2.01626 10.1713 1.42313 9.58515C1.1307 9.2962 0.89852 8.95207 0.740058 8.57271C0.581596 8.19335 0.5 7.7863 0.5 7.37517C0.5 6.96403 0.581596 6.55699 0.740058 6.17763C0.89852 5.79827 1.1307 5.45414 1.42313 5.16519L1.75063 4.87577" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Add link
                    </button>
                </div>

                <div class="admin-co-support-linked-block">
                    <p class="admin-co-support-field-label">Booking</p>
                    <div class="admin-co-support-linked-card is-booking">
                        <span class="admin-co-support-linked-icon is-booking" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                <rect width="40" height="40" rx="20" fill="#FFC97A" />
                                <path d="M17.6368 22.3634C18.8417 23.5683 21.7721 22.5917 24.1819 20.1816C26.5921 17.7718 27.5686 14.8413 26.3637 13.6365M21.182 12.818L21.7274 13.3638L21.182 12.818ZM19.2733 14.7272L19.8186 15.2725L19.2733 14.7272ZM17.6364 16.909L18.1818 17.4544L17.6364 16.909ZM17.0911 19.6362L17.6364 20.1816L17.0911 19.6362ZM24.1819 12L24.7273 12.5454L24.1819 12ZM23.6365 15.2729L24.7273 16.3637L23.6365 15.2729ZM21.7278 17.1821L22.8185 18.2728L21.7278 17.1821ZM19.546 18.8182L20.6367 19.9089L19.546 18.8182Z" fill="#FFC97A" />
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
                            <img class="admin-co-support-chat-avatar" :src="selectedTicket.detail?.linked?.chat?.avatar || '{{ $customerAvatar }}'" alt="" width="36" height="36">
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
                            <p class="admin-co-support-chat-preview-text" x-text="selectedTicket.detail?.linked?.chat?.preview"></p>
                            <span class="admin-co-support-chat-preview-time" x-text="selectedTicket.detail?.linked?.chat?.preview_time"></span>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Conversation thread --}}
            <section class="admin-card admin-co-panel admin-co-support-thread">
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
                                <img class="admin-co-support-msg-avatar" :src="msg.avatar || '{{ $customerAvatar }}'" alt="" width="32" height="32">
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
                            :placeholder="'Write your reply to {{ $customerName }} - they will receive this by email at ' + (selectedTicket.detail?.reply_to || '') + ' ...'"></textarea>
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
                        <label class="admin-co-note-compose-label" for="co-support-note-{{ $profile['id'] ?? 'detail' }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                        <textarea
                            id="co-support-note-{{ $profile['id'] ?? 'detail' }}"
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

{{-- Add linked record modal --}}
<template x-teleport="body">
    <div
        class="admin-co-modal-backdrop"
        :class="{ 'is-open': linkModalOpen }"
        @click.self="closeLinkModal()"
        @keydown.escape.window="linkModalOpen && closeLinkModal()">
        <div class="admin-co-modal admin-co-support-link-modal" role="dialog" aria-modal="true" aria-labelledby="support-link-modal-title" @click.stop>
            <div class="admin-co-modal-head admin-co-blocked-modal-head">
                <div>
                    <div class="admin-co-modal-title-row">
                        <span class="admin-co-verify-email-icon is-archive" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path d="M3.62566 7.37611L7.37571 3.62561M4.87567 1.75036L5.16505 1.41531C5.75119 0.829189 6.54613 0.499941 7.37499 0.5C8.20385 0.500059 8.99874 0.829419 9.5848 1.41563C10.1709 2.00183 10.5001 2.79687 10.5 3.62583C10.4999 4.45479 10.1706 5.24978 9.58448 5.8359L9.25073 6.12594M6.12569 9.25136L5.87756 9.58515C5.28443 10.1713 4.48419 10.5 3.65035 10.5C2.8165 10.5 2.01626 10.1713 1.42313 9.58515C1.1307 9.2962 0.89852 8.95207 0.740058 8.57271C0.581596 8.19335 0.5 7.7863 0.5 7.37517C0.5 6.96403 0.581596 6.55699 0.740058 6.17763C0.89852 5.79827 1.1307 5.45414 1.42313 5.16519L1.75063 4.87577" stroke="#649FC9" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="admin-co-modal-title" id="support-link-modal-title">Add linked record</h3>
                            <p class="admin-co-modal-sub">
                                <span x-text="selectedTicket?.ticket_id"></span> · {{ $customerName }}
                            </p>
                        </div>
                    </div>
                </div>
                <button type="button" class="admin-co-modal-close" @click="closeLinkModal()" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                        <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>

            <div class="admin-co-blocked-modal-body admin-co-support-link-body">
                <p class="admin-co-support-link-intro">Select the type of record to link, then search for it below.</p>

                <div class="admin-co-support-link-types" role="tablist" aria-label="Record type">
                    <button
                        type="button"
                        class="admin-co-support-link-type"
                        :class="{ 'is-active': linkRecordType === 'booking' }"
                        @click="setLinkRecordType('booking')">
                        <span class="admin-co-support-link-type-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="21" viewBox="0 0 23 21" fill="none">
                                <path d="M8.06614 13.8928L10.1927 16.0193L14.3002 11.9118C14.4932 11.7188 14.4938 11.4061 14.3014 11.2124C14.1082 11.0177 13.7935 11.0172 13.5995 11.2111L10.1927 14.618L8.76434 13.1936C8.57133 13.0011 8.25889 13.0013 8.06614 13.1941C7.8732 13.387 7.8732 13.6998 8.06614 13.8928Z" fill="#649FC9"/>
                                <path d="M0.5 10.2438C0.5 6.37622 0.5 4.4419 1.75569 3.2409C3.01138 2.0399 5.03099 2.03888 9.07127 2.03888H13.3569C17.3972 2.03888 19.4179 2.03888 20.6725 3.2409C21.9271 4.44293 21.9282 6.37622 21.9282 10.2438V12.2951C21.9282 16.1627 21.9282 18.097 20.6725 19.298C19.4168 20.499 17.3972 20.5 13.3569 20.5H9.07127C5.03099 20.5 3.01031 20.5 1.75569 19.298C0.501071 18.0959 0.5 16.1627 0.5 12.2951V10.2438Z" stroke="#649FC9"/>
                                <path d="M5.69515 2.03108V0.5M16.7224 2.03108V0.5M0.73291 7.13468H21.6846" stroke="#649FC9" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="admin-co-support-link-type-copy">
                            <span class="admin-co-support-link-type-title">Booking</span>
                            <span class="admin-co-support-link-type-sub">Link a booking</span>
                        </span>
                    </button>
                    <button
                        type="button"
                        class="admin-co-support-link-type"
                        :class="{ 'is-active': linkRecordType === 'payment' }"
                        @click="setLinkRecordType('payment')">
                        <span class="admin-co-support-link-type-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="21" viewBox="0 0 19 21" fill="none">
                                <path d="M3.35714 10.5H10.5M3.35714 13.3571H13.3571M3.35714 16.2143H7.64286M17.6429 17.6429V7.64286L10.5 0.5H3.35714C2.59938 0.5 1.87266 0.801019 1.33684 1.33684C0.801019 1.87266 0.5 2.59938 0.5 3.35714V17.6429C0.5 18.4006 0.801019 19.1273 1.33684 19.6632C1.87266 20.199 2.59938 20.5 3.35714 20.5H14.7857C15.5435 20.5 16.2702 20.199 16.806 19.6632C17.3418 19.1273 17.6429 18.4006 17.6429 17.6429Z" stroke="#787775" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M10.5 0.5V4.78571C10.5 5.54348 10.801 6.2702 11.3368 6.80602C11.8727 7.34184 12.5994 7.64286 13.3571 7.64286H17.6429" stroke="#787775" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <span class="admin-co-support-link-type-copy">
                            <span class="admin-co-support-link-type-title">Payment</span>
                            <span class="admin-co-support-link-type-sub">Link an invoice</span>
                        </span>
                    </button>
                    <button
                        type="button"
                        class="admin-co-support-link-type"
                        :class="{ 'is-active': linkRecordType === 'dispute' }"
                        @click="setLinkRecordType('dispute')">
                        <span class="admin-co-support-link-type-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M17.6677 9.47559C17.9296 9.21402 18.3551 9.21391 18.6169 9.47559L19.3015 10.1602C19.5631 10.4222 19.5634 10.8485 19.3015 11.1104H19.3005L15.1052 15.3096C14.8433 15.5712 14.4179 15.5712 14.156 15.3096L13.6179 14.7715V14.7686L13.4714 14.6221C13.2096 14.3602 13.2098 13.9339 13.4714 13.6719L17.6677 9.47559ZM8.88843 0.696289C9.11759 0.467106 9.47258 0.43854 9.73315 0.610352L9.83862 0.696289L10.5212 1.38281L10.5222 1.38379C10.7842 1.64579 10.7842 2.07199 10.5222 2.33398L6.3269 6.53027C6.06504 6.79191 5.63958 6.79185 5.37769 6.53027L4.69312 5.8457C4.43136 5.58369 4.43121 5.15743 4.69312 4.89551L8.88843 0.696289Z" stroke="#787775"/>
                                <path d="M10.2773 11.0864L2.14551 19.2183C1.77086 19.5929 1.16107 19.5947 0.78125 19.2173C0.406148 18.8442 0.405411 18.2352 0.78418 17.854L0.783203 17.853L8.91406 9.72314L10.2773 11.0864Z" stroke="#787775"/>
                                <rect y="0.707139" width="6.80009" height="6.98294" rx="0.5" transform="matrix(0.707075 0.707139 -0.707075 0.707139 12.5388 2.68622)" stroke="#787775"/>
                            </svg>
                        </span>
                        <span class="admin-co-support-link-type-copy">
                            <span class="admin-co-support-link-type-title">Dispute</span>
                            <span class="admin-co-support-link-type-sub">Link a dispute</span>
                        </span>
                    </button>
                    <button
                        type="button"
                        class="admin-co-support-link-type"
                        :class="{ 'is-active': linkRecordType === 'chat' }"
                        @click="setLinkRecordType('chat')">
                        <span class="admin-co-support-link-type-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="26" height="21" viewBox="0 0 26 21" fill="none">
                                <path d="M0.5 10.5C0.5 5.78625 0.5 3.42875 1.965 1.965C3.43 0.50125 5.78625 0.5 10.5 0.5H15.5C20.2137 0.5 22.5712 0.5 24.035 1.965C25.4987 3.43 25.5 5.78625 25.5 10.5C25.5 15.2137 25.5 17.5712 24.035 19.035C22.57 20.4987 20.2137 20.5 15.5 20.5H10.5C5.78625 20.5 3.42875 20.5 1.965 19.035C0.50125 17.57 0.5 15.2137 0.5 10.5Z" stroke="#787775"/>
                                <path d="M5.5 5.5L8.19875 7.75C10.495 9.6625 11.6425 10.6188 13 10.6188C14.3575 10.6188 15.5062 9.6625 17.8012 7.74875L20.5 5.5" stroke="#787775" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <span class="admin-co-support-link-type-copy">
                            <span class="admin-co-support-link-type-title">Chat thread</span>
                            <span class="admin-co-support-link-type-sub">Link a chat</span>
                        </span>
                    </button>
                </div>

                <div class="admin-co-support-link-search-wrap">
                    <label class="admin-co-support-link-search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                            <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731"/>
                        </svg>
                        <input
                            type="search"
                            x-model="linkSearch"
                            :placeholder="'Search ' + linkTypeLabel + ' for {{ $customerName }}'"
                            aria-label="Search linked records" />
                    </label>
                    <div class="admin-co-support-link-id" x-show="selectedLinkResult" x-cloak>
                        <span x-text="selectedLinkResult?.code || selectedLinkResult?.title"></span>
                    </div>
                </div>

                <div class="admin-co-support-link-results" x-show="filteredLinkResults.length" x-cloak>
                    <p class="admin-co-support-link-results-label">
                        Results — <span x-text="filteredLinkResults.length"></span>
                        <span x-text="linkTypeLabel"></span> found
                    </p>
                    <div class="admin-co-support-link-result-list">
                        <template x-for="row in filteredLinkResults" :key="row.id">
                            <button
                                type="button"
                                class="admin-co-support-link-result"
                                :class="{ 'is-selected': linkSelectedId === row.id }"
                                @click="linkSelectedId = row.id">
                                <span class="admin-co-support-link-result-icon" :class="'is-' + row.icon" aria-hidden="true">
                                    <template x-if="row.icon === 'disputed' || row.icon === 'groom'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                            <rect width="40" height="40" rx="20" fill="white"/>
                                            <path d="M17.6368 22.3634C18.8417 23.5683 21.7721 22.5917 24.1819 20.1816C26.5921 17.7718 27.5686 14.8413 26.3637 13.6365M21.182 12.818L21.7274 13.3638M19.2733 14.7272L19.8186 15.2725M17.6364 16.909L18.1818 17.4544M17.0911 19.6362L17.6364 20.1816M24.1819 12L24.7273 12.5454M23.6365 15.2729L24.7273 16.3637M21.7278 17.1821L22.8185 18.2728M19.546 18.8182L20.6367 19.9089" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M17.6363 24.0004C18.0881 23.5485 18.0881 22.8159 17.6363 22.3641C17.1844 21.9122 16.4518 21.9122 16 22.3641L13.8182 24.5458C13.3663 24.9977 13.3663 25.7303 13.8182 26.1822C14.27 26.634 15.0026 26.634 15.4545 26.1822L17.6363 24.0004Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                        </svg>
                                    </template>
                                    <template x-if="row.icon === 'completed' || row.icon === 'space'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                            <rect width="40" height="40" rx="20" fill="#F3F2F2"/>
                                            <path d="M25.9582 26.0858V17.1408C25.9582 17.1194 25.96 17.0983 25.9633 17.0778L23.5448 15.0154C23.0304 14.5775 22.6751 14.2756 22.3737 14.0788C22.0826 13.8889 21.887 13.8281 21.6999 13.8281C21.5129 13.8281 21.3186 13.8891 21.0278 14.0788C20.7263 14.2756 20.37 14.5772 19.855 15.0154L17.4348 17.0778C17.4382 17.0984 17.4416 17.1193 17.4416 17.1408V26.0858C17.4413 26.3142 17.2469 26.4999 17.0071 26.4999C16.7676 26.4998 16.573 26.3141 16.5727 26.0858V17.8121L16.1246 18.1955C15.9457 18.3479 15.6702 18.332 15.5102 18.1615C15.3509 17.9911 15.3658 17.7299 15.5442 17.5776L19.2746 14.3991H19.2763C19.7741 13.9756 20.1772 13.6303 20.5356 13.3962C20.9049 13.1552 21.2715 12.9999 21.6999 12.9999C22.1283 12.9999 22.4948 13.1552 22.8642 13.3962C23.2228 13.6304 23.6276 13.9754 24.1252 14.3991L27.8556 17.5776C28.034 17.7299 28.0489 17.9911 27.8896 18.1615C27.7296 18.332 27.4541 18.3479 27.2752 18.1955L26.8271 17.8121V26.0858C26.8268 26.3141 26.6322 26.4998 26.3927 26.4999C26.1529 26.4999 25.9585 26.3142 25.9582 26.0858Z" fill="#3B3731"/>
                                            <path d="M13.7702 20.2008C13.7702 19.8884 13.6813 19.6204 13.5549 19.4395C13.4284 19.2587 13.2815 19.1827 13.15 19.1827C13.0186 19.1828 12.8716 19.2588 12.7452 19.4395C12.6189 19.6204 12.5299 19.8886 12.5299 20.2008C12.53 20.5131 12.6187 20.7812 12.7452 20.9621C12.8715 21.1426 13.0187 21.2172 13.15 21.2173C13.2814 21.2173 13.4285 21.1426 13.5549 20.9621C13.6814 20.7812 13.77 20.5131 13.7702 20.2008ZM14.5 20.2008C14.4999 20.6661 14.3693 21.1026 14.1394 21.4314C13.9092 21.7604 13.5628 22 13.15 22C12.7376 21.9999 12.3922 21.7602 12.1621 21.4314C11.9322 21.1026 11.8002 20.6661 11.8 20.2008C11.8 19.7353 11.9321 19.2976 12.1621 18.9687C12.3922 18.64 12.7377 18.4001 13.15 18.4C13.5627 18.4 13.9092 18.6397 14.1394 18.9687C14.3695 19.2976 14.5 19.7353 14.5 20.2008Z" fill="#3B3731"/>
                                            <path d="M12.7 26.0781V21.5218C12.7 21.2888 12.9014 21.1 13.15 21.1C13.3985 21.1 13.6 21.2888 13.6 21.5218V26.0781C13.5998 26.311 13.3984 26.5 13.15 26.5C12.9015 26.5 12.7001 26.311 12.7 26.0781Z" fill="#3B3731"/>
                                            <path d="M23.3106 23.0588C23.3106 22.6913 23.309 22.458 23.2856 22.287C23.2639 22.1285 23.2307 22.0873 23.2107 22.0674C23.1906 22.0477 23.149 22.0135 22.9875 21.9921C22.8136 21.9691 22.5754 21.9691 22.2015 21.9691H21.4355C21.0616 21.9691 20.8233 21.9691 20.6494 21.9921C20.488 22.0135 20.4463 22.0477 20.4263 22.0674C20.4062 22.0873 20.373 22.1285 20.3513 22.287C20.328 22.458 20.3264 22.6913 20.3264 23.0588V25.6607H23.3106V23.0588ZM22.5862 18.8641C22.8212 18.8643 23.0121 19.0523 23.0125 19.2836C23.0125 19.5151 22.8215 19.7029 22.5862 19.703H21.0508C20.8155 19.7029 20.6245 19.5151 20.6245 19.2836C20.6248 19.0523 20.8157 18.8643 21.0508 18.8641H22.5862ZM22.5862 16.5997L22.6711 16.6079C22.8657 16.6467 23.0125 16.8162 23.0125 17.0191C23.0125 17.2221 22.8657 17.3916 22.6711 17.4304L22.5862 17.4386H21.0508C20.8155 17.4384 20.6245 17.2507 20.6245 17.0191C20.6245 16.7876 20.8155 16.5998 21.0508 16.5997H22.5862ZM24.1632 25.6607H27.5737C27.8092 25.6607 28 25.8485 28 26.0802C27.9997 26.3116 27.809 26.4997 27.5737 26.4997H12.2264C11.9911 26.4997 11.8004 26.3116 11.8 26.0802C11.8 25.8485 11.9909 25.6607 12.2264 25.6607H19.4737V23.0588C19.4737 22.7152 19.4727 22.4154 19.5054 22.1756C19.5398 21.9235 19.6188 21.6757 19.8234 21.4743C20.0282 21.2728 20.2799 21.1953 20.5362 21.1613C20.7801 21.1291 21.0858 21.1302 21.4355 21.1302H22.2015C22.5511 21.1302 22.8568 21.1291 23.1007 21.1613C23.357 21.1953 23.6087 21.2728 23.8135 21.4743C24.0181 21.6757 24.0971 21.9235 24.1316 22.1756C24.1643 22.4154 24.1632 22.7152 24.1632 23.0588V25.6607Z" fill="#3B3731"/>
                                        </svg>
                                    </template>
                                    <template x-if="row.icon === 'invoice' || row.icon === 'dispute' || row.icon === 'chat'">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                            <rect width="40" height="40" rx="20" fill="#F3F2F2"/>
                                            <path d="M26.3333 27H16.3333C15.4493 27 14.6014 26.6488 13.9763 26.0237C13.3512 25.3986 13 24.5507 13 23.6667V13.6667C13 13.2246 13.1756 12.8007 13.4882 12.4882C13.8007 12.1756 14.2246 12 14.6667 12H23C23.442 12 23.866 12.1756 24.1785 12.4882C24.4911 12.8007 24.6667 13.2246 24.6667 13.6667V24.5C24.6667 25.8808 24.9525 27 26.3333 27Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M28.0001 17.8334C28.0001 17.3914 27.8245 16.9675 27.5119 16.6549C27.1994 16.3423 26.7754 16.1667 26.3334 16.1667H24.6667V24.9167C24.6667 26.0667 25.1834 27.0001 26.3334 27.0001C27.4834 27.0001 28.0001 26.0667 28.0001 24.9167V17.8334Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                            <path d="M21.3333 18.6666H16.3333M21.3333 15.3333H16.3333M18.8333 21.9999H16.3333" stroke="#3B3731" stroke-linecap="round"/>
                                        </svg>
                                    </template>
                                </span>
                                <span class="admin-co-support-link-result-copy">
                                    <span class="admin-co-support-link-result-title" x-text="row.title"></span>
                                    <span class="admin-co-support-link-result-meta" x-text="row.meta"></span>
                                </span>
                                <span class="admin-co-support-link-radio" aria-hidden="true"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="admin-co-support-link-selected" x-show="selectedLinkResult" x-cloak>
                    <p class="admin-co-support-link-selected-label">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="8" viewBox="0 0 11 8" fill="none" aria-hidden="true">
                            <path d="M0.5 4.23333L3.83333 7.5L10.5 0.5" stroke="#FFAF3B" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Selected to link
                    </p>
                    <div class="admin-co-support-link-selected-card" x-show="selectedLinkResult">
                        <span class="admin-co-support-link-result-icon" :class="'is-' + (selectedLinkResult?.icon || 'disputed')" aria-hidden="true">
                            <template x-if="(selectedLinkResult?.icon || 'disputed') === 'disputed' || selectedLinkResult?.icon === 'groom'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="20" fill="white"/>
                                    <path d="M17.6368 22.3634C18.8417 23.5683 21.7721 22.5917 24.1819 20.1816C26.5921 17.7718 27.5686 14.8413 26.3637 13.6365M21.182 12.818L21.7274 13.3638M19.2733 14.7272L19.8186 15.2725M17.6364 16.909L18.1818 17.4544M17.0911 19.6362L17.6364 20.1816M24.1819 12L24.7273 12.5454M23.6365 15.2729L24.7273 16.3637M21.7278 17.1821L22.8185 18.2728M19.546 18.8182L20.6367 19.9089" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M17.6363 24.0004C18.0881 23.5485 18.0881 22.8159 17.6363 22.3641C17.1844 21.9122 16.4518 21.9122 16 22.3641L13.8182 24.5458C13.3663 24.9977 13.3663 25.7303 13.8182 26.1822C14.27 26.634 15.0026 26.634 15.4545 26.1822L17.6363 24.0004Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </template>
                            <template x-if="selectedLinkResult?.icon === 'completed' || selectedLinkResult?.icon === 'space'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="20" fill="#F3F2F2"/>
                                    <path d="M25.9582 26.0858V17.1408C25.9582 17.1194 25.96 17.0983 25.9633 17.0778L23.5448 15.0154C23.0304 14.5775 22.6751 14.2756 22.3737 14.0788C22.0826 13.8889 21.887 13.8281 21.6999 13.8281C21.5129 13.8281 21.3186 13.8891 21.0278 14.0788C20.7263 14.2756 20.37 14.5772 19.855 15.0154L17.4348 17.0778C17.4382 17.0984 17.4416 17.1193 17.4416 17.1408V26.0858C17.4413 26.3142 17.2469 26.4999 17.0071 26.4999C16.7676 26.4998 16.573 26.3141 16.5727 26.0858V17.8121L16.1246 18.1955C15.9457 18.3479 15.6702 18.332 15.5102 18.1615C15.3509 17.9911 15.3658 17.7299 15.5442 17.5776L19.2746 14.3991H19.2763C19.7741 13.9756 20.1772 13.6303 20.5356 13.3962C20.9049 13.1552 21.2715 12.9999 21.6999 12.9999C22.1283 12.9999 22.4948 13.1552 22.8642 13.3962C23.2228 13.6304 23.6276 13.9754 24.1252 14.3991L27.8556 17.5776C28.034 17.7299 28.0489 17.9911 27.8896 18.1615C27.7296 18.332 27.4541 18.3479 27.2752 18.1955L26.8271 17.8121V26.0858C26.8268 26.3141 26.6322 26.4998 26.3927 26.4999C26.1529 26.4999 25.9585 26.3142 25.9582 26.0858Z" fill="#3B3731"/>
                                    <path d="M13.7702 20.2008C13.7702 19.8884 13.6813 19.6204 13.5549 19.4395C13.4284 19.2587 13.2815 19.1827 13.15 19.1827C13.0186 19.1828 12.8716 19.2588 12.7452 19.4395C12.6189 19.6204 12.5299 19.8886 12.5299 20.2008C12.53 20.5131 12.6187 20.7812 12.7452 20.9621C12.8715 21.1426 13.0187 21.2172 13.15 21.2173C13.2814 21.2173 13.4285 21.1426 13.5549 20.9621C13.6814 20.7812 13.77 20.5131 13.7702 20.2008ZM14.5 20.2008C14.4999 20.6661 14.3693 21.1026 14.1394 21.4314C13.9092 21.7604 13.5628 22 13.15 22C12.7376 21.9999 12.3922 21.7602 12.1621 21.4314C11.9322 21.1026 11.8002 20.6661 11.8 20.2008C11.8 19.7353 11.9321 19.2976 12.1621 18.9687C12.3922 18.64 12.7377 18.4001 13.15 18.4C13.5627 18.4 13.9092 18.6397 14.1394 18.9687C14.3695 19.2976 14.5 19.7353 14.5 20.2008Z" fill="#3B3731"/>
                                    <path d="M12.7 26.0781V21.5218C12.7 21.2888 12.9014 21.1 13.15 21.1C13.3985 21.1 13.6 21.2888 13.6 21.5218V26.0781C13.5998 26.311 13.3984 26.5 13.15 26.5C12.9015 26.5 12.7001 26.311 12.7 26.0781Z" fill="#3B3731"/>
                                    <path d="M23.3106 23.0588C23.3106 22.6913 23.309 22.458 23.2856 22.287C23.2639 22.1285 23.2307 22.0873 23.2107 22.0674C23.1906 22.0477 23.149 22.0135 22.9875 21.9921C22.8136 21.9691 22.5754 21.9691 22.2015 21.9691H21.4355C21.0616 21.9691 20.8233 21.9691 20.6494 21.9921C20.488 22.0135 20.4463 22.0477 20.4263 22.0674C20.4062 22.0873 20.373 22.1285 20.3513 22.287C20.328 22.458 20.3264 22.6913 20.3264 23.0588V25.6607H23.3106V23.0588ZM22.5862 18.8641C22.8212 18.8643 23.0121 19.0523 23.0125 19.2836C23.0125 19.5151 22.8215 19.7029 22.5862 19.703H21.0508C20.8155 19.7029 20.6245 19.5151 20.6245 19.2836C20.6248 19.0523 20.8157 18.8643 21.0508 18.8641H22.5862ZM22.5862 16.5997L22.6711 16.6079C22.8657 16.6467 23.0125 16.8162 23.0125 17.0191C23.0125 17.2221 22.8657 17.3916 22.6711 17.4304L22.5862 17.4386H21.0508C20.8155 17.4384 20.6245 17.2507 20.6245 17.0191C20.6245 16.7876 20.8155 16.5998 21.0508 16.5997H22.5862ZM24.1632 25.6607H27.5737C27.8092 25.6607 28 25.8485 28 26.0802C27.9997 26.3116 27.809 26.4997 27.5737 26.4997H12.2264C11.9911 26.4997 11.8004 26.3116 11.8 26.0802C11.8 25.8485 11.9909 25.6607 12.2264 25.6607H19.4737V23.0588C19.4737 22.7152 19.4727 22.4154 19.5054 22.1756C19.5398 21.9235 19.6188 21.6757 19.8234 21.4743C20.0282 21.2728 20.2799 21.1953 20.5362 21.1613C20.7801 21.1291 21.0858 21.1302 21.4355 21.1302H22.2015C22.5511 21.1302 22.8568 21.1291 23.1007 21.1613C23.357 21.1953 23.6087 21.2728 23.8135 21.4743C24.0181 21.6757 24.0971 21.9235 24.1316 22.1756C24.1643 22.4154 24.1632 22.7152 24.1632 23.0588V25.6607Z" fill="#3B3731"/>
                                </svg>
                            </template>
                            <template x-if="selectedLinkResult?.icon === 'invoice' || selectedLinkResult?.icon === 'dispute' || selectedLinkResult?.icon === 'chat'">
                                <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                    <rect width="40" height="40" rx="20" fill="#F3F2F2"/>
                                    <path d="M26.3333 27H16.3333C15.4493 27 14.6014 26.6488 13.9763 26.0237C13.3512 25.3986 13 24.5507 13 23.6667V13.6667C13 13.2246 13.1756 12.8007 13.4882 12.4882C13.8007 12.1756 14.2246 12 14.6667 12H23C23.442 12 23.866 12.1756 24.1785 12.4882C24.4911 12.8007 24.6667 13.2246 24.6667 13.6667V24.5C24.6667 25.8808 24.9525 27 26.3333 27Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M28.0001 17.8334C28.0001 17.3914 27.8245 16.9675 27.5119 16.6549C27.1994 16.3423 26.7754 16.1667 26.3334 16.1667H24.6667V24.9167C24.6667 26.0667 25.1834 27.0001 26.3334 27.0001C27.4834 27.0001 28.0001 26.0667 28.0001 24.9167V17.8334Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M21.3333 18.6666H16.3333M21.3333 15.3333H16.3333M18.8333 21.9999H16.3333" stroke="#3B3731" stroke-linecap="round"/>
                                </svg>
                            </template>
                        </span>
                        <div>
                            <p class="admin-co-support-link-result-title" x-text="selectedLinkResult?.title"></p>
                            <p class="admin-co-support-link-result-meta" x-text="selectedLinkResult?.meta"></p>
                        </div>
                    </div>
                    <div class="admin-co-support-link-note">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#FFAF3B" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#FFAF3B" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <p x-text="selectedLinkResult?.note"></p>
                    </div>
                </div>
            </div>

            <div class="admin-co-blocked-modal-foot is-confirm">
                <button type="button" class="admin-co-form-btn is-cancel" @click="closeLinkModal()">Cancel</button>
                <button
                    type="button"
                    class="admin-btn-dark"
                    :disabled="!linkSelectedId"
                    @click="linkSelectedId && closeLinkModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M3.62566 7.37611L7.37571 3.62561M4.87567 1.75036L5.16505 1.41531C5.75119 0.829189 6.54613 0.499941 7.37499 0.5C8.20385 0.500059 8.99874 0.829419 9.5848 1.41563C10.1709 2.00183 10.5001 2.79687 10.5 3.62583C10.4999 4.45479 10.1706 5.24978 9.58448 5.8359L9.25073 6.12594M6.12569 9.25136L5.87756 9.58515C5.28443 10.1713 4.48419 10.5 3.65035 10.5C2.8165 10.5 2.01626 10.1713 1.42313 9.58515C1.1307 9.2962 0.89852 8.95207 0.740058 8.57271C0.581596 8.19335 0.5 7.7863 0.5 7.37517C0.5 6.96403 0.581596 6.55699 0.740058 6.17763C0.89852 5.79827 1.1307 5.45414 1.42313 5.16519L1.75063 4.87577" stroke="#FDFDFD" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Add link
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Mark as resolved modal --}}
<template x-teleport="body">
    <div
        class="admin-co-modal-backdrop"
        :class="{ 'is-open': resolveModalOpen }"
        @click.self="closeResolveModal()"
        @keydown.escape.window="resolveModalOpen && closeResolveModal()">
        <div class="admin-co-modal admin-co-support-resolve-modal" role="dialog" aria-modal="true" aria-labelledby="support-resolve-modal-title" @click.stop>
            <div class="admin-co-modal-head admin-co-blocked-modal-head">
                <div>
                    <div class="admin-co-modal-title-row">
                        <span class="admin-co-support-resolve-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect width="24" height="24" rx="5" fill="#F4F8EC"/>
                                <path d="M7 12.7333L10.3333 16L17 9" stroke="#A1C25D" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="admin-co-modal-title" id="support-resolve-modal-title">Mark as resolved</h3>
                            <p class="admin-co-modal-sub">
                                <span x-text="selectedTicket?.ticket_id"></span> · {{ $customerName }}
                            </p>
                        </div>
                    </div>
                </div>
                <button type="button" class="admin-co-modal-close" @click="closeResolveModal()" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                        <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>

            <div class="admin-co-blocked-modal-body admin-co-support-resolve-body">
                <div class="admin-co-support-resolve-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#A1C25D" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#A1C25D" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p>Marking as resolved closes the active support ticket and notifies {{ $firstName }} that her ticket has been resolved.</p>
                </div>

                <div class="admin-co-support-resolve-ticket">
                    <span class="admin-co-support-resolve-ticket-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <rect width="40" height="40" rx="20" fill="#F4F8EC"/>
                            <path d="M20.8 15V16.5714M20.8 24.4286V26M20.8 19.7143V21.2857M12 18.1429C12.6365 18.1429 13.247 18.3912 13.6971 18.8332C14.1471 19.2753 14.4 19.8748 14.4 20.5C14.4 21.1252 14.1471 21.7247 13.6971 22.1668C13.247 22.6088 12.6365 22.8571 12 22.8571V24.4286C12 24.8453 12.1686 25.245 12.4686 25.5397C12.7687 25.8344 13.1757 26 13.6 26H26.4C26.8243 26 27.2313 25.8344 27.5314 25.5397C27.8314 25.245 28 24.8453 28 24.4286V22.8571C27.3635 22.8571 26.753 22.6088 26.3029 22.1668C25.8529 21.7247 25.6 21.1252 25.6 20.5C25.6 19.8748 25.8529 19.2753 26.3029 18.8332C26.753 18.3912 27.3635 18.1429 28 18.1429V16.5714C28 16.1547 27.8314 15.755 27.5314 15.4603C27.2313 15.1656 26.8243 15 26.4 15H13.6C13.1757 15 12.7687 15.1656 12.4686 15.4603C12.1686 15.755 12 16.1547 12 16.5714V18.1429Z" stroke="#A1C25D" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="admin-co-support-resolve-ticket-copy">
                        <p class="admin-co-support-resolve-ticket-title">
                            <span x-text="selectedTicket?.ticket_id"></span> · Support ticket
                        </p>
                        <p class="admin-co-support-resolve-ticket-meta">
                            Opened <span x-text="selectedTicket?.opened || '—'"></span>
                            · <span x-text="selectedTicket?.category || 'General'"></span>
                            · Assigned: <span x-text="selectedTicket?.assigned || '—'"></span>
                        </p>
                    </div>
                </div>

                <div class="admin-co-support-resolve-field">
                    <label class="admin-co-support-resolve-label" for="support-resolve-summary">Resolution summary (optional)</label>
                    <textarea
                        id="support-resolve-summary"
                        class="admin-co-support-resolve-notes"
                        rows="4"
                        placeholder="Context for the audit log or to notify customer/hosts ..."
                        x-model="resolveSummary"></textarea>
                </div>

                <div class="admin-co-support-resolve-field">
                    <label class="admin-co-support-resolve-label">
                        Notify customer <span class="admin-co-suspend-required">*</span>
                    </label>
                    <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openResolveNotify = false">
                        <button type="button" class="admin-co-dd-trigger" @click="openResolveNotify = !openResolveNotify">
                            <span
                                class="admin-co-dd-value"
                                x-text="resolveNotify === 'send' ? 'Send resolution email to {{ $firstName }}' : 'Do not send resolution email to {{ $firstName }}'"></span>
                            <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="admin-co-dd-menu" x-show="openResolveNotify" x-cloak>
                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': resolveNotify === 'send' }" @click="resolveNotify = 'send'; openResolveNotify = false">
                                Send resolution email to {{ $firstName }}
                            </button>
                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': resolveNotify === 'none' }" @click="resolveNotify = 'none'; openResolveNotify = false">
                                Do not send resolution email to {{ $firstName }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="admin-co-blocked-modal-foot is-confirm">
                <button type="button" class="admin-co-form-btn is-cancel" @click="closeResolveModal()">Cancel</button>
                <button type="button" class="admin-btn-dark" @click="closeResolveModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="8" viewBox="0 0 11 8" fill="none" aria-hidden="true">
                        <path d="M0.5 4.23333L3.83333 7.5L10.5 0.5" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Mark as resolved
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Open dispute modal --}}
<template x-teleport="body">
    <div
        class="admin-co-modal-backdrop"
        :class="{ 'is-open': disputeModalOpen }"
        @click.self="closeDisputeModal()"
        @keydown.escape.window="disputeModalOpen && closeDisputeModal()">
        <div class="admin-co-modal admin-co-support-dispute-modal" role="dialog" aria-modal="true" aria-labelledby="support-dispute-modal-title" @click.stop>
            <div class="admin-co-modal-head admin-co-blocked-modal-head">
                <div>
                    <div class="admin-co-modal-title-row">
                        <span class="admin-co-support-dispute-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect width="24" height="24" rx="5" fill="#FEF0DC"/>
                                <path d="M16.7417 11.8271C16.8208 11.7481 16.9497 11.7481 17.0288 11.8271L17.439 12.2373C17.5181 12.3164 17.5181 12.4453 17.439 12.5244L14.9214 15.0439C14.8423 15.1229 14.7143 15.1229 14.6353 15.0439L14.3706 14.7793V14.7783L14.2241 14.6318C14.1451 14.5529 14.1454 14.4238 14.2241 14.3447L16.7417 11.8271ZM11.4741 6.55859C11.5433 6.48952 11.6513 6.4813 11.73 6.5332L11.7612 6.55957L12.1714 6.9707L12.1724 6.97168C12.2513 7.05066 12.2511 7.17969 12.1724 7.25879L9.65479 9.77637C9.57572 9.85544 9.44674 9.85544 9.36768 9.77637L8.95752 9.36621C8.87843 9.28712 8.87843 9.1582 8.95752 9.0791L11.4741 6.55859Z" stroke="#FFAF3B"/>
                                <path d="M11.8838 12.6518L7.14648 17.3901C7.00005 17.5366 6.76049 17.5374 6.61035 17.3882C6.46504 17.2438 6.46092 17.0057 6.61035 16.854L6.61133 16.855L11.3486 12.1167L11.8838 12.6518Z" stroke="#FFAF3B"/>
                                <rect y="0.707136" width="3.68006" height="3.78977" rx="0.5" transform="matrix(0.707078 0.707136 -0.707078 0.707136 13.7236 7.69457)" stroke="#FFAF3B"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="admin-co-modal-title" id="support-dispute-modal-title">Open dispute</h3>
                            <p class="admin-co-modal-sub">
                                <span x-text="selectedTicket?.ticket_id"></span> · {{ $customerName }}
                            </p>
                        </div>
                    </div>
                </div>
                <button type="button" class="admin-co-modal-close" @click="closeDisputeModal()" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                        <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>

            <div class="admin-co-blocked-modal-body admin-co-support-dispute-body">
                <div class="admin-co-support-dispute-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#FFAF3B" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#FFAF3B" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p>This will open a formal dispute linked to the booking below. Once created, the dispute is managed from the bookings section.</p>
                </div>

                <div class="admin-co-support-dispute-booking">
                    <span class="admin-co-support-dispute-booking-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <rect width="40" height="40" rx="20" fill="#FFF8EE"/>
                            <path d="M17.6366 22.3634C18.8414 23.5683 21.7719 22.5917 24.1817 20.1816C26.5918 17.7718 27.5684 14.8413 26.3635 13.6365M21.1818 12.818L21.7272 13.3638L21.1818 12.818ZM19.273 14.7272L19.8184 15.2725L19.273 14.7272ZM17.6362 16.909L18.1815 17.4544L17.6362 16.909ZM17.0908 19.6362L17.6362 20.1816L17.0908 19.6362ZM24.1817 12L24.727 12.5454L24.1817 12ZM23.6363 15.2729L24.727 16.3637L23.6363 15.2729ZM21.7275 17.1821L22.8183 18.2728L21.7275 17.1821ZM19.5457 18.8182L20.6364 19.9089L19.5457 18.8182Z" fill="#FFC97A"/>
                            <path d="M17.6366 22.3634C18.8414 23.5683 21.7719 22.5917 24.1817 20.1816C26.5918 17.7718 27.5684 14.8413 26.3635 13.6365M21.1818 12.818L21.7272 13.3638M19.273 14.7272L19.8184 15.2725M17.6362 16.909L18.1815 17.4544M17.0908 19.6362L17.6362 20.1816M24.1817 12L24.727 12.5454M23.6363 15.2729L24.727 16.3637M21.7275 17.1821L22.8183 18.2728M19.5457 18.8182L20.6364 19.9089" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.636 24.0004C18.0879 23.5485 18.0879 22.8159 17.636 22.3641C17.1842 21.9122 16.4516 21.9122 15.9997 22.3641L13.8179 24.5458C13.3661 24.9977 13.3661 25.7303 13.8179 26.1822C14.2698 26.634 15.0024 26.634 15.4543 26.1822L17.636 24.0004Z" fill="#FFC97A" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <div class="admin-co-support-dispute-booking-copy">
                        <p class="admin-co-support-dispute-booking-title" x-text="selectedTicket?.detail?.linked?.booking?.title || 'GS-0563-B12 · Full Groom'"></p>
                        <p class="admin-co-support-dispute-booking-meta" x-text="selectedTicket?.detail?.linked?.booking?.meta || 'Pawfect Salon · 09 Jul 2025 · £55.00 · Confirmed'"></p>
                    </div>
                </div>

                <div class="admin-co-support-dispute-field">
                    <label class="admin-co-support-dispute-label">
                        Dispute category <span class="admin-co-suspend-required">*</span>
                    </label>
                    <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openDisputeCategory = false">
                        <button type="button" class="admin-co-dd-trigger" @click="openDisputeCategory = !openDisputeCategory; openDisputeAssignee = false">
                            <span class="admin-co-dd-value" x-text="disputeCategory"></span>
                            <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="admin-co-dd-menu" x-show="openDisputeCategory" x-cloak>
                            <template x-for="opt in [
                                'No-show by groomer',
                                'Service not delivered as agreed',
                                'Groomer arrived late',
                                'Service quality - below standard',
                                'Animal welfare concern',
                                'Incorrect charges',
                                'Other',
                            ]" :key="opt">
                                <button
                                    type="button"
                                    class="admin-co-dd-option"
                                    :class="{ 'is-active': disputeCategory === opt }"
                                    @click="disputeCategory = opt; openDisputeCategory = false"
                                    x-text="opt"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="admin-co-support-dispute-field">
                    <label class="admin-co-support-dispute-label">Assign dispute to</label>
                    <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openDisputeAssignee = false">
                        <button type="button" class="admin-co-dd-trigger" @click="openDisputeAssignee = !openDisputeAssignee; openDisputeCategory = false">
                            <span class="admin-co-dd-value" x-text="disputeAssignee"></span>
                            <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="admin-co-dd-menu" x-show="openDisputeAssignee" x-cloak>
                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': disputeAssignee === 'Michelle M (me)' }" @click="disputeAssignee = 'Michelle M (me)'; openDisputeAssignee = false">Michelle M (me)</button>
                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': disputeAssignee === 'Ben M' }" @click="disputeAssignee = 'Ben M'; openDisputeAssignee = false">Ben M</button>
                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': disputeAssignee === 'Unassigned' }" @click="disputeAssignee = 'Unassigned'; openDisputeAssignee = false">Unassigned</button>
                        </div>
                    </div>
                </div>

                <div class="admin-co-support-dispute-field">
                    <label class="admin-co-support-dispute-label" for="support-dispute-note">Opening note (optional — internal only)</label>
                    <textarea
                        id="support-dispute-note"
                        class="admin-co-support-dispute-notes"
                        rows="4"
                        placeholder="Any context for the dispute record - e.g. customer has already provided evidence via this support ticket ..."
                        x-model="disputeNote"></textarea>
                </div>

                <div class="admin-co-support-dispute-happens">
                    <h4 class="admin-co-verify-email-happens-title">What happens when you confirm</h4>
                    <ul class="admin-co-verify-email-happens-list">
                        <li>A dispute record is created and linked to this booking and support ticket</li>
                        <li>The groomer is notified and has 7 days to respond with their statement</li>
                        <li>{{ $firstName }} receives an email confirming the dispute has been opened</li>
                        <li>A dispute link is added to this ticket's linked records section</li>
                    </ul>
                </div>
            </div>

            <div class="admin-co-blocked-modal-foot is-confirm">
                <button type="button" class="admin-co-form-btn is-cancel" @click="closeDisputeModal()">Cancel</button>
                <button type="button" class="admin-btn-dark" @click="confirmDispute()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M16.7417 11.8271C16.8208 11.7481 16.9497 11.7481 17.0288 11.8271L17.439 12.2373C17.5181 12.3164 17.5181 12.4453 17.439 12.5244L14.9214 15.0439C14.8423 15.1229 14.7143 15.1229 14.6353 15.0439L14.3706 14.7793V14.7783L14.2241 14.6318C14.1451 14.5529 14.1454 14.4238 14.2241 14.3447L16.7417 11.8271ZM11.4741 6.55859C11.5433 6.48952 11.6513 6.4813 11.73 6.5332L11.7612 6.55957L12.1714 6.9707L12.1724 6.97168C12.2513 7.05066 12.2511 7.17969 12.1724 7.25879L9.65479 9.77637C9.57572 9.85544 9.44674 9.85544 9.36768 9.77637L8.95752 9.36621C8.87843 9.28712 8.87843 9.1582 8.95752 9.0791L11.4741 6.55859Z" stroke="white"/>
                        <path d="M11.8838 12.6518L7.14648 17.3901C7.00005 17.5366 6.76049 17.5374 6.61035 17.3882C6.46504 17.2438 6.46092 17.0057 6.61035 16.854L6.61133 16.855L11.3486 12.1167L11.8838 12.6518Z" stroke="white"/>
                        <rect y="0.707136" width="3.68006" height="3.78977" rx="0.5" transform="matrix(0.707078 0.707136 -0.707078 0.707136 13.7236 7.69457)" stroke="white"/>
                    </svg>
                    Open dispute
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Dispute opened success modal --}}
<template x-teleport="body">
    <div
        class="admin-co-modal-backdrop"
        :class="{ 'is-open': disputeSuccessOpen }"
        @click.self="closeDisputeSuccess()"
        @keydown.escape.window="disputeSuccessOpen && closeDisputeSuccess()">
        <div class="admin-co-modal admin-co-support-dispute-success-modal" role="dialog" aria-modal="true" aria-labelledby="support-dispute-success-title" @click.stop>
            <button type="button" class="admin-co-modal-close admin-co-support-dispute-success-close" @click="closeDisputeSuccess()" aria-label="Close">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                    <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                    <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                </svg>
            </button>

            <div class="admin-co-support-dispute-success-head">
                <span class="admin-co-support-dispute-success-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
                        <rect width="42" height="42" rx="21" fill="#F4F8EC"/>
                        <rect x="11.5" y="11.5" width="19" height="19" rx="9.5" fill="#F4F8EC" stroke="#A7C569"/>
                        <path d="M17.3159 21.1754L19.7721 23.6316L24.6843 18.3684" stroke="#A1C25D" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </span>
                <h3 class="admin-co-support-dispute-success-title" id="support-dispute-success-title">Dispute opened successfully</h3>
                <p class="admin-co-support-dispute-success-copy">The dispute has been created and both parties have been notified. You can manage it from the bookings section.</p>
                <span class="admin-co-support-dispute-success-badge">DS-00063</span>
            </div>

            <div class="admin-co-support-dispute-success-sep is-full" aria-hidden="true"></div>

            <div class="admin-co-support-dispute-success-actions">
                <button type="button" class="admin-co-support-dispute-success-card" @click="closeDisputeSuccess()">
                    <span class="admin-co-support-dispute-success-card-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <rect width="40" height="40" rx="20" fill="#FDE9DF"/>
                            <path d="M20.8 15V16.5714M20.8 24.4286V26M20.8 19.7143V21.2857M12 18.1429C12.6365 18.1429 13.247 18.3912 13.6971 18.8332C14.1471 19.2753 14.4 19.8748 14.4 20.5C14.4 21.1252 14.1471 21.7247 13.6971 22.1668C13.247 22.6088 12.6365 22.8571 12 22.8571V24.4286C12 24.8453 12.1686 25.245 12.4686 25.5397C12.7687 25.8344 13.1757 26 13.6 26H26.4C26.8243 26 27.2313 25.8344 27.5314 25.5397C27.8314 25.245 28 24.8453 28 24.4286V22.8571C27.3635 22.8571 26.753 22.6088 26.3029 22.1668C25.8529 21.7247 25.6 21.1252 25.6 20.5C25.6 19.8748 25.8529 19.2753 26.3029 18.8332C26.753 18.3912 27.3635 18.1429 28 18.1429V16.5714C28 16.1547 27.8314 15.755 27.5314 15.4603C27.2313 15.1656 26.8243 15 26.4 15H13.6C13.1757 15 12.7687 15.1656 12.4686 15.4603C12.1686 15.755 12 16.1547 12 16.5714V18.1429Z" stroke="#FF8C50" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="admin-co-support-dispute-success-card-copy">
                        <span class="admin-co-support-dispute-success-card-title">
                            Stay on ticket <span x-text="selectedTicket?.ticket_id || 'SPRT-00412'"></span>
                        </span>
                        <span class="admin-co-support-dispute-success-card-meta">Continue the conversation · dispute linked</span>
                    </span>
                </button>

                <button type="button" class="admin-co-support-dispute-success-card" @click="closeDisputeSuccess()">
                    <span class="admin-co-support-dispute-success-card-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <rect width="40" height="40" rx="20" fill="#FFF8EE"/>
                            <path d="M26.3394 20.1953C26.487 20.0478 26.7269 20.0477 26.8745 20.1953L27.3872 20.708C27.5164 20.8372 27.5329 21.0376 27.436 21.1846L27.3872 21.2441L24.2407 24.3936C24.0931 24.5411 23.8532 24.5411 23.7056 24.3936L23.3384 24.0264V24.0244L23.1919 23.8779C23.0444 23.7303 23.0444 23.4894 23.1919 23.3418L26.3394 20.1953ZM19.7554 13.6104C19.8846 13.4815 20.0842 13.4657 20.231 13.5625L20.2905 13.6104L20.8022 14.126L20.8032 14.127C20.9508 14.2746 20.9509 14.5144 20.8032 14.6621L17.6567 17.8096C17.5092 17.9568 17.2692 17.9568 17.1216 17.8096L16.6079 17.2959C16.4607 17.1483 16.4607 16.9084 16.6079 16.7607L16.6089 16.7598L19.7554 13.6104Z" stroke="#FFAF3B"/>
                            <path d="M20.5312 21.3148L14.5205 27.3256C14.2886 27.5572 13.911 27.559 13.6748 27.3246C13.443 27.0942 13.4395 26.7157 13.6758 26.4779L19.6855 20.4691L20.5312 21.3148Z" stroke="#FFAF3B"/>
                            <rect y="0.707132" width="4.85011" height="4.98725" rx="0.5" transform="matrix(0.707081 0.707132 -0.707081 0.707132 22.5298 15.0663)" stroke="#FFAF3B"/>
                        </svg>
                    </span>
                    <span class="admin-co-support-dispute-success-card-copy">
                        <span class="admin-co-support-dispute-success-card-title">Go to dispute DSP-00063</span>
                        <span class="admin-co-support-dispute-success-card-meta">Manage dispute details · Bookings section</span>
                    </span>
                </button>

                <button type="button" class="admin-co-support-dispute-success-card" @click="closeDisputeSuccess()">
                    <span class="admin-co-support-dispute-success-card-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <rect width="40" height="40" rx="20" fill="#EAF3F8"/>
                            <path d="M17.6366 22.3634C18.8414 23.5683 21.7719 22.5917 24.1817 20.1816C26.5918 17.7718 27.5684 14.8413 26.3635 13.6365M21.1818 12.818L21.7272 13.3638M19.273 14.7272L19.8184 15.2725M17.6362 16.909L18.1815 17.4544M17.0908 19.6362L17.6362 20.1816M24.1817 12L24.727 12.5454M23.6363 15.2729L24.727 16.3637M21.7275 17.1821L22.8183 18.2728M19.5457 18.8182L20.6364 19.9089" stroke="#649FC9" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M17.636 24.0004C18.0879 23.5485 18.0879 22.8159 17.636 22.3641C17.1842 21.9122 16.4516 21.9122 15.9997 22.3641L13.8179 24.5458C13.3661 24.9977 13.3661 25.7303 13.8179 26.1822C14.2698 26.634 15.0024 26.634 15.4543 26.1822L17.636 24.0004Z" stroke="#649FC9" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </span>
                    <span class="admin-co-support-dispute-success-card-copy">
                        <span class="admin-co-support-dispute-success-card-title">
                            Go to booking <span x-text="selectedTicket?.booking_ref || selectedTicket?.detail?.linked?.booking?.id || 'FG-0563-B12'"></span>
                        </span>
                        <span class="admin-co-support-dispute-success-card-meta">View full booking detail and dispute link</span>
                    </span>
                </button>
            </div>

            <div class="admin-co-support-dispute-success-sep is-full" aria-hidden="true"></div>

            <div class="admin-co-support-dispute-success-foot">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M3.62566 7.37611L7.37571 3.62561M4.87567 1.75036L5.16505 1.41531C5.75119 0.829189 6.54613 0.499941 7.37499 0.5C8.20385 0.500059 8.99874 0.829419 9.5848 1.41563C10.1709 2.00183 10.5001 2.79687 10.5 3.62583C10.4999 4.45479 10.1706 5.24978 9.58448 5.8359L9.25073 6.12594M6.12569 9.25136L5.87756 9.58515C5.28443 10.1713 4.48419 10.5 3.65035 10.5C2.8165 10.5 2.01626 10.1713 1.42313 9.58515C1.1307 9.2962 0.89852 8.95207 0.740058 8.57271C0.581596 8.19335 0.5 7.7863 0.5 7.37517C0.5 6.96403 0.581596 6.55699 0.740058 6.17763C0.89852 5.79827 1.1307 5.45414 1.42313 5.16519L1.75063 4.87577" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <p>
                    DSP-00063 is now visible in
                    <span x-text="selectedTicket?.ticket_id || 'SPRT-00412'"></span>
                    linked records
                </p>
            </div>
        </div>
    </div>
</template>

{{-- Merge with another ticket modal --}}
<template x-teleport="body">
    <div
        class="admin-co-modal-backdrop"
        :class="{ 'is-open': mergeModalOpen }"
        @click.self="closeMergeModal()"
        @keydown.escape.window="mergeModalOpen && closeMergeModal()">
        <div class="admin-co-modal admin-co-support-merge-modal" role="dialog" aria-modal="true" aria-labelledby="support-merge-modal-title" @click.stop>
            <div class="admin-co-modal-head admin-co-blocked-modal-head">
                <div>
                    <div class="admin-co-modal-title-row">
                        <span class="admin-co-support-merge-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect width="24" height="24" rx="5" fill="#FDE9DF"/>
                                <path d="M9.90039 9.1L12.0004 7L14.1004 9.1" stroke="#FF8C50" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M11.9998 7V12.4075C12.0028 12.6871 11.9499 12.9646 11.8443 13.2235C11.7386 13.4824 11.5823 13.7176 11.3845 13.9153L7.7998 17.5M16.1998 17.5L13.5748 14.875" stroke="#FF8C50" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="admin-co-modal-title" id="support-merge-modal-title">Merge with another ticket</h3>
                            <p class="admin-co-modal-sub">
                                <span x-text="selectedTicket?.ticket_id"></span> will be merged into the selected ticket
                            </p>
                        </div>
                    </div>
                </div>
                <button type="button" class="admin-co-modal-close" @click="closeMergeModal()" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                        <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>

            <div class="admin-co-blocked-modal-body admin-co-support-merge-body">
                <div class="admin-co-support-merge-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#FFAF3B" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#FFAF3B" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p>Merging combines two tickets into one. The ticket you select below to merge into becomes the primary ticket.</p>
                </div>

                <div class="admin-co-support-merge-section">
                    <p class="admin-co-support-merge-section-label">This ticket (will be closed)</p>
                    <div class="admin-co-support-merge-closed">
                        <span class="admin-co-support-merge-ticket-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                <rect width="40" height="40" rx="20" fill="#FEE8E4"/>
                                <path d="M20.8 15V16.5714M20.8 24.4286V26M20.8 19.7143V21.2857M12 18.1429C12.6365 18.1429 13.247 18.3912 13.6971 18.8332C14.1471 19.2753 14.4 19.8748 14.4 20.5C14.4 21.1252 14.1471 21.7247 13.6971 22.1668C13.247 22.6088 12.6365 22.8571 12 22.8571V24.4286C12 24.8453 12.1686 25.245 12.4686 25.5397C12.7687 25.8344 13.1757 26 13.6 26H26.4C26.8243 26 27.2313 25.8344 27.5314 25.5397C27.8314 25.245 28 24.8453 28 24.4286V22.8571C27.3635 22.8571 26.753 22.6088 26.3029 22.1668C25.8529 21.7247 25.6 21.1252 25.6 20.5C25.6 19.8748 25.8529 19.2753 26.3029 18.8332C26.753 18.3912 27.3635 18.1429 28 18.1429V16.5714C28 16.1547 27.8314 15.755 27.5314 15.4603C27.2313 15.1656 26.8243 15 26.4 15H13.6C13.1757 15 12.7687 15.1656 12.4686 15.4603C12.1686 15.755 12 16.1547 12 16.5714V18.1429Z" stroke="#FE6F56" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </span>
                        <div class="admin-co-support-merge-ticket-copy">
                            <p class="admin-co-support-merge-ticket-title">
                                <span x-text="selectedTicket?.ticket_id"></span> · Support ticket
                            </p>
                            <p class="admin-co-support-merge-ticket-meta">
                                Opened <span x-text="selectedTicket?.opened || '—'"></span>
                                · <span x-text="selectedTicket?.category || 'General'"></span>
                                · Assigned: <span x-text="selectedTicket?.assigned || '—'"></span>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="admin-co-support-merge-section">
                    <label class="admin-co-support-merge-search" for="support-merge-search">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                            <path d="M10.8761 10.2781L8.23108 7.63361C8.99773 6.7132 9.38002 5.53266 9.29843 4.33757C9.21683 3.14248 8.67764 2.02485 7.79301 1.21718C6.90838 0.409513 5.74642 -0.0260137 4.54886 0.00120289C3.3513 0.0284195 2.21033 0.516284 1.36331 1.36331C0.516284 2.21033 0.0284195 3.3513 0.00120289 4.54886C-0.0260137 5.74642 0.409513 6.90838 1.21718 7.79301C2.02485 8.67764 3.14248 9.21683 4.33757 9.29843C5.53266 9.38002 6.7132 8.99773 7.63361 8.23108L10.2781 10.8761C10.3174 10.9154 10.364 10.9466 10.4153 10.9678C10.4666 10.9891 10.5216 11 10.5771 11C10.6327 11 10.6877 10.9891 10.739 10.9678C10.7903 10.9466 10.8369 10.9154 10.8761 10.8761C10.9154 10.8369 10.9466 10.7903 10.9678 10.739C10.9891 10.6877 11 10.6327 11 10.5771C11 10.5216 10.9891 10.4666 10.9678 10.4153C10.9466 10.364 10.9154 10.3174 10.8761 10.2781ZM0.856914 4.66048C0.856914 3.90821 1.07999 3.17283 1.49793 2.54733C1.91587 1.92184 2.50991 1.43433 3.20492 1.14644C3.89993 0.85856 4.6647 0.783237 5.40252 0.929999C6.14034 1.07676 6.81807 1.43901 7.35001 1.97095C7.88195 2.50289 8.24421 3.18062 8.39097 3.91844C8.53773 4.65626 8.46241 5.42103 8.17452 6.11605C7.88664 6.81106 7.39913 7.40509 6.77363 7.82304C6.14814 8.24098 5.41276 8.46405 4.66048 8.46405C3.65206 8.46293 2.68525 8.06184 1.97219 7.34878C1.25912 6.63571 0.858033 5.66891 0.856914 4.66048Z" fill="#3B3731"/>
                        </svg>
                        Search for ticket to merge into
                    </label>
                    <div class="admin-co-support-merge-search-field">
                        <input
                            id="support-merge-search"
                            type="search"
                            x-model="mergeSearch"
                            placeholder="Search support tickets ..."
                            aria-label="Search for ticket to merge into" />
                    </div>
                </div>

                <div class="admin-co-support-merge-results" x-show="filteredMergeResults.length" x-cloak>
                    <p class="admin-co-support-merge-results-label">
                        Results — <span x-text="filteredMergeResults.length"></span> support tickets found
                    </p>
                    <div class="admin-co-support-merge-result-list">
                        <template x-for="row in filteredMergeResults" :key="row.id">
                            <button
                                type="button"
                                class="admin-co-support-merge-result"
                                :class="{ 'is-selected': mergeSelectedId === row.id }"
                                @click="mergeSelectedId = row.id">
                                <span class="admin-co-support-merge-ticket-icon" aria-hidden="true">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                        <rect width="40" height="40" rx="20" fill="#9F9F9F" fill-opacity="0.1"/>
                                        <path d="M20.8 15V16.5714M20.8 24.4286V26M20.8 19.7143V21.2857M12 18.1429C12.6365 18.1429 13.247 18.3912 13.6971 18.8332C14.1471 19.2753 14.4 19.8748 14.4 20.5C14.4 21.1252 14.1471 21.7247 13.6971 22.1668C13.247 22.6088 12.6365 22.8571 12 22.8571V24.4286C12 24.8453 12.1686 25.245 12.4686 25.5397C12.7687 25.8344 13.1757 26 13.6 26H26.4C26.8243 26 27.2313 25.8344 27.5314 25.5397C27.8314 25.245 28 24.8453 28 24.4286V22.8571C27.3635 22.8571 26.753 22.6088 26.3029 22.1668C25.8529 21.7247 25.6 21.1252 25.6 20.5C25.6 19.8748 25.8529 19.2753 26.3029 18.8332C26.753 18.3912 27.3635 18.1429 28 18.1429V16.5714C28 16.1547 27.8314 15.755 27.5314 15.4603C27.2313 15.1656 26.8243 15 26.4 15H13.6C13.1757 15 12.7687 15.1656 12.4686 15.4603C12.1686 15.755 12 16.1547 12 16.5714V18.1429Z" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </span>
                                <span class="admin-co-support-merge-ticket-copy">
                                    <span class="admin-co-support-merge-ticket-title" x-text="row.title"></span>
                                    <span class="admin-co-support-merge-ticket-meta" x-text="row.meta"></span>
                                </span>
                                <span class="admin-co-support-link-radio" aria-hidden="true"></span>
                            </button>
                        </template>
                    </div>
                </div>

                <div class="admin-co-support-merge-happens">
                    <h4 class="admin-co-verify-email-happens-title">What happens when you confirm</h4>
                    <ul class="admin-co-verify-email-happens-list admin-co-support-merge-happens-list">
                        <li>
                            All messages from <span x-text="selectedTicket?.ticket_id"></span> move into
                            <span x-text="selectedMergeResult?.ticket_id || 'the selected ticket'"></span>
                        </li>
                        <li>{{ $firstName }} receives one combined ticket thread going forward</li>
                        <li>Cannot be undone — messages are permanently moved</li>
                    </ul>
                </div>
            </div>

            <div class="admin-co-blocked-modal-foot is-confirm">
                <button type="button" class="admin-co-form-btn is-cancel" @click="closeMergeModal()">Cancel</button>
                <button type="button" class="admin-btn-dark" :disabled="!mergeSelectedId" @click="mergeSelectedId && closeMergeModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <path d="M9.90039 9.1L12.0004 7L14.1004 9.1" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M11.9998 7V12.4075C12.0028 12.6871 11.9499 12.9646 11.8443 13.2235C11.7386 13.4824 11.5823 13.7176 11.3845 13.9153L7.7998 17.5M16.1998 17.5L13.5748 14.875" stroke="white" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Merge tickets
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Reassign ticket modal --}}
<template x-teleport="body">
    <div
        class="admin-co-modal-backdrop"
        :class="{ 'is-open': reassignModalOpen }"
        @click.self="closeReassignModal()"
        @keydown.escape.window="reassignModalOpen && closeReassignModal()">
        <div class="admin-co-modal admin-co-support-reassign-modal" role="dialog" aria-modal="true" aria-labelledby="support-reassign-modal-title" @click.stop>
            <div class="admin-co-modal-head admin-co-blocked-modal-head">
                <div>
                    <div class="admin-co-modal-title-row">
                        <span class="admin-co-support-reassign-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
                                <rect width="24" height="24" rx="5" fill="#F1F5F8"/>
                                <path d="M11.7207 7.49992C12.9705 7.49992 13.9834 8.51299 13.9834 9.76262C13.9834 11.0122 12.9705 12.0253 11.7207 12.0253C10.471 12.0252 9.45801 11.0122 9.45801 9.76262C9.45801 8.51304 10.471 7.5 11.7207 7.49992Z" stroke="#649FC9"/>
                                <path d="M14.735 13.3566C13.7914 12.3456 12.5278 12.2741 11.7213 12.2741C8.1045 12.2741 7.36774 15.3717 7.45147 17.0461" stroke="#649FC9" stroke-linecap="round"/>
                                <path d="M16.5552 14.8225L14.4455 17.1539C14.3995 17.2046 14.3458 17.2299 14.2845 17.2299C14.2232 17.2299 14.1695 17.2046 14.1235 17.1539L13.1348 16.0642" stroke="#649FC9" stroke-linecap="round"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="admin-co-modal-title" id="support-reassign-modal-title">Reassign ticket</h3>
                            <p class="admin-co-modal-sub">
                                <span x-text="selectedTicket?.ticket_id"></span>
                                · currently assigned to
                                <span x-text="selectedTicket?.assigned || '—'"></span>
                            </p>
                        </div>
                    </div>
                </div>
                <button type="button" class="admin-co-modal-close" @click="closeReassignModal()" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                        <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>

            <div class="admin-co-blocked-modal-body admin-co-support-reassign-body">
                <div class="admin-co-support-reassign-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#649FC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#649FC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p>Select a team member to assign ticket. They will be notified by email. The customer will not be told about reassignments.</p>
                </div>

                <div class="admin-co-support-reassign-list">
                    <template x-for="member in reassignMembers" :key="member.id">
                        <button
                            type="button"
                            class="admin-co-support-reassign-member"
                            :class="{ 'is-selected': reassignMemberId === member.id }"
                            @click="reassignMemberId = member.id">
                            <span class="admin-co-support-link-radio" aria-hidden="true"></span>
                            <span
                                class="admin-co-support-reassign-avatar"
                                :class="'is-' + member.tone"
                                x-text="member.initials"></span>
                            <span class="admin-co-support-reassign-member-copy">
                                <span class="admin-co-support-reassign-member-name" x-text="member.name"></span>
                                <span class="admin-co-support-reassign-member-meta" x-text="member.role + ' · ' + member.meta"></span>
                            </span>
                        </button>
                    </template>
                </div>

                <div class="admin-co-support-reassign-field">
                    <label class="admin-co-support-reassign-label" for="support-reassign-note">Note to assignee (optional)</label>
                    <textarea
                        id="support-reassign-note"
                        class="admin-co-support-reassign-notes"
                        rows="4"
                        placeholder="e.g. Please follow up on Jane's chaser email from 2 days ago ..."
                        x-model="reassignNote"></textarea>
                </div>
            </div>

            <div class="admin-co-blocked-modal-foot is-confirm">
                <button type="button" class="admin-co-form-btn is-cancel" @click="closeReassignModal()">Cancel</button>
                <button type="button" class="admin-btn-dark" :disabled="!reassignMemberId" @click="reassignMemberId && closeReassignModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="11" viewBox="0 0 10 11" fill="none" aria-hidden="true">
                        <path d="M4.67871 0.5C5.89398 0.5 6.87966 1.48503 6.87988 2.7002C6.87988 3.91554 5.89412 4.90137 4.67871 4.90137C3.46352 4.90111 2.47852 3.91539 2.47852 2.7002C2.47874 1.48519 3.46366 0.500256 4.67871 0.5Z" stroke="white"/>
                        <path d="M7.6257 6.21383C6.70333 5.22558 5.46817 5.1557 4.67978 5.1557C1.14436 5.1557 0.424177 8.18365 0.506016 9.82038" stroke="white" stroke-linecap="round"/>
                        <path d="M9.40502 7.64679L7.34273 9.92578C7.29778 9.97532 7.24533 10.0001 7.18539 10.0001C7.12545 10.0001 7.073 9.97532 7.02805 9.92578L6.06152 8.8606" stroke="white" stroke-linecap="round"/>
                    </svg>
                    Reassign ticket
                </button>
            </div>
        </div>
    </div>
</template>

{{-- Close ticket modal --}}
<template x-teleport="body">
    <div
        class="admin-co-modal-backdrop"
        :class="{ 'is-open': closeTicketModalOpen }"
        @click.self="closeCloseTicketModal()"
        @keydown.escape.window="closeTicketModalOpen && closeCloseTicketModal()">
        <div class="admin-co-modal admin-co-support-close-modal" role="dialog" aria-modal="true" aria-labelledby="support-close-modal-title" @click.stop>
            <div class="admin-co-modal-head admin-co-blocked-modal-head">
                <div>
                    <div class="admin-co-modal-title-row">
                        <span class="admin-co-support-close-icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none">
                                <path d="M6.5 12.5C9.81371 12.5 12.5 9.81371 12.5 6.5C12.5 3.18629 9.81371 0.5 6.5 0.5C3.18629 0.5 0.5 3.18629 0.5 6.5C0.5 9.81371 3.18629 12.5 6.5 12.5Z" stroke="#FE6F56"/>
                                <path d="M10.5 10.5L2.5 2.49998" stroke="#FE6F56"/>
                            </svg>
                        </span>
                        <div>
                            <h3 class="admin-co-modal-title" id="support-close-modal-title">Close ticket</h3>
                            <p class="admin-co-modal-sub">This closes the ticket with or without resolution</p>
                        </div>
                    </div>
                </div>
                <button type="button" class="admin-co-modal-close" @click="closeCloseTicketModal()" aria-label="Close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                        <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                    </svg>
                </button>
            </div>

            <div class="admin-co-blocked-modal-body admin-co-support-close-body">
                <div class="admin-co-support-close-info">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#FF6E6E" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p>Closing a ticket without resolution should only be used when the issue cannot be actioned — for example, the customer is unresponsive, the ticket is a duplicate, or it was raised in error. Consider marking as resolved instead if the issue was addressed.</p>
                </div>

                <div class="admin-co-support-close-field">
                    <label class="admin-co-support-close-label">
                        Reason for closing <span class="admin-co-suspend-required">*</span>
                    </label>
                    <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openCloseTicketReason = false">
                        <button type="button" class="admin-co-dd-trigger" @click="openCloseTicketReason = !openCloseTicketReason; openCloseTicketNotify = false">
                            <span
                                class="admin-co-dd-value"
                                :class="{ 'is-placeholder': !closeTicketReason }"
                                x-text="closeTicketReason || 'Select a reason ...'"></span>
                            <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="admin-co-dd-menu" x-show="openCloseTicketReason" x-cloak>
                            <template x-for="opt in [
                                'Customer unresponsive - no reply after follow-up',
                                'Duplicate ticket - merged with another',
                                'Raised in error - not a valid support request',
                                'Issue resolved through another channel',
                                'Spam or test submission',
                                'Other',
                            ]" :key="opt">
                                <button
                                    type="button"
                                    class="admin-co-dd-option"
                                    :class="{ 'is-active': closeTicketReason === opt }"
                                    @click="closeTicketReason = opt; openCloseTicketReason = false"
                                    x-text="opt"></button>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="admin-co-support-close-field">
                    <label class="admin-co-support-close-label" for="support-close-note">Closing note (optional — internal only)</label>
                    <textarea
                        id="support-close-note"
                        class="admin-co-support-close-notes"
                        rows="4"
                        placeholder="Any additional context for the audit log ..."
                        x-model="closeTicketNote"></textarea>
                </div>

                <div class="admin-co-support-close-field">
                    <label class="admin-co-support-close-label">
                        Notify customer <span class="admin-co-suspend-required">*</span>
                    </label>
                    <div class="admin-co-dd admin-co-suspend-dd" @click.outside="openCloseTicketNotify = false">
                        <button type="button" class="admin-co-dd-trigger" @click="openCloseTicketNotify = !openCloseTicketNotify; openCloseTicketReason = false">
                            <span
                                class="admin-co-dd-value"
                                x-text="closeTicketNotify === 'send' ? 'Send closing email to {{ $firstName }}' : 'Do not notify customer'"></span>
                            <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                <path d="M1 1.5L6 6.5L11 1.5" stroke="#9C9A97" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>
                        <div class="admin-co-dd-menu" x-show="openCloseTicketNotify" x-cloak>
                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': closeTicketNotify === 'none' }" @click="closeTicketNotify = 'none'; openCloseTicketNotify = false">
                                Do not notify customer
                            </button>
                            <button type="button" class="admin-co-dd-option" :class="{ 'is-active': closeTicketNotify === 'send' }" @click="closeTicketNotify = 'send'; openCloseTicketNotify = false">
                                Send closing email to {{ $firstName }}
                            </button>
                        </div>
                    </div>
                </div>

                <div class="admin-co-support-close-happens">
                    <h4 class="admin-co-verify-email-happens-title">What happens when closed</h4>
                    <ul class="admin-co-verify-email-happens-list">
                        <li>Ticket status changes to Closed</li>
                        <li>The ticket is archived — no further replies expected</li>
                        <li>A super admin can reopen a closed ticket if needed</li>
                    </ul>
                </div>
            </div>

            <div class="admin-co-blocked-modal-foot is-confirm">
                <button type="button" class="admin-co-form-btn is-cancel" @click="closeCloseTicketModal()">Cancel</button>
                <button
                    type="button"
                    class="admin-btn-dark"
                    :disabled="!closeTicketReason"
                    @click="closeTicketReason && closeCloseTicketModal()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                        <path d="M6.5 12.5C9.81371 12.5 12.5 9.81371 12.5 6.5C12.5 3.18629 9.81371 0.5 6.5 0.5C3.18629 0.5 0.5 3.18629 0.5 6.5C0.5 9.81371 3.18629 12.5 6.5 12.5Z" stroke="white"/>
                        <path d="M10.5 10.5L2.5 2.49998" stroke="white"/>
                    </svg>
                    Close ticket
                </button>
            </div>
        </div>
    </div>
</template>