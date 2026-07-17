@php
    use App\Support\MarketingHubNav;

    $dashboardNav = MarketingHubNav::fromSession();
    $mh = $mh ?? \App\Support\MarketingHubStats::empty();
    $mhPromos = $mhPromos ?? \App\Support\MarketingHubPromos::empty();
    $isSpaceUser = strtolower((string) (auth('groomer_spacer')->user()?->user_type ?? '')) === 'space';
    $serviceColors = ['#FBAC83', '#FDD0B3', '#FFF4E4'];
    $petColors = ['#D8E8B7', 'rgba(216, 232, 183, 0.60)', 'rgba(216, 232, 183, 0.20)'];
    $mhChartData = [
        'peakBookings' => $mh['peak_bookings'],
        'timeLabels' => $mh['time_labels'],
        'services' => $mh['services']['values'],
        'pets' => $isSpaceUser ? [] : $mh['pets']['values'],
    ];
    $showPromoForm = $showPromoForm ?? false;
    $promoServiceOptions = $promoServiceOptions ?? [];
    $promoPetTypeOptions = $promoPetTypeOptions ?? ['Cat', 'Dog', 'Other'];
    $promoPetSizeOptions = $promoPetSizeOptions ?? ['Small 0 - 7 kg', 'Medium 8 - 18 kg', 'Large 19+ kg'];
@endphp

<section class="dashboard-content-wrapper marketing-hub-content">
    <div class="active-section-header" x-cloak x-data="{ navLoading: false, navLoadingTimeout: null }"
        x-on:nav-list-loading-start.window="navLoading = true; if (navLoadingTimeout) { clearTimeout(navLoadingTimeout); navLoadingTimeout = null; } if (!$event.detail?.persistent) { navLoadingTimeout = setTimeout(() => { navLoading = false; navLoadingTimeout = null; }, 350); }"
        x-on:nav-list-loading-end.window="navLoading = false; if (navLoadingTimeout) { clearTimeout(navLoadingTimeout); navLoadingTimeout = null; }">
        <template x-if="activeSection === 'marketing-hub'">
            <div>
                <h2>Marketing Hub</h2>
            </div>
        </template>

        <template x-if="activeSection === 'promo-creation'">
            <div class="mh-promo-header-bar" @if ($showPromoForm) data-form-open="1" @endif>
                @if ($showPromoForm)
                    <button type="button" class="mh-promo-back-btn"
                        @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } }))"
                        wire:click="cancelPromoForm" wire:loading.attr="disabled"
                        wire:target="cancelPromoForm,savePromo">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="11" viewBox="0 0 17 11"
                            fill="none" aria-hidden="true">
                            <path
                                d="M0 5.202L5.211 0L5.877 0.684C6.015 0.828 6.069 0.972 6.039 1.116C6.015 1.254 5.94 1.386 5.814 1.512L3.609 3.708C3.297 4.02 3.012 4.278 2.754 4.482C3.102 4.434 3.468 4.398 3.852 4.374C4.242 4.344 4.635 4.329 5.031 4.329H16.074V6.084H5.031C4.629 6.084 4.233 6.072 3.843 6.048C3.459 6.024 3.093 5.988 2.745 5.94C2.877 6.042 3.012 6.156 3.15 6.282C3.294 6.408 3.447 6.549 3.609 6.705L5.832 8.919C5.958 9.045 6.033 9.18 6.057 9.324C6.087 9.462 6.033 9.6 5.895 9.738L5.229 10.431L0 5.202Z"
                                fill="#3B3731" />
                        </svg>
                        Promotions
                    </button>
                @endif
                <h2>Promo Creation</h2>
            </div>
        </template>

        <div class="active-section-loading-bar" x-cloak x-show="navLoading" wire:loading.class="is-loading"
            wire:target="openCreatePromo,openEditPromo,cancelPromoForm,savePromo" aria-hidden="true">
            <span class="active-section-loading-bar__sweep"></span>
        </div>
    </div>

    <div class="marketing-hub-panels">
        <div x-show="activeSection === 'marketing-hub'" x-cloak x-transition:enter="mh-panel-enter"
            x-transition:enter-start="mh-panel-enter-start" x-transition:enter-end="mh-panel-enter-end"
            x-transition:leave="mh-panel-leave" x-transition:leave-start="mh-panel-leave-start"
            x-transition:leave-end="mh-panel-leave-end" x-data="{ ready: false }"
            x-effect="
                if (activeSection === 'marketing-hub') {
                    ready = false;
                    $nextTick(() => {
                        requestAnimationFrame(() => {
                            ready = true;
                            window.__initMarketingHubCharts?.();
                        });
                    });
                } else {
                    ready = false;
                }
            ">
            <script>
                window.__mhChartData = @json($mhChartData);
            </script>
            <div class="mh-dashboard" :class="{ 'is-ready': ready }">
                {{-- Performance Snapshot --}}
                <div class="mh-section">
                    <h3 class="mh-section-title mh-anim-item" style="--i: 0">Performance Snapshot</h3>
                    <div class="mh-kpi-row">
                        <article class="mh-kpi-card mh-anim-item" style="--i: 0">
                            <p class="mh-kpi-label">Profile Views</p>
                            <p class="mh-kpi-value">{{ $mh['kpis']['profile_views']['value'] }}</p>
                            <span class="mh-kpi-pill mh-kpi-pill--yellow">
                                <svg xmlns="http://www.w3.org/2000/svg" width="6" height="9" viewBox="0 0 6 9"
                                    fill="none" aria-hidden="true">
                                    <path
                                        d="M2.91 0L5.8 2.895L5.415 3.265C5.33833 3.34167 5.26167 3.37333 5.185 3.36C5.105 3.34333 5.02833 3.3 4.955 3.23L3.74 2.005C3.65333 1.91833 3.575 1.835 3.505 1.755C3.43167 1.675 3.36667 1.59833 3.31 1.525C3.33333 1.72167 3.35333 1.92833 3.37 2.145C3.38333 2.35833 3.39 2.575 3.39 2.795L3.39 8.93H2.415L2.415 2.795C2.415 2.575 2.42333 2.35667 2.44 2.14C2.45333 1.92333 2.47333 1.71667 2.5 1.52C2.44333 1.59667 2.38 1.675 2.31 1.755C2.23667 1.835 2.15667 1.91833 2.07 2.005L0.845 3.24C0.775 3.31 0.7 3.35333 0.62 3.37C0.54 3.38333 0.461667 3.35167 0.385 3.275L0 2.905L2.91 0Z"
                                        fill="currentColor" />
                                </svg>
                                {{ $mh['kpis']['profile_views']['sublabel'] }}
                            </span>
                        </article>
                        <article class="mh-kpi-card mh-anim-item" style="--i: 1">
                            <p class="mh-kpi-label">New Clients</p>
                            <p class="mh-kpi-value">{{ $mh['kpis']['new_clients']['value'] }}</p>
                            <span
                                class="mh-kpi-pill mh-kpi-pill--peach">{{ $mh['kpis']['new_clients']['sublabel'] }}</span>
                        </article>
                        <article class="mh-kpi-card mh-anim-item" style="--i: 2">
                            <p class="mh-kpi-label">Booking Conversion</p>
                            <p class="mh-kpi-value">{{ $mh['kpis']['booking_conversion']['value'] }}</p>
                            <span
                                class="mh-kpi-pill mh-kpi-pill--pink">{{ $mh['kpis']['booking_conversion']['sublabel'] }}</span>
                        </article>
                        <article class="mh-kpi-card mh-anim-item" style="--i: 3">
                            <p class="mh-kpi-label">Repeat Clients</p>
                            <p class="mh-kpi-value">{{ $mh['kpis']['repeat_clients']['value'] }}</p>
                            <span class="mh-kpi-pill mh-kpi-pill--blue">
                                <svg xmlns="http://www.w3.org/2000/svg" width="6" height="9" viewBox="0 0 6 9"
                                    fill="none" aria-hidden="true">
                                    <path
                                        d="M2.91 0L5.8 2.895L5.415 3.265C5.33833 3.34167 5.26167 3.37333 5.185 3.36C5.105 3.34333 5.02833 3.3 4.955 3.23L3.74 2.005C3.65333 1.91833 3.575 1.835 3.505 1.755C3.43167 1.675 3.36667 1.59833 3.31 1.525C3.33333 1.72167 3.35333 1.92833 3.37 2.145C3.38333 2.35833 3.39 2.575 3.39 2.795L3.39 8.93H2.415L2.415 2.795C2.415 2.575 2.42333 2.35667 2.44 2.14C2.45333 1.92333 2.47333 1.71667 2.5 1.52C2.44333 1.59667 2.38 1.675 2.31 1.755C2.23667 1.835 2.15667 1.91833 2.07 2.005L0.845 3.24C0.775 3.31 0.7 3.35333 0.62 3.37C0.54 3.38333 0.461667 3.35167 0.385 3.275L0 2.905L2.91 0Z"
                                        fill="currentColor" />
                                </svg>
                                {{ $mh['kpis']['repeat_clients']['sublabel'] }}
                            </span>
                        </article>
                        <article class="mh-kpi-card mh-anim-item" style="--i: 4">
                            <p class="mh-kpi-label">Average Rating</p>
                            <p class="mh-kpi-value mh-kpi-value--rating">
                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                                    viewBox="0 0 20 20" fill="none">
                                    <path
                                        d="M8.75651 0.943537C9.14791 -0.314515 10.8521 -0.314511 11.2435 0.943541L12.7078 5.65027C12.8829 6.21288 13.3849 6.5938 13.9513 6.5938H18.69C19.9566 6.5938 20.4832 8.2865 19.4585 9.06402L15.6249 11.9729C15.1666 12.3207 14.9748 12.937 15.1499 13.4996L16.6142 18.2063C17.0056 19.4644 15.6269 20.5105 14.6022 19.733L10.7685 16.8241C10.3103 16.4764 9.68974 16.4764 9.23148 16.8241L5.3978 19.733C4.37311 20.5105 2.99439 19.4644 3.38579 18.2063L4.85012 13.4996C5.02516 12.937 4.83341 12.3207 4.37515 11.9729L0.541471 9.06402C-0.483225 8.2865 0.0434023 6.5938 1.31 6.5938H6.04868C6.61512 6.5938 7.11714 6.21288 7.29217 5.65027L8.75651 0.943537Z"
                                        fill="#FFC97A" />
                                </svg>
                                {{ $mh['kpis']['average_rating']['value'] }}
                            </p>
                            <span
                                class="mh-kpi-pill mh-kpi-pill--green">{{ $mh['kpis']['average_rating']['sublabel'] }}</span>
                        </article>
                    </div>

                    <div class="mh-tips-banner mh-anim-item" style="--i: 5" role="note">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="20" viewBox="0 0 14 20"
                            fill="none">
                            <path
                                d="M3.625 6.625C3.625 5.82935 3.94107 5.06629 4.50368 4.50368C5.06629 3.94107 5.82935 3.625 6.625 3.625M4.625 18.625H8.625M8.625 15.625C8.625 11.525 12.625 10.725 12.625 6.625C12.625 5.0337 11.9929 3.50758 10.8676 2.38236C9.74242 1.25714 8.2163 0.625 6.625 0.625C5.0337 0.625 3.50758 1.25714 2.38236 2.38236C1.25714 3.50758 0.625 5.0337 0.625 6.625C0.625 10.625 4.625 11.625 4.625 15.625H8.625Z"
                                stroke="#FFC979" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p>
                            <strong>Tips to Grow Faster:</strong>
                            Try offering a limited-time discount to attract new clients.
                        </p>
                    </div>
                </div>

                <div class="mh-grid mh-grid--mid">
                    <article class="mh-card mh-card--chart mh-anim-item" style="--i: 6">
                        <div class="mh-card-header">
                            <h3 class="mh-card-title">Peak Bookings Times per Day</h3>
                            <div class="mh-day-switcher" aria-label="Select day">
                                <button type="button" id="mhPeakDayPrev" class="mh-day-btn"
                                    aria-label="Previous day">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34"
                                        viewBox="0 0 34 34" fill="none">
                                        <g filter="url(#filter0_d_3_384)">
                                            <circle cx="17" cy="13" r="13" fill="white" />
                                            <circle cx="17" cy="13" r="12.5" stroke="#F5F5F5" />
                                        </g>
                                        <path d="M18.625 17.0625L14.5347 12.9722L18.5563 8.9505" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <defs>
                                            <filter id="filter0_d_3_384" x="0" y="0" width="34" height="34"
                                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix"
                                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                    result="hardAlpha" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="2" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix"
                                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix"
                                                    result="effect1_dropShadow_3_384" />
                                                <feBlend mode="normal" in="SourceGraphic"
                                                    in2="effect1_dropShadow_3_384" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </button>
                                <span id="mhPeakDayLabel" class="mh-day-label">Monday</span>
                                <button type="button" id="mhPeakDayNext" class="mh-day-btn" aria-label="Next day">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="34" height="34"
                                        viewBox="0 0 34 34" fill="none">
                                        <g filter="url(#filter0_d_3_387)">
                                            <circle cx="13" cy="13" r="13"
                                                transform="matrix(-1 0 0 1 30 0)" fill="white" />
                                            <circle cx="13" cy="13" r="12.5"
                                                transform="matrix(-1 0 0 1 30 0)" stroke="#F5F5F5" />
                                        </g>
                                        <path d="M15.375 17.0625L19.4653 12.9722L15.4437 8.9505" stroke="#3B3731"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                        <defs>
                                            <filter id="filter0_d_3_387" x="0" y="0" width="34" height="34"
                                                filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix"
                                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                    result="hardAlpha" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="2" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix"
                                                    values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix"
                                                    result="effect1_dropShadow_3_387" />
                                                <feBlend mode="normal" in="SourceGraphic"
                                                    in2="effect1_dropShadow_3_387" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mh-chart-wrap mh-chart-wrap--peak">
                            <canvas id="mhPeakBookingsChart" aria-label="Peak bookings times chart"></canvas>
                        </div>
                    </article>

                    <article class="mh-card mh-anim-item" style="--i: 7">
                        <div class="mh-card-header">
                            <h3 class="mh-card-title">Services</h3>
                        </div>
                        <div class="mh-services-stats">
                            <div class="mh-services-stat">
                                <p class="mh-stat-label">Popular Service</p>
                                <p class="mh-stat-value">{{ $mh['services']['popular'] }}</p>
                            </div>
                            <div class="mh-services-divider" aria-hidden="true"></div>
                            <div class="mh-services-stat">
                                <p class="mh-stat-label">Top Promo Code</p>
                                <p class="mh-stat-value">{{ $mh['services']['top_promo'] }}</p>
                            </div>
                        </div>
                        <div class="mh-donut-block">
                            <div>
                                <h4 class="mh-donut-title">Most Popular Services</h4>
                                <p class="mh-donut-sub">Based on this months bookings</p>
                            </div>
                            <div class="mh-donut-row">
                                <div class="mh-chart-wrap mh-chart-wrap--donut">
                                    <canvas id="mhServicesChart" data-values='@json($mh['services']['values'])'
                                        aria-label="Most popular services chart"></canvas>
                                </div>
                                <ul class="mh-legend">
                                    @forelse ($mh['services']['legend'] as $i => $item)
                                        <li>
                                            <span class="mh-legend-dot"
                                                style="background:{{ $item['color'] ?? ($serviceColors[$i] ?? '#FBAC83') }}"></span>
                                            <span class="mh-legend-name">{{ $item['name'] }}</span>
                                            <span class="mh-legend-pct">{{ $item['pct'] }}%</span>
                                        </li>
                                    @empty
                                        <li>
                                            <span class="mh-legend-name">No service data yet</span>
                                        </li>
                                    @endforelse
                                </ul>
                            </div>
                        </div>
                    </article>
                </div>

                <div class="mh-grid mh-grid--bottom {{ $isSpaceUser ? 'mh-grid--bottom-space' : '' }}">
                    @unless ($isSpaceUser)
                        <article class="mh-card mh-anim-item" style="--i: 8">
                            <div class="mh-donut-block">
                                <div>
                                    <h3 class="mh-card-title">Most Popular Pets</h3>
                                    <p class="mh-donut-sub">Based on this months bookings</p>
                                </div>
                                <div class="mh-donut-row">
                                    <div class="mh-chart-wrap mh-chart-wrap--donut">
                                        <canvas id="mhPetsChart" data-values='@json($mh['pets']['values'])'
                                            aria-label="Most popular pets chart"></canvas>
                                    </div>
                                    <ul class="mh-legend">
                                        @forelse ($mh['pets']['legend'] as $i => $item)
                                            <li>
                                                <span class="mh-legend-dot"
                                                    style="background:{{ $item['color'] ?? ($petColors[$i] ?? '#D8E8B7') }}"></span>
                                                <span class="mh-legend-name">{{ $item['name'] }}</span>
                                                <span class="mh-legend-pct">{{ $item['pct'] }}%</span>
                                            </li>
                                        @empty
                                            <li>
                                                <span class="mh-legend-name">No pet data yet</span>
                                            </li>
                                        @endforelse
                                    </ul>
                                </div>
                            </div>
                        </article>
                    @endunless

                    <article class="mh-card mh-anim-item" style="--i: {{ $isSpaceUser ? 8 : 9 }}">
                        <div class="mh-card-header mh-card-header--divider">
                            <h3 class="mh-card-title">Bookings From</h3>
                        </div>
                        <div class="mh-bookings-from">
                            @foreach ($mh['bookings_from'] as $i => $source)
                                @if ($i > 0)
                                    <div class="mh-bookings-from-divider" aria-hidden="true"></div>
                                @endif
                                <div class="mh-source">
                                    <p class="mh-stat-label">{{ $source['label'] }}</p>
                                    <p class="mh-stat-value">{{ $source['pct'] }}%</p>
                                </div>
                            @endforeach
                        </div>
                    </article>
                </div>
            </div>
        </div>

        <div x-show="activeSection === 'promo-creation'" x-cloak x-transition:enter="mh-panel-enter"
            x-transition:enter-start="mh-panel-enter-start" x-transition:enter-end="mh-panel-enter-end"
            x-transition:leave="mh-panel-leave" x-transition:leave-start="mh-panel-leave-start"
            x-transition:leave-end="mh-panel-leave-end">
            @if ($showPromoForm)
                <div class="mh-promo-view mh-promo-view--form" wire:key="promo-form-view">
                    <x-marketing-hub.promo-form :promo-service-options="$promoServiceOptions" :promo-pet-type-options="$promoPetTypeOptions" :promo-pet-size-options="$promoPetSizeOptions" :promo-start-date="$startDate"
                        :promo-end-date="$endDate" :promo-editing-id="$editingPromoId" />
                </div>
            @else
                <div class="mh-promo-view mh-promo-view--list" wire:key="promo-list-view">
                    <div class="mh-promo">
                        <div class="mh-promo-section">
                            <div class="mh-promo-header">
                                <div>
                                    <h3 class="mh-promo-title">Promotions</h3>
                                    <p class="mh-promo-subtitle">Create and manage offers to attract more bookings.</p>
                                </div>
                                <button type="button" class="mh-promo-create-btn"
                                    @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } }))"
                                    wire:click="openCreatePromo" wire:loading.attr="disabled"
                                    wire:target="openCreatePromo">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                        viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                        <path d="M7 1V13M1 7H13" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>
                                    Create Promotion
                                </button>
                            </div>

                            <div class="mh-tips-banner mh-tips-banner--promo" role="note">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16"
                                    viewBox="0 0 12 16" fill="none">
                                    <path
                                        d="M5.79447 12.1458V8.43996M4.73565 8.30654C5.08155 8.39548 5.43732 8.44031 5.79447 8.43996C6.15162 8.44031 6.50739 8.39548 6.85329 8.30654M7.3827 13.5851C6.3332 13.7837 5.25574 13.7837 4.20624 13.5851M6.85329 15.2672C6.14934 15.341 5.4396 15.341 4.73565 15.2672M7.3827 12.1458V12.0103C7.3827 11.3164 7.84718 10.7235 8.44718 10.3755C9.45518 9.79175 10.2425 8.89188 10.6872 7.81528C11.1319 6.73869 11.2092 5.54549 10.9069 4.42055C10.6047 3.29562 9.93994 2.30175 9.0156 1.59296C8.09125 0.88416 6.95894 0.5 5.79412 0.5C4.62929 0.5 3.49699 0.88416 2.57264 1.59296C1.64829 2.30175 0.983507 3.29562 0.681292 4.42055C0.379078 5.54549 0.456304 6.73869 0.901005 7.81528C1.34571 8.89188 2.13306 9.79175 3.14106 10.3755C3.74106 10.7235 4.20624 11.3164 4.20624 12.0103V12.1458"
                                        stroke="#B5D475" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <p>Limited-time offers convert 22% better than ongoing discounts.</p>
                            </div>

                            <div class="mh-promo-table-wrap" x-data="{ ready: false }"
                                x-effect="
                            if (activeSection === 'promo-creation') {
                                ready = false;
                                $nextTick(() => requestAnimationFrame(() => ready = true));
                            } else {
                                ready = false;
                            }
                        ">
                                <table class="mh-promo-table" :class="{ 'is-ready': ready }">
                                    <thead>
                                        <tr>
                                            <th>Discount Type</th>
                                            <th>Discount Amount</th>
                                            <th>Code</th>
                                            <th>Valid Dates</th>
                                            <th>Status</th>
                                            <th class="mh-promo-th-edit">Edit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($mhPromos['promos'] as $promo)
                                            <tr wire:key="promo-row-{{ $promo['id'] }}" @class(['mh-promo-row--off' => !$promo['visibility']])
                                                style="--i: {{ $loop->index }}" x-data="{ on: {{ $promo['visibility'] ? 'true' : 'false' }} }"
                                                :class="{ 'mh-promo-row--off': !on }">
                                                <td>{{ $promo['discount_type_label'] }}</td>
                                                <td>{{ $promo['discount_amount_label'] }}</td>
                                                <td class="mh-promo-code" style="font-weight: 600;">
                                                    {{ $promo['code'] }}
                                                </td>
                                                <td>{{ $promo['valid_dates_label'] }}</td>
                                                <td>
                                                    <label class="ma-switch" style="height: 24px;">
                                                        <input type="checkbox" :checked="on"
                                                            wire:loading.attr="disabled"
                                                            wire:target="togglePromoVisibility({{ $promo['id'] }})"
                                                            @change="
                                                        on = $event.target.checked;
                                                        window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } }));
                                                        $wire.togglePromoVisibility({{ $promo['id'] }})
                                                            .then(() => {
                                                                window.dispatchEvent(new CustomEvent('nav-list-loading-end'));
                                                            })
                                                            .catch(() => {
                                                                on = !on;
                                                                $event.target.checked = on;
                                                                window.dispatchEvent(new CustomEvent('nav-list-loading-end'));
                                                            });
                                                    "
                                                            aria-label="Toggle promo visibility">
                                                        <span class="ma-switch-slider"></span>
                                                        <span class="ma-switch-check-icon" aria-hidden="true">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                                height="20" viewBox="0 0 20 20" fill="none">
                                                                <path
                                                                    d="M9.99391 0C4.49726 0 0 4.49726 0 9.99391C0 15.4906 4.49726 19.9878 9.99391 19.9878C15.4906 19.9878 19.9878 15.4906 19.9878 9.99391C19.9878 4.49726 15.4906 0 9.99391 0ZM8.41154 14.5744C8.18156 14.8044 7.80869 14.8044 7.57871 14.5744L3.70323 10.699C3.31384 10.3096 3.31384 9.67824 3.70323 9.28885C4.09225 8.89984 4.72282 8.8994 5.11237 9.28786L7.99513 12.1626L14.8709 5.28678C15.2624 4.8953 15.8975 4.89642 16.2876 5.28928C16.6757 5.68019 16.6746 6.31139 16.2851 6.70092L8.41154 14.5744Z"
                                                                    fill="white" />
                                                            </svg>
                                                        </span>
                                                    </label>
                                                </td>
                                                <td class="mh-promo-td-edit">
                                                    <div class="mh-promo-actions">
                                                        <button type="button" class="mh-promo-action-btn"
                                                            @click="window.dispatchEvent(new CustomEvent('nav-list-loading-start', { detail: { persistent: true } }))"
                                                            wire:click="openEditPromo({{ $promo['id'] }})"
                                                            wire:loading.attr="disabled"
                                                            wire:target="openEditPromo({{ $promo['id'] }})"
                                                            aria-label="Edit promotion">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="17"
                                                                height="16" viewBox="0 0 17 16" fill="none">
                                                                <path
                                                                    d="M10.8529 2.51425L13.6765 5.29691M8.97059 15.5H16.5M1.44118 11.7898L0.5 15.5L4.26471 14.5724L15.1692 3.82581C15.5221 3.47793 15.7203 3.00616 15.7203 2.51425C15.7203 2.02234 15.5221 1.55057 15.1692 1.20269L15.0073 1.04315C14.6543 0.695371 14.1756 0.5 13.6765 0.5C13.1773 0.5 12.6986 0.695371 12.3456 1.04315L1.44118 11.7898Z"
                                                                    stroke="#3B3731" stroke-linecap="round"
                                                                    stroke-linejoin="round" />
                                                            </svg>
                                                        </button>
                                                        <button type="button"
                                                            class="mh-promo-action-btn mh-promo-action-btn--more"
                                                            aria-label="More options">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="5"
                                                                height="5" viewBox="0 0 5 5" fill="none">
                                                                <circle cx="2.5" cy="2.5" r="2.5"
                                                                    fill="#3B3731" />
                                                            </svg>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="5"
                                                                height="5" viewBox="0 0 5 5" fill="none">
                                                                <circle cx="2.5" cy="2.5" r="2.5"
                                                                    fill="#3B3731" />
                                                            </svg>
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="5"
                                                                height="5" viewBox="0 0 5 5" fill="none">
                                                                <circle cx="2.5" cy="2.5" r="2.5"
                                                                    fill="#3B3731" />
                                                            </svg>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr style="--i: 0">
                                                <td colspan="6" class="mh-promo-empty">No promotions yet. Create
                                                    your
                                                    first
                                                    offer to get started.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mh-promo-section">
                            <h3 class="mh-section-title" style="border-bottom: none;padding-bottom: 0;">Campaign
                                Performance
                                (This Week)</h3>
                            <div class="mh-kpi-row mh-kpi-row--promo">
                                <article class="mh-kpi-card mh-kpi-card--promo-views">
                                    <p class="mh-kpi-label">Promotion Views</p>
                                    <p class="mh-kpi-value">{{ $mhPromos['performance']['views']['value'] }}</p>
                                    <span class="mh-kpi-pill mh-kpi-pill--promo-views">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="6" height="9"
                                            viewBox="0 0 6 9" fill="none" aria-hidden="true">
                                            <path
                                                d="M2.91 0L5.8 2.895L5.415 3.265C5.33833 3.34167 5.26167 3.37333 5.185 3.36C5.105 3.34333 5.02833 3.3 4.955 3.23L3.74 2.005C3.65333 1.91833 3.575 1.835 3.505 1.755C3.43167 1.675 3.36667 1.59833 3.31 1.525C3.33333 1.72167 3.35333 1.92833 3.37 2.145C3.38333 2.35833 3.39 2.575 3.39 2.795L3.39 8.93H2.415L2.415 2.795C2.415 2.575 2.42333 2.35667 2.44 2.14C2.45333 1.92333 2.47333 1.71667 2.5 1.52C2.44333 1.59667 2.38 1.675 2.31 1.755C2.23667 1.835 2.15667 1.91833 2.07 2.005L0.845 3.24C0.775 3.31 0.7 3.35333 0.62 3.37C0.54 3.38333 0.461667 3.35167 0.385 3.275L0 2.905L2.91 0Z"
                                                fill="currentColor" />
                                        </svg>
                                        {{ $mhPromos['performance']['views']['sublabel'] }}
                                    </span>
                                </article>
                                <article class="mh-kpi-card mh-kpi-card--promo-bookings">
                                    <p class="mh-kpi-label">Bookings</p>
                                    <p class="mh-kpi-value">{{ $mhPromos['performance']['bookings']['value'] }}</p>
                                    <span
                                        class="mh-kpi-pill mh-kpi-pill--promo-bookings">{{ $mhPromos['performance']['bookings']['sublabel'] }}</span>
                                </article>
                                <article class="mh-kpi-card mh-kpi-card--promo-revenue">
                                    <p class="mh-kpi-label">Revenue</p>
                                    <p class="mh-kpi-value">{{ $mhPromos['performance']['revenue']['value'] }}</p>
                                    <span
                                        class="mh-kpi-pill mh-kpi-pill--promo-revenue">{{ $mhPromos['performance']['revenue']['sublabel'] }}</span>
                                </article>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Register rangeDateCalendar Alpine factory on first paint (Livewire morphs omit scripts). --}}
    <div hidden aria-hidden="true">
        <x-ui.range-date-calendar id="mh-range-calendar-bootstrap" start-name="_mh_boot_start"
            end-name="_mh_boot_end" calendar-width="1px" />
    </div>
</section>

<style>
    .dashboard-content-wrapper {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .active-section-header {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
        line-height: normal;
        position: relative;
    }

    .active-section-loading-bar {
        position: absolute;
        left: 0;
        right: 0;
        bottom: -6px;
        height: 4px;
        overflow: hidden;
        z-index: 10;
        pointer-events: none;
        background: rgba(232, 228, 222, 0.85);
        border-radius: 2px;
    }

    .active-section-loading-bar.is-loading {
        display: block !important;
    }

    .active-section-loading-bar__sweep {
        position: absolute;
        top: 0;
        left: -42%;
        height: 100%;
        width: 42%;
        border-radius: 2px;
        background: linear-gradient(90deg, #FFC97A 0%, #f6a623 45%, #FFC97A 100%);
        box-shadow: 0 0 12px rgba(246, 166, 35, 0.45);
        will-change: left;
        animation: active-section-load-sweep 1.1s linear infinite;
    }

    @keyframes active-section-load-sweep {
        0% {
            left: -42%;
        }

        100% {
            left: 100%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .active-section-loading-bar__sweep {
            animation: none;
            left: 0;
            width: 100%;
        }
    }

    .active-section-header h2,
    .active-section-header>div>h2 {
        color: #3B3731;
        text-align: right;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: capitalize;
    }

    .marketing-hub-content {
        padding: 0 0 3rem;
        width: 100%;
        min-width: 0;
    }

    .marketing-hub-placeholder {
        margin: 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-weight: 400;
        line-height: normal;
    }

    .marketing-hub-panels {
        margin-top: 1.5rem;
    }

    .mh-dashboard {
        display: flex;
        flex-direction: column;
        gap: 1.75rem;
    }

    .mh-section-title {
        margin: 0 0 1rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        padding-bottom: 1rem;
        border-bottom: 1px solid #D4D4D4;
        margin-bottom: 1.15rem;
    }

    .mh-kpi-row {
        display: grid;
        grid-template-columns: repeat(5, minmax(0, 1fr));
        gap: 2.5rem;
    }

    .mh-kpi-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.55rem;
        background: #fff;
        border: 1px solid #ECECEC;
        border-radius: 12px;
        padding: 1rem 1.1rem 1.15rem;
        min-width: 0;
    }

    .mh-kpi-label {
        margin: 0 0 0.55rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .mh-kpi-value {
        display: block;
        width: 100%;
        margin: 0 0 0.55rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 24px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
    }

    .mh-kpi-value--rating {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .mh-kpi-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        max-width: 100%;
        margin-top: 0;
        padding: 0.3rem 1rem;
        border-radius: 9999px;
        font-family: Lato;
        font-size: 10px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        letter-spacing: 0.1px;
    }

    .mh-kpi-pill svg {
        flex-shrink: 0;
    }

    .mh-kpi-pill--yellow {
        background: rgba(255, 201, 122, 0.2);
        color: #FFC97A;
    }

    .mh-kpi-pill--peach {
        background: rgba(251, 172, 131, 0.2);
        color: #FBAC83;
    }

    .mh-kpi-pill--pink {
        background: rgba(255, 168, 153, 0.2);
        color: #FFA899;
    }

    .mh-kpi-pill--blue {
        background: rgba(203, 220, 232, 0.2);
        color: #9AC1DD;
    }

    .mh-kpi-pill--green {
        background: rgba(193, 219, 138, 0.2);
        color: #C1DB8A;
    }

    .mh-tips-banner {
        width: fit-content;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.65rem;
        margin: 2rem auto 0;
        padding: 0.85rem 1.1rem;
        border-radius: 100px;
        background: rgba(255, 216, 140, 0.10);
        text-align: center;
    }

    .mh-tips-banner p {
        color: #FFC979;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .mh-tips-banner strong {
        font-weight: 600;
    }

    .mh-grid {
        display: grid;
        column-gap: 3rem;
        row-gap: 2.5rem;
    }

    .mh-grid--mid {
        grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr);
    }

    .mh-grid--bottom {
        grid-template-columns: minmax(0, 1fr) minmax(0, 4fr);
        align-items: center;
    }

    .mh-grid--bottom-space {
        grid-template-columns: minmax(0, 1.45fr) minmax(0, 1fr);
        align-items: start;
        margin-top: 5rem;
    }

    .mh-card {
        background: transparent;
        padding: 0;
        min-width: 0;
        border: none;
        outline: none;
    }

    .mh-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #D4D4D4;
        padding-bottom: 1rem;
    }

    .mh-card-header--divider {
        padding-bottom: 0.85rem;
        border-bottom: 1px solid #D4D4D4;
        margin-bottom: 1.15rem;
    }

    .mh-card-title {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .mh-day-switcher {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        flex-shrink: 0;
    }

    .mh-day-btn {
        margin-top: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        width: 28px;
        height: 28px;
        padding: 0;
        border: none;
        border-radius: 6px;
        background: transparent;
        cursor: pointer;
        outline: none;
    }

    .mh-day-btn:focus {
        outline: none;
    }

    .mh-day-label {
        flex: 0 0 7.25rem;
        width: 7.25rem;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .mh-card--chart {
        width: 100%;
        min-width: 0;
    }

    .mh-chart-wrap--peak {
        position: relative;
        height: 360px;
        width: 100%;
        min-width: 0;
    }

    .mh-chart-wrap--peak canvas {
        display: block;
        width: 100% !important;
        height: 100% !important;
    }

    .mh-chart-wrap--donut {
        position: relative;
        width: 180px;
        height: 180px;
        flex-shrink: 0;
    }

    .mh-services-stats {
        display: flex;
        justify-content: space-between;
        margin-bottom: 1.35rem;
    }

    .mh-services-divider {
        width: 1px;
        background: #D4D4D4;
        align-self: stretch;
    }

    .mh-services-stat {
        min-width: 0;
    }

    .mh-stat-label {
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: 40px;
        /* 222.222% */
    }

    .mh-stat-value {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: 40px;
    }

    .mh-stat-value--muted {
        color: #6B7280;
    }

    .mh-donut-block {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .mh-donut-title {
        margin: 0 0 0.2rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        line-height: normal;
    }

    .mh-donut-sub {
        margin: 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
        line-height: 20px;
    }

    .mh-donut-row {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .mh-legend {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        flex: 1;
        min-width: 0;
    }

    .mh-legend li {
        display: grid;
        grid-template-columns: 10px 1fr;
        column-gap: 0.55rem;
        row-gap: 0.15rem;
        align-items: center;
    }

    .mh-legend-name {
        grid-column: 2;
        grid-row: 1;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        line-height: normal;
    }

    .mh-legend-dot {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        flex-shrink: 0;
        grid-column: 1;
        grid-row: 2;
    }

    .mh-legend-pct {
        grid-column: 2;
        grid-row: 2;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 700;
        line-height: normal;
    }

    .mh-bookings-from {
        display: flex;
        align-items: stretch;
        justify-content: space-between;
        gap: 1rem;
        padding: 1.25rem 2rem;
        border-radius: 10px;
        background: #F6F6F6;
    }

    .mh-bookings-from-divider {
        width: 1px;
        background: #D4D4D4;
        align-self: stretch;
        flex-shrink: 0;
    }

    .mh-source {
        text-align: start;
        min-width: 0;
        flex: 1;
    }

    @media (max-width: 1200px) {
        .mh-kpi-row {
            grid-template-columns: repeat(3, minmax(0, 1fr));
        }

        .mh-grid--mid,
        .mh-grid--bottom {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .mh-kpi-row {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .mh-kpi-value {
            font-size: 26px;
        }

        .mh-bookings-from {
            flex-direction: column;
            text-align: left;
        }

        .mh-bookings-from-divider {
            width: 100%;
            height: 1px;
        }

        .mh-source {
            text-align: left;
        }

        .mh-donut-row {
            flex-wrap: wrap;
        }
    }

    @media (max-width: 520px) {
        .mh-kpi-row {
            grid-template-columns: 1fr;
        }

        .mh-services-stats {
            grid-template-columns: 1fr;
            row-gap: 1rem;
        }

        .mh-services-divider {
            width: 100%;
            height: 1px;
        }
    }

    /* Promo Creation */
    .mh-promo {
        display: flex;
        flex-direction: column;
        gap: 2.5rem;
    }

    .mh-promo-section {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .mh-promo-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #D4D4D4;
        margin-bottom: 1.25rem;
    }

    .mh-promo-title {
        margin: 0 0 0.35rem;
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .mh-promo-subtitle {
        margin: 0;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .mh-promo-create-btn {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        margin-top: 0.15rem;
        padding: 0;
        border: none;
        background: transparent;
        color: #3B3731;
        text-align: right;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        white-space: nowrap;
    }

    .mh-promo-create-btn:hover {
        color: #6B6760;
    }

    .mh-tips-banner--promo {
        border-radius: 10px;
        background: rgba(201, 221, 160, 0.20);
        margin: 0 auto 1.5rem;
    }

    .mh-tips-banner--promo p {
        color: #B5D475;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .mh-panel-enter {
        transition: opacity 0.35s ease, transform 0.35s ease;
    }

    .mh-panel-enter-start {
        opacity: 0;
        transform: translateY(12px);
    }

    .mh-panel-enter-end {
        opacity: 1;
        transform: translateY(0);
    }

    .mh-panel-leave {
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .mh-panel-leave-start {
        opacity: 1;
        transform: translateY(0);
    }

    .mh-panel-leave-end {
        opacity: 0;
        transform: translateY(8px);
    }

    .mh-dashboard .mh-anim-item {
        opacity: 0;
        transform: translateY(12px);
    }

    .mh-dashboard.is-ready .mh-anim-item {
        animation: mh-fade-up 0.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        animation-delay: calc(var(--i, 0) * 55ms + 40ms);
    }

    @keyframes mh-fade-up {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mh-promo-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .mh-promo-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .mh-promo-table thead tr {
        opacity: 0;
        transform: translateY(6px);
    }

    .mh-promo-table.is-ready thead tr {
        animation: mh-fade-up 0.4s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        animation-delay: 40ms;
    }

    .mh-promo-table tbody tr {
        opacity: 0;
        transform: translateY(10px);
    }

    .mh-promo-table.is-ready tbody tr {
        animation: mh-fade-up 0.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        animation-delay: calc(var(--i, 0) * 55ms + 100ms);
    }

    @media (prefers-reduced-motion: reduce) {

        .mh-panel-enter,
        .mh-panel-leave,
        .mh-dashboard .mh-anim-item,
        .mh-dashboard.is-ready .mh-anim-item,
        .mh-promo-table thead tr,
        .mh-promo-table tbody tr,
        .mh-promo-table.is-ready thead tr,
        .mh-promo-table.is-ready tbody tr,
        .mh-promo-view,
        .mh-promo-view--form,
        .mh-promo-view--list,
        .mh-promo-form__section,
        .mh-promo-header-bar[data-form-open] .mh-promo-back-btn,
        .mh-promo-form__section-head .mh-promo-publish-btn {
            transition: none !important;
            animation: none !important;
            opacity: 1 !important;
            transform: none !important;
        }
    }

    .mh-promo-table th {
        padding: 0.75rem 0.5rem 0.9rem;
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-align: left;
        border-bottom: 1px solid #ECECEC;
        white-space: nowrap;
    }

    .mh-promo-th-edit,
    .mh-promo-td-edit {
        border-left: 1px solid #ECECEC;
        padding-left: 5rem !important;
    }

    .mh-promo-th-edit {
        text-align: left !important;
        padding-right: 0.25rem !important;
    }

    .mh-promo-table td {
        padding: 1.05rem 0.5rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        border-bottom: 1px solid #F0F0F0;
        vertical-align: middle;
        transition: color 0.15s ease;
    }

    .mh-promo-table tbody tr:last-child td {
        border-bottom: none;
    }

    .mh-promo-code {
        font-weight: 600;
        letter-spacing: 0.02em;
    }

    .mh-promo-row--off td {
        color: #B0AEAB;
    }

    .mh-promo-empty {
        color: #9D9B98 !important;
        font-weight: 400 !important;
        text-align: center;
        padding: 2rem 0.5rem !important;
    }

    .ma-switch {
        position: relative;
        display: inline-block;
        width: 42px;
    }

    .ma-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .ma-switch-slider {
        width: 44px;
        height: 24px;
        aspect-ratio: 11/6;
        position: absolute;
        cursor: pointer;
        inset: 0;
        border-radius: 999px;
        background: #D4D4D4;
        transition: background 0.2s ease;
    }

    .ma-switch-slider::before {
        content: "";
        position: absolute;
        width: 20px;
        height: 20px;
        border-radius: 999px;
        background: #fff;
        top: 2.5px;
        left: 3px;
        transition: transform 0.2s ease, background-color 0.2s ease;
    }

    .ma-switch input:checked+.ma-switch-slider {
        background: #d4e5ad;
    }

    .ma-switch input:checked+.ma-switch-slider::before {
        transform: translateX(20px);
        background: transparent;
    }

    .ma-switch-check-icon {
        position: absolute;
        top: 2px;
        left: 2px;
        width: 20px;
        height: 20px;
        line-height: 0;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .ma-switch input:checked~.ma-switch-check-icon {
        opacity: 1;
        transform: translateX(20px);
    }

    .mh-promo-actions {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 3.5rem;
    }

    .mh-promo-action-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        padding: 0;
        border: none;
        border-radius: 6px;
        background: transparent;
        cursor: pointer;
    }

    .mh-promo-action-btn--more {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.35rem;
    }


    .mh-kpi-row--promo {
        grid-template-columns: repeat(3, minmax(0, 1fr));
        max-width: 100%;
    }

    .mh-kpi-row--promo .mh-kpi-card {
        border: none;
        border-radius: 10px;
    }

    .mh-kpi-card--promo-views {
        background: rgba(255, 235, 206, 0.20);
    }

    .mh-kpi-card--promo-bookings {
        background: rgba(253, 224, 210, 0.20);
    }

    .mh-kpi-card--promo-revenue {
        background: rgba(255, 224, 219, 0.20);
    }

    .mh-kpi-row--promo .mh-kpi-pill {
        border-radius: 74px;
        font-family: Lato;
        font-size: 10px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        letter-spacing: 0.1px;
    }

    .mh-kpi-pill--promo-views {
        background: rgba(255, 201, 122, 0.2);
        color: #FFC97A;
    }

    .mh-kpi-pill--promo-bookings {
        background: rgba(251, 172, 131, 0.2);
        color: #FBAC83;
    }

    .mh-kpi-pill--promo-revenue {
        background: rgba(255, 168, 153, 0.2);
        color: #FFA899;
    }

    .mh-promo-header-bar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        width: 100%;
        gap: 1rem;
        min-height: 37px;
    }

    .mh-promo-header-bar:not([data-form-open]) {
        justify-content: flex-end;
    }

    .mh-promo-header-bar h2 {
        margin: 0;
        text-align: right;
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
        line-height: normal;
        flex-shrink: 0;
    }

    .mh-promo-back-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: 0;
        background: transparent;
        padding: 0.4rem 0 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        white-space: nowrap;
    }

    .mh-promo-back-btn svg {
        flex-shrink: 0;
        width: 16px;
        height: 11px;
    }

    .mh-promo-publish-btn {
        min-width: 195px;
        height: 37px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        border: 0;
        border-radius: 100px;
        background: #BACF8E;
        box-shadow: 0px 2px 4px 0px rgba(0, 0, 0, 0.1);
        color: #FFF;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        padding: 0 1.25rem;
        flex-shrink: 0;
    }

    .mh-promo-publish-btn:disabled {
        opacity: 0.75;
        cursor: wait;
    }

    .mh-promo-publish-btn__loading {
        display: none;
        align-items: center;
        gap: 0.45rem;
    }

    .mh-promo-publish-spinner {
        width: 16px;
        height: 16px;
        border: 2px solid rgba(255, 255, 255, 0.45);
        border-top-color: #fff;
        border-radius: 999px;
        animation: mh-promo-spin 0.8s linear infinite;
    }

    @keyframes mh-promo-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .mh-promo-form {
        margin-top: 1.75rem;
        overflow: visible;
    }

    .mh-promo-view {
        will-change: opacity, transform;
        animation: mh-promo-view-in 0.45s cubic-bezier(0.22, 1, 0.36, 1) both;
        overflow: visible;
    }

    .mh-promo-view--form {
        animation-name: mh-promo-form-in;
        animation-duration: 0.5s;
    }

    .mh-promo-view--list {
        animation-name: mh-promo-list-in;
        animation-duration: 0.4s;
    }

    @keyframes mh-promo-view-in {
        from {
            opacity: 0;
            transform: translateY(14px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes mh-promo-form-in {
        from {
            opacity: 0;
            transform: translateY(18px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes mh-promo-list-in {
        from {
            opacity: 0;
            transform: translateY(12px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mh-promo-form__body {
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .mh-promo-form__section {
        padding: 0;
        margin-bottom: 2.75rem;
        border-bottom: 0;
        opacity: 0;
        transform: translateY(10px);
        animation: mh-promo-section-in 0.4s cubic-bezier(0.22, 1, 0.36, 1) forwards;
        overflow: visible;
    }

    .mh-promo-form__section:nth-child(1) {
        animation-delay: 0.06s;
    }

    .mh-promo-form__section:nth-child(2) {
        animation-delay: 0.12s;
    }

    .mh-promo-form__section:nth-child(3) {
        animation-delay: 0.18s;
    }

    .mh-promo-form__section:nth-child(4) {
        animation-delay: 0.24s;
    }

    .mh-promo-form__section:nth-child(5) {
        animation-delay: 0.3s;
    }

    .mh-promo-form__section:nth-child(6) {
        animation-delay: 0.36s;
    }

    @keyframes mh-promo-section-in {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mh-promo-header-bar[data-form-open] .mh-promo-back-btn {
        animation: mh-promo-header-in 0.35s cubic-bezier(0.22, 1, 0.36, 1) both;
    }

    .mh-promo-form__section-head .mh-promo-publish-btn {
        animation: mh-promo-header-in 0.35s cubic-bezier(0.22, 1, 0.36, 1) both;
        animation-delay: 0.08s;
    }

    @keyframes mh-promo-header-in {
        from {
            opacity: 0;
            transform: translateY(-6px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .mh-promo-form__section--last {
        margin-bottom: 0;
    }

    .mh-promo-form__section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #D4D4D4;
    }

    .mh-promo-form__heading {
        margin: 0 0 1.25rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #D4D4D4;
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .mh-promo-form__section-head .mh-promo-form__heading {
        margin-bottom: 0;
        padding-bottom: 0;
        border-bottom: 0;
    }

    .mh-promo-form .service-field {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        min-width: 0;
    }

    .mh-promo-form .service-field>span {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .mh-promo-form__label--muted {
        color: #9D9B98 !important;
    }

    .mh-promo-form .service-field input {
        width: 100%;
        height: 48px;
        border: 1px solid #D4D4D4;
        border-radius: 10px;
        background: #fff;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
        padding: 0.65rem 0.9rem;
    }

    .mh-promo-form .service-field input::placeholder {
        color: #9D9B98;
    }

    .mh-promo-form .service-field.is-disabled {
        pointer-events: none;
    }

    .mh-promo-form .mh-promo-form__select-field.is-disabled {
        pointer-events: auto;
    }

    .mh-promo-form .mh-promo-form__date-field.is-disabled {
        pointer-events: auto;
        cursor: not-allowed;
    }

    .mh-promo-form .mh-promo-form__date-field.is-disabled .mh-promo-form__date-display,
    .mh-promo-form .mh-promo-form__date-field.is-disabled .mh-promo-form__date-display:disabled {
        cursor: not-allowed;
    }

    .mh-promo-form .service-field.is-disabled .service-custom-trigger {
        color: #9D9B98;
        background: #fff;
        border-color: #DDD;
    }

    .mh-promo-form__grid--2 {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .mh-promo-form__code-field {
        width: 400px;
        max-width: 100%;
    }

    .mh-promo-form__code-input {
        text-transform: uppercase;
    }

    .mh-promo-form__desc-field {
        width: 505px;
        max-width: 100%;
        flex: 1 1 280px;
    }

    .mh-promo-form__grid--discount {
        display: flex;
        flex-wrap: wrap;
        gap: 1.5rem 2.5rem;
        align-items: flex-start;
    }

    .mh-promo-form .service-custom-select.mh-promo-form__discount-type {
        width: 200px;
        max-width: 200px;
    }

    .mh-promo-form__discount-type .service-custom-trigger {
        background: #F7F7F7;
        border-color: #DDD;
    }

    .mh-promo-form__amount-field {
        width: 85px;
    }

    .mh-promo-form__validity {
        position: relative;
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 1.25rem 1.75rem;
    }

    .mh-promo-form__validity-dates {
        display: flex;
        flex-wrap: wrap;
        gap: 0.7rem 1.25rem;
    }

    .mh-promo-form__date-field {
        width: 200px;
        max-width: 100%;
    }

    .mh-promo-form__date-display {
        position: relative;
        display: flex;
        align-items: center;
        width: 100%;
        height: 48px;
        min-height: 48px;
        border: 1px solid #DDD;
        border-radius: 5px;
        background-color: #FFFFFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: 25px;
        padding: 0 0.75rem 0 2.5rem;
        box-sizing: border-box;
        text-align: left;
        cursor: pointer;
        transition: border-color 160ms ease, box-shadow 160ms ease;
    }

    .mh-promo-form__date-display:hover:not(:disabled),
    .mh-promo-form__date-display.is-open {
        border-color: #AFCD6F;
        box-shadow: 0 0 0 3px rgba(175, 205, 111, 0.18);
    }

    .mh-promo-form__date-display:disabled {
        cursor: not-allowed;
        background: #F7F7F7;
        box-shadow: none;
    }

    .mh-promo-form__date-display .is-placeholder {
        color: #9D9B98;
    }

    .mh-promo-form__date-icon {
        position: absolute;
        left: 0.9rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        display: inline-flex;
    }

    .mh-promo-form__section:has(.service-custom-select.is-open),
    .mh-promo-form__section:has(.mh-promo-form__date-display.is-open) {
        position: relative;
        z-index: 50;
    }

    .mh-promo-date-picker {
        position: absolute;
        top: calc(100% + 0.5rem);
        left: 0;
        z-index: 40;
        width: min(44rem, calc(100vw - 3rem));
        max-width: 44rem;
        max-height: min(32rem, calc(100vh - 8rem));
        overflow: auto;
        border: 1px solid #E3E3E3;
        border-radius: 14px;
        background-color: #FFFFFF;
        background-image: none;
        box-shadow: 0 16px 34px rgba(0, 0, 0, 0.12);
        padding: 1rem;
    }

    .mh-promo-date-picker--single {
        width: min(22rem, calc(100vw - 3rem));
        max-width: 22rem;
    }

    .mh-promo-date-picker .rdc-month-dropdown,
    .mh-promo-date-picker .rdc-custom-select-menu {
        background-color: #FFFFFF;
        background-image: none;
    }

    .mh-promo-date-picker .rdc {
        width: 100%;
        min-width: 36rem;
        max-width: 100%;
    }

    .mh-promo-date-picker--single .rdc {
        min-width: 0;
    }

    .mh-promo-date-picker__actions {
        display: flex;
        justify-content: flex-end;
        margin-top: 0.85rem;
    }

    .mh-promo-date-picker__actions button {
        border: 1px solid #AFCD6F;
        border-radius: 999px;
        background: #AFCD6F;
        color: #FFF;
        cursor: pointer;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 700;
        padding: 0.55rem 1.2rem;
        transition: background-color 160ms ease, border-color 160ms ease;
    }

    .mh-promo-date-picker__actions button:hover {
        background: #9ec05f;
        border-color: #9ec05f;
    }

    .mh-promo-date-picker-anim {
        transition: opacity 180ms ease, transform 180ms ease;
    }

    .mh-promo-date-picker-anim-start {
        opacity: 0;
        transform: translateY(-8px);
    }

    .mh-promo-date-picker-anim-end {
        opacity: 1;
        transform: translateY(0);
    }

    .mh-promo-form__dot-label {
        display: inline-flex;
        align-items: center;
        gap: 0.65rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        margin-bottom: 0;
    }

    .mh-promo-form__dot-label--validity {
        margin-bottom: 0.35rem;
        align-self: flex-end;
        padding-bottom: 0.7rem;
    }

    .mh-promo-form .service-policies-fee-dot {
        position: relative;
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: 0 0 20px;
        cursor: pointer;
    }

    .mh-promo-form .service-policies-fee-dot input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        opacity: 0;
        cursor: pointer;
        padding: 0;
        border: 0;
    }

    .mh-promo-form .service-policies-fee-dot>span {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: 1.5px solid #FFD88C;
        border-radius: 50%;
        background: #FFFFFF;
        box-sizing: border-box;
        transition: border-color 160ms ease, background-color 160ms ease;
    }

    .mh-promo-form .service-policies-fee-dot>span::after {
        content: '';
        width: 13.333px;
        height: 13.333px;
        border-radius: 50%;
        background: #FFD88C;
        opacity: 0;
        transition: opacity 160ms ease;
    }

    .mh-promo-form .service-policies-fee-dot input:checked+span::after {
        opacity: 1;
    }

    .mh-promo-form .service-custom-select {
        position: relative;
        width: 100%;
        max-width: 246px;
    }

    .mh-promo-form .service-custom-select.is-open {
        z-index: 30;
    }

    .mh-promo-form .service-custom-trigger {
        width: 100%;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.75rem;
        border: 1px solid #DDD;
        border-radius: 10px;
        background-color: #FFFFFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: 25px;
        padding: 0.65rem 0.9rem;
        cursor: pointer;
    }

    .mh-promo-form .service-custom-trigger:disabled {
        cursor: not-allowed;
        background: #F7F7F7;
        color: #9D9B98;
    }

    .mh-promo-form .service-custom-trigger .is-placeholder {
        color: #9D9B98;
    }

    .mh-promo-form .service-custom-select.is-open .service-custom-trigger {
        border-color: #BACF8E;
    }

    .mh-promo-form .service-custom-chevron {
        flex-shrink: 0;
    }

    .mh-promo-form .service-custom-menu {
        position: absolute;
        top: calc(100% + 0.35rem);
        left: 0;
        right: 0;
        z-index: 20;
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
        padding: 0.45rem;
        border: 1px solid #ECECEC;
        border-radius: 10px;
        background-color: #FFFFFF;
        background-image: none;
        box-shadow: 0 8px 24px rgba(59, 55, 49, 0.12);
        max-height: 240px;
        overflow: auto;
    }

    .mh-promo-form .service-custom-option {
        display: flex;
        align-items: center;
        width: 100%;
        border: 0;
        border-radius: 8px;
        background: transparent;
        color: #3B3731;
        font-family: Lato;
        font-size: 15px;
        font-weight: 500;
        text-align: left;
        padding: 0.55rem 0.7rem;
        cursor: pointer;
    }

    .mh-promo-form .service-custom-option:hover {
        background: #F2F2F2;
    }

    .mh-promo-form .service-custom-option.is-active {
        background: rgba(216, 232, 183, 0.20);
        color: #A4C560;
    }

    .mh-promo-form__amount {
        position: relative;
        display: flex;
        align-items: center;
        gap: 0.15rem;
        width: 85px;
        height: 48px;
        border: 1px solid #D4D4D4;
        border-radius: 10px;
        background: #fff;
        padding: 0 0.3rem 0 0.75rem;
        box-sizing: border-box;
    }

    .mh-promo-form__amount-value {
        display: inline-flex;
        align-items: center;
        flex: 1 1 auto;
        min-width: 0;
        gap: 0;
    }

    .mh-promo-form__amount input,
    .mh-promo-form .service-field .mh-promo-form__amount input,
    .mh-promo-form .service-field .mh-promo-form__amount input:focus,
    .mh-promo-form .service-field .mh-promo-form__amount input:focus-visible {
        width: 100%;
        min-width: 0;
        height: auto;
        border: 0 !important;
        border-radius: 0;
        outline: none;
        box-shadow: none;
        background: transparent;
        padding: 0;
        -moz-appearance: textfield;
    }

    .mh-promo-form__amount input::-webkit-outer-spin-button,
    .mh-promo-form__amount input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    .mh-promo-form__amount--percent .mh-promo-form__amount-value input,
    .mh-promo-form__amount--pound .mh-promo-form__amount-value input {
        width: auto;
        flex: 0 0 auto;
        min-width: 1ch;
    }

    .mh-promo-form__amount-affix {
        position: static;
        flex-shrink: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        line-height: 1;
        pointer-events: none;
        user-select: none;
    }

    .mh-promo-form__steppers {
        position: relative;
        z-index: 2;
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.15rem;
        margin-left: auto;
    }

    .mh-promo-form__stepper-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 18px;
        height: 14px;
        border: 0;
        background: transparent;
        padding: 0;
        cursor: pointer;
    }

    .mh-promo-form__either-or-stack {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .mh-promo-form__either-or {
        display: flex;
        flex-wrap: wrap;
        align-items: flex-end;
        gap: 1rem 1.75rem;
    }

    .mh-promo-form__all-option {
        display: flex;
        flex-direction: column;
        gap: 0.55rem;
        min-width: 120px;
    }

    .mh-promo-form__all-label {
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .mh-promo-form__all-controls {
        display: flex;
        align-items: center;
        gap: 0.85rem;
        min-height: 48px;
    }

    .mh-promo-form__or {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        padding-bottom: 0.85rem;
    }

    .mh-promo-form__select-field {
        width: 246px;
        max-width: 100%;
    }

    .mh-promo-form__select-field .service-custom-select {
        width: 100%;
        max-width: none;
    }

    .mh-promo-form__visibility {
        display: flex;
        align-items: center;
        justify-content: space-between;
        max-width: 295px;
        border-bottom: 1px solid #E0E0E0;
        padding-bottom: 1.25rem;
        margin-top: 0.25rem;
    }

    .mh-promo-form__visibility p {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .mh-promo-form__error {
        color: #D35B5B;
        font-family: Lato;
        font-size: 13px;
        font-weight: 500;
    }

    @media (max-width: 900px) {
        .mh-promo-header {
            flex-direction: column;
            align-items: stretch;
        }

        .mh-promo-create-btn {
            align-self: flex-start;
        }

        .mh-kpi-row--promo {
            max-width: none;
        }

        .mh-promo-header-bar {
            flex-direction: column;
            align-items: stretch;
        }

        .mh-promo-header-bar h2 {
            text-align: left;
        }

        .mh-promo-back-btn {
            padding-top: 0;
            order: -1;
        }

        .mh-promo-form__section-head {
            flex-direction: column;
            align-items: stretch;
        }

        .mh-promo-publish-btn {
            width: 100%;
        }

        .mh-promo-form__code-field,
        .mh-promo-form__desc-field,
        .mh-promo-form__date-field,
        .mh-promo-form__discount-type,
        .mh-promo-form .service-custom-select.mh-promo-form__discount-type,
        .mh-promo-form__amount-field,
        .mh-promo-form__select-field {
            width: 100%;
            max-width: 100%;
        }

        .mh-promo-form__amount {
            width: 100%;
        }

        .mh-promo-form__validity {
            max-width: 100%;
        }

        .mh-promo-form__validity-dates {
            width: 100%;
        }

        .mh-promo-date-picker {
            width: 100%;
            left: 0;
            right: 0;
        }

        .mh-promo-date-picker .rdc {
            min-width: 0;
        }

        .mh-promo-date-picker .rdc-panel {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 720px) {
        .mh-promo-table {
            table-layout: auto;
            min-width: 640px;
        }
    }
</style>
