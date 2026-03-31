@extends('layouts.app')


@push('styles')
<link rel="stylesheet" href="{{ asset('css/company_information.css') }}">

<style>
    .top-head.d-flex.align-items-center.justify-content-center {
        padding: 25px 50px;
        border-radius: 10px;
        background: #F8F8F8;
    }

    .common-topics p.bg {
        border-radius: 100px;
        background: rgba(255, 201, 122, 0.13);
        color: #FFC97A;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        padding: 10px 20px;
    }

    .search-wrapper {
        width: 600px;
        position: relative;
        margin: 40px auto;
        border-radius: 100px;
        border: 1px solid #FFC97A;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
    }

    .search-wrapper input {
        width: 100%;
        padding: 15px 80px 15px 20px;
        border-radius: 30px;
        outline: none;
        font-size: 14px;
    }

    .search-wrapper input::placeholder {
        color: rgba(59, 55, 49, 0.25);
    }

    .search-btn {
        position: absolute;
        top: 50%;
        right: 4px;
        transform: translateY(-50%);
        border-radius: 50%;
        width: 42px;
        height: 42px;
        border: none;
        color: #fff;
        cursor: pointer;
        font-size: 16px;
    }


    .large-font {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-weight: 600;
        line-height: 60px;
    }

    .side-heading {
        max-width: 295px;
        width: 100%;
    }

    .card-text-width {
        max-width: 300px;
        width: 100%;
    }

    .aim-text-width {
        max-width: 610px;
        width: 100%;
    }

    .medium-font {
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-weight: 600;
    }

    .normal-font-weight {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .normal-font-weight.active {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .normal-font-bold {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px !important;
        font-weight: 600 !important;
    }

    .medium-font-bold {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px !important;
        font-weight: 600 !important;
    }

    .medium-light-font {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px !important;
        font-weight: 400 !important;
    }

    .light-color-font {
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-weight: 600;
    }

    .dark-color-font {
        color: var(--font-color);
        font-family: Lato;
        font-size: 14px !important;
        font-weight: 600 !important;
    }

    .simple-light-font {
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .simple-font {
        color: var(--font-color);
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    /* Layout */
    .tabs-wrapper {
        display: flex;
        min-height: 300px;
        align-items: flex-start;
    }

    /* Tab buttons */
    .tabs {
        width: 33%;
        position: -webkit-sticky;
        position: sticky;
        top: 24px;
        align-self: flex-start;
        height: fit-content;
        z-index: 5;
    }

    .tab-btn {
        display: block;
        width: 100%;
        border: none;
        background: transparent;
        cursor: pointer;
        text-align: left;
        transition: background 0.3s;
        padding: 20px 30px;
    }


    .tab-btn.active {
        border-radius: 96px;
        background: #F8F8F8;
    }

    .help-tabs.active {
        border-radius: 10px;
        border: 1px solid #FFC97A;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
    }

    .help-tabs.active p {
        color: #FFC97A !important;
    }

    /* Tab content */
    .tab-panels {
        width: 70%;
        margin-left: 15%;
    }

    /* Make the panel scrollable and smooth when jumping to top */
    .tab-panels {
        scroll-behavior: smooth;
    }

    .help-panel.tab-panels {
        margin-left: 0%;
        width: 100%;
        padding: 0 8%;
    }

    .tab-panel {
        display: none;
    }

    .tab-panel p {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 400;
    }

    .tab-panel .link-tag {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 400;
        text-decoration-line: underline;
        text-decoration-style: solid;
        text-underline-offset: 4px;
    }

    .tab-panel .small-link-tag {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        text-decoration-line: underline;
        text-decoration-style: solid;
        text-underline-offset: 4px;
    }

    .tab-panel.active {
        display: block;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .tabs-wrapper {
            flex-direction: column;
        }

        .tabs,
        .tab-panels {
            width: 100%;
            margin-left: 0;
        }

        .tab-btn {
            text-align: center;
        }

        /* disable sticky on small screens */
        .tabs {
            position: static;
        }
    }


    /* contact us css */
    .contact-left-section.mt-5 {
        height: 427px;
    }

    .contact-right-section.mt-5 {
        padding: 10px 30px;
        border-radius: 10px;
        background: rgba(255, 201, 122, 0.20);
        height: auto;
    }

    .fursgo-app-container {
        border-radius: 10px;
        background: #F2F6F9;
        padding: 40px;
    }

    /* about us csss */
    .contact-image {
        position: relative;
    }

    .contact-image-bottom-left {
        position: absolute;
        bottom: 0px;
        left: 5px;
    }

    .explore-app-container {
        border-radius: 10px;
        background: #F5F9ED;
        padding: 40px 255px;
    }

    @media (max-width: 1200px) {
        .explore-app-container {
            padding: 40px 120px;
        }
    }

    @media (max-width: 768px) {
        .explore-app-container {
            padding: 24px 24px;
        }
    }


    .groomer-container {
        width: 330px;
        height: 160px;
        border-radius: 10px;
        background: rgba(255, 201, 122, 0.13);
    }

    .space-container {
        width: 330px;
        height: 160px;
        border-radius: 10px;
        background: #FFF4F2;
    }


    /* help and support css */
    /* default: show inactive icon */
    /* Default (inactive) */
    .tab-icon .bg {
        fill: #EFEFEF;
        fill-opacity: 0.125;
    }

    .tab-icon .check {
        fill: #EFEFEF;
    }

    .tab-icon .stroke {
        stroke: #3B3731;
    }

    /* Active state */
    .tab-btn.active .tab-icon .bg {
        fill: #FFC97A;
    }


    .tab-btn.active .tab-icon .check {
        fill: #FFC97A;
    }

    /* default (inactive) */
    .tab-icon .accent {
        fill: #D4D4D4;
    }

    /* active tab */
    .tab-btn.active .tab-icon .accent {
        fill: #FFC97A;
    }

    /* Default (inactive) */
    .tab-btn .circle {
        fill: none;
        /* or a light gray if you want */
    }

    .tab-btn .bottom {
        fill: #EFEFEF;
    }

    /* Active tab */
    .tab-btn.active .circle {
        fill: #FFF8EE;
        /* your desired upper circle color */
    }

    .tab-btn.active .bottom {
        fill: #FFF8EE;
        /* example, change bottom color when active if needed */
    }

    /* Optional smooth transition */
    .tab-btn .circle,
    .tab-btn .bottom {
        transition: fill 0.25s ease;
    }

    /* Default inactive colors */
    .tab-btn .check {
        fill: #D4D4D4;
        transition: fill 0.3s ease;
    }


    /* Active tab colors */
    .tab-btn.active .check {
        fill: #FFC97A;
        /* active color */
    }

    .accordion {
        background-color: #FFF;
        cursor: pointer;
        padding: 30px 0;
        width: 100%;
        border: none;
        text-align: left;
        outline: none;
        transition: 0.4s;
    }



    .accordion:after {
        color: #777;
        font-weight: bold;
        float: right;
        margin-left: 5px;
        content: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" fill="none"><path d="M0.5 0.75656L7.24344 7.5L13.8737 0.75656" stroke="%233B3731" stroke-linecap="round" stroke-linejoin="round"/></svg>');

    }

    .acc-active:after {
        content: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" fill="none"><path d="M13.8737 7.24344L7.13022 0.499998L0.499976 7.13024" stroke="%233B3731" stroke-linecap="round" stroke-linejoin="round"/></svg>');
    }

    .panel {
        /* padding: 50px 0; */
        background-color: white;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.2s ease-out;
    }

    .panel p {
        padding: 30px 0;
    }

    .box-wrapper {
        width: 450px;
        min-height: 320px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        padding: 30px 36px;
    }

    .bg-div {
        border-radius: 10px;
        background: rgba(255, 216, 140, 0.20);
        padding: 30px;
    }

    a.underline {
        text-decoration-line: underline;
        text-decoration-style: solid;
        text-underline-offset: 4px;
        text-underline-position: from-font;
    }

    .no-bg.bg-div {
        background: none;
    }

    #request_modal {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(59, 55, 49, 0.10);
        justify-content: center;
        align-items: center;
        z-index: 999;
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(2px);
        overflow-y: auto;
    }

    #request_modal .modal-content {
        padding: 20px;
        border-radius: 10px;
        background: #FDFCF8;
        width: 820px;
        height: auto;
    }

    #request_modal .modal-footer {
        text-align: right;
        margin-top: 15px;
    }

    #request_modal .modal-footer-btn {
        padding: 8px 16px;
        border: none;
        background: #ffb26b;
        color: #fff;
        border-radius: 4px;
        cursor: pointer;
    }

    #request_modal .modal-content.size {
        width: 820px;
        height: auto;
        padding: 40px 0px;
    }

    #request_modal .modal-cross {
        padding: 0 20px 0 0;
    }

    .custom-select {
        width: 100% !important;
        max-width: 100% !important;
    }

    .custom-select .select-trigger.full-width {
        width: 100% !important;
    }

    .custom-select.open .select-trigger,
    .custom-select.has-value .select-trigger {
        border-color: var(--border)
    }

    /* upload box css */

    .upload-box {
        width: 100%;
        border: 1px solid #ddd;
        border-radius: 10px;
        background: #fff;
    }

    .upload-header {
        display: flex;
        gap: 15px;
        padding: 12px;
        border-bottom: 1px solid #eee;
    }

    .upload-header button {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 14px;
        color: #333;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .upload-header button:hover {
        color: #000;
    }

    .file-item {
        display: none;
        justify-content: space-between;
        align-items: center;
        padding: 14px;
    }

    .file-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .file-size {
        color: #9D9B98;
        font-family: Lato;
        font-size: 10px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .remove-btn {
        background: none;
        border: none;
        font-size: 18px;
        cursor: pointer;
        color: #999;
    }

    .remove-btn:hover {
        color: #000;
    }

    .toggle-button-content .bold-font,
    .settings-section-content .bold-font,
    .active-sessions .bold-font {
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
    }


    /* toggle button */
    /* Container for the toggle */
    .toggle-switch {
        position: relative;
        width: 58px;
        height: 33px;
        background: rgba(255, 201, 122, 0.50);
        /* off color */
        border-radius: 24px;
        user-select: none;
        -webkit-user-select: none;
        user-select: none;
        -webkit-user-select: none;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    /* Circle inside toggle */
    .toggle-circle {
        position: absolute;
        top: 3px;
        left: 3px;
        width: 27px;
        height: 27px;
        background-color: white;
        border-radius: 50%;
        transition: transform 0.3s;
    }


    .toggle-switch.on .toggle-circle {
        background-color: transparent;
        /* or any color you like */
        transform: translateX(34px);
        /* moves the circle to right */
    }

    /* ON state styles */
    .toggle-switch.on {
        background-color: #FFC97A;
    }

    .toggle-switch.on .toggle-circle {
        transform: translateX(26px);
    }

    .toggle-switch.on .toggle-circle::after {
        display: block;
    }


    .user-sessions {
        width: 100%;
    }

    .logged-devices {
        padding: 0 0px 20px 20px
    }

    .rounded-circle {
        border-radius: 50px;
        width: 56px;
        height: 56px;
    }

    img.social-icons {
        height: 25px;
        width: 25px;
    }

    .border-and-bg {
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #D4D4D4;
    }


    /* light and dark mode css */
    .toggle-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
    }

    .label {
        font-size: 14px;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: #8b7355;
        transition: color 0.5s;
    }

    body.dark .label {
        color: #9090b0;
    }

    /* The toggle */
    .toggle-dark-light {
        position: relative;
        width: 58px;
        height: 33px;
        cursor: pointer;
        -webkit-tap-highlight-color: transparent;
    }

    .toggle-dark-light input {
        opacity: 0;
        width: 0;
        height: 0;
        position: absolute;
    }

    .track {
        position: absolute;
        inset: 0;
        border-radius: 999px;
        background: #FFC97A;
        transition: background 0.45s cubic-bezier(.4, 0, .2, 1);
    }

    .toggle-dark-light input:checked~.track {
        background: #3B3731;
    }

    .thumb {
        position: absolute;
        top: 4px;
        left: 4px;
        width: 27px;
        height: 27px;
        border-radius: 50%;
        transition: transform 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .toggle-dark-light input:checked~.track .thumb {
        transform: translateX(26px);
    }

    .icon-dark-light {
        position: absolute;
        transition: opacity 0.3s, transform 0.4s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .icon-sun {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }

    .icon-moon {
        opacity: 0;
        transform: scale(0.5) rotate(-30deg);
    }

    .toggle-dark-light input:checked~.track .thumb .icon-sun {
        opacity: 0;
        transform: scale(0.5) rotate(30deg);
    }

    .toggle-dark-light input:checked~.track .thumb .icon-moon {
        opacity: 1;
        transform: scale(1) rotate(0deg);
    }


    /* payments tab css */

    .card-details {
        border-radius: 10px;
        border: 1px solid #EEE;
        background: #FFF;
        min-width: 550px;
        padding: 20px 30px;
    }

    .card-details.active {
        border-radius: 10px;
        background: #FFF;
        box-shadow: 0 4px 20px 5px rgba(0, 0, 0, 0.05);
        padding: 20px 30px;
    }

    .default-selection {
        background: rgba(59, 55, 49, 0.13);
        border: none;
        padding: 10px 20px;
        border-radius: 38px;
    }

    /* custom table */
    table.custom-table {
        border-radius: 10px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        width: 100%;
    }

    table.custom-table td,
    table.custom-table td,
    table.custom-table th,
    table.custom-table thead {
        padding: 12px;
        border-bottom: 1px solid #E2E2E2;
    }

    /* Remove border from last row */
    table.custom-table tr:last-child td {
        border-bottom: none !important;
    }


    /* invoice modal css */
    #invoice_modal {
        backdrop-filter: blur(2px);
    }

    #invoice_modal .invoice-font {
        color: #3B3731;
        font-family: Lato;
        font-size: 20px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
    }

    #invoice_modal .modal-content {
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        width: 670px;
        padding: 0;
    }

    #invoice_modal .modal-head {
        border-radius: 10px 10px 0 0;
        border: 1px solid #D4D4D4;
        background: #F8F8F8;
        padding: 15px;
    }

    #invoice_modal .modal-body {
        padding: 25px;
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
                    <div class="top-head d-flex align-items-center justify-content-center">
                        <h1 class="large-font">Settings</h1>
                    </div>
                </div>
                <div class="tabs-wrapper mb-5 mt-5" data-tabs>
                    <div class="tabs mt-5">
                        <button class="tab-btn normal-font-weight d-flex align-items-center gap-10 active" data-tab="general">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="18" viewBox="0 0 17 18" fill="none">
                                <path d="M8.10018 10.9413C9.41818 10.9413 10.4866 9.87283 10.4866 8.55483C10.4866 7.23682 9.41818 6.16837 8.10018 6.16837C6.78217 6.16837 5.71371 7.23682 5.71371 8.55483C5.71371 9.87283 6.78217 10.9413 8.10018 10.9413Z" stroke="#3B3731" stroke-width="1.2" />
                                <path d="M9.50404 0.72092C9.2121 0.600006 8.8414 0.600006 8.10001 0.600006C7.35861 0.600006 6.98791 0.600006 6.69597 0.72092C6.50281 0.800877 6.3273 0.918118 6.17948 1.06594C6.03166 1.21377 5.91442 1.38927 5.83446 1.58243C5.76127 1.75983 5.73184 1.96745 5.7207 2.26894C5.71552 2.48685 5.65516 2.69988 5.54525 2.88811C5.43535 3.07634 5.27949 3.23362 5.09227 3.34523C4.90199 3.45165 4.68783 3.50805 4.46983 3.50916C4.25182 3.51027 4.03709 3.45606 3.84574 3.3516C3.57846 3.21 3.38515 3.13204 3.19344 3.10659C2.77527 3.05159 2.35237 3.1649 2.01771 3.4216C1.76793 3.6149 1.58178 3.93548 1.21109 4.57744C0.84039 5.2194 0.654246 5.53998 0.613677 5.8542C0.58634 6.06138 0.600089 6.27193 0.65414 6.4738C0.708191 6.67567 0.801484 6.86492 0.928689 7.03072C1.04642 7.18346 1.21109 7.31153 1.46644 7.47222C1.8427 7.70848 2.08453 8.111 2.08453 8.55488C2.08453 8.99876 1.8427 9.40128 1.46644 9.63674C1.21109 9.79822 1.04563 9.9263 0.928689 10.079C0.801484 10.2448 0.708191 10.4341 0.65414 10.636C0.600089 10.8378 0.58634 11.0484 0.613677 11.2556C0.655042 11.569 0.84039 11.8904 1.21029 12.5323C1.58178 13.1743 1.76713 13.4949 2.01771 13.6882C2.18352 13.8154 2.37276 13.9087 2.57464 13.9627C2.77651 14.0168 2.98705 14.0305 3.19424 14.0032C3.38515 13.9777 3.57846 13.8998 3.84574 13.7582C4.03709 13.6537 4.25182 13.5995 4.46983 13.6006C4.68783 13.6017 4.90199 13.6581 5.09227 13.7645C5.47649 13.9873 5.70479 14.3969 5.7207 14.8408C5.73184 15.1431 5.76048 15.3499 5.83446 15.5273C5.91442 15.7205 6.03166 15.896 6.17948 16.0438C6.3273 16.1916 6.50281 16.3089 6.69597 16.3888C6.98791 16.5097 7.35861 16.5097 8.10001 16.5097C8.8414 16.5097 9.2121 16.5097 9.50404 16.3888C9.6972 16.3089 9.87271 16.1916 10.0205 16.0438C10.1684 15.896 10.2856 15.7205 10.3656 15.5273C10.4387 15.3499 10.4682 15.1431 10.4793 14.8408C10.4952 14.3969 10.7235 13.9865 11.1077 13.7645C11.298 13.6581 11.5122 13.6017 11.7302 13.6006C11.9482 13.5995 12.1629 13.6537 12.3543 13.7582C12.6216 13.8998 12.8149 13.9777 13.0058 14.0032C13.213 14.0305 13.4235 14.0168 13.6254 13.9627C13.8272 13.9087 14.0165 13.8154 14.1823 13.6882C14.4329 13.4956 14.6182 13.1743 14.9889 12.5323C15.3596 11.8904 15.5458 11.5698 15.5863 11.2556C15.6137 11.0484 15.5999 10.8378 15.5459 10.636C15.4918 10.4341 15.3985 10.2448 15.2713 10.079C15.1536 9.9263 14.9889 9.79822 14.7336 9.63754C14.5474 9.5241 14.393 9.36527 14.2849 9.17592C14.1768 8.98657 14.1185 8.77289 14.1155 8.55488C14.1155 8.111 14.3573 7.70848 14.7336 7.47301C14.9889 7.31153 15.1544 7.18346 15.2713 7.03072C15.3985 6.86492 15.4918 6.67567 15.5459 6.4738C15.5999 6.27193 15.6137 6.06138 15.5863 5.8542C15.545 5.54078 15.3596 5.2194 14.9897 4.57744C14.6182 3.93548 14.4329 3.6149 14.1823 3.4216C14.0165 3.29439 13.8272 3.2011 13.6254 3.14705C13.4235 3.093 13.213 3.07925 13.0058 3.10659C12.8149 3.13204 12.6216 3.21 12.3535 3.3516C12.1622 3.45592 11.9476 3.51005 11.7298 3.50894C11.5119 3.50783 11.2979 3.4515 11.1077 3.34523C10.9205 3.23362 10.7647 3.07634 10.6548 2.88811C10.5449 2.69988 10.4845 2.48685 10.4793 2.26894C10.4682 1.96665 10.4395 1.75983 10.3656 1.58243C10.2856 1.38927 10.1684 1.21377 10.0205 1.06594C9.87271 0.918118 9.6972 0.800877 9.50404 0.72092Z" stroke="#3B3731" stroke-width="1.2" />
                            </svg>
                            General
                        </button>
                        <button class="tab-btn normal-font-weight d-flex align-items-center gap-10" data-tab="notification">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                <path d="M12.5479 7.70527C13.0066 11.9487 14.8106 13.2316 14.8106 13.2316H0.600037C0.600037 13.2316 2.96846 11.5476 2.96846 5.65264C2.96846 4.3129 3.46741 3.02764 4.35556 2.08027C5.24372 1.1329 6.45004 0.600006 7.7053 0.600006C7.97214 0.600006 8.2353 0.62369 8.49477 0.671059M9.07109 15.6C8.93229 15.8393 8.73307 16.0379 8.49337 16.176C8.25368 16.314 7.98192 16.3867 7.7053 16.3867C7.42868 16.3867 7.15692 16.314 6.91723 16.176C6.67753 16.0379 6.47831 15.8393 6.33951 15.6M13.2316 5.33685C13.8598 5.33685 14.4622 5.08732 14.9063 4.64315C15.3505 4.19899 15.6 3.59657 15.6 2.96843C15.6 2.34028 15.3505 1.73787 14.9063 1.2937C14.4622 0.849536 13.8598 0.600006 13.2316 0.600006C12.6035 0.600006 12.0011 0.849536 11.5569 1.2937C11.1127 1.73787 10.8632 2.34028 10.8632 2.96843C10.8632 3.59657 11.1127 4.19899 11.5569 4.64315C12.0011 5.08732 12.6035 5.33685 13.2316 5.33685Z" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Notifications
                        </button>
                        <button class="tab-btn normal-font-weight d-flex align-items-center gap-10" data-tab="login_and_security">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="19" viewBox="0 0 17 19" fill="none">
                                <path d="M0.600006 17.4754V16.5379C0.600006 13.4347 3.12188 10.9129 6.22501 10.9129H9.97501C13.0781 10.9129 15.6 13.4347 15.6 16.5379V17.4754" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.09991 8.10001C6.02804 8.10001 4.34991 6.42189 4.34991 4.35001C4.34991 2.27813 6.02804 0.600006 8.09991 0.600006C10.1718 0.600006 11.8499 2.27813 11.8499 4.35001C11.8499 6.42189 10.1718 8.10001 8.09991 8.10001Z" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Login & Security
                        </button>
                        <button class="tab-btn normal-font-weight d-flex align-items-center gap-10" data-tab="payments">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="12" viewBox="0 0 15 12" fill="none">
                                <rect x="0.6" y="0.6" width="13.8" height="10.5855" rx="1.4" stroke="#3B3731" stroke-width="1.2" />
                                <line y1="3.52491" x2="14.7321" y2="3.52491" stroke="#3B3731" stroke-width="1.2" />
                                <line x1="10.2429" y1="7.97129" x2="12.2572" y2="7.97129" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                            Payments
                        </button>
                        <button class="tab-btn normal-font-weight d-flex align-items-center gap-10" data-tab="privacy_and_permissions">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="18" viewBox="0 0 17 18" fill="none">
                                <path d="M0.600021 7.29169V10.3128C0.600021 11.9478 0.600022 12.7658 0.79502 13.4328C1.26002 15.021 2.50236 16.2704 4.09058 16.8263C4.61999 16.9816 5.14587 17.1466 6.1588 17.2957C6.75001 17.3801 7.35143 17.3604 7.93585 17.2375C8.2032 17.1828 8.42114 17.136 8.60466 17.0963C9.03701 17.0028 9.46936 16.8907 9.86642 16.6957C10.3146 16.4769 10.6632 16.2493 11.1679 15.8725C11.4696 15.6475 11.7458 15.3731 12.2973 14.8243L15.1623 11.9743C15.301 11.8365 15.4111 11.6727 15.4862 11.4922C15.5613 11.3116 15.6 11.1181 15.6 10.9225C15.6 10.727 15.5613 10.5334 15.4862 10.3529C15.4111 10.1724 15.301 10.0086 15.1623 9.87078C14.8813 9.59168 14.5013 9.43504 14.1052 9.43504C13.7091 9.43504 13.3292 9.59168 13.0482 9.87078L11.0629 11.8464V7.29169" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M8.44672 5.34435V3.66613C8.44672 2.94702 9.0326 2.36467 9.75524 2.36467C10.477 2.36467 11.0629 2.94702 11.0629 3.66613V7.56963M5.83145 7.28816V1.90144C5.83145 1.18232 6.41733 0.599976 7.13909 0.599976C7.86173 0.599976 8.44672 1.18232 8.44672 1.90144V7.29169M3.21529 4.87494V7.29169V2.78819C3.22415 2.44715 3.36585 2.12305 3.6102 1.88497C3.85455 1.64689 4.18221 1.51365 4.52337 1.51365C4.86453 1.51365 5.1922 1.64689 5.43654 1.88497C5.68089 2.12305 5.82259 2.44715 5.83145 2.78819V7.29169M3.21529 6.22846V5.43082C3.21529 4.71171 2.6303 4.12936 1.90766 4.12936C1.1859 4.12936 0.600021 4.71171 0.600021 5.43082V7.84668" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Privacy & Permissions
                        </button>
                        <button class="tab-btn normal-font-weight d-flex align-items-center gap-10" data-tab="app_and_system">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                <path d="M9.97501 3.41248H15.6M12.7875 0.599976V6.22498M0.600006 1.53748C0.600006 1.28884 0.698778 1.05038 0.874594 0.874563C1.05041 0.698748 1.28887 0.599976 1.53751 0.599976H5.28751C5.53615 0.599976 5.7746 0.698748 5.95042 0.874563C6.12623 1.05038 6.22501 1.28884 6.22501 1.53748V5.28748C6.22501 5.53612 6.12623 5.77457 5.95042 5.95039C5.7746 6.1262 5.53615 6.22498 5.28751 6.22498H1.53751C1.28887 6.22498 1.05041 6.1262 0.874594 5.95039C0.698778 5.77457 0.600006 5.53612 0.600006 5.28748V1.53748ZM0.600006 10.9125C0.600006 10.6638 0.698778 10.4254 0.874594 10.2496C1.05041 10.0737 1.28887 9.97498 1.53751 9.97498H5.28751C5.53615 9.97498 5.7746 10.0737 5.95042 10.2496C6.12623 10.4254 6.22501 10.6638 6.22501 10.9125V14.6625C6.22501 14.9111 6.12623 15.1496 5.95042 15.3254C5.7746 15.5012 5.53615 15.6 5.28751 15.6H1.53751C1.28887 15.6 1.05041 15.5012 0.874594 15.3254C0.698778 15.1496 0.600006 14.9111 0.600006 14.6625V10.9125ZM9.97501 10.9125C9.97501 10.6638 10.0738 10.4254 10.2496 10.2496C10.4254 10.0737 10.6639 9.97498 10.9125 9.97498H14.6625C14.9111 9.97498 15.1496 10.0737 15.3254 10.2496C15.5012 10.4254 15.6 10.6638 15.6 10.9125V14.6625C15.6 14.9111 15.5012 15.1496 15.3254 15.3254C15.1496 15.5012 14.9111 15.6 14.6625 15.6H10.9125C10.6639 15.6 10.4254 15.5012 10.2496 15.3254C10.0738 15.1496 9.97501 14.9111 9.97501 14.6625V10.9125Z" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            App & System Preferences
                        </button>
                        <button class="tab-btn normal-font-weight d-flex align-items-center gap-10" data-tab="account_linking">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17" fill="none">
                                <path d="M10.4981 1.61658C11.7528 0.356668 13.6854 0.254675 14.8155 1.38784C15.9457 2.52101 15.843 4.46263 14.5883 5.72254L12.7704 7.54715M6.63588 9.59976C5.50571 8.46584 5.60845 6.52498 6.86236 5.26582L8.4755 3.64668" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                                <path d="M9.5659 6.59998C10.6953 7.73389 10.5933 9.67476 9.33866 10.9339L7.52079 12.7585L5.70292 14.5832C4.44826 15.8431 2.51565 15.9451 1.38548 14.8119C0.255312 13.6787 0.358054 11.7371 1.61271 10.4772L3.43059 8.65258" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                            </svg>
                            Account Linking
                        </button>
                        <button class="tab-btn normal-font-weight d-flex align-items-center gap-10" data-tab="data_and_legal">
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="19" viewBox="0 0 15 19" fill="none">
                                <path d="M7.5 0.599609C7.6159 0.599609 7.74865 0.62226 7.90137 0.677734L13.6572 2.82617C13.822 2.89351 13.9622 2.98868 14.082 3.11621L14.1963 3.25586C14.3323 3.44803 14.4003 3.65844 14.4004 3.90527V8.51562C14.4003 10.6471 13.8127 12.625 12.6309 14.4609C11.456 16.286 9.88134 17.5714 7.89453 18.332H7.89355C7.84043 18.3524 7.77884 18.369 7.70801 18.3809C7.63022 18.3939 7.56118 18.4004 7.5 18.4004C7.43964 18.4004 7.37031 18.394 7.29102 18.3809C7.25534 18.3749 7.22244 18.3684 7.19238 18.3604L7.10742 18.332L6.73926 18.1836C4.92392 17.409 3.4707 16.1721 2.36914 14.4609C1.18732 12.625 0.59968 10.6471 0.599609 8.51562V3.90527C0.599685 3.65844 0.667657 3.44803 0.803711 3.25586V3.25488C0.943976 3.05609 1.1187 2.91648 1.33691 2.82715L7.09961 0.676758L7.10059 0.677734C7.25215 0.622508 7.38419 0.599609 7.5 0.599609Z" stroke="#3B3731" stroke-width="1.2" />
                            </svg>
                            Data & Legal
                        </button>
                    </div>

                    <div class="tab-panels mt-5">

                        <div class="tab-panel active" id="general">
                            <h1 class="large-font">General</h1>

                            <div class="form-field mt-4">
                                <label>Language</label>
                                <div class="custom-select">
                                    <div class="select-trigger full-width">
                                        <span class="selected-text">English (United Kingdom)</span>
                                        <svg width="16" height="16" viewBox="0 0 24 24">
                                            <path d="M6 9l6 6 6-6" fill="none" stroke="#666" stroke-width="2"></path>
                                        </svg>
                                    </div>

                                    <ul class="select-options">
                                        <li data-value="english_united_kingdom">English (United Kingdom)</li>
                                    </ul>

                                    <input type="hidden" name="language">
                                </div>
                            </div>

                            <div class="form-field mt-4">
                                <label>Time Zone</label>
                                <div class="custom-select">
                                    <div class="select-trigger full-width">
                                        <span class="selected-text">Europe/London</span>
                                        <svg width="16" height="16" viewBox="0 0 24 24">
                                            <path d="M6 9l6 6 6-6" fill="none" stroke="#666" stroke-width="2"></path>
                                        </svg>
                                    </div>

                                    <ul class="select-options">
                                        <li data-value="europe_london">Europe/London</li>
                                    </ul>

                                    <input type="hidden" name="time_zone">
                                </div>
                            </div>

                            <div class="form-field mt-4">
                                <label>Currency Preference</label>
                                <div class="custom-select">
                                    <div class="select-trigger full-width">
                                        <span class="selected-text">£ - GBP</span>
                                        <svg width="16" height="16" viewBox="0 0 24 24">
                                            <path d="M6 9l6 6 6-6" fill="none" stroke="#666" stroke-width="2"></path>
                                        </svg>
                                    </div>

                                    <ul class="select-options">
                                        <li data-value="£ - GBP">£ - GBP</li>
                                    </ul>

                                    <input type="hidden" name="currency">
                                </div>
                            </div>

                        </div>

                        <div class="tab-panel" id="notification">

                            <h1 class="large-font">Notifications</h1>


                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Booking Updates</p>
                                    <p style="color: #9D9B98">Notify me when a booking is confirmed or changed.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Groomer Messages Zone</p>
                                    <p style="color: #9D9B98">Get alerts when groomers send you a message.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Space Owners Messages Zone</p>
                                    <p style="color: #9D9B98">Get alerts when space owners send you a message.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Promotions & Offers</p>
                                    <p style="color: #9D9B98">Receive special deals and exclusive discounts.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Reminder Alerts</p>
                                    <p style="color: #9D9B98">Send reminders 24 hours before my booking.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="tab-panel" id="login_and_security">
                            <h1 class="large-font">Login & Security</h1>

                            <div class="settings-section-content d-flex flex-column justify-content-between mt-5 gap-25">
                                <p class="bold-font">Current Password</p>

                                <div class="d-flex align-items-center justify-content-between gap-25">
                                    <p style="color: #9D9B98">Last updated 2 days ago.</p>
                                    <a href="" class="link-tag">Update Password</a>
                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">2FA & Login Devices</p>
                                    <p style="color: #9D9B98">Two-factor authentication is enabled.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="active-sessions mt-5">

                                <p class="bold-font">Active Sessions</p>

                                <div class="user-sessions mt-5">
                                    <div class="logged-devices d-flex align-items-center justify-content-between mt-3">
                                        <div class="d-flex align-items-center gap-20">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="33" viewBox="0 0 23 33" fill="none">
                                                <path d="M16.71 0.75H5.79C3.00648 0.75 0.75 3.00648 0.75 5.79V26.79C0.75 29.5735 3.00648 31.83 5.79 31.83H16.71C19.4935 31.83 21.75 29.5735 21.75 26.79V5.79C21.75 3.00648 19.4935 0.75 16.71 0.75Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M9.56982 25.9497H12.9298" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>

                                            <p>Logged in on iPhone - <span style="color:#9D9B98">28/08/2025, 18:52 GMT</span></p>
                                        </div>
                                        <a href="" class="link-tag">Sign out</a>
                                    </div>
                                    <hr style="border-top: 1px solid #E2E2E2;">
                                    <div class="logged-devices d-flex align-items-center justify-content-between mt-3">
                                        <div class="d-flex align-items-center gap-20">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="21" height="17" viewBox="0 0 21 17" fill="none">
                                                <path d="M6.77246 16.2041H14.2275C14.2319 16.2041 14.2347 16.2054 14.2363 16.2061C14.2384 16.207 14.2408 16.2086 14.2432 16.2109C14.2457 16.2135 14.2471 16.2166 14.248 16.2188C14.2488 16.2204 14.25 16.223 14.25 16.2275C14.25 16.2319 14.2487 16.2347 14.248 16.2363C14.2471 16.2384 14.2456 16.2408 14.2432 16.2432C14.2408 16.2456 14.2384 16.2471 14.2363 16.248C14.2347 16.2487 14.2319 16.25 14.2275 16.25H6.77246C6.76811 16.25 6.76532 16.2487 6.76367 16.248C6.7616 16.2471 6.75922 16.2456 6.75684 16.2432C6.75445 16.2408 6.75291 16.2384 6.75195 16.2363C6.75126 16.2347 6.75003 16.2319 6.75 16.2275C6.75 16.223 6.75124 16.2204 6.75195 16.2188C6.75288 16.2166 6.75427 16.2135 6.75684 16.2109C6.75922 16.2086 6.76163 16.207 6.76367 16.2061C6.76532 16.2054 6.76811 16.2041 6.77246 16.2041ZM1 0.75H20C20.1381 0.75 20.25 0.861929 20.25 1V12.9092C20.25 13.0472 20.138 13.1592 20 13.1592H1C0.861958 13.1592 0.750048 13.0472 0.75 12.9092V1C0.75 0.861929 0.861929 0.75 1 0.75Z" stroke="#3B3731" stroke-width="1.5" />
                                            </svg>

                                            <p>Logged in on Web - <span style="color:#9D9B98">28/08/2025, 18:52 GMT</span></p>
                                        </div>
                                        <a href="" class="link-tag">Sign out</a>
                                    </div>
                                    <hr style="border-top: 1px solid #E2E2E2;">
                                    <div class="logged-devices d-flex align-items-center justify-content-between mt-3">
                                        <div class="d-flex align-items-center gap-20">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="23" height="26" viewBox="0 0 23 26" fill="none">
                                                <path d="M0.75 10.35V18.35M21.75 10.35V18.35M3.98077 8.75H18.5192M3.98077 8.75V19.15C3.98077 19.7865 4.23606 20.397 4.69047 20.8471C5.14489 21.2971 5.76121 21.55 6.40385 21.55H16.0962C17.4369 21.55 18.5192 20.478 18.5192 19.15V8.75M3.98077 8.75C3.98077 4.766 7.22769 2.35 11.25 2.35C15.2723 2.35 18.5192 4.766 18.5192 8.75M5.59615 0.75L7.21154 3.15M16.9038 0.75L15.2885 3.15M7.21154 21.55V24.75M15.2885 21.55V24.75" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>

                                            <p>Logged in on Android - <span style="color:#9D9B98">28/08/2025, 18:52 GMT</span></p>
                                        </div>
                                        <a href="" class="link-tag">Sign out</a>
                                    </div>
                                    <hr style="border-top: 1px solid #E2E2E2;">
                                </div>
                            </div>

                            <div class="toggle-button-content d-flex flex-column justify-content-between mt-5 gap-25">
                                <p class="bold-font mt-3">Deactivate your account</p>

                                <div class="d-flex align-items-center justify-content-between gap-25">
                                    <p style="color: #9D9B98">This action will permanently delete your account.</p>
                                    <a href="" class="small-link-tag">Deactivate Account</a>
                                </div>
                            </div>

                        </div>

                        <div class="tab-panel" id="payments">

                            <h1 class="large-font">Payments</h1>

                            <p class="normal-font-bold mt-5">Saved payment methods</p>

                            <div class="card-edit-details cursor d-flex align-items-center justify-content-between mt-4">
                                <div class="card-details active d-flex align-items-center justify-content-between gap-20">
                                    <div class="d-flex align-items-center gap-10">
                                        <div style="width: 62px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="62" height="20" viewBox="0 0 62 20" fill="none">
                                                <path d="M32.0649 6.39377C32.0295 9.17985 34.5505 10.7345 36.4495 11.6589C38.4005 12.6073 39.0559 13.2155 39.0481 14.0637C39.0336 15.3617 37.4918 15.9346 36.0491 15.9568C33.5321 15.9958 32.0685 15.278 30.9051 14.7351L29.9983 18.9735C31.1657 19.5108 33.3272 19.9794 35.5686 20C40.8301 20 44.2724 17.4055 44.291 13.3829C44.3116 8.27769 37.222 7.99511 37.2705 5.71321C37.2872 5.02129 37.9481 4.28292 39.3964 4.09518C40.1133 4.00034 42.0922 3.92776 44.3358 4.95984L45.2164 0.859098C44.0098 0.420235 42.4591 0 40.5284 0C35.5761 0 32.093 2.6298 32.0649 6.39377ZM53.678 0.35322C52.7172 0.35322 51.9076 0.91305 51.5462 1.77215L44.0304 19.6988H49.2881L50.3343 16.8104H56.7591L57.366 19.6988H62L57.9562 0.35322H53.678ZM54.4135 5.57918L55.9308 12.8437H51.7753L54.4135 5.57918ZM25.6903 0.353462L21.546 19.6986H26.5561L30.6985 0.352978L25.6903 0.353462ZM18.2786 0.353462L13.0638 13.5206L10.9544 2.32472C10.7069 1.0749 9.7294 0.35322 8.64391 0.35322H0.119398L0 0.914984C1.75005 1.29433 3.73841 1.90618 4.94305 2.56084C5.68027 2.96076 5.89048 3.31035 6.13267 4.26066L10.128 19.6988H15.4225L23.5397 0.35322L18.2786 0.353462Z" fill="url(#paint0_linear_5_657)" />
                                                <defs>
                                                    <linearGradient id="paint0_linear_5_657" x1="2850.39" y1="60.12" x2="2908.22" y2="-1993.91" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#222357" />
                                                        <stop offset="1" stop-color="#254AA5" />
                                                    </linearGradient>
                                                </defs>
                                            </svg>
                                        </div>
                                        <p class="dark-color-font">Visa ending in 7890 | <span class="simple-light-font"> Exp. date 06/27</span></p>
                                    </div>
                                    <button class="dark-color-font default-selection">Default</button>
                                </div>
                                <a href="" class="small-link-tag">Edit</a>
                            </div>
                            <div class="card-edit-details cursor d-flex align-items-center justify-content-between mt-4">
                                <div class="card-details d-flex align-items-center justify-content-between gap-20">
                                    <div class="d-flex align-items-center gap-10">
                                        <div style="width: 62px;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="33" height="20" viewBox="0 0 33 20" fill="none">
                                                <path d="M11.7742 2.13867H20.501V17.8609H11.7742V2.13867Z" fill="#FF5F00" />
                                                <path d="M12.3284 10C12.3284 6.8056 13.8243 3.97221 16.1237 2.13884C14.4339 0.805527 12.3007 0 9.97359 0C4.46029 0 0 4.47216 0 10C0 15.5278 4.46029 20 9.97347 20C12.3006 20 14.4338 19.1945 16.1237 17.861C13.8243 16.0555 12.3284 13.1944 12.3284 10Z" fill="#EB001B" />
                                                <path d="M32.2751 10C32.2751 15.5277 27.8148 20 22.3017 20C19.9745 20 17.8414 19.1945 16.1514 17.861C18.4786 16.0278 19.9469 13.1944 19.9469 10C19.9469 6.8056 18.4508 3.97221 16.1514 2.13884C17.8412 0.805527 19.9745 0 22.3017 0C27.8148 0 32.2751 4.49999 32.2751 10Z" fill="#F79E1B" />
                                            </svg>
                                        </div>
                                        <p class="dark-color-font">Mastercard ending in 4589 | <span class="simple-light-font"> Exp. date 07/30</span></p>
                                    </div>
                                </div>
                                <a href="" class="small-link-tag">Edit</a>
                            </div>

                            <button class="btn-custom btn-active-bg mt-4">+ Add payment method</button>


                            <p class="bold-font mt-5">Payment History</p>

                            <table class="custom-table text-center mt-4">
                                <thead class="simple-font" style="color: #000;">
                                    <tr>
                                        <th scope="col">Date</th>
                                        <th scope="col">Space</th>
                                        <th scope="col">Service Type</th>
                                        <th scope="col">Pet</th>
                                        <th scope="col">Amount</th>
                                        <th scope="col">View</th>
                                        <th scope="col">Download</th>
                                    </tr>
                                </thead>
                                <tbody class="simple-font">
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Home visits</td>
                                        <td><span class="dark-color-font">Full Groom</span>
                                            <br>
                                            Claire Smith
                                        </td>
                                        <td><span class="dark-color-font">Bella</span>
                                            <br>
                                            Rabbit
                                        </td>
                                        <td>£76.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Garden/ Shed</td>
                                        <td><span class="dark-color-font">Nail Trim</span>
                                            <br>
                                            Dev Emile
                                        </td>
                                        <td><span class="dark-color-font">Louis</span>
                                            <br>
                                            Dog
                                        </td>
                                        <td>£24.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Home visits</td>
                                        <td><span class="dark-color-font">Full Groom</span>
                                            <br>
                                            Claire Smith
                                        </td>
                                        <td><span class="dark-color-font">Bella</span>
                                            <br>
                                            Rabbit
                                        </td>
                                        <td>£76.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Garden/ Shed</td>
                                        <td><span class="dark-color-font">Nail Trim</span>
                                            <br>
                                            Dev Emile
                                        </td>
                                        <td><span class="dark-color-font">Louis</span>
                                            <br>
                                            Dog
                                        </td>
                                        <td>£24.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Home visits</td>
                                        <td><span class="dark-color-font">Full Groom</span>
                                            <br>
                                            Claire Smith
                                        </td>
                                        <td><span class="dark-color-font">Bella</span>
                                            <br>
                                            Rabbit
                                        </td>
                                        <td>£76.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Garden/ Shed</td>
                                        <td><span class="dark-color-font">Nail Trim</span>
                                            <br>
                                            Dev Emile
                                        </td>
                                        <td><span class="dark-color-font">Louis</span>
                                            <br>
                                            Dog
                                        </td>
                                        <td>£24.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Home visits</td>
                                        <td><span class="dark-color-font">Full Groom</span>
                                            <br>
                                            Claire Smith
                                        </td>
                                        <td><span class="dark-color-font">Bella</span>
                                            <br>
                                            Rabbit
                                        </td>
                                        <td>£76.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Garden/ Shed</td>
                                        <td><span class="dark-color-font">Nail Trim</span>
                                            <br>
                                            Dev Emile
                                        </td>
                                        <td><span class="dark-color-font">Louis</span>
                                            <br>
                                            Dog
                                        </td>
                                        <td>£24.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Home visits</td>
                                        <td><span class="dark-color-font">Full Groom</span>
                                            <br>
                                            Claire Smith
                                        </td>
                                        <td><span class="dark-color-font">Bella</span>
                                            <br>
                                            Rabbit
                                        </td>
                                        <td>£76.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                    <tr>
                                        <td>06/11/25</td>
                                        <td>Garden/ Shed</td>
                                        <td><span class="dark-color-font">Nail Trim</span>
                                            <br>
                                            Dev Emile
                                        </td>
                                        <td><span class="dark-color-font">Louis</span>
                                            <br>
                                            Dog
                                        </td>
                                        <td>£24.00</td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none">
                                                <path d="M9.49609 15C11.4291 15 12.9961 13.433 12.9961 11.5C12.9961 9.567 11.4291 8 9.49609 8C7.5631 8 5.99609 9.567 5.99609 11.5C5.99609 13.433 7.5631 15 9.49609 15Z" stroke="black" />
                                                <path d="M18.4961 11.5C18.4961 11.5 17.4961 3.5 9.49609 3.5C1.49609 3.5 0.496094 11.5 0.496094 11.5" stroke="black" />
                                            </svg></td>
                                        <td><svg data-modal-open="invoice_modal" class="cursor" xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                <path d="M0.5 15.5V17C0.5 17.3978 0.643751 17.7794 0.899456 18.0607C1.15516 18.342 1.50207 18.5 1.86372 18.5H14.1365C14.4982 18.5 14.8451 18.342 15.1008 18.0607C15.3565 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M8 0.5V12.875M12.0909 8.75L8 13.25L3.90909 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg></td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Modal  -->

                            <div class="modal" id="invoice_modal">
                                <div class="modal-content size">
                                    <div class="modal-head d-flex align-items-center justify-content-between">
                                        <h1 class="invoice-font">Invoice</h1>
                                        <div class="cursor modal-cross" data-modal-close>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36" fill="none">
                                                <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                                                <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="modal-body">
                                        <div class="invoice-reference d-flex align-items-center justify-content-between">
                                            <p class="medium-font-bold">Booking reference: FG-10294</p>
                                            <div>
                                                <p class="medium-light-font" style="color: #9D9B98;">
                                                    02/12/2025
                                                    &nbsp;&nbsp;
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none">
                                                        <path d="M0.5 15.5V17C0.5 17.3978 0.643668 17.7794 0.8994 18.0607C1.15513 18.342 1.50198 18.5 1.86364 18.5H14.1364C14.498 18.5 14.8449 18.342 15.1006 18.0607C15.3563 17.7794 15.5 17.3978 15.5 17V15.5" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                        <path d="M7.99997 0.5V12.875M12.0909 8.75L7.99997 13.25L3.90906 8.75" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </p>
                                            </div>
                                        </div>

                                        <hr class="mt-3" style="border-top: 1px solid #E2E2E2;">

                                        <div class="name-svg d-flex align-items-center gap-20 mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="36" viewBox="0 0 32 36" fill="none">
                                                <ellipse cx="17.3668" cy="18.0807" rx="10.2458" ry="9.64315" fill="white" />
                                                <path d="M16.8932 0.202494C16.6132 0.0698256 16.3132 0 15.9998 0C15.6865 0 15.3865 0.0698256 15.1065 0.202494L2.55333 5.78156C1.08668 6.43094 -0.00663626 7.94615 3.03229e-05 9.77559C0.0333633 16.7023 2.75333 29.3756 14.2399 35.1362C15.3532 35.6949 16.6465 35.6949 17.7598 35.1362C29.2463 29.3756 31.9663 16.7023 31.9996 9.77559C32.0063 7.94615 30.913 6.43094 29.4463 5.78156L16.8932 0.202494ZM9.65991 19.9841C9.97991 20.0679 10.3199 20.1098 10.6666 20.1098C13.0199 20.1098 14.9332 18.1058 14.9332 15.6409V11.1721H17.8798C18.6865 11.1721 19.4265 11.6469 19.7865 12.408L20.2665 13.4065H24.5331C25.1197 13.4065 25.5997 13.9093 25.5997 14.5237V16.7581C25.5997 19.8444 23.2131 22.3442 20.2665 22.3442H17.0665V25.8844C17.0665 26.3941 16.6732 26.813 16.1798 26.813C16.0598 26.813 15.9398 26.7851 15.8332 26.7362L9.25325 23.7826C8.81326 23.5871 8.53326 23.1332 8.53326 22.6375C8.53326 22.4419 8.57326 22.2534 8.65993 22.0789L9.65991 19.9841ZM9.59992 11.1721H12.7999V15.6409C12.7999 16.8769 11.8466 17.8754 10.6666 17.8754C9.48658 17.8754 8.53326 16.8769 8.53326 15.6409V12.2893C8.53326 11.6748 9.01326 11.1721 9.59992 11.1721ZM18.1331 14.5237C18.1331 14.2274 18.0208 13.9433 17.8207 13.7337C17.6207 13.5242 17.3494 13.4065 17.0665 13.4065C16.7836 13.4065 16.5123 13.5242 16.3123 13.7337C16.1122 13.9433 15.9998 14.2274 15.9998 14.5237C15.9998 14.82 16.1122 15.1042 16.3123 15.3137C16.5123 15.5232 16.7836 15.6409 17.0665 15.6409C17.3494 15.6409 17.6207 15.5232 17.8207 15.3137C18.0208 15.1042 18.1331 14.82 18.1331 14.5237Z" fill="#E2E2E2" />
                                            </svg>
                                            <div>
                                                <p class="medium-font-bold">Sarah’s Grooming Studio</p>
                                                <p class="medium-light-font" style="color: #9D9B98;">Sarah W.</p>
                                            </div>
                                        </div>

                                        <div class="section-title d-flex align-items-center gap-10 mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13" fill="none">
                                                <path d="M3.79476 8.7133C4.74967 9.66821 7.07214 8.89426 8.98195 6.98414C10.8921 5.07433 11.666 2.75186 10.7111 1.79695M6.60447 1.14832L7.03668 1.58084M5.09171 2.66138L5.52393 3.09359M3.79446 4.39054L4.22667 4.82276M3.36224 6.55192L3.79446 6.98414M8.98195 0.5L9.41417 0.932215M8.54974 3.0939L9.41417 3.95833M7.03699 4.60696L7.90142 5.47139M5.30782 5.9036L6.17225 6.76803" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M3.79454 10.0107C4.15265 9.65258 4.15265 9.07196 3.79454 8.71385C3.43643 8.35574 2.85581 8.35574 2.4977 8.71385L0.768577 10.443C0.410465 10.8011 0.410464 11.3817 0.768577 11.7398C1.12669 12.0979 1.7073 12.0979 2.06542 11.7398L3.79454 10.0107Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <p class="medium-light-font" style="color: #9D9B98;">Service</p>
                                        </div>

                                        <div class="serivce-name-price mt-1">
                                            <div class="service-name d-flex justify-content-between mt">
                                                <p class="medium-light-font">Full Groom <br> <span class="simple-light-font">Bella</span></p>
                                                <p class="medium-light-font" style="color: #9D9B98;">£48</p>
                                            </div>
                                        </div>

                                        <hr class="mt-3" style="border-top: 1px solid #E2E2E2;">

                                        <div class="section-title mt-3">
                                            <p class="medium-font-bold">Extras & Add-ons</p>
                                        </div>

                                        <div class="serivce-name-price mt-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="medium-light-font">Fast-Dry Service (express grooming)</p>
                                                <p class="medium-light-font">£8</p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="medium-light-font">Hypoallergenic Shampoo Upgrade</p>
                                                <p class="medium-light-font">£20</p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="medium-light-font">Anti-Itch Treatment</p>
                                                <p class="medium-light-font">£10</p>
                                            </div>
                                        </div>

                                        <hr class="mt-3" style="border-top: 1px solid #E2E2E2;">

                                        <div class="name-svg d-flex align-items-center gap-20 mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="36" viewBox="0 0 32 36" fill="none">
                                                <ellipse cx="17.3668" cy="18.0807" rx="10.2458" ry="9.64315" fill="white" />
                                                <path d="M16.8932 0.202494C16.6132 0.0698256 16.3132 0 15.9998 0C15.6865 0 15.3865 0.0698256 15.1065 0.202494L2.55333 5.78156C1.08668 6.43094 -0.00663626 7.94615 3.03229e-05 9.77559C0.0333633 16.7023 2.75333 29.3756 14.2399 35.1362C15.3532 35.6949 16.6465 35.6949 17.7598 35.1362C29.2463 29.3756 31.9663 16.7023 31.9996 9.77559C32.0063 7.94615 30.913 6.43094 29.4463 5.78156L16.8932 0.202494ZM9.65991 19.9841C9.97991 20.0679 10.3199 20.1098 10.6666 20.1098C13.0199 20.1098 14.9332 18.1058 14.9332 15.6409V11.1721H17.8798C18.6865 11.1721 19.4265 11.6469 19.7865 12.408L20.2665 13.4065H24.5331C25.1197 13.4065 25.5997 13.9093 25.5997 14.5237V16.7581C25.5997 19.8444 23.2131 22.3442 20.2665 22.3442H17.0665V25.8844C17.0665 26.3941 16.6732 26.813 16.1798 26.813C16.0598 26.813 15.9398 26.7851 15.8332 26.7362L9.25325 23.7826C8.81326 23.5871 8.53326 23.1332 8.53326 22.6375C8.53326 22.4419 8.57326 22.2534 8.65993 22.0789L9.65991 19.9841ZM9.59992 11.1721H12.7999V15.6409C12.7999 16.8769 11.8466 17.8754 10.6666 17.8754C9.48658 17.8754 8.53326 16.8769 8.53326 15.6409V12.2893C8.53326 11.6748 9.01326 11.1721 9.59992 11.1721ZM18.1331 14.5237C18.1331 14.2274 18.0208 13.9433 17.8207 13.7337C17.6207 13.5242 17.3494 13.4065 17.0665 13.4065C16.7836 13.4065 16.5123 13.5242 16.3123 13.7337C16.1122 13.9433 15.9998 14.2274 15.9998 14.5237C15.9998 14.82 16.1122 15.1042 16.3123 15.3137C16.5123 15.5232 16.7836 15.6409 17.0665 15.6409C17.3494 15.6409 17.6207 15.5232 17.8207 15.3137C18.0208 15.1042 18.1331 14.82 18.1331 14.5237Z" fill="#E2E2E2" />
                                            </svg>
                                            <div>
                                                <p class="medium-font-bold">Sarah’s Grooming Studio</p>
                                                <p class="medium-light-font">Hosted by <span style="color: #9D9B98;">Dev É.</span></p>
                                            </div>
                                        </div>

                                        <div class="section-title d-flex align-items-center gap-10 mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="11" viewBox="0 0 14 11" fill="none">
                                                <path d="M11.5364 10.6626V3.37408C11.5364 3.35658 11.5378 3.33945 11.5405 3.32267L9.5699 1.64223C9.15083 1.28539 8.86131 1.03945 8.61569 0.879105C8.37852 0.724329 8.21914 0.674815 8.06668 0.674815C7.91435 0.674815 7.75596 0.72451 7.51905 0.879105C7.27339 1.03947 6.98309 1.28519 6.56347 1.64223L4.59145 3.32267C4.59416 3.33951 4.59698 3.35651 4.59698 3.37408V10.6626C4.59668 10.8487 4.43829 11 4.24295 11C4.04774 10.9999 3.88923 10.8486 3.88893 10.6626V3.92104L3.52384 4.23341C3.37809 4.35762 3.15356 4.34465 3.02323 4.20573C2.89342 4.06691 2.90557 3.85405 3.05089 3.72994L6.09051 1.14007H6.0919C6.49749 0.794967 6.82596 0.513638 7.11801 0.32291C7.41889 0.126502 7.71759 1.97996e-07 8.06668 0C8.41573 0 8.71438 0.126494 9.01535 0.32291C9.30759 0.513684 9.63742 0.794839 10.0429 1.14007L13.0825 3.72994C13.2278 3.85405 13.2399 4.06691 13.1101 4.20573C12.9798 4.34465 12.7553 4.35762 12.6095 4.23341L12.2444 3.92104V10.6626C12.2441 10.8486 12.0856 10.9999 11.8904 11C11.6951 11 11.5367 10.8487 11.5364 10.6626Z" fill="#9D9B98" />
                                                <path d="M1.60528 5.86768C1.60528 5.61318 1.53289 5.39481 1.42988 5.24738C1.32684 5.10005 1.20713 5.03813 1.1 5.03813C0.992933 5.03822 0.873087 5.10016 0.770116 5.24738C0.667196 5.39481 0.59472 5.61331 0.59472 5.86768C0.594815 6.12214 0.667069 6.34065 0.770116 6.48798C0.873069 6.63512 0.992961 6.6959 1.1 6.69599C1.20704 6.69599 1.32689 6.63506 1.42988 6.48798C1.53293 6.34065 1.60519 6.12214 1.60528 5.86768ZM2.2 5.86768C2.19991 6.24679 2.09349 6.60246 1.90612 6.87037C1.71859 7.13851 1.43629 7.33373 1.1 7.33373C0.763946 7.33364 0.482531 7.13828 0.295037 6.87037C0.107651 6.60245 9.37987e-05 6.24681 0 5.86768C0 5.48837 0.107569 5.13178 0.295037 4.86375C0.482531 4.59592 0.764013 4.40048 1.1 4.40039C1.43624 4.40039 1.71859 4.59567 1.90612 4.86375C2.09359 5.13178 2.2 5.48837 2.2 5.86768Z" fill="#9D9B98" />
                                                <path d="M0.733337 10.6559V6.94334C0.733337 6.7535 0.8975 6.59961 1.1 6.59961C1.30251 6.59961 1.46667 6.7535 1.46667 6.94334V10.6559C1.46652 10.8456 1.30241 10.9996 1.1 10.9996C0.897595 10.9996 0.733492 10.8456 0.733337 10.6559Z" fill="#9D9B98" />
                                                <path d="M9.37895 8.19656C9.37895 7.89715 9.37764 7.70705 9.35859 7.56773C9.34094 7.43857 9.3139 7.40496 9.29753 7.38882C9.28118 7.37274 9.24726 7.34483 9.11571 7.32741C8.97402 7.30865 8.77989 7.30872 8.47525 7.30872H7.85107C7.54643 7.30872 7.35229 7.30865 7.21061 7.32741C7.07906 7.34483 7.04513 7.37274 7.02878 7.38882C7.01241 7.40497 6.98538 7.43857 6.96772 7.56773C6.94868 7.70705 6.94737 7.89715 6.94737 8.19656V10.3167H9.37895V8.19656ZM8.78869 4.7787C8.98023 4.77885 9.13577 4.93206 9.13606 5.12049C9.13606 5.30916 8.98041 5.46213 8.78869 5.46227H7.53762C7.3459 5.46213 7.19025 5.30916 7.19025 5.12049C7.19055 4.93206 7.34608 4.77885 7.53762 4.7787H8.78869ZM8.78869 2.93359L8.85789 2.94027C9.01647 2.97193 9.13606 3.11002 9.13606 3.27538C9.13606 3.44074 9.01647 3.57883 8.85789 3.61049L8.78869 3.61717H7.53762C7.3459 3.61702 7.19025 3.46405 7.19025 3.27538C7.19025 3.08671 7.3459 2.93374 7.53762 2.93359H8.78869ZM10.0737 10.3167H12.8526C13.0445 10.3167 13.2 10.4697 13.2 10.6585C13.1997 10.847 13.0443 11.0003 12.8526 11.0003H0.347368C0.155703 11.0003 0.000292572 10.847 0 10.6585C0 10.4697 0.155522 10.3167 0.347368 10.3167H6.25263V8.19656C6.25263 7.91661 6.25176 7.67231 6.27841 7.47694C6.3065 7.27152 6.37083 7.06966 6.53758 6.90552C6.70443 6.74135 6.90951 6.67815 7.11834 6.65051C7.31708 6.62423 7.56618 6.62515 7.85107 6.62515H8.47525C8.76014 6.62515 9.00924 6.62423 9.20798 6.65051C9.41681 6.67815 9.62188 6.74135 9.78873 6.90552C9.95548 7.06966 10.0198 7.27152 10.0479 7.47694C10.0746 7.67231 10.0737 7.91661 10.0737 8.19656V10.3167Z" fill="#9D9B98" />
                                            </svg>
                                            <p class="medium-light-font" style="color: #9D9B98;">Space</p>
                                        </div>

                                        <div class="serivce-name-price mt-1">
                                            <div class="service-name d-flex justify-content-between">
                                                <p class="medium-light-font">Garden / Shed <br> Half-Day (14:30 - 18:30)</p>
                                                <p class="medium-light-font">£80</p>
                                            </div>
                                        </div>

                                        <hr class="mt-3" style="border-top: 1px solid #E2E2E2;">

                                        <div class="section-title mt-3">
                                            <p class="medium-font-bold">Add-on Service</p>
                                        </div>

                                        <div class="serivce-name-price mt-3">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="medium-light-font">Storage Locker</p>
                                                <p class="medium-light-font">£5</p>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <p class="medium-light-font">Deep Clean</p>
                                                <p class="medium-light-font">£10</p>
                                            </div>
                                        </div>

                                        <hr class="mt-3" style="border-top: 1px solid #E2E2E2;">

                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <p style="color: #9D9B98;">Service:</p>
                                            <p>£48.00</p>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <p style="color: #9D9B98;">Extras & Add-ons:</p>
                                            <p>£38.00</p>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <p style="color: #9D9B98;">Space:</p>
                                            <p>£80.00</p>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <p style="color: #9D9B98;">Add-ons Services:</p>
                                            <p>£15.00</p>
                                        </div>

                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <p style="color: #9D9B98;">Promo discount:</p>
                                            <p>- £25.00</p>
                                        </div>

                                        <hr class="mt-3" style="border-top: 1px solid #E2E2E2;">

                                        <div class="d-flex align-items-center justify-content-between mt-3">
                                            <p class="medium-font-bold">Total:</p>
                                            <p class="medium-font-bold">£158.00</p>
                                        </div>

                                    </div>
                                </div>
                            </div>

                            <!-- Modal  -->

                            <div class="d-flex justify-content-center mt-4">
                                <button class="normal-font-bold btn-custom btn-no-bg">Load More</button>
                            </div>

                        </div>

                        <div class="tab-panel" id="privacy_and_permissions">

                            <h1 class="large-font">Privacy & Permissions</h1>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Profile Visibility</p>
                                    <p style="color: #9D9B98">Your profile is visible to groomers you interact with.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Data Sharing Consent</p>
                                    <p style="color: #9D9B98">Allow anonymous usage data to improve the app.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Email Marketing</p>
                                    <p style="color: #9D9B98">Receive updates and promotions via email.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">SMS Notifications</p>
                                    <p style="color: #9D9B98">Get booking reminders and offers by SMS.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Push Notifications</p>
                                    <p style="color: #9D9B98">Allow push notifications for updates.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Partner Offers</p>
                                    <p style="color: #9D9B98">Receive selected offers from trusted partners.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Analytics Tracking</p>
                                    <p style="color: #9D9B98">Allow anonymous usage data to improve the app.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <hr class="mt-5" style="border-top: 1px solid #E2E2E2;">

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5 mb-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Blocked Users</p>
                                    <p style="color: #9D9B98">You can block groomers/hosts or customers anytime from their profiles.</p>
                                </div>
                            </div>

                            <div class="block-user-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <img src="{{ asset('images/block_user_1.png') }}" class="rounded-circle" alt="">

                                    <div>
                                        <p class="dark-color-font">The Garden Grooming Spot</p>
                                        <span class="light-color-font">Chloe D.</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unblock</a>
                                </div>
                            </div>

                            <div class="block-user-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <img src="{{ asset('images/block_user_2.png') }}" class="rounded-circle" alt="">

                                    <div>
                                        <p class="dark-color-font">Sarah W.</p>
                                        <span class="light-color-font">Sarah’s Grooming Studio</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unblock</a>
                                </div>
                            </div>

                            <div class="block-user-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <img src="{{ asset('images/block_user_3.png') }}" class="rounded-circle" alt="">

                                    <div>
                                        <p class="dark-color-font">Furs & Co. Studio</p>
                                        <span class="light-color-font">Hosted by Dev É.</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unblock</a>
                                </div>
                            </div>

                            <div class="block-user-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <img src="{{ asset('images/block_user_4.png') }}" class="rounded-circle" alt="">

                                    <div>
                                        <p class="dark-color-font">Katie Z.</p>
                                        <span class="light-color-font">Includes other accounts they may have or create.</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unblock</a>
                                </div>
                            </div>

                            <div class="block-user-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <img src="{{ asset('images/block_user_5.png') }}" class="rounded-circle" alt="">

                                    <div>
                                        <p class="dark-color-font">Lorem Ipsum</p>
                                        <span class="light-color-font">Includes other accounts they may have or create.</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unblock</a>
                                </div>
                            </div>

                        </div>

                        <div class="tab-panel" id="app_and_system">

                            <h1 class="large-font">App & System Preferences</h1>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Theme</p>
                                    <p style="color: #9D9B98">Light / Dark</p>
                                </div>
                                <div>
                                    <div class="toggle-wrap mt-4">
                                        <label class="toggle-dark-light" aria-label="Toggle dark mode">
                                            <input type="checkbox" id="toggle-input">
                                            <div class="track">
                                                <div class="thumb">
                                                    <!-- Sun icon -->
                                                    <span class="icon-dark-light icon-sun">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                            <path d="M13.333 0C20.6663 0 26.667 5.99967 26.667 13.333C26.667 20.6663 20.6663 26.666 13.333 26.666C5.99984 26.6658 4.33487e-07 20.6662 0 13.333C0 5.9998 5.99984 0.000199319 13.333 0ZM13.334 18.2598C12.7817 18.2598 12.334 18.7075 12.334 19.2598V20C12.3342 20.5521 12.7818 21 13.334 21C13.8859 20.9996 14.3338 20.5519 14.334 20V19.2598C14.334 18.7077 13.886 18.2601 13.334 18.2598ZM9.85059 16.8174C9.46006 16.4269 8.82705 16.4269 8.43652 16.8174L7.91211 17.3408C7.52192 17.7313 7.52184 18.3644 7.91211 18.7549C8.30266 19.1451 8.93673 19.1453 9.32715 18.7549L9.85059 18.2314C10.2409 17.8411 10.2405 17.208 9.85059 16.8174ZM18.2314 16.8174C17.8409 16.4269 17.2079 16.4269 16.8174 16.8174C16.4269 17.2079 16.4269 17.8409 16.8174 18.2314L17.3408 18.7549C17.7314 19.145 18.3645 19.1453 18.7549 18.7549C19.1453 18.3645 19.145 17.7314 18.7549 17.3408L18.2314 16.8174ZM13.333 9.37012C11.1446 9.37029 9.37033 11.1446 9.37012 13.333C9.37012 15.5216 11.1445 17.2957 13.333 17.2959C15.5217 17.2959 17.2959 15.5217 17.2959 13.333C17.2957 11.1445 15.5216 9.37012 13.333 9.37012ZM13.333 11.3701C14.417 11.3701 15.2957 12.2491 15.2959 13.333C15.2959 14.4171 14.4171 15.2959 13.333 15.2959C12.249 15.2957 11.3701 14.417 11.3701 13.333C11.3703 12.2492 12.2492 11.3703 13.333 11.3701ZM6.66699 12.334C6.11471 12.334 5.66699 12.7817 5.66699 13.334C5.66734 13.886 6.11492 14.334 6.66699 14.334H7.4082C7.95991 14.3336 8.40785 13.8857 8.4082 13.334C8.4082 12.782 7.96012 12.3344 7.4082 12.334H6.66699ZM19.2598 12.334C18.7075 12.334 18.2598 12.7817 18.2598 13.334C18.2601 13.886 18.7077 14.334 19.2598 14.334H20C20.5519 14.3338 20.9996 13.8859 21 13.334C21 12.7818 20.5521 12.3342 20 12.334H19.2598ZM9.32715 7.91211C8.93662 7.52158 8.30263 7.52158 7.91211 7.91211C7.52158 8.30263 7.52158 8.93662 7.91211 9.32715L8.43652 9.85059C8.8271 10.2405 9.46026 10.2409 9.85059 9.85059C10.2409 9.46026 10.2405 8.8271 9.85059 8.43652L9.32715 7.91211ZM18.7549 7.91211C18.3644 7.52184 17.7313 7.52192 17.3408 7.91211L16.8174 8.43652C16.4269 8.82705 16.4269 9.46006 16.8174 9.85059C17.208 10.2405 17.8411 10.2409 18.2314 9.85059L18.7549 9.32715C19.1453 8.93673 19.1451 8.30266 18.7549 7.91211ZM13.334 5.66699C12.7817 5.66699 12.334 6.11471 12.334 6.66699V7.4082C12.3344 7.96012 12.782 8.4082 13.334 8.4082C13.8857 8.40785 14.3336 7.95991 14.334 7.4082V6.66699C14.334 6.11492 13.886 5.66734 13.334 5.66699Z" fill="white" />
                                                        </svg>
                                                    </span>
                                                    <!-- Moon icon -->
                                                    <span class="icon-dark-light icon-moon">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                            <path d="M13.333 0C20.6663 0 26.667 5.99967 26.667 13.333C26.667 20.6663 20.6663 26.666 13.333 26.666C5.9998 26.6659 4.33491e-07 20.6662 0 13.333C0 5.99977 5.9998 0.00015493 13.333 0ZM12.5518 6.68848C12.3901 6.65154 12.2207 6.66052 12.0635 6.71289L11.79 6.80859C10.4369 7.31661 9.2766 8.23138 8.47656 9.42676L8.32227 9.66895C7.57615 10.8924 7.23667 12.3187 7.35645 13.7461L7.38672 14.0303C7.56649 15.4466 8.19325 16.7746 9.18164 17.8242L9.38379 18.0303C10.4124 19.0344 11.7372 19.6971 13.1719 19.9209C13.5213 19.9728 13.8751 19.9988 14.2285 19.999C15.1819 20.0005 16.1241 19.8084 16.9961 19.4355L17.3652 19.2646C18.2161 18.8406 18.9679 18.2489 19.5742 17.5273L19.8252 17.21C19.8999 17.1066 19.9517 16.9894 19.9785 16.8662L19.9971 16.7412C20.0099 16.5728 19.9759 16.4036 19.8984 16.2529C19.821 16.1023 19.7029 15.9752 19.5576 15.8857C19.4121 15.7963 19.2437 15.7475 19.0723 15.7451C18.0871 15.7393 17.1192 15.4839 16.2627 15.0039C15.3117 14.5037 14.5076 13.7693 13.9287 12.873C13.3499 11.9767 13.0153 10.9478 12.958 9.88672C12.9329 9.21021 13.0236 8.53372 13.2256 7.88672C13.2791 7.71939 13.2846 7.5403 13.2412 7.37012C13.2086 7.2423 13.1489 7.12284 13.0674 7.01953L12.9795 6.92188C12.8618 6.80665 12.7133 6.72552 12.5518 6.68848ZM10.959 9.96094L10.96 9.97852L10.9609 9.99512C11.0372 11.4055 11.482 12.7714 12.249 13.959C13.0063 15.1312 14.0527 16.0896 15.2852 16.748V16.749C15.7317 16.9993 16.2011 17.203 16.6846 17.3604C16.6148 17.3995 16.5445 17.4388 16.4727 17.4746C15.7792 17.8202 15.0112 18.0003 14.2314 17.999H14.2295C13.974 17.9988 13.7184 17.9799 13.4658 17.9424H13.4648C12.3759 17.7691 11.3814 17.2428 10.6377 16.4531C9.89107 15.6602 9.43935 14.6469 9.34961 13.5781C9.26008 12.5096 9.53634 11.4392 10.1387 10.5391C10.3699 10.1936 10.6445 9.88066 10.9541 9.60645C10.9532 9.72442 10.9546 9.84284 10.959 9.96094Z" fill="white" />
                                                        </svg>
                                                    </span>
                                                </div>
                                            </div>
                                        </label>
                                    </div>

                                </div>
                            </div>

                            <div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
                                <div class="d-flex flex-column gap-25">
                                    <p class="bold-font">Push Notifications</p>
                                    <p style="color: #9D9B98">Push notifications are enabled.</p>
                                </div>
                                <div>
                                    <div class="toggle-switch on" id="toggle">
                                        <div class="toggle-circle">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none">
                                                <path d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z" fill="white" />
                                            </svg>
                                        </div>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="tab-panel" id="account_linking">

                            <h1 class="large-font">Account Linking</h1>

                            <div class="settings-section-content d-flex flex-column justify-content-between mt-5 mb-5 gap-25">
                                <p class="bold-font">Connected Accounts</p>

                                <div class="d-flex align-items-center justify-content-between gap-25">
                                    <p style="color: #9D9B98">Link/unlink Google, Apple, or Facebook for login convenience</p>
                                </div>
                            </div>

                            <div class="account-linking-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <div class="border-and-bg" style="border: none;">
                                        <img src="{{ asset('images/social_media/facebook.png') }}" class="social-icons" alt="">

                                    </div>
                                    <div>
                                        <p style="color:#9D9B98">Facebook Connected</p>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unlink</a>
                                </div>
                            </div>

                            <div class="account-linking-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <div class="border-and-bg">
                                        <img src="{{ asset('images/social_media/google.png') }}" class="social-icons" alt="">

                                    </div>
                                    <div>
                                        <p>Connect your Google Account</p>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unlink</a>
                                </div>
                            </div>

                            <hr class="mt-4" style="border-top: 1px solid #E2E2E2;">

                            <div class="account-linking-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <div class="border-and-bg">
                                        <img src="{{ asset('images/social_media/linkedin.png') }}" class="social-icons" alt="">

                                    </div>
                                    <div>
                                        <p>Connect your LinkedIn Account</p>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unlink</a>
                                </div>
                            </div>

                            <hr class="mt-4" style="border-top: 1px solid #E2E2E2;">

                            <div class="account-linking-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <div class="border-and-bg">
                                        <img src="{{ asset('images/social_media/twitter.png') }}" class="social-icons" alt="">

                                    </div>
                                    <div>
                                        <p>Connect your X Account</p>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unlink</a>
                                </div>
                            </div>

                            <hr class="mt-4" style="border-top: 1px solid #E2E2E2;">

                            <div class="account-linking-card d-flex align-items-center justify-content-between mt-4">
                                <div class="image-text d-flex align-items-center gap-10">
                                    <div class="border-and-bg">
                                        <img src="{{ asset('images/social_media/apple.png') }}" class="social-icons" alt="">

                                    </div>
                                    <div>
                                        <p>Connect your Apple Account</p>
                                    </div>
                                </div>
                                <div>
                                    <a href="" class="small-link-tag">Unlink</a>
                                </div>
                            </div>

                            <hr class="mt-4" style="border-top: 1px solid #E2E2E2;">

                        </div>

                        <div class="tab-panel" id="data_and_legal">

                            <h1 class="large-font">Data & Legal</h1>

                            <div class="settings-section-content d-flex flex-column justify-content-between mt-5 gap-25">
                                <p class="bold-font">Download Data</p>

                                <div class="d-flex align-items-center justify-content-between gap-25">
                                    <p style="color: #9D9B98">Download a copy of your account data.</p>
                                    <a href="" class="small-link-tag">Download Account Data</a>
                                </div>
                            </div>

                            <div class="settings-section-content d-flex flex-column justify-content-between mt-5 gap-25">
                                <p class="bold-font">Delete Data</p>

                                <div class="d-flex align-items-center justify-content-between gap-25">
                                    <p style="color: #9D9B98">Remove all stored personal data.</p>
                                    <a href="" class="small-link-tag">Delete Personal Data</a>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-1"></div>
    </div>

</div>
@endsection

@push('script')
<script src="{{ asset('js/common.js') }}"></script>

<script>
    // Tab switching
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const target = this.getAttribute('data-tab');

                // Remove active from all buttons and panels
                tabBtns.forEach(b => b.classList.remove('active'));
                tabPanels.forEach(p => p.classList.remove('active'));

                // Activate clicked button and matching panel
                this.classList.add('active');
                document.getElementById(target).classList.add('active');
            });
        });
    });

    // Toggle switches - click anywhere on the toggle to toggle it
    document.addEventListener('click', function(e) {
        const toggle = e.target.closest('.toggle-switch');
        if (!toggle) return;
        toggle.classList.toggle('on');
    });

    // Dark/Light mode
    const input = document.getElementById('toggle-input');
    if (input) {
        input.addEventListener('change', () => {
            document.body.classList.toggle('dark', input.checked);
        });
    }
</script>
@endpush
