@php
$assetBase = asset('images/admin/customer-overview');
$fallbackAvatar = asset('images/profile_image.png');
$providerAvatars = [
asset('images/space_profile_1.png'),
asset('images/space_profile_2.png'),
asset('images/groomer-profile.png'),
asset('images/space_profile_3.png'),
];

$janeProfile = [
'id' => 'USR-01452',
'name' => 'Jane Doe',
'status' => 'active',
'status_label' => 'Active',
'flagged' => true,
'verified' => true,
'email' => 'janed@gmail.com',
'phone' => '+447 8562 5458',
'avatar' => $fallbackAvatar,
'stats' => [
'spend' => '£652',
'bookings' => '12',
'member_since' => 'Feb 2023',
'last_active' => '3 days ago',
],
'details' => [
['label' => 'Full Name', 'value' => 'Jane John Doe'],
['label' => 'Email', 'value' => 'jane.doe@gmail.com'],
['label' => 'Phone', 'value' => '+44 78562 5458'],
['label' => 'Date of Birth', 'value' => '12 Aug 1968'],
['label' => 'Address', 'value' => '142 Henderson Drive, London, SE25 63CB, United Kingdom'],
['label' => 'Account ID', 'value' => 'USER-01452'],
['label' => 'Last Active', 'value' => '2 days ago'],
['label' => 'Account Created', 'value' => '02 Feb 2023'],
],
'pets' => [
[
'name' => 'Leo',
'meta' => 'Blue Russian · Female · 6yrs · 7kg',
'status' => 'active',
'status_label' => 'Active',
'vaccinated' => true,
'sessions' => 2,
'type' => 'cat',
'image' => null,
],
[
'name' => 'Biscuit',
'meta' => 'Golden Retriever · Male · 4yrs · 28kg',
'status' => 'active',
'status_label' => 'Active',
'vaccinated' => true,
'sessions' => 8,
'type' => 'dog',
'image' => null,
],
[
'name' => 'Mochi',
'meta' => 'Shiba Inu · Male · 2yrs · 11kg',
'status' => 'active',
'status_label' => 'Active',
'vaccinated' => true,
'sessions' => 3,
'type' => 'dog',
'image' => null,
],
],
'marketing' => [
['label' => 'Email Marketing', 'enabled' => true],
['label' => 'SMS Marketing', 'enabled' => true],
['label' => 'Push Notifications', 'enabled' => true],
['label' => 'Third-party sharing', 'enabled' => false],
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
'bookings' => [
['id' => 'GS-0563-B12', 'service' => 'Full Groom', 'provider' => 'Pawfect Salon', 'date' => '12 Aug 2025', 'amount' => '£55.00', 'status' => 'completed', 'status_label' => 'Completed'],
['id' => 'SS-0412-B08', 'service' => 'Day Care', 'provider' => 'Furs & Co. Studio', 'date' => '04 Aug 2025', 'amount' => '£80.00', 'status' => 'disputed', 'status_label' => 'Disputed'],
['id' => 'GS-0499-B03', 'service' => 'Nail Trim', 'provider' => "Katie's Mobile Groom", 'date' => '28 Jul 2025', 'amount' => '£25.00', 'status' => 'cancelled', 'status_label' => 'Cancelled'],
['id' => 'SS-0388-B21', 'service' => 'Overnight Stay', 'provider' => 'Garden Paws', 'date' => '15 Jul 2025', 'amount' => '£120.00', 'status' => 'refunded', 'status_label' => 'Refunded'],
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
'blocked_providers' => [
['name' => 'The Garden Grooming Spot', 'role' => 'Groomer', 'subtitle' => 'Chloe D.', 'avatar' => $providerAvatars[0]],
['name' => 'Furs & Co. Studio', 'role' => 'Space Host', 'subtitle' => 'Hosted by Dev É.', 'avatar' => $providerAvatars[1]],
['name' => 'Sarah W.', 'role' => 'Groomer', 'subtitle' => "Sarah's Grooming Studio", 'avatar' => $providerAvatars[2]],
['name' => 'Katie Z.', 'role' => 'Groomer', 'subtitle' => 'Includes other accounts ...', 'avatar' => $providerAvatars[3]],
],
'notes' => [
[
'text' => 'Customer raised a dispute on BK-08640 claiming groomer was 20 mins late and did not complete the nail trim. Groomer denies. Awaiting photo evidence from groomer.',
'author' => 'Michelle M',
'time' => '18 Apr 2025',
'tag' => 'Internal only',
],
[
'text' => 'Verified phone number manually after customer reported SMS delay. Account marked verified.',
'author' => 'Ben M',
'time' => '22 Apr 2025',
'tag' => 'Internal only',
],
],
'activity' => [
['type' => 'flag', 'title' => 'Account flagged by Michelle M', 'time' => '18 Apr 2025 · 14:32'],
['type' => 'booking', 'title' => 'Booking FG-0563-B41 completed', 'time' => '18 Apr 2025 · 11:32'],
['type' => 'dispute', 'title' => 'Dispute raised on FG-0563-B45', 'time' => '05 Mar 2025 · 21:50'],
['type' => 'password', 'title' => 'Password changed by customer', 'time' => '01 Dec 2024 · 18:55'],
],
];

$customerProfiles = [];
foreach ($customers as $customer) {
if ($customer['id'] === 'USR-01452') {
$customerProfiles[$customer['id']] = $janeProfile;
continue;
}

$customerProfiles[$customer['id']] = array_merge($janeProfile, [
'id' => $customer['id'],
'name' => $customer['name'],
'status' => $customer['status'],
'status_label' => $statusLabels[$customer['status']],
'flagged' => $customer['status'] === 'flagged',
'verified' => $customer['verified'],
'email' => $customer['email'],
'phone' => '+44 7000 0000',
'avatar' => $fallbackAvatar,
'stats' => [
'spend' => $customer['spend'],
'bookings' => (string) $customer['bookings'],
'member_since' => $customer['joined'],
'last_active' => $customer['last'],
],
'details' => [
['label' => 'Full Name', 'value' => $customer['name']],
['label' => 'Email', 'value' => $customer['email']],
['label' => 'Phone', 'value' => '+44 7000 0000'],
['label' => 'Date of Birth', 'value' => '—'],
['label' => 'Address', 'value' => $customer['region'] . ', United Kingdom'],
['label' => 'Account ID', 'value' => $customer['id']],
['label' => 'Last Active', 'value' => $customer['last']],
['label' => 'Account Created', 'value' => $customer['joined']],
],
]);
}
@endphp

<div class="admin-customer-detail" x-show="view === 'detail'" x-cloak>
    <button type="button" class="admin-co-back" @click="closeCustomer()">
        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none">
            <g filter="url(#filter0_d_1_467)">
                <circle cx="10" cy="10" r="10" transform="matrix(-1 0 0 1 24 0)" fill="white" />
            </g>
            <path d="M15.25 13.125L12.1036 9.97859L15.1972 6.885" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
            <defs>
                <filter id="filter0_d_1_467" x="0" y="0" width="28" height="28" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                    <feOffset dy="4" />
                    <feGaussianBlur stdDeviation="2" />
                    <feComposite in2="hardAlpha" operator="out" />
                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_1_467" />
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_1_467" result="shape" />
                </filter>
            </defs>
        </svg>
        <span>ALL CUSTOMERS</span>
    </button>

    @foreach ($customerProfiles as $profileId => $profile)
    <div class="admin-co-layout" x-show="selectedCustomerId === '{{ $profileId }}'" x-cloak>
        <x-admin.customer.profile-sidebar :profile="$profile" />

        <div class="admin-co-main">
            <nav class="admin-co-tabs" aria-label="Customer detail sections">
                @foreach (['overview' => 'Overview', 'pets' => 'Pets', 'bookings' => 'Bookings', 'payments' => 'Payments', 'support' => 'Support', 'referrals' => 'Referrals', 'activity' => 'Activity'] as $tabKey => $tabLabel)
                <button type="button"
                    class="admin-co-tab"
                    :class="{ 'is-active': detailTab === '{{ $tabKey }}' }"
                    @click="detailTab = '{{ $tabKey }}'">
                    {{ $tabLabel }}
                </button>
                @endforeach
            </nav>

            <div x-show="detailTab === 'overview'" x-cloak>
                <x-admin.customer.overview :profile="$profile" />
            </div>

            @foreach (['pets' => 'Pets', 'bookings' => 'Bookings', 'payments' => 'Payments', 'support' => 'Support', 'referrals' => 'Referrals', 'activity' => 'Activity'] as $tabKey => $tabLabel)
            <div class="admin-co-placeholder" x-show="detailTab === '{{ $tabKey }}'" x-cloak>
                <h2 class="admin-page-title mb-0">{{ $tabLabel }}</h2>
                <p class="admin-section-label mb-0">This section will be built next.</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>