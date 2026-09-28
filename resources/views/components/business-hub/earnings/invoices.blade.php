@props(['invoices' => []])

@php
    $rows = collect($invoices)
        ->values()
        ->map(function (array $row) {
            $haystack = strtolower(trim(implode(' ', array_filter([(string) ($row['invoice_no'] ?? ''), (string) ($row['booking_reference'] ?? ''), (string) ($row['client'] ?? ''), (string) ($row['reference'] ?? '')]))));

            return array_merge($row, [
                'haystack' => $haystack,
                'sort_ts' => (int) ($row['sort_ts'] ?? 0),
                'time' => (string) ($row['time'] ?? ''),
            ]);
        })
        ->all();
@endphp

<div class="earnings-invoices"
    x-data="{
        rows: @js($rows),
        search: '',
        period: 'last-3-months',
        sort: 'newest',
        sortOpen: false,
        menuLeft: 8,
        menuTop: 8,
        menuWidth: 220,
        pageSize: 15,
        visibleCount: 15,
        dateFrom: '',
        dateTo: '',
        get filteredRows() {
            const q = (this.search || '').trim().toLowerCase();
            let list = this.rows.filter((row) => {
                if (!this.matchesPeriod(row)) return false;
                if (!this.matchesDateRange(row)) return false;
                if (!q) return true;
                return (row.haystack || '').includes(q);
            });
    
            list = [...list].sort((a, b) => {
                if (this.sort === 'oldest') return (a.sort_ts || 0) - (b.sort_ts || 0);
                if (this.sort === 'amount_high') return (b.total || 0) - (a.total || 0);
                if (this.sort === 'amount_low') return (a.total || 0) - (b.total || 0);
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
        get summary() {
            const now = new Date();
            const startOfMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            const startOfNext = new Date(now.getFullYear(), now.getMonth() + 1, 1);
            let thisMonthCount = 0;
            let totalInvoiced = 0;
            let paid = 0;
            let outstanding = 0;
    
            this.rows.forEach((row) => {
                const total = Number(row.total || 0);
                totalInvoiced += total;
                if (row.status_key === 'paid') paid += total;
                if (row.status_key === 'pending') outstanding += total;
                if (row.date_iso) {
                    const d = new Date(row.date_iso + 'T00:00:00');
                    if (d >= startOfMonth && d < startOfNext) thisMonthCount += 1;
                }
            });
    
            return {
                thisMonthCount,
                totalInvoiced,
                paid,
                outstanding,
            };
        },
        matchesPeriod(row) {
            if (!row.date_iso) return false;
            const date = new Date(row.date_iso + 'T00:00:00');
            const now = new Date();
            const startOfThisMonth = new Date(now.getFullYear(), now.getMonth(), 1);
            const startOfLastMonth = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const startOfNextMonth = new Date(now.getFullYear(), now.getMonth() + 1, 1);
            const startOfThreeMonths = new Date(now.getFullYear(), now.getMonth() - 2, 1);
    
            if (this.period === 'last-month') {
                return date >= startOfLastMonth && date < startOfThisMonth;
            }
            if (this.period === 'last-3-months') {
                return date >= startOfThreeMonths && date < startOfNextMonth;
            }
            return date >= startOfThisMonth && date < startOfNextMonth;
        },
        matchesDateRange(row) {
            if (!this.dateFrom && !this.dateTo) return true;
            if (!row.date_iso) return false;
            const start = this.dateFrom || this.dateTo;
            const end = this.dateTo || this.dateFrom;
            const rangeStart = start <= end ? start : end;
            const rangeEnd = start <= end ? end : start;
            return row.date_iso >= rangeStart && row.date_iso <= rangeEnd;
        },
        setPeriod(period) {
            this.period = period;
            this.visibleCount = this.pageSize;
        },
        setSort(sort) {
            this.sort = sort;
            this.sortOpen = false;
            this.visibleCount = this.pageSize;
        },
        clearDates() {
            this.dateFrom = '';
            this.dateTo = '';
            this.visibleCount = this.pageSize;
        },
        loadMore() {
            this.visibleCount += this.pageSize;
        },
        formatMoney(value, decimals = 2) {
            return '£' + Number(value || 0).toFixed(decimals);
        },
        formatMoneyShort(value) {
            const n = Number(value || 0);
            return Number.isInteger(n) ? ('£' + n) : ('£' + n.toFixed(2));
        },
        displayDate(iso) {
            if (!iso) return 'Select date';
            const parts = String(iso).split('-');
            if (parts.length !== 3) return iso;
            return parts[2] + '/' + parts[1] + '/' + parts[0];
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
        download(invoiceUrl) {
            window.downloadBookingInvoicePdf?.(invoiceUrl);
        },
        exportAll() {
            this.filteredRows
                .filter((invoice) => invoice.invoice_url)
                .forEach((invoice, index) => {
                    window.setTimeout(() => this.download(invoice.invoice_url), index * 250);
                });
        }
    }"
    @keydown.escape.window="sortOpen = false"
    @resize.window="if (sortOpen) repositionMenu()"
    @scroll.window="if (sortOpen) repositionMenu()"
    @click.window="if (sortOpen && (!$refs.sortBtn || !$refs.sortBtn.contains($event.target)) && (!$refs.sortMenu || !$refs.sortMenu.contains($event.target))) sortOpen = false">
    <style>
        .earnings-invoices {
            display: flex;
            flex-direction: column;
            gap: 40px;
            width: 100%;
            color: #3B3731;
            font-family: Lato, sans-serif;
            box-sizing: border-box;
        }

        .earnings-invoices-summary {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 21px;
        }

        .earnings-invoices-stat {
            min-height: 107px;
            padding: 20px;
            border: 1px solid #E2E2E2;
            border-radius: 10px;
            background: #FFF;
            box-shadow: 0 4px 15px 5px rgba(0, 0, 0, 0.02);
            box-sizing: border-box;
        }

        .earnings-invoices-stat__label {
            margin: 0 0 10px;
            color: #565149;
            font-size: 14px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-invoices-stat__value {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 30px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-invoices-stat__value.is-paid {
            color: #A1BF63;
        }

        .earnings-invoices-stat__value.is-outstanding {
            color: #9D9B98;
        }

        .earnings-invoices-toolbar {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .earnings-invoices-search {
            position: relative;
            display: flex;
            align-items: center;
            width: 400px;
            max-width: 100%;
            height: 42px;
        }

        .earnings-invoices-search input {
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
            box-sizing: border-box;
        }

        .earnings-invoices-search input::placeholder {
            color: #D4D4D4;
        }

        .earnings-invoices-search-icon {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
            display: inline-flex;
            width: 15px;
            height: 15px;
        }

        .earnings-invoices-search-icon img {
            width: 15px;
            height: 15px;
            display: block;
        }

        .earnings-invoices-periods {
            display: inline-flex;
            align-items: center;
            height: 42px;
            padding: 3px;
            border-radius: 100px;
            background: #F9FAFC;
            box-sizing: border-box;
        }

        .earnings-invoices-period {
            height: 36px;
            padding: 0 16px;
            border: 0;
            border-radius: 100px;
            background: transparent;
            color: #888;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
            white-space: nowrap;
        }

        .earnings-invoices-period.is-active {
            background: #FFF;
            color: #3B3731;
            box-shadow: 0 2px 4px 0 rgba(59, 55, 49, 0.10);
        }

        .earnings-invoices-export {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-width: 107px;
            height: 42px;
            padding: 0 20px;
            border: 0;
            border-radius: 100px;
            background: #3B3731;
            color: #FFF;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
        }

        .earnings-invoices-export img {
            width: 9.17px;
            height: 11px;
            display: block;
        }

        .earnings-invoices-sort {
            margin-left: auto;
            position: relative;
            z-index: 30;
        }

        .earnings-invoices-sort-trigger {
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

        .earnings-invoices-sort-menu {
            background: #F8F8F8;
            border: 2px solid #e6e6e5;
            border-radius: 10px 0 10px 10px;
            overflow: hidden;
        }

        .earnings-invoices-sort-option {
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

        .earnings-invoices-sort-option:last-child {
            border-bottom: none;
        }

        .earnings-invoices-sort-option:hover {
            background: #F2F2F2;
        }

        .earnings-invoices-sort-indicator {
            width: 26px;
            height: 26px;
            border-radius: 999px;
            border: 2px solid #FFC97A;
            position: relative;
            flex-shrink: 0;
        }

        .earnings-invoices-sort-option.is-active .earnings-invoices-sort-indicator::after {
            content: '';
            position: absolute;
            inset: 2px;
            border-radius: 999px;
            background: #FFC97A;
        }

        .earnings-invoices-range {
            display: flex;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
            min-height: 71px;
            padding: 20px;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            background: #FDFDFD;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.10);
            box-sizing: border-box;
        }

        .earnings-invoices-range__label {
            margin: 0;
            color: #3B3731;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-invoices-range__field {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .earnings-invoices-range__field span {
            color: #9D9B98;
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-invoices-range__input {
            position: relative;
            width: 131px;
        }

        .earnings-invoices-range__input input {
            width: 100%;
            height: 31px;
            padding: 0 28px 0 10px;
            border: 1px solid #DDD;
            border-radius: 5px;
            background: #FFF;
            color: #3B3731;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            line-height: 25px;
            box-sizing: border-box;
            -webkit-appearance: none;
            appearance: none;
        }

        .earnings-invoices-range__input input::-webkit-calendar-picker-indicator {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .earnings-invoices-range__input input::-webkit-inner-spin-button,
        .earnings-invoices-range__input input::-webkit-clear-button {
            display: none;
            -webkit-appearance: none;
        }

        .earnings-invoices-range__input img {
            position: absolute;
            top: 50%;
            right: 10px;
            width: 12px;
            height: 11px;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .earnings-invoices-range__count {
            margin: 0;
            color: #9D9B98;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
            white-space: nowrap;
        }

        .earnings-invoices-range__clear {
            margin-left: auto;
            border: 0;
            background: transparent;
            color: #FFC97A;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 700;
            line-height: normal;
            cursor: pointer;
            padding: 0;
            white-space: nowrap;
        }

        .earnings-invoices-card {
            width: 100%;
            overflow: visible;
            background: #FDFDFD;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.10);
        }

        .earnings-invoices-table-wrap {
            width: 100%;
            overflow-x: auto;
            overflow-y: hidden;
            border-radius: 10px;
        }

        .earnings-invoices-table {
            width: 100%;
            min-width: 1030px;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
        }

        .earnings-invoices-table th,
        .earnings-invoices-table td {
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

        .earnings-invoices-table th {
            height: 50px;
            padding: 0 8px;
            color: #948F88;
            font-weight: 600;
            background: #F6F5F5;
        }

        .earnings-invoices-table th:first-child {
            border-top-left-radius: 10px;
            padding-left: 20px;
        }

        .earnings-invoices-table th:last-child {
            border-top-right-radius: 10px;
            padding-right: 20px;
        }

        .earnings-invoices-table td {
            height: 59px;
            padding: 8px;
        }

        .earnings-invoices-table td:first-child {
            padding-left: 20px;
        }

        .earnings-invoices-table td:last-child {
            padding-right: 20px;
        }

        .earnings-invoices-table tbody tr {
            background-color: #FDFDFD;
        }

        .earnings-invoices-table tbody tr:not(:last-child) {
            background-image: linear-gradient(#E2E2E2, #E2E2E2);
            background-repeat: no-repeat;
            background-size: calc(100% - 40px) 1px;
            background-position: center bottom;
        }

        .earnings-invoices-table col.col-date {
            width: 11%;
        }

        .earnings-invoices-table col.col-invoice {
            width: 14%;
        }

        .earnings-invoices-table col.col-booking {
            width: 12%;
        }

        .earnings-invoices-table col.col-client {
            width: 12%;
        }

        .earnings-invoices-table col.col-gross,
        .earnings-invoices-table col.col-tax,
        .earnings-invoices-table col.col-total {
            width: 9%;
        }

        .earnings-invoices-table col.col-status {
            width: 12%;
        }

        .earnings-invoices-table col.col-actions {
            width: 10%;
        }

        .earnings-invoices-date {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            gap: 0;
            line-height: normal;
        }

        .earnings-invoices-date span:first-child {
            color: #3B3731;
            font-size: 16px;
            font-weight: 400;
        }

        .earnings-invoices-date span:last-child {
            color: #9D9B98;
            font-size: 14px;
            font-weight: 400;
        }

        .earnings-invoices-status {
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

        .earnings-invoices-status.is-paid {
            background: #F9FBF4;
            color: #AFCD6F;
        }

        .earnings-invoices-status.is-pending {
            background: #F5F8FA;
            color: #7AB7E3;
        }

        .earnings-invoices-status.is-refunded {
            background: rgba(255, 201, 122, 0.1);
            color: #F9C45C;
        }

        .earnings-invoices-status.is-failed {
            background: #FFF4F4;
            color: #FF6E6E;
        }

        .earnings-invoices-actions-col {
            text-align: center !important;
        }

        .earnings-invoices-download {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .earnings-invoices-download:disabled {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .earnings-invoices-download img:first-child {
            display: block;
            width: 36px;
            height: 36px;
        }

        .earnings-invoices-download-glyph {
            position: absolute;
            top: 8.5px;
            left: 10px;
            width: 16px;
            height: 19px;
        }

        .earnings-invoices-empty {
            color: #9D9B98 !important;
            text-align: center !important;
            padding: 2rem !important;
        }

        .earnings-invoices-load {
            display: flex;
            justify-content: center;
            margin-top: -8px;
        }

        .earnings-invoices-load button {
            min-width: 133px;
            height: 48px;
            border: 1px solid #3B3731;
            border-radius: 999px;
            background: #FFF;
            color: #3B3731;
            cursor: pointer;
            font-family: Lato, sans-serif;
            font-size: 16px;
            font-weight: 400;
            padding: 0 1.4rem;
        }

        @media (max-width: 1100px) {
            .earnings-invoices-summary {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 720px) {
            .earnings-invoices-summary {
                grid-template-columns: 1fr;
            }

            .earnings-invoices-toolbar {
                align-items: stretch;
            }

            .earnings-invoices-search,
            .earnings-invoices-periods,
            .earnings-invoices-export {
                width: 100%;
            }

            .earnings-invoices-sort {
                margin-left: 0;
            }

            .earnings-invoices-range__clear {
                margin-left: 0;
            }
        }
    </style>

    <section class="earnings-invoices-summary" aria-label="Invoice summary">
        <article class="earnings-invoices-stat">
            <p class="earnings-invoices-stat__label">Invoices this month</p>
            <p class="earnings-invoices-stat__value" x-text="summary.thisMonthCount"></p>
        </article>
        <article class="earnings-invoices-stat">
            <p class="earnings-invoices-stat__label">Total invoiced</p>
            <p class="earnings-invoices-stat__value" x-text="formatMoneyShort(summary.totalInvoiced)"></p>
        </article>
        <article class="earnings-invoices-stat">
            <p class="earnings-invoices-stat__label">Paid</p>
            <p class="earnings-invoices-stat__value is-paid" x-text="formatMoneyShort(summary.paid)"></p>
        </article>
        <article class="earnings-invoices-stat">
            <p class="earnings-invoices-stat__label">Outstanding</p>
            <p class="earnings-invoices-stat__value is-outstanding" x-text="formatMoneyShort(summary.outstanding)"></p>
        </article>
    </section>

    <div class="earnings-invoices-toolbar">
        <div class="earnings-invoices-search">
            <input type="search" x-model.debounce.150ms="search" placeholder="Search by invoice, booking or client..."
                aria-label="Search invoices">
            <span class="earnings-invoices-search-icon" aria-hidden="true">
                <img src="{{ asset('images/business-hub/icon-earnings-inv-search.svg') }}" width="15" height="15" alt="">
            </span>
        </div>

        <div class="earnings-invoices-periods" role="group" aria-label="Invoice period">
            <button type="button" class="earnings-invoices-period"
                :class="{ 'is-active': period === 'month' }" @click="setPeriod('month')">This month</button>
            <button type="button" class="earnings-invoices-period"
                :class="{ 'is-active': period === 'last-month' }" @click="setPeriod('last-month')">Last month</button>
            <button type="button" class="earnings-invoices-period"
                :class="{ 'is-active': period === 'last-3-months' }" @click="setPeriod('last-3-months')">Last 3 months</button>
        </div>

        <button type="button" class="earnings-invoices-export" @click="exportAll()" aria-label="Export invoices">
            <img src="{{ asset('images/business-hub/icon-earnings-inv-export.svg') }}" width="9.17" height="11" alt="">
            Export
        </button>

        <div class="earnings-invoices-sort">
            <button type="button" class="earnings-invoices-sort-trigger" x-ref="sortBtn"
                @click="toggleSort()" :aria-expanded="sortOpen.toString()" aria-haspopup="listbox">
                Sort
                <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 9 9" fill="none" aria-hidden="true">
                    <path d="M1 3.25L4.5 6.75L8 3.25" stroke="#A8A8A8" stroke-width="1.2" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <template x-teleport="body">
                <div class="earnings-invoices-sort-menu" x-cloak x-show="sortOpen" x-ref="sortMenu" role="listbox"
                    :style="`position: fixed; top: ${menuTop}px; left: ${menuLeft}px; width: ${menuWidth}px; z-index: 100200;`">
                    <button type="button" class="earnings-invoices-sort-option"
                        :class="{ 'is-active': sort === 'newest' }" @click="setSort('newest')">
                        <span>Newest first</span>
                        <span class="earnings-invoices-sort-indicator" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="earnings-invoices-sort-option"
                        :class="{ 'is-active': sort === 'oldest' }" @click="setSort('oldest')">
                        <span>Oldest first</span>
                        <span class="earnings-invoices-sort-indicator" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="earnings-invoices-sort-option"
                        :class="{ 'is-active': sort === 'amount_high' }" @click="setSort('amount_high')">
                        <span>Highest total</span>
                        <span class="earnings-invoices-sort-indicator" aria-hidden="true"></span>
                    </button>
                    <button type="button" class="earnings-invoices-sort-option"
                        :class="{ 'is-active': sort === 'amount_low' }" @click="setSort('amount_low')">
                        <span>Lowest total</span>
                        <span class="earnings-invoices-sort-indicator" aria-hidden="true"></span>
                    </button>
                </div>
            </template>
        </div>
    </div>

    <section class="earnings-invoices-range" aria-label="Invoice date range">
        <p class="earnings-invoices-range__label">Date range</p>
        <label class="earnings-invoices-range__field">
            <span>From</span>
            <span class="earnings-invoices-range__input">
                <input type="date" x-model="dateFrom" @change="visibleCount = pageSize" aria-label="From date">
                <img src="{{ asset('images/business-hub/icon-earnings-inv-calendar.svg') }}" width="12" height="11" alt="">
            </span>
        </label>
        <label class="earnings-invoices-range__field">
            <span>To</span>
            <span class="earnings-invoices-range__input">
                <input type="date" x-model="dateTo" @change="visibleCount = pageSize" aria-label="To date">
                <img src="{{ asset('images/business-hub/icon-earnings-inv-calendar.svg') }}" width="12" height="11" alt="">
            </span>
        </label>
        <p class="earnings-invoices-range__count"
            x-text="filteredRows.length + (filteredRows.length === 1 ? ' invoice' : ' invoices')"></p>
        <button type="button" class="earnings-invoices-range__clear" @click="clearDates()">Clear dates</button>
    </section>

    <div class="earnings-invoices-card">
        <div class="earnings-invoices-table-wrap">
            <table class="earnings-invoices-table">
                <colgroup>
                    <col class="col-date">
                    <col class="col-invoice">
                    <col class="col-booking">
                    <col class="col-client">
                    <col class="col-gross">
                    <col class="col-tax">
                    <col class="col-total">
                    <col class="col-status">
                    <col class="col-actions">
                </colgroup>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Invoice No.</th>
                        <th>Booking ID</th>
                        <th>Client</th>
                        <th>Gross</th>
                        <th>Tax</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th class="earnings-invoices-actions-col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="invoice in visibleRows" :key="invoice.invoice_no + '-' + invoice.booking_reference + '-' + invoice.sort_ts">
                        <tr>
                            <td>
                                <div class="earnings-invoices-date">
                                    <span x-text="invoice.date"></span>
                                    <span x-text="invoice.time || '—'"></span>
                                </div>
                            </td>
                            <td x-text="invoice.invoice_no"></td>
                            <td x-text="invoice.booking_reference"></td>
                            <td x-text="invoice.client"></td>
                            <td x-text="formatMoney(invoice.gross)"></td>
                            <td x-text="formatMoney(invoice.tax)"></td>
                            <td x-text="formatMoney(invoice.total)"></td>
                            <td>
                                <span class="earnings-invoices-status" :class="'is-' + invoice.status_key"
                                    x-text="invoice.status_label"></span>
                            </td>
                            <td class="earnings-invoices-actions-col">
                                <button type="button" class="earnings-invoices-download"
                                    :disabled="!invoice.invoice_url"
                                    @click="download(invoice.invoice_url)"
                                    aria-label="Download invoice">
                                    <img src="{{ asset('images/business-hub/icon-earnings-inv-download-ring.svg') }}"
                                        width="36" height="36" alt="">
                                    <img class="earnings-invoices-download-glyph"
                                        src="{{ asset('images/business-hub/icon-earnings-inv-download-arrow.svg') }}"
                                        width="16" height="19" alt="">
                                </button>
                            </td>
                        </tr>
                    </template>
                    <tr x-show="filteredRows.length === 0">
                        <td colspan="9" class="earnings-invoices-empty">No invoices found.</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="earnings-invoices-load" x-show="showLoadMore">
        <button type="button" @click="loadMore">Load More</button>
    </div>
</div>

@once
    @push('script')
        <script>
            if (!window.downloadBookingInvoicePdf) {
                window.downloadBookingInvoicePdf = async function(invoiceUrl) {
                    if (!invoiceUrl) {
                        return;
                    }

                    try {
                        const res = await fetch(invoiceUrl, {
                            method: 'GET',
                            credentials: 'same-origin',
                            headers: {
                                Accept: 'application/pdf',
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                        });
                        const contentType = (res.headers.get('Content-Type') || '').toLowerCase();
                        if (!res.ok || (!contentType.includes('application/pdf') && !contentType.includes(
                                'octet-stream'))) {
                            throw new Error('Invoice download failed');
                        }

                        let filename = 'Fursgo-Invoice.pdf';
                        const disposition = res.headers.get('Content-Disposition');
                        if (disposition) {
                            const utf = disposition.match(/filename\*=(?:UTF-8'')?([^;\n]+)/i);
                            const quoted = disposition.match(/filename="([^"]+)"/i);
                            const plain = disposition.match(/filename=([^;\s]+)/i);
                            if (utf && utf[1]) {
                                try {
                                    filename = decodeURIComponent(utf[1].trim().replace(/^"+|"+$/g, ''));
                                } catch (error) {
                                    filename = utf[1].trim();
                                }
                            } else if (quoted && quoted[1]) {
                                filename = quoted[1];
                            } else if (plain && plain[1]) {
                                filename = plain[1].replace(/^"+|"+$/g, '');
                            }
                        }

                        const blob = await res.blob();
                        const objectUrl = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = objectUrl;
                        link.download = filename;
                        link.rel = 'noopener';
                        document.body.appendChild(link);
                        link.click();
                        link.remove();
                        URL.revokeObjectURL(objectUrl);
                    } catch (error) {
                        console.error(error);
                        window.alert('Could not download the invoice. Please try again.');
                    }
                };
            }
        </script>
    @endpush
@endonce
