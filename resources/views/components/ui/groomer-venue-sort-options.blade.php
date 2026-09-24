{{-- Groomer venue + sort. Values match card tags used by customer_journey.js --}}
@props([
    'selectedFilters' => [],
    'venues' => [
        ['label' => 'Salons', 'value' => 'Salons'],
        ['label' => "Groomer's studio", 'value' => "Groomer's studio"],
        ['label' => 'Homevisit', 'value' => 'Homevisit'],
        ['label' => 'Visiting Groomers', 'value' => 'Visiting Groomers'],
        ['label' => 'Mobile Station', 'value' => 'Mobile Station'],
    ],
    'sortOptions' => [
        ['label' => 'Recommended (default)', 'value' => 'default', 'checked' => true],
        ['label' => 'Distance', 'value' => 'distance'],
        ['label' => 'Lowest price', 'value' => 'lowest_price'],
        ['label' => 'Soonest available', 'value' => 'soonest_available'],
    ],
])

<div class="col-lg-12 section-gap">
    <div class="selection-box d-flex justify-content-between">
        <div class="selected-item-section d-flex align-items-center flex-wrap" id="groomerSelectedSection"
            style="width: 100%; max-width: 75%;">
            @foreach ($selectedFilters as $filter)
                <div class="selected-item d-flex align-items-center">
                    <p>{{ $filter }}</p>
                    <img src="{{ asset('icons/cross.svg') }}" class="cross svg" alt="">
                </div>
            @endforeach
        </div>

        <div class="venu-sorting-section d-flex">
            <div class="venue-selection">
                Groomer Venue
                &nbsp;
                <img src="{{ asset('icons/filter-arrow-down.svg') }}" class="svg" alt="">
                <div class="venue-list">
                    <div class="venu dropdown">
                        <ul>
                            @foreach ($venues as $venue)
                                <li>
                                    <label>
                                        <span class="option-text">{{ $venue['label'] }}</span>
                                        <input type="checkbox" name="groomer-venue[]" value="{{ $venue['value'] }}"
                                            @checked($venue['checked'] ?? false)>
                                        <span class="check-circle"></span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <div class="sort-by">
                Sort
                &nbsp;
                <img src="{{ asset('icons/filter-arrow-down.svg') }}" class="svg" alt="">
                <div class="sort-by-filter">
                    <div class="sort dropdown">
                        <ul>
                            @foreach ($sortOptions as $sort)
                                <li>
                                    <label>
                                        <span class="option-text">{{ $sort['label'] }}</span>
                                        <input type="radio" name="groomer-sort" value="{{ $sort['value'] }}"
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
