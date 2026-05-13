<section class="availability-day-shell">
    <div class="availability-day-card">
        <header class="availability-day-header" x-text="dayViewLabel"></header>

        <div class="availability-drawer-empty availability-day-empty" x-show="dayViewSlots.length === 0" x-cloak>
            <p>No bookings on this day.</p>
        </div>

        <div class="availability-day-timeline" x-show="dayViewSlots.length > 0" x-cloak>
            <template x-for="(slot, slotIndex) in dayViewSlots" :key="slot.key || ('day-slot-' + slotIndex)">
                <div class="availability-day-row">
                    <div class="availability-day-hour" x-text="slot.hourLabel"></div>

                    <article class="availability-day-slot" :class="'is-' + slot.type">
                        <p class="availability-day-slot-time">
                            <svg width="16" height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                <circle cx="8" cy="8" r="6" stroke="currentColor" stroke-width="1.5" />
                                <path d="M8 4.5V8L10.5 10" stroke="currentColor" stroke-width="1.5"
                                    stroke-linecap="round" />
                            </svg>
                            <span x-text="slot.time"></span>
                        </p>

                        <div class="availability-day-slot-body">
                            <p class="availability-day-slot-pet">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13"
                                    viewBox="0 0 14 13" fill="none" aria-hidden="true">
                                    <path
                                        d="M7 5.23684C5.03948 5.23684 3.38291 6.98347 2.84805 9.22684C2.61281 10.2134 2.96752 11.2605 3.83821 11.7525C4.52845 12.1425 5.55514 12.5 7 12.5C8.44486 12.5 9.47186 12.1425 10.1621 11.7525C11.0328 11.2605 11.3872 10.2134 11.152 9.22684C10.6171 6.98316 8.96052 5.23684 7 5.23684ZM0.5 4.74547C0.5 5.61768 1.05405 6.5 1.7381 6.5C2.42214 6.5 2.97619 5.61768 2.97619 4.74547C2.97619 3.87326 2.42214 3.34211 1.7381 3.34211C1.05405 3.34211 0.5 3.87358 0.5 4.74547ZM13.5 4.74547C13.5 5.61768 12.946 6.5 12.2619 6.5C11.5779 6.5 11.0238 5.61768 11.0238 4.74547C11.0238 3.87326 11.5779 3.34211 12.2619 3.34211C12.946 3.34211 13.5 3.87358 13.5 4.74547ZM3.75 1.90337C3.75 2.77558 4.30405 3.65789 4.9881 3.65789C5.67214 3.65789 6.22619 2.77558 6.22619 1.90337C6.22619 1.03116 5.67214 0.5 4.9881 0.5C4.30405 0.5 3.75 1.03147 3.75 1.90337ZM10.25 1.90337C10.25 2.77558 9.69595 3.65789 9.01191 3.65789C8.32786 3.65789 7.77381 2.77558 7.77381 1.90337C7.77381 1.03116 8.32786 0.5 9.01191 0.5C9.69595 0.5 10.25 1.03147 10.25 1.90337Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span
                                    x-text="slot.pet.includes(' - ') ? slot.pet.split(' - ')[0] + ' -' : slot.pet"></span>
                                <span class="availability-day-slot-pet-type"
                                    x-text="slot.pet.includes(' - ') ? slot.pet.split(' - ')[1] : ''"></span>
                            </p>

                            <p class="availability-day-slot-service">
                                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="13"
                                    viewBox="0 0 12 13" fill="none" aria-hidden="true">
                                    <path
                                        d="M3.82598 8.79084C4.78992 9.75476 7.13435 8.9735 9.06223 7.04535C10.9904 5.11751 11.7717 2.77312 10.8077 1.80919M6.66226 1.15444L7.09856 1.59105M5.1352 2.68178L5.5715 3.11808M3.82568 4.42727L4.26198 4.86357M3.38937 6.60906L3.82568 7.04535M9.06223 0.5L9.49853 0.936295M8.62592 3.11839L9.49853 3.99098M7.09887 4.64573L7.97147 5.51832M5.35335 5.95461L6.22595 6.8272"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M3.82569 10.1003C4.18719 9.73885 4.18719 9.15275 3.82569 8.79126C3.4642 8.42977 2.87809 8.42977 2.51659 8.79126L0.771119 10.5367C0.40962 10.8982 0.40962 11.4843 0.771119 11.8458C1.13262 12.2073 1.71872 12.2073 2.08022 11.8458L3.82569 10.1003Z"
                                        stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <span x-text="slot.service"></span>
                            </p>
                        </div>
                    </article>
                </div>
            </template>
        </div>
    </div>
</section>

<style>
    .availability-day-empty {
        padding: 28px 16px;
        text-align: center;
    }

    .availability-day-card {
        border: 1px solid #d9d9d9;
        border-radius: 12px;
        background: #fff;
        overflow: hidden;
    }

    .availability-day-header {
        background: #f3f4f7;
        border-bottom: 1px solid #d9d9d9;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        padding: 15px 12px;
    }

    .availability-day-timeline {
        padding: 22px 20px 26px 2px;
        display: grid;
        gap: 20px;
    }

    .availability-day-row {
        display: grid;
        grid-template-columns: 62px minmax(0, 1fr);
        align-items: start;
        gap: 12px;
    }

    .availability-day-hour {
        text-align: center;
        color: #9D9B98;
        font-family: Lato;
        font-size: 12px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        padding-top: 9px;
    }

    .availability-day-slot {
        border-radius: 10px;
        overflow: hidden;
    }

    .availability-day-slot-time,
    .availability-day-slot-pet,
    .availability-day-slot-service {
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
        text-align: center;
        font-family: Lato;
        font-size: 12px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        letter-spacing: 0.12px;
    }

    .availability-day-slot-time {
        padding: 11px 16px;
    }

    .availability-day-slot-body {
        padding: 10px 16px 13px;
        display: grid;
        gap: 10px;
    }

    .availability-day-slot-pet-type {
        font-weight: 600;
    }

    .availability-day-slot svg {
        flex-shrink: 0;
    }

    .availability-day-slot.is-blue {
        background: #f5f8fa;
        color: #7ab7e3;
    }

    .availability-day-slot.is-blue .availability-day-slot-time {
        background: #edf2f6;
    }

    .availability-day-slot.is-blue .availability-day-slot-pet-type {
        color: #a9d5f4;
    }

    .availability-day-slot.is-green {
        background: #f4f8ec;
        color: #b5db65;
    }

    .availability-day-slot.is-green .availability-day-slot-time {
        background: #ebf2dd;
    }

    .availability-day-slot.is-green .availability-day-slot-pet-type {
        color: #c9dda0;
    }

    .availability-day-slot.is-red {
        background: #ffe2e2;
        color: #ff6e6e;
        text-decoration: line-through;
    }

    .availability-day-slot.is-red .availability-day-slot-time {
        background: #ffcbcb;
    }

    .availability-day-slot.is-red .availability-day-slot-pet-type {
        color: #ffa3a3;
    }

    .availability-day-slot.is-orange {
        background: #fff4e4;
        color: #ffae37;
    }

    .availability-day-slot.is-orange .availability-day-slot-time {
        background: #ffe2b7;
    }

    .availability-day-slot.is-orange .availability-day-slot-pet-type {
        color: #ffc877;
    }
</style>

<script>
    (function() {
        if (window.availabilityDayCalendar) return;

        window.availabilityDayCalendar = function() {
            const MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov',
                'Dec'
            ];
            const WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

            const getOrdinal = (day) => {
                const mod10 = day % 10;
                const mod100 = day % 100;
                if (mod10 === 1 && mod100 !== 11) return 'st';
                if (mod10 === 2 && mod100 !== 12) return 'nd';
                if (mod10 === 3 && mod100 !== 13) return 'rd';
                return 'th';
            };

            const formatDayLabel = (dateObj) => {
                const weekday = WEEKDAYS[dateObj.getDay()];
                const day = dateObj.getDate();
                const suffix = getOrdinal(day);
                const month = MONTHS_SHORT[dateObj.getMonth()];
                return `${weekday}, ${day}${suffix} ${month}`;
            };

            return {
                dayLabel: formatDayLabel(new Date()),
                daySlots: [{
                        hourLabel: '11AM',
                        time: '11:00-11:45',
                        pet: 'Surg - Turtle',
                        service: 'Bath & Brush',
                        type: 'blue',
                    },
                    {
                        hourLabel: '12PM',
                        time: '11:30-11:45',
                        pet: 'Toosie - Cat',
                        service: 'Nail Trim',
                        type: 'green',
                    },
                    {
                        hourLabel: '13PM',
                        time: '11:30-11:45',
                        pet: 'Mario - Cat',
                        service: 'Full Groom',
                        type: 'red',
                    },
                    {
                        hourLabel: '14PM',
                        time: '11:30-12:45',
                        pet: 'Daisy - Dog',
                        service: 'Luxury Spa',
                        type: 'orange',
                    },
                    {
                        hourLabel: '15AM',
                        time: '15:00-15:45',
                        pet: 'Surf - Turtle',
                        service: 'Bath & Brush',
                        type: 'blue',
                    },
                ],
            };
        };
    })();
</script>
