@props(['profile'])

@php
$isDual = ! empty($profile['dual']);
$isSpace = ($profile['type'] ?? '') === 'space';
$groomerPack = $profile['groomer'] ?? null;
$spacePack = $profile['space'] ?? null;

$payouts = $profile['payouts'] ?? [];
$groomerPayouts = $isDual ? ($groomerPack['payouts'] ?? $payouts) : $payouts;
$spacePayouts = $isDual ? ($spacePack['payouts'] ?? $payouts) : $payouts;

$metrics = $payouts['metrics'] ?? [
    ['title' => 'Total earned', 'value' => '£652.00', 'note' => 'All time'],
    ['title' => 'Pending payout', 'value' => '£157.00', 'note' => 'Next pay cycle: 10 Jun 2025'],
    ['title' => 'On Hold', 'value' => '£52.00', 'note' => '1 open dispute'],
    ['title' => 'Refunded total', 'value' => '£70', 'note' => '2 refunds issued'],
];
$monthly = $payouts['monthly'] ?? [
    ['label' => 'Jan', 'value' => 90],
    ['label' => 'Feb', 'value' => 120],
    ['label' => 'Mar', 'value' => 140],
    ['label' => 'Apr', 'value' => 110],
    ['label' => 'May', 'value' => 160],
    ['label' => 'Jun', 'value' => 250, 'active' => true, 'display' => '£250'],
    ['label' => 'Jul', 'value' => 130],
    ['label' => 'Aug', 'value' => 100],
    ['label' => 'Sep', 'value' => 150],
    ['label' => 'Oct', 'value' => 125],
    ['label' => 'Nov', 'value' => 95],
    ['label' => 'Dec', 'value' => 80],
];
$maxMonthly = max(1, collect($monthly)->max('value'));

$groomerBookingDetails = $groomerPayouts['booking_details'] ?? $payouts['booking_details'] ?? [
    ['label' => 'Full Groom', 'percent' => 60, 'amount' => '£452.00', 'tone' => 'orange'],
    ['label' => 'Add-ons', 'percent' => 30, 'amount' => '£178.00', 'tone' => 'orange'],
    ['label' => 'Pet', 'percent' => 10, 'amount' => '£52.00', 'tone' => 'blue'],
];
$spaceBookingDetails = $spacePayouts['booking_details'] ?? [
    ['label' => 'Full-Day', 'percent' => 60, 'amount' => '£452.00', 'tone' => 'coral'],
    ['label' => 'Half-Day', 'percent' => 30, 'amount' => '£178.00', 'tone' => 'coral'],
    ['label' => 'Hourly', 'percent' => 10, 'amount' => '£52.00', 'tone' => 'blue'],
];
$growthNote = $payouts['growth_note'] ?? '+18% growth over last 12 weeks';

$groomerBank = $groomerPayouts['bank_settings'] ?? $payouts['bank_settings'] ?? [
    ['label' => 'Account holder', 'value' => $profile['name'] ?? 'Pawfect Grooming'],
    ['label' => 'Bank', 'value' => 'Barclays'],
    ['label' => 'Account number', 'value' => '•••• •••• •••• 4821'],
    ['label' => 'Sort code', 'value' => '** - ** - 42'],
    ['label' => 'Payout frequency', 'value' => 'Weekly · every Tuesday'],
    ['label' => 'Next payout', 'value' => '10 Jun 2025 · £1,054 pending'],
    ['label' => 'Bank verified', 'value' => 'Verified', 'verified' => true],
];
$spaceBank = $spacePayouts['bank_settings'] ?? [
    ['label' => 'Account holder', 'value' => 'Sarah Williams'],
    ['label' => 'Bank', 'value' => 'Barclays'],
    ['label' => 'Account number', 'value' => '•••• •••• •••• 4821'],
    ['label' => 'Sort code', 'value' => '** - ** - 42'],
    ['label' => 'Payout frequency', 'value' => 'Weekly · every Tuesday'],
    ['label' => 'Next payout', 'value' => '10 Jun 2025 · £1,054 pending'],
    ['label' => 'Bank verified', 'value' => 'Verified', 'verified' => true],
];

$subTabs = [
    'overview' => 'Earnings overview',
    'transactions' => 'Transactions',
    'payouts' => 'Payouts',
    'invoices' => 'Invoices',
];
@endphp

<div
    class="admin-bp-payouts"
    :class="{ 'is-space-view': dual ? viewAs === 'space' : {{ $isSpace ? 'true' : 'false' }} }"
    @admin-bp-open-payment="openPayment($event.detail)">
    <div class="admin-co-overview-head" x-show="!selectedPayment" x-cloak>
        <h2 class="admin-page-title mb-0" x-text="({
            overview: 'Earnings overview',
            transactions: 'Transactions',
            payouts: 'Payouts',
            invoices: 'Invoices',
        })[payoutsSubTab] || 'Earnings overview'"></h2>
        <div class="admin-co-pay-head-actions">
            <button type="button" class="admin-btn-outline" x-show="payoutsSubTab === 'invoices'" x-cloak>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16" fill="none" aria-hidden="true">
                    <path d="M8.5 0.75H3.25C2.91848 0.75 2.60054 0.881696 2.36612 1.11612C2.1317 1.35054 2 1.66848 2 2V14C2 14.3315 2.1317 14.6495 2.36612 14.8839C2.60054 15.1183 2.91848 15.25 3.25 15.25H10.75C11.0815 15.25 11.3995 15.1183 11.6339 14.8839C11.8683 14.6495 12 14.3315 12 14V4.25L8.5 0.75Z" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M8.5 0.75V4.25H12" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4.75 8.25H9.25M4.75 11H9.25M4.75 5.5H6.5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                </svg>
                Statement PDF
            </button>
            <button type="button" class="admin-btn-dark">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                    <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                </svg>
                Export Data
            </button>
        </div>
    </div>

    <nav class="admin-bp-profile-subnav admin-bp-payouts-subnav" aria-label="Payouts sections" x-show="!selectedPayment" x-cloak>
        @foreach ($subTabs as $subKey => $subLabel)
        <button
            type="button"
            class="admin-bp-profile-subnav-btn"
            :class="{ 'is-active': payoutsSubTab === '{{ $subKey }}' }"
            @click="payoutsSubTab = '{{ $subKey }}'">
            {{ $subLabel }}
        </button>
        @endforeach
    </nav>

    {{-- Earnings overview --}}
    <div class="admin-bp-payouts-panels" x-show="!selectedPayment && payoutsSubTab === 'overview'" x-cloak>
        <div class="admin-bp-payouts-metrics">
            @foreach ($metrics as $metric)
            <div class="admin-bp-payouts-metric-card">
                <p class="admin-bp-payouts-metric-title">{{ $metric['title'] }}</p>
                <p class="admin-bp-payouts-metric-value">{{ $metric['value'] }}</p>
                <p class="admin-bp-payouts-metric-note">{{ $metric['note'] }}</p>
            </div>
            @endforeach
        </div>

        <section class="admin-card admin-co-panel admin-bp-payouts-chart-panel">
            <div class="admin-co-section-head admin-bp-payouts-chart-head">
                <h3 class="admin-co-section-title">Monthly earnings</h3>
                <div class="admin-bp-payouts-chart-ranges" role="tablist" aria-label="Chart range">
                    <button type="button" class="admin-bp-payouts-range" :class="{ 'is-active': chartRange === 'week' }" @click="chartRange = 'week'">Week</button>
                    <button type="button" class="admin-bp-payouts-range" :class="{ 'is-active': chartRange === 'month' }" @click="chartRange = 'month'">Month</button>
                    <button type="button" class="admin-bp-payouts-range" :class="{ 'is-active': chartRange === 'year' }" @click="chartRange = 'year'">Year</button>
                </div>
            </div>

            <div class="admin-bp-payouts-chart" aria-label="Monthly earnings chart">
                @foreach ($monthly as $bar)
                @php
                    $height = max(8, (int) round(($bar['value'] / $maxMonthly) * 100));
                    $isActive = ! empty($bar['active']);
                @endphp
                <div class="admin-bp-payouts-bar-col {{ $isActive ? 'is-active' : '' }}">
                    <div class="admin-bp-payouts-bar-track">
                        <div class="admin-bp-payouts-bar-stack" style="height: {{ $height }}%;">
                            @if ($isActive && ! empty($bar['display']))
                            <span class="admin-bp-payouts-bar-tip" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="22" viewBox="0 0 36 22" fill="none">
                                    <mask id="path-1-inside-1_payouts_bar_tip" fill="white">
                                        <path d="M31 0C33.7614 1.28851e-07 36 2.23858 36 5V12C36 14.7614 33.7614 17 31 17H20.8867L18 22L15.1133 17H5C2.23858 17 1.12749e-07 14.7614 0 12V5C0 2.23858 2.23858 1.28851e-07 5 0H31Z"/>
                                    </mask>
                                    <path d="M31 0C33.7614 1.28851e-07 36 2.23858 36 5V12C36 14.7614 33.7614 17 31 17H20.8867L18 22L15.1133 17H5C2.23858 17 1.12749e-07 14.7614 0 12V5C0 2.23858 2.23858 1.28851e-07 5 0H31Z" fill="#FFF"/>
                                    <path d="M31 0V-0.5V-0.5V0ZM36 5H36.5V5H36ZM31 17V17.5V17.5V17ZM20.8867 17V16.5H20.598L20.4537 16.75L20.8867 17ZM18 22L17.567 22.25L18 23L18.433 22.25L18 22ZM15.1133 17L15.5463 16.75L15.402 16.5H15.1133V17ZM5 17V17.5V17.5V17ZM0 12H-0.5V12H0ZM5 0V-0.5V-0.5V0ZM31 0V0.5C33.4853 0.5 35.5 2.51472 35.5 5H36H36.5C36.5 1.96243 34.0376 -0.5 31 -0.5V0ZM36 5H35.5V12H36H36.5V5H36ZM36 12H35.5C35.5 14.4853 33.4853 16.5 31 16.5V17V17.5C34.0376 17.5 36.5 15.0376 36.5 12H36ZM31 17V16.5H20.8867V17V17.5H31V17ZM20.8867 17L20.4537 16.75L17.567 21.75L18 22L18.433 22.25L21.3197 17.25L20.8867 17ZM18 22L18.433 21.75L15.5463 16.75L15.1133 17L14.6803 17.25L17.567 22.25L18 22ZM15.1133 17V16.5H5V17V17.5H15.1133V17ZM5 17V16.5C2.51472 16.5 0.5 14.4853 0.5 12H0H-0.5C-0.5 15.0376 1.96243 17.5 5 17.5V17ZM0 12H0.5V5H0H-0.5V12H0ZM0 5H0.5C0.5 2.51472 2.51472 0.5 5 0.5V0V-0.5C1.96243 -0.5 -0.5 1.96243 -0.5 5H0ZM5 0V0.5H31V0V-0.5H5V0Z" fill="#F4F4F4" mask="url(#path-1-inside-1_payouts_bar_tip)"/>
                                </svg>
                                <span class="admin-bp-payouts-bar-tip-value">{{ $bar['display'] }}</span>
                            </span>
                            @endif
                            <span
                                class="admin-bp-payouts-bar {{ $isActive ? 'is-active' : '' }}"
                                title="{{ $bar['label'] }}: £{{ number_format($bar['value']) }}"></span>
                        </div>
                    </div>
                    <span class="admin-bp-payouts-bar-label">{{ $bar['label'] }}</span>
                </div>
                @endforeach
            </div>
        </section>

        <section class="admin-card admin-co-panel admin-bp-payouts-booking-panel">
            <div class="admin-co-section-head admin-bp-payouts-booking-head">
                <h3 class="admin-co-section-title">Booking details</h3>
                <p class="admin-bp-payouts-growth">{{ $growthNote }}</p>
            </div>

            <ul class="admin-bp-payouts-booking-list" x-show="dual ? viewAs === 'groomer' : {{ $isSpace ? 'false' : 'true' }}" x-cloak>
                @foreach ($groomerBookingDetails as $row)
                <li class="admin-bp-payouts-booking-row">
                    <span class="admin-bp-payouts-booking-label">{{ $row['label'] }}</span>
                    <div class="admin-bp-payouts-progress" aria-hidden="true">
                        <span
                            class="admin-bp-payouts-progress-fill is-{{ $row['tone'] ?? 'orange' }}"
                            style="width: {{ (int) $row['percent'] }}%;"></span>
                    </div>
                    <span class="admin-bp-payouts-booking-percent">{{ (int) $row['percent'] }}%</span>
                    <span class="admin-bp-payouts-booking-amount">{{ $row['amount'] }}</span>
                </li>
                @endforeach
            </ul>

            <ul class="admin-bp-payouts-booking-list" x-show="dual ? viewAs === 'space' : {{ $isSpace ? 'true' : 'false' }}" x-cloak>
                @foreach ($spaceBookingDetails as $row)
                <li class="admin-bp-payouts-booking-row">
                    <span class="admin-bp-payouts-booking-label">{{ $row['label'] }}</span>
                    <div class="admin-bp-payouts-progress" aria-hidden="true">
                        <span
                            class="admin-bp-payouts-progress-fill is-{{ $row['tone'] ?? 'coral' }}"
                            style="width: {{ (int) $row['percent'] }}%;"></span>
                    </div>
                    <span class="admin-bp-payouts-booking-percent">{{ (int) $row['percent'] }}%</span>
                    <span class="admin-bp-payouts-booking-amount">{{ $row['amount'] }}</span>
                </li>
                @endforeach
            </ul>
        </section>

        <section class="admin-card admin-co-panel admin-bp-payouts-bank-panel">
            <div class="admin-co-section-head">
                <h3 class="admin-co-section-title">Bank &amp; payout settings</h3>
                <button type="button" class="admin-co-link-btn" @click="payoutsSubTab = 'payouts'">
                    View payouts
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10 1L4 7M10 1H6M10 1V5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <dl class="admin-co-details admin-bp-payouts-bank" x-show="dual ? viewAs === 'groomer' : {{ $isSpace ? 'false' : 'true' }}" x-cloak>
                @foreach ($groomerBank as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd class="{{ ! empty($row['verified']) ? 'is-verified' : '' }}">
                        @if (! empty($row['verified']))
                        <span class="admin-bp-po-verified">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            {{ $row['value'] }}
                        </span>
                        @else
                        {{ $row['value'] }}
                        @endif
                    </dd>
                </div>
                @endforeach
            </dl>

            <dl class="admin-co-details admin-bp-payouts-bank" x-show="dual ? viewAs === 'space' : {{ $isSpace ? 'true' : 'false' }}" x-cloak>
                @foreach ($spaceBank as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd class="{{ ! empty($row['verified']) ? 'is-verified' : '' }}">
                        @if (! empty($row['verified']))
                        <span class="admin-bp-po-verified">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            {{ $row['value'] }}
                        </span>
                        @else
                        {{ $row['value'] }}
                        @endif
                    </dd>
                </div>
                @endforeach
            </dl>
        </section>
    </div>

    <div class="admin-bp-payouts-panels" x-show="!selectedPayment && payoutsSubTab === 'transactions'" x-cloak>
        @if ($isDual)
        <div x-show="viewAs === 'groomer'" x-cloak>
            <x-admin.provider.payouts-transactions :profile="$profile" variant="groomer" />
        </div>
        <div x-show="viewAs === 'space'" x-cloak>
            <x-admin.provider.payouts-transactions :profile="$profile" variant="space" />
        </div>
        @else
        <x-admin.provider.payouts-transactions :profile="$profile" :variant="$isSpace ? 'space' : 'groomer'" />
        @endif
    </div>

    <div class="admin-bp-payouts-panels" x-show="!selectedPayment && payoutsSubTab === 'payouts'" x-cloak>
        @if ($isDual)
        <div x-show="viewAs === 'groomer'" x-cloak>
            <x-admin.provider.payouts-list :profile="$profile" variant="groomer" />
        </div>
        <div x-show="viewAs === 'space'" x-cloak>
            <x-admin.provider.payouts-list :profile="$profile" variant="space" />
        </div>
        @else
        <x-admin.provider.payouts-list :profile="$profile" :variant="$isSpace ? 'space' : 'groomer'" />
        @endif
    </div>

    <div class="admin-bp-payouts-panels" x-show="!selectedPayment && payoutsSubTab === 'invoices'" x-cloak>
        @if ($isDual)
        <div x-show="viewAs === 'groomer'" x-cloak>
            <x-admin.provider.payouts-invoices :profile="$profile" variant="groomer" />
        </div>
        <div x-show="viewAs === 'space'" x-cloak>
            <x-admin.provider.payouts-invoices :profile="$profile" variant="space" />
        </div>
        @else
        <x-admin.provider.payouts-invoices :profile="$profile" :variant="$isSpace ? 'space' : 'groomer'" />
        @endif
    </div>

    <x-admin.customer.payment-detail :profile="$profile" />
</div>
