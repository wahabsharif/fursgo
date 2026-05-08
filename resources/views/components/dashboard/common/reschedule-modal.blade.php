@props([
    'rescheduleBooking' => null,
    'bookings' => collect(),
    'rescheduleSelectedDate' => null,
    'rescheduleSelectedTime' => null,
    'rescheduleCalendarMonth' => null,
    'rescheduleDurationMinutes' => 60,
])

@if ($rescheduleBooking)
    @php
        $rescheduleBookingIdLabel = 'FG-' . str_pad((string) $rescheduleBooking->id, 5, '0', STR_PAD_LEFT);
        $reschedulePet = $rescheduleBooking->pets->first();
        $reschedulePetName = $reschedulePet->name ?? 'N/A';
        $reschedulePetType = $reschedulePet->pet_type ?? 'N/A';
        $reschedulePetBreed = $reschedulePet->breed ?? null;
        $reschedulePetMeta =
            $reschedulePetType !== 'N/A'
                ? ($reschedulePetBreed
                    ? $reschedulePetType . ' (' . $reschedulePetBreed . ')'
                    : $reschedulePetType)
                : ($reschedulePetBreed ?:
                'N/A');
        $reschedulePetPhotoRaw = trim((string) ($reschedulePet->photo ?? ''));
        $reschedulePetPhoto = null;
        if ($reschedulePetPhotoRaw !== '') {
            $isAbsolute =
                str_starts_with($reschedulePetPhotoRaw, 'http://') ||
                str_starts_with($reschedulePetPhotoRaw, 'https://') ||
                str_starts_with($reschedulePetPhotoRaw, 'data:') ||
                str_starts_with($reschedulePetPhotoRaw, '/');
            $reschedulePetPhoto = $isAbsolute
                ? $reschedulePetPhotoRaw
                : asset('storage/' . ltrim($reschedulePetPhotoRaw, '/'));
        }
        $rescheduleDateObj = $rescheduleSelectedDate
            ? new DateTimeImmutable($rescheduleSelectedDate)
            : ($rescheduleBooking->date
                ? new DateTimeImmutable((string) $rescheduleBooking->date)
                : null);
        $rescheduleDateCard = $rescheduleDateObj ? $rescheduleDateObj->format('d/m/Y') : 'N/A';
        $calendarBase = $rescheduleCalendarMonth
            ? new DateTimeImmutable($rescheduleCalendarMonth)
            : ($rescheduleDateObj ?:
            new DateTimeImmutable(date('Y-m-01')));
        $rescheduleMonthTitle = $calendarBase->format('F Y');
        $rescheduleYear = (int) $calendarBase->format('Y');
        $rescheduleMonth = (int) $calendarBase->format('m');

        $bookedDaysByMonth = $bookings
            ->filter(fn($b) => $b->date && $b->booking_status !== 'cancelled')
            ->groupBy(fn($b) => date('Y-m', strtotime((string) $b->date)))
            ->map(function ($rows) {
                return $rows
                    ->map(fn($b) => (int) date('j', strtotime((string) $b->date)))
                    ->unique()
                    ->sort()
                    ->values()
                    ->all();
            })
            ->all();
        $rescheduleAvailability = ['09:00 AM', '11:00 AM', '12:00 PM', '04:00 PM', '08:00 PM'];

        $rescheduleTimeRaw = trim((string) $rescheduleBooking->time);
        $rescheduleTimeCard = $rescheduleTimeRaw !== '' ? $rescheduleTimeRaw : 'N/A';
        $rescheduleDurationLabel = '';
        if (str_contains($rescheduleTimeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $rescheduleTimeRaw, 2);
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
                    $rescheduleDurationLabel = '(' . $durationMinutes . ' minutes)';
                } catch (Throwable $e) {
                    $rescheduleDurationLabel = '';
                }
            }
        }
        if ($rescheduleSelectedTime) {
            $selectedStart = DateTime::createFromFormat('h:i A', $rescheduleSelectedTime);
            if ($selectedStart) {
                $selectedEnd = (clone $selectedStart)->modify('+' . max(1, $rescheduleDurationMinutes) . ' minutes');
                $rescheduleTimeCard = $selectedStart->format('H:i') . ' - ' . $selectedEnd->format('H:i');
            }
        }
    @endphp
    @teleport('body')
        <div class="reschedule-modal-overlay" wire:keydown.escape="closeRescheduleModal" x-data="reschedulePicker({
            initialDate: @js($rescheduleSelectedDate),
            initialTime: @js($rescheduleSelectedTime),
            initialMonth: @js(sprintf('%04d-%02d-01', $rescheduleYear, $rescheduleMonth)),
            bookedDaysByMonth: @js($bookedDaysByMonth)
        })">
            <div class="reschedule-modal-card" role="dialog" aria-modal="true" aria-labelledby="reschedule-modal-title">
                <div class="reschedule-modal-loading-bar" wire:loading.flex wire:target="confirmRescheduleBookingFromClient"
                    aria-hidden="true">
                    <span class="reschedule-modal-loading-bar__sweep"></span>
                </div>
                <button type="button" class="reschedule-modal-close" wire:click="closeRescheduleModal"
                    aria-label="Close modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"
                        fill="none">
                        <path d="M0.75 15.75L15.75 0.75M0.75 0.75L15.75 15.75" stroke="#3B3731" stroke-width="1.5"
                            stroke-linecap="round" />
                    </svg>
                </button>

                <div class="reschedule-modal-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="82" height="77" viewBox="0 0 82 77"
                        fill="none">
                        <path
                            d="M4.20495 29C4.04175 31.9412 4.04175 35.4404 4.04175 39.6442V46.2935C4.04175 58.8307 4.04544 65.0976 8.37085 68.9941C12.6963 72.8906 19.6627 72.8906 33.5919 72.8906H48.367C62.2962 72.8906 69.259 72.8873 73.5881 68.9941C77.9172 65.101 77.9172 58.8307 77.9172 46.2935V39.6442C77.9172 35.4401 77.9168 31.941 77.7531 29H4.20495Z"
                            fill="#F2F6F9" />
                        <path
                            d="M41.3999 61C40.011 61 38.7103 60.7363 37.4977 60.2089C36.2851 59.6815 35.2295 58.9685 34.331 58.07C33.4325 57.1715 32.7195 56.1159 32.1921 54.9033C31.6647 53.6907 31.4006 52.3896 31.3999 51C31.3992 49.6104 31.6632 48.3096 32.1921 47.0978C32.721 45.8859 33.4336 44.8304 34.3299 43.9311C35.2262 43.0319 36.2818 42.3189 37.4966 41.7922C38.7114 41.2656 40.0125 41.0015 41.3999 41C42.9184 41 44.3584 41.3241 45.7199 41.9722C47.0814 42.6204 48.234 43.537 49.1777 44.7222V43.2222C49.1777 42.9074 49.2843 42.6437 49.4977 42.4311C49.711 42.2185 49.9747 42.1119 50.2888 42.1111C50.6029 42.1104 50.8669 42.217 51.081 42.4311C51.2951 42.6452 51.4014 42.9089 51.3999 43.2222V47.6667C51.3999 47.9815 51.2932 48.2456 51.0799 48.4589C50.8666 48.6722 50.6029 48.7785 50.2888 48.7778H45.8443C45.5295 48.7778 45.2658 48.6711 45.0532 48.4578C44.8406 48.2444 44.734 47.9807 44.7332 47.6667C44.7325 47.3526 44.8392 47.0889 45.0532 46.8756C45.2673 46.6622 45.531 46.5556 45.8443 46.5556H47.7888C47.0295 45.5185 46.0943 44.7037 44.9832 44.1111C43.8721 43.5185 42.6777 43.2222 41.3999 43.2222C39.2332 43.2222 37.3955 43.977 35.8866 45.4867C34.3777 46.9963 33.6229 48.8341 33.6221 51C33.6214 53.1659 34.3762 55.0041 35.8866 56.5144C37.3969 58.0248 39.2347 58.7793 41.3999 58.7778C43.1592 58.7778 44.7332 58.25 46.1221 57.1944C47.511 56.1389 48.4277 54.7778 48.8721 53.1111C48.9647 52.8148 49.1314 52.5926 49.3721 52.4444C49.6129 52.2963 49.8814 52.2407 50.1777 52.2778C50.4925 52.3148 50.7425 52.4489 50.9277 52.68C51.1129 52.9111 51.1684 53.1659 51.0943 53.4444C50.5573 55.6481 49.3906 57.4585 47.5943 58.8756C45.798 60.2926 43.7332 61.0007 41.3999 61ZM42.511 50.5556L45.2888 53.3333C45.4925 53.537 45.5943 53.7963 45.5943 54.1111C45.5943 54.4259 45.4925 54.6852 45.2888 54.8889C45.0851 55.0926 44.8258 55.1944 44.511 55.1944C44.1962 55.1944 43.9369 55.0926 43.7332 54.8889L40.6221 51.7778C40.511 51.6667 40.4277 51.5419 40.3721 51.4033C40.3166 51.2648 40.2888 51.1211 40.2888 50.9722V46.5556C40.2888 46.2407 40.3955 45.977 40.6088 45.7644C40.8221 45.5519 41.0858 45.4452 41.3999 45.4444C41.714 45.4437 41.978 45.5504 42.1921 45.7644C42.4062 45.9785 42.5125 46.2422 42.511 46.5556V50.5556Z"
                            fill="#CBDCE8" />
                        <path
                            d="M1 37.3777C1 22.9384 1 15.7168 5.688 11.233C10.376 6.74919 17.916 6.74536 33 6.74536H49C64.084 6.74536 71.628 6.74536 76.312 11.233C80.996 15.7206 81 22.9384 81 37.3777V45.0358C81 59.4751 81 66.6967 76.312 71.1805C71.624 75.6643 64.084 75.6681 49 75.6681H33C17.916 75.6681 10.372 75.6681 5.688 71.1805C1.004 66.6929 1 59.4751 1 45.0358V37.3777Z"
                            stroke="#3B3731" stroke-width="2" />
                        <path d="M20.3952 6.71614V1M61.5642 6.71614V1M1.86914 25.7699H80.0902" stroke="#3B3731"
                            stroke-width="2" stroke-linecap="round" />
                    </svg>
                </div>
                <h3 class="reschedule-modal-title" id="reschedule-modal-title"><strong>Reschedule</strong> Booking</h3>
                <p class="reschedule-modal-subtitle">Are you sure you want to reschedule this booking?</p>

                <div class="reschedule-summary">
                    <div class="reschedule-pet-avatar">
                        <img src="{{ $reschedulePetPhoto }}" alt="{{ $reschedulePetName }}">
                    </div>
                    <div>
                        <div class="reschedule-summary-header">
                            <span class="reschedule-home-chip">Home Visits</span>
                            <span class="reschedule-booking-id">Booking ID: {{ $rescheduleBookingIdLabel }}</span>
                        </div>
                        <div class="reschedule-summary-grid">
                            <div class="reschedule-summary-item">
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="17"
                                        viewBox="0 0 16 17" fill="none">
                                        <path
                                            d="M4.94591 11.5544C6.23114 12.8397 9.35699 11.798 11.9274 9.22713C14.4983 6.65667 15.54 3.53082 14.2548 2.24559M8.72754 1.37259L9.30927 1.95473M6.6915 3.40904L7.27322 3.99077M4.9455 5.73636L5.52722 6.31809M4.36377 8.6454L4.9455 9.22713M11.9274 0.5L12.5092 1.08173M11.3457 3.99118L12.5092 5.15463M9.30968 6.02763L10.4731 7.19109M6.98236 7.77281L8.14581 8.93627"
                                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        <path
                                            d="M4.94547 13.2998C5.42747 12.8178 5.42747 12.0364 4.94548 11.5544C4.46348 11.0724 3.68202 11.0724 3.20003 11.5544L0.872775 13.8816C0.390784 14.3636 0.390784 15.1451 0.872775 15.6271C1.35477 16.1091 2.13623 16.1091 2.61822 15.6271L4.94547 13.2998Z"
                                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>Service</span><strong>{{ $rescheduleBooking->service }}</strong>
                            </div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item"><span><svg xmlns="http://www.w3.org/2000/svg"
                                        width="19" height="17" viewBox="0 0 19 17" fill="none">
                                        <path
                                            d="M0.5 8.29554C0.5 5.20139 0.5 3.6539 1.50457 2.69308C2.50914 1.73227 4.12486 1.73145 7.35714 1.73145H10.7857C14.018 1.73145 15.6346 1.73145 16.6383 2.69308C17.642 3.65472 17.6429 5.20139 17.6429 8.29554V9.93656C17.6429 13.0307 17.6429 14.5782 16.6383 15.539C15.6337 16.4998 14.018 16.5007 10.7857 16.5007H7.35714C4.12486 16.5007 2.50829 16.5007 1.50457 15.539C0.500857 14.5774 0.5 13.0307 0.5 9.93656V8.29554Z"
                                            stroke="#3B3731" />
                                        <path d="M4.78585 1.73077V0.5M13.3573 1.73077V0.5M0.928711 5.83333H17.2144"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M14.2139 12.3975C14.2139 12.6151 14.1236 12.8238 13.9629 12.9777C13.8021 13.1315 13.5841 13.218 13.3568 13.218C13.1295 13.218 12.9114 13.1315 12.7507 12.9777C12.59 12.8238 12.4997 12.6151 12.4997 12.3975C12.4997 12.1799 12.59 11.9712 12.7507 11.8173C12.9114 11.6634 13.1295 11.577 13.3568 11.577C13.5841 11.577 13.8021 11.6634 13.9629 11.8173C14.1236 11.9712 14.2139 12.1799 14.2139 12.3975ZM14.2139 9.11543C14.2139 9.33305 14.1236 9.54175 13.9629 9.69562C13.8021 9.8495 13.5841 9.93595 13.3568 9.93595C13.1295 9.93595 12.9114 9.8495 12.7507 9.69562C12.59 9.54175 12.4997 9.33305 12.4997 9.11543C12.4997 8.89782 12.59 8.68912 12.7507 8.53524C12.9114 8.38137 13.1295 8.29492 13.3568 8.29492C13.5841 8.29492 13.8021 8.38137 13.9629 8.53524C14.1236 8.68912 14.2139 8.89782 14.2139 9.11543ZM9.92822 12.3975C9.92822 12.6151 9.83792 12.8238 9.67717 12.9777C9.51643 13.1315 9.29841 13.218 9.07108 13.218C8.84375 13.218 8.62573 13.1315 8.46499 12.9777C8.30424 12.8238 8.21394 12.6151 8.21394 12.3975C8.21394 12.1799 8.30424 11.9712 8.46499 11.8173C8.62573 11.6634 8.84375 11.577 9.07108 11.577C9.29841 11.577 9.51643 11.6634 9.67717 11.8173C9.83792 11.9712 9.92822 12.1799 9.92822 12.3975ZM9.92822 9.11543C9.92822 9.33305 9.83792 9.54175 9.67717 9.69562C9.51643 9.8495 9.29841 9.93595 9.07108 9.93595C8.84375 9.93595 8.62573 9.8495 8.46499 9.69562C8.30424 9.54175 8.21394 9.33305 8.21394 9.11543C8.21394 8.89782 8.30424 8.68912 8.46499 8.53524C8.62573 8.38137 8.84375 8.29492 9.07108 8.29492C9.29841 8.29492 9.51643 8.38137 9.67717 8.53524C9.83792 8.68912 9.92822 8.89782 9.92822 9.11543ZM5.64251 12.3975C5.64251 12.6151 5.5522 12.8238 5.39146 12.9777C5.23071 13.1315 5.01269 13.218 4.78537 13.218C4.55804 13.218 4.34002 13.1315 4.17927 12.9777C4.01853 12.8238 3.92822 12.6151 3.92822 12.3975C3.92822 12.1799 4.01853 11.9712 4.17927 11.8173C4.34002 11.6634 4.55804 11.577 4.78537 11.577C5.01269 11.577 5.23071 11.6634 5.39146 11.8173C5.5522 11.9712 5.64251 12.1799 5.64251 12.3975ZM5.64251 9.11543C5.64251 9.33305 5.5522 9.54175 5.39146 9.69562C5.23071 9.8495 5.01269 9.93595 4.78537 9.93595C4.55804 9.93595 4.34002 9.8495 4.17927 9.69562C4.01853 9.54175 3.92822 9.33305 3.92822 9.11543C3.92822 8.89782 4.01853 8.68912 4.17927 8.53524C4.34002 8.38137 4.55804 8.29492 4.78537 8.29492C5.01269 8.29492 5.23071 8.38137 5.39146 8.53524C5.5522 8.68912 5.64251 8.89782 5.64251 9.11543Z"
                                            fill="#3B3731" />
                                    </svg>Date</span><strong x-text="selectedDateLabel">{{ $rescheduleDateCard }}</strong>
                            </div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item"><span> <svg width="16" height="16"
                                        viewBox="0 0 16 16" fill="none">
                                        <circle cx="8" cy="8" r="6" stroke="#3B3731" stroke-width="1.5" />
                                        <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>Time</span><strong x-text="selectedTimeLabel">{{ $rescheduleTimeCard }}</strong>
                            </div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item"><span><svg xmlns="http://www.w3.org/2000/svg"
                                        width="18" height="17" viewBox="0 0 18 17" fill="none">
                                        <path
                                            d="M9 6.81579C6.43624 6.81579 4.26995 9.14463 3.57052 12.1358C3.2629 13.4512 3.72676 14.8474 4.86536 15.5034C5.76798 16.0234 7.11057 16.5 9 16.5C10.8894 16.5 12.2324 16.0234 13.135 15.5034C14.2736 14.8474 14.7371 13.4512 14.4295 12.1358C13.73 9.14421 11.5638 6.81579 9 6.81579ZM0.5 6.16063C0.5 7.32358 1.22452 8.5 2.11905 8.5C3.01357 8.5 3.7381 7.32358 3.7381 6.16063C3.7381 4.99768 3.01357 4.28947 2.11905 4.28947C1.22452 4.28947 0.5 4.99811 0.5 6.16063ZM17.5 6.16063C17.5 7.32358 16.7755 8.5 15.881 8.5C14.9864 8.5 14.2619 7.32358 14.2619 6.16063C14.2619 4.99768 14.9864 4.28947 15.881 4.28947C16.7755 4.28947 17.5 4.99811 17.5 6.16063ZM4.75 2.37116C4.75 3.53411 5.47452 4.71053 6.36905 4.71053C7.26357 4.71053 7.9881 3.53411 7.9881 2.37116C7.9881 1.20821 7.26357 0.5 6.36905 0.5C5.47452 0.5 4.75 1.20863 4.75 2.37116ZM13.25 2.37116C13.25 3.53411 12.5255 4.71053 11.631 4.71053C10.7364 4.71053 10.0119 3.53411 10.0119 2.37116C10.0119 1.20821 10.7364 0.5 11.631 0.5C12.5255 0.5 13.25 1.20863 13.25 2.37116Z"
                                            stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>Other</span><strong>{{ $reschedulePetMeta }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reschedule-update">
                    <h4>Update Date &amp; Time</h4>
                    <div class="reschedule-update-body">
                        <div class="reschedule-current-panel">
                            <div class="reschedule-current-block">
                                <span><svg xmlns="http://www.w3.org/2000/svg" width="19" height="17"
                                        viewBox="0 0 19 17" fill="none">
                                        <path
                                            d="M0.5 8.29554C0.5 5.20139 0.5 3.6539 1.50457 2.69308C2.50914 1.73227 4.12486 1.73145 7.35714 1.73145H10.7857C14.018 1.73145 15.6346 1.73145 16.6383 2.69308C17.642 3.65472 17.6429 5.20139 17.6429 8.29554V9.93656C17.6429 13.0307 17.6429 14.5782 16.6383 15.539C15.6337 16.4998 14.018 16.5007 10.7857 16.5007H7.35714C4.12486 16.5007 2.50829 16.5007 1.50457 15.539C0.500857 14.5774 0.5 13.0307 0.5 9.93656V8.29554Z"
                                            stroke="#3B3731" />
                                        <path d="M4.78561 1.73077V0.5M13.357 1.73077V0.5M0.928467 5.83333H17.2142"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M14.2139 12.3975C14.2139 12.6151 14.1236 12.8238 13.9629 12.9777C13.8021 13.1315 13.5841 13.218 13.3568 13.218C13.1295 13.218 12.9114 13.1315 12.7507 12.9777C12.59 12.8238 12.4997 12.6151 12.4997 12.3975C12.4997 12.1799 12.59 11.9712 12.7507 11.8173C12.9114 11.6634 13.1295 11.577 13.3568 11.577C13.5841 11.577 13.8021 11.6634 13.9629 11.8173C14.1236 11.9712 14.2139 12.1799 14.2139 12.3975ZM14.2139 9.11543C14.2139 9.33305 14.1236 9.54175 13.9629 9.69562C13.8021 9.8495 13.5841 9.93595 13.3568 9.93595C13.1295 9.93595 12.9114 9.8495 12.7507 9.69562C12.59 9.54175 12.4997 9.33305 12.4997 9.11543C12.4997 8.89782 12.59 8.68912 12.7507 8.53524C12.9114 8.38137 13.1295 8.29492 13.3568 8.29492C13.5841 8.29492 13.8021 8.38137 13.9629 8.53524C14.1236 8.68912 14.2139 8.89782 14.2139 9.11543ZM9.92822 12.3975C9.92822 12.6151 9.83792 12.8238 9.67717 12.9777C9.51643 13.1315 9.29841 13.218 9.07108 13.218C8.84375 13.218 8.62573 13.1315 8.46499 12.9777C8.30424 12.8238 8.21394 12.6151 8.21394 12.3975C8.21394 12.1799 8.30424 11.9712 8.46499 11.8173C8.62573 11.6634 8.84375 11.577 9.07108 11.577C9.29841 11.577 9.51643 11.6634 9.67717 11.8173C9.83792 11.9712 9.92822 12.1799 9.92822 12.3975ZM9.92822 9.11543C9.92822 9.33305 9.83792 9.54175 9.67717 9.69562C9.51643 9.8495 9.29841 9.93595 9.07108 9.93595C8.84375 9.93595 8.62573 9.8495 8.46499 9.69562C8.30424 9.54175 8.21394 9.33305 8.21394 9.11543C8.21394 8.89782 8.30424 8.68912 8.46499 8.53524C8.62573 8.38137 8.84375 8.29492 9.07108 8.29492C9.29841 8.29492 9.51643 8.38137 9.67717 8.53524C9.83792 8.68912 9.92822 8.89782 9.92822 9.11543ZM5.64251 12.3975C5.64251 12.6151 5.5522 12.8238 5.39146 12.9777C5.23071 13.1315 5.01269 13.218 4.78537 13.218C4.55804 13.218 4.34002 13.1315 4.17927 12.9777C4.01853 12.8238 3.92822 12.6151 3.92822 12.3975C3.92822 12.1799 4.01853 11.9712 4.17927 11.8173C4.34002 11.6634 4.55804 11.577 4.78537 11.577C5.01269 11.577 5.23071 11.6634 5.39146 11.8173C5.5522 11.9712 5.64251 12.1799 5.64251 12.3975ZM5.64251 9.11543C5.64251 9.33305 5.5522 9.54175 5.39146 9.69562C5.23071 9.8495 5.01269 9.93595 4.78537 9.93595C4.55804 9.93595 4.34002 9.8495 4.17927 9.69562C4.01853 9.54175 3.92822 9.33305 3.92822 9.11543C3.92822 8.89782 4.01853 8.68912 4.17927 8.53524C4.34002 8.38137 4.55804 8.29492 4.78537 8.29492C5.01269 8.29492 5.23071 8.38137 5.39146 8.53524C5.5522 8.68912 5.64251 8.89782 5.64251 9.11543Z"
                                            fill="#3B3731" />
                                    </svg>Date</span>
                                <strong x-text="selectedDateLabel">{{ $rescheduleDateCard }}</strong>
                            </div>
                            <div class="reschedule-current-block">
                                <span><svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <circle cx="8" cy="8" r="6" stroke="#3B3731"
                                            stroke-width="1.5" />
                                        <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>Time</span>
                                <strong x-text="selectedTimeLabel">{{ $rescheduleTimeCard }}</strong>
                                @if ($rescheduleDurationLabel)
                                    <small>{{ $rescheduleDurationLabel }}</small>
                                @endif
                            </div>
                        </div>

                        <div class="reschedule-calendar-panel">
                            <div class="reschedule-calendar-head">
                                <button type="button" @click="prevMonth()" aria-label="Previous month">&#10094;</button>
                                <strong x-text="monthTitle">{{ $rescheduleMonthTitle }}</strong>
                                <button type="button" @click="nextMonth()" aria-label="Next month">&#10095;</button>
                            </div>
                            <div class="reschedule-weekdays">
                                <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
                            </div>
                            <div class="reschedule-days-grid">
                                <template x-for="blank in prefixBlank" :key="'blank-' + blank"><span
                                        class="is-empty"></span></template>
                                <template x-for="day in daysInMonth" :key="'day-' + day">
                                    <button type="button" @click="selectDay(day)"
                                        :class="{ 'is-booked': isBooked(day), 'is-selected': day === selectedDay }"
                                        x-text="day"></button>
                                </template>
                            </div>
                        </div>

                        <div class="reschedule-availability-panel">
                            <h5><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                    viewBox="0 0 18 18" fill="none">
                                    <path
                                        d="M9 0C4.05 0 0 4.05 0 9C0 13.95 4.05 18 9 18C13.95 18 18 13.95 18 9C18 4.05 13.95 0 9 0ZM7.2 13.5L2.7 9L3.969 7.731L7.2 10.953L14.031 4.122L15.3 5.4L7.2 13.5Z"
                                        fill="#D8E8B7" />
                                </svg>My availability</h5>
                            @foreach ($rescheduleAvailability as $slot)
                                <button type="button" @click="selectTime('{{ $slot }}')"
                                    :class="{ 'is-active': selectedTime === '{{ $slot }}' }">{{ $slot }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="reschedule-actions">
                    <button type="button" class="reschedule-cancel-btn"
                        wire:click="closeRescheduleModal">Cancel</button>
                    <button type="button" class="reschedule-confirm-btn"
                        @click="$wire.confirmRescheduleBookingFromClient(selectedDate, selectedTime)"
                        wire:loading.attr="disabled" wire:target="confirmRescheduleBookingFromClient">
                        <span wire:loading.remove wire:target="confirmRescheduleBookingFromClient">Reschedule</span>
                        <span class="reschedule-btn-loading" wire:loading.inline-flex
                            wire:target="confirmRescheduleBookingFromClient">
                            <span class="reschedule-btn-spinner" aria-hidden="true"></span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endteleport
@endif

<style>
    .reschedule-modal-overlay {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.10);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.7rem;
        z-index: 100120;
        overflow: auto;
    }

    .reschedule-modal-card {
        width: min(100%, 850px);
        border-radius: 10px;
        border: 1px solid #E9B96D;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 2.5rem 1rem 1rem 1rem;
        position: relative;
        overflow: hidden;
    }

    .reschedule-modal-loading-bar {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 4px;
        display: none;
        overflow: hidden;
        z-index: 3;
        background: rgba(232, 228, 222, 0.85);
    }

    .reschedule-modal-loading-bar__sweep {
        position: absolute;
        top: 0;
        left: -42%;
        width: 42%;
        height: 100%;
        background: linear-gradient(90deg, #FFC97A 0%, #f6a623 45%, #FFC97A 100%);
        box-shadow: 0 0 12px rgba(246, 166, 35, 0.45);
        animation: reschedule-modal-load-sweep 1.1s linear infinite;
    }

    @keyframes reschedule-modal-load-sweep {
        0% {
            left: -42%;
        }

        100% {
            left: 100%;
        }
    }

    .reschedule-modal-close {
        position: absolute;
        right: 0.8rem;
        top: 0.8rem;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .reschedule-modal-icon,
    .reschedule-modal-title,
    .reschedule-modal-subtitle {
        text-align: center;
    }

    .reschedule-modal-title>strong {
        font-weight: 800;
    }

    .reschedule-modal-title {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-modal-subtitle {
        color: #9D9B98;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin: 0.8rem 0;
    }

    .reschedule-summary {
        display: flex;
        align-items: center;
        gap: 1rem;
        border-radius: 10px;
        background: #F8F8F8;
        padding: 2rem;
    }

    .reschedule-summary>div:nth-child(2) {
        width: 100%;
    }

    .reschedule-summary-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0.7rem 0;
    }

    .reschedule-home-chip {
        border-radius: 999px;
        background: #F2C273;
        color: #FFF;
        font-family: Lato;
        font-size: 13px;
        padding: 0.25rem 0.65rem;
    }

    .reschedule-booking-id {
        color: #3B3731;
        text-align: right;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .reschedule-summary-grid {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 2.5rem;
    }

    .reschedule-pet-avatar {
        width: 80px;
        height: auto;
        aspect-ratio: 1/1;
        border-radius: 999px;
        border: 2px solid #FFC97A;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #3B3731;
        font-weight: 700;
        font-family: Lato;
        overflow: hidden;
    }

    .reschedule-pet-avatar img {
        width: 90%;
        height: 90%;
        object-fit: cover;
        display: block;
        border-radius: 999px;
    }

    .reschedule-summary-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .reschedule-summary-divider {
        width: 1px;
        height: 52px;
        background: #D8D8D8;
        flex: 0 0 auto;
    }

    .reschedule-summary-item span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-summary-item strong {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .reschedule-update {
        border: 1px solid #D5D5D5;
        border-radius: 10px;
        margin-top: 0.8rem;
        padding: 1rem;
    }

    .reschedule-update h4 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
        margin-bottom: 0.6rem;
    }

    .reschedule-update-body {
        display: grid;
        grid-template-columns: 200px 1fr 220px;
        gap: 2.5rem;
    }

    .reschedule-current-panel {
        height: fit-content;
        border-radius: 10px;
        background: #F6F6F6;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 2.2rem;
    }

    .reschedule-current-block span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 0.5rem;
    }

    .reschedule-current-block strong {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .reschedule-current-block small {
        margin-top: 0.5rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .reschedule-calendar-head {
        border-radius: 999px;
        background: #F6F6F6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.35rem;
    }

    .reschedule-calendar-head button {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        border: none;
        background: #FFF;
        box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.03);
        color: #8C8C8C;
        cursor: pointer;
    }

    .reschedule-calendar-head strong {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-weekdays,
    .reschedule-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.35rem;
        margin-top: 0.6rem;
    }

    .reschedule-weekdays span {
        text-align: center;
        color: #9C9790;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-days-grid button {
        width: 41.538px;
        height: 41.538px;
        aspect-ratio: 1/1;
        margin: 0 auto;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #F2F2F2;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border: none;
        cursor: pointer;
    }

    .reschedule-days-grid span.is-empty {
        background: transparent;
    }

    .reschedule-days-grid button.is-selected {
        border-radius: 10px;
        border: 1px solid #91A36C !important;
        background: #D8E8B7;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.20);
    }

    .reschedule-days-grid button.is-booked {
        border: none;
        background: #ECF4DB;
    }

    .reschedule-availability-panel h5 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #BACF8E;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 0.6rem;
    }

    .reschedule-availability-panel button {
        width: 170px;
        height: 48px;
        border-radius: 999px;
        border: 1px solid #B4CB7E;
        background: #FFF;
        color: #BACF8E;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        margin-bottom: 0.55rem;
        cursor: pointer;
    }

    .reschedule-availability-panel button.is-active {
        background: #C9DDA0;
        box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.05);
        color: #fff;
    }

    .reschedule-actions {
        margin-top: 1.5rem;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }

    .reschedule-cancel-btn,
    .reschedule-confirm-btn {
        width: 240px;
        height: 42px;
        border-radius: 75px;
        font-family: Lato;
        font-size: 16px;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .reschedule-cancel-btn {
        border-color: #4D4842;
        background: #fff;
        color: #3B3731;
    }

    .reschedule-confirm-btn {
        background: #F2C273;
        border-color: #F2C273;
        color: #FFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
    }

    .reschedule-confirm-btn[disabled] {
        cursor: not-allowed;
        opacity: 0.9;
    }

    .reschedule-btn-loading {
        align-items: center;
        justify-content: center;
    }

    .reschedule-btn-spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.55);
        border-top-color: #FFF;
        border-radius: 50%;
        animation: reschedule-btn-spin 0.8s linear infinite;
        flex: 0 0 auto;
    }

    @keyframes reschedule-btn-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
