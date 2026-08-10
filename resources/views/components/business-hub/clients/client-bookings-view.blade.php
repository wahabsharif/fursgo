@props(['bookings', 'isSpaceUser' => false])

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

<div class="client-bookings-view">
    <div class="client-bookings-table-shell">
        <table class="client-bookings-table">
            <thead>
                <tr>
                    <th>Booking ID</th>
                    <th>Date</th>
                    <th>{{ $isSpaceUser ? 'Space' : 'Pet' }}</th>
                    <th>Service Type</th>
                    <th>Rating</th>
                    <th>Earnings</th>
                    <th class="client-bookings-view-col">
                        <span class="client-bookings-view-col-inner">View</span>
                    </th>
                    <th class="client-bookings-invoice-col">
                        <span class="client-bookings-view-col-inner">Invoice</span>
                    </th>
                    <th class="client-bookings-more-col"></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    @php
                        $firstPet = $booking->pets->first();
                        $petName = $firstPet->name ?? 'N/A';
                        $petType = $firstPet->pet_type ?? null;
                        $rating = data_get($booking, 'rating');
                        $spaceLabel = $formatSpaceLabel($booking->visit_type ?? null);
                    @endphp
                    <tr wire:key="client-profile-booking-{{ $booking->id }}">
                        <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ optional($booking->date)->format('d/m/y') }}</td>
                        <td>
                            @if ($isSpaceUser)
                                <span class="client-bookings-space-label">{{ $spaceLabel }}</span>
                            @else
                                <div class="client-bookings-pet-cell">
                                    <span class="client-bookings-pet-name">{{ $petName }}</span>
                                    @if ($petType)
                                        <span class="client-bookings-pet-type">{{ $petType }}</span>
                                    @endif
                                </div>
                            @endif
                        </td>
                        <td class="client-bookings-service-type">{{ $booking->service }}</td>
                        <td>
                            <span class="client-bookings-rating">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                                    fill="none">
                                    <path
                                        d="M7.00521 0.75483C7.31833 -0.251612 8.68168 -0.251609 8.9948 0.754833L10.1663 4.52021C10.3063 4.97031 10.7079 5.27504 11.1611 5.27504H14.952C15.9653 5.27504 16.3866 6.6292 15.5668 7.25122L12.4999 9.57835C12.1333 9.85652 11.9799 10.3496 12.1199 10.7997L13.2914 14.5651C13.6045 15.5715 12.5015 16.4084 11.6818 15.7864L8.61482 13.4593C8.24821 13.1811 7.75179 13.1811 7.38518 13.4593L4.31824 15.7864C3.49848 16.4084 2.39551 15.5715 2.70863 14.5651L3.8801 10.7997C4.02013 10.3496 3.86673 9.85652 3.50012 9.57835L0.433177 7.25122C-0.38658 6.6292 0.0347219 5.27504 1.048 5.27504H4.83894C5.29209 5.27504 5.69371 4.97031 5.83374 4.52021L7.00521 0.75483Z"
                                        fill="#FFC97A" />
                                </svg>
                                <span>{{ is_numeric($rating) ? number_format((float) $rating, 1) : '-' }}</span>
                            </span>
                        </td>
                        <td>£{{ number_format((float) $booking->amount, 2) }}</td>
                        <td class="client-bookings-view-col">
                            <div class="client-bookings-view-col-inner">
                                <button type="button" class="client-bookings-icon-btn"
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
                            </div>
                        </td>
                        <td class="client-bookings-invoice-col">
                            <div class="client-bookings-view-col-inner">
                                <button type="button" class="client-bookings-icon-btn"
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
                            </div>
                        </td>
                        <td class="client-bookings-more-col">
                            <div class="client-bookings-view-col-inner">
                                <x-business-hub.common.more-action-btn :row-id="$booking->id" />
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="client-bookings-empty">No bookings found.</td>
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
    .client-bookings-table-shell {
        overflow-x: auto;
    }

    .client-bookings-table {
        width: 100%;
        border-collapse: collapse;
        table-layout: fixed;
    }

    .client-bookings-table th,
    .client-bookings-table td {
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

    .client-bookings-table th {
        font-weight: 600;
        color: #000;
    }

    .client-bookings-service-type {
        font-weight: 600 !important;
    }

    .client-bookings-pet-cell {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.15rem;
    }

    .client-bookings-pet-name {
        font-weight: 600;
        color: #3B3731;
    }

    .client-bookings-pet-type {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .client-bookings-space-label {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .client-bookings-rating {
        display: inline-flex;
        align-items: center;
        gap: 0.32rem;
    }

    .client-bookings-view-col {
        vertical-align: middle;
        border-left: 1px solid #E5E2DF;
        width: 8rem;
        text-align: center;
        padding: 1.1rem 0.35rem;
    }

    .client-bookings-invoice-col {
        vertical-align: middle;
        width: 8rem;
        text-align: center;
        padding: 1.1rem 0.35rem;
    }

    .client-bookings-more-col {
        vertical-align: middle;
        width: 4rem;
        text-align: center;
        padding: 1.1rem 0.25rem;
    }

    .client-bookings-view-col-inner {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .client-bookings-icon-btn {
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

    .client-bookings-empty {
        text-align: center !important;
        color: #8f8b86 !important;
        padding: 2rem 0 !important;
    }
</style>