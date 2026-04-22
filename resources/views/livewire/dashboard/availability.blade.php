<?php

use Livewire\Volt\Component;

new class extends Component {
    //
}; ?>

<div class="availability-layout" x-data="availabilityCalendar()" x-init="init()">
    <div class="availability-toolbar">
        <div class="availability-view-toggle">
            <button type="button">Day</button>
            <button type="button">Week</button>
            <button type="button" class="is-active">Month</button>
        </div>

        <div class="availability-calendar-title">
            <button type="button" aria-label="Previous month" @click="prevMainMonth()">
                <svg xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
                    <path d="M4.59033 8.612L0.499999 4.52167L4.52167 0.499997" stroke="#3B3731" stroke-linecap="round"
                        stroke-linejoin="round" />
                </svg>
            </button>
            <h3 x-text="mainMonthYearLabel"></h3>
            <button type="button" aria-label="Next month" @click="nextMainMonth()"><svg
                    xmlns="http://www.w3.org/2000/svg" width="6" height="10" viewBox="0 0 6 10" fill="none">
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

    <div class="availability-content">
        <section class="availability-calendar-shell">
            <div class="availability-calendar-card">
                <div class="availability-weekdays">
                    <template x-for="(weekday, weekdayIndex) in weekdays" :key="weekday">
                        <span :class="{ 'is-current-weekday': weekdayIndex === currentWeekdayIndex }"
                            x-text="weekday"></span>
                    </template>
                </div>

                <div class="availability-grid">
                    <template x-for="day in mainMonthGrid" :key="day.key">
                        <article class="availability-day-cell"
                            :class="{ 'is-muted': !day.inCurrentMonth, 'is-today-cell': isToday(day.date) }">
                            <strong :class="{ 'is-today': isToday(day.date) }" x-text="day.day"></strong>
                            <ul x-show="day.inCurrentMonth && day.slots.length">
                                <template x-for="(slot, slotIndex) in day.slots" :key="day.key + '-slot-' + slotIndex">
                                    <li :class="slot.type">
                                        <svg x-show="!slot.label.trim().startsWith('+')" class="slot-icon"
                                            xmlns="http://www.w3.org/2000/svg" width="14" height="13"
                                            viewBox="0 0 14 13" fill="none" aria-hidden="true">
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

        <aside class="availability-side-panel">
            <div class="availability-mini-calendar">
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
                        <span :class="{ 'is-selected': isToday(day.date) }"
                            x-text="day.inCurrentMonth ? day.day : ''"></span>
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

    .availability-calendar-shell {
        padding-top: 2px;
    }

    .availability-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        margin: 38px 0;
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
        font-weight: 600;
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
        padding: 0 34px 0 12px;
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

    .availability-calendar-card {
        border: 1px solid #ddd6cd;
        border-radius: 10px;
        background: #fff;
        overflow: hidden;
    }

    .availability-calendar-title {
        min-width: 200px;
        display: grid;
        grid-template-columns: 26px 170px 26px;
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
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .availability-weekdays,
    .availability-grid {
        display: grid;
        grid-template-columns: repeat(7, minmax(0, 1fr));
    }

    .availability-weekdays span {
        /* border-top: 1px solid #D4D4D4; */
        border-bottom: 1px solid #D4D4D4;
        background: #F9FAFC;
        padding: 15px 8px;
        text-align: center;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .availability-weekdays span.is-current-weekday {
        color: #FFC97A;
        text-align: center;
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
        top: 30px;
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
        gap: 3px;
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

    .availability-side-panel {
        display: grid;
        gap: 24px;
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

    .availability-mini-grid span {
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
    }

    .availability-mini-grid .is-selected {
        background: #FFC97A;
        color: #fff;
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
            flex-direction: column;
            align-items: stretch;
            gap: 10px;
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
        if (window.availabilityCalendar) return;

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

        window.availabilityCalendar = function() {
            return {
                today: null,
                weekdays: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'],
                currentWeekdayIndex: 0,
                mainMonth: null,
                miniMonth: null,
                init() {
                    const now = new Date();
                    this.today = new Date(now.getFullYear(), now.getMonth(), now.getDate());
                    this.currentWeekdayIndex = (this.today.getDay() + 6) % 7;
                    this.mainMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                    this.miniMonth = new Date(now.getFullYear(), now.getMonth(), 1);
                },
                get mainMonthYearLabel() {
                    return `${MONTHS[this.mainMonth.getMonth()]}, ${this.mainMonth.getFullYear()}`;
                },
                get miniMonthYearLabel() {
                    return `${MONTHS[this.miniMonth.getMonth()]}, ${this.miniMonth.getFullYear()}`;
                },
                prevMainMonth() {
                    this.mainMonth = new Date(this.mainMonth.getFullYear(), this.mainMonth.getMonth() - 1, 1);
                },
                nextMainMonth() {
                    this.mainMonth = new Date(this.mainMonth.getFullYear(), this.mainMonth.getMonth() + 1, 1);
                },
                prevMiniMonth() {
                    this.miniMonth = new Date(this.miniMonth.getFullYear(), this.miniMonth.getMonth() - 1, 1);
                },
                nextMiniMonth() {
                    this.miniMonth = new Date(this.miniMonth.getFullYear(), this.miniMonth.getMonth() + 1, 1);
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
                            slots: includeSlots && inCurrentMonth ? this.getSlots(day, daysInMonth) :
                            [],
                        });
                    }

                    return cells;
                },
                get mainMonthGrid() {
                    return this.buildMonthGrid(this.mainMonth, true);
                },
                get miniMonthGrid() {
                    return this.buildMonthGrid(this.miniMonth, false);
                },
                getSlots(day, daysInMonth) {
                    const baseSlots = {
                        4: [{
                            label: '12:30',
                            type: 'blue'
                        }, {
                            label: '13:30',
                            type: 'blue'
                        }, {
                            label: '+ 2',
                            type: 'neutral'
                        }],
                        5: [{
                            label: '12:30',
                            type: 'blue'
                        }, {
                            label: '13:30',
                            type: 'blue'
                        }, {
                            label: '14:30',
                            type: 'blue'
                        }],
                        8: [{
                            label: '12:30',
                            type: 'blue'
                        }, {
                            label: '16:00',
                            type: 'red'
                        }],
                        10: [{
                            label: '12:30',
                            type: 'blue'
                        }, {
                            label: '13:30',
                            type: 'blue'
                        }, {
                            label: '+ 2',
                            type: 'neutral'
                        }],
                        12: [{
                            label: '12:30',
                            type: 'green'
                        }, {
                            label: '13:30',
                            type: 'green'
                        }, {
                            label: '16:00',
                            type: 'red'
                        }],
                        15: [{
                            label: '12:30',
                            type: 'green'
                        }, {
                            label: '13:30',
                            type: 'green'
                        }, {
                            label: '14:30',
                            type: 'green'
                        }],
                        17: [{
                            label: '12:30',
                            type: 'green'
                        }, {
                            label: '13:30',
                            type: 'green'
                        }, {
                            label: '14:30',
                            type: 'green'
                        }],
                        19: [{
                            label: '12:30',
                            type: 'green'
                        }, {
                            label: '13:30',
                            type: 'green'
                        }, {
                            label: '14:30',
                            type: 'green'
                        }],
                        22: [{
                            label: '13:00',
                            type: 'orange'
                        }, {
                            label: '13:30',
                            type: 'green'
                        }, {
                            label: '14:30',
                            type: 'green'
                        }],
                        24: [{
                            label: '12:30',
                            type: 'green'
                        }, {
                            label: '13:30',
                            type: 'green'
                        }, {
                            label: '+ 2',
                            type: 'neutral'
                        }],
                        26: [{
                            label: '12:30',
                            type: 'orange'
                        }, {
                            label: '13:30',
                            type: 'orange'
                        }, {
                            label: '+ 2',
                            type: 'neutral'
                        }],
                        27: [{
                            label: '16:00',
                            type: 'red'
                        }],
                    };

                    if (day > daysInMonth) return [];
                    return baseSlots[day] ?? [];
                },
            };
        };
    })();
</script>
