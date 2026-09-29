@props(['profile'])

@php
$payments = $profile['payments'] ?? [];
$rawRows = $payments['rows'] ?? [];
$filters = $payments['filters'] ?? [
'all' => count($rawRows),
'processed' => 0,
'credits' => 0,
'refunded' => 0,
'open_refund' => 0,
'failed' => 0,
];
$customerName = $profile['name'] ?? 'Jane Doe';
$defaultNote = [
'text' => 'Customer payment reviewed. No further action required at this time.',
'author' => 'Michelle M',
'time' => '18 Apr 2025',
'tag' => 'Internal only',
];

$buildPaymentDetail = function (array $row) use ($customerName, $defaultNote): array {
if (! empty($row['detail'])) {
return $row['detail'];
}

$status = $row['status'] ?? 'processed';
$amount = $row['amount'] ?? '£0.00';
$invoice = $row['invoice_id'] ?? '';
$booking = $row['booking_id'] ?? '—';
$provider = $row['provider'] ?? '—';
$method = $row['method'] ?? '—';
$date = $row['date'] ?? '';
$description = $row['description'] ?? '';
$isGroomer = ($row['provider_type'] ?? '') === 'groomer';
$serviceType = $isGroomer ? 'Groomer booking' : (($row['provider_type'] ?? '') === 'space' ? 'Space booking' : 'Payment');

$base = [
'variant' => $status === 'credits' ? 'processed' : $status,
'charge_label' => 'Charge - ' . $serviceType,
'summary_date' => $date,
'amount_label' => 'Amount charged',
'hero_amount' => $amount,
'amount_tone' => $row['amount_tone'] ?? 'default',
'amount_note' => '',
'status_side_label' => 'Payment status',
'status_side_value' => $row['status_label'] ?? ucfirst($status),
'status_key' => $status,
'status_side_note' => '',
'hero_sub' => trim($method . ' · ' . $date, ' ·'),
'rows' => [
['label' => 'Invoice ID', 'value' => $invoice],
['label' => 'Transaction type', 'value' => 'Charge'],
['label' => 'Linked booking', 'value' => $booking],
['label' => 'Description', 'value' => $description],
['label' => 'Business', 'value' => $provider],
['label' => 'Date & time', 'value' => $date . ' · 10:14 AM'],
['label' => 'Payment method', 'value' => $method],
['label' => 'Payment status', 'value' => $row['status_label'] ?? '', 'tone' => $status],
['label' => 'Processor reference', 'value' => 'pi_' . strtolower(str_replace(['INV-', '-'], ['', ''], $invoice))],
],
'price_lines' => [
['label' => 'Service', 'value' => $amount],
],
'total_label' => 'Total charged to customer',
'total' => $amount,
'notes' => [$defaultNote],
'timeline' => [
['tone' => 'green', 'title' => 'Payment processed successfully', 'time' => $date . ' · 10:14'],
['tone' => 'gray', 'title' => 'Payment initiated by customer at checkout', 'time' => $date . ' · 10:12'],
],
'callout' => null,
'callout_rows' => [],
];

if ($status === 'processed') {
$base['payout_label'] = $isGroomer ? 'Groomer payout' : 'Host payout';
$base['payout_value'] = 'Paid out';
$base['payout_key'] = 'processed';
$base['price_lines'] = [
['label' => 'Full groom (service)', 'value' => '£50.00'],
['label' => 'Ear cleaning (add-on)', 'value' => '£5.00'],
['label' => 'De-shed treatment (add-on)', 'value' => '£5.00'],
['label' => 'Discount applied', 'value' => '-£5.00', 'tone' => 'discount'],
];
$base['total'] = $amount;
$base['hero_amount'] = $amount;
$base['rows'] = [
['label' => 'Invoice ID', 'value' => $invoice],
['label' => 'Transaction type', 'value' => 'Charge'],
['label' => 'Linked booking', 'value' => $booking],
['label' => 'Description', 'value' => $description],
['label' => 'Business', 'value' => $provider . ($isGroomer ? ' SE13' : '')],
['label' => 'Date & time', 'value' => $date . ' · 10:14 AM'],
['label' => 'Payment method', 'value' => $method],
['label' => 'Settled to groomer', 'value' => '17 Jun 2025'],
['label' => 'Processor reference', 'value' => 'pi_3OxK2mR2eZv0Y'],
];
$base['callout_title'] = 'Groomer payout';
$base['callout'] = [
'title' => 'Payout completed',
'body' => $base['total'] . ' was successfully paid to ' . $provider . ' on 17 Jun 2025 via the weekly Tuesday payout cycle.',
];
$base['callout_rows'] = [
['label' => 'Payout amount', 'value' => $base['total']],
['label' => 'Recipient', 'value' => $provider],
['label' => 'Payout date', 'value' => '17 Jun 2025'],
['label' => 'Payout method', 'value' => 'Bank transfer'],
['label' => 'Payout reference', 'value' => 'po_7Kx9mR2eZv'],
];
$base['timeline'] = [
['tone' => 'green', 'title' => 'Groomer payout of £55.00 processed to Pawfect Salon', 'time' => '17 Jun 2025 · 09:00 · weekly Tuesday payout cycle'],
['tone' => 'green', 'title' => 'Payment of £55.00 settled and cleared', 'time' => '12 Jun 2025 · 08:30'],
['tone' => 'green', 'title' => 'Payment of £55.00 processed successfully · Visa **** 4563', 'time' => '10 Jun 2025 · 21:50'],
['tone' => 'blue', 'title' => 'Payment authorised by card issuer', 'time' => '10 Jun 2024 · 21:48'],
['tone' => 'gray', 'title' => 'Payment initiated by customer at checkout', 'time' => '10 Jun 2025 · 21:45'],
];
$base['hero_sub'] = $method . ' · ' . $date;
}

if ($status === 'open_refund') {
$base['variant'] = 'open_refund';
$base['charge_label'] = 'Charge - ' . $serviceType;
$base['amount_label'] = 'Amount charged';
$base['status_side_label'] = 'Refund status';
$base['status_side_value'] = 'In progress';
$base['status_key'] = 'open_refund';
$base['payout_label'] = $isGroomer ? 'Groomer payout' : 'Space Host payout';
$base['payout_value'] = 'On hold';
$base['payout_key'] = 'open_refund';
$base['price_lines'] = [
['label' => 'Full groom (service)', 'value' => '£50.00'],
['label' => 'Nail trim (add-on)', 'value' => '£5.00'],
['label' => 'Discount applied', 'value' => '-£5.00', 'tone' => 'discount'],
];
$base['total'] = $amount;
$base['hero_amount'] = $amount;
$base['aside_extra_label'] = 'Refund in progress';
$base['aside_extra_value'] = '-£35.00';
$base['aside_extra_tone'] = 'open_refund';
$base['rows'] = [
['label' => 'Invoice ID', 'value' => $invoice],
['label' => 'Transaction type', 'value' => 'Charge'],
['label' => 'Linked booking', 'value' => $booking],
['label' => 'Description', 'value' => $description],
['label' => 'Business', 'value' => $provider],
['label' => 'Date & time', 'value' => $date . ' · 10:14 AM'],
['label' => 'Payment method', 'value' => $method],
['label' => 'Payment status', 'value' => 'Open refund', 'tone' => 'open_refund'],
['label' => 'Processor reference', 'value' => 'pi_' . strtolower(str_replace(['INV-', '-'], ['', ''], $invoice))],
];
$base['callout_title'] = 'Refund detail';
$base['callout_action'] = 'View';
$base['callout'] = [
'title' => 'Refund in progress',
'body' => 'Refund of £35.00 in progress — awaiting payment processor. Expected 3-5 business days.',
];
$base['callout_rows'] = [
['label' => 'Refund amount', 'value' => '£35.00'],
['label' => 'Reason', 'value' => 'Partial service issue'],
['label' => 'Method', 'value' => $method],
['label' => 'Initiated by', 'value' => 'Michelle M'],
['label' => 'Processor ref', 'value' => 're_3OxK2mR2eZv'],
];
$base['timeline'] = [
['tone' => 'orange', 'title' => 'Refund of £35.00 initiated by Michelle M — awaiting processor', 'time' => '07 Apr 2025 · 14:32'],
['tone' => 'orange', 'title' => 'Groomer payout placed on hold — dispute open', 'time' => '05 Apr 2025 · 11:20'],
['tone' => 'red', 'title' => 'Dispute raised by customer against this booking', 'time' => '03 Apr 2025 · 09:47'],
['tone' => 'blue', 'title' => 'Payment of £55.00 processed successfully · Visa **** 4563', 'time' => '10 Jun 2025 · 21:50'],
['tone' => 'gray', 'title' => 'Payment authorised by card issuer', 'time' => '10 Jun 2024 · 21:48'],
['tone' => 'gray', 'title' => 'Payment initiated by customer at checkout', 'time' => '10 Jun 2025 · 21:45'],
];
}

if ($status === 'refunded') {
$base['variant'] = 'refunded';
$base['charge_label'] = 'Refund - ' . $serviceType;
$base['amount_label'] = 'Original charged';
$base['hero_amount'] = '- £35.00';
$base['amount_tone'] = 'refund';
$base['status_side_label'] = 'Amount refunded';
$base['status_side_value'] = '-£35.00';
$base['status_key'] = 'refunded';
$base['payout_label'] = 'Refund status';
$base['payout_value'] = 'Processed';
$base['payout_key'] = 'refunded';
$base['price_lines'] = [
['label' => 'Half-day (service)', 'value' => '£25.00'],
['label' => 'Storage locker', 'value' => '£5.00'],
['label' => 'Deep clean', 'value' => '£10.00'],
['label' => 'Discount applied', 'value' => '-£5.00', 'tone' => 'discount'],
];
$base['total'] = '£35.00';
$base['total_label'] = 'Total originally charged';
$base['aside_extra_label'] = 'Amount refunded';
$base['aside_extra_value'] = '-£35.00 (full)';
$base['aside_extra_tone'] = 'refunded';
$base['rows'] = [
['label' => 'Invoice ID', 'value' => $invoice],
['label' => 'Transaction type', 'value' => 'Charge'],
['label' => 'Linked booking', 'value' => $booking],
['label' => 'Original charge', 'value' => '£35.00 · 12 Apr 2025'],
['label' => 'Refund reason', 'value' => 'Groomer cancellation'],
['label' => 'Refunded to', 'value' => $method],
['label' => 'Refund completed', 'value' => '15 Apr 2025 · 09:30 AM'],
['label' => 'Payment status', 'value' => 'Processed', 'tone' => 'refunded'],
];
$base['callout_title'] = 'Refund detail';
$base['callout'] = [
'title' => 'Refund processed',
'body' => 'Refund of £35.00 processed — awaiting payment processor. Expected 3–5 business days.',
];
$base['callout_rows'] = [
['label' => 'Refund amount', 'value' => '£35.00'],
['label' => 'Reason', 'value' => 'Groomer cancellation'],
['label' => 'Method', 'value' => $method],
['label' => 'Initiated by', 'value' => 'System'],
['label' => 'Processor ref', 'value' => 're_9Kx2mR2eZv'],
];
$base['timeline'] = [
['tone' => 'blue', 'title' => 'Refunded of £35.00 completed · returned to Visa **** 4532', 'time' => '07 Apr 2025 · 14:32'],
['tone' => 'orange', 'title' => 'Refund initiated by Michelle M', 'time' => '05 Apr 2025 · 11:20'],
['tone' => 'red', 'title' => 'Booking cancelled by groomer - insufficient notice given', 'time' => '03 Apr 2025 · 09:47'],
['tone' => 'green', 'title' => 'Payment of £35.00 processed · Visa **** 4532', 'time' => '10 Jun 2025 · 21:50'],
];
$base['hero_sub'] = $method . ' · ' . $date;
}

if ($status === 'failed') {
$base['variant'] = 'failed';
$base['charge_label'] = 'Charge attempt - ' . $serviceType;
$base['amount_label'] = 'Amount charge attempt';
$base['amount_note'] = $description !== '' ? explode(' - ', $description)[0] . ' - ' . $provider : $provider;
$base['status_side_label'] = 'Payment status';
$base['status_side_value'] = 'Failed';
$base['status_key'] = 'failed';
$base['status_side_note'] = $date . ' · 10:14 AM';
$base['failure_reason'] = 'Card declined - Insufficient funds';
$base['price_lines'] = [
['label' => 'Full groom', 'value' => '£50.00'],
['label' => 'Nail trim', 'value' => '£5.00'],
];
$base['total'] = '£55.00';
$base['hero_amount'] = $amount;
$base['total_label'] = 'Attempt charge total';
$base['aside_extra_label'] = 'Amount collected';
$base['aside_extra_value'] = '£00.00 - failed';
$base['aside_extra_tone'] = 'failed';
$base['aside_payout_label'] = 'Groomer payout';
$base['aside_payout_value'] = 'Pending payment';
$base['rows'] = [
['label' => 'Invoice ID', 'value' => $invoice],
['label' => 'Transaction type', 'value' => 'Charge attempt'],
['label' => 'Linked booking', 'value' => $booking],
['label' => 'Description', 'value' => $description],
['label' => 'Business', 'value' => $provider],
['label' => 'Attempt date & time', 'value' => $date . ' · 10:14 AM'],
['label' => 'Payment method', 'value' => $method],
['label' => 'Payment status', 'value' => 'Failed', 'tone' => 'failed'],
['label' => 'Failure code', 'value' => 'card_declined'],
];
$base['callout_title'] = 'Payment failure details';
$base['callout'] = [
'title' => 'Payment failed',
'body' => 'Customers booking ' . $booking . ' has not been confirmed because of payment failure.',
];
$base['callout_rows'] = [
['label' => 'Payment attempt', 'value' => '£55.00'],
['label' => 'Status', 'value' => 'Failed'],
['label' => 'Method', 'value' => $method],
['label' => 'Failure code', 'value' => 'card_declined'],
];
$base['timeline'] = [
['tone' => 'orange', 'title' => 'Payment attempt failed - card declined (insufficient funds)', 'time' => '07 Apr 2025 · 14:32'],
['tone' => 'orange', 'title' => 'Payment authorisation requested from card issuer', 'time' => '05 Apr 2025 · 11:20'],
['tone' => 'red', 'title' => 'Payment initiated by customer at checkout', 'time' => '03 Apr 2025 · 09:47'],
];
$base['hero_sub'] = $method . ' · ' . $date . ' · 10:14 AM';
}

if ($status === 'credits') {
$base['variant'] = 'processed';
$base['charge_label'] = 'Credit - Referral / welcome';
$base['callout'] = [
'title' => 'Credit applied',
'body' => 'Credit of ' . $amount . ' was applied to ' . $customerName . '\'s account.',
];
$base['callout_title'] = 'Credit detail';
}

return $base;
};

$rows = collect($rawRows)->map(function ($row) use ($buildPaymentDetail) {
$row['detail'] = $buildPaymentDetail($row);
return $row;
})->values()->all();
$groupedRows = collect($rows)->groupBy(fn ($row) => $row['month'] ?? 'Other');
@endphp

<div
    class="admin-co-payments"
    x-data="{
        paymentFilter: 'all',
        paymentSearch: '',
        selectedIds: [],
        selectedPayment: null,
        allIds: @js(collect($rows)->pluck('id')->values()),
        matchesPayment(status, invoiceId, description, provider, bookingId, method, note) {
            const q = (this.paymentSearch || '').trim().toLowerCase();
            const statusOk = this.paymentFilter === 'all' || this.paymentFilter === status;
            if (!statusOk) return false;
            if (!q) return true;
            return invoiceId.toLowerCase().includes(q)
                || description.toLowerCase().includes(q)
                || provider.toLowerCase().includes(q)
                || bookingId.toLowerCase().includes(q)
                || method.toLowerCase().includes(q)
                || (note || '').toLowerCase().includes(q);
        },
        isSelected(id) {
            return this.selectedIds.includes(id);
        },
        toggleOne(id) {
            if (this.isSelected(id)) {
                this.selectedIds = this.selectedIds.filter((x) => x !== id);
            } else {
                this.selectedIds.push(id);
            }
            this.syncPaymentSelection();
        },
        get allSelected() {
            return this.allIds.length > 0 && this.selectedIds.length === this.allIds.length;
        },
        toggleAll() {
            this.selectedIds = this.allSelected ? [] : [...this.allIds];
            this.syncPaymentSelection();
        },
        monthVisible(statuses, haystacks) {
            if (this.paymentFilter !== 'all' && !statuses.includes(this.paymentFilter)) return false;
            const q = (this.paymentSearch || '').trim().toLowerCase();
            if (!q) return true;
            return haystacks.some((text) => (text || '').toLowerCase().includes(q));
        },
        syncPaymentSelection() {
            this.$dispatch('admin-payments-selection-changed', {
                count: this.selectedIds.length,
                ids: [...this.selectedIds],
            });
        },
        openPayment(row) {
            this.selectedPayment = row;
            this.$dispatch('admin-payment-selected', { payment: row });
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },
        closePayment() {
            this.selectedPayment = null;
            this.$dispatch('admin-payment-closed');
        },
    }"
    x-init="syncPaymentSelection()"
    @admin-payment-close-request.window="closePayment()">
    <div class="admin-co-pay-list" x-show="!selectedPayment" x-cloak>
        <div class="admin-co-overview-head">
            <h2 class="admin-page-title mb-0">Payments</h2>
            <div class="admin-co-pay-head-actions">
                <button type="button" class="admin-btn-outline">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="16" viewBox="0 0 14 16" fill="none" aria-hidden="true">
                        <path d="M8.5 0.75H3.25C2.91848 0.75 2.60054 0.881696 2.36612 1.11612C2.1317 1.35054 2 1.66848 2 2V14C2 14.3315 2.1317 14.6495 2.36612 14.8839C2.60054 15.1183 2.91848 15.25 3.25 15.25H10.75C11.0815 15.25 11.3995 15.1183 11.6339 14.8839C11.8683 14.6495 12 14.3315 12 14V4.25L8.5 0.75Z" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M8.5 0.75V4.25H12" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M4.75 8.25H9.25M4.75 11H9.25M4.75 5.5H6.5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                    </svg>
                    Statement PDF
                </button>
                <button type="button" class="admin-btn-dark">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 13 13" fill="none" aria-hidden="true">
                        <path d="M6.22329 9.46679C6.13909 9.43398 6.05645 9.37703 5.97536 9.29593L3.5425 6.864C3.45212 6.77362 3.40507 6.66715 3.40136 6.54457C3.39764 6.422 3.44469 6.30965 3.5425 6.2075C3.64526 6.10474 3.75576 6.05243 3.874 6.05057C3.99286 6.04872 4.10336 6.09917 4.2055 6.20193L6.03571 8.03214V0.464292C6.03571 0.332435 6.07998 0.221935 6.1685 0.132792C6.25702 0.0436494 6.36752 -0.000612643 6.5 6.40392e-06C6.63248 0.000625451 6.74298 0.0448875 6.8315 0.132792C6.92002 0.220697 6.96429 0.331197 6.96429 0.464292V8.03214L8.7945 6.20193C8.88488 6.11155 8.99229 6.06419 9.11671 6.05986C9.24114 6.05553 9.35443 6.10474 9.45657 6.2075C9.55562 6.30965 9.60607 6.41922 9.60793 6.53622C9.60979 6.65322 9.55964 6.76248 9.4575 6.864L7.02464 9.29686C6.94417 9.37734 6.86152 9.43398 6.77671 9.46679C6.69252 9.4996 6.60029 9.516 6.5 9.516C6.39971 9.516 6.30748 9.4996 6.22329 9.46679ZM1.50057 13C1.07281 13 0.715928 12.857 0.429928 12.571C0.143928 12.285 0.000619048 11.9278 0 11.4994V9.71379C0 9.58193 0.044262 9.47174 0.132786 9.38322C0.22131 9.29469 0.33181 9.25012 0.464286 9.2495C0.596762 9.24888 0.707262 9.29345 0.795786 9.38322C0.884309 9.47298 0.928571 9.58317 0.928571 9.71379V11.4994C0.928571 11.6424 0.988 11.7737 1.10686 11.8931C1.22571 12.0126 1.35664 12.072 1.49964 12.0714H11.5004C11.6427 12.0714 11.7737 12.012 11.8931 11.8931C12.0126 11.7743 12.072 11.643 12.0714 11.4994V9.71379C12.0714 9.58193 12.1157 9.47174 12.2042 9.38322C12.2927 9.29469 12.4032 9.25012 12.5357 9.2495C12.6682 9.24888 12.7787 9.29345 12.8672 9.38322C12.9557 9.47298 13 9.58317 13 9.71379V11.4994C13 11.9272 12.857 12.2841 12.571 12.5701C12.285 12.8561 11.9278 12.9994 11.4994 13H1.50057Z" fill="#FDFDFD" />
                    </svg>
                    Export Data
                </button>
            </div>
        </div>

        <div class="admin-po-toolbar admin-co-pay-toolbar">
            <div class="admin-po-filters" role="tablist" aria-label="Payment status filters">
                <button type="button" class="admin-po-filter is-all" :class="{ 'is-active': paymentFilter === 'all' }" @click="paymentFilter = 'all'">
                    All ({{ $filters['all'] }})
                </button>
                <button type="button" class="admin-po-filter is-processed" :class="{ 'is-active': paymentFilter === 'processed' }" @click="paymentFilter = 'processed'">
                    Processed ({{ $filters['processed'] }})
                </button>
                <button type="button" class="admin-po-filter is-credits" :class="{ 'is-active': paymentFilter === 'credits' }" @click="paymentFilter = 'credits'">
                    Credits ({{ $filters['credits'] }})
                </button>
                <button type="button" class="admin-po-filter is-refunded" :class="{ 'is-active': paymentFilter === 'refunded' }" @click="paymentFilter = 'refunded'">
                    Refunded ({{ $filters['refunded'] }})
                </button>
                <button type="button" class="admin-po-filter is-open-refund" :class="{ 'is-active': paymentFilter === 'open_refund' }" @click="paymentFilter = 'open_refund'">
                    Open refund ({{ $filters['open_refund'] }})
                </button>
                <button type="button" class="admin-po-filter is-failed" :class="{ 'is-active': paymentFilter === 'failed' }" @click="paymentFilter = 'failed'">
                    Failed ({{ $filters['failed'] }})
                </button>
            </div>

            <div class="admin-po-tools">
                <label class="admin-po-search">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 14 14" fill="none" aria-hidden="true">
                        <circle cx="6.2" cy="6.2" r="4.7" stroke="#3B3731" stroke-width="1.2" />
                        <path d="M9.6 9.6L12.5 12.5" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" />
                    </svg>
                    <input type="search" placeholder="Search..." x-model="paymentSearch" aria-label="Search payments" />
                </label>
                <button type="button" class="admin-po-tool-btn">
                    Sort by
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="6" viewBox="0 0 10 6" fill="none" aria-hidden="true">
                        <path d="M1 1L5 5L9 1" stroke="#3B3731" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <button type="button" class="admin-po-tool-btn filter-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10" fill="none" aria-hidden="true">
                        <path d="M9.5 4.99974H3.48946M1.36789 4.99974H0.5M1.36789 4.99974C1.36789 4.70367 1.47963 4.41972 1.67852 4.21036C1.87741 4.001 2.14716 3.88339 2.42843 3.88339C2.70971 3.88339 2.97946 4.001 3.17835 4.21036C3.37724 4.41972 3.48897 4.70367 3.48897 4.99974C3.48897 5.29582 3.37724 5.57977 3.17835 5.78913C2.97946 5.99849 2.70971 6.1161 2.42843 6.1161C2.14716 6.1161 1.87741 5.99849 1.67852 5.78913C1.47963 5.57977 1.36789 5.29582 1.36789 4.99974ZM9.5 8.38313H6.70368M6.70368 8.38313C6.70368 8.67927 6.59167 8.96355 6.39274 9.17295C6.1938 9.38236 5.92399 9.5 5.64265 9.5C5.36138 9.5 5.09162 9.38187 4.89273 9.17251C4.69384 8.96316 4.58211 8.67921 4.58211 8.38313M6.70368 8.38313C6.70368 8.08698 6.59167 7.80323 6.39274 7.59382C6.1938 7.38441 5.92399 7.26677 5.64265 7.26677C5.36138 7.26677 5.09162 7.38439 4.89273 7.59375C4.69384 7.8031 4.58211 8.08705 4.58211 8.38313M4.58211 8.38313H0.5M9.5 1.61636H7.98946M5.86789 1.61636H0.5M5.86789 1.61636C5.86789 1.32028 5.97963 1.03633 6.17852 0.826974C6.37741 0.617616 6.64716 0.5 6.92843 0.5C7.0677 0.5 7.20561 0.528875 7.33428 0.584978C7.46295 0.64108 7.57987 0.72331 7.67835 0.826974C7.77683 0.930637 7.85495 1.0537 7.90824 1.18915C7.96154 1.32459 7.98897 1.46976 7.98897 1.61636C7.98897 1.76296 7.96154 1.90813 7.90824 2.04357C7.85495 2.17901 7.77683 2.30208 7.67835 2.40574C7.57987 2.50941 7.46295 2.59164 7.33428 2.64774C7.20561 2.70384 7.0677 2.73272 6.92843 2.73272C6.64716 2.73272 6.37741 2.6151 6.17852 2.40574C5.97963 2.19639 5.86789 1.91244 5.86789 1.61636Z" stroke="#3B3731" stroke-miterlimit="10" stroke-linecap="round" />
                    </svg>
                    Filter
                </button>
            </div>
        </div>

        <section class="admin-card admin-co-panel admin-co-pay-table-panel">
            <p class="admin-co-pay-selection-count" x-show="selectedIds.length > 0" x-cloak>
                <span x-text="selectedIds.length"></span>
                <span x-text="selectedIds.length === 1 ? 'TRANSACTION SELECTED' : 'TRANSACTIONS SELECTED'"></span>
            </p>
            <div class="admin-po-table-card admin-co-pay-table-card">
                <div class="admin-co-pay-table-wrap">
                    <table class="admin-po-table admin-co-pay-table">
                        <thead>
                            <tr>
                                <th class="admin-po-check-col">
                                    <input
                                        type="checkbox"
                                        class="admin-po-check"
                                        :checked="allSelected"
                                        @change="toggleAll()"
                                        aria-label="Select all payments" />
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Invoice ID
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Description
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Booking ID
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Date
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Amount
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Method
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                                <th>
                                    <span class="admin-co-ref-th">
                                        Status
                                        <svg xmlns="http://www.w3.org/2000/svg" width="8" height="5" viewBox="0 0 8 5" fill="none" aria-hidden="true">
                                            <path d="M1 1L4 4L7 1" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($groupedRows as $month => $monthRows)
                            @php
                            $monthStatuses = $monthRows->pluck('status')->unique()->values()->all();
                            $monthHaystacks = $monthRows->map(fn ($r) => implode(' ', [
                            $r['invoice_id'] ?? '',
                            $r['description'] ?? '',
                            $r['provider'] ?? '',
                            $r['booking_id'] ?? '',
                            $r['method'] ?? '',
                            $r['note'] ?? '',
                            ]))->values()->all();
                            @endphp
                            <tr
                                class="admin-co-pay-month-row"
                                x-show="monthVisible(@js($monthStatuses), @js($monthHaystacks))"
                                x-cloak>
                                <td colspan="8">
                                    <span class="admin-co-pay-month">{{ strtoupper($month) }}</span>
                                </td>
                            </tr>
                            @foreach ($monthRows as $row)
                            @php $rowPayload = $row; @endphp
                            <tr
                                class="admin-co-pay-row"
                                role="button"
                                tabindex="0"
                                :class="{ 'is-selected': isSelected(@js($row['id'])) }"
                                x-show="matchesPayment(
                                @js($row['status']),
                                @js($row['invoice_id']),
                                @js($row['description']),
                                @js($row['provider'] ?? ''),
                                @js($row['booking_id']),
                                @js($row['method']),
                                @js($row['note'] ?? '')
                            )"
                                @click="openPayment(@js($rowPayload))"
                                @keydown.enter.prevent="openPayment(@js($rowPayload))"
                                @keydown.space.prevent="openPayment(@js($rowPayload))"
                                x-cloak>
                                <td class="admin-po-check-col" @click.stop>
                                    <input
                                        type="checkbox"
                                        class="admin-po-check"
                                        :checked="isSelected(@js($row['id']))"
                                        @change="toggleOne(@js($row['id']))"
                                        aria-label="Select {{ $row['invoice_id'] }}" />
                                </td>
                                <td>
                                    <span class="admin-co-pay-invoice">{{ $row['invoice_id'] }}</span>
                                </td>
                                <td>
                                    <div class="admin-co-pay-desc">
                                        <span class="admin-co-pay-desc-title">{{ $row['description'] }}</span>
                                        <span class="admin-co-pay-desc-meta">
                                            @if (($row['provider_type'] ?? '') === 'groomer')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
                                                <path d="M5.25833 4.875L2.59722 2.988M10.375 1.2462L2.59722 6.762M3.15278 7.9242C3.15278 7.1226 2.53056 6.4722 1.76389 6.4722C0.997222 6.4722 0.375 7.122 0.375 7.9236C0.375 8.7252 0.997222 9.375 1.76389 9.375C2.53056 9.375 3.15278 8.7252 3.15278 7.923M10.375 8.5038L7.005 6.114M3.15278 1.827C3.15278 2.6292 2.53056 3.279 1.76389 3.279C0.997222 3.279 0.375 2.6286 0.375 1.8264C0.375 1.0242 0.997222 0.375 1.76389 0.375C2.53056 0.375 3.15278 1.0248 3.15278 1.827Z" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            @elseif (($row['provider_type'] ?? '') === 'space')
                                            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="9" viewBox="0 0 10 9" fill="none">
                                                <path d="M8.7396 8.07756V2.55606C8.7396 2.54281 8.7407 2.52983 8.74274 2.51712L7.24984 1.24408C6.93237 0.973758 6.71303 0.787442 6.52696 0.665974C6.34729 0.548723 6.22654 0.511213 6.11104 0.511213C5.99564 0.511213 5.87565 0.54886 5.69617 0.665974C5.51007 0.787458 5.29015 0.973606 4.97225 1.24408L3.4783 2.51712C3.48035 2.52988 3.48249 2.54276 3.48249 2.55606V8.07756C3.48226 8.21854 3.36227 8.33317 3.21429 8.33317C3.0664 8.33306 2.94632 8.21848 2.94609 8.07756V2.97043L2.66951 3.20706C2.55909 3.30115 2.38899 3.29133 2.29026 3.18609C2.19192 3.08092 2.20113 2.91967 2.31121 2.82565L4.61395 0.86367H4.615C4.92227 0.602235 5.1711 0.389111 5.39236 0.244623C5.62029 0.0958328 5.84658 1.49993e-07 6.11104 0C6.37548 0 6.60172 0.0958269 6.82973 0.244623C7.05113 0.389146 7.30099 0.602138 7.60814 0.86367L9.91088 2.82565C10.021 2.91968 10.0302 3.08092 9.93183 3.18609C9.8331 3.29133 9.663 3.30115 9.55258 3.20706L9.276 2.97043V8.07756C9.27577 8.21848 9.15569 8.33306 9.0078 8.33317C8.85982 8.33317 8.73983 8.21854 8.7396 8.07756Z" fill="#3B3731" />
                                                <path d="M1.21612 4.44359C1.21612 4.25079 1.16128 4.08536 1.08324 3.97368C1.00518 3.86207 0.914487 3.81515 0.833331 3.81515C0.75222 3.81522 0.661428 3.86215 0.58342 3.97368C0.50545 4.08536 0.450544 4.25089 0.450544 4.44359C0.450616 4.63636 0.505354 4.80189 0.58342 4.9135C0.661414 5.02497 0.752241 5.07102 0.833331 5.07109C0.914422 5.07109 1.00522 5.02492 1.08324 4.9135C1.16131 4.80189 1.21605 4.63636 1.21612 4.44359ZM1.66666 4.44359C1.66659 4.73079 1.58597 5.00023 1.44403 5.20319C1.30196 5.40632 1.08809 5.55421 0.833331 5.55421C0.578745 5.55414 0.365553 5.40615 0.223512 5.20319C0.0815533 5.00022 7.10594e-05 4.7308 0 4.44359C0 4.15624 0.0814911 3.8861 0.223512 3.68305C0.365553 3.48015 0.578796 3.3321 0.833331 3.33203C1.08806 3.33203 1.30196 3.47997 1.44403 3.68305C1.58605 3.8861 1.66666 4.15624 1.66666 4.44359Z" fill="#3B3731" />
                                                <path d="M0.55542 8.07202V5.25954C0.55542 5.11573 0.679785 4.99915 0.833197 4.99915C0.986609 4.99915 1.11097 5.11573 1.11097 5.25954V8.07202C1.11086 8.21574 0.986537 8.33241 0.833197 8.33241C0.679857 8.33241 0.555537 8.21574 0.55542 8.07202Z" fill="#3B3731" />
                                                <path d="M7.10524 6.20674C7.10524 5.97991 7.10425 5.8359 7.08982 5.73036C7.07645 5.63252 7.05597 5.60706 7.04356 5.59483C7.03118 5.58264 7.00548 5.5615 6.90582 5.5483C6.79848 5.5341 6.65141 5.53414 6.42062 5.53414H5.94776C5.71697 5.53414 5.5699 5.5341 5.46257 5.5483C5.3629 5.5615 5.33721 5.58264 5.32482 5.59483C5.31242 5.60706 5.29194 5.63252 5.27856 5.73036C5.26413 5.8359 5.26314 5.97991 5.26314 6.20674V7.81287H7.10524V6.20674ZM6.65808 3.61751C6.80319 3.61762 6.92102 3.73368 6.92124 3.87643C6.92124 4.01936 6.80332 4.13524 6.65808 4.13535H5.7103C5.56506 4.13524 5.44715 4.01936 5.44715 3.87643C5.44737 3.73368 5.5652 3.61762 5.7103 3.61751H6.65808ZM6.65808 2.21973L6.71051 2.22478C6.83064 2.24876 6.92124 2.35338 6.92124 2.47865C6.92124 2.60392 6.83064 2.70853 6.71051 2.73252L6.65808 2.73757H5.7103C5.56506 2.73746 5.44715 2.62158 5.44715 2.47865C5.44715 2.33572 5.56506 2.21984 5.7103 2.21973H6.65808ZM7.63156 7.81287H9.73681C9.88215 7.81287 9.99997 7.92879 9.99997 8.07179C9.99975 8.2146 9.88201 8.33071 9.73681 8.33071H0.263157C0.117956 8.33071 0.000221645 8.2146 0 8.07179C0 7.92879 0.117819 7.81287 0.263157 7.81287H4.73683V6.20674C4.73683 5.99466 4.73617 5.80959 4.75636 5.66158C4.77764 5.50597 4.82638 5.35304 4.9527 5.2287C5.0791 5.10433 5.23446 5.05645 5.39266 5.03551C5.54323 5.0156 5.73193 5.0163 5.94776 5.0163H6.42062C6.63645 5.0163 6.82516 5.0156 6.97572 5.03551C7.13392 5.05645 7.28928 5.10433 7.41568 5.2287C7.54201 5.35304 7.59075 5.50597 7.61202 5.66158C7.63222 5.80959 7.63156 5.99466 7.63156 6.20674V7.81287Z" fill="#3B3731" />
                                            </svg>
                                            @else
                                            <svg xmlns="http://www.w3.org/2000/svg" width="11" height="10" viewBox="0 0 11 10" fill="none">
                                                <path d="M5.25833 4.875L2.59722 2.988M10.375 1.2462L2.59722 6.762M3.15278 7.9242C3.15278 7.1226 2.53056 6.4722 1.76389 6.4722C0.997222 6.4722 0.375 7.122 0.375 7.9236C0.375 8.7252 0.997222 9.375 1.76389 9.375C2.53056 9.375 3.15278 8.7252 3.15278 7.923M10.375 8.5038L7.005 6.114M3.15278 1.827C3.15278 2.6292 2.53056 3.279 1.76389 3.279C0.997222 3.279 0.375 2.6286 0.375 1.8264C0.375 1.0242 0.997222 0.375 1.76389 0.375C2.53056 0.375 3.15278 1.0248 3.15278 1.827Z" stroke="#3B3731" stroke-width="0.75" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            @endif
                                            <span>{{ $row['provider'] }}</span>
                                        </span>
                                        @if (! empty($row['note']))
                                        <span class="admin-co-pay-desc-note">{{ $row['note'] }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $row['booking_id'] }}</td>
                                <td>{{ $row['date'] }}</td>
                                <td>
                                    <span class="admin-co-pay-amount is-{{ $row['amount_tone'] ?? 'default' }}">{{ $row['amount'] }}</span>
                                </td>
                                <td>{{ $row['method'] }}</td>
                                <td>
                                    <span class="admin-po-status is-{{ $row['status'] }}">
                                        {{ $row['status_label'] }}
                                    </span>
                                </td>
                            </tr>
                            @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="admin-po-table-footer admin-co-pay-footer">
                <p class="admin-table-count">SHOWING 1–{{ count($rows) }} OF {{ $filters['all'] }} PAYMENTS</p>
                <nav class="admin-po-pagination" aria-label="Payment pagination">
                    <button type="button" class="admin-po-page-arrow" aria-label="Previous page" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 32 32" fill="none">
                            <circle cx="16" cy="16" r="16" transform="matrix(-1 0 0 1 32 0)" fill="#F3F3F3" />
                            <path d="M18 21L12.9657 15.9657L17.9155 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button type="button" class="admin-po-page is-current">1</button>
                    <button type="button" class="admin-po-page">2</button>
                    <button type="button" class="admin-po-page">3</button>
                    <button type="button" class="admin-po-page">4</button>
                    <button type="button" class="admin-po-page">5</button>
                    <span class="admin-po-page-ellipsis" aria-hidden="true">…</span>
                    <button type="button" class="admin-po-page">100</button>
                    <button type="button" class="admin-po-page-arrow" aria-label="Next page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" viewBox="0 0 40 40" fill="none">
                            <g filter="url(#filter0_d_pay_page_next)">
                                <circle cx="20" cy="16" r="16" fill="white" />
                            </g>
                            <path d="M18 21L23.0343 15.9657L18.0845 11.016" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                            <defs>
                                <filter id="filter0_d_pay_page_next" x="0" y="0" width="40" height="40" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                                    <feOffset dy="4" />
                                    <feGaussianBlur stdDeviation="2" />
                                    <feComposite in2="hardAlpha" operator="out" />
                                    <feColorMatrix type="matrix" values="0 0 0 0 0.231373 0 0 0 0 0.215686 0 0 0 0 0.192157 0 0 0 0.1 0" />
                                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_pay_page_next" />
                                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_pay_page_next" result="shape" />
                                </filter>
                            </defs>
                        </svg>
                    </button>
                </nav>
            </div>
        </section>
    </div>

    <x-admin.customer.payment-detail :profile="$profile" />
</div>