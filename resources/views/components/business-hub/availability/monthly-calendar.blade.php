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
                    :class="{
                        'is-outside': !day.inCurrentMonth,
                        'is-closed': day.inCurrentMonth && day.capacity.isEmpty,
                        'is-time-off-cell': day.inCurrentMonth && day.capacity.isTimeOff,
                        'is-today-cell': day.inCurrentMonth && isToday(day.date) && isSelectedDate(day.date),
                        'is-selected-cell': day.inCurrentMonth && isSelectedDate(day.date) && !isToday(day.date),
                        'is-clickable': day.inCurrentMonth,
                    }"
                    @click.stop="day.inCurrentMonth && openBookingsDrawerForDate(day.key)">
                    <div class="availability-day-cell-top">
                        <strong class="availability-day-cell-date"
                            :class="{ 'is-today': day.inCurrentMonth && (isToday(day.date) || isSelectedDate(day.date)) }"
                            x-text="day.day"></strong>
                    </div>

                    <template x-if="day.inCurrentMonth && !day.capacity.isEmpty">
                        <div class="availability-day-cell-body">
                            {{-- Figma: layered rounded pills on #EFEFEF track (bar above summary) --}}
                            <div class="availability-day-cell-bar"
                                :class="{ 'is-time-off': day.capacity.isTimeOff }"
                                aria-hidden="true">
                                <template x-if="!day.capacity.isTimeOff">
                                    <template x-for="(seg, segIdx) in day.capacity.barLayers"
                                        :key="'seg-' + day.key + '-' + segIdx">
                                        <span class="availability-day-cell-bar-segment"
                                            :class="'is-' + seg.type"
                                            :style="`left: ${seg.left}%; width: ${seg.width}%`"></span>
                                    </template>
                                </template>
                            </div>

                            <p class="availability-day-cell-summary is-time-off" x-show="day.capacity.isTimeOff" x-cloak>
                                Time off
                            </p>
                            <p class="availability-day-cell-summary" x-show="!day.capacity.isTimeOff" x-cloak>
                                <span class="availability-day-cell-booked"
                                    x-text="day.capacity.bookedCount + ' booked ·'"></span>
                                <span class="availability-day-cell-free"
                                    x-text="' ' + day.capacity.freeHours + 'h free'"></span>
                            </p>
                        </div>
                    </template>
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
        border: 1px solid #e2e2e2;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
    }

    .availability-calendar-title {
        width: min(100%, 300px);
        min-width: 280px;
        display: grid;
        grid-template-columns: 26px minmax(0, 1fr) 26px;
        align-items: center;
        justify-content: center;
        column-gap: 12px;
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

    .availability-calendar-title button:first-of-type {
        justify-self: start;
    }

    .availability-calendar-title button:last-of-type {
        justify-self: end;
    }

    .availability-calendar-title h3 {
        margin: 0;
        color: #3B3731;
        font-family: "Playfair Display", Georgia, serif;
        font-size: 22px;
        font-weight: 600;
        text-align: center;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .availability-weekdays,
    .availability-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .availability-weekdays span {
        border-bottom: 1px solid #e2e2e2;
        background: #F9FAFC;
        padding: 14px 8px;
        text-align: center;
        color: #9C9790;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .availability-weekdays span.is-current-weekday {
        color: #3B3731;
        font-weight: 600;
    }

    .availability-day-cell {
        min-height: 115px;
        border-right: 1px solid #eee7df;
        border-bottom: 1px solid #eee7df;
        padding: 10px 12px 12px;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        justify-content: flex-start;
        gap: 6px;
        background: #fff;
        box-sizing: border-box;
        transition: background 120ms ease;
    }

    .availability-day-cell.is-clickable {
        cursor: pointer;
    }

    .availability-day-cell.is-clickable:hover {
        background: #FBFBFB;
    }

    .availability-day-cell-top {
        display: flex;
        align-items: center;
        justify-content: flex-start;
    }

    .availability-day-cell-date {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 28px;
        height: 28px;
        padding: 0 6px;
        color: #565149;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        box-sizing: border-box;
    }

    .availability-day-cell-date.is-today {
        width: 28px;
        min-width: 28px;
        max-width: 28px;
        height: 28px;
        padding: 0;
        aspect-ratio: 1 / 1;
        color: #ffffff !important;
        background: var(--availability-accent, #FFC97A);
        border-radius: 50%;
        font-weight: 700;
        flex-shrink: 0;
    }

    .availability-day-cell.is-today-cell,
    .availability-day-cell.is-selected-cell {
        background: #fff;
    }

    .availability-day-cell.is-today-cell::before,
    .availability-day-cell.is-selected-cell::before {
        content: '';
        position: absolute;
        inset: 2px;
        border: 1px solid var(--availability-accent, #FFC97A);
        border-radius: 4px;
        background: #FFFCF8;
        pointer-events: none;
        z-index: 0;
    }

    .availability-layout.is-space .availability-day-cell.is-today-cell::before,
    .availability-layout.is-space .availability-day-cell.is-selected-cell::before {
        background: var(--availability-today-bg, #FFF7F5);
    }

    .availability-day-cell.is-today-cell:hover::before,
    .availability-day-cell.is-selected-cell:hover::before {
        background: var(--availability-today-bg, #FFFBF4);
    }

    .availability-day-cell.is-today-cell>*,
    .availability-day-cell.is-selected-cell>* {
        position: relative;
        z-index: 1;
    }

    .availability-day-cell-body {
        display: flex;
        flex-direction: column;
        gap: 5px;
        margin-top: auto;
        padding-top: 4px;
    }

    .availability-day-cell-summary {
        margin: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: 1.2;
        letter-spacing: 0;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .availability-day-cell.is-clickable .availability-day-cell-summary.is-time-off {
        color: #9D9B98;
    }

    .availability-day-cell-booked {
        color: #3B3731;
    }

    .availability-day-cell-free {
        color: #A0BA6B;
    }

    /* Figma: 6px track + absolute rounded segment pills (not flex-cut slices) */
    .availability-day-cell-bar {
        position: relative;
        width: 100%;
        height: 6px;
        border-radius: 100px;
        background: #EFEFEF;
        overflow: visible;
    }

    .availability-day-cell-bar.is-time-off {
        background: #E8E8E8;
    }

    .availability-day-cell-bar-segment {
        position: absolute;
        top: 0;
        height: 6px;
        border-radius: 100px;
        box-sizing: border-box;
        pointer-events: none;
    }

    /* Figma pastel layers: completed → confirmed → pending */
    .availability-day-cell-bar-segment.is-blue {
        background: #CBDCE8;
        z-index: 3;
    }

    .availability-day-cell-bar-segment.is-green {
        background: #D8E8B7;
        z-index: 2;
    }

    .availability-day-cell-bar-segment.is-orange {
        background: #FFC97A;
        z-index: 1;
    }

    .availability-day-cell.is-outside {
        background: #F9F8F4;
    }

    .availability-day-cell.is-outside .availability-day-cell-date {
        color: #BCBCBC;
    }

    .availability-day-cell.is-closed .availability-day-cell-date {
        color: #9D9B98;
    }
</style>

<script>
    (function() {
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

        const pad2 = (n) => String(n).padStart(2, '0');

        const formatDayViewLabel = (dateObj) => {
            if (!dateObj) return '';
            const weekday = DAY_VIEW_WEEKDAYS[dateObj.getDay()];
            const dayNum = dateObj.getDate();
            const suffix = dayViewOrdinal(dayNum);
            const month = MONTHS_SHORT[dateObj.getMonth()];
            return `${weekday}, ${dayNum}${suffix} ${month}`;
        };

        const WEEKDAY_KEYS = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        const WEEKDAY_LONG = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const DEFAULT_WORKING_HOURS = {
            monday: {
                status: true,
                start: '10:00',
                end: '18:00'
            },
            tuesday: {
                status: true,
                start: '10:00',
                end: '18:00'
            },
            wednesday: {
                status: true,
                start: '10:00',
                end: '18:00'
            },
            thursday: {
                status: true,
                start: '10:00',
                end: '18:00'
            },
            friday: {
                status: true,
                start: '10:00',
                end: '18:00'
            },
            saturday: {
                status: false,
                start: '10:00',
                end: '18:00'
            },
            sunday: {
                status: false,
                start: '10:00',
                end: '18:00'
            },
        };

        const expandHolidayKeys = (ranges) => {
            const keys = {};
            (ranges || []).forEach((range) => {
                const from = new Date(`${range.from}T00:00:00`);
                const to = new Date(`${range.to || range.from}T00:00:00`);
                if (Number.isNaN(from.getTime()) || Number.isNaN(to.getTime())) {
                    return;
                }
                const cursor = new Date(from);
                while (cursor <= to) {
                    const key =
                        `${cursor.getFullYear()}-${pad2(cursor.getMonth() + 1)}-${pad2(cursor.getDate())}`;
                    keys[key] = true;
                    cursor.setDate(cursor.getDate() + 1);
                }
            });
            return keys;
        };

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
            return `Week ${weekNum}, ${month} ${pad2(weekStart.getDate())} – ${pad2(weekEnd.getDate())}`;
        };

        // Shared body geometry (Figma week 436:610 + day 443:435): 10×55px rows.
        // Row 0 = spacer under the day header; last row muted. Events sit below spacer.
        const TIMELINE_ROW_PX = 55;
        const TIMELINE_ROW_COUNT = 10;
        const TIMELINE_SPACER_PX = TIMELINE_ROW_PX;

        const hourLabel = (hour) => (hour < 12 ? `${hour}am` : (hour === 12 ? '12pm' : `${hour}pm`));

        // Week (436:610): 8am–16pm, one 55px row per hour, labels on every line.
        const WEEK_TIMELINE = {
            startMin: 8 * 60,
            endMin: 17 * 60,
            hourPx: TIMELINE_ROW_PX,
            labelStride: 1,
            hours: Array.from({
                length: 9
            }, (_, i) => {
                const hour = 8 + i;
                return {
                    hour,
                    label: hourLabel(hour)
                };
            }),
        };

        // Day (443:435): 8am–12pm only; each hour spans two 55px rows (110px);
        // labels 8am / 9am / 10am / 11am / 12pm on every other line.
        const DAY_TIMELINE = {
            startMin: 8 * 60,
            endMin: 12 * 60,
            hourPx: TIMELINE_ROW_PX * 2,
            labelStride: 2,
            hours: [8, 9, 10, 11, 12].map((hour) => ({
                hour,
                label: hourLabel(hour)
            })),
        };

        const parseClockToMinutes = (value) => {
            const raw = String(value || '').trim();
            if (!raw) return null;
            const match = raw.match(/(\d{1,2}):(\d{2})\s*(am|pm)?/i);
            if (!match) return null;
            let h = parseInt(match[1], 10);
            const m = parseInt(match[2], 10);
            if (!Number.isFinite(h) || !Number.isFinite(m)) return null;
            const meridiem = (match[3] || '').toLowerCase();
            if (meridiem === 'pm' && h < 12) h += 12;
            if (meridiem === 'am' && h === 12) h = 0;
            return h * 60 + m;
        };

        const formatTimelineClock = (totalMinutes) => {
            if (!Number.isFinite(totalMinutes)) return '';
            const dayMins = ((totalMinutes % (24 * 60)) + (24 * 60)) % (24 * 60);
            let h = Math.floor(dayMins / 60);
            const m = dayMins % 60;
            const suffix = h < 12 ? 'am' : 'pm';
            const h12 = h % 12 === 0 ? 12 : h % 12;
            return `${String(h12).padStart(2, '0')}:${String(m).padStart(2, '0')}${suffix}`;
        };

        const formatClientShort = (name) => {
            const parts = String(name || '').trim().split(/\s+/).filter(Boolean);
            if (!parts.length) return 'Client';
            if (parts.length === 1) return parts[0];
            return `${parts[0]} ${(parts[1][0] || '').toUpperCase()}.`;
        };

        const spaceKindTimelineLabel = (kind) => {
            switch (kind) {
                case 'Full day':
                    return 'Full-day (8 hours)';
                case 'Half-Day':
                    return 'Half-day (4 hours)';
                case 'Hourly':
                    return 'Hourly (1 hour)';
                default:
                    return kind || 'Service';
            }
        };

        window.availabilityCalendarShell = function() {
            return {
                today: null,
                selectedDate: null,
                activeView: 'month',
                isBookingsDrawerOpen: false,
                isBookingDetailOpen: false,
                selectedBooking: null,
                selectedBookingId: null,
                isSpaceAccount: false,
                bookingsByDate: {},
                workingHours: {},
                holidayDateKeys: {},
                searchQuery: '',
                drawerTopOffset: 0,
                drawerFilterDateKey: null,
                detailsBackDateKey: null,
                dayViewDate: null,
                weekdays: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                currentWeekdayIndex: 0,
                mainMonth: null,
                weekStart: null,
                init() {
                    const now = new Date();
                    this.today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    this.selectedDate = new Date(this.today);
                    this.currentWeekdayIndex = (this.today.getDay() + 6) % 7;
                    this.mainMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                    this.weekStart = new Date(this.today);
                    this.weekStart.setDate(this.today.getDate() - this.currentWeekdayIndex);
                    this.dayViewDate = new Date(this.today);
                    this._onBookingDetailsClosed = () => {
                        this.selectedBookingId = null;
                        this.isBookingDetailOpen = false;
                        // Back restores the day drawer in the same turn; X close clears leftovers after.
                        queueMicrotask(() => {
                            if (!this.isBookingsDrawerOpen) {
                                this.detailsBackDateKey = null;
                            }
                            if (typeof window.__syncAvailabilityDrawerScrollLock === 'function') {
                                window.__syncAvailabilityDrawerScrollLock();
                            }
                        });
                    };
                    window.addEventListener('booking-details-drawer-closed', this._onBookingDetailsClosed);
                    window.addEventListener('booking-details-back', (event) => this.returnToDayDrawerFromDetails(event));
                    this.drawerFilterDateKey = null;
                    this.syncDrawerTopOffset();
                    window.addEventListener('resize', () => this.syncDrawerTopOffset());
                    this.bookingsByDate = window.__availabilityCalendar?.byDate || {};
                    this.isSpaceAccount = Boolean(window.__availabilityCalendar?.isSpace);
                    this.workingHours = window.__availabilityCalendar?.workingHours || DEFAULT_WORKING_HOURS;
                    this.holidayDateKeys = expandHolidayKeys(window.__availabilityCalendar?.holidayRanges || []);
                    this.searchQuery = '';
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
                    this.detailsBackDateKey = null;
                    this.drawerFilterDateKey = null;
                    this.syncDrawerTopOffset();
                    this.isBookingsDrawerOpen = true;
                    this.$nextTick(() => {
                        if (typeof window.__syncAvailabilityDrawerScrollLock === 'function') {
                            window.__syncAvailabilityDrawerScrollLock();
                        }
                    });
                },
                openBookingsDrawerForDate(dateKey) {
                    if (!dateKey) {
                        return;
                    }
                    this.closeBookingDetail();
                    const picked = this.dateFromKey(dateKey);
                    if (picked) {
                        this.selectedDate = picked;
                        this.currentWeekdayIndex = (picked.getDay() + 6) % 7;
                    }
                    this.detailsBackDateKey = null;
                    this.drawerFilterDateKey = dateKey;
                    this.syncDrawerTopOffset();
                    this.isBookingsDrawerOpen = true;
                    this.$nextTick(() => {
                        if (typeof window.__syncAvailabilityDrawerScrollLock === 'function') {
                            window.__syncAvailabilityDrawerScrollLock();
                        }
                    });
                },
                closeBookingsDrawer() {
                    this.isBookingsDrawerOpen = false;
                    this.drawerFilterDateKey = null;
                    // Restore scroll after slide-out so the page doesn't jump mid-animation.
                    window.setTimeout(() => {
                        if (this.isBookingsDrawerOpen || this.isBookingDetailOpen) return;
                        if (typeof window.__syncAvailabilityDrawerScrollLock === 'function') {
                            window.__syncAvailabilityDrawerScrollLock();
                            return;
                        }
                        if (document.body.classList.contains('booking-details-drawer-open')) return;
                        document.body.style.overflow = '';
                    }, 280);
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

                    this.selectedDate = new Date(picked);
                    this.currentWeekdayIndex = (picked.getDay() + 6) % 7;

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
                    const backDateKey = this.drawerFilterDateKey || row.dateKey || '';
                    const daySummary = backDateKey ? this.dayDrawerSummary(backDateKey) : null;
                    const backLabel = daySummary?.title ?
                        `Back to ${daySummary.title}` :
                        (backDateKey ? 'Back to day' : '');
                    // Close day list before opening details; Back restores it.
                    this.detailsBackDateKey = backDateKey || null;
                    this.isBookingsDrawerOpen = false;
                    this.drawerFilterDateKey = null;
                    this.selectedBookingId = Number(row.id || bookingId) || null;
                    this.isBookingDetailOpen = true;
                    this.syncDrawerTopOffset();
                    window.dispatchEvent(new CustomEvent('booking-details-open', {
                        detail: {
                            id: row.id,
                            status: row.statusKey || '',
                            statusLabel: row.status || '',
                            statusClass: row.statusBadgeClass || '',
                            service: row.service || '',
                            date: row.date || '',
                            dateKey: row.dateKey || backDateKey || '',
                            time: row.time || '',
                            client: row.client || '',
                            backLabel,
                            backDateKey,
                        },
                    }));
                },
                closeBookingDetail() {
                    this.selectedBooking = null;
                    this.selectedBookingId = null;
                    this.isBookingDetailOpen = false;
                    window.dispatchEvent(new CustomEvent('booking-details-close'));
                },
                handleAvailabilityEscape() {
                    if (document.body.classList.contains('booking-details-drawer-open') || this.isBookingDetailOpen) {
                        this.closeBookingDetail();
                        return;
                    }
                    this.closeBookingsDrawer();
                },
                returnToDayDrawerFromDetails(event) {
                    const dateKey = (event && event.detail && event.detail.dateKey) || this.detailsBackDateKey;
                    this.selectedBookingId = null;
                    this.isBookingDetailOpen = false;
                    this.detailsBackDateKey = null;
                    if (dateKey) {
                        this.openBookingsDrawerForDate(dateKey);
                        return;
                    }
                    if (typeof window.__syncAvailabilityDrawerScrollLock === 'function') {
                        window.__syncAvailabilityDrawerScrollLock();
                    }
                },
                onCalendarSlotClick(slot, dateKey) {
                    if (slot.bookingId) {
                        this.openBookingDetail(slot.bookingId);
                        return;
                    }
                    if (slot.isOverflow || slot.type === 'neutral') {
                        if (dateKey) {
                            this.openBookingsDrawerForDate(dateKey);
                            return;
                        }
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
                timelineConfig(forWeek = false) {
                    if (forWeek) return WEEK_TIMELINE;
                    return this.activeView === 'day' ? DAY_TIMELINE : WEEK_TIMELINE;
                },
                get timelineHours() {
                    return this.timelineConfig(this.activeView === 'week').hours;
                },
                get timelineHourPx() {
                    return this.timelineConfig(this.activeView === 'week').hourPx;
                },
                get timelineRowCount() {
                    return TIMELINE_ROW_COUNT;
                },
                get timelineBodyHeight() {
                    return TIMELINE_ROW_COUNT * TIMELINE_ROW_PX;
                },
                get timelineRows() {
                    return Array.from({
                        length: TIMELINE_ROW_COUNT
                    }, (_, index) => ({
                        key: `row-${index}`,
                        isSpacer: index === 0,
                        isLast: index === TIMELINE_ROW_COUNT - 1,
                    }));
                },
                // Labels sit on the lines under the spacer. Day: every 2 rows; week: every row.
                timelineHourLabelTop(index) {
                    const cfg = this.timelineConfig(this.activeView === 'week');
                    return (Number(index) * cfg.labelStride + 1) * TIMELINE_ROW_PX - 2;
                },
                parseSlotStartMinutes(row, booking) {
                    const fromStart = parseClockToMinutes(booking?.timeStart);
                    if (fromStart != null) return fromStart;
                    const fromTime = parseClockToMinutes(String(booking?.time || '').split(/\s*-\s*/)[0]);
                    if (fromTime != null) return fromTime;
                    return parseClockToMinutes(row?.label);
                },
                buildTimelineEvent(row, index, dateKey, forWeek = false) {
                    const cfg = forWeek ? WEEK_TIMELINE : DAY_TIMELINE;
                    const byId = window.__availabilityCalendar?.byId || {};
                    const b = row.bookingId ? byId[String(row.bookingId)] : null;
                    const startMin = this.parseSlotStartMinutes(row, b);
                    const duration = this.bookingDurationMinutes(row.bookingId);
                    const safeStart = Number.isFinite(startMin) ? startMin : cfg.startMin;
                    const clampedStart = Math.max(cfg.startMin, Math.min(cfg.endMin - 15, safeStart));
                    const endMin = Math.min(cfg.endMin, clampedStart + Math.max(15, duration));
                    // Events start in the row below the spacer (Figma events at y≈555).
                    const top = TIMELINE_SPACER_PX + ((clampedStart - cfg.startMin) / 60) * cfg.hourPx;
                    const height = Math.max(24, ((endMin - clampedStart) / 60) * cfg.hourPx - 2);
                    const clock = formatTimelineClock(clampedStart);
                    const clientShort = formatClientShort(b?.client || 'Client');
                    const petName = b?.petName || 'Pet';
                    const service = b?.service || (this.isSpaceAccount ? '' : 'Service Type');
                    let primary = '';
                    let secondary = '';
                    if (this.isSpaceAccount) {
                        const kind = spaceKindTimelineLabel(b?.spaceKind || '');
                        primary = `${clock} · ${kind}`;
                        secondary = ` · ${petName} · ${clientShort}`;
                    } else if (forWeek) {
                        primary = `${clock} · ${service} `;
                        secondary = `· ${petName} · ${clientShort}`;
                    } else {
                        // Figma day: tall = two lines; compact = one line with · separators
                        primary = `${clock} · ${service}`;
                        secondary = `${petName} · ${clientShort}`;
                    }
                    return {
                        key: `tl-${dateKey}-${index}-${row.bookingId || 'x'}`,
                        bookingId: row.bookingId || null,
                        type: row.type || 'blue',
                        top,
                        height,
                        primary,
                        secondary,
                        line1: primary,
                        line2: secondary.trim().replace(/^·\s*/, ''),
                        // Day short bars (~25px in Figma 443:867) stay single-line; overlaps
                        // are also forced compact in dayViewSlots / weeklyGrid.
                        isCompact: height < 48,
                        isBehind: false,
                        isWeek: forWeek,
                    };
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
                    if (this.isHolidayDate(dk)) {
                        return [];
                    }
                    const byId = window.__availabilityCalendar?.byId || {};
                    const rows = this.filteredRowsForDate(dk)
                        .filter((r) => r.bookingId)
                        .filter((r) => {
                            const b = byId[String(r.bookingId)] || null;
                            const start = this.parseSlotStartMinutes(r, b);
                            // Day axis is 8am–12pm (Figma 443:435); skip later starts.
                            return start == null || start < DAY_TIMELINE.endMin;
                        })
                        .sort((a, b) => String(a.label).localeCompare(String(b.label)));
                    const slots = rows.map((r, index) => this.buildTimelineEvent(r, index, dk, false));
                    // Figma 443:864/867 — overlapping (or short) day events use one-line copy:
                    // strong + muted on the same line with · separators.
                    for (let i = 0; i < slots.length; i++) {
                        const a = slots[i];
                        const aEnd = a.top + a.height;
                        for (let j = i + 1; j < slots.length; j++) {
                            const b = slots[j];
                            const bEnd = b.top + b.height;
                            if (a.top < bEnd && b.top < aEnd) {
                                a.isCompact = true;
                                b.isCompact = true;
                            }
                        }
                    }
                    return slots;
                },
                get statsPeriodLabel() {
                    return this.activeView === 'month' ? 'This month' : 'This week';
                },
                get statusCounts() {
                    // Figma: Week + Day stats pills read "This week"
                    if (this.activeView !== 'month' && this.weekStart) {
                        return this.countStatusesInRange(this.weekStart, 7);
                    }
                    return this.monthStatusCounts;
                },
                countStatusesInRange(startDate, dayCount) {
                    const counts = {
                        confirmed: 0,
                        pending: 0,
                        completed: 0,
                    };
                    if (!startDate) return counts;
                    const byId = window.__availabilityCalendar?.byId || {};
                    for (let i = 0; i < dayCount; i++) {
                        const d = new Date(startDate);
                        d.setDate(startDate.getDate() + i);
                        const key = this.dateKey(d);
                        (this.bookingsByDate[key] || []).forEach((r) => {
                            if (!r.bookingId) return;
                            const b = byId[String(r.bookingId)];
                            const status = String(b?.status || '').toLowerCase();
                            if (status === 'confirmed') counts.confirmed++;
                            else if (status === 'pending') counts.pending++;
                            else if (status === 'completed') counts.completed++;
                        });
                    }
                    return counts;
                },
                get dayViewIsTimeOff() {
                    if (!this.dayViewDate) return false;
                    const y = this.dayViewDate.getFullYear();
                    const m = this.dayViewDate.getMonth();
                    const d = this.dayViewDate.getDate();
                    const dk =
                        `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    return this.isHolidayDate(dk);
                },
                get dayViewHeaderLabel() {
                    if (!this.dayViewDate) {
                        return {
                            weekday: '',
                            date: ''
                        };
                    }
                    const weekday = this.weekdays[(this.dayViewDate.getDay() + 6) % 7] || '';
                    const day = this.dayViewDate.getDate();
                    const month = MONTHS[this.dayViewDate.getMonth()] || '';
                    return {
                        weekday: weekday.slice(0, 3),
                        date: `${day} ${month}`,
                    };
                },
                selectWeekDay(dateKey) {
                    const picked = this.dateFromKey(dateKey);
                    if (picked) {
                        this.selectedDate = picked;
                        this.currentWeekdayIndex = (picked.getDay() + 6) % 7;
                    }
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
                syncWeekStartToDate(dateObj) {
                    if (!dateObj) return;
                    const mondayIndex = (dateObj.getDay() + 6) % 7;
                    this.weekStart = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate() - mondayIndex);
                    this.currentWeekdayIndex = mondayIndex;
                },
                prevPeriod() {
                    if (this.activeView === 'day' && this.dayViewDate) {
                        const d = new Date(this.dayViewDate);
                        d.setDate(d.getDate() - 1);
                        this.dayViewDate = d;
                        this.selectedDate = new Date(d);
                        this.syncWeekStartToDate(d);
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
                        this.selectedDate = new Date(d);
                        this.syncWeekStartToDate(d);
                        return;
                    }
                    if (this.activeView === 'week') {
                        this.nextWeek();
                        return;
                    }

                    this.nextMainMonth();
                },
                isSameDay(a, b) {
                    if (!a || !b) return false;
                    return a.getFullYear() === b.getFullYear() &&
                        a.getMonth() === b.getMonth() &&
                        a.getDate() === b.getDate();
                },
                isToday(dateObj) {
                    return this.isSameDay(dateObj, this.today);
                },
                isSelectedDate(dateObj) {
                    return this.isSameDay(dateObj, this.selectedDate);
                },
                syncMiniCalendarToDate(dateObj) {
                    if (!dateObj) return;

                    const detail = {
                        year: dateObj.getFullYear(),
                        monthIndex: dateObj.getMonth(),
                        day: dateObj.getDate(),
                    };
                    this.$dispatch('availability-sync-date', detail);

                    const miniEl = this.$el?.querySelector?.('.availability-mini-calendar');
                    if (!miniEl || !window.Alpine?.$data) {
                        return;
                    }

                    const mini = window.Alpine.$data(miniEl);
                    if (!mini) {
                        return;
                    }

                    if (typeof mini.syncFromShell === 'function') {
                        mini.syncFromShell(detail);
                        return;
                    }

                    mini.selectedDate = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate());
                    mini.miniMonth = new Date(dateObj.getFullYear(), dateObj.getMonth(), 1);
                },
                goToToday() {
                    const now = new Date();
                    const today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    this.today = today;
                    this.selectedDate = new Date(today);
                    this.mainMonth = new Date(today.getFullYear(), today.getMonth(), 1);
                    this.dayViewDate = new Date(today);
                    const mondayIndex = (today.getDay() + 6) % 7;
                    this.weekStart = new Date(today.getFullYear(), today.getMonth(), today.getDate() - mondayIndex);
                    this.currentWeekdayIndex = mondayIndex;
                    this.closeBookingDetail();
                    this.closeBookingsDrawer();
                    this.syncMiniCalendarToDate(today);
                },
                dateKey(dateObj) {
                    return `${dateObj.getFullYear()}-${pad2(dateObj.getMonth() + 1)}-${pad2(dateObj.getDate())}`;
                },
                isClosedDay(dateObj) {
                    if (!dateObj) return false;
                    const key = WEEKDAY_KEYS[dateObj.getDay()];
                    return !(this.workingHours?.[key]?.status);
                },
                isHolidayDate(dateKey) {
                    return Boolean(this.holidayDateKeys?.[dateKey]);
                },
                slotMatchesSearch(row) {
                    const q = (this.searchQuery || '').trim().toLowerCase();
                    if (!q) return true;
                    const booking = row.bookingId ?
                        (window.__availabilityCalendar?.byId?.[String(row.bookingId)] || null) :
                        null;
                    const hay = [
                        row.label,
                        booking?.client,
                        booking?.petName,
                        booking?.petType,
                        booking?.service,
                        booking?.time,
                        booking?.status,
                    ].filter(Boolean).join(' ').toLowerCase();
                    return hay.includes(q);
                },
                holidayMatchesSearch() {
                    return !(this.searchQuery || '').trim();
                },
                filteredRowsForDate(dateKey) {
                    const rows = this.bookingsByDate[dateKey] || [];
                    return rows.filter((row) => this.slotMatchesSearch(row));
                },
                hourSlotsForDate(dateObj) {
                    const key = WEEKDAY_KEYS[dateObj.getDay()];
                    const hours = this.workingHours?.[key];
                    if (!hours?.status) return [];
                    const startParts = String(hours.start || '10:00').split(':');
                    const endParts = String(hours.end || '18:00').split(':');
                    const start = (parseInt(startParts[0], 10) || 0) * 60 + (parseInt(startParts[1], 10) || 0);
                    const end = (parseInt(endParts[0], 10) || 0) * 60 + (parseInt(endParts[1], 10) || 0);
                    const out = [];
                    for (let minutes = start; minutes + 60 <= end; minutes += 60) {
                        out.push(`${pad2(Math.floor(minutes / 60))}:${pad2(minutes % 60)}`);
                    }
                    return out;
                },
                countOpenSlotsForMonth(year, monthIndex) {
                    const daysInMonth = new Date(year, monthIndex + 1, 0).getDate();
                    let open = 0;
                    for (let day = 1; day <= daysInMonth; day++) {
                        const dateObj = new Date(year, monthIndex, day);
                        const key = this.dateKey(dateObj);
                        if (this.isClosedDay(dateObj) || this.isHolidayDate(key)) {
                            continue;
                        }
                        const capacity = this.hourSlotsForDate(dateObj).length;
                        const booked = (this.bookingsByDate[key] || []).filter((row) => row.type !== 'red').length;
                        open += Math.max(0, capacity - booked);
                    }
                    return open;
                },
                nextFreeSlotLabel() {
                    const start = this.today || new Date();
                    const nowMinutes = start.getHours() * 60 + start.getMinutes();
                    for (let offset = 0; offset < 90; offset++) {
                        const dateObj = new Date(start.getFullYear(), start.getMonth(), start.getDate() + offset);
                        const key = this.dateKey(dateObj);
                        if (this.isClosedDay(dateObj) || this.isHolidayDate(key)) {
                            continue;
                        }
                        const slots = this.hourSlotsForDate(dateObj);
                        if (!slots.length) {
                            continue;
                        }
                        const bookedCount = (this.bookingsByDate[key] || []).filter((row) => row.type !== 'red')
                            .length;
                        let index = bookedCount;
                        if (offset === 0) {
                            while (index < slots.length) {
                                const parts = String(slots[index]).split(':');
                                const slotMinutes = (parseInt(parts[0], 10) || 0) * 60 + (parseInt(parts[1], 10) || 0);
                                if (slotMinutes > nowMinutes) {
                                    break;
                                }
                                index++;
                            }
                        }
                        if (index >= slots.length) {
                            continue;
                        }
                        const time = slots[index];
                        return `${WEEKDAY_LONG[dateObj.getDay()]} ${dateObj.getDate()} ${MONTHS_SHORT[dateObj.getMonth()]}, ${time}`;
                    }
                    return '';
                },
                get availabilityStatusMeta() {
                    const ref = this.activeView === 'month' && this.mainMonth ? this.mainMonth : (this.today || new Date());
                    const open = this.countOpenSlotsForMonth(ref.getFullYear(), ref.getMonth());
                    const openText = `${open} open slot${open === 1 ? '' : 's'} this month`;
                    const next = this.nextFreeSlotLabel();
                    return next ? `${openText} · next free slot ${next}` : openText;
                },
                workingMinutesForDate(dateObj) {
                    if (!dateObj) return 0;
                    const key = WEEKDAY_KEYS[dateObj.getDay()];
                    const hours = this.workingHours?.[key];
                    if (!hours?.status) return 0;
                    const [sh, sm] = String(hours.start || '10:00').split(':');
                    const [eh, em] = String(hours.end || '18:00').split(':');
                    const start = (parseInt(sh, 10) || 0) * 60 + (parseInt(sm, 10) || 0);
                    const end = (parseInt(eh, 10) || 0) * 60 + (parseInt(em, 10) || 0);
                    return Math.max(0, end - start);
                },
                workingWindowForDate(dateObj) {
                    if (!dateObj) return null;
                    const key = WEEKDAY_KEYS[dateObj.getDay()];
                    const hours = this.workingHours?.[key];
                    if (!hours?.status) return null;
                    return {
                        start: String(hours.start || '10:00').slice(0, 5),
                        end: String(hours.end || '18:00').slice(0, 5),
                    };
                },
                dateFromKey(dateKey) {
                    if (!dateKey) return null;
                    const parts = String(dateKey).split('-').map((x) => parseInt(x, 10));
                    if (parts.length !== 3 || parts.some((n) => Number.isNaN(n))) return null;
                    return new Date(parts[0], parts[1] - 1, parts[2]);
                },
                bookingDurationMinutes(bookingId) {
                    if (!bookingId) return 60;
                    const byId = window.__availabilityCalendar?.byId || {};
                    const b = byId[String(bookingId)];
                    const raw = Number(b?.durationMinutes);
                    if (Number.isFinite(raw) && raw > 0) return raw;
                    return 60;
                },
                dayCapacitySummary(dateKey) {
                    const blank = {
                        isEmpty: true,
                        isTimeOff: false,
                        summary: '',
                        segments: [],
                        barLayers: [],
                        freeHours: 0,
                        bookedCount: 0,
                        workingMinutes: 0,
                    };
                    const timeOff = {
                        isEmpty: false,
                        isTimeOff: true,
                        summary: 'Time off',
                        segments: [],
                        barLayers: [],
                        freeHours: 0,
                        bookedCount: 0,
                        workingMinutes: 0,
                    };
                    const d = this.dateFromKey(dateKey);
                    if (!d) return blank;
                    const isHoliday = this.isHolidayDate(dateKey);
                    const isWorking = !this.isClosedDay(d);
                    // Closed weekdays/weekends stay blank; holidays show Time off
                    if (isHoliday) return timeOff;
                    if (!isWorking) return blank;
                    const workingMinutes = this.workingMinutesForDate(d);
                    if (workingMinutes <= 0) return blank;
                    const rows = (this.bookingsByDate[dateKey] || []).filter((r) => r.type !== 'red');
                    // Figma order left→right: completed (blue) → confirmed (green) → pending (orange)
                    const order = ['blue', 'green', 'orange'];
                    const minutesByType = {
                        blue: 0,
                        green: 0,
                        orange: 0,
                    };
                    let bookedMinutes = 0;
                    rows.forEach((r) => {
                        const dur = Math.max(15, this.bookingDurationMinutes(r.bookingId));
                        bookedMinutes += dur;
                        const t = order.includes(r.type) ? r.type : 'green';
                        minutesByType[t] += dur;
                    });
                    // Cap total fill at 100%; keep relative status share
                    const totalBooked = Math.min(bookedMinutes, workingMinutes);
                    const scale = bookedMinutes > 0 ? totalBooked / bookedMinutes : 0;
                    let left = 0;
                    const barLayers = [];
                    const segments = [];
                    order.forEach((type) => {
                        if (minutesByType[type] <= 0) return;
                        const width = Math.min(
                            100 - left,
                            (minutesByType[type] * scale / workingMinutes) * 100,
                        );
                        if (width <= 0.25) return;
                        // Match Figma: each status is its own rounded pill at cumulative left %
                        barLayers.push({
                            type,
                            left: Number(left.toFixed(3)),
                            width: Number(width.toFixed(3)),
                        });
                        segments.push({
                            type,
                            pct: Number(width.toFixed(3)),
                        });
                        left += width;
                    });
                    const bookedCount = rows.length;
                    const freeMinutes = Math.max(0, workingMinutes - bookedMinutes);
                    const freeHours = Math.max(0, Math.round(freeMinutes / 60));
                    return {
                        isEmpty: false,
                        isTimeOff: false,
                        summary: `${bookedCount} booked · ${freeHours}h free`,
                        segments,
                        barLayers,
                        freeHours,
                        bookedCount,
                        workingMinutes,
                    };
                },
                get monthStatusCounts() {
                    const counts = {
                        confirmed: 0,
                        pending: 0,
                        completed: 0,
                    };
                    if (!this.mainMonth) return counts;
                    const year = this.mainMonth.getFullYear();
                    const month = this.mainMonth.getMonth();
                    const daysInMonth = new Date(year, month + 1, 0).getDate();
                    const byId = window.__availabilityCalendar?.byId || {};
                    for (let day = 1; day <= daysInMonth; day++) {
                        const key = this.dateKey(new Date(year, month, day));
                        const rows = this.bookingsByDate[key] || [];
                        rows.forEach((r) => {
                            if (!r.bookingId) return;
                            const b = byId[String(r.bookingId)];
                            const status = String(b?.status || '').toLowerCase();
                            if (status === 'confirmed') counts.confirmed++;
                            else if (status === 'pending') counts.pending++;
                            else if (status === 'completed') counts.completed++;
                        });
                    }
                    return counts;
                },
                formatNextFreeSlotFigma() {
                    const WEEKDAYS_SHORT = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                    const start = this.today || new Date();
                    const nowMinutes = start.getHours() * 60 + start.getMinutes();
                    for (let offset = 0; offset < 90; offset++) {
                        const dateObj = new Date(start.getFullYear(), start.getMonth(), start.getDate() + offset);
                        const key = this.dateKey(dateObj);
                        if (this.isClosedDay(dateObj) || this.isHolidayDate(key)) continue;
                        const slots = this.hourSlotsForDate(dateObj);
                        if (!slots.length) continue;
                        const workingMinutes = this.workingMinutesForDate(dateObj);
                        const rows = (this.bookingsByDate[key] || []).filter((r) => r.type !== 'red');
                        let bookedMinutes = 0;
                        rows.forEach((r) => {
                            bookedMinutes += Math.max(15, this.bookingDurationMinutes(r.bookingId));
                        });
                        if (bookedMinutes >= workingMinutes) continue;
                        let index = rows.length;
                        if (offset === 0) {
                            while (index < slots.length) {
                                const parts = String(slots[index]).split(':');
                                const slotMinutes = (parseInt(parts[0], 10) || 0) * 60 + (parseInt(parts[
                                    1], 10) || 0);
                                if (slotMinutes > nowMinutes) break;
                                index++;
                            }
                        }
                        if (index >= slots.length) continue;
                        const time = slots[index];
                        const freeHours = Math.max(1, Math.round((workingMinutes - bookedMinutes) / 60));
                        return `${WEEKDAYS_SHORT[dateObj.getDay()]} ${dateObj.getDate()} ${MONTHS_SHORT[dateObj.getMonth()]} · ${time} (${freeHours}h)`;
                    }
                    return '—';
                },
                get nextFreeFigmaLabel() {
                    return this.formatNextFreeSlotFigma();
                },
                dayDrawerSummary(dateKey) {
                    const capacity = this.dayCapacitySummary(dateKey);
                    const d = this.dateFromKey(dateKey);
                    const WEEKDAYS_LONG = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday',
                        'Saturday'
                    ];
                    const MONTHS_FULL = ['January', 'February', 'March', 'April', 'May', 'June', 'July',
                        'August', 'September', 'October', 'November', 'December'
                    ];
                    const title = d ?
                        `${d.getDate()} ${MONTHS_FULL[d.getMonth()]} ${d.getFullYear()}` :
                        'Bookings';
                    const weekday = d ? WEEKDAYS_LONG[d.getDay()] : '';
                    const subtitle = capacity.isEmpty ?
                        'No working hours' :
                        capacity.isTimeOff ?
                        'Time off' :
                        capacity.summary;
                    const window = d ? this.workingWindowForDate(d) : null;
                    const windowLabel = window ? `${window.start} - ${window.end}` : '';
                    return {
                        title,
                        weekday,
                        subtitle,
                        isEmpty: !!capacity.isEmpty,
                        isTimeOff: capacity.isTimeOff,
                        freeHours: capacity.freeHours,
                        bookedCount: capacity.bookedCount,
                        windowLabel,
                        hasFreeTime: !capacity.isTimeOff && capacity.freeHours > 0,
                    };
                },
                get currentDayDrawerSummary() {
                    if (!this.drawerFilterDateKey) return null;
                    return this.dayDrawerSummary(this.drawerFilterDateKey);
                },
                get dayDrawerTimelineCards() {
                    if (!this.drawerFilterDateKey) return [];
                    const dk = this.drawerFilterDateKey;
                    const byId = window.__availabilityCalendar?.byId || {};
                    const spaceIcons = window.__availabilityCalendar?.spaceIcons || {};
                    const providerSpaceType = window.__availabilityCalendar?.providerSpaceType || '';
                    const selectedId = this.selectedBookingId != null ? Number(this.selectedBookingId) : null;
                    const rows = (this.bookingsByDate[dk] || []).slice();
                    rows.sort((a, b) => String(a.label || '').localeCompare(String(b.label || '')));
                    return rows
                        .filter((r) => r.bookingId && byId[String(r.bookingId)])
                        .map((r) => {
                            const b = byId[String(r.bookingId)];
                            let timeStart = String(b.timeStart || '').trim();
                            if (!timeStart) {
                                const timeRaw = String(b.time || '').trim();
                                timeStart = timeRaw.includes('-') ? timeRaw.split(/\s*-\s*/)[0] : timeRaw;
                            }
                            // Figma 437:1047 shows HH:MM without seconds.
                            if (/^\d{1,2}:\d{2}/.test(timeStart)) {
                                timeStart = timeStart.slice(0, 5);
                            }
                            const statusKey = String(b.statusKey || '').toLowerCase() || 'default';
                            const petLine = b.petBreed ?
                                `${b.petName} (${b.petBreed})` :
                                (b.petType ? `${b.petName} (${b.petType})` : b.petName);
                            const spaceDurationIcon = (() => {
                                switch (b.spaceKind) {
                                    case 'Full day':
                                        return spaceIcons.fullDay || '';
                                    case 'Half-Day':
                                        return spaceIcons.halfDay || '';
                                    case 'Hourly':
                                        return spaceIcons.hourly || '';
                                    default:
                                        return '';
                                }
                            })();
                            const mins = Number(b.durationMinutes || 0);
                            return {
                                bookingId: b.id,
                                timeStart: timeStart || '—',
                                durationMinutesLabel: b.durationMinutesLabel || (mins > 0 ? `${mins} min` : ''),
                                statusKey,
                                statusLabel: b.status || '—',
                                visitTypeLabel: b.visitTypeLabel || '',
                                hasAddOns: !!b.hasAddOns,
                                service: b.service || '',
                                petLine,
                                client: formatClientShort(b.client || 'Client'),
                                isSelected: selectedId != null && Number(b.id) === selectedId,
                                spaceKindHoursLabel: b.spaceKindHoursLabel || '',
                                spaceDurationIcon,
                                providerSpaceType: b.providerSpaceType || providerSpaceType || 'Space',
                                shedIcon: spaceIcons.shed || '',
                            };
                        });
                },
                goToManageAvailability() {
                    this.closeBookingsDrawer();
                    this.closeBookingDetail();
                    try {
                        window.dispatchEvent(new CustomEvent('nav-list-loading-start'));
                        if (window.Alpine?.$data) {
                            const data = window.Alpine.$data(document.body);
                            if (data) {
                                data.activeSection = 'manage-availability';
                            }
                        }
                    } catch (err) {}
                },
                buildMonthGrid(baseDate, includeSlots = false, includeCapacity = false) {
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
                                this.getSlotsForDate(dateObj.getFullYear(), dateObj.getMonth(), day) : [],
                            capacity: includeCapacity && inCurrentMonth ?
                                this.dayCapacitySummary(dateKey) : {
                                    isEmpty: true,
                                    isTimeOff: false,
                                    summary: '',
                                    segments: [],
                                    barLayers: [],
                                    freeHours: 0,
                                    bookedCount: 0,
                                    workingMinutes: 0,
                                },
                        });
                    }

                    return cells;
                },
                get mainMonthGrid() {
                    return this.buildMonthGrid(this.mainMonth, false, true);
                },
                get weeklyGrid() {
                    return Array.from({
                        length: 7
                    }, (_, index) => {
                        const dateObj = new Date(this.weekStart);
                        dateObj.setDate(this.weekStart.getDate() + index);
                        const dayLabel = this.weekdays[index] || '';
                        const dateKey =
                            `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-${String(dateObj.getDate()).padStart(2, '0')}`;
                        const isTimeOff = this.isHolidayDate(dateKey);
                        const rows = isTimeOff ? [] : this.filteredRowsForDate(dateKey)
                            .filter((r) => r.bookingId)
                            .sort((a, b) => String(a.label).localeCompare(String(b.label)));
                        const slots = rows.map((r, slotIdx) => this.buildTimelineEvent(r, slotIdx, dateKey, true));
                        // Sort by start so earlier events paint underneath later ones.
                        slots.sort((a, b) => a.top - b.top || b.height - a.height);
                        // Only the underneath event is compact + top-aligned; the front
                        // card keeps stacked primary/muted so muted text stays visible.
                        for (let i = 0; i < slots.length; i++) {
                            const a = slots[i];
                            const aEnd = a.top + a.height;
                            for (let j = i + 1; j < slots.length; j++) {
                                const b = slots[j];
                                const bEnd = b.top + b.height;
                                if (a.top < bEnd && b.top < aEnd) {
                                    a.isBehind = true;
                                    a.isCompact = true;
                                }
                            }
                        }

                        return {
                            key: dateKey,
                            label: dayLabel,
                            weekdayShort: dayLabel.slice(0, 3),
                            day: dateObj.getDate(),
                            isToday: this.isToday(dateObj),
                            isSelected: this.isSelectedDate(dateObj),
                            isMuted: this.isClosedDay(dateObj) && !isTimeOff,
                            isTimeOff,
                            slots,
                            dateKey,
                        };
                    });
                },
                getSlotsForDate(year, monthIndex, day) {
                    const dateKey =
                        `${year}-${String(monthIndex + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                    const rows = this.filteredRowsForDate(dateKey);
                    const sorted = [...rows].sort((a, b) => String(a.label).localeCompare(String(b.label)));
                    const out = [];
                    if (this.isHolidayDate(dateKey) && this.holidayMatchesSearch()) {
                        out.push({
                            label: 'Time off',
                            type: 'holiday',
                            bookingId: null,
                        });
                    }
                    const maxShow = 2;
                    const remaining = Math.max(0, maxShow - out.length);
                    sorted.slice(0, remaining).forEach((r) => {
                        out.push({
                            label: r.label,
                            type: r.type,
                            bookingId: r.bookingId,
                        });
                    });
                    const extra = sorted.length - remaining;
                    if (extra > 0) {
                        out.push({
                            label: String(extra),
                            type: 'neutral',
                            bookingId: null,
                            isOverflow: true,
                        });
                    }
                    return out;
                },
            };
        };
    })();
</script>
