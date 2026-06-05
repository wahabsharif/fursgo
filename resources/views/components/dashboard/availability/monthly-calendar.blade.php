<section class="availability-calendar-shell">
    <div class="availability-calendar-card">
        <div class="availability-weekdays">
            <template x-for="(weekday, weekdayIndex) in weekdays" :key="weekday">
                <span :class="{ 'is-current-weekday': weekdayIndex === currentWeekdayIndex }" x-text="weekday"></span>
            </template>
        </div>

        <div class="availability-grid">
            <template x-for="day in mainMonthGrid" :key="day.key">
                <article class="availability-day-cell"
                    :class="{ 'is-muted': !day.inCurrentMonth, 'is-today-cell': isToday(day.date) }">
                    <strong :class="{ 'is-today': isToday(day.date) }" x-text="day.day"></strong>
                    <ul x-show="day.inCurrentMonth && day.slots.length">
                        <template x-for="(slot, slotIndex) in day.slots" :key="day.key + '-slot-' + slotIndex">
                            <li :class="{
                                [slot.type]: true,
                                'is-calendar-slot-clickable': slot.bookingId || (slot.label && String(slot.label).trim()
                                    .startsWith('+')),
                            }"
                                @click.stop="onCalendarSlotClick(slot)">
                                <svg x-show="!slot.label.trim().startsWith('+')" class="slot-icon"
                                    xmlns="http://www.w3.org/2000/svg" width="14" height="13" viewBox="0 0 14 13"
                                    fill="none" aria-hidden="true">
                                    <path
                                        d="M0.5 6.3467C0.5 4.02618 0.5 2.86561 1.25343 2.14503C2.00686 1.42444 3.21864 1.42383 5.64286 1.42383H8.21429C10.6385 1.42383 11.8509 1.42383 12.6037 2.14503C13.3565 2.86623 13.3571 4.02618 13.3571 6.3467V7.57741C13.3571 9.89793 13.3571 11.0585 12.6037 11.7791C11.8503 12.4997 10.6385 12.5003 8.21429 12.5003H5.64286C3.21864 12.5003 2.00621 12.5003 1.25343 11.7791C0.500643 11.0579 0.5 9.89793 0.5 7.57741V6.3467Z"
                                        stroke="currentColor" />
                                    <path d="M3.71433 1.42304V0.5M10.1429 1.42304V0.5M0.821472 4.49983H13.0358"
                                        stroke="currentColor" stroke-linecap="round" />
                                    <path
                                        d="M10.7858 9.4225C10.7858 9.5857 10.7181 9.74222 10.5975 9.85762C10.477 9.97302 10.3135 10.0379 10.143 10.0379C9.97247 10.0379 9.80895 9.97302 9.68839 9.85762C9.56783 9.74222 9.5001 9.5857 9.5001 9.4225C9.5001 9.25929 9.56783 9.10277 9.68839 8.98737C9.80895 8.87197 9.97247 8.80714 10.143 8.80714C10.3135 8.80714 10.477 8.87197 10.5975 8.98737C10.7181 9.10277 10.7858 9.25929 10.7858 9.4225ZM10.7858 6.96106C10.7858 7.12426 10.7181 7.28078 10.5975 7.39619C10.477 7.51159 10.3135 7.57642 10.143 7.57642C9.97247 7.57642 9.80895 7.51159 9.68839 7.39619C9.56783 7.28078 9.5001 7.12426 9.5001 6.96106C9.5001 6.79786 9.56783 6.64134 9.68839 6.52594C9.80895 6.41054 9.97247 6.3457 10.143 6.3457C10.3135 6.3457 10.477 6.41054 10.5975 6.52594C10.7181 6.64134 10.7858 6.79786 10.7858 6.96106ZM7.57153 9.4225C7.57153 9.5857 7.5038 9.74222 7.38324 9.85762C7.26269 9.97302 7.09917 10.0379 6.92868 10.0379C6.75818 10.0379 6.59467 9.97302 6.47411 9.85762C6.35355 9.74222 6.28582 9.5857 6.28582 9.4225C6.28582 9.25929 6.35355 9.10277 6.47411 8.98737C6.59467 8.87197 6.75818 8.80714 6.92868 8.80714C7.09917 8.80714 7.26269 8.87197 7.38324 8.98737C7.5038 9.10277 7.57153 9.25929 7.57153 9.4225ZM7.57153 6.96106C7.57153 7.12426 7.5038 7.28078 7.38324 7.39619C7.26269 7.51159 7.09917 7.57642 6.92868 7.57642C6.75818 7.57642 6.59467 7.51159 6.47411 7.39619C6.35355 7.28078 6.28582 7.12426 6.28582 6.96106C6.28582 6.79786 6.35355 6.64134 6.47411 6.52594C6.59467 6.41054 6.75818 6.3457 6.92868 6.3457C7.09917 6.3457 7.26269 6.41054 7.38324 6.52594C7.5038 6.64134 7.57153 6.79786 7.57153 6.96106ZM4.35725 9.4225C4.35725 9.5857 4.28952 9.74222 4.16896 9.85762C4.0484 9.97302 3.88489 10.0379 3.71439 10.0379C3.54389 10.0379 3.38038 9.97302 3.25982 9.85762C3.13926 9.74222 3.07153 9.5857 3.07153 9.4225C3.07153 9.25929 3.13926 9.10277 3.25982 8.98737C3.38038 8.87197 3.54389 8.80714 3.71439 8.80714C3.88489 8.80714 4.0484 8.87197 4.16896 8.98737C4.28952 9.10277 4.35725 9.25929 4.35725 9.4225ZM4.35725 6.96106C4.35725 7.12426 4.28952 7.28078 4.16896 7.39619C4.0484 7.51159 3.88489 7.57642 3.71439 7.57642C3.54389 7.57642 3.38038 7.51159 3.25982 7.39619C3.13926 7.28078 3.07153 7.12426 3.07153 6.96106C3.07153 6.79786 3.13926 6.64134 3.25982 6.52594C3.38038 6.41054 3.54389 6.3457 3.71439 6.3457C3.88489 6.3457 4.0484 6.41054 4.16896 6.52594C4.28952 6.64134 4.35725 6.79786 4.35725 6.96106Z"
                                        fill="currentColor" />
                                </svg>
                                <span x-text="slot.label"></span>
                            </li>
                        </template>
                    </ul>
                </article>
            </template>
        </div>
    </div>
</section>

<style>
    .availability-calendar-shell {
        padding-top: 2px;
    }

    .availability-calendar-card {
        border: 1px solid #ddd6cd;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
    }

    .availability-calendar-title {
        min-width: 280px;
        display: grid;
        grid-template-columns: 26px minmax(0, max-content) 26px;
        align-items: center;
        justify-content: center;
        column-gap: 8px;
        flex: 1;
    }

    .availability-calendar-title button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        aspect-ratio: 1/1;
        border-radius: 50%;
        border: 1px solid #F5F5F5;
        background: #FFF;
        box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.03);
        cursor: pointer;
    }

    .availability-calendar-title h3 {
        margin: 0;
        color: #4c473f;
        font-family: Lato, sans-serif;
        font-size: 22px;
        font-weight: 500;
        text-align: center;
        white-space: nowrap;
    }

    .availability-weekdays,
    .availability-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .availability-weekdays span {
        border-bottom: 1px solid #D4D4D4;
        background: #F9FAFC;
        padding: 15px 8px;
        text-align: center;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .availability-weekdays span.is-current-weekday {
        color: #FFC97A;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
    }

    .availability-day-cell {
        aspect-ratio: 1 / 1;
        border-right: 1px solid #eee7df;
        border-bottom: 1px solid #eee7df;
        padding: 15px;
        position: relative;
    }

    .availability-day-cell strong {
        display: block;
        width: 100%;
        text-align: right;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .availability-day-cell strong.is-today {
        background: transparent !important;
        color: #FFC97A !important;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
    }

    .availability-day-cell.is-today-cell {
        background: #FFFBF4;
    }

    .availability-day-cell ul {
        position: absolute;
        top: 35px;
        right: 6px;
        bottom: 6px;
        left: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        gap: 5px;
    }

    .availability-day-cell li {
        border-radius: 999px;
        padding: 10px 15px;
        min-width: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        letter-spacing: 0.14px;
    }

    .availability-day-cell li .slot-icon {
        width: 20px;
        height: 20px;
        aspect-ratio: 20/20;
        flex-shrink: 0;
    }

    .availability-day-cell li.blue {
        background: rgba(203, 220, 232, 0.20);
        color: #7AB7E3;
    }

    .availability-day-cell li.green {
        background: #F4F8EC;
        color: #B5DB65;
    }

    .availability-day-cell li.orange {
        background: #FFF4E4;
        color: #FFAE37;
    }

    .availability-day-cell li.red {
        background: #FFE2E2;
        color: #FF6E6E;
        text-decoration: line-through;
    }

    .availability-day-cell li.neutral {
        background: #f2f2f2;
        color: #a7a5a2;
    }

    .availability-day-cell li.is-calendar-slot-clickable {
        cursor: pointer;
    }

    .availability-day-cell.is-muted {
        background: #F9FAFC;
    }

    .availability-day-cell.is-muted::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url('{{ asset('images/muted-bg.svg') }}');
        background-repeat: no-repeat;
        background-position: center;
        background-size: 100% 100%;
        pointer-events: none;
    }

    .availability-day-cell.is-muted>* {
        position: relative;
        z-index: 1;
    }
</style>

<script>
    (function() {
        if (window.availabilityCalendarShell) return;

        const MONTHS = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
        ];

        const DAY_VIEW_WEEKDAYS = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        const dayViewOrdinal = (n) => {
            const mod10 = n % 10;
            const mod100 = n % 100;
            if (mod10 === 1 && mod100 !== 11) return 'st';
            if (mod10 === 2 && mod100 !== 12) return 'nd';
            if (mod10 === 3 && mod100 !== 13) return 'rd';
            return 'th';
        };

        const MONTHS_SHORT = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        const formatDayViewLabel = (dateObj) => {
            if (!dateObj) return '';
            const weekday = DAY_VIEW_WEEKDAYS[dateObj.getDay()];
            const dayNum = dateObj.getDate();
            const suffix = dayViewOrdinal(dayNum);
            const month = MONTHS_SHORT[dateObj.getMonth()];
            return `${weekday}, ${dayNum}${suffix} ${month}`;
        };

        const pad2 = (n) => String(n).padStart(2, '0');

        const getISOWeekNumber = (date) => {
            const d = new Date(date.getFullYear(), date.getMonth(), date.getDate());
            const day = (d.getDay() + 6) % 7;
            d.setDate(d.getDate() - day + 3);
            const firstThursday = new Date(d.getFullYear(), 0, 4);
            const firstDay = (firstThursday.getDay() + 6) % 7;
            firstThursday.setDate(firstThursday.getDate() - firstDay + 3);
            return 1 + Math.round((d - firstThursday) / 604800000);
        };

        const formatWeekViewLabel = (weekStart) => {
            if (!weekStart) return '';
            const weekEnd = new Date(weekStart);
            weekEnd.setDate(weekStart.getDate() + 6);
            const weekNum = getISOWeekNumber(weekStart);
            const month = MONTHS_SHORT[weekStart.getMonth()];
            return `Week ${weekNum} , ${month} ${pad2(weekStart.getDate())} - ${pad2(weekEnd.getDate())}`;
        };

        const slotHourLabelFromHour = (hour24) => {
            if (hour24 == null || Number.isNaN(hour24)) return '—';
            const h = Math.min(23, Math.max(0, hour24));
            if (h === 0) return '12AM';
            if (h < 12) return `${h}AM`;
            if (h === 12) return '12PM';
            return `${h - 12}PM`;
        };

        window.availabilityCalendarShell = function() {
            return {
                today: null,
                activeView: 'month',
                isBookingsDrawerOpen: false,
                isBookingDetailOpen: false,
                selectedBooking: null,
                isSpaceAccount: false,
                bookingsByDate: {},
                drawerTopOffset: 0,
                drawerFilterDateKey: null,
                dayViewDate: null,
                weekdays: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                currentWeekdayIndex: 0,
                mainMonth: null,
                weekStart: null,
                init() {
                    const now = new Date();
                    this.today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    this.currentWeekdayIndex = (this.today.getDay() + 6) % 7;
                    this.mainMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                    this.weekStart = new Date(this.today);
                    this.weekStart.setDate(this.today.getDate() - this.currentWeekdayIndex);
                    this.dayViewDate = new Date(this.today);
                    this.drawerFilterDateKey = null;
                    this.syncDrawerTopOffset();
                    window.addEventListener('resize', () => this.syncDrawerTopOffset());
                    this.bookingsByDate = window.__availabilityCalendar?.byDate || {};
                    this.isSpaceAccount = Boolean(window.__availabilityCalendar?.isSpace);
                },
                syncDrawerTopOffset() {
                    const curve = document.querySelector('.dashboard-header .curve-shape-container');

                    if (!curve) {
                        this.drawerTopOffset = 0;
                        return;
                    }

                    const curveRect = curve.getBoundingClientRect();
                    this.drawerTopOffset = Math.max(0, Math.round(curveRect.bottom));
                },
                openBookingsDrawer() {
                    this.closeBookingDetail();
                    this.drawerFilterDateKey = null;
                    this.syncDrawerTopOffset();
                    this.isBookingsDrawerOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                openBookingsDrawerForDate(dateKey) {
                    if (!dateKey) {
                        return;
                    }
                    this.closeBookingDetail();
                    this.drawerFilterDateKey = dateKey;
                    this.syncDrawerTopOffset();
                    this.isBookingsDrawerOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeBookingsDrawer() {
                    this.isBookingsDrawerOpen = false;
                    this.drawerFilterDateKey = null;
                    if (!this.isBookingDetailOpen) {
                        document.body.style.overflow = '';
                    }
                },
                drawerHasBookingsForFilter() {
                    if (!this.drawerFilterDateKey) {
                        return true;
                    }
                    const rows = this.bookingsByDate[this.drawerFilterDateKey];
                    return Array.isArray(rows) && rows.length > 0;
                },
                get drawerHeadline() {
                    if (!this.drawerFilterDateKey) {
                        return 'Bookings';
                    }
                    const parts = this.drawerFilterDateKey.split('-').map((x) => parseInt(x, 10));
                    if (parts.length !== 3 || parts.some((n) => Number.isNaN(n))) {
                        return 'Bookings';
                    }
                    const dt = new Date(parts[0], parts[1] - 1, parts[2]);
                    return `Bookings — ${formatDayViewLabel(dt)}`;
                },
                onMiniCalendarDateSelect(detail) {
                    if (!detail || detail.year == null || detail.monthIndex == null || detail.day == null) {
                        return;
                    }
                    const {
                        year,
                        monthIndex,
                        day,
                        dateKey,
                    } = detail;
                    const picked = new Date(year, monthIndex, day);
                    if (Number.isNaN(picked.getTime())) {
                        return;
                    }

                    if (this.activeView === 'month') {
                        this.mainMonth = new Date(year, monthIndex, 1);
                        const key = dateKey ||
                            `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                        this.openBookingsDrawerForDate(key);
                        return;
                    }
                    if (this.activeView === 'week') {
                        const mondayIndex = (picked.getDay() + 6) % 7;
                        this.weekStart = new Date(year, monthIndex, day - mondayIndex);
                        return;
                    }
                    if (this.activeView === 'day') {
                        this.dayViewDate = picked;
                    }
                },
                openBookingDetail(bookingId) {
                    if (!bookingId) {
                        return;
                    }
                    const id = String(bookingId);
                    const row = window.__availabilityCalendar?.byId?.[id];
                    if (!row) {
                        return;
                    }
                    this.closeBookingsDrawer();
                    this.syncDrawerTopOffset();
                    this.selectedBooking = row;
                    this.isBookingDetailOpen = true;
                    document.body.style.overflow = 'hidden';
                },
                closeBookingDetail() {
                    this.isBookingDetailOpen = false;
                    this.selectedBooking = null;
                    if (!this.isBookingsDrawerOpen) {
                        document.body.style.overflow = '';
                    }
                },
                handleAvailabilityEscape() {
                    if (this.isBookingDetailOpen) {
                        this.closeBookingDetail();
                        return;
                    }
                    this.closeBookingsDrawer();
                },
                onCalendarSlotClick(slot) {
                    if (slot.bookingId) {
                        this.openBookingDetail(slot.bookingId);
                        return;
                    }
                    const label = slot.label ? String(slot.label).trim() : '';
                    if (label.startsWith('+')) {
                        this.openBookingsDrawer();
                    }
                },
                get mainMonthYearLabel() {
                    return `${MONTHS[this.mainMonth.getMonth()]}, ${this.mainMonth.getFullYear()}`;
                },
                get weekRangeLabel() {
                    return formatWeekViewLabel(this.weekStart);
                },
                get periodLabel() {
                    if (this.activeView === 'day' && this.dayViewDate) {
                        return formatDayViewLabel(this.dayViewDate);
                    }
                    if (this.activeView === 'week') {
                        return this.weekRangeLabel;
                    }
                    return this.mainMonthYearLabel;
                },
                get dayViewLabel() {
                    return formatDayViewLabel(this.dayViewDate);
                },
                get dayViewSlots() {
                    if (!this.dayViewDate) {
                        return [];
                    }
                    const y = this.dayViewDate.getFullYear();
                    const m = this.dayViewDate.getMonth();
                    const d = this.dayViewDate.getDate();
                    const dk =
                        `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    const rows = this.bookingsByDate[dk] || [];
                    const byId = window.__availabilityCalendar?.byId || {};
                    return rows.map((r, index) => {
                        const b = r.bookingId ? byId[String(r.bookingId)] : null;
                        const timeStr = b?.time && b.time !== 'Time not set' ? b.time : (r.label ||
                            '—');
                        const startPart = String(r.label || '').split(/\s*-\s*/)[0];
                        const hm = startPart.match(/^(\d{1,2})/);
                        const hour24 = hm ? parseInt(hm[1], 10) : NaN;
                        const hourLabel = slotHourLabelFromHour(hour24);
                        const petLine = b ?
                            (b.petName + (b.petType ? ' - ' + b.petType : '')) :
                            'Booking';
                        return {
                            hourLabel,
                            time: timeStr,
                            pet: petLine,
                            service: b?.service || '—',
                            type: r.type || 'blue',
                            bookingId: r.bookingId || null,
                            key: `day-slot-${dk}-${index}`,
                        };
                    });
                },
                prevMainMonth() {
                    this.mainMonth = new Date(this.mainMonth.getFullYear(), this.mainMonth.getMonth() - 1, 1);
                },
                nextMainMonth() {
                    this.mainMonth = new Date(this.mainMonth.getFullYear(), this.mainMonth.getMonth() + 1, 1);
                },
                prevWeek() {
                    this.weekStart = new Date(this.weekStart.getFullYear(), this.weekStart.getMonth(), this
                        .weekStart.getDate() - 7);
                },
                nextWeek() {
                    this.weekStart = new Date(this.weekStart.getFullYear(), this.weekStart.getMonth(), this
                        .weekStart.getDate() + 7);
                },
                prevPeriod() {
                    if (this.activeView === 'day' && this.dayViewDate) {
                        const d = new Date(this.dayViewDate);
                        d.setDate(d.getDate() - 1);
                        this.dayViewDate = d;
                        return;
                    }
                    if (this.activeView === 'week') {
                        this.prevWeek();
                        return;
                    }

                    this.prevMainMonth();
                },
                nextPeriod() {
                    if (this.activeView === 'day' && this.dayViewDate) {
                        const d = new Date(this.dayViewDate);
                        d.setDate(d.getDate() + 1);
                        this.dayViewDate = d;
                        return;
                    }
                    if (this.activeView === 'week') {
                        this.nextWeek();
                        return;
                    }

                    this.nextMainMonth();
                },
                isToday(dateObj) {
                    if (!dateObj || !this.today) return false;
                    return dateObj.getFullYear() === this.today.getFullYear() &&
                        dateObj.getMonth() === this.today.getMonth() &&
                        dateObj.getDate() === this.today.getDate();
                },
                buildMonthGrid(baseDate, includeSlots = false) {
                    const year = baseDate.getFullYear();
                    const month = baseDate.getMonth();
                    const firstOfMonth = new Date(year, month, 1);
                    const daysInMonth = new Date(year, month + 1, 0).getDate();
                    const mondayOffset = (firstOfMonth.getDay() + 6) % 7;
                    const startDate = new Date(year, month, 1 - mondayOffset);
                    const usedCells = mondayOffset + daysInMonth;
                    const totalCells = usedCells <= 35 ? 35 : 42;
                    const cells = [];

                    for (let index = 0; index < totalCells; index++) {
                        const dateObj = new Date(startDate);
                        dateObj.setDate(startDate.getDate() + index);
                        const day = dateObj.getDate();
                        const inCurrentMonth = dateObj.getMonth() === month;
                        const dateKey =
                            `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

                        cells.push({
                            key: dateKey,
                            date: dateObj,
                            day,
                            inCurrentMonth,
                            slots: includeSlots && inCurrentMonth ?
                                this.getSlotsForDate(dateObj.getFullYear(), dateObj.getMonth(), day) :
                                [],
                        });
                    }

                    return cells;
                },
                get mainMonthGrid() {
                    return this.buildMonthGrid(this.mainMonth, true);
                },
                get weeklyGrid() {
                    const byId = window.__availabilityCalendar?.byId || {};
                    const MAX_SLOTS = 4;

                    return Array.from({
                        length: 7
                    }, (_, index) => {
                        const dateObj = new Date(this.weekStart);
                        dateObj.setDate(this.weekStart.getDate() + index);
                        const dayLabel = this.weekdays[index];
                        const dateKey =
                            `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
                        const rows = this.bookingsByDate[dateKey] || [];
                        const sortedRows = [...rows].sort((a, b) => String(a.label).localeCompare(
                            String(b
                                .label)));
                        const visibleRows = sortedRows.slice(0, MAX_SLOTS);
                        const slots = visibleRows.map((r, slotIdx) => {
                            const b = r.bookingId ? byId[String(r.bookingId)] : null;
                            const timeStr = b?.time && b.time !== 'Time not set' ? b.time :
                                (r.label ||
                                    '—');
                            const petLine = b ?
                                (b.petName + (b.petType ? ' - ' + b.petType : '')) :
                                'Booking';
                            return {
                                time: timeStr,
                                pet: petLine,
                                service: b?.service || '—',
                                type: r.type || 'blue',
                                bookingId: r.bookingId || null,
                                key: `week-slot-${dateKey}-${slotIdx}`,
                            };
                        });
                        const moreCount = Math.max(0, sortedRows.length - MAX_SLOTS);

                        return {
                            key: dateKey,
                            label: dayLabel,
                            day: dateObj.getDate(),
                            isToday: this.isToday(dateObj),
                            isMuted: index >= 5,
                            slots,
                            moreCount,
                            dateKey,
                        };
                    });
                },
                getSlotsForDate(year, monthIndex, day) {
                    const dateKey =
                        `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const rows = this.bookingsByDate[dateKey] || [];
                    if (!rows.length) {
                        return [];
                    }
                    const sorted = [...rows].sort((a, b) => String(a.label).localeCompare(String(b.label)));
                    const maxShow = 2;
                    const out = sorted.slice(0, maxShow).map((r) => ({
                        label: r.label,
                        type: r.type,
                        bookingId: r.bookingId,
                    }));
                    const extra = sorted.length - maxShow;
                    if (extra > 0) {
                        out.push({
                            label: `+ ${extra}`,
                            type: 'neutral',
                            bookingId: null,
                        });
                    }
                    return out;
                },
            };
        };
    })();
</script>
