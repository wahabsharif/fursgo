@props(['profile'])

@php
$details = $profile['details'];
$pets = $profile['pets'];
$marketing = $profile['marketing'];
$verification = $profile['verification'];
$bookings = $profile['bookings'];
$sessions = $profile['sessions'];
$blocked = $profile['blocked_providers'];
$notes = $profile['notes'];
$activity = $profile['activity'];
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

    {{-- Personal Details --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header title="Personal Details">
            <button type="button" class="admin-co-link-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                    <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Edit
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

    {{-- Pets --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header :title="'Pets (' . count($pets) . ' Total)'">
            <button type="button" class="admin-co-link-btn" @click="detailTab = 'pets'">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                    <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                View All
            </button>
        </x-admin.customer.section-header>
        <div class="admin-co-pets-grid">
            @foreach (array_slice($pets, 0, 2) as $pet)
            <x-admin.customer.pet-card
                :name="$pet['name']"
                :meta="$pet['meta']"
                :status="$pet['status']"
                :status-label="$pet['status_label']"
                :vaccinated="$pet['vaccinated']"
                :sessions="$pet['sessions']"
                :type="$pet['type']"
                :image="$pet['image'] ?? null" />
            @endforeach
        </div>
    </section>

    {{-- Marketing + Verification --}}
    <div class="admin-co-split">
        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Marketing Consent" />
            <ul class="admin-co-status-list">
                @foreach ($marketing as $item)
                <li class="admin-co-status-row">
                    <span>{{ $item['label'] }}</span>
                    <span class="admin-co-consent is-{{ $item['enabled'] ? 'on' : 'off' }}">
                        @if ($item['enabled'])
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                        </svg>
                        Enabled
                        @else
                        Not Enabled
                        @endif
                    </span>
                </li>
                @endforeach
            </ul>
        </section>

        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Verification Status">
                <button type="button" class="admin-co-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                        <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Override Status
                </button>
            </x-admin.customer.section-header>
            <ul class="admin-co-status-list">
                @foreach ($verification['items'] as $item)
                <li class="admin-co-status-row">
                    <span>{{ $item['label'] }}</span>
                    @if (($item['type'] ?? 'check') === 'progress')
                    <span class="admin-co-verify-progress">{{ $item['value'] }}</span>
                    @elseif (($item['type'] ?? 'check') === 'text')
                    <span class="admin-co-verify-text">{{ $item['value'] }}</span>
                    @elseif ($item['ok'] ?? false)
                    <span class="admin-co-verify-ok">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                        </svg>
                        Verified
                    </span>
                    @else
                    <span class="admin-co-consent is-off">Not Enabled</span>
                    @endif
                </li>
                @endforeach
            </ul>
        </section>
    </div>

    {{-- Recent Bookings --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header title="Recent Bookings">
            <button type="button" class="admin-co-link-btn" @click="detailTab = 'bookings'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
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
                        <th>Service · Groomer / Space Host</th>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($bookings as $booking)
                    <tr>
                        <td>{{ $booking['id'] }}</td>
                        <td>
                            <span class="admin-co-booking-service">{{ $booking['service'] }}</span>
                            <span class="admin-co-booking-sep">·</span>
                            <span class="admin-co-booking-provider">{{ $booking['provider'] }}</span>
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

    {{-- Sessions + Blocked --}}
    <div class="admin-co-split">
        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Device & Session info">
                <button type="button" class="admin-co-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="15" viewBox="0 0 10 15" fill="none">
                        <path d="M8.89795 9.725L8.89795 11.625C8.89795 12.7296 8.00252 13.625 6.89795 13.625L2.62521 13.625C1.52065 13.625 0.625215 12.7296 0.625215 11.625L0.625216 2.625C0.625216 1.52043 1.52065 0.625 2.62522 0.625L6.89795 0.625C8.00252 0.625 8.89795 1.52043 8.89795 2.625L8.89795 4.525" stroke="#3B3731" stroke-width="1.25" stroke-linecap="round" />
                    </svg>
                    Revoke Access
                </button>
            </x-admin.customer.section-header>
            <ul class="admin-co-session-list">
                @foreach ($sessions as $session)
                <li class="admin-co-session-item">
                    <div class="admin-co-session-icon{{ !empty($session['warning']) ? ' has-warning' : '' }}" aria-hidden="true">
                        @if (($session['device'] ?? 'laptop') === 'phone')
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="20" viewBox="0 0 13 20" fill="none">
                            <rect x="11.75" y="0.75" width="18.5" height="11" rx="1.25" transform="rotate(90 11.75 0.75)" stroke="#3B3731" stroke-width="1.5" />
                            <path d="M4.72705 16.6362L7.72705 16.6362" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                        @elseif (($session['device'] ?? '') === 'desktop')
                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="16" viewBox="0 0 21 16" fill="none">
                            <rect x="0.75" y="0.75" width="18.8636" height="11.2273" rx="1.25" stroke="#3B3731" stroke-width="1.5" />
                            <path d="M7 15L13 15" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                            <path d="M9.25 15C9.25 15.4142 9.58579 15.75 10 15.75C10.4142 15.75 10.75 15.4142 10.75 15L10 15L9.25 15ZM10 15L10.75 15L10.75 12.5L10 12.5L9.25 12.5L9.25 15L10 15Z" fill="#3B3731" />
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                            <rect x="0.75" y="0.75" width="14.5" height="9" rx="1.5" stroke="#3B3731" stroke-width="1.2" />
                            <path d="M5 11.25H11" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                        </svg>
                        @endif
                        @if (!empty($session['warning']))
                        <span class="admin-co-session-warning">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="15" viewBox="0 0 18 15" fill="none" aria-hidden="true">
                                <path d="M9.53101 0.535238C9.11998 -0.178413 8.01236 -0.178413 7.60133 0.535238L0.129134 13.5183L0.0672303 13.6453C-0.173102 14.2451 0.256901 14.9057 0.943487 14.9911L1.09504 15H16.0373C16.8614 15 17.3895 14.1901 17.0032 13.5183L9.53101 0.535238Z" fill="#FFC97A" />
                                <path d="M8.40443 5.47978L8.60442 9.73045L8.80406 5.48152C8.8053 5.45436 8.80098 5.42723 8.79137 5.40179C8.78176 5.37635 8.76707 5.35314 8.74819 5.33358C8.72931 5.31401 8.70664 5.2985 8.68156 5.28799C8.65648 5.27749 8.62952 5.27221 8.60233 5.27247C8.57562 5.27273 8.54923 5.27834 8.52472 5.28897C8.50021 5.2996 8.47808 5.31503 8.45963 5.33436C8.44118 5.35368 8.42679 5.3765 8.41731 5.40148C8.40783 5.42646 8.40345 5.45308 8.40443 5.47978Z" fill="#3B3731" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.4707 11.1465C8.60269 11.1202 8.7399 11.1341 8.86426 11.1855C8.98873 11.2371 9.09605 11.3245 9.1709 11.4365C9.24567 11.5485 9.28516 11.6808 9.28516 11.8154C9.28504 11.9959 9.21359 12.1692 9.08594 12.2969C8.95829 12.4245 8.785 12.496 8.60449 12.4961C8.46984 12.4961 8.33757 12.4566 8.22559 12.3818C8.11356 12.307 8.02617 12.1997 7.97461 12.0752C7.9232 11.9508 7.90929 11.8136 7.93555 11.6816C7.96188 11.5496 8.02689 11.4282 8.12207 11.333C8.21725 11.2378 8.3387 11.1728 8.4707 11.1465Z" fill="#3B3731" stroke="#3B3731" stroke-width="0.03125" />
                            </svg>
                        </span>
                        @endif
                    </div>
                    <div class="admin-co-session-body">
                        <p class="admin-co-session-title">{{ $session['title'] }}</p>
                        <p class="admin-co-session-meta">{{ $session['location'] }} · {{ $session['ip'] }}</p>
                        <p class="admin-co-session-meta">{{ $session['time'] }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
            <p class="admin-co-panel-footnote">{{ count($sessions) }} active sessions · 2FA not enabled</p>
        </section>

        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header :title="'Blocked Providers (' . count($blocked) . ' blocked)'">
                <button type="button" class="admin-co-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                        <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                    </svg>
                    View All
                </button>
            </x-admin.customer.section-header>
            <ul class="admin-co-blocked-list">
                @foreach ($blocked as $provider)
                @php
                $roleKey = strtolower(str_replace(' ', '-', $provider['role']));
                @endphp
                <li class="admin-co-blocked-item">
                    <img src="{{ $provider['avatar'] }}" alt="" class="admin-co-blocked-avatar" width="36" height="36">
                    <div class="admin-co-blocked-body">
                        <p class="admin-co-blocked-name">
                            {{ $provider['name'] }}
                            <span class="admin-co-blocked-sep">·</span>
                            <span class="admin-co-blocked-role is-{{ $roleKey }}">{{ $provider['role'] }}</span>
                        </p>
                        <p class="admin-co-blocked-sub">{{ $provider['subtitle'] ?? '' }}</p>
                    </div>
                </li>
                @endforeach
            </ul>
            <p class="admin-co-panel-footnote">Blocked providers cannot be booked by this customer</p>
        </section>
    </div>

    {{-- Admin Notes --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header title="Admin Notes">
            <button type="button" class="admin-co-link-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                    <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                Add Notes
            </button>
        </x-admin.customer.section-header>
        <div class="admin-co-notes">
            @foreach ($notes as $note)
            <article class="admin-co-note">
                <p class="admin-co-note-text">{{ $note['text'] }}</p>
                <div class="admin-co-note-foot">
                    <span>{{ $note['author'] }}</span>
                    <span class="admin-co-note-dot">·</span>
                    <span>{{ $note['time'] }}</span>
                    @if (!empty($note['tag']))
                    <span class="admin-co-note-dot">·</span>
                    <span class="admin-co-note-tag">{{ $note['tag'] }}</span>
                    @endif
                </div>
            </article>
            @endforeach
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