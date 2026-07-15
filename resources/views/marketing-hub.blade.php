@php
    use App\Support\MarketingHubNav;

    $dashboardNav = MarketingHubNav::fromSession();
    $mh = $mh ?? \App\Support\MarketingHubStats::empty();
    $isSpaceUser = strtolower((string) (auth('groomer_spacer')->user()?->user_type ?? '')) === 'space';
    $serviceColors = ['#FBAC83', '#FDD0B3', '#FFF4E4'];
    $petColors = ['#D8E8B7', 'rgba(216, 232, 183, 0.60)', 'rgba(216, 232, 183, 0.20)'];
    $mhChartData = [
        'peakBookings' => $mh['peak_bookings'],
        'timeLabels' => $mh['time_labels'],
        'services' => $mh['services']['values'],
        'pets' => $isSpaceUser ? [] : $mh['pets']['values'],
    ];
@endphp

<section class="dashboard-content-wrapper marketing-hub-content">
    <div class="active-section-header" x-cloak>
        <template x-if="activeSection === 'marketing-hub'">
            <div>
                <h2>Marketing Hub</h2>
            </div>
        </template>

        <template x-if="activeSection === 'promo-creation'">
            <div>
                <h2>Promo Creation</h2>
            </div>
        </template>

        <template x-if="activeSection === 'settings'">
            <div>
                <h2>Settings</h2>
            </div>
        </template>
    </div>

    <div class="marketing-hub-panels">
        <div x-show="activeSection === 'marketing-hub'" x-cloak
            x-effect="if (activeSection === 'marketing-hub') { $nextTick(() => window.__initMarketingHubCharts?.()) }">
            <script>
                window.__mhChartData = @json($mhChartData);
            </script>
            <div class="mh-dashboard">
                {{-- Performance Snapshot --}}
                <div class="mh-section">
                    <h3 class="mh-section-title">Performance Snapshot</h3>
                    <div class="mh-kpi-row">
                        <article class="mh-kpi-card">
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
                        <article class="mh-kpi-card">
                            <p class="mh-kpi-label">New Clients</p>
                            <p class="mh-kpi-value">{{ $mh['kpis']['new_clients']['value'] }}</p>
                            <span
                                class="mh-kpi-pill mh-kpi-pill--peach">{{ $mh['kpis']['new_clients']['sublabel'] }}</span>
                        </article>
                        <article class="mh-kpi-card">
                            <p class="mh-kpi-label">Booking Conversion</p>
                            <p class="mh-kpi-value">{{ $mh['kpis']['booking_conversion']['value'] }}</p>
                            <span
                                class="mh-kpi-pill mh-kpi-pill--pink">{{ $mh['kpis']['booking_conversion']['sublabel'] }}</span>
                        </article>
                        <article class="mh-kpi-card">
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
                        <article class="mh-kpi-card">
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

                    <div class="mh-tips-banner" role="note">
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
                    <article class="mh-card mh-card--chart">
                        <div class="mh-card-header">
                            <h3 class="mh-card-title">Peak Bookings Times per Day</h3>
                            <div class="mh-day-switcher" aria-label="Select day">
                                <button type="button" id="mhPeakDayPrev" class="mh-day-btn" aria-label="Previous day">
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

                    <article class="mh-card">
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
                        <article class="mh-card">
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

                    <article class="mh-card">
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

        <div x-show="activeSection === 'promo-creation'" x-cloak>
            <p class="marketing-hub-placeholder">Promo Creation tools coming soon.</p>
        </div>

        <div x-show="activeSection === 'settings'" x-cloak>
            <p class="marketing-hub-placeholder">Marketing settings coming soon.</p>
        </div>
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
        gap: 0.875rem;
    }

    .mh-kpi-card {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
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
</style>
