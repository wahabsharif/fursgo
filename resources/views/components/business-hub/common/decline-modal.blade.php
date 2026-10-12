@props([
    'declineBooking' => null,
])

@if ($declineBooking)
    @php
        $iconBase = asset('images/business-hub');
        $spacerUser = auth('groomer_spacer')->user() ?? auth()->user();
        $isSpaceUser = strtolower((string) ($spacerUser?->user_type ?? '')) === 'space';

        $pet = $declineBooking->pets->first();
        $petName = trim((string) ($pet->name ?? '')) ?: 'N/A';
        $petType = trim((string) ($pet->pet_type ?? ($pet->type ?? ($pet->species ?? ''))));
        $declinePetLabel = $petType !== '' ? $petName . ' (' . $petType . ')' : $petName;

        $declineDateLabel = optional($declineBooking->date)->format($isSpaceUser ? 'D j M' : 'D j F') ?? 'N/A';
        $ownerName = trim((string) ($declineBooking->petOwner->name ?? 'N/A')) ?: 'N/A';
        $ownerParts = preg_split('/\s+/', $ownerName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $declineClient = count($ownerParts) >= 2 ? $ownerParts[0] . ' ' . mb_substr($ownerParts[1], 0, 1) . '.' : $ownerName;

        $rawVisit = (string) ($declineBooking->visit_type ?? '');
        $visitLabel = str_replace('_', ' ', strtolower(trim($rawVisit)));
        $declineSpaceLabel = match (true) {
            $visitLabel === '' => 'Garden/Shed',
            str_contains($rawVisit, '/') => str_replace([' / ', ' /', '/ '], '/', $rawVisit),
            $visitLabel === 'garden shed' || $visitLabel === 'garden/shed' => 'Garden/Shed',
            $visitLabel === 'home' || $visitLabel === 'home visit' => 'Home Visit',
            $visitLabel === 'salon' || $visitLabel === 'salon visit' => 'Salon',
            $visitLabel === 'private room' => 'Private room',
            $visitLabel === 'mobile station' => 'Mobile Station',
            $visitLabel === 'other' => 'Other',
            default => ucwords($visitLabel),
        };
        $declineAmountLabel = '£' . number_format((float) $declineBooking->amount, 2);
        $declineTimeRaw = (string) $declineBooking->time;
        $declineTimeLabel = $declineTimeRaw !== '' ? $declineTimeRaw : 'N/A';
        $serviceLower = strtolower(trim((string) ($declineBooking->service ?? '')));
        $spaceServiceLabel = match (true) {
            (bool) preg_match('/full[\s_-]*day|fullday/', $serviceLower) => 'Full-Day',
            (bool) preg_match('/half[\s_-]*day/', $serviceLower) => 'Half-Day',
            str_contains($serviceLower, 'hour') => 'Hourly',
            default => null,
        };

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
        } elseif ($declineTimeRaw !== '') {
            try {
                $startDt = new DateTimeImmutable(substr($declineTimeRaw, 0, 5));
                $declineTimeLabel = $startDt->format('h:i A') . ' (1hr)';
            } catch (Throwable $e) {
                // keep raw
            }
        }

        if ($spaceServiceLabel === null && str_contains($declineTimeRaw, '-')) {
            $rangeParts = preg_split('/\s*-\s*/', $declineTimeRaw, 2);
            preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[0] ?? ''), $startMatch);
            preg_match('/(\d{1,2}):(\d{2})/', (string) ($rangeParts[1] ?? ''), $endMatch);
            if (!empty($startMatch[1]) && !empty($endMatch[1])) {
                $diff = ((int) $endMatch[1]) * 60 + (int) $endMatch[2] - (((int) $startMatch[1]) * 60 + (int) $startMatch[2]);
                if ($diff < 0) {
                    $diff += 24 * 60;
                }
                $spaceServiceLabel = $diff >= 7 * 60 ? 'Full-Day' : ($diff >= 3 * 60 ? 'Half-Day' : 'Hourly');
            }
        }
        if ($spaceServiceLabel === null) {
            $plainService = trim((string) ($declineBooking->service ?? ''));
            $spaceServiceLabel = $plainService !== '' ? $plainService : 'Hourly';
        }

        $declineTimeLabelForSpace = $spaceServiceLabel;
        if (str_contains($declineTimeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $declineTimeRaw, 2);
            $start = $parts[0] ?? '';
            preg_match('/(\d{1,2}:\d{2})/', $start, $mStartOnly);
            if (!empty($mStartOnly[1])) {
                try {
                    $startDt = new DateTimeImmutable($mStartOnly[1]);
                    $declineTimeLabelForSpace = $spaceServiceLabel . ' (' . strtoupper($startDt->format('h:ia')) . ')';
                } catch (Throwable $e) {
                    $declineTimeLabelForSpace = $spaceServiceLabel . ' (' . strtoupper(str_replace(' ', '', trim((string) $start))) . ')';
                }
            }
        }

        $declineServiceLabel = trim((string) ($declineBooking->service ?? '')) ?: 'Service';
        $declineReasonOptions = \App\Support\BookingDeclineReasons::options();
        $declineReasonDefault = \App\Support\BookingDeclineReasons::default();
        $declineBookingId = (int) $declineBooking->id;
    @endphp
    @teleport('body')
        <div class="decline-modal-overlay{{ $isSpaceUser ? ' is-space' : '' }}" x-data="{ open: false, reason: @js($declineReasonDefault) }"
            @keydown.escape.window="open ? open = false : $wire.closeDeclineModal()">
            <div class="decline-modal-card" role="dialog" aria-modal="true" aria-labelledby="decline-modal-title">
                <button type="button" class="decline-modal-close" wire:click="closeDeclineModal" aria-label="Close modal">
                    <img src="{{ $iconBase }}/icon-request-modal-close.svg" alt="" width="20" height="20">
                </button>

                <div class="decline-modal-icon" aria-hidden="true">
                    <img src="{{ $iconBase }}/icon-decline-success.png" alt="" width="45" height="45">
                </div>
                <h3 class="decline-modal-title" id="decline-modal-title">Decline Booking Request</h3>
                <p class="decline-modal-subtitle">Are you sure you want to decline this booking request?</p>

                <div class="decline-reason">
                    <p class="decline-reason-label">Reason (shared with the client)</p>
                    <div class="decline-reason-dropdown" @click.outside="open = false">
                        <button type="button" class="decline-reason-trigger" @click="open = !open"
                            :aria-expanded="open.toString()" aria-haspopup="listbox">
                            <span x-text="reason"></span>
                            <span class="decline-reason-chevron-wrap" aria-hidden="true">
                                <img src="{{ $iconBase }}/icon-decline-chevron.svg" alt="" width="7.12" height="7"
                                    class="decline-reason-chevron">
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
                        <div class="decline-modal-detail-row"><span>Pet</span><strong>{{ $declinePetLabel }}</strong></div>
                        <div class="decline-modal-detail-row">
                            <span>Service</span><strong>{{ $declineServiceLabel }}</strong>
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
                    <button type="button" class="decline-reschedule-btn"
                        wire:click="openRescheduleFromDecline({{ $declineBookingId }})">
                        <img src="{{ $iconBase }}/icon-reschedule-sync.svg" alt="" width="12.9951" height="13">
                        Reschedule
                    </button>
                    <button type="button" class="decline-confirm-btn" @click="$wire.confirmDeclineBooking(reason)"
                        wire:loading.attr="disabled" wire:target="confirmDeclineBooking">
                        <span wire:loading.remove wire:target="confirmDeclineBooking">Decline</span>
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
        inset: 0;
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
        font-family: Lato, sans-serif;
    }

    .decline-modal-card {
        width: min(505px, calc(100vw - 2rem));
        border-radius: 10px;
        border: none;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 20px;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        overflow: visible;
    }

    .decline-modal-close {
        position: absolute;
        top: 20px;
        right: 20px;
        border: none;
        background: transparent;
        cursor: pointer;
        line-height: 1;
        padding: 0;
        width: 20px;
        height: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .decline-modal-close img {
        width: 20px;
        height: 20px;
        display: block;
    }

    .decline-modal-icon {
        margin: 0 0 20px;
        width: 45px;
        height: 45px;
    }

    .decline-modal-icon img {
        width: 45px;
        height: 45px;
        display: block;
    }

    .decline-modal-title {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display", serif;
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 0;
        text-box-edge: cap alphabetic;
        text-box-trim: trim-both;
    }

    .decline-modal-subtitle {
        color: #9D9B98;
        text-align: center;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin: 20px 0 0;
        max-width: 276px;
        text-box-edge: cap alphabetic;
        text-box-trim: trim-both;
    }

    .decline-reason {
        width: 100%;
        max-width: 465px;
        margin-top: 20px;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }

    .decline-reason-label {
        color: #3B3731;
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
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 12px;
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
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .decline-modal-detail-row strong {
        text-align: right;
        color: #3B3731;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .decline-modal-detail-payment {
        margin-top: 19px;
        margin-bottom: 0;
    }

    .decline-modal-detail-payment strong {
        color: #FF6E6E;
        text-decoration: line-through;
        text-decoration-color: #FF6E6E;
        font-weight: 600;
    }

    .decline-modal-actions {
        width: 100%;
        max-width: 466px;
        display: flex;
        align-items: center;
        gap: 20px;
        margin-top: 20px;
    }

    .decline-reschedule-btn,
    .decline-confirm-btn {
        flex: 1;
        height: 42px;
        text-align: center;
        font-size: 16px;
        font-style: normal;
        line-height: normal;
        cursor: pointer;
        border-radius: 75px;
        font-weight: 600;
    }

    .decline-reschedule-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        background: #FFF;
        border: 1px solid #E2E2E2;
        color: #9D9B98;
    }

    .decline-reschedule-btn img {
        width: 12.9951px;
        height: 13px;
        display: block;
        flex-shrink: 0;
    }

    .decline-confirm-btn {
        border: 0;
        background: #FF6E6E;
        color: #FFF;
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

    .decline-modal-overlay.is-space .decline-modal-detail-payment {
        margin-top: 19px;
    }

    @media (max-width: 560px) {
        .decline-modal-card {
            width: 100%;
            padding: 20px 16px;
        }

        .decline-modal-actions {
            flex-wrap: wrap;
        }
    }
</style>
