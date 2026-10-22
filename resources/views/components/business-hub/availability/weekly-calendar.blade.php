<section class="availability-weekly-shell">
    <div class="availability-weekly-card">
        <div class="availability-weekly-table">
            <div class="availability-weekly-gutter">
                <div class="availability-weekly-gutter-head" aria-hidden="true"></div>
                <div class="availability-weekly-gutter-hours" aria-hidden="true"
                    :style="`height: ${timelineBodyHeight}px`">
                    <template x-for="row in timelineRows" :key="'wg-' + row.key">
                        <div class="availability-weekly-gutter-cell"
                            :class="{ 'is-spacer': row.isSpacer, 'is-last': row.isLast }"></div>
                    </template>
                    <template x-for="(hour, hourIndex) in timelineHours" :key="'wh-' + hour.hour">
                        <span class="availability-weekly-hour-label"
                            :style="`top: ${timelineHourLabelTop(hourIndex)}px`"
                            x-text="hour.label"></span>
                    </template>
                </div>
            </div>

            <div class="availability-weekly-days">
                <template x-for="day in weeklyGrid" :key="day.key">
                    <article class="availability-weekly-day"
                        :class="{
                            'is-selected': day.isSelected,
                            'is-today': day.isToday,
                            'is-time-off': day.isTimeOff,
                            'is-muted': day.isMuted,
                        }">
                        <button type="button" class="availability-weekly-day-head"
                            @click="selectWeekDay(day.dateKey)">
                            <span class="availability-weekly-head-weekday" x-text="day.weekdayShort"></span>
                            <span class="availability-weekly-head-day" x-text="day.day"></span>
                        </button>

                        <div class="availability-weekly-day-body"
                            :style="`height: ${timelineBodyHeight}px`"
                            @click.self="selectWeekDay(day.dateKey)">
                            <div class="availability-weekly-rows" aria-hidden="true">
                                <template x-for="row in timelineRows" :key="day.key + '-' + row.key">
                                    <div class="availability-weekly-row"
                                        :class="{ 'is-spacer': row.isSpacer, 'is-last': row.isLast }"></div>
                                </template>
                            </div>

                            <div class="availability-weekly-timeoff" x-show="day.isTimeOff" x-cloak>
                                <span class="availability-weekly-timeoff-main">All Day · Time Off · </span>
                                <span class="availability-weekly-timeoff-sub">Other</span>
                            </div>

                            <template x-for="slot in day.slots" :key="slot.key">
                                <button type="button" class="availability-weekly-event"
                                    :class="['is-' + slot.type, slot.bookingId ? 'is-clickable' : '', slot.isCompact ? 'is-compact' : '', slot.isBehind ? 'is-behind' : '']"
                                    :style="`top: ${slot.top}px; height: ${slot.height}px; z-index: ${slot.isBehind ? 2 : 3}`"
                                    @click.stop="onCalendarSlotClick(slot, day.dateKey)">
                                    <span class="availability-weekly-event-text">
                                        <span class="availability-weekly-event-strong" x-text="slot.primary"></span>
                                        <span class="availability-weekly-event-muted" x-show="slot.secondary"
                                            x-text="slot.secondary"></span>
                                    </span>
                                </button>
                            </template>
                        </div>
                    </article>
                </template>
            </div>
        </div>
    </div>
</section>

<style>
    .availability-weekly-shell {
        padding-top: 2px;
    }

    .availability-weekly-card {
        border: 1px solid #E2E2E2;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
    }

    .availability-weekly-table {
        display: grid;
        grid-template-columns: 57px minmax(0, 1fr);
        align-items: stretch;
    }

    .availability-weekly-gutter {
        position: relative;
        background: #F9F8F4;
        overflow: visible;
    }

    .availability-weekly-gutter-head {
        height: 55px;
        border-right: 1px solid #E2E2E2;
        border-bottom: 1px solid #E2E2E2;
        background: #F9FAFC;
        box-sizing: border-box;
    }

    .availability-weekly-gutter-hours {
        position: relative;
        overflow: visible;
        display: flex;
        flex-direction: column;
        z-index: 1;
    }

    /* Figma: each hour is its own 57×55 bordered cell */
    .availability-weekly-gutter-cell {
        height: 55px;
        border-right: 1px solid #E2E2E2;
        border-bottom: 1px solid #E2E2E2;
        background: #F9F8F4;
        box-sizing: border-box;
        flex-shrink: 0;
    }

    .availability-weekly-gutter-cell.is-last {
        border-bottom: none;
    }

    /* Figma: label centered on the hour grid line; cream chip masks the stroke */
    .availability-weekly-hour-label {
        position: absolute;
        left: 50%;
        transform: translate(-50%, -50%);
        z-index: 3;
        color: #888888;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 400;
        line-height: 20px;
        white-space: nowrap;
        background: #F9F8F4;
        padding: 0 4px;
        pointer-events: none;
    }

    .availability-weekly-days {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        min-width: 0;
    }

    .availability-weekly-day {
        position: relative;
        min-width: 0;
        border-right: 1px solid #E2E2E2;
        background: #fff;
        display: flex;
        flex-direction: column;
    }

    .availability-weekly-day:last-child {
        border-right: none;
    }

    .availability-weekly-day-head {
        position: relative;
        appearance: none;
        width: 100%;
        height: 55px;
        margin: 0;
        padding: 8px 4px 6px;
        border: none;
        border-bottom: 1px solid #E2E2E2;
        background: #F9FAFC;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 2px;
        cursor: pointer;
    }

    .availability-weekly-day-head:focus,
    .availability-weekly-day-head:focus-visible {
        outline: none;
    }

    .availability-weekly-head-weekday {
        color: #9C9790;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: 20px;
    }

    .availability-weekly-head-day {
        color: #9C9790;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 20px;
        font-weight: 700;
        line-height: 20px;
    }

    /* Figma: only the selected column header goes dark */
    .availability-weekly-day.is-selected .availability-weekly-head-weekday,
    .availability-weekly-day.is-selected .availability-weekly-head-day {
        color: #3B3731;
    }

    .availability-weekly-day-body {
        position: relative;
        overflow: hidden;
        flex: 1;
        background: #fff;
    }

    .availability-weekly-day.is-muted .availability-weekly-day-body {
        background: #fff;
    }

    .availability-weekly-day.is-time-off .availability-weekly-day-body {
        background: #fff;
    }

    /* Figma 443:421 — selection outline inset 2px inside the hour grid, not on the header */
    .availability-weekly-day.is-selected .availability-weekly-day-body::before {
        content: '';
        position: absolute;
        inset: 2px;
        z-index: 3;
        border: 1px solid var(--availability-accent, #FFC97A);
        border-radius: 2px;
        background: rgba(255, 201, 122, 0.05);
        pointer-events: none;
        box-sizing: border-box;
    }

    .availability-layout.is-space .availability-weekly-day.is-selected .availability-weekly-day-body::before {
        background: rgba(255, 168, 153, 0.06);
        border-color: var(--availability-accent, #FFA899);
    }

    .availability-weekly-rows {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        pointer-events: none;
    }

    .availability-weekly-row {
        height: 55px;
        border-bottom: 1px solid #E2E2E2;
        box-sizing: border-box;
        background: #fff;
    }

    .availability-weekly-day.is-muted .availability-weekly-row {
        background: #FBFBFB;
    }

    .availability-weekly-day.is-time-off .availability-weekly-row {
        background: #fff;
    }

    .availability-weekly-row.is-last {
        border-bottom: none;
        background: #FBFBFB;
    }

    .availability-weekly-day.is-muted .availability-weekly-row.is-last,
    .availability-weekly-day.is-time-off .availability-weekly-row.is-last {
        background: #FBFBFB;
    }

    /* Figma 443:385 — inset ~5px, nearly full body height, top accent strip */
    .availability-weekly-timeoff {
        position: absolute;
        top: 5px;
        left: 5px;
        right: 5px;
        bottom: 5px;
        z-index: 1;
        padding: 12px 8px 8px;
        border-radius: 5px;
        background: #F1F1F1;
        border-top: 5px solid #9D9B98;
        pointer-events: none;
        font-family: Lato, sans-serif;
        font-size: 12px;
        font-weight: 600;
        line-height: 1.35;
        box-sizing: border-box;
    }

    .availability-weekly-timeoff-main {
        color: #3B3731;
    }

    .availability-weekly-timeoff-sub {
        color: #9D9B98;
    }

    .availability-weekly-event {
        position: absolute;
        left: 5px;
        right: 5px;
        z-index: 3;
        appearance: none;
        border: none;
        border-left: 3px solid #7AB7E3;
        border-radius: 5px;
        padding: 4px 6px 4px 7px;
        margin: 0;
        overflow: hidden;
        text-align: left;
        cursor: default;
        display: block;
        box-sizing: border-box;
        background: #F5F8FA;
    }

    .availability-weekly-event:focus,
    .availability-weekly-event:focus-visible {
        outline: none;
    }

    .availability-weekly-event.is-clickable {
        cursor: pointer;
    }

    .availability-weekly-event.is-clickable:hover {
        filter: brightness(0.985);
    }

    .availability-weekly-event-text {
        display: block;
        min-width: 0;
    }

    .availability-weekly-event-strong,
    .availability-weekly-event-muted {
        display: block;
        font-family: Lato, sans-serif;
        font-size: 12px;
        line-height: 1.3;
        word-break: break-word;
    }

    .availability-weekly-event-strong {
        color: #3B3731;
        font-weight: 600;
    }

    .availability-weekly-event-muted {
        color: #9D9B98;
        font-weight: 400;
    }

    .availability-weekly-event.is-compact .availability-weekly-event-text {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .availability-weekly-event.is-compact .availability-weekly-event-strong,
    .availability-weekly-event.is-compact .availability-weekly-event-muted {
        display: inline;
    }

    /* Only the event underneath another card: pin title to the top */
    .availability-weekly-event.is-behind {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        z-index: 2;
        padding-top: 3px;
        padding-bottom: 2px;
    }

    .availability-weekly-event.is-behind .availability-weekly-event-text {
        display: block;
        align-self: flex-start;
        width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .availability-weekly-event.is-behind .availability-weekly-event-strong,
    .availability-weekly-event.is-behind .availability-weekly-event-muted {
        display: inline;
        vertical-align: top;
    }

    .availability-weekly-event.is-behind .availability-weekly-event-strong {
        align-self: flex-start;
    }

    .availability-weekly-event.is-blue {
        background: #F5F8FA;
        border-left-color: #7AB7E3;
    }

    .availability-weekly-event.is-green {
        background: #F4F8EC;
        border-left-color: #B5DB65;
    }

    .availability-weekly-event.is-orange {
        background: #FFF6E9;
        border-left-color: #FFC97A;
    }

    .availability-weekly-event.is-red {
        background: #FFE2E2;
        border-left-color: #FF6E6E;
        text-decoration: line-through;
    }

    @media (max-width: 1200px) {

        .availability-weekly-event-strong,
        .availability-weekly-event-muted {
            font-size: 11px;
        }
    }
</style>
