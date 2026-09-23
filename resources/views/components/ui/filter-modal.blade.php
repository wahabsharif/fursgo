{{--

===== usage example =====

<x-ui.filter-modal modal-id="groomModal" variant="groom" />
<x-ui.filter-modal modal-id="spaceModal" variant="space" />

--}}

@props([
    'modalId' => 'groomModal',
    'variant' => 'groom', // groom | space
    'priceValue' => 75,
    'priceMax' => 95,
])

@php
$groomSections = [
    [
        'title' => 'Housing Conditions',
        'name' => 'home_condition[]',
        'titleClass' => 'modal-title mt-3 mb-2',
        'items' => [
            ['label' => 'Fenced yard', 'value' => 'Fenced yard'],
            ['label' => 'No other pets', 'value' => 'No other pets'],
            ['label' => 'No children', 'value' => 'No children'],
        ],
    ],
    [
        'title' => 'Other main service',
        'name' => 'main-service[]',
        'titleClass' => 'modal-title mb-2',
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
        'titleClass' => 'modal-title mb-2',
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
        'titleClass' => 'modal-title mb-2',
        'items' => [
            ['label' => 'Accepts non-neutered pets', 'value' => 'Accepts non-neutered pets', 'checked' => true],
        ],
    ],
    [
        'title' => 'Extras',
        'name' => 'extras[]',
        'titleClass' => 'modal-title mb-2',
        'items' => [
            ['label' => 'Bathing', 'value' => 'Bathing', 'checked' => true],
            ['label' => 'First-aid certified', 'value' => 'First-aid certified'],
        ],
    ],
    [
        'title' => 'Space Type',
        'name' => 'space-type[]',
        'titleClass' => 'modal-title mb-2',
        'items' => [
            ['label' => 'Shared space', 'value' => 'Shared space', 'checked' => true],
            ['label' => 'Entire Space just for you', 'value' => 'Entire Space just for you'],
        ],
    ],
];

$spaceSections = [
    [
        'title' => 'Amenities',
        'name' => 'amenities[]',
        'titleClass' => 'modal-title mt-3 mb-2',
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
        'name' => 'housing-conditions[]',
        'titleClass' => 'modal-title mb-2',
        'items' => [
            ['label' => 'Fenced yard', 'value' => 'Fenced yard', 'checked' => true],
            ['label' => 'No other pets', 'value' => 'No other pets'],
            ['label' => 'No children', 'value' => 'No children'],
        ],
    ],
    [
        'title' => 'Space Type',
        'name' => 'space-type[]',
        'titleClass' => 'modal-title mb-2',
        'items' => [
            ['label' => 'Shared space', 'value' => 'Shared space', 'checked' => true],
            ['label' => 'Entire space just for you', 'value' => 'Entire space just for you'],
        ],
    ],
    [
        'title' => 'Accepts non-neutered pets',
        'name' => 'non-neutered[]',
        'titleClass' => 'modal-title mb-2',
        'items' => [
            ['label' => 'Accepts non-neutered pets', 'value' => 'Accepts non-neutered pets', 'checked' => true],
        ],
    ],
    [
        'title' => 'Suitable service',
        'name' => 'suitable-service[]',
        'titleClass' => 'modal-title mb-2',
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
        'titleClass' => 'modal-title mb-2',
        'items' => [
            ['label' => 'Bathing', 'value' => 'Bathing', 'checked' => true],
            ['label' => 'First-aid certified', 'value' => 'First-aid certified'],
        ],
    ],
];

$sections = $variant === 'space' ? $spaceSections : $groomSections;
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

        <h2 class="modal-title mt-5">Price Range</h2>
        <div class="range-slider">
            <span class="output"></span>
            <span class="full-range"></span>
            <span class="incl-range"></span>
            <input type="range" name="rangeOne" min="0" max="{{ $priceMax }}" step="1" value="{{ $priceValue }}">
            <span class="max-price fs-14-600-f-color">£{{ $priceMax }}</span>
        </div>

        @foreach ($sections as $index => $section)
            <div class="filter-options-section dropdown {{ $index === 0 ? 'mt-4' : 'mt-3' }}">
                <h2 class="{{ $section['titleClass'] }}">{{ $section['title'] }}</h2>
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
            <button type="button" class="modal-footer-btn clear">Clear All</button>
            <button type="button" class="modal-footer-btn apply">Apply</button>
        </div>
    </div>
</div>
