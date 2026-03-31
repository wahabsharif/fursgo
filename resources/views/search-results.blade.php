{{-- resources/views/search-results.blade.php --}}
@extends('layouts.app')

@section('content')

{{-- filter modal --}}
<x-ui.filter-modal modal-id="groomModal" variant="groom" />
<x-ui.filter-modal modal-id="spaceModal" variant="space" />

{{-- filters section --}}
<x-ui.filter-section
    groom-modal-id="groomModal"
    space-modal-id="spaceModal"
    active-tab="groomer" />

<div class="groomer-tab-content main-tab-content" id="groomer">
    <section class="tabs section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="outer-tab-div d-flex align-items-center">
                        <div class="heading-count d-flex align-items-center">
                            <h1 class="heading">Groomer Results</h1>
                            <span class="count">25</span>
                        </div>

                        <div class="groomer-tabs text-center">
                            <a data-tab="groomer-calendar-view" class="tablinks active">Calendar View</a>
                            <a data-tab="groomer-map-view" class="tablinks">Map View</a>
                            <a data-tab="groomer-list-view" class="tablinks">List View</a>
                        </div>
                    </div>
                </div>

                <x-ui.groomer-venue-sort-options />

                <div data-tab-content="groomer-calendar-view" class="tabcontent">
                    <x-ui.calendar-view />

                    <div class="container">
                        <hr style="border-top: 1px solid #DFDFDF;">
                    </div>

                    <x-ui.groomer-tab-card-view :groomers="$groomers" />
                </div>

                <div data-tab-content="groomer-map-view" class="tabcontent" style="display: none;">
                    <x-ui.groomer-tab-map-view />
                </div>

                <div data-tab-content="groomer-list-view" class="tabcontent" style="display: none;">
                    <x-ui.groomer-tab-list-view
                        :groomers="$groomers"
                        :showLoadMore="true"
                        loadMoreText="Show More Groomers"
                        loadMoreUrl="#" />
                </div>
            </div>
        </div>
    </section>
</div>

<div class="space-tab-content main-tab-content" id="space" style="display: none;">
    <section class="tabs section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="outer-tab-div d-flex align-items-center">
                        <div class="heading-count d-flex align-items-center">
                            <h1 class="heading">Space Results</h1>
                            <span class="count">25</span>
                        </div>

                        <div class="groomer-tabs text-center">
                            <a data-tab="space-calendar-view" class="tablinks active">Calendar View</a>
                            <a data-tab="space-map-view" class="tablinks">Map View</a>
                            <a data-tab="space-list-view" class="tablinks">List View</a>
                        </div>
                    </div>
                </div>

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
                    crossIcon="cross.svg" />
                <div data-tab-content="space-calendar-view" class="tabcontent">
                    <x-ui.calendar-view />

                    <div class="container">
                        <hr style="border-top: 1px solid #DFDFDF;">
                    </div>

                    <x-ui.space-tab-card-view
                        :spaces="$spaces"
                        :showLoadMore="true"
                        loadMoreText="Show More Spaces"
                        loadMoreUrl="#" />
                </div>

                <div data-tab-content="space-map-view" class="tabcontent" style="display: none;">
                    <x-ui.space-tab-map-view :spaces="$spaces" />
                </div>

                <div data-tab-content="space-list-view" class="tabcontent" style="display: none;">
                    <x-ui.space-tab-list-view
                        :spaces="$spaces"
                        :showLoadMore="true"
                        loadMoreUrl="#" />
                </div>
            </div>
        </div>
    </section>
</div>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/responsive.css') }}">
<link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
<link rel="stylesheet" href="{{ asset('css/media_query.css') }}">
<link rel="stylesheet" href="{{ asset('css/customer_journey.css') }}">
<link rel="stylesheet" href="{{ asset('css/common.css') }}">
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('script')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script src="{{ asset('js/customer_journey.js') }}"></script>

<script>
    function setupFilterModal(modalId, selectedSectionId) {
        const modal = document.getElementById(modalId);
        const applyBtn = modal?.querySelector('.modal-footer-btn.apply');
        const selectedSection = document.getElementById(selectedSectionId);

        if (!modal || !applyBtn || !selectedSection) return;

        applyBtn.addEventListener('click', () => {
            const checkedItems = modal.querySelectorAll('.filter-options-section input[type="checkbox"]:checked');
            const uncheckedItems = modal.querySelectorAll('.filter-options-section input[type="checkbox"]:not(:checked)');

            uncheckedItems.forEach(item => {
                const value = item.value;
                const existingItem = [...selectedSection.querySelectorAll('.selected-item')]
                    .find(div => div.querySelector('p')?.textContent === value);

                if (existingItem) existingItem.remove();
            });

            checkedItems.forEach(item => {
                const value = item.value;

                const alreadyAdded = [...selectedSection.querySelectorAll('.selected-item p')]
                    .some(p => p.textContent === value);

                if (alreadyAdded) return;

                const div = document.createElement('div');
                div.className = 'selected-item d-flex align-items-center';
                div.innerHTML = `
                        <p>${value}</p>
                        <img src="{{ asset('icons/cross.svg') }}" class="cross svg" alt="remove">
                    `;

                div.querySelector('.cross').addEventListener('click', () => {
                    div.remove();
                    item.checked = false;
                });

                selectedSection.appendChild(div);
            });

            modal.style.display = 'none';
        });

        const closeBtn = modal.querySelector('.modal-close');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }
    }

    setupFilterModal('groomModal', 'groomerSelectedSection');
    setupFilterModal('spaceModal', 'spaceSelectedSection');
</script>
@endpush
