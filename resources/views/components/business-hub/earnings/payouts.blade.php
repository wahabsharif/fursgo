@props(['payouts' => []])

@php
    $futureItems = $payouts['future_items'] ?? [];
    $allFutureItems = $payouts['future_items_all'] ?? $futureItems;
    $history = $payouts['history'] ?? [];
    $bank = $payouts['bank'] ?? [];
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
            color: #3B3731;
            font-family: Lato;
            margin-top: 3rem;
        }

        .earnings-payouts-grid {
            display: grid;
            grid-template-columns: minmax(0, 1fr) minmax(280px, 0.7fr);
            gap: 2rem;
            align-items: start;
        }

        .earnings-payouts-left,
        .earnings-payouts-right {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            min-width: 0;
        }

        .earnings-payouts-summary {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 1rem;
        }

        .earnings-payouts-card,
        .earnings-payouts-panel {
            border: 1px solid #E3E3E3;
            border-radius: 10px;
            background: #FFF;
        }

        .earnings-payouts-card {
            min-height: 100px;
            padding: 1.3rem 1.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .earnings-payouts-label {
            margin: 0 0 0.6rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-value {
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 800;
            line-height: normal;
        }

        .earnings-payouts-muted {
            color: #9D9B98;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-left: 1rem;
        }

        .earnings-payouts-help {
            margin-top: 1rem;
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
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin-top: 1.25rem;
            padding-bottom: 1.25rem;
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
            padding-top: 1.25rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-payouts-help-copy p {
            margin: 0 0 1rem;
        }

        .earnings-payouts-history-title {
            margin: 1.25rem 0 1rem;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 28px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            padding-bottom: 1.25rem;
            border-bottom: 1px solid #D4D4D4;
        }

        .earnings-payouts-table-wrap {
            overflow-x: auto;
            border-radius: 10px;
            background: rgba(59, 55, 49, 0.02);
            padding: 1.25rem;
        }

        .earnings-payouts-table {
            width: 100%;
            min-width: 620px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .earnings-payouts-table th,
        .earnings-payouts-table td {
            padding: 1.15rem 1rem;
            border-bottom: 1px solid #E8E4DE;
            text-align: left;
            font-size: 14px;
        }

        .earnings-payouts-table th {
            color: #000;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-table td {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-payouts-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 92px;
            padding: 0.4rem 0.75rem;
            border-radius: 100px;
            background: rgba(186, 207, 142, 0.10);
            color: #AFCD6F;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 500;
            line-height: normal;
        }

        .earnings-payouts-invoice-cell {
            border-left: 1px solid #E8E4DE;
            text-align: center !important;
        }

        .earnings-payouts-icon-btn {
            border: 0;
            background: transparent;
            padding: 0;
            cursor: pointer;
            color: #3B3731;
        }

        .earnings-payouts-empty {
            color: #9D9B98;
            text-align: center !important;
        }

        .earnings-payouts-panel {
            overflow: hidden;
        }

        .earnings-payouts-panel-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.2rem;
            border-bottom: 1px solid #E3E3E3;
            background: rgba(59, 55, 49, 0.02);
        }

        .earnings-payouts-panel-title {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-panel-body {
            padding: 1.1rem 1.2rem 1.25rem;
        }

        .earnings-payouts-future-total {
            margin: 0 0 1rem;
            color: #3B3731;
            font-family: Lato;
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
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-panel-link {
            display: inline-flex;
            color: #AFCD6F;
            font-family: Lato;
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
        }

        .earnings-payouts-frequency {
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-verified {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: #AFCD6F;
            font-size: 14px;
            font-weight: 700;
        }

        .earnings-payouts-bank-copy {
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-payouts-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 100100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.22);
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

        @media (max-width: 1100px) {
            .earnings-payouts-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 640px) {
            .earnings-payouts-summary {
                grid-template-columns: 1fr;
            }

            .earnings-payouts-modal-body {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="earnings-payouts-grid">
        <div class="earnings-payouts-left">
            <div class="earnings-payouts-summary">
                <div class="earnings-payouts-card">
                    <p class="earnings-payouts-label">Pending Payouts</p>
                    <div class="earnings-payouts-value">
                        £{{ number_format((float) ($payouts['pending_amount'] ?? 0), 2) }}
                    </div>
                </div>

                <div class="earnings-payouts-card">
                    <p class="earnings-payouts-label">Total Payouts</p>
                    <div>
                        <span
                            class="earnings-payouts-value">£{{ number_format((float) ($payouts['total_amount'] ?? 0), 2) }}</span>
                        <span class="earnings-payouts-muted"> / All time</span>
                    </div>
                </div>
            </div>

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

            <h3 class="earnings-payouts-history-title">Pay-out History</h3>

            <div class="earnings-payouts-table-wrap">
                <table class="earnings-payouts-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Reference</th>
                            <th class="earnings-payouts-invoice-cell">Invoice</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($history as $item)
                            <tr>
                                <td>{{ $item['date'] }}</td>
                                <td>£{{ number_format((float) $item['amount'], 2) }}</td>
                                <td><span class="earnings-payouts-status">{{ $item['status'] }}</span></td>
                                <td>#{{ $item['reference'] }}</td>
                                <td class="earnings-payouts-invoice-cell">
                                    @if (!empty($item['invoice_url']))
                                        <button type="button" class="earnings-payouts-icon-btn"
                                            data-invoice-url="{{ $item['invoice_url'] }}"
                                            onclick="window.downloadBookingInvoicePdf?.(this.dataset.invoiceUrl)"
                                            aria-label="Download invoice">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19"
                                                fill="none">
                                                <path
                                                    d="M0.5 15.5V17C0.5 17.3978 0.643668 17.7794 0.8994 18.0607C1.15513 18.342 1.50198 18.5 1.86364 18.5H14.1364C14.498 18.5 14.8449 18.342 15.1006 18.0607C15.3563 17.7794 15.5 17.3978 15.5 17V15.5"
                                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                                <path d="M7.99997 0.5V12.875M12.0909 8.75L7.99997 13.25L3.90906 8.75"
                                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
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
        </div>

        <div class="earnings-payouts-right">
            <div class="earnings-payouts-panel">
                <div class="earnings-payouts-panel-head">
                    <h3 class="earnings-payouts-panel-title">Future Payouts</h3>
                </div>
                <div class="earnings-payouts-panel-body">
                    <p class="earnings-payouts-future-total">
                        £{{ number_format((float) ($payouts['future_amount'] ?? 0), 2) }}</p>
                    <div class="earnings-payouts-future-list">
                        @forelse($futureItems as $item)
                            <span>
                                <span style="margin-right: 0.5rem;">{{ $item['date'] }}</span>
                                £{{ number_format((float) $item['amount'], 2) }}
                                (Est. arrival
                                date {{ $item['arrival_date'] }})
                            </span>
                        @empty
                            <span class="earnings-payouts-muted">No scheduled future payouts.</span>
                        @endforelse
                    </div>
                    <a href="#" class="earnings-payouts-panel-link" @click.prevent="futurePayoutsModalOpen = true">View
                        More</a>
                </div>
            </div>

            <div class="earnings-payouts-panel">
                <div class="earnings-payouts-panel-head">
                    <h3 class="earnings-payouts-panel-title">Payout Frequency</h3>
                </div>
                <div class="earnings-payouts-panel-body">
                    <div class="earnings-payouts-frequency">
                        <div>{{ $payouts['frequency'] ?? 'Weekly' }}</div>
                        <div>Next Payout: {{ $payouts['next_payout_date'] ?? now()->next('Tuesday')->format('d F Y') }}
                        </div>
                    </div>
                    <a href="#" class="earnings-payouts-panel-link" style="margin-top: 3rem;"
                        @click.prevent="$wire.refreshPayoutFrequency(); frequencyModalOpen = true">Update Payout
                        Frequency</a>
                </div>
            </div>

            <div class="earnings-payouts-panel">
                <div class="earnings-payouts-panel-head">
                    <h3 class="earnings-payouts-panel-title">Bank Account</h3>
                    <span class="earnings-payouts-verified">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none">
                            <circle cx="7" cy="7" r="6.5" fill="#AFCD6F" />
                            <path d="M4 7.15L6.1 9.2L10 4.8" stroke="white" stroke-width="1.4" stroke-linecap="round"
                                stroke-linejoin="round" />
                        </svg>
                        {{ !empty($bank['verified']) ? 'Verified' : 'Not verified' }}
                    </span>
                </div>
                <div class="earnings-payouts-panel-body">
                    <div class="earnings-payouts-bank-copy">
                        <div>Bank: <span style="font-weight: 400;">{{ $bank['name'] ?? 'Bank details' }}</span></div>
                        <div>Account ending: <span
                                style="font-weight: 400;">{{ $bank['account_number'] ?? 'Not added' }}</span></div>
                        <div>Account holder: <span
                                style="font-weight: 400;">{{ $bank['account_holder'] ?? 'Not added' }}</span></div>
                    </div>
                    <a href="#" class="earnings-payouts-panel-link" style="margin-top: 1.2rem;"
                        @click.prevent="$wire.refreshPayoutBankDetails(); bankModalOpen = true">Update Bank
                        Details</a>
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
            <form class="earnings-payouts-modal-card" x-show="bankModalOpen"
                x-transition:enter="earnings-payouts-modal-card-enter"
                x-transition:enter-start="earnings-payouts-modal-card-enter-start"
                x-transition:enter-end="earnings-payouts-modal-card-enter-end"
                x-transition:leave="earnings-payouts-modal-card-leave"
                x-transition:leave-start="earnings-payouts-modal-card-leave-start"
                x-transition:leave-end="earnings-payouts-modal-card-leave-end"
                wire:submit.prevent="updatePayoutBankDetails" role="dialog" aria-modal="true"
                aria-labelledby="earnings-payouts-bank-modal-title">
                <div class="earnings-payouts-modal-head">
                    <h3 class="earnings-payouts-modal-title" id="earnings-payouts-bank-modal-title">Update Bank
                        Details
                    </h3>
                    <button type="button" class="earnings-payouts-modal-close" @click="bankModalOpen = false"
                        aria-label="Close bank details modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 36 36" fill="none">
                            <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                            <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731"
                                stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="earnings-payouts-modal-body">
                    <div class="earnings-payouts-modal-field is-full">
                        <label for="payout-bank">Bank</label>
                        <input id="payout-bank" type="text" wire:model="payoutBank" placeholder="Barclays">
                        @error('payoutBank')
                            <span class="earnings-payouts-modal-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="earnings-payouts-modal-field is-full">
                        <label for="payout-account-holder">Account Holder Name</label>
                        <input id="payout-account-holder" type="text" wire:model="payoutAccountHolderName"
                            placeholder="Dev User">
                        @error('payoutAccountHolderName')
                            <span class="earnings-payouts-modal-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="earnings-payouts-modal-field is-full">
                        <label for="payout-account-number">Account Number</label>
                        <input id="payout-account-number" type="text" wire:model="payoutAccountNumber"
                            placeholder="12345678">
                        @error('payoutAccountNumber')
                            <span class="earnings-payouts-modal-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="earnings-payouts-modal-field is-full">
                        <label for="payout-sort-code">Sort Code</label>
                        <input id="payout-sort-code" type="text" wire:model="payoutSortCode" placeholder="12-34-56">
                        @error('payoutSortCode')
                            <span class="earnings-payouts-modal-error">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="earnings-payouts-modal-field is-full">
                        <label for="payout-iban">IBAN</label>
                        <input id="payout-iban" type="text" wire:model="payoutIban"
                            placeholder="GB82WEST12345698765432">
                        @error('payoutIban')
                            <span class="earnings-payouts-modal-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="earnings-payouts-modal-actions">
                    <button type="submit" class="earnings-payouts-modal-btn earnings-payouts-modal-btn--primary"
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
            <form class="earnings-payouts-modal-card" x-show="frequencyModalOpen"
                x-transition:enter="earnings-payouts-modal-card-enter"
                x-transition:enter-start="earnings-payouts-modal-card-enter-start"
                x-transition:enter-end="earnings-payouts-modal-card-enter-end"
                x-transition:leave="earnings-payouts-modal-card-leave"
                x-transition:leave-start="earnings-payouts-modal-card-leave-start"
                x-transition:leave-end="earnings-payouts-modal-card-leave-end"
                wire:submit.prevent="updatePayoutFrequency" role="dialog" aria-modal="true"
                aria-labelledby="earnings-payouts-frequency-modal-title">
                <div class="earnings-payouts-modal-head">
                    <h3 class="earnings-payouts-modal-title" id="earnings-payouts-frequency-modal-title">Update Payout
                        Frequency
                    </h3>
                    <button type="button" class="earnings-payouts-modal-close" @click="frequencyModalOpen = false"
                        aria-label="Close payout frequency modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 36 36" fill="none">
                            <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                            <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731"
                                stroke-width="1.5" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="earnings-payouts-modal-body" x-data="{
                    frequencyDropdownOpen: false,
                    selectedFrequency: @entangle('payoutFrequency'),
                    frequencyOptions: ['Weekly', 'Fortnightly', 'Monthly'],
                    selectFrequency(option) {
                        this.selectedFrequency = option;
                        this.frequencyDropdownOpen = false;
                    }
                }">
                    <div class="earnings-payouts-modal-field is-full">
                        <label for="payout-frequency">Payout Frequency</label>
                        <div class="furs-dd" :class="{ 'is-open': frequencyDropdownOpen }"
                            @click.outside="frequencyDropdownOpen = false">
                            <button type="button" class="furs-dd__trigger" id="payout-frequency" aria-haspopup="listbox"
                                :aria-expanded="frequencyDropdownOpen.toString()"
                                @click="frequencyDropdownOpen = !frequencyDropdownOpen"
                                @keydown.escape.prevent="frequencyDropdownOpen = false">
                                <span class="furs-dd__label" x-text="selectedFrequency"></span>
                                <svg class="furs-dd__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 8"
                                    fill="none">
                                    <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731"
                                        stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                            </button>
                            <div class="furs-dd__panel" role="listbox" aria-labelledby="payout-frequency">
                                <div class="furs-dd__list">
                                    <template x-for="option in frequencyOptions" :key="option">
                                        <div class="furs-dd__option" role="option"
                                            :class="{ 'is-selected': selectedFrequency === option }"
                                            :aria-selected="(selectedFrequency === option).toString()"
                                            @click="selectFrequency(option)">
                                            <span class="furs-dd__dot"></span>
                                            <span class="furs-dd__text" x-text="option"></span>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @error('payoutFrequency')
                            <span class="earnings-payouts-modal-error">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="earnings-payouts-modal-actions">
                    <button type="submit" class="earnings-payouts-modal-btn earnings-payouts-modal-btn--primary"
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