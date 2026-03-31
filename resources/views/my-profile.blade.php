@extends('layouts.app')

@section('styles')
<style>
    body {
        background: #fff;
    }

    .profile-title {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 36px;

        font-weight: 700;

    }

    .profile-sidebar {
        width: 400px;
        height: 442px;
        background: #fff;
        padding: 33px;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.04);
    }

    .sidebar-header {
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* Optional: Make the dots look clickable */
    .dots-menu {
        cursor: pointer;
        display: flex;
        align-items: center;
        position: relative;
    }

    button.edit-button {
        display: none;
        gap: 37px;
        padding: 3px 12px;
        border-radius: 5px;
        border: 1px solid #D9D9D9;
        background: #F8F8F8;
        cursor: pointer;
        position: absolute;
        top: 0;
        right: 35px;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
    }

    button.edit-button.show {
        display: flex;

    }

    .avatar-container img {
        width: 200px;
        height: 200px;
        aspect-ratio: 1/1;
        border-radius: 144px;
        border: 5px solid #FFC97A;
        background: url(<path-to-image>) lightgray 50% / cover no-repeat;
    }

    .profile-name {
        font-family: "Playfair Display", serif;
        font-size: 24px;
        margin-bottom: 10px;
    }

    .info-block {
        width: fit-content;
        margin: 0 auto;
        /* centers the block */
        text-align: right;
        /* text starts from right */
    }

    .stats-line,
    .location-line {
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .booking-card {
        border-radius: 14px;
        border-radius: 10px;
    }

    .booking-top {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
        border-radius: 10px 10px 0 0;
        padding: 10px 22px;
    }

    .bg-top-yellow {
        background: rgba(255, 201, 122, 0.10);
    }

    .bg-top-pink {
        background: rgba(255, 168, 153, 0.10);

    }

    .bg-body-yellow {
        background: #FFFAF2;
    }

    .bg-body-pink {
        background: #FFF7F5;
    }

    .left-top {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .tag {
        background: #FFC97A;
        color: white;
        padding: 5px 12px;
        border-radius: 20px;
    }

    .type {
        color: #FFC97A;
    }

    .status {
        color: #B5CA89;
    }

    .avatar-wrap .avatar.avatar-img {
        width: 48.001px !important;
        height: 48px !important;
        aspect-ratio: 1/1 !important;
    }

    .booking-body {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 22px;

    }

    .profile {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
    }

    .view-btn {
        background: #FFC97A;
        border: none;
        color: white;
        padding: 12px 26px;
        border-radius: 25px;
        cursor: pointer;
    }

    .booking-info {
        margin-top: 14px;
        display: flex;
        gap: 28px;
        padding: 8px 0px 20px 22px
    }

    .custom.badge-shield {
        left: -9px;
        top: -4px;
    }

    /* Progress Section Layout */
    .details-completion {
        padding: 20px;
        border-radius: 12px;
        width: 1000px;
        font-family: lato;
    }

    .header-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header-row h2 {
        color: #3b3731;
        font-family: "Playfair Display";
        font-size: 28px;

        font-weight: 700;

    }

    .header-row p {
        color: #9d9b98;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    .details-grid {
        display: flex;
        gap: 20px;
        align-items: center;
    }

    /* Semi-Circle Gauge */
    .progress-container {
        width: 199px;
        height: auto;
        position: relative;
    }

    .gauge-wrapper {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 24px;
    }

    .gauge-container {
        position: relative;
        width: auto;
        height: auto;
        margin-top: -12%;
    }

    .gauge-container svg {
        width: 100%;
        height: 100%;
    }

    .gauge-label {
        position: absolute;
        top: 70%;
        left: 50%;
        transform: translate(-50%, -20%);
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 36px;
        font-weight: 600;
    }

    .gauge-min {
        position: absolute;
        bottom: -18px;
        left: 18px;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .gauge-max {
        position: absolute;
        bottom: -18px;
        right: 6px;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .controls {
        display: flex;
        align-items: center;
        gap: 12px;
        display: none;
    }

    .controls label {
        font-size: 13px;
        color: #888;
    }

    .controls input[type="range"] {
        width: 180px;
        accent-color: #ffc97a;
        cursor: pointer;
    }

    .controls span {
        font-size: 13px;
        font-weight: 500;
        color: #3d3d3d;
        min-width: 36px;
    }

    /* .gauge-wrapper {
            position: relative;
            width: 150px;
            height: 75px;
            overflow: hidden;
        }

        .gauge-body {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #fff9f0;
            border: 15px solid #fff9f0;
        }

        .gauge-fill {
            position: absolute;
            top: 0;
            left: 0;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 15px solid #ffc97a;
            border-bottom-color: transparent;
            border-left-color: transparent;
        }

        .gauge-cover {
            position: absolute;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 36px;

            font-weight: 600;

        } */

    /* Action Cards */
    .action-card {
        background: #fffaf2;
        padding: 30px 20px;
        border-radius: 12px;
        text-align: center;
        flex: 1;
        width: 296px;
        height: 135px;
    }

    .action-card h3 {
        color: #3b3731;
        text-align: center;
        font-family: Lato;
        font-size: 20px;

        font-weight: 600;

    }

    .btn-verify {
        background: #ffc97a;
        border: none;
        border-radius: 75px;
        margin-top: 15px;
        cursor: pointer;
        width: 140px;
        height: 48px;
        color: #FFF;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
    }

    .gauge-labels {
        display: flex;
        justify-content: space-between;
    }

    .gauge-labels.span {
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;

        font-weight: 600;

        padding-right: 1rem;
    }


    /* tabs section  */
    /* Tabs Navigation */
    .profile-tabs {
        display: flex;
        gap: 10px;
        background: white;
        padding: 5px;
        border-radius: 30px;
        border: 1px solid #eee;
        width: fit-content;
        margin: 30px auto;
        color: #9d9b98;
        text-align: center;
        font-family: Lato;
        font-size: 18px;

        font-weight: 400;

    }

    .tab {
        padding: 10px 25px;
        border-radius: 25px;
        border: none;
        background: none;
        cursor: pointer;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-weight: 400;
    }

    .tab.active {
        background: #ffc97a;
        color: white;
    }

    /* Add to the bottom of style.css */

    .pets-grid {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        position: relative;
        overflow: hidden;
    }

    .pet-card {
        min-width: 325px;
        border-radius: 15px;
        padding: 45px;
        position: relative;
    }

    /* Background Colors from your image */
    .orange-bg {
        background-color: #fff4e6;
    }

    .red-bg {
        background-color: #fdf2f0;
    }

    .blue-bg {
        background-color: #f0f7f9;
    }

    .pet-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 15px;
    }

    .pet-avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        border: 3px solid #ffc97a;
        object-fit: cover;
    }

    .pet-name-type h4 {
        margin: 0;
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;

        font-weight: 600;

        display: flex;
        justify-content: start;
        align-items: center;
        gap: 8px;
    }

    .pet-name-type p {
        margin: 0;
        color: #9d9b98;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    .pet-info p {
        margin: 8px 0;
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    .pet-info {
        flex-grow: 1;
    }

    .pet-card .btn-edit-outline {
        color: #3b3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;

    }

    .my-pets.btn-edit-outline {
        border: 1px solid #333;
        background: white;
        /* padding: 8px 20px; */
        border-radius: 20px;
        cursor: pointer;
        height: auto;
        width: fit-content;
    }

    .btn-add-another-pet {
        width: 80%;
        padding: 12px;
        border-radius: 30px;
        border: 1px solid #ccc;
        background: white;
        color: #3b3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;

        font-weight: 600;

        cursor: pointer;
    }

    .slider-arrow-next {
        position: absolute;
        right: 4px;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        cursor: pointer;
    }

    #edit-view {
        display: none;
    }

    .profile-details-header {
        display: flex;
        gap: 40px;
        margin-bottom: 25px;
    }

    .avatar-placeholder {
        width: 150px;
        height: 150px;
        flex-shrink: 0;
    }

    .details-completion.details-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        grid-gap: 20px;
        flex-grow: 1;
    }

    .profile-details-card {
        border-radius: 10px;
        background: #fafafa;
        height: auto;
        margin: 0 0;
        font-family: "Lato", sans-serif;
        color: #333;
        padding: 42px;
    }

    .tap-details-card {
        border-radius: 15px;
        font-family: "Lato", sans-serif;
        color: #333;
    }

    /* .profile-header {
            max-width: 1000px;
            margin: 0 auto;
        } */

    h3.section-title {
        color: #3b3731;
        font-family: Lato;
        font-size: 20px;

        font-weight: 600;

    }

    .divider {
        border: none;
        border-top: 1px solid #d4d4d4;
        margin: 13px 0 50px 0;
    }

    /* Header layout (Avatar + Grid) */
    .profile-details-header {
        display: flex;
        gap: 40px;
        align-items: flex-start;
        margin-bottom: 30px;
    }

    /* Avatar Circle */
    .avatar-placeholder {
        width: 150px;
        height: 150px;
        flex-shrink: 0;
    }

    /* The Details Grid */
    /* Ensure the grid has 3 columns to match the design */
    .profile-details-header .details-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 50px;
        flex-grow: 1;
    }

    /* Force Address Line 1 to take up the full width of the row */
    .detail-item:nth-child(4) {
        grid-column: 0 / span 3;
    }

    /* City and Postcode will naturally fall into the next row */
    .detail-item:nth-child(5) {
        grid-column: 1;
    }

    .detail-item:nth-child(6) {
        grid-column: 2;
    }

    /* Styling Labels and Text */
    .detail-item label {
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;

        font-weight: 600;

    }

    .detail-item p {
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    .bio-section label {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .bio-section p {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    textarea[name="bio"] {
        height: 80px;
        /* fixed height */
        resize: none;
        overflow: auto;
    }

    /* Edit Button Styling */
    .edit-action {
        grid-column: 3;
        /* Places button in the far right column */
        text-align: right;
    }

    .btn-edit-outline {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border-radius: 100px;
        border: 1px solid #3b3731;
        background: #fff;
        padding: 8px 16px;
        cursor: pointer;
        font-weight: 500;
        transition: all 0.2s;
        width: 143px;
        height: 48px;
    }

    .btn-edit-outline:hover {
        background: #f0f0f0;
    }

    /* Bio Section at the bottom */
    .bio-section {
        margin-top: 20px;
        border-top: 1px solid transparent;
        /* Keeps spacing consistent */
    }

    .bio-section p {
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

        letter-spacing: 0.5;
    }

    .details-grid .edit-action {
        grid-column: 3;
        /* Places button in the far right column */
        text-align: left;
    }

    .btn-edit-outline {
        border: 1px solid #333;
        background: white;
        padding: 8px 20px;
        border-radius: 100px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .bio-section {
        margin-top: 20px;
        border-top: 1px solid transparent;
        /* Keeps spacing consistent */
        padding-left: 12rem;
    }

    .profile-view .bio-section p {
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

        width: 611px;
    }

    .hidden {
        display: none !important;
    }

    .edit-layout {
        display: flex;
        gap: 50px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        flex-grow: 1;
    }

    .full-width {
        grid-column: span 2;
    }

    .form-group label {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
        margin-bottom: 5px;
    }

    .form-group input,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        background: #fff;
        font-family: "Lato", sans-serif;
    }

    /* Container for the input and icon */
    .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    /* Style the inputs to match your image */
    .input-wrapper input {
        width: 100%;
        padding: 12px 45px 12px 15px;
        /* Extra padding on the right for the icon */
        border: 1px solid #dcdcdc;
        border-radius: 8px;
        font-size: 15px;
        color: #3b3731;
        background-color: #ffffff;
        outline: none;
        transition: border-color 0.2s;
    }

    .input-wrapper input:focus {
        border-color: #c9dda0;
    }

    /* Position the checkmark */
    .input-icon {
        position: absolute;
        right: 15px;
        pointer-events: none;
        display: block;
    }

    /* HIDE icon if the input is empty (optional but professional) */
    .input-wrapper input:placeholder-shown+.input-icon {
        display: none;
    }

    .form-actions {
        grid-column: span 2;
        display: flex;
        justify-content: flex-end;
        gap: 20px;
        margin-top: 20px;
    }

    .btn-save {
        width: 325px;
        height: 48px;
        background: #ffcc80;
        border: none;
        border-radius: 25px;
        color: white;
        font-weight: bold;
        cursor: pointer;
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 700;

    }

    .btn-cancel {
        width: 295px;
        height: 48px;
        background: white;
        border: 1px solid #ddd;
        border-radius: 25px;
        cursor: pointer;
        color: #9d9b98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 500;

    }

    .site-footer {
        padding: 100px 10%;
        color: #3b3731;
        margin-top: 7rem;
    }

    /* CTA Section */
    .footer-cta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 5rem;
    }

    .cta-heading {
        color: #3b3731;
        font-family: "Playfair Display";
        font-size: 56px;

        font-weight: 900;

    }

    .cta-buttons {
        display: flex;
        gap: 15px;
    }

    .btn-outline,
    .btn-solid {
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        cursor: pointer;
        font-size: 16px;
    }

    .btn-outline {
        background: transparent;
        border: 1px solid #3b3731;
    }

    .btn-solid {
        background: #fbac83;
        /* Peach/Orange color from design */
        border: none;
        color: white;
    }

    /* Divider */
    .footer-divider {
        border: none;
        border-top: 2px solid #d1d1d1;
        margin-bottom: 40px;
    }

    /* Form Layout */
    .edit-form-container {
        padding: 20px;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-top: 20px;
    }

    .full-width {
        grid-column: span 2;
    }

    /* Segmented Control (Cat/Dog/Other) */
    .segmented-control {
        display: flex;
        background: #f5f5f5;
        border-radius: 10px;
        padding: 4px;
        gap: 5px;
    }

    .segment {
        flex: 1;
        border: none;
        padding: 10px;
        border-radius: 8px;
        background: white;
        cursor: pointer;
        font-weight: 600;
        color: #888;
    }

    .segment.active {
        background: #ffc97a;
        /* Your theme orange */
        color: white;
    }

    /* Inputs & Textareas */
    .input-field,
    .text-area {
        width: 100%;
        padding: 12px;
        border: 1px solid #eee;
        border-radius: 12px;
        margin-top: 8px;
        font-family: inherit;
    }

    .text-area {
        min-height: 100px;
        resize: vertical;
    }

    /* Action Buttons */
    .form-actions {
        display: flex;
        justify-content: space-between;
        margin-top: 30px;
        gap: 15px;
    }

    .btn-cancel {
        flex: 1;
        padding: 15px;
        border-radius: 30px;
        border: 1px solid #ddd;
        background: white;
        cursor: pointer;
    }

    .btn-save {
        flex: 1;
        padding: 15px;
        border-radius: 30px;
        border: none;
        background: #ffc97a;
        color: white;
        font-weight: bold;
        cursor: pointer;
    }

    /* Main Footer Area */
    .footer-main {
        display: grid;
        grid-template-columns: 1.5fr 1fr 1fr;
        gap: 40px;
    }

    .footer-logo {
        height: 40px;
        margin-bottom: 15px;
    }

    .footer-brand p {
        font-size: 14px;
        color: #666;
        max-width: 300px;
    }

    .link-bold {
        font-weight: bold;
        text-decoration: underline;
    }

    .footer-links {
        display: flex;
        gap: 60px;
    }

    .link-group h3 {
        font-size: 16px;
        margin-bottom: 15px;
    }

    .link-group ul {
        list-style: none;
        padding: 0;
    }

    .link-group li {
        margin-bottom: 8px;
    }

    .link-group a {
        text-decoration: none;
        color: #666;
        font-size: 14px;
    }

    /* Social and App Info */
    .footer-social {
        text-align: right;
    }

    .social-icons {
        display: flex;
        justify-content: flex-end;
        gap: 15px;
        font-size: 20px;
        flex-direction: column;
    }

    .app-info {
        font-size: 14px;
        color: #666;
    }

    /* .favourites {
            max-width: 1000px;
            margin: auto;
        } */

    h2 {
        margin: 20px 0;
        font-weight: 600;
    }

    /* TOP BAR */
    .top-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 10px;
    }

    .tabs button {
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        border: 1px solid #d4d4d4;
        background: #ffffff;
        cursor: pointer;
        margin-right: 10px;
        color: #9d9b98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 500;

    }

    .tabs .active {
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 600;

        background: #ffc97a;
        color: #fff;
        border: 1px solid #FFC97A;
    }

    .search-area {
        position: relative;
        /* Acts as the anchor for the icon */
        display: flex;
        align-items: center;
        width: 405px;
        /* Matches your input width */
    }

    .search-area input {
        padding: 10px 15px 10px 12px;
        /* Increased left padding (40px) to make room for the icon */
        border-radius: 10px;
        border: 1px solid #ddd;
        width: 405px;
        height: 48px;
        color: #333;
        /* Changed from d4d4d4 to be visible; use d4d4d4 for placeholder only if preferred */
        font-family: "Lato", sans-serif;
        font-size: 16px;
        outline: none;
    }

    /* Position the icon inside the input */
    .search-icon {
        position: absolute;
        left: 23rem;
        /* Distance from the left border */
        pointer-events: none;
        /* Allows clicks to pass through to the input */
    }

    .feature-section {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .feature-heading h2 {
        color: #3b3731;
        font-family: "Playfair Display";
        font-size: 28px;

        font-weight: 600;

    }

    .filter {
        border: 1px solid #fbac83;
        background: transparent;
        padding: 10px 15px;
        border-radius: 20px;
        cursor: pointer;
        color: #fbac83;
        text-align: center;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

    }

    /* TAG */
    .modal-tags .tag {
        display: inline-block;
        background: #fbac83;
        padding: 6px 4px;
        border-radius: 20px;
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

        width: 105px;
        height: 32px;
        margin-bottom: 2rem;
    }

    /* GRID */
    .groomer-grid {
        margin-top: 25px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(295px, 1fr));
        gap: 3rem;
        justify-content: center;
    }

    /* CARD */
    .balance-cards .card {
        border-radius: 16px;
        width: 190px;
        height: 150px;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .card-body {
        background: #FAFAFA;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        padding: 25px 20px;
        border-radius: 12px;
        flex-grow: 1;
    }

    .card-img {
        position: relative;
    }

    .left-svg {
        left: 2%;
    }

    .card-img img {
        width: 100%;
        height: 160px;
        object-fit: cover;
        border-radius: 12px;
    }

    .card-img span {
        width: 36px;
        height: 36px;
        aspect-ratio: 1/1;
        border-radius: 100px;
        border: 1px solid #eae8e5;
        background: #fff;
    }

    .delete {
        position: absolute;
        top: -8px;
        right: -6px;
        padding: 10px;
    }

    /* .group-icon {
            margin: 22px 0;
            width: 82px;
            height: 24px;
        } */

    /* LABELS */
    /* .labels {
        margin: 25px 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 10rem;
        }

        .labels span {
        background: #ffa899;
        padding: 5px 10px;
        border-radius: 12px;
        margin-right: 5px;
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

        width: 107px;
        height: 24px;
        } */

    .card .labels {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .labels span {
        margin: 5% 0 5% 0;
        padding: 5px 10px;
        border-radius: 100px;
        background: #ffa899;
        color: white;
    }

    .plus {
        border-radius: 100px;
        border: 1px solid #E6E6E5;
        background: #FFF !important;
    }

    .card-title h3 {
        color: #3b3731;
        font-family: Lato;
        font-size: 20px;

        font-weight: 600;

    }

    .card-title p {
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;

        font-weight: 400;

    }

    /* TEXT */
    .sub {
        color: #888;
        font-size: 14px;
        margin-bottom: 8px;
    }

    .meta {
        display: flex;
        justify-content: space-between;
        margin: 2px 0;
    }

    .meta-location {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .meta-location span {
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
    }

    .meta-rating span {
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

        align-items: center;
        margin-right: 10px;
    }

    .meta-rating strong {
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

    }

    .card-review {
        width: 246px;
    }

    .review {
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
        margin: 10px 0;
        min-height: 34px;
    }

    /* BOTTOM */
    .bottom {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 12px 0;
    }

    .bottom p {
        color: #3b3731;
        text-align: right;
        font-family: Lato;
        font-size: 20px;

        font-weight: 600;

    }

    .bottom p b {
        color: #3b3731;
        font-family: Lato;
        font-size: 20px;

        font-weight: 600;

        text-decoration-line: underline;
        text-decoration-style: solid;
        text-decoration-skip-ink: auto;
        text-decoration-thickness: auto;
        text-underline-offset: auto;
        text-underline-position: from-font;
    }

    .bottom p strong {
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;

        font-weight: 600;

    }

    .arrow {
        background: #cfe3a3;
        border: none;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        cursor: pointer;
    }

    /* BUTTONS */
    .book {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 20px;
        background: #cbdce8;
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 18px;

        font-weight: 600;

        cursor: pointer;
    }

    .card-profile {
        width: 100%;
        padding: 12px;
        border: none;
        border-radius: 20px;
        background: #d8e8b7;
        cursor: pointer;
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 18px;

        font-weight: 600;

    }

    /* SPACE SWITCH */
    .space-switch {
        margin: 20px 0;
    }

    .space-switch button {
        color: #9d9b98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 500;

    }

    .switch {
        border: none;
        padding: 10px 20px;
        border-radius: 20px;
        background: #eee;
        margin-right: 10px;
        cursor: pointer;
    }

    .switch.active {
        background: #ffc97a;
        color: #fff;
    }

    .favourite-spaces h2 {
        color: #3b3731;
        font-family: "Playfair Display";
        font-size: 28px;

        font-weight: 600;

    }

    .favourite-spaces .tag {
        display: inline-block;
        background: #fbac83;
        padding: 6px 2px;
        border-radius: 20px;
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

        width: 127px;
        height: 32px;
        margin-bottom: 2rem;
    }

    /* GRID */
    .spaces-grid {
        margin-top: 25px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(295px, 1fr));
        gap: 3rem;
    }

    /* CARD */
    .space-card {
        border-radius: 16px;
    }

    /* LABEL */
    .label {
        display: inline-block;
        background: #ffd9cc;
        color: #ff7a5c;
        padding: 5px 12px;
        border-radius: 14px;
        font-size: 12px;
        margin: 10px 0;
    }

    /* FEATURES */
    .features {
        margin: 12px 0;
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        max-width: 80%;
        width: 100%;
    }

    .features span {
        background: rgba(59, 55, 49, 0.13);
        padding: 6px 10px;
        border-radius: 14px;
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

    }

    .sub-title h3 {
        color: #3b3731;
        font-family: Lato;
        font-size: 20px;

        font-weight: 600;

    }

    .sub {
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;

        font-weight: 400;

    }

    .sub-title strong {
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;

        font-weight: 400;

    }

    /* REVIEWS SECTION */
    /* .reviews-section {
            max-width: 1000px;
            margin: 0 auto;
        } */

    .reviews-section hr {
        border: none;
        border-top: 1px solid #d4d4d4;
        margin: 15px 0 25px;
    }

    .review-group {
        display: none;
    }

    .review-group.active {
        display: block;
    }

    /* STATS */
    .review-stats {
        display: flex;
        gap: 20px;
        margin-bottom: 35px;
    }

    .stat-card {
        background: #f7f7f7;
        padding: 45px;
        border-radius: 14px;
        min-width: 230px;
    }

    .stat-card.light {
        background: transparent;
    }

    .stat-title {
        color: #3b3731;
        font-family: Lato;
        font-size: 20px;

        font-weight: 600;

        margin-bottom: 10px;
    }

    .stat-card h3 {
        color: #3B3731;
        font-family: Lato;
        font-size: 30px;

        font-weight: 600;

    }

    .stat-card span {
        color: #9d9b98;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    /* FILTER BAR */
    .review-filters {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 28px;
    }

    .left-filters {
        display: flex;
        gap: 10px;
    }

    .left-filters .pill {
        border: 1px solid #ddd;
        background: #fff;
        padding: 10px 16px;
        border-radius: 20px;
        cursor: pointer;
        color: #fff;
        /* REMOVE THIS */
    }

    .left-filters .pill {
        border: 1px solid #ddd;
        background: #fff;
        padding: 10px 16px;
        border-radius: 20px;
        cursor: pointer;
        color: #333;
    }

    .left-filters .pill {
        border: 1px solid #ddd;
        background: #fff;
        padding: 10px 16px;
        border-radius: 20px;
        cursor: pointer;
        color: #9d9b98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    .left-filters .pill-1 {
        border: 1px solid #ddd;
        background: #fff;
        padding: 10px 16px;
        border-radius: 20px;
        cursor: pointer;
        color: #9d9b98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    .left-filters .pill-3 {
        border: 1px solid #ddd;
        background: #fff;
        padding: 10px 16px;
        border-radius: 20px;
        cursor: pointer;
        color: #9d9b98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    .pill.active {
        background: #ffc97a;
        color: #fff;
        border: none;
    }

    /* RIGHT SIDE */
    .right-filters {
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: space-between;
    }

    .right-filters input {
        padding: 10px 15px;
        border-radius: 10px;
        border: 1px solid #ddd;
        width: 380px;
    }

    .filter-btn {
        border: 1px solid #fbac83;
        background: transparent;
        padding: 7px 16px;
        border-radius: 20px;
        cursor: pointer;
        color: #fbac83;
    }

    /* TITLE */

    .all-reviews {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .all-reviews h3 {
        margin-top: 10px;
        color: #3b3731;
        font-family: "Playfair Display";
        font-size: 28px;

        font-weight: 600;

    }

    .reviews-tag {
        margin-top: 1.5rem;
    }

    .reviews-tag span {
        display: inline-block;
        background: #fbac83;
        padding: 6px 0px;
        border-radius: 20px;
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

        width: 127px;
        height: 32px;
        margin-bottom: 2rem;
    }

    /* REVIEW LIST */
    .reviews-list {
        margin-top: 25px;
    }

    /* REVIEW CARD */
    .review-item {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        padding: 25px 0;
        border-bottom: 1px solid #eee;
    }

    .review-item:last-child {
        border: none;
    }

    /* LEFT SIDE */
    .review-left {
        display: flex;
        gap: 15px;
        min-width: 230px;
    }

    .avatar {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        object-fit: cover;
    }

    .review-user h4 {
        margin-bottom: 3px;
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;

        font-weight: 600;

    }

    .review-user p {
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;

        font-weight: 400;

    }

    .review-meta {
        margin-top: 6px;
        color: #3b3731;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

    }

    /* CONTENT */

    .review-top {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 23px;
    }

    .date {
        color: #9d9b98;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

    }

    .badge {
        border-radius: 100px;
        background: #f2f6f9;
        color: #a8c9e2;
        text-align: center;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

        padding: 2px 10px;
    }

    /* TEXT */
    .review-text {
        color: #3b3731;
        font-family: Lato;
        font-size: 18px;

        font-weight: 400;

        margin-bottom: 25px;
        width: 674px;
    }

    /* TAGS */
    .review-tags {
        display: flex;
        gap: 10px;
        margin-bottom: 1rem;
    }

    .review-tags span {
        background: rgba(255, 168, 153, 0.13);
        padding: 6px 12px;
        border-radius: 14px;
        color: #fbac83;
        text-align: center;
        font-family: Lato;
        font-size: 14px;

        font-weight: 500;

    }

    /* MENU */
    .review-menu {
        position: relative;
        cursor: pointer;
        font-size: 22px;
    }

    /* DROPDOWN */
    .menu-dropdown {
        position: absolute;
        right: 0;
        top: 25px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        display: none;
        flex-direction: column;
    }

    .menu-dropdown button {
        border: none;
        background: none;
        padding: 10px 15px;
        text-align: left;
        cursor: pointer;
    }

    /* .review-menu:hover .menu-dropdown {
            display: flex;
        } */

    /* LOAD MORE */
    .load-more {
        display: block;
        margin: 30px auto;
        padding: 12px 25px;
        border-radius: 25px;
        border: 1px solid #aaa;
        background: #fff;
        cursor: pointer;
        color: #3b3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;

        font-weight: 600;

    }

    /* Jo tab select hoga uska color change ho jaye ga */
    .tab-link.active {
        border-bottom: 2px solid #ffc97a;
        /* Neeche line aa jayegi */
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 600;

    }

    /* Sections ke switch hone par halka sa fade effect */
    .tab-content {
        animation: fadeIn 0.4s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* .rewards-container {
            max-width: 1000px;
            margin: 0 auto;
        } */

    .sub-title h2 {
        color: #3b3731;
        font-family: "Playfair Display";
        font-size: 28px;

        font-weight: 600;

        margin-bottom: 5px;
    }

    .sub-grahp {
        color: #9d9b98;
        font-family: Lato;
        font-size: 18px;

        font-weight: 400;

        margin-bottom: 30px;
    }

    /* Cards Styling */
    .balance-cards {
        display: flex;
        gap: 20px;
        justify-content: center;
        margin-bottom: 40px;
    }

    .current {
        background-color: #f5f9ed;
    }

    .redeemed {
        background-color: #f2f6f9;
    }

    .amount {
        display: block;
        color: #3b3731;
        text-align: center;
        font-family: Lato;
        font-size: 30px;

        font-weight: 600;

    }

    .card-label {
        display: inline-block;
        padding: 5px 12px;
        border-radius: 14px;
        color: #3b3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;

        font-weight: 400;

        margin: 10px 0;
    }

    /* Referral Grid */
    .referral-grid {
        display: grid;
        grid-template-columns: 1.5fr 1fr;
        gap: 75px;
        margin-bottom: 50px;
    }

    .referral-grid label {
        display: block;
        margin: 15px 0 5px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    .referral-grid .copy-box {
        display: flex;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
    }

    .referral-grid .copy-box input {
        border: none;
        padding: 12px;
        flex-grow: 1;
        background: #fafafa;
        width: 609px;
        height: 48px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .copy-box button {
        background: #d4e3b5;
        border: none;
        padding: 0 20px;
        cursor: pointer;
        font-weight: bold;
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 18px;

        font-weight: 600;

    }

    .promo {
        margin-top: 2.3rem;
    }

    .promo p strong {
        color: #3b3731;
        font-family: Lato;
        font-size: 18px;

        font-weight: 700;

    }

    .promo p {
        color: #3b3731;
        font-family: Lato;
        font-size: 18px;

        font-weight: 400;

    }

    .promo-text {
        margin-right: 7rem;
        margin-top: 1rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 600;
    }

    /* Icons */
    /* .social-icons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        } */

    /* .icon {
  width: 35px;
  height: 35px;
  background: #007bff;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
} */

    /* Steps */
    /* .steps-container {
  display: flex;
  justify-content: space-between;
  text-align: center;
  border-top: 1px solid #eee;
  padding-top: 40px;

}

.step-num {
  background: #ffe0b2;
  width: 25px;
  height: 25px;
  border-radius: 50%;
  display: inline-block;
  margin-bottom: 10px;
} */

    .referral-section {
        padding: 80px 20px;
    }

    .steps-wrapper {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        max-width: 1000px;
        margin: 0 auto;
        position: relative;
    }

    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        width: 250px;
        position: relative;
        z-index: 2;
    }

    .step-circle {
        position: relative;
    }

    .step-svg {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    /* Connectors Spacing */
    .connector {
        margin-top: 70px;
        width: 120px;
        margin: auto;
    }

    .number {
        background: #ffd79b;
        color: #ffffff;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-shrink: 0;
    }


    .step-item-2 {
        flex: 3;
        width: auto;
    }

    .step-text {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .steps-wrapper {
            flex-direction: column;
            align-items: center;
            gap: 40px;
        }

        .connector {
            display: none;
            /* Mobile par lines remove kar dein */
        }
    }

    .pf-form {
        display: flex;
        gap: 40px;
        padding: 40px;
    }

    /* LEFT */

    .pf-left {
        text-align: center;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .pf-avatar {
        width: 190px;
        height: 190px;
        margin-bottom: 1.5rem;
    }

    .pf-edit-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #333;
        padding: 10px 18px;
        border-radius: 20px;
        background: white;
        cursor: pointer;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 500;
        line-height: 1;
        white-space: nowrap;
    }

    .pf-edit-btn span {
        display: flex;
        align-items: center;
        line-height: 0;
    }

    .pf-edit-btn span svg {
        display: block;
    }

    /* RIGHT */
    .pf-pet-right {
        flex: 1;
    }

    /* rows */

    .pf-form-row {
        display: flex;
        gap: 30px;
        margin-bottom: 15px;
    }

    .pf-field {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .pf-pet-field {
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .pf-pet-field label {
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 0.8rem;
    }

    .pf-field label {
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 0.8rem;
    }

    .pf-field.small {
        max-width: 100px;
    }

    /* inputs */

    .pf-pet-field>input,
    .pf-pet-field>select {
        /* width: 295px;
  height: 48px; */
        padding: 10px;
        border-radius: 10px;
        border: 1px solid #ddd;
    }

    /* toggle buttons */

    .pf-toggle-group {
        width: 295px;
        height: 48px;
        display: flex;
        border: 1px solid #d4d4d4 !important;
        background: #fff;
        border-radius: 10px;
        overflow: hidden;
    }

    .pf-toggle-group button {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 6px;
        flex: 1;
        padding: 10px 8px;
        border: none;
        background: transparent;
        cursor: pointer;
        line-height: 1;
    }

    /* Default SVG icon colour */
    .pf-toggle-group button svg {
        display: block;
        /* removes inline baseline gap */
        flex-shrink: 0;
    }

    .pf-toggle-group button svg path,
    .pf-toggle-group button svg circle,
    .pf-toggle-group button svg ellipse,
    .pf-toggle-group button svg rect {
        fill: #D4D4D4;
    }

    .pf-toggle-group button.active {
        background: #FFC97A;
        border-radius: 0;
        color: #fff;
    }

    .pf-toggle-group button:first-child.active {
        border-radius: 8px 0 0 8px;
    }

    .pf-toggle-group button:last-child.active {
        border-radius: 0 8px 8px 0;
    }

    .pf-toggle-group button:only-child.active {
        border-radius: 8px;
    }

    .pf-toggle-group button.active p {
        color: #fff;
    }

    .pf-toggle-group button.active svg path,
    .pf-toggle-group button.active svg circle,
    .pf-toggle-group button.active svg ellipse,
    .pf-toggle-group button.active svg rect {
        fill: #fff;
    }

    .pf-toggle-group button:not(:last-child) {
        border-right: 1px solid #d4d4d4;
    }

    /* Default text colour */
    .pf-toggle-group button p {
        color: #D4D4D4;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 1;
        margin: 0;
    }

    .pf-toggle-group .active {
        background: #ffc97a;
        /* border: 1px solid #d4d4d4; */
        color: #fff;
    }

    /* radio */

    .radio-group {
        display: flex;
        gap: 10px;
        margin-top: 10px;
        max-width: 100px;
    }

    .radio-group label {
        color: #9d9b98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        display: flex;
        gap: 12px;
    }

    .input-box {
        position: relative;
        width: 295px;
        display: inline-flex;
        align-items: center;
    }

    .input-box input {
        width: 100%;
        height: 48px;
        border: 1px solid #ddd;
        border-radius: 10px;
        padding: 0 40px 0 20px;
    }

    /* SVG positioning */

    .check-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
    }

    .arrow-icon {
        position: absolute;
        right: 37%;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .number-icon {
        position: absolute;
        right: 14px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        pointer-events: auto;
        z-index: 2;
    }

    .pf-pet-field.small .input-box {
        position: relative;
        width: 85px;
    }

    .pf-pet-field option {
        width: 295px;
    }

    .input-group {
        margin-bottom: 25px;
    }

    .pf-section-label {
        display: block;
        margin-bottom: 12px;
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-top: 1rem;
    }

    /* Radio Buttons Styling */
    .pf-radio-group {
        display: flex;
        gap: 20px;
        margin-bottom: 1rem;
    }

    .pf-radio-item {
        display: flex;
        align-items: center;
        cursor: pointer;
        color: #9d9b98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .pf-radio-item input {
        display: none;
    }

    .pf-custom-radio {
        width: 20px;
        height: 20px;
        border: 1px solid #eae8e5;
        background: #fff;
        border-radius: 50%;
        margin-right: 10px;
        display: inline-block;
        position: relative;
    }

    .pf-radio-item input:checked+.custom-radio {
        border-color: #ccc;
    }

    .pf-radio-item input:checked+.custom-radio::after {
        content: "";
        width: 12px;
        height: 12px;
        background: #ffc97a;
        border-radius: 50%;
        position: absolute;
        top: 49%;
        left: 49%;
        transform: translate(-50%, -50%);
    }

    /* Textarea Styling */
    .pf-textarea {
        width: 100%;
        min-height: 80px;
        border-radius: 10px;
        padding: 15px;
        font-size: 16px;
        font-weight: 400;
        outline: none;
        line-height: 1.4;
        resize: none;

    }

    .pf-textarea::placeholder {
        white-space: pre-line;
    }

    .pf-textarea:focus {
        border-color: #b0b0b0;
    }

    .pf-gallery-container {
        color: #444;
    }

    .pf-gallery-container h3 {
        margin-top: 1rem;
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .pf-subtitle {
        color: #9d9b98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin-bottom: 25px;
    }

    /* Photo Grid */
    .pf-photo-grid {
        display: flex;
        gap: 20px;
        margin-bottom: 30px;
    }

    .pf-photo-card {
        flex: 1;
        background: #fafafa;
        border-radius: 12px;
        width: 225px;
        height: 225px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }

    .pf-upload-btn {
        border-radius: 96px;
        border: 1px solid #3b3731;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        width: 162px;
        height: 48px;
        color: #3b3731;
        font-weight: 600;
        font-size: 16px;
    }

    /* Notes Area */
    .pf-notes-section {
        margin-top: 20px;
    }

    .pf-notes-section label {
        display: block;
        color: #3b3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 10px;
    }

    .pf-notes-section label span {
        color: #9d9b98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    textarea {
        width: 100%;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        padding: 15px;
        min-height: 100px;
        outline: none;
        font-size: 14px;
    }

    /* Footer Buttons */
    .pf-action-footer {
        display: flex;
        gap: 20px;
        margin-top: 30px;
    }

    .btn-cancel,
    .btn-save {
        flex: 1;
        padding: 15px;
        border-radius: 30px;
        border: none;
        cursor: pointer;
    }

    .btn-cancel {
        background: white;
        border: 1px solid #e0e0e0;
        color: #9d9b98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .btn-save {
        color: #fff;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 700;
        line-height: normal;
        background-color: #ffc97a;
    }

    .select-wrapper {
        position: relative;
        width: 295px;
    }

    .select-wrapper select {
        width: 100%;
        height: 48px;
        padding: 0 15px;
        border: 1px solid #d4d4d4;
        border-radius: 10px;
        appearance: none;
        -webkit-appearance: none;
        -moz-appearance: none;
        background-color: white;
        font-family: "Lato", sans-serif;
        color: #3b3731;
        outline: none;
        cursor: pointer;
    }

    .select-icon {
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
    }

    .select-wrapper select:hover {
        border-color: #ffc671;
    }

    .suggestions {
        display: none;
        position: absolute;
        background-color: #fff;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        padding: 5px;
        z-index: 1;
    }

    .suggestion {
        cursor: pointer;
        padding: 5px;
    }

    .suggestion:hover {
        background-color: #f5f5f5;
    }

    /* Keep dropdown opening downward (default component behavior) */
    .bc-wrapper .bc-dropdown {
        top: 100% !important;
        bottom: auto !important;
    }

    /* Constrain the birthday picker so it cannot spill into the next column */
    .birthday-input-box {
        width: 250px;
        max-width: 250px;
    }

    .birthday-input-box .bc-wrapper {
        width: 250px;
        max-width: 250px;
    }

    .birthday-input-box .bc-wrapper .bc-dropdown {
        width: 250px;
        max-width: 250px;
        left: 0;
        right: auto;
    }

    /* Keep birthday dropdown under the Sex field for this specific row */
    .birthday-input-box .bc-wrapper .bc-dropdown {
        z-index: 10 !important;
    }

    /* Make sure Sex content is above the dropdown */
    .pf-pet-field.sex-field {
        position: relative;
        z-index: 2000000;
    }

    .sex-options {
        display: flex;
        gap: 28px;
        height: 100%;
        align-items: center;
        margin-top: 1.3rem;
    }

    /* label that wraps each radio */
    .radio--small {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        /* gap between circle and text */
        cursor: pointer;
        user-select: none;
        position: relative;
    }

    /* keep native input for accessibility but hide native radio */
    .radio--small input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 1px;
        height: 1px;
        margin: 0;
        pointer-events: none;
    }

    /* the visible circle */
    .radio--visual {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #ccc;
        box-sizing: border-box;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        transition: border-color 0.15s;
        flex: 0 0 auto;
        position: relative;
    }

    /* inner dot for checked state */
    .radio--visual::after {
        content: "";
        position: absolute;
        left: 50%;
        top: 50%;
        width: 0.9rem;
        height: 0.9rem;
        transform: translate(-50%, -50%) scale(0);
        border-radius: 50%;
        background: #FFD88C;
        transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1);
        pointer-events: none;
    }

    /* checked styles */
    .radio--small input[type="radio"]:checked+.radio--visual {
        border-color: #FFD88C;
    }

    .radio--small input[type="radio"]:checked+.radio--visual::after {
        transform: translate(-50%, -50%) scale(1);
    }

    /* label text styling */
    .radio--text {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }



    .radio--small input[type="radio"]:checked+.radio--visual::after {
        transform: translate(-50%, -50%) scale(1);
    }

    /* focus-visible for keyboard users */
    .radio--small input[type="radio"]:focus+.radio--visual {
        outline: 3px solid rgba(201, 221, 160, 0.22);
        outline-offset: 2px;
    }


    /* Chrome, Safari, Edge */
    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type="number"] {
        -moz-appearance: textfield;
    }
</style>
@endsection

@section('content')
<div class="container mb-5 mt-5">
    <div class="row">
        <div class="col-lg-1"></div>
        <div class="col-lg-10">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="profile-title">My Profile</h1>
                </div>
                <div class="col-lg-5">
                    <aside class="profile-sidebar mt-5">
                        <div class="sidebar-header">
                            <div class="dots-menu">
                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="5" viewBox="0 0 25 5"
                                    fill="none">
                                    <circle cx="2.5" cy="2.5" r="2.5" fill="#3B3731" />
                                    <circle cx="12.5" cy="2.5" r="2.5" fill="#3B3731" />
                                    <circle cx="22.5" cy="2.5" r="2.5" fill="#3B3731" />
                                </svg>
                                <button class="edit-button">Edit
                                    <svg width="12" height="11" viewBox="0 0 12 11" fill="none">
                                        <path
                                            d="M7.36765 1.59283L9.30882 3.44794M6.07353 10.25H11.25M0.897059 7.77652L0.25 10.25L2.83824 9.63163L10.3351 2.46721C10.5777 2.23528 10.714 1.92077 10.714 1.59283C10.714 1.26489 10.5777 0.950382 10.3351 0.71846L10.2238 0.6121C9.98108 0.380248 9.65198 0.25 9.30882 0.25C8.96567 0.25 8.63657 0.380248 8.39388 0.6121L0.897059 7.77652Z"
                                            stroke="#3B3731" stroke-width="0.5" stroke-linecap="round"
                                            stroke-linejoin="round" />
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <div
                            class="avatar-container d-flex justify-content-center flex-column align-items-center mt-4">

                            <img src="{{ asset('images/block_user_5.png') }}" class="avatar-img" alt="">
                            <h2 class="profile-name mt-3">Verity E. <span class="check-badge"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                        viewBox="0 0 15 15" fill="none">
                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                            d="M11.191 0.633868C11.042 0.395853 10.8226 0.210202 10.5632 0.102758C10.3038 -0.00468587 10.0173 -0.0285778 9.7437 0.0344091L7.8018 0.480493C7.60279 0.526236 7.39599 0.526236 7.19698 0.480493L5.25508 0.0344091C4.98147 -0.0285778 4.69503 -0.00468587 4.43563 0.102758C4.17623 0.210202 3.95678 0.395853 3.80783 0.633868L2.7494 2.32315C2.64139 2.49597 2.49559 2.64178 2.32278 2.75087L0.63361 3.80938C0.396021 3.9582 0.210655 4.17731 0.103241 4.43628C-0.0041733 4.69525 -0.0283068 4.98124 0.0341899 5.25456L0.480244 7.19874C0.52582 7.39742 0.52582 7.60384 0.480244 7.80252L0.0341899 9.74563C-0.0285497 10.0191 -0.00453765 10.3053 0.102888 10.5645C0.210314 10.8237 0.395816 11.043 0.63361 11.1919L2.32278 12.2504C2.49559 12.3584 2.64139 12.5042 2.75048 12.677L3.80891 14.3663C4.11348 14.8534 4.69454 15.0943 5.25508 14.9658L7.19698 14.5197C7.39599 14.474 7.60279 14.474 7.8018 14.5197L9.74479 14.9658C10.0182 15.0285 10.3044 15.0045 10.5636 14.8971C10.8228 14.7896 11.0421 14.6041 11.191 14.3663L12.2494 12.677C12.3574 12.5042 12.5032 12.3584 12.676 12.2504L14.3663 11.1919C14.6041 11.0428 14.7895 10.8233 14.8967 10.5639C15.004 10.3044 15.0277 10.0181 14.9646 9.74455L14.5196 7.80252C14.4739 7.6035 14.4739 7.39669 14.5196 7.19766L14.9657 5.25456C15.0285 4.9812 15.0047 4.69505 14.8974 4.43587C14.7902 4.17669 14.6049 3.95734 14.3673 3.8083L12.6771 2.74979C12.5045 2.64158 12.3587 2.49573 12.2505 2.32315L11.191 0.633868ZM10.6477 5.09146C10.7145 4.96862 10.731 4.82465 10.6939 4.68985C10.6567 4.55505 10.5687 4.43992 10.4483 4.3687C10.328 4.29748 10.1847 4.27571 10.0487 4.30797C9.91264 4.34023 9.79441 4.42401 9.71886 4.54169L6.89457 9.32223L5.1892 7.68911C5.1386 7.63716 5.07807 7.59593 5.0112 7.56788C4.94433 7.53984 4.87249 7.52554 4.79998 7.52586C4.72747 7.52618 4.65576 7.54109 4.58914 7.56972C4.52251 7.59835 4.46234 7.64011 4.4122 7.6925C4.36206 7.74489 4.32299 7.80684 4.29731 7.87466C4.27163 7.94248 4.25987 8.01478 4.26274 8.08724C4.2656 8.1597 4.28303 8.23084 4.31398 8.29642C4.34493 8.362 4.38878 8.42068 4.44289 8.46895L6.63968 10.5741C6.69848 10.6303 6.76922 10.6725 6.84661 10.6976C6.92401 10.7226 7.00606 10.7298 7.08665 10.7187C7.16724 10.7076 7.24427 10.6784 7.312 10.6334C7.37973 10.5883 7.4364 10.5285 7.47779 10.4585L10.6477 5.09146Z"
                                            fill="#CBDCE8" />
                                    </svg></span></h2>
                        </div>

                        <div class="info-block d-flex flex-column gap-15 mt-2">
                            <div class="d-flex flex-column gap-5">

                                <div class="stats-line">
                                    <span class="star-icon"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" viewBox="0 0 16 16" fill="none">
                                            <path
                                                d="M7.00521 0.75483C7.31833 -0.251612 8.68168 -0.251609 8.9948 0.754833L10.1663 4.52021C10.3063 4.97031 10.7079 5.27504 11.1611 5.27504H14.952C15.9653 5.27504 16.3866 6.6292 15.5668 7.25122L12.4999 9.57835C12.1333 9.85652 11.9799 10.3496 12.1199 10.7997L13.2914 14.5651C13.6045 15.5715 12.5015 16.4084 11.6818 15.7864L8.61482 13.4593C8.24821 13.1811 7.75179 13.1811 7.38518 13.4593L4.31824 15.7864C3.49848 16.4084 2.39551 15.5715 2.70863 14.5651L3.8801 10.7997C4.02013 10.3496 3.86673 9.85652 3.50012 9.57835L0.433177 7.25122C-0.38658 6.6292 0.0347219 5.27504 1.048 5.27504H4.83894C5.29209 5.27504 5.69371 4.97031 5.83374 4.52021L7.00521 0.75483Z"
                                                fill="#FFC97A" />
                                        </svg></span> <span class="normal-default-color-font">4.3</span> <span
                                        class="normal-light-color-font">(20 reviews)</span>
                                </div>
                                <div class="location-line normal-font-weight">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="16"
                                        viewBox="0 0 11 16" fill="none">
                                        <path
                                            d="M5.5 7.6C4.97904 7.6 4.47942 7.38929 4.11104 7.01421C3.74267 6.63914 3.53571 6.13043 3.53571 5.6C3.53571 5.06957 3.74267 4.56086 4.11104 4.18579C4.47942 3.81071 4.97904 3.6 5.5 3.6C6.02096 3.6 6.52058 3.81071 6.88896 4.18579C7.25734 4.56086 7.46429 5.06957 7.46429 5.6C7.46429 5.86264 7.41348 6.12272 7.31476 6.36537C7.21605 6.60802 7.07136 6.8285 6.88896 7.01421C6.70656 7.19993 6.49002 7.34725 6.2517 7.44776C6.01338 7.54827 5.75795 7.6 5.5 7.6ZM5.5 0C4.04131 0 2.64236 0.589998 1.61091 1.6402C0.579463 2.69041 0 4.11479 0 5.6C0 8.84674 3.28668 13.2886 4.77863 15.1377C5.15511 15.6043 5.84489 15.6043 6.22138 15.1377C7.71332 13.2886 11 8.84674 11 5.6C11 4.11479 10.4205 2.69041 9.38909 1.6402C8.35764 0.589998 6.95869 0 5.5 0Z"
                                            fill="#FFC97A" />
                                    </svg>
                                    London, United Kingdom
                                </div>

                            </div>
                            <p class="simple-font align-self-center">Joined Fursgo August 2025</p>
                        </div>

                    </aside>
                </div>
                <div class="col-lg-7">
                    <h2 class="medium-font mt-5">Upcoming Bookings</h2>
                    <hr class="mt-4" style="border-top: 1px solid #D4D4D4;">
                    <div class="booking-card  bg-body-yellow mt-4">

                        <div class="booking-top bg-top-yellow">
                            <div class="left-top">
                                <span class="tag light-color-font">Home Visits</span>
                                <span class="type normal-font-bold">Groomer Booking</span>
                            </div>

                            <div class="status normal-font-bold d-flex align-items-center gap-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none">
                                    <path
                                        d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z"
                                        fill="#B5CA89" />
                                </svg>
                                Confirmed
                            </div>
                        </div>

                        <div class="booking-body">

                            <div class="profile">
                                <div class="avatar-wrap">
                                    <img src="{{ asset('images/groomer-profile.png') }}" class="avatar avatar-img" alt="">
                                    <div class="custom badge-shield" title="Verified">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="20"
                                            viewBox="0 0 18 20" fill="none">
                                            <ellipse cx="9.36358" cy="9.74945" rx="5.52471" ry="5.19965"
                                                fill="white" />
                                            <path
                                                d="M9.10904 0.109186C8.95806 0.0376504 8.7963 0 8.62734 0C8.45839 0 8.29663 0.0376504 8.14565 0.109186L1.37679 3.11745C0.585956 3.4676 -0.00357837 4.28462 1.63506e-05 5.27106C0.01799 9.00598 1.48464 15.8395 7.67834 18.9457C8.27866 19.2469 8.97603 19.2469 9.57635 18.9457C15.7701 15.8395 17.2367 9.00598 17.2547 5.27106C17.2583 4.28462 16.6687 3.4676 15.8779 3.11745L9.10904 0.109186ZM5.20877 10.7755C5.38131 10.8207 5.56464 10.8433 5.75157 10.8433C7.0205 10.8433 8.05219 9.76275 8.05219 8.43369V6.02407H9.64106C10.076 6.02407 10.475 6.28009 10.6691 6.69048L10.928 7.22888H13.2286C13.5449 7.22888 13.8037 7.49996 13.8037 7.83128V9.0361C13.8037 10.7002 12.5168 12.0481 10.928 12.0481H9.2025V13.957C9.2025 14.2319 8.99041 14.4578 8.7244 14.4578C8.6597 14.4578 8.59499 14.4427 8.53748 14.4163L4.98949 12.8237C4.75224 12.7183 4.60126 12.4736 4.60126 12.2063C4.60126 12.1008 4.62283 11.9992 4.66956 11.9051L5.20877 10.7755ZM5.17641 6.02407H6.90188V8.43369C6.90188 9.1001 6.38783 9.6385 5.75157 9.6385C5.1153 9.6385 4.60126 9.1001 4.60126 8.43369V6.62647C4.60126 6.29515 4.86008 6.02407 5.17641 6.02407ZM9.77765 7.83128C9.77765 7.67152 9.71706 7.51829 9.6092 7.40532C9.50133 7.29235 9.35504 7.22888 9.2025 7.22888C9.04996 7.22888 8.90367 7.29235 8.7958 7.40532C8.68794 7.51829 8.62734 7.67152 8.62734 7.83128C8.62734 7.99105 8.68794 8.14428 8.7958 8.25725C8.90367 8.37022 9.04996 8.43369 9.2025 8.43369C9.35504 8.43369 9.50133 8.37022 9.6092 8.25725C9.71706 8.14428 9.77765 7.99105 9.77765 7.83128Z"
                                                fill="#C9DDA0" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="profile-text">
                                    <div class="normal-font-bold ">Sarah’s Grooming Studio</div>
                                    <div class="normal-light-color">Sarah W.</div>
                                </div>
                            </div>

                            <button class="view-btn medium-font-bold">View</button>

                        </div>

                        <div class="booking-info">
                            <div class="d-flex align-items-center gap-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="17" viewBox="0 0 16 17"
                                    fill="none">
                                    <path
                                        d="M4.94542 11.5544C6.23065 12.8397 9.3565 11.798 11.927 9.22713C14.4978 6.65667 15.5395 3.53082 14.2543 2.24559M8.72705 1.37259L9.30878 1.95473M6.69101 3.40904L7.27274 3.99077M4.94501 5.73636L5.52673 6.31809M4.36328 8.6454L4.94501 9.22713M11.927 0.5L12.5087 1.08173M11.3452 3.99118L12.5087 5.15463M9.30919 6.02763L10.4726 7.19109M6.98187 7.77281L8.14533 8.93627"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M4.94499 13.2998C5.42698 12.8178 5.42698 12.0364 4.94499 11.5544C4.463 11.0724 3.68153 11.0724 3.19954 11.5544L0.872287 13.8816C0.390296 14.3636 0.390296 15.1451 0.872287 15.6271C1.35428 16.1091 2.13574 16.1091 2.61773 15.6271L4.94499 13.2998Z"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span class="normal-light-color font-color">Full Groom</span>
                            </div>
                            <div class="d-flex align-items-center gap-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="17" viewBox="0 0 19 17"
                                    fill="none">
                                    <path
                                        d="M0.5 8.29554C0.5 5.20139 0.5 3.6539 1.50457 2.69308C2.50914 1.73227 4.12486 1.73145 7.35714 1.73145H10.7857C14.018 1.73145 15.6346 1.73145 16.6383 2.69308C17.642 3.65472 17.6429 5.20139 17.6429 8.29554V9.93656C17.6429 13.0307 17.6429 14.5782 16.6383 15.539C15.6337 16.4998 14.018 16.5007 10.7857 16.5007H7.35714C4.12486 16.5007 2.50829 16.5007 1.50457 15.539C0.500857 14.5774 0.5 13.0307 0.5 9.93656V8.29554Z"
                                        stroke="#3B3731" />
                                    <path d="M4.78585 1.73077V0.5M13.3573 1.73077V0.5M0.928711 5.83333H17.2144"
                                        stroke="#3B3731" stroke-linecap="round" />
                                    <path
                                        d="M14.2144 12.3975C14.2144 12.6151 14.1241 12.8238 13.9634 12.9777C13.8026 13.1315 13.5846 13.218 13.3573 13.218C13.13 13.218 12.9119 13.1315 12.7512 12.9777C12.5904 12.8238 12.5001 12.6151 12.5001 12.3975C12.5001 12.1799 12.5904 11.9712 12.7512 11.8173C12.9119 11.6634 13.13 11.577 13.3573 11.577C13.5846 11.577 13.8026 11.6634 13.9634 11.8173C14.1241 11.9712 14.2144 12.1799 14.2144 12.3975ZM14.2144 9.11543C14.2144 9.33305 14.1241 9.54175 13.9634 9.69562C13.8026 9.8495 13.5846 9.93595 13.3573 9.93595C13.13 9.93595 12.9119 9.8495 12.7512 9.69562C12.5904 9.54175 12.5001 9.33305 12.5001 9.11543C12.5001 8.89782 12.5904 8.68912 12.7512 8.53524C12.9119 8.38137 13.13 8.29492 13.3573 8.29492C13.5846 8.29492 13.8026 8.38137 13.9634 8.53524C14.1241 8.68912 14.2144 8.89782 14.2144 9.11543ZM9.92871 12.3975C9.92871 12.6151 9.83841 12.8238 9.67766 12.9777C9.51691 13.1315 9.2989 13.218 9.07157 13.218C8.84424 13.218 8.62622 13.1315 8.46548 12.9777C8.30473 12.8238 8.21443 12.6151 8.21443 12.3975C8.21443 12.1799 8.30473 11.9712 8.46548 11.8173C8.62622 11.6634 8.84424 11.577 9.07157 11.577C9.2989 11.577 9.51691 11.6634 9.67766 11.8173C9.83841 11.9712 9.92871 12.1799 9.92871 12.3975ZM9.92871 9.11543C9.92871 9.33305 9.83841 9.54175 9.67766 9.69562C9.51691 9.8495 9.2989 9.93595 9.07157 9.93595C8.84424 9.93595 8.62622 9.8495 8.46548 9.69562C8.30473 9.54175 8.21443 9.33305 8.21443 9.11543C8.21443 8.89782 8.30473 8.68912 8.46548 8.53524C8.62622 8.38137 8.84424 8.29492 9.07157 8.29492C9.2989 8.29492 9.51691 8.38137 9.67766 8.53524C9.83841 8.68912 9.92871 8.89782 9.92871 9.11543ZM5.643 12.3975C5.643 12.6151 5.55269 12.8238 5.39195 12.9777C5.2312 13.1315 5.01318 13.218 4.78585 13.218C4.55853 13.218 4.34051 13.1315 4.17976 12.9777C4.01902 12.8238 3.92871 12.6151 3.92871 12.3975C3.92871 12.1799 4.01902 11.9712 4.17976 11.8173C4.34051 11.6634 4.55853 11.577 4.78585 11.577C5.01318 11.577 5.2312 11.6634 5.39195 11.8173C5.55269 11.9712 5.643 12.1799 5.643 12.3975ZM5.643 9.11543C5.643 9.33305 5.55269 9.54175 5.39195 9.69562C5.2312 9.8495 5.01318 9.93595 4.78585 9.93595C4.55853 9.93595 4.34051 9.8495 4.17976 9.69562C4.01902 9.54175 3.92871 9.33305 3.92871 9.11543C3.92871 8.89782 4.01902 8.68912 4.17976 8.53524C4.34051 8.38137 4.55853 8.29492 4.78585 8.29492C5.01318 8.29492 5.2312 8.38137 5.39195 8.53524C5.55269 8.68912 5.643 8.89782 5.643 9.11543Z"
                                        fill="#3B3731" />
                                </svg>
                                <span class="normal-light-color font-color">18/12/2025</span>
                            </div>
                            <div class="d-flex align-items-center gap-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none">
                                    <!-- Clock circle -->
                                    <path
                                        d="M8 0.5C12.1423 0.5 15.5 3.85774 15.5 8C15.5 12.1423 12.1423 15.5 8 15.5C3.85774 15.5 0.5 12.1423 0.5 8C0.5 3.85774 3.85774 0.5 8 0.5Z"
                                        stroke="#3B3731" stroke-linecap="round" />
                                    <!-- Hour hand pointing to 6 (straight down) -->
                                    <line x1="8" y1="8" x2="8" y2="12.5" stroke="#3B3731" stroke-width="1.2"
                                        stroke-linecap="round" />
                                    <!-- Minute hand pointing to 6:30 (straight right) -->
                                    <line x1="8" y1="8" x2="12.5" y2="8" stroke="#3B3731" stroke-width="1.2"
                                        stroke-linecap="round" />
                                    <!-- Center dot -->
                                    <circle cx="8" cy="8" r="0.8" fill="#3B3731" />
                                </svg>
                                <span class="normal-light-color font-color">14:30 - 15:30 (90 mins)</span>
                            </div>
                        </div>

                    </div>
                    <div class="booking-card bg-body-pink mt-4">

                        <div class="booking-top bg-top-pink">
                            <div class="left-top">
                                <span class="tag light-color-font">Garden / Shed</span>
                                <span class="type normal-font-bold">Space Booking</span>
                            </div>

                            <div class="status normal-font-bold d-flex align-items-center gap-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none">
                                    <path
                                        d="M8 0C3.6 0 0 3.6 0 8C0 12.4 3.6 16 8 16C12.4 16 16 12.4 16 8C16 3.6 12.4 0 8 0ZM6.4 12L2.4 8L3.528 6.872L6.4 9.736L12.472 3.664L13.6 4.8L6.4 12Z"
                                        fill="#B5CA89" />
                                </svg>
                                Confirmed
                            </div>
                        </div>

                        <div class="booking-body">

                            <div class="profile">
                                <div class="avatar-wrap">

                                    <img src="{{ asset('images/space_card3.png') }}" class="avatar avatar-img" alt="">
                                    <div class="custom badge-shield" title="Verified">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="19"
                                            viewBox="0 0 18 19" fill="none">
                                            <path
                                                d="M9.00978 0.103111C8.86044 0.0355555 8.70044 0 8.53333 0C8.36622 0 8.20622 0.0355555 8.05689 0.103111L1.36179 2.94399C0.57957 3.27466 -0.00353938 4.04621 1.61724e-05 4.97777C0.0177939 8.50487 1.46846 14.9582 7.59467 17.8915C8.18845 18.176 8.87822 18.176 9.472 17.8915C15.5982 14.9582 17.0489 8.50487 17.0666 4.97777C17.0702 4.04621 16.4871 3.27466 15.7049 2.94399L9.00978 0.103111Z"
                                                fill="#CBDCE8" />
                                            <path
                                                d="M13.167 4.93799L9.50943 8.82413L13.167 4.93799ZM7.88457 8.6344C6.75073 9.06964 5.84411 8.99512 4.9375 8.63577C5.1661 11.5815 6.53951 12.7139 8.37056 13.1675C8.37056 13.1675 9.74992 12.1918 9.9488 9.87888C9.97028 9.62833 9.9808 9.50352 9.92914 9.36225C9.87702 9.22097 9.7746 9.11994 9.57024 8.9174C9.23375 8.58456 9.06596 8.41814 8.86616 8.37608C8.66637 8.33493 8.40577 8.4346 7.88457 8.6344Z"
                                                fill="#CBDCE8" />
                                            <path
                                                d="M13.167 4.93799L9.50943 8.82413M7.88457 8.6344C6.75073 9.06964 5.84411 8.99512 4.9375 8.63577C5.1661 11.5815 6.53951 12.7139 8.37056 13.1675C8.37056 13.1675 9.74992 12.1918 9.9488 9.87888C9.97028 9.62833 9.9808 9.50352 9.92914 9.36225C9.87702 9.22097 9.7746 9.11994 9.57024 8.9174C9.23375 8.58456 9.06596 8.41814 8.86616 8.37608C8.66637 8.33493 8.40577 8.4346 7.88457 8.6344Z"
                                                stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M5.62305 11.0854C5.62305 11.0854 6.76603 11.3067 7.90901 10.4243L5.62305 11.0854Z"
                                                fill="#CBDCE8" />
                                            <path
                                                d="M5.62305 11.0854C5.62305 11.0854 6.76603 11.3067 7.90901 10.4243"
                                                stroke="white" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M7.45158 6.88106C7.45158 7.03263 7.39137 7.17799 7.28419 7.28517C7.17702 7.39234 7.03165 7.45255 6.88009 7.45255C6.72852 7.45255 6.58316 7.39234 6.47598 7.28517C6.3688 7.17799 6.30859 7.03263 6.30859 6.88106C6.30859 6.72949 6.3688 6.58413 6.47598 6.47696C6.58316 6.36978 6.72852 6.30957 6.88009 6.30957C7.03165 6.30957 7.17702 6.36978 7.28419 6.47696C7.39137 6.58413 7.45158 6.72949 7.45158 6.88106Z"
                                                fill="#CBDCE8" stroke="white" />
                                            <path d="M8.59473 5.39551V5.44123V5.39551Z" fill="#CBDCE8" />
                                            <path d="M8.59473 5.39551V5.44123" stroke="white" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="profile-text">
                                    <div class="normal-font-bold ">Furs & Co. Studio</div>
                                    <p class="normal-light-color font-color">Hosted by <span
                                            class="normal-light-color">Dev É.</span></p>
                                </div>
                            </div>

                            <button class="view-btn medium-font-bold">View</button>

                        </div>

                        <div class="booking-info">
                            <div class="d-flex align-items-center gap-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="16" viewBox="0 0 12 16"
                                    fill="none">
                                    <path
                                        d="M6 0.5C7.4694 0.5 8.87214 1.04525 9.90137 2.00586C10.9293 2.96529 11.4999 4.25871 11.5 5.59961C11.5 7.10011 10.6408 8.95946 9.51855 10.7236C8.41048 12.4655 7.10434 14.0263 6.32227 14.9082C6.14625 15.1067 5.85375 15.1067 5.67773 14.9082C4.89566 14.0263 3.58952 12.4655 2.48145 10.7236C1.35919 8.95946 0.5 7.10011 0.5 5.59961C0.500111 4.25871 1.07068 2.96529 2.09863 2.00586C3.12786 1.04525 4.53061 0.5 6 0.5ZM6 3.09961C5.30978 3.09961 4.64141 3.35564 4.14355 3.82031C3.64466 4.28597 3.35753 4.92517 3.35742 5.59961C3.35742 6.27422 3.64453 6.91413 4.14355 7.37988C4.64141 7.84453 5.30979 8.09961 6 8.09961C6.34236 8.09961 6.68202 8.03695 7 7.91406C7.31807 7.7911 7.60965 7.61022 7.85645 7.37988C8.10326 7.14952 8.30086 6.87397 8.43652 6.56836C8.57224 6.26256 8.64258 5.93289 8.64258 5.59961C8.64247 4.92517 8.35534 4.28597 7.85645 3.82031C7.35859 3.35564 6.69022 3.09961 6 3.09961Z"
                                        stroke="#3B3731" />
                                </svg>
                                <span class="normal-light-color font-color">Victoria Embankment</span>
                            </div>
                            <div class="d-flex align-items-center gap-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="17" viewBox="0 0 19 17"
                                    fill="none">
                                    <path
                                        d="M0.5 8.29554C0.5 5.20139 0.5 3.6539 1.50457 2.69308C2.50914 1.73227 4.12486 1.73145 7.35714 1.73145H10.7857C14.018 1.73145 15.6346 1.73145 16.6383 2.69308C17.642 3.65472 17.6429 5.20139 17.6429 8.29554V9.93656C17.6429 13.0307 17.6429 14.5782 16.6383 15.539C15.6337 16.4998 14.018 16.5007 10.7857 16.5007H7.35714C4.12486 16.5007 2.50829 16.5007 1.50457 15.539C0.500857 14.5774 0.5 13.0307 0.5 9.93656V8.29554Z"
                                        stroke="#3B3731" />
                                    <path d="M4.78585 1.73077V0.5M13.3573 1.73077V0.5M0.928711 5.83333H17.2144"
                                        stroke="#3B3731" stroke-linecap="round" />
                                    <path
                                        d="M14.2144 12.3975C14.2144 12.6151 14.1241 12.8238 13.9634 12.9777C13.8026 13.1315 13.5846 13.218 13.3573 13.218C13.13 13.218 12.9119 13.1315 12.7512 12.9777C12.5904 12.8238 12.5001 12.6151 12.5001 12.3975C12.5001 12.1799 12.5904 11.9712 12.7512 11.8173C12.9119 11.6634 13.13 11.577 13.3573 11.577C13.5846 11.577 13.8026 11.6634 13.9634 11.8173C14.1241 11.9712 14.2144 12.1799 14.2144 12.3975ZM14.2144 9.11543C14.2144 9.33305 14.1241 9.54175 13.9634 9.69562C13.8026 9.8495 13.5846 9.93595 13.3573 9.93595C13.13 9.93595 12.9119 9.8495 12.7512 9.69562C12.5904 9.54175 12.5001 9.33305 12.5001 9.11543C12.5001 8.89782 12.5904 8.68912 12.7512 8.53524C12.9119 8.38137 13.13 8.29492 13.3573 8.29492C13.5846 8.29492 13.8026 8.38137 13.9634 8.53524C14.1241 8.68912 14.2144 8.89782 14.2144 9.11543ZM9.92871 12.3975C9.92871 12.6151 9.83841 12.8238 9.67766 12.9777C9.51691 13.1315 9.2989 13.218 9.07157 13.218C8.84424 13.218 8.62622 13.1315 8.46548 12.9777C8.30473 12.8238 8.21443 12.6151 8.21443 12.3975C8.21443 12.1799 8.30473 11.9712 8.46548 11.8173C8.62622 11.6634 8.84424 11.577 9.07157 11.577C9.2989 11.577 9.51691 11.6634 9.67766 11.8173C9.83841 11.9712 9.92871 12.1799 9.92871 12.3975ZM9.92871 9.11543C9.92871 9.33305 9.83841 9.54175 9.67766 9.69562C9.51691 9.8495 9.2989 9.93595 9.07157 9.93595C8.84424 9.93595 8.62622 9.8495 8.46548 9.69562C8.30473 9.54175 8.21443 9.33305 8.21443 9.11543C8.21443 8.89782 8.30473 8.68912 8.46548 8.53524C8.62622 8.38137 8.84424 8.29492 9.07157 8.29492C9.2989 8.29492 9.51691 8.38137 9.67766 8.53524C9.83841 8.68912 9.92871 8.89782 9.92871 9.11543ZM5.643 12.3975C5.643 12.6151 5.55269 12.8238 5.39195 12.9777C5.2312 13.1315 5.01318 13.218 4.78585 13.218C4.55853 13.218 4.34051 13.1315 4.17976 12.9777C4.01902 12.8238 3.92871 12.6151 3.92871 12.3975C3.92871 12.1799 4.01902 11.9712 4.17976 11.8173C4.34051 11.6634 4.55853 11.577 4.78585 11.577C5.01318 11.577 5.2312 11.6634 5.39195 11.8173C5.55269 11.9712 5.643 12.1799 5.643 12.3975ZM5.643 9.11543C5.643 9.33305 5.55269 9.54175 5.39195 9.69562C5.2312 9.8495 5.01318 9.93595 4.78585 9.93595C4.55853 9.93595 4.34051 9.8495 4.17976 9.69562C4.01902 9.54175 3.92871 9.33305 3.92871 9.11543C3.92871 8.89782 4.01902 8.68912 4.17976 8.53524C4.34051 8.38137 4.55853 8.29492 4.78585 8.29492C5.01318 8.29492 5.2312 8.38137 5.39195 8.53524C5.55269 8.68912 5.643 8.89782 5.643 9.11543Z"
                                        fill="#3B3731" />
                                </svg>
                                <span class="normal-light-color font-color">18/12/2025</span>
                            </div>
                            <div class="d-flex align-items-center gap-5">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none">
                                    <!-- Clock circle -->
                                    <path
                                        d="M8 0.5C12.1423 0.5 15.5 3.85774 15.5 8C15.5 12.1423 12.1423 15.5 8 15.5C3.85774 15.5 0.5 12.1423 0.5 8C0.5 3.85774 3.85774 0.5 8 0.5Z"
                                        stroke="#3B3731" stroke-linecap="round" />
                                    <!-- Hour hand pointing to 6 (straight down) -->
                                    <line x1="8" y1="8" x2="8" y2="12.5" stroke="#3B3731" stroke-width="1.2"
                                        stroke-linecap="round" />
                                    <!-- Minute hand pointing to 6:30 (straight right) -->
                                    <line x1="8" y1="8" x2="12.5" y2="8" stroke="#3B3731" stroke-width="1.2"
                                        stroke-linecap="round" />
                                    <!-- Center dot -->
                                    <circle cx="8" cy="8" r="0.8" fill="#3B3731" />
                                </svg>
                                <span class="normal-light-color font-color">14:30 - 15:30 (90 mins)</span>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="col-lg-12">
                    <section class="details-completion mt-5">
                        <div class="header-row">
                            <div>
                                <h2>Just a few more details</h2>
                                <p class="mt-1">Complete your profile to make booking quicker and easier.</p>
                            </div>
                            <div class="d-flex align-self-end gap-20">
                                <svg class="cursor" xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                    viewBox="0 0 40 40" fill="none">
                                    <g filter="url(#filter0_d)">
                                        <circle cx="20" cy="16" r="16" fill="white" />
                                    </g>
                                    <path d="M22 21L16.9657 15.9657L21.9155 11.016" stroke="#3B3731"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <defs>
                                        <filter id="filter0_d" x="0" y="0" width="40" height="40"
                                            filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                            <feColorMatrix in="SourceAlpha" type="matrix"
                                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                result="hardAlpha" />
                                            <feOffset dy="4" />
                                            <feGaussianBlur stdDeviation="2" />
                                            <feComposite in2="hardAlpha" operator="out" />
                                            <feColorMatrix type="matrix"
                                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                            <feBlend mode="normal" in2="BackgroundImageFix"
                                                result="effect1_dropShadow" />
                                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow"
                                                result="shape" />
                                        </filter>
                                    </defs>
                                </svg>
                                <svg class="cursor" xmlns="http://www.w3.org/2000/svg" width="40" height="40"
                                    viewBox="0 0 40 40" fill="none">
                                    <g filter="url(#filter0_d)">
                                        <circle cx="20" cy="16" r="16" fill="white" />
                                    </g>
                                    <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                    <defs>
                                        <filter id="filter0_d" x="0" y="0" width="40" height="40"
                                            filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                            <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                            <feColorMatrix in="SourceAlpha" type="matrix"
                                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0"
                                                result="hardAlpha" />
                                            <feOffset dy="4" />
                                            <feGaussianBlur stdDeviation="2" />
                                            <feComposite in2="hardAlpha" operator="out" />
                                            <feColorMatrix type="matrix"
                                                values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.03 0" />
                                            <feBlend mode="normal" in2="BackgroundImageFix"
                                                result="effect1_dropShadow" />
                                            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow"
                                                result="shape" />
                                        </filter>
                                    </defs>
                                </svg>
                            </div>
                        </div>

                        <div class="details-grid d-flex align-items-center justify-content-between mt-5">
                            <div class="progress-container">
                                <div class="gauge-wrapper">
                                    <div class="gauge-container">
                                        <svg viewBox="0 0 280 170" xmlns="http://www.w3.org/2000/svg">
                                            <!-- Track arc -->
                                            <path d="M 25 155 A 118 118 0 0 1 255 155" fill="none" stroke="#FAF0DC"
                                                stroke-width="20" stroke-linecap="round" />
                                            <!-- Progress arc -->
                                            <path id="progress-arc" d="M 25 155 A 118 118 0 0 1 255 155" fill="none"
                                                stroke="#ffc97a" stroke-width="20" stroke-linecap="round"
                                                stroke-dasharray="370" stroke-dashoffset="148" />
                                        </svg>
                                        <div class="gauge-label" id="gauge-label">60%</div>
                                        <div class="gauge-min">0%</div>
                                        <div class="gauge-max">100%</div>
                                    </div>

                                    <div class="controls">
                                        <label>Adjust:</label>
                                        <input type="range" min="0" max="100" value="60" id="slider" />
                                        <span id="slider-val">60%</span>
                                    </div>
                                </div>

                                <script>
                                    const arc = document.getElementById('progress-arc');
                                    const label = document.getElementById('gauge-label');
                                    const slider = document.getElementById('slider');
                                    const sliderVal = document.getElementById('slider-val');

                                    const TOTAL_LENGTH = 370;

                                    function setProgress(pct) {
                                        const offset = TOTAL_LENGTH * (1 - pct / 100);
                                        arc.style.strokeDashoffset = offset.toFixed(1);
                                        label.textContent = Math.round(pct) + '%';
                                        sliderVal.textContent = Math.round(pct) + '%';
                                    }

                                    slider.addEventListener('input', () => setProgress(Number(slider.value)));

                                    setProgress(60);
                                </script>
                            </div>

                            <div class="action-card">
                                <h3>Verify your email address</h3>
                                <button class="btn-verify">Verify Now</button>
                            </div>
                            <div class="action-card">
                                <h3>Upload a profile picture</h3>
                                <button class="btn-verify">Upload Now</button>
                            </div>
                        </div>
                    </section>
                </div>

                <div class="col-lg-12">
                    <nav class="profile-tabs mt-5">
                        <button class="tab active">My Profile</button>
                        <button class="tab">My Pets</button>
                        <button class="tab">Favourites</button>
                        <button class="tab">Reviews</button>
                        <button class="tab">Rewards</button>
                    </nav>

                    <section id="profile-section" class="profile-header mt-5">
                        <h3 class="section-title">My Profile</h3>
                        <hr class="divider mt-4">

                        <div id="profile-view" class="profile-details-card">
                            <div class="profile-details-header">
                                <div class="avatar-placeholder">
                                    <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <circle cx="50" cy="50" r="50" fill="#E6E6E6" />
                                        <circle cx="50" cy="40" r="15" fill="white" />
                                        <path d="M20 85C20 70 35 60 50 60C65 60 80 70 80 85" stroke="white"
                                            stroke-width="2" />
                                    </svg>
                                </div>

                                <div class="details-grid">
                                    <div class="detail-item">
                                        <label>Full Name</label>
                                        <p id="display-name">Verity Eve</p>
                                    </div>
                                    <div class="edit-action">
                                        <button id="edit-btn" class="btn-edit-outline">
                                            <svg width="16" height="15" viewBox="0 0 16 15" fill="none">
                                                <path
                                                    d="M10.2059 2.37997L12.8529 4.97712M8.44118 14.5H15.5M1.38235 11.0371L0.5 14.5L4.02941 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.8529 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38235 11.0371Z"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            Edit details
                                        </button>
                                    </div>
                                    <div class="detail-item"><label>Email Address</label>
                                        <p id="display-email">veve@gmail.com</p>
                                    </div>
                                    <div class="detail-item"><label>Phone Number</label>
                                        <p id="display-phone">+44 00 0000 0000</p>
                                    </div>
                                    <div class="detail-item"><label>Address Line 1</label>
                                        <p id="display-address">12 King's Road</p>
                                    </div>
                                    <div class="detail-item"><label>City</label>
                                        <p id="display-city">London</p>
                                    </div>
                                    <div class="detail-item"><label>Post Code</label>
                                        <p id="display-postcode">SW3 4JP</p>
                                    </div>
                                </div>
                            </div>
                            <div class="bio-section">
                                <label>Bio</label>
                                <p id="display-bio">Hi! I’m Sarah, and I’m a lifelong dog lover based in South
                                    London. I have two
                                    small dogs who are very much part of the family. We’re looking for calm, caring
                                    groomers who are
                                    gentle and patient, especially with nervous pups.</p>
                            </div>
                        </div>



                        <div id="edit-form-container" class="profile-details-card hidden">
                            <div class="edit-layout">
                                <div class="avatar-edit">
                                    <div class="avatar-placeholder">
                                        <svg viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="50" cy="50" r="50" fill="#E6E6E6" />
                                            <circle cx="50" cy="40" r="15" fill="white" />
                                            <path d="M20 85C20 70 35 60 50 60C65 60 80 70 80 85" stroke="white"
                                                stroke-width="2" />
                                        </svg>
                                    </div>
                                    <button class="btn-edit-outline" style="margin-top: 10px;"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="20" height="15"
                                            viewBox="0 0 20 15" fill="none">
                                            <path
                                                d="M1.25 12.5C1.25 12.8315 1.3817 13.1495 1.61612 13.3839C1.85054 13.6183 2.16848 13.75 2.5 13.75H17.5C17.8315 13.75 18.1495 13.6183 18.3839 13.3839C18.6183 13.1495 18.75 12.8315 18.75 12.5V5C18.75 4.66848 18.6183 4.35054 18.3839 4.11612C18.1495 3.8817 17.8315 3.75 17.5 3.75H16.035C15.041 3.74946 14.0878 3.35425 13.385 2.65125L12.3475 1.61625C12.1137 1.38242 11.7969 1.25073 11.4662 1.25H8.53625C8.20476 1.25007 7.88687 1.38181 7.6525 1.61625L6.6175 2.65125C6.26921 2.99964 5.85569 3.276 5.40057 3.46453C4.94544 3.65305 4.45763 3.75006 3.965 3.75H2.5C2.16848 3.75 1.85054 3.8817 1.61612 4.11612C1.3817 4.35054 1.25 4.66848 1.25 5V12.5ZM17.5 2.5C18.163 2.5 18.7989 2.76339 19.2678 3.23223C19.7366 3.70107 20 4.33696 20 5V12.5C20 13.163 19.7366 13.7989 19.2678 14.2678C18.7989 14.7366 18.163 15 17.5 15H2.5C1.83696 15 1.20107 14.7366 0.732233 14.2678C0.263393 13.7989 0 13.163 0 12.5V5C0 4.33696 0.263393 3.70107 0.732233 3.23223C1.20107 2.76339 1.83696 2.5 2.5 2.5H3.965C4.62799 2.49986 5.26377 2.23637 5.7325 1.7675L6.7675 0.7325C7.23623 0.263627 7.87201 0.000141594 8.535 0H11.465C12.128 0.000141594 12.7638 0.263627 13.2325 0.7325L14.2675 1.7675C14.7362 2.23637 15.372 2.49986 16.035 2.5H17.5Z"
                                                fill="#3B3731" />
                                            <path
                                                d="M10 11.25C10.8288 11.25 11.6237 10.9208 12.2097 10.3347C12.7958 9.74866 13.125 8.9538 13.125 8.125C13.125 7.2962 12.7958 6.50134 12.2097 5.91529C11.6237 5.32924 10.8288 5 10 5C9.1712 5 8.37634 5.32924 7.79029 5.91529C7.20424 6.50134 6.875 7.2962 6.875 8.125C6.875 8.9538 7.20424 9.74866 7.79029 10.3347C8.37634 10.9208 9.1712 11.25 10 11.25ZM10 12.5C8.83968 12.5 7.72688 12.0391 6.90641 11.2186C6.08594 10.3981 5.625 9.28532 5.625 8.125C5.625 6.96468 6.08594 5.85188 6.90641 5.03141C7.72688 4.21094 8.83968 3.75 10 3.75C11.1603 3.75 12.2731 4.21094 13.0936 5.03141C13.9141 5.85188 14.375 6.96468 14.375 8.125C14.375 9.28532 13.9141 10.3981 13.0936 11.2186C12.2731 12.0391 11.1603 12.5 10 12.5ZM16.25 5.625C16.25 5.79076 16.3158 5.94973 16.4331 6.06694C16.5503 6.18415 16.7092 6.25 16.875 6.25C17.0408 6.25 17.1997 6.18415 17.3169 6.06694C17.4342 5.94973 17.5 5.79076 17.5 5.625C17.5 5.45924 17.4342 5.30027 17.3169 5.18306C17.1997 5.06585 17.0408 5 16.875 5C16.7092 5 16.5503 5.06585 16.4331 5.18306C16.3158 5.30027 16.25 5.45924 16.25 5.625Z"
                                                fill="#3B3731" />
                                        </svg> Edit photo</button>
                                </div>

                                <form id="profile-form" class="form-grid">
                                    <div class="form-group full-width">
                                        <label>Full Name</label>
                                        <div class="input-wrapper">
                                            <input type="text" name="fullName" value="Verity Eve">
                                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                                height="19" viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Email Address</label>
                                        <div class="input-wrapper">
                                            <input type="email" name="email" value="veve@gmail.com">
                                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                                height="19" viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Phone Number</label>
                                        <div class="input-wrapper">
                                            <input type="text" name="phone" value="+440 0000 0000">
                                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                                height="19" viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Address Line 1</label>
                                        <div class="input-wrapper">
                                            <input type="text" name="address" value="12 King's Road">
                                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                                height="19" viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="form-group full-width">
                                        <label>Address Line 2</label>
                                        <div class="input-wrapper">
                                            <input type="text" name="address2" placeholder=" ">
                                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                                height="19" viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>City</label>
                                        <div class="input-wrapper">
                                            <input type="text" name="city" value="London">
                                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                                height="19" viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>Post Code</label>
                                        <div class="input-wrapper">
                                            <input type="text" name="postcode" value="SW3 4JP">
                                            <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                                height="19" viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="form-group full-width">
                                        <label>Bio</label>
                                        <textarea name="bio" rows="4"></textarea>
                                    </div>

                                    <div class="form-actions">
                                        <button type="button" id="cancel-btn" class="btn-cancel">Cancel</button>
                                        <button type="submit" class="btn-save">Save</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </section>

                    <div id="pets-view" class="tap-details-card mt-5 hidden">
                        <div class="pets-header">
                            <h3 class="section-title">My Pets</h3>
                            <hr class="divider mt-4">
                        </div>

                        <div id="pets-slider-container" class="pets-slider-container">
                            <div class="pets-grid mt-5">
                                <div class="pet-card d-flex flex-column justify-content-between orange-bg">
                                    <div class="pet-card-header">

                                        <img src="{{ asset('images/pet_details_1.png') }}" alt="Image" class="pet-avatar">
                                        <div class="pet-name-type">
                                            <h4><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                    viewBox="0 0 16 15" fill="none">
                                                    <path
                                                        d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                        stroke="#3B3731" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg> Bella</h4>
                                            <p>Rabbit • Mini Lop</p>
                                        </div>
                                    </div>
                                    <div class="pet-info">
                                        <div class="d-flex align-items-center gap-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="15"
                                                viewBox="0 0 11 15" fill="none">
                                                <path
                                                    d="M5.5 0.5C8.2836 0.5 10.5 2.64831 10.5 5.25C10.5 7.739 8.47279 9.81207 5.85938 9.98828L5.5 10.0117L5.14062 9.98828C2.52723 9.81207 0.5 7.73865 0.5 5.25C0.5 2.64831 2.7164 0.5 5.5 0.5Z"
                                                    stroke="#9D9B98" />
                                            </svg>
                                            <p>Female</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17"
                                                viewBox="0 0 15 17" fill="none">
                                                <path
                                                    d="M1.27778 11.7778V14.5C1.27778 14.9126 1.44167 15.3082 1.73339 15.5999C2.02511 15.8917 2.42077 16.0556 2.83333 16.0556H12.1667C12.5792 16.0556 12.9749 15.8917 13.2666 15.5999C13.5583 15.3082 13.7222 14.9126 13.7222 14.5V11.7778M0.5 9.83333V9.05556C0.5 8.643 0.663888 8.24734 0.955612 7.95561C1.24733 7.66389 1.643 7.5 2.05556 7.5H12.9444C13.357 7.5 13.7527 7.66389 14.0444 7.95561C14.3361 8.24734 14.5 8.643 14.5 9.05556V9.83333M7.5 5.16667V7.5M7.5 5.16667C8.48156 5.16667 9.05556 4.41378 9.05556 3.125C9.05556 1.83622 7.5 0.5 7.5 0.5C7.5 0.5 5.94444 1.83622 5.94444 3.125C5.94444 4.41378 6.51844 5.16667 7.5 5.16667Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M0.5 9.83331C0.5 10.4522 0.745833 11.0456 1.18342 11.4832C1.621 11.9208 2.21449 12.1666 2.83333 12.1666C3.45217 12.1666 4.04566 11.9208 4.48325 11.4832C4.92083 11.0456 5.16667 10.4522 5.16667 9.83331C5.16667 10.4522 5.4125 11.0456 5.85008 11.4832C6.28767 11.9208 6.88116 12.1666 7.5 12.1666C8.11884 12.1666 8.71233 11.9208 9.14992 11.4832C9.5875 11.0456 9.83333 10.4522 9.83333 9.83331C9.83333 10.4522 10.0792 11.0456 10.5168 11.4832C10.9543 11.9208 11.5478 12.1666 12.1667 12.1666C12.7855 12.1666 13.379 11.9208 13.8166 11.4832C14.2542 11.0456 14.5 10.4522 14.5 9.83331"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <p> 22/08/2020</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M4.7373 3.14703C4.7373 3.84907 5.01619 4.52235 5.51261 5.01876C6.00903 5.51518 6.68232 5.79406 7.38436 5.79406C8.08641 5.79406 8.7597 5.51518 9.25612 5.01876C9.75254 4.52235 10.0314 3.84907 10.0314 3.14703C10.0314 2.44499 9.75254 1.77171 9.25612 1.2753C8.7597 0.778883 8.08641 0.5 7.38436 0.5C6.68232 0.5 6.00903 0.778883 5.51261 1.2753C5.01619 1.77171 4.7373 2.44499 4.7373 3.14703Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M2.8269 5.79425H11.9416C12.1482 5.79422 12.3483 5.86671 12.507 5.9991C12.6657 6.13148 12.7728 6.31535 12.8098 6.51865L14.2542 14.4597C14.2774 14.5869 14.2723 14.7176 14.2394 14.8426C14.2064 14.9676 14.1464 15.0838 14.0636 15.1831C13.9807 15.2823 13.8771 15.3621 13.76 15.4169C13.643 15.4717 13.5153 15.5 13.386 15.5H1.38249C1.25323 15.5 1.12554 15.4717 1.00846 15.4169C0.891377 15.3621 0.78776 15.2823 0.704935 15.1831C0.62211 15.0838 0.5621 14.9676 0.52915 14.8426C0.496199 14.7176 0.491113 14.5869 0.514251 14.4597L1.95866 6.51865C1.99565 6.31535 2.10282 6.13148 2.26149 5.9991C2.42016 5.86671 2.62026 5.79422 2.8269 5.79425Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                            <p> 5 kg</p>
                                        </div>
                                        <div class="d-flex align-items-center gap-10">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M13.5905 8.11123L13.9601 6.73016C14.3918 5.1182 14.6084 4.31257 14.4462 3.61489C14.3176 3.0641 14.0285 2.56382 13.6155 2.17734C13.093 1.68768 12.2866 1.4718 10.6747 1.04003C9.0627 0.607553 8.25636 0.391671 7.55939 0.55394C7.0086 0.682549 6.50833 0.971618 6.12185 1.38458C5.70224 1.83207 5.4835 2.48758 5.15824 3.67851L4.98382 4.32544L4.61425 5.70651C4.18177 7.31847 3.96589 8.1241 4.12816 8.82178C4.25677 9.37257 4.54584 9.87285 4.9588 10.2593C5.48135 10.749 6.28769 10.9649 7.89966 11.3974C9.35221 11.7862 10.1507 12 10.8048 11.9192C10.8763 11.9101 10.9463 11.8977 11.0149 11.882C11.5655 11.7538 12.0658 11.4652 12.4525 11.0528C12.9421 10.5295 13.158 9.7232 13.5905 8.11123Z"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M10.8047 11.9191C10.6553 12.3768 10.3927 12.7894 10.0413 13.1186C9.51875 13.6082 8.71241 13.8241 7.10045 14.2559C5.48848 14.6876 4.68214 14.9042 3.98517 14.7413C3.43447 14.6128 2.9342 14.324 2.54763 13.9113C2.05796 13.3888 1.84137 12.5824 1.4096 10.9705L1.04003 9.5894C0.607553 7.97744 0.391671 7.1711 0.55394 6.47413C0.682549 5.92334 0.971618 5.42306 1.38458 5.03658C1.90713 4.54692 2.71347 4.33104 4.32544 3.89856C4.62948 3.81659 4.90708 3.74296 5.15823 3.67767"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M7.48902 6.21906L10.9417 7.14406M6.93359 8.29066L9.0052 8.84538"
                                                    stroke="#9D9B98" stroke-linecap="round" />
                                            </svg>
                                            <p> Nervous around hair-dryers.</p>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center mt-4">
                                        <button class="my-pets btn-edit-outline"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0
                                                    0 16 15" fill="none">
                                                <path
                                                    d="M10.2059 2.37997L12.8529 4.97712M8.44118 14.5H15.5M1.38235 11.0371L0.5 14.5L4.02941 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.8529 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38235 11.0371Z"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>Edit details</button>
                                    </div>

                                </div>

                                <div class="pet-card d-flex flex-column justify-content-between red-bg">
                                    <div class="pet-card-header">

                                        <img src="{{ asset('images/pet_details_2.png') }}" alt="Image" class="pet-avatar">
                                        <div class="pet-name-type">
                                            <h4><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                    viewBox="0 0 16 15" fill="none">
                                                    <path
                                                        d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                        stroke="#3B3731" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg> Louis</h4>
                                            <p>Dog • Labrador</p>
                                        </div>
                                    </div>
                                    <div class="pet-info">
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                viewBox="0 0 14 14" fill="none">
                                                <path
                                                    d="M4.66699 5.16699C5.14208 5.16703 5.5938 5.25271 6.02539 5.4248V5.42578C6.47992 5.60644 6.91391 5.84052 7.32715 6.12988L7.6709 6.37109L7.6748 6.36621L7.86719 6.64551C8.15425 7.06305 8.38906 7.50348 8.57227 7.96582C8.7461 8.40462 8.83297 8.85911 8.83301 9.33301C8.83301 10.5038 8.43204 11.4793 7.62305 12.2891C6.81389 13.0988 5.83895 13.5005 4.66895 13.5C3.49846 13.4994 2.52252 13.098 1.71191 12.2891C0.901734 11.4805 0.5 10.5057 0.5 9.33496C0.500055 8.1642 0.902094 7.18873 1.71191 6.37891C2.52184 5.56912 3.49688 5.16755 4.66699 5.16699Z"
                                                    stroke="#9D9B98" />
                                            </svg> Male</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="17"
                                                viewBox="0 0 15 17" fill="none">
                                                <path
                                                    d="M1.27778 11.7778V14.5C1.27778 14.9126 1.44167 15.3082 1.73339 15.5999C2.02511 15.8917 2.42077 16.0556 2.83333 16.0556H12.1667C12.5792 16.0556 12.9749 15.8917 13.2666 15.5999C13.5583 15.3082 13.7222 14.9126 13.7222 14.5V11.7778M0.5 9.83333V9.05556C0.5 8.643 0.663888 8.24734 0.955612 7.95561C1.24733 7.66389 1.643 7.5 2.05556 7.5H12.9444C13.357 7.5 13.7527 7.66389 14.0444 7.95561C14.3361 8.24734 14.5 8.643 14.5 9.05556V9.83333M7.5 5.16667V7.5M7.5 5.16667C8.48156 5.16667 9.05556 4.41378 9.05556 3.125C9.05556 1.83622 7.5 0.5 7.5 0.5C7.5 0.5 5.94444 1.83622 5.94444 3.125C5.94444 4.41378 6.51844 5.16667 7.5 5.16667Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M0.5 9.83331C0.5 10.4522 0.745833 11.0456 1.18342 11.4832C1.621 11.9208 2.21449 12.1666 2.83333 12.1666C3.45217 12.1666 4.04566 11.9208 4.48325 11.4832C4.92083 11.0456 5.16667 10.4522 5.16667 9.83331C5.16667 10.4522 5.4125 11.0456 5.85008 11.4832C6.28767 11.9208 6.88116 12.1666 7.5 12.1666C8.11884 12.1666 8.71233 11.9208 9.14992 11.4832C9.5875 11.0456 9.83333 10.4522 9.83333 9.83331C9.83333 10.4522 10.0792 11.0456 10.5168 11.4832C10.9543 11.9208 11.5478 12.1666 12.1667 12.1666C12.7855 12.1666 13.379 11.9208 13.8166 11.4832C14.2542 11.0456 14.5 10.4522 14.5 9.83331"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg> 22/08/2020</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M4.7373 3.14703C4.7373 3.84907 5.01619 4.52235 5.51261 5.01876C6.00903 5.51518 6.68232 5.79406 7.38436 5.79406C8.08641 5.79406 8.7597 5.51518 9.25612 5.01876C9.75254 4.52235 10.0314 3.84907 10.0314 3.14703C10.0314 2.44499 9.75254 1.77171 9.25612 1.2753C8.7597 0.778883 8.08641 0.5 7.38436 0.5C6.68232 0.5 6.00903 0.778883 5.51261 1.2753C5.01619 1.77171 4.7373 2.44499 4.7373 3.14703Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M2.8269 5.79395H11.9416C12.1482 5.79391 12.3483 5.86641 12.507 5.99879C12.6657 6.13117 12.7728 6.31505 12.8098 6.51835L14.2542 14.4594C14.2774 14.5866 14.2723 14.7173 14.2394 14.8423C14.2064 14.9673 14.1464 15.0835 14.0636 15.1827C13.9807 15.282 13.8771 15.3618 13.76 15.4166C13.643 15.4714 13.5153 15.4997 13.386 15.4997H1.38249C1.25323 15.4997 1.12554 15.4714 1.00846 15.4166C0.891377 15.3618 0.78776 15.282 0.704935 15.1827C0.62211 15.0835 0.5621 14.9673 0.52915 14.8423C0.496199 14.7173 0.491113 14.5866 0.514251 14.4594L1.95866 6.51835C1.99565 6.31505 2.10282 6.13117 2.26149 5.99879C2.42016 5.86641 2.62026 5.79391 2.8269 5.79395Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg> 40 kg</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M13.5905 8.11123L13.9601 6.73016C14.3918 5.1182 14.6084 4.31257 14.4462 3.61489C14.3176 3.0641 14.0285 2.56382 13.6155 2.17734C13.093 1.68768 12.2866 1.4718 10.6747 1.04003C9.0627 0.607553 8.25636 0.391671 7.55939 0.55394C7.0086 0.682549 6.50833 0.971618 6.12185 1.38458C5.70224 1.83207 5.4835 2.48758 5.15824 3.67851L4.98382 4.32544L4.61425 5.70651C4.18177 7.31847 3.96589 8.1241 4.12816 8.82178C4.25677 9.37257 4.54584 9.87285 4.9588 10.2593C5.48135 10.749 6.28769 10.9649 7.89966 11.3974C9.35221 11.7862 10.1507 12 10.8048 11.9192C10.8763 11.9101 10.9463 11.8977 11.0149 11.882C11.5655 11.7538 12.0658 11.4652 12.4525 11.0528C12.9421 10.5295 13.158 9.7232 13.5905 8.11123Z"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M10.8047 11.9191C10.6553 12.3768 10.3927 12.7894 10.0413 13.1186C9.51875 13.6082 8.71241 13.8241 7.10045 14.2559C5.48848 14.6876 4.68214 14.9042 3.98517 14.7413C3.43447 14.6128 2.9342 14.324 2.54763 13.9113C2.05796 13.3888 1.84137 12.5824 1.4096 10.9705L1.04003 9.5894C0.607553 7.97744 0.391671 7.1711 0.55394 6.47413C0.682549 5.92334 0.971618 5.42306 1.38458 5.03658C1.90713 4.54692 2.71347 4.33104 4.32544 3.89856C4.62948 3.81659 4.90708 3.74296 5.15823 3.67767"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M7.48902 6.21906L10.9417 7.14406M6.93359 8.29066L9.0052 8.84538"
                                                    stroke="#9D9B98" stroke-linecap="round" />
                                            </svg> Allergic to dust.</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <button class="my-pets btn-edit-outline"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                viewBox="0 0 16 15" fill="none">
                                                <path
                                                    d="M10.2059 2.37997L12.8529 4.97712M8.44118 14.5H15.5M1.38235 11.0371L0.5 14.5L4.02941 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.8529 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38235 11.0371Z"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>Edit details</button>
                                    </div>
                                </div>

                                <div class="pet-card d-flex flex-column justify-content-between blue-bg">
                                    <div class="pet-card-header">
                                        <img src="{{ asset('images/surf.png') }}" alt="Image" class="pet-avatar">
                                        <div class="pet-name-type">
                                            <h4><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                    viewBox="0 0 16 15" fill="none">
                                                    <path
                                                        d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                        stroke="#3B3731" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg> Surf</h4>
                                            <p>Turtle • Red-Ear</p>
                                        </div>
                                    </div>
                                    <div class="pet-info">
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="11" height="15"
                                                viewBox="0 0 11 15" fill="none">
                                                <path
                                                    d="M5.5 0.5C8.2836 0.5 10.5 2.64831 10.5 5.25C10.5 7.739 8.47279 9.81207 5.85938 9.98828L5.5 10.0117L5.14062 9.98828C2.52723 9.81207 0.5 7.73865 0.5 5.25C0.5 2.64831 2.7164 0.5 5.5 0.5Z"
                                                    stroke="#9D9B98" />
                                            </svg>
                                            Female</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="17"
                                                viewBox="0 0 15 17" fill="none">
                                                <path
                                                    d="M1.27778 11.7778V14.5C1.27778 14.9126 1.44167 15.3082 1.73339 15.5999C2.02511 15.8917 2.42077 16.0556 2.83333 16.0556H12.1667C12.5792 16.0556 12.9749 15.8917 13.2666 15.5999C13.5583 15.3082 13.7222 14.9126 13.7222 14.5V11.7778M0.5 9.83333V9.05556C0.5 8.643 0.663888 8.24734 0.955612 7.95561C1.24733 7.66389 1.643 7.5 2.05556 7.5H12.9444C13.357 7.5 13.7527 7.66389 14.0444 7.95561C14.3361 8.24734 14.5 8.643 14.5 9.05556V9.83333M7.5 5.16667V7.5M7.5 5.16667C8.48156 5.16667 9.05556 4.41378 9.05556 3.125C9.05556 1.83622 7.5 0.5 7.5 0.5C7.5 0.5 5.94444 1.83622 5.94444 3.125C5.94444 4.41378 6.51844 5.16667 7.5 5.16667Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M0.5 9.83331C0.5 10.4522 0.745833 11.0456 1.18342 11.4832C1.621 11.9208 2.21449 12.1666 2.83333 12.1666C3.45217 12.1666 4.04566 11.9208 4.48325 11.4832C4.92083 11.0456 5.16667 10.4522 5.16667 9.83331C5.16667 10.4522 5.4125 11.0456 5.85008 11.4832C6.28767 11.9208 6.88116 12.1666 7.5 12.1666C8.11884 12.1666 8.71233 11.9208 9.14992 11.4832C9.5875 11.0456 9.83333 10.4522 9.83333 9.83331C9.83333 10.4522 10.0792 11.0456 10.5168 11.4832C10.9543 11.9208 11.5478 12.1666 12.1667 12.1666C12.7855 12.1666 13.379 11.9208 13.8166 11.4832C14.2542 11.0456 14.5 10.4522 14.5 9.83331"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg> 22/08/2020</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M4.7373 3.14703C4.7373 3.84907 5.01619 4.52235 5.51261 5.01876C6.00903 5.51518 6.68232 5.79406 7.38436 5.79406C8.08641 5.79406 8.7597 5.51518 9.25612 5.01876C9.75254 4.52235 10.0314 3.84907 10.0314 3.14703C10.0314 2.44499 9.75254 1.77171 9.25612 1.2753C8.7597 0.778883 8.08641 0.5 7.38436 0.5C6.68232 0.5 6.00903 0.778883 5.51261 1.2753C5.01619 1.77171 4.7373 2.44499 4.7373 3.14703Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M2.8269 5.79419H11.9416C12.1482 5.79416 12.3483 5.86665 12.507 5.99904C12.6657 6.13142 12.7728 6.31529 12.8098 6.51859L14.2542 14.4597C14.2774 14.5869 14.2723 14.7176 14.2394 14.8425C14.2064 14.9675 14.1464 15.0838 14.0636 15.183C13.9807 15.2822 13.8771 15.3621 13.76 15.4168C13.643 15.4716 13.5153 15.5 13.386 15.5H1.38249C1.25323 15.5 1.12554 15.4716 1.00846 15.4168C0.891377 15.3621 0.78776 15.2822 0.704935 15.183C0.62211 15.0838 0.5621 14.9675 0.52915 14.8425C0.496199 14.7176 0.491113 14.5869 0.514251 14.4597L1.95866 6.51859C1.99565 6.31529 2.10282 6.13142 2.26149 5.99904C2.42016 5.86665 2.62026 5.79416 2.8269 5.79419Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg> 2 kg</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M13.5905 8.11123L13.9601 6.73016C14.3918 5.1182 14.6084 4.31257 14.4462 3.61489C14.3176 3.0641 14.0285 2.56382 13.6155 2.17734C13.093 1.68768 12.2866 1.4718 10.6747 1.04003C9.0627 0.607553 8.25636 0.391671 7.55939 0.55394C7.0086 0.682549 6.50833 0.971618 6.12185 1.38458C5.70224 1.83207 5.4835 2.48758 5.15824 3.67851L4.98382 4.32544L4.61425 5.70651C4.18177 7.31847 3.96589 8.1241 4.12816 8.82178C4.25677 9.37257 4.54584 9.87285 4.9588 10.2593C5.48135 10.749 6.28769 10.9649 7.89966 11.3974C9.35221 11.7862 10.1507 12 10.8048 11.9192C10.8763 11.9101 10.9463 11.8977 11.0149 11.882C11.5655 11.7538 12.0658 11.4652 12.4525 11.0528C12.9421 10.5295 13.158 9.7232 13.5905 8.11123Z"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M10.8047 11.9191C10.6553 12.3768 10.3927 12.7894 10.0413 13.1186C9.51875 13.6082 8.71241 13.8241 7.10045 14.2559C5.48848 14.6876 4.68214 14.9042 3.98517 14.7413C3.43447 14.6128 2.9342 14.324 2.54763 13.9113C2.05796 13.3888 1.84137 12.5824 1.4096 10.9705L1.04003 9.5894C0.607553 7.97744 0.391671 7.1711 0.55394 6.47413C0.682549 5.92334 0.971618 5.42306 1.38458 5.03658C1.90713 4.54692 2.71347 4.33104 4.32544 3.89856C4.62948 3.81659 4.90708 3.74296 5.15823 3.67767"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M7.48951 6.21906L10.9422 7.14406M6.93408 8.29066L9.00569 8.84538"
                                                    stroke="#9D9B98" stroke-linecap="round" />
                                            </svg>
                                            Loves the water.</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <button class="my-pets btn-edit-outline"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0
                                                    0 16 15" fill="none">
                                                <path
                                                    d="M10.2059 2.37997L12.8529 4.97712M8.44118 14.5H15.5M1.38235 11.0371L0.5 14.5L4.02941 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.8529 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38235 11.0371Z"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>Edit details</button>
                                    </div>

                                </div>

                                <div class="pet-card d-flex flex-column justify-content-between blue-bg">
                                    <div class="pet-card-header">
                                        <img src="{{ asset('images/surf.png') }}" alt="Image" class="pet-avatar">
                                        <div class="pet-name-type">
                                            <h4><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                    viewBox="0 0 16 15" fill="none">
                                                    <path
                                                        d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                        stroke="#3B3731" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg> Surf</h4>
                                            <p>Turtle • Red-Ear</p>
                                        </div>
                                    </div>
                                    <div class="pet-info">
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="11" height="15"
                                                viewBox="0 0 11 15" fill="none">
                                                <path
                                                    d="M5.5 0.5C8.2836 0.5 10.5 2.64831 10.5 5.25C10.5 7.739 8.47279 9.81207 5.85938 9.98828L5.5 10.0117L5.14062 9.98828C2.52723 9.81207 0.5 7.73865 0.5 5.25C0.5 2.64831 2.7164 0.5 5.5 0.5Z"
                                                    stroke="#9D9B98" />
                                            </svg>
                                            Female</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="17"
                                                viewBox="0 0 15 17" fill="none">
                                                <path
                                                    d="M1.27778 11.7778V14.5C1.27778 14.9126 1.44167 15.3082 1.73339 15.5999C2.02511 15.8917 2.42077 16.0556 2.83333 16.0556H12.1667C12.5792 16.0556 12.9749 15.8917 13.2666 15.5999C13.5583 15.3082 13.7222 14.9126 13.7222 14.5V11.7778M0.5 9.83333V9.05556C0.5 8.643 0.663888 8.24734 0.955612 7.95561C1.24733 7.66389 1.643 7.5 2.05556 7.5H12.9444C13.357 7.5 13.7527 7.66389 14.0444 7.95561C14.3361 8.24734 14.5 8.643 14.5 9.05556V9.83333M7.5 5.16667V7.5M7.5 5.16667C8.48156 5.16667 9.05556 4.41378 9.05556 3.125C9.05556 1.83622 7.5 0.5 7.5 0.5C7.5 0.5 5.94444 1.83622 5.94444 3.125C5.94444 4.41378 6.51844 5.16667 7.5 5.16667Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M0.5 9.83331C0.5 10.4522 0.745833 11.0456 1.18342 11.4832C1.621 11.9208 2.21449 12.1666 2.83333 12.1666C3.45217 12.1666 4.04566 11.9208 4.48325 11.4832C4.92083 11.0456 5.16667 10.4522 5.16667 9.83331C5.16667 10.4522 5.4125 11.0456 5.85008 11.4832C6.28767 11.9208 6.88116 12.1666 7.5 12.1666C8.11884 12.1666 8.71233 11.9208 9.14992 11.4832C9.5875 11.0456 9.83333 10.4522 9.83333 9.83331C9.83333 10.4522 10.0792 11.0456 10.5168 11.4832C10.9543 11.9208 11.5478 12.1666 12.1667 12.1666C12.7855 12.1666 13.379 11.9208 13.8166 11.4832C14.2542 11.0456 14.5 10.4522 14.5 9.83331"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg> 22/08/2020</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M4.7373 3.14703C4.7373 3.84907 5.01619 4.52235 5.51261 5.01876C6.00903 5.51518 6.68232 5.79406 7.38436 5.79406C8.08641 5.79406 8.7597 5.51518 9.25612 5.01876C9.75254 4.52235 10.0314 3.84907 10.0314 3.14703C10.0314 2.44499 9.75254 1.77171 9.25612 1.2753C8.7597 0.778883 8.08641 0.5 7.38436 0.5C6.68232 0.5 6.00903 0.778883 5.51261 1.2753C5.01619 1.77171 4.7373 2.44499 4.7373 3.14703Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M2.8269 5.79419H11.9416C12.1482 5.79416 12.3483 5.86665 12.507 5.99904C12.6657 6.13142 12.7728 6.31529 12.8098 6.51859L14.2542 14.4597C14.2774 14.5869 14.2723 14.7176 14.2394 14.8425C14.2064 14.9675 14.1464 15.0838 14.0636 15.183C13.9807 15.2822 13.8771 15.3621 13.76 15.4168C13.643 15.4716 13.5153 15.5 13.386 15.5H1.38249C1.25323 15.5 1.12554 15.4716 1.00846 15.4168C0.891377 15.3621 0.78776 15.2822 0.704935 15.183C0.62211 15.0838 0.5621 14.9675 0.52915 14.8425C0.496199 14.7176 0.491113 14.5869 0.514251 14.4597L1.95866 6.51859C1.99565 6.31529 2.10282 6.13142 2.26149 5.99904C2.42016 5.86665 2.62026 5.79416 2.8269 5.79419Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg> 2 kg</p>
                                        <p><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M13.5905 8.11123L13.9601 6.73016C14.3918 5.1182 14.6084 4.31257 14.4462 3.61489C14.3176 3.0641 14.0285 2.56382 13.6155 2.17734C13.093 1.68768 12.2866 1.4718 10.6747 1.04003C9.0627 0.607553 8.25636 0.391671 7.55939 0.55394C7.0086 0.682549 6.50833 0.971618 6.12185 1.38458C5.70224 1.83207 5.4835 2.48758 5.15824 3.67851L4.98382 4.32544L4.61425 5.70651C4.18177 7.31847 3.96589 8.1241 4.12816 8.82178C4.25677 9.37257 4.54584 9.87285 4.9588 10.2593C5.48135 10.749 6.28769 10.9649 7.89966 11.3974C9.35221 11.7862 10.1507 12 10.8048 11.9192C10.8763 11.9101 10.9463 11.8977 11.0149 11.882C11.5655 11.7538 12.0658 11.4652 12.4525 11.0528C12.9421 10.5295 13.158 9.7232 13.5905 8.11123Z"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M10.8047 11.9191C10.6553 12.3768 10.3927 12.7894 10.0413 13.1186C9.51875 13.6082 8.71241 13.8241 7.10045 14.2559C5.48848 14.6876 4.68214 14.9042 3.98517 14.7413C3.43447 14.6128 2.9342 14.324 2.54763 13.9113C2.05796 13.3888 1.84137 12.5824 1.4096 10.9705L1.04003 9.5894C0.607553 7.97744 0.391671 7.1711 0.55394 6.47413C0.682549 5.92334 0.971618 5.42306 1.38458 5.03658C1.90713 4.54692 2.71347 4.33104 4.32544 3.89856C4.62948 3.81659 4.90708 3.74296 5.15823 3.67767"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M7.48951 6.21906L10.9422 7.14406M6.93408 8.29066L9.00569 8.84538"
                                                    stroke="#9D9B98" stroke-linecap="round" />
                                            </svg>
                                            Loves the water.</p>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center">
                                        <button class="my-pets btn-edit-outline"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                viewBox="0 0 16 15" fill="none">
                                                <path
                                                    d="M10.2059 2.37997L12.8529 4.97712M8.44118 14.5H15.5M1.38235 11.0371L0.5 14.5L4.02941 13.6343L14.2524 3.60409C14.5832 3.2794 14.769 2.83908 14.769 2.37997C14.769 1.92085 14.5832 1.48054 14.2524 1.15584L14.1006 1.00694C13.7697 0.682347 13.3209 0.5 12.8529 0.5C12.385 0.5 11.9362 0.682347 11.6053 1.00694L1.38235 11.0371Z"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>Edit details</button>
                                    </div>
                                </div>


                                <div class="slider-arrow-next">
                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="7" height="11"
                                            viewBox="0 0 7 11" fill="none">
                                            <path d="M0.5 10.484L5.53426 5.44975L0.58451 0.500005" stroke="#3B3731"
                                                stroke-linecap="round" stroke-linejoin="round" />
                                        </svg></span>
                                </div>
                            </div>

                            <div class="d-flex align-items-center justify-content-center mt-5">

                                <button id="btn-add-new-pet" class="btn-add-another-pet">+ Add another pet</button>
                            </div>
                        </div>

                        <!-- Add New Pet Form (hidden by default) -->
                        <div class=" pf-form booking-container hidden" id="add-pet-form-container">

                            <!-- LEFT IMAGE -->
                            <div class="pf-left">

                                <div class="pf-avatar">
                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="190" height="190"
                                            viewBox="0 0 190 190" fill="none">
                                            <circle cx="95" cy="95" r="94.5" fill="#E3E3E3" stroke="#9D9B98" />
                                            <path
                                                d="M36.7334 169.099C40.4255 97.0391 149.511 97.0391 153.203 169.1C153.203 169.1 131.903 189.366 95.1698 189.366C58.4364 189.366 36.7334 169.099 36.7334 169.099Z"
                                                fill="white" stroke="#9D9B98" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M123.737 74.7178C123.737 82.4402 120.669 89.8464 115.209 95.3071C109.748 100.768 102.342 103.835 94.6194 103.835C86.897 103.835 79.4908 100.768 74.0303 95.3071C68.5697 89.8464 65.502 82.4402 65.502 74.7178C65.502 66.9953 68.5697 59.5891 74.0303 54.1285C79.4908 48.6678 86.897 45.6001 94.6194 45.6001C102.342 45.6001 109.748 48.6678 115.209 54.1285C120.669 59.5891 123.737 66.9953 123.737 74.7178Z"
                                                fill="white" stroke="#9D9B98" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg></span>
                                </div>

                                <button class="pf-edit-btn">
                                    <span>
                                        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="15"
                                            viewBox="0 0 20 15" fill="none">
                                            <path
                                                d="M1.25 12.5C1.25 12.8315 1.3817 13.1495 1.61612 13.3839C1.85054 13.6183 2.16848 13.75 2.5 13.75H17.5C17.8315 13.75 18.1495 13.6183 18.3839 13.3839C18.6183 13.1495 18.75 12.8315 18.75 12.5V5C18.75 4.66848 18.6183 4.35054 18.3839 4.11612C18.1495 3.8817 17.8315 3.75 17.5 3.75H16.035C15.041 3.74946 14.0878 3.35425 13.385 2.65125L12.3475 1.61625C12.1137 1.38242 11.7969 1.25073 11.4662 1.25H8.53625C8.20476 1.25007 7.88687 1.38181 7.6525 1.61625L6.6175 2.65125C6.26921 2.99964 5.85569 3.276 5.40057 3.46453C4.94544 3.65305 4.45763 3.75006 3.965 3.75H2.5C2.16848 3.75 1.85054 3.8817 1.61612 4.11612C1.3817 4.35054 1.25 4.66848 1.25 5V12.5ZM17.5 2.5C18.163 2.5 18.7989 2.76339 19.2678 3.23223C19.7366 3.70107 20 4.33696 20 5V12.5C20 13.163 19.7366 13.7989 19.2678 14.2678C18.7989 14.7366 18.163 15 17.5 15H2.5C1.83696 15 1.20107 14.7366 0.732233 14.2678C0.263393 13.7989 0 13.163 0 12.5V5C0 4.33696 0.263393 3.70107 0.732233 3.23223C1.20107 2.76339 1.83696 2.5 2.5 2.5H3.965C4.62799 2.49986 5.26377 2.23637 5.7325 1.7675L6.7675 0.7325C7.23623 0.263627 7.87201 0.000141594 8.535 0H11.465C12.128 0.000141594 12.7638 0.263627 13.2325 0.7325L14.2675 1.7675C14.7362 2.23637 15.372 2.49986 16.035 2.5H17.5Z"
                                                fill="#3B3731" />
                                            <path
                                                d="M10 11.25C10.8288 11.25 11.6237 10.9208 12.2097 10.3347C12.7958 9.74866 13.125 8.9538 13.125 8.125C13.125 7.2962 12.7958 6.50134 12.2097 5.91529C11.6237 5.32924 10.8288 5 10 5C9.1712 5 8.37634 5.32924 7.79029 5.91529C7.20424 6.50134 6.875 7.2962 6.875 8.125C6.875 8.9538 7.20424 9.74866 7.79029 10.3347C8.37634 10.9208 9.1712 11.25 10 11.25ZM10 12.5C8.83968 12.5 7.72688 12.0391 6.90641 11.2186C6.08594 10.3981 5.625 9.28532 5.625 8.125C5.625 6.96468 6.08594 5.85188 6.90641 5.03141C7.72688 4.21094 8.83968 3.75 10 3.75C11.1603 3.75 12.2731 4.21094 13.0936 5.03141C13.9141 5.85188 14.375 6.96468 14.375 8.125C14.375 9.28532 13.9141 10.3981 13.0936 11.2186C12.2731 12.0391 11.1603 12.5 10 12.5ZM16.25 5.625C16.25 5.79076 16.3158 5.94973 16.4331 6.06694C16.5503 6.18415 16.7092 6.25 16.875 6.25C17.0408 6.25 17.1997 6.18415 17.3169 6.06694C17.4342 5.94973 17.5 5.79076 17.5 5.625C17.5 5.45924 17.4342 5.30027 17.3169 5.18306C17.1997 5.06585 17.0408 5 16.875 5C16.7092 5 16.5503 5.06585 16.4331 5.18306C16.3158 5.30027 16.25 5.45924 16.25 5.625Z"
                                                fill="#3B3731" />
                                        </svg>
                                    </span> Edit photo
                                </button>

                            </div>

                            <!-- RIGHT FORM -->
                            <form id="add-pet-form" class="pf-pet-right">

                                <!-- Row 1 -->
                                <div class="pf-form-row">

                                    <div class="pf-field">
                                        <label>Select Pet Type</label>
                                        <div class="pf-toggle-group">
                                            <button type="button">
                                                <p>Cat</p> <span><svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                        height="14" viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M4.6084 2.33691C5.10435 2.27575 5.79149 2.29051 6.44824 2.5127C7.17421 2.75837 7.87556 3.26331 8.21875 4.19922L9.63379 4.87793L9.66406 4.98926C9.85166 5.66584 9.96334 6.71639 9.67285 7.67285C9.52643 8.15482 9.27602 8.61867 8.87695 8.99414C8.47673 9.37055 7.93833 9.64708 7.23535 9.77246C4.70572 10.2235 3.27073 12.3877 2.875 13.4092C2.71213 13.8744 1.74663 13.9922 1.51953 13.5547C-1.87265 7.01946 1.17825 1.85935 3.28418 0L4.6084 2.33691ZM6.17578 4.70117C5.80462 4.70117 5.41028 4.88536 5.41016 5.62207C5.41016 6.13084 6.059 5.6221 6.48145 5.62207C6.90389 5.62207 6.94043 6.13086 6.94043 5.62207C6.94028 5.11355 6.59802 4.70134 6.17578 4.70117Z"
                                                            fill="#D4D4D4" />
                                                    </svg></span>
                                            </button>
                                            <button type="button">
                                                <p>Dog</p><span><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="15" viewBox="0 0 16 15" fill="none">
                                                        <path
                                                            d="M8.02539 0C8.64242 0.000140068 9.2247 0.285386 9.60352 0.772461L11.7188 3.49219C11.8778 3.69673 12.1107 3.83152 12.3672 3.86816L14.3516 4.15137C14.7095 4.20257 15.0127 4.44309 15.1211 4.78809C15.3559 5.53534 15.694 6.86422 15.4336 7.6582C15.1019 8.66939 14.5698 9.17547 13.6104 9.42285C11.9671 9.84647 10.6084 9.36193 9.00293 10.5859C8.55955 10.924 8.24254 11.3554 8.02148 11.8379C7.15008 13.7398 4.72988 15.7687 2.92676 14.708C1.76295 14.0232 1.19713 12.6471 1.54297 11.3418L2.01855 9.5459C2.13806 9.53792 2.25497 9.52868 2.36621 9.51465C2.66071 9.47749 2.95579 9.41275 3.1543 9.28711C3.31279 9.18664 3.47135 9.01633 3.61621 8.83105C3.76496 8.64078 3.91598 8.41357 4.05859 8.18066C4.3438 7.71485 4.60464 7.20844 4.75879 6.88281C4.81783 6.75804 4.7644 6.60888 4.63965 6.5498C4.51511 6.49106 4.36683 6.54369 4.30762 6.66797C4.15931 6.98132 3.90591 7.47232 3.63184 7.91992C3.49484 8.14364 3.35443 8.35264 3.22168 8.52246C3.08497 8.69731 2.97014 8.81137 2.88672 8.86426C2.78879 8.92628 2.5909 8.98231 2.30371 9.01855C2.02727 9.05342 1.70321 9.06707 1.38281 9.06641C1.06824 9.06575 0.762506 9.04879 0.516602 9.03125C0.12944 8.76968 -0.0877677 8.27103 0.0341797 7.80859C1.06258 3.90966 1.66993 2.10637 2.67871 1.09766C3.77026 0.00653585 5.94603 3.69908e-05 5.97168 0H8.02539ZM8.5752 3.66016C8.11076 3.66016 7.61831 3.89064 7.61816 4.8125C7.61816 5.44915 8.42941 4.8125 8.95801 4.8125C9.48646 4.81262 9.53223 5.44909 9.53223 4.8125C9.53204 4.17611 9.10359 3.66027 8.5752 3.66016Z"
                                                            fill="#D4D4D4" />
                                                    </svg></span>
                                            </button>
                                            <button type="button" class="active">
                                                <p style="color:#fff;">Other</p> <span><svg
                                                        xmlns="http://www.w3.org/2000/svg" width="19" height="15"
                                                        viewBox="0 0 19 15" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M6.10714 0C5.43536 0 4.90879 0.410454 4.58307 0.902727C4.25329 1.39909 4.07143 2.04682 4.07143 2.72727C4.07143 3.40773 4.25329 4.05545 4.58307 4.55182C4.90879 5.04273 5.43536 5.45455 6.10714 5.45455C6.77893 5.45455 7.3055 5.04409 7.63121 4.55182C7.961 4.05545 8.14286 3.40773 8.14286 2.72727C8.14286 2.04682 7.961 1.39909 7.63121 0.902727C7.3055 0.411818 6.77893 0 6.10714 0ZM12.8929 0C12.2211 0 11.6945 0.410454 11.3688 0.902727C11.039 1.39909 10.8571 2.04682 10.8571 2.72727C10.8571 3.40773 11.039 4.05545 11.3688 4.55182C11.6945 5.04273 12.2211 5.45455 12.8929 5.45455C13.5646 5.45455 14.0912 5.04409 14.4169 4.55182C14.7467 4.05545 14.9286 3.40773 14.9286 2.72727C14.9286 2.04682 14.7467 1.39909 14.4169 0.902727C14.0912 0.411818 13.5646 0 12.8929 0ZM2.03571 6.13636C1.36393 6.13636 0.837357 6.54682 0.511643 7.03909C0.181857 7.53545 0 8.18318 0 8.86364C0 9.54409 0.181857 10.1918 0.511643 10.6882C0.837357 11.1791 1.36393 11.5909 2.03571 11.5909C2.7075 11.5909 3.23407 11.1805 3.55979 10.6882C3.88957 10.1918 4.07143 9.54409 4.07143 8.86364C4.07143 8.18318 3.88957 7.53545 3.55979 7.03909C3.23407 6.54818 2.7075 6.13636 2.03571 6.13636ZM9.5 6.13636C7.87143 6.13636 6.66493 7.01455 5.89407 8.10409C5.13271 9.17727 4.75 10.5095 4.75 11.5909C4.75 12.8509 5.50321 13.7277 6.42743 14.2527C7.33671 14.7709 8.47671 15 9.5 15C10.5233 15 11.6633 14.7723 12.5726 14.2527C13.4954 13.7264 14.25 12.8509 14.25 11.5909C14.25 10.5095 13.8673 9.17727 13.1059 8.10409C12.3364 7.01318 11.1299 6.13636 9.5 6.13636ZM16.9643 6.13636C16.2925 6.13636 15.7659 6.54682 15.4402 7.03909C15.1104 7.53545 14.9286 8.18318 14.9286 8.86364C14.9286 9.54409 15.1104 10.1918 15.4402 10.6882C15.7659 11.1791 16.2925 11.5909 16.9643 11.5909C17.6361 11.5909 18.1626 11.1805 18.4884 10.6882C18.8181 10.1918 19 9.54409 19 8.86364C19 8.18318 18.8181 7.53545 18.4884 7.03909C18.1626 6.54818 17.6361 6.13636 16.9643 6.13636Z"
                                                            fill="white" />
                                                    </svg></span>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="pf-field" style="flex:2;">
                                        <label>Select Pet Size</label>
                                        <div class="pf-toggle-group" style="width:100%">
                                            <button type="button">
                                                <p>Small 0-7 kg</p>
                                            </button>
                                            <button type="button">
                                                <p>Medium 8-18 kg</p>
                                            </button>
                                            <button type="button" class="active">
                                                <p style="color:#fff">Large 19+ kg</p>
                                            </button>
                                        </div>
                                    </div>

                                </div>

                                <!-- Row 2 -->
                                <div class="pf-form-row">

                                    <div class="pf-pet-field">
                                        <label>Name</label>
                                        <div class="input-box">

                                            <input type="text" value="Bella">

                                            <!-- RIGHT SVG -->
                                            <svg class="check-icon" xmlns="http://www.w3.org/2000/svg" width="19"
                                                height="19" viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>

                                        </div>
                                    </div>

                                    <div class="pf-pet-field">
                                        <div class="input-box birthday-input-box">
                                            <x-ui.calendar name="birthday" />
                                        </div>
                                    </div>

                                    <div class="pf-pet-field sex-field"
                                        style="position: relative; z-index: 2000000;">
                                        <label>Sex</label>

                                        <div class="sex-options" role="radiogroup" aria-label="Sex">
                                            <label class="radio--small">
                                                <input type="radio" name="sex" value="male" id="petSexMale">
                                                <span class="radio--visual" aria-hidden="true"></span>
                                                <span class="radio--text">Male</span>
                                            </label>

                                            <label class="radio--small">
                                                <input type="radio" name="sex" value="female" id="petSexFemale">
                                                <span class="radio--visual" aria-hidden="true"></span>
                                                <span class="radio--text">Female</span>
                                            </label>
                                        </div>

                                    </div>

                                </div>

                                <!-- Row 3 -->
                                <div class="pf-form-row">

                                    <div class="pf-pet-field" style="position:relative;">
                                        <label>Pet Type <span style="color: #9D9B98;
font-family: Lato;
font-size: 16px;
font-style: normal;
font-weight: 600;
line-height: normal;">(for ‘other’ pets)</span></label>
                                        <div style="position:relative; display:block;width: 300px;">
                                            <input type="text" id="pet_type" placeholder="e.g. Dog, Cat, Rabbit..."
                                                autocomplete="off"
                                                style="padding-right:40px; width:100%; display:block; height:48px; padding:8px 20px; border-radius:10px; border:1px solid #D4D4D4; background:#FFF; font-family:Lato; font-size:16px;">
                                            <span id="petTypeCheck_profile"
                                                style="position:absolute;right:12px;top:50%;transform:translateY(-50%);pointer-events:none;display:none;line-height:0;z-index:5;">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                    viewBox="0 0 19 19" fill="none">
                                                    <path
                                                        d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                        fill="#C9DDA0" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div id="petTypeSuggestions_profile"
                                            style="display:none; border:1px solid #D4D4D4; border-top:none; border-radius:0 0 10px 10px; background:#FFF; max-height:200px; overflow-y:auto; position:absolute; width:100%; z-index:10; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
                                        </div>
                                    </div>

                                    <div class="pf-pet-field">
                                        <label>Breed(s)</label>
                                        <div class="select-wrapper" style="width:320px;">
                                            <select id="pet-breed" data-furs-dropdown data-furs-searchable>
                                                <option value="" disabled selected>Select breed</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="pf-pet-field small">
                                        <label>Weight (kg)</label>

                                        <div class="input-box">

                                            <input type="number" value="4" style="width: 85px;height: 48px; color: #3B3731;
font-family: Lato;
font-size: 16px;
font-style: normal;
font-weight: 400;
line-height: normal;">
                                            <!-- SVG -->
                                            <svg class="number-icon" xmlns="http://www.w3.org/2000/svg" width="11"
                                                height="28" viewBox="0 0 11 28" fill="none">
                                                <path d="M10.374 21.5315L5.3952 26.5103L0.499963 21.6151"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path d="M10.374 5.47876L5.3952 0.499941L0.499963 5.39518"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>

                                        </div>
                                    </div>

                                </div>

                                <div class="pf-form-container">
                                    <div class="pf-input-group">
                                        <label class="pf-section-label">Vaccination status</label>
                                        <div class="sex-options" role="radiogroup" aria-label="Sex">
                                            <label class="radio--small">
                                                <input type="radio" name="status" value="yes" id="petSexMale">
                                                <span class="radio--visual" aria-hidden="true"></span>
                                                <span class="radio--text">Yes</span>
                                            </label>

                                            <label class="radio--small">
                                                <input type="radio" name="status" value="no" id="petSexFemale">
                                                <span class="radio--visual" aria-hidden="true"></span>
                                                <span class="radio--text">No</span>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="pf-input-group">
                                        <label class="pf-section-label">Medical Notes</label>
                                        <textarea class="pf-textarea"
                                            placeholder="Help us keep your pets healthy and safe!&#10;(e.g allergies, sensitivities, medications, or ongoing treatments)."></textarea>
                                    </div>

                                    <div class="pf-input-group">
                                        <label class="pf-section-label">Personality & behaviour</label>
                                        <textarea class="pf-textarea"
                                            placeholder="Any behaviour we should know about?&#10;(e.g. Friendly with people, nervous around loud noises, doesn't like paws touched)."></textarea>
                                    </div>

                                    <div class="pf-input-group">
                                        <label class="pf-section-label">Grooming preferences</label>
                                        <textarea class="pf-textarea"
                                            placeholder="Any style preferences?&#10;(e.g clip length, shampoo type, sensitive areas)."></textarea>
                                    </div>
                                </div>

                                <div class="pf-gallery-container">
                                    <h3>Photo Gallery</h3>
                                    <p class="pf-subtitle">Show off your pet by adding additional photos.</p>

                                    <div class="pf-photo-grid">
                                        <div class="pf-photo-card">
                                            <div class="pf-paw-icon">
                                                <i class="fas fa-paw"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="80" height="64" viewBox="0 0 80 64" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M25.7143 0C22.8857 0 20.6686 1.75127 19.2971 3.85164C17.9086 5.96945 17.1429 8.73309 17.1429 11.6364C17.1429 14.5396 17.9086 17.3033 19.2971 19.4211C20.6686 21.5156 22.8857 23.2727 25.7143 23.2727C28.5429 23.2727 30.76 21.5215 32.1314 19.4211C33.52 17.3033 34.2857 14.5396 34.2857 11.6364C34.2857 8.73309 33.52 5.96945 32.1314 3.85164C30.76 1.75709 28.5429 0 25.7143 0ZM54.2857 0C51.4571 0 49.24 1.75127 47.8686 3.85164C46.48 5.96945 45.7143 8.73309 45.7143 11.6364C45.7143 14.5396 46.48 17.3033 47.8686 19.4211C49.24 21.5156 51.4571 23.2727 54.2857 23.2727C57.1143 23.2727 59.3314 21.5215 60.7029 19.4211C62.0914 17.3033 62.8571 14.5396 62.8571 11.6364C62.8571 8.73309 62.0914 5.96945 60.7029 3.85164C59.3314 1.75709 57.1143 0 54.2857 0ZM8.57143 26.1818C5.74286 26.1818 3.52571 27.9331 2.15429 30.0335C0.765714 32.1513 0 34.9149 0 37.8182C0 40.7215 0.765714 43.4851 2.15429 45.6029C3.52571 47.6975 5.74286 49.4545 8.57143 49.4545C11.4 49.4545 13.6171 47.7033 14.9886 45.6029C16.3771 43.4851 17.1429 40.7215 17.1429 37.8182C17.1429 34.9149 16.3771 32.1513 14.9886 30.0335C13.6171 27.9389 11.4 26.1818 8.57143 26.1818ZM40 26.1818C33.1429 26.1818 28.0629 29.9287 24.8171 34.5775C21.6114 39.1564 20 44.8407 20 49.4545C20 54.8305 23.1714 58.5716 27.0629 60.8116C30.8914 63.0225 35.6914 64 40 64C44.3086 64 49.1086 63.0284 52.9371 60.8116C56.8229 58.5658 60 54.8305 60 49.4545C60 44.8407 58.3886 39.1564 55.1829 34.5775C51.9429 29.9229 46.8629 26.1818 40 26.1818ZM71.4286 26.1818C68.6 26.1818 66.3829 27.9331 65.0114 30.0335C63.6229 32.1513 62.8571 34.9149 62.8571 37.8182C62.8571 40.7215 63.6229 43.4851 65.0114 45.6029C66.3829 47.6975 68.6 49.4545 71.4286 49.4545C74.2571 49.4545 76.4743 47.7033 77.8457 45.6029C79.2343 43.4851 80 40.7215 80 37.8182C80 34.9149 79.2343 32.1513 77.8457 30.0335C76.4743 27.9389 74.2571 26.1818 71.4286 26.1818Z"
                                                            fill="#E5E5E5" />
                                                    </svg></i>
                                            </div>
                                            <label class="pf-upload-btn">
                                                <input type="pf-file" hidden>
                                                <span class="pf-upload-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                        <path
                                                            d="M7 10.3163C6.72386 10.3163 6.5 10.0924 6.5 9.81626V1.66626L4.52903 3.63722C4.33115 3.8351 4.00998 3.83398 3.81349 3.63471C3.61896 3.43744 3.62005 3.12016 3.81593 2.92422L6.55492 0.184464C6.80072 -0.0614062 7.19931 -0.0614344 7.44514 0.184402L10.185 2.92425C10.3809 3.1202 10.3822 3.43753 10.1877 3.63499C9.99116 3.83461 9.66959 3.83585 9.47149 3.63775L7.5 1.66626V9.81626C7.5 10.0924 7.27614 10.3163 7 10.3163ZM1.616 13.7393C1.15533 13.7393 0.771 13.5853 0.463 13.2773C0.155 12.9693 0.000666667 12.5846 0 12.1233V10.2003C0 9.92412 0.223858 9.70026 0.5 9.70026C0.776142 9.70026 1 9.92412 1 10.2003V12.1233C1 12.2773 1.064 12.4186 1.192 12.5473C1.32 12.6759 1.461 12.7399 1.615 12.7393H12.385C12.5383 12.7393 12.6793 12.6753 12.808 12.5473C12.9367 12.4193 13.0007 12.2779 13 12.1233V10.2003C13 9.92412 13.2239 9.70026 13.5 9.70026C13.7761 9.70026 14 9.92412 14 10.2003V12.1233C14 12.5839 13.846 12.9683 13.538 13.2763C13.23 13.5843 12.8453 13.7386 12.384 13.7393H1.616Z"
                                                            fill="#3B3731" />
                                                    </svg></span> Upload Photo
                                            </label>
                                        </div>

                                        <div class="pf-photo-card">
                                            <div class="pf-paw-icon">
                                                <i class="fas fa-paw"> <svg xmlns="http://www.w3.org/2000/svg"
                                                        width="80" height="64" viewBox="0 0 80 64" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M25.7143 0C22.8857 0 20.6686 1.75127 19.2971 3.85164C17.9086 5.96945 17.1429 8.73309 17.1429 11.6364C17.1429 14.5396 17.9086 17.3033 19.2971 19.4211C20.6686 21.5156 22.8857 23.2727 25.7143 23.2727C28.5429 23.2727 30.76 21.5215 32.1314 19.4211C33.52 17.3033 34.2857 14.5396 34.2857 11.6364C34.2857 8.73309 33.52 5.96945 32.1314 3.85164C30.76 1.75709 28.5429 0 25.7143 0ZM54.2857 0C51.4571 0 49.24 1.75127 47.8686 3.85164C46.48 5.96945 45.7143 8.73309 45.7143 11.6364C45.7143 14.5396 46.48 17.3033 47.8686 19.4211C49.24 21.5156 51.4571 23.2727 54.2857 23.2727C57.1143 23.2727 59.3314 21.5215 60.7029 19.4211C62.0914 17.3033 62.8571 14.5396 62.8571 11.6364C62.8571 8.73309 62.0914 5.96945 60.7029 3.85164C59.3314 1.75709 57.1143 0 54.2857 0ZM8.57143 26.1818C5.74286 26.1818 3.52571 27.9331 2.15429 30.0335C0.765714 32.1513 0 34.9149 0 37.8182C0 40.7215 0.765714 43.4851 2.15429 45.6029C3.52571 47.6975 5.74286 49.4545 8.57143 49.4545C11.4 49.4545 13.6171 47.7033 14.9886 45.6029C16.3771 43.4851 17.1429 40.7215 17.1429 37.8182C17.1429 34.9149 16.3771 32.1513 14.9886 30.0335C13.6171 27.9389 11.4 26.1818 8.57143 26.1818ZM40 26.1818C33.1429 26.1818 28.0629 29.9287 24.8171 34.5775C21.6114 39.1564 20 44.8407 20 49.4545C20 54.8305 23.1714 58.5716 27.0629 60.8116C30.8914 63.0225 35.6914 64 40 64C44.3086 64 49.1086 63.0284 52.9371 60.8116C56.8229 58.5658 60 54.8305 60 49.4545C60 44.8407 58.3886 39.1564 55.1829 34.5775C51.9429 29.9229 46.8629 26.1818 40 26.1818ZM71.4286 26.1818C68.6 26.1818 66.3829 27.9331 65.0114 30.0335C63.6229 32.1513 62.8571 34.9149 62.8571 37.8182C62.8571 40.7215 63.6229 43.4851 65.0114 45.6029C66.3829 47.6975 68.6 49.4545 71.4286 49.4545C74.2571 49.4545 76.4743 47.7033 77.8457 45.6029C79.2343 43.4851 80 40.7215 80 37.8182C80 34.9149 79.2343 32.1513 77.8457 30.0335C76.4743 27.9389 74.2571 26.1818 71.4286 26.1818Z"
                                                            fill="#E5E5E5" />
                                                    </svg></i>
                                            </div>
                                            <label class="pf-upload-btn">
                                                <input type="pf-file" hidden>
                                                <span class="pf-upload-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                        <path
                                                            d="M7 10.3163C6.72386 10.3163 6.5 10.0924 6.5 9.81626V1.66626L4.52903 3.63722C4.33115 3.8351 4.00998 3.83398 3.81349 3.63471C3.61896 3.43744 3.62005 3.12016 3.81593 2.92422L6.55492 0.184464C6.80072 -0.0614062 7.19931 -0.0614344 7.44514 0.184402L10.185 2.92425C10.3809 3.1202 10.3822 3.43753 10.1877 3.63499C9.99116 3.83461 9.66959 3.83585 9.47149 3.63775L7.5 1.66626V9.81626C7.5 10.0924 7.27614 10.3163 7 10.3163ZM1.616 13.7393C1.15533 13.7393 0.771 13.5853 0.463 13.2773C0.155 12.9693 0.000666667 12.5846 0 12.1233V10.2003C0 9.92412 0.223858 9.70026 0.5 9.70026C0.776142 9.70026 1 9.92412 1 10.2003V12.1233C1 12.2773 1.064 12.4186 1.192 12.5473C1.32 12.6759 1.461 12.7399 1.615 12.7393H12.385C12.5383 12.7393 12.6793 12.6753 12.808 12.5473C12.9367 12.4193 13.0007 12.2779 13 12.1233V10.2003C13 9.92412 13.2239 9.70026 13.5 9.70026C13.7761 9.70026 14 9.92412 14 10.2003V12.1233C14 12.5839 13.846 12.9683 13.538 13.2763C13.23 13.5843 12.8453 13.7386 12.384 13.7393H1.616Z"
                                                            fill="#3B3731" />
                                                    </svg></span> Upload Photo
                                            </label>
                                        </div>

                                        <div class="pf-photo-card">
                                            <div class="pf-paw-icon">
                                                <i class="fas fa-paw"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="80" height="64" viewBox="0 0 80 64" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M25.7143 0C22.8857 0 20.6686 1.75127 19.2971 3.85164C17.9086 5.96945 17.1429 8.73309 17.1429 11.6364C17.1429 14.5396 17.9086 17.3033 19.2971 19.4211C20.6686 21.5156 22.8857 23.2727 25.7143 23.2727C28.5429 23.2727 30.76 21.5215 32.1314 19.4211C33.52 17.3033 34.2857 14.5396 34.2857 11.6364C34.2857 8.73309 33.52 5.96945 32.1314 3.85164C30.76 1.75709 28.5429 0 25.7143 0ZM54.2857 0C51.4571 0 49.24 1.75127 47.8686 3.85164C46.48 5.96945 45.7143 8.73309 45.7143 11.6364C45.7143 14.5396 46.48 17.3033 47.8686 19.4211C49.24 21.5156 51.4571 23.2727 54.2857 23.2727C57.1143 23.2727 59.3314 21.5215 60.7029 19.4211C62.0914 17.3033 62.8571 14.5396 62.8571 11.6364C62.8571 8.73309 62.0914 5.96945 60.7029 3.85164C59.3314 1.75709 57.1143 0 54.2857 0ZM8.57143 26.1818C5.74286 26.1818 3.52571 27.9331 2.15429 30.0335C0.765714 32.1513 0 34.9149 0 37.8182C0 40.7215 0.765714 43.4851 2.15429 45.6029C3.52571 47.6975 5.74286 49.4545 8.57143 49.4545C11.4 49.4545 13.6171 47.7033 14.9886 45.6029C16.3771 43.4851 17.1429 40.7215 17.1429 37.8182C17.1429 34.9149 16.3771 32.1513 14.9886 30.0335C13.6171 27.9389 11.4 26.1818 8.57143 26.1818ZM40 26.1818C33.1429 26.1818 28.0629 29.9287 24.8171 34.5775C21.6114 39.1564 20 44.8407 20 49.4545C20 54.8305 23.1714 58.5716 27.0629 60.8116C30.8914 63.0225 35.6914 64 40 64C44.3086 64 49.1086 63.0284 52.9371 60.8116C56.8229 58.5658 60 54.8305 60 49.4545C60 44.8407 58.3886 39.1564 55.1829 34.5775C51.9429 29.9229 46.8629 26.1818 40 26.1818ZM71.4286 26.1818C68.6 26.1818 66.3829 27.9331 65.0114 30.0335C63.6229 32.1513 62.8571 34.9149 62.8571 37.8182C62.8571 40.7215 63.6229 43.4851 65.0114 45.6029C66.3829 47.6975 68.6 49.4545 71.4286 49.4545C74.2571 49.4545 76.4743 47.7033 77.8457 45.6029C79.2343 43.4851 80 40.7215 80 37.8182C80 34.9149 79.2343 32.1513 77.8457 30.0335C76.4743 27.9389 74.2571 26.1818 71.4286 26.1818Z"
                                                            fill="#E5E5E5" />
                                                    </svg></i>
                                            </div>
                                            <label class="pf-upload-btn">
                                                <input type="pf-file" hidden>
                                                <span class="pf-upload-icon"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="14" height="14" viewBox="0 0 14 14" fill="none">
                                                        <path
                                                            d="M7 10.3163C6.72386 10.3163 6.5 10.0924 6.5 9.81626V1.66626L4.52903 3.63722C4.33115 3.8351 4.00998 3.83398 3.81349 3.63471C3.61896 3.43744 3.62005 3.12016 3.81593 2.92422L6.55492 0.184464C6.80072 -0.0614062 7.19931 -0.0614344 7.44514 0.184402L10.185 2.92425C10.3809 3.1202 10.3822 3.43753 10.1877 3.63499C9.99116 3.83461 9.66959 3.83585 9.47149 3.63775L7.5 1.66626V9.81626C7.5 10.0924 7.27614 10.3163 7 10.3163ZM1.616 13.7393C1.15533 13.7393 0.771 13.5853 0.463 13.2773C0.155 12.9693 0.000666667 12.5846 0 12.1233V10.2003C0 9.92412 0.223858 9.70026 0.5 9.70026C0.776142 9.70026 1 9.92412 1 10.2003V12.1233C1 12.2773 1.064 12.4186 1.192 12.5473C1.32 12.6759 1.461 12.7399 1.615 12.7393H12.385C12.5383 12.7393 12.6793 12.6753 12.808 12.5473C12.9367 12.4193 13.0007 12.2779 13 12.1233V10.2003C13 9.92412 13.2239 9.70026 13.5 9.70026C13.7761 9.70026 14 9.92412 14 10.2003V12.1233C14 12.5839 13.846 12.9683 13.538 13.2763C13.23 13.5843 12.8453 13.7386 12.384 13.7393H1.616Z"
                                                            fill="#3B3731" />
                                                    </svg></span> Upload Photo
                                            </label>
                                        </div>
                                    </div>

                                    <div class="pf-notes-section">
                                        <label>Notes <span>(Optional)</span></label>
                                        <textarea class="pf-textarea"
                                            placeholder="Anything your groomer should know?&#10;(e.g. anxious around dryers, allergies, behaviour cues)."></textarea>
                                    </div>

                                    <div class="pf-action-footer">
                                        <button type="button" id="cancel-add-pet" class="btn-cancel">Cancel</button>
                                        <button type="submit" class="btn-save">Save</button>
                                    </div>
                                </div>



                            </form>

                        </div>
                    </div>

                    <section id="favourites-section" class="hidden">
                        <section id="groomers-sec" class="tab-content favourites mt-5">
                            <div class="favourite-title">
                                <h3 class="section-title">Favourites</h3>
                                <hr class="divider mt-4">

                            </div>

                            <!-- Top Header -->
                            <div class="top-bar mt-4">
                                <div class="tabs">
                                    <button class="tab-link active"
                                        onclick="showSection(event, 'groomers-sec')">Groomers</button>
                                    <button class="tab-link"
                                        onclick="showSection(event, 'spaces-sec')">Space</button>
                                </div>

                                <div class="search-area">
                                    <input type="text" placeholder="Type to search....">
                                    <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16"
                                        height="16" viewBox="0 0 16 16" fill="none">
                                        <path
                                            d="M5.73535 0.5C8.6267 0.500031 10.9707 2.844 10.9707 5.73535C10.9707 7.22006 10.3528 8.55933 9.35938 9.5127C8.41826 10.4158 7.14221 10.9707 5.73535 10.9707C2.844 10.9707 0.500031 8.6267 0.5 5.73535C0.5 2.84398 2.84398 0.5 5.73535 0.5Z"
                                            stroke="#9D9B98" />
                                        <path
                                            d="M14.6466 15.3537C14.8419 15.549 15.1585 15.549 15.3537 15.3537C15.549 15.1585 15.549 14.8419 15.3537 14.6466L15.0002 15.0002L14.6466 15.3537ZM9.70605 9.70605L9.3525 10.0596L14.6466 15.3537L15.0002 15.0002L15.3537 14.6466L10.0596 9.3525L9.70605 9.70605Z"
                                            fill="#9D9B98" />
                                    </svg>
                                </div>
                            </div>

                            <div class="feature-section mb-4">
                                <div class="feature-heading">
                                    <h2>Favourite Groomers</h2>
                                </div>
                                <div class="filter-section">
                                    <button class="filter">Groomer Venue <svg xmlns="http://www.w3.org/2000/svg"
                                            width="13" height="7" viewBox="0 0 13 7" fill="none">
                                            <path d="M11.9102 0.5L6.15672 6.25344L0.499867 0.596581"
                                                stroke="#FBAC83" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg></button>
                                    <button class="filter">Sort <svg xmlns="http://www.w3.org/2000/svg" width="13"
                                            height="7" viewBox="0 0 13 7" fill="none">
                                            <path d="M11.9102 0.5L6.15672 6.25344L0.499867 0.596581"
                                                stroke="#FBAC83" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg></button>
                                </div>
                            </div>



                            <span class="tag mt-4">Full Groom <svg xmlns="http://www.w3.org/2000/svg" width="9"
                                    height="9" viewBox="0 0 9 9" fill="none">
                                    <path d="M0.5 7.57L7.572 0.5M0.5 0.5L7.572 7.57" stroke="white"
                                        stroke-linecap="round" />
                                </svg></span>

                            <!-- Cards -->
                            <div class="groomer-grid mt-5">

                                <!-- Card 1 -->
                                <div class="card d-flex flex-column gap-25">
                                    <div class="card-body">
                                        <div class="card-img">
                                            <div class="top-left-svg left-svg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22"
                                                    viewBox="0 0 21 22" fill="none">
                                                    <path
                                                        d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z"
                                                        fill="#C9DDA0"></path>
                                                </svg>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130"
                                                fill="none">
                                                <defs>
                                                    <pattern id="pattern-card-1" patternUnits="userSpaceOnUse"
                                                        width="255" height="130">

                                                        <image href="{{ asset('images/profile_modal_image.jpg') }}" alt="Image" width="255" height="130"
                                                            preserveAspectRatio="xMinYMax slice"></image>
                                                    </pattern>
                                                </defs>

                                                <path
                                                    d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z"
                                                    fill="url(#pattern-card-1)"></path>
                                            </svg>
                                            <span class="delete cursor"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="13" height="15" viewBox="0 0 13 15" fill="none">
                                                    <path
                                                        d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                                        fill="#3B3731"></path>
                                                </svg></span>
                                        </div>

                                        <div class="group-icon mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                viewBox="0 0 82 24" fill="none">
                                                <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                <path
                                                    d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                    fill="white" />
                                                <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                    fill="#FEFEFE" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                    fill="#FEFEFE" />
                                                <rect x="58" width="24" height="24" rx="12" fill="#D8E8B7" />
                                                <path
                                                    d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                    fill="white" />
                                            </svg>
                                        </div>

                                        <div class="labels">
                                            <span>Groomer's Studio</span>
                                        </div>

                                        <div class="card-title mt-1">
                                            <h3>Sarah W.</h3>
                                            <p class="sub">Sarah's Grooming Studio</p>
                                        </div>

                                        <div class="meta mt-4">
                                            <div class="meta-location">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>

                                            <div class="meta-rating">

                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 14 14" fill="none">
                                                        <path
                                                            d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z"
                                                            fill="#FFC97A" />
                                                    </svg></span>4.3 <strong>(20)</strong>
                                            </div>

                                        </div>

                                        <div class="card-review mt-4">
                                            <p class="review">
                                                “Hands down the best groomer we’ve tried. The studio is
                                                spotless...”
                                            </p>
                                        </div>

                                        <div class="bottom mt-4">
                                            <p>From <b>£38</b></p>
                                            <button class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="36"
                                                    height="36" viewBox="0 0 36 36" fill="none">
                                                    <rect width="36" height="36" rx="18" fill="#D8E8B7" />
                                                    <path
                                                        d="M24.3526 17.2013C24.7483 17.5931 24.7477 18.2326 24.3514 18.6236L21.1135 21.8188C20.847 22.0818 20.4175 22.0784 20.1552 21.8111C20.0514 21.7086 20.0098 21.6061 20.0306 21.5036C20.0566 21.401 20.1137 21.3037 20.202 21.2114L22.438 19.0202C22.5627 18.8972 22.6796 18.787 22.7886 18.6896C22.875 18.6125 22.8085 18.4583 22.6933 18.47C22.5599 18.4835 22.4229 18.4952 22.2822 18.5051C21.9965 18.5256 21.7005 18.5359 21.394 18.5359H11.8766C11.5305 18.5359 11.25 18.2553 11.25 17.9093C11.25 17.5632 11.5305 17.2827 11.8766 17.2827H21.394C21.7005 17.2827 21.9991 17.2929 22.29 17.3134C22.4286 17.3232 22.5637 17.3347 22.6952 17.348C22.812 17.3597 22.878 17.2051 22.7886 17.1289C22.6796 17.0315 22.5627 16.9213 22.438 16.7983L20.1864 14.5917C20.0929 14.4995 20.0358 14.4021 20.015 14.2996C19.9942 14.1971 20.0332 14.0946 20.1319 13.9921C20.3983 13.7206 20.8346 13.7176 21.1049 13.9852L24.3526 17.2013Z"
                                                        fill="white" />
                                                </svg></button>
                                        </div>



                                    </div>
                                    <button class="book"><svg xmlns="http://www.w3.org/2000/svg" width="17"
                                            height="17" viewBox="0 0 17 17" fill="none">
                                            <path d="M2.28125 15.5449V12.6621H5.16408" stroke="white"
                                                stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M15.4154 6.67233C15.7213 8.30432 15.4767 9.99169 14.72 11.4697C13.9633 12.9476 12.7373 14.1326 11.2344 14.8385C9.73156 15.5444 8.03684 15.7315 6.4162 15.3702C4.79556 15.009 3.34071 14.1199 2.27994 12.8425M0.678108 9.42728C0.372236 7.79528 0.616841 6.10792 1.37354 4.62995C2.13024 3.15198 3.35621 1.96706 4.85908 1.26111C6.36195 0.555159 8.05666 0.368136 9.6773 0.729384C11.2979 1.09063 12.7528 1.97971 13.8136 3.25711"
                                                stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M13.8118 0.554688V3.43751H10.9289M4.66343 8.43829C4.25542 8.36738 4.25542 7.78152 4.66343 7.71061C5.38562 7.58429 6.05393 7.24607 6.5834 6.73895C7.11287 6.23183 7.47959 5.57872 7.63693 4.86265L7.66134 4.7499C7.74968 4.34653 8.32392 4.34421 8.41576 4.74641L8.44598 4.87776C8.60835 5.59106 8.97807 6.2404 9.50862 6.74406C10.0392 7.24772 10.7068 7.5832 11.4276 7.70828C11.8379 7.77919 11.8379 8.36854 11.4276 8.44061C10.707 8.56561 10.0394 8.90092 9.50889 9.40436C8.97836 9.9078 8.60855 10.5569 8.44598 11.27L8.41576 11.4002C8.32392 11.8024 7.74968 11.8 7.66134 11.3967L7.63809 11.2851C7.48059 10.5687 7.11353 9.91536 6.58361 9.40821C6.0537 8.90106 5.38488 8.56303 4.66227 8.43713"
                                                stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg> Book Again</button>
                                </div>


                                <!-- Card 2 -->
                                <div class="card d-flex flex-column gap-25">
                                    <div class="card-body">

                                        <div class="card-img">
                                            <div class="top-left-svg left-svg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22"
                                                    viewBox="0 0 21 22" fill="none">
                                                    <path
                                                        d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z"
                                                        fill="#C9DDA0"></path>
                                                </svg>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130"
                                                fill="none">
                                                <defs>
                                                    <pattern id="pattern-card-2" patternUnits="userSpaceOnUse"
                                                        width="255" height="130">

                                                        <image href="{{ asset('images/profile_modal_image2.jpg') }}" alt="Image" width="255" height="130"
                                                            preserveAspectRatio="xMinYMax slice">
                                                        </image>
                                                    </pattern>
                                                </defs>

                                                <path
                                                    d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z"
                                                    fill="url(#pattern-card-2)"></path>
                                            </svg>
                                            <span class="delete cursor"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="13" height="15" viewBox="0 0 13 15" fill="none">
                                                    <path
                                                        d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                                        fill="#3B3731"></path>
                                                </svg></span>
                                        </div>

                                        <div class="group-icon mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                viewBox="0 0 82 24" fill="none">
                                                <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                <path
                                                    d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                    fill="white" />
                                                <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                    fill="#FEFEFE" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                    fill="#FEFEFE" />
                                                <rect x="58" width="24" height="24" rx="12" fill="#D8E8B7" />
                                                <path
                                                    d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                    fill="white" />
                                            </svg>
                                        </div>

                                        <div class="labels">
                                            <span>Home Visit</span>
                                            <span>Mobile Station</span>
                                        </div>
                                        <div class="card-title mt-1">
                                            <h3>Ken T.</h3>
                                            <p class="sub">Ken’s Grooming Mobile</p>
                                        </div>

                                        <div class="meta mt-4">
                                            <div class="meta-location">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>

                                            <div class="meta-rating">

                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 14 14" fill="none">
                                                        <path
                                                            d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z"
                                                            fill="#FFC97A" />
                                                    </svg></span>4.3 <strong>(20)</strong>
                                            </div>

                                        </div>

                                        <div class="card-review mt-4">
                                            <p class="review">
                                                “Hands down the best groomer we’ve tried. The studio is
                                                spotless...”
                                            </p>
                                        </div>

                                        <div class="bottom mt-4">
                                            <p>From <b>£38</b></p>
                                            <button class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="36"
                                                    height="36" viewBox="0 0 36 36" fill="none">
                                                    <rect width="36" height="36" rx="18" fill="#D8E8B7" />
                                                    <path
                                                        d="M24.3526 17.2013C24.7483 17.5931 24.7477 18.2326 24.3514 18.6236L21.1135 21.8188C20.847 22.0818 20.4175 22.0784 20.1552 21.8111C20.0514 21.7086 20.0098 21.6061 20.0306 21.5036C20.0566 21.401 20.1137 21.3037 20.202 21.2114L22.438 19.0202C22.5627 18.8972 22.6796 18.787 22.7886 18.6896C22.875 18.6125 22.8085 18.4583 22.6933 18.47C22.5599 18.4835 22.4229 18.4952 22.2822 18.5051C21.9965 18.5256 21.7005 18.5359 21.394 18.5359H11.8766C11.5305 18.5359 11.25 18.2553 11.25 17.9093C11.25 17.5632 11.5305 17.2827 11.8766 17.2827H21.394C21.7005 17.2827 21.9991 17.2929 22.29 17.3134C22.4286 17.3232 22.5637 17.3347 22.6952 17.348C22.812 17.3597 22.878 17.2051 22.7886 17.1289C22.6796 17.0315 22.5627 16.9213 22.438 16.7983L20.1864 14.5917C20.0929 14.4995 20.0358 14.4021 20.015 14.2996C19.9942 14.1971 20.0332 14.0946 20.1319 13.9921C20.3983 13.7206 20.8346 13.7176 21.1049 13.9852L24.3526 17.2013Z"
                                                        fill="white" />
                                                </svg></button>
                                        </div>

                                    </div>

                                    <button class="book"><svg xmlns="http://www.w3.org/2000/svg" width="17"
                                            height="17" viewBox="0 0 17 17" fill="none">
                                            <path d="M2.28125 15.5449V12.6621H5.16408" stroke="white"
                                                stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M15.4154 6.67233C15.7213 8.30432 15.4767 9.99169 14.72 11.4697C13.9633 12.9476 12.7373 14.1326 11.2344 14.8385C9.73156 15.5444 8.03684 15.7315 6.4162 15.3702C4.79556 15.009 3.34071 14.1199 2.27994 12.8425M0.678108 9.42728C0.372236 7.79528 0.616841 6.10792 1.37354 4.62995C2.13024 3.15198 3.35621 1.96706 4.85908 1.26111C6.36195 0.555159 8.05666 0.368136 9.6773 0.729384C11.2979 1.09063 12.7528 1.97971 13.8136 3.25711"
                                                stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M13.8118 0.554688V3.43751H10.9289M4.66343 8.43829C4.25542 8.36738 4.25542 7.78152 4.66343 7.71061C5.38562 7.58429 6.05393 7.24607 6.5834 6.73895C7.11287 6.23183 7.47959 5.57872 7.63693 4.86265L7.66134 4.7499C7.74968 4.34653 8.32392 4.34421 8.41576 4.74641L8.44598 4.87776C8.60835 5.59106 8.97807 6.2404 9.50862 6.74406C10.0392 7.24772 10.7068 7.5832 11.4276 7.70828C11.8379 7.77919 11.8379 8.36854 11.4276 8.44061C10.707 8.56561 10.0394 8.90092 9.50889 9.40436C8.97836 9.9078 8.60855 10.5569 8.44598 11.27L8.41576 11.4002C8.32392 11.8024 7.74968 11.8 7.66134 11.3967L7.63809 11.2851C7.48059 10.5687 7.11353 9.91536 6.58361 9.40821C6.0537 8.90106 5.38488 8.56303 4.66227 8.43713"
                                                stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg> Book Again</button>
                                </div>


                                <!-- Card 3 -->
                                <div class="card d-flex flex-column gap-25">
                                    <div class="card-body">

                                        <div class="card-img">
                                            <div class="top-left-svg left-svg">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22"
                                                    viewBox="0 0 21 22" fill="none">
                                                    <path
                                                        d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z"
                                                        fill="#C9DDA0"></path>
                                                </svg>
                                            </div>
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130"
                                                fill="none">
                                                <defs>
                                                    <pattern id="pattern-card-3" patternUnits="userSpaceOnUse"
                                                        width="255" height="130">

                                                        <image href="{{ asset('images/profile_modal_image3.jpg') }}" alt="Image" width="255" height="130"
                                                            preserveAspectRatio="xMinYMax slice">
                                                        </image>
                                                    </pattern>
                                                </defs>

                                                <path
                                                    d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z"
                                                    fill="url(#pattern-card-3)"></path>
                                            </svg>
                                            <span class="delete cursor"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="13" height="15" viewBox="0 0 13 15" fill="none">
                                                    <path
                                                        d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                                        fill="#3B3731"></path>
                                                </svg></span>
                                        </div>

                                        <div class="group-icon mt-3">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                viewBox="0 0 82 24" fill="none">
                                                <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                <path
                                                    d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                    fill="white" />
                                                <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                    fill="#FEFEFE" />
                                                <path fill-rule="evenodd" clip-rule="evenodd"
                                                    d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                    fill="#FEFEFE" />
                                                <rect x="58" width="24" height="24" rx="12" fill="#D8E8B7" />
                                                <path
                                                    d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                    fill="white" />
                                            </svg>
                                        </div>

                                        <div class="labels">
                                            <span>Salons</span>
                                        </div>
                                        <div class="card-title mt-1">
                                            <h3>Cathy P.</h3>
                                            <p class="sub">Cathy’s Services</p>
                                        </div>

                                        <div class="meta mt-4">
                                            <div class="meta-location">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>

                                            <div class="meta-rating">

                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                        viewBox="0 0 14 14" fill="none">
                                                        <path
                                                            d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z"
                                                            fill="#FFC97A" />
                                                    </svg></span>4.3 <strong>(20)</strong>
                                            </div>

                                        </div>

                                        <div class="card-review mt-4">
                                            <p class="review">
                                                “Hands down the best groomer we’ve tried. The studio is
                                                spotless...”
                                            </p>
                                        </div>

                                        <div class="bottom mt-4">
                                            <p>From <b>£38</b></p>
                                            <button class="arrow"><svg xmlns="http://www.w3.org/2000/svg" width="36"
                                                    height="36" viewBox="0 0 36 36" fill="none">
                                                    <rect width="36" height="36" rx="18" fill="#D8E8B7" />
                                                    <path
                                                        d="M24.3526 17.2013C24.7483 17.5931 24.7477 18.2326 24.3514 18.6236L21.1135 21.8188C20.847 22.0818 20.4175 22.0784 20.1552 21.8111C20.0514 21.7086 20.0098 21.6061 20.0306 21.5036C20.0566 21.401 20.1137 21.3037 20.202 21.2114L22.438 19.0202C22.5627 18.8972 22.6796 18.787 22.7886 18.6896C22.875 18.6125 22.8085 18.4583 22.6933 18.47C22.5599 18.4835 22.4229 18.4952 22.2822 18.5051C21.9965 18.5256 21.7005 18.5359 21.394 18.5359H11.8766C11.5305 18.5359 11.25 18.2553 11.25 17.9093C11.25 17.5632 11.5305 17.2827 11.8766 17.2827H21.394C21.7005 17.2827 21.9991 17.2929 22.29 17.3134C22.4286 17.3232 22.5637 17.3347 22.6952 17.348C22.812 17.3597 22.878 17.2051 22.7886 17.1289C22.6796 17.0315 22.5627 16.9213 22.438 16.7983L20.1864 14.5917C20.0929 14.4995 20.0358 14.4021 20.015 14.2996C19.9942 14.1971 20.0332 14.0946 20.1319 13.9921C20.3983 13.7206 20.8346 13.7176 21.1049 13.9852L24.3526 17.2013Z"
                                                        fill="white" />
                                                </svg></button>
                                        </div>

                                    </div>

                                    <button class="book"><svg xmlns="http://www.w3.org/2000/svg" width="17"
                                            height="17" viewBox="0 0 17 17" fill="none">
                                            <path d="M2.28125 15.5449V12.6621H5.16408" stroke="white"
                                                stroke-width="1.1" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M15.4154 6.67233C15.7213 8.30432 15.4767 9.99169 14.72 11.4697C13.9633 12.9476 12.7373 14.1326 11.2344 14.8385C9.73156 15.5444 8.03684 15.7315 6.4162 15.3702C4.79556 15.009 3.34071 14.1199 2.27994 12.8425M0.678108 9.42728C0.372236 7.79528 0.616841 6.10792 1.37354 4.62995C2.13024 3.15198 3.35621 1.96706 4.85908 1.26111C6.36195 0.555159 8.05666 0.368136 9.6773 0.729384C11.2979 1.09063 12.7528 1.97971 13.8136 3.25711"
                                                stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                            <path
                                                d="M13.8118 0.554688V3.43751H10.9289M4.66343 8.43829C4.25542 8.36738 4.25542 7.78152 4.66343 7.71061C5.38562 7.58429 6.05393 7.24607 6.5834 6.73895C7.11287 6.23183 7.47959 5.57872 7.63693 4.86265L7.66134 4.7499C7.74968 4.34653 8.32392 4.34421 8.41576 4.74641L8.44598 4.87776C8.60835 5.59106 8.97807 6.2404 9.50862 6.74406C10.0392 7.24772 10.7068 7.5832 11.4276 7.70828C11.8379 7.77919 11.8379 8.36854 11.4276 8.44061C10.707 8.56561 10.0394 8.90092 9.50889 9.40436C8.97836 9.9078 8.60855 10.5569 8.44598 11.27L8.41576 11.4002C8.32392 11.8024 7.74968 11.8 7.66134 11.3967L7.63809 11.2851C7.48059 10.5687 7.11353 9.91536 6.58361 9.40821C6.0537 8.90106 5.38488 8.56303 4.66227 8.43713"
                                                stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                stroke-linejoin="round" />
                                        </svg> Book Again</button>
                                </div>

                            </div>

                        </section>

                        <section id="spaces-sec" class="tab-content favourite-spaces mt-5" style="display:none;">
                            <section class="favourite-spaces">
                                <div class="favourite-title">
                                    <h3 class="section-title">Favourites</h3>
                                    <hr class="divider mt-4">

                                </div>

                                <!-- Top Header -->
                                <div class="top-bar">
                                    <div class="tabs">
                                        <button class="tab-link"
                                            onclick="showSection(event, 'groomers-sec')">Groomers</button>
                                        <button class="tab-link active"
                                            onclick="showSection(event, 'spaces-sec')">Space</button>
                                    </div>

                                    <div class="search-area">
                                        <input type="text" placeholder="Type to search....">
                                        <svg class="search-icon" xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" viewBox="0 0 16 16" fill="none">
                                            <path
                                                d="M5.73535 0.5C8.6267 0.500031 10.9707 2.844 10.9707 5.73535C10.9707 7.22006 10.3528 8.55933 9.35938 9.5127C8.41826 10.4158 7.14221 10.9707 5.73535 10.9707C2.844 10.9707 0.500031 8.6267 0.5 5.73535C0.5 2.84398 2.84398 0.5 5.73535 0.5Z"
                                                stroke="#9D9B98" />
                                            <path
                                                d="M14.6466 15.3537C14.8419 15.549 15.1585 15.549 15.3537 15.3537C15.549 15.1585 15.549 14.8419 15.3537 14.6466L15.0002 15.0002L14.6466 15.3537ZM9.70605 9.70605L9.3525 10.0596L14.6466 15.3537L15.0002 15.0002L15.3537 14.6466L10.0596 9.3525L9.70605 9.70605Z"
                                                fill="#9D9B98" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="feature-section mb-4">
                                    <div class="feature-heading">
                                        <h2>Favourite Spaces</h2>
                                    </div>
                                    <div class="filter-section">
                                        <button class="filter">Venue <svg xmlns="http://www.w3.org/2000/svg"
                                                width="13" height="7" viewBox="0 0 13 7" fill="none">
                                                <path d="M11.9102 0.5L6.15672 6.25344L0.499867 0.596581"
                                                    stroke="#FBAC83" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg></button>
                                        <button class="filter">Sort <svg xmlns="http://www.w3.org/2000/svg"
                                                width="13" height="7" viewBox="0 0 13 7" fill="none">
                                                <path d="M11.9102 0.5L6.15672 6.25344L0.499867 0.596581"
                                                    stroke="#FBAC83" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg></button>
                                    </div>
                                </div>

                                <span class="tag mt-2">Garden / Shed ✕</span>

                                <!-- Cards -->
                                <div class="spaces-grid">

                                    <!-- CARD 1 -->
                                    <div class="space-card d-flex flex-column gap-25">

                                        <div class="card-body">
                                            <div class="card-img">
                                                <div class="top-left-svg left-svg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22"
                                                        viewBox="0 0 21 22" fill="none">
                                                        <path
                                                            d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z"
                                                            fill="#C9DDA0"></path>
                                                    </svg>
                                                </div>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130"
                                                    fill="none">
                                                    <defs>
                                                        <pattern id="pattern-card-4" patternUnits="userSpaceOnUse"
                                                            width="255" height="130">

                                                            <image href="{{ asset('images/space_card1.png') }}" alt="Image" width="255" height="130"
                                                                preserveAspectRatio="xMinYMax slice">
                                                            </image>
                                                        </pattern>
                                                    </defs>

                                                    <path
                                                        d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z"
                                                        fill="url(#pattern-card-4)"></path>
                                                </svg>
                                                <span class="delete cursor"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="13" height="15" viewBox="0 0 13 15" fill="none">
                                                        <path
                                                            d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                                            fill="#3B3731"></path>
                                                    </svg></span>
                                            </div>

                                            <div class="labels d-flex align-items-center justify-content-between">
                                                <span>Salons</span>
                                                <div class="label-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                        viewBox="0 0 82 24" fill="none">
                                                        <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                        <path
                                                            d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                            fill="white" />
                                                        <rect x="29" width="24" height="24" rx="12"
                                                            fill="#FFA899" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                            fill="#FEFEFE" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                            fill="#FEFEFE" />
                                                        <rect x="58" width="24" height="24" rx="12"
                                                            fill="#D8E8B7" />
                                                        <path
                                                            d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                            fill="white" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <div class="sub-title mt-1">
                                                <h3>Furs & Co. Studio</h3>
                                                <p class="sub">Hosted by <strong>Dev É.</strong> </p>
                                            </div>

                                            <div class="meta mt-4">
                                                <div class="meta-location">
                                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                            height="14" viewBox="0 0 10 14" fill="none">
                                                            <path
                                                                d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                                fill="#FFC97A" />
                                                        </svg> 2.5 mi</span>
                                                </div>

                                                <div class="meta-rating">

                                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 14 14" fill="none">
                                                            <path
                                                                d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z"
                                                                fill="#FFC97A" />
                                                        </svg></span>4.3 <strong>(20)</strong>
                                                </div>

                                            </div>
                                            <div class="card-review mt-4">
                                                <p class="review">
                                                    “I rent this space weekly — always clean and ready.”
                                                </p>
                                            </div>

                                            <div class="features mt-4">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg> Grooming Table</span>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg> Bath</span>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg> Dryer</span>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg> Parking</span>
                                                <span class="plus">+ 2</span>
                                            </div>

                                            <div class="bottom mt-4">
                                                <p>From <b>£38</b> <strong> / hour</strong></p>
                                                <button class="arrow"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="36" height="36" viewBox="0 0 36 36" fill="none">
                                                        <rect width="36" height="36" rx="18" fill="#D8E8B7" />
                                                        <path
                                                            d="M24.3526 17.2013C24.7483 17.5931 24.7477 18.2326 24.3514 18.6236L21.1135 21.8188C20.847 22.0818 20.4175 22.0784 20.1552 21.8111C20.0514 21.7086 20.0098 21.6061 20.0306 21.5036C20.0566 21.401 20.1137 21.3037 20.202 21.2114L22.438 19.0202C22.5627 18.8972 22.6796 18.787 22.7886 18.6896C22.875 18.6125 22.8085 18.4583 22.6933 18.47C22.5599 18.4835 22.4229 18.4952 22.2822 18.5051C21.9965 18.5256 21.7005 18.5359 21.394 18.5359H11.8766C11.5305 18.5359 11.25 18.2553 11.25 17.9093C11.25 17.5632 11.5305 17.2827 11.8766 17.2827H21.394C21.7005 17.2827 21.9991 17.2929 22.29 17.3134C22.4286 17.3232 22.5637 17.3347 22.6952 17.348C22.812 17.3597 22.878 17.2051 22.7886 17.1289C22.6796 17.0315 22.5627 16.9213 22.438 16.7983L20.1864 14.5917C20.0929 14.4995 20.0358 14.4021 20.015 14.2996C19.9942 14.1971 20.0332 14.0946 20.1319 13.9921C20.3983 13.7206 20.8346 13.7176 21.1049 13.9852L24.3526 17.2013Z"
                                                            fill="white" />
                                                    </svg></button>
                                            </div>

                                        </div>

                                        <button class="book"><svg xmlns="http://www.w3.org/2000/svg" width="17"
                                                height="17" viewBox="0 0 17 17" fill="none">
                                                <path d="M2.28125 15.5449V12.6621H5.16408" stroke="white"
                                                    stroke-width="1.1" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M15.4154 6.67233C15.7213 8.30432 15.4767 9.99169 14.72 11.4697C13.9633 12.9476 12.7373 14.1326 11.2344 14.8385C9.73156 15.5444 8.03684 15.7315 6.4162 15.3702C4.79556 15.009 3.34071 14.1199 2.27994 12.8425M0.678108 9.42728C0.372236 7.79528 0.616841 6.10792 1.37354 4.62995C2.13024 3.15198 3.35621 1.96706 4.85908 1.26111C6.36195 0.555159 8.05666 0.368136 9.6773 0.729384C11.2979 1.09063 12.7528 1.97971 13.8136 3.25711"
                                                    stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M13.8118 0.554688V3.43751H10.9289M4.66343 8.43829C4.25542 8.36738 4.25542 7.78152 4.66343 7.71061C5.38562 7.58429 6.05393 7.24607 6.5834 6.73895C7.11287 6.23183 7.47959 5.57872 7.63693 4.86265L7.66134 4.7499C7.74968 4.34653 8.32392 4.34421 8.41576 4.74641L8.44598 4.87776C8.60835 5.59106 8.97807 6.2404 9.50862 6.74406C10.0392 7.24772 10.7068 7.5832 11.4276 7.70828C11.8379 7.77919 11.8379 8.36854 11.4276 8.44061C10.707 8.56561 10.0394 8.90092 9.50889 9.40436C8.97836 9.9078 8.60855 10.5569 8.44598 11.27L8.41576 11.4002C8.32392 11.8024 7.74968 11.8 7.66134 11.3967L7.63809 11.2851C7.48059 10.5687 7.11353 9.91536 6.58361 9.40821C6.0537 8.90106 5.38488 8.56303 4.66227 8.43713"
                                                    stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>Book Again</button>

                                    </div>


                                    <!-- CARD 2 -->
                                    <div class="space-card d-flex flex-column gap-25">

                                        <div class="card-body">
                                            <div class="card-img">
                                                <div class="top-left-svg left-svg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22"
                                                        viewBox="0 0 21 22" fill="none">
                                                        <path
                                                            d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z"
                                                            fill="#C9DDA0"></path>
                                                    </svg>
                                                </div>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130"
                                                    fill="none">
                                                    <defs>
                                                        <pattern id="pattern-card-5" patternUnits="userSpaceOnUse"
                                                            width="255" height="130">

                                                            <image href="{{ asset('images/space_card2.png') }}" alt="Image" width="255" height="130"
                                                                preserveAspectRatio="xMinYMax slice">
                                                            </image>
                                                        </pattern>
                                                    </defs>

                                                    <path
                                                        d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z"
                                                        fill="url(#pattern-card-5)"></path>
                                                </svg>
                                                <span class="delete cursor"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="13" height="15" viewBox="0 0 13 15" fill="none">
                                                        <path
                                                            d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                                            fill="#3B3731"></path>
                                                    </svg></span>
                                            </div>

                                            <div class="labels d-flex align-items-center justify-content-between">
                                                <span>Mobile Station</span>
                                                <div class="label-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                        viewBox="0 0 82 24" fill="none">
                                                        <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                        <path
                                                            d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                            fill="white" />
                                                        <rect x="29" width="24" height="24" rx="12"
                                                            fill="#FFA899" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                            fill="#FEFEFE" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                            fill="#FEFEFE" />
                                                        <rect x="58" width="24" height="24" rx="12"
                                                            fill="#D8E8B7" />
                                                        <path
                                                            d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                            fill="white" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <div class="sub-title mt-1">
                                                <h3>Paws & Bubbles</h3>
                                                <p class="sub">Hosted by <strong>Patrick B.</strong> </p>
                                            </div>

                                            <div class="meta mt-4">
                                                <div class="meta-location">
                                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                            height="14" viewBox="0 0 10 14" fill="none">
                                                            <path
                                                                d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                                fill="#FFC97A" />
                                                        </svg> 2.5 mi</span>
                                                </div>

                                                <div class="meta-rating">

                                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 14 14" fill="none">
                                                            <path
                                                                d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z"
                                                                fill="#FFC97A" />
                                                        </svg></span>4.3 <strong>(20)</strong>
                                                </div>

                                            </div>
                                            <div class="card-review mt-4">
                                                <p class="review">
                                                    “Smooth check-in and secure entry.”
                                                </p>
                                            </div>

                                            <div class="features mt-4">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg> Waiting area</span>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg> Towels</span>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg> Wi-Fi</span>
                                                <span class="plus">+ 5</span>
                                            </div>

                                            <div class="bottom mt-4">
                                                <p>From <b>£38</b> <strong> / hour</strong></p>
                                                <button class="arrow"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="36" height="36" viewBox="0 0 36 36" fill="none">
                                                        <rect width="36" height="36" rx="18" fill="#D8E8B7" />
                                                        <path
                                                            d="M24.3526 17.2013C24.7483 17.5931 24.7477 18.2326 24.3514 18.6236L21.1135 21.8188C20.847 22.0818 20.4175 22.0784 20.1552 21.8111C20.0514 21.7086 20.0098 21.6061 20.0306 21.5036C20.0566 21.401 20.1137 21.3037 20.202 21.2114L22.438 19.0202C22.5627 18.8972 22.6796 18.787 22.7886 18.6896C22.875 18.6125 22.8085 18.4583 22.6933 18.47C22.5599 18.4835 22.4229 18.4952 22.2822 18.5051C21.9965 18.5256 21.7005 18.5359 21.394 18.5359H11.8766C11.5305 18.5359 11.25 18.2553 11.25 17.9093C11.25 17.5632 11.5305 17.2827 11.8766 17.2827H21.394C21.7005 17.2827 21.9991 17.2929 22.29 17.3134C22.4286 17.3232 22.5637 17.3347 22.6952 17.348C22.812 17.3597 22.878 17.2051 22.7886 17.1289C22.6796 17.0315 22.5627 16.9213 22.438 16.7983L20.1864 14.5917C20.0929 14.4995 20.0358 14.4021 20.015 14.2996C19.9942 14.1971 20.0332 14.0946 20.1319 13.9921C20.3983 13.7206 20.8346 13.7176 21.1049 13.9852L24.3526 17.2013Z"
                                                            fill="white" />
                                                    </svg></button>
                                            </div>

                                        </div>

                                        <button class="book"><svg xmlns="http://www.w3.org/2000/svg" width="17"
                                                height="17" viewBox="0 0 17 17" fill="none">
                                                <path d="M2.28125 15.5449V12.6621H5.16408" stroke="white"
                                                    stroke-width="1.1" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M15.4154 6.67233C15.7213 8.30432 15.4767 9.99169 14.72 11.4697C13.9633 12.9476 12.7373 14.1326 11.2344 14.8385C9.73156 15.5444 8.03684 15.7315 6.4162 15.3702C4.79556 15.009 3.34071 14.1199 2.27994 12.8425M0.678108 9.42728C0.372236 7.79528 0.616841 6.10792 1.37354 4.62995C2.13024 3.15198 3.35621 1.96706 4.85908 1.26111C6.36195 0.555159 8.05666 0.368136 9.6773 0.729384C11.2979 1.09063 12.7528 1.97971 13.8136 3.25711"
                                                    stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M13.8118 0.554688V3.43751H10.9289M4.66343 8.43829C4.25542 8.36738 4.25542 7.78152 4.66343 7.71061C5.38562 7.58429 6.05393 7.24607 6.5834 6.73895C7.11287 6.23183 7.47959 5.57872 7.63693 4.86265L7.66134 4.7499C7.74968 4.34653 8.32392 4.34421 8.41576 4.74641L8.44598 4.87776C8.60835 5.59106 8.97807 6.2404 9.50862 6.74406C10.0392 7.24772 10.7068 7.5832 11.4276 7.70828C11.8379 7.77919 11.8379 8.36854 11.4276 8.44061C10.707 8.56561 10.0394 8.90092 9.50889 9.40436C8.97836 9.9078 8.60855 10.5569 8.44598 11.27L8.41576 11.4002C8.32392 11.8024 7.74968 11.8 7.66134 11.3967L7.63809 11.2851C7.48059 10.5687 7.11353 9.91536 6.58361 9.40821C6.0537 8.90106 5.38488 8.56303 4.66227 8.43713"
                                                    stroke="white" stroke-width="1.1" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>Book Again</button>

                                    </div>


                                    <!-- CARD 3 -->
                                    <div class="space-card d-flex flex-column gap-25">

                                        <div class="card-body">
                                            <div class="card-img">
                                                <div class="top-left-svg left-svg">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="21" height="22"
                                                        viewBox="0 0 21 22" fill="none">
                                                        <path
                                                            d="M10.9482 0.125295C10.7667 0.043205 10.5723 0 10.3692 0C10.1662 0 9.97174 0.043205 9.79028 0.125295L1.65477 3.57738C0.704262 3.97918 -0.00430085 4.91673 1.96518e-05 6.0487C0.0216222 10.3346 1.78439 18.1764 9.22861 21.7408C9.95014 22.0864 10.7883 22.0864 11.5098 21.7408C18.9541 18.1764 20.7168 10.3346 20.7384 6.0487C20.7428 4.91673 20.0342 3.97918 19.0837 3.57738L10.9482 0.125295ZM6.26043 12.3653C6.46781 12.4171 6.68816 12.443 6.91282 12.443C8.43796 12.443 9.67795 11.2031 9.67795 9.67793V6.9128H11.5876C12.1104 6.9128 12.59 7.2066 12.8233 7.67753L13.1343 8.29537H15.8995C16.2797 8.29537 16.5907 8.60644 16.5907 8.98665V10.3692C16.5907 12.2789 15.044 13.8256 13.1343 13.8256H11.0605V16.0161C11.0605 16.3315 10.8056 16.5907 10.4859 16.5907C10.4081 16.5907 10.3303 16.5734 10.2612 16.5432L5.99688 14.7156C5.71172 14.5947 5.53026 14.3138 5.53026 14.0071C5.53026 13.8861 5.55619 13.7694 5.61235 13.6614L6.26043 12.3653ZM6.22154 6.9128H8.29538V9.67793C8.29538 10.4427 7.67755 11.0605 6.91282 11.0605C6.1481 11.0605 5.53026 10.4427 5.53026 9.67793V7.60408C5.53026 7.22388 5.84134 6.9128 6.22154 6.9128ZM11.7518 8.98665C11.7518 8.80331 11.679 8.62748 11.5493 8.49784C11.4197 8.3682 11.2438 8.29537 11.0605 8.29537C10.8772 8.29537 10.7013 8.3682 10.5717 8.49784C10.4421 8.62748 10.3692 8.80331 10.3692 8.98665C10.3692 9.16998 10.4421 9.34581 10.5717 9.47545C10.7013 9.60509 10.8772 9.67793 11.0605 9.67793C11.2438 9.67793 11.4197 9.60509 11.5493 9.47545C11.679 9.34581 11.7518 9.16998 11.7518 8.98665Z"
                                                            fill="#C9DDA0"></path>
                                                    </svg>
                                                </div>
                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 255 130"
                                                    fill="none">
                                                    <defs>
                                                        <pattern id="pattern-card-6" patternUnits="userSpaceOnUse"
                                                            width="255" height="130">

                                                            <image href="{{ asset('images/space_card3.png') }}" alt="Image" width="255" height="130"
                                                                preserveAspectRatio="xMinYMax slice">
                                                            </image>
                                                        </pattern>
                                                    </defs>

                                                    <path
                                                        d="M255 124.417C255 127.178 252.761 129.417 250 129.417H5C2.23858 129.417 0 127.178 0 124.417V37C0 34.2386 2.23858 32 5 32H27C29.7614 32 32 29.7614 32 27V5C32 2.23858 34.2386 0 37 0H250C252.761 0 255 2.23858 255 5V124.417Z"
                                                        fill="url(#pattern-card-6)"></path>
                                                </svg>
                                                <span class="delete cursor"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="13" height="15" viewBox="0 0 13 15" fill="none">
                                                        <path
                                                            d="M2.42915 15C2.01624 15 1.66308 14.8494 1.36965 14.5482C1.07622 14.247 0.929196 13.8859 0.928577 13.4648V1.68358H0.464292C0.332435 1.68358 0.222244 1.63792 0.133721 1.54661C0.0451968 1.45529 0.000625407 1.34211 6.36006e-06 1.20704C-0.000612687 1.07197 0.0439588 0.9591 0.133721 0.868421C0.223482 0.777743 0.333673 0.732403 0.464292 0.732403H3.71429C3.71429 0.535827 3.78548 0.364616 3.92786 0.21877C4.07024 0.0729233 4.23738 0 4.42929 0H8.57071C8.76262 0 8.92976 0.0729233 9.07214 0.21877C9.21452 0.364616 9.28571 0.535827 9.28571 0.732403H12.5357C12.6676 0.732403 12.7778 0.77806 12.8663 0.869372C12.9548 0.960685 12.9994 1.07387 13 1.20894C13.0006 1.34401 12.956 1.45688 12.8663 1.54756C12.7765 1.63824 12.6663 1.68358 12.5357 1.68358H12.0714V13.4639C12.0714 13.8862 11.9244 14.2476 11.6304 14.5482C11.3363 14.8488 10.9834 14.9994 10.5718 15H2.42915ZM11.1429 1.68358H1.85715V13.4639C1.85715 13.6344 1.91069 13.7746 2.01779 13.8843C2.12489 13.994 2.262 14.0488 2.42915 14.0488H10.5718C10.7383 14.0488 10.8751 13.994 10.9822 13.8843C11.0893 13.7746 11.1429 13.6344 11.1429 13.4639V1.68358ZM4.92886 12.1465C5.06072 12.1465 5.17122 12.1008 5.26036 12.0095C5.3495 11.9182 5.39376 11.8053 5.39314 11.6709V4.06151C5.39314 3.92644 5.34857 3.81357 5.25943 3.72289C5.17029 3.63221 5.05979 3.58656 4.92793 3.58592C4.79607 3.58529 4.68588 3.63094 4.59736 3.72289C4.50884 3.81484 4.46457 3.92771 4.46457 4.06151V11.6709C4.46457 11.806 4.50914 11.9188 4.59829 12.0095C4.68743 12.1008 4.79762 12.1465 4.92886 12.1465ZM8.07207 12.1465C8.20393 12.1465 8.31412 12.1008 8.40264 12.0095C8.49117 11.9182 8.53543 11.8053 8.53543 11.6709V4.06151C8.53543 3.92644 8.49086 3.81357 8.40171 3.72289C8.31257 3.63158 8.20238 3.58592 8.07114 3.58592C7.93928 3.58592 7.82878 3.63158 7.73964 3.72289C7.6505 3.8142 7.60624 3.92708 7.60686 4.06151V11.6709C7.60686 11.806 7.65143 11.9188 7.74057 12.0095C7.82971 12.1002 7.94021 12.1458 8.07207 12.1465Z"
                                                            fill="#3B3731"></path>
                                                    </svg></span>
                                            </div>

                                            <div class="labels d-flex align-items-center justify-content-between">
                                                <span>Garden / Shed</span>
                                                <div class="label-icon">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                        viewBox="0 0 82 24" fill="none">
                                                        <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                        <path
                                                            d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                            fill="white" />
                                                        <rect x="29" width="24" height="24" rx="12"
                                                            fill="#FFA899" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                            fill="#FEFEFE" />
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                            fill="#FEFEFE" />
                                                        <rect x="58" width="24" height="24" rx="12"
                                                            fill="#D8E8B7" />
                                                        <path
                                                            d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                            fill="white" />
                                                    </svg>
                                                </div>
                                            </div>

                                            <div class="sub-title mt-1">
                                                <h3>The Garden Grooming Spot</h3>
                                                <p class="sub">Hosted by <strong>Chloe D.</strong> </p>
                                            </div>

                                            <div class="meta mt-4">
                                                <div class="meta-location">
                                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="10"
                                                            height="14" viewBox="0 0 10 14" fill="none">
                                                            <path
                                                                d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                                fill="#FFC97A" />
                                                        </svg> 2.5 mi</span>
                                                </div>

                                                <div class="meta-rating">

                                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="14"
                                                            height="14" viewBox="0 0 14 14" fill="none">
                                                            <path
                                                                d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z"
                                                                fill="#FFC97A" />
                                                        </svg></span>4.3 <strong>(20)</strong>
                                                </div>

                                            </div>
                                            <div class="card-review mt-4">
                                                <p class="review">
                                                    “This space has become my go-to for weekday clients.”
                                                </p>
                                            </div>

                                            <div class="features mt-4">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg> Bath</span>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg> Wi-Fi</span>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg> Dryer</span>
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="12" height="9"
                                                        viewBox="0 0 12 9" fill="none">
                                                        <path d="M0.75 4.75L4.25 8.25L11.25 0.75" stroke="#3B3731"
                                                            stroke-width="1.5" stroke-linecap="round"
                                                            stroke-linejoin="round">
                                                        </path>
                                                    </svg> Parking</span>
                                            </div>

                                            <div class="bottom mt-4">
                                                <p>From <b>£38</b> <strong> / hour</strong></p>
                                                <button class="arrow"><svg xmlns="http://www.w3.org/2000/svg"
                                                        width="36" height="36" viewBox="0 0 36 36" fill="none">
                                                        <rect width="36" height="36" rx="18" fill="#D8E8B7" />
                                                        <path
                                                            d="M24.3526 17.2013C24.7483 17.5931 24.7477 18.2326 24.3514 18.6236L21.1135 21.8188C20.847 22.0818 20.4175 22.0784 20.1552 21.8111C20.0514 21.7086 20.0098 21.6061 20.0306 21.5036C20.0566 21.401 20.1137 21.3037 20.202 21.2114L22.438 19.0202C22.5627 18.8972 22.6796 18.787 22.7886 18.6896C22.875 18.6125 22.8085 18.4583 22.6933 18.47C22.5599 18.4835 22.4229 18.4952 22.2822 18.5051C21.9965 18.5256 21.7005 18.5359 21.394 18.5359H11.8766C11.5305 18.5359 11.25 18.2553 11.25 17.9093C11.25 17.5632 11.5305 17.2827 11.8766 17.2827H21.394C21.7005 17.2827 21.9991 17.2929 22.29 17.3134C22.4286 17.3232 22.5637 17.3347 22.6952 17.348C22.812 17.3597 22.878 17.2051 22.7886 17.1289C22.6796 17.0315 22.5627 16.9213 22.438 16.7983L20.1864 14.5917C20.0929 14.4995 20.0358 14.4021 20.015 14.2996C19.9942 14.1971 20.0332 14.0946 20.1319 13.9921C20.3983 13.7206 20.8346 13.7176 21.1049 13.9852L24.3526 17.2013Z"
                                                            fill="white" />
                                                    </svg></button>
                                            </div>

                                        </div>

                                        <button class="card-profile">View Profile <svg
                                                xmlns="http://www.w3.org/2000/svg" width="14" height="9"
                                                viewBox="0 0 14 9" fill="none">
                                                <path
                                                    d="M13.1026 3.41517C13.4983 3.80695 13.4977 4.44643 13.1014 4.83752L9.8635 8.03269C9.59697 8.2957 9.1675 8.29224 8.90524 8.02496C8.80136 7.92245 8.7598 7.81993 8.78058 7.71742C8.80655 7.61491 8.86369 7.51753 8.95199 7.42527L11.188 5.23409C11.3127 5.11108 11.4296 5.00088 11.5386 4.90349C11.625 4.82639 11.5585 4.67216 11.4433 4.68383C11.3099 4.69734 11.1729 4.70906 11.0322 4.71897C10.7465 4.73947 10.4505 4.74973 10.144 4.74973H0.6266C0.280539 4.74973 0 4.46919 0 4.12313C0 3.77707 0.280538 3.49653 0.626599 3.49653H10.144C10.4505 3.49653 10.7491 3.50678 11.04 3.52728C11.1786 3.53705 11.3137 3.54856 11.4452 3.56182C11.562 3.5736 11.628 3.41892 11.5386 3.34276C11.4296 3.24538 11.3127 3.13518 11.188 3.01216L8.9364 0.805611C8.84291 0.713351 8.78577 0.615965 8.765 0.513454C8.74422 0.410943 8.78318 0.308432 8.88186 0.205921C9.14831 -0.0654868 9.58462 -0.068569 9.85487 0.199047L13.1026 3.41517Z"
                                                    fill="white"></path>
                                            </svg></button>

                                    </div>

                                </div>
                            </section>





                        </section>
                    </section>

                    <section id="reviews-section" class="reviews-section hidden mt-5">

                        <h3 class="section-title">Reviews</h3 class="section-title">
                        <hr>

                        <!-- STATS -->
                        <div class="review-stats">

                            <div class="stat-card">
                                <p class="stat-title">Average Rating Given</p>
                                <h3><svg xmlns="http://www.w3.org/2000/svg" width="25" height="24"
                                        viewBox="0 0 25 24" fill="none">
                                        <path
                                            d="M10.251 1.39802C10.8393 -0.465986 13.4771 -0.465981 14.0655 1.39802L15.5337 6.04967C15.7964 6.88186 16.5683 7.44767 17.4409 7.44767L22.3128 7.44767C24.2361 7.44767 25.0509 9.89731 23.5106 11.0493L19.4704 14.071C18.7897 14.5801 18.5052 15.4639 18.761 16.2746L20.2806 21.0887C20.8654 22.9416 18.7314 24.456 17.1754 23.2923L13.3561 20.4358C12.6458 19.9046 11.6706 19.9046 10.9603 20.4358L7.14096 23.2923C5.58502 24.456 3.45102 22.9416 4.03585 21.0887L5.55537 16.2746C5.81124 15.4639 5.52672 14.5801 4.84598 14.071L0.805789 11.0493C-0.734458 9.89731 0.080267 7.44767 2.00365 7.44767L6.87548 7.44767C7.74814 7.44767 8.52006 6.88186 8.78273 6.04967L10.251 1.39802Z"
                                            fill="#FFC97A" />
                                    </svg> 4.8 / 5</h3>
                                <span class="mt-1">Based on 5 reviews</span>
                            </div>

                            <div class="stat-card light">
                                <p class="stat-title">Reviews Written</p>
                                <h3><svg xmlns="http://www.w3.org/2000/svg" width="26" height="24"
                                        viewBox="0 0 26 24" fill="none">
                                        <path
                                            d="M12.1137 4.38872C12.4229 3.35089 13.8162 3.35089 14.1254 4.38872L14.6037 5.99419C14.7417 6.4574 15.149 6.77256 15.6096 6.77256H17.2316C18.2442 6.77256 18.6745 8.13105 17.865 8.77242L16.492 9.86029C16.1349 10.1432 15.9857 10.6326 16.1196 11.0818L16.6294 12.7929C16.9365 13.8235 15.8093 14.6634 14.9901 14.0144L13.753 13.0342C13.3778 12.7369 12.8613 12.7369 12.4861 13.0342L11.249 14.0144C10.4298 14.6634 9.30261 13.8235 9.60969 12.7929L10.1195 11.0818C10.2534 10.6326 10.1042 10.1432 9.7471 9.86029L8.37408 8.77242C7.56458 8.13105 7.99487 6.77256 9.00751 6.77256H10.6295C11.0901 6.77256 11.4973 6.4574 11.6353 5.99419L12.1137 4.38872Z"
                                            fill="#FFC97A" />
                                        <path
                                            d="M15.0595 23.5273C14.8041 23.9786 14.2292 24.1343 13.7809 23.8736C13.3399 23.6172 13.1873 23.0537 13.4386 22.6098L16.6953 16.8558H22.2604C22.5041 16.8562 22.7455 16.808 22.9707 16.714C23.1959 16.62 23.4006 16.4821 23.5729 16.3081C23.7452 16.1341 23.8819 15.9275 23.9749 15.7001C24.068 15.4727 24.1158 15.229 24.1154 14.9829V3.74574C24.1158 3.49968 24.068 3.25598 23.9749 3.02858C23.8819 2.80119 23.7452 2.59458 23.5729 2.42059C23.4006 2.24661 23.1959 2.10867 22.9707 2.01468C22.7455 1.92069 22.5041 1.8725 22.2604 1.87287H3.71006C3.46635 1.8725 3.22497 1.92069 2.99974 2.01468C2.77451 2.10867 2.56987 2.24661 2.39754 2.42059C2.22521 2.59458 2.08859 2.80119 1.99549 3.02858C1.9024 3.25598 1.85466 3.49968 1.85503 3.74574V14.9829C1.85466 15.229 1.9024 15.4727 1.99549 15.7001C2.08859 15.9275 2.22521 16.1341 2.39754 16.3081C2.56987 16.4821 2.77451 16.62 2.99974 16.714C3.22497 16.808 3.46635 16.8562 3.71006 16.8558H11.1213C11.6384 16.8558 12.0577 17.2751 12.0577 17.7922C12.0577 18.3094 11.6384 18.7287 11.1213 18.7287H3.71006C2.72609 18.7287 1.78242 18.334 1.08665 17.6316C0.39088 16.9291 0 15.9764 0 14.9829V3.74574C0 2.75231 0.39088 1.79956 1.08665 1.0971C1.78242 0.394639 2.72609 0 3.71006 0H22.2604C23.2443 0 24.188 0.394639 24.8838 1.0971C25.5796 1.79956 25.9704 2.75231 25.9704 3.74574V14.9829C25.9704 15.9764 25.5796 16.9291 24.8838 17.6316C24.188 18.334 23.2443 18.7287 22.2604 18.7287H17.7758L15.0595 23.5273Z"
                                            fill="#FFC97A" />
                                    </svg> 5</h3>
                            </div>

                        </div>

                        <!-- FILTER BAR -->
                        <div class="review-filters">

                            <div class="left-filters">
                                <button class="pill active" data-review="all">All</button>
                                <button class="pill" data-review="written">Written Reviews</button>
                                <button class="pill" data-review="received">Reviews Received</button>
                            </div>

                            <div class="right-filters">
                                <input type="text" placeholder="Type to search ...">
                            </div>

                        </div>
                        <div class="all-reviews">
                            <h3>All Reviews</h3>
                            <div class="reviews-filter-btn d-flex align-items-center gap-10">
                                <button class="filter-btn">Groomer Venue <svg xmlns="http://www.w3.org/2000/svg"
                                        width="13" height="7" viewBox="0 0 13 7" fill="none">
                                        <path d="M11.9102 0.5L6.15672 6.25344L0.499867 0.596581" stroke="#FBAC83"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                                <button class="filter-btn">Space Venue <svg xmlns="http://www.w3.org/2000/svg"
                                        width="13" height="7" viewBox="0 0 13 7" fill="none">
                                        <path d="M11.9102 0.5L6.15672 6.25344L0.499867 0.596581" stroke="#FBAC83"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                                <button class="filter-btn">Sort <svg xmlns="http://www.w3.org/2000/svg" width="13"
                                        height="7" viewBox="0 0 13 7" fill="none">
                                        <path d="M11.9102 0.5L6.15672 6.25344L0.499867 0.596581" stroke="#FBAC83"
                                            stroke-linecap="round" stroke-linejoin="round" />
                                    </svg></button>
                            </div>
                        </div>

                        <div class="reviews-tag">
                            <span>Garden / Shed ✕</span>
                        </div>

                        <div id="written-reviews" class="review-group active">
                            <div class="reviews-list">

                                <!-- REVIEW ITEM -->
                                <div class="review-item">

                                    <div class="review-left">
                                        <div class="avatar-wrap">

                                            <img src="{{ asset('images/groomer-profile.png') }}" alt="Image" class="avatar">
                                            <div class="badge-shield" title="Verified">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="33"
                                                    viewBox="0 0 30 33" fill="none">
                                                    <ellipse cx="15.873" cy="16.5256" rx="9.3645" ry="8.81365"
                                                        fill="white"></ellipse>
                                                    <path
                                                        d="M15.44 0.185076C15.1841 0.0638192 14.9099 0 14.6235 0C14.3372 0 14.063 0.0638192 13.8071 0.185076L2.3337 5.28423C0.993208 5.87775 -0.00606541 7.26263 2.77146e-05 8.93469C0.0304934 15.2656 2.51649 26.8487 13.015 32.1138C14.0325 32.6244 15.2146 32.6244 16.2321 32.1138C26.7306 26.8487 29.2166 15.2656 29.247 8.93469C29.2531 7.26263 28.2539 5.87775 26.9134 5.28423L15.44 0.185076ZM8.82897 18.2651C9.12144 18.3416 9.43219 18.3799 9.74903 18.3799C11.8999 18.3799 13.6486 16.5483 13.6486 14.2955V10.2111H16.3418C17.0791 10.2111 17.7554 10.645 18.0844 11.3407L18.5231 12.2533H22.4227C22.9589 12.2533 23.3976 12.7128 23.3976 13.2744V15.3166C23.3976 18.1374 21.2163 20.4222 18.5231 20.4222H15.5984V23.6578C15.5984 24.1237 15.2389 24.5066 14.7881 24.5066C14.6784 24.5066 14.5687 24.4811 14.4712 24.4364L8.45729 21.7368C8.05514 21.5581 7.79923 21.1433 7.79923 20.6902C7.79923 20.5115 7.83579 20.3392 7.915 20.1796L8.82897 18.2651ZM8.77413 10.2111H11.6988V14.2955C11.6988 15.4251 10.8275 16.3377 9.74903 16.3377C8.67055 16.3377 7.79923 15.4251 7.79923 14.2955V11.2322C7.79923 10.6706 8.23794 10.2111 8.77413 10.2111ZM16.5733 13.2744C16.5733 13.0036 16.4706 12.7439 16.2878 12.5524C16.105 12.3609 15.857 12.2533 15.5984 12.2533C15.3399 12.2533 15.0919 12.3609 14.9091 12.5524C14.7262 12.7439 14.6235 13.0036 14.6235 13.2744C14.6235 13.5452 14.7262 13.8049 14.9091 13.9964C15.0919 14.1879 15.3399 14.2955 15.5984 14.2955C15.857 14.2955 16.105 14.1879 16.2878 13.9964C16.4706 13.8049 16.5733 13.5452 16.5733 13.2744Z"
                                                        fill="#C9DDA0"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="review-user">
                                            <h4>Cathy P.</h4>
                                            <p>Sarah's Grooming Studio</p>

                                            <div class="reviews-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="53" height="24"
                                                    viewBox="0 0 53 24" fill="none">
                                                    <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                    <path
                                                        d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                        fill="white" />
                                                    <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M43.4127 14.892C43.4417 14.3426 43.3153 13.7961 43.0481 13.3151C42.7809 12.8341 42.3837 12.4381 41.9019 12.1723C41.8164 12.1229 41.715 12.1085 41.619 12.1323C41.5231 12.1562 41.4402 12.2163 41.3877 12.3001L40.775 13.2808L40.2653 12.6643C40.2373 12.6305 40.2026 12.6029 40.1634 12.5831C40.1242 12.5634 40.0813 12.552 40.0375 12.5497C39.9936 12.5474 39.9498 12.5542 39.9087 12.5696C39.8676 12.5851 39.8302 12.6089 39.7988 12.6395C39.4987 12.9299 39.2643 13.2811 39.1114 13.6697C38.9584 14.0582 38.8905 14.475 38.9121 14.892C38.9121 15.4888 39.1492 16.0611 39.5711 16.483C39.9931 16.905 40.5654 17.142 41.1621 17.142C41.7588 17.142 42.3311 16.905 42.7531 16.483C43.175 16.0611 43.4121 15.4888 43.4121 14.892M40.0098 13.3539L39.9704 13.406C39.6715 13.8236 39.5217 14.3295 39.5452 14.8425L39.5471 14.88C39.5471 15.3081 39.7172 15.7187 40.0199 16.0215C40.3227 16.3243 40.7333 16.4943 41.1615 16.4943C41.5896 16.4943 42.0003 16.3243 42.303 16.0215C42.6058 15.7187 42.7759 15.3081 42.7759 14.88L42.7778 14.8431C42.7822 14.8018 42.8814 13.6653 41.8416 12.8925L41.7901 12.855L41.1265 13.9164C41.0945 13.9676 41.0507 14.0104 40.9989 14.0414C40.9471 14.0724 40.8886 14.0906 40.8283 14.0946C40.7681 14.0986 40.7077 14.0882 40.6523 14.0643C40.5968 14.0404 40.5478 14.0036 40.5094 13.9571L40.0098 13.3539Z"
                                                        fill="#FEFEFE" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                        fill="#FEFEFE" />
                                                </svg>
                                            </div>

                                            <div class="review-meta">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-content">

                                        <div class="review-top">
                                            <div class="stars"><svg xmlns="http://www.w3.org/2000/svg" width="163"
                                                    height="30" viewBox="0 0 163 30" fill="none">
                                                    <path
                                                        d="M12.6642 6.04253C13.2526 4.17853 15.8904 4.17854 16.4787 6.04254L17.4017 8.96675C17.6644 9.79894 18.4363 10.3647 19.309 10.3647H22.4163C24.3397 10.3647 25.1544 12.8144 23.6142 13.9664L21.0015 15.9204C20.3207 16.4296 20.0362 17.3134 20.2921 18.124L21.2664 21.2107C21.8512 23.0636 19.7172 24.578 18.1613 23.4143L15.7693 21.6254C15.0591 21.0942 14.0838 21.0942 13.3736 21.6254L10.9817 23.4143C9.42574 24.578 7.29174 23.0636 7.87658 21.2107L8.85085 18.124C9.10672 17.3134 8.8222 16.4296 8.14146 15.9204L5.52874 13.9664C3.98849 12.8144 4.80322 10.3647 6.7266 10.3647H9.83398C10.7066 10.3647 11.4786 9.79893 11.7412 8.96674L12.6642 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M46.0939 6.04253C46.6823 4.17853 49.3201 4.17854 49.9084 6.04254L50.8314 8.96675C51.0941 9.79894 51.866 10.3647 52.7386 10.3647H55.846C57.7694 10.3647 58.5841 12.8144 57.0439 13.9664L54.4312 15.9204C53.7504 16.4296 53.4659 17.3134 53.7218 18.124L54.696 21.2107C55.2809 23.0636 53.1469 24.578 51.5909 23.4143L49.199 21.6254C48.4888 21.0942 47.5135 21.0942 46.8033 21.6254L44.4114 23.4143C42.8554 24.578 40.7214 23.0636 41.3063 21.2107L42.2805 18.124C42.5364 17.3134 42.2519 16.4296 41.5711 15.9204L38.9584 13.9664C37.4182 12.8144 38.2329 10.3647 40.1563 10.3647H43.2637C44.1363 10.3647 44.9082 9.79893 45.1709 8.96674L46.0939 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M79.5216 6.04253C80.11 4.17853 82.7478 4.17854 83.3361 6.04254L84.2591 8.96675C84.5218 9.79894 85.2937 10.3647 86.1664 10.3647H89.2738C91.1971 10.3647 92.0119 12.8144 90.4716 13.9664L87.8589 15.9204C87.1782 16.4296 86.8936 17.3134 87.1495 18.124L88.1238 21.2107C88.7086 23.0636 86.5746 24.578 85.0187 23.4143L82.6267 21.6254C81.9165 21.0942 80.9413 21.0942 80.231 21.6254L77.8391 23.4143C76.2832 24.578 74.1492 23.0636 74.734 21.2107L75.7083 18.124C75.9641 17.3134 75.6796 16.4296 74.9989 15.9204L72.3862 13.9664C70.8459 12.8144 71.6606 10.3647 73.584 10.3647H76.6914C77.5641 10.3647 78.336 9.79893 78.5987 8.96674L79.5216 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M112.95 6.04253C113.539 4.17853 116.177 4.17854 116.765 6.04254L117.688 8.96675C117.951 9.79894 118.722 10.3647 119.595 10.3647H122.702C124.626 10.3647 125.441 12.8144 123.9 13.9664L121.288 15.9204C120.607 16.4296 120.322 17.3134 120.578 18.124L121.552 21.2107C122.137 23.0636 120.003 24.578 118.447 23.4143L116.055 21.6254C115.345 21.0942 114.37 21.0942 113.66 21.6254L111.268 23.4143C109.712 24.578 107.578 23.0636 108.163 21.2107L109.137 18.124C109.393 17.3134 109.108 16.4296 108.428 15.9204L105.815 13.9664C104.275 12.8144 105.089 10.3647 107.013 10.3647H110.12C110.993 10.3647 111.765 9.79893 112.027 8.96674L112.95 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M146.378 6.04253C146.966 4.17853 149.604 4.17854 150.193 6.04254L151.116 8.96675C151.378 9.79894 152.15 10.3647 153.023 10.3647H156.13C158.054 10.3647 158.868 12.8144 157.328 13.9664L154.715 15.9204C154.035 16.4296 153.75 17.3134 154.006 18.124L154.98 21.2107C155.565 23.0636 153.431 24.578 151.875 23.4143L149.483 21.6254C148.773 21.0942 147.798 21.0942 147.087 21.6254L144.696 23.4143C143.14 24.578 141.006 23.0636 141.59 21.2107L142.565 18.124C142.821 17.3134 142.536 16.4296 141.855 15.9204L139.243 13.9664C137.702 12.8144 138.517 10.3647 140.44 10.3647H143.548C144.421 10.3647 145.192 9.79893 145.455 8.96674L146.378 6.04253Z"
                                                        fill="#EFEFEF" />
                                                </svg></div>
                                            <span class="date">25/02/2025</span>
                                            <span class="badge">Reviews Written</span>
                                        </div>

                                        <p class="review-text">
                                            I booked this groomer through Fursgo for my anxious little
                                            cockapoo
                                            and honestly couldn’t be happier. They took the time to let him
                                            settle and never rushed him...
                                        </p>

                                        <div class="review-tags">
                                            <span>Home Visit</span>
                                            <span>Mobile Station</span>
                                        </div>

                                    </div>

                                    <div class="review-menu">
                                        ⋯
                                        <div class="menu-dropdown">
                                            <button>Edit ✏</button>
                                            <button>Remove 🗑</button>
                                        </div>
                                    </div>

                                </div>


                                <!-- REVIEW ITEM -->
                                <div class="review-item">

                                    <div class="review-left">
                                        <div class="avatar-wrap">

                                            <img src="{{ asset('images/space_card3.png') }}" alt="Image" class="avatar">

                                            <div class="badge-shield" title="Verified">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="29" height="30"
                                                    viewBox="0 0 29 30" fill="none">
                                                    <path
                                                        d="M14.9293 0.170856C14.6819 0.058916 14.4168 0 14.1399 0C13.8629 0 13.5978 0.058916 13.3504 0.170856L2.25651 4.87824C0.960357 5.42616 -0.0058648 6.70463 2.67979e-05 8.24823C0.0294848 14.0927 2.43326 24.7859 12.5845 29.6465C13.5684 30.1178 14.7113 30.1178 15.6952 29.6465C25.8465 24.7859 28.2502 14.0927 28.2797 8.24823C28.2856 6.70463 27.3194 5.42616 26.0232 4.87824L14.9293 0.170856Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M21.818 8.18213L15.7574 14.6215L21.818 8.18213ZM13.065 14.3071C11.1862 15.0283 9.68391 14.9049 8.18164 14.3094C8.56043 19.1905 10.8362 21.067 13.8703 21.8185C13.8703 21.8185 16.1559 20.2018 16.4854 16.3693C16.521 15.9541 16.5385 15.7473 16.4529 15.5132C16.3665 15.2791 16.1968 15.1117 15.8582 14.7761C15.3006 14.2246 15.0226 13.9488 14.6915 13.8791C14.3604 13.8109 13.9286 13.9761 13.065 14.3071Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M21.818 8.18213L15.7574 14.6215M13.065 14.3071C11.1862 15.0283 9.68391 14.9049 8.18164 14.3094C8.56043 19.1905 10.8362 21.067 13.8703 21.8185C13.8703 21.8185 16.1559 20.2018 16.4854 16.3693C16.521 15.9541 16.5385 15.7473 16.4529 15.5132C16.3665 15.2791 16.1968 15.1117 15.8582 14.7761C15.3006 14.2246 15.0225 13.9488 14.6915 13.8791C14.3604 13.8109 13.9286 13.9761 13.065 14.3071Z"
                                                        stroke="white" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path
                                                        d="M9.31836 18.3679C9.31836 18.3679 11.2123 18.7346 13.1062 17.2725L9.31836 18.3679Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M9.31836 18.3679C9.31836 18.3679 11.2123 18.7346 13.1062 17.2725"
                                                        stroke="white" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path
                                                        d="M12.3485 11.4016C12.3485 11.6527 12.2488 11.8936 12.0712 12.0712C11.8936 12.2488 11.6527 12.3485 11.4016 12.3485C11.1504 12.3485 10.9095 12.2488 10.732 12.0712C10.5544 11.8936 10.4546 11.6527 10.4546 11.4016C10.4546 11.1504 10.5544 10.9095 10.732 10.732C10.9095 10.5544 11.1504 10.4546 11.4016 10.4546C11.6527 10.4546 11.8936 10.5544 12.0712 10.732C12.2488 10.9095 12.3485 11.1504 12.3485 11.4016Z"
                                                        fill="#CBDCE8" stroke="white"></path>
                                                    <path d="M14.2432 8.93896V9.01472V8.93896Z" fill="#CBDCE8">
                                                    </path>
                                                    <path d="M14.2432 8.93896V9.01472" stroke="white"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="review-user">
                                            <h4>Paws & Bubbles</h4>
                                            <p>Hosted by Patrick B.</p>

                                            <div class="reviews-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                    viewBox="0 0 82 24" fill="none">
                                                    <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                    <path
                                                        d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                        fill="white" />
                                                    <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                        fill="#FEFEFE" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                        fill="#FEFEFE" />
                                                    <rect x="58" width="24" height="24" rx="12" fill="#D8E8B7" />
                                                    <path
                                                        d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                        fill="white" />
                                                </svg>
                                            </div>
                                            <div class="review-meta">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-content">

                                        <div class="review-top">
                                            <div class="stars"><svg xmlns="http://www.w3.org/2000/svg" width="163"
                                                    height="30" viewBox="0 0 163 30" fill="none">
                                                    <path
                                                        d="M12.6642 6.04253C13.2526 4.17853 15.8904 4.17854 16.4787 6.04254L17.4017 8.96675C17.6644 9.79894 18.4363 10.3647 19.309 10.3647H22.4163C24.3397 10.3647 25.1544 12.8144 23.6142 13.9664L21.0015 15.9204C20.3207 16.4296 20.0362 17.3134 20.2921 18.124L21.2664 21.2107C21.8512 23.0636 19.7172 24.578 18.1613 23.4143L15.7693 21.6254C15.0591 21.0942 14.0838 21.0942 13.3736 21.6254L10.9817 23.4143C9.42574 24.578 7.29174 23.0636 7.87658 21.2107L8.85085 18.124C9.10672 17.3134 8.8222 16.4296 8.14146 15.9204L5.52874 13.9664C3.98849 12.8144 4.80322 10.3647 6.7266 10.3647H9.83398C10.7066 10.3647 11.4786 9.79893 11.7412 8.96674L12.6642 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M46.0939 6.04253C46.6823 4.17853 49.3201 4.17854 49.9084 6.04254L50.8314 8.96675C51.0941 9.79894 51.866 10.3647 52.7386 10.3647H55.846C57.7694 10.3647 58.5841 12.8144 57.0439 13.9664L54.4312 15.9204C53.7504 16.4296 53.4659 17.3134 53.7218 18.124L54.696 21.2107C55.2809 23.0636 53.1469 24.578 51.5909 23.4143L49.199 21.6254C48.4888 21.0942 47.5135 21.0942 46.8033 21.6254L44.4114 23.4143C42.8554 24.578 40.7214 23.0636 41.3063 21.2107L42.2805 18.124C42.5364 17.3134 42.2519 16.4296 41.5711 15.9204L38.9584 13.9664C37.4182 12.8144 38.2329 10.3647 40.1563 10.3647H43.2637C44.1363 10.3647 44.9082 9.79893 45.1709 8.96674L46.0939 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M79.5216 6.04253C80.11 4.17853 82.7478 4.17854 83.3361 6.04254L84.2591 8.96675C84.5218 9.79894 85.2937 10.3647 86.1664 10.3647H89.2738C91.1971 10.3647 92.0119 12.8144 90.4716 13.9664L87.8589 15.9204C87.1782 16.4296 86.8936 17.3134 87.1495 18.124L88.1238 21.2107C88.7086 23.0636 86.5746 24.578 85.0187 23.4143L82.6267 21.6254C81.9165 21.0942 80.9413 21.0942 80.231 21.6254L77.8391 23.4143C76.2832 24.578 74.1492 23.0636 74.734 21.2107L75.7083 18.124C75.9641 17.3134 75.6796 16.4296 74.9989 15.9204L72.3862 13.9664C70.8459 12.8144 71.6606 10.3647 73.584 10.3647H76.6914C77.5641 10.3647 78.336 9.79893 78.5987 8.96674L79.5216 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M112.95 6.04253C113.539 4.17853 116.177 4.17854 116.765 6.04254L117.688 8.96675C117.951 9.79894 118.722 10.3647 119.595 10.3647H122.702C124.626 10.3647 125.441 12.8144 123.9 13.9664L121.288 15.9204C120.607 16.4296 120.322 17.3134 120.578 18.124L121.552 21.2107C122.137 23.0636 120.003 24.578 118.447 23.4143L116.055 21.6254C115.345 21.0942 114.37 21.0942 113.66 21.6254L111.268 23.4143C109.712 24.578 107.578 23.0636 108.163 21.2107L109.137 18.124C109.393 17.3134 109.108 16.4296 108.428 15.9204L105.815 13.9664C104.275 12.8144 105.089 10.3647 107.013 10.3647H110.12C110.993 10.3647 111.765 9.79893 112.027 8.96674L112.95 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M146.378 6.04253C146.966 4.17853 149.604 4.17854 150.193 6.04254L151.116 8.96675C151.378 9.79894 152.15 10.3647 153.023 10.3647H156.13C158.054 10.3647 158.868 12.8144 157.328 13.9664L154.715 15.9204C154.035 16.4296 153.75 17.3134 154.006 18.124L154.98 21.2107C155.565 23.0636 153.431 24.578 151.875 23.4143L149.483 21.6254C148.773 21.0942 147.798 21.0942 147.087 21.6254L144.696 23.4143C143.14 24.578 141.006 23.0636 141.59 21.2107L142.565 18.124C142.821 17.3134 142.536 16.4296 141.855 15.9204L139.243 13.9664C137.702 12.8144 138.517 10.3647 140.44 10.3647H143.548C144.421 10.3647 145.192 9.79893 145.455 8.96674L146.378 6.04253Z"
                                                        fill="#EFEFEF" />
                                                </svg></div>
                                            <span class="date">25/02/2025</span>
                                            <span class="badge">Reviews Written</span>
                                        </div>

                                        <p class="review-text">
                                            This grooming space was perfect — bright, clean, and clearly
                                            designed with pets in mind. Everything felt organised and
                                            hygienic.
                                        </p>

                                        <div class="review-tags">
                                            <span>Garden / Shed</span>
                                        </div>

                                    </div>

                                    <div class="review-menu">
                                        ⋯
                                        <div class="menu-dropdown">
                                            <button>Edit ✏</button>
                                            <button>Remove 🗑</button>
                                        </div>
                                    </div>

                                </div>

                                <div class="review-item">

                                    <div class="review-left">
                                        <div class="avatar-wrap">

                                            <img src="{{ asset('images/space_card3.png') }}" alt="Image" class="avatar">

                                            <div class="badge-shield" title="Verified">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="29" height="30"
                                                    viewBox="0 0 29 30" fill="none">
                                                    <path
                                                        d="M14.9293 0.170856C14.6819 0.058916 14.4168 0 14.1399 0C13.8629 0 13.5978 0.058916 13.3504 0.170856L2.25651 4.87824C0.960357 5.42616 -0.0058648 6.70463 2.67979e-05 8.24823C0.0294848 14.0927 2.43326 24.7859 12.5845 29.6465C13.5684 30.1178 14.7113 30.1178 15.6952 29.6465C25.8465 24.7859 28.2502 14.0927 28.2797 8.24823C28.2856 6.70463 27.3194 5.42616 26.0232 4.87824L14.9293 0.170856Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M21.818 8.18213L15.7574 14.6215L21.818 8.18213ZM13.065 14.3071C11.1862 15.0283 9.68391 14.9049 8.18164 14.3094C8.56043 19.1905 10.8362 21.067 13.8703 21.8185C13.8703 21.8185 16.1559 20.2018 16.4854 16.3693C16.521 15.9541 16.5385 15.7473 16.4529 15.5132C16.3665 15.2791 16.1968 15.1117 15.8582 14.7761C15.3006 14.2246 15.0226 13.9488 14.6915 13.8791C14.3604 13.8109 13.9286 13.9761 13.065 14.3071Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M21.818 8.18213L15.7574 14.6215M13.065 14.3071C11.1862 15.0283 9.68391 14.9049 8.18164 14.3094C8.56043 19.1905 10.8362 21.067 13.8703 21.8185C13.8703 21.8185 16.1559 20.2018 16.4854 16.3693C16.521 15.9541 16.5385 15.7473 16.4529 15.5132C16.3665 15.2791 16.1968 15.1117 15.8582 14.7761C15.3006 14.2246 15.0225 13.9488 14.6915 13.8791C14.3604 13.8109 13.9286 13.9761 13.065 14.3071Z"
                                                        stroke="white" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path
                                                        d="M9.31836 18.3679C9.31836 18.3679 11.2123 18.7346 13.1062 17.2725L9.31836 18.3679Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M9.31836 18.3679C9.31836 18.3679 11.2123 18.7346 13.1062 17.2725"
                                                        stroke="white" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path
                                                        d="M12.3485 11.4016C12.3485 11.6527 12.2488 11.8936 12.0712 12.0712C11.8936 12.2488 11.6527 12.3485 11.4016 12.3485C11.1504 12.3485 10.9095 12.2488 10.732 12.0712C10.5544 11.8936 10.4546 11.6527 10.4546 11.4016C10.4546 11.1504 10.5544 10.9095 10.732 10.732C10.9095 10.5544 11.1504 10.4546 11.4016 10.4546C11.6527 10.4546 11.8936 10.5544 12.0712 10.732C12.2488 10.9095 12.3485 11.1504 12.3485 11.4016Z"
                                                        fill="#CBDCE8" stroke="white"></path>
                                                    <path d="M14.2432 8.93896V9.01472V8.93896Z" fill="#CBDCE8">
                                                    </path>
                                                    <path d="M14.2432 8.93896V9.01472" stroke="white"
                                                        stroke-linecap="round" stroke-linejoin="round">
                                                    </path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="review-user">
                                            <h4>Paws & Bubbles</h4>
                                            <p>Hosted by Patrick B.</p>
                                            <div class="reviews-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                    viewBox="0 0 82 24" fill="none">
                                                    <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                    <path
                                                        d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                        fill="white" />
                                                    <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                        fill="#FEFEFE" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                        fill="#FEFEFE" />
                                                    <rect x="58" width="24" height="24" rx="12" fill="#D8E8B7" />
                                                    <path
                                                        d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                        fill="white" />
                                                </svg>
                                            </div>
                                            <div class="review-meta">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-content">

                                        <div class="review-top">
                                            <div class="stars"><svg xmlns="http://www.w3.org/2000/svg" width="163"
                                                    height="30" viewBox="0 0 163 30" fill="none">
                                                    <path
                                                        d="M12.6642 6.04253C13.2526 4.17853 15.8904 4.17854 16.4787 6.04254L17.4017 8.96675C17.6644 9.79894 18.4363 10.3647 19.309 10.3647H22.4163C24.3397 10.3647 25.1544 12.8144 23.6142 13.9664L21.0015 15.9204C20.3207 16.4296 20.0362 17.3134 20.2921 18.124L21.2664 21.2107C21.8512 23.0636 19.7172 24.578 18.1613 23.4143L15.7693 21.6254C15.0591 21.0942 14.0838 21.0942 13.3736 21.6254L10.9817 23.4143C9.42574 24.578 7.29174 23.0636 7.87658 21.2107L8.85085 18.124C9.10672 17.3134 8.8222 16.4296 8.14146 15.9204L5.52874 13.9664C3.98849 12.8144 4.80322 10.3647 6.7266 10.3647H9.83398C10.7066 10.3647 11.4786 9.79893 11.7412 8.96674L12.6642 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M46.0939 6.04253C46.6823 4.17853 49.3201 4.17854 49.9084 6.04254L50.8314 8.96675C51.0941 9.79894 51.866 10.3647 52.7386 10.3647H55.846C57.7694 10.3647 58.5841 12.8144 57.0439 13.9664L54.4312 15.9204C53.7504 16.4296 53.4659 17.3134 53.7218 18.124L54.696 21.2107C55.2809 23.0636 53.1469 24.578 51.5909 23.4143L49.199 21.6254C48.4888 21.0942 47.5135 21.0942 46.8033 21.6254L44.4114 23.4143C42.8554 24.578 40.7214 23.0636 41.3063 21.2107L42.2805 18.124C42.5364 17.3134 42.2519 16.4296 41.5711 15.9204L38.9584 13.9664C37.4182 12.8144 38.2329 10.3647 40.1563 10.3647H43.2637C44.1363 10.3647 44.9082 9.79893 45.1709 8.96674L46.0939 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M79.5216 6.04253C80.11 4.17853 82.7478 4.17854 83.3361 6.04254L84.2591 8.96675C84.5218 9.79894 85.2937 10.3647 86.1664 10.3647H89.2738C91.1971 10.3647 92.0119 12.8144 90.4716 13.9664L87.8589 15.9204C87.1782 16.4296 86.8936 17.3134 87.1495 18.124L88.1238 21.2107C88.7086 23.0636 86.5746 24.578 85.0187 23.4143L82.6267 21.6254C81.9165 21.0942 80.9413 21.0942 80.231 21.6254L77.8391 23.4143C76.2832 24.578 74.1492 23.0636 74.734 21.2107L75.7083 18.124C75.9641 17.3134 75.6796 16.4296 74.9989 15.9204L72.3862 13.9664C70.8459 12.8144 71.6606 10.3647 73.584 10.3647H76.6914C77.5641 10.3647 78.336 9.79893 78.5987 8.96674L79.5216 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M112.95 6.04253C113.539 4.17853 116.177 4.17854 116.765 6.04254L117.688 8.96675C117.951 9.79894 118.722 10.3647 119.595 10.3647H122.702C124.626 10.3647 125.441 12.8144 123.9 13.9664L121.288 15.9204C120.607 16.4296 120.322 17.3134 120.578 18.124L121.552 21.2107C122.137 23.0636 120.003 24.578 118.447 23.4143L116.055 21.6254C115.345 21.0942 114.37 21.0942 113.66 21.6254L111.268 23.4143C109.712 24.578 107.578 23.0636 108.163 21.2107L109.137 18.124C109.393 17.3134 109.108 16.4296 108.428 15.9204L105.815 13.9664C104.275 12.8144 105.089 10.3647 107.013 10.3647H110.12C110.993 10.3647 111.765 9.79893 112.027 8.96674L112.95 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M146.378 6.04253C146.966 4.17853 149.604 4.17854 150.193 6.04254L151.116 8.96675C151.378 9.79894 152.15 10.3647 153.023 10.3647H156.13C158.054 10.3647 158.868 12.8144 157.328 13.9664L154.715 15.9204C154.035 16.4296 153.75 17.3134 154.006 18.124L154.98 21.2107C155.565 23.0636 153.431 24.578 151.875 23.4143L149.483 21.6254C148.773 21.0942 147.798 21.0942 147.087 21.6254L144.696 23.4143C143.14 24.578 141.006 23.0636 141.59 21.2107L142.565 18.124C142.821 17.3134 142.536 16.4296 141.855 15.9204L139.243 13.9664C137.702 12.8144 138.517 10.3647 140.44 10.3647H143.548C144.421 10.3647 145.192 9.79893 145.455 8.96674L146.378 6.04253Z"
                                                        fill="#EFEFEF" />
                                                </svg></div>
                                            <span class="date">25/02/2025</span>
                                            <span class="badge">Reviews Written</span>
                                        </div>

                                        <p class="review-text">
                                            Such a nice grooming environment. The space was tidy, well
                                            ventilated,
                                            and had all
                                            the
                                            equipment needed without feeling overwhelming. It felt very
                                            calm and
                                            welcoming,
                                            which
                                            made
                                            drop-off...
                                        </p>

                                        <div class="review-tags">
                                            <span>Private Room</span>
                                        </div>

                                    </div>


                                    <div class="review-menu">
                                        ⋯
                                        <div class="menu-dropdown">
                                            <button>Edit ✏</button>
                                            <button>Remove 🗑</button>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>

                        <div id="received-reviews" class="review-group">

                            <div class="reviews-list">

                                <!-- REVIEW ITEM -->
                                <div class="review-item">

                                    <div class="review-left">

                                        <div class="avatar-wrap">

                                            <img src="{{ asset('images/groomer-profile.png') }}" alt="Image" class="avatar">

                                            <div class="badge-shield" title="Verified">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="33"
                                                    viewBox="0 0 30 33" fill="none">
                                                    <ellipse cx="15.873" cy="16.5256" rx="9.3645" ry="8.81365"
                                                        fill="white"></ellipse>
                                                    <path
                                                        d="M15.44 0.185076C15.1841 0.0638192 14.9099 0 14.6235 0C14.3372 0 14.063 0.0638192 13.8071 0.185076L2.3337 5.28423C0.993208 5.87775 -0.00606541 7.26263 2.77146e-05 8.93469C0.0304934 15.2656 2.51649 26.8487 13.015 32.1138C14.0325 32.6244 15.2146 32.6244 16.2321 32.1138C26.7306 26.8487 29.2166 15.2656 29.247 8.93469C29.2531 7.26263 28.2539 5.87775 26.9134 5.28423L15.44 0.185076ZM8.82897 18.2651C9.12144 18.3416 9.43219 18.3799 9.74903 18.3799C11.8999 18.3799 13.6486 16.5483 13.6486 14.2955V10.2111H16.3418C17.0791 10.2111 17.7554 10.645 18.0844 11.3407L18.5231 12.2533H22.4227C22.9589 12.2533 23.3976 12.7128 23.3976 13.2744V15.3166C23.3976 18.1374 21.2163 20.4222 18.5231 20.4222H15.5984V23.6578C15.5984 24.1237 15.2389 24.5066 14.7881 24.5066C14.6784 24.5066 14.5687 24.4811 14.4712 24.4364L8.45729 21.7368C8.05514 21.5581 7.79923 21.1433 7.79923 20.6902C7.79923 20.5115 7.83579 20.3392 7.915 20.1796L8.82897 18.2651ZM8.77413 10.2111H11.6988V14.2955C11.6988 15.4251 10.8275 16.3377 9.74903 16.3377C8.67055 16.3377 7.79923 15.4251 7.79923 14.2955V11.2322C7.79923 10.6706 8.23794 10.2111 8.77413 10.2111ZM16.5733 13.2744C16.5733 13.0036 16.4706 12.7439 16.2878 12.5524C16.105 12.3609 15.857 12.2533 15.5984 12.2533C15.3399 12.2533 15.0919 12.3609 14.9091 12.5524C14.7262 12.7439 14.6235 13.0036 14.6235 13.2744C14.6235 13.5452 14.7262 13.8049 14.9091 13.9964C15.0919 14.1879 15.3399 14.2955 15.5984 14.2955C15.857 14.2955 16.105 14.1879 16.2878 13.9964C16.4706 13.8049 16.5733 13.5452 16.5733 13.2744Z"
                                                        fill="#C9DDA0"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="review-user">
                                            <h4>Cathy P.</h4>
                                            <p>Sarah's Grooming Studio</p>

                                            <div class="reviews-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="53" height="24"
                                                    viewBox="0 0 53 24" fill="none">
                                                    <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                    <path
                                                        d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                        fill="white" />
                                                    <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M43.4127 14.892C43.4417 14.3426 43.3153 13.7961 43.0481 13.3151C42.7809 12.8341 42.3837 12.4381 41.9019 12.1723C41.8164 12.1229 41.715 12.1085 41.619 12.1323C41.5231 12.1562 41.4402 12.2163 41.3877 12.3001L40.775 13.2808L40.2653 12.6643C40.2373 12.6305 40.2026 12.6029 40.1634 12.5831C40.1242 12.5634 40.0813 12.552 40.0375 12.5497C39.9936 12.5474 39.9498 12.5542 39.9087 12.5696C39.8676 12.5851 39.8302 12.6089 39.7988 12.6395C39.4987 12.9299 39.2643 13.2811 39.1114 13.6697C38.9584 14.0582 38.8905 14.475 38.9121 14.892C38.9121 15.4888 39.1492 16.0611 39.5711 16.483C39.9931 16.905 40.5654 17.142 41.1621 17.142C41.7588 17.142 42.3311 16.905 42.7531 16.483C43.175 16.0611 43.4121 15.4888 43.4121 14.892M40.0098 13.3539L39.9704 13.406C39.6715 13.8236 39.5217 14.3295 39.5452 14.8425L39.5471 14.88C39.5471 15.3081 39.7172 15.7187 40.0199 16.0215C40.3227 16.3243 40.7333 16.4943 41.1615 16.4943C41.5896 16.4943 42.0003 16.3243 42.303 16.0215C42.6058 15.7187 42.7759 15.3081 42.7759 14.88L42.7778 14.8431C42.7822 14.8018 42.8814 13.6653 41.8416 12.8925L41.7901 12.855L41.1265 13.9164C41.0945 13.9676 41.0507 14.0104 40.9989 14.0414C40.9471 14.0724 40.8886 14.0906 40.8283 14.0946C40.7681 14.0986 40.7077 14.0882 40.6523 14.0643C40.5968 14.0404 40.5478 14.0036 40.5094 13.9571L40.0098 13.3539Z"
                                                        fill="#FEFEFE" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                        fill="#FEFEFE" />
                                                </svg>
                                            </div>

                                            <div class="review-meta">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-content">

                                        <div class="review-top">
                                            <div class="stars"><svg xmlns="http://www.w3.org/2000/svg" width="163"
                                                    height="30" viewBox="0 0 163 30" fill="none">
                                                    <path
                                                        d="M12.6642 6.04253C13.2526 4.17853 15.8904 4.17854 16.4787 6.04254L17.4017 8.96675C17.6644 9.79894 18.4363 10.3647 19.309 10.3647H22.4163C24.3397 10.3647 25.1544 12.8144 23.6142 13.9664L21.0015 15.9204C20.3207 16.4296 20.0362 17.3134 20.2921 18.124L21.2664 21.2107C21.8512 23.0636 19.7172 24.578 18.1613 23.4143L15.7693 21.6254C15.0591 21.0942 14.0838 21.0942 13.3736 21.6254L10.9817 23.4143C9.42574 24.578 7.29174 23.0636 7.87658 21.2107L8.85085 18.124C9.10672 17.3134 8.8222 16.4296 8.14146 15.9204L5.52874 13.9664C3.98849 12.8144 4.80322 10.3647 6.7266 10.3647H9.83398C10.7066 10.3647 11.4786 9.79893 11.7412 8.96674L12.6642 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M46.0939 6.04253C46.6823 4.17853 49.3201 4.17854 49.9084 6.04254L50.8314 8.96675C51.0941 9.79894 51.866 10.3647 52.7386 10.3647H55.846C57.7694 10.3647 58.5841 12.8144 57.0439 13.9664L54.4312 15.9204C53.7504 16.4296 53.4659 17.3134 53.7218 18.124L54.696 21.2107C55.2809 23.0636 53.1469 24.578 51.5909 23.4143L49.199 21.6254C48.4888 21.0942 47.5135 21.0942 46.8033 21.6254L44.4114 23.4143C42.8554 24.578 40.7214 23.0636 41.3063 21.2107L42.2805 18.124C42.5364 17.3134 42.2519 16.4296 41.5711 15.9204L38.9584 13.9664C37.4182 12.8144 38.2329 10.3647 40.1563 10.3647H43.2637C44.1363 10.3647 44.9082 9.79893 45.1709 8.96674L46.0939 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M79.5216 6.04253C80.11 4.17853 82.7478 4.17854 83.3361 6.04254L84.2591 8.96675C84.5218 9.79894 85.2937 10.3647 86.1664 10.3647H89.2738C91.1971 10.3647 92.0119 12.8144 90.4716 13.9664L87.8589 15.9204C87.1782 16.4296 86.8936 17.3134 87.1495 18.124L88.1238 21.2107C88.7086 23.0636 86.5746 24.578 85.0187 23.4143L82.6267 21.6254C81.9165 21.0942 80.9413 21.0942 80.231 21.6254L77.8391 23.4143C76.2832 24.578 74.1492 23.0636 74.734 21.2107L75.7083 18.124C75.9641 17.3134 75.6796 16.4296 74.9989 15.9204L72.3862 13.9664C70.8459 12.8144 71.6606 10.3647 73.584 10.3647H76.6914C77.5641 10.3647 78.336 9.79893 78.5987 8.96674L79.5216 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M112.95 6.04253C113.539 4.17853 116.177 4.17854 116.765 6.04254L117.688 8.96675C117.951 9.79894 118.722 10.3647 119.595 10.3647H122.702C124.626 10.3647 125.441 12.8144 123.9 13.9664L121.288 15.9204C120.607 16.4296 120.322 17.3134 120.578 18.124L121.552 21.2107C122.137 23.0636 120.003 24.578 118.447 23.4143L116.055 21.6254C115.345 21.0942 114.37 21.0942 113.66 21.6254L111.268 23.4143C109.712 24.578 107.578 23.0636 108.163 21.2107L109.137 18.124C109.393 17.3134 109.108 16.4296 108.428 15.9204L105.815 13.9664C104.275 12.8144 105.089 10.3647 107.013 10.3647H110.12C110.993 10.3647 111.765 9.79893 112.027 8.96674L112.95 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M146.378 6.04253C146.966 4.17853 149.604 4.17854 150.193 6.04254L151.116 8.96675C151.378 9.79894 152.15 10.3647 153.023 10.3647H156.13C158.054 10.3647 158.868 12.8144 157.328 13.9664L154.715 15.9204C154.035 16.4296 153.75 17.3134 154.006 18.124L154.98 21.2107C155.565 23.0636 153.431 24.578 151.875 23.4143L149.483 21.6254C148.773 21.0942 147.798 21.0942 147.087 21.6254L144.696 23.4143C143.14 24.578 141.006 23.0636 141.59 21.2107L142.565 18.124C142.821 17.3134 142.536 16.4296 141.855 15.9204L139.243 13.9664C137.702 12.8144 138.517 10.3647 140.44 10.3647H143.548C144.421 10.3647 145.192 9.79893 145.455 8.96674L146.378 6.04253Z"
                                                        fill="#EFEFEF" />
                                                </svg></div>
                                            <span class="date">25/02/2025</span>
                                            <span class="badge">Reviews Written</span>
                                        </div>

                                        <p class="review-text">
                                            Clear communication and Bella was well prepared for the
                                            appointment.
                                        </p>

                                        <div class="review-tags">
                                            <span>Home Visit</span>
                                            <span>Mobile Station</span>
                                        </div>

                                    </div>

                                    <div class="review-menu">
                                        ⋯
                                        <div class="menu-dropdown">
                                            <button>Edit ✏</button>
                                            <button>Remove 🗑</button>
                                        </div>
                                    </div>

                                </div>


                                <!-- REVIEW ITEM -->
                                <div class="review-item">

                                    <div class="review-left">
                                        <div class="avatar-wrap">

                                            <img src="{{ asset('images/space_card3.png') }}" alt="Image" class="avatar">

                                            <div class="badge-shield" title="Verified">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="29" height="30"
                                                    viewBox="0 0 29 30" fill="none">
                                                    <path
                                                        d="M14.9293 0.170856C14.6819 0.058916 14.4168 0 14.1399 0C13.8629 0 13.5978 0.058916 13.3504 0.170856L2.25651 4.87824C0.960357 5.42616 -0.0058648 6.70463 2.67979e-05 8.24823C0.0294848 14.0927 2.43326 24.7859 12.5845 29.6465C13.5684 30.1178 14.7113 30.1178 15.6952 29.6465C25.8465 24.7859 28.2502 14.0927 28.2797 8.24823C28.2856 6.70463 27.3194 5.42616 26.0232 4.87824L14.9293 0.170856Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M21.818 8.18213L15.7574 14.6215L21.818 8.18213ZM13.065 14.3071C11.1862 15.0283 9.68391 14.9049 8.18164 14.3094C8.56043 19.1905 10.8362 21.067 13.8703 21.8185C13.8703 21.8185 16.1559 20.2018 16.4854 16.3693C16.521 15.9541 16.5385 15.7473 16.4529 15.5132C16.3665 15.2791 16.1968 15.1117 15.8582 14.7761C15.3006 14.2246 15.0226 13.9488 14.6915 13.8791C14.3604 13.8109 13.9286 13.9761 13.065 14.3071Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M21.818 8.18213L15.7574 14.6215M13.065 14.3071C11.1862 15.0283 9.68391 14.9049 8.18164 14.3094C8.56043 19.1905 10.8362 21.067 13.8703 21.8185C13.8703 21.8185 16.1559 20.2018 16.4854 16.3693C16.521 15.9541 16.5385 15.7473 16.4529 15.5132C16.3665 15.2791 16.1968 15.1117 15.8582 14.7761C15.3006 14.2246 15.0225 13.9488 14.6915 13.8791C14.3604 13.8109 13.9286 13.9761 13.065 14.3071Z"
                                                        stroke="white" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path
                                                        d="M9.31836 18.3679C9.31836 18.3679 11.2123 18.7346 13.1062 17.2725L9.31836 18.3679Z"
                                                        fill="#CBDCE8"></path>
                                                    <path
                                                        d="M9.31836 18.3679C9.31836 18.3679 11.2123 18.7346 13.1062 17.2725"
                                                        stroke="white" stroke-linecap="round"
                                                        stroke-linejoin="round"></path>
                                                    <path
                                                        d="M12.3485 11.4016C12.3485 11.6527 12.2488 11.8936 12.0712 12.0712C11.8936 12.2488 11.6527 12.3485 11.4016 12.3485C11.1504 12.3485 10.9095 12.2488 10.732 12.0712C10.5544 11.8936 10.4546 11.6527 10.4546 11.4016C10.4546 11.1504 10.5544 10.9095 10.732 10.732C10.9095 10.5544 11.1504 10.4546 11.4016 10.4546C11.6527 10.4546 11.8936 10.5544 12.0712 10.732C12.2488 10.9095 12.3485 11.1504 12.3485 11.4016Z"
                                                        fill="#CBDCE8" stroke="white"></path>
                                                    <path d="M14.2432 8.93896V9.01472V8.93896Z" fill="#CBDCE8">
                                                    </path>
                                                    <path d="M14.2432 8.93896V9.01472" stroke="white"
                                                        stroke-linecap="round" stroke-linejoin="round"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="review-user">
                                            <h4>Paws & Bubbles</h4>
                                            <p>Hosted by Patrick B.</p>

                                            <div class="reviews-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                    viewBox="0 0 82 24" fill="none">
                                                    <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                    <path
                                                        d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                        fill="white" />
                                                    <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                        fill="#FEFEFE" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                        fill="#FEFEFE" />
                                                    <rect x="58" width="24" height="24" rx="12" fill="#D8E8B7" />
                                                    <path
                                                        d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                        fill="white" />
                                                </svg>
                                            </div>
                                            <div class="review-meta">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-content">

                                        <div class="review-top">
                                            <div class="stars"><svg xmlns="http://www.w3.org/2000/svg" width="163"
                                                    height="30" viewBox="0 0 163 30" fill="none">
                                                    <path
                                                        d="M12.6642 6.04253C13.2526 4.17853 15.8904 4.17854 16.4787 6.04254L17.4017 8.96675C17.6644 9.79894 18.4363 10.3647 19.309 10.3647H22.4163C24.3397 10.3647 25.1544 12.8144 23.6142 13.9664L21.0015 15.9204C20.3207 16.4296 20.0362 17.3134 20.2921 18.124L21.2664 21.2107C21.8512 23.0636 19.7172 24.578 18.1613 23.4143L15.7693 21.6254C15.0591 21.0942 14.0838 21.0942 13.3736 21.6254L10.9817 23.4143C9.42574 24.578 7.29174 23.0636 7.87658 21.2107L8.85085 18.124C9.10672 17.3134 8.8222 16.4296 8.14146 15.9204L5.52874 13.9664C3.98849 12.8144 4.80322 10.3647 6.7266 10.3647H9.83398C10.7066 10.3647 11.4786 9.79893 11.7412 8.96674L12.6642 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M46.0939 6.04253C46.6823 4.17853 49.3201 4.17854 49.9084 6.04254L50.8314 8.96675C51.0941 9.79894 51.866 10.3647 52.7386 10.3647H55.846C57.7694 10.3647 58.5841 12.8144 57.0439 13.9664L54.4312 15.9204C53.7504 16.4296 53.4659 17.3134 53.7218 18.124L54.696 21.2107C55.2809 23.0636 53.1469 24.578 51.5909 23.4143L49.199 21.6254C48.4888 21.0942 47.5135 21.0942 46.8033 21.6254L44.4114 23.4143C42.8554 24.578 40.7214 23.0636 41.3063 21.2107L42.2805 18.124C42.5364 17.3134 42.2519 16.4296 41.5711 15.9204L38.9584 13.9664C37.4182 12.8144 38.2329 10.3647 40.1563 10.3647H43.2637C44.1363 10.3647 44.9082 9.79893 45.1709 8.96674L46.0939 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M79.5216 6.04253C80.11 4.17853 82.7478 4.17854 83.3361 6.04254L84.2591 8.96675C84.5218 9.79894 85.2937 10.3647 86.1664 10.3647H89.2738C91.1971 10.3647 92.0119 12.8144 90.4716 13.9664L87.8589 15.9204C87.1782 16.4296 86.8936 17.3134 87.1495 18.124L88.1238 21.2107C88.7086 23.0636 86.5746 24.578 85.0187 23.4143L82.6267 21.6254C81.9165 21.0942 80.9413 21.0942 80.231 21.6254L77.8391 23.4143C76.2832 24.578 74.1492 23.0636 74.734 21.2107L75.7083 18.124C75.9641 17.3134 75.6796 16.4296 74.9989 15.9204L72.3862 13.9664C70.8459 12.8144 71.6606 10.3647 73.584 10.3647H76.6914C77.5641 10.3647 78.336 9.79893 78.5987 8.96674L79.5216 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M112.95 6.04253C113.539 4.17853 116.177 4.17854 116.765 6.04254L117.688 8.96675C117.951 9.79894 118.722 10.3647 119.595 10.3647H122.702C124.626 10.3647 125.441 12.8144 123.9 13.9664L121.288 15.9204C120.607 16.4296 120.322 17.3134 120.578 18.124L121.552 21.2107C122.137 23.0636 120.003 24.578 118.447 23.4143L116.055 21.6254C115.345 21.0942 114.37 21.0942 113.66 21.6254L111.268 23.4143C109.712 24.578 107.578 23.0636 108.163 21.2107L109.137 18.124C109.393 17.3134 109.108 16.4296 108.428 15.9204L105.815 13.9664C104.275 12.8144 105.089 10.3647 107.013 10.3647H110.12C110.993 10.3647 111.765 9.79893 112.027 8.96674L112.95 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M146.378 6.04253C146.966 4.17853 149.604 4.17854 150.193 6.04254L151.116 8.96675C151.378 9.79894 152.15 10.3647 153.023 10.3647H156.13C158.054 10.3647 158.868 12.8144 157.328 13.9664L154.715 15.9204C154.035 16.4296 153.75 17.3134 154.006 18.124L154.98 21.2107C155.565 23.0636 153.431 24.578 151.875 23.4143L149.483 21.6254C148.773 21.0942 147.798 21.0942 147.087 21.6254L144.696 23.4143C143.14 24.578 141.006 23.0636 141.59 21.2107L142.565 18.124C142.821 17.3134 142.536 16.4296 141.855 15.9204L139.243 13.9664C137.702 12.8144 138.517 10.3647 140.44 10.3647H143.548C144.421 10.3647 145.192 9.79893 145.455 8.96674L146.378 6.04253Z"
                                                        fill="#EFEFEF" />
                                                </svg></div>
                                            <span class="date">25/02/2025</span>
                                            <span class="badge">Reviews Written</span>
                                        </div>

                                        <p class="review-text">
                                            Really pleasant booking overall. Easy access, good
                                            communication,
                                            and
                                            the Pepper was
                                            well prepared. Would be happy to work together again.
                                        </p>

                                        <div class="review-tags">
                                            <span>Garden / Shed</span>
                                        </div>

                                    </div>

                                    <div class="review-menu">
                                        ⋯
                                        <div class="menu-dropdown">
                                            <button>Edit ✏</button>
                                            <button>Remove 🗑</button>
                                        </div>
                                    </div>

                                </div>

                                <div class="review-item">

                                    <div class="review-left">
                                        <div class="avatar-wrap">

                                            <img src="{{ asset('images/groomer-profile.png') }}" alt="Image" class="avatar">

                                            <div class="badge-shield" title="Verified">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="30" height="33"
                                                    viewBox="0 0 30 33" fill="none">
                                                    <ellipse cx="15.873" cy="16.5256" rx="9.3645" ry="8.81365"
                                                        fill="white"></ellipse>
                                                    <path
                                                        d="M15.44 0.185076C15.1841 0.0638192 14.9099 0 14.6235 0C14.3372 0 14.063 0.0638192 13.8071 0.185076L2.3337 5.28423C0.993208 5.87775 -0.00606541 7.26263 2.77146e-05 8.93469C0.0304934 15.2656 2.51649 26.8487 13.015 32.1138C14.0325 32.6244 15.2146 32.6244 16.2321 32.1138C26.7306 26.8487 29.2166 15.2656 29.247 8.93469C29.2531 7.26263 28.2539 5.87775 26.9134 5.28423L15.44 0.185076ZM8.82897 18.2651C9.12144 18.3416 9.43219 18.3799 9.74903 18.3799C11.8999 18.3799 13.6486 16.5483 13.6486 14.2955V10.2111H16.3418C17.0791 10.2111 17.7554 10.645 18.0844 11.3407L18.5231 12.2533H22.4227C22.9589 12.2533 23.3976 12.7128 23.3976 13.2744V15.3166C23.3976 18.1374 21.2163 20.4222 18.5231 20.4222H15.5984V23.6578C15.5984 24.1237 15.2389 24.5066 14.7881 24.5066C14.6784 24.5066 14.5687 24.4811 14.4712 24.4364L8.45729 21.7368C8.05514 21.5581 7.79923 21.1433 7.79923 20.6902C7.79923 20.5115 7.83579 20.3392 7.915 20.1796L8.82897 18.2651ZM8.77413 10.2111H11.6988V14.2955C11.6988 15.4251 10.8275 16.3377 9.74903 16.3377C8.67055 16.3377 7.79923 15.4251 7.79923 14.2955V11.2322C7.79923 10.6706 8.23794 10.2111 8.77413 10.2111ZM16.5733 13.2744C16.5733 13.0036 16.4706 12.7439 16.2878 12.5524C16.105 12.3609 15.857 12.2533 15.5984 12.2533C15.3399 12.2533 15.0919 12.3609 14.9091 12.5524C14.7262 12.7439 14.6235 13.0036 14.6235 13.2744C14.6235 13.5452 14.7262 13.8049 14.9091 13.9964C15.0919 14.1879 15.3399 14.2955 15.5984 14.2955C15.857 14.2955 16.105 14.1879 16.2878 13.9964C16.4706 13.8049 16.5733 13.5452 16.5733 13.2744Z"
                                                        fill="#C9DDA0"></path>
                                                </svg>
                                            </div>
                                        </div>

                                        <div class="review-user">
                                            <h4>Paws & Bubbles</h4>
                                            <p>Hosted by Patrick B.</p>
                                            <div class="reviews-card-icon">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="82" height="24"
                                                    viewBox="0 0 82 24" fill="none">
                                                    <rect width="24" height="24" rx="12" fill="#CBDCE8" />
                                                    <path
                                                        d="M9 17C8.85833 17 8.73967 16.952 8.644 16.856C8.54833 16.76 8.50033 16.6413 8.5 16.5C8.49967 16.3587 8.54767 16.24 8.644 16.144C8.74033 16.048 8.859 16 9 16H15C15.1417 16 15.2605 16.048 15.3565 16.144C15.4525 16.24 15.5003 16.3587 15.5 16.5C15.4997 16.6413 15.4517 16.7602 15.356 16.8565C15.2603 16.9528 15.1417 17.0007 15 17H9ZM9.35 15.25C9.10833 15.25 8.89383 15.1708 8.7065 15.0125C8.51917 14.8542 8.4045 14.6542 8.3625 14.4125L7.8625 11.2375C7.84583 11.2375 7.82717 11.2397 7.8065 11.244C7.78583 11.2483 7.767 11.2503 7.75 11.25C7.54167 11.25 7.36467 11.1772 7.219 11.0315C7.07333 10.8858 7.00033 10.7087 7 10.5C6.99967 10.2913 7.07267 10.1143 7.219 9.969C7.36533 9.82367 7.54233 9.75067 7.75 9.75C7.95767 9.74933 8.13483 9.82233 8.2815 9.969C8.42817 10.1157 8.501 10.2927 8.5 10.5C8.5 10.5583 8.49367 10.6125 8.481 10.6625C8.46833 10.7125 8.45383 10.7583 8.4375 10.8L10 11.5L11.5625 9.3625C11.4708 9.29583 11.3958 9.20833 11.3375 9.1C11.2792 8.99167 11.25 8.875 11.25 8.75C11.25 8.54167 11.323 8.3645 11.469 8.2185C11.615 8.0725 11.792 7.99967 12 8C12.208 8.00033 12.3852 8.07333 12.5315 8.219C12.6778 8.36467 12.7507 8.54167 12.75 8.75C12.75 8.875 12.7208 8.99167 12.6625 9.1C12.6042 9.20833 12.5292 9.29583 12.4375 9.3625L14 11.5L15.5625 10.8C15.5458 10.7583 15.5312 10.7125 15.5185 10.6625C15.5058 10.6125 15.4997 10.5583 15.5 10.5C15.5 10.2917 15.573 10.1145 15.719 9.9685C15.865 9.8225 16.042 9.74967 16.25 9.75C16.458 9.75033 16.6352 9.82333 16.7815 9.969C16.9278 10.1147 17.0007 10.2917 17 10.5C16.9993 10.7083 16.9265 10.8855 16.7815 11.0315C16.6365 11.1775 16.4593 11.2503 16.25 11.25C16.2333 11.25 16.2147 11.248 16.194 11.244C16.1733 11.24 16.1545 11.2378 16.1375 11.2375L15.6375 14.4125C15.5958 14.6542 15.4813 14.8542 15.294 15.0125C15.1067 15.1708 14.892 15.25 14.65 15.25H9.35ZM9.35 14.25H14.65L14.975 12.1625L14.4 12.4125C14.1833 12.5042 13.9625 12.5208 13.7375 12.4625C13.5125 12.4042 13.3292 12.2792 13.1875 12.0875L12 10.45L10.8125 12.0875C10.6708 12.2792 10.4875 12.4042 10.2625 12.4625C10.0375 12.5208 9.81667 12.5042 9.6 12.4125L9.025 12.1625L9.35 14.25Z"
                                                        fill="white" />
                                                    <rect x="29" width="24" height="24" rx="12" fill="#FFA899" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M43.4127 14.893C43.4417 14.3436 43.3153 13.7971 43.0481 13.3161C42.7809 12.8351 42.3837 12.4391 41.9019 12.1733C41.8164 12.1238 41.715 12.1095 41.619 12.1333C41.5231 12.1572 41.4402 12.2173 41.3877 12.3011L40.775 13.2818L40.2653 12.6653C40.2373 12.6315 40.2026 12.6038 40.1634 12.5841C40.1242 12.5644 40.0813 12.553 40.0375 12.5507C39.9936 12.5484 39.9498 12.5551 39.9087 12.5706C39.8676 12.586 39.8302 12.6098 39.7988 12.6405C39.4987 12.9308 39.2643 13.2821 39.1114 13.6707C38.9584 14.0592 38.8905 14.476 38.9121 14.893C38.9121 15.4897 39.1492 16.062 39.5711 16.484C39.9931 16.9059 40.5654 17.143 41.1621 17.143C41.7588 17.143 42.3311 16.9059 42.7531 16.484C43.175 16.062 43.4121 15.4897 43.4121 14.893M40.0098 13.3549L39.9704 13.407C39.6715 13.8245 39.5217 14.3305 39.5452 14.8434L39.5471 14.8809C39.5471 15.3091 39.7172 15.7197 40.0199 16.0225C40.3227 16.3252 40.7333 16.4953 41.1615 16.4953C41.5896 16.4953 42.0003 16.3252 42.303 16.0225C42.6058 15.7197 42.7759 15.3091 42.7759 14.8809L42.7778 14.8441C42.7822 14.8028 42.8814 13.6663 41.8416 12.8934L41.7901 12.8559L41.1265 13.9174C41.0945 13.9686 41.0507 14.0114 40.9989 14.0424C40.9471 14.0734 40.8886 14.0916 40.8283 14.0956C40.7681 14.0995 40.7077 14.0892 40.6523 14.0652C40.5968 14.0413 40.5478 14.0046 40.5094 13.9581L40.0098 13.3549Z"
                                                        fill="#FEFEFE" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M40.797 7.30821C40.8102 7.23997 40.8419 7.17666 40.8886 7.12514C40.9352 7.07362 40.9951 7.03586 41.0617 7.01595C41.1283 6.99604 41.1991 6.99475 41.2664 7.0122C41.3337 7.02966 41.3949 7.06521 41.4434 7.11499C41.5667 7.24084 41.8038 7.48999 42.0447 7.77727C42.2811 8.05884 42.5392 8.39761 42.6879 8.69951C42.8328 8.99443 42.9866 9.37578 43.108 9.69421L43.673 8.75354C43.7046 8.701 43.7483 8.65683 43.8005 8.62476C43.8527 8.59269 43.9118 8.57367 43.9729 8.56931C44.034 8.56494 44.0953 8.57536 44.1515 8.59969C44.2077 8.62401 44.2573 8.66152 44.2959 8.70905C45.0987 9.70057 45.4985 10.7626 45.6974 11.5736C45.7972 11.9798 45.8474 12.3249 45.8728 12.5703C45.8858 12.6928 45.8942 12.8157 45.8982 12.9389V12.9713C45.8982 15.4482 43.9336 17.4649 41.4479 17.4649C38.9621 17.4649 37 15.4476 37 12.97C37 12.288 37.3222 10.6819 38.2756 9.36498C38.3127 9.31422 38.3617 9.27341 38.4183 9.24615C38.4749 9.2189 38.5373 9.20605 38.6001 9.20876C38.6629 9.21146 38.724 9.22963 38.7781 9.26166C38.8321 9.29368 38.8774 9.33856 38.9099 9.39231L39.5551 10.4671C39.7508 10.1607 40.0127 9.73044 40.2116 9.3332C40.4976 8.76117 40.7246 7.68257 40.797 7.30885M41.3201 7.91265C41.2051 8.42113 41.015 9.14697 40.7798 9.61858C40.4671 10.2434 40.0165 10.9298 39.8614 11.1605C39.8255 11.2134 39.7769 11.2565 39.7201 11.2857C39.6632 11.315 39.5999 11.3294 39.5359 11.3278C39.472 11.3261 39.4095 11.3084 39.3542 11.2763C39.2989 11.2442 39.2526 11.1987 39.2195 11.144L38.5712 10.0648C37.8746 11.1917 37.6356 12.4584 37.6356 12.9713C37.6356 15.1056 39.3224 16.8281 41.4479 16.8281C43.5733 16.8281 45.2627 15.1056 45.2627 12.9713V12.9535L45.2601 12.89C45.2557 12.8048 45.2491 12.7199 45.2404 12.6351C45.2071 12.3287 45.1536 12.0249 45.0802 11.7256C44.8783 10.8916 44.5203 10.1033 44.0252 9.40248L43.3807 10.4753C43.3434 10.5373 43.2892 10.5874 43.2246 10.6199C43.1599 10.6523 43.0874 10.6657 43.0154 10.6585C42.9434 10.6513 42.875 10.6238 42.818 10.5792C42.761 10.5347 42.7179 10.4748 42.6936 10.4067C42.5906 10.1181 42.3383 9.42917 42.1171 8.98044C42.0046 8.751 41.7892 8.46117 41.5578 8.18596C41.4801 8.09353 41.4009 8.00242 41.3201 7.91265Z"
                                                        fill="#FEFEFE" />
                                                    <rect x="58" width="24" height="24" rx="12" fill="#D8E8B7" />
                                                    <path
                                                        d="M70 14.619C69.7348 14.619 69.4804 14.5187 69.2929 14.3401C69.1054 14.1615 69 13.9193 69 13.6667C69 13.1381 69.445 12.7143 70 12.7143C70.2652 12.7143 70.5196 12.8146 70.7071 12.9932C70.8946 13.1718 71 13.4141 71 13.6667C71 13.9193 70.8946 14.1615 70.7071 14.3401C70.5196 14.5187 70.2652 14.619 70 14.619ZM73 16.0476V11.2857H67V16.0476H73ZM73 10.3333C73.2652 10.3333 73.5196 10.4337 73.7071 10.6123C73.8946 10.7909 74 11.0331 74 11.2857V16.0476C74 16.3002 73.8946 16.5424 73.7071 16.7211C73.5196 16.8997 73.2652 17 73 17H67C66.7348 17 66.4804 16.8997 66.2929 16.7211C66.1054 16.5424 66 16.3002 66 16.0476V11.2857C66 10.7571 66.445 10.3333 67 10.3333H67.5V9.38095C67.5 8.74948 67.7634 8.14388 68.2322 7.69736C68.7011 7.25085 69.337 7 70 7C70.3283 7 70.6534 7.06159 70.9567 7.18124C71.26 7.30089 71.5356 7.47627 71.7678 7.69736C71.9999 7.91846 72.1841 8.18093 72.3097 8.4698C72.4353 8.75867 72.5 9.06828 72.5 9.38095V10.3333H73ZM70 7.95238C69.6022 7.95238 69.2206 8.10289 68.9393 8.3708C68.658 8.63871 68.5 9.00207 68.5 9.38095V10.3333H71.5V9.38095C71.5 9.00207 71.342 8.63871 71.0607 8.3708C70.7794 8.10289 70.3978 7.95238 70 7.95238Z"
                                                        fill="white" />
                                                </svg>
                                            </div>
                                            <div class="review-meta">
                                                <span><svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                        viewBox="0 0 10 14" fill="none">
                                                        <path
                                                            d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                            fill="#FFC97A" />
                                                    </svg> 2.5 mi</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="review-content">

                                        <div class="review-top">
                                            <div class="stars"><svg xmlns="http://www.w3.org/2000/svg" width="163"
                                                    height="30" viewBox="0 0 163 30" fill="none">
                                                    <path
                                                        d="M12.6642 6.04253C13.2526 4.17853 15.8904 4.17854 16.4787 6.04254L17.4017 8.96675C17.6644 9.79894 18.4363 10.3647 19.309 10.3647H22.4163C24.3397 10.3647 25.1544 12.8144 23.6142 13.9664L21.0015 15.9204C20.3207 16.4296 20.0362 17.3134 20.2921 18.124L21.2664 21.2107C21.8512 23.0636 19.7172 24.578 18.1613 23.4143L15.7693 21.6254C15.0591 21.0942 14.0838 21.0942 13.3736 21.6254L10.9817 23.4143C9.42574 24.578 7.29174 23.0636 7.87658 21.2107L8.85085 18.124C9.10672 17.3134 8.8222 16.4296 8.14146 15.9204L5.52874 13.9664C3.98849 12.8144 4.80322 10.3647 6.7266 10.3647H9.83398C10.7066 10.3647 11.4786 9.79893 11.7412 8.96674L12.6642 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M46.0939 6.04253C46.6823 4.17853 49.3201 4.17854 49.9084 6.04254L50.8314 8.96675C51.0941 9.79894 51.866 10.3647 52.7386 10.3647H55.846C57.7694 10.3647 58.5841 12.8144 57.0439 13.9664L54.4312 15.9204C53.7504 16.4296 53.4659 17.3134 53.7218 18.124L54.696 21.2107C55.2809 23.0636 53.1469 24.578 51.5909 23.4143L49.199 21.6254C48.4888 21.0942 47.5135 21.0942 46.8033 21.6254L44.4114 23.4143C42.8554 24.578 40.7214 23.0636 41.3063 21.2107L42.2805 18.124C42.5364 17.3134 42.2519 16.4296 41.5711 15.9204L38.9584 13.9664C37.4182 12.8144 38.2329 10.3647 40.1563 10.3647H43.2637C44.1363 10.3647 44.9082 9.79893 45.1709 8.96674L46.0939 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M79.5216 6.04253C80.11 4.17853 82.7478 4.17854 83.3361 6.04254L84.2591 8.96675C84.5218 9.79894 85.2937 10.3647 86.1664 10.3647H89.2738C91.1971 10.3647 92.0119 12.8144 90.4716 13.9664L87.8589 15.9204C87.1782 16.4296 86.8936 17.3134 87.1495 18.124L88.1238 21.2107C88.7086 23.0636 86.5746 24.578 85.0187 23.4143L82.6267 21.6254C81.9165 21.0942 80.9413 21.0942 80.231 21.6254L77.8391 23.4143C76.2832 24.578 74.1492 23.0636 74.734 21.2107L75.7083 18.124C75.9641 17.3134 75.6796 16.4296 74.9989 15.9204L72.3862 13.9664C70.8459 12.8144 71.6606 10.3647 73.584 10.3647H76.6914C77.5641 10.3647 78.336 9.79893 78.5987 8.96674L79.5216 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M112.95 6.04253C113.539 4.17853 116.177 4.17854 116.765 6.04254L117.688 8.96675C117.951 9.79894 118.722 10.3647 119.595 10.3647H122.702C124.626 10.3647 125.441 12.8144 123.9 13.9664L121.288 15.9204C120.607 16.4296 120.322 17.3134 120.578 18.124L121.552 21.2107C122.137 23.0636 120.003 24.578 118.447 23.4143L116.055 21.6254C115.345 21.0942 114.37 21.0942 113.66 21.6254L111.268 23.4143C109.712 24.578 107.578 23.0636 108.163 21.2107L109.137 18.124C109.393 17.3134 109.108 16.4296 108.428 15.9204L105.815 13.9664C104.275 12.8144 105.089 10.3647 107.013 10.3647H110.12C110.993 10.3647 111.765 9.79893 112.027 8.96674L112.95 6.04253Z"
                                                        fill="#FFC97A" />
                                                    <path
                                                        d="M146.378 6.04253C146.966 4.17853 149.604 4.17854 150.193 6.04254L151.116 8.96675C151.378 9.79894 152.15 10.3647 153.023 10.3647H156.13C158.054 10.3647 158.868 12.8144 157.328 13.9664L154.715 15.9204C154.035 16.4296 153.75 17.3134 154.006 18.124L154.98 21.2107C155.565 23.0636 153.431 24.578 151.875 23.4143L149.483 21.6254C148.773 21.0942 147.798 21.0942 147.087 21.6254L144.696 23.4143C143.14 24.578 141.006 23.0636 141.59 21.2107L142.565 18.124C142.821 17.3134 142.536 16.4296 141.855 15.9204L139.243 13.9664C137.702 12.8144 138.517 10.3647 140.44 10.3647H143.548C144.421 10.3647 145.192 9.79893 145.455 8.96674L146.378 6.04253Z"
                                                        fill="#EFEFEF" />
                                                </svg></div>
                                            <span class="date">25/02/2025</span>
                                            <span class="badge">Reviews Written</span>
                                        </div>

                                        <p class="review-text">
                                            Such a nice grooming environment. The space was tidy,
                                            well
                                            ventilated,
                                            and had all
                                            the
                                            equipment needed without feeling overwhelming. It felt
                                            very calm and
                                            welcoming,
                                            which
                                            made
                                            drop-off...
                                        </p>

                                        <div class="review-tags">
                                            <span>Private Room</span>
                                        </div>

                                    </div>


                                    <div class="review-menu">
                                        ⋯
                                        <div class="menu-dropdown">
                                            <button>Edit ✏</button>
                                            <button>Remove 🗑</button>
                                        </div>
                                    </div>

                                </div>

                            </div>

                        </div>



                        <button class="load-more">Load More</button>


                    </section>

                    <section id="rewards-section" class="rewards-container hidden mt-5">
                        <div class="reward-heading">
                            <h3 class="section-title">Rewards</h3>
                            <hr class="divider mt-4">
                        </div>
                        <div class="sub-title">
                            <h2>Rewards & Referrals</h2>
                            <p class="sub-grahp">Invite friends to Fursgo and earn rewards when they make their
                                first booking.
                            </p>
                        </div>


                        <div class="balance-cards">
                            <div class="card current">
                                <span class="amount">£24.60</span>
                                <span class="card-label">Current Balance</span>
                            </div>
                            <div class="card redeemed">
                                <span class="amount">£26.40</span>
                                <span class="card-label">Redeemed Rewards</span>
                            </div>
                        </div>

                        <div class="referral-grid">
                            <div class="input-section d-flex flex-column gap-45">
                                <div>
                                    <label>Your referral code</label>
                                    <div class="copy-box">
                                        <input type="text" value="FURSGO-9X3P" readonly>
                                        <button>Copy</button>
                                    </div>
                                </div>
                                <div>
                                    <label>Share your referral link</label>
                                    <div class="copy-box">
                                        <input type="text" value="fursgo.com/ref/FURSGO-9X3P" readonly>
                                        <button>Copy</button>
                                    </div>
                                </div>
                            </div>

                            <div class="promo-section d-flex flex-column gap-45">
                                <div class="promo">
                                    <p><strong>You get £10 credit</strong></p>
                                    <p>Your friend gets £5 off their first booking</p>
                                </div>

                                <div class="social-icons">
                                    <div class="promo-text">
                                        <p>Share to</p>
                                    </div>

                                    <div class="soical-group-icon d-flex align-items-center gap-20">
                                        <span class="icon-fb"><svg xmlns="http://www.w3.org/2000/svg" width="48"
                                                height="48" viewBox="0 0 48 48" fill="none">
                                                <path
                                                    d="M48 24C48 10.7452 37.2547 0 24 0C10.7452 0 0 10.7452 0 24C0 35.979 8.7765 45.9081 20.25 47.7084V30.9375H14.1562V24H20.25V18.7125C20.25 12.6975 23.8331 9.375 29.3153 9.375C31.941 9.375 34.6875 9.84375 34.6875 9.84375V15.75H31.6613C28.6798 15.75 27.75 17.6001 27.75 19.4981V24H34.4062L33.3422 30.9375H27.75V47.7084C39.2235 45.9081 48 35.9792 48 24Z"
                                                    fill="#1877F2" />
                                            </svg></span>
                                        <span class="icon-x"><svg xmlns="http://www.w3.org/2000/svg" width="48"
                                                height="48" viewBox="0 0 48 48" fill="none">
                                                <circle cx="24" cy="24" r="24" fill="black" />
                                                <path
                                                    d="M31.4337 12L24.7723 19.6147L19.0123 12H10.667L20.6363 25.0347L11.1883 35.8333H15.2337L22.5257 27.5L28.899 35.8333H37.035L26.643 22.0947L35.4763 12H31.4337ZM30.015 33.4133L15.3897 14.292H17.7937L32.255 33.412L30.015 33.4133Z"
                                                    fill="white" />
                                            </svg></span>
                                        <span class="icon-wa"><svg xmlns="http://www.w3.org/2000/svg" width="48"
                                                height="48" viewBox="0 0 48 48" fill="none">
                                                <circle cx="24" cy="24" r="24" fill="#25CC64" />
                                                <path
                                                    d="M24.1401 7.26758C28.6485 7.26947 32.8824 9.01218 36.0679 12.1748C39.2528 15.3375 41.0026 19.5382 41.0015 24.0078C40.9972 33.2255 33.4348 40.7344 24.1343 40.7344H24.1274L23.5601 40.7246C20.7291 40.629 17.9679 39.8308 15.5415 38.4023L14.9028 38.0264L14.689 37.9004L14.4478 37.9629L8.84229 39.4199L10.3315 34.0293L10.4019 33.7734L10.2593 33.5488L9.84326 32.8936C8.15576 30.2311 7.26486 27.1543 7.26611 23.9941C7.26961 14.7768 14.8321 7.26797 24.1401 7.26758Z"
                                                    stroke="white" stroke-width="1.2" />
                                                <path
                                                    d="M16.6953 14.6152C16.8714 14.6152 17.0454 14.6148 17.2129 14.6162L17.6914 14.627C17.8819 14.6347 18.0203 14.6443 18.1602 14.7246C18.2809 14.7941 18.4285 14.9333 18.5869 15.2393L18.6553 15.3809C18.8864 15.8908 19.2564 16.7877 19.5869 17.5947C19.9117 18.3877 20.2056 19.1121 20.2783 19.2568V19.2578C20.3938 19.4874 20.4457 19.7003 20.3223 19.9453C20.1866 20.2145 20.1123 20.3856 19.9834 20.5703L19.832 20.7637C19.5533 21.0862 19.2849 21.4361 19.0576 21.6611C18.9326 21.7844 18.7554 21.9574 18.6699 22.1855C18.5873 22.4064 18.5986 22.6482 18.7256 22.9141L18.7871 23.0293C19.0979 23.5586 20.1682 25.2911 21.7588 26.6982C23.7968 28.501 25.534 29.0711 26.0352 29.3203H26.0361C26.315 29.4587 26.576 29.5383 26.8281 29.5078C27.0904 29.4761 27.2945 29.3314 27.4717 29.1299C27.7758 28.785 28.7932 27.6005 29.1543 27.0635C29.3021 26.8432 29.4194 26.7914 29.5166 26.7803C29.6413 26.7661 29.7958 26.8079 30.0439 26.8975C30.2698 26.9791 31.0199 27.3305 31.7979 27.7031C32.5678 28.072 33.3436 28.4512 33.6055 28.5811C33.8823 28.7184 34.0828 28.8076 34.248 28.8975C34.4117 28.9865 34.4798 29.0466 34.5088 29.0938V29.0947C34.5105 29.0991 34.5163 29.1126 34.5225 29.1396C34.5308 29.1766 34.5378 29.2276 34.543 29.293C34.5533 29.4235 34.5524 29.5981 34.5342 29.8076C34.5023 30.174 34.4181 30.6362 34.2559 31.1396L34.1816 31.3584C33.992 31.8857 33.407 32.4454 32.707 32.8945C32.0106 33.3414 31.274 33.6326 30.8506 33.6709H30.8496C30.3499 33.7161 29.9221 33.8181 29.0576 33.7031C28.1859 33.5872 26.872 33.2475 24.6621 32.3828C20.0637 30.5833 16.9442 26.261 16.0469 24.958L15.8008 24.6006C15.6719 24.4297 15.1464 23.7369 14.6533 22.7734C14.1583 21.8061 13.707 20.5872 13.707 19.3623C13.7071 16.9041 14.998 15.7071 15.4785 15.1865C15.904 14.7256 16.4019 14.6152 16.6953 14.6152Z"
                                                    fill="white" stroke="white" stroke-width="0.5" />
                                            </svg></span>
                                        <span class="icon-tg"><svg xmlns="http://www.w3.org/2000/svg" width="48"
                                                height="48" viewBox="0 0 48 48" fill="none">
                                                <g clip-path="url(#clip0_50_1863)">
                                                    <path
                                                        d="M24 0C17.6362 0 11.5275 2.53013 7.03125 7.02938C2.5304 11.5304 0.00128793 17.6347 0 24C0 30.3626 2.53125 36.4714 7.03125 40.9706C11.5275 45.4699 17.6362 48 24 48C30.3638 48 36.4725 45.4699 40.9688 40.9706C45.4688 36.4714 48 30.3626 48 24C48 17.6374 45.4688 11.5286 40.9688 7.02938C36.4725 2.53013 30.3638 0 24 0Z"
                                                        fill="url(#paint0_linear_50_1863)" />
                                                    <path
                                                        d="M10.8637 23.7461C17.8612 20.6981 22.5262 18.6886 24.8587 17.7176C31.5262 14.9452 32.9099 14.4637 33.8137 14.4474C34.0124 14.4442 34.4549 14.4933 34.7437 14.7268C34.9837 14.9237 35.0512 15.1899 35.0849 15.3768C35.1149 15.5636 35.1562 15.9892 35.1224 16.3215C34.7624 20.1165 33.1987 29.3257 32.4037 33.5763C32.0699 35.3748 31.4062 35.9778 30.7649 36.0367C29.3699 36.165 28.3124 35.1157 26.9624 34.2311C24.8512 32.8462 23.6587 31.9845 21.6074 30.6333C19.2374 29.0718 20.7749 28.2135 22.1249 26.811C22.4774 26.4438 28.6199 20.8582 28.7362 20.3516C28.7512 20.2882 28.7662 20.052 28.6237 19.9275C28.4849 19.8026 28.2787 19.8453 28.1287 19.8791C27.9149 19.9271 24.5437 22.1576 18.0037 26.5702C17.0474 27.228 16.1812 27.5486 15.4012 27.5317C14.5462 27.5133 12.8962 27.0472 11.6699 26.649C10.1699 26.1603 8.97369 25.902 9.07869 25.0721C9.13119 24.6401 9.72744 24.198 10.8637 23.7461Z"
                                                        fill="white" />
                                                </g>
                                                <defs>
                                                    <linearGradient id="paint0_linear_50_1863" x1="2400" y1="0"
                                                        x2="2400" y2="4800" gradientUnits="userSpaceOnUse">
                                                        <stop stop-color="#2AABEE" />
                                                        <stop offset="1" stop-color="#229ED9" />
                                                    </linearGradient>
                                                    <clipPath id="clip0_50_1863">
                                                        <rect width="48" height="48" fill="white" />
                                                    </clipPath>
                                                </defs>
                                            </svg></span>
                                        <span class="icon-in"><svg xmlns="http://www.w3.org/2000/svg" width="48"
                                                height="48" viewBox="0 0 48 48" fill="none">
                                                <circle cx="24" cy="24" r="24" fill="#2667B4" />
                                                <path
                                                    d="M17.3333 14.668C17.333 15.3752 17.0517 16.0534 16.5513 16.5532C16.051 17.0531 15.3726 17.3337 14.6653 17.3333C13.9581 17.333 13.28 17.0517 12.7801 16.5513C12.2803 16.051 11.9996 15.3726 12 14.6653C12.0004 13.9581 12.2816 13.28 12.782 12.7801C13.2823 12.2803 13.9608 11.9996 14.668 12C15.3752 12.0004 16.0534 12.2816 16.5532 12.782C17.0531 13.2823 17.3337 13.9608 17.3333 14.668ZM17.4133 19.308H12.08V36.0013H17.4133V19.308ZM25.84 19.308H20.5333V36.0013H25.7867V27.2413C25.7867 22.3613 32.1467 21.908 32.1467 27.2413V36.0013H37.4133V25.428C37.4133 17.2013 28 17.508 25.7867 21.548L25.84 19.308Z"
                                                    fill="white" />
                                            </svg></span>
                                    </div>

                                </div>
                            </div>

                        </div>

                        <div class="steps-wrapper">
                            <div class="step-item">

                                <div class="step-circle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="344" height="346"
                                        viewBox="0 0 344 346" fill="none">
                                        <g filter="url(#filter0_f_55_951)">
                                            <path
                                                d="M233.723 135.946C227.632 123.702 219.682 115.365 211.93 109.243C205.84 104.791 187.21 96.3762 157.31 101.719C137.93 107.841 123.533 117.858 114.12 131.772C104.707 145.128 100 159.876 100 176.016C100 197.721 106.645 214.695 119.934 226.939C133.777 239.182 152.049 245.304 174.752 245.304C195.239 245.304 211.851 238.347 224.586 224.434C237.322 210.521 243.69 194.938 243.69 177.685C243.69 161.546 240.367 147.633 233.723 135.946Z"
                                                fill="#FFA899" fill-opacity="0.5" />
                                        </g>
                                        <defs>
                                            <filter id="filter0_f_55_951" x="0" y="0" width="343.689"
                                                height="345.305" filterUnits="userSpaceOnUse"
                                                color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix"
                                                    result="shape" />
                                                <feGaussianBlur stdDeviation="50"
                                                    result="effect1_foregroundBlur_55_951" />
                                            </filter>
                                        </defs>
                                    </svg>
                                    <div class="step-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="109" height="100"
                                            viewBox="0 0 109 100" fill="none">
                                            <path
                                                d="M2.86526 50.6914C1.03067 52.526 0 55.0143 0 57.6088V59.7827C0 59.7827 0 82.6088 32.6088 82.6088C65.2175 82.6088 48.5492 70.3212 55.7523 60.8694L62.3522 50.6914C60.5176 48.8568 58.0294 47.8262 55.4349 47.8262H9.78263C7.18811 47.8262 4.69986 48.8568 2.86526 50.6914Z"
                                                fill="#3B3731" />
                                            <path
                                                d="M93.4788 23.9127C93.4788 25.9111 93.0852 27.8899 92.3204 29.7362C91.5557 31.5824 90.4348 33.26 89.0217 34.6731C87.6086 36.0861 85.9311 37.207 84.0848 37.9718C82.2386 38.7365 80.2597 39.1302 78.2614 39.1302C76.263 39.1302 74.2842 38.7365 72.4379 37.9718C70.5916 37.207 68.9141 36.0861 67.501 34.6731C66.088 33.26 64.9671 31.5824 64.2023 29.7362C63.4376 27.8899 63.0439 25.9111 63.0439 23.9127C63.0439 19.8768 64.6472 16.0062 67.501 13.1524C70.3548 10.2986 74.2255 8.69531 78.2614 8.69531C82.2973 8.69531 86.1679 10.2986 89.0217 13.1524C91.8755 16.0062 93.4788 19.8768 93.4788 23.9127Z"
                                                fill="#3B3731" />
                                            <path
                                                d="M32.6082 39.1305C37.7973 39.1305 42.7737 37.0692 46.4429 33.4C50.1121 29.7308 52.1735 24.7543 52.1735 19.5653C52.1735 14.3762 50.1121 9.39973 46.4429 5.73053C42.7737 2.06133 37.7973 0 32.6082 0C27.4192 0 22.4427 2.06133 18.7735 5.73053C15.1043 9.39973 13.043 14.3762 13.043 19.5653C13.043 24.7543 15.1043 29.7308 18.7735 33.4C22.4427 37.0692 27.4192 39.1305 32.6082 39.1305Z"
                                                fill="#3B3731" />
                                            <path
                                                d="M77.0779 43.6802C83.2322 42.9411 89.4587 44.2455 94.7986 47.3931C100.138 50.5408 104.296 55.3567 106.63 61.0992C108.963 66.8416 109.344 73.1922 107.714 79.1724L107.555 79.731C105.86 85.4812 102.378 90.549 97.6062 94.1949L97.1413 94.5425C92.3043 98.0836 86.4615 99.9977 80.4568 99.9996C75.6696 100.002 70.9602 98.7897 66.7703 96.4742L54.9607 99.9087C54.6333 100.004 54.2888 100.021 53.9548 99.9585L53.8132 99.9273C53.4829 99.8427 53.1773 99.6821 52.9216 99.4585L52.8152 99.3589C52.5742 99.1179 52.394 98.824 52.2878 98.5015L52.2468 98.3609C52.1502 97.9836 52.1564 97.5874 52.2654 97.2134L55.7175 85.4175C52.8111 80.1628 51.657 74.1234 52.4128 68.1763L52.4919 67.6011C53.3711 61.6612 56.1181 56.1609 60.3279 51.8931L60.74 51.4839C65.0438 47.2965 70.5657 44.5914 76.5017 43.7544L77.0779 43.6802ZM92.2595 51.7007C87.8643 49.11 82.7391 48.0357 77.6736 48.6441C72.6084 49.2525 67.8838 51.5094 64.2273 55.0669C60.5706 58.6248 58.1842 63.2866 57.4372 68.3335C56.6903 73.3804 57.6232 78.5332 60.0925 82.9976L61.1013 84.8209L58.5124 93.6675L67.3689 91.0923L69.1882 92.0982C72.6369 94.0041 76.5135 95.0019 80.4538 94.9996C85.5557 94.998 90.5172 93.3198 94.571 90.2222C98.6249 87.1246 101.547 82.7793 102.889 77.857C104.231 72.9349 103.918 67.7084 101.997 62.982C100.076 58.2555 96.6547 54.2915 92.2595 51.7007Z"
                                                fill="#3B3731" />
                                            <path
                                                d="M67.4092 67.3927C67.4092 66.8161 67.6382 66.2632 68.0459 65.8555C68.4536 65.4478 69.0065 65.2188 69.5831 65.2188H91.3223C91.8988 65.2188 92.4518 65.4478 92.8595 65.8555C93.2671 66.2632 93.4962 66.8161 93.4962 67.3927C93.4962 67.9692 93.2671 68.5222 92.8595 68.9299C92.4518 69.3375 91.8988 69.5666 91.3223 69.5666H69.5831C69.0065 69.5666 68.4536 69.3375 68.0459 68.9299C67.6382 68.5222 67.4092 67.9692 67.4092 67.3927Z"
                                                fill="white" />
                                            <path
                                                d="M67.4092 67.3927C67.4092 66.8161 67.6382 66.2632 68.0459 65.8555C68.4536 65.4478 69.0065 65.2188 69.5831 65.2188H91.3223C91.8988 65.2188 92.4518 65.4478 92.8595 65.8555C93.2671 66.2632 93.4962 66.8161 93.4962 67.3927C93.4962 67.9692 93.2671 68.5222 92.8595 68.9299C92.4518 69.3375 91.8988 69.5666 91.3223 69.5666H69.5831C69.0065 69.5666 68.4536 69.3375 68.0459 68.9299C67.6382 68.5222 67.4092 67.9692 67.4092 67.3927Z"
                                                fill="#3B3731" />
                                            <path
                                                d="M69.5831 78.2619C69.0065 78.2619 68.4536 78.0329 68.0459 77.6252C67.6382 77.2175 67.4092 76.6645 67.4092 76.088C67.4092 75.5114 67.6382 74.9585 68.0459 74.5508C68.4536 74.1431 69.0065 73.9141 69.5831 73.9141H80.4527C81.0292 73.9141 81.5822 74.1431 81.9899 74.5508C82.3976 74.9585 82.6266 75.5114 82.6266 76.088C82.6266 76.6645 82.3976 77.2175 81.9899 77.6252C81.5822 78.0329 81.0292 78.2619 80.4527 78.2619H69.5831Z"
                                                fill="white" />
                                            <path
                                                d="M69.5831 78.2619C69.0065 78.2619 68.4536 78.0329 68.0459 77.6252C67.6382 77.2175 67.4092 76.6645 67.4092 76.088C67.4092 75.5114 67.6382 74.9585 68.0459 74.5508C68.4536 74.1431 69.0065 73.9141 69.5831 73.9141H80.4527C81.0292 73.9141 81.5822 74.1431 81.9899 74.5508C82.3976 74.9585 82.6266 75.5114 82.6266 76.088C82.6266 76.6645 82.3976 77.2175 81.9899 77.6252C81.5822 78.0329 81.0292 78.2619 80.4527 78.2619H69.5831Z"
                                                fill="#3B3731" />
                                        </svg>
                                    </div>
                                </div>


                                <div class="step-text">
                                    <span class="number">1</span>
                                    <p class="medium-font">Share your referral link</p>
                                </div>
                            </div>
                            <div class="connector curve-down">
                                <svg xmlns="http://www.w3.org/2000/svg" width="123" height="20" viewBox="0 0 123 20"
                                    fill="none">
                                    <path d="M0.568359 0.844492C19.9187 14.2238 71.2988 32.9547 122.017 0.844492"
                                        stroke="#3B3731" stroke-width="2" stroke-dasharray="10 10" />
                                </svg>
                            </div>
                            <div class="step-item step-item-2">

                                <div class="step-circle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="344" height="346"
                                        viewBox="0 0 344 346" fill="none">
                                        <g filter="url(#filter0_f_55_950)">
                                            <path
                                                d="M233.723 135.946C227.632 123.702 219.682 115.365 211.93 109.243C205.84 104.791 187.21 96.3762 157.31 101.719C137.93 107.841 123.533 117.858 114.12 131.772C104.707 145.128 100 159.876 100 176.016C100 197.721 106.645 214.695 119.934 226.939C133.777 239.182 152.049 245.304 174.752 245.304C195.239 245.304 211.851 238.347 224.586 224.434C237.322 210.521 243.69 194.938 243.69 177.685C243.69 161.546 240.367 147.633 233.723 135.946Z"
                                                fill="#CBDCE8" fill-opacity="0.5" />
                                        </g>
                                        <defs>
                                            <filter id="filter0_f_55_950" x="0" y="0" width="343.689"
                                                height="345.305" filterUnits="userSpaceOnUse"
                                                color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix"
                                                    result="shape" />
                                                <feGaussianBlur stdDeviation="50"
                                                    result="effect1_foregroundBlur_55_950" />
                                            </filter>
                                        </defs>
                                    </svg>
                                    <div class="step-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="106" height="100"
                                            viewBox="0 0 106 100" fill="none">
                                            <path
                                                d="M18.1714 19.697C18.1714 16.0804 19.6073 12.6119 22.1631 10.0546C24.719 7.49729 28.1855 6.06061 31.8 6.06061C35.4145 6.06061 38.881 7.49729 41.4369 10.0546C43.9927 12.6119 45.4286 16.0804 45.4286 19.697C45.4286 23.3136 43.9927 26.782 41.4369 29.3393C38.881 31.8966 35.4145 33.3333 31.8 33.3333C28.1855 33.3333 24.719 31.8966 22.1631 29.3393C19.6073 26.782 18.1714 23.3136 18.1714 19.697ZM31.8 0C26.579 0 21.5719 2.07521 17.8801 5.76911C14.1883 9.46301 12.1143 14.473 12.1143 19.697C12.1143 24.9209 14.1883 29.9309 17.8801 33.6248C21.5719 37.3187 26.579 39.3939 31.8 39.3939C37.021 39.3939 42.0281 37.3187 45.7199 33.6248C49.4117 29.9309 51.4857 24.9209 51.4857 19.697C51.4857 14.473 49.4117 9.46301 45.7199 5.76911C42.0281 2.07521 37.021 0 31.8 0ZM0 57.5758C0 54.361 1.27632 51.2779 3.54819 49.0048C5.82006 46.7316 8.90138 45.4545 12.1143 45.4545H51.4857C53.5951 45.4528 55.6684 46.0023 57.5005 47.0485C55.8206 48.4465 54.2902 49.9919 52.9091 51.6848C52.4431 51.5721 51.9652 51.5151 51.4857 51.5152H12.1143C10.5078 51.5152 8.96717 52.1537 7.83124 53.2903C6.6953 54.4268 6.05714 55.9684 6.05714 57.5758V58.0485L6.09954 58.5515C6.42823 61.4567 7.46397 64.2369 9.116 66.6485C12.0658 70.9151 18.2502 75.7576 31.8 75.7576C37.5785 75.7576 42.0184 74.8788 45.4407 73.5333C45.4932 75.6545 45.7355 77.7151 46.1675 79.7151C42.2546 81.0303 37.524 81.8182 31.8 81.8182C16.5784 81.8182 8.37703 76.2424 4.13097 70.097C1.89527 66.8377 0.501012 63.0752 0.0726855 59.1455C0.0381301 58.7927 0.0138914 58.439 0 58.0848V57.5758ZM69.6571 24.2424C69.6571 21.8314 70.6144 19.5191 72.3183 17.8142C74.0222 16.1093 76.3332 15.1515 78.7429 15.1515C81.1525 15.1515 83.4635 16.1093 85.1674 17.8142C86.8713 19.5191 87.8286 21.8314 87.8286 24.2424C87.8286 26.6535 86.8713 28.9658 85.1674 30.6707C83.4635 32.3755 81.1525 33.3333 78.7429 33.3333C76.3332 33.3333 74.0222 32.3755 72.3183 30.6707C70.6144 28.9658 69.6571 26.6535 69.6571 24.2424ZM78.7429 9.09091C74.7267 9.09091 70.8751 10.6872 68.0352 13.5287C65.1954 16.3701 63.6 20.224 63.6 24.2424C63.6 28.2609 65.1954 32.1147 68.0352 34.9562C70.8751 37.7976 74.7267 39.3939 78.7429 39.3939C82.759 39.3939 86.6106 37.7976 89.4505 34.9562C92.2903 32.1147 93.8857 28.2609 93.8857 24.2424C93.8857 20.224 92.2903 16.3701 89.4505 13.5287C86.6106 10.6872 82.759 9.09091 78.7429 9.09091ZM106 72.7273C106 79.9604 103.128 86.8974 98.0166 92.012C92.9049 97.1266 85.9719 100 78.7429 100C71.5138 100 64.5809 97.1266 59.4691 92.012C54.3574 86.8974 51.4857 79.9604 51.4857 72.7273C51.4857 65.4941 54.3574 58.5572 59.4691 53.4425C64.5809 48.3279 71.5138 45.4545 78.7429 45.4545C85.9719 45.4545 92.9049 48.3279 98.0166 53.4425C103.128 58.5572 106 65.4941 106 72.7273ZM81.7714 60.6061C81.7714 59.8024 81.4524 59.0316 80.8844 58.4633C80.3164 57.895 79.5461 57.5758 78.7429 57.5758C77.9396 57.5758 77.1693 57.895 76.6013 58.4633C76.0334 59.0316 75.7143 59.8024 75.7143 60.6061V69.697H66.6286C65.8253 69.697 65.055 70.0162 64.487 70.5845C63.9191 71.1528 63.6 71.9236 63.6 72.7273C63.6 73.531 63.9191 74.3017 64.487 74.87C65.055 75.4383 65.8253 75.7576 66.6286 75.7576H75.7143V84.8485C75.7143 85.6522 76.0334 86.4229 76.6013 86.9912C77.1693 87.5595 77.9396 87.8788 78.7429 87.8788C79.5461 87.8788 80.3164 87.5595 80.8844 86.9912C81.4524 86.4229 81.7714 85.6522 81.7714 84.8485V75.7576H90.8571C91.6604 75.7576 92.4307 75.4383 92.9987 74.87C93.5666 74.3017 93.8857 73.531 93.8857 72.7273C93.8857 71.9236 93.5666 71.1528 92.9987 70.5845C92.4307 70.0162 91.6604 69.697 90.8571 69.697H81.7714V60.6061Z"
                                                fill="#3B3731" />
                                        </svg>
                                    </div>
                                </div>

                                <div class="step-text">
                                    <span class="number">2</span>
                                    <p class="medium-font">Your friend signs up and books</p>
                                </div>
                            </div>
                            <div class="connector curve-up">
                                <svg xmlns="http://www.w3.org/2000/svg" width="121" height="20" viewBox="0 0 121 20"
                                    fill="none">
                                    <path d="M0.574219 18.7297C19.642 5.35046 70.2721 -13.3805 120.25 18.7297"
                                        stroke="#3B3731" stroke-width="2" stroke-dasharray="10 10" />
                                </svg>
                            </div>
                            <div class="step-item">

                                <div class="step-circle">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="344" height="346"
                                        viewBox="0 0 344 346" fill="none">
                                        <g filter="url(#filter0_f_55_949)">
                                            <path
                                                d="M233.723 135.946C227.632 123.702 219.682 115.365 211.93 109.243C205.84 104.791 187.21 96.3762 157.31 101.719C137.93 107.841 123.533 117.858 114.12 131.772C104.707 145.128 100 159.876 100 176.016C100 197.721 106.645 214.695 119.934 226.939C133.777 239.182 152.049 245.304 174.752 245.304C195.239 245.304 211.851 238.347 224.586 224.434C237.322 210.521 243.69 194.938 243.69 177.685C243.69 161.546 240.367 147.633 233.723 135.946Z"
                                                fill="#D8E8B7" fill-opacity="0.5" />
                                        </g>
                                        <defs>
                                            <filter id="filter0_f_55_949" x="0" y="0" width="343.689"
                                                height="345.305" filterUnits="userSpaceOnUse"
                                                color-interpolation-filters="sRGB">
                                                <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                                <feBlend mode="normal" in="SourceGraphic" in2="BackgroundImageFix"
                                                    result="shape" />
                                                <feGaussianBlur stdDeviation="50"
                                                    result="effect1_foregroundBlur_55_949" />
                                            </filter>
                                        </defs>
                                    </svg>
                                    <div class="step-svg">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="106" height="99"
                                            viewBox="0 0 106 99" fill="none">
                                            <path
                                                d="M2.86526 50.6914C1.03067 52.526 0 55.0143 0 57.6088V59.7827C0 59.7827 0 82.6088 32.6088 82.6088C65.2175 82.6088 48.5492 70.3212 55.7523 60.8694L62.3522 50.6914C60.5176 48.8568 58.0294 47.8262 55.4349 47.8262H9.78263C7.18811 47.8262 4.69986 48.8568 2.86526 50.6914Z"
                                                fill="#3B3731"></path>
                                            <path
                                                d="M93.4788 23.9127C93.4788 25.9111 93.0852 27.8899 92.3204 29.7362C91.5557 31.5824 90.4348 33.26 89.0217 34.6731C87.6086 36.0861 85.9311 37.207 84.0848 37.9718C82.2386 38.7365 80.2597 39.1302 78.2614 39.1302C76.263 39.1302 74.2842 38.7365 72.4379 37.9718C70.5916 37.207 68.9141 36.0861 67.501 34.6731C66.088 33.26 64.9671 31.5824 64.2023 29.7362C63.4376 27.8899 63.0439 25.9111 63.0439 23.9127C63.0439 19.8768 64.6472 16.0062 67.501 13.1524C70.3548 10.2986 74.2255 8.69531 78.2614 8.69531C82.2973 8.69531 86.1679 10.2986 89.0217 13.1524C91.8755 16.0062 93.4788 19.8768 93.4788 23.9127Z"
                                                fill="#3B3731"></path>
                                            <path
                                                d="M32.6082 39.1305C37.7973 39.1305 42.7737 37.0692 46.4429 33.4C50.1121 29.7308 52.1735 24.7543 52.1735 19.5653C52.1735 14.3762 50.1121 9.39973 46.4429 5.73053C42.7737 2.06133 37.7973 0 32.6082 0C27.4192 0 22.4427 2.06133 18.7735 5.73053C15.1043 9.39973 13.043 14.3762 13.043 19.5653C13.043 24.7543 15.1043 29.7308 18.7735 33.4C22.4427 37.0692 27.4192 39.1305 32.6082 39.1305Z"
                                                fill="#3B3731"></path>
                                            <path
                                                d="M100.808 72C100.808 59.9559 91.044 50.1923 78.9999 50.1923C66.9559 50.1923 57.1923 59.9559 57.1923 72C57.1923 84.0441 66.9559 93.8077 78.9999 93.8077V99C64.0883 99 52 86.9117 52 72C52 57.0883 64.0883 45 78.9999 45C93.9116 45 106 57.0883 106 72C106 86.9117 93.9116 99 78.9999 99V93.8077C91.044 93.8077 100.808 84.0441 100.808 72Z"
                                                fill="#3B3731"></path>
                                            <path
                                                d="M76.6778 58.566C77.2733 56.577 79.9348 56.577 80.5302 58.566L82.4668 65.0348C82.7327 65.923 83.5121 66.5266 84.393 66.5266H90.7692C92.7137 66.5266 93.5358 69.1469 91.9769 70.3761L86.729 74.5145C86.0392 75.0584 85.7508 76.0047 86.0105 76.8722L87.9936 83.4966C88.5859 85.4749 86.4327 87.0948 84.8597 85.8544L79.8118 81.8738C79.0953 81.3088 78.1128 81.3088 77.3963 81.8738L72.3484 85.8544C70.7754 87.0948 68.6222 85.4749 69.2144 83.4966L71.1975 76.8722C71.4573 76.0047 71.1689 75.0584 70.4791 74.5145L65.2311 70.3761C63.6723 69.1469 64.4944 66.5266 66.4389 66.5266H72.8151C73.696 66.5266 74.4754 65.923 74.7413 65.0348L76.6778 58.566Z"
                                                fill="#3B3731"></path>
                                        </svg>
                                    </div>
                                </div>

                                <div class="step-text">
                                    <span class="number">3</span>
                                    <p class="medium-font">You both earn rewards</p>
                                </div>
                            </div>
                        </div>

                    </section>
                </div>

            </div>
        </div>
        <div class="col-lg-1"></div>
    </div>
</div>
@endsection

@push('script')
<script>
    // Handle primary profile tabs (profile, pets, favourites, reviews, rewards)
    document.addEventListener('DOMContentLoaded', () => {
        const tabs = document.querySelectorAll('.tab');

        // map the text value to the corresponding section element
        const sections = {
            'My Profile': document.getElementById('profile-section'),
            'My Pets': document.getElementById('pets-view'), // already used elsewhere
            'Favourites': document.getElementById('favourites-section'),
            'Reviews': document.getElementById('reviews-section'),
            'Rewards': document.getElementById('rewards-section')
        };

        // hide all sections initially then show profile section
        Object.values(sections).forEach(sec => {
            if (sec) {
                sec.classList.add('hidden');
                sec.style.display = 'none';
            }
        });
        if (sections['My Profile']) {
            sections['My Profile'].classList.remove('hidden');
            sections['My Profile'].style.display = 'block';
        }

        // make sure a reasonable tab is active by default
        tabs.forEach(t => t.classList.remove('active'));
        const defaultTab = Array.from(tabs).find(t => t.textContent.trim() === 'My Profile') || tabs[0];
        if (defaultTab) defaultTab.classList.add('active');

        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                // reset all tab state
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');

                // hide every section
                Object.values(sections).forEach(sec => {
                    if (sec) {
                        sec.classList.add('hidden');
                        sec.style.display = 'none';
                    }
                });

                const label = tab.textContent.trim();
                const target = sections[label];
                if (target) {
                    target.classList.remove('hidden');
                    target.style.display = 'block';
                }
            });
        });
    });

    // Handle profile edit/cancel
    document.addEventListener('DOMContentLoaded', () => {
        // Handle dots menu toggle
        const dotsMenu = document.querySelector('.dots-menu');
        const editBtn = document.querySelector('.edit-button');

        if (dotsMenu && editBtn) {
            dotsMenu.addEventListener('click', () => {
                editBtn.classList.toggle('show');
            });
        }
        const editBtnProfile = document.getElementById('edit-btn');
        const cancelBtn = document.getElementById('cancel-btn');
        const profileViewForm = document.getElementById('profile-view');
        const editForm = document.getElementById('edit-form-container');

        if (editBtnProfile && cancelBtn && profileViewForm && editForm) {
            editBtnProfile.addEventListener('click', () => {
                profileViewForm.classList.add('hidden');
                editForm.classList.remove('hidden');
            });

            cancelBtn.addEventListener('click', () => {
                editForm.classList.add('hidden');
                profileViewForm.classList.remove('hidden');
            });

            const profileForm = document.getElementById('profile-form');
            if (profileForm) {
                profileForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    alert('Profile Updated!');
                    editForm.classList.add('hidden');
                    profileViewForm.classList.remove('hidden');
                });
            }
        }
    });


    const reviewButtons = document.querySelectorAll(".pill");
    const writtenReviews = document.getElementById("written-reviews");
    const receivedReviews = document.getElementById("received-reviews");

    reviewButtons.forEach(button => {
        button.addEventListener("click", () => {

            reviewButtons.forEach(btn => btn.classList.remove("active"));
            button.classList.add("active");

            const type = button.getAttribute("data-review");

            writtenReviews.classList.remove("active");
            receivedReviews.classList.remove("active");


            if (type === "written") {
                writtenReviews.classList.add("active");
            } else if (type === "received") {
                receivedReviews.classList.add("active");
            } else if (type === "all") {
                writtenReviews.classList.add("active");
                receivedReviews.classList.add("active");
            }
        });
    });

    document.querySelector('.pill[data-review="written"]').click();

    function showSection(evt, sectionId) {
        // 1. Hide all sections
        var contents = document.getElementsByClassName("tab-content");
        for (var i = 0; i < contents.length; i++) {
            contents[i].style.display = "none";
        }

        // 2. Remove active from ALL tab-link buttons everywhere
        var links = document.getElementsByClassName("tab-link");
        for (var i = 0; i < links.length; i++) {
            links[i].classList.remove("active");
        }

        // 3. Show the target section
        document.getElementById(sectionId).style.display = "block";

        // 4. Activate the clicked button
        evt.currentTarget.classList.add("active");

        // 5. FIX: Also activate the matching button INSIDE the now-visible section
        var shownSection = document.getElementById(sectionId);
        var buttonsInSection = shownSection.querySelectorAll(".tab-link");
        buttonsInSection.forEach(function(btn) {
            var onclickAttr = btn.getAttribute("onclick") || "";
            if (onclickAttr.includes(sectionId)) {
                btn.classList.add("active");
            }
        });
    }

    // ===== Add New Pet: Toggle slider ↔ form =====
    document.addEventListener('DOMContentLoaded', () => {
        const addPetBtn = document.getElementById('btn-add-new-pet');
        const cancelPetBtn = document.getElementById('cancel-add-pet');
        const sliderContainer = document.getElementById('pets-slider-container');
        const formContainer = document.getElementById('add-pet-form-container');
        const petTypeSelect = document.getElementById('pet-type');
        const petBreedSelect = document.getElementById('pet-breed');

        let petBreedsData = null;

        // Load pet breeds JSON once
        fetch("{{ asset('assets/data/pet-breeds.json') }}")
            .then(res => res.json())
            .then(data => {
                petBreedsData = data.petTypes;

                if (petTypeSelect) {
                    petBreedsData.forEach(pt => {
                        const opt = document.createElement('option');
                        opt.value = pt.name;
                        opt.textContent = pt.name;
                        petTypeSelect.appendChild(opt);
                    });
                }
            })
            .catch(err => console.error('Failed to load pet breeds:', err));

        // When pet type changes, populate breeds
        if (petTypeSelect) {
            petTypeSelect.addEventListener('change', () => {
                const selectedType = petTypeSelect.value;
                if (petBreedSelect) {
                    petBreedSelect.innerHTML = '<option value="" disabled selected>Select breed</option>';
                    petBreedSelect.disabled = true;
                }

                if (petBreedsData && petBreedSelect) {
                    const match = petBreedsData.find(pt => pt.name === selectedType);
                    if (match) {
                        match.breeds.forEach(breed => {
                            const opt = document.createElement('option');
                            opt.value = breed;
                            opt.textContent = breed;
                            petBreedSelect.appendChild(opt);
                        });
                        petBreedSelect.disabled = false;
                    }
                }
            });
        }

        // Show form, hide slider
        if (addPetBtn) {
            addPetBtn.addEventListener('click', () => {
                sliderContainer.classList.add('hidden');
                formContainer.classList.remove('hidden');
                // Scroll to form
                formContainer.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            });
        }

        // Cancel: show slider, hide form
        if (cancelPetBtn) {
            cancelPetBtn.addEventListener('click', () => {
                formContainer.classList.add('hidden');
                sliderContainer.classList.remove('hidden');
                // Reset the form
                document.getElementById('add-pet-form').reset();
                if (petBreedSelect) {
                    petBreedSelect.innerHTML = '<option value="" disabled selected>Select breed</option>';
                    petBreedSelect.disabled = true;
                }
            });
        }

        // Handle form submit
        const addPetForm = document.getElementById('add-pet-form');
        if (addPetForm) {
            addPetForm.addEventListener('submit', (e) => {
                e.preventDefault();
                addPetForm.reset();
                if (petBreedSelect) {
                    petBreedSelect.innerHTML = '<option value="" disabled selected>Select breed</option>';
                    petBreedSelect.disabled = true;
                }
                formContainer.classList.add('hidden');
                sliderContainer.classList.remove('hidden');
            });
        }
    });


    const dateToggleBtn = document.getElementById('dateToggleBtn');
    const calendarCard = document.getElementById('calendarCard');

    // Only wire this legacy toggle if the expected elements exist on the page.
    // This prevents runtime JS errors that can break other UI scripts.
    if (dateToggleBtn && calendarCard) {
        dateToggleBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            calendarCard.classList.toggle('active');
        });

        // Kahin bhi bahar click karne se calendar band ho jaye
        document.addEventListener('click', function(e) {
            if (!dateToggleBtn.contains(e.target) && !calendarCard.contains(e.target)) {
                calendarCard.classList.remove('active');
            }
        });
    }
    // ===== Profile Page: Pet Type Auto-Detection (matches booking-groomer pattern) =====
    let profilePetBreedsData = {};

    async function loadProfilePetBreedsData() {
        try {
            const response = await fetch(
                "{{ asset('assets/data/pet-breeds.json') }}");
            if (!response.ok) throw new Error('Failed to load pet breeds');
            const data = await response.json();
            profilePetBreedsData = data;
            setupProfilePetTypeAutoDetection();
        } catch (error) {
            console.error('Error loading pet breeds data:', error);
        }
    }

    function setupProfilePetTypeAutoDetection() {
        const petTypeInput = document.getElementById('pet_type');
        const suggestionBox = document.getElementById('petTypeSuggestions_profile');
        const petBreedSelect = document.getElementById('pet-breed');
        const checkIcon = document.getElementById('petTypeCheck_profile');

        if (!petTypeInput || !profilePetBreedsData.petTypes) return;

        petTypeInput.addEventListener('input', function() {
            const inputValue = this.value.trim().toLowerCase();
            suggestionBox.innerHTML = '';

            if (inputValue.length === 0) {
                suggestionBox.style.display = 'none';
                petBreedSelect.innerHTML = '<option value="">Select a Breed</option>';
                if (petBreedSelect._fursDD) petBreedSelect._fursDD.refresh();
                if (checkIcon) checkIcon.style.display = 'none';
                return;
            }

            const matches = profilePetBreedsData.petTypes.filter(pt =>
                pt.name.toLowerCase().includes(inputValue)
            );

            if (matches.length > 0) {
                suggestionBox.style.display = 'block';
                matches.forEach(match => {
                    const item = document.createElement('div');
                    item.style.cssText = 'padding:10px;cursor:pointer;border-bottom:1px solid #EEE;color:#3B3731;font-family:Lato;font-size:16px;';
                    item.textContent = match.name;
                    item.addEventListener('mouseover', function() {
                        this.style.backgroundColor = '#f5f5f5';
                    });
                    item.addEventListener('mouseout', function() {
                        this.style.backgroundColor = 'transparent';
                    });
                    item.addEventListener('click', function() {
                        petTypeInput.value = match.name;
                        suggestionBox.style.display = 'none';
                        if (checkIcon) checkIcon.style.display = 'block';
                        profilePopulateBreeds(match);
                    });
                    suggestionBox.appendChild(item);
                });
            } else {
                suggestionBox.style.display = 'none';
                petBreedSelect.innerHTML = '<option value="">Select a Breed</option>';
                if (petBreedSelect._fursDD) petBreedSelect._fursDD.refresh();
            }

            // Exact match → auto-populate breeds
            const exactMatch = profilePetBreedsData.petTypes.find(p => p.name.toLowerCase() === inputValue);
            if (exactMatch) {
                profilePopulateBreeds(exactMatch);
                suggestionBox.style.display = 'none';
                if (checkIcon) checkIcon.style.display = 'block';
            } else {
                if (checkIcon) checkIcon.style.display = 'none';
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function(e) {
            if (e.target !== petTypeInput && !suggestionBox.contains(e.target)) {
                suggestionBox.style.display = 'none';
            }
        });

        petTypeInput.addEventListener('focus', function() {
            if (this.value.trim().length > 0) suggestionBox.style.display = 'block';
        });
    }

    function profilePopulateBreeds(petType) {
        const petBreedSelect = document.getElementById('pet-breed');
        petBreedSelect.innerHTML = '<option value="">Select a Breed</option>';
        if (petType && petType.breeds) {
            petType.breeds.forEach(breed => {
                const option = document.createElement('option');
                option.value = breed;
                option.textContent = breed;
                petBreedSelect.appendChild(option);
            });
        }
        if (petBreedSelect._fursDD) petBreedSelect._fursDD.refresh();
    }

    document.addEventListener('DOMContentLoaded', function() {
        loadProfilePetBreedsData();
    });
    // ===== End Profile Page Pet Type Auto-Detection =====

    document.addEventListener('DOMContentLoaded', function() {

        // ── Select Pet Type toggle ──────────────────────────────────────────
        const petTypeGroup = document.querySelector('.pf-toggle-group');
        if (petTypeGroup) {
            const typeButtons = petTypeGroup.querySelectorAll('button[type="button"]');
            const petTypeInput = document.getElementById('pet_type');
            const petBreedSelect = document.getElementById('pet-breed');
            const checkIcon = document.getElementById('petTypeCheck_profile');

            typeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    // Deactivate all, activate clicked
                    typeButtons.forEach(b => {
                        b.classList.remove('active');
                        b.querySelector('p') && (b.querySelector('p').style.color = '');
                    });
                    this.classList.add('active');
                    if (this.querySelector('p')) this.querySelector('p').style.color = '#fff';

                    const selected = this.querySelector('p')?.textContent.trim().toLowerCase();

                    if (selected === 'cat' || selected === 'dog') {
                        // Disable the free-text pet type input
                        if (petTypeInput) {
                            petTypeInput.value = '';
                            petTypeInput.disabled = true;
                            petTypeInput.style.background = '#f5f5f5';
                            petTypeInput.style.color = '#9d9b98';
                            petTypeInput.style.cursor = 'not-allowed';
                        }
                        if (checkIcon) checkIcon.style.display = 'none';

                        // Populate breeds from loaded data
                        const typeName = selected.charAt(0).toUpperCase() + selected.slice(1);
                        if (profilePetBreedsData?.petTypes) {
                            const match = profilePetBreedsData.petTypes.find(
                                pt => pt.name.toLowerCase() === selected
                            );
                            if (match) profilePopulateBreeds(match);
                        }
                    } else {
                        // "Other" — enable the free-text input
                        if (petTypeInput) {
                            petTypeInput.disabled = false;
                            petTypeInput.style.background = '';
                            petTypeInput.style.color = '';
                            petTypeInput.style.cursor = '';
                        }
                        // Clear breeds until user types
                        if (petBreedSelect) {
                            petBreedSelect.innerHTML = '<option value="">Select a Breed</option>';
                            if (petBreedSelect._fursDD) petBreedSelect._fursDD.refresh();
                        }
                    }
                });
            });
        }

        // ── Select Pet Size toggle ──────────────────────────────────────────
        const sizeGroups = document.querySelectorAll('.pf-toggle-group');
        // The second .pf-toggle-group is the size selector
        if (sizeGroups.length >= 2) {
            const sizeButtons = sizeGroups[1].querySelectorAll('button[type="button"]');
            sizeButtons.forEach(btn => {
                btn.addEventListener('click', function() {
                    sizeButtons.forEach(b => {
                        b.classList.remove('active');
                        if (b.querySelector('p')) b.querySelector('p').style.color = '';
                    });
                    this.classList.add('active');
                    if (this.querySelector('p')) this.querySelector('p').style.color = '#fff';
                });
            });
        }
    });

    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('.input-box').forEach(box => {
            const input = box.querySelector('input[type="number"]');
            const svg = box.querySelector('.number-icon');

            if (!input || !svg) return;

            svg.addEventListener('click', (e) => {
                const rect = svg.getBoundingClientRect();
                const clickY = e.clientY - rect.top;
                const half = rect.height / 2;

                const step = parseFloat(input.step) || 1;
                const min = input.min !== "" ? parseFloat(input.min) : -Infinity;
                const max = input.max !== "" ? parseFloat(input.max) : Infinity;
                let value = parseFloat(input.value) || 0;

                if (clickY < half) {
                    value = Math.min(max, value + step);
                } else {
                    value = Math.max(min, value - step);
                }

                input.value = value;
                input.dispatchEvent(new Event('input', {
                    bubbles: true
                }));
                input.dispatchEvent(new Event('change', {
                    bubbles: true
                }));
            });
        });
    });
</script>

@endpush
