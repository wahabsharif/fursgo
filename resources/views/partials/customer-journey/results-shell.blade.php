{{--
  Shared customer-journey results shell (search + unavailability variants).
  Props: $groomers, $spaces, $variant (results|location|time|specific),
         $entity (groomer|space), $focusTab (calendar|map|list), $activeMain (groomer|space)
--}}
@php
    $variant = $variant ?? 'results';
    $entity = $entity ?? 'groomer';
    $focusTab = $focusTab ?? 'calendar';
    $activeMain = $activeMain ?? 'groomer';

    $groomerCount = isset($groomers) ? $groomers->count() : 0;
    $spaceCount = isset($spaces) ? $spaces->count() : 0;

    $showBanner = in_array($variant, ['location', 'time'], true);
    $showSpecific = $variant === 'specific';

    $bannerHtml = match ($variant) {
        'location' => 'Sorry, <span class="not-found-message">your preferred location isn\'t available today, <br>please see below for '.($entity === 'space' ? 'spaces' : 'groomers').' in the nearest proximity!</span>',
        'time' => $entity === 'space'
            ? 'Sorry, <span class="not-found-message">your preferred time isn’t available, <br>please see below for next availabilities!</span>'
            : 'Sorry, <span class="not-found-message">your preferred time isn\'t available today, <br>please see below for next availabilities!</span>',
        default => null,
    };

    $specificLead = $entity === 'space'
        ? 'Sorry, <span class="not-found-message">your preferred space isn’t available at this time. </span>'
        : 'Sorry, <span class="not-found-message">your preferred groomer isn’t available at this time. </span>';
    $specificOther = $entity === 'space'
        ? 'Please view their other available times or select a different space for this timeslot.'
        : 'Please view their other available times or select a different groomer for this timeslot.';
    $specificTitle = $entity === 'space'
        ? 'Your selected space’s availability'
        : 'Your selected groomer’s availability';
@endphp

<x-ui.filter-modal modal-id="groomModal" variant="groom" />
<x-ui.filter-modal modal-id="spaceModal" variant="space" />

<x-ui.filter-section
    groom-modal-id="groomModal"
    space-modal-id="spaceModal"
    :active-tab="$activeMain"
/>

<div class="groomer-tab-content main-tab-content" id="groomer" style="display: {{ $activeMain === 'groomer' ? 'block' : 'none' }}">
    <section class="tabs section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="outer-tab-div d-flex align-items-center">
                        <div class="heading-count d-flex align-items-center">
                            <h1 class="heading">Groomer Results</h1>
                            <span class="count">{{ $groomerCount }}</span>
                        </div>

                        <div class="groomer-tabs text-center">
                            <a data-tab="groomer-calendar-view" class="tablinks {{ $focusTab === 'calendar' ? 'active' : '' }}">Calendar View</a>
                            <a data-tab="groomer-map-view" class="tablinks {{ $focusTab === 'map' ? 'active' : '' }}">Map View</a>
                            <a data-tab="groomer-list-view" class="tablinks {{ $focusTab === 'list' ? 'active' : '' }}">List View</a>
                        </div>
                    </div>
                </div>

                <x-ui.groomer-venue-sort-options />

                @if ($showBanner && $entity === 'groomer')
                    <div class="col-lg-12 d-flex align-items-center justify-content-center section-gap">
                        <p class="not-found-message">{!! $bannerHtml !!}</p>
                    </div>
                @endif

                <div data-tab-content="groomer-calendar-view" class="tabcontent" @style(['display: none' => $focusTab !== 'calendar'])>
                    @if ($showSpecific && $entity === 'groomer' && $focusTab === 'calendar')
                        @include('partials.customer-journey.specific-unavailable', [
                            'lead' => $specificLead,
                            'other' => $specificOther,
                            'title' => $specificTitle,
                            'kind' => 'groomer',
                            'selected' => $groomers->first(),
                        ])
                        <div class="section-divider" style="background-color: #DFDFDF"></div>
                        <h1 class="section-title mt-5">Other groomer’s availability at selected time</h1>
                    @endif

                    <x-ui.calendar-view />

                    <div class="section-divider" style="background-color: #DFDFDF"></div>

                    <x-ui.groomer-tab-card-view :groomers="$groomers" />
                </div>

                <div data-tab-content="groomer-map-view" class="tabcontent" @style(['display: none' => $focusTab !== 'map'])>
                    @if ($showSpecific && $entity === 'groomer' && $focusTab === 'map')
                        @include('partials.customer-journey.specific-unavailable', [
                            'lead' => $specificLead,
                            'other' => $specificOther,
                            'title' => $specificTitle,
                            'kind' => 'groomer',
                            'selected' => $groomers->first(),
                        ])
                        <div class="section-divider" style="background-color: #DFDFDF"></div>
                        <h1 class="section-title mt-5">Other groomer’s availability at selected time</h1>
                    @endif
                    <x-ui.groomer-tab-map-view :groomers="$groomers->take(3)" />
                </div>

                <div data-tab-content="groomer-list-view" class="tabcontent" @style(['display: none' => $focusTab !== 'list'])>
                    @if ($showSpecific && $entity === 'groomer' && $focusTab === 'list')
                        @include('partials.customer-journey.specific-unavailable', [
                            'lead' => $specificLead,
                            'other' => $specificOther,
                            'title' => $specificTitle,
                            'kind' => 'groomer',
                            'selected' => $groomers->first(),
                        ])
                        <div class="section-divider" style="background-color: #DFDFDF"></div>
                        <h1 class="section-title mt-5">Other groomer’s availability at selected time</h1>
                    @endif
                    <x-ui.groomer-tab-list-view :groomers="$groomers" :showLoadMore="true" loadMoreText="Load More"
                        loadMoreUrl="#" />
                </div>
            </div>
        </div>
    </section>
</div>

<div class="space-tab-content main-tab-content" id="space" style="display: {{ $activeMain === 'space' ? 'block' : 'none' }}">
    <section class="tabs section-gap">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="outer-tab-div d-flex align-items-center">
                        <div class="heading-count d-flex align-items-center">
                            <h1 class="heading">Space Results</h1>
                            <span class="count">{{ $spaceCount }}</span>
                        </div>

                        <div class="groomer-tabs text-center">
                            <a data-tab="space-calendar-view" class="tablinks {{ $focusTab === 'calendar' ? 'active' : '' }}">Calendar View</a>
                            <a data-tab="space-map-view" class="tablinks {{ $focusTab === 'map' ? 'active' : '' }}">Map View</a>
                            <a data-tab="space-list-view" class="tablinks {{ $focusTab === 'list' ? 'active' : '' }}">List View</a>
                        </div>
                    </div>
                </div>

                <x-ui.space-venue-sort-options />

                @if ($showBanner && $entity === 'space')
                    <div class="col-lg-12 d-flex align-items-center justify-content-center section-gap">
                        <p class="not-found-message">{!! $bannerHtml !!}</p>
                    </div>
                @endif

                <div data-tab-content="space-calendar-view" class="tabcontent" @style(['display: none' => $focusTab !== 'calendar'])>
                    @if ($showSpecific && $entity === 'space' && $focusTab === 'calendar')
                        @include('partials.customer-journey.specific-unavailable', [
                            'lead' => $specificLead,
                            'other' => $specificOther,
                            'title' => $specificTitle,
                            'kind' => 'space',
                            'selected' => $spaces->first(),
                        ])
                        <div class="section-divider" style="background-color: #DFDFDF"></div>
                        <h1 class="section-title mt-5">Other space’s availability at selected time</h1>
                    @endif

                    <x-ui.calendar-view />

                    <div class="section-divider" style="background-color: #DFDFDF"></div>

                    <x-ui.space-tab-card-view :spaces="$spaces" :showLoadMore="true" loadMoreText="Show More Spaces"
                        loadMoreUrl="#" />
                </div>

                <div data-tab-content="space-map-view" class="tabcontent" @style(['display: none' => $focusTab !== 'map'])>
                    @if ($showSpecific && $entity === 'space' && $focusTab === 'map')
                        @include('partials.customer-journey.specific-unavailable', [
                            'lead' => $specificLead,
                            'other' => $specificOther,
                            'title' => $specificTitle,
                            'kind' => 'space',
                            'selected' => $spaces->first(),
                        ])
                        <div class="section-divider" style="background-color: #DFDFDF"></div>
                        <h1 class="section-title mt-5">Other space’s availability at selected time</h1>
                    @endif
                    <x-ui.space-tab-map-view :spaces="$spaces" />
                </div>

                <div data-tab-content="space-list-view" class="tabcontent" @style(['display: none' => $focusTab !== 'list'])>
                    @if ($showSpecific && $entity === 'space' && $focusTab === 'list')
                        @php
                            $selectedSpace = $spaces->first(fn ($s) => ($s['name'] ?? '') === 'The Garden Grooming Spot')
                                ?? $spaces->first();
                            if (is_array($selectedSpace)) {
                                $selectedSpace = array_merge($selectedSpace, [
                                    'name' => $selectedSpace['name'] ?? 'The Garden Grooming Spot',
                                    'host' => 'Chloe D.',
                                    'hosted_by' => 'Chloe D.',
                                    'description' => 'Outdoor garden grooming area. Calm, spacious, and ideal for stress-free sessions in fresh air.',
                                    'tags' => ['Salon'],
                                    'price' => $selectedSpace['price'] ?? 30,
                                    'distance' => '1.0 mi',
                                ]);
                            }
                        @endphp
                        @include('partials.customer-journey.specific-unavailable', [
                            'lead' => $specificLead,
                            'other' => $specificOther,
                            'title' => $specificTitle,
                            'kind' => 'space',
                            'selected' => $selectedSpace,
                        ])
                        <div class="section-divider" style="background-color: #DFDFDF"></div>
                        <h1 class="section-title mt-5">Other space’s availability at selected time</h1>
                        {{-- Custom specific list view uses vertical space cards for “other”, not list rows --}}
                        @php
                            $otherSpaces = $spaces->reject(fn ($s) => ($s['name'] ?? '') === ($selectedSpace['name'] ?? ''))->values();
                            if ($otherSpaces->isEmpty()) {
                                $otherSpaces = $spaces;
                            }
                        @endphp
                        <x-ui.space-tab-card-view :spaces="$otherSpaces" :showLoadMore="true" loadMoreText="Load More"
                            loadMoreUrl="#" />
                    @else
                        <x-ui.space-tab-list-view :spaces="$spaces" :showLoadMore="true" loadMoreUrl="#" />
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>

@include('partials.customer-journey.assets')
