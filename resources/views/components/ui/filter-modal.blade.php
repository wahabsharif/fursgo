{{--

===== usage example =====

<x-filter-modal modal-id="groomModal" variant="groom" />

<x-filter-modal modal-id="spaceModal" variant="space" />

<x-filter-modal
    modal-id="groomModal"
    variant="groom"
    selected-text="Full Groom"
    selected-value="full-groom"
/>

<x-filter-modal
    modal-id="spaceModal"
    variant="space"
    selected-text="Mobile Station"
    selected-value="mobile-station"
/>

--}}


@props([
'modalId' => 'groomModal',
'variant' => 'groom', // groom | space

'searchValue' => 'London, NW3 1AA',

'selectedValue' => null,
'selectedText' => null,

'selectedPetType' => 'Other',
'selectedPetSize' => 'Large 19+ kg',

'priceValue' => 75,
'priceMax' => 95,
])

@php
$uid = preg_replace('/[^A-Za-z0-9_-]/', '-', $modalId);

$groomSelectOptions = [
['value' => 'full-groom', 'label' => 'Full Groom'],
['value' => 'face-trim-only', 'label' => 'Face Trim Only'],
['value' => 'tail-trim-only', 'label' => 'Tail Trim Only'],
['value' => 'bath-and-wash', 'label' => 'Bath & Brush'],
['value' => 'nail-trim', 'label' => 'Nail Trim'],
];

$spaceSelectOptions = [
['value' => 'private-rooms', 'label' => 'Private Rooms'],
['value' => 'salon', 'label' => 'Salon'],
['value' => 'mobile-station', 'label' => 'Mobile Station'],
['value' => 'garden-shed', 'label' => 'Garden / Shed'],
['value' => 'others', 'label' => 'Others'],
];

$groomSections = [
[
'title' => 'Home Conditions',
'name' => 'home_condition[]',
'items' => [
['label' => 'Fenced yard', 'value' => 'Fenced yard'],
['label' => 'No other pets', 'value' => 'No other pets'],
['label' => 'No children', 'value' => 'No children'],
],
],
[
'title' => 'Other main service',
'name' => 'main-service[]',
'items' => [
['label' => 'Full Groom (bath, dry, haircut)', 'value' => 'Full Groom (bath, dry, haircut)', 'checked' => true],
['label' => 'Face Trim Only', 'value' => 'Face Trim Only'],
['label' => 'Tail Trim Only', 'value' => 'Tail Trim Only'],
['label' => 'Bath & Brush', 'value' => 'Bath & Brush'],
['label' => 'Nail Trim', 'value' => 'Nail Trim'],
['label' => 'Ear Cleaning', 'value' => 'Ear Cleaning'],
['label' => 'Luxury Spa', 'value' => 'Luxury Spa'],
],
],
[
'title' => 'Add-on',
'name' => 'addon[]',
'items' => [
['label' => 'Flea & Tick Treatment', 'value' => 'Flea & Tick Treatment', 'checked' => true],
['label' => 'Deep Conditioning Masky', 'value' => 'Deep Conditioning Masky'],
['label' => 'Hypoallergenic Shampoo Upgrade', 'value' => 'Hypoallergenic Shampoo Upgrade'],
['label' => 'Shed-Control Shampoo', 'value' => 'Shed-Control Shampoo'],
['label' => 'Tear-Stain Treatment', 'value' => 'Tear-Stain Treatment'],
['label' => 'Deodorising Treatment', 'value' => 'Deodorising Treatment'],
['label' => 'Coat Shine Spray', 'value' => 'Coat Shine Spray'],
['label' => 'Anti-Itch Treatment', 'value' => 'Anti-Itch Treatment'],
['label' => 'Breath Freshner Gel', 'value' => 'Breath Freshner Gel'],
['label' => 'Nail Grinding', 'value' => 'Nail Grinding'],
['label' => 'Soft-Claws / Nail Caps Application', 'value' => 'Soft-Claws / Nail Caps Application'],
['label' => 'Coat Colour Enhancing Shampoo', 'value' => 'Coat Colour Enhancing Shampoo'],
['label' => 'Premium Fragrance Upgrade', 'value' => 'Premium Fragrance Upgrade'],
['label' => 'Fast-Dry Service (express grooming)', 'value' => 'Fast-Dry Service (express grooming)'],
['label' => 'Paw Fur Shaping', 'value' => 'Paw Fur Shaping'],
],
],
[
'title' => 'Accepts non-neutered pets',
'name' => 'non-neutered[]',
'items' => [
['label' => 'Accepts non-neutered pets', 'value' => 'Accepts non-neutered pets', 'checked' => true],
],
],
[
'title' => 'Extras',
'name' => 'extras[]',
'items' => [
['label' => 'Bathing', 'value' => 'Bathing', 'checked' => true],
['label' => 'First-aid certified', 'value' => 'First-aid certified'],
],
],
[
'title' => 'Space Type',
'name' => 'space-type[]',
'items' => [
['label' => 'Shared space', 'value' => 'Shared space', 'checked' => true],
['label' => 'Entire Space just for you', 'value' => 'Entire Space just for you'],
],
],
];

$spaceSections = [
[
'title' => 'Amenities',
'name' => 'amenities',
'items' => [
['label' => 'Bath', 'value' => 'Bath', 'checked' => true],
['label' => 'Table', 'value' => 'Table'],
['label' => 'Dryer', 'value' => 'Dryer'],
['label' => 'Towels', 'value' => 'Towels'],
['label' => 'Parking', 'value' => 'Parking'],
['label' => 'Wi-Fi', 'value' => 'Wi-Fi'],
],
],
[
'title' => 'Housing Conditions',
'name' => 'housing-conditions',
'items' => [
['label' => 'Fenced yard', 'value' => 'Fenced yard', 'checked' => true],
['label' => 'No other pets', 'value' => 'No other pets'],
['label' => 'No children', 'value' => 'No children'],
],
],
[
'title' => 'Space Type',
'name' => 'space-type[]',
'items' => [
['label' => 'Shared space', 'value' => 'Shared space', 'checked' => true],
['label' => 'Entire space just for you', 'value' => 'Entire space just for you'],
],
],
[
'title' => 'Accepts non-neutered pets',
'name' => 'non-neutered[]',
'items' => [
['label' => 'Accepts non-neutered pets', 'value' => 'Accepts non-neutered pets', 'checked' => true],
],
],
[
'title' => 'Suitable service',
'name' => 'suitable-service',
'items' => [
['label' => 'Full Groom (bath, dry, haircut)', 'value' => 'Full Groom (bath, dry, haircut)', 'checked' => true],
['label' => 'Face Trim Only', 'value' => 'Face Trim Only'],
['label' => 'Tail Trim Only', 'value' => 'Tail Trim Only'],
['label' => 'Bath & Brush', 'value' => 'Bath & Brush'],
['label' => 'Nail Trim', 'value' => 'Nail Trim'],
['label' => 'Ear Cleaning', 'value' => 'Ear Cleaning'],
['label' => 'Luxury Spa', 'value' => 'Luxury Spa'],
],
],
[
'title' => 'Extras',
'name' => 'extras[]',
'items' => [
['label' => 'Bathing', 'value' => 'Bathing', 'checked' => true],
['label' => 'First-aid certified', 'value' => 'First-aid certified'],
],
],
];

$selectLabel = $variant === 'space' ? 'Space Type' : 'Service Type';
$selectOptions = $variant === 'space' ? $spaceSelectOptions : $groomSelectOptions;
$sections = $variant === 'space' ? $spaceSections : $groomSections;

$defaultSelectedText = $variant === 'space' ? 'Mobile Station' : 'Full Groom';
$defaultSelectedValue = $variant === 'space' ? 'mobile-station' : 'full-groom';

$selectedText = $selectedText ?? $defaultSelectedText;
$selectedValue = $selectedValue ?? $defaultSelectedValue;
@endphp

<div id="{{ $modalId }}" class="modal" data-filter-modal="{{ $modalId }}">
    <div class="modal-dialog mb-5">
        <button type="button" class="modal-close" aria-label="Close">&times;</button>

        <h2 class="modal-filter-svg d-flex align-items-center modal-title">
            Filter
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                <path
                    d="M3 9.75C2.40326 9.75 1.83097 9.51295 1.40901 9.09099C0.987053 8.66903 0.75 8.09674 0.75 7.5C0.75 6.90326 0.987053 6.33097 1.40901 5.90901C1.83097 5.48705 2.40326 5.25 3 5.25M3 9.75C3.59674 9.75 4.16903 9.51295 4.59099 9.09099C5.01295 8.66903 5.25 8.09674 5.25 7.5C5.25 6.90326 5.01295 6.33097 4.59099 5.90901C4.16903 5.48705 3.59674 5.25 3 5.25M3 9.75V18.75M3 5.25V0.75M9.75 16.5C9.15326 16.5 8.58097 16.2629 8.15901 15.841C7.73705 15.419 7.5 14.8467 7.5 14.25C7.5 13.6533 7.73705 13.081 8.15901 12.659C8.58097 12.2371 9.15326 12 9.75 12M9.75 16.5C10.3467 16.5 10.919 16.2629 11.341 15.841C11.7629 15.419 12 14.8467 12 14.25C12 13.6533 11.7629 13.081 11.341 12.659C10.919 12.2371 10.3467 12 9.75 12M9.75 16.5V18.75M9.75 12V0.75M16.5 6.375C15.9033 6.375 15.331 6.13795 14.909 5.71599C14.4871 5.29403 14.25 4.72174 14.25 4.125C14.25 3.52826 14.4871 2.95597 14.909 2.53401C15.331 2.11205 15.9033 1.875 16.5 1.875M16.5 6.375C17.0967 6.375 17.669 6.13795 18.091 5.71599C18.5129 5.29403 18.75 4.72174 18.75 4.125C18.75 3.52826 18.5129 2.95597 18.091 2.53401C17.669 2.11205 17.0967 1.875 16.5 1.875M16.5 6.375V18.75M16.5 1.875V0.75"
                    stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        </h2>

        <div class="modal-input-fields d-flex align-items-center flex-wrap mt-5">
            <div class="w-auto">
                <div class="search-input">
                    <p class="label">Search Groomer</p>
                    <input type="text" value="{{ $searchValue }}">
                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="gray" viewBox="0 0 16 16">
                        <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85zm-5.242.656a5 5 0 1 1 0-10 5 5 0 0 1 0 10z" />
                    </svg>
                </div>
            </div>

            <div class="w-auto">
                <div class="service-type-select">
                    <p class="label">{{ $selectLabel }}</p>
                    <div class="custom-select custom-select-streched">
                        <div class="select-trigger">
                            <span class="selected-text">{{ $selectedText }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                <path d="M13.8737 0.5L7.13022 7.24344L0.499976 0.613201" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>

                        <ul class="select-options">
                            @foreach ($selectOptions as $option)
                            <li data-value="{{ $option['value'] }}">{{ $option['label'] }}</li>
                            @endforeach
                        </ul>

                        <input type="hidden" name="{{ $variant === 'space' ? 'spaceType' : 'serviceType' }}" value="{{ $selectedValue }}">
                    </div>
                </div>
            </div>

            <div class="w-auto">
                <div class="datetime-wrapper" id="{{ $uid }}-datetime">
                    <div class="field-group">
                        <p class="label">Date</p>
                        <div class="field date streched" id="{{ $uid }}-dateField">
                            <div class="input-row streched" tabindex="0" role="button" aria-haspopup="dialog" aria-expanded="false">
                                <input class="fake-input" id="{{ $uid }}-dateInput" readonly placeholder="02/11/25" aria-label="Date input" />
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                    <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>

                            <div class="popover" id="{{ $uid }}-datePopover" data-type="date">
                                <div style="display:flex;flex-direction: column;">
                                    <div class="panel calendar">
                                        <div class="month-nav">
                                            <button type="button" id="{{ $uid }}-prevMonth" title="Previous month" aria-label="Previous month">
                                                <svg class="chev rotate-left" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>

                                            <div id="{{ $uid }}-monthLabel">November 2025</div>

                                            <button type="button" id="{{ $uid }}-nextMonth" title="Next month" aria-label="Next month">
                                                <svg class="chev rotate-right" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M6 9l6 6 6-6" stroke="#444" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>

                                        <div class="weekday-row" id="{{ $uid }}-weekdayRow"></div>
                                        <div class="days-grid" id="{{ $uid }}-daysGrid"></div>
                                    </div>

                                    <div class="time-col">
                                        <div class="title">
                                            <div>Time</div>
                                        </div>
                                        <div class="time-list" id="{{ $uid }}-timeList" role="listbox" aria-label="Time options"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="field-group">
                        <p class="label">Time</p>
                        <div class="field time streched" id="{{ $uid }}-timeField">
                            <div class="input-row streched" tabindex="0" role="button" aria-haspopup="dialog" aria-expanded="false">
                                <input class="fake-input" id="{{ $uid }}-timeInput" readonly placeholder="13:00" aria-label="Time input" />
                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                    <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-auto">
                <div class="pet-type-wrapper">
                    <p class="label">Pet Type</p>
                    <div class="pet-toggle">
                        <button type="button" class="pet-option highlight" data-pet="{{ strtolower($selectedPetType) }}">
                            <span>{{ $selectedPetType }}</span>
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
                <div class="pet-weight-wrapper">
                    <p class="label">Pet Size</p>
                    <div class="weight-toggle">
                        <button type="button" class="weight-option large active" data-weight="large">
                            <span>{{ $selectedPetSize }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <h2 class="modal-title mt-3">Price Range</h2>
        <div class="range-slider">
            <span class="output"></span>
            <span class="full-range"></span>
            <span class="incl-range"></span>
            <input type="range" name="rangeOne" min="0" max="{{ $priceMax }}" step="1" value="{{ $priceValue }}">
            <span class="max-price">£{{ $priceMax }}</span>
        </div>

        @foreach ($sections as $section)
        <div class="filter-options-section dropdown mt-3">
            <h2 class="modal-title">{{ $section['title'] }}</h2>
            <ul>
                @foreach ($section['items'] as $item)
                <li>
                    <label>
                        <input
                            type="checkbox"
                            name="{{ $section['name'] }}"
                            value="{{ $item['value'] }}"
                            @checked($item['checked'] ?? false)>
                        <span class="check-circle"></span>
                        <span class="option-text">{{ $item['label'] }}</span>
                    </label>
                </li>
                @endforeach
            </ul>
        </div>
        @endforeach

        <div class="modal-footer mt-3">
            <button type="button" class="modal-footer-btn">Clear All</button>
            <button type="button" class="modal-footer-btn apply">Apply</button>
        </div>
    </div>
</div>
