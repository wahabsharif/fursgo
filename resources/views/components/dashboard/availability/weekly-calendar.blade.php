<section class="availability-weekly-shell">
    <div class="availability-weekly-card">
        <div class="availability-weekly-head">
            <template x-for="day in weeklyGrid" :key="'head-' + day.key">
                <div class="availability-weekly-head-cell" :class="{ 'is-today': day.isToday }">
                    <span x-text="day.label"></span>
                </div>
            </template>
        </div>

        <div class="availability-weekly-grid">
            <template x-for="day in weeklyGrid" :key="day.key">
                <article class="availability-weekly-column"
                    :class="{ 'is-today-column': day.isToday, 'is-muted-column': day.isMuted }">
                    <div class="availability-weekly-date" x-text="day.day"></div>

                    <div class="availability-weekly-slots">
                        <template x-for="(slot, slotIndex) in day.slots" :key="day.key + '-slot-' + slotIndex">
                            <div class="availability-weekly-slot"
                                :class="['is-' + slot.type, slot.bookingId ? 'is-clickable' : '']"
                                @click.stop="onCalendarSlotClick(slot)">
                                <p class="availability-weekly-time">
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none"
                                        aria-hidden="true">
                                        <circle cx="8" cy="8" r="6" stroke="currentColor"
                                            stroke-width="1.5" />
                                        <path d="M8 4.5V8L10.5 10" stroke="currentColor" stroke-width="1.5"
                                            stroke-linecap="round" />
                                    </svg>
                                    <span x-text="slot.time"></span>
                                </p>
                                <p class="availability-weekly-pet">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="13"
                                        viewBox="0 0 14 13" fill="none" aria-hidden="true">
                                        <path
                                            d="M7 5.23684C5.03948 5.23684 3.38291 6.98347 2.84805 9.22684C2.61281 10.2134 2.96752 11.2605 3.83821 11.7525C4.52845 12.1425 5.55514 12.5 7 12.5C8.44486 12.5 9.47186 12.1425 10.1621 11.7525C11.0328 11.2605 11.3872 10.2134 11.152 9.22684C10.6171 6.98316 8.96052 5.23684 7 5.23684ZM0.5 4.74547C0.5 5.61768 1.05405 6.5 1.7381 6.5C2.42214 6.5 2.97619 5.61768 2.97619 4.74547C2.97619 3.87326 2.42214 3.34211 1.7381 3.34211C1.05405 3.34211 0.5 3.87358 0.5 4.74547ZM13.5 4.74547C13.5 5.61768 12.946 6.5 12.2619 6.5C11.5779 6.5 11.0238 5.61768 11.0238 4.74547C11.0238 3.87326 11.5779 3.34211 12.2619 3.34211C12.946 3.34211 13.5 3.87358 13.5 4.74547ZM3.75 1.90337C3.75 2.77558 4.30405 3.65789 4.9881 3.65789C5.67214 3.65789 6.22619 2.77558 6.22619 1.90337C6.22619 1.03116 5.67214 0.5 4.9881 0.5C4.30405 0.5 3.75 1.03147 3.75 1.90337ZM10.25 1.90337C10.25 2.77558 9.69595 3.65789 9.01191 3.65789C8.32786 3.65789 7.77381 2.77558 7.77381 1.90337C7.77381 1.03116 8.32786 0.5 9.01191 0.5C9.69595 0.5 10.25 1.03147 10.25 1.90337Z"
                                            stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" />
                                    </svg>
                                    <span
                                        x-text="slot.pet.includes(' - ') ? slot.pet.split(' - ')[0] : slot.pet"></span>
                                    <span class="availability-weekly-pet-type" x-show="slot.pet.includes(' - ')"
                                        x-text="' - ' + (slot.pet.split(' - ')[1] || '')"></span>
                                </p>
                                <p class="availability-weekly-service">
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
                        </template>

                        <button x-show="day.moreCount > 0" type="button" class="availability-weekly-more"
                            @click.stop="openBookingsDrawerForDate(day.dateKey)"
                            x-text="'+' + ' ' + day.moreCount"></button>
                    </div>
                </article>
            </template>
        </div>
    </div>
</section>

<style>
    .availability-weekly-shell {
        padding-top: 2px;
    }

    .availability-weekly-card {
        border: 1px solid #D9D9D9;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
    }

    .availability-weekly-head,
    .availability-weekly-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .availability-weekly-head-cell {
        border-bottom: 1px solid #D9D9D9;
        background: #F9FAFC;
        text-align: center;
        padding: 15px 8px;
    }

    .availability-weekly-head-cell:last-child {
        border-right: none;
    }

    .availability-weekly-head-cell span {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .availability-weekly-head-cell.is-today span {
        color: #FFC97A;
        font-weight: 900;
    }

    .availability-weekly-column {
        min-height: 640px;
        border-right: 1px solid #D9D9D9;
        background: #FFF;
        padding: 8px 8px 14px;
        position: relative;
    }

    .availability-weekly-column:last-child {
        border-right: none;
    }

    .availability-weekly-column.is-today-column {
        background: #FFFBF4;
    }

    .availability-weekly-column.is-muted-column {
        background: #F9FAFC;
    }

    .availability-weekly-column.is-muted-column::before {
        content: '';
        position: absolute;
        inset: 0;
        background-image: url('{{ asset('images/muted-bg-weekly.svg') }}');
        background-repeat: repeat-y;
        background-position: top calc(50% - 6px);
        background-size: 100% auto;
        pointer-events: none;
    }

    .availability-weekly-column.is-muted-column>* {
        position: relative;
        z-index: 1;
    }

    .availability-weekly-date {
        text-align: right;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        margin: 6px 25px 12px;
        line-height: normal;
    }

    .availability-weekly-column.is-today-column .availability-weekly-date {
        color: #FFC97A;
        text-align: right;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 900;
        line-height: normal;
    }

    .availability-weekly-slots {
        display: grid;
        gap: 8px;
    }

    .availability-weekly-slot {
        width: 135px;
        margin: 0 auto;
        border: none;
        border-radius: 5px !important;
        border: 1px solid transparent;
    }

    .availability-weekly-slot.is-clickable {
        cursor: pointer;
        transition: transform 0.12s ease, box-shadow 0.12s ease;
    }

    .availability-weekly-slot.is-clickable:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.06);
    }

    .availability-weekly-slot p {
        margin: 0;
        text-align: center;
        font-family: Lato;
        font-size: 12px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        letter-spacing: 0.12px;
    }

    .availability-weekly-slot p svg {
        flex-shrink: 0;
    }

    .availability-weekly-slot .availability-weekly-time {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        padding: 8px 20px;
    }

    .availability-weekly-slot .availability-weekly-pet {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        padding: 8px 20px;
    }

    .availability-weekly-pet-type {
        white-space: nowrap;
    }

    .availability-weekly-slot .availability-weekly-service {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 6px;
        padding: 0 22px 8px;
    }

    .availability-weekly-slot.is-blue {
        background: #F5F8FA;
        border: none;
        color: #7AB7E3;
    }

    .availability-weekly-slot.is-blue .availability-weekly-pet-type {
        color: #A9D5F4;
    }

    .availability-weekly-slot.is-blue>p:first-child {
        background: #EDF2F6;
        border: none;
        border-radius: 5px 5px 0 0;
        color: #7AB7E3;
    }

    .availability-weekly-slot.is-green {
        background: #f4f8ec;
        border: none;
        color: #B5DB65;
    }

    .availability-weekly-slot.is-green>p:first-child {
        border-radius: 5px 5px 0 0;
        background: #EBF2DD;
        border: none;
        color: #B5DB65;
    }

    .availability-weekly-slot.is-green .availability-weekly-pet-type {
        color: #C9DDA0;
    }

    .availability-weekly-slot.is-orange {
        background: #fff4e4;
        border: none;
        color: #FFAE37;
    }

    .availability-weekly-slot.is-orange>p:first-child {
        border-radius: 5px 5px 0 0;
        background: #FFE2B7;
        border: none;
        color: #FFAE37;
    }

    .availability-weekly-slot.is-orange .availability-weekly-pet-type {
        color: #FFC877;
    }

    .availability-weekly-slot.is-red {
        background: #ffe2e2;
        border: none;
        color: #FF6E6E;
    }

    .availability-weekly-slot.is-red>p:first-child {
        border-radius: 5px 5px 0 0;
        background: #FFCBCB;
        border: none;
        color: #FF6E6E;
    }

    .availability-weekly-slot.is-red .availability-weekly-pet-type {
        color: #FFA3A3;
    }

    .availability-weekly-slot.is-red p {
        text-decoration: line-through;
    }

    .availability-weekly-more {
        border: none;
        border-radius: 999px;
        height: 30px;
        background: #EAEAEA;
        color: #888;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        letter-spacing: 0.14px;
        cursor: pointer;
    }

    .availability-weekly-more:hover {
        background: #e0e0e0;
    }

    @media (max-width: 1450px) {
        .availability-weekly-column {
            min-height: 520px;
        }
    }
</style>
