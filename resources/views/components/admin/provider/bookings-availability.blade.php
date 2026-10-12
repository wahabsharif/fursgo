@props(['profile'])

@php
$availability = $profile['availability'] ?? [];
$scheduleDate = $availability['schedule_date'] ?? 'Thursday, 2nd Feb 2025';
$workingHoursLabel = $availability['working_hours_label'] ?? 'Working hours: 07:00 - 14:00';
$calendarLabel = $availability['calendar_label'] ?? 'Thursday, 2nd Feb';
$activeDay = (int) ($availability['active_day'] ?? 2);
$calendarDays = $availability['calendar_days'] ?? [
    [null, null, null, null, null, 1, 2],
    [3, 4, 5, 6, 7, 8, 9],
    [10, 11, 12, 13, 14, 15, 16],
    [17, 18, 19, 20, 21, 22, 23],
    [24, 25, 26, 27, 28, null, null],
];
$slots = $availability['slots'] ?? [
    [
        'time' => '08:00',
        'id' => 'FG-0563-B12',
        'service' => 'Full Groom',
        'client' => 'Jane Doe · Bella (Rabbit)',
        'range' => '08:00 - 10:30',
        'location' => 'Home visits',
        'active' => true,
    ],
    [
        'time' => '10:45',
        'id' => 'FG-0564-B08',
        'service' => 'Nail Trim',
        'client' => 'Tom H. · Spike (Cat)',
        'range' => '10:45 - 11:00',
        'location' => 'Salon',
        'active' => false,
    ],
    [
        'time' => '14:30',
        'id' => 'FG-0565-B21',
        'service' => 'Bath & Tidy',
        'client' => 'Priya K. · Maisy (Rabbit)',
        'range' => '14:30 - 15:00',
        'location' => 'Home visits',
        'active' => false,
    ],
];
$weekHours = $availability['week_hours'] ?? [
    ['day' => 'Monday', 'hours' => '07:00 AM - 14:00 PM', 'status' => 'available', 'status_label' => 'Available'],
    ['day' => 'Tuesday', 'hours' => '07:00 AM - 14:00 PM', 'status' => 'available', 'status_label' => 'Available'],
    ['day' => 'Wednesday', 'hours' => '07:00 AM - 14:00 PM', 'status' => 'available', 'status_label' => 'Available'],
    ['day' => 'Thursday', 'hours' => '07:00 AM - 14:00 PM', 'status' => 'available', 'status_label' => 'Available'],
    ['day' => 'Friday', 'hours' => '07:00 AM - 14:00 PM', 'status' => 'available', 'status_label' => 'Available'],
    ['day' => 'Saturday', 'hours' => '—', 'status' => null, 'status_label' => '—'],
    ['day' => 'Sunday', 'hours' => '—', 'status' => null, 'status_label' => '—'],
];
$timeOff = $availability['time_off'] ?? [
    ['range' => '17 Feb 2025 - 02 Mar 2025', 'reason' => 'Annual Leave', 'duration' => '13 days', 'status' => 'upcoming', 'status_label' => 'Upcoming'],
    ['range' => '03 Jan 2025 - 05 Jan 2025', 'reason' => 'Personal', 'duration' => '3 days', 'status' => 'past', 'status_label' => 'Past'],
    ['range' => '12 Dec 2024 - 15 Dec 2024', 'reason' => 'Annual Leave', 'duration' => '4 days', 'status' => 'past', 'status_label' => 'Past'],
    ['range' => '01 Nov 2024 - 03 Nov 2024', 'reason' => 'Personal', 'duration' => '3 days', 'status' => 'past', 'status_label' => 'Past'],
];
$intake = $availability['intake'] ?? [
    ['label' => 'Pause new bookings', 'value' => 'Not active'],
    ['label' => 'Last status change', 'value' => 'Never paused'],
    ['label' => 'Booking accepted today', 'value' => '3'],
];
$timeline = $availability['timeline'] ?? [
    ['tone' => 'pass', 'title' => 'Booking marked completed by groomer', 'time' => '18 Apr 2025 · 11:32'],
    ['tone' => 'pass', 'title' => 'Booking confirmed by groomer', 'time' => '05 Mar 2025 · 21:50'],
    ['tone' => 'flag', 'title' => 'Service booked by customer', 'time' => '01 Dec 2024 · 18:55'],
];
$editIcon = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="15" viewBox="0 0 16 15" fill="none" aria-hidden="true"><path d="M9.80882 2.49568L12.2794 4.90732M8.16176 13.75H14.75M1.57353 10.5345L0.75 13.75L4.04412 12.9461L13.5855 3.63237C13.8943 3.33087 14.0678 2.922 14.0678 2.49568C14.0678 2.06936 13.8943 1.6605 13.5855 1.359L13.4439 1.22073C13.135 0.919322 12.7162 0.75 12.2794 0.75C11.8427 0.75 11.4238 0.919322 11.1149 1.22073L1.57353 10.5345Z" stroke="#3B3731" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" /></svg>';
@endphp

<div class="admin-bp-availability" x-data="{ timeOffPage: 0 }">
    <div class="admin-bp-availability-top">
        <section class="admin-bp-availability-card admin-bp-availability-schedule">
            <div class="admin-bp-availability-schedule-head">
                <h3 class="admin-bp-availability-schedule-title">{{ $scheduleDate }}</h3>
                <p class="admin-bp-availability-schedule-hours">{{ $workingHoursLabel }}</p>
            </div>

            <div class="admin-bp-availability-slots">
                @foreach ($slots as $slot)
                <div class="admin-bp-availability-slot {{ ! empty($slot['active']) ? 'is-active' : '' }}">
                    <span class="admin-bp-availability-slot-time">{{ $slot['time'] }}</span>
                    <div class="admin-bp-availability-slot-card">
                        <div class="admin-bp-availability-slot-main">
                            <p class="admin-bp-availability-slot-line">
                                <span>{{ $slot['id'] }}</span>
                                <span class="admin-bp-availability-slot-sep" aria-hidden="true">·</span>
                                <span>{{ $slot['service'] }}</span>
                            </p>
                            <p class="admin-bp-availability-slot-client">{{ $slot['client'] }}</p>
                        </div>
                        <div class="admin-bp-availability-slot-side">
                            <p class="admin-bp-availability-slot-range">{{ $slot['range'] }}</p>
                            <p class="admin-bp-availability-slot-location">{{ $slot['location'] }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <aside class="admin-bp-availability-calendar-wrap">
            <div class="admin-bp-availability-calendar-nav">
                <button type="button" class="admin-bp-availability-cal-arrow" aria-label="Previous day">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                        <circle cx="14" cy="14" r="13.5" fill="#FFF" stroke="#F1F0F0" />
                        <path d="M15.5 10L12 14L15.5 18" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
                <p class="admin-bp-availability-calendar-label">{{ $calendarLabel }}</p>
                <button type="button" class="admin-bp-availability-cal-arrow" aria-label="Next day">
                    <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                        <circle cx="14" cy="14" r="13.5" fill="#FFF" stroke="#F1F0F0" />
                        <path d="M12.5 10L16 14L12.5 18" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>

            <div class="admin-bp-availability-card admin-bp-availability-calendar">
                <div class="admin-bp-availability-cal-days" aria-hidden="true">
                    @foreach (['M', 'T', 'W', 'T', 'F', 'S', 'S'] as $dayLabel)
                    <span>{{ $dayLabel }}</span>
                    @endforeach
                </div>
                <div class="admin-bp-availability-cal-grid">
                    @foreach ($calendarDays as $week)
                        @foreach ($week as $day)
                            @if ($day === null)
                            <span class="admin-bp-availability-cal-date is-empty"></span>
                            @else
                            <button
                                type="button"
                                class="admin-bp-availability-cal-date {{ $day === $activeDay ? 'is-active' : '' }}"
                                aria-label="Day {{ $day }}"
                                @if ($day === $activeDay) aria-current="date" @endif>
                                {{ $day }}
                            </button>
                            @endif
                        @endforeach
                    @endforeach
                </div>
            </div>
        </aside>
    </div>

    <div class="admin-bp-availability-mid">
        <section class="admin-bp-availability-card admin-bp-availability-hours">
            <x-admin.customer.section-header title="Working week hours">
                <button type="button" class="admin-co-link-btn">
                    {!! $editIcon !!}
                    Edit
                </button>
            </x-admin.customer.section-header>

            <div class="admin-bp-availability-hours-table-wrap">
                <table class="admin-bp-availability-hours-table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Working hours</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($weekHours as $row)
                        <tr>
                            <td>{{ $row['day'] }}</td>
                            <td>{{ $row['hours'] }}</td>
                            <td>
                                @if (($row['status'] ?? null) === 'available')
                                <span class="admin-bp-availability-status is-available">{{ $row['status_label'] }}</span>
                                @else
                                <span class="admin-bp-availability-empty">—</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>

        <section class="admin-bp-availability-card admin-bp-availability-timeoff">
            <x-admin.customer.section-header title="Time off">
                <button type="button" class="admin-co-link-btn">
                    {!! $editIcon !!}
                    Edit
                </button>
            </x-admin.customer.section-header>

            <ul class="admin-bp-availability-timeoff-list">
                @foreach ($timeOff as $item)
                <li class="admin-bp-availability-timeoff-item">
                    <div class="admin-bp-availability-timeoff-main">
                        <p class="admin-bp-availability-timeoff-range">{{ $item['range'] }}</p>
                        <p class="admin-bp-availability-timeoff-reason">
                            Reason (optional): <span>{{ $item['reason'] }}</span>
                        </p>
                        <p class="admin-bp-availability-timeoff-duration">{{ $item['duration'] }}</p>
                    </div>
                    <span class="admin-bp-availability-timeoff-tag is-{{ $item['status'] }}">{{ $item['status_label'] }}</span>
                </li>
                @endforeach
            </ul>

            <div class="admin-bp-availability-timeoff-foot">
                <div class="admin-bp-availability-dots" aria-hidden="true">
                    <span class="is-active"></span>
                    <span></span>
                    <span></span>
                </div>
                <div class="admin-bp-availability-timeoff-nav">
                    <button type="button" class="admin-bp-availability-cal-arrow" aria-label="Previous time off page" disabled>
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                            <circle cx="14" cy="14" r="13.5" fill="#F3F3F3" stroke="#F1F0F0" />
                            <path d="M15.5 10L12 14L15.5 18" stroke="#9C9A97" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                    <button type="button" class="admin-bp-availability-cal-arrow" aria-label="Next time off page">
                        <svg xmlns="http://www.w3.org/2000/svg" width="28" height="28" viewBox="0 0 28 28" fill="none" aria-hidden="true">
                            <circle cx="14" cy="14" r="13.5" fill="#FFF" stroke="#F1F0F0" />
                            <path d="M12.5 10L16 14L12.5 18" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>
                </div>
            </div>
        </section>
    </div>

    <section class="admin-bp-availability-card admin-bp-availability-intake">
        <x-admin.customer.section-header title="Booking intake status" />
        <dl class="booking-intake-details admin-co-details admin-bp-availability-intake-details">
            @foreach ($intake as $row)
            <div class="admin-co-details-row">
                <dt>{{ $row['label'] }}</dt>
                <dd>{{ $row['value'] }}</dd>
            </div>
            @endforeach
        </dl>
    </section>

    <section class="admin-card admin-co-panel admin-co-activity-panel admin-bp-availability-timeline">
        <x-admin.customer.section-header title="Activity timeline">
            <button type="button" class="admin-co-link-btn">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 11 11" fill="none" aria-hidden="true">
                    <path d="M10.0279 0.00548759L10.0224 6.35299L9.19398 6.33653C9.02574 6.33653 8.90687 6.28715 8.83738 6.1884C8.76789 6.08234 8.73131 5.95067 8.72766 5.7934L8.73863 3.11615C8.73863 2.92596 8.74411 2.74857 8.75509 2.58399C8.7624 2.41575 8.7752 2.2603 8.79349 2.11766C8.6033 2.35906 8.39849 2.60776 8.17904 2.86378C7.95959 3.11249 7.72917 3.35754 7.48778 3.59893L1.29115 9.79556C0.99573 10.091 0.516763 10.091 0.221345 9.79556C-0.0740737 9.50014 -0.0740733 9.02118 0.221345 8.72576L6.41798 2.52913C6.65937 2.28774 6.90808 2.05732 7.1641 1.83787C7.41646 1.61476 7.66517 1.40995 7.91022 1.22342C7.76392 1.24536 7.60848 1.26182 7.44389 1.27279C7.27565 1.28011 7.09643 1.28377 6.90625 1.28377L4.20705 1.29474C4.05344 1.29474 3.9236 1.25999 3.81753 1.1905C3.71512 1.11735 3.66392 0.996656 3.66392 0.828413L3.64746 1.01217e-06L10.0279 0.00548759Z" fill="#3B3731" />
                </svg>
                Full Log
            </button>
        </x-admin.customer.section-header>
        <ul class="admin-co-activity">
            @foreach ($timeline as $item)
            <li class="admin-co-activity-item">
                <span class="admin-co-activity-dot is-{{ $item['tone'] }}" aria-hidden="true"></span>
                <div>
                    <p class="admin-co-activity-title">{{ $item['title'] }}</p>
                    <span class="admin-co-activity-time">{{ $item['time'] }}</span>
                </div>
            </li>
            @endforeach
        </ul>
    </section>
</div>
