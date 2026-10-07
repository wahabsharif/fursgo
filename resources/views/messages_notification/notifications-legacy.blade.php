@extends('layouts.app')

@section('title', 'Fursgo - Notifications')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/messages_notification/notification.css') }}">
    <style>
.sort-by {
            border-radius: 100px;
            color: var(--groomer-color);
            border: 1px solid var(--groomer-color);
            padding: 10px 20px 10px 20px;
            text-align: center;
            font-family: "Lato";
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
            cursor: pointer;
            height: 32px;
        }

        /* Hide native checkbox */
        input[type="checkbox"] {
            display: none;
        }

        input[type="radio"] {
            display: none;
        }

        .check-circle {
            width: 25px;
            height: 25px;
            border: 1px solid #FBAC83;
            border-radius: 50%;
            display: grid;
            place-items: center;
            margin-right: 5px;
            cursor: pointer;
            box-sizing: border-box;
            flex-shrink: 0;
        }

        .check-circle::after {
            content: "";
            width: 15px;
            height: 15px;
            background-color: #FBAC83;
            border-radius: 50%;
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        input:checked+.check-circle::after {
            opacity: 1;
        }

        .option-text {
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        label {
            cursor: pointer;
            display: flex !important;
            align-items: center;
            width: 100%;
            justify-content: space-between;
        }
    </style>
@endpush

@section('content')
<!-- test -->
    <!-- <button class="normal-font-bold btn-custom btn-no-bg text-center mt-3" data-modal-open="promotion_modal" style="color:#FBAC83;border:1px solid #FBAC83">Submit request</button> -->

    <!-- Modal  -->

    <div class="modal" id="promotion_modal" style="display: flex;">
        <div class="modal-content">
            <div class="promotion-svg">
                <svg xmlns="http://www.w3.org/2000/svg" width="101" height="101" viewBox="0 0 101 101" fill="none">
                    <circle cx="50.5" cy="50.5" r="50.5" fill="#FDFCF8" />
                    <path d="M61.1962 47.7471C62.2992 57.9515 66.6373 61.0366 66.6373 61.0366H32.4644C32.4644 61.0366 38.1598 56.9871 38.1598 42.811C38.1598 39.5893 39.3597 36.4985 41.4955 34.2203C43.6313 31.9421 46.5322 30.6606 49.5508 30.6606C50.1925 30.6606 50.8253 30.7176 51.4493 30.8315M52.8352 66.7321C52.5014 67.3075 52.0224 67.7851 51.446 68.1171C50.8695 68.4491 50.216 68.6238 49.5508 68.6238C48.8856 68.6238 48.2321 68.4491 47.6557 68.1171C47.0793 67.7851 46.6002 67.3075 46.2664 66.7321M62.8403 42.0516C64.3508 42.0516 65.7995 41.4516 66.8676 40.3835C67.9357 39.3153 68.5358 37.8667 68.5358 36.3561C68.5358 34.8456 67.9357 33.3969 66.8676 32.3288C65.7995 31.2607 64.3508 30.6606 62.8403 30.6606C61.3298 30.6606 59.8811 31.2607 58.813 32.3288C57.7449 33.3969 57.1448 34.8456 57.1448 36.3561C57.1448 37.8667 57.7449 39.3153 58.813 40.3835C59.8811 41.4516 61.3298 42.0516 62.8403 42.0516Z" stroke="#3B3731" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </div>
            <div class="promotion-cross-svg cursor" data-modal-close>
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                    <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                    <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </div>
            <h1 class="promotion_font"> 🎉 Promotion</h1>
            <p class="normal-default-color-font text-center mt-5 line-height-20">Don’t miss the chance and <br> get 10% off your next booking!</p>
            <div class="form-field mt-4">
                <div class="promotion-input input-wrapper">
                    <input type="text" class="custom-width normal-light-color" style="color: var(--font-color);" id="promo_code" value="FURSGO-9X3P">
                    <button class="copy-button medium-font-bold cursor">Copy</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal  -->

    <div class="container mb-5 mt-5">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="top-head d-flex align-items-center justify-content-center">
                            <h1 class="large-font">Notifications</h1>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-2"></div>
            <div class="col-lg-8">
                <div class="notification-heading-searchbox d-flex align-items-center justify-content-between mt-5">
                    <h1 class="large-font" id="notifications-heading">All Notifications</h1>
                    <div class="search-box mt-4">
                        <input type="text" id="search-groomer"
                            placeholder="Search address, postcode, name ...">
                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M5.73535 0.5C8.6267 0.500031 10.9707 2.844 10.9707 5.73535C10.9707 7.22006 10.3528 8.55933 9.35938 9.5127C8.41826 10.4158 7.14221 10.9707 5.73535 10.9707C2.844 10.9707 0.500031 8.6267 0.5 5.73535C0.5 2.84398 2.84398 0.5 5.73535 0.5Z" stroke="#9D9B98" />
                            <path d="M14.6466 15.3537C14.8419 15.549 15.1585 15.549 15.3537 15.3537C15.549 15.1585 15.549 14.8419 15.3537 14.6466L15.0002 15.0002L14.6466 15.3537ZM9.70605 9.70605L9.3525 10.0596L14.6466 15.3537L15.0002 15.0002L15.3537 14.6466L10.0596 9.3525L9.70605 9.70605Z" fill="#9D9B98" />
                        </svg>
                    </div>
                </div>
                <div class="filters-sort-options d-flex align-items-center justify-content-between mt-5">

                    <div class="filters d-flex align-items-center gap-20">

                    </div>
                    <div class="sort-by">
                        Sort
                        &nbsp;
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none">
                            <path d="M11.9104 0.5L6.15697 6.25344L0.500111 0.596581" stroke="#FBAC83" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <div class="sort-by-filter">
                            <div class="sort dropdown">
                                <ul>
                                    <li>
                                        <label>
                                            <span class="option-text">Recommended (default)</span>
                                            <input type="radio" name="groomer-sort" value="default"> <span class="check-circle"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <span class="option-text">Unread first</span>
                                            <input type="radio" name="groomer-sort" value="unread-first" checked> <span class="check-circle"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <span class="option-text">Distance</span>
                                            <input type="radio" name="groomer-sort" value="distance">
                                            <span class="check-circle"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <span class="option-text">Lowest price</span>
                                            <input type="radio" name="groomer-sort" value="lowest_price">
                                            <span class="check-circle"></span>
                                        </label>
                                    </li>
                                    <li>
                                        <label>
                                            <span class="option-text">Soonest available</span>
                                            <input type="radio" name="groomer-sort" value="soonest_available">
                                            <span class="check-circle"></span>
                                        </label>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-between mt-5">
                    <div class="d-flex align-items-center gap-25">
                        <div class="tags cursor active" data-tab="all">
                            <p class="normal-font-weight-active-color">All <span class="count active">13</span></p>
                        </div>
                        <div class="tags cursor" data-tab="bookings">
                            <p class="normal-font-weight muted-color">Bookings <span class="count">5</span></p>
                        </div>
                        <div class="tags cursor" data-tab="payments">
                            <p class="normal-font-weight muted-color">Payments <span class="count">3</span></p>
                        </div>
                        <div class="tags cursor" data-tab="reviews">
                            <p class="normal-font-weight muted-color">Reviews <span class="count">2</span></p>
                        </div>
                        <div class="tags cursor" data-tab="updates">
                            <p class="normal-font-weight muted-color">Updates <span class="count">3</span></p>
                        </div>
                    </div>
                    <div class="mark-notification-dots-svg cursor">
                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="5" viewBox="0 0 25 5" fill="none">
                            <circle cx="2.5" cy="2.5" r="2.5" fill="#3B3731" />
                            <circle cx="12.5" cy="2.5" r="2.5" fill="#3B3731" />
                            <circle cx="22.5" cy="2.5" r="2.5" fill="#3B3731" />
                        </svg>
                        <div class="clear-read dropdown">
                            <div class="dropdown-item">
                                <span class="mark-all">Mark All as Read</span>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <path d="M5.1875 7.0625L8 9.875L15.5 2.375" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M15.5 8V13.625C15.5 14.1223 15.3025 14.5992 14.9508 14.9508C14.5992 15.3025 14.1223 15.5 13.625 15.5H2.375C1.87772 15.5 1.40081 15.3025 1.04917 14.9508C0.697544 14.5992 0.5 14.1223 0.5 13.625V2.375C0.5 1.87772 0.697544 1.40081 1.04917 1.04917C1.40081 0.697544 1.87772 0.5 2.375 0.5H10.8125" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                </span>
                            </div>

                            <div class="dropdown-item danger">
                                <span>Clear All</span>
                                <span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16" fill="none">
                                        <path d="M2.61601 15.77C2.17134 15.77 1.79101 15.6117 1.47501 15.295C1.15901 14.9783 1.00067 14.5987 1.00001 14.156V1.77H0.500007C0.358007 1.77 0.23934 1.722 0.144007 1.626C0.0486736 1.53 0.000673516 1.411 6.84931e-06 1.269C-0.000659817 1.127 0.0473402 1.00833 0.144007 0.913C0.240674 0.817667 0.35934 0.77 0.500007 0.77H4.00001C4.00001 0.563333 4.07667 0.383333 4.23001 0.23C4.38334 0.0766667 4.56334 0 4.77001 0H9.23001C9.43667 0 9.61667 0.0766667 9.77001 0.23C9.92334 0.383333 10 0.563333 10 0.77H13.5C13.642 0.77 13.7607 0.818 13.856 0.914C13.9513 1.01 13.9993 1.129 14 1.271C14.0007 1.413 13.9527 1.53167 13.856 1.627C13.7593 1.72233 13.6407 1.77 13.5 1.77H13V14.155C13 14.599 12.8417 14.979 12.525 15.295C12.2083 15.611 11.8283 15.7693 11.385 15.77H2.61601ZM12 1.77H2.00001V14.155C2.00001 14.3343 2.05767 14.4817 2.17301 14.597C2.28834 14.7123 2.43601 14.77 2.61601 14.77H11.385C11.5643 14.77 11.7117 14.7123 11.827 14.597C11.9423 14.4817 12 14.3343 12 14.155V1.77ZM5.30801 12.77C5.45001 12.77 5.56901 12.722 5.66501 12.626C5.76101 12.53 5.80867 12.4113 5.80801 12.27V4.27C5.80801 4.128 5.76001 4.00933 5.66401 3.914C5.56801 3.81867 5.44901 3.77067 5.30701 3.77C5.16501 3.76933 5.04634 3.81733 4.95101 3.914C4.85567 4.01067 4.80801 4.12933 4.80801 4.27V12.27C4.80801 12.412 4.85601 12.5307 4.95201 12.626C5.04801 12.722 5.16667 12.77 5.30801 12.77ZM8.69301 12.77C8.83501 12.77 8.95367 12.722 9.04901 12.626C9.14434 12.53 9.19201 12.4113 9.19201 12.27V4.27C9.19201 4.128 9.14401 4.00933 9.04801 3.914C8.95201 3.818 8.83334 3.77 8.69201 3.77C8.55001 3.77 8.43101 3.818 8.33501 3.914C8.23901 4.01 8.19134 4.12867 8.19201 4.27V12.27C8.19201 12.412 8.24001 12.5307 8.33601 12.626C8.43201 12.7213 8.55101 12.7693 8.69301 12.77Z" fill="#3B3731" />
                                    </svg>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="notification-list mt-5">
                    <div class="notification-list-item booking-confirmed mt-4" data-category="bookings">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="86" height="86" viewBox="0 0 86 86" fill="none">
                                        <g opacity="0.8" filter="url(#filter0_d_10_2216)">
                                            <circle cx="43" cy="39" r="28" fill="#C9DDA0" />
                                        </g>
                                        <path d="M33 39.0948C33 35.4849 33 33.6795 34.172 32.5586C35.344 31.4376 37.229 31.4366 41 31.4366H45C48.771 31.4366 50.657 31.4366 51.828 32.5586C52.999 33.6805 53 35.4849 53 39.0948V41.0093C53 44.6191 53 46.4245 51.828 47.5455C50.656 48.6664 48.771 48.6674 45 48.6674H41C37.229 48.6674 35.343 48.6674 34.172 47.5455C33.001 46.4236 33 44.6191 33 41.0093V39.0948Z" stroke="white" stroke-width="1.5" />
                                        <path d="M38 31.4359V30M48 31.4359V30M33.5 36.2222H52.5" stroke="white" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M49.0002 43.8804C49.0002 44.1343 48.8949 44.3778 48.7074 44.5573C48.5198 44.7368 48.2655 44.8377 48.0002 44.8377C47.735 44.8377 47.4807 44.7368 47.2931 44.5573C47.1056 44.3778 47.0002 44.1343 47.0002 43.8804C47.0002 43.6266 47.1056 43.3831 47.2931 43.2035C47.4807 43.024 47.735 42.9232 48.0002 42.9232C48.2655 42.9232 48.5198 43.024 48.7074 43.2035C48.8949 43.3831 49.0002 43.6266 49.0002 43.8804ZM49.0002 40.0514C49.0002 40.3053 48.8949 40.5487 48.7074 40.7283C48.5198 40.9078 48.2655 41.0086 48.0002 41.0086C47.735 41.0086 47.4807 40.9078 47.2931 40.7283C47.1056 40.5487 47.0002 40.3053 47.0002 40.0514C47.0002 39.7975 47.1056 39.554 47.2931 39.3745C47.4807 39.195 47.735 39.0941 48.0002 39.0941C48.2655 39.0941 48.5198 39.195 48.7074 39.3745C48.8949 39.554 49.0002 39.7975 49.0002 40.0514ZM44.0002 43.8804C44.0002 44.1343 43.8949 44.3778 43.7074 44.5573C43.5198 44.7368 43.2655 44.8377 43.0002 44.8377C42.735 44.8377 42.4807 44.7368 42.2931 44.5573C42.1056 44.3778 42.0002 44.1343 42.0002 43.8804C42.0002 43.6266 42.1056 43.3831 42.2931 43.2035C42.4807 43.024 42.735 42.9232 43.0002 42.9232C43.2655 42.9232 43.5198 43.024 43.7074 43.2035C43.8949 43.3831 44.0002 43.6266 44.0002 43.8804ZM44.0002 40.0514C44.0002 40.3053 43.8949 40.5487 43.7074 40.7283C43.5198 40.9078 43.2655 41.0086 43.0002 41.0086C42.735 41.0086 42.4807 40.9078 42.2931 40.7283C42.1056 40.5487 42.0002 40.3053 42.0002 40.0514C42.0002 39.7975 42.1056 39.554 42.2931 39.3745C42.4807 39.195 42.735 39.0941 43.0002 39.0941C43.2655 39.0941 43.5198 39.195 43.7074 39.3745C43.8949 39.554 44.0002 39.7975 44.0002 40.0514ZM39.0002 43.8804C39.0002 44.1343 38.8949 44.3778 38.7074 44.5573C38.5198 44.7368 38.2655 44.8377 38.0002 44.8377C37.735 44.8377 37.4807 44.7368 37.2931 44.5573C37.1056 44.3778 37.0002 44.1343 37.0002 43.8804C37.0002 43.6266 37.1056 43.3831 37.2931 43.2035C37.4807 43.024 37.735 42.9232 38.0002 42.9232C38.2655 42.9232 38.5198 43.024 38.7074 43.2035C38.8949 43.3831 39.0002 43.6266 39.0002 43.8804ZM39.0002 40.0514C39.0002 40.3053 38.8949 40.5487 38.7074 40.7283C38.5198 40.9078 38.2655 41.0086 38.0002 41.0086C37.735 41.0086 37.4807 40.9078 37.2931 40.7283C37.1056 40.5487 37.0002 40.3053 37.0002 40.0514C37.0002 39.7975 37.1056 39.554 37.2931 39.3745C37.4807 39.195 37.735 39.0941 38.0002 39.0941C38.2655 39.0941 38.5198 39.195 38.7074 39.3745C38.8949 39.554 39.0002 39.7975 39.0002 40.0514Z" fill="white" />
                                        <defs>
                                            <filter id="filter0_d_10_2216" x="0" y="0" width="86" height="86" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2216" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="5" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2216" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2216" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Booking Confirmed</p>
                                    <p class="dark-color-font">Your appointment with [Groomer Name] is confirmed.</p>
                                    <p class="simple-font">20m ago | Bookings</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="notification-list-item booking-updated mt-4" data-category="bookings">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="86" height="86" viewBox="0 0 86 86" fill="none">
                                        <g opacity="0.8" filter="url(#filter0_d_10_2225)">
                                            <circle cx="43" cy="39" r="28" fill="#FFC97A" />
                                        </g>
                                        <path d="M33 39.0948C33 35.4849 33 33.6795 34.172 32.5586C35.344 31.4376 37.229 31.4366 41 31.4366H45C48.771 31.4366 50.657 31.4366 51.828 32.5586C52.999 33.6805 53 35.4849 53 39.0948V41.0093C53 44.6191 53 46.4245 51.828 47.5455C50.656 48.6664 48.771 48.6674 45 48.6674H41C37.229 48.6674 35.343 48.6674 34.172 47.5455C33.001 46.4236 33 44.6191 33 41.0093V39.0948Z" stroke="white" stroke-width="1.5" />
                                        <path d="M38 31.4359V30M48 31.4359V30M33.5 36.2222H52.5" stroke="white" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M49.0002 43.8804C49.0002 44.1343 48.8949 44.3778 48.7074 44.5573C48.5198 44.7368 48.2655 44.8377 48.0002 44.8377C47.735 44.8377 47.4807 44.7368 47.2931 44.5573C47.1056 44.3778 47.0002 44.1343 47.0002 43.8804C47.0002 43.6266 47.1056 43.3831 47.2931 43.2035C47.4807 43.024 47.735 42.9232 48.0002 42.9232C48.2655 42.9232 48.5198 43.024 48.7074 43.2035C48.8949 43.3831 49.0002 43.6266 49.0002 43.8804ZM49.0002 40.0514C49.0002 40.3053 48.8949 40.5487 48.7074 40.7283C48.5198 40.9078 48.2655 41.0086 48.0002 41.0086C47.735 41.0086 47.4807 40.9078 47.2931 40.7283C47.1056 40.5487 47.0002 40.3053 47.0002 40.0514C47.0002 39.7975 47.1056 39.554 47.2931 39.3745C47.4807 39.195 47.735 39.0941 48.0002 39.0941C48.2655 39.0941 48.5198 39.195 48.7074 39.3745C48.8949 39.554 49.0002 39.7975 49.0002 40.0514ZM44.0002 43.8804C44.0002 44.1343 43.8949 44.3778 43.7074 44.5573C43.5198 44.7368 43.2655 44.8377 43.0002 44.8377C42.735 44.8377 42.4807 44.7368 42.2931 44.5573C42.1056 44.3778 42.0002 44.1343 42.0002 43.8804C42.0002 43.6266 42.1056 43.3831 42.2931 43.2035C42.4807 43.024 42.735 42.9232 43.0002 42.9232C43.2655 42.9232 43.5198 43.024 43.7074 43.2035C43.8949 43.3831 44.0002 43.6266 44.0002 43.8804ZM44.0002 40.0514C44.0002 40.3053 43.8949 40.5487 43.7074 40.7283C43.5198 40.9078 43.2655 41.0086 43.0002 41.0086C42.735 41.0086 42.4807 40.9078 42.2931 40.7283C42.1056 40.5487 42.0002 40.3053 42.0002 40.0514C42.0002 39.7975 42.1056 39.554 42.2931 39.3745C42.4807 39.195 42.735 39.0941 43.0002 39.0941C43.2655 39.0941 43.5198 39.195 43.7074 39.3745C43.8949 39.554 44.0002 39.7975 44.0002 40.0514ZM39.0002 43.8804C39.0002 44.1343 38.8949 44.3778 38.7074 44.5573C38.5198 44.7368 38.2655 44.8377 38.0002 44.8377C37.735 44.8377 37.4807 44.7368 37.2931 44.5573C37.1056 44.3778 37.0002 44.1343 37.0002 43.8804C37.0002 43.6266 37.1056 43.3831 37.2931 43.2035C37.4807 43.024 37.735 42.9232 38.0002 42.9232C38.2655 42.9232 38.5198 43.024 38.7074 43.2035C38.8949 43.3831 39.0002 43.6266 39.0002 43.8804ZM39.0002 40.0514C39.0002 40.3053 38.8949 40.5487 38.7074 40.7283C38.5198 40.9078 38.2655 41.0086 38.0002 41.0086C37.735 41.0086 37.4807 40.9078 37.2931 40.7283C37.1056 40.5487 37.0002 40.3053 37.0002 40.0514C37.0002 39.7975 37.1056 39.554 37.2931 39.3745C37.4807 39.195 37.735 39.0941 38.0002 39.0941C38.2655 39.0941 38.5198 39.195 38.7074 39.3745C38.8949 39.554 39.0002 39.7975 39.0002 40.0514Z" fill="white" />
                                        <defs>
                                            <filter id="filter0_d_10_2225" x="0" y="0" width="86" height="86" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2225" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="5" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2225" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2225" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Booking Updated</p>
                                    <p class="dark-color-font">Your appointment with [Groomer Name] was updated.</p>
                                    <p class="simple-font muted-color">20m ago | Bookings</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="notification-list-item booking-reminder mt-4" data-category="bookings">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="86" height="86" viewBox="0 0 86 86" fill="none">
                                        <g opacity="0.8" filter="url(#filter0_d_10_2234)">
                                            <circle cx="43" cy="39" r="28" fill="#FBAC83" />
                                        </g>
                                        <path d="M33 39.0949C33 35.485 33 33.6796 34.172 32.5587C35.344 31.4377 37.229 31.4368 41 31.4368H45C48.771 31.4368 50.657 31.4368 51.828 32.5587C52.999 33.6806 53 35.485 53 39.0949V41.0094C53 44.6193 53 46.4247 51.828 47.5456C50.656 48.6666 48.771 48.6675 45 48.6675H41C37.229 48.6675 35.343 48.6675 34.172 47.5456C33.001 46.4237 33 44.6193 33 41.0094V39.0949Z" stroke="white" stroke-width="1.5" />
                                        <path d="M38 31.4359V30M48 31.4359V30M33.5 36.2222H52.5" stroke="white" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M49.0002 43.8803C49.0002 44.1342 48.8949 44.3777 48.7074 44.5572C48.5198 44.7367 48.2655 44.8376 48.0002 44.8376C47.735 44.8376 47.4807 44.7367 47.2931 44.5572C47.1056 44.3777 47.0002 44.1342 47.0002 43.8803C47.0002 43.6264 47.1056 43.3829 47.2931 43.2034C47.4807 43.0239 47.735 42.9231 48.0002 42.9231C48.2655 42.9231 48.5198 43.0239 48.7074 43.2034C48.8949 43.3829 49.0002 43.6264 49.0002 43.8803ZM49.0002 40.0513C49.0002 40.3051 48.8949 40.5486 48.7074 40.7281C48.5198 40.9077 48.2655 41.0085 48.0002 41.0085C47.735 41.0085 47.4807 40.9077 47.2931 40.7281C47.1056 40.5486 47.0002 40.3051 47.0002 40.0513C47.0002 39.7974 47.1056 39.5539 47.2931 39.3744C47.4807 39.1948 47.735 39.094 48.0002 39.094C48.2655 39.094 48.5198 39.1948 48.7074 39.3744C48.8949 39.5539 49.0002 39.7974 49.0002 40.0513ZM44.0002 43.8803C44.0002 44.1342 43.8949 44.3777 43.7074 44.5572C43.5198 44.7367 43.2655 44.8376 43.0002 44.8376C42.735 44.8376 42.4807 44.7367 42.2931 44.5572C42.1056 44.3777 42.0002 44.1342 42.0002 43.8803C42.0002 43.6264 42.1056 43.3829 42.2931 43.2034C42.4807 43.0239 42.735 42.9231 43.0002 42.9231C43.2655 42.9231 43.5198 43.0239 43.7074 43.2034C43.8949 43.3829 44.0002 43.6264 44.0002 43.8803ZM44.0002 40.0513C44.0002 40.3051 43.8949 40.5486 43.7074 40.7281C43.5198 40.9077 43.2655 41.0085 43.0002 41.0085C42.735 41.0085 42.4807 40.9077 42.2931 40.7281C42.1056 40.5486 42.0002 40.3051 42.0002 40.0513C42.0002 39.7974 42.1056 39.5539 42.2931 39.3744C42.4807 39.1948 42.735 39.094 43.0002 39.094C43.2655 39.094 43.5198 39.1948 43.7074 39.3744C43.8949 39.5539 44.0002 39.7974 44.0002 40.0513ZM39.0002 43.8803C39.0002 44.1342 38.8949 44.3777 38.7074 44.5572C38.5198 44.7367 38.2655 44.8376 38.0002 44.8376C37.735 44.8376 37.4807 44.7367 37.2931 44.5572C37.1056 44.3777 37.0002 44.1342 37.0002 43.8803C37.0002 43.6264 37.1056 43.3829 37.2931 43.2034C37.4807 43.0239 37.735 42.9231 38.0002 42.9231C38.2655 42.9231 38.5198 43.0239 38.7074 43.2034C38.8949 43.3829 39.0002 43.6264 39.0002 43.8803ZM39.0002 40.0513C39.0002 40.3051 38.8949 40.5486 38.7074 40.7281C38.5198 40.9077 38.2655 41.0085 38.0002 41.0085C37.735 41.0085 37.4807 40.9077 37.2931 40.7281C37.1056 40.5486 37.0002 40.3051 37.0002 40.0513C37.0002 39.7974 37.1056 39.5539 37.2931 39.3744C37.4807 39.1948 37.735 39.094 38.0002 39.094C38.2655 39.094 38.5198 39.1948 38.7074 39.3744C38.8949 39.5539 39.0002 39.7974 39.0002 40.0513Z" fill="white" />
                                        <defs>
                                            <filter id="filter0_d_10_2234" x="0" y="0" width="86" height="86" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2234" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="5" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2234" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2234" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Booking Reminder</p>
                                    <p class="dark-color-font">Your appointment with [Groomer Name] is tomorrow.</p>
                                    <p class="simple-font muted-color">Yesterday | Bookings</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="notification-list-item noshadow mt-4" data-category="bookings">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="86" height="86" viewBox="0 0 86 86" fill="none">
                                        <g opacity="0.8" filter="url(#filter0_d_10_2243)">
                                            <circle cx="43" cy="39" r="28" fill="white" />
                                        </g>
                                        <path d="M33 39.0949C33 35.485 33 33.6796 34.172 32.5587C35.344 31.4377 37.229 31.4368 41 31.4368H45C48.771 31.4368 50.657 31.4368 51.828 32.5587C52.999 33.6806 53 35.485 53 39.0949V41.0094C53 44.6193 53 46.4247 51.828 47.5456C50.656 48.6666 48.771 48.6675 45 48.6675H41C37.229 48.6675 35.343 48.6675 34.172 47.5456C33.001 46.4237 33 44.6193 33 41.0094V39.0949Z" stroke="#3B3731" stroke-width="1.5" />
                                        <path d="M38 31.4359V30M48 31.4359V30M33.5 36.2222H52.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M49.0002 43.8803C49.0002 44.1342 48.8949 44.3777 48.7074 44.5572C48.5198 44.7367 48.2655 44.8376 48.0002 44.8376C47.735 44.8376 47.4807 44.7367 47.2931 44.5572C47.1056 44.3777 47.0002 44.1342 47.0002 43.8803C47.0002 43.6264 47.1056 43.3829 47.2931 43.2034C47.4807 43.0239 47.735 42.9231 48.0002 42.9231C48.2655 42.9231 48.5198 43.0239 48.7074 43.2034C48.8949 43.3829 49.0002 43.6264 49.0002 43.8803ZM49.0002 40.0513C49.0002 40.3051 48.8949 40.5486 48.7074 40.7281C48.5198 40.9077 48.2655 41.0085 48.0002 41.0085C47.735 41.0085 47.4807 40.9077 47.2931 40.7281C47.1056 40.5486 47.0002 40.3051 47.0002 40.0513C47.0002 39.7974 47.1056 39.5539 47.2931 39.3744C47.4807 39.1948 47.735 39.094 48.0002 39.094C48.2655 39.094 48.5198 39.1948 48.7074 39.3744C48.8949 39.5539 49.0002 39.7974 49.0002 40.0513ZM44.0002 43.8803C44.0002 44.1342 43.8949 44.3777 43.7074 44.5572C43.5198 44.7367 43.2655 44.8376 43.0002 44.8376C42.735 44.8376 42.4807 44.7367 42.2931 44.5572C42.1056 44.3777 42.0002 44.1342 42.0002 43.8803C42.0002 43.6264 42.1056 43.3829 42.2931 43.2034C42.4807 43.0239 42.735 42.9231 43.0002 42.9231C43.2655 42.9231 43.5198 43.0239 43.7074 43.2034C43.8949 43.3829 44.0002 43.6264 44.0002 43.8803ZM44.0002 40.0513C44.0002 40.3051 43.8949 40.5486 43.7074 40.7281C43.5198 40.9077 43.2655 41.0085 43.0002 41.0085C42.735 41.0085 42.4807 40.9077 42.2931 40.7281C42.1056 40.5486 42.0002 40.3051 42.0002 40.0513C42.0002 39.7974 42.1056 39.5539 42.2931 39.3744C42.4807 39.1948 42.735 39.094 43.0002 39.094C43.2655 39.094 43.5198 39.1948 43.7074 39.3744C43.8949 39.5539 44.0002 39.7974 44.0002 40.0513ZM39.0002 43.8803C39.0002 44.1342 38.8949 44.3777 38.7074 44.5572C38.5198 44.7367 38.2655 44.8376 38.0002 44.8376C37.735 44.8376 37.4807 44.7367 37.2931 44.5572C37.1056 44.3777 37.0002 44.1342 37.0002 43.8803C37.0002 43.6264 37.1056 43.3829 37.2931 43.2034C37.4807 43.0239 37.735 42.9231 38.0002 42.9231C38.2655 42.9231 38.5198 43.0239 38.7074 43.2034C38.8949 43.3829 39.0002 43.6264 39.0002 43.8803ZM39.0002 40.0513C39.0002 40.3051 38.8949 40.5486 38.7074 40.7281C38.5198 40.9077 38.2655 41.0085 38.0002 41.0085C37.735 41.0085 37.4807 40.9077 37.2931 40.7281C37.1056 40.5486 37.0002 40.3051 37.0002 40.0513C37.0002 39.7974 37.1056 39.5539 37.2931 39.3744C37.4807 39.1948 37.735 39.094 38.0002 39.094C38.2655 39.094 38.5198 39.1948 38.7074 39.3744C38.8949 39.5539 39.0002 39.7974 39.0002 40.0513Z" fill="#3B3731" />
                                        <defs>
                                            <filter id="filter0_d_10_2243" x="0" y="0" width="86" height="86" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2243" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="5" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2243" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2243" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Booking Cancelled</p>
                                    <p class="dark-color-font">Your appointment with [Groomer Name] was cancelled.</p>
                                    <p class="simple-font muted-color">3h ago | Bookings</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="bookings"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="bookings">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="86" height="86" viewBox="0 0 86 86" fill="none">
                                        <g opacity="0.8" filter="url(#filter0_d_10_2243)">
                                            <circle cx="43" cy="39" r="28" fill="white" />
                                        </g>
                                        <path d="M33 39.0949C33 35.485 33 33.6796 34.172 32.5587C35.344 31.4377 37.229 31.4368 41 31.4368H45C48.771 31.4368 50.657 31.4368 51.828 32.5587C52.999 33.6806 53 35.485 53 39.0949V41.0094C53 44.6193 53 46.4247 51.828 47.5456C50.656 48.6666 48.771 48.6675 45 48.6675H41C37.229 48.6675 35.343 48.6675 34.172 47.5456C33.001 46.4237 33 44.6193 33 41.0094V39.0949Z" stroke="#3B3731" stroke-width="1.5" />
                                        <path d="M38 31.4359V30M48 31.4359V30M33.5 36.2222H52.5" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                        <path d="M49.0002 43.8803C49.0002 44.1342 48.8949 44.3777 48.7074 44.5572C48.5198 44.7367 48.2655 44.8376 48.0002 44.8376C47.735 44.8376 47.4807 44.7367 47.2931 44.5572C47.1056 44.3777 47.0002 44.1342 47.0002 43.8803C47.0002 43.6264 47.1056 43.3829 47.2931 43.2034C47.4807 43.0239 47.735 42.9231 48.0002 42.9231C48.2655 42.9231 48.5198 43.0239 48.7074 43.2034C48.8949 43.3829 49.0002 43.6264 49.0002 43.8803ZM49.0002 40.0513C49.0002 40.3051 48.8949 40.5486 48.7074 40.7281C48.5198 40.9077 48.2655 41.0085 48.0002 41.0085C47.735 41.0085 47.4807 40.9077 47.2931 40.7281C47.1056 40.5486 47.0002 40.3051 47.0002 40.0513C47.0002 39.7974 47.1056 39.5539 47.2931 39.3744C47.4807 39.1948 47.735 39.094 48.0002 39.094C48.2655 39.094 48.5198 39.1948 48.7074 39.3744C48.8949 39.5539 49.0002 39.7974 49.0002 40.0513ZM44.0002 43.8803C44.0002 44.1342 43.8949 44.3777 43.7074 44.5572C43.5198 44.7367 43.2655 44.8376 43.0002 44.8376C42.735 44.8376 42.4807 44.7367 42.2931 44.5572C42.1056 44.3777 42.0002 44.1342 42.0002 43.8803C42.0002 43.6264 42.1056 43.3829 42.2931 43.2034C42.4807 43.0239 42.735 42.9231 43.0002 42.9231C43.2655 42.9231 43.5198 43.0239 43.7074 43.2034C43.8949 43.3829 44.0002 43.6264 44.0002 43.8803ZM44.0002 40.0513C44.0002 40.3051 43.8949 40.5486 43.7074 40.7281C43.5198 40.9077 43.2655 41.0085 43.0002 41.0085C42.735 41.0085 42.4807 40.9077 42.2931 40.7281C42.1056 40.5486 42.0002 40.3051 42.0002 40.0513C42.0002 39.7974 42.1056 39.5539 42.2931 39.3744C42.4807 39.1948 42.735 39.094 43.0002 39.094C43.2655 39.094 43.5198 39.1948 43.7074 39.3744C43.8949 39.5539 44.0002 39.7974 44.0002 40.0513ZM39.0002 43.8803C39.0002 44.1342 38.8949 44.3777 38.7074 44.5572C38.5198 44.7367 38.2655 44.8376 38.0002 44.8376C37.735 44.8376 37.4807 44.7367 37.2931 44.5572C37.1056 44.3777 37.0002 44.1342 37.0002 43.8803C37.0002 43.6264 37.1056 43.3829 37.2931 43.2034C37.4807 43.0239 37.735 42.9231 38.0002 42.9231C38.2655 42.9231 38.5198 43.0239 38.7074 43.2034C38.8949 43.3829 39.0002 43.6264 39.0002 43.8803ZM39.0002 40.0513C39.0002 40.3051 38.8949 40.5486 38.7074 40.7281C38.5198 40.9077 38.2655 41.0085 38.0002 41.0085C37.735 41.0085 37.4807 40.9077 37.2931 40.7281C37.1056 40.5486 37.0002 40.3051 37.0002 40.0513C37.0002 39.7974 37.1056 39.5539 37.2931 39.3744C37.4807 39.1948 37.735 39.094 38.0002 39.094C38.2655 39.094 38.5198 39.1948 38.7074 39.3744C38.8949 39.5539 39.0002 39.7974 39.0002 40.0513Z" fill="#3B3731" />
                                        <defs>
                                            <filter id="filter0_d_10_2243" x="0" y="0" width="86" height="86" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2243" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="5" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2243" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2243" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Booking Complete</p>
                                    <p class="dark-color-font">Your appointment with [Groomer Name] is complete.</p>
                                    <p class="simple-font muted-color">Just now | Bookings</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="payments"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="payments">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82" viewBox="0 0 82 82" fill="none">
                                        <g filter="url(#filter0_d_10_2261)">
                                            <circle cx="41" cy="37" r="28" fill="white" />
                                        </g>
                                        <path d="M31 33.5714V32.6571C31 31.3771 31 30.7371 31.2422 30.248C31.4556 29.8171 31.7944 29.4686 32.2133 29.2491C32.6889 29 33.3111 29 34.5556 29H47.4444C48.6889 29 49.3111 29 49.7856 29.2491C50.2044 29.4686 50.5444 29.8171 50.7578 30.248C51 30.736 51 31.376 51 32.6537V33.5714M31 33.5714H51M31 33.5714V41.3429C31 42.6229 31 43.2629 31.2422 43.752C31.4553 44.1821 31.7952 44.5317 32.2133 44.7509C32.6878 45 33.31 45 34.5522 45H47.4478C48.69 45 49.3111 45 49.7856 44.7509C50.2044 44.5314 50.5444 44.1817 50.7578 43.752C51 43.2629 51 42.6251 51 41.3474V33.5714M34.3333 40.4286H38.7778" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <defs>
                                            <filter id="filter0_d_10_2261" x="0" y="0" width="82" height="82" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2261" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="4" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2261" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2261" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Payment successful</p>
                                    <p class="dark-color-font">Your payment of £62 has been processed.</p>
                                    <p class="simple-font muted-color">Just now | Payments</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="payments"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="payments">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82" viewBox="0 0 82 82" fill="none">
                                        <g filter="url(#filter0_d_10_2267)">
                                            <circle cx="41" cy="37" r="28" fill="white" />
                                        </g>
                                        <path d="M31 33.5714V32.6571C31 31.3771 31 30.7371 31.2422 30.248C31.4556 29.8171 31.7944 29.4686 32.2133 29.2491C32.6889 29 33.3111 29 34.5556 29H47.4444C48.6889 29 49.3111 29 49.7856 29.2491C50.2044 29.4686 50.5444 29.8171 50.7578 30.248C51 30.736 51 31.376 51 32.6537V33.5714M31 33.5714H51M31 33.5714V41.3429C31 42.6229 31 43.2629 31.2422 43.752C31.4553 44.1821 31.7952 44.5317 32.2133 44.7509C32.6878 45 33.31 45 34.5522 45H47.4478C48.69 45 49.3111 45 49.7856 44.7509C50.2044 44.5314 50.5444 44.1817 50.7578 43.752C51 43.2629 51 42.6251 51 41.3474V33.5714M34.3333 40.4286H38.7778" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <defs>
                                            <filter id="filter0_d_10_2267" x="0" y="0" width="82" height="82" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2267" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="4" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2267" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2267" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Refund issued</p>
                                    <p class="dark-color-font">Your refund of £10 has been issued.</p>
                                    <p class="simple-font muted-color">10m ago | Payments</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="payments"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="payments">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82" viewBox="0 0 82 82" fill="none">
                                        <g filter="url(#filter0_d_10_2267)">
                                            <circle cx="41" cy="37" r="28" fill="white" />
                                        </g>
                                        <path d="M31 33.5714V32.6571C31 31.3771 31 30.7371 31.2422 30.248C31.4556 29.8171 31.7944 29.4686 32.2133 29.2491C32.6889 29 33.3111 29 34.5556 29H47.4444C48.6889 29 49.3111 29 49.7856 29.2491C50.2044 29.4686 50.5444 29.8171 50.7578 30.248C51 30.736 51 31.376 51 32.6537V33.5714M31 33.5714H51M31 33.5714V41.3429C31 42.6229 31 43.2629 31.2422 43.752C31.4553 44.1821 31.7952 44.5317 32.2133 44.7509C32.6878 45 33.31 45 34.5522 45H47.4478C48.69 45 49.3111 45 49.7856 44.7509C50.2044 44.5314 50.5444 44.1817 50.7578 43.752C51 43.2629 51 42.6251 51 41.3474V33.5714M34.3333 40.4286H38.7778" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <defs>
                                            <filter id="filter0_d_10_2267" x="0" y="0" width="82" height="82" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2267" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="4" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2267" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2267" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Payment failed</p>
                                    <p class="dark-color-font">There was an issue processing your payment.</p>
                                    <p class="simple-font muted-color">1h ago | Payments</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="reviews"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="reviews">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82" viewBox="0 0 82 82" fill="none">
                                        <g filter="url(#filter0_d_10_2279)">
                                            <circle cx="41" cy="37" r="28" fill="white" />
                                        </g>
                                        <path d="M41 28.2656C41.0605 28.2656 41.1132 28.2837 41.1504 28.3096C41.1824 28.3319 41.2169 28.3701 41.2393 28.4453L42.9014 34.0469C43.1217 34.7896 43.8044 35.2988 44.5791 35.2988H50.1377C50.3741 35.2991 50.4781 35.5978 50.293 35.7451L45.6445 39.4443C45.0864 39.8886 44.8539 40.6267 45.0566 41.3105L46.7939 47.1699C46.8631 47.404 46.5906 47.5881 46.3994 47.4365L42.0898 44.0068C41.452 43.4993 40.548 43.4993 39.9102 44.0068L35.6006 47.4365C35.4094 47.5881 35.1369 47.404 35.2061 47.1699L36.9434 41.3105C37.1461 40.6267 36.9135 39.8886 36.3555 39.4443L31.707 35.7451C31.5219 35.5978 31.6259 35.2991 31.8623 35.2988H37.4209C38.1956 35.2988 38.8783 34.7896 39.0986 34.0469L40.7607 28.4453C40.7831 28.3701 40.8176 28.3319 40.8496 28.3096C40.8868 28.2837 40.9395 28.2656 41 28.2656Z" stroke="#3B3731" stroke-width="1.5" />
                                        <defs>
                                            <filter id="filter0_d_10_2279" x="0" y="0" width="82" height="82" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2279" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="4" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2279" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2279" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Review reminder</p>
                                    <p class="dark-color-font">How was your booking with Claire Smith? Leave a review.</p>
                                    <p class="simple-font muted-color">1d ago | Reviews</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">Write a Review</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="reviews"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="reviews">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82" viewBox="0 0 82 82" fill="none">
                                        <g filter="url(#filter0_d_10_2279)">
                                            <circle cx="41" cy="37" r="28" fill="white" />
                                        </g>
                                        <path d="M41 28.2656C41.0605 28.2656 41.1132 28.2837 41.1504 28.3096C41.1824 28.3319 41.2169 28.3701 41.2393 28.4453L42.9014 34.0469C43.1217 34.7896 43.8044 35.2988 44.5791 35.2988H50.1377C50.3741 35.2991 50.4781 35.5978 50.293 35.7451L45.6445 39.4443C45.0864 39.8886 44.8539 40.6267 45.0566 41.3105L46.7939 47.1699C46.8631 47.404 46.5906 47.5881 46.3994 47.4365L42.0898 44.0068C41.452 43.4993 40.548 43.4993 39.9102 44.0068L35.6006 47.4365C35.4094 47.5881 35.1369 47.404 35.2061 47.1699L36.9434 41.3105C37.1461 40.6267 36.9135 39.8886 36.3555 39.4443L31.707 35.7451C31.5219 35.5978 31.6259 35.2991 31.8623 35.2988H37.4209C38.1956 35.2988 38.8783 34.7896 39.0986 34.0469L40.7607 28.4453C40.7831 28.3701 40.8176 28.3319 40.8496 28.3096C40.8868 28.2837 40.9395 28.2656 41 28.2656Z" stroke="#3B3731" stroke-width="1.5" />
                                        <defs>
                                            <filter id="filter0_d_10_2279" x="0" y="0" width="82" height="82" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2279" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="4" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2279" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2279" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Review received (provider)</p>
                                    <p class="dark-color-font">You’ve received a new review.</p>
                                    <p class="simple-font muted-color">2h ago | Reviews</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="updates"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="updates">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82" viewBox="0 0 82 82" fill="none">
                                        <g filter="url(#filter0_d_10_2291)">
                                            <circle cx="41" cy="37" r="28" fill="white" />
                                        </g>
                                        <path d="M46.9305 35.4737C47.5421 41.1316 49.9474 42.8421 49.9474 42.8421H31C31 42.8421 34.1579 40.5968 34.1579 32.7368C34.1579 30.9505 34.8232 29.2368 36.0074 27.9737C37.1916 26.7105 38.8 26 40.4737 26C40.8295 26 41.1804 26.0316 41.5263 26.0947M42.2947 46C42.1097 46.319 41.844 46.5838 41.5244 46.7679C41.2049 46.952 40.8425 47.0489 40.4737 47.0489C40.1049 47.0489 39.7425 46.952 39.4229 46.7679C39.1033 46.5838 38.8377 46.319 38.6526 46M47.8421 32.3158C48.6796 32.3158 49.4829 31.9831 50.0751 31.3909C50.6673 30.7986 51 29.9954 51 29.1579C51 28.3204 50.6673 27.5171 50.0751 26.9249C49.4829 26.3327 48.6796 26 47.8421 26C47.0046 26 46.2014 26.3327 45.6091 26.9249C45.0169 27.5171 44.6842 28.3204 44.6842 29.1579C44.6842 29.9954 45.0169 30.7986 45.6091 31.3909C46.2014 31.9831 47.0046 32.3158 47.8421 32.3158Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <defs>
                                            <filter id="filter0_d_10_2291" x="0" y="0" width="82" height="82" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2291" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="4" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2291" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2291" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Referral reward</p>
                                    <p class="dark-color-font">You’ve earned £10 in referral credit.</p>
                                    <p class="simple-font muted-color">30m ago | Updates</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="updates"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="updates">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82" viewBox="0 0 82 82" fill="none">
                                        <g filter="url(#filter0_d_10_2291)">
                                            <circle cx="41" cy="37" r="28" fill="white" />
                                        </g>
                                        <path d="M46.9305 35.4737C47.5421 41.1316 49.9474 42.8421 49.9474 42.8421H31C31 42.8421 34.1579 40.5968 34.1579 32.7368C34.1579 30.9505 34.8232 29.2368 36.0074 27.9737C37.1916 26.7105 38.8 26 40.4737 26C40.8295 26 41.1804 26.0316 41.5263 26.0947M42.2947 46C42.1097 46.319 41.844 46.5838 41.5244 46.7679C41.2049 46.952 40.8425 47.0489 40.4737 47.0489C40.1049 47.0489 39.7425 46.952 39.4229 46.7679C39.1033 46.5838 38.8377 46.319 38.6526 46M47.8421 32.3158C48.6796 32.3158 49.4829 31.9831 50.0751 31.3909C50.6673 30.7986 51 29.9954 51 29.1579C51 28.3204 50.6673 27.5171 50.0751 26.9249C49.4829 26.3327 48.6796 26 47.8421 26C47.0046 26 46.2014 26.3327 45.6091 26.9249C45.0169 27.5171 44.6842 28.3204 44.6842 29.1579C44.6842 29.9954 45.0169 30.7986 45.6091 31.3909C46.2014 31.9831 47.0046 32.3158 47.8421 32.3158Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <defs>
                                            <filter id="filter0_d_10_2291" x="0" y="0" width="82" height="82" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2291" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="4" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2291" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2291" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Promotion</p>
                                    <p class="dark-color-font">🎉 Get 10% off your next booking!</p>
                                    <p class="simple-font muted-color">1h ago | Updates</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)" data-category="updates"></div>
                    <div class="notification-list-item noshadow mt-4" data-category="updates">
                        <div class="notification-list-item-inner d-flex align-items-center justify-content-between cursor">
                            <div class="notification-list-item-inner-left d-flex align-items-center gap-20">
                                <div class="notification-list-item-inner-left-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="82" viewBox="0 0 82 82" fill="none">
                                        <g filter="url(#filter0_d_10_2291)">
                                            <circle cx="41" cy="37" r="28" fill="white" />
                                        </g>
                                        <path d="M46.9305 35.4737C47.5421 41.1316 49.9474 42.8421 49.9474 42.8421H31C31 42.8421 34.1579 40.5968 34.1579 32.7368C34.1579 30.9505 34.8232 29.2368 36.0074 27.9737C37.1916 26.7105 38.8 26 40.4737 26C40.8295 26 41.1804 26.0316 41.5263 26.0947M42.2947 46C42.1097 46.319 41.844 46.5838 41.5244 46.7679C41.2049 46.952 40.8425 47.0489 40.4737 47.0489C40.1049 47.0489 39.7425 46.952 39.4229 46.7679C39.1033 46.5838 38.8377 46.319 38.6526 46M47.8421 32.3158C48.6796 32.3158 49.4829 31.9831 50.0751 31.3909C50.6673 30.7986 51 29.9954 51 29.1579C51 28.3204 50.6673 27.5171 50.0751 26.9249C49.4829 26.3327 48.6796 26 47.8421 26C47.0046 26 46.2014 26.3327 45.6091 26.9249C45.0169 27.5171 44.6842 28.3204 44.6842 29.1579C44.6842 29.9954 45.0169 30.7986 45.6091 31.3909C46.2014 31.9831 47.0046 32.3158 47.8421 32.3158Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                        <defs>
                                            <filter id="filter0_d_10_2291" x="0" y="0" width="82" height="82" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                                <feMorphology radius="5" operator="dilate" in="SourceAlpha" result="effect1_dropShadow_10_2291" />
                                                <feOffset dy="4" />
                                                <feGaussianBlur stdDeviation="4" />
                                                <feComposite in2="hardAlpha" operator="out" />
                                                <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.05 0" />
                                                <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_10_2291" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_10_2291" result="shape" />
                                            </filter>
                                        </defs>
                                    </svg>
                                </div>
                                <div class="notification-list-item-inner-left-content">
                                    <p class="small-bolder-color-font">Account update</p>
                                    <p class="dark-color-font">Your profile has been updated successfully.</p>
                                    <p class="simple-font muted-color">1h ago | Updates</p>
                                </div>
                            </div>
                            <div class="notification-list-item-inner-right">
                                <a href="" class="normal-font-bold  link-tag">View Booking</a>
                            </div>
                        </div>
                    </div>
                    <div class="section-divider mt-2 mb-2" style="background-color: rgba(59, 55, 49, 0.13)"></div>


                    <div class="d-flex justify-content-center mt-5">
                        <button class="normal-font-bold btn-custom btn-no-bg">Load More</button>
                    </div>

                </div>
            </div>
            <div class="col-lg-2"></div>
        </div>
    </div>
@endsection

@push('script')
    <script src="{{ asset('js/messages_notification/common.js') }}"></script>

<script>
        toggleDisplay('.sort-by', '.sort-by-filter');
        toggleDisplay('.mark-notification-dots-svg', '.clear-read.dropdown');
    </script>


    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const copyBtn = document.querySelector('.copy-button');
            const input = document.getElementById('promo_code');

            copyBtn.addEventListener('click', () => {
                // Select the input value
                input.select();
                input.setSelectionRange(0, 99999); // for mobile devices

                // Copy to clipboard
                navigator.clipboard.writeText(input.value)
                    .then(() => {
                        // Optional: give feedback
                        copyBtn.textContent = 'Copied!';
                        setTimeout(() => {
                            copyBtn.textContent = 'Copy';
                        }, 1500);
                    })
                    .catch(err => {
                        console.error('Failed to copy: ', err);
                    });
            });
        });


        // tab buttons functionality
        document.addEventListener('DOMContentLoaded', function() {

            const tabs = document.querySelectorAll('.tags[data-tab]');
            const headings = document.querySelector('#notifications-heading');

            // Add transition style to all items once on load
            document.querySelectorAll('[data-category]').forEach(function(item) {
                item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            });

            tabs.forEach(function(tab) {
                tab.addEventListener('click', function() {

                    // Update active tab styling
                    tabs.forEach(function(t) {
                        t.classList.remove('active');
                        t.querySelector('p').classList.remove('normal-font-weight-active-color');
                        t.querySelector('p').classList.add('normal-font-weight', 'muted-color');
                        const count = t.querySelector('.count');
                        if (count) count.classList.remove('active');
                    });

                    tab.classList.add('active');
                    tab.querySelector('p').classList.add('normal-font-weight-active-color');
                    tab.querySelector('p').classList.remove('normal-font-weight', 'muted-color');
                    const activeCount = tab.querySelector('.count');
                    if (activeCount) activeCount.classList.add('active');

                    // Fade out all first
                    const selected = tab.getAttribute('data-tab');
                    const items = document.querySelectorAll('[data-category]');

                    headings.innerText = selected.charAt(0).toUpperCase() + selected.slice(1) + ' Notifications';

                    items.forEach(function(item) {
                        item.style.opacity = '0';
                        item.style.transform = 'translateY(6px)';
                    });

                    // Then after fade out, show/hide and fade back in
                    setTimeout(function() {
                        items.forEach(function(item) {
                            if (selected === 'all' || item.getAttribute('data-category') === selected) {
                                item.style.display = '';
                                // Small delay so display change registers before transition
                                requestAnimationFrame(function() {
                                    requestAnimationFrame(function() {
                                        item.style.opacity = '1';
                                        item.style.transform = 'translateY(0)';
                                    });
                                });
                            } else {
                                item.style.display = 'none';
                                item.style.opacity = '0';
                                item.style.transform = 'translateY(6px)';
                            }
                        });
                    }, 250);

                });
            });

        });


        document.addEventListener('DOMContentLoaded', function() {

            const sortBy = document.querySelector('.sort-by');
            const dropdown = document.querySelector('.sort-by-filter');
            const radios = document.querySelectorAll('input[name="groomer-sort"]');
            const filtersContainer = document.querySelector('.filters');

            function createPill(value, label) {
                const existingPill = filtersContainer.querySelector('.filter[data-sort]');
                if (existingPill) existingPill.remove();

                const pill = document.createElement('div');
                pill.className = 'filter cursor d-flex align-items-center gap-10';
                pill.setAttribute('data-sort', value);
                pill.innerHTML = `${label} <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none"><path d="M0.5 7.57L7.572 0.5M0.5 0.5L7.572 7.57" stroke="white" stroke-linecap="round" /></svg>`;

                pill.addEventListener('click', function() {
                    document.querySelector(`input[name="groomer-sort"][value="${value}"]`).checked = false;
                    pill.remove();
                });

                filtersContainer.appendChild(pill);
            }

            // Default checked pill on load
            radios.forEach(function(radio) {
                if (radio.checked) {
                    createPill(radio.value, radio.closest('label').querySelector('.option-text').textContent.trim());
                }
            });

            // Toggle open/close
            sortBy.addEventListener('click', function(e) {
                if (dropdown.contains(e.target)) return;
                dropdown.classList.toggle('open');
            });

            // Close on outside click
            document.addEventListener('click', function(e) {
                if (!sortBy.contains(e.target)) dropdown.classList.remove('open');
            });

            // On selection
            radios.forEach(function(radio) {
                radio.addEventListener('change', function() {
                    createPill(this.value, this.closest('label').querySelector('.option-text').textContent.trim());
                    setTimeout(() => dropdown.classList.remove('open'), 10);
                });

                radio.closest('label').addEventListener('click', function(e) {
                    e.stopPropagation();
                });
            });

        });
    </script>
@endpush
