@props([
    'declineBooking' => null,
])

@if ($declineBooking)
    @php
        $declinePetName = $declineBooking->pets->pluck('name')->filter()->first() ?? 'N/A';
        $declineDateLabel = optional($declineBooking->date)->format('D j F') ?? 'N/A';
        $declineClient = $declineBooking->petOwner->name ?? 'N/A';
        $declineBookingIdLabel = 'FG-' . str_pad((string) $declineBooking->id, 5, '0', STR_PAD_LEFT);
        $declineAmountLabel = '£' . number_format((float) $declineBooking->amount, 2);
        $declineTimeRaw = (string) $declineBooking->time;
        $declineTimeLabel = $declineTimeRaw !== '' ? $declineTimeRaw : 'N/A';

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
                    $durationLabel = $durationMinutes > 0 ? $durationMinutes . ' minutes' : '';

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
    @endphp
    @teleport('body')
        <div class="decline-modal-overlay" wire:keydown.escape="closeDeclineModal">
            <div class="decline-modal-card" role="dialog" aria-modal="true" aria-labelledby="decline-modal-title">
                <button type="button" class="decline-modal-close" wire:click="closeDeclineModal" aria-label="Close modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"
                        fill="none">
                        <path d="M0.75 15.75L15.75 0.75M0.75 0.75L15.75 15.75" stroke="#3B3731" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                </button>

                <div class="decline-modal-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="77" viewBox="0 0 80 77"
                        fill="none">
                        <path
                            d="M3.95918 29C3.79598 31.9412 3.79598 35.4404 3.79598 39.6442V46.2935C3.79598 58.8307 3.79967 65.0976 8.12508 68.9941C12.4505 72.8906 19.4169 72.8906 33.3461 72.8906H48.1213C62.0504 72.8906 69.0132 72.8873 73.3423 68.9941C77.6714 65.101 77.6714 58.8307 77.6714 46.2935V39.6442C77.6714 35.4401 77.6711 31.941 77.5074 29H3.95918Z"
                            fill="#F2F6F9" />
                        <path
                            d="M0.754211 37.3777C0.754211 22.9384 0.754211 15.7168 5.44221 11.233C10.1302 6.74919 17.6702 6.74536 32.7542 6.74536H48.7542C63.8382 6.74536 71.3822 6.74536 76.0662 11.233C80.7502 15.7206 80.7542 22.9384 80.7542 37.3777V45.0358C80.7542 59.4751 80.7542 66.6967 76.0662 71.1805C71.3782 75.6643 63.8382 75.6681 48.7542 75.6681H32.7542C17.6702 75.6681 10.1262 75.6681 5.44221 71.1805C0.758211 66.6929 0.754211 59.4751 0.754211 45.0358V37.3777Z"
                            stroke="#3B3731" stroke-width="2" />
                        <path d="M20.1494 6.71614V1M61.3184 6.71614V1M1.62305 25.7699H79.8441" stroke="#3B3731"
                            stroke-width="2" stroke-linecap="round" />
                        <path d="M31.2012 48.3501L49.4999 30.0514M31.2012 30.0514L49.4999 48.3501" stroke="#FF6E6E"
                            stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <h3 class="decline-modal-title" id="decline-modal-title">Decline Booking Request</h3>
                <p class="decline-modal-subtitle">Are you sure you want to decline <br />this booking request?</p>

                <div class="decline-modal-details">
                    <div class="decline-modal-detail-row"><span>Booking
                            ID</span><strong>{{ $declineBookingIdLabel }}</strong></div>
                    <div class="decline-modal-detail-row"><span>Pet</span><strong>{{ $declinePetName }}</strong></div>
                    <div class="decline-modal-detail-row">
                        <span>Service</span><strong>{{ $declineBooking->service }}</strong>
                    </div>
                    <div class="decline-modal-detail-row"><span>Date</span><strong>{{ $declineDateLabel }}</strong></div>
                    <div class="decline-modal-detail-row"><span>Time</span><strong>{{ $declineTimeLabel }}</strong></div>
                    <div class="decline-modal-detail-row"><span>Client</span><strong>{{ $declineClient }}</strong></div>
                    <div class="decline-modal-detail-row decline-modal-detail-payment">
                        <span>Payment</span><strong>{{ $declineAmountLabel }}</strong>
                    </div>
                </div>

                <div class="decline-modal-actions">
                    <button type="button" class="decline-cancel-btn" wire:click="closeDeclineModal">Cancel</button>
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
    .decline-modal-overlay {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.28);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        margin: 0;
        z-index: 100100;
        overflow: auto;
    }

    .decline-modal-card {
        width: 400px;
        border-radius: 10px;
        border: 1px solid #FF6E6E;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 1.6rem 1.7rem;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .decline-modal-close {
        position: absolute;
        top: 0.9rem;
        right: 0.9rem;
        border: none;
        background: transparent;
        cursor: pointer;
        line-height: 1;
        padding: 0.15rem;
    }

    .decline-modal-icon {
        margin-top: 0.1rem;
    }

    .decline-modal-title {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
    }

    .decline-modal-subtitle {
        color: #9D9B98;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .decline-modal-details {
        width: 100%;
        border-radius: 10px;
        background: #F1F1F1;
        padding: 1.35rem 1.7rem;
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
        line-height: normal;
    }

    .decline-modal-detail-row strong {
        text-align: right;
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .decline-modal-detail-payment {
        margin-top: 1.2rem;
        margin-bottom: 0;
    }

    .decline-modal-actions {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 0.7rem;
    }

    .decline-cancel-btn,
    .decline-confirm-btn {
        width: 150px;
        height: 42px;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 75px;
    }

    .decline-cancel-btn {
        background: #F8F8F8;
        border-color: #4D4842;
        color: #3B3731;
    }

    .decline-confirm-btn {
        background: #FF6E6E;
        color: #FFF;
        border-color: #FF6E6E;
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
</style>
