@php
$assetBase = asset('images/admin/customer-overview');
$fallbackAvatar = asset('images/profile_image.png');
$providerAvatars = [
asset('images/space_profile_1.png'),
asset('images/space_profile_2.png'),
asset('images/groomer-profile.png'),
asset('images/space_profile_3.png'),
];
$petImages = [
'leo' => asset('images/admin/customer-overview/leo.png'),
'biscuit' => asset('images/admin/customer-overview/biscuit.png'),
'surf' => asset('images/surf.png'),
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
'pet_stats' => [
'total_pets' => '3',
'vaccinated' => '2 of 3',
'sessions' => '18',
'last_groomed' => '09 Jul',
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
'edit_fields' => [
'full_name' => 'Jane John Doe',
'email' => 'jane.doe@gmail.com',
'phone' => '+44 78562 5458',
'dob' => '12 Aug 1968',
'address' => '142 Henderson Drive',
'city' => 'London',
'postcode' => 'SE25 63CB',
'country' => 'United Kingdom',
],
'pets' => [
[
'id' => 'pet-leo',
'name' => 'Leo',
'meta' => 'Blue Russian · Female · 6yrs · 7kg',
'status' => 'active',
'status_label' => 'Active',
'vaccinated' => true,
'sessions' => 8,
'type' => 'cat',
'image' => $petImages['leo'],
'added' => '14 Mar 2024',
'last_groomed' => '18 Feb',
'avg_rating' => '4.6',
'details' => [
['label' => 'Species', 'value' => 'Cat'],
['label' => 'Breed', 'value' => 'Blue Russian'],
['label' => 'Gender', 'value' => 'Female (spayed)'],
['label' => 'Age', 'value' => '6 years'],
['label' => 'Weight', 'value' => '7 kg'],
['label' => 'Coat type', 'value' => 'Shorthair'],
['label' => 'Colour', 'value' => 'Grey'],
['label' => 'Microchipped', 'value' => 'Yes'],
],
'health' => [
['label' => 'Vaccination status', 'value' => 'Up to date (expires Apr 2028)', 'ok' => true],
['label' => 'Last vaccinated', 'value' => 'Jan 2025'],
['label' => 'Flea treatment', 'value' => 'Up to date', 'ok' => true],
['label' => 'Known conditions', 'value' => 'Asthma'],
['label' => 'Allergies', 'value' => 'None known'],
['label' => 'Medication', 'value' => 'Inhaler, daily'],
['label' => 'Vet name', 'value' => 'Battersea Vets, SW11'],
['label' => 'Vet phone', 'value' => '020 6544 7897'],
],
'flags' => [
['label' => 'Vaccinated', 'tone' => 'ok'],
['label' => 'Nervous around hair-dryers.', 'tone' => 'warn'],
['label' => 'Asthma — avoid aerosol sprays', 'tone' => 'danger'],
['label' => 'Good with other pets', 'tone' => 'ok'],
['label' => 'Needs calming approach', 'tone' => 'warn'],
],
'grooming' => [
['label' => 'Preferred style', 'value' => 'Breed standard trim — no shaving'],
['label' => 'Nail trim', 'value' => 'Yes — short but not too quick'],
['label' => 'Ear cleaning', 'value' => 'Yes — prone to build-up'],
['label' => 'Blow dry', 'value' => 'No — does not tolerate'],
['label' => 'Handling notes', 'value' => 'Calm voice preferred. Gets anxious on grooming table — extra time needed at start.'],
['label' => 'Products', 'value' => 'Sensitive skin shampoo only — no fragrance'],
],
'bookings' => [
['id' => 'GS-0563-B12', 'service' => 'Full Groom', 'provider' => 'Pawfect Salon', 'date' => '10/06/2025', 'amount' => '£55.00', 'rating' => 4],
['id' => 'GS-0499-B03', 'service' => 'Nail Trim', 'provider' => "Wags'n'Wheels", 'date' => '22/04/2025', 'amount' => '£25.00', 'rating' => 5],
['id' => 'GS-0521-B17', 'service' => 'Bath & Brush', 'provider' => 'Happy Paws Co.', 'date' => '18/02/2025', 'amount' => '£35.00', 'rating' => 2],
['id' => 'GS-0388-B21', 'service' => 'Full Groom', 'provider' => "Lucy's", 'date' => '05/01/2025', 'amount' => '£50.00', 'rating' => 4],
],
'bookings_total' => 8,
'notes' => [
[
'text' => 'Leo becomes stressed with loud dryers. Flagged for quiet-room preference on future bookings.',
'author' => 'Michelle M',
'time' => '18 Apr 2025',
'tag' => 'Internal only',
],
[
'text' => 'Asthma noted on intake form. Confirm inhaler schedule with owner before any aerosol products.',
'author' => 'Ben M',
'time' => '22 Mar 2025',
'tag' => 'Internal only',
],
],
'activity' => [
['type' => 'flag', 'title' => 'Health & behaviour flag added by Michelle M', 'time' => '18 Apr 2025 · 14:32'],
['type' => 'booking', 'title' => 'Booking FG-0563-B41 completed', 'time' => '18 Apr 2025 · 11:32'],
['type' => 'dispute', 'title' => 'Dispute raised on FG-0563-B45', 'time' => '05 Mar 2025 · 21:50'],
['type' => 'password', 'title' => 'Archived pet profile by Ben M', 'time' => '01 Dec 2024 · 18:55'],
],
],
[
'id' => 'pet-biscuit',
'name' => 'Biscuit',
'meta' => 'Golden Retriever · Male · 4yrs · 28kg',
'status' => 'active',
'status_label' => 'Active',
'vaccinated' => true,
'sessions' => 2,
'type' => 'dog',
'image' => $petImages['biscuit'],
'added' => '02 Jun 2023',
'last_groomed' => '03 Mar',
'avg_rating' => '4.9',
'details' => [
['label' => 'Species', 'value' => 'Dog'],
['label' => 'Breed', 'value' => 'Golden Retriever'],
['label' => 'Gender', 'value' => 'Male (neutered)'],
['label' => 'Age', 'value' => '4 years'],
['label' => 'Weight', 'value' => '28 kg'],
['label' => 'Coat type', 'value' => 'Longhair'],
['label' => 'Colour', 'value' => 'Golden'],
['label' => 'Microchipped', 'value' => 'Yes'],
],
'health' => [
['label' => 'Vaccination status', 'value' => 'Up to date (expires Nov 2026)', 'ok' => true],
['label' => 'Last vaccinated', 'value' => 'Nov 2024'],
['label' => 'Flea treatment', 'value' => 'Up to date', 'ok' => true],
['label' => 'Known conditions', 'value' => 'None'],
['label' => 'Allergies', 'value' => 'Chicken'],
['label' => 'Medication', 'value' => 'None'],
['label' => 'Vet name', 'value' => 'Battersea Vets, SW11'],
['label' => 'Vet phone', 'value' => '020 6544 7897'],
],
'flags' => [
['label' => 'Vaccinated', 'tone' => 'ok'],
['label' => 'Friendly with children', 'tone' => 'ok'],
['label' => 'Food allergy — chicken', 'tone' => 'warn'],
],
'grooming' => [
['label' => 'Preferred style', 'value' => 'Full fluff dry — feathered trim'],
['label' => 'Nail trim', 'value' => 'Yes'],
['label' => 'Ear cleaning', 'value' => 'Yes'],
['label' => 'Blow dry', 'value' => 'Yes — enjoys it'],
['label' => 'Handling notes', 'value' => 'Very calm. Loves treats during dry-off.'],
['label' => 'Products', 'value' => 'Hypoallergenic shampoo — no chicken derivatives'],
],
'bookings' => [
['id' => 'GS-0601-B04', 'service' => 'Full Groom', 'provider' => 'Pawfect Salon', 'date' => '03/03/2025', 'amount' => '£65.00', 'rating' => 5],
['id' => 'GS-0555-B11', 'service' => 'Bath & Brush', 'provider' => 'Happy Paws Co.', 'date' => '12/12/2024', 'amount' => '£40.00', 'rating' => 5],
],
'bookings_total' => 2,
'notes' => [
[
'text' => 'Chicken allergy confirmed with owner. Avoid any treats containing poultry.',
'author' => 'Michelle M',
'time' => '03 Mar 2025',
'tag' => 'Internal only',
],
],
'activity' => [
['type' => 'booking', 'title' => 'Booking GS-0601-B04 completed', 'time' => '03 Mar 2025 · 16:10'],
['type' => 'flag', 'title' => 'Food allergy flag added by Michelle M', 'time' => '02 Mar 2025 · 09:40'],
],
],
[
'id' => 'pet-rocket',
'name' => 'Rocket',
'meta' => 'Hermann’s Tortoise · Male · 12yrs · 1.2kg',
'status' => 'active',
'status_label' => 'Active',
'vaccinated' => false,
'sessions' => 1,
'type' => 'other',
'image' => $petImages['surf'],
'added' => '20 Sep 2024',
'last_groomed' => '—',
'avg_rating' => '—',
'details' => [
['label' => 'Species', 'value' => 'Tortoise'],
['label' => 'Breed', 'value' => 'Hermann’s Tortoise'],
['label' => 'Gender', 'value' => 'Male'],
['label' => 'Age', 'value' => '12 years'],
['label' => 'Weight', 'value' => '1.2 kg'],
['label' => 'Coat type', 'value' => 'Shell'],
['label' => 'Colour', 'value' => 'Brown / olive'],
['label' => 'Microchipped', 'value' => 'Yes'],
],
'health' => [
['label' => 'Vaccination status', 'value' => 'N/A'],
['label' => 'Last vaccinated', 'value' => '—'],
['label' => 'Flea treatment', 'value' => 'N/A'],
['label' => 'Known conditions', 'value' => 'None'],
['label' => 'Allergies', 'value' => 'None known'],
['label' => 'Medication', 'value' => 'None'],
['label' => 'Vet name', 'value' => 'Exotic Pets Clinic, SW8'],
['label' => 'Vet phone', 'value' => '020 7000 1122'],
],
'flags' => [
['label' => 'Exotic species', 'tone' => 'warn'],
['label' => 'Handle with care', 'tone' => 'warn'],
],
'grooming' => [
['label' => 'Preferred style', 'value' => 'Shell clean only'],
['label' => 'Nail trim', 'value' => 'Yes — specialist'],
['label' => 'Ear cleaning', 'value' => 'No'],
['label' => 'Blow dry', 'value' => 'No'],
['label' => 'Handling notes', 'value' => 'Keep warm. Limit time out of carrier.'],
['label' => 'Products', 'value' => 'Reptile-safe shell wipe only'],
],
'bookings' => [
['id' => 'GS-0610-B02', 'service' => 'Shell Clean', 'provider' => 'Exotic Care Co.', 'date' => '20/09/2024', 'amount' => '£35.00', 'rating' => 5],
],
'bookings_total' => 1,
'notes' => [],
'activity' => [
['type' => 'booking', 'title' => 'Booking GS-0610-B02 completed', 'time' => '20 Sep 2024 · 12:05'],
],
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
['name' => 'Pawfect Salon', 'role' => 'Groomer', 'subtitle' => 'Mia R.', 'avatar' => $providerAvatars[0]],
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
'pet_stats' => [
'total_pets' => (string) count($janeProfile['pets'] ?? []),
'vaccinated' => collect($janeProfile['pets'] ?? [])->where('vaccinated', true)->count() . ' of ' . max(1, count($janeProfile['pets'] ?? [])),
'sessions' => (string) collect($janeProfile['pets'] ?? [])->sum('sessions'),
'last_groomed' => collect($janeProfile['pets'] ?? [])->pluck('last_groomed')->filter()->first() ?? '—',
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
'edit_fields' => [
'full_name' => $customer['name'],
'email' => $customer['email'],
'phone' => '+44 7000 0000',
'dob' => '',
'address' => $customer['region'],
'city' => '',
'postcode' => '',
'country' => 'United Kingdom',
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

            <div x-show="detailTab === 'pets'" x-cloak>
                <x-admin.customer.pets :profile="$profile" />
            </div>

            @foreach (['bookings' => 'Bookings', 'payments' => 'Payments', 'support' => 'Support', 'referrals' => 'Referrals', 'activity' => 'Activity'] as $tabKey => $tabLabel)
            <div class="admin-co-placeholder" x-show="detailTab === '{{ $tabKey }}'" x-cloak>
                <h2 class="admin-page-title mb-0">{{ $tabLabel }}</h2>
                <p class="admin-section-label mb-0">This section will be built next.</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>