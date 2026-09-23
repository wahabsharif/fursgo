{{--

===== usage example =====

<x-ui.filter-section
    groom-modal-id="groomModal"
    space-modal-id="spaceModal"
    active-tab="groomer"
/>

--}}

@props([
'groomModalId' => 'groomModal',
'spaceModalId' => 'spaceModal',

'searchValue' => 'London, NW3 1AA',

'activeTab' => 'groomer', // groomer | space
'groomerTabLabel' => 'Find Groomer',
'spaceTabLabel' => 'Find Space',

'groomerSearchLabel' => 'Search Groomer',
'spaceSearchLabel' => 'Search Space',

'groomerServiceLabel' => 'Service Type',
'spaceServiceLabel' => 'Space Type',

'groomerServiceSelected' => 'Full Groom',
'spaceServiceSelected' => 'Mobile Station',

'petTypeLabel' => 'Pet Type',
'petTypeSelected' => 'Other',

'petSizeLabel' => 'Pet Size',
'petSizeSelected' => 'Large 19+ kg',
])

@php
$groomerOptions = [
['value' => 'full-groom', 'label' => 'Full Groom'],
['value' => 'face-trim-only', 'label' => 'Face Trim Only'],
['value' => 'tail-trim-only', 'label' => 'Tail Trim Only'],
['value' => 'bath-and-wash', 'label' => 'Bath & Brush'],
['value' => 'nail-trim', 'label' => 'Nail Trim'],
];

$spaceOptions = [
['value' => 'private-rooms', 'label' => 'Private Rooms'],
['value' => 'salon', 'label' => 'Salon'],
['value' => 'mobile-station', 'label' => 'Mobile Station'],
['value' => 'garden-shed', 'label' => 'Garden / Shed'],
['value' => 'others', 'label' => 'Others'],
];

$tabs = [
'groomer' => [
'tabClass' => $activeTab === 'groomer' ? 'top-tabs find-groomer-search active' : 'top-tabs find-groomer-search',
'contentClass' => 'find-groomer-search-content-area',
'title' => $groomerTabLabel,
'searchLabel' => $groomerSearchLabel,
'serviceLabel' => $groomerServiceLabel,
'serviceSelected' => $groomerServiceSelected,
'serviceOptions' => $groomerOptions,
'modalId' => $groomModalId,
'modalOpenId' => $groomModalId,
'prefix' => 'groomer',
],
'space' => [
'tabClass' => $activeTab === 'space' ? 'top-tabs find-space-search active' : 'top-tabs find-space-search',
'contentClass' => 'find-space-search-content-area',
'title' => $spaceTabLabel,
'searchLabel' => $spaceSearchLabel,
'serviceLabel' => $spaceServiceLabel,
'serviceSelected' => $spaceServiceSelected,
'serviceOptions' => $spaceOptions,
'modalId' => $spaceModalId,
'modalOpenId' => $spaceModalId,
'prefix' => 'space',
],
];
@endphp

<div class="sticky-spacer"></div>
<div class="sticky-search">
<section>
    <div class="container mt-3 mt-xs-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="mini-search-widget">

                    {{-- Groomer tab --}}
                    <div class="find-groomer-search-content">
                        <div data-section="groomer" class="{{ $tabs['groomer']['tabClass'] }}">
                            <p class="find-groomer-space-text {{ $activeTab === 'groomer' ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14" viewBox="0 0 13 14" fill="none">
                                    <path d="M4.10318 9.48141C5.14742 10.5256 7.68713 9.67928 9.7756 7.59053C11.8644 5.50211 12.7107 2.96245 11.6665 1.91824M7.17571 1.20895L7.64836 1.68192M5.52145 2.86351L5.9941 3.33615M4.10285 4.75439L4.57549 5.22702M3.6302 7.1179L4.10285 7.59053M9.7756 0.5L10.2482 0.972635M9.30295 3.33648L10.2482 4.28175M7.64869 4.99104L8.59398 5.93631M5.75778 6.40894L6.70307 7.35421" stroke="#FDFCF8" stroke-linecap="round" stroke-linejoin="round"></path>
                                    <path d="M4.10274 10.9001C4.49435 10.5085 4.49435 9.87361 4.10274 9.48201C3.71113 9.09041 3.0762 9.09041 2.68459 9.48201L0.793717 11.3728C0.402106 11.7644 0.402106 12.3994 0.793717 12.791C1.18533 13.1826 1.82026 13.1826 2.21187 12.791L4.10274 10.9001Z" stroke="#FDFCF8" stroke-linecap="round" stroke-linejoin="round"></path>
                                </svg>
                                {{ $tabs['groomer']['title'] }}
                            </p>
                        </div>
                    </div>

                    <div class="{{ $tabs['groomer']['contentClass'] }}" style="display: {{ $activeTab === 'groomer' ? 'block' : 'none' }}">
                        <form action="" method="GET">
                            <div class="row gx-2 g-xs-5 input-fields mt-0 d-flex justify-content-center">
                                <div class="w-auto">
                                    <div class="search-input">
                                        <p class="label">{{ $tabs['groomer']['searchLabel'] }}</p>
                                        <input type="text" name="groomer_location" value="{{ $searchValue }}">
                                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="service-type-select">
                                        <p class="label">{{ $tabs['groomer']['serviceLabel'] }}</p>
                                        <div class="custom-select custom-select-streched">
                                            <div class="select-trigger">
                                                <span class="selected-text">{{ $tabs['groomer']['serviceSelected'] }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                    <path d="M13.8737 0.5L7.13022 7.24344L0.499976 0.613201" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>

                                            <ul class="select-options">
                                                @foreach ($tabs['groomer']['serviceOptions'] as $option)
                                                <li data-value="{{ $option['value'] }}">{{ $option['label'] }}</li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="serviceType" value="{{ $tabs['groomer']['serviceSelected'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="datetime-wrapper">
                                        <div class="field-group">
                                            <p class="label">Date</p>
                                            <div class="field date streched">
                                                <div class="input-row streched" tabindex="0" role="button" aria-haspopup="dialog" aria-expanded="false">
                                                    <input class="fake-input" readonly placeholder="02/11/25" aria-label="Date input" />
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                        <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <div class="popover date-popover" data-type="date">
                                                    <div class="panel calendar">
                                                        <div class="month-nav">
                                                            <button type="button" class="prev-month" title="Previous month" aria-label="Previous month">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
                                                                    <path d="M4.56836 0.5L0.500066 4.56829L4.50007 8.56829" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </button>
                                                            <div class="month-label">November 2025</div>
                                                            <button type="button" class="next-month" title="Next month" aria-label="Next month">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                    <circle cx="10" cy="10" r="9.5" fill="#F5F5F5" stroke="#F5F5F5" />
                                                                    <path d="M9 6L13.0683 10.0683L9.06829 14.0683" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div class="weekday-row"></div>
                                                        <div class="days-grid"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="field-group">
                                            <p class="label">Time</p>
                                            <div class="field time streched">
                                                <div class="input-row streched" tabindex="0" role="button" aria-haspopup="dialog" aria-expanded="false">
                                                    <input class="fake-input" readonly placeholder="13:00" aria-label="Time input" />
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                        <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <div class="popover time-popover" data-type="time">
                                                    <div class="time-col">
                                                        <div class="time-list d-flex flex-column align-items-center justify-content-center" role="listbox" aria-label="Time options"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="pet-type-wrapper wider">
                                        <p class="label">{{ $petTypeLabel }}</p>

                                        <div class="pet-toggle" id="petTypeToggle" style="cursor:pointer;">
                                            <button type="button" id="petTypeTriggerBtn" class="pet-option highlight search-custom-width" data-pet="other">
                                                <span>{{ $petTypeSelected }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.42074 0C5.71446 0 5.16085 0.437285 4.81841 0.961736C4.47169 1.49055 4.28049 2.18061 4.28049 2.90555C4.28049 3.63048 4.47169 4.32055 4.81841 4.84936C5.16085 5.37236 5.71446 5.8111 6.42074 5.8111C7.12702 5.8111 7.68063 5.37381 8.02307 4.84936C8.36979 4.32055 8.56099 3.63048 8.56099 2.90555C8.56099 2.18061 8.36979 1.49055 8.02307 0.961736C7.68063 0.438738 7.12702 0 6.42074 0ZM13.5549 0C12.8486 0 12.295 0.437285 11.9526 0.961736C11.6058 1.49055 11.4147 2.18061 11.4147 2.90555C11.4147 3.63048 11.6058 4.32055 11.9526 4.84936C12.295 5.37236 12.8486 5.8111 13.5549 5.8111C14.2612 5.8111 14.8148 5.37381 15.1572 4.84936C15.504 4.32055 15.6951 3.63048 15.6951 2.90555C15.6951 2.18061 15.504 1.49055 15.1572 0.961736C14.8148 0.438738 14.2612 0 13.5549 0ZM2.14025 6.53748C1.43397 6.53748 0.880355 6.97477 0.537915 7.49922C0.191195 8.02803 0 8.7181 0 9.44303C0 10.168 0.191195 10.858 0.537915 11.3868C0.880355 11.9098 1.43397 12.3486 2.14025 12.3486C2.84653 12.3486 3.40014 11.9113 3.74258 11.3868C4.0893 10.858 4.28049 10.168 4.28049 9.44303C4.28049 8.7181 4.0893 8.02803 3.74258 7.49922C3.40014 6.97622 2.84653 6.53748 2.14025 6.53748ZM9.98782 6.53748C8.27562 6.53748 7.00717 7.47307 6.19673 8.63383C5.39628 9.77717 4.99391 11.1965 4.99391 12.3486C4.99391 13.6909 5.7858 14.6251 6.75747 15.1844C7.71345 15.7364 8.91199 15.9805 9.98782 15.9805C11.0637 15.9805 12.2622 15.7379 13.2182 15.1844C14.1884 14.6236 14.9817 13.6909 14.9817 12.3486C14.9817 11.1965 14.5794 9.77717 13.7789 8.63383C12.9699 7.47162 11.7014 6.53748 9.98782 6.53748ZM17.8354 6.53748C17.1291 6.53748 16.5755 6.97477 16.2331 7.49922C15.8863 8.02803 15.6951 8.7181 15.6951 9.44303C15.6951 10.168 15.8863 10.858 16.2331 11.3868C16.5755 11.9098 17.1291 12.3486 17.8354 12.3486C18.5417 12.3486 19.0953 11.9113 19.4377 11.3868C19.7844 10.858 19.9756 10.168 19.9756 9.44303C19.9756 8.7181 19.7844 8.02803 19.4377 7.49922C19.0953 6.97622 18.5417 6.53748 17.8354 6.53748Z" fill="white" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div id="petTypeOptions" style="display:none;">
                                            <div class="pet-toggle" style="border-radius:0;">
                                                <button type="button" class="pet-option search-custom-width" data-pet="cat" style="flex:1; border-radius:0;" onclick="
                                                    document.getElementById('petTypeTriggerBtn').innerHTML = '<span>Cat</span><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'23\' viewBox=\'0 0 16 23\' fill=\'none\'><path fill-rule=\'evenodd\' clip-rule=\'evenodd\' d=\'M7.06676 3.58301C7.8272 3.48926 8.88019 3.51288 9.88707 3.85352C11.0004 4.23022 12.0757 5.00405 12.6019 6.43945L14.7718 7.47949L14.8187 7.64941C15.1065 8.68692 15.2771 10.2987 14.8314 11.7656C14.6068 12.5047 14.2228 13.2153 13.6107 13.791C12.997 14.3682 12.1721 14.7931 11.0941 14.9854C7.21594 15.6771 5.0156 18.9931 4.40856 20.5596C4.16972 21.2436 3.6234 22.8966 3.6234 23C-3.55928 11.9396 1.57287 3.05798 5.03649 0L7.06676 3.58301ZM9.46911 7.20898C8.89995 7.20898 8.29637 7.49132 8.29625 8.62109C8.29625 9.4011 9.2901 8.62135 9.93786 8.62109C10.5856 8.62109 10.642 9.40123 10.642 8.62109C10.6418 7.84114 10.1167 7.20904 9.46911 7.20898Z\' fill=\'white\'/></svg>';
                                                    document.getElementById('petTypeOptions').style.display='none';
                                                    document.getElementById('petTypeToggle').style.borderRadius='10px';
                                                ">
                                                    <span>Cat</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="23" viewBox="0 0 16 23" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.06676 3.58301C7.8272 3.48926 8.88019 3.51288 9.88707 3.85352C11.0004 4.23022 12.0757 5.00405 12.6019 6.43945L14.7718 7.47949L14.8187 7.64941C15.1065 8.68692 15.2771 10.2987 14.8314 11.7656C14.6068 12.5047 14.2228 13.2153 13.6107 13.791C12.997 14.3682 12.1721 14.7931 11.0941 14.9854C7.21594 15.6771 5.0156 18.9931 4.40856 20.5596C4.16972 21.2436 3.6234 22.8966 3.6234 23C-3.55928 11.9396 1.57287 3.05798 5.03649 0L7.06676 3.58301ZM9.46911 7.20898C8.89995 7.20898 8.29637 7.49132 8.29625 8.62109C8.29625 9.4011 9.2901 8.62135 9.93786 8.62109C10.5856 8.62109 10.642 9.40123 10.642 8.62109C10.6418 7.84114 10.1167 7.20904 9.46911 7.20898Z" fill="#D4D4D4" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="pet-toggle" style="border-radius:0 0 10px 10px;">
                                                <button type="button" class="pet-option search-custom-width" data-pet="dog" style="flex:1; border-radius:0 0 10px 10px;" onclick="
                                                    document.getElementById('petTypeTriggerBtn').innerHTML = '<span>Dog</span><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'22\' height=\'21\' viewBox=\'0 0 22 21\' fill=\'none\'><path fill-rule=\'evenodd\' clip-rule=\'evenodd\' d=\'M11.4592 0C12.0762 1.17851e-05 12.6594 0.284537 13.0383 0.771484L16.2531 4.90625C16.4122 5.11061 16.6453 5.24551 16.9016 5.28223L19.9856 5.72266C20.3434 5.77382 20.646 6.01312 20.759 6.35645C21.0768 7.32333 21.6368 9.33325 21.2541 10.5C20.7993 11.8862 20.0695 12.5798 18.7541 12.9189C16.5013 13.4995 14.6388 12.8359 12.4377 14.5137C11.7581 15.0318 11.2942 15.7094 10.9895 16.4668C9.95231 19.0452 6.72481 21.7057 4.32931 20.2969L1.40646 18.5781L2.88595 12.9932C3.03721 12.9827 3.18554 12.9709 3.32638 12.9531C3.72891 12.9023 4.1159 12.8149 4.36935 12.6543C4.57266 12.5253 4.78069 12.3018 4.97774 12.0498C5.17866 11.7928 5.38389 11.4855 5.57833 11.168C5.96742 10.5325 6.3241 9.84072 6.53439 9.39648C6.59336 9.2718 6.53981 9.12263 6.41524 9.06348C6.29077 9.00495 6.14238 9.05747 6.08321 9.18164C5.87882 9.61347 5.53039 10.2892 5.15255 10.9062C4.96358 11.2149 4.76927 11.5055 4.58419 11.7422C4.39527 11.9838 4.23006 12.151 4.10177 12.2324C3.94889 12.3293 3.65885 12.4072 3.26388 12.457C2.87959 12.5055 2.43068 12.5234 1.98946 12.5225C1.58435 12.5216 1.18926 12.5013 0.86251 12.4795C0.852906 12.4768 0.842764 12.4744 0.833213 12.4717C0.259745 12.3089 -0.117591 11.6851 0.0334085 11.1084C1.50836 5.48351 2.34847 2.92214 3.76583 1.50488C5.26191 0.00930829 8.24426 5.72137e-05 8.28146 0H11.4592ZM11.8498 5.01758C11.2131 5.01772 10.5383 5.33454 10.5383 6.59863C10.5386 7.47088 11.6506 6.59863 12.3752 6.59863C13.0998 6.59867 13.1623 7.47086 13.1623 6.59863C13.1623 5.72576 12.5746 5.01758 11.8498 5.01758Z\' fill=\'white\'/></svg>';
                                                    document.getElementById('petTypeOptions').style.display='none';
                                                    document.getElementById('petTypeToggle').style.borderRadius='10px';
                                                ">
                                                    <span>Dog</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="21" viewBox="0 0 22 21" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 0C12.0762 1.17851e-05 12.6594 0.284537 13.0383 0.771484L16.2531 4.90625C16.4122 5.11061 16.6453 5.24551 16.9016 5.28223L19.9856 5.72266C20.3434 5.77382 20.646 6.01312 20.759 6.35645C21.0768 7.32333 21.6368 9.33325 21.2541 10.5C20.7993 11.8862 20.0695 12.5798 18.7541 12.9189C16.5013 13.4995 14.6388 12.8359 12.4377 14.5137C11.7581 15.0318 11.2942 15.7094 10.9895 16.4668C9.95231 19.0452 6.72481 21.7057 4.32931 20.2969L1.40646 18.5781L2.88595 12.9932C3.03721 12.9827 3.18554 12.9709 3.32638 12.9531C3.72891 12.9023 4.1159 12.8149 4.36935 12.6543C4.57266 12.5253 4.78069 12.3018 4.97774 12.0498C5.17866 11.7928 5.38389 11.4855 5.57833 11.168C5.96742 10.5325 6.3241 9.84072 6.53439 9.39648C6.59336 9.2718 6.53981 9.12263 6.41524 9.06348C6.29077 9.00495 6.14238 9.05747 6.08321 9.18164C5.87882 9.61347 5.53039 10.2892 5.15255 10.9062C4.96358 11.2149 4.76927 11.5055 4.58419 11.7422C4.39527 11.9838 4.23006 12.151 4.10177 12.2324C3.94889 12.3293 3.65885 12.4072 3.26388 12.457C2.87959 12.5055 2.43068 12.5234 1.98946 12.5225C1.58435 12.5216 1.18926 12.5013 0.86251 12.4795C0.852906 12.4768 0.842764 12.4744 0.833213 12.4717C0.259745 12.3089 -0.117591 11.6851 0.0334085 11.1084C1.50836 5.48351 2.34847 2.92214 3.76583 1.50488C5.26191 0.00930829 8.24426 5.72137e-05 8.28146 0H11.4592ZM11.8498 5.01758C11.2131 5.01772 10.5383 5.33454 10.5383 6.59863C10.5386 7.47088 11.6506 6.59863 12.3752 6.59863C13.0998 6.59867 13.1623 7.47086 13.1623 6.59863C13.1623 5.72576 12.5746 5.01758 11.8498 5.01758Z" fill="#D4D4D4" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="pet-weight-wrapper wider">
                                        <p class="label">{{ $petSizeLabel }}</p>

                                        <div class="weight-toggle flex-column" id="petSizeToggle" style="cursor:pointer;">
                                            <button type="button" class="weight-option search-custom-large-btn-width large active" data-weight="large">
                                                <span id="petSizeLabel">{{ $petSizeSelected }}</span>
                                            </button>
                                        </div>

                                        <div id="petSizeOptions" style="display:none;">
                                            <div class="weight-toggle" style="border-radius:0;">
                                                <button type="button" class="weight-option search-custom-large-btn-width large" data-weight="small" style="flex:1; border-radius:0;" onclick="document.getElementById('petSizeLabel').textContent='Small 0-7 kg'; document.getElementById('petSizeOptions').style.display='none'; document.getElementById('petSizeToggle').style.borderRadius='10px';">
                                                    <span>Small 0-7 kg</span>
                                                </button>
                                            </div>
                                            <div class="weight-toggle" style="border-radius:0 0 10px 10px;">
                                                <button type="button" class="weight-option search-custom-large-btn-width large" data-weight="medium" style="flex:1; border-radius:0 0 10px 10px;" onclick="document.getElementById('petSizeLabel').textContent='Medium 8-18 kg'; document.getElementById('petSizeOptions').style.display='none'; document.getElementById('petSizeToggle').style.borderRadius='10px';">
                                                    <span>Medium 8-18 kg</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto cursor">
                                    <p class="label" style="visibility: hidden;">search</p>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                        <rect width="48" height="48" rx="24" fill="#FBAC83" />
                                        <path d="M22.4121 17C25.4011 17 27.8241 19.423 27.8242 22.4121C27.8242 23.9471 27.1868 25.3321 26.1592 26.3183C25.1858 27.2524 23.8667 27.8242 22.4121 27.8242C19.4231 27.8241 17 25.4012 17 22.4121C17.0001 19.4231 19.4231 17 22.4121 17Z" stroke="white" stroke-width="2" />
                                        <path d="M32.0634 33.4776C32.454 33.8681 33.0871 33.8681 33.4776 33.4776C33.8682 33.0871 33.8682 32.4539 33.4777 32.0634L32.7705 32.7705L32.0634 33.4776ZM26.8516 26.8515L26.1445 27.5586L32.0634 33.4776L32.7705 32.7705L33.4777 32.0634L27.5587 26.1444L26.8516 26.8515Z" fill="white" />
                                    </svg>
                                </div>
                                <div class="w-auto" style="margin-left: 2%;">
                                    <p class="label">Filter</p>
                                    <div class="filter-svg">
                                        <svg data-modal-open="{{ $tabs['groomer']['modalOpenId'] }}" class="filters-icon" xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                            <path
                                                d="M3.875 13.25C3.0462 13.25 2.25134 12.9208 1.66529 12.3347C1.07924 11.7487 0.75 10.9538 0.75 10.125C0.75 9.2962 1.07924 8.50134 1.66529 7.91529C2.25134 7.32924 3.0462 7 3.875 7M3.875 13.25C4.7038 13.25 5.49866 12.9208 6.08471 12.3347C6.67076 11.7487 7 10.9538 7 10.125C7 9.2962 6.67076 8.50134 6.08471 7.91529C5.49866 7.32924 4.7038 7 3.875 7M3.875 13.25V25.75M3.875 7V0.75M13.25 22.625C12.4212 22.625 11.6263 22.2958 11.0403 21.7097C10.4542 21.1237 10.125 20.3288 10.125 19.5C10.125 18.6712 10.4542 17.8763 11.0403 17.2903C11.6263 16.7042 12.4212 16.375 13.25 16.375M13.25 22.625C14.0788 22.625 14.8737 22.2958 15.4597 21.7097C16.0458 21.1237 16.375 20.3288 16.375 19.5C16.375 18.6712 16.0458 17.8763 15.4597 17.2903C14.8737 16.7042 14.0788 16.375 13.25 16.375M13.25 22.625V25.75M13.25 16.375V0.75M22.625 8.5625C21.7962 8.5625 21.0013 8.23326 20.4153 7.64721C19.8292 7.06116 19.5 6.2663 19.5 5.4375C19.5 4.6087 19.8292 3.81384 20.4153 3.22779C21.0013 2.64174 21.7962 2.3125 22.625 2.3125M22.625 8.5625C23.4538 8.5625 24.2487 8.23326 24.8347 7.64721C25.4208 7.06116 25.75 6.2663 25.75 5.4375C25.75 4.6087 25.4208 3.81384 24.8347 3.22779C24.2487 2.64174 23.4538 2.3125 22.625 2.3125M22.625 8.5625V25.75M22.625 2.3125V0.75"
                                                stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Space tab --}}
                    <div class="find-space-search-content">
                        <div data-section="space" class="{{ $tabs['space']['tabClass'] }}">
                            <p class="find-groomer-space-text {{ $activeTab === 'space' ? 'active' : '' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none">
                                    <path d="M13.6338 12.6012V3.98751C13.6338 3.96684 13.6355 3.94659 13.6387 3.92677L11.3098 1.9408C10.8146 1.51908 10.4724 1.22843 10.1821 1.03893C9.90186 0.856019 9.7135 0.797503 9.53333 0.797503C9.3533 0.797503 9.16612 0.856233 8.88614 1.03893C8.59582 1.22845 8.25275 1.51885 7.75684 1.9408L5.42632 3.92677C5.42953 3.94667 5.43286 3.96676 5.43286 3.98751V12.6012C5.43251 12.8211 5.24532 12.9999 5.01448 12.9999C4.78378 12.9998 4.59645 12.821 4.59609 12.6012V4.63393L4.16464 5.00308C3.99239 5.14987 3.72704 5.13454 3.57302 4.97037C3.41961 4.80631 3.43397 4.55476 3.60571 4.40807L7.19791 1.34734H7.19954C7.67887 0.939499 8.06705 0.607022 8.4122 0.381618C8.76777 0.149501 9.12077 2.33993e-07 9.53333 0C9.94583 0 10.2988 0.149492 10.6545 0.381618C10.9998 0.607077 11.3896 0.939348 11.8687 1.34734L15.461 4.40807C15.6327 4.55476 15.647 4.80631 15.4936 4.97037C15.3396 5.13454 15.0743 5.14987 14.902 5.00308L14.4706 4.63393V12.6012C14.4702 12.821 14.2829 12.9998 14.0522 12.9999C13.8213 12.9999 13.6342 12.8211 13.6338 12.6012Z" fill="#FDFCF8"></path>
                                    <path d="M1.89711 6.93401C1.89711 6.63323 1.81156 6.37517 1.68983 6.20094C1.56805 6.02682 1.42657 5.95363 1.29997 5.95363C1.17344 5.95374 1.03181 6.02694 0.910117 6.20094C0.788487 6.37516 0.702836 6.6334 0.702836 6.93401C0.702947 7.23473 0.788337 7.49296 0.910117 7.66708C1.03179 7.84098 1.17347 7.91281 1.29997 7.91291C1.42647 7.91291 1.56811 7.8409 1.68983 7.66708C1.81161 7.49296 1.897 7.23473 1.89711 6.93401ZM2.59994 6.93401C2.59983 7.38204 2.47407 7.80238 2.25264 8.119C2.03101 8.43588 1.69739 8.6666 1.29997 8.6666C0.902825 8.66649 0.570251 8.43562 0.348672 8.119C0.127221 7.80237 0.000110851 7.38206 0 6.93401C0 6.48573 0.127124 6.06432 0.348672 5.74755C0.570251 5.43102 0.902904 5.20006 1.29997 5.19995C1.69734 5.19995 2.03101 5.43073 2.25264 5.74755C2.47419 6.06432 2.59994 6.48573 2.59994 6.93401Z" fill="#FDFCF8"></path>
                                    <path d="M0.866821 12.5937V8.20615C0.866821 7.9818 1.06083 7.79993 1.30015 7.79993C1.53946 7.79993 1.73347 7.9818 1.73347 8.20615V12.5937C1.73329 12.8179 1.53935 12.9999 1.30015 12.9999C1.06094 12.9999 0.867004 12.8179 0.866821 12.5937Z" fill="#FDFCF8"></path>
                                    <path d="M11.084 9.68613C11.084 9.33228 11.0824 9.10762 11.0599 8.94297C11.039 8.79034 11.0071 8.75062 10.9878 8.73154C10.9684 8.71253 10.9283 8.67955 10.7729 8.65896C10.6054 8.6368 10.376 8.63687 10.016 8.63687H9.27833C8.91831 8.63687 8.68888 8.6368 8.52144 8.65896C8.36597 8.67955 8.32588 8.71253 8.30656 8.73154C8.28722 8.75062 8.25527 8.79034 8.2344 8.94297C8.21189 9.10762 8.21035 9.33228 8.21035 9.68613V12.1917H11.084V9.68613ZM10.3864 5.64688C10.6128 5.64705 10.7966 5.82811 10.7969 6.0508C10.7969 6.27378 10.613 6.45456 10.3864 6.45473H8.9079C8.68133 6.45456 8.49739 6.27378 8.49739 6.0508C8.49773 5.82811 8.68154 5.64705 8.9079 5.64688H10.3864ZM10.3864 3.46631L10.4682 3.4742C10.6556 3.51161 10.7969 3.67481 10.7969 3.87023C10.7969 4.06566 10.6556 4.22886 10.4682 4.26627L10.3864 4.27416H8.9079C8.68133 4.27399 8.49739 4.09321 8.49739 3.87023C8.49739 3.64726 8.68133 3.46648 8.9079 3.46631H10.3864ZM11.905 12.1917H15.1891C15.4159 12.1917 15.5997 12.3726 15.5997 12.5957C15.5993 12.8185 15.4156 12.9996 15.1891 12.9996H0.410517C0.184008 12.9996 0.000345759 12.8185 0 12.5957C0 12.3726 0.183795 12.1917 0.410517 12.1917H7.38931V9.68613C7.38931 9.35529 7.38828 9.06657 7.41978 8.83568C7.45297 8.59291 7.529 8.35435 7.72606 8.16037C7.92324 7.96635 8.1656 7.89167 8.4124 7.859C8.64727 7.82794 8.94165 7.82902 9.27833 7.82902H10.016C10.3527 7.82902 10.647 7.82794 10.8819 7.859C11.1287 7.89167 11.3711 7.96635 11.5682 8.16037C11.7653 8.35435 11.8413 8.59291 11.8745 8.83568C11.906 9.06657 11.905 9.35529 11.905 9.68613V12.1917Z" fill="#FDFCF8"></path>
                                </svg>
                                {{ $tabs['space']['title'] }}
                            </p>
                        </div>
                    </div>

                    <div class="{{ $tabs['space']['contentClass'] }}" style="display: {{ $activeTab === 'space' ? 'block' : 'none' }}">
                        <form action="" method="GET">
                            <div class="row gx-2 g-xs-5 input-fields mt-0 d-flex justify-content-center">
                                <div class="w-auto">
                                    <div class="search-input">
                                        <p class="label">{{ $tabs['space']['searchLabel'] }}</p>
                                        <input type="text" name="space_location" value="{{ $searchValue }}">
                                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" viewBox="0 0 16 16">
                                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="service-type-select">
                                        <p class="label">{{ $tabs['space']['serviceLabel'] }}</p>
                                        <div class="custom-select custom-select-streched">
                                            <div class="select-trigger">
                                                <span class="selected-text">{{ $tabs['space']['serviceSelected'] }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                    <path d="M13.8737 0.5L7.13022 7.24344L0.499976 0.613201" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>

                                            <ul class="select-options">
                                                @foreach ($tabs['space']['serviceOptions'] as $option)
                                                <li data-value="{{ $option['value'] }}">{{ $option['label'] }}</li>
                                                @endforeach
                                            </ul>

                                            <input type="hidden" name="spaceType" value="{{ $tabs['space']['serviceSelected'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="datetime-wrapper">
                                        <div class="field-group">
                                            <p class="label">Date</p>
                                            <div class="field date streched">
                                                <div class="input-row streched" tabindex="0" role="button" aria-haspopup="dialog" aria-expanded="false">
                                                    <input class="fake-input" readonly placeholder="02/11/25" aria-label="Date input" />
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                        <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <div class="popover date-popover" data-type="date">
                                                    <div class="panel calendar">
                                                        <div class="month-nav">
                                                            <button type="button" class="prev-month" title="Previous month" aria-label="Previous month">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
                                                                    <path d="M4.56836 0.5L0.500066 4.56829L4.50007 8.56829" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </button>
                                                            <div class="month-label">November 2025</div>
                                                            <button type="button" class="next-month" title="Next month" aria-label="Next month">
                                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                                                    <circle cx="10" cy="10" r="9.5" fill="#F5F5F5" stroke="#F5F5F5" />
                                                                    <path d="M9 6L13.0683 10.0683L9.06829 14.0683" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                                </svg>
                                                            </button>
                                                        </div>
                                                        <div class="weekday-row"></div>
                                                        <div class="days-grid"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="field-group">
                                            <p class="label">Time</p>
                                            <div class="field time streched">
                                                <div class="input-row streched" tabindex="0" role="button" aria-haspopup="dialog" aria-expanded="false">
                                                    <input class="fake-input" readonly placeholder="13:00" aria-label="Time input" />
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                        <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                                <div class="popover time-popover" data-type="time">
                                                    <div class="time-col">
                                                        <div class="time-list d-flex flex-column align-items-center justify-content-center" role="listbox" aria-label="Time options"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="pet-type-wrapper wider">
                                        <p class="label">{{ $petTypeLabel }}</p>

                                        <div class="pet-toggle" id="spaceTypeToggle" style="cursor:pointer;">
                                            <button type="button" id="spaceTypeTriggerBtn" class="pet-option highlight search-custom-width" data-pet="other">
                                                <span>{{ $petTypeSelected }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd" d="M6.42074 0C5.71446 0 5.16085 0.437285 4.81841 0.961736C4.47169 1.49055 4.28049 2.18061 4.28049 2.90555C4.28049 3.63048 4.47169 4.32055 4.81841 4.84936C5.16085 5.37236 5.71446 5.8111 6.42074 5.8111C7.12702 5.8111 7.68063 5.37381 8.02307 4.84936C8.36979 4.32055 8.56099 3.63048 8.56099 2.90555C8.56099 2.18061 8.36979 1.49055 8.02307 0.961736C7.68063 0.438738 7.12702 0 6.42074 0ZM13.5549 0C12.8486 0 12.295 0.437285 11.9526 0.961736C11.6058 1.49055 11.4147 2.18061 11.4147 2.90555C11.4147 3.63048 11.6058 4.32055 11.9526 4.84936C12.295 5.37236 12.8486 5.8111 13.5549 5.8111C14.2612 5.8111 14.8148 5.37381 15.1572 4.84936C15.504 4.32055 15.6951 3.63048 15.6951 2.90555C15.6951 2.18061 15.504 1.49055 15.1572 0.961736C14.8148 0.438738 14.2612 0 13.5549 0ZM2.14025 6.53748C1.43397 6.53748 0.880355 6.97477 0.537915 7.49922C0.191195 8.02803 0 8.7181 0 9.44303C0 10.168 0.191195 10.858 0.537915 11.3868C0.880355 11.9098 1.43397 12.3486 2.14025 12.3486C2.84653 12.3486 3.40014 11.9113 3.74258 11.3868C4.0893 10.858 4.28049 10.168 4.28049 9.44303C4.28049 8.7181 4.0893 8.02803 3.74258 7.49922C3.40014 6.97622 2.84653 6.53748 2.14025 6.53748ZM9.98782 6.53748C8.27562 6.53748 7.00717 7.47307 6.19673 8.63383C5.39628 9.77717 4.99391 11.1965 4.99391 12.3486C4.99391 13.6909 5.7858 14.6251 6.75747 15.1844C7.71345 15.7364 8.91199 15.9805 9.98782 15.9805C11.0637 15.9805 12.2622 15.7379 13.2182 15.1844C14.1884 14.6236 14.9817 13.6909 14.9817 12.3486C14.9817 11.1965 14.5794 9.77717 13.7789 8.63383C12.9699 7.47162 11.7014 6.53748 9.98782 6.53748ZM17.8354 6.53748C17.1291 6.53748 16.5755 6.97477 16.2331 7.49922C15.8863 8.02803 15.6951 8.7181 15.6951 9.44303C15.6951 10.168 15.8863 10.858 16.2331 11.3868C16.5755 11.9098 17.1291 12.3486 17.8354 12.3486C18.5417 12.3486 19.0953 11.9113 19.4377 11.3868C19.7844 10.858 19.9756 10.168 19.9756 9.44303C19.9756 8.7181 19.7844 8.02803 19.4377 7.49922C19.0953 6.97622 18.5417 6.53748 17.8354 6.53748Z" fill="white" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div id="spaceTypeOptions" style="display:none;">
                                            <div class="pet-toggle" style="border-radius:0;">
                                                <button type="button" class="pet-option search-custom-width" data-pet="cat" style="flex:1; border-radius:0;" onclick="
                                                    document.getElementById('spaceTypeTriggerBtn').innerHTML = '<span>Cat</span><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'16\' height=\'23\' viewBox=\'0 0 16 23\' fill=\'none\'><path fill-rule=\'evenodd\' clip-rule=\'evenodd\' d=\'M7.06676 3.58301C7.8272 3.48926 8.88019 3.51288 9.88707 3.85352C11.0004 4.23022 12.0757 5.00405 12.6019 6.43945L14.7718 7.47949L14.8187 7.64941C15.1065 8.68692 15.2771 10.2987 14.8314 11.7656C14.6068 12.5047 14.2228 13.2153 13.6107 13.791C12.997 14.3682 12.1721 14.7931 11.0941 14.9854C7.21594 15.6771 5.0156 18.9931 4.40856 20.5596C4.16972 21.2436 3.6234 22.8966 3.6234 23C-3.55928 11.9396 1.57287 3.05798 5.03649 0L7.06676 3.58301ZM9.46911 7.20898C8.89995 7.20898 8.29637 7.49132 8.29625 8.62109C8.29625 9.4011 9.2901 8.62135 9.93786 8.62109C10.5856 8.62109 10.642 9.40123 10.642 8.62109C10.6418 7.84114 10.1167 7.20904 9.46911 7.20898Z\' fill=\'white\'/></svg>';
                                                    document.getElementById('spaceTypeOptions').style.display='none';
                                                    document.getElementById('spaceTypeToggle').style.borderRadius='10px';
                                                ">
                                                    <span>Cat</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="23" viewBox="0 0 16 23" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M7.06676 3.58301C7.8272 3.48926 8.88019 3.51288 9.88707 3.85352C11.0004 4.23022 12.0757 5.00405 12.6019 6.43945L14.7718 7.47949L14.8187 7.64941C15.1065 8.68692 15.2771 10.2987 14.8314 11.7656C14.6068 12.5047 14.2228 13.2153 13.6107 13.791C12.997 14.3682 12.1721 14.7931 11.0941 14.9854C7.21594 15.6771 5.0156 18.9931 4.40856 20.5596C4.16972 21.2436 3.6234 22.8966 3.6234 23C-3.55928 11.9396 1.57287 3.05798 5.03649 0L7.06676 3.58301ZM9.46911 7.20898C8.89995 7.20898 8.29637 7.49132 8.29625 8.62109C8.29625 9.4011 9.2901 8.62135 9.93786 8.62109C10.5856 8.62109 10.642 9.40123 10.642 8.62109C10.6418 7.84114 10.1167 7.20904 9.46911 7.20898Z" fill="#D4D4D4" />
                                                    </svg>
                                                </button>
                                            </div>
                                            <div class="pet-toggle" style="border-radius:0 0 10px 10px;">
                                                <button type="button" class="pet-option search-custom-width" data-pet="dog" style="flex:1; border-radius:0 0 10px 10px;" onclick="
                                                    document.getElementById('spaceTypeTriggerBtn').innerHTML = '<span>Dog</span><svg xmlns=\'http://www.w3.org/2000/svg\' width=\'22\' height=\'21\' viewBox=\'0 0 22 21\' fill=\'none\'><path fill-rule=\'evenodd\' clip-rule=\'evenodd\' d=\'M11.4592 0C12.0762 1.17851e-05 12.6594 0.284537 13.0383 0.771484L16.2531 4.90625C16.4122 5.11061 16.6453 5.24551 16.9016 5.28223L19.9856 5.72266C20.3434 5.77382 20.646 6.01312 20.759 6.35645C21.0768 7.32333 21.6368 9.33325 21.2541 10.5C20.7993 11.8862 20.0695 12.5798 18.7541 12.9189C16.5013 13.4995 14.6388 12.8359 12.4377 14.5137C11.7581 15.0318 11.2942 15.7094 10.9895 16.4668C9.95231 19.0452 6.72481 21.7057 4.32931 20.2969L1.40646 18.5781L2.88595 12.9932C3.03721 12.9827 3.18554 12.9709 3.32638 12.9531C3.72891 12.9023 4.1159 12.8149 4.36935 12.6543C4.57266 12.5253 4.78069 12.3018 4.97774 12.0498C5.17866 11.7928 5.38389 11.4855 5.57833 11.168C5.96742 10.5325 6.3241 9.84072 6.53439 9.39648C6.59336 9.2718 6.53981 9.12263 6.41524 9.06348C6.29077 9.00495 6.14238 9.05747 6.08321 9.18164C5.87882 9.61347 5.53039 10.2892 5.15255 10.9062C4.96358 11.2149 4.76927 11.5055 4.58419 11.7422C4.39527 11.9838 4.23006 12.151 4.10177 12.2324C3.94889 12.3293 3.65885 12.4072 3.26388 12.457C2.87959 12.5055 2.43068 12.5234 1.98946 12.5225C1.58435 12.5216 1.18926 12.5013 0.86251 12.4795C0.852906 12.4768 0.842764 12.4744 0.833213 12.4717C0.259745 12.3089 -0.117591 11.6851 0.0334085 11.1084C1.50836 5.48351 2.34847 2.92214 3.76583 1.50488C5.26191 0.00930829 8.24426 5.72137e-05 8.28146 0H11.4592ZM11.8498 5.01758C11.2131 5.01772 10.5383 5.33454 10.5383 6.59863C10.5386 7.47088 11.6506 6.59863 12.3752 6.59863C13.0998 6.59867 13.1623 7.47086 13.1623 6.59863C13.1623 5.72576 12.5746 5.01758 11.8498 5.01758Z\' fill=\'white\'/></svg>';
                                                    document.getElementById('spaceTypeOptions').style.display='none';
                                                    document.getElementById('spaceTypeToggle').style.borderRadius='10px';
                                                ">
                                                    <span>Dog</span>
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="21" viewBox="0 0 22 21" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.4592 0C12.0762 1.17851e-05 12.6594 0.284537 13.0383 0.771484L16.2531 4.90625C16.4122 5.11061 16.6453 5.24551 16.9016 5.28223L19.9856 5.72266C20.3434 5.77382 20.646 6.01312 20.759 6.35645C21.0768 7.32333 21.6368 9.33325 21.2541 10.5C20.7993 11.8862 20.0695 12.5798 18.7541 12.9189C16.5013 13.4995 14.6388 12.8359 12.4377 14.5137C11.7581 15.0318 11.2942 15.7094 10.9895 16.4668C9.95231 19.0452 6.72481 21.7057 4.32931 20.2969L1.40646 18.5781L2.88595 12.9932C3.03721 12.9827 3.18554 12.9709 3.32638 12.9531C3.72891 12.9023 4.1159 12.8149 4.36935 12.6543C4.57266 12.5253 4.78069 12.3018 4.97774 12.0498C5.17866 11.7928 5.38389 11.4855 5.57833 11.168C5.96742 10.5325 6.3241 9.84072 6.53439 9.39648C6.59336 9.2718 6.53981 9.12263 6.41524 9.06348C6.29077 9.00495 6.14238 9.05747 6.08321 9.18164C5.87882 9.61347 5.53039 10.2892 5.15255 10.9062C4.96358 11.2149 4.76927 11.5055 4.58419 11.7422C4.39527 11.9838 4.23006 12.151 4.10177 12.2324C3.94889 12.3293 3.65885 12.4072 3.26388 12.457C2.87959 12.5055 2.43068 12.5234 1.98946 12.5225C1.58435 12.5216 1.18926 12.5013 0.86251 12.4795C0.852906 12.4768 0.842764 12.4744 0.833213 12.4717C0.259745 12.3089 -0.117591 11.6851 0.0334085 11.1084C1.50836 5.48351 2.34847 2.92214 3.76583 1.50488C5.26191 0.00930829 8.24426 5.72137e-05 8.28146 0H11.4592ZM11.8498 5.01758C11.2131 5.01772 10.5383 5.33454 10.5383 6.59863C10.5386 7.47088 11.6506 6.59863 12.3752 6.59863C13.0998 6.59867 13.1623 7.47086 13.1623 6.59863C13.1623 5.72576 12.5746 5.01758 11.8498 5.01758Z" fill="#D4D4D4" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="pet-weight-wrapper wider">
                                        <p class="label">{{ $petSizeLabel }}</p>

                                        <div class="weight-toggle flex-column" id="spaceSizeToggle" style="cursor:pointer;">
                                            <button type="button" class="weight-option search-custom-large-btn-width large active" data-weight="large">
                                                <span id="spaceSizeLabel">{{ $petSizeSelected }}</span>
                                            </button>
                                        </div>

                                        <div id="spaceSizeOptions" style="display:none;">
                                            <div class="weight-toggle" style="border-radius:0;">
                                                <button type="button" class="weight-option search-custom-large-btn-width large" data-weight="small" style="flex:1; border-radius:0;"
                                                    onclick="document.getElementById('spaceSizeLabel').textContent='Small 0-7 kg'; document.getElementById('spaceSizeOptions').style.display='none'; document.getElementById('spaceSizeToggle').style.borderRadius='10px';">
                                                    <span>Small 0-7 kg</span>
                                                </button>
                                            </div>
                                            <div class="weight-toggle" style="border-radius:0 0 10px 10px;">
                                                <button type="button" class="weight-option search-custom-large-btn-width large" data-weight="medium" style="flex:1; border-radius:0 0 10px 10px;"
                                                    onclick="document.getElementById('spaceSizeLabel').textContent='Medium 8-18 kg'; document.getElementById('spaceSizeOptions').style.display='none'; document.getElementById('spaceSizeToggle').style.borderRadius='10px';">
                                                    <span>Medium 8-18 kg</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto cursor">
                                    <p class="label" style="visibility: hidden;">search</p>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                                        <rect width="48" height="48" rx="24" fill="#ffa899" />
                                        <path d="M22.4121 17C25.4011 17 27.8241 19.423 27.8242 22.4121C27.8242 23.9471 27.1868 25.3321 26.1592 26.3183C25.1858 27.2524 23.8667 27.8242 22.4121 27.8242C19.4231 27.8241 17 25.4012 17 22.4121C17.0001 19.4231 19.4231 17 22.4121 17Z" stroke="white" stroke-width="2" />
                                        <path d="M32.0634 33.4776C32.454 33.8681 33.0871 33.8681 33.4776 33.4776C33.8682 33.0871 33.8682 32.4539 33.4777 32.0634L32.7705 32.7705L32.0634 33.4776ZM26.8516 26.8515L26.1445 27.5586L32.0634 33.4776L32.7705 32.7705L33.4777 32.0634L27.5587 26.1444L26.8516 26.8515Z" fill="white" />
                                    </svg>
                                </div>
                                <div class="w-auto" style="margin-left: 2%;">
                                    <p class="label">Filter</p>
                                    <div class="filter-svg">
                                        <svg data-modal-open="{{ $tabs['space']['modalOpenId'] }}" class="filters-icon" xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                            <path
                                                d="M3.875 13.25C3.0462 13.25 2.25134 12.9208 1.66529 12.3347C1.07924 11.7487 0.75 10.9538 0.75 10.125C0.75 9.2962 1.07924 8.50134 1.66529 7.91529C2.25134 7.32924 3.0462 7 3.875 7M3.875 13.25C4.7038 13.25 5.49866 12.9208 6.08471 12.3347C6.67076 11.7487 7 10.9538 7 10.125C7 9.2962 6.67076 8.50134 6.08471 7.91529C5.49866 7.32924 4.7038 7 3.875 7M3.875 13.25V25.75M3.875 7V0.75M13.25 22.625C12.4212 22.625 11.6263 22.2958 11.0403 21.7097C10.4542 21.1237 10.125 20.3288 10.125 19.5C10.125 18.6712 10.4542 17.8763 11.0403 17.2903C11.6263 16.7042 12.4212 16.375 13.25 16.375M13.25 22.625C14.0788 22.625 14.8737 22.2958 15.4597 21.7097C16.0458 21.1237 16.375 20.3288 16.375 19.5C16.375 18.6712 16.0458 17.8763 15.4597 17.2903C14.8737 16.7042 14.0788 16.375 13.25 16.375M13.25 22.625V25.75M13.25 16.375V0.75M22.625 8.5625C21.7962 8.5625 21.0013 8.23326 20.4153 7.64721C19.8292 7.06116 19.5 6.2663 19.5 5.4375C19.5 4.6087 19.8292 3.81384 20.4153 3.22779C21.0013 2.64174 21.7962 2.3125 22.625 2.3125M22.625 8.5625C23.4538 8.5625 24.2487 8.23326 24.8347 7.64721C25.4208 7.06116 25.75 6.2663 25.75 5.4375C25.75 4.6087 25.4208 3.81384 24.8347 3.22779C24.2487 2.64174 23.4538 2.3125 22.625 2.3125M22.625 8.5625V25.75M22.625 2.3125V0.75"
                                                stroke="#3B3731" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>
</div>
