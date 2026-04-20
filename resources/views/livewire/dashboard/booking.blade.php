<?php

use App\Models\Booking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Volt\Component;

new class extends Component {
    public $bookings;
    public $statusCounts = [];
    public string $activeStatus = 'all';
    public string $pendingSort = 'latest_submitted';
    public int $visibleRows = 10;
    public ?int $declineBookingId = null;
    public ?int $rescheduleBookingId = null;
    public ?string $rescheduleCalendarMonth = null;
    public ?string $rescheduleSelectedDate = null;
    public ?string $rescheduleSelectedTime = null;
    public int $rescheduleDurationMinutes = 60;
    private array $allowedStatuses = ['all', 'pending', 'confirmed', 'completed', 'cancelled'];
    private array $reschedulableStatuses = ['pending', 'confirmed'];

    private function scopedBookingQuery(int $bookingId)
    {
        return Booking::query()->where('goormer_spacer_id', Auth::id())->where('id', $bookingId);
    }

    public function refreshBookingsAndCounts(): void
    {
        // Avoid heavy polling refresh while a modal is open, so calendar
        // interactions (day/time/month selection) stay responsive.
        if ($this->declineBookingId !== null || $this->rescheduleBookingId !== null) {
            return;
        }

        $profileId = Auth::id();

        $this->bookings = Booking::with(['petOwner:id,name', 'pets:id,name,pet_type,breed,photo'])
            ->where('goormer_spacer_id', $profileId)
            ->latest('date')
            ->latest('id')
            ->get();

        $this->statusCounts = [
            'pending' => $this->bookings->where('booking_status', 'pending')->count(),
            'confirmed' => $this->bookings->where('booking_status', 'confirmed')->count(),
            'completed' => $this->bookings->where('booking_status', 'completed')->count(),
            'cancelled' => $this->bookings->where('booking_status', 'cancelled')->count(),
        ];

        // Keep sidebar status counters in sync without a full page refresh.
        $this->dispatch('booking-counts-updated', counts: $this->statusCounts);
    }

    public function mount(): void
    {
        $this->refreshBookingsAndCounts();

        $requestedStatus = strtolower((string) request()->query('booking_status', 'all'));
        $this->activeStatus = in_array($requestedStatus, $this->allowedStatuses, true) ? $requestedStatus : 'all';

        // Sync dashboard header with the initial filter (e.g. ?booking_status=pending).
        $this->dispatch('booking-status-changed', status: $this->activeStatus === 'all' ? '' : $this->activeStatus);
    }

    public function setActiveStatus(string $status): void
    {
        $status = strtolower($status);
        if (!in_array($status, $this->allowedStatuses, true)) {
            return;
        }

        $this->activeStatus = $status;
        $this->visibleRows = 10;
        $this->dispatch('booking-status-changed', status: $status);
        $this->dispatch('bookings-tabs-loading-end');
    }

    #[On('booking-status-selected')]
    public function onBookingStatusSelected(string $status): void
    {
        $this->setActiveStatus($status);
    }

    #[On('booking-filter-reset')]
    public function onBookingFilterReset(): void
    {
        $this->activeStatus = 'all';
        $this->visibleRows = 10;
        $this->dispatch('booking-status-changed', status: '');
        $this->dispatch('bookings-tabs-loading-end');
    }

    public function acceptBooking(int $bookingId): void
    {
        $booking = $this->scopedBookingQuery($bookingId)->firstOrFail();

        if ($booking->booking_status !== 'pending') {
            return;
        }

        $booking->update(['booking_status' => 'confirmed']);
        $this->refreshBookingsAndCounts();
    }

    public function cancelBooking(int $bookingId): void
    {
        $booking = $this->scopedBookingQuery($bookingId)->firstOrFail();

        if (!in_array($booking->booking_status, $this->reschedulableStatuses, true)) {
            return;
        }

        $booking->update(['booking_status' => 'cancelled']);
        $this->refreshBookingsAndCounts();
    }

    public function openDeclineModal(int $bookingId): void
    {
        $bookingExists = $this->scopedBookingQuery($bookingId)->whereIn('booking_status', $this->reschedulableStatuses)->exists();

        if (!$bookingExists) {
            $this->dispatch('bookings-tabs-loading-end');
            return;
        }

        $this->declineBookingId = $bookingId;
        $this->dispatch('bookings-tabs-loading-end');
        $this->dispatch('decline-modal-opened');
    }

    public function closeDeclineModal(): void
    {
        $this->declineBookingId = null;
        $this->dispatch('decline-modal-closed');
    }

    public function confirmDeclineBooking(): void
    {
        if ($this->declineBookingId === null) {
            return;
        }

        $this->cancelBooking($this->declineBookingId);
        $this->declineBookingId = null;
        $this->dispatch('decline-modal-closed');
    }

    public function openRescheduleModal(int $bookingId): void
    {
        $booking = $this->scopedBookingQuery($bookingId)->whereIn('booking_status', $this->reschedulableStatuses)->first();

        if (!$booking) {
            $this->dispatch('bookings-tabs-loading-end');
            return;
        }

        $this->rescheduleBookingId = $bookingId;
        $currentDate = $booking->date ? date('Y-m-d', strtotime((string) $booking->date)) : date('Y-m-d');
        $this->rescheduleSelectedDate = $currentDate;
        $this->rescheduleCalendarMonth = date('Y-m-01', strtotime($currentDate));
        $this->rescheduleDurationMinutes = 60;
        $this->rescheduleSelectedTime = null;

        $timeRaw = trim((string) $booking->time);
        if (preg_match('/(\d{1,2}:\d{2})/', $timeRaw, $mStart)) {
            $this->rescheduleSelectedTime = date('h:i A', strtotime($mStart[1]));
        }

        if (str_contains($timeRaw, '-')) {
            $parts = preg_split('/\s*-\s*/', $timeRaw, 2);
            $start = $parts[0] ?? '';
            $end = $parts[1] ?? '';
            preg_match('/(\d{1,2}:\d{2})/', $start, $mStart);
            preg_match('/(\d{1,2}:\d{2})/', $end, $mEnd);
            if (!empty($mStart[1]) && !empty($mEnd[1])) {
                try {
                    $startTs = strtotime($mStart[1]);
                    $endTs = strtotime($mEnd[1]);
                    if ($endTs < $startTs) {
                        $endTs = strtotime('+1 day', $endTs);
                    }
                    $this->rescheduleDurationMinutes = (int) max(1, ($endTs - $startTs) / 60);
                } catch (Throwable $e) {
                    $this->rescheduleDurationMinutes = 60;
                }
            }
        }

        $this->dispatch('bookings-tabs-loading-end');
        $this->dispatch('reschedule-modal-opened');
    }

    public function closeRescheduleModal(): void
    {
        $this->rescheduleBookingId = null;
        $this->rescheduleCalendarMonth = null;
        $this->rescheduleSelectedDate = null;
        $this->rescheduleSelectedTime = null;
        $this->rescheduleDurationMinutes = 60;
        $this->dispatch('reschedule-modal-closed');
    }

    public function confirmRescheduleBooking(): void
    {
        if (!$this->rescheduleBookingId || !$this->rescheduleSelectedDate || !$this->rescheduleSelectedTime) {
            return;
        }

        $booking = $this->scopedBookingQuery($this->rescheduleBookingId)->whereIn('booking_status', $this->reschedulableStatuses)->first();

        if (!$booking) {
            $this->closeRescheduleModal();
            return;
        }

        /** @var Booking $booking */
        $start = DateTime::createFromFormat('h:i A', $this->rescheduleSelectedTime);
        if (!$start) {
            return;
        }

        $duration = max(1, $this->rescheduleDurationMinutes);
        $end = (clone $start)->modify('+' . $duration . ' minutes');
        $timeRange = $start->format('H:i') . ' - ' . $end->format('H:i');

        $booking->update([
            'date' => $this->rescheduleSelectedDate,
            'time' => $timeRange,
        ]);

        $this->refreshBookingsAndCounts();
        $this->closeRescheduleModal();
    }

    public function confirmRescheduleBookingFromClient(?string $selectedDate, ?string $selectedTime): void
    {
        if (is_string($selectedDate) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $selectedDate)) {
            $this->rescheduleSelectedDate = $selectedDate;
        }

        if (is_string($selectedTime) && preg_match('/^\d{2}:\d{2}\s(?:AM|PM)$/', $selectedTime)) {
            $this->rescheduleSelectedTime = $selectedTime;
        }

        $this->confirmRescheduleBooking();
    }

    public function setPendingSort(string $sort): void
    {
        $allowedSorts = ['latest_submitted', 'oldest_submitted', 'amount_high', 'amount_low'];
        if (!in_array($sort, $allowedSorts, true)) {
            $this->dispatch('bookings-tabs-loading-end');
            return;
        }

        $this->pendingSort = $sort;
        $this->visibleRows = 10;
        $this->dispatch('bookings-tabs-loading-end');
    }

    public function loadMoreBookings(): void
    {
        $this->visibleRows += 10;
        $this->dispatch('bookings-tabs-loading-end');
    }
}; ?>

<section class="bookings-board" wire:poll.5s="refreshBookingsAndCounts">
    <div class="bookings-board-header">
        @if ($activeStatus === 'pending')
            <div class="booking-list-header">
                <div class="booking-list-title">
                    Pending Bookings ({{ $statusCounts['pending'] }})
                </div>

                <div class="booking-list-sort">
                    <div class="sort-dropdown" x-data="{ open: false }" @keydown.escape.window="open = false">
                        <button type="button" class="sort-trigger" @click="open = !open"
                            aria-label="Sort pending bookings" :aria-expanded="open.toString()">
                            <span>Sort</span>
                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="7" viewBox="0 0 13 7"
                                fill="none">
                                <path d="M11.9103 0.5L6.15684 6.25344L0.499989 0.596581" stroke="#A8A8A8"
                                    stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                        <div class="sort-menu" x-cloak x-show="open" @click.outside="open = false"
                            x-transition.opacity.duration.100ms>
                            <button type="button" class="sort-options"
                                :class="{ 'is-active': @js($pendingSort) === 'latest_submitted' }"
                                wire:click="setPendingSort('latest_submitted')"
                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                                <span>Recommended (default)</span>
                                <span class="sort-indicator"></span>
                            </button>
                            <button type="button" class="sort-options"
                                :class="{ 'is-active': @js($pendingSort) === 'oldest_submitted' }"
                                wire:click="setPendingSort('oldest_submitted')"
                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                                <span>New to Old</span>
                                <span class="sort-indicator"></span>
                            </button>
                            <button type="button" class="sort-options"
                                :class="{ 'is-active': @js($pendingSort) === 'amount_low' }"
                                wire:click="setPendingSort('amount_low')"
                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                                <span>Old to New</span>
                                <span class="sort-indicator"></span>
                            </button>
                            <button type="button" class="sort-options"
                                :class="{ 'is-active': @js($pendingSort) === 'amount_high' }"
                                wire:click="setPendingSort('amount_high')"
                                @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); open = false">
                                <span>Price Descending</span>
                                <span class="sort-indicator"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="booking-pill-row">
                @if ($activeStatus === 'all')
                    <button type="button" wire:click="setActiveStatus('pending')" class="booking-pill pending">Pending
                        Bookings ({{ $statusCounts['pending'] }})</button>
                    <button type="button" wire:click="setActiveStatus('confirmed')"
                        class="booking-pill confirmed">Confirmed
                        Bookings ({{ $statusCounts['confirmed'] }})</button>
                    <button type="button" wire:click="setActiveStatus('completed')"
                        class="booking-pill completed">Completed
                        Bookings ({{ $statusCounts['completed'] }})</button>
                    <button type="button" wire:click="setActiveStatus('cancelled')"
                        class="booking-pill cancelled">Cancelled
                        Bookings ({{ $statusCounts['cancelled'] }})</button>
                @else
                    @if ($activeStatus === 'pending')
                        <button type="button" wire:click="setActiveStatus('pending')"
                            class="booking-pill pending is-active">Pending
                            Bookings ({{ $statusCounts['pending'] }})</button>
                    @elseif ($activeStatus === 'confirmed')
                        <button type="button" wire:click="setActiveStatus('confirmed')"
                            class="booking-pill confirmed is-active">Confirmed
                            Bookings ({{ $statusCounts['confirmed'] }})</button>
                    @elseif ($activeStatus === 'completed')
                        <button type="button" wire:click="setActiveStatus('completed')"
                            class="booking-pill completed is-active">Completed
                            Bookings ({{ $statusCounts['completed'] }})</button>
                    @elseif ($activeStatus === 'cancelled')
                        <button type="button" wire:click="setActiveStatus('cancelled')"
                            class="booking-pill cancelled is-active">Cancelled
                            Bookings ({{ $statusCounts['cancelled'] }})</button>
                    @endif
                @endif
            </div>

        @endif
    </div>

    @php
        $extractPetMeta = function ($booking) {
            $petNames = $booking->pets->pluck('name')->filter()->values()->all();
            $petTypes = $booking->pets->pluck('pet_type')->filter()->unique()->values()->all();

            return [
                'name' => $petNames[0] ?? 'N/A',
                'type' => $petTypes[0] ?? null,
                'more' => count($petNames) > 1 ? '+' . (count($petNames) - 1) : '',
            ];
        };

        $formatPendingTimeRange = function (string $raw): string {
            if (!str_contains($raw, '-')) {
                return $raw;
            }

            $timeParts = preg_split('/\s*-\s*/', $raw, 2);
            $startPart = $timeParts[0] ?? '';
            $endPart = $timeParts[1] ?? '';
            preg_match('/(\d{1,2}:\d{2})/', $startPart, $mStart);
            preg_match('/(\d{1,2}:\d{2})/', $endPart, $mEnd);
            if (empty($mStart[1]) || empty($mEnd[1])) {
                return $raw;
            }

            try {
                $startDt = new DateTime($mStart[1]);
                $endDt = new DateTime($mEnd[1]);
                if ($endDt < $startDt) {
                    $endDt->modify('+1 day');
                }

                $startHHMM = $startDt->format('H:i');
                $endHHMM = $endDt->format('H:i');
                $startMeridiem = strtolower($startDt->format('a'));
                $endMeridiem = strtolower($endDt->format('a'));
                $diffMinutes = max(0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60);
                $hours = (int) floor($diffMinutes / 60);
                $minutes = (int) ($diffMinutes % 60);
                $durationLabel = $minutes === 0 ? $hours . 'hr' : $hours . 'hr ' . $minutes . 'm';

                if ($startMeridiem === $endMeridiem) {
                    return $startHHMM . ' - ' . $endHHMM . ' ' . $startMeridiem . ' (' . $durationLabel . ')';
                }

                return $startDt->format('H:i a') . ' - ' . $endDt->format('H:i a') . ' (' . $durationLabel . ')';
            } catch (Throwable $e) {
                return $raw;
            }
        };

        $formatConfirmedTimeRange = function (string $raw): string {
            if (!str_contains($raw, '-')) {
                return $raw;
            }

            $parts = preg_split('/\s*-\s*/', $raw, 2);
            $startPart = $parts[0] ?? '';
            $endPart = $parts[1] ?? '';
            preg_match('/(\d{1,2}:\d{2})/', $startPart, $mStart);
            preg_match('/(\d{1,2}:\d{2})/', $endPart, $mEnd);
            if (empty($mStart[1]) || empty($mEnd[1])) {
                return $raw;
            }

            try {
                $startDt = new DateTime($mStart[1]);
                $endDt = new DateTime($mEnd[1]);
                if ($endDt < $startDt) {
                    $endDt->modify('+1 day');
                }
                $durationMinutes = (int) max(0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60);
                $durationLabel =
                    '(' .
                    (int) floor($durationMinutes / 60) .
                    'hr' .
                    ($durationMinutes % 60 ? ' ' . $durationMinutes % 60 . 'm' : '') .
                    ')';
                return $startDt->format('H:i') .
                    ' - ' .
                    $endDt->format('H:i') .
                    ' ' .
                    strtolower($endDt->format('a')) .
                    ' ' .
                    $durationLabel;
            } catch (Throwable $e) {
                return $raw;
            }
        };

        $formatLocationLabel = function (?string $visitType): string {
            $label = str_replace('_', ' ', strtolower((string) $visitType));
            if ($label === 'home' || $label === 'home visit') {
                return 'Home Visit';
            }
            if ($label === 'salon' || $label === 'salon visit') {
                return 'Salon Visit';
            }
            return ucfirst($label ?: 'N/A');
        };
    @endphp

    @if ($declineBookingId === null && $rescheduleBookingId === null)
        <div class="bookings-table-wrap">
            @if ($activeStatus === 'pending')
                <table class="bookings-table booking-list-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Submitted at</th>
                            <th>Owner</th>
                            <th>Pet</th>
                            <th>Service Type</th>
                            <th>Booking Details</th>
                            <th>Payment</th>
                            <th class="action-col">Action</th>
                        </tr>
                    </thead>
                    <tbody wire:key="bookings-table-pending" class="bookings-table-body">
                        @php
                            $pendingBookings = $bookings->where('booking_status', 'pending')->values();
                            if ($pendingSort === 'oldest_submitted') {
                                $pendingBookings = $pendingBookings->sortBy('created_at')->values();
                            } elseif ($pendingSort === 'amount_high') {
                                $pendingBookings = $pendingBookings->sortByDesc(fn($b) => (float) $b->amount)->values();
                            } elseif ($pendingSort === 'amount_low') {
                                $pendingBookings = $pendingBookings->sortBy(fn($b) => (float) $b->amount)->values();
                            } else {
                                $pendingBookings = $pendingBookings->sortByDesc('created_at')->values();
                            }
                        @endphp
                        @php
                            $visiblePendingBookings = $pendingBookings->take($visibleRows);
                        @endphp
                        @forelse ($visiblePendingBookings as $booking)
                            @php
                                $petNames = $booking->pets->pluck('name')->filter()->values()->all();
                                $petTypes = $booking->pets->pluck('pet_type')->filter()->unique()->values()->all();

                                $petName = $petNames[0] ?? 'N/A';
                                $petMore = count($petNames) > 1 ? '+' . (count($petNames) - 1) : '';
                                $petType = $petTypes[0] ?? null;

                                // "Submitted at" should reflect when the booking row was created.
                                $submittedDate = optional($booking->created_at)->format('d/m/y');
                                $submittedTime = optional($booking->created_at)->format('H:i');

                                // Booking Details keep using your booking date/time fields.
                                $bookingDetailsDate = optional($booking->date)->format('d/m/y');
                                $bookingDetailsTimeRaw = (string) $booking->time;

                                // Example output: "08:00 - 09:00 am (1hr)"
                                // `booking->time` is usually stored like "08:00 - 09:00".
                                $bookingDetailsTime = $bookingDetailsTimeRaw;
                                if (str_contains($bookingDetailsTimeRaw, '-')) {
                                    $timeParts = preg_split('/\s*-\s*/', $bookingDetailsTimeRaw, 2);
                                    $startPart = $timeParts[0] ?? '';
                                    $endPart = $timeParts[1] ?? '';

                                    preg_match('/(\d{1,2}:\d{2})/', $startPart, $mStart);
                                    preg_match('/(\d{1,2}:\d{2})/', $endPart, $mEnd);

                                    if (!empty($mStart[1]) && !empty($mEnd[1])) {
                                        $startTimeStr = $mStart[1];
                                        $endTimeStr = $mEnd[1];

                                        try {
                                            $startDt = new DateTime($startTimeStr);
                                            $endDt = new DateTime($endTimeStr);

                                            // If end is earlier than start, assume it rolls over (rare for groom slots).
                                            if ($endDt < $startDt) {
                                                $endDt->modify('+1 day');
                                            }

                                            $startHHMM = $startDt->format('H:i');
                                            $endHHMM = $endDt->format('H:i');

                                            $startMeridiem = strtolower($startDt->format('a'));
                                            $endMeridiem = strtolower($endDt->format('a'));

                                            $diffMinutes = max(
                                                0,
                                                ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60,
                                            );
                                            $hours = (int) floor($diffMinutes / 60);
                                            $minutes = (int) ($diffMinutes % 60);

                                            if ($minutes === 0) {
                                                $durationLabel = $hours . 'hr';
                                            } else {
                                                $durationLabel = $hours . 'hr ' . $minutes . 'm';
                                            }

                                            if ($startMeridiem === $endMeridiem) {
                                                $bookingDetailsTime =
                                                    $startHHMM .
                                                    ' - ' .
                                                    $endHHMM .
                                                    ' ' .
                                                    $startMeridiem .
                                                    ' (' .
                                                    $durationLabel .
                                                    ')';
                                            } else {
                                                $bookingDetailsTime =
                                                    $startDt->format('H:i a') .
                                                    ' - ' .
                                                    $endDt->format('H:i a') .
                                                    ' (' .
                                                    $durationLabel .
                                                    ')';
                                            }
                                        } catch (Throwable $e) {
                                            // Keep raw value on parse failure.
                                            $bookingDetailsTime = $bookingDetailsTimeRaw;
                                        }
                                    }
                                }
                            @endphp

                            <tr wire:key="booking-pending-row-{{ $booking->id }}">
                                <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="submitted-at">
                                        <div class="submitted-time">{{ $submittedTime }}</div>
                                        <div class="submitted-date">{{ $submittedDate }}</div>
                                    </div>
                                </td>
                                <td>{{ $booking->petOwner->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="filtered-pet-cell">
                                        <span class="booking-pet-name">{{ $petName }}</span>
                                        <span>
                                            @if ($petType)
                                                <span class="booking-pet-type">{{ $petType }}</span>
                                            @endif
                                            @if ($petMore)
                                                <span class="booking-pet-more">{{ $petMore }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $booking->service }}</td>
                                <td>
                                    <div class="booking-details">
                                        <div class="details-date">{{ $bookingDetailsDate }}</div>
                                        <div class="details-time">{{ $bookingDetailsTime }}</div>
                                    </div>
                                </td>
                                <td>£{{ number_format((float) $booking->amount, 2) }}</td>
                                <td class="action-col">
                                    <div class="booking-action-cell" x-data="{
                                        rowId: {{ $booking->id }},
                                        openMore: false,
                                        menuLeft: 8,
                                        menuTop: 8,
                                        repositionMore() {
                                            const rect = $refs.moreBtn.getBoundingClientRect();
                                            const menuWidth = 210;
                                            this.menuLeft = Math.min(Math.max(8, rect.left), window.innerWidth - menuWidth - 8);
                                            this.menuTop = Math.max(8, rect.bottom + 8);
                                        },
                                        toggleMore() {
                                            if (!this.openMore) {
                                                window.dispatchEvent(new CustomEvent('more-action-opened', { detail: { id: this.rowId } }));
                                                this.repositionMore();
                                            }
                                            this.openMore = !this.openMore;
                                        }
                                    }"
                                        :class="{ 'is-open': openMore }"
                                        @more-action-opened.window="if (($event.detail?.id ?? null) !== rowId) { openMore = false }"
                                        @keydown.escape.window="openMore = false"
                                        @resize.window="if (openMore) repositionMore()"
                                        @scroll.window="if (openMore) repositionMore()"
                                        @click.window="if (openMore && !$refs.moreBtn.contains($event.target) && (!$refs.moreMenu || !$refs.moreMenu.contains($event.target))) { openMore = false }">
                                        <button type="button" class="booking-accept-btn"
                                            wire:click="acceptBooking({{ $booking->id }})"
                                            wire:loading.attr="disabled"
                                            wire:target="acceptBooking({{ $booking->id }})"
                                            aria-label="Accept booking">
                                            <span wire:loading.remove
                                                wire:target="acceptBooking({{ $booking->id }})">Accept</span>
                                            <span class="booking-accept-loading" wire:loading.inline-flex
                                                wire:target="acceptBooking({{ $booking->id }})">
                                                <span class="booking-accept-spinner" aria-hidden="true"></span>
                                            </span>
                                        </button>
                                        <button type="button" class="booking-decline-btn"
                                            @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                                            wire:click="openDeclineModal({{ $booking->id }})"
                                            aria-label="Decline booking">
                                            <span aria-hidden="true"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="36" height="36" viewBox="0 0 36 36"
                                                    fill="none">
                                                    <rect width="36" height="36" rx="18"
                                                        fill="#FF6E6E" />
                                                    <path d="M13 23L23 13M13 13L23 23" stroke="white"
                                                        stroke-width="1.5" stroke-linecap="round" />
                                                </svg></span>
                                        </button>
                                        <div class="more-action-wrapper">
                                            <button type="button" class="more-action-trigger"
                                                aria-label="More actions" x-ref="moreBtn" @click.stop="toggleMore()">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="25" height="5"
                                                    viewBox="0 0 25 5" fill="none">
                                                    <circle cx="2.5" cy="2.5" r="2.5" fill="#3B3731" />
                                                    <circle cx="12.5" cy="2.5" r="2.5" fill="#3B3731" />
                                                    <circle cx="22.5" cy="2.5" r="2.5" fill="#3B3731" />
                                                </svg>
                                            </button>

                                            <template x-teleport="body">
                                                <div class="more-action-menu" x-cloak x-show="openMore"
                                                    x-ref="moreMenu" x-transition.opacity.duration.120ms
                                                    :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; z-index: 99999;`">
                                                    <button type="button" class="more-action-menu-item">
                                                        <span>Message</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="15"
                                                            height="14" viewBox="0 0 15 14" fill="none">
                                                            <path
                                                                d="M7.5 0.75C11.3248 0.75 14.25 3.44368 14.25 6.56348C14.25 9.58586 11.5045 12.2084 7.85547 12.3691L7.5 12.377H7.49805C6.82132 12.3784 6.14689 12.2902 5.49316 12.1152L5.2168 12.041L4.96094 12.1709C4.55369 12.3769 3.6394 12.7709 2.12793 13.0908C2.34446 12.4211 2.52462 11.6686 2.59375 10.9482L2.62695 10.5967L2.37793 10.3467C1.35243 9.3185 0.750021 7.99417 0.75 6.56348C0.75 3.44368 3.67522 0.75 7.5 0.75Z"
                                                                stroke="#CBDCE8" stroke-width="1.5" />
                                                        </svg>
                                                    </button>
                                                    <button type="button" class="more-action-menu-item"
                                                        @click.stop="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start')); $wire.openRescheduleModal(rowId); openMore = false;">
                                                        <span>Reschedule</span>
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="16"
                                                            height="16" viewBox="0 0 16 16" fill="none">
                                                            <path d="M2.36584 14.7456V12.0549H5.05648" stroke="#FFC97A"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                            <path
                                                                d="M14.6246 6.46435C14.91 7.98755 14.6817 9.56243 13.9755 10.9419C13.2692 12.3213 12.125 13.4272 10.7223 14.0861C9.31964 14.745 7.7379 14.9196 6.2253 14.5824C4.7127 14.2452 3.35484 13.4154 2.36479 12.2232M0.86975 9.03565C0.58427 7.51245 0.812567 5.93757 1.51882 4.55813C2.22507 3.1787 3.36931 2.07277 4.77199 1.41388C6.17467 0.754998 7.7564 0.580442 9.269 0.917607C10.7816 1.25477 12.1395 2.08458 13.1295 3.27681"
                                                                stroke="#FFC97A" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                            <path
                                                                d="M13.1284 0.754517V3.44515H10.4377M4.58993 8.11254C4.20912 8.04636 4.20912 7.49956 4.58993 7.43337C5.26397 7.31547 5.88773 6.9998 6.3819 6.52649C6.87608 6.05318 7.21834 5.44361 7.36519 4.77528L7.38798 4.67005C7.47043 4.29357 8.00639 4.2914 8.0921 4.66679L8.12031 4.78939C8.27185 5.45513 8.61692 6.06118 9.1121 6.53126C9.60728 7.00135 10.2304 7.31446 10.9032 7.4312C11.2861 7.49738 11.2861 8.04745 10.9032 8.11471C10.2306 8.23138 9.60753 8.54433 9.11236 9.01421C8.6172 9.48409 8.27204 10.0899 8.12031 10.7554L8.0921 10.877C8.00639 11.2523 7.47043 11.2502 7.38798 10.8737L7.36628 10.7695C7.21928 10.1009 6.87669 9.49114 6.3821 9.0178C5.88751 8.54446 5.26328 8.22897 4.58885 8.11146"
                                                                stroke="#FFC97A" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr wire:key="booking-row-{{ $activeStatus }}-empty">
                                <td colspan="8" class="empty-bookings">No pending bookings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($pendingBookings->count() > $visibleRows)
                    <div class="bookings-load-more-wrap">
                        <button type="button" class="bookings-load-more-btn" wire:click="loadMoreBookings"
                            wire:loading.attr="disabled" wire:target="loadMoreBookings">
                            <span wire:loading.remove wire:target="loadMoreBookings">Load more</span>
                            <span class="bookings-load-more-loading" wire:loading.inline-flex
                                wire:target="loadMoreBookings">
                                <span class="bookings-load-more-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                @endif
            @endif

            @if ($activeStatus === 'confirmed')
                <table class="bookings-table confirmed-bookings-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Appointment Details</th>
                            <th>Pet</th>
                            <th>Service Type</th>
                            <th>Owner</th>
                            <th>Location</th>
                            <th>Staff</th>
                            <th class="confirmed-action-col">Action</th>
                        </tr>
                    </thead>
                    <tbody wire:key="bookings-table-confirmed" class="bookings-table-body">
                        @php
                            $confirmedBookings = $bookings->where('booking_status', 'confirmed')->values();
                            $visibleConfirmedBookings = $confirmedBookings->take($visibleRows);
                        @endphp
                        @forelse ($visibleConfirmedBookings as $booking)
                            @php
                                $petNames = $booking->pets->pluck('name')->filter()->values()->all();
                                $petTypes = $booking->pets->pluck('pet_type')->filter()->unique()->values()->all();
                                $petName = $petNames[0] ?? 'N/A';
                                $petMore = count($petNames) > 1 ? '+' . (count($petNames) - 1) : '';
                                $petType = $petTypes[0] ?? null;

                                $appointmentDate = optional($booking->date)->format('d/m/y');
                                $appointmentTimeRaw = (string) $booking->time;
                                $appointmentTime = $appointmentTimeRaw;

                                if (str_contains($appointmentTimeRaw, '-')) {
                                    $parts = preg_split('/\s*-\s*/', $appointmentTimeRaw, 2);
                                    $startPart = $parts[0] ?? '';
                                    $endPart = $parts[1] ?? '';
                                    preg_match('/(\d{1,2}:\d{2})/', $startPart, $mStart);
                                    preg_match('/(\d{1,2}:\d{2})/', $endPart, $mEnd);

                                    if (!empty($mStart[1]) && !empty($mEnd[1])) {
                                        try {
                                            $startDt = new DateTime($mStart[1]);
                                            $endDt = new DateTime($mEnd[1]);
                                            if ($endDt < $startDt) {
                                                $endDt->modify('+1 day');
                                            }
                                            $durationMinutes = (int) max(
                                                0,
                                                ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60,
                                            );
                                            $durationLabel =
                                                '(' .
                                                (int) floor($durationMinutes / 60) .
                                                'hr' .
                                                ($durationMinutes % 60 ? ' ' . $durationMinutes % 60 . 'm' : '') .
                                                ')';
                                            $appointmentTime =
                                                $startDt->format('H:i') .
                                                ' - ' .
                                                $endDt->format('H:i') .
                                                ' ' .
                                                strtolower($endDt->format('a')) .
                                                ' ' .
                                                $durationLabel;
                                        } catch (Throwable $e) {
                                            $appointmentTime = $appointmentTimeRaw;
                                        }
                                    }
                                }

                                $locationLabel = strtolower((string) ($booking->visit_type ?? ''));
                                $locationLabel = str_replace('_', ' ', $locationLabel);
                                $locationLabel =
                                    $locationLabel === 'home' || $locationLabel === 'home visit'
                                        ? 'Home Visit'
                                        : ($locationLabel === 'salon' || $locationLabel === 'salon visit'
                                            ? 'Salon Visit'
                                            : ucfirst($locationLabel ?: 'N/A'));
                            @endphp
                            <tr wire:key="booking-row-confirmed-{{ $booking->id }}">
                                <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="confirmed-appointment-cell">
                                        <div>{{ $appointmentDate }}</div>
                                        <div>{{ $appointmentTime }}</div>
                                    </div>
                                </td>
                                <td>
                                    <div class="filtered-pet-cell">
                                        <span class="booking-pet-name">{{ $petName }}</span>
                                        <span>
                                            @if ($petType)
                                                <span class="booking-pet-type">{{ $petType }}</span>
                                            @endif
                                            @if ($petMore)
                                                <span class="booking-pet-more">{{ $petMore }}</span>
                                            @endif
                                        </span>
                                    </div>
                                </td>
                                <td>{{ $booking->service }}</td>
                                <td>{{ $booking->petOwner->name ?? 'N/A' }}</td>
                                <td>{{ $locationLabel }}</td>
                                <td>—</td>
                                <td class="confirmed-action-col">
                                    <div class="confirmed-action-cell" x-data="{
                                        rowId: {{ $booking->id }},
                                        openMore: false,
                                        menuLeft: 8,
                                        menuTop: 8,
                                        repositionMore() {
                                            const rect = $refs.moreBtn.getBoundingClientRect();
                                            const menuWidth = 210;
                                            this.menuLeft = Math.min(Math.max(8, rect.left), window.innerWidth - menuWidth - 8);
                                            this.menuTop = Math.max(8, rect.bottom + 8);
                                        },
                                        toggleMore() {
                                            if (!this.openMore) {
                                                window.dispatchEvent(new CustomEvent('confirmed-more-opened', { detail: { id: this.rowId } }));
                                                this.repositionMore();
                                            }
                                            this.openMore = !this.openMore;
                                        }
                                    }"
                                        :class="{ 'is-open': openMore }"
                                        @confirmed-more-opened.window="if (($event.detail?.id ?? null) !== rowId) { openMore = false }"
                                        @keydown.escape.window="openMore = false"
                                        @resize.window="if (openMore) repositionMore()"
                                        @scroll.window="if (openMore) repositionMore()"
                                        @click.window="if (openMore && !$refs.moreBtn.contains($event.target) && (!$refs.moreMenu || !$refs.moreMenu.contains($event.target))) { openMore = false }">
                                        <button type="button" class="confirmed-action-btn is-message"
                                            aria-label="Message">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                                viewBox="0 0 36 36" fill="none">
                                                <rect width="36" height="36" rx="18" fill="#CBDCE8" />
                                                <path
                                                    d="M18.3955 11.25C22.4278 11.25 25.542 14.1354 25.542 17.5137C25.542 20.892 22.4278 23.7773 18.3955 23.7773H18.3945C17.6796 23.779 16.9672 23.6847 16.2764 23.4971L15.9951 23.4209L15.7373 23.5537C15.3001 23.7782 14.314 24.2099 12.6807 24.5547C12.9199 23.8218 13.1163 22.9878 13.1914 22.1934L13.2236 21.8457L12.9795 21.5967C11.8924 20.4903 11.25 19.0614 11.25 17.5137C11.25 14.1355 14.3634 11.2502 18.3955 11.25Z"
                                                    stroke="white" stroke-width="1.5" />
                                            </svg>
                                        </button>
                                        <button type="button" class="confirmed-action-btn is-reschedule"
                                            @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                                            wire:click="openRescheduleModal({{ $booking->id }})"
                                            aria-label="Reschedule">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                                viewBox="0 0 36 36" fill="none">
                                                <rect width="36" height="36" rx="18" fill="#FFC97A" />
                                                <path d="M12.2312 25.4951V22.6123H15.114" stroke="white"
                                                    stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M25.3656 16.6225C25.6715 18.2545 25.4269 19.9419 24.6702 21.4199C23.9135 22.8978 22.6875 24.0827 21.1846 24.7887C19.6818 25.4946 17.987 25.6817 16.3664 25.3204C14.7458 24.9592 13.2909 24.0701 12.2301 22.7927M10.6283 19.3775C10.3224 17.7455 10.567 16.0581 11.3237 14.5801C12.0804 13.1022 13.3064 11.9173 14.8093 11.2113C16.3121 10.5054 18.0069 10.3183 19.6275 10.6796C21.2481 11.0408 22.703 11.9299 23.7638 13.2073"
                                                    stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                                <path
                                                    d="M23.7626 10.5049V13.3877H20.8797M14.6142 18.3885C14.2062 18.3176 14.2062 17.7317 14.6142 17.6608C15.3364 17.5345 16.0047 17.1963 16.5342 16.6891C17.0637 16.182 17.4304 15.5289 17.5877 14.8128L17.6121 14.7001C17.7005 14.2967 18.2747 14.2944 18.3666 14.6966L18.3968 14.828C18.5592 15.5413 18.9289 16.1906 19.4594 16.6943C19.99 17.1979 20.6576 17.5334 21.3784 17.6585C21.7888 17.7294 21.7888 18.3187 21.3784 18.3908C20.6578 18.5158 19.9902 18.8511 19.4597 19.3546C18.9292 19.858 18.5594 20.5071 18.3968 21.2202L18.3666 21.3504C18.2747 21.7526 17.7005 21.7502 17.6121 21.3469L17.5889 21.2353C17.4314 20.5189 17.0643 19.8655 16.5344 19.3584C16.0045 18.8513 15.3357 18.5132 14.6131 18.3873"
                                                    stroke="white" stroke-width="1.5" stroke-linecap="round"
                                                    stroke-linejoin="round" />
                                            </svg>
                                        </button>
                                        <button type="button" class="confirmed-action-btn is-cancel"
                                            @click="window.dispatchEvent(new CustomEvent('bookings-tabs-loading-start'))"
                                            wire:click="openDeclineModal({{ $booking->id }})" aria-label="Cancel">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"
                                                viewBox="0 0 36 36" fill="none">
                                                <rect width="36" height="36" rx="18" fill="#FF6E6E" />
                                                <path d="M13 23L23 13M13 13L23 23" stroke="white" stroke-width="1.5"
                                                    stroke-linecap="round" />
                                            </svg>
                                        </button>
                                        {{-- <button type="button" class="confirmed-more-btn" aria-label="More"
                                        x-ref="moreBtn" @click.stop="toggleMore()">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="25" height="5"
                                            viewBox="0 0 25 5" fill="none">
                                            <circle cx="2.5" cy="2.5" r="2.5" fill="#3B3731" />
                                            <circle cx="12.5" cy="2.5" r="2.5" fill="#3B3731" />
                                            <circle cx="22.5" cy="2.5" r="2.5" fill="#3B3731" />
                                        </svg>
                                    </button>
                                    <template x-teleport="body">
                                        <div class="more-action-menu" x-cloak x-show="openMore" x-ref="moreMenu"
                                            x-transition.opacity.duration.120ms
                                            :style="`position: fixed; left: ${menuLeft}px; top: ${menuTop}px; z-index: 99999;`">
                                            <button type="button" class="more-action-menu-item">
                                                <span>Message</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="14"
                                                    viewBox="0 0 15 14" fill="none">
                                                    <path
                                                        d="M7.5 0.75C11.3248 0.75 14.25 3.44368 14.25 6.56348C14.25 9.58586 11.5045 12.2084 7.85547 12.3691L7.5 12.377H7.49805C6.82132 12.3784 6.14689 12.2902 5.49316 12.1152L5.2168 12.041L4.96094 12.1709C4.55369 12.3769 3.6394 12.7709 2.12793 13.0908C2.34446 12.4211 2.52462 11.6686 2.59375 10.9482L2.62695 10.5967L2.37793 10.3467C1.35243 9.3185 0.750021 7.99417 0.75 6.56348C0.75 3.44368 3.67522 0.75 7.5 0.75Z"
                                                        stroke="#CBDCE8" stroke-width="1.5" />
                                                </svg>
                                            </button>
                                            <button type="button" class="more-action-menu-item"
                                                @click.stop="$wire.openRescheduleModal(rowId); openMore = false;">
                                                <span>Reschedule</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                                    viewBox="0 0 16 16" fill="none">
                                                    <path d="M2.36584 14.7456V12.0549H5.05648" stroke="#FFC97A"
                                                        stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                    <path
                                                        d="M14.6246 6.46435C14.91 7.98755 14.6817 9.56243 13.9755 10.9419C13.2692 12.3213 12.125 13.4272 10.7223 14.0861C9.31964 14.745 7.7379 14.9196 6.2253 14.5824C4.7127 14.2452 3.35484 13.4154 2.36479 12.2232M0.86975 9.03565C0.58427 7.51245 0.812567 5.93757 1.51882 4.55813C2.22507 3.1787 3.36931 2.07277 4.77199 1.41388C6.17467 0.754998 7.7564 0.580442 9.269 0.917607C10.7816 1.25477 12.1395 2.08458 13.1295 3.27681"
                                                        stroke="#FFC97A" stroke-width="1.5" stroke-linecap="round"
                                                        stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </div>
                                    </template> --}}
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-bookings">No confirmed bookings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($confirmedBookings->count() > $visibleRows)
                    <div class="bookings-load-more-wrap">
                        <button type="button" class="bookings-load-more-btn" wire:click="loadMoreBookings"
                            wire:loading.attr="disabled" wire:target="loadMoreBookings">
                            <span wire:loading.remove wire:target="loadMoreBookings">Load more</span>
                            <span class="bookings-load-more-loading" wire:loading.inline-flex
                                wire:target="loadMoreBookings">
                                <span class="bookings-load-more-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                @endif
            @endif

            @if ($activeStatus !== 'pending' && $activeStatus !== 'confirmed')
                <table class="bookings-table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Owner</th>
                            <th>Pet</th>
                            <th>Service Type</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Amount</th>
                            <th class="view-col">
                                <span class="view-col-inner">View</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody wire:key="bookings-table-{{ $activeStatus }}" class="bookings-table-body">
                        @php
                            $filteredBookings = ($activeStatus === 'all'
                                ? $bookings
                                : $bookings->where('booking_status', $activeStatus)
                            )->values();
                            $visibleFilteredBookings = $filteredBookings->take($visibleRows);
                        @endphp
                        @forelse ($visibleFilteredBookings as $booking)
                            @php
                                $firstPet = $booking->pets->first();
                                $petName = $firstPet->name ?? 'N/A';
                                $petType = $firstPet->pet_type ?? null;
                            @endphp
                            <tr>
                                <td>FG-{{ str_pad((string) $booking->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $booking->petOwner->name ?? 'N/A' }}</td>
                                <td>
                                    <div class="pet-name-wrap">
                                        <span class="pet-name">{{ $petName }}</span>
                                        @if ($petType)
                                            <span class="pet-type">{{ $petType }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>{{ $booking->service }}</td>
                                <td>{{ optional($booking->date)->format('d/m/y') }}</td>
                                <td>
                                    <span class="status-chip {{ $booking->booking_status }}">
                                        {{ ucfirst($booking->booking_status) }}
                                    </span>
                                </td>
                                <td>£{{ number_format((float) $booking->amount, 2) }}</td>
                                <td class="view-col">
                                    <div class="view-col-inner">
                                        <button type="button" class="view-btn" aria-label="View booking">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="13"
                                                viewBox="0 0 19 13" fill="none">
                                                <path
                                                    d="M9.49609 12C11.4291 12 12.9961 10.433 12.9961 8.5C12.9961 6.567 11.4291 5 9.49609 5C7.5631 5 5.99609 6.567 5.99609 8.5C5.99609 10.433 7.5631 12 9.49609 12Z"
                                                    stroke="black" />
                                                <path
                                                    d="M18.4961 8.5C18.4961 8.5 17.4961 0.5 9.49609 0.5C1.49609 0.5 0.496094 8.5 0.496094 8.5"
                                                    stroke="black" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="empty-bookings">No bookings found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                @if ($filteredBookings->count() > $visibleRows)
                    <div class="bookings-load-more-wrap">
                        <button type="button" class="bookings-load-more-btn" wire:click="loadMoreBookings"
                            wire:loading.attr="disabled" wire:target="loadMoreBookings">
                            <span wire:loading.remove wire:target="loadMoreBookings">Load more</span>
                            <span class="bookings-load-more-loading" wire:loading.inline-flex
                                wire:target="loadMoreBookings">
                                <span class="bookings-load-more-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                @endif
            @endif
        </div>
    @endif

    @php
        $declineBooking = $declineBookingId ? $bookings->firstWhere('id', $declineBookingId) : null;
    @endphp

    @if ($declineBooking)
        @php
            $declinePetName = $declineBooking->pets->pluck('name')->filter()->first() ?? 'N/A';
            $declineDateLabel = optional($declineBooking->date)->format('D j F') ?? 'N/A';
            $declineClient = $declineBooking->petOwner->name ?? 'N/A';
            $declineBookingIdLabel = 'FG-' . str_pad((string) $declineBooking->id, 5, '0', STR_PAD_LEFT);
            $declineAmountLabel = '£' . number_format((float) $declineBooking->amount, 2);
            $declineTimeRaw = (string) $declineBooking->time;
            $declineTimeLabel = $declineTimeRaw !== '' ? $declineTimeRaw : 'N/A';

            if (str_contains($declineTimeRaw, '-')) {
                $parts = preg_split('/\s*-\s*/', $declineTimeRaw, 2);
                $start = $parts[0] ?? '';
                $end = $parts[1] ?? '';
                preg_match('/(\d{1,2}:\d{2})/', $start, $mStart);
                preg_match('/(\d{1,2}:\d{2})/', $end, $mEnd);

                if (!empty($mStart[1]) && !empty($mEnd[1])) {
                    try {
                        $startDt = new DateTime($mStart[1]);
                        $endDt = new DateTime($mEnd[1]);
                        if ($endDt < $startDt) {
                            $endDt->modify('+1 day');
                        }

                        $durationMinutes = (int) max(0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60);
                        $hours = (int) floor($durationMinutes / 60);
                        $minutes = $durationMinutes % 60;
                        $durationLabel = $minutes === 0 ? $hours . 'hr' : $hours . 'hr ' . $minutes . 'm';
                        $declineTimeLabel = $startDt->format('h:i A') . ' (' . $durationLabel . ')';
                    } catch (Throwable $e) {
                        $declineTimeLabel = $declineTimeRaw;
                    }
                }
            }
        @endphp

        @teleport('body')
            <div class="decline-modal-overlay" wire:keydown.escape="closeDeclineModal">
                <div class="decline-modal-card" role="dialog" aria-modal="true" aria-labelledby="decline-modal-title">
                    <button type="button" class="decline-modal-close" wire:click="closeDeclineModal"
                        aria-label="Close modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"
                            fill="none">
                            <path d="M0.75 15.75L15.75 0.75M0.75 0.75L15.75 15.75" stroke="#3B3731" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </button>

                    <div class="decline-modal-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="75" viewBox="0 0 80 75"
                            fill="none">
                            <path
                                d="M3.92636 28.2354C3.76636 31.1189 3.76636 34.5495 3.76636 38.6709V45.1898C3.76636 57.4811 3.76998 63.6252 8.01057 67.4453C12.2512 71.2653 19.081 71.2653 32.7371 71.2653H47.2225C60.8786 71.2653 67.7048 71.2621 71.9491 67.4453C76.1933 63.6285 76.1933 57.4811 76.1933 45.1898V38.6709C76.1933 34.5492 76.1929 31.1187 76.0324 28.2354H3.92636Z"
                                fill="#FFEDED" />
                            <path
                                d="M78.4314 36.4483C78.4314 29.3475 78.4285 24.1046 77.8631 20.0834C77.3012 16.0884 76.1974 13.4147 74.0771 11.3832C71.9522 9.34738 69.1454 8.28455 64.9548 7.74501C60.745 7.203 55.2585 7.2012 47.8431 7.2012H32.1569C24.7416 7.2012 19.256 7.20331 15.0467 7.74577C10.8566 8.28577 8.04924 9.34877 5.92218 11.3832C3.80034 13.4127 2.69679 16.0863 2.13542 20.0819C1.5704 24.1035 1.56863 29.3474 1.56863 36.4483V43.9567C1.56863 51.0577 1.57141 56.3011 2.13695 60.3224C2.69882 64.3173 3.80257 66.9911 5.92295 69.0226C8.04782 71.0583 10.8546 72.1212 15.0452 72.6608C19.255 73.2028 24.7415 73.2038 32.1569 73.2038H47.8431C55.2584 73.2038 60.744 73.2017 64.9533 72.6592C69.1434 72.1192 71.9508 71.0562 74.0778 69.0218C76.1996 66.9924 77.3032 64.3192 77.8646 60.3239C78.4296 56.3022 78.4314 51.0579 78.4314 43.9567V36.4483ZM80 43.9567C80 51.0114 80.0018 56.3853 79.4179 60.5414C78.8303 64.7238 77.6358 67.789 75.1616 70.1554C72.6927 72.5168 69.5058 73.654 65.154 74.2148C60.8211 74.7732 55.2161 74.7724 47.8431 74.7724H32.1569C24.7842 74.7724 19.1786 74.7742 14.8453 74.2164C10.4928 73.656 7.30489 72.5185 4.83762 70.1546C2.36609 67.7866 1.17177 64.7224 0.58364 60.5407C-0.000812774 56.3849 5.42728e-08 51.0117 6.88498e-08 43.9567V36.4483C1.55774e-08 29.3936 -0.00180258 24.0197 0.582108 19.8636C1.16974 15.6812 2.36421 12.616 4.83839 10.2496C7.30734 7.88825 10.4942 6.75103 14.846 6.19017C19.1789 5.63178 24.7839 5.63257 32.1569 5.63257H47.8431C55.2158 5.63257 60.8214 5.63078 65.1547 6.18863C69.5072 6.74901 72.6951 7.88654 75.1624 10.2504C77.6339 12.6184 78.8282 15.6826 79.4164 19.8643C80.0008 24.0201 80 29.3934 80 36.4483V43.9567Z"
                                fill="#3B3731" />
                            <path
                                d="M78.3237 24.2846C78.7569 24.2846 79.108 24.6358 79.108 25.0689C79.1078 25.5019 78.7567 25.8532 78.3237 25.8532H1.63636C1.20332 25.8532 0.85225 25.5019 0.852051 25.0689C0.852051 24.6358 1.2032 24.2846 1.63636 24.2846H78.3237ZM19.0146 6.38863V0.784314C19.0146 0.351235 19.3659 0.000139025 19.7989 0C20.2321 0 20.5832 0.351149 20.5832 0.784314V6.38863C20.5831 6.82168 20.232 7.17295 19.7989 7.17295C19.3659 7.17281 19.0147 6.82159 19.0146 6.38863ZM59.3769 6.38863V0.784314C59.3769 0.351149 59.728 0 60.1612 0C60.5942 0.000148722 60.9455 0.351241 60.9455 0.784314V6.38863C60.9454 6.82159 60.5941 7.1728 60.1612 7.17295C59.7281 7.17295 59.377 6.82168 59.3769 6.38863Z"
                                fill="#3B3731" />
                            <path
                                d="M48.2354 49.8039C48.2354 45.4723 44.7239 41.9608 40.3923 41.9608C36.0607 41.9608 32.5492 45.4723 32.5492 49.8039C32.5492 54.1356 36.0607 57.6471 40.3923 57.6471V59.6078C34.9777 59.6078 30.5884 55.2185 30.5884 49.8039C30.5884 44.3894 34.9777 40 40.3923 40C45.8069 40 50.1962 44.3894 50.1962 49.8039C50.1962 55.2185 45.8069 59.6078 40.3923 59.6078V57.6471C44.7239 57.6471 48.2354 54.1356 48.2354 49.8039Z"
                                fill="#FF6E6E" />
                            <path
                                d="M42.9666 46.2784C43.3495 45.8955 43.9709 45.8955 44.3537 46.2784C44.7363 46.6612 44.7363 47.2819 44.3537 47.6647L41.9962 50.0215L44.3537 52.3782C44.7365 52.7611 44.7366 53.3825 44.3537 53.7653C43.9709 54.1482 43.3495 54.1482 42.9666 53.7653L40.6098 51.4078L38.2531 53.7653C37.8703 54.1479 37.2496 54.1479 36.8667 53.7653C36.4839 53.3825 36.4839 52.7611 36.8667 52.3782L39.2227 50.0215L36.8667 47.6647C36.4839 47.2818 36.4839 46.6612 36.8667 46.2784C37.2496 45.8955 37.8702 45.8955 38.2531 46.2784L40.6098 48.6344L42.9666 46.2784Z"
                                fill="#FF6E6E" />
                        </svg>
                    </div>

                    <h3 class="decline-modal-title" id="decline-modal-title">Decline Booking Request</h3>
                    <p class="decline-modal-subtitle">Are you sure you want to decline <br />this booking request?</p>

                    <div class="decline-modal-details">
                        <div class="decline-modal-detail-row"><span>Booking
                                ID</span><strong>{{ $declineBookingIdLabel }}</strong></div>
                        <div class="decline-modal-detail-row"><span>Pet</span><strong>{{ $declinePetName }}</strong></div>
                        <div class="decline-modal-detail-row">
                            <span>Service</span><strong>{{ $declineBooking->service }}</strong>
                        </div>
                        <div class="decline-modal-detail-row"><span>Date</span><strong>{{ $declineDateLabel }}</strong>
                        </div>
                        <div class="decline-modal-detail-row"><span>Time</span><strong>{{ $declineTimeLabel }}</strong>
                        </div>
                        <div class="decline-modal-detail-row"><span>Client</span><strong>{{ $declineClient }}</strong>
                        </div>
                        <div class="decline-modal-detail-row decline-modal-detail-payment">
                            <span>Payment</span><strong>{{ $declineAmountLabel }}</strong>
                        </div>
                    </div>

                    <div class="decline-modal-actions">
                        <button type="button" class="decline-cancel-btn" wire:click="closeDeclineModal">Cancel</button>
                        <button type="button" class="decline-confirm-btn" wire:click="confirmDeclineBooking"
                            wire:loading.attr="disabled" wire:target="confirmDeclineBooking">
                            <span wire:loading.remove wire:target="confirmDeclineBooking">Decline Booking</span>
                            <span class="decline-btn-loading" wire:loading.inline-flex
                                wire:target="confirmDeclineBooking">
                                <span class="decline-btn-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif

    @php
        $rescheduleBooking = $rescheduleBookingId ? $bookings->firstWhere('id', $rescheduleBookingId) : null;
    @endphp

    @if ($rescheduleBooking)
        @php
            $rescheduleBookingIdLabel = 'FG-' . str_pad((string) $rescheduleBooking->id, 5, '0', STR_PAD_LEFT);
            $reschedulePet = $rescheduleBooking->pets->first();
            $reschedulePetName = $reschedulePet->name ?? 'N/A';
            $reschedulePetType = $reschedulePet->pet_type ?? 'N/A';
            $reschedulePetBreed = $reschedulePet->breed ?? null;
            $reschedulePetMeta =
                $reschedulePetType !== 'N/A'
                    ? ($reschedulePetBreed
                        ? $reschedulePetType . ' (' . $reschedulePetBreed . ')'
                        : $reschedulePetType)
                    : ($reschedulePetBreed ?:
                    'N/A');
            $reschedulePetPhotoRaw = trim((string) ($reschedulePet->photo ?? ''));
            $reschedulePetPhoto = null;
            if ($reschedulePetPhotoRaw !== '') {
                $isAbsolute =
                    str_starts_with($reschedulePetPhotoRaw, 'http://') ||
                    str_starts_with($reschedulePetPhotoRaw, 'https://') ||
                    str_starts_with($reschedulePetPhotoRaw, 'data:') ||
                    str_starts_with($reschedulePetPhotoRaw, '/');
                $reschedulePetPhoto = $isAbsolute
                    ? $reschedulePetPhotoRaw
                    : asset('storage/' . ltrim($reschedulePetPhotoRaw, '/'));
            }
            $rescheduleDateObj = $rescheduleSelectedDate
                ? new DateTimeImmutable($rescheduleSelectedDate)
                : ($rescheduleBooking->date
                    ? new DateTimeImmutable((string) $rescheduleBooking->date)
                    : null);
            $rescheduleDateCard = $rescheduleDateObj ? $rescheduleDateObj->format('d/m/Y') : 'N/A';
            $calendarBase = $rescheduleCalendarMonth
                ? new DateTimeImmutable($rescheduleCalendarMonth)
                : ($rescheduleDateObj ?:
                new DateTimeImmutable(date('Y-m-01')));
            $rescheduleMonthTitle = $calendarBase->format('F Y');
            $rescheduleYear = (int) $calendarBase->format('Y');
            $rescheduleMonth = (int) $calendarBase->format('m');
            $selectedYear = (int) ($rescheduleDateObj ? $rescheduleDateObj->format('Y') : 0);
            $selectedMonth = (int) ($rescheduleDateObj ? $rescheduleDateObj->format('m') : 0);
            $rescheduleSelectedDay =
                $selectedYear === $rescheduleYear && $selectedMonth === $rescheduleMonth
                    ? (int) $rescheduleDateObj->format('j')
                    : 0;
            $bookedDaysByMonth = $bookings
                ->filter(function ($b) {
                    return $b->date && $b->booking_status !== 'cancelled';
                })
                ->groupBy(function ($b) {
                    return date('Y-m', strtotime((string) $b->date));
                })
                ->map(function ($rows) {
                    return $rows
                        ->map(fn($b) => (int) date('j', strtotime((string) $b->date)))
                        ->unique()
                        ->sort()
                        ->values()
                        ->all();
                })
                ->all();
            $bookedDaysInMonth = $bookedDaysByMonth[sprintf('%04d-%02d', $rescheduleYear, $rescheduleMonth)] ?? [];
            $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $rescheduleMonth, $rescheduleYear);
            $startDayN = (int) date('N', strtotime(sprintf('%04d-%02d-01', $rescheduleYear, $rescheduleMonth)));
            $prefixBlank = $startDayN - 1;
            $rescheduleAvailability = ['09:00 AM', '11:00 AM', '12:00 PM', '04:00 PM', '08:00 PM'];

            $rescheduleTimeRaw = trim((string) $rescheduleBooking->time);
            $rescheduleTimeCard = $rescheduleTimeRaw !== '' ? $rescheduleTimeRaw : 'N/A';
            $rescheduleDurationLabel = '';
            if (str_contains($rescheduleTimeRaw, '-')) {
                $parts = preg_split('/\s*-\s*/', $rescheduleTimeRaw, 2);
                $start = $parts[0] ?? '';
                $end = $parts[1] ?? '';
                preg_match('/(\d{1,2}:\d{2})/', $start, $mStart);
                preg_match('/(\d{1,2}:\d{2})/', $end, $mEnd);
                if (!empty($mStart[1]) && !empty($mEnd[1])) {
                    try {
                        $startDt = new DateTimeImmutable($mStart[1]);
                        $endDt = new DateTimeImmutable($mEnd[1]);
                        if ($endDt < $startDt) {
                            $endDt = $endDt->modify('+1 day');
                        }
                        $durationMinutes = (int) max(0, ($endDt->getTimestamp() - $startDt->getTimestamp()) / 60);
                        $rescheduleDurationLabel = '(' . $durationMinutes . ' minutes)';
                    } catch (Throwable $e) {
                        $rescheduleDurationLabel = '';
                    }
                }
            }

            if ($rescheduleSelectedTime) {
                $selectedStart = DateTime::createFromFormat('h:i A', $rescheduleSelectedTime);
                if ($selectedStart) {
                    $selectedEnd = (clone $selectedStart)->modify(
                        '+' . max(1, $rescheduleDurationMinutes) . ' minutes',
                    );
                    $rescheduleTimeCard = $selectedStart->format('H:i') . ' - ' . $selectedEnd->format('H:i');
                }
            }
        @endphp
        @teleport('body')
            <div class="reschedule-modal-overlay" wire:keydown.escape="closeRescheduleModal" x-data="reschedulePicker({
                initialDate: @js($rescheduleSelectedDate),
                initialTime: @js($rescheduleSelectedTime),
                initialMonth: @js(sprintf('%04d-%02d-01', $rescheduleYear, $rescheduleMonth)),
                bookedDaysByMonth: @js($bookedDaysByMonth)
            })">
                <div class="reschedule-modal-card" role="dialog" aria-modal="true"
                    aria-labelledby="reschedule-modal-title">
                    <div class="reschedule-modal-loading-bar" wire:loading.flex
                        wire:target="confirmRescheduleBookingFromClient" aria-hidden="true">
                        <span class="reschedule-modal-loading-bar__sweep"></span>
                    </div>
                    <button type="button" class="reschedule-modal-close" wire:click="closeRescheduleModal"
                        aria-label="Close modal">
                        <svg xmlns="http://www.w3.org/2000/svg" width="17" height="17" viewBox="0 0 17 17"
                            fill="none">
                            <path d="M0.75 15.75L15.75 0.75M0.75 0.75L15.75 15.75" stroke="#3B3731" stroke-width="1.5"
                                stroke-linecap="round" />
                        </svg>
                    </button>

                    <div class="reschedule-modal-icon" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="82" height="77" viewBox="0 0 82 77"
                            fill="none">
                            <path
                                d="M4.20495 29C4.04175 31.9412 4.04175 35.4404 4.04175 39.6442V46.2935C4.04175 58.8307 4.04544 65.0976 8.37085 68.9941C12.6963 72.8906 19.6627 72.8906 33.5919 72.8906H48.367C62.2962 72.8906 69.259 72.8873 73.5881 68.9941C77.9172 65.101 77.9172 58.8307 77.9172 46.2935V39.6442C77.9172 35.4401 77.9168 31.941 77.7531 29H4.20495Z"
                                fill="#F2F6F9" />
                            <path
                                d="M41.3999 61C40.011 61 38.7103 60.7363 37.4977 60.2089C36.2851 59.6815 35.2295 58.9685 34.331 58.07C33.4325 57.1715 32.7195 56.1159 32.1921 54.9033C31.6647 53.6907 31.4006 52.3896 31.3999 51C31.3992 49.6104 31.6632 48.3096 32.1921 47.0978C32.721 45.8859 33.4336 44.8304 34.3299 43.9311C35.2262 43.0319 36.2818 42.3189 37.4966 41.7922C38.7114 41.2656 40.0125 41.0015 41.3999 41C42.9184 41 44.3584 41.3241 45.7199 41.9722C47.0814 42.6204 48.234 43.537 49.1777 44.7222V43.2222C49.1777 42.9074 49.2843 42.6437 49.4977 42.4311C49.711 42.2185 49.9747 42.1119 50.2888 42.1111C50.6029 42.1104 50.8669 42.217 51.081 42.4311C51.2951 42.6452 51.4014 42.9089 51.3999 43.2222V47.6667C51.3999 47.9815 51.2932 48.2456 51.0799 48.4589C50.8666 48.6722 50.6029 48.7785 50.2888 48.7778H45.8443C45.5295 48.7778 45.2658 48.6711 45.0532 48.4578C44.8406 48.2444 44.734 47.9807 44.7332 47.6667C44.7325 47.3526 44.8392 47.0889 45.0532 46.8756C45.2673 46.6622 45.531 46.5556 45.8443 46.5556H47.7888C47.0295 45.5185 46.0943 44.7037 44.9832 44.1111C43.8721 43.5185 42.6777 43.2222 41.3999 43.2222C39.2332 43.2222 37.3955 43.977 35.8866 45.4867C34.3777 46.9963 33.6229 48.8341 33.6221 51C33.6214 53.1659 34.3762 55.0041 35.8866 56.5144C37.3969 58.0248 39.2347 58.7793 41.3999 58.7778C43.1592 58.7778 44.7332 58.25 46.1221 57.1944C47.511 56.1389 48.4277 54.7778 48.8721 53.1111C48.9647 52.8148 49.1314 52.5926 49.3721 52.4444C49.6129 52.2963 49.8814 52.2407 50.1777 52.2778C50.4925 52.3148 50.7425 52.4489 50.9277 52.68C51.1129 52.9111 51.1684 53.1659 51.0943 53.4444C50.5573 55.6481 49.3906 57.4585 47.5943 58.8756C45.798 60.2926 43.7332 61.0007 41.3999 61ZM42.511 50.5556L45.2888 53.3333C45.4925 53.537 45.5943 53.7963 45.5943 54.1111C45.5943 54.4259 45.4925 54.6852 45.2888 54.8889C45.0851 55.0926 44.8258 55.1944 44.511 55.1944C44.1962 55.1944 43.9369 55.0926 43.7332 54.8889L40.6221 51.7778C40.511 51.6667 40.4277 51.5419 40.3721 51.4033C40.3166 51.2648 40.2888 51.1211 40.2888 50.9722V46.5556C40.2888 46.2407 40.3955 45.977 40.6088 45.7644C40.8221 45.5519 41.0858 45.4452 41.3999 45.4444C41.714 45.4437 41.978 45.5504 42.1921 45.7644C42.4062 45.9785 42.5125 46.2422 42.511 46.5556V50.5556Z"
                                fill="#CBDCE8" />
                            <path
                                d="M1 37.3777C1 22.9384 1 15.7168 5.688 11.233C10.376 6.74919 17.916 6.74536 33 6.74536H49C64.084 6.74536 71.628 6.74536 76.312 11.233C80.996 15.7206 81 22.9384 81 37.3777V45.0358C81 59.4751 81 66.6967 76.312 71.1805C71.624 75.6643 64.084 75.6681 49 75.6681H33C17.916 75.6681 10.372 75.6681 5.688 71.1805C1.004 66.6929 1 59.4751 1 45.0358V37.3777Z"
                                stroke="#3B3731" stroke-width="2" />
                            <path d="M20.3952 6.71614V1M61.5642 6.71614V1M1.86914 25.7699H80.0902" stroke="#3B3731"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </div>

                    <h3 class="reschedule-modal-title" id="reschedule-modal-title"><strong>Reschedule</strong> Booking
                    </h3>
                    <p class="reschedule-modal-subtitle">Are you sure you want to reschedule this booking?</p>

                    <div class="reschedule-summary">
                        <div class="reschedule-pet-avatar">
                            <img src="{{ $reschedulePetPhoto }}" alt="{{ $reschedulePetName }}">
                        </div>
                        <div>
                            <div class="reschedule-summary-header">
                                <span class="reschedule-home-chip">Home Visits</span>
                                <span class="reschedule-booking-id">Booking ID: {{ $rescheduleBookingIdLabel }}</span>
                            </div>
                            <div class="reschedule-summary-grid">
                                <div class="reschedule-summary-item">
                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="16" height="17"
                                            viewBox="0 0 16 17" fill="none">
                                            <path
                                                d="M4.94591 11.5544C6.23114 12.8397 9.35699 11.798 11.9274 9.22713C14.4983 6.65667 15.54 3.53082 14.2548 2.24559M8.72754 1.37259L9.30927 1.95473M6.6915 3.40904L7.27322 3.99077M4.9455 5.73636L5.52722 6.31809M4.36377 8.6454L4.9455 9.22713M11.9274 0.5L12.5092 1.08173M11.3457 3.99118L12.5092 5.15463M9.30968 6.02763L10.4731 7.19109M6.98236 7.77281L8.14581 8.93627"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M4.94547 13.2998C5.42747 12.8178 5.42747 12.0364 4.94548 11.5544C4.46348 11.0724 3.68202 11.0724 3.20003 11.5544L0.872775 13.8816C0.390784 14.3636 0.390784 15.1451 0.872775 15.6271C1.35477 16.1091 2.13623 16.1091 2.61822 15.6271L4.94547 13.2998Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>Service</span><strong>{{ $rescheduleBooking->service }}</strong>
                                </div>
                                <div class="reschedule-summary-divider" aria-hidden="true"></div>
                                <div class="reschedule-summary-item">
                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="19" height="17"
                                            viewBox="0 0 19 17" fill="none">
                                            <path
                                                d="M0.5 8.29554C0.5 5.20139 0.5 3.6539 1.50457 2.69308C2.50914 1.73227 4.12486 1.73145 7.35714 1.73145H10.7857C14.018 1.73145 15.6346 1.73145 16.6383 2.69308C17.642 3.65472 17.6429 5.20139 17.6429 8.29554V9.93656C17.6429 13.0307 17.6429 14.5782 16.6383 15.539C15.6337 16.4998 14.018 16.5007 10.7857 16.5007H7.35714C4.12486 16.5007 2.50829 16.5007 1.50457 15.539C0.500857 14.5774 0.5 13.0307 0.5 9.93656V8.29554Z"
                                                stroke="#3B3731" />
                                            <path d="M4.78585 1.73077V0.5M13.3573 1.73077V0.5M0.928711 5.83333H17.2144"
                                                stroke="#3B3731" stroke-linecap="round" />
                                            <path
                                                d="M14.2139 12.3975C14.2139 12.6151 14.1236 12.8238 13.9629 12.9777C13.8021 13.1315 13.5841 13.218 13.3568 13.218C13.1295 13.218 12.9114 13.1315 12.7507 12.9777C12.59 12.8238 12.4997 12.6151 12.4997 12.3975C12.4997 12.1799 12.59 11.9712 12.7507 11.8173C12.9114 11.6634 13.1295 11.577 13.3568 11.577C13.5841 11.577 13.8021 11.6634 13.9629 11.8173C14.1236 11.9712 14.2139 12.1799 14.2139 12.3975ZM14.2139 9.11543C14.2139 9.33305 14.1236 9.54175 13.9629 9.69562C13.8021 9.8495 13.5841 9.93595 13.3568 9.93595C13.1295 9.93595 12.9114 9.8495 12.7507 9.69562C12.59 9.54175 12.4997 9.33305 12.4997 9.11543C12.4997 8.89782 12.59 8.68912 12.7507 8.53524C12.9114 8.38137 13.1295 8.29492 13.3568 8.29492C13.5841 8.29492 13.8021 8.38137 13.9629 8.53524C14.1236 8.68912 14.2139 8.89782 14.2139 9.11543ZM9.92822 12.3975C9.92822 12.6151 9.83792 12.8238 9.67717 12.9777C9.51643 13.1315 9.29841 13.218 9.07108 13.218C8.84375 13.218 8.62573 13.1315 8.46499 12.9777C8.30424 12.8238 8.21394 12.6151 8.21394 12.3975C8.21394 12.1799 8.30424 11.9712 8.46499 11.8173C8.62573 11.6634 8.84375 11.577 9.07108 11.577C9.29841 11.577 9.51643 11.6634 9.67717 11.8173C9.83792 11.9712 9.92822 12.1799 9.92822 12.3975ZM9.92822 9.11543C9.92822 9.33305 9.83792 9.54175 9.67717 9.69562C9.51643 9.8495 9.29841 9.93595 9.07108 9.93595C8.84375 9.93595 8.62573 9.8495 8.46499 9.69562C8.30424 9.54175 8.21394 9.33305 8.21394 9.11543C8.21394 8.89782 8.30424 8.68912 8.46499 8.53524C8.62573 8.38137 8.84375 8.29492 9.07108 8.29492C9.29841 8.29492 9.51643 8.38137 9.67717 8.53524C9.83792 8.68912 9.92822 8.89782 9.92822 9.11543ZM5.64251 12.3975C5.64251 12.6151 5.5522 12.8238 5.39146 12.9777C5.23071 13.1315 5.01269 13.218 4.78537 13.218C4.55804 13.218 4.34002 13.1315 4.17927 12.9777C4.01853 12.8238 3.92822 12.6151 3.92822 12.3975C3.92822 12.1799 4.01853 11.9712 4.17927 11.8173C4.34002 11.6634 4.55804 11.577 4.78537 11.577C5.01269 11.577 5.23071 11.6634 5.39146 11.8173C5.5522 11.9712 5.64251 12.1799 5.64251 12.3975ZM5.64251 9.11543C5.64251 9.33305 5.5522 9.54175 5.39146 9.69562C5.23071 9.8495 5.01269 9.93595 4.78537 9.93595C4.55804 9.93595 4.34002 9.8495 4.17927 9.69562C4.01853 9.54175 3.92822 9.33305 3.92822 9.11543C3.92822 8.89782 4.01853 8.68912 4.17927 8.53524C4.34002 8.38137 4.55804 8.29492 4.78537 8.29492C5.01269 8.29492 5.23071 8.38137 5.39146 8.53524C5.5522 8.68912 5.64251 8.89782 5.64251 9.11543Z"
                                                fill="#3B3731" />
                                        </svg>Date</span><strong
                                        x-text="selectedDateLabel">{{ $rescheduleDateCard }}</strong>
                                </div>
                                <div class="reschedule-summary-divider" aria-hidden="true"></div>
                                <div class="reschedule-summary-item">
                                    <span><svg width="16" height="16" viewBox="0 0 16 16" fill="none">
                                            <circle cx="8" cy="8" r="6" stroke="#3B3731"
                                                stroke-width="1.5" />
                                            <path d="M8 4.5V8L10.5 10" stroke="#3B3731" stroke-width="1.5"
                                                stroke-linecap="round" />
                                        </svg>Time</span><strong
                                        x-text="selectedTimeLabel">{{ $rescheduleTimeCard }}</strong>
                                </div>
                                <div class="reschedule-summary-divider" aria-hidden="true"></div>
                                <div class="reschedule-summary-item">
                                    <span><svg xmlns="http://www.w3.org/2000/svg" width="18" height="17"
                                            viewBox="0 0 18 17" fill="none">
                                            <path
                                                d="M9 6.81579C6.43624 6.81579 4.26995 9.14463 3.57052 12.1358C3.2629 13.4512 3.72676 14.8474 4.86536 15.5034C5.76798 16.0234 7.11057 16.5 9 16.5C10.8894 16.5 12.2324 16.0234 13.135 15.5034C14.2736 14.8474 14.7371 13.4512 14.4295 12.1358C13.73 9.14421 11.5638 6.81579 9 6.81579ZM0.5 6.16063C0.5 7.32358 1.22452 8.5 2.11905 8.5C3.01357 8.5 3.7381 7.32358 3.7381 6.16063C3.7381 4.99768 3.01357 4.28947 2.11905 4.28947C1.22452 4.28947 0.5 4.99811 0.5 6.16063ZM17.5 6.16063C17.5 7.32358 16.7755 8.5 15.881 8.5C14.9864 8.5 14.2619 7.32358 14.2619 6.16063C14.2619 4.99768 14.9864 4.28947 15.881 4.28947C16.7755 4.28947 17.5 4.99811 17.5 6.16063ZM4.75 2.37116C4.75 3.53411 5.47452 4.71053 6.36905 4.71053C7.26357 4.71053 7.9881 3.53411 7.9881 2.37116C7.9881 1.20821 7.26357 0.5 6.36905 0.5C5.47452 0.5 4.75 1.20863 4.75 2.37116ZM13.25 2.37116C13.25 3.53411 12.5255 4.71053 11.631 4.71053C10.7364 4.71053 10.0119 3.53411 10.0119 2.37116C10.0119 1.20821 10.7364 0.5 11.631 0.5C12.5255 0.5 13.25 1.20863 13.25 2.37116Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>Other</span><strong>{{ $reschedulePetMeta }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="reschedule-update">
                        <h4>Update Date &amp; Time</h4>
                        <div class="reschedule-update-body">
                            <div class="reschedule-current-panel">
                                <div class="reschedule-current-block">
                                    <span>Date</span>
                                    <strong x-text="selectedDateLabel">{{ $rescheduleDateCard }}</strong>
                                </div>
                                <div class="reschedule-current-block">
                                    <span>Time</span>
                                    <strong x-text="selectedTimeLabel">{{ $rescheduleTimeCard }}</strong>
                                    @if ($rescheduleDurationLabel)
                                        <small>{{ $rescheduleDurationLabel }}</small>
                                    @endif
                                </div>
                            </div>

                            <div class="reschedule-calendar-panel">
                                <div class="reschedule-calendar-head">
                                    <button type="button" @click="prevMonth()"
                                        aria-label="Previous month">&#10094;</button>
                                    <strong x-text="monthTitle">{{ $rescheduleMonthTitle }}</strong>
                                    <button type="button" @click="nextMonth()" aria-label="Next month">&#10095;</button>
                                </div>
                                <div class="reschedule-weekdays">
                                    <span>M</span><span>T</span><span>W</span><span>T</span><span>F</span><span>S</span><span>S</span>
                                </div>
                                <div class="reschedule-days-grid">
                                    <template x-for="blank in prefixBlank" :key="'blank-' + blank">
                                        <span class="is-empty"></span>
                                    </template>
                                    <template x-for="day in daysInMonth" :key="'day-' + day">
                                        <button type="button" @click="selectDay(day)"
                                            :class="{ 'is-booked': isBooked(day), 'is-selected': day === selectedDay }"
                                            x-text="day"></button>
                                    </template>
                                </div>
                            </div>

                            <div class="reschedule-availability-panel">
                                <h5><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18"
                                        viewBox="0 0 18 18" fill="none">
                                        <path
                                            d="M9 0C4.05 0 0 4.05 0 9C0 13.95 4.05 18 9 18C13.95 18 18 13.95 18 9C18 4.05 13.95 0 9 0ZM7.2 13.5L2.7 9L3.969 7.731L7.2 10.953L14.031 4.122L15.3 5.4L7.2 13.5Z"
                                            fill="#D8E8B7" />
                                    </svg>My availability</h5>
                                @foreach ($rescheduleAvailability as $slot)
                                    <button type="button" @click="selectTime('{{ $slot }}')"
                                        :class="{ 'is-active': selectedTime === '{{ $slot }}' }">{{ $slot }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <div class="reschedule-actions">
                        <button type="button" class="reschedule-cancel-btn"
                            wire:click="closeRescheduleModal">Cancel</button>
                        <button type="button" class="reschedule-confirm-btn"
                            @click="$wire.confirmRescheduleBookingFromClient(selectedDate, selectedTime)"
                            wire:loading.attr="disabled" wire:target="confirmRescheduleBookingFromClient">
                            <span wire:loading.remove wire:target="confirmRescheduleBookingFromClient">Reschedule</span>
                            <span class="reschedule-btn-loading" wire:loading.inline-flex
                                wire:target="confirmRescheduleBookingFromClient">
                                <span class="reschedule-btn-spinner" aria-hidden="true"></span>
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        @endteleport
    @endif
</section>

<script>
    if (!window.reschedulePicker) {
        window.reschedulePicker = function(config) {
            const monthNames = [
                'January', 'February', 'March', 'April', 'May', 'June',
                'July', 'August', 'September', 'October', 'November', 'December'
            ];

            const parseYmd = (ymd) => {
                const [y, m, d] = (ymd || '').split('-').map(Number);
                if (!y || !m || !d) return null;
                return {
                    y,
                    m,
                    d
                };
            };

            return {
                selectedDate: config.initialDate,
                selectedTime: config.initialTime,
                monthDate: config.initialMonth,
                bookedDaysByMonth: config.bookedDaysByMonth || {},
                get monthKey() {
                    return this.monthDate.slice(0, 7);
                },
                get monthMeta() {
                    const [y, m] = this.monthKey.split('-').map(Number);
                    return {
                        y,
                        m
                    };
                },
                get monthTitle() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    return `${monthNames[m - 1]} ${y}`;
                },
                get daysInMonth() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    return new Date(y, m, 0).getDate();
                },
                get prefixBlank() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    const mondayFirst = (new Date(y, m - 1, 1).getDay() + 6) % 7;
                    return Array.from({
                        length: mondayFirst
                    }, (_, i) => i);
                },
                get selectedDay() {
                    const parsed = parseYmd(this.selectedDate);
                    if (!parsed) return 0;
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    return parsed.y === y && parsed.m === m ? parsed.d : 0;
                },
                get selectedDateLabel() {
                    const parsed = parseYmd(this.selectedDate);
                    if (!parsed) return 'N/A';
                    return `${String(parsed.d).padStart(2, '0')}/${String(parsed.m).padStart(2, '0')}/${parsed.y}`;
                },
                get selectedTimeLabel() {
                    return this.selectedTime || 'N/A';
                },
                prevMonth() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    const d = new Date(y, m - 2, 1);
                    this.monthDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-01`;
                },
                nextMonth() {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    const d = new Date(y, m, 1);
                    this.monthDate = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-01`;
                },
                selectDay(day) {
                    const {
                        y,
                        m
                    } = this.monthMeta;
                    this.selectedDate = `${y}-${String(m).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                },
                selectTime(slot) {
                    this.selectedTime = slot;
                },
                isBooked(day) {
                    return (this.bookedDaysByMonth[this.monthKey] || []).includes(day);
                }
            };
        };
    }

    if (!window.__declineModalScrollLockBound) {
        window.__declineModalScrollLockBound = true;

        window.addEventListener('decline-modal-opened', () => {
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        });

        window.addEventListener('decline-modal-closed', () => {
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        });

        window.addEventListener('reschedule-modal-opened', () => {
            document.body.style.overflow = 'hidden';
            document.documentElement.style.overflow = 'hidden';
        });

        window.addEventListener('reschedule-modal-closed', () => {
            document.body.style.overflow = '';
            document.documentElement.style.overflow = '';
        });
    }
</script>

<style>
    [x-cloak] {
        display: none !important;
    }

    .bookings-board {
        width: 100%;
        padding-top: 0.35rem;
    }

    .bookings-board-header {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 1rem;
        flex-wrap: wrap-reverse;
        margin-bottom: 1rem;
        position: relative;
    }

    .booking-pill-row {
        display: flex;
        gap: 0.9rem;
        flex-wrap: wrap;
    }

    .booking-pill {
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        border-radius: 100px;
        padding: 0.6rem 1.15rem;
        border: none;
        cursor: pointer;
    }

    .booking-pill.pending {
        color: #FFBA55;
        background: rgba(255, 201, 122, 0.10);
    }

    .booking-pill.confirmed {
        color: #AFCD6F;
        background: rgba(175, 205, 111, 0.10);
    }

    .booking-pill.completed {
        color: #9FC7E4;
        background: rgba(159, 199, 228, 0.10);
    }

    .booking-pill.cancelled {
        color: #FF6E6E;
        background: #FFE2E2;
    }

    .bookings-table-wrap {
        width: 100%;
        overflow-x: auto;
        overflow-y: visible;
        -ms-overflow-style: none;
        scrollbar-width: none;
    }

    .bookings-table-wrap::-webkit-scrollbar {
        display: none;
        /* Chrome/Safari */
    }

    .bookings-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 980px;
    }

    .bookings-load-more-wrap {
        display: flex;
        justify-content: center;
        margin-top: 1rem;
    }

    .bookings-load-more-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 133px;
        height: 48px;
        background: #FFF;
        color: #7a5d34;
        border-radius: 75px;
        border: 1px solid #3B3731;
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        cursor: pointer;
        transition: background-color 0.15s ease, transform 0.15s ease;
    }

    .bookings-load-more-btn:hover {
        background: #FFC97A;
        color: #FFF;
        border: 1px solid #FFC97A;
        transform: translateY(-1px);
    }

    .bookings-load-more-btn[disabled] {
        opacity: 0.9;
        cursor: wait;
        transform: none;
    }

    .bookings-load-more-loading {
        display: none;
        align-items: center;
        justify-content: center;
    }

    .bookings-load-more-spinner {
        width: 18px;
        height: 18px;
        border-radius: 9999px;
        border: 2px solid #3B3731;
        border-top-color: transparent;
        animation: bookings-load-more-spin 0.7s linear infinite;
    }

    @keyframes bookings-load-more-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .bookings-table th,
    .bookings-table td {
        border-bottom: 1px solid #e5e2df;
        padding: 1.15rem 1.25rem;
        text-align: left;
        white-space: nowrap;
    }

    .bookings-table th {
        color: #000;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .bookings-table td {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .bookings-table-body tr {
        animation: bookings-row-enter 220ms ease-out both;
    }

    @keyframes bookings-row-enter {
        from {
            opacity: 0;
            transform: translateY(8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pet-name-wrap {
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .pet-name {
        color: #3B3731;
    }

    .pet-type {
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .status-chip {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 9999px;
        padding: 0.25rem 0.9rem;
    }

    .status-chip.pending {
        color: #c29b41;
        background: #f8f2e0;
    }

    .status-chip.confirmed {
        color: #9db268;
        background: #f1f5e7;
    }

    .status-chip.completed {
        color: #8faec5;
        background: #edf4f9;
    }

    .status-chip.cancelled {
        color: #d06b72;
        background: #f8e9eb;
    }

    .view-col {
        vertical-align: middle;
        border-left: 1px solid #E5E2DF;
        width: 8rem;
        padding: 0 !important;
    }

    .view-col-inner {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }

    .view-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: transparent;
        border: 0;
        padding: 0;
        margin: 0 auto;
        cursor: pointer;
    }

    .empty-bookings {
        text-align: center !important;
        color: #8f8b86 !important;
    }

    .booking-list-header {
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
    }

    .booking-list-title {
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        color: #FFBA55;
        background: rgba(255, 201, 122, 0.10);
        border-radius: 999px;
        padding: 0.6rem 1.15rem;
        border: none;
        white-space: nowrap;
    }

    .booking-list-sort {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .sort-dropdown {
        position: relative;
    }

    .sort-trigger {
        width: 69px;
        height: 32px;
        border-radius: 100px;
        border: 1px solid #A8A8A8;
        background: transparent;
        color: #A8A8A8;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.6rem;
        cursor: pointer;
    }

    .sort-menu {
        position: absolute;
        top: calc(100% + 0.6rem);
        right: 0;
        min-width: 250px;
        background: #F8F8F8;
        border: 2px solid #e6e6e5;
        border-radius: 10px 0 10px 10px;
        box-shadow: none;
        z-index: 20;
        overflow: hidden;
    }

    .sort-options {
        width: 100%;
        border: 0;
        border-bottom: 2px solid #e6e6e5;
        background: #FFF;
        padding: 1rem;
        text-align: left;
        color: #3B3731;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        line-height: 1.15;
    }

    .sort-options:last-child {
        border-bottom: 0;
    }

    .sort-options:hover {
        background: #F2F2F2;
    }

    .sort-indicator {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        border: 2px solid #FFC97A;
        background: transparent;
        position: relative;
        flex-shrink: 0;
    }

    .sort-options.is-active .sort-indicator::after {
        content: '';
        position: absolute;
        inset: 2px;
        border-radius: 999px;
        background: #FFC97A;
    }

    .submitted-at {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .submitted-time {
        font-family: Lato;
        font-weight: 600;
    }

    .submitted-date {
        color: #9D9B98;
        font-family: Lato;
        font-weight: 400;
    }

    .filtered-pet-cell {
        display: flex;
        flex-direction: column;
        flex-wrap: wrap;
        gap: 0.35rem;
        align-items: start;
    }

    .booking-pet-name {
        font-weight: 600;
        color: #3B3731;
    }

    .booking-pet-type {
        color: #9D9B98;
        font-weight: 400;
    }

    .booking-pet-more {
        color: #9D9B98;
        font-weight: 400;
    }

    .action-col {
        border-left: 1px solid #E5E2DF;
    }

    .confirmed-bookings-table .confirmed-action-col {
        border-left: 1px solid #E5E2DF;
    }

    .confirmed-appointment-cell {
        display: flex;
        flex-direction: column;
        gap: 0.18rem;
    }

    .confirmed-appointment-cell div:first-child {
        color: #3B3731;
    }

    .confirmed-appointment-cell div:last-child {
        color: #3B3731;
    }

    .confirmed-action-cell {
        display: flex;
        align-items: center;
        justify-content: flex-start;
        gap: 0.55rem;
    }

    .confirmed-action-btn {
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
        background: transparent;
    }

    .confirmed-more-btn {
        width: 26px;
        height: 26px;
        border: none;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        padding: 0;
    }

    .booking-action-cell {
        display: flex;
        align-items: center;
        justify-content: start;
        gap: 0.6rem;
        position: relative;
        overflow: visible;
    }

    .booking-action-cell.is-open {
        z-index: 10001;
    }


    .booking-accept-btn {
        border-radius: 100px;
        background: #C9DDA0;
        width: 75.939px;
        height: 36px;
        color: #FFF;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
    }

    .booking-accept-btn[disabled] {
        cursor: not-allowed;
        opacity: 0.9;
    }

    .booking-accept-loading {
        align-items: center;
        gap: 0.4rem;
    }

    .booking-accept-spinner {
        width: 13px;
        height: 13px;
        border: 2px solid rgba(255, 255, 255, 0.55);
        border-top-color: #FFF;
        border-radius: 50%;
        animation: booking-accept-spin 0.8s linear infinite;
        flex: 0 0 auto;
    }

    @keyframes booking-accept-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .booking-decline-btn {
        width: 36px;
        height: 36px;
        aspect-ratio: 1/1;
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
    }

    .more-action-trigger,
    .booking-message-btn {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        border: none;
        background: transparent;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        color: #3B3731;
        font-weight: 700;
    }

    .more-action-trigger {
        font-size: 18px;
        line-height: 1;
    }

    .more-action-wrapper {
        position: relative;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        overflow: visible;
    }

    .more-action-menu {
        position: absolute;
        top: calc(100% + 0.45rem);
        left: 0;
        min-width: 205px;
        width: max-content;
        max-width: min(260px, calc(100vw - 24px));
        background: #F8F8F8;
        border: 1px solid #D9D9D9;
        border-radius: 8px;
        overflow: hidden;
        z-index: 9999;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    }

    .more-action-menu-item {
        width: 100%;
        border: 0;
        border-bottom: 1px solid #D6D6D6;
        background: transparent;
        padding: 0.65rem 0.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-family: Lato;
        color: #3B3731;
        cursor: pointer;
    }

    .more-action-menu-item:last-child {
        border-bottom: 0;
    }

    .more-action-menu-item span {
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .more-action-menu-item:hover {
        background: #ECECEC;
    }

    .booking-details {
        display: flex;
        flex-direction: column;
        gap: 0.15rem;
    }

    .details-date {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .details-time {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .decline-modal-overlay {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.28);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
        margin: 0;
        z-index: 100100;
        overflow: auto;
    }

    .reschedule-modal-overlay {
        position: fixed;
        inset: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.28);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.7rem;
        z-index: 100120;
        overflow: auto;
    }

    .reschedule-modal-card {
        width: min(100%, 980px);
        border-radius: 10px;
        border: 1px solid #E9B96D;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 1rem;
        position: relative;
        overflow: hidden;
    }

    .reschedule-modal-loading-bar {
        position: absolute;
        left: 0;
        right: 0;
        top: 0;
        height: 4px;
        display: none;
        overflow: hidden;
        z-index: 3;
        background: rgba(232, 228, 222, 0.85);
    }

    .reschedule-modal-loading-bar__sweep {
        position: absolute;
        top: 0;
        left: -42%;
        width: 42%;
        height: 100%;
        background: linear-gradient(90deg, #FFC97A 0%, #f6a623 45%, #FFC97A 100%);
        box-shadow: 0 0 12px rgba(246, 166, 35, 0.45);
        animation: reschedule-modal-load-sweep 1.1s linear infinite;
    }

    @keyframes reschedule-modal-load-sweep {
        0% {
            left: -42%;
        }

        100% {
            left: 100%;
        }
    }

    .reschedule-modal-close {
        position: absolute;
        right: 0.8rem;
        top: 0.8rem;
        border: none;
        background: transparent;
        cursor: pointer;
    }

    .reschedule-modal-icon,
    .reschedule-modal-title,
    .reschedule-modal-subtitle {
        text-align: center;
    }

    .reschedule-modal-title>strong {
        font-weight: 800;
    }

    .reschedule-modal-title {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-modal-subtitle {
        color: #9D9B98;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        margin-bottom: 0.8rem;
    }

    .reschedule-summary {
        display: flex;
        align-items: center;
        gap: 1rem;
        border-radius: 10px;
        background: #F8F8F8;
        padding: 2rem;
    }

    .reschedule-summary>div:nth-child(2) {
        width: 100%;
    }

    .reschedule-summary-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin: 0.7rem 1.2rem;
    }

    .reschedule-home-chip {
        border-radius: 999px;
        background: #F2C273;
        color: #FFF;
        font-family: Lato;
        font-size: 13px;
        padding: 0.25rem 0.65rem;
    }

    .reschedule-booking-id {
        color: #3B3731;
        text-align: right;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .reschedule-summary-grid {
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 3.5rem;
    }

    .reschedule-pet-avatar {
        width: 80px;
        height: auto;
        aspect-ratio: 1/1;
        border-radius: 999px;
        border: 2px solid #FFC97A;
        background: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: #3B3731;
        font-weight: 700;
        font-family: Lato;
        overflow: hidden;
    }

    .reschedule-pet-avatar img {
        width: 90%;
        height: 90%;
        object-fit: cover;
        display: block;
        border-radius: 999px;
    }

    .reschedule-summary-item {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .reschedule-summary-divider {
        width: 1px;
        height: 52px;
        background: #D8D8D8;
        flex: 0 0 auto;
    }

    .reschedule-summary-item span {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-summary-item strong {
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .reschedule-update {
        border: 1px solid #D5D5D5;
        border-radius: 10px;
        margin-top: 0.8rem;
        padding: 1rem;
    }

    .reschedule-update h4 {
        color: #3B3731;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
        margin-bottom: 0.6rem;
    }

    .reschedule-update-body {
        display: grid;
        grid-template-columns: 200px 1fr 220px;
        gap: 1rem;
    }

    .reschedule-current-panel {
        height: fit-content;
        border-radius: 10px;
        background: #F6F6F6;
        padding: 1rem;
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }

    .reschedule-current-block span {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-current-block strong {
        display: block;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .reschedule-current-block small {
        color: #9D9B98;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 400;
        line-height: 20px;
    }

    .reschedule-calendar-head {
        border-radius: 999px;
        background: #F6F6F6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.35rem;
    }

    .reschedule-calendar-head button {
        width: 34px;
        height: 34px;
        border-radius: 999px;
        border: none;
        background: #FFF;
        color: #8C8C8C;
        cursor: pointer;
    }

    .reschedule-calendar-head strong {
        color: #3B3731;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-weekdays,
    .reschedule-days-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
        gap: 0.35rem;
        margin-top: 0.6rem;
    }

    .reschedule-weekdays span {
        text-align: center;
        color: #9C9790;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
    }

    .reschedule-days-grid button {
        width: 41.538px;
        height: 41.538px;
        aspect-ratio: 1/1;
        margin: 0 auto;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: #F2F2F2;
        color: #3B3731;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        border: none;
        cursor: pointer;
    }

    .reschedule-days-grid span.is-empty {
        background: transparent;
    }

    .reschedule-days-grid button.is-selected {
        border-radius: 10px;
        border: 1px solid #91A36C !important;
        background: #D8E8B7;
        box-shadow: 0 5px 8px 0 rgba(0, 0, 0, 0.20);
    }

    .reschedule-days-grid button.is-booked {
        border: none;
        background: #ECF4DB;
    }

    .reschedule-availability-panel h5 {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #BACF8E;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 600;
        line-height: normal;
        margin-bottom: 0.6rem;
    }

    .reschedule-availability-panel button {
        width: 170px;
        height: 48px;
        border-radius: 999px;
        border: 1px solid #B4CB7E;
        background: #FFF;
        color: #BACF8E;
        text-align: center;
        font-family: Lato;
        font-size: 14px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
        margin-bottom: 0.55rem;
        cursor: pointer;
    }

    .reschedule-availability-panel button.is-active {
        background: #C9DDA0;
        box-shadow: 0 4px 4px 0 rgba(0, 0, 0, 0.05);
        color: #fff;
    }

    .reschedule-actions {
        margin-top: 1.5rem;
        display: flex;
        justify-content: space-between;
        gap: 1rem;
    }

    .reschedule-cancel-btn,
    .reschedule-confirm-btn {
        width: 240px;
        height: 42px;
        border-radius: 75px;
        font-family: Lato;
        font-size: 16px;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .reschedule-cancel-btn {
        border-color: #4D4842;
        background: #fff;
        color: #3B3731;
    }

    .reschedule-confirm-btn {
        background: #F2C273;
        border-color: #F2C273;
        color: #FFF;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
    }

    .reschedule-confirm-btn[disabled] {
        cursor: not-allowed;
        opacity: 0.9;
    }

    .reschedule-btn-loading {
        align-items: center;
        justify-content: center;
    }

    .reschedule-btn-spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.55);
        border-top-color: #FFF;
        border-radius: 50%;
        animation: reschedule-btn-spin 0.8s linear infinite;
        flex: 0 0 auto;
    }

    @keyframes reschedule-btn-spin {
        to {
            transform: rotate(360deg);
        }
    }

    .decline-modal-card {
        width: 400px;
        border-radius: 10px;
        border: 1px solid #FF6E6E;
        background: #FFF;
        box-shadow: 0 10px 20px 2px rgba(0, 0, 0, 0.05);
        padding: 1.6rem 1.7rem;
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
    }

    .decline-modal-close {
        position: absolute;
        top: 0.9rem;
        right: 0.9rem;
        border: none;
        background: transparent;
        cursor: pointer;
        line-height: 1;
        padding: 0.15rem;
    }

    .decline-modal-icon {
        margin-top: 0.1rem;
    }

    .decline-modal-title {
        color: #3B3731;
        text-align: center;
        font-family: "Playfair Display";
        font-size: 28px;
        font-style: normal;
        font-weight: 800;
        line-height: normal;
    }

    .decline-modal-subtitle {
        color: #9D9B98;
        text-align: center;
        font-family: Lato;
        font-size: 18px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .decline-modal-details {
        width: 100%;
        border-radius: 10px;
        background: #F1F1F1;
        padding: 1.35rem 1.7rem;
    }

    .decline-modal-detail-row {
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 1rem;
        color: #9D9B98;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
    }

    .decline-modal-detail-row strong {
        text-align: right;
        color: #000;
        text-align: right;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 500;
        line-height: normal;
    }

    .decline-modal-detail-payment {
        margin-top: 1.2rem;
        margin-bottom: 0;
    }

    .decline-modal-actions {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-top: 0.7rem;
    }

    .decline-cancel-btn,
    .decline-confirm-btn {
        width: 150px;
        height: 42px;
        text-align: center;
        font-family: Lato;
        font-size: 16px;
        font-style: normal;
        font-weight: 400;
        line-height: normal;
        cursor: pointer;
        border: 1px solid transparent;
        border-radius: 75px;

    }

    .decline-cancel-btn {
        background: #F8F8F8;
        border-color: #4D4842;
        color: #3B3731;
    }

    .decline-confirm-btn {
        background: #FF6E6E;
        color: #FFF;
        border-color: #FF6E6E;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.45rem;
    }

    .decline-confirm-btn[disabled] {
        opacity: 0.9;
        cursor: not-allowed;
    }

    .decline-btn-loading {
        align-items: center;
        gap: 0.45rem;
    }

    .decline-btn-spinner {
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255, 255, 255, 0.55);
        border-top-color: #FFF;
        border-radius: 50%;
        animation: decline-btn-spin 0.8s linear infinite;
    }

    @keyframes decline-btn-spin {
        to {
            transform: rotate(360deg);
        }
    }
</style>
