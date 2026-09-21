@props(['profile'])

@php
$isDual = ! empty($profile['dual']);
$isSpace = ($profile['type'] ?? '') === 'space';
$snapshot = $profile['snapshot'] ?? [];
$details = $profile['details'] ?? [];

$groomerLocationTypes = $isDual
    ? ($profile['groomer']['location_types'] ?? [])
    : ($profile['location_types'] ?? []);
$groomerServices = $isDual
    ? ($profile['groomer']['services'] ?? [])
    : ($profile['services'] ?? []);
$groomerPetPreferences = $isDual
    ? ($profile['groomer']['pet_preferences'] ?? [])
    : ($profile['pet_preferences'] ?? []);

$serviceAreas = $isDual
    ? ($profile['space']['service_areas'] ?? [])
    : ($profile['service_areas'] ?? []);
$spaceServices = $isDual
    ? ($profile['space']['services'] ?? [])
    : ($profile['services'] ?? []);
$spacePetPreferences = $isDual
    ? ($profile['space']['pet_preferences'] ?? [])
    : ($profile['pet_preferences'] ?? []);

$bookings = $profile['bookings'] ?? [];
$payout = $profile['payout'] ?? [];
$notes = $profile['notes'] ?? [];
$activity = $profile['activity'] ?? [];
$areaCount = count($serviceAreas);
$showGroomerOfferings = $isDual || ! $isSpace;
$showSpaceOfferings = $isDual || $isSpace;
@endphp

<div class="admin-co-overview">
    <div class="admin-co-overview-head">
        <h2 class="admin-page-title mb-0">Overview</h2>
        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    {{-- Performance Snapshot --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header title="Performance Snapshot" />
        <div class="admin-po-metric-row admin-bp-snapshot-row">
            @foreach ($snapshot as $metric)
            <div class="admin-card admin-po-metric-card">
                <p class="admin-card-title">{{ $metric['title'] }}</p>
                <p class="admin-card-value">
                    @if (! empty($metric['rating']))
                    <span class="admin-bp-rating">
                        {{ $metric['value'] }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" fill="#3B3731" />
                        </svg>
                    </span>
                    @else
                    {{ $metric['value'] }}
                    @endif
                </p>
                @if (! empty($metric['badge']))
                <div class="admin-metric-foot">
                    <span class="admin-pill-badge {{ $metric['badge_class'] ?? 'up' }}">{{ $metric['badge'] }}</span>
                    <span class="admin-metric-note">{{ $metric['note'] ?? '' }}</span>
                </div>
                @else
                <p class="admin-card-meta muted mb-0">{{ $metric['note'] ?? '' }}</p>
                @endif
            </div>
            @endforeach
        </div>
    </section>

    {{-- Business Details --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header title="Business Details">
            <button type="button" class="admin-co-link-btn" @click="detailTab = 'profile'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M10.0284 0.00548691L10.0229 6.35299L9.19447 6.33653C9.02623 6.33653 8.90736 6.28715 8.83787 6.1884C8.76838 6.08234 8.7318 5.95067 8.72814 5.7934L8.73912 3.11615C8.73912 2.92596 8.7446 2.74857 8.75558 2.58399C8.76289 2.41575 8.77569 2.2603 8.79398 2.11766C8.60379 2.35906 8.39897 2.60776 8.17953 2.86378C7.96008 3.11249 7.72966 3.35754 7.48827 3.59893L1.29164 9.79556C0.996218 10.091 0.517251 10.091 0.221833 9.79556C-0.073585 9.50015 -0.0735852 9.02118 0.221833 8.72576L6.41847 2.52913C6.65986 2.28774 6.90856 2.05732 7.16459 1.83787C7.41695 1.61476 7.66566 1.40995 7.9107 1.22342C7.76441 1.24536 7.60896 1.26182 7.44438 1.27279C7.27614 1.28011 7.09692 1.28377 6.90673 1.28376L4.20754 1.29474C4.05393 1.29474 3.92409 1.25999 3.81802 1.1905C3.71561 1.11735 3.66441 0.996656 3.66441 0.828412L3.64795 3.3782e-07L10.0284 0.00548691Z" fill="#3B3731" />
                </svg>
                View
            </button>
        </x-admin.customer.section-header>
        <dl class="admin-co-details">
            @foreach ($details as $row)
            <div class="admin-co-details-row">
                <dt>{{ $row['label'] }}</dt>
                <dd>{{ $row['value'] }}</dd>
            </div>
            @endforeach
        </dl>
    </section>

    @if ($showGroomerOfferings)
    {{-- Groomer: Location Types · Services · Pet preferences --}}
    <section class="admin-card admin-co-panel admin-bp-offerings-panel" x-show="!dual || viewAs === 'groomer'" @if ($isDual) x-cloak @endif>
        <div class="admin-bp-offerings-block">
            <x-admin.customer.section-header title="Location Types">
                <button type="button" class="admin-co-link-btn" @click="detailTab = 'profile'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0284 0.00548691L10.0229 6.35299L9.19447 6.33653C9.02623 6.33653 8.90736 6.28715 8.83787 6.1884C8.76838 6.08234 8.7318 5.95067 8.72814 5.7934L8.73912 3.11615C8.73912 2.92596 8.7446 2.74857 8.75558 2.58399C8.76289 2.41575 8.77569 2.2603 8.79398 2.11766C8.60379 2.35906 8.39897 2.60776 8.17953 2.86378C7.96008 3.11249 7.72966 3.35754 7.48827 3.59893L1.29164 9.79556C0.996218 10.091 0.517251 10.091 0.221833 9.79556C-0.073585 9.50015 -0.0735852 9.02118 0.221833 8.72576L6.41847 2.52913C6.65986 2.28774 6.90856 2.05732 7.16459 1.83787C7.41695 1.61476 7.66566 1.40995 7.9107 1.22342C7.76441 1.24536 7.60896 1.26182 7.44438 1.27279C7.27614 1.28011 7.09692 1.28377 6.90673 1.28376L4.20754 1.29474C4.05393 1.29474 3.92409 1.25999 3.81802 1.1905C3.71561 1.11735 3.66441 0.996656 3.66441 0.828412L3.64795 3.3782e-07L10.0284 0.00548691Z" fill="#3B3731" />
                    </svg>
                    View
                </button>
            </x-admin.customer.section-header>
            <div class="admin-bp-loc-tags">
                @foreach ($groomerLocationTypes as $tag)
                <span class="admin-bp-loc-tag">{{ $tag }}</span>
                @endforeach
            </div>
        </div>

        <div class="admin-bp-offerings-block">
            <x-admin.customer.section-header title="Services Offered" />
            <ul class="admin-co-status-list">
                @foreach ($groomerServices as $service)
                <li class="admin-co-status-row">
                    <span class="admin-bp-service-label">
                        {{ $service['name'] }}
                        @if (! empty($service['meta']))
                        <span class="admin-bp-service-sep" aria-hidden="true">·</span>
                        <span class="admin-bp-service-meta">{{ $service['meta'] }}</span>
                        @endif
                    </span>
                    <span class="admin-bp-service-price">From {{ $service['price'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="admin-bp-offerings-block">
            <x-admin.customer.section-header title="Pet preferences" />
            <dl class="admin-co-details">
                @foreach ($groomerPetPreferences as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                </div>
                @endforeach
            </dl>
        </div>
    </section>
    @endif

    @if ($showSpaceOfferings)
    {{-- Space: Location & Service Areas --}}
    <section class="admin-card admin-co-panel" x-show="!dual || viewAs === 'space'" @if ($isDual) x-cloak @endif>
        <x-admin.customer.section-header title="Location & Service Areas ({{ $areaCount }} {{ Str::plural('area', $areaCount) }})">
            <button type="button" class="admin-co-link-btn" @click="detailTab = 'profile'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M10.0284 0.00548691L10.0229 6.35299L9.19447 6.33653C9.02623 6.33653 8.90736 6.28715 8.83787 6.1884C8.76838 6.08234 8.7318 5.95067 8.72814 5.7934L8.73912 3.11615C8.73912 2.92596 8.7446 2.74857 8.75558 2.58399C8.76289 2.41575 8.77569 2.2603 8.79398 2.11766C8.60379 2.35906 8.39897 2.60776 8.17953 2.86378C7.96008 3.11249 7.72966 3.35754 7.48827 3.59893L1.29164 9.79556C0.996218 10.091 0.517251 10.091 0.221833 9.79556C-0.073585 9.50015 -0.0735852 9.02118 0.221833 8.72576L6.41847 2.52913C6.65986 2.28774 6.90856 2.05732 7.16459 1.83787C7.41695 1.61476 7.66566 1.40995 7.9107 1.22342C7.76441 1.24536 7.60896 1.26182 7.44438 1.27279C7.27614 1.28011 7.09692 1.28377 6.90673 1.28376L4.20754 1.29474C4.05393 1.29474 3.92409 1.25999 3.81802 1.1905C3.71561 1.11735 3.66441 0.996656 3.66441 0.828412L3.64795 3.3782e-07L10.0284 0.00548691Z" fill="#3B3731" />
                </svg>
                View on Map
            </button>
        </x-admin.customer.section-header>

        <div class="admin-bp-areas-list">
            @foreach ($serviceAreas as $area)
            <div class="admin-bp-area-block">
                <div class="admin-bp-area-card">
                    <div class="admin-bp-area-info">
                        <p class="admin-bp-area-name">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="15" viewBox="0 0 11 15" fill="none">
                                <path d="M5.5 7.125C4.97904 7.125 4.47942 6.92746 4.11104 6.57583C3.74267 6.22419 3.53571 5.74728 3.53571 5.25C3.53571 4.75272 3.74267 4.27581 4.11104 3.92417C4.47942 3.57254 4.97904 3.375 5.5 3.375C6.02096 3.375 6.52058 3.57254 6.88896 3.92417C7.25733 4.27581 7.46429 4.75272 7.46429 5.25C7.46429 5.49623 7.41348 5.74005 7.31476 5.96753C7.21605 6.19502 7.07136 6.40172 6.88896 6.57583C6.70656 6.74994 6.49002 6.88805 6.2517 6.98227C6.01338 7.0765 5.75795 7.125 5.5 7.125ZM5.5 0C4.04131 0 2.64236 0.553123 1.61091 1.53769C0.579463 2.52226 0 3.85761 0 5.25C0 9.1875 5.5 15 5.5 15C5.5 15 11 9.1875 11 5.25C11 3.85761 10.4205 2.52226 9.38909 1.53769C8.35764 0.553123 6.95869 0 5.5 0Z" fill="#FFC97A" />
                            </svg>
                            {{ $area['name'] }}
                        </p>
                        <p class="admin-bp-area-address">{!! nl2br(e($area['address'])) !!}</p>
                        @if (! empty($area['availability']))
                        <p class="admin-bp-area-availability">{{ $area['availability'] }}</p>
                        @endif
                    </div>
                    <div class="admin-bp-area-map" aria-hidden="true">
                        <img src="{{ $area['map'] ?? asset('images/admin/provider/map-southwark.png') }}" alt="">
                    </div>
                </div>
                <dl class="admin-co-details admin-bp-area-details">
                    @if (! empty($area['location']))
                    <div class="admin-co-details-row">
                        <dt>Location</dt>
                        <dd>{{ $area['location'] }}</dd>
                    </div>
                    @endif
                    @if (! empty($area['accessibility']))
                    <div class="admin-co-details-row">
                        <dt>Accessibility</dt>
                        <dd>{{ $area['accessibility'] }}</dd>
                    </div>
                    @endif
                </dl>
            </div>
            @endforeach
        </div>
    </section>

    {{-- Space: Services Offered + Pet preferences --}}
    <section class="admin-card admin-co-panel admin-bp-offerings-panel" x-show="!dual || viewAs === 'space'" @if ($isDual) x-cloak @endif>
        <div class="admin-bp-offerings-block">
            <x-admin.customer.section-header title="Services Offered">
                <button type="button" class="admin-co-link-btn" @click="detailTab = 'profile'">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0284 0.00548691L10.0229 6.35299L9.19447 6.33653C9.02623 6.33653 8.90736 6.28715 8.83787 6.1884C8.76838 6.08234 8.7318 5.95067 8.72814 5.7934L8.73912 3.11615C8.73912 2.92596 8.7446 2.74857 8.75558 2.58399C8.76289 2.41575 8.77569 2.2603 8.79398 2.11766C8.60379 2.35906 8.39897 2.60776 8.17953 2.86378C7.96008 3.11249 7.72966 3.35754 7.48827 3.59893L1.29164 9.79556C0.996218 10.091 0.517251 10.091 0.221833 9.79556C-0.073585 9.50015 -0.0735852 9.02118 0.221833 8.72576L6.41847 2.52913C6.65986 2.28774 6.90856 2.05732 7.16459 1.83787C7.41695 1.61476 7.66566 1.40995 7.9107 1.22342C7.76441 1.24536 7.60896 1.26182 7.44438 1.27279C7.27614 1.28011 7.09692 1.28377 6.90673 1.28376L4.20754 1.29474C4.05393 1.29474 3.92409 1.25999 3.81802 1.1905C3.71561 1.11735 3.66441 0.996656 3.66441 0.828412L3.64795 3.3782e-07L10.0284 0.00548691Z" fill="#3B3731" />
                    </svg>
                    View
                </button>
            </x-admin.customer.section-header>
            <ul class="admin-co-status-list">
                @foreach ($spaceServices as $service)
                <li class="admin-co-status-row">
                    <span class="admin-bp-service-label">{{ $service['name'] }}</span>
                    <span class="admin-bp-service-price">{{ $service['price'] }}</span>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="admin-bp-offerings-block">
            <x-admin.customer.section-header title="Pet preferences" />
            <dl class="admin-co-details">
                @foreach ($spacePetPreferences as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                </div>
                @endforeach
            </dl>
        </div>
    </section>
    @endif

    {{-- Recent Bookings --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header title="Recent Bookings">
            <button type="button" class="admin-co-link-btn" @click="detailTab = 'bookings'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                </svg>
                View All
            </button>
        </x-admin.customer.section-header>
        <div class="admin-table-wrap">
            <table class="admin-live-table admin-co-bookings-table">
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Service</th>
                        <th>Customer · User no.</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                    <tr>
                        <td>{{ $booking['id'] }}</td>
                        <td>{{ $booking['service'] }}</td>
                        <td>
                            <span class="admin-co-booking-service">{{ $booking['customer'] }}</span>
                            <span class="admin-co-booking-sep">·</span>
                            <span class="admin-co-booking-provider">{{ $booking['user_no'] }}</span>
                        </td>
                        <td>{{ $booking['date'] }}</td>
                        <td>{{ $booking['amount'] }}</td>
                        <td>
                            <span class="admin-co-booking-status is-{{ $booking['status'] }}">
                                {{ $booking['status_label'] }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </section>

    {{-- Payout Summary --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header title="Payout Summary">
            <button type="button" class="admin-co-link-btn" @click="detailTab = 'payouts'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M10.0284 0.00548691L10.0229 6.35299L9.19447 6.33653C9.02623 6.33653 8.90736 6.28715 8.83787 6.1884C8.76838 6.08234 8.7318 5.95067 8.72814 5.7934L8.73912 3.11615C8.73912 2.92596 8.7446 2.74857 8.75558 2.58399C8.76289 2.41575 8.77569 2.2603 8.79398 2.11766C8.60379 2.35906 8.39897 2.60776 8.17953 2.86378C7.96008 3.11249 7.72966 3.35754 7.48827 3.59893L1.29164 9.79556C0.996218 10.091 0.517251 10.091 0.221833 9.79556C-0.073585 9.50015 -0.0735852 9.02118 0.221833 8.72576L6.41847 2.52913C6.65986 2.28774 6.90856 2.05732 7.16459 1.83787C7.41695 1.61476 7.66566 1.40995 7.9107 1.22342C7.76441 1.24536 7.60896 1.26182 7.44438 1.27279C7.27614 1.28011 7.09692 1.28377 6.90673 1.28376L4.20754 1.29474C4.05393 1.29474 3.92409 1.25999 3.81802 1.1905C3.71561 1.11735 3.66441 0.996656 3.66441 0.828412L3.64795 3.3782e-07L10.0284 0.00548691Z" fill="#3B3731" />
                </svg>
                View
            </button>
        </x-admin.customer.section-header>
        <dl class="admin-co-details">
            @foreach ($payout as $row)
            <div class="admin-co-details-row">
                <dt>{{ $row['label'] }}</dt>
                <dd @class(['is-muted' => ! empty($row['muted'])])>
                    @if (! empty($row['status']))
                    <span class="admin-po-status is-{{ $row['status'] }} has-dot">{{ $row['value'] }}</span>
                    @else
                    {{ $row['value'] }}
                    @endif
                </dd>
            </div>
            @endforeach
        </dl>
    </section>

    {{-- Admin Notes --}}
    <section
        class="admin-card admin-co-panel"
        :class="{ 'is-editing': editingNotes }"
        x-data="{
            editingNotes: false,
            newNote: '',
            editIndex: -1,
            notes: @js($notes),
        }">
        <div x-show="!editingNotes">
            <x-admin.customer.section-header title="Admin Notes">
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
                <label class="admin-co-note-compose-label" for="bp-new-note-{{ $profile['id'] }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                <textarea
                    id="bp-new-note-{{ $profile['id'] }}"
                    class="admin-co-note-textarea"
                    rows="4"
                    placeholder="Write an internal note about this provider - only visible to the admin team members."
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

    {{-- Recent Activity --}}
    <section class="admin-card admin-co-panel admin-co-activity-panel">
        <x-admin.customer.section-header title="Recent Activity">
            <button type="button" class="admin-co-link-btn" @click="detailTab = 'activity'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                </svg>
                Full Log
            </button>
        </x-admin.customer.section-header>
        <ul class="admin-co-activity">
            @foreach ($activity as $event)
            <li class="admin-co-activity-item">
                <span class="admin-co-activity-dot is-{{ $event['type'] }}" aria-hidden="true"></span>
                <div class="admin-co-activity-body">
                    <p class="admin-co-activity-title">{{ $event['title'] }}</p>
                    <time class="admin-co-activity-time">{{ $event['time'] }}</time>
                </div>
            </li>
            @endforeach
        </ul>
    </section>
</div>
