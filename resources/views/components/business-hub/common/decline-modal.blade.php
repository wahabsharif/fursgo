@props([
    'declineBooking' => null,
])

@if ($declineBooking)
    @php
        $isSpaceUser = auth()->check() && strtolower((string) auth()->user()->user_type) === 'space';
        $declinePetName = $declineBooking->pets->pluck('name')->filter()->first() ?? 'N/A';
        $declineDateLabel = optional($declineBooking->date)->format('D j F') ?? 'N/A';
        $declineClient = $declineBooking->petOwner->name ?? 'N/A';
        $declineVisitType = str_replace('_', ' ', strtolower((string) ($declineBooking->visit_type ?? '')));
        if ($declineVisitType === 'home' || $declineVisitType === 'home visit') {
            $declineSpaceLabel = 'Home Visit';
        } elseif ($declineVisitType === 'salon' || $declineVisitType === 'salon visit') {
            $declineSpaceLabel = 'Salon Visit';
        } else {
            $declineSpaceLabel = ucfirst($declineVisitType ?: 'N/A');
        }
        $declineBookingIdLabel = 'FG-' . str_pad((string) $declineBooking->id, 5, '0', STR_PAD_LEFT);
        $declineAmountLabel = '£' . number_format((float) $declineBooking->amount, 2);
        $declineTimeRaw = (string) $declineBooking->time;
        $declineTimeLabel = $declineTimeRaw !== '' ? $declineTimeRaw : 'N/A';

        $formatDurationLabel = static function (int $durationMinutes): string {
            $hours = (int) floor($durationMinutes / 60);
            $minutes = (int) ($durationMinutes % 60);

            if ($hours <= 0 && $minutes <= 0) {
                return '';
            }

            if ($minutes === 0) {
                return $hours . 'hr';
            }

            if ($hours === 0) {
                return $minutes . 'm';
            }

            return $hours . 'hr ' . $minutes . 'm';
        };

        if (str_contains($declineTimeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $declineTimeRaw, 2);
            $start = $parts[0] ?? '';
            $end = $parts[1] ?? '';
            preg_match('/(\d{1,2}:\d{2})/', $start, $mStart);
            preg_match('/(\d{1,2}:\d{2})/', $end, $mEnd);
            if (!empty($mStart[1]) && !empty($mEnd[1])) {
                try {
                    $startDt = new DateTimeImmutable($mStart[1]);
                    $endDt = new DateTimeImmutable($mEnd[1]);
                    if ($endDt < $startDt) {
                        $endDt = $endDt->modify('+1 day');
                    }
                    $durationMinutes = (int) max(0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60);
                    $durationLabel = $formatDurationLabel($durationMinutes);

                    if ($durationLabel !== '') {
                        $declineTimeLabel = $startDt->format('h:i A') . ' (' . $durationLabel . ')';
                    } else {
                        $declineTimeLabel = $declineTimeRaw;
                    }
                } catch (Throwable $e) {
                    $declineTimeLabel = $declineTimeRaw;
                }
            }
        }
        $declineTimeLabelForSpace = $declineTimeLabel;
        if (str_contains($declineTimeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $declineTimeRaw, 2);
            $start = $parts[0] ?? '';
            preg_match('/(\d{1,2}:\d{2})/', $start, $mStartOnly);
            if (!empty($mStartOnly[1])) {
                try {
                    $startDt = new DateTimeImmutable($mStartOnly[1]);
                    $declineTimeLabelForSpace = 'Hourly (' . strtoupper($startDt->format('h:ia')) . ')';
                } catch (Throwable $e) {
                    $declineTimeLabelForSpace =
                        'Hourly (' . strtoupper(str_replace(' ', '', trim((string) $start))) . ')';
                }
            }
        }

        $declineReasonOptions = ['Changes in schedule', 'Can’t accommodate this request', 'Other'];
    @endphp
    @teleport('body')
    <div class="decline-modal-overlay" x-data="{ open: false, reason: 'Changes in schedule' }"
        @keydown.escape.window="open ? open = false : $wire.closeDeclineModal()">
        <div class="decline-modal-card" role="dialog" aria-modal="true" aria-labelledby="decline-modal-title">
            <button type="button" class="decline-modal-close" wire:click="closeDeclineModal" aria-label="Close modal">
                <img src="{{ asset('images/business-hub/icon-decline-close.svg') }}" alt="" width="14.5" height="14.5"
                    class="decline-modal-close-icon">
            </button>

            <div class="decline-modal-icon" aria-hidden="true">
                <img src="{{ asset('images/business-hub/icon-decline-calendar.svg') }}" alt="" width="53.496" height="50"
                    class="decline-modal-icon-img">
            </div>
            <h3 class="decline-modal-title" id="decline-modal-title">
                <span class="decline-modal-title-strong">Decline</span> Booking Request
            </h3>
            <p class="decline-modal-subtitle">Are you sure you want to decline this booking request?</p>

            <div class="decline-reason">
                <p class="decline-reason-label">Reason (shared with the client)</p>
                <div class="decline-reason-dropdown" @click.outside="open = false">
                    <button type="button" class="decline-reason-trigger" @click="open = !open"
                        :aria-expanded="open.toString()" aria-haspopup="listbox">
                        <span x-text="reason"></span>
                        <span class="decline-reason-chevron-wrap" aria-hidden="true">
                            <img src="{{ asset('images/business-hub/icon-decline-chevron.svg') }}" alt="" width="7.12"
                                height="7" class="decline-reason-chevron">
                        </span>
                    </button>
                    <div class="decline-reason-menu" x-cloak x-show="open" role="listbox"
                        x-transition.opacity.duration.100ms>
                        @foreach ($declineReasonOptions as $option)
                            <button type="button" class="decline-reason-option" role="option"
                                @click="reason = @js($option); open = false">{{ $option }}</button>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="decline-modal-details">
                <div class="decline-modal-detail-row"><span>Booking
                        ID</span><strong>{{ $declineBookingIdLabel }}</strong></div>
                @if ($isSpaceUser)
                    <div class="decline-modal-detail-row"><span>Client</span><strong>{{ $declineClient }}</strong></div>
                    <div class="decline-modal-detail-row"><span>Space</span><strong>{{ $declineSpaceLabel }}</strong>
                    </div>
                    <div class="decline-modal-detail-row">
                        <span>Time</span><strong>{{ $declineTimeLabelForSpace }}</strong>
                    </div>
                    <div class="decline-modal-detail-row"><span>Date</span><strong>{{ $declineDateLabel }}</strong>
                    </div>
                @else
                    <div class="decline-modal-detail-row"><span>Pet</span><strong>{{ $declinePetName }}</strong></div>
                    <div class="decline-modal-detail-row">
                        <span>Service</span><strong>{{ $declineBooking->service }}</strong>
                    </div>
                    <div class="decline-modal-detail-row"><span>Date</span><strong>{{ $declineDateLabel }}</strong>
                    </div>
                    <div class="decline-modal-detail-row"><span>Time</span><strong>{{ $declineTimeLabel }}</strong>
                    </div>
                    <div class="decline-modal-detail-row"><span>Client</span><strong>{{ $declineClient }}</strong></div>
                @endif
                <div class="decline-modal-detail-row decline-modal-detail-payment">
                    <span>Payment</span><strong>{{ $declineAmountLabel }}</strong>
                </div>
            </div>

            <div class="decline-modal-actions">
                <button type="button" class="decline-cancel-btn" wire:click="closeDeclineModal">Keep booking</button>
                <button type="button" class="decline-confirm-btn" wire:click="confirmDeclineBooking"
                    wire:loading.attr="disabled" wire:target="confirmDeclineBooking">
                    <span wire:loading.remove wire:target="confirmDeclineBooking">Decline Booking</span>
                    <span class="decline-btn-loading" wire:loading.inline-flex wire:target="confirmDeclineBooking">
                        <span class="decline-btn-spinner" aria-hidden="true"></span>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endteleport
@endif

<style>
    .decline-modal-overlay [x-cloak] {
        display: none !important;
    }

    .decline-modal-overlay {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(59, 55, 49, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        margin: 0;
        z-index: 100100;
        overflow: auto;
    }

    .decline-modal-card {
        width: 505px;
        max-width: calc(100vw - 2rem);
        border-radius: 10px;
        border: none;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 20px 19.5px;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow: visible;
    }

    .decline-modal-close {
        position: absolute;
        top: 16px;
        right: 16px;
        border: none;
        background: transparent;
        cursor: pointer;
        line-height: 1;
        padding: 0;
        width: 14.5px;
        height: 14.5px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .decline-modal-close-icon {
        width: 14.5px;
        height: 14.5px;
        display: block;
    }

    .decline-modal-icon {
        margin-top: 0;
        width: 53.496px;
        height: 50px;
    }

    .decline-modal-icon-img {
        width: 53.496px;
        height: 50px;
        display: block;
    }

    .decline-modal-title {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 10px 0 0;
    }

    .decline-modal-title-strong {
        font-weight: 800;
    }

    .decline-modal-subtitle {
        color: #9D9B98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin: 10px 0 0;
        max-width: 466px;
    }

    .decline-reason {
        width: 100%;
        max-width: 466px;
        margin-top: 24px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 13px;
    }

    .decline-reason-label {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 0;
    }

    .decline-reason-dropdown {
        position: relative;
        width: 100%;
    }

    .decline-reason-trigger {
        width: 100%;
        height: 42px;
        border-radius: 10px;
        border: 1px solid #FF6E6E;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 12px 0 12px;
        text-align: left;
    }

    .decline-reason-chevron-wrap {
        width: 9.984px;
        height: 9.984px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex: none;
    }

    .decline-reason-chevron {
        width: 7.12px;
        height: 7px;
        display: block;
        transform: rotate(135deg);
    }

    .decline-reason-menu {
        position: absolute;
        top: 100%;
        left: 0;
        width: 100%;
        z-index: 2;
        background: #FFF;
        overflow: hidden;
        border-radius: 5px;
    }

    .decline-reason-option {
        width: 100%;
        height: 42px;
        border: 1px solid #D9D9D9;
        background: #FFF;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        text-align: left;
        padding: 0 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        margin-top: -1px;
    }

    .decline-reason-option:first-child {
        border-radius: 5px 5px 0 0;
        margin-top: 0;
    }

    .decline-reason-option:last-child {
        border-radius: 0 0 5px 5px;
    }

    .decline-reason-option:hover {
        background: #F9F9F9;
    }

    .decline-modal-details {
        width: 100%;
        max-width: 466px;
        min-height: 192px;
        border-radius: 5px;
        background: #F9F9F9;
        padding: 20px;
        margin-top: 20px;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        gap: 0;
    }

    .decline-modal-detail-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 19px;
    }

    .decline-modal-detail-row strong {
        text-align: right;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: 19px;
    }

    .decline-modal-detail-payment {
        margin-top: 19px;
        margin-bottom: 0;
    }

    .decline-modal-detail-payment strong {
        color: #FF6E6E;
        text-decoration: line-through;
        text-decoration-color: #FF6E6E;
        font-weight: 500;
    }

    .decline-modal-actions {
        width: 100%;
        max-width: 466px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 20px;
    }

    .decline-cancel-btn,
    .decline-confirm-btn {
        height: 42px;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        line-height: normal;
        cursor: pointer;
        border: 1px solid transparent;
    }

    .decline-cancel-btn {
        width: 137px;
        border-radius: 75px;
        background: #FFF;
        border-color: #3B3731;
        color: #3B3731;
        font-weight: 400;
    }

    .decline-confirm-btn {
        width: 156px;
        border-radius: 96px;
        background: #FF6E6E;
        color: #FFF;
        border-color: #FF6E6E;
        font-weight: 600;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.1);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
    }

    .decline-confirm-btn[disabled] {
        opacity: 0.9;
        cursor: not-allowed;
    }

    .decline-btn-loading {
        align-items: center;
        gap: 0.45rem;
    }

    .decline-btn-spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.55);
        border-top-color: #FFF;
        border-radius: 50%;
        animation: decline-btn-spin 0.8s linear infinite;
    }

    @keyframes decline-btn-spin {
        to {
            transform: rotate(360deg);
        }
    }

    @media (max-width: 560px) {
        .decline-modal-card {
            width: 100%;
            padding: 20px 16px;
        }

        .decline-modal-actions {
            flex-wrap: wrap;
            justify-content: center;
        }
    }
</style>