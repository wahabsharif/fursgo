<!-- booking-groomer.blade.php -->

@extends('layouts.app')

@push('styles')
<style>
    body {
        background: #FDFCF8;
    }

    h1 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 1.75rem;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
    }

    p,
    span {
        color: #3B3731;
        font-family: Lato;
        line-height: normal;
    }

    /* step css  */
    .step-indicator {
        width: 100%;
        max-width: 15.625rem;
    }

    .steps-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: .75rem;
    }

    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
        position: relative;
    }

    .step-circle {
        width: 2.5rem;
        height: 2.5rem;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: .625rem;
        position: relative;
        z-index: 2;
        color: #FDFCF8;
        text-align: center;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .step-item.active .step-circle {
        background-color: #C9DDA0;
    }

    .step-item.inactive .step-circle {
        background-color: #e8e8e8;
        /* color: #c0c0c0 !important; */
    }

    .step-label {
        color: #C9DDA0;
        text-align: center;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .step-item.active .step-label {
        color: #C9DDA0;
    }

    .step-item.inactive .step-label {
        color: #d0d0d0;
    }

    .progress-bar {
        width: 100%;
        height: .5rem;
        background-color: #e8e8e8;
        border-radius: .25rem;
        overflow: hidden;
        position: relative;
    }

    .progress-fill {
        height: 100%;
        background-color: #C9DDA0;
        border-radius: .25rem;
        transition: width 0.3s ease;
    }

    .groomer-service-button.active {
        border: .0625rem solid var(--groomer-color);
        background-color: var(--groomer-color);
        color: #fff;
    }

    /* Animation for demo purposes */
    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }

    .step-item.active .step-circle {
        animation: pulse 2s infinite;
    }

    .booking-details {
        margin-top: 64px;
    }

    .card {
        margin-top: 32px;
        border-radius: .625rem;
        background: #F8F8F8;
        padding: 1.875rem;
        display: flex;
    }


    .card>.clip-path {
        display: flex;
        align-items: flex-start;
        position: relative;
    }

    .card>.clip-path>svg>image {
        width: 100%;
        height: 100%;
    }

    .card>.clip-path>.svg-icon {
        position: absolute;
        left: .1875rem;
        top: 0rem;
    }

    .card-body {
        width: 100%;
        padding-left: 1.875rem;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        align-items: start;
    }

    .card-body>div {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: end;
    }

    .card-body>div:first-child>div:first-child>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .card-body>div>div:first-child>div {
        margin-top: .625rem;
        width: 5.8125rem;
        height: 1.5rem;
        border-radius: 6.25rem;
        background: #FFC97A;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .card-body>div>div:first-child>div>p {
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .card-body>div>div:nth-child(2)>div:first-child {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .375rem;
    }

    .card-body>div>div:nth-child(2)>div:first-child>div:first-child {
        width: 5.125rem;
        height: 1.5rem;
        border-radius: 6.25rem;
        background: #CBDCE8;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .375rem;
    }

    .card-body>div>div:nth-child(2)>div:first-child>div:last-child {
        width: 5.8125rem;
        height: 1.5rem;
        border-radius: 6.25rem;
        background: #FFA899;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .375rem;
    }

    .card-body>div>div:nth-child(2)>div:first-child>div:first-child p {
        color: #FFF;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .card-body>div>div:nth-child(2)>div:first-child>div:last-child p {
        color: #FFF;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .card-body>div>div:nth-child(2)>div:first-child>div:first-child svg {
        width: .625rem;
        height: .5625rem;
        aspect-ratio: 10/9;
    }

    .card-body>div>div:nth-child(2)>div:last-child {
        margin-top: 1.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .625rem;
    }

    .card-body>div>div:nth-child(2)>div:last-child>div {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
    }

    .card-body-info>div>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        display: flex;
        gap: .3125rem;
    }

    .card-body-info>div>button {
        border-radius: 6.25rem;
        background: #EAE8E5;
        width: 4.1875rem;
        height: 2rem;
        border: none;
        cursor: pointer;
    }

    .pet-details-form {
        margin-top: 1.875rem;
    }

    .pet-details-form>div>h2 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 1.75rem;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
    }

    .pet-details-form>div:nth-child(2) {
        margin-top: 1.5625rem;
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 2.5rem;
    }

    .pet-details-form>div>button {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
        width: 11.8125rem;
        height: 3rem;
        border-radius: 6rem;
        border: .0625rem solid #3B3731;
        background: transparent;
        cursor: pointer;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-top: 32px;
    }

    .form-group {
        display: flex;
        flex-direction: column;
        position: relative;
        /* ADDED: for pseudo icon and opener button */
    }

    .form-group .input-check-icon {
        position: absolute;
        right: .75rem;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        display: none;
        line-height: 0;
        z-index: 5;
    }

    .form-group .input-check-icon.visible {
        display: block;
    }

    .form-group label {
        margin-bottom: .9375rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .form-group label>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .form-group input,
    .form-group select,
    .form-group option {
        padding: .5rem .625rem;
        font-size: .875rem;
        border-radius: .625rem;
        border: .0625rem solid #D4D4D4;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        height: 3rem;
    }

    .form-group select {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        padding-right: 2.5rem;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none"><path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="%233B3731" stroke-linecap="round" stroke-linejoin="round"/></svg>') !important;
        background-repeat: no-repeat !important;
        background-position: right .875rem center;
        background-size: .9375rem .5rem;
        cursor: pointer;
    }

    .form-group input[type="number"] {
        width: 5.3125rem;
        height: 3rem;
    }

    .form-group textarea {
        padding: .5rem .625rem;
        border-radius: .625rem;
        border: .0625rem solid #D4D4D4;
        background: #FFF;
        color: #D4D4D4;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        resize: none;

    }

    /* REPLACE the entire rule for the date input with this */
    .form-group input[type="date"] {
        -webkit-appearance: none;
        -moz-appearance: textfield;
        appearance: none;

        padding: .5rem 2.75rem .5rem .625rem;
        cursor: pointer;
        font-size: 1rem;
        border-radius: .625rem .625rem 0 0;
        border: .0625rem solid #D4D4D4;

        /* keep color separate so it doesn't override background-image */
        background-color: #FFF;
        color: #3B3731;

        /* SVG arrow (note: # must be encoded as %23) */
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none"><path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="%233B3731" stroke-linecap="round" stroke-linejoin="round"/></svg>');
        background-repeat: no-repeat;
        background-position: right .875rem center;
        /* .875rem from right + vertically centered */
        background-size: .9375rem .5rem;

        height: 3rem;
        box-sizing: border-box;
        outline: none;
    }

    /* Hide native calendar picker in WebKit (Chrome/Safari) */
    .form-group input[type="date"]::-webkit-calendar-picker-indicator {
        display: none;
    }

    /* hide inner clear/spin controls if present */
    .form-group input[type="date"]::-webkit-clear-button,
    .form-group input[type="date"]::-webkit-inner-spin-button {
        display: none;
    }

    /* Firefox tweaks */
    .form-group input[type="date"]::-moz-focus-inner {
        border: 0;
    }

    /* Pet Type Auto-Detection Suggestions */
    #petTypeSuggestions {
        box-shadow: 0 .25rem .5rem rgba(0, 0, 0, 0.1);
    }

    #petTypeSuggestions>div:last-child {
        border-bottom: none;
    }

    /* clickable (transparent) button that forwards to the input picker */
    .form-group .picker-opener {
        position: absolute;
        right: .375rem;
        top: 50%;
        transform: translateY(-50%);
        width: 2.125rem;
        height: 2.125rem;
        border: 0;
        background: transparent;
        cursor: pointer;
        padding: 0;
        margin: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    /* sex small circular radios (matches the screenshot) */
    .sex-options {
        display: flex;
        gap: 1.75rem;
        height: 100%;
        align-items: center;
        margin-top: 20.8px;
    }

    /* label that wraps each radio */
    .radio--small {
        display: inline-flex;
        align-items: center;
        gap: .625rem;
        /* gap between circle and text */
        cursor: pointer;
        user-select: none;
        position: relative;
    }

    /* keep native input for accessibility but hide native radio */
    .radio--small input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: .0625rem;
        height: .0625rem;
        margin: 0;
        pointer-events: none;
    }

    /* the visible circle */
    .radio--visual {
        width: 1.375rem;
        height: 1.375rem;
        border-radius: 50%;
        border: .125rem solid #ccc;
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
        width: 14.4px;
        height: 14.4px;
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
        font-size: 1rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }



    .radio--small input[type="radio"]:checked+.radio--visual::after {
        transform: translate(-50%, -50%) scale(1);
    }

    /* focus-visible for keyboard users */
    .radio--small input[type="radio"]:focus+.radio--visual {
        outline: .1875rem solid rgba(201, 221, 160, 0.22);
        outline-offset: .125rem;
    }




    .full-width {
        grid-column: span 3;
    }

    /* placeholder color for inputs & textareas (cross-browser) */
    .form-group input::placeholder,
    .form-group textarea::placeholder {
        color: #D4D4D4;
        opacity: 1;
        /* ensure consistent opacity across browsers */
    }

    /* WebKit/Blink */
    .form-group input::-webkit-input-placeholder,
    .form-group textarea::-webkit-input-placeholder {
        color: #D4D4D4;
    }

    /* Mozilla Firefox 19+ */
    .form-group input::-moz-placeholder,
    .form-group textarea::-moz-placeholder {
        color: #D4D4D4;
    }

    /* Mozilla Firefox 4 - 18 */
    .form-group input:-moz-placeholder,
    .form-group textarea:-moz-placeholder {
        color: #D4D4D4;
    }

    /* Internet Explorer 10+ */
    .form-group input:-ms-input-placeholder,
    .form-group textarea:-ms-input-placeholder {
        color: #D4D4D4;
    }

    /* Select "placeholder" (first disabled option) — note: styling options is inconsistent across browsers */
    .form-group select option[disabled][selected] {
        color: #D4D4D4;
    }

    .form-btns {
        display: flex;
        justify-content: flex-end;
        margin-top: 32px;
    }

    .form-btns div {
        display: flex;
        justify-content: flex-end;
        gap: 1.875rem;

    }

    .form-btns div>button:first-child {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-decoration-line: underline;
        text-decoration-style: solid;
        text-decoration-skip-ink: auto;
        text-decoration-thickness: auto;
        text-underline-offset: auto;
        text-underline-position: from-font;
        background: transparent;
        border: none;
        cursor: pointer;
    }

    .form-btns div>button:last-child {
        border-radius: 6rem;
        border: .0625rem solid #FFC97A;
        background: #FFF;
        box-shadow: 0 .3125rem .5rem 0 rgba(0, 0, 0, 0.05);
        width: 7.5rem;
        height: 3rem;
        cursor: pointer;
        color: #FFC97A;
        text-align: center;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;

    }

    .service-summary {
        width: 25rem;
        border-radius: .625rem;
        border: .0625rem solid #FFC97A;
        background: #FFF;
        padding: 1.25rem;
    }

    .service-summary>div>h3 {
        color: #3B3731;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-summary>div:nth-child(2) {
        margin-top: 32px;
        display: flex;
        align-items: flex-start;
        position: relative;
        /* border-bottom: .0625rem solid #FFC97A;
                                                                                                                                                        padding-bottom: 32px; */
    }

    .service-summary>div:nth-child(2)>div>svg {
        position: absolute;
    }

    .service-summary>div:nth-child(2)>div>img {
        width: 5.6757rem;
        height: 5.6757rem;
        aspect-ratio: 1/1;
        border-radius: 50%;
    }

    .service-summary>div:nth-child(2)>div:nth-child(2) {
        margin-left: 16px;
    }

    .service-summary>div:nth-child(2)>div:nth-child(2) p {
        color: #3B3731;
        font-family: Lato;
        font-size: 1.125rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-summary>div:nth-child(2)>div:nth-child(2) span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 1.125rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .service-summary>div:nth-child(2)>div:nth-child(2) {
        display: flex;
        flex-direction: column
    }

    .service-summary>div:nth-child(2)>div:nth-child(2)>div:nth-child(2) {
        display: flex;
        align-items: center;
        gap: .3125rem
    }

    .service-summary>div:nth-child(2)>div:nth-child(2)>div:nth-child(2) p {
        color: #3B3731;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .service-summary>div:nth-child(2)>div:nth-child(2)>div:nth-child(2) span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .divider {
        display: block;
        width: 100%;
        height: .125rem;
        background-color: #FFC97A;
        margin: 24px auto;
    }


    .service-summary>div:nth-child(4)>div {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .service-summary>div:nth-child(4)>div>p {
        color: #3B3731;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin-top: .625rem
    }

    .service-summary>div:nth-child(4)>span {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #9D9B98;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .service-summary>div:nth-child(6)>div {
        margin-top: 24px;
    }

    .service-summary>div:nth-child(6)>p {
        color: #3B3731;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        display: none;
    }

    .service-summary>div:nth-child(6)>div>div {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .service-summary>div:nth-child(6)>div>div>p {
        color: #3B3731;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 400;
        line-height: 1.4375rem;
        /* 164.286% */
    }

    .promocode {
        margin-top: 32px;
        width: 21.25rem;
        height: 7.3125rem;
        border-radius: .625rem;
        background: #F8F8F8;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: .625rem;
    }

    .promocode label {
        color: #3B3731;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .promocode input {
        border-radius: .625rem;
        border: .0625rem solid #D4D4D4;
        background: #FFF;
        width: 18.536rem;
        height: 3rem;
        padding: .5rem .625rem;
    }


    .sum div {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
    }

    .sum div p {
        color: #9D9B98;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 400;
        line-height: 1.4375rem;
    }

    .sum div span {
        color: #3B3731;
        text-align: right;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 400;
        line-height: 1.4375rem;
        /* 143.75% */
    }

    .total {
        margin-top: 32px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .total p,
    span {
        color: #3B3731;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .payment button {
        margin: 24px auto;
        width: 21.25rem;
        height: 3rem;
        border-radius: 6rem;
        background: #F8F8F8;
        border: transparent;
        color: #DDD;
        text-align: center;
        font-family: Lato;
        font-size: 1.125rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .caution {
        display: flex;
        align-items: center;
        width: 21.25rem;
        height: 5.5rem;
        gap: .625rem;
        padding: 1.25rem;
        border-radius: .625rem;
        background: #FFF;
        box-shadow: 0 .3125rem 3.4375rem 0 rgba(0, 0, 0, 0.05);

    }

    .caution svg {
        width: 5rem;
        height: 5rem;
    }

    .caution p {
        margin-left: 16px;
    }

    .need-help {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .625rem;
        margin-top: 24px;
    }

    .need-help p {
        color: #3B3731;
        font-family: Lato;
        font-size: 1.125rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .need-help p>a {
        color: #3B3731;
        font-family: Lato;
        font-size: 1.125rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-decoration-line: underline;
        text-decoration-style: solid;
        text-decoration-skip-ink: auto;
        text-decoration-thickness: auto;
        text-underline-offset: auto;
        text-underline-position: from-font;
    }

    .address-service h3 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 1.75rem;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
    }

    .address-service div {
        display: flex;
        flex-direction: column;
        position: relative;
    }

    .address-service .input-wrapper {
        position: relative;
        display: flex;
        align-items: center;
    }

    .address-service .input-wrapper svg {
        position: absolute;
        right: 1.25rem;
        top: 50%;
        bottom: 50%;
        pointer-events: none;
        z-index: 1;
    }

    .address-service .input-wrapper input {
        padding: 0 1.25rem !important;
        margin-top: .625rem;
    }

    .address-service div input::placeholder {
        color: #D4D4D4;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .address-service div label {
        color: #3B3731;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .address-service div input {
        width: 100%;
        border-radius: .625rem;
        border: .0625rem solid #D4D4D4;
        background: #FFF;
        height: 3rem;
    }






    .option {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: .6875rem .25rem;
        cursor: pointer;
        border-radius: .5rem;
        transition: background 0.12s;
        user-select: none;
    }

    .option:hover {
        background: rgba(0, 0, 0, 0.03);
    }

    /* Custom radio */
    .radio {
        width: 1.375rem;
        height: 1.375rem;
        border-radius: 50%;
        border: .125rem solid #ccc;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color 0.15s, background 0.15s;
        position: relative;
    }

    .radio::after {
        content: '';
        width: 14.4px;
        height: 14.4px;
        border-radius: 50%;
        background: #FFD88C;
        transform: scale(0);
        transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .option.selected .radio {
        border-color: #FFD88C;
        background: #fff;
    }

    .option.selected .radio::after {
        transform: scale(1);
    }

    .option-name {
        flex: 1;
        color: #9D9B98;
        font-family: Lato;
        font-size: 1.125rem;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }




    /* Add this to your <style> block */
    #home-address-option {
        flex-direction: row !important;
        align-items: center !important;
    }

    .continue-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .625rem;
        cursor: pointer;
        width: 25rem;
        height: 3rem;
        border-radius: 2.5rem;
        background: #F8F8F8;
        margin-left: auto;
        transition: background 0.3s ease;
    }

    .continue-btn button {
        color: #DDD;
        text-align: center;
        font-family: Lato;
        font-size: 1.125rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border: none;
        cursor: pointer;
        background: transparent;
        transition: color 0.3s ease;
    }

    .continue-btn.active {
        background: #C9DDA0;
    }

    .continue-btn.active button {
        color: #FFF;
    }

    .continue-btn.active svg path {
        fill: #FFF;
    }

    /* Pet Details Display Styles */
    .pet-details-display {
        display: none;
        margin-top: 1.875rem;
        padding: 1.875rem;
        border-radius: .625rem;
        border-radius: .625rem;
        background: #F8F8F8;
        border: none;
    }

    .pet-details-display.active {
        display: flex;
        align-items: center;
        justify-content: space-between
    }

    .pet-details-display h2 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 1.5rem;
        font-weight: 800;
        margin-bottom: 1.25rem;
    }

    .pet-details-display-content {
        display: flex;
        gap: 64px;
    }

    .pet-detail-item {
        display: flex;
        flex-direction: column;
    }

    .pet-detail-item label {
        display: flex;
        align-items: center;
        color: #9D9B98;
        font-family: Lato;
        font-size: .875rem;
        font-weight: 600;
        margin-bottom: .5rem;
        gap: .3125rem
    }

    .pet-detail-item span {
        color: #3B3731;
        font-family: Lato;
        font-size: 1rem;
        font-weight: 400;
    }

    .pet-detail-item.full-width {
        grid-column: span 2;
    }

    .pet-display-action-btns {
        display: flex;
        gap: .9375rem;
        margin-top: 1.25rem;
        justify-content: flex-end;
    }

    .pet-display-action-btns button {
        width: 4.1875rem;
        height: 2rem;
        border-radius: 6.25rem;
        background: #EAE8E5;
        border: none;
        cursor: pointer;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: .875rem;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }


    .pet-details-form.hidden {
        display: none;
    }

    .pet-photo-preview {
        position: relative;
        width: 5.3125rem;
        height: 5.3125rem;
        border-radius: 50%;
        overflow: hidden;
        flex-shrink: 0;
    }

    .pet-photo-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .pet-photo-col-wrapper {
        display: flex;
        align-items: center;
        gap: 1.25rem;
        width: 100%;
        margin-bottom: 1.25rem;
    }

    .pet-photo-action-btns {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex: 1;
    }

    .pet-photo-edit-btn {
        background-color: transparent;
        color: #3B3731;
        border: .0625rem solid #D4D4D4;
        height: 3rem;
        padding: 0 1.25rem;
        border-radius: 6rem;
        cursor: pointer;
        font-family: Lato;
        font-size: .875rem;
        font-weight: 600;
        white-space: nowrap;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
    }

    .pet-photo-save-btn {
        background-color: transparent;
        border: .0625rem solid #FFC97A;
        color: #FFC97A;
        height: 3rem;
        padding: 0 1.25rem;
        border-radius: 6rem;
        cursor: pointer;
        font-family: Lato;
        font-size: .875rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .pet-photo-edit-btn,
    .pet-photo-save-btn {
        padding: .625rem .9375rem;
        border: none;
        border-radius: 6rem;
        cursor: pointer;
        font-family: Lato;
        font-size: .875rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .pet-photo-edit-btn {
        background-color: transparent;
        color: #3B3731;
        border: .0625rem solid #D4D4D4;
        width: 8.6875rem;
        height: 3rem;

    }

    .pet-photo-edit-btn:hover {
        background-color: #E8E7E6;
        border-color: #C0BFBE;
    }

    .pet-photo-save-btn {
        background-color: transparent;
        border: .0625rem solid #FFC97A;
        color: #FFC97A;
        width: 7.5rem;
        height: 3rem;
    }

    .pet-photo-save-btn:hover {
        background-color: #FFB85C;
        color: #FFF;
    }

    .dot {
        width: .375rem;
        height: .375rem;
        background-color: #3B3731;
        border-radius: 50%;
        display: inline-block;
        margin: 0 .25rem;
    }

    .confirm-pay {
        max-width: 100%;
        margin: 0 auto;
    }

    .confirm-pay form {
        width: 100%;
    }

    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.125rem 1.5rem;
        align-items: start;
    }

    .full-row {
        grid-column: 1 / -1;
    }


    .form-grid>div>label {
        color: #3B3731;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-top: .625rem;
    }

    .form-grid>div>label>span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .input-wrap {
        position: relative;
        margin: .5rem 0;
    }

    .input-wrap>input,
    .input-wrap>select {
        border-radius: .625rem !important;
        border: .0625rem solid #D4D4D4 !important;
        background: #FFF !important;
    }

    .input-wrap input[type="text"],
    .input-wrap input[type="tel"],
    .input-wrap input[type="email"],
    .input-wrap select,
    .input-wrap textarea {
        width: 100%;
        box-sizing: border-box;
        padding: .75rem 2.75rem .75rem .875rem;
        border-radius: .5rem;
        border: .0625rem solid #eee6dd;
        background: #FFF;
        font-size: .875rem;
        outline: none;
        transition: border-color .15s, box-shadow .12s;
    }

    .input-wrap select {
        -webkit-appearance: none;
        appearance: none;
        padding-right: 2.75rem;
        background-image: none;
        cursor: pointer;
    }

    .input-wrap.select-wrap {
        position: relative;
    }


    .input-wrap.select-wrap.open::after {
        transform: translateY(-50%) rotate(180deg);
    }



    .input-wrap select {
        -webkit-appearance: none;
        appearance: none;
        padding-right: 2.75rem;
        background-color: #FFF;
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="15" height="8" viewBox="0 0 15 8" fill="none"><path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="%233B3731" stroke-linecap="round" stroke-linejoin="round"/></svg>');
        background-repeat: no-repeat;
        background-position: right .875rem center;
        background-size: .9375rem .5rem;
        cursor: pointer;
    }

    .input-wrap textarea {
        min-height: 2.375rem;
        resize: vertical;
    }

    .input-wrap input:focus,
    .input-wrap select:focus,
    .input-wrap textarea:focus {
        border-color: #e6dccd;
        box-shadow: 0 0 0 .1875rem rgba(201, 221, 160, 0.12);
    }

    /* check icon container */
    .icon {
        position: absolute;
        right: .625rem;
        top: 50%;
        transform: translateY(-50%);
        width: 1.375rem;
        height: 1.375rem;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        opacity: 0;
        transition: opacity .12s, transform .12s;
    }

    .input-wrap.valid .icon {
        opacity: 1;
        transform: translateY(-50%) scale(1);
    }

    /* red state */
    .input-wrap.error input,
    .input-wrap.error textarea,
    .input-wrap.error select {
        border-color: rgba(255, 107, 107, 0.22);
        box-shadow: 0 0 0 .1875rem rgba(255, 107, 107, 0.06);
    }

    .error-msg {
        color: #FF6E6E;
        font-size: .8125rem;
        margin-top: .375rem;
        font-family: Lato;
        display: none;
    }

    .input-wrap.error+.error-msg {
        display: block;
    }

    .buttons {
        display: flex;
        justify-content: flex-end;
        gap: .75rem;
        margin-top: 1.125rem;
    }

    .btn {
        padding: .625rem 1.125rem;
        border-radius: 1.625rem;
        border: .0625rem solid #f0d5c0;
        background: transparent;
        cursor: pointer;
        font-weight: 600;
    }

    .btn.cancel {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-decoration-line: underline;
        text-decoration-style: solid;
        text-decoration-skip-ink: auto;
        text-decoration-thickness: auto;
        text-underline-offset: auto;
        text-underline-position: from-font;
        border: none;
    }

    .btn.save {
        width: 7.5rem;
        height: 3rem;
        border-radius: 6rem;
        border: .0625rem solid #FFC97A;
        background: #FFF;
        box-shadow: 0 .3125rem .5rem 0 rgba(0, 0, 0, 0.05);
        color: #FFC97A;
        text-align: center;
        font-family: Lato;
        font-size: 1rem;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }
</style>
@endpush

@section('content')
<section>
    <div class="container">
        <div class="d-flex align-items-end justify-content-between">
            <!-- Back button -->
            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 32px;">
                <button id="backToStep1Btn"
                    style="display: flex; align-items: center; gap: .5rem; background: transparent; border: none; cursor: pointer; font-family: Lato; font-size: 1rem; font-weight: 600; color: #3B3731; text-decoration: underline;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48" fill="none">
                        <rect width="48" height="48" rx="24" transform="matrix(-1 0 0 1 48 0)" fill="#EAE8E5" />
                        <path
                            d="M15.2902 23.1723C14.8946 23.5641 14.8952 24.2036 15.2915 24.5947L19.8487 29.0918C20.204 29.4424 20.7767 29.4378 21.1263 29.0815C21.2649 28.9448 21.3203 28.8081 21.2926 28.6714C21.2579 28.5347 21.1818 28.4049 21.064 28.2819L18.0826 25.3603C17.9164 25.1963 17.7606 25.0493 17.6152 24.9195C17.5 24.8167 17.5887 24.6111 17.7423 24.6266C17.9201 24.6446 18.1028 24.6603 18.2904 24.6735C18.6713 24.7008 19.066 24.7145 19.4746 24.7145H32.1645C32.6259 24.7145 33 24.3404 33 23.879C33 23.4176 32.6259 23.0435 32.1645 23.0435H19.4746C19.066 23.0435 18.6678 23.0572 18.28 23.0846C18.0952 23.0976 17.9151 23.1129 17.7398 23.1306C17.584 23.1463 17.496 22.9401 17.6152 22.8385C17.7606 22.7087 17.9164 22.5617 18.0826 22.3977L21.0848 19.4557C21.2095 19.3326 21.2856 19.2028 21.3133 19.0661C21.341 18.9294 21.2891 18.7928 21.1575 18.6561C20.8023 18.2942 20.2205 18.2901 19.8602 18.6469L15.2902 23.1723Z"
                            fill="#3B3731" />
                    </svg>
                </button>
            </div>
            <div class="steps-and-btn w-15">
                <div class="active-inactive-div d-flex align-items-center justify-content-center">
                    <div class="step-indicator">
                        <div class="steps-container">
                            <div class="step-item active">
                                <div class="step-circle">1</div>
                                <div class="step-label">Groomer Details</div>
                            </div>
                            <div class="step-item inactive">
                                <div class="step-circle">2</div>
                                <div class="step-label">Confirm & Pay</div>
                            </div>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: 50%;"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div>

            </div>
        </div>
        <div class="booking-details">
            <div class="row">
                <div class="col-lg-8">
                    <div id="step1Content">
                        <div>
                            <h1>Booking Details</h1>
                        </div>
                        <div class="card">
                            <div class="clip-path">
                                <div class="svg-icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="12" viewBox="0 0 11 12"
                                        fill="none">
                                        <rect x="2.71265" y="2.71231" width="6.41005" height="7.12228" rx="3"
                                            fill="white" />
                                        <path
                                            d="M5.80708 0.0683425C5.71083 0.0235664 5.60771 0 5.5 0C5.39229 0 5.28917 0.0235664 5.19292 0.0683425L0.877717 1.9513C0.373551 2.17046 -0.00228124 2.68185 1.04236e-05 3.29929C0.0114687 5.63708 0.946467 9.91438 4.895 11.8586C5.27771 12.0471 5.72229 12.0471 6.105 11.8586C10.0535 9.91438 10.9885 5.63708 11 3.29929C11.0023 2.68185 10.6264 2.17046 10.1223 1.9513L5.80708 0.0683425ZM3.32063 6.7447C3.43063 6.77298 3.5475 6.78712 3.66667 6.78712C4.47563 6.78712 5.13333 6.11076 5.13333 5.27887V3.77062H6.14625C6.42354 3.77062 6.67791 3.93087 6.80166 4.18775L6.96666 4.52474H8.43333C8.63499 4.52474 8.79999 4.69442 8.79999 4.90181V5.65593C8.79999 6.69756 7.97958 7.54124 6.96666 7.54124H5.86667V8.73606C5.86667 8.90809 5.73146 9.04949 5.56187 9.04949C5.52063 9.04949 5.47937 9.04006 5.44271 9.02357L3.18084 8.02671C3.02959 7.96072 2.93334 7.80754 2.93334 7.64022C2.93334 7.57423 2.94709 7.5106 2.97688 7.45169L3.32063 6.7447ZM3.3 3.77062H4.4V5.27887C4.4 5.69599 4.07229 6.03299 3.66667 6.03299C3.26105 6.03299 2.93334 5.69599 2.93334 5.27887V4.14768C2.93334 3.9403 3.09834 3.77062 3.3 3.77062ZM6.23333 4.90181C6.23333 4.8018 6.1947 4.7059 6.12594 4.63518C6.05717 4.56447 5.96391 4.52474 5.86667 4.52474C5.76942 4.52474 5.67616 4.56447 5.60739 4.63518C5.53863 4.7059 5.5 4.8018 5.5 4.90181C5.5 5.00181 5.53863 5.09772 5.60739 5.16843C5.67616 5.23914 5.76942 5.27887 5.86667 5.27887C5.96391 5.27887 6.05717 5.23914 6.12594 5.16843C6.1947 5.09772 6.23333 5.00181 6.23333 4.90181Z"
                                            fill="#C9DDA0" />
                                    </svg>
                                </div>
                                <div>
                                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                        width="93" height="136" viewBox="0 0 93 136" fill="none">
                                        <path
                                            d="M87.2119 0C89.9733 0 92.2119 2.23858 92.2119 5V131C92.2119 133.761 89.9733 136 87.2119 136H5C2.23858 136 8.33008e-06 133.761 0 131V21.4844C0 18.723 2.23858 16.4844 5 16.4844H11.4844C14.2458 16.4844 16.4844 14.2458 16.4844 11.4844V5C16.4844 2.23858 18.723 0 21.4844 0H87.2119Z"
                                            fill="url(#pattern0_50_500)" />
                                        <defs>
                                            <clipPath id="shape-clip">
                                                <path
                                                    d="M87.2119 0C89.9733 0 92.2119 2.23858 92.2119 5V131C92.2119 133.761 89.9733 136 87.2119 136H5C2.23858 136 8.33008e-06 133.761 0 131V21.4844C0 18.723 2.23858 16.4844 5 16.4844H11.4844C14.2458 16.4844 16.4844 14.2458 16.4844 11.4844V5C16.4844 2.23858 18.723 0 21.4844 0H87.2119Z" />
                                            </clipPath>
                                        </defs>
                                        <image href="{{ asset('images/card1.png') }}" x="0" y="0" width="93"
                                            height="136" preserveAspectRatio="xMidYMid slice"
                                            clip-path="url(#shape-clip)" />
                                    </svg>
                                </div>
                            </div>
                            <div class="card-body">
                                <div>
                                    <div>
                                        <p>Sarah’s Grooming Studio</p>
                                        <span>Sarah W.</span>
                                        <div>
                                            <p>Home Visits</p>
                                        </div>
                                    </div>
                                    <div>
                                        <div>
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9"
                                                    viewBox="0 0 10 9" fill="none">
                                                    <path
                                                        d="M2 8.99999C1.85833 8.99999 1.73967 8.95199 1.644 8.85599C1.54833 8.75999 1.50033 8.64133 1.5 8.49999C1.49967 8.35866 1.54767 8.23999 1.644 8.14399C1.74033 8.04799 1.859 7.99999 2 7.99999H8C8.14166 7.99999 8.2605 8.04799 8.3565 8.14399C8.4525 8.23999 8.50033 8.35866 8.5 8.49999C8.49966 8.64133 8.45166 8.76016 8.356 8.85649C8.26033 8.95283 8.14166 9.00066 8 8.99999H2ZM2.35 7.24999C2.10833 7.24999 1.89383 7.17083 1.7065 7.0125C1.51917 6.85416 1.4045 6.65416 1.3625 6.4125L0.862501 3.2375C0.845834 3.2375 0.827167 3.23967 0.806501 3.244C0.785834 3.24833 0.767001 3.25033 0.750001 3.25C0.541667 3.25 0.364668 3.17717 0.219001 3.0315C0.0733344 2.88583 0.000334469 2.70867 1.13636e-06 2.5C-0.000332197 2.29133 0.0726677 2.11433 0.219001 1.969C0.365334 1.82367 0.542334 1.75067 0.750001 1.75C0.957667 1.74933 1.13483 1.82233 1.2815 1.969C1.42817 2.11567 1.501 2.29267 1.5 2.5C1.5 2.55833 1.49367 2.6125 1.481 2.6625C1.46833 2.7125 1.45383 2.75833 1.4375 2.8L3 3.5L4.5625 1.3625C4.47083 1.29583 4.39583 1.20833 4.3375 1.1C4.27917 0.991667 4.25 0.875 4.25 0.75C4.25 0.541667 4.323 0.364501 4.469 0.218501C4.615 0.0725011 4.792 -0.000332194 5 1.13895e-06C5.208 0.000334472 5.38516 0.0733344 5.5315 0.219001C5.67783 0.364667 5.75066 0.541667 5.75 0.75C5.75 0.875 5.72083 0.991667 5.6625 1.1C5.60416 1.20833 5.52916 1.29583 5.4375 1.3625L7 3.5L8.5625 2.8C8.54583 2.75833 8.53116 2.7125 8.5185 2.6625C8.50583 2.6125 8.49966 2.55833 8.5 2.5C8.5 2.29167 8.573 2.1145 8.719 1.9685C8.865 1.8225 9.042 1.74967 9.25 1.75C9.458 1.75033 9.63516 1.82333 9.7815 1.969C9.92783 2.11467 10.0007 2.29167 10 2.5C9.99933 2.70833 9.92649 2.8855 9.7815 3.0315C9.6365 3.1775 9.45933 3.25033 9.25 3.25C9.23333 3.25 9.21466 3.248 9.194 3.244C9.17333 3.24 9.1545 3.23783 9.1375 3.2375L8.6375 6.4125C8.59583 6.65416 8.48133 6.85416 8.294 7.0125C8.10666 7.17083 7.892 7.24999 7.65 7.24999H2.35ZM2.35 6.25H7.65L7.975 4.1625L7.4 4.4125C7.18333 4.50416 6.9625 4.52083 6.7375 4.4625C6.5125 4.40416 6.32916 4.27916 6.1875 4.0875L5 2.45L3.8125 4.0875C3.67083 4.27916 3.4875 4.40416 3.2625 4.4625C3.0375 4.52083 2.81667 4.50416 2.6 4.4125L2.025 4.1625L2.35 6.25Z"
                                                        fill="white" />
                                                </svg>
                                                <p>Popular</p>
                                            </div>
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="11"
                                                    viewBox="0 0 9 11" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M6.41298 7.89331C6.44191 7.34386 6.31555 6.79737 6.04836 6.31639C5.78117 5.83541 5.38396 5.43938 4.90218 5.17363C4.81663 5.12414 4.7152 5.1098 4.61928 5.13363C4.52336 5.15746 4.44044 5.2176 4.38799 5.30138L3.77528 6.28209L3.26554 5.66557C3.23754 5.63178 3.20283 5.60415 3.16362 5.58443C3.12441 5.56471 3.08154 5.55333 3.03771 5.551C2.99388 5.54867 2.95005 5.55545 2.90896 5.5709C2.86788 5.58635 2.83045 5.61015 2.79902 5.64078C2.49895 5.93115 2.26456 6.28243 2.11162 6.67097C1.95868 7.05952 1.89074 7.47631 1.91237 7.89331C1.91237 8.49004 2.14943 9.06234 2.57138 9.48429C2.99333 9.90624 3.56563 10.1433 4.16236 10.1433C4.75909 10.1433 5.33138 9.90624 5.75334 9.48429C6.17529 9.06234 6.41234 8.49004 6.41234 7.89331M3.01004 6.35519L2.97063 6.4073C2.67173 6.82484 2.52196 7.33078 2.54542 7.84374L2.54733 7.88124C2.54733 8.3094 2.71741 8.72003 3.02017 9.02279C3.32293 9.32554 3.73356 9.49563 4.16172 9.49563C4.58989 9.49563 5.00052 9.32554 5.30327 9.02279C5.60603 8.72003 5.77612 8.3094 5.77612 7.88124L5.77803 7.84437C5.78247 7.80306 5.88163 6.66662 4.8418 5.89375L4.79032 5.85625L4.12677 6.91768C4.09473 6.96887 4.05098 7.01172 3.99915 7.0427C3.94731 7.07368 3.88885 7.09191 3.82859 7.09588C3.76833 7.09985 3.70799 7.08946 3.65253 7.06555C3.59708 7.04164 3.54809 7.0049 3.50961 6.95836L3.01004 6.35519Z"
                                                        fill="#FEFEFE" />
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M3.79701 0.30821C3.81025 0.23997 3.84191 0.176658 3.88858 0.125138C3.93524 0.0736173 3.99512 0.0358558 4.06172 0.0159479C4.12832 -0.00396001 4.1991 -0.00525449 4.26638 0.0122049C4.33367 0.0296642 4.39489 0.0652111 4.4434 0.114991C4.56671 0.240837 4.80378 0.489988 5.04467 0.777274C5.28111 1.05884 5.53916 1.39761 5.68788 1.69951C5.8328 1.99443 5.98661 2.37578 6.10801 2.69421L6.67305 1.75354C6.70456 1.701 6.74826 1.65683 6.80046 1.62476C6.85265 1.59269 6.91181 1.57367 6.97292 1.56931C7.03402 1.56494 7.09528 1.57536 7.1515 1.59969C7.20773 1.62401 7.25727 1.66152 7.29592 1.70905C8.09867 2.70057 8.49846 3.76263 8.6974 4.57365C8.79718 4.97979 8.8474 5.32491 8.87282 5.57025C8.88576 5.69278 8.89424 5.81574 8.89824 5.93889V5.97131C8.89824 8.4482 6.93364 10.4649 4.44785 10.4649C1.96206 10.4649 0 8.44756 0 5.97004C0 5.28805 0.322244 3.68192 1.27563 2.36498C1.31266 2.31422 1.36166 2.27341 1.41826 2.24615C1.47487 2.2189 1.53734 2.20605 1.60011 2.20876C1.66287 2.21146 1.724 2.22963 1.77806 2.26166C1.83211 2.29368 1.87741 2.33856 1.90994 2.39231L2.55507 3.46709C2.75083 3.16073 3.01269 2.73044 3.21163 2.3332C3.49765 1.76117 3.72455 0.682572 3.79701 0.308845M4.3201 0.912655C4.20506 1.42113 4.01501 2.14697 3.77985 2.61858C3.46714 3.24336 3.0165 3.9298 2.86142 4.16051C2.82554 4.21345 2.77693 4.25651 2.72005 4.28574C2.66317 4.31497 2.59986 4.32943 2.53593 4.32778C2.472 4.32614 2.40952 4.30844 2.35422 4.27632C2.29892 4.24421 2.25258 4.1987 2.21948 4.14399L1.57118 3.06476C0.87457 4.19166 0.635589 5.45839 0.635589 5.97131C0.635589 8.10561 2.32244 9.82806 4.44785 9.82806C6.57326 9.82806 8.26265 8.10561 8.26265 5.97131V5.95351L8.26011 5.88995C8.25568 5.80484 8.24911 5.71986 8.24041 5.63508C8.20712 5.3287 8.15362 5.02487 8.08024 4.72555C7.87826 3.89157 7.52027 3.10334 7.02516 2.40248L6.38068 3.47535C6.34338 3.53732 6.28925 3.58743 6.22459 3.61985C6.15993 3.65227 6.08739 3.66566 6.01542 3.65847C5.94344 3.65128 5.87499 3.6238 5.81802 3.57923C5.76105 3.53466 5.71791 3.47483 5.6936 3.40671C5.59064 3.11815 5.33831 2.42917 5.11713 1.98044C5.00463 1.751 4.78916 1.46117 4.55781 1.18596C4.4801 1.09353 4.40086 1.00242 4.3201 0.912655Z"
                                                        fill="#FEFEFE" />
                                                </svg>
                                                <p>Top Rated</p>
                                            </div>
                                        </div>
                                        <div>
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="14"
                                                    viewBox="0 0 10 14" fill="none">
                                                    <path
                                                        d="M5 6.65C4.5264 6.65 4.0722 6.46563 3.73731 6.13744C3.40242 5.80925 3.21429 5.36413 3.21429 4.9C3.21429 4.43587 3.40242 3.99075 3.73731 3.66256C4.0722 3.33437 4.5264 3.15 5 3.15C5.4736 3.15 5.9278 3.33437 6.26269 3.66256C6.59758 3.99075 6.78571 4.43587 6.78571 4.9C6.78571 5.12981 6.73953 5.35738 6.64979 5.5697C6.56004 5.78202 6.42851 5.97493 6.26269 6.13744C6.09687 6.29994 5.90002 6.42884 5.68336 6.51679C5.46671 6.60473 5.2345 6.65 5 6.65ZM5 0C3.67392 0 2.40215 0.516248 1.46447 1.43518C0.526784 2.3541 0 3.60044 0 4.9C0 8.575 5 14 5 14C5 14 10 8.575 10 4.9C10 3.60044 9.47322 2.3541 8.53553 1.43518C7.59785 0.516248 6.32608 0 5 0Z"
                                                        fill="#FFC97A" />
                                                </svg>
                                                <p>2.5 mi</p>
                                            </div>
                                            <div>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14"
                                                    viewBox="0 0 14 14" fill="none">
                                                    <path
                                                        d="M6.12956 0.660476C6.40354 -0.220161 7.59647 -0.220158 7.87045 0.660479L8.89548 3.95519C9.01801 4.34902 9.36942 4.61566 9.76593 4.61566H13.083C13.9696 4.61566 14.3383 5.80055 13.621 6.34481L10.9374 8.38106C10.6166 8.62446 10.4824 9.0559 10.6049 9.44973L11.63 12.7444C11.9039 13.6251 10.9388 14.3574 10.2215 13.8131L7.53797 11.7769C7.21719 11.5335 6.78282 11.5335 6.46204 11.7769L3.77846 13.8131C3.06117 14.3574 2.09607 13.6251 2.37005 12.7444L3.39508 9.44973C3.51761 9.0559 3.38338 8.62446 3.0626 8.38106L0.37903 6.34481C-0.338258 5.80055 0.0303816 4.61566 0.916998 4.61566H4.23408C4.63058 4.61566 4.98199 4.34902 5.10452 3.95519L6.12956 0.660476Z"
                                                        fill="#FFC97A" />
                                                </svg>
                                                <p>4.3</p>
                                                <span>
                                                    (20 reviews)
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body-info">
                                    <div>
                                        <span><svg xmlns="http://www.w3.org/2000/svg" width="14" height="15"
                                                viewBox="0 0 14 15" fill="none">
                                                <path
                                                    d="M4.38022 10.1726C5.5048 11.2972 8.23992 10.3857 10.4891 8.13624C12.7386 5.88709 13.65 3.15197 12.5255 2.02739M7.68915 1.26352L8.19816 1.77289M5.90761 3.04541L6.41662 3.55442M4.37986 5.08182L4.88887 5.59083M3.87085 7.62723L4.37986 8.13624M10.4891 0.5L10.9981 1.00901M9.98006 3.55478L10.9981 4.5728M8.19852 5.33668L9.21654 6.3547M6.16212 6.86371L7.18014 7.88173"
                                                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                                <path
                                                    d="M4.37996 11.7C4.80171 11.2783 4.80171 10.5945 4.37996 10.1727C3.95822 9.75101 3.27444 9.75101 2.8527 10.1728L0.816351 12.2091C0.394609 12.6308 0.39461 13.3146 0.816351 13.7364C1.23809 14.1581 1.92187 14.1581 2.34361 13.7364L4.37996 11.7Z"
                                                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Service</span>
                                        <p>Full Groom</p>
                                    </div>
                                    <div>
                                        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                viewBox="0 0 16 15" fill="none">
                                                <path
                                                    d="M0.5 7.32052C0.5 4.61314 0.5 3.25909 1.379 2.41837C2.258 1.57765 3.67175 1.57693 6.5 1.57693H9.5C12.3282 1.57693 13.7427 1.57693 14.621 2.41837C15.4992 3.2598 15.5 4.61314 15.5 7.32052V8.75641C15.5 11.4638 15.5 12.8178 14.621 13.6586C13.742 14.4993 12.3282 14.5 9.5 14.5H6.5C3.67175 14.5 2.25725 14.5 1.379 13.6586C0.50075 12.8171 0.5 11.4638 0.5 8.75641V7.32052Z"
                                                    stroke="#9D9B98" />
                                                <path d="M4.25 1.57692V0.5M11.75 1.57692V0.5M0.875 5.16666H15.125"
                                                    stroke="#9D9B98" stroke-linecap="round" />
                                                <path
                                                    d="M12.5 10.9103C12.5 11.1007 12.421 11.2833 12.2803 11.418C12.1397 11.5526 11.9489 11.6282 11.75 11.6282C11.5511 11.6282 11.3603 11.5526 11.2197 11.418C11.079 11.2833 11 11.1007 11 10.9103C11 10.7199 11.079 10.5373 11.2197 10.4026C11.3603 10.268 11.5511 10.1923 11.75 10.1923C11.9489 10.1923 12.1397 10.268 12.2803 10.4026C12.421 10.5373 12.5 10.7199 12.5 10.9103ZM12.5 8.0385C12.5 8.22892 12.421 8.41153 12.2803 8.54617C12.1397 8.68081 11.9489 8.75645 11.75 8.75645C11.5511 8.75645 11.3603 8.68081 11.2197 8.54617C11.079 8.41153 11 8.22892 11 8.0385C11 7.84809 11.079 7.66548 11.2197 7.53084C11.3603 7.3962 11.5511 7.32056 11.75 7.32056C11.9489 7.32056 12.1397 7.3962 12.2803 7.53084C12.421 7.66548 12.5 7.84809 12.5 8.0385ZM8.75 10.9103C8.75 11.1007 8.67098 11.2833 8.53033 11.418C8.38968 11.5526 8.19891 11.6282 8 11.6282C7.80109 11.6282 7.61032 11.5526 7.46967 11.418C7.32902 11.2833 7.25 11.1007 7.25 10.9103C7.25 10.7199 7.32902 10.5373 7.46967 10.4026C7.61032 10.268 7.80109 10.1923 8 10.1923C8.19891 10.1923 8.38968 10.268 8.53033 10.4026C8.67098 10.5373 8.75 10.7199 8.75 10.9103ZM8.75 8.0385C8.75 8.22892 8.67098 8.41153 8.53033 8.54617C8.38968 8.68081 8.19891 8.75645 8 8.75645C7.80109 8.75645 7.61032 8.68081 7.46967 8.54617C7.32902 8.41153 7.25 8.22892 7.25 8.0385C7.25 7.84809 7.32902 7.66548 7.46967 7.53084C7.61032 7.3962 7.80109 7.32056 8 7.32056C8.19891 7.32056 8.38968 7.3962 8.53033 7.53084C8.67098 7.66548 8.75 7.84809 8.75 8.0385ZM5 10.9103C5 11.1007 4.92098 11.2833 4.78033 11.418C4.63968 11.5526 4.44891 11.6282 4.25 11.6282C4.05109 11.6282 3.86032 11.5526 3.71967 11.418C3.57902 11.2833 3.5 11.1007 3.5 10.9103C3.5 10.7199 3.57902 10.5373 3.71967 10.4026C3.86032 10.268 4.05109 10.1923 4.25 10.1923C4.44891 10.1923 4.63968 10.268 4.78033 10.4026C4.92098 10.5373 5 10.7199 5 10.9103ZM5 8.0385C5 8.22892 4.92098 8.41153 4.78033 8.54617C4.63968 8.68081 4.44891 8.75645 4.25 8.75645C4.05109 8.75645 3.86032 8.68081 3.71967 8.54617C3.57902 8.41153 3.5 8.22892 3.5 8.0385C3.5 7.84809 3.57902 7.66548 3.71967 7.53084C3.86032 7.3962 4.05109 7.32056 4.25 7.32056C4.44891 7.32056 4.63968 7.3962 4.78033 7.53084C4.92098 7.66548 5 7.84809 5 8.0385Z"
                                                    fill="#9D9B98" />
                                            </svg>Date</span>
                                        <p>18/12/2025</p>
                                    </div>
                                    <div>
                                        <span>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                viewBox="0 0 48 48">
                                                <path
                                                    d="M 24 4 C 12.972066 4 4 12.972074 4 24 C 4 35.027926 12.972066 44 24 44 C 35.027934 44 44 35.027926 44 24 C 44 12.972074 35.027934 4 24 4 z
                                                                                                                                               M 24 7 C 33.406615 7 41 14.593391 41 24 C 41 33.406609 33.406615 41 24 41 C 14.593385 41 7 33.406609 7 24 C 7 14.593391 14.593385 7 24 7 z
                                                                                                                                               M 22.476562 11.978516 A 1.50015 1.50015 0 0 0 21 13.5 L 21 24.5 A 1.50015 1.50015 0 0 0 21.439453 25.560547 L 26.439453 30.560547 A 1.50015 1.50015 0 1 0 28.560547 28.439453 L 24 23.878906 L 24 13.5 A 1.50015 1.50015 0 0 0 22.476562 11.978516 z"
                                                    fill="#9D9B98" />
                                            </svg>
                                            Time</span>
                                        <p>14:30 - 15:30 (90 mins)</p>
                                    </div>
                                    <div>
                                        <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                viewBox="0 0 16 15" fill="none">
                                                <path
                                                    d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>Pet</span>
                                        <p> Other • Medium</p>
                                    </div>
                                    <div>
                                        <button>
                                            Change
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('pet-details.store') }}" class="pet-details-form" id="petDetailsForm">
                            @csrf
                            <input type="hidden" name="photo" id="photoBase64" value="{{ $petDetails->photo ?? '' }}">
                            <div>
                                <h2>Pet Details</h2>
                            </div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="85" height="85" viewBox="0 0 85 85"
                                    fill="none" id="petPhotoPlaceholder">
                                    <mask id="path-1-inside-1_85_569" fill="white">
                                        <path
                                            d="M43.5 0C66.4198 0 85 18.5802 85 41.5V43.5C85 66.4198 66.4198 85 43.5 85H41.5C18.5802 85 0 66.4198 0 43.5V41.5C0 18.5802 18.5802 0 41.5 0H43.5ZM43 39.3184C39.2287 39.3184 36.4346 41.367 34.6494 43.9092C32.8863 46.4133 32 49.5227 32 52.0459C32.0002 54.9857 33.7446 57.0319 35.8848 58.2568C37.9904 59.4658 40.6304 60 43 60C45.3696 60 48.0096 59.469 50.1152 58.2568C52.2523 57.0287 53.9998 54.9857 54 52.0459C54 49.5227 53.1137 46.4133 51.3506 43.9092C49.5686 41.3638 46.7745 39.3184 43 39.3184ZM25.7139 39.3184C24.1584 39.3185 22.9388 40.2763 22.1846 41.4248C21.421 42.5829 21 44.0941 21 45.6816C21 47.2694 21.4209 48.7813 22.1846 49.9395C22.9388 51.0848 24.1584 52.0457 25.7139 52.0459C27.2696 52.0459 28.4899 51.0881 29.2441 49.9395C30.0079 48.7813 30.4287 47.2694 30.4287 45.6816C30.4287 44.0942 30.0077 42.5829 29.2441 41.4248C28.4899 40.2794 27.2696 39.3184 25.7139 39.3184ZM60.2861 39.3184C58.7304 39.3184 57.5101 40.2762 56.7559 41.4248C55.9923 42.5829 55.5713 44.0942 55.5713 45.6816C55.5713 47.2694 55.9921 48.7813 56.7559 49.9395C57.5102 51.0849 58.7304 52.0459 60.2861 52.0459C61.8416 52.0457 63.0612 51.0879 63.8154 49.9395C64.5791 48.7813 65 47.2694 65 45.6816C65 44.0941 64.579 42.5829 63.8154 41.4248C63.0612 40.2795 61.8416 39.3185 60.2861 39.3184ZM35.1426 25C33.587 25.0001 32.3675 25.9579 31.6133 27.1064C30.8497 28.2645 30.4288 29.7757 30.4287 31.3633C30.4287 32.951 30.8496 34.4629 31.6133 35.6211C32.3675 36.7665 33.587 37.7274 35.1426 37.7275C36.6982 37.7275 37.9176 36.7696 38.6719 35.6211C39.4356 34.4629 39.8574 32.951 39.8574 31.3633C39.8574 29.7757 39.4355 28.2645 38.6719 27.1064C37.9176 25.961 36.6982 25 35.1426 25ZM50.8574 25C49.3018 25 48.0824 25.9579 47.3281 27.1064C46.5645 28.2645 46.1426 29.7757 46.1426 31.3633C46.1426 32.951 46.5644 34.4629 47.3281 35.6211C48.0824 36.7664 49.3019 37.7275 50.8574 37.7275C52.413 37.7274 53.6325 36.7696 54.3867 35.6211C55.1504 34.4629 55.5713 32.951 55.5713 31.3633C55.5712 29.7757 55.1503 28.2645 54.3867 27.1064C53.6325 25.9611 52.413 25.0001 50.8574 25Z" />
                                    </mask>
                                    <path
                                        d="M43.5 0C66.4198 0 85 18.5802 85 41.5V43.5C85 66.4198 66.4198 85 43.5 85H41.5C18.5802 85 0 66.4198 0 43.5V41.5C0 18.5802 18.5802 0 41.5 0H43.5ZM43 39.3184C39.2287 39.3184 36.4346 41.367 34.6494 43.9092C32.8863 46.4133 32 49.5227 32 52.0459C32.0002 54.9857 33.7446 57.0319 35.8848 58.2568C37.9904 59.4658 40.6304 60 43 60C45.3696 60 48.0096 59.469 50.1152 58.2568C52.2523 57.0287 53.9998 54.9857 54 52.0459C54 49.5227 53.1137 46.4133 51.3506 43.9092C49.5686 41.3638 46.7745 39.3184 43 39.3184ZM25.7139 39.3184C24.1584 39.3185 22.9388 40.2763 22.1846 41.4248C21.421 42.5829 21 44.0941 21 45.6816C21 47.2694 21.4209 48.7813 22.1846 49.9395C22.9388 51.0848 24.1584 52.0457 25.7139 52.0459C27.2696 52.0459 28.4899 51.0881 29.2441 49.9395C30.0079 48.7813 30.4287 47.2694 30.4287 45.6816C30.4287 44.0942 30.0077 42.5829 29.2441 41.4248C28.4899 40.2794 27.2696 39.3184 25.7139 39.3184ZM60.2861 39.3184C58.7304 39.3184 57.5101 40.2762 56.7559 41.4248C55.9923 42.5829 55.5713 44.0942 55.5713 45.6816C55.5713 47.2694 55.9921 48.7813 56.7559 49.9395C57.5102 51.0849 58.7304 52.0459 60.2861 52.0459C61.8416 52.0457 63.0612 51.0879 63.8154 49.9395C64.5791 48.7813 65 47.2694 65 45.6816C65 44.0941 64.579 42.5829 63.8154 41.4248C63.0612 40.2795 61.8416 39.3185 60.2861 39.3184ZM35.1426 25C33.587 25.0001 32.3675 25.9579 31.6133 27.1064C30.8497 28.2645 30.4288 29.7757 30.4287 31.3633C30.4287 32.951 30.8496 34.4629 31.6133 35.6211C32.3675 36.7665 33.587 37.7274 35.1426 37.7275C36.6982 37.7275 37.9176 36.7696 38.6719 35.6211C39.4356 34.4629 39.8574 32.951 39.8574 31.3633C39.8574 29.7757 39.4355 28.2645 38.6719 27.1064C37.9176 25.961 36.6982 25 35.1426 25ZM50.8574 25C49.3018 25 48.0824 25.9579 47.3281 27.1064C46.5645 28.2645 46.1426 29.7757 46.1426 31.3633C46.1426 32.951 46.5644 34.4629 47.3281 35.6211C48.0824 36.7664 49.3019 37.7275 50.8574 37.7275C52.413 37.7274 53.6325 36.7696 54.3867 35.6211C55.1504 34.4629 55.5713 32.951 55.5713 31.3633C55.5712 29.7757 55.1503 28.2645 54.3867 27.1064C53.6325 25.9611 52.413 25.0001 50.8574 25Z"
                                        fill="#D4D4D4" />
                                    <path
                                        d="M43.5 0V-1V0ZM43.5 85V86V85ZM34.6494 43.9092L35.4671 44.4849L35.4678 44.4839L34.6494 43.9092ZM32 52.0459H31V52.046L32 52.0459ZM35.8848 58.2568L36.3827 57.3896L36.3815 57.3889L35.8848 58.2568ZM50.1152 58.2568L49.617 57.3898L49.6163 57.3902L50.1152 58.2568ZM54 52.0459L55 52.046V52.0459H54ZM51.3506 43.9092L50.5314 44.4827L50.5329 44.4849L51.3506 43.9092ZM25.7139 39.3184V38.3184H25.7138L25.7139 39.3184ZM22.1846 41.4248L23.0194 41.9753L23.0205 41.9737L22.1846 41.4248ZM21 45.6816L20 45.6816V45.6816H21ZM22.1846 49.9395L23.0197 49.3895L23.0194 49.389L22.1846 49.9395ZM25.7139 52.0459L25.7138 53.0459H25.7139V52.0459ZM29.2441 49.9395L28.4093 49.389L28.4083 49.3905L29.2441 49.9395ZM30.4287 45.6816H31.4287V45.6816L30.4287 45.6816ZM29.2441 41.4248L28.409 41.9748L28.4093 41.9752L29.2441 41.4248ZM60.2861 39.3184L60.2862 38.3184H60.2861V39.3184ZM56.7559 41.4248L57.5907 41.9752L57.5917 41.9737L56.7559 41.4248ZM55.5713 45.6816L54.5713 45.6816V45.6816H55.5713ZM56.7559 49.9395L57.591 49.3895L57.5907 49.389L56.7559 49.9395ZM60.2861 52.0459V53.0459H60.2862L60.2861 52.0459ZM63.8154 49.9395L62.9806 49.389L62.9796 49.3905L63.8154 49.9395ZM65 45.6816H66V45.6816L65 45.6816ZM63.8154 41.4248L62.9802 41.9748L62.9806 41.9753L63.8154 41.4248ZM35.1426 25V24H35.1425L35.1426 25ZM31.6133 27.1064L32.4481 27.6569L32.4492 27.6554L31.6133 27.1064ZM30.4287 31.3633L29.4287 31.3632V31.3633H30.4287ZM31.6133 35.6211L32.4485 35.0711L32.4481 35.0706L31.6133 35.6211ZM35.1426 37.7275L35.1425 38.7275H35.1426V37.7275ZM38.6719 35.6211L37.837 35.0706L37.836 35.0721L38.6719 35.6211ZM39.8574 31.3633H40.8574V31.3632L39.8574 31.3633ZM38.6719 27.1064L37.8367 27.6564L37.837 27.6569L38.6719 27.1064ZM50.8574 25L50.8575 24H50.8574V25ZM47.3281 27.1064L48.163 27.6569L48.164 27.6554L47.3281 27.1064ZM46.1426 31.3633L45.1426 31.3632V31.3633H46.1426ZM47.3281 35.6211L48.1633 35.0711L48.163 35.0706L47.3281 35.6211ZM50.8574 37.7275V38.7275H50.8575L50.8574 37.7275ZM54.3867 35.6211L53.5519 35.0706L53.5508 35.0722L54.3867 35.6211ZM55.5713 31.3633H56.5713V31.3632L55.5713 31.3633ZM54.3867 27.1064L53.5515 27.6564L53.5519 27.6569L54.3867 27.1064ZM43.5 0V1C65.8675 1 84 19.1325 84 41.5H85H86C86 18.0279 66.9721 -1 43.5 -1V0ZM85 41.5H84V43.5H85H86V41.5H85ZM85 43.5H84C84 65.8675 65.8675 84 43.5 84V85V86C66.9721 86 86 66.9721 86 43.5H85ZM43.5 85V84H41.5V85V86H43.5V85ZM41.5 85V84C19.1325 84 1 65.8675 1 43.5H0H-1C-1 66.9721 18.0279 86 41.5 86V85ZM0 43.5H1V41.5H0H-1V43.5H0ZM0 41.5H1C1 19.1325 19.1325 1 41.5 1V0V-1C18.0279 -1 -1 18.0279 -1 41.5H0ZM41.5 0V1H43.5V0V-1H41.5V0ZM43 39.3184V38.3184C38.8207 38.3184 35.7506 40.601 33.831 43.3345L34.6494 43.9092L35.4678 44.4839C37.1186 42.1331 39.6367 40.3184 43 40.3184V39.3184ZM34.6494 43.9092L33.8318 43.3335C31.9482 46.0085 31 49.3196 31 52.0459H32H33C33 49.7258 33.8243 46.818 35.4671 44.4849L34.6494 43.9092ZM32 52.0459L31 52.046C31.0002 55.4494 33.0401 57.7809 35.388 59.1247L35.8848 58.2568L36.3815 57.3889C34.4491 56.2829 33.0001 54.5219 33 52.0458L32 52.0459ZM35.8848 58.2568L35.3869 59.1241C37.6792 60.4402 40.5013 61 43 61V60V59C40.7595 59 38.3016 58.4914 36.3827 57.3896L35.8848 58.2568ZM43 60V61C45.4977 61 48.3206 60.4438 50.6141 59.1235L50.1152 58.2568L49.6163 57.3902C47.6986 58.4942 45.2415 59 43 59V60ZM50.1152 58.2568L50.6135 59.1239C52.9556 57.7779 54.9998 55.4505 55 52.046L54 52.0459L53 52.0458C52.9999 54.5208 51.549 56.2795 49.617 57.3898L50.1152 58.2568ZM54 52.0459H55C55 49.3196 54.0518 46.0085 52.1682 43.3335L51.3506 43.9092L50.5329 44.4849C52.1757 46.818 53 49.7258 53 52.0459H54ZM51.3506 43.9092L52.1698 43.3357C50.2525 40.5971 47.1818 38.3184 43 38.3184V39.3184V40.3184C46.3671 40.3184 48.8846 42.1305 50.5314 44.4827L51.3506 43.9092ZM25.7139 39.3184L25.7138 38.3184C23.7118 38.3186 22.2182 39.5518 21.3487 40.8759L22.1846 41.4248L23.0205 41.9737C23.6594 41.0007 24.6049 40.3185 25.714 40.3184L25.7139 39.3184ZM22.1846 41.4248L21.3497 40.8744C20.4636 42.2183 20 43.9272 20 45.6816L21 45.6816L22 45.6817C22 44.2611 22.3784 42.9475 23.0194 41.9753L22.1846 41.4248ZM21 45.6816H20C20 47.4362 20.4634 49.1458 21.3497 50.49L22.1846 49.9395L23.0194 49.389C22.3783 48.4167 22 47.1026 22 45.6816H21ZM22.1846 49.9395L21.3494 50.4894C22.2178 51.8082 23.7109 53.0457 25.7138 53.0459L25.7139 52.0459L25.714 51.0459C24.6059 51.0458 23.6598 50.3614 23.0197 49.3895L22.1846 49.9395ZM25.7139 52.0459V53.0459C27.7159 53.0459 29.2103 51.8127 30.08 50.4884L29.2441 49.9395L28.4083 49.3905C27.7694 50.3634 26.8232 51.0459 25.7139 51.0459V52.0459ZM29.2441 49.9395L30.079 50.49C30.9653 49.1458 31.4287 47.4362 31.4287 45.6816H30.4287H29.4287C29.4287 47.1026 29.0504 48.4167 28.4093 49.389L29.2441 49.9395ZM30.4287 45.6816L31.4287 45.6816C31.4287 43.9272 30.965 42.2183 30.079 40.8744L29.2441 41.4248L28.4093 41.9752C29.0503 42.9475 29.4287 44.2611 29.4287 45.6817L30.4287 45.6816ZM29.2441 41.4248L30.0793 40.8748C29.2107 39.5558 27.7169 38.3184 25.7139 38.3184V39.3184V40.3184C26.8223 40.3184 27.769 41.0029 28.409 41.9748L29.2441 41.4248ZM60.2861 39.3184V38.3184C58.2841 38.3184 56.7896 39.5515 55.92 40.8759L56.7559 41.4248L57.5917 41.9737C58.2306 41.0008 59.1768 40.3184 60.2861 40.3184V39.3184ZM56.7559 41.4248L55.921 40.8744C55.035 42.2183 54.5713 43.9272 54.5713 45.6816L55.5713 45.6816L56.5713 45.6817C56.5713 44.2611 56.9497 42.9475 57.5907 41.9752L56.7559 41.4248ZM55.5713 45.6816H54.5713C54.5713 47.4362 55.0347 49.1458 55.921 50.49L56.7559 49.9395L57.5907 49.389C56.9496 48.4167 56.5713 47.1026 56.5713 45.6816H55.5713ZM56.7559 49.9395L55.9207 50.4894C56.7893 51.8085 58.2832 53.0459 60.2861 53.0459V52.0459V51.0459C59.1777 51.0459 58.231 50.3613 57.591 49.3895L56.7559 49.9395ZM60.2861 52.0459L60.2862 53.0459C62.2881 53.0457 63.7818 51.8124 64.6513 50.4884L63.8154 49.9395L62.9796 49.3905C62.3406 50.3635 61.395 51.0458 60.286 51.0459L60.2861 52.0459ZM63.8154 49.9395L64.6503 50.49C65.5366 49.1458 66 47.4362 66 45.6816H65H64C64 47.1026 63.6217 48.4167 62.9806 49.389L63.8154 49.9395ZM65 45.6816L66 45.6816C66 43.9272 65.5364 42.2183 64.6503 40.8744L63.8154 41.4248L62.9806 41.9753C63.6216 42.9475 64 44.2611 64 45.6817L65 45.6816ZM63.8154 41.4248L64.6506 40.8748C63.7822 39.556 62.2891 38.3186 60.2862 38.3184L60.2861 39.3184L60.286 40.3184C61.3942 40.3185 62.3402 41.0029 62.9802 41.9748L63.8154 41.4248ZM35.1426 25L35.1425 24C33.1404 24.0001 31.6469 25.2335 30.7774 26.5575L31.6133 27.1064L32.4492 27.6554C33.0882 26.6823 34.0336 26.0001 35.1427 26L35.1426 25ZM31.6133 27.1064L30.7784 26.556C29.8923 27.8999 29.4288 29.6088 29.4287 31.3632L30.4287 31.3633L31.4287 31.3633C31.4288 29.9427 31.8071 28.6291 32.4481 27.6569L31.6133 27.1064ZM30.4287 31.3633H29.4287C29.4287 33.1178 29.8921 34.8275 30.7784 36.1716L31.6133 35.6211L32.4481 35.0706C31.807 34.0984 31.4287 32.7842 31.4287 31.3633H30.4287ZM31.6133 35.6211L30.7781 36.1711C31.6465 37.4898 33.1395 38.7274 35.1425 38.7275L35.1426 37.7275L35.1427 36.7275C34.0345 36.7275 33.0885 36.0431 32.4485 35.0711L31.6133 35.6211ZM35.1426 37.7275V38.7275C37.1448 38.7275 38.6383 37.4939 39.5077 36.1701L38.6719 35.6211L37.836 35.0721C37.1969 36.0453 36.2515 36.7275 35.1426 36.7275V37.7275ZM38.6719 35.6211L39.5067 36.1716C40.3928 34.8278 40.8574 33.1183 40.8574 31.3633H39.8574H38.8574C38.8574 32.7837 38.4784 34.098 37.837 35.0706L38.6719 35.6211ZM39.8574 31.3633L40.8574 31.3632C40.8574 29.6083 40.3927 27.8996 39.5067 26.5559L38.6719 27.1064L37.837 27.6569C38.4784 28.6295 38.8574 29.9431 38.8574 31.3633L39.8574 31.3633ZM38.6719 27.1064L39.507 26.5565C38.6386 25.2378 37.1458 24 35.1426 24V25V26C36.2507 26 37.1965 26.6843 37.8367 27.6564L38.6719 27.1064ZM50.8574 25V24C48.8552 24 47.3617 25.2335 46.4923 26.5575L47.3281 27.1064L48.164 27.6554C48.8031 26.6822 49.7484 26 50.8574 26V25ZM47.3281 27.1064L46.4933 26.5559C45.6073 27.8996 45.1426 29.6083 45.1426 31.3632L46.1426 31.3633L47.1426 31.3633C47.1426 29.9431 47.5217 28.6295 48.163 27.6569L47.3281 27.1064ZM46.1426 31.3633H45.1426C45.1426 33.1183 45.6072 34.8278 46.4933 36.1716L47.3281 35.6211L48.163 35.0706C47.5216 34.098 47.1426 32.7837 47.1426 31.3633H46.1426ZM47.3281 35.6211L46.493 36.1711C47.3614 37.4897 48.8543 38.7275 50.8574 38.7275V37.7275V36.7275C49.7494 36.7275 48.8035 36.0431 48.1633 35.0711L47.3281 35.6211ZM50.8574 37.7275L50.8575 38.7275C52.8595 38.7274 54.3531 37.494 55.2226 36.17L54.3867 35.6211L53.5508 35.0722C52.9118 36.0452 51.9664 36.7275 50.8573 36.7275L50.8574 37.7275ZM54.3867 35.6211L55.2216 36.1716C56.1079 34.8275 56.5713 33.1178 56.5713 31.3633H55.5713H54.5713C54.5713 32.7842 54.193 34.0984 53.5519 35.0706L54.3867 35.6211ZM55.5713 31.3633L56.5713 31.3632C56.5712 29.6088 56.1077 27.8999 55.2216 26.556L54.3867 27.1064L53.5519 27.6569C54.1929 28.6291 54.5712 29.9427 54.5713 31.3633L55.5713 31.3633ZM54.3867 27.1064L55.2219 26.5565C54.3535 25.2377 52.8605 24.0001 50.8575 24L50.8574 25L50.8573 26C51.9655 26.0001 52.9115 26.6844 53.5515 27.6564L54.3867 27.1064Z"
                                        fill="#C9C9C9" mask="url(#path-1-inside-1_85_569)" />
                                </svg>
                                <!-- Hidden file input -->
                                <input type="file" id="petPhotoInput" accept="image/*" style="display: none;">
                                <button type="button" id="petPhotoUploadBtn">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14"
                                        fill="none">
                                        <path
                                            d="M7 10.3162C6.72386 10.3162 6.5 10.0923 6.5 9.8162V1.6662L4.52903 3.63716C4.33115 3.83504 4.00998 3.83392 3.81349 3.63465C3.61896 3.43738 3.62005 3.1201 3.81593 2.92416L6.55492 0.184403C6.80072 -0.0614672 7.19931 -0.0614954 7.44514 0.184341L10.185 2.92419C10.3809 3.12014 10.3822 3.43747 10.1877 3.63493C9.99116 3.83455 9.66959 3.83579 9.47149 3.63769L7.5 1.6662V9.8162C7.5 10.0923 7.27614 10.3162 7 10.3162ZM1.616 13.7392C1.15533 13.7392 0.771 13.5852 0.463 13.2772C0.155 12.9692 0.000666667 12.5845 0 12.1232V10.2002C0 9.92405 0.223858 9.7002 0.5 9.7002C0.776142 9.7002 1 9.92406 1 10.2002V12.1232C1 12.2772 1.064 12.4185 1.192 12.5472C1.32 12.6759 1.461 12.7399 1.615 12.7392H12.385C12.5383 12.7392 12.6793 12.6752 12.808 12.5472C12.9367 12.4192 13.0007 12.2779 13 12.1232V10.2002C13 9.92405 13.2239 9.7002 13.5 9.7002C13.7761 9.7002 14 9.92406 14 10.2002V12.1232C14 12.5839 13.846 12.9682 13.538 13.2762C13.23 13.5842 12.8453 13.7385 12.384 13.7392H1.616Z"
                                            fill="#3B3731" />
                                    </svg>
                                    Upload Pet Photo
                                </button>
                            </div>
                            <div class="form-grid">
                                <div class="form-group">
                                    <label>Name</label>
                                    <div style="position:relative; display:block;">
                                        <input type="text" id="petName" name="name"
                                            value="{{ $petDetails->name ?? '' }}"
                                            style="padding-right:2.5rem; width:100%; display:block;">
                                        <span class="input-check-icon" id="petNameCheck">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                                viewBox="0 0 19 19" fill="none">
                                                <path
                                                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                                    fill="#C9DDA0" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>

                                <x-ui.calendar name="birthday" :value="$petDetails->birthday ?? ''" />

                                <div class="form-group">
                                    <x-ui.pet-type id="my-pet-type" name="pet_type" label="Pet Type"
                                        placeholder="e.g. Dog, Cat, Rabbit..." :required="true"
                                        breedsSelectId="my-pet-breed" :value="$petDetails->pet_type ?? ''" />
                                </div>

                                <div class="form-group">
                                    <x-ui.breeds id="my-pet-breed" name="breed" label="Breed(s)" :required="true" :value="$petDetails->breed ?? ''" />
                                </div>

                                <div class="form-group">
                                    <label>Sex</label>

                                    <div class="sex-options" role="radiogroup" aria-label="Sex">
                                        <label class="radio--small">
                                            <input type="radio" name="sex" value="male" id="petSexMale"
                                                {{ ($petDetails->sex ?? '') === 'male' ? 'checked' : '' }}>
                                            <span class="radio--visual" aria-hidden="true"></span>
                                            <span class="radio--text">Male</span>
                                        </label>

                                        <label class="radio--small">
                                            <input type="radio" name="sex" value="female" id="petSexFemale"
                                                {{ ($petDetails->sex ?? '') === 'female' ? 'checked' : '' }}>
                                            <span class="radio--visual" aria-hidden="true"></span>
                                            <span class="radio--text">Female</span>
                                        </label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>Weight <span>(kg)</span></label>
                                    <input type="number" id="petWeight" name="weight"
                                        value="{{ $petDetails->weight ?? '4' }}">
                                </div>

                                <div class="form-group full-width">
                                    <label>Notes <span>(Optional)</span></label>
                                    <textarea placeholder="Anything your groomer should know?
                    (e.g. anxious around dryers, allergies, behaviour cues)" rows="4" cols="50" id="petNotes" name="notes">{{ $petDetails->notes ?? '' }}</textarea>
                                </div>
                            </div>
                            <div class="form-btns" id="petFormBtns">
                                <div>
                                    <button type="button" id="petFormCancelBtn">Cancel</button>
                                    <button type="submit" id="petFormSaveBtn">Save</button>
                                </div>
                            </div>
                        </form>

                        <!-- Pet Details Display Section -->
                        <div class="pet-details-display" id="petDetailsDisplay">
                            <div class="pet-details-display-content" id="petDetailsContent">
                            </div>
                            <div class="pet-display-action-btns">
                                <button type="button" id="petDisplayChangeBtn">Change</button>
                            </div>
                        </div>
                        <div class=" address-service">
                            <h3>
                                Attending Address of Service
                            </h3>
                            <div style="margin-top: 32px;">
                                <label>Address</label>
                                <div class="input-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                        fill="none">
                                        <path
                                            d="M5.73535 0.5C8.6267 0.500031 10.9707 2.844 10.9707 5.73535C10.9707 7.22006 10.3528 8.55933 9.35938 9.5127C8.41826 10.4158 7.14221 10.9707 5.73535 10.9707C2.844 10.9707 0.500031 8.6267 0.5 5.73535C0.5 2.84398 2.84398 0.5 5.73535 0.5Z"
                                            stroke="#3B3731" />
                                        <path
                                            d="M14.6464 15.3535C14.8416 15.5487 15.1582 15.5487 15.3535 15.3535C15.5487 15.1582 15.5487 14.8416 15.3535 14.6464L14.9999 14.9999L14.6464 15.3535ZM9.70581 9.70581L9.35226 10.0594L14.6464 15.3535L14.9999 14.9999L15.3535 14.6464L10.0594 9.35226L9.70581 9.70581Z"
                                            fill="#3B3731" />
                                    </svg>
                                    <input placeholder="Start typing address..." />
                                </div>
                            </div>
                            <div class="option" id="home-address-option"
                                style="margin: 32px 0; width: fit-content; display: flex !important; flex-direction: row !important; align-items: center !important;"
                                onclick="toggleHomeAddress(this)">
                                <div class="radio"></div>
                                <span class="option-name">Use home address</span>
                            </div>
                        </div>
                        <div style="margin-top: 32px;">
                            <!-- Extras & Add-ons -->
                            <x-ui.extras-addons :addons="$addons ?? []" currency="£" title="Extras & Add-ons"
                                instance-id="booking-1" :default-selected="[1, 4]" on-change-js="handleAddonsChange"
                                :background="false" />
                        </div>


                        <div class="continue-btn">
                            <button>Continue</button>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="11" viewBox="0 0 18 11"
                                fill="none">
                                <path
                                    d="M17.7098 4.79038C18.1054 5.18216 18.1048 5.82163 17.7085 6.21273L13.1513 10.7098C12.796 11.0605 12.2233 11.0559 11.8737 10.6995C11.7351 10.5628 11.6797 10.4261 11.7074 10.2895C11.7421 10.1528 11.8182 10.0229 11.936 9.89991L14.9174 6.97834C15.0836 6.81432 15.2394 6.66739 15.3848 6.53754C15.5 6.43474 15.4113 6.2291 15.2577 6.24466C15.0799 6.26268 14.8972 6.27829 14.7096 6.29152C14.3287 6.31885 13.934 6.33252 13.5254 6.33252H0.835466C0.374051 6.33252 0 5.95847 0 5.49705C0 5.03564 0.374051 4.66159 0.835466 4.66159H13.5254C13.934 4.66159 14.3322 4.67526 14.72 4.70259C14.9048 4.71562 15.0849 4.73097 15.2602 4.74865C15.416 4.76436 15.504 4.55812 15.3848 4.45657C15.2394 4.32672 15.0836 4.17979 14.9174 4.01577L11.9152 1.0737C11.7905 0.950687 11.7144 0.820839 11.6867 0.684158C11.659 0.547476 11.7109 0.410795 11.8425 0.274113C12.1977 -0.0877635 12.7795 -0.0918727 13.1398 0.264949L17.7098 4.79038Z"
                                    fill="#DDDDDD" />
                            </svg>
                        </div>

                    </div>

                    <!-- Step 2: Confirm & Pay -->

                    <div id="step2Content" style="display:none;">
                        <x-ui.confirm-pay />
                    </div>

                    <!-- End Step 2 -->

                </div>
                <div class=" col-lg-4">
                    <div class="service-summary">
                        <div>
                            <h3>Your Booking</h3>
                        </div>
                        <div>
                            <div>
                                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="36" viewBox="0 0 32 36"
                                    fill="none">
                                    <ellipse cx="17.3667" cy="18.0807" rx="10.2458" ry="9.64315" fill="white" />
                                    <path
                                        d="M16.8932 0.202494C16.6132 0.0698256 16.3132 0 15.9998 0C15.6865 0 15.3865 0.0698256 15.1065 0.202494L2.55333 5.78156C1.08668 6.43094 -0.00663626 7.94615 3.03229e-05 9.77559C0.0333633 16.7023 2.75333 29.3756 14.2399 35.1362C15.3532 35.6949 16.6465 35.6949 17.7598 35.1362C29.2463 29.3756 31.9663 16.7023 31.9996 9.77559C32.0063 7.94615 30.913 6.43094 29.4463 5.78156L16.8932 0.202494ZM9.65991 19.9841C9.97991 20.0679 10.3199 20.1098 10.6666 20.1098C13.0199 20.1098 14.9332 18.1058 14.9332 15.6409V11.1721H17.8798C18.6865 11.1721 19.4265 11.6469 19.7865 12.408L20.2665 13.4065H24.5331C25.1197 13.4065 25.5997 13.9093 25.5997 14.5237V16.7581C25.5997 19.8444 23.2131 22.3442 20.2665 22.3442H17.0665V25.8844C17.0665 26.3941 16.6732 26.813 16.1798 26.813C16.0598 26.813 15.9398 26.7851 15.8332 26.7362L9.25325 23.7826C8.81326 23.5871 8.53326 23.1332 8.53326 22.6375C8.53326 22.4419 8.57326 22.2534 8.65993 22.0789L9.65991 19.9841ZM9.59992 11.1721H12.7999V15.6409C12.7999 16.8769 11.8466 17.8754 10.6666 17.8754C9.48658 17.8754 8.53326 16.8769 8.53326 15.6409V12.2893C8.53326 11.6748 9.01326 11.1721 9.59992 11.1721ZM18.1331 14.5237C18.1331 14.2274 18.0208 13.9433 17.8207 13.7337C17.6207 13.5242 17.3494 13.4065 17.0665 13.4065C16.7836 13.4065 16.5123 13.5242 16.3123 13.7337C16.1122 13.9433 15.9998 14.2274 15.9998 14.5237C15.9998 14.82 16.1122 15.1042 16.3123 15.3137C16.5123 15.5232 16.7836 15.6409 17.0665 15.6409C17.3494 15.6409 17.6207 15.5232 17.8207 15.3137C18.0208 15.1042 18.1331 14.82 18.1331 14.5237Z"
                                        fill="#C9DDA0" />
                                </svg>

                                <img src="{{ asset('images/service-summary-image.png') }}" alt="Hero Image">
                            </div>
                            <div>
                                <div>
                                    <p>
                                        Sarah’s Grooming Studio
                                    </p>
                                    <span>
                                        Sarah W.
                                    </span>
                                </div>
                                <div>
                                    <p><svg xmlns="http://www.w3.org/2000/svg" width="13" height="18"
                                            viewBox="0 0 13 18" fill="none">
                                            <path
                                                d="M6.5 8.55C5.88432 8.55 5.29385 8.31295 4.8585 7.89099C4.42315 7.46903 4.17857 6.89674 4.17857 6.3C4.17857 5.70326 4.42315 5.13097 4.8585 4.70901C5.29385 4.28705 5.88432 4.05 6.5 4.05C7.11568 4.05 7.70615 4.28705 8.1415 4.70901C8.57685 5.13097 8.82143 5.70326 8.82143 6.3C8.82143 6.59547 8.76138 6.88806 8.64472 7.16104C8.52806 7.43402 8.35706 7.68206 8.1415 7.89099C7.92593 8.09992 7.67002 8.26566 7.38837 8.37873C7.10672 8.4918 6.80485 8.55 6.5 8.55ZM6.5 0C4.77609 0 3.12279 0.663748 1.90381 1.84523C0.684819 3.02671 0 4.62914 0 6.3C0 11.025 6.5 18 6.5 18C6.5 18 13 11.025 13 6.3C13 4.62914 12.3152 3.02671 11.0962 1.84523C9.87721 0.663748 8.22391 0 6.5 0Z"
                                                fill="#FFC97A" />
                                        </svg>2.5 mi</p>
                                    <p><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                            viewBox="0 0 18 18" fill="none">
                                            <path
                                                d="M7.88086 0.849183C8.23312 -0.283064 9.76689 -0.28306 10.1191 0.849187L11.437 5.08524C11.5946 5.5916 12.0464 5.93442 12.5562 5.93442H16.821C17.9609 5.93442 18.4349 7.45785 17.5127 8.15762L14.0624 10.7756C13.6499 11.0886 13.4774 11.6433 13.6349 12.1497L14.9528 16.3857C15.3051 17.518 14.0642 18.4595 13.142 17.7597L9.69167 15.1417C9.27924 14.8287 8.72076 14.8287 8.30833 15.1417L4.85802 17.7597C3.93579 18.4595 2.69495 17.518 3.04721 16.3857L4.36511 12.1497C4.52264 11.6433 4.35007 11.0886 3.93763 10.7756L0.487324 8.15762C-0.434903 7.45785 0.0390621 5.93442 1.179 5.93442H5.44381C5.95361 5.93442 6.40542 5.59159 6.56296 5.08524L7.88086 0.849183Z"
                                                fill="#FFC97A" />
                                        </svg>4.3 <span>(20 reviews)</span></p>
                                </div>
                            </div>
                        </div>

                        <div class="divider"></div>

                        <div>
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="15" viewBox="0 0 14 15"
                                    fill="none">
                                    <path
                                        d="M4.38022 10.1726C5.5048 11.2972 8.23992 10.3857 10.4891 8.13624C12.7386 5.88709 13.65 3.15197 12.5255 2.02739M7.68915 1.26352L8.19816 1.77289M5.90761 3.04541L6.41662 3.55442M4.37986 5.08182L4.88887 5.59083M3.87085 7.62723L4.37986 8.13624M10.4891 0.5L10.9981 1.00901M9.98006 3.55478L10.9981 4.5728M8.19852 5.33668L9.21654 6.3547M6.16212 6.86371L7.18014 7.88173"
                                        stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M4.37984 11.7C4.80158 11.2783 4.80158 10.5945 4.37984 10.1727C3.9581 9.75101 3.27432 9.75101 2.85258 10.1728L0.816229 12.2091C0.394487 12.6308 0.394487 13.3146 0.816229 13.7364C1.23797 14.1581 1.92175 14.1581 2.34349 13.7364L4.37984 11.7Z"
                                        stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>Service</span>
                            <div>
                                <p>Full Groom</p>
                                <p>£48</p>
                            </div>

                        </div>

                        <div class="divider"></div>

                        <div>
                            <p>Extras & Add-ons</p>
                            <div>
                                <!-- Add-ons will be inserted here by JavaScript -->
                            </div>
                        </div>

                        <div class="promocode">
                            <label for="">Promo code</label>
                            <input type="text" placeholder="Enter Promo Code">
                        </div>

                        <div class="sum">
                            <div>
                            </div>
                        </div>
                        <div class="divider"></div>
                        <div class="total">
                            <p>Total:</p><span>£58.00</span>
                        </div>
                        <div class="payment"><button id="confirmPayBtnSidebar" disabled
                                style="cursor: not-allowed; transition: all 0.2s ease;">Confirm & Pay</button></div>
                        <div class="caution">
                            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 48 48"
                                fill="none">
                                <path
                                    d="M24 4.8C34.5864 4.8 43.2 13.4136 43.2 24C43.2 34.5864 34.5864 43.2 24 43.2C13.4136 43.2 4.8 34.5864 4.8 24C4.8 13.4136 13.4136 4.8 24 4.8ZM24 0C10.7448 0 0 10.7448 0 24C0 37.2552 10.7448 48 24 48C37.2552 48 48 37.2552 48 24C48 10.7448 37.2552 0 24 0ZM26.4 31.2H21.6V36H26.4V31.2ZM21.6 26.4H26.4L27.6 12H20.4L21.6 26.4Z"
                                    fill="#FFC97A" />
                            </svg>
                            <p>
                                Free cancellations up to 24 hours before appointment.
                                <br />Tools sanitised after every pet
                            </p>
                        </div>

                    </div>
                    <div class="need-help">
                        <svg xmlns="http://www.w3.org/2000/svg" width="34" height="28" viewBox="0 0 34 28" fill="none">
                            <path
                                d="M3.08333 13.875C1.3875 13.875 0 12.4875 0 10.7917V3.08333C0 1.3875 1.3875 0 3.08333 0H15.4167C17.1125 0 18.5 1.3875 18.5 3.08333V10.7917C18.5 12.4875 17.1125 13.875 15.4167 13.875H12.3333V18.5L7.70833 13.875H3.08333ZM30.8333 23.125C32.5292 23.125 33.9167 21.7375 33.9167 20.0417V12.3333C33.9167 10.6375 32.5292 9.25 30.8333 9.25H21.5833V10.7917C21.5833 14.1833 18.8083 16.9583 15.4167 16.9583V20.0417C15.4167 21.7375 16.8042 23.125 18.5 23.125H21.5833V27.75L26.2083 23.125H30.8333Z"
                                fill="#D8E8B7" />
                        </svg>
                        <p>Need help? Chat with <a>Fursgo Support</a>.</p>
                    </div>
</section>
@endsection


@section('script')
<script>
    // ===== Pet Breeds Data =====
    let petBreedsData = {};
    let selectedPetType = '';

    async function loadPetBreedsData() {
        try {
            const response = await fetch('/data/pet-breeds.json');
            if (!response.ok) throw new Error(`HTTP ${response.status}`);
            petBreedsData = await response.json();
            setupPetTypeAutoDetection();
        } catch (error) {
            console.error('Failed to load pet breeds:', error);
        }
    }

    function setupPetTypeAutoDetection() {
        const petTypeInput = document.getElementById('my-pet-type');
        const suggestionBox = document.getElementById('my-pet-type-suggestions');
        const petBreedSelect = document.getElementById('my-pet-breed');

        if (!petTypeInput || !petBreedsData.petTypes) return;

        petTypeInput.addEventListener('input', function() {
            const inputValue = this.value.trim().toLowerCase();
            suggestionBox.innerHTML = '';

            if (inputValue.length === 0) {
                suggestionBox.style.display = 'none';
                petBreedSelect.innerHTML = '<option value="">Select a Breed</option>';
                if (petBreedSelect._fursDD) petBreedSelect._fursDD.refresh();
                selectedPetType = '';
                checkContinueBtnState();
                return;
            }

            const matches = petBreedsData.petTypes.filter(pt =>
                pt.name.toLowerCase().includes(inputValue)
            );

            if (matches.length > 0) {
                suggestionBox.style.display = 'block';
                suggestionBox.innerHTML = '';
                matches.forEach(match => {
                    const item = document.createElement('div');
                    item.style.cssText = 'padding:.625rem;cursor:pointer;border-bottom:.0625rem solid #EEE;color:#3B3731;font-family:Lato;';
                    item.textContent = match.name;
                    item.addEventListener('mouseover', function() {
                        this.style.backgroundColor = '#f5f5f5';
                    });
                    item.addEventListener('mouseout', function() {
                        this.style.backgroundColor = 'transparent';
                    });
                    item.addEventListener('click', function() {
                        petTypeInput.value = match.name;
                        selectedPetType = match.name;
                        suggestionBox.style.display = 'none';
                        populateBreeds('my-pet-breed', match);
                        checkContinueBtnState();
                    });
                    suggestionBox.appendChild(item);
                });
            } else {
                suggestionBox.style.display = 'none';
                petBreedSelect.innerHTML = '<option value="">Select a Breed</option>';
                if (petBreedSelect._fursDD) petBreedSelect._fursDD.refresh();
            }

            const exactMatch = petBreedsData.petTypes.find(p => p.name.toLowerCase() === inputValue);
            if (exactMatch) {
                selectedPetType = exactMatch.name;
                populateBreeds('my-pet-breed', exactMatch);
                suggestionBox.style.display = 'none';
            }

            checkContinueBtnState();
        });

        document.addEventListener('click', function(e) {
            if (e.target !== petTypeInput && e.target !== suggestionBox) {
                suggestionBox.style.display = 'none';
            }
        });

        petTypeInput.addEventListener('focus', function() {
            if (this.value.trim().length > 0) suggestionBox.style.display = 'block';
        });
    }

    function populateBreeds(breedSelectId, petType) {
        const petBreedSelect = document.getElementById(breedSelectId);
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
        // Force re-check after breeds are loaded
        setTimeout(checkContinueBtnState, 100);
    }

    // ===== Service Summary Add-ons =====
    window.handleAddonsChange = function(selectedIds, total, selectedAddons) {
        const addonsContainer = document.querySelector('.service-summary .extras-addons-list');
        const extrasHeading = document.querySelector('.service-summary .extras-heading');

        if (addonsContainer) {
            addonsContainer.innerHTML = '';
            selectedAddons.forEach(a => {
                const div = document.createElement('div');
                div.style.cssText = 'display:flex;justify-content:space-between;align-items:center;';
                div.innerHTML = `<p>${a.name}</p><p>£${a.price}</p>`;
                addonsContainer.appendChild(div);
            });
        }
        if (extrasHeading) {
            extrasHeading.style.display = selectedAddons.length ? 'block' : 'none';
        }

        const baseServicePrice = 48;
        const platformFee = 2;
        const totalWithAddons = baseServicePrice + total + platformFee;

        const sumDiv = document.querySelector('.service-summary .sum');
        if (sumDiv) {
            sumDiv.innerHTML = `
                <div><p>Service:</p><span>£${baseServicePrice}</span></div>
                ${selectedAddons.length ? `<div><p>Add-ons:</p><span>£${total}</span></div>` : ''}
                <div><p>Platform fee:</p><span>£${platformFee}</span></div>
            `;
        }

        const totalSpan = document.querySelector('.service-summary .total span');
        if (totalSpan) totalSpan.innerText = `£${totalWithAddons}`;

        // IMPORTANT: re-check continue button state after add-ons change
        checkContinueBtnState();
    };

    // ===== SIMPLIFIED Continue Button State Check =====
    let isCheckingButtonState = false;
    let checkButtonTimeout;

    function checkContinueBtnState() {
        // Prevent recursive calls
        if (isCheckingButtonState) return;
        isCheckingButtonState = true;

        try {
            const continueBtn = document.querySelector('.continue-btn');
            if (!continueBtn) return;

            // Get field values - ONLY check native .value properties, not text content
            // (text content gives placeholder/label text, not actual selected values)
            const petName = (document.getElementById('petName')?.value || '').trim();
            const petType = (document.getElementById('my-pet-type')?.value || '').trim();
            const petBreed = (document.getElementById('my-pet-breed')?.value || '').trim();
            const petWeight = (document.getElementById('petWeight')?.value || '').trim();
            const birthday = (document.querySelector('input[name="birthday"]')?.value || '').trim();
            const sex = (document.querySelector('input[name="sex"]:checked')?.value || '').trim();
            const addressInput = (document.querySelector('.address-service .input-wrapper input')?.value || '').trim();
            const homeAddressToggled = document.getElementById('home-address-option')?.classList.contains('selected') || false;

            // Boolean checks
            const hasName = petName !== '';
            const hasType = petType !== '';
            const hasBreed = petBreed !== '';
            const hasWeight = petWeight !== '';
            const hasBirthday = birthday !== '';
            const hasSex = sex !== '';
            const hasAddress = addressInput !== '' || homeAddressToggled;

            // All required
            const allFilled = hasName && hasType && hasBreed && hasWeight && hasBirthday && hasSex && hasAddress;

            if (allFilled) {
                continueBtn.classList.add('active');
                continueBtn.querySelector('button').style.color = '#FFF';
                const arrow = continueBtn.querySelector('svg path');
                if (arrow) arrow.setAttribute('fill', '#FFF');
            } else {
                continueBtn.classList.remove('active');
                continueBtn.querySelector('button').style.color = '#DDD';
                const arrow = continueBtn.querySelector('svg path');
                if (arrow) arrow.setAttribute('fill', '#DDDDDD');
            }

            return allFilled;
        } finally {
            isCheckingButtonState = false;
        }
    }

    // ===== Address Toggle =====
    window.toggleHomeAddress = function(el) {
        el.classList.toggle('selected');
        setTimeout(checkContinueBtnState, 100);
    };

    // ===== Photo Upload =====
    let petPhotoBase64 = null;

    function initPhotoUpload() {
        const petPhotoUploadBtn = document.getElementById('petPhotoUploadBtn');
        const petPhotoInput = document.getElementById('petPhotoInput');
        const petPhotoPlaceholder = document.getElementById('petPhotoPlaceholder');

        if (!petPhotoUploadBtn || !petPhotoInput) return;

        // Move input to body to avoid display:none issues
        document.body.appendChild(petPhotoInput);
        petPhotoInput.style.cssText = 'position:absolute;left:-9999px;top:-9999px;opacity:0;width:1px;height:1px;';

        petPhotoUploadBtn.addEventListener('click', function(e) {
            e.preventDefault();
            petPhotoInput.click();
        });

        petPhotoInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            if (!file.type.startsWith('image/')) {
                alert('Please select an image file');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Image size must be less than 5MB');
                return;
            }

            const reader = new FileReader();
            reader.onload = function(event) {
                petPhotoBase64 = event.target.result;
                displayPetPhotoPreview(petPhotoBase64);
            };
            reader.readAsDataURL(file);
        });
    }

    function displayPetPhotoPreview(photoBase64) {
        const petPhotoPlaceholder = document.getElementById('petPhotoPlaceholder');
        const petPhotoUploadBtn = document.getElementById('petPhotoUploadBtn');
        if (!petPhotoPlaceholder) return;

        const existingWrapper = petPhotoPlaceholder.parentElement.querySelector('.pet-photo-col-wrapper');
        if (existingWrapper) existingWrapper.remove();

        const previewDiv = document.createElement('div');
        previewDiv.className = 'pet-photo-preview';
        const img = document.createElement('img');
        img.src = photoBase64;
        previewDiv.appendChild(img);

        petPhotoPlaceholder.style.display = 'none';
        if (petPhotoUploadBtn) petPhotoUploadBtn.style.display = 'none';

        const colWrapper = document.createElement('div');
        colWrapper.className = 'pet-photo-col-wrapper';

        const actionBtnsDiv = document.createElement('div');
        actionBtnsDiv.className = 'pet-photo-action-btns';

        const editBtn = document.createElement('button');
        editBtn.type = 'button';
        editBtn.className = 'pet-photo-edit-btn';
        editBtn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
            <path d="M7.78125 2.1875L11.8125 6.21875M1.3125 12.6875H3.64583L11.8125 4.52083C12.4419 3.89148 12.4419 2.87114 11.8125 2.2418C11.1832 1.61245 10.1628 1.61245 9.53345 2.2418L1.3125 10.4627V12.6875Z" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg> Edit photo`;
        editBtn.addEventListener('click', function() {
            const input = document.getElementById('petPhotoInput');
            if (input) input.click();
        });

        const saveBtn = document.createElement('button');
        saveBtn.type = 'button';
        saveBtn.className = 'pet-photo-save-btn';
        saveBtn.textContent = 'Save';
        saveBtn.addEventListener('click', function() {
            console.log('Photo confirmed');
        });

        actionBtnsDiv.appendChild(editBtn);
        actionBtnsDiv.appendChild(saveBtn);

        colWrapper.appendChild(previewDiv);
        colWrapper.appendChild(actionBtnsDiv);

        petPhotoPlaceholder.parentElement.insertBefore(colWrapper, petPhotoPlaceholder);
    }

    // ===== Pet Details Form =====
    function collectPetDetails() {
        return {
            name: (document.getElementById('petName')?.value ?? '').trim(),
            birthday: (document.querySelector('input[name="birthday"]')?.value ?? ''),
            type: (document.getElementById('my-pet-type')?.value ?? '').trim(),
            breed: (document.getElementById('my-pet-breed')?.value ?? ''),
            sex: (document.querySelector('input[name="sex"]:checked')?.value ?? ''),
            weight: (document.getElementById('petWeight')?.value ?? ''),
            notes: (document.getElementById('petNotes')?.value ?? '').trim(),
            photo: petPhotoBase64,
            address: (document.querySelector('.address-service .input-wrapper input')?.value ?? '').trim(),
            homeAddressToggled: document.getElementById('home-address-option')?.classList.contains('selected') || false
        };
    }

    function displayPetDetails(details) {
        const petDetailsDisplay = document.getElementById('petDetailsDisplay');
        const petDetailsContent = document.getElementById('petDetailsContent');
        if (!petDetailsDisplay || !petDetailsContent) return;

        let birthdayDisplay = 'Not provided';
        if (details.birthday) {
            try {
                const d = new Date(details.birthday + 'T00:00:00');
                if (!isNaN(d.getTime())) {
                    birthdayDisplay = `${String(d.getDate()).padStart(2, '0')} / ${String(d.getMonth() + 1).padStart(2, '0')} / ${d.getFullYear()}`;
                }
            } catch (e) {
                birthdayDisplay = details.birthday;
            }
        }

        const existingPhoto = document.getElementById('petDisplayPhoto');
        if (existingPhoto) existingPhoto.remove();

        if (details.photo) {
            const photoEl = document.createElement('div');
            photoEl.id = 'petDisplayPhoto';
            photoEl.innerHTML = `<img src="${details.photo}" alt="Pet photo" style="width:5.3125rem;height:5.3125rem;border-radius:50%;object-fit:cover;flex-shrink:0;">`;
            petDetailsDisplay.insertBefore(photoEl, petDetailsContent);
        }

        petDetailsContent.innerHTML = `
            <div class="pet-detail-item">
                <label>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none">
                        <path d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    ${details.name || 'N/A'}
                </label>
                <span>${details.type || 'Not provided'} <span class="dot"></span> ${details.breed || 'Not provided'}</span>
            </div>
            <div class="pet-detail-item">
                <label>
                              <svg xmlns="http://www.w3.org/2000/svg" width="15" height="17" viewBox="0 0 15 17" fill="none">
  <path d="M1.27778 11.7778V14.5C1.27778 14.9126 1.44167 15.3082 1.73339 15.5999C2.02511 15.8917 2.42077 16.0556 2.83333 16.0556H12.1667C12.5792 16.0556 12.9749 15.8917 13.2666 15.5999C13.5583 15.3082 13.7222 14.9126 13.7222 14.5V11.7778M0.5 9.83333V9.05556C0.5 8.643 0.663888 8.24734 0.955612 7.95561C1.24733 7.66389 1.643 7.5 2.05556 7.5H12.9444C13.357 7.5 13.7527 7.66389 14.0444 7.95561C14.3361 8.24734 14.5 8.643 14.5 9.05556V9.83333M7.5 5.16667V7.5M7.5 5.16667C8.48156 5.16667 9.05556 4.41378 9.05556 3.125C9.05556 1.83622 7.5 0.5 7.5 0.5C7.5 0.5 5.94444 1.83622 5.94444 3.125C5.94444 4.41378 6.51844 5.16667 7.5 5.16667Z" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round"/>
  <path d="M0.5 9.8335C0.5 10.4523 0.745833 11.0458 1.18342 11.4834C1.621 11.921 2.21449 12.1668 2.83333 12.1668C3.45217 12.1668 4.04566 11.921 4.48325 11.4834C4.92083 11.0458 5.16667 10.4523 5.16667 9.8335C5.16667 10.4523 5.4125 11.0458 5.85008 11.4834C6.28767 11.921 6.88116 12.1668 7.5 12.1668C8.11884 12.1668 8.71233 11.921 9.14992 11.4834C9.5875 11.0458 9.83333 10.4523 9.83333 9.8335C9.83333 10.4523 10.0792 11.0458 10.5168 11.4834C10.9543 11.921 11.5478 12.1668 12.1667 12.1668C12.7855 12.1668 13.379 11.921 13.8166 11.4834C14.2542 11.0458 14.5 10.4523 14.5 9.8335" stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round"/>
</svg>
                    Birthday
                </label>
                <span>${birthdayDisplay}</span>
            </div>
            <div class="pet-detail-item">
                <label>
                    <svg fill="#9D9B98" width="15" height="15" viewBox="0 0 61.13 61.13" xmlns="http://www.w3.org/2000/svg">
                        <path d="M27.482,34.031v12.317h-6.92c-1.703,0-3.084,1.381-3.084,3.084s1.381,3.084,3.084,3.084h6.92v5.531c0,1.703,1.381,3.084,3.084,3.084s3.084-1.381,3.084-3.084v-5.531h6.92c1.703,0,3.084-1.381,3.084-3.084s-1.381-3.084-3.084-3.084h-6.92V34.031c7.993-1.458,14.072-8.467,14.072-16.874C47.723,7.697,40.026,0,30.566,0c-9.46,0-17.157,7.697-17.157,17.157C13.409,25.564,19.489,32.573,27.482,34.031z M30.566,6.169c6.059,0,10.988,4.929,10.988,10.988s-4.929,10.988-10.988,10.988s-10.988-4.929-10.988-10.988S24.507,6.169,30.566,6.169z"/>
                    </svg>
                    Sex
                </label>
                <span>${details.sex ? details.sex.charAt(0).toUpperCase() + details.sex.slice(1) : 'Not provided'}</span>
            </div>
            <div class="pet-detail-item full-width">
                <label>
                         <svg xmlns="http://www.w3.org/2000/svg" width="15" height="16" viewBox="0 0 15 16" fill="none">
  <path d="M13.5905 8.11123L13.9601 6.73016C14.3918 5.1182 14.6084 4.31257 14.4462 3.61489C14.3176 3.0641 14.0285 2.56382 13.6155 2.17734C13.093 1.68768 12.2866 1.4718 10.6747 1.04003C9.0627 0.607553 8.25636 0.391671 7.55939 0.55394C7.0086 0.682549 6.50833 0.971618 6.12185 1.38458C5.70224 1.83207 5.4835 2.48758 5.15824 3.67851L4.98382 4.32544L4.61425 5.70651C4.18177 7.31847 3.96589 8.1241 4.12816 8.82178C4.25677 9.37257 4.54584 9.87285 4.9588 10.2593C5.48135 10.749 6.28769 10.9649 7.89966 11.3974C9.35221 11.7862 10.1507 12 10.8048 11.9192C10.8763 11.9101 10.9463 11.8977 11.0149 11.882C11.5655 11.7538 12.0658 11.4652 12.4525 11.0528C12.9421 10.5295 13.158 9.7232 13.5905 8.11123Z" stroke="#9D9B98"/>
  <path d="M10.8047 11.9191C10.6553 12.3769 10.3927 12.7895 10.0413 13.1186C9.51875 13.6083 8.71241 13.8242 7.10045 14.2559C5.48848 14.6877 4.68214 14.9043 3.98517 14.7413C3.43447 14.6129 2.9342 14.3241 2.54763 13.9114C2.05796 13.3888 1.84137 12.5825 1.4096 10.9705L1.04003 9.58946C0.607553 7.9775 0.391671 7.17116 0.55394 6.47419C0.682549 5.9234 0.971618 5.42313 1.38458 5.03665C1.90713 4.54698 2.71347 4.3311 4.32544 3.89862C4.62948 3.81665 4.90708 3.74302 5.15823 3.67773" stroke="#9D9B98"/>
  <path d="M7.48927 6.21924L10.9419 7.14424M6.93384 8.29084L9.00544 8.84556" stroke="#9D9B98" stroke-linecap="round"/>
</svg>
                    Notes
                </label>
                <span>${details.notes || 'No notes added'}</span>
            </div>
        `;
    }

    function toggleFormDisplay(showDisplay) {
        const petDetailsForm = document.querySelector('.pet-details-form');
        const petDetailsDisplay = document.getElementById('petDetailsDisplay');
        const petFormBtns = document.getElementById('petFormBtns');

        if (showDisplay) {
            if (petDetailsForm) petDetailsForm.classList.add('hidden');
            if (petDetailsDisplay) petDetailsDisplay.classList.add('active');
            if (petFormBtns) petFormBtns.style.display = 'none';
        } else {
            if (petDetailsForm) petDetailsForm.classList.remove('hidden');
            if (petDetailsDisplay) petDetailsDisplay.classList.remove('active');
            if (petFormBtns) petFormBtns.style.display = 'block';
            populateFormWithSavedData();
        }
    }

    // Server-side rendered pet details (passed from controller)
    const serverPetDetails = @json($petDetails ?? null);

    function checkSavedPetDetails() {
        if (!serverPetDetails) {
            console.log('[Init] No saved pet details found');
            return;
        }

        try {
            console.log('[Init] Found pet details:', serverPetDetails.name);

            // Populate all form fields
            const petNameInput = document.getElementById('petName');
            if (petNameInput && !petNameInput.value) {
                petNameInput.value = serverPetDetails.name || '';
            }

            const petTypeInput = document.getElementById('my-pet-type');
            if (petTypeInput && !petTypeInput.value) {
                petTypeInput.value = serverPetDetails.pet_type || '';
            }

            // Show the saved details display
            toggleFormDisplay(true);
            displayPetDetails({
                name: serverPetDetails.name,
                type: serverPetDetails.pet_type,
                breed: serverPetDetails.breed,
                birthday: serverPetDetails.birthday,
                sex: serverPetDetails.sex,
                weight: serverPetDetails.weight,
                notes: serverPetDetails.notes,
                photo: serverPetDetails.photo,
                address: serverPetDetails.address,
                homeAddressToggled: serverPetDetails.home_address_toggled
            });
            checkContinueBtnState();

            console.log('[Init] Successfully restored pet details from server');
        } catch (e) {
            console.error('Error restoring saved pet details:', e);
        }
    }

    // ===== Step Navigation =====
    function goToStep2() {
        const step1 = document.querySelector('.step-item:nth-child(1)');
        const step2 = document.querySelector('.step-item:nth-child(2)');

        if (step1) {
            step1.classList.remove('active');
            step1.classList.add('inactive');
            const circle1 = step1.querySelector('.step-circle');
            if (circle1) {
                circle1.style.backgroundColor = '#C9DDA0';
                circle1.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="13" viewBox="0 0 16 13" fill="none"><path d="M14.6667 1.49969L5.50004 10.6664L1.33337 6.49969" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>';
            }
            const label1 = step1.querySelector('.step-label');
            if (label1) label1.style.color = '#C9DDA0';
        }
        if (step2) {
            step2.classList.remove('inactive');
            step2.classList.add('active');
        }

        const progressFill = document.querySelector('.progress-fill');
        if (progressFill) progressFill.style.width = '100%';

        // Populate confirm page fields from current form data
        const petDetails = collectPetDetails();
        if (petDetails.name) {
            const petNameEl = document.getElementById('confirmPetName');
            if (petNameEl) petNameEl.textContent = `${petDetails.name} · ${petDetails.type}${petDetails.breed ? ' · ' + petDetails.breed : ''}`;
        }

        const addressInput = document.querySelector('.address-service .input-wrapper input');
        const confirmAddress = document.getElementById('confirmAddress');
        if (addressInput && confirmAddress) confirmAddress.textContent = addressInput.value.trim() || '—';

        document.getElementById('step1Content').style.display = 'none';
        document.getElementById('step2Content').style.display = 'block';
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    function goToStep1() {
        const step1 = document.querySelector('.step-item:nth-child(1)');
        const step2 = document.querySelector('.step-item:nth-child(2)');

        if (step1) {
            step1.classList.add('active');
            step1.classList.remove('inactive');
            const circle1 = step1.querySelector('.step-circle');
            if (circle1) {
                circle1.style.backgroundColor = '';
                circle1.innerHTML = '1';
            }
            const label1 = step1.querySelector('.step-label');
            if (label1) label1.style.color = '';
        }
        if (step2) {
            step2.classList.add('inactive');
            step2.classList.remove('active');
        }

        const progressFill = document.querySelector('.progress-fill');
        if (progressFill) progressFill.style.width = '50%';

        document.getElementById('step1Content').style.display = 'block';
        document.getElementById('step2Content').style.display = 'none';
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    }

    // ===== Date Picker Opener =====
    function initDatePickers() {
        document.querySelectorAll('.form-group').forEach(group => {
            const input = group.querySelector('input[type="date"]');
            if (!input || group.querySelector('.picker-opener')) return;

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'picker-opener';
            btn.setAttribute('aria-label', 'Open date picker');
            btn.innerHTML = '<span style="position:absolute;left:-624.9375rem">Open date picker</span>';

            const openPicker = () => {
                if (typeof input.showPicker === 'function') {
                    try {
                        input.showPicker();
                        return;
                    } catch (e) {}
                }
                input.focus();
            };

            btn.addEventListener('click', e => {
                e.stopPropagation();
                openPicker();
            });
            input.addEventListener('click', e => {
                e.stopPropagation();
                openPicker();
            });
            input.addEventListener('keydown', e => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    openPicker();
                }
            });

            group.appendChild(btn);
        });
    }

    // ===== Checkmark Icon Toggles =====
    function initCheckmarkToggles() {
        const petNameInput = document.getElementById('petName');
        const petTypeInput = document.getElementById('my-pet-type');

        if (petNameInput) {
            petNameInput.addEventListener('input', function() {
                const icon = document.getElementById('petNameCheck');
                if (icon) icon.classList.toggle('visible', this.value.trim() !== '');
            });
        }
        if (petTypeInput) {
            petTypeInput.addEventListener('input', function() {
                const icon = document.getElementById('petTypeCheck');
                if (icon) icon.classList.toggle('visible', this.value.trim() !== '');
            });
        }

        try {
            if (serverPetDetails) {
                if (serverPetDetails.name) {
                    const ic = document.getElementById('petNameCheck');
                    if (ic) ic.classList.add('visible');
                }
                if (serverPetDetails.pet_type) {
                    const ic = document.getElementById('petTypeCheck');
                    if (ic) ic.classList.add('visible');
                }
            }
        } catch (e) {}
    }

    // ===== MAIN DOMContentLoaded =====
    document.addEventListener('DOMContentLoaded', function() {
        // Init subsystems
        initPhotoUpload();
        initDatePickers();
        initCheckmarkToggles();

        // Load breed data, then show saved pet details
        loadPetBreedsData().then(() => {
            checkSavedPetDetails();
        });

        // ---- Pet form cancel (delegated) ----
        document.body.addEventListener('click', function(e) {
            const cancelBtn = e.target.closest('#petFormCancelBtn');

            if (cancelBtn) {
                e.preventDefault();
                e.stopPropagation();
                // Check if we have saved data
                if (serverPetDetails) {
                    if (confirm('Discard unsaved changes?')) toggleFormDisplay(true);
                } else {
                    // Clear fields
                    ['petName'].forEach(id => {
                        const el = document.getElementById(id);
                        if (el) el.value = '';
                    });
                    const bi = document.querySelector('input[name="birthday"]');
                    if (bi) bi.value = '';
                    const ti = document.getElementById('my-pet-type');
                    if (ti) ti.value = '';
                    const bs = document.getElementById('my-pet-breed');
                    if (bs) {
                        if (bs._fursDD) bs._fursDD.setValue('');
                        else bs.value = '';
                    }
                    document.querySelectorAll('input[name="sex"]').forEach(r => r.checked = false);
                    const wi = document.getElementById('petWeight');
                    if (wi) wi.value = '4';
                    const ni = document.getElementById('petNotes');
                    if (ni) ni.value = '';
                    petPhotoBase64 = null;
                    const pi = document.getElementById('petPhotoInput');
                    if (pi) pi.value = '';
                    const ph = document.getElementById('petPhotoPlaceholder');
                    if (ph) ph.style.display = 'block';
                    const ub = document.getElementById('petPhotoUploadBtn');
                    if (ub) ub.style.display = 'block';
                    const cw = document.querySelector('.pet-photo-col-wrapper');
                    if (cw) cw.remove();
                }
                checkContinueBtnState();
            }
        });

        // ---- Pet display Change button ----
        const petDisplayChangeBtn = document.getElementById('petDisplayChangeBtn');
        if (petDisplayChangeBtn) {
            petDisplayChangeBtn.addEventListener('click', function() {
                toggleFormDisplay(false);
                setTimeout(checkContinueBtnState, 50);
            });
        }

        // ---- Live field watchers for continue button ----
        ['petName', 'my-pet-type', 'my-pet-breed', 'petWeight'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', checkContinueBtnState);
                el.addEventListener('change', checkContinueBtnState);
            }
        });

        const birthdayInput = document.querySelector('input[name="birthday"]');
        if (birthdayInput) {
            birthdayInput.addEventListener('input', checkContinueBtnState);
            birthdayInput.addEventListener('change', checkContinueBtnState);
        }

        const petBreedSelect = document.getElementById('my-pet-breed');
        if (petBreedSelect) {
            petBreedSelect.addEventListener('change', checkContinueBtnState);
            // If custom component exists, try to hook its internal change
            if (petBreedSelect._fursDD && typeof petBreedSelect._fursDD.onChange === 'function') {
                const orig = petBreedSelect._fursDD.onChange;
                petBreedSelect._fursDD.onChange = function(...args) {
                    if (orig) orig.apply(this, args);
                    checkContinueBtnState();
                };
            }
        }

        document.querySelectorAll('input[name="sex"]').forEach(r => r.addEventListener('change', checkContinueBtnState));

        // Address input listeners with fallbacks
        const addressInput = document.querySelector('.address-service .input-wrapper input');
        if (addressInput) {
            addressInput.addEventListener('input', checkContinueBtnState);
            addressInput.addEventListener('change', checkContinueBtnState);
        }

        // Watch for home address toggle
        const homeAddressOption = document.getElementById('home-address-option');
        if (homeAddressOption) {
            homeAddressOption.addEventListener('click', checkContinueBtnState);
        }

        // Watch for add‑on checkbox changes (dynamic)
        const addonsContainer = document.getElementById('furs-addons-groomer');
        if (addonsContainer) {
            addonsContainer.addEventListener('change', (e) => {
                if (e.target.type === 'checkbox') checkContinueBtnState();
            });
        }

        // Initial check
        setTimeout(checkContinueBtnState, 200);

        // ---- Broad mutation observer for custom component updates ----
        // This catches DOM changes from custom components that don't fire standard events
        // but ONLY on form inputs, not on the button itself
        let mutationTimeout;
        const petDetailsForm = document.querySelector('.pet-details-form');
        if (petDetailsForm && typeof MutationObserver !== 'undefined') {
            const observer = new MutationObserver((mutations) => {
                // Ignore mutations on the continue button - only care about form changes
                const isButtonChange = mutations.every(m =>
                    m.target.closest('.continue-btn') ||
                    m.target.classList.contains('continue-btn')
                );
                if (isButtonChange) return;

                clearTimeout(mutationTimeout);
                mutationTimeout = setTimeout(() => {
                    checkContinueBtnState();
                }, 400);
            });
            observer.observe(petDetailsForm, {
                attributes: true,
                attributeFilter: ['class'],
                subtree: true,
                characterData: false,
            });
        }

        // ---- Try to hook into custom component libraries' internal updates ----
        // For Furs custom components (pet-type, breeds, etc), they may use internal state
        const petTypeEl = document.getElementById('my-pet-type');
        const petBreedEl = document.getElementById('my-pet-breed');

        // Hook BEFORE library initialization to capture the real value setters
        if (petTypeEl) {
            const originalPetTypeValue = Object.getOwnPropertyDescriptor(Object.getPrototypeOf(petTypeEl), 'value');
            if (originalPetTypeValue && originalPetTypeValue.set) {
                Object.defineProperty(petTypeEl, 'value', {
                    get: originalPetTypeValue.get,
                    set: function(val) {
                        originalPetTypeValue.set.call(this, val);
                        console.log('[Hook] my-pet-type value changed to:', val);
                        checkContinueBtnState();
                    }
                });
            }
        }

        if (petBreedEl) {
            const originalPetBreedValue = Object.getOwnPropertyDescriptor(Object.getPrototypeOf(petBreedEl), 'value');
            if (originalPetBreedValue && originalPetBreedValue.set) {
                Object.defineProperty(petBreedEl, 'value', {
                    get: originalPetBreedValue.get,
                    set: function(val) {
                        originalPetBreedValue.set.call(this, val);
                        console.log('[Hook] my-pet-breed value changed to:', val);
                        checkContinueBtnState();
                    }
                });
            }
        }

        // ---- Continue button click ----
        const continueBtn = document.querySelector('.continue-btn');
        if (continueBtn) {
            continueBtn.addEventListener('click', function(e) {
                console.log('[Continue Click] Button clicked, active class:', continueBtn.classList.contains('active'));
                e.preventDefault();
                if (!continueBtn.classList.contains('active')) {
                    console.error('[Continue Click] Not active, showing alert');
                    alert('Please complete all required fields and select at least one add-on.');
                    return;
                }
                console.log('[Continue Click] Active! Proceeding to goToStep2()');
                goToStep2();
            });
        }

        // ---- Back button click ----
        document.addEventListener('click', function(e) {
            if (e.target && e.target.closest('#backToStep1Btn')) {
                goToStep1();
            }
        });
    });
</script>
@endsection
