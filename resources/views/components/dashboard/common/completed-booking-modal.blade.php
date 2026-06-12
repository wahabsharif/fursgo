@props([
    'booking' => null,
    'closeMethod' => 'closeCompletedBookingModal',
    'loadingEvent' => null,
])

@if ($booking)
    @php
        $isSpaceUser = auth()->check() && strtolower((string) auth()->user()->user_type) === 'space';
        $completedBookingIdLabel = 'FG-' . str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT);
        $completedDateLabel = optional($booking->date)->format('d/m/Y') ?? 'N/A';
        $completedOwnerName = $booking->petOwner->name ?? 'N/A';
        $completedFirstPet = $booking->pets->first();
        $completedPetName = $completedFirstPet->name ?? 'N/A';
        $completedPetType = $completedFirstPet->pet_type ?? '';
        $completedService = $booking->service ?: 'N/A';
        $completedSpaceRaw = str_replace('_', ' ', strtolower((string) ($booking->visit_type ?? '')));
        $completedSpaceLabel =
            $completedSpaceRaw === 'home' || $completedSpaceRaw === 'home visit'
                ? 'Home Visit'
                : ($completedSpaceRaw === 'salon' || $completedSpaceRaw === 'salon visit'
                    ? 'Salon Visit'
                    : ucfirst($completedSpaceRaw ?: 'N/A'));
        $completedTimeRaw = (string) ($booking->time ?? '');
        $completedTimeLabelForSpace = trim($completedTimeRaw) !== '' ? trim($completedTimeRaw) : 'N/A';
        if (str_contains($completedTimeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $completedTimeRaw, 2);
            $start = $parts[0] ?? '';
            $end = $parts[1] ?? '';
            preg_match('/(\d{1,2}:\d{2})/', $start, $mStartOnly);
            preg_match('/(\d{1,2}:\d{2})/', $end, $mEndOnly);
            if (!empty($mStartOnly[1]) && !empty($mEndOnly[1])) {
                try {
                    $startDt = new DateTimeImmutable($mStartOnly[1]);
                    $endDt = new DateTimeImmutable($mEndOnly[1]);
                    $completedTimeLabelForSpace = $startDt->format('H:i') . ' - ' . $endDt->format('H:i');
                } catch (Throwable $e) {
                    $completedTimeLabelForSpace = trim((string) $mStartOnly[1]) . ' - ' . trim((string) $mEndOnly[1]);
                }
            }
        }
        $completedServiceTimeLabelForSpace = $completedService . ' (' . $completedTimeLabelForSpace . ')';
        $completedServiceAmount = (float) $booking->amount;
        $completedExtraAddOnsRaw = $booking->extra_add_ons;
        $completedExtraAddOns = collect(is_array($completedExtraAddOnsRaw) ? $completedExtraAddOnsRaw : [])
            ->map(function ($item) {
                return [
                    'label' => trim((string) data_get($item, 'label', '')),
                    'amount' => (float) data_get($item, 'amount', 0),
                ];
            })
            ->filter(fn($item) => $item['label'] !== '')
            ->values();
        $completedExtrasAmount = (float) $completedExtraAddOns->sum('amount');
        $completedPromoDiscount = (float) ($booking->discount ?? 0);
        $completedTotalAmount = $completedServiceAmount + $completedExtrasAmount - $completedPromoDiscount;
    @endphp
    @teleport('body')
        <div class="completed-booking-modal-overlay" wire:keydown.escape="{{ $closeMethod }}">
            <div class="completed-booking-modal-card" role="dialog" aria-modal="true"
                aria-labelledby="completed-booking-modal-title">
                <div class="completed-booking-modal-head">
                    <h3 class="completed-booking-modal-title" id="completed-booking-modal-title">Completed Booking</h3>
                    <button type="button" class="completed-booking-modal-close"
                        @if ($loadingEvent) @click="window.dispatchEvent(new CustomEvent(@js($loadingEvent)))" @endif
                        wire:click="{{ $closeMethod }}" aria-label="Close modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 36 36"
                            fill="none">
                            <circle cx="18" cy="18" r="17.5" stroke="#3B3731" />
                            <path d="M12.8 23.9998L24 12.7998M12.8 12.7998L24 23.9998" stroke="#3B3731" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="completed-booking-modal-booking-row">
                    <strong>Booking ID: {{ $completedBookingIdLabel }}</strong>
                    <div class="completed-booking-modal-booking-meta">
                        <span>{{ $completedDateLabel }}</span>
                        <button type="button" data-invoice-url="{{ route('dashboard.bookings.invoice-pdf', $booking) }}"
                            onclick="window.downloadBookingInvoicePdf(this.dataset.invoiceUrl)"
                            class="completed-booking-download-btn" aria-label="Download invoice">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="19" viewBox="0 0 16 19"
                                fill="none">
                                <path
                                    d="M0.5 15.5V17C0.5 17.3978 0.643668 17.7794 0.8994 18.0607C1.15513 18.342 1.50198 18.5 1.86364 18.5H14.1364C14.498 18.5 14.8449 18.342 15.1006 18.0607C15.3563 17.7794 15.5 17.3978 15.5 17V15.5"
                                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                <path d="M7.99997 0.5V12.875M12.0909 8.75L7.99997 13.25L3.90906 8.75" stroke="#3B3731"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="completed-booking-modal-customer">
                    <div class="completed-booking-modal-user-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="36" viewBox="0 0 32 36"
                            fill="none">
                            <ellipse cx="17.3668" cy="18.0807" rx="10.2458" ry="9.64315" fill="white" />
                            <path
                                d="M16.8932 0.202494C16.6132 0.0698256 16.3132 0 15.9998 0C15.6865 0 15.3865 0.0698256 15.1065 0.202494L2.55333 5.78156C1.08668 6.43094 -0.00663626 7.94615 3.03229e-05 9.77559C0.0333633 16.7023 2.75333 29.3756 14.2399 35.1362C15.3532 35.6949 16.6465 35.6949 17.7598 35.1362C29.2463 29.3756 31.9663 16.7023 31.9996 9.77559C32.0063 7.94615 30.913 6.43094 29.4463 5.78156L16.8932 0.202494ZM9.65991 19.9841C9.97991 20.0679 10.3199 20.1098 10.6666 20.1098C13.0199 20.1098 14.9332 18.1058 14.9332 15.6409V11.1721H17.8798C18.6865 11.1721 19.4265 11.6469 19.7865 12.408L20.2665 13.4065H24.5331C25.1197 13.4065 25.5997 13.9093 25.5997 14.5237V16.7581C25.5997 19.8444 23.2131 22.3442 20.2665 22.3442H17.0665V25.8844C17.0665 26.3941 16.6732 26.813 16.1798 26.813C16.0598 26.813 15.9398 26.7851 15.8332 26.7362L9.25325 23.7826C8.81326 23.5871 8.53326 23.1332 8.53326 22.6375C8.53326 22.4419 8.57326 22.2534 8.65993 22.0789L9.65991 19.9841ZM9.59992 11.1721H12.7999V15.6409C12.7999 16.8769 11.8466 17.8754 10.6666 17.8754C9.48658 17.8754 8.53326 16.8769 8.53326 15.6409V12.2893C8.53326 11.6748 9.01326 11.1721 9.59992 11.1721ZM18.1331 14.5237C18.1331 14.2274 18.0208 13.9433 17.8207 13.7337C17.6207 13.5242 17.3494 13.4065 17.0665 13.4065C16.7836 13.4065 16.5123 13.5242 16.3123 13.7337C16.1122 13.9433 15.9998 14.2274 15.9998 14.5237C15.9998 14.82 16.1122 15.1042 16.3123 15.3137C16.5123 15.5232 16.7836 15.6409 17.0665 15.6409C17.3494 15.6409 17.6207 15.5232 17.8207 15.3137C18.0208 15.1042 18.1331 14.82 18.1331 14.5237Z"
                                fill="#E2E2E2" />
                        </svg>
                    </div>
                    <div>
                        <p class="completed-booking-modal-owner">{{ $completedOwnerName }}</p>
                        @unless ($isSpaceUser)
                            <p class="completed-booking-modal-pet">{{ $completedPetName }}<span
                                    class="completed-booking-modal-pet-type">{{ $completedPetType }}</span></p>
                        @endunless
                    </div>
                </div>

                <div class="completed-booking-modal-section">
                    <p class="completed-booking-modal-section-label">
                        @if ($isSpaceUser)
                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13" viewBox="0 0 15 13"
                                fill="none">
                                <path
                                    d="M13.1097 12.1166V3.83417C13.1097 3.81429 13.1113 3.79482 13.1144 3.77576L10.875 1.86616C10.3988 1.46067 10.0698 1.18119 9.79071 0.998982C9.52119 0.823101 9.34008 0.766834 9.16683 0.766834C8.99372 0.766835 8.81374 0.823306 8.54452 0.998982C8.26536 1.18121 7.93548 1.46044 7.45863 1.86616L5.2177 3.77576C5.22078 3.7949 5.22398 3.81422 5.22398 3.83417V12.1166C5.22364 12.3281 5.04366 12.5 4.82168 12.5C4.59985 12.4998 4.41972 12.328 4.41938 12.1166V4.45573L4.00451 4.81069C3.83888 4.95183 3.58373 4.93709 3.43564 4.77924C3.28813 4.62148 3.30193 4.3796 3.46707 4.23856L6.92118 1.29553H6.92275C7.38366 0.90337 7.75691 0.583679 8.08879 0.366942C8.4307 0.143752 8.77013 2.24995e-07 9.16683 0C9.56348 0 9.90284 0.143743 10.2449 0.366942C10.577 0.583731 10.9518 0.903225 11.4125 1.29553L14.8666 4.23856C15.0317 4.3796 15.0455 4.62148 14.898 4.77924C14.7499 4.93709 14.4948 4.95183 14.3291 4.81069L13.9143 4.45573V12.1166C13.9139 12.328 13.7338 12.4998 13.512 12.5C13.29 12.5 13.11 12.3281 13.1097 12.1166Z"
                                    fill="#9D9B98" />
                                <path
                                    d="M1.82418 6.66737C1.82418 6.37816 1.74192 6.13002 1.62487 5.96249C1.50777 5.79507 1.37173 5.7247 1.25 5.7247C1.12833 5.7248 0.992145 5.79519 0.875132 5.96249C0.758177 6.13002 0.675818 6.37832 0.675818 6.66737C0.675926 6.95653 0.758033 7.20483 0.875132 7.37226C0.992124 7.53946 1.12837 7.60853 1.25 7.60863C1.37164 7.60863 1.50783 7.53939 1.62487 7.37226C1.74197 7.20483 1.82407 6.95653 1.82418 6.66737ZM2.5 6.66737C2.49989 7.09818 2.37897 7.50235 2.16605 7.80679C1.95294 8.11149 1.63215 8.33333 1.25 8.33333C0.868121 8.33323 0.548331 8.11124 0.335269 7.80679C0.12233 7.50234 0.000106589 7.0982 0 6.66737C0 6.23634 0.122237 5.83113 0.335269 5.52654C0.548331 5.22219 0.868196 5.0001 1.25 5C1.63209 5 1.95294 5.22191 2.16605 5.52654C2.37908 5.83113 2.5 6.23634 2.5 6.66737Z"
                                    fill="#9D9B98" />
                                <path
                                    d="M0.833008 12.1094V7.8906C0.833008 7.67488 1.01956 7.5 1.24967 7.5C1.47979 7.5 1.66634 7.67488 1.66634 7.8906V12.1094C1.66617 12.325 1.47968 12.5 1.24967 12.5C1.01966 12.5 0.833183 12.325 0.833008 12.1094Z"
                                    fill="#9D9B98" />
                                <path
                                    d="M10.6579 9.31364C10.6579 8.9734 10.6564 8.75738 10.6348 8.59906C10.6147 8.4523 10.584 8.41411 10.5654 8.39576C10.5468 8.37748 10.5083 8.34577 10.3588 8.32597C10.1978 8.30466 9.97715 8.30473 9.63096 8.30473H8.92167C8.57549 8.30473 8.35488 8.30466 8.19387 8.32597C8.04438 8.34577 8.00583 8.37748 7.98725 8.39576C7.96865 8.41411 7.93793 8.4523 7.91787 8.59906C7.89622 8.75738 7.89474 8.9734 7.89474 9.31364V11.7229H10.6579V9.31364ZM9.98715 5.42972C10.2048 5.42988 10.3816 5.60399 10.3819 5.81811C10.3819 6.03251 10.205 6.20634 9.98715 6.2065H8.56548C8.34762 6.20634 8.17074 6.03251 8.17074 5.81811C8.17108 5.60399 8.34782 5.42988 8.56548 5.42972H9.98715ZM9.98715 3.33301L10.0658 3.34059C10.246 3.37657 10.3819 3.53349 10.3819 3.7214C10.3819 3.90931 10.246 4.06623 10.0658 4.10221L9.98715 4.10979H8.56548C8.34762 4.10963 8.17074 3.9358 8.17074 3.7214C8.17074 3.507 8.34762 3.33317 8.56548 3.33301H9.98715ZM11.4474 11.7229H14.6053C14.8233 11.7229 15 11.8968 15 12.1113C14.9997 12.3255 14.8231 12.4997 14.6053 12.4997H0.394737C0.176935 12.4997 0.000332468 12.3255 0 12.1113C0 11.8968 0.17673 11.7229 0.394737 11.7229H7.10526V9.31364C7.10526 8.99552 7.10427 8.71791 7.13456 8.4959C7.16648 8.26247 7.23958 8.03308 7.42907 7.84655C7.61867 7.66 7.85172 7.58819 8.08902 7.55678C8.31486 7.52691 8.59793 7.52795 8.92167 7.52795H9.63096C9.95471 7.52795 10.2378 7.52691 10.4636 7.55678C10.7009 7.58819 10.934 7.66 11.1236 7.84655C11.313 8.03308 11.3862 8.26247 11.4181 8.4959C11.4484 8.71791 11.4474 8.99552 11.4474 9.31364V11.7229Z"
                                    fill="#9D9B98" />
                            </svg>
                            Space
                        @else
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13" viewBox="0 0 12 13"
                                fill="none">
                                <path
                                    d="M3.79507 8.7133C4.74998 9.66821 7.07244 8.89426 8.98226 6.98414C10.8924 5.07433 11.6663 2.75186 10.7114 1.79695M6.60477 1.14832L7.03699 1.58084M5.09202 2.66138L5.52423 3.09359M3.79476 4.39054L4.22698 4.82276M3.36255 6.55192L3.79476 6.98414M8.98226 0.5L9.41447 0.932215M8.55004 3.0939L9.41447 3.95833M7.03729 4.60696L7.90172 5.47139M5.30813 5.9036L6.17256 6.76803"
                                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                                <path
                                    d="M3.79466 10.0107C4.15277 9.65258 4.15277 9.07196 3.79466 8.71385C3.43655 8.35574 2.85593 8.35574 2.49782 8.71385L0.768699 10.443C0.410587 10.8011 0.410587 11.3817 0.768699 11.7398C1.12681 12.0979 1.70743 12.0979 2.06554 11.7398L3.79466 10.0107Z"
                                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            Service
                        @endif
                    </p>
                    <div class="completed-booking-modal-line">
                        <div>
                            <p>{{ $isSpaceUser ? $completedSpaceLabel : $completedService }}</p>
                            <p class="completed-booking-modal-line-sub" style="color: #9D9B98;">
                                {{ $isSpaceUser ? $completedServiceTimeLabelForSpace : $completedPetName }}</p>
                        </div>
                        <span>£{{ number_format($completedServiceAmount, 2) }}</span>
                    </div>
                </div>

                <div class="completed-booking-modal-section">
                    <p class="completed-booking-modal-section-title">Extras &amp; Add-ons</p>
                    @if ($completedExtraAddOns->isNotEmpty())
                        @foreach ($completedExtraAddOns as $addon)
                            <div class="completed-booking-modal-line completed-booking-addon-line">
                                <p class="completed-booking-modal-line-sub">{{ $addon['label'] }}</p>
                                <span>£{{ number_format((float) $addon['amount'], 2) }}</span>
                            </div>
                        @endforeach
                    @else
                        <div class="completed-booking-modal-line">
                            <p class="completed-booking-modal-line-sub">No add-ons recorded</p>
                            <span>£{{ number_format($completedExtrasAmount, 2) }}</span>
                        </div>
                    @endif
                </div>

                <div class="completed-booking-modal-total-block">
                    <div class="completed-booking-modal-total-row">
                        <span>Service:</span>
                        <span>£{{ number_format($completedServiceAmount, 2) }}</span>
                    </div>
                    <div class="completed-booking-modal-total-row">
                        <span>Extras &amp; Add-ons:</span>
                        <span>£{{ number_format($completedExtrasAmount, 2) }}</span>
                    </div>
                    <div class="completed-booking-modal-total-row">
                        <span>Promo discount:</span>
                        <span style="color: #9D9B98;">- £{{ number_format($completedPromoDiscount, 2) }}</span>
                    </div>
                    <div class="completed-booking-modal-total-row is-grand">
                        <span>Total</span>
                        <span>£{{ number_format($completedTotalAmount, 2) }}</span>
                    </div>
                </div>
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
            width: min(680px, 100%);
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
            font-family: Lato, sans-serif;
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

        .completed-booking-modal-total-block {
            padding: 1.35rem 1.65rem 1.55rem;
        }

        .completed-booking-modal-total-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            color: #9D9B98;
            font-family: Lato, sans-serif;
            font-size: 18px;
            font-weight: 400;
            line-height: 23px;
            margin-bottom: 1.7rem;
        }

        .completed-booking-modal-total-row>span:last-child {
            color: #3B3731;
            text-align: right;
            font-family: Lato, sans-serif;
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
    </style>
@endonce
