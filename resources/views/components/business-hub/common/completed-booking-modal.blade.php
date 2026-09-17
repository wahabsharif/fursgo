@props([
    'booking' => null,
    'closeMethod' => 'closeCompletedBookingModal',
    'loadingEvent' => null,
])

@if ($booking)
    @php
        $isSpaceUser = auth()->check() && strtolower((string) auth()->user()->user_type) === 'space';
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
            $isAbsolute =
                str_starts_with($raw, 'http://') ||
                str_starts_with($raw, 'https://') ||
                str_starts_with($raw, 'data:') ||
                str_starts_with($raw, '/');

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
        $issuerShortName = count($issuerParts) >= 2
            ? $issuerParts[0] . ' ' . strtoupper(substr((string) end($issuerParts), 0, 1)) . '.'
            : $issuerName;
        $timeRaw = trim((string) ($booking->time ?? ''));
        $visitRaw = str_replace('_', ' ', strtolower((string) ($booking->visit_type ?? '')));
        $spaceVisitLabel = match (true) {
            $visitRaw === '' => 'Garden / Shed',
            str_contains((string) ($booking->visit_type ?? ''), '/') || str_contains((string) ($booking->visit_type ?? ''), ' ') => str_replace('Garden/Shed', 'Garden / Shed', (string) $booking->visit_type),
            $visitRaw === 'garden shed' || $visitRaw === 'garden/shed' => 'Garden / Shed',
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
                $diff = (((int) $endMatch[1]) * 60 + (int) $endMatch[2]) - (((int) $startMatch[1]) * 60 + (int) $startMatch[2]);
                if ($diff < 0) {
                    $diff += 24 * 60;
                }
                $durationLabel = $diff >= 7 * 60 ? 'Full-Day' : ($diff >= 3 * 60 ? 'Half-Day' : 'Hourly');
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
        $fallbackAddress = trim((string) ($firstPet?->address ?? $booking->petOwner?->address ?? ''));
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
            ->map(fn($item) => [
                'label' => trim((string) data_get($item, 'label', '')),
                'amount' => (float) data_get($item, 'amount', 0),
            ])
            ->filter(fn($item) => $item['label'] !== '')
            ->values();
        $extrasAmount = (float) $extraAddOns->sum('amount');
        $promoDiscount = (float) ($booking->discount ?? 0);
        $subtotalAmount = $serviceAmount + $extrasAmount;
        $totalPaidAmount = $subtotalAmount - $promoDiscount;
        $serviceLineLabel = $isSpaceUser
            ? ($spaceVisitLabel . ' — ' . $durationLabel)
            : ($booking->service ?: 'Service');
        $subtotalLabel = $isSpaceUser ? 'Space subtotal' : 'Groomer subtotal';
    @endphp
    @teleport('body')
    <div class="invoice-preview-overlay" wire:keydown.escape="{{ $closeMethod }}">
        <div class="invoice-preview-card" role="dialog" aria-modal="true" aria-labelledby="completed-booking-modal-title">
            <div class="invoice-preview-head">
                <img src="{{ asset('images/business-hub/icon-invoice-wordmark.svg') }}" alt="fursgo" width="73" height="20"
                    class="invoice-preview-wordmark">
                <button type="button" class="invoice-preview-close" @if ($loadingEvent)
                @click="window.dispatchEvent(new CustomEvent(@js($loadingEvent)))" @endif
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
                        <img src="{{ asset('images/business-hub/icon-invoice-avatar-ring.svg') }}" alt="" width="36"
                            height="36" class="invoice-preview-avatar-ring">
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
            </div>

            <div class="invoice-preview-footer">
                <div class="invoice-preview-footer-row">
                    <span>{{ $subtotalLabel }}</span>
                    <span>£{{ number_format($subtotalAmount, 2) }}</span>
                </div>
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
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: normal;
        }

        .invoice-preview-paid {
            margin: 0;
            color: #A2C35D;
            font-family: Lato, sans-serif;
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
            font-family: Lato, sans-serif;
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
            font-family: Lato, sans-serif;
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
            font-family: Lato, sans-serif;
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
            font-family: Lato, sans-serif;
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
            border-bottom: 1px solid #E8E8E8;
        }

        .invoice-preview-detail-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            color: #3B3731;
            font-family: Lato, sans-serif;
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

        .invoice-preview-charge-block {
            margin-bottom: 12px;
        }

        .invoice-preview-charge-block:last-child {
            margin-bottom: 0;
        }

        .invoice-preview-charge-title {
            margin: 0 0 4px;
            color: #3B3731;
            font-family: Lato, sans-serif;
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
            font-family: Lato, sans-serif;
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
            font-family: Lato, sans-serif;
            font-size: 14px;
            font-weight: 400;
            line-height: 20px;
        }

        .invoice-preview-footer-row.is-total {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #E8E8E8;
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
    </style>
@endonce