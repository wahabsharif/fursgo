{{--

============ usage ============

<x-groomer-venue-sort-options />

<x-groomer-venue-sort-options
    :selectedFilters="['Top Rated', 'Homevisit']"
    :venues="[
        ['label' => 'Salons', 'value' => 'salons'],
        ['label' => 'Mobile Station', 'value' => 'mobile', 'checked' => true],
    ]"
    :sortOptions="[
        ['label' => 'Distance', 'value' => 'distance'],
        ['label' => 'Lowest price', 'value' => 'lowest_price', 'checked' => true],
    ]"
/>

--}}


@props([
'selectedFilters' => ['Top Rated', 'Mobile Station'],
'venues' => [
['label' => 'Salons', 'value' => 'salons', 'checked' => true],
['label' => 'Groomer’s studio', 'value' => 'studio'],
['label' => 'Homevisit', 'value' => 'homevisit'],
['label' => 'Visiting Groomers', 'value' => 'visiting'],
['label' => 'Mobile Station', 'value' => 'mobile'],
],
'sortOptions' => [
['label' => 'Recommended (default)', 'value' => 'default', 'checked' => true],
['label' => 'Distance', 'value' => 'distance'],
['label' => 'Lowest price', 'value' => 'lowest_price'],
['label' => 'Soonest available', 'value' => 'soonest_available'],
]
])

<div class="col-lg-12 section-gap">
    <div class="selection-box d-flex justify-content-between">

        {{-- Selected Filters --}}
        <div class="selected-item-section d-flex align-items-center flex-wrap" style="width: 100%; max-width: 75%;">
            @foreach($selectedFilters as $filter)
            <div class="selected-item d-flex align-items-center">
                <p>{{ $filter }}</p>
                <img src="{{ asset('assets/icons/cross.svg') }}" class="cross svg" alt="">
            </div>
            @endforeach
        </div>

        <div class="venu-sorting-section d-flex">

            {{-- Groomer Venue --}}
            <div class="venue-selection">
                Groomer Venue
                <img src="{{ asset('assets/icons/filter-arrow-down.svg') }}" class="svg" alt="">

                <div class="venue-list">
                    <div class="venu dropdown">
                        <ul>
                            @foreach($venues as $venue)
                            <li>
                                <label>
                                    <span class="option-text">
                                        {{ $venue['label'] }}

                                        {{-- Tooltip only for Visiting Groomers --}}
                                        @if($venue['label'] === 'Visiting Groomers')
                                        <span class="tooltip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12">
                                                <circle cx="6" cy="6" r="6" fill="#9D9B98" />
                                            </svg>
                                            <span class="tooltiptext">
                                                Visiting Groomers provide mobile services and don’t have a grooming space.
                                            </span>
                                        </span>
                                        @endif
                                    </span>

                                    <input type="checkbox"
                                        name="groomer-venue[]"
                                        value="{{ $venue['value'] }}"
                                        @checked($venue['checked'] ?? false)>
                                    <span class="check-circle"></span>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Sort Options --}}
            <div class="sort-by">
                Sort
                <img src="{{ asset('assets/icons/filter-arrow-down.svg') }}" class="svg" alt="">

                <div class="sort-by-filter">
                    <div class="sort dropdown">
                        <ul>
                            @foreach($sortOptions as $sort)
                            <li>
                                <label>
                                    <span class="option-text">{{ $sort['label'] }}</span>
                                    <input type="checkbox"
                                        name="sort-by[]"
                                        value="{{ $sort['value'] }}"
                                        @checked($sort['checked'] ?? false)>
                                    <span class="check-circle"></span>
                                </label>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
