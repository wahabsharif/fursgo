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
                            d="M41.3999 61C40.011 61 38.7103 60.7363 37.4977 60.2089C36.2851 59.6815 35.2295 58.9685 34.331 58.07C33.4325 57.1715 32.7195 56.1159 32.1921 54.9033C31.6647 53.6907 31.4006 52.3896 31.3999 51C31.3992 49.6104 31.6632 48.3096 32.1921 47.0978C32.721 45.8859 33.4336 44.8304 34.3299 43.9311C35.2262 43.0319 36.2818 42.3189 37.4966 41.7922C38.7114 41.2656 40.0125 41.0015 41.3999 41C42.9184 41 44.3584 41.3241 45.7199 41.9722C47.0814 42.6204 48.234 43.537 49.1777 44.7222V43.2222C49.1777 42.9074 49.2843 42.6437 49.4977 42.4311C49.711 42.2185 49.9747 42.1119 50.2888 42.1111C50.6029 42.1104 50.8669 42.217 51.081 42.4311C51.2951 42.6452 51.4014 42.9089 51.3999 43.2222V47.6667C51.3999 47.9815 51.2932 48.2456 51.0799 48.4589C50.8666 48.6722 50.6029 48.7785 50.2888 48.7778H45.8443C45.5295 48.7778 45.2658 48.6711 45.0532 48.4578C44.8406 48.2444 44.734 47.9807 44.7332 47.6667C44.7325 47.3526 44.8392 47.0889 45.0532 46.8756C45.2673 46.6622 45.531 46.5556 45.8443 46.5556H47.7888C47.0295 45.5185 46.0943 44.7037 44.9832 44.1111C43.8721 43.5185 42.6777 43.2222 41.3999 43.2222C39.2332 43.2222 37.3955 43.977 35.8866 45.4867C34.3777 46.9963 33.6229 48.8341 33.6221 51C33.6214 53.1659 34.3762 55.0041 35.8866 56.5144C37.3969 58.0248 39.2347 58.7793 41.3999 58.7778C43.1592 58.7778 44.7332 58.25 46.1221 57.1944C47.511 56.1389 48.4277 54.7778 48.8721 53.1111C48.9647 52.8148 49.1314 52.5926 49.3721 52.4444C49.6129 52.2963 49.8814 52.2407 50.1777 52.2778C50.4925 52.3148 50.7425 52.4489 50.9277 52.68C51.1129 52.9111 51.1684 53.1659 51.0943 53.4444C50.5573 55.6481 49.3906 57.4585 47.5943 58.8756C45.798 60.2926 43.7332 61.0007 41.3999 61Z"
                            fill="#CBDCE8" />
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
                                <span>Service</span><strong>{{ $rescheduleBooking->service }}</strong></div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item"><span>Date</span><strong
                                    x-text="selectedDateLabel">{{ $rescheduleDateCard }}</strong></div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item"><span>Time</span><strong
                                    x-text="selectedTimeLabel">{{ $rescheduleTimeCard }}</strong></div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item"><span>Other</span><strong>{{ $reschedulePetMeta }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reschedule-update">
                    <h4>Update Date &amp; Time</h4>
                    <div class="reschedule-update-body">
                        <div class="reschedule-current-panel">
                            <div class="reschedule-current-block">
                                <span>Date</span>
                                <strong x-text="selectedDateLabel">{{ $rescheduleDateCard }}</strong>
                            </div>
                            <div class="reschedule-current-block">
                                <span>Time</span>
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
                            <h5>My availability</h5>
                            @foreach ($rescheduleAvailability as $slot)
                                <button type="button" @click="selectTime('{{ $slot }}')"
                                    :class="{ 'is-active': selectedTime === '{{ $slot }}' }">{{ $slot }}</button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="reschedule-actions">
                    <button type="button" class="reschedule-cancel-btn" wire:click="closeRescheduleModal">Cancel</button>
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
        background: rgba(0, 0, 0, 0.28);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.7rem;
        z-index: 100120;
        overflow: auto;
    }

    .reschedule-modal-card {
        width: min(100%, 980px);
        border-radius: 10px;
        border: 1px solid #E9B96D;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 1rem;
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
        margin-bottom: 0.8rem;
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
        margin: 0.7rem 1.2rem;
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
        gap: 3.5rem;
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
        gap: 1rem;
    }

    .reschedule-current-panel {
        height: fit-content;
        border-radius: 10px;
        background: #F6F6F6;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .reschedule-current-block span {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
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
