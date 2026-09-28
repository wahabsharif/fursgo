@props(['payouts' => []])

@php
    $futureItems = $payouts['future_items'] ?? [];
    $allFutureItems = $payouts['future_items_all'] ?? $futureItems;
    $history = $payouts['history'] ?? [];
    $bank = $payouts['bank'] ?? [];
    $bankDigits = preg_replace('/\D+/', '', (string) ($bank['account_number'] ?? ''));
    $bankEnding = $bankDigits !== '' ? substr($bankDigits, -4) : 'Not added';
@endphp

<div class="earnings-payouts"
    x-data="{ bankModalOpen: false, frequencyModalOpen: false, futurePayoutsModalOpen: false }" x-effect="
        const modalOpen = bankModalOpen || frequencyModalOpen || futurePayoutsModalOpen;
        document.body.style.overflow = modalOpen ? 'hidden' : '';
        document.documentElement.style.overflow = modalOpen ? 'hidden' : '';
    " x-on:payout-bank-details-saved.window="bankModalOpen = false"
    x-on:payout-frequency-saved.window="frequencyModalOpen = false">
    <style>
        .earnings-payouts {
            width: 100%;
            padding: 2px;
            box-sizing: border-box;
            color: #3B3731;
            font-family: Lato, sans-serif;
        }

        .earnings-payouts-grid {
            display: grid;
            grid-template-columns: minmax(0, 610fr) minmax(320px, 400fr);
            grid-template-areas:
                "title ."
                "table right";
            column-gap: 20px;
            row-gap: 20px;
            align-items: start;
        }

        .earnings-payouts-history-title {
            grid-area: title;
        }

        .earnings-payouts-table-wrap {
            grid-area: table;
            min-width: 0;
        }

        .earnings-payouts-right {
            grid-area: right;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }

        .earnings-payouts-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .earnings-payouts-card,
        .earnings-payouts-panel {
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            background: #FFF;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
        }

        .earnings-payouts-card {
            min-height: 140px;
            padding: 21px 20px 16px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .earnings-payouts-label {
            margin: 0;
            color: #565149;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-value {
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-muted {
            margin-left: 8px;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-payouts-help {
            margin-top: 8px;
        }

        .earnings-payouts-help-toggle {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            border: 0;
            background: transparent;
            cursor: pointer;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin-top: 0;
            padding: 0 0 18px;
            border-bottom: 1px solid #D4D4D4;
            user-select: none;
            -webkit-user-select: none;
            -ms-user-select: none;
        }

        .earnings-payouts-help-toggle svg {
            flex-shrink: 0;
            transition: transform 0.25s ease;
        }

        .earnings-payouts-help-toggle.is-open svg {
            transform: rotate(180deg);
        }

        .earnings-payouts-help-panel {
            display: grid;
            grid-template-rows: 0fr;
            opacity: 0;
            overflow: hidden;
            transition: grid-template-rows 0.35s ease, opacity 0.25s ease;
        }

        .earnings-payouts-help-panel.is-open {
            grid-template-rows: 1fr;
            opacity: 1;
        }

        .earnings-payouts-help-panel-inner {
            min-height: 0;
            overflow: hidden;
        }

        .earnings-payouts-help-panel-inner .earnings-payouts-help-copy {
            transform: translateY(-6px);
            transition: transform 0.35s ease;
        }

        .earnings-payouts-help-panel.is-open .earnings-payouts-help-copy {
            transform: translateY(0);
        }

        .earnings-payouts-help-copy {
            max-width: 720px;
            padding-top: 18px;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 1.45;
        }

        .earnings-payouts-help-copy p {
            margin: 0 0 14px;
        }

        .earnings-payouts-history-title {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 24px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            padding-bottom: 16px;
            border-bottom: 1px solid #D4D4D4;
        }

        .earnings-payouts-table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 10px;
            border: 1px solid #F6F5F5;
            background: #FDFDFD;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.10);
        }

        .earnings-payouts-table {
            width: 100%;
            min-width: 680px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .earnings-payouts-table th,
        .earnings-payouts-table td {
            height: 68px;
            padding: 0 16px;
            border: 0;
            border-bottom: 1px solid #E2E2E2;
            text-align: left;
            font-size: 14px;
            vertical-align: middle;
        }

        .earnings-payouts-table th {
            height: 50px;
            background: rgba(59, 55, 49, 0.02);
            color: #000;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-table td {
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-payouts-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 84px;
            min-height: 30px;
            padding: 0 12px;
            border-radius: 100px;
            background: rgba(209, 235, 154, 0.20);
            color: #8BAE40;
            text-align: center;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .earnings-payouts-invoice-cell {
            border-left: 1px solid #E2E2E2 !important;
            text-align: center !important;
        }

        .earnings-payouts-icon-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: 1px solid #E2E2E2;
            border-radius: 50%;
            background: #FFF;
            padding: 0;
            cursor: pointer;
            color: #3B3731;
            transition: border-color .15s ease, background-color .15s ease;
        }

        .earnings-payouts-icon-btn:hover {
            border-color: #FFC97A;
            background: #FFF8EA;
        }

        .earnings-payouts-empty {
            color: #9D9B98;
            text-align: center !important;
        }

        .earnings-payouts-panel {
            overflow: hidden;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.06);
        }

        .earnings-payouts-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 54px;
            padding: 15px 20px;
            border-bottom: 1px solid #E2E2E2;
            background: rgba(59, 55, 49, 0.02);
        }

        .earnings-payouts-panel-title {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-panel-body {
            padding: 18px 20px 20px;
        }

        .earnings-payouts-future-total {
            margin: 0 0 1rem;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-future-list {
            display: flex;
            flex-direction: column;
            gap: 0.35rem;
            margin-bottom: 1rem;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-panel-link {
            display: inline-flex;
            color: #AFCD6F;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-decoration-line: underline;
            text-decoration-style: solid;
            text-decoration-skip-ink: auto;
            text-decoration-thickness: auto;
            text-underline-offset: auto;
            text-underline-position: from-font;
            transition: color .15s ease;
        }

        .earnings-payouts-panel-link:hover {
            color: #8BAE40;
        }

        .earnings-payouts-frequency {
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: 1.55;
        }

        .earnings-payouts-verified {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: #AFCD6F;
            font-size: 14px;
            font-weight: 700;
        }

        .earnings-payouts-verified.is-unverified {
            color: #9D9B98;
        }

        .earnings-payouts-bank-copy {
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: 1.55;
        }

        .earnings-payouts-frequency-link {
            margin-top: 28px;
        }

        .earnings-payouts-bank-link {
            margin-top: 18px;
        }

        .earnings-payouts-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 100100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(59, 55, 49, 0.10);
            font-family: Lato;
        }

        .earnings-payouts-modal-overlay-enter,
        .earnings-payouts-modal-overlay-leave {
            transition: opacity 0.2s ease;
        }

        .earnings-payouts-modal-overlay-enter-start,
        .earnings-payouts-modal-overlay-leave-end {
            opacity: 0;
        }

        .earnings-payouts-modal-overlay-enter-end,
        .earnings-payouts-modal-overlay-leave-start {
            opacity: 1;
        }

        .earnings-payouts-modal-card {
            width: min(500px, 100%);
            border-radius: 10px;
            border: 1px solid #CBDCE8;
            background: #F8F8F8;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
            overflow: visible;
        }

        .earnings-payouts-modal-card--scrollable {
            max-height: calc(100vh - 2rem);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        .earnings-payouts-modal-card-enter {
            transition: opacity 0.22s ease, transform 0.22s ease;
        }

        .earnings-payouts-modal-card-leave {
            transition: opacity 0.16s ease, transform 0.16s ease;
        }

        .earnings-payouts-modal-card-enter-start,
        .earnings-payouts-modal-card-leave-end {
            opacity: 0;
            transform: translateY(12px) scale(0.96);
        }

        .earnings-payouts-modal-card-enter-end,
        .earnings-payouts-modal-card-leave-start {
            opacity: 1;
            transform: translateY(0) scale(1);
        }

        .earnings-payouts-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            border-radius: 10px 10px 0 0;
            border-bottom: 1px solid #CBDCE8;
            background: rgba(203, 220, 232, 0.20);
            padding: 1.2rem 1.65rem;
        }

        .earnings-payouts-modal-title {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .earnings-payouts-modal-copy {
            margin: 0.25rem 0 0;
            color: #9D9B98;
            font-size: 14px;
            line-height: 1.4;
        }

        .earnings-payouts-modal-close {
            border: none;
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .earnings-payouts-modal-body {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
            padding: 1.2rem 1.65rem;
        }

        .earnings-payouts-modal-body--scrollable {
            display: block;
            min-height: 0;
            overflow-y: auto;
            overscroll-behavior: contain;
        }

        .earnings-payouts-modal-field {
            display: flex;
            flex-direction: column;
            gap: 0.45rem;
        }

        .earnings-payouts-modal-field.is-full {
            grid-column: 1 / -1;
        }

        .earnings-payouts-modal-field .furs-dd.is-open {
            z-index: 20;
        }

        .earnings-payouts-modal-field .furs-dd__panel {
            z-index: 100101;
        }

        .earnings-payouts-modal-field label {
            color: #3B3731;
            font-size: 14px;
            font-weight: 700;
        }

        .earnings-payouts-modal-field input,
        .earnings-payouts-modal-field select {
            width: 100%;
            border: 1px solid #D4D4D4;
            border-radius: 10px;
            padding: 0.85rem 0.95rem;
            color: #3B3731;
            font: inherit;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }

        .earnings-payouts-modal-field input:focus,
        .earnings-payouts-modal-field select:focus {
            border-color: #AFCD6F;
            box-shadow: 0 0 0 3px rgba(175, 205, 111, 0.18);
        }

        .earnings-payouts-modal-error {
            color: #FF6E6E;
            font-size: 12px;
        }

        .earnings-payouts-modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            padding: 0 1.65rem 1.5rem;
        }

        .earnings-payouts-modal-btn {
            min-width: 118px;
            border-radius: 999px;
            border: 1px solid #D4D4D4;
            padding: 0.75rem 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .earnings-payouts-modal-btn:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .earnings-payouts-modal-btn--secondary {
            background: #FFF;
            color: #3B3731;
        }

        .earnings-payouts-modal-btn--primary {
            border-color: #AFCD6F;
            background: #AFCD6F;
            color: #FFF;
        }

        .earnings-payouts-modal-btn:not(:disabled):active {
            transform: translateY(1px);
        }

        .earnings-payouts-modal-spinner {
            width: 14px;
            height: 14px;
            border: 2px solid rgba(255, 255, 255, 0.55);
            border-top-color: #FFF;
            border-radius: 999px;
            display: inline-block;
            margin-right: 0.45rem;
            vertical-align: -2px;
            animation: earnings-payouts-modal-spin 0.75s linear infinite;
        }

        @keyframes earnings-payouts-modal-spin {
            to {
                transform: rotate(360deg);
            }
        }

        @media (max-width: 1180px) {
            .earnings-payouts-grid {
                grid-template-columns: 1fr;
                grid-template-areas:
                    "title"
                    "table"
                    "right";
            }
        }

        @media (max-width: 640px) {
            .earnings-payouts {
                padding: 0;
            }

            .earnings-payouts-summary {
                grid-template-columns: 1fr;
            }

            .earnings-payouts-card {
                min-height: 120px;
            }

            .earnings-payouts-help-copy {
                font-size: 14px;
            }

            .earnings-payouts-panel-head,
            .earnings-payouts-panel-body {
                padding-left: 16px;
                padding-right: 16px;
            }

            .earnings-payouts-modal-body {
                grid-template-columns: 1fr;
            }
        }

        /* Figma 8.4 — Groomer / Business Profile / Earnings: Payout */
        .earnings-payouts {
            padding: 0;
        }

        .earnings-payouts-cashout {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 24px;
            min-height: 134px;
            padding: 20px;
            border: 1px solid #C9DDA0;
            border-radius: 10px;
            background: #F9FBF4;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
        }

        .earnings-payouts-cashout__label {
            margin: 0 0 10px;
            color: #A1BF63;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 700;
            line-height: normal;
            text-transform: uppercase;
        }

        .earnings-payouts-cashout__amount {
            margin: 0 0 10px;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-cashout__note,
        .earnings-payouts-cashout__fee {
            margin: 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-payouts-cashout__action {
            display: flex;
            flex: 0 0 auto;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }

        .earnings-payouts-cashout__button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-width: 159px;
            height: 48px;
            padding: 0 20px;
            border: 0;
            border-radius: 100px;
            background: #A1BF63;
            box-shadow: 0 2px 10px 0 #D3D3D3;
            color: #FFF;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
        }

        .earnings-payouts-cashout__button img {
            width: 11.5px;
            height: 15px;
            max-width: 11.5px;
            display: block;
            flex: 0 0 11.5px;
        }

        .earnings-payouts-cashout__fee {
            color: #AFCD6F;
            font-size: 14px;
            font-weight: 500;
        }

        .earnings-payouts-summary {
            margin-top: 20px;
            gap: 20px;
        }

        .earnings-payouts-card {
            min-height: 140px;
            padding: 23px 20px 18px;
            border: 1px solid #E2E2E2;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
        }

        .earnings-payouts-card__bottom {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .earnings-payouts-card .earnings-payouts-muted {
            margin: 0;
        }

        .earnings-payouts-grid {
            grid-template-columns: minmax(0, 610fr) minmax(320px, 400fr);
            grid-template-areas:
                "title ."
                "table right";
            column-gap: 20px;
            row-gap: 20px;
            margin-top: 40px;
        }

        .earnings-payouts-right {
            gap: 20px;
        }

        .earnings-payouts-history-title {
            margin: 0;
            padding: 0;
            border: 0;
            font-family: "Playfair Display", serif;
            font-size: 28px;
            font-weight: 600;
        }

        .earnings-payouts-table-wrap {
            border: 0;
            background: #FDFDFD;
            box-shadow:
                inset 0 0 0 1px #F6F5F5,
                0 0 15px 2px rgba(59, 55, 49, 0.10);
        }

        .earnings-payouts-table {
            min-width: 600px;
        }

        .earnings-payouts-table th,
        .earnings-payouts-table td {
            height: 76px;
            padding: 0 20px;
            font-size: 16px;
            white-space: nowrap;
            border-bottom: 0;
        }

        .earnings-payouts-table th {
            height: 50px;
            color: #948F88;
            font-size: 16px;
            font-weight: 600;
            background: #F6F5F5;
        }

        .earnings-payouts-table th:nth-child(1) {
            width: 21.803%;
        }

        .earnings-payouts-table th:nth-child(2) {
            width: 19.672%;
        }

        .earnings-payouts-table th:nth-child(3) {
            width: 24.098%;
        }

        .earnings-payouts-table th:nth-child(4) {
            width: 20.164%;
        }

        .earnings-payouts-table th:nth-child(5) {
            width: 14.263%;
        }

        .earnings-payouts-table tbody tr:not(:last-child) {
            background-image: linear-gradient(to right,
                    transparent 20px,
                    #E2E2E2 20px,
                    #E2E2E2 calc(100% - 20px),
                    transparent calc(100% - 20px));
            background-position: bottom left;
            background-repeat: no-repeat;
            background-size: 100% 1px;
        }

        .earnings-payouts-status {
            min-width: 85px;
            min-height: 25px;
            padding: 0 10px;
            background: rgba(201, 221, 160, 0.20);
            color: #AFCD6F;
            font-size: 14px;
            font-weight: 500;
        }

        .earnings-payouts-view-cell {
            text-align: left !important;
        }

        .earnings-payouts-icon-btn {
            width: 36px;
            height: 36px;
            margin-left: 1px;
            border: 0;
            background: transparent;
        }

        .earnings-payouts-icon-btn:hover {
            border-color: transparent;
            background: transparent;
        }

        .earnings-payouts-icon-btn img {
            display: block;
            width: 36px;
            height: 36px;
            max-width: 36px;
        }

        .earnings-payouts-help {
            margin: 0;
        }

        .earnings-payouts-help-toggle {
            padding: 4px 0 18px;
            font-size: 20px;
        }

        .earnings-payouts-help-copy {
            max-width: none;
            padding-top: 20px;
            font-size: 14px;
            line-height: 1.35;
        }

        .earnings-payouts-help-copy p {
            margin-bottom: 14px;
        }

        .earnings-payouts-panel {
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            background: #FFF;
            box-shadow: none;
        }

        .earnings-payouts-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: auto;
            padding: 20px 20px 0;
            border: 0;
            background: #FFF;
        }

        .earnings-payouts-panel--future {
            height: 140px;
            min-height: 140px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            box-sizing: border-box;
        }

        .earnings-payouts-panel--future .earnings-payouts-panel-head {
            flex-shrink: 0;
        }

        .earnings-payouts-panel-title {
            margin: 0;
            color: #565149;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-panel-body {
            padding: 20px;
        }

        .earnings-payouts-panel--future .earnings-payouts-panel-body {
            padding: 20px 20px 20px;
            flex: 1;
            min-height: 0;
            box-sizing: border-box;
        }

        .earnings-payouts-panel-link,
        .earnings-payouts-panel-link:hover {
            color: #FFC97A;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 600;
            line-height: normal;
            text-decoration: none;
        }

        .earnings-payouts-future-list {
            display: flex;
            flex-direction: column;
            gap: 0;
            margin: 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 23px;
        }

        .earnings-payouts-future-row {
            display: grid;
            grid-template-columns: 78px 1fr auto;
            column-gap: 24px;
            align-items: center;
            min-height: 23px;
            padding: 0 0 5px;
            margin-bottom: 5px;
            border-bottom: 1px solid #E2E2E2;
            box-sizing: border-box;
        }

        .earnings-payouts-future-row:last-child {
            padding-bottom: 0;
            margin-bottom: 0;
            border-bottom: 0;
        }

        .earnings-payouts-future-row__date {
            color: #9D9B98;
            font-weight: 400;
            white-space: nowrap;
        }

        .earnings-payouts-future-row__amount {
            color: #3B3731;
            font-weight: 400;
            text-align: left;
            justify-self: start;
            white-space: nowrap;
        }

        .earnings-payouts-future-row__arrival {
            color: #9D9B98;
            font-weight: 400;
            text-align: right;
            white-space: nowrap;
        }

        .earnings-payouts-frequency {
            color: #3B3731;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-payouts-frequency strong {
            font-weight: 700;
        }

        .earnings-payouts-bank-copy {
            color: #9D9B98;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.45;
        }

        .earnings-payouts-bank-holder {
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            gap: 16px;
            margin-top: 2px;
            color: #3B3731;
        }

        .earnings-payouts-verified {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            color: #AFCD6F;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
        }

        .earnings-payouts-verified.is-unverified {
            color: #9D9B98;
        }

        .earnings-payouts-frequency-link,
        .earnings-payouts-bank-link {
            margin-top: 0;
        }

        .earnings-payouts-freq-modal {
            width: 610px;
            max-width: calc(100vw - 32px);
            padding: 0;
            border: 0;
            background: #FBFBFB;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: none;
        }

        .earnings-payouts .sr-only {
            position: absolute;
            width: 1px;
            height: 1px;
            padding: 0;
            margin: -1px;
            overflow: hidden;
            clip: rect(0, 0, 0, 0);
            white-space: nowrap;
            border: 0;
        }

        .earnings-payouts-freq-modal__head {
            position: relative;
            padding: 20px 30px 0;
        }

        .earnings-payouts-freq-modal__title {
            margin: 0 0 8px;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 28px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-freq-modal__copy {
            margin: 0;
            max-width: 324px;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .earnings-payouts-freq-modal__close {
            position: absolute;
            top: 30px;
            right: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 14.5px;
            height: 14.5px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .earnings-payouts-freq-modal__close img {
            width: 14.5px;
            height: 14.5px;
            max-width: 14.5px;
            display: block;
        }

        .earnings-payouts-freq-modal__body {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px 20px 0;
        }

        .earnings-payouts-freq-option {
            display: block;
            width: 100%;
            margin: 0;
            padding: 20px;
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            background: #FFF;
            cursor: pointer;
            box-sizing: border-box;
            text-align: left;
        }

        .earnings-payouts-freq-option.is-selected {
            border-color: #FFD88C;
            background: rgba(255, 201, 122, 0.10);
        }

        .earnings-payouts-freq-option.is-monthly {
            min-height: 172px;
        }

        .earnings-payouts-freq-option__row {
            display: flex;
            align-items: flex-start;
            gap: 20px;
        }

        .earnings-payouts-freq-radio {
            position: relative;
            flex: 0 0 20px;
            width: 20px;
            height: 20px;
            margin-top: 10px;
            border: 1.5px solid #E2E2E2;
            border-radius: 100px;
            background: #FFF;
            box-sizing: border-box;
        }

        .earnings-payouts-freq-option.is-selected>.earnings-payouts-freq-option__row>.earnings-payouts-freq-radio {
            border-width: 1px;
            border-color: #FFD88C;
        }

        .earnings-payouts-freq-option.is-selected>.earnings-payouts-freq-option__row>.earnings-payouts-freq-radio::after {
            content: '';
            position: absolute;
            top: 2.5px;
            left: 2.5px;
            width: 13.333px;
            height: 13.333px;
            border-radius: 100px;
            background: #FFD88C;
        }

        .earnings-payouts-freq-option__text {
            min-width: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
        }

        .earnings-payouts-freq-option__text strong {
            display: block;
            margin-bottom: 2px;
            font-size: 18px;
            font-weight: 700;
            line-height: normal;
        }

        .earnings-payouts-freq-option__text span {
            display: block;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-payouts-freq-monthly {
            display: grid;
            grid-template-columns: minmax(0, 232.5px) minmax(0, 1fr);
            gap: 20px;
            align-items: end;
            margin-top: 20px;
            padding-left: 40px;
        }

        .earnings-payouts-freq-date-label {
            margin: 0 0 10px;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-freq-date {
            position: relative;
            width: 232.5px;
            max-width: 100%;
        }

        .earnings-payouts-freq-date input {
            width: 100%;
            height: 42px;
            padding: 0 42px 0 12px;
            border: 1px solid #DDD;
            border-radius: 5px;
            background: #FFF;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 400;
            line-height: 25px;
            box-sizing: border-box;
            -webkit-appearance: none;
            appearance: none;
        }

        .earnings-payouts-freq-date input::-webkit-calendar-picker-indicator {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .earnings-payouts-freq-date input::-webkit-inner-spin-button,
        .earnings-payouts-freq-date input::-webkit-clear-button {
            display: none;
            -webkit-appearance: none;
        }

        .earnings-payouts-freq-date input:focus {
            outline: none;
            border-color: #FFC97A;
        }

        .earnings-payouts-freq-date img {
            position: absolute;
            top: 50%;
            right: 14px;
            width: 14px;
            height: 14px;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .earnings-payouts-freq-suboptions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            padding-bottom: 4px;
        }

        .earnings-payouts-freq-suboption {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 0;
            padding: 0;
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
            cursor: pointer;
            text-align: left;
        }

        .earnings-payouts-freq-suboption .earnings-payouts-freq-radio {
            margin-top: 0;
        }

        .earnings-payouts-freq-suboption.is-selected .earnings-payouts-freq-radio {
            border-width: 1px;
            border-color: #FFD88C;
        }

        .earnings-payouts-freq-suboption.is-selected .earnings-payouts-freq-radio::after {
            content: '';
            position: absolute;
            top: 2.5px;
            left: 2.5px;
            width: 13.333px;
            height: 13.333px;
            border-radius: 100px;
            background: #FFD88C;
        }

        .earnings-payouts-freq-modal__actions {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            padding: 20px 20px 20px;
        }

        .earnings-payouts-freq-modal__btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 42px;
            border-radius: 96px;
            font-family: Lato, sans-serif;
            font-size: 16px;
            cursor: pointer;
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .earnings-payouts-freq-modal__btn:disabled {
            cursor: wait;
            opacity: 0.65;
        }

        .earnings-payouts-freq-modal__btn:not(:disabled):active {
            transform: translateY(1px);
        }

        .earnings-payouts-freq-modal__btn--cancel {
            width: 88px;
            border: 1px solid #E2E2E2;
            background: #FFF;
            color: #3B3731;
            font-weight: 400;
        }

        .earnings-payouts-freq-modal__btn--save {
            min-width: 152px;
            padding: 0 20px;
            border: 0;
            background: #FFC97A;
            box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
            color: #FFF;
            font-weight: 600;
        }

        .earnings-payouts-bank-modal {
            width: 399px;
            max-width: calc(100vw - 32px);
            padding: 0;
            border: 0;
            background: #FBFBFB;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: none;
        }

        .earnings-payouts-bank-modal__head {
            position: relative;
            padding: 20px 30px 0 20px;
        }

        .earnings-payouts-bank-modal__title {
            margin: 0 0 6px;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 28px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-bank-modal__copy {
            margin: 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .earnings-payouts-bank-modal__close {
            position: absolute;
            top: 30px;
            right: 30px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 14.5px;
            height: 14.5px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .earnings-payouts-bank-modal__close img {
            width: 14.5px;
            height: 14.5px;
            max-width: 14.5px;
            display: block;
        }

        .earnings-payouts-bank-modal__body {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 20px 20px 0;
        }

        .earnings-payouts-bank-field {
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-width: 0;
        }

        .earnings-payouts-bank-field__label {
            margin: 0;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-bank-field__label span {
            color: #9D9B98;
            font-weight: 400;
        }

        .earnings-payouts-bank-field__control {
            position: relative;
        }

        .earnings-payouts-bank-field__control input {
            width: 100%;
            height: 48px;
            padding: 0 44px 0 20px;
            border: 1px solid #D4D4D4;
            border-radius: 10px;
            background: #FFF;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
            box-sizing: border-box;
            outline: none;
            transition: border-color 0.2s ease;
        }

        .earnings-payouts-bank-field__control.is-plain input {
            padding-right: 20px;
        }

        .earnings-payouts-bank-field__control input:focus {
            border-color: #FFC97A;
        }

        .earnings-payouts-bank-field__check {
            position: absolute;
            top: 50%;
            right: 20px;
            width: 19px;
            height: 19px;
            transform: translateY(-50%);
            pointer-events: none;
            opacity: 0;
            transition: opacity 0.15s ease;
        }

        .earnings-payouts-bank-field__control.is-filled .earnings-payouts-bank-field__check {
            opacity: 1;
        }

        .earnings-payouts-bank-field__hint {
            margin: 0;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-payouts-bank-field-row {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .earnings-payouts-bank-modal__actions {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            padding: 20px;
        }

        @media (max-width: 480px) {
            .earnings-payouts-bank-field-row {
                grid-template-columns: 1fr;
            }

            .earnings-payouts-bank-modal__actions {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 1180px) {
            .earnings-payouts-grid {
                grid-template-columns: minmax(0, 1fr);
                grid-template-areas:
                    "title"
                    "table"
                    "right";
            }

            .earnings-payouts-right {
                display: grid;
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .earnings-payouts-help {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 720px) {
            .earnings-payouts-cashout {
                align-items: flex-start;
                flex-direction: column;
            }

            .earnings-payouts-cashout__action,
            .earnings-payouts-cashout__button {
                width: 100%;
            }

            .earnings-payouts-summary,
            .earnings-payouts-right {
                grid-template-columns: minmax(0, 1fr);
            }

            .earnings-payouts-help {
                grid-column: auto;
            }

            .earnings-payouts-freq-monthly {
                grid-template-columns: 1fr;
                padding-left: 0;
            }
        }
    </style>

    <section class="earnings-payouts-cashout" aria-label="Available cash out balance">
        <div>
            <p class="earnings-payouts-cashout__label">Available to cash out</p>
            <p class="earnings-payouts-cashout__amount">
                £{{ number_format((float) ($payouts['pending_amount'] ?? 0), 2) }}</p>
            <p class="earnings-payouts-cashout__note">
                Otherwise paid automatically on
                {{ $payouts['next_payout_friendly'] ?? ($payouts['next_payout_date'] ?? '') }}
            </p>
        </div>
        <div class="earnings-payouts-cashout__action">
            <button type="button" class="earnings-payouts-cashout__button" @click.prevent
                aria-label="Cash out available balance now">
                <img src="{{ asset('images/business-hub/icon-earnings-payout-cashout.svg') }}" width="11.5" height="15" alt="">
                Cash out now
            </button>
            <p class="earnings-payouts-cashout__fee">£0.50 fee · same-day</p>
        </div>
    </section>

    <div class="earnings-payouts-summary">
        <article class="earnings-payouts-card">
            <p class="earnings-payouts-label">Pending Payouts</p>
            <div class="earnings-payouts-card__bottom">
                <div class="earnings-payouts-value">£{{ number_format((float) ($payouts['pending_amount'] ?? 0), 2) }}
                </div>
                <p class="earnings-payouts-muted">{{ $payouts['next_payout_short'] ?? 'Next auto pay out' }}</p>
            </div>
        </article>

        <article class="earnings-payouts-card">
            <p class="earnings-payouts-label">Total Payouts</p>
            <div class="earnings-payouts-card__bottom">
                <div class="earnings-payouts-value">£{{ number_format((float) ($payouts['total_amount'] ?? 0), 2) }}
                </div>
                <p class="earnings-payouts-muted">All time</p>
            </div>
        </article>
    </div>

    <div class="earnings-payouts-grid">
        <h3 class="earnings-payouts-history-title">Pay-out history</h3>

        <div class="earnings-payouts-table-wrap">
            <table class="earnings-payouts-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Amount</th>
                        <th>Status</th>
                        <th>Service</th>
                        <th class="earnings-payouts-view-cell">View</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        <tr>
                            <td>{{ $item['date'] }}</td>
                            <td>£{{ number_format((float) $item['amount'], 2) }}</td>
                            <td><span class="earnings-payouts-status">{{ $item['status'] }}</span></td>
                            <td>{{ $item['reference'] }}</td>
                            <td class="earnings-payouts-view-cell">
                                @if (!empty($item['invoice_url']))
                                    <button type="button" class="earnings-payouts-icon-btn"
                                        data-invoice-url="{{ $item['invoice_url'] }}"
                                        onclick="window.open(this.dataset.invoiceUrl, '_blank', 'noopener')"
                                        aria-label="View payout invoice">
                                        <img src="{{ asset('images/business-hub/icon-earnings-tx-view.svg') }}" alt="">
                                    </button>
                                @else
                                    <span class="earnings-payouts-muted">N/A</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="earnings-payouts-empty">No payout history available yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="earnings-payouts-right">
            <div class="earnings-payouts-help" x-data="{ helpOpen: true }">
                <button type="button" class="earnings-payouts-help-toggle" :class="{ 'is-open': helpOpen }"
                    :aria-expanded="helpOpen.toString()" aria-controls="earnings-payouts-help-copy" @mousedown.prevent
                    @click="helpOpen = !helpOpen">
                    <span>How do Payouts work?</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="9" viewBox="0 0 15 9" fill="none">
                        <path d="M1 8L7.5 1L14 8" stroke="#9D9B98" stroke-width="1.5" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                </button>

                <div id="earnings-payouts-help-copy" class="earnings-payouts-help-panel"
                    :class="{ 'is-open': helpOpen }">
                    <div class="earnings-payouts-help-panel-inner">
                        <div class="earnings-payouts-help-copy">
                            <p>With Fursgo, you can choose to receive your grooming earnings and tips whenever it suits
                                you, instead of waiting for the usual weekly payout.</p>
                            <p>Your regular payment will still be sent every Tuesday, but you also have the option to
                                withdraw your available balance earlier. Just open the Earnings section in your Fursgo
                                account, tap your current balance, and confirm the cash out.</p>
                            <p>If you request a cash out before 17:30, Monday to Friday, the money will normally arrive
                                in your bank account the same day. Requests made after 17:30 or at the weekend are
                                usually processed on the next working day.</p>
                            <p>When you use cash out, a £0.50 transaction fee is deducted from your grooming fees in
                                exchange for receiving your money ahead of the standard weekly cycle. This will appear
                                on your statement as “Transaction Fee”.</p>
                            <p>From time to time, cash out may be temporarily unavailable, for example due to bank
                                checks or system issues. If that happens, your earnings will still be included in your
                                regular Tuesday payment as usual.</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="earnings-payouts-panel earnings-payouts-panel--future">
                <div class="earnings-payouts-panel-head">
                    <h3 class="earnings-payouts-panel-title">Future Payouts</h3>
                    <a href="#" class="earnings-payouts-panel-link"
                        @click.prevent="futurePayoutsModalOpen = true">View more</a>
                </div>
                <div class="earnings-payouts-panel-body">
                    <div class="earnings-payouts-future-list">
                        @forelse($futureItems as $item)
                            <div class="earnings-payouts-future-row">
                                <span class="earnings-payouts-future-row__date">{{ $item['date'] }}</span>
                                <span class="earnings-payouts-future-row__amount">£{{ number_format((float) $item['amount'], 2) }}</span>
                                <span class="earnings-payouts-future-row__arrival">est. arrival date {{ $item['arrival_date_short'] ?? '--/--' }}</span>
                            </div>
                        @empty
                            <span class="earnings-payouts-muted">No scheduled future payouts.</span>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="earnings-payouts-panel">
                <div class="earnings-payouts-panel-head">
                    <h3 class="earnings-payouts-panel-title">Payout Frequency</h3>
                    <a href="#" class="earnings-payouts-panel-link earnings-payouts-frequency-link"
                        @click.prevent="$wire.refreshPayoutFrequency(); frequencyModalOpen = true">Change Frequency</a>
                </div>
                <div class="earnings-payouts-panel-body">
                    <div class="earnings-payouts-frequency">
                        <strong>{{ $payouts['frequency'] ?? 'Weekly' }}</strong>
                        — next on {{ $payouts['next_payout_date'] ?? now()->next('Tuesday')->format('d F Y') }}
                    </div>
                </div>
            </div>

            <div class="earnings-payouts-panel">
                <div class="earnings-payouts-panel-head">
                    <h3 class="earnings-payouts-panel-title">Bank Account</h3>
                    <a href="#" class="earnings-payouts-panel-link earnings-payouts-bank-link"
                        @click.prevent="$wire.refreshPayoutBankDetails(); bankModalOpen = true">Update Bank Details</a>
                </div>
                <div class="earnings-payouts-panel-body">
                    <div class="earnings-payouts-bank-copy">
                        <div>{{ $bank['name'] ?? 'Bank details' }} - ending {{ $bank['ending'] ?? $bankEnding }}</div>
                        <div class="earnings-payouts-bank-holder">
                            <span>{{ $bank['account_holder'] ?? 'Not added' }}</span>
                            <span class="earnings-payouts-verified {{ !empty($bank['verified']) ? '' : 'is-unverified' }}">
                                <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none">
                                    <circle cx="5" cy="5" r="5"
                                        fill="{{ !empty($bank['verified']) ? '#AFCD6F' : '#D4D4D4' }}" />
                                    <path d="M2.9 5.1L4.4 6.55L7.25 3.35" stroke="white" stroke-width="1"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                {{ !empty($bank['verified']) ? 'Verified' : 'Not verified' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <template x-teleport="body">
        <div class="earnings-payouts-modal-overlay" x-cloak x-show="bankModalOpen"
            x-transition:enter="earnings-payouts-modal-overlay-enter"
            x-transition:enter-start="earnings-payouts-modal-overlay-enter-start"
            x-transition:enter-end="earnings-payouts-modal-overlay-enter-end"
            x-transition:leave="earnings-payouts-modal-overlay-leave"
            x-transition:leave-start="earnings-payouts-modal-overlay-leave-start"
            x-transition:leave-end="earnings-payouts-modal-overlay-leave-end"
            @keydown.escape.window="bankModalOpen = false" @click.self="bankModalOpen = false">
            <form class="earnings-payouts-modal-card earnings-payouts-bank-modal" x-show="bankModalOpen"
                x-transition:enter="earnings-payouts-modal-card-enter"
                x-transition:enter-start="earnings-payouts-modal-card-enter-start"
                x-transition:enter-end="earnings-payouts-modal-card-enter-end"
                x-transition:leave="earnings-payouts-modal-card-leave"
                x-transition:leave-start="earnings-payouts-modal-card-leave-start"
                x-transition:leave-end="earnings-payouts-modal-card-leave-end"
                wire:submit.prevent="updatePayoutBankDetails" role="dialog" aria-modal="true"
                aria-labelledby="earnings-payouts-bank-modal-title"
                x-data="{
                    holderName: @entangle('payoutAccountHolderName'),
                    sortCode: @entangle('payoutSortCode'),
                    accountNumber: @entangle('payoutAccountNumber'),
                    bankName: @entangle('payoutBank'),
                    accountNumberEditing: false,
                    maskedAccountNumber() {
                        const digits = String(this.accountNumber || '').replace(/\D+/g, '');
                        if (!digits) return '';
                        return '•••• ' + digits.slice(-4);
                    },
                    accountNumberDisplay() {
                        return this.accountNumberEditing ?
                            (this.accountNumber || '') :
                            this.maskedAccountNumber();
                    },
                    startAccountNumberEdit(event) {
                        this.accountNumberEditing = true;
                        this.$nextTick(() => {
                            event.target.value = this.accountNumber || '';
                        });
                    },
                    endAccountNumberEdit(event) {
                        this.accountNumber = event.target.value;
                        this.accountNumberEditing = false;
                    },
                }"
                x-effect="if (!bankModalOpen) accountNumberEditing = false">
                <div class="earnings-payouts-bank-modal__head">
                    <h3 class="earnings-payouts-bank-modal__title" id="earnings-payouts-bank-modal-title">Update bank details</h3>
                    <p class="earnings-payouts-bank-modal__copy">Where your pay outs are sent.</p>
                    <button type="button" class="earnings-payouts-bank-modal__close" @click="bankModalOpen = false"
                        aria-label="Close bank details modal">
                        <img src="{{ asset('images/business-hub/icon-earnings-payout-bank-close.svg') }}" width="14.5" height="14.5" alt="">
                    </button>
                </div>

                <div class="earnings-payouts-bank-modal__body">
                    <div class="earnings-payouts-bank-field">
                        <label class="earnings-payouts-bank-field__label" for="payout-account-holder">Account holder name</label>
                        <div class="earnings-payouts-bank-field__control" :class="{ 'is-filled': (holderName || '').trim().length > 0 }">
                            <input id="payout-account-holder" type="text" x-model="holderName" placeholder="Sarah Grooming Studio">
                            <img class="earnings-payouts-bank-field__check"
                                src="{{ asset('images/business-hub/icon-earnings-payout-bank-check.svg') }}" width="19" height="19" alt="">
                        </div>
                        <p class="earnings-payouts-bank-field__hint">Must match the name on the account.</p>
                        @error('payoutAccountHolderName')
                            <span class="earnings-payouts-modal-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="earnings-payouts-bank-field-row">
                        <div class="earnings-payouts-bank-field">
                            <label class="earnings-payouts-bank-field__label" for="payout-sort-code">Sort code</label>
                            <div class="earnings-payouts-bank-field__control is-plain">
                                <input id="payout-sort-code" type="text" x-model="sortCode" placeholder="20-00-00">
                            </div>
                            @error('payoutSortCode')
                                <span class="earnings-payouts-modal-error">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="earnings-payouts-bank-field">
                            <label class="earnings-payouts-bank-field__label" for="payout-account-number">Account number</label>
                            <div class="earnings-payouts-bank-field__control is-plain">
                                <input id="payout-account-number" type="text"
                                    :value="accountNumberDisplay()"
                                    @focus="startAccountNumberEdit($event)"
                                    @blur="endAccountNumberEdit($event)"
                                    @input="if (accountNumberEditing) accountNumber = $event.target.value"
                                    autocomplete="off"
                                    placeholder="•••• 4821">
                            </div>
                            @error('payoutAccountNumber')
                                <span class="earnings-payouts-modal-error">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>

                    <div class="earnings-payouts-bank-field">
                        <label class="earnings-payouts-bank-field__label" for="payout-bank">Bank name <span>(optional)</span></label>
                        <div class="earnings-payouts-bank-field__control" :class="{ 'is-filled': (bankName || '').trim().length > 0 }">
                            <input id="payout-bank" type="text" x-model="bankName" placeholder="Barclays">
                            <img class="earnings-payouts-bank-field__check"
                                src="{{ asset('images/business-hub/icon-earnings-payout-bank-check.svg') }}" width="19" height="19" alt="">
                        </div>
                        @error('payoutBank')
                            <span class="earnings-payouts-modal-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="earnings-payouts-bank-modal__actions">
                    <button type="button" class="earnings-payouts-freq-modal__btn earnings-payouts-freq-modal__btn--cancel"
                        @click="bankModalOpen = false">Cancel</button>
                    <button type="submit" class="earnings-payouts-freq-modal__btn earnings-payouts-freq-modal__btn--save"
                        wire:loading.attr="disabled" wire:target="updatePayoutBankDetails">
                        <span wire:loading.remove wire:target="updatePayoutBankDetails">Save Details</span>
                        <span wire:loading wire:target="updatePayoutBankDetails">
                            <span class="earnings-payouts-modal-spinner" aria-hidden="true"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </template>

    <template x-teleport="body">
        <div class="earnings-payouts-modal-overlay" x-cloak x-show="frequencyModalOpen"
            x-transition:enter="earnings-payouts-modal-overlay-enter"
            x-transition:enter-start="earnings-payouts-modal-overlay-enter-start"
            x-transition:enter-end="earnings-payouts-modal-overlay-enter-end"
            x-transition:leave="earnings-payouts-modal-overlay-leave"
            x-transition:leave-start="earnings-payouts-modal-overlay-leave-start"
            x-transition:leave-end="earnings-payouts-modal-overlay-leave-end"
            @keydown.escape.window="frequencyModalOpen = false" @click.self="frequencyModalOpen = false">
            <form class="earnings-payouts-modal-card earnings-payouts-freq-modal" x-show="frequencyModalOpen"
                x-transition:enter="earnings-payouts-modal-card-enter"
                x-transition:enter-start="earnings-payouts-modal-card-enter-start"
                x-transition:enter-end="earnings-payouts-modal-card-enter-end"
                x-transition:leave="earnings-payouts-modal-card-leave"
                x-transition:leave-start="earnings-payouts-modal-card-leave-start"
                x-transition:leave-end="earnings-payouts-modal-card-leave-end"
                wire:submit.prevent="updatePayoutFrequency" role="dialog" aria-modal="true"
                aria-labelledby="earnings-payouts-frequency-modal-title"
                x-data="{
                    selectedFrequency: @entangle('payoutFrequency'),
                    monthlyMode: @entangle('payoutMonthlyMode'),
                    monthlyDate: @entangle('payoutMonthlyDate'),
                    monthlyDay() {
                        const raw = String(this.monthlyDate || '');
                        if (!raw) return 18;
                        if (raw.includes('-')) {
                            const parts = raw.split('-');
                            return parseInt(parts[2], 10) || 18;
                        }
                        const parts = raw.split('/');
                        return parseInt(parts[0], 10) || 18;
                    }
                }">
                <div class="earnings-payouts-freq-modal__head">
                    <h3 class="earnings-payouts-freq-modal__title" id="earnings-payouts-frequency-modal-title">Pay out Frequency</h3>
                    <p class="earnings-payouts-freq-modal__copy">Choose how often your available balance is paid to your bank automatically.</p>
                    <button type="button" class="earnings-payouts-freq-modal__close" @click="frequencyModalOpen = false"
                        aria-label="Close payout frequency modal">
                        <img src="{{ asset('images/business-hub/icon-earnings-payout-freq-close.svg') }}" width="14.5" height="14.5" alt="">
                    </button>
                </div>

                <div class="earnings-payouts-freq-modal__body">
                    <button type="button" class="earnings-payouts-freq-option"
                        :class="{ 'is-selected': selectedFrequency === 'Daily' }"
                        @click="selectedFrequency = 'Daily'">
                        <span class="earnings-payouts-freq-option__row">
                            <span class="earnings-payouts-freq-radio" aria-hidden="true"></span>
                            <span class="earnings-payouts-freq-option__text">
                                <strong>Daily</strong>
                                <span>Paid every working day for the previous day's earnings.</span>
                            </span>
                        </span>
                    </button>

                    <button type="button" class="earnings-payouts-freq-option"
                        :class="{ 'is-selected': selectedFrequency === 'Weekly' }"
                        @click="selectedFrequency = 'Weekly'">
                        <span class="earnings-payouts-freq-option__row">
                            <span class="earnings-payouts-freq-radio" aria-hidden="true"></span>
                            <span class="earnings-payouts-freq-option__text">
                                <strong>Weekly</strong>
                                <span>Paid every Tuesday. Next payout {{ $payouts['next_weekly_day_month'] ?? ($payouts['next_payout_day_month'] ?? '') }}.</span>
                            </span>
                        </span>
                    </button>

                    <div class="earnings-payouts-freq-option is-monthly"
                        :class="{ 'is-selected': selectedFrequency === 'Monthly' }"
                        role="button" tabindex="0"
                        @click="selectedFrequency = 'Monthly'; if (!monthlyMode) monthlyMode = 'day_of_month'"
                        @keydown.enter.prevent="selectedFrequency = 'Monthly'; if (!monthlyMode) monthlyMode = 'day_of_month'">
                        <div class="earnings-payouts-freq-option__row">
                            <span class="earnings-payouts-freq-radio" aria-hidden="true"></span>
                            <span class="earnings-payouts-freq-option__text">
                                <strong>Monthly, on a day you choose</strong>
                                <span>One payout a month, on the day that suits you.</span>
                            </span>
                        </div>

                        <div class="earnings-payouts-freq-monthly" @click.stop>
                            <div>
                                <p class="earnings-payouts-freq-date-label">Select Date</p>
                                <div class="earnings-payouts-freq-date">
                                    <input type="date" x-model="monthlyDate"
                                        @focus="selectedFrequency = 'Monthly'; monthlyMode = 'day_of_month'"
                                        @change="selectedFrequency = 'Monthly'; monthlyMode = 'day_of_month'"
                                        aria-label="Select monthly payout date">
                                    <img src="{{ asset('images/business-hub/icon-earnings-payout-calendar.svg') }}" width="14" height="14" alt="">
                                </div>
                            </div>
                            <div class="earnings-payouts-freq-suboptions" role="radiogroup" aria-label="Monthly payout day">
                                <button type="button" class="earnings-payouts-freq-suboption"
                                    :class="{ 'is-selected': selectedFrequency === 'Monthly' && monthlyMode === 'day_of_month' }"
                                    role="radio"
                                    :aria-checked="(selectedFrequency === 'Monthly' && monthlyMode === 'day_of_month').toString()"
                                    @click="selectedFrequency = 'Monthly'; monthlyMode = 'day_of_month'">
                                    <span class="earnings-payouts-freq-radio" aria-hidden="true"></span>
                                    <span x-text="'On day ' + monthlyDay() + ' of every month'"></span>
                                </button>
                                <button type="button" class="earnings-payouts-freq-suboption"
                                    :class="{ 'is-selected': selectedFrequency === 'Monthly' && monthlyMode === 'nth_weekday' }"
                                    role="radio"
                                    :aria-checked="(selectedFrequency === 'Monthly' && monthlyMode === 'nth_weekday').toString()"
                                    @click="selectedFrequency = 'Monthly'; monthlyMode = 'nth_weekday'">
                                    <span class="earnings-payouts-freq-radio" aria-hidden="true"></span>
                                    <span>On the third Thursday</span>
                                </button>
                            </div>
                        </div>
                    </div>

                    @error('payoutFrequency')
                        <span class="earnings-payouts-modal-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="earnings-payouts-freq-modal__actions">
                    <button type="button" class="earnings-payouts-freq-modal__btn earnings-payouts-freq-modal__btn--cancel"
                        @click="frequencyModalOpen = false">Cancel</button>
                    <button type="submit" class="earnings-payouts-freq-modal__btn earnings-payouts-freq-modal__btn--save"
                        wire:loading.attr="disabled" wire:target="updatePayoutFrequency">
                        <span wire:loading.remove wire:target="updatePayoutFrequency">Save Frequency</span>
                        <span wire:loading wire:target="updatePayoutFrequency">
                            <span class="earnings-payouts-modal-spinner" aria-hidden="true"></span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    </template>

    <template x-teleport="body">
        <div class="earnings-payouts-modal-overlay" x-cloak x-show="futurePayoutsModalOpen"
            x-transition:enter="earnings-payouts-modal-overlay-enter"
            x-transition:enter-start="earnings-payouts-modal-overlay-enter-start"
            x-transition:enter-end="earnings-payouts-modal-overlay-enter-end"
            x-transition:leave="earnings-payouts-modal-overlay-leave"
            x-transition:leave-start="earnings-payouts-modal-overlay-leave-start"
            x-transition:leave-end="earnings-payouts-modal-overlay-leave-end"
            @keydown.escape.window="futurePayoutsModalOpen = false" @click.self="futurePayoutsModalOpen = false">
            <div class="earnings-payouts-modal-card earnings-payouts-modal-card--scrollable"
                x-show="futurePayoutsModalOpen" x-transition:enter="earnings-payouts-modal-card-enter"
                x-transition:enter-start="earnings-payouts-modal-card-enter-start"
                x-transition:enter-end="earnings-payouts-modal-card-enter-end"
                x-transition:leave="earnings-payouts-modal-card-leave"
                x-transition:leave-start="earnings-payouts-modal-card-leave-start"
                x-transition:leave-end="earnings-payouts-modal-card-leave-end" role="dialog" aria-modal="true"
                aria-labelledby="earnings-payouts-future-modal-title">
                <div class="earnings-payouts-modal-head">
                    <h3 class="earnings-payouts-modal-title" id="earnings-payouts-future-modal-title">Future Payouts
                    </h3>
                    <button type="button" class="earnings-payouts-modal-close" @click="futurePayoutsModalOpen = false"
                        aria-label="Close future payouts modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 36 36" fill="none">
                            <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                            <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731"
                                stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="earnings-payouts-modal-body earnings-payouts-modal-body--scrollable">
                    <div class="earnings-payouts-modal-field is-full">
                        <p class="earnings-payouts-future-total" style="margin-bottom: 0.75rem;">
                            Total scheduled: £{{ number_format((float) ($payouts['future_amount'] ?? 0), 2) }}
                        </p>
                        <div class="earnings-payouts-table-wrap" style="padding: 0; background: transparent;">
                            <table class="earnings-payouts-table" style="min-width: 100%;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Amount</th>
                                        <th>Estimated Arrival</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allFutureItems as $item)
                                        <tr>
                                            <td>{{ $item['date'] }}</td>
                                            <td>£{{ number_format((float) $item['amount'], 2) }}</td>
                                            <td>{{ $item['arrival_date'] }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="earnings-payouts-empty">No scheduled future
                                                payouts.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </template>
</div>
