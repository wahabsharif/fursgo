@props([
    'booking' => null,
    'closeMethod' => 'closeCompletedBookingModal',
    'loadingEvent' => null,
    'variant' => null,
])

@if ($booking)
    @php
        $spacerUser = auth('groomer_spacer')->user() ?? auth()->user();
        $isSpaceUser = strtolower((string) ($spacerUser?->user_type ?? '')) === 'space';
        $invoiceIdLabel = 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
        $issuedDateLabel = $booking->date?->format('j M Y') ?? now()->format('j M Y');
        $bookingDateLabel = $booking->date?->format('D, j M Y') ?? 'N/A';
        $billedToName = $booking->petOwner?->name ?? 'N/A';
        $firstPet = $booking->pets->first();
        $petName = $firstPet?->name ?? 'N/A';
        $petType = trim((string) ($firstPet?->pet_type ?? ''));
        $petLine = trim($petName . ($petType !== '' ? ' · ' . $petType : ''));

        $resolvePhotoUrl = function (?string $raw): ?string {
            $raw = trim((string) $raw);
            if ($raw === '') {
                return null;
            }
            $isAbsolute = str_starts_with($raw, 'http://') || str_starts_with($raw, 'https://') || str_starts_with($raw, 'data:') || str_starts_with($raw, '/');

            return $isAbsolute ? $raw : asset('storage/' . ltrim($raw, '/'));
        };

        $ownerPhotoUrl = $resolvePhotoUrl($booking->petOwner?->profile_image ?? null);
        $petPhotoUrl = $resolvePhotoUrl($firstPet?->photo ?? null);
        $ownerInitial = strtoupper(substr((string) $billedToName, 0, 1));
        $petInitial = strtoupper(substr((string) $petName, 0, 1));

        $issuer = Auth::guard('groomer_spacer')->user();
        $issuerName = trim((string) ($issuer?->full_name ?? ''));
        if ($issuerName === '') {
            $issuerName = trim((string) data_get($issuer?->business_details ?? [], 'business_name', '')) ?: 'Business';
        }
        $issuerParts = preg_split('/\s+/', $issuerName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $issuerShortName = count($issuerParts) >= 2 ? $issuerParts[0] . ' ' . strtoupper(substr((string) end($issuerParts), 0, 1)) . '.' : $issuerName;
        $timeRaw = trim((string) ($booking->time ?? ''));
        $visitRaw = str_replace('_', ' ', strtolower((string) ($booking->visit_type ?? '')));
        $spaceVisitLabel = match (true) {
            $visitRaw === '' => 'Garden / Shed',
            str_contains((string) ($booking->visit_type ?? ''), '/') => str_replace('Garden/Shed', 'Garden / Shed', (string) $booking->visit_type),
            $visitRaw === 'garden shed' || $visitRaw === 'garden/shed' => 'Garden / Shed',
            $visitRaw === 'home' || $visitRaw === 'home visit' => 'Home Visit',
            $visitRaw === 'salon' || $visitRaw === 'salon visit' => 'Salon',
            $visitRaw === 'private room' => 'Private room',
            $visitRaw === 'mobile station' => 'Mobile Station',
            $visitRaw === 'other' => 'Other',
            default => ucwords($visitRaw),
        };
        $serviceLower = strtolower(trim((string) ($booking->service ?? '')));
        $durationLabel = match (true) {
            (bool) preg_match('/full[\s_-]*day|fullday/', $serviceLower) => 'Full-Day',
            (bool) preg_match('/half[\s_-]*day/', $serviceLower) => 'Half-Day',
            str_contains($serviceLower, 'hour') => 'Hourly',
            default => null,
        };
        if ($durationLabel === null && str_contains($timeRaw, '-')) {
            $rangeParts = preg_split('/\s*-\s*/', $timeRaw, 2);
            preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[0] ?? ''), $startMatch);
            preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[1] ?? ''), $endMatch);
            if (!empty($startMatch[1]) && !empty($endMatch[1])) {
                $diff = ((int) $endMatch[1]) * 60 + (int) $endMatch[2] - (((int) $startMatch[1]) * 60 + (int) $startMatch[2]);
                if (abs($diff) !== $diff) {
                    $diff += 24 * 60;
                }
                $durationLabel = $diff > 419 ? 'Full-Day' : ($diff > 179 ? 'Half-Day' : 'Hourly');
            }
        }
        $durationLabel = $durationLabel ?: 'Half-Day';
        $serviceTypeLabel = $isSpaceUser
            ? $durationLabel
            : match (true) {
                str_contains($visitRaw, 'home') => 'Home visit',
                str_contains($visitRaw, 'salon') => 'Salon visit',
                $visitRaw !== '' => ucfirst($visitRaw),
                default => 'Home visit',
            };
        $spaceLocation = trim((string) data_get($issuer?->spacer_business_profile ?? [], 'legacy.location', ''));
        if ($spaceLocation === '') {
            $spaceLocation = trim((string) data_get($issuer?->freelance_details ?? [], 'service_home_address_line1', ''));
        }
        if ($spaceLocation === '') {
            $spaceLocation = trim((string) data_get($issuer?->freelance_details ?? [], 'service_home_address_line2', ''));
        }
        if ($spaceLocation === '' && $issuer) {
            $area = \App\Models\ServiceArea::query()->where('groomer_spacer_id', $issuer->id)->first();
            $spaceLocation = trim((string) ($area?->name ?? ''));
            if ($spaceLocation === '') {
                $spaceLocation = trim((string) ($area?->address ?? ''));
            }
        }
        $fallbackAddress = trim((string) ($firstPet?->address ?? ($booking->petOwner?->address ?? '')));
        $locationLabel = match (true) {
            $isSpaceUser => $spaceLocation !== '' ? $spaceLocation : ($fallbackAddress !== '' ? $fallbackAddress : 'N/A'),
            str_contains($visitRaw, 'home') => 'At your home',
            str_contains($visitRaw, 'salon') => 'Salon',
            default => $fallbackAddress !== '' ? $fallbackAddress : 'N/A',
        };

        $timeLabel = $timeRaw !== '' ? $timeRaw : 'N/A';
        if (str_contains($timeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $timeRaw, 2);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($parts[0] ?? ''), $mStart);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($parts[1] ?? ''), $mEnd);
            if (!empty($mStart[1]) && !empty($mEnd[1])) {
                $timeLabel = $mStart[1] . ' – ' . $mEnd[1];
            }
        }

        $serviceAmount = (float) $booking->amount;
        $extraAddOns = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])
            ->map(
                fn($item) => [
                    'label' => trim((string) data_get($item, 'label', '')),
                    'amount' => (float) data_get($item, 'amount', 0),
                ],
            )
            ->filter(fn($item) => $item['label'] !== '')
            ->values();
        $extrasAmount = (float) $extraAddOns->sum('amount');
        $promoDiscount = (float) ($booking->discount ?? 0);
        $subtotalAmount = $serviceAmount + $extrasAmount;
        $totalPaidAmount = $subtotalAmount - $promoDiscount;
        $serviceDurationLine = match ($durationLabel) {
            'Full-Day' => 'Full-day',
            'Half-Day' => 'Half-day',
            default => $durationLabel,
        };
        $serviceLineLabel = $isSpaceUser ? $spaceVisitLabel . ' — ' . $serviceDurationLine : ($booking->service ?: 'Service');
        $subtotalLabel = $isSpaceUser ? 'Space subtotal' : 'Groomer subtotal';
        $historyServiceLabel = trim((string) ($booking->service ?: 'Service'));
        if (!$isSpaceUser && $petName !== '' && $petName !== 'N/A') {
            $historyServiceLabel .= ' (' . $petName . ')';
        }
        $spaceTimeRange = '';
        if (str_contains($timeRaw, '-')) {
            $spaceRangeParts = preg_split('/\s*-\s*/', $timeRaw, 2);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($spaceRangeParts[0] ?? ''), $spaceStartMatch);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($spaceRangeParts[1] ?? ''), $spaceEndMatch);
            if (!empty($spaceStartMatch[1]) && !empty($spaceEndMatch[1])) {
                $spaceTimeRange = $spaceStartMatch[1] . '-' . $spaceEndMatch[1];
            }
        } elseif ($timeRaw !== '') {
            $spaceTimeRange = $timeRaw;
        }
        $historySpaceDetail = $serviceDurationLine;
        if ($spaceTimeRange !== '') {
            $historySpaceDetail .= ' - ' . $spaceTimeRange;
        }
        $historySpaceLabel = str_replace(' / ', '/', $spaceVisitLabel) . ' (' . $historySpaceDetail . ')';
        $historyDateLabel = $booking->date?->format('d/m/Y') ?? '—';
        $historyPromoLabel = $promoDiscount > 0 ? '- £' . number_format($promoDiscount, 2) : '£' . number_format($promoDiscount, 2);
    @endphp
    @if ($variant === 'history')
        @teleport('body')
            <div class="client-history-modal-overlay" wire:keydown.escape="{{ $closeMethod }}">
                <div class="client-history-modal" role="dialog" aria-modal="true" aria-labelledby="client-history-modal-title">
                    <div class="client-history-modal__head{{ $isSpaceUser ? ' is-space' : '' }}">
                        <h2 class="client-history-modal__title" id="client-history-modal-title">Completed <span>Booking</span></h2>
                        <button type="button" class="client-history-modal__close" wire:click="{{ $closeMethod }}" aria-label="Close modal">
                            <img src="{{ asset('images/business-hub/icon-booking-modal-close.svg') }}" width="14.5" height="14.5" alt="">
                        </button>
                    </div>

                    <div class="client-history-modal__body">
                        <div class="client-history-modal__id">
                            <p>Booking ID: {{ $invoiceIdLabel }}</p>
                            <div class="client-history-modal__id-end">
                                <span>{{ $historyDateLabel }}</span>
                                <button type="button" class="client-history-modal__download"
                                    data-invoice-url="{{ route('business-hub.bookings.invoice-pdf', $booking) }}"
                                    onclick="window.downloadBookingInvoicePdf?.(this.dataset.invoiceUrl)"
                                    aria-label="Download invoice">
                                    <img src="{{ asset('images/business-hub/icon-booking-download-circle.svg') }}" width="36" height="36" alt="">
                                    <img class="client-history-modal__download-glyph"
                                        src="{{ asset('images/business-hub/icon-booking-download-arrow.svg') }}" width="16" height="19" alt="">
                                </button>
                            </div>
                        </div>

                        <div class="client-history-modal__person">
                            <span class="client-history-modal__avatar">
                                @if ($ownerPhotoUrl)
                                    <img src="{{ $ownerPhotoUrl }}" alt="" width="44" height="44">
                                @else
                                    <span>{{ $ownerInitial }}</span>
                                @endif
                            </span>
                            <span>
                                <span class="client-history-modal__name">{{ $billedToName }}</span>
                                @unless ($isSpaceUser)
                                    <span class="client-history-modal__pet">{{ $petName }}@if ($petType !== '')
                                            <span>{{ $petType }}</span>
                                        @endif
                                    </span>
                                @endunless
                            </span>
                        </div>

                        <div class="client-history-modal__section">
                            <p class="client-history-modal__section-title">{{ $isSpaceUser ? 'Space' : 'Service' }}</p>
                            <div class="client-history-modal__line">
                                <span>{{ $isSpaceUser ? $historySpaceLabel : $historyServiceLabel }}</span>
                                <span>£{{ number_format($serviceAmount, 2) }}</span>
                            </div>
                        </div>

                        <div class="client-history-modal__section">
                            <p class="client-history-modal__section-title">Extras &amp; Add-ons</p>
                            @forelse ($extraAddOns as $addon)
                                <div class="client-history-modal__line">
                                    <span>{{ $addon['label'] }}</span>
                                    <span>£{{ number_format((float) $addon['amount'], 2) }}</span>
                                </div>
                            @empty
                                <div class="client-history-modal__line">
                                    <span>No add-ons</span>
                                    <span>£0.00</span>
                                </div>
                            @endforelse
                        </div>

                        <div class="client-history-modal__summary">
                            <div class="client-history-modal__line">
                                <span>Service:</span>
                                <span>£{{ number_format($serviceAmount, 2) }}</span>
                            </div>
                            <div class="client-history-modal__line">
                                <span>Extras &amp; Add-ons:</span>
                                <span>£{{ number_format($extrasAmount, 2) }}</span>
                            </div>
                            <div class="client-history-modal__line">
                                <span>Promo discount:</span>
                                <span>{{ $historyPromoLabel }}</span>
                            </div>
                        </div>

                        <div class="client-history-modal__total">
                            <span>Total</span>
                            <span>£{{ number_format($totalPaidAmount, 2) }}</span>
                        </div>
                    </div>
                </div>
            </div>
        @endteleport
    @else
        @teleport('body')
            <div class="invoice-preview-overlay{{ $isSpaceUser ? ' is-space' : '' }}" wire:keydown.escape="{{ $closeMethod }}">
                <div class="invoice-preview-card{{ $isSpaceUser ? ' is-space' : '' }}" role="dialog" aria-modal="true" aria-labelledby="completed-booking-modal-title">
                    <div class="invoice-preview-head">
                        <img src="{{ asset('images/business-hub/icon-invoice-wordmark.svg') }}" alt="fursgo" width="73" height="20"
                            class="invoice-preview-wordmark">
                        <button type="button" class="invoice-preview-close" @if ($loadingEvent) @click="window.dispatchEvent(new CustomEvent(@js($loadingEvent)))" @endif
                            wire:click="{{ $closeMethod }}" aria-label="Close modal">
                            <img src="{{ asset('images/business-hub/icon-invoice-close.svg') }}" alt="" width="20" height="20">
                        </button>
                    </div>

                    <div class="invoice-preview-meta">
                        <p class="invoice-preview-id">{{ $invoiceIdLabel }} · Issued {{ $issuedDateLabel }}</p>
                        <p class="invoice-preview-paid">Paid</p>
                    </div>

                    <div class="invoice-preview-bill">
                        <div>
                            <p class="invoice-preview-kicker">Billed to</p>
                            <p class="invoice-preview-value">{{ $billedToName }}</p>
                        </div>
                        <div class="invoice-preview-bill-date">
                            <p class="invoice-preview-kicker">Booking date</p>
                            <p class="invoice-preview-value">{{ $bookingDateLabel }}</p>
                        </div>
                    </div>

                    <div class="invoice-preview-people">
                        <div class="invoice-preview-person">
                            <span class="invoice-preview-avatar">
                                @if ($isSpaceUser)
                                    <svg class="invoice-preview-avatar-ring" width="36" height="36" viewBox="0 0 36 36" fill="none"
                                        xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                        <circle cx="18" cy="18" r="17.5" fill="white" stroke="#FFA899" />
                                    </svg>
                                @else
                                    <img src="{{ asset('images/business-hub/icon-invoice-avatar-ring.svg') }}" alt="" width="36"
                                        height="36" class="invoice-preview-avatar-ring">
                                @endif
                                @if ($ownerPhotoUrl)
                                    <img src="{{ $ownerPhotoUrl }}" alt="" width="32" height="32" class="invoice-preview-avatar-photo">
                                @else
                                    <span class="invoice-preview-avatar-fallback">{{ $ownerInitial }}</span>
                                @endif
                            </span>
                            <span class="invoice-preview-person-name">{{ $billedToName }}</span>
                        </div>
                        <div class="invoice-preview-people-end">
                            @unless ($isSpaceUser)
                                <span class="invoice-preview-pet-chip">
                                    @if ($petPhotoUrl)
                                        <img src="{{ $petPhotoUrl }}" alt="" width="24" height="24">
                                    @else
                                        <span class="invoice-preview-pet-fallback">{{ $petInitial }}</span>
                                    @endif
                                    <span>{{ $petName }}</span>
                                </span>
                            @endunless
                            <span class="invoice-preview-next" aria-hidden="true">
                                <img src="{{ asset('images/business-hub/icon-invoice-next.svg') }}" alt="" width="36" height="36">
                            </span>
                        </div>
                    </div>

                    <div class="invoice-preview-details">
                        <div class="invoice-preview-detail-row">
                            <span>{{ $isSpaceUser ? 'Space Host' : 'Groomer' }}</span>
                            <span>{{ $issuerShortName }}</span>
                        </div>
                        <div class="invoice-preview-detail-row">
                            <span>Service type</span>
                            <span>{{ $serviceTypeLabel }}</span>
                        </div>
                        <div class="invoice-preview-detail-row">
                            <span>Time</span>
                            <span>{{ $timeLabel }}</span>
                        </div>
                        <div class="invoice-preview-detail-row">
                            <span>Location</span>
                            <span>{{ $locationLabel }}</span>
                        </div>
                        @unless ($isSpaceUser)
                            <div class="invoice-preview-detail-row">
                                <span>Pet</span>
                                <span>{{ $petLine }}</span>
                            </div>
                        @endunless
                    </div>

                    <div class="invoice-preview-charges">
                        <div class="invoice-preview-charge-block">
                            <p class="invoice-preview-charge-title">Service</p>
                            <div class="invoice-preview-charge-row">
                                <span>{{ $serviceLineLabel }}</span>
                                <span>£{{ number_format($serviceAmount, 2) }}</span>
                            </div>
                        </div>
                        <div class="invoice-preview-charge-block">
                            <p class="invoice-preview-charge-title">Extras &amp; Add-ons</p>
                            @forelse ($extraAddOns as $addon)
                                <div class="invoice-preview-charge-row">
                                    <span>{{ $addon['label'] }}</span>
                                    <span>£{{ number_format((float) $addon['amount'], 2) }}</span>
                                </div>
                            @empty
                                <div class="invoice-preview-charge-row">
                                    <span>No add-ons</span>
                                    <span>£0.00</span>
                                </div>
                            @endforelse
                        </div>
                        @if ($isSpaceUser)
                            <svg class="invoice-preview-rule is-dashed" viewBox="0 0 360 1" width="360" height="1" preserveAspectRatio="none" aria-hidden="true" focusable="false">
                                <line x1="0.5" y1="0.5" x2="359.5" y2="0.5" stroke="#E2E2E2" stroke-linecap="round" stroke-dasharray="10 10" />
                            </svg>
                            <div class="invoice-preview-body-subtotal">
                                <span>{{ $subtotalLabel }}</span>
                                <span>£{{ number_format($subtotalAmount, 2) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="invoice-preview-footer">
                        <div class="invoice-preview-footer-row">
                            <span>{{ $subtotalLabel }}</span>
                            <span>£{{ number_format($subtotalAmount, 2) }}</span>
                        </div>
                        <div class="invoice-preview-rule is-solid" aria-hidden="true"></div>
                        <div class="invoice-preview-footer-row is-total">
                            <span>Total paid</span>
                            <span>£{{ number_format($totalPaidAmount, 2) }}</span>
                        </div>
                    </div>
                    <h2 class="sr-only" id="completed-booking-modal-title">Invoice {{ $invoiceIdLabel }}</h2>
                </div>
            </div>
        @endteleport
    @endif
@endif

@once
    <style>
        :root {
            --booking-modal-divider-color: #DCDCDC;
            --booking-modal-divider-height: 1px;
        }

        .completed-booking-modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.22);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            z-index: 100100;
        }

        .completed-booking-modal-card {
            width: min(610px, 100%);
            border-radius: 10px;
            border: 1px solid #CBDCE8;
            background: #F8F8F8;
            box-shadow: 0 10px 22px rgba(0, 0, 0, 0.12);
            overflow: hidden;
        }

        .completed-booking-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-radius: 10px 10px 0 0;
            border-bottom: 1px solid #CBDCE8;
            background: rgba(203, 220, 232, 0.20);
            padding: 1.2rem 1.65rem;
        }

        .completed-booking-modal-title {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-style: normal;
            font-weight: 700;
            line-height: normal;
        }

        .completed-booking-modal-close {
            border: none;
            background: transparent;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            line-height: 1;
        }

        .completed-booking-modal-booking-row {
            padding: 1.2rem 1.65rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            position: relative;
        }

        .completed-booking-modal-booking-row strong {
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .completed-booking-modal-booking-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-weight: 400;
            line-height: normal;
        }

        .completed-booking-download-btn {
            border: 0;
            background: transparent;
            color: inherit;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            cursor: pointer;
        }

        .completed-booking-modal-customer {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 1.2rem 1.65rem;
            position: relative;
        }

        .completed-booking-modal-user-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .completed-booking-modal-owner {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .completed-booking-modal-pet {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .completed-booking-modal-pet-type {
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            margin-left: 0.35rem;
        }

        .completed-booking-modal-section {
            padding: 1.2rem 1.65rem;
            position: relative;
        }

        .completed-booking-modal-booking-row::after,
        .completed-booking-modal-section::after {
            content: '';
            position: absolute;
            left: 1.65rem;
            right: 1.65rem;
            bottom: 0;
            height: var(--booking-modal-divider-height);
            background: var(--booking-modal-divider-color);
        }

        .completed-booking-modal-section-label {
            margin: 0 0 1rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .completed-booking-modal-section-title {
            margin: 0 0 0.65rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .completed-booking-modal-line {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 1rem;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .completed-booking-modal-line p {
            margin: 0;
        }

        .completed-booking-modal-line-sub {
            color: #9D9B98;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: 20px;
        }

        .completed-booking-modal-extras .completed-booking-modal-line-sub {
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: 23px;
        }

        .completed-booking-modal-card.is-space-user .completed-booking-modal-line-sub {
            color: #3B3731;
        }

        .completed-booking-modal-total-block {
            padding: 1.35rem 1.65rem 1.55rem;
        }

        .completed-booking-modal-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-weight: 400;
            line-height: 23px;
            margin-bottom: 1.7rem;
        }

        .completed-booking-modal-total-row>span:last-child {
            color: #3B3731;
            text-align: right;
            font-family: Lato;
            font-size: 18px;
            font-weight: 400;
            line-height: 23px;
        }

        .completed-booking-modal-total-row.is-grand {
            border-top: var(--booking-modal-divider-height) solid var(--booking-modal-divider-color);
            padding-top: 1rem;
            margin-top: 0.8rem;
            margin-bottom: 0;
        }

        .completed-booking-modal-total-row.is-grand>span {
            color: #3B3731 !important;
            font-family: Lato !important;
            font-style: normal !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            line-height: normal !important;
        }

        .completed-booking-addon-line {
            margin-bottom: 0.35rem;
        }

        .completed-booking-addon-line:last-child {
            margin-bottom: 0;
        }

        .invoice-preview-overlay {
            position: fixed;
            inset: 0;
            z-index: 100100;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1rem;
            background: rgba(59, 55, 49, 0.1);
        }

        .invoice-preview-card {
            width: min(400px, 100%);
            max-height: calc(100vh - 2rem);
            overflow: auto;
            background: #FFF;
            border-radius: 10px;
        }

        .invoice-preview-card.is-space {
            width: min(400px, 100%);
            min-height: 648px;
            display: flex;
            flex-direction: column;
        }

        .invoice-preview-card.is-space .invoice-preview-head {
            height: 60px;
            flex-shrink: 0;
        }

        .invoice-preview-card.is-space .invoice-preview-meta {
            margin-top: 19px;
            margin-bottom: 10px;
        }

        .invoice-preview-card.is-space .invoice-preview-id,
        .invoice-preview-card.is-space .invoice-preview-paid,
        .invoice-preview-card.is-space .invoice-preview-kicker,
        .invoice-preview-card.is-space .invoice-preview-value {
            line-height: 17px;
        }

        .invoice-preview-card.is-space .invoice-preview-bill {
            margin-bottom: 20px;
        }

        .invoice-preview-card.is-space .invoice-preview-people {
            height: 68px;
            padding: 16px 20px;
            margin-bottom: 20px;
        }

        .invoice-preview-card.is-space .invoice-preview-people-end {
            gap: 0;
        }

        .invoice-preview-card.is-space .invoice-preview-details {
            padding-bottom: 20px;
        }

        .invoice-preview-card.is-space .invoice-preview-detail-row>span:first-child {
            flex: 0 0 auto;
            min-width: 0;
        }

        .invoice-preview-card.is-space .invoice-preview-charges {
            padding: 20px 0;
            flex: 1 1 auto;
        }

        .invoice-preview-card.is-space .invoice-preview-charge-block {
            margin-bottom: 0;
        }

        .invoice-preview-card.is-space .invoice-preview-charge-title {
            margin: 0;
        }

        .invoice-preview-card.is-space .invoice-preview-footer {
            margin-top: auto;
            min-height: 120px;
            padding: 20px;
            flex-shrink: 0;
        }

        .invoice-preview-head {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 60px;
            padding: 0 20px;
        }

        .invoice-preview-wordmark {
            display: block;
            width: 72.86px;
            height: 20px;
        }

        .invoice-preview-close {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 20px;
            height: 20px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            line-height: 0;
        }

        .invoice-preview-close img {
            display: block;
            width: 20px;
            height: 20px;
        }

        .invoice-preview-meta,
        .invoice-preview-bill,
        .invoice-preview-people,
        .invoice-preview-details,
        .invoice-preview-charges {
            margin: 0 20px;
        }

        .invoice-preview-meta {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 10px;
        }

        .invoice-preview-id,
        .invoice-preview-kicker {
            margin: 0;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
        }

        .invoice-preview-paid {
            margin: 0;
            color: #A2C35D;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
        }

        .invoice-preview-paid::before {
            content: '';
            display: inline-block;
            width: 6px;
            height: 6px;
            margin-right: 6px;
            border-radius: 50%;
            background: #A2C35D;
            vertical-align: middle;
        }

        .invoice-preview-bill {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
            margin-bottom: 20px;
        }

        .invoice-preview-bill-date {
            text-align: right;
        }

        .invoice-preview-value {
            margin: 0;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
        }

        .invoice-preview-people {
            box-sizing: border-box;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            height: 68px;
            padding: 16px;
            margin-bottom: 20px;
            background: #FDFCF8;
            border-radius: 10px;
        }

        .invoice-preview-person {
            display: flex;
            align-items: center;
            gap: 10px;
            min-width: 0;
        }

        .invoice-preview-person-name {
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 600;
            line-height: normal;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .invoice-preview-people-end {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .invoice-preview-next {
            flex: 0 0 36px;
            width: 36px;
            height: 36px;
            display: block;
            line-height: 0;
        }

        .invoice-preview-next img {
            display: block;
            width: 36px;
            height: 36px;
        }

        .invoice-preview-avatar {
            position: relative;
            display: inline-flex;
            width: 36px;
            height: 36px;
            flex: 0 0 36px;
        }

        .invoice-preview-avatar-ring {
            position: absolute;
            inset: 0;
            display: block;
            width: 36px;
            height: 36px;
        }

        .invoice-preview-avatar-photo,
        .invoice-preview-avatar-fallback {
            position: absolute;
            top: 1.8px;
            left: 1.8px;
            width: 32.4px;
            height: 32.4px;
            border-radius: 50%;
            object-fit: cover;
        }

        .invoice-preview-avatar-fallback,
        .invoice-preview-pet-fallback {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #F2F6F9;
            color: #3B3731;
            font-family: Lato;
            font-size: 12px;
            font-weight: 600;
        }

        .invoice-preview-pet-chip {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            height: 36px;
            padding: 6px 10px 6px 6px;
            border-radius: 100px;
            background: rgba(255, 201, 122, 0.4);
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            line-height: normal;
        }

        .invoice-preview-pet-chip img,
        .invoice-preview-pet-fallback {
            width: 24px;
            height: 24px;
            border-radius: 199.5px;
            object-fit: cover;
            flex: 0 0 24px;
        }

        .invoice-preview-details {
            display: flex;
            flex-direction: column;
            gap: 0;
            padding-bottom: 20px;
            border-bottom: 1px solid #E2E2E2;
        }

        .invoice-preview-detail-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .invoice-preview-detail-row>span:first-child {
            flex: 0 0 90px;
            color: #9D9B98;
        }

        .invoice-preview-detail-row>span:last-child {
            flex: 1;
            text-align: right;
        }

        .invoice-preview-charges {
            padding: 20px 0;
        }

        .invoice-preview-rule {
            display: block;
            width: 100%;
            height: 1px;
            flex-shrink: 0;
            overflow: visible;
        }

        .invoice-preview-charges>.invoice-preview-rule {
            margin-top: 20px;
        }

        .invoice-preview-body-subtotal {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-top: 20px;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .invoice-preview-body-subtotal>span:last-child {
            color: #3B3731;
            text-align: right;
            white-space: nowrap;
        }

        .invoice-preview-footer>.invoice-preview-rule {
            margin: 20px 0;
        }

        .invoice-preview-rule.is-solid {
            background: #F0EAD9;
        }

        .invoice-preview-charge-block {
            margin-bottom: 12px;
        }

        .invoice-preview-charge-block:last-of-type {
            margin-bottom: 0;
        }

        .invoice-preview-charge-title {
            margin: 0 0 4px;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 700;
            line-height: 20px;
        }

        .invoice-preview-charge-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            color: #9D9B98;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .invoice-preview-charge-row>span:last-child {
            color: #3B3731;
            text-align: right;
            white-space: nowrap;
        }

        .invoice-preview-footer {
            box-sizing: border-box;
            min-height: 120px;
            padding: 20px;
            background: #F7F4EC;
            border-radius: 0 0 10px 10px;
        }

        .invoice-preview-footer-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .invoice-preview-footer-row.is-total {
            margin-top: 0;
            padding-top: 0;
            border-top: 0;
        }

        .invoice-preview-footer-row.is-total>span:first-child {
            font-weight: 600;
        }

        .invoice-preview-footer-row.is-total>span:last-child {
            font-family: "Playfair Display", serif;
            font-size: 20px;
            font-weight: 700;
            line-height: 20px;
        }

        .sr-only {
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

        .client-history-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 1200;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
            background: rgba(59, 55, 49, 0.35);
        }

        .client-history-modal {
            width: 610px;
            max-width: 100%;
            max-height: calc(100vh - 48px);
            overflow: auto;
            background: #fff;
            border-radius: 10px;
        }

        .client-history-modal__head {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 80px;
            background: rgba(203, 220, 232, 0.2);
            border-radius: 10px 10px 0 0;
        }

        .client-history-modal__head.is-space {
            background: #F5F8FA;
        }

        .client-history-modal__title {
            margin: 0;
            color: #3B3731;
            font-family: "Playfair Display", serif;
            font-size: 28px;
            font-weight: 800;
            line-height: normal;
            text-align: center;
        }

        .client-history-modal__title span {
            font-weight: 600;
        }

        .client-history-modal__close {
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

        .client-history-modal__close img {
            width: 14.5px;
            height: 14.5px;
            max-width: 14.5px;
            display: block;
        }

        .client-history-modal__body {
            padding: 0 25px 32px;
        }

        .client-history-modal__id,
        .client-history-modal__line,
        .client-history-modal__total {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
        }

        .client-history-modal__id {
            min-height: 56px;
            margin: 0;
            border-bottom: 1px solid #E2E2E2;
            color: #000;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
        }

        .client-history-modal__id p {
            margin: 0;
        }

        .client-history-modal__id-end {
            display: inline-flex;
            align-items: center;
            gap: 20px;
            color: #9D9B98;
        }

        .client-history-modal__download {
            position: relative;
            display: inline-flex;
            width: 36px;
            height: 36px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
        }

        .client-history-modal__download img {
            width: 36px;
            height: 36px;
            max-width: 36px;
            display: block;
        }

        .client-history-modal__download-glyph {
            position: absolute;
            top: 8px;
            left: 10px;
            width: 16px !important;
            height: 19px !important;
            max-width: 16px !important;
        }

        .client-history-modal__person {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 20px 0 0;
        }

        .client-history-modal__avatar,
        .client-history-modal__avatar img,
        .client-history-modal__avatar span {
            width: 44px;
            height: 44px;
            border-radius: 50%;
        }

        .client-history-modal__avatar {
            display: inline-flex;
            flex: 0 0 44px;
            overflow: hidden;
            background: #E7EEF3;
        }

        .client-history-modal__avatar img {
            max-width: 44px;
            object-fit: cover;
            display: block;
        }

        .client-history-modal__avatar span {
            display: flex;
            align-items: center;
            justify-content: center;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-weight: 600;
        }

        .client-history-modal__name,
        .client-history-modal__pet {
            display: block;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            line-height: normal;
        }

        .client-history-modal__name {
            font-weight: 600;
        }

        .client-history-modal__pet {
            font-weight: 400;
        }

        .client-history-modal__pet span {
            color: #9D9B98;
        }

        .client-history-modal__section,
        .client-history-modal__summary {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E2E2E2;
        }

        .client-history-modal__section-title {
            margin: 0 0 20px;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-weight: 600;
            line-height: normal;
        }

        .client-history-modal__line {
            color: #9D9B98;
            font-family: Lato;
            font-size: 18px;
            font-weight: 400;
            line-height: 23px;
        }

        .client-history-modal__line+.client-history-modal__line {
            margin-top: 10px;
        }

        .client-history-modal__line span:last-child {
            color: #3B3731;
            text-align: right;
            white-space: nowrap;
        }

        .client-history-modal__summary {
            padding-bottom: 20px;
            border-bottom: 1px solid #E2E2E2;
        }

        .client-history-modal__total {
            padding-top: 20px;
            color: #3B3731;
            font-family: Lato;
            font-size: 20px;
            font-weight: 700;
            line-height: normal;
        }
    </style>
@endonce
