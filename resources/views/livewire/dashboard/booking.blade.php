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

                <x-dashboard.common.sort-dropdown :pending-sort="$pendingSort" />
            </div>
        @else
            <div class="booking-list-header">
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

                @if (in_array($activeStatus, ['confirmed', 'completed', 'cancelled'], true))
                    <x-dashboard.common.sort-dropdown :pending-sort="$pendingSort" />
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
                                    <div class="booking-action-cell">
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
                                                    width="36" height="36" viewBox="0 0 36 36" fill="none">
                                                    <rect width="36" height="36" rx="18" fill="#FF6E6E" />
                                                    <path d="M13 23L23 13M13 13L23 23" stroke="white" stroke-width="1.5"
                                                        stroke-linecap="round" />
                                                </svg></span>
                                        </button>
                                        <x-dashboard.common.more-action-btn :row-id="$booking->id" />
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
                            if ($pendingSort === 'oldest_submitted') {
                                $confirmedBookings = $confirmedBookings->sortBy('created_at')->values();
                            } elseif ($pendingSort === 'amount_high') {
                                $confirmedBookings = $confirmedBookings
                                    ->sortByDesc(fn($b) => (float) $b->amount)
                                    ->values();
                            } elseif ($pendingSort === 'amount_low') {
                                $confirmedBookings = $confirmedBookings->sortBy(fn($b) => (float) $b->amount)->values();
                            } else {
                                $confirmedBookings = $confirmedBookings->sortByDesc('created_at')->values();
                            }
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
                            if ($pendingSort === 'oldest_submitted') {
                                $filteredBookings = $filteredBookings->sortBy('created_at')->values();
                            } elseif ($pendingSort === 'amount_high') {
                                $filteredBookings = $filteredBookings
                                    ->sortByDesc(fn($b) => (float) $b->amount)
                                    ->values();
                            } elseif ($pendingSort === 'amount_low') {
                                $filteredBookings = $filteredBookings->sortBy(fn($b) => (float) $b->amount)->values();
                            } else {
                                $filteredBookings = $filteredBookings->sortByDesc('created_at')->values();
                            }
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
        $rescheduleBooking = $rescheduleBookingId ? $bookings->firstWhere('id', $rescheduleBookingId) : null;
    @endphp

    <x-dashboard.common.decline-modal :decline-booking="$declineBooking" />
    <x-dashboard.common.reschedule-modal :reschedule-booking="$rescheduleBooking" :bookings="$bookings" :reschedule-selected-date="$rescheduleSelectedDate" :reschedule-selected-time="$rescheduleSelectedTime"
        :reschedule-calendar-month="$rescheduleCalendarMonth" :reschedule-duration-minutes="$rescheduleDurationMinutes" />
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
</style>
