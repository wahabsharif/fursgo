@php
$fallbackAvatar = asset('images/profile_image.png');
$spaceAvatar = asset('images/space-profile.png');

$groomerServices = [
    ['name' => 'Full Groom', 'meta' => '60 - 90 mins', 'price' => '£45'],
    ['name' => 'Bath & Tidy', 'meta' => '30 - 60 min', 'price' => '£30'],
    ['name' => 'Nail Trim', 'meta' => '15 - 20 min', 'price' => '£10'],
    ['name' => 'Pet Spa', 'meta' => '60 min', 'price' => '£50'],
    ['name' => 'Mobile Grooming', 'meta' => "At customer's location", 'price' => '£35'],
];

$spaceServices = [
    ['name' => 'Hourly', 'price' => '£25 / Hourly'],
    ['name' => 'Half-day (4 hours)', 'price' => '£80 / Half-day'],
    ['name' => 'Full day (8 hours)', 'price' => '£150 / Full-day'],
];

$sharedPetPreferences = [
    ['label' => 'Pet types accepted', 'value' => 'Cats, Others +'],
    ['label' => 'Pet size preferences', 'value' => 'Small 0-7 kg, Medium 8-18kg'],
];

$spaceDefaults = [
    'location_types' => [],
    'service_areas' => [
        [
            'name' => 'Southwark',
            'address' => "22 Studio Way, London, SW4 6NR\nUnited Kingdom",
            'availability' => 'Full Day · Half Day · Hourly',
            'map' => asset('images/admin/provider/map-southwark.png'),
            'location' => 'West London',
            'accessibility' => 'Located near Victoria Embankment, with excellent public transport connections and free on-site parking available.',
        ],
        [
            'name' => 'Camden',
            'address' => "14 Regent's Canal Walk, London, NW1 8AN\nUnited Kingdom",
            'availability' => 'Half Day · Hourly',
            'map' => asset('images/admin/provider/map-southwark.png'),
            'location' => 'North London',
            'accessibility' => 'Step-free access from street level. Limited paid parking nearby.',
        ],
        [
            'name' => 'Hackney',
            'address' => "8 Mare Street Yard, London, E8 3RH\nUnited Kingdom",
            'availability' => 'Full Day · Half Day',
            'map' => asset('images/admin/provider/map-southwark.png'),
            'location' => 'East London',
            'accessibility' => 'Ground-floor space with wide doorway. Street parking only.',
        ],
    ],
    'services' => $spaceServices,
    'pet_preferences' => $sharedPetPreferences,
];

// Dual-role demo: Pawfect has both Groomer + Space Host profiles
$pawfectProfile = [
    'id' => 'PRV-001',
    'dual' => true,
    'default_view' => 'groomer',
    'roles' => ['groomer', 'space'],
    'owner' => 'Sarah Smith',
    'status' => 'active',
    'status_class' => 'active',
    'status_label' => 'Active',
    'flagged' => false,
    'verified' => true,
    'email' => 'contact@pawfectgrooming.co.uk',
    'phone' => '+44 7700 900456',
    'type' => 'groomer',
    'type_label' => 'Groomer',
    'insurance_badge' => 'Freelance',
    'insurance_class' => 'freelance',
    'avatar' => $fallbackAvatar,
    'name' => 'Pawfect Grooming',
    'stats' => [
        'earned' => '£4,820',
        'bookings' => '248',
        'rating' => '4.3',
        'reviews' => '94',
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
    'services' => $groomerServices,
    'pet_preferences' => $sharedPetPreferences,
    'bookings' => [
        ['id' => 'GS-0563-B12', 'service' => 'Full Groom', 'customer' => 'Jane Doe', 'user_no' => 'USR-01452', 'date' => '12 Aug 2025', 'amount' => '£55.00', 'status' => 'completed', 'status_label' => 'Completed'],
        ['id' => 'GS-0499-B03', 'service' => 'Nail Trim', 'customer' => 'Tom Harris', 'user_no' => 'USR-01488', 'date' => '28 Jul 2025', 'amount' => '£25.00', 'status' => 'disputed', 'status_label' => 'Disputed'],
        ['id' => 'GS-0521-B17', 'service' => 'Bath & Brush', 'customer' => 'Alex Rivera', 'user_no' => 'USR-01519', 'date' => '15 Jul 2025', 'amount' => '£35.00', 'status' => 'cancelled', 'status_label' => 'Cancelled'],
        ['id' => 'GS-0388-B21', 'service' => 'Puppy Intro', 'customer' => 'Mia Brooks', 'user_no' => 'USR-01622', 'date' => '02 Jul 2025', 'amount' => '£40.00', 'status' => 'refunded', 'status_label' => 'Refunded'],
    ],
    'payout' => [
        ['label' => 'Last payout', 'value' => '03 Jun 2025 · £890'],
        ['label' => 'Payout status', 'value' => 'Active', 'status' => 'active'],
        ['label' => 'Payout hold', 'value' => 'None', 'muted' => true],
        ['label' => 'Bank account', 'value' => '**** **** **** 4821'],
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
    'sessions' => [
        [
            'device' => 'laptop',
            'title' => 'MacBook Pro · Chrome 124',
            'location' => 'London, UK',
            'ip' => '82.34.120.45',
            'time' => 'Last active 4 mins ago · signed in 18 Apr 2025',
            'warning' => false,
        ],
        [
            'device' => 'phone',
            'title' => 'iPhone 15 · Safari 17',
            'location' => 'Manchester, UK',
            'ip' => '86.22.110.19',
            'time' => 'Last active 3 days ago · signed in 02 Mar 2025',
            'warning' => false,
        ],
        [
            'device' => 'desktop',
            'title' => 'Windows PC · Chrome 122',
            'location' => 'London, UK',
            'ip' => '82.14.201.44',
            'time' => 'Last active 12 days ago · signed in 12 Jan 2025',
            'warning' => true,
        ],
    ],
    'blocked_customers' => [
        ['name' => 'Sam P', 'user_id' => 'USR-01452', 'email' => 'sam.p@gmail.com', 'avatar' => $fallbackAvatar],
        ['name' => 'Rikki Q', 'user_id' => 'USR-01453', 'email' => 'rikki.q@gmail.com', 'avatar' => $fallbackAvatar],
        ['name' => 'Luke B', 'user_id' => 'USR-01455', 'email' => 'luke.b@gmail.com', 'avatar' => $fallbackAvatar],
        ['name' => 'Jade W', 'user_id' => 'USR-01468', 'email' => 'janed@gmail.com', 'avatar' => $fallbackAvatar],
        ['name' => 'Mia Brooks', 'user_id' => 'USR-01690', 'email' => 'mia.brooks@gmail.com', 'avatar' => $fallbackAvatar],
    ],
    'marketing' => [
        ['label' => 'Email Marketing', 'enabled' => true],
        ['label' => 'SMS Notifications', 'enabled' => false],
        ['label' => 'Push Notification', 'enabled' => true],
        ['label' => 'Third Party sharing', 'enabled' => true],
    ],
    'verification' => [
        'profile_complete' => 70,
        'items' => [
            ['label' => 'Email Verified', 'ok' => true],
            ['label' => 'Phone Verified', 'ok' => true],
            ['label' => '2FA Enabled', 'ok' => false],
            ['label' => 'Connected Login', 'type' => 'text', 'value' => 'Google'],
            ['label' => 'Profile Complete', 'type' => 'progress', 'value' => '70% Completion'],
        ],
    ],
    'connected_accounts' => [
        [
            'name' => 'Facebook',
            'detail' => 'sarah.w@pawfect.co.uk',
            'connected_at' => 'Connected 12 Jan 2024',
            'status' => 'connected',
            'icon' => 'facebook',
        ],
    ],
    'connected_apps' => [
        [
            'name' => 'Google Calendar',
            'detail' => 'Booking sync',
            'connected_at' => 'Connected 14 Jan 2023',
            'status' => 'disconnected',
            'icon' => 'calendar',
        ],
        [
            'name' => 'Quickbooks',
            'detail' => 'Earnings & invoicing',
            'connected_at' => 'Connected 02 Mar 2023',
            'status' => 'connected',
            'icon' => 'quickbooks',
        ],
        [
            'name' => 'Zapier',
            'detail' => 'Automation workflows',
            'connected_at' => 'Connected 15 Apr 2023',
            'status' => 'connected',
            'icon' => 'zapier',
        ],
    ],
    'groomer' => [
        'name' => 'Pawfect Grooming',
        'type' => 'groomer',
        'type_label' => 'Groomer',
        'avatar' => $fallbackAvatar,
        'insurance_badge' => 'Freelance',
        'insurance_class' => 'freelance',
        'location_types' => ['Home studio', 'Salon', 'Home visits'],
        'service_areas' => [],
        'services' => $groomerServices,
        'pet_preferences' => $sharedPetPreferences,
    ],
    'space' => array_merge($spaceDefaults, [
        'name' => 'The Garden Grooming Spot',
        'type' => 'space',
        'type_label' => 'Space Host',
        'avatar' => $spaceAvatar,
        'insurance_badge' => 'Registered',
        'insurance_class' => 'registered',
    ]),
];

$providerProfiles = [];
foreach ($providers as $provider) {
    if ($provider['id'] === 'PRV-001') {
        $providerProfiles[$provider['id']] = $pawfectProfile;
        continue;
    }

    $merged = array_merge($pawfectProfile, [
        'dual' => false,
        'default_view' => $provider['type'],
        'roles' => [$provider['type']],
        'groomer' => null,
        'space' => null,
        'id' => $provider['id'],
        'name' => $provider['business'],
        'owner' => $provider['owner'],
        'status' => $provider['status'],
        'status_class' => $providerStatusClass[$provider['status']],
        'status_label' => $providerStatusLabels[$provider['status']],
        'type' => $provider['type'],
        'type_label' => $typeLabels[$provider['type']],
        'insurance_badge' => $provider['verified'] ? 'Registered' : 'Freelance',
        'insurance_class' => $provider['verified'] ? 'registered' : 'freelance',
        'flagged' => $provider['status'] === 'flagged',
        'verified' => $provider['verified'],
        'email' => $provider['email'],
        'phone' => '+44 7000 000000',
        'avatar' => $provider['type'] === 'space' ? $spaceAvatar : $fallbackAvatar,
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
        'location_types' => $provider['type'] === 'groomer' ? ['Home studio', 'Salon', 'Home visits'] : [],
        'services' => $provider['type'] === 'space' ? $spaceServices : $groomerServices,
        'pet_preferences' => $sharedPetPreferences,
    ]);

    if ($provider['type'] === 'space') {
        $merged = array_merge($merged, $spaceDefaults);
    }

    $providerProfiles[$provider['id']] = $merged;
}
@endphp

<div class="admin-customer-detail" x-show="view === 'detail'" x-cloak>
    <button type="button" class="admin-co-back" @click="goBack()">
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
        <span x-text="backLabel">ALL PROVIDERS</span>
    </button>

    @foreach ($providerProfiles as $profileId => $profile)
    @php
        $isDual = ! empty($profile['dual']);
        $defaultView = $profile['default_view'] ?? $profile['type'] ?? 'groomer';
    @endphp
    <div
        class="admin-co-layout"
        x-show="selectedProviderId === '{{ $profileId }}'"
        x-cloak
        @admin-provider-opened.window="if ($event.detail && $event.detail.id === '{{ $profileId }}') viewAs = @js($defaultView)"
        x-data="{
            viewAs: @js($defaultView),
            dual: @js($isDual),
            resetPasswordOpen: false,
            suspendOpen: false,
            suspendReason: '',
            suspendDuration: 'indefinite',
            suspendNotes: '',
            openSuspendReason: false,
            openSuspendDuration: false,
            flagOpen: false,
            flagReason: '',
            flagNote: '',
            flagBy: 'Michelle M (me)',
            openFlagReason: false,
            openFlagBy: false,
            deleteOpen: false,
            deleteReason: '',
            deleteGdprRef: '',
            deleteConfirm: '',
            openDeleteReason: false,
            deleteReady: false,
        }"
        x-init="
            const syncModalLock = () => {
                const open = resetPasswordOpen || suspendOpen || flagOpen || deleteOpen;
                if (open) {
                    if (!document.body.classList.contains('admin-co-modal-lock')) {
                        document.body.dataset.adminCoScrollY = String(window.scrollY);
                        document.body.style.top = '-' + window.scrollY + 'px';
                        document.body.classList.add('admin-co-modal-lock');
                    }
                } else if (document.body.classList.contains('admin-co-modal-lock')) {
                    const y = parseInt(document.body.dataset.adminCoScrollY || '0', 10);
                    document.body.classList.remove('admin-co-modal-lock');
                    document.body.style.top = '';
                    delete document.body.dataset.adminCoScrollY;
                    window.scrollTo(0, y);
                }
            };
            const syncDeleteReady = () => {
                deleteReady = !!deleteReason
                    && deleteGdprRef.trim() !== ''
                    && deleteConfirm.trim() === 'DELETE';
            };
            $watch('resetPasswordOpen', () => $nextTick(() => syncModalLock()));
            $watch('suspendOpen', () => $nextTick(() => syncModalLock()));
            $watch('flagOpen', () => $nextTick(() => syncModalLock()));
            $watch('deleteOpen', () => $nextTick(() => syncModalLock()));
            $watch('deleteReason', () => syncDeleteReady());
            $watch('deleteGdprRef', () => syncDeleteReady());
            $watch('deleteConfirm', () => syncDeleteReady());
        "
    >
        <x-admin.provider.profile-sidebar :profile="$profile" />

        <div class="admin-co-main">
            <nav class="admin-co-tabs" aria-label="Provider detail sections">
                @foreach (['overview' => 'Overview', 'profile' => 'Profile', 'bookings' => 'Bookings', 'payouts' => 'Payouts', 'compliance' => 'Compliance', 'account' => 'Account', 'support' => 'Support', 'activity' => 'Activity'] as $tabKey => $tabLabel)
                <button type="button"
                    class="admin-co-tab"
                    :class="{ 'is-active': detailTab === '{{ $tabKey }}' }"
                    @click="switchDetailTab('{{ $tabKey }}')">
                    {{ $tabLabel }}
                </button>
                @endforeach
            </nav>

            <div x-show="detailTab === 'overview'" x-cloak>
                <x-admin.provider.overview :profile="$profile" />
            </div>

            <div x-show="detailTab === 'activity'" x-cloak>
                <x-admin.provider.activity :profile="$profile" />
            </div>

            <div x-show="detailTab === 'account'" x-cloak>
                <x-admin.provider.account :profile="$profile" />
            </div>

            @foreach (['profile' => 'Profile', 'bookings' => 'Bookings', 'payouts' => 'Payouts', 'compliance' => 'Compliance', 'support' => 'Support'] as $tabKey => $tabLabel)
            <div class="admin-co-placeholder" x-show="detailTab === '{{ $tabKey }}'" x-cloak>
                <h2 class="admin-page-title mb-0">{{ $tabLabel }}</h2>
                <p class="admin-section-label mb-0">This section will be built next.</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
