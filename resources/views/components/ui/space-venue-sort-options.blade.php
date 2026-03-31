{{--

===== Usage Example =====
<x-ui.space-venue-sort-options
    :selectedFilters="['Top Rated', 'Garden / Shed']"
    :venueOptions="[
        ['label' => 'Private rooms', 'value' => 'private_rooms'],
        ['label' => 'Salon', 'value' => 'salon'],
        ['label' => 'Mobile station', 'value' => 'mobile_station'],
        ['label' => 'Garden / Shed', 'value' => 'garden_shed'],
        ['label' => 'Others', 'value' => 'others'],
    ]"
    :sortOptions="[
        ['label' => 'Recommended (default)', 'value' => 'recommended'],
        ['label' => 'Distance', 'value' => 'distance'],
        ['label' => 'Lowest price', 'value' => 'lowest_price'],
        ['label' => 'Soonest available', 'value' => 'soonest'],
    ]"
    defaultVenue="private_rooms"
    defaultSort="recommended"
    filterIcon="fire.svg"
    crossIcon="cross.svg"
/>

--}}
@props([
'selectedFilters' => [], // Array of selected filter labels (e.g. ['Top Rated', 'Garden / Shed'])
'venueOptions' => [ // Venue dropdown options
['label' => 'Private rooms', 'value' => 'private_rooms'],
['label' => 'Salon', 'value' => 'salon'],
['label' => 'Mobile station', 'value' => 'mobile_station'],
['label' => 'Garden / Shed', 'value' => 'garden_shed'],
['label' => 'Others', 'value' => 'others'],
],
'sortOptions' => [ // Sort dropdown options
['label' => 'Recommended (default)', 'value' => 'recommended'],
['label' => 'Distance', 'value' => 'distance'],
['label' => 'Lowest price', 'value' => 'lowest_price'],
['label' => 'Soonest available', 'value' => 'soonest'],
],
'defaultVenue' => 'private_rooms', // Initially selected venue value
'defaultSort' => 'recommended', // Initially selected sort value
'filterIcon' => 'fire.svg', // Icon for "Top Rated" filter (customizable)
'crossIcon' => 'cross.svg', // Icon for removing filter
])

@php
// Helper to get label by value
$getVenueLabel = function($value) use ($venueOptions) {
foreach ($venueOptions as $option) {
if ($option['value'] == $value) return $option['label'];
}
return ucfirst(str_replace('_', ' ', $value));
};
$getSortLabel = function($value) use ($sortOptions) {
foreach ($sortOptions as $option) {
if ($option['value'] == $value) return $option['label'];
}
return $sortOptions[0]['label'] ?? 'Recommended';
};
@endphp

<div class="col-lg-12 section-gap">
    <div class="selection-box d-flex justify-content-between">
        {{-- Selected Filters Section --}}
        <div class="selected-item-section d-flex align-items-center flex-wrap" id="spaceSelectedSection" style="width: 100%; max-width: 75%;">
            @foreach($selectedFilters as $filter)
            <div class="selected-item d-flex align-items-center" data-filter-value="{{ $filter }}">
                @if($filter === 'Top Rated')
                <img src="{{ asset("assets/icons/{$filterIcon}") }}" class="svg" alt="">
                @endif
                <p>{{ $filter }}</p>
                <img src="{{ asset("assets/icons/{$crossIcon}") }}" class="cross svg" alt="Remove">
            </div>
            @endforeach
        </div>

        {{-- Right side: Venue & Sort Dropdowns --}}
        <div class="venu-sorting-section d-flex">
            {{-- Venue Dropdown --}}
            <div class="venue-selection" data-dropdown="venue">
                Space Venue
                &nbsp;
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none">
                    <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#FBAC83" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="venue-list">
                    <div class="venu dropdown">
                        <ul>
                            @foreach($venueOptions as $option)
                            <li data-venue-value="{{ $option['value'] }}" class="{{ $defaultVenue == $option['value'] ? 'active' : '' }}">
                                <span class="option-text">{{ $option['label'] }}</span>
                                <span class="radio-circle"></span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            {{-- Sort Dropdown --}}
            <div class="sort-by" data-dropdown="sort">
                Sort
                &nbsp;
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none">
                    <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#FBAC83" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <div class="sort-by-filter">
                    <div class="sort dropdown">
                        <ul>
                            @foreach($sortOptions as $option)
                            <li data-sort-value="{{ $option['value'] }}" class="{{ $defaultSort == $option['value'] ? 'active' : '' }}">
                                <span class="option-text">{{ $option['label'] }}</span>
                                <span class="radio-circle"></span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@once
@push('script')
<script>
    (function() {
        // Dropdown toggle logic
        const dropdowns = document.querySelectorAll('[data-dropdown]');
        dropdowns.forEach(dropdown => {
            const trigger = dropdown;
            const menu = dropdown.querySelector('.dropdown, .venue-list .dropdown, .sort-by-filter .dropdown');
            if (menu) {
                trigger.addEventListener('click', (e) => {
                    e.stopPropagation();
                    // Close other dropdowns
                    dropdowns.forEach(d => {
                        if (d !== dropdown) {
                            const otherMenu = d.querySelector('.dropdown, .venue-list .dropdown, .sort-by-filter .dropdown');
                            if (otherMenu) otherMenu.classList.remove('show');
                        }
                    });
                    menu.classList.toggle('show');
                });
            }
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function() {
            dropdowns.forEach(dropdown => {
                const menu = dropdown.querySelector('.dropdown, .venue-list .dropdown, .sort-by-filter .dropdown');
                if (menu) menu.classList.remove('show');
            });
        });

        // Handle venue selection
        const venueItems = document.querySelectorAll('.venue-selection .dropdown li');
        venueItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const venueValue = item.dataset.venueValue;
                // Remove active class from siblings
                item.parentElement.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                item.classList.add('active');
                // Emit custom event or call a callback (you can extend this)
                const event = new CustomEvent('venueChanged', {
                    detail: {
                        venue: venueValue
                    }
                });
                document.dispatchEvent(event);
            });
        });

        // Handle sort selection
        const sortItems = document.querySelectorAll('.sort-by .dropdown li');
        sortItems.forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const sortValue = item.dataset.sortValue;
                item.parentElement.querySelectorAll('li').forEach(li => li.classList.remove('active'));
                item.classList.add('active');
                const event = new CustomEvent('sortChanged', {
                    detail: {
                        sort: sortValue
                    }
                });
                document.dispatchEvent(event);
            });
        });

        // Handle removal of filter chips
        const removeButtons = document.querySelectorAll('.selected-item .cross');
        removeButtons.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const selectedItem = btn.closest('.selected-item');
                const filterValue = selectedItem.dataset.filterValue;
                selectedItem.remove();
                const event = new CustomEvent('filterRemoved', {
                    detail: {
                        filter: filterValue
                    }
                });
                document.dispatchEvent(event);
            });
        });
    })();
</script>
<style>
    /* Basic dropdown styling - adjust as needed */
    .dropdown,
    .venue-list .dropdown,
    .sort-by-filter .dropdown {
        display: none;
        position: absolute;
        background: white;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        z-index: 100;
        margin-top: 4px;
    }

    .dropdown.show,
    .venue-list .dropdown.show,
    .sort-by-filter .dropdown.show {
        display: block;
    }

    .venue-selection,
    .sort-by {
        position: relative;
        cursor: pointer;
    }

    .selected-item .cross {
        cursor: pointer;
        margin-left: 6px;
    }
</style>
@endpush
@endonce
