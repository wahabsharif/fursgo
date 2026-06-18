@props(['invoices' => []])

<div class="earnings-invoices" x-data="earningsInvoices(@js(array_values($invoices)))"
    x-on:range-calendar-changed.window="handleDateRangeChanged($event.detail)">
    <style>
        .earnings-invoices {
            color: #3B3731;
            font-family: Lato, sans-serif;
            margin-top: 3rem;
            position: relative;
        }

        .earnings-invoices-search {
            border-radius: 10px;
            background: rgba(59, 55, 49, 0.02);
            padding: 1.6rem 2rem;
            margin-bottom: 3rem;
            overflow: visible;
            position: relative;
            z-index: 5;
        }

        .earnings-invoices-search.is-date-picker-open {
            z-index: 100100;
        }

        .earnings-invoices-search-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .earnings-invoices-search-title {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-invoices-search-copy {
            margin: 0.25rem 0 0;
            color: #9C9790;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-invoices-export {
            border: 0;
            background: transparent;
            display: inline-flex;
            align-items: center;
            gap: 0.8rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
            text-decoration-line: underline;
            text-decoration-style: solid;
            text-decoration-skip-ink: auto;
            text-decoration-thickness: auto;
            text-underline-offset: auto;
            text-underline-position: from-font;
            cursor: pointer;
            padding: 0;
        }

        .earnings-invoices-fields {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 2rem;
            overflow: visible;
        }

        .earnings-invoices-field {
            min-width: 0;
            overflow: visible;
        }

        .earnings-invoices-field label {
            display: block;
            margin-bottom: 0.6rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .earnings-invoices-field input {
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .earnings-invoices-input-wrap {
            position: relative;
        }

        .earnings-invoices-input-wrap>svg {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            pointer-events: none;
        }

        .earnings-invoices-field input {
            width: 100%;
            height: 44px;
            border: 1px solid #D4D4D4;
            border-radius: 10px;
            background: #FFF;
            color: #3B3731;
            font: inherit;
            outline: none;
            padding: 0 1rem;
        }

        .earnings-invoices-field input.has-icon {
            padding-left: 3rem;
        }

        .earnings-invoices-field input:focus {
            border-color: #AFCD6F;
            box-shadow: 0 0 0 3px rgba(175, 205, 111, 0.18);
        }

        .earnings-invoices-date-trigger {
            width: 100%;
            height: 44px;
            border: 1px solid #D4D4D4;
            border-radius: 10px;
            background: #FFF;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            overflow: hidden;
            padding: 0 1rem 0 3rem;
            text-align: left;
            white-space: nowrap;
        }

        .earnings-invoices-date-trigger:focus {
            border-color: #AFCD6F;
            box-shadow: 0 0 0 3px rgba(175, 205, 111, 0.18);
            outline: none;
        }

        .earnings-invoices-date-placeholder {
            color: #9D9B98;
        }

        .earnings-invoices-date-picker {
            position: fixed;
            width: min(44rem, calc(100vw - 3rem));
            max-height: calc(100vh - 10rem);
            overflow: auto;
            z-index: 100100;
            border: 1px solid #E3E3E3;
            border-radius: 14px;
            background: #FFF;
            box-shadow: 0 16px 34px rgba(0, 0, 0, 0.12);
            color: #3B3731;
            font-family: Lato, sans-serif;
            padding: 1rem;
        }

        .earnings-invoices-date-picker .rdc {
            min-width: 38rem;
        }

        .earnings-invoices-date-picker-actions {
            display: flex;
            justify-content: flex-end;
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .earnings-invoices-date-picker-actions button {
            border: 1px solid #D4D4D4;
            border-radius: 999px;
            background: #FFF;
            color: #3B3731;
            cursor: pointer;
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 700;
            padding: 0.55rem 1.1rem;
        }

        .earnings-invoices-date-picker-actions button.is-primary {
            border-color: #AFCD6F;
            background: #AFCD6F;
            color: #FFF;
        }

        .earnings-invoices-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-bottom: 2.2rem;
            position: relative;
            z-index: 1;
        }

        .earnings-invoices-note {
            margin: 0;
            color: #9D9B98;
            font-size: 14px;
        }

        .earnings-invoices-period-toggle {
            display: inline-flex;
            align-items: center;
            overflow: hidden;
            border: 1px solid #D4D4D4;
            border-radius: 6px;
            background: #FFF;
        }

        .earnings-invoices-period-toggle button {
            min-width: 108px;
            border: 0;
            border-right: 1px solid #D4D4D4;
            background: transparent;
            color: #3B3731;
            cursor: pointer;
            font: inherit;
            font-size: 14px;
            padding: 0.65rem 1rem;
        }

        .earnings-invoices-period-toggle button:last-child {
            border-right: 0;
        }

        .earnings-invoices-period-toggle button.is-active {
            background: #FBAC83;
            color: #FFF;
            font-weight: 700;
        }

        .earnings-invoices-table-wrap {
            overflow-x: auto;
            position: relative;
            z-index: 1;
        }

        .earnings-invoices-table {
            width: 100%;
            min-width: 940px;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .earnings-invoices-table th,
        .earnings-invoices-table td {
            border-bottom: 1px solid #E3E3E3;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            line-height: normal;
            padding: 1.25rem 1rem;
            text-align: left;
            vertical-align: middle;
        }

        .earnings-invoices-table th {
            color: #000;
            font-weight: 600;
        }

        .earnings-invoices-table td {
            color: #3B3731;
            font-weight: 400;
        }

        .earnings-invoices-status {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 84px;
            border-radius: 100px;
            padding: 0.45rem 0.85rem;
            font-size: 14px;
            font-weight: 500;
        }

        .earnings-invoices-status.is-paid {
            background: rgba(186, 207, 142, 0.10);
            color: #AFCD6F;
        }

        .earnings-invoices-status.is-failed {
            background: #FFE2E2;
            color: #FF6E6E;
        }

        .earnings-invoices-status.is-refunded {
            background: #FFF4E4;
            color: #FFAE37;
        }

        .earnings-invoices-download-cell {
            border-left: 1px solid #E3E3E3;
            text-align: center !important;
        }

        .earnings-invoices-icon-btn {
            border: 0;
            background: transparent;
            color: #3B3731;
            cursor: pointer;
            line-height: 0;
            padding: 0.2rem;
        }

        .earnings-invoices-empty {
            color: #9D9B98 !important;
            text-align: center !important;
        }

        .earnings-invoices-load {
            display: flex;
            justify-content: center;
            margin-top: 4rem;
        }

        .earnings-invoices-load button {
            min-width: 124px;
            border: 1px solid #3B3731;
            border-radius: 999px;
            background: #FFF;
            color: #3B3731;
            cursor: pointer;
            font: inherit;
            font-size: 16px;
            padding: 0.75rem 1.4rem;
        }

        @media (max-width: 900px) {

            .earnings-invoices-fields,
            .earnings-invoices-toolbar {
                grid-template-columns: 1fr;
                flex-direction: column;
                align-items: stretch;
            }

            .earnings-invoices-date-picker {
                width: calc(100vw - 3rem);
            }

            .earnings-invoices-date-picker .rdc {
                min-width: 0;
            }

            .earnings-invoices-date-picker .rdc-panel {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <section class="earnings-invoices-search" :class="{ 'is-date-picker-open': dateDropdownOpen }"
        aria-label="Invoice search">
        <div class="earnings-invoices-search-top">
            <div>
                <h3 class="earnings-invoices-search-title">Invoice Search</h3>
                <p class="earnings-invoices-search-copy">Search by date, reference or invoice number.</p>
            </div>
            <button type="button" class="earnings-invoices-export" @click="exportAll()">
                <span>Export All</span>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19" fill="none"
                    aria-hidden="true">
                    <path
                        d="M0.5 15.5V17C0.5 17.3978 0.643668 17.7794 0.8994 18.0607C1.15513 18.342 1.50198 18.5 1.86364 18.5H14.1364C14.498 18.5 14.8449 18.342 15.1006 18.0607C15.3563 17.7794 15.5 17.3978 15.5 17V15.5"
                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    <path d="M7.99997 0.5V12.875M12.0909 8.75L7.99997 13.25L3.90906 8.75" stroke="#3B3731"
                        stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>

        <div class="earnings-invoices-fields">
            <div class="earnings-invoices-field">
                <label for="earnings-invoices-date">Date</label>
                <div class="earnings-invoices-input-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14" viewBox="0 0 15 14"
                        fill="none">
                        <path
                            d="M0.5 6.83375C0.5 4.31955 0.5 3.06212 1.31621 2.28139C2.13243 1.50067 3.4452 1.5 6.07143 1.5H8.85714C11.4834 1.5 12.7968 1.5 13.6124 2.28139C14.4279 3.06279 14.4286 4.31955 14.4286 6.83375V8.16718C14.4286 10.6814 14.4286 11.9388 13.6124 12.7195C12.7961 13.5003 11.4834 13.5009 8.85714 13.5009H6.07143C3.4452 13.5009 2.13173 13.5009 1.31621 12.7195C0.500696 11.9381 0.5 10.6814 0.5 8.16718V6.83375Z"
                            stroke="#3B3731" />
                        <path d="M3.98256 1.50008V0.5M10.9468 1.50008V0.5M0.848633 4.83367H14.0808" stroke="#3B3731"
                            stroke-linecap="round" />
                        <path
                            d="M11.6433 10.1656C11.6433 10.3424 11.5699 10.512 11.4393 10.6371C11.3087 10.7621 11.1316 10.8323 10.9468 10.8323C10.7621 10.8323 10.585 10.7621 10.4544 10.6371C10.3238 10.512 10.2504 10.3424 10.2504 10.1656C10.2504 9.9888 10.3238 9.81922 10.4544 9.69418C10.585 9.56915 10.7621 9.4989 10.9468 9.4989C11.1316 9.4989 11.3087 9.56915 11.4393 9.69418C11.5699 9.81922 11.6433 9.9888 11.6433 10.1656ZM11.6433 7.49875C11.6433 7.67557 11.5699 7.84516 11.4393 7.97019C11.3087 8.09522 11.1316 8.16547 10.9468 8.16547C10.7621 8.16547 10.585 8.09522 10.4544 7.97019C10.3238 7.84516 10.2504 7.67557 10.2504 7.49875C10.2504 7.32192 10.3238 7.15234 10.4544 7.02731C10.585 6.90227 10.7621 6.83203 10.9468 6.83203C11.1316 6.83203 11.3087 6.90227 11.4393 7.02731C11.5699 7.15234 11.6433 7.32192 11.6433 7.49875ZM8.16113 10.1656C8.16113 10.3424 8.08776 10.512 7.95715 10.6371C7.82655 10.7621 7.64941 10.8323 7.4647 10.8323C7.28 10.8323 7.10286 10.7621 6.97225 10.6371C6.84165 10.512 6.76828 10.3424 6.76828 10.1656C6.76828 9.9888 6.84165 9.81922 6.97225 9.69418C7.10286 9.56915 7.28 9.4989 7.4647 9.4989C7.64941 9.4989 7.82655 9.56915 7.95715 9.69418C8.08776 9.81922 8.16113 9.9888 8.16113 10.1656ZM8.16113 7.49875C8.16113 7.67557 8.08776 7.84516 7.95715 7.97019C7.82655 8.09522 7.64941 8.16547 7.4647 8.16547C7.28 8.16547 7.10286 8.09522 6.97225 7.97019C6.84165 7.84516 6.76828 7.67557 6.76828 7.49875C6.76828 7.32192 6.84165 7.15234 6.97225 7.02731C7.10286 6.90227 7.28 6.83203 7.4647 6.83203C7.64941 6.83203 7.82655 6.90227 7.95715 7.02731C8.08776 7.15234 8.16113 7.32192 8.16113 7.49875ZM4.67899 10.1656C4.67899 10.3424 4.60562 10.512 4.47501 10.6371C4.3444 10.7621 4.16727 10.8323 3.98256 10.8323C3.79786 10.8323 3.62072 10.7621 3.49011 10.6371C3.35951 10.512 3.28613 10.3424 3.28613 10.1656C3.28613 9.9888 3.35951 9.81922 3.49011 9.69418C3.62072 9.56915 3.79786 9.4989 3.98256 9.4989C4.16727 9.4989 4.3444 9.56915 4.47501 9.69418C4.60562 9.81922 4.67899 9.9888 4.67899 10.1656ZM4.67899 7.49875C4.67899 7.67557 4.60562 7.84516 4.47501 7.97019C4.3444 8.09522 4.16727 8.16547 3.98256 8.16547C3.79786 8.16547 3.62072 8.09522 3.49011 7.97019C3.35951 7.84516 3.28613 7.67557 3.28613 7.49875C3.28613 7.32192 3.35951 7.15234 3.49011 7.02731C3.62072 6.90227 3.79786 6.83203 3.98256 6.83203C4.16727 6.83203 4.3444 6.90227 4.47501 7.02731C4.60562 7.15234 4.67899 7.32192 4.67899 7.49875Z"
                            fill="#3B3731" />
                    </svg>
                    <button id="earnings-invoices-date" x-ref="dateTrigger" type="button"
                        class="earnings-invoices-date-trigger" @click="toggleDateDropdown()"
                        :aria-expanded="dateDropdownOpen ? 'true' : 'false'" aria-haspopup="dialog">
                        <span x-text="dateRangeLabel"
                            :class="{ 'earnings-invoices-date-placeholder': !dateRange.start && !dateRange.end }"></span>
                    </button>
                </div>
                <template x-teleport="body">
                    <div class="earnings-invoices-date-picker" data-earnings-invoices-date-picker x-cloak
                        x-show="dateDropdownOpen" :style="datePickerStyle"
                        x-transition:enter="transition ease-out duration-180"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-140"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 -translate-y-2">
                        <x-ui.range-date-calendar id="earnings-invoices-date-range"
                            start-name="earnings_invoice_start_date" end-name="earnings_invoice_end_date"
                            calendar-width="100%" />
                        <div class="earnings-invoices-date-picker-actions">
                            <button type="button" @click="clearDateRange()">Clear</button>
                            <button type="button" class="is-primary" @click="dateDropdownOpen = false">Apply</button>
                        </div>
                    </div>
                </template>
            </div>
            <div class="earnings-invoices-field">
                <label for="earnings-invoices-reference">Reference</label>
                <input id="earnings-invoices-reference" type="text" x-model.debounce.150ms="filters.reference"
                    placeholder="Full Groom">
            </div>
            <div class="earnings-invoices-field">
                <label for="earnings-invoices-number">Invoice Number</label>
                <input id="earnings-invoices-number" type="text" x-model.debounce.150ms="filters.invoice"
                    placeholder="CI-2025-0118">
            </div>
        </div>
    </section>

    <div class="earnings-invoices-toolbar">
        <p class="earnings-invoices-note">Invoices are automatically generated for every completed booking.</p>
        <div class="earnings-invoices-period-toggle" role="group" aria-label="Invoice period">
            <button type="button" :class="{ 'is-active': period === 'month' }" @click="setPeriod('month')">This
                Month</button>
            <button type="button" :class="{ 'is-active': period === 'last-month' }"
                @click="setPeriod('last-month')">Last Month</button>
            <button type="button" :class="{ 'is-active': period === 'last-3-months' }"
                @click="setPeriod('last-3-months')">Last 3 Months</button>
        </div>
    </div>

    <div class="earnings-invoices-table-wrap">
        <table class="earnings-invoices-table">
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
                    <th class="earnings-invoices-download-cell">Download</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="invoice in visibleRows" :key="invoice.invoice_no + '-' + invoice.booking_reference">
                    <tr>
                        <td x-text="invoice.date"></td>
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
                        <td class="earnings-invoices-download-cell">
                            <button type="button" class="earnings-invoices-icon-btn" :disabled="!invoice.invoice_url"
                                @click="download(invoice.invoice_url)" aria-label="Download invoice">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="19"
                                    viewBox="0 0 16 19" fill="none">
                                    <path
                                        d="M0.5 15.5V17C0.5 17.3978 0.643668 17.7794 0.8994 18.0607C1.15513 18.342 1.50198 18.5 1.86364 18.5H14.1364C14.498 18.5 14.8449 18.342 15.1006 18.0607C15.3563 17.7794 15.5 17.3978 15.5 17V15.5"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path d="M8.00009 0.5V12.875M12.091 8.75L8.00009 13.25L3.90918 8.75"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
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

    <div class="earnings-invoices-load" x-show="visibleRows.length < filteredRows.length">
        <button type="button" @click="limit += 8">Load More</button>
    </div>
</div>

@once
    @push('script')
        <script>
            window.earningsInvoices = function(invoices) {
                return {
                    invoices,
                    filters: {
                        reference: '',
                        invoice: '',
                    },
                    dateDropdownOpen: false,
                    dateRange: {
                        start: '',
                        end: '',
                    },
                    datePickerPosition: {
                        top: -9999,
                        left: -9999,
                    },
                    period: 'all',
                    limit: 8,
                    init() {
                        window.addEventListener('scroll', () => {
                            if (this.dateDropdownOpen) {
                                this.positionDatePicker();
                            }
                        }, true);
                        window.addEventListener('resize', () => {
                            if (this.dateDropdownOpen) {
                                this.positionDatePicker();
                            }
                        });
                        document.addEventListener('click', (event) => {
                            if (!this.dateDropdownOpen) {
                                return;
                            }

                            const trigger = this.$refs.dateTrigger;
                            const picker = document.querySelector('[data-earnings-invoices-date-picker]');
                            const target = event.target;

                            if (trigger?.contains(target) || picker?.contains(target)) {
                                return;
                            }

                            this.dateDropdownOpen = false;
                        });
                    },
                    get datePickerStyle() {
                        return `top: ${this.datePickerPosition.top}px; left: ${this.datePickerPosition.left}px;`;
                    },
                    get dateRangeLabel() {
                        if (this.dateRange.start && this.dateRange.end) {
                            return `${this.formatDateLabel(this.dateRange.start)} — ${this.formatDateLabel(this.dateRange.end)}`;
                        }

                        if (this.dateRange.start) {
                            return this.formatDateLabel(this.dateRange.start);
                        }

                        return 'Select date range';
                    },
                    get filteredRows() {
                        return this.invoices.filter((invoice) => {
                            const haystack = [
                                invoice.reference,
                                invoice.booking_reference,
                                invoice.client,
                            ].join(' ').toLowerCase();

                            return this.matchesPeriod(invoice) &&
                                this.matchesSelectedDateRange(invoice) &&
                                haystack.includes(this.filters.reference.toLowerCase().trim()) &&
                                invoice.invoice_no.toLowerCase().includes(this.filters.invoice.toLowerCase()
                                    .trim());
                        });
                    },
                    get visibleRows() {
                        return this.filteredRows.slice(0, this.limit);
                    },
                    setPeriod(period) {
                        this.period = period;
                        this.limit = 8;
                    },
                    toggleDateDropdown() {
                        if (this.dateDropdownOpen) {
                            this.dateDropdownOpen = false;

                            return;
                        }

                        this.positionDatePicker();
                        this.dateDropdownOpen = true;
                    },
                    positionDatePicker() {
                        const trigger = this.$refs.dateTrigger;
                        if (!trigger) {
                            return;
                        }

                        const rect = trigger.getBoundingClientRect();
                        const pickerWidth = Math.min(704, window.innerWidth - 48);
                        const left = Math.min(Math.max(rect.left, 24), window.innerWidth - pickerWidth - 24);

                        this.datePickerPosition = {
                            top: rect.bottom + 10,
                            left,
                        };
                    },
                    handleDateRangeChanged(detail) {
                        if (detail?.componentId !== 'earnings-invoices-date-range') {
                            return;
                        }

                        this.dateRange.start = detail.start || '';
                        this.dateRange.end = detail.end || '';
                        this.limit = 8;
                    },
                    clearDateRange() {
                        this.dateRange.start = '';
                        this.dateRange.end = '';
                        this.limit = 8;
                        window.dispatchEvent(new CustomEvent('range-calendar-set', {
                            detail: {
                                componentId: 'earnings-invoices-date-range',
                                start: '',
                                end: '',
                            },
                        }));
                    },
                    matchesSelectedDateRange(invoice) {
                        if (!this.dateRange.start && !this.dateRange.end) {
                            return true;
                        }

                        if (!invoice.date_iso) {
                            return false;
                        }

                        const invoiceDate = invoice.date_iso;
                        const start = this.dateRange.start || this.dateRange.end;
                        const end = this.dateRange.end || this.dateRange.start;
                        const rangeStart = start <= end ? start : end;
                        const rangeEnd = start <= end ? end : start;

                        return invoiceDate >= rangeStart && invoiceDate <= rangeEnd;
                    },
                    matchesPeriod(invoice) {
                        if (!invoice.date_iso) {
                            return false;
                        }

                        if (this.period === 'all') {
                            return true;
                        }

                        const date = new Date(`${invoice.date_iso}T00:00:00`);
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
                    formatDateLabel(dateIso) {
                        if (!dateIso) {
                            return '';
                        }

                        const date = new Date(`${dateIso}T00:00:00`);

                        return date.toLocaleDateString('en-GB', {
                            day: '2-digit',
                            month: 'short',
                            year: 'numeric',
                        });
                    },
                    formatMoney(value) {
                        return `£${Number(value || 0).toFixed(2)}`;
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
                    },
                };
            };

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
