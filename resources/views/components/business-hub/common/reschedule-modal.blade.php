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
        $originalDateObj = $rescheduleBooking->date
            ? new DateTimeImmutable((string) $rescheduleBooking->date)
            : null;
        $rescheduleDateCard = $originalDateObj ? $originalDateObj->format('d/m/Y') : 'N/A';
        $rescheduleDateObj = $rescheduleSelectedDate
            ? new DateTimeImmutable($rescheduleSelectedDate)
            : ($originalDateObj ?:
            null);
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
        $rescheduleAvailability = ['09:00 AM', '11:00 AM', '12:00 PM', '16:00 PM', '20:00 PM'];

        $visitRaw = strtolower(str_replace('_', ' ', (string) ($rescheduleBooking->visit_type ?? '')));
        $rescheduleVisitChip = match (true) {
            str_contains($visitRaw, 'home') => 'Home Visits',
            str_contains($visitRaw, 'salon') => 'Salon Visits',
            $visitRaw !== '' => ucwords($visitRaw),
            default => 'Home Visits',
        };

        $rescheduleTimeRaw = trim((string) $rescheduleBooking->time);
        $rescheduleTimeCard = $rescheduleTimeRaw !== '' ? $rescheduleTimeRaw : 'N/A';
        if (str_contains($rescheduleTimeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $rescheduleTimeRaw, 2);
            preg_match('/(\d{1,2}:\d{2})/', $parts[0] ?? '', $mStart);
            preg_match('/(\d{1,2}:\d{2})/', $parts[1] ?? '', $mEnd);
            if (!empty($mStart[1]) && !empty($mEnd[1])) {
                try {
                    $startDt = new DateTimeImmutable($mStart[1]);
                    $endDt = new DateTimeImmutable($mEnd[1]);
                    $rescheduleTimeCard = $startDt->format('H:i') . ' - ' . $endDt->format('H:i');
                } catch (Throwable $e) {
                    $rescheduleTimeCard = $rescheduleTimeRaw;
                }
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
                    <img src="{{ asset('images/business-hub/icon-decline-close.svg') }}" alt="" width="14.5" height="14.5">
                </button>

                <div class="reschedule-modal-icon" aria-hidden="true">
                    <img src="{{ asset('images/business-hub/icon-reschedule-calendar.svg') }}" alt="" width="54.57"
                        height="51">
                </div>
                <h3 class="reschedule-modal-title" id="reschedule-modal-title"><strong>Reschedule</strong> Booking</h3>
                <p class="reschedule-modal-subtitle">Pick a new date and time from your availability. The client will be
                    asked to approve the change.</p>

                <div class="reschedule-summary">
                    <div class="reschedule-pet-avatar">
                        <img src="{{ asset('images/business-hub/icon-reschedule-avatar-ring.svg') }}" alt="" width="95"
                            height="95" class="reschedule-pet-avatar-ring">
                        @if ($reschedulePetPhoto)
                            <img src="{{ $reschedulePetPhoto }}" alt="{{ $reschedulePetName }}" width="85" height="85"
                                class="reschedule-pet-photo">
                        @else
                            <span class="reschedule-pet-photo is-fallback">{{ strtoupper(substr((string) $reschedulePetName, 0, 1)) }}</span>
                        @endif
                    </div>
                    <div class="reschedule-summary-body">
                        <div class="reschedule-summary-header">
                            <span class="reschedule-home-chip">{{ $rescheduleVisitChip }}</span>
                            <span class="reschedule-booking-id">Booking ID: {{ $rescheduleBookingIdLabel }}</span>
                        </div>
                        <div class="reschedule-summary-grid">
                            <div class="reschedule-summary-item">
                                <span>
                                    <img src="{{ asset('images/business-hub/icon-reschedule-service.svg') }}" alt=""
                                        width="15.3" height="16.5" class="reschedule-icon-service">
                                    Service
                                </span>
                                <strong>{{ $rescheduleBooking->service }}</strong>
                            </div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item">
                                <span>
                                    <img src="{{ asset('images/business-hub/icon-reschedule-date.svg') }}" alt=""
                                        width="18.14" height="17" class="reschedule-icon-date">
                                    Date
                                </span>
                                <strong>{{ $rescheduleDateCard }}</strong>
                            </div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item">
                                <span>
                                    <img src="{{ asset('images/business-hub/icon-reschedule-time.svg') }}" alt=""
                                        width="16" height="16" class="reschedule-icon-time">
                                    Time
                                </span>
                                <strong>{{ $rescheduleTimeCard }}</strong>
                            </div>
                            <div class="reschedule-summary-divider" aria-hidden="true"></div>
                            <div class="reschedule-summary-item">
                                <span>
                                    <img src="{{ asset('images/business-hub/icon-reschedule-other.svg') }}" alt=""
                                        width="18" height="17" class="reschedule-icon-other">
                                    Other
                                </span>
                                <strong>{{ $reschedulePetMeta }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="reschedule-update-body">
                    <div class="reschedule-calendar-panel">
                        <div class="reschedule-calendar-head">
                            <button type="button" class="reschedule-month-nav" @click="prevMonth()"
                                aria-label="Previous month">
                                <img src="{{ asset('images/business-hub/icon-reschedule-prev.svg') }}" alt=""
                                    width="40" height="40">
                            </button>
                            <strong x-text="monthTitle">{{ $rescheduleMonthTitle }}</strong>
                            <button type="button" class="reschedule-month-nav" @click="nextMonth()"
                                aria-label="Next month">
                                <img src="{{ asset('images/business-hub/icon-reschedule-next.svg') }}" alt=""
                                    width="40" height="40">
                            </button>
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
                        <h5>
                            <img src="{{ asset('images/business-hub/icon-reschedule-check.svg') }}" alt="" width="18"
                                height="18">
                            My availability
                        </h5>
                        @foreach ($rescheduleAvailability as $slot)
                            <button type="button" @click="selectTime('{{ $slot }}')"
                                :class="{ 'is-active': selectedTime === '{{ $slot }}' }">{{ $slot }}</button>
                        @endforeach
                    </div>
                </div>

                <p class="reschedule-new-appointment">
                    <span>New appointment:</span>
                    <strong x-text="newAppointmentLabel"></strong>
                </p>

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
        background: rgba(59, 55, 49, 0.10);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.7rem;
        z-index: 100120;
        overflow: auto;
    }

    .reschedule-modal-card {
        width: min(100%, 820px);
        border-radius: 10px;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 20px;
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
        right: 20px;
        top: 20px;
        width: 14.5px;
        height: 14.5px;
        padding: 0;
        border: none;
        background: transparent;
        cursor: pointer;
        z-index: 2;
    }

    .reschedule-modal-close img {
        display: block;
        width: 14.5px;
        height: 14.5px;
    }

    .reschedule-modal-icon,
    .reschedule-modal-title,
    .reschedule-modal-subtitle {
        text-align: center;
    }

    .reschedule-modal-icon {
        margin: 0 auto;
        width: 54.57px;
        height: 51px;
    }

    .reschedule-modal-icon img {
        display: block;
        width: 54.57px;
        height: 51px;
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
        margin: 20px 0 0;
    }

    .reschedule-modal-subtitle {
        color: #9D9B98;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin: 10px 0 20px;
    }

    .reschedule-summary {
        display: flex;
        align-items: center;
        gap: 24px;
        border-radius: 10px;
        background: #F8F8F8;
        padding: 20px 19px;
        min-height: 135px;
    }

    .reschedule-summary-body {
        min-width: 0;
        flex: 1;
    }

    .reschedule-summary-header {
        display: flex;
        align-items: center;
        gap: 26px;
        margin-bottom: 17px;
    }

    .reschedule-home-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 93px;
        height: 24px;
        padding: 0 10px;
        border-radius: 100px;
        background: #FFC97A;
        color: #FFF;
        font-family: Lato;
        font-size: 14px;
        font-weight: 500;
        line-height: normal;
        text-align: center;
    }

    .reschedule-booking-id {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .reschedule-summary-grid {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .reschedule-pet-avatar {
        position: relative;
        width: 95px;
        height: 95px;
        flex-shrink: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .reschedule-pet-avatar-ring {
        position: absolute;
        inset: 0;
        width: 95px;
        height: 95px;
        display: block;
    }

    .reschedule-pet-photo {
        position: relative;
        z-index: 1;
        width: 84.82px;
        height: 84.82px;
        object-fit: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 104px;
        background: #fff;
        color: #3B3731;
        font-weight: 700;
        font-family: Lato;
        overflow: hidden;
    }

    .reschedule-summary-item {
        display: flex;
        flex-direction: column;
        gap: 13px;
        min-width: 0;
    }

    .reschedule-summary-divider {
        width: 1px;
        height: 54px;
        background: #D8D8D8;
        flex: 0 0 auto;
    }

    .reschedule-summary-item span {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-summary-item span img {
        display: block;
        flex-shrink: 0;
        max-width: none;
    }

    .reschedule-icon-service {
        width: 15.3px;
        height: 16.5px;
    }

    .reschedule-icon-date {
        width: 18.14px;
        height: 17px;
    }

    .reschedule-icon-time {
        width: 16px;
        height: 16px;
    }

    .reschedule-icon-other {
        width: 18px;
        height: 17px;
    }

    .reschedule-summary-item strong {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .reschedule-update-body {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 17px;
        margin-top: 40px;
    }

    .reschedule-calendar-panel {
        width: 380px;
        flex-shrink: 0;
    }

    .reschedule-calendar-head {
        height: 48px;
        border-radius: 100px;
        background: #F3F3F3;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 4px;
    }

    .reschedule-month-nav {
        margin-top: 10px;
        width: 40px;
        height: 40px;
        padding: 0;
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .reschedule-month-nav img {
        display: block;
        width: 40px;
        height: 40px;
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
        gap: 11.5px 11.6px;
        margin-top: 20px;
    }

    .reschedule-days-grid {
        margin-top: 12px;
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
        background: #F7F7F7;
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

    .reschedule-days-grid button.is-booked.is-selected {
        border: 1px solid #91A36C !important;
        background: #D8E8B7;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.20);
    }

    .reschedule-availability-panel {
        width: 383px;
        flex-shrink: 0;
        padding-top: 13px;
    }

    .reschedule-availability-panel h5 {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        color: #BACF8E;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 0 0 33px;
        text-align: center;
    }

    .reschedule-availability-panel h5 img {
        display: block;
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    .reschedule-availability-panel button {
        display: block;
        width: 383px;
        max-width: 100%;
        height: 48px;
        border-radius: 68px;
        border: 1px solid #BACF8E;
        background: #FFF;
        color: #BACF8E;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        margin-bottom: 10px;
        cursor: pointer;
    }

    .reschedule-availability-panel button:last-child {
        margin-bottom: 0;
    }

    .reschedule-availability-panel button.is-active {
        background: #C9DDA0;
        border-color: #C9DDA0;
        box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.05);
        color: #fff;
    }

    .reschedule-new-appointment {
        margin: 40px 0 0;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        line-height: normal;
    }

    .reschedule-new-appointment span {
        color: #9D9B98;
        font-weight: 400;
    }

    .reschedule-new-appointment strong {
        color: #000;
        font-weight: 600;
    }

    .reschedule-actions {
        margin-top: 40px;
        display: flex;
        justify-content: flex-end;
        align-items: center;
        gap: 9px;
    }

    .reschedule-cancel-btn,
    .reschedule-confirm-btn {
        height: 42px;
        border-radius: 75px;
        font-family: Lato;
        font-size: 16px;
        border: 1px solid transparent;
        cursor: pointer;
        padding: 0;
    }

    .reschedule-cancel-btn {
        width: 88px;
        border-color: #3B3731;
        background: #fff;
        color: #3B3731;
        font-weight: 400;
    }

    .reschedule-confirm-btn {
        width: 121px;
        background: #FFC97A;
        border-color: #FFC97A;
        color: #FFF;
        font-weight: 600;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.10);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
        border-radius: 96px;
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

    @media (max-width: 860px) {
        .reschedule-update-body {
            flex-direction: column;
        }

        .reschedule-calendar-panel,
        .reschedule-availability-panel {
            width: 100%;
        }

        .reschedule-availability-panel button {
            width: 100%;
        }

        .reschedule-summary {
            flex-direction: column;
            align-items: flex-start;
        }

        .reschedule-summary-grid {
            flex-wrap: wrap;
            justify-content: flex-start;
        }

        .reschedule-summary-divider {
            display: none;
        }
    }
</style>
