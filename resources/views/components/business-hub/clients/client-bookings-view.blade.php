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
                    <th class="col-id">Booking ID</th>
                    <th class="col-date">Date</th>
                    <th class="col-pet">{{ $isSpaceUser ? 'Space' : 'Pet' }}</th>
                    <th class="col-service">Service</th>
                    <th class="col-rating">Rating</th>
                    <th class="col-earnings">Earnings</th>
                    <th class="col-action">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    @php
                        $pets = $booking->pets;
                        $firstPet = $pets->first();
                        $extraPetCount = max($pets->count() - 1, 0);
                        $petName = $firstPet->name ?? '—';
                        if ($extraPetCount > 0) {
                            $petName .= ' +' . $extraPetCount;
                        }
                        $petType = strtolower((string) ($firstPet->pet_type ?? ''));
                        $petIcon = str_contains($petType, 'cat') ? 'images/business-hub/icon-profile-pet-cat.svg' : 'images/business-hub/icon-profile-pet-dog.svg';
                        $petIconWidth = str_contains($petType, 'cat') ? 21 : 14;
                        $petIconHeight = str_contains($petType, 'cat') ? 17 : 19.6525;
                        $rating = data_get($booking, 'rating');
                        $hasRating = is_numeric($rating) && (float) $rating > 0;
                        $spaceLabel = $formatSpaceLabel($booking->visit_type ?? null);
                    @endphp
                    <tr wire:key="client-profile-booking-{{ $booking->id }}">
                        <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td>{{ optional($booking->date)->format('d/m/y') }}</td>
                        <td>
                            @if ($isSpaceUser)
                                <span class="client-bookings-space-label">{{ $spaceLabel }}</span>
                            @else
                                <span class="client-bookings-pet">
                                    @if ($firstPet)
                                        <img src="{{ asset($petIcon) }}" width="{{ $petIconWidth }}"
                                            height="{{ $petIconHeight }}" alt="">
                                    @endif
                                    <span>{{ $petName }}</span>
                                </span>
                            @endif
                        </td>
                        <td>{{ $booking->service }}</td>
                        <td>
                            @if ($hasRating)
                                <span class="client-bookings-rating">
                                    <img src="{{ asset('images/business-hub/icon-booking-star.svg') }}" width="16"
                                        height="16" alt="">
                                    <span>{{ number_format((float) $rating, 1) }}</span>
                                </span>
                            @else
                                <span class="client-bookings-unrated">Not rated</span>
                            @endif
                        </td>
                        <td>£{{ number_format((float) $booking->amount, 2) }}</td>
                        <td class="col-action">
                            <div class="client-bookings-actions">
                                <button type="button" class="client-bookings-icon-btn"
                                    wire:click="openCompletedBookingModal({{ $booking->id }})" aria-label="View booking">
                                    <img src="{{ asset('images/business-hub/icon-booking-history-view.svg') }}"
                                        width="36" height="36" alt="">
                                </button>
                                <button type="button" class="client-bookings-icon-btn"
                                    data-invoice-url="{{ route('business-hub.bookings.invoice-pdf', $booking) }}"
                                    onclick="window.downloadBookingInvoicePdf(this.dataset.invoiceUrl)"
                                    aria-label="Download invoice">
                                    <span class="client-bookings-download">
                                        <img src="{{ asset('images/business-hub/icon-booking-download-circle.svg') }}"
                                            width="36" height="36" alt="">
                                        <img class="client-bookings-download-glyph"
                                            src="{{ asset('images/business-hub/icon-booking-download-arrow.svg') }}"
                                            width="16" height="19" alt="">
                                    </span>
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="client-bookings-empty">No bookings found.</td>
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
    .client-bookings-table-shell {
        width: calc(100% - 4px);
        margin: 2px;
        overflow-x: auto;
        background: #FDFDFD;
        border: 1px solid #F6F5F5;
        border-radius: 10px;
        box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
    }

    .client-bookings-table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        table-layout: fixed;
    }

    .client-bookings-table th,
    .client-bookings-table td {
        border: 0;
        text-align: left;
        vertical-align: middle;
        background: transparent;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .client-bookings-table th {
        height: 50px;
        padding: 0 8px;
        color: #948F88;
        font-weight: 600;
        background: #F6F5F5;
        white-space: nowrap;
    }

    .client-bookings-table th:first-child {
        border-top-left-radius: 10px;
        padding-left: 20px;
    }

    .client-bookings-table th:last-child {
        border-top-right-radius: 10px;
        padding-right: 20px;
    }

    .client-bookings-table td {
        height: 76px;
        padding: 8px;
    }

    .client-bookings-table td:first-child {
        padding-left: 20px;
    }

    .client-bookings-table td:last-child {
        padding-right: 20px;
    }

    .client-bookings-table tbody tr {
        background-color: #FDFDFD;
    }

    .client-bookings-table tbody tr:not(:last-child) {
        background-image: linear-gradient(#E2E2E2, #E2E2E2);
        background-repeat: no-repeat;
        background-size: calc(100% - 40px) 1px;
        background-position: center bottom;
    }

    .client-bookings-table .col-id {
        width: 15%;
    }

    .client-bookings-table .col-date {
        width: 14%;
    }

    .client-bookings-table .col-pet {
        width: 15%;
    }

    .client-bookings-table .col-service {
        width: 16%;
    }

    .client-bookings-table .col-rating {
        width: 14%;
    }

    .client-bookings-table .col-earnings {
        width: 12%;
    }

    .client-bookings-table .col-action {
        width: 14%;
        text-align: center;
    }

    .client-bookings-pet,
    .client-bookings-rating,
    .client-bookings-actions {
        display: inline-flex;
        align-items: center;
    }

    .client-bookings-pet {
        gap: 10px;
    }

    .client-bookings-rating {
        gap: 5px;
        font-weight: 500;
    }

    .client-bookings-unrated {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: italic;
        font-weight: 500;
    }

    .client-bookings-space-label {
        color: #3B3731;
        font-weight: 400;
    }

    .client-bookings-actions {
        gap: 10px;
    }

    .client-bookings-icon-btn {
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

    .client-bookings-download {
        position: relative;
        display: block;
        width: 36px;
        height: 36px;
    }

    .client-bookings-download-glyph {
        position: absolute;
        top: 8.5px;
        left: 10px;
    }

    .client-profile-wrapper .client-bookings-view .client-bookings-pet img[width="14"] {
        width: 14px;
        height: 19.6525px;
        max-width: 14px;
    }

    .client-profile-wrapper .client-bookings-view .client-bookings-pet img[width="21"] {
        width: 21px;
        height: 17px;
        max-width: 21px;
    }

    .client-profile-wrapper .client-bookings-view .client-bookings-rating img,
    .client-profile-wrapper .client-bookings-view .client-bookings-icon-btn>img,
    .client-profile-wrapper .client-bookings-view .client-bookings-download>img:not(.client-bookings-download-glyph) {
        width: 36px;
        height: 36px;
        max-width: 36px;
        display: block;
    }

    .client-profile-wrapper .client-bookings-view .client-bookings-rating img {
        width: 16px;
        height: 16px;
        max-width: 16px;
    }

    .client-profile-wrapper .client-bookings-view .client-bookings-download>.client-bookings-download-glyph {
        width: 16px;
        height: 19px;
        max-width: 16px;
        display: block;
    }

    .client-bookings-empty {
        height: auto !important;
        text-align: center !important;
        color: #9D9B98 !important;
        padding: 2rem 0 !important;
    }
</style>
