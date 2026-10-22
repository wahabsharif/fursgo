@extends('layouts.app')

@section('title', 'Fursgo - Space Profile')

@section('body_class', 'space-profile')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/groomer_space_profile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/choose_partner_modal.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>

        .image-grid {
            display: grid;
            grid-template-columns: 3fr 1fr 1fr;
            grid-template-rows: repeat(2, 200px);
            gap: 12px;
            grid-template-areas:
                "img-1 img-2 img-3"
                "img-1 img-4 img-5";
        }

        .image-grid-item {
            overflow: hidden;
            border-radius: 10px;
        }

        .image-grid-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        .inverted-radius {
            --r: 21px;
            --s: 18px;
            --x: 260px;
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
            background: #FBAC83;
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
            color: #FBAC83;
            border: 1px solid #FBAC83;
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
            /* border: 1px solid var(--groomer-color);
            background: var(--groomer-color); */
            border: 1px solid #FBAC83 !important;
            padding: 10px 20px 10px 20px;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-weight: 500;
            background: #fff;
            color: #FBAC83 !important;
            padding: 10px;
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
            border: 1px solid #FFC97A;
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
            min-height: 558px;
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
            /* align-items: center; */
            flex-direction: column;
            justify-content: space-between;
        }

        .card .tags {
            display: flex;
            gap: 10px
        }

        .card .tag {
            background: #FBAC83;
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
            height: 555px;
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
                    <h4 class="fs-18-600">Block this Space?</h4>
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
                    <img class="block-modal-avatar" src="{{ asset('images/space_profile_avatar.png') }}" alt="The Garden Grooming Spot">
                    <div>
                        <p class="block-modal-name">The Garden Grooming Spot <span class="dot">·</span> <span class="block-modal-type" style="color:#FBAC83">Space</span></p>
                        <p class="block-modal-owner">Dev Émile</p>
                    </div>
                </div>
                <p class="block-modal-info">You won't see this space in search, and Dev Émile won't be able to message you. You can unblock anytime in settings.</p>
                <p class="block-modal-reason-label">Reason (optional)</p>
                <div class="block-modal-reasons">
                    <label class="block-modal-reason">
                        <input type="radio" name="block_reason" value="not_interested" checked>
                        <span class="block-modal-radio"></span>
                        <span>I'm not interested in this Space</span>
                    </label>
                    <label class="block-modal-reason">
                        <input type="radio" name="block_reason" value="unwanted_messages">
                        <span class="block-modal-radio"></span>
                        <span>Unwanted messages from Space owner</span>
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
                    <span>Also report this Space to Fursgo for review</span>
                </label>
                <div class="block-modal-actions">
                    <button type="button" class="block-modal-cancel" data-modal-close>Cancel</button>
                    <button type="button" class="block-modal-confirm" data-home-url="{{ url('/') }}">Block Space</button>
                </div>
            </div>
        </div>

        <!-- Modal  -->

        <!-- <button class="book-btn" data-modal-open="groomer_book_space">Book a Space for Your Groomer</button> -->

        <!-- Modal 1 -->

        <div class="modal" id="groomer_book_space" data-partner-checkout="{{ url('/checkout_booking_space') }}/">
            <div class="modal-content">
                <div class="space-choose-head">
                    <button type="button" class="space-go-back" id="goBack">&larr; Go back</button>
                    <div class="space-choose-title">
                        <h1>Choose a pet groomer</h1>
                        <p>We've matched 3 groomers that fit your space booking.</p>
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
                            <img class="avatar" src="{{ asset('images/space_profile_avatar.png') }}" alt="Furs &amp; Co. Studio">
                            <div class="badge-shield" title="Verified">
                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="33" viewBox="0 0 30 33" fill="none">
                                    <ellipse cx="15.873" cy="16.5256" rx="9.3645" ry="8.81365" fill="white"></ellipse>
                                    <path d="M15.44 0.185076C15.1841 0.0638192 14.9099 0 14.6235 0C14.3372 0 14.063 0.0638192 13.8071 0.185076L2.3337 5.28423C0.993208 5.87775 -0.00606541 7.26263 2.77146e-05 8.93469C0.0304934 15.2656 2.51649 26.8487 13.015 32.1138C14.0325 32.6244 15.2146 32.6244 16.2321 32.1138C26.7306 26.8487 29.2166 15.2656 29.247 8.93469C29.2531 7.26263 28.2539 5.87775 26.9134 5.28423L15.44 0.185076ZM8.82897 18.2651C9.12144 18.3416 9.43219 18.3799 9.74903 18.3799C11.8999 18.3799 13.6486 16.5483 13.6486 14.2955V10.2111H16.3418C17.0791 10.2111 17.7554 10.645 18.0844 11.3407L18.5231 12.2533H22.4227C22.9589 12.2533 23.3976 12.7128 23.3976 13.2744V15.3166C23.3976 18.1374 21.2163 20.4222 18.5231 20.4222H15.5984V23.6578C15.5984 24.1237 15.2389 24.5066 14.7881 24.5066C14.6784 24.5066 14.5687 24.4811 14.4712 24.4364L8.45729 21.7368C8.05514 21.5581 7.79923 21.1433 7.79923 20.6902C7.79923 20.5115 7.83579 20.3392 7.915 20.1796L8.82897 18.2651ZM8.77413 10.2111H11.6988V14.2955C11.6988 15.4251 10.8275 16.3377 9.74903 16.3377C8.67055 16.3377 7.79923 15.4251 7.79923 14.2955V11.2322C7.79923 10.6706 8.23794 10.2111 8.77413 10.2111ZM16.5733 13.2744C16.5733 13.0036 16.4706 12.7439 16.2878 12.5524C16.105 12.3609 15.857 12.2533 15.5984 12.2533C15.3399 12.2533 15.0919 12.3609 14.9091 12.5524C14.7262 12.7439 14.6235 13.0036 14.6235 13.2744C14.6235 13.5452 14.7262 13.8049 14.9091 13.9964C15.0919 14.1879 15.3399 14.2955 15.5984 14.2955C15.857 14.2955 16.105 14.1879 16.2878 13.9964C16.4706 13.8049 16.5733 13.5452 16.5733 13.2744Z" fill="#CBDCE8"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="name-reviews">
                            <div>
                                <p class="fs-14-600-f-color">Furs &amp; Co. Studio</p>
                                <p class="fs-14-400-light ">Hosted by Dev E.</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-card-separator" aria-hidden="true"></div>

                    <div class="modal-card-right-section">
                        <div class="details-section">
                            <div class="details">
                                <div class="d-flex align-items-center gap-10">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13" fill="none">
                                        <path d="M13.1094 12.1166V3.83417C13.1094 3.81429 13.1111 3.79482 13.1141 3.77576L10.8748 1.86616C10.3986 1.46067 10.0696 1.18119 9.79046 0.998982C9.52095 0.823101 9.33983 0.766834 9.16658 0.766834C8.99347 0.766835 8.81349 0.823306 8.54428 0.998982C8.26512 1.18121 7.93524 1.46044 7.45838 1.86616L5.21745 3.77576C5.22054 3.7949 5.22374 3.81422 5.22374 3.83417V12.1166C5.2234 12.3281 5.04341 12.5 4.82144 12.5C4.59961 12.4998 4.41948 12.328 4.41914 12.1166V4.45573L4.00427 4.81069C3.83864 4.95183 3.58349 4.93709 3.43539 4.77924C3.28788 4.62148 3.30169 4.3796 3.46682 4.23856L6.92094 1.29553H6.92251C7.38342 0.90337 7.75667 0.583679 8.08855 0.366942C8.43046 0.143752 8.76989 2.24995e-07 9.16658 0C9.56323 0 9.9026 0.143743 10.2446 0.366942C10.5767 0.583731 10.9515 0.903225 11.4122 1.29553L14.8663 4.23856C15.0315 4.3796 15.0453 4.62148 14.8978 4.77924C14.7497 4.93709 14.4945 4.95183 14.3289 4.81069L13.914 4.45573V12.1166C13.9137 12.328 13.7336 12.4998 13.5117 12.5C13.2898 12.5 13.1098 12.3281 13.1094 12.1166Z" fill="#9D9B98" />
                                        <path d="M1.82418 6.66737C1.82418 6.37816 1.74192 6.13002 1.62487 5.96249C1.50777 5.79507 1.37173 5.7247 1.25 5.7247C1.12833 5.7248 0.992145 5.79519 0.875132 5.96249C0.758177 6.13002 0.675818 6.37832 0.675818 6.66737C0.675926 6.95653 0.758033 7.20483 0.875132 7.37226C0.992124 7.53946 1.12837 7.60853 1.25 7.60863C1.37164 7.60863 1.50783 7.53939 1.62487 7.37226C1.74197 7.20483 1.82407 6.95653 1.82418 6.66737ZM2.5 6.66737C2.49989 7.09818 2.37897 7.50235 2.16605 7.80679C1.95294 8.11149 1.63215 8.33333 1.25 8.33333C0.868121 8.33323 0.548331 8.11124 0.335269 7.80679C0.12233 7.50234 0.000106589 7.0982 0 6.66737C0 6.23634 0.122237 5.83113 0.335269 5.52654C0.548331 5.22219 0.868196 5.0001 1.25 5C1.63209 5 1.95294 5.22191 2.16605 5.52654C2.37908 5.83113 2.5 6.23634 2.5 6.66737Z" fill="#9D9B98" />
                                        <path d="M0.833252 12.1094V7.8906C0.833252 7.67488 1.0198 7.5 1.24992 7.5C1.48004 7.5 1.66659 7.67488 1.66659 7.8906V12.1094C1.66641 12.325 1.47993 12.5 1.24992 12.5C1.01991 12.5 0.833428 12.325 0.833252 12.1094Z" fill="#9D9B98" />
                                        <path d="M10.6579 9.31364C10.6579 8.9734 10.6564 8.75738 10.6348 8.59906C10.6147 8.4523 10.584 8.41411 10.5654 8.39576C10.5468 8.37748 10.5083 8.34577 10.3588 8.32597C10.1978 8.30466 9.97715 8.30473 9.63096 8.30473H8.92167C8.57549 8.30473 8.35488 8.30466 8.19387 8.32597C8.04438 8.34577 8.00583 8.37748 7.98725 8.39576C7.96865 8.41411 7.93793 8.4523 7.91787 8.59906C7.89622 8.75738 7.89474 8.9734 7.89474 9.31364V11.7229H10.6579V9.31364ZM9.98715 5.42972C10.2048 5.42988 10.3816 5.60399 10.3819 5.81811C10.3819 6.03251 10.205 6.20634 9.98715 6.2065H8.56548C8.34762 6.20634 8.17074 6.03251 8.17074 5.81811C8.17108 5.60399 8.34782 5.42988 8.56548 5.42972H9.98715ZM9.98715 3.33301L10.0658 3.34059C10.246 3.37657 10.3819 3.53349 10.3819 3.7214C10.3819 3.90931 10.246 4.06623 10.0658 4.10221L9.98715 4.10979H8.56548C8.34762 4.10963 8.17074 3.9358 8.17074 3.7214C8.17074 3.507 8.34762 3.33317 8.56548 3.33301H9.98715ZM11.4474 11.7229H14.6053C14.8233 11.7229 15 11.8968 15 12.1113C14.9997 12.3255 14.8231 12.4997 14.6053 12.4997H0.394737C0.176935 12.4997 0.000332468 12.3255 0 12.1113C0 11.8968 0.17673 11.7229 0.394737 11.7229H7.10526V9.31364C7.10526 8.99552 7.10427 8.71791 7.13456 8.4959C7.16648 8.26247 7.23958 8.03308 7.42907 7.84655C7.61867 7.66 7.85172 7.58819 8.08902 7.55678C8.31486 7.52691 8.59793 7.52795 8.92167 7.52795H9.63096C9.95471 7.52795 10.2378 7.52691 10.4636 7.55678C10.7009 7.58819 10.934 7.66 11.1236 7.84655C11.313 8.03308 11.3862 8.26247 11.4181 8.4959C11.4484 8.71791 11.4474 8.99552 11.4474 9.31364V11.7229Z" fill="#9D9B98" />
                                    </svg>
                                    <p class="light-bold-color-font">Space</p>
                                </div>
                                <p class="simple-font mt-1">Garden / Shed</p>
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
                                <p class="simple-font mt-1">14:30 - 18:30</p>
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
                                <p class="simple-font mt-1">Victoria Embankment</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-toolbar">
                    <div>
                        <h2>Groomer Results</h2>
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
                            Groomer Type
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none">
                                <path d="M1 1L6 6L11 1" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="venue-list">
                                <div class="venu dropdown">
                                    <ul>
                                        <li><label><span class="option-text">Salons</span><input type="checkbox" name="groomer-venue[]" value="Salons"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Groomer's studio</span><input type="checkbox" name="groomer-venue[]" value="Groomer's studio"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Home Visit</span><input type="checkbox" name="groomer-venue[]" value="Home Visit"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Top Rated</span><input type="checkbox" name="groomer-venue[]" value="Top Rated" checked><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Others</span><input type="checkbox" name="groomer-venue[]" value="Others"><span class="check-circle"></span></label></li>
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
                                        <li><label><span class="option-text">Best Match</span><input type="radio" name="groomer-sort" value="default" checked><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Distance</span><input type="radio" name="groomer-sort" value="distance"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Lowest price</span><input type="radio" name="groomer-sort" value="lowest_price"><span class="check-circle"></span></label></li>
                                        <li><label><span class="option-text">Soonest available</span><input type="radio" name="groomer-sort" value="soonest_available"><span class="check-circle"></span></label></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="filter space-active-filters">
                    <div class="selected-item-section d-flex align-items-center flex-wrap gap-10" id="spaceGroomerSelectedSection"></div>
                </div>

                <div data-tab-content="groomer-list-view" class="tabcontent" data-display="flex">
                    <div class="space-results">
                        <article class="space-result" data-name="Ken&#39;s Grooming Mobile" data-total="128.00" data-photos="{{ asset('images/card1.png') }}|{{ asset('images/card2.png') }}|{{ asset('images/card3.png') }}|{{ asset('images/card1.png') }}|{{ asset('images/card2.png') }}|{{ asset('images/card3.png') }}|{{ asset('images/card1.png') }}">
                            <div class="space-result-main">
                                <button type="button" class="space-result-photo" data-open-gallery="0" aria-label="Ken&#39;s Grooming Mobile">
                                    <div class="top-left-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none" aria-hidden="true">
                                            <rect x="4.14746" y="4.14746" width="12.443" height="13.8256" rx="3" fill="white" />
                                            <path d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z" fill="#C9DDA0" />
                                        </svg>
                                    </div>
                                    <img src="{{ asset('images/card1.png') }}" alt="Ken&#39;s Grooming Mobile">
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
                                                <h3>Ken&#39;s Grooming Mobile</h3>
                                                <span class="tag">Groomer's Studio</span>
                                                <span class="pill toprated">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                                                        <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                                                    </svg>
                                                    Best match
                                                </span>
                                            </div>
                                            <p class="space-hosted">Hosted by Ken M.</p>
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
                                            </svg>Nail Trim</span>

                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Dryer</span>
                                        <button type="button" class="space-amenity-chip more" data-expand>+4 more</button>
                                    </div>
                                    <div class="space-result-bottom">
                                        <div class="space-price">
                                            <strong>£58.00 <span>/ per hour</span></strong>
                                            <p>Full Groom £70 + Space £58 (Hourly)</p>
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
                                    <p class="space-about-kicker">ABOUT KEN'S GROOMING MOBILE</p>
                                    <p class="space-tagline">Reliable mobile grooming with calm handling.</p>
                                    <p class="space-about-copy">Ken brings a fully equipped mobile setup to your booked space, with calm handling for nervous pets and everything needed for a complete full groom.</p>
                                    <div class="space-stat-row">
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            ID-Verified
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            5+ years of experience
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            City & Guilds Certified
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Insured
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Animal-handling certified
                                        </span>
                                    </div>
                                    <p class="space-about-kicker">SERVICES OFFERED</p>
                                    <div class="space-amenity-grid">
                                        <span class="space-amenity-pill">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            Grooming Table</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Nail Trim</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath &amp; Brush</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Face Trim</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Pet Spa</span>
                                    </div>
                                    <button type="button" class="space-show-less">
                                        Show less
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                                            <path d="M0.625 5.625L5.625 0.625L10.625 5.625" stroke="#FFA899" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-thumbs">
                                    <button type="button" data-open-gallery="0"><img src="{{ asset('images/card1.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="1"><img src="{{ asset('images/card2.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="2"><img src="{{ asset('images/card3.png') }}" alt="">
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

                        <article class="space-result is-open" data-name="Cathy&#39;s Services" data-total="110.00" data-photos="{{ asset('images/card2.png') }}|{{ asset('images/card3.png') }}|{{ asset('images/card1.png') }}|{{ asset('images/card2.png') }}|{{ asset('images/card3.png') }}|{{ asset('images/card1.png') }}|{{ asset('images/card2.png') }}">
                            <div class="space-result-main">
                                <button type="button" class="space-result-photo" data-open-gallery="0" aria-label="Cathy&#39;s Services">
                                    <div class="top-left-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none" aria-hidden="true">
                                            <rect x="4.14746" y="4.14746" width="12.443" height="13.8256" rx="3" fill="white" />
                                            <path d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z" fill="#C9DDA0" />
                                        </svg>
                                    </div>
                                    <img src="{{ asset('images/card2.png') }}" alt="Cathy&#39;s Services">
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
                                                <h3>Cathy&#39;s Services</h3>
                                                <span class="tag">Home Visit</span>
                                            </div>
                                            <p class="space-hosted">Hosted by Cathy R.</p>
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
                                            </svg>Nail Trim</span>
                                        <span class="space-amenity-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath &amp; Brush</span>
                                        <button type="button" class="space-amenity-chip more" data-expand>+4 more</button>
                                    </div>
                                    <div class="space-result-bottom">
                                        <div class="space-price">
                                            <strong>£52.00 <span>/ per hour</span></strong>
                                            <p>Full Groom £58 + Space £52 (Hourly)</p>
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
                                    <p class="space-about-kicker">ABOUT CATHY'S SERVICES</p>
                                    <p class="space-tagline">Gentle, professional care for every coat type.</p>
                                    <p class="space-about-copy">Cathy specialises in small breeds and anxious pets, with soft handling and a thorough finish that fits the time window on your space booking.</p>
                                    <div class="space-stat-row">
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            ID-Verified
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            5+ years of experience
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            City & Guilds Certified
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Insured
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Animal-handling certified
                                        </span>
                                    </div>
                                    <p class="space-about-kicker">SERVICES OFFERED</p>
                                    <div class="space-amenity-grid">
                                        <span class="space-amenity-pill">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Full Groom</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Nail Trim</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath &amp; Brush</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Face Trim</span>
                                        <span class="space-amenity-pill">Pet Spa</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Mobile Visit</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Hypoallergenic</span>
                                    </div>
                                    <button type="button" class="space-show-less">
                                        Show less
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                                            <path d="M0.625 5.625L5.625 0.625L10.625 5.625" stroke="#FFA899" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-thumbs">
                                    <button type="button" data-open-gallery="0"><img src="{{ asset('images/card2.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="1"><img src="{{ asset('images/card3.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="2"><img src="{{ asset('images/card1.png') }}" alt="">
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

                        <article class="space-result" data-name="Sarah&#39;s Grooming Studio" data-total="106.00" data-photos="{{ asset('images/card3.png') }}|{{ asset('images/card1.png') }}|{{ asset('images/card2.png') }}|{{ asset('images/card3.png') }}|{{ asset('images/card1.png') }}|{{ asset('images/card2.png') }}|{{ asset('images/card3.png') }}">
                            <div class="space-result-main">
                                <button type="button" class="space-result-photo" data-open-gallery="0" aria-label="Sarah&#39;s Grooming Studio">
                                    <div class="top-left-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none" aria-hidden="true">
                                            <rect x="4.14746" y="4.14746" width="12.443" height="13.8256" rx="3" fill="white" />
                                            <path d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z" fill="#C9DDA0" />
                                        </svg>
                                    </div>
                                    <img src="{{ asset('images/card3.png') }}" alt="Sarah&#39;s Grooming Studio">
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
                                                <h3>Sarah&#39;s Grooming Studio</h3>
                                                <span class="pill garden-shed">Visiting Groomers</span>
                                                <span class="pill toprated">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                                                        <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                                                    </svg>
                                                    Best match
                                                </span>
                                            </div>
                                            <p class="space-hosted">Hosted by Sarah W.</p>
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
                                            </svg>Full Groom</span>
                                        <span class="space-amenity-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Nail Trim</span>
                                        <span class="space-amenity-chip"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.5 4.5L4 8L11 0.5" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath &amp; Brush</span>
                                        <button type="button" class="space-amenity-chip more" data-expand>+4 more</button>
                                    </div>
                                    <div class="space-result-bottom">
                                        <div class="space-price">
                                            <strong>£48.00 <span>/ per hour</span></strong>
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
                                    <p class="space-about-kicker">ABOUT SARAH'S GROOMING STUDIO</p>
                                    <p class="space-tagline">Luxury grooming with a gentle touch.</p>
                                    <p class="space-about-copy">Sarah is a professional groomer in West London specialising in small breeds, anxious pets, and gentle handling through every full groom.</p>
                                    <div class="space-stat-row">
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            ID-Verified
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            5+ years of experience
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            City & Guilds Certified
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Insured
                                        </span>
                                        <span class="space-amenity-chip">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                <path d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z" fill="#DDDDDD" />
                                            </svg>
                                            Animal-handling certified
                                        </span>
                                    </div>
                                    <p class="space-about-kicker">SERVICES OFFERED</p>
                                    <div class="space-amenity-grid">
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Full Groom</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Nail Trim</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Bath &amp; Brush</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Face Trim</span>
                                        <span class="space-amenity-pill">Pet Spa</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Mobile Visit</span>
                                        <span class="space-amenity-pill"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                                <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Hypoallergenic</span>
                                    </div>
                                    <button type="button" class="space-show-less">
                                        Show less
                                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="7" viewBox="0 0 12 7" fill="none" aria-hidden="true">
                                            <path d="M0.625 5.625L5.625 0.625L10.625 5.625" stroke="#FFA899" stroke-width="1.25" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </button>
                                </div>
                                <div class="space-thumbs">
                                    <button type="button" data-open-gallery="0"><img src="{{ asset('images/card3.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="1"><img src="{{ asset('images/card1.png') }}" alt=""></button>
                                    <button type="button" data-open-gallery="2"><img src="{{ asset('images/card2.png') }}" alt="">
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
                            <article class="map-space-card is-selected" data-name="Ken&#39;s Grooming Mobile" data-total="128.00" data-price="58.00">
                                <div class="map-space-card-photo">
                                    <div class="top-left-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none" aria-hidden="true">
                                            <rect x="4.14746" y="4.14746" width="12.443" height="13.8256" rx="3" fill="white" />
                                            <path d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z" fill="#C9DDA0" />
                                        </svg>
                                    </div>
                                    <span class="map-space-card-best">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none" aria-hidden="true">
                                            <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                                        </svg>
                                        Best match
                                    </span>
                                    <img src="{{ asset('images/card1.png') }}" alt="Ken&#39;s Grooming Mobile">
                                </div>
                                <div class="map-space-card-body">
                                    <div class="map-space-card-head">
                                        <div>
                                            <h3>Ken&#39;s Grooming Mobile</h3>
                                            <p>Hosted by Ken M.</p>
                                        </div>
                                        <a class="map-space-card-msg" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener" aria-label="Message">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none" aria-hidden="true">
                                                <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                                                <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="map-space-card-meta">
                                        <span class="map-space-card-type">Groomer's Studio</span>
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
                                        <strong>&pound;58.00 <span>p/h</span></strong>
                                        <button type="button" class="map-space-card-select" aria-pressed="true">Selected</button>
                                    </div>
                                </div>
                            </article>
                            <article class="map-space-card" data-name="Cathy&#39;s Services" data-total="110.00" data-price="42.00">
                                <div class="map-space-card-photo">
                                    <div class="top-left-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none" aria-hidden="true">
                                            <rect x="4.14746" y="4.14746" width="12.443" height="13.8256" rx="3" fill="white" />
                                            <path d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z" fill="#C9DDA0" />
                                        </svg>
                                    </div>
                                    <img src="{{ asset('images/card2.png') }}" alt="Cathy&#39;s Services">
                                </div>
                                <div class="map-space-card-body">
                                    <div class="map-space-card-head">
                                        <div>
                                            <h3>Cathy&#39;s Services</h3>
                                            <p>Hosted by Cathy R.</p>
                                        </div>
                                        <a class="map-space-card-msg" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener" aria-label="Message">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none" aria-hidden="true">
                                                <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                                                <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="map-space-card-meta">
                                        <span class="map-space-card-type">Home Visit</span>
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
                                        <strong>&pound;42.00 <span>p/h</span></strong>
                                        <button type="button" class="map-space-card-select" aria-pressed="false">Select</button>
                                    </div>
                                </div>
                            </article>
                            <article class="map-space-card" data-name="Sarah&#39;s Grooming Studio" data-total="106.00" data-price="48.00">
                                <div class="map-space-card-photo">
                                    <div class="top-left-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22" viewBox="0 0 21 22" fill="none" aria-hidden="true">
                                            <rect x="4.14746" y="4.14746" width="12.443" height="13.8256" rx="3" fill="white" />
                                            <path d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z" fill="#C9DDA0" />
                                        </svg>
                                    </div>
                                    <span class="map-space-card-best">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none" aria-hidden="true">
                                            <path d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z" fill="white" />
                                        </svg>
                                        Best match
                                    </span>
                                    <img src="{{ asset('images/card3.png') }}" alt="Sarah&#39;s Grooming Studio">
                                </div>
                                <div class="map-space-card-body">
                                    <div class="map-space-card-head">
                                        <div>
                                            <h3>Sarah&#39;s Grooming Studio</h3>
                                            <p>Hosted by Sarah W.</p>
                                        </div>
                                        <a class="map-space-card-msg" href="{{ url('/messages_notification/messages') }}" target="_blank" rel="noopener" aria-label="Message">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="15" viewBox="0 0 19 15" fill="none" aria-hidden="true">
                                                <path d="M0.5 7.5C0.5 4.20038 0.5 2.55012 1.5255 1.5255C2.551 0.500875 4.20038 0.5 7.5 0.5H11C14.2996 0.5 15.9499 0.5 16.9745 1.5255C17.9991 2.551 18 4.20038 18 7.5C18 10.7996 18 12.4499 16.9745 13.4745C15.949 14.4991 14.2996 14.5 11 14.5H7.5C4.20038 14.5 2.55012 14.5 1.5255 13.4745C0.500875 12.449 0.5 10.7996 0.5 7.5Z" stroke="#9D9B98" />
                                                <path d="M4 4L5.88913 5.575C7.4965 6.91375 8.29975 7.58313 9.25 7.58313C10.2002 7.58313 11.0044 6.91375 12.6109 5.57412L14.5 4" stroke="#9D9B98" stroke-linecap="round" />
                                            </svg>
                                        </a>
                                    </div>
                                    <div class="map-space-card-meta">
                                        <span class="map-space-card-type">Groomer's Studio</span>
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
                                        <strong>&pound;48.00 <span>p/h</span></strong>
                                        <button type="button" class="map-space-card-select" aria-pressed="false">Select</button>
                                    </div>
                                </div>
                            </article>
                        </div>
                        <div class="map-view-map">
                            <div class="map-wrapper modal-map-wrapper">
                                <div id="modal-map" data-map-type="groomer"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="space-list-footer map-shared-footer">
                    <p id="spaceSelectionLabel">Select a groomer to continue</p>
                    <button type="button" class="space-continue" id="spaceContinueBtn" disabled>Select a Groomer</button>
                </div>

                <div class="space-gallery" id="spaceGallery" hidden>
                    <div class="space-gallery-dialog" role="dialog" aria-modal="true" aria-label="Groomer photos">
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
                            <img class="avatar" src="{{ asset('images/space_profile_avatar.png') }}" alt="The Garden Grooming Spot">
                            <div class="badge-shield" title="Verified">
                                <svg xmlns="http://www.w3.org/2000/svg" width="29" height="30" viewBox="0 0 29 30" fill="none">
                                    <path d="M14.9293 0.170856C14.6819 0.058916 14.4168 0 14.1399 0C13.8629 0 13.5978 0.058916 13.3504 0.170856L2.25651 4.87824C0.960357 5.42616 -0.0058648 6.70463 2.67979e-05 8.24823C0.0294848 14.0927 2.43326 24.7859 12.5845 29.6465C13.5684 30.1178 14.7113 30.1178 15.6952 29.6465C25.8465 24.7859 28.2502 14.0927 28.2797 8.24823C28.2856 6.70463 27.3194 5.42616 26.0232 4.87824L14.9293 0.170856Z" fill="#CBDCE8" />
                                    <path d="M21.818 8.18213L15.7574 14.6215L21.818 8.18213ZM13.065 14.3071C11.1862 15.0283 9.68391 14.9049 8.18164 14.3094C8.56043 19.1905 10.8362 21.067 13.8703 21.8185C13.8703 21.8185 16.1559 20.2018 16.4854 16.3693C16.521 15.9541 16.5385 15.7473 16.4529 15.5132C16.3665 15.2791 16.1968 15.1117 15.8582 14.7761C15.3006 14.2246 15.0226 13.9488 14.6915 13.8791C14.3604 13.8109 13.9286 13.9761 13.065 14.3071Z" fill="#CBDCE8" />
                                    <path d="M21.818 8.18213L15.7574 14.6215M13.065 14.3071C11.1862 15.0283 9.68391 14.9049 8.18164 14.3094C8.56043 19.1905 10.8362 21.067 13.8703 21.8185C13.8703 21.8185 16.1559 20.2018 16.4854 16.3693C16.521 15.9541 16.5385 15.7473 16.4529 15.5132C16.3665 15.2791 16.1968 15.1117 15.8582 14.7761C15.3006 14.2246 15.0225 13.9488 14.6915 13.8791C14.3604 13.8109 13.9286 13.9761 13.065 14.3071Z" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M9.31836 18.3679C9.31836 18.3679 11.2123 18.7346 13.1062 17.2725L9.31836 18.3679Z" fill="#CBDCE8" />
                                    <path d="M9.31836 18.3679C9.31836 18.3679 11.2123 18.7346 13.1062 17.2725" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M12.3485 11.4016C12.3485 11.6527 12.2488 11.8936 12.0712 12.0712C11.8936 12.2488 11.6527 12.3485 11.4016 12.3485C11.1504 12.3485 10.9095 12.2488 10.732 12.0712C10.5544 11.8936 10.4546 11.6527 10.4546 11.4016C10.4546 11.1504 10.5544 10.9095 10.732 10.732C10.9095 10.5544 11.1504 10.4546 11.4016 10.4546C11.6527 10.4546 11.8936 10.5544 12.0712 10.732C12.2488 10.9095 12.3485 11.1504 12.3485 11.4016Z" fill="#CBDCE8" stroke="white" />
                                    <path d="M14.2432 8.93896V9.01472V8.93896Z" fill="#CBDCE8" />
                                    <path d="M14.2432 8.93896V9.01472" stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </div>
                        </div>

                        <div class="meta">
                            <h1>The Garden Grooming Spot</h1>
                            <div class="owner">Hosted by <span style="color: var(--muted)">Dev Émile</span></div>

                            <div style="display:flex; align-items:center; gap:12px;">
                                <div class="tags-row gap mt-2">
                                    <span class="pill garden-shed">
                                        Garden / Shed
                                    </span>
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
                                <span class="add-to-fav">Add to Favourites</span>
                            </div>

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

                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="19" viewBox="0 0 20 19" fill="none">
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
                                        Block Space Host
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
                        <img src="{{ asset('images/space_profile_1.png') }}"
                            alt="Front Entrance"
                            data-desc="Clear signage and easy access, so drop-off and pick-up are smooth and stress-free.">
                    </div>

                    <div class="image-grid-item large-image" style="grid-area: img-2;">
                        <img src="{{ asset('images/space_profile_2.png') }}"
                            alt="Space Image 2"
                            data-desc="Well-organized grooming station with professional tools and a hygienic setup.">
                    </div>

                    <div class="image-grid-item small-image" style="grid-area: img-3;">
                        <img src="{{ asset('images/space_profile_3.png') }}"
                            alt="Space Image 3"
                            data-desc="Comfortable grooming area designed to keep pets relaxed during sessions.">
                    </div>

                    <div class="image-grid-item small-image" style="grid-area: img-4;">
                        <img src="{{ asset('images/space_profile_4.png') }}"
                            alt="Space Image 4"
                            data-desc="Professional grooming environment focused on hygiene and precision care.">
                    </div>

                    <div class="image-grid-item small-image last-image-text" style="grid-area: img-5;">
                        <img src="{{ asset('images/space_profile_5.png') }}"
                            alt="Space Image 5"
                            data-desc="Final overview of a premium grooming space built for comfort and efficiency.">

                        <p class="show-all-pics">
                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M9.77778 4.88889C10.0861 4.88879 10.3831 5.00525 10.6092 5.21491C10.8353 5.42458 10.9738 5.71196 10.9969 6.01944L11 6.11111V9.77778C11.0001 10.0861 10.8836 10.3831 10.674 10.6092C10.4643 10.8353 10.1769 10.9738 9.86944 10.9969L9.77778 11H7.33333C7.02498 11.0001 6.72799 10.8836 6.50189 10.674C6.27579 10.4643 6.13729 10.1769 6.11417 9.86944L6.11111 9.77778V6.11111C6.11101 5.80276 6.22747 5.50576 6.43714 5.27966C6.6468 5.05357 6.93418 4.91507 7.24167 4.89194L7.33333 4.88889H9.77778ZM3.66667 7.33333C3.99082 7.33333 4.3017 7.4621 4.53091 7.69131C4.76012 7.92053 4.88889 8.2314 4.88889 8.55556V9.77778C4.88889 10.1019 4.76012 10.4128 4.53091 10.642C4.3017 10.8712 3.99082 11 3.66667 11H1.22222C0.898069 11 0.587192 10.8712 0.357981 10.642C0.128769 10.4128 0 10.1019 0 9.77778V8.55556C0 8.2314 0.128769 7.92053 0.357981 7.69131C0.587192 7.4621 0.898069 7.33333 1.22222 7.33333H3.66667ZM3.66667 0C3.99082 0 4.3017 0.128769 4.53091 0.357981C4.76012 0.587192 4.88889 0.898069 4.88889 1.22222V4.88889C4.88889 5.21304 4.76012 5.52392 4.53091 5.75313C4.3017 5.98234 3.99082 6.11111 3.66667 6.11111H1.22222C0.898069 6.11111 0.587192 5.98234 0.357981 5.75313C0.128769 5.52392 0 5.21304 0 4.88889V1.22222C0 0.898069 0.128769 0.587192 0.357981 0.357981C0.587192 0.128769 0.898069 0 1.22222 0H3.66667ZM9.77778 0C10.1019 0 10.4128 0.128769 10.642 0.357981C10.8712 0.587192 11 0.898069 11 1.22222V2.44444C11 2.7686 10.8712 3.07947 10.642 3.30869C10.4128 3.5379 10.1019 3.66667 9.77778 3.66667H7.33333C7.00918 3.66667 6.6983 3.5379 6.46909 3.30869C6.23988 3.07947 6.11111 2.7686 6.11111 2.44444V1.22222C6.11111 0.898069 6.23988 0.587192 6.46909 0.357981C6.6983 0.128769 7.00918 0 7.33333 0H9.77778Z"
                                    fill="white" />
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
                        <span class="lb-title">The Garden Grooming Spot</span>
                        <span class="lb-subtitle">Dev Émile</span>
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
            <div class="col-lg-8">
                <div class="service-content mt-4 mb-5">
                    <div class="service-content-header about-studio">
                        <h2 class="section-content-heading about-studio__title">About The Garden Grooming Spot</h2>
                        <p class="about-studio__tagline">Spacious garden space for all furry friends (tagline).</p>
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
                            A bright, clean, fully equipped grooming outdoor space ideal for professional use. Outdoor garden grooming area. Calm, spacious, and ideal for stress-free sessions in fresh air.
                        </p>
                        <div class="about-studio__badges">
                            <span class="about-studio__badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="8" fill="#D8E8B7" />
                                    <path d="M4.5 8.2L6.6 10.2L11.5 5.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Fits 1–3 groomers
                            </span>
                            <span class="about-studio__badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="8" fill="#D8E8B7" />
                                    <path d="M4.5 8.2L6.6 10.2L11.5 5.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Up to 4 pets at a time
                            </span>
                            <span class="about-studio__badge">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                    <circle cx="8" cy="8" r="8" fill="#D8E8B7" />
                                    <path d="M4.5 8.2L6.6 10.2L11.5 5.5" stroke="#fff" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Approx. 400 sq. ft.
                            </span>
                        </div>
                    </div>
                    <div class="tab-go-to-section less-gap d-flex align-items-center flex-wrap justify-content-center mt-5">
                        <a href="#services_and_pricing" class="active">Services</a>
                        <a href="#amenities">Amenities</a>
                        <a href="#before_you_book">Before you book</a>
                        <a href="#reviews">Reviews</a>
                        <a href="#location">Location</a>
                    </div>
                    <div id="services_and_pricing" class="mt-5">
                        <h2 class="section-content-heading">Services & Pricing</h2>
                        <div class="services-list d-flex align-items-center justify-content-between mt-4">
                            <div class="service-list-left d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="22" viewBox="0 0 26 22" fill="none">
                                    <path d="M10.9401 0C15.9387 0 20.1532 3.35263 21.4601 7.93179C21.5815 8.35738 21.2287 8.75207 20.7861 8.75207C20.6431 8.75207 20.5181 8.65675 20.4831 8.5181C19.4037 4.25167 15.5419 1.09401 10.9401 1.09401C5.50224 1.09401 1.09401 5.50225 1.09401 10.9401C1.09401 15.8782 4.72947 19.9655 9.46984 20.6752C9.71705 20.7122 9.912 20.9111 9.94481 21.1589C9.99054 21.5043 9.71174 21.8142 9.36694 21.7645C4.07035 21.0019 0 16.4479 0 10.9401C0 4.89805 4.89804 0 10.9401 0Z" fill="#3B3731" />
                                    <path d="M22.3843 17.0281C22.6177 16.7911 22.8748 16.5996 23.1556 16.4538C23.44 16.3079 23.7664 16.235 24.1347 16.235C24.441 16.235 24.7091 16.286 24.9388 16.3881C25.1722 16.4902 25.3673 16.6343 25.5241 16.8203C25.6809 17.0026 25.7994 17.2232 25.8796 17.4821C25.9599 17.7411 26 18.0273 26 18.3409V21.8801H24.9279V18.3409C24.9279 17.9435 24.8367 17.6353 24.6544 17.4165C24.472 17.1941 24.193 17.0828 23.8174 17.0828C23.5439 17.0828 23.2868 17.1485 23.0462 17.2798C22.8091 17.4074 22.5885 17.5824 22.3843 17.8049V21.8801H21.3176V13.8063H22.3843V17.0281Z" fill="#3B3731" />
                                    <path d="M19.8125 21.0651V21.8802H15.5677V21.0651H17.2306V15.9178C17.2306 15.7537 17.2361 15.5842 17.247 15.4091L15.9124 16.5414C15.8613 16.5815 15.8102 16.6071 15.7592 16.618C15.7081 16.6289 15.6589 16.6308 15.6115 16.6235C15.5677 16.6162 15.5276 16.6016 15.4912 16.5797C15.4583 16.5542 15.431 16.5287 15.4091 16.5031L15.0754 16.0382L17.4276 14.0088H18.2973V21.0651H19.8125Z" fill="#3B3731" />
                                    <path d="M11.487 7.65809C11.487 7.35599 11.7319 7.11108 12.034 7.11108C12.3361 7.11108 12.581 7.35599 12.581 7.65809V12.5811H8.75196C8.44986 12.5811 8.20496 12.3362 8.20496 12.0341C8.20496 11.732 8.44986 11.4871 8.75196 11.4871H11.487V7.65809Z" fill="#3B3731" />
                                </svg>
                                &nbsp;
                                &nbsp;
                                <p>Hourly</p>
                            </div>
                            <div class="service-list-right">
                                <div class="price space d-flex align-items-center">
                                    <p>£25</p><span> / </span> <span>Hourly</span>
                                </div>
                            </div>
                        </div>
                        <div class="services-list d-flex align-items-center justify-content-between mt-4">
                            <div class="service-list-left d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="22" viewBox="0 0 26 22" fill="none">
                                    <path d="M10.94 0C15.9386 0 20.1531 3.35262 21.4599 7.93175C21.5813 8.35734 21.2285 8.75203 20.786 8.75203C20.643 8.75203 20.518 8.65671 20.4829 8.51806C19.4036 4.25165 15.5418 1.094 10.94 1.094C5.5022 1.094 1.094 5.50222 1.094 10.94C1.094 15.8781 4.72943 19.9654 9.46977 20.6751C9.71697 20.7121 9.91192 20.911 9.94473 21.1588C9.99046 21.5042 9.71166 21.8141 9.36686 21.7644C4.07032 21.0018 0 16.4478 0 10.94C0 4.89802 4.898 0 10.94 0Z" fill="#3B3731" />
                                    <path d="M22.3842 17.0281C22.6175 16.7911 22.8746 16.5996 23.1554 16.4538C23.4399 16.3079 23.7662 16.235 24.1346 16.235C24.4409 16.235 24.7089 16.286 24.9386 16.3881C25.172 16.4902 25.3671 16.6343 25.5239 16.8203C25.6807 17.0026 25.7993 17.2232 25.8795 17.4821C25.9597 17.741 25.9998 18.0273 25.9998 18.3409V21.88H24.9277V18.3409C24.9277 17.9434 24.8365 17.6353 24.6542 17.4165C24.4719 17.194 24.1929 17.0828 23.8173 17.0828C23.5438 17.0828 23.2867 17.1485 23.046 17.2797C22.809 17.4074 22.5884 17.5824 22.3842 17.8049V21.88H21.3175V13.8063H22.3842V17.0281Z" fill="#3B3731" />
                                    <path d="M18.1931 19.0137V15.9504C18.1931 15.8447 18.1949 15.7335 18.1986 15.6168C18.2059 15.4964 18.2186 15.3724 18.2369 15.2448L15.4854 19.0137H18.1931ZM20.2662 19.0137V19.6318C20.2662 19.6938 20.2462 19.7466 20.206 19.7904C20.1659 19.8342 20.1094 19.856 20.0365 19.856H19.1285V21.88H18.1931V19.856H14.747C14.6668 19.856 14.5993 19.8342 14.5446 19.7904C14.4899 19.743 14.4534 19.6846 14.4352 19.6154L14.3258 19.0684L18.122 14.0195H19.1285V19.0137H20.2662Z" fill="#3B3731" />
                                    <path d="M11.487 7.65809C11.487 7.35599 11.7319 7.11108 12.034 7.11108C12.3361 7.11108 12.581 7.35599 12.581 7.65809V12.5811H8.75196C8.44986 12.5811 8.20496 12.3362 8.20496 12.0341C8.20496 11.732 8.44986 11.4871 8.75196 11.4871H11.487V7.65809Z" fill="#3B3731" />
                                </svg>
                                &nbsp;
                                &nbsp;
                                <p>Half-Day (4 hours)</p>
                            </div>
                            <div class="service-list-right">
                                <div class="price space d-flex align-items-center">
                                    <p>£25</p><span> / </span> <span>Hourly</span>
                                </div>
                            </div>
                        </div>
                        <div class="services-list d-flex align-items-center justify-content-between mt-4">
                            <div class="service-list-left d-flex align-items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" width="26" height="22" viewBox="0 0 26 22" fill="none">
                                    <path d="M10.9401 0C15.9387 0 20.1532 3.35263 21.46 7.93179C21.5815 8.35737 21.2287 8.75207 20.7861 8.75207C20.6431 8.75207 20.5181 8.65675 20.4831 8.51809C19.4037 4.25167 15.5419 1.09401 10.9401 1.09401C5.50224 1.09401 1.09401 5.50225 1.09401 10.9401C1.09401 15.8782 4.72946 19.9655 9.46984 20.6752C9.71704 20.7122 9.91199 20.9111 9.9448 21.1589C9.99053 21.5042 9.71173 21.8142 9.36693 21.7645C4.07035 21.0019 0 16.4479 0 10.9401C0 4.89804 4.89803 0 10.9401 0Z" fill="#3B3731" />
                                    <path d="M22.3843 17.0281C22.6177 16.7911 22.8748 16.5996 23.1556 16.4538C23.44 16.3079 23.7664 16.235 24.1347 16.235C24.441 16.235 24.709 16.286 24.9388 16.3881C25.1722 16.4902 25.3673 16.6343 25.5241 16.8203C25.6809 17.0026 25.7994 17.2232 25.8796 17.4821C25.9599 17.7411 26 18.0273 26 18.3409V21.8801H24.9279V18.3409C24.9279 17.9435 24.8367 17.6353 24.6543 17.4165C24.472 17.1941 24.193 17.0828 23.8174 17.0828C23.5439 17.0828 23.2868 17.1485 23.0462 17.2798C22.8091 17.4074 22.5885 17.5824 22.3843 17.8049V21.8801H21.3176V13.8063H22.3843V17.0281Z" fill="#3B3731" />
                                    <path d="M17.3947 21.1199C17.6427 21.1199 17.8633 21.0853 18.0566 21.016C18.2499 20.9467 18.414 20.8501 18.5489 20.7261C18.6838 20.5984 18.7859 20.4471 18.8552 20.2721C18.9245 20.097 18.9591 19.9056 18.9591 19.6977C18.9591 19.4424 18.9172 19.2236 18.8333 19.0413C18.7495 18.8553 18.6364 18.704 18.4942 18.5873C18.352 18.4669 18.1861 18.3794 17.9964 18.3247C17.8068 18.2664 17.6062 18.2372 17.3947 18.2372C17.1796 18.2372 16.9772 18.2664 16.7875 18.3247C16.5979 18.3794 16.432 18.4669 16.2898 18.5873C16.1476 18.704 16.0345 18.8553 15.9506 19.0413C15.8704 19.2236 15.8303 19.4424 15.8303 19.6977C15.8303 19.9056 15.8649 20.097 15.9342 20.2721C16.0035 20.4471 16.1056 20.5984 16.2405 20.7261C16.3755 20.8501 16.5396 20.9467 16.7328 21.016C16.9261 21.0853 17.1467 21.1199 17.3947 21.1199ZM17.3947 14.7583C17.1686 14.7583 16.9699 14.7929 16.7985 14.8622C16.6307 14.9278 16.4903 15.019 16.3773 15.1357C16.2642 15.2524 16.1785 15.3873 16.1202 15.5405C16.0655 15.6936 16.0382 15.8577 16.0382 16.0328C16.0382 16.2042 16.0619 16.3719 16.1093 16.536C16.1603 16.6965 16.2387 16.8405 16.3445 16.9681C16.4539 17.0921 16.5943 17.1924 16.7657 17.269C16.9371 17.3456 17.1467 17.3839 17.3947 17.3839C17.639 17.3839 17.8469 17.3456 18.0183 17.269C18.1933 17.1924 18.3337 17.0921 18.4395 16.9681C18.5489 16.8405 18.6273 16.6965 18.6747 16.536C18.7258 16.3719 18.7513 16.2042 18.7513 16.0328C18.7513 15.8577 18.7221 15.6936 18.6638 15.5405C18.6091 15.3873 18.5252 15.2524 18.4121 15.1357C18.2991 15.019 18.1569 14.9278 17.9855 14.8622C17.8177 14.7929 17.6208 14.7583 17.3947 14.7583ZM18.5708 17.7832C19.0594 17.9291 19.4296 18.1643 19.6812 18.4888C19.9365 18.8134 20.0641 19.2236 20.0641 19.7196C20.0641 20.0624 19.9985 20.3723 19.8672 20.6495C19.7395 20.9266 19.5572 21.1637 19.3202 21.3606C19.0868 21.5539 18.806 21.7034 18.4778 21.8091C18.1496 21.9149 17.7886 21.9678 17.3947 21.9678C17.0009 21.9678 16.6399 21.9149 16.3117 21.8091C15.9834 21.7034 15.7008 21.5539 15.4638 21.3606C15.2304 21.1637 15.0481 20.9266 14.9168 20.6495C14.7892 20.3723 14.7253 20.0624 14.7253 19.7196C14.7253 19.2236 14.8512 18.8134 15.1028 18.4888C15.358 18.1643 15.73 17.9291 16.2187 17.7832C15.8139 17.63 15.5094 17.4021 15.3052 17.0994C15.101 16.7931 14.9988 16.4303 14.9988 16.0109C14.9988 15.7192 15.0554 15.4475 15.1684 15.1959C15.2851 14.9406 15.4474 14.72 15.6552 14.534C15.8668 14.348 16.1202 14.2021 16.4156 14.0964C16.711 13.9906 17.0373 13.9377 17.3947 13.9377C17.7521 13.9377 18.0785 13.9906 18.3739 14.0964C18.6692 14.2021 18.9209 14.348 19.1287 14.534C19.3402 14.72 19.5025 14.9406 19.6156 15.1959C19.7322 15.4475 19.7906 15.7192 19.7906 16.0109C19.7906 16.4303 19.6885 16.7931 19.4843 17.0994C19.2801 17.4021 18.9756 17.63 18.5708 17.7832Z" fill="#3B3731" />
                                    <path d="M11.487 7.65809C11.487 7.35599 11.7319 7.11108 12.034 7.11108C12.3361 7.11108 12.581 7.35599 12.581 7.65809V12.5811H8.75196C8.44986 12.5811 8.20496 12.3362 8.20496 12.0341C8.20496 11.732 8.44986 11.4871 8.75196 11.4871H11.487V7.65809Z" fill="#3B3731" />
                                </svg>
                                &nbsp;
                                &nbsp;
                                <p>Full-Day (8 hours)</p>
                            </div>
                            <div class="service-list-right">
                                <div class="price space d-flex align-items-center">
                                    <p>£25</p><span> / </span> <span> Hourly</span>
                                </div>
                            </div>
                        </div>

                        <h2 class="section-content-heading mt-5">Add-ons</h2>

                        <div class="addons-list mt-4">
                            <div class="addons-list__row">
                                <div class="addons-list__left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="20" viewBox="0 0 26 23" fill="none" aria-hidden="true">
                                        <path d="M21.4783 4.3125C21.4783 4.43958 21.4187 4.56146 21.3127 4.65132C21.2067 4.74118 21.0629 4.79167 20.913 4.79167H17.5217C17.3718 4.79167 17.2281 4.74118 17.1221 4.65132C17.0161 4.56146 16.9565 4.43958 16.9565 4.3125C16.9565 4.18542 17.0161 4.06354 17.1221 3.97368C17.2281 3.88382 17.3718 3.83333 17.5217 3.83333H20.913C21.0629 3.83333 21.2067 3.88382 21.3127 3.97368C21.4187 4.06354 21.4783 4.18542 21.4783 4.3125ZM20.913 7.66667H17.5217C17.3718 7.66667 17.2281 7.71715 17.1221 7.80701C17.0161 7.89687 16.9565 8.01875 16.9565 8.14583C16.9565 8.27292 17.0161 8.39479 17.1221 8.48466C17.2281 8.57452 17.3718 8.625 17.5217 8.625H20.913C21.0629 8.625 21.2067 8.57452 21.3127 8.48466C21.4187 8.39479 21.4783 8.27292 21.4783 8.14583C21.4783 8.01875 21.4187 7.89687 21.3127 7.80701C21.2067 7.71715 21.0629 7.66667 20.913 7.66667ZM5.08696 4.79167H8.47826C8.62817 4.79167 8.77193 4.74118 8.87793 4.65132C8.98393 4.56146 9.04348 4.43958 9.04348 4.3125C9.04348 4.18542 8.98393 4.06354 8.87793 3.97368C8.77193 3.88382 8.62817 3.83333 8.47826 3.83333H5.08696C4.93705 3.83333 4.79329 3.88382 4.68729 3.97368C4.58129 4.06354 4.52174 4.18542 4.52174 4.3125C4.52174 4.43958 4.58129 4.56146 4.68729 4.65132C4.79329 4.74118 4.93705 4.79167 5.08696 4.79167ZM8.47826 7.66667H5.08696C4.93705 7.66667 4.79329 7.71715 4.68729 7.80701C4.58129 7.89687 4.52174 8.01875 4.52174 8.14583C4.52174 8.27292 4.58129 8.39479 4.68729 8.48466C4.79329 8.57452 4.93705 8.625 5.08696 8.625H8.47826C8.62817 8.625 8.77193 8.57452 8.87793 8.48466C8.98393 8.39479 9.04348 8.27292 9.04348 8.14583C9.04348 8.01875 8.98393 7.89687 8.87793 7.80701C8.77193 7.71715 8.62817 7.66667 8.47826 7.66667ZM26 1.4375V22.5208C26 22.6479 25.9405 22.7698 25.8345 22.8597C25.7285 22.9495 25.5847 23 25.4348 23C25.2849 23 25.1411 22.9495 25.0351 22.8597C24.9291 22.7698 24.8696 22.6479 24.8696 22.5208V20.125H13.5652V22.5208C13.5652 22.6479 13.5057 22.7698 13.3997 22.8597C13.2937 22.9495 13.1499 23 13 23C12.8501 23 12.7063 22.9495 12.6003 22.8597C12.4943 22.7698 12.4348 22.6479 12.4348 22.5208V20.125H1.13043V22.5208C1.13043 22.6479 1.07089 22.7698 0.964886 22.8597C0.858887 22.9495 0.715122 23 0.565217 23C0.415312 23 0.271547 22.9495 0.165548 22.8597C0.0595496 22.7698 0 22.6479 0 22.5208V1.4375C0 1.05625 0.178648 0.690617 0.496645 0.421034C0.814641 0.15145 1.24594 0 1.69565 0H24.3043C24.7541 0 25.1854 0.15145 25.5034 0.421034C25.8214 0.690617 26 1.05625 26 1.4375ZM12.4348 19.1667V0.958333H1.69565C1.54575 0.958333 1.40198 1.00882 1.29598 1.09868C1.18998 1.18854 1.13043 1.31042 1.13043 1.4375V19.1667H12.4348ZM13.5652 19.1667H24.8696V1.4375C24.8696 1.31042 24.81 1.18854 24.704 1.09868C24.598 1.00882 24.4543 0.958333 24.3043 0.958333H13.5652V19.1667Z" fill="#9D9B98" />
                                    </svg>
                                    <span class="addons-list__name">Storage Locker</span>
                                </div>
                                <span class="addons-list__price">+ £5 / day</span>
                            </div>
                            <div class="addons-list__row">
                                <div class="addons-list__left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 26 26" fill="none" aria-hidden="true">
                                        <path d="M25.1356 0.157339C25.3249 -0.0437372 25.6416 -0.0533737 25.8427 0.135855C26.0437 0.325102 26.0534 0.641795 25.8641 0.842883L15.0369 12.3448C15.4974 12.8008 15.8389 13.1434 16.0466 13.5655L16.1326 13.7676L16.1336 13.7686C16.3358 14.3217 16.2842 14.8207 16.2214 15.5528C15.911 19.1627 14.6773 21.7425 13.5105 23.4248C12.9279 24.2648 12.363 24.8796 11.9401 25.2871C11.7286 25.4909 11.5519 25.6434 11.4265 25.7461C11.3639 25.7973 11.3135 25.8364 11.278 25.8633C11.2604 25.8766 11.2462 25.8871 11.236 25.8945L11.2204 25.9062L11.2184 25.9072V25.9082L10.9284 25.5L11.2175 25.9082C11.0989 25.9921 10.9493 26.0203 10.8083 25.9853C7.96118 25.2801 5.42094 24.0342 3.50741 21.7793C1.59497 19.5256 0.354279 16.3173 0.00150955 11.7725C-0.0118332 11.6006 0.0639331 11.4335 0.202684 11.3311C0.341519 11.2287 0.523768 11.2051 0.684135 11.2686C3.33074 12.3176 5.95486 12.5365 9.27311 11.2628C10.0564 10.9625 10.6744 10.7254 11.1716 10.5821C11.6674 10.4392 12.1115 10.368 12.5349 10.4551H12.5378C12.9508 10.5422 13.3021 10.7542 13.6628 11.0469C13.8642 11.2104 14.0808 11.4114 14.323 11.6446L25.1356 0.157339ZM12.3337 11.4346C12.1504 11.3969 11.8953 11.4141 11.4479 11.543C11.2249 11.6073 10.9708 11.6939 10.6725 11.8028L9.63152 12.1963C6.37382 13.4468 3.68812 13.3588 1.06891 12.4727C1.48508 16.4533 2.63665 19.2069 4.27012 21.1319C5.95651 23.1191 8.19967 24.2721 10.8141 24.9531C10.9192 24.8662 11.0678 24.7389 11.2458 24.5674C11.6274 24.1996 12.1487 23.6337 12.6892 22.8545C13.7684 21.2983 14.9316 18.8829 15.2253 15.4668C15.293 14.6779 15.3052 14.418 15.1941 14.1133C15.1105 13.887 14.9712 13.7049 14.6326 13.3565L14.2214 12.9444C13.7031 12.4317 13.3404 12.0729 13.0329 11.8233C12.733 11.5798 12.5267 11.4747 12.3337 11.4337V11.4346Z" fill="#9D9B98" />
                                        <path d="M9.22351 16.7723C9.44201 16.6036 9.75589 16.6437 9.92469 16.8621C10.0934 17.0806 10.0533 17.3945 9.83484 17.5633C8.01569 18.9676 6.19097 19.4966 4.81818 19.6726C4.13281 19.7605 3.55961 19.7606 3.15409 19.7381C2.95117 19.7268 2.7894 19.7107 2.67655 19.6961C2.6203 19.6888 2.57612 19.6816 2.54471 19.6765C2.529 19.674 2.51578 19.6713 2.50663 19.6697C2.50228 19.6689 2.49867 19.6683 2.49588 19.6678C2.49445 19.6675 2.49299 19.6679 2.49198 19.6678L2.491 19.6668H2.49002C2.21891 19.6143 2.04105 19.352 2.09353 19.0808C2.14607 18.8099 2.40851 18.633 2.67948 18.6853H2.68241C2.68661 18.6861 2.69431 18.6875 2.70487 18.6892C2.72595 18.6927 2.7594 18.6981 2.80448 18.7039C2.89508 18.7156 3.03291 18.7302 3.20976 18.74C3.56369 18.7597 4.0751 18.7605 4.69122 18.6814C5.92256 18.5235 7.57049 18.0483 9.22351 16.7723Z" fill="#9D9B98" />
                                        <path d="M7.63969 6.40392C7.63969 6.07619 7.50907 5.76169 7.27738 5.5299C7.04562 5.29814 6.73111 5.16766 6.40335 5.1676C6.07551 5.1676 5.76113 5.29809 5.52931 5.5299C5.2975 5.76172 5.167 6.07608 5.167 6.40392C5.16706 6.73168 5.29755 7.04618 5.52931 7.27794C5.76111 7.50962 6.07561 7.64024 6.40335 7.64024C6.73111 7.64019 7.04562 7.5097 7.27738 7.27794C7.50915 7.04618 7.63964 6.73168 7.63969 6.40392ZM8.63971 6.40392C8.63965 6.99689 8.40373 7.56567 7.98442 7.98497C7.56512 8.40427 6.99633 8.64018 6.40335 8.64024C5.81039 8.64024 5.24161 8.40418 4.82227 7.98497C4.40297 7.56567 4.16705 6.99689 4.16699 6.40392C4.16699 5.81087 4.40292 5.24222 4.82227 4.82287C5.24163 4.40352 5.81029 4.1676 6.40335 4.1676C6.99633 4.16766 7.56512 4.40357 7.98442 4.82287C8.40365 5.2422 8.63971 5.81097 8.63971 6.40392Z" fill="#9D9B98" />
                                        <path d="M11.1115 2.02771V1.88904C11.1115 1.61289 11.3353 1.38904 11.6115 1.38904C11.8876 1.38904 12.1115 1.61289 12.1115 1.88904V2.02771C12.1115 2.30385 11.8876 2.5277 11.6115 2.5277C11.3353 2.5277 11.1115 2.30385 11.1115 2.02771Z" fill="#9D9B98" />
                                    </svg>
                                    <span class="addons-list__name">Deep Clean</span>
                                </div>
                                <span class="addons-list__price">+ £10</span>
                            </div>
                            <div class="addons-list__row">
                                <div class="addons-list__left">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 26 26" fill="none" aria-hidden="true">
                                        <path d="M25.3158 24.6316C25.6937 24.6316 26 24.9379 26 25.3158C26 25.6937 25.6937 26 25.3158 26H0.684211C0.306331 26 0 25.6937 0 25.3158C0 24.9379 0.306331 24.6316 0.684211 24.6316H25.3158ZM2.05263 19.1579C2.43051 19.1579 2.73684 19.4642 2.73684 19.8421C2.73684 20.22 2.43051 20.5263 2.05263 20.5263H0.684211C0.306331 20.5263 0 20.22 0 19.8421C0 19.4642 0.306331 19.1579 0.684211 19.1579H2.05263ZM17.7895 19.8421C17.7895 18.5719 17.2845 17.354 16.3863 16.4558C15.4881 15.5576 14.2702 15.0526 13 15.0526C11.7298 15.0526 10.5119 15.5576 9.61369 16.4558C8.71549 17.354 8.21053 18.5719 8.21053 19.8421C8.21053 20.22 7.90419 20.5263 7.52632 20.5263C7.14844 20.5263 6.84211 20.22 6.84211 19.8421C6.84211 18.2089 7.49135 16.6431 8.64618 15.4883C9.80101 14.3335 11.3668 13.6842 13 13.6842C14.6332 13.6842 16.199 14.3335 17.3538 15.4883C18.5087 16.6431 19.1579 18.2089 19.1579 19.8421C19.1579 20.22 18.8516 20.5263 18.4737 20.5263C18.0958 20.5263 17.7895 20.22 17.7895 19.8421ZM25.3158 19.1579C25.6937 19.1579 26 19.4642 26 19.8421C26 20.22 25.6937 20.5263 25.3158 20.5263H23.9474C23.5695 20.5263 23.2632 20.22 23.2632 19.8421C23.2632 19.4642 23.5695 19.1579 23.9474 19.1579H25.3158ZM3.75781 10.5999C4.02484 10.3329 4.45809 10.3333 4.72533 10.5999L5.68349 11.5581C5.95069 11.8253 5.95069 12.2584 5.68349 12.5256C5.41629 12.7928 4.98318 12.7928 4.71597 12.5256L3.75781 11.5674C3.49115 11.3002 3.49079 10.8669 3.75781 10.5999ZM21.2747 10.5999C21.5419 10.3333 21.9752 10.3329 22.2422 10.5999C22.5092 10.8669 22.5089 11.3002 22.2422 11.5674L21.284 12.5256C21.0168 12.7928 20.5837 12.7928 20.3165 12.5256C20.0493 12.2584 20.0493 11.8253 20.3165 11.5581L21.2747 10.5999ZM12.3158 0.684211C12.3158 0.306331 12.6221 0 13 0C13.3779 0 13.6842 0.306331 13.6842 0.684211V7.24301L16.6215 4.30572C16.8887 4.03851 17.3218 4.03851 17.589 4.30572C17.8562 4.57292 17.8562 5.00603 17.589 5.27323L13.4838 9.3785C13.2166 9.6457 12.7834 9.6457 12.5162 9.3785L8.41098 5.27323C8.14378 5.00603 8.14378 4.57292 8.41098 4.30572C8.67818 4.03851 9.11129 4.03851 9.3785 4.30572L12.3158 7.24301V0.684211Z" fill="#9D9B98" />
                                    </svg>
                                    <span class="addons-list__name">After-hours access</span>
                                </div>
                                <span class="addons-list__price">+ £20</span>
                            </div>
                        </div>

                    </div>

                    <div id="amenities" class="mt-5">
                        <h2 class="section-content-heading">Amenities Included</h2>
                        <div class="amenities-options d-flex align-items-center mt-4 flex-wrap gap-10">
                            <div class="selected-item dark-bg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                    <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Grooming Table
                            </div>
                            <div class="selected-item dark-bg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                    <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Bath
                            </div>
                            <div class="selected-item dark-bg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                    <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Dryer
                            </div>
                            <div class="selected-item dark-bg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                    <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Towels
                            </div>
                            <div class="selected-item dark-bg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                    <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Waiting area
                            </div>
                            <div class="selected-item dark-bg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                    <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Parking
                            </div>
                            <div class="selected-item dark-bg">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="9" viewBox="0 0 12 9" fill="none">
                                    <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                Wi-Fi
                            </div>
                        </div>
                    </div>


                    <div id="before_you_book" class="mt-5">
                        <h2 class="section-content-heading">Before you book</h2>

                        <h2 class="section-content-heading mt-3">Service Suitability</h2>

                        <div class="add-on-services d-flex align-items-center mt-4 flex-wrap">
                            <div class="selected-item">
                                Full Groom
                            </div>
                            <div class="selected-item">
                                Bath & Brush
                            </div>
                            <div class="selected-item">
                                Nail Trim
                            </div>
                            <div class="selected-item">
                                Ear Cleaning
                            </div>
                            <div class="selected-item">
                                Tear-Stain Treatment
                            </div>
                            <div class="selected-item">
                                Deshedding
                            </div>
                            <div class="selected-item">
                                Dematting
                            </div>
                            <div class="selected-item">
                                Medicated / Sensitive Skin Bath
                            </div>
                            <div class="selected-item">
                                Sanitary Trim
                            </div>
                            <div class="selected-item">
                                Paw Pad Trim
                            </div>
                            <div class="selected-item">
                                Teeth Brushing
                            </div>
                            <div class="selected-item">
                                Anal Gland Expression
                            </div>
                            <div class="selected-item">
                                Paw Balm
                            </div>
                            <div class="selected-item">
                                Perfume
                            </div>
                            <div class="selected-item">
                                Bandana / Bow
                            </div>
                        </div>

                        <h2 class="section-content-heading mt-5">Rules & Restrictions</h2>

                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M10 0C4.47719 0 0 4.47719 0 10C0 15.5231 4.47719 20 10 20C15.5231 20 20 15.5231 20 10C20 4.47719 15.5231 0 10 0ZM10 18.7697C5.17531 18.7697 1.25 14.8247 1.25 9.99996C1.25 5.17527 5.17531 1.24996 10 1.24996C14.8247 1.24996 18.75 5.17529 18.75 9.99996C18.75 14.8246 14.8247 18.7697 10 18.7697Z" fill="#3B3731" />
                                <path d="M5.5 10.5L8.5 13.5L14.5 7.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>Suitable for all breeds and coat types</p>
                        </div>
                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M10 0C4.47719 0 0 4.47719 0 10C0 15.5231 4.47719 20 10 20C15.5231 20 20 15.5231 20 10C20 4.47719 15.5231 0 10 0ZM10 18.7697C5.17531 18.7697 1.25 14.8247 1.25 9.99996C1.25 5.17527 5.17531 1.24996 10 1.24996C14.8247 1.24996 18.75 5.17529 18.75 9.99996C18.75 14.8246 14.8247 18.7697 10 18.7697Z" fill="#3B3731" />
                                <path d="M5.5 10.5L8.5 13.5L14.5 7.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>Leave space tidy</p>
                        </div>
                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M6.11099 13.8891C6.01726 13.7953 5.9646 13.6681 5.9646 13.5356C5.9646 13.403 6.01726 13.2758 6.11099 13.1821L9.29299 10.0001L6.11099 6.81806C6.01991 6.72376 5.96952 6.59746 5.97066 6.46636C5.9718 6.33526 6.02438 6.20985 6.11708 6.11715C6.20979 6.02445 6.33519 5.97186 6.46629 5.97072C6.59739 5.96958 6.72369 6.01998 6.81799 6.11106L9.99999 9.29306L13.182 6.11106C13.2763 6.01998 13.4026 5.96958 13.5337 5.97072C13.6648 5.97186 13.7902 6.02445 13.8829 6.11715C13.9756 6.20985 14.0282 6.33526 14.0293 6.46636C14.0305 6.59746 13.9801 6.72376 13.889 6.81806L10.707 10.0001L13.889 13.1821C13.9801 13.2764 14.0305 13.4027 14.0293 13.5338C14.0282 13.6649 13.9756 13.7903 13.8829 13.883C13.7902 13.9757 13.6648 14.0283 13.5337 14.0294C13.4026 14.0305 13.2763 13.9801 13.182 13.8891L9.99999 10.7071L6.81799 13.8891C6.72423 13.9828 6.59708 14.0355 6.46449 14.0355C6.33191 14.0355 6.20476 13.9828 6.11099 13.8891Z" fill="#3B3731" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 20C15.523 20 20 15.523 20 10C20 4.477 15.523 0 10 0C4.477 0 0 4.477 0 10C0 15.523 4.477 20 10 20ZM10 19C14.9705 19 19 14.9705 19 10C19 5.0295 14.9705 1 10 1C5.0295 1 1 5.0295 1 10C1 14.9705 5.0295 19 10 19Z" fill="#3B3731" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>Not suitable for aggressive animals in heat</p>
                        </div>
                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M6.11099 13.8891C6.01726 13.7953 5.9646 13.6681 5.9646 13.5356C5.9646 13.403 6.01726 13.2758 6.11099 13.1821L9.29299 10.0001L6.11099 6.81806C6.01991 6.72376 5.96952 6.59746 5.97066 6.46636C5.9718 6.33526 6.02438 6.20985 6.11708 6.11715C6.20979 6.02445 6.33519 5.97186 6.46629 5.97072C6.59739 5.96958 6.72369 6.01998 6.81799 6.11106L9.99999 9.29306L13.182 6.11106C13.2763 6.01998 13.4026 5.96958 13.5337 5.97072C13.6648 5.97186 13.7902 6.02445 13.8829 6.11715C13.9756 6.20985 14.0282 6.33526 14.0293 6.46636C14.0305 6.59746 13.9801 6.72376 13.889 6.81806L10.707 10.0001L13.889 13.1821C13.9801 13.2764 14.0305 13.4027 14.0293 13.5338C14.0282 13.6649 13.9756 13.7903 13.8829 13.883C13.7902 13.9757 13.6648 14.0283 13.5337 14.0294C13.4026 14.0305 13.2763 13.9801 13.182 13.8891L9.99999 10.7071L6.81799 13.8891C6.72423 13.9828 6.59708 14.0355 6.46449 14.0355C6.33191 14.0355 6.20476 13.9828 6.11099 13.8891Z" fill="#3B3731" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 20C15.523 20 20 15.523 20 10C20 4.477 15.523 0 10 0C4.477 0 0 4.477 0 10C0 15.523 4.477 20 10 20ZM10 19C14.9705 19 19 14.9705 19 10C19 5.0295 14.9705 1 10 1C5.0295 1 1 5.0295 1 10C1 14.9705 5.0295 19 10 19Z" fill="#3B3731" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>No overnight stays</p>
                        </div>
                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M6.11099 13.8891C6.01726 13.7953 5.9646 13.6681 5.9646 13.5356C5.9646 13.403 6.01726 13.2758 6.11099 13.1821L9.29299 10.0001L6.11099 6.81806C6.01991 6.72376 5.96952 6.59746 5.97066 6.46636C5.9718 6.33526 6.02438 6.20985 6.11708 6.11715C6.20979 6.02445 6.33519 5.97186 6.46629 5.97072C6.59739 5.96958 6.72369 6.01998 6.81799 6.11106L9.99999 9.29306L13.182 6.11106C13.2763 6.01998 13.4026 5.96958 13.5337 5.97072C13.6648 5.97186 13.7902 6.02445 13.8829 6.11715C13.9756 6.20985 14.0282 6.33526 14.0293 6.46636C14.0305 6.59746 13.9801 6.72376 13.889 6.81806L10.707 10.0001L13.889 13.1821C13.9801 13.2764 14.0305 13.4027 14.0293 13.5338C14.0282 13.6649 13.9756 13.7903 13.8829 13.883C13.7902 13.9757 13.6648 14.0283 13.5337 14.0294C13.4026 14.0305 13.2763 13.9801 13.182 13.8891L9.99999 10.7071L6.81799 13.8891C6.72423 13.9828 6.59708 14.0355 6.46449 14.0355C6.33191 14.0355 6.20476 13.9828 6.11099 13.8891Z" fill="#3B3731" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 20C15.523 20 20 15.523 20 10C20 4.477 15.523 0 10 0C4.477 0 0 4.477 0 10C0 15.523 4.477 20 10 20ZM10 19C14.9705 19 19 14.9705 19 10C19 5.0295 14.9705 1 10 1C5.0295 1 1 5.0295 1 10C1 14.9705 5.0295 19 10 19Z" fill="#3B3731" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>Pets must remain supervised</p>
                        </div>
                        <div class="preferences-list d-flex align-items-center mt-4">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                                <path d="M6.11099 13.8891C6.01726 13.7953 5.9646 13.6681 5.9646 13.5356C5.9646 13.403 6.01726 13.2758 6.11099 13.1821L9.29299 10.0001L6.11099 6.81806C6.01991 6.72376 5.96952 6.59746 5.97066 6.46636C5.9718 6.33526 6.02438 6.20985 6.11708 6.11715C6.20979 6.02445 6.33519 5.97186 6.46629 5.97072C6.59739 5.96958 6.72369 6.01998 6.81799 6.11106L9.99999 9.29306L13.182 6.11106C13.2763 6.01998 13.4026 5.96958 13.5337 5.97072C13.6648 5.97186 13.7902 6.02445 13.8829 6.11715C13.9756 6.20985 14.0282 6.33526 14.0293 6.46636C14.0305 6.59746 13.9801 6.72376 13.889 6.81806L10.707 10.0001L13.889 13.1821C13.9801 13.2764 14.0305 13.4027 14.0293 13.5338C14.0282 13.6649 13.9756 13.7903 13.8829 13.883C13.7902 13.9757 13.6648 14.0283 13.5337 14.0294C13.4026 14.0305 13.2763 13.9801 13.182 13.8891L9.99999 10.7071L6.81799 13.8891C6.72423 13.9828 6.59708 14.0355 6.46449 14.0355C6.33191 14.0355 6.20476 13.9828 6.11099 13.8891Z" fill="#3B3731" />
                                <path fill-rule="evenodd" clip-rule="evenodd" d="M10 20C15.523 20 20 15.523 20 10C20 4.477 15.523 0 10 0C4.477 0 0 4.477 0 10C0 15.523 4.477 20 10 20ZM10 19C14.9705 19 19 14.9705 19 10C19 5.0295 14.9705 1 10 1C5.0295 1 1 5.0295 1 10C1 14.9705 5.0295 19 10 19Z" fill="#3B3731" />
                            </svg>
                            &nbsp;
                            &nbsp;
                            <p>No smoking</p>
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
                                <p>Located near Victoria Embankment</p>
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
            <div class="col-lg-4">
                <div id="booking-sidebar" class="service-card mt-4 mb-5">
                    <div class="service__header">
                        <div class="message-btn-div d-flex justify-content-center">
                            <a href="{{ url('/messages_notification/messages') }}" class="message-btn mt-3">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="16" viewBox="0 0 19 16" fill="none">
                                    <path d="M0.75 7.75C0.75 4.45038 0.75 2.80012 1.7755 1.7755C2.801 0.750875 4.45038 0.75 7.75 0.75H11.25C14.5496 0.75 16.1999 0.75 17.2245 1.7755C18.2491 2.801 18.25 4.45038 18.25 7.75C18.25 11.0496 18.25 12.6999 17.2245 13.7245C16.199 14.7491 14.5496 14.75 11.25 14.75H7.75C4.45038 14.75 2.80012 14.75 1.7755 13.7245C0.750875 12.699 0.75 11.0496 0.75 7.75Z" stroke="#FFA899" stroke-width="1.5" />
                                    <path d="M4.25 4.25L6.13913 5.825C7.7465 7.16375 8.54975 7.83313 9.5 7.83313C10.4502 7.83313 11.2544 7.16375 12.8609 5.82412L14.75 4.25" stroke="#FFA899" stroke-width="1.5" stroke-linecap="round" />
                                </svg>
                                Message Space Host
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
                            <span class="booking-starting-price__amount">£25</span>
                            <span class="booking-starting-price__label">starting price</span>
                        </div>

                        <div class="service-type-select booking-step mt-4">
                            <p class="booking-step__label">
                                <span class="booking-step__num">1</span>
                                Choose a service type
                            </p>
                            <div class="custom-select" data-singleselect data-summary="service" data-placeholder="Select service type">
                                <div class="select-trigger">
                                    <span class="selected-text">Select service type</span>
                                    <span class="select-trigger-meta">
                                        <span class="selected-price"></span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none">
                                            <path d="M13.8737 0.5L7.13022 7.24344L0.499976 0.613201" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </div>
                                <ul class="select-options">
                                    <li data-value="hourly" data-hours="1" data-label="Hourly" data-price="£25" data-amount="25">
                                        <span class="option-label">Hourly</span>
                                        <span class="option-price">£25</span>
                                    </li>
                                    <li data-value="half-day" data-hours="4" data-label="Half-Day (4 hours)" data-price="£25" data-amount="25">
                                        <span class="option-label">Half-Day (4 hours)</span>
                                        <span class="option-price">£25</span>
                                    </li>
                                    <li data-value="full-day" data-hours="8" data-label="Full-Day (8 hours)" data-price="£25" data-amount="25">
                                        <span class="option-label">Full-Day (8 hours)</span>
                                        <span class="option-price">£25</span>
                                    </li>
                                </ul>
                                <input type="hidden" name="serviceType">
                            </div>
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
                                    <li data-value="storage-locker" data-label="Storage Locker" data-price="+£5" data-amount="5">
                                        <span class="option-label">Storage Locker</span>
                                        <span class="option-price">+£5</span>
                                    </li>
                                    <li data-value="deep-clean" data-label="Deep Clean" data-price="+£10" data-amount="10">
                                        <span class="option-label">Deep Clean</span>
                                        <span class="option-price">+£10</span>
                                    </li>
                                    <li data-value="after-hours" data-label="After-hours access" data-price="+£20" data-amount="20">
                                        <span class="option-label">After-hours access</span>
                                        <span class="option-price">+£20</span>
                                    </li>
                                </ul>
                                <input type="hidden" name="addons">
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

                                <div class="weekdays mt-4">
                                    <div>M</div>
                                    <div>T</div>
                                    <div>W</div>
                                    <div>T</div>
                                    <div>F</div>
                                    <div>S</div>
                                    <div>S</div>
                                </div>

                                <div class="dates mt-4">
                                    <div></div>
                                    <div></div>
                                    <div class="date">1</div>
                                    <div class="date">2</div>
                                    <div class="date">3</div>
                                    <div class="date">4</div>

                                    <div class="date">5</div>
                                    <div class="date">6</div>
                                    <div class="date">7</div>
                                    <div class="date">8</div>
                                    <div class="date">9</div>
                                    <div class="date">10</div>
                                    <div class="date">11</div>

                                    <div class="date">12</div>
                                    <div class="date">13</div>
                                    <div class="date available selected">14</div>
                                    <div class="date">15</div>
                                    <div class="date">16</div>
                                    <div class="date">17</div>
                                    <div class="date">18</div>

                                    <div class="date">19</div>
                                    <div class="date">20</div>
                                    <div class="date">21</div>
                                    <div class="date">22</div>
                                    <div class="date">23</div>
                                    <div class="date">24</div>
                                    <div class="date">25</div>

                                    <div class="date">26</div>
                                    <div class="date">27</div>
                                    <div class="date">28</div>
                                    <div class="date">29</div>
                                    <div class="date">30</div>
                                    <div class="date">31</div>
                                </div>
                            </div>
                        </div>

                        <div class="booking-step mt-5">
                            <div class="booking-step__label-row">
                                <p class="booking-step__label">
                                    <span class="booking-step__num">4</span>
                                    Pick a time
                                </p>
                                <span class="booking-step__time-range" data-selected-time-range>09:00 AM - 10:00 AM</span>
                            </div>
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
                                <button type="button" class="book-btn booking-cta-btn" data-modal-open="space_prompt">Book for Oct 14, 09:00-10:00</button>
                            </div>

                            <p class="booking-cancel-note">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                                    <path d="M3.125 5L4.375 6.25L6.875 3.75" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                    <circle cx="5" cy="5" r="4.5" stroke="#9D9B98" />
                                </svg>
                                Cancellations within 24 hours of your booking start may incur a one-hour charge.
                            </p>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <!-- SPACE PROMPT MODAL -->
    <div class="modal" id="space_prompt">
        <div class="modal-content">
            <div class="groomer-cross-svg cursor d-flex justify-content-end" data-modal-close>
                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                    <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                    <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                </svg>
            </div>
            <div class="d-flex flex-column align-items-center justify-content-center">
                <div class="svg-wrapper">
                    <svg xmlns="http://www.w3.org/2000/svg" width="76" height="50" viewBox="0 0 76 50" fill="none">
                        <rect x="11.8813" width="50.0001" height="17.8218" rx="8.91091" fill="#FFC97A" />
                        <rect y="17.3271" width="50.0001" height="17.3268" rx="8.66339" fill="#FFC97A" />
                        <rect x="25.2476" y="32.1782" width="50.0001" height="17.8218" rx="8.91091" fill="#FFC97A" />
                    </svg>
                    <svg class="space-modal-svg" xmlns="http://www.w3.org/2000/svg" width="74" height="52" viewBox="0 0 74 52" fill="none">
                        <path d="M27.5 27.5448C24.2602 27.5448 21.4854 26.3909 19.1754 24.0831C16.8654 21.7754 15.7117 19.0045 15.7143 15.7705C15.7169 12.5365 16.8706 9.76298 19.1754 7.44998C21.4801 5.13699 24.255 3.98703 27.5 4.00011C30.745 4.01319 33.5199 5.16446 35.8246 7.45391C38.1294 9.74335 39.2831 12.5169 39.2857 15.7744C39.2883 19.032 38.1346 21.8029 35.8246 24.0871C33.5146 26.3713 30.7398 27.5212 27.5 27.5448ZM0 47.9261V45.5712C0 43.9045 0.480596 42.3726 1.44179 40.9753C2.40298 39.5781 3.66536 38.4883 5.22893 37.706C8.75417 36.0445 12.3815 34.7415 16.1111 33.7969C19.8406 32.8524 23.6421 32.3788 27.5157 32.3762C31.3893 32.3736 35.1856 32.8471 38.9046 33.7969C42.6237 34.7467 46.2458 36.0498 49.7711 37.706C51.3346 38.4857 52.597 39.5755 53.5582 40.9753C54.5194 42.3752 55 43.9058 55 45.5673V47.9222C55 49.0734 54.6085 50.0415 53.8254 50.8265C53.0423 51.6088 52.0732 52 50.9182 52H4.08571C2.93071 52 1.96167 51.6075 1.17857 50.8226C0.392858 50.0402 0 49.0774 0 47.9261ZM27.5 23.62C29.6607 23.62 31.5111 22.8508 33.0511 21.3123C34.5911 19.7737 35.3598 17.9265 35.3571 15.7705C35.3545 13.6145 34.5858 11.7659 33.0511 10.2248C31.5163 8.68367 29.666 7.91572 27.5 7.92095C25.334 7.92619 23.485 8.69413 21.9529 10.2248C20.4207 11.7555 19.6507 13.604 19.6429 15.7705C19.635 17.937 20.405 19.7842 21.9529 21.3123C23.5007 22.8403 25.3498 23.6096 27.5 23.62ZM40.9475 38.3654V48.0752H51.0714V45.5359C51.0714 44.5652 50.7571 43.7109 50.1286 42.973C49.5 42.2352 48.7562 41.6255 47.8971 41.1441C46.7788 40.5685 45.6369 40.0569 44.4714 39.6095C43.306 39.1621 42.1313 38.7474 40.9475 38.3654ZM17.9811 37.3645V42.1881H37.0189V37.3606C35.458 36.9995 33.8774 36.7327 32.2771 36.56C30.6795 36.3873 29.0819 36.3009 27.4843 36.3009C25.8814 36.3009 24.2877 36.3873 22.7032 36.56C21.1187 36.7327 19.5446 37.0009 17.9811 37.3645ZM3.92857 48.0752H14.0525V38.3654C12.8687 38.7448 11.694 39.1595 10.5286 39.6095C9.36309 40.0596 8.22119 40.5711 7.10286 41.1441C6.24643 41.6229 5.50393 42.2326 4.87536 42.973C4.24417 43.7109 3.92857 44.5652 3.92857 45.5359V48.0752Z" fill="#3B3731" />
                        <path d="M69.5056 12.1945L65.0523 10.0763C64.2465 9.69593 63.7499 10.8177 64.5276 11.1796L68.9809 13.2977L68.7116 13.8639L64.2583 11.7457C63.4612 11.3586 62.9642 12.4795 63.7335 12.849L68.1868 14.9672L67.9116 15.5457L63.4584 13.4276C62.6582 13.047 62.1615 14.1672 62.9336 14.5308L67.3869 16.649L67.1246 17.2003L62.6713 15.0822C61.8853 14.7148 61.3714 15.8283 62.1404 16.1985L66.5937 18.3166L66.3309 18.8692L61.8776 16.751C61.0952 16.376 60.5661 17.4801 61.3528 17.8543L65.8061 19.9724L65.5439 20.5238L61.0906 18.4056C60.3059 18.0353 59.7763 19.1406 60.5655 19.5095L65.0188 21.6277L64.7501 22.1926L60.2968 20.0745C59.5164 19.6953 58.9873 20.7994 59.7721 21.1777L64.2253 23.2959L63.9637 23.8461L59.5104 21.7279C58.7271 21.3546 58.2129 22.4687 58.9853 22.8318L63.4386 24.9499L58.3291 35.6923C57.5531 37.3304 60.2599 38.5816 61.0321 36.9779L72.9891 11.8391C73.3371 11.0877 73.1217 9.82603 72.0289 9.3026L66.6396 6.73924C65.8346 6.35709 65.338 7.47884 66.1148 7.84252L70.5616 9.97428L70.3 10.5244L65.8467 8.40629C65.045 8.02061 64.5486 9.14176 65.3216 9.51016L69.7749 11.6283L69.5056 12.1945ZM55.6875 28.9757C56.5938 27.0637 55.4263 23.9884 52.2306 24.1005L55.7067 16.7922L59.7823 3.9506C59.8993 3.61529 59.6423 3.4009 59.4481 3.30202C59.2425 3.21075 58.8 3.09238 58.6041 3.39021L51.222 14.6591L47.7459 21.9674C45.8163 19.4175 42.7569 20.4983 41.8606 22.381C41.0644 24.0566 41.8066 26.0969 43.6985 26.9917C45.6006 27.9015 47.7039 27.0108 48.4217 25.5017L52.0326 17.91L52.6732 18.2147L49.0624 25.8064C48.2587 27.4959 49.0811 29.4206 50.6181 30.2489C50.1512 31.1628 49.85 32.6401 50.217 33.4573C50.6471 34.4163 51.8817 34.0388 51.8335 33.2302C51.7762 32.4718 51.6098 31.9595 52.5007 30.7867C53.8667 30.8416 55.1109 30.1813 55.6875 28.9757ZM44.4989 25.4972C44.0669 25.2917 43.7279 24.9362 43.5566 24.5089C43.3853 24.0815 43.3957 23.6173 43.5854 23.2184C43.7751 22.8195 44.1287 22.5185 44.5683 22.3818C45.008 22.245 45.4977 22.2836 45.9297 22.4891C46.8154 22.9162 47.2188 23.9328 46.8316 24.7635C46.6414 25.1594 46.29 25.4583 45.8535 25.5957C45.4169 25.733 44.9302 25.6976 44.4989 25.4972ZM50.705 26.6059C50.8953 26.2097 51.2468 25.9105 51.6836 25.7729C52.1204 25.6353 52.6074 25.6704 53.0392 25.8706C53.4672 26.079 53.8019 26.4346 53.9707 26.8603C54.1395 27.286 54.1289 27.7476 53.9411 28.145C53.7516 28.5416 53.4003 28.8411 52.9635 28.9785C52.5266 29.1159 52.0396 29.0801 51.6084 28.8788C51.1805 28.6707 50.8458 28.3155 50.6768 27.8901C50.5077 27.4647 50.5178 27.0033 50.705 26.6059Z" fill="#3B3731" />
                    </svg>
                </div>
                <h1 class="large-font text-center mt-3" style="line-height: normal;">Do you have a groomer?</h1>
                <p class="normal-light-color">Tell us how you’d like to continue.</p>
            </div>

            <div class="d-flex flex-column align-items-center justify-content-center">
                <div class="groomer-find-card cursor d-flex flex-column justify-content-center mt-4">
                    <p class="medium-font-m-bold">Find a groomer for me</p>
                    <p class="normal-light-color-font">We’ll match you with a trusted professional.</p>
                </div>

                <div class="service-type-select mt-4" id="find-groomer-for-me" style="display:none;">
                    <p class="label">Main Service Type</p>
                    <div class="custom-select">
                        <div class="select-trigger full-width">
                            <span class="selected-text">Full Grooming, Pet Spa ...</span>
                            <svg width="16" height="16" viewBox="0 0 24 24">
                                <path d="M6 9l6 6 6-6" fill="none" stroke="#666" stroke-width="2" />
                            </svg>
                        </div>

                        <ul class="select-options">
                            <li data-value="full-groom">Full Groom</li>
                            <li data-value="bath-and-brush" class="disabled">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>Bath & Brush</span>
                                    <span class="disabled-text">Not available at this space</span>
                                </div>
                            </li>
                            <li data-value="medicated-bath">Medicated / Sensitive Skin Bath</li>
                            <li data-value="ear-cleaning">Ear Cleaning</li>
                            <li data-value="deshedding" class="disabled">
                                <div class="d-flex align-items-center justify-content-between">
                                    <span>Deshedding</span>
                                    <span class="disabled-text">Not available at this space</span>
                                </div>
                            </li>
                        </ul>

                        <input type="hidden" name="main_service">
                    </div>
                </div>

                <div class="space-find-card dont-need-groomer cursor d-flex flex-column justify-content-center mt-4">
                    <p class="medium-font-m-bold">I don’t need a groomer</p>
                    <p class="normal-light-color-font">Continue with booking this space only.</p>
                </div>
            </div>

            <div class="modal-footer d-flex align-items-center justify-content-center mt-4 gap-10">
                <button type="button" class="modal-footer-btn apply">Continue</button>
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
            const spacePromptModal = document.getElementById('space_prompt');
            const bookSpaceModal = document.getElementById('groomer_book_space');

            const groomerCard = spacePromptModal.querySelector('.groomer-find-card');
            const dontNeedGroomer = spacePromptModal.querySelector('.dont-need-groomer');
            const serviceBox = spacePromptModal.querySelector('#find-groomer-for-me');

            const continueBtn = spacePromptModal.querySelector('.modal-footer-btn.apply');
            const options = serviceBox.querySelectorAll('.select-options li');
            const selectedText = serviceBox.querySelector('.selected-text');
            const hiddenInput = serviceBox.querySelector('input[name="main_service"]');
            const goBackBtn = document.getElementById('goBack');

            let groomerSelected = false;

            function openModal(modal) {
                modal.style.display = 'flex';
            }

            function closeModal(modal) {
                modal.style.display = 'none';
            }

            groomerCard.addEventListener('click', function() {
                groomerSelected = true;
                groomerCard.classList.add('active');
                dontNeedGroomer.classList.remove('active');
                serviceBox.style.display = 'block';
            });

            dontNeedGroomer.addEventListener('click', function() {
                groomerSelected = false;
                dontNeedGroomer.classList.add('active');
                groomerCard.classList.remove('active');
                serviceBox.style.display = 'none';
            });

            options.forEach(function(option) {
                option.addEventListener('click', function() {
                    if (this.classList.contains('disabled')) return;

                    selectedText.textContent = this.textContent.trim();
                    hiddenInput.value = this.getAttribute('data-value');
                });
            });

            continueBtn.addEventListener('click', function() {
                if (!groomerSelected) {
                    //redirect to checkout page
                    window.location.href = @json(url('/checkout_booking_space'));
                } else {
                    closeModal(spacePromptModal);
                    openModal(bookSpaceModal);
                }

            });

            document.querySelectorAll('[data-modal-close]').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const modal = this.closest('.modal');
                    if (modal) closeModal(modal);
                });
            });

            goBackBtn.addEventListener('click', function() {
                openModal(spacePromptModal);
                closeModal(bookSpaceModal);
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


        // loop through all venu-sorting-section blocks
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


        // selected filter remove js starts

        document.querySelectorAll('.selected-item-section').forEach(section => {
            section.addEventListener('click', e => {
                if (e.target.classList.contains('cross')) {
                    e.target.closest('.selected-item')?.remove();
                }
            });
        });


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
            const pillBox = document.getElementById('spaceGroomerSelectedSection');
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
                modal.querySelectorAll('input[name="groomer-venue[]"]:checked').forEach(function(input) {
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
                    label.textContent = 'Select a groomer to continue';
                    continueBtn.textContent = 'Select a Groomer';
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
                    card.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center',
                        inline: 'nearest'
                    });
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
                    scroller.scrollBy({
                        top: delta,
                        behavior: 'smooth'
                    });
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
                syncPartnerSelection(card.dataset.name, {
                    scrollMap: true,
                    scrollList: false
                });
            }

            function selectMapCard(card) {
                if (card.classList.contains('is-selected')) {
                    clearPartnerSelection();
                    return;
                }
                syncPartnerSelection(card.dataset.name, {
                    scrollMap: true,
                    scrollList: true
                });
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
                galleryTitle = card.dataset.name || 'Groomer photos';
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

            modal.querySelectorAll('input[name="groomer-venue[]"]').forEach(function(input) {
                input.addEventListener('change', function() {
                    renderVenuePills();
                    const list = input.closest('.venu-sorting-section')?.querySelector('.venue-list');
                    if (list) list.style.display = 'none';
                });
            });

            modal.querySelectorAll('input[name="groomer-sort"]').forEach(function(input) {
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
                syncPartnerSelection(name, {
                    scrollMap: true,
                    scrollList: true
                });
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
