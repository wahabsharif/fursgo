@props(['profile'])

@php
$isDual = ! empty($profile['dual']);
$isSpace = ($profile['type'] ?? '') === 'space';
$groomerPack = $profile['groomer'] ?? null;
$spacePack = $profile['space'] ?? null;

$publicProfile = $profile['public_profile'] ?? [];
$gallery = $profile['gallery'] ?? [];
$galleryExtra = (int) ($profile['gallery_extra'] ?? 0);
$businessDetails = $profile['business_details'] ?? ($profile['details'] ?? []);
$notes = $profile['notes'] ?? [];
$activity = $profile['activity'] ?? [];
$policies = $profile['policies'] ?? [];
$hygieneStandards = $profile['hygiene_standards'] ?? [];
$policyRestrictions = $profile['pet_restrictions'] ?? [];
$groomerHygiene = $hygieneStandards;
$groomerPolicyRestrictions = $policyRestrictions;
$reviewsSummary = $profile['reviews_summary'] ?? [];
$reviewItems = $reviewsSummary['items'] ?? [];
$cancellationPolicies = $policies['cancellation'] ?? [];
$lateArrivalPolicies = $policies['late_arrival'] ?? [];

$groomerLocationTypes = $isDual
? ($groomerPack['location_types'] ?? [])
: ($profile['location_types'] ?? []);
$groomerServices = $isDual
? ($groomerPack['services'] ?? [])
: ($profile['services'] ?? []);
$groomerAddons = $isDual
? ($groomerPack['addons'] ?? [])
: ($profile['addons'] ?? []);
$groomerPetPreferenceGroups = $isDual
? ($groomerPack['pet_preference_groups'] ?? [])
: ($profile['pet_preference_groups'] ?? []);
$groomerPetRestrictions = $isDual
? ($groomerPack['pet_restrictions'] ?? [])
: ($profile['pet_restrictions'] ?? []);
$groomerServiceAreas = $isDual
? ($groomerPack['service_areas'] ?? [])
: ((! $isSpace) ? ($profile['service_areas'] ?? []) : []);
$groomerCoverageMeta = $isDual
? ($groomerPack['coverage_meta'] ?? [])
: ($profile['coverage_meta'] ?? []);

$spaceServices = $isDual
? ($spacePack['services'] ?? [])
: ($isSpace ? ($profile['services'] ?? []) : []);
$spaceAddons = $isDual
? ($spacePack['addons'] ?? [])
: ($isSpace ? ($profile['addons'] ?? []) : []);
$spaceListingDetails = $isDual
? ($spacePack['listing_details'] ?? [])
: ($isSpace ? ($profile['listing_details'] ?? []) : []);
$spaceListingAddons = $isDual
? ($spacePack['listing_addons'] ?? [])
: ($isSpace ? ($profile['listing_addons'] ?? []) : []);
$spaceSuitableFor = $isDual
? ($spacePack['suitable_for'] ?? [])
: ($isSpace ? ($profile['suitable_for'] ?? []) : []);
$spaceAmenities = $isDual
? ($spacePack['amenities'] ?? [])
: ($isSpace ? ($profile['amenities'] ?? []) : []);
$spacePetPreferenceGroups = $isDual
? ($spacePack['pet_preference_groups'] ?? [])
: ($isSpace ? ($profile['pet_preference_groups'] ?? []) : []);
$spacePetRestrictions = $isDual
? ($spacePack['pet_restrictions'] ?? [])
: ($isSpace ? ($profile['pet_restrictions'] ?? []) : []);
$spaceServiceAreas = $isDual
? ($spacePack['service_areas'] ?? [])
: ($isSpace ? ($profile['service_areas'] ?? []) : []);
$spaceCoverageMeta = $isDual
? ($spacePack['coverage_meta'] ?? [])
: ($isSpace ? ($profile['coverage_meta'] ?? []) : []);
$spaceHygiene = $isDual
? ($spacePack['hygiene_standards'] ?? ($profile['hygiene_standards'] ?? []))
: ($isSpace ? ($profile['hygiene_standards'] ?? []) : ($profile['hygiene_standards'] ?? []));
$spacePolicyRestrictions = $isDual
? ($spacePack['policy_restrictions'] ?? ($spacePack['pet_restrictions'] ?? []))
: ($isSpace ? ($profile['policy_restrictions'] ?? ($profile['pet_restrictions'] ?? [])) : []);
$spaceGallery = $isDual
? ($spacePack['gallery'] ?? $gallery)
: ($isSpace ? ($profile['gallery'] ?? $gallery) : $gallery);
$spaceGalleryExtra = $isDual
? (int) ($spacePack['gallery_extra'] ?? 0)
: ($isSpace ? (int) ($profile['gallery_extra'] ?? 0) : $galleryExtra);
$spaceReviewsSummary = $isDual
? ($spacePack['reviews_summary'] ?? $reviewsSummary)
: $reviewsSummary;
$spaceReviewItems = $spaceReviewsSummary['items'] ?? [];
$groomerReviewsSummary = $reviewsSummary;
$groomerReviewItems = $reviewItems;

$primarySpaceArea = $spaceServiceAreas[0] ?? null;

$showGroomerOfferings = $isDual || ! $isSpace;
$showSpaceOfferings = $isDual || $isSpace;

$defaultName = $profile['name'] ?? 'Provider';
$defaultAvatar = $profile['avatar'] ?? asset('images/profile_image.png');
$defaultTagline = $publicProfile['tagline'] ?? '';
$defaultBio = $publicProfile['bio'] ?? '';

$groomerName = $groomerPack['name'] ?? $defaultName;
$groomerAvatar = $groomerPack['avatar'] ?? $defaultAvatar;
$groomerTagline = $groomerPack['tagline'] ?? $defaultTagline;
$groomerBio = $groomerPack['bio'] ?? $defaultBio;
$groomerDetails = $groomerPack['business_details'] ?? $businessDetails;

$spaceName = $spacePack['name'] ?? $defaultName;
$spaceAvatar = $spacePack['avatar'] ?? $defaultAvatar;
$spaceTagline = $spacePack['tagline'] ?? $defaultTagline;
$spaceBio = $spacePack['bio'] ?? $defaultBio;
$spaceDetails = $spacePack['business_details'] ?? $businessDetails;

$visibleGallery = array_slice($gallery, 0, 7);
$galleryCount = count($visibleGallery);
$editBusiness = $profile['edit_business'] ?? [];
$editReadonly = $editBusiness['readonly'] ?? [];
$providerIdLabel = collect($businessDetails)->firstWhere('label', 'Provider ID')['value']
?? ($editReadonly[2]['value'] ?? ($profile['id'] ?? ''));
$modalSubtitleName = $profile['name'] ?? 'Provider';
@endphp

<div
    class="admin-co-overview admin-bp-profile"
    :class="{ 'is-space-view': dual ? viewAs === 'space' : {{ $isSpace ? 'true' : 'false' }} }">
    <div class="admin-co-overview-head">
        <h2 class="admin-page-title mb-0">Profile</h2>
        <button type="button" class="admin-btn-dark">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
            </svg>
            Export Data
        </button>
    </div>

    <nav class="admin-bp-profile-subnav" aria-label="Profile sections">
        @foreach (['profile' => 'Profile', 'services' => 'Services', 'policies' => 'Policies', 'reviews' => 'Reviews'] as $subKey => $subLabel)
        <button
            type="button"
            class="admin-bp-profile-subnav-btn"
            :class="{ 'is-active': profileSubTab === '{{ $subKey }}' }"
            @click="profileSubTab = '{{ $subKey }}'">
            {{ $subLabel }}
        </button>
        @endforeach
    </nav>

    {{-- Profile sub-tab --}}
    <div class="admin-bp-profile-panels" x-show="profileSubTab === 'profile'" x-cloak>
        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Public profile">
                <button type="button" class="admin-co-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                        <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Edit
                </button>
            </x-admin.customer.section-header>

            @if ($isDual && $groomerPack && $spacePack)
            <div class="admin-bp-public-profile" x-show="viewAs === 'groomer'" x-cloak>
                <img class="admin-bp-public-avatar" src="{{ $groomerAvatar }}" alt="{{ $groomerName }}" width="72" height="72">
                <div class="admin-bp-public-copy">
                    <p class="admin-bp-public-title">
                        <span class="admin-bp-public-name">{{ $groomerName }}</span>
                        @if ($groomerTagline !== '')
                        <span class="admin-bp-public-sep" aria-hidden="true">-</span>
                        <span class="admin-bp-public-tagline">{{ $groomerTagline }}</span>
                        @endif
                    </p>
                    @if ($groomerBio !== '')
                    <p class="admin-bp-public-bio">{{ $groomerBio }}</p>
                    @endif
                </div>
            </div>
            <div class="admin-bp-public-profile" x-show="viewAs === 'space'" x-cloak>
                <img class="admin-bp-public-avatar" src="{{ $spaceAvatar }}" alt="{{ $spaceName }}" width="72" height="72">
                <div class="admin-bp-public-copy">
                    <p class="admin-bp-public-title">
                        <span class="admin-bp-public-name">{{ $spaceName }}</span>
                        @if ($spaceTagline !== '')
                        <span class="admin-bp-public-sep" aria-hidden="true">-</span>
                        <span class="admin-bp-public-tagline">{{ $spaceTagline }}</span>
                        @endif
                    </p>
                    @if ($spaceBio !== '')
                    <p class="admin-bp-public-bio">{{ $spaceBio }}</p>
                    @endif
                </div>
            </div>
            @else
            <div class="admin-bp-public-profile">
                <img class="admin-bp-public-avatar" src="{{ $defaultAvatar }}" alt="{{ $defaultName }}" width="72" height="72">
                <div class="admin-bp-public-copy">
                    <p class="admin-bp-public-title">
                        <span class="admin-bp-public-name">{{ $defaultName }}</span>
                        @if ($defaultTagline !== '')
                        <span class="admin-bp-public-sep" aria-hidden="true">-</span>
                        <span class="admin-bp-public-tagline">{{ $defaultTagline }}</span>
                        @endif
                    </p>
                    @if ($defaultBio !== '')
                    <p class="admin-bp-public-bio">{{ $defaultBio }}</p>
                    @endif
                </div>
            </div>
            @endif

            @if ($isDual && $groomerPack && $spacePack)
            <div class="admin-bp-gallery-block" x-show="viewAs === 'groomer'" x-cloak>
                <h4 class="admin-bp-gallery-title">Photo gallery</h4>
                <div class="admin-bp-gallery-grid">
                    @foreach (array_slice($gallery, 0, 7) as $index => $image)
                    @php
                    $gCount = min(7, count($gallery));
                    $isLast = $index === $gCount - 1;
                    $showMore = $isLast && $galleryExtra > 0;
                    @endphp
                    <div class="admin-bp-gallery-item {{ $showMore ? 'has-more' : '' }}">
                        <img src="{{ $image }}" alt="Gallery photo {{ $index + 1 }}" loading="lazy">
                        @if ($showMore)
                        <span class="admin-bp-gallery-more" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.77778 4.88889C10.0861 4.88879 10.3831 5.00525 10.6092 5.21491C10.8353 5.42458 10.9738 5.71196 10.9969 6.01944L11 6.11111V9.77778C11.0001 10.0861 10.8836 10.3831 10.674 10.6092C10.4643 10.8353 10.1769 10.9738 9.86944 10.9969L9.77778 11H7.33333C7.02498 11.0001 6.72799 10.8836 6.50189 10.674C6.27579 10.4643 6.13729 10.1769 6.11417 9.86944L6.11111 9.77778V6.11111C6.11101 5.80276 6.22747 5.50576 6.43714 5.27966C6.6468 5.05357 6.93418 4.91507 7.24167 4.89194L7.33333 4.88889H9.77778ZM3.66667 7.33333C3.99082 7.33333 4.3017 7.4621 4.53091 7.69131C4.76012 7.92053 4.88889 8.2314 4.88889 8.55556V9.77778C4.88889 10.1019 4.76012 10.4128 4.53091 10.642C4.3017 10.8712 3.99082 11 3.66667 11H1.22222C0.898069 11 0.587192 10.8712 0.357981 10.642C0.128769 10.4128 0 10.1019 0 9.77778V8.55556C0 8.2314 0.128769 7.92053 0.357981 7.69131C0.587192 7.4621 0.898069 7.33333 1.22222 7.33333H3.66667ZM3.66667 0C3.99082 0 4.3017 0.128769 4.53091 0.357981C4.76012 0.587192 4.88889 0.898069 4.88889 1.22222V4.88889C4.88889 5.21304 4.76012 5.52392 4.53091 5.75313C4.3017 5.98234 3.99082 6.11111 3.66667 6.11111H1.22222C0.898069 6.11111 0.587192 5.98234 0.357981 5.75313C0.128769 5.52392 0 5.21304 0 4.88889V1.22222C0 0.898069 0.128769 0.587192 0.357981 0.357981C0.587192 0.128769 0.898069 0 1.22222 0H3.66667ZM9.77778 0C10.1019 0 10.4128 0.128769 10.642 0.357981C10.8712 0.587192 11 0.898069 11 1.22222V2.44444C11 2.7686 10.8712 3.07947 10.642 3.30869C10.4128 3.5379 10.1019 3.66667 9.77778 3.66667H7.33333C7.00918 3.66667 6.6983 3.5379 6.46909 3.30869C6.23988 3.07947 6.11111 2.7686 6.11111 2.44444V1.22222C6.11111 0.898069 6.23988 0.587192 6.46909 0.357981C6.6983 0.128769 7.00918 0 7.33333 0H9.77778Z" fill="white" />
                            </svg>
                            +{{ $galleryExtra }} more
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="admin-bp-gallery-block" x-show="viewAs === 'space'" x-cloak>
                <h4 class="admin-bp-gallery-title">Photo gallery</h4>
                <div class="admin-bp-gallery-grid admin-bp-gallery-grid--space">
                    @foreach ($spaceGallery as $index => $image)
                    <div class="admin-bp-gallery-item">
                        <img src="{{ $image }}" alt="Space gallery photo {{ $index + 1 }}" loading="lazy">
                    </div>
                    @endforeach
                </div>
            </div>
            @elseif ($galleryCount > 0)
            <div class="admin-bp-gallery-block">
                <h4 class="admin-bp-gallery-title">Photo gallery</h4>
                <div class="admin-bp-gallery-grid {{ $isSpace ? 'admin-bp-gallery-grid--space' : '' }}">
                    @foreach ($visibleGallery as $index => $image)
                    @php
                    $isLast = $index === $galleryCount - 1;
                    $showMore = $isLast && $galleryExtra > 0;
                    @endphp
                    <div class="admin-bp-gallery-item {{ $showMore ? 'has-more' : '' }}">
                        <img src="{{ $image }}" alt="Gallery photo {{ $index + 1 }}" loading="lazy">
                        @if ($showMore)
                        <span class="admin-bp-gallery-more" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 18 18" fill="none">
                                <rect x="1.5" y="1.5" width="6" height="6" rx="1.2" stroke="#FDFDFD" stroke-width="1.2" />
                                <rect x="10.5" y="1.5" width="6" height="6" rx="1.2" stroke="#FDFDFD" stroke-width="1.2" />
                                <rect x="1.5" y="10.5" width="6" height="6" rx="1.2" stroke="#FDFDFD" stroke-width="1.2" />
                                <rect x="10.5" y="10.5" width="6" height="6" rx="1.2" stroke="#FDFDFD" stroke-width="1.2" />
                            </svg>
                            +{{ $galleryExtra }} more
                        </span>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </section>

        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Business Details">
                <button type="button" class="admin-co-link-btn" @click="editBusinessOpen = true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                        <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Edit
                </button>
            </x-admin.customer.section-header>

            @if ($isDual && $groomerPack && $spacePack)
            <dl class="admin-co-details" x-show="viewAs === 'groomer'" x-cloak>
                @foreach ($groomerDetails as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                </div>
                @endforeach
            </dl>
            <dl class="admin-co-details" x-show="viewAs === 'space'" x-cloak>
                @foreach ($spaceDetails as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                </div>
                @endforeach
            </dl>
            @else
            <dl class="admin-co-details">
                @foreach ($businessDetails as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                </div>
                @endforeach
            </dl>
            @endif
        </section>

        {{-- Admin Notes (same Alpine pattern as Overview) --}}
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
                <x-admin.customer.section-header title="Admin notes">
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
                    <label class="admin-co-note-compose-label" for="bp-profile-note-{{ $profile['id'] }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                    <textarea
                        id="bp-profile-note-{{ $profile['id'] }}"
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

        <section class="admin-card admin-co-panel admin-co-activity-panel">
            <x-admin.customer.section-header title="Recent Activity">
                <button type="button" class="admin-co-link-btn" @click="switchDetailTab('activity')">
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

    {{-- Services sub-tab --}}
    <div class="admin-bp-profile-panels" x-show="profileSubTab === 'services'" x-cloak>
        @if ($showGroomerOfferings)
        <div class="admin-bp-profile-panels" x-show="!dual || viewAs === 'groomer'" @if ($isDual) x-cloak @endif>
            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Services & Pricing">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>

                <ul class="admin-bp-svc-list">
                    @foreach ($groomerServices as $service)
                    @php
                    $isInactive = ($service['status'] ?? 'active') === 'inactive';
                    $pricePrefix = $service['price_prefix'] ?? 'From';
                    $priceText = trim(($pricePrefix !== '' ? $pricePrefix . ' ' : '') . ($service['price'] ?? ''));
                    @endphp
                    <li class="admin-bp-svc-row {{ $isInactive ? 'is-inactive' : '' }}">
                        <div class="admin-bp-svc-main">
                            <p class="admin-bp-svc-name">{{ $service['name'] }}</p>
                            @if (! empty($service['meta']))
                            <p class="admin-bp-svc-meta">{{ $service['meta'] }}</p>
                            @endif
                        </div>
                        <div class="admin-bp-svc-side">
                            <span class="admin-bp-svc-price">{{ $priceText }}</span>
                            <span class="admin-po-status has-dot is-{{ $service['status'] ?? 'active' }}">
                                {{ $service['status_label'] ?? ucfirst($service['status'] ?? 'active') }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>

                @if (count($groomerAddons) > 0)
                <div class="admin-bp-addons-block">
                    <p class="admin-bp-addons-label">Add-on Services</p>
                    <div class="admin-bp-addons-tags">
                        @foreach ($groomerAddons as $addon)
                        <span class="admin-bp-addon-tag {{ ! empty($addon['active']) ? 'is-active' : 'is-inactive' }}">
                            {{ $addon['label'] }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif
            </section>

            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Pet preferences">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>

                <div class="admin-bp-pref-groups">
                    @foreach ($groomerPetPreferenceGroups as $group)
                    <div class="admin-bp-pref-group">
                        <p class="admin-bp-pref-group-label">{{ $group['label'] }}</p>
                        <div class="admin-bp-pref-tags">
                            @foreach ($group['tags'] as $tag)
                            <span class="admin-bp-pref-tag {{ ! empty($tag['active']) ? 'is-active' : 'is-inactive' }}">
                                {{ $tag['label'] }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if (count($groomerPetRestrictions) > 0)
                    <div class="admin-bp-pref-group">
                        <p class="admin-bp-pref-group-label">Preferences &amp; restrictions</p>
                        <ul class="admin-bp-pref-rules">
                            @foreach ($groomerPetRestrictions as $rule)
                            <li class="admin-bp-pref-rule">
                                @if (($rule['type'] ?? 'allow') === 'deny')
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                    <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                                    <path d="M6.49971 3.5L3.5 6.8" stroke="#9C9A97" stroke-width="0.75"/>
                                    <path d="M3.50029 3.5L6.5 6.8" stroke="#9C9A97" stroke-width="0.75"/>
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                    <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                                    <path d="M7 3.5L4.27273 6.5L3 5.1" stroke="#9C9A97" stroke-width="0.75"/>
                                </svg>
                                @endif
                                <span>{{ $rule['text'] }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </section>

            @php $groomerAreaCount = count($groomerServiceAreas); @endphp
            @if ($groomerAreaCount > 0)
            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Location & Service Areas ({{ $groomerAreaCount }} {{ Str::plural('area', $groomerAreaCount) }})">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>

                <div class="admin-bp-coverage-list">
                    @foreach ($groomerServiceAreas as $area)
                    <article class="admin-bp-coverage-card">
                        <div class="admin-bp-coverage-info">
                            <p class="admin-bp-coverage-name">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="15" viewBox="0 0 11 15" fill="none" aria-hidden="true">
                                    <path d="M5.5 7.125C4.97904 7.125 4.47942 6.92746 4.11104 6.57583C3.74267 6.22419 3.53571 5.74728 3.53571 5.25C3.53571 4.75272 3.74267 4.27581 4.11104 3.92417C4.47942 3.57254 4.97904 3.375 5.5 3.375C6.02096 3.375 6.52058 3.57254 6.88896 3.92417C7.25733 4.27581 7.46429 4.75272 7.46429 5.25C7.46429 5.49623 7.41348 5.74005 7.31476 5.96753C7.21605 6.19502 7.07136 6.40172 6.88896 6.57583C6.70656 6.74994 6.49002 6.88805 6.2517 6.98227C6.01338 7.0765 5.75795 7.125 5.5 7.125ZM5.5 0C4.04131 0 2.64236 0.553123 1.61091 1.53769C0.579463 2.52226 0 3.85761 0 5.25C0 9.1875 5.5 15 5.5 15C5.5 15 11 9.1875 11 5.25C11 3.85761 10.4205 2.52226 9.38909 1.53769C8.35764 0.553123 6.95869 0 5.5 0Z" fill="#FFC97A" />
                                </svg>
                                {{ $area['name'] }}
                            </p>
                            <p class="admin-bp-coverage-address">{!! nl2br(e($area['address'])) !!}</p>
                            @if (! empty($area['radius']))
                            <p class="admin-bp-coverage-radius">{{ $area['radius'] }}</p>
                            @endif
                        </div>
                        <div class="admin-bp-coverage-map">
                            <img src="{{ $area['map'] ?? asset('images/admin/provider/map-southwark.png') }}" alt="">
                            <span class="admin-bp-coverage-radius-ring" aria-hidden="true"></span>
                            @if (! empty($area['maps_url']))
                            <a class="admin-bp-coverage-maps-link" href="{{ $area['maps_url'] }}" target="_blank" rel="noopener noreferrer">
                                Open in Maps
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                                    <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="currentColor" />
                                </svg>
                            </a>
                            @endif
                        </div>
                    </article>
                    @endforeach
                </div>

                @if (! empty($groomerCoverageMeta['location']) || ! empty($groomerCoverageMeta['accessibility']))
                <dl class="admin-co-details admin-bp-coverage-meta">
                    @if (! empty($groomerCoverageMeta['location']))
                    <div class="admin-co-details-row">
                        <dt>Location</dt>
                        <dd>{{ $groomerCoverageMeta['location'] }}</dd>
                    </div>
                    @endif
                    @if (! empty($groomerCoverageMeta['accessibility']))
                    <div class="admin-co-details-row">
                        <dt>Accessibility</dt>
                        <dd>{{ $groomerCoverageMeta['accessibility'] }}</dd>
                    </div>
                    @endif
                </dl>
                @endif
            </section>
            @endif
        </div>
        @endif

        @if ($showSpaceOfferings)
        <div class="admin-bp-profile-panels" x-show="!dual || viewAs === 'space'" @if ($isDual) x-cloak @endif>
            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Space listing & pricing">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>

                <div class="admin-bp-listing-block">
                    <p class="admin-bp-addons-label">Listing details</p>
                    <dl class="admin-co-details admin-bp-policy-details">
                        @foreach ($spaceListingDetails as $row)
                        <div class="admin-co-details-row">
                            <dt>{{ $row['label'] }}</dt>
                            <dd>{{ $row['value'] }}</dd>
                        </div>
                        @endforeach
                    </dl>
                </div>

                <div class="admin-bp-listing-block">
                    <p class="admin-bp-addons-label">Pricing tiers</p>
                    <ul class="admin-bp-svc-list">
                        @foreach ($spaceServices as $service)
                        @php $isInactive = ($service['status'] ?? 'active') === 'inactive'; @endphp
                        <li class="admin-bp-svc-row {{ $isInactive ? 'is-inactive' : '' }}">
                            <div class="admin-bp-svc-main">
                                <p class="admin-bp-svc-name">{{ $service['name'] }}</p>
                                @if (! empty($service['meta']))
                                <p class="admin-bp-svc-meta">{{ $service['meta'] }}</p>
                                @endif
                            </div>
                            <div class="admin-bp-svc-side">
                                <span class="admin-bp-svc-price">{{ $service['price'] ?? '' }}</span>
                                <span class="admin-po-status has-dot is-{{ $service['status'] ?? 'active' }}">
                                    {{ $service['status_label'] ?? ucfirst($service['status'] ?? 'active') }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <div class="admin-bp-listing-block">
                    <p class="admin-bp-addons-label">Add-on services</p>
                    <ul class="admin-bp-svc-list">
                        @foreach ($spaceListingAddons as $addon)
                        @php $isInactive = ($addon['status'] ?? 'active') === 'inactive'; @endphp
                        <li class="admin-bp-svc-row {{ $isInactive ? 'is-inactive' : '' }}">
                            <div class="admin-bp-svc-main">
                                <p class="admin-bp-svc-name">{{ $addon['name'] }}</p>
                                @if (! empty($addon['meta']))
                                <p class="admin-bp-svc-meta">{{ $addon['meta'] }}</p>
                                @endif
                            </div>
                            <div class="admin-bp-svc-side">
                                <span class="admin-bp-svc-price">{{ $addon['price'] ?? '' }}</span>
                                <span class="admin-po-status has-dot is-{{ $addon['status'] ?? 'active' }}">
                                    {{ $addon['status_label'] ?? ucfirst($addon['status'] ?? 'active') }}
                                </span>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
            </section>

            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Pet preferences">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>

                <div class="admin-bp-pref-groups">
                    @foreach ($spacePetPreferenceGroups as $group)
                    <div class="admin-bp-pref-group">
                        <p class="admin-bp-pref-group-label">{{ $group['label'] }}</p>
                        <div class="admin-bp-pref-tags">
                            @foreach ($group['tags'] as $tag)
                            <span class="admin-bp-pref-tag {{ ! empty($tag['active']) ? 'is-active' : 'is-inactive' }}">
                                {{ $tag['label'] }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endforeach

                    @if (count($spacePetRestrictions) > 0)
                    <div class="admin-bp-pref-group">
                        <p class="admin-bp-pref-group-label">Rules &amp; restrictions</p>
                        <ul class="admin-bp-pref-rules">
                            @foreach ($spacePetRestrictions as $rule)
                            <li class="admin-bp-pref-rule">
                                @if (($rule['type'] ?? 'allow') === 'deny')
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                    <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                                    <path d="M6.49971 3.5L3.5 6.8" stroke="#9C9A97" stroke-width="0.75"/>
                                    <path d="M3.50029 3.5L6.5 6.8" stroke="#9C9A97" stroke-width="0.75"/>
                                </svg>
                                @else
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                                    <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                                    <path d="M7 3.5L4.27273 6.5L3 5.1" stroke="#9C9A97" stroke-width="0.75"/>
                                </svg>
                                @endif
                                <span>{{ $rule['text'] }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </section>

            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Suitable for">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>
                <div class="admin-bp-pref-tags">
                    @foreach ($spaceSuitableFor as $tag)
                    <span class="admin-bp-feature-tag {{ ! empty($tag['active']) ? 'is-active' : 'is-inactive' }}">
                        {{ $tag['label'] }}
                    </span>
                    @endforeach
                </div>
            </section>

            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Amenities Included">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>
                <div class="admin-bp-pref-tags">
                    @foreach ($spaceAmenities as $tag)
                    <span class="admin-bp-feature-tag {{ ! empty($tag['active']) ? 'is-active' : 'is-inactive' }}">
                        {{ $tag['label'] }}
                    </span>
                    @endforeach
                </div>
            </section>

            @if ($primarySpaceArea)
            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Location">
                    <a class="admin-co-link-btn" href="{{ $primarySpaceArea['maps_url'] ?? '#' }}" target="_blank" rel="noopener noreferrer">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                            <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                        </svg>
                        View on Map
                    </a>
                </x-admin.customer.section-header>

                <article class="admin-bp-coverage-card">
                    <div class="admin-bp-coverage-info">
                        <p class="admin-bp-coverage-name">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="15" viewBox="0 0 11 15" fill="none" aria-hidden="true">
                                <path d="M5.5 7.125C4.97904 7.125 4.47942 6.92746 4.11104 6.57583C3.74267 6.22419 3.53571 5.74728 3.53571 5.25C3.53571 4.75272 3.74267 4.27581 4.11104 3.92417C4.47942 3.57254 4.97904 3.375 5.5 3.375C6.02096 3.375 6.52058 3.57254 6.88896 3.92417C7.25733 4.27581 7.46429 4.75272 7.46429 5.25C7.46429 5.49623 7.41348 5.74005 7.31476 5.96753C7.21605 6.19502 7.07136 6.40172 6.88896 6.57583C6.70656 6.74994 6.49002 6.88805 6.2517 6.98227C6.01338 7.0765 5.75795 7.125 5.5 7.125ZM5.5 0C4.04131 0 2.64236 0.553123 1.61091 1.53769C0.579463 2.52226 0 3.85761 0 5.25C0 9.1875 5.5 15 5.5 15C5.5 15 11 9.1875 11 5.25C11 3.85761 10.4205 2.52226 9.38909 1.53769C8.35764 0.553123 6.95869 0 5.5 0Z" fill="#FFC97A" />
                            </svg>
                            {{ $primarySpaceArea['name'] }}
                        </p>
                        <p class="admin-bp-coverage-address">{!! nl2br(e($primarySpaceArea['address'])) !!}</p>
                        @if (! empty($primarySpaceArea['availability']))
                        <p class="admin-bp-coverage-radius">{{ $primarySpaceArea['availability'] }}</p>
                        @endif
                    </div>
                    <div class="admin-bp-coverage-map">
                        <img src="{{ $primarySpaceArea['map'] ?? asset('images/admin/provider/map-southwark.png') }}" alt="">
                    </div>
                </article>

                @php
                $spaceMetaLocation = $spaceCoverageMeta['location'] ?? ($primarySpaceArea['location'] ?? null);
                $spaceMetaAccess = $spaceCoverageMeta['accessibility'] ?? ($primarySpaceArea['accessibility'] ?? null);
                @endphp
                @if ($spaceMetaLocation || $spaceMetaAccess)
                <dl class="admin-co-details admin-bp-coverage-meta">
                    @if ($spaceMetaLocation)
                    <div class="admin-co-details-row">
                        <dt>Location</dt>
                        <dd>{{ $spaceMetaLocation }}</dd>
                    </div>
                    @endif
                    @if ($spaceMetaAccess)
                    <div class="admin-co-details-row">
                        <dt>Accessibility</dt>
                        <dd>{{ $spaceMetaAccess }}</dd>
                    </div>
                    @endif
                </dl>
                @endif
            </section>
            @endif
        </div>
        @endif

        {{-- Admin Notes (same Alpine pattern) --}}
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
                <x-admin.customer.section-header title="Admin notes">
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
                    <label class="admin-co-note-compose-label" for="bp-services-note-{{ $profile['id'] }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                    <textarea
                        id="bp-services-note-{{ $profile['id'] }}"
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

        <section class="admin-card admin-co-panel admin-co-activity-panel">
            <x-admin.customer.section-header title="Recent Activity">
                <button type="button" class="admin-co-link-btn" @click="switchDetailTab('activity')">
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

    {{-- Policies sub-tab --}}
    <div class="admin-bp-profile-panels" x-show="profileSubTab === 'policies'" x-cloak>
        <section class="admin-card admin-co-panel">
            <x-admin.customer.section-header title="Cancellation Policy">
                <button type="button" class="admin-co-link-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                        <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Edit
                </button>
            </x-admin.customer.section-header>

            <dl class="admin-co-details admin-bp-policy-details">
                @foreach ($cancellationPolicies as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                </div>
                @endforeach
            </dl>

            <div class="admin-bp-policy-subhead">
                <h4 class="admin-co-section-title">Late arrival policy</h4>
            </div>
            <dl class="admin-co-details admin-bp-policy-details">
                @foreach ($lateArrivalPolicies as $row)
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                </div>
                @endforeach
            </dl>
        </section>

        <div class="admin-bp-policy-split">
            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Hygiene standards">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>
                @if ($showGroomerOfferings)
                <ul class="admin-bp-pref-rules" x-show="!dual || viewAs === 'groomer'" @if ($isDual) x-cloak @endif>
                    @foreach ($groomerHygiene as $item)
                    <li class="admin-bp-pref-rule">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                            <path d="M7 3.5L4.27273 6.5L3 5.1" stroke="#9C9A97" stroke-width="0.75"/>
                        </svg>
                        <span>{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
                @if ($showSpaceOfferings)
                <ul class="admin-bp-pref-rules" x-show="!dual || viewAs === 'space'" @if ($isDual) x-cloak @endif>
                    @foreach ($spaceHygiene as $item)
                    <li class="admin-bp-pref-rule">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                            <path d="M7 3.5L4.27273 6.5L3 5.1" stroke="#9C9A97" stroke-width="0.75"/>
                        </svg>
                        <span>{{ $item }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </section>

            <section class="admin-card admin-co-panel">
                <x-admin.customer.section-header title="Preferences & Restrictions">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Edit
                    </button>
                </x-admin.customer.section-header>
                @if ($showGroomerOfferings)
                <ul class="admin-bp-pref-rules" x-show="!dual || viewAs === 'groomer'" @if ($isDual) x-cloak @endif>
                    @foreach ($groomerPolicyRestrictions as $rule)
                    <li class="admin-bp-pref-rule">
                        @if (($rule['type'] ?? 'allow') === 'deny')
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                            <path d="M6.49971 3.5L3.5 6.8" stroke="#9C9A97" stroke-width="0.75"/>
                            <path d="M3.50029 3.5L6.5 6.8" stroke="#9C9A97" stroke-width="0.75"/>
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                            <path d="M7 3.5L4.27273 6.5L3 5.1" stroke="#9C9A97" stroke-width="0.75"/>
                        </svg>
                        @endif
                        <span>{{ $rule['text'] }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
                @if ($showSpaceOfferings)
                <ul class="admin-bp-pref-rules" x-show="!dual || viewAs === 'space'" @if ($isDual) x-cloak @endif>
                    @foreach ($spacePolicyRestrictions as $rule)
                    <li class="admin-bp-pref-rule">
                        @if (($rule['type'] ?? 'allow') === 'deny')
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                            <path d="M6.49971 3.5L3.5 6.8" stroke="#9C9A97" stroke-width="0.75"/>
                            <path d="M3.50029 3.5L6.5 6.8" stroke="#9C9A97" stroke-width="0.75"/>
                        </svg>
                        @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <circle cx="5" cy="5" r="4.625" stroke="#9C9A97" stroke-width="0.75"/>
                            <path d="M7 3.5L4.27273 6.5L3 5.1" stroke="#9C9A97" stroke-width="0.75"/>
                        </svg>
                        @endif
                        <span>{{ $rule['text'] }}</span>
                    </li>
                    @endforeach
                </ul>
                @endif
            </section>
        </div>

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
                <x-admin.customer.section-header title="Admin notes">
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
                    <label class="admin-co-note-compose-label" for="bp-policies-note-{{ $profile['id'] }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                    <textarea
                        id="bp-policies-note-{{ $profile['id'] }}"
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

        <section class="admin-card admin-co-panel admin-co-activity-panel">
            <x-admin.customer.section-header title="Recent Activity">
                <button type="button" class="admin-co-link-btn" @click="switchDetailTab('activity')">
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

    {{-- Reviews sub-tab --}}
    <div class="admin-bp-profile-panels" x-show="profileSubTab === 'reviews'" x-cloak>
        <section
            class="admin-card admin-co-panel"
            x-data="{
                page: 0,
                perPage: 2,
                isSpaceOnly: {{ $isSpace && ! $isDual ? 'true' : 'false' }},
                groomerItems: @js($groomerReviewItems),
                spaceItems: @js($spaceReviewItems),
                get items() {
                    const asSpace = (typeof dual !== 'undefined' && dual)
                        ? viewAs === 'space'
                        : this.isSpaceOnly;
                    return asSpace ? this.spaceItems : this.groomerItems;
                },
                get pageCount() {
                    return Math.max(1, Math.ceil(this.items.length / this.perPage));
                },
                get pages() {
                    return Array.from({ length: this.pageCount }, (_, i) => i);
                },
                get visible() {
                    const start = this.page * this.perPage;
                    return this.items.slice(start, start + this.perPage);
                },
                prev() {
                    if (this.page > 0) this.page -= 1;
                },
                next() {
                    if (this.page < this.pageCount - 1) this.page += 1;
                },
            }"
            x-effect="if (typeof viewAs !== 'undefined') { page = 0 }">
            <div class="admin-co-section-head">
                <h3 class="admin-co-section-title admin-bp-reviews-title">
                    Reviews
                    @if ($showGroomerOfferings)
                    <span class="admin-bp-reviews-summary" x-show="!dual || viewAs === 'groomer'" @if ($isDual) x-cloak @endif>
                        — {{ $groomerReviewsSummary['avg'] ?? '—' }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" fill="#3B3731" />
                        </svg>
                        <span class="admin-bp-reviews-count">· {{ $groomerReviewsSummary['count'] ?? 0 }} verified reviews</span>
                    </span>
                    @endif
                    @if ($showSpaceOfferings)
                    <span class="admin-bp-reviews-summary" x-show="!dual || viewAs === 'space'" @if ($isDual) x-cloak @endif>
                        — {{ $spaceReviewsSummary['avg'] ?? '—' }}
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" fill="#3B3731" />
                        </svg>
                        <span class="admin-bp-reviews-count">· {{ $spaceReviewsSummary['count'] ?? 0 }} verified reviews</span>
                    </span>
                    @endif
                </h3>
                <div class="admin-co-section-action-wrap">
                    <button type="button" class="admin-co-link-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                            <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                        </svg>
                        View All
                    </button>
                </div>
            </div>

            <div class="admin-bp-reviews-feed">
                <template x-for="(review, index) in visible" :key="page + '-' + index">
                    <article class="admin-bp-review-card">
                        <div class="admin-bp-review-card-head">
                            <p class="admin-bp-review-identity">
                                <span x-text="review.author"></span>
                                <span class="admin-bp-review-sep" aria-hidden="true">·</span>
                                <span x-text="review.user_id"></span>
                            </p>
                            <div class="admin-bp-review-stars" aria-hidden="true">
                                <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 10 10" fill="none">
                                        <path d="M5 0.5L6.12257 3.52786L9.5 3.76393L6.95 5.97214L7.75528 9.5L5 7.7L2.24472 9.5L3.05 5.97214L0.5 3.76393L3.87743 3.52786L5 0.5Z" :fill="star <= review.stars ? '#FFC97A' : '#E8E6E3'" />
                                    </svg>
                                </template>
                            </div>
                            <time class="admin-bp-review-date" x-text="review.time_ago"></time>
                        </div>
                        <p class="admin-bp-review-text" x-text="review.text"></p>
                        <p class="admin-bp-review-service">
                            <span x-text="review.service"></span>
                            <span class="admin-bp-review-sep" aria-hidden="true">·</span>
                            <span x-text="review.booking_id"></span>
                        </p>
                        <div class="admin-bp-review-reply">
                            <p class="admin-bp-review-reply-label">Provider reply</p>
                            <p class="admin-bp-review-reply-text" x-text="review.reply || 'No reply from provider'" :class="{ 'is-empty': !review.reply }"></p>
                        </div>
                    </article>
                </template>
            </div>

            <div class="admin-bp-reviews-nav" x-show="pageCount > 1" x-cloak>
                <div class="admin-bp-reviews-dots" role="tablist" aria-label="Review pages">
                    <template x-for="dot in pages" :key="dot">
                        <button
                            type="button"
                            class="admin-bp-reviews-dot"
                            :class="{ 'is-active': page === dot }"
                            :aria-label="'Page ' + (dot + 1)"
                            @click="page = dot"></button>
                    </template>
                </div>
                <div class="admin-co-pets-arrows">
                    <button
                        type="button"
                        class="admin-co-pets-arrow"
                        :class="{ 'is-active': page > 0 }"
                        :aria-disabled="page === 0"
                        aria-label="Previous reviews"
                        @click="prev()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                            <circle cx="16" cy="16" r="16" fill="currentColor" />
                            <path d="M18 21L12.9657 15.9657L17.9155 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button
                        type="button"
                        class="admin-co-pets-arrow"
                        :class="{ 'is-active': page < pageCount - 1 }"
                        :aria-disabled="page >= pageCount - 1"
                        aria-label="Next reviews"
                        @click="next()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none" aria-hidden="true">
                            <circle cx="16" cy="16" r="16" fill="currentColor" />
                            <path d="M14 21L19.0343 15.9657L14.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>

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
                <x-admin.customer.section-header title="Admin notes">
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
                    <label class="admin-co-note-compose-label" for="bp-reviews-note-{{ $profile['id'] }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                    <textarea
                        id="bp-reviews-note-{{ $profile['id'] }}"
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

        <section class="admin-card admin-co-panel admin-co-activity-panel">
            <x-admin.customer.section-header title="Recent Activity">
                <button type="button" class="admin-co-link-btn" @click="switchDetailTab('activity')">
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

    {{-- Edit Business details modal --}}
    <template x-teleport="body">
        <div
            class="admin-co-modal-backdrop"
            :class="{ 'is-open': editBusinessOpen }"
            @click.self="editBusinessOpen = false"
            @keydown.escape.window="editBusinessOpen && (editBusinessOpen = false)">
            <div class="admin-co-modal admin-co-verify-email-modal admin-bp-edit-business-modal" role="dialog" aria-modal="true" aria-labelledby="bp-edit-business-title-{{ $profile['id'] }}" @click.stop>
                <div class="admin-co-modal-head admin-co-blocked-modal-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <span class="admin-co-verify-email-icon is-edit-business" aria-hidden="true">
                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                    <path d="M5.55147 1.44927L6.96324 2.93335M4.61029 8.375H8.375M0.845588 6.39622L0.375 8.375L2.25735 7.8803L7.70959 2.14877C7.88603 1.96323 7.98515 1.71162 7.98515 1.44927C7.98515 1.18692 7.88603 0.935306 7.70959 0.749768L7.62865 0.66468C7.45215 0.479198 7.2128 0.375 6.96324 0.375C6.71367 0.375 6.47432 0.479198 6.29782 0.66468L0.845588 6.39622Z" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </span>
                            <div>
                                <h3 class="admin-co-modal-title" id="bp-edit-business-title-{{ $profile['id'] }}">Edit Business details</h3>
                                <p class="admin-co-modal-sub">{{ $modalSubtitleName }} · {{ $providerIdLabel }}</p>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="admin-co-modal-close" @click="editBusinessOpen = false" aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8"></circle>
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"></path>
                        </svg>
                    </button>
                </div>

                <div class="admin-co-blocked-modal-body admin-co-verify-email-body admin-bp-edit-business-body">
                    <div class="admin-bp-edit-business-fields">
                        <div class="admin-co-edit-field is-full">
                            <label for="bp-edit-email-{{ $profile['id'] }}">
                                Email address <span class="admin-co-suspend-required">*</span>
                            </label>
                            <input id="bp-edit-email-{{ $profile['id'] }}" type="email" x-model="editEmail" autocomplete="email">
                        </div>

                        <div class="admin-co-edit-field is-full">
                            <label for="bp-edit-phone-{{ $profile['id'] }}">
                                Phone number <span class="admin-co-suspend-required">*</span>
                            </label>
                            <input id="bp-edit-phone-{{ $profile['id'] }}" type="tel" x-model="editPhone" autocomplete="tel">
                        </div>

                        <div class="admin-co-edit-field is-full">
                            <label for="bp-edit-address1-{{ $profile['id'] }}">
                                Address line 1 <span class="admin-co-suspend-required">*</span>
                            </label>
                            <input id="bp-edit-address1-{{ $profile['id'] }}" type="text" x-model="editAddress1" autocomplete="address-line1">
                        </div>

                        <div class="admin-co-edit-field is-full">
                            <label for="bp-edit-address2-{{ $profile['id'] }}">Address line 2</label>
                            <input id="bp-edit-address2-{{ $profile['id'] }}" type="text" x-model="editAddress2" autocomplete="address-line2">
                        </div>

                        <div class="admin-bp-edit-business-row">
                            <div class="admin-co-edit-field">
                                <label for="bp-edit-city-{{ $profile['id'] }}">
                                    City <span class="admin-co-suspend-required">*</span>
                                </label>
                                <input id="bp-edit-city-{{ $profile['id'] }}" type="text" x-model="editCity" autocomplete="address-level2">
                            </div>
                            <div class="admin-co-edit-field">
                                <label for="bp-edit-postcode-{{ $profile['id'] }}">
                                    Postcode <span class="admin-co-suspend-required">*</span>
                                </label>
                                <input id="bp-edit-postcode-{{ $profile['id'] }}" type="text" x-model="editPostcode" autocomplete="postal-code">
                            </div>
                        </div>

                        <div class="admin-co-edit-field is-full">
                            <label for="bp-edit-country-{{ $profile['id'] }}">Country</label>
                            <input id="bp-edit-country-{{ $profile['id'] }}" type="text" x-model="editCountry" autocomplete="country-name">
                        </div>
                    </div>

                    @if (count($editReadonly) > 0)
                    <div class="admin-bp-edit-readonly">
                        <p class="admin-bp-edit-readonly-label">Read-only fields — cannot be edited</p>
                        <dl class="admin-co-details admin-bp-edit-readonly-list">
                            @foreach ($editReadonly as $row)
                            <div class="admin-co-details-row">
                                <dt>{{ $row['label'] }}</dt>
                                <dd>{{ $row['value'] }}</dd>
                            </div>
                            @endforeach
                        </dl>
                    </div>
                    @endif

                    <div class="admin-co-verify-email-happens">
                        <h4 class="admin-co-verify-email-happens-title">What happens</h4>
                        <ul class="admin-co-verify-email-happens-list">
                            <li>Provider is notified by email that their details were updated by admin</li>
                        </ul>
                    </div>
                </div>

                <div class="admin-co-blocked-modal-foot is-confirm">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="editBusinessOpen = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-send-email"
                        @click="editEmail.trim() && editPhone.trim() && editAddress1.trim() && editCity.trim() && editPostcode.trim() && (editBusinessOpen = false)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M7.75 1.25V7.25M7.75 7.25L5.5 5M7.75 7.25L10 5M2.5 8.75V10.75C2.5 11.8546 3.39543 12.75 4.5 12.75H11C12.1046 12.75 13 11.8546 13 10.75V8.75" stroke="currentColor" stroke-width="1.3" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Save changes
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>