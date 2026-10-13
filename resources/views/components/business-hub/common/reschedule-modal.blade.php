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
        $iconBase = asset('images/business-hub');
        $ownerName = trim((string) ($rescheduleBooking->petOwner?->name ?? 'Client')) ?: 'Client';
        $ownerParts = preg_split('/\s+/', $ownerName, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $ownerShort = count($ownerParts) >= 2 ? $ownerParts[0] . ' ' . mb_substr($ownerParts[1], 0, 1) . '.' : $ownerName;
        $serviceLabel = trim((string) ($rescheduleBooking->service ?? '')) ?: 'Service';
        $originalDateObj = $rescheduleBooking->date ? new DateTimeImmutable((string) $rescheduleBooking->date) : null;
        $scheduledLabel = $originalDateObj ? $originalDateObj->format('D, j M Y') : 'N/A';

        $rescheduleDateObj = $rescheduleSelectedDate ? new DateTimeImmutable($rescheduleSelectedDate) : ($originalDateObj ?: null);
        $calendarBase = $rescheduleCalendarMonth ? new DateTimeImmutable($rescheduleCalendarMonth) : ($rescheduleDateObj ?: new DateTimeImmutable(date('Y-m-01')));
        $rescheduleMonthTitle = strtoupper($calendarBase->format('F Y'));
        $rescheduleYear = (int) $calendarBase->format('Y');
        $rescheduleMonth = (int) $calendarBase->format('m');

        $bookedDaysByMonth = $bookings
            ->filter(fn($b) => $b->date && $b->booking_status !== 'cancelled')
            ->groupBy(fn($b) => date('Y-m', strtotime((string) $b->date)))
            ->map(function ($rows) {
                return $rows->map(fn($b) => (int) date('j', strtotime((string) $b->date)))->unique()->sort()->values()->all();
            })
            ->all();

        $rescheduleAvailability = ['09:00', '11:00', '13:00', '15:30', '16:00', '18:00'];
        $durationMinutes = max(1, (int) $rescheduleDurationMinutes);

        $initialTime = $rescheduleSelectedTime;
        if (is_string($initialTime) && $initialTime !== '') {
            $parsed = DateTime::createFromFormat('H:i', $initialTime) ?: DateTime::createFromFormat('H:i A', $initialTime) ?: DateTime::createFromFormat('h:i A', $initialTime);
            if ($parsed) {
                $initialTime = $parsed->format('H:i');
            }
        }
    @endphp
    @teleport('body')
        <div class="reschedule-modal-overlay" wire:keydown.escape="closeRescheduleModal" x-data="reschedulePicker({
            initialDate: @js($rescheduleSelectedDate),
            initialTime: @js($initialTime),
            initialMonth: @js(sprintf('%04d-%02d-01', $rescheduleYear, $rescheduleMonth)),
            bookedDaysByMonth: @js($bookedDaysByMonth),
            durationMinutes: @js($durationMinutes)
        })">
            <div class="reschedule-modal-card" role="dialog" aria-modal="true" aria-labelledby="reschedule-modal-title">
                <div class="reschedule-modal-loading-bar" wire:loading.flex wire:target="confirmRescheduleBookingFromClient"
                    aria-hidden="true">
                    <span class="reschedule-modal-loading-bar__sweep"></span>
                </div>
                <button type="button" class="reschedule-modal-close" wire:click="closeRescheduleModal"
                    aria-label="Close modal">
                    <img src="{{ asset('images/business-hub/icon-reschedule-close.svg') }}" alt="" width="20"
                        height="20">
                </button>

                <header class="reschedule-modal-header">
                    <h3 class="reschedule-modal-title" id="reschedule-modal-title">Reschedule Booking</h3>
                    <p class="reschedule-modal-subtitle">
                        Propose a new time for <strong>{{ $serviceLabel }}</strong> with
                        <strong>{{ $ownerShort }} scheduled for {{ $scheduledLabel }}.</strong>
                        The customer confirms the change.
                    </p>
                </header>

                <div class="reschedule-modal-divider" aria-hidden="true"></div>

                <div class="reschedule-update-body">
                    <section class="reschedule-date-section">
                        <h4 class="reschedule-section-label">New date</h4>
                        <div class="reschedule-calendar-head">
                            <button type="button" class="reschedule-month-nav" @click="prevMonth()"
                                aria-label="Previous month">
                                <img src="{{ $iconBase }}/icon-reschedule-month-prev.svg" alt="" width="40" height="40">
                            </button>
                            <strong x-text="monthTitle">{{ $rescheduleMonthTitle }}</strong>
                            <button type="button" class="reschedule-month-nav" @click="nextMonth()"
                                aria-label="Next month">
                                <img src="{{ $iconBase }}/icon-reschedule-month-next.svg" alt="" width="40" height="40">
                            </button>
                        </div>
                        <div class="reschedule-date-grid">
                            <template x-for="day in daysInMonth" :key="'day-' + day">
                                <button type="button" class="reschedule-date-card" @click="selectDay(day)"
                                    :class="{ 'is-booked': isBooked(day), 'is-selected': day === selectedDay }">
                                    <span class="reschedule-date-card__dow" x-text="dayName(day)"></span>
                                    <span class="reschedule-date-card__num" x-text="day"></span>
                                </button>
                            </template>
                        </div>
                    </section>

                    <section class="reschedule-time-section">
                        <h4 class="reschedule-section-label">New time</h4>
                        <div class="reschedule-time-grid">
                            @foreach ($rescheduleAvailability as $slot)
                                <button type="button" class="reschedule-time-card" @click="selectTime('{{ $slot }}')"
                                    :class="{ 'is-active': selectedTime === '{{ $slot }}' }">{{ $slot }}</button>
                            @endforeach
                        </div>
                    </section>
                </div>

                <p class="reschedule-new-appointment" x-show="newAppointmentLabel" x-cloak>
                    <span>New date &amp; time:</span>
                    <strong x-text="newAppointmentLabel"></strong>
                </p>

                <div class="reschedule-actions">
                    <button type="button" class="reschedule-cancel-btn"
                        wire:click="closeRescheduleModal">Cancel</button>
                    <button type="button" class="reschedule-confirm-btn"
                        @click="$wire.confirmRescheduleBookingFromClient(selectedDate, selectedTime)"
                        wire:loading.attr="disabled" wire:target="confirmRescheduleBookingFromClient">
                        <span wire:loading.remove wire:target="confirmRescheduleBookingFromClient"
                            class="reschedule-confirm-btn__content">
                            <img src="{{ $iconBase }}/icon-reschedule-sync-white.svg" alt="" width="12.9951" height="13">
                            Reschedule
                        </span>
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

@once
    <script>
        window.reschedulePicker = function(config) {
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];
            const dayNames = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];

            const parseYmd = (ymd) => {
                const [y, m, d] = (ymd || '').split('-').map(Number);
                if (!y || !m || !d) return null;
                return {
                    y,
                    m,
                    d
                };
            };

            const parseTime = (raw) => {
                if (!raw) return null;
                let dt = new Date(`1970-01-01T${raw}`);
                if (!Number.isNaN(dt.getTime()) && /^\d{1,2}:\d{2}$/.test(raw)) {
                    return dt;
                }
                const match = String(raw).match(/(\d{1,2}):(\d{2})\s*(AM|PM)?/i);
                if (!match) return null;
                let hours = parseInt(match[1], 10);
                const minutes = parseInt(match[2], 10);
                const meridiem = (match[3] || '').toUpperCase();
                if (meridiem === 'PM' && hours < 12) hours += 12;
                if (meridiem === 'AM' && hours === 12) hours = 0;
                dt = new Date(1970, 0, 1, hours, minutes, 0);
                return Number.isNaN(dt.getTime()) ? null : dt;
            };

            const formatHm = (date) => {
                return `${String(date.getHours()).padStart(2, '0')}:${String(date.getMinutes()).padStart(2, '0')}`;
            };

            return {
                selectedDate: config.initialDate,
                selectedTime: config.initialTime,
                monthDate: config.initialMonth,
                bookedDaysByMonth: config.bookedDaysByMonth || {},
                durationMinutes: config.durationMinutes || 60,
                get monthKey() {
                    return this.monthDate.slice(0, 7);
                },
                get monthMeta() {
                    const [y, m] = this.monthKey.split('-').map(Number);
                    return {
                        y,
                        m
                    };
                },
                get monthTitle() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    return `${monthNames[m - 1].toUpperCase()} ${y}`;
                },
                get daysInMonth() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    // Alpine x-for treats a number as 1..n; returning a new array each
                    // evaluation can freeze the modal.
                    return new Date(y, m, 0).getDate();
                },
                get selectedDay() {
                    const parsed = parseYmd(this.selectedDate);
                    if (!parsed) return 0;
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    return parsed.y === y && parsed.m === m ? parsed.d : 0;
                },
                get newAppointmentLabel() {
                    const parsed = parseYmd(this.selectedDate);
                    const start = parseTime(this.selectedTime);
                    if (!parsed || !start) return '';
                    const end = new Date(start.getTime() + this.durationMinutes * 60000);
                    const weekday = dayNames[new Date(parsed.y, parsed.m - 1, parsed.d).getDay()];
                    return `${weekday}, ${parsed.d} ${monthNames[parsed.m - 1]} ${parsed.y} · ${formatHm(start)} - ${formatHm(end)}`;
                },
                dayName(day) {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    return dayNames[new Date(y, m - 1, day).getDay()];
                },
                prevMonth() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    const d = new Date(y, m - 2, 1);
                    this.monthDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-01`;
                },
                nextMonth() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    const d = new Date(y, m, 1);
                    this.monthDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-01`;
                },
                selectDay(day) {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    this.selectedDate = `${y}-${String(m).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                },
                selectTime(slot) {
                    this.selectedTime = slot;
                },
                isBooked(day) {
                    return (this.bookedDaysByMonth[this.monthKey] || []).includes(day);
                }
            };
        };
    </script>
@endonce

<style>
    .reschedule-modal-overlay [x-cloak] {
        display: none !important;
    }

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
        font-family: Lato, sans-serif;
    }

    .reschedule-modal-card {
        width: min(100%, 505px);
        border-radius: 10px;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 20px;
        position: relative;
        overflow: visible;
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
        width: 20px;
        height: 20px;
        padding: 0;
        margin: 0;
        border: none;
        background: transparent;
        cursor: pointer;
        z-index: 5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        line-height: 0;
        flex-shrink: 0;
    }

    .reschedule-modal-close img {
        display: block;
        width: 20px;
        height: 20px;
        max-width: none;
        flex-shrink: 0;
        pointer-events: none;
    }

    .reschedule-modal-header {
        padding-right: 36px;
        position: relative;
        z-index: 0;
    }

    .reschedule-modal-title {
        color: #3B3731;
        font-family: "Playfair Display", serif;
        font-size: 24px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin: 0;
        text-align: left;
        text-box-edge: cap alphabetic;
        text-box-trim: trim-both;
    }

    .reschedule-modal-subtitle {
        color: #9D9B98;
        text-align: left;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin: 20px 0 0;
        max-width: 423px;
        text-box-edge: cap alphabetic;
        text-box-trim: trim-both;
    }

    .reschedule-modal-subtitle strong {
        color: #3B3731;
        font-weight: 600;
    }

    .reschedule-modal-divider {
        height: 1px;
        background: #E2E2E2;
        margin: 20px -20px;
    }

    .reschedule-update-body {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .reschedule-section-label {
        margin: 0 0 20px;
        color: #9D9B98;
        font-size: 14px;
        font-weight: 600;
        line-height: normal;
        text-transform: uppercase;
        text-box-edge: cap alphabetic;
        text-box-trim: trim-both;
    }

    .reschedule-calendar-head {
        height: 48px;
        border-radius: 100px;
        background: #F9F9F9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 8px;
        margin-bottom: 20px;
    }

    .reschedule-month-nav {
        margin-top: 5px;
        width: 32px;
        height: 32px;
        padding: 0;
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        overflow: visible;
    }

    .reschedule-month-nav img {
        display: block;
        width: 40px;
        height: 40px;
        max-width: none;
    }

    .reschedule-calendar-head strong {
        color: #3B3731;
        text-align: center;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        text-transform: uppercase;
    }

    .reschedule-date-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 20px 19.5px;
        max-height: 154px;
        overflow-y: auto;
        padding-right: 2px;
        scrollbar-width: thin;
    }

    .reschedule-date-card {
        height: 67px;
        border-radius: 5px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0;
        cursor: pointer;
        padding: 10px 0;
        color: #3B3731;
    }

    .reschedule-date-card__dow {
        color: #9D9B98;
        font-size: 14px;
        font-weight: 600;
        line-height: 25px;
    }

    .reschedule-date-card__num {
        font-family: "Playfair Display", serif;
        font-size: 25px;
        font-weight: 600;
        line-height: 25px;
    }

    .reschedule-date-card.is-selected {
        border-color: #FFC97A;
        background: #FFF7E8;
        color: #FFBA55;
    }

    .reschedule-date-card.is-selected .reschedule-date-card__dow {
        color: #FFBA55;
        font-weight: 700;
    }

    .reschedule-date-card.is-selected .reschedule-date-card__num {
        font-weight: 700;
    }

    .reschedule-date-card.is-booked:not(.is-selected) {
        background: #F9F9F9;
    }

    .reschedule-time-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 20px 20px;
    }

    .reschedule-time-card {
        height: 48px;
        border-radius: 5px;
        border: 1px solid #E2E2E2;
        background: #FFF;
        color: #3B3731;
        text-align: center;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        padding: 0;
    }

    .reschedule-time-card.is-active {
        border-color: #FFC97A;
        background: #FFF7E8;
        color: #FFBA55;
        font-weight: 700;
    }

    .reschedule-new-appointment {
        margin: 20px 0 0;
        height: 40px;
        border-radius: 5px;
        background: #F9F9F9;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
        font-size: 16px;
        line-height: 20px;
        padding: 0 12px;
        text-align: center;
    }

    .reschedule-new-appointment span {
        color: #3B3731;
        font-weight: 400;
    }

    .reschedule-new-appointment strong {
        color: #3B3731;
        font-weight: 600;
    }

    .reschedule-actions {
        margin-top: 20px;
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .reschedule-cancel-btn,
    .reschedule-confirm-btn {
        flex: 1;
        height: 42px;
        border-radius: 75px;
        font-size: 16px;
        border: 1px solid transparent;
        cursor: pointer;
        padding: 0;
        font-weight: 600;
    }

    .reschedule-cancel-btn {
        border-color: #E2E2E2;
        background: #fff;
        color: #9D9B98;
    }

    .reschedule-confirm-btn {
        background: #9FC7E4;
        border-color: #9FC7E4;
        color: #FFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
    }

    .reschedule-confirm-btn__content {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }

    .reschedule-confirm-btn__content img {
        width: 12.9951px;
        height: 13px;
        display: block;
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

    .business-hub-container.is-space .reschedule-date-card.is-selected,
    .business-hub-container.is-space .reschedule-time-card.is-active {
        border-color: #FFA899;
        background: rgba(255, 168, 153, 0.12);
        color: #FFA899;
    }

    .business-hub-container.is-space .reschedule-date-card.is-selected .reschedule-date-card__dow {
        color: #FFA899;
    }

    .business-hub-container.is-space .reschedule-confirm-btn {
        background: #9FC7E4;
        border-color: #9FC7E4;
    }

    @media (max-width: 560px) {
        .reschedule-date-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .reschedule-time-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .reschedule-actions {
            flex-wrap: wrap;
        }
    }
</style>
