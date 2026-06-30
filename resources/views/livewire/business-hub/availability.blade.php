@php
    $profileId = auth('groomer_spacer')->id();

    $userBookings = $profileId
        ? \App\Models\Booking::query()
            ->with(['petOwner:id,name,created_at', 'pets:id,name,photo,pet_type,breed,sex,weight,notes'])
            ->where('goormer_spacer_id', $profileId)
            ->whereBetween('date', [now()->subMonths(3)->startOfDay(), now()->addYear()->endOfDay()])
            ->latest('date')
            ->latest('time')
            ->get()
        : collect();
    $loggedUserType = (string) (auth('groomer_spacer')->user()?->user_type ?? (auth()->user()?->user_type ?? ''));
    $isSpaceAccount = strtolower($loggedUserType) === 'space';

    $availabilitySpaceDurationKind = static function (?string $service): string {
        $s = strtolower(trim((string) $service));
        if ($s === '') {
            return '';
        }
        if (preg_match('/full[\s_-]*day|fullday/', $s)) {
            return 'Full day';
        }
        if (preg_match('/half[\s_-]*day/', $s)) {
            return 'Half-Day';
        }
        if (str_contains($s, 'hour')) {
            return 'Hourly';
        }

        return '';
    };

    $availabilitySpaceAddOnLabels = static function ($booking): array {
        $raw = $booking->extra_add_ons ?? null;
        if (!is_array($raw)) {
            return [];
        }
        $out = [];
        foreach ($raw as $item) {
            $label = '';
            if (is_array($item)) {
                $label = trim((string) ($item['label'] ?? ''));
            } elseif (is_string($item)) {
                $label = trim($item);
            }
            if ($label !== '') {
                $out[] = $label;
            }
        }

        return $out;
    };

    /** Human-readable booking time for labels (handles strings, Carbon, TIME columns). */
    $availabilityNormalizedBookingTime = static function ($booking): string {
        $t = $booking->time ?? null;
        if ($t === null || $t === '') {
            return '';
        }
        if ($t instanceof \DateTimeInterface) {
            return $t->format('H:i');
        }
        $s = trim(preg_replace('/\s+/', ' ', (string) $t));
        if ($s === '') {
            return '';
        }

        // Range like "08:30 - 12:30" or "08:30:00 - 12:30:00"
        if (preg_match('/^(\d{1,2}:\d{2})(?::\d{2})?\s*-\s*(\d{1,2}:\d{2})(?::\d{2})?$/', $s, $range)) {
            return $range[1] . ' - ' . $range[2];
        }

        // Single time at start
        if (preg_match('/^(\d{1,2}:\d{2})(?::\d{2})?/', $s, $m)) {
            return $m[1];
        }

        return $s;
    };

    $availabilitySpaceDurationLine = static function ($booking) use (
        $availabilitySpaceDurationKind,
        $availabilityNormalizedBookingTime,
    ): string {
        $kind = $availabilitySpaceDurationKind($booking->service ?? null);
        $timeRaw = $availabilityNormalizedBookingTime($booking);

        // Derive kind from the time range duration when service doesn't explicitly say so.
    if (
        $kind === '' &&
        $timeRaw !== '' &&
        preg_match('/^(\d{1,2}):(\d{2})\s*-\s*(\d{1,2}):(\d{2})$/', $timeRaw, $r)
    ) {
        $startMinutes = ((int) $r[1]) * 60 + (int) $r[2];
        $endMinutes = ((int) $r[3]) * 60 + (int) $r[4];
        $diff = $endMinutes - $startMinutes;
        if ($diff < 0) {
            $diff += 24 * 60; // wraps past midnight
        }
        if ($diff >= 7 * 60) {
            $kind = 'Full day';
        } elseif ($diff >= 3 * 60) {
            $kind = 'Half-Day';
        } else {
            $kind = 'Hourly';
        }
    } elseif ($kind === '' && $timeRaw !== '') {
        $kind = 'Hourly';
    }

    if ($kind !== '' && $timeRaw !== '') {
        return $kind . ' (' . $timeRaw . ')';
    }

    if ($kind !== '') {
        return $kind;
    }

    return $timeRaw !== '' ? $timeRaw : 'Time not set';
};

$previewPet = $userBookings->first()?->pets?->first();
$previewPetSizeLabel = null;
if ($isSpaceAccount) {
    $previewPetWeight = (float) ($previewPet?->weight ?? 0);
    if ($previewPetWeight > 0) {
        $previewPetSizeLabel = $previewPetWeight <= 7 ? 'Small' : ($previewPetWeight <= 18 ? 'Medium' : 'Large');
    }
}

$calendarBookingsByDate = [];
$calendarBookingsById = [];
foreach ($userBookings as $booking) {
    if (!$booking->date) {
        continue;
    }
    $dateKey = $booking->date->format('Y-m-d');
    $bookingStatus = strtolower((string) ($booking->booking_status ?? ''));
    $statusBadgeClass = match ($bookingStatus) {
        'confirmed' => 'is-confirmed',
        'pending' => 'is-pending',
        'cancelled' => 'is-cancelled',
        default => 'is-default',
    };
    $slotType = match ($bookingStatus) {
        'confirmed' => 'green',
        'pending' => 'orange',
        'cancelled' => 'red',
        default => 'blue',
    };
    $normalizedTime = $availabilityNormalizedBookingTime($booking);
    $pillTime = $normalizedTime;
    if ($pillTime !== '' && str_contains($pillTime, ' - ')) {
        $pillTime = trim(explode(' - ', $pillTime, 2)[0]);
    }
    $timeLabel = $pillTime !== '' ? $pillTime : '—';
    $firstPet = $booking->pets->first();
    $petPhoto = trim((string) ($firstPet?->photo ?? ''));
    $petPhotoUrl = $petPhoto !== '' ? $petPhoto : asset('images/ellipse-65.svg');
    $petSizeLabel = null;
    if ($isSpaceAccount) {
        $w = (float) ($firstPet?->weight ?? 0);
        if ($w > 0) {
            $petSizeLabel = $w <= 7 ? 'Small' : ($w <= 18 ? 'Medium' : 'Large');
        }
    }
    $clientSinceDate = $booking->petOwner?->created_at ?? $booking->created_at;
    $calendarBookingsByDate[$dateKey][] = [
        'bookingId' => $booking->id,
        'label' => $timeLabel,
        'type' => $slotType,
    ];
    $calendarBookingsById[(string) $booking->id] = [
        'id' => $booking->id,
        'statusBadgeClass' => $statusBadgeClass,
        'client' => $booking->petOwner?->name ?? (auth()->user()?->name ?? 'Client'),
        'clientSince' => $clientSinceDate ? $clientSinceDate->format('M d, Y') : 'N/A',
        'status' => ucfirst((string) ($booking->booking_status ?: 'unknown')),
        'date' => $booking->date->format('l, jS F d/m/Y'),
        'time' => $normalizedTime !== '' ? $normalizedTime : 'Time not set',
        'service' => (string) ($booking->service ?? ''),
        'petName' => $firstPet?->name ?? 'Pet',
        'petType' => $firstPet?->pet_type ?? 'Pet type',
        'petBreed' => (string) ($firstPet?->breed ?? ''),
        'petSizeLabel' => $petSizeLabel,
        'petPhoto' => $petPhotoUrl,
        'petSex' => $firstPet?->sex ? ucfirst($firstPet->sex) : 'Not provided',
        'petWeight' => $firstPet?->weight ?: 'Not provided',
        'petNotes' => $firstPet?->notes ?: 'No notes',
        'spacePetTitle' => $firstPet?->name ?? 'Pet',
        'spaceDurationLine' => $availabilitySpaceDurationLine($booking),
        'spaceServiceLabel' => trim((string) ($booking->service ?? '')) ?: '—',
        'spacePetType' => $firstPet?->pet_type ?? 'Pet type',
        'spaceAddOns' => $availabilitySpaceAddOnLabels($booking),
    ];
}
foreach ($calendarBookingsByDate as $dk => $rows) {
    usort($calendarBookingsByDate[$dk], fn($a, $b) => strcmp((string) $a['label'], (string) $b['label']));
    }
@endphp

<script>
    window.__availabilityCalendar = {
        byDate: @json($calendarBookingsByDate),
        byId: @json($calendarBookingsById),
        isSpace: @json($isSpaceAccount),
    };
</script>

<div class="availability-layout" x-data="availabilityCalendarShell()" x-init="init()"
    @keydown.escape.window="handleAvailabilityEscape()"
    @availability-navigate-day="onMiniCalendarDateSelect($event.detail)">
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
                <x-business-hub.availability.day-calendar />
            </div>

            <div x-show="activeView === 'month'" x-cloak x-transition:enter="availability-view-enter"
                x-transition:enter-start="availability-view-enter-start"
                x-transition:enter-end="availability-view-enter-end" x-transition:leave="availability-view-leave"
                x-transition:leave-start="availability-view-leave-start"
                x-transition:leave-end="availability-view-leave-end">
                <x-business-hub.availability.monthly-calendar />
            </div>

            <div x-show="activeView === 'week'" x-cloak x-transition:enter="availability-view-enter"
                x-transition:enter-start="availability-view-enter-start"
                x-transition:enter-end="availability-view-enter-end" x-transition:leave="availability-view-leave"
                x-transition:leave-start="availability-view-leave-start"
                x-transition:leave-end="availability-view-leave-end">
                <x-business-hub.availability.weekly-calendar />
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
                <article class="availability-booking-card {{ $isSpaceAccount ? 'is-space' : '' }}">
                    <div class="img-circle {{ $isSpaceAccount ? 'is-space' : '' }}">
                        <div>
                            <img src="{{ asset('images/ellipse-65.svg') }}" alt="Booking profile image" />
                        </div>
                    </div>
                    <div>
                        <div class="booking-chip {{ $isSpaceAccount ? 'is-space' : '' }}">Home Visits</div>
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
                                @if ($isSpaceAccount)
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="13"
                                        viewBox="0 0 15 13" fill="none">
                                        <path
                                            d="M5.5438 12.2186C2.69175 11.808 0.5 9.35597 0.5 6.39044C0.5 3.13724 3.13744 0.5 6.39088 0.5C9.08249 0.5 11.3519 2.30515 12.0556 4.77069"
                                            stroke="#3B3731" stroke-linecap="round" />
                                        <path
                                            d="M12.5534 9.66857C12.679 9.54095 12.8175 9.43786 12.9687 9.35932C13.1218 9.28079 13.2976 9.24152 13.4959 9.24152C13.6609 9.24152 13.8052 9.269 13.9289 9.32398C14.0546 9.37896 14.1596 9.45652 14.244 9.55665C14.3285 9.65483 14.3923 9.77362 14.4355 9.91303C14.4787 10.0524 14.5003 10.2066 14.5003 10.3754V12.281H13.923V10.3754C13.923 10.1614 13.8739 9.99549 13.7757 9.87768C13.6775 9.75791 13.5273 9.69803 13.3251 9.69803C13.1778 9.69803 13.0394 9.73337 12.9098 9.80405C12.7821 9.87278 12.6633 9.96702 12.5534 10.0868V12.281H11.979V7.93384H12.5534V9.66857Z"
                                            fill="#3B3731" />
                                        <path
                                            d="M11.1689 11.8426V12.2814H8.88325V11.8426H9.77867V9.07116C9.77867 8.9828 9.78161 8.8915 9.7875 8.79725L9.06882 9.40691C9.04133 9.42851 9.01384 9.44225 8.98634 9.44814C8.95885 9.45403 8.93234 9.45502 8.90682 9.45109C8.88325 9.44716 8.86165 9.43931 8.84202 9.42753C8.82435 9.41378 8.80962 9.40004 8.79784 9.38629L8.61816 9.13595L9.8847 8.04327H10.353V11.8426H11.1689Z"
                                            fill="#3B3731" />
                                        <path
                                            d="M6.68523 4.62356C6.68523 4.4609 6.81711 4.32904 6.97978 4.32904C7.14245 4.32904 7.27432 4.4609 7.27432 4.62356V7.27426H5.21251C5.04984 7.27426 4.91797 7.1424 4.91797 6.97974C4.91797 6.81708 5.04984 6.68522 5.21251 6.68522H6.68523V4.62356Z"
                                            fill="#3B3731" />
                                    </svg>
                                @else
                                    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                        <circle cx="8" cy="8" r="6" stroke="#3B3731"
                                            stroke-width="1" />
                                        <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1"
                                            stroke-linecap="round" />
                                    </svg>
                                @endif
                                14:30 - 15:30
                            </li>
                            @unless ($isSpaceAccount)
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
                            @endunless
                            <li><svg xmlns="http://www.w3.org/2000/svg" width="13" height="12"
                                    viewBox="0 0 13 12" fill="none">
                                    <path
                                        d="M6.5 4.84211C4.69029 4.84211 3.16114 6.44318 2.66743 8.4996C2.45029 9.40392 2.77771 10.3638 3.58143 10.8148C4.21857 11.1723 5.16629 11.5 6.5 11.5C7.83371 11.5 8.78171 11.1723 9.41886 10.8148C10.2226 10.3638 10.5497 9.40392 10.3326 8.4996C9.83886 6.44289 8.30971 4.84211 6.5 4.84211ZM0.5 4.39168C0.5 5.19121 1.01143 6 1.64286 6C2.27429 6 2.78571 5.19121 2.78571 4.39168C2.78571 3.59216 2.27429 3.10526 1.64286 3.10526C1.01143 3.10526 0.5 3.59245 0.5 4.39168ZM12.5 4.39168C12.5 5.19121 11.9886 6 11.3571 6C10.7257 6 10.2143 5.19121 10.2143 4.39168C10.2143 3.59216 10.7257 3.10526 11.3571 3.10526C11.9886 3.10526 12.5 3.59245 12.5 4.39168ZM3.5 1.78642C3.5 2.58595 4.01143 3.39474 4.64286 3.39474C5.27429 3.39474 5.78571 2.58595 5.78571 1.78642C5.78571 0.986895 5.27429 0.5 4.64286 0.5C4.01143 0.5 3.5 0.987184 3.5 1.78642ZM9.5 1.78642C9.5 2.58595 8.98857 3.39474 8.35714 3.39474C7.72571 3.39474 7.21429 2.58595 7.21429 1.78642C7.21429 0.986895 7.72571 0.5 8.35714 0.5C8.98857 0.5 9.5 0.987184 9.5 1.78642Z"
                                        stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>Bella
                                <span class="pet-size-inline">
                                    @if ($isSpaceAccount && $previewPetSizeLabel)
                                        <span class="black-dot" style="margin: 0 5px;"></span>
                                        <span
                                            style="color: #3B3731; font-family: Lato; font-size: 14px; font-style: normal; font-weight: 400; line-height: normal;">{{ $previewPetSizeLabel }}</span>
                                    @else
                                        - Rabbit
                                    @endif
                                </span>
                            </li>
                        </ul>
                    </div>
                </article>
                <button type="button" class="availability-view-all" @click="openBookingsDrawer()">View All</button>
            </div>
        </aside>
    </div>

    <template x-teleport=".dashboard-content-wrapper">
        <div class="availability-teleport-fragment" style="display: contents">
            <div class="availability-bookings-drawer-layer" x-show="isBookingsDrawerOpen" x-cloak
                :style="{ top: drawerTopOffset + 'px' }">
                <button type="button" class="availability-bookings-drawer-backdrop" @click="closeBookingsDrawer()"
                    x-show="isBookingsDrawerOpen" x-transition.opacity aria-label="Close bookings drawer"></button>

                <aside class="availability-bookings-drawer" role="dialog" aria-modal="true"
                    aria-label="All bookings" x-show="isBookingsDrawerOpen" x-transition:enter="drawer-enter"
                    x-transition:enter-start="drawer-enter-start" x-transition:enter-end="drawer-enter-end"
                    x-transition:leave="drawer-leave" x-transition:leave-start="drawer-leave-start"
                    x-transition:leave-end="drawer-leave-end">
                    <header class="availability-drawer-head">
                        <h3 x-text="drawerHeadline">Bookings</h3>
                        <button type="button" class="availability-drawer-close" @click="closeBookingsDrawer()"
                            aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                viewBox="0 0 17 17" fill="none">
                                <path d="M0.75 15.75L15.75 0.75M0.75 0.75L15.75 15.75" stroke="#3B3731"
                                    stroke-width="1" stroke-linecap="round" />
                            </svg>
                        </button>
                    </header>

                    <div class="availability-drawer-body">
                        <div class="availability-drawer-empty"
                            x-show="drawerFilterDateKey && !drawerHasBookingsForFilter()" x-cloak>
                            <p>No bookings on this day.</p>
                        </div>
                        @forelse ($userBookings as $booking)
                            @php
                                $firstPet = $booking->pets->first();
                                $petPhoto = trim((string) ($firstPet?->photo ?? ''));
                                $petPhotoUrl = $petPhoto !== '' ? $petPhoto : asset('images/ellipse-65.svg');
                                $bookingDate = $booking->date?->format('l, jS F d/m/Y') ?? 'No date';
                                $bookingTime = $availabilityNormalizedBookingTime($booking);
                                $bookingStatus = strtolower((string) ($booking->booking_status ?? ''));
                                $bookingStatusClass = match ($bookingStatus) {
                                    'confirmed' => 'is-confirmed',
                                    'pending' => 'is-pending',
                                    'cancelled' => 'is-cancelled',
                                    default => 'is-default',
                                };
                                $bookingStatusLabel = ucfirst((string) ($booking->booking_status ?: 'unknown'));
                                $clientSinceDate = $booking->petOwner?->created_at ?? $booking->created_at;
                                $loggedUserType =
                                    (string) (auth('groomer_spacer')->user()?->user_type ??
                                        (auth()->user()?->user_type ?? ''));
                                $isSpaceAccount = strtolower($loggedUserType) === 'space';
                                $petSizeLabel = null;
                                if ($isSpaceAccount) {
                                    $petWeight = (float) ($firstPet?->weight ?? 0);
                                    if ($petWeight > 0) {
                                        $petSizeLabel =
                                            $petWeight <= 7 ? 'Small' : ($petWeight <= 18 ? 'Medium' : 'Large');
                                    }
                                }
                                $bookingDateKey = $booking->date?->format('Y-m-d') ?? '';
                                $spacePetTitle = $firstPet?->name ?? 'Pet';
                                $spaceDurationLine = $availabilitySpaceDurationLine($booking);
                                $spaceServiceLabel = trim((string) ($booking->service ?? '')) ?: '—';
                                $spacePetTypeLine = $firstPet?->pet_type ?? 'Pet type';
                                $spaceAddOnLabels = $availabilitySpaceAddOnLabels($booking);
                            @endphp
                            <article class="availability-drawer-booking-card {{ $bookingStatusClass }}"
                                x-show="!drawerFilterDateKey || drawerFilterDateKey === @js($bookingDateKey)">
                                <div class="availability-drawer-booking-top">
                                    <div class="availability-drawer-client">
                                        <img src="{{ asset('images/ellipse-65.svg') }}" alt="Client image" />
                                        <div style="display: flex;flex-direction: column;gap: 10px;">
                                            <div class="availability-drawer-client-name-row">
                                                <strong>{{ $booking->petOwner?->name ?? (auth()->user()?->name ?? 'Client') }}</strong>
                                                <span class="availability-drawer-space-badge" aria-hidden="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                        height="15" viewBox="0 0 15 15" fill="none">
                                                        <path fill-rule="evenodd" clip-rule="evenodd"
                                                            d="M11.191 0.633868C11.042 0.395853 10.8226 0.210202 10.5632 0.102758C10.3038 -0.00468587 10.0173 -0.0285778 9.7437 0.0344091L7.8018 0.480493C7.60279 0.526236 7.39599 0.526236 7.19698 0.480493L5.25508 0.0344091C4.98147 -0.0285778 4.69503 -0.00468587 4.43563 0.102758C4.17623 0.210202 3.95678 0.395853 3.80783 0.633868L2.7494 2.32315C2.64139 2.49597 2.49559 2.64178 2.32278 2.75087L0.63361 3.80938C0.396021 3.9582 0.210655 4.17731 0.103241 4.43628C-0.0041733 4.69525 -0.0283068 4.98124 0.0341899 5.25456L0.480244 7.19874C0.52582 7.39742 0.52582 7.60384 0.480244 7.80252L0.0341899 9.74563C-0.0285497 10.0191 -0.00453765 10.3053 0.102888 10.5645C0.210314 10.8237 0.395816 11.043 0.63361 11.1919L2.32278 12.2504C2.49559 12.3584 2.64139 12.5042 2.75048 12.677L3.80891 14.3663C4.11348 14.8534 4.69454 15.0943 5.25508 14.9658L7.19698 14.5197C7.39599 14.474 7.60279 14.474 7.8018 14.5197L9.74479 14.9658C10.0182 15.0285 10.3044 15.0045 10.5636 14.8971C10.8228 14.7896 11.0421 14.6041 11.191 14.3663L12.2494 12.677C12.3574 12.5042 12.5032 12.3584 12.676 12.2504L14.3663 11.1919C14.6041 11.0428 14.7895 10.8233 14.8967 10.5639C15.004 10.3044 15.0277 10.0181 14.9646 9.74455L14.5196 7.80252C14.4739 7.6035 14.4739 7.39669 14.5196 7.19766L14.9657 5.25456C15.0285 4.9812 15.0047 4.69505 14.8974 4.43587C14.7902 4.17669 14.6049 3.95734 14.3673 3.8083L12.6771 2.74979C12.5045 2.64158 12.3587 2.49573 12.2505 2.32315L11.191 0.633868ZM10.6477 5.09146C10.7145 4.96862 10.731 4.82465 10.6939 4.68985C10.6567 4.55505 10.5687 4.43992 10.4483 4.3687C10.328 4.29748 10.1847 4.27571 10.0487 4.30797C9.91264 4.34023 9.79441 4.42401 9.71886 4.54169L6.89457 9.32223L5.1892 7.68911C5.1386 7.63716 5.07807 7.59593 5.0112 7.56788C4.94433 7.53984 4.87249 7.52554 4.79998 7.52586C4.72747 7.52618 4.65576 7.54109 4.58914 7.56972C4.52251 7.59835 4.46234 7.64011 4.4122 7.6925C4.36206 7.74489 4.32299 7.80684 4.29731 7.87466C4.27163 7.94248 4.25987 8.01478 4.26274 8.08724C4.2656 8.1597 4.28303 8.23084 4.31398 8.29642C4.34493 8.362 4.38878 8.42068 4.44289 8.46895L6.63968 10.5741C6.69848 10.6303 6.76922 10.6725 6.84661 10.6976C6.92401 10.7226 7.00606 10.7298 7.08665 10.7187C7.16724 10.7076 7.24427 10.6784 7.312 10.6334C7.37973 10.5883 7.4364 10.5285 7.47779 10.4585L10.6477 5.09146Z"
                                                            fill="#CBDCE8" />
                                                    </svg>
                                                </span>
                                            </div>
                                            <small
                                                style="color: #9C9790; font-family: Lato; font-size: 16px; font-style: normal; font-weight: 400; line-height: normal;">
                                                Client since
                                                {{ $clientSinceDate ? $clientSinceDate->format('M d, Y') : 'N/A' }}
                                            </small>
                                        </div>
                                    </div>
                                    <span class="availability-drawer-status {{ $bookingStatusClass }}">
                                        {{ $bookingStatusLabel }}
                                    </span>
                                </div>

                                <div class="availability-drawer-meta {{ $isSpaceAccount ? 'is-space' : '' }}">
                                    <div class="availability-drawer-meta-row">
                                        <span class="availability-drawer-meta-item"><svg
                                                xmlns="http://www.w3.org/2000/svg" width="17" height="16"
                                                viewBox="0 0 17 16" fill="none">
                                                <path
                                                    d="M0.5 7.77557C0.5 4.88782 0.5 3.44357 1.4376 2.54684C2.3752 1.65012 3.8832 1.64935 6.9 1.64935H10.1C13.1168 1.64935 14.6256 1.64935 15.5624 2.54684C16.4992 3.44433 16.5 4.88782 16.5 7.77557V9.30712C16.5 12.1949 16.5 13.6391 15.5624 14.5358C14.6248 15.4326 13.1168 15.4333 10.1 15.4333H6.9C3.8832 15.4333 2.3744 15.4333 1.4376 14.5358C0.5008 13.6384 0.5 12.1949 0.5 9.30712V7.77557Z"
                                                    stroke="#3B3731" />
                                                <path
                                                    d="M4.50039 1.64867V0.5M12.5004 1.64867V0.5M0.900391 5.47755H16.1004"
                                                    stroke="#3B3731" stroke-linecap="round" />
                                                <path
                                                    d="M13.3004 11.6038C13.3004 11.8069 13.2162 12.0017 13.0661 12.1453C12.9161 12.2889 12.7126 12.3696 12.5004 12.3696C12.2883 12.3696 12.0848 12.2889 11.9348 12.1453C11.7847 12.0017 11.7004 11.8069 11.7004 11.6038C11.7004 11.4008 11.7847 11.206 11.9348 11.0624C12.0848 10.9188 12.2883 10.8381 12.5004 10.8381C12.7126 10.8381 12.9161 10.9188 13.0661 11.0624C13.2162 11.206 13.3004 11.4008 13.3004 11.6038ZM13.3004 8.54074C13.3004 8.74384 13.2162 8.93862 13.0661 9.08223C12.9161 9.22584 12.7126 9.30652 12.5004 9.30652C12.2883 9.30652 12.0848 9.22584 11.9348 9.08223C11.7847 8.93862 11.7004 8.74384 11.7004 8.54074C11.7004 8.33764 11.7847 8.14287 11.9348 7.99925C12.0848 7.85564 12.2883 7.77496 12.5004 7.77496C12.7126 7.77496 12.9161 7.85564 13.0661 7.99925C13.2162 8.14287 13.3004 8.33764 13.3004 8.54074ZM9.30044 11.6038C9.30044 11.8069 9.21615 12.0017 9.06612 12.1453C8.9161 12.2889 8.71261 12.3696 8.50044 12.3696C8.28827 12.3696 8.08478 12.2889 7.93475 12.1453C7.78472 12.0017 7.70044 11.8069 7.70044 11.6038C7.70044 11.4008 7.78472 11.206 7.93475 11.0624C8.08478 10.9188 8.28827 10.8381 8.50044 10.8381C8.71261 10.8381 8.9161 10.9188 9.06612 11.0624C9.21615 11.206 9.30044 11.4008 9.30044 11.6038ZM9.30044 8.54074C9.30044 8.74384 9.21615 8.93862 9.06612 9.08223C8.9161 9.22584 8.71261 9.30652 8.50044 9.30652C8.28827 9.30652 8.08478 9.22584 7.93475 9.08223C7.78472 8.93862 7.70044 8.74384 7.70044 8.54074C7.70044 8.33764 7.78472 8.14287 7.93475 7.99925C8.08478 7.85564 8.28827 7.77496 8.50044 7.77496C8.71261 7.77496 8.9161 7.85564 9.06612 7.99925C9.21615 8.14287 9.30044 8.33764 9.30044 8.54074ZM5.30044 11.6038C5.30044 11.8069 5.21615 12.0017 5.06612 12.1453C4.9161 12.2889 4.71261 12.3696 4.50044 12.3696C4.28827 12.3696 4.08478 12.2889 3.93475 12.1453C3.78473 12.0017 3.70044 11.8069 3.70044 11.6038C3.70044 11.4008 3.78473 11.206 3.93475 11.0624C4.08478 10.9188 4.28827 10.8381 4.50044 10.8381C4.71261 10.8381 4.9161 10.9188 5.06612 11.0624C5.21615 11.206 5.30044 11.4008 5.30044 11.6038ZM5.30044 8.54074C5.30044 8.74384 5.21615 8.93862 5.06612 9.08223C4.9161 9.22584 4.71261 9.30652 4.50044 9.30652C4.28827 9.30652 4.08478 9.22584 3.93475 9.08223C3.78473 8.93862 3.70044 8.74384 3.70044 8.54074C3.70044 8.33764 3.78473 8.14287 3.93475 7.99925C4.08478 7.85564 4.28827 7.77496 4.50044 7.77496C4.71261 7.77496 4.9161 7.85564 5.06612 7.99925C5.21615 8.14287 5.30044 8.33764 5.30044 8.54074Z"
                                                    fill="#3B3731" />
                                            </svg>{{ $bookingDate }}</span>
                                    </div>
                                    @unless ($isSpaceAccount)
                                        <div class="availability-drawer-meta-row">
                                            <span class="availability-drawer-meta-item">
                                                <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                                    <circle cx="8" cy="8" r="6" stroke="#3B3731"
                                                        stroke-width="1" />
                                                    <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1"
                                                        stroke-linecap="round" />
                                                </svg>
                                                {{ $bookingTime !== '' ? $bookingTime : 'Time not set' }}
                                            </span>
                                            <span class="availability-drawer-meta-item"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="16" height="17"
                                                    viewBox="0 0 16 17" fill="none">
                                                    <path
                                                        d="M4.9464 11.5546C6.23165 12.8398 9.35755 11.7981 11.928 9.22725C14.499 6.65675 15.5407 3.53086 14.2554 2.24561M8.72809 1.3726L9.30983 1.95475M6.69202 3.40908L7.27376 3.99082M4.94599 5.73643L5.52773 6.31816M4.36426 8.64551L4.94599 9.22725M11.928 0.5L12.5098 1.08173M11.3463 3.99123L12.5098 5.1547M9.31024 6.02771L10.4737 7.19118M6.98289 7.77291L8.14636 8.93638"
                                                        stroke="#3B3731" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M4.94549 13.3C5.42749 12.818 5.42749 12.0365 4.94549 11.5545C4.46349 11.0725 3.68202 11.0725 3.20002 11.5545L0.872726 13.8818C0.390728 14.3638 0.390728 15.1453 0.872726 15.6273C1.35472 16.1092 2.1362 16.1092 2.6182 15.6273L4.94549 13.3Z"
                                                        stroke="#3B3731" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>{{ $booking->service ?: 'Service not set' }}</span>
                                        </div>
                                    @endunless
                                </div>

                                @if ($isSpaceAccount)
                                    <div class="availability-drawer-space" style="margin-top: 0;">
                                        <div class="availability-drawer-space-main">
                                            <div class="availability-drawer-space-duration">
                                                <svg class="availability-drawer-space-duration-icon" width="16"
                                                    height="16" viewBox="0 0 16 16" fill="none"
                                                    aria-hidden="true">
                                                    <circle cx="8" cy="8" r="6" stroke="#3B3731"
                                                        stroke-width="1" />
                                                    <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1"
                                                        stroke-linecap="round" />
                                                </svg>
                                                <span>{{ $spaceDurationLine }}</span>
                                            </div>
                                            <div class="availability-drawer-space-visit">
                                                <span class="availability-drawer-space-visit-icon" aria-hidden="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="20"
                                                        height="16" viewBox="0 0 20 16" fill="none">
                                                        <path
                                                            d="M16.7801 15.5092V4.90774C16.7801 4.8823 16.7822 4.85737 16.7861 4.83297L13.9198 2.38869C13.3102 1.86965 12.8891 1.51192 12.5318 1.2787C12.1869 1.05357 11.955 0.981548 11.7333 0.981548C11.5117 0.981548 11.2813 1.05383 10.9367 1.2787C10.5794 1.51195 10.1571 1.86936 9.54677 2.38869L6.67838 4.83297C6.68232 4.85747 6.68642 4.8822 6.68642 4.90774V15.5092C6.68599 15.7799 6.45561 16 6.17148 16C5.88754 15.9998 5.65697 15.7798 5.65654 15.5092V5.70333L5.1255 6.15768C4.91349 6.33835 4.5869 6.31948 4.39734 6.11742C4.20853 5.91549 4.2262 5.60589 4.43757 5.42535L8.85884 1.65828H8.86085C9.45081 1.15631 9.92858 0.747109 10.3534 0.469686C10.791 0.184003 11.2255 2.87993e-07 11.7333 0C12.241 0 12.6754 0.183991 13.1132 0.469686C13.5382 0.747176 14.018 1.15613 14.6077 1.65828L19.029 5.42535C19.2403 5.60589 19.258 5.91549 19.0692 6.11742C18.8796 6.31948 18.553 6.33835 18.341 6.15768L17.81 5.70333V15.5092C17.8096 15.7798 17.579 15.9998 17.2951 16C17.0109 16 16.7805 15.7799 16.7801 15.5092Z"
                                                            fill="#3B3731" />
                                                        <path
                                                            d="M2.33495 8.53426C2.33495 8.16407 2.22966 7.84645 2.07983 7.63201C1.92995 7.41771 1.75582 7.32764 1.6 7.32764C1.44427 7.32777 1.26995 7.41787 1.12017 7.63201C0.970467 7.84645 0.865048 8.16427 0.865048 8.53426C0.865185 8.90438 0.970282 9.22221 1.12017 9.43651C1.26992 9.65054 1.44431 9.73895 1.6 9.73908C1.75569 9.73908 1.93003 9.65044 2.07983 9.43651C2.22972 9.22221 2.33481 8.90438 2.33495 8.53426ZM3.2 8.53426C3.19986 9.08569 3.04508 9.60303 2.77254 9.99272C2.49976 10.3827 2.08915 10.6667 1.6 10.6667C1.11119 10.6666 0.701863 10.3824 0.429145 9.99272C0.156583 9.60302 0.000136435 9.08572 0 8.53426C0 7.98254 0.156463 7.46387 0.429145 7.07399C0.701863 6.68442 1.11129 6.40016 1.6 6.40002C2.08908 6.40002 2.49976 6.68407 2.77254 7.07399C3.04523 7.46387 3.2 7.98254 3.2 8.53426Z"
                                                            fill="#3B3731" />
                                                        <path
                                                            d="M1.06689 15.5V10.0999C1.06689 9.82382 1.30568 9.59998 1.60023 9.59998C1.89478 9.59998 2.13356 9.82382 2.13356 10.0999V15.5C2.13334 15.776 1.89464 16 1.60023 16C1.30581 16 1.06712 15.776 1.06689 15.5Z"
                                                            fill="#3B3731" />
                                                        <path
                                                            d="M13.6421 11.9202C13.6421 11.4847 13.6402 11.2082 13.6125 11.0056C13.5868 10.8177 13.5475 10.7688 13.5237 10.7453C13.4999 10.7219 13.4506 10.6814 13.2592 10.656C13.0531 10.6287 12.7707 10.6288 12.3276 10.6288H11.4197C10.9766 10.6288 10.6942 10.6287 10.4882 10.656C10.2968 10.6814 10.2475 10.7219 10.2237 10.7453C10.1999 10.7688 10.1606 10.8177 10.1349 11.0056C10.1072 11.2082 10.1053 11.4847 10.1053 11.9202V15.0041H13.6421V11.9202ZM12.7836 6.94881C13.0622 6.94902 13.2884 7.17187 13.2888 7.44595C13.2888 7.72038 13.0624 7.94288 12.7836 7.94309H10.9638C10.6849 7.94288 10.4586 7.72038 10.4586 7.44595C10.459 7.17187 10.6852 6.94901 10.9638 6.94881H12.7836ZM12.7836 4.26501L12.8842 4.27472C13.1149 4.32077 13.2888 4.52163 13.2888 4.76216C13.2888 5.00269 13.1149 5.20354 12.8842 5.24959L12.7836 5.2593H10.9638C10.6849 5.25909 10.4586 5.03659 10.4586 4.76216C10.4586 4.48772 10.6849 4.26522 10.9638 4.26501H12.7836ZM14.6526 15.0041H18.6947C18.9738 15.0041 19.2 15.2266 19.2 15.5012C19.1996 15.7754 18.9735 15.9983 18.6947 15.9983H0.505263C0.226477 15.9983 0.000425559 15.7754 0 15.5012C0 15.2266 0.226214 15.0041 0.505263 15.0041H9.09474V11.9202C9.09474 11.513 9.09346 11.1577 9.13224 10.8735C9.17309 10.5747 9.26667 10.2811 9.50921 10.0424C9.7519 9.80356 10.0502 9.71164 10.3539 9.67144C10.643 9.6332 11.0053 9.63454 11.4197 9.63454H12.3276C12.742 9.63454 13.1043 9.6332 13.3934 9.67144C13.6972 9.71164 13.9955 9.80356 14.2382 10.0424C14.4807 10.2811 14.5743 10.5747 14.6151 10.8735C14.6539 11.1577 14.6526 11.513 14.6526 11.9202V15.0041Z"
                                                            fill="#3B3731" />
                                                    </svg>
                                                </span>
                                                <div class="availability-drawer-space-visit-copy">
                                                    <p class="availability-drawer-space-service-line">
                                                        {{ $spaceServiceLabel }}</p>
                                                </div>
                                            </div>
                                            <div class="availability-drawer-space-pet-type-row">
                                                <span class="availability-drawer-space-pet-type-icon"
                                                    aria-hidden="true">
                                                    <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="15" viewBox="0 0 16 15" fill="none">
                                                        <path fill="none"
                                                            d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                            stroke="#3B3731" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>
                                                </span>
                                                <span
                                                    class="availability-drawer-space-pet-type-value">{{ $spacePetTypeLine }}</span>
                                            </div>
                                        </div>
                                        @if (!empty($spaceAddOnLabels))
                                            <div class="availability-drawer-space-addons">
                                                <div class="availability-drawer-space-addons-heading">Add-on Service
                                                </div>
                                                <div class="availability-drawer-space-addons-list">
                                                    @foreach ($spaceAddOnLabels as $addonLabel)
                                                        <div class="availability-drawer-space-addon">
                                                            {{ $addonLabel }}
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <div class="availability-drawer-pet">
                                        <div class="availability-drawer-pet-left">
                                            <img src="{{ $petPhotoUrl }}" alt="Pet image" />
                                            <div class="availability-drawer-pet-copy">
                                                <strong><svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                        height="15" viewBox="0 0 16 15" fill="none">
                                                        <path
                                                            d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                            stroke="#3B3731" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg>{{ $firstPet?->name ?: 'Pet' }}</strong>
                                                <small>
                                                    {{ $firstPet?->pet_type ?: 'Pet type' }}
                                                    @if ($firstPet?->breed)
                                                        <span class="black-dot"
                                                            style="margin: 0 5px;background:#9D9B98;"></span>
                                                        {{ $firstPet->breed }}
                                                    @endif
                                                </small>
                                            </div>
                                        </div>
                                        <div class="availability-drawer-pet-right">
                                            <p><span>
                                                    <svg fill="#9D9B98" width="15" height="15"
                                                        viewBox="0 0 61.13 61.13" xmlns="http://www.w3.org/2000/svg">
                                                        <path
                                                            d="M27.482,34.031v12.317h-6.92c-1.703,0-3.084,1.381-3.084,3.084s1.381,3.084,3.084,3.084h6.92v5.531c0,1.703,1.381,3.084,3.084,3.084s3.084-1.381,3.084-3.084v-5.531h6.92c1.703,0,3.084-1.381,3.084-3.084s-1.381-3.084-3.084-3.084h-6.92V34.031c7.993-1.458,14.072-8.467,14.072-16.874C47.723,7.697,40.026,0,30.566,0c-9.46,0-17.157,7.697-17.157,17.157C13.409,25.564,19.489,32.573,27.482,34.031z M30.566,6.169c6.059,0,10.988,4.929,10.988,10.988s-4.929,10.988-10.988,10.988s-10.988-4.929-10.988-10.988S24.507,6.169,30.566,6.169z" />
                                                    </svg>
                                                </span>
                                                {{ $firstPet?->sex ? ucfirst($firstPet->sex) : 'Not provided' }}</p>
                                            <p><span><svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                        height="16" viewBox="0 0 15 16" fill="none">
                                                        <path
                                                            d="M4.7373 3.14703C4.7373 3.84907 5.01619 4.52235 5.51261 5.01876C6.00903 5.51518 6.68232 5.79406 7.38436 5.79406C8.08641 5.79406 8.7597 5.51518 9.25612 5.01876C9.75254 4.52235 10.0314 3.84907 10.0314 3.14703C10.0314 2.44499 9.75254 1.77171 9.25612 1.2753C8.7597 0.778883 8.08641 0.5 7.38436 0.5C6.68232 0.5 6.00903 0.778883 5.51261 1.2753C5.01619 1.77171 4.7373 2.44499 4.7373 3.14703Z"
                                                            stroke="#9D9B98" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                        <path
                                                            d="M2.8269 5.79492H11.9416C12.1482 5.79489 12.3483 5.86739 12.507 5.99977C12.6657 6.13215 12.7728 6.31602 12.8098 6.51933L14.2542 14.4604C14.2774 14.5876 14.2723 14.7183 14.2394 14.8433C14.2064 14.9683 14.1464 15.0845 14.0636 15.1837C13.9807 15.283 13.8771 15.3628 13.76 15.4176C13.643 15.4723 13.5153 15.5007 13.386 15.5007H1.38249C1.25323 15.5007 1.12554 15.4723 1.00846 15.4176C0.891377 15.3628 0.78776 15.283 0.704935 15.1837C0.62211 15.0845 0.5621 14.9683 0.52915 14.8433C0.496199 14.7183 0.491113 14.5876 0.514251 14.4604L1.95866 6.51933C1.99565 6.31602 2.10282 6.13215 2.26149 5.99977C2.42016 5.86739 2.62026 5.79489 2.8269 5.79492Z"
                                                            stroke="#9D9B98" stroke-linecap="round"
                                                            stroke-linejoin="round" />
                                                    </svg></span> {{ $firstPet?->weight ?: 'Not provided' }}</p>
                                            <p><span><svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                        height="16" viewBox="0 0 15 16" fill="none">
                                                        <path
                                                            d="M13.5905 8.11123L13.9601 6.73016C14.3918 5.1182 14.6084 4.31257 14.4462 3.61489C14.3176 3.0641 14.0285 2.56382 13.6155 2.17734C13.093 1.68768 12.2866 1.4718 10.6747 1.04003C9.0627 0.607553 8.25636 0.391671 7.55939 0.55394C7.0086 0.682549 6.50833 0.971618 6.12185 1.38458C5.70224 1.83207 5.4835 2.48758 5.15824 3.67851L4.98382 4.32544L4.61425 5.70651C4.18177 7.31847 3.96589 8.1241 4.12816 8.82178C4.25677 9.37257 4.54584 9.87285 4.9588 10.2593C5.48135 10.749 6.28769 10.9649 7.89966 11.3974C9.35221 11.7862 10.1507 12 10.8048 11.9192C10.8763 11.9101 10.9463 11.8977 11.0149 11.882C11.5655 11.7538 12.0658 11.4652 12.4525 11.0528C12.9421 10.5295 13.158 9.7232 13.5905 8.11123Z"
                                                            stroke="#9D9B98" />
                                                        <path
                                                            d="M10.8047 11.9191C10.6553 12.3768 10.3927 12.7894 10.0413 13.1186C9.51875 13.6082 8.71241 13.8241 7.10045 14.2559C5.48848 14.6876 4.68214 14.9042 3.98517 14.7413C3.43447 14.6128 2.9342 14.324 2.54763 13.9113C2.05796 13.3888 1.84137 12.5824 1.4096 10.9705L1.04003 9.5894C0.607553 7.97744 0.391671 7.1711 0.55394 6.47413C0.682549 5.92334 0.971618 5.42306 1.38458 5.03658C1.90713 4.54692 2.71347 4.33104 4.32544 3.89856C4.62948 3.81659 4.90708 3.74296 5.15823 3.67767"
                                                            stroke="#9D9B98" />
                                                        <path
                                                            d="M7.48902 6.21875L10.9417 7.14375M6.93359 8.29036L9.0052 8.84507"
                                                            stroke="#9D9B98" stroke-linecap="round" />
                                                    </svg></span> {{ $firstPet?->notes ?: 'No notes' }}</p>
                                        </div>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <div class="availability-drawer-empty">
                                <p>No bookings found for your account.</p>
                            </div>
                        @endforelse
                    </div>
                </aside>
            </div>

            <div class="availability-bookings-drawer-layer availability-booking-detail-layer"
                x-show="isBookingDetailOpen" x-cloak :style="{ top: drawerTopOffset + 'px' }">
                <button type="button" class="availability-bookings-drawer-backdrop" @click="closeBookingDetail()"
                    x-show="isBookingDetailOpen" x-transition.opacity aria-label="Close booking details"></button>

                <aside class="availability-bookings-drawer" role="dialog" aria-modal="true"
                    aria-label="Booking details" x-show="isBookingDetailOpen" x-transition:enter="drawer-enter"
                    x-transition:enter-start="drawer-enter-start" x-transition:enter-end="drawer-enter-end"
                    x-transition:leave="drawer-leave" x-transition:leave-start="drawer-leave-start"
                    x-transition:leave-end="drawer-leave-end">
                    <header class="availability-drawer-head">
                        <h3>Booking</h3>
                        <button type="button" class="availability-drawer-close" @click="closeBookingDetail()"
                            aria-label="Close">
                            <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17"
                                viewBox="0 0 17 17" fill="none">
                                <path d="M0.75 15.75L15.75 0.75M0.75 0.75L15.75 15.75" stroke="#3B3731"
                                    stroke-width="1" stroke-linecap="round" />
                            </svg>
                        </button>
                    </header>

                    <div class="availability-drawer-body" x-show="selectedBooking">
                        <article class="availability-drawer-booking-card"
                            :class="selectedBooking ? selectedBooking.statusBadgeClass : ''">
                            <div class="availability-drawer-booking-top">
                                <div class="availability-drawer-client">
                                    <img src="{{ asset('images/ellipse-65.svg') }}" alt="Client image" />
                                    <div style="display: flex;flex-direction: column;gap: 10px;">
                                        <div class="availability-drawer-client-name-row">
                                            <span class="availability-drawer-space-badge" aria-hidden="true">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15"
                                                    viewBox="0 0 15 15" fill="none">
                                                    <path fill-rule="evenodd" clip-rule="evenodd"
                                                        d="M11.191 0.633868C11.042 0.395853 10.8226 0.210202 10.5632 0.102758C10.3038 -0.00468587 10.0173 -0.0285778 9.7437 0.0344091L7.8018 0.480493C7.60279 0.526236 7.39599 0.526236 7.19698 0.480493L5.25508 0.0344091C4.98147 -0.0285778 4.69503 -0.00468587 4.43563 0.102758C4.17623 0.210202 3.95678 0.395853 3.80783 0.633868L2.7494 2.32315C2.64139 2.49597 2.49559 2.64178 2.32278 2.75087L0.63361 3.80938C0.396021 3.9582 0.210655 4.17731 0.103241 4.43628C-0.0041733 4.69525 -0.0283068 4.98124 0.0341899 5.25456L0.480244 7.19874C0.52582 7.39742 0.52582 7.60384 0.480244 7.80252L0.0341899 9.74563C-0.0285497 10.0191 -0.00453765 10.3053 0.102888 10.5645C0.210314 10.8237 0.395816 11.043 0.63361 11.1919L2.32278 12.2504C2.49559 12.3584 2.64139 12.5042 2.75048 12.677L3.80891 14.3663C4.11348 14.8534 4.69454 15.0943 5.25508 14.9658L7.19698 14.5197C7.39599 14.474 7.60279 14.474 7.8018 14.5197L9.74479 14.9658C10.0182 15.0285 10.3044 15.0045 10.5636 14.8971C10.8228 14.7896 11.0421 14.6041 11.191 14.3663L12.2494 12.677C12.3574 12.5042 12.5032 12.3584 12.676 12.2504L14.3663 11.1919C14.6041 11.0428 14.7895 10.8233 14.8967 10.5639C15.004 10.3044 15.0277 10.0181 14.9646 9.74455L14.5196 7.80252C14.4739 7.6035 14.4739 7.39669 14.5196 7.19766L14.9657 5.25456C15.0285 4.9812 15.0047 4.69505 14.8974 4.43587C14.7902 4.17669 14.6049 3.95734 14.3673 3.8083L12.6771 2.74979C12.5045 2.64158 12.3587 2.49573 12.2505 2.32315L11.191 0.633868ZM10.6477 5.09146C10.7145 4.96862 10.731 4.82465 10.6939 4.68985C10.6567 4.55505 10.5687 4.43992 10.4483 4.3687C10.328 4.29748 10.1847 4.27571 10.0487 4.30797C9.91264 4.34023 9.79441 4.42401 9.71886 4.54169L6.89457 9.32223L5.1892 7.68911C5.1386 7.63716 5.07807 7.59593 5.0112 7.56788C4.94433 7.53984 4.87249 7.52554 4.79998 7.52586C4.72747 7.52618 4.65576 7.54109 4.58914 7.56972C4.52251 7.59835 4.46234 7.64011 4.4122 7.6925C4.36206 7.74489 4.32299 7.80684 4.29731 7.87466C4.27163 7.94248 4.25987 8.01478 4.26274 8.08724C4.2656 8.1597 4.28303 8.23084 4.31398 8.29642C4.34493 8.362 4.38878 8.42068 4.44289 8.46895L6.63968 10.5741C6.69848 10.6303 6.76922 10.6725 6.84661 10.6976C6.92401 10.7226 7.00606 10.7298 7.08665 10.7187C7.16724 10.7076 7.24427 10.6784 7.312 10.6334C7.37973 10.5883 7.4364 10.5285 7.47779 10.4585L10.6477 5.09146Z"
                                                        fill="#CBDCE8" />
                                                </svg>
                                            </span>
                                            <strong x-text="selectedBooking?.client"></strong>
                                        </div>
                                        <small
                                            style="color: #9C9790; font-family: Lato; font-size: 16px; font-style: normal; font-weight: 400; line-height: normal;">
                                            Client since
                                            <span x-text="selectedBooking?.clientSince"></span>
                                        </small>
                                    </div>
                                </div>
                                <span class="availability-drawer-status"
                                    :class="selectedBooking ? selectedBooking.statusBadgeClass : ''"
                                    x-text="selectedBooking?.status"></span>
                            </div>

                            <div class="availability-drawer-meta" :class="{ 'is-space': isSpaceAccount }">
                                <div class="availability-drawer-meta-row">
                                    <span class="availability-drawer-meta-item"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="17" height="16"
                                            viewBox="0 0 17 16" fill="none">
                                            <path
                                                d="M0.5 7.77557C0.5 4.88782 0.5 3.44357 1.4376 2.54684C2.3752 1.65012 3.8832 1.64935 6.9 1.64935H10.1C13.1168 1.64935 14.6256 1.64935 15.5624 2.54684C16.4992 3.44433 16.5 4.88782 16.5 7.77557V9.30712C16.5 12.1949 16.5 13.6391 15.5624 14.5358C14.6248 15.4326 13.1168 15.4333 10.1 15.4333H6.9C3.8832 15.4333 2.3744 15.4333 1.4376 14.5358C0.5008 13.6384 0.5 12.1949 0.5 9.30712V7.77557Z"
                                                stroke="#3B3731" />
                                            <path d="M4.50039 1.64867V0.5M12.5004 1.64867V0.5M0.900391 5.47755H16.1004"
                                                stroke="#3B3731" stroke-linecap="round" />
                                            <path
                                                d="M13.3004 11.6038C13.3004 11.8069 13.2162 12.0017 13.0661 12.1453C12.9161 12.2889 12.7126 12.3696 12.5004 12.3696C12.2883 12.3696 12.0848 12.2889 11.9348 12.1453C11.7847 12.0017 11.7004 11.8069 11.7004 11.6038C11.7004 11.4008 11.7847 11.206 11.9348 11.0624C12.0848 10.9188 12.2883 10.8381 12.5004 10.8381C12.7126 10.8381 12.9161 10.9188 13.0661 11.0624C13.2162 11.206 13.3004 11.4008 13.3004 11.6038ZM13.3004 8.54074C13.3004 8.74384 13.2162 8.93862 13.0661 9.08223C12.9161 9.22584 12.7126 9.30652 12.5004 9.30652C12.2883 9.30652 12.0848 9.22584 11.9348 9.08223C11.7847 8.93862 11.7004 8.74384 11.7004 8.54074C11.7004 8.33764 11.7847 8.14287 11.9348 7.99925C12.0848 7.85564 12.2883 7.77496 12.5004 7.77496C12.7126 7.77496 12.9161 7.85564 13.0661 7.99925C13.2162 8.14287 13.3004 8.33764 13.3004 8.54074ZM9.30044 11.6038C9.30044 11.8069 9.21615 12.0017 9.06612 12.1453C8.9161 12.2889 8.71261 12.3696 8.50044 12.3696C8.28827 12.3696 8.08478 12.2889 7.93475 12.1453C7.78472 12.0017 7.70044 11.8069 7.70044 11.6038C7.70044 11.4008 7.78472 11.206 7.93475 11.0624C8.08478 10.9188 8.28827 10.8381 8.50044 10.8381C8.71261 10.8381 8.9161 10.9188 9.06612 11.0624C9.21615 11.206 9.30044 11.4008 9.30044 11.6038ZM9.30044 8.54074C9.30044 8.74384 9.21615 8.93862 9.06612 9.08223C8.9161 9.22584 8.71261 9.30652 8.50044 9.30652C8.28827 9.30652 8.08478 9.22584 7.93475 9.08223C7.78472 8.93862 7.70044 8.74384 7.70044 8.54074C7.70044 8.33764 7.78472 8.14287 7.93475 7.99925C8.08478 7.85564 8.28827 7.77496 8.50044 7.77496C8.71261 7.77496 8.9161 7.85564 9.06612 7.99925C9.21615 8.14287 9.30044 8.33764 9.30044 8.54074ZM5.30044 11.6038C5.30044 11.8069 5.21615 12.0017 5.06612 12.1453C4.9161 12.2889 4.71261 12.3696 4.50044 12.3696C4.28827 12.3696 4.08478 12.2889 3.93475 12.1453C3.78473 12.0017 3.70044 11.8069 3.70044 11.6038C3.70044 11.4008 3.78473 11.206 3.93475 11.0624C4.08478 10.9188 4.28827 10.8381 4.50044 10.8381C4.71261 10.8381 4.9161 10.9188 5.06612 11.0624C5.21615 11.206 5.30044 11.4008 5.30044 11.6038ZM5.30044 8.54074C5.30044 8.74384 5.21615 8.93862 5.06612 9.08223C4.9161 9.22584 4.71261 9.30652 4.50044 9.30652C4.28827 9.30652 4.08478 9.22584 3.93475 9.08223C3.78473 8.93862 3.70044 8.74384 3.70044 8.54074C3.70044 8.33764 3.78473 8.14287 3.93475 7.99925C4.08478 7.85564 4.28827 7.77496 4.50044 7.77496C4.71261 7.77496 4.9161 7.85564 5.06612 7.99925C5.21615 8.14287 5.30044 8.33764 5.30044 8.54074Z"
                                                fill="#3B3731" />
                                        </svg><span x-text="selectedBooking?.date"></span></span>
                                </div>
                                <div class="availability-drawer-meta-row" x-show="!isSpaceAccount">
                                    <span class="availability-drawer-meta-item">
                                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <circle cx="8" cy="8" r="6" stroke="#3B3731"
                                                stroke-width="1" />
                                            <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1"
                                                stroke-linecap="round" />
                                        </svg>
                                        <span x-text="selectedBooking?.time"></span>
                                    </span>
                                    <span class="availability-drawer-meta-item"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="16" height="17"
                                            viewBox="0 0 16 17" fill="none">
                                            <path
                                                d="M4.9464 11.5546C6.23165 12.8398 9.35755 11.7981 11.928 9.22725C14.499 6.65675 15.5407 3.53086 14.2554 2.24561M8.72809 1.3726L9.30983 1.95475M6.69202 3.40908L7.27376 3.99082M4.94599 5.73643L5.52773 6.31816M4.36426 8.64551L4.94599 9.22725M11.928 0.5L12.5098 1.08173M11.3463 3.99123L12.5098 5.1547M9.31024 6.02771L10.4737 7.19118M6.98289 7.77291L8.14636 8.93638"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M4.94549 13.3C5.42749 12.818 5.42749 12.0365 4.94549 11.5545C4.46349 11.0725 3.68202 11.0725 3.20002 11.5545L0.872726 13.8818C0.390728 14.3638 0.390728 15.1453 0.872726 15.6273C1.35472 16.1092 2.1362 16.1092 2.6182 15.6273L4.94549 13.3Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg><span
                                            x-text="selectedBooking?.service ? selectedBooking.service : 'Service not set'"></span></span>
                                </div>
                            </div>

                            <div class="availability-drawer-space" x-show="isSpaceAccount" x-cloak>
                                <div class="availability-drawer-space-main">
                                    <div class="availability-drawer-space-duration">
                                        <svg class="availability-drawer-space-duration-icon" width="16"
                                            height="16" viewBox="0 0 16 16" fill="none" aria-hidden="true">
                                            <circle cx="8" cy="8" r="6" stroke="#3B3731"
                                                stroke-width="1" />
                                            <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1"
                                                stroke-linecap="round" />
                                        </svg>
                                        <span
                                            x-text="selectedBooking && selectedBooking.spaceDurationLine ? selectedBooking.spaceDurationLine : (selectedBooking && selectedBooking.time ? selectedBooking.time : 'Time not set')"></span>
                                    </div>
                                    <div class="availability-drawer-space-visit">
                                        <span class="availability-drawer-space-visit-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="16"
                                                viewBox="0 0 20 16" fill="none">
                                                <path
                                                    d="M16.7801 15.5092V4.90774C16.7801 4.8823 16.7822 4.85737 16.7861 4.83297L13.9198 2.38869C13.3102 1.86965 12.8891 1.51192 12.5318 1.2787C12.1869 1.05357 11.955 0.981548 11.7333 0.981548C11.5117 0.981548 11.2813 1.05383 10.9367 1.2787C10.5794 1.51195 10.1571 1.86936 9.54677 2.38869L6.67838 4.83297C6.68232 4.85747 6.68642 4.8822 6.68642 4.90774V15.5092C6.68599 15.7799 6.45561 16 6.17148 16C5.88754 15.9998 5.65697 15.7798 5.65654 15.5092V5.70333L5.1255 6.15768C4.91349 6.33835 4.5869 6.31948 4.39734 6.11742C4.20853 5.91549 4.2262 5.60589 4.43757 5.42535L8.85884 1.65828H8.86085C9.45081 1.15631 9.92858 0.747109 10.3534 0.469686C10.791 0.184003 11.2255 2.87993e-07 11.7333 0C12.241 0 12.6754 0.183991 13.1132 0.469686C13.5382 0.747176 14.018 1.15613 14.6077 1.65828L19.029 5.42535C19.2403 5.60589 19.258 5.91549 19.0692 6.11742C18.8796 6.31948 18.553 6.33835 18.341 6.15768L17.81 5.70333V15.5092C17.8096 15.7798 17.579 15.9998 17.2951 16C17.0109 16 16.7805 15.7799 16.7801 15.5092Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M2.33495 8.53426C2.33495 8.16407 2.22966 7.84645 2.07983 7.63201C1.92995 7.41771 1.75582 7.32764 1.6 7.32764C1.44427 7.32777 1.26995 7.41787 1.12017 7.63201C0.970467 7.84645 0.865048 8.16427 0.865048 8.53426C0.865185 8.90438 0.970282 9.22221 1.12017 9.43651C1.26992 9.65054 1.44431 9.73895 1.6 9.73908C1.75569 9.73908 1.93003 9.65044 2.07983 9.43651C2.22972 9.22221 2.33481 8.90438 2.33495 8.53426ZM3.2 8.53426C3.19986 9.08569 3.04508 9.60303 2.77254 9.99272C2.49976 10.3827 2.08915 10.6667 1.6 10.6667C1.11119 10.6666 0.701863 10.3824 0.429145 9.99272C0.156583 9.60302 0.000136435 9.08572 0 8.53426C0 7.98254 0.156463 7.46387 0.429145 7.07399C0.701863 6.68442 1.11129 6.40016 1.6 6.40002C2.08908 6.40002 2.49976 6.68407 2.77254 7.07399C3.04523 7.46387 3.2 7.98254 3.2 8.53426Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M1.06689 15.5V10.0999C1.06689 9.82382 1.30568 9.59998 1.60023 9.59998C1.89478 9.59998 2.13356 9.82382 2.13356 10.0999V15.5C2.13334 15.776 1.89464 16 1.60023 16C1.30581 16 1.06712 15.776 1.06689 15.5Z"
                                                    fill="#3B3731" />
                                                <path
                                                    d="M13.6421 11.9202C13.6421 11.4847 13.6402 11.2082 13.6125 11.0056C13.5868 10.8177 13.5475 10.7688 13.5237 10.7453C13.4999 10.7219 13.4506 10.6814 13.2592 10.656C13.0531 10.6287 12.7707 10.6288 12.3276 10.6288H11.4197C10.9766 10.6288 10.6942 10.6287 10.4882 10.656C10.2968 10.6814 10.2475 10.7219 10.2237 10.7453C10.1999 10.7688 10.1606 10.8177 10.1349 11.0056C10.1072 11.2082 10.1053 11.4847 10.1053 11.9202V15.0041H13.6421V11.9202ZM12.7836 6.94881C13.0622 6.94902 13.2884 7.17187 13.2888 7.44595C13.2888 7.72038 13.0624 7.94288 12.7836 7.94309H10.9638C10.6849 7.94288 10.4586 7.72038 10.4586 7.44595C10.459 7.17187 10.6852 6.94901 10.9638 6.94881H12.7836ZM12.7836 4.26501L12.8842 4.27472C13.1149 4.32077 13.2888 4.52163 13.2888 4.76216C13.2888 5.00269 13.1149 5.20354 12.8842 5.24959L12.7836 5.2593H10.9638C10.6849 5.25909 10.4586 5.03659 10.4586 4.76216C10.4586 4.48772 10.6849 4.26522 10.9638 4.26501H12.7836ZM14.6526 15.0041H18.6947C18.9738 15.0041 19.2 15.2266 19.2 15.5012C19.1996 15.7754 18.9735 15.9983 18.6947 15.9983H0.505263C0.226477 15.9983 0.000425559 15.7754 0 15.5012C0 15.2266 0.226214 15.0041 0.505263 15.0041H9.09474V11.9202C9.09474 11.513 9.09346 11.1577 9.13224 10.8735C9.17309 10.5747 9.26667 10.2811 9.50921 10.0424C9.7519 9.80356 10.0502 9.71164 10.3539 9.67144C10.643 9.6332 11.0053 9.63454 11.4197 9.63454H12.3276C12.742 9.63454 13.1043 9.6332 13.3934 9.67144C13.6972 9.71164 13.9955 9.80356 14.2382 10.0424C14.4807 10.2811 14.5743 10.5747 14.6151 10.8735C14.6539 11.1577 14.6526 11.513 14.6526 11.9202V15.0041Z"
                                                    fill="#3B3731" />
                                            </svg>
                                        </span>
                                        <div class="availability-drawer-space-visit-copy">
                                            <p class="availability-drawer-space-service-line"
                                                x-text="selectedBooking && selectedBooking.spaceServiceLabel ? selectedBooking.spaceServiceLabel : '—'">
                                            </p>
                                        </div>
                                    </div>
                                    <div class="availability-drawer-space-pet-type-row">
                                        <span class="availability-drawer-space-pet-type-icon" aria-hidden="true">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="15"
                                                viewBox="0 0 16 15" fill="none">
                                                <path fill="none"
                                                    d="M8 6.02632C5.73786 6.02632 3.82643 8.06405 3.20929 10.6813C2.93786 11.8323 3.34714 13.0539 4.35179 13.6279C5.14821 14.0829 6.33286 14.5 8 14.5C9.66714 14.5 10.8521 14.0829 11.6486 13.6279C12.6532 13.0539 13.0621 11.8323 12.7907 10.6813C12.1736 8.06368 10.2621 6.02632 8 6.02632ZM0.5 5.45305C0.5 6.47063 1.13929 7.5 1.92857 7.5C2.71786 7.5 3.35714 6.47063 3.35714 5.45305C3.35714 4.43547 2.71786 3.81579 1.92857 3.81579C1.13929 3.81579 0.5 4.43584 0.5 5.45305ZM15.5 5.45305C15.5 6.47063 14.8607 7.5 14.0714 7.5C13.2821 7.5 12.6429 6.47063 12.6429 5.45305C12.6429 4.43547 13.2821 3.81579 14.0714 3.81579C14.8607 3.81579 15.5 4.43584 15.5 5.45305ZM4.25 2.13726C4.25 3.15484 4.88929 4.18421 5.67857 4.18421C6.46786 4.18421 7.10714 3.15484 7.10714 2.13726C7.10714 1.11968 6.46786 0.5 5.67857 0.5C4.88929 0.5 4.25 1.12005 4.25 2.13726ZM11.75 2.13726C11.75 3.15484 11.1107 4.18421 10.3214 4.18421C9.53214 4.18421 8.89286 3.15484 8.89286 2.13726C8.89286 1.11968 9.53214 0.5 10.3214 0.5C11.1107 0.5 11.75 1.12005 11.75 2.13726Z"
                                                    stroke="#3B3731" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </span>
                                        <span class="availability-drawer-space-pet-type-value"
                                            x-text="selectedBooking && selectedBooking.spacePetType ? selectedBooking.spacePetType : 'Pet type'"></span>
                                    </div>
                                </div>
                                <div class="availability-drawer-space-addons"
                                    x-show="(selectedBooking?.spaceAddOns || []).length > 0">
                                    <div class="availability-drawer-space-addons-heading">Add-on Service</div>
                                    <div class="availability-drawer-space-addons-list">
                                        <template
                                            x-for="(addonLabel, addonIndex) in (selectedBooking?.spaceAddOns || [])"
                                            :key="'addon-' + addonIndex">
                                            <div class="availability-drawer-space-addon" x-text="addonLabel"></div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="availability-drawer-pet" x-show="!isSpaceAccount">
                                <div class="availability-drawer-pet-left">
                                    <img :src="selectedBooking?.petPhoto" alt="Pet image" />
                                    <div class="availability-drawer-pet-copy">
                                        <strong x-text="selectedBooking?.petName"></strong>
                                        <small>
                                            <span x-text="selectedBooking?.petType"></span>
                                            <span x-show="selectedBooking?.petBreed"><span class="black-dot"
                                                    style="margin: 0 5px; background:#9D9B98;"></span><span
                                                    x-text="selectedBooking?.petBreed"></span></span>
                                        </small>
                                    </div>
                                </div>
                                <div class="availability-drawer-pet-right">
                                    <p><span>
                                            <svg fill="#9D9B98" width="15" height="15"
                                                viewBox="0 0 61.13 61.13" xmlns="http://www.w3.org/2000/svg">
                                                <path
                                                    d="M27.482,34.031v12.317h-6.92c-1.703,0-3.084,1.381-3.084,3.084s1.381,3.084,3.084,3.084h6.92v5.531c0,1.703,1.381,3.084,3.084,3.084s3.084-1.381,3.084-3.084v-5.531h6.92c1.703,0,3.084-1.381,3.084-3.084s-1.381-3.084-3.084-3.084h-6.92V34.031c7.993-1.458,14.072-8.467,14.072-16.874C47.723,7.697,40.026,0,30.566,0c-9.46,0-17.157,7.697-17.157,17.157C13.409,25.564,19.489,32.573,27.482,34.031z M30.566,6.169c6.059,0,10.988,4.929,10.988,10.988s-4.929,10.988-10.988,10.988s-10.988-4.929-10.988-10.988S24.507,6.169,30.566,6.169z" />
                                            </svg>
                                        </span>
                                        <span x-text="selectedBooking?.petSex"></span>
                                    </p>
                                    <p><span><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M4.7373 3.14703C4.7373 3.84907 5.01619 4.52235 5.51261 5.01876C6.00903 5.51518 6.68232 5.79406 7.38436 5.79406C8.08641 5.79406 8.7597 5.51518 9.25612 5.01876C9.75254 4.52235 10.0314 3.84907 10.0314 3.14703C10.0314 2.44499 9.75254 1.77171 9.25612 1.2753C8.7597 0.778883 8.08641 0.5 7.38436 0.5C6.68232 0.5 6.00903 0.778883 5.51261 1.2753C5.01619 1.77171 4.7373 2.44499 4.7373 3.14703Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M2.8269 5.79492H11.9416C12.1482 5.79489 12.3483 5.86739 12.507 5.99977C12.6657 6.13215 12.7728 6.31602 12.8098 6.51933L14.2542 14.4604C14.2774 14.5876 14.2723 14.7183 14.2394 14.8433C14.2064 14.9683 14.1464 15.0845 14.0636 15.1837C13.9807 15.283 13.8771 15.3628 13.76 15.4176C13.643 15.4723 13.5153 15.5007 13.386 15.5007H1.38249C1.25323 15.5007 1.12554 15.4723 1.00846 15.4176C0.891377 15.3628 0.78776 15.283 0.704935 15.1837C0.62211 15.0845 0.5621 14.9683 0.52915 14.8433C0.496199 14.7183 0.491113 14.5876 0.514251 14.4604L1.95866 6.51933C1.99565 6.31602 2.10282 6.13215 2.26149 5.99977C2.42016 5.86739 2.62026 5.79489 2.8269 5.79492Z"
                                                    stroke="#9D9B98" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg></span> <span x-text="selectedBooking?.petWeight"></span></p>
                                    <p><span><svg xmlns="http://www.w3.org/2000/svg" width="15" height="16"
                                                viewBox="0 0 15 16" fill="none">
                                                <path
                                                    d="M13.5905 8.11123L13.9601 6.73016C14.3918 5.1182 14.6084 4.31257 14.4462 3.61489C14.3176 3.0641 14.0285 2.56382 13.6155 2.17734C13.093 1.68768 12.2866 1.4718 10.6747 1.04003C9.0627 0.607553 8.25636 0.391671 7.55939 0.55394C7.0086 0.682549 6.50833 0.971618 6.12185 1.38458C5.70224 1.83207 5.4835 2.48758 5.15824 3.67851L4.98382 4.32544L4.61425 5.70651C4.18177 7.31847 3.96589 8.1241 4.12816 8.82178C4.25677 9.37257 4.54584 9.87285 4.9588 10.2593C5.48135 10.749 6.28769 10.9649 7.89966 11.3974C9.35221 11.7862 10.1507 12 10.8048 11.9192C10.8763 11.9101 10.9463 11.8977 11.0149 11.882C11.5655 11.7538 12.0658 11.4652 12.4525 11.0528C12.9421 10.5295 13.158 9.7232 13.5905 8.11123Z"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M10.8047 11.9191C10.6553 12.3768 10.3927 12.7894 10.0413 13.1186C9.51875 13.6082 8.71241 13.8241 7.10045 14.2559C5.48848 14.6876 4.68214 14.9042 3.98517 14.7413C3.43447 14.6128 2.9342 14.324 2.54763 13.9113C2.05796 13.3888 1.84137 12.5824 1.4096 10.9705L1.04003 9.5894C0.607553 7.97744 0.391671 7.1711 0.55394 6.47413C0.682549 5.92334 0.971618 5.42306 1.38458 5.03658C1.90713 4.54692 2.71347 4.33104 4.32544 3.89856C4.62948 3.81659 4.90708 3.74296 5.15823 3.67767"
                                                    stroke="#9D9B98" />
                                                <path
                                                    d="M7.48902 6.21875L10.9417 7.14375M6.93359 8.29036L9.0052 8.84507"
                                                    stroke="#9D9B98" stroke-linecap="round" />
                                            </svg></span> <span x-text="selectedBooking?.petNotes"></span></p>
                                </div>
                            </div>
                        </article>
                    </div>
                </aside>
            </div>
        </div>
    </template>
</div>

<style>
    .availability-layout {
        display: flex;
        flex-direction: column;
        --availability-aside-width: 250px;
    }

    .availability-content {
        display: grid;
        grid-template-columns: minmax(0, 1fr) var(--availability-aside-width);
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
        gap: 20px;
    }

    .availability-calendar-title {
        justify-self: end;
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
        display: flex;
        align-items: center;
        width: var(--availability-aside-width);
        max-width: 100%;
    }

    .availability-search input {
        width: 100%;
        min-width: 0;
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

    .availability-booking-card.is-space {
        background: #FFF7F5;
        display: grid;
        grid-template-columns: auto 1fr;
        grid-template-rows: auto auto;
        column-gap: 12px;
        row-gap: 14px;
        align-items: center;
    }

    .availability-booking-card.is-space>.img-circle {
        grid-column: 1;
        grid-row: 1;
    }

    .availability-booking-card.is-space>div:last-child {
        display: contents;
    }

    .availability-booking-card.is-space>div:last-child>.booking-chip {
        grid-column: 2;
        grid-row: 1;
        margin-bottom: 0;
        align-self: center;
        justify-self: start;
    }

    .availability-booking-card.is-space>div:last-child>ul {
        grid-column: 1 / -1;
        grid-row: 2;
        justify-self: start;
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

    .img-circle.is-space {
        background: #FFA899;
        padding: 2px;
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

    .img-circle.is-space>div {
        width: 100%;
        height: 100%;
        background: #FFF;
        border-radius: 50%;
        padding: 2px;
    }

    .img-circle>div>img {
        width: 30px;
        height: 30px;
        aspect-ratio: 1/1;
        border-radius: 86px;

    }

    .img-circle.is-space>div>img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
        display: block;
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

    .booking-chip.is-space {
        background: #FFA899;
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
        /* gap: 15px; */
    }

    .availability-booking-card>div:last-child>ul>li>span {
        color: #9D9B98;
    }

    .availability-booking-card>div:last-child>ul>li>svg {
        margin-right: 10px;
    }

    .availability-booking-card .pet-size-inline {
        display: inline-flex;
        align-items: center;
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

    .availability-bookings-drawer-layer {
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 1100;
        pointer-events: none;
    }

    .availability-booking-detail-layer {
        z-index: 1200;
    }

    .availability-bookings-drawer-backdrop {
        position: absolute;
        inset: 0;
        background: transparent;
        border: 0;
        width: 100%;
        pointer-events: auto;
    }

    .availability-bookings-drawer {
        position: absolute;
        top: 0;
        right: 0;
        width: min(35rem, 100%);
        height: 100%;
        border-radius: 10px 0 0 10px;
        background: #FFF;
        box-shadow: -6px 0 16.8px -1px rgba(0, 0, 0, 0.10);
        pointer-events: auto;
        display: flex;
        flex-direction: column;
    }

    .availability-drawer-head {
        padding: 24px 24px 14px;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .availability-drawer-head h3 {
        margin: 0;
        color: #3B3731;
        font-family: Lato;
        font-size: 36px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .availability-drawer-close {
        border: 0;
        background: transparent;
        cursor: pointer;
        line-height: 0;
        padding: 0;
    }

    .availability-drawer-body {
        padding: 0 24px 24px;
        overflow-y: auto;
        display: grid;
        gap: 18px;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .availability-drawer-body::-webkit-scrollbar {
        width: 0;
        height: 0;
        display: none;
    }

    .availability-drawer-booking-card {
        border: 1px solid #dcd4c8;
        border-radius: 10px;
        background: #fff;
        padding: 18px;
    }

    .availability-drawer-booking-card.is-confirmed {
        border-color: #AFCD6F;
    }

    .availability-drawer-booking-card.is-pending {
        border-color: #FFBA55;
    }

    .availability-drawer-booking-card.is-cancelled {
        border-color: #FF6E6E;
    }

    .availability-drawer-booking-card.is-cancelled .availability-drawer-client-name-row,
    .availability-drawer-booking-card.is-cancelled .availability-drawer-meta,
    .availability-drawer-booking-card.is-cancelled .availability-drawer-pet,
    .availability-drawer-booking-card.is-cancelled .availability-drawer-space-main {
        text-decoration-line: line-through;
        text-decoration-thickness: 1px;
    }

    .availability-drawer-booking-top {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 10px;
        padding-bottom: 14px;
        border-bottom: 1px solid #ebe7e0;
    }

    .availability-drawer-client {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .availability-drawer-client-name-row {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .availability-drawer-client img {
        width: 54px;
        height: 54px;
        border-radius: 999px;
        object-fit: cover;
    }

    .availability-drawer-client strong {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        line-height: 1.2;
    }

    .availability-drawer-client small {
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
    }

    .availability-drawer-status {
        border-radius: 100px;
        padding: 6px 14px;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .availability-drawer-status.is-confirmed {
        color: #AFCD6F;
        background: rgba(201, 221, 160, 0.2);
    }

    .availability-drawer-status.is-pending {
        color: #FFBA55;
        background: rgba(255, 201, 122, 0.2);
    }

    .availability-drawer-status.is-cancelled {
        color: #FF6E6E;
        background: rgba(255, 110, 110, 0.2);
    }

    .availability-drawer-status.is-default {
        color: #6f6a64;
        background: #f0ece6;
    }

    .availability-drawer-meta {
        padding: 14px 0;
        display: grid;
        gap: 8px;
    }

    .availability-drawer-meta.is-space {
        padding-bottom: 0;
        margin-bottom: 5px;
    }

    .availability-drawer-meta-row {
        display: flex;
        align-items: center;
        gap: 16px;
        flex-wrap: wrap;
    }

    .availability-drawer-meta-item {
        display: flex;
        align-items: center;
        gap: 15px;
        color: #3B3731;
        font-family: Lato;
        font-size: 15px;
        font-weight: 500;
    }

    .availability-drawer-space {
        margin-top: 14px;
        display: flex;
        align-items: flex-start;
        gap: 12px;
    }

    .availability-drawer-space-main {
        flex: 1;
        min-width: 0;
    }

    .availability-drawer-space-addons {
        flex-shrink: 0;
        width: 135px;
        display: flex;
        flex-direction: column;
        gap: 15px;
        align-self: stretch;
        border-radius: 5px;
        background: #FBFBFB;
        padding: 14px;
    }

    .availability-drawer-space-addons-heading {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .availability-drawer-space-addons-list {
        display: flex;
        flex-direction: column;
    }

    .availability-drawer-space-title {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .availability-drawer-space-badge {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
    }

    .availability-drawer-space-title strong {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 18px;
        font-weight: 600;
        line-height: 1.2;
    }

    .availability-drawer-space-duration {
        margin: 10px 0 0;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 10px;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.35;
    }

    .availability-drawer-space-duration-icon {
        flex-shrink: 0;
    }

    .availability-drawer-space-visit {
        display: flex;
        align-items: flex-start;
        gap: 10px;
        margin-top: 12px;
    }

    .availability-drawer-space-visit-icon {
        display: flex;
        flex-shrink: 0;
        padding-top: 2px;
    }

    .availability-drawer-space-visit-copy {
        margin: 0;
        flex: 1;
        min-width: 0;
    }

    .availability-drawer-space-service-line {
        margin: 0;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.35;
    }

    .availability-drawer-space-pet-type-row {
        margin: 12px 0 0;
        padding: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .availability-drawer-space-pet-type-value {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 15px;
        font-weight: 500;
        line-height: 1.35;
    }

    .availability-drawer-space-pet-type-icon {
        display: flex;
        flex-shrink: 0;
        align-items: center;
        justify-content: center;
    }

    .availability-drawer-space-pet-type-icon svg {
        display: block;
    }

    .availability-drawer-space-addon {
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 23px;
    }

    .availability-drawer-pet {
        margin-top: 14px;
        border: 1px solid #ede7dc;
        border-radius: 5px;
        padding: 14px;
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .availability-drawer-pet-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .availability-drawer-pet img {
        width: 54px;
        height: 54px;
        border-radius: 999px;
        object-fit: cover;
    }

    .availability-drawer-pet-copy strong {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #3B3731;
        font-family: Lato;
        font-size: 18px;
        font-weight: 600;
        line-height: 1.2;
    }

    .availability-drawer-pet-copy small {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .availability-drawer-pet-copy p {
        margin: 6px 0 0;
        color: #5d5852;
        font-family: Lato;
        font-size: 14px;
        font-weight: 400;
    }

    .availability-drawer-pet-right {
        min-width: 180px;
        display: flex;
        flex-direction: column;
        align-items: start;
        gap: 8px;
    }

    .availability-drawer-pet-right p {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }


    .availability-drawer-empty {
        border: 1px dashed #ddd5c9;
        border-radius: 12px;
        background: #fff;
        padding: 20px;
    }

    .availability-drawer-empty p {
        margin: 0;
        color: #6f685f;
        font-family: Lato;
        font-size: 15px;
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

    .drawer-enter,
    .drawer-leave {
        transition: transform 250ms ease, opacity 250ms ease;
    }

    .drawer-enter-start,
    .drawer-leave-end {
        transform: translateX(100%);
        opacity: 0;
    }

    .drawer-enter-end,
    .drawer-leave-start {
        transform: translateX(0);
        opacity: 1;
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
                    const y = dateObj.getFullYear();
                    const m = dateObj.getMonth();
                    const d = dateObj.getDate();
                    const dateKey =
                        `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                    this.$dispatch('availability-navigate-day', {
                        year: y,
                        monthIndex: m,
                        day: d,
                        dateKey,
                    });
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
