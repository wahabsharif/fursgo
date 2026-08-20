@props([
    'booking' => null,
    'closeMethod' => 'closeBookingDetailsDrawer',
    'loadingEvent' => 'bookings-tabs-loading-start',
])

@if ($booking)
    @php
        $bookingIdLabel = 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
        $status = strtolower((string) ($booking->booking_status ?? ''));
        $statusLabel = $status !== '' ? ucfirst($status) : 'N/A';

        $owner = $booking->petOwner;
        $ownerName = $owner->name ?? 'N/A';
        $ownerImageRaw = (string) ($owner->profile_image ?? '');
        $ownerImageUrl = $ownerImageRaw !== ''
            ? asset('storage/' . ltrim($ownerImageRaw, '/'))
            : null;

        $pet = $booking->pets->first();
        $petName = $pet->name ?? 'N/A';
        $petType = $pet->pet_type ?? '';
        $petBreed = $pet->breed ?? '';
        $petSexRaw = strtolower(trim((string) ($pet->sex ?? '')));
        $petSex = $petSexRaw !== '' ? ucfirst($petSexRaw) : 'N/A';
        $petWeight = $pet && $pet->weight !== null ? rtrim(rtrim(number_format((float) $pet->weight, 2, '.', ''), '0'), '.') . ' kg' : 'N/A';
        $petNotes = trim((string) ($pet->notes ?? ''));
        $petPhotoRaw = (string) ($pet->photo ?? '');
        $petPhotoUrl = $petPhotoRaw !== ''
            ? asset('storage/' . ltrim($petPhotoRaw, '/'))
            : null;

        $petSummaryParts = array_values(array_filter([$petName, $petType, $petBreed !== '' ? $petBreed : null]));
        $petSummary = $petSummaryParts !== [] ? implode(' · ', $petSummaryParts) : 'N/A';
        $petTypeBreed = trim(implode(' • ', array_filter([$petType, $petBreed])));

        $serviceLabel = $booking->service ?: 'N/A';
        $dateLabel = optional($booking->date)->format('l, jS F d/m/Y') ?? 'N/A';
        $timeRaw = trim((string) ($booking->time ?? ''));
        $timeLabel = $timeRaw !== '' ? $timeRaw : 'N/A';
        if (str_contains($timeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $timeRaw, 2);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($parts[0] ?? ''), $mStart);
            preg_match('/(\d{1,2}:\d{2})/', (string) ($parts[1] ?? ''), $mEnd);
            if (!empty($mStart[1]) && !empty($mEnd[1])) {
                $timeLabel = $mStart[1] . ' - ' . $mEnd[1];
            }
        }

        $serviceFee = (float) ($booking->amount ?? 0);
        $addOns = collect(is_array($booking->extra_add_ons) ? $booking->extra_add_ons : [])
            ->map(fn($item) => (float) data_get($item, 'amount', 0))
            ->sum();
        $discount = (float) ($booking->discount ?? 0);
        $total = $serviceFee + $addOns - $discount;

        $statusClass = match ($status) {
            'pending' => 'is-pending',
            'confirmed' => 'is-confirmed',
            'completed' => 'is-completed',
            'cancelled' => 'is-cancelled',
            default => 'is-default',
        };
    @endphp

    @teleport('body')
        <div class="booking-details-drawer-layer" wire:keydown.escape="{{ $closeMethod }}" x-data="{
            topOffset: 0,
            syncTopOffset() {
                const curve = document.querySelector('.dashboard-header .curve-shape-container');
                if (!curve) {
                    this.topOffset = 0;
                    return;
                }
                this.topOffset = Math.max(0, Math.round(curve.getBoundingClientRect().bottom));
            }
        }" x-init="
            if (window.__lockBookingDetailsDrawer) window.__lockBookingDetailsDrawer();
            syncTopOffset();
            const onResize = () => syncTopOffset();
            const blockScroll = (event) => {
                if (!event.target.closest('.booking-details-drawer')) {
                    event.preventDefault();
                }       
            };
            window.addEventListener('resize', onResize);
            document.addEventListener('wheel', blockScroll, { passive: false });
            document.addEventListener('touchmove', blockScroll, { passive: false });
            return () => {
                window.removeEventListener('resize', onResize);
                document.removeEventListener('wheel', blockScroll);
                document.removeEventListener('touchmove', blockScroll);
                if (window.__unlockBookingDetailsDrawer) window.__unlockBookingDetailsDrawer();
            };
        ">
            <button type="button" class="booking-details-drawer-backdrop"
                @if ($loadingEvent) @click="window.dispatchEvent(new CustomEvent(@js($loadingEvent)))" @endif
                wire:click="{{ $closeMethod }}" aria-label="Close booking details"></button>

            <aside class="booking-details-drawer" role="dialog" aria-modal="true"
                aria-labelledby="booking-details-drawer-title" :style="{ top: topOffset + 'px' }"
                @click.stop
                @wheel.stop.passive
                @touchmove.stop>
                <header class="booking-details-drawer-head">
                    <div>
                        <h3 id="booking-details-drawer-title" class="booking-details-drawer-title">Booking details</h3>
                        <p class="booking-details-drawer-id">{{ $bookingIdLabel }}</p>
                    </div>
                    <button type="button" class="booking-details-drawer-close"
                        @if ($loadingEvent) @click="window.dispatchEvent(new CustomEvent(@js($loadingEvent)))" @endif
                        wire:click="{{ $closeMethod }}" aria-label="Close">
                        <img src="{{ asset('images/booking-details/close.svg') }}" alt="" width="16" height="16">
                    </button>
                </header>

                <div class="booking-details-drawer-card {{ $statusClass }}">
                    <div class="booking-details-client-row">
                        <div class="booking-details-client">
                            <div class="booking-details-avatar-wrap">
                                @if ($ownerImageUrl)
                                    <img src="{{ $ownerImageUrl }}" alt="" class="booking-details-avatar">
                                @else
                                    <span class="booking-details-avatar is-fallback"
                                        aria-hidden="true">{{ strtoupper(substr($ownerName, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div class="booking-details-client-copy">
                                <div class="booking-details-client-name-row">
                                    <strong>{{ $ownerName }}</strong>
                                    <img src="{{ asset('images/booking-details/verified.svg') }}" alt="" width="16"
                                        height="16" class="booking-details-verified">
                                </div>
                                <p>{{ $petSummary }}</p>
                            </div>
                        </div>
                        <span class="booking-details-status {{ $statusClass }}">{{ $statusLabel }}</span>
                    </div>

                    <div class="booking-details-divider"></div>

                    <section class="booking-details-section">
                        <h4>Booking</h4>
                        <div class="booking-details-row">
                            <span>Service</span>
                            <strong>{{ $serviceLabel }}</strong>
                        </div>
                        <div class="booking-details-row">
                            <span>Date</span>
                            <strong>{{ $dateLabel }}</strong>
                        </div>
                        <div class="booking-details-row">
                            <span>Time</span>
                            <strong>{{ $timeLabel }}</strong>
                        </div>
                    </section>

                    <section class="booking-details-section">
                        <h4>Payment</h4>
                        <div class="booking-details-row">
                            <span>Service fee</span>
                            <strong>£{{ number_format($serviceFee, 2) }}</strong>
                        </div>
                        <div class="booking-details-row">
                            <span>Add-ons</span>
                            <strong>£{{ number_format($addOns, 2) }}</strong>
                        </div>
                        <div class="booking-details-row is-total">
                            <span>Total</span>
                            <strong>£{{ number_format($total, 2) }}</strong>
                        </div>
                    </section>

                    <div class="booking-details-pet-card">
                        <div class="booking-details-pet-main">
                            <div class="booking-details-pet-avatar-wrap">
                                @if ($petPhotoUrl)
                                    <img src="{{ $petPhotoUrl }}" alt="" class="booking-details-pet-avatar">
                                @else
                                    <span class="booking-details-pet-avatar is-fallback"
                                        aria-hidden="true">{{ strtoupper(substr($petName, 0, 1)) }}</span>
                                @endif
                            </div>
                            <div>
                                <div class="booking-details-pet-name-row">
                                    <img src="{{ asset('images/booking-details/paw.svg') }}" alt="" width="14"
                                        height="13">
                                    <strong>{{ $petName }}</strong>
                                </div>
                                @if ($petTypeBreed !== '')
                                    <p class="booking-details-pet-type">{{ $petTypeBreed }}</p>
                                @endif
                            </div>
                        </div>
                        <div class="booking-details-pet-meta">
                            <div class="booking-details-pet-meta-row">
                                <img src="{{ asset('images/booking-details/gender.svg') }}" alt="" width="11"
                                    height="15">
                                <span>{{ $petSex }}</span>
                            </div>
                            <div class="booking-details-pet-meta-row">
                                <img src="{{ asset('images/booking-details/weight.svg') }}" alt="" width="14"
                                    height="14">
                                <span>{{ $petWeight }}</span>
                            </div>
                            @if ($petNotes !== '')
                                <div class="booking-details-pet-meta-row">
                                    <img src="{{ asset('images/booking-details/note.svg') }}" alt="" width="14"
                                        height="14">
                                    <span>{{ $petNotes }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    @endteleport
@endif

<style>
    .booking-details-drawer-layer {
        position: fixed;
        inset: 0;
        z-index: 2147483000;
        overscroll-behavior: none;
    }

    .booking-details-drawer-backdrop {
        position: absolute;
        inset: 0;
        border: 0;
        background: rgba(59, 55, 49, 0.10);
        cursor: pointer;
        pointer-events: auto;
        touch-action: none;
    }

    .booking-details-drawer {
        position: absolute;
        right: 0;
        bottom: 0;
        z-index: 1;
        width: min(578px, 100vw);
        background: #fff;
        border-radius: 10px 0 0 10px;
        box-shadow: -6px 0 16.8px -1px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: column;
        overflow: hidden;
        pointer-events: auto;
        touch-action: auto;
        animation: booking-details-drawer-in 180ms ease-out;
    }

    @keyframes booking-details-drawer-in {
        from {
            transform: translateX(24px);
            opacity: 0.85;
        }

        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    .booking-details-drawer-head {
        background: #fafafa;
        border-radius: 10px 0 0 0;
        min-height: 120px;
        padding: 1.8rem 1.55rem 1.2rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
        flex-shrink: 0;
    }

    .booking-details-drawer-title {
        margin: 0;
        color: #3B3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 24px;
        font-weight: 600;
        line-height: normal;
    }

    .booking-details-drawer-id {
        margin: 0.2rem 0 0;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .booking-details-drawer-close {
        border: 0;
        background: transparent;
        padding: 0.35rem;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
    }

    .booking-details-drawer-card {
        margin: 0 1.2rem 1.4rem;
        padding: 1.15rem 1.1rem 1.1rem;
        border: 1px solid #AFCD6F;
        border-radius: 10px;
        background: #fff;
        overflow: auto;
        flex: 1;
    }

    .booking-details-drawer-card.is-pending {
        border-color: #FFC97A;
    }

    .booking-details-drawer-card.is-completed {
        border-color: #9FC7E4;
    }

    .booking-details-drawer-card.is-cancelled {
        border-color: #FFA899;
    }

    .booking-details-client-row {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 0.75rem;
    }

    .booking-details-client {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 0;
    }

    .booking-details-avatar-wrap {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
    }

    .booking-details-avatar {
        width: 45px;
        height: 45px;
        margin: 2.5px;
        border-radius: 999px;
        object-fit: cover;
        display: block;
    }

    .booking-details-avatar.is-fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #F1F1F1;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
    }

    .booking-details-client-copy {
        min-width: 0;
    }

    .booking-details-client-name-row {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .booking-details-client-name-row strong {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .booking-details-verified {
        display: block;
        flex-shrink: 0;
    }

    .booking-details-client-copy p {
        margin: 0.2rem 0 0;
        color: #9C9790;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .booking-details-status {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 100px;
        padding: 0.28rem 0.75rem;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 500;
        line-height: normal;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .booking-details-status.is-confirmed {
        color: #AFCD6F;
        background: rgba(201, 221, 160, 0.2);
    }

    .booking-details-status.is-pending {
        color: #C29B41;
        background: rgba(255, 201, 122, 0.2);
    }

    .booking-details-status.is-completed {
        color: #8FAEC5;
        background: rgba(203, 220, 232, 0.35);
    }

    .booking-details-status.is-cancelled {
        color: #D06B72;
        background: rgba(255, 168, 153, 0.25);
    }

    .booking-details-status.is-default {
        color: #9D9B98;
        background: #F1F1F1;
    }

    .booking-details-divider {
        height: 1px;
        background: #E8E6E3;
        margin: 1rem 0 1.15rem;
    }

    .booking-details-section {
        margin-bottom: 1.4rem;
    }

    .booking-details-section h4 {
        margin: 0 0 0.85rem;
        color: #000;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .booking-details-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        padding: 0.55rem 0;
        border-bottom: 1px solid #E8E6E3;
    }

    .booking-details-row span {
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .booking-details-row strong {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
        text-align: right;
    }

    .booking-details-row.is-total strong {
        color: #AFCD6F;
    }

    .booking-details-pet-card {
        margin-top: 0.35rem;
        background: rgba(255, 230, 194, 0.2);
        border-radius: 5px;
        padding: 1rem 1.05rem;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 1rem;
    }

    .booking-details-pet-main {
        display: flex;
        align-items: flex-start;
        gap: 0.7rem;
        min-width: 0;
    }

    .booking-details-pet-avatar-wrap {
        width: 50px;
        height: 50px;
        flex-shrink: 0;
    }

    .booking-details-pet-avatar {
        width: 46px;
        height: 46px;
        margin: 2px;
        border-radius: 999px;
        object-fit: cover;
        display: block;
    }

    .booking-details-pet-avatar.is-fallback {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #FFF;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 15px;
        font-weight: 600;
    }

    .booking-details-pet-name-row {
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .booking-details-pet-name-row strong {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: normal;
    }

    .booking-details-pet-type {
        margin: 0.25rem 0 0;
        color: #9D9B98;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    .booking-details-pet-meta {
        display: flex;
        flex-direction: column;
        gap: 0.45rem;
        max-width: 220px;
    }

    .booking-details-pet-meta-row {
        display: flex;
        align-items: flex-start;
        gap: 0.4rem;
    }

    .booking-details-drawer-close img,
    .booking-details-verified,
    .booking-details-pet-name-row img,
    .booking-details-pet-meta-row img {
        display: block;
        width: auto;
        height: auto;
        max-width: 16px;
        max-height: 16px;
        object-fit: contain;
        flex-shrink: 0;
        aspect-ratio: auto;
    }

    .booking-details-drawer-close img {
        width: 16px;
        height: 16px;
    }

    .booking-details-verified {
        width: 15px;
        height: 15px;
    }

    .booking-details-pet-name-row img {
        width: 14px;
        height: 13px;
    }

    .booking-details-pet-meta-row img {
        margin-top: 0.15rem;
        width: 14px;
        height: 14px;
    }

    .booking-details-pet-meta-row img[src*="gender"] {
        width: 11px;
        height: 15px;
    }

    .booking-details-pet-meta-row span {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 400;
        line-height: normal;
    }

    @media (max-width: 640px) {
        .booking-details-drawer {
            width: 100%;
            border-radius: 0;
        }

        .booking-details-drawer-card {
            margin: 0 0.85rem 1rem;
        }

        .booking-details-pet-card {
            flex-direction: column;
        }

        .booking-details-pet-meta {
            max-width: none;
        }
    }
</style>
