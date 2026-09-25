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
            'garden shed', 'garden/shed' => 'Garden/Shed',
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
                    <th class="col-id">Booking ID</th>
                    <th class="col-date">Date</th>
                    <th class="col-pet">{{ $isSpaceUser ? 'Space' : 'Pet' }}</th>
                    <th class="col-service">Service</th>
                    <th class="col-amount">Amount</th>
                    <th class="col-status">Status</th>
                    <th class="col-action">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($payments as $payment)
                    @php
                        $booking = $payment->booking;
                        $pets = $booking?->pets;
                        $firstPet = $pets?->first() ?? $payment->pet;
                        $extraPetCount = max(($pets?->count() ?? ($firstPet ? 1 : 0)) - 1, 0);
                        $petName = $firstPet->name ?? 'N/A';
                        if ($extraPetCount > 0) {
                            $petName .= ' +' . $extraPetCount;
                        }
                        $petType = strtolower((string) ($firstPet->pet_type ?? ''));
                        $petIcon = str_contains($petType, 'cat') ? 'images/business-hub/icon-profile-pet-cat.svg' : 'images/business-hub/icon-profile-pet-dog.svg';
                        $petIconWidth = str_contains($petType, 'cat') ? 21 : 14;
                        $petIconHeight = str_contains($petType, 'cat') ? 17 : 19.6525;
                        $status = strtolower((string) $payment->status);
                        $statusLabel = ucfirst($status);
                        $spaceLabel = $formatSpaceLabel($booking?->visit_type ?? null);
                    @endphp
                    <tr wire:key="client-profile-payment-{{ $payment->id }}">
                        <td>FG-{{ str_pad((string) ($booking?->id ?? $payment->booking_id), 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ optional($payment->date)->format('d/m/y') }}</td>
                        <td>
                            @if ($isSpaceUser)
                                <span class="client-payments-space-label">{{ $spaceLabel }}</span>
                            @else
                                <span class="client-payments-pet">
                                    @if ($firstPet)
                                        <img src="{{ asset($petIcon) }}" width="{{ $petIconWidth }}"
                                            height="{{ $petIconHeight }}" alt="">
                                    @endif
                                    <span>{{ $petName }}</span>
                                </span>
                            @endif
                        </td>
                        <td>{{ $payment->service_type }}</td>
                        <td>£{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>
                            <span class="client-payments-status client-payments-status--{{ $status }}">
                                {{ $statusLabel }}
                            </span>
                        </td>
                        <td class="col-action">
                            @if ($booking)
                                <div class="client-payments-actions">
                                    <button type="button" class="client-payments-icon-btn"
                                        wire:click="openCompletedBookingModal({{ $booking->id }})"
                                        aria-label="View booking">
                                        <img src="{{ asset('images/business-hub/icon-booking-history-view.svg') }}"
                                            width="36" height="36" alt="">
                                    </button>
                                    <button type="button" class="client-payments-icon-btn"
                                        data-invoice-url="{{ route('business-hub.bookings.invoice-pdf', $booking) }}"
                                        onclick="window.downloadBookingInvoicePdf(this.dataset.invoiceUrl)"
                                        aria-label="Download invoice">
                                        <span class="client-payments-download">
                                            <img src="{{ asset('images/business-hub/icon-booking-download-circle.svg') }}"
                                                width="36" height="36" alt="">
                                            <img class="client-payments-download-glyph"
                                                src="{{ asset('images/business-hub/icon-booking-download-arrow.svg') }}"
                                                width="16" height="19" alt="">
                                        </span>
                                    </button>
                                </div>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="client-payments-empty">No payments found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@once
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
        width: calc(100% - 4px);
        margin: 2px;
        overflow-x: auto;
        background: #FDFDFD;
        border: 1px solid #F6F5F5;
        border-radius: 10px;
        box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
    }

    .client-payments-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
    }

    .client-payments-table th,
    .client-payments-table td {
        border: 0;
        text-align: left;
        vertical-align: middle;
        background: transparent;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .client-payments-table th {
        height: 50px;
        padding: 0 8px;
        color: #948F88;
        font-weight: 600;
        background: #F6F5F5;
        white-space: nowrap;
    }

    .client-payments-table th:first-child {
        border-top-left-radius: 10px;
        padding-left: 20px;
    }

    .client-payments-table th:last-child {
        border-top-right-radius: 10px;
        padding-right: 20px;
    }

    .client-payments-table td {
        height: 76px;
        padding: 8px;
    }

    .client-payments-table td:first-child {
        padding-left: 20px;
    }

    .client-payments-table td:last-child {
        padding-right: 20px;
    }

    .client-payments-table tbody tr {
        background-color: #FDFDFD;
    }

    .client-payments-table tbody tr:not(:last-child) {
        background-image: linear-gradient(#E2E2E2, #E2E2E2);
        background-repeat: no-repeat;
        background-size: calc(100% - 40px) 1px;
        background-position: center bottom;
    }

    .client-payments-table .col-id {
        width: 15%;
    }

    .client-payments-table .col-date {
        width: 14%;
    }

    .client-payments-table .col-pet {
        width: 16%;
    }

    .client-payments-table .col-service {
        width: 15%;
    }

    .client-payments-table .col-amount {
        width: 12%;
    }

    .client-payments-table .col-status {
        width: 14%;
    }

    .client-payments-table .col-action {
        width: 14%;
        text-align: center;
    }

    .client-payments-pet,
    .client-payments-actions {
        display: inline-flex;
        align-items: center;
    }

    .client-payments-pet {
        gap: 10px;
    }

    .client-payments-space-label {
        color: #3B3731;
        font-weight: 400;
    }

    .client-payments-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        height: 32px;
        padding: 0 10px;
        border-radius: 100px;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 500;
        line-height: normal;
        white-space: nowrap;
    }

    .client-payments-status--paid {
        background: rgba(186, 207, 142, 0.1);
        color: #AFCD6F;
    }

    .client-payments-status--failed {
        background: rgba(251, 204, 196, 0.2);
        color: #FF6E6E;
    }

    .client-payments-status--refunded {
        background: rgba(255, 201, 122, 0.1);
        color: #F9C45C;
    }

    .client-payments-actions {
        gap: 10px;
    }

    .client-payments-icon-btn {
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

    .client-payments-download {
        position: relative;
        display: block;
        width: 36px;
        height: 36px;
    }

    .client-payments-download-glyph {
        position: absolute;
        top: 8.5px;
        left: 10px;
    }

    .client-profile-wrapper .client-payments-view .client-payments-pet img[width="14"] {
        width: 14px;
        height: 19.6525px;
        max-width: 14px;
        display: block;
    }

    .client-profile-wrapper .client-payments-view .client-payments-pet img[width="21"] {
        width: 21px;
        height: 17px;
        max-width: 21px;
        display: block;
    }

    .client-profile-wrapper .client-payments-view .client-payments-icon-btn>img,
    .client-profile-wrapper .client-payments-view .client-payments-download>img:not(.client-payments-download-glyph) {
        width: 36px;
        height: 36px;
        max-width: 36px;
        display: block;
    }

    .client-profile-wrapper .client-payments-view .client-payments-download>.client-payments-download-glyph {
        width: 16px;
        height: 19px;
        max-width: 16px;
        display: block;
    }

    .client-payments-empty {
        height: auto !important;
        text-align: center !important;
        color: #9D9B98 !important;
        padding: 2rem 0 !important;
    }
</style>
