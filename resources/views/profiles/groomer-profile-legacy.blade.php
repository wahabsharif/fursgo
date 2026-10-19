@extends('layouts.app')

@section('title', 'Fursgo - Groomer Profile')

@section('body_class', 'groomer-profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/groomer_space_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/choose_partner_modal.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>

        /* Apply grayscale filter only to tiles, not markers */
        .inverted-radius {
            --r: 21px;
            --s: 18px;
            --x: 170px;
            --y: 38px;
            width: 100%;
            height: 400px;
            aspect-ratio: 1;
            border-radius: var(--r);
            --_m: /calc(2*var(--r)) calc(2*var(--r)) radial-gradient(#000 70%, #0000 72%);
            --_g: conic-gradient(at var(--r) var(--r), #000 75%, #0000 0);
            --_d: (var(--s) + var(--r));
            mask: calc(var(--_d) + var(--x)) 0 var(--_m), 0 calc(var(--_d) + var(--y)) var(--_m), radial-gradient(var(--s) at 0 0, #0000 99%, #000 calc(100% + 1px)) calc(var(--r) + var(--x)) calc(var(--r) + var(--y)), var(--_g) calc(var(--_d) + var(--x)) 0, var(--_g) 0 calc(var(--_d) + var(--y));
            mask-repeat: no-repeat;
        }

        .modal-top-card {
            border-radius: 10px;
            background: rgba(217, 217, 217, 0.13);
            padding: 10px 22px;
        }

        .name-reviews {
            width: 25%;
        }

        .modal-card-right-section,
        .details-section {
            width: 100%;
        }


        /* mid section css starts */
        .heading-count span.count {
            padding: 2px 0 0 0;
            border-radius: 100px;
            background: #F8F8F8;
            width: 36px;
            height: 24px;
            text-align: center;
            color: #999794;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .groomer-tabs {
            display: flex;
            gap: 1.5em 1em;
            height: 48px;
            border-radius: 75px;
            background: #F7F7F5;
            padding: 1.5px;
        }

        .groomer-tabs a.active {
            background: var(--groomer-color);
            color: #fff;
            animation: shake 0.3s;
        }

        /* Shake keyframes */
        @keyframes shake {
            0% {
                transform: translateX(0);
            }

            25% {
                transform: translateX(-3px);
            }

            50% {
                transform: translateX(3px);
            }

            75% {
                transform: translateX(-3px);
            }

            100% {
                transform: translateX(0);
            }
        }

        .groomer-tabs a {
            border-radius: 75px;
            color: rgba(59, 55, 49, 0.50);
            text-align: center;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            padding: 10px;
            display: block;
            height: 44px;
            width: 155px;
            cursor: pointer;
        }

        /* venu-sorting-section */

        .sort-by,
        .venue-selection {
            position: relative;
            display: flex;
            align-items: center;
        }

        .venue-selection,
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

        /* venu-sorting-section */


        .filter .selected-item {
            border-radius: 100px;
            border: 1px solid #FBAC83 !important;
            background: #fff;
            padding: 10px 20px;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-weight: 500;
            color: #FBAC83 !important;
            padding: 10px;
        }

        .filter .selected-item p {
            color: #FBAC83 !important;
            margin: 0;
        }

        /* mid section css Ends */


        /* card css  */

        .card {
            width: 100%;
            display: flex;
            gap: 10px;
            background: #FFF;
            border-radius: 16px;
            padding: 18px;
            border: 1px solid #D4D4D4;
            cursor: pointer;
        }

        .card:hover {
            border: 1px solid var(--active-bg);
            box-shadow: 0 5px 20px 0 rgba(0, 0, 0, 0.15);
        }

        /* map card styling */

        .custom-popup .leaflet-popup-content-wrapper {
            padding: 5px;
            border-radius: 5px;
            width: min-content;
            background: #FFF;
            box-shadow: 0 0 8px 0 rgba(0, 0, 0, 0.05);
            height: auto;
        }

        .custom-popup .leaflet-popup-content {
            margin: 0;
            min-width: 200px;
        }

        .custom-popup .leaflet-popup-tip {
            background: white;
        }

        .map-top-left-svg {
            position: absolute;
            top: 0;
            left: 2px;
        }

        a.leaflet-popup-close-button {
            display: none;
        }

        .leaflet-popup.custom-popup.leaflet-zoom-animated {
            bottom: 8px !important;
        }

        #groomer_book_space .map-wrapper.modal-map-wrapper {
            position: relative;
            min-height: 565px;
            border-radius: 10px;
            overflow: hidden;
        }

        #groomer_book_space .map-zoom-hint {
            position: absolute;
            left: 14px;
            bottom: 14px;
            z-index: 1000;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid rgba(255, 255, 255, 0.55);
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 4px 16px rgba(31, 31, 31, 0.08);
            font-family: Lato, sans-serif;
            font-size: 12px;
            color: #3B3731;
        }

        /* map card styling */


        .card .left {
            width: 180px;
            min-width: 170px;
            display: flex;
            align-items: flex-start;
            position: relative
        }

        .card .left image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .card .top-left-svg {
            position: absolute;
            left: 6%;
            top: 1%;
        }

        .card .right {
            flex: 1;
        }

        .card .top-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .card .tags {
            display: flex;
            gap: 10px
        }

        .card .tag {
            background: #FFA899;
            border: 1px solid rgba(246, 168, 127, 0.15);
            color: #FFF;
            padding: 6px 10px;
            border-radius: 999px;
            text-align: center;
            font-family: "Lato";
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .icons button.first-icon,
        .icons button.second-icon,
        .icons button.third-icon {
            width: 24px;
            height: 24px;
            padding: 0;
            border: none;
            border-radius: 50%;
        }

        .icons button.first-icon {
            background: #cbdce8;
        }

        .icons button.second-icon {
            background: #ffa899;
        }

        .icons button.third-icon {
            background: #D8E8B7;
        }


        .card .name {
            color: var(--font-color);
            font-family: "Lato";
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin: 10px 0 0px;
        }

        .card .studio {
            color: #9D9B98;
            font-family: "Lato";
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .card .meta {
            display: flex;
            gap: 14px;
            align-items: center;
            color: var(--font-color);
            font-family: "Lato";
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .card .meta .distance {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .card .meta .rating {
            display: flex;
            align-items: center;
            gap: 6px
        }

        .experience {
            margin: 25px 0 25px 0;
            color: var(--font-color);
            font-family: "Lato";
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .availability {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 6px;
            color: #BACF8E;
            font-family: "Lato";
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;

        }

        .slots {
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 8px;
            width: 100%;
            max-width: 278px;
        }

        #groomer-list-view .slots {
            width: 100%;
        }

        #groomer-list-view .price {
            width: 12%;
        }

        #groomer-list-view .name {
            margin: 20px 0 0px;
        }

        #groomer-list-view .slots {
            width: 100%;
        }

        .slot {
            color: #BACF8E;
            text-align: center;
            font-family: "Lato";
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            border-radius: 68px;
            border: 1px solid #BACF8E;
            /* padding: 5px 10px 5px 10px; */
            padding: 5px;
        }

        .slot.highlight {
            background-color: #D1E3A6;
            color: #FFF;
            border: none;
        }

        .slot {
            background-color: #fff;
        }

        .price {
            color: var(--font-color);
            font-family: "Lato";
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin-left: auto;
            align-self: end;
        }

        .price span:not(.time-frame) {
            text-decoration-line: underline;
            text-decoration-style: solid;
            text-decoration-skip-ink: auto;
            text-decoration-thickness: auto;
            text-underline-offset: auto;
            text-underline-position: from-font;
        }

        button.load-more {
            padding: 10px 20px;
            border-radius: 75px;
            border: 1px solid var(--font-color);
            background: none;
            color: var(--font-color);
            text-align: center;
            font-family: "Lato";
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
        }

        /* space cards  */

        .space-cards.card,
        .space-cards-list-view.card {
            gap: 20px;
        }

        .space-cards .left {
            width: 100%;
        }

        .space-cards .top-left-svg {
            position: absolute;
            left: 2%;
            top: 1%;
        }

        .space-cards .slots {
            width: 60%;
        }

        .space-cards .price {
            margin-top: 10px;
            margin-left: 0;
            align-self: flex-start;
        }

        .space-cards-list-view .price {
            margin-left: auto;
            align-self: auto;
        }

        .list-view-desc {
            color: var(--font-color);
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .space-cards-list-view.specific .list-view-desc {
            padding: 44px 0 44px 0;
        }

        .card.specific .price {
            margin-left: auto;
            align-self: auto;
        }

        .card.specific .left {
            width: auto;
        }

        .hosted-by {
            color: var(--font-color);
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .hosted-by span {
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .time-frame {
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .amenities {
            color: var(--font-color);
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .space-cards-list-view .left {
            width: auto;
        }

        .space-cards-list-view .list-view-desc {
            padding: 15px 0 15px 0;
        }

        .space-cards-list-view p.amenities.d-flex {
            padding: 10px 0;
        }

        .space-cards-list-view .top-left-svg {
            position: absolute;
            left: 2%;
            top: 1%;
        }

        /* space cards css end */

        .space-scroll-wrapper {
            position: relative;
            overflow: hidden;
            width: 100%;
        }

        .space-scroll {
            overflow-x: auto;
            overflow-y: hidden;
            scrollbar-width: none;
        }

        .space-scroll::-webkit-scrollbar {
            display: none;
        }

        /* bottom controls */
        .scroll-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-top: 14px;
        }

        /* arrows */
        .scroll-btn {
            background: none;
            border: none;
            padding: 0;
            cursor: pointer;
            z-index: 2;
        }

        /* track */
        .scroll-track {
            position: relative;
            flex: 1;
            height: 5px;
            background: #eee;
            border-radius: 10px;
            pointer-events: none;
            /* allow clicks to pass through */
        }

        /* progress bar */
        .scroll-progress {
            position: absolute;
            top: 0;
            left: 0;
            height: 5px;
            border-radius: 21px;
            background: #FFC97A;
            min-width: 40px;
        }

        .map-image {
            height: 565px;
            aspect-ratio: 107 / 32;
            border-radius: 10px 0 0 10px;
            object-fit: none;
        }

    </style>
@endpush

@section('content')
<div class="container mt-5" style="padding: 0 80px">

        <!-- test -->
        <!-- <button class="normal-font-bold btn-custom btn-no-bg text-center mt-3" data-modal-open="social_media_share" style="color:#FBAC83;border:1px solid #FBAC83">Submit request</button> -->

        <!-- Modal  -->

        <div class="modal" id="social_media_share">
            <div class="modal-content">
                <div class="modal-header d-flex align-items-center justify-content-between">
                    <p class="fs-18-600">Share</p>
                    <div class="groomer-cross-svg cursor" data-modal-close>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8" />
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="section-divider mt-3" style="background-color: #DFDFDF"></div>
                <div class="modal-main-body mt-3">
                    <p class="fs-14-400-f-color">Share this link via</p>
                    <div class="social-media-icons mt-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex flex-column align-items-center gap-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                <path d="M40 20C40 8.95438 31.0456 0 20 0C8.95438 0 0 8.95438 0 20C0 29.9825 7.31375 38.2567 16.875 39.757V25.7813H11.7969V20H16.875V15.5938C16.875 10.5813 19.8609 7.8125 24.4294 7.8125C26.6175 7.8125 28.9062 8.20313 28.9062 8.20313V13.125H26.3844C23.8998 13.125 23.125 14.6667 23.125 16.2484V20H28.6719L27.7852 25.7813H23.125V39.757C32.6863 38.2567 40 29.9827 40 20Z" fill="#1877F2" />
                                <path d="M27.7852 25.7812L28.6719 20H23.125V16.2484C23.125 14.6666 23.8998 13.125 26.3844 13.125H28.9062V8.20312C28.9062 8.20312 26.6175 7.8125 24.4292 7.8125C19.8609 7.8125 16.875 10.5813 16.875 15.5938V20H11.7969V25.7812H16.875V39.757C17.9088 39.919 18.9536 40.0003 20 40C21.0464 40.0003 22.0912 39.919 23.125 39.757V25.7812H27.7852Z" fill="white" />
                            </svg>
                            <p class="fs-12-400-f-color">Facebook</p>
                        </div>
                        <div class="d-flex flex-column align-items-center gap-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                <circle cx="20" cy="20" r="20" fill="url(#insta_grad)" />
                                <rect x="11" y="11" width="18" height="18" rx="5" ry="5" stroke="white" stroke-width="1.5" fill="none" />
                                <circle cx="20" cy="20" r="4" stroke="white" stroke-width="1.5" fill="none" />
                                <circle cx="25.5" cy="14.5" r="1" fill="white" />
                                <defs>
                                    <linearGradient id="insta_grad" x1="0" y1="40" x2="40" y2="0" gradientUnits="userSpaceOnUse">
                                        <stop offset="0.0885197" stop-color="#FFD453" />
                                        <stop offset="0.310382" stop-color="#FA5148" />
                                        <stop offset="0.658561" stop-color="#C938A9" />
                                        <stop offset="1" stop-color="#4A61CA" />
                                    </linearGradient>
                                </defs>
                            </svg>
                            <p class="fs-12-400-f-color">Instagram</p>
                        </div>
                        <div class="d-flex flex-column align-items-center gap-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                <circle cx="20" cy="20" r="20" fill="#3B3731" />
                                <path d="M28.6006 10.5L21.9512 18.1426L21.6855 18.4482L21.9297 18.7715L29.998 29.5H24.459L19.2881 22.7061L18.916 22.2178L18.5137 22.6807L12.5811 29.5H10.5293L17.6914 21.2646L17.959 20.957L17.7129 20.6338L10.0088 10.502H15.7119L20.3643 16.6904L20.7354 17.1846L21.1406 16.7188L26.5527 10.5H28.6006ZM12.5371 12.2275L24.7402 28.2725L24.8906 28.4697H28.0107L27.4082 27.6689L15.3398 11.624L15.1895 11.4248H11.9268L12.5371 12.2275Z" fill="#F5F5F4" stroke="#F5F5F4" />
                            </svg>
                            <p class="fs-12-400-f-color">X</p>
                        </div>
                        <div class="d-flex flex-column align-items-center gap-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                <circle cx="20" cy="20" r="20" fill="#3B3731" />
                                <g transform="translate(11, 10)">
                                    <g clip-path="url(#clip_tiktok)">
                                        <path d="M13.3397 7.20142C14.653 8.12177 16.2619 8.66328 17.9995 8.66328V5.38535C17.6706 5.38548 17.3426 5.35182 17.0209 5.28494V7.86515C15.2834 7.86515 13.6747 7.3237 12.3611 6.40342V13.0928C12.3611 16.4392 9.59391 19.1518 6.18066 19.1518C4.90709 19.1518 3.72331 18.7743 2.73999 18.1269C3.86232 19.252 5.42747 19.9498 7.15899 19.9498C10.5725 19.9498 13.3398 17.2373 13.3398 13.8907V7.20142H13.3397ZM14.5469 3.89439C13.8757 3.17556 13.435 2.24659 13.3397 1.21956V0.797974H12.4123C12.6458 2.10328 13.442 3.21846 14.5469 3.89439ZM4.89886 15.5591C4.52384 15.0771 4.32114 14.4874 4.32209 13.8811C4.32209 12.3507 5.58779 11.1097 7.14936 11.1097C7.44033 11.1095 7.72958 11.1533 8.00696 11.2396V7.88832C7.68282 7.8448 7.35572 7.82625 7.02877 7.83308V10.4415C6.75126 10.3553 6.46186 10.3115 6.17075 10.3117C4.60925 10.3117 3.34362 11.5525 3.34362 13.0832C3.34362 14.1656 3.97622 15.1026 4.89886 15.5591Z" fill="#FF004F" />
                                        <path d="M12.3611 6.40335C13.6748 7.32363 15.2833 7.86508 17.0209 7.86508V5.28487C16.051 5.08232 15.1924 4.58549 14.5469 3.89439C13.4419 3.21839 12.6458 2.10322 12.4123 0.797974H9.97648V13.8906C9.97092 15.4169 8.70741 16.6528 7.14921 16.6528C6.23107 16.6528 5.4153 16.2238 4.89872 15.559C3.97622 15.1026 3.34355 14.1655 3.34355 13.0833C3.34355 11.5527 4.60917 10.3118 6.17067 10.3118C6.46985 10.3118 6.7582 10.3574 7.0287 10.4416V7.83315C3.67535 7.90108 0.978516 10.5871 0.978516 13.8907C0.978516 15.5398 1.65007 17.0347 2.74005 18.1271C3.72338 18.7743 4.90709 19.1519 6.18073 19.1519C9.59405 19.1519 12.3612 16.4391 12.3612 13.0928L12.3611 6.40335Z" fill="white" />
                                        <path d="M17.0209 5.28483V4.58731C16.1463 4.58855 15.289 4.34844 14.5469 3.89441C15.2037 4.59939 16.0687 5.08554 17.0209 5.28497M12.4123 0.797931C12.39 0.673212 12.373 0.547661 12.3611 0.421586V0H8.99775V13.0928C8.99241 14.619 7.72889 15.8548 6.17063 15.8548C5.72887 15.8555 5.29315 15.7542 4.89867 15.5592C5.41526 16.2238 6.23102 16.6528 7.14916 16.6528C8.70729 16.6528 9.97094 15.417 9.97643 13.8907V0.798L12.4123 0.797931ZM7.02886 7.8331V7.09041C6.74781 7.05278 6.46448 7.03393 6.18082 7.034C2.76715 7.034 0 9.74669 0 13.0928C0 15.1906 1.08752 17.0394 2.74015 18.127C1.65016 17.0347 0.978609 15.5397 0.978609 13.8906C0.978609 10.5872 3.67538 7.90103 7.02886 7.8331Z" fill="#00F2EA" />
                                    </g>
                                    <defs>
                                        <clipPath id="clip_tiktok">
                                            <rect width="18" height="20" fill="white" />
                                        </clipPath>
                                    </defs>
                                </g>
                            </svg>
                            <p class="fs-12-400-f-color">Tiktok</p>
                        </div>
                        <div class="d-flex flex-column align-items-center gap-10">
                            <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                                <circle cx="20" cy="20" r="20" fill="#48C857" />
                                <path d="M20.0402 10.0001C14.5528 10.0001 10.0804 14.45 10.0804 19.91C10.0804 21.66 10.5427 23.36 11.407 24.86L10 30L15.2764 28.62C16.7337 29.41 18.3719 29.83 20.0402 29.83C25.5276 29.83 30 25.38 30 19.92C30 17.27 28.9648 14.78 27.0854 12.91C26.1639 11.9842 25.0664 11.25 23.8568 10.7504C22.6472 10.2508 21.3498 9.99574 20.0402 10.0001ZM20.0503 11.67C22.2613 11.67 24.3317 12.53 25.8995 14.09C26.6687 14.8556 27.2786 15.7646 27.6943 16.765C28.1099 17.7654 28.3231 18.8375 28.3216 19.92C28.3216 24.46 24.603 28.15 20.0402 28.15C18.5528 28.15 17.0955 27.76 15.8291 27L15.5276 26.83L12.392 27.65L13.2261 24.61L13.0251 24.29C12.1955 22.9784 11.7565 21.4598 11.7588 19.91C11.7688 15.37 15.4774 11.67 20.0503 11.67ZM16.5126 15.33C16.3518 15.33 16.0804 15.39 15.8492 15.64C15.6281 15.89 14.9749 16.5 14.9749 17.71C14.9749 18.93 15.8693 20.1 15.9799 20.27C16.1206 20.44 17.7487 22.94 20.2513 24C20.8442 24.27 21.3065 24.42 21.6683 24.53C22.2613 24.72 22.804 24.69 23.2362 24.63C23.7186 24.56 24.7035 24.03 24.9146 23.45C25.1256 22.87 25.1256 22.38 25.0653 22.27C24.995 22.17 24.8342 22.11 24.5829 22C24.3317 21.86 23.1055 21.26 22.8844 21.18C22.6533 21.1 22.5126 21.06 22.3216 21.3C22.1608 21.55 21.6784 22.11 21.5377 22.27C21.3869 22.44 21.2462 22.46 21.005 22.34C20.7437 22.21 19.9397 21.95 18.995 21.11C18.2513 20.45 17.7588 19.64 17.608 19.39C17.4874 19.15 17.598 19 17.7186 18.89C17.8291 18.78 17.9899 18.6 18.0905 18.45C18.2211 18.31 18.2613 18.2 18.3417 18.04C18.4221 17.87 18.3819 17.73 18.3216 17.61C18.2613 17.5 17.7588 16.26 17.5477 15.77C17.3467 15.29 17.1457 15.35 16.9849 15.34C16.8442 15.34 16.6834 15.33 16.5126 15.33Z" fill="white" />
                            </svg>
                            <p class="fs-12-400-f-color">Whatsapp</p>
                        </div>
                    </div>
                    <div class="copy-link-wrapper mt-4">
                        <span class="fs-14-400-f-color">Or copy link</span>
                        <div class="copy-link-box">
                            <div class="copy-link-input-area">
                                <!-- link icon SVG here -->
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                    <path d="M9.41429 3.01429L11.2429 1.18571C12.1571 0.271429 13.9857 0.271429 14.9 1.18571L15.8143 2.1C16.7286 3.01429 16.7286 4.84286 15.8143 5.75714L11.2429 10.3286C10.3286 11.2429 8.5 11.2429 7.58571 10.3286M7.58571 13.9857L5.75714 15.8143C4.84286 16.7286 3.01429 16.7286 2.1 15.8143L1.18571 14.9C0.271429 13.9857 0.271429 12.1571 1.18571 11.2429L5.75714 6.67143C6.67143 5.75714 8.5 5.75714 9.41429 6.67143" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <input class="copy-link-input fs-14-400-f-color" type="text" value="groomerpage/share-link-fursgo.com" readonly />
                            </div>
                            <button class="copy-btn" onclick="copyLink()">Copy</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="modal" id="block_profile_modal">
            <div class="modal-content">
                <div class="block-modal-header">
                    <h4 class="fs-18-600">Block this Groomer?</h4>
                    <div class="groomer-cross-svg cursor" data-modal-close>
                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                            <circle cx="10" cy="10" r="9.5" transform="matrix(-1 0 0 1 20 0)" fill="#F3F3F3" stroke="#E8E8E8" />
                            <path d="M13.1465 13.24L10.0001 10.0936L13.0937 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M7.09375 13.24L10.2402 10.0936L7.14657 6.99999" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                </div>
                <div class="block-modal-divider"></div>
                <div class="block-modal-profile">
                    <img class="block-modal-avatar" src="{{ asset('images/groomer-profile.png') }}" alt="Sarah's Grooming Studio">
                    <div>
                        <p class="block-modal-name">Sarah's Grooming Studio <span class="dot">·</span> <span class="block-modal-type">Groomer</span></p>
                        <p class="block-modal-owner">Sarah W.</p>
                    </div>
                </div>
                <p class="block-modal-info">You won't see this Groomer in search, and Sarah W. won't be able to message you. You can unblock anytime in settings.</p>
                <p class="block-modal-reason-label">Reason (optional)</p>
                <div class="block-modal-reasons">
                    <label class="block-modal-reason">
                        <input type="radio" name="block_reason" value="not_interested" checked>
                        <span class="block-modal-radio"></span>
                        <span>I'm not interested in this Groomer</span>
                    </label>
                    <label class="block-modal-reason">
                        <input type="radio" name="block_reason" value="unwanted_messages">
                        <span class="block-modal-radio"></span>
                        <span>Unwanted messages from Groomer</span>
                    </label>
                    <label class="block-modal-reason">
                        <input type="radio" name="block_reason" value="inappropriate">
                        <span class="block-modal-radio"></span>
                        <span>Inappropriate or offensive content</span>
                    </label>
                    <label class="block-modal-reason">
                        <input type="radio" name="block_reason" value="other">
                        <span class="block-modal-radio"></span>
                        <span>Other</span>
                    </label>
                </div>
                <label class="block-modal-report">
                    <input type="checkbox" name="also_report" checked>
                    <span class="block-modal-checkbox">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 12 12" fill="none">
                            <path d="M2.2 6.2L4.7 8.7L9.8 3.3" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </span>
                    <span>Also report this Groomer to Fursgo for review</span>
                </label>
                <div class="block-modal-actions">
                    <button type="button" class="block-modal-cancel" data-modal-close>Cancel</button>
                    <button type="button" class="block-modal-confirm" data-home-url="{{ url('/') }}">Block Groomer</button>
                </div>
            </div>
        </div>

        <!-- Modal  -->

        <!-- <button class="book-btn" data-modal-open="groomer_book_space">Book a Space for Your Groomer</button> -->

        <!-- Modal 1 -->

        <div class="modal" id="groomer_book_space" data-partner-checkout="{{ url('/checkout_booking_groomer') }}/">
            <div class="modal-content">
                <div class="space-choose-head">
                    <button type="button" class="space-go-back" id="goBack">&larr; Go back</button>
                    <div class="space-choose-title">
                        <h1>Choose a grooming space</h1>
                        <p>We've matched 3 nearby spaces that fit your booking with Sarah.</p>
                    </div>
                    <button type="button" class="space-close" data-modal-close aria-label="Close">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                            <path d="M3.2 12.8L12.8 3.2M3.2 3.2L12.8 12.8" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="modal-top-card mt-4">
                    <div class="modal-card-left-section">
                        <div class="avatar-wrap">
                            <img class="avatar" src="{{ asset('images/groomer-profile.png') }}" alt="Sarah's avatar">
                            <div class="badge-shield" title="Verified">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="33" viewBox="0 0 30 33" fill="none">
                                    <ellipse cx="15.873" cy="16.5256" rx="9.3645" ry="8.81365" fill="white"></ellipse>
                                    <path d="M15.44 0.185076C15.1841 0.0638192 14.9099 0 14.6235 0C14.3372 0 14.063 0.0638192 13.8071 0.185076L2.3337 5.28423C0.993208 5.87775 -0.00606541 7.26263 2.77146e-05 8.93469C0.0304934 15.2656 2.51649 26.8487 13.015 32.1138C14.0325 32.6244 15.2146 32.6244 16.2321 32.1138C26.7306 26.8487 29.2166 15.2656 29.247 8.93469C29.2531 7.26263 28.2539 5.87775 26.9134 5.28423L15.44 0.185076ZM8.82897 18.2651C9.12144 18.3416 9.43219 18.3799 9.74903 18.3799C11.8999 18.3799 13.6486 16.5483 13.6486 14.2955V10.2111H16.3418C17.0791 10.2111 17.7554 10.645 18.0844 11.3407L18.5231 12.2533H22.4227C22.9589 12.2533 23.3976 12.7128 23.3976 13.2744V15.3166C23.3976 18.1374 21.2163 20.4222 18.5231 20.4222H15.5984V23.6578C15.5984 24.1237 15.2389 24.5066 14.7881 24.5066C14.6784 24.5066 14.5687 24.4811 14.4712 24.4364L8.45729 21.7368C8.05514 21.5581 7.79923 21.1433 7.79923 20.6902C7.79923 20.5115 7.83579 20.3392 7.915 20.1796L8.82897 18.2651ZM8.77413 10.2111H11.6988V14.2955C11.6988 15.4251 10.8275 16.3377 9.74903 16.3377C8.67055 16.3377 7.79923 15.4251 7.79923 14.2955V11.2322C7.79923 10.6706 8.23794 10.2111 8.77413 10.2111ZM16.5733 13.2744C16.5733 13.0036 16.4706 12.7439 16.2878 12.5524C16.105 12.3609 15.857 12.2533 15.5984 12.2533C15.3399 12.2533 15.0919 12.3609 14.9091 12.5524C14.7262 12.7439 14.6235 13.0036 14.6235 13.2744C14.6235 13.5452 14.7262 13.8049 14.9091 13.9964C15.0919 14.1879 15.3399 14.2955 15.5984 14.2955C15.857 14.2955 16.105 14.1879 16.2878 13.9964C16.4706 13.8049 16.5733 13.5452 16.5733 13.2744Z" fill="#C9DDA0"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="name-reviews">
                            <div>
                                <p class="fs-14-600-f-color">Sarah’s Grooming Studio</p>
                                <p class="fs-14-400-light ">Sarah W · Visiting groomer</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-card-separator" aria-hidden="true"></div>

                    <div class="modal-card-right-section">
                        <div class="details-section">
                            <div class="details">
                                <div class="d-flex align-items-center gap-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15" fill="none">
                                        <path d="M4.38016 10.1726C5.50473 11.2972 8.23986 10.3857 10.489 8.13624C12.7385 5.88709 13.65 3.15197 12.5254 2.02739M7.68909 1.26352L8.1981 1.77289M5.90755 3.04541L6.41656 3.55442M4.3798 5.08182L4.88881 5.59083M3.87079 7.62723L4.3798 8.13624M10.489 0.5L10.998 1.00901M9.97999 3.55478L10.998 4.5728M8.19846 5.33668L9.21648 6.3547M6.16206 6.86371L7.18008 7.88173" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M4.3799 11.7001C4.80164 11.2783 4.80164 10.5946 4.3799 10.1728C3.95816 9.75107 3.27438 9.75107 2.85264 10.1728L0.81629 12.2092C0.394548 12.6309 0.394549 13.3147 0.81629 13.7364C1.23803 14.1582 1.92181 14.1582 2.34355 13.7364L4.3799 11.7001Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="light-bold-color-font">Service</p>
                                </div>
                                <p class="simple-font mt-1">Full Groom</p>
                            </div>
                            <div class="details">
                                <div class="d-flex align-items-center gap-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                                        <path d="M0.5 7.32073C0.5 4.61335 0.5 3.2593 1.379 2.41858C2.258 1.57787 3.67175 1.57715 6.5 1.57715H9.5C12.3282 1.57715 13.7427 1.57715 14.621 2.41858C15.4992 3.26002 15.5 4.61335 15.5 7.32073V8.75663C15.5 11.464 15.5 12.8181 14.621 13.6588C13.742 14.4995 12.3282 14.5002 9.5 14.5002H6.5C3.67175 14.5002 2.25725 14.5002 1.379 13.6588C0.50075 12.8173 0.5 11.464 0.5 8.75663V7.32073Z" stroke="#9D9B98" />
                                        <path d="M4.25 1.57692V0.5M11.75 1.57692V0.5M0.875 5.16666H15.125" stroke="#9D9B98" stroke-linecap="round" />
                                        <path d="M12.5 10.9101C12.5 11.1005 12.421 11.2831 12.2803 11.4177C12.1397 11.5524 11.9489 11.628 11.75 11.628C11.5511 11.628 11.3603 11.5524 11.2197 11.4177C11.079 11.2831 11 11.1005 11 10.9101C11 10.7196 11.079 10.537 11.2197 10.4024C11.3603 10.2677 11.5511 10.1921 11.75 10.1921C11.9489 10.1921 12.1397 10.2677 12.2803 10.4024C12.421 10.537 12.5 10.7196 12.5 10.9101ZM12.5 8.03826C12.5 8.22867 12.421 8.41128 12.2803 8.54593C12.1397 8.68057 11.9489 8.75621 11.75 8.75621C11.5511 8.75621 11.3603 8.68057 11.2197 8.54593C11.079 8.41128 11 8.22867 11 8.03826C11 7.84785 11.079 7.66524 11.2197 7.53059C11.3603 7.39595 11.5511 7.32031 11.75 7.32031C11.9489 7.32031 12.1397 7.39595 12.2803 7.53059C12.421 7.66524 12.5 7.84785 12.5 8.03826ZM8.75 10.9101C8.75 11.1005 8.67098 11.2831 8.53033 11.4177C8.38968 11.5524 8.19891 11.628 8 11.628C7.80109 11.628 7.61032 11.5524 7.46967 11.4177C7.32902 11.2831 7.25 11.1005 7.25 10.9101C7.25 10.7196 7.32902 10.537 7.46967 10.4024C7.61032 10.2677 7.80109 10.1921 8 10.1921C8.19891 10.1921 8.38968 10.2677 8.53033 10.4024C8.67098 10.537 8.75 10.7196 8.75 10.9101ZM8.75 8.03826C8.75 8.22867 8.67098 8.41128 8.53033 8.54593C8.38968 8.68057 8.19891 8.75621 8 8.75621C7.80109 8.75621 7.61032 8.68057 7.46967 8.54593C7.32902 8.41128 7.25 8.22867 7.25 8.03826C7.25 7.84785 7.32902 7.66524 7.46967 7.53059C7.61032 7.39595 7.80109 7.32031 8 7.32031C8.19891 7.32031 8.38968 7.39595 8.53033 7.53059C8.67098 7.66524 8.75 7.84785 8.75 8.03826ZM5 10.9101C5 11.1005 4.92098 11.2831 4.78033 11.4177C4.63968 11.5524 4.44891 11.628 4.25 11.628C4.05109 11.628 3.86032 11.5524 3.71967 11.4177C3.57902 11.2831 3.5 11.1005 3.5 10.9101C3.5 10.7196 3.57902 10.537 3.71967 10.4024C3.86032 10.2677 4.05109 10.1921 4.25 10.1921C4.44891 10.1921 4.63968 10.2677 4.78033 10.4024C4.92098 10.537 5 10.7196 5 10.9101ZM5 8.03826C5 8.22867 4.92098 8.41128 4.78033 8.54593C4.63968 8.68057 4.44891 8.75621 4.25 8.75621C4.05109 8.75621 3.86032 8.68057 3.71967 8.54593C3.57902 8.41128 3.5 8.22867 3.5 8.03826C3.5 7.84785 3.57902 7.66524 3.71967 7.53059C3.86032 7.39595 4.05109 7.32031 4.25 7.32031C4.44891 7.32031 4.63968 7.39595 4.78033 7.53059C4.92098 7.66524 5 7.84785 5 8.03826Z" fill="#9D9B98" />
                                    </svg>
                                    <p class="light-bold-color-font">Date</p>
                                </div>
                                <p class="simple-font mt-1">18/12/2025</p>
                            </div>
                            <div class="details">
                                <div class="d-flex align-items-center gap-10">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <circle cx="8" cy="8" r="6" stroke="#9d9b98" stroke-width="1.5" />
                                        <path d="M8 4.5V8L10.5 10" stroke="#9d9b98" stroke-width="1.5" stroke-linecap="round" />
                                    </svg>
                                    <p class="light-bold-color-font">Time</p>
                                </div>
                                <p class="simple-font mt-1">14:30 - 15:30 (90 mins)</p>
                            </div>
                            <div class="details">
                                <div class="d-flex align-items-center gap-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                                        <path d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <p class="light-bold-color-font">Pet</p>
                                </div>
                                <p class="simple-font mt-1"> Other • Medium</p>
                            </div>
                            <div class="details">
                                <div class="d-flex align-items-center gap-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="15" viewBox="0 0 11 15" fill="none">
                                        <path d="M5.5 0.5C6.83339 0.5 8.10786 1.00588 9.04395 1.89941C9.9792 2.79219 10.5 3.99796 10.5 5.25C10.5 6.6294 9.73861 8.338 8.73145 9.9707C7.73727 11.5823 6.5574 13.0362 5.82422 13.8867C5.6489 14.0901 5.3511 14.0901 5.17578 13.8867C4.4426 13.0362 3.26273 11.5823 2.26855 9.9707C1.26139 8.338 0.5 6.6294 0.5 5.25C0.5 3.99796 1.0208 2.79219 1.95605 1.89941C2.89214 1.00588 4.16661 0.5 5.5 0.5ZM5.5 2.875C4.85374 2.875 4.22936 3.11984 3.76562 3.5625C3.30115 4.00591 3.03613 4.61245 3.03613 5.25C3.03613 5.88755 3.30115 6.49409 3.76562 6.9375C4.22936 7.38016 4.85374 7.625 5.5 7.625C5.82047 7.625 6.13831 7.56479 6.43555 7.44727C6.73282 7.32973 7.00457 7.15686 7.23438 6.9375C7.46409 6.7182 7.64771 6.45659 7.77344 6.16699C7.89921 5.87715 7.96387 5.5652 7.96387 5.25C7.96387 4.61245 7.69885 4.00591 7.23438 3.5625C6.77064 3.11984 6.14626 2.875 5.5 2.875Z" stroke="#9D9B98" />
                                    </svg>
                                    <p class="light-bold-color-font">Location</p>
                                </div>
                                <p class="simple-font mt-1">West London</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-toolbar">
                    <div>
                        <h2>Space Results</h2>
                        <span class="space-total">3 total</span>
                    </div>
                    <div class="tab-wrapper">
                        <div class="tabs groomer-tabs text-center">
                            <a data-tab="groomer-list-view" class="tablinks active">List View</a>
                            <a data-tab="groomer-map-view" class="tablinks">Map View</a>
                        </div>
                    </div>
                    <div class="venu-sorting-section d-flex gap-10">
                        <div class="venue-selection">
                            Space Type
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none">
                                <path d="M1 1L6 6L11 1" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="venue-list">
                                <div class="venu dropdown">
                                    <ul>
                                        <li><label><span class="option-text">Private rooms</span><input type="checkbox" name="space-venue[]" value="Private rooms"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Salon</span><input type="checkbox" name="space-venue[]" value="Salon"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Mobile Station</span><input type="checkbox" name="space-venue[]" value="Mobile Station"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Garden / Shed</span><input type="checkbox" name="space-venue[]" value="Garden / Shed" checked><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Others</span><input type="checkbox" name="space-venue[]" value="Others"><span class="check-circle"></span></label></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <div class="sort-by">
                            <span class="space-sort-label">Sort: Best Match</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none">
                                <path d="M1 1L6 6L11 1" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="sort-by-filter">
                                <div class="sort dropdown">
                                    <ul>
                                        <li><label><span class="option-text">Best Match</span><input type="radio" name="space-sort" value="default" checked><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Distance</span><input type="radio" name="space-sort" value="distance"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Lowest price</span><input type="radio" name="space-sort" value="lowest_price"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Soonest available</span><input type="radio" name="space-sort" value="soonest_available"><span class="check-circle"></span></label></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter space-active-filters">
                    <div class="selected-item-section d-flex align-items-center flex-wrap gap-10" id="groomerSelectedSection"></div>
                </div>

                <div data-tab-content="groomer-list-view" class="tabcontent" data-display="flex">
                    <div class="space-results">
                        <article class="space-result" data-name="Furs &amp; Co. Studio" data-total="92.00" data-photos="{{ asset('images/space_card1.png') }}|{{ asset('images/space_card2.png') }}|{{ asset('images/space_card3.png') }}|{{ asset('images/space_card1.png') }}|{{ asset('images/space_card2.png') }}|{{ asset('images/space_card3.png') }}|{{ asset('images/space_card1.png') }}">
                            <div class="space-result-main">
                                <button type="button" class="space-result-photo" data-open-gallery="0" aria-label="Furs &amp; Co. Studio">
                                    <div class="top-left-svg">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 18 19" fill="none" aria-hidden="true">
                                        <path d="M9.1404 0.104606C8.98891 0.036071 8.82659 0 8.65705 0C8.48752 0 8.3252 0.036071 8.1737 0.104606L1.38154 2.98668C0.587973 3.32214 -0.00359069 4.10488 1.64069e-05 5.04994C0.0180519 8.62818 1.48975 15.1751 7.70478 18.1509C8.30717 18.4395 9.00694 18.4395 9.60933 18.1509C15.8244 15.1751 17.2961 8.62818 17.3141 5.04994C17.3177 4.10488 16.7261 3.32214 15.9326 2.98668L9.1404 0.104606Z" fill="#CBDCE8" />
                                        <path d="M13.3581 5.00928L9.6475 8.95176L13.3581 5.00928ZM7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" fill="#CBDCE8" />
                                        <path d="M13.3581 5.00928L9.6475 8.95176M7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747L5.70508 11.2454Z" fill="#CBDCE8" />
                                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7.56043 6.98041C7.56043 7.13418 7.49935 7.28165 7.39062 7.39038C7.28189 7.49911 7.13442 7.56019 6.98066 7.56019C6.82689 7.56019 6.67942 7.49911 6.57069 7.39038C6.46196 7.28165 6.40088 7.13418 6.40088 6.98041C6.40088 6.82665 6.46196 6.67918 6.57069 6.57045C6.67942 6.46172 6.82689 6.40063 6.98066 6.40063C7.13442 6.40063 7.28189 6.46172 7.39062 6.57045C7.49935 6.67918 7.56043 6.82665 7.56043 6.98041Z" fill="#CBDCE8" stroke="white" />
                                        <path d="M8.72021 5.47266V5.51904V5.47266Z" fill="#CBDCE8" />
                                        <path d="M8.72021 5.47266V5.51904" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <img src="{{ asset('images/space_card1.png') }}" alt="Furs &amp; Co. Studio">
                                    <span class="space-photo-count">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4C8.25229 3.99992 8.49528 4.0952 8.68027 4.26675C8.86526 4.43829 8.97858 4.67342 8.9975 4.925L9 5V8C9.00008 8.25229 8.9048 8.49528 8.73325 8.68027C8.56171 8.86526 8.32658 8.97858 8.075 8.9975L8 9H6C5.74771 9.00008 5.50472 8.9048 5.31973 8.73325C5.13474 8.56171 5.02142 8.32658 5.0025 8.075L5 8V5C4.99992 4.74771 5.0952 4.50472 5.26675 4.31973C5.43829 4.13474 5.67342 4.02142 5.925 4.0025L6 4H8ZM3 6C3.26522 6 3.51957 6.10536 3.70711 6.29289C3.89464 6.48043 4 6.73478 4 7V8C4 8.26522 3.89464 8.51957 3.70711 8.70711C3.51957 8.89464 3.26522 9 3 9H1C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8V7C0 6.73478 0.105357 6.48043 0.292893 6.29289C0.48043 6.10536 0.734784 6 1 6H3ZM3 0C3.26522 0 3.51957 0.105357 3.70711 0.292893C3.89464 0.48043 4 0.734784 4 1V4C4 4.26522 3.89464 4.51957 3.70711 4.70711C3.51957 4.89464 3.26522 5 3 5H1C0.734784 5 0.48043 4.89464 0.292893 4.70711C0.105357 4.51957 0 4.26522 0 4V1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H3ZM8 0C8.26522 0 8.51957 0.105357 8.70711 0.292893C8.89464 0.48043 9 0.734784 9 1V2C9 2.26522 8.89464 2.51957 8.70711 2.70711C8.51957 2.89464 8.26522 3 8 3H6C5.73478 3 5.48043 2.89464 5.29289 2.70711C5.10536 2.51957 5 2.26522 5 2V1C5 0.734784 5.10536 0.48043 5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0H8Z" fill="white" />
                                        </svg>
                                        7 photos
                                    </span>
                                </button>
                                <div class="space-result-body">
                                    <div class="space-result-top">
                                        <div>
                                            <div class="space-title-row">
                                                <h3>Furs &amp; Co. Studio</h3>
                                                <span class="tag">Salons</span>
                                                <span class="pill toprated">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                                                        <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                                                    </svg>
                                                    Best match
                                                </span>
                                            </div>
                                            <p class="space-hosted">Hosted by Dev E.</p>
                                        </div>
                                        <div class="space-meta">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                                    <path d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z" fill="#FFC97A" />
                                                </svg>
                                                4.3 <small>(20)</small>
                                            </span>
                                            <span class="space-meta-distance">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14" viewBox="0 0 10 14" fill="none" aria-hidden="true">
                                                    <path d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z" fill="#D9D9D9" />
                                                </svg>
                                                2.5 mi away
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-chip-row">
                                        <span class="space-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Free at 14:30-15:30</span>
                                        <span class="space-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Full Groom</span>
                                        <span class="space-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Other Pets</span>
                                        <span class="space-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Medium Pets</span>
                                    </div>
                                    <div class="space-chip-row">
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Grooming Table</span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath</span>

                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Dryer</span>
                                        <button type="button" class="space-amenity-chip more" data-expand>+4 more</button>
                                    </div>
                                    <div class="space-result-bottom">
                                        <div class="space-price">
                                            <strong>£38.00 <span>/ per hour</span></strong>
                                            <p>Full Groom £48 + Space £38 (Hourly)</p>
                                        </div>
                                        <div class="space-actions">
                                            <a class="space-message" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none">
                                                    <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                                                    <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                                                </svg>
                                                Message
                                            </a>
                                            <button type="button" class="space-select" aria-pressed="false">Select</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-result-more">
                                <div>
                                    <p class="space-about-kicker">ABOUT FURS &amp; CO. STUDIO</p>
                                    <p class="space-tagline">A polished salon bay, ready for a full groom.</p>
                                    <p class="space-about-copy">Bright, clean and set up for professional grooming. The bay is easy to reach in West London and equipped for the booking you already chose with Sarah.</p>
                                    <div class="space-stat-row">
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Fits 1–2 groomers</span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Up to 2 pets at a time</span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Approx. 180 sq. ft.</span>
                                    </div>
                                    <p class="space-about-kicker">AMENITIES</p>
                                    <div class="space-amenity-grid">
                                        <span class="space-amenity-pill">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Grooming Table</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Dryer</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Towels</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Waiting area</span>
                                    </div>
                                    <p class="space-extra">Extra towels &bull; Premium shampoos &bull; Drying crates &bull; Tool storage (where available).</p>
                                    <button type="button" class="space-show-less">
                                        Show less
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                                            <path d="M0.625 5.625L5.625 0.625L10.625 5.625" stroke="#FFA899" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-thumbs">
                                    <button type="button" data-open-gallery="0"><img src="{{ asset('images/space_card1.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="1"><img src="{{ asset('images/space_card2.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="2"><img src="{{ asset('images/space_card3.png') }}" alt="">
                                        <span class="space-thumb-more">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4C8.25229 3.99992 8.49528 4.0952 8.68027 4.26675C8.86526 4.43829 8.97858 4.67342 8.9975 4.925L9 5V8C9.00008 8.25229 8.9048 8.49528 8.73325 8.68027C8.56171 8.86526 8.32658 8.97858 8.075 8.9975L8 9H6C5.74771 9.00008 5.50472 8.9048 5.31973 8.73325C5.13474 8.56171 5.02142 8.32658 5.0025 8.075L5 8V5C4.99992 4.74771 5.0952 4.50472 5.26675 4.31973C5.43829 4.13474 5.67342 4.02142 5.925 4.0025L6 4H8ZM3 6C3.26522 6 3.51957 6.10536 3.70711 6.29289C3.89464 6.48043 4 6.73478 4 7V8C4 8.26522 3.89464 8.51957 3.70711 8.70711C3.51957 8.89464 3.26522 9 3 9H1C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8V7C0 6.73478 0.105357 6.48043 0.292893 6.29289C0.48043 6.10536 0.734784 6 1 6H3ZM3 0C3.26522 0 3.51957 0.105357 3.70711 0.292893C3.89464 0.48043 4 0.734784 4 1V4C4 4.26522 3.89464 4.51957 3.70711 4.70711C3.51957 4.89464 3.26522 5 3 5H1C0.734784 5 0.48043 4.89464 0.292893 4.70711C0.105357 4.51957 0 4.26522 0 4V1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H3ZM8 0C8.26522 0 8.51957 0.105357 8.70711 0.292893C8.89464 0.48043 9 0.734784 9 1V2C9 2.26522 8.89464 2.51957 8.70711 2.70711C8.51957 2.89464 8.26522 3 8 3H6C5.73478 3 5.48043 2.89464 5.29289 2.70711C5.10536 2.51957 5 2.26522 5 2V1C5 0.734784 5.10536 0.48043 5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0H8Z" fill="white" />
                                            </svg>
                                            4
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <article class="space-result" data-name="Paws &amp; Bubbles" data-total="93.00" data-photos="{{ asset('images/space_card2.png') }}|{{ asset('images/space_card3.png') }}|{{ asset('images/space_card1.png') }}|{{ asset('images/space_card2.png') }}|{{ asset('images/space_card3.png') }}|{{ asset('images/space_card1.png') }}|{{ asset('images/space_card2.png') }}">
                            <div class="space-result-main">
                                <button type="button" class="space-result-photo" data-open-gallery="0" aria-label="Paws &amp; Bubbles">
                                    <div class="top-left-svg">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 18 19" fill="none" aria-hidden="true">
                                        <path d="M9.1404 0.104606C8.98891 0.036071 8.82659 0 8.65705 0C8.48752 0 8.3252 0.036071 8.1737 0.104606L1.38154 2.98668C0.587973 3.32214 -0.00359069 4.10488 1.64069e-05 5.04994C0.0180519 8.62818 1.48975 15.1751 7.70478 18.1509C8.30717 18.4395 9.00694 18.4395 9.60933 18.1509C15.8244 15.1751 17.2961 8.62818 17.3141 5.04994C17.3177 4.10488 16.7261 3.32214 15.9326 2.98668L9.1404 0.104606Z" fill="#CBDCE8" />
                                        <path d="M13.3581 5.00928L9.6475 8.95176L13.3581 5.00928ZM7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" fill="#CBDCE8" />
                                        <path d="M13.3581 5.00928L9.6475 8.95176M7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747L5.70508 11.2454Z" fill="#CBDCE8" />
                                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7.56043 6.98041C7.56043 7.13418 7.49935 7.28165 7.39062 7.39038C7.28189 7.49911 7.13442 7.56019 6.98066 7.56019C6.82689 7.56019 6.67942 7.49911 6.57069 7.39038C6.46196 7.28165 6.40088 7.13418 6.40088 6.98041C6.40088 6.82665 6.46196 6.67918 6.57069 6.57045C6.67942 6.46172 6.82689 6.40063 6.98066 6.40063C7.13442 6.40063 7.28189 6.46172 7.39062 6.57045C7.49935 6.67918 7.56043 6.82665 7.56043 6.98041Z" fill="#CBDCE8" stroke="white" />
                                        <path d="M8.72021 5.47266V5.51904V5.47266Z" fill="#CBDCE8" />
                                        <path d="M8.72021 5.47266V5.51904" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <img src="{{ asset('images/space_card2.png') }}" alt="Paws &amp; Bubbles">
                                    <span class="space-photo-count">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4C8.25229 3.99992 8.49528 4.0952 8.68027 4.26675C8.86526 4.43829 8.97858 4.67342 8.9975 4.925L9 5V8C9.00008 8.25229 8.9048 8.49528 8.73325 8.68027C8.56171 8.86526 8.32658 8.97858 8.075 8.9975L8 9H6C5.74771 9.00008 5.50472 8.9048 5.31973 8.73325C5.13474 8.56171 5.02142 8.32658 5.0025 8.075L5 8V5C4.99992 4.74771 5.0952 4.50472 5.26675 4.31973C5.43829 4.13474 5.67342 4.02142 5.925 4.0025L6 4H8ZM3 6C3.26522 6 3.51957 6.10536 3.70711 6.29289C3.89464 6.48043 4 6.73478 4 7V8C4 8.26522 3.89464 8.51957 3.70711 8.70711C3.51957 8.89464 3.26522 9 3 9H1C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8V7C0 6.73478 0.105357 6.48043 0.292893 6.29289C0.48043 6.10536 0.734784 6 1 6H3ZM3 0C3.26522 0 3.51957 0.105357 3.70711 0.292893C3.89464 0.48043 4 0.734784 4 1V4C4 4.26522 3.89464 4.51957 3.70711 4.70711C3.51957 4.89464 3.26522 5 3 5H1C0.734784 5 0.48043 4.89464 0.292893 4.70711C0.105357 4.51957 0 4.26522 0 4V1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H3ZM8 0C8.26522 0 8.51957 0.105357 8.70711 0.292893C8.89464 0.48043 9 0.734784 9 1V2C9 2.26522 8.89464 2.51957 8.70711 2.70711C8.51957 2.89464 8.26522 3 8 3H6C5.73478 3 5.48043 2.89464 5.29289 2.70711C5.10536 2.51957 5 2.26522 5 2V1C5 0.734784 5.10536 0.48043 5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0H8Z" fill="white" />
                                        </svg>
                                        7 photos
                                    </span>
                                </button>
                                <div class="space-result-body">
                                    <div class="space-result-top">
                                        <div>
                                            <div class="space-title-row">
                                                <h3>Paws &amp; Bubbles</h3>
                                                <span class="tag">Mobile Station</span>
                                            </div>
                                            <p class="space-hosted">Hosted by Maya K.</p>
                                        </div>
                                        <div class="space-meta">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                                    <path d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z" fill="#FFC97A" />
                                                </svg>
                                                4.3 <small>(20)</small>
                                            </span>
                                            <span class="space-meta-distance">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14" viewBox="0 0 10 14" fill="none" aria-hidden="true">
                                                    <path d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z" fill="#D9D9D9" />
                                                </svg>
                                                2.5 mi away
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-chip-row">
                                        <span class="space-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Free at 14:30-15:30</span>
                                        <span class="space-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Full Groom</span>
                                        <span class="space-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Other Pets</span>
                                        <span class="space-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Medium Pets</span>
                                    </div>
                                    <div class="space-chip-row">
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Grooming Table</span>
                                        <span class="space-amenity-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath</span>
                                        <span class="space-amenity-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Dryer</span>
                                        <button type="button" class="space-amenity-chip more" data-expand>+4 more</button>
                                    </div>
                                    <div class="space-result-bottom">
                                        <div class="space-price">
                                            <strong>£45.00 <span>/ per hour</span></strong>
                                            <p>Full Groom £48 + Space £45 (Hourly)</p>
                                        </div>
                                        <div class="space-actions">
                                            <a class="space-message" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none">
                                                    <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                                                    <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                                                </svg>
                                                Message
                                            </a>
                                            <button type="button" class="space-select" aria-pressed="false">Select</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-result-more">
                                <div>
                                    <p class="space-about-kicker">ABOUT PAWS &amp; BUBBLES</p>
                                    <p class="space-tagline">A mobile station that sets up where you need it.</p>
                                    <p class="space-about-copy">A compact mobile station with a bath, table and dryer, matched to this full groom and the time you already picked.</p>
                                    <div class="space-stat-row">
                                        <span class="space-amenity-chip">Fits 1 groomer</span>
                                        <span class="space-amenity-chip">Up to 2 pets at a time</span>
                                        <span class="space-amenity-chip">Approx. 80 sq. ft.</span>
                                    </div>
                                    <p class="space-about-kicker">AMENITIES</p>
                                    <div class="space-amenity-grid">
                                        <span class="space-amenity-pill">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Grooming Table</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Dryer</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Towels</span>
                                        <span class="space-amenity-pill">Waiting area</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Parking</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Wi-Fi</span>
                                    </div>
                                    <p class="space-extra">Extra towels &bull; Premium shampoos &bull; Drying crates &bull; Tool storage (where available).</p>
                                    <button type="button" class="space-show-less">
                                        Show less
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                                            <path d="M0.625 5.625L5.625 0.625L10.625 5.625" stroke="#FFA899" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-thumbs">
                                    <button type="button" data-open-gallery="0"><img src="{{ asset('images/space_card2.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="1"><img src="{{ asset('images/space_card3.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="2"><img src="{{ asset('images/space_card1.png') }}" alt="">
                                        <span class="space-thumb-more">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4C8.25229 3.99992 8.49528 4.0952 8.68027 4.26675C8.86526 4.43829 8.97858 4.67342 8.9975 4.925L9 5V8C9.00008 8.25229 8.9048 8.49528 8.73325 8.68027C8.56171 8.86526 8.32658 8.97858 8.075 8.9975L8 9H6C5.74771 9.00008 5.50472 8.9048 5.31973 8.73325C5.13474 8.56171 5.02142 8.32658 5.0025 8.075L5 8V5C4.99992 4.74771 5.0952 4.50472 5.26675 4.31973C5.43829 4.13474 5.67342 4.02142 5.925 4.0025L6 4H8ZM3 6C3.26522 6 3.51957 6.10536 3.70711 6.29289C3.89464 6.48043 4 6.73478 4 7V8C4 8.26522 3.89464 8.51957 3.70711 8.70711C3.51957 8.89464 3.26522 9 3 9H1C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8V7C0 6.73478 0.105357 6.48043 0.292893 6.29289C0.48043 6.10536 0.734784 6 1 6H3ZM3 0C3.26522 0 3.51957 0.105357 3.70711 0.292893C3.89464 0.48043 4 0.734784 4 1V4C4 4.26522 3.89464 4.51957 3.70711 4.70711C3.51957 4.89464 3.26522 5 3 5H1C0.734784 5 0.48043 4.89464 0.292893 4.70711C0.105357 4.51957 0 4.26522 0 4V1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H3ZM8 0C8.26522 0 8.51957 0.105357 8.70711 0.292893C8.89464 0.48043 9 0.734784 9 1V2C9 2.26522 8.89464 2.51957 8.70711 2.70711C8.51957 2.89464 8.26522 3 8 3H6C5.73478 3 5.48043 2.89464 5.29289 2.70711C5.10536 2.51957 5 2.26522 5 2V1C5 0.734784 5.10536 0.48043 5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0H8Z" fill="white" />
                                            </svg>
                                            4
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </article>

                        <article class="space-result" data-name="The Garden Grooming Spot" data-total="106.00" data-photos="{{ asset('images/space_card3.png') }}|{{ asset('images/space_card1.png') }}|{{ asset('images/space_card2.png') }}|{{ asset('images/space_card3.png') }}|{{ asset('images/space_card1.png') }}|{{ asset('images/space_card2.png') }}|{{ asset('images/space_card3.png') }}">
                            <div class="space-result-main">
                                <button type="button" class="space-result-photo" data-open-gallery="0" aria-label="The Garden Grooming Spot">
                                    <div class="top-left-svg">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 18 19" fill="none" aria-hidden="true">
                                        <path d="M9.1404 0.104606C8.98891 0.036071 8.82659 0 8.65705 0C8.48752 0 8.3252 0.036071 8.1737 0.104606L1.38154 2.98668C0.587973 3.32214 -0.00359069 4.10488 1.64069e-05 5.04994C0.0180519 8.62818 1.48975 15.1751 7.70478 18.1509C8.30717 18.4395 9.00694 18.4395 9.60933 18.1509C15.8244 15.1751 17.2961 8.62818 17.3141 5.04994C17.3177 4.10488 16.7261 3.32214 15.9326 2.98668L9.1404 0.104606Z" fill="#CBDCE8" />
                                        <path d="M13.3581 5.00928L9.6475 8.95176L13.3581 5.00928ZM7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" fill="#CBDCE8" />
                                        <path d="M13.3581 5.00928L9.6475 8.95176M7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747L5.70508 11.2454Z" fill="#CBDCE8" />
                                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        <path d="M7.56043 6.98041C7.56043 7.13418 7.49935 7.28165 7.39062 7.39038C7.28189 7.49911 7.13442 7.56019 6.98066 7.56019C6.82689 7.56019 6.67942 7.49911 6.57069 7.39038C6.46196 7.28165 6.40088 7.13418 6.40088 6.98041C6.40088 6.82665 6.46196 6.67918 6.57069 6.57045C6.67942 6.46172 6.82689 6.40063 6.98066 6.40063C7.13442 6.40063 7.28189 6.46172 7.39062 6.57045C7.49935 6.67918 7.56043 6.82665 7.56043 6.98041Z" fill="#CBDCE8" stroke="white" />
                                        <path d="M8.72021 5.47266V5.51904V5.47266Z" fill="#CBDCE8" />
                                        <path d="M8.72021 5.47266V5.51904" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                    <img src="{{ asset('images/space_card3.png') }}" alt="The Garden Grooming Spot">
                                    <span class="space-photo-count">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4C8.25229 3.99992 8.49528 4.0952 8.68027 4.26675C8.86526 4.43829 8.97858 4.67342 8.9975 4.925L9 5V8C9.00008 8.25229 8.9048 8.49528 8.73325 8.68027C8.56171 8.86526 8.32658 8.97858 8.075 8.9975L8 9H6C5.74771 9.00008 5.50472 8.9048 5.31973 8.73325C5.13474 8.56171 5.02142 8.32658 5.0025 8.075L5 8V5C4.99992 4.74771 5.0952 4.50472 5.26675 4.31973C5.43829 4.13474 5.67342 4.02142 5.925 4.0025L6 4H8ZM3 6C3.26522 6 3.51957 6.10536 3.70711 6.29289C3.89464 6.48043 4 6.73478 4 7V8C4 8.26522 3.89464 8.51957 3.70711 8.70711C3.51957 8.89464 3.26522 9 3 9H1C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8V7C0 6.73478 0.105357 6.48043 0.292893 6.29289C0.48043 6.10536 0.734784 6 1 6H3ZM3 0C3.26522 0 3.51957 0.105357 3.70711 0.292893C3.89464 0.48043 4 0.734784 4 1V4C4 4.26522 3.89464 4.51957 3.70711 4.70711C3.51957 4.89464 3.26522 5 3 5H1C0.734784 5 0.48043 4.89464 0.292893 4.70711C0.105357 4.51957 0 4.26522 0 4V1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H3ZM8 0C8.26522 0 8.51957 0.105357 8.70711 0.292893C8.89464 0.48043 9 0.734784 9 1V2C9 2.26522 8.89464 2.51957 8.70711 2.70711C8.51957 2.89464 8.26522 3 8 3H6C5.73478 3 5.48043 2.89464 5.29289 2.70711C5.10536 2.51957 5 2.26522 5 2V1C5 0.734784 5.10536 0.48043 5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0H8Z" fill="white" />
                                        </svg>
                                        7 photos
                                    </span>
                                </button>
                                <div class="space-result-body">
                                    <div class="space-result-top">
                                        <div>
                                            <div class="space-title-row">
                                                <h3>The Garden Grooming Spot</h3>
                                                <span class="pill garden-shed">Garden / Shed</span>
                                                <span class="pill toprated">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                                                        <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                                                    </svg>
                                                    Best match
                                                </span>
                                            </div>
                                            <p class="space-hosted">Hosted by Chloe D.</p>
                                        </div>
                                        <div class="space-meta">
                                            <span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                                                    <path d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z" fill="#FFC97A" />
                                                </svg>
                                                4.3 <small>(20)</small>
                                            </span>
                                            <span class="space-meta-distance">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14" viewBox="0 0 10 14" fill="none" aria-hidden="true">
                                                    <path d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z" fill="#D9D9D9" />
                                                </svg>
                                                2.5 mi away
                                            </span>
                                        </div>
                                    </div>
                                    <div class="space-chip-row">
                                        <span class="space-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Free at 14:30-15:30</span>
                                        <span class="space-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Full Groom</span>
                                        <span class="space-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Other Pets</span>
                                        <span class="space-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Medium Pets</span>
                                    </div>
                                    <div class="space-chip-row">
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Grooming Table</span>
                                        <span class="space-amenity-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath</span>
                                        <span class="space-amenity-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Dryer</span>
                                        <button type="button" class="space-amenity-chip more" data-expand>+4 more</button>
                                    </div>
                                    <div class="space-result-bottom">
                                        <div class="space-price">
                                            <strong>£58.00 <span>/ per hour</span></strong>
                                            <p>Full Groom £48 + Space £58 (Hourly)</p>
                                        </div>
                                        <div class="space-actions">
                                            <a class="space-message" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none">
                                                    <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                                                    <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                                                </svg>
                                                Message
                                            </a>
                                            <button type="button" class="space-select" aria-pressed="false">Select</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="space-result-more">
                                <div>
                                    <p class="space-about-kicker">ABOUT THE GARDEN GROOMING SPOT</p>
                                    <p class="space-tagline">Spacious garden space for all furry friends.</p>
                                    <p class="space-about-copy">A bright, clean garden space ideal for professional use. Fully equipped, easy to access, and comfortable for pets and groomers working through a full groom.</p>
                                    <div class="space-stat-row">
                                        <span class="space-amenity-chip">Fits 1–3 groomers</span>
                                        <span class="space-amenity-chip">Up to 4 pets at a time</span>
                                        <span class="space-amenity-chip">Approx. 400 sq. ft.</span>
                                    </div>
                                    <p class="space-about-kicker">AMENITIES</p>
                                    <div class="space-amenity-grid">
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Grooming Table</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Dryer</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Towels</span>
                                        <span class="space-amenity-pill">Waiting area</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Parking</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Wi-Fi</span>
                                    </div>
                                    <p class="space-extra">Extra towels &bull; Premium shampoos &bull; Drying crates &bull; Tool storage (where available).</p>
                                    <button type="button" class="space-show-less">
                                        Show less
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                                            <path d="M0.625 5.625L5.625 0.625L10.625 5.625" stroke="#FFA899" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-thumbs">
                                    <button type="button" data-open-gallery="0"><img src="{{ asset('images/space_card3.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="1"><img src="{{ asset('images/space_card1.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="2"><img src="{{ asset('images/space_card2.png') }}" alt="">
                                        <span class="space-thumb-more">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none">
                                                <path fill-rule="evenodd" clip-rule="evenodd" d="M8 4C8.25229 3.99992 8.49528 4.0952 8.68027 4.26675C8.86526 4.43829 8.97858 4.67342 8.9975 4.925L9 5V8C9.00008 8.25229 8.9048 8.49528 8.73325 8.68027C8.56171 8.86526 8.32658 8.97858 8.075 8.9975L8 9H6C5.74771 9.00008 5.50472 8.9048 5.31973 8.73325C5.13474 8.56171 5.02142 8.32658 5.0025 8.075L5 8V5C4.99992 4.74771 5.0952 4.50472 5.26675 4.31973C5.43829 4.13474 5.67342 4.02142 5.925 4.0025L6 4H8ZM3 6C3.26522 6 3.51957 6.10536 3.70711 6.29289C3.89464 6.48043 4 6.73478 4 7V8C4 8.26522 3.89464 8.51957 3.70711 8.70711C3.51957 8.89464 3.26522 9 3 9H1C0.734784 9 0.48043 8.89464 0.292893 8.70711C0.105357 8.51957 0 8.26522 0 8V7C0 6.73478 0.105357 6.48043 0.292893 6.29289C0.48043 6.10536 0.734784 6 1 6H3ZM3 0C3.26522 0 3.51957 0.105357 3.70711 0.292893C3.89464 0.48043 4 0.734784 4 1V4C4 4.26522 3.89464 4.51957 3.70711 4.70711C3.51957 4.89464 3.26522 5 3 5H1C0.734784 5 0.48043 4.89464 0.292893 4.70711C0.105357 4.51957 0 4.26522 0 4V1C0 0.734784 0.105357 0.48043 0.292893 0.292893C0.48043 0.105357 0.734784 0 1 0H3ZM8 0C8.26522 0 8.51957 0.105357 8.70711 0.292893C8.89464 0.48043 9 0.734784 9 1V2C9 2.26522 8.89464 2.51957 8.70711 2.70711C8.51957 2.89464 8.26522 3 8 3H6C5.73478 3 5.48043 2.89464 5.29289 2.70711C5.10536 2.51957 5 2.26522 5 2V1C5 0.734784 5.10536 0.48043 5.29289 0.292893C5.48043 0.105357 5.73478 0 6 0H8Z" fill="white" />
                                            </svg>
                                            4
                                        </span>
                                    </button>
                                </div>
                            </div>
                        </article>
                    </div>

                </div>

                                <div data-tab-content="groomer-map-view" class="tabcontent" style="display: none;" data-display="flex">
                    <div class="map-view-layout">
                        <div class="map-space-rail">
                <article class="map-space-card" data-name="Furs &amp; Co. Studio" data-total="92.00" data-price="38.00">
                    <div class="map-space-card-photo">
                    <div class="top-left-svg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 18 19" fill="none" aria-hidden="true">
                        <path d="M9.1404 0.104606C8.98891 0.036071 8.82659 0 8.65705 0C8.48752 0 8.3252 0.036071 8.1737 0.104606L1.38154 2.98668C0.587973 3.32214 -0.00359069 4.10488 1.64069e-05 5.04994C0.0180519 8.62818 1.48975 15.1751 7.70478 18.1509C8.30717 18.4395 9.00694 18.4395 9.60933 18.1509C15.8244 15.1751 17.2961 8.62818 17.3141 5.04994C17.3177 4.10488 16.7261 3.32214 15.9326 2.98668L9.1404 0.104606Z" fill="#CBDCE8" />
                        <path d="M13.3581 5.00928L9.6475 8.95176L13.3581 5.00928ZM7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" fill="#CBDCE8" />
                        <path d="M13.3581 5.00928L9.6475 8.95176M7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747L5.70508 11.2454Z" fill="#CBDCE8" />
                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7.56043 6.98041C7.56043 7.13418 7.49935 7.28165 7.39062 7.39038C7.28189 7.49911 7.13442 7.56019 6.98066 7.56019C6.82689 7.56019 6.67942 7.49911 6.57069 7.39038C6.46196 7.28165 6.40088 7.13418 6.40088 6.98041C6.40088 6.82665 6.46196 6.67918 6.57069 6.57045C6.67942 6.46172 6.82689 6.40063 6.98066 6.40063C7.13442 6.40063 7.28189 6.46172 7.39062 6.57045C7.49935 6.67918 7.56043 6.82665 7.56043 6.98041Z" fill="#CBDCE8" stroke="white" />
                        <path d="M8.72021 5.47266V5.51904V5.47266Z" fill="#CBDCE8" />
                        <path d="M8.72021 5.47266V5.51904" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                        <img src="{{ asset('images/space_card1.png') }}" alt="Furs &amp; Co. Studio">
                    </div>
                    <div class="map-space-card-body">
                        <div class="map-space-card-head">
                            <div>
                                <h3>Furs &amp; Co. Studio</h3>
                                <p>Hosted by Dev E.</p>
                            </div>
                            <a class="map-space-card-msg" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener" aria-label="Message">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none" aria-hidden="true">
                            <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                            <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                        </svg>
                            </a>
                        </div>
                        <div class="map-space-card-meta">
                            <span class="map-space-card-type">Salons</span>
                            <span class="map-space-card-rating">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z" fill="#FFC97A" />
                        </svg>
                                4.3 <small>(20)</small>
                            </span>
                        </div>
                        <p class="map-space-card-distance">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14" viewBox="0 0 10 14" fill="none" aria-hidden="true">
                            <path d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z" fill="#D9D9D9" />
                        </svg>
                            2.5 mi away
                        </p>
                        <span class="map-space-card-avail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                            Free at 14:30-15:30
                        </span>
                        <div class="map-space-card-foot">
                            <strong>&pound;38.00 <span>p/h</span></strong>
                            <button type="button" class="map-space-card-select" aria-pressed="false">Select</button>
                        </div>
                    </div>
                </article>
                <article class="map-space-card is-selected" data-name="Paws &amp; Bubbles" data-total="93.00" data-price="28.00">
                    <div class="map-space-card-photo">
                    <div class="top-left-svg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 18 19" fill="none" aria-hidden="true">
                        <path d="M9.1404 0.104606C8.98891 0.036071 8.82659 0 8.65705 0C8.48752 0 8.3252 0.036071 8.1737 0.104606L1.38154 2.98668C0.587973 3.32214 -0.00359069 4.10488 1.64069e-05 5.04994C0.0180519 8.62818 1.48975 15.1751 7.70478 18.1509C8.30717 18.4395 9.00694 18.4395 9.60933 18.1509C15.8244 15.1751 17.2961 8.62818 17.3141 5.04994C17.3177 4.10488 16.7261 3.32214 15.9326 2.98668L9.1404 0.104606Z" fill="#CBDCE8" />
                        <path d="M13.3581 5.00928L9.6475 8.95176L13.3581 5.00928ZM7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" fill="#CBDCE8" />
                        <path d="M13.3581 5.00928L9.6475 8.95176M7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747L5.70508 11.2454Z" fill="#CBDCE8" />
                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7.56043 6.98041C7.56043 7.13418 7.49935 7.28165 7.39062 7.39038C7.28189 7.49911 7.13442 7.56019 6.98066 7.56019C6.82689 7.56019 6.67942 7.49911 6.57069 7.39038C6.46196 7.28165 6.40088 7.13418 6.40088 6.98041C6.40088 6.82665 6.46196 6.67918 6.57069 6.57045C6.67942 6.46172 6.82689 6.40063 6.98066 6.40063C7.13442 6.40063 7.28189 6.46172 7.39062 6.57045C7.49935 6.67918 7.56043 6.82665 7.56043 6.98041Z" fill="#CBDCE8" stroke="white" />
                        <path d="M8.72021 5.47266V5.51904V5.47266Z" fill="#CBDCE8" />
                        <path d="M8.72021 5.47266V5.51904" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="map-space-card-best">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none" aria-hidden="true">
                            <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                        </svg>
                        Best match
                    </span>
                        <img src="{{ asset('images/space_card2.png') }}" alt="Paws &amp; Bubbles">
                    </div>
                    <div class="map-space-card-body">
                        <div class="map-space-card-head">
                            <div>
                                <h3>Paws &amp; Bubbles</h3>
                                <p>Hosted by Patrick B.</p>
                            </div>
                            <a class="map-space-card-msg" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener" aria-label="Message">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none" aria-hidden="true">
                            <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                            <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                        </svg>
                            </a>
                        </div>
                        <div class="map-space-card-meta">
                            <span class="map-space-card-type">Mobile Station</span>
                            <span class="map-space-card-rating">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z" fill="#FFC97A" />
                        </svg>
                                4.3 <small>(20)</small>
                            </span>
                        </div>
                        <p class="map-space-card-distance">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14" viewBox="0 0 10 14" fill="none" aria-hidden="true">
                            <path d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z" fill="#D9D9D9" />
                        </svg>
                            2.5 mi away
                        </p>
                        <span class="map-space-card-avail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                            Free at 14:30-15:30
                        </span>
                        <div class="map-space-card-foot">
                            <strong>&pound;28.00 <span>p/h</span></strong>
                            <button type="button" class="map-space-card-select" aria-pressed="true">Selected</button>
                        </div>
                    </div>
                </article>
                <article class="map-space-card" data-name="The Garden Grooming Spot" data-total="106.00" data-price="38.00">
                    <div class="map-space-card-photo">
                    <div class="top-left-svg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 18 19" fill="none" aria-hidden="true">
                        <path d="M9.1404 0.104606C8.98891 0.036071 8.82659 0 8.65705 0C8.48752 0 8.3252 0.036071 8.1737 0.104606L1.38154 2.98668C0.587973 3.32214 -0.00359069 4.10488 1.64069e-05 5.04994C0.0180519 8.62818 1.48975 15.1751 7.70478 18.1509C8.30717 18.4395 9.00694 18.4395 9.60933 18.1509C15.8244 15.1751 17.2961 8.62818 17.3141 5.04994C17.3177 4.10488 16.7261 3.32214 15.9326 2.98668L9.1404 0.104606Z" fill="#CBDCE8" />
                        <path d="M13.3581 5.00928L9.6475 8.95176L13.3581 5.00928ZM7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" fill="#CBDCE8" />
                        <path d="M13.3581 5.00928L9.6475 8.95176M7.99907 8.75928C6.84879 9.20084 5.92904 9.12523 5.00928 8.76067C5.24119 11.7491 6.63451 12.898 8.49212 13.3581C8.49212 13.3581 9.89147 12.3683 10.0932 10.0218C10.115 9.76763 10.1257 9.641 10.0733 9.49768C10.0204 9.35436 9.91651 9.25186 9.70918 9.04638C9.36781 8.70872 9.19759 8.53989 8.9949 8.49722C8.79221 8.45547 8.52783 8.55659 7.99907 8.75928Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747L5.70508 11.2454Z" fill="#CBDCE8" />
                        <path d="M5.70508 11.2454C5.70508 11.2454 6.86463 11.4699 8.02419 10.5747" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M7.56043 6.98041C7.56043 7.13418 7.49935 7.28165 7.39062 7.39038C7.28189 7.49911 7.13442 7.56019 6.98066 7.56019C6.82689 7.56019 6.67942 7.49911 6.57069 7.39038C6.46196 7.28165 6.40088 7.13418 6.40088 6.98041C6.40088 6.82665 6.46196 6.67918 6.57069 6.57045C6.67942 6.46172 6.82689 6.40063 6.98066 6.40063C7.13442 6.40063 7.28189 6.46172 7.39062 6.57045C7.49935 6.67918 7.56043 6.82665 7.56043 6.98041Z" fill="#CBDCE8" stroke="white" />
                        <path d="M8.72021 5.47266V5.51904V5.47266Z" fill="#CBDCE8" />
                        <path d="M8.72021 5.47266V5.51904" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </div>
                    <span class="map-space-card-best">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none" aria-hidden="true">
                            <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                        </svg>
                        Best match
                    </span>
                        <img src="{{ asset('images/space_card3.png') }}" alt="The Garden Grooming Spot">
                    </div>
                    <div class="map-space-card-body">
                        <div class="map-space-card-head">
                            <div>
                                <h3>The Garden Grooming Spot</h3>
                                <p>Hosted by Chloe D.</p>
                            </div>
                            <a class="map-space-card-msg" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener" aria-label="Message">
                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none" aria-hidden="true">
                            <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                            <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                        </svg>
                            </a>
                        </div>
                        <div class="map-space-card-meta">
                            <span class="map-space-card-type">Garden / Shed</span>
                            <span class="map-space-card-rating">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                            <path d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z" fill="#FFC97A" />
                        </svg>
                                4.3 <small>(20)</small>
                            </span>
                        </div>
                        <p class="map-space-card-distance">
                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14" viewBox="0 0 10 14" fill="none" aria-hidden="true">
                            <path d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z" fill="#D9D9D9" />
                        </svg>
                            2.5 mi away
                        </p>
                        <span class="map-space-card-avail">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none" aria-hidden="true">
                            <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#A4C25C" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                            Free at 14:30-15:30
                        </span>
                        <div class="map-space-card-foot">
                            <strong>&pound;38.00 <span>p/h</span></strong>
                            <button type="button" class="map-space-card-select" aria-pressed="false">Select</button>
                        </div>
                    </div>
                </article>
                        </div>
                        <div class="map-view-map">
                            <div class="map-wrapper modal-map-wrapper">
                                <div id="modal-map" data-map-type="space"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-list-footer map-shared-footer">
                    <p id="spaceSelectionLabel">Select a space to continue</p>
                    <button type="button" class="space-continue" id="spaceContinueBtn" disabled>Select a Space</button>
                </div>

                <div class="space-gallery" id="spaceGallery" hidden>
                    <div class="space-gallery-dialog" role="dialog" aria-modal="true" aria-label="Space photos">
                        <button type="button" class="space-gallery-close" aria-label="Close gallery">&times;</button>
                        <button type="button" class="space-gallery-nav prev" aria-label="Previous photo">&#8592;</button>
                        <button type="button" class="space-gallery-nav next" aria-label="Next photo">&#8594;</button>
                        <img id="spaceGalleryImg" alt="">
                        <p class="space-gallery-caption" id="spaceGalleryCaption"></p>
                    </div>
                </div>

            </div>
        </div>

        <!-- Modal 1 -->

        <div class="row">
            <div class="col-lg-12">
                <div class="profile-header" role="banner">
                    <div class="left">
                        <div class="avatar-wrap">
                            <img class="avatar" src="{{ asset('images/groomer-profile.png') }}" alt="Sarah's avatar">
                            <div class="badge-shield" title="Verified">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="33" viewBox="0 0 30 33" fill="none">
                                    <ellipse cx="15.873" cy="16.5256" rx="9.3645" ry="8.81365" fill="white" />
                                    <path d="M15.44 0.185076C15.1841 0.0638192 14.9099 0 14.6235 0C14.3372 0 14.063 0.0638192 13.8071 0.185076L2.3337 5.28423C0.993208 5.87775 -0.00606541 7.26263 2.77146e-05 8.93469C0.0304934 15.2656 2.51649 26.8487 13.015 32.1138C14.0325 32.6244 15.2146 32.6244 16.2321 32.1138C26.7306 26.8487 29.2166 15.2656 29.247 8.93469C29.2531 7.26263 28.2539 5.87775 26.9134 5.28423L15.44 0.185076ZM8.82897 18.2651C9.12144 18.3416 9.43219 18.3799 9.74903 18.3799C11.8999 18.3799 13.6486 16.5483 13.6486 14.2955V10.2111H16.3418C17.0791 10.2111 17.7554 10.645 18.0844 11.3407L18.5231 12.2533H22.4227C22.9589 12.2533 23.3976 12.7128 23.3976 13.2744V15.3166C23.3976 18.1374 21.2163 20.4222 18.5231 20.4222H15.5984V23.6578C15.5984 24.1237 15.2389 24.5066 14.7881 24.5066C14.6784 24.5066 14.5687 24.4811 14.4712 24.4364L8.45729 21.7368C8.05514 21.5581 7.79923 21.1433 7.79923 20.6902C7.79923 20.5115 7.83579 20.3392 7.915 20.1796L8.82897 18.2651ZM8.77413 10.2111H11.6988V14.2955C11.6988 15.4251 10.8275 16.3377 9.74903 16.3377C8.67055 16.3377 7.79923 15.4251 7.79923 14.2955V11.2322C7.79923 10.6706 8.23794 10.2111 8.77413 10.2111ZM16.5733 13.2744C16.5733 13.0036 16.4706 12.7439 16.2878 12.5524C16.105 12.3609 15.857 12.2533 15.5984 12.2533C15.3399 12.2533 15.0919 12.3609 14.9091 12.5524C14.7262 12.7439 14.6235 13.0036 14.6235 13.2744C14.6235 13.5452 14.7262 13.8049 14.9091 13.9964C15.0919 14.1879 15.3399 14.2955 15.5984 14.2955C15.857 14.2955 16.105 14.1879 16.2878 13.9964C16.4706 13.8049 16.5733 13.5452 16.5733 13.2744Z" fill="#C9DDA0" />
                                </svg>
                            </div>
                        </div>

                        <div class="meta">
                            <h1>Sarah's Grooming Studio</h1>
                            <div class="owner">Sarah W. · Home visits & mobile station</div>

                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="tags-row mt-2">
                                    <span class="pill popular">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                                            <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                                        </svg>
                                        Popular
                                    </span>
                                    <span class="pill toprated">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="11" viewBox="0 0 9 11" fill="none">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.79701 0.30821C3.81025 0.23997 3.84191 0.176658 3.88858 0.125138C3.93524 0.0736173 3.99512 0.0358558 4.06172 0.0159479C4.12832 -0.00396001 4.1991 -0.00525449 4.26638 0.0122049C4.33367 0.0296642 4.39489 0.0652111 4.4434 0.114991C4.56671 0.240837 4.80378 0.489988 5.04467 0.777274C5.28111 1.05884 5.53916 1.39761 5.68788 1.69951C5.8328 1.99443 5.98661 2.37578 6.10801 2.69421L6.67305 1.75354C6.70456 1.701 6.74826 1.65683 6.80046 1.62476C6.85265 1.59269 6.91181 1.57367 6.97292 1.56931C7.03402 1.56494 7.09528 1.57536 7.1515 1.59969C7.20773 1.62401 7.25727 1.66152 7.29592 1.70905C8.09867 2.70057 8.49846 3.76263 8.6974 4.57365C8.79718 4.97979 8.8474 5.32491 8.87282 5.57025C8.88576 5.69278 8.89424 5.81574 8.89824 5.93889V5.97131C8.89824 8.4482 6.93364 10.4649 4.44785 10.4649C1.96206 10.4649 0 8.44756 0 5.97004C0 5.28805 0.322244 3.68192 1.27563 2.36498C1.31266 2.31422 1.36166 2.27341 1.41826 2.24615C1.47487 2.2189 1.53734 2.20605 1.60011 2.20876C1.66287 2.21146 1.724 2.22963 1.77806 2.26166C1.83211 2.29368 1.87741 2.33856 1.90994 2.39231L2.55507 3.46709C2.75083 3.16073 3.01269 2.73044 3.21163 2.3332C3.49765 1.76117 3.72455 0.682572 3.79701 0.308845M4.3201 0.912655C4.20506 1.42113 4.01501 2.14697 3.77985 2.61858C3.46714 3.24336 3.0165 3.9298 2.86142 4.16051C2.82554 4.21345 2.77693 4.25651 2.72005 4.28574C2.66317 4.31497 2.59986 4.32943 2.53593 4.32778C2.472 4.32614 2.40952 4.30844 2.35422 4.27632C2.29892 4.24421 2.25258 4.1987 2.21948 4.14399L1.57118 3.06476C0.87457 4.19166 0.635589 5.45839 0.635589 5.97131C0.635589 8.10561 2.32244 9.82806 4.44785 9.82806C6.57326 9.82806 8.26265 8.10561 8.26265 5.97131V5.95351L8.26011 5.88995C8.25568 5.80484 8.24911 5.71986 8.24041 5.63508C8.20712 5.3287 8.15362 5.02487 8.08024 4.72555C7.87826 3.89157 7.52027 3.10334 7.02516 2.40248L6.38068 3.47535C6.34338 3.53732 6.28925 3.58743 6.22459 3.61985C6.15993 3.65227 6.08739 3.66566 6.01542 3.65847C5.94344 3.65128 5.87499 3.6238 5.81802 3.57923C5.76105 3.53466 5.71791 3.47483 5.6936 3.40671C5.59064 3.11815 5.33831 2.42917 5.11713 1.98044C5.00463 1.751 4.78916 1.46117 4.55781 1.18596C4.4801 1.09353 4.40086 1.00242 4.3201 0.912655Z" fill="#FEFEFE" />
                                        </svg>
                                        Top Rated
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="right">
                        <div class="action-row">
                            <div class="fav" role="button" aria-pressed="false" title="Add to favourites">
                                <svg xmlns="http://www.w3.org/2000/svg" width="23" height="21" viewBox="0 0 23 21" fill="none">
                                    <path d="M11.25 4.3125C11.25 4.3125 11.25 4.3125 10.3633 3.125C9.33667 1.7475 7.82 0.75 6 0.75C3.095 0.75 0.75 3.13687 0.75 6.09375C0.75 7.19813 1.07667 8.21938 1.63667 9.0625C2.58167 10.4994 11.25 19.75 11.25 19.75M11.25 4.3125C11.25 4.3125 11.25 4.3125 12.1367 3.125C13.1633 1.7475 14.68 0.75 16.5 0.75C19.405 0.75 21.75 3.13687 21.75 6.09375C21.75 7.19813 21.4233 8.21938 20.8633 9.0625C19.9183 10.4994 11.25 19.75 11.25 19.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span>Add to Favourites</span>
                            </div>

                            <!-- <button class="icon-btn" title="Copy link">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none">
                                    <path d="M8 7h9v9" stroke="#333" stroke-width="1.4" stroke-linecap="round"
                                        stroke-linejoin="round" />
                                    <rect x="3" y="3" width="13" height="13" rx="2" stroke="#333" stroke-width="1.4" />
                                </svg>
                            </button>
                            <button class="icon-btn" title="Share">
                                <svg xmlns="http://www.w3.org/2000/svg" width="17" height="20" viewBox="0 0 17 20" fill="none">
                                    <path d="M0.75 11.25V17.25C0.75 17.6478 0.893668 18.0294 1.1494 18.3107C1.40513 18.592 1.75198 18.75 2.11364 18.75H14.3864C14.748 18.75 15.0949 18.592 15.3506 18.3107C15.6063 18.0294 15.75 17.6478 15.75 17.25V11.25M8.25 13.5V1.125M12.3409 5.25L8.25 0.75L4.15909 5.25" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button> -->

                            <!-- <span class="fs-14-600-f-color" id="copy-msg" style="display:none; margin-left:10px; color:green;">
                                Link copied!
                            </span> -->

                            <div class="copy-wrapper">
                                <div id="copy-msg" class="copy-tooltip">
                                    Link copied!
                                </div>

                                <svg class="cursor" id="copy-link" xmlns="http://www.w3.org/2000/svg" width="16" height="20" viewBox="0 0 16 20" fill="none">
                                    <!-- Front document -->
                                    <mask id="path-inside" fill="white">
                                        <rect x="3.12488" y="3.12497" width="11.875" height="16.625" rx="1" />
                                    </mask>
                                    <rect x="3.12488" y="3.12497" width="11.875" height="16.625" rx="1" stroke="#3B3731" stroke-width="3" mask="url(#path-inside)" />
                                    <!-- Back document -->
                                    <path d="M12.625 3.125V1.75C12.625 1.19772 12.1773 0.75 11.625 0.75H1.75C1.19772 0.75 0.75 1.19772 0.75 1.75V16.375C0.75 16.9273 1.19771 17.375 1.75 17.375H3.125" stroke="#3B3731" stroke-width="1.5" />
                                </svg>
                            </div>
                            <svg class="cursor" data-modal-open="social_media_share" xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19" fill="none">
                                <path d="M12.5755 13.9084C12.2195 14.6202 12.161 15.4443 12.4127 16.1993C12.6643 16.9543 13.2056 17.5784 13.9175 17.9344C14.6293 18.2903 15.4534 18.3488 16.2084 18.0972C16.9634 17.8455 17.5875 17.3042 17.9435 16.5924C18.2994 15.8805 18.3579 15.0564 18.1063 14.3014C17.8546 13.5464 17.3133 12.9223 16.6015 12.5664C16.249 12.3901 15.8653 12.285 15.4722 12.2571C15.0791 12.2292 14.6844 12.2789 14.3105 12.4035C13.5555 12.6552 12.9314 13.1965 12.5755 13.9084ZM12.5755 13.9084L6.44346 10.8424M6.44346 8.15836C6.26911 7.80288 6.02611 7.48543 5.72849 7.22432C5.43086 6.9632 5.08449 6.76358 4.70935 6.63697C4.3342 6.51036 3.93771 6.45925 3.54272 6.48661C3.14773 6.51397 2.76207 6.61924 2.40796 6.79636C2.05385 6.97348 1.73831 7.21894 1.47952 7.5186C1.22073 7.81825 1.02382 8.16616 0.900134 8.54228C0.77645 8.9184 0.728442 9.31529 0.758877 9.71005C0.789311 10.1048 0.897588 10.4896 1.07746 10.8424C1.43709 11.5476 2.06086 12.082 2.81284 12.3293C3.56483 12.5766 4.38407 12.5166 5.09204 12.1625C5.80001 11.8084 6.33933 11.1888 6.59247 10.4388C6.84561 9.68876 6.79205 8.86907 6.44346 8.15836ZM6.44346 8.15836L12.5755 5.09236M12.5755 5.09236C12.9314 5.80407 13.5555 6.34524 14.3104 6.59682C14.6842 6.72139 15.0789 6.77111 15.4719 6.74315C15.8649 6.71519 16.2486 6.61009 16.601 6.43386C16.9534 6.25762 17.2676 6.01371 17.5257 5.71603C17.7839 5.41835 17.9809 5.07274 18.1054 4.69894C18.23 4.32514 18.2797 3.93046 18.2518 3.53744C18.2238 3.14442 18.1187 2.76076 17.9425 2.40836C17.5865 1.69665 16.9625 1.15548 16.2075 0.9039C15.4526 0.65232 14.6287 0.710938 13.917 1.06686C13.2053 1.42278 12.6641 2.04685 12.4125 2.80177C12.1609 3.5567 12.2195 4.38065 12.5755 5.09236Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                            </svg>

                            <div class="stat">
                                <svg class="block-svg cursor" xmlns="http://www.w3.org/2000/svg" width="25" height="5" viewBox="0 0 25 5" fill="none">
                                    <circle cx="2.5" cy="2.5" r="2.5" fill="#3B3731" />
                                    <circle cx="12.5" cy="2.5" r="2.5" fill="#3B3731" />
                                    <circle cx="22.5" cy="2.5" r="2.5" fill="#3B3731" />
                                </svg>

                                <div class="block-btn-div">
                                    <button type="button" class="block-btn d-flex align-items-center justify-content-between" data-modal-open="block_profile_modal">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                            <path d="M5.99805 0.5C6.76207 0.500006 7.47427 0.644741 8.13965 0.931641C8.81419 1.22232 9.39675 1.61443 9.89062 2.10742C10.3843 2.60033 10.7773 3.18202 11.0684 3.85645C11.3553 4.52143 11.5 5.23387 11.5 5.99805C11.5 6.7623 11.355 7.47509 11.0684 8.14062C10.7777 8.81532 10.3852 9.39739 9.8916 9.89062C9.3975 10.3842 8.8159 10.7769 8.14258 11.0684C7.47936 11.3555 6.76738 11.5004 6.00195 11.5C5.2377 11.5 4.52491 11.355 3.85938 11.0684H3.8584C3.18441 10.7773 2.60294 10.3848 2.10938 9.8916C1.61542 9.39805 1.2227 8.81625 0.931641 8.14258C0.644906 7.47878 0.5 6.76695 0.5 6.00195C0.500006 5.23688 0.6448 4.52471 0.931641 3.86035C1.22239 3.18564 1.61474 2.6033 2.10742 2.10938C2.59984 1.61574 3.18152 1.22271 3.85645 0.931641C4.52182 0.644757 5.23404 0.5 5.99805 0.5Z" stroke="#3B3731" />
                                            <line x1="1.76517" y1="1.76409" x2="10.2358" y2="10.2347" stroke="#3B3731" />
                                        </svg>
                                        Block Groomer
                                    </button>
                                </div>
                            </div>

                        </div>

                        <div class="meta-stats mt-2">
                            <div class="stat"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                    <path d="M8.75651 0.943537C9.14791 -0.314515 10.8521 -0.314511 11.2435 0.943541L12.7078 5.65027C12.8829 6.21288 13.3849 6.5938 13.9513 6.5938H18.69C19.9566 6.5938 20.4832 8.2865 19.4585 9.06402L15.6249 11.9729C15.1666 12.3207 14.9748 12.937 15.1499 13.4996L16.6142 18.2063C17.0056 19.4644 15.6269 20.5105 14.6022 19.733L10.7685 16.8241C10.3103 16.4764 9.68974 16.4764 9.23148 16.8241L5.3978 19.733C4.37311 20.5105 2.99439 19.4644 3.38579 18.2063L4.85012 13.4996C5.02516 12.937 4.83341 12.3207 4.37515 11.9729L0.541471 9.06402C-0.483225 8.2865 0.0434023 6.5938 1.31 6.5938H6.04868C6.61512 6.5938 7.11714 6.21288 7.29217 5.65027L8.75651 0.943537Z" fill="#FFC97A" />
                                </svg><span>4.3</span><span style="color:var(--muted); font-weight:400;">(20 reviews)</span></div>
                            <div class="stat distance"><svg xmlns="http://www.w3.org/2000/svg" width="14" height="20" viewBox="0 0 14 20" fill="none">
                                    <path d="M7 9.5C6.33696 9.5 5.70107 9.23661 5.23223 8.76777C4.76339 8.29893 4.5 7.66304 4.5 7C4.5 6.33696 4.76339 5.70107 5.23223 5.23223C5.70107 4.76339 6.33696 4.5 7 4.5C7.66304 4.5 8.29893 4.76339 8.76777 5.23223C9.23661 5.70107 9.5 6.33696 9.5 7C9.5 7.3283 9.43534 7.65339 9.3097 7.95671C9.18406 8.26002 8.99991 8.53562 8.76777 8.76777C8.53562 8.99991 8.26002 9.18406 7.95671 9.3097C7.65339 9.43534 7.3283 9.5 7 9.5ZM7 0C5.14348 0 3.36301 0.737498 2.05025 2.05025C0.737498 3.36301 0 5.14348 0 7C0 12.25 7 20 7 20C7 20 14 12.25 14 7C14 5.14348 13.2625 3.36301 11.9497 2.05025C10.637 0.737498 8.85652 0 7 0Z" fill="#DEDEDE" />
                                </svg><span>2.5 mi</span></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="image-grid mt-4">
                    <div class="image-grid-item large-image" style="grid-area: img-1;">
                        <img src="{{ asset('images/profile-img-1.png') }}"
                            alt="Profile Image 1"
                            data-desc="A calm, one-to-one grooming session in a clean, well-equipped space, with gentle handling to keep pets relaxed and comfortable.">
                    </div>

                    <div class="image-grid-item large-image" style="grid-area: img-2;">
                        <img src="{{ asset('images/profile-img-2.png') }}"
                            alt="Grooming in Progress"
                            data-desc="A calm, one-to-one grooming session in a clean, well-equipped space, with gentle handling to keep pets relaxed and comfortable.">
                    </div>

                    <div class="image-grid-item small-image" style="grid-area: img-3;">
                        <img src="{{ asset('images/profile-img-3.png') }}"
                            alt="Profile Image 3"
                            data-desc="Happy pets enjoying a stress-free grooming experience with professional care.">
                    </div>

                    <div class="image-grid-item small-image" style="grid-area: img-4;">
                        <img src="{{ asset('images/profile-img-4.png') }}"
                            alt="Profile Image 4"
                            data-desc="Golden-hour styling finish with detailed coat care and professional grooming precision.">
                    </div>

                    <div class="image-grid-item small-image" style="grid-area: img-5;">
                        <img src="{{ asset('images/profile-img-5.png') }}"
                            alt="Profile Image 5"
                            data-desc="Final grooming touch ensuring pets look clean, neat, and perfectly maintained.">
                    </div>
                    <div class="image-grid-item small-image last-image-text" style="grid-area: img-6;">
                        <img src="{{ asset('images/profile-img-6.png') }}"
                            alt="Profile Image 6"
                            data-desc="Premium grooming result highlighting hygiene, comfort, and expert finishing detail.">

                        <p class="show-all-pics">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M9.77778 4.88889C10.0861 4.88879 10.3831 5.00525 10.6092 5.21491C10.8353 5.42458 10.9738 5.71196 10.9969 6.01944L11 6.11111V9.77778C11.0001 10.0861 10.8836 10.3831 10.674 10.6092C10.4643 10.8353 10.1769 10.9738 9.86944 10.9969L9.77778 11H7.33333C7.02498 11.0001 6.72799 10.8836 6.50189 10.674C6.27579 10.4643 6.13729 10.1769 6.11417 9.86944L6.11111 9.77778V6.11111C6.11101 5.80276 6.22747 5.50576 6.43714 5.27966C6.6468 5.05357 6.93418 4.91507 7.24167 4.89194L7.33333 4.88889H9.77778ZM3.66667 7.33333C3.99082 7.33333 4.3017 7.4621 4.53091 7.69131C4.76012 7.92053 4.88889 8.2314 4.88889 8.55556V9.77778C4.88889 10.1019 4.76012 10.4128 4.53091 10.642C4.3017 10.8712 3.99082 11 3.66667 11H1.22222C0.898069 11 0.587192 10.8712 0.357981 10.642C0.128769 10.4128 0 10.1019 0 9.77778V8.55556C0 8.2314 0.128769 7.92053 0.357981 7.69131C0.587192 7.4621 0.898069 7.33333 1.22222 7.33333H3.66667ZM3.66667 0C3.99082 0 4.3017 0.128769 4.53091 0.357981C4.76012 0.587192 4.88889 0.898069 4.88889 1.22222V4.88889C4.88889 5.21304 4.76012 5.52392 4.53091 5.75313C4.3017 5.98234 3.99082 6.11111 3.66667 6.11111H1.22222C0.898069 6.11111 0.587192 5.98234 0.357981 5.75313C0.128769 5.52392 0 5.21304 0 4.88889V1.22222C0 0.898069 0.128769 0.587192 0.357981 0.357981C0.587192 0.128769 0.898069 0 1.22222 0H3.66667ZM9.77778 0C10.1019 0 10.4128 0.128769 10.642 0.357981C10.8712 0.587192 11 0.898069 11 1.22222V2.44444C11 2.7686 10.8712 3.07947 10.642 3.30869C10.4128 3.5379 10.1019 3.66667 9.77778 3.66667H7.33333C7.00918 3.66667 6.6983 3.5379 6.46909 3.30869C6.23988 3.07947 6.11111 2.7686 6.11111 2.44444V1.22222C6.11111 0.898069 6.23988 0.587192 6.46909 0.357981C6.6983 0.128769 7.00918 0 7.33333 0H9.77778Z" fill="white" />
                            </svg>
                            Show all photos
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lb-overlay" id="lbOverlay" onclick="handleBgClick(event)">
            <div class="lb-modal" id="lbModal">
                <div class="lb-header">
                    <div class="lb-title-block">
                        <span class="lb-title">Sarah's Grooming Studio</span>
                        <span class="lb-subtitle">Sarah W.</span>
                    </div>

                    <div class="lb-header-right">

                        <svg class="cursor" onclick="closeLb()" xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                            <circle cx="18" cy="18" r="17" stroke="white" stroke-width="2" />
                            <path d="M12.7998 23.9998L23.9998 12.7998M12.7998 12.7998L23.9998 23.9998" stroke="white" stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>
                </div>

                <div class="lb-image-wrap">
                    <img id="lbImg" src="" alt="">
                </div>

                <div class="lb-nav-bar">
                    <svg class="cursor" onclick="slide(-1)" xmlns="http://www.w3.org/2000/svg" width="13" height="24" viewBox="0 0 13 24" fill="none">
                        <path d="M11.8719 22.5615L1 11.6897L11.6894 1.00031" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>

                    <div class="lb-caption-block">
                        <div class="lb-cap-title" id="lbTitle"></div>
                        <div class="lb-cap-desc" id="lbDesc"></div>
                    </div>

                    <div class="lb-count" id="lbCount"></div>

                    <svg class="cursor" onclick="slide(1)" xmlns="http://www.w3.org/2000/svg" width="13" height="24" viewBox="0 0 13 24" fill="none">
                        <path d="M1 22.5615L11.8719 11.6897L1.18251 1.00031" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </div>

                <div class="lb-thumbs" id="lbThumbs"></div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-4">
                <div id="booking-sidebar" class="service-card mt-4 mb-5">
                    <div class="service__header">
                        <div class="message-btn-div d-flex justify-content-center">
                            <a href="{{ url('/messages_notification/messages') }}" class="message-btn mt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="16" viewBox="0 0 19 16" fill="none">
                                    <path d="M0.75 7.75C0.75 4.45038 0.75 2.80012 1.7755 1.7755C2.801 0.750875 4.45038 0.75 7.75 0.75H11.25C14.5496 0.75 16.1999 0.75 17.2245 1.7755C18.2491 2.801 18.25 4.45038 18.25 7.75C18.25 11.0496 18.25 12.6999 17.2245 13.7245C16.199 14.7491 14.5496 14.75 11.25 14.75H7.75C4.45038 14.75 2.80012 14.75 1.7755 13.7245C0.750875 12.699 0.75 11.0496 0.75 7.75Z" stroke="#FBAC83" stroke-width="1.5" />
                                    <path d="M4.25 4.25L6.13913 5.825C7.7465 7.16375 8.54975 7.83313 9.5 7.83313C10.4502 7.83313 11.2544 7.16375 12.8609 5.82412L14.75 4.25" stroke="#FBAC83" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                Message Groomer
                            </a>
                        </div>
                        <p class="responding-time text-center mt-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="9" viewBox="0 0 13 9" fill="none">
                                <path d="M0.5 4.5L4.5 8.5L12.5 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Typically responds within 1 hour.
                        </p>
                    </div>

                    <div class="service__availability mt-4">
                        <div class="booking-starting-price">
                            <span class="booking-starting-price__amount">£10</span>
                            <span class="booking-starting-price__label">starting price</span>
                        </div>

                        <div class="service-type-select booking-step mt-4">
                            <p class="booking-step__label">
                                <span class="booking-step__num">1</span>
                                Choose a service type
                            </p>
                            <div class="custom-select" data-multiselect data-pill="accent" data-summary="service" data-placeholder="Select services">
                                <div class="select-trigger">
                                    <span class="selected-text">Select services</span>
                                    <span class="select-trigger-meta">
                                        <span class="selected-price"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                            <path d="M13.8737 0.5L7.13022 7.24344L0.499976 0.613201" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                <ul class="select-options">
                                    <li data-value="full-groom" data-label="Full Groom" data-price="£45" data-amount="45">
                                        <span class="option-label">Full Groom</span>
                                        <span class="option-price">£45</span>
                                    </li>
                                    <li data-value="face-trim" data-label="Face Trim Only" data-price="£25" data-amount="25">
                                        <span class="option-label">Face Trim Only</span>
                                        <span class="option-price">£25</span>
                                    </li>
                                    <li data-value="tail-trim" data-label="Tail Trim Only" data-price="£20" data-amount="20">
                                        <span class="option-label">Tail Trim Only</span>
                                        <span class="option-price">£20</span>
                                    </li>
                                    <li data-value="bath-brush" data-label="Bath &amp; Brush" data-price="£30" data-amount="30">
                                        <span class="option-label">Bath &amp; Brush</span>
                                        <span class="option-price">£30</span>
                                    </li>
                                </ul>
                                <input type="hidden" name="serviceType">
                            </div>
                            <div class="service-selected-options d-flex align-items-center flex-wrap gap-10 mt-4"></div>
                        </div>

                        <div class="service-type-select booking-step mt-5">
                            <p class="booking-step__label">
                                <span class="booking-step__num">2</span>
                                Add-ons <span class="booking-step__optional">(optional)</span>
                            </p>
                            <div class="custom-select" data-multiselect data-pill="muted" data-summary="addons" data-placeholder="Select add-ons">
                                <div class="select-trigger">
                                    <span class="selected-text">Select add-ons</span>
                                    <span class="select-trigger-meta">
                                        <span class="selected-price"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                            <path d="M13.8737 0.5L7.13022 7.24344L0.499976 0.613201" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                <ul class="select-options">
                                    <li data-value="hypoallergenic-shampoo-upgrade" data-label="Hypoallergenic Shampoo Upgrade" data-price="+£10" data-amount="10">
                                        <span class="option-label">Hypoallergenic Shampoo Upgrade</span>
                                        <span class="option-price">+£10</span>
                                    </li>
                                    <li data-value="flea-tick-treatment" data-label="Flea &amp; Tick Treatment" data-price="+£10" data-amount="10">
                                        <span class="option-label">Flea &amp; Tick Treatment</span>
                                        <span class="option-price">+£10</span>
                                    </li>
                                    <li data-value="nail-trim" data-label="Nail Trim" data-price="+£5" data-amount="5">
                                        <span class="option-label">Nail Trim</span>
                                        <span class="option-price">+£5</span>
                                    </li>
                                    <li data-value="hypoallergenic-shampoo" data-label="Hypoallergenic Shampoo" data-price="+£6" data-amount="6">
                                        <span class="option-label">Hypoallergenic Shampoo</span>
                                        <span class="option-price">+£6</span>
                                    </li>
                                </ul>
                                <input type="hidden" name="extrasAddons">
                            </div>
                            <div class="service-selected-options d-flex align-items-center flex-wrap gap-10 mt-3"></div>
                        </div>

                        <div class="booking-step mt-5">
                            <p class="booking-step__label">
                                <span class="booking-step__num">3</span>
                                Pick a date
                            </p>
                            <div class="calendar">
                                <div class="calendar-header">
                                    <button type="button" class="nav-btn" aria-label="Previous month">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="11" viewBox="0 0 7 11" fill="none">
                                            <path d="M5.53426 10.484L0.499999 5.44975L5.44975 0.500005" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                    <span>October 2025</span>
                                    <button type="button" class="nav-btn" aria-label="Next month">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="7" height="11" viewBox="0 0 7 11" fill="none">
                                            <path d="M0.5 10.484L5.53426 5.44975L0.58451 0.500005" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>

                                <div class="weekdays mt-3">
                                    <div>M</div>
                                    <div>T</div>
                                    <div>W</div>
                                    <div>T</div>
                                    <div>F</div>
                                    <div>S</div>
                                    <div>S</div>
                                </div>

                                <div class="dates mt-3"></div>
                            </div>
                        </div>

                        <div class="booking-step mt-5">
                            <p class="booking-step__label">
                                <span class="booking-step__num">4</span>
                                Pick a time
                            </p>
                            <div class="times booking-times">
                                <button type="button" class="time selected" data-time="09:00">09:00</button>
                                <button type="button" class="time" data-time="11:00">11:00</button>
                                <button type="button" class="time" data-time="12:00">12:00</button>
                                <button type="button" class="time" data-time="16:00">16:00</button>
                                <button type="button" class="time" data-time="17:30">17:30</button>
                                <button type="button" class="time" data-time="18:00">18:00</button>
                                <button type="button" class="time" data-time="19:00">19:00</button>
                                <button type="button" class="time" data-time="19:45">19:45</button>
                            </div>
                        </div>

                        <div class="booking-summary mt-4">
                            <div class="booking-summary__section" data-summary-section="service" hidden>
                                <p class="booking-summary__heading">Service</p>
                                <div class="booking-summary__rows" data-summary-rows="service"></div>
                            </div>

                            <div class="booking-summary__section mt-3" data-summary-section="addons" hidden>
                                <p class="booking-summary__heading" data-summary-addons-heading>Add-Ons (0)</p>
                                <div class="booking-summary__rows" data-summary-rows="addons"></div>
                            </div>

                            <div class="booking-summary__divider"></div>

                            <div class="booking-summary__total">
                                <span>Total</span>
                                <span class="booking-summary__total-amount" data-summary-total>£0.00</span>
                            </div>

                            <div class="book-btn-div mt-3">
                                <button type="button" class="book-btn booking-cta-btn" data-modal-open="groomer_prompt">Book for Oct 14, 09:00</button>
                            </div>

                            <p class="booking-cancel-note">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                                    <path d="M3.125 5L4.375 6.25L6.875 3.75" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="5" cy="5" r="4.5" stroke="#9D9B98" />
                                </svg>
                                Free cancellation up to 24 hours before appointment
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- <button class="normal-font-bold btn-custom btn-no-bg text-center" data-modal-open="groomer_prompt" style="color:#FBAC83;border:1px solid #FBAC83">Submit request</button> -->

            <!-- Modal  -->

            <div class="modal" id="groomer_prompt">
                <div class="modal-content">
                    <div class="groomer-cross-svg cursor d-flex justify-content-end" data-modal-close>
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                            <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                            <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </div>
                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="76" height="52" viewBox="0 0 76 52" fill="none">
                            <rect x="11.8813" y="1.00977" width="50.0001" height="17.8218" rx="8.91091" fill="#FFA899" />
                            <rect y="18.3369" width="50.0001" height="17.3268" rx="8.66339" fill="#FFA899" />
                            <rect x="25.2478" y="33.188" width="50.0001" height="17.8218" rx="8.91091" fill="#FFA899" />
                            <path d="M23.0199 18.9307L36.4798 6.89581C40.1369 3.631 41.9626 2 44.1833 2C46.4041 2 48.2326 3.631 51.8868 6.89581L65.3467 18.9307M27.2526 16.1089V49.9704M61.1141 49.9704V16.1089" stroke="#3B3731" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                            <path d="M13.1436 33.0396C15.4813 33.0396 17.3763 30.5129 17.3763 27.396C17.3763 24.2792 15.4813 21.7524 13.1436 21.7524C10.806 21.7524 8.91095 24.2792 8.91095 27.396C8.91095 30.5129 10.806 33.0396 13.1436 33.0396Z" stroke="#3B3731" stroke-width="4" />
                            <path d="M13.1437 33.0396V49.9703" stroke="#3B3731" stroke-width="4" stroke-linecap="round" />
                            <path d="M8.91095 49.9704H65.3467M37.1288 49.9704V38.6832C37.1288 36.0223 37.1288 34.6932 37.9556 33.8664C38.7824 33.0396 40.1115 33.0396 42.7724 33.0396H45.5942C48.2552 33.0396 49.5842 33.0396 50.411 33.8664C51.2378 34.6932 51.2378 36.0223 51.2378 38.6832V49.9704M41.3615 24.5743H47.0051M41.3615 16.1089H47.0051" stroke="#3B3731" stroke-width="4" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                        <p class="normal-font-weight font-bg-color mt-3" style="color:#FFA899">
                            You’ve selected a Visiting Groomer

                            <span class="tooltip cursor">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M5.4 8.4C5.4 8.73137 5.66863 9 6 9C6.33137 9 6.6 8.73137 6.6 8.4V6C6.6 5.66863 6.33137 5.4 6 5.4C5.66863 5.4 5.4 5.66863 5.4 6V8.4ZM6 4.2C6.17 4.2 6.3126 4.1424 6.4278 4.0272C6.543 3.912 6.6004 3.7696 6.6 3.6C6.5996 3.4304 6.542 3.288 6.4272 3.1728C6.3124 3.0576 6.17 3 6 3C5.83 3 5.6876 3.0576 5.5728 3.1728C5.458 3.288 5.4004 3.4304 5.4 3.6C5.3996 3.7696 5.4572 3.9122 5.5728 4.0278C5.6884 4.1434 5.8308 4.2008 6 4.2ZM6 12C5.17 12 4.39 11.8424 3.66 11.5272C2.93 11.212 2.295 10.7846 1.755 10.245C1.215 9.7054 0.7876 9.0704 0.4728 8.34C0.158001 7.6096 0.000400759 6.8296 7.59493e-07 6C-0.00039924 5.1704 0.157201 4.3904 0.4728 3.66C0.7884 2.9296 1.2158 2.2946 1.755 1.755C2.2942 1.2154 2.9292 0.788 3.66 0.4728C4.3908 0.1576 5.1708 0 6 0C6.8292 0 7.6092 0.1576 8.33999 0.4728C9.07079 0.788 9.7058 1.2154 10.245 1.755C10.7842 2.2946 11.2118 2.9296 11.5278 3.66C11.8438 4.3904 12.0012 5.1704 12 6C11.9988 6.8296 11.8412 7.6096 11.5272 8.34C11.2132 9.0704 10.7858 9.7054 10.245 10.245C9.7042 10.7846 9.06919 11.2122 8.33999 11.5278C7.6108 11.8434 6.8308 12.0008 6 12ZM6 10.8C7.34 10.8 8.47499 10.335 9.40499 9.405C10.335 8.475 10.8 7.34 10.8 6C10.8 4.66 10.335 3.525 9.40499 2.595C8.47499 1.665 7.34 1.2 6 1.2C4.66 1.2 3.525 1.665 2.595 2.595C1.665 3.525 1.2 4.66 1.2 6C1.2 7.34 1.665 8.475 2.595 9.405C3.525 10.335 4.66 10.8 6 10.8Z" fill="#FFA899" />
                                </svg>

                                <span class="tooltiptext">
                                    Visiting Groomers provide mobile services and don’t have a grooming space.
                                </span>
                            </span>
                        </p>
                        <h1 class="large-font text-center mt-3" style="line-height: normal;">Where would you like the <br> service to take place?</h1>
                    </div>

                    <div class="d-flex flex-column align-items-center justify-content-center">
                        <div class="groomer-find-card cursor d-flex flex-column justify-content-center mt-4">
                            <p class="medium-font-m-bold">At my home</p>
                            <p class="normal-light-color-font">The groomer comes to you.</p>
                        </div>

                        <div class="groomer-find-card cursor d-flex flex-column justify-content-center mt-4">
                            <p class="medium-font-m-bold">Find a grooming space for me</p>
                            <p class="normal-light-color-font">We’ll match you with a nearby available space.</p>
                        </div>
                    </div>

                    <div class="modal-footer d-flex align-items-center justify-content-center mt-4 gap-10">
                        <!-- <button class="modal-footer-btn">Go Back</button> -->
                        <button class="modal-footer-btn apply">Continue</button>
                    </div>

                </div>
            </div>

            <!-- Modal  -->

            <div class="col-lg-8">
                <div class="service-content mt-4 mb-5">
                    <div class="service-content-header about-studio">
                        <h2 class="section-content-heading about-studio__title">About Sarah’s Grooming Studio</h2>
                        <p class="about-studio__tagline">Luxury grooming with a gentle touch (tagline).</p>
                        <div class="about-studio__socials" aria-label="Social links">
                            <svg xmlns="http://www.w3.org/2000/svg" width="86" height="20" viewBox="0 0 86 20" fill="none">
                                <path d="M48.75 0.75H46.0227C44.8172 0.75 43.661 1.22411 42.8086 2.06802C41.9562 2.91193 41.4773 4.05653 41.4773 5.25V7.95H38.75V11.55H41.4773V18.75H45.1136V11.55H47.8409L48.75 7.95H45.1136V5.25C45.1136 5.01131 45.2094 4.78239 45.3799 4.6136C45.5504 4.44482 45.7816 4.35 46.0227 4.35H48.75V0.75Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M9.75 13.75C10.8109 13.75 11.8283 13.3286 12.5784 12.5784C13.3286 11.8283 13.75 10.8109 13.75 9.75C13.75 8.68913 13.3286 7.67172 12.5784 6.92157C11.8283 6.17143 10.8109 5.75 9.75 5.75C8.68913 5.75 7.67172 6.17143 6.92157 6.92157C6.17143 7.67172 5.75 8.68913 5.75 9.75C5.75 10.8109 6.17143 11.8283 6.92157 12.5784C7.67172 13.3286 8.68913 13.75 9.75 13.75Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M0.75 13.75V5.75C0.75 4.42392 1.27678 3.15215 2.21447 2.21447C3.15215 1.27678 4.42392 0.75 5.75 0.75H13.75C15.0761 0.75 16.3479 1.27678 17.2855 2.21447C18.2232 3.15215 18.75 4.42392 18.75 5.75V13.75C18.75 15.0761 18.2232 16.3479 17.2855 17.2855C16.3479 18.2232 15.0761 18.75 13.75 18.75H5.75C4.42392 18.75 3.15215 18.2232 2.21447 17.2855C1.27678 16.3479 0.75 15.0761 0.75 13.75Z" stroke="#3B3731" stroke-width="1.5" />
                                <path d="M15.25 4.26002L15.26 4.24902" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M84.749 7.95013C84.749 8.15856 84.5734 8.33003 84.3588 8.31961C83.2121 8.26378 82.0922 7.96294 81.079 7.43854C80.8049 7.29643 80.4566 7.48117 80.4566 7.78339V13.066C80.4565 14.1741 80.123 15.258 79.4969 16.1844C78.8709 17.1109 77.9797 17.8394 76.9329 18.2804C75.8861 18.7214 74.7295 18.8557 73.6051 18.6667C72.4808 18.4778 71.4379 17.9738 70.6046 17.2168C69.7713 16.4598 69.1841 15.4828 68.915 14.406C68.646 13.3291 68.7069 12.1994 69.0903 11.1557C69.4737 10.112 70.1629 9.19995 71.073 8.53163C71.9831 7.8633 73.0746 7.46789 74.2131 7.39401C74.264 7.3914 74.3148 7.399 74.3626 7.41634C74.4103 7.43368 74.4538 7.46038 74.4904 7.49479C74.527 7.52919 74.556 7.57056 74.5754 7.6163C74.5948 7.66204 74.6043 7.71118 74.6033 7.76065V10.4133C74.6033 10.6218 74.4277 10.7894 74.215 10.8236C73.7748 10.8955 73.3648 11.0881 73.0336 11.3788C72.7023 11.6695 72.4635 12.0461 72.3454 12.4642C72.2273 12.8823 72.2347 13.3245 72.3669 13.7387C72.499 14.1528 72.7503 14.5217 73.0911 14.8017C73.432 15.0817 73.8482 15.2613 74.2907 15.3192C74.7331 15.3771 75.1834 15.3109 75.5884 15.1285C75.9934 14.946 76.3363 14.6549 76.5766 14.2895C76.8169 13.9241 76.9446 13.4996 76.9446 13.066V1.12895C76.9446 1.02845 76.9857 0.932061 77.0589 0.860993C77.1321 0.789925 77.2314 0.75 77.3348 0.75H80.0664C80.1707 0.752584 80.2703 0.792844 80.3457 0.862918C80.4211 0.932991 80.4669 1.02785 80.4742 1.12895C80.5654 2.09976 81.0041 3.00881 81.714 3.69805C82.4239 4.38728 83.3601 4.81309 84.3598 4.90144C84.5744 4.92039 84.75 5.08808 84.75 5.29745L84.749 7.95013Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </div>
                        <p class="about-studio__bio">
                            Hi, I’m Sarah — a professional groomer in West London specialising in small breeds, anxious pets, and gentle handling. I’ve been grooming for over five years and take real care to make every visit calm and stress-free, whether I come to you or you visit my mobile station.
                        </p>
                        <div class="about-studio__badges">
                            <span class="about-studio__badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="8" fill="#D8E8B7" />
                                    <path d="M4.5 8.2L6.6 10.2L11.5 5.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                ID-Verified
                            </span>
                            <span class="about-studio__badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="8" fill="#D8E8B7" />
                                    <path d="M4.5 8.2L6.6 10.2L11.5 5.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                5+ years of experience
                            </span>
                            <span class="about-studio__badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="8" fill="#D8E8B7" />
                                    <path d="M4.5 8.2L6.6 10.2L11.5 5.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                City &amp; Guilds Certified
                            </span>
                            <span class="about-studio__badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="8" fill="#D8E8B7" />
                                    <path d="M4.5 8.2L6.6 10.2L11.5 5.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Insured
                            </span>
                            <span class="about-studio__badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="8" fill="#D8E8B7" />
                                    <path d="M4.5 8.2L6.6 10.2L11.5 5.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Animal-handling certified
                            </span>
                        </div>
                    </div>
                    <div class="tab-go-to-section d-flex align-items-center flex-wrap justify-content-center mt-5">
                        <a href="#services_and_pricing" class="active">Services</a>
                        <a href="#before_you_book">Before you book</a>
                        <a href="#reviews">Reviews</a>
                        <a href="#location">Location</a>
                    </div>
                    <div id="services_and_pricing" class="mt-5">
                        <h2 class="section-content-heading">Services & Pricing</h2>
                        <div class="services-list d-flex align-items-center justify-content-between mt-4">
                            <div class="service-list-left d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="27" viewBox="0 0 25 27" fill="none">
                                    <path d="M22.6808 2.95467C23.0165 2.61894 23.5608 2.61896 23.8965 2.95467C25.2459 4.30411 25.2257 6.44019 24.5413 8.49354C23.8387 10.6014 22.3573 12.9175 20.2931 14.9817C18.2289 17.0458 15.9128 18.5272 13.805 19.2299C11.7516 19.9143 9.61553 19.9345 8.26608 18.5851C7.93035 18.2494 7.93035 17.7051 8.26608 17.3694C8.60182 17.0337 9.14603 17.0337 9.48177 17.3694C10.1226 18.0102 11.4016 18.2186 13.2609 17.5989C15.0659 16.9972 17.1611 15.6823 19.0774 13.766C20.9938 11.8496 22.3087 9.75444 22.9103 7.94951C23.53 6.09018 23.3216 4.81116 22.6808 4.17035C22.3451 3.83462 22.3451 3.2904 22.6808 2.95467Z" fill="#3B3731" />
                                    <path d="M14.1219 1.60312C14.4576 1.26738 15.0018 1.26738 15.3375 1.60312L16.2387 2.50425C16.5742 2.84001 16.5743 3.38427 16.2387 3.71994C15.903 4.05561 15.3587 4.05547 15.023 3.71994L14.1219 2.81881C13.7861 2.48307 13.7861 1.93886 14.1219 1.60312Z" fill="#3B3731" />
                                    <path d="M10.969 4.75632C11.3047 4.42058 11.8489 4.42058 12.1847 4.75632L13.0858 5.65745C13.4213 5.99321 13.4215 6.53747 13.0858 6.87314C12.7501 7.2088 12.2059 7.20866 11.8701 6.87314L10.969 5.97201C10.6332 5.63627 10.6332 5.09206 10.969 4.75632Z" fill="#3B3731" />
                                    <path d="M8.26578 8.36045C8.60152 8.02471 9.14573 8.02471 9.48147 8.36045L10.3826 9.26158C10.7181 9.59733 10.7183 10.1416 10.3826 10.4773C10.0469 10.8129 9.50267 10.8128 9.16691 10.4773L8.26578 9.57613C7.93004 9.2404 7.93004 8.69618 8.26578 8.36045Z" fill="#3B3731" />
                                    <path d="M7.36502 12.8646C7.70076 12.5289 8.24497 12.5289 8.58071 12.8646L9.48184 13.7657C9.81736 14.1015 9.81751 14.6457 9.48184 14.9814C9.14618 15.3171 8.60191 15.3169 8.26615 14.9814L7.36502 14.0803C7.02929 13.7445 7.02929 13.2003 7.36502 12.8646Z" fill="#3B3731" />
                                    <path d="M19.0771 0.251804C19.4128 -0.0839344 19.957 -0.0839347 20.2927 0.251804L21.1939 1.15293C21.5294 1.48869 21.5295 2.03295 21.1939 2.36862C20.8582 2.70429 20.3139 2.70415 19.9782 2.36862L19.0771 1.46749C18.7413 1.13175 18.7413 0.587542 19.0771 0.251804Z" fill="#3B3731" />
                                    <path d="M18.1763 5.65744C18.512 5.3217 19.0562 5.32171 19.392 5.65744L21.1942 7.4597C21.5296 7.79546 21.5298 8.33978 21.1942 8.67539C20.8586 9.01095 20.3143 9.01072 19.9786 8.67539L18.1763 6.87312C17.8406 6.53739 17.8406 5.99318 18.1763 5.65744Z" fill="#3B3731" />
                                    <path d="M15.0233 8.81076C15.359 8.47503 15.9032 8.47503 16.239 8.81076L18.0412 10.613C18.3765 10.9488 18.3768 11.4931 18.0412 11.8287C17.7056 12.1643 17.1613 12.164 16.8255 11.8287L15.0233 10.0265C14.6875 9.69071 14.6876 9.1465 15.0233 8.81076Z" fill="#3B3731" />
                                    <path d="M11.4193 11.5135C11.755 11.1778 12.2992 11.1778 12.635 11.5135L14.4372 13.3158C14.7726 13.6515 14.7728 14.1959 14.4372 14.5315C14.1016 14.8671 13.5573 14.8668 13.2215 14.5315L11.4193 12.7292C11.0835 12.3935 11.0835 11.8493 11.4193 11.5135Z" fill="#3B3731" />
                                    <path d="M8.26615 20.0722C8.67655 19.6616 8.6759 18.9955 8.26536 18.5849C7.8548 18.1746 7.18939 18.1746 6.77883 18.5849L3.1749 22.1888C2.76437 22.5994 2.76451 23.2647 3.1749 23.6754C3.58551 24.086 4.25161 24.0868 4.66223 23.6761L8.26615 20.0722ZM5.87805 24.892C4.79595 25.974 3.04117 25.9733 1.95908 24.8912C0.877208 23.8091 0.877068 22.055 1.95908 20.973L5.56301 17.3691C6.64505 16.2873 8.39914 16.2873 9.48118 17.3691C10.5632 18.4511 10.5638 20.2059 9.48197 21.2881L5.87805 24.892Z" fill="#3B3731" />
                                </svg>
                                &nbsp;
                                &nbsp;
                                <p>Full Groom</p>
                            </div>
                            <div class="service-list-right">
                                <div class="price">
                                    <p>From <span>£38</span></p>
                                </div>
                                <div class="reach-time">
                                    <p>60 - 90 min</p>
                                </div>
                            </div>
                        </div>
                        <div class="services-list d-flex align-items-center justify-content-between mt-4">
                            <div class="service-list-left d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="23" viewBox="0 0 25 23" fill="none">
                                    <path d="M22.3518 11.4419L22.3403 11.3626C22.321 11.2858 22.281 11.2148 22.2243 11.1581C22.1485 11.0824 22.0474 11.0376 21.9405 11.0306V11.0295H3.05839C2.95189 11.0366 2.85125 11.0826 2.77576 11.1581C2.70002 11.2339 2.65518 11.335 2.64823 11.4419C2.64773 11.4516 2.64708 11.4729 2.64708 11.5683C2.64708 11.8041 2.64734 11.9735 2.64938 12.1129L2.66317 12.4771V12.4805C2.88515 15.9886 5.89367 19.1126 9.39225 19.467H9.39111C9.62526 19.4904 9.75727 19.4954 10.0598 19.5072H10.0586C10.9158 19.5387 11.7456 19.5577 12.4977 19.5577C13.3116 19.5556 14.1258 19.5392 14.9391 19.5072H14.9403C15.2427 19.4954 15.3739 19.4902 15.6078 19.4658H15.6101C19.107 19.1124 22.1149 15.9887 22.338 12.4805L22.3507 12.114C22.3526 11.9739 22.353 11.8041 22.353 11.5683C22.353 11.4719 22.3523 11.4515 22.3518 11.4419ZM23.8213 12.1347L23.8052 12.5736C23.5365 16.799 19.9714 20.5023 15.7583 20.9284L15.7594 20.9295C15.474 20.9592 15.3012 20.9648 14.9977 20.9766C14.1665 21.0094 13.3342 21.0273 12.5023 21.0295H12.5C11.7234 21.0295 10.8736 21.0086 10.0046 20.9766H10.0035C9.85153 20.9707 9.7325 20.9672 9.61629 20.9605L9.24405 20.9306C5.03085 20.5039 1.46519 16.8016 1.19602 12.577L1.17879 12.1347C1.17642 11.9762 1.17649 11.7969 1.17649 11.5683C1.17649 11.4894 1.17583 11.4151 1.17994 11.3488V11.3465C1.21006 10.8834 1.4078 10.4466 1.73601 10.1184C2.06421 9.79018 2.50101 9.59243 2.96418 9.56231H2.96762C3.03409 9.5583 3.10466 9.55887 3.18591 9.55887H21.8141C21.8941 9.55887 21.9659 9.5583 22.0324 9.56231H22.0359C22.499 9.59243 22.9358 9.79018 23.264 10.1184C23.5512 10.4056 23.7381 10.7759 23.8006 11.1742L23.8201 11.3465V11.3488C23.8242 11.4151 23.8236 11.4881 23.8236 11.5683C23.8236 11.7975 23.8235 11.9767 23.8213 12.1347Z" fill="#3B3731" />
                                    <path d="M4.78401 19.3773C4.96562 19.0141 5.40654 18.8671 5.76976 19.0487C6.13298 19.2303 6.27995 19.6713 6.09835 20.0345L4.92188 22.3874C4.74026 22.7506 4.29934 22.8976 3.93612 22.716C3.57291 22.5344 3.42594 22.0935 3.60754 21.7303L4.78401 19.3773ZM19.2302 19.0487C19.5935 18.8671 20.0344 19.0141 20.216 19.3773L21.3925 21.7303C21.5741 22.0935 21.4271 22.5344 21.0639 22.716C20.7007 22.8976 20.2597 22.7506 20.0781 22.3874L18.9017 20.0345C18.72 19.6713 18.867 19.2303 19.2302 19.0487ZM24.2647 9.55884C24.6708 9.55884 25 9.88804 25 10.2941C25 10.7002 24.6708 11.0294 24.2647 11.0294H0.735294C0.329202 11.0294 0 10.7002 0 10.2941C0 9.88804 0.329202 9.55884 0.735294 9.55884H24.2647Z" fill="#3B3731" />
                                    <path d="M1.91174 9.95294C1.91174 10.1214 1.98056 10.283 2.10306 10.4022C2.22557 10.5213 2.39171 10.5882 2.56496 10.5882C2.7382 10.5882 2.90435 10.5213 3.02685 10.4022C3.14935 10.283 3.21817 10.1214 3.21817 9.95294H1.91174ZM5.47916 6.34616L4.87124 6.57911C4.90339 6.65817 4.95154 6.73015 5.01283 6.79079C5.07411 6.85142 5.14729 6.89947 5.22802 6.9321C5.30875 6.96472 5.3954 6.98125 5.48282 6.98071C5.57024 6.98017 5.65666 6.96256 5.73696 6.92894L5.47916 6.34616ZM10.67 4.17176L10.9287 4.75624C11.0846 4.69093 11.2082 4.56918 11.2734 4.41676C11.3386 4.26435 11.3402 4.09324 11.278 3.93967L10.67 4.17176ZM3.21817 9.95294V2.65553H1.91174V9.95294H3.21817ZM4.64218 1.27059C5.22484 1.27059 5.74828 1.61534 5.96428 2.14136L7.17751 1.66871C6.97493 1.17599 6.62521 0.754448 6.17347 0.456865C5.72172 0.159282 5.18866 0.000152063 4.64305 0L4.64218 1.27059ZM3.21817 2.65553C3.21817 1.89064 3.85571 1.27059 4.64218 1.27059L4.64305 0C3.91889 0 3.22352 0.279778 2.71147 0.777787C2.19941 1.27579 1.91174 1.95124 1.91174 2.65553H3.21817ZM5.96428 2.14136L6.29524 2.94438L7.5076 2.47256L7.17751 1.66871L5.96428 2.14136ZM6.08621 6.11322C5.86721 5.57014 5.87158 4.96591 6.09841 4.42588L4.88778 3.94729C4.5345 4.7898 4.52913 5.73225 4.87124 6.57911L6.08621 6.11322ZM10.4122 3.58899L5.22049 5.76254L5.73696 6.92894L10.9278 4.75539L10.4122 3.58899ZM8.90722 3.23407C9.44895 3.45939 9.85221 3.88546 10.063 4.40471L11.278 3.93967C11.112 3.52304 10.8622 3.14192 10.5434 2.82021C10.2246 2.4985 9.84306 2.24181 9.42108 2.06513L8.90722 3.23407ZM6.09841 4.42588C6.30305 3.92583 6.6909 3.51809 7.18709 3.27981L6.61575 2.13798C5.82981 2.51289 5.21431 3.15737 4.88778 3.94729L6.09841 4.42588ZM7.18709 3.27981C7.45345 3.15188 7.74514 3.08162 8.0421 3.07387C8.33905 3.06612 8.6342 3.12022 8.90722 3.23407L9.42108 2.06513C8.97575 1.87988 8.49451 1.79125 8.01032 1.80368C7.52613 1.8161 7.05042 1.93014 6.61575 2.13798L7.18709 3.27981Z" fill="#3B3731" />
                                </svg>
                                &nbsp;
                                &nbsp;
                                <p>Bath & Tidy</p>
                            </div>
                            <div class="service-list-right">
                                <div class="price">
                                    <p>From <span>£30</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="services-list d-flex align-items-center justify-content-between mt-4">
                            <div class="service-list-left d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="24" viewBox="0 0 25 24" fill="none">
                                    <path d="M11.1621 9.58842C12.0295 9.29401 12.9692 9.29407 13.8366 9.58842C14.7981 9.91503 15.4985 10.7259 16.1473 11.7619C16.7984 12.8039 17.508 14.2554 18.408 16.1028L19.4627 18.264C19.6949 18.7376 19.8783 19.1155 20.0107 19.4161C20.1421 19.7123 20.2547 20.0023 20.3013 20.2833C20.622 22.1716 19.2354 23.9438 17.3216 24C17.0194 23.999 16.7187 23.9581 16.4268 23.8791C16.0203 23.7795 15.615 23.6726 15.2123 23.5584L15.194 23.5535C14.825 23.4443 14.4524 23.3479 14.0771 23.2636C13.0366 23.0504 11.9633 23.0504 10.9228 23.2636C10.5475 23.3479 10.1749 23.4444 9.80593 23.5535L9.78763 23.5584C9.28881 23.6954 8.88938 23.8065 8.57428 23.8791C8.28205 23.9583 7.98092 23.9991 7.67831 24L7.50009 23.9901C5.67915 23.8333 4.38683 22.1126 4.69743 20.2833C4.74394 20.0023 4.85773 19.7123 4.98795 19.4161C5.05274 19.2691 5.13078 19.1039 5.21988 18.9178L5.52139 18.2961L5.53725 18.264L6.59191 16.1028L6.61266 16.0584C7.39217 14.4603 8.02741 13.1607 8.60724 12.169L8.85259 11.7619C9.46077 10.7908 10.1128 10.0171 10.9851 9.65503L11.1621 9.58842ZM13.4386 10.785C12.8296 10.5783 12.1691 10.5795 11.56 10.7862C11.0931 10.9448 10.6503 11.3367 10.1343 12.0913L9.90847 12.4379C9.30552 13.4 8.63783 14.7651 7.73446 16.6172L7.71249 16.6604L6.65783 18.8216L6.64196 18.8549C6.41377 19.3224 6.24766 19.6646 6.1305 19.9305C6.00423 20.2178 5.94861 20.3812 5.93031 20.4918V20.4967C5.73505 21.6457 6.57593 22.6949 7.7015 22.7356C7.88704 22.7328 8.07162 22.7077 8.2508 22.6591L8.274 22.653L8.29597 22.648C8.57901 22.5828 8.94534 22.4808 9.45926 22.3396L9.47269 22.3359L9.4849 22.3335C9.87065 22.2201 10.2597 22.1181 10.6519 22.03L10.6628 22.0276L10.6751 22.0251C11.8793 21.7784 13.1206 21.7784 14.3249 22.0251L14.3358 22.0276L14.348 22.03C14.7337 22.1166 15.116 22.2175 15.4955 22.3285L15.515 22.3335L15.5321 22.3372L15.5504 22.3421C15.7445 22.3972 15.9389 22.4509 16.1339 22.5025L16.721 22.6517L16.7503 22.6591C16.9284 22.7073 17.1116 22.7326 17.296 22.7356C18.4226 22.6961 19.265 21.6464 19.0696 20.4967L19.0684 20.4918C19.0499 20.38 18.9953 20.2141 18.8706 19.933L18.8694 19.9305C18.7493 19.6578 18.5771 19.3031 18.3421 18.824L18.3409 18.8216L17.2862 16.6604C16.3748 14.7894 15.6978 13.409 15.0902 12.4367C14.4746 11.4539 13.9718 10.9661 13.4386 10.785ZM1.66895 8.9112C2.50374 8.61394 3.35196 8.85555 4.00653 9.31211C4.66554 9.77219 5.20769 10.4919 5.52505 11.3462C5.84241 12.2007 5.90025 13.0978 5.69228 13.875C5.48533 14.65 4.98391 15.3774 4.14569 15.676L3.98945 15.7253C3.20584 15.9416 2.42196 15.7032 1.80811 15.2751C1.23129 14.8724 0.744281 14.271 0.418987 13.5543L0.289596 13.241C0.0129539 12.4934 -0.0671517 11.7134 0.0564481 11.0095L0.122364 10.7122C0.316349 9.98577 0.768893 9.30167 1.51515 8.97288L1.66895 8.9112ZM20.9934 9.31211C21.648 8.85669 22.4961 8.61377 23.331 8.90997C24.1692 9.20852 24.6706 9.93718 24.8775 10.7122C25.0856 11.4895 25.0277 12.3864 24.7103 13.241C24.393 14.0965 23.8508 14.815 23.1918 15.2751L22.9367 15.4355C22.3245 15.7828 21.5846 15.9361 20.8542 15.676C20.0159 15.3775 19.5146 14.65 19.3076 13.875C19.1008 13.0977 19.1586 12.2007 19.4749 11.3462C19.7923 10.4907 20.3343 9.77216 20.9934 9.31211ZM22.9147 10.1003C22.5641 9.97694 22.1334 10.0524 21.7026 10.352C21.2749 10.6509 20.8826 11.1511 20.6455 11.7903C20.409 12.4299 20.3842 13.0557 20.5149 13.5469L20.571 13.7233C20.7169 14.1169 20.9659 14.3762 21.2692 14.4844C21.6196 14.6092 22.0507 14.5357 22.4826 14.2352C22.9101 13.9363 23.3027 13.4359 23.5397 12.7969C23.7475 12.2373 23.7919 11.6885 23.7118 11.2315L23.6703 11.0415C23.5382 10.547 23.2609 10.2249 22.9147 10.1016V10.1003ZM3.2961 10.352C2.86607 10.052 2.43527 9.97768 2.08398 10.1028C1.73716 10.2264 1.46139 10.5481 1.32961 11.0415H1.32839C1.19745 11.5306 1.22337 12.156 1.46022 12.7969L1.55665 13.03C1.79694 13.5585 2.1443 13.9753 2.51854 14.2364L2.67967 14.3376C3.05386 14.5501 3.42192 14.5937 3.73066 14.4844L3.85639 14.4289C4.14346 14.278 4.36964 13.9786 4.48504 13.5469V13.5457C4.61634 13.0552 4.5919 12.4297 4.35443 11.7903C4.11686 11.1508 3.72375 10.6492 3.2961 10.3508V10.352ZM6.74328 0.0900743C7.73261 -0.193113 8.67304 0.225792 9.35551 0.867212C10.045 1.51461 10.5776 2.46891 10.8508 3.55882C11.1252 4.64995 11.1062 5.75179 10.8179 6.66614L10.697 6.9992C10.3778 7.76706 9.81065 8.44789 8.95513 8.69286L8.76959 8.73727C7.84848 8.92217 6.98153 8.5159 6.34168 7.91449C5.65224 7.26714 5.11962 6.31393 4.84636 5.22412C4.60629 4.26954 4.59122 3.30659 4.7841 2.46836L4.87931 2.1168C5.14635 1.27397 5.69121 0.476331 6.56384 0.149285L6.74328 0.0900743ZM15.6444 0.868445C16.3269 0.227062 17.2673 -0.193197 18.2566 0.0900743C19.2344 0.369915 19.8357 1.21771 20.1206 2.1168C20.41 3.02992 20.4279 4.1331 20.1536 5.22412C19.8803 6.31392 19.3476 7.26713 18.6582 7.91449C18.0184 8.51588 17.1513 8.92202 16.2303 8.73727L16.0448 8.69286C15.1891 8.44696 14.6221 7.76687 14.3029 6.9992L14.1808 6.66614C13.9285 5.86616 13.8825 4.9226 14.0587 3.96836L14.1491 3.55882C14.3882 2.60517 14.8254 1.75545 15.3929 1.12502L15.6444 0.868445ZM8.5047 1.79238C8.01407 1.33119 7.50852 1.18357 7.08384 1.30512C6.65672 1.42734 6.28174 1.83092 6.06947 2.4992C5.88705 3.0776 5.84295 3.80848 5.98524 4.5802L6.05848 4.91203V4.91326C6.2826 5.80728 6.70742 6.53244 7.19248 6.98809L7.1937 6.98933C7.68521 7.45118 8.19088 7.59913 8.61456 7.47781C9.04161 7.35547 9.41539 6.94931 9.62772 6.28004C9.80951 5.70206 9.85399 4.97335 9.71194 4.20273L9.63992 3.86968C9.4437 3.08693 9.09332 2.43243 8.68414 1.97617L8.5047 1.79238ZM17.9161 1.30512C17.4923 1.18378 16.9868 1.3315 16.4952 1.79361L16.494 1.79484C16.0704 2.19182 15.6923 2.79696 15.454 3.54155L15.36 3.86968C15.1357 4.76141 15.1633 5.61948 15.371 6.28004C15.5829 6.94822 15.9576 7.35447 16.3866 7.47781H16.3853C16.8091 7.59915 17.3158 7.4502 17.8074 6.98809C18.2924 6.53243 18.7173 5.80717 18.9414 4.91326V4.91203C19.1662 4.01806 19.1389 3.15912 18.9304 2.50167C18.7183 1.83206 18.3437 1.42748 17.9161 1.30512Z" fill="#3B3731" />
                                </svg>
                                &nbsp;
                                &nbsp;
                                <p>Nail Trim</p>
                            </div>
                            <div class="service-list-right">
                                <div class="price">
                                    <p>From <span>£10</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="services-list d-flex align-items-center justify-content-between mt-4">
                            <div class="service-list-left d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="20" viewBox="0 0 25 20" fill="none">
                                    <path d="M7.14286 10V5.88235H4.16667C3.62029 5.88235 3.18073 5.88222 2.82389 5.85823C2.46174 5.83384 2.14114 5.78236 1.83803 5.65832C1.47669 5.51051 1.14846 5.29396 0.871931 5.02068C0.595401 4.7474 0.376275 4.42303 0.226702 4.06595C0.101183 3.7664 0.0490882 3.44958 0.0244141 3.09168C0.000130046 2.73905 0 2.30466 0 1.76471V0.588235C0 0.263362 0.266497 0 0.595238 0C0.923979 0 1.19048 0.263362 1.19048 0.588235V1.76471C1.19048 2.32098 1.19048 2.70895 1.2114 3.01241C1.23195 3.31031 1.27118 3.48346 1.3265 3.61558V3.61673C1.41624 3.83076 1.54786 4.02506 1.71363 4.18888C1.87941 4.3527 2.07602 4.48278 2.2926 4.57146H2.29376C2.42745 4.62613 2.60266 4.6649 2.90411 4.6852C3.08925 4.69767 3.30559 4.70153 3.57143 4.70358V3.52941C3.57143 2.90537 3.82246 2.30707 4.26897 1.86581C4.71549 1.42455 5.32091 1.17647 5.95238 1.17647H16.0714C17.177 1.17647 18.0657 1.17576 18.7628 1.26838C19.4771 1.36335 20.0789 1.56601 20.5566 2.03814C21.0344 2.51027 21.2395 3.10493 21.3356 3.81089C21.3721 4.07919 21.3908 4.37633 21.4042 4.70358C21.6812 4.70165 21.9051 4.69805 22.0959 4.6852C22.3973 4.6649 22.5725 4.62613 22.7062 4.57146H22.7074C22.924 4.48278 23.1206 4.3527 23.2864 4.18888C23.4521 4.02506 23.5838 3.83076 23.6735 3.61673V3.61558C23.7288 3.48346 23.768 3.31031 23.7886 3.01241C23.8095 2.70895 23.8095 2.32098 23.8095 1.76471V0.588235C23.8095 0.263362 24.076 0 24.4048 0C24.7335 0 25 0.263362 25 0.588235V1.76471C25 2.30466 24.9999 2.73905 24.9756 3.09168C24.9509 3.44958 24.8988 3.7664 24.7733 4.06595C24.6237 4.42303 24.4046 4.7474 24.1281 5.02068C23.8515 5.29396 23.5233 5.51051 23.162 5.65832C22.8589 5.78236 22.5383 5.83384 22.1761 5.85823C21.959 5.87283 21.7112 5.87791 21.4274 5.88006C21.4279 6.06952 21.4286 6.26631 21.4286 6.47059V10C21.4286 11.0925 21.4293 11.9708 21.3356 12.6597C21.2395 13.3657 21.0344 13.9603 20.5566 14.4324C20.1434 14.8408 19.6377 15.0474 19.0465 15.1574C19.0451 16.0435 19.0351 16.7743 18.9546 17.3656C18.8585 18.0715 18.6534 18.6662 18.1757 19.1383C17.6979 19.6105 17.0962 19.8131 16.3818 19.9081C15.6848 20.0007 14.796 20 13.6905 20H8.92857C7.82302 20 6.93427 20.0007 6.23721 19.9081C5.52285 19.8131 4.92111 19.6105 4.44336 19.1383C3.96561 18.6662 3.76054 18.0715 3.66443 17.3656C3.57071 16.6767 3.57143 15.7984 3.57143 14.7059V8.82353C3.57143 8.49866 3.83793 8.23529 4.16667 8.23529C4.49541 8.23529 4.7619 8.49866 4.7619 8.82353V14.7059C4.7619 15.8319 4.76266 16.6171 4.84329 17.2093C4.92159 17.7845 5.0652 18.0893 5.28506 18.3065C5.50493 18.5238 5.81328 18.6657 6.39532 18.7431C6.99462 18.8228 7.78911 18.8235 8.92857 18.8235H13.6905C14.8299 18.8235 15.6244 18.8228 16.2237 18.7431C16.8058 18.6657 17.1141 18.5238 17.334 18.3065C17.5538 18.0893 17.6975 17.7845 17.7758 17.2093C17.8422 16.7214 17.8501 16.1025 17.8525 15.27C17.3351 15.2904 16.7434 15.2941 16.0714 15.2941H12.5C11.3945 15.2941 10.5057 15.2948 9.80864 15.2022C9.09428 15.1072 8.49254 14.9046 8.01479 14.4324C7.53704 13.9603 7.33196 13.3657 7.23586 12.6597C7.14214 11.9708 7.14286 11.0925 7.14286 10ZM4.7619 4.70588H7.14286V3.52941C7.14286 3.21739 7.01734 2.91824 6.79408 2.69761C6.57083 2.47698 6.26812 2.35294 5.95238 2.35294C5.63665 2.35294 5.33393 2.47698 5.11068 2.69761C4.88742 2.91824 4.7619 3.21739 4.7619 3.52941V4.70588ZM8.33333 10C8.33333 11.1261 8.33409 11.9112 8.41471 12.5034C8.49302 13.0786 8.63663 13.3834 8.85649 13.6006C9.07635 13.8179 9.38471 13.9598 9.96675 14.0372C10.5661 14.1169 11.3605 14.1176 12.5 14.1176H16.0714C17.2109 14.1176 18.0054 14.1169 18.6047 14.0372C19.1867 13.9598 19.4951 13.8179 19.7149 13.6006C19.9348 13.3834 20.0784 13.0786 20.1567 12.5034C20.2373 11.9112 20.2381 11.1261 20.2381 10V6.47059C20.2381 5.34453 20.2373 4.55939 20.1567 3.96714C20.0784 3.39195 19.9348 3.08722 19.7149 2.86995C19.4951 2.65267 19.1867 2.51075 18.6047 2.43336C18.0054 2.35369 17.2109 2.35294 16.0714 2.35294H8.0113C8.21897 2.7079 8.33333 3.11229 8.33333 3.52941V10Z" fill="#3B3731" />
                                </svg>
                                &nbsp;
                                &nbsp;
                                <p>Pet Spa</p>
                            </div>
                            <div class="service-list-right">
                                <div class="price">
                                    <p>From <span>£50</span></p>
                                </div>
                            </div>
                        </div>
                        <div class="services-list d-flex align-items-center justify-content-between mt-4">
                            <div class="service-list-left d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="16" viewBox="0 0 25 16" fill="none">
                                    <path d="M24.8464 5.66628L20.0212 0.487716C19.8818 0.334782 19.7077 0.211853 19.5111 0.127587C19.3145 0.0433216 19.1003 -0.00022993 18.8835 9.12899e-07H1.48305C1.08972 9.12899e-07 0.712502 0.143877 0.434376 0.399977C0.15625 0.656077 0 1.00342 0 1.3656V12.2904C0 12.6526 0.15625 13 0.434376 13.2561C0.712502 13.5122 1.08972 13.656 1.48305 13.656H3.45339C3.59927 14.3175 3.98902 14.9123 4.55661 15.3395C5.12421 15.7666 5.83473 16 6.5678 16C7.30086 16 8.01138 15.7666 8.57898 15.3395C9.14657 14.9123 9.53632 14.3175 9.6822 13.656H15.3178C15.4637 14.3175 15.8534 14.9123 16.421 15.3395C16.9886 15.7666 17.6991 16 18.4322 16C19.1653 16 19.8758 15.7666 20.4434 15.3395C21.011 14.9123 21.4007 14.3175 21.5466 13.656H23.5169C23.9103 13.656 24.2875 13.5122 24.5656 13.2561C24.8437 13 25 12.6526 25 12.2904V6.04767C25 5.90772 24.9455 5.77241 24.8464 5.66628ZM19.0519 1.24563L22.9809 5.46241H16.5254V1.17052H18.8835C18.9159 1.17033 18.948 1.177 18.9772 1.19002C19.0064 1.20304 19.032 1.22207 19.0519 1.24563ZM8.8983 5.46241V1.17052H15.2542V5.46241H8.8983ZM1.48305 1.17052H7.62712V5.46241H1.27119V1.3656C1.27119 1.31386 1.29351 1.26424 1.33324 1.22766C1.37297 1.19107 1.42686 1.17052 1.48305 1.17052ZM6.5678 14.8266C6.19067 14.8266 5.82201 14.7236 5.50845 14.5306C5.19488 14.3377 4.95048 14.0635 4.80616 13.7427C4.66184 13.4219 4.62408 13.0688 4.69765 12.7282C4.77123 12.3877 4.95283 12.0748 5.2195 11.8293C5.48617 11.5837 5.82592 11.4165 6.1958 11.3487C6.56568 11.281 6.94907 11.3158 7.29749 11.4486C7.64591 11.5815 7.94371 11.8066 8.15323 12.0953C8.36275 12.3841 8.47458 12.7235 8.47458 13.0708C8.47458 13.5364 8.27368 13.983 7.91609 14.3123C7.5585 14.6416 7.07351 14.8266 6.5678 14.8266ZM18.4322 14.8266C18.0551 14.8266 17.6864 14.7236 17.3729 14.5306C17.0593 14.3377 16.8149 14.0635 16.6706 13.7427C16.5262 13.4219 16.4885 13.0688 16.5621 12.7282C16.6356 12.3877 16.8172 12.0748 17.0839 11.8293C17.3506 11.5837 17.6903 11.4165 18.0602 11.3487C18.4301 11.281 18.8135 11.3158 19.1619 11.4486C19.5103 11.5815 19.8081 11.8066 20.0176 12.0953C20.2271 12.3841 20.339 12.7235 20.339 13.0708C20.339 13.5364 20.1381 13.983 19.7805 14.3123C19.4229 14.6416 18.9379 14.8266 18.4322 14.8266ZM23.5169 12.4855H21.5466C21.4007 11.824 21.011 11.2293 20.4434 10.8021C19.8758 10.3749 19.1653 10.1415 18.4322 10.1415C17.6991 10.1415 16.9886 10.3749 16.421 10.8021C15.8534 11.2293 15.4637 11.824 15.3178 12.4855H9.6822C9.53632 11.824 9.14657 11.2293 8.57898 10.8021C8.01138 10.3749 7.30086 10.1415 6.5678 10.1415C5.83473 10.1415 5.12421 10.3749 4.55661 10.8021C3.98902 11.2293 3.59927 11.824 3.45339 12.4855H1.48305C1.42686 12.4855 1.37297 12.465 1.33324 12.4284C1.29351 12.3918 1.27119 12.3422 1.27119 12.2904V6.63293H23.7288V12.2904C23.7288 12.3422 23.7065 12.3918 23.6668 12.4284C23.627 12.465 23.5731 12.4855 23.5169 12.4855Z" fill="#3B3731" />
                                </svg>
                                &nbsp;
                                &nbsp;
                                <p>Mobile Grooming</p>
                            </div>
                            <div class="service-list-right">
                                <div class="price">
                                    <p>From <span>£10</span></p>
                                </div>
                            </div>
                        </div>

                        <h2 class="section-content-heading mt-5">Add-ons</h2>

                        <div class="addons-list mt-4">
                            <div class="addons-list__row">
                                <span class="addons-list__name">Flea &amp; Tick Treatment</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Hypoallergenic Shampoo</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Deep Conditioning Mask</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Shed-Control Shampoo</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Tear-Stain Treatment</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Deodorising Treatment</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Nail Grinding</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Coat Shine Spray</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Anti-itch Treatment</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Breath Freshener Gel</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Paw Fur Shaping</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Premium Fragrance Upgrade</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Fast-Dry Service</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Coat Colour Enhancing Shampoo</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                            <div class="addons-list__row">
                                <span class="addons-list__name">Soft-Claws / Nail Caps Application</span>
                                <span class="addons-list__price">+ £2</span>
                            </div>
                        </div>

                    </div>

                    <div id="before_you_book" class="mt-5">
                        <h2 class="section-content-heading mt-5">Before you book</h2>
                        <h2 class="section-sub-content-heading mt-3">Rules & Restrictions</h2>

                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M10 0C4.47719 0 0 4.47719 0 10C0 15.5231 4.47719 20 10 20C15.5231 20 20 15.5231 20 10C20 4.47719 15.5231 0 10 0ZM10 18.7697C5.17531 18.7697 1.25 14.8247 1.25 9.99996C1.25 5.17527 5.17531 1.24996 10 1.24996C14.8247 1.24996 18.75 5.17529 18.75 9.99996C18.75 14.8246 14.8247 18.7697 10 18.7697Z" fill="#3B3731" />
                                <path d="M5.5 10.5L8.5 13.5L14.5 7.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>Works with dogs & cats up to 35kg</p>
                        </div>
                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M10 0C4.47719 0 0 4.47719 0 10C0 15.5231 4.47719 20 10 20C15.5231 20 20 15.5231 20 10C20 4.47719 15.5231 0 10 0ZM10 18.7697C5.17531 18.7697 1.25 14.8247 1.25 9.99996C1.25 5.17527 5.17531 1.24996 10 1.24996C14.8247 1.24996 18.75 5.17529 18.75 9.99996C18.75 14.8246 14.8247 18.7697 10 18.7697Z" fill="#3B3731" />
                                <path d="M5.5 10.5L8.5 13.5L14.5 7.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>Special care for seniors & anxious pets</p>
                        </div>
                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M6.11099 13.8891C6.01726 13.7953 5.9646 13.6681 5.9646 13.5356C5.9646 13.403 6.01726 13.2758 6.11099 13.1821L9.29299 10.0001L6.11099 6.81806C6.01991 6.72376 5.96952 6.59746 5.97066 6.46636C5.9718 6.33526 6.02438 6.20985 6.11708 6.11715C6.20979 6.02445 6.33519 5.97186 6.46629 5.97072C6.59739 5.96958 6.72369 6.01998 6.81799 6.11106L9.99999 9.29306L13.182 6.11106C13.2763 6.01998 13.4026 5.96958 13.5337 5.97072C13.6648 5.97186 13.7902 6.02445 13.8829 6.11715C13.9756 6.20985 14.0282 6.33526 14.0293 6.46636C14.0305 6.59746 13.9801 6.72376 13.889 6.81806L10.707 10.0001L13.889 13.1821C13.9801 13.2764 14.0305 13.4027 14.0293 13.5338C14.0282 13.6649 13.9756 13.7903 13.8829 13.883C13.7902 13.9757 13.6648 14.0283 13.5337 14.0294C13.4026 14.0305 13.2763 13.9801 13.182 13.8891L9.99999 10.7071L6.81799 13.8891C6.72423 13.9828 6.59708 14.0355 6.46449 14.0355C6.33191 14.0355 6.20476 13.9828 6.11099 13.8891Z" fill="#3B3731" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 20C15.523 20 20 15.523 20 10C20 4.477 15.523 0 10 0C4.477 0 0 4.477 0 10C0 15.523 4.477 20 10 20ZM10 19C14.9705 19 19 14.9705 19 10C19 5.0295 14.9705 1 10 1C5.0295 1 1 5.0295 1 10C1 14.9705 5.0295 19 10 19Z" fill="#3B3731" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>Not suitable for aggressive pets</p>
                        </div>
                    </div>

                    <div id="reviews" class="mt-5">
                        <h2 class="section-content-heading">Reviews</h2>

                        <div class="reviews-summary">
                            <div class="reviews-summary-inner">
                                <span class="reviews-summary-score">4.9</span>
                                <div class="reviews-summary-meta">
                                    <div class="reviews-summary-stars" aria-hidden="true">
                                        <svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none">
                                            <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                        </svg>
                                        <svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none">
                                            <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                        </svg>
                                        <svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none">
                                            <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                        </svg>
                                        <svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none">
                                            <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                        </svg>
                                        <svg class="review-star review-star--muted" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none">
                                            <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#D9D4CC" />
                                        </svg>
                                    </div>
                                    <p class="reviews-summary-count">Based on 120 verified reviews.</p>
                                </div>
                            </div>
                        </div>

                        <div class="reviews-grid">
                            <article class="review-card">
                                <div class="review-card-header">
                                    <div class="avatar avatar--amber" aria-hidden="true">JR</div>
                                    <div class="review-user">
                                        <p class="review-name">Jane R.</p>
                                        <p class="review-date">1 days ago</p>
                                    </div>
                                </div>
                                <p class="review-text">Such a lovely experience! He came back looking fluffy and smelling amazing.</p>
                                <div class="review-stars"><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg></div>
                            </article>
                            <article class="review-card">
                                <div class="review-card-header">
                                    <div class="avatar avatar--coral" aria-hidden="true">ST</div>
                                    <div class="review-user">
                                        <p class="review-name">Sunny T.</p>
                                        <p class="review-date">2 week ago</p>
                                    </div>
                                </div>
                                <p class="review-text">Hands down the best groomer we've tried. The studio is spotless and ...</p>
                                <div class="review-stars"><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg></div>
                            </article>
                            <article class="review-card">
                                <div class="review-card-header">
                                    <div class="avatar avatar--blue" aria-hidden="true">RK</div>
                                    <div class="review-user">
                                        <p class="review-name">Raj K.</p>
                                        <p class="review-date">1 month ago</p>
                                    </div>
                                </div>
                                <p class="review-text">Clean, professional, and my pup looked like a show dog after.</p>
                                <div class="review-stars"><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg></div>
                            </article>
                            <article class="review-card">
                                <div class="review-card-header">
                                    <div class="avatar avatar--sage" aria-hidden="true">AM</div>
                                    <div class="review-user">
                                        <p class="review-name">Jane R.</p>
                                        <p class="review-date">1 days ago</p>
                                    </div>
                                </div>
                                <p class="review-text">Such a lovely experience! He came back looking fluffy and smelling amazing....</p>
                                <div class="review-stars"><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg></div>
                            </article>
                            <article class="review-card">
                                <div class="review-card-header">
                                    <div class="avatar avatar--lilac" aria-hidden="true">LB</div>
                                    <div class="review-user">
                                        <p class="review-name">Sunny T.</p>
                                        <p class="review-date">2 week ago</p>
                                    </div>
                                </div>
                                <p class="review-text">My dog is usually nervous but Sarah was so calming. Highly recommend!</p>
                                <div class="review-stars"><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg></div>
                            </article>
                            <article class="review-card">
                                <div class="review-card-header">
                                    <div class="avatar avatar--peach" aria-hidden="true">DW</div>
                                    <div class="review-user">
                                        <p class="review-name">Raj K.</p>
                                        <p class="review-date">1 month ago</p>
                                    </div>
                                </div>
                                <p class="review-text">Clean, professional, and my pup looked like a show dog after.</p>
                                <div class="review-stars"><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg><svg class="review-star" xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 17 17" fill="none" aria-hidden="true">
                                        <path d="M6.70397 1.06752C7.04681 -0.0193251 8.58365 -0.0193239 8.92649 1.06752L10.1396 4.91173C10.2927 5.39768 10.7425 5.72737 11.2509 5.72737H15.2521C16.373 5.72737 16.8477 7.15663 15.9495 7.82804L12.6982 10.2597C12.3011 10.5566 12.1351 11.0725 12.2844 11.5457L13.5283 15.4897C13.8694 16.5704 12.6261 17.454 11.7189 16.7758L8.51305 14.3785C8.09936 14.0691 7.53131 14.0691 7.11761 14.3785L3.91176 16.7758C3.00459 17.454 1.76125 16.5704 2.10239 15.4897L3.34625 11.5457C3.49556 11.0725 3.32963 10.5566 2.93251 10.2597L-0.31879 7.82804C-1.21696 7.15663 -0.742306 5.72737 0.378611 5.72737H4.37878C4.88719 5.72737 5.33703 5.39768 5.49012 4.91173L6.70397 1.06752Z" fill="#FFC97A" />
                                    </svg></div>
                            </article>
                        </div>

                        <div class="reviews-footer">
                            <div class="reviews-verified">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                                    <path d="M7 0C3.15 0 0 3.15 0 7C0 10.85 3.15 14 7 14C10.85 14 14 10.85 14 7C14 3.15 10.85 0 7 0ZM5.6 10.5L2.1 7L3.087 6.013L5.6 8.519L10.913 3.206L11.9 4.2L5.6 10.5Z" fill="#D8E8B7" />
                                </svg>
                                <p>All reviews are from verified Fursgo bookings.</p>
                            </div>
                            <a href="#" class="reviews-read-more">Read more reviews</a>
                        </div>
                    </div>

                    <div id="location" class="mt-5">
                        <h2 class="section-content-heading">Location</h2>

                        <div class="map-location-section">
                            <div class="location-badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="16" viewBox="0 0 11 16" fill="none" aria-hidden="true">
                                    <path d="M5.5 7.6C4.97904 7.6 4.47942 7.38929 4.11104 7.01421C3.74267 6.63914 3.53571 6.13043 3.53571 5.6C3.53571 5.06957 3.74267 4.56086 4.11104 4.18579C4.47942 3.81071 4.97904 3.6 5.5 3.6C6.02096 3.6 6.52058 3.81071 6.88896 4.18579C7.25733 4.56086 7.46429 5.06957 7.46429 5.6C7.46429 5.86264 7.41348 6.12272 7.31476 6.36537C7.21605 6.60802 7.07136 6.8285 6.88896 7.01421C6.70656 7.19993 6.49002 7.34725 6.2517 7.44776C6.01338 7.54827 5.75795 7.6 5.5 7.6ZM5.5 0C4.04131 0 2.64236 0.589998 1.61091 1.6402C0.579463 2.69041 0 4.11479 0 5.6C0 9.8 5.5 16 5.5 16C5.5 16 11 9.8 11 5.6C11 4.11479 10.4205 2.69041 9.38909 1.6402C8.35764 0.589998 6.95869 0 5.5 0Z" fill="#E5E5E5" />
                                </svg>
                                <p>Based in West London</p>
                            </div>

                            <div class="inverted-radius mt-5">
                                <div id="map" style="width:100%; height:400px;"></div>
                            </div>

                            <p class="location-privacy-note">
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                                    <path d="M12.1429 0.0380266C12.3762 -0.0461647 12.6373 0.0119765 12.8127 0.187338C12.9879 0.362723 13.0462 0.623823 12.962 0.857052L8.80375 12.3721C8.79773 12.3888 8.79051 12.4054 8.78313 12.4215C8.70404 12.5939 8.5773 12.7401 8.41766 12.8426C8.25795 12.9451 8.07205 12.9999 7.88227 13C7.69239 13 7.50604 12.9451 7.34624 12.8426C7.19251 12.7439 7.06968 12.6044 6.99015 12.4402L4.84606 8.15332L0.559137 6.00985C0.394864 5.93029 0.255538 5.80698 0.156808 5.65313C0.0543512 5.49339 0 5.30752 0 5.11774C0 4.92795 0.0543512 4.74209 0.156808 4.58234L0.197416 4.52361C0.296464 4.39121 0.427503 4.28544 0.578503 4.21624C0.594616 4.20886 0.611187 4.20227 0.627857 4.19625L12.1429 0.0380266Z" fill="#D8E8B7" />
                                </svg>
                                <span>Exact address shared after booking.</span>
                            </p>
                        </div>

                        <h2 class="section-content-heading mt-5">Accessbility</h2>
                        <p class="fs-16 mt-3">
                            Located near Victoria Embankment, with excellent public transport connections and free on-site parking available.
                        </p>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
<script>
        window.BASE_URL = @json(rtrim(asset(''), '/') . '/');
        window.CARTO_API_KEY = @json(config('services.carto.api_key'));
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/profile.js') }}"></script>
    <script src="{{ asset('js/common.js') }}"></script>
<script>
        document.addEventListener('DOMContentLoaded', function() {

            const map = L.map('map', {
                zoomControl: true,
                attributionControl: false,
                preferCanvas: true
            }).setView([51.510131, -0.146812], 15);

            enableCtrlScrollZoom(map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_nolabels/{z}/{x}/{y}{r}.png?key={{ urlencode(config('services.carto.api_key')) }}', {
                subdomains: 'abcd',
                maxZoom: 20,
                attribution: '&copy; <a href="https://carto.com/">CARTO</a> &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>'
            }).addTo(map);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_only_labels/{z}/{x}/{y}{r}.png?key={{ urlencode(config('services.carto.api_key')) }}', {
                subdomains: 'abcd',
                maxZoom: 20
            }).addTo(map);

            const yellowPin = L.icon({
                iconUrl: 'data:image/svg+xml;utf8,' + encodeURIComponent(`
            <svg xmlns="http://www.w3.org/2000/svg" width="34" height="48" viewBox="0 0 34 48" fill="none">
                <path d="M17 22.8C15.3898 22.8 13.8455 22.1679 12.7069 21.0426C11.5682 19.9174 10.9286 18.3913 10.9286 16.8C10.9286 15.2087 11.5682 13.6826 12.7069 12.5574C13.8455 11.4321 15.3898 10.8 17 10.8C18.6102 10.8 20.1545 11.4321 21.2931 12.5574C22.4318 13.6826 23.0714 15.2087 23.0714 16.8ZM17 0C12.4913 0 8.1673 1.76999 4.97918 4.92061C1.79107 8.07122 0 12.3444 0 16.8C0 29.4 17 48 17 48C17 48 34 29.4 34 16.8C34 12.3444 32.2089 8.07122 29.0208 4.92061C25.8327 1.76999 21.5087 0 17 0Z" fill="#FFC97A"/>
            </svg>
        `),
                iconSize: [28, 28],
                iconAnchor: [14, 28]
            });

            // Location object (keeps name available for the tooltip)
            const location = {
                loc_name: "Sarah's Grooming Studio",
                lat: 51.510131,
                lng: -0.146812
            };

            // Marker + hover tooltip showing the location name (no other changes)
            const marker = L.marker([location.lat, location.lng], {
                    icon: yellowPin
                })
                .addTo(map)
                .bindTooltip(location.loc_name, {
                    direction: 'top',
                    offset: [0, -24],
                    opacity: 0.95
                });

        });

        function enableCtrlScrollZoom(map) {
            map.scrollWheelZoom.disable();

            map.getContainer().addEventListener('wheel', function(e) {
                if (e.ctrlKey) {
                    map.scrollWheelZoom.enable();

                    clearTimeout(map._ctrlZoomTimeout);
                    map._ctrlZoomTimeout = setTimeout(() => {
                        map.scrollWheelZoom.disable();
                    }, 1000);
                } else {
                    map.scrollWheelZoom.disable();
                }
            });
        }

        // groomer-find-card

        const groomerFindCard = document.querySelectorAll('.groomer-find-card');

        groomerFindCard.forEach(card => {
            card.addEventListener('click', () => {
                // remove active from all cards
                groomerFindCard.forEach(c => c.classList.remove('active'));

                card.classList.toggle('active');
            });
        });

        const blockSvg = document.querySelectorAll('.block-svg');
        const blockBtnDiv = document.querySelector('.block-btn-div');
        const blockBtn = document.querySelector('.block-btn');
        const blockConfirmBtn = document.querySelector('#block_profile_modal .block-modal-confirm');

        blockSvg.forEach(svg => {
            svg.addEventListener('click', () => {
                blockBtnDiv.style.display = blockBtnDiv.style.display === 'block' ? 'none' : 'block';
            });
        });

        if (blockBtn) {
            blockBtn.addEventListener('click', () => {
                if (blockBtnDiv) blockBtnDiv.style.display = 'none';
            });
        }

        if (blockConfirmBtn) {
            blockConfirmBtn.addEventListener('click', function() {
                const homeUrl = this.dataset.homeUrl || '/';
                const referrer = document.referrer;
                if (referrer && referrer !== window.location.href) {
                    window.location.href = referrer;
                } else {
                    window.location.href = homeUrl;
                }
            });
        }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const scrollers = document.querySelectorAll('.space-scroll');

            scrollers.forEach(scroller => {
                const progress = scroller.parentElement.querySelector('.scroll-progress');
                const leftBtn = scroller.parentElement.querySelector('.scroll-btn.left');
                const rightBtn = scroller.parentElement.querySelector('.scroll-btn.right');
                const scrollAmount = 200;

                if (!progress || !leftBtn || !rightBtn) return;

                function updateProgress() {
                    const visible = scroller.clientWidth;
                    const total = scroller.scrollWidth;
                    const left = scroller.scrollLeft;

                    if (total === 0 || visible === 0) {
                        requestAnimationFrame(updateProgress);
                        return;
                    }

                    if (total <= visible) {
                        progress.style.left = '0%';
                        progress.style.width = '100%';
                        return;
                    }

                    let widthPercent = (visible / total) * 100;
                    let leftPercent = (left / total) * 100;

                    widthPercent = Math.max(15, widthPercent);
                    leftPercent = Math.min(100 - widthPercent, leftPercent);

                    progress.style.left = leftPercent + '%';
                    progress.style.width = widthPercent + '%';
                }

                scroller.addEventListener('scroll', updateProgress);
                window.addEventListener('resize', updateProgress);
                setTimeout(updateProgress, 100);

                leftBtn.addEventListener('click', () => {
                    scroller.scrollBy({
                        left: -scrollAmount,
                        behavior: 'smooth'
                    });
                });

                rightBtn.addEventListener('click', () => {
                    scroller.scrollBy({
                        left: scrollAmount,
                        behavior: 'smooth'
                    });
                });
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            const promptModal = document.getElementById('groomer_prompt');
            const bookSpaceModal = document.getElementById('groomer_book_space');

            const cards = promptModal.querySelectorAll('.groomer-find-card');
            const continueBtn = promptModal.querySelector('.modal-footer-btn.apply');
            const goBackBtn = document.getElementById('goBack');

            let selectedOption = null;

            // Card selection
            cards.forEach((card, index) => {
                card.addEventListener('click', function() {

                    // Remove active from all
                    cards.forEach(c => c.classList.remove('active'));

                    // Add active to selected
                    this.classList.add('active');

                    // 0 = home, 1 = find space
                    selectedOption = index;
                });
            });

            // Continue button
            continueBtn.addEventListener('click', function() {

                // If "Find a grooming space for me"
                if (selectedOption === 1) {
                    promptModal.style.display = 'none';
                    bookSpaceModal.style.display = 'flex';
                    if (typeof window.syncBodyScrollLock === 'function') {
                        window.syncBodyScrollLock();
                    }
                }
                // If "Home"
                else {
                    window.location.href = @json(url('/checkout_booking_groomer'));
                }
            });

            // Go Back button → return to previous modal
            goBackBtn.addEventListener('click', function() {
                bookSpaceModal.style.display = 'none';
                promptModal.style.display = 'flex';
                if (typeof window.syncBodyScrollLock === 'function') {
                    window.syncBodyScrollLock();
                }
            });

        });

        // loop through all venu-sorting-section blocks
        document.querySelectorAll('.venu-sorting-section').forEach(container => {
            const sortBy = container.querySelector('.sort-by');
            const sortByFilter = container.querySelector('.sort-by-filter');

            const venueSelection = container.querySelector('.venue-selection');
            const venueList = container.querySelector('.venue-list');

            sortBy.addEventListener('click', (e) => {
                e.stopPropagation();
                if (e.target.closest('.sort-by-filter')) return;

                venueList.style.display = 'none';

                sortByFilter.style.display =
                    (sortByFilter.style.display === 'block') ? 'none' : 'block';
            });

            venueSelection.addEventListener('click', (e) => {
                e.stopPropagation();
                if (e.target.closest('.venue-list')) return;

                sortByFilter.style.display = 'none';

                venueList.style.display =
                    (venueList.style.display === 'block') ? 'none' : 'block';
            });
        });

        document.addEventListener('click', () => {
            document.querySelectorAll('.sort-by-filter, .venue-list')
                .forEach(el => el.style.display = 'none');
        });


        // sort and venue selection ends

        // tab map js starts
        function initModalMap() {
            if (typeof window.initPartnerModalMap === 'function') {
                window.initPartnerModalMap();
            }
        }
        // tab map js ends
    </script>
    <script>
        document.querySelectorAll('.tab-go-to-section a[href^="#"]').forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();

                const targetId = this.getAttribute('href');
                const target = document.querySelector(targetId);
                if (!target) return;

                // Measure live heights of both sticky layers at click time
                const header = document.querySelector('header');
                const tabs = document.querySelector('.tab-go-to-section');

                const stickyOffset = (header ? header.offsetHeight : 0) +
                    (tabs ? tabs.offsetHeight : 0) +
                    20; // breathing room

                const top = target.getBoundingClientRect().top + window.scrollY - stickyOffset;

                window.scrollTo({
                    top,
                    behavior: 'smooth'
                });

                // Update active tab
                document.querySelectorAll('.tab-go-to-section a').forEach(a => a.classList.remove('active'));
                this.classList.add('active');
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const modal = document.getElementById('groomer_book_space');
            if (!modal) return;

            const list = modal.querySelector('[data-tab-content="groomer-list-view"]');
            const label = document.getElementById('spaceSelectionLabel');
            const continueBtn = document.getElementById('spaceContinueBtn');
            const pillBox = document.getElementById('groomerSelectedSection');
            const sortLabel = modal.querySelector('.space-sort-label');
            const gallery = document.getElementById('spaceGallery');
            const galleryImg = document.getElementById('spaceGalleryImg');
            const galleryCaption = document.getElementById('spaceGalleryCaption');
            let galleryPhotos = [];
            let galleryIndex = 0;
            let galleryTitle = '';

            function renderVenuePills() {
                if (!pillBox) return;
                pillBox.innerHTML = '';
                const seen = new Set();
                modal.querySelectorAll('input[name="space-venue[]"]:checked').forEach(function(input) {
                    const value = input.value;
                    if (seen.has(value)) return;
                    seen.add(value);

                    const pill = document.createElement('div');
                    pill.className = 'selected-item cursor d-flex align-items-center gap-10';
                    pill.dataset.value = value;
                    pill.innerHTML =
                        '<p></p>' +
                        '<svg class="cross cursor" xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none" aria-label="Remove filter">' +
                        '<path d="M0.5 7.57L7.572 0.5M0.5 0.5L7.572 7.57" stroke="#9D9B98" stroke-linecap="round"/>' +
                        '</svg>';
                    pill.querySelector('p').textContent = value;
                    pill.addEventListener('click', function(e) {
                        e.stopPropagation();
                        input.checked = false;
                        renderVenuePills();
                    });
                    pillBox.appendChild(pill);
                });
            }

            const mapRail = modal.querySelector('.map-space-rail');

            function getSelectedPartner() {
                return modal.querySelector('.space-result.is-selected, .map-space-card.is-selected');
            }

            function updateFooter() {
                const selected = getSelectedPartner();
                if (!selected) {
                    label.textContent = 'Select a space to continue';
                    continueBtn.textContent = 'Select a Space';
                    continueBtn.disabled = true;
                    continueBtn.classList.remove('is-ready');
                    return;
                }
                label.innerHTML = 'Selected <strong></strong> · <strong class="space-selection-total"></strong>';
                const strongs = label.querySelectorAll('strong');
                strongs[0].textContent = selected.dataset.name;
                strongs[1].textContent = '£' + selected.dataset.total + ' total';
                continueBtn.textContent = 'Continue to checkout';
                continueBtn.disabled = false;
                continueBtn.classList.add('is-ready');
            }

            function clearPartnerSelection() {
                list.querySelectorAll('.space-result.is-selected').forEach(function(item) {
                    item.classList.remove('is-selected');
                    const button = item.querySelector('.space-select');
                    if (button) {
                        button.textContent = 'Select';
                        button.setAttribute('aria-pressed', 'false');
                    }
                });
                if (mapRail) {
                    mapRail.querySelectorAll('.map-space-card.is-selected').forEach(function(item) {
                        item.classList.remove('is-selected');
                        const button = item.querySelector('.map-space-card-select');
                        if (button) {
                            button.textContent = 'Select';
                            button.setAttribute('aria-pressed', 'false');
                        }
                    });
                }
                updateFooter();
                if (typeof window.setPartnerModalMarkerActive === 'function') {
                    window.setPartnerModalMarkerActive(null);
                }
            }

            function scrollCardFullyVisible(card) {
                if (!card) return;
                const scroller = card.closest('.map-space-rail, .space-results');
                if (!scroller) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'center', inline: 'nearest' });
                    return;
                }
                const cardRect = card.getBoundingClientRect();
                const scrollerRect = scroller.getBoundingClientRect();
                const padding = 12;
                let delta = 0;
                if (cardRect.top < scrollerRect.top + padding) {
                    delta = cardRect.top - scrollerRect.top - padding;
                } else if (cardRect.bottom > scrollerRect.bottom - padding) {
                    delta = cardRect.bottom - scrollerRect.bottom + padding;
                }
                if (delta !== 0) {
                    scroller.scrollBy({ top: delta, behavior: 'smooth' });
                }
            }

            function syncPartnerSelection(name, opts) {
                opts = opts || {};
                if (!name) {
                    clearPartnerSelection();
                    return;
                }
                const scrollMap = opts.scrollMap !== false;
                const scrollList = opts.scrollList !== false;
                let listCard = null;
                let mapCard = null;

                list.querySelectorAll('.space-result').forEach(function(item) {
                    const on = item.dataset.name === name;
                    item.classList.toggle('is-selected', on);
                    const button = item.querySelector('.space-select');
                    if (button) {
                        button.textContent = on ? 'Selected' : 'Select';
                        button.setAttribute('aria-pressed', on ? 'true' : 'false');
                    }
                    if (on) listCard = item;
                });

                if (mapRail) {
                    mapRail.querySelectorAll('.map-space-card').forEach(function(item) {
                        const on = item.dataset.name === name;
                        item.classList.toggle('is-selected', on);
                        const button = item.querySelector('.map-space-card-select');
                        if (button) {
                            button.textContent = on ? 'Selected' : 'Select';
                            button.setAttribute('aria-pressed', on ? 'true' : 'false');
                        }
                        if (on) mapCard = item;
                    });
                }

                if (scrollMap && mapCard) scrollCardFullyVisible(mapCard);
                if (scrollList && listCard) scrollCardFullyVisible(listCard);

                updateFooter();
                if (typeof window.setPartnerModalMarkerActive === 'function') {
                    window.setPartnerModalMarkerActive(name);
                }
            }

            function selectSpace(card) {
                if (card.classList.contains('is-selected')) {
                    clearPartnerSelection();
                    return;
                }
                syncPartnerSelection(card.dataset.name, { scrollMap: true, scrollList: false });
            }

            function selectMapCard(card) {
                if (card.classList.contains('is-selected')) {
                    clearPartnerSelection();
                    return;
                }
                syncPartnerSelection(card.dataset.name, { scrollMap: true, scrollList: true });
            }

            function showGallery() {
                const photo = galleryPhotos[galleryIndex];
                if (!photo) return;
                galleryImg.src = photo;
                galleryImg.alt = galleryTitle;
                galleryCaption.textContent = galleryTitle + ' · ' + (galleryIndex + 1) + ' / ' + galleryPhotos.length;
                gallery.hidden = false;
            }

            function openGallery(card, index) {
                galleryPhotos = (card.dataset.photos || '').split('|').filter(Boolean);
                galleryTitle = card.dataset.name || 'Space photos';
                galleryIndex = index || 0;
                showGallery();
            }

            list.addEventListener('click', function(e) {
                const card = e.target.closest('.space-result');
                if (!card) return;

                if (e.target.closest('.space-select')) {
                    e.preventDefault();
                    selectSpace(card);
                    return;
                }

                if (e.target.closest('.space-message')) {
                    return;
                }

                const galleryTrigger = e.target.closest('[data-open-gallery]');
                if (galleryTrigger) {
                    e.preventDefault();
                    openGallery(card, Number(galleryTrigger.dataset.openGallery) || 0);
                    return;
                }

                if (e.target.closest('.space-show-less')) {
                    e.preventDefault();
                    card.classList.remove('is-open');
                    return;
                }

                if (e.target.closest('[data-expand]')) {
                    e.preventDefault();
                    card.classList.add('is-open');
                    return;
                }

                card.classList.toggle('is-open');
            });

            continueBtn.addEventListener('click', function() {
                if (continueBtn.disabled) return;
                const checkout = modal.dataset.partnerCheckout;
                if (checkout) window.location.href = checkout;
            });

            modal.querySelectorAll('input[name="space-venue[]"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    renderVenuePills();
                    const list = input.closest('.venu-sorting-section')?.querySelector('.venue-list');
                    if (list) list.style.display = 'none';
                });
            });

            modal.querySelectorAll('input[name="space-sort"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    if (!sortLabel) return;
                    const names = {
                        default: 'Best Match',
                        distance: 'Distance',
                        lowest_price: 'Lowest price',
                        soonest_available: 'Soonest available'
                    };
                    sortLabel.textContent = 'Sort: ' + (names[input.value] || 'Best Match');
                    const list = input.closest('.venu-sorting-section')?.querySelector('.sort-by-filter');
                    if (list) list.style.display = 'none';
                });
            });

            if (gallery) {
                gallery.addEventListener('click', function(e) {
                    if (e.target === gallery || e.target.closest('.space-gallery-close')) {
                        gallery.hidden = true;
                        return;
                    }
                    if (e.target.closest('.space-gallery-nav.next')) {
                        galleryIndex = (galleryIndex + 1) % galleryPhotos.length;
                        showGallery();
                    }
                    if (e.target.closest('.space-gallery-nav.prev')) {
                        galleryIndex = (galleryIndex - 1 + galleryPhotos.length) % galleryPhotos.length;
                        showGallery();
                    }
                });
            }

            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && gallery && !gallery.hidden) gallery.hidden = true;
            });


            window.selectPartnerFromMap = function(name) {
                syncPartnerSelection(name, { scrollMap: true, scrollList: true });
            };

            if (mapRail) {
                mapRail.addEventListener('click', function(e) {
                    if (e.target.closest('.map-space-card-msg')) return;
                    const card = e.target.closest('.map-space-card');
                    if (!card) return;
                    e.preventDefault();
                    selectMapCard(card);
                });
            }

            modal.querySelectorAll('[data-tab="groomer-map-view"]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const selected = getSelectedPartner();
                    setTimeout(function() {
                        if (window.initPartnerModalMap) window.initPartnerModalMap();
                        if (selected) {
                            syncPartnerSelection(selected.dataset.name, {
                                scrollMap: true,
                                scrollList: false
                            });
                        }
                    }, 120);
                });
            });

            renderVenuePills();
            clearPartnerSelection();
        });
    </script>

@endpush
