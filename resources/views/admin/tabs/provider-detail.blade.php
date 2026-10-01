@php
$fallbackAvatar = asset('images/profile_image.png');
$spaceAvatar = asset('images/admin/provider/gallery/space/1.jpg');

$groomerServices = [
    [
        'name' => 'Full Groom',
        'meta' => 'Cat · Dog · Other · 60–90 min · Small £35 · Medium £45',
        'price' => '£35',
        'price_prefix' => 'From',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Bath & Tidy',
        'meta' => 'Cat · Dog · Other · 30–60 min · Buffer: 15 min',
        'price' => '£30',
        'price_prefix' => 'From',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Nail Trim',
        'meta' => 'Cat · Dog · Other · 5–15 min · Overtime: £1 per 5 min',
        'price' => '£10',
        'price_prefix' => 'From',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Ear Cleaning',
        'meta' => 'Cat · Dog · 15 min · Add-ons compatible',
        'price' => '£20',
        'price_prefix' => '',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Face Trim Only',
        'meta' => 'Cat · Dog · Other · 20 min',
        'price' => '£10',
        'price_prefix' => '',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Mobile Grooming',
        'meta' => "At customer's location",
        'price' => '£35',
        'price_prefix' => 'From',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Luxury Spa',
        'meta' => 'Cat · Dog · 90 min',
        'price' => '£60',
        'price_prefix' => '',
        'status' => 'inactive',
        'status_label' => 'Inactive',
    ],
];

$groomerAddons = [
    ['label' => 'Hypoallergenic Shampoo £5', 'active' => true],
    ['label' => 'Coat Colour Enhancing Shampoo £10', 'active' => false],
    ['label' => 'Anti-itch Treatment £4', 'active' => true],
    ['label' => 'Coat Shine Spray £2', 'active' => false],
    ['label' => 'Teeth Brushing £8', 'active' => true],
    ['label' => 'Nail Grinding £20', 'active' => false],
    ['label' => 'Anal Gland Expression £5', 'active' => false],
    ['label' => 'De-shedding Treatment £10', 'active' => true],
    ['label' => 'Flea Treatment £5', 'active' => false],
];

$spaceServices = [
    [
        'name' => 'Hourly',
        'meta' => 'Cat · Dog · Other · 60 min · Overtime: £10 per 15 min · Buffer: 15 min',
        'price' => '£25 / hr',
        'price_prefix' => '',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Half-day',
        'meta' => 'Cat · Dog · 4 hours · Select start time',
        'price' => '£80 / half day',
        'price_prefix' => '',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Full-day',
        'meta' => 'Cat · Dog · Other · 8 hours · Select start time',
        'price' => '£150 / full day',
        'price_prefix' => '',
        'status' => 'active',
        'status_label' => 'Active',
    ],
];

$spaceListingAddons = [
    [
        'name' => 'Storage Locker',
        'meta' => 'Per session',
        'price' => '£5 / day',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'Deep Clean',
        'meta' => 'Per session',
        'price' => '£10',
        'status' => 'active',
        'status_label' => 'Active',
    ],
    [
        'name' => 'After-hours access',
        'meta' => 'Per session',
        'price' => '£20',
        'status' => 'inactive',
        'status_label' => 'Inactive',
    ],
];

$spaceListingDetails = [
    ['label' => 'Space type', 'value' => 'Garden / Shed'],
    ['label' => 'Capacity', 'value' => '1–3 people · up to 4 pets at once'],
    ['label' => 'Size', 'value' => '~ 400 sq ft'],
    ['label' => 'Exact address', 'value' => '22 Studio Way, London, SW4 6NR'],
];

$spaceSuitableFor = [
    ['label' => 'Full Groom', 'active' => true],
    ['label' => 'Bath & Brush', 'active' => true],
    ['label' => 'Nail Trim', 'active' => true],
    ['label' => 'Ear Cleaning', 'active' => true],
    ['label' => 'Deshedding', 'active' => true],
    ['label' => 'Dematting', 'active' => true],
    ['label' => 'Medicated / Sensitive Skin Bath', 'active' => false],
    ['label' => 'Sanitary Trim', 'active' => false],
    ['label' => 'Paw Pad Trim', 'active' => false],
    ['label' => 'Teeth Brushing', 'active' => false],
    ['label' => 'Anal Gland Expression', 'active' => false],
    ['label' => 'Paw Balm', 'active' => false],
    ['label' => 'Perfume', 'active' => false],
    ['label' => 'Bandana / Bow', 'active' => false],
];

$spaceAmenities = [
    ['label' => 'Grooming Table', 'active' => true],
    ['label' => 'Bath', 'active' => true],
    ['label' => 'Dryer', 'active' => true],
    ['label' => 'Towels', 'active' => true],
    ['label' => 'Wi-Fi', 'active' => true],
    ['label' => 'Waiting area', 'active' => true],
    ['label' => 'Parking', 'active' => true],
];

$spacePetRestrictions = [
    ['type' => 'allow', 'text' => 'Suitable for all breeds and coat types'],
    ['type' => 'allow', 'text' => 'Leave space tidy'],
    ['type' => 'deny', 'text' => 'Not suitable for aggressive animals in heat'],
    ['type' => 'deny', 'text' => 'No overnight stays'],
    ['type' => 'deny', 'text' => 'Pets must remain supervised'],
    ['type' => 'deny', 'text' => 'No smoking'],
];

$spaceHygiene = [
    'Space regularly cleaned after each booking',
    'CCTV running 24/7',
    'Fire exits up to safety standards',
    'Fire alarm installed in every room',
];

$spacePolicyRestrictions = [
    ['type' => 'allow', 'text' => 'Suitable for all breeds and coat types'],
    ['type' => 'allow', 'text' => 'Pets must remain supervised'],
    ['type' => 'deny', 'text' => 'No overnight stays'],
    ['type' => 'deny', 'text' => 'Not suitable for aggressive animals in heat'],
];

$sharedPetPreferences = [
    ['label' => 'Pet types accepted', 'value' => 'Dogs, Others +'],
    ['label' => 'Pet size preferences', 'value' => 'Small 0-7 kg, Medium 8-18kg'],
];

$petPreferenceGroups = [
    [
        'label' => 'Pet types accepted',
        'tags' => [
            ['label' => 'Dogs', 'active' => true],
            ['label' => 'Other - Rabbits, Turtles, Guinea Pigs', 'active' => true],
            ['label' => 'Cats', 'active' => false],
        ],
    ],
    [
        'label' => 'Pet sizes accepted',
        'tags' => [
            ['label' => 'Small 0-7 kg', 'active' => true],
            ['label' => 'Medium 8-18 kg', 'active' => true],
            ['label' => 'Large 19+ kg', 'active' => false],
        ],
    ],
];

$petRestrictions = [
    ['type' => 'allow', 'text' => 'Works with dogs & cats up to 35kg'],
    ['type' => 'allow', 'text' => 'Special care for seniors & anxious pets'],
    ['type' => 'deny', 'text' => 'Not suitable for aggressive pets'],
];

$mapSouthwark = asset('images/admin/provider/map-southwark.png');

$groomerCoverageAreas = [
    [
        'name' => 'Southwark',
        'address' => "22 Studio Way, London, SW4 6NR\nUnited Kingdom",
        'radius' => '1 mile radius',
        'map' => $mapSouthwark,
        'maps_url' => 'https://maps.google.com/?q=Southwark,London',
    ],
    [
        'name' => 'Waterloo South Bank',
        'address' => "14 Riverside Walk, London, SE1 9PP\nUnited Kingdom",
        'radius' => '1 mile radius',
        'map' => $mapSouthwark,
        'maps_url' => 'https://maps.google.com/?q=Waterloo,London',
    ],
    [
        'name' => 'Cannon Street',
        'address' => "8 Cannon Street, London, EC4N 6AP\nUnited Kingdom",
        'radius' => '0.5 mile radius',
        'map' => $mapSouthwark,
        'maps_url' => 'https://maps.google.com/?q=Cannon+Street,London',
    ],
];

$coverageMeta = [
    'location' => 'West London',
    'accessibility' => 'Located near Victoria Embankment, with excellent public transport connections and free on-site parking available.',
];

$spaceDefaults = [
    'location_types' => [],
    'service_areas' => [
        [
            'name' => 'Southwark',
            'address' => "22 Studio Way, London, SW4 6NR\nUnited Kingdom",
            'availability' => 'Full Day · Half Day · Hourly',
            'radius' => 'Studio location',
            'map' => $mapSouthwark,
            'maps_url' => 'https://maps.google.com/?q=Southwark,London',
            'location' => 'West London',
            'accessibility' => 'Located near Victoria Embankment, with excellent public transport connections and free on-site parking available.',
        ],
    ],
    'services' => $spaceServices,
    'listing_details' => $spaceListingDetails,
    'listing_addons' => $spaceListingAddons,
    'suitable_for' => $spaceSuitableFor,
    'amenities' => $spaceAmenities,
    'addons' => [
        ['label' => 'Storage Locker £5', 'active' => true],
        ['label' => 'Deep Clean £10', 'active' => true],
        ['label' => 'After-hours access £20', 'active' => false],
    ],
    'pet_preferences' => $sharedPetPreferences,
    'pet_preference_groups' => $petPreferenceGroups,
    'pet_restrictions' => $spacePetRestrictions,
    'hygiene_standards' => $spaceHygiene,
    'policy_restrictions' => $spacePolicyRestrictions,
    'coverage_meta' => $coverageMeta,
    'gallery' => [
        asset('images/admin/provider/gallery/space/1.jpg'),
        asset('images/admin/provider/gallery/space/2.jpg'),
        asset('images/admin/provider/gallery/space/3.jpg'),
        asset('images/admin/provider/gallery/space/4.jpg'),
        asset('images/admin/provider/gallery/space/5.jpg'),
    ],
    'gallery_extra' => 0,
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
    'public_profile' => [
        'tagline' => 'Luxury grooming with a gentle touch.',
        'bio' => "Hi, I'm Sarah — a professional groomer with over 8 years' experience. I specialise in stress-free grooming for dogs and cats, using gentle techniques and premium products. Every pet leaves looking and feeling their best.",
    ],
    'gallery' => [
        asset('images/admin/provider/gallery/1.jpg'),
        asset('images/admin/provider/gallery/2.jpg'),
        asset('images/admin/provider/gallery/3.jpg'),
        asset('images/admin/provider/gallery/4.jpg'),
        asset('images/admin/provider/gallery/5.jpg'),
        asset('images/admin/provider/gallery/6.jpg'),
        asset('images/admin/provider/gallery/1.jpg'),
    ],
    'gallery_extra' => 2,
    'business_details' => [
        ['label' => 'Full name (must match ID)', 'value' => 'Sarah Smith'],
        ['label' => 'Business name', 'value' => 'Pawfect Grooming'],
        ['label' => 'Account type', 'value' => 'Freelance Groomer'],
        ['label' => 'Email', 'value' => 'sarah@pawfectgrooming.com'],
        ['label' => 'Phone', 'value' => '+447 8562 5458'],
        ['label' => 'Business reg. number', 'value' => '0123456'],
        ['label' => 'Joined', 'value' => '12 Jan 2023'],
        ['label' => 'Provider ID', 'value' => 'GS-00312'],
        ['label' => 'Last active', 'value' => '2 days ago'],
    ],
    'edit_business' => [
        'email' => 'sarah.w@pawfect.co.uk',
        'phone' => '+447 8562 5458',
        'address_1' => '142 Henderson Drive',
        'address_2' => '',
        'city' => 'London',
        'postcode' => 'SE25 63CB',
        'country' => 'United Kingdom',
        'readonly' => [
            ['label' => 'Business name', 'value' => 'Pawfect Grooming'],
            ['label' => 'Account type', 'value' => 'Freelance'],
            ['label' => 'Provider ID', 'value' => 'GS-00312'],
            ['label' => 'Joined', 'value' => '12 Jan 2023'],
        ],
    ],
    'policies' => [
        'cancellation' => [
            ['label' => 'Cancellation window', 'value' => '24 hours before appointment'],
            ['label' => 'Late Cancellation window', 'value' => 'Late Cancellation Fee 50% of booking price'],
            ['label' => 'No-show fee', 'value' => '100% of booking price'],
        ],
        'late_arrival' => [
            ['label' => 'Grace Period', 'value' => '10 minutes'],
            ['label' => 'Late arrival fee', 'value' => '£10 after 15 mins'],
        ],
    ],
    'hygiene_standards' => [
        'Tools sanitised between pets',
        'Clean table after each appointment',
        'Fresh towels per pet',
        'Equipment safety checked',
    ],
    'reviews_summary' => [
        'avg' => '4.8',
        'count' => '94',
        'items' => [
            [
                'author' => 'Jane Doe',
                'user_id' => 'USER-01452',
                'stars' => 5,
                'time_ago' => '2 weeks ago',
                'text' => 'Absolutely wonderful experience from start to finish. Biscuit was nervous at first but Sarah was so patient and gentle — he looked and smelled amazing afterwards. Will definitely book again.',
                'service' => 'Full Groom',
                'booking_id' => 'FG-0563-B12',
                'reply' => 'Thank you so much for your kind words! It was a pleasure caring for him — he was such a good boy once he settled in.',
            ],
            [
                'author' => 'Jane Doe',
                'user_id' => 'USER-01452',
                'stars' => 5,
                'time_ago' => '2 weeks ago',
                'text' => 'Absolutely wonderful experience from start to finish. Biscuit was nervous at first but Sarah was so patient and gentle — he looked and smelled amazing afterwards. Will definitely book again.',
                'service' => 'Full Groom',
                'booking_id' => 'FG-0563-B12',
                'reply' => 'Thank you so much for your kind words! It was a pleasure caring for him — he was such a good boy once he settled in.',
            ],
            [
                'author' => 'Tom Harris',
                'user_id' => 'USER-01488',
                'stars' => 4,
                'time_ago' => '1 month ago',
                'text' => 'Quick nail trim and friendly service. Studio was clean and calm. Would have given five stars if parking were a little easier.',
                'service' => 'Nail Trim',
                'booking_id' => 'NT-0499-B03',
                'reply' => 'Thanks Tom — glad Max was comfortable. We are looking at extra parking guidance for future visits.',
            ],
            [
                'author' => 'Alex Rivera',
                'user_id' => 'USER-01519',
                'stars' => 5,
                'time_ago' => '6 weeks ago',
                'text' => 'Best mobile groomer we have used. Punctual, professional, and Luna looked fantastic.',
                'service' => 'Mobile Grooming',
                'booking_id' => 'MG-0521-B17',
                'reply' => null,
            ],
            [
                'author' => 'Mia Brooks',
                'user_id' => 'USER-01622',
                'stars' => 5,
                'time_ago' => '2 months ago',
                'text' => 'Puppy intro session was perfect. Clear communication and a calm environment for our nervous pup.',
                'service' => 'Puppy Intro',
                'booking_id' => 'PI-0388-B21',
                'reply' => 'So happy Poppy enjoyed her first groom — looking forward to seeing you both again!',
            ],
            [
                'author' => 'Sam Patel',
                'user_id' => 'USER-01690',
                'stars' => 4,
                'time_ago' => '3 months ago',
                'text' => 'Bath & tidy left our spaniel fluffy and fresh. Slight wait at drop-off but overall excellent.',
                'service' => 'Bath & Tidy',
                'booking_id' => 'BT-0412-B08',
                'reply' => 'Appreciate the feedback Sam — we have adjusted drop-off slots to reduce wait times.',
            ],
        ],
    ],
    'location_types' => ['Home studio', 'Salon', 'Home visits'],
    'services' => $groomerServices,
    'addons' => $groomerAddons,
    'pet_preferences' => $sharedPetPreferences,
    'pet_preference_groups' => $petPreferenceGroups,
    'pet_restrictions' => $petRestrictions,
    'service_areas' => $groomerCoverageAreas,
    'coverage_meta' => $coverageMeta,
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
        ['type' => 'booking', 'title' => 'Booking GS-0563-B12 completed · £55.00', 'time' => '18 Apr 2025 · 14:32'],
        ['type' => 'hold', 'title' => 'Payout of £890 held — dispute open', 'time' => '18 Apr 2025 · 11:32'],
        ['type' => 'pass', 'title' => 'Verification approved by Michelle M', 'time' => '05 Mar 2025 · 21:50'],
        ['type' => 'password', 'title' => 'Account created · signed up via email', 'time' => '01 Dec 2024 · 18:55'],
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
    'compliance' => [
        'groomer' => [
            'status' => 'verified',
            'banner_title' => 'Account verified',
            'banner_text' => 'All required documents verified & approved.',
            'verification' => [
                'status_label' => 'Verified',
                'identity_title' => 'Identity verified',
                'identity_text' => 'Provider passed third-party verification at onboarding',
                'system_title' => 'Third-party verification system',
                'system_text' => 'Documents and identity files are held externally.',
                'meta' => [
                    ['label' => 'Verified', 'value' => '12 Jan 2023'],
                    ['label' => 'Type', 'value' => 'Freelance Groomer'],
                    ['label' => 'Result', 'value' => 'Pass', 'tone' => 'pass'],
                    ['label' => 'Reference', 'value' => 'VRF-2023-00142'],
                ],
            ],
            'business' => [
                ['label' => 'Account type', 'value' => 'Freelance Groomer', 'key' => 'account_type'],
                ['label' => 'Business name', 'value' => 'Pawfect Grooming', 'key' => 'business_name'],
                ['label' => 'Owner', 'value' => 'Sarah Williams', 'key' => 'owner'],
                ['label' => 'Provider ID', 'value' => 'GS-00312', 'key' => 'provider_id'],
                ['label' => 'Joined', 'value' => '12 Jan 2023', 'key' => 'joined'],
                ['label' => 'Business reg. number', 'value' => '—', 'key' => 'reg_number'],
            ],
            'agreements_status' => 'All signed',
            'agreements' => [
                [
                    'title' => 'FursGo refund policy',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
                [
                    'title' => 'Animal welfare statement',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
                [
                    'title' => 'Compliance declaration',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
                [
                    'title' => 'Provider terms of service',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
                [
                    'title' => 'Privacy & data policy',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
            ],
            'activity' => [
                [
                    'title' => 'Annual re-verification passed',
                    'time' => '18 Apr 2025 · 14:32',
                    'tone' => 'pass',
                ],
                [
                    'title' => 'Annual re-verification passed',
                    'time' => '18 Apr 2025 · 11:32',
                    'tone' => 'pass',
                ],
                [
                    'title' => 'Annual re-verification passed',
                    'time' => '05 Mar 2025 · 21:50',
                    'tone' => 'pass',
                ],
                [
                    'title' => 'Verification submitted by provider · Documents uploaded for review',
                    'time' => '01 Dec 2024 · 18:55',
                    'tone' => 'neutral',
                ],
            ],
        ],
        'space' => [
            'status' => 'needs_info',
            'banner_title' => 'More Info Needed',
            'banner_text' => 'Required documents are pending · Awaiting resubmission.',
            'verification' => [
                'status_label' => 'Verified',
                'identity_title' => 'Identity verified',
                'identity_text' => 'Provider passed third-party verification at onboarding',
                'system_title' => 'Third-party verification system',
                'system_text' => 'Documents and identity files are held externally.',
                'meta' => [
                    ['label' => 'Verified', 'value' => '12 Jan 2023'],
                    ['label' => 'Type', 'value' => 'Registered Space Host'],
                    ['label' => 'Result', 'value' => 'Pass', 'tone' => 'pass'],
                    ['label' => 'Reference', 'value' => 'VRF-2023-00142'],
                ],
            ],
            'business' => [
                ['label' => 'Account type', 'value' => 'Registered Space Host', 'key' => 'account_type'],
                ['label' => 'Business name', 'value' => 'The Garden Grooming Spot', 'key' => 'business_name'],
                ['label' => 'Owner', 'value' => 'Dev Evans', 'key' => 'owner'],
                ['label' => 'Provider ID', 'value' => 'GS-00312', 'key' => 'provider_id'],
                ['label' => 'Joined', 'value' => '12 Jan 2023', 'key' => 'joined'],
                ['label' => 'Business reg. number', 'value' => '87654321', 'key' => 'reg_number'],
            ],
            'agreements_status' => 'Overdue agreements (1)',
            'agreements_tone' => 'overdue',
            'agreements' => [
                [
                    'title' => 'FursGo refund policy',
                    'meta' => 'Renewal overdue · was due 12 Jan 2025 · last signed 12 Jan 2024',
                    'status' => 'overdue',
                ],
                [
                    'title' => 'Animal welfare statement',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
                [
                    'title' => 'Compliance declaration',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
                [
                    'title' => 'Provider terms of service',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
                [
                    'title' => 'Privacy & data policy',
                    'meta' => 'Signed 12 Jan 2026 · Annual renewal · due Jan 2027',
                    'status' => 'signed',
                ],
            ],
            'activity' => [
                [
                    'title' => 'Annual re-verification passed',
                    'time' => '18 Apr 2025 · 14:32',
                    'tone' => 'pass',
                ],
                [
                    'title' => 'Annual re-verification passed',
                    'time' => '18 Apr 2025 · 11:32',
                    'tone' => 'pass',
                ],
                [
                    'title' => 'Annual re-verification passed',
                    'time' => '05 Mar 2025 · 21:50',
                    'tone' => 'pass',
                ],
                [
                    'title' => 'Verification submitted by provider · Documents uploaded for review',
                    'time' => '01 Dec 2024 · 18:55',
                    'tone' => 'neutral',
                ],
            ],
        ],
    ],
    'support' => [
        'filters' => [
            'all' => 14,
            'open' => 1,
            'in_progress' => 3,
            'resolved' => 4,
            'closed' => 1,
            'reopened' => 1,
        ],
        'rows' => [
            [
                'id' => 'tkt-1',
                'ticket_id' => 'SPRT-00412',
                'subject' => 'My account has been flagged — I believe this is incorrect',
                'category' => 'App/Technical',
                'status' => 'open',
                'status_label' => 'Open',
                'opened' => '2 days ago',
                'assigned' => 'Michelle M',
                'month' => 'April',
                'booking_ref' => '—',
            ],
            [
                'id' => 'tkt-2',
                'ticket_id' => 'SPRT-00413',
                'subject' => 'Payout not received — week ending 03 Jun',
                'category' => 'Payment',
                'status' => 'in_progress',
                'status_label' => 'In progress',
                'opened' => '3 days ago',
                'assigned' => 'Ben M',
                'month' => 'April',
            ],
            [
                'id' => 'tkt-3',
                'ticket_id' => 'SPRT-00401',
                'subject' => 'Unable to upload insurance documents in app',
                'category' => 'App/Technical',
                'status' => 'in_progress',
                'status_label' => 'In progress',
                'opened' => '5 days ago',
                'assigned' => 'Michelle M',
                'month' => 'April',
            ],
            [
                'id' => 'tkt-4',
                'ticket_id' => 'SPRT-00388',
                'subject' => 'Client no-show — requesting booking fee retention',
                'category' => 'Booking',
                'status' => 'resolved',
                'status_label' => 'Resolved',
                'opened' => '2 weeks ago',
                'assigned' => 'Michelle M',
                'month' => 'April',
            ],
            [
                'id' => 'tkt-5',
                'ticket_id' => 'SPRT-00372',
                'subject' => 'Account flagged for review after client complaint',
                'category' => 'Account',
                'status' => 'resolved',
                'status_label' => 'Resolved',
                'opened' => '3 weeks ago',
                'assigned' => 'Ben M',
                'month' => 'February',
            ],
            [
                'id' => 'tkt-6',
                'ticket_id' => 'SPRT-00355',
                'subject' => "Can't update my service area postcode coverage",
                'category' => 'Other',
                'status' => 'reopened',
                'status_label' => 'Re-opened',
                'opened' => '2 months ago',
                'assigned' => 'Michelle M',
                'month' => 'February',
            ],
            [
                'id' => 'tkt-7',
                'ticket_id' => 'SPRT-00341',
                'subject' => 'Need help changing availability for Bank Holiday',
                'category' => 'Booking',
                'status' => 'in_progress',
                'status_label' => 'In progress',
                'opened' => '1 month ago',
                'assigned' => 'Ben M',
                'month' => 'February',
            ],
            [
                'id' => 'tkt-8',
                'ticket_id' => 'SPRT-00320',
                'subject' => 'Promo listing fee charged twice in March',
                'category' => 'Payment',
                'status' => 'resolved',
                'status_label' => 'Resolved',
                'opened' => '20/05/24',
                'assigned' => 'Michelle M',
                'month' => '2024',
            ],
            [
                'id' => 'tkt-9',
                'ticket_id' => 'SPRT-00305',
                'subject' => 'App crashes when opening earnings dashboard',
                'category' => 'App/Technical',
                'status' => 'closed',
                'status_label' => 'Closed',
                'opened' => '12/04/24',
                'assigned' => 'Ben M',
                'month' => '2024',
            ],
            [
                'id' => 'tkt-10',
                'ticket_id' => 'SPRT-00291',
                'subject' => 'How do I add a second salon location?',
                'category' => 'Account',
                'status' => 'resolved',
                'status_label' => 'Resolved',
                'opened' => '03/03/24',
                'assigned' => 'Michelle M',
                'month' => '2024',
            ],
        ],
    ],
    'groomer' => [
        'name' => 'Pawfect Grooming',
        'type' => 'groomer',
        'type_label' => 'Groomer',
        'avatar' => $fallbackAvatar,
        'insurance_badge' => 'Freelance',
        'insurance_class' => 'freelance',
        'tagline' => 'Luxury grooming with a gentle touch.',
        'bio' => "Hi, I'm Sarah — a professional groomer with over 8 years' experience. I specialise in stress-free grooming for dogs and cats, using gentle techniques and premium products. Every pet leaves looking and feeling their best.",
        'business_details' => [
            ['label' => 'Full name (must match ID)', 'value' => 'Sarah Smith'],
            ['label' => 'Business name', 'value' => 'Pawfect Grooming'],
            ['label' => 'Account type', 'value' => 'Freelance Groomer'],
            ['label' => 'Email', 'value' => 'sarah@pawfectgrooming.com'],
            ['label' => 'Phone', 'value' => '+447 8562 5458'],
            ['label' => 'Business reg. number', 'value' => '0123456'],
            ['label' => 'Joined', 'value' => '12 Jan 2023'],
            ['label' => 'Provider ID', 'value' => 'GS-00312'],
            ['label' => 'Last active', 'value' => '2 days ago'],
        ],
        'location_types' => ['Home studio', 'Salon', 'Home visits'],
        'service_areas' => $groomerCoverageAreas,
        'services' => $groomerServices,
        'addons' => $groomerAddons,
        'pet_preferences' => $sharedPetPreferences,
        'pet_preference_groups' => $petPreferenceGroups,
        'pet_restrictions' => $petRestrictions,
        'coverage_meta' => $coverageMeta,
    ],
    'space' => array_merge($spaceDefaults, [
        'name' => 'The Garden Grooming Spot',
        'type' => 'space',
        'type_label' => 'Space Host',
        'avatar' => $spaceAvatar,
        'insurance_badge' => 'Registered',
        'insurance_class' => 'registered',
        'tagline' => 'Luxury grooming with a gentle touch.',
        'bio' => 'A bright, clean, fully equipped grooming outdoor space ideal for professional use. Outdoor garden grooming area. Calm, spacious, and ideal for stress-free sessions in fresh air.',
        'email' => 'dev.e@gardenspot.co.uk',
        'phone' => '+447 8562 5458',
        'business_details' => [
            ['label' => 'Full name (must match ID)', 'value' => 'Dev Étienne'],
            ['label' => 'Business name', 'value' => 'The Garden Grooming Spot'],
            ['label' => 'Account type', 'value' => 'Space Owner'],
            ['label' => 'Email', 'value' => 'dev.e@gardenspot.co.uk'],
            ['label' => 'Phone', 'value' => '+447 8562 5458'],
            ['label' => 'Business reg. number', 'value' => '0123456'],
            ['label' => 'Joined', 'value' => '12 Jan 2023'],
            ['label' => 'Provider ID', 'value' => 'SP-00312'],
            ['label' => 'Last active', 'value' => '2 days ago'],
        ],
        'reviews_summary' => [
            'avg' => '4.8',
            'count' => '94',
            'items' => [
                [
                    'author' => 'Jane Doe',
                    'user_id' => 'USER-01452',
                    'stars' => 5,
                    'time_ago' => '2 weeks ago',
                    'text' => 'Absolutely wonderful experience from start to finish. The garden space was calm and spotless — Biscuit settled quickly and the setup made the whole session feel easy.',
                    'service' => 'Garden / Shed · Half-Day',
                    'booking_id' => 'SP-10291',
                    'reply' => 'Thank you so much for your kind words! It was a pleasure hosting you — glad Biscuit felt at home.',
                ],
                [
                    'author' => 'Tom Harris',
                    'user_id' => 'USER-01488',
                    'stars' => 4,
                    'time_ago' => '1 month ago',
                    'text' => 'Great outdoor shed space with everything I needed. Parking was straightforward and the dryer worked perfectly.',
                    'service' => 'Garden / Shed · Hourly',
                    'booking_id' => 'SP-10112',
                    'reply' => null,
                ],
                [
                    'author' => 'Alex Rivera',
                    'user_id' => 'USER-01519',
                    'stars' => 5,
                    'time_ago' => '6 weeks ago',
                    'text' => 'Clean, bright, and well equipped. Booked a full day and would happily return.',
                    'service' => 'Garden / Shed · Full-day',
                    'booking_id' => 'SP-10088',
                    'reply' => 'Thanks Alex — looking forward to hosting you again!',
                ],
                [
                    'author' => 'Mia Brooks',
                    'user_id' => 'USER-01622',
                    'stars' => 5,
                    'time_ago' => '2 months ago',
                    'text' => 'Perfect for mobile grooming. Quiet garden area and clear access instructions.',
                    'service' => 'Garden / Shed · Half-Day',
                    'booking_id' => 'SP-09941',
                    'reply' => null,
                ],
            ],
        ],
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
    @continue(! in_array($profileId, $openedProviderIds ?? [], true))
    @php
        $isDual = ! empty($profile['dual']);
        $defaultView = $profile['default_view'] ?? $profile['type'] ?? 'groomer';
    @endphp
    <div
        class="admin-co-layout"
        wire:key="provider-detail-{{ $profileId }}"
        x-show="selectedProviderId === '{{ $profileId }}'"
        x-cloak
        @admin-provider-opened.window="if ($event.detail && $event.detail.id === '{{ $profileId }}') viewAs = @js($defaultView)"
        x-data="{
            viewAs: @js($defaultView),
            dual: @js($isDual),
            profileSubTab: 'profile',
            editBusinessOpen: false,
            editEmail: @js($profile['edit_business']['email'] ?? $profile['email'] ?? ''),
            editPhone: @js($profile['edit_business']['phone'] ?? $profile['phone'] ?? ''),
            editAddress1: @js($profile['edit_business']['address_1'] ?? ''),
            editAddress2: @js($profile['edit_business']['address_2'] ?? ''),
            editCity: @js($profile['edit_business']['city'] ?? ''),
            editPostcode: @js($profile['edit_business']['postcode'] ?? ''),
            editCountry: @js($profile['edit_business']['country'] ?? ''),
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
                const open = resetPasswordOpen || suspendOpen || flagOpen || deleteOpen || editBusinessOpen;
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
            $watch('editBusinessOpen', () => $nextTick(() => syncModalLock()));
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

            <div x-show="detailTab === 'profile'" x-cloak>
                <x-admin.provider.profile :profile="$profile" />
            </div>

            <div x-show="detailTab === 'activity'" x-cloak>
                <x-admin.provider.activity :profile="$profile" />
            </div>

            <div x-show="detailTab === 'account'" x-cloak>
                <x-admin.provider.account :profile="$profile" />
            </div>

            <div x-show="detailTab === 'compliance'" x-cloak>
                <x-admin.provider.compliance :profile="$profile" />
            </div>

            <div x-show="detailTab === 'support'" x-cloak>
                <x-admin.provider.support :profile="$profile" />
            </div>

            @foreach (['bookings' => 'Bookings', 'payouts' => 'Payouts'] as $tabKey => $tabLabel)
            <div class="admin-co-placeholder" x-show="detailTab === '{{ $tabKey }}'" x-cloak>
                <h2 class="admin-page-title mb-0">{{ $tabLabel }}</h2>
                <p class="admin-section-label mb-0">This section will be built next.</p>
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
