<section class="availability-day-shell">
    <div class="availability-day-card">
        {{-- Figma 443:717 — "Wed" + "15 February" --}}
        <header class="availability-day-header">
            <span class="availability-day-header-weekday" x-text="dayViewHeaderLabel.weekday"></span>
            <span class="availability-day-header-date" x-text="dayViewHeaderLabel.date"></span>
        </header>

        <div class="availability-day-body" :style="`--timeline-body-h: ${timelineBodyHeight}px`">
            <div class="availability-day-hours" aria-hidden="true"
                :style="`height: ${timelineBodyHeight}px`">
                <template x-for="row in timelineRows" :key="'dg-' + row.key">
                    <div class="availability-day-gutter-cell"
                        :class="{ 'is-spacer': row.isSpacer, 'is-last': row.isLast }"></div>
                </template>
                <template x-for="(hour, hourIndex) in timelineHours" :key="'dh-' + hour.hour">
                    <span class="availability-day-hour-label"
                        :style="`top: ${timelineHourLabelTop(hourIndex)}px`"
                        x-text="hour.label"></span>
                </template>
            </div>

            <div class="availability-day-track" :class="{ 'is-time-off': dayViewIsTimeOff }">
                <div class="availability-day-rows" aria-hidden="true">
                    <template x-for="row in timelineRows" :key="'d' + row.key">
                        <div class="availability-day-row"
                            :class="{ 'is-spacer': row.isSpacer, 'is-last': row.isLast }"></div>
                    </template>
                </div>

                <div class="availability-day-timeoff" x-show="dayViewIsTimeOff" x-cloak>
                    <span class="availability-day-timeoff-main">All Day · Time Off · </span>
                    <span class="availability-day-timeoff-sub">Other</span>
                </div>

                <template x-for="slot in dayViewSlots" :key="slot.key">
                    <button type="button" class="availability-day-event"
                        :class="['is-' + slot.type, slot.bookingId ? 'is-clickable' : '', (slot.isCompact || isSpaceAccount) ? 'is-compact' : '']"
                        :style="`top: ${slot.top}px; height: ${slot.height}px`"
                        @click.stop="onCalendarSlotClick(slot)">
                        {{-- Figma 443:864/867: short/overlapping = one line; tall = stacked --}}
                        <span
                            :class="(slot.isCompact || isSpaceAccount) ? 'availability-day-event-inline' : 'availability-day-event-stack'">
                            <span class="availability-day-event-strong" x-text="slot.primary"></span>
                            <span class="availability-day-event-muted"
                                x-text="slot.line2
                                    ? ((slot.isCompact || isSpaceAccount) ? (' · ' + slot.line2) : slot.line2)
                                    : ''"></span>
                        </span>
                    </button>
                </template>
            </div>
        </div>
    </div>
</section>

<style>
    .availability-day-shell {
        padding-top: 2px;
    }

    .availability-day-card {
        border: 1px solid #E2E2E2;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
    }

    .availability-day-header {
        background: #F9FAFC;
        border-bottom: 1px solid #E2E2E2;
        height: 55px;
        min-height: 55px;
        padding: 6px 12px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0;
        text-align: center;
        box-sizing: border-box;
    }

    .availability-day-header-weekday {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 16px;
        font-weight: 600;
        line-height: 20px;
    }

    .availability-day-header-date {
        color: #3B3731;
        font-family: 'Playfair Display', Georgia, serif;
        font-size: 20px;
        font-weight: 700;
        line-height: 20px;
    }

    .availability-day-body {
        position: relative;
        display: grid;
        grid-template-columns: 57px minmax(0, 1fr);
        min-height: var(--timeline-body-h, 550px);
        background: #F9F8F4;
        overflow: visible;
        z-index: 1;
    }

    .availability-day-hours {
        position: relative;
        overflow: visible;
        display: flex;
        flex-direction: column;
        background: #F9F8F4;
        z-index: 2;
    }

    .availability-day-gutter-cell {
        height: 55px;
        border-right: 1px solid #E2E2E2;
        border-bottom: 1px solid #E2E2E2;
        background: #F9F8F4;
        box-sizing: border-box;
        flex-shrink: 0;
    }

    .availability-day-gutter-cell.is-last {
        border-bottom: none;
    }

    .availability-day-hour-label {
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

    .availability-day-track {
        position: relative;
        min-height: var(--timeline-body-h, 550px);
        background: #fff;
    }

    .availability-day-track.is-time-off {
        background: #fff;
    }

    .availability-day-rows {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        pointer-events: none;
    }

    .availability-day-row {
        height: 55px;
        border-bottom: 1px solid #E2E2E2;
        box-sizing: border-box;
        background: #fff;
    }

    /* Figma 443:694 / 443:703 — first + last hour bands muted */
    .availability-day-row.is-spacer,
    .availability-day-row.is-last {
        background: #FBFBFB;
    }

    .availability-day-row.is-last {
        border-bottom: none;
    }

    .availability-day-timeoff {
        position: absolute;
        top: 5px;
        left: 1px;
        right: 1px;
        bottom: 5px;
        z-index: 1;
        padding: 12px 16px 8px;
        border-radius: 5px;
        background: #F1F1F1;
        border-top: 6px solid #9D9B98;
        pointer-events: none;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        line-height: 1.35;
        box-sizing: border-box;
    }

    .availability-day-timeoff-main {
        color: #3B3731;
    }

    .availability-day-timeoff-sub {
        color: #9D9B98;
    }

    /* Figma 443:864+ — rounded card + inset 6px top accent strip (Group 5637) */
    .availability-day-event {
        position: absolute;
        left: 1px;
        right: 1px;
        z-index: 2;
        appearance: none;
        border: none;
        border-radius: 5px;
        padding: 10px 12px 6px;
        margin: 0;
        overflow: hidden;
        text-align: left;
        cursor: default;
        display: block;
        box-sizing: border-box;
        background: #F5F8FA;
        --day-event-accent: #CBDCE8;
    }

    .availability-day-event::before {
        content: '';
        position: absolute;
        top: 2px;
        left: 2px;
        right: 2px;
        height: 6px;
        border-radius: 3px 3px 0 0;
        background: var(--day-event-accent);
        pointer-events: none;
    }

    .availability-day-event:focus,
    .availability-day-event:focus-visible {
        outline: none;
    }

    .availability-day-event.is-clickable {
        cursor: pointer;
    }

    .availability-day-event.is-clickable:hover {
        filter: brightness(0.985);
    }

    .availability-day-event.is-compact {
        padding: 8px 12px 2px;
        display: flex;
        align-items: flex-start;
    }

    .availability-day-event-stack {
        display: flex;
        flex-direction: column;
        gap: 2px;
        position: relative;
        z-index: 1;
    }

    .availability-day-event-inline {
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        min-width: 0;
        position: relative;
        z-index: 1;
    }

    .availability-day-event-inline .availability-day-event-strong,
    .availability-day-event-inline .availability-day-event-muted {
        display: inline;
    }

    .availability-day-event-strong,
    .availability-day-event-muted {
        font-family: Lato, sans-serif;
        font-size: 12px;
        line-height: 1.35;
    }

    .availability-day-event-strong {
        color: #3B3731;
        font-weight: 600;
    }

    .availability-day-event-muted {
        color: #9D9B98;
        font-weight: 400;
    }

    .availability-day-event.is-blue {
        background: #F5F8FA;
        --day-event-accent: #CBDCE8;
    }

    .availability-day-event.is-green {
        background: #F4F8EC;
        --day-event-accent: #D8E8B7;
    }

    .availability-day-event.is-orange {
        background: #FFF6E9;
        --day-event-accent: #FFC97A;
    }

    .availability-day-event.is-red {
        background: #FFE2E2;
        --day-event-accent: #FF6E6E;
        text-decoration: line-through;
    }
</style>
