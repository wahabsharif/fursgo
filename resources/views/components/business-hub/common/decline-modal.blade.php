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
                    <svg xmlns="http://www.w3.org/2000/svg" width="80" height="75" viewBox="0 0 80 75"
                        fill="none">
                        <path
                            d="M3.92636 28.2354C3.76636 31.1189 3.76636 34.5495 3.76636 38.6709V45.1898C3.76636 57.4811 3.76998 63.6252 8.01057 67.4453C12.2512 71.2653 19.081 71.2653 32.7371 71.2653H47.2225C60.8786 71.2653 67.7048 71.2621 71.9491 67.4453C76.1933 63.6285 76.1933 57.4811 76.1933 45.1898V38.6709C76.1933 34.5492 76.1929 31.1187 76.0324 28.2354H3.92636Z"
                            fill="#FFEDED" />
                        <path
                            d="M78.4314 36.4483C78.4314 29.3475 78.4285 24.1046 77.8631 20.0834C77.3012 16.0884 76.1974 13.4147 74.0771 11.3832C71.9522 9.34738 69.1454 8.28455 64.9548 7.74501C60.745 7.203 55.2585 7.2012 47.8431 7.2012H32.1569C24.7416 7.2012 19.256 7.20331 15.0467 7.74577C10.8566 8.28577 8.04924 9.34877 5.92218 11.3832C3.80034 13.4127 2.69679 16.0863 2.13542 20.0819C1.5704 24.1035 1.56863 29.3474 1.56863 36.4483V43.9567C1.56863 51.0577 1.57141 56.3011 2.13695 60.3224C2.69882 64.3173 3.80257 66.9911 5.92295 69.0226C8.04782 71.0583 10.8546 72.1212 15.0452 72.6608C19.255 73.2028 24.7415 73.2038 32.1569 73.2038H47.8431C55.2584 73.2038 60.744 73.2017 64.9533 72.6592C69.1434 72.1192 71.9508 71.0562 74.0778 69.0218C76.1996 66.9924 77.3032 64.3192 77.8646 60.3239C78.4296 56.3022 78.4314 51.0579 78.4314 43.9567V36.4483ZM80 43.9567C80 51.0114 80.0018 56.3853 79.4179 60.5414C78.8303 64.7238 77.6358 67.789 75.1616 70.1554C72.6927 72.5168 69.5058 73.654 65.154 74.2148C60.8211 74.7732 55.2161 74.7724 47.8431 74.7724H32.1569C24.7842 74.7724 19.1786 74.7742 14.8453 74.2164C10.4928 73.656 7.30489 72.5185 4.83762 70.1546C2.36609 67.7866 1.17177 64.7224 0.58364 60.5407C-0.000812774 56.3849 5.42728e-08 51.0117 6.88498e-08 43.9567V36.4483C1.55774e-08 29.3936 -0.00180258 24.0197 0.582108 19.8636C1.16974 15.6812 2.36421 12.616 4.83839 10.2496C7.30734 7.88825 10.4942 6.75103 14.846 6.19017C19.1789 5.63178 24.7839 5.63257 32.1569 5.63257H47.8431C55.2158 5.63257 60.8214 5.63078 65.1547 6.18863C69.5072 6.74901 72.6951 7.88654 75.1624 10.2504C77.6339 12.6184 78.8282 15.6826 79.4164 19.8643C80.0008 24.0201 80 29.3934 80 36.4483V43.9567Z"
                            fill="#3B3731" />
                        <path
                            d="M78.3237 24.2846C78.7569 24.2846 79.108 24.6358 79.108 25.0689C79.1078 25.5019 78.7567 25.8532 78.3237 25.8532H1.63636C1.20332 25.8532 0.85225 25.5019 0.852051 25.0689C0.852051 24.6358 1.2032 24.2846 1.63636 24.2846H78.3237ZM19.0146 6.38863V0.784314C19.0146 0.351235 19.3659 0.000139025 19.7989 0C20.2321 0 20.5832 0.351149 20.5832 0.784314V6.38863C20.5831 6.82168 20.232 7.17295 19.7989 7.17295C19.3659 7.17281 19.0147 6.82159 19.0146 6.38863ZM59.3769 6.38863V0.784314C59.3769 0.351149 59.728 0 60.1612 0C60.5942 0.000148722 60.9455 0.351241 60.9455 0.784314V6.38863C60.9454 6.82159 60.5941 7.1728 60.1612 7.17295C59.7281 7.17295 59.377 6.82168 59.3769 6.38863Z"
                            fill="#3B3731" />
                        <path
                            d="M48.2354 49.8039C48.2354 45.4723 44.7239 41.9608 40.3923 41.9608C36.0607 41.9608 32.5492 45.4723 32.5492 49.8039C32.5492 54.1356 36.0607 57.6471 40.3923 57.6471V59.6078C34.9777 59.6078 30.5884 55.2185 30.5884 49.8039C30.5884 44.3894 34.9777 40 40.3923 40C45.8069 40 50.1962 44.3894 50.1962 49.8039C50.1962 55.2185 45.8069 59.6078 40.3923 59.6078V57.6471C44.7239 57.6471 48.2354 54.1356 48.2354 49.8039Z"
                            fill="#FF6E6E" />
                        <path
                            d="M42.9666 46.2784C43.3495 45.8955 43.9709 45.8955 44.3537 46.2784C44.7363 46.6612 44.7363 47.2819 44.3537 47.6647L41.9962 50.0215L44.3537 52.3782C44.7365 52.7611 44.7366 53.3825 44.3537 53.7653C43.9709 54.1482 43.3495 54.1482 42.9666 53.7653L40.6098 51.4078L38.2531 53.7653C37.8703 54.1479 37.2496 54.1479 36.8667 53.7653C36.4839 53.3825 36.4839 52.7611 36.8667 52.3782L39.2227 50.0215L36.8667 47.6647C36.4839 47.2818 36.4839 46.6612 36.8667 46.2784C37.2496 45.8955 37.8702 45.8955 38.2531 46.2784L40.6098 48.6344L42.9666 46.2784Z"
                            fill="#FF6E6E" />
                    </svg>
                </div>
                <h3 class="decline-modal-title" id="decline-modal-title">Decline Booking Request</h3>
                <p class="decline-modal-subtitle">Are you sure you want to decline <br />this booking request?</p>

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
        background: rgba(0, 0, 0, 0.10);
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
        background: #F8F8F8;
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
