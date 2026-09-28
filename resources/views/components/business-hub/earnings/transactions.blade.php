@props(['transactions' => [], 'isSpaceUser' => false])

@php
    $rows = collect($transactions)
        ->values()
        ->map(function (array $row) use ($isSpaceUser) {
            $client = (string) ($row['client'] ?? '');
            $pet = (string) ($row['pet'] ?? '');
            $petType = (string) ($row['pet_type'] ?? '');
            $space = (string) ($row['space'] ?? '');
            $bookingRef = (string) ($row['booking_reference'] ?? '');
            $haystack = strtolower(trim(implode(' ', array_filter([$bookingRef, $client, $pet, $petType, $space]))));

            return array_merge($row, [
                'client' => $client !== '' ? $client : 'Unknown',
                'owner' => $client !== '' ? $client : 'Unknown',
                'pet' => $pet !== '' ? $pet : 'Unknown',
                'pet_type' => $petType !== '' ? $petType : 'Pet',
                'space' => $space !== '' ? $space : '—',
                'amount_label' => '£' . number_format((float) ($row['amount'] ?? 0), 2),
                'invoice_url' => $row['receipt']['invoice_url'] ?? null,
                'haystack' => $haystack,
                'sort_ts' => (int) ($row['sort_ts'] ?? 0),
            ]);
        })
        ->all();

    $searchPlaceholder = $isSpaceUser ? 'Search client, space or booking ID...' : 'Search client, pet or booking ID...';
@endphp

<div class="earnings-tx"
    x-data="{
        rows: @js($rows),
        isSpaceUser: @js((bool) $isSpaceUser),
        search: '',
        statusFilter: 'all',
        sort: 'newest',
        pageSize: 15,
        visibleCount: 15,
        sortOpen: false,
        menuLeft: 8,
        menuTop: 8,
        menuWidth: 220,
        get filteredRows() {
            const q = (this.search || '').trim().toLowerCase();
            let list = this.rows.filter((row) => {
                if (this.statusFilter !== 'all' && row.status_key !== this.statusFilter) {
                    return false;
                }
                if (!q) return true;
                return (row.haystack || '').includes(q);
            });
    
            list = [...list].sort((a, b) => {
                if (this.sort === 'oldest') return (a.sort_ts || 0) - (b.sort_ts || 0);
                if (this.sort === 'amount_high') return (b.amount || 0) - (a.amount || 0);
                if (this.sort === 'amount_low') return (a.amount || 0) - (b.amount || 0);
                return (b.sort_ts || 0) - (a.sort_ts || 0);
            });
    
            return list;
        },
        get visibleRows() {
            return this.filteredRows.slice(0, this.visibleCount);
        },
        get showLoadMore() {
            return this.filteredRows.length > this.visibleCount;
        },
        setStatus(status) {
            this.statusFilter = status;
            this.visibleCount = this.pageSize;
        },
        setSort(sort) {
            this.sort = sort;
            this.sortOpen = false;
            this.visibleCount = this.pageSize;
        },
        loadMore() {
            this.visibleCount += this.pageSize;
        },
        repositionMenu() {
            const btn = this.$refs.sortBtn;
            if (!btn) return;
            const rect = btn.getBoundingClientRect();
            this.menuTop = rect.bottom + 10;
            this.menuLeft = Math.max(8, Math.min(rect.right - this.menuWidth, window.innerWidth - this.menuWidth - 8));
        },
        toggleSort() {
            if (!this.sortOpen) this.repositionMenu();
            this.sortOpen = !this.sortOpen;
        },
        openReceipt(row) {
            if (!row?.receipt) return;
            try {
                window.openEarningsReceiptModal?.(btoa(JSON.stringify(row.receipt)));
            } catch (e) {
                console.warn('Could not open receipt.', e);
            }
        },
        downloadInvoice(row) {
            if (!row?.invoice_url) return;
            window.downloadBookingInvoicePdf?.(row.invoice_url);
        }
    }"
    @keydown.escape.window="sortOpen = false"
    @resize.window="if (sortOpen) repositionMenu()"
    @scroll.window="if (sortOpen) repositionMenu()"
    @click.window="if (sortOpen && (!$refs.sortBtn || !$refs.sortBtn.contains($event.target)) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) sortOpen = false">
    <style>
        .earnings-tx {
            display: flex;
            flex-direction: column;
            gap: 2.5rem;
            width: 100%;
            padding: 2px;
            box-sizing: border-box;
        }

        .earnings-tx-search {
            position: relative;
            display: flex;
            align-items: center;
            width: 400px;
            max-width: 100%;
            height: 42px;
            margin-top: 1rem;
        }

        .earnings-tx-search input {
            width: 100%;
            height: 42px;
            border-radius: 10px;
            border: 1px solid #FFC97A;
            background: #FFF;
            padding: 0 42px 0 10px;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            outline: none;
        }

        .earnings-tx-search input::placeholder {
            color: #D4D4D4;
        }

        .earnings-tx-search-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            display: inline-flex;
            width: 18px;
            height: 18px;
        }

        .earnings-tx-search-icon img {
            width: 18px;
            height: 18px;
            display: block;
        }

        .earnings-tx-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .earnings-tx-pills {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .earnings-tx-pill {
            height: 36px;
            padding: 0 1.15rem;
            border: 0;
            border-radius: 100px;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: normal;
            cursor: pointer;
            white-space: nowrap;
            background: #F6F5F5;
            color: #9D9B98;
            transition: background-color 0.15s ease, color 0.15s ease;
        }

        .earnings-tx-pill.is-all.is-active {
            background: #3B3731;
            color: #FFF;
        }

        .earnings-tx-pill.is-paid.is-active {
            background: #F9FBF4;
            color: #AFCD6F;
        }

        .earnings-tx-pill.is-failed.is-active {
            background: #FFF4F4;
            color: #FF6E6E;
        }

        .earnings-tx-pill.is-refunded.is-active {
            background: rgba(255, 201, 122, 0.1);
            color: #F9C45C;
        }

        .earnings-tx-sort {
            margin-left: auto;
            position: relative;
            z-index: 30;
        }

        .earnings-tx-sort-trigger {
            width: 59px;
            height: 32px;
            border-radius: 100px;
            border: none;
            background: #FFF;
            color: #A8A8A8;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.35rem;
            box-shadow: 0px 1px 6.7px 0px rgba(59, 55, 49, 0.12);
        }

        .earnings-tx-sort-menu {
            background: #F8F8F8;
            border: 2px solid #e6e6e5;
            border-radius: 10px 0 10px 10px;
            overflow: hidden;
        }

        .earnings-tx-sort-option {
            width: 100%;
            border: 0;
            border-bottom: 2px solid #e6e6e5;
            background: #FFF;
            padding: 1rem;
            text-align: left;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .earnings-tx-sort-option:last-child {
            border-bottom: none;
        }

        .earnings-tx-sort-option:hover {
            background: #F2F2F2;
        }

        .earnings-tx-sort-indicator {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            border: 2px solid #FFC97A;
            position: relative;
            flex-shrink: 0;
        }

        .earnings-tx-sort-option.is-active .earnings-tx-sort-indicator::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 999px;
            background: #FFC97A;
        }

        .earnings-tx-card {
            width: calc(100% - 4px);
            margin: 2px;
            overflow: visible;
            background: #FDFDFD;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            box-shadow: 0px 0px 15px 2px rgba(59, 55, 49, 0.1);
        }

        .earnings-tx-table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 10px;
        }

        .earnings-tx-table {
            width: 100%;
            min-width: 980px;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
        }

        .earnings-tx-table th,
        .earnings-tx-table td {
            border: 0;
            text-align: left;
            vertical-align: middle;
            background: transparent;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-tx-table th {
            height: 50px;
            padding: 0 8px;
            color: #948F88;
            font-weight: 600;
            background: #F6F5F5;
        }

        .earnings-tx-table th:first-child {
            border-top-left-radius: 10px;
            padding-left: 20px;
        }

        .earnings-tx-table th:last-child {
            border-top-right-radius: 10px;
            padding-right: 20px;
        }

        .earnings-tx-table td {
            height: 59px;
            padding: 8px;
        }

        .earnings-tx-table td:first-child {
            padding-left: 20px;
        }

        .earnings-tx-table td:last-child {
            padding-right: 20px;
        }

        .earnings-tx-table tbody tr {
            background-color: #FDFDFD;
        }

        .earnings-tx-table tbody tr:not(:last-child) {
            background-image: linear-gradient(#E2E2E2, #E2E2E2);
            background-repeat: no-repeat;
            background-size: calc(100% - 40px) 1px;
            background-position: center bottom;
        }

        .earnings-tx-table col.col-booking {
            width: 12.5%;
        }

        .earnings-tx-table col.col-date {
            width: 11.5%;
        }

        .earnings-tx-table col.col-owner,
        .earnings-tx-table col.col-client {
            width: 11.5%;
        }

        .earnings-tx-table col.col-pet,
        .earnings-tx-table col.col-space {
            width: 13%;
        }

        .earnings-tx-table col.col-method {
            width: 16.5%;
        }

        .earnings-tx-table col.col-status {
            width: 12%;
        }

        .earnings-tx-table col.col-amount {
            width: 10%;
        }

        .earnings-tx-table col.col-actions {
            width: 106px;
        }

        .earnings-tx-date {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            gap: 0;
            line-height: normal;
        }

        .earnings-tx-date span:first-child {
            color: #3B3731;
            font-size: 16px;
            font-weight: 400;
        }

        .earnings-tx-date span:last-child {
            color: #9D9B98;
            font-size: 14px;
            font-weight: 400;
        }

        .earnings-tx-pet {
            white-space: nowrap;
        }

        .earnings-tx-pet-name {
            color: #3B3731;
            font-weight: 600;
        }

        .earnings-tx-pet-type {
            color: #9D9B98;
            font-weight: 400;
        }

        .earnings-tx-pet-type::before {
            content: ' ';
        }

        .earnings-tx-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            height: 32px;
            padding: 0 10px;
            border-radius: 100px;
            box-sizing: border-box;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 500;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-tx-status.is-paid {
            background: #F9FBF4;
            color: #AFCD6F;
        }

        .earnings-tx-status.is-failed {
            background: #FFF4F4;
            color: #FF6E6E;
        }

        .earnings-tx-status.is-refunded {
            background: rgba(255, 201, 122, 0.1);
            color: #F9C45C;
        }

        .earnings-tx-actions-col {
            width: 106px;
            text-align: center !important;
        }

        .earnings-tx-actions {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            width: 100%;
            min-height: 36px;
        }

        .earnings-tx-action-btn {
            border: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            background: transparent;
            width: 36px;
            height: 36px;
            flex-shrink: 0;
        }

        .earnings-tx-action-btn:disabled {
            opacity: 0.45;
            cursor: not-allowed;
        }

        .earnings-tx-action-btn.is-view img {
            display: block;
            width: 36px;
            height: 36px;
        }

        .earnings-tx-download {
            position: relative;
            display: block;
            width: 36px;
            height: 36px;
        }

        .earnings-tx-download img:first-child {
            display: block;
            width: 36px;
            height: 36px;
        }

        .earnings-tx-download-glyph {
            position: absolute;
            top: 8.5px;
            left: 10px;
            width: 16px;
            height: 19px;
            display: block;
        }

        .earnings-tx-empty {
            text-align: center !important;
            color: #9D9B98 !important;
            font-size: 15px !important;
            padding: 2rem 1rem !important;
            white-space: normal !important;
            height: auto !important;
        }

        .earnings-tx-load-more-wrap {
            display: flex;
            justify-content: center;
        }

        .earnings-tx-load-more {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 133px;
            height: 48px;
            background: #FFF;
            border-radius: 75px;
            border: 1px solid #3B3731;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }

        .earnings-tx-load-more:hover {
            background: #FFC97A;
            color: #FFF;
            border-color: #FFC97A;
            transform: translateY(-1px);
        }

        @media (max-width: 900px) {
            .earnings-tx-search {
                width: 100%;
            }
        }
    </style>

    <label class="earnings-tx-search">
        <input type="search" x-model="search" @input="visibleCount = pageSize" placeholder="{{ $searchPlaceholder }}" />
        <span class="earnings-tx-search-icon" aria-hidden="true">
            <x-business-hub.common.icon name="search" />
        </span>
    </label>

    <div class="earnings-tx-toolbar">
        <div class="earnings-tx-pills" role="tablist" aria-label="Transaction status filters">
            <button type="button" class="earnings-tx-pill is-all" :class="{ 'is-active': statusFilter === 'all' }"
                @click="setStatus('all')">All</button>
            <button type="button" class="earnings-tx-pill is-paid" :class="{ 'is-active': statusFilter === 'paid' }"
                @click="setStatus('paid')">Paid</button>
            <button type="button" class="earnings-tx-pill is-failed" :class="{ 'is-active': statusFilter === 'failed' }"
                @click="setStatus('failed')">Failed</button>
            <button type="button" class="earnings-tx-pill is-refunded"
                :class="{ 'is-active': statusFilter === 'refunded' }" @click="setStatus('refunded')">Refunded</button>
        </div>

        <div class="earnings-tx-sort">
            <button type="button" class="earnings-tx-sort-trigger" x-ref="sortBtn" @click.stop="toggleSort()"
                aria-label="Sort transactions" :aria-expanded="sortOpen.toString()">
                <span>Sort</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7" fill="none"
                    aria-hidden="true">
                    <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <template x-teleport="body">
                <div class="earnings-tx-sort-menu" x-cloak x-show="sortOpen" x-ref="sortMenu"
                    x-transition.opacity.duration.100ms
                    :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; width: ${menuWidth}px; z-index: 99999;`">
                    <button type="button" class="earnings-tx-sort-option" :class="{ 'is-active': sort === 'newest' }"
                        @click="setSort('newest')">
                        <span>Newest first</span>
                        <span class="earnings-tx-sort-indicator"></span>
                    </button>
                    <button type="button" class="earnings-tx-sort-option" :class="{ 'is-active': sort === 'oldest' }"
                        @click="setSort('oldest')">
                        <span>Oldest first</span>
                        <span class="earnings-tx-sort-indicator"></span>
                    </button>
                    <button type="button" class="earnings-tx-sort-option"
                        :class="{ 'is-active': sort === 'amount_high' }" @click="setSort('amount_high')">
                        <span>Amount high to low</span>
                        <span class="earnings-tx-sort-indicator"></span>
                    </button>
                    <button type="button" class="earnings-tx-sort-option" :class="{ 'is-active': sort === 'amount_low' }"
                        @click="setSort('amount_low')">
                        <span>Amount low to high</span>
                        <span class="earnings-tx-sort-indicator"></span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <div class="earnings-tx-card">
        <div class="earnings-tx-table-wrap">
            <table class="earnings-tx-table">
                <colgroup>
                    <col class="col-booking">
                    <col class="col-date">
                    @if ($isSpaceUser)
                        <col class="col-client">
                        <col class="col-space">
                    @else
                        <col class="col-owner">
                        <col class="col-pet">
                    @endif
                    <col class="col-method">
                    <col class="col-status">
                    <col class="col-amount">
                    <col class="col-actions">
                </colgroup>
                <thead>
                    <tr>
                        <th>Booking ID</th>
                        <th>Date</th>
                        @if ($isSpaceUser)
                            <th>Client</th>
                            <th>Space</th>
                        @else
                            <th>Owner</th>
                            <th>Pet</th>
                        @endif
                        <th>Payment Method</th>
                        <th>Status</th>
                        <th>Amount</th>
                        <th class="earnings-tx-actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(row, index) in visibleRows" :key="`${row.booking_id}-${row.date}-${index}`">
                        <tr>
                            <td x-text="row.booking_reference"></td>
                            <td>
                                <div class="earnings-tx-date">
                                    <span x-text="row.date"></span>
                                    <span x-text="row.time"></span>
                                </div>
                            </td>
                            @if ($isSpaceUser)
                                <td x-text="row.client"></td>
                                <td x-text="row.space"></td>
                            @else
                                <td x-text="row.owner"></td>
                                <td class="earnings-tx-pet">
                                    <span class="earnings-tx-pet-name" x-text="row.pet"></span>
                                    <span class="earnings-tx-pet-type" x-text="row.pet_type"></span>
                                </td>
                            @endif
                            <td x-text="row.payment_method"></td>
                            <td>
                                <span class="earnings-tx-status" :class="'is-' + (row.status_key || 'paid')"
                                    x-text="row.status_label"></span>
                            </td>
                            <td x-text="row.amount_label"></td>
                            <td class="earnings-tx-actions-col">
                                <div class="earnings-tx-actions">
                                    <button type="button" class="earnings-tx-action-btn is-view"
                                        @click="openReceipt(row)" aria-label="View receipt">
                                        <img src="{{ asset('images/business-hub/icon-earnings-tx-view.svg') }}" alt=""
                                            width="36" height="36">
                                    </button>
                                    <button type="button" class="earnings-tx-action-btn is-download"
                                        :disabled="!row.invoice_url" @click="downloadInvoice(row)"
                                        aria-label="Download invoice">
                                        <span class="earnings-tx-download">
                                            <img src="{{ asset('images/business-hub/icon-earnings-tx-download-ring.svg') }}"
                                                alt="" width="36" height="36">
                                            <img class="earnings-tx-download-glyph"
                                                src="{{ asset('images/business-hub/icon-earnings-tx-download-arrow.svg') }}"
                                                alt="" width="16" height="19">
                                        </span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="visibleRows.length === 0" x-cloak>
                        <td colspan="8" class="earnings-tx-empty">No transactions available yet.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="earnings-tx-load-more-wrap" x-show="showLoadMore" x-cloak>
        <button type="button" class="earnings-tx-load-more" @click="loadMore">Load More</button>
    </div>
</div>

@once
    @push('script')
        <script>
            window.openEarningsReceiptModal = function(encodedReceipt) {
                if (!encodedReceipt) {
                    return;
                }

                let receipt = null;

                try {
                    receipt = JSON.parse(atob(encodedReceipt));
                } catch (error) {
                    console.warn('Could not read receipt data.', error);
                    return;
                }

                if (!receipt || !receipt.booking_id) {
                    return;
                }

                const assets = {
                    close: @json(asset('images/business-hub/icon-earnings-receipt-close.svg')),
                    downloadRing: @json(asset('images/business-hub/icon-earnings-receipt-download-ring.svg')),
                    downloadArrow: @json(asset('images/business-hub/icon-earnings-receipt-download-arrow.svg')),
                };

                let modal = document.querySelector('[data-earnings-receipt-modal]');

                if (!modal) {
                    modal = document.createElement('div');
                    modal.dataset.earningsReceiptModal = 'true';
                    modal.className = 'earnings-receipt-modal-overlay';
                    modal.innerHTML = `
                        <div class="earnings-receipt-modal" data-receipt-card role="dialog" aria-modal="true" aria-labelledby="earnings-receipt-title">
                            <div class="earnings-receipt-modal__head" data-receipt-head>
                                <h2 class="earnings-receipt-modal__title" id="earnings-receipt-title">Booking <span>Receipt</span></h2>
                                <button type="button" class="earnings-receipt-modal__close" data-receipt-close aria-label="Close modal">
                                    <img src="${assets.close}" width="14.5" height="14.5" alt="">
                                </button>
                            </div>
                            <div class="earnings-receipt-modal__body">
                                <div class="earnings-receipt-modal__id">
                                    <p data-field="booking_id_label"></p>
                                    <div class="earnings-receipt-modal__id-end">
                                        <span data-field="date_label"></span>
                                        <button type="button" class="earnings-receipt-modal__download" data-receipt-download aria-label="Download invoice">
                                            <img src="${assets.downloadRing}" width="36" height="36" alt="">
                                            <img class="earnings-receipt-modal__download-glyph" src="${assets.downloadArrow}" width="16" height="19" alt="">
                                        </button>
                                    </div>
                                </div>
                                <div class="earnings-receipt-modal__person">
                                    <span class="earnings-receipt-modal__avatar" data-receipt-avatar></span>
                                    <span>
                                        <span class="earnings-receipt-modal__name" data-field="owner_name"></span>
                                        <span class="earnings-receipt-modal__pet" data-pet-line>
                                            <span data-field="pet_name"></span> <span data-field="pet_type"></span>
                                        </span>
                                    </span>
                                </div>
                                <div class="earnings-receipt-modal__section">
                                    <p class="earnings-receipt-modal__section-title" data-field="section_label"></p>
                                    <div class="earnings-receipt-modal__line">
                                        <span data-field="service_line"></span>
                                        <span data-field="service_amount"></span>
                                    </div>
                                </div>
                                <div class="earnings-receipt-modal__section">
                                    <p class="earnings-receipt-modal__section-title">Extras &amp; Add-ons</p>
                                    <div data-addons></div>
                                </div>
                                <div class="earnings-receipt-modal__summary">
                                    <div class="earnings-receipt-modal__line">
                                        <span>Service:</span>
                                        <span data-field="service_total"></span>
                                    </div>
                                    <div class="earnings-receipt-modal__line">
                                        <span>Extras &amp; Add-ons:</span>
                                        <span data-field="extras_total"></span>
                                    </div>
                                    <div class="earnings-receipt-modal__line">
                                        <span>Promo discount:</span>
                                        <span data-field="promo_discount"></span>
                                    </div>
                                </div>
                                <div class="earnings-receipt-modal__total">
                                    <span>Total</span>
                                    <span data-field="grand_total"></span>
                                </div>
                            </div>
                        </div>
                    `;
                    document.body.appendChild(modal);

                    modal.addEventListener('click', function(event) {
                        if (event.target === modal || event.target.closest('[data-receipt-close]')) {
                            closeEarningsReceiptModal();
                        }
                    });
                    document.addEventListener('keydown', function(event) {
                        if (event.key === 'Escape') {
                            closeEarningsReceiptModal();
                        }
                    });
                }

                const setText = (name, value) => {
                    modal.querySelectorAll(`[data-field="${name}"]`).forEach((node) => {
                        node.textContent = value || '';
                    });
                };

                const head = modal.querySelector('[data-receipt-head]');
                if (head) {
                    head.classList.toggle('is-space', Boolean(receipt.is_space_user));
                }

                setText('booking_id_label', `Booking ID: ${receipt.booking_id_label}`);
                setText('date_label', receipt.date_label);
                setText('owner_name', receipt.owner_name);
                setText('pet_name', receipt.pet_name);
                setText('pet_type', receipt.pet_type);
                setText('section_label', receipt.is_space_user ? 'Space' : 'Service');
                setText('service_line', receipt.service_line_label || (receipt.is_space_user ? receipt.space_label : receipt.service));
                setText('service_amount', `£${receipt.service_amount_formatted}`);
                setText('service_total', `£${receipt.service_amount_formatted}`);
                setText('extras_total', `£${receipt.extras_amount_formatted}`);
                setText('promo_discount', receipt.promo_discount_label || `- £${receipt.promo_discount_formatted}`);
                setText('grand_total', `£${receipt.total_amount_formatted}`);

                const petLine = modal.querySelector('[data-pet-line]');
                if (petLine) {
                    petLine.style.display = receipt.is_space_user ? 'none' : '';
                }

                const avatar = modal.querySelector('[data-receipt-avatar]');
                if (avatar) {
                    avatar.textContent = '';
                    if (receipt.owner_photo_url) {
                        const img = document.createElement('img');
                        img.src = receipt.owner_photo_url;
                        img.alt = '';
                        img.width = 44;
                        img.height = 44;
                        avatar.appendChild(img);
                    } else {
                        const initial = document.createElement('span');
                        initial.textContent = receipt.owner_initial || '?';
                        avatar.appendChild(initial);
                    }
                }

                const downloadButton = modal.querySelector('[data-receipt-download]');
                if (downloadButton) {
                    downloadButton.onclick = () => window.downloadBookingInvoicePdf?.(receipt.invoice_url);
                }

                const addons = modal.querySelector('[data-addons]');
                if (addons) {
                    addons.textContent = '';
                    const items = Array.isArray(receipt.addons) && receipt.addons.length ?
                        receipt.addons : [{
                            label: 'No add-ons',
                            amount_formatted: '0.00'
                        }];

                    items.forEach((addon) => {
                        const row = document.createElement('div');
                        row.className = 'earnings-receipt-modal__line';
                        row.innerHTML = '<span></span><span></span>';
                        row.children[0].textContent = addon.label;
                        row.children[1].textContent = `£${addon.amount_formatted}`;
                        addons.appendChild(row);
                    });
                }

                if (modal.__closeTimer) {
                    clearTimeout(modal.__closeTimer);
                    modal.__closeTimer = null;
                }

                modal.style.display = 'flex';
                requestAnimationFrame(() => {
                    modal.classList.add('is-open');
                });
                document.body.style.overflow = 'hidden';
                document.documentElement.style.overflow = 'hidden';
            };

            window.closeEarningsReceiptModal = function() {
                const modal = document.querySelector('[data-earnings-receipt-modal]');

                if (!modal) {
                    return;
                }

                modal.classList.remove('is-open');
                modal.__closeTimer = setTimeout(() => {
                    modal.style.display = 'none';
                    modal.__closeTimer = null;
                }, 180);
                document.body.style.overflow = '';
                document.documentElement.style.overflow = '';
            };

            if (!window.downloadBookingInvoicePdf) {
                window.downloadBookingInvoicePdf = async function(invoiceUrl) {
                    if (!invoiceUrl) return;
                    window.open(invoiceUrl, '_blank', 'noopener');
                };
            }
        </script>
    @endpush
@endonce
