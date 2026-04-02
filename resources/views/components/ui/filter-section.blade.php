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
'contentClass' => $activeTab === 'groomer' ? 'find-groomer-search-content-area' : 'find-groomer-search-content-area d-none',
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
'contentClass' => $activeTab === 'space' ? 'find-space-search-content-area' : 'find-space-search-content-area d-none',
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

<section>
    <div class="container mt-3 mt-xs-5">
        <div class="row">
            <div class="col-lg-12">
                <div class="mini-search-widget">

                    {{-- Groomer tab --}}
                    <div class="find-groomer-search-content">
                        <div data-section="groomer" class="{{ $tabs['groomer']['tabClass'] }}">
                            <p>{{ $tabs['groomer']['title'] }}</p>
                        </div>
                    </div>

                    <div class="{{ $tabs['groomer']['contentClass'] }}">
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
                                    <div class="datetime-wrapper" id="groomer-datetime">
                                        <div class="field-group">
                                            <p class="label">Date</p>
                                            <div class="field date streched" id="groomer-dateField">
                                                <div class="input-row streched" tabindex="0" role="button">
                                                    <input class="fake-input" id="groomer-dateInput" readonly placeholder="02/11/25" />
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                        <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>

                                                <div class="popover" id="groomer-datePopover">
                                                    <div style="display:flex;flex-direction: column;">
                                                        <div class="panel calendar">
                                                            <div class="month-nav">
                                                                <button type="button" id="groomer-prevMonth" title="Previous month" aria-label="Previous month">
                                                                    <svg class="chev rotate-left" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>

                                                                <div id="groomer-monthLabel">November 2025</div>

                                                                <button type="button" id="groomer-nextMonth" title="Next month" aria-label="Next month">
                                                                    <svg class="chev rotate-right" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="weekday-row" id="groomer-weekdayRow"></div>
                                                            <div class="days-grid" id="groomer-daysGrid"></div>
                                                        </div>
                                                        <div class="time-col">
                                                            <div class="title">
                                                                <div>Time</div>
                                                            </div>
                                                            <div class="time-list" id="groomer-timeList" role="listbox" aria-label="Time options"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-group">
                                            <p class="label">Time</p>
                                            <div class="field time streched" id="groomer-timeField">
                                                <div class="input-row streched" tabindex="0" role="button">
                                                    <input class="fake-input" id="groomer-timeInput" readonly placeholder="13:00" />
                                                    <svg ...>...</svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="pet-type-wrapper wider">
                                        <p class="label">{{ $petTypeLabel }}</p>
                                        <div class="pet-toggle">
                                            <button type="button" class="pet-option highlight" data-pet="other">
                                                <span>{{ $petTypeSelected }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M6.42074 0C5.71446 0 5.16085 0.437285 4.81841 0.961736C4.47169 1.49055 4.28049 2.18061 4.28049 2.90555C4.28049 3.63048 4.47169 4.32055 4.81841 4.84936C5.16085 5.37236 5.71446 5.8111 6.42074 5.8111C7.12702 5.8111 7.68063 5.37381 8.02307 4.84936C8.36979 4.32055 8.56099 3.63048 8.56099 2.90555C8.56099 2.18061 8.36979 1.49055 8.02307 0.961736C7.68063 0.438738 7.12702 0 6.42074 0ZM13.5549 0C12.8486 0 12.295 0.437285 11.9526 0.961736C11.6058 1.49055 11.4147 2.18061 11.4147 2.90555C11.4147 3.63048 11.6058 4.32055 11.9526 4.84936C12.295 5.37236 12.8486 5.8111 13.5549 5.8111C14.2612 5.8111 14.8148 5.37381 15.1572 4.84936C15.504 4.32055 15.6951 3.63048 15.6951 2.90555C15.6951 2.18061 15.504 1.49055 15.1572 0.961736C14.8148 0.438738 14.2612 0 13.5549 0ZM2.14025 6.53748C1.43397 6.53748 0.880355 6.97477 0.537915 7.49922C0.191195 8.02803 0 8.7181 0 9.44303C0 10.168 0.191195 10.858 0.537915 11.3868C0.880355 11.9098 1.43397 12.3486 2.14025 12.3486C2.84653 12.3486 3.40014 11.9113 3.74258 11.3868C4.0893 10.858 4.28049 10.168 4.28049 9.44303C4.28049 8.7181 4.0893 8.02803 3.74258 7.49922C3.40014 6.97622 2.84653 6.53748 2.14025 6.53748ZM9.98782 6.53748C8.27562 6.53748 7.00717 7.47307 6.19673 8.63383C5.39628 9.77717 4.99391 11.1965 4.99391 12.3486C4.99391 13.6909 5.7858 14.6251 6.75747 15.1844C7.71345 15.7364 8.91199 15.9805 9.98782 15.9805C11.0637 15.9805 12.2622 15.7379 13.2182 15.1844C14.1884 14.6236 14.9817 13.6909 14.9817 12.3486C14.9817 11.1965 14.5794 9.77717 13.7789 8.63383C12.9699 7.47162 11.7014 6.53748 9.98782 6.53748ZM17.8354 6.53748C17.1291 6.53748 16.5755 6.97477 16.2331 7.49922C15.8863 8.02803 15.6951 8.7181 15.6951 9.44303C15.6951 10.168 15.8863 10.858 16.2331 11.3868C16.5755 11.9098 17.1291 12.3486 17.8354 12.3486C18.5417 12.3486 19.0953 11.9113 19.4377 11.3868C19.7844 10.858 19.9756 10.168 19.9756 9.44303C19.9756 8.7181 19.7844 8.02803 19.4377 7.49922C19.0953 6.97622 18.5417 6.53748 17.8354 6.53748Z"
                                                        fill="white" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="pet-weight-wrapper wider">
                                        <p class="label">{{ $petSizeLabel }}</p>
                                        <div class="weight-toggle">
                                            <button type="button" class="weight-option large active" data-weight="large">
                                                <span>{{ $petSizeSelected }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto" style="margin-left: 2%;">
                                    <p class="label">Filter</p>
                                    <div class="filter-svg">
                                        <svg data-modal-open="{{ $tabs['groomer']['modalOpenId'] }}" class="filters-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path
                                                d="M3 9.75C2.40326 9.75 1.83097 9.51295 1.40901 9.09099C0.987053 8.66903 0.75 8.09674 0.75 7.5C0.75 6.90326 0.987053 6.33097 1.40901 5.90901C1.83097 5.48705 2.40326 5.25 3 5.25M3 9.75C3.59674 9.75 4.16903 9.51295 4.59099 9.09099C5.01295 8.66903 5.25 8.09674 5.25 7.5C5.25 6.90326 5.01295 6.33097 4.59099 5.90901C4.16903 5.48705 3.59674 5.25 3 5.25M3 9.75V18.75M3 5.25V0.75M9.75 16.5C9.15326 16.5 8.58097 16.2629 8.15901 15.841C7.73705 15.419 7.5 14.8467 7.5 14.25C7.5 13.6533 7.73705 13.081 8.15901 12.659C8.58097 12.2371 9.15326 12 9.75 12M9.75 16.5C10.3467 16.5 10.919 16.2629 11.341 15.841C11.7629 15.419 12 14.8467 12 14.25C12 13.6533 11.7629 13.081 11.341 12.659C10.919 12.2371 10.3467 12 9.75 12M9.75 16.5V18.75M9.75 12V0.75M16.5 6.375C15.9033 6.375 15.331 6.13795 14.909 5.71599C14.4871 5.29403 14.25 4.72174 14.25 4.125C14.25 3.52826 14.4871 2.95597 14.909 2.53401C15.331 2.11205 15.9033 1.875 16.5 1.875M16.5 6.375C17.0967 6.375 17.669 6.13795 18.091 5.71599C18.5129 5.29403 18.75 4.72174 18.75 4.125C18.75 3.52826 18.5129 2.95597 18.091 2.53401C17.669 2.11205 17.0967 1.875 16.5 1.875M16.5 6.375V18.75M16.5 1.875V0.75"
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
                            <p>{{ $tabs['space']['title'] }}</p>
                        </div>
                    </div>

                    <div class="{{ $tabs['space']['contentClass'] }}">
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
                                    <div class="datetime-wrapper" id="space-datetime">
                                        <div class="field-group">
                                            <p class="label">Date</p>
                                            <div class="field date streched" id="space-dateField">
                                                <div class="input-row streched" tabindex="0" role="button" aria-haspopup="dialog" aria-expanded="false">
                                                    <input class="fake-input" id="space-dateInput" readonly placeholder="02/11/25" aria-label="Date input" />
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                        <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>

                                                <div class="popover" id="space-datePopover" data-type="date">
                                                    <div style="display:flex;flex-direction: column;">
                                                        <div class="panel calendar">
                                                            <div class="month-nav">
                                                                <button type="button" id="space-prevMonth" title="Previous month" aria-label="Previous month">
                                                                    <svg class="chev rotate-left" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>

                                                                <div id="space-monthLabel">November 2025</div>

                                                                <button type="button" id="space-nextMonth" title="Next month" aria-label="Next month">
                                                                    <svg class="chev rotate-right" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                                        <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                                    </svg>
                                                                </button>
                                                            </div>

                                                            <div class="weekday-row" id="space-weekdayRow"></div>
                                                            <div class="days-grid" id="space-daysGrid"></div>
                                                        </div>

                                                        <div class="time-col">
                                                            <div class="title">
                                                                <div>Time</div>
                                                            </div>
                                                            <div class="time-list" id="space-timeList" role="listbox" aria-label="Time options"></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="field-group">
                                            <p class="label">Time</p>
                                            <div class="field time streched" id="space-timeField">
                                                <div class="input-row streched" tabindex="0" role="button" aria-haspopup="dialog" aria-expanded="false">
                                                    <input class="fake-input" id="space-timeInput" readonly placeholder="13:00" aria-label="Time input" />
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                                        <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="pet-type-wrapper wider">
                                        <p class="label">{{ $petTypeLabel }}</p>
                                        <div class="pet-toggle">
                                            <button type="button" class="pet-option highlight" data-pet="other">
                                                <span>{{ $petTypeSelected }}</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16" viewBox="0 0 20 16" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M6.42074 0C5.71446 0 5.16085 0.437285 4.81841 0.961736C4.47169 1.49055 4.28049 2.18061 4.28049 2.90555C4.28049 3.63048 4.47169 4.32055 4.81841 4.84936C5.16085 5.37236 5.71446 5.8111 6.42074 5.8111C7.12702 5.8111 7.68063 5.37381 8.02307 4.84936C8.36979 4.32055 8.56099 3.63048 8.56099 2.90555C8.56099 2.18061 8.36979 1.49055 8.02307 0.961736C7.68063 0.438738 7.12702 0 6.42074 0ZM13.5549 0C12.8486 0 12.295 0.437285 11.9526 0.961736C11.6058 1.49055 11.4147 2.18061 11.4147 2.90555C11.4147 3.63048 11.6058 4.32055 11.9526 4.84936C12.295 5.37236 12.8486 5.8111 13.5549 5.8111C14.2612 5.8111 14.8148 5.37381 15.1572 4.84936C15.504 4.32055 15.6951 3.63048 15.6951 2.90555C15.6951 2.18061 15.504 1.49055 15.1572 0.961736C14.8148 0.438738 14.2612 0 13.5549 0ZM2.14025 6.53748C1.43397 6.53748 0.880355 6.97477 0.537915 7.49922C0.191195 8.02803 0 8.7181 0 9.44303C0 10.168 0.191195 10.858 0.537915 11.3868C0.880355 11.9098 1.43397 12.3486 2.14025 12.3486C2.84653 12.3486 3.40014 11.9113 3.74258 11.3868C4.0893 10.858 4.28049 10.168 4.28049 9.44303C4.28049 8.7181 4.0893 8.02803 3.74258 7.49922C3.40014 6.97622 2.84653 6.53748 2.14025 6.53748ZM9.98782 6.53748C8.27562 6.53748 7.00717 7.47307 6.19673 8.63383C5.39628 9.77717 4.99391 11.1965 4.99391 12.3486C4.99391 13.6909 5.7858 14.6251 6.75747 15.1844C7.71345 15.7364 8.91199 15.9805 9.98782 15.9805C11.0637 15.9805 12.2622 15.7379 13.2182 15.1844C14.1884 14.6236 14.9817 13.6909 14.9817 12.3486C14.9817 11.1965 14.5794 9.77717 13.7789 8.63383C12.9699 7.47162 11.7014 6.53748 9.98782 6.53748ZM17.8354 6.53748C17.1291 6.53748 16.5755 6.97477 16.2331 7.49922C15.8863 8.02803 15.6951 8.7181 15.6951 9.44303C15.6951 10.168 15.8863 10.858 16.2331 11.3868C16.5755 11.9098 17.1291 12.3486 17.8354 12.3486C18.5417 12.3486 19.0953 11.9113 19.4377 11.3868C19.7844 10.858 19.9756 10.168 19.9756 9.44303C19.9756 8.7181 19.7844 8.02803 19.4377 7.49922C19.0953 6.97622 18.5417 6.53748 17.8354 6.53748Z"
                                                        fill="white" />
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto">
                                    <div class="pet-weight-wrapper wider">
                                        <p class="label">{{ $petSizeLabel }}</p>
                                        <div class="weight-toggle">
                                            <button type="button" class="weight-option large active" data-weight="large">
                                                <span>{{ $petSizeSelected }}</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="w-auto" style="margin-left: 2%;">
                                    <p class="label">Filter</p>
                                    <div class="filter-svg">
                                        <svg data-modal-open="{{ $tabs['space']['modalOpenId'] }}" class="filters-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                            <path
                                                d="M3 9.75C2.40326 9.75 1.83097 9.51295 1.40901 9.09099C0.987053 8.66903 0.75 8.09674 0.75 7.5C0.75 6.90326 0.987053 6.33097 1.40901 5.90901C1.83097 5.48705 2.40326 5.25 3 5.25M3 9.75C3.59674 9.75 4.16903 9.51295 4.59099 9.09099C5.01295 8.66903 5.25 8.09674 5.25 7.5C5.25 6.90326 5.01295 6.33097 4.59099 5.90901C4.16903 5.48705 3.59674 5.25 3 5.25M3 9.75V18.75M3 5.25V0.75M9.75 16.5C9.15326 16.5 8.58097 16.2629 8.15901 15.841C7.73705 15.419 7.5 14.8467 7.5 14.25C7.5 13.6533 7.73705 13.081 8.15901 12.659C8.58097 12.2371 9.15326 12 9.75 12M9.75 16.5C10.3467 16.5 10.919 16.2629 11.341 15.841C11.7629 15.419 12 14.8467 12 14.25C12 13.6533 11.7629 13.081 11.341 12.659C10.919 12.2371 10.3467 12 9.75 12M9.75 16.5V18.75M9.75 12V0.75M16.5 6.375C15.9033 6.375 15.331 6.13795 14.909 5.71599C14.4871 5.29403 14.25 4.72174 14.25 4.125C14.25 3.52826 14.4871 2.95597 14.909 2.53401C15.331 2.11205 15.9033 1.875 16.5 1.875M16.5 6.375C17.0967 6.375 17.669 6.13795 18.091 5.71599C18.5129 5.29403 18.75 4.72174 18.75 4.125C18.75 3.52826 18.5129 2.95597 18.091 2.53401C17.669 2.11205 17.0967 1.875 16.5 1.875M16.5 6.375V18.75M16.5 1.875V0.75"
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