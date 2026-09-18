@props(['profile'])

@php
$details = $profile['details'];
$editFields = $profile['edit_fields'] ?? [];
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
    @php
    $ef = $editFields;
    $savedAddress = collect([
    $ef['address'] ?? '',
    $ef['city'] ?? '',
    $ef['postcode'] ?? '',
    $ef['country'] ?? '',
    ])->filter()->implode(', ');
    @endphp
    <section
        class="admin-card admin-co-panel"
        :class="{ 'is-editing': editing }"
        x-data="{
            editing: false,
            full_name: @js($ef['full_name'] ?? ''),
            email: @js($ef['email'] ?? ''),
            phone: @js($ef['phone'] ?? ''),
            dob: @js($ef['dob'] ?? ''),
            address: @js($ef['address'] ?? ''),
            city: @js($ef['city'] ?? ''),
            postcode: @js($ef['postcode'] ?? ''),
            country: @js($ef['country'] ?? ''),
            saved_full_name: @js($ef['full_name'] ?? ''),
            saved_email: @js($ef['email'] ?? ''),
            saved_phone: @js($ef['phone'] ?? ''),
            saved_dob: @js($ef['dob'] ?? ''),
            saved_address: @js($ef['address'] ?? ''),
            saved_city: @js($ef['city'] ?? ''),
            saved_postcode: @js($ef['postcode'] ?? ''),
            saved_country: @js($ef['country'] ?? ''),
            saved_address_line: @js($savedAddress),
        }">
        <div x-show="!editing" x-cloak>
            <x-admin.customer.section-header title="Personal Details">
                <button
                    type="button"
                    class="admin-co-link-btn"
                    @click="
                        full_name = saved_full_name;
                        email = saved_email;
                        phone = saved_phone;
                        dob = saved_dob;
                        address = saved_address;
                        city = saved_city;
                        postcode = saved_postcode;
                        country = saved_country;
                        editing = true;
                    ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                        <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Edit
                </button>
            </x-admin.customer.section-header>
            <dl class="admin-co-details">
                <div class="admin-co-details-row">
                    <dt>Full Name</dt>
                    <dd x-text="saved_full_name"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Email</dt>
                    <dd x-text="saved_email"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Phone</dt>
                    <dd x-text="saved_phone"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Date of Birth</dt>
                    <dd x-text="saved_dob"></dd>
                </div>
                <div class="admin-co-details-row">
                    <dt>Address</dt>
                    <dd x-text="saved_address_line"></dd>
                </div>
                @foreach ($details as $row)
                @if (! in_array($row['label'], ['Full Name', 'Email', 'Phone', 'Date of Birth', 'Address'], true))
                <div class="admin-co-details-row">
                    <dt>{{ $row['label'] }}</dt>
                    <dd>{{ $row['value'] }}</dd>
                </div>
                @endif
                @endforeach
            </dl>
        </div>

        <div x-show="editing" x-cloak>
            <div class="admin-co-section-head">
                <h3 class="admin-co-section-title admin-co-edit-title">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                        <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Personal Details
                </h3>
                <div class="admin-co-section-action-wrap">
                    <button type="button" class="admin-co-form-btn is-cancel" @click="editing = false">Cancel</button>
                    <button
                        type="button"
                        class="admin-co-form-btn is-save"
                        @click="
                            saved_full_name = full_name;
                            saved_email = email;
                            saved_phone = phone;
                            saved_dob = dob;
                            saved_address = address;
                            saved_city = city;
                            saved_postcode = postcode;
                            saved_country = country;
                            saved_address_line = address + ', ' + city + ', ' + postcode + ', ' + country;
                            editing = false;
                        ">Save changes</button>
                </div>
            </div>

            <form class="admin-co-edit-form" @submit.prevent>
                <div class="admin-co-edit-field">
                    <label for="co-full-name-{{ $profile['id'] }}">Full Name</label>
                    <input id="co-full-name-{{ $profile['id'] }}" type="text" x-model="full_name">
                </div>
                <div class="admin-co-edit-field">
                    <div class="admin-co-edit-label-row">
                        <label for="co-email-{{ $profile['id'] }}">Email</label>
                        <span class="admin-co-edit-hint">Changing this will require email re-verification</span>
                    </div>
                    <input id="co-email-{{ $profile['id'] }}" type="email" x-model="email">
                </div>
                <div class="admin-co-edit-field">
                    <label for="co-phone-{{ $profile['id'] }}">Phone</label>
                    <input id="co-phone-{{ $profile['id'] }}" type="text" x-model="phone">
                </div>
                <div class="admin-co-edit-field">
                    <label for="co-dob-{{ $profile['id'] }}">Date of Birth</label>
                    <input id="co-dob-{{ $profile['id'] }}" type="text" x-model="dob">
                </div>
                <div class="admin-co-edit-field">
                    <label for="co-address-{{ $profile['id'] }}">Address</label>
                    <input id="co-address-{{ $profile['id'] }}" type="text" x-model="address">
                </div>
                <div class="admin-co-edit-field">
                    <label for="co-city-{{ $profile['id'] }}">City</label>
                    <input id="co-city-{{ $profile['id'] }}" type="text" x-model="city">
                </div>
                <div class="admin-co-edit-field">
                    <label for="co-postcode-{{ $profile['id'] }}">Post Code</label>
                    <input id="co-postcode-{{ $profile['id'] }}" type="text" x-model="postcode">
                </div>
                <div class="admin-co-edit-field">
                    <label for="co-country-{{ $profile['id'] }}">Country</label>
                    <input id="co-country-{{ $profile['id'] }}" type="text" x-model="country">
                </div>
            </form>
        </div>
    </section>

    {{-- Pets --}}
    <section class="admin-card admin-co-panel">
        <x-admin.customer.section-header :title="'Pets (' . count($pets) . ' Total)'">
            <button type="button" class="admin-co-link-btn" @click="detailTab = 'pets'">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                    <path d="M10.0284 0.00548691L10.0229 6.35299L9.19447 6.33653C9.02623 6.33653 8.90736 6.28715 8.83787 6.1884C8.76838 6.08234 8.7318 5.95067 8.72814 5.7934L8.73912 3.11615C8.73912 2.92596 8.7446 2.74857 8.75558 2.58399C8.76289 2.41575 8.77569 2.2603 8.79398 2.11766C8.60379 2.35906 8.39897 2.60776 8.17953 2.86378C7.96008 3.11249 7.72966 3.35754 7.48827 3.59893L1.29164 9.79556C0.996218 10.091 0.517251 10.091 0.221833 9.79556C-0.073585 9.50015 -0.0735852 9.02118 0.221833 8.72576L6.41847 2.52913C6.65986 2.28774 6.90856 2.05732 7.16459 1.83787C7.41695 1.61476 7.66566 1.40995 7.9107 1.22342C7.76441 1.24536 7.60896 1.26182 7.44438 1.27279C7.27614 1.28011 7.09692 1.28377 6.90673 1.28376L4.20754 1.29474C4.05393 1.29474 3.92409 1.25999 3.81802 1.1905C3.71561 1.11735 3.66441 0.996656 3.66441 0.828412L3.64795 3.3782e-07L10.0284 0.00548691Z" fill="#3B3731" />
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

        <section
            class="admin-card admin-co-panel"
            :class="{ 'is-editing': editingVerify }"
            x-data="{
                editingVerify: false,
                openEmail: false,
                openPhone: false,
                openTwofa: false,
                openLogin: false,
                email: 'verified',
                phone: 'verified',
                twofa: 'not_verified',
                login: 'Google',
                saved_email: 'verified',
                saved_phone: 'verified',
                saved_twofa: 'not_verified',
                saved_login: 'Google',
                profile_label: '70% Completion',
            }">
            <div x-show="!editingVerify" x-cloak>
                <x-admin.customer.section-header title="Verification Status">
                    <button
                        type="button"
                        class="admin-co-link-btn"
                        @click="
                            email = saved_email;
                            phone = saved_phone;
                            twofa = saved_twofa;
                            login = saved_login;
                            openEmail = false;
                            openPhone = false;
                            openTwofa = false;
                            openLogin = false;
                            editingVerify = true;
                        ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true">
                            <path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        Override Status
                    </button>
                </x-admin.customer.section-header>
                <ul class="admin-co-status-list">
                    <li class="admin-co-status-row">
                        <span>Email Verified</span>
                        <span class="admin-co-verify-ok" x-show="saved_email === 'verified'" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            Verified
                        </span>
                        <span class="admin-co-consent is-off" x-show="saved_email === 'not_verified'" x-cloak>Not verified</span>
                        <span class="admin-co-verify-pending" x-show="saved_email === 'pending'" x-cloak>Pending</span>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Phone Verified</span>
                        <span class="admin-co-verify-ok" x-show="saved_phone === 'verified'" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            Verified
                        </span>
                        <span class="admin-co-consent is-off" x-show="saved_phone === 'not_verified'" x-cloak>Not verified</span>
                        <span class="admin-co-verify-pending" x-show="saved_phone === 'pending'" x-cloak>Pending</span>
                    </li>
                    <li class="admin-co-status-row">
                        <span>2FA Enabled</span>
                        <span class="admin-co-verify-ok" x-show="saved_twofa === 'verified'" x-cloak>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                                <path d="M4.19632 9L0 4.73389L1.04908 3.66736L4.19632 6.86694L10.9509 0L12 1.06653L4.19632 9Z" fill="#A7C569" />
                            </svg>
                            Verified
                        </span>
                        <span class="admin-co-consent is-off" x-show="saved_twofa === 'not_verified'" x-cloak>Not Enabled</span>
                        <span class="admin-co-verify-pending" x-show="saved_twofa === 'pending'" x-cloak>Pending</span>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Connected Login</span>
                        <span class="admin-co-verify-text" x-text="saved_login"></span>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Profile Complete</span>
                        <span class="admin-co-verify-progress" x-text="profile_label"></span>
                    </li>
                </ul>
            </div>

            <div x-show="editingVerify" x-cloak>
                <div class="admin-co-section-head">
                    <h3 class="admin-co-section-title">Verification Status</h3>
                    <div class="admin-co-section-action-wrap">
                        <button type="button" class="admin-co-form-btn is-cancel" @click="editingVerify = false; openEmail = false; openPhone = false; openTwofa = false; openLogin = false;">Cancel</button>
                        <button
                            type="button"
                            class="admin-co-form-btn is-save"
                            @click="
                                saved_email = email;
                                saved_phone = phone;
                                saved_twofa = twofa;
                                saved_login = login;
                                openEmail = false;
                                openPhone = false;
                                openTwofa = false;
                                openLogin = false;
                                editingVerify = false;
                            ">Save changes</button>
                    </div>
                </div>

                <ul class="admin-co-status-list admin-co-verify-edit-list">
                    <li class="admin-co-status-row admin-co-verify-edit-row" :class="{ 'is-open': openEmail }">
                        <span>Email Verified</span>
                        <div class="admin-co-dd" @click.outside="openEmail = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openEmail = !openEmail; openPhone = false; openTwofa = false; openLogin = false;">
                                <span class="admin-co-dd-value" x-show="email === 'not_verified'" x-cloak>
                                    <span class="admin-co-dd-x" aria-hidden="true">✕</span> Not verified
                                </span>
                                <span class="admin-co-dd-value" x-show="email === 'verified'" x-cloak>
                                    <span class="admin-co-dd-check" aria-hidden="true">✔</span> Verified
                                </span>
                                <span class="admin-co-dd-value" x-show="email === 'pending'" x-cloak>Pending</span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openEmail" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': email === 'not_verified' }" @click="email = 'not_verified'; openEmail = false;">
                                    <span class="admin-co-dd-x" aria-hidden="true">✕</span> Not verified
                                </button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': email === 'verified' }" @click="email = 'verified'; openEmail = false;">
                                    <span class="admin-co-dd-check" aria-hidden="true">✔</span> Verified
                                </button>
                                <button type="button" class="admin-co-dd-option is-pending" :class="{ 'is-active': email === 'pending' }" @click="email = 'pending'; openEmail = false;">
                                    Pending
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="admin-co-status-row admin-co-verify-edit-row" :class="{ 'is-open': openPhone }">
                        <span>Phone Verified</span>
                        <div class="admin-co-dd" @click.outside="openPhone = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openPhone = !openPhone; openEmail = false; openTwofa = false; openLogin = false;">
                                <span class="admin-co-dd-value" x-show="phone === 'not_verified'" x-cloak>
                                    <span class="admin-co-dd-x" aria-hidden="true">✕</span> Not verified
                                </span>
                                <span class="admin-co-dd-value" x-show="phone === 'verified'" x-cloak>
                                    <span class="admin-co-dd-check" aria-hidden="true">✔</span> Verified
                                </span>
                                <span class="admin-co-dd-value" x-show="phone === 'pending'" x-cloak>Pending</span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openPhone" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': phone === 'not_verified' }" @click="phone = 'not_verified'; openPhone = false;">
                                    <span class="admin-co-dd-x" aria-hidden="true">✕</span> Not verified
                                </button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': phone === 'verified' }" @click="phone = 'verified'; openPhone = false;">
                                    <span class="admin-co-dd-check" aria-hidden="true">✔</span> Verified
                                </button>
                                <button type="button" class="admin-co-dd-option is-pending" :class="{ 'is-active': phone === 'pending' }" @click="phone = 'pending'; openPhone = false;">
                                    Pending
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="admin-co-status-row admin-co-verify-edit-row" :class="{ 'is-open': openTwofa }">
                        <span>2FA Enabled</span>
                        <div class="admin-co-dd" @click.outside="openTwofa = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openTwofa = !openTwofa; openEmail = false; openPhone = false; openLogin = false;">
                                <span class="admin-co-dd-value" x-show="twofa === 'not_verified'" x-cloak>
                                    <span class="admin-co-dd-x" aria-hidden="true">✕</span> Not verified
                                </span>
                                <span class="admin-co-dd-value" x-show="twofa === 'verified'" x-cloak>
                                    <span class="admin-co-dd-check" aria-hidden="true">✔</span> Verified
                                </span>
                                <span class="admin-co-dd-value" x-show="twofa === 'pending'" x-cloak>Pending</span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openTwofa" x-cloak>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': twofa === 'not_verified' }" @click="twofa = 'not_verified'; openTwofa = false;">
                                    <span class="admin-co-dd-x" aria-hidden="true">✕</span> Not verified
                                </button>
                                <button type="button" class="admin-co-dd-option" :class="{ 'is-active': twofa === 'verified' }" @click="twofa = 'verified'; openTwofa = false;">
                                    <span class="admin-co-dd-check" aria-hidden="true">✔</span> Verified
                                </button>
                                <button type="button" class="admin-co-dd-option is-pending" :class="{ 'is-active': twofa === 'pending' }" @click="twofa = 'pending'; openTwofa = false;">
                                    Pending
                                </button>
                            </div>
                        </div>
                    </li>
                    <li class="admin-co-status-row admin-co-verify-edit-row" :class="{ 'is-open': openLogin }">
                        <span>Connected Login</span>
                        <div class="admin-co-dd" @click.outside="openLogin = false">
                            <button
                                type="button"
                                class="admin-co-dd-trigger"
                                @click="openLogin = !openLogin; openEmail = false; openPhone = false; openTwofa = false;">
                                <span class="admin-co-dd-value" x-text="login"></span>
                                <svg class="admin-co-dd-chevron" xmlns="http://www.w3.org/2000/svg" width="12" height="8" viewBox="0 0 12 8" fill="none" aria-hidden="true">
                                    <path d="M1 1.5L6 6.5L11 1.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="admin-co-dd-menu" x-show="openLogin" x-cloak>
                                <button type="button" class="admin-co-dd-option is-pending" :class="{ 'is-active': login === 'Google' }" @click="login = 'Google'; openLogin = false;">Google</button>
                                <button type="button" class="admin-co-dd-option is-pending" :class="{ 'is-active': login === 'Apple' }" @click="login = 'Apple'; openLogin = false;">Apple</button>
                                <button type="button" class="admin-co-dd-option is-pending" :class="{ 'is-active': login === 'Facebook' }" @click="login = 'Facebook'; openLogin = false;">Facebook</button>
                                <button type="button" class="admin-co-dd-option is-pending" :class="{ 'is-active': login === 'None' }" @click="login = 'None'; openLogin = false;">None</button>
                            </div>
                        </div>
                    </li>
                    <li class="admin-co-status-row">
                        <span>Profile Complete</span>
                        <span class="admin-co-verify-progress" x-text="profile_label"></span>
                    </li>
                </ul>
            </div>
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
        <section
            class="admin-card admin-co-panel"
            :class="{ 'is-editing': revoking }"
            x-data="{
                revoking: false,
                revokeConfirm: false,
                sessionCount: {{ count($sessions) }},
                @foreach ($sessions as $i => $session)
                pick{{ $i }}: false,
                show{{ $i }}: true,
                @endforeach
            }">
            <div x-show="!revoking">
                <x-admin.customer.section-header title="Device & Session info">
                    <button
                        type="button"
                        class="admin-co-link-btn"
                        @click="
                            revoking = true;
                            revokeConfirm = false;
                            @foreach ($sessions as $i => $session)
                            pick{{ $i }} = false;
                            @endforeach
                        ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                            <path d="M6 2H3.5C2.67157 2 2 2.67157 2 3.5V12.5C2 13.3284 2.67157 14 3.5 14H6" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" />
                            <path d="M10.5 11L14 8L10.5 5" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M14 8H6" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" />
                        </svg>
                        Revoke Access
                    </button>
                </x-admin.customer.section-header>
            </div>

            <div x-show="revoking" x-cloak>
                <div class="admin-co-section-head admin-co-revoke-head">
                    <div>
                        <div class="admin-co-modal-title-row">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <path d="M6 2H3.5C2.67157 2 2 2.67157 2 3.5V12.5C2 13.3284 2.67157 14 3.5 14H6" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" />
                                <path d="M10.5 11L14 8L10.5 5" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M14 8H6" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" />
                            </svg>
                            <div>
                                <h3 class="admin-co-section-title">Revoke Access</h3>
                                <p class="admin-co-modal-sub">{{ $profile['name'] }} · {{ $profile['id'] }}</p>
                            </div>
                        </div>
                    </div>
                    <button
                        type="button"
                        class="admin-co-modal-close"
                        aria-label="Close"
                        @click="
                            revoking = false;
                            revokeConfirm = false;
                            @foreach ($sessions as $i => $session)
                            pick{{ $i }} = false;
                            @endforeach
                        ">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                            <path d="M1 1L9 9M9 1L1 9" stroke="#3B3731" stroke-width="1.4" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>
                <p class="admin-co-modal-hint" x-show="!revokeConfirm" x-cloak>Select session to revoke.</p>
            </div>

            <ul class="admin-co-session-list" :class="{ 'is-revoking': revoking }" x-show="!revoking">
                @foreach ($sessions as $i => $session)
                <li class="admin-co-session-item" x-show="show{{ $i }}">
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

            <ul class="admin-co-revoke-list" :class="{ 'is-confirm': revokeConfirm }" x-show="revoking" x-cloak>
                @foreach ($sessions as $i => $session)
                <li x-show="show{{ $i }} && (!revokeConfirm || pick{{ $i }})" x-cloak>
                    <button
                        type="button"
                        class="admin-co-revoke-item"
                        :class="{ 'is-selected': pick{{ $i }}, 'is-confirm': revokeConfirm }"
                        @click="revokeConfirm || (pick{{ $i }} = !pick{{ $i }})">
                        <div class="admin-co-session-icon{{ !empty($session['warning']) ? ' has-warning' : '' }}" aria-hidden="true">
                            @if (($session['device'] ?? 'laptop') === 'phone')
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="20" viewBox="0 0 13 20" fill="none">
                                <rect x="11.75" y="0.75" width="18.5" height="11" rx="1.25" transform="rotate(90 11.75 0.75)" stroke="currentColor" stroke-width="1.5" />
                                <path d="M4.72705 16.6362L7.72705 16.6362" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                            </svg>
                            @elseif (($session['device'] ?? '') === 'desktop')
                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="16" viewBox="0 0 21 16" fill="none">
                                <rect x="0.75" y="0.75" width="18.8636" height="11.2273" rx="1.25" stroke="currentColor" stroke-width="1.5" />
                                <path d="M7 15L13 15" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
                                <path d="M9.25 15C9.25 15.4142 9.58579 15.75 10 15.75C10.4142 15.75 10.75 15.4142 10.75 15L10 15L9.25 15ZM10 15L10.75 15L10.75 12.5L10 12.5L9.25 12.5L9.25 15L10 15Z" fill="currentColor" />
                            </svg>
                            @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="12" viewBox="0 0 16 12" fill="none">
                                <rect x="0.75" y="0.75" width="14.5" height="9" rx="1.5" stroke="currentColor" stroke-width="1.2" />
                                <path d="M5 11.25H11" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" />
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
                        <div class="admin-co-revoke-item-body">
                            <p class="admin-co-revoke-item-title">{{ $session['title'] }}</p>
                            <p class="admin-co-revoke-item-meta">{{ $session['location'] }} · {{ $session['ip'] }}</p>
                            <p class="admin-co-revoke-item-meta">{{ $session['time'] }}</p>
                        </div>
                    </button>
                </li>
                @endforeach
            </ul>

            <div class="admin-co-revoke-note" x-show="revoking && revokeConfirm" x-cloak>
                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                    <path d="M4.875 9.375C7.36028 9.375 9.375 7.36028 9.375 4.875C9.375 2.38972 7.36028 0.375 4.875 0.375C2.38972 0.375 0.375 2.38972 0.375 4.875C0.375 7.36028 2.38972 9.375 4.875 9.375Z" stroke="#A6BBC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M4.875 6.67495V4.87495M4.875 3.07495H4.8795" stroke="#A6BBC9" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <p>Revoking the selected sessions will immediately sign user out their device. The customer will need to log in again from this device. They will be notified by email that a session was revoked.</p>
            </div>

            <div class="admin-co-revoke-foot" x-show="revoking" x-cloak>
                <button
                    type="button"
                    class="admin-co-form-btn is-cancel"
                    @click="
                        revokeConfirm
                            ? revokeConfirm = false
                            : (
                                revoking = false,
                                @foreach ($sessions as $i => $session)
                                pick{{ $i }} = false{{ !$loop->last ? ',' : '' }}
                                @endforeach
                            )
                    ">Cancel</button>

                <button
                    type="button"
                    class="admin-co-form-btn is-revoke"
                    x-show="!revokeConfirm"
                    x-cloak
                    @click="(
                        false
                        @foreach ($sessions as $i => $session)
                        || pick{{ $i }}
                        @endforeach
                    ) && (revokeConfirm = true)">Revoke Selected</button>

                <button
                    type="button"
                    class="admin-co-form-btn is-revoke"
                    x-show="revokeConfirm"
                    x-cloak
                    @click="
                        @foreach ($sessions as $i => $session)
                        pick{{ $i }} && (show{{ $i }} = false, pick{{ $i }} = false, sessionCount = sessionCount - 1);
                        @endforeach
                        revokeConfirm = false;
                        revoking = false;
                    ">Revoke Sessions</button>
            </div>

            <p class="admin-co-panel-footnote" x-show="!revoking">
                <span x-text="sessionCount"></span> active sessions · 2FA not enabled
            </p>
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
    <section
        class="admin-card admin-co-panel"
        :class="{ 'is-editing': editingNotes }"
        x-data="{
            editingNotes: false,
            newNote: '',
            editIndex: -1,
            notes: @js($notes),
        }"
    >
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
                <label class="admin-co-note-compose-label" for="co-new-note-{{ $profile['id'] }}" x-text="editIndex >= 0 ? 'Edit note' : 'Add a new note'"></label>
                <textarea
                    id="co-new-note-{{ $profile['id'] }}"
                    class="admin-co-note-textarea"
                    rows="4"
                    placeholder="Write an internal note about this customer - only visible to the admin team members."
                    x-model="newNote"
                    x-ref="noteInput"
                ></textarea>
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
                        x-text="editIndex >= 0 ? 'Update note' : 'Add note'"
                    ></button>
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
                                "
                            >
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