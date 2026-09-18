@extends('layouts.app')

@section('title', 'Fursgo - Help & Support')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
    <style>
        #request-submitted-modal .modal-content.size {
            width: 645px;
        }
    </style>
@endpush

@section('content')
<div class="container mb-5 mt-5">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="top-head d-flex flex-column align-items-center justify-content-center">
                            <h1 class="large-font">Help & Support Center</h1>
                            <form action="{{ route('help-and-support-search-legacy') }}">
                                <div class="search-wrapper">
                                    <input
                                        type="text"
                                        placeholder="Search for topics like refunds, bookings, payments."
                                        name="search_results"
                                        value="{{ $search_results }}"
                                        class="normal-font-weight">
                                    <button class="search-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
                                            <circle cx="21" cy="21" r="21" fill="#FFC97A" />
                                            <path d="M19.7354 14.75C22.4886 14.75 24.7207 16.9821 24.7207 19.7354C24.7207 21.1492 24.1329 22.4248 23.1865 23.333C22.2901 24.1932 21.0751 24.7207 19.7354 24.7207C16.9821 24.7207 14.75 22.4886 14.75 19.7354C14.75 16.982 16.982 14.75 19.7354 14.75Z" stroke="white" stroke-width="1.5" />
                                            <path d="M28.4697 29.5303C28.7626 29.8232 29.2374 29.8232 29.5303 29.5303C29.8232 29.2374 29.8232 28.7626 29.5303 28.4697L29 29L28.4697 29.5303ZM23.7059 23.7059L23.1755 24.2362L28.4697 29.5303L29 29L29.5303 28.4697L24.2362 23.1755L23.7059 23.7059Z" fill="white" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                            <div class="common-topics d-flex align-items-center justify-content-center gap-20 mb-3">
                                <p>Common Topics</p>
                                <p class="bg cursor">Bookings</p>
                                <p class="bg cursor">Payments</p>
                                <p class="bg cursor">Account</p>
                                <p class="bg cursor">Pets</p>
                                <p class="bg cursor">Policies</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1"></div>
                    <div class="col-lg-10">
                        <div class="d-flex align-items-center mt-5">
                            <h1 class="large-font">Results</h1>
                        </div>
                        <div class="bg-div result-item mt-5">
                            <p class="normal-font-bold">How do I book a grooming appointment?</p>
                            <div class="result-answer simple-font mt-3">
                                <p>Booking through FursGo is straightforward. Simply choose your location, select the service you’re looking for, and pick a time.</p>
                                <p class="mt-3">Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</p>
                                <p class="mt-3">Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</p>
                                <p class="mt-3">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p>
                            </div>
                            <button type="button" class="result-read-more underline normal-font-bold">Read more</button>
                        </div>
                        <div class="bg-div result-item mt-5">
                            <p class="normal-font-bold">Can I reschedule or cancel a booking?</p>
                            <div class="result-answer simple-font mt-3">
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae.</p>
                            </div>
                            <button type="button" class="result-read-more underline normal-font-bold">Read more</button>
                        </div>
                        <div class="bg-div result-item mt-5">
                            <p class="normal-font-bold">Where can I see my upcoming bookings?</p>
                            <div class="result-answer simple-font mt-3">
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae.</p>
                            </div>
                            <button type="button" class="result-read-more underline normal-font-bold">Read more</button>
                        </div>

                        <div class="d-flex justify-content-center mt-5">
                            <button class="btn-custom btn-no-bg">Load More</button>
                        </div>
                    </div>
                    <div class="col-lg-1"></div>
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                @include('support_and_assistance.contact-support-legacy')
            </div>
            <div class="col-lg-1"></div>

        </div>

        
@endsection

@push('script')
    <script src="{{ asset('js/help-common.js') }}"></script>
    <script src="{{ asset('js/help-support.js') }}"></script>
@endpush
