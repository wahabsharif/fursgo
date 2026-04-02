@extends('layouts.app')

@section('content')
<section>
    <div class="groomer-tab-content main-tab-content" id="groomer">
        <x-ui.filter-section
            groom-modal-id="groomModal"
            space-modal-id="spaceModal"
            active-tab="groomer" />
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
                    {{--

                    <!-- not found groomers -->
                    <div class="col-lg-12 d-flex align-items-center justify-content-center section-gap">
                        <p class="not-found-message">Sorry, <span class="not-found-message">your preferred location isn't available today, <br>
                                please see below for groomers in the nearest proximity!</span></p>
                    </div>

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
                        <x-ui.groomer-tab-list-view :groomers="$groomers" />
                    </div>
                    --}}
                </div>
            </div>
        </section>
    </div>
    <div class="space-tab-content main-tab-content" id="space">
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

                    <x-ui.space-venue-sort-options />
                    {{--
                    <div data-tab-content="space-calendar-view" class="tabcontent">
                        <x-ui.calendar-view />

                        <div class="container">
                            <hr style="border-top: 1px solid #DFDFDF;">
                        </div>

                        <x-ui.space-tab-card-view :spaces="$spaces" />
                    </div>

                    <div data-tab-content="space-map-view" class="tabcontent" style="display: none;">
                        <x-ui.space-tab-map-view />
                    </div>

                    <div data-tab-content="space-list-view" class="tabcontent" style="display: none;">
                        <x-ui.space-tab-list-view :spaces="$spaces" />

                    </div>
                    --}}
                </div>
            </div>
        </section>
    </div>
</section>

@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/customer_journey.css') }}">
@endpush

@push('script')
<script src="{{ asset('js/costumer_journey.js') }}"></script>
@endpush