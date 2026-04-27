<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="availability-layout" x-data="availabilityCalendarShell()" x-init="init()">
    <div class="availability-header">
        <div class="availability-toolbar">
            <div class="availability-view-toggle">
                <button type="button" :class="{ 'is-active': activeView === 'day' }"
                    @click="activeView = 'day'">Day</button>
                <button type="button" :class="{ 'is-active': activeView === 'week' }"
                    @click="activeView = 'week'">Week</button>
                <button type="button" :class="{ 'is-active': activeView === 'month' }"
                    @click="activeView = 'month'">Month</button>
            </div>

            <div class="availability-calendar-title">
                <button type="button" aria-label="Previous period" @click="prevPeriod()">
                    <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10"
                        fill="none">
                        <path d="M4.59033 8.612L0.499999 4.52167L4.52167 0.499997" stroke="#3B3731"
                            stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <h3 x-text="periodLabel"></h3>
                <button type="button" aria-label="Next period" @click="nextPeriod()"><svg
                        xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10"
                        fill="none">
                        <path d="M0.5 8.612L4.59033 4.52167L0.568664 0.499997" stroke="#3B3731" stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg></button>
            </div>

            <label class="availability-search">
                <input type="search" placeholder="Type to search ..." />
                <span class="availability-search-icon" aria-hidden="true">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 16 16"
                        fill="none">
                        <path
                            d="M5.73535 0.5C8.6267 0.500031 10.9707 2.844 10.9707 5.73535C10.9707 7.22006 10.3528 8.55933 9.35938 9.5127C8.41826 10.4158 7.14221 10.9707 5.73535 10.9707C2.844 10.9707 0.500031 8.6267 0.5 5.73535C0.5 2.84398 2.84398 0.5 5.73535 0.5Z"
                            stroke="#9D9B98" />
                        <path
                            d="M14.6466 15.3537C14.8419 15.549 15.1585 15.549 15.3537 15.3537C15.549 15.1585 15.549 14.8419 15.3537 14.6466L15.0002 15.0002L14.6466 15.3537ZM9.70605 9.70605L9.3525 10.0596L14.6466 15.3537L15.0002 15.0002L15.3537 14.6466L10.0596 9.3525L9.70605 9.70605Z"
                            fill="#9D9B98" />
                    </svg>
                </span>
            </label>
        </div>
    </div>

    <div class="availability-content">
        <div>
            <div x-show="activeView === 'day'" x-cloak x-transition:enter="availability-view-enter"
                x-transition:enter-start="availability-view-enter-start"
                x-transition:enter-end="availability-view-enter-end" x-transition:leave="availability-view-leave"
                x-transition:leave-start="availability-view-leave-start"
                x-transition:leave-end="availability-view-leave-end">
                <x-dashboard.availability.day-calendar />
            </div>

            <div x-show="activeView === 'month'" x-cloak x-transition:enter="availability-view-enter"
                x-transition:enter-start="availability-view-enter-start"
                x-transition:enter-end="availability-view-enter-end" x-transition:leave="availability-view-leave"
                x-transition:leave-start="availability-view-leave-start"
                x-transition:leave-end="availability-view-leave-end">
                <x-dashboard.availability.monthly-calendar />
            </div>

            <div x-show="activeView === 'week'" x-cloak x-transition:enter="availability-view-enter"
                x-transition:enter-start="availability-view-enter-start"
                x-transition:enter-end="availability-view-enter-end" x-transition:leave="availability-view-leave"
                x-transition:leave-start="availability-view-leave-start"
                x-transition:leave-end="availability-view-leave-end">
                <x-dashboard.availability.weekly-calendar />
            </div>
        </div>

        <aside class="availability-side-panel">
            <div class="availability-mini-calendar" x-data="availabilityMiniCalendar()" x-init="init()">
                <div class="availability-mini-header">
                    <h4 x-text="miniMonthYearLabel"></h4>
                    <div>
                        <button type="button" aria-label="Previous month" @click="prevMiniMonth()"><svg
                                xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10"
                                fill="none">
                                <path d="M4.59033 8.612L0.499999 4.52167L4.52167 0.499997" stroke="#3B3731"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                        <button type="button" aria-label="Next month" @click="nextMiniMonth()"><svg
                                xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10"
                                fill="none">
                                <path d="M0.5 8.612L4.59033 4.52167L0.568664 0.499997" stroke="#3B3731"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg></button>
                    </div>
                </div>
                <div class="availability-mini-weekdays">
                    <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
                </div>
                <div class="availability-mini-grid">
                    <template x-for="day in miniMonthGrid" :key="'mini-' + day.key">
                        <button type="button" class="availability-mini-day" :disabled="!day.inCurrentMonth"
                            @click="selectMiniDate(day.date)" :aria-pressed="isSelectedDate(day.date)"
                            :class="{ 'is-selected': isSelectedDate(day.date), 'is-outside-month': !day.inCurrentMonth }"
                            x-text="day.inCurrentMonth ? day.day : ''"></button>
                    </template>
                </div>
            </div>

            <div class="availability-booking-card-wrap">
                <h5>Upcoming Bookings <span>(2)</span></h5>
                <article class="availability-booking-card">
                    <div class="img-circle">
                        <div>
                            <img src="{{ asset('images/ellipse-65.svg') }}" alt="Booking profile image" />
                        </div>
                    </div>
                    <div>
                        <div class="booking-chip">Home Visits</div>
                        <ul>
                            <li><svg xmlns="http://www.w3.org/2000/svg" width="13" height="13"
                                    viewBox="0 0 13 13" fill="none">
                                    <path
                                        d="M0.5 5.95717C0.5 3.79127 0.5 2.70803 1.2032 2.03545C1.9064 1.36288 3.0374 1.3623 5.3 1.3623H7.7C9.9626 1.3623 11.0942 1.3623 11.7968 2.03545C12.4994 2.7086 12.5 3.79127 12.5 5.95717V7.10589C12.5 9.27179 12.5 10.355 11.7968 11.0276C11.0936 11.7002 9.9626 11.7008 7.7 11.7008H5.3C3.0374 11.7008 1.9058 11.7008 1.2032 11.0276C0.5006 10.3545 0.5 9.27179 0.5 7.10589V5.95717Z"
                                        stroke="#3B3731" />
                                    <path d="M3.50005 1.36154V0.5M9.50005 1.36154V0.5M0.800049 4.23333H12.2"
                                        stroke="#3B3731" stroke-linecap="round" />
                                    <path
                                        d="M10.0997 8.82785C10.0997 8.98017 10.0364 9.12627 9.92392 9.23398C9.8114 9.34169 9.65879 9.4022 9.49966 9.4022C9.34053 9.4022 9.18792 9.34169 9.07539 9.23398C8.96287 9.12627 8.89966 8.98017 8.89966 8.82785C8.89966 8.67552 8.96287 8.52943 9.07539 8.42171C9.18792 8.314 9.34053 8.25349 9.49966 8.25349C9.65879 8.25349 9.8114 8.314 9.92392 8.42171C10.0364 8.52943 10.0997 8.67552 10.0997 8.82785ZM10.0997 6.53041C10.0997 6.68274 10.0364 6.82883 9.92392 6.93655C9.8114 7.04426 9.65879 7.10477 9.49966 7.10477C9.34053 7.10477 9.18792 7.04426 9.07539 6.93655C8.96287 6.82883 8.89966 6.68274 8.89966 6.53041C8.89966 6.37808 8.96287 6.23199 9.07539 6.12428C9.18792 6.01657 9.34053 5.95605 9.49966 5.95605C9.65879 5.95605 9.8114 6.01657 9.92392 6.12428C10.0364 6.23199 10.0997 6.37808 10.0997 6.53041ZM7.09966 8.82785C7.09966 8.98017 7.03644 9.12627 6.92392 9.23398C6.8114 9.34169 6.65879 9.4022 6.49966 9.4022C6.34053 9.4022 6.18792 9.34169 6.07539 9.23398C5.96287 9.12627 5.89966 8.98017 5.89966 8.82785C5.89966 8.67552 5.96287 8.52943 6.07539 8.42171C6.18792 8.314 6.34053 8.25349 6.49966 8.25349C6.65879 8.25349 6.8114 8.314 6.92392 8.42171C7.03644 8.52943 7.09966 8.67552 7.09966 8.82785ZM7.09966 6.53041C7.09966 6.68274 7.03644 6.82883 6.92392 6.93655C6.8114 7.04426 6.65879 7.10477 6.49966 7.10477C6.34053 7.10477 6.18792 7.04426 6.07539 6.93655C5.96287 6.82883 5.89966 6.68274 5.89966 6.53041C5.89966 6.37808 5.96287 6.23199 6.07539 6.12428C6.18792 6.01657 6.34053 5.95605 6.49966 5.95605C6.65879 5.95605 6.8114 6.01657 6.92392 6.12428C7.03644 6.23199 7.09966 6.37808 7.09966 6.53041ZM4.09966 8.82785C4.09966 8.98017 4.03644 9.12627 3.92392 9.23398C3.8114 9.34169 3.65879 9.4022 3.49966 9.4022C3.34053 9.4022 3.18792 9.34169 3.07539 9.23398C2.96287 9.12627 2.89966 8.98017 2.89966 8.82785C2.89966 8.67552 2.96287 8.52943 3.07539 8.42171C3.18792 8.314 3.34053 8.25349 3.49966 8.25349C3.65879 8.25349 3.8114 8.314 3.92392 8.42171C4.03644 8.52943 4.09966 8.67552 4.09966 8.82785ZM4.09966 6.53041C4.09966 6.68274 4.03644 6.82883 3.92392 6.93655C3.8114 7.04426 3.65879 7.10477 3.49966 7.10477C3.34053 7.10477 3.18792 7.04426 3.07539 6.93655C2.96287 6.82883 2.89966 6.68274 2.89966 6.53041C2.89966 6.37808 2.96287 6.23199 3.07539 6.12428C3.18792 6.01657 3.34053 5.95605 3.49966 5.95605C3.65879 5.95605 3.8114 6.01657 3.92392 6.12428C4.03644 6.23199 4.09966 6.37808 4.09966 6.53041Z"
                                        fill="#3B3731" />
                                </svg>
                                18/12/2025</li>
                            <li>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                    <circle cx="8" cy="8" r="6" stroke="#3B3731"
                                        stroke-width="1.5" />
                                    <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                        stroke-linecap="round" />
                                </svg>14:30 - 15:30
                            </li>
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="14"
                                    viewBox="0 0 13 14" fill="none">
                                    <path
                                        d="M4.09452 9.45989C5.13622 10.5016 7.66978 9.6573 9.75319 7.57355C11.8369 5.49013 12.6812 2.95655 11.6395 1.91484M7.1596 1.20725L7.6311 1.67909M5.50935 2.85785L5.98085 3.32935M4.09418 4.7442L4.56568 5.2157M3.62268 7.10204L4.09418 7.57355M9.75319 0.5L10.2247 0.971503M9.28169 3.32969L10.2247 4.27269M7.63144 4.98028L8.57444 5.92329M5.7451 6.39479L6.6881 7.3378"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                    <path
                                        d="M4.09395 10.874C4.48462 10.4834 4.48462 9.84998 4.09395 9.45931C3.70329 9.06865 3.0699 9.06865 2.67924 9.45932L0.792951 11.3456C0.402288 11.7363 0.402288 12.3697 0.792951 12.7603C1.18361 13.151 1.817 13.151 2.20767 12.7603L4.09395 10.874Z"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>Full Groom
                            </li>
                            <li><svg xmlns="http://www.w3.org/2000/svg" width="13" height="12"
                                    viewBox="0 0 13 12" fill="none">
                                    <path
                                        d="M6.5 4.84211C4.69029 4.84211 3.16114 6.44318 2.66743 8.4996C2.45029 9.40392 2.77771 10.3638 3.58143 10.8148C4.21857 11.1723 5.16629 11.5 6.5 11.5C7.83371 11.5 8.78171 11.1723 9.41886 10.8148C10.2226 10.3638 10.5497 9.40392 10.3326 8.4996C9.83886 6.44289 8.30971 4.84211 6.5 4.84211ZM0.5 4.39168C0.5 5.19121 1.01143 6 1.64286 6C2.27429 6 2.78571 5.19121 2.78571 4.39168C2.78571 3.59216 2.27429 3.10526 1.64286 3.10526C1.01143 3.10526 0.5 3.59245 0.5 4.39168ZM12.5 4.39168C12.5 5.19121 11.9886 6 11.3571 6C10.7257 6 10.2143 5.19121 10.2143 4.39168C10.2143 3.59216 10.7257 3.10526 11.3571 3.10526C11.9886 3.10526 12.5 3.59245 12.5 4.39168ZM3.5 1.78642C3.5 2.58595 4.01143 3.39474 4.64286 3.39474C5.27429 3.39474 5.78571 2.58595 5.78571 1.78642C5.78571 0.986895 5.27429 0.5 4.64286 0.5C4.01143 0.5 3.5 0.987184 3.5 1.78642ZM9.5 1.78642C9.5 2.58595 8.98857 3.39474 8.35714 3.39474C7.72571 3.39474 7.21429 2.58595 7.21429 1.78642C7.21429 0.986895 7.72571 0.5 8.35714 0.5C8.98857 0.5 9.5 0.987184 9.5 1.78642Z"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>Bella - <span>Rabbit</span></li>
                        </ul>
                    </div>
                </article>
                <button type="button" class="availability-view-all">View All</button>
            </div>
        </aside>
    </div>
</div>

<style>
    .availability-layout {
        display: flex;
        flex-direction: column;
    }

    .availability-content {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 250px;
        gap: 20px;
        align-items: flex-start;
    }

    .availability-side-panel {
        display: grid;
        gap: 24px;
    }

    .availability-header {
        margin: 38px 0;
    }



    .availability-toolbar {
        display: grid;
        grid-template-columns: auto 1fr auto;
        align-items: center;
        gap: 12px;
    }

    .availability-calendar-title {
        justify-self: center;
    }

    .availability-view-toggle {
        display: inline-flex;
        border: 1px solid #D4D4D4;
        border-radius: 10px;
        overflow: hidden;
        background: #fff;
    }

    .availability-view-toggle button {
        border: none;
        background: transparent;
        padding: 10px 18px;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
    }

    .availability-view-toggle button:nth-child(2) {
        border-left: 1px solid #D4D4D4;
        border-right: 1px solid #D4D4D4;
    }

    .availability-view-toggle .is-active {
        background: #F9FAFC;
        color: #3B3731;
    }

    .availability-search {
        position: relative;
        display: inline-flex;
        align-items: center;
    }

    .availability-search input {
        width: 200px;
        height: 42px;
        border: 1px solid #e5e2de;
        border-radius: 10px;
        padding: 0 35px 0 15px;
        color: #8b8781;
        font-size: 12px;
        font-family: Lato, sans-serif;
        outline: none;
    }

    .availability-search-icon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        display: inline-flex;
    }

    .availability-mini-calendar {
        border: 1px solid #e8e2db;
        border-radius: 12px;
        background: #fff;
        padding: 12px 12px 0;
    }

    .availability-booking-card-wrap {
        padding-top: 2px;
    }

    .availability-mini-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 8px;
    }

    .availability-mini-header h4 {
        margin: 0;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .availability-mini-header>div {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .availability-mini-header>div>button {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        aspect-ratio: 1 / 1;
        border-radius: 50%;
        border: 1px solid #F5F5F5;
        background: #FFF;
        box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.03);
        cursor: pointer;
    }

    .availability-mini-weekdays,
    .availability-mini-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
        gap: 4px;
        margin: 10px 0;
    }

    .availability-mini-weekdays span {
        width: 30px;
        height: 30px;
        aspect-ratio: 1/1;
        margin: 0 auto;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        color: #9C9790;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .availability-mini-grid .availability-mini-day {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        aspect-ratio: 1/1;
        margin: 0 auto;
        border-radius: 50%;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border: none;
        background: transparent;
        cursor: pointer;
        padding: 0;
    }

    .availability-mini-grid .is-selected {
        background: #FFC97A;
        color: #fff;
    }

    .availability-mini-grid .availability-mini-day.is-outside-month {
        cursor: default;
        color: transparent;
    }

    .availability-booking-card-wrap h5 {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        padding-bottom: 15px;
        border-bottom: 1px solid #e5ded5;
        margin-bottom: 15px;
    }

    .availability-booking-card-wrap h5 span {
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .availability-booking-card {
        display: flex;
        align-items: start;
        gap: 20px;
        border-radius: 10px;
        background: #FFFBF4;
        padding: 15px;
        margin-bottom: 1.5rem;
    }

    .img-circle {
        width: 50px;
        height: 50px;
        aspect-ratio: 1/1;
        border-radius: 50px;
        background: rgba(255, 201, 122, 0.30);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .img-circle>div {
        width: 40px;
        height: 40px;
        aspect-ratio: 1/1;
        border-radius: 40px;
        background: rgba(255, 201, 122, 0.50);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .img-circle>div>img {
        width: 30px;
        height: 30px;
        aspect-ratio: 1/1;
        border-radius: 86px;

    }

    .booking-chip {
        width: 93px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 100px;
        background: #FFC97A;
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        margin-bottom: 15px;
    }

    .availability-booking-card>div:last-child>ul {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        display: grid;
        gap: 10px;
    }

    .availability-booking-card>div:last-child>ul>li {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 15px;
    }

    .availability-booking-card>div:last-child>ul>li>span {
        color: #9D9B98;

    }

    .availability-view-all {
        border: none;
        background: transparent;
        color: #4e4942;
        text-decoration: underline;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: block;
        margin: 0 auto;
    }

    .availability-view-enter,
    .availability-view-leave {
        transition: opacity 220ms ease, transform 220ms ease;
    }

    .availability-view-enter-start,
    .availability-view-leave-end {
        opacity: 0;
        transform: translateY(8px);
    }

    .availability-view-enter-end,
    .availability-view-leave-start {
        opacity: 1;
        transform: translateY(0);
    }

    @media (max-width: 1200px) {
        .availability-content {
            grid-template-columns: minmax(0, 1fr);
        }

        .availability-side-panel {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 768px) {
        .availability-toolbar {
            grid-template-columns: 1fr;
            align-items: stretch;
            gap: 10px;
        }

        .availability-calendar-title {
            justify-self: start;
        }

        .availability-search input {
            width: 100%;
        }

        .availability-side-panel {
            grid-template-columns: minmax(0, 1fr);
        }
    }
</style>

<script>
    (function() {
        if (window.availabilityMiniCalendar) return;

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

        window.availabilityMiniCalendar = function() {
            return {
                today: null,
                selectedDate: null,
                miniMonth: null,
                init() {
                    const now = new Date();
                    this.today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    this.selectedDate = new Date(this.today);
                    this.miniMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                },
                get miniMonthYearLabel() {
                    return `${MONTHS[this.miniMonth.getMonth()]}, ${this.miniMonth.getFullYear()}`;
                },
                prevMiniMonth() {
                    this.miniMonth = new Date(this.miniMonth.getFullYear(), this.miniMonth.getMonth() - 1, 1);
                },
                nextMiniMonth() {
                    this.miniMonth = new Date(this.miniMonth.getFullYear(), this.miniMonth.getMonth() + 1, 1);
                },
                isSelectedDate(dateObj) {
                    if (!dateObj || !this.selectedDate) return false;
                    return dateObj.getFullYear() === this.selectedDate.getFullYear() &&
                        dateObj.getMonth() === this.selectedDate.getMonth() &&
                        dateObj.getDate() === this.selectedDate.getDate();
                },
                selectMiniDate(dateObj) {
                    if (!dateObj || dateObj.getMonth() !== this.miniMonth.getMonth() || dateObj
                        .getFullYear() !== this.miniMonth.getFullYear()) {
                        return;
                    }

                    this.selectedDate = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate());
                },
                buildMonthGrid(baseDate) {
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
                        });
                    }

                    return cells;
                },
                get miniMonthGrid() {
                    return this.buildMonthGrid(this.miniMonth);
                },
            };
        };
    })();
</script>
