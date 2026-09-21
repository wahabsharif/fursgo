@php
$fallbackAvatar = asset('images/groomer-profile.png');

$pawfectProfile = [
    'id' => 'PRV-001',
    'name' => 'Pawfect Grooming',
    'owner' => 'Sarah Smith',
    'status' => 'active',
    'status_class' => 'active',
    'status_label' => 'Active',
    'type' => 'groomer',
    'type_label' => 'Groomer',
    'insurance_badge' => 'Freelance',
    'insurance_class' => 'freelance',
    'flagged' => false,
    'verified' => true,
    'email' => 'contact@pawfectgrooming.co.uk',
    'phone' => '+44 7700 900456',
    'avatar' => $fallbackAvatar,
    'stats' => [
        'earned' => '£4,820',
        'bookings' => '248',
        'rating' => '4.3',
        'reviews' => '20',
    ],
    'snapshot' => [
        ['title' => 'Total bookings', 'value' => '248', 'badge' => '+18', 'badge_class' => 'up', 'note' => 'more vs last month'],
        ['title' => 'Total earnings', 'value' => '£4,820', 'note' => 'All time'],
        ['title' => 'Avg. per booking', 'value' => '£56', 'note' => 'Based on completed bookings'],
        ['title' => 'Repeat clients', 'value' => '65', 'badge' => '+12%', 'badge_class' => 'up', 'note' => 'more vs last month'],
        ['title' => 'Avg. rating', 'value' => '4.3', 'rating' => true, 'note' => 'From 20 reviews'],
        ['title' => 'Profile views', 'value' => '1,240', 'note' => 'This month'],
    ],
    'details' => [
        ['label' => 'Account owner', 'value' => 'Sarah Smith'],
        ['label' => 'Email', 'value' => 'contact@pawfectgrooming.co.uk'],
        ['label' => 'Phone', 'value' => '+44 7700 900456'],
        ['label' => 'Member since', 'value' => 'Feb 2023'],
        ['label' => 'Business reg. number', 'value' => '12345678'],
        ['label' => 'Last active', 'value' => 'Today'],
    ],
    'location_types' => ['Home studio', 'Salon', 'Home visits'],
    'services' => [
        ['name' => 'Full Groom', 'meta' => '60 - 90 mins', 'price' => '£45'],
        ['name' => 'Bath & Tidy', 'meta' => '30 - 60 min', 'price' => '£30'],
        ['name' => 'Nail Trim', 'meta' => '15 - 20 min', 'price' => '£10'],
        ['name' => 'Pet Spa', 'meta' => '60 min', 'price' => '£50'],
        ['name' => 'Mobile Grooming', 'meta' => "At customer's location", 'price' => '£35'],
    ],
    'pet_preferences' => [
        ['label' => 'Pet types accepted', 'value' => 'Cats, Others +'],
        ['label' => 'Pet size preferences', 'value' => 'Small 0-7 kg, Medium 8-18kg'],
    ],
    'bookings' => [
        ['id' => 'GS-0563-B12', 'service' => 'Full Groom', 'customer' => 'Jane Doe', 'user_no' => 'USR-01452', 'date' => '12 Aug 2025', 'amount' => '£55.00', 'status' => 'completed', 'status_label' => 'Completed'],
        ['id' => 'GS-0499-B03', 'service' => 'Nail Trim', 'customer' => 'Tom Harris', 'user_no' => 'USR-01488', 'date' => '28 Jul 2025', 'amount' => '£25.00', 'status' => 'disputed', 'status_label' => 'Disputed'],
        ['id' => 'GS-0521-B17', 'service' => 'Bath & Brush', 'customer' => 'Alex Rivera', 'user_no' => 'USR-01519', 'date' => '15 Jul 2025', 'amount' => '£35.00', 'status' => 'cancelled', 'status_label' => 'Cancelled'],
        ['id' => 'GS-0388-B21', 'service' => 'Puppy Intro', 'customer' => 'Mia Brooks', 'user_no' => 'USR-01622', 'date' => '02 Jul 2025', 'amount' => '£40.00', 'status' => 'refunded', 'status_label' => 'Refunded'],
    ],
    'payout' => [
        ['label' => 'Last payout', 'value' => '£580.00 · 03 Mar 2025'],
        ['label' => 'Payout status', 'value' => 'Active', 'status' => 'active'],
        ['label' => 'Payout hold', 'value' => 'None'],
        ['label' => 'Bank account', 'value' => '•••• 4521'],
    ],
    'notes' => [
        [
            'text' => 'Provider flagged for late arrivals on 3 bookings this month. Monitoring performance before next review cycle.',
            'author' => 'Michelle M',
            'time' => '18 Apr 2025',
            'tag' => 'Internal only',
        ],
        [
            'text' => 'Insurance documents verified and approved. Public liability cover valid until Dec 2025.',
            'author' => 'Ben M',
            'time' => '22 Apr 2025',
            'tag' => 'Internal only',
        ],
    ],
    'activity' => [
        ['type' => 'booking', 'title' => 'Booking GS-0563-B12 completed', 'time' => '18 Apr 2025 · 11:32'],
        ['type' => 'booking', 'title' => 'Payout PO-0142-25 processed (£580.00)', 'time' => '03 Mar 2025 · 09:15'],
        ['type' => 'dispute', 'title' => 'Dispute raised on GS-0499-B03', 'time' => '28 Jul 2025 · 14:20'],
        ['type' => 'flag', 'title' => 'Profile updated — new service added', 'time' => '15 Jul 2025 · 16:45'],
    ],
];

$providerProfiles = [];
foreach ($providers as $provider) {
    if ($provider['id'] === 'PRV-001') {
        $providerProfiles[$provider['id']] = $pawfectProfile;
        continue;
    }

    $providerProfiles[$provider['id']] = array_merge($pawfectProfile, [
        'id' => $provider['id'],
        'name' => $provider['business'],
        'owner' => $provider['owner'],
        'status' => $provider['status'],
        'status_class' => $providerStatusClass[$provider['status']],
        'status_label' => $providerStatusLabels[$provider['status']],
        'type' => $provider['type'],
        'type_label' => $typeLabels[$provider['type']],
        'insurance_badge' => $provider['verified'] ? 'Insured' : 'Freelance',
        'insurance_class' => $provider['verified'] ? 'active' : 'freelance',
        'flagged' => $provider['status'] === 'flagged',
        'verified' => $provider['verified'],
        'email' => $provider['email'],
        'phone' => '+44 7000 000000',
        'avatar' => $fallbackAvatar,
        'stats' => [
            'earned' => $provider['earnings'],
            'bookings' => (string) $provider['bookings'],
            'rating' => $provider['rating'] === '—' ? '—' : $provider['rating'],
            'reviews' => '—',
        ],
        'details' => [
            ['label' => 'Account owner', 'value' => $provider['owner']],
            ['label' => 'Email', 'value' => $provider['email']],
            ['label' => 'Phone', 'value' => '+44 7000 000000'],
            ['label' => 'Member since', 'value' => $provider['joined']],
            ['label' => 'Business reg. number', 'value' => '—'],
            ['label' => 'Last active', 'value' => $provider['last']],
        ],
    ]);
}
@endphp

<div class="admin-customer-detail" x-show="view === 'detail'" x-cloak>
    <button type="button" class="admin-co-back" @click="closeProvider()">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
            <g filter="url(#filter0_d_bp_provider_back)">
                <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
            </g>
            <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
            <defs>
                <filter id="filter0_d_bp_provider_back" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                    <feOffset dy="4" />
                    <feGaussianBlur stdDeviation="2" />
                    <feComposite in2="hardAlpha" operator="out" />
                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_bp_provider_back" />
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_bp_provider_back" result="shape" />
                </filter>
            </defs>
        </svg>
        <span>ALL PROVIDERS</span>
    </button>

    @foreach ($providerProfiles as $profileId => $profile)
    <div class="admin-co-layout" x-show="selectedProviderId === '{{ $profileId }}'" x-cloak>
        <x-admin.provider.profile-sidebar :profile="$profile" />

        <div class="admin-co-main">
            <nav class="admin-co-tabs" aria-label="Provider detail sections">
                @foreach (['overview' => 'Overview', 'profile' => 'Profile', 'bookings' => 'Bookings', 'payouts' => 'Payouts', 'compliance' => 'Compliance', 'account' => 'Account', 'support' => 'Support', 'activity' => 'Activity'] as $tabKey => $tabLabel)
                <button type="button"
                    class="admin-co-tab"
                    :class="{ 'is-active': detailTab === '{{ $tabKey }}' }"
                    @click="detailTab = '{{ $tabKey }}'">
                    {{ $tabLabel }}
                </button>
                @endforeach
            </nav>

            <div x-show="detailTab === 'overview'" x-cloak>
                <x-admin.provider.overview :profile="$profile" />
            </div>

            @foreach (['profile' => 'Profile', 'bookings' => 'Bookings', 'payouts' => 'Payouts', 'compliance' => 'Compliance', 'account' => 'Account', 'support' => 'Support', 'activity' => 'Activity'] as $tabKey => $tabLabel)
            <div class="admin-co-placeholder" x-show="detailTab === '{{ $tabKey }}'" x-cloak>
                <h2 class="admin-page-title mb-0">{{ $tabLabel }}</h2>
                <p class="admin-section-label mb-0">This section will be built next.</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
