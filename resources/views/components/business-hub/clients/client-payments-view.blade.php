@props(['payments', 'isSpaceUser' => false])

@php
    $formatSpaceLabel = function (?string $visitType): string {
        $raw = trim((string) $visitType);

        if ($raw === '') {
            return 'N/A';
        }

        if (str_contains($raw, '/') || str_contains($raw, ' ')) {
            return $raw;
        }

        $normalized = str_replace('_', ' ', strtolower($raw));

        return match ($normalized) {
            'garden shed', 'garden/shed' => 'Garden / Shed',
            'salon', 'salon visit' => 'Salon',
            'home', 'home visit' => 'Home Visit',
            default => ucwords($normalized),
        };
    };
@endphp

<div class="client-payments-view">
    <div class="client-payments-table-shell">
        <table class="client-payments-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    @if ($isSpaceUser)
                        <th>Service Type</th>
                        <th>Space</th>
                        <th>Date</th>
                    @else
                        <th>Date</th>
                        <th>Pet</th>
                        <th>Service Type</th>
                    @endif
                    <th>Amount</th>
                    <th>Status</th>
                    <th class="client-payments-view-col">
                        <span class="client-payments-view-col-inner">View Booking</span>
                    </th>
                    <th class="client-payments-invoice-col">
                        <span class="client-payments-view-col-inner">Invoice</span>
                    </th>
                    <th class="client-payments-more-col"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    @php
                        $booking = $payment->booking;
                        $pet = $payment->pet;
                        $petName = $pet->name ?? 'N/A';
                        $petType = $pet->pet_type ?? null;
                        $status = strtolower((string) $payment->status);
                        $statusLabel = ucfirst($status);
                        $spaceLabel = $formatSpaceLabel($booking?->visit_type ?? null);
                    @endphp
                    <tr wire:key="client-profile-payment-{{ $payment->id }}">
                        <td>FG-{{ str_pad((string) ($booking?->id ?? $payment->booking_id), 5, '0', STR_PAD_LEFT) }}
                        </td>
                        @if ($isSpaceUser)
                            <td class="client-payments-service-type">{{ $payment->service_type }}</td>
                            <td><span class="client-payments-space-label">{{ $spaceLabel }}</span></td>
                            <td>{{ optional($payment->date)->format('d/m/y') }}</td>
                        @else
                            <td>{{ optional($payment->date)->format('d/m/y') }}</td>
                            <td>
                                <div class="client-payments-pet-cell">
                                    <span class="client-payments-pet-name">{{ $petName }}</span>
                                    @if ($petType)
                                        <span class="client-payments-pet-type">{{ $petType }}</span>
                                    @endif
                                </div>
                            </td>
                            <td class="client-payments-service-type">{{ $payment->service_type }}</td>
                        @endif
                        <td>£{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>
                            <span class="client-payments-status client-payments-status--{{ $status }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="client-payments-view-col">
                            <div class="client-payments-view-col-inner">
                                @if ($booking)
                                    <button type="button" class="client-payments-icon-btn"
                                        wire:click="openCompletedBookingModal({{ $booking->id }})" aria-label="View booking">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="19" height="13" viewBox="0 0 19 13"
                                            fill="none">
                                            <path
                                                d="M9.49609 12C11.4291 12 12.9961 10.433 12.9961 8.5C12.9961 6.567 11.4291 5 9.49609 5C7.5631 5 5.99609 6.567 5.99609 8.5C5.99609 10.433 7.5631 12 9.49609 12Z"
                                                stroke="black" />
                                            <path
                                                d="M18.4961 8.5C18.4961 8.5 17.4961 0.5 9.49609 0.5C1.49609 0.5 0.496094 8.5 0.496094 8.5"
                                                stroke="black" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="client-payments-invoice-col">
                            <div class="client-payments-view-col-inner">
                                @if ($booking)
                                    <button type="button" class="client-payments-icon-btn"
                                        data-invoice-url="{{ route('business-hub.bookings.invoice-pdf', $booking) }}"
                                        onclick="window.downloadBookingInvoicePdf(this.dataset.invoiceUrl)"
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
                                @endif
                            </div>
                        </td>
                        <td class="client-payments-more-col">
                            <div class="client-payments-view-col-inner">
                                <x-business-hub.common.more-action-btn :row-id="$payment->id" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="client-payments-empty">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@once
    <script>
        if (!window.downloadBookingInvoicePdf) {
            window.downloadBookingInvoicePdf = async function (invoiceUrl) {
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
                    const ct = (res.headers.get('Content-Type') || '').toLowerCase();
                    if (!res.ok || (!ct.includes('application/pdf') && !ct.includes('octet-stream'))) {
                        throw new Error('Invoice download failed');
                    }
                    let filename = 'Fursgo-Invoice.pdf';
                    const cd = res.headers.get('Content-Disposition');
                    if (cd) {
                        const utf = cd.match(/filename\*=(?:UTF-8'')?([^;\n]+)/i);
                        const quoted = cd.match(/filename="([^"]+)"/i);
                        const plain = cd.match(/filename=([^;\s]+)/i);
                        if (utf && utf[1]) {
                            try {
                                filename = decodeURIComponent(utf[1].trim().replace(/^"+|"+$/g, ''));
                            } catch (e) {
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
                    const a = document.createElement('a');
                    a.href = objectUrl;
                    a.download = filename;
                    a.rel = 'noopener';
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(objectUrl);
                } catch (e) {
                    console.error(e);
                    window.alert('Could not download the invoice. Please try again.');
                }
            };
        }
    </script>
@endonce

<style>
    .client-payments-table-shell {
        overflow-x: auto;
    }

    .client-payments-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .client-payments-table th,
    .client-payments-table td {
        border-bottom: 1px solid #dcdcdc;
        text-align: left;
        padding: 1.1rem 0.65rem;
        vertical-align: middle;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .client-payments-table th {
        font-weight: 600;
        color: #000;
    }

    .client-payments-service-type {
        font-weight: 600 !important;
    }

    .client-payments-pet-cell {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.15rem;
    }

    .client-payments-pet-name {
        font-weight: 600;
        color: #3B3731;
    }

    .client-payments-pet-type {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .client-payments-space-label {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .client-payments-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.35rem 0.85rem;
        border-radius: 999px;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        white-space: nowrap;
    }

    .client-payments-status--paid {
        background: rgba(186, 207, 142, 0.10);
        color: #AFCD6F;
    }

    .client-payments-status--failed {
        background: #FFE2E2;
        color: #FF6E6E;
    }

    .client-payments-status--refunded {
        background: #FFF4E4;
        color: #FFAE37;
    }

    .client-payments-view-col {
        vertical-align: middle;
        border-left: 1px solid #E5E2DF;
        width: 8rem;
        text-align: center;
        padding: 1.1rem 0.35rem;
    }

    .client-payments-invoice-col {
        vertical-align: middle;
        width: 8rem;
        text-align: center;
        padding: 1.1rem 0.35rem;
    }

    .client-payments-more-col {
        vertical-align: middle;
        width: 4rem;
        text-align: center;
        padding: 1.1rem 0.25rem;
    }

    .client-payments-view-col-inner {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .client-payments-icon-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 0;
        padding: 0;
        margin: 0 auto;
        cursor: pointer;
        color: inherit;
    }

    .client-payments-empty {
        text-align: center !important;
        color: #8f8b86 !important;
        padding: 2rem 0 !important;
    }
</style>